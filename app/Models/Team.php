<?php
// app/Models/Team.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'name', 'logo', 'city', 'country', 'contact_email',
        'contact_phone', 'is_active', 'user_id', 'password_hash', 'access_code'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function staff()
    {
        return $this->hasMany(TeamStaff::class);
    }

    public function vehicles()
    {
        return $this->hasMany(TeamVehicle::class);
    }

    public function photos()
    {
        return $this->hasMany(TeamPhoto::class);
    }
}
