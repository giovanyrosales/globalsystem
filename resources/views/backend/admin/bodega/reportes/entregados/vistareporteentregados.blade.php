@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table {
        /*Ajustar tablas*/
        table-layout: fixed;
    }

</style>

<div id="divcontenedor" style="display: none">

    <section class="content" style="margin-top: 20px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTE DE ENTREGAS POR UNIDAD</h3>
                </div>
                <div class="card-body">

                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <div class="row">

                                <div class="form-group col-md-6" >
                                    <label style="color: #686868">Unidad</label>
                                    <select class="form-control" id="select-unidad">
                                        @foreach($arrayUnidad as $item)
                                            <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-2" >
                                    <label style="color: #686868">Desde: </label>
                                    <input type="date" autocomplete="off" class="form-control" id="fecha-desde">
                                </div>

                                <div class="form-group col-md-2" >
                                    <label style="color: #686868">Hasta: </label>
                                    <input type="date" autocomplete="off" class="form-control" id="fecha-hasta">
                                </div>

                                <div class="form-group" style="margin-top: 30px">
                                    <button type="button" class="btn btn-success form-control" onclick="verificarUnidad()">Generar</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>


    <section class="content" style="margin-top: 20px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTE DE ENTREGAS TOTAL</h3>
                </div>
                <div class="card-body">

                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <div class="row">
                                <div class="form-group col-md-2" >
                                    <label style="color: #686868">Desde: </label>
                                    <input type="date" autocomplete="off" class="form-control" id="fecha-desde2">
                                </div>

                                <div class="form-group col-md-2" >
                                    <label style="color: #686868">Hasta: </label>
                                    <input type="date" autocomplete="off" class="form-control" id="fecha-hasta2">
                                </div>

                                <div class="form-group" style="margin-top: 30px">
                                    <button type="button" class="btn btn-success form-control" onclick="verificarTotal()">Generar</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>



</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>


        function verificarUnidad(){

            // USUARIO ASIGNADO A ESA UNIDAD
            let idusuario = document.getElementById("select-unidad").value;
            let fechaDesde = document.getElementById("fecha-desde").value;
            let fechaHasta = document.getElementById("fecha-hasta").value;

            if(fechaDesde === ''){
                toastr.error('Fecha Desde es requerido');
                return;
            }

            if(fechaHasta === ''){
                toastr.error('Fecha Hasta es requerido');
                return;
            }

            // Convertir a objetos Date para comparar
            let dateDesde = new Date(fechaDesde);
            let dateHasta = new Date(fechaHasta);

            if (dateHasta < dateDesde) {
                toastr.error('La Fecha Hasta no puede ser menor que la Fecha Desde');
                return;
            }

            window.open("{{ URL::to('admin/bodega/reportes/unidadentrega/pdf') }}/" + idusuario+ "/" + fechaDesde + "/" + fechaHasta);
        }


        function verificarTotal(){


            let fechaDesde = document.getElementById("fecha-desde2").value;
            let fechaHasta = document.getElementById("fecha-hasta2").value;

            if(fechaDesde === ''){
                toastr.error('Fecha Desde es requerido');
                return;
            }

            if(fechaHasta === ''){
                toastr.error('Fecha Hasta es requerido');
                return;
            }

            // Convertir a objetos Date para comparar
            let dateDesde = new Date(fechaDesde);
            let dateHasta = new Date(fechaHasta);

            if (dateHasta < dateDesde) {
                toastr.error('La Fecha Hasta no puede ser menor que la Fecha Desde');
                return;
            }

            window.open("{{ URL::to('admin/bodega/reportes/totalentrega/pdf') }}/" + fechaDesde + "/" + fechaHasta);
        }


    </script>

@endsection
