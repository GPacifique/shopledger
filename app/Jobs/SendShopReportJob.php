<?php

namespace App\Jobs;

use App\Mail\ShopReportMail;
use App\Models\ReportDelivery;
use App\Models\Shop;
use App\Services\ReportDeliveryService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendShopReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of attempts.
     */
    public int $tries = 3;

    /**
     * Retry delays in seconds.
     */
    public array $backoff = [
        60,
        300,
        900,
    ];

    public function __construct(
        public Shop $shop,
        public string $type,
        public Carbon $referenceDate
    ) {
        $this->type = strtolower(trim($type));
    }

    /**
     * Execute the job.
     */
    public function handle(
        ReportDeliveryService $reportDeliveryService
    ): void {
        /*
         * Prepare the report using the fixed reference date.
         * This guarantees that retries use the same reporting period.
         */
        $delivery = $reportDeliveryService->prepare(
            $this->shop,
            $this->type,
            $this->referenceDate->copy()
        );

        $emails = $delivery['recipient_emails'] ?? [];

        /*
         * If there are no valid recipients, don't attempt to send.
         */
        if (empty($emails)) {
            Log::warning(
                'MahWi report email skipped: no recipients found.',
                [
                    'shop_id' => $this->shop->id,
                    'shop_name' => $this->shop->business_name
                        ?? $this->shop->name
                        ?? null,
                    'report_type' => $this->type,
                    'period' => $delivery['period']['label'] ?? null,
                ]
            );

            return;
        }

        /*
         * Create the delivery history record.
         */
        $reportDelivery = ReportDelivery::create([
            'shop_id' => $this->shop->id,
            'report_type' => $this->type,
            'period_start' => $delivery['period']['start']->toDateString(),
            'period_end' => $delivery['period']['end']->toDateString(),
            'recipient_emails' => implode(',', $emails),
            'status' => 'pending',
        ]);

        try {
            /*
             * Send the email.
             */
            Mail::to($emails)->send(
                new ShopReportMail($delivery)
            );

            /*
             * Mark the delivery as successfully sent.
             */
            $reportDelivery->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            Log::info(
                'MahWi report email sent successfully.',
                [
                    'delivery_id' => $reportDelivery->id,
                    'shop_id' => $this->shop->id,
                    'shop_name' => $this->shop->business_name
                        ?? $this->shop->name
                        ?? null,
                    'report_type' => $this->type,
                    'period' => $delivery['period']['label'] ?? null,
                    'recipients' => $emails,
                ]
            );
        } catch (Throwable $exception) {
            /*
             * Record the failure before allowing the queue
             * worker to retry the job.
             */
            $reportDelivery->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            Log::error(
                'MahWi report email failed.',
                [
                    'delivery_id' => $reportDelivery->id,
                    'shop_id' => $this->shop->id,
                    'report_type' => $this->type,
                    'period' => $delivery['period']['label'] ?? null,
                    'error' => $exception->getMessage(),
                ]
            );

            /*
             * Re-throw the exception so Laravel's queue system
             * knows the job failed and can retry it.
             */
            throw $exception;
        }
    }

    /**
     * Handle a permanently failed queued job.
     */
    public function failed(Throwable $exception): void
    {
        Log::error(
            'MahWi report email job failed permanently.',
            [
                'shop_id' => $this->shop->id,
                'shop_name' => $this->shop->business_name
                    ?? $this->shop->name
                    ?? null,
                'report_type' => $this->type,
                'reference_date' => $this->referenceDate->toDateString(),
                'error' => $exception->getMessage(),
            ]
        );
    }
}
