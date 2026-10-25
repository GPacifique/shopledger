<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-lg font-bold text-white shadow-lg">
                    {{ mb_strtoupper(mb_substr($shop->business_name, 0, 2)) }}
                </div>

                <div class="min-w-0">
                    <h2 class="tone-blue tx truncate text-xl font-bold leading-tight sm:text-3xl">
                        {{ $shop->business_name }}
                    </h2>

                    <p class="tone-red tx flex items-center text-base sm:text-xl">
                        <svg class="mr-1 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="truncate">{{ __('Financial Overview') }}</span>
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-3">
                @php $approved = $shop->status === 'approved'; @endphp

                <span class="d-badge {{ $approved ? 'tone-green' : 'tone-yellow' }} hidden sm:inline-flex">
                    <span class="mr-2 h-2 w-2 animate-pulse rounded-full {{ $approved ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                    {{ __($approved ? 'Approved' : 'Pending') }}
                </span>

                <div class="hidden text-right md:block">
                    <p class="d-muted text-xs">{{ __('Today') }}</p>
                    <p class="d-soft text-sm font-semibold" id="current-time"></p>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $approved = $shop->status === 'approved';

        $paymentIcons = [
            'cash' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'momo' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
            'bank' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>',
            'card' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
        ];

        // Icon badge gradients are coloured on purpose (white icon on top) and look right in both themes.
        $paymentColors = [
            'cash' => 'from-green-400 to-emerald-500',
            'momo' => 'from-yellow-400 to-orange-500',
            'bank' => 'from-blue-400 to-indigo-500',
            'card' => 'from-purple-400 to-violet-500',
        ];

        // Theme-aware tint of each row (see .tone-* in the style block)
        $paymentTones = [
            'cash' => 'tone-green',
            'momo' => 'tone-yellow',
            'bank' => 'tone-blue',
            'card' => 'tone-purple',
        ];

        $periods = [
            ['title' => __('Daily Net Profit'),  'net' => $dailyNetProfit,  'sales' => $dailySales,  'other' => $dailyOtherIncome,  'purchases' => $dailyPurchases,  'expenses' => $dailyExpenses],
            ['title' => __('Weekly Net Profit'), 'net' => $weeklyNetProfit, 'sales' => $weeklySales, 'other' => $weeklyOtherIncome, 'purchases' => $weeklyPurchases, 'expenses' => $weeklyExpenses],
            ['title' => __('Yearly Net Profit'), 'net' => $yearlyNetProfit, 'sales' => $yearlySales, 'other' => $yearlyOtherIncome, 'purchases' => $yearlyPurchases, 'expenses' => $yearlyExpenses],
        ];

        $monthRows = [
            ['label' => __('Total Sales'),     'value' => $stats['month_sales'],        'tone' => 'tone-green'],
            ['label' => __('Other Income'),    'value' => $stats['month_other_income'], 'tone' => 'tone-emerald'],
            ['label' => __('Total Purchases'), 'value' => $stats['month_purchases'],    'tone' => 'tone-red'],
            ['label' => __('Total Expenses'),  'value' => $stats['month_expenses'],     'tone' => 'tone-orange'],
        ];

        $quickActions = [
            ['sales.create',                'New Sale',             'tone-green'],
            ['purchases.create',            'New Purchase',         'tone-red'],
            ['other_incomes.create',        'Add Other Income',     'tone-emerald'],
            ['products.create',             'Add Product',          'tone-indigo'],
            ['staff.create',                'Add Staff',            'tone-purple'],
            ['reports.index',               'View Reports',         'tone-purple'],
            ['pos.index',                   'POS',                  'tone-purple'],
            ['customers.create',            'Add customer',         'tone-purple'],
            ['categories.create',           'Add products Category','tone-purple'],
            ['expenses.create',             'Add Expenses',         'tone-purple'],
            ['expensecategories.create',    'Add Expense Category', 'tone-purple'],
            ['income_categories.create',    'Add Income Category',  'tone-purple'],
        ];

        $showAlerts = $outOfStockProducts->isNotEmpty() || $expiringProducts->isNotEmpty();
    @endphp

    <div class="dash py-8">
        <div class="w-full px-0">

            {{-- ============================================================
                INVENTORY ALERTS
            ============================================================ --}}
            @if($showAlerts)
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="d-alert tone-amber fixed right-4 top-20 z-50 w-[min(92vw,24rem)]"
                     role="alert">

                    <div class="d-alert-head flex items-center justify-between gap-3 px-4 py-3">
                        <div class="min-w-0">
                            <p class="tx text-sm font-semibold">{{ __('Inventory Alerts') }}</p>
                            <p class="st text-xs">{{ __('Products needing attention') }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="d-pill tone-amber">
                                {{ $outOfStockProducts->count() + $expiringProducts->count() }}
                            </span>
                            <button type="button" @click="show = false" aria-label="{{ __('Close') }}"
                                    class="d-icon-btn">&times;</button>
                        </div>
                    </div>

                    <div class="max-h-72 space-y-2 overflow-y-auto p-4">

                        @if($outOfStockProducts->isNotEmpty())
                            <div class="d-tint tone-red p-3">
                                <p class="tx text-sm font-semibold">{{ __('Out of stock') }}</p>

                                @foreach($outOfStockProducts as $product)
                                    <div class="st mt-2 flex items-center justify-between gap-3 text-sm">
                                        <span class="min-w-0 truncate">{{ $product->name }}</span>
                                        <span class="shrink-0 font-medium">0 {{ __('in stock') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($expiringProducts->isNotEmpty())
                            <div class="d-tint tone-orange p-3">
                                <p class="tx text-sm font-semibold">{{ __('Expiring soon') }}</p>

                                @foreach($expiringProducts as $product)
                                    <div class="st mt-2 flex items-center justify-between gap-3 text-sm">
                                        <span class="min-w-0 truncate">{{ $product->name }}</span>
                                        <span class="shrink-0 font-medium">{{ $product->expiry_date->format('M d, Y') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
            @endif


            {{-- ============================================================
                WELCOME BANNER
            ============================================================ --}}
            <div class="d-banner animate-fade-in mb-8">

                <div class="d-banner-dots absolute inset-0" aria-hidden="true">
                    <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="welcome-grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                <circle cx="1" cy="1" r="1" fill="currentColor"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#welcome-grid)"/>
                    </svg>
                </div>

                <div class="relative flex flex-col gap-4 px-6 py-8 sm:px-10 sm:py-10 md:flex-row md:items-center md:justify-between">

                    <div class="min-w-0">
                        <h3 class="d-text mb-2 text-2xl font-bold sm:text-3xl">
                            {{ __('Welcome back') }}, {{ auth()->user()->name }}! 👋
                        </h3>

                        <p class="tone-emerald tx mb-2 text-base font-bold">
                            {{ __("Here's what's happening with ") }}{{ $shop->business_name }}{{ __("   today") }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">

                        {{-- New Sale --}}
                        <a href="{{ route('sales.create') }}" class="d-btn-light inline-flex items-center rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg transition-all hover:scale-105">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ __('New Sale') }}
                        </a>

                        {{-- New Purchase --}}
                        <a href="{{ route('purchases.create') }}" class="inline-flex items-center rounded-xl bg-red-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:scale-105 hover:bg-red-600">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            {{ __('New Purchase') }}
                        </a>

                        {{-- Other Income --}}
                        <a href="{{ route('other_incomes.create') }}" class="inline-flex items-center rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:scale-105 hover:bg-emerald-600">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('Other Income') }}
                        </a>

                    </div>
                </div>
            </div>


            {{-- ============================================================
                NET PROFIT SUMMARY (daily / weekly / yearly)
            ============================================================ --}}
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">

                @foreach($periods as $p)
                    <div class="d-card-flat p-5">

                        <h3 class="tone-green st text-lg font-bold">{{ $p['title'] }}</h3>

                        <p class="d-text mt-1 text-2xl font-bold">RWF {{ number_format($p['net'], 0) }}</p>

                        <div class="d-muted mt-3 space-y-1 text-xs">
                            <div class="flex justify-between gap-3">
                                <span>{{ __('Sales') }}</span>
                                <span>{{ rwf($p['sales']) }}</span>
                            </div>
                            <div class="tone-emerald st flex justify-between gap-3">
                                <span>{{ __('Other Income') }}</span>
                                <span>{{ rwf($p['other']) }}</span>
                            </div>
                            <div class="tone-red st flex justify-between gap-3">
                                <span>{{ __('Purchases') }}</span>
                                <span>{{ rwf($p['purchases']) }}</span>
                            </div>
                            <div class="tone-orange st flex justify-between gap-3">
                                <span>{{ __('Expenses') }}</span>
                                <span>{{ rwf($p['expenses']) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>


            {{-- ============================================================
                MONTHLY SUMMARY + BUSINESS INFO + PAYMENT STATUS
            ============================================================ --}}
            <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Monthly overview --}}
                <div class="d-card">
                    <div class="d-head">
                        <h3 class="d-title">{{ __('Monthly Overview') }}</h3>
                    </div>

                    <div class="space-y-4 p-6">

                        @foreach($monthRows as $row)
                            <div class="d-tint {{ $row['tone'] }} flex items-center justify-between gap-3 p-4">
                                <span class="tx font-medium">{{ $row['label'] }}</span>
                                <span class="st text-xl font-bold">{{ rwf($row['value']) }}</span>
                            </div>
                        @endforeach

                        {{-- Net Profit --}}
                        <div class="flex items-center justify-between gap-3 rounded-xl bg-gradient-to-r p-4 text-white shadow-lg
                            {{ $stats['month_net_profit'] >= 0 ? 'from-emerald-500 to-green-600' : 'from-red-500 to-rose-600' }}">
                            <span class="font-semibold">{{ __('Net Profit') }}</span>
                            <span class="text-xl font-bold sm:text-2xl">{{ rwf($stats['month_net_profit']) }}</span>
                        </div>

                    </div>
                </div>


                {{-- Business information --}}
                <div class="d-card">
                    <div class="d-head">
                        <h3 class="d-title">{{ __('Business Information') }}</h3>
                    </div>

                    <div class="p-6">

                        <div class="mb-6 flex items-center">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-xl font-bold text-white shadow-lg">
                                {{ mb_strtoupper(mb_substr($shop->business_name, 0, 2)) }}
                            </div>

                            <div class="ml-4 min-w-0">
                                <h4 class="tone-blue st truncate text-lg font-bold">{{ $shop->business_name }}</h4>
                                <p class="tone-green st truncate text-sm italic">{{ $shop->slug }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">

                            <div class="d-row-soft flex items-center justify-between px-4 py-2.5">
                                <span class="d-muted text-sm">{{ __('Status') }}</span>

                                <span class="d-badge {{ $approved ? 'tone-green' : 'tone-yellow' }}">
                                    {{ __($approved ? 'Approved' : 'Pending') }}
                                </span>
                            </div>

                            @foreach([
                                [__('Created'),           $shop->created_at->format('M d, Y')],
                                [__('Staff'),             $stats['total_staff']],
                                [__('Suppliers'),         $stats['total_suppliers']],
                                [__('Products'),          $stats['total_products']],
                                [__('Yearly Net profit'), $stats['yearlyNetProfit']],
                            ] as [$infoLabel, $infoValue])
                                <div class="flex items-center justify-between gap-3 rounded-xl px-4 py-2.5">
                                    <span class="d-muted text-sm">{{ $infoLabel }}</span>
                                    <span class="d-text text-sm font-medium">{{ $infoValue }}</span>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>


                {{-- Payment status --}}
                <div class="d-card">
                    <div class="d-head">
                        <h3 class="d-title">{{ __('Payment Status') }}</h3>
                    </div>

                    <div class="p-6">

                        @php
                            $ps = $stats['paymentStatusStats'];
                            $psTotal = max(($ps['Paid'] ?? 0) + ($ps['Unpaid'] ?? 0) + ($ps['Partial'] ?? 0), 1);
                            $paidPct = round((($ps['Paid'] ?? 0) / $psTotal) * 100);
                            $partialPct = round((($ps['Partial'] ?? 0) / $psTotal) * 100);
                            $unpaidPct = max(100 - $paidPct - $partialPct, 0);
                        @endphp

                        <div class="d-track mb-5 flex h-3 w-full overflow-hidden rounded-full"
                             role="img"
                             aria-label="{{ __('Paid') }} {{ $paidPct }}%, {{ __('Partial') }} {{ $partialPct }}%, {{ __('Unpaid') }} {{ $unpaidPct }}%">
                            <div class="bg-green-500" style="width: {{ $paidPct }}%"></div>
                            <div class="bg-yellow-400" style="width: {{ $partialPct }}%"></div>
                            <div class="bg-red-400" style="width: {{ $unpaidPct }}%"></div>
                        </div>

                        <div class="space-y-3">
                            @foreach([
                                [__('Paid'),    'Paid',    'bg-green-500'],
                                [__('Partial'), 'Partial', 'bg-yellow-400'],
                                [__('Unpaid'),  'Unpaid',  'bg-red-400'],
                            ] as [$psLabel, $psKey, $psDot])
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="d-soft flex items-center">
                                        <span class="mr-2 h-2.5 w-2.5 rounded-full {{ $psDot }}"></span>
                                        {{ $psLabel }}
                                    </span>
                                    <span class="d-text font-semibold">{{ rwf($ps[$psKey] ?? 0) }}</span>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>


            {{-- ============================================================
                PAYMENT METHOD STATS
            ============================================================ --}}
            <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

                @foreach([
                    ['today', __("Today's Sales by Payment Method")],
                    ['month', __("This Month's Sales by Payment Method")],
                ] as [$periodKey, $periodTitle])

                    <div class="d-card">
                        <div class="d-head">
                            <h3 class="tone-green st text-lg font-semibold">{{ $periodTitle }}</h3>
                        </div>

                        <div class="space-y-3 p-6">

                            @forelse(\App\Models\Sale::PAYMENT_METHODS as $method => $label)
                                <div class="d-tint {{ $paymentTones[$method] ?? 'tone-gray' }} flex items-center justify-between gap-3 p-4">

                                    <div class="flex min-w-0 items-center">
                                        <div class="mr-3 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br shadow {{ $paymentColors[$method] ?? 'from-gray-400 to-gray-500' }}">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                {!! $paymentIcons[$method] ?? '' !!}
                                            </svg>
                                        </div>

                                        <span class="d-soft truncate font-medium">{{ __($label) }}</span>
                                    </div>

                                    <span class="d-text shrink-0 text-lg font-bold">
                                        {{ rwf($paymentMethodStats[$periodKey][$method] ?? 0) }}
                                    </span>
                                </div>
                            @empty
                                <p class="d-muted py-4 text-center">{{ __('No payment methods defined') }}</p>
                            @endforelse

                        </div>
                    </div>

                @endforeach

            </div>


            {{-- ============================================================
                CATEGORY BREAKDOWN
            ============================================================ --}}
            <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

                <div class="d-card">
                    <div class="d-head">
                        <h3 class="tone-green st text-lg font-semibold">{{ __('Sales by Category') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-80"><canvas id="salesCategoryChart"></canvas></div>
                    </div>
                </div>

                <div class="d-card">
                    <div class="d-head">
                        <h3 class="tone-red st text-lg font-semibold">{{ __('Expenses by Category') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-80"><canvas id="expenseCategoryChart"></canvas></div>
                    </div>
                </div>

            </div>


            {{-- ============================================================
                TREND CHARTS
            ============================================================ --}}
            <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- Daily --}}
                <div class="d-card">
                    <div class="d-head">
                        <h3 class="tone-blue tx text-lg font-semibold">{{ __('Last 7 Days') }}</h3>

                        <div class="d-soft mt-2 flex flex-wrap gap-4 text-xs">
                            <span class="flex items-center"><span class="mr-1 h-3 w-3 rounded-full bg-green-500"></span>{{ __('Sales') }}</span>
                            <span class="flex items-center"><span class="mr-1 h-3 w-3 rounded-full bg-emerald-400"></span>{{ __('Other Income') }}</span>
                            <span class="flex items-center"><span class="mr-1 h-3 w-3 rounded-full bg-red-500"></span>{{ __('Purchases') }}</span>
                            <span class="flex items-center"><span class="mr-1 h-3 w-3 rounded-full bg-orange-400"></span>{{ __('Expenses') }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="h-64"><canvas id="dailyChart"></canvas></div>
                    </div>
                </div>

                {{-- Monthly --}}
                <div class="d-card">
                    <div class="d-head">
                        <h3 class="tone-blue tx text-lg font-semibold">{{ __('Monthly Trend') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-64"><canvas id="monthlyChart"></canvas></div>
                    </div>
                </div>

            </div>


            {{-- ============================================================
                QUICK ACTIONS + RECENT ACTIVITY
            ============================================================ --}}
            <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Quick actions --}}
                <div class="d-card">
                    <div class="d-head">
                        <h3 class="d-title">{{ __('Quick Actions') }}</h3>
                    </div>

                    <div class="space-y-1 p-4">
                        @foreach($quickActions as [$actionRoute, $actionLabel, $actionTone])
                            <a href="{{ route($actionRoute) }}" class="d-action {{ $actionTone }} flex items-center rounded-xl p-3">
                                <div class="d-action-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg">
                                    @if($actionRoute === 'other_incomes.create')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        +
                                    @endif
                                </div>
                                <span class="d-action-label ml-3 font-medium">{{ __($actionLabel) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>


                {{-- Recent activity --}}
                <div class="d-card lg:col-span-2">

                    <div class="d-head">
                        <div class="flex flex-wrap gap-2" role="tablist">
                            <button type="button" role="tab" aria-selected="true"  data-tab-target="tab-sales"        class="tab-btn d-tab is-active">{{ __('Recent Sales') }}</button>
                            <button type="button" role="tab" aria-selected="false" data-tab-target="tab-purchases"    class="tab-btn d-tab">{{ __('Recent Purchases') }}</button>
                            <button type="button" role="tab" aria-selected="false" data-tab-target="tab-other-income" class="tab-btn d-tab">{{ __('Other Income') }}</button>
                        </div>
                    </div>

                    {{-- SALES --}}
                    <div id="tab-sales" class="tab-panel overflow-x-auto" role="tabpanel">
                        <table class="d-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Sale ID') }}</th>
                                    <th class="text-left">{{ __('Date') }}</th>
                                    <th class="text-left">{{ __('Items') }}</th>
                                    <th class="text-right">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales.show', $sale) }}" class="d-link font-semibold">
                                                #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                                            </a>
                                        </td>
                                        <td class="d-muted text-sm">{{ $sale->sale_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="d-badge tone-indigo">{{ $sale->items->count() }} {{ __('items') }}</span>
                                        </td>
                                        <td class="text-right">
                                            <span class="tone-green st font-bold">{{ rwf($sale->total_amount) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="d-muted py-12 text-center">{{ __('No sales yet') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PURCHASES --}}
                    <div id="tab-purchases" class="tab-panel hidden overflow-x-auto" role="tabpanel">
                        <table class="d-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Purchase ID') }}</th>
                                    <th class="text-left">{{ __('Supplier') }}</th>
                                    <th class="text-left">{{ __('Date') }}</th>
                                    <th class="text-right">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchases as $purchase)
                                    <tr>
                                        <td>
                                            <a href="{{ route('purchases.show', $purchase) }}" class="d-link font-semibold">
                                                #{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                                            </a>
                                        </td>
                                        <td class="d-soft text-sm">{{ $purchase->supplier->name ?? __('N/A') }}</td>
                                        <td class="d-muted text-sm">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                        <td class="text-right">
                                            <span class="tone-red st font-bold">{{ rwf($purchase->total_amount) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="d-muted py-12 text-center">{{ __('No purchases yet') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- OTHER INCOME --}}
                    <div id="tab-other-income" class="tab-panel hidden overflow-x-auto" role="tabpanel">
                        <table class="d-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Income') }}</th>
                                    <th class="text-left">{{ __('Category') }}</th>
                                    <th class="text-left">{{ __('Date') }}</th>
                                    <th class="text-right">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOtherIncomes as $income)
                                    <tr>
                                        <td>
                                            <a href="{{ route('other_incomes.show', $income) }}" class="tone-emerald st font-semibold hover:underline">
                                                #{{ str_pad($income->id, 5, '0', STR_PAD_LEFT) }}
                                            </a>
                                        </td>
                                        <td class="d-soft text-sm">
                                            {{ $income->category->name ?? $income->category ?? __('Other Income') }}
                                        </td>
                                        <td class="d-muted text-sm">
                                            {{ $income->income_date ? $income->income_date->format('M d, Y') : $income->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="text-right">
                                            <span class="tone-emerald st font-bold">{{ rwf($income->amount) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="d-muted py-12 text-center">{{ __('No other income recorded yet') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>


            {{-- ============================================================
                LOW STOCK + STOCK MOVEMENTS
            ============================================================ --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- Low stock --}}
                <div class="d-card">
                    <div class="d-head flex items-center justify-between gap-3">
                        <h3 class="d-title">{{ __('Low Stock Products') }}</h3>
                        <a href="{{ route('products.index') }}" class="d-link text-sm font-medium">{{ __('View all') }} →</a>
                    </div>

                    <div class="d-divide">
                        @forelse($lowStockProducts as $product)
                            <div class="flex items-center justify-between gap-3 px-6 py-3">
                                <div class="min-w-0">
                                    <p class="d-text truncate font-medium">{{ $product->name }}</p>
                                    <p class="d-faint text-xs">{{ __('Product') }}</p>
                                </div>

                                <span class="d-pill shrink-0 {{ $product->stock <= 0 ? 'tone-red' : 'tone-amber' }}">
                                    {{ $product->stock }} {{ __('left') }}
                                </span>
                            </div>
                        @empty
                            <p class="d-muted py-8 text-center">{{ __('All products are well stocked') }}</p>
                        @endforelse
                    </div>
                </div>


                {{-- Stock movements --}}
                <div class="d-card">
                    <div class="d-head">
                        <h3 class="d-title">{{ __('Recent Stock Movements') }}</h3>
                    </div>

                    <div class="d-divide max-h-80 overflow-y-auto">
                        @forelse($recentStockMovements as $movement)
                            @php
                                $quantityChange   = (float) $movement->quantity_change;
                                $isIn             = $quantityChange > 0;
                                $movementQuantity = abs($quantityChange);
                            @endphp

                            <div class="flex items-center justify-between gap-3 px-6 py-3">
                                <div class="flex min-w-0 items-center">
                                    <div class="d-action-icon mr-3 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $isIn ? 'tone-teal' : 'tone-orange' }}">
                                        {{ $isIn ? '↑' : '↓' }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="d-text truncate text-sm font-medium">
                                            {{ $movement->product->name ?? __('Unknown product') }}
                                        </p>
                                        <p class="d-faint truncate text-xs">
                                            {{ str_replace('_', ' ', ucfirst($movement->type)) }} · {{ $movement->creator->name ?? __('System') }}
                                        </p>
                                    </div>
                                </div>

                                <span class="{{ $isIn ? 'tone-teal' : 'tone-orange' }} st shrink-0 text-sm font-semibold">
                                    {{ $isIn ? '+' : '-' }}{{ $movementQuantity }}
                                </span>
                            </div>
                        @empty
                            <p class="d-muted py-8 text-center">{{ __('No stock movements yet') }}</p>
                        @endforelse
                    </div>
                </div>

            </div>


            {{-- ============================================================
                STAFF DIRECTORY
            ============================================================ --}}
            <div class="d-card mt-6">

                <div class="d-head flex items-center justify-between gap-3">
                    <h3 class="d-title">{{ __('Staff Directory') }}</h3>
                    <a href="{{ route('staff.index') }}" class="d-link text-sm font-medium">{{ __('View all') }} →</a>
                </div>

                <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($staff as $member)
                        <div class="d-row-outline flex items-center p-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-violet-600 text-sm font-semibold text-white">
                                {{ mb_strtoupper(mb_substr($member->name, 0, 2)) }}
                            </div>

                            <div class="ml-3 min-w-0 overflow-hidden">
                                <p class="d-text truncate text-sm font-medium">{{ $member->name }}</p>
                                <p class="d-faint truncate text-xs">{{ $member->email }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="d-muted py-4 text-center sm:col-span-2 lg:col-span-3">{{ __('No staff added yet') }}</p>
                    @endforelse
                </div>

            </div>

        </div>
    </div>


    {{-- ================================================================
        STYLES  (all colours come from theme tokens; `html.dark` swaps them)
    ================================================================= --}}
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in .6s ease-out forwards; }
        @media (prefers-reduced-motion: reduce) { .animate-fade-in { animation: none; } }

        /* ---- Surfaces: card sits one step above the page in both themes ---- */
        :root, .dash {
            --card:   var(--surface, #fff);
            --card-2: var(--surface-2, #f8fafc);
            --d-text:  var(--text, #0f172a);
            --d-soft:  var(--text-soft, #475569);
            --d-muted: var(--text-muted, #64748b);
            --d-faint: #94a3b8;
            --d-line:  var(--border-soft, #f1f5f9);
            --d-bd:    var(--border, #e2e8f0);
            --d-hover: var(--hover, #f1f5f9);
            --d-link:  #4f46e5;
            --d-link-hover: #3730a3;
        }
        html.dark, html.dark .dash {
            --card:   #162033;
            --card-2: #1b2740;
            --d-faint: #64748b;
            --d-line:  #243049;
            --d-bd:    #2a3648;
            --d-link:  #a5b4fc;
            --d-link-hover: #c7d2fe;
        }

        /* ---- Text ---- */
        .d-text  { color: var(--d-text); }
        .d-soft  { color: var(--d-soft); }
        .d-muted { color: var(--d-muted); }
        .d-faint { color: var(--d-faint); }
        .d-link  { color: var(--d-link); transition: color .15s ease; }
        .d-link:hover { color: var(--d-link-hover); }
        .d-title { color: var(--d-text); font-size: 1.125rem; font-weight: 600; }

        /* ---- Cards ---- */
        .d-card {
            background: var(--card);
            border: 1px solid var(--d-bd);
            border-radius: 1rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / .05);
            overflow: hidden;
            min-width: 0;
        }
        .d-card-flat {
            background: var(--card-2);
            border: 1px solid var(--d-bd);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgb(0 0 0 / .08);
            min-width: 0;
        }
        .d-head {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--d-line);
            background: linear-gradient(to right, var(--card-2), var(--card));
        }
        .d-divide > * + * { border-top: 1px solid var(--d-line); }
        .d-track { background: var(--d-hover); }
        .d-row-soft    { background: var(--card-2); border-radius: .75rem; }
        .d-row-outline { border: 1px solid var(--d-line); border-radius: .75rem; }

        /* ---- Welcome banner ---- */
        .d-banner {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            color: var(--d-text);
            background: linear-gradient(135deg, #e5e7eb 0%, #f1f5f9 100%);
            border: 1px solid var(--d-bd);
            box-shadow: 0 20px 40px -20px rgb(15 23 42 / .35);
        }
        html.dark .d-banner {
            background: linear-gradient(135deg, #1b2740 0%, #14301f 100%);
            box-shadow: 0 20px 40px -20px rgb(0 0 0 / .8);
        }
        .d-banner-dots { color: #0f172a; opacity: .08; pointer-events: none; }
        html.dark .d-banner-dots { color: #fff; opacity: .1; }

        .d-btn-light { background: #fff; color: #4f46e5; }
        .d-btn-light:hover { background: #eef2ff; }
        html.dark .d-btn-light { background: #e2e8f0; color: #3730a3; }
        html.dark .d-btn-light:hover { background: #fff; }

        /* ---- Tones: set 5 variables, everything inside follows ---- */
        .tx { color: var(--t-tx); }
        .st { color: var(--t-st); }

        .d-tint {
            background: var(--t-bg);
            border: 1px solid var(--t-bd);
            border-radius: .75rem;
        }
        .d-badge, .d-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            background: var(--t-ic);
            color: var(--t-tx);
            font-weight: 600;
        }
        .d-badge { padding: .25rem .75rem; font-size: .8125rem; }
        .d-pill  { padding: .25rem .625rem; font-size: .75rem; }

        .d-icon-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 1.75rem; height: 1.75rem; border-radius: .5rem;
            font-size: 1.25rem; line-height: 1;
            color: var(--t-tx);
        }
        .d-icon-btn:hover { background: var(--t-ic); }

        /* Floating inventory alert */
        .d-alert {
            background: var(--card);
            border: 1px solid var(--t-bd);
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / .35);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
        }
        .d-alert-head { border-bottom: 1px solid var(--t-bd); }

        /* Quick actions */
        .d-action { transition: background-color .15s ease; }
        .d-action:hover { background: var(--t-bg); }
        .d-action-icon { background: var(--t-ic); color: var(--t-st); }
        .d-action-label { color: var(--d-soft); transition: color .15s ease; }
        .d-action:hover .d-action-label { color: var(--t-tx); }
        .d-action:focus-visible { outline: 2px solid var(--t-st); outline-offset: 2px; }

        /* Tabs */
        .d-tab {
            padding: .375rem .75rem;
            border-radius: .5rem;
            font-size: .875rem;
            font-weight: 600;
            color: var(--d-soft);
            transition: background-color .15s ease, color .15s ease;
        }
        .d-tab:hover { background: var(--d-hover); color: var(--d-text); }
        .d-tab.is-active, .d-tab.is-active:hover { background: #4f46e5; color: #fff; }
        .d-tab:focus-visible { outline: 2px solid #6366f1; outline-offset: 2px; }

        /* Tables */
        .d-table { border-collapse: collapse; }
        .d-table thead { background: var(--card-2); }
        .d-table th {
            padding: .75rem 1.5rem;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--d-muted);
            white-space: nowrap;
        }
        .d-table td { padding: 1rem 1.5rem; color: var(--d-text); }
        .d-table tbody tr + tr { border-top: 1px solid var(--d-line); }
        .d-table tbody tr:hover { background: var(--card-2); }

        /* ---- Tone palette (light values; dark values below) ---- */
        .tone-green{--t-bg:#f0fdf4;--t-bd:#dcfce7;--t-tx:#15803d;--t-st:#16a34a;--t-ic:#dcfce7;}
        .tone-emerald{--t-bg:#ecfdf5;--t-bd:#d1fae5;--t-tx:#047857;--t-st:#059669;--t-ic:#d1fae5;}
        .tone-red{--t-bg:#fef2f2;--t-bd:#fee2e2;--t-tx:#b91c1c;--t-st:#dc2626;--t-ic:#fee2e2;}
        .tone-orange{--t-bg:#fff7ed;--t-bd:#ffedd5;--t-tx:#c2410c;--t-st:#ea580c;--t-ic:#ffedd5;}
        .tone-amber{--t-bg:#fffbeb;--t-bd:#fde68a;--t-tx:#b45309;--t-st:#d97706;--t-ic:#fef3c7;}
        .tone-indigo{--t-bg:#eef2ff;--t-bd:#e0e7ff;--t-tx:#4338ca;--t-st:#4f46e5;--t-ic:#e0e7ff;}
        .tone-purple{--t-bg:#faf5ff;--t-bd:#f3e8ff;--t-tx:#7e22ce;--t-st:#9333ea;--t-ic:#f3e8ff;}
        .tone-teal{--t-bg:#f0fdfa;--t-bd:#ccfbf1;--t-tx:#0f766e;--t-st:#0d9488;--t-ic:#ccfbf1;}
        .tone-blue{--t-bg:#eff6ff;--t-bd:#dbeafe;--t-tx:#1d4ed8;--t-st:#2563eb;--t-ic:#dbeafe;}
        .tone-yellow{--t-bg:#fefce8;--t-bd:#fef9c3;--t-tx:#a16207;--t-st:#ca8a04;--t-ic:#fef9c3;}
        .tone-gray{--t-bg:var(--card-2);--t-bd:var(--d-line);--t-tx:var(--d-soft);--t-st:var(--d-muted);--t-ic:var(--d-hover);}
        html.dark .tone-green{--t-bg:rgb(34 197 94 / .12);--t-bd:rgb(34 197 94 / .30);--t-tx:#86efac;--t-st:#4ade80;--t-ic:rgb(34 197 94 / .22);}
        html.dark .tone-emerald{--t-bg:rgb(16 185 129 / .12);--t-bd:rgb(16 185 129 / .30);--t-tx:#6ee7b7;--t-st:#34d399;--t-ic:rgb(16 185 129 / .22);}
        html.dark .tone-red{--t-bg:rgb(239 68 68 / .12);--t-bd:rgb(239 68 68 / .30);--t-tx:#fca5a5;--t-st:#f87171;--t-ic:rgb(239 68 68 / .22);}
        html.dark .tone-orange{--t-bg:rgb(249 115 22 / .12);--t-bd:rgb(249 115 22 / .30);--t-tx:#fdba74;--t-st:#fb923c;--t-ic:rgb(249 115 22 / .22);}
        html.dark .tone-amber{--t-bg:rgb(245 158 11 / .12);--t-bd:rgb(245 158 11 / .30);--t-tx:#fcd34d;--t-st:#fbbf24;--t-ic:rgb(245 158 11 / .22);}
        html.dark .tone-indigo{--t-bg:rgb(99 102 241 / .12);--t-bd:rgb(99 102 241 / .30);--t-tx:#a5b4fc;--t-st:#818cf8;--t-ic:rgb(99 102 241 / .22);}
        html.dark .tone-purple{--t-bg:rgb(168 85 247 / .12);--t-bd:rgb(168 85 247 / .30);--t-tx:#d8b4fe;--t-st:#c084fc;--t-ic:rgb(168 85 247 / .22);}
        html.dark .tone-teal{--t-bg:rgb(20 184 166 / .12);--t-bd:rgb(20 184 166 / .30);--t-tx:#5eead4;--t-st:#2dd4bf;--t-ic:rgb(20 184 166 / .22);}
        html.dark .tone-blue{--t-bg:rgb(59 130 246 / .12);--t-bd:rgb(59 130 246 / .30);--t-tx:#93c5fd;--t-st:#60a5fa;--t-ic:rgb(59 130 246 / .22);}
        html.dark .tone-yellow{--t-bg:rgb(234 179 8 / .12);--t-bd:rgb(234 179 8 / .30);--t-tx:#fde047;--t-st:#facc15;--t-ic:rgb(234 179 8 / .22);}
    </style>


    {{-- ================================================================
        CHART.JS
    ================================================================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" nonce="{{ Vite::cspNonce() }}"></script>

    <script nonce="{{ Vite::cspNonce() }}">
    document.addEventListener('DOMContentLoaded', function () {

        /* ---------------- Current time ---------------- */
        const timeEl = document.getElementById('current-time');
        function updateTime() {
            if (!timeEl) return;
            timeEl.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        }
        updateTime();
        setInterval(updateTime, 1000);


        /* ---------------- Tabs ---------------- */
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels  = document.querySelectorAll('.tab-panel');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-tab-target');

                tabPanels.forEach(panel => panel.classList.toggle('hidden', panel.id !== target));

                tabButtons.forEach(b => {
                    const active = b === btn;
                    b.classList.toggle('is-active', active);
                    b.setAttribute('aria-selected', active ? 'true' : 'false');
                });
            });
        });


        /* ---------------- Charts (follow the light / dark class on <html>) ---------------- */
        if (typeof Chart === 'undefined') return;

        const dashEl = document.querySelector('.dash') || document.documentElement;

        // Colours are read from the same CSS variables the page uses
        function theme() {
            const css = getComputedStyle(dashEl);
            const v = (name, fallback) => (css.getPropertyValue(name) || '').trim() || fallback;
            return {
                text:    v('--d-soft',  '#475569'),
                muted:   v('--d-muted', '#64748b'),
                grid:    v('--d-line',  '#f1f5f9'),
                surface: v('--card',    '#ffffff'),
            };
        }

        Chart.defaults.font.family = 'Figtree, system-ui, sans-serif';
        Chart.defaults.plugins.legend.display = false;

        const charts = [];

        /*
         * IMPORTANT: chart.options is a Chart.js resolver proxy. Never do
         *   scale.ticks = scale.ticks || {}
         * on it: that assigns the proxy to itself and recurses until the stack
         * overflows. Every nested object touched here (ticks, grid, legend.labels)
         * is pre-declared in the configs below, so we only write leaf values.
         */
        function paint(chart) {
            const c = theme();
            const o = chart.options;

            o.color = c.text;
            o.borderColor = c.grid;

            Object.values(o.scales || {}).forEach(scale => {
                if (scale.ticks) scale.ticks.color = c.muted;
                if (scale.grid && scale.grid.display !== false) scale.grid.color = c.grid;
            });

            if (o.plugins && o.plugins.legend && o.plugins.legend.labels) {
                o.plugins.legend.labels.color = c.text;
            }

            // Doughnut slices get a gap in the card colour
            if (chart.config.type === 'doughnut') {
                chart.data.datasets.forEach(ds => { ds.borderColor = c.surface; });
            }

            chart.update('none');
        }

        function makeChart(id, config) {
            const el = document.getElementById(id);
            if (!el) return;
            const chart = new Chart(el.getContext('2d'), config);
            charts.push(chart);
            paint(chart);
        }

        // Re-colour every chart whenever the theme toggle flips the `dark` class
        new MutationObserver(() => charts.forEach(paint))
            .observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        const rwfFormat = (value) =>
            'RWF ' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

        const categoryColors = ['#10B981', '#F59E0B', '#EF4444', '#6366F1', '#06B6D4', '#8B5CF6', '#F97316', '#14B8A6'];

        // Factories: each chart gets its own fresh objects, with ticks / grid / labels pre-declared
        const moneyAxis    = () => ({ beginAtZero: true, ticks: { callback: (v) => rwfFormat(v) }, grid: {} });
        const xAxis        = () => ({ ticks: {}, grid: { display: false } });
        const legendBottom = () => ({ display: true, position: 'bottom', labels: {} });
        const legendSide   = () => ({ display: true, position: window.innerWidth < 640 ? 'bottom' : 'right', labels: {} });
        const moneyTooltip = () => ({ callbacks: { label: (c) => c.dataset.label + ': ' + rwfFormat(c.raw) } });


        /* Last 7 days */
        const daily = @json($chartData);

        makeChart('dailyChart', {
            type: 'line',
            data: {
                labels: daily.labels || [],
                datasets: [
                    { label: 'Sales',        data: daily.sales        || [], borderColor: '#10B981', backgroundColor: 'rgba(16,185,129,0.08)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 },
                    { label: 'Other Income', data: daily.other_income || [], borderColor: '#14B8A6', backgroundColor: 'rgba(20,184,166,0.08)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 },
                    { label: 'Purchases',    data: daily.purchases    || [], borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,0.08)',  fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 },
                    { label: 'Expenses',     data: daily.expenses     || [], borderColor: '#F97316', backgroundColor: 'rgba(249,115,22,0.08)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: legendBottom(), tooltip: moneyTooltip() },
                scales: { y: moneyAxis(), x: xAxis() },
                interaction: { intersect: false, mode: 'index' },
            },
        });


        /* Monthly trend */
        const monthly = @json($monthlyChartData);

        const bar = (label, key, color) => ({
            label,
            data: monthly[key] || [],
            backgroundColor: color,
            borderRadius: 8,
            borderSkipped: false,
        });

        makeChart('monthlyChart', {
            type: 'bar',
            data: {
                labels: monthly.labels || [],
                datasets: [
                    bar('Sales',        'sales',        'rgba(16,185,129,0.8)'),
                    bar('Other Income', 'other_income', 'rgba(20,184,166,0.8)'),
                    bar('Purchases',    'purchases',    'rgba(239,68,68,0.8)'),
                    bar('Expenses',     'expenses',     'rgba(249,115,22,0.8)'),
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: legendBottom(), tooltip: moneyTooltip() },
                scales: { y: moneyAxis(), x: xAxis() },
            },
        });


        /* Category doughnuts */
        function doughnut(id, labels, values) {
            makeChart(id, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: labels.map((_, i) => categoryColors[i % categoryColors.length]),
                        hoverOffset: 8,
                        borderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: legendSide(), tooltip: { callbacks: { label: (c) => c.label + ': ' + rwfFormat(c.raw) } } },
                },
            });
        }

        doughnut(
            'salesCategoryChart',
            @json($salesCategoryData->pluck('category')->values()),
            @json($salesCategoryData->pluck('total')->map(fn ($v) => (float) $v)->values())
        );

        doughnut(
            'expenseCategoryChart',
            @json($expenseCategoryData->pluck('category')->values()),
            @json($expenseCategoryData->pluck('total')->map(fn ($v) => (float) $v)->values())
        );

    });
    </script>

</x-app-layout>