<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'coupon_code',
        'status',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'coupon_id'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            OrderItem::class,
            'order_id',
            'order_item_id',
            'id',
            'id'
        );
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Pending',
            'confirmed'  => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'delivered'  => 'Terkirim',
            'cancelled'  => 'Dibatalkan',
            'refunded'   => 'Refund',
            default      => ucfirst($this->status),
        };
    }
}
