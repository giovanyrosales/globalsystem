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
    table {
        /*Ajustar tablas*/
        table-layout: fixed;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        font-size: 16px; /* Tamaño de texto más pequeño */
        text-align: left; /* Alineación del texto a la izquierda */
    }

    .checkbox {
        margin: 3; /* Elimina el margen para pegar el checkbox al texto */
        width: 15px; /* Tamaño pequeño para el checkbox */
        height: 15px; /* Ajusta la altura del checkbox */
        margin-right: 3px; /* Pega el checkbox al texto */
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content" style="margin-top: 20px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTE GENERAL TODOS</h3>
                </div>
                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <div class="row">

                                <label>PDF Existencias</label>
                                <button type="button" onclick="pdfExistencias()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                    <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                    Generar PDF
                                </button>


                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>



    <section class="content" style="margin-top: 35px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTE GENERAL DE EXISTENCIAS (TODOS EN GENERAL)</h3>
                </div>
                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <div class="row">

                                <div class="form-group">
                                    <label>Desde</label>
                                    <input type="date"  class="form-control" id="fecha-desde">
                                </div>

                                <div class="form-group" style="margin-left: 15px">
                                    <label>Hasta</label>
                                    <input type="date" class="form-control" id="fecha-hasta">
                                </div>


                                <button type="button" onclick="pdfExistenciasFecha()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                    <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                    Generar PDF
                                </button>
                            </div>

                            <div class="form-group" style="margin-top: 5px">
                                <label for="checkbox-todos" class="checkbox-label">
                                    <input type="checkbox" class="checkbox" id="checkbox-todos">
                                    Todos los Productos
                                </label>
                            </div>

                            <label>Productos</label>
                            <select class="form-control" id="select-productos" style="height: 150px" multiple="multiple">
                                @foreach($arrayProductos as $item)
                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                @endforeach
                            </select>

                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>



    <section class="content" style="margin-top: 35px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTE SALIDAS DE EXISTENCIAS (POR LOTES)</h3>
                </div>
                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <div class="row">

                                <div class="form-group">
                                    <label>Desde</label>
                                    <input type="date"  class="form-control" id="fecha-desdelote">
                                </div>

                                <div class="form-group" style="margin-left: 15px">
                                    <label>Hasta</label>
                                    <input type="date" class="form-control" id="fecha-hastalote">
                                </div>


                                <button type="button" onclick="pdfExistenciasFechaLote()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                    <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                    Generar PDF
                                </button>
                            </div>

                            <label>Lotes</label>
                            <select class="form-control" id="select-lotes" style="height: 150px" multiple="multiple">
                                @foreach($arrayLotes as $item)
                                    <option value="{{$item->id}}">{{$item->lote}}</option>
                                @endforeach
                            </select>

                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>




    <section class="content" style="margin-top: 35px; margin-bottom: 60px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">DESGLOSE DE MOVIMIENTOS DE INVENTARIO</h3>
                </div>
                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <div class="row">

                                <!-- Fechas -->
                                <div class="form-group">
                                    <label>Desde</label>
                                    <input type="date" class="form-control" id="fecha-desde2">
                                </div>

                                <div class="form-group" style="margin-left: 15px">
                                    <label>Hasta</label>
                                    <input type="date" class="form-control" id="fecha-hasta2">
                                </div>
                            </div>

                            <!-- Checkbox: Todos los Productos -->
                            <div class="form-group" style="margin-top: 5px">
                                <label>
                                    <input type="checkbox" class="checkbox" id="checkboxdesglose-todos">
                                    Todos los Productos
                                </label>
                            </div>

                            <!-- Select de Productos -->
                            <div class="form-group">
                                <label>Productos</label>
                                <select class="form-control" id="select-productos2" style="height: 150px">
                                    @foreach($arrayProductos as $item)
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Botón PDF -->
                            <div class="form-group" style="margin-top: 10px">
                                <button type="button" onclick="pdfExistenciasFechaDesglose()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                    <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                    Generar PDF
                                </button>
                            </div>


                        </div>
                    </section>
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
        $(document).ready(function () {

            $('#select-productos').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            $('#select-productos2').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-lotes').select2({
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


        function pdfExistencias(){
            window.open("{{ URL::to('admin/bodega/reportes/pdf-existencias') }}");
        }


        function pdfExistenciasFecha(){
            var fechadesde = document.getElementById('fecha-desde').value;
            var fechahasta = document.getElementById('fecha-hasta').value;
            var checkbox = document.getElementById('checkbox-todos');
            var valorCheckbox = checkbox.checked ? 1 : 0;

            if(fechadesde === ''){
                toastr.error('Fecha desde es requerido');
                return;
            }

            if(fechahasta === ''){
                toastr.error('Fecha hasta es requerido');
                return;
            }

            // Convertir a objetos Date para comparar
            let dateDesde = new Date(fechadesde);
            let dateHasta = new Date(fechahasta);

            if (dateHasta < dateDesde) {
                toastr.error('La Fecha Hasta no puede ser menor que la Fecha Desde');
                return;
            }


            var valores = $('#select-productos').val();
            if(valores.length ==  null || valores.length === 0){
                if(valorCheckbox === 0){
                    toastr.error('Seleccionar mínimo 1 Producto o marcar TODOS');
                    return;
                }
            }

            var selected = [];
            for (var option of document.getElementById('select-productos').options){
                if (option.selected) {
                    selected.push(option.value);
                }
            }

            let listado = selected.toString();
            let reemplazo = listado.replace(/,/g, "-");
            if(valorCheckbox === 1){
                reemplazo = "nada";
            }

            window.open("{{ URL::to('admin/bodega/reportes/pdf/existencias-fechas') }}/" +
                fechadesde + "/" + fechahasta + "/" + valorCheckbox + "/" + reemplazo);
        }


        function pdfExistenciasFechaDesglose(){
            var fechadesde = document.getElementById('fecha-desde2').value;
            var fechahasta = document.getElementById('fecha-hasta2').value;
            var idproducto = document.getElementById('select-productos2').value;
            var checkbox = document.getElementById('checkboxdesglose-todos');
            var valorCheckbox = checkbox.checked ? 1 : 0;

            if(fechadesde === ''){
                toastr.error('Fecha desde es requerido');
                return;
            }

            if(fechahasta === ''){
                toastr.error('Fecha hasta es requerido');
                return;
            }

            // Convertir a objetos Date para comparar
            let dateDesde = new Date(fechadesde);
            let dateHasta = new Date(fechahasta);

            if (dateHasta < dateDesde) {
                toastr.error('La Fecha Hasta no puede ser menor que la Fecha Desde');
                return;
            }

            if(checkbox){
                window.open("{{ URL::to('admin/bodega/reportes/pdf/existencias/desglosetodos') }}/" +
                    fechadesde + "/" + fechahasta);
            }else{
                window.open("{{ URL::to('admin/bodega/reportes/pdf/existencias/desglose') }}/" +
                    fechadesde + "/" + fechahasta + "/" + idproducto);
            }
        }



        // REPORTE POR LOTES

        function pdfExistenciasFechaLote(){
            var fechadesde = document.getElementById('fecha-desdelote').value;
            var fechahasta = document.getElementById('fecha-hastalote').value;

            if(fechadesde === ''){
                toastr.error('Fecha desde es requerido');
                return;
            }

            if(fechahasta === ''){
                toastr.error('Fecha hasta es requerido');
                return;
            }

            // Convertir a objetos Date para comparar
            let dateDesde = new Date(fechadesde);
            let dateHasta = new Date(fechahasta);

            if (dateHasta < dateDesde) {
                toastr.error('La Fecha Hasta no puede ser menor que la Fecha Desde');
                return;
            }


            var valores = $('#select-lotes').val();
            if(valores.length ==  null || valores.length === 0){
                toastr.error('Seleccionar mínimo 1 LOTE');
                return;
            }

            var selected = [];
            for (var option of document.getElementById('select-lotes').options){
                if (option.selected) {
                    selected.push(option.value);
                }
            }

            let listado = selected.toString();
            let reemplazo = listado.replace(/,/g, "-");


            window.open("{{ URL::to('admin/bodega/reportes/pdf/existencias-fechas-lotes') }}/" +
                fechadesde + "/" + fechahasta + "/" + reemplazo);
        }





    </script>

@endsection
