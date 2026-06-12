{{-- resources/views/admin/specials/index.blade.php --}}
@extends('layouts.master')

@section('title')
    Momentos Especiales - Vuelta a la Fría
@endsection

@section('css')
    <style>
        .special-icon {
            font-size: 24px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .btn-action {
            margin: 0 2px;
        }
        .status-badge-active {
            background: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .status-badge-inactive {
            background: #f8d7da;
            color: #721c24;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-star me-2"></i> Momentos Especiales</h3>
                        <p class="text-muted mb-0">Gestiona los eventos especiales para fotos (previas, premiaciones, detrás de cámaras, etc.)</p>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.specials.create') }}" class="btn btn-primary mb-3">
                            <i class="fas fa-plus me-2"></i> Nuevo Momento Especial
                        </a>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th width="80">Icono</th>
                                    <th>Nombre</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th width="100">Estado</th>
                                    <th width="120">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($specials as $special)
                                    <tr id="special-row-{{ $special->id }}">
                                        <td class="text-center">
                                            <div class="special-icon">
                                                <i class="{{ $special->icon ?? 'fas fa-camera' }}"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $special->name }}</strong>
                                            <br>
                                            <small class="text-muted">ID: {{ $special->id }}</small>
                                        </td>
                                        <td>
                                            @if($special->date)
                                                {{ \Carbon\Carbon::parse($special->date)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ Str::limit($special->description, 50) ?: '—' }}
                                        </td>
                                        <td class="text-center">
                                            @if($special->is_active)
                                                <span class="status-badge-active">
                                                        <i class="fas fa-check-circle me-1"></i> Activo
                                                    </span>
                                            @else
                                                <span class="status-badge-inactive">
                                                        <i class="fas fa-times-circle me-1"></i> Inactivo
                                                    </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.specials.edit', $special->id) }}" class="btn btn-sm btn-warning btn-action" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger btn-action" onclick="deleteSpecial({{ $special->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary btn-action" onclick="toggleStatus({{ $special->id }})" title="Cambiar estado">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-camera fa-3x text-muted mb-3 d-block"></i>
                                            <p>No hay momentos especiales creados aún.</p>
                                            <a href="{{ route('admin.specials.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i> Crear primer momento especial
                                            </a>
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
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteSpecial(id) {
            Swal.fire({
                title: '¿Eliminar este momento especial?',
                text: 'Las fotos asociadas a este evento NO se eliminarán, pero quedarán sin categoría',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/specials/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Eliminado', data.message, 'success');
                                document.getElementById(`special-row-${id}`).remove();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Error de conexión', 'error');
                        });
                }
            });
        }

        function toggleStatus(id) {
            fetch(`/admin/specials/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(() => location.reload(), 1000);
                    }
                });
        }
    </script>
@endsection
