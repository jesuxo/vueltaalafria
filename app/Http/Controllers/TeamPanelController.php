<?php
// app/Http/Controllers/TeamPanelController.php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamStaff;
use App\Models\TeamVehicle;
use App\Models\TeamPhoto;
use App\Models\Athlete;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AthletesImport;

class TeamPanelController extends Controller
{
    public function loginForm()
    {
        return view('team.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'access_code' => 'required'
        ]);

        $team = Team::where('access_code', $request->access_code)->first();

        if ($team) {
            session(['team_id' => $team->id, 'team_name' => $team->name]);
            return redirect()->route('team.dashboard');
        }

        return back()->withErrors(['access_code' => 'Código de acceso inválido']);
    }

    public function logout()
    {
        session()->forget(['team_id', 'team_name']);
        return redirect()->route('team.login');
    }

    public function dashboard()
    {
        $team = Team::findOrFail(session('team_id'));
        $athletes = Athlete::where('team_id', $team->id)->get();
        $staff = TeamStaff::where('team_id', $team->id)->get();
        $vehicles = TeamVehicle::where('team_id', $team->id)->get();
        $photos = TeamPhoto::where('team_id', $team->id)->orderBy('created_at', 'desc')->get();
        $registration = Registration::where('team_id', $team->id)->first();

        return view('team.dashboard', compact('team', 'athletes', 'staff', 'vehicles', 'photos', 'registration'));
    }

    public function staffIndex()
    {
        $staff = TeamStaff::where('team_id', session('team_id'))->get();
        return view('team.staff.index', compact('staff'));
    }

    public function staffStore(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'identification_number' => 'required|unique:team_staff',
            'role' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'position' => 'nullable'
        ]);

        TeamStaff::create([
            'team_id' => session('team_id'),
            'full_name' => $request->full_name,
            'identification_number' => $request->identification_number,
            'role' => $request->role,
            'phone' => $request->phone,
            'email' => $request->email,
            'position' => $request->position
        ]);

        return redirect()->route('team.staff')->with('success', 'Personal agregado exitosamente');
    }

    public function staffDestroy($id)
    {
        $staff = TeamStaff::where('team_id', session('team_id'))->findOrFail($id);
        $staff->delete();
        return redirect()->route('team.staff')->with('success', 'Personal eliminado');
    }

    public function vehiclesIndex()
    {
        $vehicles = TeamVehicle::where('team_id', session('team_id'))->get();
        return view('team.vehicles.index', compact('vehicles'));
    }

    public function vehiclesStore(Request $request)
    {
        $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'plate' => 'required',
            'capacity' => 'required|integer',
            'type' => 'required',
            'country_origin' => 'required'
        ]);

        TeamVehicle::create([
            'team_id' => session('team_id'),
            'brand' => $request->brand,
            'model' => $request->model,
            'plate' => $request->plate,
            'color' => $request->color,
            'capacity' => $request->capacity,
            'type' => $request->type,
            'country_origin' => $request->country_origin,
            'driver_name' => $request->driver_name,
            'driver_phone' => $request->driver_phone,
            'notes' => $request->notes
        ]);

        return redirect()->route('team.vehicles')->with('success', 'Vehículo agregado exitosamente');
    }

    public function vehiclesDestroy($id)
    {
        $vehicle = TeamVehicle::where('team_id', session('team_id'))->findOrFail($id);
        $vehicle->delete();
        return redirect()->route('team.vehicles')->with('success', 'Vehículo eliminado');
    }

    public function photosIndex()
    {
        $photos = TeamPhoto::where('team_id', session('team_id'))->orderBy('created_at', 'desc')->get();
        return view('team.photos.index', compact('photos'));
    }

    public function photosStore(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'stage_number' => 'nullable|integer'
        ]);

        $path = $request->file('photo')->store('team-photos', 'public');

        TeamPhoto::create([
            'team_id' => session('team_id'),
            'photo_path' => $path,
            'title' => $request->title,
            'description' => $request->description,
            'stage_number' => $request->stage_number,
            'is_approved' => false // Requiere aprobación del admin
        ]);

        return redirect()->route('team.photos')->with('success', 'Foto subida exitosamente. Esperando aprobación.');
    }

    public function photosDestroy($id)
    {
        $photo = TeamPhoto::where('team_id', session('team_id'))->findOrFail($id);
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();
        return redirect()->route('team.photos')->with('success', 'Foto eliminada');
    }

    public function registrationForm()
    {
        $team = Team::findOrFail(session('team_id'));
        $athletes = Athlete::where('team_id', $team->id)->get();
        $existingRegistration = Registration::where('team_id', $team->id)->first();

        if ($existingRegistration && $existingRegistration->status != 'pending') {
            return redirect()->route('team.dashboard')->with('error', 'Ya has completado el proceso de inscripción');
        }

        return view('team.registration', compact('team', 'athletes', 'existingRegistration'));
    }

    public function registrationSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required'
        ]);

        $team = Team::findOrFail(session('team_id'));
        $athletes = Athlete::where('team_id', $team->id)->get();

        if ($athletes->count() == 0) {
            return back()->with('error', 'Debes agregar al menos un atleta antes de inscribir al equipo');
        }

        Registration::updateOrCreate(
            ['team_id' => $team->id],
            [
                'registration_type' => 'team',
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'pending',
                'registered_at' => now()
            ]
        );

        return redirect()->route('team.dashboard')->with('success', 'Inscripción enviada exitosamente. Espera la confirmación.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'gender',
            'category',
            'birth_date',
            'nationality'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_atletas.csv"',
        ]);
    }

    public function importAthletes(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,csv,xls'
        ]);

        $import = new AthletesImport(session('team_id'));
        Excel::import($import, $request->file('excel_file'));

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return back()->with('import_errors', $errors);
        }

        return redirect()->route('team.dashboard')->with('success', 'Atletas importados exitosamente');
    }
}
