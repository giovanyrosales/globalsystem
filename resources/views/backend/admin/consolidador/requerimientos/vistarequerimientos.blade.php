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
            <div class="col-sm-12">

                <div class="row">
                    <h1 style="margin-left: 15px">Listado de Requerimientos Pendientes</h1>
                </div>
                <button type="button" style="font-weight: bold; margin-top: 15px; background-color: #28a745; color: white !important;"
                        onclick="modalAgrupar()" class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-plus"></i>
                    Agrugar
                </button>

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



    <div class="modal fade" id="modalDetalle">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detalle</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaDetalle">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>





    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agrupados</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-5">

                                        <div class="form-group">
                                            <label>Fecha de cotización:</label>
                                            <input type="date" id="fecha-agrupados" class="form-control">
                                        </div>


                                    </div>

                                    <div class="col-md-5">

                                        <label>Descripción</label>

                                        <input type="text" maxlength="800" id="descripcion-agrupados" placeholder="Descripción (Opcional)" class="form-control">

                                    </div>


                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <!-- Selección del lado izquierdo -->
                                                <div class="col-xs-7 col-md-7 col-sm-7">
                                                    <label>Lista de Items de Requisición</label>
                                                    <select name="from[]" id="mySideToSideSelect" class="form-control" size="8" multiple="multiple">

                                                    </select>
                                                </div>

                                                <!-- Botones de acción -->
                                                <div class="col-xs-2 col-md-2 col-sm-2">

                                                    <label>&nbsp;</label>
                                                    <button type="button" id="mySideToSideSelect_rightAll" class="btn btn-secondary col-xs-12 col-md-12 col-sm-12 mt-1"><i class="fas fa-forward"></i></button>
                                                    <button type="button" id="mySideToSideSelect_rightSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-right"></i></button>
                                                    <button type="button" id="mySideToSideSelect_leftSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-left"></i></button>
                                                    <button type="button" id="mySideToSideSelect_leftAll" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-backward"></i></button>
                                                </div>

                                                <!-- Selección del lado derecho -->
                                                <div class="col-xs-3 col-md-3 col-sm-3">
                                                    <label>Lista de Items para Agrupación</label>
                                                    <select name="to[]" id="mySideToSideSelect_to" class="form-control" size="8" multiple="multiple"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="verificar()">Guardar</button>
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

    <script src="{{ asset('js/multiselect.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            var id = {{ $idanio }};
            var ruta = "{{ url('/admin/consolidador/requerimientos/pendientes/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            $('#mySideToSideSelect').multiselect();

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var id = {{ $idanio }};
            var ruta = "{{ url('/admin/consolidador/requerimientos/pendientes/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }


        // MUESTRA EL DETALLE DE REQUERIMIENTO

        function detalleRequisicion(id){
            // id requisicion

            var ruta = "{{ URL::to('/admin/consolidador/info/requisicion/detalle') }}/" + id;
            $('#tablaDetalle').load(ruta);
            $('#modalDetalle').modal('show');
        }





        function modalAgrupar(){

            var idanio = {{ $idanio }};
            document.getElementById("mySideToSideSelect").options.length = 0;
            document.getElementById("mySideToSideSelect_to").options.length = 0;

            openLoading();

            axios.post(url+'/consolidatos/listado/ordenado/paraselect', {
                'anio': idanio
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('bien');
                        document.getElementById("formulario-nuevo").reset();
                        $('#modalAgregar').modal('show');
                    }else{
                        toastr.error('mal');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }


        function removeOptionsFromSelect(selectElement) {
            var i, L = selectElement.options.length - 1;
            for(i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }



    </script>


@endsection
