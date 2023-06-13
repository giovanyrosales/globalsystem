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
            <div class="col-sm-12">

                <div class="row">
                    <h1 style="margin-left: 15px">Listado de Requerimientos Pendientes</h1>
                </div>
                <button type="button" style="font-weight: bold; margin-top: 15px; background-color: #28a745; color: white !important;"
                        onclick="modalAgrupar()" class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-plus"></i>
                    Agrupar
                </button>

            </div>

        </div>
    </section>



    <div class="modal fade" id="modalDetalle">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detalle</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaDetalle">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>



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



    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agrupados</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Fecha:</label>
                                                <input type="date" id="fecha-agrupados" class="form-control">
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <label>Nombre o Destino (Opcional):</label>
                                            <input type="text" maxlength="800" autocomplete="off" id="nombreodestino-agrupados" placeholder="Nombre o Destino del Proyecto" class="form-control">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Justificación:</label>
                                                <input type="text" maxlength="800" autocomplete="off" id="justificacion-agrupados" placeholder="Justificación " class="form-control">
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <label>Forma de Entrega (Parcial o Total):</label>
                                            <input type="text" maxlength="350" autocomplete="off" id="entrega-agrupados" placeholder="Forma de Entrega" class="form-control">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Plazo de Entrega:</label>
                                                <input type="text" maxlength="350" autocomplete="off" id="plazo-agrupados" placeholder="Plazo o tiempo de entrega" class="form-control">
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <label>Lugar de Entrega</label>
                                            <input type="text" maxlength="350" autocomplete="off" id="lugar-agrupados" placeholder="Lugar de entrega" class="form-control">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Forma de Contratación (Contrato u Orden):</label>
                                                <input type="text" maxlength="350" autocomplete="off" id="forma-agrupados" placeholder="Forma de Pago" class="form-control">
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <label>Otras Condiciones Especificar (Opcional):</label>
                                            <input type="text" maxlength="350" autocomplete="off" id="otros-agrupados" placeholder="Otras Condiciones (Opcional)" class="form-control">
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-6">

                                            <label>Administrador</label>
                                            <select class="custom-select" id="select-administrador">
                                                @foreach($adminContrato as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-6">

                                            <label>Evaluador Técnico 1</label>
                                            <select class="custom-select" id="select-evaluador">
                                                @foreach($adminContrato as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Evaluador Técnico 2</label>
                                            <select class="custom-select" id="select-evaluador2">
                                                <option value="">Seleccionar Opción</option>
                                                @foreach($adminContrato as $dd)
                                                    <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>


                                    <br><br>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <!-- Selección del lado izquierdo -->
                                                <div class="col-xs-7 col-md-7 col-sm-7">
                                                    <label>Lista de Items de Requisición</label>
                                                    <select name="from[]" id="mySideToSideSelect" class="form-control" size="8" multiple="multiple">

                                                    </select>
                                                </div>

                                                <!-- Botones de acción -->
                                                <div class="col-xs-2 col-md-2 col-sm-2">

                                                    <label>&nbsp;</label>
                                                    <button type="button" id="mySideToSideSelect_rightAll" class="btn btn-secondary col-xs-12 col-md-12 col-sm-12 mt-1"><i class="fas fa-forward"></i></button>
                                                    <button type="button" id="mySideToSideSelect_rightSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-right"></i></button>
                                                    <button type="button" id="mySideToSideSelect_leftSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-left"></i></button>
                                                    <button type="button" id="mySideToSideSelect_leftAll" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-backward"></i></button>
                                                </div>

                                                <!-- Selección del lado derecho -->
                                                <div class="col-xs-3 col-md-3 col-sm-3">
                                                    <label>Lista de Items para Agrupación</label>
                                                    <select name="to[]" id="mySideToSideSelect_to" class="form-control" size="8" multiple="multiple"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="verificar()">Guardar</button>
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

    <script src="{{ asset('js/multiselect.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            var id = {{ $idanio }};
            var ruta = "{{ url('/admin/consolidador/requerimientos/pendientes/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);


            $('#mySideToSideSelect').multiselect();

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var id = {{ $idanio }};
            var ruta = "{{ url('/admin/consolidador/requerimientos/pendientes/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }


        // MUESTRA EL DETALLE DE REQUERIMIENTO

        function detalleRequisicion(id){
            // id requisicion

            var ruta = "{{ URL::to('/admin/consolidador/info/requisicion/detalle') }}/" + id;
            $('#tablaDetalle').load(ruta);
            $('#modalDetalle').modal('show');
        }


        function modalAgrupar(){

            var idanio = {{ $idanio }};
            document.getElementById("mySideToSideSelect").options.length = 0;
            document.getElementById("mySideToSideSelect_to").options.length = 0;
            document.getElementById("formulario-nuevo").reset();

            openLoading();

            axios.post(url+'/consolidatos/listado/ordenado/paraselect', {
                'anio': idanio
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        console.log(response)

                        $.each(response.data.detalle, function( key, val ){
                            $('#mySideToSideSelect').append('<option value='+val.id+'>'+val.texto+'</option>');
                        });

                        $('#modalAgregar').modal('show');
                    }else{
                        toastr.error('mal');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }


        function removeOptionsFromSelect(selectElement) {
            var i, L = selectElement.options.length - 1;
            for(i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }

        function verificar(){
            Swal.fire({
                title: 'Guardar Agrupación',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarRegistro();
                }
            })
        }


        function guardarRegistro(){

            var fecha = document.getElementById('fecha-agrupados').value;
            var nombreodestino = document.getElementById('nombreodestino-agrupados').value; // NULL
            var justificacion = document.getElementById('justificacion-agrupados').value; // NULL
            var entrega = document.getElementById('entrega-agrupados').value; // NULL
            var plazo = document.getElementById('plazo-agrupados').value; // NULL
            var lugar = document.getElementById('lugar-agrupados').value; // NULL
            var forma = document.getElementById('forma-agrupados').value; // NULL
            var otros = document.getElementById('otros-agrupados').value; // NULL

            var administrador = document.getElementById('select-administrador').value;
            var evaluador = document.getElementById('select-evaluador').value;
            var evaluador2 = document.getElementById('select-evaluador2').value;

            if(fecha === ''){
                toastr.error('Fecha es requerida');
                return;
            }

            if(administrador === ''){
                toastr.error('Administrador es requerido');
                return;
            }

            if(evaluador === ''){
                toastr.error('Evaluador es requerida');
                return;
            }

            var idanio = {{ $idanio }};

            var formData = new FormData();
            formData.append('anio', idanio)
            formData.append('fecha', fecha);
            formData.append('nombreodestino', nombreodestino);
            formData.append('justificacion', justificacion);
            formData.append('entrega', entrega);
            formData.append('plazo', plazo);
            formData.append('lugar', lugar);
            formData.append('forma', forma);
            formData.append('otros', otros);
            formData.append('administrador', administrador);
            formData.append('evaluador', evaluador);
            formData.append('evaluador2', evaluador2);

            var noHayElemento = true;

            // AQUI VAN ID REQUISICION UNIDAD DETALLE
            $("#mySideToSideSelect_to option").each(function(){
                noHayElemento = false;
                formData.append('lista[]', $(this).val());
            });

            if(noHayElemento){

                Swal.fire({
                    title: 'Error',
                    text: "Se deben agregar al Contenedor de la Derecha para ser Agrupados los Materiales",
                    icon: 'info',
                    showCancelButton: false,
                    allowOutsideClick: false,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Aceptar',
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                })

                return;
            }

            openLoading();

            axios.post(url+'/consolidador/registar/agrupados', formData,{

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // UN MATERIAL YA ESTA AGRUPADO O CANCELADO

                        Swal.fire({
                            title: 'Error',
                            text: "Se detecto un Material que ya fue Agrupado o Cancelado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }

                    else if(response.data.success === 2){

                        // AGRUPADOS CORRECTAMENTE

                        $('#modalAgregar').modal('hide');
                        toastr.success('Agrupados correctamente');
                        recargar();

                    }
                    else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });

        }



    </script>


@endsection
