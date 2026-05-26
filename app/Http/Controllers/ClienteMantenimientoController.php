<?php
// app/Http/Controllers/ClienteMantenimientoController.php

namespace App\Http\Controllers;

use App\Models\CWMantenimiento;
use App\Models\CwRecordatorio;
use App\Models\CWVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClienteMantenimientoController extends Controller
{
    /**
     * Ver mantenimientos por token
     */
    public function verPorToken($token){

    }

    public function verMantenimiento($token)
    {
        $mantenimiento = CWMantenimiento::with([
            'vehiculo',
            'vehiculo.cliente',
            'vehiculo.tipo',
            'productos',
            'fotos',
            'vendedor'
        ])
            ->where('token_cliente', $token)
            ->firstOrFail();

        // Obtener todos los mantenimientos del mismo vehículo
        $historial = CWMantenimiento::with(['productos', 'fotos'])
            ->where('fk_vehiculo', $mantenimiento->fk_vehiculo)
            ->orderBy('fecha_mantenimiento', 'desc')
            ->get();

        // Obtener otros vehículos del mismo cliente
        $otrosVehiculos = CWVehiculo::with(['tipo', 'mantenimientos' => function($q) {
            $q->latest('fecha_mantenimiento')->limit(1);
        }])
            ->where('codclie', $mantenimiento->codclie)
            ->where('id', '!=', $mantenimiento->fk_vehiculo)
            ->get();

        // Registrar acceso para estadísticas (opcional)
        Log::info('Cliente accedió a mantenimiento', [
            'token' => $token,
            'mantenimiento_id' => $mantenimiento->id,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return view('cliente.mantenimiento-ver', compact('mantenimiento', 'historial', 'otrosVehiculos'));
    }

    /**
     * Generar token para un mantenimiento (usado desde el panel admin)
     */
    public function generarToken(Request $request, $id)
    {
        $mantenimiento = CWMantenimiento::findOrFail($id);

        $token = $mantenimiento->generarTokenCliente();

        $url = route('cliente.mantenimiento.ver', $token);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'token' => $token,
                'url' => $url,
                'mantenimiento_id' => $mantenimiento->id
            ]);
        }

        return redirect()->back()->with([
            'success' => 'Enlace generado correctamente',
            'enlace_cliente' => $url
        ]);
    }

    /**
     * Enviar enlace por WhatsApp
     */
    public function enviarWhatsApp(Request $request, $id)
    {
        $request->validate([
            'telefono' => 'required|string'
        ]);

        $mantenimiento = CWMantenimiento::with('vehiculo', 'vehiculo.cliente')->findOrFail($id);

        // Generar token si no existe
        if (!$mantenimiento->token_cliente) {
            $mantenimiento->generarTokenCliente();
        }

        $url = route('cliente.mantenimiento.ver', $mantenimiento->token_cliente);

        $mensaje = "Hola! Te comparto el detalle del mantenimiento realizado a tu {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} el día {$mantenimiento->fechaformat}. Podés ver todos los detalles aquí: " . $url;

        $telefono = preg_replace('/[^0-9]/', '', $request->telefono);

        // Aquí integrarías con API de WhatsApp (Twilio, WATI, etc.)
        // Por ahora solo retornamos la URL de WhatsApp Web
        $whatsappUrl = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);

        return response()->json([
            'success' => true,
            'whatsapp_url' => $whatsappUrl,
            'url' => $url,
            'mensaje' => $mensaje
        ]);
    }

    public function confirmarVista($token, Request $request)
    {
        $mantenimiento = CWMantenimiento::with(['vehiculo', 'vehiculo.cliente'])
            ->where('token_cliente', $token)
            ->firstOrFail();

        $recordatorioId = $request->get('recordatorio', 0);
        $respuestaParam = $request->get('respuesta', '');

        $yaRespondio = false;
        $fechaRespuesta = null;
        $respuestaUsuario = '';

        // Si viene con respuesta por URL (whatsapp) y NO ha respondido antes
        if ( $respuestaParam) {
            if ($respuestaParam == 'si') {
                return $this->procesarConfirmacionFromUrl($token, true, $recordatorioId);
            } elseif ($respuestaParam == 'no') {
                return $this->procesarConfirmacionFromUrl($token, false, $recordatorioId);
            }
        }

        return view('cliente.confirmar-mantenimiento', compact(
            'mantenimiento',
            'yaRespondio',
            'fechaRespuesta',
            'respuestaUsuario',
            'recordatorioId'
        ));
    }

    public function procesarConfirmacion(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'confirmar' => 'required', // Cambiamos de boolean a required
            'recordatorio_id' => 'nullable|integer'
        ]);

        $mantenimiento = CWMantenimiento::where('token_cliente', $request->token)->firstOrFail();

        // Convertir el valor a booleano/entero correctamente
        $confirmar = filter_var($request->confirmar, FILTER_VALIDATE_BOOLEAN);

        // Actualizar mantenimiento
        $mantenimiento->confirmo_asistencia = $confirmar ? 1 : 0; // Usar 1/0 en lugar de true/false
        $mantenimiento->fecha_confirmacion = now();
        $mantenimiento->cliente_contactado = 1;
        $mantenimiento->fecha_contactado = now();
        $mantenimiento->save();

        // Actualizar recordatorio si existe
        if ($request->recordatorio_id) {
            $recordatorio = CwRecordatorio::find($request->recordatorio_id);
            if ($recordatorio) {
                $recordatorio->respuesta_cliente = $confirmar ? 'confirmado' : 'rechazado';
                $recordatorio->fecha_respuesta = now();
                $recordatorio->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Confirmación registrada',
            'confirmo' => $confirmar
        ]);
    }

    private function procesarConfirmacionFromUrl($token, $confirmar, $recordatorioId = 0)
    {

        $mantenimiento = CWMantenimiento::where('token_cliente', $token)->firstOrFail();

        // Convertir a entero
        $valorConfirmar = $confirmar ? 1 : 0;

        // Actualizar mantenimiento
        $mantenimiento->confirmo_asistencia = $valorConfirmar;
        $mantenimiento->fecha_confirmacion = now();
        $mantenimiento->cliente_contactado = 1;
        $mantenimiento->fecha_contactado = now();
        $mantenimiento->save();

        // Actualizar recordatorio si existe
        if ($recordatorioId) {
            $recordatorio = CwRecordatorio::find($recordatorioId);
            if ($recordatorio) {
                $recordatorio->respuesta_cliente = $confirmar ? 'confirmado' : 'rechazado';
                $recordatorio->fecha_respuesta = now();
                $recordatorio->save();

            }
        }


        // Redirigir a la vista de confirmación
        return redirect()->route('cliente.mantenimiento.confirmar-vista', [
            'token' => $token,
            'recordatorio' => $recordatorioId,
            'respuesta_procesada' => $confirmar ? 'si' : 'no'
        ])->with('success', '¡Gracias por confirmar!');
    }
}
