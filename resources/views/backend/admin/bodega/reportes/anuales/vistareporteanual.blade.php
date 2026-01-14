@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
@stop

<div id="divcontenedor" style="display:none">

    <section class="content" style="margin-top:35px;margin-bottom:60px;">
        <div class="container-fluid">

            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTES ANUALES</h3>
                </div>

                <div class="card-body row">

                    <!-- Unidad -->
                    <div class="col-md-6">
                        <label>Unidad</label>
                        <select class="form-control" id="select-unidad">
                            @foreach($arrayUnidades as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Desde -->
                    <div class="col-md-2">
                        <label>Desde</label>
                        <input type="date" class="form-control" id="fecha-desde">
                    </div>

                    <!-- Hasta -->
                    <div class="col-md-2">
                        <label>Hasta</label>
                        <input type="date" class="form-control" id="fecha-hasta">
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-6 mt-3">
                        <label>Descripción</label>
                        <textarea id="descripcion" class="form-control"
                                  rows="3" maxlength="500"></textarea>
                    </div>

                    <!-- Botón -->
                    <div class="col-md-2 d-flex align-items-end mt-3">
                        <button onclick="pdfReporte()" class="btn btn-outline-dark w-100">
                            <img src="{{ asset('images/logopdf.png') }}" width="28"> PDF
                        </button>
                    </div>


                    <!-- Botón Guardar -->
                    <div class="col-md-2 d-flex align-items-end mt-3">
                        <button type="button" style="font-weight: bold; background-color: #28a745;
                        color: white !important;"
                                class="button button-3d button-rounded button-pill button-small"
                                onclick="guardarReporte()">GUARDAR</button>
                    </div>

                </div>
            </div>

            <p style="color: red; font-weight: bold">Nota: El año del Título se  toma de Fecha Hasta</p>

        </div>
    </section>
</div>

<!-- FORMULARIO OCULTO -->
<form id="formPdfReporte" method="POST" target="_blank"
      action="{{ route('bodega.reporte.entrega.anual') }}">
    @csrf
    <input type="hidden" name="unidad" id="pdf_unidad">
    <input type="hidden" name="desde" id="pdf_desde">
    <input type="hidden" name="hasta" id="pdf_hasta">
    <input type="hidden" name="descripcion" id="pdf_descripcion">
</form>

@extends('backend.menus.footerjs')

@section('archivos-js')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#select-unidad').select2({
                theme: "bootstrap-5",
                language: {
                    noResults: () => "Búsqueda no encontrada"
                }
            });

            $('#divcontenedor').show();
        });

        function pdfReporte(){
            let unidad = $('#select-unidad').val();
            let desde = $('#fecha-desde').val();
            let hasta = $('#fecha-hasta').val();
            let descripcion = $('#descripcion').val();

            if(!desde){
                toastr.error('Fecha desde es requerida'); return;
            }
            if(!hasta){
                toastr.error('Fecha hasta es requerida'); return;
            }

            if(new Date(hasta) < new Date(desde)){
                toastr.error('Fecha Hasta no puede ser menor'); return;
            }

            $('#pdf_unidad').val(unidad);
            $('#pdf_desde').val(desde);
            $('#pdf_hasta').val(hasta);
            $('#pdf_descripcion').val(descripcion);

            $('#formPdfReporte').submit();
        }


        function guardarReporte(){
            let unidad = $('#select-unidad').val();
            let desde = $('#fecha-desde').val();
            let hasta = $('#fecha-hasta').val();
            let descripcion = $('#descripcion').val();

            if(!desde){
                toastr.error('Fecha desde es requerida'); return;
            }
            if(!hasta){
                toastr.error('Fecha hasta es requerida'); return;
            }

            if(new Date(hasta) < new Date(desde)){
                toastr.error('Fecha Hasta no puede ser menor'); return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('unidad', unidad);
            formData.append('desde', desde);
            formData.append('hasta', hasta);
            formData.append('descripcion', descripcion);

            axios.post(url+'/bodega/guardarpdf/entregaanual', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.error('No hay datos para guardar');
                    }
                    else if(response.data.success === 2){
                        toastr.success('Datos Guardados');
                    }
                    else {
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al guardar');
                    closeLoading();
                });

        }



    </script>
@endsection
