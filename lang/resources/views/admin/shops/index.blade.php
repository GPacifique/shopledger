<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Shops</h2>
            <a href="{{ route('admin.shops.create') }}"
                class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-medium hover:from-indigo-600 hover:to-purple-700 transition">
                + Create Shop
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-3">Business Name</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Owner</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shops as $shop)
                            <tr class="border-t border-gray-100">
                                <td class="px-6 py-4 font-medium">{{ $shop->business_name }}</td>
                                <td class="px-6 py-4">{{ $shop->business_type }}</td>
                                <td class="px-6 py-4">{{ $shop->creator?->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($shop->status === 'approved') bg-green-100 text-green-700
                                        @elseif($shop->status === 'pending') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($shop->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $shop->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No shops found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $shops->links() }}
            </div>
        </div>
    </div>
</x-app-layout>