<?php
// app/Http/Controllers/StageController.php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Schedule;
use App\Models\Result;
use App\Models\Athlete;
use Illuminate\Http\Request;

class StageController extends Controller
{
    // Listar etapas
    public function index()
    {
        $stages = Stage::orderBy('stage_number')->get();
        return view('admin.stages.index', compact('stages'));
    }

    // Ver detalles de una etapa
    public function show($id)
    {
        $stage = Stage::with(['schedules', 'results.athlete.team'])->findOrFail($id);

        // Resultados por categoría
        $resultsByCategory = [];
        foreach ($stage->results as $result) {
            $category = $result->athlete->category;
            if (!isset($resultsByCategory[$category])) {
                $resultsByCategory[$category] = [];
            }
            $resultsByCategory[$category][] = $result;
        }

        // Ordenar resultados por posición
        foreach ($resultsByCategory as $category => $results) {
            usort($results, function($a, $b) {
                return $a->position <=> $b->position;
            });
            $resultsByCategory[$category] = $results;
        }

        return view('admin.stages.show', compact('stage', 'resultsByCategory'));
    }

    // Formulario para crear etapa
    public function create()
    {
        return view('admin.stages.create');
    }

    // Guardar nueva etapa
    public function store(Request $request)
    {
        $request->validate([
            'stage_number' => 'required|integer|unique:stages',
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_location' => 'required|string|max:255',
            'finish_location' => 'required|string|max:255',
            'total_distance' => 'required|numeric|min:0',
            'route_details' => 'nullable|string',
            'sprints' => 'nullable|array',
            'mountain_prizes' => 'nullable|array'
        ]);

        $stage = Stage::create($request->all());

        return redirect()->route('admin.stages.show', $stage->id)
            ->with('success', 'Etapa creada exitosamente');
    }

    // Actualizar etapa
    public function update(Request $request, $id)
    {
        $stage = Stage::findOrFail($id);

        $request->validate([
            'stage_number' => 'required|integer|unique:stages,stage_number,' . $id,
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_location' => 'required|string|max:255',
            'finish_location' => 'required|string|max:255',
            'total_distance' => 'required|numeric|min:0',
            'route_details' => 'nullable|string'
        ]);

        $stage->update($request->all());

        return redirect()->route('admin.stages.show', $stage->id)
            ->with('success', 'Etapa actualizada exitosamente');
    }

    // Eliminar etapa
    public function destroy($id)
    {
        $stage = Stage::findOrFail($id);
        $stage->delete();

        return redirect()->route('admin.stages.index')
            ->with('success', 'Etapa eliminada exitosamente');
    }

    // Gestionar horarios de la etapa
    public function schedules($stageId)
    {
        $stage = Stage::findOrFail($stageId);
        $schedules = Schedule::where('stage_id', $stageId)->get();
        $categories = $this->getCategories();

        return view('admin.stages.schedules', compact('stage', 'schedules', 'categories'));
    }

    // Agregar horario
    public function addSchedule(Request $request, $stageId)
    {
        $request->validate([
            'category' => 'required|string',
            'start_time' => 'required',
            'distance' => 'required|numeric|min:0',
            'laps' => 'nullable|integer',
            'meeting_time' => 'nullable',
            'notes' => 'nullable|string'
        ]);

        Schedule::create([
            'stage_id' => $stageId,
            'category' => $request->category,
            'start_time' => $request->start_time,
            'meeting_time' => $request->meeting_time,
            'distance' => $request->distance,
            'laps' => $request->laps,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Horario agregado exitosamente');
    }

    // Eliminar horario
    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->delete();

        return redirect()->back()->with('success', 'Horario eliminado');
    }

    // Ingresar resultados
    public function enterResults($stageId)
    {
        $stage = Stage::findOrFail($stageId);
        $athletes = Athlete::with('team')->orderBy('dorsal_number')->get();

        // Resultados existentes
        $existingResults = Result::where('stage_id', $stageId)
            ->pluck('athlete_id')
            ->toArray();

        return view('admin.stages.results', compact('stage', 'athletes', 'existingResults'));
    }

    // Guardar resultados
    public function saveResults(Request $request, $stageId)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.athlete_id' => 'required|exists:athletes,id',
            'results.*.position' => 'nullable|integer',
            'results.*.finish_time' => 'nullable',
            'results.*.average_speed' => 'nullable|numeric',
            'results.*.points' => 'nullable|integer',
            'results.*.sprint_points' => 'nullable|integer',
            'results.*.mountain_points' => 'nullable|integer'
        ]);

        foreach ($request->results as $resultData) {
            Result::updateOrCreate(
                [
                    'athlete_id' => $resultData['athlete_id'],
                    'stage_id' => $stageId
                ],
                [
                    'position' => $resultData['position'] ?? null,
                    'finish_time' => $resultData['finish_time'] ?? null,
                    'average_speed' => $resultData['average_speed'] ?? null,
                    'points' => $resultData['points'] ?? 0,
                    'sprint_points' => $resultData['sprint_points'] ?? 0,
                    'mountain_points' => $resultData['mountain_points'] ?? 0
                ]
            );
        }

        return redirect()->route('admin.stages.show', $stageId)
            ->with('success', 'Resultados guardados exitosamente');
    }

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
}
