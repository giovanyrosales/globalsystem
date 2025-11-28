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

                <label>LOTE: {{ $info->lote }}</label>
                <br>

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Bodega</li>
                    <li class="breadcrumb-item active">Detalle - Historial Entradas</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado De Entradas - Detalle</h3>
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
                                        <label>Código de Producto (Opcional)</label>
                                        <input type="text" class="form-control" maxlength="100" id="codigoproducto-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Precio (Opcional)</label>
                                        <input type="text" class="form-control" maxlength="100" id="precio-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Número ITEM (opcional)</label>
                                        <input type="text" maxlength="100" class="form-control" id="numeroItem-editar" autocomplete="off">
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
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/bodega/historial/entradadetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/bodega/historial/entradadetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function infoBorrar(id){
            Swal.fire({
                title: 'ADVERTENCIA',
                text: "Esto eliminará todo el ingreso de este producto. Si hubo salidas de producto también se eliminarán",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id)
                }
            })
        }

        function borrarRegistro(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/bodega/historial/entradadetalle/borraritem', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Borrado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
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
                        $('#codigoproducto-editar').val(response.data.info.codigo_producto);
                        $('#precio-editar').val(response.data.info.precio);
                        $('#numeroItem-editar').val(response.data.info.numero_item);
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
            var codigo = document.getElementById('codigoproducto-editar').value;
            var precio = document.getElementById('precio-editar').value;
            var numeroItem = document.getElementById('numeroItem-editar').value;

            if(precio === ''){
                toastr.error('Precio es requerido');
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



            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('codigo', codigo);
            formData.append('precio', precio);
            formData.append('numeroitem', numeroItem);

            axios.post(url+'/bodega/historial/entradadetalle/editar', formData, {
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
