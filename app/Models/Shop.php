<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'business_type',
        'registration_number',
        'tin_number',
        'email',
        'phone',
        'country',
        'city',
        'address',
        'logo',
        'subscriptionplan_id',
        'slug',
        'status',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
        'print_settings',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'print_settings' => 'array',
    ];

    public static function defaultPrintSettings(): array
    {
        return [
            'receipt_paper_size' => '80mm',
            'invoice_paper_size' => 'a4',
            'receipt_custom_width_mm' => 80,
            'invoice_custom_width_mm' => 210,
            'invoice_custom_height_mm' => 297,
            'show_logo' => true,
            'show_customer' => true,
            'show_tax' => true,
            'show_qr' => true,
            'show_cashier' => true,
            'show_contact' => true,
            'receipt_footer' => 'Thank you for shopping with us!',
            'invoice_notes' => '',
            'invoice_terms' => '',
            'auto_print' => false,
        ];
    }

    public function resolvedPrintSettings(): array
    {
        return array_replace(self::defaultPrintSettings(), $this->print_settings ?? []);
    }

    /*
    |--------------------------------------------------------------------------
    | Shop Approval
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return strtolower((string) $this->status) === 'approved';
    }

    public function isPending(): bool
    {
        return strtolower((string) $this->status) === 'pending';
    }

    public function isRejected(): bool
    {
        return strtolower((string) $this->status) === 'rejected';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(
            SubscriptionPlan::class,
            'subscriptionplan_id'
        );
    }

    public function subscriptions()
    {
        return $this->hasMany(ShopSubscription::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reportSchedules(): HasMany
    {
        return $this->hasMany(ReportSchedule::class);
    }

    public function reportDeliveries(): HasMany
    {
        return $this->hasMany(ReportDelivery::class);
    }
    public function getLogoUrlAttribute(): ?string
{
    if (! $this->logo) {
        return null;
    }

    // New logos: images/logos/xxx.png  |  older logos: shop-logos/xxx.png (in storage)
    return str_starts_with($this->logo, 'images/')
        ? asset($this->logo)
        : asset('storage/' . $this->logo);
}
}