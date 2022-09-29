@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" type="text/css" rel="stylesheet" />
@stop


<div class="content-wrapper">
    <!-- ESTA VISTA APARECE CUANDO NO TENEMOS UN NUEVO AÑO PARA CREAR UN PRESUPUESTO, ES DECIR QUE YA SE CREO
        Y FALTA ESPERAR QUE COLOQUEN NUEVO AÑO PARA PODER CREAR UN NUEVO PRESUPUESTO-->
    <section class="content-header">
        <div class="container-fluid">
            <div>
                <br>
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Nota:</h5>
                    <label>El presupuesto ya fue creado. Visite la sección editar.</label>
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

    <script type="text/javascript">
        $(document).ready(function(){
        });
    </script>

@endsection
