<?php
// app/Models/Athlete.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $table = 'athletes';

    protected $fillable = [
        'first_name', 'last_name', 'dorsal_number', 'team_id','document_type','document_number','uci_id',
        'gender', 'category', 'birth_date', 'nationality', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // Accessor para nombre completo
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Accessor para edad
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }
}
