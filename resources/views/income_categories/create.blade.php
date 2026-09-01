<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Add Income Category') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Create an income stream for this shop.') }}
                </p>
            </div>

            <a href="{{ route('income_categories.index') }}"
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

                {{ __('Back to Categories') }}

            </a>

        </div>
    </x-slot>


    <div class="py-8">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Validation Errors --}}
            @if ($errors->any())

                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">

                    <div class="flex">

                        <svg class="h-5 w-5 text-red-400 flex-shrink-0"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>

                        </svg>

                        <div class="ml-3">

                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('Please correct the following errors:') }}
                            </h3>

                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">

                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                </div>

            @endif


            {{-- Form Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-gray-200">

                    <div class="flex items-center">

                        <div class="w-11 h-11 rounded-lg bg-indigo-100 flex items-center justify-center mr-4">

                            <svg class="w-6 h-6 text-indigo-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.105 0 2 .895 2 2m-2-2V6m0 12v-2m-6-4a6 6 0 1112 0 6 6 0 01-12 0z"/>

                            </svg>

                        </div>

                        <div>

                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('Income Category Details') }}
                            </h3>

                            <p class="text-sm text-gray-500">
                                {{ __('Define a reusable income stream for this shop.') }}
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Form --}}
                <form action="{{ route('income_categories.store') }}"
                      method="POST">

                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Category Name --}}
                        <div>

                            <label for="name"
                                   class="block text-sm font-medium text-gray-700 mb-1">

                                {{ __('Category Name') }}

                                <span class="text-red-500">*</span>

                            </label>

                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   required
                                   maxlength="255"
                                   autofocus
                                   placeholder="{{ __('e.g. Rental Income') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">

                            @error('name')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('Choose a clear name that identifies the source of income.') }}
                            </p>

                        </div>


                        {{-- Description --}}
                        <div>

                            <label for="description"
                                   class="block text-sm font-medium text-gray-700 mb-1">

                                {{ __('Description') }}

                            </label>

                            <textarea name="description"
                                      id="description"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="{{ __('Describe this income stream...') }}"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>

                            @error('description')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Active Status --}}
                        <div>

                            <label class="flex items-start cursor-pointer">

                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">

                                <span class="ml-3">

                                    <span class="block text-sm font-medium text-gray-700">
                                        {{ __('Active Category') }}
                                    </span>

                                    <span class="block text-xs text-gray-500 mt-1">
                                        {{ __('Active categories can be selected when recording other income.') }}
                                    </span>

                                </span>

                            </label>

                            @error('is_active')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Information --}}
                        <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">

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
                                        {{ __('Shop-specific income stream') }}
                                    </p>

                                    <p class="mt-1 text-sm text-blue-700">
                                        {{ __('This category will belong to the current shop. Other shops will have their own income categories.') }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">

                        <div class="flex flex-col sm:flex-row sm:justify-end gap-3">

                            <a href="{{ route('income_categories.index') }}"
                               class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">

                                {{ __('Cancel') }}

                            </a>


                            <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">

                                <svg class="w-5 h-5 mr-2"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 13l4 4L19 7"/>

                                </svg>

                                {{ __('Save Income Category') }}

                            </button>

                        </div>

                    </div>

                </form>

            </div>


            {{-- Examples --}}
            <div class="mt-6 bg-white rounded-xl border border-gray-200 shadow-sm p-6">

                <h3 class="text-sm font-semibold text-gray-900">
                    {{ __('Examples of Other Income Streams') }}
                </h3>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <div class="flex items-center p-3 rounded-lg bg-gray-50">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></span>
                        <span class="text-sm text-gray-700">
                            {{ __('Rental Income') }}
                        </span>
                    </div>

                    <div class="flex items-center p-3 rounded-lg bg-gray-50">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></span>
                        <span class="text-sm text-gray-700">
                            {{ __('Commission Income') }}
                        </span>
                    </div>

                    <div class="flex items-center p-3 rounded-lg bg-gray-50">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></span>
                        <span class="text-sm text-gray-700">
                            {{ __('Service Income') }}
                        </span>
                    </div>

                    <div class="flex items-center p-3 rounded-lg bg-gray-50">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></span>
                        <span class="text-sm text-gray-700">
                            {{ __('Interest Income') }}
                        </span>
                    </div>

                    <div class="flex items-center p-3 rounded-lg bg-gray-50">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></span>
                        <span class="text-sm text-gray-700">
                            {{ __('Grants') }}
                        </span>
                    </div>

                    <div class="flex items-center p-3 rounded-lg bg-gray-50">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></span>
                        <span class="text-sm text-gray-700">
                            {{ __('Miscellaneous Income') }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>