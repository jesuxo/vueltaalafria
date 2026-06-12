<?php
// app/Models/Stage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = [
        'name', 'type', 'stage_number', 'date', 'description',
        'distance', 'start_location', 'end_location', 'icon',
        'is_active', 'order'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean'
    ];

    // Relación con fotos
    public function photos()
    {
        return $this->hasMany(Photo::class, 'stage_id');
    }

    // Scopes para filtrar por tipo
    public function scopeStages($query)
    {
        return $query->where('type', 'stage');
    }

    public function scopeSpecials($query)
    {
        return $query->where('type', 'special');
    }

    // Verificar si es etapa normal
    public function isStage()
    {
        return $this->type === 'stage';
    }

    // Verificar si es especial
    public function isSpecial()
    {
        return $this->type === 'special';
    }
}
