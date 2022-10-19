@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
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
                <label style="font-size: 16px">Fecha: {{ $fecha }}</label> <br>

                @if($infoContenedor->estado == 0)
                    <label style="font-size: 16px">Estado: <strong>Partida en Desarrollo</strong></label>
                @elseif($infoContenedor->estado == 1)
                    <label style="font-size: 16px">Estado: <strong>Partida en Revisión</strong></label>
                @else
                    <label style="font-size: 16px">Estado: <strong>Partidas Aprobadas</strong></label>
                @endif

                <br>
                <label style="font-size: 16px">Proyecto: <strong>{{ $nombreProyecto }}</strong></label>

                <br>
                <div class="col-md-8">
                    <a class="btn btn-info mt-3 float-left" href= "javascript:history.back()" target="frameprincipal">
                        <i title="Cancelar"></i> Atras </a>
                </div>

                @can('boton.crear.partida.adicional')
                    <button type="button" style="margin-left: 25px; margin-top: 15px" onclick="modalCrearPartida()" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i>
                        Nueva Partida Adicional
                    </button>
                @endcan

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Partida Adicional</li>
                    <li class="breadcrumb-item active">Listado</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Partidas Adicionales</h3>
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

            // id contenedor partida adicional
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/partida/adicional/creacion/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>


    <script>

        function modalCrearPartida(){



        }


    </script>



@endsection
