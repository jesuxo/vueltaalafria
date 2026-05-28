<?php
// app/Http/Controllers/AdminDashboardController.php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Athlete;
use App\Models\Stage;
use App\Models\Registration;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_teams' => Team::count(),
            'total_athletes' => Athlete::count(),
            'total_stages' => Stage::count(),
            'total_registrations' => Registration::count(),
            'pending_registrations' => Registration::where('status', 'pending')->count(),
            'completed_athletes' => Athlete::where('is_active', true)->count(),
        ];

        // Inscripciones por mes
        $registrationsByMonth = Registration::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        // Atletas por categoría
        $athletesByCategory = Athlete::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();

        // Equipos por país
        $teamsByCountry = Team::select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get();

        // Últimas inscripciones
        $recentRegistrations = Registration::with(['team', 'athlete'])
            ->latest()
            ->limit(10)
            ->get();

        // Próxima etapa
        $nextStage = Stage::where('date', '>=', now())
            ->orderBy('date')
            ->first();

        return view('admin.dashboard', compact(
            'stats',
            'registrationsByMonth',
            'athletesByCategory',
            'teamsByCountry',
            'recentRegistrations',
            'nextStage'
        ));
    }

    // Aprobar/rechazar inscripciones
    public function approveRegistration($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->status = 'approved';
        $registration->save();

        return redirect()->back()->with('success', 'Inscripción aprobada');
    }

    public function rejectRegistration($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->status = 'rejected';
        $registration->save();

        return redirect()->back()->with('success', 'Inscripción rechazada');
    }

    // Aprobar fotos de equipos
    public function pendingPhotos()
    {
        $photos = \App\Models\TeamPhoto::where('is_approved', false)
            ->with('team')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.photos.pending', compact('photos'));
    }

    public function approvePhoto($id)
    {
        $photo = \App\Models\TeamPhoto::findOrFail($id);
        $photo->is_approved = true;
        $photo->save();

        return response()->json(['success' => true]);
    }

    public function deletePhoto($id)
    {
        $photo = \App\Models\TeamPhoto::findOrFail($id);
        \Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return redirect()->back()->with('success', 'Foto eliminada');
    }
}
