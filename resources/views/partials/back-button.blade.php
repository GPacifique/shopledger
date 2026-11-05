<a
    href="{{ $fallbackUrl }}"
    data-app-back-button
    class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
    aria-label="{{ __('Back') }}"
>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    <span>{{ __('Back') }}</span>
</a>

<script nonce="{{ Vite::cspNonce() }}">
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-app-back-button]');

        if (!button) {
            return;
        }

        event.preventDefault();

        try {
            if (document.referrer && new URL(document.referrer).origin === window.location.origin && window.history.length > 1) {
                window.history.back();
                return;
            }
        } catch (error) {
            // Use the provided fallback for invalid referrers.
        }

        window.location.assign(button.href);
    });
</script>
