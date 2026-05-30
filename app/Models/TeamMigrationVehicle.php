<?php
// app/Models/TeamMigrationData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMigrationVehicle extends Model
{
    protected $table = 'team_migration_data';

    protected $fillable = [
        'team_id', 'arrival_date', 'arrival_border', 'transport_type',
        'return_date', 'return_flight_time', 'notes'
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'return_date' => 'date',
        'return_flight_time' => 'datetime'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
