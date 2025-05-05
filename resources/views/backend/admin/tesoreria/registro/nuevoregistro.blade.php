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
                    <li class="breadcrumb-item">Registro</li>
                    <li class="breadcrumb-item active">Formulario</li>
                </ol>
            </div>
        </div>
    </section>



    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <form id="formulario-1">
                        <div class="card card-blue">
                        <div class="card-header">
                            <h3 class="card-title"></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>N° DE CONTROL INTERNO</label>
                                <input type="text" maxlength="50" class="form-control" id="numcontrol-nuevo" autocomplete="off">
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
                                <select class="form-control" id="select-proveedor">
                                    @foreach($arrayProveedor as $fila)
                                        <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>GARANTIA</label>
                                <select class="form-control" id="select-garantia">
                                    @foreach($arrayGarantia as $fila)
                                        <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
                                    @endforeach
                                </select>
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

                            <div class="form-group">
                                <label>ASEGURADORA</label>
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
                                <input type="date" class="form-control col-md-4" id="fechaucp-nuevo" autocomplete="off">
                            </div>

                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="col-sm-6 d-flex justify-content-end">
        <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" onclick="guardarRegistro()"
                class="button button-3d button-rounded button-pill button-small">
            <i class="fas fa-pencil-alt"></i>
            GUARDAR REGISTRO
        </button>
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

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>
        function guardarRegistro(){

            var numControl = document.getElementById('numcontrol-nuevo').value; //50
            var referencia = document.getElementById('referencia-nuevo').value; //100
            var descripcion = document.getElementById('descripcion-nuevo').value; //300
            var proveedor = document.getElementById('select-proveedor').value;
            var garantia = document.getElementById('select-garantia').value;
            var tipogarantia = document.getElementById('select-tipogarantia').value;
            var monto = document.getElementById('monto-nuevo').value;
            var aseguradora = document.getElementById('aseguradora-nuevo').value;

            var fechaDesde = document.getElementById('fechadesde-nuevo').value;
            var fechaHasta = document.getElementById('fechahasta-nuevo').value;
            var fechaRecibida = document.getElementById('fecharecibida-nuevo').value;
            var fechaEntrega = document.getElementById('fechaentrega-nuevo').value;
            var fechaUcp = document.getElementById('fechaucp-nuevo').value;


            openLoading();
            var formData = new FormData();
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

            axios.post(url+'/tesoreria/registro', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        document.getElementById("formulario-1").reset();
                        document.getElementById("formulario-2").reset();
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
