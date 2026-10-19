<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6">

    {{-- Total Sales --}}
    <div class="ui-grad tone-green relative overflow-hidden rounded-2xl p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <p class="ui-muted text-sm font-semibold">Total Sales</p>
            <div class="ui-chip flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <i class="fas fa-chart-line text-lg" aria-hidden="true"></i>
            </div>
        </div>
        <p class="ui-text mt-4 break-words text-2xl font-bold tracking-tight">
            {{ number_format($data['summary']['sales'] ?? $data['sales']['total'] ?? 0) }}
            <span class="ui-muted text-sm font-semibold">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full" style="background-color: var(--t-st)"></div>
    </div>

    {{-- Purchases --}}
    <div class="ui-grad tone-blue relative overflow-hidden rounded-2xl p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <p class="ui-muted text-sm font-semibold">Purchases</p>
            <div class="ui-chip flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <i class="fas fa-truck-loading text-lg" aria-hidden="true"></i>
            </div>
        </div>
        <p class="ui-text mt-4 break-words text-2xl font-bold tracking-tight">
            {{ number_format($data['summary']['purchases'] ?? $data['purchases']['total'] ?? 0) }}
            <span class="ui-muted text-sm font-semibold">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full" style="background-color: var(--t-st)"></div>
    </div>

    {{-- Expenses --}}
    <div class="ui-grad tone-red relative overflow-hidden rounded-2xl p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <p class="ui-muted text-sm font-semibold">Expenses</p>
            <div class="ui-chip flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <i class="fas fa-wallet text-lg" aria-hidden="true"></i>
            </div>
        </div>
        <p class="ui-text mt-4 break-words text-2xl font-bold tracking-tight">
            {{ number_format($data['summary']['expenses'] ?? $data['expenses']['total'] ?? 0) }}
            <span class="ui-muted text-sm font-semibold">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full" style="background-color: var(--t-st)"></div>
    </div>

    {{-- Other Income --}}
    <div class="ui-grad tone-emerald relative overflow-hidden rounded-2xl p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <p class="ui-muted text-sm font-semibold">Other Income</p>
            <div class="ui-chip flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <i class="fas fa-hand-holding-usd text-lg" aria-hidden="true"></i>
            </div>
        </div>
        <p class="ui-text mt-4 break-words text-2xl font-bold tracking-tight">
            {{ number_format($data['summary']['other_income'] ?? $data['other_income']['total'] ?? 0) }}
            <span class="ui-muted text-sm font-semibold">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full" style="background-color: var(--t-st)"></div>
    </div>

    {{-- Orders --}}
    <div class="ui-grad tone-violet relative overflow-hidden rounded-2xl p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <p class="ui-muted text-sm font-semibold">Orders</p>
            <div class="ui-chip flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <i class="fas fa-shopping-bag text-lg" aria-hidden="true"></i>
            </div>
        </div>
        <p class="ui-text mt-4 break-words text-2xl font-bold tracking-tight">
            {{ number_format($data['summary']['orders'] ?? $data['sales']['order_count'] ?? 0) }}
        </p>
        <p class="ui-muted mt-1 text-xs">Total orders</p>
        <div class="mt-3 h-1 w-12 rounded-full" style="background-color: var(--t-st)"></div>
    </div>

    {{-- Net Profit --}}
    <div class="ui-grad tone-amber relative overflow-hidden rounded-2xl p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <p class="ui-muted text-sm font-semibold">Net Profit</p>
            <div class="ui-chip flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <i class="fas fa-coins text-lg" aria-hidden="true"></i>
            </div>
        </div>
        <p class="ui-text mt-4 break-words text-2xl font-bold tracking-tight">
            {{ number_format($data['summary']['net_profit'] ?? 0) }}
            <span class="ui-muted text-sm font-semibold">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full" style="background-color: var(--t-st)"></div>
    </div>

</div>
