<form method="GET"
      action="{{ $action ?? route('reports.index') }}"
      class="bg-white border rounded-xl p-5 mb-6">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Shop --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Shop
            </label>

            <select
                name="shop_id"
                class="w-full rounded-lg border-slate-300">

                <option value="">
                    Current Shop
                </option>

                @foreach(\App\Models\Shop::all() as $reportShop)

                    <option
                        value="{{ $reportShop->id }}"
                        @selected(request('shop_id') == $reportShop->id)
                    >
                        {{ $reportShop->name }}
                    </option>

                @endforeach

            </select>
        </div>


        {{-- From --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                From
            </label>

            <input
                type="date"
                name="date_from"
                value="{{ request('date_from', $startDate->format('Y-m-d')) }}"
                class="w-full rounded-lg border-slate-300"
            >
        </div>


        {{-- To --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                To
            </label>

            <input
                type="date"
                name="date_to"
                value="{{ request('date_to', $endDate->format('Y-m-d')) }}"
                class="w-full rounded-lg border-slate-300"
            >
        </div>


        {{-- Button --}}
        <div class="flex items-end">

            <button
                type="submit"
                class="w-full rounded-lg bg-slate-900 text-white px-4 py-2.5 hover:bg-slate-800">

                Generate Report

            </button>

        </div>

    </div>

</form>