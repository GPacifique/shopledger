<?php

namespace App\Console\Commands;

use App\Jobs\SendShopReportJob;
use App\Models\ReportDelivery;
use App\Models\ReportSchedule;
use App\Services\ReportDeliveryService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use InvalidArgumentException;

class SendScheduledShopReports extends Command
{
    protected $signature = 'reports:send-scheduled
                            {--type= : Send only a specific report type}
                            {--date= : Reference date in Y-m-d format}
                            {--force : Ignore the configured send time and frequency}';

    protected $description = 'Dispatch scheduled MahWi shop reports';

public function handle(
    ReportDeliveryService $reportDeliveryService
): void {
    $delivery = $reportDeliveryService->prepare(
        $this->shop,
        $this->type,
        $this->referenceDate->copy()
    );

    $emails = $delivery['recipient_emails'] ?? [];

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

    $periodStart = $delivery['period']['start']->toDateString();
    $periodEnd = $delivery['period']['end']->toDateString();

    $reportDelivery = ReportDelivery::firstOrCreate(
        [
            'shop_id' => $this->shop->id,
            'report_type' => $this->type,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ],
        [
            'recipient_emails' => implode(',', $emails),
            'status' => 'pending',
        ]
    );

    /*
     * The report for this exact shop/type/period has already
     * been successfully sent. Do not send it again.
     */
    if ($reportDelivery->status === 'sent') {
        Log::info(
            'MahWi report already sent. Skipping duplicate.',
            [
                'delivery_id' => $reportDelivery->id,
                'shop_id' => $this->shop->id,
                'report_type' => $this->type,
                'period' => $delivery['period']['label'] ?? null,
            ]
        );

        return;
    }

    try {
        /*
         * Keep the recipient list synchronized in case the
         * Shop-Admin users changed before a retry.
         */
        $reportDelivery->update([
            'recipient_emails' => implode(',', $emails),
            'status' => 'pending',
            'error_message' => null,
        ]);

        Mail::to($emails)->send(
            new ShopReportMail($delivery)
        );

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

        throw $exception;
    }
}

    protected function isDue(
        ReportSchedule $schedule,
        Carbon $referenceDate
    ): bool {
        /*
         * Match configured time.
         *
         * Scheduler will eventually run this command every minute,
         * so comparing HH:mm is sufficient.
         */
        if ($schedule->send_at) {
            $scheduledTime = Carbon::parse(
                $schedule->send_at
            )->format('H:i');

            if ($referenceDate->format('H:i') !== $scheduledTime) {
                return false;
            }
        }

        return match (strtolower(trim($schedule->report_type))) {
            'daily' => true,

            /*
             * ISO day:
             * Monday = 1
             * Sunday = 7
             *
             * If day_of_week is NULL, default to Monday.
             */
            'weekly' => $referenceDate->dayOfWeekIso === (
                $schedule->day_of_week ?: 1
            ),

            /*
             * If day_of_month is NULL, default to
             * the first day of the month.
             */
            'monthly' => $referenceDate->day === (
                $schedule->day_of_month ?: 1
            ),

            /*
             * Yearly reports run on January 1 by default.
             *
             * If day_of_month is configured, it is used
             * together with January.
             */
            'yearly' => $referenceDate->month === 1
                && $referenceDate->day === (
                    $schedule->day_of_month ?: 1
                ),

            default => false,
        };
    }
}

