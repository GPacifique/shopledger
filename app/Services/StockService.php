<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record a stock movement and update product stock
     */
    public function recordMovement(
        Product $product,
        string $type,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $createdBy = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $type, $quantity, $referenceType, $referenceId, $notes, $createdBy) {
            $stockBefore = $product->stock;
            
            // Calculate new stock
            $stockChange = $this->isIncomingType($type) ? abs($quantity) : -abs($quantity);
            $stockAfter = $stockBefore + $stockChange;
            
            // Update product stock
            $product->update(['stock' => max(0, $stockAfter)]);
            $product->refresh();
            
            // Create movement record
            $movement = StockMovement::create([
                'shop_id' => $product->shop_id,
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $stockChange,
                'stock_before' => $stockBefore,
                'stock_after' => $product->stock,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'created_by' => $createdBy ?? auth()->id(),
            ]);
            
            // Check and create/update stock alerts
            StockAlert::checkAndCreateAlert($product);
            
            return $movement;
        });
    }

    /**
     * Record a purchase (stock in)
     */
    public function recordPurchase(Product $product, int $quantity, ?int $purchaseId = null, ?int $createdBy = null): StockMovement
    {
        return $this->recordMovement(
            $product,
            StockMovement::TYPE_PURCHASE,
            $quantity,
            'App\Models\Purchase',
            $purchaseId,
            null,
            $createdBy
        );
    }

    /**
     * Record a sale (stock out)
     */
    public function recordSale(Product $product, int $quantity, ?int $saleId = null, ?int $createdBy = null): StockMovement
    {
        return $this->recordMovement(
            $product,
            StockMovement::TYPE_SALE,
            $quantity,
            'App\Models\Sale',
            $saleId,
            null,
            $createdBy
        );
    }

    /**
     * Record a stock adjustment
     */
    public function recordAdjustment(Product $product, int $newStock, string $notes, ?int $createdBy = null): StockMovement
    {
        $difference = $newStock - $product->stock;
        
        return $this->recordMovement(
            $product,
            StockMovement::TYPE_ADJUSTMENT,
            abs($difference),
            null,
            null,
            $notes,
            $createdBy
        );
    }

    /**
     * Record damaged stock
     */
    public function recordDamage(Product $product, int $quantity, string $notes, ?int $createdBy = null): StockMovement
    {
        return $this->recordMovement(
            $product,
            StockMovement::TYPE_DAMAGE,
            $quantity,
            null,
            null,
            $notes,
            $createdBy
        );
    }

    /**
     * Record a return (stock back in)
     */
    public function recordReturn(Product $product, int $quantity, ?int $saleId = null, ?string $notes = null, ?int $createdBy = null): StockMovement
    {
        return $this->recordMovement(
            $product,
            StockMovement::TYPE_RETURN,
            $quantity,
            'App\Models\Sale',
            $saleId,
            $notes,
            $createdBy
        );
    }

    /**
     * Check if movement type is incoming stock
     */
    protected function isIncomingType(string $type): bool
    {
        return in_array($type, [
            StockMovement::TYPE_PURCHASE,
            StockMovement::TYPE_TRANSFER_IN,
            StockMovement::TYPE_RETURN,
        ]);
    }

    /**
     * Get stock summary for a shop
     */
    public function getShopStockSummary(int $shopId): array
    {
        $products = Product::where('shop_id', $shopId)->get();
        
        return [
            'total_products' => $products->count(),
            'total_stock_value' => $products->sum(fn ($p) => $p->stock * $p->cost_price),
            'total_retail_value' => $products->sum(fn ($p) => $p->stock * $p->sale_price),
            'low_stock_count' => $products->filter(fn ($p) => $p->stock > 0 && $p->stock <= $p->low_stock_threshold)->count(),
            'out_of_stock_count' => $products->filter(fn ($p) => $p->stock <= 0)->count(),
            'healthy_stock_count' => $products->filter(fn ($p) => $p->stock > $p->low_stock_threshold)->count(),
        ];
    }

    /**
     * Get real-time stock data for a shop
     */
    public function getRealTimeStockData(int $shopId, ?string $filter = null): array
    {
        $query = Product::where('shop_id', $shopId)
            ->where('track_stock', true);

        if ($filter === 'low') {
            $query->whereRaw('stock > 0 AND stock <= low_stock_threshold');
        } elseif ($filter === 'out') {
            $query->where('stock', '<=', 0);
        } elseif ($filter === 'healthy') {
            $query->whereRaw('stock > low_stock_threshold');
        }

        $products = $query->orderBy('stock', 'asc')->get();

        return $products->map(function ($product) {
            $status = 'healthy';
            $statusColor = 'green';
            
            if ($product->stock <= 0) {
                $status = 'out_of_stock';
                $statusColor = 'red';
            } elseif ($product->stock <= $product->low_stock_threshold) {
                $status = 'low_stock';
                $statusColor = 'yellow';
            }

            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'stock' => $product->stock,
                'low_stock_threshold' => $product->low_stock_threshold,
                'status' => $status,
                'status_color' => $statusColor,
                'stock_value' => $product->stock * $product->cost_price,
                'retail_value' => $product->stock * $product->sale_price,
                'stock_percentage' => $product->low_stock_threshold > 0 
                    ? min(100, round(($product->stock / ($product->low_stock_threshold * 3)) * 100)) 
                    : 100,
            ];
        })->toArray();
    }
}
