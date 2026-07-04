<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Shop;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_can_be_saved_without_supplier_selection(): void
    {
        $user = User::factory()->create();

        $shop = Shop::create([
            'business_name' => 'Test Shop',
            'business_type' => 'retail',
            'registration_number' => '12345',
            'email' => 'shop@example.com',
            'phone' => '250788000000',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test Address',
            'slug' => 'test-shop',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $user->update(['shop_id' => $shop->id, 'role' => 'shop_admin']);

        $category = Category::create([
            'shop_id' => $shop->id,
            'name' => 'General',
            'status' => 'active',
        ]);

        $supplier = Supplier::create([
            'shop_id' => $shop->id,
            'name' => 'Test Supplier',
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'sku' => 'SKU-1',
            'name' => 'Test Product',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'buying_price' => 100,
            'selling_price' => 150,
            'stock' => 0,
            'minimum_stock' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('purchases.store'), [
            'supplier_id' => '',
            'purchase_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_cost' => 100,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchases', [
            'shop_id' => $shop->id,
            'supplier_id' => null,
            'created_by' => $user->id,
        ]);
    }
}
