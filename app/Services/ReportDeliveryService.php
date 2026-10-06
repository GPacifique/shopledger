<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ReportDeliveryService
{
    public function __construct(
        protected ReportService $reportService
    ) {
    }

    /**
     * Prepare a report for delivery.
     *
     * Supported report types:
     * - daily
     * - weekly
     * - monthly
     * - yearly
     */
    public function prepare(
        Shop $shop,
        string $type,
        ?Carbon $referenceDate = null
    ): array {
        $referenceDate ??= now();

        [$startDate, $endDate] = $this->resolvePeriod(
            $type,
            $referenceDate
        );

        /*
         * Use the existing ReportService.
         *
         * This is important because the same calculations used by
         * the MahWi web reports will also be used by email reports.
         */
        $report = $this->reportService->generate(
            $shop->id,
            $startDate,
            $endDate
        );

        $recipients = $this->getRecipients($shop);

        return [
            'shop' => $shop,

            'type' => strtolower($type),

            'title' => $this->reportTitle($type),

            'period' => [
                'start' => $startDate,
                'end' => $endDate,
                'label' => $this->formatPeriod(
                    $startDate,
                    $endDate
                ),
            ],

            'report' => $report,

            'recipients' => $recipients,

            'recipient_emails' => $recipients
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ];
    }


    /**
     * Resolve the correct completed reporting period.
     *
     * Daily:
     *      Yesterday
     *
     * Weekly:
     *      Previous completed week
     *
     * Monthly:
     *      Previous completed month
     *
     * Yearly:
     *      Previous completed year
     */
    public function resolvePeriod(
        string $type,
        ?Carbon $referenceDate = null
    ): array {
        $referenceDate ??= now();

        $type = strtolower(trim($type));

        return match ($type) {

            /*
             * Previous day
             */
            'daily' => [
                $referenceDate
                    ->copy()
                    ->subDay()
                    ->startOfDay(),

                $referenceDate
                    ->copy()
                    ->subDay()
                    ->endOfDay(),
            ],


            /*
             * Previous completed week.
             *
             * Carbon's startOfWeek/endOfWeek use the
             * application's configured week start.
             */
            'weekly' => [
                $referenceDate
                    ->copy()
                    ->subWeek()
                    ->startOfWeek(),

                $referenceDate
                    ->copy()
                    ->subWeek()
                    ->endOfWeek(),
            ],


            /*
             * Previous completed month
             */
            'monthly' => [
                $referenceDate
                    ->copy()
                    ->subMonth()
                    ->startOfMonth(),

                $referenceDate
                    ->copy()
                    ->subMonth()
                    ->endOfMonth(),
            ],


            /*
             * Previous completed year
             */
            'yearly' => [
                $referenceDate
                    ->copy()
                    ->subYear()
                    ->startOfYear(),

                $referenceDate
                    ->copy()
                    ->subYear()
                    ->endOfYear(),
            ],


            default => throw new InvalidArgumentException(
                "Unsupported report type: {$type}"
            ),
        };
    }


    /**
     * Get users who should receive reports for this shop.
     *
     * NOTE:
     * The role names here should match the actual values in
     * your MahWi users table.
     */
    public function getRecipients(Shop $shop): Collection
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where(function ($query) {
                $query
                    ->where('role', 'Shop-Admin')
                    ->orWhere('role', 'shop-admin')
                    ->orWhere('role', 'shop_admin');
            })
            ->where(function ($query) use ($shop) {

                /*
                 * Normal shop-specific users.
                 */
                $query->where('shop_id', $shop->id);

                /*
                 * If your Shop-Admin relationship is stored
                 * differently, this section can be changed later.
                 */
            })
            ->get();
    }


    /**
     * Get only valid unique email addresses.
     */
    public function recipientEmails(Shop $shop): array
    {
        return $this->getRecipients($shop)
            ->pluck('email')
            ->filter(fn ($email) => filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            ))
            ->unique()
            ->values()
            ->all();
    }


    /**
     * Check whether the shop has report recipients.
     */
    public function hasRecipients(Shop $shop): bool
    {
        return count(
            $this->recipientEmails($shop)
        ) > 0;
    }


    /**
     * Human-readable report title.
     */
    public function reportTitle(string $type): string
    {
        return match (strtolower(trim($type))) {

            'daily' => 'Daily Report',

            'weekly' => 'Weekly Report',

            'monthly' => 'Monthly Report',

            'yearly' => 'Yearly Report',

            default => throw new InvalidArgumentException(
                "Unsupported report type: {$type}"
            ),
        };
    }


    /**
     * Format a reporting period for email/PDF display.
     */
    public function formatPeriod(
        Carbon $startDate,
        Carbon $endDate
    ): string {

        /*
         * Same day
         *
         * Example:
         * 05 Oct 2026
         */
        if ($startDate->isSameDay($endDate)) {
            return $startDate->format('d M Y');
        }


        /*
         * Same month
         *
         * Example:
         * 01–07 Oct 2026
         */
        if ($startDate->isSameMonth($endDate)) {
            return $startDate->format('d')
                . '–'
                . $endDate->format('d M Y');
        }


        /*
         * Same year
         *
         * Example:
         * 28 Sep–05 Oct 2026
         */
        if ($startDate->isSameYear($endDate)) {
            return $startDate->format('d M')
                . '–'
                . $endDate->format('d M Y');
        }


        /*
         * Different years
         */
        return $startDate->format('d M Y')
            . '–'
            . $endDate->format('d M Y');
    }
}
