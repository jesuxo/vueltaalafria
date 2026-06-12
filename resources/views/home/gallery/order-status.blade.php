{{-- resources/views/home/gallery/order-status.blade.php --}}
@extends('home.layouts.master')

@section('title')
    Estado de mi Pedido - Vuelta a la Fría
@endsection

@section('css')
    <style>
        .order-status-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 14px;
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
            border-left: 4px solid #00ecfe;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .photo-thumb {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .photo-thumb:hover {
            transform: scale(1.05);
        }

        .share-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }

        .qr-code {
            background: white;
            padding: 15px;
            border-radius: 15px;
            display: inline-block;
            margin: 10px 0;
        }

        .copy-link-btn {
            cursor: pointer;
            transition: all 0.3s;
        }

        .copy-link-btn:hover {
            background: #00ecfe;
            color: white;
            border-color: #00ecfe;
        }

        .step-timeline {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }

        .step-timeline::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 10%;
            width: 80%;
            height: 2px;
            background: #ddd;
            z-index: 0;
        }

        .step {
            text-align: center;
            flex: 1;
            position: relative;
            z-index: 1;
            background: white;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            background: #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: white;
        }

        .step.active .step-circle {
            background: #00ecfe;
        }

        .step.completed .step-circle {
            background: #28a745;
        }

        .step-label {
            font-size: 12px;
            color: #666;
        }

        .step.active .step-label {
            color: #00ecfe;
            font-weight: bold;
        }

        .step.completed .step-label {
            color: #28a745;
        }
        .section-title {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <section class="section" style="padding-top: 120px;">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>ESTADO DE MI PEDIDO</h2>
                <p>{{ $order->order_number }}</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Tarjeta principal -->
                    <div class="order-status-card" data-aos="fade-up">
                        <div class="text-center mb-4">
                            <i class="fas fa-shopping-cart fa-3x" style="color: #00ecfe;"></i>
                            <h3 class="mt-3">Pedido #{{ $order->order_number }}</h3>

                            @php
                                $statusClass = 'status-pending';
                                $statusText = 'En lista de espera';
                                if($order->status == 'paid') {
                                    $statusClass = 'status-paid';
                                    $statusText = 'Pago confirmado';
                                } elseif($order->status == 'processing') {
                                    $statusClass = 'status-processing';
                                    $statusText = 'Procesando';
                                } elseif($order->status == 'completed') {
                                    $statusClass = 'status-completed';
                                    $statusText = 'Completado - Fotos listas';
                                } elseif($order->status == 'cancelled') {
                                    $statusClass = 'status-cancelled';
                                    $statusText = 'Cancelado';
                                }
                            @endphp

                            <div class="status-badge {{ $statusClass }} mt-2">
                                <i class="fas {{ $order->status == 'completed' ? 'fa-check-circle' : ($order->status == 'pending' ? 'fa-clock' : 'fa-info-circle') }} me-2"></i>
                                {{ $statusText }}
                            </div>
                        </div>

                        <!-- Timeline de pasos -->
                        <div class="step-timeline">
                            <div class="step {{ $order->status != 'pending' ? 'completed' : 'active' }}">
                                <div class="step-circle">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="step-label">Pedido creado</div>
                                <small>{{ $order->created_at->format('d/m/Y') }}</small>
                            </div>
                            <div class="step {{ $order->status == 'paid' || $order->status == 'processing' || $order->status == 'completed' ? 'completed' : ($order->status == 'pending' ? '' : '') }}">
                                <div class="step-circle">
                                    <i class="fas fa-money-bill"></i>
                                </div>
                                <div class="step-label">Pago confirmado</div>
                                @if($order->paid_at)
                                    <small>{{ $order->paid_at->format('d/m/Y') }}</small>
                                @endif
                            </div>
                            <div class="step {{ $order->status == 'processing' || $order->status == 'completed' ? 'active' : '' }}">
                                <div class="step-circle">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <div class="step-label">Procesando</div>
                            </div>
                            <div class="step {{ $order->status == 'completed' ? 'completed' : '' }}">
                                <div class="step-circle">
                                    <i class="fas fa-download"></i>
                                </div>
                                <div class="step-label">Fotos listas</div>
                                @if($order->delivered_at)
                                    <small>{{ $order->delivered_at->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        </div>

                        <!-- Información del pedido -->
                        <div class="order-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-user me-2"></i> Cliente:</strong> {{ $order->customer_name }}</p>
                                    <p><strong><i class="fas fa-phone me-2"></i> Teléfono:</strong> {{ $order->customer_phone }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-calendar me-2"></i> Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong><i class="fas fa-tag me-2"></i> Código:</strong> <code>{{ $order->public_code }}</code></p>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de fotos -->
                        <h5 class="mb-3"><i class="fas fa-images me-2"></i> Fotos seleccionadas ({{ $order->items->count() }})</h5>
                        <div class="row g-3 mb-4">
                            @foreach($order->items as $item)
                                <div class="col-md-3 col-6">
                                    <div class="photo-thumb">
                                        <img src="/{{ $item->photo->thumbnail_path }}" class="img-fluid rounded" alt="Foto">
                                        <div class="text-center mt-1">
                                            <small class="text-muted">${{ number_format($item->price, 2) }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total -->
                        <div class="text-end">
                            <h4>Total: <span style="color: #00ecfe;">${{ number_format($order->total, 2) }} USD</span></h4>
                        </div>
                    </div>

                    <!-- Sección para compartir -->
                    <div class="share-section" data-aos="fade-up">
                        <h5><i class="fas fa-share-alt me-2"></i> Compartir mi pedido</h5>
                        <p>Comparte este enlace para que otros vean tu pedido o escanea el código QR</p>

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" id="orderLink" class="form-control" value="{{ route('public.order.show', $order->public_code) }}" readonly>
                                    <button class="btn btn-primary copy-link-btn" onclick="copyLink()">
                                        <i class="fas fa-copy"></i> Copiar
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <a href="https://wa.me/?text={{ urlencode('Mira mi pedido de fotos de la Vuelta a la Fría: ' . route('public.order.show', $order->public_code)) }}"
                                       class="btn btn-success me-2" target="_blank">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('public.order.show', $order->public_code)) }}"
                                       class="btn btn-primary" target="_blank">
                                        <i class="fab fa-facebook"></i> Facebook
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="qr-code">
                                    {!! QrCode::size(150)->generate(route('public.order.show', $order->public_code)) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instrucciones según el estado -->
                    @if($order->status == 'pending')
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>¡Pendiente!</strong>   Pedido de fotos en lista de espera
                        </div>
                    @elseif($order->status == 'paid')
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Pago confirmado.</strong> Estamos procesando tus fotos.
                        </div>
                    @elseif($order->status == 'completed')
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-download me-2"></i>
                            <strong>¡Tus fotos están listas!</strong>
                            <a href="{{ route('public.order.download', $order->public_code) }}" class="alert-link">Haz clic aquí para descargarlas</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function copyLink() {
            const linkInput = document.getElementById('orderLink');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            document.execCommand('copy');

            Swal.fire({
                icon: 'success',
                title: '¡Enlace copiado!',
                text: 'El enlace de tu pedido ha sido copiado al portapapeles',
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    </script>
@endsection
