
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight" style="color: white">PANEL DE CONTROL</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">

                <!-- ROLES Y PERMISO -->
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
                                <p>Rol y Permisos</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.permisos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuario</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.usuario.departamento.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuario Departamento</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.usuario.formulador.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuario Formulador</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.usuario.consolidador.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuario Consolidador</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('admin.informacion.consolidador.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Información Consolidador</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.solicitudit.control.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Solicitud IT</p>
                            </a>
                        </li>

                    </ul>
                 </li>
                @endcan


                <!-- Para que jefe de presupuesto pueda ver los usuarios asignados a las unidades -->
                @can('sidebar.usuarios.departamentos.asignados')

                    <li class="nav-item">
                        <a href="{{ route('admin.usuario.departamento.vista.index') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Usuario Departamento</p>
                        </a>
                    </li>
                @endcan



                <!-- ESTADISTICAS -->
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


                <!-- PROYECTOS -->
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


                    </ul>
                </li>
                @endcan


                <!-- REQUERIMIENTOS PARA PROYECTOS -->
                @can('sidebar.seccion.requerimientos')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Requerimientos
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            @can('sidebar.requerimientos.listar')
                                <li class="nav-item">
                                    <a href="{{ route('admin.listar.requerimientos.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listar Requerimientos</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                <!-- COTIZACIONES PARA PROYECTOS -->
                @can('sidebar.seccion.cotizaciones')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="fas fa-tasks"></i>
                        <p>
                            Cotizaciones Proyecto
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        @can('sidebar.cotizacion.pendiente')
                        <li class="nav-item">
                            <a href="{{ route('cotizaciones.pendientes.proyecto.index') }}" target="frameprincipal" class="nav-link">
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




            <!-- ORDENES DE COMPRA PARA PROYECTOS -->
                @can('sidebar.seccion.ordenescompra')

                        <li class="nav-item">

                            <a href="#" class="nav-link nav-">
                                <i class="far fa-edit"></i>
                                <p>
                                    Orden de Compra Proy.
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                @can('ordenes.compras.procesadas.index')
                                    <li class="nav-item has-treeview">
                                        <a href="{{ route('admin.ordenes.compra.procesadas') }}" target="frameprincipal" class="nav-link">
                                            <i class="nav-icon fas fa-list"></i>
                                            <p>
                                                Ordenes Procesadas
                                            </p>
                                        </a>
                                    </li>
                                @endcan

                                @can('ordenes.compras.denegadas.index')
                                    <li class="nav-item has-treeview">
                                        <a href="{{ route('admin.ordenes.compra.denegadas') }}" target="frameprincipal" class="nav-link">
                                            <i class="nav-icon fas fa-list"></i>
                                            <p>
                                                Ordenes Denegadas
                                            </p>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </li>

                @endcan

                <!-- REQUERIMIENTOS PARA UNIDAD -->
                @can('sidebar.seccion.requerimientosunidad')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Reque. de Agrupados
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">



                            <li class="nav-item">
                                <a href="{{ route('admin.fecha.de.agrupapos.ucp') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listar Agrupados</p>
                                </a>
                            </li>



                            @can('sidebar.requerimientos.listar.denegados')
                                <li class="nav-item">
                                    <a href="{{ route('admin.listar.requerimientos.denegados.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Agrupados Denegados</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan


                <!-- COTIZACIONES PARA UNIDAD -->
                @can('sidebar.seccion.cotizaciones.unidad')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="fas fa-tasks"></i>
                            <p>
                                Cotizaciones Unidad
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            @can('sidebar.cotizacion.unidad.pendiente')
                                <li class="nav-item">
                                    <a href="{{ route('cotizaciones.pendientes.unidad.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cotización Pendiente</p>
                                    </a>
                                </li>
                            @endcan

                            @can('sidebar.cotizacion.unidad.procesada')
                                <li class="nav-item">
                                    <a href="{{ route('cotizaciones.autorizadas.unidad.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cotización Procesada</p>
                                    </a>
                                </li>
                            @endcan

                                @can('sidebar.cotizacion.unidad.denegadas')
                                    <li class="nav-item">
                                        <a href="{{ route('cotizaciones.denegadas.unidad.index') }}" target="frameprincipal" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Cotización Denegada</p>
                                        </a>
                                    </li>
                                @endcan


                        </ul>
                    </li>
                @endcan



            <!-- ORDENES DE COMPRA PARA UNIDADES -->
                @can('sidebar.seccion.ordenescompra.unidades')

                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Ordenes de Compra Unidad
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            @can('ordenes.compras.procesadas.unidades.index')
                                <li class="nav-item">
                                    <a href="{{ route('admin.ordenes.compra.unidades.procesadas') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            Ordenes Procesadas
                                        </p>
                                    </a>
                                </li>
                            @endcan


                        </ul>
                    </li>

                @endcan

            <!-- DESCARGOS DIRECTOS -->
                @can('sidebar.descargos.directos')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Descargos Directos
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            @can('sidebar.descargos.directos.revision')
                                <li class="nav-item">
                                    <a href="{{ route('crear.descargos.directos') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear</p>
                                    </a>
                                </li>
                            @endcan

                            @can('sidebar.descargos.directos.historial')
                                <li class="nav-item">
                                    <a href="{{ route('historial.descargos.directos') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Historial</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan



            <!-- CONFIGURACIONES PARA PROYECTO -->
                @can('sidebar.seccion.configuraciones')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Configuración Proyectos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        @can('sidebar.rubro')
                            <li class="nav-item">
                                <a href="{{ route('admin.rubro.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rubro</p>
                                </a>
                            </li>
                        @endcan

                        @can('sidebar.cuenta')
                            <li class="nav-item">
                                <a href="{{ route('admin.cuenta.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cuenta</p>
                                </a>
                            </li>
                        @endcan

                        @can('sidebar.obj.especifico')
                        <li class="nav-item">
                            <a href="{{ route('admin.obj.especifico.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Obj. Específico</p>
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

                        @can('sidebar.clasificaciones')
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
                                <p>Materiales Proyecto</p>
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
                        @can('sidebar.adescos')
                        <li class="nav-item">
                            <a href="{{ route('admin.adescos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Adescos</p>
                            </a>
                        </li>
                        @endcan
                        @can('sidebar.equipos')
                        <li class="nav-item">
                            <a href="{{ route('admin.equipos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Equipos</p>
                            </a>
                        </li>
                        @endcan
                        @can('sidebar.asociaciones')
                        <li class="nav-item">
                            <a href="{{ route('admin.asociaciones.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Asociaciones</p>
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

                        @can('sidebar.solicitud.material.uaci')
                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.material.ing.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Solicitud Material ING</p>
                                </a>
                            </li>
                        @endcan

                            <!-- es una vista del catalogo de materiales, para que ingeniería busque un material -->
                        @can('sidebar.vista.catalogo.materiales.ing')
                            <li class="nav-item">
                                <a href="{{ route('admin.vista.catalogo.material.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Catálogo de Materiales</p>
                                </a>
                            </li>
                        @endcan

                        @can('sidebar.vista.bolson')
                            <li class="nav-item">
                                <a href="{{ route('admin.bolson.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bolsón</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endcan


                <!-- CONFIGURACIONES PARA UNIDADES -->
                @can('sidebar.seccion.configuraciones.presupuesto.unidades')

                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Configuración Pres. Unidades
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            @can('p.anio.presupuesto.unidades')
                            <li class="nav-item">
                                <a href="{{ route('p.admin.anio.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Año Presupuesto</p>
                                </a>
                            </li>
                            @endcan

                            @can('p.departamento.presupuesto.unidades')
                            <li class="nav-item">
                                <a href="{{ route('p.admin.departamentos.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Departamentos</p>
                                </a>
                            </li>
                            @endcan

                            @can('p.unidadmedida.presupuesto.unidades')
                            <li class="nav-item">
                                <a href="{{ route('p.admin.unidadmedida.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Unidad Medida</p>
                                </a>
                            </li>
                            @endcan

                            @can('p.materiales.presupuesto.unidades')
                            <li class="nav-item">
                                <a href="{{ route('p.admin.materiales.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Materiales Unidad</p>
                                </a>
                            </li>
                            @endcan


                            @can('p.cuentas.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.admin.cuentas.unidades.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cuentas Unidades</p>
                                    </a>
                                </li>
                            @endcan

                            @can('p.sidebar.historico.movimiento.pendientes.cuentas.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.admin.movimientos.pendientes.historicos.unidades.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Movimientos Pendientes</p>
                                    </a>
                                </li>
                            @endcan

                            @can('p.sidebar.historico.movimiento.aprobados.cuentas.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.admin.movimientos.aprobados.historicos.unidades.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Movimientos Autorizados</p>
                                    </a>
                                </li>
                            @endcan

                            @can('p.sidebar.solicitud.materiales.cuentas.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.admin.nuevas.solicitudes.materiales') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Solicitudes de Materiales</p>
                                    </a>
                                </li>
                            @endcan

                            @can('p.sidebar.solicitud.aprobadas.materiales.cuentas.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.admin.nuevas.solicitudes.materiales.aprobados') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Solicitudes Aprobadas</p>
                                    </a>
                                </li>
                            @endcan


                            @can('p.sidebar.referencias')
                                <li class="nav-item">
                                    <a href="{{ route('admin.referencias.orden.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Referencias</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                    <!-- REVISION DE PRESUPUESTO UNIDADES Y REPORTES -->
                    @can('sidebar.revision.presupuesto.unidades')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Reportes de Unidades
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            @can('p.revision.presupuesto.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.revision.presupuesto.unidad') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Revisión</p>
                                    </a>
                                </li>
                            @endcan

                            @can('p.generar.reportes.presupuesto.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.generar.reportes.presupuesto.unidad') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reporte Totales</p>
                                    </a>
                                </li>
                            @endcan

                            @can('p.generar.reportes.presupuesto.movimientos.unidades')
                                <li class="nav-item">
                                    <a href="{{ route('p.generar.reportes.presupuesto..movimientos.unidad') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reporte Movimientos</p>
                                    </a>
                                </li>
                            @endcan

                            @can('p.generar.reporte.materiales.anualcompras')
                                <li class="nav-item">
                                    <a href="{{ route('p.generar.reportes.uaci.unidad') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reporte Plan Anual</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                    @endcan

                <!-- CREACION DE PRESUPUESTO DE UNIDADES -->
                @can('sidebar.p.presupuesto.crear')
                    <li class="nav-item">

                        <a href="#" class="nav-link">
                            <i class="far fa-edit"></i>
                            <p>
                                Mi Presupuesto
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            @can('p.crear.presupuesto.unidad')
                            <li class="nav-item">
                                <a href="{{ route('p.admin.crear.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Crear</p>
                                </a>
                            </li>
                            @endcan

                            @can('p.editar.presupuesto.unidad')
                                <li class="nav-item">
                                    <a href="{{ route('p.admin.editar.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Editar</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan


                <!-- VISTA PARA AÑOS DONDE QUIERE HACER REQUERIMIENTO LA UNIDAD  -->
                @can('sidebar.p.requerimientos.unidades')
                    <li class="nav-item has-treeview">
                        <a href="{{ route('admin.p.unidad.requerimientos.index') }}" target="frameprincipal" class="nav-link">
                            <i class="nav-icon fas fa-list-alt"></i>
                            <p>
                                Requerimientos
                            </p>
                        </a>

                        <a href="{{ route('admin.p.solicitudesit.bloquefecha') }}" target="frameprincipal" class="nav-link">
                            <i class="nav-icon fas fa-list-alt"></i>
                            <p>
                                Solicitudes IT
                            </p>
                        </a>
                    </li>
                @endcan



                <!-- VISTA PARA USUARIOS CONSOLIDADORES -->

                @can('sidebar.usuarios.consolidadores')


                    <li class="nav-item">
                        <a href="{{ route('requerimientos.pendientes.consolidadoras') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Requerimientos</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('requerimientos.consolidador.agrupados') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Agrupados</p>
                        </a>
                    </li>


                @endcan









                <!-- SECRETARIA DESPACHO -->

                @can('sidebar.secretaria.despacho')
                    <a href="{{ route('sidebar.secretaria.calendario') }}" target="frameprincipal" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Calendario</p>
                    </a>

                    <a href="{{ route('sidebar.secretaria.despacho') }}" target="frameprincipal" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Solicitudes Despacho</p>
                    </a>
                    <a href="{{ route('sidebar.secretaria.transporte') }}" target="frameprincipal" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Admin. Transporte</p>
                    </a>


                    <a href="{{ route('sidebar.reportes.despacho') }}" target="frameprincipal" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Reportes</p>
                    </a>

                @endcan

                <!-- BODEGA -->

                @can('bodega.control')
                    <a href="{{ route('sidebar.bodega.materiales') }}" target="frameprincipal" class="nav-link">
                        <i class="fa fa-book nav-icon"></i>
                        <p>Materiales</p>
                    </a>
                    <a href="{{ route('sidebar.bodega.solicitudes') }}" target="frameprincipal" class="nav-link">
                        <i class="far fa-list-alt nav-icon"></i>
                        <p>Solicitudes</p>
                    </a>
                    <a href="{{ route('sidebar.bodega.entradas') }}" target="frameprincipal" class="nav-link">
                        <i class="fa fa-chevron-circle-left"></i>
                        <p>Entradas</p>
                    </a>
                    <a href="{{ route('sidebar.bodega.salidas') }}" target="frameprincipal" class="nav-link">
                        <i class="fa fa-chevron-circle-right"></i>
                        <p>Salidas</p>
                    </a>

                @endcan







                @can('bodega.solicitud')
                    <a href="{{ route('sidebar.bodega.solicitud') }}" target="frameprincipal" class="nav-link">
                        <i class="far fa-edit nav-icon"></i>
                        <p>Solicitud de Materiales</p>
                    </a>
                @endcan
                @can('bodega.reportes')
                    <a href="{{ route('sidebar.bodega.reportes') }}" target="frameprincipal" class="nav-link">
                        <i class="fa fa-list nav-icon"></i>
                        <p>Reportes</p>
                    </a>
                @endcan



                <!-- RECURSOS HUMANOS INGRESO DE DATOS -->
                @can('sidebar.rrhh.datos.personal')
                <a href="{{ route('sidebar.rrhh.actualizacion.datos') }}" target="frameprincipal" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Datos</p>
                </a>
                @endcan









                <!-- SINDICATURA -->
                @can('sidebar.sindico')


                    <li class="nav-item">
                        <a href="{{ route('admin.sindico.registro.index') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Registrar</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.sindico.registro.todos.index') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Todos</p>
                        </a>
                    </li>

                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Configuración
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{ route('admin.sindico.estados.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Estados</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.sindico.tiposolicitud.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tipo Solicitud</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.sindico.tipodeligencia.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tipo Diligencia</p>
                                </a>
                            </li>

                        </ul>
                    </li>




                @endcan


            </ul>
        </nav>


    </div>
</aside>






