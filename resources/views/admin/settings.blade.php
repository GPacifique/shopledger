{{-- resources/views/admin/settings.blade.php
     Expects: $settings (array of key => value). Submits to route('admin.settings.update') via PUT. --}}
@php
    $value = fn (string $key, $default = null) => old($key, $settings[$key] ?? $default);

    $input = 'mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
    $label = 'block text-sm font-medium text-gray-700';
    $hint  = 'mt-1 text-xs text-gray-500';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('System settings') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    <p class="font-semibold">{{ __('Some settings could not be saved:') }}</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6" autocomplete="off">
                @csrf
                @method('PUT')

                {{-- ============ General ============ --}}
                <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <header class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('General') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Name and language shown across the platform.') }}</p>
                    </header>

                    <div class="grid gap-5 p-5 sm:grid-cols-2">
                        <div>
                            <label for="app_name" class="{{ $label }}">{{ __('Platform name') }}</label>
                            <input id="app_name" name="app_name" type="text" required maxlength="80"
                                   value="{{ $value('app_name', 'MahWi') }}" class="{{ $input }}">
                            @error('app_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="default_locale" class="{{ $label }}">{{ __('Default language') }}</label>
                            <select id="default_locale" name="default_locale" class="{{ $input }}">
                                @foreach (['en' => 'English', 'fr' => 'Français', 'rw' => 'Kinyarwanda', 'sw' => 'Kiswahili'] as $code => $name)
                                    <option value="{{ $code }}" @selected($value('default_locale', 'en') === $code)>{{ $name }}</option>
                                @endforeach
                            </select>
                            <p class="{{ $hint }}">{{ __('Used for visitors who have not picked a language yet.') }}</p>
                            @error('default_locale') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="currency" class="{{ $label }}">{{ __('Currency code') }}</label>
                            <input id="currency" name="currency" type="text" required maxlength="3"
                                   value="{{ $value('currency', 'RWF') }}" class="{{ $input }} uppercase">
                            @error('currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- ============ Contact ============ --}}
                <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <header class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Support contact') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Shown on the landing page and contact form.') }}</p>
                    </header>

                    <div class="grid gap-5 p-5 sm:grid-cols-2">
                        <div>
                            <label for="support_email" class="{{ $label }}">{{ __('Support email') }}</label>
                            <input id="support_email" name="support_email" type="email"
                                   value="{{ $value('support_email') }}" class="{{ $input }}">
                            @error('support_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="support_phone" class="{{ $label }}">{{ __('Support phone') }}</label>
                            <input id="support_phone" name="support_phone" type="text" maxlength="30"
                                   value="{{ $value('support_phone') }}" class="{{ $input }}">
                            @error('support_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- ============ Subscription ============ --}}
                <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <header class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Shop subscription') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Applies to new shops. Existing shops keep their current plan.') }}</p>
                    </header>

                    <div class="grid gap-5 p-5 sm:grid-cols-2">
                        <div>
                            <label for="monthly_price" class="{{ $label }}">{{ __('Monthly price per shop') }}</label>
                            <input id="monthly_price" name="monthly_price" type="number" min="0" step="1" required
                                   value="{{ $value('monthly_price', 0) }}" class="{{ $input }}">
                            @error('monthly_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="trial_days" class="{{ $label }}">{{ __('Free trial (days)') }}</label>
                            <input id="trial_days" name="trial_days" type="number" min="0" max="365" step="1" required
                                   value="{{ $value('trial_days', 0) }}" class="{{ $input }}">
                            @error('trial_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- ============ Access ============ --}}
                <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <header class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Access') }}</h3>
                    </header>

                    <div class="space-y-5 p-5">
                        <label class="flex min-h-[44px] items-start gap-3">
                            <input type="hidden" name="registration_open" value="0">
                            <input type="checkbox" name="registration_open" value="1"
                                   @checked((bool) $value('registration_open', 1))
                                   class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="block text-sm font-medium text-gray-800">{{ __('Allow new shop registrations') }}</span>
                                <span class="{{ $hint }}">{{ __('Turn off to stop new sign-ups. Existing users can still log in.') }}</span>
                            </span>
                        </label>

                        <label class="flex min-h-[44px] items-start gap-3">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" name="maintenance_mode" value="1"
                                   @checked((bool) $value('maintenance_mode', 0))
                                   class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="block text-sm font-medium text-gray-800">{{ __('Show maintenance notice') }}</span>
                                <span class="{{ $hint }}">{{ __('Displays the message below to all shop users. It does not block access.') }}</span>
                            </span>
                        </label>

                        <div>
                            <label for="maintenance_message" class="{{ $label }}">{{ __('Notice message') }}</label>
                            <textarea id="maintenance_message" name="maintenance_message" rows="3" maxlength="500"
                                      class="{{ $input }}">{{ $value('maintenance_message') }}</textarea>
                            @error('maintenance_message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex min-h-[44px] items-center rounded-lg px-4 text-sm font-medium text-gray-600 hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                            class="inline-flex min-h-[44px] items-center rounded-lg bg-white px-5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        {{ __('Save settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>