<div class="bg-white border rounded-xl overflow-hidden">

    <div class="p-5 border-b">

        <h2 class="text-lg font-semibold">
            Expenses
        </h2>

    </div>


    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-slate-50 border-b">

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


            <tbody class="divide-y">

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
                            class="px-4 py-12 text-center text-slate-500">

                            No expenses found.

                        </td>

                    </tr>

                @endforelse

            </tbody>


            <tfoot class="bg-slate-50 border-t font-semibold">

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