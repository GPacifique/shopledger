<div class="bg-white border rounded-xl overflow-hidden">

    <div class="p-5 border-b">

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


    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-slate-50 border-b">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Date
                    </th>

                    <th class="px-4 py-3 text-left">
                        Order
                    </th>

                    <th class="px-4 py-3 text-left">
                        Product
                    </th>

                    <th class="px-4 py-3 text-right">
                        Qty
                    </th>

                    <th class="px-4 py-3 text-right">
                        Buying Price
                    </th>

                    <th class="px-4 py-3 text-right">
                        Selling Price
                    </th>

                    <th class="px-4 py-3 text-right">
                        Revenue
                    </th>

                    <th class="px-4 py-3 text-right">
                        Profit
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y">

                @forelse($data['items'] as $row)

                    @php

                        $item = $row['item'];

                        $product = $row['product'];

                        $quantity = (float) ($item->quantity ?? 0);

                        $sellingPrice = (float) (
                            $item->selling_price
                            ?? $item->unit_price
                            ?? $item->price
                            ?? 0
                        );

                        $buyingPrice = (float) (
                            $item->buying_price
                            ?? $item->cost_price
                            ?? $product?->buying_price
                            ?? 0
                        );

                        $revenue = $quantity * $sellingPrice;

                        $cost = $quantity * $buyingPrice;

                        $profit = $revenue - $cost;

                    @endphp

                    <tr class="hover:bg-slate-50">

                        <td class="px-4 py-3">
                            {{ $row['order']->created_at?->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-4 py-3">
                            #{{ $row['order']->id }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $product?->name ?? 'Unknown Product' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($quantity, 2) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($buyingPrice) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($sellingPrice) }}
                        </td>

                        <td class="px-4 py-3 text-right font-medium">
                            {{ number_format($revenue) }}
                        </td>

                        <td class="px-4 py-3 text-right font-medium">
                            {{ number_format($profit) }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8"
                            class="px-4 py-12 text-center text-slate-500">

                            No sales found for this period.

                        </td>

                    </tr>

                @endforelse

            </tbody>


            <tfoot class="bg-slate-50 border-t font-semibold">

                <tr>

                    <td colspan="3"
                        class="px-4 py-4 text-right">

                        TOTAL

                    </td>

                    <td class="px-4 py-4 text-right">

                        {{ number_format($data['quantity'], 2) }}

                    </td>

                    <td></td>

                    <td></td>

                    <td class="px-4 py-4 text-right">

                        {{ number_format($data['total']) }}

                    </td>

                    <td class="px-4 py-4 text-right">

                        {{ number_format(
                            $data['total'] - $data['cost']
                        ) }}

                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>