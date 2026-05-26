@extends('layouts.master')
@section('title')
    Vehículos - Total: {{ $totalVehiculos ?? 0 }}
@endsection
@section('css')
    <style>
        .btn-soft-light:hover, .vehiculoselected {
            background-color: #e0f2ff !important;
        }
        .nav-pills .nav-link {
            background: #eee !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills .nav-link.active  {
            background: #0072c5 !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills{
            border-bottom: 1px solid #0072c5;
        }
        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }
        .error {
            border: 2px solid red !important;
            background-color: #ffe6e6;
        }

        .error:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }

        .vehiculo-item {
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }
        .vehiculo-item:hover {
            background-color: #e0f2ff !important;
            transform: translateX(5px);
        }
        .vehiculo-item.seleccionado {
            border-left-color: #0072c5;
            background-color: #e0f2ff !important;
        }

        .badge-tipo {
            background: #e6f3ff;
            color: #2c3e50;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .foto-thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        .foto-thumb:hover {
            border-color: #0072c5;
            transform: scale(1.05);
            transition: all 0.2s;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        @if(Auth::user() and auth()->user()->type == 'admin')
            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ri-search-line"></i> Buscar Vehículos
                            <span class="badge bg-primary float-end">Total: {{ $totalVehiculos ?? 0 }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vehiculos.index') }}" method="GET"
                              autocomplete="off" class="needs-validation" id="vehiculoForm">
                            <input type="hidden" id="vehiculo_id" name="vehiculo_id" value="">
                            <div class="row">
                                <div class="col-xxl-12">
                                    <div class="search-box mb-3">
                                        <input type="text" class="form-control search" id="busqueda" name="busqueda"
                                               value="{{ $busqueda }}" required
                                               placeholder="Buscar por placa, marca, modelo, cliente...">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                    <div class="invalid-feedback">Ingrese un criterio de búsqueda</div>
                                </div>

                                <div class="col-xxl-12 col-lg-6">
                                    @if($busqueda != '')
                                        <div class="accordion accordion-flush filter-accordion">
                                            <div class="card-body border-bottom p-0">
                                                <div>
                                                    <p class="text-muted fs-13 mb-3">
                                                        Resultados para: <strong>{{ $busqueda }}</strong>
                                                        <span class="badge bg-info float-end">{{ $vehiculos->count() }} encontrado(s)</span>
                                                    </p>
                                                    @forelse($vehiculos as $v)
                                                        <a href="javascript:;"
                                                           onclick="seleccionarVehiculo({{ $v->id }})"
                                                           class="card btn btn-soft-light card-animate d-flex p-2 vehiculo-item
                                                           {{ isset($vehiculo) && $vehiculo->id == $v->id ? 'seleccionado' : '' }}
                                                           border-bottom border-bottom-dashed cursor-pointer"
                                                           style="text-align: left">
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1">{{ $v->marca }} {{ $v->modelo }}</h5>
                                                                <p class="text-muted mb-1 small">
                                                                    <i class="ri-road-map-line"></i> {{ $v->identificacion }}
                                                                    @if($v->year)
                                                                        | <i class="ri-calendar-line"></i> {{ $v->year }}
                                                                    @endif
                                                                </p>
                                                                <p class="text-muted mb-0 small">
                                                                    <i class="ri-user-line"></i> {{ $v->cliente->descrip ?? 'Sin cliente' }}
                                                                    <span class="badge-tipo ms-2">{{ $v->tipo->tipo ?? 'N/A' }}</span>
                                                                </p>
                                                            </div>
                                                        </a>
                                                    @empty
                                                        <p class="text-muted text-center py-3">No se encontraron vehículos</p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-xxl-{{ Auth::user() && auth()->user()->type == 'admin' ? '9' : '12' }}">
            @if(empty($busqueda) && !isset($vehiculo))
                {{-- Mostrar estadísticas cuando no hay búsqueda --}}
                @include('vehiculos.partials.estadisticas')
            @endif

            @if(isset($vehiculo))
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                <small class="text-muted fs-6">({{ $vehiculo->identificacion }})</small>
                            </h5>
                            <div class="flex-shrink-0">
                                <p class="mb-0">
                                    <i class="ri-user-line"></i>
                                    <b>{{ $vehiculo->cliente->descrip ?? 'Sin cliente' }}</b>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Pestañas de navegación -->
                        <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                            <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ $tab == 'tab1' ? 'active' : '' }}"
                                       href="{{ route('vehiculos.index', ['vehiculo_id' => $vehiculo->id, 'tab' => 'tab1', 'busqueda' => $busqueda]) }}"
                                       role="tab">
                                        Información General
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tab == 'tab2' ? 'active' : '' }}"
                                       href="{{ route('vehiculos.index', ['vehiculo_id' => $vehiculo->id, 'tab' => 'tab2', 'busqueda' => $busqueda]) }}"
                                       role="tab">
                                        Mantenimientos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tab == 'tab3' ? 'active' : '' }}"
                                       href="{{ route('vehiculos.index', ['vehiculo_id' => $vehiculo->id, 'tab' => 'tab3', 'busqueda' => $busqueda]) }}"
                                       role="tab">
                                        Historial Completo
                                    </a>
                                </li>
                            </ul>
                            <div class="flex-shrink-0">
                                <a href="javascript:;" class="btn btn-warning btn-sm me-2" onclick="editarVehiculo({{ $vehiculo->id }})">
                                    <i class="ri-edit-line"></i> Editar
                                </a>
                                <a href="{{ route('mantenimiento.rapido') }}?placa={{ urlencode($vehiculo->identificacion) }}"
                                   class="btn btn-success btn-sm">
                                    <i class="ri-add-line"></i> Nuevo Mantenimiento
                                </a>
                            </div>
                        </div>

                        <!-- Contenido de las pestañas (mantén el mismo código que tenías) -->
                        <div class="tab-content">
                            {{-- Aquí va el contenido de las pestañas que ya tenías --}}
                            @include('vehiculos.partials.tablas')
                        </div>
                    </div>
                </div>
            @elseif($busqueda && $vehiculos->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-car-line" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3">No se encontraron vehículos</h5>
                        <p class="text-muted">Intenta con otro criterio de búsqueda</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para editar vehículo (igual que antes) -->
    @include('vehiculos.partials.modal-editar')

@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            $('#busqueda').focus();

            // Enviar formulario al presionar Enter
            $('#busqueda').keypress(function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    $('#vehiculoForm').submit();
                }
            });

            // Inicializar gráfico si existe el canvas
            if ($('#vehiculosChart').length) {
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
            }
        });

        function seleccionarVehiculo(id) {
            $('#vehiculo_id').val(id);
            $('#vehiculoForm').submit();
        }

        function editarVehiculo(id) {
            $.ajax({
                url: '/vehiculos/' + id + '/detalles',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let v = response.vehiculo;
                        $('#edit_fk_tipo').val(v.fk_tipo);
                        $('#edit_marca').val(v.marca);
                        $('#edit_modelo').val(v.modelo);
                        $('#edit_year').val(v.year);
                        $('#edit_identificacion').val(v.identificacion);
                        $('#edit_serialmotor').val(v.serialmotor);
                        $('#edit_serialchasis').val(v.serialchasis);
                        $('#edit_observaciones').val(v.observaciones);

                        $('#formEditarVehiculo').attr('action', '/vehiculos/' + id);
                        $('#editarVehiculoModal').modal('show');
                    }
                }
            });
        }

        function validarFormVehiculo() {
            var campos = ['#edit_marca', '#edit_modelo', '#edit_identificacion'];
            var vacios = [];

            $(campos.join(',')).each(function() {
                if (!$(this).val().trim()) {
                    vacios.push($(this).attr('name') || 'campo');
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });

            if (!$('#edit_fk_tipo').val()) {
                vacios.push('tipo');
                $('#edit_fk_tipo').addClass('error');
            }

            if (vacios.length > 0) {
                alert('Complete los campos obligatorios');
                return false;
            }

            return true;
        }
    </script>
@endsection
