<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpenseDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $expenseId,
        public float $amount,
        public string $deletedByName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Expense Deleted')
            ->line("Expense #{$this->expenseId} ({$this->amount} RWF) was deleted by {$this->deletedByName}.")
            ->action('View Expenses', route('expenses.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'expense_id' => $this->expenseId,
            'amount' => $this->amount,
            'deleted_by' => $this->deletedByName,
            'message' => "Expense #{$this->expenseId} was deleted.",
        ];
    }
}
