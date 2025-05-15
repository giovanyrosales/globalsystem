@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

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
                    <li class="breadcrumb-item">Despacho</li>
                    <li class="breadcrumb-item active">Listado Viajes</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Registro de Transporte</h3>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Información (Ver)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="col-md-12">

                            <div class="form-group">
                                <input name="id-editar" type="hidden" id="id-editar">
                                <label for="fecha-editar">Fecha:</label>
                                <input type="date" id="fecha-editar" name="fecha"  class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="nombre-editar">Nombre:</label>
                                <input type="text" id="nombre-editar" name="nombre" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="acompanantes-editar">Cantidad de Acompañantes:</label>
                                <input type="text" id="acompanantes-editar" name="acompanantes"  class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="lugar-editar">Lugar:</label>
                                <input type="text" id="lugar-editar" name="lugar"  class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="subida-editar">Se sube en:</label>
                                <input type="text" id="subida-editar" name="subida"  class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono-editar">Teléfono:</label>
                                <input type="text" id="telefono-editar" name="telefono"  class="form-control" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="editarRegistro()">Actualizar</button>
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
    <script src="{{ asset('plugins/ckeditor5v1/build/ckeditor.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function(){

            var ruta = "{{ URL::to('/admin/secretaria/transporte/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/secretaria/transporte/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalBorrar(id){

            Swal.fire({
                title: 'Borrar?',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    solicitudBorrar(id);
                }
            })
        }

        function solicitudBorrar(id){

            openLoading();

            axios.post(url+'/secretaria/transporte/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                       toastr.success('Borrado');
                       recargar();
                    }else{
                        toastr.error('Error al borrar el registro');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al borrar el registro');
                });
        }
        //Carga información para editar
        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/secretaria/transporte/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.info.id);
                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#acompanantes-editar').val(response.data.info.acompanantes);
                        $('#lugar-editar').val(response.data.info.lugar);
                        $('#subida-editar').val(response.data.info.subida);
                        $('#telefono-editar').val(response.data.info.telefono);
                    } else {
                        toastr.error('Información no encontrada');   
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }
       
        function editarRegistro(){
            var id = document.getElementById('id-editar').value;
            var fecha = document.getElementById('fecha-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var acompanantes = document.getElementById('acompanantes-editar').value;
            var lugar = document.getElementById('lugar-editar').value;
            var subida = document.getElementById('subida-editar').value;
            var telefono = document.getElementById('telefono-editar').value;
            

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('nombre', nombre);
            formData.append('acompanantes', acompanantes);
            formData.append('lugar', lugar);
            formData.append('subida', subida);
            formData.append('telefono', telefono);
            axios.post(url+'/secretaria/transporte/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
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
