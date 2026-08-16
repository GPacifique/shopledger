{{-- resources/views/orders/waiter.blade.php --}}

<x-application-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">
                Take Order
            </h2>

            <span class="text-sm text-gray-500 break-anywhere">
                {{ $shop->name }}
            </span>
        </div>
    </x-slot>


    <div
        class="w-full max-w-5xl mx-auto"
        x-data="orderForm({{ Js::from($products) }})"
    >

        {{-- Success message --}}
        @if (session('success'))
            <div
                class="mb-4 rounded-lg border border-green-200 bg-green-50
                       px-3 sm:px-4 py-3 text-sm text-green-800"
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


        {{-- Validation errors --}}
        @if ($errors->any())
            <div
                class="mb-4 rounded-lg border border-red-200 bg-red-50
                       px-3 sm:px-4 py-3 text-sm text-red-800"
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
             NEW ORDER
        ================================================================= --}}
        <form
            method="POST"
            action="{{ route('shops.orders.store', $shop) }}"
            @submit="beforeSubmit"
            class="bg-white rounded-xl border border-gray-200 shadow-sm
                   p-4 sm:p-6 mb-6 sm:mb-8"
        >

            @csrf


            {{-- Customer / Payment --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                <div>
                    <label
                        for="customer_id"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Customer
                        <span class="text-gray-400 font-normal">(optional)</span>
                    </label>

                    <select
                        id="customer_id"
                        name="customer_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm
                               text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Walk-in</option>

                        @foreach ($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div>
                    <label
                        for="payment_method"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Payment method
                        <span class="text-gray-400 font-normal">(optional)</span>
                    </label>

                    <select
                        id="payment_method"
                        name="payment_method"
                        class="w-full rounded-lg border-gray-300 shadow-sm
                               text-sm focus:border-blue-500 focus:ring-blue-500"
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


            {{-- ============================================================
                 ORDER ITEMS
            ============================================================= --}}
            <div class="mb-6">

                <div class="flex items-center justify-between gap-3 mb-3">

                    <label class="block text-sm font-medium text-gray-700">
                        Order Items
                    </label>

                    <button
                        type="button"
                        @click="addItem"
                        class="inline-flex items-center gap-1.5
                               text-sm text-blue-600 hover:text-blue-800
                               font-medium whitespace-nowrap"
                    >
                        <i class="fa-solid fa-plus text-xs"></i>
                        Add item
                    </button>

                </div>


                {{-- Items --}}
                <div class="space-y-3">

                    <template
                        x-for="(item, index) in items"
                        :key="index"
                    >

                        <div
                            class="bg-gray-50 rounded-lg border border-gray-200
                                   p-3 sm:p-4"
                        >

                            {{-- Product --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">

                                <div class="md:col-span-5">

                                    <label
                                        class="block text-xs font-medium
                                               text-gray-600 mb-1"
                                    >
                                        Product
                                    </label>

                                    <select
                                        :name="`items[${index}][product_id]`"
                                        x-model="item.product_id"
                                        @change="onProductChange(index)"
                                        class="w-full rounded-lg border-gray-300
                                               shadow-sm text-sm
                                               focus:border-blue-500
                                               focus:ring-blue-500"
                                    >

                                        <option value="">
                                            Custom item…
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
                                        :name="`items[${index}][description]`"
                                        x-model="item.description"
                                        placeholder="Custom item description"
                                        class="mt-2 w-full rounded-lg
                                               border-gray-300 shadow-sm text-sm
                                               focus:border-blue-500
                                               focus:ring-blue-500"
                                    >

                                </div>


                                {{-- Quantity --}}
                                <div class="md:col-span-2">

                                    <label
                                        class="block text-xs font-medium
                                               text-gray-600 mb-1"
                                    >
                                        Quantity
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        :name="`items[${index}][quantity]`"
                                        x-model.number="item.quantity"
                                        placeholder="Qty"
                                        class="w-full rounded-lg
                                               border-gray-300 shadow-sm text-sm
                                               focus:border-blue-500
                                               focus:ring-blue-500"
                                    >

                                </div>


                                {{-- Unit price --}}
                                <div class="md:col-span-2">

                                    <label
                                        class="block text-xs font-medium
                                               text-gray-600 mb-1"
                                    >
                                        Unit Price
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        :name="`items[${index}][unit_price]`"
                                        x-model.number="item.unit_price"
                                        placeholder="Price"
                                        class="w-full rounded-lg
                                               border-gray-300 shadow-sm text-sm
                                               focus:border-blue-500
                                               focus:ring-blue-500"
                                    >

                                </div>


                                {{-- Total --}}
                                <div class="md:col-span-2">

                                    <label
                                        class="block text-xs font-medium
                                               text-gray-600 mb-1"
                                    >
                                        Total
                                    </label>

                                    <div
                                        class="w-full min-h-[42px] flex items-center
                                               rounded-lg bg-white border
                                               border-gray-200 px-3
                                               text-sm font-medium text-gray-800"
                                        x-text="lineTotal(item)"
                                    ></div>

                                </div>


                                {{-- Remove --}}
                                <div
                                    class="md:col-span-1 flex items-end
                                           justify-end md:justify-center"
                                >

                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        x-show="items.length > 1"
                                        class="inline-flex items-center justify-center
                                               h-10 w-10 rounded-lg
                                               text-red-500 hover:text-red-700
                                               hover:bg-red-50"
                                        title="Remove item"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                </div>

                            </div>

                        </div>

                    </template>

                </div>


                {{-- Stock warning --}}
                <div
                    x-show="stockWarning"
                    x-text="stockWarning"
                    class="mt-2 text-xs text-red-600"
                ></div>

            </div>


            {{-- Notes --}}
            <div class="mb-6">

                <label
                    for="notes"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Notes
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm
                           text-sm focus:border-blue-500
                           focus:ring-blue-500"
                    placeholder="e.g. table number, special request"
                ></textarea>

            </div>


            {{-- Order summary --}}
            <div
                class="border-t border-gray-200 pt-4
                       flex flex-col gap-4
                       sm:flex-row sm:items-center
                       sm:justify-between"
            >

                <div class="text-sm text-gray-600">

                    Subtotal:

                    <span
                        class="font-semibold text-gray-900 ml-1"
                        x-text="formatMoney(subtotal())"
                    ></span>

                    <span class="text-gray-400 ml-1">
                        RWF
                    </span>

                </div>


                <button
                    type="submit"
                    class="w-full sm:w-auto
                           inline-flex items-center justify-center gap-2
                           bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-medium
                           px-5 py-2.5 rounded-lg
                           transition-colors"
                >

                    <i class="fa-solid fa-paper-plane"></i>

                    Submit Order

                </button>

            </div>

        </form>


        {{-- ================================================================
             MY ORDERS
        ================================================================= --}}
        <div class="flex items-center justify-between gap-3 mb-3">

            <h2 class="text-lg font-semibold text-gray-800">
                My Orders
            </h2>

            <span class="text-xs sm:text-sm text-gray-500">
               {{ $orders->count() }} orders
            </span>

        </div>


        {{-- Responsive table --}}
        <div
            class="bg-white rounded-xl border border-gray-200
                   shadow-sm overflow-hidden"
        >

            <div class="overflow-x-auto">

                <table class="min-w-[700px] w-full divide-y divide-gray-200 text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-4 py-3 text-left font-medium text-gray-500">
                                Order #
                            </th>

                            <th class="px-4 py-3 text-left font-medium text-gray-500">
                                Total
                            </th>

                            <th class="px-4 py-3 text-left font-medium text-gray-500">
                                Status
                            </th>

                            <th class="px-4 py-3 text-left font-medium text-gray-500">
                                Submitted
                            </th>

                            <th class="px-4 py-3 text-right font-medium text-gray-500">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($orders as $order)

                            <tr class="hover:bg-gray-50">

                                <td class="px-4 py-3 whitespace-nowrap">

                                    <a
                                        href="{{ route('shops.orders.show', [$shop, $order]) }}"
                                        class="font-medium text-blue-600 hover:text-blue-800 hover:underline"
                                    >
                                        {{ $order->order_number }}
                                    </a>

                                </td>


                                <td class="px-4 py-3 whitespace-nowrap">

                                    <span class="font-medium text-gray-900">
                                        {{ number_format($order->total_amount, 2) }}
                                    </span>

                                    <span class="text-xs text-gray-400 ml-1">
                                        RWF
                                    </span>

                                </td>


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
                                            inline-flex items-center
                                            px-2 py-1
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
                                            class="text-xs text-gray-500 mt-1
                                                   max-w-xs break-anywhere"
                                        >
                                            {{ $order->rejection_reason }}
                                        </div>

                                    @endif

                                </td>


                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">

                                    {{ $order->created_at->diffForHumans() }}

                                </td>


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
                                                class="inline-flex items-center gap-1
                                                       text-xs font-medium
                                                       text-red-500
                                                       hover:text-red-700
                                                       px-2 py-1"
                                            >
                                                <i class="fa-solid fa-xmark"></i>
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
                                    class="px-4 py-10 text-center text-gray-400"
                                >

                                    <div class="flex flex-col items-center gap-2">

                                        <i class="fa-solid fa-receipt text-2xl"></i>

                                        <span>
                                            No orders submitted yet.
                                        </span>

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
                || $orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
            )

                <div class="px-4 py-3 border-t border-gray-100 overflow-x-auto">
                    {{ $orders->links() }}
                </div>

            @endif

        </div>

    </div>


    {{-- ================================================================
         ALPINE ORDER FORM
    ================================================================= --}}
    @push('scripts')

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

                        if (!product) {
                            item.unit_price = 0;
                            this.stockWarning = '';
                            return;
                        }

                        item.unit_price =
                            parseFloat(product.selling_price) || 0;

                        item.description = '';

                        if (product.stock <= 0) {

                            this.stockWarning =
                                `${product.name} is out of stock.`;

                        } else if (item.quantity > product.stock) {

                            this.stockWarning =
                                `Only ${product.stock} of ${product.name} in stock.`;

                        } else {

                            this.stockWarning = '';

                        }
                    },

                    lineTotal(item) {
                        const quantity =
                            parseFloat(item.quantity) || 0;

                        const price =
                            parseFloat(item.unit_price) || 0;

                        return this.formatMoney(quantity * price);
                    },

                    subtotal() {
                        return this.items.reduce((sum, item) => {

                            const quantity =
                                parseFloat(item.quantity) || 0;

                            const price =
                                parseFloat(item.unit_price) || 0;

                            return sum + (quantity * price);

                        }, 0);
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

                            if (!item.product_id) {
                                continue;
                            }

                            const product = this.products.find(
                                p => p.id == item.product_id
                            );

                            if (!product) {
                                continue;
                            }

                            if (item.quantity > product.stock) {

                                event.preventDefault();

                                alert(
                                    `Only ${product.stock} of ${product.name} available.`
                                );

                                return;
                            }
                        }
                    }
                };
            }
        </script>

    @endpush

</x-application-layout>