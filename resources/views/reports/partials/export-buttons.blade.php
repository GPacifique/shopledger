{{-- Coloured buttons keep white text; Print uses the theme classes. No bottom margin: it sits inside the filter card. --}}
<div class="ui-no-print flex flex-wrap gap-2">

    <a href="{{ route('reports.export.pdf', request()->query()) }}"
       class="ui-focus inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
        PDF
    </a>

    <a href="{{ route('reports.export.excel', request()->query()) }}"
       class="ui-focus inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700">
        Excel
    </a>

    <a href="{{ route('reports.export.csv', request()->query()) }}"
       class="ui-focus inline-flex items-center rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600">
        CSV
    </a>

    {{-- data-print is handled by the script in partials/ui-theme (CSP-safe, no inline onclick) --}}
    <button type="button" data-print
            class="ui-btn-outline ui-focus inline-flex items-center rounded-lg px-4 py-2">
        Print
    </button>

</div>