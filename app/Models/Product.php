<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\Common\EccLevel;

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
        'buying_price',
        'selling_price',
        'quantity',
        'stock',
        'minimum_stock',
        'expiry_date',
        'product_image',
        'status',
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
        'expiry_date' => 'date',
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

    /**
     * Determine whether the product stock is low (but not out).
     */
    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->minimum_stock;
    }

    /**
     * Determine whether the product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
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