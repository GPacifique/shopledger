<div class="bg-white border rounded-xl overflow-hidden">

    <div class="p-5 border-b">

        <h2 class="text-lg font-semibold">
            Period Performance
        </h2>

    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-slate-50">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Date
                    </th>

                    <th class="px-4 py-3 text-right">
                        Sales
                    </th>

                    <th class="px-4 py-3 text-right">
                        Purchases
                    </th>

                    <th class="px-4 py-3 text-right">
                        Expenses
                    </th>

                    <th class="px-4 py-3 text-right">
                        Other Income
                    </th>

                    <th class="px-4 py-3 text-right">
                        Gross Profit
                    </th>

                    <th class="px-4 py-3 text-right">
                        Net Profit
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y">

                @foreach($data['daily_breakdown'] as $day)

                    <tr>

                        <td class="px-4 py-3 font-medium">
                            {{ $day['date']->format('D, d M Y') }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($day['sales']) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($day['purchases']) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($day['expenses']) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($day['other_income']) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($day['gross_profit']) }}
                        </td>

                        <td class="px-4 py-3 text-right font-semibold">
                            {{ number_format($day['net_profit']) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>


            <tfoot class="bg-slate-50 border-t font-semibold">

                <tr>

                    <td class="px-4 py-4">
                        TOTAL
                    </td>

                    <td class="px-4 py-4 text-right">
                        {{ number_format($data['summary']['sales']) }}
                    </td>

                    <td class="px-4 py-4 text-right">
                        {{ number_format($data['summary']['purchases']) }}
                    </td>

                    <td class="px-4 py-4 text-right">
                        {{ number_format($data['summary']['expenses']) }}
                    </td>

                    <td class="px-4 py-4 text-right">
                        {{ number_format($data['summary']['other_income']) }}
                    </td>

                    <td class="px-4 py-4 text-right">
                        {{ number_format($data['summary']['gross_profit']) }}
                    </td>

                    <td class="px-4 py-4 text-right">
                        {{ number_format($data['summary']['net_profit']) }}
                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>