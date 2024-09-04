@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Solicitud IT</h1>
                </div>

            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label style="margin: 8px">Año de Presupuesto</label>
                                    <div style="margin-left: 6px" class="col-sm-2">
                                        <select class="form-control" id="select-anio">
                                            @foreach($anios as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" onclick="verificar()" style="font-weight: bold; background-color: #28a745; color: white !important;"
                                            class="button button-rounded button-pill button-small">Buscar</button>
                                </div>
                            </div>

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


    <script>

        function verificar(){

            var anio = document.getElementById('select-anio').value;

            if(anio === ''){
                toastr.error('Año es requerido');
                return;
            }

            window.location.href="{{ url('/admin/p/solicitudesit/vistaanio') }}/" + anio;
        }

    </script>


@endsection
