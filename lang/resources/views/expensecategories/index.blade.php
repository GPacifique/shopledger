<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800">
                {{ __('Expense Categories') }}
            </h2>

            <a href="{{ route('expensecategories.create') }}"
               class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">
                + New Category
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
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                        @forelse($categories as $category)
                            <tr>

                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ $category->name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $category->description ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm">

                                    <a href="{{ route('expensecategories.edit', $category->id) }}"
                                       class="text-indigo-600 hover:underline">
                                        Edit
                                    </a>

                                    <form action="{{ route('expensecategories.destroy', $category->id) }}"
                                          method="POST"
                                          class="inline-block ml-3">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                onclick="return confirm('Delete this category?')"
                                                class="text-red-600 hover:underline">
                                            Delete
                                        </button>
                                    </form>

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                    No categories found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>
</x-app-layout>