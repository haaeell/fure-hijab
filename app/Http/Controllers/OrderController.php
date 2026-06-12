<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use App\Services\BiteshipService;
use App\Services\ShipmentTrackingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        [$startDate, $endDate, $status] = $this->resolveFilters($request);

        $ordersQuery = Order::query()
            ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate->toDateString()))
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate->toDateString()));

        $summary = [
            'total' => (clone $ordersQuery)->count(),
            'pending' => (clone $ordersQuery)->whereIn('status', ['pending', 'confirmed'])->count(),
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
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
            'resi'   => 'nullable|string|max:100',
            'note'   => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $order->update(['status' => $request->status]);

            // Update / create shipment resi when status = shipped
            if ($request->status === 'shipped' && $request->filled('resi')) {
                $shipment = $order->shipment ?? new Shipment(['order_id' => $order->id]);
                $shipment->resi   = $request->resi;
                $shipment->status = 'in_transit';
                $shipment->save();
            }

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

    public function trackShipment($id, ShipmentTrackingService $trackingService)
    {
        $order = Order::with('shipment')->findOrFail($id);

        if (!$order->shipment || blank($order->shipment->resi)) {
            return redirect()->back()->with('error', 'Nomor resi belum tersedia.');
        }

        try {
            $tracking = $trackingService->trackShipment($order->shipment);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Tracking Biteship gagal: ' . $e->getMessage());
        }

        $order->shipment->update([
            'tracking_history' => $tracking,
            'tracked_at' => now(),
            'status' => str_contains(strtolower((string) $trackingService->latestStatus($tracking)), 'delivered')
                ? 'delivered'
                : 'in_transit',
        ]);

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
            'price' => Arr::get($payload, 'order_price')
                ?: Arr::get($payload, 'price')
                ?: $order->shipment->cost,
            'service' => Arr::get($payload, 'courier_type')
                ?: Arr::get($courier, 'type')
                ?: $order->shipment->service,
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
        ];

        if ($labelOptions['mask_receiver_name']) {
            $label['destination_name'] = $this->maskName((string) $label['destination_name']);
        }

        return view('orders.biteship-label', compact('order', 'label', 'labelOptions'));
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
            'status' => ['nullable', 'in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'],
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
}
