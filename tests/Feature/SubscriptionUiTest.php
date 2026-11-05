<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubscriptionUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_plans_uses_only_the_canonical_plan_table(): void
    {
        $this->assertTrue(Schema::hasTable('subscription_plans'));
        $this->assertFalse(Schema::hasTable('subscriptionplans'));
        $this->assertTrue(Schema::hasColumn('subscription_plans', 'billing_period'));
        $this->assertTrue(Schema::hasColumn('subscription_plans', 'is_active'));
        $this->assertFalse(Schema::hasColumn('subscription_plans', 'billing_cycle'));
        $this->assertFalse(Schema::hasColumn('subscription_plans', 'status'));
    }

    public function test_system_admin_can_manage_plans_and_view_subscription_overview(): void
    {
        $admin = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.subscription-plans.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.subscription-plans.store'), [
                'name' => 'Test Quarterly',
                'price' => 18000,
                'billing_period' => 'quarterly',
                'max_users' => 8,
                'max_products' => '',
                'features' => ['Monthly reports', ''],
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.subscription-plans.index'));

        $this->assertDatabaseHas('subscription_plans', [
            'slug' => 'test-quarterly',
            'billing_period' => 'quarterly',
            'max_products' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.shops.index'))
            ->assertOk()
            ->assertSee('Monthly recurring revenue');
    }

    public function test_shop_admin_can_view_subscription_and_payment_history(): void
    {
        $owner = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);
        $shop = Shop::create([
            'business_name' => 'Subscription UI Shop',
            'business_type' => 'retail',
            'registration_number' => 'UI-123',
            'email' => 'subscription-ui@example.com',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test address',
            'slug' => 'subscription-ui-shop',
            'status' => 'approved',
            'created_by' => $owner->id,
        ]);
        $shopAdmin = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'shop_admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($shopAdmin)
            ->get(route('shop.subscriptions.index'))
            ->assertOk()
            ->assertSee('Payment history');
    }

    public function test_system_admin_can_assign_a_plan_to_a_shop_without_a_subscription(): void
    {
        $admin = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);
        $shop = Shop::create([
            'business_name' => 'Unassigned Subscription Shop',
            'business_type' => 'retail',
            'registration_number' => 'UI-456',
            'email' => 'unassigned-subscription@example.com',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test address',
            'slug' => 'unassigned-subscription-shop',
            'status' => 'approved',
            'created_by' => $admin->id,
        ]);
        $shop->subscription->delete();
        $plan = SubscriptionPlan::query()->where('is_active', true)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.shops.index'))
            ->assertOk()
            ->assertSee(__('subscription.assign_plan'));

        $this->actingAs($admin)
            ->post(route('admin.shops.subscription.assign', $shop), [
                'subscription_plan_id' => $plan->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('subscriptions', [
            'shop_id' => $shop->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_system_admin_dashboard_shows_subscription_payment_totals(): void
    {
        $admin = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);
        $shop = Shop::create([
            'business_name' => 'Payment Stats Shop',
            'business_type' => 'retail',
            'registration_number' => 'UI-789',
            'email' => 'payment-stats@example.com',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test address',
            'slug' => 'payment-stats-shop',
            'status' => 'approved',
            'created_by' => $admin->id,
        ]);
        $subscription = $shop->subscription;

        foreach ([
            ['amount' => 12000, 'paid_at' => now()],
            ['amount' => 18000, 'paid_at' => now()->subMonth()->subDay()],
        ] as $payment) {
            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'shop_id' => $shop->id,
                'amount' => $payment['amount'],
                'method' => 'cash',
                'paid_at' => $payment['paid_at'],
                'period_start' => $payment['paid_at'],
                'period_end' => $payment['paid_at']->copy()->addMonth(),
                'recorded_by' => $admin->id,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(__('subscription.payments_received_this_month'))
            ->assertSee(__('subscription.total_payments_received'))
            ->assertSee(__('subscription.payment_count_this_month'))
            ->assertSee('12,000 RWF')
            ->assertSee('30,000 RWF');
    }

    public function test_system_admin_shop_creation_assigns_the_selected_canonical_plan(): void
    {
        $admin = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);
        $shopAdmin = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
        $plan = SubscriptionPlan::query()->where('is_active', true)->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.shops.store'), [
                'user_id' => $shopAdmin->id,
                'business_name' => 'Canonical Plan Shop',
                'business_type' => 'retail',
                'registration_number' => 'CANON-001',
                'email' => 'canonical-plan@example.com',
                'country' => 'Rwanda',
                'city' => 'Kigali',
                'address' => 'Test address',
                'subscription_plan_id' => $plan->id,
                'status' => 'approved',
            ])
            ->assertRedirect(route('admin.shops.index'));

        $shop = Shop::query()->where('registration_number', 'CANON-001')->firstOrFail();
        $this->assertSame($plan->id, $shop->subscription->subscription_plan_id);
        $this->assertSame($shop->id, $shopAdmin->fresh()->shop_id);
        $this->assertDatabaseHas('subscriptions', [
            'shop_id' => $shop->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_system_admin_dashboard_uses_subscription_plan_for_plan_stats(): void
    {
        $admin = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);
        $shop = Shop::create([
            'business_name' => 'Canonical Stats Shop',
            'business_type' => 'retail',
            'registration_number' => 'CANON-002',
            'email' => 'canonical-stats@example.com',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test address',
            'slug' => 'canonical-stats-shop',
            'status' => 'approved',
            'created_by' => $admin->id,
        ]);
        $monthlyPrice = (float) $shop->subscription->subscriptionPlan->price;

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(number_format($monthlyPrice) . ' RWF')
            ->assertSee('Shops with a plan');
    }
}
