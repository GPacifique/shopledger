<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                {{ __('Real-Time Stock Tracking') }}
            </h2>
            <div class="flex items-center space-x-3">
                <span id="last-updated" class="text-sm text-gray-500">
                    Last updated: <span class="font-medium">Just now</span>
                </span>
                <button onclick="refreshStockData()" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                </button>
                <a href="{{ route('stock.export') }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Live Indicator -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-sm font-medium text-green-600">Live Stock Tracking Active</span>
                </div>
                <div class="text-sm text-gray-500">
                    Auto-refresh: <span class="font-medium">Every 30 seconds</span>
                </div>
            </div>

            <!-- Summary Cards -->
            <div id="summary-cards" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                    <div class="text-2xl font-bold text-gray-900" id="total-products">{{ $summary['total_products'] }}</div>
                    <div class="text-sm text-gray-500">Total Products</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                    <div class="text-2xl font-bold text-green-600" id="healthy-count">{{ $summary['healthy_stock_count'] }}</div>
                    <div class="text-sm text-gray-500">Healthy Stock</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
                    <div class="text-2xl font-bold text-yellow-600" id="low-count">{{ $summary['low_stock_count'] }}</div>
                    <div class="text-sm text-gray-500">Low Stock</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
                    <div class="text-2xl font-bold text-red-600" id="out-count">{{ $summary['out_of_stock_count'] }}</div>
                    <div class="text-sm text-gray-500">Out of Stock</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                    <div class="text-xl font-bold text-blue-600" id="stock-value">{{ number_format($summary['total_stock_value']) }}</div>
                    <div class="text-sm text-gray-500">Stock Value (RWF)</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
                    <div class="text-xl font-bold text-purple-600" id="retail-value">{{ number_format($summary['total_retail_value']) }}</div>
                    <div class="text-sm text-gray-500">Retail Value (RWF)</div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Stock Alerts Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-red-500 to-orange-500 text-white flex items-center justify-between">
                            <h3 class="font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Stock Alerts
                            </h3>
                            <span id="alert-count" class="bg-white/20 px-2 py-0.5 rounded-full text-sm">{{ $alerts->count() }}</span>
                        </div>
                        <div id="alerts-container" class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                            @forelse($alerts as $alert)
                                <div class="p-4 hover:bg-gray-50 transition" data-alert-id="{{ $alert->id }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            @if($alert->alert_type === 'out_of_stock')
                                                <div class="p-2 bg-red-100 rounded-lg">
                                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="p-2 bg-yellow-100 rounded-lg">
                                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">{{ $alert->product->name }}</p>
                                                <p class="text-xs text-gray-500">SKU: {{ $alert->product->sku }}</p>
                                                <p class="text-xs mt-1">
                                                    <span class="font-medium {{ $alert->alert_type === 'out_of_stock' ? 'text-red-600' : 'text-yellow-600' }}">
                                                        {{ $alert->current_stock }} units
                                                    </span>
                                                    <span class="text-gray-400">/ {{ $alert->threshold }} threshold</span>
                                                </p>
                                            </div>
                                        </div>
                                        <button onclick="resolveAlert({{ $alert->id }})" class="text-gray-400 hover:text-green-600 transition" title="Mark as resolved">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <svg class="w-12 h-12 text-green-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-gray-500 text-sm">All stock levels are healthy!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Movements -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                        <div class="px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                            <h3 class="font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                                Recent Stock Movements
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                            @forelse($recentMovements as $movement)
                                <div class="p-3 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-1.5 rounded-lg {{ $movement->isIncoming() ? 'bg-green-100' : 'bg-blue-100' }}">
                                                @if($movement->isIncoming())
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $movement->product->name }}</p>
                                                <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $movement->type)) }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                            </p>
                                            <p class="text-xs text-gray-400">{{ $movement->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-gray-500 text-sm">
                                    No recent movements
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Stock Levels Table -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800">Stock Levels</h3>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('stock.index', ['filter' => 'all']) }}" 
                                   class="px-3 py-1.5 text-sm rounded-lg transition {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    All
                                </a>
                                <a href="{{ route('stock.index', ['filter' => 'healthy']) }}" 
                                   class="px-3 py-1.5 text-sm rounded-lg transition {{ $filter === 'healthy' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    Healthy
                                </a>
                                <a href="{{ route('stock.index', ['filter' => 'low']) }}" 
                                   class="px-3 py-1.5 text-sm rounded-lg transition {{ $filter === 'low' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    Low
                                </a>
                                <a href="{{ route('stock.index', ['filter' => 'out']) }}" 
                                   class="px-3 py-1.5 text-sm rounded-lg transition {{ $filter === 'out' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    Out
                                </a>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Product</th>
                                        <th class="px-4 py-3 text-center">Stock Level</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-right">Value</th>
                                        <th class="px-4 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="stock-table-body" class="divide-y divide-gray-100">
                                    @forelse($products as $product)
                                        <tr class="hover:bg-gray-50 transition" data-product-id="{{ $product['id'] }}">
                                            <td class="px-4 py-3">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $product['name'] }}</p>
                                                    <p class="text-xs text-gray-500">SKU: {{ $product['sku'] }}</p>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-col items-center">
                                                    <span class="text-lg font-bold {{ $product['status_color'] === 'red' ? 'text-red-600' : ($product['status_color'] === 'yellow' ? 'text-yellow-600' : 'text-green-600') }}">
                                                        {{ $product['stock'] }}
                                                    </span>
                                                    <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                                                        <div class="h-2 rounded-full {{ $product['status_color'] === 'red' ? 'bg-red-500' : ($product['status_color'] === 'yellow' ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                                             style="width: {{ $product['stock_percentage'] }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-400 mt-0.5">Threshold: {{ $product['low_stock_threshold'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($product['status'] === 'out_of_stock')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Out of Stock
                                                    </span>
                                                @elseif($product['status'] === 'low_stock')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Low Stock
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Healthy
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <p class="text-sm font-medium text-gray-900">{{ number_format($product['stock_value']) }} RWF</p>
                                                <p class="text-xs text-gray-500">Retail: {{ number_format($product['retail_value']) }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('stock.movements', $product['id']) }}" 
                                                       class="text-indigo-600 hover:text-indigo-800" title="View History">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('stock.adjust', $product['id']) }}" 
                                                       class="text-yellow-600 hover:text-yellow-800" title="Adjust Stock">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-12 text-center">
                                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                <p class="text-gray-500">No products found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let refreshInterval;

        function refreshStockData() {
            fetch('{{ route("stock.realtime") }}')
                .then(response => response.json())
                .then(data => {
                    // Update summary cards
                    document.getElementById('total-products').textContent = data.summary.total_products;
                    document.getElementById('healthy-count').textContent = data.summary.healthy_stock_count;
                    document.getElementById('low-count').textContent = data.summary.low_stock_count;
                    document.getElementById('out-count').textContent = data.summary.out_of_stock_count;
                    document.getElementById('stock-value').textContent = new Intl.NumberFormat().format(data.summary.total_stock_value);
                    document.getElementById('retail-value').textContent = new Intl.NumberFormat().format(data.summary.total_retail_value);
                    
                    // Update last updated time
                    document.querySelector('#last-updated span').textContent = 'Just now';
                    
                    // Flash effect on updated cards
                    document.querySelectorAll('#summary-cards > div').forEach(card => {
                        card.classList.add('ring-2', 'ring-indigo-300');
                        setTimeout(() => card.classList.remove('ring-2', 'ring-indigo-300'), 500);
                    });
                })
                .catch(error => console.error('Error refreshing stock data:', error));
        }

        function resolveAlert(alertId) {
            fetch(`/stock/alerts/${alertId}/resolve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
                    if (alertElement) {
                        alertElement.classList.add('bg-green-50');
                        setTimeout(() => alertElement.remove(), 500);
                    }
                    // Update alert count
                    const countEl = document.getElementById('alert-count');
                    countEl.textContent = parseInt(countEl.textContent) - 1;
                }
            })
            .catch(error => console.error('Error resolving alert:', error));
        }

        // Start auto-refresh every 30 seconds
        document.addEventListener('DOMContentLoaded', function() {
            refreshInterval = setInterval(refreshStockData, 30000);
        });

        // Stop refresh when page is hidden
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(refreshInterval);
            } else {
                refreshStockData();
                refreshInterval = setInterval(refreshStockData, 30000);
            }
        });
    </script>
    @endpush
</x-app-layout>
