<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use App\Services\SubscriptionLifecycleService;
use App\Services\SubscriptionLimitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubscriptionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_creation_provisions_a_subscription(): void
    {
        $shop = $this->createShop();

        $this->assertNotNull($shop->fresh()->subscription);
        $this->assertSame('active', $shop->subscription->status);
    }

    public function test_renewal_extends_from_the_later_of_now_or_current_expiry(): void
    {
        $this->travelTo(now()->startOfSecond());
        $shop = $this->createShop();
        $subscription = $shop->subscription;
        $originalEnd = now()->addDays(10);
        $subscription->update(['ends_at' => $originalEnd]);

        $payment = app(SubscriptionLifecycleService::class)->recordPayment(
            $subscription->fresh()->load('subscriptionPlan'),
            'mobile_money',
            'MM-123',
            null,
            User::find($shop->created_by)
        );

        $this->assertTrue($payment->period_start->equalTo($originalEnd));
        $this->assertTrue($payment->period_end->equalTo($originalEnd->copy()->addMonth()));
        $this->assertTrue($subscription->fresh()->ends_at->equalTo($payment->period_end));
    }

    public function test_expired_subscription_redirects_shop_staff(): void
    {
        $shop = $this->createShop();
        $shop->subscription()->update([
            'status' => 'active',
            'ends_at' => now()->subDays(10),
        ]);
        $seller = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($seller)
            ->get(route('products.index'))
            ->assertRedirect(route('shop.subscription.expired'));

        $this->assertSame('expired', $shop->subscription()->first()->status);
    }

    public function test_plan_user_limit_is_enforced(): void
    {
        $shop = $this->createShop();
        User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'shop_admin',
        ]);
        $shop->subscription->subscriptionPlan->update(['max_users' => 1]);

        $this->expectException(ValidationException::class);
        app(SubscriptionLimitService::class)->assertCanAddUsers($shop);
    }

    private function createShop(): Shop
    {
        $creator = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);

        return Shop::create([
            'business_name' => 'Subscription Test Shop',
            'business_type' => 'retail',
            'registration_number' => 'SUB-' . fake()->unique()->numerify('######'),
            'email' => fake()->unique()->safeEmail(),
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test address',
            'slug' => 'sub-' . fake()->unique()->slug(2),
            'status' => 'approved',
            'created_by' => $creator->id,
        ]);
    }
}
