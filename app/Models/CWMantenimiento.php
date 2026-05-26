<?php
// app/Models/CWMantenimiento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CWMantenimiento extends Model
{
    protected $table = 'cwmantenimientos';

    protected $fillable = [
        'codclie', 'fk_vehiculo', 'fecha_mantenimiento', 'hora_mantenimiento',
        'kilometraje', 'tipo_mantenimiento', 'producto_utilizado', 'marca_producto',
        'costo', 'observaciones', 'proximo_mantenimiento',
        'proximo_kilometraje', 'realizado_por', 'codvend', 'token_cliente',
        'cliente_contactado', 'fecha_contactado', 'confirmo_asistencia',
        'fecha_confirmacion', 'observaciones_seguimiento'
    ];

    protected $casts = [
        'proximo_mantenimiento' => 'date',
        'fecha_contactado'      => 'datetime',
        'fecha_confirmacion'    => 'datetime',
        'confirmo_asistencia'   => 'boolean', // Esto ayuda a Laravel a manejar booleanos
        'cliente_contactado'    => 'boolean'
    ];

    protected $appends = ['fechaformat', 'horaformated', 'estado_proximo'];

    // Relaciones existentes
    public function vehiculo()
    {
        return $this->belongsTo(CWVehiculo::class, 'fk_vehiculo');
    }

    public function cliente()
    {
        return $this->belongsTo(Saclie::class, 'codclie', 'codclie');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }

    public function vendedor()
    {
        return $this->belongsTo(Savend::class, 'codvend', 'codvend');
    }

    public function productos()
    {
        return $this->hasMany(Cwmantenimientoproducto::class, 'mantenimiento_id');
    }

    public function fotos()
    {
        return $this->hasMany(Cwmantenimientofoto::class, 'mantenimiento_id')->orderBy('orden');
    }

    public function tipos()
    {
        return $this->hasMany(CwMantenimientoTipo::class, 'mantenimiento_id');
    }

    public function generarTokenCliente()
    {
        // Generar token numérico de 6 dígitos
        $token = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Verificar que no exista otro mantenimiento con el mismo token
        while (self::where('token_cliente', $token)->exists()) {
            $token = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        }

        $this->token_cliente = $token;
        $this->save();

        return $token;
    }

    public function getUrlAccesoClienteAttribute()
    {
        if (!$this->token_cliente) {
            return null;
        }
        return route('cliente.mantenimiento.ver', $this->token_cliente);
    }

    public function getEstadoProximoAttribute()
    {
        if (!$this->proximo_mantenimiento && !$this->proximo_kilometraje) {
            return 'sin_proximo';
        }

        if ($this->confirmo_asistencia) {
            return 'confirmado';
        }

        if ($this->cliente_contactado) {
            return 'contactado';
        }

        // Verificar si es pronto
        if ($this->proximo_mantenimiento) {
            $dias = now()->diffInDays($this->proximo_mantenimiento, false);

            if ($dias < 0) {
                return 'vencido';
            } elseif ($dias <= 2) {
                return 'urgente';
            } elseif ($dias <= 7) {
                return 'proximo';
            }
        }

        return 'pendiente';
    }

    // Scope para próximos mantenimientos
    public function scopeProximos($query, $dias = 7)
    {
        return $query->where(function($q) use ($dias) {
            $q->whereNotNull('proximo_mantenimiento')
                ->whereDate('proximo_mantenimiento', '<=', now()->addDays($dias))
                ->whereDate('proximo_mantenimiento', '>=', now());
        })->orWhere(function($q) {
            $q->whereNotNull('proximo_kilometraje');
        });
    }

    public function scopePorContactar($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('proximo_mantenimiento')
                ->whereDate('proximo_mantenimiento', '<=', now()->addDays(2))
                ->whereDate('proximo_mantenimiento', '>=', now())
                ->orWhereNotNull('proximo_kilometraje');
        })->where('cliente_contactado', false)
            ->where('confirmo_asistencia', false);
    }

    // Accessors existentes
    public function getFechaformatAttribute(){
        $date = $this->fecha_mantenimiento;
        if(isset($date)){
            list($y,$m,$d) = explode('-',$date);
            return "$d/$m/$y";
        }
    }

    public function getHoraformatedAttribute(){
        $hour = $this->hora_mantenimiento;
        if(isset($hour)){
            $time = \DateTime::createFromFormat('H:i:s', $hour);
            if ($time) {
                return $time->format('h:i A');
            }
            return $hour;
        }
        return null;
    }

    public function recordatorios()
    {
        return $this->hasMany(CwRecordatorio::class, 'mantenimiento_id')->orderBy('fecha_envio', 'desc');
    }

    public function ultimoRecordatorio()
    {
        return $this->hasOne(CwRecordatorio::class, 'mantenimiento_id')->latest('fecha_envio');
    }
}
