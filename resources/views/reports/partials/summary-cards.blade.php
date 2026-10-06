<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

    {{-- Sales --}}
    <div class="bg-green rounded-xl border p-5">
        <p class="text-xl font-semi-bold text-green-500">
            Sales
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['sales'] ?? $data['sales']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Purchases --}}
    <div class="bg-blue rounded-xl border p-5">
        <p class="text-xl font-bold text-red-500">
            Purchases
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['purchases'] ?? $data['purchases']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Expenses --}}
    <div class="bg-blue rounded-xl border p-5">
        <p class="text-xl font-bold text-red-500">
            Expenses
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['expenses'] ?? $data['expenses']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Other Income --}}
    <div class="bg-blue rounded-xl border p-5">
        <p class="text-xl font-bold text-green-500">
            Other Income
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['other_income'] ?? $data['other_income']['total'] ?? 0) }}
            RWF
        </p>
    </div>


    {{-- Orders --}}
    <div class="bg-blue rounded-xl border p-5">
        <p class="text-xl font-bold text-blue-500">
            Orders
        </p>

        <p class="text-xl font-bold text-blue-800 mt-2">
            {{ number_format($data['summary']['orders'] ?? $data['sales']['order_count'] ?? 0) }}
        </p>
    </div>


    {{-- Net Profit --}}
    <div class="bg-blue rounded-xl border p-5">
        <p class="text-xl font-bold text-blue-500">
            Net Profit
        </p>

        <p class="text-xl font-bold text-slate-800 mt-2">
            {{ number_format($data['summary']['net_profit'] ?? 0) }}
            RWF
        </p>
    </div>

</div>