<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Tenant isolation for every shop-owned model (products, sales, expenses, orders, ...).
 *
 * - Reads: logged-in shop users only ever see rows of their own shop.
 *   Route-model binding uses the same query, so /sales/{id} of another shop returns 404.
 * - Writes: shop_id is forced to the user's shop on create and cannot be changed on update,
 *   so a tampered shop_id in a form is ignored.
 * - Deny by default: a logged-in non-admin with no shop sees nothing.
 * - System admins and console/queue contexts (no logged-in user) are not restricted.
 *
 * Requires a `shop_id` column on the model's table and `shop_id` on the users table.
 */
trait BelongsToShop
{
    protected static function bootBelongsToShop(): void
    {
        static::addGlobalScope('shop', function (Builder $builder) {
            $user = Auth::user();

            if (! $user || static::isSystemAdmin($user)) {
                return;
            }

            if (empty($user->shop_id)) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $builder->where($builder->getModel()->getTable() . '.shop_id', $user->shop_id);
        });

        static::creating(function ($model) {
            $user = Auth::user();

            if ($user && ! static::isSystemAdmin($user)) {
                $model->shop_id = $user->shop_id;
            }
        });

        static::updating(function ($model) {
            $user = Auth::user();

            if ($user && ! static::isSystemAdmin($user) && $model->isDirty('shop_id')) {
                $model->shop_id = $model->getOriginal('shop_id');
            }
        });
    }

    protected static function isSystemAdmin($user): bool
    {
        return strtolower(trim((string) $user->role)) === 'system_admin';
    }

    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }
}