<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;

class Shop extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Shop $shop): void {
            if ($shop->subscriptions()->exists()) {
                return;
            }

            $plan = SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderBy('price')
                ->firstOrFail();
            $startsAt = now();
            $pending = $shop->isPending();
            $endsAt = $pending
                ? $startsAt->copy()->addDays((int) config('subscription.trial_days', 14))
                : match ($plan->billing_period) {
                    'quarterly' => $startsAt->copy()->addMonths(3),
                    'yearly' => $startsAt->copy()->addYear(),
                    default => $startsAt->copy()->addMonth(),
                };

            Subscription::create([
                'shop_id' => $shop->id,
                'subscription_plan_id' => $plan->id,
                'status' => $pending ? 'trial' : ($shop->isApproved() ? 'active' : 'expired'),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'trial_ends_at' => $pending ? $endsAt : null,
            ]);
        });
    }

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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    public function businessOwners()
    {
        return $this->belongsToMany(User::class, 'business_owner_shop', 'shop_id', 'business_owner_id')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
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