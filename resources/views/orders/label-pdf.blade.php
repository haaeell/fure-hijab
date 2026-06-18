<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000; background: #fff; }

.page { width: 100%; padding: 14px; }

/* ── Header ── */
.header { border: 2px solid #000; margin-bottom: 0; display: table; width: 100%; }
.header-cell { display: table-cell; vertical-align: middle; padding: 10px 12px; }
.header-left { width: 40%; border-right: 2px solid #000; }
.header-right { width: 60%; text-align: right; }

.store-name { font-size: 20px; font-weight: 900; letter-spacing: -0.5px; color: #1a1a1a; }
.store-sub  { font-size: 9px; color: #555; margin-top: 2px; }

.courier-name { font-size: 22px; font-weight: 900; letter-spacing: 1px; }
.courier-service { font-size: 10px; color: #444; margin-top: 3px; }

/* ── Resi box ── */
.resi-box { border: 2px solid #000; border-top: none; padding: 12px 14px; text-align: center; background: #f8f8f8; }
.resi-label { font-size: 9px; font-weight: 700; letter-spacing: 2px; color: #555; text-transform: uppercase; }
.resi-number { font-size: 28px; font-weight: 900; letter-spacing: 3px; font-family: 'Courier New', Courier, monospace; color: #000; margin: 4px 0 2px; }
.resi-sub { font-size: 9px; color: #666; }

/* ── Address grid ── */
.address-grid { border: 2px solid #000; border-top: none; display: table; width: 100%; }
.address-cell { display: table-cell; vertical-align: top; padding: 10px 12px; width: 50%; }
.address-left { border-right: 2px solid #000; }
.section-title { font-size: 8px; font-weight: 900; letter-spacing: 1.5px; color: #555; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 6px; }
.address-name { font-size: 13px; font-weight: 800; color: #000; margin-bottom: 3px; }
.address-phone { font-size: 11px; font-weight: 700; color: #333; margin-bottom: 4px; }
.address-text { font-size: 10px; color: #444; line-height: 1.5; }

/* ── Info row ── */
.info-row { border: 2px solid #000; border-top: none; display: table; width: 100%; }
.info-cell { display: table-cell; vertical-align: middle; padding: 8px 12px; text-align: center; }
.info-cell + .info-cell { border-left: 2px solid #000; }
.info-label { font-size: 8px; font-weight: 700; letter-spacing: 1px; color: #777; text-transform: uppercase; }
.info-value { font-size: 13px; font-weight: 800; color: #000; margin-top: 2px; }

/* ── Items ── */
.items-section { border: 2px solid #000; border-top: none; padding: 10px 12px; }
.items-title { font-size: 8px; font-weight: 900; letter-spacing: 1.5px; color: #555; text-transform: uppercase; margin-bottom: 6px; }
.item-row { display: table; width: 100%; padding: 3px 0; border-bottom: 1px dotted #ddd; }
.item-row:last-child { border-bottom: none; }
.item-qty  { display: table-cell; width: 35px; font-weight: 800; font-size: 11px; color: #000; }
.item-name { display: table-cell; font-size: 10px; color: #333; }
.item-var  { display: table-cell; font-size: 9px; color: #888; text-align: right; width: 80px; }

/* ── Notes ── */
.notes-section { border: 2px solid #000; border-top: none; padding: 8px 12px; }
.notes-text { font-size: 10px; color: #444; }

/* ── Footer ── */
.footer { border: 2px solid #000; border-top: none; padding: 8px 12px; display: table; width: 100%; }
.footer-left  { display: table-cell; vertical-align: middle; font-size: 9px; color: #555; }
.footer-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 9px; color: #555; }
.footer-order { font-weight: 800; font-size: 10px; color: #000; }
</style>
</head>
<body>
<div class="page">

    {{-- ── Header: store + courier ── --}}
    <div class="header">
        <div class="header-cell header-left">
            @if($storeLogoUrl)
                <img src="{{ $storeLogoUrl }}" style="height:32px; margin-bottom:4px;" alt="Logo">
            @endif
            <div class="store-name">{{ \App\Models\Setting::getValue('store_name', config('app.name', 'FURE')) }}</div>
            <div class="store-sub">{{ \App\Models\Setting::getValue('store_address', '') }}</div>
        </div>
        <div class="header-cell header-right">
            @if($label['courier_logo_url'])
                <img src="{{ $label['courier_logo_url'] }}" style="height:30px; margin-bottom:4px;" alt="{{ $label['courier_name'] }}">
            @else
                <div class="courier-name">{{ $label['courier_name'] }}</div>
            @endif
            <div class="courier-service">{{ strtoupper($label['service'] ?? '-') }}</div>
            @if($label['estimated_days'])
                <div class="courier-service">Est. {{ $label['estimated_days'] }} hari</div>
            @endif
        </div>
    </div>

    {{-- ── Resi number ── --}}
    <div class="resi-box">
        <div class="resi-label">Nomor Resi / Waybill</div>
        <div class="resi-number">{{ $label['waybill'] ?: '-' }}</div>
        <div class="resi-sub">{{ $label['courier_name'] }} · {{ strtoupper($label['service'] ?? '') }} · {{ number_format($label['cost'], 0, ',', '.') }} IDR</div>
    </div>

    {{-- ── Sender / Receiver ── --}}
    <div class="address-grid">
        <div class="address-cell address-left">
            <div class="section-title">Penerima</div>
            <div class="address-name">{{ $label['destination_name'] }}</div>
            @if($label['destination_phone'])
                <div class="address-phone">{{ $label['destination_phone'] }}</div>
            @endif
            <div class="address-text">{{ $label['destination_address'] }}</div>
        </div>
        <div class="address-cell">
            <div class="section-title">Pengirim</div>
            <div class="address-name">{{ $label['origin_name'] }}</div>
            @if($label['origin_phone'])
                <div class="address-phone">{{ $label['origin_phone'] }}</div>
            @endif
            @if($label['origin_address'])
                <div class="address-text">{{ $label['origin_address'] }}</div>
            @endif
        </div>
    </div>

    {{-- ── Info: qty, weight, cost ── --}}
    <div class="info-row">
        <div class="info-cell">
            <div class="info-label">Jumlah Item</div>
            <div class="info-value">{{ $order->items->sum('qty') }} pcs</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Berat</div>
            <div class="info-value">{{ number_format(($label['weight'] ?? 0) / 1000, 2) }} kg</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Ongkir</div>
            <div class="info-value">Rp {{ number_format($label['cost'], 0, ',', '.') }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">No. Order</div>
            <div class="info-value" style="font-size:10px;">{{ $order->order_number }}</div>
        </div>
    </div>

    {{-- ── Items ── --}}
    <div class="items-section">
        <div class="items-title">Daftar Produk</div>
        @foreach($order->items as $item)
            <div class="item-row">
                <div class="item-qty">{{ $item->qty }}×</div>
                <div class="item-name">{{ $item->product_name }}</div>
                <div class="item-var">{{ $item->variant_name }}</div>
            </div>
        @endforeach
    </div>

    @if($order->notes)
        <div class="notes-section">
            <span style="font-weight:700;">Catatan:</span> {{ $order->notes }}
        </div>
    @endif

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-left">Dicetak {{ now()->format('d/m/Y H:i') }}</div>
        <div class="footer-right">
            <span class="footer-order">{{ $order->order_number }}</span>
        </div>
    </div>

</div>
</body>
</html>
