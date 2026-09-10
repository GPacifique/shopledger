<?php

namespace App\Services\Imports;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class ImportService
{
    public function __construct(private readonly StockService $stockService)
    {
    }

    public function normalizeRows(array $rows): array
    {
        return array_values(array_filter(array_map(function ($row) {
            $clean = [];
            foreach ((array) $row as $key => $value) {
                $key = trim((string) $key);
                $value = is_string($value) ? trim($value) : $value;
                $clean[$key] = $value;
            }
            return $clean;
        }, $rows), fn ($row) => collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty()));
    }

    public function validate(string $type, array $rows, int $shopId): array
    {
        $rows = $this->normalizeRows($rows);
        $errors = [];
        $valid = [];
        $seen = [];

        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $result = $this->validateRow($type, $row, $shopId, $seen);
            if ($result['errors']) {
                foreach ($result['errors'] as $error) {
                    $errors[] = "Row {$excelRow}: {$error}";
                }
            } else {
                $valid[] = $row;
            }
        }

        return [
            'rows' => $rows,
            'valid' => $valid,
            'errors' => $errors,
            'total' => count($rows),
        ];
    }

    public function import(string $type, array $rows, int $shopId, int $userId): array
    {
        return DB::transaction(function () use ($type, $rows, $shopId, $userId) {
            return match ($type) {
                'products' => $this->importProducts($rows, $shopId),
                'suppliers' => $this->importSuppliers($rows, $shopId),
                'customers' => $this->importCustomers($rows, $shopId),
                'purchases' => $this->importPurchases($rows, $shopId, $userId),
                'sales' => $this->importSales($rows, $shopId, $userId),
                default => throw new \InvalidArgumentException('Unsupported import type.'),
            };
        });
    }

    private function validateRow(string $type, array $row, int $shopId, array &$seen): array
    {
        $errors = [];
        $required = match ($type) {
            'products' => ['sku', 'product_name', 'category', 'buying_price', 'selling_price'],
            'suppliers' => ['supplier_name'],
            'customers' => ['customer_name'],
            'purchases' => ['purchase_reference', 'purchase_date', 'product_sku', 'quantity', 'unit_cost'],
            'sales' => ['sale_reference', 'sale_date', 'product_sku', 'quantity', 'unit_price', 'payment_method', 'payment_status'],
            default => [],
        };

        foreach ($required as $field) {
            if (!isset($row[$field]) || $row[$field] === '') {
                $errors[] = "{$field} is required.";
            }
        }

        if ($errors) {
            return compact('errors');
        }

        try {
            if (in_array($type, ['purchases', 'sales'], true)) {
                $refKey = strtolower((string) $row[$type === 'purchases' ? 'purchase_reference' : 'sale_reference']);
                if (isset($seen[$type][$refKey])) {
                    // Repeated references are intentional: they group line items.
                } else {
                    $seen[$type][$refKey] = true;
                }
            }

            if ($type === 'products') {
                $this->validateDecimal($row['buying_price'], 'Buying Price', $errors, 0);
                $this->validateDecimal($row['selling_price'], 'Selling Price', $errors, 0);
                $this->validateDecimal($row['opening_quantity'] ?? 0, 'Opening Quantity', $errors, 0);
                $this->validateDecimal($row['opening_unit_cost'] ?? 0, 'Opening Unit Cost', $errors, 0);
                $this->validateDecimal($row['minimum_stock'] ?? 0, 'Minimum Stock', $errors, 0);
                if (!empty($row['expiry_date'])) $this->validateDate($row['expiry_date'], 'Expiry Date', $errors);
                if (!empty($row['status']) && !in_array(strtolower((string) $row['status']), ['active', 'inactive'], true)) $errors[] = 'Status must be active or inactive.';
                if (Product::where('shop_id', $shopId)->where('sku', $row['sku'])->exists()) {
                    // Existing SKU is an update, not an error.
                }
                $skuKey = strtolower(trim((string) $row['sku']));
                if (($seen['products']['sku'][$skuKey] ?? false) === true) $errors[] = 'Duplicate SKU appears more than once in this file.';
                $seen['products']['sku'][$skuKey] = true;
                $nameOwner = Product::where('shop_id', $shopId)->where('name', $row['product_name'])->first();
                $skuOwner = Product::where('shop_id', $shopId)->where('sku', $row['sku'])->first();
                if ($nameOwner && (!$skuOwner || $nameOwner->id !== $skuOwner->id)) $errors[] = 'Product Name already belongs to another product.';
                if (!empty($row['barcode'])) {
                    $barcodeOwner = Product::where('shop_id', $shopId)->where('barcode', $row['barcode'])->first();
                    if ($barcodeOwner && $barcodeOwner->sku !== $row['sku']) $errors[] = 'Barcode already belongs to another product.';
                }
                if (!Category::where('shop_id', $shopId)->where('name', $row['category'])->exists()) {
                    // Category is created automatically during import.
                }
            }

            if ($type === 'suppliers') {
                if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
            }

            if ($type === 'customers') {
                if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
            }

            if ($type === 'purchases') {
                $this->validateDate($row['purchase_date'], 'Purchase Date', $errors);
                $this->validatePositive($row['quantity'], 'Quantity', $errors, true);
                $this->validateDecimal($row['unit_cost'], 'Unit Cost', $errors, 0.01);
                $product = Product::where('shop_id', $shopId)->where('sku', $row['product_sku'])->first();
                if (!$product) $errors[] = "Product SKU '{$row['product_sku']}' was not found in this shop.";
                if (!empty($row['supplier']) && !Supplier::where('shop_id', $shopId)->where('name', $row['supplier'])->exists()) $errors[] = "Supplier '{$row['supplier']}' was not found.";
            }

            if ($type === 'sales') {
                $this->validateDate($row['sale_date'], 'Sale Date', $errors);
                $this->validatePositive($row['quantity'], 'Quantity', $errors, true);
                $this->validateDecimal($row['unit_price'], 'Unit Price', $errors, 0);
                if (!in_array(strtolower((string) $row['payment_method']), ['cash', 'momo', 'bank', 'card'], true)) $errors[] = 'Payment Method must be cash, momo, bank, or card.';
                if (!in_array(strtolower((string) $row['payment_status']), ['paid', 'partial', 'unpaid'], true)) $errors[] = 'Payment Status must be paid, partial, or unpaid.';
                $product = Product::where('shop_id', $shopId)->where('sku', $row['product_sku'])->first();
                if (!$product) $errors[] = "Product SKU '{$row['product_sku']}' was not found in this shop.";
                if (!empty($row['customer']) && !$this->findCustomer($shopId, $row['customer'])) $errors[] = "Customer '{$row['customer']}' was not found.";
            }
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }

        return compact('errors');
    }

    private function importProducts(array $rows, int $shopId): array
    {
        $created = $updated = 0;
        foreach ($rows as $row) {
            $category = Category::firstOrCreate(['shop_id' => $shopId, 'name' => $row['category']], ['description' => null]);
            $supplier = !empty($row['supplier'])
                ? Supplier::where('shop_id', $shopId)->where('name', $row['supplier'])->first()
                : null;
            $sku = trim((string) $row['sku']);
            $product = Product::where('shop_id', $shopId)->where('sku', $sku)->first();
            $openingQty = $this->number($row['opening_quantity'] ?? 0);
            $openingCost = $this->number($row['opening_unit_cost'] ?? $row['buying_price']);
            $data = [
                'shop_id' => $shopId,
                'sku' => $sku,
                'name' => trim((string) $row['product_name']),
                'category_id' => $category->id,
                'supplier_id' => $supplier?->id,
                'barcode' => $this->nullable($row['barcode'] ?? null),
                'description' => $this->nullable($row['description'] ?? null),
                'buying_price' => $this->number($row['buying_price']),
                'selling_price' => $this->number($row['selling_price']),
                'minimum_stock' => $this->number($row['minimum_stock'] ?? 0),
                'low_stock_threshold' => (int) ($row['low_stock_threshold'] ?? 10),
                'track_stock' => $this->boolean($row['track_stock'] ?? 1),
                'expiry_date' => $this->nullable($row['expiry_date'] ?? null),
                'status' => strtolower((string) ($row['status'] ?? 'active')) ?: 'active',
            ];
            if (!$product) {
                $data['opening_quantity'] = $openingQty;
                $data['opening_unit_cost'] = $openingCost;
                $data['opening_stock_value'] = $openingQty * $openingCost;
                $data['opening_stock_date'] = $openingQty > 0 ? ($row['opening_stock_date'] ?? now()->toDateString()) : null;
                $data['stock'] = 0;
                $data['quantity'] = 0;
                $product = Product::create($data);
                if ($openingQty > 0) {
                    $this->stockService->recordMovement(
                        $product->fresh(),
                        StockMovement::TYPE_OPENING,
                        $openingQty,
                        Product::class,
                        $product->id,
                        'Opening stock imported from template',
                        auth()->id(),
                        $openingCost,
                        $data['opening_stock_date']
                    );
                }
                $created++;
            } else {
                $product->update($data);
                $updated++;
            }
        }
        return compact('created', 'updated');
    }

    private function importSuppliers(array $rows, int $shopId): array
    {
        $created = $updated = 0;
        foreach ($rows as $row) {
            $supplier = Supplier::where('shop_id', $shopId)->where('name', $row['supplier_name'])->first();
            $data = [
                'shop_id' => $shopId,
                'name' => trim((string) $row['supplier_name']),
                'contact_name' => $this->nullable($row['contact_name'] ?? null),
                'email' => $this->nullable($row['email'] ?? null),
                'phone' => $this->nullable($row['phone'] ?? null),
                'address' => $this->nullable($row['address'] ?? null),
            ];
            if ($supplier) { $supplier->update($data); $updated++; }
            else { Supplier::create($data); $created++; }
        }
        return compact('created', 'updated');
    }

    private function importCustomers(array $rows, int $shopId): array
    {
        $created = $updated = 0;
        foreach ($rows as $row) {
            $customer = null;
            if (!empty($row['phone'])) $customer = Customer::where('shop_id', $shopId)->where('phone', $row['phone'])->first();
            if (!$customer && !empty($row['email'])) $customer = Customer::where('shop_id', $shopId)->where('email', $row['email'])->first();
            if (!$customer) $customer = Customer::where('shop_id', $shopId)->where('name', $row['customer_name'])->first();
            $data = [
                'shop_id' => $shopId,
                'name' => trim((string) $row['customer_name']),
                'phone' => $this->nullable($row['phone'] ?? null),
                'email' => $this->nullable($row['email'] ?? null),
                'address' => $this->nullable($row['address'] ?? null),
            ];
            if ($customer) { $customer->update($data); $updated++; }
            else { Customer::create($data); $created++; }
        }
        return compact('created', 'updated');
    }

    private function importPurchases(array $rows, int $shopId, int $userId): array
    {
        $groups = collect($rows)->groupBy(fn ($r) => trim((string) $r['purchase_reference']));
        $created = $updated = 0;
        foreach ($groups as $reference => $items) {
            $first = $items->first();
            $supplier = !empty($first['supplier']) ? Supplier::where('shop_id', $shopId)->where('name', $first['supplier'])->first() : null;
            $purchase = Purchase::where('shop_id', $shopId)->where('import_reference', $reference)->first();
            if ($purchase) {
                $this->reversePurchaseStock($purchase, $userId);
                $purchase->items()->delete();
                $updated++;
            } else {
                $purchase = Purchase::create([
                    'shop_id' => $shopId, 'supplier_id' => $supplier?->id,
                    'purchase_date' => Carbon::parse($first['purchase_date'])->toDateString(),
                    'total_amount' => 0, 'created_by' => $userId, 'import_reference' => $reference,
                ]);
                $created++;
            }
            $total = 0;
            foreach ($items as $row) {
                $product = Product::where('shop_id', $shopId)->where('sku', $row['product_sku'])->firstOrFail();
                $qty = $this->number($row['quantity']);
                $cost = $this->number($row['unit_cost']);
                $line = $qty * $cost;
                PurchaseItem::create(['shop_id' => $shopId, 'purchase_id' => $purchase->id, 'product_id' => $product->id, 'quantity' => (int) $qty, 'unit_cost' => $cost, 'line_total' => $line]);
                $product->update(['buying_price' => $cost]);
                $this->stockService->recordPurchase($product->fresh(), $qty, $purchase->id, $userId, $cost);
                $total += $line;
            }
            $purchase->update(['supplier_id' => $supplier?->id, 'purchase_date' => Carbon::parse($first['purchase_date'])->toDateString(), 'total_amount' => $total]);
        }
        return compact('created', 'updated');
    }

    private function importSales(array $rows, int $shopId, int $userId): array
    {
        $groups = collect($rows)->groupBy(fn ($r) => trim((string) $r['sale_reference']));
        $created = $updated = 0;
        foreach ($groups as $reference => $items) {
            $first = $items->first();
            $customer = !empty($first['customer']) ? $this->findCustomer($shopId, $first['customer']) : null;
            $sale = Sale::where('shop_id', $shopId)->where('import_reference', $reference)->first();
            if ($sale) {
                $this->reverseSaleStock($sale, $userId);
                $sale->items()->delete();
                $updated++;
            } else {
                $sale = Sale::create([
                    'shop_id' => $shopId, 'customer_id' => $customer?->id,
                    'sale_date' => Carbon::parse($first['sale_date'])->toDateString(), 'total_amount' => 0,
                    'payment_method' => strtolower((string) $first['payment_method']),
                    'payment_status' => strtolower((string) $first['payment_status']),
                    'created_by' => $userId, 'import_reference' => $reference,
                ]);
                $created++;
            }
            $total = 0;
            foreach ($items as $row) {
                $product = Product::where('shop_id', $shopId)->where('sku', $row['product_sku'])->firstOrFail();
                $qty = $this->number($row['quantity']);
                $price = $this->number($row['unit_price']);
                $line = $qty * $price;
                SaleItem::create([
                    'shop_id' => $shopId, 'sale_id' => $sale->id, 'product_id' => $product->id,
                    'quantity' => (int) $qty, 'unit_price' => $price,
                    'cost_price_at_sale' => $product->buying_price, 'line_total' => $line,
                ]);
                $this->stockService->recordSale($product->fresh(), $qty, $sale->id, $userId);
                $total += $line;
            }
            $sale->update([
                'customer_id' => $customer?->id,
                'sale_date' => Carbon::parse($first['sale_date'])->toDateString(),
                'payment_method' => strtolower((string) $first['payment_method']),
                'payment_status' => strtolower((string) $first['payment_status']),
                'total_amount' => $total,
            ]);
        }
        return compact('created', 'updated');
    }

    private function reversePurchaseStock(Purchase $purchase, int $userId): void
    {
        foreach ($purchase->items()->with('product')->get() as $item) {
            $this->stockService->recordMovement($item->product, StockMovement::TYPE_RETURN, $item->quantity, Purchase::class, $purchase->id, 'Import replacement - purchase reversal', $userId, $item->unit_cost);
        }
    }

    private function reverseSaleStock(Sale $sale, int $userId): void
    {
        foreach ($sale->items()->with('product')->get() as $item) {
            $this->stockService->recordMovement($item->product, StockMovement::TYPE_RETURN, $item->quantity, Sale::class, $sale->id, 'Import replacement - sale reversal', $userId, $item->cost_price_at_sale);
        }
    }

    private function findCustomer(int $shopId, string $value): ?Customer
    {
        return Customer::where('shop_id', $shopId)
            ->where(function ($q) use ($value) {
                $q->where('name', $value)->orWhere('phone', $value)->orWhere('email', $value);
            })->first();
    }

    private function validateDate($value, string $label, array &$errors): void
    {
        try { Carbon::parse($value); } catch (Throwable) { $errors[] = "{$label} is not a valid date."; }
    }

    private function validateDecimal($value, string $label, array &$errors, float $min): void
    {
        if (!is_numeric($value) || (float) $value < $min) $errors[] = "{$label} must be a number >= {$min}.";
    }

    private function validatePositive($value, string $label, array &$errors, bool $integer = false): void
    {
        if (!is_numeric($value) || (float) $value <= 0 || ($integer && floor((float) $value) != (float) $value)) $errors[] = "{$label} must be a positive number" . ($integer ? ' without decimals.' : '.');
    }

    private function number($value): float
    {
        return (float) str_replace(',', '', (string) $value);
    }

    private function boolean($value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y', 'on'], true);
    }

    private function nullable($value): ?string
    {
        $value = is_string($value) ? trim($value) : $value;
        return ($value === null || $value === '') ? null : (string) $value;
    }
}
