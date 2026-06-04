<?php
// app/Http/Controllers/AdminDashboardController.php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Team;
use App\Models\Athlete;
use App\Models\TeamPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $pendingRegistrations = Registration::where('status', 'pending')->count();
        $approvedRegistrations = Registration::where('status', 'approved')->count();
        $totalTeams = Team::count();
        $totalAthletes = Athlete::count();
        $recentRegistrations = Registration::with(['athlete', 'team'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('index', compact(
            'pendingRegistrations',
            'approvedRegistrations',
            'totalTeams',
            'totalAthletes',
            'recentRegistrations'
        ));
    }

    public function registrations(Request $request)
    {
        $query = Registration::with(['athlete', 'team']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->type) {
            $query->where('registration_type', $request->type);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")
                    ->orWhereHas('athlete', function($sq) use ($request) {
                        $sq->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%");
                    })
                    ->orWhereHas('team', function($sq) use ($request) {
                        $sq->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('registrations.index', compact('registrations'));
    }

    public function showRegistration($id)
    {
        $registration = Registration::with(['athlete', 'team'])->findOrFail($id);
        return response()->json($registration);
    }

    public function updateRegistrationStatus($id, Request $request)
    {
        try {
            $registration = Registration::findOrFail($id);
            $registration->status = $request->status;
            $registration->save();

            return response()->json(['success' => true, 'message' => 'Estado actualizado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteRegistration($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            if ($registration->payment_proof && file_exists(public_path($registration->payment_proof))) {
                unlink(public_path($registration->payment_proof));
            }
            $registration->delete();

            return response()->json(['success' => true, 'message' => 'Inscripción eliminada']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportRegistrations()
    {
        $registrations = Registration::with(['athlete', 'team'])->get();

        $filename = 'inscripciones_' . date('Y-m-d') . '.csv';

        $callback = function() use ($registrations) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Tipo', 'Nombre', 'Email', 'Teléfono', 'Monto', 'Estado', 'Fecha Registro']);

            foreach ($registrations as $reg) {
                $nombre = $reg->registration_type == 'individual' && $reg->athlete
                    ? $reg->athlete->first_name . ' ' . $reg->athlete->last_name
                    : ($reg->team ? $reg->team->name : 'N/A');

                fputcsv($file, [
                    $reg->id,
                    $reg->registration_type,
                    $nombre,
                    $reg->email,
                    $reg->phone,
                    $reg->amount,
                    $reg->status,
                    $reg->registered_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
