<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800">
                    Accountant Dashboard
                </h2>
                <p class="text-sm text-gray-500">Financial overview & analytics</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Total Sales</p>
                <h3 class="text-2xl font-bold text-green-600">
                    {{ number_format($totalSales ?? 0) }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Total Expenses</p>
                <h3 class="text-2xl font-bold text-red-600">
                    {{ number_format($totalExpenses ?? 0) }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Profit</p>
                <h3 class="text-2xl font-bold text-indigo-600">
                    {{ number_format($profit ?? 0) }}
                </h3>
            </div>

        </div>

        <!-- CHARTS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <!-- Sales vs Expenses Line Chart -->
            <div class="bg-white p-5 rounded-xl shadow">
                <h3 class="font-semibold mb-4">Sales vs Expenses (Monthly)</h3>
                <canvas id="salesExpenseChart"></canvas>
            </div>

            <!-- Profit Bar Chart -->
            <div class="bg-white p-5 rounded-xl shadow">
                <h3 class="font-semibold mb-4">Monthly Profit</h3>
                <canvas id="profitChart"></canvas>
            </div>

        </div>

        <!-- QUICK ACTIONS -->
        <div class="bg-white p-5 rounded-xl shadow mb-6">
            <h3 class="font-semibold mb-4">Quick Actions</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <a href="{{ route('sales.create') }}"
                   class="p-4 bg-green-50 hover:bg-green-100 rounded-xl flex items-center justify-between transition">
                    <span class="font-medium text-green-700">Add Sale</span>
                    <span class="text-green-500">➕</span>
                </a>

                <a href="{{ route('purchases.create') }}"
                   class="p-4 bg-red-50 hover:bg-red-100 rounded-xl flex items-center justify-between transition">
                    <span class="font-medium text-red-700">Add Purchase</span>
                    <span class="text-red-500">🛒</span>
                </a>

                <a href="{{ route('expenses.create') }}"
                   class="p-4 bg-yellow-50 hover:bg-yellow-100 rounded-xl flex items-center justify-between transition">
                    <span class="font-medium text-yellow-700">Add Expense</span>
                    <span class="text-yellow-500">💸</span>
                </a>

            </div>
        </div>

    </div>

    <!-- CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const months = @json(array_keys($monthlySales->toArray() ?? []));
        const sales = @json(array_values($monthlySales->toArray() ?? []));
        const expenses = @json(array_values($monthlyExpenses->toArray() ?? []));

        const profit = sales.map((s, i) => s - (expenses[i] ?? 0));

        // Sales vs Expenses Line Chart
        new Chart(document.getElementById('salesExpenseChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Sales',
                        data: sales,
                        borderColor: 'green',
                        fill: false
                    },
                    {
                        label: 'Expenses',
                        data: expenses,
                        borderColor: 'red',
                        fill: false
                    }
                ]
            }
        });

        // Profit Chart
        new Chart(document.getElementById('profitChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Profit',
                        data: profit,
                        backgroundColor: 'blue'
                    }
                ]
            }
        });
    </script>
</x-app-layout>