<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaleDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $saleId,
        public float $totalAmount,
        public string $paymentMethod,
        public string $deletedByName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sale Deleted — Stock Restored')
            ->line("Sale #{$this->saleId} ({$this->totalAmount} RWF, {$this->paymentMethod}) was deleted by {$this->deletedByName}.")
            ->line('Stock quantities have been restored accordingly.')
            ->action('View Sales', route('sales.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'sale_id'     => $this->saleId,
            'total'       => $this->totalAmount,
            'deleted_by'  => $this->deletedByName,
            'message'     => "Sale #{$this->saleId} was deleted (stock restored)",
        ];
    }
}