@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
@stop

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div>
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Nota:</h5>
                    <label>PRESUPUESTO NO CREADO AUN.</label>
                </div>
                <div class="col-md-8">
                    <a class="btn btn-info mt-3 float-left" href= "javascript:history.back()" target="frameprincipal">
                        <i title="Atrás"></i> Atrás </a>
                </div>
            </div>
        </div>
    </section>


</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

@endsection
