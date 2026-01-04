<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">System Admin Dashboard</h2>
                    <p class="text-sm text-gray-500">Manage all shops and users</p>
                </div>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-xs text-gray-500" id="current-date"></p>
                <p class="text-sm font-semibold text-gray-700" id="current-time"></p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <!-- Total Shops -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.1s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Shops</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalShops) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-indigo-600 font-medium">All registered shops</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-indigo-400 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Pending Approval -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 {{ $pendingShops > 0 ? 'ring-2 ring-yellow-400 ring-opacity-50' : '' }}" style="animation-delay: 0.2s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg {{ $pendingShops > 0 ? 'animate-pulse' : '' }}">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            @if($pendingShops > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600 animate-pulse">
                                Action needed
                            </span>
                            @endif
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Pending Approval</h3>
                        <p class="text-3xl font-bold {{ $pendingShops > 0 ? 'text-yellow-600' : 'text-gray-900' }}">{{ number_format($pendingShops) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-yellow-600 font-medium">Awaiting review</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-yellow-400 to-orange-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Approved Shops -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.3s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Approved Shops</h3>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($approvedShops) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-green-600 font-medium">Active & running</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-green-400 to-emerald-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Total Users -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.4s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-purple-400 to-pink-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Users</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-purple-600 font-medium">Registered users</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-purple-400 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Unassigned Users -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.5s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-cyan-400 to-teal-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Unassigned Users</h3>
                        <p class="text-3xl font-bold text-cyan-600">{{ number_format($unassignedUsers) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-cyan-600 font-medium">Without a shop</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-cyan-400 to-teal-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 animate-slide-up" style="animation-delay: 0.2s">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                            <p class="text-xs text-gray-500">Manage shops and users</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Create Shop Button -->
                        <a href="{{ route('admin.shops.create') }}" class="group flex items-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 hover:border-green-400 transition-all duration-300 hover:shadow-lg transform hover:scale-105">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center mr-4 group-hover:rotate-6 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-green-800">Create Shop</p>
                                <p class="text-xs text-green-600">Add new shop & assign user</p>
                            </div>
                        </a>

                        <!-- View All Users Button -->
                        <a href="{{ route('admin.users.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200 hover:border-purple-400 transition-all duration-300 hover:shadow-lg transform hover:scale-105">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center mr-4 group-hover:rotate-6 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-purple-800">All Users</p>
                                <p class="text-xs text-purple-600">View & manage users</p>
                            </div>
                        </a>

                        <!-- View All Shops Button -->
                        <a href="#recent-shops" class="group flex items-center p-4 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border-2 border-indigo-200 hover:border-indigo-400 transition-all duration-300 hover:shadow-lg transform hover:scale-105">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center mr-4 group-hover:rotate-6 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-indigo-800">All Shops</p>
                                <p class="text-xs text-indigo-600">View recent shops</p>
                            </div>
                        </a>

                        <!-- System Stats -->
                        <div class="group flex items-center p-4 bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl border-2 border-gray-200 transition-all duration-300">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-500 to-slate-600 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">System Health</p>
                                <p class="text-xs text-green-600 flex items-center">
                                    <span class="h-2 w-2 rounded-full bg-green-500 mr-1 animate-pulse"></span>
                                    All systems operational
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Shops Section -->
            @if($pendingShops > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-yellow-200 overflow-hidden mb-8 animate-slide-up">
                <div class="px-6 py-5 border-b border-yellow-200 bg-gradient-to-r from-yellow-50 to-orange-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center mr-3 animate-pulse">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-yellow-800">Shops Awaiting Approval</h3>
                                <p class="text-xs text-yellow-600">{{ $pendingShops }} shop(s) need your attention</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800">
                            {{ $pendingShops }} pending
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Shop Details</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Owner</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pendingShopsList as $shop)
                            <tr class="hover:bg-gradient-to-r hover:from-yellow-50 hover:to-orange-50 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mr-4">
                                            <span class="text-lg font-bold text-gray-600">{{ strtoupper(substr($shop->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $shop->name }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $shop->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center mr-2">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $shop->users->where('role', 'shop_admin')->first()?->email ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $shop->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $shop->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <form action="{{ route('admin.shops.approve', $shop) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-sm font-medium hover:from-green-600 hover:to-emerald-700 transition-all transform hover:scale-105 shadow-sm hover:shadow-md">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.shops.reject', $shop) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg text-sm font-medium hover:from-red-600 hover:to-rose-700 transition-all transform hover:scale-105 shadow-sm hover:shadow-md">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <!-- All Clear Message -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200 mb-8 animate-fade-in">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-green-800">All caught up! ✨</h4>
                        <p class="text-sm text-green-600">No pending shop approvals at the moment.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Shops -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 0.3s">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Recent Shops</h3>
                                <p class="text-xs text-gray-500">Latest registered shops</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Shop</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Users</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentShops as $shop)
                            <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center mr-3">
                                            <span class="text-sm font-bold text-indigo-600">{{ strtoupper(substr($shop->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $shop->name }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $shop->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($shop->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Approved
                                    </span>
                                    @elseif($shop->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                        Pending
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                        Rejected
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $shop->users->count() }} users
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $shop->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $shop->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500">No shops registered yet</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
        .animate-slide-up { animation: slide-up 0.6s ease-out forwards; }
        .stat-card { animation: fade-in 0.5s ease-out forwards; opacity: 0; }
    </style>

    <script>
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
            });
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</x-app-layout>
