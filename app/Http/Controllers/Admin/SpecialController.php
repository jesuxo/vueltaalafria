<?php
// app/Http/Controllers/Admin/SpecialController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use Illuminate\Http\Request;

class SpecialController extends Controller
{
    /**
     * Mostrar lista de eventos especiales
     */
    public function index()
    {
        $specials = Stage::where('type', 'special')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.specials.index', compact('specials'));
    }

    /**
     * Mostrar formulario para crear evento especial
     */
    public function create()
    {
        return view('admin.specials.create');
    }

    /**
     * Guardar nuevo evento especial
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $special = Stage::create([
            'name' => $request->name,
            'type' => 'special',
            'icon' => $request->icon ?? 'fas fa-camera',
            'date' => $request->date,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Evento especial creado correctamente',
                'special' => $special
            ]);
        }

        return redirect()->route('admin.specials.index')
            ->with('success', 'Evento especial creado correctamente');
    }

    /**
     * Mostrar formulario para editar evento especial
     */
    public function edit($id)
    {
        $special = Stage::where('type', 'special')->findOrFail($id);
        return view('admin.specials.edit', compact('special'));
    }

    /**
     * Actualizar evento especial
     */
    public function update(Request $request, $id)
    {
        $special = Stage::where('type', 'special')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $special->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'date' => $request->date,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Evento especial actualizado correctamente',
                'special' => $special
            ]);
        }

        return redirect()->route('admin.specials.index')
            ->with('success', 'Evento especial actualizado correctamente');
    }

    /**
     * Eliminar evento especial
     */
    public function destroy($id)
    {
        $special = Stage::where('type', 'special')->findOrFail($id);

        // Verificar si tiene fotos asociadas
        if ($special->photos()->count() > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar porque tiene fotos asociadas'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'No se puede eliminar porque tiene fotos asociadas');
        }

        $special->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Evento especial eliminado correctamente'
            ]);
        }

        return redirect()->route('admin.specials.index')
            ->with('success', 'Evento especial eliminado correctamente');
    }

    /**
     * Cambiar estado del evento especial (activar/desactivar)
     */
    public function toggleStatus($id)
    {
        $special = Stage::where('type', 'special')->findOrFail($id);
        $special->is_active = !$special->is_active;
        $special->save();

        return response()->json([
            'success' => true,
            'message' => $special->is_active ? 'Evento activado' : 'Evento desactivado',
            'is_active' => $special->is_active
        ]);
    }

    /**
     * Obtener lista de eventos especiales (para selects)
     */
    public function getList()
    {
        $specials = Stage::where('type', 'special')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'icon']);

        return response()->json($specials);
    }
}
