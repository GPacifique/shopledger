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
    ): int {
        $referenceDate = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now();

        $requestedType = $this->option('type');
        $force = (bool) $this->option('force');

        $query = ReportSchedule::query()
            ->with('shop')
            ->where('is_enabled', true);

        if ($requestedType) {
            $query->where(
                'report_type',
                strtolower(trim($requestedType))
            );
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            $this->info('No enabled report schedules found.');

            return self::SUCCESS;
        }

        $dispatched = 0;
        $skipped = 0;

        foreach ($schedules as $schedule) {
            $type = strtolower(trim($schedule->report_type));

            if (!$schedule->shop) {
                $this->warn(
                    "Skipping schedule #{$schedule->id}: shop not found."
                );

                $skipped++;
                continue;
            }

            /*
             * When running normally, only process schedules
             * that are actually due.
             *
             * --force is useful for testing.
             */
            if (!$force && !$this->isDue($schedule, $referenceDate)) {
                $this->line(
                    "Not due: "
                    . $schedule->shop->business_name
                    . " / "
                    . $type
                );

                $skipped++;
                continue;
            }

            try {
                [$startDate, $endDate] =
                    $reportDeliveryService->resolvePeriod(
                        $type,
                        $referenceDate->copy()
                    );
            } catch (InvalidArgumentException $exception) {
                $this->warn(
                    "Skipping schedule #{$schedule->id}: "
                    . $exception->getMessage()
                );

                $skipped++;
                continue;
            }

            /*
             * Prevent duplicate reports for the same shop,
             * report type and reporting period.
             */
            $alreadySent = ReportDelivery::query()
                ->where('shop_id', $schedule->shop_id)
                ->where('report_type', $type)
                ->whereDate(
                    'period_start',
                    $startDate->toDateString()
                )
                ->whereDate(
                    'period_end',
                    $endDate->toDateString()
                )
                ->where('status', 'sent')
                ->exists();

            if ($alreadySent) {
                $this->line(
                    "Already sent: "
                    . $schedule->shop->business_name
                    . " / "
                    . $type
                    . " / "
                    . $startDate->toDateString()
                    . " - "
                    . $endDate->toDateString()
                );

                $skipped++;
                continue;
            }

            SendShopReportJob::dispatch(
                $schedule->shop,
                $type,
                $referenceDate->copy()
            );

            $this->info(
                "Dispatched: "
                . $schedule->shop->business_name
                . " / "
                . $type
                . " / "
                . $startDate->toDateString()
                . " - "
                . $endDate->toDateString()
            );

            $dispatched++;
        }

        $this->newLine();

        $this->info("Dispatched: {$dispatched}");
        $this->info("Skipped: {$skipped}");

        return self::SUCCESS;
    }

    /**
     * Determine whether a schedule is due.
     */
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

