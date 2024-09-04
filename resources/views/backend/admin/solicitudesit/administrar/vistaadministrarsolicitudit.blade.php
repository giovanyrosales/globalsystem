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
                                    <button type="button" onclick="cargarUnidades()" style="font-weight: bold; background-color: #28a745; color: white !important;"
                                            class="button button-rounded button-pill button-small">Cargar Unidades</button>
                                </div>

                                <hr> <br>

                                <div class="form-group row">
                                    <label style="margin: 8px">Unidades</label>
                                    <div style="margin-left: 6px" class="col-sm-5">
                                        <select class="form-control" id="select-unidades">

                                        </select>
                                    </div>
                                    <button type="button" onclick="verListadoEquipos()" style="font-weight: bold; background-color: #28a745; color: white !important;"
                                            class="button button-rounded button-pill button-small">Ver Lista</button>
                                </div>
                            </div>


                            <hr>
                            <br>


                            <div class="col-md-6" style="float: right">

                                <div class="card card-gray">
                                    <div class="card-header">
                                        <h3 class="card-title">FECHA LÍMITE</h3>
                                    </div>
                                    <div class="card-body">
                                        <input class="form-control" type="date" id="fechalimite" value="{{ $fechaLimite }}">
                                        <br>

                                        <button type="button" onclick="actualizarFecha()" style="font-weight: bold; background-color: #2c96d5; color: white !important;"
                                                class="button button-rounded button-pill button-small">Actualizar</button>
                                    </div>

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

        function cargarUnidades(){

            var anio = document.getElementById('select-anio').value;

            if(anio === ''){
                toastr.error('Año es requerido');
                return;
            }

            // BUSCAR UNIDADES
            openLoading()


            axios.post(url+'/solicitudesit/listadounidades',{
                'idanio': anio
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("select-unidades").options.length = 0;

                        $.each(response.data.listado, function( key, val ){
                            $('#select-unidades').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                        });

                    }else{
                        toastr.error('información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('información no encontrada');
                });
        }


        function verListadoEquipos(){

            var idFila = document.getElementById('select-unidades').value;

            window.location.href="{{ url('/admin/solicitudit/administracion/tablafinal') }}/" + idFila;
        }


        function actualizarFecha(){


            var fecha = document.getElementById('fechalimite').value;

            if(fecha === ''){
                toastr.error('Fecha es requerida');
                return;
            }

            openLoading();
            var formData = new FormData();

            formData.append('fecha', fecha);

            axios.post(url+'/solicitudesit/fechalimite', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                    }
                    else {
                        toastr.error('error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al registrar');
                    closeLoading();
                });
        }

    </script>


@endsection
