<?php
// app/Http/Controllers/VehiculoController.php

namespace App\Http\Controllers;

use App\Models\CWVehiculo;
use App\Models\Saclie;
use App\Models\CWTipoVehiculo;
use App\Models\CWMantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('busqueda', '');
        $vehiculo_id = $request->get('vehiculo_id', '');
        $tab = $request->get('tab', 'tab1');

        $vehiculos = collect();
        $vehiculo = null;
        $mantenimientos = collect();

        // Estadísticas de vehículos (últimos 6 meses)
        $estadisticas = $this->obtenerEstadisticas();

        // Buscar vehículos si hay criterio de búsqueda
        if (!empty($busqueda)) {
            $vehiculos = CWVehiculo::with(['cliente', 'tipo'])
                ->where('identificacion', 'LIKE', '%' . $busqueda . '%')
                ->orWhere('marca', 'LIKE', '%' . $busqueda . '%')
                ->orWhere('modelo', 'LIKE', '%' . $busqueda . '%')
                ->orWhere('serialmotor', 'LIKE', '%' . $busqueda . '%')
                ->orWhere('serialchasis', 'LIKE', '%' . $busqueda . '%')
                ->orWhereHas('cliente', function($q) use ($busqueda) {
                    $q->where('descrip', 'LIKE', '%' . $busqueda . '%')
                        ->orWhere('id3', 'LIKE', '%' . $busqueda . '%');
                })
                ->orderBy('marca')
                ->orderBy('modelo')
                ->limit(50)
                ->get();
        }

        // Si se seleccionó un vehículo específico
        if (!empty($vehiculo_id)) {
            $vehiculo = CWVehiculo::with(['cliente', 'tipo', 'mantenimientos' => function($q) {
                $q->orderBy('fecha_mantenimiento', 'desc');
            }])->find($vehiculo_id);

            if ($vehiculo) {
                $mantenimientos = $vehiculo->mantenimientos;
            }
        }

        $tipos = CWTipoVehiculo::orderBy('tipo')->get();
        $totalVehiculos = CWVehiculo::count();

        return view('vehiculos.index', compact(
            'busqueda',
            'vehiculos',
            'vehiculo',
            'mantenimientos',
            'tipos',
            'tab',
            'totalVehiculos',
            'estadisticas'
        ));
    }

    private function obtenerEstadisticas()
    {
        $hoy = now();
        $meses = [];
        $datos = [];

        // Generar los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subMonths($i);
            $meses[] = $fecha->format('M Y');

            $inicioMes = $fecha->copy()->startOfMonth();
            $finMes = $fecha->copy()->endOfMonth();

            $datos[] = CWVehiculo::whereBetween('created_at', [$inicioMes, $finMes])->count();
        }

        // Totales por período
        $totalUltimoMes = CWVehiculo::whereBetween('created_at', [
            $hoy->copy()->subMonth()->startOfMonth(),
            $hoy->copy()->subMonth()->endOfMonth()
        ])->count();

        $totalMesActual = CWVehiculo::whereBetween('created_at', [
            $hoy->copy()->startOfMonth(),
            $hoy->copy()->endOfMonth()
        ])->count();

        // Variación porcentual
        $variacion = 0;
        if ($totalUltimoMes > 0) {
            $variacion = round((($totalMesActual - $totalUltimoMes) / $totalUltimoMes) * 100, 1);
        }

        // Vehículos por tipo
        $porTipo = CWTipoVehiculo::withCount('vehiculos')
            ->orderBy('vehiculos_count', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'tipo' => $item->tipo,
                    'total' => $item->vehiculos_count
                ];
            });

        // Marcas más comunes
        $topMarcas = CWVehiculo::select('marca', DB::raw('count(*) as total'))
            ->whereNotNull('marca')
            ->where('marca', '!=', '')
            ->groupBy('marca')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return [
            'meses' => $meses,
            'datos' => $datos,
            'total_ultimo_mes' => $totalUltimoMes,
            'total_mes_actual' => $totalMesActual,
            'variacion' => $variacion,
            'por_tipo' => $porTipo,
            'top_marcas' => $topMarcas,
            'total_general' => CWVehiculo::count(),
            'con_mantenimientos' => CWVehiculo::has('mantenimientos')->count(),
            'sin_mantenimientos' => CWVehiculo::doesntHave('mantenimientos')->count(),
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'fk_tipo' => 'required|exists:cwtipovehiculo,id',
            'modelo' => 'required|string|max:50',
            'marca' => 'required|string|max:50',
            'identificacion' => 'required|string|max:50|unique:cwvehiculo,identificacion',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'serialmotor' => 'nullable|string|max:100',
            'serialchasis' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:200'
        ]);

        $vehiculo = CWVehiculo::create([
            'codclie' => $request->codclie ?? '000000',
            'fk_tipo' => $request->fk_tipo,
            'modelo' => $request->modelo,
            'marca' => $request->marca,
            'identificacion' => $request->identificacion,
            'year' => $request->year,
            'serialmotor' => $request->serialmotor,
            'serialchasis' => $request->serialchasis,
            'observaciones' => $request->observaciones
        ]);

        return redirect()->route('vehiculos.index', ['vehiculo_id' => $vehiculo->id])
            ->with('success', 'Vehículo creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $vehiculo = CWVehiculo::findOrFail($id);

        $request->validate([
            'fk_tipo' => 'required|exists:cwtipovehiculo,id',
            'modelo' => 'required|string|max:50',
            'marca' => 'required|string|max:50',
            'identificacion' => 'required|string|max:50|unique:cwvehiculo,identificacion,' . $id,
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'serialmotor' => 'nullable|string|max:100',
            'serialchasis' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:200'
        ]);

        $vehiculo->update($request->all());

        return redirect()->route('vehiculos.index', ['vehiculo_id' => $vehiculo->id])
            ->with('success', 'Vehículo actualizado exitosamente');
    }

    public function destroy($id)
    {
        $vehiculo = CWVehiculo::findOrFail($id);

        // Verificar si tiene mantenimientos
        if ($vehiculo->mantenimientos()->count() > 0) {
            return redirect()->route('vehiculos.index')
                ->with('error', 'No se puede eliminar el vehículo porque tiene mantenimientos asociados');
        }

        $vehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo eliminado exitosamente');
    }

    public function getDetalles($id)
    {
        $vehiculo = CWVehiculo::with(['cliente', 'tipo', 'mantenimientos' => function($q) {
            $q->orderBy('fecha_mantenimiento', 'desc')->limit(5);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'vehiculo' => $vehiculo
        ]);
    }
}
