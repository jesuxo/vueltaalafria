<style>
    /* Agregar al final de la sección de estilos */
    .foto-thumbnail {
        transition: all 0.2s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .foto-thumbnail:hover {
        transform: scale(1.05);
        border-color: #9fb7c9;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 10;
    }

    .foto-thumbnail img {
        transition: transform 0.3s ease;
    }

    .foto-thumbnail:hover img {
        transform: scale(1.1);
    }

    /* Modal personalizado */
    #fotoModalRapido .modal-content {
        background: transparent;
        border: none;
    }

    #fotoModalRapido .btn-close {
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    #fotoModalRapido .btn-close:hover {
        opacity: 1;
    }

    /* Badge de tipo de foto */
    .foto-tipo-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background: rgba(255,255,255,0.9);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        z-index: 2;
    }

    /* Descripción overlay */
    .foto-descripcion {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 3px;
        font-size: 0.6rem;
        text-align: center;
        transform: translateY(100%);
        transition: transform 0.3s ease;
        z-index: 2;
    }

    .foto-thumbnail:hover .foto-descripcion {
        transform: translateY(0);
    }
</style>

{{-- resources/views/mantenimientos/partials/detalle_rapido.blade.php --}}
<div class="column-header">
    <h5>
        <i class="ri-file-copy-line"></i>
        Detalle #{{ str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT) }}
    </h5>
    <div>
        <button class="btn btn-sm btn-light" onclick="cargarDetalle({{ $mantenimiento->id }})">
            <i class="ri-refresh-line"></i>
        </button>
        <button class="btn btn-sm btn-light" onclick="$('#columnaLista').removeClass('colapsada'); $('#columnaDetalle').addClass('placeholder').html('<div class=\'text-center p-5\'><i class=\'ri-arrow-left-s-line\' style=\'font-size: 4rem; color: #cbd5e0;\'></i><h5>Seleccione un mantenimiento</h5><p class=\'text-muted\'>Haga clic en cualquier item de la lista para ver sus detalles</p></div>')">
            <i class="ri-close-line"></i>
        </button>
    </div>
</div>

<div class="detalle-content">
    <!-- Tarjeta de información básica -->
    <div class="card mb-3" style="border: none; background: var(--pastel-pink);">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="mb-1"><i class="ri-user-line"></i> Cliente</h6>
                    <p class="mb-0"><strong>{{ $mantenimiento->vehiculo->cliente->descrip }}</strong></p>
                    <small>{{ $mantenimiento->vehiculo->cliente->id3 }}</small>
                </div>
                <div class="text-end">
                    <h6 class="mb-1"><i class="ri-car-line"></i> Vehículo</h6>
                    <p class="mb-0"><strong>{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</strong></p>
                    <small>{{ $mantenimiento->vehiculo->identificacion }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles del mantenimiento -->
    <div class="card mb-3" style="border: none; background: var(--pastel-yellow);">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Fecha/Hora</small>
                    <p><strong>{{ $mantenimiento->fechaformat }} {{ $mantenimiento->horaformated }}</strong></p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Kilometraje</small>
                    <p class="editable-field" ondblclick="editarCampo({{ $mantenimiento->id }}, 'kilometraje')">
                        <strong class="kilometraje-value">{{ number_format($mantenimiento->kilometraje, 0, ',', '.') }} km</strong>
                    </p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <small class="text-muted">Tipo</small>
                    <p>
                        <span class="badge" style="background: var(--pastel-green); color: burlywood !important;">
                            @switch($mantenimiento->tipo_mantenimiento)
                                @case('cambio_aceite') Cambio Aceite @break
                                @case('cambio_filtro_aceite') Filtro Aceite @break
                                @case('cambio_filtro_gasolina') Filtro Gasolina @break
                                @case('cambio_filtro_aire') Filtro Aire @break
                                @case('mantenimiento_inyectores') Inyectores @break
                                @case('bateria') Batería @break
                                @default {{ $mantenimiento->tipo_mantenimiento }}
                            @endswitch
                        </span>
                    </p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Vendedor</small>
                    <p class="editable-field" ondblclick="editarCampo({{ $mantenimiento->id }}, 'codvend')">
                        <strong class="codvend-value">
                            @if($mantenimiento->vendedor)
                                {{ $mantenimiento->vendedor->descrip }}
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <div class="card mb-3" style="border: none; background: white;">
        <div class="card-body">
            <h6><i class="ri-shopping-cart-line"></i> Productos</h6>
            @if($mantenimiento->productos && $mantenimiento->productos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($mantenimiento->productos as $p)
                            <tr>
                                <td>{{ $p->descripcion }}</td>
                                <td class="text-center">{{ $p->cantidad }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No hay productos registrados</p>
            @endif
        </div>
    </div>

    <!-- ============ NUEVA SECCIÓN: FOTOS DE EVIDENCIA ============ -->
    @if($mantenimiento->fotos && $mantenimiento->fotos->count() > 0)
        <div class="card mb-3" style="border: none; background: white;">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="ri-camera-line"></i>
                    Fotos de Evidencia ({{ $mantenimiento->fotos->count() }})
                </h6>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 8px;">
                    @foreach($mantenimiento->fotos as $foto)
                        @php
                            $tipos = [
                                'cambio_aceite'  => 'Cambio Aceite',
                                'producto_usado' => 'Producto',
                                'kilometraje'    => 'Km',
                                'general'        => 'General'
                            ];
                            $tipoTexto = $tipos[$foto->tipo_evidencia] ?? ucfirst($foto->tipo_evidencia);

                            // Construir la URL correctamente
                            $urlFoto =  "/storage/public/".$foto->ruta_foto;
                        @endphp

                        <div class="foto-thumbnail"
                             style="position: relative; border-radius: 6px; overflow: hidden; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); aspect-ratio: 1/1;"
                             onclick="verFoto('{{ $urlFoto }}')">

                            <img src="{{ $urlFoto }}"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 alt="Evidencia"
                                 onerror="this.src='{{ asset('build/images/logo-light.png') }}'; this.onerror=null;">

                            <span style="position: absolute; top: 3px; left: 3px; background: rgba(255,255,255,0.9); padding: 2px 4px; border-radius: 4px; font-size: 0.65rem; font-weight: 600; z-index: 2;">
                            {{ $tipoTexto }}
                        </span>

                            @if($foto->descripcion)
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 3px; font-size: 0.6rem; text-align: center; z-index: 2;">
                                    {{ Str::limit($foto->descripcion, 15) }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card mb-3" style="border: none; background: var(--pastel-yellow);">
            <div class="card-body text-center py-3">
                <i class="ri-camera-off-line" style="font-size: 2rem; color: #b8d9c6;"></i>
                <p class="mb-0 small">No hay fotos de evidencia</p>
            </div>
        </div>
    @endif
    <!-- ============ FIN NUEVA SECCIÓN ============ -->

    <!-- Observaciones -->
    @if($mantenimiento->observaciones)
        <div class="card mb-3" style="border: none; background: var(--pastel-peach);">
            <div class="card-body">
                <h6><i class="ri-chat-1-line"></i> Observaciones</h6>
                <p class="mb-0">{{ $mantenimiento->observaciones }}</p>
            </div>
        </div>
    @endif

    <!-- Próximo mantenimiento -->
    @if($mantenimiento->proximo_mantenimiento || $mantenimiento->proximo_kilometraje)
        <div class="card mb-3" style="border: none; background: var(--pastel-purple);">
            <div class="card-body">
                <h6><i class="ri-calendar-check-line"></i> Próximo mantenimiento</h6>
                <div class="row">
                    @if($mantenimiento->proximo_mantenimiento)
                        <div class="col-6">
                            <small>Fecha:</small>
                            <p><strong>{{ $mantenimiento->proximo_mantenimiento->format('d/m/Y') }}</strong></p>
                        </div>
                    @endif
                    @if($mantenimiento->proximo_kilometraje)
                        <div class="col-6">
                            <small>Km:</small>
                            <p><strong>{{ number_format($mantenimiento->proximo_kilometraje, 0, ',', '.') }} km</strong></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Botones de acción -->
    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('clientes.vehiculos.mantenimientos.edit', [$mantenimiento->codclie, $mantenimiento->fk_vehiculo, $mantenimiento->id]) }}"
           class="btn btn-success flex-fill">
            <i class="ri-edit-line"></i> Editar completo
        </a>
        <button class="btn btn-info flex-fill" onclick="window.open('/mantenimiento/comprobante/{{ $mantenimiento->id }}', '_blank')">
            <i class="ri-printer-line"></i> Imprimir
        </button>
    </div>
</div>

<!-- Agregar estilos para las fotos -->
<style>
    .foto-thumbnail {
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .foto-thumbnail:hover {
        transform: scale(1.05);
        border-color: #9fb7c9;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 10;
    }

    /* Modal para fotos grandes */
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
</style>

<script>
    function editarCampo(id, campo) {
        let elemento = $(`.${campo}-value`);
        let valorActual = elemento.text().trim();
        let nuevoValor = prompt(`Editar ${campo}:`, valorActual);

        if (nuevoValor !== null) {
            actualizarCampo(id, campo, nuevoValor);
        }
    }

    // Función para ver foto grande
    function verFoto(url) {
        // Verificar si la URL es válida
        if (!url || url === '') {
            alert('No se puede cargar la imagen');
            return;
        }

        // Verificar si ya existe el modal, si no, crearlo
        if ($('#fotoModalRapido').length === 0) {
            let modalHtml = `
            <div class="modal fade" id="fotoModalRapido" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content" style="background: transparent; border: none;">
                        <div class="modal-body text-center p-0 position-relative">
                            <img src="" id="fotoGrandeRapido" style="max-width: 100%; max-height: 80vh; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 bg-white rounded-circle" style="padding: 8px;" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
            $('body').append(modalHtml);
        }

        // Actualizar la imagen y mostrar el modal
        $('#fotoGrandeRapido').attr('src', url);
        $('#fotoModalRapido').modal('show');

        // Manejar error de carga de imagen
        $('#fotoGrandeRapido').on('error', function() {
            $(this).attr('src', '{{ asset('build/images/error-image.png') }}');
        });
    }

    function actualizarCampo(id, campo, valor) {
        // Aquí va tu código para actualizar el campo via AJAX
        console.log('Actualizando:', id, campo, valor);
        // Implementa la llamada AJAX correspondiente
    }
</script>
