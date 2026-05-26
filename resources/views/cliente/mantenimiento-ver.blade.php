{{-- resources/views/cliente/mantenimiento-ver.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Mantenimiento - {{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --pastel-blue: #e6f3ff;
            --pastel-green: #e1f7e6;
            --pastel-yellow: #fff9e6;
            --pastel-pink: #ffe6f0;
            --pastel-purple: #f0e6ff;
            --pastel-peach: #ffe6d9;
        }

        body {
            background-color: #faf7f5;
            font-family: 'Arial', sans-serif;
            padding: 20px 0;
        }

        .container-cliente {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header-card {
            background: linear-gradient(135deg, var(--pastel-purple), var(--pastel-blue));
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            color: #2c3e50;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .header-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .header-card h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }

        .vehiculo-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 5px solid var(--pastel-green);
            transition: all 0.3s;
        }

        .vehiculo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .mantenimiento-destacado {
            background: var(--pastel-yellow);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 3px solid #c1e0cd;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
        }

        .mantenimiento-destacado::after {
            content: '✨ RECIENTE';
            position: absolute;
            top: -10px;
            right: 20px;
            background: #c1e0cd;
            color: #2c3e50;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .tipo-badge {
            background: var(--pastel-blue);
            color: #2c3e50;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 0.9rem;
            display: inline-block;
            margin: 3px;
            font-weight: 500;
            border: 1px solid #9fb7c9;
        }

        .historial-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--pastel-purple);
            transition: all 0.2s;
        }

        .historial-item:hover {
            background: white;
            transform: translateX(5px);
        }

        .foto-evidencia {
            border-radius: 10px;
            overflow: hidden;
            height: 150px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
        }

        .foto-evidencia:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .foto-evidencia img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .foto-evidencia .tipo-foto {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 2px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
        }

        .proximo-badge {
            background: var(--pastel-peach);
            color: #2c3e50;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            border-left: 4px solid #f1be46;
        }

        .btn-whatsapp {
            background: #25D366;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-whatsapp:hover {
            background: #128C7E;
            color: white;
            transform: scale(1.05);
        }

        .footer-note {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 0.85rem;
        }

        .producto-item {
            background: var(--pastel-blue);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 5px;
            border-left: 3px solid #0072c5;
        }

        .otros-vehiculo-item {
            background: white;
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e0e0e0;
            transition: all 0.2s;
        }

        .otros-vehiculo-item:hover {
            background: var(--pastel-blue);
            border-color: #0072c5;
        }

        @media (max-width: 768px) {
            .header-card h1 {
                font-size: 1.3rem;
            }
            .mantenimiento-destacado::after {
                font-size: 0.7rem;
                padding: 3px 10px;
            }
        }
    </style>
</head>
<body>
<div class="container-cliente">
    <!-- Header con datos del cliente -->
    <div class="header-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>
                    <i class="ri-user-line"></i>
                    {{ $mantenimiento->vehiculo->cliente->descrip }}
                </h1>
                <p class="mb-0 opacity-75">
                    <i class="ri-mail-line"></i> {{ $mantenimiento->vehiculo->cliente->email ?? 'No registrado' }} |
                    <i class="ri-phone-line"></i> {{ $mantenimiento->vehiculo->cliente->telef ?? $mantenimiento->vehiculo->cliente->movil ?? 'No registrado' }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <img src="{{ asset('build/images/logo-light.png') }}" alt="Logo" height="50" style="opacity: 0.8;">
            </div>
        </div>
    </div>

    <!-- Vehículo actual -->
    <div class="vehiculo-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="ri-car-line"></i> {{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}
                </h4>
                <p class="text-muted mb-0">
                    <span class="badge bg-secondary">{{ $mantenimiento->vehiculo->identificacion }}</span>
                    @if($mantenimiento->vehiculo->year)
                        <span class="ms-2"><i class="ri-calendar-line"></i> {{ $mantenimiento->vehiculo->year }}</span>
                    @endif
                    <span class="ms-2"><i class="ri-caravan-line"></i> {{ $mantenimiento->vehiculo->tipo->tipo ?? 'N/A' }}</span>
                </p>
            </div>
            <div>
                    <span class="badge bg-info p-2">
                        <i class="ri-tools-line"></i> {{ $historial->count() }} mantenimientos
                    </span>
            </div>
        </div>
    </div>

    <!-- Mantenimiento destacado (el actual) -->
    <div class="mantenimiento-destacado">
        <h5 class="mb-3">
            <i class="ri-star-fill" style="color: gold;"></i>
            Mantenimiento del {{ $mantenimiento->fechaformat }}
        </h5>

        <div class="row">
            <div class="col-md-8">
                <p><strong><i class="ri-tools-line"></i> Servicios realizados:</strong></p>
                <div class="mb-3">
                    @if($mantenimiento->tipo_mantenimiento == 'multiple' && $mantenimiento->tipos)
                        @foreach($mantenimiento->tipos as $tipo)
                            <span class="tipo-badge">
                                    @switch($tipo->tipo)
                                    @case('cambio_aceite') 🔧 Cambio de Aceite @break
                                    @case('cambio_filtro_aceite') 🔧 Cambio de Filtro de Aceite @break
                                    @case('cambio_filtro_gasolina') 🔧 Cambio de Filtro de Gasolina @break
                                    @case('cambio_filtro_aire') 🔧 Cambio de Filtro de Aire @break
                                    @case('mantenimiento_inyectores') 🔧 Mantenimiento de Inyectores @break
                                    @case('bateria') 🔋 Batería @break
                                    @default 📌 {{ $tipo->descripcion ?? $tipo->tipo }}
                                @endswitch
                                </span>
                        @endforeach
                    @else
                        <span class="tipo-badge">
                                @switch($mantenimiento->tipo_mantenimiento)
                                @case('cambio_aceite') 🔧 Cambio de Aceite @break
                                @case('cambio_filtro_aceite') 🔧 Cambio de Filtro de Aceite @break
                                @case('cambio_filtro_gasolina') 🔧 Cambio de Filtro de Gasolina @break
                                @case('cambio_filtro_aire') 🔧 Cambio de Filtro de Aire @break
                                @case('mantenimiento_inyectores') 🔧 Mantenimiento de Inyectores @break
                                @case('bateria') 🔋 Batería @break
                                @default 📌 {{ $mantenimiento->tipo_mantenimiento }}
                            @endswitch
                            </span>
                    @endif
                </div>

                @if($mantenimiento->productos && $mantenimiento->productos->count() > 0)
                    <p><strong><i class="ri-shopping-cart-line"></i> Productos utilizados:</strong></p>
                    <div class="mb-3">
                        @foreach($mantenimiento->productos as $producto)
                            <div class="producto-item">
                                <strong>{{ $producto->descripcion }}</strong>
                                @if($producto->referencia)
                                    <small class="text-muted">(Ref: {{ $producto->referencia }})</small>
                                @endif
                                <span class="badge bg-primary float-end">x{{ $producto->cantidad }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <p class="mb-1"><strong><i class="ri-speed-line"></i> Kilometraje:</strong></p>
                        <h5>{{ number_format($mantenimiento->kilometraje, 0, ',', '.') }} km</h5>

                        <p class="mb-1 mt-3"><strong><i class="ri-user-star-line"></i> Atendido por:</strong></p>
                        <p>{{ $mantenimiento->vendedor->descrip ?? 'No especificado' }}</p>

                        @if($mantenimiento->hora_mantenimiento)
                            <p class="mb-0"><small><i class="ri-time-line"></i> Hora: {{ $mantenimiento->horaformated }}</small></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($mantenimiento->observaciones)
            <div class="mt-3 p-3" style="background: var(--pastel-pink); border-radius: 10px;">
                <strong><i class="ri-chat-1-line"></i> Observaciones:</strong>
                <p class="mb-0 mt-1">{{ $mantenimiento->observaciones }}</p>
            </div>
        @endif

        @if($mantenimiento->proximo_mantenimiento || $mantenimiento->proximo_kilometraje)
            <div class="mt-3 proximo-badge">
                <i class="ri-calendar-check-line"></i>
                <strong>Próximo mantenimiento sugerido:</strong><br>
                @if($mantenimiento->proximo_mantenimiento)
                    📅 {{ \Carbon\Carbon::parse($mantenimiento->proximo_mantenimiento)->format('d/m/Y') }}
                @endif
                @if($mantenimiento->proximo_kilometraje)
                    @if($mantenimiento->proximo_mantenimiento) o @endif
                    🔧 {{ number_format($mantenimiento->proximo_kilometraje, 0, ',', '.') }} km
                @endif
            </div>
        @endif
    </div>

    <!-- Fotos de evidencia - CORREGIDO -->
    @if($mantenimiento->fotos && $mantenimiento->fotos->count() > 0)
        <div class="vehiculo-card">
            <h5 class="mb-3">
                <i class="ri-camera-line"></i>
                Fotos del Servicio ({{ $mantenimiento->fotos->count() }})
            </h5>
            <div class="row g-3">
                @foreach($mantenimiento->fotos as $index => $foto)
                    @php
                        // Limpiar la ruta (eliminar 'public/' si existe)
                        $rutaLimpia = $foto->ruta_foto;
                        if (str_starts_with($rutaLimpia, 'public/')) {
                            $rutaLimpia = substr($rutaLimpia, 7);
                        }
                        // Construir URL directa con asset() (sin storage)
                        $urlFoto = asset($rutaLimpia);
                        // Agregar timestamp para evitar caché
                        $timestamp = time() . '_' . $index;
                        $urlFotoConCache = $urlFoto . '?v=' . $timestamp;
                    @endphp
                    <div class="col-md-3 col-6">
                        <div class="foto-evidencia" onclick="verFoto('{{ $urlFoto }}')">
                            <img src="{{ $urlFotoConCache }}" alt="Evidencia"
                                 onerror="this.onerror=null; this.src='{{ asset('build/images/logo-light.png') }}'">
                            <span class="tipo-foto">
                                @if($foto->tipo_evidencia == 'cambio_aceite') 🔧 Cambio Aceite
                                @elseif($foto->tipo_evidencia == 'producto_usado') 🛒 Producto
                                @elseif($foto->tipo_evidencia == 'kilometraje') 📊 Kilometraje
                                @else 📷 General
                                @endif
                            </span>
                        </div>
                        @if($foto->descripcion)
                            <small class="text-muted d-block text-center mt-1">{{ $foto->descripcion }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Historial de mantenimientos del mismo vehículo -->
    @if($historial->count() > 1)
        <div class="vehiculo-card">
            <h5 class="mb-3"><i class="ri-history-line"></i> Historial de Mantenimientos</h5>
            @foreach($historial as $h)
                @if($h->id != $mantenimiento->id)
                    <div class="historial-item">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>{{ $h->fechaformat }}</strong>
                            </div>
                            <div class="col-md-3">
                                {{ number_format($h->kilometraje, 0, ',', '.') }} km
                            </div>
                            <div class="col-md-4">
                                @if($h->tipo_mantenimiento == 'multiple' && $h->tipos)
                                    @foreach($h->tipos->take(2) as $tipo)
                                        <span class="badge bg-secondary">{{ $tipo->tipo_texto ?? $tipo->tipo }}</span>
                                    @endforeach
                                    @if($h->tipos->count() > 2)
                                        <span class="badge bg-secondary">+{{ $h->tipos->count() - 2 }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">{{ $h->tipo_mantenimiento }}</span>
                                @endif
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="{{ route('cliente.mantenimiento.ver', $h->token_cliente ?? '#') }}"
                                   class="btn btn-sm btn-outline-primary"
                                    {{ !$h->token_cliente ? 'disabled' : '' }}>
                                    Ver
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Otros vehículos del cliente -->
    @if($otrosVehiculos->count() > 0)
        <div class="vehiculo-card">
            <h5 class="mb-3"><i class="ri-car-line"></i> Otros Vehículos</h5>
            <div class="row">
                @foreach($otrosVehiculos as $vehiculo)
                    <div class="col-md-6 mb-2">
                        <div class="otros-vehiculo-item">
                            <strong>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</strong>
                            <span class="badge bg-secondary float-end">{{ $vehiculo->identificacion }}</span>
                            <p class="text-muted small mb-0 mt-1">
                                @if($vehiculo->mantenimientos->count() > 0)
                                    <i class="ri-history-line"></i>
                                    Último: {{ $vehiculo->mantenimientos->first()->fechaformat }}
                                @else
                                    Sin mantenimientos
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Botón para compartir -->
    <div class="text-center mt-4">
        <button class="btn btn-whatsapp" onclick="compartirWhatsApp()">
            <i class="ri-whatsapp-line"></i> Compartir este mantenimiento
        </button>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <p>
            <i class="ri-lock-line"></i> Este es un enlace seguro y temporal para visualizar el mantenimiento de tu vehículo.
        </p>
        <p class="small">
            © {{ date('Y') }} - Sistema de Gestión de Mantenimientos
        </p>
    </div>
</div>

<!-- Modal para ver foto grande -->
<div class="modal fade" id="fotoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img src="" id="fotoGrande" style="max-width: 100%; max-height: 80vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function verFoto(url) {
        // Limpiar la URL de parámetros anteriores
        var cleanUrl = url.split('?')[0];
        var urlConTimestamp = cleanUrl + '?v=' + new Date().getTime();
        $('#fotoGrande').attr('src', urlConTimestamp);
        $('#fotoModal').modal('show');
    }

    function compartirWhatsApp() {
        let url = window.location.href;
        let mensaje = encodeURIComponent(`¡Hola! 👋\n\nTe comparto el detalle del mantenimiento de mi vehículo:\n\n${url}\n\n¡Gracias! 🚗`);
        window.open(`https://wa.me/?text=${mensaje}`, '_blank');
    }

    // Forzar recarga de imágenes después de cargar la página
    $(document).ready(function() {
        $('.foto-evidencia img').each(function() {
            var $img = $(this);
            var src = $img.attr('src');
            if (src && !src.includes('logo-light.png')) {
                var cleanSrc = src.split('?')[0];
                var newSrc = cleanSrc + '?v=' + new Date().getTime() + '_' + Math.random();
                $img.attr('src', newSrc);
            }
        });
    });
</script>
</body>
</html>
