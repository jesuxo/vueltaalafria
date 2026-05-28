<?php
// app/Models/TeamStaff.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamStaff extends Model
{
    protected $table = 'team_staff';

    protected $fillable = [
        'team_id', 'full_name', 'identification_number', 'role',
        'phone', 'email', 'position', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Roles predefinidos
    public static function getRoles()
    {
        return [
            'Entrenador',
            'Asistente Técnico',
            'Médico',
            'Fisioterapeuta',
            'Mecánico',
            'Padre/Acompañante',
            'Manager',
            'Conductor'
        ];
    }
}
