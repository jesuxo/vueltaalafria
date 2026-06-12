<?php
// app/Models/Photo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'stage_id',
        'filename',
        'original_name',
        'thumbnail_path',
        'preview_path',
        'original_path',
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

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function orderItems()
    {
        return $this->hasMany(PhotoOrderItem::class);
    }

    // Obtener la ruta completa del archivo original (para descarga)
    public function getOriginalFullPathAttribute()
    {
        return storage_path('app/private/' . $this->original_path);
    }
}
