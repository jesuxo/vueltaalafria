<?php
// app/Models/Photo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'stage_id',  // Cambiado: stage_id
        'filename',
        'original_name',
        'thumbnail_path',
        'full_path',
        'description',
        'tags',
        'price',
        'downloads',
        'is_active'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Relación con Stage
    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');  // Cambiado: stage_id
    }

    public function orderItems()
    {
        return $this->hasMany(PhotoOrderItem::class);
    }
}
