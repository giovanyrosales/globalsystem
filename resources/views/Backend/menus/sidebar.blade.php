
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight" style="color: white">PANEL DE CONTROL</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">

                @can('sidebar.roles.y.permisos')
                 <li class="nav-item">

                     <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Roles y Permisos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.permisos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permisos</p>
                            </a>
                        </li>

                    </ul>
                 </li>
                @endcan

                @can('sidebar.estadisticas')
                <li class="nav-item has-treeview">
                    <a href="{{ route('admin.estadisticas.index') }}" target="frameprincipal" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Estadística
                        </p>
                    </a>
                </li>
                @endcan

                @can('sidebar.seccion.proyecto')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Proyectos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        @can('sidebar.nuevo.proyecto')
                        <li class="nav-item">
                            <a href="{{ route('admin.nuevo.proyecto.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nuevo Proyecto</p>
                            </a>
                        </li>
                        @endcan

                        @can('sidebar.lista.proyecto')
                        <li class="nav-item">
                            <a href="{{ route('admin.lista.proyectos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Proyectos</p>
                            </a>
                        </li>
                        @endcan

                        @can('sidebar.cuenta.proyecto')
                            <!--
                        <li class="nav-item">
                            <a href="#admin.cuenta.proyectos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cuenta Proyecto</p>
                            </a>
                        </li>
                            -->
                        @endcan


                        @can('sidebar.movimiento.cuenta')
                        <li class="nav-item">
                            <a href="{{ route('admin.movi.cuenta.proy.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Movimiento Cuenta Proyecto</p>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcan

                @can('sidebar.seccion.cotizaciones')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="fas fa-tasks"></i>
                        <p>
                            Cotizaciones
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        @can('sidebar.cotizacion.pendiente')
                        <li class="nav-item">
                            <a href="{{ route('cotizaciones.pendientes.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cotización Pendiente</p>
                            </a>
                        </li>
                        @endcan

                        @can('sidebar.cotizacion.procesada')
                        <li class="nav-item">
                            <a href="{{ route('cotizaciones.autorizadas.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cotización Procesada</p>
                            </a>
                        </li>
                        @endcan

                        @can('sidebar.cotizacion.denegada')
                        <li class="nav-item">
                            <a href="{{ route('cotizaciones.denegadas.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cotización Denegadas</p>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcan

                @can('sidebar.seccion.ordenescompra')
                <li class="nav-item has-treeview">
                    <a href="{{ route('ordenes.compras.index') }}" target="frameprincipal" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Ordenes de Compra
                        </p>
                    </a>
                </li>
                @endcan

                @can('sidebar.seccion.cuentabolson')
               <!-- <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Cuentas Bolsón
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>


                    <ul class="nav nav-treeview">
                        @can('sidebar.cuentabolson.cuenta')
                        <li class="nav-item">
                            <a href="{{ route('admin.bolson.cuenta.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cuenta</p>
                            </a>
                        </li>
                        @endcan

                        @can('sidebar.cuentabolson.movimiento')
                        <li class="nav-item">
                            <a href="{{ route('admin.movimiento.bolson.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Movimiento</p>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcan
                    -->

                @can('sidebar.seccion.configuraciones')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Configuraciones
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('sidebar.codigo.especifico')
                        <li class="nav-item">
                            <a href="{{ route('admin.cuenta.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Código Específ.</p>
                            </a>
                        </li>
                        @endcan

                            @can('sidebar.unidad.medida')
                        <li class="nav-item">
                            <a href="{{ route('admin.unidadmedida.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Unidad de Medida</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.calificaciones')
                        <li class="nav-item">
                            <a href="{{ route('admin.clasificaciones.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Clasificaciones</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.catalogo.materiales')
                        <li class="nav-item">
                            <a href="{{ route('admin.catalogo.materiales.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Catálogo Materiales</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.linea.trabajo')
                        <li class="nav-item">
                            <a href="{{ route('admin.linea.de.trabajo.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Línea de Trabajo</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.fuente.financiamiento')
                        <li class="nav-item">
                            <a href="{{ route('admin.fuente.financiamiento.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fuente Financiamiento</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.fuente.recursos')
                        <li class="nav-item">
                            <a href="{{ route('admin.fuente.recurso.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fuente de Recursos</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.area.gestion')
                        <li class="nav-item">
                            <a href="{{ route('admin.area.gestion.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Área de Gestión</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.proveedores')
                        <li class="nav-item">
                            <a href="{{ route('admin.proveedores.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Proveedores</p>
                            </a>
                        </li>
                            @endcan

                            @can('sidebar.administradores')
                        <li class="nav-item">
                            <a href="{{ route('admin.administradores.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Administradores</p>
                            </a>
                        </li>
                            @endcan

                    </ul>
                </li>
                @endcan

            </ul>
        </nav>

    </div>
</aside>






