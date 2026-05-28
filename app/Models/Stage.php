<?php
// app/Models/Stage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $table = 'stages';

    protected $fillable = [
        'stage_number', 'date', 'name', 'description',
        'start_location', 'finish_location', 'total_distance',
        'route_details', 'sprints', 'mountain_prizes'
    ];

    protected $casts = [
        'date' => 'date',
        'total_distance' => 'decimal:2',
        'sprints' => 'array',
        'mountain_prizes' => 'array'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    // Accessor para nombre con número
    public function getFullNameAttribute()
    {
        return "Etapa {$this->stage_number}: {$this->name}";
    }
}
