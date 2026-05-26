<?php
// app/Models/CwAccesoCliente.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CwAccesoCliente extends Model
{
    protected $table = 'cwaccesos_cliente';

    protected $fillable = [
        'codclie', 'token', 'email', 'telefono', 'expira_at'
    ];

    protected $dates = ['expira_at'];

    public static function generarToken($codclie, $email = null, $telefono = null)
    {
        $token = Str::random(64);

        return self::create([
            'codclie' => $codclie,
            'token' => $token,
            'email' => $email,
            'telefono' => $telefono,
            'expira_at' => now()->addDays(30)
        ]);
    }

    public function cliente()
    {
        return $this->belongsTo(Saclie::class, 'codclie', 'codclie');
    }
}
