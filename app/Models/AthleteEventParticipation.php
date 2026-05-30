<?php
// app/Models/AthleteEventParticipation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AthleteEventParticipation extends Model
{
    protected $table = 'athlete_event_participations';

    protected $fillable = [
        'athlete_id', 'event_id', 'dorsal_number', 'team_id', 'status'
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
