{{-- resources/views/admin/reports/photo-revenue.blade.php --}}
@extends('layouts.master')

@section('title')
    Reporte de Ingresos - Venta de Fotos
@endsection

@section('css')
    <style>
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 40px;
            color: #00ecfe;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .filter-bar {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .revenue-total {
            font-size: 36px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line me-2"></i> Reporte de Ingresos - Venta de Fotos</h3>
                        <p class="text-muted mb-0">Análisis de ingresos generados por la venta de fotos</p>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <div class="filter-bar">
                            <form method="GET" action="{{ route('admin.reports.photo-revenue') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Período</label>
                                    <select name="period" id="period" class="form-select" onchange="toggleCustomDate()">
                                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hoy</option>
                                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Esta semana</option>
                                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Este mes</option>
                                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Este año</option>
                                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="customDates" style="display: {{ $period == 'custom' ? 'flex' : 'none' }}; gap: 10px;">
                                    <div class="flex-grow-1">
                                        <label class="form-label">Desde</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-label">Hasta</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select name="status" class="form-select">
                                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completados</option>
                                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Pagados</option>
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Todos</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-2"></i> Filtrar
                                    </button>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <a href="{{ route('admin.reports.photo-revenue-export', request()->all()) }}" class="btn btn-success w-100">
                                        <i class="fas fa-file-excel me-2"></i> Exportar
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Tarjetas de estadísticas -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-card text-center">
                                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                                    <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
                                    <div class="stat-label">Ingresos Totales</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card text-center">
                                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                                    <div class="stat-value">{{ $stats['total_orders'] }}</div>
                                    <div class="stat-label">Pedidos Realizados</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card text-center">
                                    <div class="stat-icon"><i class="fas fa-camera"></i></div>
                                    <div class="stat-value">{{ $stats['total_photos_sold'] }}</div>
                                    <div class="stat-label">Fotos Vendidas</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card text-center">
                                    <div class="stat-icon"><i class="fas fa-chart-simple"></i></div>
                                    <div class="stat-value">${{ number_format($stats['avg_order_value'], 2) }}</div>
                                    <div class="stat-label">Valor Promedio por Pedido</div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de ingresos mensuales -->
                        <div class="chart-container">
                            <h5><i class="fas fa-chart-bar me-2"></i> Ingresos por Mes</h5>
                            <canvas id="monthlyRevenueChart" style="height: 300px;"></canvas>
                        </div>

                        <div class="row">
                            <!-- Ingresos por método de pago -->
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h5><i class="fas fa-credit-card me-2"></i> Ingresos por Método de Pago</h5>
                                    <canvas id="paymentMethodChart" style="height: 250px;"></canvas>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <thead>
                                            <tr><th>Método</th><th>Pedidos</th><th>Ingresos</th></tr>
                                            </thead>
                                            <tbody>
                                            @foreach($paymentMethods as $method)
                                                <tr>
                                                    <td>
                                                        @if($method->payment_method == 'transferencia') Transferencia
                                                        @elseif($method->payment_method == 'bancolombia') Bancolombia
                                                        @elseif($method->payment_method == 'usdt') USDT
                                                        @else {{ $method->payment_method }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $method->count }}</td>
                                                    <td>${{ number_format($method->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Ingresos por etapa/evento -->
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h5><i class="fas fa-flag-checkered me-2"></i> Ingresos por Etapa/Evento</h5>
                                    <canvas id="stageRevenueChart" style="height: 250px;"></canvas>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <thead><tr><th>Etapa/Evento</th><th>Fotos</th><th>Ingresos</th></tr></thead>
                                            <tbody>
                                            @foreach($revenueByStage as $stageName => $data)
                                                <tr>
                                                    <td>{{ $stageName }}</td>
                                                    <td>{{ $data['count'] }}</td>
                                                    <td>${{ number_format($data['revenue'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top fotos más vendidas -->
                        <div class="chart-container">
                            <h5><i class="fas fa-fire me-2"></i> Top 10 Fotos Más Vendidas</h5>
                            <div class="row">
                                @foreach($topPhotos as $photo)
                                    <div class="col-md-2 col-4 mb-3">
                                        <div class="text-center">
                                            <img src="/{{ $photo->thumbnail_path }}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                            <small class="d-block mt-1">#{{ $photo->id }}</small>
                                            <small class="text-success">Vendidas: {{ $photo->sold_count ?? 0 }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Lista de pedidos -->
                        <div class="chart-container">
                            <h5><i class="fas fa-list me-2"></i> Pedidos Realizados</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Fotos</th>
                                        <th>Total</th>
                                        <th>Método de Pago</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($orders as $order)
                                        <tr>
                                            <td><strong>{{ $order->order_number }}</strong><br><small class="text-muted">{{ $order->public_code }}</small></td>
                                            <td>{{ $order->customer_name }}</td>
                                            <td class="text-center">{{ $order->items->count() }}</td>
                                            <td class="text-end"><strong>${{ number_format($order->total, 2) }}</strong></td>
                                            <td>
                                                @if($order->payment_method == 'transferencia') Transferencia
                                                @elseif($order->payment_method == 'bancolombia') Bancolombia
                                                @elseif($order->payment_method == 'usdt') USDT
                                                @else {{ $order->payment_method }}
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = $order->status == 'completed' ? 'success' : ($order->status == 'paid' ? 'info' : 'warning');
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.photos.order.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                                <p>No hay pedidos en el período seleccionado</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-end"><strong>Totales:</strong></td>
                                        <td class="text-end"><strong>${{ number_format($orders->sum('total'), 2) }}</strong></td>
                                        <td colspan="4"></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $orders->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleCustomDate() {
            const period = document.getElementById('period').value;
            const customDates = document.getElementById('customDates');
            customDates.style.display = period === 'custom' ? 'flex' : 'none';
        }

        // Gráfico de ingresos mensuales
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($monthlyRevenue, 'month')) !!},
                datasets: [{
                    label: 'Ingresos (USD)',
                    data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
                    backgroundColor: 'rgba(0, 236, 254, 0.5)',
                    borderColor: '#00ecfe',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return '$' + value; } } }
                }
            }
        });

        // Gráfico de métodos de pago
        const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($paymentMethods->map(function($m) {
                    return $m->payment_method == 'transferencia' ? 'Transferencia' : ($m->payment_method == 'bancolombia' ? 'Bancolombia' : 'USDT');
                })) !!},
                datasets: [{
                    data: {!! json_encode($paymentMethods->pluck('total')) !!},
                    backgroundColor: ['#00ecfe', '#28a745', '#ffc107', '#dc3545']
                }]
            }
        });

        // Gráfico de ingresos por etapa
        const stageCtx = document.getElementById('stageRevenueChart').getContext('2d');
        new Chart(stageCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($revenueByStage->toArray())) !!},
                datasets: [{
                    label: 'Ingresos (USD)',
                    data: {!! json_encode(array_column($revenueByStage->toArray(), 'revenue')) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.5)',
                    borderColor: '#28a745',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '$' + value; } } } }
            }
        });
    </script>
@endsection
