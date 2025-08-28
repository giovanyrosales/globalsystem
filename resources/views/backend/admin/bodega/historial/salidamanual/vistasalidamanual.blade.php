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
                    <li class="breadcrumb-item">Bodega</li>
                    <li class="breadcrumb-item active">Historial de Salidas</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Historial de Salidas</h3>
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
                    <h4 class="modal-title">Editar Datos</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-datos">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de Salida</label>
                                        <input type="date" class="form-control" id="fecha-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Número Solicitud (Opcional)</label>
                                        <input type="text" maxlength="300" class="form-control" id="numero-solicitud-editar" autocomplete="off">
                                    </div>


                                    <div class="form-group col-md-4" style="margin-top: 5px">
                                        <label class="control-label" style="color: #686868">Tipo de Salida</label>
                                        <select id="select-tiposalida-editar" class="form-control">
                                            <option value="1">Salida con Solicitud</option>
                                            <option value="2">Salida por Desperfecto</option>
                                        </select>
                                    </div>

                                    <div class="form-group" style="margin-top: 15px">
                                        <label style="color:#191818">Unidad (Opcional):</label>
                                        <br>
                                        <div>
                                            <select class="form-control" id="select-unidades-editar">
                                            </select>
                                        </div>
                                    </div>


                                    <hr>

                                    <div class="form-group">
                                        <label>Observación (Opcional)</label>
                                        <input type="text" maxlength="300" class="form-control" id="observacion-editar" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;"
                            class="button button-rounded button-pill button-small" onclick="editarDatos()">Guardar</button>
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
            openLoading()

            var ruta = "{{ URL::to('/admin/bodega/historial/salidasmanual/tabla') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-unidades-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/bodega/historial/salidasmanual/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function vistaDetalle(idsolicitud){
            window.location.href="{{ url('/admin/bodega/historial/salidamanualdetalle/index') }}/" + idsolicitud;
        }


        function modalInformacion(id){
            openLoading();
            document.getElementById("formulario-datos").reset();

            axios.post(url+'/bodega/historial/salidamanualdetalle/datosinformacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        $('#id-editar').val(response.data.info.id);

                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#numero-solicitud-editar').val(response.data.info.numero_solicitud);
                        $('#observacion-editar').val(response.data.info.observacion);


                        document.getElementById("select-unidades-editar").options.length = 0;

                        $('#select-unidades-editar').append('<option value="" selected="selected">Seleccionar Opción</option>');

                        $.each(response.data.arrayUnidades, function( key, val ){
                            if(response.data.info.id_unidad_manual == val.id){
                                $('#select-unidades-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-unidades-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        if(response.data.info.estado_salida === 1){
                            $('#select-tiposalida-editar').prop('selectedIndex', 0).change();
                        }else{
                            $('#select-tiposalida-editar').prop('selectedIndex', 1).change();
                        }



                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function editarDatos(){

            var id = document.getElementById('id-editar').value;
            var fecha = document.getElementById('fecha-editar').value;
            var numeroSolicitud = document.getElementById('numero-solicitud-editar').value;
            var idunidad = document.getElementById('select-unidades-editar').value;
            var observacion = document.getElementById('observacion-editar').value;
            var idTipoSalida = document.getElementById('select-tiposalida-editar').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('numeroSolicitud', numeroSolicitud);
            formData.append('idunidad', idunidad);
            formData.append('observacion', observacion);
            formData.append('idtiposolicitud', idTipoSalida); // tipo solitud


            axios.post(url+'/bodega/historial/salidamanualdetalle/actualizar', formData, {
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
