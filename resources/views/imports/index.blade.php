<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import & Templates') }}</h2>
                <p class="text-sm text-gray-500 mt-1">Download a template, fill it in, then upload it for validation and import.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                <h3 class="font-semibold text-blue-900">{{ __('How it works') }}</h3>
                <ol class="mt-2 text-sm text-blue-800 list-decimal list-inside space-y-1">
                    <li>Download the correct template.</li>
                    <li>Replace the example row(s) with your records.</li>
                    <li>Upload the completed file.</li>
                    <li>Review validation results and preview.</li>
                    <li>Confirm the import. Transactions update stock automatically.</li>
                </ol>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach([
                    'products' => ['Products', 'Bulk create or update products. SKU identifies a product.', 'shop'],
                    'suppliers' => ['Suppliers', 'Create or update suppliers by supplier name.', 'shop'],
                    'customers' => ['Customers', 'Create or update customers using phone, email, or name.', 'shop'],
                    'purchases' => ['Purchases', 'Import grouped purchase lines and increase stock.', 'transaction'],
                    'sales' => ['Sales', 'Import grouped sale lines and decrease stock.', 'transaction'],
                ] as $type => [$title, $description, $permission])
                    @php($allowed = $permission === 'shop' ? auth()->user()->isShopAdmin() : (auth()->user()->isShopAdmin() || auth()->user()->isSeller()))
                    @if($allowed)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __($title) }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ __($description) }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">XLSX</span>
                            </div>
                            <div class="mt-5 flex gap-2">
                                <a href="{{ route('imports.template', $type) }}" class="inline-flex items-center px-3 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-700">Download Template</a>
                            </div>
                            <form action="{{ route('imports.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                                @csrf
                                <input type="hidden" name="type" value="{{ $type }}">
                                <input type="file" name="file" accept=".xlsx,.csv,.txt" required class="block w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2">
                                <button class="w-full inline-flex justify-center items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Upload & Validate</button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
