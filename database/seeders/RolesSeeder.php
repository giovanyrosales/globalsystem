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

        // Recursos Humanos
        //$roleRRHH = Role::create(['name' => 'rrhh']);

        // ROLES Y PERMISOS
        Permission::create(['name' => 'sidebar.roles.y.permisos', 'description' => 'sidebar seccion roles y permisos'])->syncRoles($roleAdmin);

        // ESTADISTICAS
        Permission::create(['name' => 'sidebar.estadisticas', 'description' => 'sidebar seccion estadisticas'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleJefeUaci, $roleAdministrador, $roleSecretaria);

        // SECCION PROYECTO
        Permission::create(['name' => 'sidebar.seccion.proyecto', 'description' => 'sidebar seccion proyecto'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleAdministrador);

            Permission::create(['name' => 'sidebar.nuevo.proyecto', 'description' => 'sidebar seccion proyecto - nuevo proyecto'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.lista.proyecto', 'description' => 'sidebar seccion proyecto - lista proyectos'])->syncRoles($roleUaci, $roleIng, $rolePresupuesto, $roleAdministrador);

            // botones
            Permission::create(['name' => 'boton.ver.proyecto', 'description' => 'sidebar seccion proyecto - boton ver proyecto'])->syncRoles($roleIng, $roleUaci, $roleAdministrador);
            Permission::create(['name' => 'boton.editar.proyecto', 'description' => 'sidebar seccion proyecto - boton editar proyecto'])->syncRoles($rolePresupuesto, $roleUaci, $roleIng);
            Permission::create(['name' => 'boton.pdf.generar.presupuesto', 'description' => 'sidebar seccion proyecto - boton generar presupuesto para proyecto pdf'])->syncRoles($roleIng);

            Permission::create(['name' => 'boton.agregar.requisicion', 'description' => 'sidebar seccion proyecto - boton agregar nueva requisicion'])->syncRoles($roleAdministrador);
            Permission::create(['name' => 'boton.editar.requisicion', 'description' => 'sidebar seccion proyecto - boton editar requisicion'])->syncRoles($roleAdministrador);
            Permission::create(['name' => 'boton.ver.presupuesto', 'description' => 'sidebar seccion proyecto - boton ver presupuesto'])->syncRoles($rolePresupuesto, $roleAdministrador, $roleJefeUaci, $roleSecretaria);
            Permission::create(['name' => 'boton.cotizar.requisicion', 'description' => 'sidebar seccion proyecto - boton cotizar requisicion'])->syncRoles($roleUaci);
            Permission::create(['name' => 'boton.ver.planilla', 'description' => 'sidebar seccion proyecto - boton ver planilla'])->syncRoles($roleUaci);



            Permission::create(['name' => 'modulo.agregar.requisicion.proyecto', 'description' => 'sidebar seccion proyecto - modulo para agregar requisicion'])->syncRoles($roleAdministrador);

            Permission::create(['name' => 'modulo.agregar.bitacoras.proyecto', 'description' => 'sidebar seccion proyecto - modulo para ver bitacoras'])->syncRoles($roleIng);
            Permission::create(['name' => 'modulo.agregar.partida.proyecto', 'description' => 'sidebar seccion proyecto - modulo para agregar partidas'])->syncRoles($roleIng);


        //Permission::create(['name' => 'sidebar.cuenta.proyecto', 'description' => 'sidebar seccion proyecto - cuenta de proyecto'])->syncRoles($roleUaci, $rolePresupuesto);
        Permission::create(['name' => 'sidebar.movimiento.cuenta', 'description' => 'sidebar seccion proyecto - movimiento de cuenta'])->syncRoles($roleUaci, $rolePresupuesto);

        // COTIZACIONES
        Permission::create(['name' => 'sidebar.seccion.cotizaciones', 'description' => 'sidebar seccion cotizaciones'])->syncRoles($roleUaci, $roleJefeUaci);

            Permission::create(['name' => 'sidebar.cotizacion.pendiente', 'description' => 'sidebar seccion cotizaciones - cotizacion pendiente'])->syncRoles($roleUaci, $roleJefeUaci);
            Permission::create(['name' => 'sidebar.cotizacion.procesada', 'description' => 'sidebar seccion cotizaciones - cotizacion procesada'])->syncRoles($roleUaci, $roleJefeUaci);
            Permission::create(['name' => 'sidebar.cotizacion.denegada', 'description' => 'sidebar seccion cotizaciones - cotizacion denegada'])->syncRoles($roleUaci, $roleJefeUaci);

        // ORDENES DE COMPRAS
        Permission::create(['name' => 'sidebar.seccion.ordenescompra', 'description' => 'sidebar seccion ordenes de compra'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleJefeUaci);

        // SECCION CUENTA BOLSON
        Permission::create(['name' => 'sidebar.seccion.cuentabolson', 'description' => 'sidebar seccion cuenta bolson'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleJefeUaci);

            Permission::create(['name' => 'sidebar.cuentabolson.cuenta', 'description' => 'sidebar seccion cuenta bolson - cuenta'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleJefeUaci);
            Permission::create(['name' => 'sidebar.cuentabolson.movimiento', 'description' => 'sidebar seccion cuenta bolson - movimiento'])->syncRoles($roleUaci, $rolePresupuesto, $roleIng, $roleJefeUaci);

        // CONFIGURACIONES
        Permission::create(['name' => 'sidebar.seccion.configuraciones', 'description' => 'sidebar seccion configuraciones'])->syncRoles($roleUaci, $rolePresupuesto);

            Permission::create(['name' => 'sidebar.fuente.financiamiento', 'description' => 'sidebar seccion fuente de financiamiento'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.area.gestion', 'description' => 'sidebar seccion area de gestion'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.fuente.recursos', 'description' => 'sidebar seccion fuente de recursos'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.linea.trabajo', 'description' => 'sidebar seccion linea de trabajo'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.catalogo.materiales', 'description' => 'sidebar seccion catalogo de materiales'])->syncRoles($roleUaci, $rolePresupuesto);
            Permission::create(['name' => 'sidebar.proveedores', 'description' => 'sidebar seccion proveedores'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.unidad.medida', 'description' => 'sidebar seccion unidad de medida'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.calificaciones', 'description' => 'sidebar seccion calificaciones'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.administradores', 'description' => 'sidebar seccion administradores'])->syncRoles($roleUaci);
            Permission::create(['name' => 'sidebar.rubro', 'description' => 'sidebar seccion rubro'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.cuenta', 'description' => 'sidebar seccion cuenta'])->syncRoles($rolePresupuesto);
            Permission::create(['name' => 'sidebar.obj.especifico', 'description' => 'sidebar objeto específico'])->syncRoles($rolePresupuesto);



    }
}
