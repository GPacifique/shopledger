<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    {{-- Header --}}
    <div class="p-5 border-b border-slate-200">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Sold Products
                </h2>

                <p class="text-sm text-slate-500">
                    Products sold during the selected period
                </p>
            </div>

        </div>

    </div>


    {{-- Table --}}
    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-slate-50 border-b border-slate-200">

                <tr>

                    <th class="px-4 py-3 text-left whitespace-nowrap">
                        Date
                    </th>

                    <th class="px-4 py-3 text-left whitespace-nowrap">
                        Sale
                    </th>

                    <th class="px-4 py-3 text-left whitespace-nowrap">
                        Product
                    </th>

                    <th class="px-4 py-3 text-right whitespace-nowrap">
                        Qty
                    </th>

                    <th class="px-4 py-3 text-right whitespace-nowrap">
                        Buying Price
                    </th>

                    <th class="px-4 py-3 text-right whitespace-nowrap">
                        Selling Price
                    </th>

                    <th class="px-4 py-3 text-right whitespace-nowrap">
                        Revenue
                    </th>

                    <th class="px-4 py-3 text-right whitespace-nowrap">
                        Profit
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-100">

                @forelse($data['items'] as $row)

                    @php
                        /*
                         * ReportService sales rows are expected to contain:
                         *
                         * [
                         *     'sale'    => Sale model,
                         *     'item'    => SaleItem model,
                         *     'product' => Product model,
                         * ]
                         */

                        $item = $row['item'] ?? null;

                        $product = $row['product'] ?? null;

                        $sale = $row['sale'] ?? null;


                        /*
                         * Quantity sold
                         */
                        $quantity = (float) ($item?->quantity ?? 0);


                        /*
                         * Actual buying/cost price at the time of sale.
                         *
                         * This should come from sale_items.cost_price_at_sale
                         * when available.
                         */
                        $buyingPrice = (float) (
                            $item?->cost_price_at_sale
                            ?? $product?->buying_price
                            ?? 0
                        );


                        /*
                         * Actual selling price.
                         *
                         * unit_price should represent the price charged
                         * to the customer for one unit.
                         */
                        $sellingPrice = (float) (
                            $item?->unit_price
                            ?? 0
                        );


                        /*
                         * Revenue
                         */
                        $revenue = $quantity * $sellingPrice;


                        /*
                         * Cost of goods sold
                         */
                        $cost = $quantity * $buyingPrice;


                        /*
                         * Gross profit
                         */
                        $profit = $revenue - $cost;
                    @endphp


                    <tr class="hover:bg-slate-50">

                        {{-- Sale Date --}}
                        <td class="px-4 py-3 whitespace-nowrap text-slate-600">
                            {{ $sale?->sale_date
                                ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y')
                                : ($sale?->created_at?->format('d/m/Y') ?? '—') }}
                        </td>


                        {{-- Sale --}}
                        <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-700">
                            #{{ $sale?->id ?? '—' }}
                        </td>


                        {{-- Product --}}
                        <td class="px-4 py-3 font-medium text-slate-800">
                            {{ $product?->name ?? 'Unknown Product' }}
                        </td>


                        {{-- Quantity --}}
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            {{ number_format($quantity, 2) }}
                        </td>


                        {{-- Buying Price --}}
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            {{ number_format($buyingPrice) }}
                        </td>


                        {{-- Selling Price --}}
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            {{ number_format($sellingPrice) }}
                        </td>


                        {{-- Revenue --}}
                        <td class="px-4 py-3 text-right font-medium whitespace-nowrap">
                            {{ number_format($revenue) }}
                        </td>


                        {{-- Profit --}}
                        <td class="px-4 py-3 text-right font-medium whitespace-nowrap">
                            {{ number_format($profit) }}
                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="px-4 py-12 text-center text-slate-500"
                        >
                            No sales found for this period.
                        </td>

                    </tr>

                @endforelse

            </tbody>


            {{-- Totals --}}
            <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold">

                <tr>

                    <td
                        colspan="3"
                        class="px-4 py-4 text-right text-slate-700"
                    >
                        TOTAL
                    </td>


                    {{-- Total Quantity --}}
                    <td class="px-4 py-4 text-right whitespace-nowrap">
                        {{ number_format((float) ($data['quantity'] ?? 0), 2) }}
                    </td>


                    {{-- Buying Price --}}
                    <td class="px-4 py-4 text-right">
                        —
                    </td>


                    {{-- Selling Price --}}
                    <td class="px-4 py-4 text-right">
                        —
                    </td>


                    {{-- Total Revenue --}}
                    <td class="px-4 py-4 text-right whitespace-nowrap">
                        {{ number_format((float) ($data['total'] ?? 0)) }}
                    </td>


                    {{-- Total Profit --}}
                    <td class="px-4 py-4 text-right whitespace-nowrap">
                        {{ number_format(
                            (float) ($data['total'] ?? 0)
                            - (float) ($data['cost'] ?? 0)
                        ) }}
                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>