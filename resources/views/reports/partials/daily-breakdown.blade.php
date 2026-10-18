<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    {{-- Header --}}
    <div class="flex flex-col gap-3 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                <i class="fas fa-chart-bar text-lg"></i>
            </div>

            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Period Performance
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Financial performance breakdown by date
                </p>
            </div>
        </div>

        <span class="inline-flex w-fit items-center rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">
            <i class="fas fa-calendar-alt mr-2"></i>
            {{ count($data['daily_breakdown'] ?? []) }} records
        </span>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Date
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-green-700">
                        Sales
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-blue-700">
                        Purchases
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-red-700">
                        Expenses
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-emerald-700">
                        Other Income
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-indigo-700">
                        Gross Profit
                    </th>

                    <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-700">
                        Net Profit
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">

                @forelse($data['daily_breakdown'] ?? [] as $day)

                    <tr class="transition-colors duration-150 hover:bg-slate-50">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                    <i class="far fa-calendar"></i>
                                </div>

                                <span class="font-semibold text-slate-700">
                                    {{ $day['date']->format('D, d M Y') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-5 py-4 text-right font-medium tabular-nums text-slate-700">
                            {{ number_format($day['sales'] ?? 0) }}
                        </td>

                        <td class="px-5 py-4 text-right font-medium tabular-nums text-slate-700">
                            {{ number_format($day['purchases'] ?? 0) }}
                        </td>

                        <td class="px-5 py-4 text-right font-medium tabular-nums text-slate-700">
                            {{ number_format($day['expenses'] ?? 0) }}
                        </td>

                        <td class="px-5 py-4 text-right font-medium tabular-nums text-slate-700">
                            {{ number_format($day['other_income'] ?? 0) }}
                        </td>

                        <td class="px-5 py-4 text-right font-semibold tabular-nums {{ ($day['gross_profit'] ?? 0) < 0 ? 'text-red-600' : 'text-indigo-600' }}">
                            {{ number_format($day['gross_profit'] ?? 0) }}
                        </td>

                        <td class="px-5 py-4 text-right">
                            <span class="inline-flex items-center rounded-lg px-3 py-1.5 font-bold tabular-nums {{ ($day['net_profit'] ?? 0) < 0 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                {{ number_format($day['net_profit'] ?? 0) }}
                            </span>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <i class="fas fa-chart-line text-2xl"></i>
                            </div>

                            <p class="mt-4 font-semibold text-slate-700">
                                No performance data available
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Data will appear here when records are available for the selected period.
                            </p>
                        </td>
                    </tr>

                @endforelse

            </tbody>

            {{-- Totals --}}
            <tfoot>
                <tr class="border-t-2 border-slate-200 bg-slate-100/80">

                    <td class="px-5 py-5">
                        <span class="font-extrabold uppercase tracking-wide text-slate-800">
                            Total
                        </span>
                    </td>

                    <td class="px-5 py-5 text-right font-extrabold tabular-nums text-green-700">
                        {{ number_format($data['summary']['sales'] ?? 0) }}
                    </td>

                    <td class="px-5 py-5 text-right font-extrabold tabular-nums text-blue-700">
                        {{ number_format($data['summary']['purchases'] ?? 0) }}
                    </td>

                    <td class="px-5 py-5 text-right font-extrabold tabular-nums text-red-700">
                        {{ number_format($data['summary']['expenses'] ?? 0) }}
                    </td>

                    <td class="px-5 py-5 text-right font-extrabold tabular-nums text-emerald-700">
                        {{ number_format($data['summary']['other_income'] ?? 0) }}
                    </td>

                    <td class="px-5 py-5 text-right font-extrabold tabular-nums text-indigo-700">
                        {{ number_format($data['summary']['gross_profit'] ?? 0) }}
                    </td>

                    <td class="px-5 py-5 text-right">
                        <span class="inline-flex rounded-lg bg-white px-3 py-2 font-extrabold tabular-nums shadow-sm {{ ($data['summary']['net_profit'] ?? 0) < 0 ? 'text-red-700' : 'text-green-700' }}">
                            {{ number_format($data['summary']['net_profit'] ?? 0) }}
                        </span>
                    </td>

                </tr>
            </tfoot>

        </table>
    </div>

    {{-- Currency note --}}
    <div class="flex items-center justify-between border-t border-slate-100 bg-white px-6 py-3">
        <p class="text-xs text-slate-500">
            Financial figures for the selected period
        </p>

        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">
            Currency: RWF
        </span>
    </div>

</div>
