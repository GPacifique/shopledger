<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                    {{ __('Front Desk') }}
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

    <div class="relative min-h-screen -mt-6 -mx-4 sm:-mx-6 lg:-mx-8 overflow-hidden" style="background:#14110D;">

        {{-- Ambient glow, no images --}}
        <div class="absolute -top-32 -left-24 w-[420px] h-[420px] rounded-full -z-10"
             style="background: radial-gradient(circle, rgba(227,168,87,0.18) 0%, transparent 70%);"></div>
        <div class="absolute top-1/3 -right-32 w-[500px] h-[500px] rounded-full -z-10"
             style="background: radial-gradient(circle, rgba(47,107,94,0.22) 0%, transparent 70%);"></div>
        <div class="absolute bottom-0 left-1/4 w-[380px] h-[380px] rounded-full -z-10"
             style="background: radial-gradient(circle, rgba(227,168,87,0.1) 0%, transparent 70%);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- Stats ribbon --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-10">
                @php
                    $stats = [
                        ['label' => 'My Orders', 'value' => $orders, 'accent' => '#E3A857'],
                        ['label' => 'Pending', 'value' => $pendingOrders, 'accent' => '#E8C468'],
                        ['label' => 'Approved', 'value' => $approvedOrders, 'accent' => '#6FB4C9'],
                        ['label' => 'Completed', 'value' => $completedOrders, 'accent' => '#4FBE8E'],
                        ['label' => 'Cancelled', 'value' => $cancelledOrders, 'accent' => '#E07A63'],
                    ];
                @endphp

                @foreach ($stats as $stat)
                    <div class="rounded-2xl p-4" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                        <p class="text-xs uppercase tracking-wide font-semibold" style="color: {{ $stat['accent'] }};">
                            {{ $stat['label'] }}
                        </p>
                        <p class="text-2xl font-bold mt-1" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                            {{ $stat['value'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Take Order — full-width banner --}}
            <a href="{{ route('shops.orders.waiter', $shop) }}"
               class="block rounded-3xl p-8 mb-6 relative overflow-hidden transition-transform hover:-translate-y-1"
               style="background: linear-gradient(135deg, #2A2013 0%, #1C1712 100%); border:1px solid rgba(227,168,87,0.4);">

                <div class="absolute -bottom-16 -right-16 w-56 h-56 rounded-full"
                     style="background: radial-gradient(circle, rgba(227,168,87,0.15) 0%, transparent 70%);"></div>

                <div class="flex items-center gap-5 relative">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0" style="background:rgba(227,168,87,0.22);">
                        <svg class="w-7 h-7" style="color:#E3A857" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-2xl font-semibold mb-1" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                            Take Order
                        </h3>
                        <p class="text-sm" style="color:#C9BFAE;">
                            Start a new order for a customer at the counter — sent straight to the seller for approval.
                        </p>
                    </div>

                    <a href="{{ route('shops.orders.index', $shop) }}"
                       class="hidden sm:inline-flex items-center gap-2 text-sm font-medium hover:underline flex-shrink-0"
                       style="color:#F7F3EC;" onclick="event.stopPropagation()">
                        View all orders &rarr;
                    </a>
                </div>
            </a>

            {{-- Products (left) + Calculator (right) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

                {{-- Products & selling prices --}}
                <div class="rounded-3xl p-5 w-full" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs uppercase tracking-wide font-semibold" style="color:#E3A857;">
                            Products
                        </p>
                        <span class="text-xs" style="color:#C9BFAE;">{{ $products->count() }} items</span>
                    </div>

                    <div class="flex flex-col divide-y max-h-80 overflow-y-auto" style="border-color: rgba(255,255,255,0.06);">
                        @forelse ($products as $product)
                            <div class="flex items-center justify-between py-2.5 px-1">
                                <span class="text-sm truncate pr-3" style="color:#F7F3EC;">
                                    {{ $product->name }}
                                </span>
                                <span class="text-sm font-semibold flex-shrink-0" style="color:#4FBE8E; font-family:'IBM Plex Mono',monospace;">
                                    {{ number_format($product->selling_price) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm py-3" style="color:#C9BFAE;">No products added yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Calculator --}}
                <div class="w-full">
                    @include('waiter.partials.calculator')
                </div>
            </div>

            {{-- Taken orders + status --}}
            @php
                $statusStyles = [
                    'pending'   => ['bg' => 'rgba(232,196,104,0.15)', 'text' => '#E8C468', 'border' => 'rgba(232,196,104,0.4)'],
                    'approved'  => ['bg' => 'rgba(111,180,201,0.15)', 'text' => '#6FB4C9', 'border' => 'rgba(111,180,201,0.4)'],
                    'completed' => ['bg' => 'rgba(79,190,142,0.15)',  'text' => '#4FBE8E', 'border' => 'rgba(79,190,142,0.4)'],
                    'cancelled' => ['bg' => 'rgba(224,122,99,0.15)',  'text' => '#E07A63', 'border' => 'rgba(224,122,99,0.4)'],
                ];
            @endphp

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold" style="font-family:'Fraunces',serif; color:#F7F3EC;">
                    Recent Orders
                </h3>
                <a href="{{ route('shops.orders.index', $shop) }}" class="text-sm font-medium hover:underline" style="color:#E3A857;">
                    View all
                </a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="rounded-3xl p-10 text-center" style="background:#1C1712; border:1px solid rgba(227,168,87,0.18);">
                    <p class="text-sm" style="color:#C9BFAE;">No orders taken yet — start with "Take Order" above.</p>
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
                            @foreach ($recentOrders as $order)
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
                    @foreach ($recentOrders as $order)
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
            @endif

        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');
    </style>
</x-app-layout>