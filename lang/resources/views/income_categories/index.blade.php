<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Income Categories') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Manage other income streams for this shop.') }}
                </p>
            </div>

            <a href="{{ route('income_categories.create') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">

                <svg class="w-5 h-5 mr-2"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>

                {{ __('Add Income Category') }}

            </a>

        </div>
    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif


            {{-- Statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

                {{-- Total --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                {{ __('Total Categories') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-gray-900">
                                {{ $categories->count() }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-indigo-100 flex items-center justify-center">

                            <svg class="w-6 h-6 text-indigo-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Active --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                {{ __('Active Categories') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-green-600">
                                {{ $categories->where('is_active', true)->count() }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-green-100 flex items-center justify-center">

                            <svg class="w-6 h-6 text-green-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Inactive --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                {{ __('Inactive Categories') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-gray-600">
                                {{ $categories->where('is_active', false)->count() }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-gray-100 flex items-center justify-center">

                            <svg class="w-6 h-6 text-gray-500"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Income Records --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                {{ __('Income Records') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-blue-600">
                                {{ $totalIncomeRecords ?? 0 }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-blue-100 flex items-center justify-center">

                            <svg class="w-6 h-6 text-blue-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.105 0 2 .895 2 2m-2-2V6m0 12v-2m-6-4a6 6 0 1112 0 6 6 0 01-12 0z"/>
                            </svg>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Categories Table --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-200">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('Other Income Streams') }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Income categories available for this shop.') }}
                            </p>
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $categories->count() }}
                            {{ __('categories') }}
                        </div>

                    </div>

                </div>


                @if($categories->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Category') }}
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Description') }}
                                    </th>

                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach($categories as $category)

                                    <tr class="hover:bg-gray-50">

                                        {{-- Number --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>


                                        {{-- Name --}}
                                        <td class="px-6 py-4 whitespace-nowrap">

                                            <div class="flex items-center">

                                                <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">

                                                    <svg class="w-5 h-5 text-indigo-600"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M7 7h.01M7 3h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                                                    </svg>

                                                </div>

                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ $category->name }}
                                                    </p>
                                                </div>

                                            </div>

                                        </td>


                                        {{-- Description --}}
                                        <td class="px-6 py-4 text-sm text-gray-600">

                                            @if($category->description)
                                                {{ $category->description }}
                                            @else
                                                <span class="text-gray-400">
                                                    —
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Status --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center">

                                            @if($category->is_active)

                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>

                                                    {{ __('Active') }}

                                                </span>

                                            @else

                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>

                                                    {{ __('Inactive') }}

                                                </span>

                                            @endif

                                        </td>


                                        {{-- Actions --}}
                                        <td class="px-6 py-4 whitespace-nowrap">

                                            <div class="flex items-center justify-end gap-3">

                                                <a href="{{ route('income_categories.edit', $category) }}"
                                                   class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                    {{ __('Edit') }}
                                                </a>


                                                {{-- Toggle --}}
                                                <form action="{{ route('income_categories.toggle-status', $category) }}"
                                                      method="POST"
                                                      class="inline">

                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                            class="text-sm font-medium {{ $category->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}">

                                                        {{ $category->is_active ? __('Deactivate') : __('Activate') }}

                                                    </button>

                                                </form>


                                                {{-- Delete --}}
                                                <form action="{{ route('income_categories.destroy', $category) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this income category?') }}');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                        {{ __('Delete') }}
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    {{-- Empty State --}}
                    <div class="px-6 py-16 text-center">

                        <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-5">

                            <svg class="w-8 h-8 text-gray-400"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M7 7h.01M7 3h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z"/>

                            </svg>

                        </div>

                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ __('No Income Categories') }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">
                            {{ __('Create income categories such as Rental Income, Commission, Interest, Grants, Services, or Other Income.') }}
                        </p>

                        <a href="{{ route('income_categories.create') }}"
                           class="inline-flex items-center mt-5 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">

                            + {{ __('Create Income Category') }}

                        </a>

                    </div>

                @endif

            </div>


            {{-- Back to Other Income --}}
            <div class="mt-6">

                <a href="{{ route('other_incomes.index') }}"
                   class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">

                    ← {{ __('Back to Other Income') }}

                </a>

            </div>

        </div>
    </div>

</x-app-layout>

