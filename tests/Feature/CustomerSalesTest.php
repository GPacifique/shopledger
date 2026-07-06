<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_can_be_linked_to_a_customer(): void
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
            'status' => 'approved',
            'created_by' => $user->id,
        ]);

        $user->update(['shop_id' => $shop->id, 'role' => 'shop_admin']);

        $category = Category::create([
            'shop_id' => $shop->id,
            'name' => 'General',
            'status' => 'active',
        ]);

        $customer = Customer::create([
            'shop_id' => $shop->id,
            'name' => 'Jane Doe',
            'phone' => '250788111111',
            'email' => 'jane@example.com',
            'address' => 'Kigali',
        ]);

        $supplier = \App\Models\Supplier::create([
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
            'stock' => 10,
            'minimum_stock' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 150,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', [
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_sale_export_renders_receipt_successfully(): void
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
            'status' => 'approved',
            'created_by' => $user->id,
        ]);

        $user->update(['shop_id' => $shop->id, 'role' => 'shop_admin']);

        $category = Category::create([
            'shop_id' => $shop->id,
            'name' => 'General',
            'status' => 'active',
        ]);

        $supplier = \App\Models\Supplier::create([
            'shop_id' => $shop->id,
            'name' => 'Test Supplier',
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'sku' => 'SKU-2',
            'name' => 'Export Product',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'buying_price' => 80,
            'selling_price' => 120,
            'stock' => 10,
            'minimum_stock' => 1,
        ]);

        $sale = Sale::create([
            'shop_id' => $shop->id,
            'customer_id' => null,
            'sale_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'created_by' => $user->id,
            'total_amount' => 0,
        ]);

        $sale->items()->create([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 120,
            'cost_price_at_sale' => 80,
            'line_total' => 120,
        ]);

        $sale->update(['total_amount' => 120]);

        $response = $this->actingAs($user)->get(route('sales.export', $sale));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
