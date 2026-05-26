<?php
// app/Http/Controllers/ReporteProximosMantenimientosController.php

namespace App\Http\Controllers;

use App\Models\CWMantenimiento;
use App\Models\Savend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteProximosMantenimientosController extends Controller
{
    // app/Http/Controllers/ReporteProximosMantenimientosController.php

    public function index(Request $request)
    {
        $dias = $request->get('dias', 7);
        $estado = $request->get('estado', 'todos');
        $vendedor = $request->get('vendedor', '');

        $query = CWMantenimiento::with(['vehiculo', 'vehiculo.cliente', 'vendedor'])
            ->whereNotNull('proximo_mantenimiento');

        // Filtrar por rango de días (incluyendo los que ya respondieron)
        if ($dias != 'todos') {
            $query->where(function($q) use ($dias) {
                $q->whereDate('proximo_mantenimiento', '<=', now()->addDays($dias))
                    ->whereDate('proximo_mantenimiento', '>=', now());
            });
        }

        // También incluir los que ya vencieron pero respondieron (últimos 30 días)
        $query->orWhere(function($q) {
            $q->whereNotNull('proximo_mantenimiento')
                ->whereDate('proximo_mantenimiento', '<', now())
                ->whereDate('proximo_mantenimiento', '>=', now()->subDays(30))
                ->where(function($q2) {
                    $q2->where('confirmo_asistencia', 1)
                        ->orWhere('cliente_contactado', 1);
                });
        });

        // Filtrar por estado
        if ($estado == 'contactados') {
            $query->where('cliente_contactado', 1);
        } elseif ($estado == 'confirmados') {
            $query->where('confirmo_asistencia', 1);
        } elseif ($estado == 'rechazados') {
            $query->where('confirmo_asistencia', 0)
                ->where('cliente_contactado', 1);
        } elseif ($estado == 'pendientes') {
            $query->where('cliente_contactado', 0)
                ->where('confirmo_asistencia', 0);
        } elseif ($estado == 'urgentes') {
            $query->whereDate('proximo_mantenimiento', '<=', now()->addDays(2))
                ->whereDate('proximo_mantenimiento', '>=', now())
                ->where('cliente_contactado', 0);
        }

        // Filtrar por vendedor
        if ($vendedor) {
            $query->where('codvend', $vendedor);
        }

        $query->orderBy('proximo_mantenimiento', 'asc');
        $mantenimientos = $query->get();

        // Estadísticas
        $estadisticas = [
            'total' => CWMantenimiento::whereNotNull('proximo_mantenimiento')
                ->whereDate('proximo_mantenimiento', '>=', now())
                ->count(),
            'urgentes' => CWMantenimiento::whereNotNull('proximo_mantenimiento')
                ->whereDate('proximo_mantenimiento', '<=', now()->addDays(2))
                ->whereDate('proximo_mantenimiento', '>=', now())
                ->count(),
            'contactados' => CWMantenimiento::whereNotNull('proximo_mantenimiento')
                ->where('cliente_contactado', 1)
                ->count(),
            'confirmados' => CWMantenimiento::whereNotNull('proximo_mantenimiento')
                ->where('confirmo_asistencia', 1)
                ->count(),
            'rechazados' => CWMantenimiento::whereNotNull('proximo_mantenimiento')
                ->where('confirmo_asistencia', 0)
                ->where('cliente_contactado', 1)
                ->count(),
        ];

        $vendedores = Savend::where('activo', 1)->orderBy('descrip')->get();

        return view('reportes.proximos-mantenimientos', compact(
            'mantenimientos', 'estadisticas', 'vendedores', 'dias', 'estado', 'vendedor'
        ));
    }

    public function marcarContactado(Request $request, $id)
    {
        $mantenimiento = CWMantenimiento::findOrFail($id);

        $mantenimiento->cliente_contactado = true;
        $mantenimiento->fecha_contactado = now();
        $mantenimiento->observaciones_seguimiento = $request->observaciones;
        $mantenimiento->save();

        return response()->json([
            'success' => true,
            'message' => 'Cliente marcado como contactado'
        ]);
    }

    public function marcarConfirmacion(Request $request, $id)
    {
        $mantenimiento = CWMantenimiento::findOrFail($id);

        $confirmar = filter_var($request->confirmar, FILTER_VALIDATE_BOOLEAN);

        $mantenimiento->confirmo_asistencia = $confirmar ? 1 : 0;
        $mantenimiento->fecha_confirmacion = now();
        $mantenimiento->observaciones_seguimiento = $request->observaciones;
        $mantenimiento->save();

        return response()->json([
            'success' => true,
            'message' => $confirmar ? 'Asistencia confirmada' : 'Confirmación cancelada'
        ]);
    }



    public function enviarRecordatorio(Request $request, $id)
    {
        $mantenimiento = CWMantenimiento::with('vehiculo', 'vehiculo.cliente')->findOrFail($id);

        // Generar token si no existe
        if (!$mantenimiento->token_cliente) {
            $mantenimiento->generarTokenCliente();
        }

        // Crear URL específica para confirmación
        $urlBase = route('cliente.mantenimiento.confirmar-vista', [
            'token' => $mantenimiento->token_cliente
        ]);

        $urlSi = $urlBase . '?respuesta=si';
        $urlNo = $urlBase . '?respuesta=no';

        $telefono = $mantenimiento->vehiculo->cliente->telef ?? $mantenimiento->vehiculo->cliente->movil;

        if (!$telefono) {
            return response()->json([
                'success' => false,
                'message' => 'El cliente no tiene teléfono registrado'
            ]);
        }

        $fecha = $mantenimiento->proximo_mantenimiento
            ? $mantenimiento->proximo_mantenimiento->format('d/m/Y')
            : 'próximamente';

        $mensaje = "Hola! Te recordamos que el próximo mantenimiento de tu {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} está programado para el {$fecha}.\n\n";
        $mensaje .= "📌 *¿Podrás asistir?* Confirmános haciendo clic aquí:\n";
        $mensaje .= "✅ Sí, asistiré: {$urlSi}\n";
        $mensaje .= "❌ No podré asistir: {$urlNo}\n\n";
        $mensaje .= "Si necesitas reprogramar, podés responder a este mensaje.";

        $telefono = preg_replace('/[^0-9]/', '', $telefono);
        $whatsappUrl = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);

        // Registrar el envío del recordatorio
        $recordatorio = new \App\Models\CwRecordatorio();
        $recordatorio->mantenimiento_id = $mantenimiento->id;
        $recordatorio->tipo = 'whatsapp';
        $recordatorio->enviado_por = auth()->user()->id ?? 'sistema';
        $recordatorio->fecha_envio = now();
        $recordatorio->estado = 'enviado';
        $recordatorio->respuesta_cliente = 'pendiente';
        $recordatorio->save();

        // Marcar al cliente como contactado
        $mantenimiento->cliente_contactado = true;
        $mantenimiento->fecha_contactado = now();
        $mantenimiento->save();

        return response()->json([
            'success' => true,
            'whatsapp_url' => $whatsappUrl,
            'url_si' => $urlSi,
            'url_no' => $urlNo,
            'mensaje' => $mensaje,
            'recordatorio_id' => $recordatorio->id
        ]);
    }
}
