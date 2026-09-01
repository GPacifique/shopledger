```blade
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-indigo-50 px-4 py-10">

        <div class="w-full max-w-md">

            {{-- MahWi Branding --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 shadow-lg shadow-indigo-200 mb-4">
                    <span class="text-2xl font-extrabold text-white">M</span>
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                    Mah<span class="text-indigo-600">Wi</span>
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Multi-Shop Business Management
                </p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-6 sm:p-8">

                {{-- Header --}}
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">
                        Forgot your password?
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        No problem. Enter the email address associated with your
                        MahWi account and we'll send you a secure link to reset
                        your password.
                    </p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status
                    class="mb-5 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
                    :status="session('status')"
                />

                {{-- Form --}}
                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    {{-- Email Address --}}
                    <div>
                        <x-input-label
                            for="email"
                            :value="__('Email Address')"
                            class="text-sm font-semibold text-slate-700"
                        />

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg
                                    class="h-5 w-5 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>

                            <x-text-input
                                id="email"
                                class="block w-full rounded-xl border-slate-300 pl-11 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="you@example.com"
                            />
                        </div>

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2"
                        />
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition duration-200 hover:bg-indigo-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 00-2 2H5a2 2 0 002-2V7a2 2 0 00-2 2H5z"
                                />
                            </svg>

                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </form>

                {{-- Back to Login --}}
                <div class="mt-6 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>

                        Back to Sign In
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">
                    © {{ date('Y') }} MahWi. All rights reserved.
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Manage your business. Grow with confidence.
                </p>
            </div>

        </div>
    </div>
</x-guest-layout>
```
