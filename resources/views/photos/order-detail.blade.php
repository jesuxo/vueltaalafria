{{-- resources/views/admin/photos/order-detail.blade.php --}}
<div class="container-fluid p-0">
    <!-- Encabezado del pedido -->
    <div class="row mb-3">
        <div class="col-md-6">
            <h5><i class="fas fa-receipt me-2"></i> Pedido #{{ $order->order_number }}</h5>
            <p class="text-muted mb-0">Código público: <strong>{{ $order->public_code }}</strong></p>
            <p class="text-muted">Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="col-md-6 text-end">
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
        </div>
    </div>

    <hr>

    <!-- Información del cliente -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h6><i class="fas fa-user me-2"></i> Información del Cliente</h6>
            <div class="order-info p-3">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Nombre:</strong> {{ $order->customer_name }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Teléfono:</strong> {{ $order->customer_phone }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Email:</strong> {{ $order->customer_email ?? 'No especificado' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de pago -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h6><i class="fas fa-credit-card me-2"></i> Información de Pago</h6>
            <div class="order-info p-3">
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
                            <i class="fas fa-eye me-1"></i> Ver comprobante
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lista de fotos -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h6><i class="fas fa-images me-2"></i> Fotos solicitadas ({{ $order->items->count() }})</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>Etapa/Evento</th>
                        <th>Precio</th>
                        <th>Descarga</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <img src="/{{ $item->photo->thumbnail_path }}" style="width: 50px; height: 40px; object-fit: cover; border-radius: 5px;">
                                <br>
                                <small class="text-muted">ID: {{ $item->photo->id }}</small>
                            </td>
                            <td>{{ $item->photo->stage->name ?? 'N/A' }}</td>
                            <td>${{ number_format($item->price, 2) }} USD</td>
                            <td>
                                <a href="{{ route('photo.download', ['photoId' => $item->photo->id, 'code' => $order->public_code]) }}"
                                   class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="table-light">
                        <td colspan="2" class="text-end"><strong>Subtotal:</strong></td>
                        <td colspan="2"><strong>${{ number_format($order->subtotal, 2) }} USD</strong></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="2" class="text-end"><strong>Total:</strong></td>
                        <td colspan="2"><strong class="text-success">${{ number_format($order->total, 2) }} USD</strong></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Fechas importantes -->
    <div class="row">
        <div class="col-md-12">
            <div class="order-info p-3">
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
</div>

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
        border-left: 4px solid #00ecfe;
    }
</style>
