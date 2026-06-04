<!-- resources/views/index.blade.php -->
@extends('layouts.master')

@section('title')
    Dashboard - Vuelta a la Fría 2026
@endsection

@section('css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0072c5, #0059a3);
            --secondary-gradient: linear-gradient(135deg, #7c6bff, #6356cc);
            --success-gradient: linear-gradient(135deg, #06d6a0, #05ab80);
            --warning-gradient: linear-gradient(135deg, #f1be46, #c19838);
            --danger-gradient: linear-gradient(135deg, #ef476f, #bf3959);
        }

        /* Estadísticas principales */
        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 114, 197, 0.1);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stats-card:hover::before {
            opacity: 1;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, rgba(0, 114, 197, 0.1), rgba(0, 89, 163, 0.1));
            color: #0072c5;
            transition: all 0.3s ease;
        }

        .stats-card:hover .stats-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #0c192c;
        }

        .stats-label {
            color: #878a99;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tarjetas de acceso rápido */
        .quick-access-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            cursor: pointer;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .quick-access-card:hover {
            transform: translateY(-5px);
            border-color: #0072c5;
            box-shadow: 0 10px 30px rgba(0, 114, 197, 0.15);
        }

        .quick-access-card .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .quick-access-card.primary .icon-wrapper {
            background: rgba(0, 114, 197, 0.1);
            color: #0072c5;
        }

        .quick-access-card.success .icon-wrapper {
            background: rgba(6, 214, 160, 0.1);
            color: #06d6a0;
        }

        .quick-access-card.warning .icon-wrapper {
            background: rgba(241, 190, 70, 0.1);
            color: #f1be46;
        }

        .quick-access-card.purple .icon-wrapper {
            background: rgba(124, 107, 255, 0.1);
            color: #7c6bff;
        }

        .quick-access-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .quick-access-card h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #0c192c;
        }

        .quick-access-card p {
            font-size: 0.85rem;
            color: #878a99;
            margin-bottom: 0;
        }

        /* Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending { background: #f1be46; color: #000; }
        .status-approved { background: #06d6a0; color: #000; }
        .status-rejected { background: #ef476f; color: #fff; }
        .status-paid { background: #0072c5; color: #fff; }

        /* Modal */
        .payment-proof-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-proof-card:hover {
            background: #e9ecef;
            transform: scale(1.02);
        }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="mb-1 fw-bold" style="color: #0c192c;">
                👋 ¡Bienvenido, {{ Auth::user()->name ?? 'Administrador' }}!
            </h3>
            <p class="text-muted mb-0">
                <i class="ri-calendar-line me-1"></i>
                {{ now()->format('l, d F Y') }}
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Inscripciones Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingRegistrations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Inscripciones Aprobadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedRegistrations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Equipos Registrados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeams }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Atletas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAthletes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bicycle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acceso Rápido -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-rocket me-2 text-primary"></i>Acceso Rápido</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.registrations.index') }}" class="text-decoration-none">
                                <div class="quick-access-card primary text-center">
                                    <div class="icon-wrapper mx-auto">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h4>Inscripciones</h4>
                                    <p>Gestionar todas</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.teams.index') }}" class="text-decoration-none">
                                <div class="quick-access-card success text-center">
                                    <div class="icon-wrapper mx-auto">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h4>Equipos</h4>
                                    <p>Ver y gestionar</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.athletes.index') }}" class="text-decoration-none">
                                <div class="quick-access-card warning text-center">
                                    <div class="icon-wrapper mx-auto">
                                        <i class="fas fa-bicycle"></i>
                                    </div>
                                    <h4>Atletas</h4>
                                    <p>Lista de participantes</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.stages.index') }}" class="text-decoration-none">
                                <div class="quick-access-card purple text-center">
                                    <div class="icon-wrapper mx-auto">
                                        <i class="fas fa-route"></i>
                                    </div>
                                    <h4>Etapas</h4>
                                    <p>Gestionar recorridos</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Inscripciones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Últimas Inscripciones
            </h6>
            <a href="{{ route('admin.registrations.index') }}" class="btn btn-sm btn-primary">
                Ver todas <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Comprobante</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentRegistrations as $reg)
                        <tr>
                            <td>{{ $reg->id }}</td>
                            <td>
                                <span class="badge {{ $reg->registration_type == 'individual' ? 'bg-info' : 'bg-primary' }}">
                                    {{ ucfirst($reg->registration_type) }}
                                </span>
                            </td>
                            <td>
                                @if($reg->registration_type == 'individual' && $reg->athlete)
                                    <strong>{{ $reg->athlete->first_name }} {{ $reg->athlete->last_name }}</strong>
                                    <br><small class="text-muted">{{ $reg->athlete->category ?? '' }}</small>
                                @elseif($reg->team)
                                    <strong>{{ $reg->team->name }}</strong>
                                    <br><small class="text-muted">Código: {{ $reg->team->access_code }}</small>
                                @endif
                            </td>
                            <td>{{ $reg->email }}</td>
                            <td><span class="fw-bold text-success">${{ number_format($reg->amount, 2) }}</span></td>
                            <td>
                                @if($reg->status == 'pending')
                                    <span class="badge status-pending">⏳ Pendiente</span>
                                @elseif($reg->status == 'approved')
                                    <span class="badge status-approved">✅ Aprobada</span>
                                @elseif($reg->status == 'rejected')
                                    <span class="badge status-rejected">❌ Rechazada</span>
                                @elseif($reg->status == 'paid')
                                    <span class="badge status-paid">💰 Pagada</span>
                                @endif
                            </td>
                            <td>
                                @if($reg->payment_proof)
                                    <button class="btn btn-sm btn-secondary" onclick="viewPaymentProof('{{ $reg->payment_proof }}')" title="Ver comprobante">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                @else
                                    <span class="text-muted">Sin comprobante</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-info" onclick="viewDetails({{ $reg->id }})" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($reg->status == 'pending')
                                        <button class="btn btn-success" onclick="changeStatus({{ $reg->id }}, 'approved')" title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="changeStatus({{ $reg->id }}, 'rejected')" title="Rechazar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    @if($reg->status == 'approved' && !$reg->payment_status == 'paid')
                                        <button class="btn btn-primary" onclick="changeStatus({{ $reg->id }}, 'paid')" title="Marcar como pagado">
                                            <i class="fas fa-dollar-sign"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                No hay inscripciones registradas
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detalles de Inscripción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Cargando...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Comprobante -->
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Comprobante de Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="proofContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Ver detalles completos
        function viewDetails(id) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            const content = document.getElementById('detailsContent');
            content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>';
            modal.show();

            fetch(`/admin/registrations/${id}`)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if(data.registration_type == 'individual') {
                        html = `
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary"><i class="fas fa-user"></i> Datos del Ciclista</h6>
                                            <hr>
                                            <p><strong>Nombre:</strong> ${data.athlete?.first_name} ${data.athlete?.last_name}</p>
                                            <p><strong>Documento:</strong> ${data.athlete?.document_type || 'N/A'} - ${data.athlete?.document_number || 'N/A'}</p>
                                            <p><strong>Nacionalidad:</strong> ${data.athlete?.nationality || 'Venezolana'}</p>
                                            <p><strong>Fecha Nac.:</strong> ${data.athlete?.birth_date}</p>
                                            <p><strong>Categoría:</strong> <span class="badge bg-primary">${data.athlete?.category}</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary"><i class="fas fa-credit-card"></i> Datos de Pago</h6>
                                            <hr>
                                            <p><strong>Email:</strong> ${data.email}</p>
                                            <p><strong>Teléfono:</strong> ${data.phone}</p>
                                            <p><strong>Monto:</strong> <span class="fw-bold text-success">$${data.amount}</span></p>
                                            <p><strong>Método:</strong> ${data.payment_method || 'N/A'}</p>
                                            <p><strong>Referencia:</strong> ${data.payment_reference || 'N/A'}</p>
                                            <p><strong>Estructura:</strong> ${JSON.parse(data.notes || '{}').structure_name || 'Independiente'}</p>
                                            <p><strong>Estado:</strong> ${getStatusBadge(data.status)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        html = `
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary"><i class="fas fa-building"></i> Datos del Equipo</h6>
                                            <hr>
                                            <p><strong>Equipo:</strong> ${data.team?.name}</p>
                                            <p><strong>País:</strong> ${data.team?.country}</p>
                                            <p><strong>Código:</strong> <code>${data.team?.access_code}</code></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary"><i class="fas fa-user-tie"></i> Delegado</h6>
                                            <hr>
                                            <p><strong>Nombre:</strong> ${JSON.parse(data.notes || '{}').delegate_name || 'N/A'}</p>
                                            <p><strong>Email:</strong> ${data.email}</p>
                                            <p><strong>Teléfono:</strong> ${data.phone}</p>
                                            <p><strong>Monto:</strong> <span class="fw-bold text-success">$${data.amount}</span></p>
                                            <p><strong>Estado:</strong> ${getStatusBadge(data.status)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    if(data.payment_proof) {
                        html += `
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary"><i class="fas fa-receipt"></i> Comprobante de Pago</h6>
                                            <div class="payment-proof-card" onclick="viewPaymentProof('${data.payment_proof}')">
                                                <i class="fas fa-file-invoice fa-3x text-primary mb-2"></i>
                                                <p>Haz clic para ver el comprobante</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    content.innerHTML = html;
                })
                .catch(error => {
                    content.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles</div>';
                });
        }

        // Cambiar estado
        function changeStatus(id, status) {
            let title = '', text = '', confirmText = '';
            switch(status) {
                case 'approved':
                    title = '¿Aprobar inscripción?';
                    text = 'Esta inscripción será aprobada.';
                    confirmText = 'Sí, aprobar';
                    break;
                case 'rejected':
                    title = '¿Rechazar inscripción?';
                    text = 'Esta inscripción será rechazada.';
                    confirmText = 'Sí, rechazar';
                    break;
                case 'paid':
                    title = '¿Marcar como pagado?';
                    text = 'Se confirmará el pago de esta inscripción.';
                    confirmText = 'Sí, marcar como pagado';
                    break;
                default:
                    title = '¿Cambiar estado?';
                    text = `Estado: ${status}`;
                    confirmText = 'Sí, cambiar';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'rejected' ? '#d33' : '#0072c5',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/registrations/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    }).then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                Swal.fire('¡Actualizado!', 'Estado cambiado correctamente', 'success');
                                location.reload();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Ver comprobante
        function viewPaymentProof(path) {
            const modal = new bootstrap.Modal(document.getElementById('proofModal'));
            const content = document.getElementById('proofContent');
            const extension = path.split('.').pop().toLowerCase();
            const fullPath = '/' + path;

            if(extension === 'pdf') {
                content.innerHTML = `<iframe src="${fullPath}" width="100%" height="500px"></iframe>`;
            } else {
                content.innerHTML = `<img src="${fullPath}" class="img-fluid rounded" style="max-height: 500px; width: auto;">`;
            }
            modal.show();
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge status-pending">⏳ Pendiente</span>',
                'approved': '<span class="badge status-approved">✅ Aprobada</span>',
                'rejected': '<span class="badge status-rejected">❌ Rechazada</span>',
                'paid': '<span class="badge status-paid">💰 Pagada</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
        }
    </script>
@endsection
