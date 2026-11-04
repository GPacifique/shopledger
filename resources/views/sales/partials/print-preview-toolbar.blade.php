<nav class="print-toolbar" aria-label="{{ __('Print preview controls') }}">
    <a class="toolbar-link" href="{{ $previewReturnUrl }}">{{ __('Return to sale') }}</a>
    <label class="toolbar-control">
        <span>{{ __('Document') }}</span>
        <select id="print-document" aria-label="{{ __('Document type') }}">
            <option value="receipt" @selected($documentType === 'receipt')>{{ __('Receipt') }}</option>
            <option value="invoice" @selected($documentType === 'invoice')>{{ __('Invoice') }}</option>
        </select>
    </label>
    <label class="toolbar-control">
        <span>{{ __('Paper size') }}</span>
        <select id="print-paper" aria-label="{{ __('Paper size') }}">
            @foreach ($paperOptions as $paperValue => $paperLabel)
                <option value="{{ $paperValue }}" @selected($paperSize === $paperValue)>{{ $paperLabel }}</option>
            @endforeach
        </select>
    </label>
    <a id="download-pdf" class="toolbar-link" href="{{ route($pdfRouteName, ['sale' => $sale, 'document' => $documentType, 'paper' => $paperSize]) }}">{{ __('Download PDF') }}</a>
    <button class="toolbar-primary" type="button" id="print-document-button">{{ __('Print / Save PDF') }}</button>
</nav>
<script nonce="{{ Vite::cspNonce() }}">
    (() => {
        const documentSelect = document.getElementById('print-document');
        const paperSelect = document.getElementById('print-paper');
        const pdfLink = document.getElementById('download-pdf');
        const defaults = @json([
            'receipt' => $printSettings['receipt_paper_size'],
            'invoice' => $printSettings['invoice_paper_size'],
        ]);
        const updateUrl = () => {
            const url = new URL(window.location.href);
            url.searchParams.set('document', documentSelect.value);
            url.searchParams.set('paper', paperSelect.value);
            url.searchParams.delete('print');
            url.searchParams.delete('auto');
            window.location.assign(url);
        };
        documentSelect.addEventListener('change', () => {
            const nextDocument = documentSelect.value;
            const options = nextDocument === 'receipt'
                ? { '58mm': '58 mm thermal', '80mm': '80 mm thermal', custom: 'Custom width' }
                : { a4: 'A4', a5: 'A5', letter: 'Letter', custom: 'Custom size' };
            paperSelect.replaceChildren(...Object.entries(options).map(([value, label]) => {
                const option = new Option(label, value);
                return option;
            }));
            paperSelect.value = defaults[nextDocument];
            updateUrl();
        });
        paperSelect.addEventListener('change', updateUrl);
        pdfLink.addEventListener('click', event => {
            event.preventDefault();
            const url = new URL(pdfLink.href);
            url.searchParams.set('document', documentSelect.value);
            url.searchParams.set('paper', paperSelect.value);
            window.location.assign(url);
        });
        document.getElementById('print-document-button').addEventListener('click', () => window.print());

        const params = new URLSearchParams(window.location.search);
        if (params.get('print') === '1') {
            window.addEventListener('load', () => window.print(), { once: true });
        }
        if (params.get('auto') === '1') {
            window.addEventListener('afterprint', () => window.close(), { once: true });
        }
    })();
</script>
