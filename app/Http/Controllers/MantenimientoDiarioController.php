<?php

namespace App\Http\Controllers;

use App\Models\CWMantenimiento;
use App\Models\CWVehiculo;
use App\Models\Saclie;
use App\Models\Savend;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MantenimientoDiarioController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));

        // Obtener mantenimientos del día con relaciones
        $mantenimientos = CWMantenimiento::with([
            'vehiculo',
            'vehiculo.cliente',
            'vehiculo.tipo',
            'vendedor',
            'usuario',
            'productos'
        ])
            ->whereDate('fecha_mantenimiento', $fecha)
            ->orderBy('id', 'desc')
            ->get();

        // Estadísticas del día
        $estadisticas = [
            'total'         => $mantenimientos->count(),
            'cambio_aceite' => $mantenimientos->where('tipo_mantenimiento', 'cambio_aceite')->count(),
            'total_ventas'  => $mantenimientos->sum('costo'),
            'con_vendedor'  => $mantenimientos->whereNotNull('codvend')->count()
        ];

        // Vendedores para filtros
        $vendedores = Savend::where('activo', 1)->orderBy('descrip')->get();

        return view('mantenimientosdiario', compact('mantenimientos', 'fecha', 'estadisticas', 'vendedores'));
    }

    public function getDetalle($id)
    {
        $mantenimiento = CWMantenimiento::with([
            'vehiculo.cliente',
            'vehiculo.tipo',
            'vendedor',
            'productos',
            'fotos'  // <-- IMPORTANTE: Asegúrate de cargar las fotos
        ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'html' => view('partials.detallerapido', compact('mantenimiento'))->render()
        ]);
    }

    private function getTipoEvidenciaTexto($tipo)
    {
        $tipos = [
            'cambio_aceite'  => 'Cambio de Aceite',
            'producto_usado' => 'Producto Usado',
            'kilometraje'    => 'Kilometraje',
            'general'        => 'General'
        ];

        return $tipos[$tipo] ?? $tipo;
    }

    public function updateCampo(Request $request, $id)
    {
        $request->validate([
            'campo' => 'required|string',
            'valor' => 'required'
        ]);

        $mantenimiento = CWMantenimiento::findOrFail($id);

        // Actualizar solo el campo permitido
        $campo = $request->campo;
        $valor = $request->valor;

        // Lista de campos permitidos para actualización rápida
        $camposPermitidos = ['observaciones', 'kilometraje', 'codvend', 'proximo_kilometraje'];

        if (in_array($campo, $camposPermitidos)) {
            $mantenimiento->$campo = $valor;
            $mantenimiento->save();

            // Si se actualizó el vendedor, recargar para mostrar nombre
            if ($campo == 'codvend') {
                $mantenimiento->load('vendedor');
            }

            return response()->json([
                'success' => true,
                'campo' => $campo,
                'valor' => $valor,
                'display' => $campo == 'codvend' ? ($mantenimiento->vendedor->descrip ?? 'No asignado') : $valor
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Campo no permitido'], 403);
    }

    public function cambiarFecha(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'nueva_fecha' => 'required|date'
        ]);

        $mantenimiento = CWMantenimiento::findOrFail($request->id);
        $mantenimiento->fecha_mantenimiento = $request->nueva_fecha;
        $mantenimiento->save();

        return response()->json([
            'success' => true,
            'message' => 'Fecha actualizada correctamente'
        ]);
    }

    public function imprimirComprobante($id)
    {
        $mantenimiento = CWMantenimiento::with([
            'vehiculo',
            'vehiculo.cliente',
            'vendedor',
            'productos'
        ])
            ->findOrFail($id);

        return view('mantenimientoscomprobante', compact('mantenimiento'));
    }

    public function contadordia(Request $request){

        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));

        $mantenimientos = CWMantenimiento::whereDate('fecha_mantenimiento', $fecha)
            ->orderBy('id', 'desc')
            ->count();

         return $mantenimientos;
    }


}
