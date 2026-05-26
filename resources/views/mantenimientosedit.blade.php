@extends('layouts.master')
@section('title')
    Editar Mantenimiento #{{ $mantenimiento->id }}
@endsection
@section('css')
    <style>
        :root {
            --pastel-blue: #e6f3ff;
            --pastel-green: #e1f7e6;
            --pastel-yellow: #fff9e6;
            --pastel-pink: #ffe6f0;
            --pastel-purple: #f0e6ff;
            --pastel-peach: #ffe6d9;
        }

        .producto-item {
            background: var(--pastel-blue);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            border-left: 4px solid #9fb7c9;
        }

        .producto-item:hover {
            background: #d4e8ff;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 240, 230, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(3px);
        }

        .pastel-btn-primary {
            background: #c5d9e8 !important;
            border: none;
            color: #2c3e50 !important;
        }
        .pastel-btn-primary:hover {
            background: #b3c9da!important;
        }

        .pastel-btn-success {
            background: #c1e0cd !important;
            border: none;
            color: #2c3e50 !important;
        }
        .pastel-btn-success:hover {
            background: #aed0bd!important;
        }
    </style>
@endsection
@section('content')
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-secondary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: var(--pastel-purple);">
                    <h4 class="mb-0"><i class="ri-edit-line"></i> Editar Mantenimiento #{{ str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT) }}</h4>
                    <small>Vehículo: {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->identificacion }})</small>
                </div>
                <div class="card-body">
                    <!-- Información del cliente y vehículo -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card" style="background: var(--pastel-pink);">
                                <div class="card-body" style="position: relative">
                                    <h6><i class="ri-user-line"></i> Cliente </h6>
                                    <a href="/clientes/{{$vehiculo->codclie}}/tab1" target="_blank" style=" position: absolute; right: 10px; top: 10px"><i class="ri-edit-line"></i></a>
                                    <p class="mb-1"><strong>{{ $vehiculo->cliente->descrip }}</strong></p>
                                    <p class="mb-1">Cédula: {{ $vehiculo->cliente->id3 }}</p>
                                    <p class="mb-0">Tel: {{ $vehiculo->cliente->telef ?? $vehiculo->cliente->movil ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card" style="background: var(--pastel-green);">
                                <div class="card-body">
                                    <h6><i class="ri-car-line"></i> Vehículo</h6>
                                    <p class="mb-1"><strong>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->year }})</strong></p>
                                    <p class="mb-1">Placa: {{ $vehiculo->identificacion }}</p>
                                    <p class="mb-0">Tipo: {{ $vehiculo->tipo->tipo ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('clientes.vehiculos.mantenimientos.update', [$vehiculo->codclie, $vehiculo->id, $mantenimiento->id]) }}"
                          method="POST" id="mantenimientoForm">
                        @csrf
                        @method('POST')

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Fecha *</label>
                                <input type="date" class="form-control" name="fecha_mantenimiento"
                                       value="{{ $mantenimiento->fecha_mantenimiento }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Hora</label>
                                <input type="time" class="form-control" name="hora_mantenimiento"
                                       value="{{ $mantenimiento->hora_mantenimiento    }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Kilometraje</label>
                                <input type="number" class="form-control" name="kilometraje" id="km_actual"
                                       value="{{ $mantenimiento->kilometraje }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Vendedor</label>
                                <select class="form-select" name="codvend">
                                    <option value="">Seleccione vendedor</option>
                                    @foreach($vendedores as $vendedor)
                                        <option value="{{ $vendedor->codvend }}"
                                            {{ $mantenimiento->codvend == $vendedor->codvend ? 'selected' : '' }}>
                                            {{ $vendedor->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="fw-bold"><i class="ri-tools-line"></i> Tipos de Mantenimiento Realizados:</label>
                                <div class="card" style="background: var(--pastel-yellow);">
                                    <div class="card-body">
                                        <div class="row" id="tiposMantenimientoContainer">
                                            @php
                                                $tiposSeleccionados = $mantenimiento->tipos->pluck('tipo')->toArray();
                                            @endphp

                                            @foreach($tipos_mantenimiento as $value => $label)
                                                <div class="col-md-4 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input tipo-mantenimiento-check" type="checkbox"
                                                               name="tipos_mantenimiento[]" value="{{ $value }}"
                                                               id="tipo_{{ $value }}"
                                                            {{ in_array($value, $tiposSeleccionados) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tipo_{{ $value }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Campo para especificar "Otros" con el valor guardado -->
                                        @php
                                            $otroTipo = $mantenimiento->tipos->where('tipo', 'otros')->first();
                                        @endphp
                                        <div class="row mt-2" id="otrosTipoContainer" style="{{ $otroTipo ? 'display: flex;' : 'display: none;' }}">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="otro_tipo_descripcion"
                                                       placeholder="Especifique otro tipo de mantenimiento"
                                                       value="{{ $otroTipo->descripcion ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN DE PRODUCTOS -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">🛒 Productos/Servicios utilizados:</label>

                                <!-- Lista de productos actuales -->
                                <div id="productosList" class="mb-3">
                                    @forelse($mantenimiento->productos as $producto)
                                        <div class="producto-item" id="producto-{{ $producto->id }}">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <strong>{{ $producto->descripcion }}</strong>
                                                    @if($producto->referencia)
                                                        <br><small>Ref: {{ $producto->referencia }}</small>
                                                    @endif
                                                    @if($producto->codprod && $producto->codprod != 'MANUAL')
                                                        <br><small>Cód: {{ $producto->codprod }}</small>
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control form-control-sm"
                                                           value="{{ $producto->cantidad }}"
                                                           onchange="actualizarProducto({{ $producto->id }}, 'cantidad', this.value)">
                                                </div>

                                                <div class="col-md-1 text-end">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="eliminarProductoExistente({{ $producto->id }})">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted text-center p-3" id="noProductosMsg">
                                            <i class="ri-information-line"></i> No hay productos registrados
                                        </div>
                                    @endforelse
                                </div>

                                <div id="productosTempList"></div>

                                <div class="card" style="background: var(--pastel-yellow);">
                                    <div class="card-body">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" id="buscadorProducto"
                                                   placeholder="Buscar producto por nombre, código o referencia...">
                                            <button class="btn pastel-btn-primary" type="button" id="btnBuscarProducto">
                                                <i class="ri-search-line"></i> Buscar
                                            </button>
                                        </div>

                                        <div id="resultadosProductos" class="list-group"
                                             style="max-height: 200px; overflow-y: auto; display: none;"></div>

                                        <div class="mt-2">
                                            <a href="javascript:;" id="btnAgregarManual" style="color: #0072c5;">
                                                <i class="ri-add-line"></i> Agregar producto manualmente
                                            </a>
                                        </div>

                                        <div id="formProductoManual" style="display: none; margin-top: 10px;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm"
                                                           id="productoManualDesc" placeholder="Descripción del producto">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm"
                                                           id="productoManualRef" placeholder="Referencia">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control form-control-sm"
                                                           id="productoManualCant" placeholder="Cant" value="1" min="0.01" step="0.01">
                                                </div>
                                                <div class="col-md-1">
                                                    <button class="btn btn-sm pastel-btn-success" id="btnGuardarProductoManual">
                                                        <i class="ri-check-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Próximo mantenimiento -->
                        <!-- Próximo mantenimiento -->
                        <div class="row" id="proximoMantenimientoSection"
                             style="{{ $mantenimiento->tipo_mantenimiento == 'cambio_aceite' || $mantenimiento->tipo_mantenimiento == 'multiple' && in_array('cambio_aceite', $tiposSeleccionados) ? '' : 'display: none;' }}">
                            <div class="col-md-12">
                                <hr>
                                <h6>⏰ Próximo Mantenimiento <small class="text-muted">(Opcional)</small></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Fecha próxima sugerida</label>
                                <input type="date" class="form-control" name="proximo_mantenimiento" id="proximo_mantenimiento"
                                       value="{{ $mantenimiento->proximo_mantenimiento ? $mantenimiento->proximo_mantenimiento->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kilometraje próximo sugerido</label>
                                <input type="number" class="form-control" name="proximo_kilometraje" id="proximo_kilometraje"
                                       value="{{ $mantenimiento->proximo_kilometraje }}">
                            </div>
                        </div>

                        <div class="row mb-3" id="calcularProximoBtn"
                             style="{{ $mantenimiento->tipo_mantenimiento == 'cambio_aceite' || $mantenimiento->tipo_mantenimiento == 'multiple' && in_array('cambio_aceite', $tiposSeleccionados) ? '' : 'display: none;' }}">
                            <div class="col-md-12">
                                <button type="button" class="btn pastel-btn-primary" onclick="calcularProximoOpcional()">
                                    <i class="ri-calculator-line"></i> Calcular Próximo Mantenimiento (+6 meses / +5000 km)
                                </button>
                            </div>
                        </div>

                        <div class="row mb-3" id="calcularProximoBtn" style="display: none;">
                            <div class="col-md-12">
                                <button type="button" class="btn pastel-btn-primary" onclick="calcularProximoOpcional()">
                                    <i class="ri-calculator-line"></i> Calcular Próximo Mantenimiento (+6 meses / +5000 km)
                                </button>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Observaciones</label>
                                <textarea class="form-control" name="observaciones" rows="2">{{ $mantenimiento->observaciones }}</textarea>
                            </div>
                        </div>

                        <!-- Total
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <h4>Total: <span id="totalProductos">${{ number_format($mantenimiento->costo ?? 0, 2) }}</span></h4>
                            </div>
                        </div>
-->
                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <a href="{{ route('clientes.vehiculos.mantenimientos.show', [$vehiculo->codclie, $vehiculo->id, $mantenimiento->id]) }}"
                                   class="btn btn-info btn-lg">
                                    <i class="ri-eye-line"></i> Ver Detalle
                                </a>
                                <button type="button" onclick="$('#mantenimientoForm').submit()" class="btn pastel-btn-success btn-lg">
                                    <i class="ri-save-line"></i> Actualizar Mantenimiento
                                </button>
                                <a href="{{ route('clientes.vehiculos.mantenimientos', [$vehiculo->codclie, $vehiculo->id]) }}"
                                   class="btn btn-secondary btn-lg">
                                    <i class="ri-arrow-go-back-line"></i> Cancelar
                                </a>
                            </div>
                        </div>

                        <!-- Hidden inputs para productos existentes -->
                        @foreach($mantenimiento->productos as $producto)
                            <input type="hidden" name="productos_existentes[{{ $producto->id }}][id]" value="{{ $producto->id }}" id="existing_{{ $producto->id }}_id">
                            <input type="hidden" name="productos_existentes[{{ $producto->id }}][descripcion]" value="{{ $producto->descripcion }}" id="existing_{{ $producto->id }}_desc">
                            <input type="hidden" name="productos_existentes[{{ $producto->id }}][referencia]" value="{{ $producto->referencia }}" id="existing_{{ $producto->id }}_ref">
                            <input type="hidden" name="productos_existentes[{{ $producto->id }}][cantidad]" value="{{ $producto->cantidad }}" id="existing_{{ $producto->id }}_cant">
                            <!-- <input type="hidden" name="productos_existentes[{{ $producto->id }}][precio]" value="{{ $producto->precio }}" id="existing_{{ $producto->id }}_precio">-->
                            <input type="hidden" name="productos_existentes[{{ $producto->id }}][codprod]" value="{{ $producto->codprod }}" id="existing_{{ $producto->id }}_cod">
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        let productosTemp = [];
        let productosExistentes = @json($mantenimiento->productos->keyBy('id'));
        let productosEliminados = [];


        function calcularProximoOpcional() {
            let fecha = $('input[name="fecha_mantenimiento"]').val();
            let km = $('#km_actual').val();

            if (fecha) {
                let f = new Date(fecha);
                f.setMonth(f.getMonth() + 6);
                $('#proximo_mantenimiento').val(f.toISOString().split('T')[0]);
            }

            if (km) {
                $('#proximo_kilometraje').val(parseInt(km) + 5000);
            }

            $('#proximoMantenimientoSection').fadeIn();
        }

        $(document).ready(function() {
            // Inicializar checkboxes según los tipos guardados
            let tiposSeleccionados = @json($tiposSeleccionados);

            // Marcar los checkboxes
            tiposSeleccionados.forEach(function(tipo) {
                $('#tipo_' + tipo).prop('checked', true);
            });

            // Mostrar campo "Otros" si está seleccionado
            if (tiposSeleccionados.includes('otros')) {
                $('#otrosTipoContainer').show();
            }

            // Mostrar botón de cálculo si hay cambio de aceite
            if (tiposSeleccionados.includes('cambio_aceite')) {
                $('#calcularProximoBtn').show();
                $('#proximoMantenimientoSection').show();
            }

            // Evento change para checkboxes
            $('input[name="tipos_mantenimiento[]"]').change(function() {
                if ($(this).val() === 'otros' && $(this).is(':checked')) {
                    $('#otrosTipoContainer').fadeIn();
                } else if ($(this).val() === 'otros' && !$(this).is(':checked')) {
                    $('#otrosTipoContainer').fadeOut();
                }

                // Verificar si hay cambio de aceite seleccionado
                if ($('#tipo_cambio_aceite').is(':checked')) {
                    $('#calcularProximoBtn').fadeIn();
                    $('#proximoMantenimientoSection').fadeIn();
                } else {
                    let hayCambioAceite = false;
                    $('input[name="tipos_mantenimiento[]"]:checked').each(function() {
                        if ($(this).val() === 'cambio_aceite') {
                            hayCambioAceite = true;
                        }
                    });

                    if (!hayCambioAceite) {
                        $('#calcularProximoBtn').fadeOut();
                        // No ocultamos la sección si ya tiene valores
                        if (!$('#proximo_mantenimiento').val() && !$('#proximo_kilometraje').val()) {
                            $('#proximoMantenimientoSection').fadeOut();
                        }
                    }
                }
            });





            // También para selects y textareas si lo deseas
            $('#mantenimientoForm').on('keydown', 'input', 'select, textarea', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    return false;
                }
            });

            // Prevenir envío del formulario con Enter en búsquedas
            $('#buscadorProducto').keypress(function(e) {

                if (e.which == 13) {
                    e.preventDefault();
                    buscarProductos();
                }
            });

            $('#btnBuscarProducto').click(buscarProductos);

            $('#btnAgregarManual').click(function() {
                $('#formProductoManual').fadeIn();
                $('#productoManualDesc').focus();
            });

            $('#btnGuardarProductoManual').click(function() {
                let desc = $('#productoManualDesc').val().trim();
                if (!desc) {
                    alert('Ingrese la descripción del producto');
                    return;
                }

                let producto = {
                    tempId: 'temp_' + Date.now(),
                    codprod: 'MANUAL',
                    descripcion: desc,
                    referencia: $('#productoManualRef').val().trim(),
                    cantidad: parseFloat($('#productoManualCant').val()) || 1,
                    //precio: 0,
                    tipo: 'producto'
                };

                agregarProductoTemp(producto);

                $('#productoManualDesc').val('');
                $('#productoManualRef').val('');
                $('#productoManualCant').val('1');
                $('#formProductoManual').hide();
            });

            // Calcular próximo mantenimiento
            $('#tipo_mantenimiento').change(function() {
                if ($(this).val() == 'cambio_aceite') {
                    $('#proximoMantenimientoSection').fadeIn();
                    calcularProximoMantenimiento();
                } else {
                    $('#proximoMantenimientoSection').fadeOut();
                }
            });

            $('#km_actual').change(calcularProximoMantenimiento);
            $('input[name="fecha_mantenimiento"]').change(calcularProximoMantenimiento);

            // Actualizar total inicial
            //calcularTotal();
        });

        function buscarProductos() {
            let busqueda = $('#buscadorProducto').val().trim();
            if (busqueda.length < 2) {
                alert('Ingrese al menos 2 caracteres');
                return;
            }

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.buscar-productos") }}',
                method: 'POST',
                data: {
                    busqueda: busqueda,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success && response.productos.length > 0) {
                        mostrarResultadosProductos(response.productos);
                    } else {
                        $('#resultadosProductos').html(`
                        <div class="list-group-item text-muted">
                            No se encontraron productos.
                            <a href="javascript:;" onclick="$('#btnAgregarManual').click()">Agregar manual</a>
                        </div>
                    `).fadeIn();
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    alert('Error al buscar productos');
                }
            });
        }

        function mostrarResultadosProductos(productos) {
            let html = '';
            productos.forEach(p => {
                html += `
                <a href="javascript:;" class="list-group-item list-group-item-action"
                   onclick="seleccionarProducto('${p.codprod}', '${p.descrip.replace(/'/g, "\\'")}', '${p.refere || ''}')">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${p.descrip}</strong><br>
                            <small class="text-muted">Cód: ${p.codprod} | Ref: ${p.refere || 'N/A'}</small>
                        </div>
                        <div>
                            <i class="ri-add-line"></i>
                        </div>
                    </div>
                </a>
            `;
            });

            $('#resultadosProductos').html(html).fadeIn();
        }

        function seleccionarProducto(codprod, descrip, referencia) {
            let cantidad = prompt('Ingrese la cantidad:', '1');
            if (cantidad === null) return;
            cantidad = parseFloat(cantidad) || 1;

          /*  let precio = prompt('Ingrese el precio:', '0');
            precio = parseFloat(precio) || 0;*/

            let producto = {
                tempId: 'temp_' + Date.now() + Math.random(),
                codprod: codprod,
                descripcion: descrip,
                referencia: referencia,
                cantidad: cantidad,
              //  precio: precio,
                tipo: 'producto'
            };

            agregarProductoTemp(producto);
            $('#resultadosProductos').hide();
            $('#buscadorProducto').val('');
        }

        function agregarProductoTemp(producto) {
            productosTemp.push(producto);
            renderizarProductosTemp();
            //calcularTotal();
        }

        function eliminarProductoTemp(tempId) {
            productosTemp = productosTemp.filter(p => p.tempId != tempId);
            renderizarProductosTemp();
           // calcularTotal();
        }

        function eliminarProductoExistente(productoId) {
            if (confirm('¿Eliminar este producto?')) {
                productosEliminados.push(productoId);
                $(`#producto-${productoId}`).fadeOut(function() {
                    $(this).remove();
                    //calcularTotal();

                    // Agregar hidden input para indicar eliminación
                    $('#mantenimientoForm').append(`
                    <input type="hidden" name="productos_eliminados[]" value="${productoId}">
                `);
                });
            }
        }

        function actualizarProducto(productoId, campo, valor) {
            $(`#existing_${productoId}_${campo}`).val(valor);

            // Actualizar el hidden input correspondiente
            if (campo == 'cantidad') {
                $(`input[name="productos_existentes[${productoId}][cantidad]"]`).val(valor);
            }

            /*else if (campo == 'precio') {
                $(`input[name="productos_existentes[${productoId}][precio]"]`).val(valor);
            }*/

            //calcularTotal();
        }

        function renderizarProductosTemp() {
            if (productosTemp.length === 0) {
                $('#productosTempList').html('');
                return;
            }

            let html = '<h6 class="mt-3">Nuevos productos:</h6>';
            productosTemp.forEach(p => {
                html += `
                <div class="producto-item" style="background: var(--pastel-yellow);" id="temp-${p.tempId}">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <strong>${p.descripcion}</strong>
                            ${p.referencia ? `<br><small>Ref: ${p.referencia}</small>` : ''}
                            ${p.codprod && p.codprod != 'MANUAL' ? `<br><small>Cód: ${p.codprod}</small>` : ''}
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control form-control-sm" value="${p.cantidad}"
                                   onchange="actualizarProductoTemp('${p.tempId}', 'cantidad', this.value)">
                        </div>

                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProductoTemp('${p.tempId}')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="productos[${p.tempId}][tempId]" value="${p.tempId}">
                    <input type="hidden" name="productos[${p.tempId}][codprod]" value="${p.codprod}">
                    <input type="hidden" name="productos[${p.tempId}][descripcion]" value="${p.descripcion}">
                    <input type="hidden" name="productos[${p.tempId}][referencia]" value="${p.referencia}">
                    <input type="hidden" name="productos[${p.tempId}][cantidad]" value="${p.cantidad}" id="temp_${p.tempId}_cant">
                   <input type="hidden" name="productos[${p.tempId}][tipo]" value="${p.tipo}">
                </div>
            `;
            });

            /*  <input type="hidden" name="productos[${p.tempId}][precio]" value="${p.precio}" id="temp_${p.tempId}_precio">

                    <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control form-control-sm"
                                       value="${p.precio}" onchange="actualizarProductoTemp('${p.tempId}', 'precio', this.value)">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <strong>$${(p.precio * p.cantidad).toFixed(2)}</strong>
                        </div>*/

            $('#productosTempList').html(html);
        }

        function actualizarProductoTemp(tempId, campo, valor) {
            let producto = productosTemp.find(p => p.tempId == tempId);
            if (producto) {
                producto[campo] = parseFloat(valor) || 0;
                $(`#temp_${tempId}_${campo}`).val(valor);
                renderizarProductosTemp();
                //calcularTotal();
            }
        }

        function calcularTotal() {
            let total = 0;

            // Sumar productos existentes
           /* $('.producto-item:not(#productosTempList .producto-item)').each(function() {
                let precio = parseFloat($(this).find('input[type="number"][step="0.01"]').val()) || 0;
                let cantidad = parseFloat($(this).find('input[type="number"]:first').val()) || 1;
                total += precio * cantidad;
            });*/

            // Sumar productos temporales
            /*productosTemp.forEach(p => {
                total += (p.precio || 0) * (p.cantidad || 1);
            });*/

            //$('#totalProductos').text('$' + total.toFixed(2));
        }

        function calcularProximoMantenimiento() {
            if ($('#tipo_mantenimiento').val() == 'cambio_aceite') {
                let fecha = $('input[name="fecha_mantenimiento"]').val();
                if (fecha) {
                    let f = new Date(fecha);
                    f.setMonth(f.getMonth() + 6);
                    $('#proximo_mantenimiento').val(f.toISOString().split('T')[0]);
                }

                let km = $('#km_actual').val();
                if (km) {
                    $('#proximo_kilometraje').val(parseInt(km) + 5000);
                }
            }
        }
    </script>

@endsection
