@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
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
                <h2>Registro de Proyectos Municipales</h2>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Proyectos</li>
                    <li class="breadcrumb-item active">Registro de Proyectos Municipales</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Formulario</h3>
                </div>

                <form id="formulario-proyecto">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Código *:</label>
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
                                    @foreach($arrayNaturaleza as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Área de Gestión:</label>
                                <select class="form-control" id="select-area-gestion">
                                    <option value="" disabled selected>Seleccione una opción...</option>
                                    @foreach($arrayAreaGestion as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->codigo }} - {{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Linea de Trabajo:</label>
                                <select class="form-control" id="select-linea" >
                                    <option value="" disabled selected>Seleccione una opción...</option>
                                    @foreach($arrayLineaTrabajo as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->codigo }} - {{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fuente de Financiamiento:</label>
                                <select class="form-control" id="select-fuente-financiamiento" onchange="buscarFuenteRecursos()">
                                    <option value="" selected disabled>Seleccionar opción...</option>
                                    @foreach($arrayFuenteFinanciamiento as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->codigo }} - {{ $sel->nombre }}</option>
                                    @endforeach
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
                                <select class="form-control" id="select-formulador" >
                                    @foreach($formuladores as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
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

                <div class="card-footer">
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important; float: right" class="button button-3d button-rounded button-pill button-small" onclick="verificar();">Guardar</button>
                </div>


            </div>
        </div>
    </section>

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
            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function verificar(){
            Swal.fire({
                title: 'Guardar Proyecto?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    crearProyecto();
                }
            })
        }

        function buscarFuenteRecursos(){

            let id = document.getElementById('select-fuente-financiamiento').value;
            // compara si es tipo texto o numero
            if(id === ''){
                return;
            }

            openLoading();

            axios.post(url+'/bolson/retornar/fuente/recursos',{
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {

                        document.getElementById("select-fuente-recursos").options.length = 0;

                        $.each(response.data.lista, function( key, val ){
                            $('#select-fuente-recursos').append('<option value="' +val.id +'">'+val.unido+'</option>');
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

        function crearProyecto(){

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

            var ejecutor = document.getElementById('ejecutor').value; // null
            var supervisor = document.getElementById('supervisor').value; // null
            var encargado = document.getElementById('encargado').value; // null
            var idformulador = document.getElementById('select-formulador').value;

            if(codigo === ''){
                toastr.error('Código es requerido');
                return;
            }

            if(idformulador === ''){
                toastr.error('Formulador es requerido');
                return;
            }

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

            if(ejecutor.length > 300){
                toastr.error('Ejecutor máximo 300 caracteres');
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
            formData.append('ejecutor', ejecutor);
            formData.append('formulador', idformulador);
            formData.append('supervisor', supervisor);
            formData.append('encargado', encargado);

            axios.post(url+'/proyecto/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        Swal.fire({
                            title: 'Código Repetido',
                            text: "El código de Proyecto ya se encuentra registrado",
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

                        Swal.fire({
                            title: 'Proyecto Registrado',
                            text: "",
                            icon: 'success',
                            allowOutsideClick: false,
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function limpiarFormulario(){
            $('#formulario-proyecto')[0].reset();
        }


    </script>


@endsection
