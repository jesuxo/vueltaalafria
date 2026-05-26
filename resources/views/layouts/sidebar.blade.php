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
        <a href=/dashboard" class="logo logo-light">
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

    <div id="scrollbar" style="background: #f2f2f2;"  >
        <div class="container-fluid" >

            <div id="two-column-menu">

            </div>
            <ul class="navbar-nav" id="navbar-nav" >
                <li class="menu-title"><span data-key="t-menu">{{ __('t-menu') }}</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPanel" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPanel">
                        <i class="bi bi-speedometer2"></i> <span data-key="t-products">Panel</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPanel">
                        @if(Auth::user() and auth()->user()->type == 'cliente')
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item"  >
                                    <a href="/dashboard" class="nav-link" data-key="t-create-product">Inicio</a>
                                </li>

                                <li class="nav-item"  >
                                    <a href="/" class="nav-link" data-key="t-create-product">P&aacute;gina Web</a>
                                </li>
                            </ul>
                        @endif
                        @if(Auth::user() and auth()->user()->type == 'admin')
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item"  >
                                    <a href="/" class="nav-link" data-key="t-create-product">Inicio</a>
                                </li>
                            </ul>
                            <ul class="nav nav-sm flex-column">

                                <li class="nav-item"  >
                                    <a href="/reporte/venta" class="nav-link" data-key="t-create-product">Reporte de ventas</a>
                                </li>
                                <li class="nav-item"  >
                                    <a href="/reporte/instpagobs" class="nav-link" data-key="t-create-product">Rep. Inst Pago Bs</a>
                                </li>
                                <li class="nav-item"  >
                                    <a href="/reporte/instpagodolares" class="nav-link" data-key="t-create-product">Rep. Inst Pago Dolares</a>
                                </li>
                                <li class="nav-item"  >
                                    <a href="/ventas/productos/sucursales" class="nav-link" data-key="t-create-product">Venta por sucursal</a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </li>

                @if(Auth::user() and auth()->user()->type == 'admin')


                    @if(Auth::user()  and auth()->user()->can('menu_token') )
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/reporte/tokens"   >
                                <i class="bi bi-key"></i> <span data-key="t-sellers">Tokens</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user() and auth()->user()->type == 'admin')
                    <li class="nav-item">
                        <a class="nav-link menu-link btn btn-warning "
                           style="background-color: antiquewhite !important; padding-left: 14px !important;" href="#servmtto" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="servmtto">
                            <i class="bi bi-share"></i> <span data-key="t-products">Serv/Mantenimientos</span>
                        </a>
                        <div class="collapse menu-dropdown" id="servmtto">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('mantenimiento.rapido') }}" class="nav-link" data-key="t-list-view"><i class="ri-flashlight-fill "></i> Nuevo Mantenimiento</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('mantenimientos.diario') }}" class="nav-link" data-key="t-list-view"><i class="bi bi-calendar "></i>Reporte Diario</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('vehiculos.index') }}" class="nav-link" data-key="t-list-view"> <i class="bi bi-car-front"></i> Vehículos</a>
                                </li>
                                <li class="nav-item">
                                    <a  href="{{ route('reportes.proximos-mantenimientos') }}"class="nav-link" data-key="t-list-view"> <i class="ri-calendar-check-line"></i> Prox Mantenimientos
                                        @php
                                            $urgentes = \App\Models\CWMantenimiento::whereNotNull('proximo_mantenimiento')
                                                ->whereDate('proximo_mantenimiento', '<=', now()->addDays(2))
                                                ->whereDate('proximo_mantenimiento', '>=', now())
                                                ->where('cliente_contactado', false)
                                                ->count();
                                        @endphp
                                        @if($urgentes > 0)
                                            <span class="badge bg-danger ms-2">{{ $urgentes }}</span>
                                        @endif
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="bi bi-box-seam"></i> <span data-key="t-products">Inventario</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/productos" class="nav-link" data-key="t-list-view">Productos</a>
                            </li>
                            @if(Auth::user() and auth()->user()->type == 'admin')
                            <li class="nav-item">
                                <a href="/existencias" class="nav-link" data-key="t-list-view">Existencias</a>
                            </li>

                            @endif
                            <li class="nav-item">
                                <a href="/instancias" class="nav-link" data-key="t-sub-categories">{{ __('t-sub-categories') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="/depositos" class="nav-link" data-key="t-list-view">Dep&oacute;sitos</a>
                            </li>

                        </ul>
                    </div>
                </li>

                    @if(Auth::user()  and auth()->user()->can('menu_transferencias') )
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarTransferencias"
                               data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTransferencias">
                                <i class="bi bi-box-seam"></i> <span data-key="t-products">Transferencias</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarTransferencias">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('reportetransferencias')}}" class="nav-link" data-key="t-list-view">Ver Transferencias</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/transferencias/create" class="nav-link" data-key="t-list-view">Agregar Transferencia</a>
                                    </li>
                                    <li class="nav-item" style="display: none">
                                        <a href="/transferencia/informacion" class="nav-link" data-key="t-list-view">Info P/Transferencias</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                <li class="nav-item"  >
                    <a class="nav-link menu-link" href="#sidebarClientes" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarClientes">
                        <i class="bi bi-person-bounding-box"></i> <span data-key="t-orders">Clientes</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarClientes">
                        <ul class="nav nav-sm flex-column">

                            @if(Auth::user() and auth()->user()->type == 'admin')
                                <li class="nav-item">
                                    <a href="/clientes" class="nav-link" data-key="t-list-view">Listado Clientes</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/cxc" class="nav-link" data-key="t-list-view">Cuentas x Cobrar</a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="/vendedores"  >
                        <i class="bi bi-binoculars"></i> Vendedores
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="/instpago"  >
                        <i class="bi bi-cash-coin"></i> Inst de Pago
                    </a>
                </li>
                @endif

                @if(Auth::user() and auth()->user()->type == 'cliente')
                <li class="nav-item"  style="display: none">
                    <a class="nav-link menu-link" href="#sidebarShipping" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarShipping">
                        <i class="bi bi-truck"></i> <span data-key="t-shipping">{{ __('t-shipping') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarShipping">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="shipping-list" class="nav-link" data-key="t-shipping-list">{{ __('t-shipping-list') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="shipments" class="nav-link" data-key="t-shipments">{{ __('t-shipments') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item"  style="">
                    <a class="nav-link menu-link" href="/components/index" target="_blank">
                        <i class="bi bi-layers"></i> <span data-key="t-components">Alertas de mttos</span>
                        <span class="badge badge-pill bg-secondary" data-key="t-v1.0">3 p</span>
                    </a>
                </li>

                @endif

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
