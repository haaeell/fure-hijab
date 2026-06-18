<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'biteship_order_id',
        'courier',
        'service',
        'service_code',
        'cost',
        'total_weight',
        'resi',
        'label_url',
        'status',
        'origin_city_id',
        'destination_city_id',
        'estimated_days',
        'tracking_history',
        'biteship_payload',
        'tracked_at',
    ];

    protected $casts = [
        'tracking_history' => 'array',
        'biteship_payload' => 'array',
        'tracked_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** All known Biteship shipment statuses + legacy values. */
    public static function statusMap(): array
    {
        return [
            'pending'           => ['label' => 'Menunggu Pickup',        'class' => 'bg-gray-100 text-gray-500'],
            'confirmed'         => ['label' => 'Dikonfirmasi Kurir',      'class' => 'bg-blue-50 text-blue-600'],
            'allocated'         => ['label' => 'Kurir Dialokasikan',      'class' => 'bg-cyan-50 text-cyan-600'],
            'picking_up'        => ['label' => 'Kurir Menuju Pickup',     'class' => 'bg-indigo-50 text-indigo-600'],
            'picked'            => ['label' => 'Paket Dijemput',          'class' => 'bg-purple-50 text-purple-600'],
            'dropping_off'      => ['label' => 'Dalam Perjalanan',        'class' => 'bg-orange-50 text-orange-600'],
            'in_transit'        => ['label' => 'Dalam Perjalanan',        'class' => 'bg-orange-50 text-orange-600'],
            'delivered'         => ['label' => 'Terkirim',                'class' => 'bg-green-50 text-green-700'],
            'cancelled'         => ['label' => 'Dibatalkan',              'class' => 'bg-red-50 text-red-600'],
            'on_hold'           => ['label' => 'Ditahan Sementara',       'class' => 'bg-yellow-50 text-yellow-700'],
            'return_in_transit' => ['label' => 'Retur Dalam Proses',      'class' => 'bg-amber-50 text-amber-700'],
            'returned'          => ['label' => 'Diretur ke Pengirim',     'class' => 'bg-orange-50 text-orange-700'],
            'disposed'          => ['label' => 'Paket Dimusnahkan',       'class' => 'bg-gray-100 text-gray-600'],
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusMap()[$this->status]['label']
            ?? ucwords(str_replace('_', ' ', $this->status ?? 'pending'));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return static::statusMap()[$this->status]['class'] ?? 'bg-gray-100 text-gray-500';
    }

    /** Whether the shipment is in a problem state. */
    public function hasIssue(): bool
    {
        return in_array($this->status, ['cancelled', 'on_hold', 'return_in_transit', 'returned', 'disposed'], true);
    }
}
