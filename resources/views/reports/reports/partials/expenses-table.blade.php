<div class="ui-card rounded-xl overflow-hidden">

    <div class="ui-bd p-5 border-b">

        <h2 class="ui-text text-lg font-semibold">
            Expenses
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
                        Category
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

                @forelse($data['expenses'] as $expense)

                    <tr>

                        <td class="px-4 py-3">
                            {{ $expense->created_at?->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $expense->category->name ?? 'Other' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $expense->description ?? $expense->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $expense->payment_method ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-right font-medium">
                            {{ number_format(
                                $expense->amount
                                ?? $expense->total
                                ?? 0
                            ) }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="ui-muted px-4 py-12 text-center">

                            No expenses found.

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