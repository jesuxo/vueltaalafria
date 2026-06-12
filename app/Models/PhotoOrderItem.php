<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoOrderItem extends Model
{
    protected $fillable = ['photo_order_id', 'photo_id', 'price'];

    public function order()
    {
        return $this->belongsTo(PhotoOrder::class, 'photo_order_id');
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}
