<div class="ui-card rounded-xl overflow-hidden">

    <div class="ui-bd p-5 border-b">

        <h2 class="ui-text text-lg font-semibold">
            Purchases
        </h2>

        <p class="ui-muted text-sm">
            Products purchased during the selected period
        </p>

    </div>


    <div class="overflow-x-auto">

        <table class="ui-table min-w-full text-sm">

            <thead>

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


            <tbody>

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
                            class="ui-muted px-4 py-12 text-center">

                            No purchases found.

                        </td>

                    </tr>

                @endforelse

            </tbody>


            <tfoot class="font-semibold">

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