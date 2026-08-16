<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\Common\EccLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'sku',
        'name',
        'category_id',
        'supplier_id',
        'barcode',
        'qr_code',
        'description',

        // Pricing
        'buying_price',
        'selling_price',

        // Current inventory
        'quantity',
        'stock',
        'minimum_stock',

        // Opening inventory
        'opening_quantity',
        'opening_unit_cost',
        'opening_stock_value',
        'opening_stock_date',

        // Other
        'expiry_date',
        'product_image',
        'status',
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',

        'quantity' => 'decimal:2',
        'stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',

        'opening_quantity' => 'decimal:2',
        'opening_unit_cost' => 'decimal:2',
        'opening_stock_value' => 'decimal:2',

        'expiry_date' => 'date',
        'opening_stock_date' => 'date',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Determine whether the product stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->stock > 0
            && $this->stock <= $this->minimum_stock;
    }

    /**
     * Determine whether the product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Calculate opening stock value.
     */
    public function calculateOpeningStockValue(): float
    {
        return (float) $this->opening_quantity
            * (float) $this->opening_unit_cost;
    }

    /**
     * Determine whether this product has opening stock.
     */
    public function hasOpeningStock(): bool
    {
        return (float) $this->opening_quantity > 0;
    }

    /**
     * Generate an SVG QR code representing this product's identifying code.
     */
    public function generateQrCode(): string
    {
        $options = new QROptions([
            'outputType' => QRMarkupSVG::class,
            'eccLevel' => EccLevel::L,
        ]);

        $data = $this->qr_code ?: $this->sku;

        return (new QRCode($options))->render($data);
    }
}