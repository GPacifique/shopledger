<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-600 to-indigo-700 text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('System Admin Dashboard') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('Manage all shops and users') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden text-right sm:block">
                    <p id="current-date" class="text-xs text-gray-500"></p>
                    <p id="current-time" class="text-sm font-semibold tabular-nums text-gray-800"></p>
                </div>

                <a href="{{ route('admin.settings') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <circle cx="12" cy="12" r="3" stroke-width="2"/>
                    </svg>
                    {{ __('Settings') }}
                </a>
            </div>
        </div>
    </x-slot>

    @php
        /* ------------------------------------------------------------------
         | View helpers (all charts are plain server-rendered SVG, so there is
         | no JS library to load and nothing for your CSP to block).
         * ---------------------------------------------------------------- */
        $fmt   = fn ($n) => number_format((float) $n);
        $money = fn ($n) => number_format((float) $n) . ' ' . $currency;

        // Turn a series into [x%, y%] points. Plot area runs from y=6 (top) to y=94 (baseline).
        $toPoints = function (array $values) {
            $values = array_values($values);
            $count  = count($values);
            $max    = max($count ? max($values) : 0, 1);
            $points = [];

            foreach ($values as $i => $v) {
                $points[] = [
                    $count > 1 ? $i / ($count - 1) * 100 : 50,
                    94 - ($v / $max) * 88,
                ];
            }

            return $points;
        };

        // Smooth cubic path through points.
        $curve = function (array $p) {
            if (! count($p)) {
                return 'M0,94 L100,94';
            }

            $d = 'M' . round($p[0][0], 2) . ',' . round($p[0][1], 2);

            for ($i = 1; $i < count($p); $i++) {
                $cx = ($p[$i - 1][0] + $p[$i][0]) / 2;
                $d .= ' C' . round($cx, 2) . ',' . round($p[$i - 1][1], 2)
                    . ' ' . round($cx, 2) . ',' . round($p[$i][1], 2)
                    . ' ' . round($p[$i][0], 2) . ',' . round($p[$i][1], 2);
            }

            return $d;
        };

        $badge = function (?float $t) {
            if ($t === null) {
                return '';
            }

            $up    = $t >= 0;
            $cls   = $up ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700';
            $arrow = $up ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7';

            return '<span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold ' . $cls . '">'
                . '<svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="' . $arrow . '"/></svg>'
                . abs($t) . '%</span>';
        };

        $pct = fn ($part, $whole) => $whole > 0 ? round($part / $whole * 100, 1) : 0;

        $palette = [
            ['bg' => 'bg-indigo-500',  'soft' => 'bg-indigo-50',  'text' => 'text-indigo-700'],
            ['bg' => 'bg-violet-500',  'soft' => 'bg-violet-50',  'text' => 'text-violet-700'],
            ['bg' => 'bg-emerald-500', 'soft' => 'bg-emerald-50', 'text' => 'text-emerald-700'],
            ['bg' => 'bg-amber-500',   'soft' => 'bg-amber-50',   'text' => 'text-amber-700'],
            ['bg' => 'bg-sky-500',     'soft' => 'bg-sky-50',     'text' => 'text-sky-700'],
            ['bg' => 'bg-rose-500',    'soft' => 'bg-rose-50',    'text' => 'text-rose-700'],
        ];

        $icons = [
            'store'     => 'M3 10h18M5 10V6h14v4M5 10v10h14V10M9 20v-6h6v6',
            'clock'     => 'M12 7v5l3 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'check'     => 'M5 13l4 4L19 7',
            'users'     => 'M17 20h5v-1a6 6 0 00-9-5m-3 6H2v-1a7 7 0 0114 0v1zm1-13a4 4 0 11-8 0 4 4 0 018 0zm5 4a4 4 0 100-8',
            'user-plus' => 'M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm10-3v6m-3-3h6',
        ];

        // KPI cards: sparkline for growth metrics, progress line for share metrics.
        $kpis = [
            [
                'label' => __('Total Shops'), 'value' => $totalShops, 'hint' => __('All registered shops'),
                'icon' => 'store', 'tile' => 'bg-indigo-100 text-indigo-700', 'hex' => '#4f46e5',
                'spark' => $shopCumulative, 'trend' => $shopTrend,
            ],
            [
                'label' => __('Pending Approval'), 'value' => $pendingShops, 'hint' => __('of all shops'),
                'icon' => 'clock', 'tile' => 'bg-amber-100 text-amber-700', 'bar' => 'bg-amber-500',
                'share' => $pct($pendingShops, $totalShops),
            ],
            [
                'label' => __('Approved Shops'), 'value' => $approvedShops, 'hint' => __('of all shops'),
                'icon' => 'check', 'tile' => 'bg-emerald-100 text-emerald-700', 'bar' => 'bg-emerald-500',
                'share' => $pct($approvedShops, $totalShops),
            ],
            [
                'label' => __('Total Users'), 'value' => $totalUsers, 'hint' => __('Registered accounts'),
                'icon' => 'users', 'tile' => 'bg-purple-100 text-purple-700', 'hex' => '#7c3aed',
                'spark' => $userCumulative, 'trend' => $userTrend,
            ],
            [
                'label' => __('Unassigned Users'), 'value' => $unassignedUsers, 'hint' => __('of all users'),
                'icon' => 'user-plus', 'tile' => 'bg-cyan-100 text-cyan-700', 'bar' => 'bg-cyan-500',
                'share' => $pct($unassignedUsers, $totalUsers),
            ],
        ];

        // Growth charts (one panel per tab).
        $growth = [
            'shops' => [
                'label' => __('Shops'), 'series' => $shopSeries, 'total' => $totalShops,
                'new' => $newShopsThisMonth, 'trend' => $shopTrend, 'cum' => $shopCumulative,
                'hex' => '#4f46e5', 'dot' => 'bg-indigo-600', 'rule' => 'bg-indigo-200',
            ],
            'users' => [
                'label' => __('Users'), 'series' => $userSeries, 'total' => $totalUsers,
                'new' => $newUsersThisMonth, 'trend' => $userTrend, 'cum' => $userCumulative,
                'hex' => '#059669', 'dot' => 'bg-emerald-600', 'rule' => 'bg-emerald-200',
            ],
        ];

        $statusMeta = [
            'approved' => ['label' => __('Approved'), 'hex' => '#10b981', 'dot' => 'bg-emerald-500'],
            'pending'  => ['label' => __('Pending'),  'hex' => '#f59e0b', 'dot' => 'bg-amber-500'],
            'rejected' => ['label' => __('Rejected'), 'hex' => '#ef4444', 'dot' => 'bg-red-500'],
            'other'    => ['label' => __('Other'),    'hex' => '#9ca3af', 'dot' => 'bg-gray-400'],
        ];

        $health = [
            ['label' => __('Approval rate'),      'value' => $approvalRate,      'note' => __('Reviewed shops that were approved'), 'bar' => 'bg-emerald-500'],
            ['label' => __('Users in a shop'),    'value' => $assignmentRate,    'note' => __('Accounts linked to a shop'),         'bar' => 'bg-indigo-500'],
            ['label' => __('Active accounts'),    'value' => $activeAccountRate, 'note' => __('Accounts not suspended'),            'bar' => 'bg-sky-500'],
            ['label' => __('Shops with a plan'),  'value' => $planCoverageRate,  'note' => __('Shops on a subscription plan'),     'bar' => 'bg-violet-500'],
        ];

        $maxRole  = max($roleBreakdown->max() ?? 0, 1);
        $maxTeam  = max($topShops->max('users_count') ?? 0, 1);
        $heroPts  = $toPoints($shopCumulative);
        $heroLine = $curve($heroPts);
        $hasPlans = $planStats->isNotEmpty();
        $topPlan  = $hasPlans && $planStats->first()['shops'] > 0 ? $planStats->first()['id'] : null;
    @endphp

    <style nonce="{{ Vite::cspNonce() }}">
        @keyframes sa-reveal { from { clip-path: inset(0 100% 0 0); } to { clip-path: inset(0 0 0 0); } }
        .sa-reveal { animation: sa-reveal 1.2s cubic-bezier(.22, 1, .36, 1) both; }
        .sa-bar { transition: width .9s cubic-bezier(.22, 1, .36, 1); }
        @media (prefers-reduced-motion: reduce) {
            .sa-reveal { animation: none; }
            .sa-bar { transition: none; }
        }
    </style>

    <div class="min-h-screen bg-slate-50 py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- ============================ HERO ============================ --}}
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-950 via-indigo-900 to-purple-900 text-white shadow-xl">
                <svg class="sa-reveal pointer-events-none absolute inset-x-0 bottom-0 h-3/4 w-full" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="hero-fill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#a5b4fc" stop-opacity=".35"/>
                            <stop offset="100%" stop-color="#a5b4fc" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <path d="{{ $heroLine }} L100,100 L0,100 Z" fill="url(#hero-fill)"/>
                    <path d="{{ $heroLine }}" fill="none" stroke="#c7d2fe" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round"/>
                </svg>

                <div class="relative p-6 sm:p-8">
                    <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-start">
                        <div>
                            <p class="text-sm font-medium text-indigo-200">{{ __('Platform administration') }}</p>
                            <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
                                {{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}
                            </h1>
                            <p class="mt-2 max-w-xl text-sm text-indigo-100">
                                @if($pendingShops > 0)
                                    {{ trans_choice(':count shop is waiting for your review.|:count shops are waiting for your review.', $pendingShops, ['count' => $pendingShops]) }}
                                @else
                                    {{ __('Every shop application has been reviewed.') }}
                                @endif
                            </p>
                        </div>

                        <a href="{{ route('admin.shops.create') }}"
                           class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 font-semibold text-indigo-900 shadow transition hover:bg-indigo-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-indigo-900">
                            <span class="text-xl leading-none">+</span>
                            {{ __('Create Shop') }}
                        </a>
                    </div>

                    <dl class="mt-8 grid grid-cols-2 gap-x-6 gap-y-5 lg:grid-cols-4">
                        <div>
                            <dt class="text-sm text-indigo-200">{{ __('Monthly recurring revenue') }}</dt>
                            <dd class="mt-1 text-2xl font-bold tabular-nums sm:text-3xl">{{ $fmt($mrr) }}
                                <span class="text-sm font-medium text-indigo-200">{{ $currency }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-indigo-200">{{ __('Active subscriptions') }}</dt>
                            <dd class="mt-1 text-2xl font-bold tabular-nums sm:text-3xl">{{ $fmt($activeSubscriptions) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-indigo-200">{{ __('New shops this month') }}</dt>
                            <dd class="mt-1 flex items-center gap-2 text-2xl font-bold tabular-nums sm:text-3xl">
                                {{ $fmt($newShopsThisMonth) }}
                                @if($shopTrend !== null)
                                    <span class="rounded-full bg-white/15 px-2 py-0.5 text-xs font-semibold {{ $shopTrend >= 0 ? 'text-emerald-200' : 'text-red-200' }}">
                                        {{ $shopTrend >= 0 ? '+' : '−' }}{{ abs($shopTrend) }}%
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-indigo-200">{{ __('Approval rate') }}</dt>
                            <dd class="mt-1 text-2xl font-bold tabular-nums sm:text-3xl">{{ $approvalRate }}%</dd>
                        </div>
                    </dl>

                    <div class="h-24 sm:h-28" aria-hidden="true"></div>
                </div>
            </section>

            {{-- ============================ KPI CARDS ============================ --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5" aria-label="{{ __('Key figures') }}">
                @foreach($kpis as $kpi)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">{{ $kpi['label'] }}</span>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $kpi['tile'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$kpi['icon']] }}"/>
                                </svg>
                            </span>
                        </div>

                        <div class="mt-4 flex items-end justify-between gap-2">
                            <p class="text-3xl font-bold tracking-tight tabular-nums text-gray-900">{{ $fmt($kpi['value']) }}</p>
                            @isset($kpi['trend'])
                                {!! $badge($kpi['trend']) !!}
                            @endisset
                        </div>

                        @isset($kpi['spark'])
                            @php
                                $sp   = $toPoints($kpi['spark']);
                                $spD  = $curve($sp);
                                $spId = 'sp-' . $loop->index;
                            @endphp
                            <svg class="mt-4 h-10 w-full" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                                <defs>
                                    <linearGradient id="{{ $spId }}" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="{{ $kpi['hex'] }}" stop-opacity=".25"/>
                                        <stop offset="100%" stop-color="{{ $kpi['hex'] }}" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <path d="{{ $spD }} L100,100 L0,100 Z" fill="url(#{{ $spId }})"/>
                                <path d="{{ $spD }}" fill="none" stroke="{{ $kpi['hex'] }}" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round"/>
                            </svg>
                            <p class="mt-2 text-xs text-gray-500">{{ $kpi['hint'] }} · {{ __('last 12 months') }}</p>
                        @else
                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-100" role="progressbar" aria-valuenow="{{ $kpi['share'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="sa-bar h-full w-0 rounded-full {{ $kpi['bar'] }}" data-w="{{ $kpi['share'] }}"></div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ $kpi['share'] }}% {{ $kpi['hint'] }}</p>
                        @endisset
                    </div>
                @endforeach
            </section>

            {{-- ============================ GROWTH + STATUS ============================ --}}
            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Growth chart --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ __('Registrations') }}</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ __('New sign-ups per month over the last 12 months.') }}</p>
                        </div>

                        <div class="inline-flex w-fit rounded-xl bg-gray-100 p-1" role="tablist">
                            @foreach($growth as $key => $g)
                                <button type="button" role="tab" data-chart-tab="{{ $key }}"
                                        class="rounded-lg px-4 py-1.5 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ $loop->first ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                                    {{ $g['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @foreach($growth as $key => $g)
                        @php
                            $pts  = $toPoints($g['series']);
                            $line = $curve($pts);
                            $peak = max($g['series'] ? max($g['series']) : 0, 0);
                        @endphp

                        <div data-chart-panel="{{ $key }}" class="{{ $loop->first ? '' : 'hidden' }}">
                            <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2">
                                <p class="text-3xl font-bold tabular-nums text-gray-900">{{ $fmt($g['total']) }}
                                    <span class="text-sm font-medium text-gray-500">{{ __('total') }}</span>
                                </p>
                                <p class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="font-semibold tabular-nums">+{{ $fmt($g['new']) }}</span> {{ __('this month') }}
                                    {!! $badge($g['trend']) !!}
                                </p>
                                <p class="text-sm text-gray-500">{{ __('Peak month') }}: <span class="font-semibold tabular-nums text-gray-700">{{ $fmt($peak) }}</span></p>
                            </div>

                            <div class="relative mx-8 mt-6 h-56">
                                <svg class="sa-reveal absolute inset-0 h-full w-full overflow-visible" viewBox="0 0 100 100" preserveAspectRatio="none" role="img"
                                     aria-label="{{ $g['label'] }} {{ __('registrations per month') }}">
                                    <defs>
                                        <linearGradient id="gf-{{ $key }}" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="{{ $g['hex'] }}" stop-opacity=".28"/>
                                            <stop offset="100%" stop-color="{{ $g['hex'] }}" stop-opacity="0"/>
                                        </linearGradient>
                                    </defs>
                                    @foreach([6, 28, 50, 72] as $gy)
                                        <line x1="0" x2="100" y1="{{ $gy }}" y2="{{ $gy }}" stroke="#e5e7eb" stroke-dasharray="3 4" vector-effect="non-scaling-stroke"/>
                                    @endforeach
                                    <line x1="0" x2="100" y1="94" y2="94" stroke="#d1d5db" vector-effect="non-scaling-stroke"/>
                                    <path d="{{ $line }} L100,94 L0,94 Z" fill="url(#gf-{{ $key }})"/>
                                    <path d="{{ $line }}" fill="none" stroke="{{ $g['hex'] }}" stroke-width="2.5" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                {{-- Hover columns: one per month --}}
                                @foreach($pts as $i => $p)
                                    <div class="group absolute inset-y-0 w-[8.33%] -translate-x-1/2" data-x="{{ round($p[0], 2) }}">
                                        <div class="absolute inset-y-0 left-1/2 w-px opacity-0 transition group-hover:opacity-100 {{ $g['rule'] }}"></div>
                                        <div class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2" data-y="{{ round($p[1], 2) }}">
                                            <span class="block h-3 w-3 rounded-full border-2 border-white shadow transition {{ $g['dot'] }} {{ $loop->last ? '' : 'scale-0 group-hover:scale-100' }}"></span>
                                            <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-2 hidden -translate-x-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-3 py-2 text-xs text-white shadow-lg group-hover:block">
                                                <p class="font-semibold">{{ $monthLabels[$i] }}</p>
                                                <p class="tabular-nums text-gray-300">+{{ $fmt($g['series'][$i]) }} {{ __('new') }}</p>
                                                <p class="tabular-nums text-gray-400">{{ $fmt($g['cum'][$i]) }} {{ __('total') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mx-8 mt-3 flex justify-between text-xs text-gray-500">
                                @foreach($monthLabels as $i => $label)
                                    <span class="{{ $i % 2 ? 'hidden sm:inline' : '' }}">{{ $label }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Status donut --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Shop status') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('How every registered shop is doing.') }}</p>

                    <div class="relative mx-auto mt-6 h-48 w-48">
                        <svg viewBox="0 0 42 42" class="h-full w-full -rotate-90" role="img" aria-label="{{ __('Shop status breakdown') }}">
                            <circle cx="21" cy="21" r="15.9155" fill="none" stroke="#f3f4f6" stroke-width="5"/>
                            @php $offset = 0; @endphp
                            @foreach($statusBreakdown as $key => $count)
                                @php $share = $totalShops > 0 ? $count / $totalShops * 100 : 0; @endphp
                                @if($share > 0)
                                    <circle cx="21" cy="21" r="15.9155" fill="none"
                                            stroke="{{ $statusMeta[$key]['hex'] }}" stroke-width="5"
                                            stroke-dasharray="{{ round(max($share - 0.8, 0.2), 2) }} {{ round(100 - max($share - 0.8, 0.2), 2) }}"
                                            stroke-dashoffset="{{ round(-$offset, 2) }}"/>
                                    @php $offset += $share; @endphp
                                @endif
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <p class="text-3xl font-bold tabular-nums text-gray-900">{{ $fmt($totalShops) }}</p>
                            <p class="text-xs text-gray-500">{{ __('shops') }}</p>
                        </div>
                    </div>

                    <ul class="mt-6 space-y-3">
                        @foreach($statusBreakdown as $key => $count)
                            @continue($key === 'other' && $count === 0)
                            <li class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-gray-700">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $statusMeta[$key]['dot'] }}"></span>
                                    {{ $statusMeta[$key]['label'] }}
                                </span>
                                <span class="tabular-nums text-gray-500">
                                    <span class="font-semibold text-gray-900">{{ $fmt($count) }}</span> · {{ $pct($count, $totalShops) }}%
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            {{-- ============================ SUBSCRIPTIONS ============================ --}}
            <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-4 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:p-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ __('Subscriptions') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Revenue is estimated from approved shops: plan price, with yearly plans divided by 12.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if(Route::has('admin.subscription-plans.create'))
                            <a href="{{ route('admin.subscription-plans.create') }}"
                               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                {{ __('New plan') }}
                            </a>
                        @endif
                        @if(Route::has('admin.subscription-plans.index'))
                            <a href="{{ route('admin.subscription-plans.index') }}"
                               class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                {{ __('Manage plans') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 border-b border-gray-100 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-3">
                    <div class="rounded-xl bg-sky-50 p-4">
                        <p class="text-sm text-sky-700">{{ __('subscription.payments_received_this_month') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-sky-950">{{ $money($subscriptionRevenueThisMonth) }}</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-4">
                        <p class="text-sm text-emerald-700">{{ __('subscription.total_payments_received') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-emerald-950">{{ $money($subscriptionRevenueTotal) }}</p>
                    </div>
                    <div class="rounded-xl bg-violet-50 p-4">
                        <p class="text-sm text-violet-700">{{ __('subscription.payment_count_this_month') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-violet-950">{{ $fmt($subscriptionPaymentCountThisMonth) }}</p>
                    </div>
                </div>

                @if(! $hasPlans)
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-indigo-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('No subscription plans yet') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Create a plan to start tracking revenue and plan usage here.') }}</p>
                    </div>
                @else
                    <div class="space-y-6 p-5 sm:p-6">
                        {{-- Summary strip --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            <div class="rounded-xl bg-indigo-50 p-4">
                                <p class="text-sm text-indigo-700">{{ __('Monthly recurring revenue') }}</p>
                                <p class="mt-1 text-2xl font-bold tabular-nums text-indigo-950">{{ $money($mrr) }}</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-4">
                                <p class="text-sm text-emerald-700">{{ __('Active subscriptions') }}</p>
                                <p class="mt-1 text-2xl font-bold tabular-nums text-emerald-950">{{ $fmt($activeSubscriptions) }}</p>
                            </div>
                            <div class="rounded-xl {{ $shopsWithoutPlan > 0 ? 'bg-amber-50' : 'bg-gray-50' }} p-4">
                                <p class="text-sm {{ $shopsWithoutPlan > 0 ? 'text-amber-700' : 'text-gray-600' }}">{{ __('Shops without a plan') }}</p>
                                <p class="mt-1 text-2xl font-bold tabular-nums {{ $shopsWithoutPlan > 0 ? 'text-amber-950' : 'text-gray-900' }}">{{ $fmt($shopsWithoutPlan) }}</p>
                            </div>
                        </div>

                        {{-- Plan mix bar --}}
                        @if($planStats->sum('shops') > 0)
                            <div>
                                <p class="mb-2 text-sm font-semibold text-gray-700">{{ __('Plan mix') }}</p>
                                <div class="flex h-3 overflow-hidden rounded-full bg-gray-100" role="img" aria-label="{{ __('Share of shops per plan') }}">
                                    @foreach($planStats as $plan)
                                        @continue($plan['shops'] === 0)
                                        <div class="sa-bar h-full w-0 {{ $palette[$loop->index % count($palette)]['bg'] }} {{ $loop->first ? '' : 'border-l-2 border-white' }}"
                                             data-w="{{ $plan['share'] }}" title="{{ $plan['name'] }}: {{ $plan['share'] }}%"></div>
                                    @endforeach
                                </div>
                                <ul class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-600">
                                    @foreach($planStats as $plan)
                                        <li class="flex items-center gap-2">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $palette[$loop->index % count($palette)]['bg'] }}"></span>
                                            {{ $plan['name'] }}
                                            <span class="tabular-nums text-gray-400">{{ $plan['share'] }}%</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Plan cards --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($planStats as $plan)
                                @php $c = $palette[$loop->index % count($palette)]; @endphp
                                <article class="flex flex-col rounded-2xl border border-gray-200 p-5">
                                    <header class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold {{ $c['soft'] }} {{ $c['text'] }}">
                                                {{ strtoupper(substr($plan['name'], 0, 2)) }}
                                            </span>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $plan['name'] }}</h3>
                                                <p class="text-xs text-gray-500">{{ __(ucfirst($plan['billing_cycle'])) }}</p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-end gap-1">
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $plan['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ __(ucfirst($plan['status'])) }}
                                            </span>
                                            @if($topPlan === $plan['id'])
                                                <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-800">{{ __('Most used') }}</span>
                                            @endif
                                        </div>
                                    </header>

                                    <p class="mt-4 text-2xl font-bold tabular-nums text-gray-900">
                                        {{ $money($plan['price']) }}
                                        <span class="text-sm font-medium text-gray-500">/ {{ $plan['billing_cycle'] === 'yearly' ? __('year') : __('month') }}</span>
                                    </p>

                                    <div class="mt-4">
                                        <div class="mb-1.5 flex justify-between text-xs text-gray-500">
                                            <span><span class="font-semibold tabular-nums text-gray-800">{{ $fmt($plan['shops']) }}</span> {{ __('shops') }}</span>
                                            <span class="tabular-nums">{{ $plan['share'] }}%</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                            <div class="sa-bar h-full w-0 rounded-full {{ $c['bg'] }}" data-w="{{ $plan['share'] }}"></div>
                                        </div>
                                    </div>

                                    <dl class="mt-4 grid grid-cols-3 gap-2 rounded-xl bg-gray-50 p-3 text-center">
                                        <div>
                                            <dd class="text-sm font-semibold tabular-nums text-gray-900">{{ is_numeric($plan['max_users']) ? $fmt($plan['max_users']) : '—' }}</dd>
                                            <dt class="text-xs text-gray-500">{{ __('Users') }}</dt>
                                        </div>
                                        <div>
                                            <dd class="text-sm font-semibold tabular-nums text-gray-900">{{ is_numeric($plan['max_products']) ? $fmt($plan['max_products']) : '—' }}</dd>
                                            <dt class="text-xs text-gray-500">{{ __('Products') }}</dt>
                                        </div>
                                        <div>
                                            <dd class="text-sm font-semibold tabular-nums text-gray-900">{{ is_numeric($plan['max_branches']) ? $fmt($plan['max_branches']) : '—' }}</dd>
                                            <dt class="text-xs text-gray-500">{{ __('Branches') }}</dt>
                                        </div>
                                    </dl>

                                    <p class="mt-4 text-sm text-gray-500">
                                        {{ __('Earning') }}
                                        <span class="font-semibold tabular-nums text-gray-800">{{ $money($plan['mrr']) }}</span>
                                        {{ __('per month') }}
                                    </p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            {{-- ============================ HEALTH / ROLES / TEAMS ============================ --}}
            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Platform health --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Platform health') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Quick checks on how well the platform is set up.') }}</p>

                    <div class="mt-6 space-y-5">
                        @foreach($health as $row)
                            <div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-sm font-semibold text-gray-800">{{ $row['label'] }}</span>
                                    <span class="text-sm font-bold tabular-nums text-gray-900">{{ $row['value'] }}%</span>
                                </div>
                                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100" role="progressbar" aria-valuenow="{{ $row['value'] }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="sa-bar h-full w-0 rounded-full {{ $row['bar'] }}" data-w="{{ $row['value'] }}"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">{{ $row['note'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Users by role --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Users by role') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Who is using the platform.') }}</p>

                    @if($roleBreakdown->isEmpty())
                        <p class="mt-8 text-center text-sm text-gray-500">{{ __('No users yet.') }}</p>
                    @else
                        <ul class="mt-6 space-y-4">
                            @foreach($roleBreakdown as $role => $count)
                                <li>
                                    <div class="flex items-baseline justify-between text-sm">
                                        <span class="font-medium text-gray-800">{{ __(ucwords(str_replace('_', ' ', (string) $role ?: 'unassigned'))) }}</span>
                                        <span class="tabular-nums text-gray-500"><span class="font-semibold text-gray-900">{{ $fmt($count) }}</span> · {{ $pct($count, $totalUsers) }}%</span>
                                    </div>
                                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100">
                                        <div class="sa-bar h-full w-0 rounded-full {{ $palette[$loop->index % count($palette)]['bg'] }}" data-w="{{ $pct($count, $maxRole) }}"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($suspendedUsers > 0)
                        <p class="mt-6 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700">
                            {{ trans_choice(':count account is suspended.|:count accounts are suspended.', $suspendedUsers, ['count' => $suspendedUsers]) }}
                        </p>
                    @endif
                </div>

                {{-- Largest shops --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Largest teams') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Shops with the most users.') }}</p>

                    @if($topShops->isEmpty())
                        <p class="mt-8 text-center text-sm text-gray-500">{{ __('No shops have been registered yet.') }}</p>
                    @else
                        <ol class="mt-6 space-y-4">
                            @foreach($topShops as $shop)
                                <li>
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <span class="flex min-w-0 items-center gap-2">
                                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-xs font-bold text-indigo-700">
                                                {{ strtoupper(substr($shop->business_name ?? 'SH', 0, 2)) }}
                                            </span>
                                            <span class="truncate font-medium text-gray-800">{{ $shop->business_name }}</span>
                                        </span>
                                        <span class="shrink-0 font-semibold tabular-nums text-gray-900">{{ $fmt($shop->users_count) }}</span>
                                    </div>
                                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100">
                                        <div class="sa-bar h-full w-0 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" data-w="{{ $pct($shop->users_count, $maxTeam) }}"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            {{-- ============================ QUICK ACTIONS ============================ --}}
            <section>
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('Quick Actions') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Access common administration tasks.') }}</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @php
                        $actions = [
                            ['route' => 'admin.shops.create', 'title' => __('Register Shop'),  'text' => __('Create a shop account.'),               'border' => 'hover:border-emerald-400', 'color' => 'text-emerald-700'],
                            ['route' => 'admin.shops.index',  'title' => __('Manage Shops'),   'text' => __('View and review shop registrations.'),  'border' => 'hover:border-indigo-400',  'color' => 'text-indigo-700'],
                            ['route' => 'admin.users.index',  'title' => __('Manage Users'),   'text' => __('Review and update user accounts.'),     'border' => 'hover:border-purple-400',  'color' => 'text-purple-700'],
                            ['route' => 'admin.subscription-plans.index', 'title' => __('Subscription Plans'), 'text' => __('Prices, limits and status.'),  'border' => 'hover:border-sky-400',     'color' => 'text-sky-700'],
                            ['route' => 'admin.settings',     'title' => __('System Settings'), 'text' => __('Open platform configuration.'),        'border' => 'hover:border-gray-400',    'color' => 'text-gray-800'],
                        ];
                    @endphp

                    @foreach($actions as $action)
                        @continue(! Route::has($action['route']))
                        <a href="{{ route($action['route']) }}"
                           class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ $action['border'] }}">
                            <span class="text-sm font-semibold {{ $action['color'] }}">{{ $action['title'] }}</span>
                            <p class="mt-2 text-xs text-gray-500">{{ $action['text'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- ============================ PENDING APPROVALS ============================ --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-3 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ __('Shops Awaiting Approval') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Review new shop registrations.') }}</p>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-800">
                        {{ number_format($pendingShops) }} {{ __('pending') }}
                    </span>
                </div>

                @if($pendingShopsList->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('All caught up!') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('There are no shops awaiting approval.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 sm:px-6">{{ __('Shop') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 sm:px-6">{{ __('Owner') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 sm:px-6">{{ __('Submitted') }}</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 sm:px-6">{{ __('Actions') }}</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach($pendingShopsList as $shop)
                                    @php $owner = $shop->users->where('role', 'shop_admin')->first(); @endphp
                                    <tr class="transition hover:bg-amber-50/50">
                                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 font-bold text-indigo-700">
                                                    {{ strtoupper(substr($shop->business_name ?? 'SH', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $shop->business_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $shop->slug }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-5 py-4 sm:px-6">
                                            <p class="text-sm text-gray-700">{{ $owner?->name ?? __('Not assigned') }}</p>
                                            <p class="text-xs text-gray-500">{{ $owner?->email ?? '—' }}</p>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                            <p class="text-sm text-gray-700">{{ $shop->created_at?->format('d M Y') ?? '—' }}</p>
                                            <p class="text-xs text-gray-500">{{ $shop->created_at?->diffForHumans() ?? '' }}</p>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                                            <div class="flex justify-end gap-2">
                                                <form action="{{ route('admin.shops.approve', $shop) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Approve this shop?') }}')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                                                        {{ __('Approve') }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.shops.reject', $shop) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Reject this shop?') }}')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                        {{ __('Reject') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- ============================ RECENT SHOPS ============================ --}}
            <section id="recent-shops" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-3 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ __('Recent Shops') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Latest registered businesses on the platform.') }}</p>
                    </div>

                    <a href="{{ route('admin.shops.index') }}"
                       class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                        {{ __('View All Shops') }} →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 sm:px-6">{{ __('Shop') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 sm:px-6">{{ __('Status') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 sm:px-6">{{ __('Users') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 sm:px-6">{{ __('Registered') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentShops as $shop)
                                @php $status = strtolower($shop->status ?? 'unknown'); @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-sm font-bold text-indigo-700">
                                                {{ strtoupper(substr($shop->business_name ?? 'SH', 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $shop->business_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $shop->slug }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                            {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ ! in_array($status, ['approved', 'pending', 'rejected']) ? 'bg-gray-100 text-gray-700' : '' }}">
                                            {{ __(ucfirst($status)) }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-800">
                                            {{ $shop->users_count ?? $shop->users->count() }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600 sm:px-6">
                                        {{ $shop->created_at?->format('d M Y') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                        {{ __('No shops have been registered yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <footer class="pb-4 text-center text-xs text-gray-400">
                © {{ date('Y') }} Mahwi POS. {{ __('System Administration') }}.
            </footer>
        </div>
    </div>

    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            /* Clock */
            const dateElement = document.getElementById('current-date');
            const timeElement = document.getElementById('current-time');

            function updateClock() {
                const now = new Date();

                if (dateElement) {
                    dateElement.textContent = now.toLocaleDateString(undefined, {
                        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                    });
                }

                if (timeElement) {
                    timeElement.textContent = now.toLocaleTimeString(undefined, {
                        hour: '2-digit', minute: '2-digit', second: '2-digit'
                    });
                }
            }

            updateClock();
            setInterval(updateClock, 1000);

            /* Chart positions are applied here (not with inline style attributes) so a strict CSP stays happy. */
            document.querySelectorAll('[data-x]').forEach((el) => { el.style.left = el.dataset.x + '%'; });
            document.querySelectorAll('[data-y]').forEach((el) => { el.style.top = el.dataset.y + '%'; });

            /* Progress lines grow in once on load. */
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    document.querySelectorAll('[data-w]').forEach((el) => {
                        el.style.width = Math.min(Math.max(parseFloat(el.dataset.w) || 0, 0), 100) + '%';
                    });
                });
            });

            /* Shops / Users tabs */
            const tabs = document.querySelectorAll('[data-chart-tab]');
            const panels = document.querySelectorAll('[data-chart-panel]');
            const on = ['bg-white', 'text-gray-900', 'shadow-sm'];
            const off = ['text-gray-500', 'hover:text-gray-800'];

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    tabs.forEach((t) => {
                        const active = t === tab;
                        t.classList.remove(...(active ? off : on));
                        t.classList.add(...(active ? on : off));
                        t.setAttribute('aria-selected', active ? 'true' : 'false');
                    });

                    panels.forEach((p) => {
                        p.classList.toggle('hidden', p.dataset.chartPanel !== tab.dataset.chartTab);
                    });
                });
            });
        })();
    </script>
</x-app-layout>