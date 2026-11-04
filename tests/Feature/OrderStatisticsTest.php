<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_index_shows_shop_scoped_order_statistics(): void
    {
        $user = User::factory()->create([
            'role' => 'shop_admin',
            'email_verified_at' => now(),
        ]);
        $shop = Shop::create([
            'business_name' => 'Order Statistics Shop',
            'business_type' => 'retail',
            'registration_number' => 'ORDER-STATS-001',
            'email' => 'orders-stats@example.com',
            'phone' => '250788000001',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => '1 Main Street',
            'slug' => 'order-statistics-shop',
            'status' => 'approved',
            'created_by' => $user->id,
        ]);
        $user->update(['shop_id' => $shop->id]);

        foreach ([
            ['status' => 'pending', 'total_amount' => 500],
            ['status' => 'approved', 'total_amount' => 1500],
            ['status' => 'rejected', 'total_amount' => 700],
        ] as $index => $attributes) {
            Order::create($attributes + [
                'shop_id' => $shop->id,
                'created_by' => $user->id,
                'order_number' => 'STATS-' . ($index + 1),
            ]);
        }

        $this->actingAs($user)
            ->get(route('shops.orders.index', ['shop' => $shop, 'status' => 'pending']))
            ->assertOk()
            ->assertSee('Order statistics')
            ->assertSee('1,500 RWF')
            ->assertSee('STATS-1')
            ->assertSee(route('shops.orders.print', [$shop, $shop->orders()->where('order_number', 'STATS-1')->first()]), false)
            ->assertSee('Print')
            ->assertDontSee('STATS-2')
            ->assertDontSee('STATS-3');
    }
}
