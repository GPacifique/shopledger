@php
    // Theme-aware class sets. They read the CSS variables defined in layouts/app.blade.php
    // (--surface, --text, --border, ...), which switch automatically when `html.dark` is toggled.
    // Class names are written out in full so Tailwind's JIT can see them.
    $card   = 'overflow-hidden rounded-lg border border-[color:var(--border)] bg-[color:var(--surface)] shadow-sm';
    $text   = 'text-[color:var(--text)]';
    $muted  = 'text-[color:var(--text-muted)]';
    $link   = 'text-[color:var(--accent-text)] hover:underline';
    $danger = 'text-[color:var(--danger-text)]';
    $th     = 'px-6 py-3 text-xs font-medium uppercase tracking-wider text-[color:var(--text-muted)]';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex min-w-0 items-center">
                <a href="{{ route('purchases.index') }}"
                   class="mr-4 text-[color:var(--text-muted)] hover:text-[color:var(--text)]"
                   aria-label="{{ __('Back to purchases') }}">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="truncate text-xl font-semibold leading-tight {{ $text }}">
                    {{ __('Purchase') }} #{{ $purchase->id }}
                </h2>
            </div>

             <div class="flex shrink-0 gap-2">
                {{-- data-no-loader: a file download never unloads the page, so the loading overlay must not start --}}
                <a href="{{ route('purchases.download', $purchase) }}" data-no-loader
                   class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-red-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('Download PDF') }}
                </a>
                <a href="{{ route('purchases.create') }}"
                   class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-700">
                    {{ __('New Purchase') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
           

            <!-- Purchase Info -->
            <div class="{{ $card }} mb-6">
                <div class="p-6">
                    <dl class="grid grid-cols-2 gap-6 md:grid-cols-4">
                        <div>
                            <dt class="text-sm font-medium {{ $muted }}">{{ __('Purchase Date') }}</dt>
                            <dd class="mt-1 text-lg font-semibold {{ $text }}">{{ $purchase->purchase_date->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium {{ $muted }}">{{ __('Supplier') }}</dt>
                            <dd class="mt-1 text-lg {{ $text }}">
                                @if($purchase->supplier)
                                    <a href="{{ route('suppliers.show', $purchase->supplier) }}" class="{{ $link }}">
                                        {{ $purchase->supplier->name }}
                                    </a>
                                @else
                                    <span class="{{ $muted }}">{{ __('No supplier') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium {{ $muted }}">{{ __('Created By') }}</dt>
                            <dd class="mt-1 text-lg {{ $text }}">{{ $purchase->creator->name ?? __('Unknown') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium {{ $muted }}">{{ __('Total Amount') }}</dt>
                            <dd class="mt-1 text-2xl font-bold {{ $danger }}">{{ rwf($purchase->total_amount) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Items -->
            <div class="{{ $card }} mb-6">
                <div class="border-b border-[color:var(--border)] px-6 py-4">
                    <h3 class="text-lg font-medium {{ $text }}">{{ __('Purchase Items') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[color:var(--border)]">
                        <thead class="bg-[color:var(--surface-2)]">
                            <tr>
                                <th scope="col" class="{{ $th }} text-left">#</th>
                                <th scope="col" class="{{ $th }} text-left">{{ __('Product') }}</th>
                                <th scope="col" class="{{ $th }} text-right">{{ __('Quantity') }}</th>
                                <th scope="col" class="{{ $th }} text-right">{{ __('Unit Cost') }}</th>
                                <th scope="col" class="{{ $th }} text-right">{{ __('Line Total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[color:var(--border)] bg-[color:var(--surface)]">
                            @foreach($purchase->items as $index => $item)
                                <tr class="hover:bg-[color:var(--hover)]">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm {{ $muted }}">{{ $index + 1 }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('products.show', $item->product) }}" class="text-sm font-medium {{ $link }}">
                                            {{ $item->product->name }}
                                        </a>
                                        <p class="text-xs {{ $muted }}">{{ $item->product->sku }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm {{ $text }}">{{ $item->quantity }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm {{ $text }}">{{ rwf($item->unit_cost) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold {{ $text }}">{{ rwf($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-[color:var(--surface-2)]">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-medium {{ $text }}">{{ __('Total') }}:</td>
                                <td class="px-6 py-4 text-right text-lg font-bold {{ $danger }}">{{ rwf($purchase->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between gap-4">
                <div class="text-sm {{ $muted }}">
                    {{ __('Created on') }} {{ $purchase->created_at->format('M d, Y \a\t h:i A') }}
                </div>

                @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())
                    {{-- data-confirm is handled by the global submit listener in layouts/app.blade.php (CSP-safe, no inline JS) --}}
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                          data-confirm="{{ __('Are you sure you want to delete this purchase? This will reverse all stock changes.') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center rounded-md border border-[color:var(--danger-text)] bg-[color:var(--surface)] px-4 py-2 text-sm font-medium shadow-sm hover:bg-[color:var(--danger-soft)] {{ $danger }}">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('Delete Purchase') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>