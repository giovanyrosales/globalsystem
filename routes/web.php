<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\Roles\PermisoController;
use App\Http\Controllers\Backend\Perfil\PerfilController;
use App\Http\Controllers\Backend\Proyectos\Proyecto\ProyectoController;
use App\Http\Controllers\Backend\Configuraciones\CodigoEspecifController;
use App\Http\Controllers\Backend\Configuraciones\UnidadMedidaController;
use App\Http\Controllers\Backend\Configuraciones\ClasificacionesController;
use App\Http\Controllers\Backend\Configuraciones\MaterialesController;
use App\Http\Controllers\Backend\Configuraciones\LineaTrabajoController;
use App\Http\Controllers\Backend\Configuraciones\FuenteFinanciamientoController;
use App\Http\Controllers\Backend\Configuraciones\FuenteRecursosController;
use App\Http\Controllers\Backend\Configuraciones\AreaGestionController;
use App\Http\Controllers\Backend\Configuraciones\ProveedoresController;
use App\Http\Controllers\Backend\Configuraciones\AdescosController;
use App\Http\Controllers\Backend\Configuraciones\EquiposController;
use App\Http\Controllers\Backend\Configuraciones\AsociacionesController;
use App\Http\Controllers\Backend\Configuraciones\AdministradoresController;
use App\Http\Controllers\Backend\Proyectos\Cotizacion\CotizacionController;
use App\Http\Controllers\Backend\Orden\OrdenController;
use App\Http\Controllers\Backend\Bolson\BolsonController;
use App\Http\Controllers\Backend\Cuenta\CuentaProyectoController;
use App\Http\Controllers\Backend\Recursos\RecursosController;
use App\Http\Controllers\Backend\Pdf\ControlPdfController;
use App\Http\Controllers\Backend\PresupuestoUnidad\Reportes\ReportesUnidadController;

use App\Http\Controllers\Backend\Configuracion\Estadisticas\EstadisticasController;

use App\Http\Controllers\Backend\PresupuestoUnidad\Anio\AnioPresupuestoUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\Departamento\DepartamentoPresupuestoUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\UnidadMedida\UnidadMedidaPresupuestoUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\Materiales\MaterialesPresupuestoUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\Presupuesto\ConfiguracionPresupuestoUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\Presupuesto\ReportesPresupuestoUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos\RequerimientosUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\CuentasUnidad\CuentaUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\MovimientoUnidad\MovimientosUnidadControlles;
use App\Http\Controllers\Backend\PresupuestoUnidad\Cotizaciones\CotizacionesUnidadController;
use App\Http\Controllers\Backend\PresupuestoUnidad\OrdenCompra\OrdenCompraUnidadController;
use App\Http\Controllers\Backend\DescargosDirectos\DescargosDirectosController;
use App\Http\Controllers\Backend\Configuracion\Consolidador\ConsolidadorController;
use App\Http\Controllers\Backend\Configuracion\Referencias\ReferenciasController;

use App\Http\Controllers\Backend\Recursos\RecursosHumanosController;
use App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos\SolicitudesITController;
use App\Http\Controllers\Backend\Bodega\BMaterialesController;
use App\Http\Controllers\Backend\Bodega\BSolicitudesController;
use App\Http\Controllers\Backend\Bodega\BHistorialController;
use App\Http\Controllers\Backend\Bodega\BReportesController;

use App\Http\Controllers\Backend\Sindico\SindicoController;


use App\Http\Controllers\Backend\Tesoreria\Config\TesoreriaConfigController;


//
//// --- LOGIN ---
//
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

// --- PERMISOS A USUARIOS ---

Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

// --- ASIGNAR USUARIO A DEPARTAMENTO ---

Route::get('/admin/usuario/departamento/index', [PermisoController::class,'indexUsuarioDepartamento'])->name('admin.usuario.departamento.index');
Route::get('/admin/usuario/departamento/tabla', [PermisoController::class,'tablaUsuarioDepartamento']);
Route::post('/admin/p/usuario/departamento/nuevo', [PermisoController::class, 'nuevoUsuarioDepartamento']);
Route::post('/admin/p/usuario/departamento/informacion', [PermisoController::class, 'informacionUsuarioDepartamento']);
Route::post('/admin/p/usuario/departamento/editar', [PermisoController::class, 'editarUsuarioDepartamento']);


// --- USUARIO CONSOLIDADOR ---
Route::get('/admin/usuario/consolidador/index', [PermisoController::class,'indexVistaConsolidador'])->name('admin.usuario.consolidador.index');
Route::get('/admin/usuario/consolidador/tabla', [PermisoController::class,'tablaVistaConsolidador']);
Route::post('/admin/registrar/usuario/consolidador', [PermisoController::class, 'registrarUsuarioConsolidador']);
Route::post('/admin/borrar/usuario/consolidador', [PermisoController::class, 'borrarUsuarioConsolidador']);



// --- INFORMACION DE UN CONSOLIDADOR PARA EL PDF DE ORDEN DE COMPRA


Route::get('/admin/informacion/consolidador/index', [ConsolidadorController::class,'indexInformacionConsolidador'])->name('admin.informacion.consolidador.index');
Route::get('/admin/informacion/consolidador/tabla', [ConsolidadorController::class,'tablaInformacionConsolidador']);
Route::post('/admin/informacion/consolidador/nuevo', [ConsolidadorController::class, 'nuevaInformacionConsolidador']);
Route::post('/admin/informacion/consolidador/informacion', [ConsolidadorController::class, 'infoInformacionConsolidador']);
Route::post('/admin/informacion/consolidador/actualizar', [ConsolidadorController::class, 'actualizarInformacionConsolidador']);







// Para que jefe de presupuesto pueda ver los usuarios asignados a las unidades
Route::get('/admin/usuario/departamento-vista/index', [PermisoController::class,'indexUsuarioDepartamentoVista'])->name('admin.usuario.departamento.vista.index');
Route::get('/admin/usuario/departamento-vista/tabla', [PermisoController::class,'tablaUsuarioDepartamentoVista']);







// --- ASIGNAR USUARIO SEA FORMULADOR ---
// puede editar el proyecto y puede crear las partidas, y otro usuario puede solamente ver

Route::get('/admin/usuario/formulador/index', [PermisoController::class,'indexUsuarioFormulador'])->name('admin.usuario.formulador.index');
Route::get('/admin/usuario/formulador/tabla', [PermisoController::class,'tablaUsuarioFormulador']);
Route::post('/admin/usuario/formulador/nuevo', [PermisoController::class, 'nuevoUsuarioFormulador']);
Route::post('/admin/usuario/formulador/borrar', [PermisoController::class, 'borrarUsuarioFormulador']);


// --- PERFIL DE USUARIO ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

// --- SIN PERMISOS VISTA 403 ---
Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');

// ********** ESTADÍSTICAS DEL SISTEMA **********

// --- ESTADÍSTICAS ---
Route::get('/admin/inicio/index', [EstadisticasController::class,'indexEstadisticas'])->name('admin.estadisticas.index');



// * NUEVO PROYECTO

// retorna vista para registrar nuevo proyecto
Route::get('/admin/proyecto/nuevo/index', [ProyectoController::class,'indexNuevoProyecto'])->name('admin.nuevo.proyecto.index');
// guarda un nuevo proyecto
Route::post('/admin/proyecto/nuevo', [ProyectoController::class, 'nuevoProyecto']);


// * LISTA DE PROYECTOS

// retorna vista de todos los proyectos
Route::get('/admin/proyecto/lista/index', [ProyectoController::class,'indexProyectoLista'])->name('admin.lista.proyectos.index');
// retorna tabla de todos los proyectos
Route::get('/admin/proyecto/lista/tabla/index', [ProyectoController::class,'tablaProyectoLista']);
// información de un proyecto
Route::post('/admin/proyecto/lista/informacion', [ProyectoController::class, 'informacionProyecto']);
// edita la información de un proyecto
Route::post('/admin/proyecto/lista/editar', [ProyectoController::class, 'editarProyecto']);
// retorna vista para MODAL, aquí se visualiza el presupuesto. Aquí se visualiza botón para aprobar el presupuesto
Route::get('/admin/ver/presupuesto/uaci/{id}', [ProyectoController::class,'informacionPresupuestoParaAprobacion']);
// petición para aprobar el presupuesto y guardar las cuentas proyecto
Route::post('/admin/proyecto/aprobar/presupuesto', [ProyectoController::class, 'aprobarPresupuesto']);
// mostrará vista para MODAL, donde está el saldo inicial, el restante y el retenido.
Route::get('/admin/ver/presupuesto/saldo/{id}', [ProyectoController::class,'infoTablaSaldoProyecto']);
// petición para cambiar estado de presupuesto, asi para que el encargado de Presupuesto lo apruebe
Route::post('/admin/proyecto/estado/presupuesto', [ProyectoController::class, 'cambiarEstadoPresupuesto']);
// buscar materiales de un determinado presupuesto de proyecto + partida adicional
Route::post('/admin/buscar/material/soloproyecto',  [ProyectoController::class,'buscadorMaterialRequisicion']);
// información de un estado de proyecto
Route::post('/admin/proyecto/estado/informacion',  [ProyectoController::class,'informacionEstadoProyecto']);
// editar estado de un proyecto
Route::post('/admin/proyecto/estado/editar',  [ProyectoController::class,'editarEstadoProyecto']);
// obtener todos los bolsones
Route::post('/admin/bolsones/todos/informacion',  [ProyectoController::class,'obtenerLosBolsones']);
// información de saldo de bolsón, ya con todos sus descuentos
Route::post('/admin/bolson/saldo/detalle/informacion',  [ProyectoController::class,'infoSaldoBolson']);
// obtener información del proyecto
Route::post('/admin/proyecto/informacion/individual',  [ProyectoController::class,'informacionProyectoIndividual']);


// --- VISTA DE PROYECTO ---

// retorna vista de todos los proyectos
Route::get('/admin/proyecto/lista/index', [ProyectoController::class,'indexProyectoLista'])->name('admin.lista.proyectos.index');
// retorna tabla con todos los proyectos creados
Route::get('/admin/proyecto/lista/tabla/index', [ProyectoController::class,'tablaProyectoLista']);

// retorna vista con información del proyecto individual por ID
Route::get('/admin/proyecto/vista/index/{id}', [ProyectoController::class,'indexProyectoVista']);

// * Bitácoras

// retorna vista de tabla de las bitácoras de un proyecto por ID
Route::get('/admin/proyecto/vista/bitacora/{id}', [ProyectoController::class,'tablaProyectoListaBitacora']);
// registra una nueva bitácora para proyecto ID
Route::post('/admin/proyecto/vista/bitacora/registrar', [ProyectoController::class, 'registrarBitacora']);
// borrar una bitácora de proyecto ID
Route::post('/admin/proyecto/vista/bitacora/borrar', [ProyectoController::class, 'borrarBitacora']);
// información de una bitácora proyecto por ID
Route::post('/admin/proyecto/vista/bitacora/informacion', [ProyectoController::class, 'informacionBitacora']);
// editar bitácora de un proyecto ID
Route::post('/admin/proyecto/vista/bitacora/editar', [ProyectoController::class, 'editarBitacora']);
// pasa a otra vista donde esta el detalle de la bitácora de un proyecto por ID bitácora
Route::get('/admin/proyecto/vista/bitacora-detalle/{id}', [ProyectoController::class,'vistaBitacoraDetalle']);
// retorna tabla detalle de bitácora de un proyecto por ID bitácora
Route::get('/admin/proyecto/vista/tabla/bitacora-detalle/{id}', [ProyectoController::class,'tablaBitacoraDetalle']);
// descargar un documento de la bitácora por ID
Route::get('/admin/proyecto/vista/bitacora-detalle-doc/{file}' , [ProyectoController::class, 'descargarBitacoraDoc']);
// borrar documento bitácora por ID
Route::post('/admin/proyecto/vista/bitacora-detalle/borrar' , [ProyectoController::class, 'borrarBitacoraDetalle']);
// registrar nuevo detalle a una bitácora por ID
Route::post('/admin/proyecto/vista/bitacora-detalle/nuevo' , [ProyectoController::class, 'nuevoBitacoraDetalle']);


// * REQUISICIÓN PARA PROYECTOS

// retorna tabla con todas las requisiciones de un Proyecto ID
Route::get('/admin/proyecto/vista/requisicion/{id}', [ProyectoController::class,'tablaProyectoListaRequisicion']);
// crea una nueva requisición
Route::post('/admin/proyecto/vista/requisicion/nuevo', [ProyectoController::class, 'nuevoRequisicion']);
// información de una requisición de proyecto
Route::post('/admin/proyecto/vista/requisicion/informacion', [ProyectoController::class, 'informacionRequisicion']);
// editar una requisición de proyecto
Route::post('/admin/proyecto/vista/requisicion/editar', [ProyectoController::class, 'editarRequisicion']);
// borrar toda una requisición
Route::post('/admin/proyecto/requisicion/borrar/todo', [ProyectoController::class, 'borrarRequisicion']);
// petición para cancelar un material de requisición
Route::post('/admin/proyecto/requisicion/material/cancelar', [ProyectoController::class, 'cancelarMaterialRequisicion']);
// borrar una requi detalle, específicamente una Fila, ya que ya no se puede Editar
Route::post('/admin/proyecto/requisicion/material/borrarfila', [ProyectoController::class, 'borrarMaterialRequisicionFila']);



// * LISTADO DE REQUERIMIENTOS PENDIENTES PARA SER COTIZADOS POR UACI

// retorna vista con el proyecto, que tiene requerimientos pendientes de cotización
Route::get('/admin/listar/requerimientos/index', [CotizacionController::class,'indexListarRequerimientos'])->name('admin.listar.requerimientos.index');
// retorna tabla con el proyecto, que tiene requerimientos pendientes de cotización
Route::get('/admin/listar/requerimientos/tabla', [CotizacionController::class,'indexTablaListarRequerimientos']);
// retorna vista de requisiciones pendientes de proyecto para ser cotizadas
Route::get('/admin/requerimientos/listado/{id}', [CotizacionController::class,'listadoRequerimientoPorProyecto']);
// retorna tabla de requisiciones pendientes de proyecto para ser cotizadas
Route::get('/admin/requerimientos/listado/tabla/{id}', [CotizacionController::class,'tablaRequerimientosIndividual']);
// retorna información de requerimiento para ser cotizada
Route::post('/admin/requerimientos/informacion', [CotizacionController::class, 'informacionRequerimiento']);
// se envía los ID requi_detalle de proyectos para verificar y retornar información de lo que se cotizara
Route::post('/admin/requerimientos/verificar', [CotizacionController::class, 'verificarRequerimiento']);
// guarda una nueva cotización
Route::post('/admin/requerimientos/cotizacion/guardar', [CotizacionController::class, 'guardarNuevaCotizacion']);


// * PRESUPUESTO DE PROYECTO

// retorna tabla con las partidas de un proyecto por ID
Route::get('/admin/proyecto/vista/presupuesto/{id}', [ProyectoController::class,'tablaProyectoListaPresupuesto']);
// registra una nueva partida a un proyecto por ID
Route::post('/admin/proyecto/agregar/presupuesto',  [ProyectoController::class,'agregarPresupuestoPartida']);
// obtiene información de la partida de un proyecto
Route::post('/admin/proyecto/vista/presupuesto/informacion', [ProyectoController::class, 'informacionPresupuesto']);
// editar la información de una partida
Route::post('/admin/proyecto/vista/presupuesto/editar', [ProyectoController::class, 'editarPresupuesto']);
// borra una partida con todos los detalle
Route::post('/admin/proyecto/vista/presupuesto/borrar', [ProyectoController::class, 'borrarPresupuesto']);
// generar un PDF con el presupuesto de Proyecto
Route::get('/admin/generar/pdf/presupuesto/{id}', [ControlPdfController::class,'generarPrespuestoPdf']);
// verifica si partida mano de obra existe
Route::post('/admin/proyecto/partida/manoobra/existe', [ProyectoController::class, 'verificarPartidaManoObra']);

// * PLANILLA PARA PROYECTO

// retorna vista para agregar planilla a proyecto
Route::get('/admin/planilla/lista/{id}', [CuentaProyectoController::class,'indexPlanilla']);
// retorna tabla para agregar planilla a proyecto
Route::get('/admin/planilla/tabla/lista/{id}', [CuentaProyectoController::class,'tablaPlanilla']);
// agrega una nueva planilla a proyecto
Route::post('/admin/planilla/nuevo',  [CuentaProyectoController::class,'nuevaPlanilla']);
// obtener información de planilla
Route::post('/admin/planilla/informacion',  [CuentaProyectoController::class,'informacionPlanilla']);
// edita la información de una planilla
Route::post('/admin/planilla/editar',  [CuentaProyectoController::class,'editarPlanilla']);

// * MOVIMIENTO CUENTA PROYECTO

// retorna vista con los movimientos de cuenta para un proyecto ID
Route::get('/admin/movicuentaproy/indexmovicuentaproy/{id}', [CuentaProyectoController::class,'indexMoviCuentaProy']);
// retorna tabla con los movimientos de cuenta para un proyecto ID
Route::get('/admin/movicuentaproy/tablamovicuentaproy/{id}', [CuentaProyectoController::class,'indexTablaMoviCuentaProy']);
// retorna vista con los historicos movimientos por proyecto ID
Route::get('/admin/movicuentaproy/historico/{id}', [CuentaProyectoController::class,'indexMoviCuentaProyHistorico']);
// retorna tabla con los historicos movimientos por proyecto ID
Route::get('/admin/movicuentaproy/tablahistorico/{id}', [CuentaProyectoController::class,'tablaMoviCuentaProyHistorico']);
// registra una nuevo movimiento de cuenta
Route::post('/admin/movicuentaproy/nuevo',  [CuentaProyectoController::class,'nuevaMoviCuentaProy']);
// descargar un documento Reforma de movimiento de cuenta
Route::get('/admin/movicuentaproy/documento/{id}',  [CuentaProyectoController::class,'descargarReforma']);
// guardar un documento Reforma para movimiento de cuenta
Route::post('/admin/movicuentaproy/documento/guardar',  [CuentaProyectoController::class,'guardarDocumentoReforma']);
// información de un movimiento de cuenta
Route::post('/admin/movicuentaproy/informacion',  [CuentaProyectoController::class,'informacionMoviCuentaProy']);
// al mover el select de movimiento cuenta a modificar, quiero ver el saldo restante
Route::post('/admin/movicuentaproy/info/saldo',  [CuentaProyectoController::class,'infoSaldoRestanteCuenta']);
// petición para que jefe presupuesto de permiso de realizar un movimiento de cuenta
Route::post('/admin/movicuentaproy/autorizar/movimiento',  [CuentaProyectoController::class,'autorizarMovimientoDeCuenta']);
// petición para que jefe presupuesto quita permiso para realizar un movimiento de cuenta
Route::post('/admin/movicuentaproy/denegar/movimiento',  [CuentaProyectoController::class,'denegarMovimientoDeCuenta']);
// ver informacion del movimiento de cuenta para que jefe presupuesto Apruebe o Denegar
Route::post('/admin/movimientohistorico/verificar/informacion',  [CuentaProyectoController::class,'informacionHistoricoParaAutorizar']);
// denegar y borrar un movimiento de cuenta
Route::post('/admin/movimientohistorico/denegar/borrar',  [CuentaProyectoController::class,'denegarBorrarMovimientoCuenta']);
// autorizar y verificar el movimiento de cuenta
Route::post('/admin/movimientohistorico/autorizar/actualizar',  [CuentaProyectoController::class,'autorizarMovimientoCuenta']);


// * PARTIDAS ADICIONALES
// retorna vista con las partidas adicionales de un x proyecto
Route::get('/admin/partida/adicional/contenedor/index/{id}', [CuentaProyectoController::class,'indexPartidaAdicionalContenedor']);
// retorna tabla con las partidas adicionales de un x proyecto
Route::get('/admin/partida/adicional/contenedor/tabla/{id}', [CuentaProyectoController::class,'tablaPartidaAdicionalContenedor']);
// autorizar que se pueda crear partidas adicionales
Route::post('/admin/partida/adicional/permiso/autorizar',  [CuentaProyectoController::class,'autorizarPartidaAdicionalPermiso']);
// denegar que se pueda crear partidas adicionales
Route::post('/admin/partida/adicional/permiso/denegar',  [CuentaProyectoController::class,'denegarPartidaAdicionalPermiso']);
// crear solicitud de partida
Route::post('/admin/partida/adicional/crear/solicitud',  [CuentaProyectoController::class,'crearSolicitudPartidaAdicional']);
// vista donde se crean ya las partidas adicionales
Route::get('/admin/partida/adicional/creacion/index/{id}', [CuentaProyectoController::class,'indexCreacionPartidasAdicionales']);
// tabla donde se crean ya las partidas adicionales
Route::get('/admin/partida/adicional/creacion/tabla/{id}', [CuentaProyectoController::class,'tablaCreacionPartidasAdicionales']);
// borrar contenedor de partidas adicionales
Route::post('/admin/partida/adicional/borrar/contenedor',  [CuentaProyectoController::class,'borrarContenedorPartidaAdicional']);
// descargar documento de obra adicional
Route::get('/admin/partida/adicional/obraadicional/doc/{id}', [CuentaProyectoController::class,'documentoObraAdicional']);
// registrar partida adicional con su detalle, validando que no sobrepase el x porcentaje modificable%
// en este caso no se valida que haya fondos en bolsón, sino cuando se aprueba todas las partidas adicionales
Route::post('/admin/proyecto/agregar/partida/adicional/presupuesto',  [CuentaProyectoController::class,'registrarPartidaAdicional']);
// borrar una partida adicional
Route::post('/admin/proyecto/borrar/partida/adicional',  [CuentaProyectoController::class,'borrarPartidaAdicional']);
// obtiene información de la partida adicional de un proyecto
Route::post('/admin/proyecto/partida/adicional/informacion', [CuentaProyectoController::class, 'informacionPartidaAdicional']);
// editar la información de una partida adicional
Route::post('/admin/proyecto/partida/adicional/editar', [CuentaProyectoController::class, 'editarPresupuestoPartidaAdicional']);
// información de contenedor partida adicional
Route::post('/admin/partida/adicio/contenedor/estado/informacion', [CuentaProyectoController::class, 'informacionEstadoContenedorPartidaAdic']);
// actualizar estado de contenedor partida adicional
Route::post('/admin/partida/adicio/contenedor/estado/actualizar', [CuentaProyectoController::class, 'actualizarEstadoContenedorPartidaAdic']);
// vista de partidas adicionales, otras opciones solo para jefatura
Route::get('/admin/partida/adici/conte/jefatura/index/{id}', [CuentaProyectoController::class,'indexPartidaAdicionalConteJefatura']);
// retorna tabla con las partidas adicionales de un x proyecto para jefatura
Route::get('/admin/partida/adici/conte/jefatura/tabla/{id}', [CuentaProyectoController::class,'tablaPartidaAdicionalConteJefatura']);
// información de porcentaje de obra adicional
Route::post('/admin/partida/adicional/porcentaje/info', [CuentaProyectoController::class, 'informacionPorcentajeObra']);
// actualizar porcentaje de obra adicional
Route::post('/admin/partida/adicional/porcentaje/actualizar', [CuentaProyectoController::class, 'actualizarPorcentajeObra']);
// información del contenedor para jefatura
Route::post('/admin/partida/adicio/infojefatura/estado/informacion', [CuentaProyectoController::class, 'infoContenedorJefatura']);
// aprobar una partida adicional
Route::post('/admin/partida/adicional/aprobar', [CuentaProyectoController::class, 'aprobarPartidaAdicional']);
// generar un documento PDF
Route::get('/admin/partida/adicional/verpdf/{id}', [CuentaProyectoController::class, 'generarPdfPartidaAdicional']);
// comprobar que haya partidas adicionales
Route::post('/admin/partida/adicional/comprobar/quehaya', [CuentaProyectoController::class, 'verificarSiHayPartidas']);
// guardar documento de contenedor para partida adicional
Route::post('/admin/partida/adicional/documento/guardar', [CuentaProyectoController::class, 'guardarDocumentoPartidaAdic']);


// buscador de material para crear una partida
Route::post('/admin/proyecto/buscar/material-presupuesto',  [ProyectoController::class,'buscadorMaterialPresupuesto']);
// buscador de material para editar una partida
Route::post('/admin/proyecto/buscar/material-presupuesto-editar',  [ProyectoController::class,'buscadorMaterialPresupuestoEditar']);
// mostrar modal de materiales para crear una requisición, solo muestra materiales asignado a presupuesto de proyecto
Route::get('/admin/ver/materiales/admin/requisicion/{id}', [ProyectoController::class,'verCatalogoMaterialRequisicion']);
// mostrar modal de todos los materiales de partidas adicionales
Route::get('/admin/ver/materiales/partida/adicional/{id}', [ProyectoController::class,'verCatalogoMaterialPartidaAdicional']);


// * COTIZACIONES PENDIENTES PARA PROYECTOS

// retorna vista con las cotizaciones pendientes
Route::get('/admin/cotizacion/proyecto/pendiente/index', [CotizacionController::class,'indexPendiente'])->name('cotizaciones.pendientes.proyecto.index');
// retorna tabla con las cotizaciones pendientes
Route::get('/admin/cotizacion/proyecto/pendiente/tabla', [CotizacionController::class,'indexPendienteTabla']);
// retorna vista de los detalle de la cotización, un uso es cuando uaci espera que sea aprobada la coti
Route::get('/admin/cotizacion/proyecto/individual/index/{id}', [CotizacionController::class,'indexCotizacion']);
// autorizar la cotización
Route::post('/admin/cotizacion/proyecto/autorizar',  [CotizacionController::class,'autorizarCotizacion']);
// denegar la cotización
Route::post('/admin/cotizacion/proyecto/denegar',  [CotizacionController::class,'denegarCotizacion']);
// vista de cotización detalle para procesadas o denegadas
Route::get('/admin/cotizacion/proyecto/detalle/{id}', [CotizacionController::class,'vistaDetalleCotizacion']);


// * VISTA COTIZACIONES AUTORIZADAS

// retorna vista de cotizaciones autorizadas
Route::get('/admin/cotizacion/autorizadas/index', [CotizacionController::class,'indexAutorizadas'])->name('cotizaciones.autorizadas.index');
// retorna tabla de cotizaciones autorizadas
Route::get('/admin/cotizacion/proyecto/autorizadas/tabla-index', [CotizacionController::class,'indexAutorizadasTabla']);

// * VISTA COTIZACIONES DENEGADAS

// retorna vista de cotizaciones denegadas
Route::get('/admin/cotizacion/proyecto/denegadas/index', [CotizacionController::class,'indexDenegadas'])->name('cotizaciones.denegadas.index');
// retorna tabla de cotizaciones denegadas
Route::get('/admin/cotizacion/proyecto/denegadas/tabla-index', [CotizacionController::class,'indexDenegadasTabla']);

// * ORDENES

// crear una nueva orden
Route::post('/admin/ordenes/proyecto/generar/nuevo',  [OrdenController::class,'generarOrden']);
// generar PDF de orden de compra y variable {cantidad} es # de material por hoja
Route::get('/admin/ordenes/proyecto/pdf/{id}/{cantidad}', [OrdenController::class,'vistaPdfOrden']);

// * ORDENES DE COMPRAS PROCESADAS

// retorna vista con las ordenes de compras
Route::get('/admin/ordenes/compras/index', [OrdenController::class,'indexOrdenesCompras'])->name('admin.ordenes.compra.procesadas');
// retorna tabla con las ordenes de compras
Route::get('/admin/ordenes/compras/tabla-index', [OrdenController::class,'tablaOrdenesCompras']);
// anular una orden de compra
Route::post('/admin/ordenes/proyecto/anular/compra',  [OrdenController::class,'anularCompra']);
// generar acta de una orden de compra
Route::post('/admin/ordenes/proyecto/generar/acta',  [OrdenController::class,'generarActa']);
// generar PDF de la acta de compra
Route::get('/admin/ordenes/acta/reporte/{id}', [OrdenController::class,'reporteActaGenerada']);

// * ORDENES DE COMPRA DENEGADAS
// retorna vista con las ordenes de compras denegadas
Route::get('/admin/ordenes/compras/denegadas/index', [OrdenController::class,'indexOrdenesComprasDenegadas'])->name('admin.ordenes.compra.denegadas');
// retorna tabla con las ordenes de compras denegadas
Route::get('/admin/ordenes/compras/denegadas/tabla', [OrdenController::class,'tablaOrdenesComprasDenegadas']);



// * CONFIGURACIÓN DEL SISTEMA

// retorna vista de las unidades de medida para Proyecto
Route::get('/admin/unidadmedida/index', [UnidadMedidaController::class,'indexUnidadMedidaProyecto'])->name('admin.unidadmedida.index');
// retorna tabla de las unidades de medida para Proyecto
Route::get('/admin/unidadmedida/tabla/index', [UnidadMedidaController::class,'tablaUnidadMedidaProyecto']);
// registrar una nueva unidad de medida
Route::post('/admin/unidadmedida/nuevo', [UnidadMedidaController::class, 'nuevaUnidadMedida']);
// obtener información de unidad de medida
Route::post('/admin/unidadmedida/informacion', [UnidadMedidaController::class, 'informacionUnidadMedida']);
// editar una unidad de medida
Route::post('/admin/unidadmedida/editar', [UnidadMedidaController::class, 'editarUnidadMedida']);

// * PROVEEDORES

// retorna vista con los proveedores para cotizaciones
Route::get('/admin/proveedores/index', [ProveedoresController::class,'indexVistaProveedor'])->name('admin.proveedores.index');
// retorna tabla con los proveedores para cotizaciones
Route::get('/admin/proveedores/tabla/index', [ProveedoresController::class,'tablaVistaProveedor']);
// registra nuevo proveedor
Route::post('/admin/proveedores/nuevo', [ProveedoresController::class, 'nuevoProveedor']);
// obtener información de un proveedor
Route::post('/admin/proveedores/informacion', [ProveedoresController::class, 'informacionProveedor']);
// edita la información de proveedor
Route::post('/admin/proveedores/editar', [ProveedoresController::class, 'editarProveedor']);

// * ADMINISTRADORES DE PROYECTO

// retorna vista con los nombres de administradores
Route::get('/admin/administradores/index', [AdministradoresController::class,'indexVistaAdministradores'])->name('admin.administradores.index');
// retorna tabla con los nombres de administradores
Route::get('/admin/administradores/tabla/index', [AdministradoresController::class,'tablaVistaAdministradores']);
// registra nuevo administrador de proyectos
Route::post('/admin/administradores/nuevo', [AdministradoresController::class, 'nuevoAdministrador']);
// obtener información de administrador de proyecto
Route::post('/admin/administradores/informacion', [AdministradoresController::class, 'informacionAdministrador']);
// editar datos de administrador de proyecto
Route::post('/admin/administradores/editar', [AdministradoresController::class, 'editarAdministrador']);
// obtener información de un imprevisto de proyecto
Route::post('/admin/proyecto/buscar/imprevisto', [AdministradoresController::class, 'informacionImprevistoProyecto']);
// editar imprevisto de proyecto
Route::post('/admin/proyecto/editar/imprevisto', [AdministradoresController::class, 'editarImprevistoProyecto']);



// * CATALOGO DE MATERIALES PARA PROYECTO

// retorna vista con catálogo de materiales para proyecto
Route::get('/admin/catalogo/materiales/index', [MaterialesController::class,'indexCatalogoMaterial'])->name('admin.catalogo.materiales.index');
// retorna tabla con catálogo de materiales para proyecto
Route::get('/admin/catalogo/materiales/tabla/index', [MaterialesController::class,'tablaCatalogoMaterial']);
// registra nuevo material para proyectos
Route::post('/admin/catalogo/materiales/nuevo', [MaterialesController::class, 'nuevoMaterial']);
// obtener información de un material de proyecto
Route::post('/admin/catalogo/materiales/informacion', [MaterialesController::class, 'informacionCatalogoMaterial']);
// editar catálogo de material de proyecto
Route::post('/admin/catalogo/materiales/editar', [MaterialesController::class, 'editarMaterial']);

// * SOLICITUD DE MATERIAL POR PARTE DE INGENIERÍA

// retorna vista con materiales solicitados para agregar catálogo de materiales
Route::get('/admin/solicitud/material/ing/index', [MaterialesController::class,'indexSolicitudMaterialIng'])->name('admin.solicitud.material.ing.index');
// retorna tabla con materiales solicitados para agregar catálogo de materiales
Route::get('/admin/solicitud/material/ing/tabla', [MaterialesController::class,'tablaSolicitudMaterialIng']);
// nuevo registro de material solicitado
Route::post('/admin/solicitud/material/ing/nuevo', [MaterialesController::class, 'nuevoSolicitudMaterialIng']);
// información para editar material solicitado
Route::post('/admin/solicitud/material/ing/informacion', [MaterialesController::class, 'informacionSolicitudMaterialIng']);
// borrar material solicitado
Route::post('/admin/solicitud/material/ing/borrar', [MaterialesController::class, 'borrarSolicitudMaterialIng']);
// agregar material solicitado por ingenieria
Route::post('/admin/solicitud/material/ing/agregar', [MaterialesController::class, 'agregarSolicitudMaterialIng']);

// * CATÁLOGO DE MATERIALES PARA QUE INGENIERÍA VEA LA LISTA DE LO QUE HAY

// retorna vista con todos los materiales de catálogo para que unicamente pueda verse
Route::get('/admin/vista/catalogo/material/index', [MaterialesController::class,'indexVistaCatalogoMaterial'])->name('admin.vista.catalogo.material.index');
// retorna tabla con todos los materiales de catálogo para que unicamente pueda verse
Route::get('/admin/vista/catalogo/material/tabla', [MaterialesController::class,'tablaVistaCatalogoMaterial']);


// * BOLSÓN

// retorna vista con lista de bolsones
Route::get('/admin/bolson/index', [BolsonController::class,'indexBolson'])->name('admin.bolson.index');
// retorna tabla con lista de bolsones
Route::get('/admin/bolson/tabla', [BolsonController::class,'tablaBolson']);
// ver cuanto saldo hay en x obj especi, de x año, de un presupuesto unidad si existe
Route::post('/admin/bolson/verificar/saldo/objetos', [BolsonController::class, 'verificarSaldosObjetos']);
// registrar nuevo bolsón
Route::post('/admin/bolson/registrar/nuevo', [BolsonController::class, 'nuevoRegistroBolson']);
// retorna vista para detalle de bolsón
Route::get('/admin/bolson/detalle/index/{id}', [BolsonController::class,'indexDetalleBolson']);
// retorna tabla con detalle de bolsón
Route::get('/admin/bolson/detalle/tabla/{id}', [BolsonController::class,'tablaDetalleBolson']);
// retorna lista de fuente de recursos, solo si estan activos
Route::post('/admin/bolson/retornar/fuente/recursos', [BolsonController::class, 'retornarFuenteRecursosActivos']);


// * CLASIFICACIONES

// retorna vista con las clasificaciones de material
Route::get('/admin/clasificaciones/index', [ClasificacionesController::class,'indexClasificaciones'])->name('admin.clasificaciones.index');
// retorna tabla con las clasificaciones de material
Route::get('/admin/clasificaciones/tabla/index', [ClasificacionesController::class,'tablaClasificaciones']);
// registra nueva clasificación
Route::post('/admin/clasificaciones/nuevo', [ClasificacionesController::class, 'nuevaClasificacion']);
// obtener información de una clasificación
Route::post('/admin/clasificaciones/informacion', [ClasificacionesController::class, 'informacionClasificacion']);
// editar clasificación
Route::post('/admin/clasificaciones/editar', [ClasificacionesController::class, 'editarClasificacion']);


// * LÍNEA DE TRABAJO

// retorna vista con las líneas de trabajo
Route::get('/admin/linea/trabajo/index', [LineaTrabajoController::class,'indexLineaTrabajo'])->name('admin.linea.de.trabajo.index');
// retorna tabla con las líneas de trabajo
Route::get('/admin/linea/trabajo/tabla/index', [LineaTrabajoController::class,'tablaLineaTrabajo']);
// registrar nueva línea de trabajo
Route::post('/admin/linea/trabajo/nuevo', [LineaTrabajoController::class, 'nuevaLineaTrabajo']);
// obtener información de línea de trabajo
Route::post('/admin/linea/trabajo/informacion', [LineaTrabajoController::class, 'informacionLineaTrabajo']);
// editar línea de trabajo
Route::post('/admin/linea/trabajo/editar', [LineaTrabajoController::class, 'editarLineaTrabajo']);

// * FUENTE DE FINANCIAMIENTO

// retorna vista con las fuentes de financiamiento
Route::get('/admin/fuentef/index', [FuenteFinanciamientoController::class,'indexFuenteFinanciamiento'])->name('admin.fuente.financiamiento.index');
// retorna tabla con las fuentes de financiamiento
Route::get('/admin/fuentef/tabla/index', [FuenteFinanciamientoController::class,'tablaFuenteFinanciamiento']);
// registrar nueva fuente de financiamiento
Route::post('/admin/fuentef/nuevo', [FuenteFinanciamientoController::class, 'nuevaFuenteFinanciamiento']);
// obtener información de una fuente de financiamiento
Route::post('/admin/fuentef/informacion', [FuenteFinanciamientoController::class, 'informacionFuenteFinanciamiento']);
// editar fuente de financiamiento
Route::post('/admin/fuentef/editar', [FuenteFinanciamientoController::class, 'editarFuenteFinanciamiento']);

// * FUENTE DE RECURSOS

// retorna vista con las fuentes de recursos
Route::get('/admin/fuenter/index', [FuenteRecursosController::class,'indexFuenteRecursos'])->name('admin.fuente.recurso.index');
// retorna tabla con las fuentes de recursos
Route::get('/admin/fuenter/tabla/index', [FuenteRecursosController::class,'tablaFuenteRecursos']);
// registrar nueva fuente de recursos
Route::post('/admin/fuenter/nuevo', [FuenteRecursosController::class, 'nuevaFuenteRecursos']);
// obtener información de una fuente de recursos
Route::post('/admin/fuenter/informacion', [FuenteRecursosController::class, 'informacionFuenteRecursos']);
// editar una fuente de recursos
Route::post('/admin/fuenter/editar', [FuenteRecursosController::class, 'editarFuenteRecursos']);

// * ÁREA DE GESTIÓN

// retorna vista con las áreas de gestión
Route::get('/admin/areagestion/index', [AreaGestionController::class,'indexAreaGestion'])->name('admin.area.gestion.index');
// retorna tabla con las áreas de gestión
Route::get('/admin/areagestion/tabla/index', [AreaGestionController::class,'tablaAreaGestion']);
// registrar nueva área de gestión
Route::post('/admin/areagestion/nuevo', [AreaGestionController::class, 'nuevaAreaGestion']);
// obtener información de un área de gestión
Route::post('/admin/areagestion/informacion', [AreaGestionController::class, 'informacionAreaGestion']);
// editar área de gestión
Route::post('/admin/areagestion/editar', [AreaGestionController::class, 'editarAreaGestion']);


// * EQUIPOS

// retorna vista de equipos
Route::get('/admin/equipos/index', [EquiposController::class,'indexEquipos'])->name('admin.equipos.index');
// retorna tabla de equipos
Route::get('/admin/equipos/tabla/index', [EquiposController::class,'tablaEquipos']);
// registra un nuevo equipo
Route::post('/admin/equipos/nuevo', [EquiposController::class, 'nuevoEquipo']);
// obtener información de un equipo
Route::post('/admin/equipos/informacion', [EquiposController::class, 'informacionEquipo']);
// editar un equipo
Route::post('/admin/equipos/editar', [EquiposController::class, 'editarEquipo']);

// * RUBRO

// retorna vista de rubros
Route::get('/admin/rubro/index', [ProveedoresController::class,'indexRubro'])->name('admin.rubro.index');
// retorna tabla de rubros
Route::get('/admin/rubro/tabla', [ProveedoresController::class,'tablaRubro']);
// registra un nuevo rubro
Route::post('/admin/rubro/nuevo', [ProveedoresController::class, 'nuevaRubro']);
// obtener información de un rubro
Route::post('/admin/rubro/informacion', [ProveedoresController::class, 'informacionRubro']);
// editar un rubro
Route::post('/admin/rubro/editar', [ProveedoresController::class, 'editarRubro']);

// * CUENTA

// retorna vista de cuenta
Route::get('/admin/cuenta/index', [CodigoEspecifController::class,'indexCuenta'])->name('admin.cuenta.index');
// retorna tabla de cuenta
Route::get('/admin/cuenta/tabla', [CodigoEspecifController::class,'tablaCuenta']);
// registrar una nueva cuenta
Route::post('/admin/cuenta/nuevo', [CodigoEspecifController::class, 'nuevaCuenta']);
// obtener información de una cuenta
Route::post('/admin/cuenta/informacion', [CodigoEspecifController::class, 'informacionCuenta']);
// editar una cuenta
Route::post('/admin/cuenta/editar', [CodigoEspecifController::class, 'editarCuenta']);

// * OBJETO ESPECIFICO

// retorna vista de objeto específico
Route::get('/admin/objespecifico/index', [CodigoEspecifController::class,'indexObjEspecifico'])->name('admin.obj.especifico.index');
// retorna tabla de objeto específico
Route::get('/admin/objespecifico/tabla', [CodigoEspecifController::class,'tablaObjEspecifico']);
// registrar un objeto específico
Route::post('/admin/objespecifico/nuevo', [CodigoEspecifController::class, 'nuevaObjEspecifico']);
// obtener información de un objeto específico
Route::post('/admin/objespecifico/informacion', [CodigoEspecifController::class, 'informacionObjEspecifico']);
// editar un objeto específico
Route::post('/admin/objespecifico/editar', [CodigoEspecifController::class, 'editarObjEspecifico']);

// * ADESCOS

// retorna vista de adescos
Route::get('/admin/adescos/index', [AdescosController::class,'indexAdescos'])->name('admin.adescos.index');
// retorna tabla de adescos
Route::get('/admin/adescos/tabla/index', [AdescosController::class,'tablaAdescos']);
// registrar una nueva adesco
Route::post('/admin/adescos/nuevo', [AdescosController::class, 'nuevoAdesco']);
// obtener información de una adesco
Route::post('/admin/adescos/informacion', [AdescosController::class, 'informacionAdesco']);
// editar una adesco
Route::post('/admin/adescos/editar', [AdescosController::class, 'editarAdesco']);

// * ASOCIACIONES

// retorna vista de asociación
Route::get('/admin/asociaciones/index', [AsociacionesController::class,'indexAsociacion'])->name('admin.asociaciones.index');
// retorna tabla de asociación
Route::get('/admin/asociaciones/tabla/index', [AsociacionesController::class,'tablaAsociacion']);
// registrar una nueva asociación
Route::post('/admin/asociaciones/nuevo', [AsociacionesController::class, 'nuevoAsociacion']);
// obtener información de una asociación
Route::post('/admin/asociaciones/informacion', [AsociacionesController::class, 'informacionAsociacion']);
// editar una asociación
Route::post('/admin/asociaciones/editar', [AsociacionesController::class, 'editarAsociacion']);


// * RECURSOS HUMANOS

// retorna vista de recursos humanos
Route::get('/admin/recursos/index', [RecursosController::class,'indexRecursosHumanos'])->name('admin.recursos.index');



// ************************************** PRESUPUESTO DE UNIDADES **********************************************************************************

// --- AÑO DE PRESUPUESTO ---

// retorna vista de años para presupuesto
Route::get('/admin/p/anio/presupuesto/index', [AnioPresupuestoUnidadController::class,'indexAnioPresupuesto'])->name('p.admin.anio.presupuesto.index');
// retorna tabla de años para presupuesto
Route::get('/admin/p/anio/presupuesto/tabla', [AnioPresupuestoUnidadController::class,'tablaAnioPresupuesto']);
// registra nuevo año
Route::post('/admin/p/anio/presupuesto/nuevo', [AnioPresupuestoUnidadController::class, 'nuevoAnioPresupuesto']);
// obtener información de año
Route::post('/admin/p/anio/presupuesto/informacion', [AnioPresupuestoUnidadController::class, 'informacionAnioPresupuesto']);
// editar un año de presupuesto
Route::post('/admin/p/anio/presupuesto/editar', [AnioPresupuestoUnidadController::class, 'editarAnioPresupuesto']);

// * NOMBRE DE LOS DEPARTAMENTOS

// retorna vista con los departamentos
Route::get('/admin/p/departamentos/index', [DepartamentoPresupuestoUnidadController::class,'indexDepartamentos'])->name('p.admin.departamentos.presupuesto.index');
// retorna tabla con los departamentos
Route::get('/admin/p/departamentos/tabla', [DepartamentoPresupuestoUnidadController::class,'tablaDepartamentos']);
// registrar un nuevo departamento
Route::post('/admin/p/departamentos/nuevo', [DepartamentoPresupuestoUnidadController::class, 'nuevoDepartamentos']);
// obtener información de un departamento
Route::post('/admin/p/departamentos/informacion', [DepartamentoPresupuestoUnidadController::class, 'informacionDepartamentos']);
// editar un departamento
Route::post('/admin/p/departamentos/editar', [DepartamentoPresupuestoUnidadController::class, 'editarDepartamentos']);

// * UNIDAD DE MEDIDA PARA UNIDADES

// retorna vista con unidades de medida para presupuesto unidades
Route::get('/admin/p/unidadmedida/index', [UnidadMedidaPresupuestoUnidadController::class,'indexUnidadMedida'])->name('p.admin.unidadmedida.presupuesto.index');
// retorna tabla con unidades de medida para presupuesto unidades
Route::get('/admin/p/unidadmedida/tabla', [UnidadMedidaPresupuestoUnidadController::class,'tablaUnidadMedida']);
// registra una nueva unidad de medida
Route::post('/admin/p/unidadmedida/nuevo', [UnidadMedidaPresupuestoUnidadController::class, 'nuevoUnidadMedida']);
// obtener información de unidad de medida
Route::post('/admin/p/unidadmedida/informacion', [UnidadMedidaPresupuestoUnidadController::class, 'informacionUnidadMedida']);
// edita una unidad de medida
Route::post('/admin/p/unidadmedida/editar', [UnidadMedidaPresupuestoUnidadController::class, 'editarUnidadMedida']);

// * CATÁLOGO DE MATERIALES PARA DEPARTAMENTOS

// retorna vista catálogo de materiales para presupuesto de unidades
Route::get('/admin/p/materiales/index', [MaterialesPresupuestoUnidadController::class,'indexMaterialesPresupuesto'])->name('p.admin.materiales.presupuesto.index');
// retorna tabla catálogo de materiales para presupuesto de unidades
Route::get('/admin/p/materiales/tabla/index', [MaterialesPresupuestoUnidadController::class,'tablaMaterialesPresupuesto']);
// registrar un nuevo material
Route::post('/admin/p/materiales/nuevo', [MaterialesPresupuestoUnidadController::class, 'nuevoMaterialesPresupuesto']);
// obtener información de material
Route::post('/admin/p/materiales/informacion', [MaterialesPresupuestoUnidadController::class, 'informacionMaterialesPresupuesto']);
// editar un material
Route::post('/admin/p/materiales/editar', [MaterialesPresupuestoUnidadController::class, 'editarMaterialesPresupuesto']);
// oculta un material, pero siempre sera visible si usuario ya habia seleccionado ese material
Route::post('/admin/p/basepresupuesto/materiales/ocultar', [MaterialesPresupuestoUnidadController::class, 'ocultarMaterialesPresupuesto']);




// * CREACIÓN Y EDICIÓN DE PRESUPUESTO DE UNIDAD

// retorna vista para revisión de presupuesto por unidad y año
Route::get('/admin/p/revision/presupuesto/index', [ConfiguracionPresupuestoUnidadController::class,'indexRevisionPresupuestoUnidad'])->name('p.revision.presupuesto.unidad');
// retorna vista para generar reportes y consolidado de presupuesto de unidades
Route::get('/admin/p/reportes/unidad/presupuesto/index', [ConfiguracionPresupuestoUnidadController::class,'indexReportePresupuestoUnidad'])->name('p.generar.reportes.presupuesto.unidad');
// retorna vista para crear nuevo presupuesto de la unidad
Route::get('/admin/p/crear/presupuesto/unidad/index', [ConfiguracionPresupuestoUnidadController::class,'indexCrearPresupuestoUnidad'])->name('p.admin.crear.presupuesto.index');
// esta vista retorna con el presupuesto nuevo. y al cargarse desactiva el modal loading de carga
Route::get('/admin/p/contenedor/nuevo/presupuesto', [ConfiguracionPresupuestoUnidadController::class,'contenedorNuevoPresupuesto']);
// busca material del catálogo de materiales para unidades
Route::post('/admin/p/buscar/material/presupuesto', [ConfiguracionPresupuestoUnidadController::class, 'buscarMaterialPresupuestoUnidad']);
// crea el nuevo presupuesto del año correspondiente
Route::post('/admin/p/crear/presupuesto/unidad', [ConfiguracionPresupuestoUnidadController::class, 'nuevoPresupuestoUnidades']);
// retorna vista editar un presupuesto
Route::get('/admin/p/editar/presupuesto/unidad/index', [ConfiguracionPresupuestoUnidadController::class,'indexEditarPresupuestoUnidad'])->name('p.admin.editar.presupuesto.index');
// retorna vista para seleccionar año para editar un presupuesto
Route::get('/admin/p/editar/presupuesto/anio/{id}', [ConfiguracionPresupuestoUnidadController::class,'indexPresupuestoUnidadEdicion']);
// retorna contenedor para editar un presupuesto
Route::get('/admin/p/editar/presupuesto/anio/contenedor/{id}', [ConfiguracionPresupuestoUnidadController::class,'contenedorEditarPresupuestoUnidad']);
// petición para editar un presupuesto si no esta en revisión o aprobado
Route::post('/admin/p/editar/presupuesto/editar', [ConfiguracionPresupuestoUnidadController::class,'editarPresupuestoUnidad']);
// registrar un nuevo proyecto para presupuesto de la unidad
Route::post('/admin/p/registrar/proyecto/presupuesto/unidad', [ConfiguracionPresupuestoUnidadController::class,'registrarProyectoPresupuestoUnidad']);
// solo obtener las unidades de medida para registrar material solicitado por unidad
Route::post('/admin/p/presupuesto/obtener/unidad/medida', [ConfiguracionPresupuestoUnidadController::class,'informacionUnidadMedidaPresupuesto']);

// actualizar fila de un proyecto aprobado
Route::post('/admin/p/actualizar/proyecto/aprobadosfila', [ConfiguracionPresupuestoUnidadController::class,'actualizarDatosProyectoAprobado']);






// retorna vista revisar presupuesto y ver si se aprueba, se envía ID departamento y ID unidad
Route::get('/admin/p/departamento/presupuesto/unidad/{depa}/{anio}', [ConfiguracionPresupuestoUnidadController::class,'indexPresupuestoParaAprobar']);
// retorna contenedor de presupuesto para revisión
Route::get('/admin/p/departamento/presupuesto/contenedor/{depa}/{anio}', [ConfiguracionPresupuestoUnidadController::class,'contenedorPresupuestoIndividual']);
// petición para transferir material solicitado por una unidad y agregar a base de materiales
Route::post('/admin/p/presupuesto/nuevo/material/transferir', [ConfiguracionPresupuestoUnidadController::class,'transferirNuevoMaterial']);
// actualizar estado de un presupuesto
Route::post('/admin/p/presupuesto/unidad/cambiar/estado', [ConfiguracionPresupuestoUnidadController::class,'editarEstadoPresupuesto']);
// verifica si todos los presupuestos estén aprobados para generar consolidado PDF
Route::post('/admin/p/generador/verificar/consolidado/presupuesto', [ConfiguracionPresupuestoUnidadController::class,'verificarConsolidadoPresupuesto']);

// retornar PDF con los totales, se envía el ID año
Route::get('/admin/p/generador/pdf/totales/{anio}', [ReportesPresupuestoUnidadController::class,'generarTotalesPdfPresupuesto']);
// retorna Excel con los totales, se envía el ID año
Route::get('/admin/p/generador/excel/totales/{anio}', [ReportesPresupuestoUnidadController::class,'generarTotalesExcelPresupuesto']);
// retorna PDF con el consolidado, todos los presupuestos ya están aprobados
Route::get('/admin/p/generador/consolidado/pdf/presupuesto/{anio}', [ReportesPresupuestoUnidadController::class,'generarConsolidadoPdfPresupuesto']);
// retorna Excel con el consolidado, todos los presupuestos ya están aprobados
Route::get('/admin/p/generador/excel/consolidado/{anio}', [ReportesPresupuestoUnidadController::class,'generarConsolidadoExcelPresupuesto']);

// retorna PDF con los totales por unidad que se seleccionó
Route::get('/admin/p/generador/pdf/porunidad/{anio}/{unidad}', [ReportesPresupuestoUnidadController::class, 'generarTotalPdfPorUnidades']);
// reporte PDF solo para 1 unidad, ya que lleva columna precio unitario
Route::get('/admin/p/generador/pdf/unaunidad/{anio}/{unidad}', [ReportesPresupuestoUnidadController::class, 'generarPdfSoloUnaUnidad']);


// ver si existe el presupuesto departamento y su año
Route::post('/admin/p/ver/unidad/tiene/presupuesto/anio', [ConfiguracionPresupuestoUnidadController::class,'verificarSiExistePresupuesto']);
// ver si existe el presupuesto departamento y su año para x unidades que se envía
Route::post('/admin/p/ver/unidades/tiene/presupuesto/anio', [ConfiguracionPresupuestoUnidadController::class,'verificarSiExistePresupuestoTodoDepa']);

// retorna Excel con los totales por unidad que se seleccionó
Route::get('/admin/p/generador/excel/porunidad/{anio}/{unidad}', [ReportesPresupuestoUnidadController::class, 'generarTotalExcelPorUnidades']);
// retorna Excel con los totales por solo una unidad
Route::get('/admin/p/generador/excel/unaunidad/{anio}/{unidad}', [ReportesPresupuestoUnidadController::class, 'generarTotalExcelSoloUnidad']);


// * CUENTAS UNIDADES

// retorna vista con las cuentas de unidades
Route::get('/admin/p/cuentas/unidades/index', [CuentaUnidadController::class,'indexCuentasUnidades'])->name('p.admin.cuentas.unidades.index');
// retorna tabla con las cuentas de unidades
Route::get('/admin/p/cuentas/unidades/tabla', [CuentaUnidadController::class,'tablaCuentasUnidades']);
// crear las cuentas unidades para todos los presupuesto aprobado
Route::post('/admin/p/registrar/cuentas/unidades', [CuentaUnidadController::class,'registrarCuentasUnidades']);
// cuando hace falta un departamento nuevo y ya se creó cuenta unidad anteriormente se hara manual
Route::post('/admin/p/registrar/cuentas/unidad/manual', [CuentaUnidadController::class,'registrarCuentasUnidadManual']);





// * REQUERIMIENTOS DE UNA UNIDAD

// retornar vista para poder elegir año de presupuesto para solicitar requerimiento
Route::get('/admin/p/anio/presupuesto/requerimiento/index', [RequerimientosUnidadController::class,'indexBuscarAñoPresupuesto'])->name('admin.p.unidad.requerimientos.index');

// verifica si puede hacer requerimientos según año de presupuesto
Route::post('/admin/p/anio/permiso/requerimiento', [RequerimientosUnidadController::class,'verificarEstadoPresupuesto']);
// retorna vista donde están los requerimientos por año
Route::get('/admin/p/requerimientos/vista/{idanio}', [RequerimientosUnidadController::class, 'indexRequerimientosUnidades']);
// retorna tabla donde están los requerimientos por año
Route::get('/admin/p/requerimientos/tabla/{idanio}', [RequerimientosUnidadController::class, 'tablaRequerimientosUnidades']);
// visualizar MODAL DE SALDOS para unidades. se recibe id p_presup_unidad
Route::get('/admin/p/modal/saldo/unidad/{idpresup}', [RequerimientosUnidadController::class,'infoModalSaldoUnidad']);

// VER ESTADOS DE LOS MATERIALES, TODOS SU PROCESO COMO VA
Route::get('/admin/p/modal/material/estados/{idrequisicion}', [RequerimientosUnidadController::class,'infoModalEstadoMaterial']);




// * MOVIMIENTOS DE CUENTA PARA UNIDAD

// retorna vista para movimientos de cuenta para unidad
Route::get('/admin/p/requerimientos/movicuentaunidad/index/{idpresup}', [MovimientosUnidadControlles::class, 'indexMovimientoCuentaUnidad']);
// retorna tabla para movimientos de cuenta para unidad
Route::get('/admin/p/requerimientos/movicuentaunidad/tabla/{idpresup}', [MovimientosUnidadControlles::class, 'tablaMovimientoCuentaUnidad']);
// información de saldos para cambiar cuenta de unidades
Route::post('/admin/p/movicuentaunidad/informacion',  [MovimientosUnidadControlles::class,'informacionMoviCuentaUnidad']);
// al mover select de movimiento cuenta unidad, retorna saldo restante del obj seleccionado
Route::post('/admin/p/movicuentaunidad/informacion/saldo',  [MovimientosUnidadControlles::class,'infoSaldoRestanteCuentaUnidad']);
// registrar un nuevo movimiento de cuenta unidad por jefe de unidad
Route::post('/admin/p/registrar/movimiento/cuentaunidad',  [MovimientosUnidadControlles::class,'nuevaMoviCuentaUnidad']);


// retorna vista con los historicos movimientos cuenta unidad por ID PRESUP UNIDAD
Route::get('/admin/p/movicuentaunidad/historico/{id}', [MovimientosUnidadControlles::class,'indexMoviCuentaUnidadHistorico']);
// retorna tabla con los historicos movimientos cuenta unidad por ID PRESUP UNIDAD
Route::get('/admin/p/movicuentaunidad/tablahistorico/{id}', [MovimientosUnidadControlles::class,'tablaMoviCuentaUnidadHistorico']);

// ver los movimientos historicos para que jefe presupuesto los apruebe
Route::get('/admin/p/movicuentaunidad/presupuesto/index', [MovimientosUnidadControlles::class,'indexMovimientoCuentaUnidadTodos'])->name('p.admin.movimientos.pendientes.historicos.unidades.index');
// ver tabla de los movimientos historicos de cuenta unidad, jefatura presupuesto para aprobar
Route::get('/admin/p/movicuentaunidad/presupuesto/tabla', [MovimientosUnidadControlles::class,'tablaMovimientoCuentaUnidadTodos']);
// información para jefe de presupuesto para que revise un movimiento de cuenta unidad
Route::post('/admin/p/movimientohistorico/unidad/verificar/informacion',  [MovimientosUnidadControlles::class,'infoHistoricoMovimientoUnidadParaAutorizar']);
// borrar movimiento de cuenta para unidades
Route::post('/admin/p/movimientohistorico/unidades/denegar/borrar',  [MovimientosUnidadControlles::class,'denegarBorrarMovimientoCuentaUnidad']);
// autorizar movimiento de cuenta unidad
Route::post('/admin/p/movimientohistorico/unidades/autorizar',  [MovimientosUnidadControlles::class,'autorizarMovimientoCuentaUnidad']);

// ver los movimientos de cuenta unidad aprobados

// año para buscar movimientos de cuenta unidad aprobados
Route::get('/admin/p/movicuentaunidad/aprobados/presupuesto/anio', [MovimientosUnidadControlles::class,'indexMovimientoCuentaUnidadAprobadosAnio'])->name('p.admin.movimientos.aprobados.historicos.unidades.index');


Route::get('/admin/p/movicuentaunidad/aprobados/presupuesto/index/{idanio}', [MovimientosUnidadControlles::class,'indexMovimientoCuentaUnidadAprobados']);
// ver tabla de los movimientos historicos aprobados de cuenta unidad
Route::get('/admin/p/movicuentaunidad/aprobados/presupuesto/tabla/{idanio}', [MovimientosUnidadControlles::class,'tablaMovimientoCuentaUnidadAprobados']);
// descargar documento reforma de movimiento cuenta unidad
Route::get('/admin/p/movicuentaunidad/bajar/reforma/{id}',  [MovimientosUnidadControlles::class,'descargarReformaMovimientoUnidades']);
// guardar documento reforma para movimiento cuenta unidades
Route::post('/admin/p/movicuentaunidad/documento/guardar',  [MovimientosUnidadControlles::class,'guardarDocumentoReformaMoviUnidad']);


// * REQUISICIONES PARA UNIDAD

// busca materiales para pedir requisición unidades
Route::post('/admin/buscar/material/requisicion/unidad',  [RequerimientosUnidadController::class,'buscadorMaterialRequisicionUnidad']);
// registrar una nueva requisición para unidades
Route::post('/admin/p/regisrar/requisicion/unidades', [RequerimientosUnidadController::class, 'nuevoRequisicionUnidades']);
// borrar requisición de unidades
Route::post('/admin/p/requisicion/unidad/borrar/todo', [RequerimientosUnidadController::class, 'borrarRequisicionUnidades']);
// informacion de una requisicion unidad
Route::post('/admin/p/requisicion/unidad/informacion', [RequerimientosUnidadController::class, 'informacionRequisicionUnidad']);
// modificar las requisiciones de unidad
Route::post('/admin/p/requisicion/unidad/editar', [RequerimientosUnidadController::class, 'editarRequisicionUnidad']);

// cancelar material de requisicion unidad detalle
Route::post('/admin/p/requisicion/unidad/material/cancelar', [RequerimientosUnidadController::class, 'cancelarMaterialRequisicionUnidad']);








// ***** LISTADO DE AGRUPADOS PARA UCP   *****

// seleccionar fecha para ver los agrupados de esa fecha
Route::get('/admin/p/requerimientos/fecha/agrupados', [CotizacionesUnidadController::class,'indexListaAgrupadoAnios'])->name('admin.fecha.de.agrupapos.ucp');


// retorna vista de requerimientos pendientes unidad
Route::get('/admin/p/requerimientos/pendiente/unidad/index/{idanio}', [CotizacionesUnidadController::class,'indexListarRequerimientosPendienteUnidad']);
// retorna tabla de requerimientos pendientes para unidad
Route::get('/admin/p/requerimientos/pendiente/unidad/tabla/{idanio}', [CotizacionesUnidadController::class,'indexTablaListarRequerimientosPendienteUnidad']);

// Denegar requisicion, ejemplo cuando el concejo lo deniega
Route::post('/admin/p/denegar/completa/requisicion/agrupada', [RequerimientosUnidadController::class,'denegarAgrupadoPorUCP']);


// CREAR LA COTIZACION
// información de requisición para hacer la cotizacion
Route::post('/admin/p/requerimientos/listado/cotizar/info', [CotizacionesUnidadController::class, 'informacionRequerimientoCotizarInfo']);
// se envía los ID requi_detalle para verificar y retornar información de lo que se cotizara
Route::post('/admin/p/requerimientos/unidad/verificar', [CotizacionesUnidadController::class, 'verificarRequerimientoUnidadAcotizar']);
// guardar cotización para requerimiento de unidad
Route::post('/admin/p/requerimientos/cotizacion/unidad/guardar', [CotizacionesUnidadController::class, 'guardarNuevaCotizacionRequeriUnidad']);





// * COTIZACIONES PARA UNIDAD

// busqueda de año para ver cotizaciones pendiente de unidad
Route::get('/admin/p/cotizacion/unidad/pendiente/anio', [CotizacionesUnidadController::class,'indexAnioCotiUnidadPendiente'])->name('cotizaciones.pendientes.unidad.index');

// retorna vista con las cotizaciones pendientes
Route::get('/admin/p/cotizacion/unidad/pendiente/index/{idanio}', [CotizacionesUnidadController::class,'indexCotizacionesUnidadesPendiente']);
// COTIZACIONES PENDIENTE UCP
Route::get('/admin/p/cotizacion/unidad/pendiente/tabla/{idanio}', [CotizacionesUnidadController::class,'indexCotizacionesUnidadesPendienteTabla']);
// ver detalle de una cotización para unidades
Route::get('/admin/p/cotizacion/unidad/vistadetalle/{id}', [CotizacionesUnidadController::class,'indexCotizacionUnidadDetalle']);
// autorizar cotizacion
Route::post('/admin/p/cotizacion/unidad/autorizar',  [CotizacionesUnidadController::class,'autorizarCotizacionUnidad']);


// - AUTORIZADAS

// busqueda de año para ver cotizaciones autorizadas de unidad
Route::get('/admin/p/cotizacion/unidad/autorizadas/anio', [CotizacionesUnidadController::class,'indexAnioCotiUnidadAutorizada'])->name('cotizaciones.autorizadas.unidad.index');
// retorna vista con las cotizaciones autorizaciones para unidades
Route::get('/admin/p/cotizacion/unidad/autorizadas/index/{idanio}', [CotizacionesUnidadController::class,'indexCotizacionesUnidadesAutorizadas']);
// retorna tabla de cotizaciones unidad autorizadas
Route::get('/admin/p/cotizacion/unidad/autorizadas/tabla/{idanio}', [CotizacionesUnidadController::class,'tablaCotizacionesUnidadesAutorizadas']);
// DENEGAR COTIZACION POR JEFA DE UACI
Route::post('/admin/p/cotizacion/denegar',  [CotizacionesUnidadController::class,'denegarCotizacionUnidad']);
// vista de cotización detalle para procesadas o denegadas
Route::get('/admin/p/cotizacion/unidad/detalle/{idcoti}', [CotizacionesUnidadController::class,'vistaDetalleCotizacionUnidad']);


// - DENEGADAS
// busqueda de año para ver cotizaciones denegadas de unidad
Route::get('/admin/p/cotizacion/unidad/denegadas/anio', [CotizacionesUnidadController::class,'indexAnioCotiUnidadDenegadas'])->name('cotizaciones.denegadas.unidad.index');
// retorna vista con las cotizaciones denegadas para unidades
Route::get('/admin/p/cotizacion/unidad/denegadas/index/{idanio}', [CotizacionesUnidadController::class,'indexCotizacionesUnidadesDenegadas']);
// retorna tabla de cotizaciones unidad denegadas
Route::get('/admin/p/cotizacion/unidad/denegadas/tabla/{idanio}', [CotizacionesUnidadController::class,'tablaCotizacionesUnidadesDenegadas']);

// retorna ACTA que se subio al denegar una cotizacion
Route::get('/admin/p/cotizacion/descargar/acta/{id}' , [CotizacionesUnidadController::class, 'descargarActaCotizacionDenegada']);




// ORDENES DE COMPRA PARA UNIDADES

// crear una nueva orden de compra para unidades
Route::post('/admin/p/ordencompra/unidad/generar',  [OrdenCompraUnidadController::class,'generarOrdenCompraUnidades']);
// generar PDF de orden de compra de unidades y variable {cantidad} es # de material por hoja
Route::get('/admin/p/ordencompra/unidad/pdf/{id}/{cantidad}', [OrdenCompraUnidadController::class,'vistaPdfOrdenUnidad']);

// * ORDENES DE COMPRAS PROCESADAS

Route::get('/admin/p/ordenes/comprasunidades/aprobadas/anio', [OrdenCompraUnidadController::class,'vistaAñoOrdenesComprasUnidadesAprobadas'])->name('admin.ordenes.compra.unidades.procesadas');
// retorna vista con las ordenes de compras para unidades
Route::get('/admin/p/ordenes/comprasunidades/aprobadas/index/{idanio}', [OrdenCompraUnidadController::class,'indexOrdenesComprasAprobadasUnidades']);
// retorna tabla con las ordenes de compras para unidades
Route::get('/admin/p/ordenes/comprasunidades/aprobadas/tabla/{idanio}', [OrdenCompraUnidadController::class,'tablaOrdenesComprasAprobadasUnidades']);





// vista detalle de una cotización unidad, esto se mira desde las ordenes de compra
Route::get('/admin/p/detalle/ordencompra/coti/unidad/{idorden}', [OrdenCompraUnidadController::class,'vistaDetalleCotizacionUnidadOrden']);

// ver materiales en un MODAL de presupuesto unidad para ver que puede pedir en Requerimientos
Route::get('/admin/p/listado/materiales/presupuestounidad/{id}', [MovimientosUnidadControlles::class,'verCatalogoMaterialRequisicionUnidad']);


// generar acta de una orden de compra para unidad
Route::post('/admin/p/ordenes/unidad/generar/acta',  [OrdenCompraUnidadController::class,'generarActadeCompraUnidades']);
// generar PDF de la acta de compra para unidad
Route::get('/admin/p/ordenes/acta/unidad/reporte/{id}', [OrdenCompraUnidadController::class,'reporteActaGeneradaUnidades']);


// * MOVIMIENTO DE CUENTA DE UNIDAD PARA SOLICITAR MATERIAL QUE NO ESTA EN EL PRESUPUESTO

// retorna vista para ver materiales solicitados y se quita dinero de un código
Route::get('/admin/p/movicuentaunidad/solicitud/material/{idpresup}', [MovimientosUnidadControlles::class,'indexSolicitudMovimientoUnidadMaterial']);
// retorna tabla para ver materiales solicitados y se quita dinero de un código
Route::get('/admin/p/movicuentaunidad/solicitud/materialtabla/{idpresup}', [MovimientosUnidadControlles::class,'tablaSolicitudMovimientoUnidadMaterial']);
// buscador de material de solicitud
Route::post('/admin/p/buscar/material/solicitud/unidad',  [MovimientosUnidadControlles::class,'buscadorMaterialSolicitudUnidad']);
// llenar select para obtener obj específico a descontar para obtener nuevo material
Route::post('/admin/p/select/objespecifico/solicitud',  [MovimientosUnidadControlles::class,'buscadorObjEspeciSolicitudMaterial']);
// obtener saldo restando MENOS el saldo retenido de un obj especifico
Route::post('/admin/p/select/obj/saldos/solicitud/material',  [MovimientosUnidadControlles::class,'infoSaldoRestanteSolicitudMaterial']);
// guardar solicitud de materiales
Route::post('/admin/p/guardar/solicitud/material',  [MovimientosUnidadControlles::class,'guardarSolicitudMaterialUnidad']);


// PRESUPUESTO REVISA SOLICITUDES DE MATERIAL QUE NO DEJO EN PRESUPUESTO

// retorna vista para ver materiales solicitados y se quita dinero de un código
Route::get('/admin/p/revision/solicitud/material/unidades', [MovimientosUnidadControlles::class,'indexRevisionSolicitudMaterialUnidad'])->name('p.admin.nuevas.solicitudes.materiales');
// retorna tabla para ver materiales solicitados y se quita dinero de un código
Route::get('/admin/p/revision/solicitud/material/unidades/tabla', [MovimientosUnidadControlles::class,'tablaRevisionSolicitudMaterialUnidad']);
// revision por presupuesto de material solicitado por una unidad
Route::post('/admin/p/solicitud/material/revision/presupuesto',  [MovimientosUnidadControlles::class,'informacionSolicitudMaterialPresupuesto']);
// borrar solicitud material solicitado
Route::post('/admin/p/borrar/solicitud/material/presupuesto',  [MovimientosUnidadControlles::class,'borrarSolicitudMaterialPresupuesto']);
// aprobar solicitud de material solicitado y sumar a obj y descontar a otro obj
Route::post('/admin/p/aprobar/solicitud/material/presupuesto',  [MovimientosUnidadControlles::class,'aprobarSolicitudMaterialPresupuesto']);


// SOLICITUDES APROBADAS DE MATERIAL PARA UNIDADES

// ver año de solicitud aprobada

Route::get('/admin/p/anio/aprobadas/material/solicitudes', [MovimientosUnidadControlles::class,'vistaAñoPresupuestoMaterialAprobados'])->name('p.admin.nuevas.solicitudes.materiales.aprobados');

Route::get('/admin/p/aprobados/solicitud/material/{idanio}', [MovimientosUnidadControlles::class,'indexRevisionSolicitudMaterialAprobada']);
Route::get('/admin/p/aprobados/solicitud/material/tabla/{idanio}', [MovimientosUnidadControlles::class,'tablaRevisionSolicitudMaterialUnidadAprobados']);
Route::post('/admin/p/aprobados/solicitud/material/informacion', [MovimientosUnidadControlles::class,'presupuestoMaterialAprobadosInformacion']);


// DESCARGOS DIRECTOS
Route::get('/admin/descargos/directos/index', [DescargosDirectosController::class,'indexDescargosDirectos'])->name('crear.descargos.directos');
Route::post('/admin/verificar/tipodescargo/directo/informacion', [DescargosDirectosController::class,'tipoDescargoDirectoInformacion']);

// retorna los objeto especificos del proyecto seleccionado
Route::post('/admin/obj/proyecto/descargodirecto/informacion', [DescargosDirectosController::class,'objEspecificosSegunProyecto']);

// retorna saldo restante (- el saldo retenido) de cuenta proy
Route::post('/admin/obj/cuentaproy/saldo/descargodirecto/info', [DescargosDirectosController::class,'infoCuentaProySaldos']);

// guardar un descargo directo PARA PROYECTO
Route::post('/admin/guardar/descargodirecto/tipo/proyecto', [DescargosDirectosController::class,'guardarDescargoDirectoProyecto']);

// retorna el departamento con el monto disponible
Route::post('/admin/unidades/descargodirecto/anio/presupuesto', [DescargosDirectosController::class,'buscarUnidadSegunAnio']);

// retorna saldo restante (- el saldo retenido) de cuenta unidad
Route::post('/admin/obj/cuentaunidad/saldo/descargodirecto/info', [DescargosDirectosController::class,'infoCuentaUnidadSaldos']);

// guardar un descargo directo PARA PROVEEDOR
Route::post('/admin/guardar/descargodirecto/tipo/proveedor', [DescargosDirectosController::class,'guardarDescargoDirectoProveedor']);

// guardar un descargo directo PARA CONTRIBUCION
Route::post('/admin/guardar/descargodirecto/tipo/contribucion', [DescargosDirectosController::class,'guardarDescargoDirectoContribucion']);

// vista historial para descargos directos
Route::get('/admin/descargos/directos/historial/index', [DescargosDirectosController::class,'indexDescargosDirectosHistorial'])->name('historial.descargos.directos');
Route::get('/admin/descargos/directos/historial/tabla', [DescargosDirectosController::class,'tablaDescargosDirectosHistorial']);
Route::post('/admin/descargos/directos/historial/informacion', [DescargosDirectosController::class,'informacionDescargosDirectosHistorial']);

// generar pdf para que jefe de cada unidad lo pueda visualizar los materiales que ha solicitado en un nuevo requerimiento
Route::get('/admin/p/generador/pdf/requisicion/{id}', [CotizacionesUnidadController::class,'pdfRequerimientoUnidadMateriales']);

// generar pdf con el catalogo de materiales de cada unidad. Esto cada unidad puede sacarlo
Route::get('/admin/p/generador/pdf/catalogomaterial/unidad/{id}', [ReportesUnidadController::class,'pdfCatalogoMaterialesUnidad']);

// vista para elegir departamento y generar reporte de movimientos de cuenta
Route::get('/admin/p/vista/unidades/movimientosunidad/index', [ReportesUnidadController::class,'indexVistaReporteMovimientoUnidad'])->name('p.generar.reportes.presupuesto..movimientos.unidad');

// generar reporte PDF de movimientos de cuentas de una o varias unidades
Route::get('/admin/p/reporte/pdf/movimientosunidad/jefepresupuesto/{anio}/{unidad}', [ReportesUnidadController::class,'generarReportePDFMovimientoDeCuentas']);


//REPORTES PARA JEFE UACI

// retorna vista para generar reportes UACI de unidades
Route::get('/admin/p/reportes/unidad/uaci/index', [ConfiguracionPresupuestoUnidadController::class,'indexReporteUaciUnidad'])->name('p.generar.reportes.uaci.unidad');
// retornar PDF de plan de compra anual, se envía el ID año
Route::get('/admin/p/generador/pdf/plan/{anio}', [ReportesPresupuestoUnidadController::class,'generarPlanPdfUaci']);



// UCP

// SELECCIONAR AÑO PARA VER AGRUPADOS DENEGADOS
Route::get('/admin/p/buscarfecha/requerimientos/denegados/unidadindex', [CotizacionesUnidadController::class,'indexFechaRequerimientosDenegadosUnidades'])->name('admin.listar.requerimientos.denegados.index');
// VISTA DE AGRUPADOS DENEGADOS
Route::get('/admin/p/requerimiento/denegados/listado/{idanio}', [CotizacionesUnidadController::class,'indexRequerimientosDenegadosUnidades']);
// TABLA DE AGRUPADOS DENEGADOS
Route::get('/admin/p/requerimiento/denegados/listado/denegados/{idanio}', [CotizacionesUnidadController::class,'tablaRequerimientosDenegadosUnidades']);

// este seria la vista para ver los materiales detallados
Route::get('/admin/p/reque/dene/listado/materiales/{idrequi}', [CotizacionesUnidadController::class,'indexRequeDeneUnidadesMateriales']);
// este seria la tabla para ver los materiales detallados
Route::get('/admin/p/reque/dene/listado/materiales/tabla/{idrequi}', [CotizacionesUnidadController::class,'indexRequeDeneUnidadesMaterialesDetalle']);





//**********   CONSOLIDADOR *************
Route::get('/admin/consolidador/requerimientos/pendientes', [ConsolidadorController::class,'indexRequerimientosPendientes'])->name('requerimientos.pendientes.consolidadoras');

Route::get('/admin/consolidador/requerimientos/pendientes/vista/{idanio}', [ConsolidadorController::class,'vistaRequerimientosPendientes']);
Route::get('/admin/consolidador/requerimientos/pendientes/tabla/{idanio}', [ConsolidadorController::class,'tablaRequerimientosPendientes']);

// se obtiene listado para meter al select de materiales ya agrupados
Route::post('/admin/consolidatos/listado/ordenado/paraselect', [ConsolidadorController::class,'listadoAgrupadosParaSelect']);


// informacion para mostrar detalle de una requisicion en el modal
Route::get('/admin/consolidador/info/requisicion/detalle/{idrequi}', [ConsolidadorController::class,'informacionDetalleRequisicion']);
Route::post('/admin/consolidador/registar/agrupados', [ConsolidadorController::class,'registrarAgrupados']);

// VER LISTA DE YA AGRUPADOS
Route::get('/admin/consolidador/listado/agrupados', [ConsolidadorController::class,'indexListaAgrupados'])->name('requerimientos.consolidador.agrupados');
Route::get('/admin/consolidador/listado/agrupados/tabla', [ConsolidadorController::class,'tablaListaAgrupados']);

// generar PDF de los materiales agrupados
Route::get('/admin/consolidador/generar/pdf/{idagrupado}', [ConsolidadorController::class,'generarPdfAgrupado']);

// borrar agrupacion
Route::post('/admin/consolidador/borrar/agrupado', [ConsolidadorController::class,'borrarAgrupado']);

// informacion del agrupado
Route::post('/admin/consolidador/informacion/agrupada', [ConsolidadorController::class, 'informacionAgrupado']);

// editar unos campos del agrupado
Route::post('/admin/consolidador/actualizar/agrupado', [ConsolidadorController::class, 'actualizarAgrupado']);



// --- REFERENCIAS ---

// retorna vista de años para presupuesto
Route::get('/admin/referencias/orden/index', [ReferenciasController::class,'indexReferencia'])->name('admin.referencias.orden.index');
Route::get('/admin/referencias/orden/tabla', [ReferenciasController::class,'tablaReferencia']);
Route::post('/admin/referencias/orden/nuevo', [ReferenciasController::class, 'nuevaReferencia']);
Route::post('/admin/referencias/orden/informacion', [ReferenciasController::class, 'informacionReferencia']);
Route::post('/admin/referencias/orden/editar', [ReferenciasController::class, 'editarReferencia']);


// informacion de un proyecto aprobado del presupuesto unidad
Route::post('/admin/p/proyectos/informacion/deaprobados', [ConsolidadorController::class, 'informacionPresupuestoUniProyectos']);




// SECRETARIA DESPACHO
//Calendario
Route::get('/admin/secretaria/calendario/index', [ReferenciasController::class,'indexSecreCalendario'])->name('sidebar.secretaria.calendario');
Route::get('/admin/secretaria/calendario/informacion', [ReferenciasController::class, 'getRegistrosPorDia']);
Route::post('/admin/secretaria/calendario/nuevo', [ReferenciasController::class, 'guardarRegistro']);
//Transporte
Route::get('/admin/secretaria/transporte/index', [ReferenciasController::class,'indexSecreTransporte'])->name('sidebar.secretaria.transporte');
Route::get('/admin/secretaria/transporte/tabla', [ReferenciasController::class,'tablaSecreTransporte']);
Route::post('/admin/secretaria/transporte/nuevo', [ReferenciasController::class,'guardarSecreTransporte']);
Route::post('/admin/secretaria/transporte/borrar', [ReferenciasController::class,'borrarSecreTransporte']);
Route::post('/admin/secretaria/transporte/informacion', [ReferenciasController::class,'informacionSecreTransporte']);
Route::post('/admin/secretaria/transporte/editar', [ReferenciasController::class,'editarSecreTransporte']);

//Reportes de Transporte
Route::get('/admin/reporte/despacho/transporte/{desde}/{hasta}', [ReferenciasController::class,'reporteDespachoTransporte']);

//Solicitudes de Despacho
Route::get('/admin/secretaria/despacho/index', [ReferenciasController::class,'indexSecreDespacho'])->name('sidebar.secretaria.despacho');
Route::get('/admin/secretaria/despacho/tabla', [ReferenciasController::class,'tablaSecreDespacho']);
Route::post('/admin/secretaria/despacho/nuevo', [ReferenciasController::class,'guardarSecreDespacho']);
Route::post('/admin/secretaria/despacho/borrar', [ReferenciasController::class,'borrarSecreDespacho']);
Route::post('/admin/secretaria/despacho/informacion', [ReferenciasController::class,'informacionSecreDespacho']);
Route::post('/admin/secretaria/despacho/editar', [ReferenciasController::class,'editarSecreDespacho']);

//Reportes de solicitudes de despacho
Route::get('/admin/secretaria/reportes/index', [ReferenciasController::class,'indexReportes'])->name('sidebar.reportes.despacho');
Route::get('/admin/reporte/despacho/{desde}/{hasta}/{tipo}', [ReferenciasController::class,'reporteDespachoSecretaria']);





Route::get('/admin/rrhh/hojadatos/index', [ReferenciasController::class,'indexRRHHDatosHoja'])->name('sidebar.rrhh.actualizacion.datos');
Route::get('/admin/rrhh/hojadatos/tabla', [ReferenciasController::class,'tablaRRHHDatosHoja']);
Route::get('/admin/rrhh/hojadatos/reporte/{id}', [ReferenciasController::class,'RRHHDatosReporte']);
Route::post('/admin/rrhh/hojadatos/borrarregistro', [ReferenciasController::class,'RRHHDatosBorrar']);


// RECURSOS HUMANOS - PUBLICO
Route::get('/actualizacion/datos', [RecursosHumanosController::class,'vistaIngresoDatos']);
Route::post('/admin/actualizacion/datos/guardar', [RecursosHumanosController::class,'guardarIngresoDatos']);



// ** SOLICITUDES IT ***

// retornar vista para poder elegir año de de solicitudes IT
Route::get('/admin/p/solicitudesit/index', [SolicitudesITController::class,'indexSolicitudesIT'])->name('admin.p.solicitudesit.bloquefecha');
Route::get('/admin/p/solicitudesit/vistaanio/{idanio}', [SolicitudesITController::class, 'indexListadoSolicitudesIT']);
Route::post('/admin/p/solicitudesit/guardardatos', [SolicitudesITController::class, 'guardarDatosSolicitudesIT']);


// ADMINISTRAR SOLICITUD IT
Route::get('/admin/solicitudit/administracion', [SolicitudesITController::class,'indexSolicitudesITControl'])->name('admin.solicitudit.control.index');
Route::post('/admin/solicitudesit/listadounidades', [SolicitudesITController::class, 'listadoSolicitudeITBloqueFecha']);
Route::get('/admin/solicitudit/administracion/tablafinal/{idfila}', [SolicitudesITController::class,'indexSolicitudTablaFinal']);
Route::post('/admin/solicitudesit/fechalimite', [SolicitudesITController::class, 'guardarFechaLimiteSolicitudIT']);


// ** BODEGA ***

// Administrar Materiales de bodega
Route::get('/admin/bodega/materiales/index', [BMaterialesController::class,'indexBodegaMateriales'])->name('sidebar.bodega.materiales');
Route::get('/admin/bodega/materiales/tabla/index', [BMaterialesController::class,'tablaMateriales']);
Route::post('/admin/bodega/materiales/nuevo', [BMaterialesController::class, 'nuevoMaterial']);
Route::post('/admin/bodega/materiales/informacion', [BMaterialesController::class, 'informacionMaterial']);
Route::post('/admin/bodega/materiales/editar', [BMaterialesController::class, 'editarMaterial']);

// detalle de materiales -> Ver cantidad desglosado actual de cada lote
Route::get('/admin/bodega/materialesdetalle/vista/index/{id}', [BMaterialesController::class,'indexDetalleMaterialCantidad']);
Route::get('/admin/bodega/materialesdetalle/tabla/index/{id}', [BMaterialesController::class,'tablaDetalleMaterialCantidad']);


// registrar
Route::get('/admin/bodega/entradasregistro/index', [BMaterialesController::class,'indexEntradasRegistro'])->name('sidebar.bodega.entradasregistro');
Route::post('/admin/bodega/buscar/producto',  [BMaterialesController::class,'buscarProducto']);
Route::post('/admin/bodega/registrar/productos',  [BMaterialesController::class,'registrarProductos']);
// agregar productos extras a un lote
Route::post('/admin/bodega/registrar/productosextras',  [BMaterialesController::class,'registrarProductosExtras']);







// Realizar Solicitud por parte de unidad
Route::get('/admin/bodega/nuevasolicitud/index', [BSolicitudesController::class,'indexNuevaSolicitud'])->name('sidebar.bodega.nueva.solicitudunidad');
Route::post('/admin/bodega/registrar/nuevasolicitud',  [BSolicitudesController::class,'registrarSolicitudUnidad']);

// Ver mis solicitudes como unidad
Route::get('/admin/bodega/missolicitudes/index', [BSolicitudesController::class,'indexMisSolicitudUnidad'])->name('sidebar.bodega.mis.solicitudunidad');
Route::get('/admin/bodega/missolicitudes/tabla', [BSolicitudesController::class,'tablaMisSolicitudUnidad']);
// detalle
Route::get('/admin/bodega/missolicitudes/detalle/index/{id}', [BSolicitudesController::class,'indexDetalleMisSolicitudUnidad']);
Route::get('/admin/bodega/missolicitudes/detalle/tabla/{id}', [BSolicitudesController::class,'tablaDetalleMisSolicitudUnidad']);

// asignar usuario bodega a su objeto especifico correspondiente
Route::get('/admin/bodega/usuario/objespecifico/index', [PermisoController::class,'indexVistaUsuarioBodegaObjEspecifico'])->name('admin.usuario.bodega.objespecifico.index');
Route::get('/admin/bodega/usuario/objespecifico/tabla', [PermisoController::class,'tablaVistaUsuarioBodegaObjEspecifico']);
Route::post('/admin/bodega/usuario/objespecifico/borrar', [PermisoController::class, 'borrarUsuarioBodegaObjEspecifico']);
Route::post('/admin/bodega/usuario/objespecifico/registrar', [PermisoController::class, 'registrarUsuarioBodegaObjEspecifico']);

// HISTORIAL
// ---- ENTRADAS
Route::get('/admin/bodega/historial/entrada/index', [BHistorialController::class,'indexHistorialEntradas'])->name('sidebar.bodega.historial.entradas');
Route::get('/admin/bodega/historial/entrada/tabla', [BHistorialController::class,'tablaHistorialEntradas']);
Route::get('/admin/bodega/historial/entradadetalle/index/{id}', [BHistorialController::class,'indexHistorialEntradasDetalle']);
Route::get('/admin/bodega/historial/entradadetalle/tabla/{id}', [BHistorialController::class,'tablaHistorialEntradasDetalle']);
// vista para ingresar nuevo producto al lote existente
Route::get('/admin/bodega/historial/nuevoingresoentradadetalle/index/{id}', [BHistorialController::class,'indexNuevoIngresoEntradaDetalle']);

// Informacion de la entrada para modificar fecha, etc
Route::post('/admin/bodega/historial/entrada/datosinformacion', [BHistorialController::class,'informacionDatosEntrada']);
Route::post('/admin/bodega/historial/entrada/guardarinformacion', [BHistorialController::class,'guardarDatosEntrada']);







// ---- SALIDAS SOLICITUD
Route::get('/admin/bodega/historial/salidas/index', [BHistorialController::class,'indexHistorialSalidas'])->name('sidebar.bodega.historial.salidas');
Route::get('/admin/bodega/historial/salidas/tabla', [BHistorialController::class,'tablaHistorialSalidas']);
Route::get('/admin/bodega/historial/salidadetalle/index/{id}', [BHistorialController::class,'indexHistorialSalidasDetalle']);
Route::get('/admin/bodega/historial/salidadetalle/tabla/{id}', [BHistorialController::class,'tablaHistorialSalidasDetalle']);
Route::post('/admin/bodega/historial/salidadetalle/borraritem', [BHistorialController::class,'salidaDetalleBorrarItem']);

// --- HISTORIAL - SALIDAS MANUAL
Route::get('/admin/bodega/historial/salidasmanual/index', [BHistorialController::class,'indexHistorialSalidasManual'])->name('sidebar.bodega.historial.salidas.manual');
Route::get('/admin/bodega/historial/salidasmanual/tabla', [BHistorialController::class,'tablaHistorialSalidasManual']);
Route::get('/admin/bodega/historial/salidamanualdetalle/index/{id}', [BHistorialController::class,'indexHistorialSalidasManualDetalle']);
Route::get('/admin/bodega/historial/salidamanualdetalle/tabla/{id}', [BHistorialController::class,'tablaHistorialSalidasManualDetalle']);


// SALIDAS MANUAL BODEGA
Route::get('/admin/bodega/salidasmanual/index', [BMaterialesController::class,'indexSalidasManual'])->name('sidebar.bodega.salidasmanual');
Route::post('/admin/bodega/salidasmanual/registrar',  [BMaterialesController::class,'registrarSalidaManual']);
Route::post('/admin/bodega/salidasmanual/borraritem', [BMaterialesController::class,'salidaManualDetalleBorrarItem']);


// REPORTES

// SOLICITUD DE LA UNIDAD SOLICITANTE
Route::get('/admin/bodega/reporte/solitudcompleta/{id}', [BReportesController::class,'reporteSolicitudCompleta']);
// SOLICITUD DE PARTE ENCARGADO BODEGA -> COMPLETO
Route::get('/admin/bodega/reporte/encargadobodega/completa/{id}', [BReportesController::class,'reporteEncargadoBodegaCompleta']);
// SOLICITUD DE PARTE ENCARGADO BODEGA -> ITEM
Route::get('/admin/bodega/reporte/encargadobodega/item/{id}', [BReportesController::class,'reporteEncargadoBodegaItem']);


// VISTA PARA MODIFICAR NOMBRE PARA REPORTE
Route::get('/admin/bodega/extras/nombrereporte', [BReportesController::class,'vistaConfigurarNombreReporte'])->name('sidebar.bodega.extras.reportenombre');
Route::post('/admin/bodega/extras/actualizarDatos', [BReportesController::class,'actualizarConfigurarNombreReporte']);

// VISTA REPORTE GENERAL -> VARIOS
Route::get('/admin/bodega/reportes/generales', [BReportesController::class,'vistaReporteGenerales'])->name('sidebar.bodega.reportes.general');
Route::get('/admin/bodega/reportes/pdf-existencias', [BReportesController::class,'generarPDFExistencias']);

// Reporte General de Existencias por Fechas
Route::get('/admin/bodega/reportes/pdf/existencias-fechas/{desde}/{hasta}/{check}/{arrayproductos}', [BReportesController::class,'generarPDFExistenciasFechas']);
// Reporte General de Existencia DESGLOSE
Route::get('/admin/bodega/reportes/pdf/existencias/desglose/{desde}/{hasta}/{idproducto}', [BReportesController::class,'generarPDFExistenciasFechasDesglose']);

// Reporte General de Existencias por Fechas LOTES
Route::get('/admin/bodega/reportes/pdf/existencias-fechas-lotes/{desde}/{hasta}/{arrayproductos}', [BReportesController::class,'generarPDFExistenciasFechasLotes']);





// VISTA PARA REPORTE DE UNIDADES DE LO QUE HEMOS ENTREGADO. INDIVIDUAL Y TODAS LAS UNIDADES
Route::get('/admin/bodega/reportes/entregaunidadesvarios', [BReportesController::class,'vistaReporteEntregadoUnidades'])->name('sidebar.bodega.reportes.salidasunidad');
Route::get('/admin/bodega/reportes/unidadentrega/pdf/{idusuario}/{desde}/{hasta}', [BReportesController::class,'reporteUnidadPDFEntregas']);
Route::get('/admin/bodega/reportes/totalentrega/pdf/{desde}/{hasta}', [BReportesController::class,'reportePDFEntregasTotal']);








// BORRAR ENTRADA COMPLETA DE PRODUCTOS -> ELIMINARA SALIDAS SI HUBIERON
Route::post('/admin/bodega/historial/entrada/borrarlote', [BHistorialController::class, 'historialEntradaBorrarLote']);
Route::post('/admin/bodega/historial/entradadetalle/borraritem', [BHistorialController::class, 'historialEntradaDetalleBorrarItem']);
Route::post('/admin/bodega/historial/entradadetalle/informacion', [BHistorialController::class, 'informacionItemEntradaDetalle']);
Route::post('/admin/bodega/historial/entradadetalle/editar', [BHistorialController::class, 'editarItemEntradaDetalle']);


// ** SOLICITUDES PENDIENTES
Route::get('/admin/bodega/solicitudpendiente/index', [BSolicitudesController::class,'indexSolicitudesPendientes'])->name('sidebar.bodega.solicitudes.pendientes');
Route::get('/admin/bodega/solicitudpendiente/tabla', [BSolicitudesController::class,'tablaSolicitudesPendientes']);
// cambiar estado completo -> finalizar
Route::post('/admin/bodega/solicitudpendiente/estadofinalizar', [BSolicitudesController::class, 'cambiarEstadoAFinalizar']);
// cambiar estado completo -> pendiente
Route::post('/admin/bodega/solicitudpendiente/estadopendiente', [BSolicitudesController::class, 'cambiarEstadoAPendiente']);

// ELIMINAR COMPLETAMENTE UNA SOLICITUD SINO TIENE SALIDAS
Route::post('/admin/bodega/solicitud/eliminarcompleta', [BSolicitudesController::class, 'eliminarCompletamenteSolicitud']);

Route::get('/admin/bodega/solicitudpendiente/detalle/index/{id}', [BSolicitudesController::class,'indexDetalleSolicitudesPendientes']);

// informacion de fila bodega_solicitud_detalle
Route::post('/admin/bodega/solicitudpendiente/infobodesolituddetalle', [BSolicitudesController::class, 'infoBodegaSolitudDetalleFila']);

// asignar referencia a un material de una solicitud.
Route::post('/admin/bodega/solicitudpendiente/asignar/referencia', [BSolicitudesController::class, 'asignarReferenciaMaterialSolicitado']);

// cuando alguien solicito algo y no se tenga se puede denegar sin asignar referencia
Route::post('/admin/bodega/noreferencia/estadodenegado', [BSolicitudesController::class, 'noReferenciaEstadoDenegado']);
Route::post('/admin/bodega/noreferencia/estadopendiente', [BSolicitudesController::class, 'noReferenciaEstadoPendiente']);

// cambiar la referencia unicamente sino ha entregado nada de producto
Route::post('/admin/bodega/referencia/cambionuevoid', [BSolicitudesController::class, 'cambiarNuevaReferenciaProducto']);


// modificar estado de un material solicitado fila
Route::post('/admin/bodega/solicitudpendiente/modificar/estadofila', [BSolicitudesController::class, 'modificarEstadoFilaSolicitud']);

// informacion de lista de materiales lote para dar salida final
Route::post('/admin/bodega/solicitudpendiente/infomaterialsalidalote', [BSolicitudesController::class, 'infoBodegaMaterialLoteDetalleFila']);

// ** REGISTRAR SALIDA DE PRODUCTO
Route::post('/admin/bodega/solicitudpendiente/registrarsalida', [BSolicitudesController::class, 'registrarSalidaBodegaSolicitud']);


// VERIFICAR CUANTAS SALIDAS TIENE ESTE ID: bodega_solicitud_detalle
Route::post('/admin/bodega/infosalidas/bodegasolidetalle', [BSolicitudesController::class, 'infoCuantasSalidasTieneSoliDetalle']);

// INFORMACION DE LA SOLICITUD
Route::post('/admin/bodega/solicitud/informacion', [BSolicitudesController::class, 'informacionSolicitud']);
Route::post('/admin/bodega/solicitud/editardatos', [BSolicitudesController::class, 'editarSolicitudNombre']);





//*** SOLICITUDES FINALIZADAS
Route::get('/admin/bodega/solicitudfinalizadas/index', [BSolicitudesController::class,'indexSolicitudesFinalizadas'])->name('sidebar.bodega.solicitudes.finalizadas');
Route::get('/admin/bodega/solicitudfinalizadas/tabla', [BSolicitudesController::class,'tablaSolicitudesFinalizadas']);

Route::get('/admin/bodega/solicitudfinalizadas/detalle/{id}', [BSolicitudesController::class,'indexDetalleSolicitudesFinalizadas']);





// ACTUALIZAR TABLA DE COSTOS 11/09/2024
//Route::post('/admin/actualizartabla', [SolicitudesITController::class, 'actualizarTabla']);


// *** SINDICATURA *** 13/12/2024

// Agregar los Estados
Route::get('/admin/sindico/estados/index', [SindicoController::class,'indexEstado'])->name('admin.sindico.estados.index');
Route::get('/admin/sindico/estados/tabla', [SindicoController::class, 'tablaEstado']);
Route::post('/admin/sindico/estados/nuevo', [SindicoController::class, 'nuevoEstado']);
Route::post('/admin/sindico/estados/informacion', [SindicoController::class, 'informacionEstado']);
Route::post('/admin/sindico/estados/editar', [SindicoController::class, 'actualizarEstado']);

// Agregar los tipos de solicitudes
Route::get('/admin/sindico/tiposolicitud/index', [SindicoController::class,'indexTipoSolicitud'])->name('admin.sindico.tiposolicitud.index');
Route::get('/admin/sindico/tiposolicitud/tabla', [SindicoController::class, 'tablaTipoSolicitud']);
Route::post('/admin/sindico/tiposolicitud/nuevo', [SindicoController::class, 'nuevoTipoSolicitud']);
Route::post('/admin/sindico/tiposolicitud/informacion', [SindicoController::class, 'informacionTipoSolicitud']);
Route::post('/admin/sindico/tiposolicitud/editar', [SindicoController::class, 'actualizarTipoSolicitud']);

// Agregar los estados de inmueble
Route::get('/admin/sindico/inmueble/index', [SindicoController::class,'indexInmueble'])->name('admin.sindico.inmueble.index');
Route::get('/admin/sindico/inmueble/tabla', [SindicoController::class, 'tablaInmueble']);
Route::post('/admin/sindico/inmueble/nuevo', [SindicoController::class, 'nuevoInmueble']);
Route::post('/admin/sindico/inmueble/informacion', [SindicoController::class, 'informacionInmueble']);
Route::post('/admin/sindico/inmueble/editar', [SindicoController::class, 'actualizarInmueble']);

// Agregar los tipos deligencia
Route::get('/admin/sindico/tipodeligencia/index', [SindicoController::class,'indexTipoDeligencia'])->name('admin.sindico.tipodeligencia.index');
Route::get('/admin/sindico/tipodeligencia/tabla', [SindicoController::class, 'tablaTipoDeligencia']);
Route::post('/admin/sindico/tipodeligencia/nuevo', [SindicoController::class, 'nuevoTipoDeligencia']);
Route::post('/admin/sindico/tipodeligencia/informacion', [SindicoController::class, 'informacionTipoDeligencia']);
Route::post('/admin/sindico/tipodeligencia/editar', [SindicoController::class, 'actualizarTipoDeligencia']);


// registro datos sindico
Route::get('/admin/sindico/registro/index', [SindicoController::class,'indexRegistroDatos'])->name('admin.sindico.registro.index');
Route::post('/admin/sindico/registro/nuevo', [SindicoController::class, 'registroDatosSindicatura']);

Route::get('/admin/sindico/registrotodos/index', [SindicoController::class,'indexTodosRegistros'])->name('admin.sindico.registro.todos.index');
Route::get('/admin/sindico/registrotodos/tabla/{id}', [SindicoController::class,'tablaTodosRegistros']);

Route::post('/admin/sindico/registrotodos/borrar', [SindicoController::class, 'borrarDatosSindicatura']);
Route::post('/admin/sindico/registrotodos/informacion', [SindicoController::class, 'informacionDatosSindicatura']);
Route::post('/admin/sindico/registrotodos/editar', [SindicoController::class, 'editarDatosSindicatura']);




//-------------------- MODULO PARA TESORERIA -> PROVEEDORES ----------------------------------


Route::get('/admin/tesoreria/proveedores/index', [TesoreriaConfigController::class,'indexProveedor'])->name('admin.tesoreria.proveedores.index');
Route::get('/admin/tesoreria/proveedores/tabla', [TesoreriaConfigController::class, 'tablaProveedor']);
Route::post('/admin/tesoreria/proveedores/nuevo', [TesoreriaConfigController::class, 'nuevoProveedor']);
Route::post('/admin/tesoreria/proveedores/informacion', [TesoreriaConfigController::class, 'informacionProveedor']);
Route::post('/admin/tesoreria/proveedores/editar', [TesoreriaConfigController::class, 'actualizarProveedor']);

//-------------------- MODULO PARA TESORERIA -> GARANTIAS ----------------------------------

Route::get('/admin/tesoreria/garantia/index', [TesoreriaConfigController::class,'indexGarantia'])->name('admin.tesoreria.garantia.index');
Route::get('/admin/tesoreria/garantia/tabla', [TesoreriaConfigController::class, 'tablaGarantia']);
Route::post('/admin/tesoreria/garantia/nuevo', [TesoreriaConfigController::class, 'nuevoGarantia']);
Route::post('/admin/tesoreria/garantia/informacion', [TesoreriaConfigController::class, 'informacionGarantia']);
Route::post('/admin/tesoreria/garantia/editar', [TesoreriaConfigController::class, 'actualizarGarantia']);

//-------------------- MODULO PARA TESORERIA -> TIPO DE GARANTIAS ----------------------------------

Route::get('/admin/tesoreria/tipo/garantia/index', [TesoreriaConfigController::class,'indexTipoGarantia'])->name('admin.tesoreria.tipogarantia.index');
Route::get('/admin/tesoreria/tipo/garantia/tabla', [TesoreriaConfigController::class, 'tablaTipoGarantia']);
Route::post('/admin/tesoreria/tipo/garantia/nuevo', [TesoreriaConfigController::class, 'nuevoTipoGarantia']);
Route::post('/admin/tesoreria/tipo/garantia/informacion', [TesoreriaConfigController::class, 'informacionTipoGarantia']);
Route::post('/admin/tesoreria/tipo/garantia/editar', [TesoreriaConfigController::class, 'actualizarTipoGarantia']);


//-------------------- MODULO PARA TESORERIA -> NUEVO REGISTRO -------------------------------------

Route::get('/admin/tesoreria/registro/index', [TesoreriaConfigController::class,'indexRegistroTesoreria'])->name('admin.tesoreria.nuevo.registro.index');
Route::post('/admin/tesoreria/registro', [TesoreriaConfigController::class,'nuevoRegistroTesoreria']);

//--- LISTADO
Route::get('/admin/tesoreria/listado/index', [TesoreriaConfigController::class,'vistaListadoRegistros'])->name('admin.tesoreria.listado.index');
Route::get('/admin/tesoreria/listado/tabla/index', [TesoreriaConfigController::class,'tablaListadoRegistros']);

Route::get('/admin/tesoreria/listado/edicion/index/{id}', [TesoreriaConfigController::class,'vistaListadoEdicion']);
Route::post('/admin/tesoreria/listado/actualizar', [TesoreriaConfigController::class,'actualizarRegistro']);

Route::post('/admin/tesoreria/registro/borrar', [TesoreriaConfigController::class,'borrarRegistro']);
Route::post('/admin/tesoreria/registro/informacion', [TesoreriaConfigController::class,'informacionRegistro']);




