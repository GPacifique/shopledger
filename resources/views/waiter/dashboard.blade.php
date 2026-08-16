<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Waiter Dashboard') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $shop->business_name }}
                </p>
            </div>

            <a href="{{ route('shops.orders.waiter', $shop) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent
                      rounded-md font-semibold text-xs text-white uppercase tracking-widest
                      hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900">
                {{ __('Take New Order') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">My Orders</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ $orders }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-5">
                    <p class="text-sm text-yellow-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-700 mt-1">
                        {{ $pendingOrders }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-5">
                    <p class="text-sm text-blue-600">Approved</p>
                    <p class="text-2xl font-bold text-blue-700 mt-1">
                        {{ $approvedOrders }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-green-200 p-5">
                    <p class="text-sm text-green-600">Completed</p>
                    <p class="text-2xl font-bold text-green-700 mt-1">
                        {{ $completedOrders }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-red-200 p-5">
                    <p class="text-sm text-red-600">Cancelled</p>
                    <p class="text-2xl font-bold text-red-700 mt-1">
                        {{ $cancelledOrders }}
                    </p>
                </div>

            </div>

            {{-- Main Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Take Order --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-4">

                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-indigo-100
                                    flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Take a New Order
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Create a customer order and send it to the seller
                                for approval.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('shops.orders.waiter', $shop) }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2
                                  bg-indigo-600 text-white rounded-md font-medium
                                  hover:bg-indigo-700">
                            Take Order
                        </a>
                    </div>
                </div>

                {{-- My Orders --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-4">

                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gray-100
                                    flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                         M9 5a3 3 0 006 0
                                         M9 12h6
                                         M9 16h6"/>
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">
                                My Orders
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                View orders that you have created.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('shops.orders.index', $shop) }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2
                                  bg-gray-800 text-white rounded-md font-medium
                                  hover:bg-gray-900">
                            View My Orders
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>