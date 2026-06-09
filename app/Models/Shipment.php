<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier',
        'service',
        'service_code',
        'cost',
        'total_weight',
        'resi',
        'status',
        'origin_city_id',
        'destination_city_id',
        'estimated_days',
        'tracking_history',
        'tracked_at',
    ];

    protected $casts = [
        'tracking_history' => 'array',
        'tracked_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
