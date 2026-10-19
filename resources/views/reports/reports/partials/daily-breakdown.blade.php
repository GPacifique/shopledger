<div class="ui-card overflow-hidden rounded-2xl shadow-sm">

    {{-- Header --}}
    <div class="ui-grad-head ui-line flex flex-col gap-3 border-b px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="ui-chip tone-indigo flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <i class="fas fa-chart-bar text-lg" aria-hidden="true"></i>
            </div>

            <div>
                <h2 class="ui-text text-lg font-bold">Period Performance</h2>
                <p class="ui-muted mt-1 text-sm">Financial performance breakdown by date</p>
            </div>
        </div>

        <span class="ui-badge tone-indigo inline-flex w-fit items-center rounded-full px-3 py-1.5 text-xs font-semibold">
            <i class="fas fa-calendar-alt mr-2" aria-hidden="true"></i>
            {{ count($data['daily_breakdown'] ?? []) }} records
        </span>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="ui-table min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr>
                    <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider ui-muted">
                        Date
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider tone-green tx">
                        Sales
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider tone-blue tx">
                        Purchases
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider tone-red tx">
                        Expenses
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider tone-emerald tx">
                        Other Income
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider tone-indigo tx">
                        Gross Profit
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider ui-soft">
                        Net Profit
                    </th>

                </tr>
            </thead>

            <tbody>

                @forelse($data['daily_breakdown'] ?? [] as $day)

                    <tr class="transition-colors duration-150">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="ui-icon-circle flex h-9 w-9 shrink-0 items-center justify-center rounded-lg">
                                    <i class="far fa-calendar" aria-hidden="true"></i>
                                </div>

                                <span class="ui-soft font-semibold">
                                    {{ $day['date']->format('D, d M Y') }}
                                </span>
                            </div>
                        </td>

                        <td class="ui-soft px-5 py-4 text-right font-medium tabular-nums">
                            {{ number_format($day['sales'] ?? 0) }}
                        </td>

                        <td class="ui-soft px-5 py-4 text-right font-medium tabular-nums">
                            {{ number_format($day['purchases'] ?? 0) }}
                        </td>

                        <td class="ui-soft px-5 py-4 text-right font-medium tabular-nums">
                            {{ number_format($day['expenses'] ?? 0) }}
                        </td>

                        <td class="ui-soft px-5 py-4 text-right font-medium tabular-nums">
                            {{ number_format($day['other_income'] ?? 0) }}
                        </td>

                        <td class="px-5 py-4 text-right font-semibold tabular-nums {{ ($day['gross_profit'] ?? 0) < 0 ? 'tone-red' : 'tone-indigo' }} st">
                            {{ number_format($day['gross_profit'] ?? 0) }}
                        </td>

                        <td class="px-5 py-4 text-right">
                            <span class="ui-badge inline-flex items-center rounded-lg px-3 py-1.5 font-bold tabular-nums {{ ($day['net_profit'] ?? 0) < 0 ? 'tone-red' : 'tone-green' }}">
                                {{ number_format($day['net_profit'] ?? 0) }}
                            </span>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <div class="ui-icon-circle mx-auto flex h-14 w-14 items-center justify-center rounded-2xl">
                                <i class="fas fa-chart-line text-2xl" aria-hidden="true"></i>
                            </div>

                            <p class="ui-soft mt-4 font-semibold">No performance data available</p>

                            <p class="ui-muted mt-1 text-sm">
                                Data will appear here when records are available for the selected period.
                            </p>
                        </td>
                    </tr>

                @endforelse

            </tbody>

            {{-- Totals --}}
            <tfoot>
                <tr>

                    <td class="px-5 py-5">
                        <span class="ui-text font-extrabold uppercase tracking-wide">Total</span>
                    </td>

                    <td class="tone-green st px-5 py-5 text-right font-extrabold tabular-nums">
                        {{ number_format($data['summary']['sales'] ?? 0) }}
                    </td>

                    <td class="tone-blue st px-5 py-5 text-right font-extrabold tabular-nums">
                        {{ number_format($data['summary']['purchases'] ?? 0) }}
                    </td>

                    <td class="tone-red st px-5 py-5 text-right font-extrabold tabular-nums">
                        {{ number_format($data['summary']['expenses'] ?? 0) }}
                    </td>

                    <td class="tone-emerald st px-5 py-5 text-right font-extrabold tabular-nums">
                        {{ number_format($data['summary']['other_income'] ?? 0) }}
                    </td>

                    <td class="tone-indigo st px-5 py-5 text-right font-extrabold tabular-nums">
                        {{ number_format($data['summary']['gross_profit'] ?? 0) }}
                    </td>

                    <td class="px-5 py-5 text-right">
                        <span class="ui-card inline-flex rounded-lg px-3 py-2 font-extrabold tabular-nums shadow-sm {{ ($data['summary']['net_profit'] ?? 0) < 0 ? 'tone-red' : 'tone-green' }} st">
                            {{ number_format($data['summary']['net_profit'] ?? 0) }}
                        </span>
                    </td>

                </tr>
            </tfoot>

        </table>
    </div>

    {{-- Currency note --}}
    <div class="ui-surface ui-line flex items-center justify-between gap-3 border-t px-6 py-3">
        <p class="ui-muted text-xs">Financial figures for the selected period</p>

        <span class="ui-pill-gray rounded-md px-2 py-1 text-xs font-bold">Currency: RWF</span>
    </div>

</div>
