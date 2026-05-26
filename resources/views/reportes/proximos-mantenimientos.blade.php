{{-- resources/views/reportes/proximos-mantenimientos.blade.php --}}
@extends('layouts.master')
@section('title')
    Próximos Mantenimientos - Seguimiento
@endsection
@section('css')
    <style>
        :root {
            --pastel-blue: #e6f3ff;
            --pastel-green: #e1f7e6;
            --pastel-yellow: #fff9e6;
            --pastel-pink: #ffe6f0;
            --pastel-purple: #f0e6ff;
            --pastel-peach: #ffe6d9;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .stats-card.urgente {
            border-left-color: #ef476f;
        }

        .stats-card.contactado {
            border-left-color: #f1be46;
        }

        .stats-card.confirmado {
            border-left-color: #06d6a0;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            line-height: 1;
        }

        .stats-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .mantenimiento-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }

        .mantenimiento-item.urgente {
            border-left-color: #ef476f;
            background: #ffe6e6;
        }

        .mantenimiento-item.proximo {
            border-left-color: #f1be46;
            background: #fff9e6;
        }

        .mantenimiento-item.confirmado {
            border-left-color: #06d6a0;
            background: #e1f7e6;
        }

        .mantenimiento-item.contactado {
            border-left-color: #0072c5;
            background: #e6f3ff;
        }

        .badge-estado {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-urgente {
            background: #ef476f;
            color: white;
        }

        .badge-proximo {
            background: #f1be46;
            color: #2c3e50;
        }

        .badge-confirmado {
            background: #06d6a0;
            color: white;
        }

        .badge-contactado {
            background: #0072c5;
            color: white;
        }

        .telefono-link {
            color: #0072c5;
            text-decoration: none;
        }

        .telefono-link:hover {
            text-decoration: underline;
        }

        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 0.85rem;
            margin-right: 5px;
        }

        .filtros-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 0;
        }

        .quick-grid-item {
            background: white;
            border-radius: 16px;
            padding: 6px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            text-decoration: none;
            color: inherit;
        }

        .quick-grid-item:hover {
            transform: translateY(-5px);
            border-color: #0072c5;
            box-shadow: 0 10px 30px rgba(0, 114, 197, 0.1);
        }

        .quick-grid-item i {
            font-size: 1rem;
            color: #0072c5;
            margin-bottom: 0;
            display: block;
        }

        .quick-grid-item span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .quick-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between" style="background:#faebd7; border-color: #f1be46;">
                    <div>
                        <h5 class="mb-0"><i class="ri-calendar-check-line"></i> Próximos Mantenimientos - Seguimiento</h5>
                        <small>Clientes a contactar para confirmar asistencia</small>
                    </div>
                    <div>
                        <div class="quick-grid  ">
                            <div  class="quick-grid-item">
                                <i class="ri-alert-line" style="font-size: 30px; color: #ef476f;"></i>
                                <span>{{ $estadisticas['urgentes'] }} Urgentes (hoy/mañana)</span>
                            </div>
                            <div   class="quick-grid-item confirmado">
                                <i class="ri-phone-line" style="font-size: 30px; color: #f1be46;"></i>
                                <span>{{ $estadisticas['contactados'] }} Contactados</span>
                            </div>
                            <div  class="quick-grid-item">
                                <i class="ri-checkbox-circle-line" style="font-size: 30px; color: #06d6a0;"></i>
                                <span>{{ $estadisticas['confirmados'] }} Confirmados</span>
                            </div>
                            <div   class="quick-grid-item">
                                <i class="ri-calendar-line" style="font-size: 30px; color: #0072c5;"></i>
                                <span>{{ $estadisticas['total'] }} Total próximos</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Estadísticas -->





                    <!-- Filtros -->
                    <div class="filtros-card">
                        <form method="GET" action="{{ route('reportes.proximos-mantenimientos') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Mostrar próximos</label>
                                <select name="dias" class="form-select" onchange="this.form.submit()">
                                    <option value="7" {{ $dias == '7' ? 'selected' : '' }}>Próximos 7 días</option>
                                    <option value="15" {{ $dias == '15' ? 'selected' : '' }}>Próximos 15 días</option>
                                    <option value="30" {{ $dias == '30' ? 'selected' : '' }}>Próximos 30 días</option>
                                    <option value="todos" {{ $dias == 'todos' ? 'selected' : '' }}>Todos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select" onchange="this.form.submit()">
                                    <option value="todos" {{ $estado == 'todos' ? 'selected' : '' }}>Todos</option>
                                    <option value="urgentes" {{ $estado == 'urgentes' ? 'selected' : '' }}>Urgentes (hoy/mañana)</option>
                                    <option value="pendientes" {{ $estado == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                                    <option value="contactados" {{ $estado == 'contactados' ? 'selected' : '' }}>Contactados</option>
                                    <option value="confirmados" {{ $estado == 'confirmados' ? 'selected' : '' }}>Confirmados</option>
                                    <option value="rechazados" {{ $estado == 'rechazados' ? 'selected' : '' }}>Rechazados</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Vendedor</label>
                                <select name="vendedor" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($vendedores as $v)
                                        <option value="{{ $v->codvend }}" {{ $vendedor == $v->codvend ? 'selected' : '' }}>
                                            {{ $v->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a href="{{ route('reportes.proximos-mantenimientos') }}" class="btn btn-warning bg-opacity-25 w-100">
                                    <i class="ri-refresh-line"></i> Limpiar filtros
                                </a>
                                <button onclick="location.reload()" class="btn btn-info ms-2" title="Recargar">
                                    <i class="ri-refresh-line"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de mantenimientos -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Vehículo</th>
                                <th>Próximo mantenimiento</th>
                                <th>Vendedor</th>
                                <th>Seguimiento</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($mantenimientos as $m)
                                @php
                                    $diasRestantes = $m->proximo_mantenimiento
                                        ? now()->diffInDays($m->proximo_mantenimiento, false)
                                        : null;

                                    $estadoClase = '';
                                    $estadoTexto = '';

                                    if ($m->confirmo_asistencia) {
                                        $estadoClase = 'confirmado';
                                        $estadoTexto = 'Confirmado';
                                    } elseif ($m->cliente_contactado) {
                                        $estadoClase = 'contactado';
                                        $estadoTexto = 'Contactado';
                                    } elseif ($diasRestantes !== null and $diasRestantes <= 2) {
                                        $estadoClase = 'urgente';
                                        $estadoTexto = 'Urgente';
                                    } elseif ($diasRestantes !== null and $diasRestantes <= 7) {
                                        $estadoClase = 'proximo';
                                        $estadoTexto = 'Próximo';
                                    } else {
                                        $estadoClase = '';
                                        $estadoTexto = 'Pendiente';
                                    }
                                @endphp
                                <tr class="mantenimiento-item {{ $estadoClase }}">
                                    <td>

                                        @if($m->confirmo_asistencia === 1)
                                            <span class="badge bg-success">
                                                <i class="ri-check-line"></i> Confirmado
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $m->fecha_confirmacion ? $m->fecha_confirmacion->format('d/m/Y H:i') : '' }}
                                            </small>
                                        @elseif($m->confirmo_asistencia ==0 and $m->fecha_confirmacion and $m->cliente_contactado)
                                            <span class="badge bg-danger">
                                                <i class="ri-close-line"></i> Rechazado
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $m->fecha_confirmacion ? $m->fecha_confirmacion->format('d/m/Y H:i') : '' }}
                                            </small>
                                        @elseif($m->cliente_contactado)
                                            <span class="badge bg-warning">
                                                <i class="ri-phone-line"></i> Contactado
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
            <i class="ri-time-line"></i> Pendiente
        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $m->vehiculo->cliente->descrip ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $m->vehiculo->cliente->id3 ?? '' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $telefono = $m->vehiculo->cliente->telef ?? $m->vehiculo->cliente->movil;
                                        @endphp
                                        @if($telefono)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $telefono) }}"
                                               target="_blank" class="telefono-link">
                                                <i class="ri-whatsapp-line"></i> {{ $telefono }}
                                            </a>
                                        @else
                                            <span class="text-muted">No registrado</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $m->vehiculo->marca }} {{ $m->vehiculo->modelo }}<br>
                                        <small class="text-muted">{{ $m->vehiculo->identificacion }}</small>
                                    </td>
                                    <td>
                                        @if($m->proximo_mantenimiento)
                                            <strong>{{ $m->proximo_mantenimiento->format('d/m/Y') }}</strong>
                                            @if($diasRestantes !== null)
                                                <br>
                                                <small class="{{ $diasRestantes <= 2 ? 'text-danger' : 'text-muted' }}">
                                                    ({{ $diasRestantes == 0 ? 'hoy' : ($diasRestantes == 1 ? 'mañana' : "en {$diasRestantes} días") }})
                                                </small>
                                            @endif
                                        @elseif($m->proximo_kilometraje)
                                            {{ number_format($m->proximo_kilometraje, 0, ',', '.') }} km
                                        @endif
                                    </td>
                                    <td>{{ $m->vendedor->descrip ?? 'N/A' }}</td>
                                    <td>
                                        @if($m->fecha_contactado)
                                            <small>
                                                <i class="ri-phone-line"></i>
                                                {{ $m->fecha_contactado->format('d/m/Y H:i') }}
                                            </small>
                                        @endif
                                        @if($m->fecha_confirmacion)
                                            <br>
                                            <small class="text-success">
                                                <i class="ri-check-line"></i>
                                                Confirmó: {{ $m->fecha_confirmacion->format('d/m/Y H:i') }}
                                            </small>
                                        @endif
                                        @if($m->observaciones_seguimiento)
                                            <br>
                                            <small class="text-muted" data-bs-toggle="tooltip" title="{{ $m->observaciones_seguimiento }}">
                                                <i class="ri-chat-1-line"></i> Ver nota
                                            </small>
                                        @endif
                                    </td>

                                    <td class="action-buttons">

                                        <button onclick="verDetalle('{{ $m->codclie }}', '{{ $m->fk_vehiculo }}', '{{ $m->id }}')"
                                                class="btn btn-sm btn-info">
                                            <i class="ri-eye-line"></i>
                                        </button>

                                        @if(!$m->cliente_contactado)
                                            <button class="btn btn-sm btn-warning" onclick="marcarContactado({{ $m->id }})" title="Marcar como contactado">
                                                <i class="ri-phone-line"></i>
                                            </button>
                                        @endif

                                        @if(!$m->fecha_confirmacion and $m->cliente_contactado)
                                            <button class="btn btn-sm btn-success" onclick="confirmarAsistencia({{ $m->id }}, true)" title="Confirmar asistencia">
                                                <i class="ri-check-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="confirmarAsistencia({{ $m->id }}, false)" title="No asistirá">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        @endif

                                        @if($telefono)
                                            <button class="btn btn-sm btn-success" onclick="enviarWhatsApp({{ $m->id }})" title="Enviar WhatsApp">
                                                <i class="ri-whatsapp-line"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="ri-inbox-line" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="mt-2">No hay mantenimientos próximos para mostrar</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para observaciones de contacto -->
    <div class="modal fade" id="contactarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar contacto con cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="contactarForm">
                        <input type="hidden" id="contactar_id">
                        <div class="mb-3">
                            <label class="form-label">Observaciones del contacto</label>
                            <textarea class="form-control" id="contactar_observaciones" rows="3"
                                      placeholder="¿Qué se conversó con el cliente? ¿Confirmó asistencia?"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarContacto()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar asistencia -->
    <div class="modal fade" id="confirmarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar asistencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="confirmarForm">
                        <input type="hidden" id="confirmar_id">
                        <input type="hidden" id="confirmar_estado">
                        <div class="mb-3">
                            <label class="form-label">Observaciones adicionales</label>
                            <textarea class="form-control" id="confirmar_observaciones" rows="3"
                                      placeholder="Comentarios sobre la confirmación"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarConfirmacion()">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function marcarContactado(id) {
            $('#contactar_id').val(id);
            $('#contactar_observaciones').val('');
            $('#contactarModal').modal('show');
        }

        function guardarContacto() {
            let id = $('#contactar_id').val();
            let observaciones = $('#contactar_observaciones').val();

            $.ajax({
                url: '/reportes/proximos-mantenimientos/' + id + '/contactar',
                method: 'POST',
                data: {
                    observaciones: observaciones,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#contactarModal').modal('hide');
                        location.reload();
                    }
                }
            });
        }

        function confirmarAsistencia(id, confirmar) {
            $('#confirmar_id').val(id);
            $('#confirmar_estado').val(confirmar);
            $('#confirmar_observaciones').val('');
            $('#confirmarModal').modal('show');
        }

        function guardarConfirmacion() {
            let id = $('#confirmar_id').val();
            let confirmar = $('#confirmar_estado').val() === 'true';
            let observaciones = $('#confirmar_observaciones').val();

            $.ajax({
                url: '/reportes/proximos-mantenimientos/' + id + '/confirmar',
                method: 'POST',
                data: {
                    confirmar: confirmar,
                    observaciones: observaciones,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#confirmarModal').modal('hide');
                        location.reload();
                    }
                }
            });
        }

        function enviarWhatsApp(id) {
            $.ajax({
                url: '/reportes/proximos-mantenimientos/' + id + '/enviar-recordatorio',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.open(response.whatsapp_url, '_blank');
                    } else {
                        alert(response.message);
                    }
                }
            });
        }


        function verDetalle(codclie, vehiculoid, mantenimientoid) {
            let url = '/clientes/' + codclie + '/vehiculos/' + vehiculoid + '/mantenimientos/' + mantenimientoid;
            // Abre en nueva pestaña con características específicas
            window.open(url, '_blank', 'noopener,noreferrer');
        }

        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
