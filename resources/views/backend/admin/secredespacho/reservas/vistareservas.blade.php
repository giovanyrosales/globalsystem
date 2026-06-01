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
                    <li class="breadcrumb-item active">Reservas</li>
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
                            data-target="#modalNuevaReserva">
                        Nueva Reserva
                    </button>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

</div>


<!-- MODAL NUEVA RESERVA -->
<div class="modal fade" id="modalNuevaReserva">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Nueva Reserva</h4>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Nombre de quien reserva</label>
                    <input type="text" class="form-control" id="nombre" autocomplete="off" maxlength="100">
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" class="form-control" id="telefono" autocomplete="off" maxlength="50">
                </div>

                <div class="form-group">
                    <label>Lugar que reserva</label>
                    <select class="form-control" id="lugar">
                        <option value="">Seleccione un lugar</option>
                        @foreach($arrayLugares as $dato)
                            <option value="{{ $dato->id }}">{{ $dato->nombre }}</option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" class="form-control" id="fecha">
                </div>

                <div class="form-group">
                    <label>Hora Inicio</label>
                    <input type="time" class="form-control" id="hora_inicio">
                </div>

                <div class="form-group">
                    <label>Hora Fin</label>
                    <input type="time" class="form-control" id="hora_fin">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" id="descripcion" rows="3"></textarea>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Cerrar
                </button>

                <button type="button"
                        class="btn btn-success"
                        onclick="guardarReserva()">
                    Guardar
                </button>

            </div>

        </div>
    </div>
</div>

<!-- MODAL EDITAR RESERVA -->
<div class="modal fade" id="modalEditarReserva">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Editar Reserva</h4>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="id-editar">

                <div class="form-group">
                    <label>Nombre de quien reserva</label>
                    <input type="text" class="form-control" id="nombre-editar">
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" class="form-control" id="telefono-editar">
                </div>

                <div class="form-group">
                    <label>Lugar que reserva</label>

                    <select class="form-control" id="lugar-editar">
                        <option value="">Seleccione un lugar</option>

                        @foreach($arrayLugares as $dato)
                            <option value="{{ $dato->id }}">{{ $dato->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" class="form-control" id="fecha-editar">
                    </div>

                    <div class="form-group">
                        <label>Hora inicio</label>
                        <input type="time" class="form-control" id="hora_inicio-editar">
                    </div>

                    <div class="form-group">
                        <label>Hora fin</label>
                        <input type="time" class="form-control" id="hora_fin-editar">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cerrar
                </button>

                <button type="button" class="btn btn-primary" onclick="editarReserva()">
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script>

        $(document).ready(function(){
            recargar();

            $('#lugar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#lugar-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });

        function recargar(){
            var ruta = "{{ url('/admin/secretaria/reservas/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function guardarReserva(){

            var nombre = document.getElementById('nombre').value;
            var telefono = document.getElementById('telefono').value;
            var lugar = document.getElementById('lugar').value;
            var fecha = document.getElementById('fecha').value;
            var hora_inicio = document.getElementById('hora_inicio').value;
            var hora_fin = document.getElementById('hora_fin').value;
            var descripcion = document.getElementById('descripcion').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(telefono === ''){
                toastr.error('Teléfono es requerido');
                return;
            }

            if(lugar === ''){
                toastr.error('Lugar es requerido');
                return;
            }

            if(fecha === ''){
                toastr.error('Fecha es requerida');
                return;
            }

            if(hora_inicio === ''){
                toastr.error('Hora inicio es requerida');
                return;
            }

            if(hora_fin === ''){
                toastr.error('Hora fin es requerida');
                return;
            }

            if(hora_inicio >= hora_fin){
                toastr.error('La hora fin debe ser mayor que la hora inicio');
                return;
            }

            let formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('lugar', lugar);
            formData.append('fecha', fecha);
            formData.append('hora_inicio', hora_inicio);
            formData.append('hora_fin', hora_fin);
            formData.append('descripcion', descripcion);

            axios.post("{{ url('/admin/secretaria/reservas/nuevo') }}", formData)
                .then((response) => {

                    if(response.data.success == 1){

                        toastr.success('Reserva guardada');

                        $('#modalNuevaReserva').modal('hide');

                        document.getElementById('nombre').value = '';
                        document.getElementById('telefono').value = '';
                        document.getElementById('fecha').value = '';
                        document.getElementById('hora_inicio').value = '';
                        document.getElementById('hora_fin').value = '';
                        document.getElementById('descripcion').value = '';

                        recargar();

                    }else if(response.data.success == 3){

                        toastr.error(response.data.mensaje);

                    }else{

                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    console.log(error.response);
                    toastr.error('Error al guardar');
                });
        }



        function informacionReserva(id){

            axios.post("{{ url('/admin/secretaria/reservas/informacion') }}", {
                id: id
            })
                .then((response) => {

                    if(response.data.success == 1){

                        $('#modalEditarReserva').modal('show');

                        $('#id-editar').val(response.data.info.id);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#telefono-editar').val(response.data.info.telefono);
                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#hora_inicio-editar').val(response.data.info.hora_inicio);
                        $('#hora_fin-editar').val(response.data.info.hora_fin);

                        $('#descripcion-editar').val(response.data.info.descripcion);

                        document.getElementById("lugar-editar").options.length = 0;

                        $.each(response.data.arrayLugares, function( key, val ){
                            if(response.data.info.id_lugares == val.id){
                                $('#lugar-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#lugar-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });


                    }else{
                        toastr.error('Información no encontrada');
                    }

                })
                .catch((error) => {
                    console.log(error.response);
                    toastr.error('Información no encontrada');
                });
        }

        function editarReserva() {

            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var telefono = document.getElementById('telefono-editar').value;
            var lugar = document.getElementById('lugar-editar').value;
            var fecha = document.getElementById('fecha-editar').value;
            var hora_inicio = document.getElementById('hora_inicio-editar').value;
            var hora_fin = document.getElementById('hora_fin-editar').value;

            if (nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            if (telefono === '') {
                toastr.error('Teléfono es requerido');
                return;
            }

            if (lugar === '') {
                toastr.error('Lugar es requerido');
                return;
            }

            if (fecha === '') {
                toastr.error('Fecha es requerida');
                return;
            }

            if (hora_inicio === '') {
                toastr.error('Hora inicio es requerida');
                return;
            }

            if (hora_fin === '') {
                toastr.error('Hora fin es requerida');
                return;
            }

            if (hora_inicio >= hora_fin) {
                toastr.error('La hora fin debe ser mayor que la hora inicio');
                return;
            }

            let formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('lugar', lugar);
            formData.append('fecha', fecha);
            formData.append('hora_inicio', hora_inicio);
            formData.append('hora_fin', hora_fin);


            axios.post(url + '/secretaria/reservas/editar', formData, {})
                .then((response) => {
                    closeLoading();
                    if (response.data.success == 1) {
                        toastr.success('Reserva actualizada');
                        $('#modalEditarReserva').modal('hide');
                        recargar();
                    } else if (response.data.success == 3) {
                        toastr.error(response.data.mensaje);
                    } else {
                        toastr.error('Error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function modalBorrar(id){
            Swal.fire({
                title: 'Borrar?',
                text: '',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if(result.isConfirmed){
                    borrarReserva(id);
                }
            });
        }

        function borrarReserva(id){
            axios.post("{{ url('/admin/secretaria/reservas/borrar') }}", {
                id: id
            })
                .then((response) => {
                    if(response.data.success == 1){
                        toastr.success('Reserva eliminada');
                        recargar();
                    }else{
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {

                    console.log(error.response.data);

                    toastr.error('Error al borrar');
                });
        }

    </script>

@endsection
