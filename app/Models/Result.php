<?php
// app/Models/Result.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'athlete_id', 'stage_id', 'position', 'finish_time',
        'average_speed', 'points', 'sprint_points', 'mountain_points'
    ];

    protected $casts = [
        'finish_time' => 'datetime',
        'average_speed' => 'decimal:2',
        'position' => 'integer',
        'points' => 'integer',
        'sprint_points' => 'integer',
        'mountain_points' => 'integer'
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    // Scope para resultados por etapa
    public function scopeByStage($query, $stageId)
    {
        return $query->where('stage_id', $stageId)->orderBy('position');
    }

    // Scope para clasificación general
    public function scopeGeneralClassification($query)
    {
        return $query->select('athlete_id')
            ->selectRaw('SUM(points) as total_points')
            ->groupBy('athlete_id')
            ->orderBy('total_points', 'desc');
    }
}
