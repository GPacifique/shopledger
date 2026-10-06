<?php

namespace App\Console\Commands;

use App\Jobs\SendShopReportJob;
use App\Models\ReportDelivery;
use App\Models\ReportSchedule;
use App\Models\Shop;
use App\Services\ReportDeliveryService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledShopReports extends Command
{
    protected $signature = 'reports:send-scheduled
                            {--type= : Report type: daily, weekly, monthly, yearly}
                            {--date= : Reference date in Y-m-d format}
                            {--force : Ignore schedule time/frequency checks}';

    protected $description = 'Dispatch scheduled MahWi shop reports';

    public function handle(
        ReportDeliveryService $reportDeliveryService
    ): int {
        $referenceDate = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now();

        $requestedType = $this->option('type');

        $schedules = ReportSchedule::query()
            ->with('shop')
            ->where('is_enabled', true)
            ->when(
                $requestedType,
                fn ($query) =>
                    $query->where(
                        'report_type',
                        strtolower(trim($requestedType))
                    )
            )
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('No enabled report schedules found.');

            return self::SUCCESS;
        }

        $dispatched = 0;
        $skipped = 0;

        foreach ($schedules as $schedule) {
            $shop = $schedule->shop;

            if (!$shop) {
                $this->warn(
                    "Skipped schedule {$schedule->id}: shop not found."
                );

                $skipped++;
                continue;
            }

            $type = strtolower(trim($schedule->report_type));

            /*
             * --force is intended for manual testing.
             * It bypasses send time and frequency checks.
             */
            if (!$this->option('force')) {
                if (!$this->isDue($schedule, $referenceDate)) {
                    $skipped++;
                    continue;
                }
            }

            /*
             * Resolve the exact period that this report will cover.
             */
            [$periodStart, $periodEnd] =
                $reportDeliveryService->resolvePeriod(
                    $type,
                    $referenceDate->copy()
                );

            /*
             * Prevent dispatching a report that has already
             * been successfully delivered.
             */
            $alreadySent = ReportDelivery::query()
                ->where('shop_id', $shop->id)
                ->where('report_type', $type)
                ->whereDate('period_start', $periodStart->toDateString())
                ->whereDate('period_end', $periodEnd->toDateString())
                ->where('status', 'sent')
                ->exists();

            if ($alreadySent) {
                $this->line(
                    "Skipped: {$shop->business_name} / {$type} / " .
                    "{$periodStart->toDateString()} - " .
                    "{$periodEnd->toDateString()} " .
                    '(already sent)'
                );

                $skipped++;
                continue;
            }

            /*
             * Dispatch the actual queued job.
             */
            SendShopReportJob::dispatch(
                $shop,
                $type,
                $referenceDate->copy()
            );

            $this->info(
                "Dispatched: {$shop->business_name} / {$type} / " .
                "{$periodStart->toDateString()} - " .
                "{$periodEnd->toDateString()}"
            );

            $dispatched++;
        }

        $this->newLine();

        $this->info("Dispatched: {$dispatched}");
        $this->info("Skipped: {$skipped}");

        return self::SUCCESS;
    }

    /**
     * Determine whether a schedule is due now.
     */
    protected function isDue(
        ReportSchedule $schedule,
        Carbon $referenceDate
    ): bool {
        $type = strtolower(trim($schedule->report_type));

        /*
         * Compare using the application/server time.
         *
         * cPanel is currently running the scheduler every 5 minutes,
         * so schedules should preferably use times such as 07:00,
         * 07:05, 07:10, etc.
         */
        $scheduledTime = Carbon::parse(
            $referenceDate->toDateString() . ' ' . $schedule->send_at
        );

        /*
         * Allow a small window around the scheduled minute.
         * This is useful because cPanel invokes schedule:run
         * every 5 minutes.
         */
        if (
            $referenceDate->hour !== $scheduledTime->hour ||
            $referenceDate->minute !== $scheduledTime->minute
        ) {
            return false;
        }

        return match ($type) {
            'daily' => true,

            'weekly' => (
                ($schedule->day_of_week ?? 1)
                === $referenceDate->dayOfWeekIso
            ),

            'monthly' => (
                ($schedule->day_of_month ?? 1)
                === $referenceDate->day
            ),

            'yearly' => (
                $referenceDate->month === 1 &&
                ($schedule->day_of_month ?? 1)
                === $referenceDate->day
            ),

            default => false,
        };
    }
}