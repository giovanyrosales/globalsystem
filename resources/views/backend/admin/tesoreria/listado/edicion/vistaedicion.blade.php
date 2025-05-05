@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">

@stop

<style>

</style>



<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Registros</li>
                    <li class="breadcrumb-item active">Listado</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Formulario</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">


                            <div class="form-group">
                                <label>N° DE CONTROL INTERNO</label>
                                <input type="text" maxlength="50" value="{{ $info->control_interno }}" class="form-control" id="numcontrol-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>REFERENCIA</label>
                                <input type="text" maxlength="100" value="{{ $info->referencia }}" class="form-control" id="referencia-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>DESCRIPCION</label>
                                <input type="text" maxlength="300" value="{{ $info->descripcion_licitacion }}" class="form-control" id="descripcion-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>PROVEEDOR</label>
                                <select class="form-control" id="select-proveedor">
                                    @foreach($arrayProveedor as $item)
                                        @if($item->id == $info->id_proveedor)
                                            <option value="{{$item->id}}" selected>{{ $item->nombre }}</option>
                                        @else
                                            <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>GARANTIA</label>
                                <select class="form-control" id="select-garantia">
                                    @foreach($arrayGarantia as $item)
                                        @if($item->id == $info->id_garantia)
                                            <option value="{{$item->id}}" selected>{{ $item->nombre }}</option>
                                        @else
                                            <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>TIPO DE GARANTIA</label>
                                <select class="form-control" id="select-tipogarantia">
                                    @foreach($arrayTipoGarantia as $item)
                                        @if($item->id == $info->id_tipo_garantia)
                                            <option value="{{$item->id}}" selected>{{ $item->nombre }}</option>
                                        @else
                                            <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>MONTO GARANTIA</label>
                                <input type="number" value="{{ $info->monto_garantia }}" class="form-control" id="monto-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>ASEGURADORA</label>
                                <input type="text" maxlength="300" value="{{ $info->aseguradora }}" class="form-control" id="aseguradora-nuevo" autocomplete="off">
                            </div>


                            <hr>


                            <label>VIGENCIA</label>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>A PARTIR DEL</label>
                                    <input type="date" class="form-control" value="{{ $info->vigencia_desde }}" id="fechadesde-nuevo" autocomplete="off">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>HASTA EL</label>
                                    <input type="date" class="form-control" value="{{ $info->vigencia_hasta }}" id="fechahasta-nuevo" autocomplete="off">
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>FECHA DE RECIBIDA</label>
                                <input type="date" class="form-control col-md-4" value="{{ $info->fecha_recibida }}" id="fecharecibida-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>FECHA DE ENTREGA</label>
                                <input type="date" class="form-control col-md-4" value="{{ $info->fecha_entrega }}" id="fechaentrega-nuevo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>ENTREGADA A UCP</label>
                                <input type="date" class="form-control col-md-4" id="fechaucp-nuevo" value="{{ $info->fecha_entrega_ucp }}" autocomplete="off">
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="editar()">Actualizar</button>
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
        <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function(){

                $('#select-proveedor').select2({
                    theme: "bootstrap-5",
                    "language": {
                        "noResults": function(){
                            return "Búsqueda no encontrada";
                        }
                    },
                });

                $('#select-garantia').select2({
                    theme: "bootstrap-5",
                    "language": {
                        "noResults": function(){
                            return "Búsqueda no encontrada";
                        }
                    },
                });

                $('#select-tipogarantia').select2({
                    theme: "bootstrap-5",
                    "language": {
                        "noResults": function(){
                            return "Búsqueda no encontrada";
                        }
                    },
                });

                document.getElementById("divcontenedor").style.display = "block";
            });
        </script>

        <script>



            function editar(){

                var id = {{ $info->id }};
                var numControl = document.getElementById('numcontrol-nuevo').value;
                var referencia = document.getElementById('referencia-nuevo').value;
                var descripcion = document.getElementById('descripcion-nuevo').value;
                var monto = document.getElementById('monto-nuevo').value;
                var aseguradora = document.getElementById('aseguradora-nuevo').value;

                var fechaDesde = document.getElementById('fechadesde-nuevo').value;
                var fechaHasta = document.getElementById('fechahasta-nuevo').value;
                var fechaRecibida = document.getElementById('fecharecibida-nuevo').value;
                var fechaEntrega = document.getElementById('fechaentrega-nuevo').value;
                var fechaEntregaUcp = document.getElementById('fechaucp-nuevo').value;

                var proveedor = document.getElementById('select-proveedor').value;
                var garantia = document.getElementById('select-garantia').value;
                var tipoGarantia = document.getElementById('select-tipogarantia').value;

                openLoading();
                var formData = new FormData();
                formData.append('id', id);
                formData.append('numcontrol', numControl);
                formData.append('referencia', referencia);
                formData.append('descripcion', descripcion);
                formData.append('monto', monto);
                formData.append('aseguradora', aseguradora);
                formData.append('fechadesde', fechaDesde);
                formData.append('fechahasta', fechaHasta);
                formData.append('fecharecibida', fechaRecibida);
                formData.append('fechaentrega', fechaEntrega);
                formData.append('fechaentregaucp', fechaEntregaUcp);
                formData.append('proveedor', proveedor);
                formData.append('garantia', garantia);
                formData.append('tipogarantia', tipoGarantia);

                axios.post(url+'/tesoreria/listado/actualizar', formData, {
                })
                    .then((response) => {
                        closeLoading();

                        if(response.data.success === 1){
                            toastr.success('Actualizado correctamente');
                        }
                        else {
                            toastr.error('Error al actualizar');
                        }

                    })
                    .catch((error) => {
                        toastr.error('Error al actualizar');
                        closeLoading();
                    });
            }







        </script>

@endsection
