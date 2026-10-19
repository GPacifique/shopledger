<div class="ui-card rounded-xl overflow-hidden">

    <div class="ui-bd p-5 border-b">

        <h2 class="ui-text text-lg font-semibold">
            Other Income
        </h2>

    </div>


    <div class="overflow-x-auto">

        <table class="ui-table min-w-full text-sm">

            <thead>

                <tr>

                    <th class="px-4 py-3 text-left">
                        Date
                    </th>

                    <th class="px-4 py-3 text-left">
                        Source
                    </th>

                    <th class="px-4 py-3 text-left">
                        Description
                    </th>

                    <th class="px-4 py-3 text-left">
                        Payment Method
                    </th>

                    <th class="px-4 py-3 text-right">
                        Amount
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($data['items'] as $income)

                    <tr>

                        <td class="px-4 py-3">
                            {{ $income->created_at?->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $income->source ?? $income->category->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $income->description ?? $income->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $income->payment_method ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-right font-medium">
                            {{ number_format(
                                $income->amount
                                ?? $income->total
                                ?? 0
                            ) }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="ui-muted px-4 py-12 text-center">

                            No other income found.

                        </td>

                    </tr>

                @endforelse

            </tbody>


            <tfoot class="font-semibold">

                <tr>

                    <td colspan="4"
                        class="px-4 py-4 text-right">

                        TOTAL

                    </td>

                    <td class="px-4 py-4 text-right">

                        {{ number_format($data['total']) }}

                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>