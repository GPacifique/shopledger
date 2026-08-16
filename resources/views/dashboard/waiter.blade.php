{{-- resources/views/orders/waiter.blade.php --}}

<x-application-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">
                    Take Order
                </h2>

                <p class="text-xs sm:text-sm text-gray-500">
                    {{ $shop->name }}
                </p>
            </div>

            <span class="text-xs sm:text-sm text-gray-500">
                Order Management
            </span>
        </div>
    </x-slot>


    <div
        class="w-full max-w-5xl mx-auto"
        x-data="orderForm({{ Js::from($products) }})"
    >

        {{-- ============================================================
             SUCCESS MESSAGE
        ============================================================= --}}
        @if (session('success'))
            <div
                class="mb-4 rounded-lg bg-green-50 border border-green-200
                       text-green-800 px-3 sm:px-4 py-3 text-sm"
                role="alert"
            >
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>

                    <span>
                        {{ session('success') }}
                    </span>
                </div>
            </div>
        @endif


        {{-- ============================================================
             VALIDATION ERRORS
        ============================================================= --}}
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
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>
        @endif


        {{-- ============================================================
             NEW ORDER FORM
        ============================================================= --}}
        <form
            method="POST"
            action="{{ route('shops.orders.store', $shop) }}"
            @submit="beforeSubmit($event)"
            class="
                bg-white
                rounded-xl
                border
                border-gray-200
                shadow-sm
                p-4
                sm:p-6
                mb-8
            "
        >

            @csrf


            {{-- ========================================================
                 ORDER INFORMATION
            ========================================================= --}}
            <div class="mb-6">

                <div class="flex items-center gap-2 mb-4">

                    <div
                        class="
                            h-8
                            w-8
                            rounded-lg
                            bg-blue-50
                            text-blue-600
                            flex
                            items-center
                            justify-center
                            shrink-0
                        "
                    >
                        <i class="fa-solid fa-receipt text-sm"></i>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            New Order
                        </h3>

                        <p class="text-xs text-gray-500">
                            Add products and submit the order for approval.
                        </p>
                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Customer --}}
                    <div>

                        <label
                            for="customer_id"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Customer
                            <span class="text-gray-400 font-normal">
                                (optional)
                            </span>
                        </label>

                        <select
                            id="customer_id"
                            name="customer_id"
                            class="
                                w-full
                                rounded-lg
                                border-gray-300
                                shadow-sm
                                text-sm
                                focus:border-blue-500
                                focus:ring-blue-500
                            "
                        >

                            <option value="">
                                Walk-in customer
                            </option>

                            @foreach ($customers ?? [] as $customer)

                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Payment --}}
                    <div>

                        <label
                            for="payment_method"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Payment Method
                            <span class="text-gray-400 font-normal">
                                (optional)
                            </span>
                        </label>

                        <select
                            id="payment_method"
                            name="payment_method"
                            class="
                                w-full
                                rounded-lg
                                border-gray-300
                                shadow-sm
                                text-sm
                                focus:border-blue-500
                                focus:ring-blue-500
                            "
                        >

                            <option value="">
                                Seller decides on approval
                            </option>

                            @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)

                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 ORDER ITEMS
            ========================================================= --}}
            <div class="mb-6">

                <div
                    class="
                        flex
                        flex-col
                        gap-2
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        mb-3
                    "
                >

                    <div>

                        <label class="block text-sm font-semibold text-gray-700">
                            Order Items
                        </label>

                        <p class="text-xs text-gray-400 mt-0.5">
                            Select products and enter quantities.
                        </p>

                    </div>


                    <button
                        type="button"
                        @click="addItem"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            gap-2
                            text-sm
                            text-blue-600
                            hover:text-blue-800
                            font-medium
                            border
                            border-blue-200
                            hover:border-blue-300
                            rounded-lg
                            px-3
                            py-2
                            bg-blue-50
                            hover:bg-blue-100
                            transition
                        "
                    >

                        <i class="fa-solid fa-plus"></i>

                        Add Item

                    </button>

                </div>


                {{-- Stock warning --}}
                <div
                    x-show="stockWarning"
                    x-cloak
                    class="
                        mb-3
                        rounded-lg
                        bg-amber-50
                        border
                        border-amber-200
                        text-amber-800
                        px-3
                        py-2.5
                        text-sm
                    "
                >

                    <div class="flex items-start gap-2">

                        <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>

                        <span x-text="stockWarning"></span>

                    </div>

                </div>


                {{-- Items --}}
                <div class="space-y-3">

                    <template
                        x-for="(item, index) in items"
                        :key="index"
                    >

                        <div
                            class="
                                bg-gray-50
                                border
                                border-gray-200
                                rounded-xl
                                p-3
                                sm:p-4
                            "
                        >

                            {{-- Product --}}
                            <div class="mb-3">

                                <label
                                    class="
                                        block
                                        text-xs
                                        font-medium
                                        text-gray-600
                                        mb-1
                                    "
                                >
                                    Product
                                </label>

                                <select
                                    :name="`items[${index}][product_id]`"
                                    x-model="item.product_id"
                                    @change="onProductChange(index)"
                                    class="
                                        w-full
                                        rounded-lg
                                        border-gray-300
                                        shadow-sm
                                        text-sm
                                        focus:border-blue-500
                                        focus:ring-blue-500
                                    "
                                >

                                    <option value="">
                                        Custom item
                                    </option>

                                    <template
                                        x-for="product in products"
                                        :key="product.id"
                                    >

                                        <option
                                            :value="product.id"
                                            x-text="product.name"
                                            :disabled="product.stock <= 0"
                                        ></option>

                                    </template>

                                </select>


                                {{-- Custom description --}}
                                <input
                                    type="text"
                                    x-show="!item.product_id"
                                    x-cloak
                                    :name="`items[${index}][description]`"
                                    x-model="item.description"
                                    placeholder="Custom item description"
                                    class="
                                        mt-2
                                        w-full
                                        rounded-lg
                                        border-gray-300
                                        shadow-sm
                                        text-sm
                                        focus:border-blue-500
                                        focus:ring-blue-500
                                    "
                                />

                            </div>


                            {{-- Quantity / Price / Total --}}
                            <div
                                class="
                                    grid
                                    grid-cols-1
                                    sm:grid-cols-3
                                    gap-3
                                "
                            >

                                {{-- Quantity --}}
                                <div>

                                    <label
                                        class="
                                            block
                                            text-xs
                                            font-medium
                                            text-gray-600
                                            mb-1
                                        "
                                    >
                                        Quantity
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        :name="`items[${index}][quantity]`"
                                        x-model.number="item.quantity"
                                        @input="checkStock(index)"
                                        class="
                                            w-full
                                            rounded-lg
                                            border-gray-300
                                            shadow-sm
                                            text-sm
                                            focus:border-blue-500
                                            focus:ring-blue-500
                                        "
                                    />

                                </div>


                                {{-- Unit price --}}
                                <div>

                                    <label
                                        class="
                                            block
                                            text-xs
                                            font-medium
                                            text-gray-600
                                            mb-1
                                        "
                                    >
                                        Unit Price
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        :name="`items[${index}][unit_price]`"
                                        x-model.number="item.unit_price"
                                        class="
                                            w-full
                                            rounded-lg
                                            border-gray-300
                                            shadow-sm
                                            text-sm
                                            focus:border-blue-500
                                            focus:ring-blue-500
                                        "
                                    />

                                </div>


                                {{-- Line total --}}
                                <div>

                                    <label
                                        class="
                                            block
                                            text-xs
                                            font-medium
                                            text-gray-600
                                            mb-1
                                        "
                                    >
                                        Total
                                    </label>

                                    <div
                                        class="
                                            w-full
                                            min-h-[42px]
                                            flex
                                            items-center
                                            justify-between
                                            rounded-lg
                                            bg-white
                                            border
                                            border-gray-200
                                            px-3
                                            text-sm
                                            font-semibold
                                            text-gray-900
                                        "
                                    >

                                        <span>
                                            RWF
                                        </span>

                                        <span
                                            x-text="lineTotal(item)"
                                        ></span>

                                    </div>

                                </div>

                            </div>


                            {{-- Remove item --}}
                            <div class="mt-3 flex justify-end">

                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    x-show="items.length > 1"
                                    class="
                                        inline-flex
                                        items-center
                                        gap-1.5
                                        text-xs
                                        text-red-500
                                        hover:text-red-700
                                        font-medium
                                    "
                                >

                                    <i class="fa-solid fa-trash-can"></i>

                                    Remove item

                                </button>

                            </div>

                        </div>

                    </template>

                </div>

            </div>


            {{-- ========================================================
                 NOTES
            ========================================================= --}}
            <div class="mb-6">

                <label
                    for="notes"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Notes
                    <span class="text-gray-400 font-normal">
                        (optional)
                    </span>
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="3"
                    class="
                        w-full
                        rounded-lg
                        border-gray-300
                        shadow-sm
                        text-sm
                        focus:border-blue-500
                        focus:ring-blue-500
                    "
                    placeholder="e.g. table number, special request"
                ></textarea>

            </div>


            {{-- ========================================================
                 ORDER SUMMARY
            ========================================================= --}}
            <div
                class="
                    border-t
                    border-gray-100
                    pt-4
                "
            >

                <div
                    class="
                        flex
                        flex-col
                        gap-4
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >

                    <div>

                        <p class="text-xs text-gray-500">
                            Order Subtotal
                        </p>

                        <p
                            class="
                                text-xl
                                sm:text-2xl
                                font-bold
                                text-gray-900
                            "
                        >

                            <span
                                x-text="formatMoney(subtotal())"
                            ></span>

                            <span class="text-sm font-medium text-gray-400">
                                RWF
                            </span>

                        </p>

                    </div>


                    <button
                        type="submit"
                        class="
                            w-full
                            sm:w-auto
                            inline-flex
                            items-center
                            justify-center
                            gap-2
                            bg-blue-600
                            hover:bg-blue-700
                            active:bg-blue-800
                            text-white
                            text-sm
                            font-semibold
                            px-5
                            py-3
                            rounded-lg
                            shadow-sm
                            transition
                        "
                    >

                        <i class="fa-solid fa-paper-plane"></i>

                        Submit Order

                    </button>

                </div>

            </div>

        </form>


        {{-- ============================================================
             MY ORDERS
        ============================================================= --}}
        <div class="mb-3">

            <div class="flex items-center gap-2">

                <div
                    class="
                        h-8
                        w-8
                        rounded-lg
                        bg-gray-100
                        text-gray-600
                        flex
                        items-center
                        justify-center
                    "
                >
                    <i class="fa-solid fa-list-check text-sm"></i>
                </div>

                <div>

                    <h2 class="text-lg font-semibold text-gray-800">
                        My Orders
                    </h2>

                    <p class="text-xs text-gray-400">
                        Orders you have submitted
                    </p>

                </div>

            </div>

        </div>


        {{-- ============================================================
             ORDERS TABLE
        ============================================================= --}}
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

            {{-- Horizontal scroll on small devices --}}
            <div class="overflow-x-auto">

                <table
                    class="
                        min-w-[700px]
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
                                Status
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

                            <th class="px-4 py-3"></th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($orders as $order)

                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Order --}}
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


                                {{-- Status --}}
                                <td class="px-4 py-3">

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


                                    @if (
                                        $order->status === 'rejected'
                                        && $order->rejection_reason
                                    )

                                        <div
                                            class="
                                                mt-1
                                                text-xs
                                                text-red-600
                                                max-w-[250px]
                                            "
                                        >
                                            {{ $order->rejection_reason }}
                                        </div>

                                    @endif

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


                                {{-- Cancel --}}
                                <td class="px-4 py-3 text-right">

                                    @if ($order->canBeCancelled())

                                        <form
                                            method="POST"
                                            action="{{ route('shops.orders.cancel', [$shop, $order]) }}"
                                            onsubmit="return confirm('Cancel this order?');"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1.5
                                                    text-xs
                                                    text-red-500
                                                    hover:text-red-700
                                                    font-medium
                                                "
                                            >

                                                <i class="fa-solid fa-ban"></i>

                                                Cancel

                                            </button>

                                        </form>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
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
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>

                                        <div>

                                            <p class="font-medium text-gray-500">
                                                No orders submitted yet
                                            </p>

                                            <p class="text-xs text-gray-400 mt-1">
                                                Your submitted orders will appear here.
                                            </p>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if (
                $orders instanceof \Illuminate\Contracts\Pagination\Paginator
                || method_exists($orders, 'links')
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


    {{-- ================================================================
         ALPINE ORDER FORM
    ================================================================= --}}
    <script>
        function orderForm(products) {
            return {

                products: products,

                items: [
                    {
                        product_id: '',
                        description: '',
                        quantity: 1,
                        unit_price: 0
                    }
                ],

                stockWarning: '',


                addItem() {

                    this.items.push({
                        product_id: '',
                        description: '',
                        quantity: 1,
                        unit_price: 0
                    });

                },


                removeItem(index) {

                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }

                },


                onProductChange(index) {

                    const item = this.items[index];

                    const product = this.products.find(
                        p => p.id == item.product_id
                    );


                    if (product) {

                        item.unit_price =
                            parseFloat(product.selling_price) || 0;

                        item.description = '';

                        this.checkStock(index);

                    } else {

                        this.stockWarning = '';

                    }

                },


                checkStock(index) {

                    const item = this.items[index];

                    const product = this.products.find(
                        p => p.id == item.product_id
                    );


                    if (!product) {
                        this.stockWarning = '';
                        return;
                    }


                    const quantity =
                        parseFloat(item.quantity) || 0;


                    const stock =
                        parseFloat(product.stock) || 0;


                    if (stock <= 0) {

                        this.stockWarning =
                            `${product.name} is out of stock.`;

                    } else if (quantity > stock) {

                        this.stockWarning =
                            `Only ${stock} of ${product.name} available.`;

                    } else {

                        this.stockWarning = '';

                    }

                },


                lineTotal(item) {

                    const quantity =
                        parseFloat(item.quantity) || 0;

                    const price =
                        parseFloat(item.unit_price) || 0;

                    return this.formatMoney(
                        quantity * price
                    );

                },


                subtotal() {

                    return this.items.reduce(
                        (sum, item) => {

                            const quantity =
                                parseFloat(item.quantity) || 0;

                            const price =
                                parseFloat(item.unit_price) || 0;

                            return sum + (quantity * price);

                        },
                        0
                    );

                },


                formatMoney(value) {

                    return Number(value || 0).toLocaleString(
                        undefined,
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    );

                },


                beforeSubmit(event) {

                    for (const item of this.items) {

                        const product =
                            this.products.find(
                                p => p.id == item.product_id
                            );


                        if (!product) {
                            continue;
                        }


                        const quantity =
                            parseFloat(item.quantity) || 0;

                        const stock =
                            parseFloat(product.stock) || 0;


                        if (quantity <= 0) {

                            event.preventDefault();

                            alert(
                                'Please enter a valid quantity.'
                            );

                            return;

                        }


                        if (quantity > stock) {

                            event.preventDefault();

                            alert(
                                `Only ${stock} of ${product.name} available.`
                            );

                            return;

                        }

                    }

                }

            };
        }
    </script>


    {{-- Prevent Alpine x-cloak elements from flashing --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</x-application-layout>