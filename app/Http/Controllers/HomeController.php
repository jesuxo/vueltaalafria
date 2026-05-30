<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Team;
use App\Models\Athlete;
use App\Models\Registration;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Mostrar la página principal
     */
    public function index()
    {
        // Obtener datos para la página de inicio si los necesitas
        $totalTeams = Team::count();
        $totalAthletes = Athlete::count();
        $nextStage = Stage::where('date', '>=', now())->orderBy('date')->first();
        $recentRegistrations = Registration::where('status', 'pending')->count();

        return view('home.home', compact('totalTeams', 'totalAthletes', 'nextStage', 'recentRegistrations'));
    }

    /**
     * Mostrar información de la carrera
     */
    public function info()
    {
        return view('home.info');
    }

    /**
     * Mostrar etapas
     */
    public function stages()
    {
        $stages = Stage::orderBy('stage_number')->get();
        return view('home.stages', compact('stages'));
    }

    /**
     * Mostrar resultados públicos
     */
    public function results()
    {
        $stages = Stage::orderBy('stage_number')->get();
        return view('home.results', compact('stages'));
    }

    /**
     * Mostrar galería de fotos
     */
    public function gallery()
    {
        return view('home.gallery');
    }

    /**
     * Cambiar idioma
     */
    public function lang($locale)
    {
        app()->setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
