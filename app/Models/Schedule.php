<?php
// app/Models/Schedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'stage_id', 'category', 'start_time', 'meeting_time',
        'distance', 'laps', 'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'meeting_time' => 'datetime',
        'distance' => 'decimal:2'
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    // Accessor para hora formateada
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i') : null;
    }
}
