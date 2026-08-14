<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800">{{ __('Create Account') }}</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-2">{{ __('Start managing your shop today') }}</p>
    </div>

    <!-- Registration Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-4 sm:space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input 
                id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                required 
                autocomplete="new-password"
                placeholder="••••••••" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input 
                id="password_confirmation" 
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="••••••••" 
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="pt-4 sm:pt-6">
            <x-primary-button class="w-full justify-center py-2.5 sm:py-3 text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Login Link -->
    <div class="mt-5 sm:mt-6 text-center">
        <p class="text-xs sm:text-sm text-gray-600">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold transition duration-200">
                {{ __('Sign in') }}
            </a>
        </p>
    </div>

    <!-- Features Summary -->
    <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t border-gray-200">
        <p class="text-xs sm:text-sm text-gray-600 text-center mb-4 font-medium">{{ __('What you\'ll get:') }}</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Sales Tracking -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center p-3 rounded-lg hover:bg-gray-50 transition duration-200">
                <svg class="w-5 h-5 sm:w-4 sm:h-4 text-green-500 mr-0 sm:mr-2 mb-2 sm:mb-0 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs sm:text-sm text-gray-700">{{ __('Sales Tracking') }}</span>
            </div>

            <!-- Inventory -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center p-3 rounded-lg hover:bg-gray-50 transition duration-200">
                <svg class="w-5 h-5 sm:w-4 sm:h-4 text-green-500 mr-0 sm:mr-2 mb-2 sm:mb-0 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs sm:text-sm text-gray-700">{{ __('Inventory') }}</span>
            </div>

            <!-- Staff Roles -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center p-3 rounded-lg hover:bg-gray-50 transition duration-200">
                <svg class="w-5 h-5 sm:w-4 sm:h-4 text-green-500 mr-0 sm:mr-2 mb-2 sm:mb-0 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs sm:text-sm text-gray-700">{{ __('Staff Roles') }}</span>
            </div>

            <!-- Profit Reports -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center p-3 rounded-lg hover:bg-gray-50 transition duration-200">
                <svg class="w-5 h-5 sm:w-4 sm:h-4 text-green-500 mr-0 sm:mr-2 mb-2 sm:mb-0 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs sm:text-sm text-gray-700">{{ __('Profit Reports') }}</span>
            </div>
        </div>
    </div>
</x-guest-layout>
