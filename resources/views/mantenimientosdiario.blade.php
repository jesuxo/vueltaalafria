{{-- resources/views/mantenimientos/diario.blade.php --}}
@extends('layouts.master')
@section('title')
    Mantenimientos del Día - {{ Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
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

        body {
            background-color: #faf7f5;
            overflow-x: hidden;
        }

        .container-fluid {
            padding: 0 15px;
        }

        /* Layout de dos columnas */
        .main-container {
            display: flex;
            height: calc(100vh - 120px);
            gap: 15px;
            padding: 15px 0;
        }

        .columna-lista {
            flex: 0 0 40%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .columna-lista.colapsada {
            flex: 0 0 30%;
        }

        .columna-detalle {
            flex: 0 0 60%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .columna-detalle.placeholder {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
        }

        /* Header de columnas */
        .column-header {
            padding: 15px 20px;
            background: var(--pastel-purple);
            border-bottom: 2px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .column-header h5 {
            margin: 0;
            color: #2c3e50;
        }

        /* Lista de mantenimientos */
        .mantenimientos-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .mantenimiento-item {
            background: white;
            border: 1px solid #f0eae5;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .mantenimiento-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .mantenimiento-item.selected {
            border-left-color: #9fb7c9;
            background: var(--pastel-blue);
        }

        .tipo-badge {
            background: var(--pastel-green);
            color: #2c3e50;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        /* Contenido del detalle */
        .detalle-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        /* Botones de acción rápida */
        .quick-actions {
            display: flex;
            gap: 5px;
            margin-top: 8px;
        }

        .quick-actions .btn-sm {
            padding: 2px 8px;
            font-size: 0.8rem;
        }

        /* Filtros superiores */
        .filtros-bar {
            background: white;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            align-items: center;
        }

        /* Loading overlay */
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
    </style>
@endsection

@section('content')
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-secondary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <!-- Barra de filtros y estadísticas -->
    <div class="filtros-bar">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text" style="background: var(--pastel-blue);">
                    <i class="ri-calendar-line"></i>
                </span>
                <input type="date" class="form-control" id="fechaSelector" value="{{ $fecha }}">
            </div>

            <select class="form-select" id="filtroVendedor" style="width: 200px;">
                <option value="">Todos los vendedores</option>
                @foreach($vendedores as $vendedor)
                    <option value="{{ $vendedor->descrip }}">{{ $vendedor->descrip }}</option>
                @endforeach
            </select>

            <select class="form-select" id="filtroTipo" style="width: 200px;">
                <option value="">Todos los tipos</option>
                <option value="cambio_aceite">Cambio de Aceite</option>
                <option value="cambio_filtro_aceite">Filtro Aceite</option>
                <option value="cambio_filtro_gasolina">Filtro Gasolina</option>
                <option value="cambio_filtro_aire">Filtro Aire</option>
                <option value="mantenimiento_inyectores">Inyectores</option>
                <option value="bateria">Batería</option>
            </select>

            <div class="ms-auto">
                <span class="badge" style="background: var(--pastel-purple); padding: 8px 12px;">
                    <i class="ri-calendar-check-line"></i> Total: {{ $estadisticas['total'] }}
                </span>
                <span class="badge" style="background: var(--pastel-green); padding: 8px 12px;">
                    <i class="ri-oil-line"></i> Aceites: {{ $estadisticas['cambio_aceite'] }}
                </span>
                <span class="badge" style="background: var(--pastel-pink); padding: 8px 12px;">
                    <i class="ri-money-dollar-circle-line"></i> ${{ number_format($estadisticas['total_ventas'], 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Contenedor principal de dos columnas -->
    <div class="main-container">
        <!-- Columna izquierda: Lista de mantenimientos -->
        <div class="columna-lista" id="columnaLista">
            <div class="column-header">
                <h5><i class="ri-list-check"></i> Mantenimientos del día</h5>
                <span class="badge" style="background: white; color: blueviolet;">{{ count($mantenimientos) }} registros</span>
            </div>
            <div class="mantenimientos-list" id="mantenimientosList">
                @forelse($mantenimientos as $m)
                    <div class="mantenimiento-item" data-id="{{ $m->id }}" onclick="cargarDetalle({{ $m->id }})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $m->vehiculo->marca }} {{ $m->vehiculo->modelo }}</strong>
                                <span class="badge ms-2 " style="background: var(--pastel-purple);color: blueviolet;">
                                    {{ $m->vehiculo->identificacion }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $m->fechaformat }} {{$m->horaformated}} </small>
                        </div>

                        <div class="mt-2">
                            <span class="tipo-badge">
                                @switch($m->tipo_mantenimiento)
                                    @case('cambio_aceite') 🔧 Cambio Aceite @break
                                    @case('cambio_filtro_aceite') 🔧 Filtro Aceite @break
                                    @case('cambio_filtro_gasolina') 🔧 Filtro Gasolina @break
                                    @case('cambio_filtro_aire') 🔧 Filtro Aire @break
                                    @case('mantenimiento_inyectores') 🔧 Inyectores @break
                                    @case('bateria') 🔋 Batería @break
                                    @default 🔧 {{ $m->tipo_mantenimiento }}
                                @endswitch
                            </span>
                            <span class="ms-2">
                                <i class="ri-user-line"></i> {{ $m->vehiculo->cliente->descrip }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                @if($m->vendedor)
                                    <small><i class="ri-user-star-line"></i> {{ $m->vendedor->descrip }}</small>
                                @endif
                            </div>
                            <div>
                                @if($m->observaciones)
                                    <small><i class="bi bi-info-circle"></i> {{ $m->observaciones  }}</small>
                                @endif
                            </div>

                        </div>


                    </div>
                @empty
                    <div class="text-center p-4">
                        <i class="ri-inbox-line" style="font-size: 3rem; color: #b8d9c6;"></i>
                        <p class="mt-2">No hay mantenimientos para esta fecha</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Columna derecha: Detalle del mantenimiento seleccionado -->
        <div class="columna-detalle placeholder" id="columnaDetalle">
            <div class="text-center p-5">
                <i class="ri-arrow-left-s-line" style="font-size: 4rem; color: #cbd5e0;"></i>
                <h5>Seleccione un mantenimiento</h5>
                <p class="text-muted">Haga clic en cualquier item de la lista para ver sus detalles</p>
            </div>
        </div>
    </div>

    <!-- Modal para edición rápida -->
    <div class="modal fade" id="modalEditarRapido" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--pastel-purple);">
                    <h5 class="modal-title">Editar Mantenimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalEditarContent">
                    <!-- Se carga vía AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        let mantenimientoActual = null;
        let timeoutId = null;

        $(document).ready(function() {
            // Cambiar fecha
            $('#fechaSelector').change(function() {
                window.location.href = '{{ route("mantenimientos.diario") }}?fecha=' + $(this).val();
            });

            // Filtros en tiempo real
            $('#filtroVendedor, #filtroTipo').change(filtrarMantenimientos);

            // Búsqueda por cliente/placa
            $('#busquedaRapida').on('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(filtrarMantenimientos, 300);
            });
        });

        function filtrarMantenimientos() {
            let vendedor = $('#filtroVendedor').val();
            let tipo = $('#filtroTipo').val();
            let busqueda = $('#busquedaRapida').val()?.toLowerCase() || '';

            $('.mantenimiento-item').each(function() {
                let mostrar = true;
                let item = $(this);

                // Filtro por vendedor
                if (vendedor && !item.find('.ri-user-star-line').parent().text().includes(vendedor)) {
                    mostrar = false;
                }

                // Filtro por tipo
                if (tipo && !item.find('.tipo-badge').text().toLowerCase().includes(tipo.replace('_', ' '))) {
                    mostrar = false;
                }

                // Búsqueda por texto
                if (busqueda) {
                    let texto = item.text().toLowerCase();
                    if (!texto.includes(busqueda)) {
                        mostrar = false;
                    }
                }

                item.toggle(mostrar);
            });
        }

        function cargarDetalle(id) {
            // Marcar item como seleccionado
            $('.mantenimiento-item').removeClass('selected');
            $(`.mantenimiento-item[data-id="${id}"]`).addClass('selected');

            // Colapsar un poco la columna izquierda
            $('#columnaLista').addClass('colapsada');

            // Mostrar loading
            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: `/mantenimientos/diario/detalle/${id}`,
                method: 'GET',
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success) {
                        $('#columnaDetalle').removeClass('placeholder').html(response.html);
                        mantenimientoActual = response.mantenimiento;
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    alert('Error al cargar el detalle');
                }
            });
        }

        function actualizarCampo(id, campo, valor) {
            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: `/mantenimientos/diario/${id}/actualizar-campo`,
                method: 'POST',
                data: {
                    campo: campo,
                    valor: valor,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success) {
                        // Actualizar el valor en la lista
                        $(`.mantenimiento-item[data-id="${id}"]`).find(`.${campo}-value`).text(response.display);

                        // Mostrar notificación
                        mostrarNotificacion('✅ Campo actualizado', 'success');
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    alert('Error al actualizar');
                }
            });
        }

        function abrirModalEditar(id) {
            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: `/mantenimientos/diario/detalle/${id}`,
                method: 'GET',
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success) {
                        // Crear formulario de edición rápido
                        let html = `
                            <form id="formEditarRapido">
                                <input type="hidden" name="id" value="${id}">
                                <div class="mb-3">
                                    <label>Observaciones</label>
                                    <textarea class="form-control" name="observaciones" rows="3">${response.mantenimiento.observaciones || ''}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Kilometraje</label>
                                    <input type="number" class="form-control" name="kilometraje" value="${response.mantenimiento.kilometraje || ''}">
                                </div>
                                <div class="mb-3">
                                    <label>Fecha próxima</label>
                                    <input type="date" class="form-control" name="proximo_mantenimiento" value="${response.mantenimiento.proximo_mantenimiento || ''}">
                                </div>
                                <button type="submit" class="btn pastel-btn-success">Guardar Cambios</button>
                            </form>
                        `;

                        $('#modalEditarContent').html(html);
                        $('#modalEditarRapido').modal('show');

                        // Manejar envío del formulario
                        $('#formEditarRapido').submit(function(e) {
                            e.preventDefault();
                            // Aquí iría la lógica para guardar
                        });
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    alert('Error al cargar');
                }
            });
        }

        function mostrarNotificacion(mensaje, tipo) {
            let notificacion = $(`
                <div style="position: fixed; top: 20px; right: 20px; z-index: 10000;
                            background: ${tipo == 'success' ? '#c1e0cd' : '#ffe6e6'};
                            color: #2c3e50; padding: 15px; border-radius: 10px;
                            box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    ${mensaje}
                </div>
            `);

            $('body').append(notificacion);

            setTimeout(() => {
                notificacion.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    </script>
@endsection
