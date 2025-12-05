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

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Bodega</li>
                    <li class="breadcrumb-item active">Todo Historial de Entradas</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Historial de Todas las Entradas de los Materiales</h3>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Datos</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Precio FACTURA<span style="color: red">*</span></label>
                                        <input type="number" class="form-control" id="precio-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Precio ORDEN COMPRA<span style="color: red">*</span></label>
                                        <input type="number" class="form-control" id="precio-editar-orden" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;"
                            class="button button-rounded button-pill button-small" onclick="editar()">Guardar</button>
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
            openLoading()

            var ruta = "{{ URL::to('/admin/bodega/historial/entradatodos/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/bodega/historial/entradatodos/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function infoEditar(id){
            openLoading();
            document.getElementById("formulario-editar").reset();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/bodega/historial/entradadetalle/informacion', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.info.id);
                        $('#precio-editar').val(response.data.info.precio);
                        $('#precio-editar-orden').val(response.data.info.precio_ordencompra);
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


        function editar(){

            var id = document.getElementById('id-editar').value;
            var precio = document.getElementById('precio-editar').value;
            var precioOrden = document.getElementById('precio-editar-orden').value;


            if(precio === ''){
                toastr.error('Precio es requerido');
                return;
            }


            if(precioOrden === ''){
                toastr.error('Precio Orden es requerido');
                return;
            }

            var reglaNumeroDiesDecimal = /^([0-9]+\.?[0-9]{0,10})$/;


            if (!precio.match(reglaNumeroDiesDecimal)) {
                toastr.error('Precio debe ser decimal (10 decimales) y no negativo');
                return;
            }

            if (precio < 0) {
                toastr.error('Precio no debe ser negativo');
                return;
            }

            if (precio > 9000000) {
                toastr.error('Precio máximo 9 millones');
                return;
            }


            if (!precioOrden.match(reglaNumeroDiesDecimal)) {
                toastr.error('Precio Orden debe ser decimal (10 decimales) y no negativo');
                return;
            }

            if (precioOrden < 0) {
                toastr.error('Precio Orden no debe ser negativo');
                return;
            }

            if (precioOrden > 9000000) {
                toastr.error('Precio Orden máximo 9 millones');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('precio', precio);
            formData.append('precioOrden', precioOrden);

            axios.post(url+'/bodega/historial/entradadetalle/editarprecios', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
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


    </script>


@endsection
