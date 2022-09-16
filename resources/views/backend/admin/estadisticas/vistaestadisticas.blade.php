@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="container-fluid">
            <div class="col-sm-12">
                <h1>Estadísticas</h1>
            </div>
        </div>
    </section>

    <section class="content">

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header" id="card-header-color">
                        <h3 class="card-title"></h3>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-shopping-cart" style="color: white"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Proyectos a la Fecha</span>
                                        <span class="info-box-number">x</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-gray-dark">
                                    <span class="info-box-icon"><i class="fas fa-shopping-cart" style="color: white"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Ejecutados en el Mes</span>
                                        <span class="info-box-number">x</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="far fa-user" style="color: white"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Ejecutados en el Mes</span>
                                        <span class="info-box-number">x</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

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
            document.getElementById("divcontenedor").style.display = "block";



        });
    </script>

    <script>



    </script>


@endsection
