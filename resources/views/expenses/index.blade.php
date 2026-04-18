<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800">
                {{ __('Expenses') }}
            </h2>

            <a href="{{ route('expenses.create') }}"
               class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">
                + Add Expense
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                Title
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                        @forelse ($expenses as $expense)
                            <tr>

                                <!-- DATE (FIXED) -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $expense->expense_date
                                        ? \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y')
                                        : '-' }}
                                </td>

                                <!-- TITLE -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $expense->title ?? 'No Title' }}
                                </td>

                                <!-- CATEGORY -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $expense->category->name ?? 'Uncategorized' }}
                                </td>

                                <!-- AMOUNT -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-rose-600">
                                    {{ number_format($expense->amount, 2) }}
                                </td>

                                <!-- ACTIONS -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm">

                                    <a href="{{ route('expenses.edit', $expense->id) }}"
                                       class="text-indigo-600 hover:underline">
                                        Edit
                                    </a>

                                    <form action="{{ route('expenses.destroy', $expense->id) }}"
                                          method="POST"
                                          class="inline-block ml-3">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                onclick="return confirm('Delete this expense?')"
                                                class="text-red-600 hover:underline">
                                            Delete
                                        </button>
                                    </form>

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No expenses found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $expenses->links() }}
            </div>

        </div>
    </div>
</x-app-layout>