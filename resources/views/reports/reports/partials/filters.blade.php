<form method="GET"
      action="{{ $action ?? route('reports.index') }}"
      class="ui-card mb-6 rounded-xl p-5">

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

        {{-- Shop --}}
        <div>
            <label for="filter-shop" class="ui-label mb-1 block text-sm font-medium">Shop</label>

            <select id="filter-shop" name="shop_id" class="ui-input w-full rounded-lg">
                <option value="">Current Shop</option>

                @foreach(\App\Models\Shop::all() as $reportShop)
                    <option value="{{ $reportShop->id }}" @selected(request('shop_id') == $reportShop->id)>
                        {{ $reportShop->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- From --}}
        <div>
            <label for="filter-from" class="ui-label mb-1 block text-sm font-medium">From</label>

            <input
                id="filter-from"
                type="date"
                name="date_from"
                value="{{ request('date_from', $startDate->format('Y-m-d')) }}"
                class="ui-input w-full rounded-lg"
            >
        </div>

        {{-- To --}}
        <div>
            <label for="filter-to" class="ui-label mb-1 block text-sm font-medium">To</label>

            <input
                id="filter-to"
                type="date"
                name="date_to"
                value="{{ request('date_to', $endDate->format('Y-m-d')) }}"
                class="ui-input w-full rounded-lg"
            >
        </div>

        {{-- Button --}}
        <div class="flex items-end">
            <button type="submit" class="ui-btn-dark ui-focus w-full rounded-lg px-4 py-2.5">
                Generate Report
            </button>
        </div>

    </div>

</form>
