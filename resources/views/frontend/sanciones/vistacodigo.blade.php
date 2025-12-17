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
    .card-custom {
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        max-width: 650px;   /* 👈 tamaño del card */
        margin: 0 auto;     /* 👈 centrar card */
    }

    .card-body {
        padding: 20px;      /* 👈 menos espacio interno */
    }

    .btn-buscar {
        height: 32px;
        padding: 0 18px;
        margin-top: 28px;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content mt-3">
        <div class="container-fluid">

            <!-- CARD PRINCIPAL -->
            <div class="card card-gray-dark card-custom">
                <div class="card-header text-center">
                    <h3 class="card-title mb-0">Generar Carta</h3>
                </div>

                <div class="card-body">

                    <div class="row justify-content-center align-items-end">

                        <div class="form-group col-8">
                            <label class="text-muted text-center d-block">
                                ID del empleado
                            </label>
                            <input
                                type="text"
                                id="idEmpleado"
                                class="form-control text-center"
                                placeholder="Ej: 01382"
                                onkeydown="if (event.key === 'Enter') buscarEmpleado();">
                        </div>

                        <div class="form-group col-auto text-center">
                            <button
                                type="button"
                                class="btn btn-success btn-sm btn-buscar"
                                onclick="buscarEmpleado()">
                                Buscar
                            </button>
                        </div>
                    </div>
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
        $(document).ready(function(){
            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>


        function buscarEmpleado() {

            const id = document.getElementById('idEmpleado').value.trim();

            if (!id) {
                toastr.info('Ingrese el ID del empleado');
                return;
            }

            window.open("{{ URL::to('sanciones/reporte') }}/" + id);
        }
    </script>

@endsection
