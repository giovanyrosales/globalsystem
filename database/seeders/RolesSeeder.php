<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * roles y permisos
     *
     * @return void
     */
    public function run()
    {
        // --- CREAR ROLES ---

        // Administrador
        $roleAdmin = Role::create(['name' => 'admin']);

        // UACI
        $roleUaci = Role::create(['name' => 'uaci']);

        // Presupuesto
        $rolePresupuesto = Role::create(['name' => 'presupuesto']);

        // Ingenieria
        $roleIng = Role::create(['name' => 'formulador']);

        // Jefe UACI
        $roleJefeUaci = Role::create(['name' => 'jefeuaci']);

        // Administrador
        $roleAdministrador = Role::create(['name' => 'administrador']);

        // Secretaria
        $roleSecretaria = Role::create(['name' => 'secretaria']);

        // Uaci Unidad
        $roleUaciUnidad = Role::create(['name' => 'uaciunidad']);

        // PARA PRESUPUESTO DE UNIDADES
        // este crea y edita el presupuesto de una unidad
        $roleUnidad = Role::create(['name' => 'unidad']);

        // Recursos Humanos
        //$roleRRHH = Role::create(['name' => 'rrhh']);






        // ROLES Y PERMISOS
        Permission::create(['name' => 'sidebar.roles.y.permisos', 'description' => 'sidebar seccion roles y permisos'])->syncRoles($roleAdmin);

        // ESTADISTICAS
        Permission::create(['name' => 'sidebar.estadisticas', 'description' => 'sidebar seccion estadisticas'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleJefeUaci, $roleAdministrador, $roleSecretaria);


        // clasificaciones
        Permission::create(['name' => 'sidebar.clasificaciones', 'description' => 'clasificaciones de los materiales de proyecto'])->syncRoles($roleUaci, $rolePresupuesto);




        // SECCION PROYECTO
        Permission::create(['name' => 'sidebar.seccion.proyecto', 'description' => 'sidebar seccion proyecto'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleAdministrador);

            Permission::create(['name' => 'sidebar.nuevo.proyecto', 'description' => 'sidebar seccion proyecto - nuevo proyecto'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.lista.proyecto', 'description' => 'sidebar seccion proyecto - lista proyectos'])->syncRoles($roleUaci, $roleIng, $rolePresupuesto, $roleAdministrador);

            // botones
            Permission::create(['name' => 'boton.ver.proyecto', 'description' => 'sidebar seccion proyecto - boton ver proyecto'])->syncRoles($roleIng, $roleUaci, $roleAdministrador);
            Permission::create(['name' => 'boton.editar.proyecto', 'description' => 'sidebar seccion proyecto - boton editar proyecto'])->syncRoles($roleIng);
            Permission::create(['name' => 'boton.pdf.generar.presupuesto', 'description' => 'sidebar seccion proyecto - boton generar presupuesto para proyecto pdf'])->syncRoles($roleIng);

            Permission::create(['name' => 'boton.agregar.requisicion', 'description' => 'sidebar seccion proyecto - boton agregar nueva requisicion'])->syncRoles($roleAdministrador);
            Permission::create(['name' => 'boton.editar.requisicion', 'description' => 'sidebar seccion proyecto - boton editar requisicion'])->syncRoles($roleAdministrador);
            Permission::create(['name' => 'boton.borrar.requisicion', 'description' => 'sidebar seccion proyecto - boton borrar requisicion'])->syncRoles($roleAdministrador);

            Permission::create(['name' => 'boton.ver.presupuesto.modal', 'description' => 'sidebar seccion proyecto - boton ver presupuesto modal para aprobar'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.cotizar.requisicion', 'description' => 'sidebar seccion proyecto - boton cotizar requisicion'])->syncRoles($roleUaci);
            Permission::create(['name' => 'boton.ver.planilla', 'description' => 'sidebar seccion proyecto - boton ver planilla'])->syncRoles($roleUaci);
            Permission::create(['name' => 'boton.ver.presupuesto.administrador', 'description' => 'sidebar seccion proyecto - boton ver presupuesto por administrador, solo ver'])->syncRoles($roleAdministrador);
            Permission::create(['name' => 'boton.editar.imprevisto.administrador', 'description' => 'sidebar seccion proyecto - boton para editar el imprevisto por administrador'])->syncRoles($roleIng);
            Permission::create(['name' => 'boton.autorizar.denegar.movimiento.cuenta', 'description' => 'utilizado por jefe presupuesto para autorizar o denegar un movimiento de cuenta'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.agregar.movimiento.cuenta', 'description' => 'utilizado por administrador para hacer un movimiento de cuenta'])->syncRoles($roleAdministrador);
            Permission::create(['name' => 'boton.agregar.reforma.movimiento.cuenta', 'description' => 'utilizado para subir la reforma de un movimiento de cuenta'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.descargar.reforma.movimiento.cuenta', 'description' => 'utilizado para descargar la reforma de movimiento de cuenta'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.revision.movimiento.cuenta', 'description' => 'utilizado para autorizar o denegar un movimiento de cuenta'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.modal.estados.proyectos', 'description' => 'Ver modal con estado de proyecto'])->syncRoles($roleUaci);
            Permission::create(['name' => 'boton.modal.vista.partida.adicionales', 'description' => 'para opciones de proyecto, visible para presupuesto'])->syncRoles($rolePresupuesto, $roleUaci, $roleAdministrador);
            Permission::create(['name' => 'boton.autorizar.denegar.partida.adicional', 'description' => 'botón para autorizar crear x partidas adicionales'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.crear.vista.partida.adicionales', 'description' => 'redirecciona a vista contenedor de partida adicional'])->syncRoles($roleIng);
            Permission::create(['name' => 'boton.modal.crear.solicitud.partida.adicional', 'description' => 'abrir modal para crear solicitud partida adicional'])->syncRoles($roleIng);
            Permission::create(['name' => 'boton.borrar.contenedor.partida.adicional', 'description' => 'borra el contenedor con todas las partidas adicionales'])->syncRoles($roleIng);
            Permission::create(['name' => 'boton.crear.partida.adicional', 'description' => 'boton para abrir modal y crear una partida adicional'])->syncRoles($roleIng);
            Permission::create(['name' => 'boton.modal.porcentaje.obra.dicional', 'description' => 'boton para abrir modal y ver porcentaje de obra adicional'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.agregar.documento.partida.dicional', 'description' => 'boton para abrir modal y subir documento a partida adicional'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'boton.revisar.documento.partida.dicional', 'description' => 'boton para abrir modal y subir documento a partida adicional'])->syncRoles($rolePresupuesto);




            Permission::create(['name' => 'modulo.agregar.requisicion.proyecto', 'description' => 'sidebar seccion proyecto - modulo para agregar requisicion'])->syncRoles($roleAdministrador);

            Permission::create(['name' => 'modulo.agregar.bitacoras.proyecto', 'description' => 'sidebar seccion proyecto - modulo para ver bitacoras'])->syncRoles($roleIng);
            Permission::create(['name' => 'modulo.agregar.partida.proyecto', 'description' => 'sidebar seccion proyecto - modulo para agregar partidas'])->syncRoles($roleIng);



        // COTIZACIONES
        Permission::create(['name' => 'sidebar.seccion.cotizaciones', 'description' => 'sidebar seccion cotizaciones'])->syncRoles($roleUaci, $roleJefeUaci);

            Permission::create(['name' => 'sidebar.cotizacion.pendiente', 'description' => 'sidebar seccion cotizaciones - cotizacion pendiente'])->syncRoles($roleUaci, $roleJefeUaci);
            Permission::create(['name' => 'sidebar.cotizacion.procesada', 'description' => 'sidebar seccion cotizaciones - cotizacion procesada'])->syncRoles($roleUaci, $roleJefeUaci);
            Permission::create(['name' => 'sidebar.cotizacion.denegada', 'description' => 'sidebar seccion cotizaciones - cotizacion denegada'])->syncRoles($roleUaci, $roleJefeUaci);

            Permission::create(['name' => 'boton.cotizacion.generar.orden', 'description' => 'sidebar seccion cotizacion - boton para generar orden de compra'])->syncRoles($roleUaci);

            Permission::create(['name' => 'boton.autorizar.denegar.cotizacion', 'description' => 'boton para autorizar o denegar cotización'])->syncRoles($roleJefeUaci);

            // solo para mostrar texto, esperando aprobar o denegar cotizacion
            Permission::create(['name' => 'texto.esperando.aprobacion.cotizacion', 'description' => 'texto esperando aprobacion o denegacion de cotización'])->syncRoles($roleUaci);




        // ORDENES DE COMPRAS
        Permission::create(['name' => 'sidebar.seccion.ordenescompra', 'description' => 'sidebar seccion ordenes de compra'])->syncRoles($roleUaci, $roleJefeUaci);

        Permission::create(['name' => 'ordenes.compras.procesadas.index', 'description' => 'ver ordenes de compras procesadas'])->syncRoles($roleUaci, $roleJefeUaci);
        Permission::create(['name' => 'ordenes.compras.denegadas.index', 'description' => 'ver ordenes de compras denegadas'])->syncRoles($roleUaci, $roleJefeUaci);



        Permission::create(['name' => 'boton.anular.orden.compra', 'description' => 'boton para solo Anular una orden de compra'])->syncRoles($roleJefeUaci);


        // CONFIGURACIONES
        Permission::create(['name' => 'sidebar.seccion.configuraciones', 'description' => 'sidebar seccion configuraciones'])->syncRoles($roleUaci, $roleIng, $rolePresupuesto, $roleUaciUnidad);

            Permission::create(['name' => 'sidebar.fuente.financiamiento', 'description' => 'sidebar seccion fuente de financiamiento'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.area.gestion', 'description' => 'sidebar seccion area de gestion'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.fuente.recursos', 'description' => 'sidebar seccion fuente de recursos'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.linea.trabajo', 'description' => 'sidebar seccion linea de trabajo'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.catalogo.materiales', 'description' => 'sidebar seccion catalogo de materiales'])->syncRoles($roleUaci, $rolePresupuesto);
            Permission::create(['name' => 'sidebar.proveedores', 'description' => 'sidebar seccion proveedores'])->syncRoles($roleUaci, $roleUaciUnidad);
            Permission::create(['name' => 'sidebar.unidad.medida', 'description' => 'sidebar seccion unidad de medida'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.calificaciones', 'description' => 'sidebar seccion calificaciones'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.administradores', 'description' => 'sidebar seccion administradores'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.rubro', 'description' => 'sidebar seccion rubro'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.cuenta', 'description' => 'sidebar seccion cuenta'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.obj.especifico', 'description' => 'sidebar objeto específico'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.solicitud.material.uaci', 'description' => 'sidebar solicitud de materiales presupuesto ingenieria'])->syncRoles($roleUaci, $roleIng);
            Permission::create(['name' => 'sidebar.vista.catalogo.materiales.ing', 'description' => 'sidebar vista catalogo de materiales para ver lista por parte de ing'])->syncRoles($roleIng);
            Permission::create(['name' => 'sidebar.vista.bolson', 'description' => 'sidebar - vista para ir a bolson'])->syncRoles($rolePresupuesto);


            Permission::create(['name' => 'boton.solicitar.material.ing', 'description' => 'boton para solicitar nuevo material por parte de ing'])->syncRoles($roleIng);
            Permission::create(['name' => 'boton.agregar.material.solicitado.ing', 'description' => 'boton para agregar el material solicitado por ing'])->syncRoles($roleUaci);




            //nuevos
            Permission::create(['name' => 'sidebar.adescos', 'description' => 'sidebar seccion adescos'])->syncRoles($roleUaciUnidad);
            Permission::create(['name' => 'sidebar.equipos', 'description' => 'sidebar seccion equipos'])->syncRoles($roleUaciUnidad);
            Permission::create(['name' => 'sidebar.asociaciones', 'description' => 'sidebar seccion asociaciones'])->syncRoles($roleUaciUnidad);




        // REQUERIMIENTOS PROYECTO
        Permission::create(['name' => 'sidebar.seccion.requerimientos', 'description' => 'sidebar seccion requerimientos'])->syncRoles($roleUaci);
        Permission::create(['name' => 'sidebar.requerimientos.listar', 'description' => 'sidebar seccion requerimientos - listar requerimientos'])->syncRoles($roleUaci);

        // REQUERIMIENTOS UNIDAD
        Permission::create(['name' => 'sidebar.seccion.requerimientosunidad', 'description' => 'sidebar seccion requerimientos de unidad'])->syncRoles($roleUaciUnidad);
        Permission::create(['name' => 'sidebar.requerimientosunidad.listar', 'description' => 'sidebar seccion requerimientos - listar requerimientos de unidad'])->syncRoles($roleUaciUnidad);


        Permission::create(['name' => 'boton.aprobar.presupuesto', 'description' => 'boton para aprobar presupuesto, mostrado en modal'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'boton.dinero.presupuesto', 'description' => 'boton para mostrar MODAL cuanto dinero le queda a un proyecto del presupuesto'])->syncRoles($roleAdministrador, $roleUaci, $roleIng, $rolePresupuesto);
        Permission::create(['name' => 'boton.movimiento.cuenta.proyecto', 'description' => 'botón para redirigir a movimiento cuenta proyecto'])->syncRoles($roleAdministrador, $roleUaci, $rolePresupuesto);





        // ************** PERMISOS PARA PRESUPUESTO DE UNIDADES ***************

        // CONFIGURACIÓN PARA PRESUPUESTO DE UNIDADES
        Permission::create(['name' => 'sidebar.seccion.configuraciones.presupuesto.unidades', 'description' => 'sidebar sección configuración para presupuesto de unidades'])->syncRoles($rolePresupuesto, $roleUaciUnidad);

        Permission::create(['name' => 'p.anio.presupuesto.unidades', 'description' => 'crear años para presupuesto de unidades'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'p.departamento.presupuesto.unidades', 'description' => 'crear departamentos para presupuesto de unidades'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'p.unidadmedida.presupuesto.unidades', 'description' => 'crear unidad de medida para presupuesto de unidades'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'p.materiales.presupuesto.unidades', 'description' => 'crear materiales para presupuesto de unidades'])->syncRoles($rolePresupuesto, $roleUaciUnidad);

        // REPORTES PARA PRESUPUESTO DE UNIDADES

        Permission::create(['name' => 'sidebar.revision.presupuesto.unidades', 'description' => 'sidebar sección revisión para presupuesto de unidades'])->syncRoles($rolePresupuesto);

        Permission::create(['name' => 'p.revision.presupuesto.unidades', 'description' => 'revision de presupuesto de cada unidad'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'p.generar.reportes.presupuesto.unidades', 'description' => 'vista para generar reportes general'])->syncRoles($rolePresupuesto);

        // CREAR PRESUPUESTO DE UNA UNIDAD
        Permission::create(['name' => 'sidebar.p.presupuesto.crear', 'description' => 'sidebar para crear presupuesto de unidades'])->syncRoles($roleUnidad);
        Permission::create(['name' => 'p.crear.presupuesto.unidad', 'description' => 'para crear presupuesto de unidades'])->syncRoles($roleUnidad);
        Permission::create(['name' => 'p.editar.presupuesto.unidad', 'description' => 'para editar presupuesto de unidades'])->syncRoles($roleUnidad);

        Permission::create(['name' => 'p.cuentas.unidades', 'description' => 'para ver todas las cuentas unidades y aquí se crean por año'])->syncRoles($rolePresupuesto);

        Permission::create(['name' => 'sidebar.p.requerimientos.unidades', 'description' => 'para ver los años que puede pedir requerimientos'])->syncRoles($roleUnidad);




        Permission::create(['name' => 'boton.cotizar.requisicion.unidad', 'description' => 'boton cotizar requisicion para unidad'])->syncRoles($roleUnidad);
        Permission::create(['name' => 'boton.editar.requisicion.unidad', 'description' => 'boton editar requisicion para unidad'])->syncRoles($roleUnidad);
        Permission::create(['name' => 'boton.borrar.requisicion.unidad', 'description' => 'boton borrar requisicion para unidad'])->syncRoles($roleUnidad);
        Permission::create(['name' => 'boton.agregar.movimiento.cuenta.unidad', 'description' => 'utilizado por jefe unidad realizar un movimiento de cuenta unidad'])->syncRoles($roleUnidad);
        Permission::create(['name' => 'p.sidebar.historico.movimiento.pendientes.cuentas.unidades', 'description' => 'para ver históricos de movimientos pendientes de cuenta para unidades'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'p.sidebar.historico.movimiento.aprobados.cuentas.unidades', 'description' => 'para ver históricos de movimientos aprobados de cuenta para unidades'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'boton.revision.movimiento.cuenta.unidad', 'description' => 'boton para revisar el nuevo movimiento de cuenta para unidad'])->syncRoles($rolePresupuesto);

        Permission::create(['name' => 'boton.reforma.movimiento.cuenta.unidad', 'description' => 'boton para ver reforma o subir a movimiento cuenta unidad'])->syncRoles($rolePresupuesto);

        Permission::create(['name' => 'sidebar.seccion.cotizaciones.unidad', 'description' => 'sidebar ver cotizaciones para unidades'])->syncRoles($roleUaciUnidad, $roleJefeUaci);
        Permission::create(['name' => 'sidebar.cotizacion.unidad.pendiente', 'description' => 'ver cotizacione pendientes para unidades'])->syncRoles($roleUaciUnidad, $roleJefeUaci);

        Permission::create(['name' => 'boton.autorizar.denegar.cotizacion.unidad', 'description' => 'boton para autorizar o denegar cotización unidad'])->syncRoles($roleJefeUaci);
        Permission::create(['name' => 'texto.esperando.aprobacion.cotizacion.unidad', 'description' => 'texto esperando aprobacion o denegacion de cotización unidad'])->syncRoles($roleUaciUnidad);

        Permission::create(['name' => 'sidebar.cotizacion.unidad.procesada', 'description' => 'sidebar ver torizaciones autorizadas para unidades'])->syncRoles($roleUaciUnidad, $roleJefeUaci);
        Permission::create(['name' => 'sidebar.cotizacion.unidad.denegadas', 'description' => 'sidebar ver busqueda de año para cotizaciones denegadas'])->syncRoles($roleUaciUnidad, $roleJefeUaci);
        Permission::create(['name' => 'boton.cotizacion.unidad.generar.orden', 'description' => 'boton para generar orden de compra para cotizaciones de unidad'])->syncRoles($roleUaciUnidad);

        Permission::create(['name' => 'sidebar.seccion.ordenescompra.unidades', 'description' => 'sidebar para ver ordenes de compra para unidades'])->syncRoles($roleUaciUnidad, $roleJefeUaci);
        Permission::create(['name' => 'ordenes.compras.procesadas.unidades.index', 'description' => 'ver ordenes de compras aprobadas para unidades'])->syncRoles($roleUaciUnidad, $roleJefeUaci);
        Permission::create(['name' => 'ordenes.compras.denegadas.unidades.index', 'description' => 'ver ordenes de compras denegadas para unidades'])->syncRoles($roleJefeUaci);

        Permission::create(['name' => 'boton.anular.orden.compra.unidad', 'description' => 'boton para anular orden de compra ya aprobada de unidades'])->syncRoles($roleJefeUaci);

        Permission::create(['name' => 'p.sidebar.solicitud.materiales.cuentas.unidades', 'description' => 'sidebar para que Presupuesto revise solicitud de materiales de unidad'])->syncRoles($rolePresupuesto);
        Permission::create(['name' => 'p.sidebar.solicitud.aprobadas.materiales.cuentas.unidades', 'description' => 'sidebar para que Presupuesto revise solicitud de materiales de unidad Aprobados'])->syncRoles($rolePresupuesto);


        // DESCARGOS DIRECTOS
        Permission::create(['name' => 'sidebar.descargos.directos', 'description' => 'ver sidebar de descargos directos'])->syncRoles($roleUaci);
        Permission::create(['name' => 'sidebar.descargos.directos.revision', 'description' => 'ver index de solicitud de descargos directos'])->syncRoles($roleUaci);



    }
}
