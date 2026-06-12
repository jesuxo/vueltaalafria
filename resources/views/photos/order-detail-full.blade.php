{{-- resources/views/admin/photos/order-detail-full.blade.php --}}
@extends('layouts.master')

@section('title')
    Detalle del Pedido - Vuelta a la Fría
@endsection

@section('css')
    <style>
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 13px;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-paid {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-processing {
            background: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .order-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid #00ecfe;
            margin-bottom: 20px;
        }
        .photo-thumb {
            width: 60px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .btn-back {
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3><i class="fas fa-shopping-cart me-2"></i> Detalle del Pedido</h3>
                            <a href="{{ route('admin.photos.orders') }}" class="btn btn-secondary btn-back">
                                <i class="fas fa-arrow-left me-2"></i> Volver a la lista
                            </a>
                        </div>
                        <p class="text-muted mb-0">Pedido #{{ $order->order_number }}</p>
                    </div>
                    <div class="card-body">
                        <!-- Encabezado del pedido -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5><i class="fas fa-receipt me-2"></i> Información General</h5>
                                <div class="order-info">
                                    <p><strong>Número de pedido:</strong> {{ $order->order_number }}</p>
                                    <p><strong>Código público:</strong> <code>{{ $order->public_code }}</code></p>
                                    <p><strong>Fecha de creación:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Estado:</strong>
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
                                        <span class="status-badge {{ $statusClass }}">
                                            <i class="fas {{ $order->status == 'completed' ? 'fa-check-circle' : ($order->status == 'pending' ? 'fa-clock' : 'fa-info-circle') }} me-1"></i>
                                            {{ $statusText }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-user me-2"></i> Información del Cliente</h5>
                                <div class="order-info">
                                    <p><strong>Nombre:</strong> {{ $order->customer_name }}</p>
                                    <p><strong>Teléfono:</strong> {{ $order->customer_phone }}</p>
                                    <p><strong>Email:</strong> {{ $order->customer_email ?? 'No especificado' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información de pago -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5><i class="fas fa-credit-card me-2"></i> Información de Pago</h5>
                                <div class="order-info">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Método de pago:</strong>
                                                @if($order->payment_method == 'transferencia')
                                                    Transferencia Bancaria
                                                @elseif($order->payment_method == 'bancolombia')
                                                    Bancolombia (COP)
                                                @elseif($order->payment_method == 'usdt')
                                                    USDT (Cripto)
                                                @else
                                                    {{ $order->payment_method }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Referencia:</strong> {{ $order->payment_reference ?? 'No especificada' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Total:</strong> <strong class="text-success">${{ number_format($order->total, 2) }} USD</strong></p>
                                        </div>
                                    </div>
                                    @if($order->payment_proof)
                                        <div class="mt-2">
                                            <a href="/{{ $order->payment_proof }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i> Ver comprobante de pago
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Fechas importantes -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5><i class="fas fa-calendar-alt me-2"></i> Fechas Importantes</h5>
                                <div class="order-info">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong><i class="fas fa-calendar-plus me-1"></i> Creado:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        @if($order->paid_at)
                                            <div class="col-md-4">
                                                <p><strong><i class="fas fa-check-circle me-1 text-success"></i> Pagado:</strong> {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        @endif
                                        @if($order->delivered_at)
                                            <div class="col-md-4">
                                                <p><strong><i class="fas fa-download me-1 text-primary"></i> Entregado:</strong> {{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de fotos -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5><i class="fas fa-images me-2"></i> Fotos solicitadas ({{ $order->items->count() }})</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                        <tr>
                                            <th width="80">Foto</th>
                                            <th>ID Foto</th>
                                            <th>Etapa/Evento</th>
                                            <th>Precio</th>
                                            <th>Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    <img src="/{{ $item->photo->thumbnail_path }}" class="photo-thumb" alt="Foto">
                                                </td>
                                                <td>#{{ $item->photo->id }}</td>
                                                <td>{{ $item->photo->stage->name ?? 'N/A' }}</td>
                                                <td>${{ number_format($item->price, 2) }} USD</td>
                                                <td>
                                                    <a href="{{ route('photo.download', ['photoId' => $item->photo->id, 'code' => $order->public_code]) }}"
                                                       class="btn btn-sm btn-primary" target="_blank">
                                                        <i class="fas fa-download"></i> Descargar
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr class="table-light">
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td colspan="2"><strong>${{ number_format($order->subtotal, 2) }} USD</strong></td>
                                        </tr>
                                        <tr class="table-light">
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td colspan="2"><strong class="text-success">${{ number_format($order->total, 2) }} USD</strong></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('photo.download.all', $order->public_code) }}" class="btn btn-success" target="_blank">
                                    <i class="fas fa-download me-2"></i> Descargar todas las fotos (ZIP)
                                </a>
                                <a href="{{ route('admin.photos.orders') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-arrow-left me-2"></i> Volver
                                </a>
                            </div>
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
        // Función para actualizar el estado (opcional)
        function updateOrderStatus(orderId, status) {
            fetch(`/admin/photos/order/${orderId}/status`, {
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
