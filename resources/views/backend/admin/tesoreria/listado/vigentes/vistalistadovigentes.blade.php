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
    .dataTables_wrapper .dataTables_info {
        float: left !important;
        text-align: left;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: left !important;
        text-align: left;
        padding-left: 10px;
    }
</style>



<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Registros</li>
                    <li class="breadcrumb-item active">Listado Vigentes</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-green">
                <div class="card-header">
                    <h3 class="card-title">Listado VIGENTES</h3>
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
                    <h4 class="modal-title">MODIFICAR ESTADO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-estado">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group col-md-8">
                                        <label>Estado</label>
                                        <select class="form-control" id="select-estado">
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="actualizarEstado()">Actualizar</button>
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

            var ruta = "{{ URL::to('/admin/tesoreria/listado-vigentes/tabla/index') }}";
            $('#tablaDatatable').load(ruta);


            document.getElementById("divcontenedor").style.display = "block";
        });


        function recargar(){
            var ruta = "{{ url('/admin/tesoreria/listado-vigentes/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function informacion(id){
            window.location.href="{{ url('/admin/tesoreria/listado/edicion/index') }}/"+id;
        }

        function infoBorrar(id){
            Swal.fire({
                title: 'BORRAR REGISTRO',
                text: "",
                icon: 'warning',
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

        // BORRAR LOTE DE ENTRADA COMPLETO
        function borrarRegistro(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/tesoreria/registro/borrar', formData, {
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


        function infoEstado(id){
            openLoading();
            document.getElementById("formulario-estado").reset();

            axios.post(url+'/tesoreria/registro/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);

                        document.getElementById("select-estado").options.length = 0;

                        $.each(response.data.arrayEstados, function( key, val ){
                            if(response.data.info.id_estado === val.id){
                                $('#select-estado').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-estado').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function actualizarEstado(){

            var id = document.getElementById('id-editar').value;
            var estado = document.getElementById('select-estado').value;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('estado', estado);

            axios.post(url+'/tesoreria/actualizar/estado', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalEditar').modal('hide');
                        toastr.success('Actualizado correctamente');
                        recargar()
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
