@php
    $value = fn (string $key) => old($key, $settings[$key] ?? null);
    $input = 'mt-1 block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500';
    $label = 'block text-sm font-medium text-gray-700';
    $toggles = [
        'show_logo' => __('Show business logo'),
        'show_customer' => __('Show customer details when available'),
        'show_tax' => __('Show tax details when present on the transaction'),
        'show_qr' => __('Include a receipt QR code'),
        'show_cashier' => __('Show cashier name'),
        'show_contact' => __('Print business contact details'),
        'auto_print' => __('Open the print dialog automatically after a POS sale'),
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Printing preferences') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Configure receipts and invoices for :business.', ['business' => $shop->business_name]) }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    <p class="font-semibold">{{ __('Please check the following settings:') }}</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('print-settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <header class="border-b border-gray-100 px-6 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[.18em] text-emerald-700">{{ __('Paper') }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-gray-900">{{ __('Receipt and invoice formats') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Choose the default paper size. A paper selector remains available on every print preview.') }}</p>
                    </header>
                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <label for="receipt_paper_size" class="{{ $label }}">{{ __('Default receipt paper') }}</label>
                                <select id="receipt_paper_size" name="receipt_paper_size" class="{{ $input }}">
                                    <option value="58mm" @selected($value('receipt_paper_size') === '58mm')>{{ __('58 mm thermal') }}</option>
                                    <option value="80mm" @selected($value('receipt_paper_size') === '80mm')>{{ __('80 mm thermal') }}</option>
                                    <option value="custom" @selected($value('receipt_paper_size') === 'custom')>{{ __('Custom width') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="receipt_custom_width_mm" class="{{ $label }}">{{ __('Custom receipt width (mm)') }}</label>
                                <input id="receipt_custom_width_mm" name="receipt_custom_width_mm" type="number" min="40" max="120" step="1" required value="{{ $value('receipt_custom_width_mm') }}" class="{{ $input }}">
                                <p class="mt-1 text-xs text-gray-500">{{ __('Used when custom width is selected. Supported range: 40–120 mm.') }}</p>
                            </div>
                            <div>
                                <label for="invoice_paper_size" class="{{ $label }}">{{ __('Default invoice paper') }}</label>
                                <select id="invoice_paper_size" name="invoice_paper_size" class="{{ $input }}">
                                    <option value="a4" @selected($value('invoice_paper_size') === 'a4')>A4</option>
                                    <option value="a5" @selected($value('invoice_paper_size') === 'a5')>A5</option>
                                    <option value="letter" @selected($value('invoice_paper_size') === 'letter')>{{ __('Letter') }}</option>
                                    <option value="custom" @selected($value('invoice_paper_size') === 'custom')>{{ __('Custom size') }}</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="invoice_custom_width_mm" class="{{ $label }}">{{ __('Custom width (mm)') }}</label>
                                    <input id="invoice_custom_width_mm" name="invoice_custom_width_mm" type="number" min="100" max="300" step="1" required value="{{ $value('invoice_custom_width_mm') }}" class="{{ $input }}">
                                </div>
                                <div>
                                    <label for="invoice_custom_height_mm" class="{{ $label }}">{{ __('Custom height (mm)') }}</label>
                                    <input id="invoice_custom_height_mm" name="invoice_custom_height_mm" type="number" min="100" max="400" step="1" required value="{{ $value('invoice_custom_height_mm') }}" class="{{ $input }}">
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-gray-50 p-5">
                            <h4 class="font-semibold text-gray-900">{{ __('Print options') }}</h4>
                            <div class="mt-4 space-y-4">
                                @foreach ($toggles as $key => $title)
                                    <label class="flex items-start gap-3 text-sm text-gray-700">
                                        <input type="hidden" name="{{ $key }}" value="0">
                                        <input type="checkbox" name="{{ $key }}" value="1" @checked((bool) $value($key)) class="mt-0.5 rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                                        <span>{{ $title }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900">
                                {{ __('Automatic printing may require pop-ups to be allowed by the browser. Payment amounts are printed only when recorded by the transaction.') }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <header class="border-b border-gray-100 px-6 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[.18em] text-emerald-700">{{ __('Document content') }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-gray-900">{{ __('Messages and terms') }}</h3>
                    </header>
                    <div class="grid gap-5 p-6 md:grid-cols-3">
                        <div>
                            <label for="receipt_footer" class="{{ $label }}">{{ __('Receipt footer') }}</label>
                            <textarea id="receipt_footer" name="receipt_footer" rows="3" maxlength="160" class="{{ $input }}">{{ $value('receipt_footer') }}</textarea>
                            @error('receipt_footer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="invoice_notes" class="{{ $label }}">{{ __('Invoice payment instructions') }}</label>
                            <textarea id="invoice_notes" name="invoice_notes" rows="3" maxlength="1000" class="{{ $input }}">{{ $value('invoice_notes') }}</textarea>
                            @error('invoice_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="invoice_terms" class="{{ $label }}">{{ __('Invoice terms and conditions') }}</label>
                            <textarea id="invoice_terms" name="invoice_terms" rows="3" maxlength="3000" class="{{ $input }}">{{ $value('invoice_terms') }}</textarea>
                            @error('invoice_terms') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Return to POS') }}</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-800">{{ __('Save printing preferences') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
