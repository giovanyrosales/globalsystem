@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

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
                    <div class="form-group">
                        <label>Presupuesto Año: {{ $infoAnio->nombre }}</label>
                    </div>
                    <div class="form-group">
                        @if($estado == 1)
                            <label style="margin-left: 15px;">Estado: Pendiente de Aprobación</label>
                        @else
                            <label style="margin-left: 15px;">Estado: <span class="badge bg-success">Presupuesto Aprobado</span> </label>
                        @endif
                    </div>
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
                                        <div id="tablaDatatable">

                                        </div>
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
            openLoading();

            openLoading();

            let iddepa = {{ $iddepa }};
            let idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/departamento/presupuesto/contenedor/') }}/" + iddepa + "/" + idanio;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>


@endsection
