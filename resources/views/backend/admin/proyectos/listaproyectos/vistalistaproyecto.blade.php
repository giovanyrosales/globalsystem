@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }

    .modal-xl { max-width: 90% !important; }

</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Proyectos Registrados</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Proyectos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalEditar" style="overflow-y: auto">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Información del Proyecto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Código *:</label>
                                        <input type="hidden" id="id-editar">
                                        <input type="text" maxlength="100" id="codigo" class="form-control" placeholder="Código de proyecto" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre *:</label>
                                        <input type="text" maxlength="300" id="nombre" class="form-control" placeholder="Nombre del proyecto" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ubicación *:</label>
                                        <input type="text" maxlength="300" id="ubicacion" placeholder="Ubicación" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Naturaleza:</label>
                                        <select id="select-naturaleza" class="form-control">
                                            <option value="" disabled selected>Seleccione una opción...</option>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Área de Gestión:</label>
                                        <select class="form-control" id="select-area-gestion">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Linea de Trabajo:</label>
                                        <select class="form-control" id="select-linea" >

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fuente de Financiamiento:</label>
                                        <select class="form-control" id="select-fuente-financiamiento" >

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fuente de Recursos:</label>
                                        <select class="form-control" id="select-fuente-recursos" >

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Contraparte:</label>
                                        <input type="text" maxlength="300" id="contraparte" autocomplete="off" placeholder="Contraparte" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Código Contable:</label>
                                        <input type="text" maxlength="150" id="codcontable" class="form-control" placeholder="Código contable" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de Inicio:</label>
                                        <input type="date" id="fecha-inicio" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Acuerdo Apertura:</label>
                                        <p id="hayAcuerdo"></p>
                                        <input type="file" id="acuerdo-apertura" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ejecutor:</label>
                                        <input type="text" maxlength="300" id="ejecutor" placeholder="Nombre de Ejecutor de la Obra" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Formulador:</label>
                                        <input type="text" maxlength="300" id="formulador" placeholder="Nombre de formulador" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Supervisor:</label>
                                        <input type="text" maxlength="300" id="supervisor" placeholder="Nombre de Supervisor" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Encargado:</label>
                                        <input type="text" maxlength="300" id="encargado"  placeholder="Nombre de Encargado" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarU" onclick="verificar()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- modal opciones de proyecto -->
    <div class="modal fade" id="modalOpcion">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Opciones</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <!-- ID del proyecto -->
                                <input type="hidden" id="id-proyecto">

                                <!-- información del proyecto -->
                                @can('boton.ver.proyecto')
                                    <div class="form-group">
                                        <button type="button" style="width: 100%" class="btn btn-warning" onclick="vista()">
                                            <i class="fas fa-eye" title="Ver"></i>&nbsp; Ver Información
                                        </button>
                                    </div>
                                @endcan

                                <!-- editar información del proyecto -->
                                @can('boton.editar.proyecto')
                                    <div class="form-group">
                                        <button type="button" style="width: 100%" class="btn btn-info" onclick="informacion()">
                                            <i class="fas fa-pen" title="Editar"></i>&nbsp; Editar Proyecto
                                        </button>
                                    </div>
                                @endcan

                                <!-- este el presupuesto mostrado en un modal, aquí aparece botón para aprobar -->
                                @can('boton.ver.presupuesto.modal')

                                    <div class="form-group" id="divModalPresupuesto">
                                        <button type="button" style="width: 100%;" class="btn btn-success" onclick="informacionPresupuesto()">
                                            <i class="fas fa-list-alt" title="Presupuesto"></i>&nbsp; Presupuesto
                                        </button>
                                    </div>
                                @endcan

                                <!-- ver planilla de proyecto -->
                                @can('boton.ver.planilla')
                                    <div class="form-group" id="divBtnPlanilla" >
                                        <button type="button" style="width: 100%;" class="btn btn-info" onclick="informacionPlanilla()">
                                            <i class="fas fa-eye" title="Planilla"></i>&nbsp; Planilla
                                        </button>
                                    </div>
                                @endcan

                                <!-- modal con los saldos -->
                                @can('boton.dinero.presupuesto')
                                        <div class="form-group" id="divContenedorSaldos">
                                            <button type="button" style="width: 100%;" class="btn btn-info" onclick="informacionSaldo()">
                                                <i class="fas fa-list-alt" title="Saldos"></i>&nbsp; Saldos
                                            </button>
                                        </div>
                                @endcan

                                @can('boton.movimiento.cuenta.proyecto')
                                    <div class="form-group" id="divContenedorMovimiento" >
                                        <button type="button" style="width: 100%;" class="btn btn-info" onclick="informacionMovimiento()">
                                            <i class="fas fa-list-alt" title="Movimiento Cuenta"></i>&nbsp; Movimiento Cuenta
                                        </button>
                                    </div>
                                @endcan

                                @can('boton.modal.vista.partida.adicionales')
                                    <div class="form-group" id="divContenedorPartidaAdicional" >
                                        <button type="button" style="width: 100%;" class="btn btn-info" onclick="vistaPartidaAdicional()">
                                            <i class="fas fa-list-alt" title="Partida Adicional"></i>&nbsp; Partida Adicional
                                        </button>
                                    </div>
                                @endcan

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- modal presupuesto para aprobacion-->
    <div class="modal fade" id="modalPresupuesto">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Presupuesto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaPre">

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>


    <!-- modal para ver saldo del proyecto -->
    <div class="modal fade" id="modalSaldo">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Saldo Disponible</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaSaldo">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- modal para cambiar estado de un proyecto -->
    <div class="modal fade" id="modalEstadoProyecto">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estado de Proyecto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="idproyecto-estado">
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre del Proyecto:</label>
                                        <input type="text" disabled class="form-control" id="nombreproyecto-estado">
                                    </div>

                                    <div class="form-group">
                                        <label>Presupuesto de Proyecto</label>
                                        <input type="text" style="font-weight: bold" disabled id="nompresupuesto-estado" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Bolsón Asignado</label>
                                        <input type="text" disabled id="nombolson-estado" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Estado de Proyecto</label>
                                        <select class="form-control" id="select-estado-proyecto">
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarEstado()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- modal para seleccionar bolsón para asignar a un proyecto. USADO CUANDO SE APRUEBA PRESUPUESTO PROYECTO -->

    <div class="modal fade" id="modalBolsonPendiente" tabindex="-10">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aprobar Presupuesto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Presupuesto de Proyecto</label>
                                        <label id="texto-presupuesto" class="form-control"></label>
                                    </div>

                                    <hr>
                                    <br>

                                    <label>Asignar Bolsón a Proyecto</label>
                                    <div class="form-group">
                                        <select class="form-control" id="select-bolson-pendiente" onchange="infoSaldoBolson(this)">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Saldo de Bolsón</label>
                                        <label id="texto-bolson-pendiente" class="form-control"></label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="preguntaAprobarPresupuesto()">Aprobar</button>
                </div>
            </div>
        </div>
    </div>


</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/proyecto/lista/tabla/index') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/proyecto/lista/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function informacion(){
            var id = document.getElementById('id-proyecto').value;
            $('#modalOpcion').modal('hide');
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/proyecto/lista/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $("#modalEditar").on("show", function () {
                            $("body").addClass("modal-open");
                        }).on("hidden", function () {
                            $("body").removeClass("modal-open")
                        });

                        $('#id-editar').val(response.data.info.id);
                        $('#codigo').val(response.data.info.codigo);
                        $('#nombre').val(response.data.info.nombre);
                        $('#ubicacion').val(response.data.info.ubicacion);
                        $('#ejecutor').val(response.data.info.ejecutor);
                        $('#formulador').val(response.data.info.formulador);
                        $('#supervisor').val(response.data.info.supervisor);
                        $('#encargado').val(response.data.info.encargado);
                        $('#contraparte').val(response.data.info.contraparte);
                        $('#codcontable').val(response.data.info.codcontable);


                        // cuando presupuesto este aprobado, se puede agregar
                        // fecha de inicio y acuerdo.
                        // MODO DESARROLLO Y REVISIÓN
                        if(response.data.info.presu_aprobado === 0 || response.data.info.presu_aprobado === 1){
                            document.getElementById('fecha-inicio').disabled = true;
                            document.getElementById('acuerdo-apertura').disabled = true;
                            $('#fecha-inicio').val('');
                        }else{
                            // presupuesto aprobado
                            document.getElementById('fecha-inicio').disabled = false;
                            document.getElementById('acuerdo-apertura').disabled = false;
                            $('#fecha-inicio').val(response.data.info.fechaini);
                        }

                        if(response.data.info.acuerdoapertura === null){
                            document.getElementById("hayAcuerdo").innerHTML = '';
                        }else{
                            document.getElementById("hayAcuerdo").innerHTML = 'Ya se encuentra un Acuerdo registrado';
                        }

                        document.getElementById("select-naturaleza").options.length = 0;
                        document.getElementById("select-area-gestion").options.length = 0;
                        document.getElementById("select-linea").options.length = 0;

                        $.each(response.data.arrayNaturaleza, function( key, val ){
                            if(response.data.info.id_naturaleza == val.id){
                                $('#select-naturaleza').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-naturaleza').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        // *** area gestion

                        if(response.data.info.id_areagestion == null){
                            $('#select-area-gestion').append('<option value="" selected="selected">Ninguna</option>');
                        }else{
                            $('#select-area-gestion').append('<option value="">Ninguna</option>');
                        }

                        $.each(response.data.arrayAreaGestion, function( key, val ){
                            if (response.data.info.id_areagestion == val.id) {
                                $('#select-area-gestion').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " - " + val.nombre + '</option>');
                            } else {
                                $('#select-area-gestion').append('<option value="' + val.id + '">' + val.codigo + " - " + val.nombre + '</option>');
                            }
                        });

                        // *** linea de trabajo
                        if(response.data.info.id_linea == null){
                            $('#select-linea').append('<option value="" selected="selected">Ninguna</option>');
                        }else{
                            $('#select-linea').append('<option value="">Ninguna</option>');
                        }

                        $.each(response.data.arrayLineaTrabajo, function( key, val ){
                            if (response.data.info.id_linea == val.id) {
                                $('#select-linea').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " - " + val.nombre + '</option>');
                            } else {
                                $('#select-linea').append('<option value="' + val.id + '">' + val.codigo + " - " + val.nombre + '</option>');
                            }
                        });

                        // *** fuente de financiamiento
                        if(response.data.info.id_fuentef == null){
                            $('#select-fuente-financiamiento').append('<option value="" selected="selected">Ninguna</option>');
                        }else{
                            $('#select-fuente-financiamiento').append('<option value="">Ninguna</option>');
                        }

                        $.each(response.data.arrayFuenteFinanciamiento, function( key, val ){
                            if (response.data.info.id_fuentef == val.id) {
                                $('#select-fuente-financiamiento').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " - " + val.nombre + '</option>');
                            } else {
                                $('#select-fuente-financiamiento').append('<option value="' + val.id + '">' + val.codigo + " - " + val.nombre + '</option>');
                            }
                        });

                        // *** fuente de recursos
                        if(response.data.info.id_fuenter == null){
                            $('#select-fuente-recursos').append('<option value="" selected="selected">Ninguna</option>');
                        }else{
                            $('#select-fuente-recursos').append('<option value="">Ninguna</option>');
                        }

                        $.each(response.data.arrayFuenteRecursos, function( key, val ){
                            if (response.data.info.id_fuenter == val.id) {
                                $('#select-fuente-recursos').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " - " + val.nombre + '</option>');
                            } else {
                                $('#select-fuente-recursos').append('<option value="' + val.id + '">' + val.codigo + " - " + val.nombre + '</option>');
                            }
                        });


                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function verificar(){
            Swal.fire({
                title: 'Actualizar Proyecto?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    editar();
                }
            })
        }

        function editar(){

            var id = document.getElementById('id-editar').value;
            var codigo = document.getElementById('codigo').value; //null
            var nombre = document.getElementById('nombre').value;
            var ubicacion = document.getElementById('ubicacion').value;
            var naturaleza = document.getElementById('select-naturaleza').value; // null
            var areagestion = document.getElementById('select-area-gestion').value; // null
            var linea = document.getElementById('select-linea').value; // null
            var fuentef = document.getElementById('select-fuente-financiamiento').value; // null
            var fuenter = document.getElementById('select-fuente-recursos').value; // null
            var contraparte = document.getElementById('contraparte').value; // null
            var codcontable = document.getElementById('codcontable').value; // null
            var fechainicio = document.getElementById('fecha-inicio').value; // null
            var acuerdoApertura = document.getElementById('acuerdo-apertura'); // null file
            var ejecutor = document.getElementById('ejecutor').value; // null
            var formulador = document.getElementById('formulador').value; // null
            var supervisor = document.getElementById('supervisor').value; // null
            var encargado = document.getElementById('encargado').value; // null


            if(codigo.length > 100){
                toastr.error('Código máximo 100 caracteres');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Nombre máximo 300 caracteres');
                return;
            }

            if(ubicacion === ''){
                toastr.error('Ubicación es requerido');
                return;
            }

            if(ubicacion.length > 300){
                toastr.error('Ubicación máximo 300 caracteres');
                return;
            }

            if(contraparte.length > 300){
                toastr.error('Contraparte máximo 300 caracteres');
                return;
            }

            if(codcontable.length > 150){
                toastr.error('Cod. Contable máximo 150 caracteres');
                return;
            }

            if(acuerdoApertura.files && acuerdoApertura.files[0]){ // si trae doc
                if (!acuerdoApertura.files[0].type.match('image/jpeg|image/jpeg|image/png|pdf')){
                    toastr.error('formato de acuerdo apertura permitido: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            if(ejecutor.length > 300){
                toastr.error('Ejecutor máximo 300 caracteres');
                return;
            }

            if(formulador.length > 300){
                toastr.error('Formulador máximo 300 caracteres');
                return;
            }

            if(supervisor.length > 300){
                toastr.error('Supervisor máximo 300 caracteres');
                return;
            }

            if(encargado.length > 300){
                toastr.error('Encargado máximo 300 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('codigo', codigo);
            formData.append('nombre', nombre);
            formData.append('ubicacion', ubicacion);
            formData.append('naturaleza', naturaleza);
            formData.append('areagestion', areagestion);
            formData.append('linea', linea);
            formData.append('fuentef', fuentef);
            formData.append('fuenter', fuenter);
            formData.append('contraparte', contraparte);
            formData.append('codcontable', codcontable);
            formData.append('fechainicio', fechainicio);
            formData.append('documento', acuerdoApertura.files[0]);
            formData.append('ejecutor', ejecutor);
            formData.append('formulador', formulador);
            formData.append('supervisor', supervisor);
            formData.append('encargado', encargado);

            axios.post(url+'/proyecto/lista/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        Swal.fire({
                            title: 'Código Erróneo',
                            text: "El código ya se encuentra registrado",
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        $('#modalEditar').modal('hide');
                        toastr.success('Actualizado correctamente');
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }

        // modal para varias opciones de proyecto
        function modalOpciones(dato){

            // ocultar botones

            if (document.getElementById('divModalPresupuesto') !== null) {
                document.getElementById("divModalPresupuesto").style.display = "none";
            }

            if (document.getElementById('divBtnPlanilla') !== null) {
                document.getElementById("divBtnPlanilla").style.display = "none";
            }

            if (document.getElementById('divContenedorSaldos') !== null) {
                document.getElementById("divContenedorSaldos").style.display = "none";
            }

            if (document.getElementById('divContenedorMovimiento') !== null) {
                document.getElementById("divContenedorMovimiento").style.display = "none";
            }

            if (document.getElementById('divContenedorPartidaAdicional') !== null) {
                document.getElementById("divContenedorPartidaAdicional").style.display = "none";
            }


            // VIENE ID PROYECTO
            $('#id-proyecto').val(dato.id);

            // OBTENER INFORMACION DEL PROYECTO POR SEGUIRIDAD

            openLoading();
            axios.post(url+'/proyecto/informacion/individual', {
                'id' : dato.id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let estado = response.data.info.presu_aprobado;


                        // 1: LISTO PARA REVISIÓN
                        // el botón aparecerá si usuario tiene permiso y el estado presupuesto sea 1 o 2
                        if(estado === 1 || estado === 2){
                            if (document.getElementById('divModalPresupuesto') !== null) {
                                document.getElementById("divModalPresupuesto").style.display = "block";
                            }

                            if(estado === 2){
                                if (document.getElementById('divBtnPlanilla') !== null) {
                                    document.getElementById("divBtnPlanilla").style.display = "block";
                                }

                                if (document.getElementById('divContenedorSaldos') !== null) {
                                    document.getElementById("divContenedorSaldos").style.display = "block";
                                }

                                if (document.getElementById('divContenedorMovimiento') !== null) {
                                    document.getElementById("divContenedorMovimiento").style.display = "block";
                                }

                                if (document.getElementById('divContenedorPartidaAdicional') !== null) {
                                    document.getElementById("divContenedorPartidaAdicional").style.display = "block";
                                }
                            }
                        }

                        $('#modalOpcion').modal('show');
                    }
                    else {
                        toastr.error('Error al buscar información');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al buscar información');
                    closeLoading();
                });
        }


        function vista(){
            $('#modalOpcion').modal('hide');
            var id = document.getElementById('id-proyecto').value;
            window.location.href="{{ url('/admin/proyecto/vista/index') }}/" + id;
        }

        function informacionPlanilla(){
            var id = document.getElementById('id-proyecto').value;
            window.location.href="{{ url('/admin/planilla/lista') }}/" + id;
        }

        // cargar modal con todos el presupuesto
        function informacionPresupuesto(){

            var id = document.getElementById('id-proyecto').value;
            var ruta = "{{ URL::to('/admin/ver/presupuesto/uaci') }}/" + id;
            $('#tablaPre').load(ruta);
            $('#modalPresupuesto').modal('show');
        }

        // cargar modal con el saldo del proyecto
        function informacionSaldo(){
            var id = document.getElementById('id-proyecto').value;
            var ruta = "{{ URL::to('/admin/ver/presupuesto/saldo') }}/" + id;
            $('#tablaSaldo').load(ruta);
            $('#modalSaldo').modal('show');
        }

        // carga otra vista para manejar los movimiento de cuenta
        function informacionMovimiento(){
            // ID PROYECTO
            var id = document.getElementById('id-proyecto').value;
            window.location.href="{{ url('/admin/movicuentaproy/indexmovicuentaproy') }}/"+id;
        }

        function vistaPartidaAdicional(){
            // ID PROYECTO
            var id = document.getElementById('id-proyecto').value;
            window.location.href="{{ url('/admin/partida/adici/conte/jefatura/index/') }}/"+id;
        }

        // aquí se abre modal para asignar un bolsón al presupuesto
        function btnAprobarPresupuesto(){

            var id = document.getElementById('id-proyecto').value;
            document.getElementById("texto-presupuesto").innerHTML = '';

            openLoading();
            // solo obtener los bolsones

            document.getElementById("texto-bolson-pendiente").innerHTML = "";

            axios.post(url+'/bolsones/todos/informacion', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                  if(response.data.success === 1){

                      $('#modalOpcion').modal('hide');
                      $('#modalPresupuesto').modal('hide');
                      $('#modalBolsonPendiente').modal('show');

                      let presupuesto = response.data.presupuesto;

                      document.getElementById("texto-presupuesto").innerHTML = presupuesto;

                      document.getElementById("select-bolson-pendiente").options.length = 0;

                      $('#select-bolson-pendiente').append('<option value="0">Seleccionar Bolsón</option>');

                      $.each(response.data.lista, function( key, val ){
                          $('#select-bolson-pendiente').append('<option value='+val.id+'>'+val.nombre+'</option>');
                      });

                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function preguntaAprobarPresupuesto(){


            var sel = document.getElementById('select-bolson-pendiente').value;

            if(sel == '0'){
                toastr.error('Cuenta Bolsón es requerido');
                return;
            }

            Swal.fire({
                title: 'Aprobar Presupuesto',
                text: "Se asignara Cuenta Bolsón a Proyecto",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: "Cancelar",
                confirmButtonText: 'Aprobar'
            }).then((result) => {
                if (result.isConfirmed) {
                    aprobarPresupuesto(sel);
                }
            })
        }

        function aprobarPresupuesto(sel){

            openLoading();
            var id = document.getElementById('id-proyecto').value;

            let formData = new FormData();
            formData.append('id', id);
            formData.append('idbolson', sel);

            $('#modalOpcion').modal('hide');

            axios.post(url+'/proyecto/aprobar/presupuesto', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        // el estado ha cambiado para su aprobación
                        $('#modalPresupuesto').modal('hide');
                        Swal.fire({
                            title: 'Estado Cambio',
                            text: "El presupuesto se encuentra en desarrollo",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                recargar();
                            }
                        })
                    }
                    else if(response.data.success === 2){
                        $('#modalPresupuesto').modal('hide');

                        Swal.fire({
                            title: 'Presupuesto Aprobado',
                            text: "",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }

                    else if(response.data.success === 3){
                        $('#modalPresupuesto').modal('hide');

                        Swal.fire({
                            title: 'Falta Información',
                            text: "El presupuesto no tiene APORTE MANO DE OBRA (POR ADMINISTRACIÓN)",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }

                    else if(response.data.success === 4){
                        $('#modalPresupuesto').modal('hide');

                        let actual = response.data.actual;
                        let requerido = response.data.requerido;

                        var texto = "El Bolsón no cuenta con suficientes Fondos. " + "<br>" +
                            "Monto Actual $" + actual + "<br>" +
                            "Monto Requerido $" + requerido + "<br>";

                        Swal.fire({
                            title: 'Fondo Insuficientes',
                            html: texto,
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }

                    else if(response.data.success === 5){
                        let conteo = response.data.conteo;

                        document.getElementById('modalPresupuesto').style.overflowY = 'auto';

                        Swal.fire({
                            title: 'Materiales Incompleto',
                            text: "Hay " + conteo + " Materiales que no tiene asignado un Objeto Específico",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else{
                        toastr.error('error al aprobar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al aprobar');
                    closeLoading();
                });
        }

        function verPresupuestoPorAdministrador(){
            var id = document.getElementById('id-proyecto').value;
            axios.post(url+'/proyecto/partida/manoobra/existe', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        window.open("{{ URL::to('admin/generar/pdf/presupuesto') }}/"+id);
                    }
                    else if(response.data.success === 2){
                        Swal.fire({
                            title: 'Partida Requerida',
                            text: "Se debe crear la Partida para Mano de Obra (por Administración)",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else{
                        toastr.error('error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al buscar');
                    closeLoading();
                });
        }

        // estados de un proyecto
        function modalEstados(id){
            // ID PROYECTO

            let formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/proyecto/estado/informacion', formData, {
            })
                .then((response) => {
                    closeLoading();

                     if(response.data.success === 1){

                         $('#modalEstadoProyecto').modal('show');
                         $('#idproyecto-estado').val(id);

                         $('#nombreproyecto-estado').val(response.data.info.nombre);

                         // Presupuesto aprobado
                         if(response.data.info.presu_aprobado === 2){

                             $('#nompresupuesto-estado').val('$' + response.data.monto);
                         }else{
                             $('#nompresupuesto-estado').val('Pendiente de Aprobación');
                         }

                         if(response.data.info.id_bolson != null) {

                             $('#nombolson-estado').val(response.data.nombolson);
                         }else{
                             $('#nombolson-estado').val('Pendiente de Asignación');
                         }

                        document.getElementById("select-estado-proyecto").options.length = 0;

                        if(response.data.info.id_estado == null){
                            $('#select-estado-proyecto').append('<option value="" selected="selected">Seleccionar Estado</option>');
                        }

                        $.each(response.data.arrayEstado, function( key, val ){
                            if (response.data.info.id_estado == val.id) {
                                $('#select-estado-proyecto').append('<option value="' + val.id + '" selected="selected">'+ val.nombre + '</option>');
                            } else {
                                $('#select-estado-proyecto').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                            }
                        });

                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function actualizarEstado(){

            openLoading();

            var idproy = document.getElementById('idproyecto-estado').value;
            var idselect = document.getElementById('select-estado-proyecto').value;

            let formData = new FormData();
            formData.append('id', idproy);
            formData.append('idestado', idselect);

            axios.post(url+'/proyecto/estado/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    // cualquier intento de modificar estados si proyecto ya estaba finalizado
                    if(response.data.success === 1){

                        let titulo = response.data.titulo;
                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: titulo,
                            text: mensaje,
                            icon: 'error',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }

                    // puede actualizarse, a PRIORIZADO
                    else if(response.data.success === 2) {

                        $('#modalEstadoProyecto').modal('hide');
                        toastr.success('Actualizado a PRIORIZADO');
                        recargar();

                    }
                    else if(response.data.success === 3){

                        // varios estados cuando no se puede actualizar

                        let titulo = response.data.titulo;
                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: titulo,
                            text: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })

                    } else if(response.data.success === 4){

                        // estado a INICIADO

                        $('#modalEstadoProyecto').modal('hide');
                        toastr.success('Actualizado a INICIADO');
                        recargar();

                    } else if(response.data.success === 5){

                        // estado a PAUSADO

                        $('#modalEstadoProyecto').modal('hide');
                        toastr.success('Actualizado a PAUSADO');
                        recargar();
                    }

                    else if(response.data.success === 6){

                        // estado a FINALIZADO
                        $('#modalEstadoProyecto').modal('hide');

                        Swal.fire({
                            title: "Finalizado",
                            text: "Si el proyecto tiene saldo disponible, regresara al Bolsón que esta asignado",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                recargar();
                            }
                        })
                    }

                    else {
                        toastr.error('Error al Actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al Actualizar');
                    closeLoading();
                });
        }

        // información de saldo bolsón
        function infoSaldoBolson(e){

            // id select bolson
            let idbolson = $(e).val();
            var idproyecto = document.getElementById('id-proyecto').value;


            if(idbolson == '0'){
                document.getElementById("texto-bolson-pendiente").innerHTML = '';
                return;
            }

            openLoading();

            let formData = new FormData();
            formData.append('idproyecto', idproyecto);
            formData.append('idbolson', idbolson);

            axios.post(url+'/bolson/saldo/detalle/informacion', formData, {

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("texto-bolson-pendiente").innerHTML = response.data.monto;

                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });


        }


    </script>


@endsection
