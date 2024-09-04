@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<style>

    .modal-xl {
        max-width: 90% !important;
    }

</style>

<div class="content-wrapper" style="display: none" id="divcontenedor">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Solicitud IT Año: {{ $anioActual }}</h1> <br>

                    <p style="font-weight: bold">Departamento: {{ $departamento }}</p>
                </div>
            </div>

        </div>
    </section>

    <section class="content" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">

                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">

                                        <table class="table" id="matriz-solicitudes"  data-toggle="table">
                                            <thead>
                                            <tr>
                                                <th style="width: 3%">#</th>
                                                <th style="width: 35%">DESCRIPCIÓN EQUIPO INFORMÁTICO</th>
                                                <th style="width: 12%">CANTIDAD</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @if($arrayDatos != null)
                                                @foreach($arrayDatos as $item)

                                                    <tr>

                                                        <td>
                                                            <p disabled class='form-control'>{{ $item->conteo }}</p>
                                                        </td>

                                                        <td>
                                                            <input class="form-control" disabled value="{{ $item->nombre }}">
                                                        </td>

                                                        <td>
                                                            <input class="form-control" disabled value="{{ $item->cantidad }}">
                                                        </td>

                                                    </tr>

                                                    @if($loop->last)
                                                        <script>
                                                            setTimeout(function () {
                                                                closeLoading();
                                                            }, 1000);
                                                        </script>
                                                    @endif

                                                @endforeach
                                            @endif

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </section>


                        </form>
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            let lista = {{ $haydatos }};
            if(lista === 1){
                openLoading();
            }

           document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>


    </script>


@endsection
