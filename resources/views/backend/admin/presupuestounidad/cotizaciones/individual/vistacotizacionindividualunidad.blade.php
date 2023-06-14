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
                <h1>Cotización Unidad - Detalle</h1>

                <div class="col-md-8">
                    <a style="font-weight: bold; margin-top: 15px; color: white !important;" class="button button-primary button-rounded button-pill button-small" href= "javascript:history.back()" target="frameprincipal">
                        <i title="Atrás"></i> Atrás </a>
                </div>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Cotizaciones Pendientes</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">

                            <div class="form-group">
                                <label>Destino</label>
                                <input type="text" value="{{ $infoAgrupado->nombreodestino }}" class="form-control" disabled>
                            </div>
                            <div class="form-group">
                                <label>Justificación</label>
                                <textarea class="form-control" rows="3" disabled>{{ $infoAgrupado->justificacion }}</textarea>
                            </div>
                        </div>

                        <div class="col-sm-5" style="margin-left: 25px">

                            <div class="form-group">
                                <label>Proveedor</label>
                                <input type="text" value="{{ $proveedor }}" class="form-control" disabled>
                            </div>

                            <div class="form-group">
                                <label>Fecha de Cotización</label>
                                <input type="text" value="{{ $fecha }}" class="form-control" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <table class="table" id="matriz" data-toggle="table">
                            <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th style="width: 6%">Cantidad</th>
                                <th style="width: 15%">Descripción</th>
                                <th style="width: 5%">Precio Unitario</th>
                                <th style="width: 5%">Total</th>
                                <th style="width: 5%">Cod. Presup</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($infoCotiDetalle as $dd)
                                <tr>
                                    <td><p id="fila{{$dd->conteo}}" class='form-control'>{{$dd->conteo}}</p></td>
                                    <td><input disabled class="form-control" value="{{$dd->cantidad}}"></td>
                                    <td><input disabled class="form-control" type="text" value="{{$dd->descripcion}}"></td>
                                    <td><input disabled class="form-control" value="${{$dd->precio_u }}"></td>
                                    <td><input disabled class="form-control" value="${{$dd->total }}"></td>
                                    <td><input disabled class="form-control" value="{{$dd->objeto }}"></td>
                                </tr>
                            @endforeach

                            <tr>
                                <td><p class='form-control'>Total</p></td>
                                <td><input disabled class="form-control" type="text" value="{{$totalCantidad}}"></td>
                                <td><input disabled class="form-control" type="text"></td>
                                <td><input disabled class="form-control" value="${{$totalPrecio }}"></td>
                                <td><input disabled class="form-control" value="${{$totalTotal }}"></td>
                                <td><input disabled class="form-control"></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="card-body">
                    <div class="row">



                        <!-- JEFA DE UACI PUEDE DENEGAR O AUTORIZAR COTIZACIÓN -->

                        @can('boton.autorizar.denegar.cotizacion.unidad')

                            @if($cotizacion->estado == 0)
                                <div class="col-md-4" style="float: right">
                                    <button type="button" class="btn btn-success float-right mt-3" onclick="autorizarCotizacion()">Autorizar Cotización</button>
                                    <button type="button"  class="btn btn-danger float-right mt-3" style="margin-right: 20px" onclick="denegarCotizacion()">Denegar Cotización</button>
                                </div>
                            @endif

                        @endcan

                        <!-- EL TEXTO SOLO PUEDE VER LOS QUE TENGAN EL PERMISO -->

                        @can('texto.esperando.aprobacion.cotizacion.unidad')
                            @if($cotizacion->estado == 0)
                            <div class="col-md-4 float-right">
                                <span class="badge bg-secondary" style="font-size: 18px">Esperando Aprobación por Jefatura</span>
                            </div>
                            @endcan
                        @endcan

                    </div>
                </div>

            </div>
        </div>
    </section>

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

        function autorizarCotizacion(){
            Swal.fire({
                title: 'Autorizar Cotización',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarAutorizacion();
                }
            })
        }

        function denegarCotizacion(){
            Swal.fire({
                title: 'Denegar Cotización',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarDenegada();
                }
            })
        }

        function verificarAutorizacion(){
            // id de cotizacion unidad
            var id = {{ $id }};

            openLoading();

            axios.post(url+'/p/cotizacion/unidad/autorizar', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {

                        Swal.fire({
                            title: 'Cotización Autorizada',
                            text: "",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                                // REDIRECCIONAR A COTIZACIONES AUTORIZADAS
                                window.location.href="{{ url('/admin/p/cotizacion/unidad/autorizadas/anio') }}";
                            }
                        })
                    }
                    else{
                        toastr.error('Error al autorizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al autorizar');
                    closeLoading();
                });
        }

        function verificarDenegada(){
            // id de cotizacion unidad
            var id = {{ $id }};

            openLoading();

            axios.post(url+'/p/cotizacion/denegar', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){


                        Swal.fire({
                            title: 'Error',
                            text: "La Cotización ya esta Aprobada",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                                // REDIRECCIONAR A COTIZACIONES AUTORIZADAS
                                window.location.href="{{ url('/admin/p/cotizacion/unidad/autorizadas/anio') }}";
                            }
                        })

                    }
                    else if(response.data.success === 2){


                        Swal.fire({
                            title: 'Cotización Denegada',
                            text: "Se redireccionara a Cotizaciones Denegadas",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                                // REDIRECCIONAR A COTIZACIONES DENEGADAS

                                window.location.href="{{ url('/admin/p/cotizacion/unidad/denegadas/anio') }}";
                            }
                        })
                    }
                    else{
                        toastr.error('Error al denegar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al denegar');
                    closeLoading();
                });
        }


    </script>


@endsection
