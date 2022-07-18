<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\Roles\PermisoController;
use App\Http\Controllers\Backend\Perfil\PerfilController;
use App\Http\Controllers\Backend\Proyecto\ProyectoController;
use App\Http\Controllers\Backend\Configuraciones\CodigoEspecifController;
use App\Http\Controllers\Backend\Configuraciones\UnidadMedidaController;
use App\Http\Controllers\Backend\Configuraciones\ClasificacionesController;
use App\Http\Controllers\Backend\Configuraciones\MaterialesController;
use App\Http\Controllers\Backend\Configuraciones\LineaTrabajoController;
use App\Http\Controllers\Backend\Configuraciones\FuenteFinanciamientoController;
use App\Http\Controllers\Backend\Configuraciones\FuenteRecursosController;
use App\Http\Controllers\Backend\Configuraciones\AreaGestionController;
use App\Http\Controllers\Backend\Configuraciones\ProveedoresController;
use App\Http\Controllers\Backend\Configuraciones\AdministradoresController;
use App\Http\Controllers\Backend\Proyecto\CotizacionController;
use App\Http\Controllers\Backend\Orden\OrdenController;
use App\Http\Controllers\Backend\Inicio\InicioController;
use App\Http\Controllers\Backend\Bolson\BolsonController;
use App\Http\Controllers\Backend\Cuenta\CuentaProyectoController;
use App\Http\Controllers\Backend\Recursos\RecursosController;
use App\Http\Controllers\Backend\Pdf\ControlPdfController;

Route::get('/', [LoginController::class,'index'])->name('login');

Route::post('admin/login', [LoginController::class, 'login']);
Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---
Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

// --- ROLES ---
Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

// --- PERMISOS ---
Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

// --- PERFIL ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

// --- SIN PERMISOS VISTA 403 ---
Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');

// --- VISTA ESTADISTICAS ---
Route::get('/admin/inicio/index', [InicioController::class,'index'])->name('admin.estadisticas.index');


// --- PROYECTO ---
Route::get('/admin/proyecto/nuevo/index', [ProyectoController::class,'index'])->name('admin.nuevo.proyecto.index');
Route::post('/admin/proyecto/nuevo', [ProyectoController::class, 'nuevoProyecto']);

// --- LISTA DE PROYECTOS ---
Route::get('/admin/proyecto/lista/index', [ProyectoController::class,'indexProyectoLista'])->name('admin.lista.proyectos.index');
Route::get('/admin/proyecto/lista/tabla/index', [ProyectoController::class,'tablaProyectoLista']);
Route::post('/admin/proyecto/lista/informacion', [ProyectoController::class, 'informacionProyecto']);
Route::post('/admin/proyecto/lista/editar', [ProyectoController::class, 'editarProyecto']);
Route::get('/admin/ver/presupuesto/uaci/{id}', [ProyectoController::class,'informacionPresupuestoParaAprobacion']);
Route::post('/admin/proyecto/aprobar/presupuesto', [ProyectoController::class, 'aprobarPresupuesto']);





// --- VISTA DE PROYECTO ---
Route::get('/admin/proyecto/vista/index/{id}', [ProyectoController::class,'indexProyectoVista']);
Route::get('/admin/proyecto/vista/bitacora/{id}', [ProyectoController::class,'tablaProyectoListaBitacora']);
Route::post('/admin/proyecto/vista/bitacora/registrar', [ProyectoController::class, 'registrarBitacora']);
Route::post('/admin/proyecto/vista/bitacora/borrar', [ProyectoController::class, 'borrarBitacora']);
Route::post('/admin/proyecto/vista/bitacora/informacion', [ProyectoController::class, 'informacionBitacora']);
Route::post('/admin/proyecto/vista/bitacora/editar', [ProyectoController::class, 'editarBitacora']);
Route::get('/admin/proyecto/vista/bitacora-detalle/{id}', [ProyectoController::class,'vistaBitacoraDetalle']);
Route::get('/admin/proyecto/vista/tabla/bitacora-detalle/{id}', [ProyectoController::class,'tablaBitacoraDetalle']);
Route::get('/admin/proyecto/vista/bitacora-detalle-doc/{file}' , [ProyectoController::class, 'descargarBitacoraDoc']);
Route::post('/admin/proyecto/vista/bitacora-detalle/borrar' , [ProyectoController::class, 'borrarBitacoraDetalle']);
Route::post('/admin/proyecto/vista/bitacora-detalle/nuevo' , [ProyectoController::class, 'nuevoBitacoraDetalle']);

Route::get('/admin/proyecto/lista/index', [ProyectoController::class,'indexProyectoLista'])->name('admin.lista.proyectos.index');
Route::get('/admin/proyecto/lista/tabla/index', [ProyectoController::class,'tablaProyectoLista']);



// --- VISTA REQUISICION ---
Route::get('/admin/proyecto/vista/requisicion/{id}', [ProyectoController::class,'tablaProyectoListaRequisicion']);
Route::post('/admin/proyecto/vista/requisicion/nuevo', [ProyectoController::class, 'nuevoRequisicion']);
Route::post('/admin/proyecto/vista/requisicion/informacion', [ProyectoController::class, 'informacionRequisicion']);
Route::post('/admin/proyecto/vista/requisicion/editar', [ProyectoController::class, 'editarRequisicion']);


//** INGENIERIA */
// --- Presupuesto de Proyecto ---
Route::get('/admin/proyecto/vista/presupuesto/{id}', [ProyectoController::class,'tablaProyectoListaPresupuesto']);
Route::post('/admin/proyecto/agregar/presupuesto',  [ProyectoController::class,'agregarPresupuesto']);
Route::post('/admin/proyecto/vista/presupuesto/informacion', [ProyectoController::class, 'informacionPresupuesto']);
Route::post('/admin/proyecto/vista/presupuesto/editar', [ProyectoController::class, 'editarPresupuesto']);
Route::post('/admin/proyecto/vista/presupuesto/borrar', [ProyectoController::class, 'borrarPresupuesto']);

//Route::get('/admin/generar/pdf/presupuesto/{id}', [ProyectoController::class,'generarPrespuestoPdf']);

Route::get('/admin/generar/pdf/presupuesto/{id}', [ControlPdfController::class,'generarPrespuestoPdf']);

// verifica si partida mano de obra existe
Route::post('/admin/proyecto/partida/manoobra/existe', [ProyectoController::class, 'verificarPartidaManoObra']);


// --- CUENTA BOLSON ---
Route::get('/admin/bolson/cuenta/index', [BolsonController::class,'indexCuenta'])->name('admin.bolson.cuenta.index');
Route::get('/admin/bolson/cuenta/indextabla', [BolsonController::class,'tablaCuenta']);
Route::post('/admin/bolson/buscar/cuenta', [BolsonController::class, 'buscarNombreCuenta']);
Route::post('/adm/proyecto/agregar/presupuestoin/bolson/buscar/cuenta-editar', [BolsonController::class, 'buscarNombreCuentaEditar']);
Route::post('/admin/bolson/nuevo',  [BolsonController::class,'nuevoRegistro']);
Route::post('/admin/bolson/informacion',  [BolsonController::class,'informacionBolson']);
Route::post('/admin/bolson/editar',  [BolsonController::class,'editarRegistro']);

// --- MOVIMIENTO BOLSON ---
Route::get('/admin/bolson/movimiento/index', [BolsonController::class,'indexMovimiento'])->name('admin.movimiento.bolson.index');
Route::get('/admin/bolson/movimiento/tabla', [BolsonController::class,'tablaMovimiento']);
Route::post('/admin/bolson/movimiento/nuevo',  [BolsonController::class,'nuevoMovimiento']);
Route::post('/admin/bolson/movimiento/informacion',  [BolsonController::class,'informacionMovimiento']);
Route::post('/admin/bolson/movimiento/editar',  [BolsonController::class,'editarMovimiento']);


// --- CUENTA PROYECTO ----
Route::get('/admin/cuentaproy/cuenta/{id}', [CuentaProyectoController::class,'indexCuenta']);
Route::get('/admin/cuentaproy/cuenta/indextabla/{id}', [CuentaProyectoController::class,'tablaCuenta']);
//Route::post('/admin/cuentaproy/nuevo',  [CuentaProyectoController::class,'nuevaCuentaProy']);
//Route::post('/admin/cuentaproy/informacion',  [CuentaProyectoController::class,'informacionCuentaProy']);
//Route::post('/admin/cuentaproy/editar',  [CuentaProyectoController::class,'editarCuentaProy']);

// --- PLANILLA ---
Route::get('/admin/planilla/lista/{id}', [CuentaProyectoController::class,'indexPlanilla']);
Route::get('/admin/planilla/tabla/lista/{id}', [CuentaProyectoController::class,'tablaPlanilla']);
Route::post('/admin/planilla/nuevo',  [CuentaProyectoController::class,'nuevaPlanilla']);
Route::post('/admin/planilla/informacion',  [CuentaProyectoController::class,'informacionPlanilla']);
Route::post('/admin/planilla/editar',  [CuentaProyectoController::class,'editarPlanilla']);





// --- MOVIMIENTO CUENTA PROYECTO ----
Route::get('/admin/movicuentaproy/indexmovicuentaproy', [CuentaProyectoController::class,'indexMoviCuentaProy'])->name('admin.movi.cuenta.proy.index');
Route::get('/admin/movicuentaproy/tablamovicuentaproy', [CuentaProyectoController::class,'indexTablaMoviCuentaProy']);
Route::post('/admin/movicuentaproy/buscador',  [CuentaProyectoController::class,'buscadorCuentaProy']);
Route::post('/admin/movicuentaproy/nuevo',  [CuentaProyectoController::class,'nuevaMoviCuentaProy']);
Route::get('/admin/movicuentaproy/documento/{id}',  [CuentaProyectoController::class,'descargarReforma']);
Route::post('/admin/movicuentaproy/informacion',  [CuentaProyectoController::class,'informacionMoviCuentaProy']);
Route::post('/admin/movicuentaproy/editar',  [CuentaProyectoController::class,'editarMoviCuentaProy']);



// --- VISTA GENERAR COTIZACION ---
Route::get('/admin/proyecto/vista/cotizacion/{id}', [ProyectoController::class,'indexCotizacion']);
Route::post('/admin/proyecto/lista/cotizaciones',  [ProyectoController::class,'obtenerListaCotizaciones']);
Route::post('/admin/proyecto/buscar/material',  [ProyectoController::class,'buscadorMaterial']);

// utilizado para un usuario tipo ingenieria
Route::post('/admin/proyecto/buscar/material-presupuesto',  [ProyectoController::class,'buscadorMaterialPresupuesto']);
Route::post('/admin/proyecto/buscar/material-presupuesto-editar',  [ProyectoController::class,'buscadorMaterialPresupuestoEditar']);

Route::post('/admin/proyecto/cotizacion/nuevo',  [ProyectoController::class,'nuevaCotizacion']);

// --- VISTA COTIZACIONES PENDIENTES ---
Route::get('/admin/cotizacion/pendiente/index', [CotizacionController::class,'indexPendiente'])->name('cotizaciones.pendientes.index');
Route::get('/admin/cotizacion/pendiente/tabla-index', [CotizacionController::class,'indexPendienteTabla']);
Route::get('/admin/cotizacion/individual/index/{id}', [CotizacionController::class,'indexCotizacion']);
Route::post('/admin/cotizacion/pendiente/actualizar',  [CotizacionController::class,'actualizarCotizacion']);
Route::post('/admin/cotizacion/borrar',  [CotizacionController::class,'borrarCotizacion']);
Route::post('/admin/cotizacion/autorizar',  [CotizacionController::class,'autorizarCotizacion']);
Route::post('/admin/cotizacion/denegar',  [CotizacionController::class,'denegarCotizacion']);

// --- VISTA COTIZACIONES AUTORIZADAS ---
Route::get('/admin/cotizacion/autorizadas/index', [CotizacionController::class,'indexAutorizadas'])->name('cotizaciones.autorizadas.index');
Route::get('/admin/cotizacion/autorizadas/tabla-index', [CotizacionController::class,'indexAutorizadasTabla']);

// --- VISTA COTIZACIONES DENEGADAS ---
Route::get('/admin/cotizacion/denegadas/index', [CotizacionController::class,'indexDenegadas'])->name('cotizaciones.denegadas.index');
Route::get('/admin/cotizacion/denegadas/tabla-index', [CotizacionController::class,'indexDenegadasTabla']);

// --- ORDENES ---
Route::post('/admin/ordenes/generar/nuevo',  [OrdenController::class,'generarOrden']);
Route::get('/admin/documento/pdf/orden/{id}', [OrdenController::class,'vistaPdfOrden']);

// --- ORDENES DE COMPRAS ---
Route::get('/admin/ordenes/compras/index', [OrdenController::class,'indexOrdenesCompras'])->name('ordenes.compras.index');
Route::get('/admin/ordenes/compras/tabla-index', [OrdenController::class,'tablaOrdenesCompras']);
Route::post('/admin/ordenes/anular/compra',  [OrdenController::class,'anularCompra']);
Route::post('/admin/ordenes/generar/acta',  [OrdenController::class,'generarActa']);
Route::get('/admin/ordenes/acta/reporte/{id}', [OrdenController::class,'reporteActaGenerada']);



// --- UNIDAD MEDIDA ---
Route::get('/admin/unidadmedida/index', [UnidadMedidaController::class,'index'])->name('admin.unidadmedida.index');
Route::get('/admin/unidadmedida/tabla/index', [UnidadMedidaController::class,'tabla']);
Route::post('/admin/unidadmedida/nuevo', [UnidadMedidaController::class, 'nuevaUnidadMedida']);
Route::post('/admin/unidadmedida/informacion', [UnidadMedidaController::class, 'informacionUnidadMedida']);
Route::post('/admin/unidadmedida/editar', [UnidadMedidaController::class, 'editarUnidadMedida']);

// --- CLASIFICACIONES ---
Route::get('/admin/clasificaciones/index', [ClasificacionesController::class,'index'])->name('admin.clasificaciones.index');
Route::get('/admin/clasificaciones/tabla/index', [ClasificacionesController::class,'tabla']);
Route::post('/admin/clasificaciones/nuevo', [ClasificacionesController::class, 'nuevaClasificacion']);
Route::post('/admin/clasificaciones/informacion', [ClasificacionesController::class, 'informacionClasificacion']);
Route::post('/admin/clasificaciones/editar', [ClasificacionesController::class, 'editarClasificacion']);

// --- CATALOGO DE MATERIALES ---
Route::get('/admin/catalogo/materiales/index', [MaterialesController::class,'index'])->name('admin.catalogo.materiales.index');
Route::get('/admin/catalogo/materiales/tabla/index', [MaterialesController::class,'tabla']);
Route::post('/admin/catalogo/materiales/nuevo', [MaterialesController::class, 'nuevoMaterial']);
Route::post('/admin/catalogo/materiales/informacion', [MaterialesController::class, 'informacion']);
Route::post('/admin/catalogo/materiales/editar', [MaterialesController::class, 'editarMaterial']);

// --- LÍNEA DE TRABAJO ---
Route::get('/admin/linea/trabajo/index', [LineaTrabajoController::class,'index'])->name('admin.linea.de.trabajo.index');
Route::get('/admin/linea/trabajo/tabla/index', [LineaTrabajoController::class,'tabla']);
Route::post('/admin/linea/trabajo/nuevo', [LineaTrabajoController::class, 'nuevaLinea']);
Route::post('/admin/linea/trabajo/informacion', [LineaTrabajoController::class, 'informacionLinea']);
Route::post('/admin/linea/trabajo/editar', [LineaTrabajoController::class, 'editarLinea']);

// --- FUENTE DE FINANCIAMIENTO ---
Route::get('/admin/fuentef/index', [FuenteFinanciamientoController::class,'index'])->name('admin.fuente.financiamiento.index');
Route::get('/admin/fuentef/tabla/index', [FuenteFinanciamientoController::class,'tabla']);
Route::post('/admin/fuentef/nuevo', [FuenteFinanciamientoController::class, 'nuevaFuente']);
Route::post('/admin/fuentef/informacion', [FuenteFinanciamientoController::class, 'informacionFuente']);
Route::post('/admin/fuentef/editar', [FuenteFinanciamientoController::class, 'editarFuente']);

// --- FUENTE DE RECURSOS ---
Route::get('/admin/fuenter/index', [FuenteRecursosController::class,'index'])->name('admin.fuente.recurso.index');
Route::get('/admin/fuenter/tabla/index', [FuenteRecursosController::class,'tabla']);
Route::post('/admin/fuenter/nuevo', [FuenteRecursosController::class, 'nuevaFuente']);
Route::post('/admin/fuenter/informacion', [FuenteRecursosController::class, 'informacionFuente']);
Route::post('/admin/fuenter/editar', [FuenteRecursosController::class, 'editarFuente']);

// --- ÁREA DE GESTIÓN ---
Route::get('/admin/areagestion/index', [AreaGestionController::class,'index'])->name('admin.area.gestion.index');
Route::get('/admin/areagestion/tabla/index', [AreaGestionController::class,'tabla']);
Route::post('/admin/areagestion/nuevo', [AreaGestionController::class, 'nuevaAreaGestion']);
Route::post('/admin/areagestion/informacion', [AreaGestionController::class, 'informacionArea']);
Route::post('/admin/areagestion/editar', [AreaGestionController::class, 'editarArea']);

// --- PROVEEDORES ---
Route::get('/admin/proveedores/index', [ProveedoresController::class,'index'])->name('admin.proveedores.index');
Route::get('/admin/proveedores/tabla/index', [ProveedoresController::class,'tabla']);
Route::post('/admin/proveedores/nuevo', [ProveedoresController::class, 'nuevoProveedor']);
Route::post('/admin/proveedores/informacion', [ProveedoresController::class, 'informacionProveedor']);
Route::post('/admin/proveedores/editar', [ProveedoresController::class, 'editarProveedor']);

// --- ADMINISTRADORES DE PROYECTO ---
Route::get('/admin/administradores/index', [AdministradoresController::class,'index'])->name('admin.administradores.index');
Route::get('/admin/administradores/tabla/index', [AdministradoresController::class,'tabla']);
Route::post('/admin/administradores/nuevo', [AdministradoresController::class, 'nuevoAdministrador']);
Route::post('/admin/administradores/informacion', [AdministradoresController::class, 'informacionAdministrador']);
Route::post('/admin/administradores/editar', [AdministradoresController::class, 'editarAdministrador']);


// --- RECURSOS HUMANOS ---
Route::get('/admin/recursos/index', [RecursosController::class,'index'])->name('admin.recursos.index');

// --- RUBRO ---
Route::get('/admin/rubro/index', [ProveedoresController::class,'indexRubro'])->name('admin.rubro.index');
Route::get('/admin/rubro/tabla', [ProveedoresController::class,'tablaRubro']);
Route::post('/admin/rubro/nuevo', [ProveedoresController::class, 'nuevaRubro']);
Route::post('/admin/rubro/informacion', [ProveedoresController::class, 'informacionRubro']);
Route::post('/admin/rubro/editar', [ProveedoresController::class, 'editarRubro']);

// --- CUENTA ---
Route::get('/admin/cuenta/index', [CodigoEspecifController::class,'indexCuenta'])->name('admin.cuenta.index');
Route::get('/admin/cuenta/tabla', [CodigoEspecifController::class,'tablaCuenta']);
Route::post('/admin/cuenta/nuevo', [CodigoEspecifController::class, 'nuevaCuenta']);
Route::post('/admin/cuenta/informacion', [CodigoEspecifController::class, 'informacionCuenta']);
Route::post('/admin/cuenta/editar', [CodigoEspecifController::class, 'editarCuenta']);

// --- OBJETO ESPECIFICO ---
Route::get('/admin/objespecifico/index', [CodigoEspecifController::class,'indexObjEspecifico'])->name('admin.obj.especifico.index');
Route::get('/admin/objespecifico/tabla', [CodigoEspecifController::class,'tablaObjEspecifico']);
Route::post('/admin/objespecifico/nuevo', [CodigoEspecifController::class, 'nuevaObjEspecifico']);
Route::post('/admin/objespecifico/informacion', [CodigoEspecifController::class, 'informacionObjEspecifico']);
Route::post('/admin/objespecifico/editar', [CodigoEspecifController::class, 'editarObjEspecifico']);

// --- GENERAR PRESUPUESTO PDF
Route::get('/admin/generar/presupuesto/{id}', [CodigoEspecifController::class,'generarPrespuesto']);

