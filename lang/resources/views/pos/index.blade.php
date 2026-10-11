@php
    // Catalog for the clickable product picker (prices are still re-read from the DB when the sale is saved).
    $catalog = collect($products ?? [])->map(fn ($p) => [
        'id'    => $p->id,
        'name'  => (string) $p->name,
        'sku'   => (string) ($p->sku ?? ''),
        'price' => (float) $p->selling_price,
        'stock' => (float) $p->stock,
        'min'   => (float) ($p->minimum_stock ?? 0),
    ])->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-green-800 leading-tight">{{ __('Point of Sale') }}</h2>
            <span class="inline-flex items-center gap-2 text-sm font-medium text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                {{ __('Scanner ready') }}
            </span>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-indigo-50 via-white to-emerald-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-5 gap-6">

            <!-- LEFT: scanner + cart -->
            <div class="lg:col-span-3 space-y-6">

                <!-- Scanner -->
                <div class="rounded-xl shadow-md overflow-hidden bg-white border border-indigo-100">
                    <div class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6v12M8 6v12M12 6v12M16 6v12M20 6v12"/>
                        </svg>
                        <label for="scanInput" class="font-medium">{{ __('Scan barcode / SKU') }}</label>
                    </div>
                    <div class="p-6">
                        <input id="scanInput" type="text" autocomplete="off" autofocus
                               placeholder="{{ __('Scan an item or type a code and press Enter') }}"
                               class="w-full rounded-lg border-2 border-indigo-200 bg-indigo-50/40 shadow-sm text-lg py-3 focus:border-indigo-500 focus:ring-indigo-500">
                        <p id="scanStatus" class="mt-3 text-sm font-medium min-h-[1.5rem] text-gray-500"></p>
                    </div>
                </div>

                <!-- Cart -->
                <div class="rounded-xl shadow-md overflow-hidden bg-white border border-gray-100">
                    <div class="px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 text-white flex items-center justify-between">
                        <span class="font-medium">{{ __('Current Sale') }}</span>
                        <span class="text-sm bg-white/25 px-3 py-0.5 rounded-full">
                            <span id="itemCount">0</span> {{ __('items') }}
                        </span>
                    </div>

                    <div class="p-6 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-sky-700 bg-sky-50">
                                    <th class="py-2 px-2 rounded-l-md">{{ __('Product') }}</th>
                                    <th class="py-2 px-2 w-28">{{ __('Qty') }}</th>
                                    <th class="py-2 px-2 text-right">{{ __('Price') }}</th>
                                    <th class="py-2 px-2 text-right">{{ __('Total') }}</th>
                                    <th class="py-2 px-2 w-10 rounded-r-md"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                        <div id="emptyCart" class="py-12 text-center">
                            <div class="mx-auto mb-3 h-14 w-14 rounded-full bg-sky-100 flex items-center justify-center">
                                <svg class="h-7 w-7 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3c-.6.6-.2 1.7.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-400">{{ __('Cart is empty. Scan an item or pick one from the list.') }}</p>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="px-6 py-5 bg-gradient-to-r from-emerald-50 to-teal-50 border-t border-emerald-100">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-medium text-gray-700">{{ __('Total') }}</span>
                            <span id="grandTotal" class="text-4xl font-extrabold text-emerald-600">RWF 0.00</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <button id="clearBtn" type="button"
                                    class="px-4 py-3 rounded-lg font-medium text-rose-700 bg-rose-100 hover:bg-rose-200 transition">
                                {{ __('Clear') }}
                            </button>
                            <button id="completeBtn" type="button" disabled
                                    class="col-span-2 px-4 py-3 rounded-lg text-white font-semibold text-lg bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 shadow-md transition disabled:opacity-40 disabled:cursor-not-allowed">
                                {{ __('Complete Sale') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: product picker -->
            <div class="lg:col-span-2">
                <div class="rounded-xl shadow-md overflow-hidden bg-white border border-amber-100 lg:sticky lg:top-4">
                    <div class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white flex items-center justify-between">
                        <span class="font-medium">{{ __('Choose Products') }}</span>
                        <span class="text-sm bg-white/25 px-3 py-0.5 rounded-full">
                            <span id="productCount">0</span>
                        </span>
                    </div>
                    <div class="p-4">
                        <input id="productSearch" type="search" autocomplete="off"
                               placeholder="{{ __('Search by name or SKU...') }}"
                               class="w-full rounded-lg border-2 border-amber-200 bg-amber-50/50 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <div id="productGrid" class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[60vh] overflow-y-auto pr-1"></div>
                        <p id="noProducts" class="hidden py-8 text-center text-gray-400 text-sm">{{ __('No products match your search.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const LOOKUP_URL = @json(route('pos.lookup'));
        const STORE_URL  = @json(route('pos.store'));
        const CSRF       = @json(csrf_token());
        const catalog    = @json($catalog);

        const cart = new Map(); // product_id -> {id, name, price, stock, qty}
        let flashId = null;

        const scanInput = document.getElementById('scanInput');
        const statusEl  = document.getElementById('scanStatus');
        const cartBody  = document.getElementById('cartBody');
        const grid      = document.getElementById('productGrid');
        const searchEl  = document.getElementById('productSearch');

        /* ---------- Beep (Web Audio, no sound files needed) ---------- */
        let audioCtx = null;
        const TONES = {
            scan:  { freq: 1800, type: 'sine',     len: 0.12 },
            pick:  { freq: 1200, type: 'triangle', len: 0.10 },
            error: { freq: 300,  type: 'square',   len: 0.40 },
        };
        function beep(kind = 'scan') {
            try {
                const t = TONES[kind] || TONES.scan;
                audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
                if (audioCtx.state === 'suspended') audioCtx.resume();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = t.type;
                osc.frequency.value = t.freq;
                gain.gain.value = 0.18;
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + t.len);
            } catch (e) { /* audio blocked: ignore */ }
        }

        function setStatus(text, ok = true) {
            statusEl.textContent = text;
            statusEl.className = 'mt-3 text-sm font-medium min-h-[1.5rem] ' + (ok ? 'text-emerald-600' : 'text-rose-600');
        }

        const money = v => 'RWF ' + v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        /* ---------- Product picker ---------- */
        function stockBadge(p) {
            if (p.stock <= 0)         return ['Out of stock', 'bg-rose-100 text-rose-700'];
            if (p.stock <= p.min)     return ['Low: ' + p.stock, 'bg-amber-100 text-amber-700'];
            return ['In stock: ' + p.stock, 'bg-emerald-100 text-emerald-700'];
        }

        function renderProducts() {
            const q = searchEl.value.trim().toLowerCase();
            const list = catalog.filter(p =>
                !q || p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));

            grid.innerHTML = '';
            document.getElementById('productCount').textContent = list.length;
            document.getElementById('noProducts').classList.toggle('hidden', list.length > 0);

            list.forEach(p => {
                const [label, badgeClass] = stockBadge(p);
                const out = p.stock <= 0;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'text-left rounded-lg border-2 p-3 transition ' + (out
                    ? 'border-gray-200 bg-gray-50 opacity-60'
                    : 'border-indigo-100 bg-white hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md active:scale-95');
                btn.innerHTML = `
                    <div class="font-semibold text-gray-900 text-sm leading-snug"></div>
                    <div class="text-xs text-gray-400 mt-0.5"></div>
                    <div class="mt-2 flex items-center justify-between gap-2">
                        <span class="font-bold text-indigo-600 text-sm">${money(p.price)}</span>
                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full ${badgeClass}">${label}</span>
                    </div>`;
                btn.children[0].textContent = p.name;
                btn.children[1].textContent = p.sku ? 'SKU: ' + p.sku : '';
                btn.addEventListener('click', () => {
                    addToCart(p, 'pick');
                    scanInput.focus();
                });
                grid.appendChild(btn);
            });
        }
        searchEl.addEventListener('input', renderProducts);

        /* ---------- Scan handling ---------- */
        scanInput.addEventListener('keydown', async (e) => {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            const code = scanInput.value.trim();
            scanInput.value = '';
            if (!code) return;

            try {
                const res = await fetch(LOOKUP_URL + '?code=' + encodeURIComponent(code), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (!res.ok || !data.found) {
                    beep('error');
                    setStatus(data.message || 'Product not found.', false);
                    return;
                }
                addToCart(data.product, 'scan');
            } catch (err) {
                beep('error');
                setStatus('Network error. Try again.', false);
            }
        });

        function addToCart(p, source = 'scan') {
            const line = cart.get(p.id) || { id: p.id, name: p.name, price: p.price, stock: p.stock, qty: 0 };

            if (line.qty + 1 > p.stock) {
                beep('error');
                setStatus(p.stock <= 0 ? `${p.name} is out of stock.` : `Only ${p.stock} of ${p.name} in stock.`, false);
                return;
            }

            line.qty += 1;
            line.stock = p.stock;
            cart.set(p.id, line);
            flashId = p.id;
            beep(source);
            setStatus(`Added: ${p.name}`, true);
            render();
        }

        /* ---------- Cart rendering ---------- */
        function render() {
            cartBody.innerHTML = '';
            let total = 0, count = 0;

            cart.forEach(line => {
                const lineTotal = line.qty * line.price;
                total += lineTotal;
                count += line.qty;

                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-100 transition-colors duration-700 ' +
                    (line.id === flashId ? 'bg-emerald-100' : 'hover:bg-sky-50/60');
                tr.innerHTML = `
                    <td class="py-3 px-2 font-medium text-gray-900"></td>
                    <td class="py-3 px-2">
                        <input type="number" min="0.01" step="0.01" value="${line.qty}"
                               class="w-24 rounded-md border-sky-200 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </td>
                    <td class="py-3 px-2 text-right text-gray-600">${money(line.price)}</td>
                    <td class="py-3 px-2 text-right font-bold text-indigo-700">${money(lineTotal)}</td>
                    <td class="py-3 px-2 text-right">
                        <button type="button" title="Remove"
                                class="h-8 w-8 rounded-full text-rose-600 bg-rose-50 hover:bg-rose-100 text-lg leading-none">&times;</button>
                    </td>`;
                tr.children[0].textContent = line.name; // textContent: no HTML injection

                tr.querySelector('input').addEventListener('change', (ev) => {
                    let q = parseFloat(ev.target.value) || 0;
                    if (q <= 0) {
                        cart.delete(line.id);
                    } else if (q > line.stock) {
                        beep('error');
                        setStatus(`Only ${line.stock} in stock.`, false);
                        line.qty = line.stock;
                    } else {
                        line.qty = q;
                    }
                    flashId = null;
                    render();
                    scanInput.focus();
                });
                tr.querySelector('button').addEventListener('click', () => {
                    cart.delete(line.id);
                    flashId = null;
                    render();
                    scanInput.focus();
                });

                cartBody.appendChild(tr);
            });

            document.getElementById('grandTotal').textContent = money(total);
            document.getElementById('itemCount').textContent = count;
            document.getElementById('emptyCart').style.display = cart.size ? 'none' : 'block';
            document.getElementById('completeBtn').disabled = cart.size === 0;

            // fade the highlight away
            if (flashId !== null) {
                const id = flashId;
                setTimeout(() => {
                    if (flashId === id) { flashId = null; render(); }
                }, 700);
            }
        }

        /* ---------- Save sale ---------- */
        document.getElementById('completeBtn').addEventListener('click', async (e) => {
            const btn = e.currentTarget;
            btn.disabled = true;

            const lines = [...cart.values()];
            const items = lines.map(l => ({ product_id: l.id, quantity: l.qty }));

            try {
                const res = await fetch(STORE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ items }),
                });
                const data = await res.json();

                if (!res.ok) {
                    const msg = data.errors ? Object.values(data.errors).flat()[0] : (data.message || 'Sale failed.');
                    beep('error');
                    setStatus(msg, false);
                    btn.disabled = false;
                    return;
                }

                beep('scan');
                setTimeout(() => beep('scan'), 180);

                // keep the picker's stock figures in step with the sale
                lines.forEach(l => {
                    const p = catalog.find(c => c.id === l.id);
                    if (p) p.stock = Math.max(0, p.stock - l.qty);
                });

                cart.clear();
                flashId = null;
                render();
                renderProducts();
                setStatus(`Sale #${data.sale_id} saved: ${money(data.total)}`, true);
            } catch (err) {
                beep('error');
                setStatus('Network error. Sale not saved.', false);
                btn.disabled = false;
            }
            scanInput.focus();
        });

        document.getElementById('clearBtn').addEventListener('click', () => {
            cart.clear();
            flashId = null;
            render();
            setStatus('');
            scanInput.focus();
        });

        /* Keep the scanner input focused so scans are never lost */
        document.addEventListener('click', (e) => {
            if (!e.target.closest('input, button, select, textarea')) scanInput.focus();
        });

        renderProducts();
        render();
    </script>
</x-app-layout>