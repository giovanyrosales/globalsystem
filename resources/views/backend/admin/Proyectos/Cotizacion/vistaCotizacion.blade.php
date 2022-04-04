@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6" style="margin-right: 10px;">
                <h1>Crear Cotización</h1>
            </div>
        </div>
    </section>

    <section class="content" >
        <div class="container-fluid">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Formulario</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Destino</label>
                                <input type="text" value="{{ $requisicion->destino }}" class="form-control" id="destino" disabled>
                            </div>

                            <div class="form-group">
                                <label>Necesidad</label>
                                <textarea class="form-control" rows="3" disabled>{{ $requisicion->necesidad }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Proveedor</label>
                                <select class="form-control" id="select-proveedor">
                                    @foreach($proveedores as $data)
                                        <option value="{{ $data->id }}">{{ $data->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Fecha de cotización *:</label>
                                <input type="date" id="fecha-cotizacion" class="form-control" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <!-- Selección del lado izquierdo -->
                                <div class="col-xs-5 col-md-5 col-sm-5">
                                    <label>Lista de Items de requisición</label>
                                    <select name="from[]" id="mySideToSideSelect" class="form-control" size="8" multiple="multiple">
                                        @foreach ($requisicionDetalle as $item)
                                            <option value="{{$item->id}}">{{$item->descripcion}}</option>
                                        @endforeach
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

                    <!-- boton generar cotizacion -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" onclick="revisarCotizacion()" class="btn btn-primary float-right mt-3" >Generar Cotización</button>
                            <button type="button" onclick="location.href='javascript: history.go(-1)'" class="btn float-left btn-default mt-3">Cancelar</button>
                        </div>
                    </div>

                    <!-- Modal Agregar Cotizacion -->
                    <div class="modal fade" id="modalAgregarCotizacion" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Detalles Cotización</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formulario-crear-cotizacion">
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table" id="matriz"  data-toggle="table">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 3%">#</th>
                                                        <th style="width: 3%">Cantidad</th>
                                                        <th style="width: 15%">Descripción</th>
                                                        <th style="width: 5%">Unidad Medida</th>
                                                        <th style="width: 3%">Precio Unitario</th>
                                                        <th style="width: 5%">Cod. Presup</th>
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
                                    <button type="button" class="btn btn-primary" id="btnGuardarCotizacion">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="{{ asset('js/multiselect.min.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function(){
            document.getElementById("divcontenedor").style.display = "block";

            var fecha = new Date();
            document.getElementById('fecha-cotizacion').value = fecha.toJSON().slice(0,10);

            $('#mySideToSideSelect').multiselect();
        });
    </script>

    <script type="text/javascript">

        function revisarCotizacion(){

            openLoading();

            let lista = [];
            $('option','#mySideToSideSelect_to').each(function(){
                lista.push($(this).attr('value'));
            });

            document.getElementById("formulario-crear-cotizacion").reset();
            $('#tablecrearcotizacion tbody').empty();
            //$('#matriz tbody').empty();

            axios.post(url+'/proyecto/lista/cotizaciones', {
                'lista' : lista
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
                               "<input disabled value='"+infodetalle[i].cantidad+"' maxlength='10' class='form-control' type='number'>"+
                               "</td>"+

                               "<td>"+
                               "<input disabled class='form-control' value='"+infodetalle[i].descripcion+"' style='width:100%' type='text'>"+
                               "</td>"+

                               "<td>"+
                               "<input disabled class='form-control' value='"+infodetalle[i].medida+"' style='width:100%' type='text'>"+
                               "</td>"+

                               "<td>"+
                               "<input name='precio[]' maxlength='10' class='form-control' type='number'>"+
                               "</td>"+

                               "<td>"+
                               "<input name='codigo[]' maxlength='100' class='form-control' type='text'>"+
                               "</td>"+

                               "</tr>";

                           $("#matriz tbody").append(markup);
                       }

                       $('#modalAgregarCotizacion').css('overflow-y', 'auto');
                       $('#modalAgregarCotizacion').modal('show');
                   }else{
                       toastr.error('información no encontrada');
                   }

                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function verificar(){
            Swal.fire({
                title: 'Generar Cotización?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    crearCotizacion();
                }
            })
        }

        function crearCotizacion(){

            // fecha de cotizacion
            var fecha = document.getElementById('fecha-cotizacion').value;
            var proveedor = document.getElementById('select-proveedor').value;

            if(fecha === ''){
                toastr.error('Fecha de cotización es requerida');
                return;
            }

            if(proveedor === ''){
                toastr.error('Proveedor es requerido');
                return;
            }

            var nRegistro = $('#matriz-requisicion >tbody >tr').length;

            if(nRegistro <= 0){
                toastr.error('Se necesitan Materiales para cotización');
                return;
            }

            let formData = new FormData();
            var id = {{ $id }}; // id de la requisicion

            var precio = $("input[name='precio[]']").map(function(){return $(this).val();}).get();
            var codigo = $("input[name='codigo[]']").map(function(){return $(this).val();}).get();

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

                for(var a = 0; a < precio.length; a++){
                    let datoPrecio = precio[a];

                    if(datoPrecio === ''){
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad es requerida');
                        return;
                    }

                    if(!datoPrecio.match(reglaNumeroDecimal)) {
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad debe ser decimal y no negativo');
                        return;
                    }

                    if(datoPrecio <= 0){
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad no debe ser negativo');
                        return;
                    }

                    if(datoPrecio.length > 10){
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad máximo 10 caracteres');
                        return;
                    }
                }

                // codigo puede ser null
                for(var b = 0; b < codigo.length; b++){

                    var datoCodigo = codigo[b];

                    if(datoCodigo.length > 10){
                        colorRojoTabla(b);
                        toastr.error('Fila #' + (b+1) + ' Código tiene más de 100 caracteres');
                    }
                }

                // como tienen la misma cantidad de filas, podemos recorrer
                // todas las filas de una vez
                for(var p = 0; p < cantidad.length; p++){
                    var idarr = $("#matriz tr:eq("+(p+1)+")").attr('id');
                    formData.append('idarray[]', idarr);
                    formData.append('precio[]', precio[p]);
                    formData.append('codigo[]', codigo[p]);
                }

            openLoading();

            formData.append('fecha', fecha);
            formData.append('proveedor', proveedor);
            formData.append('id', id);

            axios.post(url+'/proyecto/vista/requisicion/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregarRequisicion').modal('hide');
                        toastr.success('Registrado correctamente');
                        recargarRequisicion();
                        limpiarRequisicion(response.data.contador);
                    }
                    else{
                        toastr.error('error al crear requisición');
                    }
                })
                .catch((error) => {
                    toastr.error('error al crear requisición');
                    closeLoading();
                });
        }

        function colorRojoTabla(index){
            $("#matriz tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function removeOptionsFromSelect(selectElement) {
            var i, L = selectElement.options.length - 1;
            for(i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }

    </script>



@endsection
