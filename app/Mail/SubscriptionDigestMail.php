<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $subscriptions)
    {
    }

    public function build(): static
    {
        return $this
            ->subject(__('subscription.digest_subject', ['date' => now()->toDateString()]))
            ->view('emails.subscriptions.digest');
    }
}
