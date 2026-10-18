@extends('layouts.app')

@section('content')
    {{--
        This view lives inside the layout's content area, next to the sidebar.
        It must not use negative margins, min-h-screen, or anything fixed/absolute
        outside its own box, or it will slide under the sidebar.
        - `isolate overflow-hidden` keeps the glow and background inside this panel.
        - `min-w-0` lets the panel shrink inside a flex layout instead of pushing the page wider.
        - Table shows from `lg` up, cards below it, because the sidebar takes width from the content area.
    --}}
    <div class="relative isolate w-full min-w-0 overflow-hidden rounded-3xl" style="background:#14110D;">

        {{-- Glow stays inside the panel --}}
        <div class="pointer-events-none absolute -top-32 -right-24 -z-10 h-[420px] w-[420px] rounded-full"
             aria-hidden="true"
             style="background: radial-gradient(circle, rgba(227,168,87,0.14) 0%, transparent 70%);"></div>

        <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">

            {{-- Header --}}
            <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold leading-tight" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                        {{ __('My Orders') }}
                    </h2>
                    <p class="mt-1 truncate text-sm" style="color:#C9BFAE;">
                        {{ $shop->business_name }}
                    </p>
                </div>

                <a href="{{ route('shops.orders.waiter', $shop) }}"
                   class="inline-flex w-full items-center justify-center rounded-full px-5 py-2.5 text-sm font-semibold sm:w-auto"
                   style="background:#E3A857; color:#14110D;">
                    {{ __('+ New Order') }}
                </a>
            </div>

            @php
                $statusStyles = [
                    'pending'   => ['bg' => 'rgba(232,196,104,0.15)', 'text' => '#E8C468', 'border' => 'rgba(232,196,104,0.4)'],
                    'approved'  => ['bg' => 'rgba(111,180,201,0.15)', 'text' => '#6FB4C9', 'border' => 'rgba(111,180,201,0.4)'],
                    'completed' => ['bg' => 'rgba(79,190,142,0.15)',  'text' => '#4FBE8E', 'border' => 'rgba(79,190,142,0.4)'],
                    'cancelled' => ['bg' => 'rgba(224,122,99,0.15)',  'text' => '#E07A63', 'border' => 'rgba(224,122,99,0.4)'],
                ];
            @endphp

            @if ($orders->isEmpty())
                <div class="rounded-3xl p-8 text-center sm:p-12" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                    <p class="mb-2 text-lg font-semibold" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                        No orders yet
                    </p>
                    <p class="mb-6 text-sm" style="color:#C9BFAE;">
                        Orders you take will show up here with their status.
                    </p>
                    <a href="{{ route('shops.orders.waiter', $shop) }}"
                       class="inline-flex items-center justify-center rounded-full px-5 py-2.5 text-sm font-semibold"
                       style="background:#E3A857; color:#14110D;">
                        Take Your First Order
                    </a>
                </div>
            @else

                {{-- Table (large screens). Scrolls inside its own box if space is tight. --}}
                <div class="hidden overflow-hidden rounded-3xl lg:block" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(227,168,87,0.18);">
                                    <th class="whitespace-nowrap px-4 py-4 text-xs font-semibold uppercase tracking-wide xl:px-6" style="color:#E3A857;">Order</th>
                                    <th class="whitespace-nowrap px-4 py-4 text-xs font-semibold uppercase tracking-wide xl:px-6" style="color:#E3A857;">Items</th>
                                    <th class="whitespace-nowrap px-4 py-4 text-xs font-semibold uppercase tracking-wide xl:px-6" style="color:#E3A857;">Total</th>
                                    <th class="whitespace-nowrap px-4 py-4 text-xs font-semibold uppercase tracking-wide xl:px-6" style="color:#E3A857;">Status</th>
                                    <th class="whitespace-nowrap px-4 py-4 text-xs font-semibold uppercase tracking-wide xl:px-6" style="color:#E3A857;">Taken</th>
                                    <th class="whitespace-nowrap px-4 py-4 text-right text-xs font-semibold uppercase tracking-wide xl:px-6" style="color:#E3A857;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                        $style = $statusStyles[$order->status] ?? $statusStyles['pending'];
                                        $total = $order->total ?? $order->items->sum(fn ($i) => $i->quantity * $i->price);
                                    @endphp
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                                        <td class="whitespace-nowrap px-4 py-4 font-semibold xl:px-6" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                            #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm xl:px-6" style="color:#C9BFAE;">
                                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold xl:px-6" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                            {{ number_format($total) }} RWF
                                        </td>
                                        <td class="px-4 py-4 xl:px-6">
                                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                                  style="background:{{ $style['bg'] }}; color:{{ $style['text'] }}; border:1px solid {{ $style['border'] }};">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm xl:px-6" style="color:#C9BFAE;">
                                            {{ $order->created_at->format('d M, H:i') }}
                                        </td>
                                        <td class="px-4 py-4 xl:px-6">
                                            <div class="flex items-center justify-end gap-4">
                                                <a href="{{ route('shops.orders.bill.download', [$shop, $order]) }}"
                                                   class="text-sm font-medium hover:underline" style="color:#4FBE8E;">
                                                    Bill
                                                </a>
                                                <a href="{{ route('shops.orders.print', [$shop, $order]) }}" target="_blank" rel="noopener"
                                                   class="text-sm font-medium hover:underline" style="color:#6FB4C9;">
                                                    Print
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Cards (below lg, including tablets with the sidebar open) --}}
                <div class="flex flex-col gap-4 lg:hidden">
                    @foreach ($orders as $order)
                        @php
                            $style = $statusStyles[$order->status] ?? $statusStyles['pending'];
                            $total = $order->total ?? $order->items->sum(fn ($i) => $i->quantity * $i->price);
                        @endphp
                        <div class="min-w-0 rounded-2xl p-4 sm:p-5" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <span class="font-semibold" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                    #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-block shrink-0 rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                      style="background:{{ $style['bg'] }}; color:{{ $style['text'] }}; border:1px solid {{ $style['border'] }};">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <p class="mb-1 text-sm" style="color:#C9BFAE;">
                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} &middot; {{ $order->created_at->format('d M, H:i') }}
                            </p>
                            <p class="mb-4 text-lg font-semibold" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                {{ number_format($total) }} RWF
                            </p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                                <a href="{{ route('shops.orders.bill.download', [$shop, $order]) }}"
                                   class="text-sm font-medium hover:underline" style="color:#4FBE8E;">
                                    Download Bill
                                </a>
                                <a href="{{ route('shops.orders.print', [$shop, $order]) }}" target="_blank" rel="noopener"
                                   class="text-sm font-medium hover:underline" style="color:#6FB4C9;">
                                    Print
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 overflow-x-auto">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');
    </style>
@endsection