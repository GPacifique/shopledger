<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shop;
use App\Models\User;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\IncomeCategory;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessOwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_owner_dashboard_is_available_without_a_primary_shop(): void
    {
        $owner = User::factory()->create([
            'role' => 'business_owner',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertRedirect(route('business.dashboard'));

        $this->get(route('business.dashboard'))
            ->assertOk()
            ->assertSee('No shops are assigned to your account yet.');
    }

    public function test_business_owner_sees_only_their_shops_and_combined_sales(): void
    {
        $owner = User::factory()->create([
            'role' => 'business_owner',
            'email_verified_at' => now(),
        ]);
        $firstShop = $this->createShop($owner, 'North Shop');
        $secondShop = $this->createShop($owner, 'South Shop');
        $unrelatedShop = $this->createShop(User::factory()->create(), 'Hidden Shop');
        $owner->shops()->attach([$firstShop->id, $secondShop->id]);

        Sale::create([
            'shop_id' => $firstShop->id,
            'sale_date' => today(),
            'total_amount' => 125.50,
            'created_by' => $owner->id,
        ]);
        Sale::create([
            'shop_id' => $unrelatedShop->id,
            'sale_date' => today(),
            'total_amount' => 900,
            'created_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->get(route('business.dashboard'))
            ->assertOk()
            ->assertSee('North Shop')
            ->assertSee('South Shop')
            ->assertDontSee('Hidden Shop')
            ->assertSee('125.50')
            ->assertDontSee('900.00');

        $this->get(route('business.dashboard', [
            'shop' => $secondShop->id,
            'period' => 'this_month',
        ]))
            ->assertOk()
            ->assertViewHas('shopPerformance', fn ($performance) => $performance->count() === 1
                && $performance->first()['shop']->is($secondShop))
            ->assertViewHas('salesTotal', 0.0);
    }

    public function test_business_owner_can_add_another_shop_from_the_dashboard(): void
    {
        $owner = User::factory()->create([
            'role' => 'business_owner',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post(route('business.shops.store'), [
                'business_name' => 'New Location',
                'business_type' => 'Retail',
                'registration_number' => 'NEW-LOCATION-001',
                'email' => 'new-location@example.com',
                'country' => 'Rwanda',
                'city' => 'Kigali',
                'address' => '1 Main Street',
            ])
            ->assertRedirect(route('business.dashboard'));

        $shop = Shop::query()->where('business_name', 'New Location')->firstOrFail();

        $this->assertDatabaseHas('business_owner_shop', [
            'business_owner_id' => $owner->id,
            'shop_id' => $shop->id,
        ]);
        $this->assertSame('pending', $shop->status);
        $this->assertSame($shop->id, $owner->fresh()->shop_id);

        $this->actingAs($owner)
            ->get(route('business.dashboard'))
            ->assertOk()
            ->assertSee('New Location')
            ->assertSee('pending');
    }

    public function test_business_owner_can_open_only_a_linked_approved_shop(): void
    {
        $owner = User::factory()->create([
            'role' => 'business_owner',
            'email_verified_at' => now(),
        ]);
        $shop = $this->createShop($owner, 'Managed Shop');
        $unrelatedShop = $this->createShop(User::factory()->create(), 'Unrelated Shop');
        $owner->shops()->attach($shop);

        $this->actingAs($owner)
            ->post(route('business.shops.select', $shop))
            ->assertRedirect(route('shop.dashboard'))
            ->assertSessionHas('active_business_shop_id', $shop->id);

        $this->get(route('products.index'))->assertOk();

        $this->post(route('business.shops.select', $unrelatedShop))
            ->assertForbidden();
    }

    public function test_dashboard_uses_existing_financial_and_inventory_calculations(): void
    {
        $owner = User::factory()->create([
            'role' => 'business_owner',
            'email_verified_at' => now(),
        ]);
        $shop = $this->createShop($owner, 'Analytics Shop');
        $owner->shops()->attach($shop);

        $category = Category::create(['shop_id' => $shop->id, 'name' => 'Food']);
        $product = Product::create([
            'shop_id' => $shop->id,
            'sku' => 'ANALYTICS-1',
            'name' => 'Coffee',
            'category_id' => $category->id,
            'buying_price' => 20,
            'selling_price' => 50,
            'quantity' => 2,
            'stock' => 2,
            'minimum_stock' => 3,
            'low_stock_threshold' => 10,
        ]);
        $customer = Customer::create([
            'shop_id' => $shop->id,
            'name' => 'Returning Customer',
        ]);
        $sale = Sale::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'sale_date' => today(),
            'total_amount' => 100,
            'payment_method' => 'cash',
            'payment_status' => 'Unpaid',
            'created_by' => $owner->id,
        ]);
        SaleItem::create([
            'shop_id' => $shop->id,
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50,
            'cost_price_at_sale' => 20,
            'line_total' => 100,
        ]);
        $supplier = Supplier::create([
            'shop_id' => $shop->id,
            'name' => 'Local Supplier',
        ]);
        $purchase = Purchase::create([
            'shop_id' => $shop->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => today(),
            'total_amount' => 40,
            'created_by' => $owner->id,
        ]);
        PurchaseItem::create([
            'shop_id' => $shop->id,
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_cost' => 20,
            'line_total' => 40,
        ]);
        Expense::create([
            'shop_id' => $shop->id,
            'amount' => 10,
            'expense_date' => today(),
            'status' => 'unpaid',
            'created_by' => $owner->id,
        ]);
        $incomeCategory = IncomeCategory::create([
            'shop_id' => $shop->id,
            'name' => 'Service income',
        ]);
        OtherIncome::create([
            'shop_id' => $shop->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 5,
            'income_date' => today(),
            'created_by' => $owner->id,
        ]);
        StockMovement::create([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'type' => 'return',
            'quantity' => 1,
            'stock_before' => 1,
            'stock_after' => 2,
            'created_by' => $owner->id,
        ]);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'sale_date' => today()->startOfDay()->toDateTimeString(),
        ]);

        $this->actingAs($owner)
            ->get(route('business.dashboard', ['period' => 'today']))
            ->assertOk()
            ->assertViewHas('period', 'today')
            ->assertViewHas('salesTotal', 100.0)
            ->assertViewHas('purchaseTotal', 40.0)
            ->assertViewHas('grossProfit', 60.0)
            ->assertViewHas('netProfit', 55.0)
            ->assertViewHas('lowStockTotal', 1)
            ->assertViewHas('inventoryValue', 40.0)
            ->assertViewHas('customerCount', 1)
            ->assertViewHas('supplierCount', 1)
            ->assertViewHas('unpaidSales', 100.0)
            ->assertViewHas('unpaidExpenses', 10.0)
            ->assertSee('Returning Customer')
            ->assertSee('Local Supplier');
    }

    private function createShop(User $creator, string $name): Shop
    {
        return Shop::create([
            'business_name' => $name,
            'business_type' => 'Retail',
            'registration_number' => strtoupper(str_replace(' ', '-', $name)),
            'email' => strtolower(str_replace(' ', '-', $name)) . '@example.com',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => '1 Main Street',
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'status' => 'approved',
            'created_by' => $creator->id,
        ]);
    }
}
