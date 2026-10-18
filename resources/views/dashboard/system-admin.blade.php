
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-600 to-indigo-700 text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ __('System Admin Dashboard') }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ __('Manage all shops and users') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden text-right sm:block">
                    <p id="current-date" class="text-xs text-gray-500"></p>
                    <p id="current-time" class="text-sm font-semibold text-gray-800"></p>
                </div>

                <a href="{{ route('admin.settings') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('Settings') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- Welcome --}}
            <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-700 via-purple-700 to-indigo-900 p-6 text-white shadow-lg sm:p-8">
                <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                    <div>
                        <p class="text-sm font-medium text-indigo-200">
                            {{ __('PLATFORM ADMINISTRATION') }}
                        </p>
                        <h1 class="mt-2 text-2xl font-bold text-sm text-blue-50 sm:text-3xl">
                            {{ __('Welcome back, :name', [
                                'name' => auth()->user()->name
                            ]) }}
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-indigo-100">
                            {{ __('Monitor your shops, review applications, and manage user accounts from one dashboard.') }}
                        </p>
                    </div>

                    <a href="{{ route('admin.shops.create') }}"
                       class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 font-semibold text-indigo-800 shadow transition hover:bg-indigo-50">
                        <span class="text-xl">+</span>
                        {{ __('Create Shop') }}
                    </a>
                </div>
            </section>

            {{-- Statistics --}}
            @php
                $statistics = [
                    [
                        'label' => __('Total Shops'),
                        'value' => $totalShops,
                        'description' => __('All registered shops'),
                        'color' => 'indigo',
                        'icon' => 'store',
                    ],
                    [
                        'label' => __('Pending Approval'),
                        'value' => $pendingShops,
                        'description' => __('Awaiting review'),
                        'color' => 'amber',
                        'icon' => 'clock',
                    ],
                    [
                        'label' => __('Approved Shops'),
                        'value' => $approvedShops,
                        'description' => __('Approved businesses'),
                        'color' => 'emerald',
                        'icon' => 'check',
                    ],
                    [
                        'label' => __('Total Users'),
                        'value' => $totalUsers,
                        'description' => __('Registered accounts'),
                        'color' => 'purple',
                        'icon' => 'users',
                    ],
                    [
                        'label' => __('Unassigned Users'),
                        'value' => $unassignedUsers,
                        'description' => __('Without a shop'),
                        'color' => 'cyan',
                        'icon' => 'user-plus',
                    ],
                ];
            @endphp

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                @foreach($statistics as $stat)
                    <div class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">
                                {{ $stat['label'] }}
                            </span>

                            <span class="flex h-11 w-11 items-center justify-center rounded-xl
                                @switch($stat['color'])
                                    @case('indigo') bg-indigo-100 text-indigo-700 @break
                                    @case('amber') bg-amber-100 text-amber-700 @break
                                    @case('emerald') bg-emerald-100 text-emerald-700 @break
                                    @case('purple') bg-purple-100 text-purple-700 @break
                                    @default bg-cyan-100 text-cyan-700
                                @endswitch">

                                @if($stat['icon'] === 'store')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M5 10V6h14v4M5 10v10h14V10M9 20v-6h6v6"/>
                                    </svg>
                                @elseif($stat['icon'] === 'clock')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                        <path stroke-linecap="round" stroke-width="2" d="M12 7v5l3 2"/>
                                    </svg>
                                @elseif($stat['icon'] === 'check')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif($stat['icon'] === 'users')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a6 6 0 00-9-5m-3 6H2v-1a7 7 0 0114 0v1zm1-13a4 4 0 11-8 0 4 4 0 018 0zm5 4a4 4 0 100-8"/>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm10-3v6m-3-3h6"/>
                                    </svg>
                                @endif
                            </span>
                        </div>

                        <p class="mt-5 text-3xl font-bold tracking-tight text-gray-900">
                            {{ number_format($stat['value']) }}
                        </p>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ $stat['description'] }}
                        </p>
                    </div>
                @endforeach
            </section>

            {{-- Quick Actions --}}
            <section>
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">
                        {{ __('Quick Actions') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Access common administration tasks.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('admin.shops.create') }}"
                       class="rounded-xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:border-emerald-400 hover:shadow-md">
                        <span class="text-sm font-semibold text-emerald-700">
                            {{ __('Register Shop') }} →
                        </span>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('Create a shop account.') }}
                        </p>
                    </a>

                    <a href="{{ route('admin.shops.index') }}"
                       class="rounded-xl border border-indigo-200 bg-white p-5 shadow-sm transition hover:border-indigo-400 hover:shadow-md">
                        <span class="text-sm font-semibold text-indigo-700">
                            {{ __('Manage Shops') }} →
                        </span>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('View and review shop registrations.') }}
                        </p>
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl border border-purple-200 bg-white p-5 shadow-sm transition hover:border-purple-400 hover:shadow-md">
                        <span class="text-sm font-semibold text-purple-700">
                            {{ __('Manage Users') }} →
                        </span>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('Review and update user accounts.') }}
                        </p>
                    </a>

                    <a href="{{ route('admin.settings') }}"
                       class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-gray-400 hover:shadow-md">
                        <span class="text-sm font-semibold text-gray-800">
                            {{ __('System Settings') }} →
                        </span>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('Open platform configuration.') }}
                        </p>
                    </a>
                </div>
            </section>

            {{-- Pending Approvals --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-3 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ __('Shops Awaiting Approval') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Review new shop registrations.') }}
                        </p>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-800">
                        {{ number_format($pendingShops) }} {{ __('pending') }}
                    </span>
                </div>

                @if($pendingShopsList->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">
                            {{ __('All caught up!') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('There are no shops awaiting approval.') }}
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                        {{ __('Shop') }}
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                        {{ __('Owner') }}
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                        {{ __('Submitted') }}
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach($pendingShopsList as $shop)
                                    <tr class="transition hover:bg-amber-50/50">
                                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 font-bold text-indigo-700">
                                                    {{ strtoupper(substr($shop->business_name ?? 'SH', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ $shop->business_name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $shop->slug }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-5 py-4 sm:px-6">
                                            <p class="text-sm text-gray-700">
                                                {{ $shop->users->where('role', 'shop_admin')->first()?->name ?? __('Not assigned') }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $shop->users->where('role', 'shop_admin')->first()?->email ?? '—' }}
                                            </p>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                            <p class="text-sm text-gray-700">
                                                {{ $shop->created_at?->format('d M Y') ?? '—' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $shop->created_at?->diffForHumans() ?? '' }}
                                            </p>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                                            <div class="flex justify-end gap-2">
                                                <form action="{{ route('admin.shops.approve', $shop) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Approve this shop?') }}')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                                        {{ __('Approve') }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.shops.reject', $shop) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Reject this shop?') }}')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                                        {{ __('Reject') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Recent Shops --}}
            <section id="recent-shops" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-3 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ __('Recent Shops') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Latest registered businesses on the platform.') }}
                        </p>
                    </div>

                    <a href="{{ route('admin.shops.index') }}"
                       class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                        {{ __('View All Shops') }} →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                    {{ __('Shop') }}
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                    {{ __('Status') }}
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                    {{ __('Users') }}
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                                    {{ __('Registered') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentShops as $shop)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-sm font-bold text-indigo-700">
                                                {{ strtoupper(substr($shop->business_name ?? 'SH', 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $shop->business_name }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $shop->slug }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                        @php
                                            $status = strtolower($shop->status ?? 'unknown');
                                        @endphp

                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                            {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ !in_array($status, ['approved', 'pending', 'rejected']) ? 'bg-gray-100 text-gray-700' : '' }}">
                                            {{ __(ucfirst($status)) }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-800">
                                            {{ $shop->users_count ?? $shop->users->count() }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600 sm:px-6">
                                        {{ $shop->created_at?->format('d M Y') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                        {{ __('No shops have been registered yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <footer class="pb-4 text-center text-xs text-gray-400">
                © {{ date('Y') }} Mahwi POS. {{ __('System Administration') }}.
            </footer>
        </div>
    </div>

    <script>
        (() => {
            const dateElement = document.getElementById('current-date');
            const timeElement = document.getElementById('current-time');

            function updateClock() {
                const now = new Date();

                if (dateElement) {
                    dateElement.textContent = now.toLocaleDateString(undefined, {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }

                if (timeElement) {
                    timeElement.textContent = now.toLocaleTimeString(undefined, {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }
            }

            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
</x-app-layout>
