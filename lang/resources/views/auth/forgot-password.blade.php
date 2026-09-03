<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Brand --}}
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg">
                        <span class="text-white text-xl font-bold">M</span>
                    </div>

                    <div class="text-left">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Mah<span class="text-indigo-600">Wi</span>
                        </h1>
                        <p class="text-xs text-gray-500">
                            Business Management System
                        </p>
                    </div>
                </a>
            </div>

            {{-- Forgot Password Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        Forgot your password?
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-gray-600">
                        No problem. Enter the email address associated with your MahWi
                        account and we will send you a secure password reset link.
                    </p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status
                    class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700"
                    :status="session('status')"
                />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    {{-- Email Address --}}
                    <div>
                        <label
                            for="email"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Email Address
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@example.com"
                            class="block w-full rounded-xl border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   px-4 py-3 text-gray-900 placeholder-gray-400"
                        />

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-6">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center
                                   rounded-xl bg-indigo-600 px-4 py-3
                                   text-sm font-semibold text-white
                                   shadow-lg shadow-indigo-200
                                   transition duration-200
                                   hover:bg-indigo-700
                                   focus:outline-none
                                   focus:ring-2 focus:ring-indigo-500
                                   focus:ring-offset-2"
                        >
                            Email Password Reset Link
                        </button>
                    </div>

                    {{-- Back to Login --}}
                    <div class="mt-6 text-center">
                        <a
                            href="{{ route('login') }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition"
                        >
                            ← Back to Sign In
                        </a>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} MahWi. Manage your business smarter.
                </p>
            </div>

        </div>
    </div>
</x-guest-layout>