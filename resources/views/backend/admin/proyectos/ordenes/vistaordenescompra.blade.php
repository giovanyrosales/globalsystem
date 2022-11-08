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
                                            <input class="col-md-6" type="time" id="horaacta" value="<?php echo date('h:i'); ?>">
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

    <!-- CUANTOS MATERIALES QUIERE VER POR HOJA -->
    <div class="modal fade" id="modalHoja">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Materiales por Página</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id-pagina"/>
                </div>

                <div class="form-group col-md-12">
                    <label>Cantidad de Materiales por Página</label>
                    <input type="number" id="cantidad-pagina" class="form-control" value="15" placeholder="0">
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-success" type="button" onclick="generarImpresion()">Imprimir Orden</button>
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

        function verProcesadas(id){
            window.location.href="{{ url('/admin/cotizacion/proyecto/detalle') }}/" + id;
        }

        function abrirModalAnular(id){

            Swal.fire({
                title: 'Anular Orden',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#808080',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Anular',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    anularOrden(id);
                }
            })
        }

        function anularOrden(id){

            openLoading();

            axios.post(url+'/ordenes/proyecto/anular/compra',{
                'id': id
            })
                .then((response) => {
                   closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Acta Encontrada',
                            text: "La orden de compra tiene un Acta Generada, no se puede Anular esta orden",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#808080',
                            confirmButtonText: 'Recargar',
                            reverseButtons: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }
                    else if(response.data.success === 2){
                        toastr.success('Orden Anulada!');
                        recargar();
                    }
                    else if(response.data.success === 3) {

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
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
                        toastr.error('Error al Anular Orden');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al Anular Orden');
                });
        }

        function abrirModalActa(id){
            $('#modalGenerarActa').modal('show');
            $('#idacta').val(id); // id orden
        }

        function enviarModalGenerarActa(){
            var idorden = document.getElementById('idacta').value;
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
            formData.append('idorden', idorden);
            formData.append('horaacta', horaacta);
            formData.append('fechaacta', fechaacta);

            axios.post(url+'/ordenes/proyecto/generar/acta', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
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
                    else if(response.data.success === 2) {
                        toastr.success('Acta creada correctamente');
                        recargar();
                        $('#modalGenerarActa').modal('hide');
                        window.open("{{ URL::to('admin/ordenes/acta/reporte') }}/" + response.data.actaid);
                    }
                    else{
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

        function Imprimir(id){
            // PREGUNTAR CUANDO MATERIALES QUIERE POR HOJA
            $('#id-pagina').val(id);
            $('#modalHoja').modal('show');
        }

        function generarImpresion(){

            var id = document.getElementById('id-pagina').value;
            var cantidad = document.getElementById('cantidad-pagina').value;

            if(cantidad === ''){
                toastr.error('Cantidad por Página es Requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!cantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser número Entero');
                return;
            }

            if(cantidad <= 0){
                toastr.error('Cantidad no debe tener negativos o Cero');
                return;
            }

            if(cantidad.length > 3){
                toastr.error('Cantidad máximo 3 dígitos de límite');
                return;
            }

            $('#modalHoja').modal('hide');

            window.open("{{ URL::to('admin/ordenes/proyecto/pdf') }}/" + id + "/" + cantidad);
        }

    </script>


@endsection
