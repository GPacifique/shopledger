<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class SubscriptionEndingNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $noticeType,
        public int $daysBefore,
        public string $channel
    ) {
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        return [$this->channel === 'email' ? 'mail' : 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->subscription->loadMissing(['shop', 'subscriptionPlan']);
        $shop = $this->subscription->shop;
        $plan = $this->subscription->subscriptionPlan;
        $data = $this->mailData($shop, $plan);
        $subjectKey = match ($this->noticeType) {
            'grace' => 'subscription.grace_subject',
            'expired' => 'subscription.expired_subject',
            default => 'subscription.email_subject',
        };
        $data['heading'] = __($subjectKey, ['shop' => $shop->business_name]);

        return (new MailMessage())
            ->subject(__($subjectKey, ['shop' => $shop->business_name]))
            ->view('emails.subscriptions.ending', $data);
    }

    public function toDatabase(object $notifiable): array
    {
        $this->subscription->loadMissing(['shop', 'subscriptionPlan']);
        $shop = $this->subscription->shop;
        $plan = $this->subscription->subscriptionPlan;

        return [
            'title' => __($this->noticeType === 'expired' ? 'subscription.expired_title' : 'subscription.ending_title'),
            'message' => $this->message($shop->business_name, $plan->name, $this->subscription->ends_at?->format('Y-m-d')),
            'url' => route('shop.subscriptions.index'),
            'shop_id' => $shop->id,
            'subscription_id' => $this->subscription->id,
            'days_before' => $this->daysBefore,
            'notice_type' => $this->noticeType,
        ];
    }

    private function mailData(object $shop, object $plan): array
    {
        return [
            'shopName' => $shop->business_name,
            'planName' => $plan->name,
            'expiryDate' => $this->subscription->ends_at?->format('Y-m-d'),
            'daysLeft' => max(0, $this->subscription->daysRemaining()),
            'amountDue' => number_format((float) $plan->price),
            'renewUrl' => route('shop.subscriptions.index'),
            'noticeType' => $this->noticeType,
        ];
    }

    private function message(string $shopName, string $planName, ?string $expiryDate): string
    {
        $key = match ($this->noticeType) {
            'grace' => 'subscription.grace_message',
            'expired' => 'subscription.expired_message',
            default => 'subscription.ending_message',
        };

        return __($key, [
            'shop' => $shopName,
            'plan' => $planName,
            'date' => $expiryDate ?? '',
            'days' => max(0, $this->daysBefore),
        ]);
    }
}
