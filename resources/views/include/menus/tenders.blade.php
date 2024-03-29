<div class="pcoded-main-container">
    <div class="pcoded-wrapper">

        <nav class="pcoded-navbar">
            <div class="nav-list">
                <div class="pcoded-inner-navbar main-menu">
                    <div class="pcoded-navigation-label">Navegación</div>
                    <ul class="pcoded-item pcoded-left-item">
                        <li class="@if(Route::currentRouteName() == 'home') active @endif">
                            <a href="{{ route('home') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon"><i class="feather icon-shield"></i></span>
                                <span class="pcoded-mtext">Inicio</span>
                                {{-- <a href="/pdf/secuencia" class="btn btn-info" target="_blank">Flujo-Licitaciones</a>
                                <a href="/pdf/modalities" class="btn btn-info" target="_blank">Plazos de Procesos</a>  --}}
                            </a>
                            <a href="/pdf/change_pass" class="btn btn-primary" target="_blank">
                                <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                                <span class="pcoded-mtext">Cambiar Password</span>
                            </a>
                        </li>
                    </ul>

                    {{-- <div class="pcoded-navigation-label">Plazos de Procesos</div>
                        <a href="/pdf/modalities" class="btn btn-info" target="_blank">Plazos de Procesos</a>
                    </div> --}}

                    <div class="pcoded-navigation-label">Módulo de Licitaciones</div>
                    <ul class="pcoded-item pcoded-left-item">
                        <li class="pcoded-hasmenu">
                            <a href="javascript:void(0)" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="fa fa-clone"></i></span>
                            <span class="pcoded-mtext">Licitaciones</span>
                            </a>
                            <ul class="pcoded-submenu">
                                <li class="@if(Route::currentRouteName() == 'tenders.index') active @endif">
                                    <a href="{{ route('tenders.index') }}" class="waves-effect waves-dark">
                                    <span class="pcoded-mtext">Listado de Llamados</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
