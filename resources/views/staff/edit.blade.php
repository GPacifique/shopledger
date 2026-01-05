<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('staff.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 transition">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Staff') }}: {{ $staff->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <form method="POST" action="{{ route('staff.update', $staff) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="text-center pb-6 border-b border-gray-200">
                        <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                        </div>
                        <p class="mt-2 text-sm text-gray-500">{{ __('Editing') }} {{ $staff->email }}</p>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Full Name') }} *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $staff->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email Address') }} *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">{{ __('Role') }} *</label>
                            <select name="role" id="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="seller" {{ old('role', $staff->role) == 'seller' ? 'selected' : '' }}>{{ __('Seller') }}</option>
                                <option value="accountant" {{ old('role', $staff->role) == 'accountant' ? 'selected' : '' }}>{{ __('Accountant') }}</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_status" class="block text-sm font-medium text-gray-700">{{ __('Account Status') }} *</label>
                            <select name="account_status" id="account_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active" {{ old('account_status', $staff->account_status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="suspended" {{ old('account_status', $staff->account_status) == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                            </select>
                            @error('account_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>{{ __('Change Password (Optional)') }}</strong> - {{ __('Leave blank to keep current password') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
                            <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Leave blank to keep current') }}">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Repeat new password') }}">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t">
                        <button type="button" onclick="document.getElementById('delete-form').submit()" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition">
                            <svg class="-ml-1 mr-2 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('Delete') }}
                        </button>
                        <div class="space-x-3">
                            <a href="{{ route('staff.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                {{ __('Update Staff') }}
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Hidden delete form -->
                <form id="delete-form" action="{{ route('staff.destroy', $staff) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this staff member? This action cannot be undone.') }}')">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
