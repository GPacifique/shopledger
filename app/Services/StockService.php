<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    /**
     * Record a stock movement and update product stock.
     *
     * Supports fractional quantities.
     */
    public function recordMovement(
        Product $product,
        string $type,
        float $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $createdBy = null,
        ?float $unitCost = null,
        ?string $movementDate = null
    ): StockMovement {
        return DB::transaction(function () use (
            $product,
            $type,
            $quantity,
            $referenceType,
            $referenceId,
            $notes,
            $createdBy,
            $unitCost,
            $movementDate
        ) {
            if ($quantity == 0) {
                throw new InvalidArgumentException(
                    'Stock movement quantity cannot be zero.'
                );
            }

            $incoming = $this->isIncomingType($type);

            /*
             * Always work with a positive quantity here.
             * The direction is determined by the movement type.
             */
            $quantityChange = $incoming
                ? abs($quantity)
                : -abs($quantity);

            $stockBefore = (float) $product->stock;
            $stockAfter = $stockBefore + $quantityChange;

            /*
             * Never allow stock to become negative.
             */
            if ($stockAfter < 0) {
                throw new InvalidArgumentException(
                    "Insufficient stock for product '{$product->name}'. "
                    . "Available: {$stockBefore}, requested: " . abs($quantity)
                );
            }

            /*
             * Update both stock and quantity because the existing
             * MahWi product structure keeps them synchronized.
             */
            $product->update([
                'stock' => $stockAfter,
                'quantity' => $stockAfter,
            ]);

            $product->refresh();

            /*
             * Calculate movement financial values where applicable.
             */
            $effectiveUnitCost = $unitCost !== null
                ? (float) $unitCost
                : (float) $product->buying_price;

            $totalCost = abs($quantity) * $effectiveUnitCost;

            /*
             * Create movement using the current StockMovement schema.
             */
            $movement = StockMovement::create([
                'shop_id' => $product->shop_id,
                'product_id' => $product->id,

                'type' => $type,

                'quantity_change' => $quantityChange,
                'quantity_after' => $stockAfter,

                'unit_cost' => $effectiveUnitCost,
                'total_cost' => $totalCost,

                'reference_type' => $referenceType,
                'reference_id' => $referenceId,

                'notes' => $notes,

                'movement_date' => $movementDate ?? now(),

                'created_by' => $createdBy ?? auth()->id(),
            ]);

            /*
             * Update stock alerts after the stock change.
             */
            if (method_exists(StockAlert::class, 'checkAndCreateAlert')) {
                StockAlert::checkAndCreateAlert($product);
            }

            return $movement;
        });
    }

    /**
     * Record a purchase.
     *
     * Stock IN.
     */
    public function recordPurchase(
        Product $product,
        float $quantity,
        ?int $purchaseId = null,
        ?int $createdBy = null,
        ?float $unitCost = null
    ): StockMovement {
        return $this->recordMovement(
            product: $product,
            type: StockMovement::TYPE_PURCHASE,
            quantity: $quantity,
            referenceType: 'App\Models\Purchase',
            referenceId: $purchaseId,
            notes: null,
            createdBy: $createdBy,
            unitCost: $unitCost ?? $product->buying_price
        );
    }

    /**
     * Record a sale.
     *
     * Stock OUT.
     */
    public function recordSale(
        Product $product,
        float $quantity,
        ?int $saleId = null,
        ?int $createdBy = null
    ): StockMovement {
        return $this->recordMovement(
            product: $product,
            type: StockMovement::TYPE_SALE,
            quantity: $quantity,
            referenceType: 'App\Models\Sale',
            referenceId: $saleId,
            notes: null,
            createdBy: $createdBy,
            unitCost: $product->buying_price
        );
    }

    /**
     * Record an order.
     *
     * An order reserves/uses stock depending on your workflow,
     * therefore it is treated as stock OUT here.
     */
    public function recordOrder(
        Product $product,
        float $quantity,
        ?int $orderId = null,
        ?int $createdBy = null,
        ?string $notes = null
    ): StockMovement {
        return $this->recordMovement(
            product: $product,
            type: StockMovement::TYPE_ORDER,
            quantity: $quantity,
            referenceType: 'App\Models\Order',
            referenceId: $orderId,
            notes: $notes,
            createdBy: $createdBy,
            unitCost: $product->buying_price
        );
    }

    /**
     * Adjust stock to an exact target quantity.
     */
    public function recordAdjustment(
        Product $product,
        float $newStock,
        string $notes,
        ?int $createdBy = null
    ): StockMovement {
        $currentStock = (float) $product->stock;
        $difference = $newStock - $currentStock;

        if ($difference == 0) {
            throw new InvalidArgumentException(
                'Stock adjustment does not change the current stock.'
            );
        }

        /*
         * recordMovement determines direction from the type.
         * Adjustment can be either IN or OUT, so handle it explicitly.
         */
        return DB::transaction(function () use (
            $product,
            $newStock,
            $difference,
            $notes,
            $createdBy
        ) {
            $stockBefore = (float) $product->stock;

            if ($newStock < 0) {
                throw new InvalidArgumentException(
                    'Adjusted stock cannot be negative.'
                );
            }

            $product->update([
                'stock' => $newStock,
                'quantity' => $newStock,
            ]);

            $product->refresh();

            $unitCost = (float) $product->buying_price;

            $movement = StockMovement::create([
                'shop_id' => $product->shop_id,
                'product_id' => $product->id,

                'type' => StockMovement::TYPE_ADJUSTMENT,

                'quantity_change' => $difference,
                'quantity_after' => $newStock,

                'unit_cost' => $unitCost,
                'total_cost' => abs($difference) * $unitCost,

                'reference_type' => null,
                'reference_id' => null,

                'notes' => $notes,

                'movement_date' => now(),

                'created_by' => $createdBy ?? auth()->id(),
            ]);

            if (method_exists(StockAlert::class, 'checkAndCreateAlert')) {
                StockAlert::checkAndCreateAlert($product);
            }

            return $movement;
        });
    }

    /**
     * Record damaged stock.
     *
     * Stock OUT.
     */
    public function recordDamage(
        Product $product,
        float $quantity,
        string $notes,
        ?int $createdBy = null
    ): StockMovement {
        return $this->recordMovement(
            product: $product,
            type: StockMovement::TYPE_DAMAGE,
            quantity: $quantity,
            referenceType: null,
            referenceId: null,
            notes: $notes,
            createdBy: $createdBy,
            unitCost: $product->buying_price
        );
    }

    /**
     * Record a returned sale item.
     *
     * Stock IN.
     */
    public function recordReturn(
        Product $product,
        float $quantity,
        ?int $saleId = null,
        ?string $notes = null,
        ?int $createdBy = null
    ): StockMovement {
        return $this->recordMovement(
            product: $product,
            type: StockMovement::TYPE_RETURN,
            quantity: $quantity,
            referenceType: 'App\Models\Sale',
            referenceId: $saleId,
            notes: $notes,
            createdBy: $createdBy,
            unitCost: $product->buying_price
        );
    }

    /**
     * Record stock transferred into this shop.
     *
     * Stock IN.
     */
    public function recordTransferIn(
        Product $product,
        float $quantity,
        ?int $referenceId = null,
        ?int $createdBy = null,
        ?string $notes = null
    ): StockMovement {
        return $this->recordMovement(
            product: $product,
            type: StockMovement::TYPE_TRANSFER,
            quantity: $quantity,
            referenceType: 'transfer',
            referenceId: $referenceId,
            notes: $notes,
            createdBy: $createdBy,
            unitCost: $product->buying_price
        );
    }

    /**
     * Determine whether the movement increases stock.
     */
    protected function isIncomingType(string $type): bool
    {
        return in_array($type, [
            StockMovement::TYPE_PURCHASE,
            StockMovement::TYPE_RETURN,
            StockMovement::TYPE_OPENING,
        ], true);
    }

    /**
     * Get stock summary for a shop.
     */
    public function getShopStockSummary(int $shopId): array
    {
        $products = Product::where('shop_id', $shopId)->get();

        return [
            'total_products' => $products->count(),

            'total_stock_value' => $products->sum(
                fn ($product) =>
                    (float) $product->stock *
                    (float) $product->buying_price
            ),

            'total_retail_value' => $products->sum(
                fn ($product) =>
                    (float) $product->stock *
                    (float) $product->selling_price
            ),

            'low_stock_count' => $products->filter(
                fn ($product) =>
                    (float) $product->stock > 0 &&
                    (float) $product->stock <=
                    (float) $product->minimum_stock
            )->count(),

            'out_of_stock_count' => $products->filter(
                fn ($product) =>
                    (float) $product->stock <= 0
            )->count(),

            'healthy_stock_count' => $products->filter(
                fn ($product) =>
                    (float) $product->stock >
                    (float) $product->minimum_stock
            )->count(),
        ];
    }

    /**
     * Get real-time stock data for a shop.
     */
    public function getRealTimeStockData(
        int $shopId,
        ?string $filter = null
    ): array {
        $query = Product::where('shop_id', $shopId);

        if ($filter === 'low') {
            $query->whereRaw(
                'stock > 0 AND stock <= minimum_stock'
            );
        } elseif ($filter === 'out') {
            $query->where('stock', '<=', 0);
        } elseif ($filter === 'healthy') {
            $query->whereRaw(
                'stock > minimum_stock'
            );
        }

        $products = $query
            ->orderBy('stock', 'asc')
            ->get();

        return $products->map(function ($product) {
            $stock = (float) $product->stock;
            $minimumStock = (float) $product->minimum_stock;

            if ($stock <= 0) {
                $status = 'out_of_stock';
                $statusColor = 'red';
            } elseif ($stock <= $minimumStock) {
                $status = 'low_stock';
                $statusColor = 'yellow';
            } else {
                $status = 'healthy';
                $statusColor = 'green';
            }

            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,

                'stock' => $stock,
                'minimum_stock' => $minimumStock,

                'status' => $status,
                'status_color' => $statusColor,

                'stock_value' =>
                    $stock * (float) $product->buying_price,

                'retail_value' =>
                    $stock * (float) $product->selling_price,

                'stock_percentage' => $minimumStock > 0
                    ? min(
                        100,
                        round(
                            ($stock / ($minimumStock * 3)) * 100
                        )
                    )
                    : 100,
            ];
        })->toArray();
    }
}