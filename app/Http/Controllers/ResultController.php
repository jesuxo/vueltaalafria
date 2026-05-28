<?php
// app/Http/Controllers/ResultController.php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Athlete;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    // Tabla de posiciones por categoría
    public function standings(Request $request)
    {
        $category = $request->get('category', 'Juvenil Masculino');
        $stageId = $request->get('stage_id');

        $query = Result::with(['athlete.team', 'stage'])
            ->whereHas('athlete', function($q) use ($category) {
                $q->where('category', $category);
            });

        if ($stageId) {
            $query->where('stage_id', $stageId);
        }

        $results = $query->orderBy('position')
            ->orderBy('finish_time')
            ->paginate(50);

        $categories = $this->getCategories();
        $stages = Stage::orderBy('stage_number')->get();

        return view('admin.results.standings', compact('results', 'categories', 'stages', 'category', 'stageId'));
    }

    // Exportar resultados a Excel/CSV
    public function export(Request $request)
    {
        $stageId = $request->get('stage_id');
        $category = $request->get('category');

        $query = Result::with(['athlete.team', 'stage'])
            ->orderBy('position');

        if ($stageId) {
            $query->where('stage_id', $stageId);
        }

        if ($category) {
            $query->whereHas('athlete', function($q) use ($category) {
                $q->where('category', $category);
            });
        }

        $results = $query->get();

        $filename = 'resultados_vuelta_fria_' . date('Ymd_His') . '.csv';

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Dorsal', 'Nombre', 'Equipo', 'Etapa', 'Posición', 'Tiempo', 'Velocidad Media', 'Puntos', 'Puntos Sprint', 'Puntos Montaña']);

            foreach ($results as $result) {
                fputcsv($file, [
                    $result->athlete->dorsal_number,
                    $result->athlete->full_name,
                    $result->athlete->team->name ?? 'Individual',
                    $result->stage->full_name,
                    $result->position,
                    $result->finish_time,
                    $result->average_speed,
                    $result->points,
                    $result->sprint_points,
                    $result->mountain_points
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    // Clasificación por puntos
    public function pointsClassification(Request $request)
    {
        $category = $request->get('category', 'Juvenil Masculino');

        $classification = Athlete::select(
            'athletes.id',
            'athletes.first_name',
            'athletes.last_name',
            'athletes.dorsal_number',
            'teams.name as team_name',
            DB::raw('COALESCE(SUM(results.points), 0) as total_points')
        )
            ->leftJoin('teams', 'athletes.team_id', '=', 'teams.id')
            ->leftJoin('results', 'athletes.id', '=', 'results.athlete_id')
            ->where('athletes.category', $category)
            ->groupBy('athletes.id', 'athletes.first_name', 'athletes.last_name',
                'athletes.dorsal_number', 'teams.name')
            ->orderBy('total_points', 'desc')
            ->paginate(50);

        $categories = $this->getCategories();

        return view('admin.results.points', compact('classification', 'categories', 'category'));
    }

    // Clasificación de Sprint
    public function sprintClassification(Request $request)
    {
        $category = $request->get('category', 'Juvenil Masculino');

        $classification = Athlete::select(
            'athletes.id',
            'athletes.first_name',
            'athletes.last_name',
            'athletes.dorsal_number',
            'teams.name as team_name',
            DB::raw('COALESCE(SUM(results.sprint_points), 0) as total_sprint_points')
        )
            ->leftJoin('teams', 'athletes.team_id', '=', 'teams.id')
            ->leftJoin('results', 'athletes.id', '=', 'results.athlete_id')
            ->where('athletes.category', $category)
            ->groupBy('athletes.id', 'athletes.first_name', 'athletes.last_name',
                'athletes.dorsal_number', 'teams.name')
            ->orderBy('total_sprint_points', 'desc')
            ->paginate(50);

        $categories = $this->getCategories();

        return view('admin.results.sprint', compact('classification', 'categories', 'category'));
    }

    // Clasificación de Montaña
    public function mountainClassification(Request $request)
    {
        $category = $request->get('category', 'Juvenil Masculino');

        $classification = Athlete::select(
            'athletes.id',
            'athletes.first_name',
            'athletes.last_name',
            'athletes.dorsal_number',
            'teams.name as team_name',
            DB::raw('COALESCE(SUM(results.mountain_points), 0) as total_mountain_points')
        )
            ->leftJoin('teams', 'athletes.team_id', '=', 'teams.id')
            ->leftJoin('results', 'athletes.id', '=', 'results.athlete_id')
            ->where('athletes.category', $category)
            ->groupBy('athletes.id', 'athletes.first_name', 'athletes.last_name',
                'athletes.dorsal_number', 'teams.name')
            ->orderBy('total_mountain_points', 'desc')
            ->paginate(50);

        $categories = $this->getCategories();

        return view('admin.results.mountain', compact('classification', 'categories', 'category'));
    }

    // Clasificación por equipos
    public function teamClassification(Request $request)
    {
        $classification = DB::table('teams')
            ->select(
                'teams.id',
                'teams.name',
                'teams.logo',
                DB::raw('COUNT(DISTINCT athletes.id) as athletes_count'),
                DB::raw('COALESCE(SUM(results.points), 0) as total_points')
            )
            ->leftJoin('athletes', 'teams.id', '=', 'athletes.team_id')
            ->leftJoin('results', 'athletes.id', '=', 'results.athlete_id')
            ->groupBy('teams.id', 'teams.name', 'teams.logo')
            ->orderBy('total_points', 'desc')
            ->paginate(30);

        return view('admin.results.team', compact('classification'));
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
