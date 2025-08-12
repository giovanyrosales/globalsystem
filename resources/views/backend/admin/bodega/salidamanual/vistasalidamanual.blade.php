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

    .cursor-pointer:hover {
        cursor: pointer;
        color: #401fd2;
        font-weight: bold;
    }

    *:focus {
        outline: none;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content" style="margin-top: 20px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">SALIDA DE PRODUCTOS MANUAL</h3>
                </div>
                <div class="card-body">

                    <section class="content">
                        <div class="container-fluid">

                            <div class="row">
                                <div class="form-group col-md-2" style="margin-top: 5px">
                                    <label class="control-label" style="color: #686868">Fecha: </label>
                                    <div>
                                        <input type="date" id="fecha" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4" style="margin-top: 5px">
                                    <label class="control-label" style="color: #686868">Tipo de Salida</label>
                                    <select id="select-salida" class="form-control">
                                        <option value="1">Salida sin Solicitud</option>
                                        <option value="2">Salida por Desperfecto</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Asignar Unidad (Opcional):</label>
                                <br>
                                <select width="100%" class="form-control" id="select-unidad">
                                    <option value="">Seleccionar Opción</option>
                                    @foreach($arrayUnidades as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="row">
                                <div class="form-group col-md-5" style="margin-top: 5px">
                                    <label style="color: #686868">Observación: </label>
                                    <div>
                                        <input type="text" id="observacion" maxlength="300" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <br>

                    <div class="border-box" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px;">


                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="form-group col-md-8" style="margin-top: 5px">
                                        <h3 class="card-title" style="color: #005eab; font-weight: bold">Buscar Producto</h3>
                                        <div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </section>

                        <section class="content" style="margin-top: 15px">
                            <div class="container-fluid">

                                <select class="form-control" id="select-material" onchange="productoSeleccionado()">
                                    <option value="0" selected>Seleccionar Producto</option>
                                    @foreach($arrayEntraDeta as $item)
                                        <option value="{{ $item->id }}" data-cantidadActual="{{ $item->cantidadRestante }}">{{ $item->nombreMaterial }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </section>

                        <section class="content">
                            <div class="container-fluid">

                                <div class="row">

                                    <div class="form-group col-md-2" style="margin-top: 5px">
                                        <label class="control-label" style="color: #686868">Cantidad Disponible: </label>
                                        <div>
                                            <input type="text" disabled autocomplete="off" class="form-control" id="cantidad-disponible" placeholder="0">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2" style="margin-top: 5px">
                                        <label class="control-label" style="color: #686868">Cantidad Retirar: </label>
                                        <div>
                                            <input type="text" autocomplete="off" class="form-control" id="cantidad-retirar" placeholder="0">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>

                    </div>

                    <br>

                    <section class="content">
                        <div class="container-fluid">

                            <div style="margin-right: 30px">
                                <button type="button" style="float: right" class="btn btn-success" onclick="agregarFila();">Agregar a Tabla</button>

                            </div>

                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2>Detalle de Ingreso</h2>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información de Ingreso</h3>
                </div>

                <table class="table" id="matriz" data-toggle="table" style="margin-right: 15px; margin-left: 15px;">
                    <thead>
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 10%">Producto</th>
                        <th style="width: 6%">Cantidad</th>
                        <th style="width: 5%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </section>

    <div class="modal-footer justify-content-end" style="margin-top: 25px;">
        <button type="button" class="btn btn-success" onclick="preguntarGuardar()">Guardar Salida</button>
    </div>



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

            $('#select-material').select2({
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


        function productoSeleccionado(){

            var verificarSelect = document.getElementById('select-material').value;

            if(verificarSelect === '0'){
                // borrar input de informacion
                document.getElementById('cantidad-disponible').value = '';
                document.getElementById('cantidad-retirar').value = '';
                return;
            }

            // Obtener la opción seleccionada
            var selectedOption = document.getElementById('select-material').options[document.getElementById('select-material').selectedIndex];

            document.getElementById('cantidad-disponible').value = selectedOption.getAttribute('data-cantidadActual');
        }


        function agregarFila(){

            var idBodeEntraDeta = document.getElementById('select-material').value;
            var cantidadDisponible = document.getElementById('cantidad-disponible').value;

            if(idBodeEntraDeta === '0'){
                toastr.error('Seleccionar Producto');
                return;
            }

            var selectElement = document.getElementById('select-material');
            var selectedOption = selectElement.options[selectElement.selectedIndex];

            var cantidad = document.getElementById('cantidad-retirar').value;
            var reglaNumeroEntero = /^[0-9]\d*$/;


            //**************

            if(cantidad === ''){
                toastr.error('Cantidad es requerido');
                return;
            }

            if(!cantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad es requerido');
                return;
            }

            if(cantidad < 0){
                toastr.error('Cantidad Mínima no debe tener negativos');
                return;
            }

            if(cantidad > 9000000){
                toastr.error('Cantidad máximo debe ser 9 millones');
                return;
            }

            if(cantidad > Number(cantidadDisponible)){
                toastr.error('Cantidad Retirar es mayor a la Disponible');
                return
            }

            //**************

            // Crear un objeto Date a partir del valor del input

            var nFilas = $('#matriz >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>" +

                "<td>" +
                "<p id='fila" + (nFilas) + "' class='form-control' style='max-width: 65px'>" + (nFilas) + "</p>" +
                "</td>" +

                "<td>" +
                "<input name='arrayNombre[]' disabled data-idproducto='" + idBodeEntraDeta + "' value='" + selectedOption.innerText + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<input name='arrayCantidad[]' disabled value='" + cantidad + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>" +
                "</td>" +

                "</tr>";

            $("#matriz tbody").append(markup);

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Agregado al Detalle',
                showConfirmButton: false,
                timer: 1500
            })

            document.getElementById('cantidad-retirar').value = '';
        }

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFila();
        }

        function setearFila(){

            var table = document.getElementById('matriz');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }


        function preguntarGuardar(){

            Swal.fire({
                title: '¿Registrar Salida?',
                text: '',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                allowOutsideClick: false,
                confirmButtonText: 'SI',
                cancelButtonText: 'NO'
            }).then((result) => {
                if (result.isConfirmed) {
                    registrarProductos();
                }
            })
        }


        function registrarProductos(){

            var fecha = document.getElementById('fecha').value;
            var observacion = document.getElementById('observacion').value;
            var tipoSalida = document.getElementById('select-salida').value;
            var selectUnidad = document.getElementById('select-unidad').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            var nRegistro = $('#matriz > tbody >tr').length;

            if (nRegistro <= 0){
                toastr.error('Productos a Ingresar son requeridos');
                return;
            }

            // ID BODEGA ENTRADA DETALLE
            var arrayIdProducto = $("input[name='arrayNombre[]']").map(function(){return $(this).attr("data-idproducto");}).get();
            var arrayCantidad = $("input[name='arrayCantidad[]']").map(function(){return $(this).val();}).get();

            var reglaNumeroEntero = /^[0-9]\d*$/;


            // VALIDACIONES DE CADA FILA, RECORRER 1 ELEMENTO YA QUE TODOS TIENEN LA MISMA CANTIDAD DE FILAS

            colorBlancoTabla();

            for(var a = 0; a < arrayIdProducto.length; a++){

                let idProducto = arrayIdProducto[a];
                let cantidadProducto = arrayCantidad[a];

                // identifica si el 0 es tipo number o texto
                if(idProducto == 0){
                    colorRojoTabla(a);
                    alertaMensaje('info', 'No encontrado', 'En la Fila #' + (a+1) + " El Producto no se encuentra. Por favor borrar la Fila y buscar de nuevo el Producto");
                    return;
                }

                // **** VALIDAR CANTIDAD DE PRODUCTO

                if (cantidadProducto === '') {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad de producto es requerida. Por favor borrar la Fila y buscar de nuevo el Producto');
                    return;
                }

                if (!cantidadProducto.match(reglaNumeroEntero)) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad debe ser entero y no negativo. Por favor borrar la Fila y buscar de nuevo el Producto');
                    return;
                }

                if (cantidadProducto < 0) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad no debe ser negativo. Por favor borrar la Fila y buscar de nuevo el Producto');
                    return;
                }

                if (cantidadProducto > 9000000) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad máximo 9 millones. Por favor borrar la Fila y buscar de nuevo el Producto');
                    return;
                }
            }

            openLoading();

            let formData = new FormData();

            const contenedorArray = [];

            for(var i = 0; i < arrayIdProducto.length; i++){
                let infoIdProducto = arrayIdProducto[i];
                let infoCantidad = arrayCantidad[i];

                // ESTOS NOMBRES SE UTILIZAN EN CONTROLADOR
                contenedorArray.push({ infoIdProducto, infoCantidad });
            }

            formData.append('contenedorArray', JSON.stringify(contenedorArray));
            formData.append('fecha', fecha);
            formData.append('observacion', observacion);
            formData.append('tiposalida', tipoSalida);
            formData.append('selectUnidad', selectUnidad);

            axios.post(url+'/bodega/salidasmanual/registrar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    // REVISAR CANTIDAD PORQUE SUPERA A LAS DISPONIBLES
                    if(response.data.success === 1){
                        cantidadSuperada(response.data.fila)
                    }
                    else if(response.data.success === 2){
                        toastr.success('Registrado correctamente');
                        limpiar();
                    }
                    else{
                        toastr.error('error al guardar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al guardar');
                    closeLoading();
                });
        }

        function cantidadSuperada(fila){

            let texto = "En la Fila #" + fila + " - Se supera la cantidad disponible, revisar las salidas Similares " +
                "o verificar cantidad de Producto Disponible";

            Swal.fire({
                title: 'Cantidad Superada',
                text: texto,
                icon: 'error',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                allowOutsideClick: false,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'NO'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        }

        function limpiar(){

            document.getElementById('cantidad-retirar').value = '';
            document.getElementById('observacion').value = '';
            document.getElementById('fecha').value = '';

            $("#matriz tbody tr").remove();
        }

        function colorRojoTabla(index){
            $("#matriz tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function colorBlancoTabla(){
            $("#matriz tbody tr").css('background', 'white');
        }


    </script>

@endsection






