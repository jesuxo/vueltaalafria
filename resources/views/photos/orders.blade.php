{{-- resources/views/admin/photos/orders.blade.php --}}
@extends('layouts.master')

@section('title')
    Pedidos de Fotos - Vuelta a la Fría
@endsection

@section('css')
    <style>
        .status-pending {
            background: #fff3cd;
            color: #856404;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        .status-paid {
            background: #d1ecf1;
            color: #0c5460;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        .status-processing {
            background: #cce5ff;
            color: #004085;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        .order-card {
            transition: all 0.3s;
            cursor: pointer;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-shopping-cart me-2"></i> Pedidos de Fotos</h3>
                        <p class="text-muted mb-0">Gestiona los pedidos de fotos realizados por los clientes</p>
                    </div>
                    <div class="card-body">
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
                                        <td>
                                            <strong>{{ $order->order_number }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $order->public_code }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $order->customer_name }}</strong>
                                            <br>
                                            <small>{{ $order->customer_phone }}</small>
                                            @if($order->customer_email)
                                                <br>
                                                <small>{{ $order->customer_email }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $order->items->count() }} fotos</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>${{ number_format($order->total, 2) }} USD</strong>
                                        </td>
                                        <td>
                                            @if($order->payment_method == 'transferencia')
                                                <span class="badge bg-info">Transferencia</span>
                                            @elseif($order->payment_method == 'bancolombia')
                                                <span class="badge bg-primary">Bancolombia</span>
                                            @elseif($order->payment_method == 'usdt')
                                                <span class="badge bg-success">USDT</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $order->payment_method }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'status-pending';
                                                $statusText = 'Pendiente';
                                                if($order->status == 'paid') {
                                                    $statusClass = 'status-paid';
                                                    $statusText = 'Pagado';
                                                } elseif($order->status == 'processing') {
                                                    $statusClass = 'status-processing';
                                                    $statusText = 'Procesando';
                                                } elseif($order->status == 'completed') {
                                                    $statusClass = 'status-completed';
                                                    $statusText = 'Completado';
                                                } elseif($order->status == 'cancelled') {
                                                    $statusClass = 'status-cancelled';
                                                    $statusText = 'Cancelado';
                                                }
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="viewOrder({{ $order->id }})" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="updateStatus({{ $order->id }})" title="Cambiar estado">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                                            <p>No hay pedidos de fotos aún.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles del pedido -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-shopping-cart me-2"></i> Detalle del Pedido
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderModalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar estado -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="statusOrderId">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select id="orderStatus" class="form-select">
                            <option value="pending">Pendiente de pago</option>
                            <option value="paid">Pago confirmado</option>
                            <option value="processing">Procesando</option>
                            <option value="completed">Completado (fotos listas)</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveStatus()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function viewOrder(id) {
            fetch(`/admin/photos/order/${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('orderModalContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('orderModal')).show();
                })
                .catch(error => {
                    Swal.fire('Error', 'No se pudo cargar el detalle del pedido', 'error');
                });
        }

        let currentOrderId = null;

        function updateStatus(id) {
            currentOrderId = id;
            document.getElementById('statusOrderId').value = id;
            document.getElementById('orderStatus').value = '';
            new bootstrap.Modal(document.getElementById('statusModal')).show();
        }

        function saveStatus() {
            const id = document.getElementById('statusOrderId').value;
            const status = document.getElementById('orderStatus').value;

            if (!status) {
                Swal.fire('Error', 'Selecciona un estado', 'error');
                return;
            }

            fetch(`/admin/photos/order/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Éxito', data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Error de conexión', 'error');
                });
        }
    </script>
@endsection
