@php
    $pdfMode  = $pdfMode ?? false;
    $opts     = $labelOptions ?? [];

    $showCost        = $opts['shipping_cost']    ?? true;
    $showItems       = $opts['item_description'] ?? true;
    $showSenderPhone = $opts['sender_phone']     ?? true;
    $showSenderAddr  = $opts['sender_address']   ?? true;
    $showRcvPhone    = $opts['receiver_phone']   ?? true;

    $waybill  = $label['waybill'] ?? null;
    $qty      = $order->items->sum('qty');
    $weightKg = number_format(max(1, ($label['weight'] ?? 1000)) / 1000, 1);
    $ref      = $order->shipment->biteship_order_id ?? $order->order_number;
    $cost     = $label['cost'] ?? 0;

    // Strip Biteship "1 - 2 days" → "1 - 2 hari"
    $estRaw     = $label['estimated_days'] ?? null;
    $estDisplay = $estRaw !== null
        ? (trim(preg_replace('/\s*days?\s*/i', '', (string) $estRaw)) ?: '-') . ' hari'
        : '-';

    // Courier logo: PDF needs local path, browser needs URL
    if ($pdfMode) {
        $courierLogoSrc    = isset($courierLabel['logo']) && $courierLabel['logo']
            ? public_path('storage/' . $courierLabel['logo']) : null;
        $courierLogoExists = $courierLogoSrc && file_exists($courierLogoSrc);
    } else {
        $courierLogoSrc    = isset($courierLabel['logo']) ? asset('storage/' . $courierLabel['logo']) : null;
        $courierLogoExists = (bool) $courierLogoSrc;
    }
    $courierName = $courierLabel['name'] ?? $order->shipment->courier ?? '';

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
                    $bgen->getBarcode((string) $ref, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128, 1.5, 32)
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
body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #111; background: #eef0f2; }

.toolbar {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    padding: 12px 18px; background: #fff; border-bottom: 1px solid #e0e3e7;
}
.toolbar a {
    text-decoration: none; border-radius: 8px; padding: 8px 16px;
    font-size: 12px; font-weight: 700; background: #f5f6f7;
    color: #222; border: 1px solid #d7dce0;
}
.page-wrap { display: flex; justify-content: center; padding: 22px 2cm; }

.label {
    width: 100mm;
    background: #fff;
    border: 2px solid #000;
}
.sec { border-top: 1.5px solid #111; }
.sec:first-child { border-top: 0; }
table { border-collapse: collapse; width: 100%; }
td { vertical-align: top; }

.eyebrow {
    font-size: 6.5px; font-weight: 900; letter-spacing: 1.2px;
    text-transform: uppercase; color: #666; margin-bottom: 2px; display: block;
}
.courier-logo { max-height: 12mm; max-width: 42mm; object-fit: contain; display: block; }
.courier-name { font-size: 16px; font-weight: 900; text-transform: uppercase; line-height: 1; }
.service-text { font-size: 8px; font-weight: 900; letter-spacing: .5px; text-transform: uppercase; color: #333; margin-top: 2px; display: block; }
.waybill-num  { font-family: "Courier New", monospace; font-size: 11px; font-weight: 900; letter-spacing: 1px; line-height: 1.1; word-break: break-all; margin-top: 3px; }
.person-name  { font-size: 11px; font-weight: 900; line-height: 1.2; word-break: break-word; margin-bottom: 2px; }
.sender-name  { font-size: 9.5px; }
.phone        { font-size: 9px; font-weight: 800; margin-bottom: 2px; }
.addr-text    { font-size: 8.5px; line-height: 1.35; color: #222; word-break: break-word; }
.compact      { font-size: 8.5px; line-height: 1.35; word-break: break-word; }
.footer       { text-align: center; font-size: 7.5px; font-weight: 700; color: #555; border-top: 1px dashed #aaa; padding: 5px 8px; }

@page { margin: 0; }
@media print {
    html, body { background: #fff; margin: 0; padding: 0; }
    .toolbar { display: none !important; }
    .page-wrap { padding: 0; }
    .label { width: 100%; border: 1.2px solid #111; }
}
</style>
</head>
<body>

@if(!$pdfMode)
<div class="toolbar">
    <a href="{{ route('orders.show', $order->id) }}">← Kembali</a>
    <a href="{{ route('orders.label.pdf', $order->id) }}" style="background:#A78B6F; color:#fff; border:none;">⬇ Download PDF</a>
</div>
@endif

@if($pdfMode)
<div style="margin: 0 20mm;">
<div style="background:#fff; border:2px solid #000;">
@else
<div class="page-wrap">
<div class="label">
@endif

{{-- Row 1: Kurir | Estimasi --}}
<div class="sec">
<table><tr>
    <td style="padding:7px 8px; border-right:1.2px solid #111; width:62%; vertical-align:middle;">
        @if($courierLogoExists)
            <img src="{{ $courierLogoSrc }}" class="courier-logo" alt="{{ $courierName }}">
        @else
            <div class="courier-name">{{ strtoupper($courierName ?: '-') }}</div>
        @endif
        @if(!empty($label['service']))
            <span class="service-text">{{ strtoupper($label['service']) }}</span>
        @endif
    </td>
    <td style="padding:7px 6px; text-align:center; vertical-align:middle; width:38%;">
        <span class="eyebrow">Estimasi</span>
        <div style="font-size:13px; font-weight:900; line-height:1.1;">{{ $estDisplay }}</div>
        @if($showCost && $cost > 0)
            <div style="margin-top:3px; font-size:8px; font-weight:700; color:#333;">
                Rp {{ number_format($cost, 0, ',', '.') }}
            </div>
        @endif
    </td>
</tr></table>
</div>

{{-- Row 2: Waybill barcode --}}
<div class="sec" style="padding:6px 8px 5px; text-align:center;">
    @if($waybill)
        @if($pdfMode && $waybillBarcodeB64)
            <img src="data:image/png;base64,{{ $waybillBarcodeB64 }}"
                 style="display:block; width:90%; height:14mm; margin:0 auto;">
        @elseif(!$pdfMode)
            <svg id="waybillBarcode" style="display:block; width:100%; max-width:88mm; height:15mm; margin:0 auto;"></svg>
        @endif
        <div class="waybill-num">{{ $waybill }}</div>
    @else
        <div style="padding:10px 0; font-size:10px; color:#777; font-weight:700;">Nomor resi belum tersedia</div>
    @endif
</div>

{{-- Row 3: Referensi | Qty + Berat --}}
<div class="sec">
<table><tr>
    <td style="padding:6px 8px; border-right:1.2px solid #111; width:60%; vertical-align:top;">
        <span class="eyebrow">Referensi</span>
        @if($pdfMode && $refBarcodeB64)
            <img src="data:image/png;base64,{{ $refBarcodeB64 }}"
                 style="display:block; width:100%; height:9mm; margin-bottom:2px;">
        @elseif(!$pdfMode)
            <svg id="referenceBarcode" style="display:block; width:100%; max-width:48mm; height:9mm;"></svg>
        @endif
        <div style="font-size:7px; font-weight:700; word-break:break-all; color:#333; margin-top:1px;">{{ $ref }}</div>
    </td>
    <td style="padding:6px 8px; width:40%; vertical-align:top;">
        <span class="eyebrow">Paket</span>
        <div style="font-size:9px; line-height:1.6;">
            <strong>Qty</strong> {{ $qty }} pcs<br>
            <strong>Berat</strong> {{ $weightKg }} kg
        </div>
    </td>
</tr></table>
</div>

{{-- Row 4: Penerima | Pengirim --}}
<div class="sec">
<table><tr>
    <td style="padding:7px 8px; border-right:1.2px solid #111; width:57%; vertical-align:top;">
        <span class="eyebrow">Penerima</span>
        <div class="person-name">{{ $label['destination_name'] ?? '-' }}</div>
        @if($showRcvPhone && !empty($label['destination_phone']))
            <div class="phone">{{ $label['destination_phone'] }}</div>
        @endif
        <div class="addr-text">{{ $label['destination_address'] ?? '-' }}</div>
    </td>
    <td style="padding:7px 8px; width:43%; vertical-align:top;">
        <span class="eyebrow">Pengirim</span>
        <div class="person-name sender-name">{{ $label['origin_name'] ?? $globalStoreName }}</div>
        @if($showSenderPhone && !empty($label['origin_phone']))
            <div class="phone">{{ $label['origin_phone'] }}</div>
        @endif
        @if($showSenderAddr && !empty($label['origin_address']))
            <div class="addr-text">{{ $label['origin_address'] }}</div>
        @endif
    </td>
</tr></table>
</div>

{{-- Row 5: Isi Barang --}}
@if($showItems && $order->items->count())
<div class="sec" style="padding:6px 8px;">
    <span class="eyebrow">Isi Barang</span>
    <div class="compact">
        @foreach($order->items as $item)
            {{ $item->qty }}x {{ $item->product_name }}{{ $item->variant_name ? ' – ' . $item->variant_name : '' }}<br>
        @endforeach
    </div>
</div>
@endif

{{-- Row 6: Catatan --}}
@if($order->notes)
<div class="sec" style="padding:5px 8px;">
    <span class="eyebrow">Catatan</span>
    <div class="compact">{{ $order->notes }}</div>
</div>
@endif

<div class="footer">{{ $order->order_number }} · {{ now()->format('d/m/Y H:i') }}</div>

@if($pdfMode)
</div>{{-- label box --}}
</div>{{-- margin wrapper --}}
@else
</div>{{-- .label --}}
</div>{{-- .page-wrap --}}
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
