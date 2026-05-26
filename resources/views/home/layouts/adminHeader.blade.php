<header class=" header_area">
    <nav class="navbar navbar-expand-lg menu_one menu_four">
        <div class="container">
            <div class=" search-form input-group"  >
                <input type="text"  autocomplete="off" name="busqueda" value="" class="form-control search-field" id="busqueda" placeholder="Puedes buscar por descripcion, marca" style="font-weight: 100 !important;">
                <span class="input-group-addon">
                    <button type="button"  style="opacity: 0 !important;" >
                        <i class="ti-search cursor_pointer btn-search"></i>
                    </button>
                </span>
                <input type="hidden" name="post_type"  data-unset="1"   value="product">
            </div>

            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="menu_toggle">
                            <span class="hamburger">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                            <span class="hamburger-cross">
                                <span></span>
                                <span></span>
                            </span>
                        </span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                <ul class="navbar-nav menu w_menu pl_100">

                    <li class="nav-item dropdown submenu mega_menu mega_menu_two">
                        <a class="nav-link d-flex" href="{{route('dashboard')}}" style="color: #333;"  >
                            <i class="ti-layers-alt mr-2"></i>
                            <div style="font-weight: 200">
                            <?php
                                $productos = \App\Models\Saprod::->count();
                                echo $productos;
                            ?>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item dropdown submenu mega_menu mega_menu_two">
                        <a class="nav-link abrir-compras " href="javascript:;" style="color: #333;"  >
                            Compras
                        </a>
                    </li>

                    <li class="nav-item dropdown submenu mega_menu mega_menu_two">
                        <a class="nav-link abrir-promociones " href="javascript:;" style="color: #333;"  >
                            Promociones
                        </a>
                    </li>

                    <li class="nav-item dropdown submenu mega_menu mega_menu_two">
                        <a class="nav-link" href="{{route('promos.create')}}" style="color: #333;"  >
                            +1
                        </a>
                    </li>

                    <li class="nav-item dropdown submenu mega_menu mega_menu_two">
                        <a class="nav-link dropdown-toggle" href="javascript:;"   style="color: #333;"  onclick="document.getElementById('logout-form').submit();" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Salir
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </a>
                    </li>

                </ul>

            </div>

        </div>
    </nav>
</header>
