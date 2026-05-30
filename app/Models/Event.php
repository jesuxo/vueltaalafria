<?php
// app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'year', 'name', 'description', 'start_date', 'end_date',
        'registration_start', 'registration_end', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_start' => 'date',
        'registration_end' => 'date',
        'is_active' => 'boolean'
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function athleteParticipations()
    {
        return $this->hasMany(AthleteEventParticipation::class);
    }

    public function isRegistrationOpen()
    {
        $now = now();
        return $now->between($this->registration_start, $this->registration_end);
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' - ' . $this->year;
    }
}
