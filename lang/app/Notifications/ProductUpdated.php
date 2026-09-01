<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $productId,
        public string $productName,
        public string $updatedByName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Product Updated')
            ->line("Product \"{$this->productName}\" (ID: {$this->productId}) was updated by {$this->updatedByName}.")
            ->action('View Products', route('products.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'updated_by' => $this->updatedByName,
            'message' => "Product \"{$this->productName}\" was updated.",
        ];
    }
}
