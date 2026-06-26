<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Setting;
use App\Services\BiteshipService;
use App\Traits\ExpiresUnpaidOrders;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ShipmentTrackingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    use ExpiresUnpaidOrders;

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->expireUnpaidOrders();
        [$startDate, $endDate, $status] = $this->resolveFilters($request);

        $ordersQuery = Order::query()
            ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate->toDateString()))
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate->toDateString()));

        $summary = [
            'total' => (clone $ordersQuery)->count(),
            'pending' => (clone $ordersQuery)->where('status', 'pending')->count(),
            'processing' => (clone $ordersQuery)->whereIn('status', ['processing', 'shipped'])->count(),
            'done' => (clone $ordersQuery)->where('status', 'delivered')->count(),
            'revenue' => (clone $ordersQuery)->where('status', 'delivered')->sum('total'),
        ];

        $statusCounts = (clone $ordersQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $filters = [
            'status' => $status,
            'start_date' => $startDate?->toDateString(),
            'end_date' => $endDate?->toDateString(),
        ];

        return view('orders.index', compact('summary', 'statusCounts', 'filters'));
    }

    public function data(Request $request)
    {
        [$startDate, $endDate, $status] = $this->resolveFilters($request);

        $query = Order::query()
            ->with(['user', 'items', 'payment'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate->toDateString()))
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate->toDateString()));

        return DataTables::eloquent($query)
            ->filter(function ($query) use ($request) {
                $search = $request->input('search.value');

                if (blank($search)) {
                    return;
                }

                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('total', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('items', function ($query) use ($search) {
                            $query->where('product_name', 'like', "%{$search}%")
                                ->orWhere('variant_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('payment', function ($query) use ($search) {
                            $query->where('status', 'like', "%{$search}%")
                                ->orWhere('payment_method', 'like', "%{$search}%");
                        });
                });
            })
            ->addColumn('order_identity', fn (Order $order) => $this->renderOrderIdentity($order))
            ->addColumn('customer', fn (Order $order) => $this->renderCustomer($order))
            ->addColumn('items_summary', fn (Order $order) => $this->renderItems($order))
            ->addColumn('total_summary', fn (Order $order) => $this->renderTotal($order))
            ->addColumn('payment_summary', fn (Order $order) => $this->renderPayment($order))
            ->addColumn('status_badge', fn (Order $order) => $this->renderStatus($order))
            ->addColumn('date_summary', fn (Order $order) => $this->renderDate($order))
            ->addColumn('action', fn (Order $order) => $this->renderActions($order))
            ->rawColumns([
                'order_identity',
                'customer',
                'items_summary',
                'total_summary',
                'payment_summary',
                'status_badge',
                'date_summary',
                'action',
            ])
            ->toJson();
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product.images',
            'items.variant',
            'payment',
            'shipment',
            'address',
            'coupon',
            'reviews.product',
        ])->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    // ─── Update Status ────────────────────────────────────────────────────────

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'note'   => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $order->update(['status' => $request->status]);

            DB::commit();

            return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    // ─── Update Resi only ─────────────────────────────────────────────────────

    public function updateResi(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate(['resi' => 'required|string|max:100']);

        $shipment = $order->shipment ?? new Shipment(['order_id' => $order->id]);
        $shipment->resi   = $request->resi;
        $shipment->status = 'in_transit';
        $shipment->save();

        return redirect()->back()->with('success', 'Nomor resi berhasil disimpan.');
    }

    public function generateBiteshipWaybill($id, BiteshipService $biteship)
    {
        $order = Order::with(['user', 'items.product', 'shipment', 'address'])->findOrFail($id);

        if (!$order->shipment) {
            return redirect()->back()->with('error', 'Data shipment belum tersedia.');
        }

        if ($order->shipment->resi) {
            return redirect()->back()->with('error', 'Order ini sudah memiliki resi.');
        }

        try {
            $result = $biteship->createOrder($order);
            $courier = $result['courier'] ?? [];
            $waybill = $courier['waybill_id']
                ?? $courier['waybill_number']
                ?? $result['courier_waybill_id']
                ?? $result['waybill_id']
                ?? $result['waybill_number']
                ?? null;

            $order->shipment->update([
                'biteship_order_id' => $result['id'] ?? $result['order_id'] ?? null,
                'resi' => $waybill,
                'label_url' => $this->biteshipLabelUrl($result),
                'status' => $waybill ? 'in_transit' : 'pending',
                'biteship_payload' => $result,
            ]);

            if ($waybill) {
                $order->update(['status' => 'shipped']);
            }

            return redirect()->back()->with('success', $waybill ? 'Resi Biteship berhasil dibuat: ' . $waybill : 'Order Biteship berhasil dibuat, tetapi resi belum tersedia.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal generate resi Biteship: ' . $e->getMessage());
        }
    }

    public function trackShipment(Request $request, $id, ShipmentTrackingService $trackingService)
    {
        $order = Order::with('shipment')->findOrFail($id);

        if (!$order->shipment || blank($order->shipment->resi)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Nomor resi belum tersedia.'], 422);
            }
            return redirect()->back()->with('error', 'Nomor resi belum tersedia.');
        }

        try {
            $tracking = $trackingService->trackShipment($order->shipment);
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tracking gagal: ' . $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', 'Tracking Biteship gagal: ' . $e->getMessage());
        }

        $biteshipStatus = $trackingService->statusCode($tracking);

        $order->shipment->update([
            'tracking_history' => $tracking,
            'tracked_at'       => now(),
            'status'           => $biteshipStatus ?? $order->shipment->status,
        ]);

        if ($request->wantsJson()) {
            $fresh   = $order->shipment->fresh();
            $manifest = $tracking['manifest'] ?? $tracking['history'] ?? [];
            return response()->json([
                'success'           => true,
                'message'           => 'Tracking berhasil diperbarui.',
                'manifest'          => array_values($manifest),
                'status'            => $fresh->status,
                'status_label'      => $fresh->status_label,
                'status_badge_class'=> $fresh->status_badge_class,
                'tracked_at'        => now()->format('d M Y H:i'),
            ]);
        }

        return redirect()->back()->with('success', 'Tracking resi berhasil diperbarui.');
    }

    public function printBiteshipLabel(Request $request, $id)
    {
        $order = Order::with(['user', 'items', 'shipment', 'address'])->findOrFail($id);

        if (!$order->shipment) {
            return redirect()->back()->with('error', 'Data shipment belum tersedia.');
        }

        $payload = is_array($order->shipment->biteship_payload)
            ? $order->shipment->biteship_payload
            : [];
        $courier = $payload['courier'] ?? [];

        $label = [
            'waybill' => Arr::get($payload, 'courier_waybill_id')
                ?: Arr::get($payload, 'waybill_id')
                ?: Arr::get($payload, 'waybill_number')
                ?: Arr::get($courier, 'waybill_id')
                ?: Arr::get($courier, 'waybill_number')
                ?: $order->shipment->resi,
            'tracking_id' => Arr::get($payload, 'courier_tracking_id')
                ?: Arr::get($payload, 'tracking_id')
                ?: Arr::get($payload, 'id'),
            'tracking_link' => Arr::get($payload, 'courier_link')
                ?: Arr::get($payload, 'link'),
            'driver_name' => Arr::get($payload, 'courier_driver_name')
                ?: Arr::get($courier, 'driver_name'),
            'driver_phone' => Arr::get($payload, 'courier_driver_phone')
                ?: Arr::get($courier, 'driver_phone'),
            'driver_plate' => Arr::get($payload, 'courier_driver_plate_number')
                ?: Arr::get($courier, 'driver_plate_number'),
            'origin_name' => Arr::get($payload, 'origin.contact_name')
                ?: config('services.biteship.origin_contact_name', config('app.name')),
            'origin_phone' => Arr::get($payload, 'origin.contact_phone')
                ?: config('services.biteship.origin_contact_phone'),
            'origin_address' => Arr::get($payload, 'origin.address')
                ?: config('services.biteship.origin_address'),
            'destination_name' => Arr::get($payload, 'destination.contact_name')
                ?: $order->address?->receiver_name
                ?: $order->user?->name,
            'destination_phone' => Arr::get($payload, 'destination.contact_phone')
                ?: $order->address?->phone
                ?: $order->user?->phone,
            'destination_address' => Arr::get($payload, 'destination.address')
                ?: trim(($order->address?->address ?? '') . ', ' . ($order->address?->subdistrict ?? '') . ', ' . ($order->address?->district ?? '') . ', ' . ($order->address?->city ?? '') . ', ' . ($order->address?->province ?? '') . ' ' . ($order->address?->postal_code ?? '')),
            'weight' => Arr::get($payload, 'weight')
                ?: Arr::get($payload, 'total_weight')
                ?: $order->shipment->total_weight,
            'cost' => Arr::get($payload, 'price')
                ?: Arr::get($payload, 'order_price')
                ?: $order->shipment->cost
                ?: 0,
            'service' => Arr::get($payload, 'courier_type')
                ?: Arr::get($courier, 'type')
                ?: $order->shipment->service,
            'estimated_days' => Arr::get($payload, 'courier_estimated_time_send_to_start')
                ?: Arr::get($payload, 'estimated_time_send_to_start')
                ?: Arr::get($payload, 'courier_estimated_time')
                ?: null,
        ];

        if (!$label['tracking_link'] && $label['tracking_id']) {
            $label['tracking_link'] = 'https://track.biteship.com/' . $label['tracking_id'];
        }

        $fromModal = $request->has('auto_print');
        $labelOptions = [
            'insurance' => $fromModal ? $request->boolean('insurance') : true,
            'shipping_cost' => $fromModal ? $request->boolean('shipping_cost') : true,
            'item_description' => $fromModal ? $request->boolean('item_description') : true,
            'item_sku' => $fromModal ? $request->boolean('item_sku') : true,
            'sender_phone' => $fromModal ? $request->boolean('sender_phone') : true,
            'sender_address' => $fromModal ? $request->boolean('sender_address') : true,
            'receiver_phone' => $fromModal ? $request->boolean('receiver_phone') : true,
            'mask_receiver_name' => $fromModal ? $request->boolean('mask_receiver_name') : true,
            'auto_print' => $request->boolean('auto_print'),
            'paper_size' => $request->input('paper_size', 'thermal2'),
        ];

        if ($labelOptions['mask_receiver_name']) {
            $label['destination_name'] = $this->maskName((string) $label['destination_name']);
        }

        $courierModel = Courier::where('code', $order->shipment->courier ?? '')->first();
        $courierLabel = [
            'name' => $courierModel?->name,
            'logo' => $courierModel?->logo,
        ];

        return view('orders.biteship-label', compact('order', 'label', 'labelOptions', 'courierLabel'));
    }

    public function downloadBiteshipLabel($id)
    {
        $order = Order::with('shipment')->findOrFail($id);

        if (!$order->shipment) {
            return redirect()->back()->with('error', 'Data shipment belum tersedia.');
        }

        $payload = is_array($order->shipment->biteship_payload)
            ? $order->shipment->biteship_payload
            : [];
        $labelUrl = $order->shipment->label_url ?: $this->biteshipLabelUrl($payload);

        if (!$labelUrl) {
            return redirect()->back()->with('error', 'Label resmi Biteship belum tersedia. Gunakan Cetak Resi internal dulu.');
        }

        try {
            $response = Http::timeout(30)->get($labelUrl);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengambil label Biteship: ' . $e->getMessage());
        }

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Label Biteship belum bisa didownload. Coba buka Label Biteship langsung.');
        }

        $contentType = $response->header('Content-Type') ?: 'application/pdf';
        $extension = str_contains($contentType, 'image/png') ? 'png' : (str_contains($contentType, 'image/jpeg') ? 'jpg' : 'pdf');
        $filename = 'label-biteship-' . $order->order_number . '.' . $extension;

        return response($response->body(), 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadLabelPdf(Request $request, $id)
    {
        $order = Order::with(['user', 'items.product', 'shipment', 'address'])->findOrFail($id);

        if (!$order->shipment) {
            return redirect()->back()->with('error', 'Data shipment belum tersedia.');
        }

        $shipment   = $order->shipment;
        $payload    = is_array($shipment->biteship_payload) ? $shipment->biteship_payload : [];
        $courierObj = Courier::where('code', $shipment->courier)->first();

        // Label options from modal form
        $opts = [
            'shipping_cost'      => $request->boolean('shipping_cost', true),
            'item_description'   => $request->boolean('item_description', true),
            'sender_phone'       => $request->boolean('sender_phone', true),
            'sender_address'     => $request->boolean('sender_address', true),
            'receiver_phone'     => $request->boolean('receiver_phone', true),
            'mask_receiver_name' => $request->boolean('mask_receiver_name', false),
        ];

        $destinationName = Arr::get($payload, 'destination.contact_name')
            ?: $order->address?->receiver_name
            ?: $order->user?->name;

        if ($opts['mask_receiver_name']) {
            $destinationName = $this->maskName((string) $destinationName);
        }

        $label = [
            'waybill'             => Arr::get($payload, 'courier_waybill_id')
                ?: Arr::get($payload, 'waybill_id')
                ?: $shipment->resi,
            'courier_name'        => $courierObj?->name ?? strtoupper((string) $shipment->courier),
            'courier_logo_path'   => $courierObj?->logo ? public_path('storage/' . $courierObj->logo) : null,
            'courier_name_display' => $courierObj?->name ?? strtoupper((string) $shipment->courier),
            'service'             => $shipment->service ?? Arr::get($payload, 'courier_type'),
            'weight'              => $shipment->total_weight ?? Arr::get($payload, 'weight') ?? 0,
            'cost'                => $shipment->cost,
            'estimated_days'      => $shipment->estimated_days,
            'origin_name'         => Arr::get($payload, 'origin.contact_name')
                ?: Setting::getValue('store_name', config('app.name')),
            'origin_phone'        => $opts['sender_phone']
                ? (Arr::get($payload, 'origin.contact_phone') ?: Setting::getValue('biteship_origin_contact_phone'))
                : null,
            'origin_address'      => $opts['sender_address']
                ? (Arr::get($payload, 'origin.address') ?: Setting::getValue('biteship_origin_address'))
                : null,
            'destination_name'    => $destinationName,
            'destination_phone'   => $opts['receiver_phone']
                ? (Arr::get($payload, 'destination.contact_phone') ?: $order->address?->phone)
                : null,
            'destination_address' => Arr::get($payload, 'destination.address')
                ?: collect([
                    $order->address?->address,
                    $order->address?->subdistrict,
                    $order->address?->district,
                    $order->address?->city,
                    $order->address?->province,
                    $order->address?->postal_code,
                ])->filter()->implode(', '),
        ];

        // Courier label (local path for DomPDF)
        $courierLabel = [
            'name' => $courierObj?->name ?? strtoupper((string) $shipment->courier),
            'logo' => $courierObj?->logo ?? null,
        ];

        $labelOptions = [
            'shipping_cost'    => $opts['shipping_cost'],
            'item_description' => $opts['item_description'],
            'sender_phone'     => $opts['sender_phone'],
            'sender_address'   => $opts['sender_address'],
            'receiver_phone'   => $opts['receiver_phone'],
            'paper_size'       => $request->input('paper_size', 'thermal2'),
        ];

        $pdfMode = true;

        // Paper sizes (in points: 1cm = 28.35pt)
        $paperSizes = [
            'a4'       => [0, 0, 595, 842],  // 21 × 29.7 cm
            'thermal1' => [0, 0, 227, 284],  // 8 × 10 cm
            'thermal2' => [0, 0, 284, 426],  // 10 × 15 cm
        ];
        $paper = $paperSizes[$request->input('paper_size', 'thermal2')] ?? $paperSizes['thermal2'];

        $pdf = Pdf::loadView('orders.biteship-label',
            compact('order', 'label', 'labelOptions', 'courierLabel', 'pdfMode'))
            ->setPaper($paper, 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'defaultMediaType' => 'print',
                'isFontSubsettingEnabled' => true,
                'margin_top'    => 0,
                'margin_right'  => 0,
                'margin_bottom' => 0,
                'margin_left'   => 0,
            ]);

        return $pdf->download('resi-' . $order->order_number . '.pdf');
    }

    // ─── API: show (for fetch) ─────────────────────────────────────────────────

    public function showApi($id)
    {
        $order = Order::with([
            'user',
            'items.product.images',
            'items.variant',
            'payment',
            'shipment',
            'addresses',
            'coupon',
            'reviews',
        ])->findOrFail($id);

        return response()->json($order);
    }

    private function resolveFilters(Request $request): array
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,processing,shipped,delivered,cancelled,refunded'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        return [
            filled($validated['start_date'] ?? null) ? Carbon::parse($validated['start_date'])->startOfDay() : null,
            filled($validated['end_date'] ?? null) ? Carbon::parse($validated['end_date'])->endOfDay() : null,
            $validated['status'] ?? null,
        ];
    }

    private function biteshipLabelUrl(array $payload): ?string
    {
        return Arr::get($payload, 'label_url')
            ?: Arr::get($payload, 'waybill_label_url')
            ?: Arr::get($payload, 'courier.label_url')
            ?: Arr::get($payload, 'courier.waybill_label_url')
            ?: Arr::get($payload, 'data.label_url')
            ?: Arr::get($payload, 'data.waybill_label_url')
            ?: Arr::get($payload, 'data.courier.label_url')
            ?: Arr::get($payload, 'data.courier.waybill_label_url');
    }

    private function maskName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            return '-';
        }

        return mb_substr($name, 0, 1) . str_repeat('*', max(3, mb_strlen($name) - 1));
    }

    private function renderOrderIdentity(Order $order): string
    {
        return '<div class="font-black text-brand-primary text-sm font-mono">' . e($order->order_number) . '</div>'
            . '<div class="text-[10px] text-gray-400 mt-0.5">ID #' . e($order->id) . '</div>';
    }

    private function renderCustomer(Order $order): string
    {
        $name = $order->user->name ?? '-';
        $email = $order->user->email ?? '';
        $initial = strtoupper(substr($name !== '-' ? $name : 'U', 0, 1));

        return '<div class="flex items-center gap-3">'
            . '<div class="w-8 h-8 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary font-black text-xs flex-shrink-0">' . e($initial) . '</div>'
            . '<div><div class="font-semibold text-brand-dark text-sm">' . e($name) . '</div>'
            . '<div class="text-[10px] text-gray-400">' . e($email) . '</div></div></div>';
    }

    private function renderItems(Order $order): string
    {
        return '<div class="font-bold text-gray-700">' . number_format($order->items->count()) . ' item</div>'
            . '<div class="text-[10px] text-gray-400 mt-0.5 max-w-[160px] truncate">' . e($order->items->pluck('product_name')->implode(', ')) . '</div>';
    }

    private function renderTotal(Order $order): string
    {
        $html = '<div class="font-extrabold text-brand-dark text-sm">Rp ' . number_format($order->total, 0, ',', '.') . '</div>';

        if ($order->discount > 0) {
            $html .= '<div class="text-[10px] text-green-500 font-semibold mt-0.5"><i class="fa-solid fa-tag text-[8px]"></i> Diskon Rp '
                . number_format($order->discount, 0, ',', '.') . '</div>';
        }

        if ($order->shipping_cost > 0) {
            $html .= '<div class="text-[10px] text-gray-400 mt-0.5">Ongkir: Rp '
                . number_format($order->shipping_cost, 0, ',', '.') . '</div>';
        }

        return $html;
    }

    private function renderPayment(Order $order): string
    {
        $payment = $order->payment;

        if (!$payment) {
            return '<span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider bg-gray-50 text-gray-400">Belum Bayar</span>';
        }

        $class = match ($payment->status) {
            'success' => 'bg-green-50 text-green-600',
            'pending' => 'bg-amber-50 text-amber-600',
            'failed' => 'bg-red-50 text-red-600',
            'expired' => 'bg-gray-100 text-gray-500',
            default => 'bg-purple-50 text-purple-600',
        };

        $html = '<span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider ' . $class . '">'
            . e(ucfirst($payment->status)) . '</span>';

        if ($payment->payment_method) {
            $html .= '<div class="text-[10px] text-gray-400 mt-1">' . e(strtoupper($payment->payment_method)) . '</div>';
        }

        return $html;
    }

    private function renderStatus(Order $order): string
    {
        $statusCfg = [
            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Pending'],
            'confirmed' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Dikonfirmasi'],
            'processing' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'label' => 'Diproses'],
            'shipped' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'label' => 'Dikirim'],
            'delivered' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Terkirim'],
            'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'Dibatalkan'],
            'refunded' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'label' => 'Refund'],
        ];
        $cfg = $statusCfg[$order->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'label' => $order->status];

        return '<span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider '
            . $cfg['bg'] . ' ' . $cfg['text'] . '">' . e($cfg['label']) . '</span>';
    }

    private function renderDate(Order $order): string
    {
        return '<div class="font-semibold text-gray-700 text-sm">' . e($order->created_at->format('d M Y')) . '</div>'
            . '<div class="text-[10px] text-gray-400">' . e($order->created_at->format('H:i')) . '</div>';
    }

    private function renderActions(Order $order): string
    {
        // Pesanan yang sudah dibatalkan: hanya tampilkan tombol lihat detail
        if ($order->status === 'cancelled') {
            return '<div class="flex items-center justify-center gap-2">'
                . '<a href="' . route('orders.show', $order->id) . '" class="w-9 h-9 flex items-center justify-center bg-brand-primary/10 text-brand-primary rounded-xl hover:bg-brand-primary hover:text-white transition-all shadow-sm" title="Lihat Detail">'
                . '<i class="fa-solid fa-eye text-xs"></i></a>'
                . '<span class="px-2 py-1 rounded-lg bg-red-50 text-red-400 text-[10px] font-black">Dibatalkan</span>'
                . '</div>';
        }

        $html = '<div class="flex items-center justify-center gap-2">'
            . '<a href="' . route('orders.show', $order->id) . '" class="w-9 h-9 flex items-center justify-center bg-brand-primary/10 text-brand-primary rounded-xl hover:bg-brand-primary hover:text-white transition-all shadow-sm" title="Lihat Detail">'
            . '<i class="fa-solid fa-eye text-xs"></i></a>'
            . '<button onclick="openStatusModal(' . $order->id . ', \'' . e($order->status) . '\')" class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Ubah Status">'
            . '<i class="fa-solid fa-pen-to-square text-xs"></i></button>';

        if (in_array($order->status, ['pending', 'confirmed'], true)) {
            $html .= '<button onclick="cancelOrder(' . $order->id . ')" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Batalkan">'
                . '<i class="fa-solid fa-ban text-xs"></i></button>';
        }

        return $html . '</div>';
    }

    public function pollNotifications(Request $request)
    {
        $since = $request->input('since')
            ? Carbon::createFromTimestampMs((int) $request->input('since'))
            : now()->subSeconds(35);

        // Batas maksimal lookback agar tidak flood event lama
        if ($since->lt(now()->subMinutes(5))) {
            $since = now()->subMinutes(5);
        }

        $events = [];

        // Pesanan baru masuk (pending, dibuat setelah $since)
        Order::with('user:id,name')
            ->where('status', 'pending')
            ->where('created_at', '>', $since)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->each(function (Order $order) use (&$events) {
                $events[] = [
                    'type'         => 'new_order',
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                    'customer'     => $order->user?->name ?? 'Guest',
                    'total'        => $order->total_price,
                    'at'           => $order->created_at->toIso8601String(),
                ];
            });

        // Pesanan baru saja dibayar (payment berubah jadi success/settlement setelah $since)
        Order::with(['user:id,name', 'payment'])
            ->whereHas('payment', fn ($q) => $q->whereIn('status', ['success', 'settlement'])->where('updated_at', '>', $since))
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->each(function (Order $order) use (&$events) {
                $events[] = [
                    'type'         => 'paid',
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                    'customer'     => $order->user?->name ?? 'Guest',
                    'total'        => $order->total_price,
                    'method'       => $order->payment?->payment_method ?? '-',
                    'at'           => $order->payment?->updated_at->toIso8601String(),
                ];
            });

        // Pesanan baru dibatalkan (oleh customer) setelah $since
        Order::with('user:id,name')
            ->where('status', 'cancelled')
            ->where('updated_at', '>', $since)
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->each(function (Order $order) use (&$events) {
                $events[] = [
                    'type'         => 'cancelled',
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                    'customer'     => $order->user?->name ?? 'Guest',
                    'total'        => $order->total_price,
                    'at'           => $order->updated_at->toIso8601String(),
                ];
            });

        return response()->json([
            'events'    => $events,
            'server_ts' => now()->getTimestampMs(),
        ]);
    }
}
