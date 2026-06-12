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

    public function photoRevenueReport(Request $request)
    {
        // Filtros
        $period = $request->get('period', 'month'); // day, week, month, year, custom
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status', 'completed'); // pending, paid, completed, all

        // Construir consulta base
        $query = PhotoOrder::with(['items.photo.stage']);

        // Filtrar por estado
        if ($status !== 'all') {
            $query->where('status', $status);
        } else {
            // Para reporte de ingresos reales, solo pedidos completados o pagados
            $query->whereIn('status', ['completed', 'paid']);
        }

        // Filtrar por fecha
        if ($period === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        // Obtener pedidos
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas generales
        $stats = [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'total_photos_sold' => PhotoOrder::whereIn('status', ['completed', 'paid'])
                ->withCount('items')
                ->get()
                ->sum('items_count'),
            'avg_order_value' => $query->count() > 0 ? $query->sum('total') / $query->count() : 0,
        ];

        // Estadísticas por método de pago
        $paymentMethods = PhotoOrder::whereIn('status', ['completed', 'paid'])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        // Ingresos por mes (últimos 12 meses)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $revenue = PhotoOrder::whereIn('status', ['completed', 'paid'])
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total');

            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'orders' => PhotoOrder::whereIn('status', ['completed', 'paid'])
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count()
            ];
        }

        // Top fotos más vendidas
        $topPhotos = Photo::withCount(['orderItems as sold_count' => function($query) {
            $query->whereHas('order', function($q) {
                $q->whereIn('status', ['completed', 'paid']);
            });
        }])
            ->orderBy('sold_count', 'desc')
            ->limit(10)
            ->get();

        // Ingresos por etapa/evento
        $revenueByStage = PhotoOrder::whereIn('status', ['completed', 'paid'])
            ->with(['items.photo.stage'])
            ->get()
            ->flatMap(function($order) {
                return $order->items;
            })
            ->groupBy(function($item) {
                return $item->photo->stage->name ?? 'Sin categoría';
            })
            ->map(function($items) {
                return [
                    'count' => $items->count(),
                    'revenue' => $items->sum('price')
                ];
            });

        return view('admin.reports.photo-revenue', compact(
            'orders', 'stats', 'paymentMethods', 'monthlyRevenue',
            'topPhotos', 'revenueByStage', 'period', 'status'
        ));
    }

    /**
     * Exportar reporte de ingresos a Excel
     */
    public function exportPhotoRevenue(Request $request)
    {
        // Similar al método anterior pero para exportar
        $query = PhotoOrder::with(['items.photo.stage'])
            ->whereIn('status', ['completed', 'paid']);

        // Aplicar mismos filtros...

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Generar Excel
        return Excel::download(new PhotoRevenueExport($orders), 'reporte_ingresos_fotos.xlsx');
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
