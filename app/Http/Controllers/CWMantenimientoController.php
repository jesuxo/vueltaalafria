<?php

// app/Http/Controllers/CWMantenimientoController.php
namespace App\Http\Controllers;

use App\Models\CWMantenimiento;
use App\Models\Cwmantenimientoproducto;
use App\Models\CWTipoVehiculo;
use App\Models\CWVehiculo;
use App\Models\Saclie;
use App\Models\Savend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CWMantenimientoController extends Controller
{
    public function index(Request $request, $codclie = null, $vehiculo_id = null)
    {
        if(Auth::user() && Auth::user()->type == 'cliente') {
            $codclie = Auth::user()->codclie;
        }

        $query = CWMantenimiento::with(['vehiculo', 'cliente'])
            ->where('codclie', $codclie);

        if ($vehiculo_id) {
            $query->where('fk_vehiculo', $vehiculo_id);
        }

        $mantenimientos = $query->orderBy('fecha_mantenimiento', 'desc')->get();
        $vehiculo = $vehiculo_id ? CWVehiculo::find($vehiculo_id) : null;
        $cliente = Saclie::where('codclie', $codclie)->first();

        return view('mantenimientos', compact('mantenimientos', 'cliente', 'vehiculo'));
    }

    public function create($codclie, $vehiculo_id)
    {
        $vehiculo = CWVehiculo::with('cliente')->findOrFail($vehiculo_id);

        if ($vehiculo->codclie != $codclie) {
            return redirect()->back()->with('error', 'Vehículo no pertenece al cliente');
        }

        $tipos_mantenimiento = [
            'cambio_aceite' => 'Cambio de Aceite',
            'cambio_filtro_aceite' => 'Cambio Filtro de Aceite',
            'cambio_filtro_gasolina' => 'Cambio Filtro de Gasolina',
            'cambio_filtro_aire' => 'Cambio Filtro de Aire',
            'mantenimiento_inyectores' => 'Mantenimiento de Inyectores',
            'bateria' => 'Batería',
            'otros' => 'Otros'
        ];

        return view('mantenimientosform', compact('vehiculo', 'tipos_mantenimiento'));
    }

    public function store(Request $request, $codclie, $vehiculo_id)
    {
        $request->validate([
            'fecha_mantenimiento' => 'required|date',
            'tipo_mantenimiento' => 'required|in:cambio_aceite,cambio_filtro_aceite,cambio_filtro_gasolina,cambio_filtro_aire,mantenimiento_inyectores,bateria,otros',
            'kilometraje' => 'nullable|integer',
            'producto_utilizado' => 'nullable|string|max:100',
            'costo' => 'nullable|numeric',
        ]);

        $vehiculo = CWVehiculo::where('id', $vehiculo_id)
            ->where('codclie', $codclie)
            ->firstOrFail();

        $data = $request->all();
        $data['codclie'] = $codclie;
        $data['fk_vehiculo'] = $vehiculo_id;
        $data['realizado_por'] = Auth::id();

        // Calcular próximo mantenimiento según tipo
        if ($request->tipo_mantenimiento == 'cambio_aceite') {
            $data['proximo_mantenimiento'] = date('Y-m-d', strtotime($request->fecha_mantenimiento . ' + 6 months'));
            $data['proximo_kilometraje'] = ($request->kilometraje ?? 0) + 5000;
        }

        CWMantenimiento::create($data);

        return redirect()->route('clientes.vehiculos.mantenimientos', [$codclie, $vehiculo_id])
            ->with('success', 'Mantenimiento registrado exitosamente');
    }

    public function show($codclie, $vehiculo_id, $id)
    {
        $mantenimiento = CWMantenimiento::with([
            'vehiculo',
            'vehiculo.cliente',
            'vehiculo.tipo',
            'usuario',
            'vendedor',
            'productos'
        ])
            ->where('id', $id)
            ->where('codclie', $codclie)
            ->where('fk_vehiculo', $vehiculo_id)
            ->firstOrFail();

        return view('mantenimientosshow', compact('mantenimiento'));
    }

    public function edit($codclie, $vehiculo_id, $id)
    {
        $mantenimiento = CWMantenimiento::with([
            'vehiculo',
            'vehiculo.cliente',
            'vehiculo.tipo',
            'productos',
            'vendedor'
        ])
            ->where('id', $id)
            ->where('codclie', $codclie)
            ->where('fk_vehiculo', $vehiculo_id)
            ->firstOrFail();

        $vehiculo = $mantenimiento->vehiculo;
        $tipos_mantenimiento = [
            'cambio_aceite' => 'Cambio de Aceite',
            'cambio_filtro_aceite' => 'Cambio Filtro de Aceite',
            'cambio_filtro_gasolina' => 'Cambio Filtro de Gasolina',
            'cambio_filtro_aire' => 'Cambio Filtro de Aire',
            'mantenimiento_inyectores' => 'Mantenimiento de Inyectores',
            'bateria' => 'Batería',
            'otros' => 'Otros'
        ];

        $tipos_vehiculo = CWTipoVehiculo::orderBy('tipo')->get();
        $vendedores = Savend::where('activo', 1)->orderBy('descrip')->get();

        return view('mantenimientosedit', compact(
            'mantenimiento',
            'vehiculo',
            'tipos_mantenimiento',
            'tipos_vehiculo',
            'vendedores'
        ));
    }

    public function comprobante($id)
    {
        $mantenimiento = CWMantenimiento::with(['vehiculo', 'vehiculo.cliente', 'usuario'])
            ->findOrFail($id);

        return view('mantenimientoscomprobante', compact('mantenimiento'));
    }


    public function update(Request $request, $codclie, $vehiculo_id, $id)
    {
        $request->validate([
            'fecha_mantenimiento' => 'required|date',
            'hora_mantenimiento'  => 'nullable|string',
            'tipo_mantenimiento'  => 'nullable|string', // Ya no es tan relevante
            'tipos_mantenimiento' => 'required|array|min:1', // NUEVO: validar array
            'tipos_mantenimiento.*' => 'required|string',
            'kilometraje'         => 'nullable|integer',
            'observaciones'       => 'nullable|string',
            'codvend'             => 'nullable|string|max:50',
            'productos'           => 'nullable|array'
        ]);

        $mantenimiento = CWMantenimiento::where('id', $id)
            ->where('codclie', $codclie)
            ->where('fk_vehiculo', $vehiculo_id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // Actualizar datos del mantenimiento
            $mantenimiento->update([
                'fecha_mantenimiento'   => $request->fecha_mantenimiento,
                'hora_mantenimiento'    => $request->hora_mantenimiento,
                'tipo_mantenimiento'    => 'multiple', // Guardamos como 'multiple' si hay varios tipos
                'kilometraje'           => $request->kilometraje,
                'observaciones'         => $request->observaciones,
                'proximo_mantenimiento' => $request->proximo_mantenimiento,
                'proximo_kilometraje'   => $request->proximo_kilometraje,
                'codvend'               => $request->codvend
            ]);

            // ============ NUEVO: ACTUALIZAR TIPOS DE MANTENIMIENTO ============
            // Eliminar tipos anteriores
            $mantenimiento->tipos()->delete();

            // Guardar los nuevos tipos
            $tiposMantenimiento = $request->tipos_mantenimiento;
            foreach ($tiposMantenimiento as $tipo) {
                $descripcion = null;
                if ($tipo === 'otros' && $request->has('otro_tipo_descripcion')) {
                    $descripcion = $request->otro_tipo_descripcion;
                }

                \App\Models\CwMantenimientoTipo::create([
                    'mantenimiento_id' => $mantenimiento->id,
                    'tipo' => $tipo,
                    'descripcion' => $descripcion
                ]);
            }
            // ============ FIN NUEVO ============

            // Manejar productos eliminados
            if ($request->has('productos_eliminados')) {
                \App\Models\Cwmantenimientoproducto::whereIn('id', $request->productos_eliminados)->delete();
            }

            // Actualizar productos existentes
            if ($request->has('productos_existentes')) {
                foreach ($request->productos_existentes as $prodId => $prodData) {
                    \App\Models\Cwmantenimientoproducto::where('id', $prodId)->update([
                        'cantidad' => $prodData['cantidad'],
                    ]);
                }
            }

            // Agregar nuevos productos
            if ($request->has('productos')) {
                foreach ($request->productos as $p) {
                    // Verificar si es un producto temporal (nuevo)
                    if (isset($p['tempId']) && strpos($p['tempId'], 'temp_') === 0) {
                        \App\Models\Cwmantenimientoproducto::create([
                            'mantenimiento_id' => $mantenimiento->id,
                            'codprod'          => $p['codprod'] ?? null,
                            'descripcion'      => $p['descripcion'],
                            'referencia'       => $p['referencia'] ?? null,
                            'cantidad'         => $p['cantidad'] ?? 1,
                            'tipo'             => $p['tipo'] ?? 'producto'
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('clientes.vehiculos.mantenimientos.show', [$codclie, $vehiculo_id, $id])
                ->with('success', 'Mantenimiento actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($codclie, $vehiculo_id, $id)
    {
        $mantenimiento = CWMantenimiento::where('id', $id)
            ->where('codclie', $codclie)
            ->where('fk_vehiculo', $vehiculo_id)
            ->firstOrFail();

        $mantenimiento->delete();

        return redirect()->route('clientes.vehiculos.mantenimientos', [$codclie, $vehiculo_id])
            ->with('success', 'Mantenimiento eliminado');
    }
}
