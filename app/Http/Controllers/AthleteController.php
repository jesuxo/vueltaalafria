<?php
// app/Http/Controllers/AthleteController.php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Result;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AthleteController extends Controller
{
    // Listar atletas con filtros
    public function index(Request $request)
    {
        $query = Athlete::with('team');

        if ($request->has('team_id') && $request->team_id) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('dorsal_number', 'like', '%' . $request->search . '%');
            });
        }

        $athletes = $query->orderBy('dorsal_number')->paginate(30);
        $teams = Team::orderBy('name')->get();
        $categories = $this->getCategories();

        return view('admin.athletes.index', compact('athletes', 'teams', 'categories'));
    }

    // Ver detalles de un atleta
    public function show($id)
    {
        $athlete = Athlete::with(['team', 'results.stage'])->findOrFail($id);

        // Calcular estadísticas del atleta
        $stats = [
            'total_races' => $athlete->results->count(),
            'best_position' => $athlete->results->min('position'),
            'total_points' => $athlete->results->sum('points'),
            'sprint_points' => $athlete->results->sum('sprint_points'),
            'mountain_points' => $athlete->results->sum('mountain_points'),
            'average_speed' => $athlete->results->avg('average_speed')
        ];

        return view('admin.athletes.show', compact('athlete', 'stats'));
    }

    // Formulario para crear atleta individual
    public function create()
    {
        $teams = Team::where('is_active', true)->orderBy('name')->get();
        $categories = $this->getCategories();
        $genders = ['Masculino', 'Femenino'];

        return view('admin.athletes.create', compact('teams', 'categories', 'genders'));
    }

    // Guardar nuevo atleta
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'gender' => 'required|in:Masculino,Femenino',
            'category' => 'required|in:' . implode(',', $this->getCategories()),
            'birth_date' => 'required|date|before:today',
            'nationality' => 'nullable|string|max:255'
        ]);

        // Generar dorsal automático
        $dorsalNumber = $this->generateDorsalNumber();

        $athlete = Athlete::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'dorsal_number' => $dorsalNumber,
            'team_id' => $request->team_id,
            'gender' => $request->gender,
            'category' => $request->category,
            'birth_date' => $request->birth_date,
            'nationality' => $request->nationality ?? 'Venezolana',
            'is_active' => true
        ]);

        return redirect()->route('admin.athletes.show', $athlete->id)
            ->with('success', 'Atleta creado exitosamente. Dorsal: ' . $dorsalNumber);
    }

    // Actualizar atleta
    public function update(Request $request, $id)
    {
        $athlete = Athlete::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'gender' => 'required|in:Masculino,Femenino',
            'category' => 'required|in:' . implode(',', $this->getCategories()),
            'birth_date' => 'required|date|before:today',
            'nationality' => 'nullable|string|max:255'
        ]);

        $athlete->update($request->all());

        return redirect()->route('admin.athletes.show', $athlete->id)
            ->with('success', 'Atleta actualizado exitosamente');
    }

    // Eliminar atleta
    public function destroy($id)
    {
        $athlete = Athlete::findOrFail($id);
        $athlete->delete();

        return redirect()->route('admin.athletes.index')
            ->with('success', 'Atleta eliminado exitosamente');
    }

    // Clasificación general
    public function generalClassification(Request $request)
    {
        $stageId = $request->get('stage_id');

        $query = Athlete::select(
            'athletes.id',
            'athletes.first_name',
            'athletes.last_name',
            'athletes.dorsal_number',
            'athletes.category',
            'teams.name as team_name',
            DB::raw('COALESCE(SUM(results.points), 0) as total_points'),
            DB::raw('COALESCE(SUM(results.sprint_points), 0) as total_sprint_points'),
            DB::raw('COALESCE(SUM(results.mountain_points), 0) as total_mountain_points'),
            DB::raw('COALESCE(AVG(results.average_speed), 0) as avg_speed')
        )
            ->leftJoin('teams', 'athletes.team_id', '=', 'teams.id')
            ->leftJoin('results', 'athletes.id', '=', 'results.athlete_id');

        if ($stageId) {
            $query->where('results.stage_id', $stageId);
        }

        $classifications = $query->groupBy('athletes.id', 'athletes.first_name', 'athletes.last_name',
            'athletes.dorsal_number', 'athletes.category', 'teams.name')
            ->orderBy('total_points', 'desc')
            ->paginate(50);

        $stages = Stage::orderBy('stage_number')->get();

        return view('admin.athletes.classification', compact('classifications', 'stages', 'stageId'));
    }

    // Métodos privados
    private function getCategories()
    {
        return [
            'Pre-Infantil Masculino',
            'Pre-Infantil Femenino',
            'Infantil Masculino',
            'Infantil Femenino',
            'Pre-Juvenil Masculino',
            'Pre-Juvenil Femenino',
            'Juvenil Masculino',
            'Juvenil Femenino'
        ];
    }

    private function generateDorsalNumber()
    {
        $lastAthlete = Athlete::orderBy('id', 'desc')->first();
        $number = $lastAthlete ? intval(substr($lastAthlete->dorsal_number, 1)) + 1 : 1;
        return 'D' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
