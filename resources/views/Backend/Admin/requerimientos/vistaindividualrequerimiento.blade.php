@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>Requisiciones Pendientes</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Requisiciones Pendientes</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Requisiciones</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalCotizar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cotizar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-cotizar-nuevo">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Destino</label>
                                        <input type="text" class="form-control" id="destino" disabled>
                                        <input id="idcotizar" type="hidden" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Necesidad</label>
                                        <textarea class="form-control" id="necesidad" rows="3" disabled></textarea>
                                    </div>
                                </div>

                                <div class="col-md-5">

                                        <label>Proveedor</label>
                                        <select class="form-control" id="select-proveedor">
                                            @foreach($proveedores as $proveedor)
                                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                            @endforeach
                                        </select>

                                    <div class="form-group">
                                        <label>Fecha de cotización:</label>
                                        <input type="date" id="fecha-cotizacion" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <!-- Selección del lado izquierdo -->
                                        <div class="col-xs-5 col-md-5 col-sm-5">
                                            <label>Lista de Items de Requisición</label>
                                            <select name="from[]" id="mySideToSideSelect" class="form-control" size="8" multiple="multiple">

                                            </select>
                                        </div>

                                        <!-- Botones de acción -->
                                        <div class="col-xs-2 col-md-2 col-sm-2">

                                            <label>&nbsp;</label>
                                            <button type="button" id="mySideToSideSelect_rightAll" class="btn btn-secondary col-xs-12 col-md-12 col-sm-12 mt-1"><i class="fas fa-forward"></i></button>
                                            <button type="button" id="mySideToSideSelect_rightSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-right"></i></button>
                                            <button type="button" id="mySideToSideSelect_leftSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-left"></i></button>
                                            <button type="button" id="mySideToSideSelect_leftAll" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-backward"></i></button>
                                        </div>

                                        <!-- Selección del lado derecho -->
                                        <div class="col-xs-5 col-md-5 col-sm-5">
                                            <label>Lista de Items a cotizar</label>
                                            <select name="to[]" id="mySideToSideSelect_to" class="form-control" size="8" multiple="multiple"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="detalleCotizacion()">Detalle</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detalles Cotizacion</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulariocrearcotizacion">
                        <div class="card-body">
                            <div class="row">
                                <table  class="table" id="matriz-requisicion"  data-toggle="table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%">#</th>
                                        <th style="width: 5%">Cantidad</th>
                                        <th style="width: 12%">Descripción</th>
                                        <th style="width: 5%">Medida</th>
                                        <th style="width: 5%">Precio U.</th>
                                        <th style="width: 5%">Total</th>
                                        <th style="width: 8%">Cod. Presup</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="verificarCotizacion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>




</div>

@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/multiselect.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/requerimientos/listado/tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

            $('#mySideToSideSelect').multiselect();

            $('#select-proveedor').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });
        });

    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/requerimientos/listado/tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }

        function modalCotizar(id){

            document.getElementById("formulario-cotizar-nuevo").reset();
            $('#select-proveedor').prop('selectedIndex', 0).change();
            document.getElementById("mySideToSideSelect").options.length = 0;
            document.getElementById("mySideToSideSelect_to").options.length = 0;

            openLoading();

            axios.post(url+'/requerimientos/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalCotizar').modal('show');
                        $('#idcotizar').val(id);
                        $('#destino').val(response.data.info.destino);
                        $('#necesidad').val(response.data.info.necesidad);

                        var fecha = new Date();
                        document.getElementById('fecha-cotizacion').value = fecha.toJSON().slice(0,10);

                        $.each(response.data.listado, function( key, val ){
                            $('#mySideToSideSelect').append('<option value='+val.id+'>'+val.nombre+'</option>');
                        });
                    }
                    else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function detalleCotizacion(){

            var fecha = document.getElementById('fecha-cotizacion').value;
            $("#matriz-requisicion tbody tr").remove();

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            var formData = new FormData();
            var hayLista = true;

            $("#mySideToSideSelect_to option").each(function(){
                hayLista = false;
                formData.append('lista[]', $(this).val());
            });

            if(hayLista){
                Swal.fire({
                    title: 'Nota',
                    text: "Lista de Items de Requisición es Requerido",
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                });
                return;
            }

            openLoading();

            axios.post(url+'/requerimientos/verificar', formData,{

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        var infodetalle = response.data.lista;

                        for (var i = 0; i < infodetalle.length; i++) {

                            var markup = "<tr id='"+infodetalle[i].id+"'>"+

                                "<td>"+
                                "<p id='fila"+(i+1)+"' class='form-control' style='max-width: 65px'>"+(i+1)+"</p>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].cantidad+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].nombre+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].medida+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].pu+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].multiTotal+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].codigo+"' disabled class='form-control'>"+
                                "</td>"+

                                "</tr>";

                            $("#matriz-requisicion tbody").append(markup);
                        }

                        // TOTAL (CANTIDAD * PRECIO UNITARIO)

                        var markup = "<tr id=''>"+

                            "<td>"+
                            "<p class='form-control' style='max-width: 65px'>Total</p>"+
                            "</td>"+

                            "<td>"+
                            "<input value='"+response.data.totalCantidad+"' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='"+response.data.totalPrecio+"' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='"+response.data.totalMulti+"' disabled class='form-control'>"+
                            "</td>"+

                            "</tr>";

                        $("#matriz-requisicion tbody").append(markup);

                        $('#modalDetalle').css('overflow-y', 'auto');
                        $('#modalDetalle').modal({backdrop: 'static', keyboard: false})
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function verificarCotizacion(){
            Swal.fire({
                title: 'Guardar',
                text: "Nueva Cotización",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarCotizacion();
                }
            });
        }

        function guardarCotizacion(){

            var fecha = document.getElementById('fecha-cotizacion').value;
            var proveedor = document.getElementById('select-proveedor').value;

            var formData = new FormData();
            formData.append('fecha', fecha);
            formData.append('proveedor', proveedor);
            formData.append('idcotizar', $('#idcotizar').val());

            $("#mySideToSideSelect_to option").each(function(){
                hayLista = false;
                formData.append('lista[]', $(this).val());
            });

            openLoading();

            axios.post(url+'/requerimientos/cotizacion/guardar', formData,{

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalCotizar').modal('hide');
                        $('#modalDetalle').modal('hide');

                        recargar();

                        toastr.success('guardado');
                    }else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });
        }


        function removeOptionsFromSelect(selectElement) {
            var i, L = selectElement.options.length - 1;
            for(i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }

    </script>


@endsection
