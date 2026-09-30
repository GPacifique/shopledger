<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

    {{-- Sales --}}
    <div class="bg-white rounded-xl border p-5">
        <p class="text-sm text-slate-500">
            Sales
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['sales'] ?? $data['sales']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Purchases --}}
    <div class="bg-white rounded-xl border p-5">
        <p class="text-sm text-slate-500">
            Purchases
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['purchases'] ?? $data['purchases']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Expenses --}}
    <div class="bg-white rounded-xl border p-5">
        <p class="text-sm text-slate-500">
            Expenses
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['expenses'] ?? $data['expenses']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Other Income --}}
    <div class="bg-white rounded-xl border p-5">
        <p class="text-sm text-slate-500">
            Other Income
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['other_income'] ?? $data['other_income']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Orders --}}
    <div class="bg-white rounded-xl border p-5">
        <p class="text-sm text-slate-500">
            Orders
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['orders'] ?? $data['sales']['order_count'] ?? 0) }}
        </p>
    </div>


    {{-- Net Profit --}}
    <div class="bg-white rounded-xl border p-5">
        <p class="text-sm text-slate-500">
            Net Profit
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['net_profit'] ?? 0) }}
            RWF
        </p>
    </div>

</div>