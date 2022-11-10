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

    .modal-xl {
        max-width: 90% !important;
    }

</style>

<div class="content-wrapper" style="display: none" id="divcontenedor">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label style="font-size: 18px">Presupuesto Año: {{ $infoAnio->nombre }}</label>
                    </div>


                    <div class="form-group col-md-3">
                        <label style="color:#191818">Estado</label>
                        <br>
                        <div>
                            <select class="form-control" id="select-estado" onchange="actualizarEstado()">
                                @foreach($arrayestado as $item)

                                    @if($estado == $item->id)
                                        <option value="{{$item->id}}" selected="selected">{{$item->nombre}}</option>
                                    @else
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    <section class="content" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">

                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="tablaDatatable">

                                        </div>
                                    </div>
                                </div>
                            </section>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- PROYECTOS SOLICITUD -->
    <div class="modal fade" id="modalNuevoProyecto">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitud de Proyecto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo-proyecto">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Descripción</label>
                                            <input type="hidden" id="proyecto-id-aborrar">
                                            <input type="text" class="form-control" disabled id="proyecto-descripcion-nuevo">
                                        </div>

                                        <div class="form-group">
                                            <label>Monto ($)</label>
                                            <input type="number" class="form-control" disabled id="proyecto-costo-nuevo">
                                        </div>

                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <select class="form-control" id="select-obj-proyecto">
                                                @foreach( $arrayObjeto as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->codigo }} - {{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Fuente de Recursos para Año {{ $infoAnio->nombre }}</label>
                                            <select class="form-control" id="select-fuenter-proyecto">
                                                @foreach( $arrayFuente as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->codigo }} - {{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Línea de Trabajo</label>
                                            <select class="form-control" id="select-linea-proyecto">
                                                @foreach( $arrayLinea as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->codigo }} - {{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <label>Área de Gestión</label>
                                            <select class="form-control" id="select-area-proyecto">
                                                @foreach( $arrayGestion as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->codigo }} - {{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="verificarNuevoProyecto()">Agregar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- MATERIALES SOLICITUD -->
    <div class="modal fade" id="modalNuevoMaterial">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitud de Materiales</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo-material">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <select class="form-control" id="select-obj-material">
                                                @foreach( $arrayObjeto as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->codigo }} - {{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Descripción</label>
                                            <input type="hidden" id="material-id-aborrar">
                                            <input type="text" class="form-control" disabled id="material-descripcion-nuevo">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Costo ($)</label>
                                            <input type="number" class="form-control" disabled id="material-costo-nuevo">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Cantidad</label>
                                            <input type="number" class="form-control" disabled id="material-cantidad-nuevo">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Periodo</label>
                                            <input type="number" class="form-control" disabled id="material-periodo-nuevo">
                                        </div>

                                        <div class="form-group">
                                            <label>Unidad de Medida</label>
                                            <select class="form-control" id="select-material-unidadmedida">
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="verificarNuevoMaterial()">Agregar</button>
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

            openLoading();

            let iddepa = {{ $iddepa }};
            let idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/departamento/presupuesto/contenedor/') }}/" + iddepa + "/" + idanio;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";


            $('#select-obj-proyecto').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-fuenter-proyecto').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-linea-proyecto').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-area-proyecto').select2({
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

        function actualizarEstado(){

            var estado = document.getElementById('select-estado').value;

            var idpresupuesto = {{ $idpre }};

            let formData = new FormData();
            formData.append('idpresupuesto', idpresupuesto);
            formData.append('idestado',estado);

            axios.post(url+'/p/presupuesto/unidad/cambiar/estado', formData, {
            })
                .then((response) => {

                    if(response.data.success === 1) {

                        Swal.fire({
                            title: 'Presupuesto Vacío',
                            text: "El Presupuesto esta creado, pero no tiene ningún Material Registrado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Recargar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Estado Actualizado',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }else{
                        toastr.error('Error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }

        function verificarNuevoProyecto(){

            var idpresupuesto = {{ $idpre }};

            var descripcion = document.getElementById('proyecto-descripcion-nuevo').value;
            var costo = document.getElementById('proyecto-costo-nuevo').value;

            var objespeci = document.getElementById('select-obj-proyecto').value;
            var fuenter = document.getElementById('select-fuenter-proyecto').value;
            var linea = document.getElementById('select-linea-proyecto').value;
            var areagestion = document.getElementById('select-area-proyecto').value;

            // id de tabla proyectos pendientes para borrarla
            var proidborrar = document.getElementById('proyecto-id-aborrar').value;

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            // ****

            if(descripcion === ''){
                toastr.error('Descripción es requerido');
                return;
            }

            if(descripcion.length > 300){
                toastr.error('Descripción máximo 300 caracteres');
                return;
            }

            // ****

            if(costo === ''){
                toastr.error('Costo es requerido');
                return;
            }

            if(!costo.match(reglaNumeroDosDecimal)) {
                toastr.error('Costo debe ser número Decimal Positivo. Solo se permite 2 Decimales');
                return;
            }

            if(costo < 0){
                toastr.error('Costo no permite números negativos');
                return;
            }

            if(costo > 99000000){
                toastr.error('Costo máximo 99 millones de límite');
                return;
            }

            if(objespeci === ''){
                toastr.error('Objeto Específico es requerido');
                return;
            }

            if(fuenter === ''){
                toastr.error('Fuente de Recursos es requerido');
                return;
            }

            if(linea === ''){
                toastr.error('Línea de Trabajo es requerido');
                return;
            }

            if(areagestion === ''){
                toastr.error('Área de Gestión es requerido');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', idpresupuesto);
            formData.append('descripcion', descripcion);
            formData.append('costo', costo);
            formData.append('objeto', objespeci);
            formData.append('fuenter', fuenter);
            formData.append('linea', linea);
            formData.append('areagestion', areagestion);
            formData.append('proidborrar', proidborrar);

            axios.post(url+'/p/registrar/proyecto/presupuesto/unidad', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalNuevoProyecto').modal('hide');

                        Swal.fire({
                            title: 'Proyecto Registrado',
                            text: "",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
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

    </script>

@endsection
