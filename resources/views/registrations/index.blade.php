<!-- resources/views/registrations/index.blade.php -->
@extends('layouts.master')

@section('title', 'Gestionar Inscripciones')

@section('css')
    <style>
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

        .modal-image {
            max-width: 100%;
            max-height: 500px;
            object-fit: contain;
        }

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
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">📋 Gestión de Inscripciones</h4>
            <a href="{{ route('admin.registrations.export') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Exportar CSV
            </a>
        </div>

        <!-- Filtros -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select id="statusFilter" class="form-select">
                            <option value="">Todos</option>
                            <option value="pending">Pendiente</option>
                            <option value="approved">Aprobada</option>
                            <option value="rejected">Rechazada</option>
                            <option value="paid">Pagada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select id="typeFilter" class="form-select">
                            <option value="">Todos</option>
                            <option value="individual">Individual</option>
                            <option value="team">Equipo</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Buscar</label>
                        <input type="text" id="searchInput" class="form-control" placeholder="Nombre, email o teléfono...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de inscripciones -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="registrationsTable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Monto</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($registrations as $reg)
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
                                        <br><small class="text-muted">Categoría: {{ $reg->athlete->category }}</small>
                                    @elseif($reg->team)
                                        <strong>{{ $reg->team->name }}</strong>
                                        <br><small class="text-muted">Código: {{ $reg->team->access_code }}</small>
                                    @endif
                                </td>
                                <td>{{ $reg->email }}</td>
                                <td>{{ $reg->phone }}</td>
                                <td class="fw-bold text-success">${{ number_format($reg->amount, 2) }}</td>
                                <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($reg->payment_method ?? 'N/A') }}
                                </span>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm status-select" data-id="{{ $reg->id }}" style="width: 130px;">
                                        <option value="pending" {{ $reg->status == 'pending' ? 'selected' : '' }}>⏳ Pendiente</option>
                                        <option value="approved" {{ $reg->status == 'approved' ? 'selected' : '' }}>✅ Aprobada</option>
                                        <option value="rejected" {{ $reg->status == 'rejected' ? 'selected' : '' }}>❌ Rechazada</option>
                                        <option value="paid" {{ $reg->status == 'paid' ? 'selected' : '' }}>💰 Pagada</option>
                                    </select>
                                </td>
                                <td>{{ $reg->registered_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetails({{ $reg->id }})" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($reg->payment_proof)
                                        <button class="btn btn-sm btn-secondary" onclick="viewPaymentProof('{{ $reg->payment_proof }}')" title="Ver comprobante">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-danger" onclick="deleteRegistration({{ $reg->id }})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $registrations->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalles de Inscripción</h5>
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
                    <h5 class="modal-title">Comprobante de Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="proofContent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Cambiar estado
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const id = this.dataset.id;
                const status = this.value;

                Swal.fire({
                    title: '¿Cambiar estado?',
                    text: `La inscripción quedará como: ${status}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0072c5',
                    confirmButtonText: 'Sí, cambiar',
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
                    } else {
                        // Restaurar valor anterior
                        location.reload();
                    }
                });
            });
        });

        // Ver detalles
        function viewDetails(id) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            const content = document.getElementById('detailsContent');
            content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando...</p></div>';
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
                                        <h6 class="card-title text-primary"><i class="fas fa-user"></i> Datos del Ciclista</h6>
                                        <hr>
                                        <p><strong>Nombre:</strong> ${data.athlete?.first_name} ${data.athlete?.last_name}</p>
                                        <p><strong>Tipo Documento:</strong> ${data.athlete?.document_type || 'N/A'}</p>
                                        <p><strong>N° Documento:</strong> ${data.athlete?.document_number || 'N/A'}</p>
                                        <p><strong>Nacionalidad:</strong> ${data.athlete?.nationality || 'Venezolana'}</p>
                                        <p><strong>Fecha Nacimiento:</strong> ${data.athlete?.birth_date}</p>
                                        <p><strong>Género:</strong> ${data.athlete?.gender}</p>
                                        <p><strong>Categoría:</strong> <span class="badge bg-primary">${data.athlete?.category}</span></p>
                                        <p><strong>Contacto Emergencia:</strong> ${JSON.parse(data.notes || '{}').emergency_contact || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary"><i class="fas fa-credit-card"></i> Datos de Contacto y Pago</h6>
                                        <hr>
                                        <p><strong>Email:</strong> ${data.email}</p>
                                        <p><strong>Teléfono:</strong> ${data.phone}</p>
                                        <p><strong>Monto:</strong> <span class="fw-bold text-success">$${data.amount}</span></p>
                                        <p><strong>Método Pago:</strong> ${data.payment_method || 'N/A'}</p>
                                        <p><strong>Referencia:</strong> ${data.payment_reference || 'N/A'}</p>
                                        <p><strong>Estructura:</strong> ${JSON.parse(data.notes || '{}').structure_name || 'Independiente'}</p>
                                        <p><strong>Estado:</strong> ${getStatusBadge(data.status)}</p>
                                        <p><strong>Fecha Registro:</strong> ${new Date(data.registered_at).toLocaleString()}</p>
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
                                        <h6 class="card-title text-primary"><i class="fas fa-building"></i> Datos del Equipo</h6>
                                        <hr>
                                        <p><strong>Equipo:</strong> ${data.team?.name}</p>
                                        <p><strong>País:</strong> ${data.team?.country}</p>
                                        <p><strong>Código Acceso:</strong> <code>${data.team?.access_code}</code></p>
                                        <p><strong>Atletas:</strong> ${data.team?.athletes_count || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary"><i class="fas fa-user-tie"></i> Delegado</h6>
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
                                            <p>Haz clic para ver el comprobante completo</p>
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

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge bg-warning">⏳ Pendiente</span>',
                'approved': '<span class="badge bg-success">✅ Aprobada</span>',
                'rejected': '<span class="badge bg-danger">❌ Rechazada</span>',
                'paid': '<span class="badge bg-info">💰 Pagada</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
        }

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

        function deleteRegistration(id) {
            Swal.fire({
                title: '¿Eliminar inscripción?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/registrations/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                Swal.fire('Eliminada', 'Inscripción eliminada correctamente', 'success');
                                location.reload();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Filtros
        document.getElementById('statusFilter')?.addEventListener('change', filterTable);
        document.getElementById('typeFilter')?.addEventListener('change', filterTable);
        document.getElementById('searchInput')?.addEventListener('keyup', filterTable);

        function filterTable() {
            const status = document.getElementById('statusFilter').value;
            const type = document.getElementById('typeFilter').value;
            const search = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#registrationsTable tbody tr');

            rows.forEach(row => {
                let show = true;
                const rowStatus = row.cells[7]?.querySelector('select')?.value;
                const rowType = row.cells[1]?.innerText?.toLowerCase();
                const rowText = row.innerText.toLowerCase();

                if(status && rowStatus !== status) show = false;
                if(type && !rowType?.includes(type)) show = false;
                if(search && !rowText.includes(search)) show = false;

                row.style.display = show ? '' : 'none';
            });
        }
    </script>
@endsection
