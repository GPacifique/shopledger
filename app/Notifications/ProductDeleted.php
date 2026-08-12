<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $productId,
        public string $productName,
        public string $deletedByName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Product Deleted')
            ->line("Product \"{$this->productName}\" (ID: {$this->productId}) was deleted by {$this->deletedByName}.")
            ->action('View Products', route('products.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'deleted_by' => $this->deletedByName,
            'message' => "Product \"{$this->productName}\" was deleted.",
        ];
    }
}
