<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_channel',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'snap_token',
        'proof_image',
        'proof_uploaded_at',
        'reviewed_at',
        'reviewed_by',
        'review_note',
        'payment_method',
        'status',
        'amount',
        'paid_at',
        'payload',
        'expired_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'paid_at' => 'datetime',
        'proof_uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
