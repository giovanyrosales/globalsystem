@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

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

                <div class="col-md-8" style="margin-top: 8px;">
                    <a class="btn btn-info mt-3 float-left" href= "javascript:history.back()" target="frameprincipal">
                        <i title="Atrás"></i> Atrás </a>
                </div>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Cotizaciones</li>
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
                                <label>Nombre o Destino</label>
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
                                <th style="width: 14%">Descripción Material</th>
                                <th style="width: 7%">Unidad Medida</th>
                                <th style="width: 5%">Precio Unitario
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
                                    <td><input disabled class="form-control" type="text" value="{{$dd->unidadmedida}}"></td>
                                    <td><input disabled class="form-control" value="${{$dd->precio_u }}"></td>
                                    <td><input disabled class="form-control" value="${{$dd->total }}"></td>
                                    <td><input disabled class="form-control" value="{{$dd->objeto }}"></td>
                                </tr>
                            @endforeach

                            <tr>
                                <td><p style="font-size: 15px" class='form-control'>Total</p></td>
                                <td><input disabled class="form-control" type="text" value="{{$totalCantidad}}"></td>
                                <td><input disabled class="form-control" type="text"></td>
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

@endsection
