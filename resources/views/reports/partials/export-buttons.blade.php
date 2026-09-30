<div class="flex flex-wrap gap-2 mb-6">

    <a
        href="{{ route('reports.export.pdf', request()->query()) }}"
        class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">

        PDF
    </a>


    <a
        href="{{ route('reports.export.excel', request()->query()) }}"
        class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">

        Excel
    </a>


    <a
        href="{{ route('reports.export.csv', request()->query()) }}"
        class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-700 text-white hover:bg-slate-800">

        CSV
    </a>


    <button
        type="button"
        onclick="window.print()"
        class="inline-flex items-center px-4 py-2 rounded-lg border bg-white hover:bg-slate-50">

        Print
    </button>

</div>