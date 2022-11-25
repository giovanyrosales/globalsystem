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
        <div class="container-fluid">
            <div class="row mb-10">
                <div class="col-sm-10">
                    <h1>Revisión de Solicitudes de Material Para Presupuesto</h1>
                    <h1>Año: {{ $anio }}</h1>
                </div>

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

    <div class="modal fade" id="modalInformacion">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-informacion">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Departamento</label>
                                            <input type="text" class="form-control" disabled id="txt-departamento">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Material</label>
                                            <input type="text" class="form-control" disabled id="txt-material">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Unidades</label>
                                            <input type="text" class="form-control" disabled id="txt-unidades">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Periodo</label>
                                            <input type="text" class="form-control" disabled id="txt-periodo">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Monto Solicitado</label>
                                            <input type="text" class="form-control" disabled id="txt-montosolicitado">
                                        </div>


                                        <hr>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Monto Solicitado</label>
                                            <input type="text" class="form-control" disabled id="txt-montosolicitado">
                                        </div>



                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

            let idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/aprobados/solicitud/material/tabla') }}/" + idanio;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function informacion(id){

            openLoading();

            document.getElementById("formulario-informacion").reset();

            axios.post(url+'/p/aprobados/solicitud/material/informacion', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $.each(response.data.infolista, function( key, val ) {

                            $('#txt-departamento').val(val.departamento);
                            $('#txt-material').val(val.material);
                            $('#txt-unidades').val(val.unidades);
                            $('#txt-periodo').val(val.periodo);
                            $('#txt-montosolicitado').val(val.solicitado);

                        });

                        $('#modalInformacion').modal('show');
                    }else{
                        toastr.error('información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('información no encontrada');
                });
        }



    </script>


@endsection
