<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Other Income Details') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ __('View details of this other income transaction.') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a href="{{ route('other_incomes.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 transition">

                    <svg class="w-5 h-5 mr-2"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>

                    {{ __('Back') }}

                </a>

                <a href="{{ route('other_incomes.edit', $otherIncome) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">

                    <svg class="w-5 h-5 mr-2"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-5-5l5-5m0 0l-5 5m5-5v4"/>
                    </svg>

                    {{ __('Edit') }}

                </a>

            </div>

        </div>
    </x-slot>


    <div class="py-8">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))

                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3">

                    <div class="flex items-center">

                        <svg class="w-5 h-5 text-green-500 mr-2"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 13l4 4L19 7"/>

                        </svg>

                        <p class="text-sm text-green-700">
                            {{ session('success') }}
                        </p>

                    </div>

                </div>

            @endif


            {{-- Main Details Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-gray-200">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                        <div>

                            <p class="text-sm text-gray-500">
                                {{ __('Income Record') }}
                            </p>

                            <h3 class="text-2xl font-bold text-gray-900 mt-1">
                                {{ number_format($otherIncome->amount, 0) }} RWF
                            </h3>

                        </div>


                        {{-- Status --}}
                        <div>

                            @if($otherIncome->status === 'received')

                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700">

                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>

                                    {{ __('Received') }}

                                </span>

                            @elseif($otherIncome->status === 'pending')

                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">

                                    <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>

                                    {{ __('Pending') }}

                                </span>

                            @else

                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-700">

                                    <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>

                                    {{ __('Cancelled') }}

                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- Information --}}
                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">


                        {{-- Income Category --}}
                        <div>

                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Income Category') }}
                            </p>

                            <div class="mt-2 flex items-center">

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

                                <span class="text-sm font-semibold text-gray-900">

                                    {{ $otherIncome->category?->name ?? __('Uncategorized') }}

                                </span>

                            </div>

                        </div>


                        {{-- Date --}}
                        <div>

                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Income Date') }}
                            </p>

                            <p class="mt-2 text-sm font-semibold text-gray-900">

                                {{ $otherIncome->income_date?->format('d M Y') ?? '—' }}

                            </p>

                        </div>


                        {{-- Amount --}}
                        <div>

                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Amount') }}
                            </p>

                            <p class="mt-2 text-lg font-bold text-green-600">

                                {{ number_format($otherIncome->amount, 0) }} RWF

                            </p>

                        </div>


                        {{-- Reference --}}
                        <div>

                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Reference') }}
                            </p>

                            <p class="mt-2 text-sm font-medium text-gray-900">

                                {{ $otherIncome->reference ?: '—' }}

                            </p>

                        </div>


                        {{-- Created By --}}
                        <div>

                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Created By') }}
                            </p>

                            <p class="mt-2 text-sm font-medium text-gray-900">

                                {{ $otherIncome->creator?->name ?? __('System') }}

                            </p>

                        </div>


                        {{-- Created At --}}
                        <div>

                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Created At') }}
                            </p>

                            <p class="mt-2 text-sm font-medium text-gray-900">

                                {{ $otherIncome->created_at?->format('d M Y H:i') ?? '—' }}

                            </p>

                        </div>


                        {{-- Updated At --}}
                        <div>

                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Last Updated') }}
                            </p>

                            <p class="mt-2 text-sm font-medium text-gray-900">

                                {{ $otherIncome->updated_at?->format('d M Y H:i') ?? '—' }}

                            </p>

                        </div>

                    </div>


                    {{-- Description --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">

                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Description') }}
                        </p>

                        <div class="mt-3 rounded-lg bg-gray-50 border border-gray-200 p-4">

                            @if($otherIncome->description)

                                <p class="text-sm text-gray-700 whitespace-pre-line">
                                    {{ $otherIncome->description }}
                                </p>

                            @else

                                <p class="text-sm text-gray-400">
                                    {{ __('No description provided.') }}
                                </p>

                            @endif

                        </div>

                    </div>


                    {{-- Tax Information --}}
                    @if(
                        isset($otherIncome->tax_rate) ||
                        isset($otherIncome->tax_amount)
                    )

                        <div class="mt-8 pt-6 border-t border-gray-200">

                            <h4 class="text-sm font-semibold text-gray-900 mb-4">
                                {{ __('Tax Information') }}
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">

                                    <p class="text-xs text-gray-500">
                                        {{ __('Tax Rate') }}
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ number_format($otherIncome->tax_rate ?? 0, 2) }}%
                                    </p>

                                </div>

                                <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">

                                    <p class="text-xs text-gray-500">
                                        {{ __('Tax Amount') }}
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ number_format($otherIncome->tax_amount ?? 0, 0) }} RWF
                                    </p>

                                </div>

                                <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">

                                    <p class="text-xs text-gray-500">
                                        {{ __('Net Amount') }}
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">

                                        {{
                                            number_format(
                                                ($otherIncome->amount ?? 0) -
                                                ($otherIncome->tax_amount ?? 0),
                                                0
                                            )
                                        }}

                                        RWF

                                    </p>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>


                {{-- Footer Actions --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">

                    <div class="flex flex-col sm:flex-row sm:justify-between gap-3">

                        <a href="{{ route('other_incomes.index') }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">

                            ← {{ __('Back to Other Income') }}

                        </a>


                        <div class="flex flex-col sm:flex-row gap-3">

                            <a href="{{ route('other_incomes.edit', $otherIncome) }}"
                               class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">

                                {{ __('Edit Income') }}

                            </a>


                            <form action="{{ route('other_incomes.destroy', $otherIncome) }}"
                                  method="POST"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this income record?') }}');">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">

                                    {{ __('Delete Income') }}

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Shop Isolation Notice --}}
            <div class="mt-6 rounded-xl bg-blue-50 border border-blue-200 p-4">

                <div class="flex">

                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>

                    </svg>

                    <div class="ml-3">

                        <p class="text-sm font-medium text-blue-800">
                            {{ __('Shop-specific income record') }}
                        </p>

                        <p class="mt-1 text-sm text-blue-700">
                            {{ __('This income transaction belongs to the current MahWiPOS shop and is included only in that shop’s financial records and reports.') }}
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
