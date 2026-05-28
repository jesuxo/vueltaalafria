<?php
// app/Models/TeamVehicle.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamVehicle extends Model
{
    protected $table = 'team_vehicles';

    protected $fillable = [
        'team_id', 'brand', 'model', 'plate', 'color', 'capacity',
        'type', 'country_origin', 'driver_name', 'driver_phone', 'notes'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Tipos de vehículos
    public static function getTypes()
    {
        return [
            'Autobús' => 'Autobús',
            'Camioneta' => 'Camioneta',
            'Carro' => 'Carro Particular',
            'Furgón' => 'Furgón/Camper',
            'Camión' => 'Camión de carga'
        ];
    }
}
