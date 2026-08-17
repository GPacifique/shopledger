<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                    {{ __('My Orders') }}
                </h2>
                <p class="text-sm mt-1" style="color:#C9BFAE;">
                    {{ $shop->business_name }}
                </p>
            </div>

            <a href="{{ route('shops.orders.waiter', $shop) }}"
               class="inline-flex items-center px-5 py-2.5 rounded-full font-semibold text-sm"
               style="background:#E3A857; color:#14110D;">
                {{ __('+ New Order') }}
            </a>
        </div>
    </x-slot>

    <div class="relative min-h-screen -mt-6 -mx-4 sm:-mx-6 lg:-mx-8" style="background:#14110D;">

        <div class="absolute -top-32 -right-24 w-[420px] h-[420px] rounded-full -z-10"
             style="background: radial-gradient(circle, rgba(227,168,87,0.14) 0%, transparent 70%);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            @php
                $statusStyles = [
                    'pending'   => ['bg' => 'rgba(232,196,104,0.15)', 'text' => '#E8C468', 'border' => 'rgba(232,196,104,0.4)'],
                    'approved'  => ['bg' => 'rgba(111,180,201,0.15)', 'text' => '#6FB4C9', 'border' => 'rgba(111,180,201,0.4)'],
                    'completed' => ['bg' => 'rgba(79,190,142,0.15)',  'text' => '#4FBE8E', 'border' => 'rgba(79,190,142,0.4)'],
                    'cancelled' => ['bg' => 'rgba(224,122,99,0.15)',  'text' => '#E07A63', 'border' => 'rgba(224,122,99,0.4)'],
                ];
            @endphp

            @if ($orders->isEmpty())
                <div class="rounded-3xl p-12 text-center" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                    <p class="text-lg font-semibold mb-2" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                        No orders yet
                    </p>
                    <p class="text-sm mb-6" style="color:#C9BFAE;">
                        Orders you take will show up here with their status.
                    </p>
                    <a href="{{ route('shops.orders.waiter', $shop) }}"
                       class="inline-flex items-center px-5 py-2.5 rounded-full font-semibold text-sm"
                       style="background:#E3A857; color:#14110D;">
                        Take Your First Order
                    </a>
                </div>
            @else

                {{-- Desktop table --}}
                <div class="hidden md:block rounded-3xl overflow-hidden" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                    <table class="w-full text-left">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(227,168,87,0.18);">
                                <th class="px-6 py-4 text-xs uppercase tracking-wide font-semibold" style="color:#E3A857;">Order</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-wide font-semibold" style="color:#E3A857;">Items</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-wide font-semibold" style="color:#E3A857;">Total</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-wide font-semibold" style="color:#E3A857;">Status</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-wide font-semibold" style="color:#E3A857;">Taken</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-wide font-semibold text-right" style="color:#E3A857;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $style = $statusStyles[$order->status] ?? $statusStyles['pending'];
                                    $total = $order->total ?? $order->items->sum(fn ($i) => $i->quantity * $i->price);
                                @endphp
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                                    <td class="px-6 py-4 font-semibold" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm" style="color:#C9BFAE;">
                                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                        {{ number_format($total) }} RWF
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                              style="background:{{ $style['bg'] }}; color:{{ $style['text'] }}; border:1px solid {{ $style['border'] }};">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm" style="color:#C9BFAE;">
                                        {{ $order->created_at->format('d M, H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('shops.orders.bill.download', [$shop, $order]) }}"
                                               class="text-sm font-medium hover:underline" style="color:#4FBE8E;">
                                                Bill
                                            </a>
                                            <a href="{{ route('shops.orders.print', [$shop, $order]) }}" target="_blank"
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

                {{-- Mobile cards --}}
                <div class="md:hidden flex flex-col gap-4">
                    @foreach ($orders as $order)
                        @php
                            $style = $statusStyles[$order->status] ?? $statusStyles['pending'];
                            $total = $order->total ?? $order->items->sum(fn ($i) => $i->quantity * $i->price);
                        @endphp
                        <div class="rounded-2xl p-5" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                    #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                      style="background:{{ $style['bg'] }}; color:{{ $style['text'] }}; border:1px solid {{ $style['border'] }};">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <p class="text-sm mb-1" style="color:#C9BFAE;">
                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} &middot; {{ $order->created_at->format('d M, H:i') }}
                            </p>
                            <p class="text-lg font-semibold mb-4" style="color:#F7F3EC; font-family:'IBM Plex Mono',monospace;">
                                {{ number_format($total) }} RWF
                            </p>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('shops.orders.bill.download', [$shop, $order]) }}"
                                   class="text-sm font-medium hover:underline" style="color:#4FBE8E;">
                                    Download Bill
                                </a>
                                <a href="{{ route('shops.orders.print', [$shop, $order]) }}" target="_blank"
                                   class="text-sm font-medium hover:underline" style="color:#6FB4C9;">
                                    Print
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');
    </style>
</x-app-layout>