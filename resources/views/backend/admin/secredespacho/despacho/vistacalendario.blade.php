@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

    <!-- FullCalendar v5 (sin jQuery) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar/daygrid@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar/interaction@5.11.3/main.min.js"></script>
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Despacho</li>
                    <li class="breadcrumb-item active">Calendario</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Calendario de Viajes</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                        <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>  


<!-- Modal -->
<div class="modal fade" id="registroModal" tabindex="-1" role="dialog" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroModalLabel">Información a Registrar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="registroForm">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="acompanantes">Número de Acompañantes:</label>
                    <input type="text" id="acompanantes" name="acompanantes" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="lugar">Lugar de Llegada:</label>
                    <input type="text" id="lugar" name="lugar" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="subida">Se sube en:</label>
                    <input type="text" id="subida" name="subida" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="number" id="telefono" name="telefono" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
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
    <script>
         document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            events: url2+'/secretaria/calendario/informacion', // Ruta para cargar eventos desde el backend
            dateClick: function (info) {
                //console.log('Día seleccionado:', info.dateStr);
                abrirModal(info.dateStr);
            }
        });

        calendar.render();
    });

    // Función para abrir el modal y asignar la fecha seleccionada
    function abrirModal(fecha) {
            $('#fecha').val(fecha); // Establece la fecha en el input del formulario
            $('#registroModal').modal('show'); // Muestra el modal
        }

    // Guardar el formulario
        $(document).ready(function () {
        $('#registroForm').on('submit', function (e) {
            e.preventDefault(); // Evita el envío del formulario por defecto

            $.ajax({
                url: url2+'/secretaria/calendario/nuevo', // Ruta para guardar el registro
                method: 'POST',
                data: $(this).serialize(), // Serializa los datos del formulario
                success: function (response) {
                    toastr.success('Registro guardado con éxito', '', {
                    onHidden: function () {
                        // Recarga la página para actualizar el calendario
                        location.reload();
                    } }); // Muestra un mensaje de éxito
                    $('#registroModal').modal('hide'); // Cierra el modal
                    },
                error: function (xhr) {
                    toastr.error('Hubo un error al guardar el registro'); // Muestra un mensaje de error
                    //console.error('Error al guardar:', xhr.responseText); // Log del error en la consola
                }
            });
        });
    });
    </script>
@endsection