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
            cursor: pointer;
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

        .download-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .download-btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .download-all-btn {
            background: #00ecfe;
            color: #000;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            margin: 10px 0;
        }

        .download-all-btn:hover {
            background: #00c4d4;
            transform: translateY(-2px);
            color: #000;
        }

        .single-download-btn {
            background: #007bff;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
            margin-top: 8px;
        }

        .single-download-btn:hover {
            background: #0056b3;
            color: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .navbar {
            background: rgba(0,0,0,0.9) !important;
        }

        /* Modal para ver foto */
        .photo-modal-img {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 10px;
        }

        .modal-content {
            border-radius: 20px;
            overflow: hidden;
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
                                    @if($order->customer_email)
                                        <p><strong><i class="fas fa-envelope me-2"></i> Email:</strong> {{ $order->customer_email }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-calendar me-2"></i> Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong><i class="fas fa-tag me-2"></i> Código:</strong> <code>{{ $order->public_code }}</code></p>
                                    @if($order->payment_method)
                                        <p><strong><i class="fas fa-credit-card me-2"></i> Método de pago:</strong> {{ ucfirst($order->payment_method) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Botón de descarga masiva (solo si está completado) -->
                        @if($order->status == 'completed')
                            <div class="text-center mb-4">
                                <a href="{{ route('photo.download.all', $order->public_code) }}" class="download-all-btn">
                                    <i class="fas fa-download"></i> Descargar todas las fotos (ZIP)
                                </a>
                                <p class="text-muted small mt-2">
                                    <i class="fas fa-info-circle"></i> Las fotos se descargarán en un archivo comprimido ZIP
                                </p>
                            </div>
                        @endif

                        <!-- Lista de fotos -->
                        <h5 class="mb-3"><i class="fas fa-images me-2"></i> Fotos seleccionadas ({{ $order->items->count() }})</h5>
                        <div class="row g-3 mb-4">
                            @foreach($order->items as $item)
                                <div class="col-md-3 col-6">
                                    <div class="photo-thumb" onclick="showPhotoModal({{ $item->photo->id }}, '{{ $item->photo->preview_path ?? $item->photo->thumbnail_path }}', '{{ $item->photo->price }}')">
                                        <img src="/{{ $item->photo->thumbnail_path }}" class="img-fluid rounded" alt="Foto" style="width: 100%; height: 150px; object-fit: cover;">
                                        <div class="text-center mt-1">
                                            <small class="text-muted">${{ number_format($item->price, 2) }}</small>
                                        </div>
                                        @if($order->status == 'completed')
                                            <div class="text-center mt-1">
                                                <a href="{{ route('photo.download', ['photoId' => $item->photo->id, 'code' => $order->public_code]) }}"
                                                   class="single-download-btn">
                                                    <i class="fas fa-download"></i> Descargar
                                                </a>
                                            </div>
                                        @endif
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
                            <strong>¡Pendiente!</strong> Pedido de fotos en lista de espera.
                            <br><small>Una vez confirmado el pago, comenzaremos a procesar tus fotos.</small>
                        </div>
                    @elseif($order->status == 'paid')
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Pago confirmado.</strong> Estamos procesando tus fotos.
                            <br><small>En breve recibirás un aviso cuando estén listas para descargar.</small>
                        </div>
                    @elseif($order->status == 'processing')
                        <div class="alert alert-primary mt-3">
                            <i class="fas fa-cogs me-2"></i>
                            <strong>Fotos en proceso.</strong> Estamos preparando tus fotos.
                            <br><small>Pronto estarán disponibles para descarga.</small>
                        </div>
                    @elseif($order->status == 'completed')
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-download me-2"></i>
                            <strong>¡Tus fotos están listas!</strong>
                            <br>Puedes descargarlas individualmente o todas juntas en un archivo ZIP.
                        </div>
                    @elseif($order->status == 'cancelled')
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-ban me-2"></i>
                            <strong>Pedido cancelado.</strong>
                            <br>Si tienes alguna duda, contacta con el organizador.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para ver foto en grande -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-camera me-2"></i> Foto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhotoImage" src="" alt="Foto" class="photo-modal-img">
                    <div class="mt-3">
                        <p><strong><i class="fas fa-dollar-sign me-2"></i> Precio:</strong> $<span id="modalPhotoPrice">5</span> USD</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
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

        function showPhotoModal(photoId, imageUrl, price) {
            const modalImage = document.getElementById('modalPhotoImage');
            const modalPrice = document.getElementById('modalPhotoPrice');

            if (modalImage) {
                modalImage.src = '/' + imageUrl;
            }
            if (modalPrice) {
                modalPrice.innerText = price || 5;
            }

            new bootstrap.Modal(document.getElementById('photoModal')).show();
        }
    </script>
@endsection
