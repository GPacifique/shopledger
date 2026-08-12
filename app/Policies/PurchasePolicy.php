<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Purchase;

class PurchasePolicy
{
    public function view(User $user, Purchase $purchase): bool
    {
        return $user->isSystemAdmin() || $user->shop_id === $purchase->shop_id;
    }

    public function update(User $user, Purchase $purchase): bool
    {
        return $user->isSystemAdmin()
            || ($user->shop_id === $purchase->shop_id && $user->role === 'admin');
    }

    public function delete(User $user, Purchase $purchase): bool
    {
        return $this->update($user, $purchase); // same rule, keep or split later
    }
}