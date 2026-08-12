<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpenseUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $expenseId,
        public float $amount,
        public string $updatedByName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Expense Updated')
            ->line("Expense #{$this->expenseId} ({$this->amount} RWF) was updated by {$this->updatedByName}.")
            ->action('View Expenses', route('expenses.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'expense_id' => $this->expenseId,
            'amount' => $this->amount,
            'updated_by' => $this->updatedByName,
            'message' => "Expense #{$this->expenseId} was updated.",
        ];
    }
}
