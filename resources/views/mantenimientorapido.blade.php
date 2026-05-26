@extends('layouts.master')
@section('title')
    Registro Rápido de Mantenimiento
@endsection
@section('css')

        <style>
        .btn-file {
            transition: all 0.2s ease;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-file:active {
            transform: scale(0.95);
            background: #b3c9da !important;
            opacity: 0.8;
        }

        /* Feedback táctil para iOS */
        @media (hover: none) and (pointer: coarse) {
            .btn-file {
                -webkit-tap-highlight-color: rgba(0,114,197,0.3);
            }

            .btn-file:active {
                background: #b3c9da !important;
            }
        }

        /* Colores pastel */
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
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border-bottom: none;
        }

        .step-card {
            transition: all 0.3s ease;
            border-left: 4px solid #ddd;
            border-radius: 10px;
            background: white;
        }

        .step-card.active {
            border-left-color: #9fb7c9;
            background: var(--pastel-blue);
        }

        .step-number {
            width: 30px;
            height: 30px;
            background-color: #c5d5e0;
            color: #4a5568;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .step-number.active {
            background-color: #9fb7c9;
            color: white;
        }

        .step-number.completed {
            background-color: #b8d9c6;
            color: white;
        }

        .quick-search {
            font-size: 1.5rem;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
        }

        .quick-search:focus {
            border-color: #9fb7c9;
            box-shadow: 0 0 0 3px rgba(159, 183, 201, 0.2);
        }

        .result-card {
            background: white;
            border-radius: 12px;
            transition: all 0.2s;
            border: 1px solid #f0eae5;
        }

        .result-card:hover {
            transform: translateY(-2px);
            background: #fefcf9;
        }

        /* Estilos para la lista de vehículos */
        .vehicle-item {
            background: white;
            border-radius: 10px;
            border-left: 4px solid #b8d9c6;
            transition: all 0.2s;
            cursor: pointer;
        }

        .vehicle-item:hover {
            background: var(--pastel-green);
            transform: translateX(5px);
        }

        .vehicle-item.selected {
            background: var(--pastel-blue);
            border-left-color: #9fb7c9;
        }

        .badge-pastel-blue {
            background: var(--pastel-blue);
            color: #2c3e50;
        }

        .badge-pastel-green {
            background: var(--pastel-green);
            color: #2c3e50;
        }

        .badge-pastel-yellow {
            background: var(--pastel-yellow);
            color: #2c3e50;
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
            background: #b3c9da !important;
            color: #2c3e50 !important;
        }

        .pastel-btn-success {
            background: #c1e0cd;
            border: none;
            color: #2c3e50;
        }

        .pastel-btn-success:hover {
            background: #aed0bd;
            color: #2c3e50;
        }

        .search-hint {
            background: #fff6e5;
            border-radius: 8px;
            padding: 10px;
            color: #7f8c8d;
        }

        /* Efectos de carga mejorados */
        .loading-overlay {
            backdrop-filter: blur(5px);
        }

        /* Animación para los items seleccionados */
        .vehicle-item {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .vehicle-item.selected {
            border: 2px solid #9fb7c9;
            background: var(--pastel-blue) !important;
            transform: translateX(8px);
        }

        /* Badges mejorados */
        .badge-info-pastel {
            background: var(--pastel-blue);
            color: #2c3e50;
            padding: 5px 10px;
            border-radius: 20px;
        }

        /* Tooltips personalizados */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #2c3e50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            display: none;
        }

        [data-tooltip]:hover:before {
            display: block;
        }

        /* Animación de entrada para resultados */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #singleResult,
        #multipleResults {
            animation: fadeInUp 0.4s ease-out;
        }

        /* Estilos mejorados para el área de carga */
        .upload-area {
            border: 2px dashed #9fb7c9;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: white;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: all 0.3s ease;
        }

        .upload-area:active {
            background: var(--pastel-blue);
            transform: scale(0.98);
        }

        .btn-file {
            display: inline-block;
            padding: 10px 20px;
            background: #c5d9e8;
            border-radius: 8px;
            color: #2c3e50;
            font-weight: 500;
            margin: 5px;
            -webkit-touch-callout: none;
            transition: all 0.2s ease;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-file:active {
            transform: scale(0.95);
            background: #b3c9da !important;
            opacity: 0.8;
        }

        /* ========== ESTILOS PARA MÓVILES (CORREGIDOS) ========== */
        @media (hover: none) and (pointer: coarse) {
            .btn-file {
                -webkit-tap-highlight-color: rgba(0,114,197,0.3);
                padding: 12px 24px;
                font-size: 1.1rem;
            }

            .btn-file:active {
                background: #b3c9da !important;
                transform: scale(0.95);
            }

            /* Botones de cámara */
            #btnTomarFoto,
            #btnSeleccionarFotos {
                -webkit-tap-highlight-color: rgba(0,114,197,0.3);
                touch-action: manipulation;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            #btnTomarFoto:active,
            #btnSeleccionarFotos:active {
                transform: scale(0.97);
                background-color: #e0e0e0 !important;
            }

            .btn-foto {
                padding: 15px 10px;
                font-size: 1rem;
            }

            /* Área de subida - VISIBLE en móviles */
            .upload-area {
                padding: 20px 15px !important;
                display: block !important;
            }

            /* Ocultar solo el texto de "arrastrar" en móviles */
            .upload-area p.text-muted {
                display: none;
            }

            /* Ajustar íconos en móviles */
            .upload-area i {
                font-size: 36px;
            }

            .upload-area h6 {
                font-size: 1rem;
                margin-top: 8px;
            }
        }

        /* Estilos para escritorio */
        @media (min-width: 769px) {
            .upload-area p.text-muted {
                display: block;
            }
        }

        /* Asegurar que el área de foto del vehículo sea visible en todos los dispositivos */
        #editVehiculoFotoForm {
            display: block !important;
        }

        #editVehiculoFotoForm .upload-area {
            display: block !important;
        }

        .foto-thumbnail {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .foto-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .foto-thumbnail .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .foto-thumbnail:hover .remove-btn {
            opacity: 1;
        }

        /* Estilos para checkboxes de tipos */
        .tipo-checkbox-card {
            background: white;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #e0e0e0;
            transition: all 0.2s;
        }

        .tipo-checkbox-card:hover {
            background: var(--pastel-blue);
            border-color: #9fb7c9;
        }

        .tipo-checkbox-card input[type="checkbox"] {
            margin-right: 8px;
        }

        /* Estilos para autocompletado predictivo */
        .predictive-dropdown {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .predictive-item {
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }

        .predictive-item:hover {
            background: var(--pastel-blue);
        }

        .predictive-item:last-child {
            border-bottom: none;
        }

        .position-relative {
            position: relative;
        }

        /* Estilos para selección de cliente encontrado */
        .cliente-encontrado-card {
            background: linear-gradient(135deg, var(--pastel-green) 0%, #d4edda 100%);
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            animation: pulse-green 1.5s ease-in-out;
        }

        @keyframes pulse-green {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
                transform: scale(1.02);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
                transform: scale(1);
            }
        }

        .cliente-icono-grande {
            font-size: 60px;
            color: #28a745;
            margin-right: 15px;
        }

        .cliente-info-destacada {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
        }

        .btn-registrar-vehiculo {
            background: #dbf2e0;
            border: none;
            color: white;
            padding: 12px 25px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-registrar-vehiculo:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            background: #dbf2e0;
        }

        .btn-registrar-vehiculo i {
            font-size: 1.2rem;
            margin-right: 8px;
        }

        .vehiculo-sugerido-card {
            background: var(--pastel-blue);
            border-left: 4px solid #28a745;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .estado-icono {
            width: 50px;
            height: 50px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .estado-icono i {
            font-size: 30px;
            color: white;
        }

        .badge-vehiculo-pendiente {
            background: #ffc107;
            color: #856404;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: bold;
            animation: blink 1s ease-in-out infinite;
        }

        @keyframes blink {
            0%,
            100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        /* Estilos para botones de cámara */
        .btn-foto {
            -webkit-tap-highlight-color: rgba(0, 114, 197, 0.3);
            touch-action: manipulation;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-foto:active {
            transform: scale(0.97);
            background-color: #e0e0e0 !important;
        }

        /* Corrección de orientación de fotos */
        .foto-evidencia img,
        #vehiculoFotoPreview,
        #fotoGrande,
        .foto-item img {
            image-orientation: from-image;
        }

        /* Estilos para la galería de fotos */
        .galeria-fotos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        /* Asegurar que el contenedor de foto del vehículo sea visible */
        #editVehiculoFotoForm {
            margin: 15px;
            padding: 15px;
            background: var(--pastel-blue);
            border-radius: 12px;
        }

        /* Responsive para pantallas muy pequeñas */
        @media (max-width: 576px) {
            .galeria-fotos {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .btn-registrar-vehiculo {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .cliente-encontrado-card {
                padding: 15px;
            }

            .badge-vehiculo-pendiente {
                font-size: 0.8rem;
                padding: 5px 10px;
            }
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
                <div class="card-header" >
                    <h4 class="mb-0" style="color: #4a5568;"><i class="ri-flashlight-fill"></i> REGISTRO EXPRESS DE MANTENIMIENTO</h4>
                    <small style="color: #5d6d7e;">Proceso rápido en 3 pasos - Busque por placa, serial motor o chasis</small>
                </div>
                <div class="card-body" style="background: #fffcf9;">

                    <!-- Paso 1: Búsqueda -->
                    <div id="step1">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="input-group input-group-lg">
                                <span class="input-group-text" style="background: var(--pastel-blue); border: none;">
                                    <i class="ri-search-line"></i>
                                </span>
                                    <input type="text" class="form-control quick-search" id="placa"
                                           placeholder="Ingrese placa, serial motor o chasis (puede escribir solo una parte)"
                                           autofocus
                                           style="background: white; text-transform: uppercase;">
                                    <button class="btn pastel-btn-primary" type="button" id="btnBuscar">
                                        <i class="ri-search-line"></i> Buscar
                                    </button>
                                </div>
                                <div class="search-hint mt-2">
                                    <i class="ri-information-line"></i>
                                    Puede buscar escribiendo solo una parte de la placa (ej: "ABC" encontrará ABC123, ABC789, etc.)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Resultados y selección -->
                    <div id="step2" style="display: none;">
                        <!-- Contenedor para múltiples resultados -->
                        <div id="multipleResults" style="display: none;">
                            <div class="card" style="background: var(--pastel-yellow);">
                                <div class="card-header" style="background: var(--pastel-peach);">
                                    <h5 class="mb-0"><i class="ri-car-line"></i> Se encontraron varios vehículos</h5>
                                    <small>Seleccione el vehículo correcto:</small>
                                </div>
                                <div class="card-body" id="vehiculosList">
                                    <!-- Se llena vía AJAX -->
                                </div>
                            </div>
                        </div>


                        <div id="singleResult" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card result-card">
                                        <div class="card-header" style="background: var(--pastel-purple); display: flex; justify-content: space-between; align-items: center;">
                                            <h5 class="mb-0"><i class="ri-user-line"></i> Datos del Cliente</h5>
                                            <button type="button" class="btn btn-sm pastel-btn-primary" onclick="editarCliente()">
                                                <i class="ri-edit-line"></i> Editar
                                            </button>
                                        </div>
                                        <div class="card-body" id="clienteData">
                                            <!-- Se llena vía AJAX -->
                                        </div>

                                        <!-- Formulario de edición de cliente (oculto inicialmente) -->
                                        <div id="editClienteForm" style="display: none; padding: 15px; background: var(--pastel-pink); margin: 15px; border-radius: 12px 12px 12px 12px;">
                                            <div class="row">
                                                <div class="col-md-12 mb-2">
                                                    <label>Cédula/RIF</label>
                                                    <input type="text" class="form-control" id="editClienteCedula">
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <label>Nombre completo</label>
                                                    <input type="text" class="form-control" id="editClienteNombre">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Teléfono</label>
                                                    <input type="text" class="form-control" id="editClienteTelefono">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control" id="editClienteEmail">
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <button class="btn  btn-success btn-sm" onclick="guardarEdicionCliente()">
                                                        <i class="ri-save-line"></i> Guardar
                                                    </button>
                                                    <button class="btn btn-secondary btn-sm" onclick="cancelarEdicionCliente()">
                                                        <i class="ri-close-line"></i> Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Datos del Vehículo -->
                                    <div class="card result-card" id="vehiculoDataid">
                                        <div class="card-header" style="background: var(--pastel-green); display: flex; justify-content: space-between; align-items: center;">
                                            <h5 class="mb-0"><i class="ri-car-line"></i> Datos del Vehículo</h5>
                                            <button type="button" class="btn btn-sm pastel-btn-primary" onclick="editarVehiculo()">
                                                <i class="ri-edit-line"></i> Editar
                                            </button>
                                        </div>
                                        <div class="card-body" id="vehiculoData">
                                            <!-- Se llena vía AJAX -->
                                        </div>

                                        <!-- FORMULARIO DE EDICIÓN - DEBE ESTAR AQUÍ DENTRO -->
                                        <div id="editVehiculoForm" style="display: none; padding: 15px; background: var(--pastel-blue); border-radius: 0 0 12px 12px;">
                                            <h6><i class="ri-edit-line"></i> Editar Vehículo</h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label>Tipo *</label>
                                                    <select class="form-select" id="editVehiculoTipo">
                                                        <option value="">Seleccione tipo</option>
                                                        @foreach($tipos_vehiculo as $tipo)
                                                            <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Marca *</label>
                                                    <input type="text" class="form-control" id="editVehiculoMarca">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Modelo *</label>
                                                    <input type="text" class="form-control" id="editVehiculoModelo">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Año</label>
                                                    <input type="number" class="form-control" id="editVehiculoYear" min="1900" max="2100">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Placa *</label>
                                                    <input type="text" class="form-control" id="editVehiculoPlaca">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Serial Motor</label>
                                                    <input type="text" class="form-control" id="editVehiculoSerialMotor">
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <label>Serial Chasis</label>
                                                    <input type="text" class="form-control" id="editVehiculoSerialChasis">
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <label>Observaciones</label>
                                                    <textarea class="form-control" id="editVehiculoObservaciones" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <button class="btn btn-success btn-sm" onclick="guardarEdicionVehiculo()">
                                                        <i class="ri-save-line"></i> Guardar
                                                    </button>
                                                    <button class="btn btn-secondary btn-sm" onclick="cancelarEdicionVehiculo()">
                                                        <i class="ri-close-line"></i> Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- FIN FORMULARIO DE EDICIÓN -->

                                        <div class="col-md-12 mb-2">
                                            <div id="editVehiculoFotoForm" style="display: none; padding: 15px; background: var(--pastel-blue); margin:15px;  border-radius: 12px 12px 12px 12px;">
                                                <h6><i class="ri-camera-line"></i> Fotos del Vehículo</h6>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="upload-area" id="vehiculoFotoPrincipalDropArea" style="border: 2px dashed #9fb7c9; border-radius: 10px; padding: 15px; text-align: center; background: white; cursor: pointer; margin-bottom: 10px;">
                                                            <i class="ri-camera-line" style="font-size: 24px; color: #9fb7c9;"></i>
                                                            <h6>Foto Principal</h6>
                                                            <small class="text-muted">Haga clic para seleccionar</small>
                                                            <input type="file" id="vehiculoFotoPrincipalInput" accept="image/jpeg,image/png" style="display: none;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3" id="nuevoClienteForm" style="display: none; background: var(--pastel-pink);">
                            <div class="card-header" style="background: #ffd9e6;">
                                <h5 class="mb-0"><i class="ri-user-add-line"></i> Registrar Nuevo Cliente</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <input type="text" class="form-control" id="nuevaCedula" placeholder="Cédula/RIF *">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <input type="text" class="form-control" id="nuevoNombre" placeholder="Nombre completo *">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <input type="text" class="form-control" id="nuevoTelefono" placeholder="Teléfono">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="email" class="form-control" id="nuevoEmail" placeholder="Email (opcional)">
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <button class="btn pastel-btn-success" id="btnGuardarCliente">
                                            <i class="ri-save-line"></i> Guardar Cliente
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3" id="nuevoVehiculoForm" style="display: none; background: linear-gradient(135deg, var(--pastel-blue) 0%, #e6f3ff 100%); border: 2px solid #9fb7c9;">
                            <div class="card-header" style="background: linear-gradient(135deg, #c5d9e8 0%, #b3c9da 100%);">
                                <h5 class="mb-0">
                                    <i class="ri-car-line"></i> Registrar Nuevo Vehículo
                                </h5>
                                <small id="placaVehiculoNuevo" class="text-muted"></small>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning mb-3">
                                    <i class="ri-information-line"></i>
                                    <strong>¡Importante!</strong> Complete los datos del vehículo para asociarlo al cliente.
                                    Una vez creado el vehículo, podrá continuar con el mantenimiento.
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="fw-bold">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-select" id="nuevoTipoVehiculo">
                                            <option value="">Seleccione tipo</option>
                                            @foreach($tipos_vehiculo as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2 position-relative">
                                        <label class="fw-bold">Marca <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nuevaMarca" placeholder="Ej: TOYOTA, FORD, CHEVROLET" autocomplete="off" style="text-transform: uppercase;">
                                        <div id="marcaPredictive" class="predictive-dropdown"></div>
                                    </div>
                                    <div class="col-md-3 mb-2 position-relative">
                                        <label class="fw-bold">Modelo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nuevoModelo" placeholder="Ej: COROLLA, FIESTA, CRUZE" autocomplete="off" style="text-transform: uppercase;">
                                        <div id="modeloPredictive" class="predictive-dropdown"></div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label>Año</label>
                                        <input type="number" class="form-control" id="nuevoYear" placeholder="Ej: 2020" min="1900" max="2100">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="fw-bold">Placa/Identificación <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nuevaPlaca" placeholder="Ej: ABC123" readonly style="text-transform: uppercase; background: #f0f0f0;">
                                        <small class="text-muted">La placa se ha detectado automáticamente</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label>Serial Motor</label>
                                        <input type="text" class="form-control" id="nuevoSerialMotor" placeholder="Número de motor" style="text-transform: uppercase;">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Serial Chasis</label>
                                        <input type="text" class="form-control" id="nuevoSerialChasis" placeholder="Número de chasis" style="text-transform: uppercase;">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Observaciones</label>
                                        <textarea class="form-control" id="nuevasObservaciones" placeholder="Notas adicionales del vehículo..." rows="2"></textarea>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-secondary" onclick="cancelarRegistroVehiculo()">
                                                <i class="ri-close-line"></i> Cancelar
                                            </button>
                                            <button class="btn pastel-btn-success btn-lg" id="btnGuardarVehiculo">
                                                <i class="ri-save-line"></i> Guardar Vehículo y Continuar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button class="btn pastel-btn-primary" id="btnContinuarMantenimiento" style="display: none;">
                                <i class="ri-arrow-right-line"></i> Continuar al Mantenimiento
                            </button>
                            <button class="btn btn-secondary" id="btnNuevaBusqueda">
                                <i class="ri-search-line"></i> Nueva Búsqueda
                            </button>
                        </div>
                    </div>

                    <!-- Paso 3: Registrar mantenimiento -->
                    <div id="step3" style="display: none;">
                        <div class="card" style="background: var(--pastel-green);">
                            <div class="card-header" style="background: #c1e0cd;">
                                <h5 class="mb-0">Registrar Mantenimiento</h5>
                            </div>
                            <div class="card-body">
                                <form id="mantenimientoForm">
                                    <input type="hidden" id="mantenimiento_vehiculo_id" name="fk_vehiculo">

                                    <!-- Fila 1: Fecha, Hora, Kilometraje -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label>Fecha *</label>
                                            <input type="date" class="form-control" name="fecha_mantenimiento"
                                                   value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Hora <small class="text-muted">(hora local del cliente)</small></label>
                                            <div class="input-group">
                                                <input type="time" class="form-control" name="hora_mantenimiento"
                                                       id="hora_mantenimiento" readonly>
                                                <span class="input-group-text" style="background: var(--pastel-blue);">
                                                    <i class="ri-time-line"></i>
                                                </span>
                                            </div>
                                            <small class="text-muted" id="hora_detalle"></small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Kilometraje <small class="text-muted">(opcional)</small></label>
                                            <input type="number" class="form-control" name="kilometraje" id="km_actual" placeholder="Opcional">
                                        </div>
                                    </div>

                                    <!-- Fila 2: Vendedor -->
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label><i class="ri-user-star-line"></i> Vendedor que atendió</label>
                                            <select class="form-select" name="codvend" required id="vendedorSelect">
                                                <option value="">Seleccione el vendedor</option>
                                                @foreach($vendedores as $vendedor)
                                                    <option value="{{ $vendedor->codvend }}">
                                                        {{ strtoupper($vendedor->descrip) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Seleccione el vendedor que realizó la venta o atención</small>
                                        </div>
                                    </div>

                                    <!-- NUEVA SECCIÓN: Múltiples tipos de mantenimiento -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="fw-bold"><i class="ri-tools-line"></i> Tipos de Mantenimiento Realizados *</label>
                                            <div class="card" style="background: var(--pastel-yellow);">
                                                <div class="card-body">
                                                    <div class="row" id="tiposMantenimientoContainer">
                                                        @foreach($tipos_mantenimiento as $value => $label)
                                                            <div class="col-md-4 col-lg-3 mb-2">
                                                                <div class="form-check tipo-checkbox-card">
                                                                    <input class="form-check-input tipo-mantenimiento-check" type="checkbox"
                                                                           style="margin-left: -12px;"
                                                                           name="tipos_mantenimiento[]" value="{{ $value }}" id="tipo_{{ $value }}">
                                                                    <label class="form-check-label" for="tipo_{{ $value }}">
                                                                        {{ $label }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <!-- Campo para especificar "Otros" -->
                                                    <div class="row mt-2" id="otrosTipoContainer" style="display: none;">
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="otro_tipo_descripcion"
                                                                   placeholder="Especifique otro tipo de mantenimiento">
                                                        </div>
                                                    </div>

                                                    <!-- Botón para calcular próximo mantenimiento -->
                                                    <div class="row mt-3" id="calcularProximoBtn" style="display: none;">
                                                        <div class="col-md-12">
                                                            <button type="button" class="btn pastel-btn-primary" onclick="calcularProximoOpcional()">
                                                                <i class="ri-calculator-line"></i> Calcular Próximo Mantenimiento (+6 meses / +5000 km)
                                                            </button>
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
                                            <div class="card" style="background: var(--pastel-yellow);">
                                                <div class="card-body">
                                                    <!-- Lista de productos agregados -->
                                                    <div id="productosList" class="mb-3">
                                                        <div class="text-muted text-center p-3">
                                                            <i class="ri-information-line"></i> No hay productos agregados
                                                        </div>
                                                    </div>

                                                    <!-- Productos temporales (nuevos) -->
                                                    <div id="productosTempList"></div>

                                                    <!-- Buscador de productos con escáner -->
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" id="buscadorProducto"
                                                               placeholder="Buscar producto por nombre, código o referencia...">
                                                        <button class="btn pastel-btn-primary" type="button" id="btnBuscarProducto">
                                                            <i class="ri-search-line"></i>
                                                        </button>
                                                        <button class="btn pastel-btn-success" type="button" id="btnEscanearCodigo">
                                                            <i class="ri-qr-code-line"></i> Escanear
                                                        </button>
                                                    </div>

                                                    <!-- Resultados de búsqueda -->
                                                    <div id="resultadosProductos" class="list-group"
                                                         style="max-height: 200px; overflow-y: auto; display: none;">
                                                    </div>

                                                    <!-- Opción para agregar producto manual -->
                                                    <div class="mt-2">
                                                        <a href="javascript:;" id="btnAgregarManual" style="color: #0072c5;">
                                                            <i class="ri-add-line"></i> Agregar producto manualmente
                                                        </a>
                                                    </div>

                                                    <!-- Formulario manual (oculto) -->
                                                    <div id="formProductoManual" style="display: none; margin-top: 10px;">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm"
                                                                       id="productoManualDesc" placeholder="Descripción del producto *">
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
                                                                <button class="btn btn-sm pastel-btn-success" type="button" id="btnGuardarProductoManual">
                                                                    <i class="ri-check-line"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECCIÓN PRÓXIMO MANTENIMIENTO (ahora opcional) -->
                                    <div class="row" id="proximoMantenimientoSection" style="display: none;">
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>⏰ Próximo Mantenimiento <small class="text-muted">(Opcional)</small></h6>
                                            <small class="text-muted">Complete solo si desea registrar una recomendación</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Fecha próxima sugerida</label>
                                            <input type="date" class="form-control" name="proximo_mantenimiento" id="proximo_mantenimiento">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Kilometraje próximo sugerido</label>
                                            <input type="number" class="form-control" name="proximo_kilometraje" id="proximo_kilometraje">
                                        </div>
                                    </div>

                                    <!-- OBSERVACIONES -->
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label>📝 Observaciones</label>
                                            <textarea class="form-control" name="observaciones" rows="3"
                                                      placeholder="Notas adicionales, recomendaciones, etc."></textarea>
                                        </div>
                                    </div>

                                    <!-- ============ SECCIÓN DE FOTOS OPTIMIZADA PARA IPHONE ============ -->
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card" style="background: var(--pastel-yellow);">
                                                <div class="card-header" style="background: var(--pastel-peach);">
                                                    <h6 class="mb-0"><i class="ri-camera-line"></i> Fotos de Evidencia</h6>
                                                    <small>Puede agregar fotos del cambio de aceite, productos usados, kilometraje, etc.</small>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <!-- Botones separados para mejor compatibilidad iOS -->
                                                            <div class="row g-2">
                                                                <div class="col-6">
                                                                    <button type="button" class="btn btn-light w-100 py-3 btn-foto" id="btnTomarFoto" style="border: 2px dashed #9fb7c9; border-radius: 10px;">
                                                                        <i class="ri-camera-line" style="font-size: 24px;"></i><br>
                                                                        <small>Tomar Foto</small>
                                                                    </button>
                                                                </div>
                                                                <div class="col-6">
                                                                    <button type="button" class="btn btn-light w-100 py-3 btn-foto" id="btnSeleccionarFotos" style="border: 2px dashed #9fb7c9; border-radius: 10px;">
                                                                        <i class="ri-image-line" style="font-size: 24px;"></i><br>
                                                                        <small>Galería</small>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="upload-area mt-3" id="dropArea" style="border: 2px dashed #9fb7c9; border-radius: 10px; padding: 20px; text-align: center; background: white;">
                                                                <i class="ri-upload-cloud-line" style="font-size: 32px; color: #9fb7c9;"></i>
                                                                <p class="text-muted mb-0 small">Arrastra fotos aquí (escritorio)</p>
                                                            </div>

                                                            <!-- Inputs ocultos separados -->
                                                            <input type="file" id="camaraInput" accept="image/*" capture="environment" style="display: none;">
                                                            <input type="file" id="galeriaInput" accept="image/jpeg,image/png" multiple style="display: none;">
                                                        </div>

                                                        <!-- Previsualización de fotos -->
                                                        <div class="col-md-12" id="fotosPreview"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ============ FIN SECCIÓN DE FOTOS ============ -->

                                    <!-- BOTONES DE ACCIÓN -->
                                    <div class="row mt-4">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btnsubmit btn-success btn-lg px-5" >
                                                <i class="ri-check-line"></i> Registrar Mantenimiento
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-lg px-5" id="btnCancelar">
                                                <i class="ri-close-line"></i> Cancelar
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="fotoPreviewTemplate">
        <div class="col-md-3 mb-3 foto-item" data-index="{index}">
            <div class="card" style="background: white;">
                <div class="position-relative">
                    <img src="{src}" class="card-img-top" style="height: 150px; object-fit: cover; border-radius: 10px 10px 0 0;" alt="Preview">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="eliminarFoto({index})">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
                <div class="card-body p-2">
                    <select class="form-select form-select-sm mb-2" name="tipo_foto_{index}" onchange="actualizarTipoFoto({index}, this.value)">
                        <option value="general">General</option>
                        <option value="cambio_aceite">Cambio de Aceite</option>
                        <option value="producto_usado">Producto Usado</option>
                        <option value="kilometraje">Kilometraje</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" name="descripcion_foto_{index}" placeholder="Descripción (opcional)">
                </div>
            </div>
        </div>
    </template>

    <!-- Modal para últimos mantenimientos -->
    <div class="modal fade" id="ultimosMantenimientosModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--pastel-purple);">
                    <h5 class="modal-title">Últimos Mantenimientos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="ultimosMantenimientosContent">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para escáner de código de barras -->
    <div class="modal fade" id="scannerModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--pastel-purple);">
                    <h5 class="modal-title">Escanear Código de Barras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="detenerEscanerCodigoBarras()"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="scannerView" style="width: 100%; height: 300px; background: #000;"></div>
                    <p class="text-center mt-2 text-muted">Enfoca el código de barras del producto</p>
                    <div id="scanResult" class="alert alert-success mx-3" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="detenerEscanerCodigoBarras()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
    <script>
        let vehiculoActual       = null;
        let clienteActual        = null;
        let vehiculosEncontrados = [];
        let productosTemp        = [];
        let fotosSeleccionadas   = [];
        let fotoIndex            = 0;
        let fotosBase64          = [];
        let vehiculoFotos        = [];
        let fotoPrincipal        = null;
        let scannerActive = false;
        let marcasCache = [];
        let modelosCache = [];

        $(document).ready(function() {

            function cancelarRegistroVehiculo() {
                if (confirm('¿Estás seguro de que deseas cancelar el registro del vehículo? Perderás los datos ingresados.')) {
                    $('#nuevoVehiculoForm').hide();
                    $('#nuevaPlaca').val('');
                    $('#placaVehiculoNuevo').text('');
                    $('#nuevoTipoVehiculo').val('');
                    $('#nuevaMarca').val('');
                    $('#nuevoModelo').val('');
                    $('#nuevoYear').val('');
                    $('#nuevoSerialMotor').val('');
                    $('#nuevoSerialChasis').val('');
                    $('#nuevasObservaciones').val('');

                    // Mostrar nuevamente el modal de búsqueda
                    const placa = sessionStorage.getItem('placaPendiente');
                    if (placa) {
                        mostrarModalBuscarCliente(placa);
                    }
                }
            }

            setupFotosInput();

            // Búsqueda al presionar ENTER
            $('#placa').keypress(function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    buscarVehiculo();
                }
            });

            setTimeout(function() {
                console.log('Verificando elementos:');
                console.log('- editVehiculoForm existe:', $('#editVehiculoForm').length > 0);
                console.log('- vehiculoActual:', vehiculoActual);
            }, 3000);


            const urlParams = new URLSearchParams(window.location.search);
            const placaParam = urlParams.get('placa');

            if (placaParam) {
                $('#placa').val(placaParam);
                setTimeout(function() {
                    buscarVehiculo();
                }, 500); // Pequeño delay para asegurar que todo esté cargado
                $('#placa').change();
            }

            $('#btnBuscar').click(buscarVehiculo);
            $('#btnNuevaBusqueda').click(resetToSearch);

            // Mostrar/ocultar campo "Otros" cuando se selecciona
            $(document).on('change', 'input[name="tipos_mantenimiento[]"]', function() {
                if ($(this).val() === 'otros' && $(this).is(':checked')) {
                    $('#otrosTipoContainer').fadeIn();
                } else if ($(this).val() === 'otros' && !$(this).is(':checked')) {
                    $('#otrosTipoContainer').fadeOut();
                }

                // Mostrar botón de cálculo si se selecciona cambio_aceite
                if ($('#tipo_cambio_aceite').is(':checked')) {
                    $('#calcularProximoBtn').fadeIn();
                } else {
                    // Si no hay cambio aceite, verificar si algún otro está seleccionado
                    if ($('input[name="tipos_mantenimiento[]"]:checked').length === 0) {
                        $('#calcularProximoBtn').fadeOut();
                    }
                }
            });

            // Auto-calcular próximo mantenimiento (opcional)
            $('#km_actual').change(calcularProximoOpcional);
            $('input[name="fecha_mantenimiento"]').change(calcularProximoOpcional);

            // Continuar a mantenimiento
            $('#btnContinuarMantenimiento').click(function() {
                actualizarHoraCliente();
                $('#step2').hide();
                $('#mantenimiento_vehiculo_id').val(vehiculoActual.id);
                $('#step3').fadeIn();
            });

            // Cancelar
            $('#btnCancelar').click(function() {
                resetToSearch();
            });

            // Guardar mantenimiento
            $('#mantenimientoForm').submit(function(e) {
                e.preventDefault();
                guardarMantenimiento();
            });

            // Productos
            $('#btnBuscarProducto').click(buscarProductos);
            $('#buscadorProducto').keypress(function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    buscarProductos();
                }
            });

            $('#btnEscanearCodigo').click(function() {
                iniciarEscanerCodigoBarras();
            });

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
                    codprod: 'MANUAL',
                    descripcion: desc,
                    referencia: $('#productoManualRef').val().trim(),
                    cantidad: parseFloat($('#productoManualCant').val()) || 1,
                    tipo: 'producto'
                };

                agregarProductoALista(producto);

                // Limpiar y ocultar formulario
                $('#productoManualDesc').val('');
                $('#productoManualRef').val('');
                $('#productoManualCant').val('1');
                $('#formProductoManual').hide();
            });

            // Atajo de teclado: Ctrl+Shift+M
            $(document).keydown(function(e) {
                if (e.ctrlKey && e.shiftKey && e.keyCode == 77) {
                    e.preventDefault();
                    window.location.href = '{{ route("mantenimiento.rapido") }}';
                }
            });

            // Efecto hover para items de vehículos
            $(document).on('mouseenter', '.vehicle-item', function() {
                $(this).css('transform', 'translateX(8px)');
            }).on('mouseleave', '.vehicle-item', function() {
                if (!$(this).hasClass('selected')) {
                    $(this).css('transform', 'translateX(0)');
                }
            });

            $('#mantenimientoForm').on('keypress', function(e) {
                if (e.which == 13 && !$(e.target).is('textarea')) {
                    e.preventDefault();
                }
            });

            actualizarHoraCliente();
            setInterval(actualizarHoraCliente, 60000);

            // ============ MANEJO DE FOTOS OPTIMIZADO PARA IPHONE ============
            const dropArea = document.getElementById('dropArea');
            const fotosInput = document.getElementById('fotosInput');
            const btnSeleccionarFotos = document.getElementById('btnSeleccionarFotos');

            if (dropArea && fotosInput) {
                // Para iPhone: usar el botón en lugar del área completa
                if (btnSeleccionarFotos) {
                    // Evento click para escritorio
                    btnSeleccionarFotos.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Botón clickeado');
                        fotosInput.click();
                    });

                    // Evento touch para iOS (con passive: false para mejor respuesta)
                    btnSeleccionarFotos.addEventListener('touchstart', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Touch detectado en iOS');
                        fotosInput.click();
                    }, { passive: false });

                    // Feedback visual al tocar
                    btnSeleccionarFotos.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.95)';
                        this.style.opacity = '0.8';
                    });

                    btnSeleccionarFotos.addEventListener('touchend', function() {
                        this.style.transform = 'scale(1)';
                        this.style.opacity = '1';
                    });

                    btnSeleccionarFotos.addEventListener('touchcancel', function() {
                        this.style.transform = 'scale(1)';
                        this.style.opacity = '1';
                    });
                }

                // Mantener drag & drop para desktop
                dropArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropArea.style.background = 'var(--pastel-blue)';
                    dropArea.style.borderColor = '#0072c5';
                });

                dropArea.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    dropArea.style.background = 'white';
                    dropArea.style.borderColor = '#9fb7c9';
                });

                dropArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropArea.style.background = 'white';
                    dropArea.style.borderColor = '#9fb7c9';

                    if (e.dataTransfer.files.length > 0) {
                        handleFiles(e.dataTransfer.files);
                    }
                });

                // Evento change para todos los dispositivos
                fotosInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        console.log(`${e.target.files.length} archivo(s) seleccionado(s)`);
                        handleFiles(e.target.files);
                    }
                });

                // Para debug en iOS
                if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
                    console.log('Dispositivo iOS detectado - Modo táctil optimizado');

                    // Asegurar que el botón sea claramente clickeable en iOS
                    if (btnSeleccionarFotos) {
                        btnSeleccionarFotos.style.cursor = 'pointer';
                        btnSeleccionarFotos.style.userSelect = 'none';
                        btnSeleccionarFotos.style.webkitTapHighlightColor = 'rgba(0,114,197,0.3)';
                    }
                }
            }

            setupVehiculoFotoDrop();

            $('#btnGuardarCliente').click(crearCliente);
            $('#btnGuardarVehiculo').click(crearVehiculo);

            // ============ FUNCIONES DE AUTOCOMPLETADO PREDICTIVO ============
            // Cargar marcas y modelos al inicio
            cargarMarcasParaPredictivo();
            cargarModelosParaPredictivo();

            // Autocompletado para marca
            $('#nuevaMarca').on('input', function() {
                let busqueda = $(this).val().toUpperCase();
                if (busqueda.length < 1) {
                    $('#marcaPredictive').hide();
                    return;
                }

                let resultados = marcasCache.filter(m =>
                    m.marca.toUpperCase().includes(busqueda)
                ).slice(0, 8);

                mostrarResultadosMarcas(resultados);
            });

            // Autocompletado para modelo
            $('#nuevoModelo').on('input', function() {
                let busqueda = $(this).val().toUpperCase();
                if (busqueda.length < 1) {
                    $('#modeloPredictive').hide();
                    return;
                }

                let resultados = modelosCache.filter(m =>
                    m.modelo.toUpperCase().includes(busqueda)
                ).slice(0, 8);

                mostrarResultadosModelos(resultados);
            });

            // Ocultar dropdowns al hacer clic fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#nuevaMarca').length && !$(e.target).closest('#marcaPredictive').length) {
                    $('#marcaPredictive').hide();
                }
                if (!$(e.target).closest('#nuevoModelo').length && !$(e.target).closest('#modeloPredictive').length) {
                    $('#modeloPredictive').hide();
                }
            });

            // Cuando se selecciona una marca, sugerir modelos relacionados
            $('#nuevaMarca').on('blur', function() {
                let marcaSeleccionada = $(this).val().toUpperCase();
                if (marcaSeleccionada.length > 2) {
                    sugerirModelosPorMarca(marcaSeleccionada);
                }
            });
        });

        // ========== FUNCIONES DE AUTOCOMPLETADO PREDICTIVO ==========
        function cargarMarcasParaPredictivo() {
            $.ajax({
                url: '{{ route("mantenimiento.rapido.marcas") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        marcasCache = response.marcas;
                        console.log('Marcas cargadas:', marcasCache.length);
                    }
                },
                error: function() {
                    console.log('Error cargando marcas');
                }
            });
        }

        function cargarModelosParaPredictivo() {
            $.ajax({
                url: '{{ route("mantenimiento.rapido.modelos") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        modelosCache = response.modelos;
                        console.log('Modelos cargados:', modelosCache.length);
                    }
                },
                error: function() {
                    console.log('Error cargando modelos');
                }
            });
        }

        function mostrarResultadosMarcas(resultados) {
            if (resultados.length === 0) {
                $('#marcaPredictive').hide();
                return;
            }

            let html = '';
            resultados.forEach(r => {
                html += `
                    <div class="predictive-item" onclick="seleccionarMarca('${r.marca}')">
                        ${r.marca} ${r.vehiculos_count ? '(' + r.vehiculos_count + ' vehículos)' : ''}
                    </div>
                `;
            });

            $('#marcaPredictive').html(html).show();
        }

        function mostrarResultadosModelos(resultados) {
            if (resultados.length === 0) {
                $('#modeloPredictive').hide();
                return;
            }

            let html = '';
            resultados.forEach(r => {
                html += `
                    <div class="predictive-item" onclick="seleccionarModelo('${r.modelo}')">
                        ${r.modelo} ${r.marca ? '- ' + r.marca : ''}
                    </div>
                `;
            });

            $('#modeloPredictive').html(html).show();
        }

        function seleccionarMarca(marca) {
            $('#nuevaMarca').val(marca);
            $('#marcaPredictive').hide();

            // Sugerir modelos de esta marca
            sugerirModelosPorMarca(marca.toUpperCase());
        }

        function seleccionarModelo(modelo) {
            $('#nuevoModelo').val(modelo);
            $('#modeloPredictive').hide();
        }

        function sugerirModelosPorMarca(marca) {
            let resultados = modelosCache.filter(m =>
                m.marca && m.marca.toUpperCase().includes(marca)
            ).slice(0, 5);

            if (resultados.length > 0) {
                let sugerencias = resultados.map(m => m.modelo).join(', ');
                $('#nuevoModelo').attr('placeholder', `Sugerencias: ${sugerencias}`);
            }
        }

        // ========== FUNCIONES DE ESCÁNER ==========
        function iniciarEscanerCodigoBarras() {
            // Verificar si el navegador soporta la API de cámara
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Tu navegador no soporta el acceso a la cámara');
                return;
            }

            $('#scannerModal').modal('show');
            scannerActive = true;

            // Inicializar Quagga después de que el modal esté visible
            $('#scannerModal').on('shown.bs.modal', function() {
                iniciarQuagga();
            });
        }

        function detenerEscanerCodigoBarras() {
            if (typeof Quagga !== 'undefined') {
                Quagga.stop();
            }
            scannerActive = false;
            $('#scannerModal').modal('hide');
        }

        function iniciarQuagga() {
            if (typeof Quagga === 'undefined') {
                console.error('Quagga no está cargado');
                return;
            }

            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scannerView'),
                    constraints: {
                        facingMode: "environment",
                        width: { min: 640 },
                        height: { min: 480 }
                    },
                },
                decoder: {
                    readers: [
                        "ean_reader",
                        "ean_8_reader",
                        "code_128_reader",
                        "code_39_reader",
                        "upc_reader",
                        "upc_e_reader"
                    ]
                },
            }, function(err) {
                if (err) {
                    console.error(err);
                    alert('Error al iniciar el escáner: ' + err.message);
                    detenerEscanerCodigoBarras();
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function(data) {
                var code = data.codeResult.code;
                $('#scanResult').text('Código detectado: ' + code).fadeIn();
                $('#buscadorProducto').val(code);
                buscarProductos();

                // Detener después de 1 segundo
                setTimeout(function() {
                    detenerEscanerCodigoBarras();
                }, 1000);
            });
        }

        // ========== FUNCIONES DE CÁLCULO ==========
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

        function setupFotosInput() {
            const camaraInput = document.getElementById('camaraInput');
            const galeriaInput = document.getElementById('galeriaInput');
            const btnTomarFoto = document.getElementById('btnTomarFoto');
            const btnSeleccionarFotos = document.getElementById('btnSeleccionarFotos');
            const dropArea = document.getElementById('dropArea');

            // Detectar si es iPhone
            const esIphone = /iPhone|iPad|iPod/.test(navigator.userAgent);

            // ========== CONFIGURAR BOTÓN TOMAR FOTO ==========
            if (btnTomarFoto) {
                // Eliminar eventos anteriores
                const nuevoBoton = btnTomarFoto.cloneNode(true);
                btnTomarFoto.parentNode.replaceChild(nuevoBoton, btnTomarFoto);

                if (esIphone) {
                    // Para iPhone: crear input temporal para la cámara
                    nuevoBoton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Crear input temporal solo para la cámara
                        const inputTemp = document.createElement('input');
                        inputTemp.type = 'file';
                        inputTemp.accept = 'image/jpeg,image/png';
                        inputTemp.setAttribute('capture', 'camera'); // Esto fuerza la cámara en iPhone

                        inputTemp.onchange = function(evento) {
                            if (evento.target.files && evento.target.files.length > 0) {
                                handleFiles(evento.target.files);
                            }
                            inputTemp.remove(); // Eliminar input temporal
                        };

                        document.body.appendChild(inputTemp);
                        inputTemp.click();
                    });
                } else {
                    // Para Android/PC: usar input normal
                    nuevoBoton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        camaraInput.click();
                    });
                }
            }

            // ========== CONFIGURAR BOTÓN GALERÍA ==========
            if (btnSeleccionarFotos) {
                // Eliminar eventos anteriores
                const nuevoBoton = btnSeleccionarFotos.cloneNode(true);
                btnSeleccionarFotos.parentNode.replaceChild(nuevoBoton, btnSeleccionarFotos);

                if (esIphone) {
                    // Para iPhone: crear input temporal para la galería
                    nuevoBoton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Crear input temporal para la galería (sin capture)
                        const inputTemp = document.createElement('input');
                        inputTemp.type = 'file';
                        inputTemp.accept = 'image/jpeg,image/png';
                        inputTemp.multiple = true; // Permitir múltiples fotos

                        inputTemp.onchange = function(evento) {
                            if (evento.target.files && evento.target.files.length > 0) {
                                handleFiles(evento.target.files);
                            }
                            inputTemp.remove(); // Eliminar input temporal
                        };

                        document.body.appendChild(inputTemp);
                        inputTemp.click();
                    });
                } else {
                    // Para Android/PC: usar input normal
                    nuevoBoton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        galeriaInput.click();
                    });
                }
            }

            // ========== CONFIGURAR INPUTS NORMALES (para Android/PC) ==========
            if (camaraInput) {
                camaraInput.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files.length > 0) {
                        handleFiles(e.target.files);
                        camaraInput.value = '';
                    }
                });
            }

            if (galeriaInput) {
                galeriaInput.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files.length > 0) {
                        handleFiles(e.target.files);
                        galeriaInput.value = '';
                    }
                });
            }

            // ========== DRAG & DROP PARA ESCRITORIO ==========
            if (dropArea) {
                dropArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    dropArea.style.background = 'var(--pastel-blue)';
                    dropArea.style.borderColor = '#28a745';
                });

                dropArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    dropArea.style.background = 'white';
                    dropArea.style.borderColor = '#9fb7c9';
                });

                dropArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    dropArea.style.background = 'white';
                    dropArea.style.borderColor = '#9fb7c9';
                    if (e.dataTransfer.files.length > 0) {
                        handleFiles(e.dataTransfer.files);
                    }
                });
            }
        }

        // ========== FUNCIONES DE MANEJO DE FOTOS ==========
        function handleFiles(files) {
            // Convertir FileList a array si es necesario
            const fileArray = Array.isArray(files) ? files : Array.from(files);

            console.log('Procesando archivos:', fileArray.length);

            fileArray.forEach((file, idx) => {
                // Validar tipo de archivo
                if (!file.type.match('image.*')) {
                    mostrarNotificacion('⚠️ Solo se permiten imágenes', 'warning');
                    return;
                }

                // Validar tamaño (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    mostrarNotificacion('⚠️ La imagen excede los 5MB', 'warning');
                    return;
                }

                // Verificar duplicados
                const archivoDuplicado = fotosSeleccionadas.some(f =>
                    f.name === file.name && f.size === file.size
                );

                if (archivoDuplicado) {
                    mostrarNotificacion('⚠️ La imagen ' + file.name + ' ya fue agregada', 'warning');
                    return;
                }

                // Agregar a la lista
                fotosSeleccionadas.push(file);

                // Crear preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    agregarPreviewFoto(e.target.result, file.name, fotoIndex);
                    fotoIndex++;
                };
                reader.readAsDataURL(file);
            });

            // Limpiar inputs
            const camaraInput = document.getElementById('camaraInput');
            const galeriaInput = document.getElementById('galeriaInput');
            if (camaraInput) camaraInput.value = '';
            if (galeriaInput) galeriaInput.value = '';
        }

        function agregarPreviewFoto(src, fileName, index) {
            // Verificar que el template existe
            const template = document.getElementById('fotoPreviewTemplate');
            if (!template) {
                console.error('Template no encontrado');
                return;
            }

            // Evitar índices duplicados
            if ($(`.foto-item[data-index="${index}"]`).length > 0) {
                while ($(`.foto-item[data-index="${fotoIndex}"]`).length > 0) {
                    fotoIndex++;
                }
                index = fotoIndex;
            }

            // Crear el HTML del preview
            const html = template.innerHTML
                .replace(/{src}/g, src)
                .replace(/{index}/g, index)
                .replace(/{fileName}/g, fileName);

            $('#fotosPreview').append(html);

            // Scroll al nuevo preview (útil en móviles)
            setTimeout(() => {
                $('.foto-item:last-child')[0]?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }

        function eliminarFoto(index) {
            fotosSeleccionadas = fotosSeleccionadas.filter((_, i) => i !== index);
            $(`.foto-item[data-index="${index}"]`).remove();

            $('.foto-item').each(function(newIndex) {
                const oldIndex = $(this).data('index');
                $(this).attr('data-index', newIndex);
                $(this).find('select').attr('name', `tipo_foto_${newIndex}`);
                $(this).find('input').attr('name', `descripcion_foto_${newIndex}`);
            });

            fotoIndex = $('.foto-item').length;
        }

        function actualizarTipoFoto(index, tipo) {
            console.log('Foto ' + index + ' tipo: ' + tipo);
        }

        // ========== FUNCIONES DE HORA ==========
        function obtenerHoraCliente() {
            const ahora = new Date();
            const horas = ahora.getHours().toString().padStart(2, '0');
            const minutos = ahora.getMinutes().toString().padStart(2, '0');
            return `${horas}:${minutos}`;
        }

        function actualizarHoraCliente() {
            $('#hora_mantenimiento').val(obtenerHoraCliente());
        }

        // ========== FUNCIONES DE BÚSQUEDA DE VEHÍCULOS ==========
        function buscarVehiculo() {
            let criterio = $('#placa').val().trim().toUpperCase();
            if (!criterio) {
                alert('Ingrese un criterio de búsqueda');
                return;
            }

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.buscar") }}',
                method: 'POST',
                data: {
                    identificacion: criterio,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success) {
                        if (response.tipo_resultado == 'unico') {
                            vehiculoActual = response.vehiculo;
                            clienteActual = response.vehiculo.cliente;

                            mostrarDatosUnicos(response);
                            avanzarPaso2();

                            if (response.ultimos_mantenimientos && response.ultimos_mantenimientos.length > 0) {
                                mostrarUltimosMantenimientos(response.ultimos_mantenimientos);
                            }
                        } else if (response.tipo_resultado == 'multiple') {
                            mostrarMultiplesVehiculos(response.vehiculos);
                            avanzarPaso2();
                        }
                    } else {
                        $('#step1').hide();
                        $('#multipleResults').hide();
                        $('#singleResult').hide();

                        mostrarNotificacion('⚠️ Vehículo no encontrado. Verificando si el cliente existe...', 'warning');
                        sessionStorage.setItem('placaBuscada', criterio);
                        mostrarModalBuscarCliente(criterio);
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    alert('Error al buscar el vehículo');
                }
            });
        }

        function mostrarMultiplesVehiculos(vehiculos) {
            vehiculosEncontrados = vehiculos;

            let html = '';
            vehiculos.forEach(v => {
                html += `
                <div class="vehicle-item p-3 mb-2" onclick="seleccionarVehiculo(${v.id})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="ri-car-line"></i> ${v.marca} ${v.modelo}</strong><br>
                            <small class="text-muted">
                                <i class="ri-road-map-line"></i> Placa: ${v.identificacion} |
                                <i class="ri-caravan-line"></i> ${v.tipo} |
                                <i class="ri-calendar-line"></i> ${v.year || 'N/A'}
                            </small>
                        </div>
                        <div>
                            <span class="badge" style="background: var(--pastel-purple); color: #2c3e50;">
                                <i class="ri-user-line"></i> ${v.cliente.nombre}
                            </span>
                        </div>
                    </div>
                </div>
            `;
            });

            $('#vehiculosList').html(html);
            $('#multipleResults').fadeIn();
            $('#singleResult').hide();
            $('#nuevoClienteForm').hide();
            $('#nuevoVehiculoForm').hide();
            $('#btnContinuarMantenimiento').hide();
        }

        function seleccionarVehiculo(vehiculoId) {
            $('#loadingOverlay').fadeIn();

            let vehiculoSeleccionado = vehiculosEncontrados.find(v => v.id == vehiculoId);
            if (vehiculoSeleccionado) {
                $('.vehicle-item').removeClass('selected');
                $(event.currentTarget).addClass('selected');
            }

            $.ajax({
                url: '{{ route("mantenimiento.rapido.obtener-vehiculo") }}',
                method: 'POST',
                data: {
                    vehiculo_id: vehiculoId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success) {
                        vehiculoActual = response.vehiculo;
                        clienteActual = response.vehiculo.cliente;

                        mostrarDatosVehiculoSeleccionado(response.vehiculo);

                        if (response.ultimos_mantenimientos && response.ultimos_mantenimientos.length > 0) {
                            mostrarUltimosMantenimientos(response.ultimos_mantenimientos);
                        }

                        $('#multipleResults').fadeOut(300, function() {
                            $('#singleResult').fadeIn();
                            $('#btnContinuarMantenimiento').fadeIn();
                        });
                    } else {
                        alert('Error al cargar los detalles del vehículo');
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    alert('Error de conexión al cargar el vehículo');
                }
            });
        }

        function mostrarDatosUnicos(data) {
                mostrarDatosVehiculoSeleccionado(data.vehiculo);
            $('#multipleResults').hide();
            $('#singleResult').fadeIn();
            $('#btnContinuarMantenimiento').fadeIn();
        }

        function mostrarDatosVehiculoSeleccionado(vehiculo) {
            vehiculoActual = vehiculo;
            clienteActual = vehiculo.cliente;

            console.log('Vehículo actual:', vehiculoActual);
            console.log('Foto URL:', vehiculo.foto_url);

            let cliente = vehiculo.cliente;
            let telefono = cliente.telefono || 'N/A';

            // Mostrar datos del cliente
            $('#clienteData').html(`
        <div style="background: var(--pastel-pink); padding: 10px; border-radius: 8px; margin-bottom: 10px;">
            <p><strong><i class="ri-id-card-line"></i> Cédula:</strong> ${escapeHtml(cliente.cedula)}</p>
            <p><strong><i class="ri-user-line"></i> Nombre:</strong> ${escapeHtml(cliente.nombre)}</p>
        </div>
        <div style="background: var(--pastel-yellow); padding: 10px; border-radius: 8px;">
            <p><strong><i class="ri-phone-line"></i> Teléfono:</strong> ${escapeHtml(telefono)}</p>
            <p><strong><i class="ri-mail-line"></i> Email:</strong> ${escapeHtml(cliente.email) || 'No registrado'}</p>
        </div>
    `);

            // Seriales del vehículo
            let serialesHTML = '';
            if (vehiculo.serialmotor || vehiculo.serialchasis) {
                serialesHTML = `
            <div style="background: var(--pastel-blue); padding: 10px; border-radius: 8px; margin-top: 10px;">
                ${vehiculo.serialmotor ? `<p><strong><i class="ri-engine-line"></i> Serial Motor:</strong> ${escapeHtml(vehiculo.serialmotor)}</p>` : ''}
                ${vehiculo.serialchasis ? `<p><strong><i class="ri-car-line"></i> Serial Chasis:</strong> ${escapeHtml(vehiculo.serialchasis)}</p>` : ''}
            </div>
        `;
            }

            // Usar directamente la foto_url del backend
            let fotoUrl = vehiculo.foto_url || '{{ asset("build/images/logo-light.png") }}';

            $('#vehiculoDataid').show();
            $('#editVehiculoFotoForm').hide();

            $('#vehiculoData').html(`
        <div class="text-center mb-3">
            <img src="${fotoUrl}"
                 id="vehiculoFotoPreview"
                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px; border: 3px solid var(--pastel-purple); cursor: pointer;"
                 onclick="verFotoVehiculo()"
                 onerror="this.onerror=null; this.src='{{ asset("build/images/logo-light.png") }}'">
            <div class="mt-2">
                ${vehiculo.foto_vehiculo ?
                '<span class="badge" style="background: var(--pastel-green); color: #2c3e50;"><i class="ri-image-line"></i> Foto registrada</span>' :
                '<span class="badge" style="background: var(--pastel-pink); color: #2c3e50;"><i class="ri-image-off-line"></i> Sin foto principal</span>'}
            </div>
        </div>
        <div style="background: var(--pastel-green); padding: 10px; border-radius: 8px;">
            <p><strong><i class="ri-road-map-line"></i> Placa:</strong> ${escapeHtml(vehiculo.identificacion)}</p>
            <p><strong><i class="ri-caravan-line"></i> Tipo:</strong> ${escapeHtml(vehiculo.tipo)}</p>
            <p><strong><i class="ri-car-line"></i> Marca/Modelo:</strong> ${escapeHtml(vehiculo.marca)} ${escapeHtml(vehiculo.modelo)}</p>
            <p><strong><i class="ri-calendar-line"></i> Año:</strong> ${escapeHtml(vehiculo.year) || 'No especificado'}</p>
        </div>
        ${serialesHTML}
        ${vehiculo.observaciones ? `
            <div style="background: var(--pastel-peach); padding: 10px; border-radius: 8px; margin-top: 10px;">
                <p><strong><i class="ri-chat-1-line"></i> Observaciones:</strong> ${escapeHtml(vehiculo.observaciones)}</p>
            </div>
        ` : ''}
    `);

            $('#multipleResults').hide();
            $('#singleResult').show();
        }

        // Función auxiliar para escapar HTML y evitar XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ========== FUNCIONES DE BÚSQUEDA DE CLIENTES ==========
        function mostrarModalBuscarCliente(placa) {
            $('#buscarClienteModal').remove();

            let modalHtml = `
                <div class="modal fade" id="buscarClienteModal" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background: var(--pastel-purple);">
                                <h5 class="modal-title">🔍 Verificar Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>No se encontró el vehículo con placa <strong>${placa}</strong></p>
                                <p>Por favor, verifique si el cliente ya existe:</p>
                                <div class="mb-3">
                                    <label>Cédula/RIF del Cliente:</label>
                                    <input type="text" class="form-control" id="cedulaBusqueda" placeholder="Ingrese cédula" autofocus>
                                </div>
                                <div id="resultadoBusquedaCliente"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn pastel-btn-primary" id="btnBuscarCliente">
                                    <i class="ri-search-line"></i> Buscar Cliente
                                </button>
                                <button type="button" class="btn btn-success" id="btnClienteNuevo">
                                    <i class="ri-user-add-line"></i> Cliente Nuevo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHtml);

            $('#btnBuscarCliente').click(function() {
                let cedula = $('#cedulaBusqueda').val().trim();
                if (cedula) {
                    buscarClienteExistente(cedula, placa);
                } else {
                    alert('Ingrese una cédula para buscar');
                }
            });

            $('#btnClienteNuevo').click(function() {
                $('#buscarClienteModal').modal('hide');
                $('#multipleResults').hide();
                $('#singleResult').hide();
                $('#nuevoClienteForm').fadeIn();
                $('#nuevoVehiculoForm').hide();
                sessionStorage.setItem('placaPendiente', placa);
                $('#nuevaPlaca').val(placa);
                $('#placaVehiculoNuevo').text(`Placa: ${placa}`);
                avanzarPaso2();
            });

            $('#cedulaBusqueda').keypress(function(e) {
                if (e.which == 13) {
                    $('#btnBuscarCliente').click();
                }
            });

            $('#buscarClienteModal').modal('show');
        }

        function buscarClienteExistente(cedula, placa) {
            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.buscar-cliente") }}',
                method: 'POST',
                data: {
                    cedula: cedula,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success) {
                        clienteActual = response.cliente;

                        // Guardar la placa pendiente
                        sessionStorage.setItem('placaPendiente', placa);

                        // Mostrar resultado mejorado cuando SÍ existe el cliente
                        $('#resultadoBusquedaCliente').html(`
                    <div class="cliente-encontrado-card">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="estado-icono">
                                    <i class="ri-user-star-line"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h4 class="text-success mb-3">
                                    <i class="ri-checkbox-circle-fill"></i> ¡Cliente Encontrado!
                                </h4>

                                <div class="cliente-info-destacada">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong><i class="ri-id-card-line"></i> Cédula/RIF:</strong><br>
                                                <span class="h5">${escapeHtml(response.cliente.cedula)}</span>
                                            </p>
                                            <p class="mb-2">
                                                <strong><i class="ri-user-line"></i> Nombre:</strong><br>
                                                <span class="h5">${escapeHtml(response.cliente.nombre)}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong><i class="ri-phone-line"></i> Teléfonos:</strong><br>
                                                 ${escapeHtml(response.cliente.telefono || 'No registrado')}
                                            </p>
                                            <p class="mb-2">
                                                <strong><i class="ri-mail-line"></i> Correo:</strong><br>  ${escapeHtml(response.cliente.email || 'No registrado')}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="ri-information-line"></i>
                                    <strong>¡Excelente!</strong> Este cliente no tiene registrado el vehículo con placa <strong class="badge-vehiculo-pendiente" style="text-transform: uppercase">${escapeHtml(placa)}</strong>
                                </div>

                                <div class="vehiculo-sugerido-card text-center">
                                    <i class="ri-car-line" style="font-size: 40px; color: #28a745;"></i>
                                    <h5 class="mt-2">¿Deseas registrar este vehículo?</h5>
                                    <p class="text-muted">Completa los datos del vehículo para asociarlo a ${escapeHtml(response.cliente.nombre)}</p>
                                    <button class="btn btn-registrar-vehiculo" id="btnRegistrarVehiculoCliente"
                                        style="background: #dbf2e1; display: flex; align-items: center;">
                                        <i class="ri-add-circle-line"></i> Registrar Vehículo para este Cliente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                        // Evento para registrar vehículo
                        $('#btnRegistrarVehiculoCliente').off('click').on('click', function() {
                            $('#buscarClienteModal').modal('hide');
                            mostrarNotificacion('✨ Preparando formulario para registrar vehículo...', 'success');

                            setTimeout(() => {
                                $('#clienteData').html(`
                            <div style="background: linear-gradient(135deg, var(--pastel-pink) 0%, #ffe6f0 100%); padding: 15px; border-radius: 12px; border: 2px solid #28a745;">
                                <p><strong><i class="ri-id-card-line"></i> Cédula:</strong> ${escapeHtml(clienteActual.cedula)}</p>
                                <p><strong><i class="ri-user-line"></i> Nombre:</strong> ${escapeHtml(clienteActual.nombre)}</p>
                                <p><strong><i class="ri-phone-line"></i> Teléfono:</strong> ${escapeHtml(clienteActual.telefono || 'N/A')}</p>
                                <p><strong><i class="ri-mail-line"></i> Email:</strong> ${escapeHtml(clienteActual.email || 'N/A')}</p>
                            </div>
                        `);

                                $('#vehiculoDataid').hide();
                                $('#vehiculoData').empty();
                                $('#nuevoClienteForm').hide();
                                $('#nuevoVehiculoForm').fadeIn(300);
                                $('#nuevaPlaca').val(placa);
                                $('#placaVehiculoNuevo').html(`<i class="ri-road-map-line"></i> Placa: <strong>${placa}</strong> <span class="badge bg-warning">Pendiente de registro</span>`);
                                $('#btnContinuarMantenimiento').hide();
                                $('#nuevoTipoVehiculo').focus();
                                mostrarNotificacion(`🚗 Complete los datos del vehículo para ${clienteActual.nombre}`, 'success');

                                $('#nuevoVehiculoForm').addClass('border-success');
                                setTimeout(() => {
                                    $('#nuevoVehiculoForm').removeClass('border-success');
                                }, 2000);

                                $('#multipleResults').hide();
                                $('#singleResult').fadeIn();

                                if ($('#step2').is(':hidden')) {
                                    avanzarPaso2();
                                }
                            }, 500);
                        });

                    } else {
                        // Cuando NO se encuentra el cliente - Mostrar opción para crear nuevo cliente
                        $('#resultadoBusquedaCliente').html(`
                    <div class="alert alert-warning mt-3" style="border-left: 4px solid #ffc107; background: #fff9e6;">
                        <div class="text-center">
                            <i class="ri-emotion-sad-line" style="font-size: 50px; color: #ffc107;"></i>
                            <h5 class="mt-2">Cliente no encontrado</h5>
                            <p>No se encontró ningún cliente con cédula <strong>${escapeHtml(cedula)}</strong></p>
                            <hr>
                            <p class="text-muted">¿Deseas registrar un nuevo cliente?</p>
                            <button class="btn btn-success" id="btnIrNuevoCliente" style="background: #28a745; border: none;">
                                <i class="ri-user-add-line"></i> Registrar Nuevo Cliente
                            </button>
                        </div>
                    </div>
                `);

                        // Evento para el botón "Registrar Nuevo Cliente"
                        $('#btnIrNuevoCliente').off('click').on('click', function() {
                            console.log('Botón Registrar Nuevo Cliente clickeado');
                            $('#buscarClienteModal').modal('hide');
                            $('#multipleResults').hide();
                            $('#singleResult').hide();
                            $('#nuevoClienteForm').fadeIn();
                            $('#nuevoVehiculoForm').hide();
                            sessionStorage.setItem('placaPendiente', placa);
                            $('#nuevaPlaca').val(placa);
                            $('#placaVehiculoNuevo').text(`Placa: ${placa}`);
                            $('#nuevaCedula').val(cedula);
                            $('#nuevaCedula').focus();
                            avanzarPaso2();
                            mostrarNotificacion('📝 Complete los datos del nuevo cliente', 'info');
                        });
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();
                    $('#resultadoBusquedaCliente').html(`
                <div class="alert alert-danger mt-3">
                    <i class="ri-error-warning-line"></i> Error al buscar cliente. Por favor, intente nuevamente.
                </div>
            `);
                }
            });
        }

        function verFotoVehiculo() {
            let src = $('#vehiculoFotoPreview').attr('src');
            // No mostrar el modal si es la imagen por defecto
            if (src && src !== '{{ asset("build/images/logo-light.png") }}' && !src.includes('logo-light.png')) {
                if ($('#fotoVehiculoModal').length === 0) {
                    let modalHtml = `
                <div class="modal fade" id="fotoVehiculoModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background: var(--pastel-purple);">
                                <h5 class="modal-title">Foto del Vehículo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-0">
                                <img src="" id="fotoVehiculoGrande" style="max-width: 100%; max-height: 80vh; border-radius: 8px;">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    $('body').append(modalHtml);
                }

                $('#fotoVehiculoGrande').attr('src', src);
                $('#fotoVehiculoModal').modal('show');
            } else {
                mostrarNotificacion('⚠️ No hay foto registrada para este vehículo', 'warning');
            }
        }

        // ========== FUNCIONES DE CREACIÓN DE CLIENTE/Vehículo ==========
        function crearCliente() {
            let cedula = $('#nuevaCedula').val().trim();
            let nombre = $('#nuevoNombre').val().trim();
            let telefono = $('#nuevoTelefono').val().trim();
            let email = $('#nuevoEmail').val().trim();

            if (!cedula) {
                alert('⚠️ La cédula/RIF es obligatoria');
                $('#nuevaCedula').focus();
                return;
            }

            if (!nombre) {
                alert('⚠️ El nombre del cliente es obligatorio');
                $('#nuevoNombre').focus();
                return;
            }

            if (email && !email.includes('@')) {
                alert('⚠️ El email no tiene formato válido');
                $('#nuevoEmail').focus();
                return;
            }

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.crear-cliente") }}',
                method: 'POST',
                data: {
                    cedula: cedula,
                    nombre: nombre,
                    telefono: telefono,
                    email: email,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    if (response.success) {
                        clienteActual = response.cliente;

                        mostrarNotificacion('✅ Cliente creado exitosamente', 'success');

                        $('#clienteData').html(`
                            <div style="background: var(--pastel-pink); padding: 10px; border-radius: 8px;">
                                <p><strong><i class="ri-id-card-line"></i> Cédula:</strong> ${response.cliente.cedula}</p>
                                <p><strong><i class="ri-user-line"></i> Nombre:</strong> ${response.cliente.nombre}</p>
                                <p><strong><i class="ri-phone-line"></i> Teléfono:</strong> ${response.cliente.telefono || 'N/A'}</p>
                                <p><strong><i class="ri-mail-line"></i> Email:</strong> ${response.cliente.email || 'N/A'}</p>
                            </div>
                        `);

                        $('#nuevoClienteForm').fadeOut();

                        if ($('#nuevaPlaca').val().trim()) {
                            $('#nuevoVehiculoForm').fadeIn();
                        }
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();

                    let mensaje = 'Error al crear cliente';
                    let errores = '';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errores = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('❌ Errores de validación:\n' + errores);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                        alert('❌ ' + mensaje);
                    } else {
                        alert('❌ ' + mensaje);
                    }

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $('.is-invalid').removeClass('is-invalid');
                        Object.keys(xhr.responseJSON.errors).forEach(campo => {
                            if (campo === 'cedula') $('#nuevaCedula').addClass('is-invalid');
                            if (campo === 'nombre') $('#nuevoNombre').addClass('is-invalid');
                            if (campo === 'email') $('#nuevoEmail').addClass('is-invalid');
                            if (campo === 'telefono') $('#nuevoTelefono').addClass('is-invalid');
                        });
                    }
                }
            });
        }

        function crearVehiculo() {
            if (!clienteActual) {
                alert('Primero debe crear o seleccionar un cliente');
                $('#nuevoClienteForm').fadeIn();
                $('#nuevaCedula').focus();
                return;
            }

            let data = {
                codclie: clienteActual.codclie,
                fk_tipo: $('#nuevoTipoVehiculo').val(),
                modelo: $('#nuevoModelo').val().toUpperCase(),
                marca: $('#nuevaMarca').val().toUpperCase(),
                identificacion: $('#nuevaPlaca').val().toUpperCase(),
                year: $('#nuevoYear').val(),
                serialmotor: $('#nuevoSerialMotor').val().toUpperCase(),
                serialchasis: $('#nuevoSerialChasis').val().toUpperCase(),
                observaciones: $('#nuevasObservaciones').val(),
                _token: '{{ csrf_token() }}'
            };

            if (!data.fk_tipo || !data.modelo || !data.marca || !data.identificacion) {
                alert('Complete los campos obligatorios (*)');
                return;
            }

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.crear-vehiculo") }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    if (response.success) {
                        // Obtener el vehículo completo con todos sus datos
                        $.ajax({
                            url: '{{ route("mantenimiento.rapido.obtener-vehiculo") }}',
                            method: 'POST',
                            data: {
                                vehiculo_id: response.vehiculo.id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(detalleResponse) {
                                if (detalleResponse.success) {
                                    vehiculoActual = detalleResponse.vehiculo;
                                    vehiculoActual.cliente = clienteActual;

                                    // AHORA SÍ, mostrar los datos del vehículo recién creado
                                    mostrarDatosVehiculoSeleccionado(vehiculoActual);

                                    // Mostrar la tarjeta del vehículo
                                    $('#vehiculoDataid').fadeIn();

                                    // Mostrar el botón "Continuar al Mantenimiento"
                                    $('#btnContinuarMantenimiento').fadeIn();

                                    $('#nuevoVehiculoForm').hide();
                                    $('#nuevoClienteForm').hide();
                                    $('#multipleResults').hide();
                                    $('#singleResult').fadeIn();

                                    mostrarNotificacion('✅ Vehículo creado exitosamente', 'success');

                                    // Limpiar el formulario de nuevo vehículo para futuros usos
                                    limpiarFormularioNuevoVehiculo();
                                }
                            },
                            error: function() {
                                // Fallback si falla la obtención de detalles
                                vehiculoActual = response.vehiculo;
                                vehiculoActual.cliente = clienteActual;
                                vehiculoActual.tipo = $('#nuevoTipoVehiculo option:selected').text();

                                mostrarDatosVehiculoSeleccionado(vehiculoActual);
                                $('#vehiculoDataid').fadeIn();
                                $('#btnContinuarMantenimiento').fadeIn();
                                $('#nuevoVehiculoForm').hide();
                                $('#singleResult').fadeIn();
                                mostrarNotificacion('✅ Vehículo creado exitosamente', 'success');

                                limpiarFormularioNuevoVehiculo();
                            }
                        });
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();
                    let mensaje = 'Error al crear vehículo';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        mensaje = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    alert('❌ ' + mensaje);
                }
            });
        }

        function limpiarFormularioNuevoVehiculo() {
            $('#nuevoTipoVehiculo').val('');
            $('#nuevaMarca').val('');
            $('#nuevoModelo').val('');
            $('#nuevoYear').val('');
            $('#nuevaPlaca').val('');
            $('#nuevoSerialMotor').val('');
            $('#nuevoSerialChasis').val('');
            $('#nuevasObservaciones').val('');
        }

        // ========== FUNCIONES DE PRODUCTOS ==========
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

            let producto = {
                codprod: codprod,
                descripcion: descrip,
                referencia: referencia,
                cantidad: cantidad,
                tipo: 'producto'
            };

            agregarProductoALista(producto);
            $('#resultadosProductos').hide();
            $('#buscadorProducto').val('');
        }

        function agregarProductoALista(producto) {
            producto.tempId = Date.now() + Math.random();
            productosTemp.push(producto);
            renderizarListaProductos();
        }

        function eliminarProductoTemp(tempId) {
            productosTemp = productosTemp.filter(p => p.tempId != tempId);
            renderizarListaProductos();
        }

        function renderizarListaProductos() {
            if (productosTemp.length === 0) {
                $('#productosList').html(`
                    <div class="text-muted text-center p-3">
                        <i class="ri-information-line"></i> No hay productos agregados
                    </div>
                `);
                return;
            }

            let html = '<table class="table table-sm table-bordered">';
            html += `
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Referencia</th>
                        <th>Cant</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
            `;

            productosTemp.forEach(p => {
                html += `
                    <tr>
                        <td>${p.descripcion}</td>
                        <td>${p.referencia || 'N/A'}</td>
                        <td>${p.cantidad}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="eliminarProductoTemp(${p.tempId})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            $('#productosList').html(html);
        }

        // ========== FUNCIONES DE MANTENIMIENTO ==========
        function guardarMantenimiento() {
            // Validar vendedor
            if (!$('#vendedorSelect').val()) {
                alert('Error: No hay vendedor seleccionado');
                $('#vendedorSelect').focus();
                return;
            }

            // Validar vehículo seleccionado
            if (!$('#mantenimiento_vehiculo_id').val()) {
                alert('Error: No hay vehículo seleccionado');
                return;
            }

            // Validar que se haya seleccionado al menos un tipo de mantenimiento
            let tiposSeleccionados = $('input[name="tipos_mantenimiento[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (tiposSeleccionados.length === 0) {
                alert('⚠️ Debe seleccionar al menos un tipo de mantenimiento');
                $('input[name="tipos_mantenimiento[]"]:first').focus();
                return;
            }

            // Validar fecha
            let fechaMantenimiento = $('input[name="fecha_mantenimiento"]').val();
            if (!fechaMantenimiento) {
                alert('⚠️ La fecha del mantenimiento es obligatoria');
                return;
            }

            // Actualizar hora del cliente
            actualizarHoraCliente();

            // Crear FormData
            let formData = new FormData();

            // Datos básicos del mantenimiento
            formData.append('fk_vehiculo', $('#mantenimiento_vehiculo_id').val());
            formData.append('fecha_mantenimiento', fechaMantenimiento);
            formData.append('hora_mantenimiento', $('#hora_mantenimiento').val());

            // Enviar todos los tipos seleccionados
            tiposSeleccionados.forEach(tipo => {
                formData.append('tipos_mantenimiento[]', tipo);
            });

            // Si hay descripción para "otros"
            if ($('#tipo_otros').is(':checked') && $('#otrosTipoContainer input').val()) {
                formData.append('otro_tipo_descripcion', $('#otrosTipoContainer input').val());
            }

            // Datos adicionales
            formData.append('kilometraje', $('#km_actual').val() || '');
            formData.append('codvend', $('#vendedorSelect').val() || '');
            formData.append('observaciones', $('textarea[name="observaciones"]').val() || '');
            formData.append('proximo_mantenimiento', $('#proximo_mantenimiento').val() || '');
            formData.append('proximo_kilometraje', $('#proximo_kilometraje').val() || '');
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('productos', JSON.stringify(productosTemp));

            // Agregar fotos de evidencia
            let totalFotos = fotosSeleccionadas ? fotosSeleccionadas.length : 0;

            if (totalFotos > 0) {
                fotosSeleccionadas.forEach((foto, index) => {
                    formData.append('fotos[]', foto);
                    const tipoFoto = $(`select[name="tipo_foto_${index}"]`).val() || 'general';
                    formData.append(`tipo_foto_${index}`, tipoFoto);
                    const descripcionFoto = $(`input[name="descripcion_foto_${index}"]`).val() || '';
                    formData.append(`descripcion_foto_${index}`, descripcionFoto);
                });
            }

            // Mostrar loading
            $('#loadingOverlay').fadeIn();
            $('#loadingOverlay').html(`
        <div style="background: white; padding: 20px; border-radius: 15px; text-align: center; min-width: 300px;">
            <div class="spinner-border text-secondary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <div class="mt-3">
                <strong id="uploadStatus">Guardando mantenimiento...</strong>
                <div class="progress mt-2" style="height: 8px; display: none;" id="uploadProgressBar">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%; background-color: #9fb7c9;"></div>
                </div>
                <small class="text-muted d-block mt-2" id="uploadDetail"></small>
            </div>
        </div>
    `);

            $.ajax({
                url: '{{ route("mantenimiento.rapido.guardar") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            $('#uploadProgressBar').show();
                            $('#uploadProgressBar .progress-bar').css('width', percentComplete + '%');
                            $('#uploadStatus').text(`Subiendo fotos (${percentComplete}%)...`);
                            $('#uploadDetail').text(`${formatFileSize(evt.loaded)} de ${formatFileSize(evt.total)}`);
                            if (percentComplete === 100) {
                                $('#uploadStatus').text('Procesando imágenes...');
                            }
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        mostrarNotificacion('✅ Mantenimiento guardado exitosamente', 'success');
                        $('#uploadStatus').html('✅ ¡Completado! Redirigiendo...');
                        $('#uploadProgressBar').hide();

                        // Usar location.replace en lugar de href para evitar caché
                        setTimeout(function() {
                            window.location.replace(response.route);
                        }, 1500);
                    } else {
                        $('#loadingOverlay').fadeOut();
                        alert('❌ ' + (response.message || 'Error al guardar el mantenimiento'));
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();
                    let mensaje = 'Error al guardar el mantenimiento';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errores = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('❌ Errores de validación:\n' + errores);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert('❌ ' + xhr.responseJSON.message);
                    } else {
                        alert('❌ ' + mensaje);
                    }
                    console.error('Error:', xhr);
                }
            });
        }

        // Función auxiliar para formatear tamaño de archivo
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function mostrarUltimosMantenimientos(mantenimientos) {
            // Función para mostrar últimos mantenimientos si es necesario
        }

        // ========== FUNCIONES DE UTILIDAD ==========
        function mostrarNotificacion(mensaje, tipo) {
            // Eliminar notificaciones anteriores
            $('.custom-notification').remove();

            let fondo = tipo === 'success' ? '#c1e0cd' : (tipo === 'error' ? '#ffe6e6' : '#fff9e6');
            let icono = tipo === 'success' ? '✓' : (tipo === 'error' ? '✗' : 'ℹ');

            let notificacion = $(`
        <div class="custom-notification" style="position: fixed; top: 20px; right: 20px; z-index: 10000;
                    background: ${fondo}; color: #2c3e50; padding: 15px 20px; border-radius: 10px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.15); border-left: 4px solid ${tipo === 'success' ? '#28a745' : (tipo === 'error' ? '#dc3545' : '#ffc107')};
                    animation: slideInRight 0.3s ease-out;">
            <strong style="font-size: 1.1rem;">${icono} ${mensaje}</strong>
        </div>
    `);

            $('body').append(notificacion);

            // Agregar animación CSS si no existe
            if (!$('#notificationAnimation').length) {
                $('head').append(`
            <style id="notificationAnimation">
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(100px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
                @keyframes slideOutRight {
                    from {
                        opacity: 1;
                        transform: translateX(0);
                    }
                    to {
                        opacity: 0;
                        transform: translateX(100px);
                    }
                }
            </style>
        `);
            }

            setTimeout(() => {
                notificacion.css('animation', 'slideOutRight 0.3s ease-out');
                setTimeout(() => {
                    notificacion.remove();
                }, 300);
            }, 4000);
        }

        function avanzarPaso2() {
            $('#step1').hide();
            $('#step2').fadeIn();
            if(vehiculoActual)
                $('#editVehiculoFotoForm').fadeIn();
        }

        function resetToSearch() {
            $('#step2').hide();
            $('#step3').hide();
            $('#step1').fadeIn();
            $('#placa').val('').focus();

            $('#multipleResults').hide();
            $('#singleResult').hide();
            $('#nuevoClienteForm').hide();
            $('#nuevoVehiculoForm').hide();
            $('#btnContinuarMantenimiento').hide();

            $('#clienteData').empty();
            $('#vehiculoData').empty();
            $('#editVehiculoFotoForm').hide();
            $('#vehiculoDataid').hide();

            productosTemp = [];
            $('#productosList').empty();
            $('#productosTempList').empty();
            $('#resultadosProductos').hide().empty();
            $('#mantenimientoForm')[0].reset();

            $('input[name="fecha_mantenimiento"]').val(new Date().toISOString().split('T')[0]);
            $('input[name="kilometraje"]').val('');
            $('input[name="tipos_mantenimiento[]"]').prop('checked', false);
            $('textarea[name="observaciones"]').val('');
            $('#proximo_mantenimiento').val('');
            $('#proximo_kilometraje').val('');
            $('#mantenimiento_vehiculo_id').val('');
            $('#proximoMantenimientoSection').hide();
            $('#otrosTipoContainer').hide();
            $('#calcularProximoBtn').hide();

            $('#nuevaCedula').val('');
            $('#nuevoNombre').val('');
            $('#nuevoTelefono').val('');
            $('#nuevoEmail').val('');
            $('#nuevoTipoVehiculo').val('');
            $('#nuevaMarca').val('');
            $('#nuevoModelo').val('');
            $('#nuevaPlaca').val('');
            $('#nuevoYear').val('');
            $('#nuevoSerialMotor').val('');
            $('#nuevoSerialChasis').val('');
            $('#nuevasObservaciones').val('');

            $('#productoManualDesc').val('');
            $('#productoManualRef').val('');
            $('#productoManualCant').val('1');
            $('#formProductoManual').hide();
            $('#buscadorProducto').val('');
            $('#vendedorSelect').val('');

            vehiculoActual = null;
            clienteActual = null;
            vehiculosEncontrados = [];

            sessionStorage.removeItem('placaPendiente');
            $('.modal').modal('hide');

            mostrarNotificacion('✨ Listo para nuevo registro', 'success');
        }

        // ========== FUNCIONES DE EDICIÓN ==========
        function editarCliente() {
            if (!clienteActual) {
                console.log('No hay cliente actual');
                return;
            }

            console.log('Editando cliente:', clienteActual);

            $('#editClienteCedula').val(clienteActual.cedula || '');
            $('#editClienteNombre').val(clienteActual.nombre || '');
            $('#editClienteTelefono').val(clienteActual.telefono || '');
            $('#editClienteEmail').val(clienteActual.email || '');

            $('#clienteData').hide();
            $('#editClienteForm').fadeIn();
        }

        function cancelarEdicionCliente() {
            $('#editClienteForm').hide();
            $('#clienteData').fadeIn();
        }

        function guardarEdicionCliente() {
            let data = {
                codclie: clienteActual.codclie,
                cedula: $('#editClienteCedula').val().trim(),
                nombre: $('#editClienteNombre').val().trim(),
                telefono: $('#editClienteTelefono').val().trim(),
                email: $('#editClienteEmail').val().trim(),
                _token: '{{ csrf_token() }}'
            };

            if (!data.cedula || !data.nombre) {
                alert('⚠️ Cédula y nombre son obligatorios');
                return;
            }

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.actualizar-cliente") }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    if (response.success) {
                        clienteActual = response.cliente;
                        actualizarVistaCliente(clienteActual);
                        cancelarEdicionCliente();
                        mostrarNotificacion('✅ Cliente actualizado exitosamente', 'success');
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();
                    let mensaje = 'Error al actualizar cliente';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                    alert('❌ ' + mensaje);
                }
            });
        }

        function editarVehiculo() {
            console.log('Función editarVehiculo llamada');
            console.log('vehiculoActual:', vehiculoActual);

            if (!vehiculoActual) {
                alert('Error: No hay vehículo seleccionado');
                return;
            }

            // Mostrar el formulario y ocultar la vista
            $('#vehiculoData').hide();
            $('#editVehiculoFotoForm').hide();
            $('#editVehiculoForm').show();

            // Cargar los datos en el formulario
            $('#editVehiculoTipo').val(vehiculoActual.fk_tipo || '');
            $('#editVehiculoMarca').val(vehiculoActual.marca || '');
            $('#editVehiculoModelo').val(vehiculoActual.modelo || '');
            $('#editVehiculoYear').val(vehiculoActual.year || '');
            $('#editVehiculoPlaca').val(vehiculoActual.identificacion || '');
            $('#editVehiculoSerialMotor').val(vehiculoActual.serialmotor || '');
            $('#editVehiculoSerialChasis').val(vehiculoActual.serialchasis || '');
            $('#editVehiculoObservaciones').val(vehiculoActual.observaciones || '');

            console.log('Datos cargados en el formulario');
        }

        function cancelarEdicionVehiculo() {
            console.log('Cancelando edición');
            $('#editVehiculoForm').hide();
            $('#vehiculoData').fadeIn(300);
            $('#vehiculoDataid').fadeIn(300);
            $('#editVehiculoFotoForm').fadeIn(300);
        }

        function guardarEdicionVehiculo() {
            if (!vehiculoActual) {
                alert('Error: No hay vehículo seleccionado');
                return;
            }

            let data = {
                id: vehiculoActual.id,
                fk_tipo: $('#editVehiculoTipo').val(),
                marca: $('#editVehiculoMarca').val().trim(),
                modelo: $('#editVehiculoModelo').val().trim(),
                year: $('#editVehiculoYear').val(),
                identificacion: $('#editVehiculoPlaca').val().trim(),
                serialmotor: $('#editVehiculoSerialMotor').val().trim(),
                serialchasis: $('#editVehiculoSerialChasis').val().trim(),
                observaciones: $('#editVehiculoObservaciones').val().trim(),
                _token: '{{ csrf_token() }}'
            };

            console.log('Guardando datos:', data);

            // Validar campos obligatorios
            let camposVacios = [];
            if (!data.fk_tipo) camposVacios.push('Tipo');
            if (!data.marca) camposVacios.push('Marca');
            if (!data.modelo) camposVacios.push('Modelo');
            if (!data.identificacion) camposVacios.push('Placa');

            if (camposVacios.length > 0) {
                alert('⚠️ Complete los campos obligatorios: ' + camposVacios.join(', '));
                return;
            }

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.actualizar-vehiculo") }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    console.log('Respuesta:', response);

                    if (response.success) {
                        // Actualizar vehiculoActual con los nuevos datos
                        vehiculoActual = {
                            ...vehiculoActual,
                            ...response.vehiculo
                        };

                        // Actualizar la vista
                        actualizarVistaVehiculo(vehiculoActual);
                        cancelarEdicionVehiculo();
                        mostrarNotificacion('✅ Vehículo actualizado exitosamente', 'success');
                    } else {
                        alert('Error: ' + (response.message || 'Error al actualizar'));
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();
                    console.error('Error en la petición:', xhr);

                    let mensaje = 'Error al actualizar vehículo';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errores = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('❌ Errores:\n' + errores);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert('❌ ' + xhr.responseJSON.message);
                    } else {
                        alert('❌ ' + mensaje);
                    }
                }
            });
        }

        function actualizarVistaCliente(cliente) {
            let telefono = cliente.telefono || 'N/A';
            if (cliente.telefono && cliente.telefono.includes('-')) {
                telefono = cliente.telefono;
            }

            $('#clienteData').html(`
                <div style="background: var(--pastel-pink); padding: 10px; border-radius: 8px; margin-bottom: 10px;">
                    <p><strong><i class="ri-id-card-line"></i> Cédula:</strong> ${cliente.cedula}</p>
                    <p><strong><i class="ri-user-line"></i> Nombre:</strong> ${cliente.nombre}</p>
                </div>
                <div style="background: var(--pastel-yellow); padding: 10px; border-radius: 8px;">
                    <p><strong><i class="ri-phone-line"></i> Teléfono:</strong> ${telefono}</p>
                    <p><strong><i class="ri-mail-line"></i> Email:</strong> ${cliente.email || 'No registrado'}</p>
                </div>
            `);
        }

        function actualizarVistaVehiculo(vehiculo) {
            let serialesHTML = '';
            if (vehiculo.serialmotor || vehiculo.serialchasis) {
                serialesHTML = `
            <div style="background: var(--pastel-blue); padding: 10px; border-radius: 8px; margin-top: 10px;">
                ${vehiculo.serialmotor ? `<p><strong><i class="ri-engine-line"></i> Serial Motor:</strong> ${escapeHtml(vehiculo.serialmotor)}</p>` : ''}
                ${vehiculo.serialchasis ? `<p><strong><i class="ri-car-line"></i> Serial Chasis:</strong> ${escapeHtml(vehiculo.serialchasis)}</p>` : ''}
            </div>
        `;
            }

            // ============ USAR foto_url del backend ============
            let fotoUrl = '{{ asset("build/images/logo-light.png") }}';

            if (vehiculo.foto_url && vehiculo.foto_url !== 'null' && vehiculo.foto_url !== '') {
                fotoUrl = vehiculo.foto_url;
                console.log('URL foto en actualizarVistaVehiculo:', fotoUrl);
            } else if (vehiculo.foto_vehiculo) {
                let rutaLimpia = vehiculo.foto_vehiculo;
                if (rutaLimpia.startsWith('public/')) {
                    rutaLimpia = rutaLimpia.substring(7);
                }
                if (rutaLimpia.startsWith('/public/')) {
                    rutaLimpia = rutaLimpia.substring(8);
                }
                fotoUrl = '/storage/' + rutaLimpia;
            }

            $('#vehiculoDataid').fadeIn();
            $('#editVehiculoFotoForm').hide();

            if ($('#vehiculoFotoPreview').length) {
                $('#vehiculoFotoPreview').attr('src', fotoUrl);
            }

            $('#vehiculoData').html(`
        <div class="text-center mb-3">
            <img src="${fotoUrl}"
                 id="vehiculoFotoPreview"
                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px; border: 3px solid var(--pastel-purple); cursor: pointer;"
                 onclick="verFotoVehiculo()"
                 onerror="this.onerror=null; this.src='{{ asset("build/images/logo-light.png") }}'">
            <div class="mt-2">
                ${vehiculo.foto_vehiculo ?
                '<span class="badge" style="background: var(--pastel-green); color: #2c3e50;"><i class="ri-image-line"></i> Foto registrada</span>' :
                '<span class="badge" style="background: var(--pastel-pink); color: #2c3e50;"><i class="ri-image-off-line"></i> Sin foto principal</span>'}
            </div>
        </div>
        <div style="background: var(--pastel-green); padding: 10px; border-radius: 8px;">
            <p><strong><i class="ri-road-map-line"></i> Placa:</strong> ${escapeHtml(vehiculo.identificacion)}</p>
            <p><strong><i class="ri-caravan-line"></i> Tipo:</strong> ${escapeHtml(vehiculo.tipo)}</p>
            <p><strong><i class="ri-car-line"></i> Marca/Modelo:</strong> ${escapeHtml(vehiculo.marca)} ${escapeHtml(vehiculo.modelo)}</p>
            <p><strong><i class="ri-calendar-line"></i> Año:</strong> ${escapeHtml(vehiculo.year) || 'No especificado'}</p>
        </div>
        ${serialesHTML}
        ${vehiculo.observaciones ? `
            <div style="background: var(--pastel-peach); padding: 10px; border-radius: 8px; margin-top: 10px;">
                <p><strong><i class="ri-chat-1-line"></i> Observaciones:</strong> ${escapeHtml(vehiculo.observaciones)}</p>
            </div>
        ` : ''}
    `);
        }

        function editarFotoVehiculo() {
            if (!vehiculoActual) return;

            cargarFotosVehiculo();

            $('#vehiculoDataid').hide();
            $('#vehiculoData').hide();
            $('#editVehiculoForm').hide();
            $('#editVehiculoFotoForm').fadeIn();
        }

        function cargarFotosVehiculo() {
            if (!vehiculoActual) return;

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.listar-fotos") }}',
                method: 'POST',
                data: {
                    vehiculo_id: vehiculoActual.id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loadingOverlay').fadeOut();

                    if (response.success) {
                        vehiculoFotos = response.fotos;
                        console.log('Fotos cargadas:', vehiculoFotos);
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();
                    console.error('Error al cargar fotos:', xhr);
                    mostrarNotificacion('Error al cargar las fotos', 'error');
                }
            });
        }

        function mostrarFotoVehiculoEnDatos(vehiculo) {
            if ($('#vehiculoFotoContainer').length === 0) {
                $('#vehiculoData').before(`
                    <div class="text-center mb-3" id="vehiculoFotoContainer">
                        <img id="vehiculoFotoPreview" src="{{ asset('build/images/logo-light.png') }}"
                             style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px; border: 3px solid var(--pastel-purple); cursor: pointer;"
                             onclick="verFotoVehiculo()">
                    </div>
                `);
            }

            let fotoUrl = vehiculo.foto_vehiculo ? '/storage/public/' + vehiculo.foto_vehiculo : '{{ asset("build/images/logo-light.png") }}';
            $('#vehiculoFotoPreview').attr('src', fotoUrl);
        }

        function establecerComoPrincipal(ruta) {
            if (!vehiculoActual) return;

            $.ajax({
                url: '{{ route("mantenimiento.rapido.actualizar-foto-vehiculo") }}',
                method: 'POST',
                data: {
                    vehiculo_id: vehiculoActual.id,
                    ruta_foto: ruta,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        vehiculoActual.foto_vehiculo = ruta;
                        $('#vehiculoFotoPreview').attr('src', response.url_foto);
                        cargarFotosVehiculo();
                        mostrarNotificacion('✅ Foto principal actualizada', 'success');
                    }
                }
            });
        }

        function eliminarFotoVehiculo(ruta) {
            if (!confirm('¿Eliminar esta foto?')) return;

            $.ajax({
                url: '{{ route("mantenimiento.rapido.eliminar-foto-vehiculo") }}',
                method: 'POST',
                data: {
                    vehiculo_id: vehiculoActual.id,
                    ruta_foto: ruta,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (ruta === vehiculoActual.foto_vehiculo) {
                            vehiculoActual.foto_vehiculo = null;
                            $('#vehiculoFotoPreview').attr('src', '{{ asset("build/images/logo-light.png") }}');
                        }
                        cargarFotosVehiculo();
                        mostrarNotificacion('✅ Foto eliminada', 'success');
                    }
                }
            });
        }

        function handleVehiculoFoto(file, tipo = 'foto_adicional') {
            if (!file) {
                mostrarNotificacion('⚠️ No se seleccionó ningún archivo', 'warning');
                return;
            }

            if (!file.type.match('image.*')) {
                mostrarNotificacion('⚠️ Solo se permiten imágenes', 'warning');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                mostrarNotificacion('⚠️ La imagen excede los 5MB', 'warning');
                return;
            }

            if (tipo == 'foto_principal' && vehiculoActual && vehiculoActual.foto_vehiculo) {
                if (!confirm('¿Reemplazar la foto principal actual?')) {
                    return;
                }
            }

            let formData = new FormData();
            formData.append('vehiculo_id', vehiculoActual.id);
            formData.append('foto', file);
            formData.append('tipo', tipo);
            formData.append('_token', '{{ csrf_token() }}');

            console.log('Enviando foto:', file.name, 'tipo:', tipo, 'tamaño:', file.size);

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: '{{ route("mantenimiento.rapido.actualizar-foto-vehiculo") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                // Dentro de la función handleVehiculoFoto, en la respuesta success:
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    console.log('Respuesta:', response);

                    if (response.success) {
                        if (response.es_principal) {
                            // Actualizar la variable vehiculoActual
                            vehiculoActual.foto_vehiculo = response.ruta_foto;

                            // Guardar también la URL completa
                            vehiculoActual.foto_url = response.url_foto;

                            // Actualizar la imagen en la vista usando la URL del backend
                            let fotoUrl = response.url_foto;
                            $('#vehiculoFotoPreview').attr('src', fotoUrl);

                            $('#vehiculoData .mt-2').html(`
                <span class="badge" style="background: var(--pastel-green); color: #2c3e50;">
                    <i class="ri-image-line"></i> Foto registrada
                </span>
            `);

                            mostrarNotificacion('✅ Foto del vehículo actualizada correctamente', 'success');

                            setTimeout(() => {
                                $('#vehiculoFotoPreview').attr('src', fotoUrl + '?t=' + new Date().getTime());
                            }, 100);
                        }
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').fadeOut();
                    console.error('Error:', xhr);

                    let mensaje = 'Error al guardar la foto';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                    alert('❌ ' + mensaje);
                }
            });
        }

        function actualizarGaleriaFotos(fotos) {
            if (!$('#galeriaFotosVehiculo').length) {
                // Si no existe el contenedor, crearlo
                $('#vehiculoData').append(`
            <div class="mt-3" id="galeriaFotosVehiculo">
                <h6><i class="ri-image-line"></i> Galería de Fotos</h6>
                <div class="row" id="galeriaFotosContainer"></div>
            </div>
        `);
            }

            let html = '';
            fotos.forEach(foto => {
                html += `
            <div class="col-md-3 mb-2">
                <img src="${foto.url}" class="img-thumbnail" style="height: 80px; object-fit: cover; cursor: pointer;"
                     onclick="verFotoAmpliada('${foto.url}')">
            </div>
        `;
            });

            $('#galeriaFotosContainer').html(html);
        }

        function setupVehiculoFotoDrop() {
            const principalArea = document.getElementById('vehiculoFotoPrincipalDropArea');
            const principalInput = document.getElementById('vehiculoFotoPrincipalInput');

            if (principalArea && principalInput) {
                // Remover eventos anteriores para evitar duplicados
                const newPrincipalArea = principalArea.cloneNode(true);
                const newPrincipalInput = principalInput.cloneNode(true);
                principalArea.parentNode.replaceChild(newPrincipalArea, principalArea);
                principalInput.parentNode.replaceChild(newPrincipalInput, principalInput);

                newPrincipalArea.addEventListener('click', () => newPrincipalInput.click());
                newPrincipalInput.addEventListener('change', (e) => {
                    console.log('Evento change disparado');

                    if (e.target.files && e.target.files.length > 0) {
                        const file = e.target.files[0];
                        console.log('Archivo seleccionado:', file.name);

                        // Mostrar preview inmediato antes de subir
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            $('#vehiculoFotoPreview').attr('src', event.target.result);
                            mostrarNotificacion('📸 Procesando imagen...', 'info');
                        };
                        reader.readAsDataURL(file);

                        handleVehiculoFoto(file, 'foto_principal');
                        e.target.value = ''; // Limpiar para permitir subir la misma foto nuevamente
                    }
                });
            }
        }

        function verFotoAmpliada(url) {
            if ($('#fotoAmpliadaModal').length === 0) {
                let modalHtml = `
            <div class="modal fade" id="fotoAmpliadaModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background: var(--pastel-purple);">
                            <h5 class="modal-title">Foto del Vehículo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img src="" id="fotoAmpliada" style="max-width: 100%; max-height: 80vh; border-radius: 8px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
                $('body').append(modalHtml);
            }

            $('#fotoAmpliada').attr('src', url);
            $('#fotoAmpliadaModal').modal('show');
        }

    </script>
@endsection
