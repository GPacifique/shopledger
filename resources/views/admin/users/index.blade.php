<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">All Users</h2>
                    <p class="text-sm text-gray-500">Manage system users</p>
                </div>
            </div>
            <a href="{{ route('admin.shops.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-medium hover:from-green-600 hover:to-emerald-700 transition transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Shop
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Shop Admins</p>
                            <p class="text-2xl font-bold text-green-600">{{ $users->where('role', 'shop_admin')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-cyan-400 to-teal-600 flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Unassigned</p>
                            <p class="text-2xl font-bold text-cyan-600">{{ $users->whereNull('shop_id')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-400 to-pink-600 flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">System Admins</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $users->where('role', 'system_admin')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">User Directory</h3>
                                <p class="text-xs text-gray-500">All registered users in the system</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Shop</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center mr-3">
                                            <span class="text-sm font-bold text-indigo-600">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleColors = [
                                            'system_admin' => 'bg-purple-100 text-purple-800',
                                            'shop_admin' => 'bg-green-100 text-green-800',
                                            'seller' => 'bg-blue-100 text-blue-800',
                                            'accountant' => 'bg-yellow-100 text-yellow-800',
                                            'user' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $roleColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->shop)
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $user->shop->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">No shop assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($user->shop_id)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <span class="h-2 w-2 rounded-full bg-green-500 mr-1"></span>
                                            Assigned
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            <span class="h-2 w-2 rounded-full bg-yellow-500 mr-1 animate-pulse"></span>
                                            Unassigned
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-16 w-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">No users found</p>
                                        <p class="text-sm text-gray-400">Users will appear here when they register</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
