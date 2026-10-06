<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Prepared report delivery data.
     */
    public array $delivery;

    /**
     * Create a new message instance.
     */
    public function __construct(array $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $shop = $this->delivery['shop'];

        $title = $this->delivery['title'];

        $period = $this->delivery['period'];

        return $this
            ->subject(
                'MahWi ' .
                $title .
                ' - ' .
                $shop->business_name .
                ' - ' .
                $period['label']
            )
            ->view('emails.reports.shop-report');
    }
}
