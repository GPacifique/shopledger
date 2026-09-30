<div class="bg-white border rounded-xl overflow-hidden">

    <div class="p-5 border-b">

        <h2 class="text-lg font-semibold">
            Purchases
        </h2>

        <p class="text-sm text-slate-500">
            Products purchased during the selected period
        </p>

    </div>


    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-slate-50 border-b">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Date
                    </th>

                    <th class="px-4 py-3 text-left">
                        Supplier
                    </th>

                    <th class="px-4 py-3 text-left">
                        Product
                    </th>

                    <th class="px-4 py-3 text-right">
                        Qty
                    </th>

                    <th class="px-4 py-3 text-right">
                        Unit Cost
                    </th>

                    <th class="px-4 py-3 text-right">
                        Total
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y">

                @forelse($data['items'] as $row)

                    @php

                        $item = $row['item'];

                        $product = $row['product'];

                        $quantity = (float) (
                            $item->quantity ?? 0
                        );

                        $unitCost = (float) (
                            $item->buying_price
                            ?? $item->unit_cost
                            ?? $item->cost
                            ?? 0
                        );

                        $total = $quantity * $unitCost;

                    @endphp

                    <tr>

                        <td class="px-4 py-3">
                            {{ $row['purchase']->created_at?->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row['purchase']->supplier?->name ?? 'N/A' }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $product?->name ?? 'Unknown Product' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($quantity, 2) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($unitCost) }}
                        </td>

                        <td class="px-4 py-3 text-right font-medium">
                            {{ number_format($total) }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"
                            class="px-4 py-12 text-center text-slate-500">

                            No purchases found.

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

                    <td class="px-4 py-4 text-right">

                        {{ number_format($data['total']) }}

                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>