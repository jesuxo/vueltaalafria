<?php
// app/Models/User.php (modificar el existente)

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type', // Agrega este campo si quieres diferenciar admin de usuarios normales
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con equipo
    public function team()
    {
        return $this->hasOne(Team::class);
    }

    // Verificar si es admin
    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    public function routeNotificationForWhatsapp()
    {
        return $this->phone; // Ej: +584247371101
    }
}
