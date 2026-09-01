{{-- resources/views/orders/index.blade.php --}}

<x-application-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">
                    Orders
                </h2>

                <p class="text-xs sm:text-sm text-gray-500">
                    {{ $shop->name }}
                </p>
            </div>

            <div class="text-xs sm:text-sm text-gray-500">
                Order Management
            </div>

        </div>
    </x-slot>


    <div class="w-full max-w-7xl mx-auto">


        {{-- ================================================================
             SUCCESS MESSAGE
        ================================================================= --}}
        @if (session('success'))

            <div
                class="mb-4 rounded-lg bg-green-50 border border-green-200
                       text-green-800 px-3 sm:px-4 py-3 text-sm"
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
             ERRORS
        ================================================================= --}}
        @if ($errors->any())

            <div
                class="mb-4 rounded-lg bg-red-50 border border-red-200
                       text-red-800 px-3 sm:px-4 py-3 text-sm"
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
             STATUS TABS
        ================================================================= --}}
        <div
            class="
                mb-4
                bg-white
                border
                border-gray-200
                rounded-xl
                shadow-sm
                overflow-hidden
            "
        >

            <div
                class="
                    flex
                    overflow-x-auto
                    scrollbar-thin
                "
            >

                @foreach (
                    [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled'
                    ]
                    as $key => $label
                )

                    <a
                        href="{{ route('shops.orders.index', [$shop, 'status' => $key]) }}"
                        class="
                            flex
                            items-center
                            gap-1.5
                            shrink-0
                            px-4
                            py-3
                            text-sm
                            font-medium
                            border-b-2
                            transition-colors
                            {{ ($status ?? 'pending') === $key
                                ? 'border-blue-600 text-blue-600 bg-blue-50/50'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                            }}
                        "
                    >

                        {{ $label }}


                        @if (
                            $key === 'pending'
                            && ($pendingCount ?? 0) > 0
                        )

                            <span
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    rounded-full
                                    bg-amber-100
                                    text-amber-800
                                    text-xs
                                    font-semibold
                                    min-w-[20px]
                                    h-5
                                    px-1
                                "
                            >
                                {{ $pendingCount }}
                            </span>

                        @endif

                    </a>

                @endforeach

            </div>

        </div>


        {{-- ================================================================
             ORDERS TABLE
        ================================================================= --}}
        <div
            class="
                bg-white
                rounded-xl
                border
                border-gray-200
                shadow-sm
                overflow-hidden
            "
        >

            {{-- Mobile horizontal scrolling --}}
            <div class="overflow-x-auto">

                <table
                    class="
                        min-w-[850px]
                        w-full
                        divide-y
                        divide-gray-200
                        text-sm
                    "
                >

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="
                                    px-4
                                    py-3
                                    text-left
                                    font-medium
                                    text-gray-500
                                    whitespace-nowrap
                                "
                            >
                                Order #
                            </th>

                            <th
                                class="
                                    px-4
                                    py-3
                                    text-left
                                    font-medium
                                    text-gray-500
                                    whitespace-nowrap
                                "
                            >
                                Waiter
                            </th>

                            <th
                                class="
                                    px-4
                                    py-3
                                    text-left
                                    font-medium
                                    text-gray-500
                                    whitespace-nowrap
                                "
                            >
                                Items
                            </th>

                            <th
                                class="
                                    px-4
                                    py-3
                                    text-left
                                    font-medium
                                    text-gray-500
                                    whitespace-nowrap
                                "
                            >
                                Total
                            </th>

                            <th
                                class="
                                    px-4
                                    py-3
                                    text-left
                                    font-medium
                                    text-gray-500
                                    whitespace-nowrap
                                "
                            >
                                Submitted
                            </th>

                            <th
                                class="
                                    px-4
                                    py-3
                                    text-right
                                    font-medium
                                    text-gray-500
                                    whitespace-nowrap
                                "
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($orders as $order)

                            <tr
                                class="hover:bg-gray-50 transition-colors"
                            >

                                {{-- Order number --}}
                                <td class="px-4 py-3 whitespace-nowrap">

                                    <a
                                        href="{{ route('shops.orders.show', [$shop, $order]) }}"
                                        class="
                                            text-blue-600
                                            hover:text-blue-800
                                            hover:underline
                                            font-medium
                                        "
                                    >
                                        {{ $order->order_number }}
                                    </a>

                                </td>


                                {{-- Waiter --}}
                                <td class="px-4 py-3">

                                    <div class="flex items-center gap-2">

                                        <div
                                            class="
                                                h-8
                                                w-8
                                                rounded-full
                                                bg-gray-100
                                                flex
                                                items-center
                                                justify-center
                                                text-gray-500
                                                shrink-0
                                            "
                                        >
                                            <i class="fa-solid fa-user text-xs"></i>
                                        </div>

                                        <span
                                            class="
                                                text-gray-600
                                                max-w-[160px]
                                                truncate
                                            "
                                        >
                                            {{ $order->waiter->name ?? '—' }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Items --}}
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">

                                    {{ $order->items->count() }}

                                    item{{ $order->items->count() === 1 ? '' : 's' }}

                                </td>


                                {{-- Total --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        font-medium
                                        text-gray-900
                                        whitespace-nowrap
                                    "
                                >

                                    {{ number_format($order->total_amount, 2) }}

                                    <span class="text-xs text-gray-400">
                                        RWF
                                    </span>

                                </td>


                                {{-- Date --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        text-gray-500
                                        whitespace-nowrap
                                    "
                                >

                                    {{ $order->created_at->diffForHumans() }}

                                </td>


                                {{-- Actions --}}
                                <td class="px-4 py-3">

                                    @if ($order->status === 'pending')

                                        <div
                                            x-data="{ rejecting: false }"
                                            class="
                                                relative
                                                flex
                                                items-center
                                                justify-end
                                                gap-2
                                            "
                                        >

                                            {{-- Approve --}}
                                            <form
                                                method="POST"
                                                action="{{ route('shops.orders.approve', [$shop, $order]) }}"
                                                class="
                                                    flex
                                                    items-center
                                                    gap-1
                                                "
                                            >

                                                @csrf

                                                <select
                                                    name="payment_method"
                                                    required
                                                    class="
                                                        text-xs
                                                        rounded-lg
                                                        border-gray-300
                                                        shadow-sm
                                                        focus:border-green-500
                                                        focus:ring-green-500
                                                        py-1.5
                                                    "
                                                >

                                                    <option value="">
                                                        Payment…
                                                    </option>

                                                    @foreach (
                                                        \App\Models\Order::PAYMENT_METHODS
                                                        as $value => $label
                                                    )

                                                        <option
                                                            value="{{ $value }}"
                                                            @selected($order->payment_method === $value)
                                                        >
                                                            {{ $label }}
                                                        </option>

                                                    @endforeach

                                                </select>


                                                <button
                                                    type="submit"
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        gap-1.5
                                                        bg-green-600
                                                        hover:bg-green-700
                                                        text-white
                                                        text-xs
                                                        font-medium
                                                        px-3
                                                        py-1.5
                                                        rounded-lg
                                                        whitespace-nowrap
                                                    "
                                                >

                                                    <i class="fa-solid fa-check"></i>

                                                    Approve

                                                </button>

                                            </form>


                                            {{-- Reject --}}
                                            <button
                                                type="button"
                                                @click="rejecting = !rejecting"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1.5
                                                    bg-red-50
                                                    hover:bg-red-100
                                                    text-red-700
                                                    text-xs
                                                    font-medium
                                                    px-3
                                                    py-1.5
                                                    rounded-lg
                                                    whitespace-nowrap
                                                "
                                            >

                                                <i class="fa-solid fa-xmark"></i>

                                                Reject

                                            </button>


                                            {{-- Reject popup --}}
                                            <div
                                                x-show="rejecting"
                                                x-cloak
                                                @click.outside="rejecting = false"
                                                class="
                                                    absolute
                                                    right-0
                                                    top-full
                                                    mt-2
                                                    bg-white
                                                    border
                                                    border-gray-200
                                                    rounded-xl
                                                    shadow-xl
                                                    p-4
                                                    w-72
                                                    z-50
                                                "
                                            >

                                                <form
                                                    method="POST"
                                                    action="{{ route('shops.orders.reject', [$shop, $order]) }}"
                                                >

                                                    @csrf

                                                    <div class="flex items-center justify-between mb-2">

                                                        <label
                                                            class="
                                                                text-xs
                                                                font-semibold
                                                                text-gray-700
                                                            "
                                                        >
                                                            Reason for rejection
                                                        </label>

                                                        <button
                                                            type="button"
                                                            @click="rejecting = false"
                                                            class="text-gray-400 hover:text-gray-600"
                                                        >
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>

                                                    </div>


                                                    <textarea
                                                        name="reason"
                                                        rows="3"
                                                        required
                                                        class="
                                                            w-full
                                                            text-xs
                                                            rounded-lg
                                                            border-gray-300
                                                            focus:border-red-500
                                                            focus:ring-red-500
                                                            mb-3
                                                        "
                                                        placeholder="e.g. item unavailable"
                                                    ></textarea>


                                                    <button
                                                        type="submit"
                                                        class="
                                                            w-full
                                                            inline-flex
                                                            items-center
                                                            justify-center
                                                            gap-2
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

                                                        <i class="fa-solid fa-xmark"></i>

                                                        Confirm Rejection

                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                    @else

                                        @php
                                            $badgeClasses = [
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                'cancelled' => 'bg-gray-100 text-gray-600',
                                            ];
                                        @endphp

                                        <div class="flex justify-end">

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-2.5
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-medium
                                                    whitespace-nowrap
                                                    {{ $badgeClasses[$order->status] ?? 'bg-gray-100 text-gray-600' }}
                                                "
                                            >

                                                {{ ucfirst($order->status) }}

                                            </span>

                                        </div>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="
                                        px-4
                                        py-12
                                        text-center
                                        text-gray-400
                                    "
                                >

                                    <div
                                        class="
                                            flex
                                            flex-col
                                            items-center
                                            gap-3
                                        "
                                    >

                                        <div
                                            class="
                                                h-12
                                                w-12
                                                rounded-full
                                                bg-gray-100
                                                flex
                                                items-center
                                                justify-center
                                            "
                                        >
                                            <i class="fa-solid fa-receipt text-gray-400"></i>
                                        </div>

                                        <div>

                                            <p class="font-medium text-gray-500">
                                                No {{ $status ?? 'pending' }} orders
                                            </p>

                                            <p class="text-xs text-gray-400 mt-1">
                                                Orders will appear here when available.
                                            </p>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ============================================================
                 PAGINATION
            ============================================================= --}}
            @if (
                $orders instanceof \Illuminate\Contracts\Pagination\Paginator
                || $orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
            )

                <div
                    class="
                        px-4
                        py-3
                        border-t
                        border-gray-100
                        overflow-x-auto
                    "
                >
                    {{ $orders->links() }}
                </div>

            @endif

        </div>

    </div>

</x-application-layout>