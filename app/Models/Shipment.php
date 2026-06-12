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
}
