<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 10000,
                'billing_period' => 'monthly',
                'max_users' => 3,
                'max_products' => 100,
                'features' => ['Inventory management', 'Sales and expenses'],
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'price' => 25000,
                'billing_period' => 'monthly',
                'max_users' => 10,
                'max_products' => 500,
                'features' => ['Inventory management', 'Sales and expenses', 'Reports and imports'],
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'price' => 50000,
                'billing_period' => 'monthly',
                'max_users' => 30,
                'max_products' => null,
                'features' => ['All features', 'Unlimited products', 'Priority support'],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                [...$plan, 'is_active' => true]
            );
        }
    }
}
