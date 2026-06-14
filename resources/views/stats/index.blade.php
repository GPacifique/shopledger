<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Category Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Sales by Category') }}</h3>
                    <canvas id="salesByCategoryChart" height="260"></canvas>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Expenses by Category') }}</h3>
                    <canvas id="expensesByCategoryChart" height="260"></canvas>
                </div>
            </div>

            <!-- Sales Trends -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Sales Trend') }}</h3>
                    <div class="flex space-x-2" role="group">
                        <button type="button" data-target="sales" data-period="weekly" class="period-btn px-3 py-1 text-sm font-medium rounded-md bg-indigo-600 text-white">{{ __('Weekly') }}</button>
                        <button type="button" data-target="sales" data-period="monthly" class="period-btn px-3 py-1 text-sm font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">{{ __('Monthly') }}</button>
                        <button type="button" data-target="sales" data-period="yearly" class="period-btn px-3 py-1 text-sm font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">{{ __('Yearly') }}</button>
                    </div>
                </div>
                <canvas id="salesTrendChart" height="100"></canvas>
            </div>

            <!-- Expenses Trends -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Expenses Trend') }}</h3>
                    <div class="flex space-x-2" role="group">
                        <button type="button" data-target="expenses" data-period="weekly" class="period-btn px-3 py-1 text-sm font-medium rounded-md bg-indigo-600 text-white">{{ __('Weekly') }}</button>
                        <button type="button" data-target="expenses" data-period="monthly" class="period-btn px-3 py-1 text-sm font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">{{ __('Monthly') }}</button>
                        <button type="button" data-target="expenses" data-period="yearly" class="period-btn px-3 py-1 text-sm font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">{{ __('Yearly') }}</button>
                    </div>
                </div>
                <canvas id="expensesTrendChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
    <script>
        const palette = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#84cc16'];

        // Server-supplied data
        const salesByCategory = @json($salesByCategory);
        const expensesByCategory = @json($expensesByCategory);

        const salesTrends = {
            weekly: @json($salesWeekly),
            monthly: @json($salesMonthly),
            yearly: @json($salesYearly),
        };

        const expensesTrends = {
            weekly: @json($expensesWeekly),
            monthly: @json($expensesMonthly),
            yearly: @json($expensesYearly),
        };

        // --- Doughnut: Sales by Category ---
        new Chart(document.getElementById('salesByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: salesByCategory.labels,
                datasets: [{
                    data: salesByCategory.data,
                    backgroundColor: palette,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // --- Doughnut: Expenses by Category ---
        new Chart(document.getElementById('expensesByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: expensesByCategory.labels,
                datasets: [{
                    data: expensesByCategory.data,
                    backgroundColor: palette,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // --- Line: Sales Trend ---
        const salesTrendChart = new Chart(document.getElementById('salesTrendChart'), {
            type: 'line',
            data: {
                labels: salesTrends.weekly.labels,
                datasets: [{
                    label: 'Sales (RWF)',
                    data: salesTrends.weekly.data,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // --- Line: Expenses Trend ---
        const expensesTrendChart = new Chart(document.getElementById('expensesTrendChart'), {
            type: 'line',
            data: {
                labels: expensesTrends.weekly.labels,
                datasets: [{
                    label: 'Expenses (RWF)',
                    data: expensesTrends.weekly.data,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // --- Period toggle buttons ---
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                const period = btn.dataset.period;

                // Update active styling within this button group
                btn.parentElement.querySelectorAll('.period-btn').forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                btn.classList.remove('bg-gray-100', 'text-gray-700');
                btn.classList.add('bg-indigo-600', 'text-white');

                const chart = target === 'sales' ? salesTrendChart : expensesTrendChart;
                const dataset = (target === 'sales' ? salesTrends : expensesTrends)[period];

                chart.data.labels = dataset.labels;
                chart.data.datasets[0].data = dataset.data;
                chart.update();
            });
        });
    </script>
</x-app-layout>