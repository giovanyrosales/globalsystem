@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }

    #modalAgregar {
        max-height: 700px;
        overflow-y: auto;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                @if($puedeAgregar)
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" onclick="modalAgregar()"
                            class="button button-3d button-rounded button-pill button-small">
                        <i class="fas fa-plus-square"></i>
                        Nuevo Bolsón
                    </button>
                @endif
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Bolsones</li>
                    <li class="breadcrumb-item active">Listado</li>
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


    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Cuenta Bolsón</h4>
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
                                        <label>Año de Presupuesto</label>
                                        <select id="select-anio" class="form-control">
                                            @foreach($listadoanios as $dd)
                                                <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de creación</label>
                                        <input type="date" class="form-control" id="fecha-nuevo">
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre Cuenta Bolsón:</label>
                                        <input type="text" autocomplete="off" class="form-control" maxlength="200" id="nombre-nuevo">
                                    </div>

                                    <div class="form-group">
                                        <label>Número de Cuenta Bolsón:</label>
                                        <input type="text" autocomplete="off" class="form-control" maxlength="100" id="numero-nuevo">
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Seleccionar Objeto Específico</label>
                                        <select id="select-obj" class="form-control" multiple="multiple">
                                            @foreach($arrayobj as $dd)
                                                <option value="{{ $dd->id }}">{{ $dd->codigo }} - {{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="button" class="btn btn-info" onclick="verificarCuentaSaldo()">Verificar</button>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="nuevo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalPendiente">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Presupuestos aun sin Aprobar</h4>
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
                                        <select class="form-control" id="select-departamento">
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSaldosVerificados">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Objetos Específicos</h4>
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
                                        <label id="totalverificado"></label>
                                    </div>

                                    <div class="form-group">
                                        <select class="form-control" id="select-saldos-verificados">
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/bolson/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

            $('#select-obj').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/bolson/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $("#select-obj").val([]).change();
            $('#modalAgregar').modal('show');
        }

        // buscar cuanto dinero habra en las cuentas seleccionadas
        function verificarCuentaSaldo(){

            var anio = document.getElementById('select-anio').value;

            if(anio === ''){
                toastr.error('Año Presupuesto es requerido');
                return;
            }

            var valores = $('#select-obj').val();
            if(valores.length ==  null || valores.length === 0){
                toastr.error('Seleccionar mínimo 1 objeto específico');
                return;
            }

            var selected = [];
            for (var option of document.getElementById('select-obj').options){
                if (option.selected) {
                    selected.push(option.value);
                }
            }

            let formData = new FormData();
            formData.append('objetos', selected);
            formData.append('anio', anio);

            axios.post(url+'/bolson/verificar/saldo/objetos', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {
                        toastr.error('El Bolsón ya esta creado para este Año');
                    }
                    // departamentos que su presupuesto no está aprobado aun
                    else if(response.data.success === 2){
                        $('#modalPendiente').modal('show');

                        document.getElementById("select-departamento").options.length = 0;

                        $.each(response.data.lista, function( key, val ){
                            $('#select-departamento').append('<option value="0">'+val.nombre+'</option>');
                        });
                    }
                    else if(response.data.success === 3){
                        // viene el saldo según los códigos

                        let total = response.data.total;

                        $('#modalSaldosVerificados').modal('show');

                        document.getElementById("select-saldos-verificados").options.length = 0;

                        document.getElementById("totalverificado").innerHTML = "Total: $" + total;

                        $.each(response.data.lista, function( key, val ){
                            $('#select-saldos-verificados').append('<option value="0">'+val.unido+'</option>');
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

        function nuevo(){

            var anio = document.getElementById('select-anio').value;
            var fecha = document.getElementById('fecha-nuevo').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var numero = document.getElementById('numero-nuevo').value; // null

            if(anio === ''){
                toastr.error('Año Presupuesto es requerido');
                return;
            }

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre Bolsón es requerido');
                return;
            }

            if(nombre.length > 200){
                toastr.error('Máximo 200 caracteres para nombre');
                return;
            }

            if(numero.length > 0){
                if(numero.length > 100){
                    toastr.error('Máximo 100 caracteres para número de cuenta');
                    return;
                }
            }

            var valores = $('#select-obj').val();
            if(valores.length ==  null || valores.length === 0){
                toastr.error('Seleccionar mínimo 1 objeto específico');
                return;
            }

            var selected = [];
            for (var option of document.getElementById('select-obj').options){
                if (option.selected) {
                    selected.push(option.value);
                }
            }

            var sel = document.getElementById("select-anio");
            var textoanio = sel.options[sel.selectedIndex].text;

            openLoading();
            var formData = new FormData();
            formData.append('anio', anio);
            formData.append('fecha', fecha);
            formData.append('nombre', nombre);
            formData.append('numero', numero);
            formData.append('objetos', selected);

            axios.post(url+'/bolson/registrar/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    // bolsón ya creado para este año
                    if(response.data.success === 1){
                        Swal.fire({
                            title: 'Año Repetido',
                            text: "El Bolsón para este año " + textoanio + " Ya se encuentra registrado",
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
                    else if(response.data.success === 2){
                        $('#modalPendiente').modal('show');

                        document.getElementById("select-departamento").options.length = 0;

                        $.each(response.data.lista, function( key, val ){
                            $('#select-departamento').append('<option value="0">'+val.nombre+'</option>');
                        });
                    }

                    else if(response.data.success === 3){
                       toastr.success('Bolsón Creado');
                       recargar();
                        $('#modalAgregar').modal('hide');
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


        function informacion(id){
            // id bolson
            window.location.href="{{ url('/admin/bolson/detalle/index/') }}/"+id;
        }


    </script>


@endsection
