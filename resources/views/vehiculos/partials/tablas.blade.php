{{-- resources/views/vehiculos/partials/tablas.blade.php --}}
{{-- Contenido de las pestañas que ya tenías --}}
@if($tab == 'tab1')
    <div class="tab-pane active" id="info-general">
        <div class="row">
            <!-- Foto del vehículo -->
            <div class="col-md-3 text-center mb-3">
                @if($vehiculo->foto_vehiculo)
                    <img src="{{ asset('storage/' . $vehiculo->foto_vehiculo) }}"
                         class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                @else
                    <div class="bg-light p-4 text-center rounded">
                        <i class="ri-car-line" style="font-size: 5rem; color: #ccc;"></i>
                        <p class="text-muted">Sin foto</p>
                    </div>
                @endif
            </div>

            <!-- Datos del vehículo -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Tipo:</strong></td>
                                <td>{{ $vehiculo->tipo->tipo ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Marca:</strong></td>
                                <td>{{ $vehiculo->marca }}</td>
                            </tr>
                            <tr>
                                <td><strong>Modelo:</strong></td>
                                <td>{{ $vehiculo->modelo }}</td>
                            </tr>
                            <tr>
                                <td><strong>Año:</strong></td>
                                <td>{{ $vehiculo->year ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Placa/Identificación:</strong></td>
                                <td><span class="badge bg-primary">{{ $vehiculo->identificacion }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Serial Motor:</strong></td>
                                <td>{{ $vehiculo->serialmotor ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Serial Chasis:</strong></td>
                                <td>{{ $vehiculo->serialchasis ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha Registro:</strong></td>
                                <td>{{ $vehiculo->created_at ? $vehiculo->created_at->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Última Actualización:</strong></td>
                                <td>{{ $vehiculo->updated_at ? $vehiculo->updated_at->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($vehiculo->observaciones)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Observaciones:</strong><br>
                                {{ $vehiculo->observaciones }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Últimos mantenimientos (resumen) -->
        @if($mantenimientos->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="mb-3">Últimos Mantenimientos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Kilometraje</th>
                                <th>Próximo</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mantenimientos->take(5) as $m)
                                <tr>
                                    <td>{{ $m->fechaformat }}</td>
                                    <td>
                                        @switch($m->tipo_mantenimiento)
                                            @case('cambio_aceite') Cambio Aceite @break
                                            @case('cambio_filtro_aceite') Filtro Aceite @break
                                            @default {{ $m->tipo_mantenimiento }}
                                        @endswitch
                                    </td>
                                    <td>{{ number_format($m->kilometraje, 0, ',', '.') }} km</td>
                                    <td>
                                        @if($m->proximo_mantenimiento)
                                            {{ $m->proximo_mantenimiento->format('d/m/Y') }}
                                        @elseif($m->proximo_kilometraje)
                                            {{ number_format($m->proximo_kilometraje, 0, ',', '.') }} km
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('clientes.vehiculos.mantenimientos.show', [$vehiculo->codclie, $vehiculo->id, $m->id]) }}"
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

@if($tab == 'tab2')
    <div class="tab-pane active" id="mantenimientos">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Tipo</th>
                    <th>Kilometraje</th>
                    <th>Productos</th>
                    <th>Próximo</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($mantenimientos as $m)
                    <tr>
                        <td>{{ $m->fechaformat }}</td>
                        <td>{{ $m->horaformated ?? $m->hora_mantenimiento }}</td>
                        <td>
                            @switch($m->tipo_mantenimiento)
                                @case('cambio_aceite') Cambio Aceite @break
                                @case('cambio_filtro_aceite') Filtro Aceite @break
                                @case('cambio_filtro_gasolina') Filtro Gasolina @break
                                @case('cambio_filtro_aire') Filtro Aire @break
                                @case('mantenimiento_inyectores') Inyectores @break
                                @case('bateria') Batería @break
                                @default {{ $m->tipo_mantenimiento }}
                            @endswitch
                        </td>
                        <td>{{ number_format($m->kilometraje, 0, ',', '.') }} km</td>
                        <td>{{ $m->productos->count() }} producto(s)</td>
                        <td>
                            @if($m->proximo_mantenimiento)
                                {{ $m->proximo_mantenimiento->format('d/m/Y') }}
                            @elseif($m->proximo_kilometraje)
                                {{ number_format($m->proximo_kilometraje, 0, ',', '.') }} km
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('clientes.vehiculos.mantenimientos.show', [$vehiculo->codclie, $vehiculo->id, $m->id]) }}"
                               class="btn btn-sm btn-info" title="Ver">
                                <i class="ri-eye-line"></i>
                            </a>
                            <a href="{{ route('clientes.vehiculos.mantenimientos.edit', [$vehiculo->codclie, $vehiculo->id, $m->id]) }}"
                               class="btn btn-sm btn-warning" title="Editar">
                                <i class="ri-edit-line"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            No hay mantenimientos registrados para este vehículo
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@if($tab == 'tab3')
    <div class="tab-pane active" id="historial">
        <div class="timeline">
            @forelse($mantenimientos as $m)
                <div class="card mb-3">
                    <div class="card-header" style="background: #f0f7ff;">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">
                                <i class="ri-calendar-line"></i>
                                {{ $m->fechaformat }} {{ $m->horaformated }}
                            </h6>
                            <span class="badge bg-info">{{ number_format($m->kilometraje, 0, ',', '.') }} km</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Tipo:</strong>
                                    @switch($m->tipo_mantenimiento)
                                        @case('cambio_aceite') Cambio de Aceite @break
                                        @case('cambio_filtro_aceite') Cambio de Filtro de Aceite @break
                                        @case('cambio_filtro_gasolina') Cambio de Filtro de Gasolina @break
                                        @case('cambio_filtro_aire') Cambio de Filtro de Aire @break
                                        @case('mantenimiento_inyectores') Mantenimiento de Inyectores @break
                                        @case('bateria') Batería @break
                                        @default {{ $m->tipo_mantenimiento }}
                                    @endswitch
                                </p>

                                @if($m->productos->count() > 0)
                                    <p><strong>Productos utilizados:</strong></p>
                                    <ul>
                                        @foreach($m->productos as $p)
                                            <li>{{ $p->descripcion }} (x{{ $p->cantidad }})</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if($m->observaciones)
                                    <p><strong>Observaciones:</strong> {{ $m->observaciones }}</p>
                                @endif
                            </div>
                            <div class="col-md-4 text-end">
                                @if($m->proximo_mantenimiento || $m->proximo_kilometraje)
                                    <div class="alert alert-success p-2">
                                        <strong>Próximo:</strong><br>
                                        @if($m->proximo_mantenimiento)
                                            {{ $m->proximo_mantenimiento->format('d/m/Y') }}
                                        @endif
                                        @if($m->proximo_kilometraje)
                                            {{ number_format($m->proximo_kilometraje, 0, ',', '.') }} km
                                        @endif
                                    </div>
                                @endif

                                <a href="{{ route('clientes.vehiculos.mantenimientos.show', [$vehiculo->codclie, $vehiculo->id, $m->id]) }}"
                                   class="btn btn-sm btn-primary">
                                    Ver detalles
                                </a>
                            </div>
                        </div>

                        @if($m->fotos->count() > 0)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <small class="text-muted">
                                        <i class="ri-camera-line"></i> {{ $m->fotos->count() }} foto(s)
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="ri-history-line" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-2">No hay mantenimientos registrados</p>
                </div>
            @endforelse
        </div>
    </div>
@endif
