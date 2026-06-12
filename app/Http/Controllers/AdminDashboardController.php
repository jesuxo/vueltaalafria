<?php
// app/Http/Controllers/AdminDashboardController.php

namespace App\Http\Controllers;

use App\Models\PhotoOrder;
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

    public function pendingCount()
    {
        $count = Registration::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
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
    public function photoOrders()
    {
        $orders = PhotoOrder::with(['items.photo', 'items.photo.stage'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('photos.orders', compact('orders'));
    }

    /**
     * Ver detalle de un pedido de fotos
     */
    public function showPhotoOrder($id)
    {
        $order = PhotoOrder::with(['items.photo', 'items.photo.stage'])
            ->findOrFail($id);

        // Si es una petición AJAX, devolver solo la vista parcial
        if (request()->ajax()) {
            return view('photos.order-detail', compact('order'));
        }

        return view('photos.order-detail-full', compact('order'));
    }

    /**
     * Actualizar estado de un pedido de fotos
     */
    public function updatePhotoOrderStatus(Request $request, $id)
    {
        $order = PhotoOrder::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,cancelled'
        ]);

        $order->status = $request->status;

        if ($request->status == 'paid' && !$order->paid_at) {
            $order->paid_at = now();
        }

        if ($request->status == 'completed' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
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
