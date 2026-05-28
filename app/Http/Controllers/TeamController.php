<?php
// app/Http/Controllers/TeamController.php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Athlete;
use App\Models\TeamStaff;
use App\Models\TeamVehicle;
use App\Models\TeamPhoto;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    // Listar todos los equipos (Admin)
    public function index(Request $request)
    {
        $query = Team::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('city', 'like', '%' . $request->search . '%')
                ->orWhere('country', 'like', '%' . $request->search . '%');
        }

        $teams = $query->orderBy('name')->paginate(20);

        return view('admin.teams.index', compact('teams'));
    }

    // Ver detalles de un equipo (Admin)
    public function show($id)
    {
        $team = Team::with(['athletes', 'staff', 'vehicles', 'photos', 'registrations'])->findOrFail($id);

        $stats = [
            'athletes_count' => $team->athletes->count(),
            'staff_count' => $team->staff->count(),
            'vehicles_count' => $team->vehicles->count(),
            'photos_count' => $team->photos->count(),
        ];

        return view('admin.teams.show', compact('team', 'stats'));
    }

    // Formulario para crear equipo
    public function create()
    {
        return view('admin.teams.create');
    }

    // Guardar nuevo equipo
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'city' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('logo');
        $data['access_code'] = Str::upper(Str::random(8));
        $data['is_active'] = true;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team-logos', 'public');
            $data['logo'] = $path;
        }

        $team = Team::create($data);

        return redirect()->route('admin.teams.show', $team->id)
            ->with('success', 'Equipo creado exitosamente. Código de acceso: ' . $team->access_code);
    }

    // Actualizar equipo
    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $id,
            'city' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
            $path = $request->file('logo')->store('team-logos', 'public');
            $data['logo'] = $path;
        }

        $team->update($data);

        return redirect()->route('admin.teams.show', $team->id)
            ->with('success', 'Equipo actualizado exitosamente');
    }

    // Activar/Desactivar equipo
    public function toggleActive($id)
    {
        $team = Team::findOrFail($id);
        $team->is_active = !$team->is_active;
        $team->save();

        return response()->json(['success' => true, 'is_active' => $team->is_active]);
    }

    // Regenerar código de acceso
    public function regenerateAccessCode($id)
    {
        $team = Team::findOrFail($id);
        $team->access_code = Str::upper(Str::random(8));
        $team->save();

        return redirect()->back()->with('success', 'Nuevo código de acceso: ' . $team->access_code);
    }
}
