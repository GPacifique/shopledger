{{--
    resources/views/partials/ui-theme.blade.php

    Shared light/dark classes for the report and product pages.
    Colours come from the same tokens as layouts/app.blade.php (`html.dark` swaps them),
    so every page that includes this partial follows the header theme toggle.
    @once makes it safe to include from several places on one page.
--}}
@once
<style>
        :root {
            --u-page:   #f8fafc;
            --u-card:   var(--surface, #ffffff);
            --u-card-2: var(--surface-2, #f8fafc);
            --u-text:   var(--text, #0f172a);
            --u-soft:   var(--text-soft, #475569);
            --u-muted:  var(--text-muted, #64748b);
            --u-faint:  #94a3b8;
            --u-bd:     var(--border, #e2e8f0);
            --u-line:   var(--border-soft, #f1f5f9);
            --u-hover:  var(--hover, #f1f5f9);
            --u-link:   #4f46e5;
            --u-link-h: #3730a3;
            --u-input-bd: #cbd5e1;
            --u-ring:   #6366f1;
            --u-btn-bg: #0f172a;
            --u-btn-tx: #ffffff;
            --u-btn-h:  #334155;
        }
        html.dark {
            --u-page:   #0e1626;
            --u-card:   #162033;
            --u-card-2: #1b2740;
            --u-faint:  #64748b;
            --u-bd:     #2a3648;
            --u-line:   #243049;
            --u-hover:  #222f47;
            --u-link:   #a5b4fc;
            --u-link-h: #c7d2fe;
            --u-input-bd: #3b4a63;
            --u-ring:   #818cf8;
            --u-btn-bg: #e2e8f0;
            --u-btn-tx: #0f172a;
            --u-btn-h:  #ffffff;
        }

        /* ---- Surfaces ---- */
        .ui-page      { background-color: var(--u-page); }
        .ui-card      { background-color: var(--u-card);   border: 1px solid var(--u-bd); }
        .ui-card-2    { background-color: var(--u-card-2); border: 1px solid var(--u-bd); }
        .ui-surface-2 { background-color: var(--u-card-2); }
        .ui-surface   { background-color: var(--u-card); }
        .ui-bd        { border-color: var(--u-bd); }
        .ui-line      { border-color: var(--u-line); }
        .ui-divide > * + * { border-top: 1px solid var(--u-line); }
        .ui-hover:hover { background-color: var(--u-hover); }

        /* ---- Text ---- */
        .ui-text  { color: var(--u-text); }
        .ui-soft  { color: var(--u-soft); }
        .ui-muted { color: var(--u-muted); }
        .ui-faint { color: var(--u-faint); }
        .ui-link  { color: var(--u-link); transition: color .15s ease; }
        .ui-link:hover { color: var(--u-link-h); }
        .tx { color: var(--t-tx); }
        .st { color: var(--t-st); }

        /* ---- Buttons ---- */
        .ui-btn-dark    { background-color: var(--u-btn-bg); color: var(--u-btn-tx); transition: background-color .15s ease; }
        .ui-btn-dark:hover { background-color: var(--u-btn-h); }
        .ui-btn-outline { background-color: var(--u-card); color: var(--u-soft); border: 1px solid var(--u-input-bd); transition: background-color .15s ease, color .15s ease; }
        .ui-btn-outline:hover { background-color: var(--u-hover); color: var(--u-text); }
        .ui-icon-btn    { color: var(--u-faint); transition: background-color .15s ease, color .15s ease; }
        .ui-icon-btn:hover { background-color: var(--u-hover); color: var(--u-text); }
        .ui-focus:focus-visible { outline: 2px solid var(--u-ring); outline-offset: 2px; }

        /* Small bordered action (tinted by a .tone-* class) */
        .ui-act { border: 1px solid var(--t-bd); color: var(--t-tx); transition: background-color .15s ease; }
        .ui-act:hover { background-color: var(--t-bg); }

        /* ---- Forms ---- */
        .ui-label { color: var(--u-soft); }
        .ui-input {
            background-color: var(--u-card);
            color: var(--u-text);
            border: 1px solid var(--u-input-bd);
        }
        .ui-input::placeholder { color: var(--u-faint); }
        .ui-input:focus { border-color: var(--u-ring); outline: none; box-shadow: 0 0 0 1px var(--u-ring); }
        .ui-input::file-selector-button {
            margin-right: 1rem;
            padding: .75rem 1rem;
            border: 0;
            background-color: var(--u-hover);
            color: var(--u-soft);
            font-weight: 600;
            cursor: pointer;
        }
        .ui-input::file-selector-button:hover { background-color: var(--u-bd); }

        /* ---- Tints, badges, icons (use with a .tone-* class) ---- */
        .ui-tint  { background-color: var(--t-bg); border: 1px solid var(--t-bd); }
        .ui-badge { background-color: var(--t-ic); color: var(--t-tx); }
        .ui-icon  { background-color: var(--t-bg); color: var(--t-st); transition: background-color .15s ease; }
        .group:hover .ui-icon { background-color: var(--t-ic); }
        .ui-arrow { color: var(--u-faint); transition: color .15s ease, transform .15s ease; }
        .group:hover .ui-arrow { color: var(--t-st); }
        .ui-card-lift { transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
        .ui-card-lift:hover { border-color: var(--t-bd); }
        .ui-icon-circle { background-color: var(--u-hover); color: var(--u-faint); }

        /* ---- Tables ---- */
        .ui-table thead { background-color: var(--u-card-2); border-bottom: 1px solid var(--u-bd); }
        .ui-table th    { color: var(--u-muted); }
        .ui-table tbody { background-color: var(--u-card); }
        .ui-table tbody tr + tr { border-top: 1px solid var(--u-line); }
        .ui-table tbody tr:hover { background-color: var(--u-card-2); }
        .ui-table tfoot { background-color: var(--u-card-2); border-top: 1px solid var(--u-bd); }

        /* ---- Menus & modals ---- */
        .ui-menu       { background-color: var(--u-card); border: 1px solid var(--u-bd); }
        .ui-menu-item  { color: var(--u-soft); transition: background-color .15s ease, color .15s ease; }
        .ui-menu-item:hover { background-color: var(--u-hover); color: var(--u-text); }
        .ui-modal      { background-color: var(--u-card); color: var(--u-text); border: 1px solid var(--u-bd); }
        .ui-modal-foot { background-color: var(--u-card-2); border-top: 1px solid var(--u-bd); }

        /* ---- Tone palette ---- */
        .tone-green{--t-bg:#f0fdf4;--t-bd:#dcfce7;--t-tx:#15803d;--t-st:#16a34a;--t-ic:#dcfce7;}
        .tone-emerald{--t-bg:#ecfdf5;--t-bd:#d1fae5;--t-tx:#047857;--t-st:#059669;--t-ic:#d1fae5;}
        .tone-red{--t-bg:#fef2f2;--t-bd:#fee2e2;--t-tx:#b91c1c;--t-st:#dc2626;--t-ic:#fee2e2;}
        .tone-orange{--t-bg:#fff7ed;--t-bd:#ffedd5;--t-tx:#c2410c;--t-st:#ea580c;--t-ic:#ffedd5;}
        .tone-amber{--t-bg:#fffbeb;--t-bd:#fde68a;--t-tx:#b45309;--t-st:#d97706;--t-ic:#fef3c7;}
        .tone-indigo{--t-bg:#eef2ff;--t-bd:#e0e7ff;--t-tx:#4338ca;--t-st:#4f46e5;--t-ic:#e0e7ff;}
        .tone-violet{--t-bg:#f5f3ff;--t-bd:#ede9fe;--t-tx:#6d28d9;--t-st:#7c3aed;--t-ic:#ede9fe;}
        .tone-purple{--t-bg:#faf5ff;--t-bd:#f3e8ff;--t-tx:#7e22ce;--t-st:#9333ea;--t-ic:#f3e8ff;}
        .tone-teal{--t-bg:#f0fdfa;--t-bd:#ccfbf1;--t-tx:#0f766e;--t-st:#0d9488;--t-ic:#ccfbf1;}
        .tone-blue{--t-bg:#eff6ff;--t-bd:#dbeafe;--t-tx:#1d4ed8;--t-st:#2563eb;--t-ic:#dbeafe;}
        .tone-yellow{--t-bg:#fefce8;--t-bd:#fef9c3;--t-tx:#a16207;--t-st:#ca8a04;--t-ic:#fef9c3;}
        .tone-gray{--t-bg:var(--u-hover);--t-bd:var(--u-bd);--t-tx:var(--u-soft);--t-st:var(--u-muted);--t-ic:var(--u-hover);}
        html.dark .tone-green{--t-bg:rgb(34 197 94 / .12);--t-bd:rgb(34 197 94 / .30);--t-tx:#86efac;--t-st:#4ade80;--t-ic:rgb(34 197 94 / .22);}
        html.dark .tone-emerald{--t-bg:rgb(16 185 129 / .12);--t-bd:rgb(16 185 129 / .30);--t-tx:#6ee7b7;--t-st:#34d399;--t-ic:rgb(16 185 129 / .22);}
        html.dark .tone-red{--t-bg:rgb(239 68 68 / .12);--t-bd:rgb(239 68 68 / .30);--t-tx:#fca5a5;--t-st:#f87171;--t-ic:rgb(239 68 68 / .22);}
        html.dark .tone-orange{--t-bg:rgb(249 115 22 / .12);--t-bd:rgb(249 115 22 / .30);--t-tx:#fdba74;--t-st:#fb923c;--t-ic:rgb(249 115 22 / .22);}
        html.dark .tone-amber{--t-bg:rgb(245 158 11 / .12);--t-bd:rgb(245 158 11 / .30);--t-tx:#fcd34d;--t-st:#fbbf24;--t-ic:rgb(245 158 11 / .22);}
        html.dark .tone-indigo{--t-bg:rgb(99 102 241 / .12);--t-bd:rgb(99 102 241 / .30);--t-tx:#a5b4fc;--t-st:#818cf8;--t-ic:rgb(99 102 241 / .22);}
        html.dark .tone-violet{--t-bg:rgb(139 92 246 / .12);--t-bd:rgb(139 92 246 / .30);--t-tx:#c4b5fd;--t-st:#a78bfa;--t-ic:rgb(139 92 246 / .22);}
        html.dark .tone-purple{--t-bg:rgb(168 85 247 / .12);--t-bd:rgb(168 85 247 / .30);--t-tx:#d8b4fe;--t-st:#c084fc;--t-ic:rgb(168 85 247 / .22);}
        html.dark .tone-teal{--t-bg:rgb(20 184 166 / .12);--t-bd:rgb(20 184 166 / .30);--t-tx:#5eead4;--t-st:#2dd4bf;--t-ic:rgb(20 184 166 / .22);}
        html.dark .tone-blue{--t-bg:rgb(59 130 246 / .12);--t-bd:rgb(59 130 246 / .30);--t-tx:#93c5fd;--t-st:#60a5fa;--t-ic:rgb(59 130 246 / .22);}
        html.dark .tone-yellow{--t-bg:rgb(234 179 8 / .12);--t-bd:rgb(234 179 8 / .30);--t-tx:#fde047;--t-st:#facc15;--t-ic:rgb(234 179 8 / .22);}

        /* ---- Printing: always light, without the app chrome ---- */
        @media print {
            .ui-no-print, #app-sidebar, .app-header { display: none !important; }
            .app-shell { padding-left: 0 !important; }
            .ui-page, .app-main { background: #fff !important; }
        }
</style>

<script nonce="{{ Vite::cspNonce() }}">
    (() => {
        // [data-print] buttons (CSP-safe replacement for onclick="window.print()")
        document.addEventListener('click', (e) => {
            if (e.target instanceof Element && e.target.closest('[data-print]')) window.print();
        });

        // Print in light colours even when the screen is in dark mode, then restore.
        let restoreDark = false;
        window.addEventListener('beforeprint', () => {
            const root = document.documentElement;
            restoreDark = root.classList.contains('dark');
            if (restoreDark) root.classList.remove('dark');
        });
        window.addEventListener('afterprint', () => {
            if (restoreDark) document.documentElement.classList.add('dark');
            restoreDark = false;
        });
    })();
</script>
@endonce