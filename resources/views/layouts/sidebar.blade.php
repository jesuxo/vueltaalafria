<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/dashboard" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <a href="/dashboard" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" style="background: #f2f2f2;">
        <div class="container-fluid">
            <div id="two-column-menu"></div>

            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('t-menu') }}</span></li>

                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ url('/admin') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                @if(Auth::user() && auth()->user()->type == 'admin')

                    <!-- Gestión de Fotos -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPhotos" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarPhotos">
                            <i class="bi bi-camera"></i>
                            <span data-key="t-photos">Fotos</span>
                            <span class="badge bg-success rounded-pill ms-2">Nuevo</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarPhotos">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/fotos') }}" class="nav-link" data-key="t-upload-photos">
                                        <i class="ri-upload-cloud-line me-2"></i> Subir Fotos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/galeria') }}" class="nav-link" data-key="t-gallery">
                                        <i class="ri-image-line me-2"></i> Ver Galería
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/photos/pending') }}" class="nav-link" data-key="t-pending-photos">
                                        <i class="ri-time-line me-2"></i> Fotos Pendientes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/photos/orders') }}" class="nav-link" data-key="t-photo-orders">
                                        <i class="ri-shopping-cart-line me-2"></i> Pedidos de Fotos
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Gestión de Etapas -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarStages" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarStages">
                            <i class="bi bi-flag"></i>
                            <span data-key="t-stages">Etapas</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarStages">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/stages') }}" class="nav-link" data-key="t-list-stages">
                                        <i class="ri-list-check me-2"></i> Lista de Etapas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/stages/create') }}" class="nav-link" data-key="t-create-stage">
                                        <i class="ri-add-line me-2"></i> Crear Etapa
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/stages/schedules') }}" class="nav-link" data-key="t-schedules">
                                        <i class="ri-calendar-line me-2"></i> Horarios
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Resultados y Clasificaciones -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarResults" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarResults">
                            <i class="bi bi-trophy"></i>
                            <span data-key="t-results">Resultados</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarResults">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/results/standings') }}" class="nav-link" data-key="t-standings">
                                        <i class="ri-medal-line me-2"></i> Clasificación General
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/results/points') }}" class="nav-link" data-key="t-points">
                                        <i class="ri-star-line me-2"></i> Puntos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/results/mountain') }}" class="nav-link" data-key="t-mountain">
                                        <i class="ri-mountain-line me-2"></i> Montaña
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/results/team') }}" class="nav-link" data-key="t-team">
                                        <i class="ri-team-line me-2"></i> Clasificación por Equipos
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Gestión de Equipos -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTeams" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarTeams">
                            <i class="bi bi-people"></i>
                            <span data-key="t-teams">Equipos</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTeams">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/teams') }}" class="nav-link" data-key="t-list-teams">
                                        <i class="ri-team-line me-2"></i> Lista de Equipos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/teams/create') }}" class="nav-link" data-key="t-create-team">
                                        <i class="ri-add-line me-2"></i> Crear Equipo
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/athletes') }}" class="nav-link" data-key="t-athletes">
                                        <i class="ri-user-line me-2"></i> Atletas
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Inscripciones -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarRegistrations" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarRegistrations">
                            <i class="bi bi-clipboard-check"></i>
                            <span data-key="t-registrations">Inscripciones</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarRegistrations">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/registrations') }}" class="nav-link" data-key="t-all-registrations">
                                        <i class="ri-list-check me-2"></i> Todas las Inscripciones
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/registrations/pending') }}" class="nav-link" data-key="t-pending">
                                        <i class="ri-time-line me-2"></i> Pendientes
                                        <span class="badge bg-warning rounded-pill ms-2" id="pendingCount">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/registrations/approved') }}" class="nav-link" data-key="t-approved">
                                        <i class="ri-checkbox-circle-line me-2"></i> Aprobadas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/registrations/paid') }}" class="nav-link" data-key="t-paid">
                                        <i class="ri-money-dollar-circle-line me-2"></i> Pagadas
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Reportes -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarReports">
                            <i class="bi bi-graph-up"></i>
                            <span data-key="t-reports">Reportes</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReports">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/reports/financial') }}" class="nav-link" data-key="t-financial">
                                        <i class="ri-money-dollar-circle-line me-2"></i> Reporte Financiero
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/reports/athletes') }}" class="nav-link" data-key="t-athletes-report">
                                        <i class="ri-user-line me-2"></i> Reporte de Atletas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/reports/teams') }}" class="nav-link" data-key="t-teams-report">
                                        <i class="ri-team-line me-2"></i> Reporte de Equipos
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Configuración -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarSettings">
                            <i class="bi bi-gear"></i>
                            <span data-key="t-settings">Configuración</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSettings">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/settings') }}" class="nav-link" data-key="t-general">
                                        <i class="ri-settings-3-line me-2"></i> General
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/settings/categories') }}" class="nav-link" data-key="t-categories">
                                        <i class="ri-price-tag-line me-2"></i> Categorías
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                @endif

                <!-- Panel de Equipo (para usuarios tipo team) -->
                @if(Auth::user() && auth()->user()->type == 'team')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ url('/equipo/dashboard') }}">
                            <i class="bi bi-building"></i>
                            <span data-key="t-team-panel">Mi Equipo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ url('/equipo/atletas') }}">
                            <i class="bi bi-people"></i>
                            <span data-key="t-team-athletes">Mis Atletas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ url('/equipo/inscripcion') }}">
                            <i class="bi bi-clipboard-check"></i>
                            <span data-key="t-team-registration">Inscripción</span>
                        </a>
                    </li>
                @endif

                <!-- Separador -->
                <li class="menu-title mt-3"><span data-key="t-utilities">Utilidades</span></li>

                <!-- Galería Pública -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ url('/galeria') }}" target="_blank">
                        <i class="bi bi-camera"></i>
                        <span data-key="t-public-gallery">Ver Galería Pública</span>
                        <i class="ri-external-link-line ms-1 fs-14"></i>
                    </a>
                </li>

            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
<div class="vertical-overlay"></div>

<script>
    // Script para actualizar el contador de inscripciones pendientes
    document.addEventListener('DOMContentLoaded', function() {
        // Función para obtener el número de inscripciones pendientes
        function updatePendingCount() {
            fetch('{{ url("/admin/registrations/pending/count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('pendingCount');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Actualizar cada 30 segundos
        updatePendingCount();
        setInterval(updatePendingCount, 30000);
    });
</script>
