<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_view_products_and_expenses_but_cannot_manage_them(): void
    {
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
            'created_by' => 1,
        ]);

        $admin = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'shop_admin',
        ]);

        $seller = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'seller',
        ]);

        $category = Category::create([
            'shop_id' => $shop->id,
            'name' => 'General',
            'status' => 'active',
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'sku' => 'SKU-1',
            'name' => 'Test Product',
            'category_id' => $category->id,
            'buying_price' => 100,
            'selling_price' => 150,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'shop_id' => $shop->id,
            'name' => 'Office',
            'description' => 'Office costs',
        ]);

        $expense = Expense::create([
            'shop_id' => $shop->id,
            'category_id' => $expenseCategory->id,
            'amount' => 2500,
            'expense_date' => now()->toDateString(),
            'description' => 'Stationery',
            'status' => 'paid',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($seller)->get(route('products.index'))->assertOk();
        $this->actingAs($seller)->get(route('products.show', $product))->assertOk();
        $this->actingAs($seller)->get(route('expenses.index'))->assertOk();

        $this->actingAs($seller)->get(route('products.edit', $product))->assertForbidden();
        $this->actingAs($seller)->delete(route('products.destroy', $product))->assertForbidden();
        $this->actingAs($seller)->get(route('expenses.edit', $expense))->assertForbidden();
        $this->actingAs($seller)->delete(route('expenses.destroy', $expense))->assertForbidden();
    }
}
