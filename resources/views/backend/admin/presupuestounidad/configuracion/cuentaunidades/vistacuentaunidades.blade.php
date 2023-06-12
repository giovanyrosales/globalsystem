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

<!-- VISTA PARA CREAR NUEVOS DEPARTAMENTOS -->

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="container-fluid">
            <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" onclick="modalAgregar()" class="button button-3d button-rounded button-pill button-small">
                <i class="fas fa-pencil-alt"></i>
                Crear Cuenta Unidades
            </button>

            <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" onclick="modalAgregarManual()" class="button button-3d button-rounded button-pill button-small">
                <i class="fas fa-pencil-alt"></i>
                Crear Cuenta Unidades Manual
            </button>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Cuentas de Unidades</h3>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Cuenta Unidad</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <label style="text-align: center">Se genera el Monto Total para todos los Presupuestos Aprobados</label>

                                    <br><br>

                                    <div class="form-group">
                                        <label>Año de Presupuesto</label>
                                        <select class="form-control" id="select-anios">
                                            @foreach( $anios as $dd)
                                                <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="nuevoRegistro()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAgregarManual">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Cuenta Unidad Manual</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo-manual">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <label style="text-align: center">Para Agregar Manualmente primero se debe crear todas las cuentas unidades por Año</label>

                                    <br><br>
                                    <div class="form-group">
                                        <label>Año de Presupuesto</label>
                                        <select class="form-control" id="select-anios-manual">
                                            @foreach( $aniostodos as $dd)
                                                <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Departamento</label>
                                        <select class="form-control" id="select-departamentos">
                                            @foreach( $departamentos as $dd)
                                                <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="nuevoRegistroManual()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- inserción automática: aquí muestra los departamentos que faltan -->
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

                                    <select class="form-control" id="select-departamento-faltante">
                                    </select>

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

    <script type="text/javascript">
        $(document).ready(function(){

            openLoading();

            var ruta = "{{ URL::to('/admin/p/cuentas/unidades/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/p/cuentas/unidades/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function modalAgregarManual(){
            document.getElementById("formulario-nuevo-manual").reset();
            $('#modalAgregarManual').modal('show');
        }

        function nuevoRegistro(){
            var anio = document.getElementById('select-anios').value;

            if(anio === ''){
                toastr.error('Año es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('idanio', anio);

            axios.post(url+'/p/registrar/cuentas/unidades', formData, {
            })
                .then((response) => {
                    closeLoading();
                    $('#modalAgregar').modal('hide');

                    // ya existe un registro para este año para la inserción automática
                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Mismo Año Encontrado',
                            html: "No se puede crear la Cuentas Unidades",
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

                    // departamentos si aprobar aun
                    else if(response.data.success === 2){

                        $('#modalPendiente').modal('show');

                        document.getElementById("select-departamento-faltante").options.length = 0;

                        $.each(response.data.lista, function( key, val ){
                            $('#select-departamento-faltante').append('<option value="0">'+val.nombre+'</option>');
                        });
                    }

                    // creados
                    else if(response.data.success === 3) {

                        recargar();

                        toastr.success('creado');
                    }
                    else {
                        toastr.error('Error al Crear');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al Crear');
                    closeLoading();
                });
        }


        function nuevoRegistroManual(){

            var anio = document.getElementById('select-anios-manual').value;
            var departamento = document.getElementById('select-departamentos').value;

            if(anio === ''){
                toastr.error('Año es requerido');
                return;
            }

            if(departamento === ''){
                toastr.error('Departamento es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('idanio', anio);
            formData.append('iddepartamento', departamento);

            axios.post(url+'/p/registrar/cuentas/unidad/manual', formData, {
            })
                .then((response) => {
                    closeLoading();
                    $('#modalAgregarManual').modal('hide');

                    if (response.data.success === 1) {

                        Swal.fire({
                            title: 'No Creado',
                            html: "Primero se deben crear Todos las Cuentas Unidad Automáticamente",
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
                    // si no esta aprobado presupuesto aun
                    else if (response.data.success === 2) {
                        Swal.fire({
                            title: 'Presupuesto No Aprobado',
                            html: "Verificar El Presupuesto para Aprobación",
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

                    // ya estaba registrado en cuenta unidad
                    else if (response.data.success === 3) {
                        Swal.fire({
                            title: 'Cuenta Unidad Encontrada',
                            html: "Ya estaba Registrado el Departamento para el Año Correspondiente",
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

                    // guardado correctamente
                    else if(response.data.success === 4) {

                        toastr.success('Guardado correctamente');
                        recargar();
                    }

                    // presupuesto unidad no estaba creado
                    else if(response.data.success === 5) {

                        Swal.fire({
                            title: 'Presupuesto No Encontrado',
                            html: "Se debe crear el Presupuesto para el Departamento y año correspondiente",
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

                    else {
                        toastr.error('Error al Crear');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al Crear');
                    closeLoading();
                });

        }


    </script>


@endsection
