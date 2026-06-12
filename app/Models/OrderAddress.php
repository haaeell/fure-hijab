<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'receiver_name',
        'phone',
        'address',
        'province',
        'city',
        'district',
        'subdistrict',
        'postal_code',
        'biteship_area_id',
        'latitude',
        'longitude',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
