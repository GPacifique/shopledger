<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $purchaseId,
        public float $totalAmount,
        public string $deletedByName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Purchase Deleted — Stock Reversed')
            ->line("Purchase #{$this->purchaseId} ({$this->totalAmount} RWF) was deleted by {$this->deletedByName}.")
            ->line('Stock quantities have been reversed accordingly.')
            ->action('View Purchases', route('purchases.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'purchase_id' => $this->purchaseId,
            'total'       => $this->totalAmount,
            'deleted_by'  => $this->deletedByName,
            'message'     => "Purchase #{$this->purchaseId} was deleted (stock reversed)",
        ];
    }
}