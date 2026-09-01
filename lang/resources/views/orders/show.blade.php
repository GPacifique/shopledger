{{-- resources/views/orders/show.blade.php --}}

<x-application-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div class="min-w-0">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 break-anywhere">
                    Order {{ $order->order_number }}
                </h2>

                <p class="text-xs sm:text-sm text-gray-500 break-anywhere">
                    {{ $shop->name }}
                    <span class="hidden sm:inline">·</span>
                    <span class="sm:ml-1">
                        {{ $order->created_at->format('M j, Y g:ia') }}
                    </span>
                </p>
            </div>

            @php
                $badgeClasses = [
                    'pending' => 'bg-amber-100 text-amber-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                ];
            @endphp

            <span
                class="
                    inline-flex
                    self-start
                    sm:self-auto
                    items-center
                    px-3
                    py-1
                    rounded-full
                    text-xs
                    sm:text-sm
                    font-medium
                    whitespace-nowrap
                    {{ $badgeClasses[$order->status] ?? 'bg-gray-100 text-gray-600' }}
                "
            >
                {{ ucfirst($order->status) }}
            </span>

        </div>
    </x-slot>


    <div class="w-full max-w-4xl mx-auto">


        {{-- ================================================================
             SUCCESS MESSAGE
        ================================================================= --}}
        @if (session('success'))

            <div
                class="
                    mb-4
                    rounded-lg
                    bg-green-50
                    border
                    border-green-200
                    text-green-800
                    px-3
                    sm:px-4
                    py-3
                    text-sm
                "
                role="alert"
            >

                <div class="flex items-start gap-2">

                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>

                    <span class="break-anywhere">
                        {{ session('success') }}
                    </span>

                </div>

            </div>

        @endif


        {{-- ================================================================
             VALIDATION ERRORS
        ================================================================= --}}
        @if ($errors->any())

            <div
                class="
                    mb-4
                    rounded-lg
                    bg-red-50
                    border
                    border-red-200
                    text-red-800
                    px-3
                    sm:px-4
                    py-3
                    text-sm
                "
                role="alert"
            >

                <div class="flex items-start gap-2">

                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>

                    <ul class="list-disc list-inside space-y-1">

                        @foreach ($errors->all() as $error)

                            <li class="break-anywhere">
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        @endif


        {{-- ================================================================
             ORDER DETAILS
        ================================================================= --}}
        <div
            class="
                bg-white
                rounded-xl
                border
                border-gray-200
                shadow-sm
                p-4
                sm:p-6
                mb-6
            "
        >

            {{-- Waiter / Customer --}}
            <div
                class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    gap-4
                    mb-6
                    text-sm
                "
            >

                <div>

                    <span class="text-gray-500">
                        Waiter
                    </span>

                    <p class="font-medium text-gray-900 break-anywhere">
                        {{ $order->waiter->name ?? '—' }}
                    </p>

                </div>


                <div>

                    <span class="text-gray-500">
                        Customer
                    </span>

                    <p class="font-medium text-gray-900 break-anywhere">
                        {{ $order->customer->name ?? 'Walk-in' }}
                    </p>

                </div>

            </div>


            {{-- ============================================================
                 ORDER ITEMS
            ============================================================= --}}
            <div class="overflow-x-auto -mx-1 px-1">

                <table class="w-full min-w-[600px] text-sm">

                    <thead>

                        <tr
                            class="
                                border-b
                                border-gray-100
                                text-gray-500
                            "
                        >

                            <th class="text-left font-medium pb-3">
                                Item
                            </th>

                            <th class="text-right font-medium pb-3">
                                Qty
                            </th>

                            <th class="text-right font-medium pb-3">
                                Price
                            </th>

                            <th class="text-right font-medium pb-3">
                                Total
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-50">

                        @forelse ($order->items as $item)

                            <tr>

                                <td
                                    class="
                                        py-3
                                        text-gray-800
                                        break-anywhere
                                    "
                                >
                                    {{ $item->product->name ?? $item->description ?? 'Item' }}
                                </td>


                                <td class="py-3 text-right text-gray-600 whitespace-nowrap">

                                    {{ rtrim(rtrim((string) $item->quantity, '0'), '.') }}

                                </td>


                                <td class="py-3 text-right text-gray-600 whitespace-nowrap">

                                    {{ number_format($item->unit_price, 2) }}

                                    <span class="text-xs text-gray-400">
                                        RWF
                                    </span>

                                </td>


                                <td
                                    class="
                                        py-3
                                        text-right
                                        font-medium
                                        text-gray-900
                                        whitespace-nowrap
                                    "
                                >

                                    {{ number_format($item->line_total, 2) }}

                                    <span class="text-xs text-gray-400">
                                        RWF
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="py-6 text-center text-gray-400"
                                >
                                    No items found for this order.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ============================================================
                 TOTALS
            ============================================================= --}}
            <div
                class="
                    border-t
                    border-gray-100
                    mt-4
                    pt-4
                    space-y-2
                    text-sm
                    w-full
                    sm:max-w-sm
                    sm:ml-auto
                "
            >

                <div class="flex justify-between gap-4 text-gray-600">

                    <span>
                        Subtotal
                    </span>

                    <span class="font-medium whitespace-nowrap">
                        {{ number_format($order->subtotal, 2) }}
                        <span class="text-xs text-gray-400">RWF</span>
                    </span>

                </div>


                @if ($order->discount_amount > 0)

                    <div class="flex justify-between gap-4 text-gray-600">

                        <span>
                            Discount
                        </span>

                        <span class="font-medium text-red-600 whitespace-nowrap">
                            -{{ number_format($order->discount_amount, 2) }}
                            <span class="text-xs text-gray-400">RWF</span>
                        </span>

                    </div>

                @endif


                @if ($order->tax_amount > 0)

                    <div class="flex justify-between gap-4 text-gray-600">

                        <span>
                            Tax
                        </span>

                        <span class="font-medium whitespace-nowrap">
                            {{ number_format($order->tax_amount, 2) }}
                            <span class="text-xs text-gray-400">RWF</span>
                        </span>

                    </div>

                @endif


                <div
                    class="
                        flex
                        justify-between
                        gap-4
                        font-semibold
                        text-gray-900
                        text-base
                        border-t
                        border-gray-100
                        pt-2
                    "
                >

                    <span>
                        Total
                    </span>

                    <span class="whitespace-nowrap">

                        {{ number_format($order->total_amount, 2) }}

                        <span class="text-xs text-gray-400">
                            RWF
                        </span>

                    </span>

                </div>

            </div>


            {{-- ============================================================
                 NOTES
            ============================================================= --}}
            @if ($order->notes)

                <div
                    class="
                        mt-5
                        pt-4
                        border-t
                        border-gray-100
                        text-sm
                    "
                >

                    <span class="text-gray-500">
                        Notes
                    </span>

                    <p
                        class="
                            text-gray-700
                            mt-1
                            break-anywhere
                        "
                    >
                        {{ $order->notes }}
                    </p>

                </div>

            @endif

        </div>


        {{-- ================================================================
             REVIEW
        ================================================================= --}}
        @if ($order->status !== 'pending')

            <div
                class="
                    bg-white
                    rounded-xl
                    border
                    border-gray-200
                    shadow-sm
                    p-4
                    sm:p-6
                    mb-6
                    text-sm
                "
            >

                <h2 class="font-semibold text-gray-800 mb-3">
                    Review
                </h2>


                @if ($order->status === 'approved')

                    <p class="text-gray-600">

                        Approved by

                        <span class="font-medium text-gray-900">
                            {{ $order->reviewer->name ?? '—' }}
                        </span>

                        @if ($order->reviewed_at)
                            {{ $order->reviewed_at->diffForHumans() }}.
                        @endif

                    </p>


                    @if ($order->sale)

                        <p class="mt-2 text-gray-600">

                            Recorded as sale

                            <a
                                href="{{ route('shops.sales.show', [$shop, $order->sale]) }}"
                                class="
                                    text-blue-600
                                    hover:text-blue-800
                                    hover:underline
                                    font-medium
                                "
                            >
                                #{{ $order->sale->id }}
                            </a>.

                        </p>

                    @endif


                @elseif ($order->status === 'rejected')

                    <p class="text-gray-600">

                        Rejected by

                        <span class="font-medium text-gray-900">
                            {{ $order->reviewer->name ?? '—' }}
                        </span>

                        @if ($order->reviewed_at)
                            {{ $order->reviewed_at->diffForHumans() }}.
                        @endif

                    </p>


                    @if ($order->rejection_reason)

                        <div
                            class="
                                mt-3
                                rounded-lg
                                bg-red-50
                                border
                                border-red-100
                                p-3
                                text-red-800
                                break-anywhere
                            "
                        >
                            <span class="font-medium">
                                Reason:
                            </span>

                            {{ $order->rejection_reason }}
                        </div>

                    @endif


                @elseif ($order->status === 'cancelled')

                    <p class="text-gray-600">
                        Cancelled by the waiter before review.
                    </p>

                @endif

            </div>

        @endif


        {{-- ================================================================
             ACTIONS
        ================================================================= --}}
        @if ($order->status === 'pending')

            <div
                class="
                    bg-white
                    rounded-xl
                    border
                    border-gray-200
                    shadow-sm
                    p-4
                    sm:p-5
                "
            >

                <div
                    class="
                        flex
                        flex-col
                        gap-3
                        sm:flex-row
                        sm:flex-wrap
                        sm:items-start
                    "
                >

                    {{-- Waiter cancel --}}
                    @if (
                        auth()->user()->role === 'waiter'
                        && $order->created_by === auth()->id()
                    )

                        <form
                            method="POST"
                            action="{{ route('shops.orders.cancel', [$shop, $order]) }}"
                            onsubmit="return confirm('Cancel this order?');"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    w-full
                                    sm:w-auto
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2
                                    text-sm
                                    text-red-600
                                    hover:text-red-800
                                    font-medium
                                    border
                                    border-red-200
                                    hover:bg-red-50
                                    px-4
                                    py-2
                                    rounded-lg
                                "
                            >
                                <i class="fa-solid fa-xmark"></i>
                                Cancel Order
                            </button>

                        </form>

                    @endif


                    {{-- Seller / Admin actions --}}
                    @if (
                        in_array(
                            auth()->user()->role,
                            ['seller', 'admin'],
                            true
                        )
                    )

                        {{-- Approve --}}
                        <form
                            method="POST"
                            action="{{ route('shops.orders.approve', [$shop, $order]) }}"
                            class="
                                w-full
                                sm:w-auto
                                flex
                                flex-col
                                sm:flex-row
                                gap-2
                            "
                        >

                            @csrf

                            <select
                                name="payment_method"
                                required
                                class="
                                    w-full
                                    sm:w-auto
                                    text-sm
                                    rounded-lg
                                    border-gray-300
                                    shadow-sm
                                    focus:border-green-500
                                    focus:ring-green-500
                                "
                            >

                                <option value="">
                                    Payment method…
                                </option>

                                @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)

                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>

                                @endforeach

                            </select>


                            <button
                                type="submit"
                                class="
                                    w-full
                                    sm:w-auto
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2
                                    bg-green-600
                                    hover:bg-green-700
                                    text-white
                                    text-sm
                                    font-medium
                                    px-4
                                    py-2
                                    rounded-lg
                                "
                            >

                                <i class="fa-solid fa-check"></i>

                                Approve

                            </button>

                        </form>


                        {{-- Reject --}}
                        <div
                            x-data="{ open: false }"
                            class="relative w-full sm:w-auto"
                        >

                            <form
                                method="POST"
                                action="{{ route('shops.orders.reject', [$shop, $order]) }}"
                                class="w-full sm:w-auto"
                            >

                                @csrf

                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="
                                        w-full
                                        sm:w-auto
                                        inline-flex
                                        items-center
                                        justify-center
                                        gap-2
                                        bg-red-50
                                        hover:bg-red-100
                                        text-red-700
                                        text-sm
                                        font-medium
                                        px-4
                                        py-2
                                        rounded-lg
                                    "
                                >

                                    <i class="fa-solid fa-xmark"></i>

                                    Reject

                                </button>


                                {{-- Reject form --}}
                                <div
                                    x-show="open"
                                    x-cloak
                                    @click.outside="open = false"
                                    class="
                                        mt-2
                                        sm:absolute
                                        sm:right-0
                                        sm:top-full
                                        sm:mt-2
                                        bg-white
                                        border
                                        border-gray-200
                                        rounded-lg
                                        shadow-xl
                                        p-4
                                        w-full
                                        sm:w-72
                                        z-50
                                    "
                                >

                                    <label
                                        class="
                                            block
                                            text-xs
                                            font-medium
                                            text-gray-700
                                            mb-1
                                        "
                                    >
                                        Rejection reason
                                    </label>

                                    <textarea
                                        name="reason"
                                        rows="3"
                                        required
                                        class="
                                            w-full
                                            text-sm
                                            rounded-lg
                                            border-gray-300
                                            focus:border-red-500
                                            focus:ring-red-500
                                            mb-3
                                        "
                                        placeholder="Enter reason for rejection..."
                                    ></textarea>


                                    <div class="flex gap-2">

                                        <button
                                            type="button"
                                            @click="open = false"
                                            class="
                                                flex-1
                                                border
                                                border-gray-300
                                                text-gray-600
                                                text-xs
                                                font-medium
                                                px-3
                                                py-2
                                                rounded-lg
                                            "
                                        >
                                            Cancel
                                        </button>


                                        <button
                                            type="submit"
                                            class="
                                                flex-1
                                                bg-red-600
                                                hover:bg-red-700
                                                text-white
                                                text-xs
                                                font-medium
                                                px-3
                                                py-2
                                                rounded-lg
                                            "
                                        >
                                            Confirm Rejection
                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    @endif

                </div>

            </div>

        @endif

    </div>

</x-application-layout>