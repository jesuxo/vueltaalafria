@extends('layouts.master')
@section('title')
    Dashboard - Stars Motors
@endsection
@section('css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0072c5, #0059a3);
            --secondary-gradient: linear-gradient(135deg, #7c6bff, #6356cc);
            --success-gradient: linear-gradient(135deg, #06d6a0, #05ab80);
            --warning-gradient: linear-gradient(135deg, #f1be46, #c19838);
            --danger-gradient: linear-gradient(135deg, #ef476f, #bf3959);
        }

        /* Estadísticas principales */
        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 114, 197, 0.1);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stats-card:hover::before {
            opacity: 1;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, rgba(0, 114, 197, 0.1), rgba(0, 89, 163, 0.1));
            color: #0072c5;
            transition: all 0.3s ease;
        }

        .stats-card:hover .stats-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #0c192c;
        }

        .stats-label {
            color: #878a99;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tarjetas de acceso rápido */
        .quick-access-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            cursor: pointer;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .quick-access-card:hover {
            transform: translateY(-5px);
            border-color: #0072c5;
            box-shadow: 0 10px 30px rgba(0, 114, 197, 0.15);
        }

        .quick-access-card .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .quick-access-card.primary .icon-wrapper {
            background: rgba(0, 114, 197, 0.1);
            color: #0072c5;
        }

        .quick-access-card.success .icon-wrapper {
            background: rgba(6, 214, 160, 0.1);
            color: #06d6a0;
        }

        .quick-access-card.warning .icon-wrapper {
            background: rgba(241, 190, 70, 0.1);
            color: #f1be46;
        }

        .quick-access-card.purple .icon-wrapper {
            background: rgba(124, 107, 255, 0.1);
            color: #7c6bff;
        }

        .quick-access-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .quick-access-card h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #0c192c;
        }

        .quick-access-card p {
            font-size: 0.85rem;
            color: #878a99;
            margin-bottom: 0;
        }

        /* Tarjetas de sección */
        .section-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.02);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header h5 {
            margin: 0;
            font-weight: 600;
            color: #0c192c;
            font-size: 1.1rem;
        }

        .section-header h5 i {
            margin-right: 10px;
            color: #0072c5;
        }

        .section-body {
            padding: 1.5rem;
        }

        /* Menú de categorías */
        .category-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 0.5rem;
        }

        .category-item {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            text-decoration: none;
            color: inherit;
        }

        .category-item:hover {
            transform: translateY(-5px);
            border-color: #0072c5;
            box-shadow: 0 10px 30px rgba(0, 114, 197, 0.1);
        }

        .category-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 24px;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .category-item:hover .category-icon {
            border-color: #0072c5;
            transform: scale(1.1);
        }

        .category-item h6 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #0c192c;
        }

        .category-item small {
            font-size: 0.8rem;
            color: #878a99;
        }

        /* Grid de acceso rápido */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .quick-grid-item {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            text-decoration: none;
            color: inherit;
        }

        .quick-grid-item:hover {
            transform: translateY(-5px);
            border-color: #0072c5;
            box-shadow: 0 10px 30px rgba(0, 114, 197, 0.1);
        }

        .quick-grid-item i {
            font-size: 2rem;
            color: #0072c5;
            margin-bottom: 0.5rem;
            display: block;
        }

        .quick-grid-item span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .quick-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Contador de mantenimientos - NUEVO */
        .maintenance-counter {
            background: linear-gradient(135deg, #0072c5, #0059a3);
            border-radius: 16px;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 114, 197, 0.3);
            position: relative;
            overflow: hidden;
        }

        .maintenance-counter::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .counter-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .counter-content {
            position: relative;
            z-index: 1;
        }

        .counter-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .counter-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .counter-link {
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            opacity: 0.8;
            transition: opacity 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .counter-link:hover {
            opacity: 1;
            color: white;
        }

        .counter-link:hover i {
            transform: translateX(5px);
        }

        .counter-link i {
            transition: transform 0.3s ease;
        }

        /* Animaciones */
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

        .stats-card, .quick-access-card, .section-card, .maintenance-counter {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        /* Badges y etiquetas */
        .badge-gradient {
            background: var(--primary-gradient);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <!-- Barra superior con saludo y búsqueda -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="mb-1 fw-bold" style="color: #0c192c;">
                👋 ¡Bienvenido, jesus!
            </h3>
            <p class="text-muted mb-0">
                <i class="ri-calendar-line me-1"></i>
                {{ now()->format('l, d F Y') }}
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <!-- Contador de mantenimientos más compacto -->
            <a href="{{ route('mantenimientos.diario') }}" class="text-decoration-none">
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background: #f0f7ff; border: 1px solid #0072c5;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: #0072c5;">
                        <i class="ri-tools-line text-white" style="font-size: 16px;"></i>
                    </div>
                    <div>
                        <span class="fw-bold" style="color: #0c192c; font-size: 1.1rem;" id="mantenimientosCount">
                            Contando...
                        </span>
                        <span class="text-muted small d-none d-sm-inline">servicios hoy</span>
                    </div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </div>
            </a>

            <!-- Botón Registro Express -->

            <a href="{{ route('mantenimiento.rapido') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background: #f0f7ff; border: 1px solid #0072c5;">
                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: #0072c5;">
                    <i class="ri-flashlight-fill text-white" style="font-size: 16px;"></i>
                </div>
                <div>
                    <span class="fw-bold" style="color: #0c192c; font-size: 1.1rem;" >Registro Express</span>

                </div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>

        </div>
    </div>

    <!-- Script para cargar el contador (mantén el que ya tienes) -->


    <!-- Grid de acceso rápido -->
    <div class="quick-grid mb-4">
        <a href="/resumenVentas" class="quick-grid-item">
            <i class="ph-wallet"></i>
            <span>Resumen Ventas</span>
        </a>
        <a href="/existencias" class="quick-grid-item">
            <i class="ph-sketch-logo text-success"></i>
            <span>Inventario</span>
        </a>
        <a href="/cxc" class="quick-grid-item">
            <i class="ph-currency-dollar-bold"></i>
            <span>Cuentas x Cobrar</span>
        </a>
        <a href="/existencia/lubricantes" class="quick-grid-item">
            <i class="ri-oil-fill text-warning"></i>
            <span>Exist. Lubricantes</span>
        </a>
    </div>


    <!-- Sección de Ventas por Categoría -->
    <div class="section-card">
        <div class="section-header">
            <h5>
                <i class="ri-bar-chart-2-line"></i>
                Ventas por Categoría
            </h5>

        </div>
        <div class="section-body">
            <div class="category-menu">
                <a href="/reporte/lubricantes" class="category-item">
                    <div class="category-icon" style="color: #0072c5;">
                        <i class="ri-oil-line"></i>
                    </div>
                    <h6>Lubricantes</h6>
                    <small>345 ventas</small>
                </a>
                <a href="/reporte/filtros" class="category-item">
                    <div class="category-icon" style="color: #7c6bff;">
                        <i class="mdi mdi-air-filter"></i>
                    </div>
                    <h6>Filtros</h6>
                    <small  style="display: none">234 ventas</small>
                </a>
                <a href="/reporte/baterias" class="category-item">
                    <div class="category-icon" style="color: #06d6a0;">
                        <i class="bi-battery-charging"></i>
                    </div>
                    <h6>Baterías</h6>
                    <small  style="display: none">123 ventas</small>
                </a>
                <a href="/reporte/neumaticos" class="category-item" style="display: none">
                    <div class="category-icon" style="color: #f1be46;">
                        <i class="ri-tire-line"></i>
                    </div>
                    <h6>Neumáticos</h6>
                    <small  style="display: none">89 ventas</small>
                </a>
            </div>
        </div>
    </div>



    <!-- Accesos Rápidos Adicionales -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="quick-access-card primary">
                <div class="icon-wrapper">
                    <i class="ph-wallet"></i>
                </div>
                <h4>Ventas por Sucursal</h4>
                <p>Reporte de unidades vendidas</p>
                <a href="/ventas/productos/sucursales" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="quick-access-card success">
                <div class="icon-wrapper">
                    <i class="bi-border-top"></i>
                </div>
                <h4>Resultado General</h4>
                <p>Análisis de resultados</p>
                <a href="/ventas/resultado" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="quick-access-card warning">
                <div class="icon-wrapper">
                    <i class="ri-file-copy-line"></i>
                </div>
                <h4>Mantenimientos</h4>
                <p>Historial HOY</p>
                <a href="/mantenimientos/diario" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="quick-access-card purple">
                <div class="icon-wrapper">
                    <i class="ri-user-line"></i>
                </div>
                <h4>Clientes</h4>
                <p>Gestión de clientes</p>
                <a href="/clientes" class="stretched-link"></a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>

            $(document).ready(function() {
                cargarMantenimientosDelDia();
                setInterval(cargarMantenimientosDelDia, 300000);
            });

            function cargarMantenimientosDelDia() {
                $.ajax({
                    url: '/mantenimientos/diario/count',
                    method: 'GET',
                    success: function(response) {
                        $('#mantenimientosCount').html(response || 0);
                    }
                });
            }
    </script>
@endsection
