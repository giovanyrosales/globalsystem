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
                    <h3 class="card-title">Listado</h3>
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


    <div class="modal fade" id="modalEditar">
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
                                        <label>Código:</label>
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <hr>
                                    <label>Sección de Prespuesto</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cuenta Bolsón:</label>
                                    <select id="select-bolson" class="form-control">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Monto de proyecto $:</label>
                                    <input type="number" id="monto"  class="form-control" step="any">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <hr>
                                    <label>Sección de UACI</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Estado del Proyecto:</label>
                                    <select id="select-estado" class="form-control">

                                    </select>
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

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/proyecto/lista/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})
                        $('#id-editar').val(response.data.info.id);
                        $('#codigo').val(response.data.info.codigo);
                        $('#nombre').val(response.data.info.nombre);
                        $('#ubicacion').val(response.data.info.ubicacion);
                        $('#fecha-inicio').val(response.data.info.fechaini);
                        $('#ejecutor').val(response.data.info.ejecutor);
                        $('#formulador').val(response.data.info.formulador);
                        $('#supervisor').val(response.data.info.supervisor);
                        $('#encargado').val(response.data.info.encargado);
                        $('#contraparte').val(response.data.info.contraparte);
                        $('#codcontable').val(response.data.info.codcontable);
                        $('#monto').val(response.data.info.monto);

                        if(response.data.info.acuerdoapertura === null){
                            document.getElementById("hayAcuerdo").innerHTML = '';
                        }else{
                            document.getElementById("hayAcuerdo").innerHTML = 'Ya se encuentra un Acuerdo registrado';
                        }

                        document.getElementById("select-naturaleza").options.length = 0;
                        document.getElementById("select-area-gestion").options.length = 0;
                        document.getElementById("select-linea").options.length = 0;
                        document.getElementById("select-bolson").options.length = 0;
                        document.getElementById("select-estado").options.length = 0;

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
                                $('#select-area-gestion').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " " + val.nombre + '</option>');
                            } else {
                                $('#select-area-gestion').append('<option value="' + val.id + '">' + val.codigo + " " + val.nombre + '</option>');
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
                                $('#select-linea').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " " + val.nombre + '</option>');
                            } else {
                                $('#select-linea').append('<option value="' + val.id + '">' + val.codigo + " " + val.nombre + '</option>');
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
                                $('#select-fuente-financiamiento').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " " + val.nombre + '</option>');
                            } else {
                                $('#select-fuente-financiamiento').append('<option value="' + val.id + '">' + val.codigo + " " + val.nombre + '</option>');
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
                                $('#select-fuente-recursos').append('<option value="' + val.id + '" selected="selected">' + val.codigo + " " + val.nombre + '</option>');
                            } else {
                                $('#select-fuente-recursos').append('<option value="' + val.id + '">' + val.codigo + " " + val.nombre + '</option>');
                            }
                        });

                        // *** bolson
                        if(response.data.info.id_bolson == null){
                            $('#select-bolson').append('<option value="" selected="selected">Ninguna</option>');
                        }else{
                            $('#select-bolson').append('<option value="">Ninguna</option>');
                        }

                        $.each(response.data.arrayBolson, function( key, val ){
                            if (response.data.info.id_fuenter == val.id) {
                                $('#select-bolson').append('<option value="' + val.id + '" selected="selected">'+ val.nombre + '</option>');
                            } else {
                                $('#select-bolson').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                            }
                        });

                        // *** estado de proyecto
                        if(response.data.info.id_estado == null){
                            $('#select-estado').append('<option value="" selected="selected">Ninguna</option>');
                        }else{
                            $('#select-estado').append('<option value="">Ninguna</option>');
                        }

                        $.each(response.data.arrayEstado, function( key, val ){
                            if (response.data.info.id_estado == val.id) {
                                $('#select-estado').append('<option value="' + val.id + '" selected="selected">'+ val.nombre + '</option>');
                            } else {
                                $('#select-estado').append('<option value="' + val.id + '">' + val.nombre + '</option>');
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
            var bolson = document.getElementById('select-bolson').value; // null
            var monto = document.getElementById('monto').value; // null
            var estado = document.getElementById('select-estado').value; // null

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

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(monto.length > 0){
                if(!monto.match(reglaNumeroDecimal)) {
                    toastr.error('valor debe ser número Decimal y No Negativos');
                    return;
                }

                if(monto < 0){
                    toastr.error('monto no permite números negativos');
                    return;
                }

                if(monto.length > 10){
                    toastr.error('monto máximo 10 dígitos de límite');
                    return;
                }
            }else{
                monto = 0;
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
            formData.append('bolson', bolson);
            formData.append('monto', monto);
            formData.append('estado', estado);

            axios.post(url+'/proyecto/lista/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        errorCodigo();
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

        function errorCodigo(){
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

        function vista(id){
            window.location.href="{{ url('/admin/proyecto/vista/index') }}/" + id;
        }

       

    </script>


@endsection
