<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6 gap-5">

    {{-- Sales --}}
    <div class="relative overflow-hidden rounded-2xl border border-green-100 bg-gradient-to-br from-white to-green-50 p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500">Total Sales</p>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-green-600">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
        </div>
        <p class="mt-4 text-2xl font-bold tracking-tight text-slate-800">
            {{ number_format($data['summary']['sales'] ?? $data['sales']['total'] ?? 0) }}
            <span class="text-sm font-semibold text-slate-500">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full bg-green-500"></div>
    </div>

    {{-- Purchases --}}
    <div class="relative overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-br from-white to-blue-50 p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500">Purchases</p>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                <i class="fas fa-truck-loading text-lg"></i>
            </div>
        </div>
        <p class="mt-4 text-2xl font-bold tracking-tight text-slate-800">
            {{ number_format($data['summary']['purchases'] ?? $data['purchases']['total'] ?? 0) }}
            <span class="text-sm font-semibold text-slate-500">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full bg-blue-500"></div>
    </div>

    {{-- Expenses --}}
    <div class="relative overflow-hidden rounded-2xl border border-red-100 bg-gradient-to-br from-white to-red-50 p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500">Expenses</p>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-600">
                <i class="fas fa-wallet text-lg"></i>
            </div>
        </div>
        <p class="mt-4 text-2xl font-bold tracking-tight text-slate-800">
            {{ number_format($data['summary']['expenses'] ?? $data['expenses']['total'] ?? 0) }}
            <span class="text-sm font-semibold text-slate-500">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full bg-red-500"></div>
    </div>

    {{-- Other Income --}}
    <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-br from-white to-emerald-50 p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500">Other Income</p>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                <i class="fas fa-hand-holding-usd text-lg"></i>
            </div>
        </div>
        <p class="mt-4 text-2xl font-bold tracking-tight text-slate-800">
            {{ number_format($data['summary']['other_income'] ?? $data['other_income']['total'] ?? 0) }}
            <span class="text-sm font-semibold text-slate-500">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full bg-emerald-500"></div>
    </div>

    {{-- Orders --}}
    <div class="relative overflow-hidden rounded-2xl border border-violet-100 bg-gradient-to-br from-white to-violet-50 p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500">Orders</p>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                <i class="fas fa-shopping-bag text-lg"></i>
            </div>
        </div>
        <p class="mt-4 text-2xl font-bold tracking-tight text-slate-800">
            {{ number_format($data['summary']['orders'] ?? $data['sales']['order_count'] ?? 0) }}
        </p>
        <p class="mt-1 text-xs text-slate-500">Total orders</p>
        <div class="mt-3 h-1 w-12 rounded-full bg-violet-500"></div>
    </div>

    {{-- Net Profit --}}
    <div class="relative overflow-hidden rounded-2xl border border-amber-100 bg-gradient-to-br from-white to-amber-50 p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500">Net Profit</p>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                <i class="fas fa-coins text-lg"></i>
            </div>
        </div>
        <p class="mt-4 text-2xl font-bold tracking-tight text-slate-800">
            {{ number_format($data['summary']['net_profit'] ?? 0) }}
            <span class="text-sm font-semibold text-slate-500">RWF</span>
        </p>
        <div class="mt-3 h-1 w-12 rounded-full bg-amber-500"></div>
    </div>

</div>
