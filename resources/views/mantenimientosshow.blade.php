{{-- resources/views/mantenimientosshow.blade.php --}}
@extends('layouts.master')
@section('title')
    Detalle del Mantenimiento #{{ str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT) }}
@endsection
@section('css')
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <style>
        :root {
            --pastel-blue: #e6f3ff;
            --pastel-green: #e1f7e6;
            --pastel-yellow: #fff9e6;
            --pastel-pink: #ffe6f0;
            --pastel-purple: #f0e6ff;
            --pastel-peach: #ffe6d9;
        }

        .detail-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 20px;
        }

        .detail-header {
            background: var(--pastel-purple);
            border-radius: 15px 15px 0 0;
            padding: 15px 20px;
            border-bottom: none;
        }

        .info-section {
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .producto-item {
            background: var(--pastel-blue);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            border-left: 4px solid #9fb7c9;
        }

        .producto-item:hover {
            background: #d4e8ff;
        }

        .badge-tipo {
            background: var(--pastel-green);
            color: #2c3e50;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin: 2px;
        }

        .table-productos {
            border-radius: 10px;
            overflow: hidden;
        }

        .table-productos thead {
            background: var(--pastel-purple);
        }

        .table-productos th {
            border-bottom: none;
            padding: 12px;
        }

        .table-productos td {
            padding: 12px;
            vertical-align: middle;
        }

        .total-section {
            background: var(--pastel-green);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        .btn-pastel {
            background: #c5d9e8;
            border: none;
            color: #2c3e50;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-pastel:hover {
            background: #b3c9da;
            color: #2c3e50;
        }

        .btn-whatsapp {
            background: #25D366;
            color: white;
            border: none;
        }

        .btn-whatsapp:hover {
            background: #128C7E;
            color: white;
        }

        /* Estilos para la galería de fotos */
        .foto-evidencia {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            height: 200px;
        }

        .foto-evidencia:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .foto-evidencia img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .foto-evidencia .tipo-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,255,255,0.9);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #2c3e50;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .foto-evidencia .descripcion-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px;
            font-size: 0.85rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .foto-evidencia:hover .descripcion-overlay {
            transform: translateY(0);
        }

        .galeria-fotos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .foto-placeholder {
            background: var(--pastel-yellow);
            border: 2px dashed var(--pastel-purple);
            border-radius: 10px;
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
        }

        .foto-placeholder i {
            font-size: 3rem;
            color: #b8d9c6;
            margin-bottom: 10px;
        }

        /* Modal para ver foto grande */
        .modal-fullscreen {
            max-width: 90vw;
            max-height: 90vh;
        }

        .modal-fullscreen img {
            width: 100%;
            height: auto;
            max-height: 85vh;
            object-fit: contain;
        }

        /* Estilos para el enlace de cliente */
        .enlace-cliente {
            background: var(--pastel-blue);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #9fb7c9;
        }

        .enlace-cliente .btn-copiar {
            background: white;
            border: 1px solid #9fb7c9;
            color: #2c3e50;
        }

        .enlace-cliente .btn-copiar:hover {
            background: var(--pastel-purple);
        }
        .foto-evidencia img {
            image-orientation: from-image; /* Esto respeta la orientación EXIF */
        }

        .modal-fullscreen {
            padding: 0 !important;
        }

        .modal-fullscreen .modal-content {
            height: 100vh;
            border-radius: 0;
        }

        .btn-close-white {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
        }

        .btn-close-white:active {
            transform: scale(0.95);
            opacity: 1;
        }

        @media (max-width: 768px) {
            .btn-close-white {
                width: 44px;
                height: 44px;
                background-size: 28px;
            }

            .position-absolute.bottom-0 .btn-light {
                padding: 12px 30px;
                font-size: 1.1rem;
                margin-bottom: 20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="loading-overlay" id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 240, 230, 0.7); z-index: 9999; backdrop-filter: blur(3px); justify-content: center; align-items: center;">
        <div class="spinner-border text-secondary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Tarjeta principal -->
            <div class="detail-card">
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="ri-file-copy-line"></i>
                                Mantenimiento #{{ str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT) }}
                            </h4>
                        </div>
                        <div>
                            <span class="badge-tipo">
                                <i class="ri-calendar-line"></i>
                                {{ $mantenimiento->fechaformat }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body pb-2">
                    <!-- Información del Cliente y Vehículo -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section" style="background: var(--pastel-pink);">
                                <h5 class="mb-3"><i class="ri-user-line"></i> Datos del Cliente</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="120"><strong>Nombre:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->cliente->descrip }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cédula/RIF:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->cliente->id3 }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Teléfono:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->cliente->telef ?? $mantenimiento->vehiculo->cliente->movil ?? 'N/A' }}</td>
                                    </tr>
                                    @if($mantenimiento->vehiculo->cliente->email)
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $mantenimiento->vehiculo->cliente->email }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section" style="background: var(--pastel-green);">
                                <h5 class="mb-3"><i class="ri-car-line"></i> Datos del Vehículo</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="120"><strong>Placa:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->identificacion }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Marca:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->marca }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Modelo:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->modelo }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Año:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->year ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipo:</strong></td>
                                        <td>{{ $mantenimiento->vehiculo->tipo->tipo ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Mantenimiento (ACTUALIZADA con múltiples tipos) -->
                    <div class="info-section" style="background: var(--pastel-yellow);">
                        <h5 class="mb-3"><i class="ri-tools-line"></i> Detalles del Mantenimiento</h5>
                        <div class="row">
                            <div class="col-md-2">
                                <strong>Fecha:</strong>
                                <p>{{ $mantenimiento->fechaformat }}</p>
                            </div>
                            <div class="col-md-2">
                                <strong>Hora:</strong>
                                <p>{{ $mantenimiento->hora_formatted ?? $mantenimiento->hora_mantenimiento }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Tipos de Mantenimiento Realizados:</strong>
                                <div class="mt-1">
                                    @forelse($mantenimiento->tipos as $tipo)
                                        <span class="badge-tipo me-1 mb-1">
                                            @switch($tipo->tipo)
                                                @case('cambio_aceite')
                                                    <i class="ri-oil-line"></i> Cambio Aceite
                                                    @break
                                                @case('cambio_filtro_aceite')
                                                    <i class="ri-filter-line"></i> Filtro Aceite
                                                    @break
                                                @case('cambio_filtro_gasolina')
                                                    <i class="ri-gas-station-line"></i> Filtro Gasolina
                                                    @break
                                                @case('cambio_filtro_aire')
                                                    <i class="ri-windy-line"></i> Filtro Aire
                                                    @break
                                                @case('mantenimiento_inyectores')
                                                    <i class="ri-drop-line"></i> Inyectores
                                                    @break
                                                @case('bateria')
                                                    <i class="ri-flashlight-line"></i> Batería
                                                    @break
                                                @case('otros')
                                                    <i class="ri-more-line"></i> {{ $tipo->descripcion ?? 'Otros' }}
                                                    @break
                                                @default
                                                    <i class="ri-tools-line"></i> {{ $tipo->tipo }}
                                            @endswitch
                                        </span>
                                    @empty
                                        @if($mantenimiento->tipo_mantenimiento && $mantenimiento->tipo_mantenimiento != 'multiple')
                                            <span class="badge-tipo">
                                                @switch($mantenimiento->tipo_mantenimiento)
                                                    @case('cambio_aceite') Cambio de Aceite @break
                                                    @case('cambio_filtro_aceite') Cambio de Filtro de Aceite @break
                                                    @case('cambio_filtro_gasolina') Cambio de Filtro de Gasolina @break
                                                    @case('cambio_filtro_aire') Cambio de Filtro de Aire @break
                                                    @case('mantenimiento_inyectores') Mantenimiento de Inyectores @break
                                                    @case('bateria') Batería @break
                                                    @default {{ $mantenimiento->tipo_mantenimiento }}
                                                @endswitch
                                            </span>
                                        @else
                                            <span class="text-muted">No especificado</span>
                                        @endif
                                    @endforelse
                                </div>
                            </div>
                            <div class="col-md-2">
                                <strong>Kilometraje:</strong>
                                <p>{{ $mantenimiento->kilometraje ? number_format($mantenimiento->kilometraje, 0, ',', '.') . ' km' : 'N/A' }}</p>
                            </div>
                            <div class="col-md-2">
                                <strong>Vendedor:</strong>
                                <p>
                                    @if($mantenimiento->vendedor)
                                        <i class="ri-user-star-line"></i> {{ $mantenimiento->vendedor->descrip }}
                                    @else
                                        <span class="text-muted">No asignado</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Línea adicional con información de registro --}}
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="ri-user-line"></i> Registrado por:
                                    <strong>{{ $mantenimiento->usuario->first_name ?? 'Sistema' }}</strong>
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="ri-time-line"></i>
                                    {{ $mantenimiento->fechaformat }} {{ $mantenimiento->horaformated ?? $mantenimiento->hora_mantenimiento }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- ============ NUEVA SECCIÓN: ENLACE PARA CLIENTE ============ -->
                    <!-- Botón para compartir con cliente -->
                    @if($mantenimiento->token_cliente)
                        <div class="mt-3 p-3" style="background: var(--pastel-blue); border-radius: 10px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><i class="ri-share-line"></i> Compartir con el Cliente</h6>
                                    <small class="text-muted">El cliente puede ver este mantenimiento sin necesidad de login</small>
                                </div>
                                <button class="btn btn-sm btn-success" onclick="compartirWhatsApp('{{ route('cliente.mantenimiento.ver', $mantenimiento->token_cliente) }}')">
                                    <i class="ri-whatsapp-line"></i> Compartir en WhatsApp
                                </button>
                            </div>
                            <div class="input-group mt-2">
                                <input type="text" class="form-control form-control-sm"
                                       value="{{ route('cliente.mantenimiento.ver', $mantenimiento->token_cliente) }}"
                                       id="enlaceCliente" readonly>
                                <button class="btn btn-sm btn-outline-primary" onclick="copyText('enlaceCliente')">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <script>

                        function compartirWhatsApp(url) {
                            let mensaje = encodeURIComponent(`¡Hola! Mira el detalle del mantenimiento de tu vehículo {{$mantenimiento->vehiculo->marca}} {{$mantenimiento->vehiculo->modelo}}\n\n${url}`);
                            window.open(`https://wa.me/?phone={{ (isset($mantenimiento->vehiculo->cliente->telef))? $mantenimiento->vehiculo->cliente->telef : ( (isset($mantenimiento->vehiculo->cliente->movil))? $mantenimiento->vehiculo->cliente->movil : '') }}&&text=${mensaje}`, '_blank');
                        }

                        function copyText(copyInput) {
                            const input = document.getElementById(copyInput);

                            input.select();
                            input.setSelectionRange(0, 99999);

                            try {
                                const copied = document.execCommand('copy');
                                if (copied) {
                                    $("#btncopy").html('Token Copiado <i class="bi bi-check-circle"></i>');
                                } else {
                                    // If execCommand fails, try to help user copy manually
                                    input.focus();
                                    alert('Please press Ctrl+C to copy the selected text');
                                }
                            } catch (err) {
                                console.error('Copy error:', err);
                                // Show text in alert as last resort
                                alert('Text to copy: ' + input.value);
                            }

                            // Remove selection
                            window.getSelection().removeAllRanges();
                        }


                    </script>
                    <!-- ============ FIN NUEVA SECCIÓN ============ -->

                    <!-- ============ SECCIÓN DE FOTOS DE EVIDENCIA ============ -->
                    @if($mantenimiento->fotos && $mantenimiento->fotos->count() > 0)
                        <div class="info-section" style="background: white;">
                            <h5 class="mb-3">
                                <i class="ri-camera-line"></i>
                                Fotos de Evidencia ({{ $mantenimiento->fotos->count() }})
                            </h5>
                            <div class="galeria-fotos">
                                @foreach($mantenimiento->fotos as $index => $foto)
                                    @php
                                        // La ruta ya está guardada como 'mantenimientos/ID/archivo.jpg'
                                        $rutaFoto = $foto->ruta_foto;

                                        // Eliminar 'public/' si existe al inicio
                                        if (str_starts_with($rutaFoto, 'public/')) {
                                            $rutaFoto = substr($rutaFoto, 7);
                                        }

                                        // Construir URL directa con asset() (sin storage)
                                        $timestamp = time() . '_' . $index . '_' . rand(1000, 9999);
                                        $urlFoto = asset($rutaFoto) . '?v=' . $timestamp;

                                        // Para debug (opcional, eliminar en producción)
                                        // console.log('Ruta foto: {{ $rutaFoto }}');
                                        // console.log('URL generada: {{ $urlFoto }}');
                                    @endphp
                                    <div class="foto-evidencia" onclick="verFoto('{{ asset($rutaFoto) }}')">
                                        <img src="{{ $urlFoto }}" alt="Evidencia"
                                             onerror="this.onerror=null; this.src='{{ asset('build/images/logo-light.png') }}'">
                                        <span class="tipo-badge">
            @switch($foto->tipo_evidencia)
                                                @case('cambio_aceite')
                                                    <i class="ri-oil-line"></i> Cambio Aceite
                                                    @break
                                                @case('producto_usado')
                                                    <i class="ri-shopping-cart-line"></i> Producto
                                                    @break
                                                @case('kilometraje')
                                                    <i class="ri-speed-line"></i> Kilometraje
                                                    @break
                                                @default
                                                    <i class="ri-image-line"></i> {{ ucfirst($foto->tipo_evidencia) }}
                                            @endswitch
        </span>
                                        @if($foto->descripcion)
                                            <div class="descripcion-overlay">
                                                {{ $foto->descripcion }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="info-section" style="background: var(--pastel-yellow);">
                            <h5 class="mb-3"><i class="ri-camera-line"></i> Fotos de Evidencia</h5>
                            <div class="foto-placeholder">
                                <i class="ri-camera-off-line"></i>
                                <p class="mb-0">No hay fotos de evidencia para este mantenimiento</p>
                            </div>
                        </div>
                    @endif
                    <!-- ============ FIN SECCIÓN DE FOTOS ============ -->

                    <!-- PRODUCTOS UTILIZADOS -->
                    <div class="info-section" style="background: white;">
                        <h5 class="mb-3"><i class="ri-shopping-cart-line"></i> Productos/Servicios Utilizados</h5>

                        @if($mantenimiento->productos && $mantenimiento->productos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-productos">
                                    <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Referencia</th>
                                        <th>Código</th>
                                        <th class="text-center">Cantidad</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($mantenimiento->productos as $producto)
                                        <tr>
                                            <td>
                                                <strong>{{ $producto->descripcion }}</strong>
                                                @if($producto->tipo == 'servicio')
                                                    <span class="badge bg-info">Servicio</span>
                                                @endif
                                            </td>
                                            <td>{{ $producto->referencia ?? 'N/A' }}</td>
                                            <td>{{ $producto->codprod && $producto->codprod != 'MANUAL' ? $producto->codprod : 'Manual' }}</td>
                                            <td class="text-center">{{ $producto->cantidad+0 }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($mantenimiento->producto_utilizado)
                            <!-- Para mantenimientos antiguos con un solo producto -->
                            <div class="producto-item">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>{{ $mantenimiento->producto_utilizado }}</strong>
                                        @if($mantenimiento->marca_producto)
                                            <br><small>Marca: {{ $mantenimiento->marca_producto }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if($mantenimiento->costo)
                                            <strong>$ {{ number_format($mantenimiento->costo, 2) }}</strong>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center p-4" style="background: var(--pastel-yellow); border-radius: 10px;">
                                <i class="ri-information-line" style="font-size: 2rem; color: #b8d9c6;"></i>
                                <p class="mt-2">No se registraron productos para este mantenimiento</p>
                            </div>
                        @endif
                    </div>

                    <!-- Próximo Mantenimiento -->
                    @if($mantenimiento->proximo_mantenimiento || $mantenimiento->proximo_kilometraje)
                        <div class="info-section" style="background: var(--pastel-purple);">
                            <h5 class="mb-3"><i class="ri-calendar-check-line"></i> Próximo Mantenimiento Sugerido</h5>
                            <div class="row">
                                @if($mantenimiento->proximo_mantenimiento)
                                    <div class="col-md-6">
                                        <strong>Fecha sugerida:</strong>
                                        <p>{{ \Carbon\Carbon::parse($mantenimiento->proximo_mantenimiento)->format('d/m/Y') }}</p>
                                    </div>
                                @endif
                                @if($mantenimiento->proximo_kilometraje)
                                    <div class="col-md-6">
                                        <strong>Kilometraje sugerido:</strong>
                                        <p>{{ number_format($mantenimiento->proximo_kilometraje, 0, ',', '.') }} km</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Observaciones -->
                    @if($mantenimiento->observaciones)
                        <div class="info-section" style="background: var(--pastel-peach);">
                            <h5 class="mb-3"><i class="ri-chat-1-line"></i> Observaciones</h5>
                            <p class="mb-0">{{ $mantenimiento->observaciones }}</p>
                        </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="text-end mt-4 mb-4  ">
                        <a href="{{ route('clientes.vehiculos.mantenimientos.edit', [$mantenimiento->codclie, $mantenimiento->fk_vehiculo, $mantenimiento->id]) }}"
                           class="btn btn-warning me-2">
                            <i class="ri-edit-line"></i> Editar
                        </a>
                        <a href="/mantenimiento-rapido"
                           class="btn btn-secondary me-2">
                            <i class="ri-arrow-go-back-line"></i> Nuevo mantenimiento
                        </a>
                        <button onclick="window.print()" class="btn btn-pastel me-2">
                            <i class="ri-printer-line"></i> Imprimir
                        </button>
                        <button onclick="generarEnlaceCliente({{ $mantenimiento->id }})"
                                class="btn btn-whatsapp btn-success" style="display: none" >
                            <i class="ri-whatsapp-line"></i> WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver foto grande - Mejorado para móviles -->
    <div class="modal fade" id="fotoModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: rgba(0,0,0,0.95); border: none;">
                <!-- Botón de cierre en la esquina superior derecha (fijo) -->
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Cerrar"
                        style="z-index: 1050; width: 40px; height: 40px; background-size: 24px; opacity: 0.8;"></button>

                <!-- Botón de cierre flotante en la parte inferior -->
                <div class="position-absolute bottom-0 start-0 end-0 p-3 text-center" style="z-index: 1050;">
                    <button type="button" class="btn btn-light btn-lg rounded-pill px-4 shadow" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.9);">
                        <i class="ri-close-line"></i> Cerrar
                    </button>
                </div>

                <div class="modal-body d-flex align-items-center justify-content-center p-0">
                    <img src="" id="fotoGrande" style="max-width: 100%; max-height: 90vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        // Función para ver foto en modal
        function verFoto(url) {
            console.log('Abriendo foto desde URL:', url);
            // Limpiar la URL de parámetros anteriores
            var cleanUrl = url.split('?')[0];
            var urlConTimestamp = cleanUrl + '?v=' + new Date().getTime();
            $('#fotoGrande').attr('src', urlConTimestamp);

            // Configurar el modal para que se pueda cerrar tocando fuera
            $('#fotoModal').modal({
                backdrop: true,
                keyboard: true
            });

            $('#fotoModal').modal('show');
        }

        function imprimirDetalle() {
            var contenido = document.querySelector('.detail-card').cloneNode(true);
            var ventana = window.open('', '_blank');
            ventana.document.write(`
            <html>
                <head>
                    <title>Mantenimiento #{{ str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT) }}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
                    <style>
                        body { padding: 20px; background: white; }
                        .btn { display: none; }
                        .foto-evidencia { break-inside: avoid; }
                        @media print { .btn { display: none; } }
                    </style>
                </head>
                <body>
                    ${contenido.outerHTML}
                </body>
            </html>
        `);
            ventana.document.close();
            ventana.print();
        }

        function generarEnlaceCliente() {
            $('#loadingOverlay').fadeIn();
            $.ajax({
                url: '/mantenimiento/notificar',
                method: 'POST',
                data: {
                    mantenimientoId : {{$mantenimiento->id}},
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    if (response.success) {
                        mostrarNotificacion('Enlace generado: ' + response.url, 'success');
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    alert('Error al generar enlace');
                }
            });
        }

        // Recargar imágenes después de cargar la página
        $(document).ready(function() {
            // Forzar recarga de todas las imágenes
            $('.foto-evidencia img').each(function(index) {
                var $img = $(this);
                var src = $img.attr('src');
                if (src && !src.includes('logo-light.png')) {
                    var cleanSrc = src.split('?')[0];
                    var newSrc = cleanSrc + '?v=' + new Date().getTime() + '_' + index;
                    $img.attr('src', newSrc);
                    console.log('Recargando imagen:', newSrc);
                }
            });

            // Cerrar modal al hacer clic/tocar la imagen
            $(document).on('click', '#fotoGrande', function() {
                $('#fotoModal').modal('hide');
            });

            // Cerrar modal con el botón de volver de Android (evento de historial)
            $('#fotoModal').on('shown.bs.modal', function() {
                history.pushState(null, null, location.href);
                $(window).on('popstate.modalClose', function() {
                    $('#fotoModal').modal('hide');
                    $(window).off('popstate.modalClose');
                });
            });

            $('#fotoModal').on('hidden.bs.modal', function() {
                $(window).off('popstate.modalClose');
                history.back();
            });
        });
    </script>
@endsection
