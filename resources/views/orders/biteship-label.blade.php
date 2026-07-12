@php
    $pdfMode  = $pdfMode ?? false;
    $opts     = $labelOptions ?? [];

    $showCost        = $opts['shipping_cost']    ?? true;
    $showItems       = $opts['item_description'] ?? true;
    $showSenderPhone = $opts['sender_phone']     ?? true;
    $showSenderAddr  = $opts['sender_address']   ?? true;
    $showRcvPhone    = $opts['receiver_phone']   ?? true;
    $paperSize       = $opts['paper_size']       ?? 'thermal2';
    $isA4Paper       = in_array($paperSize, ['a4', 'a6'], true);

    $waybill  = $label['waybill'] ?? null;
    $qty      = $order->items->sum('qty');
    $weightKg = number_format(max(1, ($label['weight'] ?? 1000)) / 1000, 1);
    $ref      = $order->shipment->biteship_order_id ?? $order->order_number;
    $cost     = $label['cost'] ?? 0;
    $printedAt = now()->format('d/m/Y H:i');

    $storeDisplayName = $globalStoreName ?? $storeName ?? config('app.name', 'Toko');
    $storeLogoPath    = $globalStoreLogo ?? $storeLogo ?? null;
    $storeInitials    = collect(preg_split('/\s+/', trim((string) $storeDisplayName)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
        ->implode('') ?: 'T';

    $normalizeImagePath = function (?string $path): ?string {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (preg_match('/^(https?:)?\/\//i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $path = preg_replace('#^/?storage/#', '', $path);
        $path = preg_replace('#^/?public/#', '', $path);

        return ltrim((string) $path, '/');
    };

    $imageSource = function (?string $path) use ($pdfMode, $normalizeImagePath): ?string {
        $path = $normalizeImagePath($path);

        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'data:')) {
            return $path;
        }

        if (preg_match('/^(https?:)?\/\//i', $path)) {
            return $pdfMode ? null : $path;
        }

        if (!$pdfMode) {
            return asset('storage/' . $path);
        }

        $candidates = [
            storage_path('app/public/' . $path),
            public_path('storage/' . $path),
            public_path($path),
        ];

        foreach ($candidates as $candidate) {
            if (!is_file($candidate) || !is_readable($candidate)) {
                continue;
            }

            $mime = function_exists('mime_content_type') ? mime_content_type($candidate) : null;
            if (!$mime || !str_starts_with($mime, 'image/')) {
                $mime = match (strtolower(pathinfo($candidate, PATHINFO_EXTENSION))) {
                    'svg' => 'image/svg+xml',
                    'webp' => 'image/webp',
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    default => 'image/png',
                };
            }

            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($candidate));
        }

        return null;
    };

    $storeLogoSrc = $imageSource($storeLogoPath);

    // Strip Biteship "1 - 2 days" -> "1 - 2 hari"
    $estRaw     = $label['estimated_days'] ?? null;
    $estDisplay = $estRaw !== null
        ? (trim(preg_replace('/\s*days?\s*/i', '', (string) $estRaw)) ?: '-') . ' hari'
        : '-';

    $courierLogoSrc = $imageSource($courierLabel['logo'] ?? null);
    $courierLogoExists = (bool) $courierLogoSrc;
    $courierName = $courierLabel['name'] ?? $order->shipment->courier ?? '';
    $serviceName = strtoupper((string) ($label['service'] ?? '-'));

    // Barcodes: PNG base64 for DomPDF, JsBarcode SVG for browser
    $waybillBarcodeB64 = null;
    $refBarcodeB64     = null;
    if ($pdfMode) {
        try {
            $bgen = new \Picqer\Barcode\BarcodeGeneratorPNG();
            if ($waybill) {
                $waybillBarcodeB64 = base64_encode(
                    $bgen->getBarcode((string) $waybill, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128, 2, 50)
                );
            }
            if ($ref) {
                $refBarcodeB64 = base64_encode(
                    $bgen->getBarcode((string) $ref, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128, 2, 32)
                );
            }
        } catch (\Throwable $e) {}
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resi {{ $waybill ?? $order->order_number }}</title>
@if(!$pdfMode)
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
@endif
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #111827;
    background: #eef0f2;
}

.toolbar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 18px;
    background: #ffffff;
    border-bottom: 1px solid #e0e3e7;
}
.toolbar a {
    text-decoration: none;
    border-radius: 10px;
    padding: 9px 16px;
    font-size: 12px;
    font-weight: 800;
    background: #f5f6f7;
    color: #222222;
    border: 1px solid #d7dce0;
}
.toolbar a.primary {
    background: #5F4A3A;
    color: #ffffff;
    border-color: #5F4A3A;
}

.page-wrap {
    display: flex;
    justify-content: center;
    padding: 22px 2cm;
}
.label {
    width: 100mm;
    background: #ffffff;
    border: 2px solid #111111;
    overflow: hidden;
}
.sec { border-top: 1.4px solid #111111; }
.sec:first-child { border-top: 0; }
table { border-collapse: collapse; width: 100%; }
td { vertical-align: top; }

.eyebrow {
    display: block;
    margin-bottom: 2px;
    color: #687076;
    font-size: 6.7px;
    font-weight: 900;
    letter-spacing: 1.1px;
    line-height: 1.1;
    text-transform: uppercase;
}
.store-logo {
    display: inline-block;
    width: auto;
    height: auto;
    max-width: 28mm;
    max-height: 13mm;
    object-fit: contain;
    vertical-align: middle;
    border: 0;
    padding: 0;
    background: transparent;
}
.store-mark {
    display: inline-block;
    width: 13mm;
    height: 13mm;
    line-height: 13mm;
    text-align: center;
    vertical-align: middle;
    background: #111827;
    color: #ffffff;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .5px;
}
.store-copy {
    display: inline-block;
    max-width: 48mm;
    margin-left: 7px;
    vertical-align: middle;
}
.store-name {
    display: block;
    font-size: 14px;
    font-weight: 900;
    line-height: 1.05;
    word-break: break-word;
    color: #111827;
}
.store-subtitle {
    display: block;
    margin-top: 2px;
    color: #687076;
    font-size: 7px;
    font-weight: 800;
    letter-spacing: .9px;
    text-transform: uppercase;
}
.doc-number {
    font-family: "Courier New", monospace;
    font-size: 8.5px;
    font-weight: 900;
    line-height: 1.2;
    word-break: break-all;
}
.doc-date {
    margin-top: 2px;
    color: #687076;
    font-size: 7.5px;
    font-weight: 700;
}
.courier-logo { max-height: 12mm; max-width: 39mm; object-fit: contain; display: block; }
.courier-name { font-size: 16px; font-weight: 900; text-transform: uppercase; line-height: 1; word-break: break-word; }
.courier-display-name { margin-top: 3px; font-size: 8px; font-weight: 900; line-height: 1.1; text-transform: uppercase; word-break: break-word; }
.service-text { display: block; margin-top: 3px; color: #333333; font-size: 8px; font-weight: 900; letter-spacing: .5px; text-transform: uppercase; }
.metric-value { font-size: 12px; font-weight: 900; line-height: 1.1; word-break: break-word; }
.metric-small { margin-top: 3px; color: #333333; font-size: 7.5px; font-weight: 800; }
.cost-strike { display: inline-block; font-size: 11px; font-weight: 900; line-height: 1.05; text-decoration: line-through; text-decoration-thickness: 1.4px; word-break: break-word; }
.paid-label { display: block; margin-top: 2px; color: #16803c; font-size: 6.8px; font-weight: 900; letter-spacing: .7px; text-transform: uppercase; }
.waybill-num  {
    margin-top: 3px;
    font-family: "Courier New", monospace;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .7px;
    line-height: 1.12;
    word-break: break-all;
}
.route-label {
    display: inline-block;
    margin-bottom: 4px;
    padding: 2px 5px;
    border: 1px solid #111111;
    font-size: 6.5px;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.person-name  { margin-bottom: 2px; font-size: 11.5px; font-weight: 900; line-height: 1.2; word-break: break-word; }
.sender-name  { font-size: 9.5px; }
.phone        { margin-bottom: 2px; font-size: 9px; font-weight: 800; }
.addr-text    { color: #222222; font-size: 8.5px; line-height: 1.35; word-break: break-word; }
.compact      { font-size: 8.4px; line-height: 1.35; word-break: break-word; }
.footer       { padding: 5px 8px; border-top: 1px dashed #9ca3af; color: #555555; text-align: center; font-size: 7.4px; font-weight: 800; }

@page { margin: 0; }

@media screen {
    .page-wrap { padding: clamp(12px, 4vw, 28px); }
    .label {
        width: min(100%, 100mm);
        max-width: calc(100vw - 24px);
        box-shadow: 0 18px 45px rgba(17, 24, 39, .12);
    }
}

@media screen and (max-width: 480px) {
    body { background: #ffffff; }
    .toolbar {
        position: sticky;
        top: 0;
        z-index: 10;
        justify-content: stretch;
        gap: 8px;
        padding: 10px;
    }
    .toolbar a {
        flex: 1;
        padding: 10px 8px;
        text-align: center;
        font-size: 11px;
    }
    .page-wrap { padding: 10px; }
    .label {
        width: 100%;
        max-width: none;
        border-width: 1.5px;
        box-shadow: none;
    }
    .brand-head td {
        display: block;
        width: 100% !important;
        text-align: left !important;
    }
    .doc-cell {
        border-top: 1px solid #e5e7eb;
        margin-top: 7px;
        padding-top: 7px !important;
    }
    .store-copy { max-width: calc(100% - 17mm); }
    .stack-table,
    .stack-table tbody,
    .stack-table tr,
    .stack-table td {
        display: block;
        width: 100% !important;
    }
    .stack-table td {
        border-right: 0 !important;
        border-bottom: 1.2px solid #111111;
    }
    .stack-table tr td:last-child { border-bottom: 0; }
}

.paper-thermal1,
.paper-thermal2 {
    font-size: 9px;
}
.paper-thermal1 .header-sec,
.paper-thermal2 .header-sec { padding: 4px 6px !important; }
.paper-thermal1 .store-logo,
.paper-thermal2 .store-logo { max-width: 22mm; max-height: 10mm; }
.paper-thermal1 .store-mark,
.paper-thermal2 .store-mark { width: 10mm; height: 10mm; line-height: 10mm; font-size: 9px; }
.paper-thermal1 .store-copy,
.paper-thermal2 .store-copy { max-width: 43mm; margin-left: 5px; }
.paper-thermal1 .store-name,
.paper-thermal2 .store-name { font-size: 12px; line-height: 1; }
.paper-thermal1 .store-subtitle,
.paper-thermal2 .store-subtitle { margin-top: 1px; font-size: 5.8px; letter-spacing: .75px; }
.paper-thermal1 .doc-number,
.paper-thermal2 .doc-number { font-size: 7.5px; }
.paper-thermal1 .doc-date,
.paper-thermal2 .doc-date { font-size: 6.5px; }
.paper-thermal1 .courier-sec td,
.paper-thermal2 .courier-sec td,
.paper-thermal1 .reference-sec td,
.paper-thermal2 .reference-sec td,
.paper-thermal1 .route-sec td,
.paper-thermal2 .route-sec td { padding: 4px 6px !important; }
.paper-thermal1 .courier-logo,
.paper-thermal2 .courier-logo { max-height: 7.5mm; max-width: 25mm; }
.paper-thermal1 .courier-name,
.paper-thermal2 .courier-name { font-size: 12px; }
.paper-thermal1 .courier-display-name,
.paper-thermal2 .courier-display-name { margin-top: 2px; font-size: 6.8px; }
.paper-thermal1 .service-text,
.paper-thermal2 .service-text { margin-top: 1px; font-size: 7px; letter-spacing: .35px; }
.paper-thermal1 .metric-value,
.paper-thermal2 .metric-value { font-size: 10px; line-height: 1.05; }
.paper-thermal1 .cost-strike,
.paper-thermal2 .cost-strike { font-size: 9px; }
.paper-thermal1 .paid-label,
.paper-thermal2 .paid-label { font-size: 5.8px; letter-spacing: .45px; }
.paper-thermal1 .waybill-sec,
.paper-thermal2 .waybill-sec { padding: 4px 6px !important; }
.paper-thermal1 .waybill-barcode,
.paper-thermal2 .waybill-barcode { height: 10mm !important; }
.paper-thermal1 .reference-barcode,
.paper-thermal2 .reference-barcode { height: 7mm !important; }
.paper-thermal1 .waybill-num,
.paper-thermal2 .waybill-num { margin-top: 1px; font-size: 9.5px; letter-spacing: .45px; }
.paper-thermal1 .eyebrow,
.paper-thermal2 .eyebrow { margin-bottom: 1px; font-size: 5.8px; letter-spacing: .9px; }
.paper-thermal1 .route-label,
.paper-thermal2 .route-label { margin-bottom: 2px; padding: 1px 4px; font-size: 5.8px; letter-spacing: .8px; }
.paper-thermal1 .person-name,
.paper-thermal2 .person-name { margin-bottom: 1px; font-size: 9.2px; line-height: 1.1; }
.paper-thermal1 .sender-name,
.paper-thermal2 .sender-name { font-size: 8.4px; }
.paper-thermal1 .phone,
.paper-thermal2 .phone { margin-bottom: 1px; font-size: 8px; }
.paper-thermal1 .addr-text,
.paper-thermal2 .addr-text { font-size: 7.4px; line-height: 1.22; }
.paper-thermal1 .items-sec,
.paper-thermal2 .items-sec { padding: 4px 6px !important; }
.paper-thermal1 .compact,
.paper-thermal2 .compact { font-size: 7.2px; line-height: 1.2; }
.paper-thermal1 .footer,
.paper-thermal2 .footer { padding: 4px 6px; font-size: 6.4px; }

.paper-thermal1 .header-sec { padding: 3px 5px !important; }
.paper-thermal1 .store-logo { max-width: 18mm; max-height: 8mm; }
.paper-thermal1 .store-name { font-size: 10px; }
.paper-thermal1 .courier-sec td,
.paper-thermal1 .reference-sec td,
.paper-thermal1 .route-sec td { padding: 3px 5px !important; }
.paper-thermal1 .waybill-sec,
.paper-thermal1 .items-sec { padding: 3px 5px !important; }
.paper-thermal1 .waybill-barcode { height: 8mm !important; }
.paper-thermal1 .reference-barcode { height: 5mm !important; }
.paper-thermal1 .addr-text { font-size: 6.6px; line-height: 1.16; }

@media print {
    html, body { background: #ffffff; margin: 0; padding: 0; }
    .toolbar { display: none !important; }
    .page-wrap { padding: 0; }
    .label { width: 100%; border: 1.2px solid #111111; box-shadow: none; }
}
</style>
</head>
<body>

@if(!$pdfMode)
<div class="toolbar">
    <a href="{{ route('orders.show', $order->id) }}">Kembali</a>
    <a href="{{ route('orders.label.pdf', $order->id) }}" class="primary">Download PDF</a>
</div>
@endif

@if($pdfMode)
<div style="{{ $isA4Paper ? 'width:100mm; margin:12mm auto;' : 'width:100%; margin:0;' }}">
<div class="label paper-{{ $paperSize }}">
@else
<div class="page-wrap">
<div class="label paper-{{ $paperSize }}">
@endif

{{-- Header toko --}}
<div class="sec header-sec" style="padding:7px 8px;">
    <table class="brand-head">
        <tr>
            <td style="width:68%; vertical-align:middle;">
                @if($storeLogoSrc)
                    <img src="{{ $storeLogoSrc }}" class="store-logo" alt="{{ $storeDisplayName }}">
                @else
                    <span class="store-mark">{{ $storeInitials }}</span>
                @endif
                <span class="store-copy">
                    <span class="store-name">{{ $storeDisplayName }}</span>
                    <span class="store-subtitle">Label Pengiriman</span>
                </span>
            </td>
            <td class="doc-cell" style="width:32%; text-align:right; vertical-align:middle;">
                <span class="eyebrow">Nomor Order</span>
                <div class="doc-number">{{ $order->order_number }}</div>
                <div class="doc-date">{{ $printedAt }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- Kurir | Estimasi | Ongkir --}}
<div class="sec courier-sec">
    <table class="stack-table">
        <tr>
            <td style="padding:7px 8px; border-right:1.2px solid #111111; width:54%; vertical-align:middle;">
                <span class="eyebrow">Kurir</span>
                @if($courierLogoExists)
                    <img src="{{ $courierLogoSrc }}" class="courier-logo" alt="{{ $courierName }}">
                    <div class="courier-display-name">{{ $courierName ?: strtoupper($order->shipment->courier ?? '-') }}</div>
                @else
                    <div class="courier-name">{{ strtoupper($courierName ?: '-') }}</div>
                @endif
                <span class="service-text">{{ $serviceName }}</span>
            </td>
            <td style="padding:7px 6px; border-right:1.2px solid #111111; text-align:center; vertical-align:middle; width:23%;">
                <span class="eyebrow">Estimasi</span>
                <div class="metric-value">{{ $estDisplay }}</div>
            </td>
            <td style="padding:7px 6px; text-align:center; vertical-align:middle; width:23%;">
                <span class="eyebrow">Ongkir</span>
                @if($showCost && $cost > 0)
                    <div>
                        <span class="cost-strike">Rp{{ number_format($cost, 0, ',', '.') }}</span>
                        <span class="paid-label">Sudah dibayar</span>
                    </div>
                @else
                    <div class="paid-label" style="margin-top:0;">Sudah dibayar</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- Barcode resi --}}
<div class="sec waybill-sec" style="padding:6px 8px 5px; text-align:center;">
    <span class="eyebrow" style="text-align:left;">Nomor Resi</span>
    @if($waybill)
        @if($pdfMode && $waybillBarcodeB64)
            <img src="data:image/png;base64,{{ $waybillBarcodeB64 }}"
                 class="waybill-barcode" style="display:block; width:92%; height:14mm; margin:0 auto;">
        @elseif(!$pdfMode)
            <svg id="waybillBarcode" class="waybill-barcode" style="display:block; width:100%; max-width:88mm; height:15mm; margin:0 auto;"></svg>
        @endif
        <div class="waybill-num">{{ $waybill }}</div>
    @else
        <div style="padding:10px 0; color:#777777; font-size:10px; font-weight:800;">Nomor resi belum tersedia</div>
    @endif
</div>

{{-- Referensi | Paket --}}
<div class="sec reference-sec">
    <table class="stack-table">
        <tr>
            <td style="padding:6px 8px; border-right:1.2px solid #111111; width:60%; vertical-align:top;">
                <span class="eyebrow">Referensi</span>
                @if($pdfMode && $refBarcodeB64)
                    <img src="data:image/png;base64,{{ $refBarcodeB64 }}"
                         class="reference-barcode" style="display:block; width:100%; height:9mm; margin-bottom:2px;">
                @elseif(!$pdfMode)
                    <svg id="referenceBarcode" class="reference-barcode" style="display:block; width:100%; max-width:48mm; height:9mm;"></svg>
                @endif
                <div style="margin-top:1px; color:#333333; font-size:7px; font-weight:800; word-break:break-all;">{{ $ref }}</div>
            </td>
            <td style="padding:6px 8px; width:40%; vertical-align:top;">
                <span class="eyebrow">Detail Paket</span>
                <div style="font-size:9px; line-height:1.55;">
                    <strong>Qty</strong> {{ $qty }} pcs<br>
                    <strong>Berat</strong> {{ $weightKg }} kg
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- Penerima | Pengirim --}}
<div class="sec route-sec">
    <table class="stack-table">
        <tr>
            <td style="padding:7px 8px; border-right:1.2px solid #111111; width:58%; vertical-align:top;">
                <span class="route-label">Penerima</span>
                <div class="person-name">{{ $label['destination_name'] ?? '-' }}</div>
                @if($showRcvPhone && !empty($label['destination_phone']))
                    <div class="phone">{{ $label['destination_phone'] }}</div>
                @endif
                <div class="addr-text">{{ $label['destination_address'] ?? '-' }}</div>
            </td>
            <td style="padding:7px 8px; width:42%; vertical-align:top;">
                <span class="route-label">Pengirim</span>
                <div class="person-name sender-name">{{ $label['origin_name'] ?? $storeDisplayName }}</div>
                @if($showSenderPhone && !empty($label['origin_phone']))
                    <div class="phone">{{ $label['origin_phone'] }}</div>
                @endif
                @if($showSenderAddr && !empty($label['origin_address']))
                    <div class="addr-text">{{ $label['origin_address'] }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- Isi Barang --}}
@if($showItems && $order->items->count())
<div class="sec items-sec" style="padding:6px 8px;">
    <span class="eyebrow">Isi Barang</span>
    <div class="compact">
        @foreach($order->items as $item)
            {{ $item->qty }}x {{ $item->product_name }}{{ $item->variant_name ? ' - ' . $item->variant_name : '' }}<br>
        @endforeach
    </div>
</div>
@endif

{{-- Catatan --}}
@if($order->notes)
<div class="sec" style="padding:5px 8px;">
    <span class="eyebrow">Catatan</span>
    <div class="compact">{{ $order->notes }}</div>
</div>
@endif

<div class="footer">{{ $storeDisplayName }} | {{ $order->order_number }} | Dicetak {{ $printedAt }}</div>

@if($pdfMode)
</div>
</div>
@else
</div>
</div>
@endif

@if(!$pdfMode)
<script>
const waybill   = @json($waybill);
const reference = @json($ref);

document.addEventListener('DOMContentLoaded', function () {
    if (waybill) {
        try {
            JsBarcode('#waybillBarcode', String(waybill), {
                format: 'CODE128', width: 1.6, height: 48, displayValue: false, margin: 2,
            });
        } catch (e) {}
    }
    if (reference) {
        try {
            JsBarcode('#referenceBarcode', String(reference), {
                format: 'CODE128', width: 1.1, height: 30, displayValue: false, margin: 2,
            });
        } catch (e) {}
    }
});
</script>
@endif
</body>
</html>
