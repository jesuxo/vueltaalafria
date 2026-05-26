<?php
// app/Models/CwRecordatorio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CwRecordatorio extends Model
{
    protected $table = 'cwrecordatorios';

    protected $fillable = [
        'mantenimiento_id',
        'tipo',
        'enviado_por',
        'fecha_envio',
        'estado',
        'respuesta_cliente',
        'fecha_respuesta',
        'observaciones'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_respuesta' => 'datetime'
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(CWMantenimiento::class, 'mantenimiento_id');
    }

    public function getRespuestaTextoAttribute()
    {
        $respuestas = [
            'pendiente' => '⏳ Pendiente',
            'confirmado' => '✅ Confirmado',
            'rechazado' => '❌ Rechazado',
            'reprogramado' => '📅 Reprogramado'
        ];

        return $respuestas[$this->respuesta_cliente] ?? $this->respuesta_cliente;
    }
}
