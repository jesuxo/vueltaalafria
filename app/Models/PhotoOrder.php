<?php
// app/Models/PhotoOrder.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PhotoOrder extends Model
{
    protected $table = 'photo_orders';

    protected $fillable = [
        'order_number', 'public_code', 'customer_name', 'customer_email', 'customer_phone',
        'subtotal', 'total', 'payment_method', 'payment_reference',
        'payment_proof', 'status', 'notes', 'paid_at', 'delivered_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(PhotoOrderItem::class);
    }

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'photo_order_items')
            ->withPivot('price')
            ->withTimestamps();
    }

    // Generar código público único
    public static function generatePublicCode()
    {
        do {
            $code = 'FOTO-' . strtoupper(Str::random(8));
        } while (self::where('public_code', $code)->exists());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $order->public_code = self::generatePublicCode();
        });
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->delivered_at = now();
        $this->save();
    }
}
