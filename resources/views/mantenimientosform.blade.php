@extends('layouts.master')
@section('title')
    {{ isset($mantenimiento) ? 'Editar' : 'Nuevo' }} Mantenimiento - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
@endsection
@section('css')
    <style>
        .btn-soft-light:hover, .codclieseleted{
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
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        {{ isset($mantenimiento) ? 'Editar' : 'Nuevo' }} Mantenimiento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="alert alert-info">
                                <strong>Cliente:</strong> {{ $vehiculo->cliente->descrip }}<br>
                                <strong>Vehículo:</strong> {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                ({{ $vehiculo->identificacion }}) - {{ $vehiculo->year }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ isset($mantenimiento)
                    ? route('clientes.vehiculos.mantenimientos.update', [$vehiculo->codclie, $vehiculo->id, $mantenimiento->id])
                    : route('clientes.vehiculos.mantenimientos.store', [$vehiculo->codclie, $vehiculo->id]) }}"
                          method="POST" id="mantenimientoForm" onsubmit="return validateForm()">

                        @csrf
                        @if(isset($mantenimiento))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Fecha del mantenimiento -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha del Mantenimiento <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('fecha_mantenimiento') is-invalid @enderror"
                                       name="fecha_mantenimiento" id="fecha_mantenimiento" required
                                       value="{{ old('fecha_mantenimiento', isset($mantenimiento) ? $mantenimiento->fecha_mantenimiento->format('Y-m-d') : date('Y-m-d')) }}">
                                @error('fecha_mantenimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kilometraje -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kilometraje Actual</label>
                                <input type="number" class="form-control @error('kilometraje') is-invalid @enderror"
                                       name="kilometraje" id="kilometraje" min="0" step="1"
                                       value="{{ old('kilometraje', isset($mantenimiento) ? $mantenimiento->kilometraje : '') }}"
                                       placeholder="Ej: 50000">
                                @error('kilometraje')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tipo de mantenimiento -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo de Mantenimiento <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipo_mantenimiento') is-invalid @enderror"
                                        name="tipo_mantenimiento" id="tipo_mantenimiento" required>
                                    <option value="">Seleccione tipo</option>
                                    @foreach($tipos_mantenimiento as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('tipo_mantenimiento', isset($mantenimiento) ? $mantenimiento->tipo_mantenimiento : '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_mantenimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Productos utilizados -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Producto/Servicio Utilizado</label>
                                <input type="text" class="form-control" name="producto_utilizado" id="producto_utilizado"
                                       value="{{ old('producto_utilizado', isset($mantenimiento) ? $mantenimiento->producto_utilizado : '') }}"
                                       placeholder="Ej: Aceite Mobil 1 20W-50">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Marca del Producto</label>
                                <input type="text" class="form-control" name="marca_producto" id="marca_producto"
                                       value="{{ old('marca_producto', isset($mantenimiento) ? $mantenimiento->marca_producto : '') }}"
                                       placeholder="Ej: Mobil">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Costo ($)</label>
                                <input type="number" class="form-control" name="costo" id="costo" min="0" step="0.01"
                                       value="{{ old('costo', isset($mantenimiento) ? $mantenimiento->costo : '') }}"
                                       placeholder="0.00">
                            </div>
                        </div>

                        <!-- Próximo mantenimiento (sugerido) -->
                        <div class="row" id="proximoSection" style="{{ (old('tipo_mantenimiento', isset($mantenimiento) ? $mantenimiento->tipo_mantenimiento : '') == 'cambio_aceite') ? '' : 'display: none;' }}">
                            <div class="col-md-12">
                                <hr>
                                <h6>Próximo Mantenimiento Sugerido</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Próximo Mantenimiento</label>
                                <input type="date" class="form-control" name="proximo_mantenimiento" id="proximo_mantenimiento"
                                       value="{{ old('proximo_mantenimiento', isset($mantenimiento) && $mantenimiento->proximo_mantenimiento ? $mantenimiento->proximo_mantenimiento->format('Y-m-d') : '') }}">
                                <small class="text-muted">Sugerido: 6 meses después</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kilometraje Próximo Mantenimiento</label>
                                <input type="number" class="form-control" name="proximo_kilometraje" id="proximo_kilometraje" min="0"
                                       value="{{ old('proximo_kilometraje', isset($mantenimiento) ? $mantenimiento->proximo_kilometraje : '') }}">
                                <small class="text-muted">Sugerido: +5000 km</small>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observaciones / Notas adicionales</label>
                                <textarea class="form-control" name="observaciones" id="observaciones" rows="3">{{ old('observaciones', isset($mantenimiento) ? $mantenimiento->observaciones : '') }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-save-line"></i>
                                    {{ isset($mantenimiento) ? 'Actualizar' : 'Guardar' }} Mantenimiento
                                </button>
                                <a href="{{ route('clientes.vehiculos.mantenimientos', [$vehiculo->codclie, $vehiculo->id]) }}"
                                   class="btn btn-secondary">
                                    <i class="ri-arrow-go-back-line"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial rápido de mantenimientos -->
    @if(!isset($mantenimiento))
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Últimos Mantenimientos de este Vehículo</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Kilometraje</th>
                                    <th>Producto</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $ultimos = App\Models\CWMantenimiento::where('fk_vehiculo', $vehiculo->id)
                                        ->orderBy('fecha_mantenimiento', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp
                                @forelse($ultimos as $m)
                                    <tr>
                                        <td>{{ $m->fechaformat }}</td>
                                        <td>
                                            @switch($m->tipo_mantenimiento)
                                                @case('cambio_aceite') Cambio Aceite @break
                                                @case('cambio_filtro_aceite') Filtro Aceite @break
                                                @case('cambio_filtro_gasolina') Filtro Gasolina @break
                                                @case('cambio_filtro_aire') Filtro Aire @break
                                                @case('mantenimiento_inyectores') Inyectores @break
                                                @case('bateria') Batería @break
                                                @default Otros
                                            @endswitch
                                        </td>
                                        <td>{{ number_format($m->kilometraje, 0, ',', '.') }} km</td>
                                        <td>{{ $m->producto_utilizado }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No hay mantenimientos previos</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Mostrar/ocultar sección de próximo mantenimiento según el tipo
            $('#tipo_mantenimiento').change(function() {
                if ($(this).val() == 'cambio_aceite') {
                    $('#proximoSection').fadeIn();

                    // Sugerir fechas automáticamente
                    var fechaActual = $('#fecha_mantenimiento').val();
                    if (fechaActual) {
                        var fecha = new Date(fechaActual);
                        fecha.setMonth(fecha.getMonth() + 6);
                        var year = fecha.getFullYear();
                        var month = (fecha.getMonth() + 1).toString().padStart(2, '0');
                        var day = fecha.getDate().toString().padStart(2, '0');
                        $('#proximo_mantenimiento').val(year + '-' + month + '-' + day);
                    }

                    var km = $('#kilometraje').val();
                    if (km) {
                        $('#proximo_kilometraje').val(parseInt(km) + 5000);
                    }
                } else {
                    $('#proximoSection').fadeOut();
                }
            });

            // Auto-calcular próximo kilometraje cuando cambia el kilometraje actual
            $('#kilometraje').change(function() {
                if ($('#tipo_mantenimiento').val() == 'cambio_aceite') {
                    var km = $(this).val();
                    if (km) {
                        $('#proximo_kilometraje').val(parseInt(km) + 5000);
                    }
                }
            });

            // Auto-calcular próxima fecha cuando cambia la fecha
            $('#fecha_mantenimiento').change(function() {
                if ($('#tipo_mantenimiento').val() == 'cambio_aceite') {
                    var fecha = new Date($(this).val());
                    fecha.setMonth(fecha.getMonth() + 6);
                    var year = fecha.getFullYear();
                    var month = (fecha.getMonth() + 1).toString().padStart(2, '0');
                    var day = fecha.getDate().toString().padStart(2, '0');
                    $('#proximo_mantenimiento').val(year + '-' + month + '-' + day);
                }
            });

            // Si es edición y es cambio de aceite, mostrar la sección
            @if(isset($mantenimiento) && $mantenimiento->tipo_mantenimiento == 'cambio_aceite')
            $('#proximoSection').show();
            @endif
        });

        function validateForm() {
            var fecha = $('#fecha_mantenimiento').val();
            var tipo = $('#tipo_mantenimiento').val();
            var camposVacios = [];

            if (!fecha) camposVacios.push('Fecha');
            if (!tipo) camposVacios.push('Tipo de mantenimiento');

            if (camposVacios.length > 0) {
                alert('Complete los campos obligatorios: ' + camposVacios.join(', '));
                return false;
            }

            // Validar que la fecha no sea futura (opcional, depende de la lógica del negocio)
            var fechaIngresada = new Date(fecha);
            var hoy = new Date();
            if (fechaIngresada > hoy) {
                if (!confirm('La fecha ingresada es futura. ¿Desea continuar?')) {
                    return false;
                }
            }

            return true;
        }
    </script>
@endsection
