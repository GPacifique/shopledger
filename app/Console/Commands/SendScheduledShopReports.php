<?php

namespace App\Console\Commands;

use App\Jobs\SendShopReportJob;
use App\Models\ReportSchedule;
use App\Services\ReportDeliveryService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledShopReports extends Command
{
    protected $signature = 'reports:send-scheduled
                            {--type= : Send only a specific report type}
                            {--date= : Reference date in Y-m-d format}';

    protected $description = 'Dispatch scheduled MahWi shop reports';

    public function handle(
        ReportDeliveryService $reportDeliveryService
    ): int {
        $referenceDate = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now();

        $requestedType = $this->option('type');

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

            /*
             * Validate the report type and calculate its period.
             */
            try {
                [$startDate, $endDate] =
                    $reportDeliveryService->resolvePeriod(
                        $type,
                        $referenceDate->copy()
                    );
            } catch (\InvalidArgumentException $exception) {
                $this->warn(
                    "Skipping schedule #{$schedule->id}: "
                    . $exception->getMessage()
                );

                $skipped++;

                continue;
            }

            /*
             * Make sure the shop still exists.
             */
            if (!$schedule->shop) {
                $this->warn(
                    "Skipping schedule #{$schedule->id}: shop not found."
                );

                $skipped++;

                continue;
            }

            /*
             * Prevent duplicate delivery for the same shop,
             * report type and reporting period.
             */
            $alreadySent = \App\Models\ReportDelivery::query()
                ->where('shop_id', $schedule->shop_id)
                ->where('report_type', $type)
                ->whereDate('period_start', $startDate->toDateString())
                ->whereDate('period_end', $endDate->toDateString())
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

            /*
             * Dispatch the report job.
             *
             * The reference date is passed explicitly so retries
             * always generate the same reporting period.
             */
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
}
