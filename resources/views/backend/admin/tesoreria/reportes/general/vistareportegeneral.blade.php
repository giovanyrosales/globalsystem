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

    <section class="content" style="margin-top: 35px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTE GENERAL</h3>
                </div>
                <div class="card-body">
                    <section class="content" style="margin-left: 30px">


                        <label>AÑOS</label>
                        <select class="form-control col-md-3" id="select-anios">
                            @foreach($arrayAnios as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                        </select>

                        <div class="form-group" style="margin-top: 15px">
                            <label>
                                <input type="checkbox" class="checkbox" id="checkbox-todos">
                                Todos los años
                            </label>
                        </div>

                        <label style="margin-top: 15px">Estado</label>
                        <select class="form-control col-md-3" id="select-estado">
                            <option value="1">VIGENTES</option>
                            <option value="2">VENCIDAS</option>
                            <option value="3">ENTRADAS A UCP</option>
                            <option value="4">ENTREGADAS A PROVEEDOR</option>
                        </select>


                        <button type="button" onclick="pdfEstados()" class="btn" style="margin-top: 25px; border-color: black; border-radius: 0.1px;">
                            <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                            Generar PDF
                        </button>

                        <hr>

                        <div class="form-group" id="grupo-checkbox" style="margin-top: 15px; display: none;">

                            <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" onclick="setearACompletados()"
                                    class="button button-3d button-rounded button-pill button-small">
                                <i class="fas fa-pencil-alt"></i>
                                Completar
                            </button>

                        </div>

                    </section>
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

        $('#select-estado').on('change', function () {
            const selected = $(this).val();
            if (selected === '3' || selected === '4') {
                $('#grupo-checkbox').show();
            } else {
                $('#grupo-checkbox').hide();
                $('#checkbox-todos').prop('checked', false); // opcional: desmarcar si se oculta
            }
        });

        document.getElementById("divcontenedor").style.display = "block";
    </script>


    <script>

        function pdfEstados(){

            var anios = document.getElementById('select-anios').value;
            var estado = document.getElementById('select-estado').value;
            var checkbox = document.getElementById('checkbox-todos');
            var valorCheckbox = checkbox.checked ? 1 : 0;

            if(anios === ''){
                toastr.error('Años es requerido');
                return;
            }

            // SE VA A SETEAR EL ESTADO
            window.open("{{ URL::to('admin/tesoreria/pdf/general') }}/" +
                anios + "/" + estado + "/" + valorCheckbox);
        }


        function setearACompletados(){

            var estado = document.getElementById('select-estado').value;

            if(estado == '3' || estado == '4'){
                openLoading();
                var formData = new FormData();
                formData.append('estado', estado); // SOLO PUEDE IR 3 O 4

                axios.post(url+'/tesoreria/setear/segun-checkbox/estado', formData, {
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
            }else{
                toastr.error('Error');
            }
        }


    </script>




@endsection
