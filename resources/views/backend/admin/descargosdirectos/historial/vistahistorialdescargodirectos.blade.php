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
                    <h1>Historial de Descargos Directos</h1>
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

    <div class="modal fade" id="modalProveedor">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-proveedor">
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
                                        <label>Cuenta Unidad que Sube</label>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Objeto Específico</label>
                                            <input type="text" class="form-control" disabled id="txt-objespeci-subir">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Inicial que tenía antes de ser modificado</label>
                                            <input type="text" class="form-control" disabled id="txt-antessubir">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>La Cuenta Unidad Fue Creada</label>
                                            <input type="text" class="form-control" disabled id="txt-creada">
                                        </div>


                                        <hr>
                                        <label>Cuenta Unidad que Baja</label>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Objeto Específico</label>
                                            <input type="text" class="form-control" disabled id="txt-objespeci-bajar">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Inicial que tenía antes de ser modificado</label>
                                            <input type="text" class="form-control" disabled id="txt-antesbajar">
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

    <div class="modal fade" id="modalProyecto">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-proveedor">
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
                                        <label>Cuenta Unidad que Sube</label>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Objeto Específico</label>
                                            <input type="text" class="form-control" disabled id="txt-objespeci-subir">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Inicial que tenía antes de ser modificado</label>
                                            <input type="text" class="form-control" disabled id="txt-antessubir">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>La Cuenta Unidad Fue Creada</label>
                                            <input type="text" class="form-control" disabled id="txt-creada">
                                        </div>


                                        <hr>
                                        <label>Cuenta Unidad que Baja</label>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Objeto Específico</label>
                                            <input type="text" class="form-control" disabled id="txt-objespeci-bajar">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Inicial que tenía antes de ser modificado</label>
                                            <input type="text" class="form-control" disabled id="txt-antesbajar">
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

    <div class="modal fade" id="modalContribucion">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-contribucion">
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
                                        <label>Cuenta Unidad que Sube</label>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Objeto Específico</label>
                                            <input type="text" class="form-control" disabled id="txt-objespeci-subir">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Inicial que tenía antes de ser modificado</label>
                                            <input type="text" class="form-control" disabled id="txt-antessubir">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>La Cuenta Unidad Fue Creada</label>
                                            <input type="text" class="form-control" disabled id="txt-creada">
                                        </div>


                                        <hr>
                                        <label>Cuenta Unidad que Baja</label>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Objeto Específico</label>
                                            <input type="text" class="form-control" disabled id="txt-objespeci-bajar">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Inicial que tenía antes de ser modificado</label>
                                            <input type="text" class="form-control" disabled id="txt-antesbajar">
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

            var ruta = "{{ URL::to('/admin/descargos/directos/historial/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function informacion(e){

            let id = e.id;
            let tipo = e.tipodescargo;

            openLoading();

            document.getElementById("formulario-proveedor").reset();
            document.getElementById("formulario-proyecto").reset();
            document.getElementById("formulario-contribucion").reset();

            axios.post(url+'/', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $.each(response.data.infolista, function( key, val ) {

                            $('#txt-departamento').val(val.departamento);
                            $('#txt-departamento').val(val.departamento);
                            $('#txt-material').val(val.material);
                            $('#txt-unidades').val(val.unidades);
                            $('#txt-periodo').val(val.periodo);
                            $('#txt-montosolicitado').val(val.solicitado);

                            $('#txt-objespeci-subir').val(val.txtobjsube);
                            $('#txt-antessubir').val(val.antessubir);


                            $('#txt-objespeci-bajar').val(val.txtobjbaja);
                            $('#txt-antesbajar').val(val.antesbajar);

                            $('#txt-creada').val(val.txtcreada);


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
