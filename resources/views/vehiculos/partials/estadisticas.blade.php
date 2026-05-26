{{-- resources/views/vehiculos/partials/estadisticas.blade.php --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #0072c5 0%, #0072c5 100%); color: white;">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        <i class="ri-bar-chart-2-line"></i> Estadísticas de Vehículos
                    </h5>
                    <div class="flex-shrink-0">
                        <span class="badge bg-light text-dark">Últimos 6 meses</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Tarjetas de resumen -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 bg-primary bg-opacity-10">
                            <div class="card-body" style="min-height: 131px">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Vehículos</h6>
                                        <h3 class="mb-0">{{ number_format($estadisticas['total_general']) }}</h3>
                                    </div>
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-primary rounded-circle fs-20">
                                            <i class="ri-car-line text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 bg-success bg-opacity-10">
                            <div class="card-body" style="min-height: 131px">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Este Mes</h6>
                                        <h3 class="mb-0">{{ number_format($estadisticas['total_mes_actual']) }}</h3>
                                        @if($estadisticas['variacion'] > 0)
                                            <small class="text-success">
                                                <i class="ri-arrow-up-line"></i> {{ $estadisticas['variacion'] }}%
                                            </small>
                                        @elseif($estadisticas['variacion'] < 0)
                                            <small class="text-danger">
                                                <i class="ri-arrow-down-line"></i> {{ abs($estadisticas['variacion']) }}%
                                            </small>
                                        @else
                                            <small class="text-muted">Sin cambios</small>
                                        @endif
                                    </div>
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-success rounded-circle fs-20">
                                            <i class="ri-calendar-check-line text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 bg-info bg-opacity-10">
                            <div class="card-body" style="min-height: 131px">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Con Mantenimientos</h6>
                                        <h3 class="mb-0">{{ number_format($estadisticas['con_mantenimientos']) }}</h3>
                                        <small class="text-info">
                                            {{ round(($estadisticas['con_mantenimientos'] / max($estadisticas['total_general'], 1)) * 100) }}% del total
                                        </small>
                                    </div>
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-info rounded-circle fs-20">
                                            <i class="ri-tools-line text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 bg-warning bg-opacity-10">
                            <div class="card-body" style="min-height: 131px">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Sin Mantenimientos</h6>
                                        <h3 class="mb-0">{{ number_format($estadisticas['sin_mantenimientos']) }}</h3>
                                        <small class="text-warning">
                                            {{ round(($estadisticas['sin_mantenimientos'] / max($estadisticas['total_general'], 1)) * 100) }}% del total
                                        </small>
                                    </div>
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-warning rounded-circle fs-20">
                                            <i class="ri-alert-line text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de últimos 6 meses -->
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="mb-3">Vehículos Registrados por Mes</h6>
                        <canvas id="vehiculosChart" style="max-height: 250px;"></canvas>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-3">Top 5 Marcas</h6>
                        <div class="list-group">
                            @foreach($estadisticas['top_marcas'] as $marca)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $marca->marca }}
                                    <span class="badge bg-primary rounded-pill">{{ $marca->total }}</span>
                                </div>
                            @endforeach
                        </div>

                        <h6 class="mb-3 mt-4">Vehículos por Tipo</h6>
                        @foreach($estadisticas['por_tipo'] as $tipo)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $tipo['tipo'] }}</span>
                                <span class="badge bg-info">{{ $tipo['total'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('vehiculosChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($estadisticas['meses']) !!},
                    datasets: [{
                        label: 'Vehículos Registrados',
                        data: {!! json_encode($estadisticas['datos']) !!},
                        backgroundColor: 'rgba(0, 114, 197, 0.2)',
                        borderColor: 'rgba(0, 114, 197, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
