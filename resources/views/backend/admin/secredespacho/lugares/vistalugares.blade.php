@extends('backend.menus.superior')

@section('content-admin-css')

    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">

@stop

<style>
    table{
        table-layout: fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">

            <div class="col-sm-6"></div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Despacho</li>
                    <li class="breadcrumb-item active">Lugares</li>
                </ol>
            </div>

        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            <div class="card card-blue">

                <div class="card-header">

                    <button type="button"
                            class="btn btn-success btn-sm float-left"
                            data-toggle="modal"
                            data-target="#modalNuevoLugar">
                        Nuevo Lugar
                    </button>
                </div>

                <div class="card-body">
                    <div id="tablaDatatable"></div>
                </div>

            </div>

        </div>

    </section>

</div>


<!-- MODAL NUEVO -->
<div class="modal fade" id="modalNuevoLugar">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Nuevo Lugar</h4>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" autocomplete="off" class="form-control" id="nombre" maxlength="100">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" id="descripcion" rows="4" maxlength="2000"></textarea>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cerrar
                </button>

                <button type="button" class="btn btn-success" onclick="guardarLugar()">
                    Guardar
                </button>

            </div>

        </div>

    </div>

</div>


<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Editar Lugar</h4>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="id-editar">

                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-control" id="nombre-editar" autocomplete="off" maxlength="100">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" id="descripcion-editar" autocomplete="off" rows="4" maxlength="2000"></textarea>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cerrar
                </button>

                <button type="button" class="btn btn-primary" onclick="editarLugar()">
                    Actualizar
                </button>

            </div>

        </div>

    </div>

</div>


@extends('backend.menus.footerjs')

@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>

        $(document).ready(function(){

            recargar();

            document.getElementById("divcontenedor").style.display = "block";
        });


        function recargar(){

            var ruta = "{{ url('/admin/secretaria/lugar/tabla') }}";

            $('#tablaDatatable').load(ruta);
        }


        function guardarLugar(){

            var nombre = document.getElementById('nombre').value;
            var descripcion = document.getElementById('descripcion').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            openLoading();

            axios.post("{{ url('/admin/secretaria/lugar/nuevo') }}", {
                nombre: nombre,
                descripcion: descripcion
            })
                .then((response) => {

                    closeLoading();

                    if(response.data.success == 1){

                        toastr.success('Lugar guardado correctamente');

                        $('#modalNuevoLugar').modal('hide');

                        document.getElementById('nombre').value = '';
                        document.getElementById('descripcion').value = '';

                        recargar();

                    }else{
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {

                    closeLoading();

                    toastr.error('Error al guardar');
                });
        }


        function informacionLugar(id){

            openLoading();

            axios.post("{{ url('/admin/secretaria/lugar/informacion') }}", {
                id: id
            })
                .then((response) => {

                    closeLoading();

                    if(response.data.success == 1){

                        $('#modalEditar').modal('show');

                        $('#id-editar').val(response.data.info.id);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#descripcion-editar').val(response.data.info.descripcion);

                    }else{
                        toastr.error('Información no encontrada');
                    }

                })
                .catch((error) => {

                    closeLoading();

                    toastr.error('Información no encontrada');
                });
        }


        function editarLugar(){

            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            openLoading();

            axios.post("{{ url('/admin/secretaria/lugar/editar') }}", {
                id: id,
                nombre: nombre,
                descripcion: descripcion
            })
                .then((response) => {

                    closeLoading();

                    if(response.data.success == 1){

                        toastr.success('Lugar actualizado correctamente');

                        $('#modalEditar').modal('hide');

                        recargar();

                    }else{
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {

                    closeLoading();

                    toastr.error('Error al actualizar');
                });
        }

    </script>

@endsection
