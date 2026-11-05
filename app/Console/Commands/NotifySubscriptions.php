<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionDigestMail;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionEndingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotifySubscriptions extends Command
{
    protected $signature = 'subscriptions:notify';

    protected $description = 'Send subscription reminders and the system administrator digest';

    private const THRESHOLDS = [14, 7, 3, 1, 0];

    public function handle(): int
    {
        $today = now()->toDateString();
        $digestItems = [];

        $shops = Shop::query()
            ->with(['subscription.subscriptionPlan', 'users'])
            ->get();

        foreach ($shops as $shop) {
            $subscription = $shop->subscription;

            if (! $subscription) {
                $this->warn("Shop {$shop->id} has no subscription; skipped.");
                continue;
            }

            $this->synchronizeStatus($subscription);
            $subscription->loadMissing('subscriptionPlan');
            $daysLeft = $this->daysUntil($subscription->ends_at);

            if (
                $daysLeft >= 0 && $daysLeft <= 14
                || in_array($subscription->status, ['past_due', 'expired', 'suspended'], true)
            ) {
                $digestItems[] = [
                    'subscription' => $subscription,
                    'shop' => $shop->business_name,
                    'plan' => $subscription->subscriptionPlan?->name ?? __('subscription.no_plan'),
                    'status' => $subscription->status,
                    'expiry' => $subscription->ends_at?->toDateString() ?? '',
                    'days' => $daysLeft,
                ];
            }

            $notice = $this->noticeFor($subscription, $daysLeft);
            if (! $notice) {
                continue;
            }

            $admins = $shop->users->where('role', 'shop_admin');
            foreach (['email', 'database'] as $channel) {
                $this->deliverOnce(
                    $subscription,
                    $notice['type'],
                    $notice['days_before'],
                    $channel,
                    $admins,
                    $today
                );
            }
        }

        $this->sendAdminDigest($digestItems, $today);
        $this->info('Subscription reminders processed.');

        return self::SUCCESS;
    }

    private function synchronizeStatus(Subscription $subscription): void
    {
        if (
            in_array($subscription->status, ['active', 'trial'], true)
            && $subscription->ends_at?->isPast()
        ) {
            $subscription->update([
                'status' => 'past_due',
                'grace_ends_at' => $subscription->ends_at->copy()->addDays(
                    (int) config('subscription.grace_days', 7)
                ),
            ]);
            $subscription->refresh();
        }

        if (
            $subscription->status === 'past_due'
            && $subscription->grace_ends_at?->isPast()
        ) {
            $subscription->update(['status' => 'expired']);
        }
    }

    private function daysUntil(?Carbon $endsAt): int
    {
        if (! $endsAt) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays(
            $endsAt->copy()->startOfDay(),
            false
        );
    }

    private function noticeFor(Subscription $subscription, int $daysLeft): ?array
    {
        if (in_array($subscription->status, ['expired', 'suspended'], true)) {
            return ['type' => 'expired', 'days_before' => 0];
        }

        if ($subscription->status === 'past_due') {
            return ['type' => 'grace', 'days_before' => 0];
        }

        if (in_array($daysLeft, self::THRESHOLDS, true)) {
            return ['type' => 'ending', 'days_before' => $daysLeft];
        }

        return null;
    }

    private function deliverOnce(
        Subscription $subscription,
        string $type,
        int $daysBefore,
        string $channel,
        iterable $recipients,
        string $today
    ): void {
        if (collect($recipients)->isEmpty()) {
            return;
        }

        $created = DB::table('subscription_notifications')->insertOrIgnore([
            'subscription_id' => $subscription->id,
            'type' => $type,
            'days_before' => $daysBefore,
            'channel' => $channel,
            'notification_date' => $today,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $created) {
            return;
        }

        foreach ($recipients as $recipient) {
            $recipient->notify(
                (new SubscriptionEndingNotification(
                    $subscription,
                    $type,
                    $daysBefore,
                    $channel
                ))->locale($recipient->locale ?: 'en')
            );
        }
    }

    private function sendAdminDigest(array $items, string $today): void
    {
        $admins = User::query()->where('role', 'system_admin')->get();

        if ($items === [] || $admins->isEmpty()) {
            return;
        }

        $createdRun = DB::transaction(function () use ($items, $today): int {
            $created = DB::table('subscription_digest_runs')->insertOrIgnore([
                'digest_date' => $today,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! $created) {
                return 0;
            }

            foreach ($items as $item) {
                DB::table('subscription_notifications')->insertOrIgnore([
                    'subscription_id' => $item['subscription']->id,
                    'type' => 'admin_digest',
                    'days_before' => 0,
                    'channel' => 'email',
                    'notification_date' => $today,
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return 1;
        });

        if (! $createdRun) {
            return;
        }

        try {
            Mail::to($admins)->queue(new SubscriptionDigestMail(
                array_map(fn (array $item) => array_diff_key($item, ['subscription' => true]), $items)
            ));
        } catch (Throwable $exception) {
            DB::table('subscription_digest_runs')->where('digest_date', $today)->delete();
            DB::table('subscription_notifications')
                ->where('type', 'admin_digest')
                ->where('notification_date', $today)
                ->delete();
            throw $exception;
        }
    }
}
