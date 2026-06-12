<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label {{ $label['waybill'] ?? $order->order_number }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        @page { size: 100mm 150mm; margin: 3mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f4f6; color: #000; font-family: Arial, Helvetica, sans-serif; }
        .toolbar { width: 100mm; margin: 18px auto 10px; display: flex; gap: 8px; justify-content: space-between; }
        .toolbar a, .toolbar button { border: 1px solid #ddd; background: #fff; border-radius: 10px; padding: 9px 14px; font-weight: 700; font-size: 12px; cursor: pointer; color: #333; text-decoration: none; }
        .toolbar button { background: #55249b; color: #fff; border-color: #55249b; }
        .label { width: 100mm; height: 150mm; margin: 0 auto 18px; background: #fff; border: 3px solid #000; overflow: hidden; }
        .row { border-top: 3px solid #000; }
        .grid { display: grid; }
        .col { border-right: 3px solid #000; }
        .col:last-child { border-right: 0; }
        .pad { padding: 8px 10px; }
        .small { font-size: 10px; }
        .xs { font-size: 9px; }
        .muted { color: #333; }
        .bold { font-weight: 800; }
        .black { font-weight: 900; }
        .center { text-align: center; }
        .break { word-break: break-word; }
        .courier-logo { font-size: 28px; font-style: italic; color: #1d3f9f; letter-spacing: -2px; }
        .brand-logo { font-size: 34px; letter-spacing: -2px; }
        .brand-mark { display: inline-block; width: 34px; height: 34px; margin-right: 6px; vertical-align: -5px; border-radius: 8px 8px 8px 20px; background: linear-gradient(135deg, #6d35c5 0 48%, #57c6b7 49% 100%); }
        .barcode-wrap svg { max-width: 100%; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .label { margin: 0; width: auto; height: auto; min-height: 144mm; border: 3px solid #000; }
        }
    </style>
</head>

@php
    $options = $labelOptions ?? [];
    $showInsurance = $options['insurance'] ?? true;
    $showShippingCost = $options['shipping_cost'] ?? true;
    $showItems = $options['item_description'] ?? true;
    $showSku = $options['item_sku'] ?? true;
    $showSenderPhone = $options['sender_phone'] ?? true;
    $showSenderAddress = $options['sender_address'] ?? true;
    $showReceiverPhone = $options['receiver_phone'] ?? true;
    $autoPrint = $options['auto_print'] ?? false;
    $quantity = $order->items->sum('qty');
    $weightKg = max(1, ceil(($label['weight'] ?? 10) / 1000));
    $itemsText = $order->items->map(fn ($item) => $item->qty . 'x ' . $item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : ''))->implode(', ');
@endphp

<body>
    <div class="toolbar">
        <a href="{{ route('orders.show', $order->id) }}">Kembali</a>
        <button onclick="window.print()">Cetak Label</button>
    </div>

    <main class="label">
        <section class="pad grid" style="grid-template-columns: 1fr 2.1fr; align-items:center; min-height:25mm;">
            <div class="center">
                <div class="courier-logo">{{ strtoupper($order->shipment->courier) }}</div>
                <div class="xs bold">EXPRESS ACROSS NATIONS</div>
            </div>
            <div class="center">
                <div class="brand-logo black"><span class="brand-mark"></span>biteship</div>
                <div class="bold" style="font-size:16px;">biteship.com</div>
            </div>
        </section>

        <section class="row pad center barcode-wrap">
            <svg id="waybillBarcode"></svg>
            <div style="font-size:18px; margin-top:2px;">Nomor Resi - <span class="bold">{{ $label['waybill'] ?? '-' }}</span></div>
        </section>

        <section class="row pad center">
            @if($showShippingCost)
                <div style="font-size:16px;">Ongkos Kirim: <span class="bold">Rp. {{ number_format($label['price'] ?? 0, 0, ',', '.') }}</span></div>
            @endif
            <div style="font-size:15px;">Jenis Layanan - <span class="bold">{{ strtoupper($label['service'] ?? '-') }}</span>. Kode Rute - {{ $label['route_code'] ?? 'ROUTEC123' }}</div>
            @if($showInsurance)
                <div class="small bold">Nilai Asuransi: Tidak Ada</div>
            @endif
        </section>

        <section class="row grid" style="grid-template-columns: 1fr 1fr; min-height:25mm;">
            <div class="col pad">
                <div style="font-size:15px;">Reference Number</div>
                <svg id="referenceBarcode"></svg>
                <div class="small bold break">{{ $order->shipment->biteship_order_id ?? $order->order_number }}</div>
            </div>
            <div class="pad" style="font-size:15px; line-height:1.8;">
                <div><span class="bold">Quantity:</span> {{ $quantity }} Pcs</div>
                <div><span class="bold">Weight:</span> {{ $weightKg }} Kg</div>
            </div>
        </section>

        <section class="row grid" style="grid-template-columns: 1fr 1fr; min-height:29mm;">
            <div class="col pad">
                <div class="bold">Alamat Penerima:</div>
                <div class="bold">{{ $label['destination_name'] ?? '-' }}</div>
                @if($showReceiverPhone)
                    <div>{{ $label['destination_phone'] ?? '-' }}</div>
                @endif
                <div class="break">{{ $label['destination_address'] ?? '-' }}</div>
            </div>
            <div class="pad">
                <div class="bold">Alamat Pengirim:</div>
                <div class="bold">{{ $label['origin_name'] ?? 'FURE' }}</div>
                @if($showSenderPhone)
                    <div>{{ $label['origin_phone'] ?? '-' }}</div>
                @endif
                @if($showSenderAddress)
                    <div class="break">{{ $label['origin_address'] ?? '-' }}</div>
                @endif
            </div>
        </section>

        @if($showItems)
            <section class="row pad" style="min-height:16mm;">
                <span class="bold">Jenis Barang :</span>
                <span style="margin-left:14px;">{{ $itemsText ?: '-' }}</span>
            </section>
        @endif

        @if($showSku)
            <section class="row pad" style="min-height:10mm;">
                <span class="bold">SKU Barang :</span>
                <span style="margin-left:14px;">{{ $order->items->pluck('product_id')->implode(', ') ?: '-' }}</span>
            </section>
        @endif

        <section class="row pad" style="min-height:12mm;">
            <span class="bold">Catatan :</span>
            <span style="margin-left:38px;">{{ $order->notes ?: 'Tidak Ada' }}</span>
        </section>

        <section class="row pad center" style="font-size:13px;">
            <div>Pengiriman melalui platform Biteship</div>
            <div>biteship.com</div>
        </section>
    </main>

    <script>
        const waybill = @json($label['waybill']);
        const reference = @json($order->shipment->biteship_order_id ?? $order->order_number);
        const autoPrint = @json($autoPrint);

        if (window.JsBarcode) {
            if (waybill) {
                JsBarcode('#waybillBarcode', waybill, { format: 'CODE128', width: 1.7, height: 45, displayValue: false, margin: 0 });
            }

            if (reference) {
                JsBarcode('#referenceBarcode', reference, { format: 'CODE128', width: 1.05, height: 30, displayValue: false, margin: 0 });
            }
        }

        if (autoPrint) {
            window.addEventListener('load', function () {
                setTimeout(function () {
                    window.print();
                }, 350);
            });
        }
    </script>
</body>

</html>
