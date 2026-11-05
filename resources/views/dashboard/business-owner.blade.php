<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">{{ __('Business intelligence') }}</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ __('Business owner dashboard') }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $selectedShop?->business_name ?? __('All shops') }}
                    · {{ $startDate->format('M j, Y') }} – {{ $endDate->format('M j, Y') }}
                </p>
            </div>
            <a href="#add-shop" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Add a shop') }}
            </a>
        </div>
    </x-slot>

    @php
        $money = fn ($value) => number_format((float) $value, 2);
        $periodLabels = [
            'today' => __('Today'),
            'yesterday' => __('Yesterday'),
            'this_week' => __('This week'),
            'this_month' => __('This month'),
            'last_month' => __('Last month'),
            'this_year' => __('This year'),
            'custom' => __('Custom date range'),
        ];
        $kpis = [
            ['label' => __('Total shops'), 'value' => number_format($shops->count()), 'note' => __('Across your portfolio'), 'color' => 'indigo'],
            ['label' => __('Sales in range'), 'value' => $money($salesTotal), 'note' => $salesGrowth === null ? __('No previous-period baseline') : sprintf('%s%% %s', number_format(abs($salesGrowth), 1), $salesGrowth >= 0 ? __('up vs previous period') : __('down vs previous period')), 'color' => 'emerald'],
            ['label' => __('Total income'), 'value' => $money($salesTotal + $otherIncomeTotal), 'note' => __('Sales plus other recorded income'), 'color' => 'teal'],
            ['label' => __('Sales today'), 'value' => $money($salesToday), 'note' => __('All selected shops'), 'color' => 'blue'],
            ['label' => __('Sales this month'), 'value' => $money($salesThisMonth), 'note' => __('Calendar month to date'), 'color' => 'cyan'],
            ['label' => __('Total purchases'), 'value' => $money($purchaseTotal), 'note' => __('Selected date range'), 'color' => 'violet'],
            ['label' => __('Total expenses'), 'value' => $money($expenseTotal), 'note' => __('Selected date range'), 'color' => 'rose'],
            ['label' => __('Gross profit'), 'value' => $money($grossProfit), 'note' => __('Sales less recorded cost of goods'), 'color' => 'teal'],
            ['label' => __('Net profit'), 'value' => $money($netProfit), 'note' => __('Gross profit less expenses, plus other income'), 'color' => 'green'],
            ['label' => __('Total products'), 'value' => number_format($productCount), 'note' => __('Current inventory'), 'color' => 'sky'],
            ['label' => __('Low-stock products'), 'value' => number_format($lowStockTotal), 'note' => __('At or below reorder level'), 'color' => 'amber'],
            ['label' => __('Out-of-stock products'), 'value' => number_format($outOfStockTotal), 'note' => __('No stock available'), 'color' => 'orange'],
            ['label' => __('Total customers'), 'value' => number_format($customerCount), 'note' => sprintf('%s %s · %s %s', number_format($newCustomers), __('new'), number_format($returningCustomers), __('returning in range')), 'color' => 'fuchsia'],
            ['label' => __('Total employees'), 'value' => number_format($totalEmployees), 'note' => __('Assigned to selected shops'), 'color' => 'purple'],
            ['label' => __('Total suppliers'), 'value' => number_format($supplierCount), 'note' => __('Assigned to selected shops'), 'color' => 'sky'],
            ['label' => __('Outstanding receivables'), 'value' => $money($unpaidSales), 'note' => __('Unpaid sales only; partial balances are not stored'), 'color' => 'yellow'],
            ['label' => __('Outstanding payables'), 'value' => $money($unpaidExpenses), 'note' => __('Unpaid expenses; supplier purchase balances are not stored'), 'color' => 'red'],
        ];
        $toneClasses = [
            'indigo' => 'bg-indigo-50 text-indigo-700',
            'emerald' => 'bg-emerald-50 text-emerald-700',
            'blue' => 'bg-blue-50 text-blue-700',
            'cyan' => 'bg-cyan-50 text-cyan-700',
            'violet' => 'bg-violet-50 text-violet-700',
            'rose' => 'bg-rose-50 text-rose-700',
            'teal' => 'bg-teal-50 text-teal-700',
            'green' => 'bg-green-50 text-green-700',
            'sky' => 'bg-sky-50 text-sky-700',
            'amber' => 'bg-amber-50 text-amber-700',
            'orange' => 'bg-orange-50 text-orange-700',
            'fuchsia' => 'bg-fuchsia-50 text-fuchsia-700',
            'purple' => 'bg-purple-50 text-purple-700',
            'yellow' => 'bg-yellow-50 text-yellow-700',
            'red' => 'bg-red-50 text-red-700',
        ];
        $activityDots = [
            'indigo' => 'bg-indigo-500',
            'blue' => 'bg-blue-500',
            'rose' => 'bg-rose-500',
            'green' => 'bg-green-500',
            'amber' => 'bg-amber-500',
            'violet' => 'bg-violet-500',
        ];
        $kpiIcons = [
            'M3 10h18v10H3zM5 10l2-6h10l2 6M8 14h.01M12 14h.01M16 14h.01',
            'M12 2v20m5-16H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6',
            'M12 2v20m5-16H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6',
            'M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            'M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z',
            'M3 3h2l2 12h11l3-8H6m2 12a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z',
            'M4 4h16v16H4zM8 8h8m-8 4h8m-8 4h5',
            'M3 17l6-6 4 3 8-9M15 5h6v6',
            'M4 12h16M12 4v16m7-16H9a3 3 0 0 0 0 6h6a3 3 0 0 1 0 6H5',
            'M4 5h16v15H4zM8 5V3h8v2M8 9h8m-8 4h8m-8 4h5',
            'M12 3 2.8 20h18.4L12 3Zm0 6v5m0 3h.01',
            'M4 4h16v16H4zM8 8h8m-8 4h8m-8 4h4',
            'M16 20v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m6-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-7a4 4 0 0 1 0 8m2 3a4 4 0 0 1 2 4v1',
            'M16 20v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m6-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
            'M3 7h13v11H3zM16 11h3l3 3v4h-6m-9 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm11 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z',
            'M12 19V5m-6 6 6-6 6 6',
            'M12 5v14m6-6-6 6-6-6',
        ];
        $bestShopId = $shopPerformance->sortByDesc('sales')->first()['shop']->id ?? null;
        $chartLabels = $chartRows->pluck('date')->values();
    @endphp

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-[1600px] space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ session('error') }}</div>
            @endif

            <section aria-label="{{ __('Dashboard filters') }}" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form id="dashboard-filters" action="{{ route('business.dashboard') }}" method="GET" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5 xl:items-end">
                    <div>
                        <label for="shop-filter" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Shop') }}</label>
                        <select id="shop-filter" name="shop" class="mt-1.5 block w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                            <option value="all" @selected($selectedShopId === null)>{{ __('All shops') }}</option>
                            @foreach ($shops as $shop)
                                <option value="{{ $shop->id }}" @selected($selectedShopId === $shop->id)>{{ $shop->business_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="period-filter" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Date range') }}</label>
                        <select id="period-filter" name="period" class="mt-1.5 block w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                            @foreach ($periodLabels as $key => $label)
                                <option value="{{ $key }}" @selected($period === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="date-range-field {{ $period === 'custom' ? '' : 'hidden' }}">
                        <label for="start-date" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Start date') }}</label>
                        <input id="start-date" type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="mt-1.5 block w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    </div>
                    <div class="date-range-field {{ $period === 'custom' ? '' : 'hidden' }}">
                        <label for="end-date" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('End date') }}</label>
                        <input id="end-date" type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="mt-1.5 block w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    </div>
                    <div class="flex gap-2">
                        <button id="apply-filters" type="submit" class="inline-flex min-h-[42px] flex-1 items-center justify-center rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                            {{ __('Apply filters') }}
                        </button>
                        <a href="{{ route('business.dashboard') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-xl border border-gray-200 px-3 text-sm font-medium text-gray-600 hover:bg-gray-50" aria-label="{{ __('Clear filters') }}">×</a>
                    </div>
                </form>
                @foreach ($errors->all() as $error)
                    <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
                @endforeach
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 border-t border-gray-100 pt-4 text-xs text-gray-500">
                    <span>{{ __('Sales this week') }}: <strong class="text-gray-800">{{ $money($salesThisWeek) }}</strong></span>
                    <span>{{ __('Sales this year') }}: <strong class="text-gray-800">{{ $money($salesThisYear) }}</strong></span>
                    <span>{{ __('Purchases this month') }}: <strong class="text-gray-800">{{ $money($purchasesThisMonth) }}</strong></span>
                    <span>{{ __('Transactions') }}: <strong class="text-gray-800">{{ number_format($transactionCount) }}</strong></span>
                    <span>{{ __('Average transaction') }}: <strong class="text-gray-800">{{ $money($averageTransaction) }}</strong></span>
                    <span>{{ __('Inventory value') }}: <strong class="text-gray-800">{{ $money($inventoryValue) }}</strong></span>
                    <span>{{ __('Stock quantity') }}: <strong class="text-gray-800">{{ number_format($stockQuantity, 2) }}</strong></span>
                </div>
            </section>

            <section aria-label="{{ __('Key performance indicators') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5">
                @foreach ($kpis as $kpi)
                    <article class="min-w-0 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $kpi['label'] }}</p>
                                <p class="mt-2 truncate text-xl font-bold text-gray-900 sm:text-2xl">{{ $kpi['value'] }}</p>
                            </div>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $toneClasses[$kpi['color']] }}" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $kpiIcons[$loop->index] }}"/></svg>
                            </span>
                        </div>
                        <p class="mt-2 text-xs leading-5 text-gray-500">{{ $kpi['note'] }}</p>
                    </article>
                @endforeach
            </section>

            <section aria-label="{{ __('Sales analytics') }}" class="space-y-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Sales analytics') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('Sales trends, transaction mix, products, locations, and cashiers.') }}</p>
                </div>
                <div class="grid gap-4 xl:grid-cols-2">
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Sales trend') }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ $periodLabels[$period] }} · {{ __('sales and transaction volume') }}</p>
                        <div class="mt-4 h-64"><canvas id="sales-trend" role="img" aria-label="{{ __('Sales trend chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Sales by payment method') }}</h3>
                        <div class="mt-4 h-64"><canvas id="payment-methods" role="img" aria-label="{{ __('Sales by payment method chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Sales by shop') }}</h3>
                        <div class="mt-4 h-64"><canvas id="shop-sales" role="img" aria-label="{{ __('Sales by shop chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Top-selling products') }}</h3>
                        <div class="mt-4 h-64"><canvas id="top-products" role="img" aria-label="{{ __('Top-selling products chart') }}"></canvas></div>
                    </article>
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Sales by cashier') }}</h3>
                        @forelse ($salesByCashier as $cashier)
                            <div class="mt-3 flex items-center justify-between gap-4 border-b border-gray-100 pb-2 text-sm last:border-0">
                                <span class="truncate text-gray-700">{{ $cashier->cashier }}</span>
                                <span class="shrink-0 font-semibold text-gray-900">{{ $money($cashier->total) }} <span class="font-normal text-gray-500">· {{ number_format($cashier->transactions) }} {{ __('sales') }}</span></span>
                            </div>
                        @empty
                            <p class="mt-4 text-sm text-gray-500">{{ __('No cashier sales in this date range.') }}</p>
                        @endforelse
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Top customers by spending') }}</h3>
                        @forelse ($topCustomers as $customer)
                            <div class="mt-3 flex items-center justify-between gap-4 border-b border-gray-100 pb-2 text-sm last:border-0">
                                <span class="truncate text-gray-700">{{ $customer->name }}</span>
                                <span class="shrink-0 font-semibold text-gray-900">{{ $money($customer->total) }} <span class="font-normal text-gray-500">· {{ number_format($customer->transactions) }} {{ __('sales') }}</span></span>
                            </div>
                        @empty
                            <p class="mt-4 text-sm text-gray-500">{{ __('No customer sales in this date range.') }}</p>
                        @endforelse
                    </article>
                </div>
            </section>

            <section aria-label="{{ __('Financial analytics') }}" class="space-y-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Financial analytics') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('Profit follows the existing report calculation: sales less recorded cost of goods and expenses, plus other income.') }}</p>
                </div>
                <div class="grid gap-4 xl:grid-cols-2">
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Income versus expenses') }}</h3>
                        <div class="mt-4 h-64"><canvas id="income-expenses" role="img" aria-label="{{ __('Income versus expenses chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Profit trend') }}</h3>
                        <div class="mt-4 h-64"><canvas id="profit-trend" role="img" aria-label="{{ __('Profit trend chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Expenses by category') }}</h3>
                        <div class="mt-4 h-64"><canvas id="expense-categories" role="img" aria-label="{{ __('Expenses by category chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Cash flow overview') }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Recorded sales and other income compared with purchases and expenses; payment settlement is not recorded for every transaction.') }}</p>
                        <div class="mt-4 h-64"><canvas id="cash-flow" role="img" aria-label="{{ __('Cash flow overview chart') }}"></canvas></div>
                    </article>
                </div>
            </section>

            <section aria-label="{{ __('Inventory analytics') }}" class="space-y-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Inventory analytics') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('Current stock health and movement during the selected date range.') }}</p>
                </div>
                <div class="grid gap-4 xl:grid-cols-2">
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Inventory value by shop') }}</h3>
                        <div class="mt-4 h-64"><canvas id="inventory-value" role="img" aria-label="{{ __('Inventory value by shop chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Stock movement trend') }}</h3>
                        <div class="mt-4 h-64"><canvas id="stock-movement" role="img" aria-label="{{ __('Stock movement trend chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Stock distribution by shop') }}</h3>
                        <div class="mt-4 h-64"><canvas id="stock-distribution" role="img" aria-label="{{ __('Stock distribution chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Stock health') }}</h3>
                        <div class="mt-4 h-64"><canvas id="stock-health" role="img" aria-label="{{ __('Stock health chart') }}"></canvas></div>
                    </article>
                    <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <div class="border-b border-gray-100 p-4 sm:p-5">
                            <h3 class="font-semibold text-gray-900">{{ __('Low-stock products') }}</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Current stock at or below the product reorder level.') }}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                    <tr><th class="px-4 py-3">{{ __('Product') }}</th><th class="px-4 py-3">{{ __('Shop') }}</th><th class="px-4 py-3">{{ __('Current quantity') }}</th><th class="px-4 py-3">{{ __('Reorder level') }}</th></tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($lowStockProducts as $product)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $product->shop?->business_name }}</td>
                                            <td class="px-4 py-3 font-semibold text-amber-700">{{ number_format((float) $product->stock, 2) }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ number_format((float) $product->minimum_stock, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('No low-stock products to show.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                </div>
                <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                    <h3 class="font-semibold text-gray-900">{{ __('Slow-moving products') }}</h3>
                    <p class="mt-1 text-xs text-gray-500">{{ __('In-stock products with no recorded sale in the selected date range.') }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($slowMovingProducts as $product)
                            <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs text-gray-700">{{ $product->name }} · {{ $product->shop?->business_name }} ({{ number_format((float) $product->stock, 2) }})</span>
                        @empty
                            <p class="text-sm text-gray-500">{{ __('No slow-moving products found.') }}</p>
                        @endforelse
                    </div>
                </article>
            </section>

            <section aria-label="{{ __('Purchases analytics') }}" class="space-y-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Purchases analytics') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('Purchase trends and recent supplier activity.') }}</p>
                </div>
                <div class="grid gap-4 xl:grid-cols-2">
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Purchase trend') }}</h3>
                        <div class="mt-4 h-64"><canvas id="purchase-trend" role="img" aria-label="{{ __('Purchase trend chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Purchases by shop') }}</h3>
                        <div class="mt-4 h-64"><canvas id="purchase-shops" role="img" aria-label="{{ __('Purchases by shop chart') }}"></canvas></div>
                    </article>
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Purchases by supplier') }}</h3>
                        <div class="mt-4 h-64"><canvas id="purchase-suppliers" role="img" aria-label="{{ __('Purchases by supplier chart') }}"></canvas></div>
                    </article>
                </div>
                <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-4 sm:p-5">
                        <h3 class="font-semibold text-gray-900">{{ __('Recent purchases') }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                <tr><th class="px-4 py-3">{{ __('Date') }}</th><th class="px-4 py-3">{{ __('Shop') }}</th><th class="px-4 py-3">{{ __('Supplier') }}</th><th class="px-4 py-3">{{ __('Recorded by') }}</th><th class="px-4 py-3 text-right">{{ __('Total') }}</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($recentPurchases as $purchase)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $purchase->purchase_date?->format('M j, Y') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $purchase->shop?->business_name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $purchase->supplier?->name ?? __('Unknown') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $purchase->creator?->name ?? __('Unknown') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-900">{{ $money($purchase->total_amount) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('No purchases in this date range.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

            <section aria-label="{{ __('Shop performance comparison') }}" class="space-y-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Shop performance comparison') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('Performance for the selected date range. Shops with low stock or no sales may need attention.') }}</p>
                </div>
                @if ($shopPerformance->isEmpty())
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-sm text-gray-500">{{ __('No shops are assigned to your account yet.') }}</div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($shopPerformance as $performance)
                            @php
                                $shop = $performance['shop'];
                                $needsAttention = $shop->status !== 'approved' || $performance['low_stock'] > 0 || $performance['sales'] <= 0;
                                $highlight = $shop->id === $bestShopId && $performance['sales'] > 0;
                            @endphp
                            <article class="rounded-2xl border bg-white p-5 shadow-sm {{ $highlight ? 'border-emerald-300 ring-1 ring-emerald-100' : ($needsAttention ? 'border-amber-200' : 'border-gray-100') }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-bold text-gray-900">{{ $shop->business_name }}</h3>
                                        <p class="mt-1 text-xs text-gray-500">{{ $shop->city }}, {{ $shop->country }}</p>
                                    </div>
                                    @if ($highlight)
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ __('Top performer') }}</span>
                                    @elseif ($needsAttention)
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ __('Needs attention') }}</span>
                                    @else
                                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">{{ __('On track') }}</span>
                                    @endif
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div><p class="text-xs text-gray-500">{{ __('Sales in range') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['sales']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Profit in range') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['profit']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Expenses in range') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['expenses']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Sales today') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['sales_today']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Sales this month') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['sales_month']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Monthly expenses') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['expenses_month']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Monthly profit') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['profit_month']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Transactions') }}</p><p class="mt-1 font-semibold text-gray-900">{{ number_format($performance['transactions']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Products') }}</p><p class="mt-1 font-semibold text-gray-900">{{ number_format($performance['products']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Low-stock alerts') }}</p><p class="mt-1 font-semibold {{ $performance['low_stock'] > 0 ? 'text-amber-700' : 'text-gray-900' }}">{{ number_format($performance['low_stock']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Employees') }}</p><p class="mt-1 font-semibold text-gray-900">{{ number_format($performance['employees']) }}</p></div>
                                    <div><p class="text-xs text-gray-500">{{ __('Purchases in range') }}</p><p class="mt-1 font-semibold text-gray-900">{{ $money($performance['purchases']) }}</p></div>
                                </div>
                                @if ($shop->isApproved())
                                    <form action="{{ route('business.shops.select', $shop) }}" method="POST" class="mt-4 border-t border-gray-100 pt-4">
                                        @csrf
                                        <button type="submit" class="w-full rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-100">{{ __('Manage shop') }}</button>
                                    </form>
                                @else
                                    <p class="mt-4 border-t border-gray-100 pt-4 text-xs text-amber-700">{{ __('This shop is awaiting approval.') }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section aria-label="{{ __('Recent activities') }}" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-4 sm:p-5">
                    <h2 class="font-bold text-gray-900">{{ __('Recent activities across shops') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Sales, purchases, expenses, stock movements, returns, and new customers. Refund amounts are not separately recorded.') }}</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($activities as $activity)
                        <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $activityDots[$activity['tone']] }}"></span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $activity['type'] }} · {{ $activity['shop'] }} · {{ __('By') }} {{ $activity['user'] }}</p>
                                </div>
                            </div>
                            <time class="shrink-0 pl-6 text-xs text-gray-500 sm:pl-0" datetime="{{ $activity['date']->toIso8601String() }}">{{ $activity['date']->format('M j, Y H:i') }}</time>
                        </div>
                    @empty
                        <p class="px-5 py-10 text-center text-sm text-gray-500">{{ __('No activity found for this shop and date range.') }}</p>
                    @endforelse
                </div>
            </section>

            <details id="add-shop" class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <summary class="cursor-pointer list-none px-5 py-4 font-semibold text-gray-900">{{ __('Add another shop') }} <span class="ml-1 text-sm font-normal text-gray-500">{{ __('New shops are submitted for approval.') }}</span></summary>
                <form action="{{ route('business.shops.store') }}" method="POST" class="space-y-5 border-t border-gray-100 p-5">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            'business_name' => __('Shop name'),
                            'business_type' => __('Business type'),
                            'registration_number' => __('Registration number'),
                            'tin_number' => __('TIN (optional)'),
                            'email' => __('Business email'),
                            'phone' => __('Phone (optional)'),
                            'country' => __('Country'),
                            'city' => __('City'),
                        ] as $field => $label)
                            <div>
                                <label for="add-{{ $field }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                <input id="add-{{ $field }}" name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field) }}" @required(! in_array($field, ['tin_number', 'phone'], true)) class="mt-1 block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                                @error($field)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                        <div class="sm:col-span-2 lg:col-span-4">
                            <label for="add-address" class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                            <textarea id="add-address" name="address" rows="2" required class="mt-1 block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('address') }}</textarea>
                            @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">{{ __('Submit shop for approval') }}</button>
                    </div>
                </form>
            </details>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" nonce="{{ Vite::cspNonce() }}"></script>
    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            const period = document.getElementById('period-filter');
            const dateFields = document.querySelectorAll('.date-range-field');
            const updateDateFields = () => dateFields.forEach((field) => field.classList.toggle('hidden', period.value !== 'custom'));
            period.addEventListener('change', updateDateFields);

            const filterForm = document.getElementById('dashboard-filters');
            filterForm.addEventListener('submit', () => {
                const button = document.getElementById('apply-filters');
                button.disabled = true;
                button.textContent = @json(__('Loading dashboard…'));
                button.classList.add('opacity-70');
            });

            if (typeof Chart === 'undefined') return;

            const colors = ['#047857', '#4f46e5', '#0891b2', '#d97706', '#db2777', '#7c3aed', '#2563eb', '#ea580c'];
            Chart.register({
                id: 'dashboardEmptyState',
                afterDraw(chartInstance) {
                    const hasValues = chartInstance.data.datasets.some((dataset) =>
                        (dataset.data ?? []).some((value) => Number(value) !== 0)
                    );
                    if (hasValues) return;
                    const { ctx, chartArea } = chartInstance;
                    if (!chartArea) return;
                    ctx.save();
                    ctx.fillStyle = '#6b7280';
                    ctx.font = '13px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText(@json(__('No data for this selection')), (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
                    ctx.restore();
                },
            });
            const labels = @json($chartLabels);
            const rows = @json($chartRows->values());
            const shopLabels = @json($shopPerformance->map(fn ($row) => $row['shop']->business_name)->values());
            const shopSales = @json($shopPerformance->pluck('sales')->values());
            const shopPurchases = @json($shopPerformance->pluck('purchases')->values());
            const shopInventory = @json($shopPerformance->map(fn ($row) => (float) ($inventoryByShop->get($row['shop']->id)?->value ?? 0))->values());
            const shopStock = @json($shopPerformance->map(fn ($row) => (float) ($inventoryByShop->get($row['shop']->id)?->quantity ?? 0))->values());
            const supplierLabels = @json($purchasesBySupplier->pluck('supplier')->values());
            const supplierValues = @json($purchasesBySupplier->pluck('total')->map(fn ($value) => (float) $value)->values());
            const paymentLabels = @json($salesByPaymentMethod->map(fn ($row) => ucfirst(str_replace('_', ' ', $row->payment_method ?? __('Unknown'))))->values());
            const paymentValues = @json($salesByPaymentMethod->pluck('total')->map(fn ($value) => (float) $value)->values());
            const productLabels = @json($topProducts->pluck('name')->values());
            const productValues = @json($topProducts->pluck('quantity')->map(fn ($value) => (float) $value)->values());
            const expenseLabels = @json($expenseCategories->pluck('category')->values());
            const expenseValues = @json($expenseCategories->pluck('total')->map(fn ($value) => (float) $value)->values());

            const common = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
                scales: { y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,.15)' } }, x: { grid: { display: false } } },
            };
            const chart = (id, type, datasets, options = {}) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, { type, data: { labels, datasets }, options: { ...common, ...options } });
            };
            const line = (label, key, color, fill = false) => ({
                label,
                data: rows.map((row) => Number(row[key] ?? 0)),
                borderColor: color,
                backgroundColor: fill ? `${color}22` : color,
                borderWidth: 2,
                pointRadius: rows.length > 40 ? 0 : 2,
                tension: 0.3,
                fill,
            });
            const bar = (id, chartLabels, data, label, color = colors[0], horizontal = false) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, {
                    type: 'bar',
                    data: { labels: chartLabels, datasets: [{ label, data, backgroundColor: colors.map((item, index) => index === 0 ? color : item), borderRadius: 5 }] },
                    options: { ...common, indexAxis: horizontal ? 'y' : 'x', plugins: { ...common.plugins, legend: { display: false } } },
                });
            };

            chart('sales-trend', 'line', [line(@json(__('Sales')), 'sales', '#047857', true)]);
            chart('income-expenses', 'line', [
                line(@json(__('Sales')), 'sales', '#047857'),
                line(@json(__('Other income')), 'income', '#0891b2'),
                line(@json(__('Expenses')), 'expenses', '#e11d48'),
            ]);
            chart('profit-trend', 'line', [line(@json(__('Net profit')), 'profit', '#4f46e5', true)]);
            chart('cash-flow', 'bar', [
                { label: @json(__('Recorded income')), data: rows.map((row) => Number(row.sales) + Number(row.income)), backgroundColor: '#059669', borderRadius: 4 },
                { label: @json(__('Recorded outflow')), data: rows.map((row) => Number(row.purchases) + Number(row.expenses)), backgroundColor: '#f97316', borderRadius: 4 },
            ]);
            chart('stock-movement', 'bar', [line(@json(__('Net quantity change')), 'stock', '#d97706')]);
            chart('purchase-trend', 'line', [line(@json(__('Purchases')), 'purchases', '#7c3aed', true)]);
            bar('shop-sales', shopLabels, shopSales, @json(__('Sales')), '#047857');
            bar('purchase-shops', shopLabels, shopPurchases, @json(__('Purchases')), '#7c3aed');
            bar('inventory-value', shopLabels, shopInventory, @json(__('Inventory value')), '#0891b2');
            bar('stock-distribution', shopLabels, shopStock, @json(__('Stock quantity')), '#d97706');
            bar('top-products', productLabels, productValues, @json(__('Units sold')), '#4f46e5', true);

            const doughnut = (id, chartLabels, data) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, {
                    type: 'doughnut',
                    data: { labels: chartLabels, datasets: [{ data, backgroundColor: colors, borderWidth: 0 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: common.plugins },
                });
            };
            doughnut('payment-methods', paymentLabels, paymentValues);
            doughnut('expense-categories', expenseLabels, expenseValues);
            doughnut('stock-health', [@json(__('Available')), @json(__('Low stock')), @json(__('Out of stock'))], [
                Math.max(0, {{ $productCount - $lowStockTotal - $outOfStockTotal }}),
                {{ $lowStockTotal }},
                {{ $outOfStockTotal }},
            ]);
            bar('purchase-suppliers', supplierLabels, supplierValues, @json(__('Purchases')), '#7c3aed', true);
        })();
    </script>
</x-app-layout>
