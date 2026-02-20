@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Configurar Reporte</h1>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid" style="margin-left: 15px">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-gray-dark">
                    <div class="card-header">
                        <h3 class="card-title">Formulario</h3>
                    </div>
                    <form>
                        <div class="card-body">


                            <div class="form-group">
                                <label>Nombre Gerente</label>
                                <input type="text" id="nombre-gerente" maxlength="100" class="form-control" value="{{ $info->nombre_gerente }}">
                            </div>

                            <div class="form-group">
                                <label>Cargo de Gerente</label>
                                <input type="text" id="nombre-gerentecargo" maxlength="100" class="form-control" value="{{ $info->nombre_gerente_cargo }}">
                            </div>


                            <div class="form-group">
                                <label>Margen Superior para Firma</label>
                                <input type="number" id="margen" class="form-control" value="{{ $info->margen }}">
                            </div>


                        </div>

                        <div class="card-footer" style="float: right;">
                            <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-3d button-rounded button-pill button-small" onclick="actualizar()">Actualizar</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</section>

@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>

        function abrirModalAgregar(){
            $('#modalAgregar').modal('show');
        }

        function actualizar(){
            var nombreGerente = document.getElementById('nombre-gerente').value;
            var nombreGerenteCargo = document.getElementById('nombre-gerentecargo').value;
            var margen = document.getElementById('margen').value;

            if(margen === ''){
                toastr.error('Margen es requerido');
                return
            }

            openLoading()
            var formData = new FormData();
            formData.append('nombreGerente', nombreGerente);
            formData.append('nombreGerenteCargo', nombreGerenteCargo);
            formData.append('margen', margen);

            axios.post(url+'/bodega/extras/actualizarDatos', formData, {
            })
                .then((response) => {
                    closeLoading()

                    if (response.data.success === 1) {
                        toastr.success('Actualizado');
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al actualizar');
                });
        }


    </script>



@stop
