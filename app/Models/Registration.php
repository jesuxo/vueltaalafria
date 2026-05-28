<?php
// app/Models/Registration.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $table = 'registrations';

    protected $fillable = [
        'registration_type', 'team_id', 'athlete_id', 'email', 'phone',
        'status', 'payment_proof', 'amount', 'notes', 'registered_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    // Estados de inscripción
    public static function getStatuses()
    {
        return [
            'pending' => 'Pendiente',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'paid' => 'Pagada'
        ];
    }

    // Tipos de inscripción
    public static function getTypes()
    {
        return [
            'individual' => 'Individual',
            'team' => 'Por Equipo'
        ];
    }
}
