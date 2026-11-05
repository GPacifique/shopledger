<?php

namespace Tests\Feature;

use App\Mail\SubscriptionDigestMail;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\SubscriptionEndingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionNotificationCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_threshold_reminders_and_digest_are_idempotent(): void
    {
        $this->travelTo(now()->startOfDay());
        Notification::fake();
        Mail::fake();

        $systemAdmin = User::factory()->create([
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);
        $shop = Shop::create([
            'business_name' => 'Reminder Shop',
            'business_type' => 'retail',
            'registration_number' => 'REM-123',
            'email' => 'reminder-shop@example.com',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test address',
            'slug' => 'reminder-shop',
            'status' => 'approved',
            'created_by' => $systemAdmin->id,
        ]);
        $shopAdmin = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'shop_admin',
            'email_verified_at' => now(),
        ]);

        foreach ([14, 7, 3, 1, 0] as $days) {
            $shop->subscription()->update([
                'status' => 'active',
                'ends_at' => now()->addDays($days),
            ]);
            $this->artisan('subscriptions:notify')->assertExitCode(0);
        }

        $this->assertDatabaseCount('subscription_notifications', 11);
        Notification::assertSentToTimes($shopAdmin, SubscriptionEndingNotification::class, 10);
        Mail::assertQueued(SubscriptionDigestMail::class, 1);

        $this->artisan('subscriptions:notify')->assertExitCode(0);

        $this->assertDatabaseCount('subscription_notifications', 11);
        Notification::assertSentToTimes($shopAdmin, SubscriptionEndingNotification::class, 10);
        Mail::assertQueued(SubscriptionDigestMail::class, 1);
    }
}
