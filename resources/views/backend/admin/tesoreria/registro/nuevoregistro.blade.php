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

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <form id="formulario-1">
                        <div class="card card-blue">
                        <div class="card-header">
                            <h3 class="card-title"></h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group col-md-4">
                                <label>FECHA REGISTRO</label>
                                <input type="date" class="form-control" id="fecharegistro-nuevo" autocomplete="off">
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>N° DE CONTROL INTERNO</label>
                                <input type="text" maxlength="50" value="{{ $correlativo }}" class="form-control" id="numcontrol-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>REFERENCIA</label>
                                <input type="text" maxlength="100" class="form-control" id="referencia-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>DESCRIPCION</label>
                                <input type="text" maxlength="300" class="form-control" id="descripcion-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>PROVEEDOR</label>
                                <div class="d-flex align-items-center">
                                    <select class="form-control me-2" id="select-proveedor" style="flex: 1;">
                                        @foreach($arrayProveedor as $fila)
                                            <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" style="margin-left: 8px" class="btn btn-success" onclick="modalNuevoProveedor()" title="Agregar nuevo proveedor">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>


                            <div class="form-group">
                                <label>GARANTIA</label>
                                <div class="d-flex align-items-center">
                                    <select class="form-control" id="select-garantia">
                                        @foreach($arrayGarantia as $fila)
                                            <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" style="margin-left: 8px" class="btn btn-success" onclick="modalNuevoGarantia()" title="Agregar nueva garantía">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>


                            <div class="form-group">
                                <label>TIPO DE GARANTIA</label>
                                <select class="form-control" id="select-tipogarantia">
                                    @foreach($arrayTipoGarantia as $fila)
                                        <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>MONTO GARANTIA</label>
                                <input type="number" class="form-control" id="monto-nuevo" autocomplete="off">
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>ASEGURADORA</label>
                                <select class="form-control" id="select-aseguradoras">
                                    <option value="">Seleccionar Opción</option>
                                    @foreach($arrayAseguradora as $fila)
                                        <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" id="input-aseguradora-container" style="display: none;">
                                <label id="label-aseguradora"></label>
                                <input type="text" maxlength="300" class="form-control" id="aseguradora-nuevo" autocomplete="off">
                            </div>

                        </div>
                    </div>
                    </form>

                </div>
                <div class="col-md-6">
                    <div class="card card-blue">
                        <div class="card-header">
                            <h3 class="card-title"></h3>
                        </div>
                        <form id="formulario-2">
                            <div class="card-body">

                            <label>VIGENCIA</label>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>A PARTIR DEL</label>
                                    <input type="date" class="form-control" id="fechadesde-nuevo" autocomplete="off">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>HASTA EL</label>
                                    <input type="date" class="form-control" id="fechahasta-nuevo" autocomplete="off">
                                </div>
                            </div>

                            <hr>


                            <div class="form-group">
                                <label>FECHA DE RECIBIDA</label>
                                <input type="date" class="form-control col-md-4" id="fecharecibida-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>FECHA DE ENTREGA</label>
                                <input type="date" class="form-control col-md-4" id="fechaentrega-nuevo" autocomplete="off">
                            </div>


                            <div class="form-group">
                                <label>ENTREGADA A UCP</label>
                                <div class="d-flex align-items-center">
                                    <input type="date" class="form-control col-md-4" id="fechaucp-nuevo" autocomplete="off">
                                    <div class="form-check ml-3">
                                        <input class="form-check-input" type="checkbox" id="check-ucp">
                                        <label class="form-check-label" for="check-ucp">Entregada a UCP</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 d-flex justify-content-end">
                                <button type="button"
                                        style="font-weight: bold; background-color: #2156af; color: white !important;"
                                        onclick="guardarRegistro()"
                                        class="button button-3d button-rounded button-pill button-small">
                                    <i class="fas fa-pencil-alt"></i>
                                    GUARDAR REGISTRO
                                </button>
                            </div>

                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalProveedor">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Proveedor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-proveedor">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Proveedor</label>
                                        <input type="text" maxlength="300" class="form-control" id="modal-proveedor-nuevo" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #2156af; color: white
                    !important;" class="button button-rounded button-pill button-small" onclick="nuevoProveedor()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalGarantia">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Garatía</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-garantia">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Garantía</label>
                                        <input type="text" maxlength="300" class="form-control" id="modal-garantia-nuevo" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #2156af; color: white
                    !important;" class="button button-rounded button-pill button-small" onclick="nuevoGarantia()">Guardar</button>
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

            document.getElementById("divcontenedor").style.display = "block";


            $('#select-proveedor').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            $('#select-garantia').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            $('#select-tipogarantia').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });



            $('#select-aseguradoras').on('change', function () {
                const valor = $(this).val();
                if (valor) {
                    $('#input-aseguradora-container').show();
                } else {
                    $('#input-aseguradora-container').hide();
                }

                const nombreSeleccionado = $(this).find("option:selected").text();

                if ($(this).val()) {
                    $('#label-aseguradora').text('NOTA DE ' + nombreSeleccionado);
                    $('#input-aseguradora-container').show();
                } else {
                    $('#label-aseguradora').text('ASEGURADORA');
                    $('#input-aseguradora-container').hide();
                }
            });


            $('#fechadesde-nuevo, #fechahasta-nuevo').on('change', function () {
                const desde = document.getElementById('fechadesde-nuevo').value;
                const hasta = document.getElementById('fechahasta-nuevo').value;

                if (desde && hasta && hasta < desde) {
                    alert("La fecha 'hasta' no puede ser menor que la fecha 'desde'.");
                    document.getElementById('fechahasta-nuevo').value = desde; // autoajustar
                }
            });

            // Obtener la fecha actual en formato YYYY-MM-DD
            const hoy = new Date();
            const fechaFormateada = hoy.toISOString().split('T')[0];

            // Asignarla al input date
            document.getElementById("fecharegistro-nuevo").value = fechaFormateada;
        });
    </script>

    <script>
        function guardarRegistro(){

            var fecharegistro = document.getElementById('fecharegistro-nuevo').value; //50
            var numControl = document.getElementById('numcontrol-nuevo').value; //50
            var referencia = document.getElementById('referencia-nuevo').value; //100
            var descripcion = document.getElementById('descripcion-nuevo').value; //300
            var proveedor = document.getElementById('select-proveedor').value;
            var garantia = document.getElementById('select-garantia').value;
            var tipogarantia = document.getElementById('select-tipogarantia').value;
            var monto = document.getElementById('monto-nuevo').value;

            var idAseguradora = document.getElementById('select-aseguradoras').value;
            var aseguradora = document.getElementById('aseguradora-nuevo').value;

            var fechaDesde = document.getElementById('fechadesde-nuevo').value;
            var fechaHasta = document.getElementById('fechahasta-nuevo').value;
            var fechaRecibida = document.getElementById('fecharecibida-nuevo').value;
            var fechaEntrega = document.getElementById('fechaentrega-nuevo').value;
            var fechaUcp = document.getElementById('fechaucp-nuevo').value;


            var checkbox = document.getElementById('check-ucp');
            var valorCheckboxUCP = checkbox.checked ? 1 : 0;


            if(fecharegistro === ''){
                toastr.error('Fecha Registro es requerido');
                return
            }

            openLoading();
            var formData = new FormData();
            formData.append('fechaRegistro', fecharegistro);
            formData.append('numcontrol', numControl);
            formData.append('referencia', referencia);
            formData.append('descripcion', descripcion);
            formData.append('proveedor', proveedor);
            formData.append('garantia', garantia);
            formData.append('tipogarantia', tipogarantia);
            formData.append('monto', monto);
            formData.append('aseguradora', aseguradora);
            formData.append('fechadesde', fechaDesde);
            formData.append('fechahasta', fechaHasta);
            formData.append('fecharecibida', fechaRecibida);
            formData.append('fechaentrega', fechaEntrega);
            formData.append('fechaucp', fechaUcp);
            formData.append('idaseguradora', idAseguradora);
            formData.append('checkucp', valorCheckboxUCP);

            axios.post(url+'/tesoreria/registro', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        document.getElementById("formulario-1").reset();
                        document.getElementById("formulario-2").reset();

                        // SETEAR CORRELATIVO
                        var correlativo = response.data.correlativo
                        document.getElementById("numcontrol-nuevo").value = correlativo;

                        setearFecha()
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


        function setearFecha(){
            // Obtener la fecha actual en formato YYYY-MM-DD
            const hoy = new Date();
            const fechaFormateada = hoy.toISOString().split('T')[0];

            // Asignarla al input date
            document.getElementById("fecharegistro-nuevo").value = fechaFormateada;
        }


        function modalNuevoProveedor(){
            document.getElementById("formulario-proveedor").reset();
            $('#modalProveedor').modal('show');
        }


        function nuevoProveedor(){
            var nombre = document.getElementById('modal-proveedor-nuevo').value;

            if(nombre === ''){
                toastr.error('Proveedor es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);

            axios.post(url+'/tesoreria/proveedores/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalProveedor').modal('hide');

                        // RECARGAR LISTA PROVEEDORES

                        document.getElementById("select-proveedor").options.length = 0;

                        $.each(response.data.arrayProveedores, function( key, val ){
                            $('#select-proveedor').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                        });

                        $('#select-proveedor').prop('selectedIndex', 0).change();
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


        //**************************


        function modalNuevoGarantia(){
            document.getElementById("formulario-garantia").reset();
            $('#modalGarantia').modal('show');
        }


        function nuevoGarantia(){
            var nombre = document.getElementById('modal-garantia-nuevo').value;

            if(nombre === ''){
                toastr.error('Garantia es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);

            axios.post(url+'/tesoreria/garantia/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalGarantia').modal('hide');

                        // RECARGAR LISTA GARANTIA

                        document.getElementById("select-garantia").options.length = 0;

                        $.each(response.data.arrayGarantia, function( key, val ){
                            $('#select-garantia').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                        });

                        $('#select-garantia').prop('selectedIndex', 0).change();
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
