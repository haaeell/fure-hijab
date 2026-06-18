<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi {{ $label['waybill'] ?? $order->order_number }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        @page { size: 100mm auto; margin: 0; }
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f0f0f0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        /* ── Toolbar (hidden on print) ── */
        .toolbar {
            width: 100mm;
            margin: 14px auto 10px;
            display: flex;
            gap: 8px;
        }
        .toolbar a {
            flex: 1;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 8px;
            padding: 9px 12px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            color: #333;
            text-decoration: none;
            text-align: center;
        }
        .toolbar button {
            flex: 1;
            border: none;
            background: #A78B6F;
            color: #fff;
            border-radius: 8px;
            padding: 9px 12px;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
        }
        .toolbar button:hover { background: #5F4A3A; }

        /* ── Label wrapper ── */
        .label {
            width: 100mm;
            min-height: 150mm;        /* no fixed height — grows with content */
            margin: 0 auto 18px;
            background: #fff;
            border: 2px solid #000;
        }

        /* ── Rows / grid ── */
        .row { border-top: 2px solid #000; }
        .row:first-child { border-top: none; }
        .grid-2 { display: table; width: 100%; }
        .col-l, .col-r { display: table-cell; vertical-align: top; }
        .col-l { border-right: 2px solid #000; width: 50%; }
        .pad { padding: 6px 9px; }

        /* ── Typography ── */
        .label-title { font-size: 8px; font-weight: 900; letter-spacing: 1.5px; text-transform: uppercase; color: #777; margin-bottom: 3px; }
        .bold  { font-weight: 800; }
        .small { font-size: 10px; }
        .xs    { font-size: 9px; }
        .center { text-align: center; }
        .break { word-break: break-all; }

        /* ── Courier branding ── */
        .courier-area { font-size: 22px; font-weight: 900; letter-spacing: 1px; color: #1a3a8f; }
        .courier-logo-img { max-height: 28px; max-width: 60mm; object-fit: contain; }

        /* ── Barcode ── */
        .barcode-cell { text-align: center; padding: 6px 9px 4px; }
        .barcode-cell svg { max-width: 88mm; width: 100%; display: block; margin: 0 auto; }
        .resi-number { font-size: 13px; font-weight: 900; font-family: 'Courier New', Courier, monospace; letter-spacing: 2px; margin-top: 2px; }
        .ref-barcode-cell svg { max-width: 42mm; display: block; margin: 0 auto; }

        /* ── Address ── */
        .addr-name { font-size: 12px; font-weight: 900; margin-bottom: 2px; }
        .addr-phone { font-size: 10px; font-weight: 700; margin-bottom: 2px; }
        .addr-text  { font-size: 9px; line-height: 1.45; }

        /* ── Separator ── */
        .divider { border-top: 1px dashed #bbb; margin: 3px 0; }

        @media print {
            html, body { background: #fff; }
            .toolbar { display: none !important; }
            .label {
                width: 100%;
                min-height: 0;
                margin: 0;
                border: 2px solid #000;
            }
        }
    </style>
</head>

@php
    $opts             = $labelOptions ?? [];
    $showInsurance    = $opts['insurance']         ?? true;
    $showCost         = $opts['shipping_cost']     ?? true;
    $showItems        = $opts['item_description']  ?? true;
    $showSku          = $opts['item_sku']          ?? false;
    $showSenderPhone  = $opts['sender_phone']      ?? true;
    $showSenderAddr   = $opts['sender_address']    ?? true;
    $showRcvPhone     = $opts['receiver_phone']    ?? true;
    $autoPrint        = $opts['auto_print']        ?? false;

    $quantity   = $order->items->sum('qty');
    $weightKg   = number_format(max(1, ($label['weight'] ?? 1000)) / 1000, 1);
    $itemsText  = $order->items->map(fn($i) => $i->qty . 'x ' . $i->product_name . ($i->variant_name ? ' (' . $i->variant_name . ')' : ''))->implode(', ');
    $waybill    = $label['waybill'] ?? null;
    $reference  = $order->shipment->biteship_order_id ?? $order->order_number;

    // Courier logo from Courier model
    $courierModel = \App\Models\Courier::where('code', $order->shipment->courier ?? '')->first();
@endphp

<body>

{{-- Toolbar --}}
<div class="toolbar">
    <a href="{{ route('orders.show', $order->id) }}">← Kembali</a>
    <button onclick="doPrint()">🖨 Cetak Label</button>
</div>

{{-- Label --}}
<main class="label">

    {{-- Row 1: Kurir + Nomor Resi --}}
    <div class="pad grid-2">
        <div class="col-l center" style="padding:8px 6px;">
            @if($courierModel?->logo)
                <img src="{{ asset('storage/' . $courierModel->logo) }}" class="courier-logo-img" alt="{{ $courierModel->name }}">
            @else
                <div class="courier-area">{{ strtoupper($order->shipment->courier ?? '-') }}</div>
            @endif
            <div class="xs bold" style="margin-top:3px; letter-spacing:0.5px;">{{ strtoupper($label['service'] ?? '') }}</div>
        </div>
        <div class="col-r center" style="padding:8px 6px;">
            <div style="font-size:9px; color:#777; font-weight:700; letter-spacing:1px; text-transform:uppercase;">Estimasi</div>
            <div class="bold" style="font-size:13px;">{{ $label['estimated_days'] ?? '-' }} hari</div>
            @if($showCost)
                <div class="xs" style="margin-top:3px;">Ongkir: <span class="bold">Rp {{ number_format($label['cost'] ?? 0, 0, ',', '.') }}</span></div>
            @endif
        </div>
    </div>

    {{-- Row 2: Waybill barcode --}}
    <div class="row barcode-cell">
        @if($waybill)
            <svg id="waybillBarcode"></svg>
            <div class="resi-number">{{ $waybill }}</div>
        @else
            <div style="padding:10px 0; font-size:11px; color:#aaa;">Nomor resi belum tersedia</div>
        @endif
    </div>

    {{-- Row 3: Reference + Qty/Weight --}}
    <div class="row grid-2">
        <div class="col-l pad ref-barcode-cell">
            <div class="label-title">Referensi</div>
            <svg id="referenceBarcode"></svg>
            <div class="xs bold break">{{ $reference }}</div>
        </div>
        <div class="col-r pad" style="font-size:11px; line-height:2;">
            <div class="label-title">Paket</div>
            <div><span class="bold">Qty:</span> {{ $quantity }} pcs</div>
            <div><span class="bold">Berat:</span> {{ $weightKg }} kg</div>
            @if($showInsurance)
                <div class="xs" style="color:#555;">Asuransi: Tidak Ada</div>
            @endif
        </div>
    </div>

    {{-- Row 4: Receiver / Sender --}}
    <div class="row grid-2">
        <div class="col-l pad">
            <div class="label-title">Penerima</div>
            <div class="addr-name">{{ $label['destination_name'] ?? '-' }}</div>
            @if($showRcvPhone && ($label['destination_phone'] ?? false))
                <div class="addr-phone">{{ $label['destination_phone'] }}</div>
            @endif
            <div class="addr-text">{{ $label['destination_address'] ?? '-' }}</div>
        </div>
        <div class="col-r pad">
            <div class="label-title">Pengirim</div>
            <div class="addr-name">{{ $label['origin_name'] ?? 'FURE' }}</div>
            @if($showSenderPhone && ($label['origin_phone'] ?? false))
                <div class="addr-phone">{{ $label['origin_phone'] }}</div>
            @endif
            @if($showSenderAddr && ($label['origin_address'] ?? false))
                <div class="addr-text">{{ $label['origin_address'] }}</div>
            @endif
        </div>
    </div>

    {{-- Row 5: Items --}}
    @if($showItems && $itemsText)
        <div class="row pad">
            <div class="label-title">Isi Paket</div>
            <div class="small">{{ $itemsText }}</div>
        </div>
    @endif

    {{-- Row 6: SKU --}}
    @if($showSku)
        <div class="row pad">
            <div class="label-title">SKU</div>
            <div class="small break">{{ $order->items->pluck('product_id')->implode(', ') ?: '-' }}</div>
        </div>
    @endif

    {{-- Row 7: Notes --}}
    @if($order->notes)
        <div class="row pad">
            <div class="label-title">Catatan</div>
            <div class="small">{{ $order->notes }}</div>
        </div>
    @endif

    {{-- Row 8: Footer --}}
    <div class="row pad center xs" style="color:#666;">
        {{ $order->order_number }} · Dicetak {{ now()->format('d/m/Y H:i') }}
    </div>

</main>

<script>
    const waybill   = @json($waybill);
    const reference = @json($reference);
    const autoPrint = @json($autoPrint);

    function renderBarcodes() {
        if (!window.JsBarcode) return;

        if (waybill) {
            try {
                JsBarcode('#waybillBarcode', String(waybill), {
                    format: 'CODE128',
                    width: 1.6,
                    height: 48,
                    displayValue: false,
                    margin: 2,
                });
            } catch(e) { console.warn('waybill barcode error', e); }
        }

        if (reference) {
            try {
                JsBarcode('#referenceBarcode', String(reference), {
                    format: 'CODE128',
                    width: 1.1,
                    height: 32,
                    displayValue: false,
                    margin: 2,
                });
            } catch(e) { console.warn('reference barcode error', e); }
        }
    }

    function doPrint() {
        renderBarcodes(); // ensure rendered before print
        setTimeout(() => window.print(), 100);
    }

    // Render barcodes once DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        renderBarcodes();
        if (autoPrint) {
            setTimeout(() => window.print(), 400);
        }
    });
</script>
</body>
</html>
