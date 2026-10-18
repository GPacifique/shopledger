<x-app-layout>
    @php
        $user      = auth()->user();
        $isWaiter  = $user->role === 'waiter';
        $canReview = in_array($user->role, ['seller', 'admin'], true);   // same roles your approve() allows

        $tabs = [
            'pending'   => 'Pending',
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            'cancelled' => 'Cancelled',
            'all'       => 'All',
        ];
        $activeTab = $status ?: 'all';

        // Payment options for the approve form (same source as the order page)
        $pm = \App\Models\Order::PAYMENT_METHODS;
        $paymentOptions = array_is_list($pm)
            ? collect($pm)->mapWithKeys(fn ($v) => [$v => ucfirst(str_replace('_', ' ', $v))])->all()
            : $pm;

        $badges = [
            'pending'   => 'bg-amber-100 text-amber-800',
            'approved'  => 'bg-green-100 text-green-800',
            'rejected'  => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-700',
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $isWaiter ? 'My Orders' : 'Orders' }}
                </h2>
                <p class="text-sm text-gray-500">{{ $shop->business_name }}</p>
            </div>

            @if ($isWaiter)
                <a href="{{ route('shops.orders.waiter', $shop) }}"
                   class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    + Take Order
                </a>
            @endif
        </div>
    </x-slot>

    <style>[x-cloak]{display:none !important}</style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"
             x-data="{
                rejectOpen: false,
                rejectAction: '',
                rejectLabel: '',
                openReject(action, label) { this.rejectAction = action; this.rejectLabel = label; this.rejectOpen = true; }
             }">

            {{-- Messages --}}
            @if (session('success'))
                <div class="mx-4 sm:mx-0 rounded-lg bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mx-4 sm:mx-0 rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mx-4 sm:mx-0 rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200">
                    <ul class="list-disc pl-5">
                        @foreach (collect($errors->all())->unique() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @if ($canReview && $status === 'pending')
                <div class="mx-4 sm:mx-0 rounded-lg bg-amber-50 p-3 text-sm text-amber-800 ring-1 ring-amber-200">
                    These orders are waiting for your decision. Approving records a sale and deducts stock.
                </div>
            @endif

            {{-- Status tabs --}}
            <div class="px-4 sm:px-0">
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach ($tabs as $key => $label)
                        <a href="{{ route('shops.orders.index', [$shop, 'status' => $key]) }}"
                           class="shrink-0 rounded-full px-4 py-2 text-sm font-medium
                                  {{ $activeTab === $key ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Orders table --}}
            <div class="mx-4 sm:mx-0 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Order</th>
                                @unless ($isWaiter)<th class="px-4 py-3">Waiter</th>@endunless
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Items</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Submitted</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr class="align-top">
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $order->order_number }}
                                    </td>

                                    @unless ($isWaiter)
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $order->waiter?->name ?? '—' }}</td>
                                    @endunless

                                    <td class="px-4 py-3 whitespace-nowrap">{{ $order->customer?->name ?? 'Walk-in' }}</td>

                                    <td class="px-4 py-3">
                                        {{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('item', $order->items->count()) }}
                                        <p class="max-w-[14rem] truncate text-xs text-gray-500">
                                            {{ $order->items->pluck('description')->filter()->take(3)->implode(', ') }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap font-medium">
                                        {{ number_format($order->total_amount) }} RWF
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badges[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        @if ($order->status === 'rejected' && $order->rejection_reason)
                                            <p class="mt-1 max-w-[14rem] text-xs text-red-600">{{ $order->rejection_reason }}</p>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('shops.orders.show', [$shop, $order]) }}"
                                               class="text-indigo-600 hover:underline">View</a>

                                            {{-- Seller / admin review actions --}}
                                            @if ($canReview && $order->canBeReviewed())
                                                <form method="POST" action="{{ route('shops.orders.approve', [$shop, $order]) }}"
                                                      class="flex items-center gap-1"
                                                      onsubmit="return confirm('Approve order {{ $order->order_number }}? This records a sale and deducts stock.')">
                                                    @csrf
                                                    <select name="payment_method" required aria-label="Payment method"
                                                            class="rounded border-gray-300 py-1 text-xs">
                                                        @unless ($order->payment_method)
                                                            <option value="">Payment…</option>
                                                        @endunless
                                                        @foreach ($paymentOptions as $value => $label)
                                                            <option value="{{ $value }}" @selected($order->payment_method === $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="font-medium text-green-700 hover:underline">Approve</button>
                                                </form>

                                                <button type="button"
                                                        @click="openReject(@js(route('shops.orders.reject', [$shop, $order])), @js($order->order_number))"
                                                        class="font-medium text-red-600 hover:underline">Reject</button>
                                            @endif

                                            {{-- Waiter cancel (own pending orders only) --}}
                                            @if ($isWaiter && $order->created_by === $user->id && $order->canBeCancelled())
                                                <form method="POST" action="{{ route('shops.orders.cancel', [$shop, $order]) }}"
                                                      onsubmit="return confirm('Cancel order {{ $order->order_number }}?')">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:underline">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isWaiter ? 7 : 8 }}" class="px-4 py-12 text-center text-gray-500">
                                        <p class="font-medium">No orders found.</p>
                                        @if ($isWaiter)
                                            <a href="{{ route('shops.orders.waiter', $shop) }}" class="mt-2 inline-block text-indigo-600 underline">Take your first order</a>
                                        @elseif ($status === 'pending')
                                            <p class="text-sm">Nothing is waiting for approval.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="border-t px-4 py-3">{{ $orders->withQueryString()->links() }}</div>
                @endif
            </div>

            {{-- Reject modal (one shared dialog for every row) --}}
            <div x-show="rejectOpen" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                 @keydown.escape.window="rejectOpen = false">
                <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.outside="rejectOpen = false">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Reject order <span x-text="rejectLabel"></span>
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">The waiter will see this reason. Stock is not changed.</p>

                    <form method="POST" :action="rejectAction" class="mt-4">
                        @csrf
                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea id="reason" name="reason" rows="3" required maxlength="500"
                                  class="mt-1 w-full rounded-lg border-gray-300 text-sm"
                                  placeholder="e.g. Item no longer available"></textarea>

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" @click="rejectOpen = false"
                                    class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Back</button>
                            <button type="submit"
                                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Reject order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>