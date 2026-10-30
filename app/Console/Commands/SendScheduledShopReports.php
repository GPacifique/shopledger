<?php

namespace App\Console\Commands;

use App\Jobs\SendShopReportJob;
use App\Models\ReportDelivery;
use App\Models\ReportSchedule;
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

    /**
     * How many minutes after the scheduled time a report is still
     * considered "due". Must be >= the scheduler interval (cPanel
     * runs schedule:run every 5 minutes).
     */
    protected const DUE_WINDOW_MINUTES = 5;

    /**
     * Delivery statuses that mean "do not dispatch this period again".
     * Adjust to match the statuses your ReportDelivery model really uses.
     */
    protected const BLOCKING_STATUSES = ['sent', 'queued', 'pending'];

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
                try {
                    $due = $this->isDue($schedule, $referenceDate);
                } catch (\Throwable $e) {
                    // One bad schedule must not break all the others.
                    $this->error(
                        "Skipped schedule {$schedule->id}: " .
                        $e->getMessage()
                    );

                    report($e);

                    $skipped++;
                    continue;
                }

                if (!$due) {
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
             * Prevent dispatching a report that has already been
             * delivered, or is already queued/pending for this period.
             */
            $alreadyHandled = ReportDelivery::query()
                ->where('shop_id', $shop->id)
                ->where('report_type', $type)
                ->whereDate('period_start', $periodStart->toDateString())
                ->whereDate('period_end', $periodEnd->toDateString())
                ->whereIn('status', self::BLOCKING_STATUSES)
                ->exists();

            if ($alreadyHandled) {
                $this->line(
                    "Skipped: {$shop->business_name} / {$type} / " .
                    "{$periodStart->toDateString()} - " .
                    "{$periodEnd->toDateString()} " .
                    '(already sent or queued)'
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
         * send_at may be a Carbon instance (model cast), a plain time
         * string such as "07:00:00", or a full datetime string such as
         * "2026-10-30 07:00:00". Carbon::parse() handles all three, and
         * we only keep the hour and minute, so the date is never
         * concatenated twice.
         */
        if (empty($schedule->send_at)) {
            return false;
        }

        $sendAt = Carbon::parse($schedule->send_at);

        $scheduledTime = $referenceDate->copy()->setTime(
            $sendAt->hour,
            $sendAt->minute,
            0
        );

        /*
         * Due window: from the scheduled time up to
         * DUE_WINDOW_MINUTES after it. cPanel invokes schedule:run
         * every 5 minutes, so an exact-minute match could be missed.
         * Duplicates are prevented by the ReportDelivery check.
         */
        $secondsAfter =
            $referenceDate->getTimestamp() - $scheduledTime->getTimestamp();

        if (
            $secondsAfter < 0 ||
            $secondsAfter >= self::DUE_WINDOW_MINUTES * 60
        ) {
            return false;
        }

        return match ($type) {
            'daily' => true,

            'weekly' => (
                (int) ($schedule->day_of_week ?? 1)
                === $referenceDate->dayOfWeekIso
            ),

            'monthly' => (
                (int) ($schedule->day_of_month ?? 1)
                === $referenceDate->day
            ),

            'yearly' => (
                $referenceDate->month === 1 &&
                (int) ($schedule->day_of_month ?? 1)
                === $referenceDate->day
            ),

            default => false,
        };
    }
}