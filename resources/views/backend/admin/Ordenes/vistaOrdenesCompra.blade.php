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
                <h1>Ordenes de Compra Procesadas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Ordenes</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Ordenes de Compra</h3>
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

    <div class="modal fade" id="modalAnular">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Anular Orden de Compra</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idorden"/> <!-- id de la orden para Anularla -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-danger" id="btnBorrar" type="button" onclick="anularOrden()">Sí, Anular</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalGenerarActa">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detalles de Acta</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-crear-orden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="col-md-12">Hora de la acta</label>
                                            <input class="col-md-6" type="time" id="horaacta" min="09:00" max="18:00" value="<?php echo date('h:i'); ?>">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Fecha de la Acta</label>
                                            <input type="date" id="fechaacta" class="form-control" value="<?php echo date("Y-m-d");?>">
                                            <input type="hidden" id="idacta" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="enviarModalGenerarActa()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/ordenes/compras/tabla-index') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/ordenes/compras/tabla-index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAnular(id){
            $('#modalAnular').modal('show');
            $('#idorden').val(id);
        }

        function anularOrden(){
            var id = document.getElementById("idorden").value;

            openLoading();

            axios.post(url+'/ordenes/anular/compra',{
                'id': id
            })
                .then((response) => {
                   closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Orden Anulada!')
                        $('#modalAnular').modal('hide');
                        recargar();
                    }else{
                        toastr.error('Error', 'No se pudo anular la Orden');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error', 'No se pudo anular la Orden');
                });
        }

        function abrirModalActa(id){
            $('#modalGenerarActa').modal('show');
            $('#idacta').val(id);
        }

        function enviarModalGenerarActa(){
            var idacta = document.getElementById('idacta').value;
            var horaacta = document.getElementById('horaacta').value;
            var fechaacta = document.getElementById('fechaacta').value;

            if(horaacta === ''){
                toastr.error('Hora para Acta es requerido');
                return;
            }

            if(fechaacta === ''){
                toastr.error('Fecha para Acta es requerida');
                return;
            }

            let formData = new FormData();
            formData.append('idacta', idacta);
            formData.append('horaacta', horaacta);
            formData.append('fechaacta', fechaacta);

            axios.post(url+'/ordenes/generar/acta', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Acta creada correctamente');
                        recargar();
                        $('#modalGenerarActa').modal('hide');
                        window.open("{{ URL::to('admin/ordenes/acta/reporte') }}/" + response.data.actaid);
                    }else{
                        toastr.error('Error al guardar Acta');
                    }
                })
                .catch((error) => {
                   closeLoading();
                    toastr.error('Error al guardar Acta');
                });
        }

        function imprimirActa(actaid){
            window.open("{{ URL::to('admin/ordenes/acta/reporte') }}/" + actaid);
        }

    </script>


@endsection
