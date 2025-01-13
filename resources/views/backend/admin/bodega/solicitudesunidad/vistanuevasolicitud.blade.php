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
                    <h3 class="card-title">NUEVA SOLICITUD</h3>
                </div>
                <div class="card-body">

                    <section class="content">
                        <div class="container-fluid" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            <div class="row">
                                <div class="form-group col-md-5" style="margin-top: 5px">
                                    <label style="color: #686868">Código específico</label>
                                    <div>
                                        <select id="select-especifico" class="form-control">
                                            @foreach($arrayCodigo as $item)
                                                <option value="{{$item->id}}">{{$item->codigo}} - {{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>


                    <hr>


                    <section class="content">
                        <div class="container-fluid" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">

                            <div class="row">
                                <div class="form-group col-md-5" style="margin-top: 5px">
                                    <label class="control-label" style="color: #686868">Descripción (con especificaciones claras de lo que requieren)</label>
                                    <div>
                                        <input type="text" id="nombre" maxlength="300" autocomplete="off" class="form-control">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="form-group col-md-5" style="margin-top: 5px">
                                    <label style="color: #686868">Unidad de medida</label>
                                    <div>
                                        <select id="select-unidad" class="form-control">
                                            @foreach($arrayMedida as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-2" style="margin-top: 5px">
                                    <label style="color: #686868">Cantidad solicitada</label>
                                    <div>
                                        <input type="number" id="cantidad" min="0" max="1000000" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="form-group col-md-2" style="margin-top: 5px">
                                    <label style="color: #686868">Prioridad</label>
                                    <div>
                                        <select id="select-prioridad" class="form-control">
                                                <option value="1">Baja</option>
                                                <option value="2">Media</option>
                                                <option value="3">Alta</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                    <br>
                    <hr>

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
                <h2>Detalle de Solicitud</h2>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información de Solicitud</h3>
                </div>

                <table class="table" id="matriz" data-toggle="table" style="margin-right: 15px; margin-left: 15px;">
                    <thead>
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 10%">Descripción</th>
                        <th style="width: 6%">U/M</th>
                        <th style="width: 6%">Prioridad</th>
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
        <button type="button" class="btn btn-success" onclick="preguntarGuardar()">Guardar Solicitud</button>
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

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function agregarFila(){

            var nombre = document.getElementById('nombre').value;
            var unidad = document.getElementById('select-unidad').value;
            var cantidad = document.getElementById('cantidad').value;
            var prioridad = document.getElementById('select-prioridad').value;

            var reglaNumeroEntero = /^[0-9]\d*$/;
            var reglaNumeroDiesDecimal = /^([0-9]+\.?[0-9]{0,10})$/;

            var selectElementUnidad = document.getElementById('select-unidad');
            var selectElementPrioridad = document.getElementById('select-prioridad');
            var textoUnidad = selectElementUnidad.options[selectElementUnidad.selectedIndex].text;
            var textoPrioridad = selectElementPrioridad.options[selectElementPrioridad.selectedIndex].text;


            if(nombre === ''){
                toastr.error('Nombre de producto es requerido')
                return;
            }

            //**************

            if(cantidad === ''){
                toastr.error('Cantidad es requerido');
                return;
            }

            if(!cantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad es requerido');
                return;
            }

            if(cantidad <= 0){
                toastr.error('Cantidad Mínima no debe tener negativos o cero');
                return;
            }

            if(cantidad > 9000000){
                toastr.error('Cantidad máximo debe ser 9 millones');
                return;
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
                "<input name='arrayNombre[]' disabled value='" + nombre + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<input name='arrayUnidad[]' disabled data-idunidad='" + unidad + "' value='" + textoUnidad + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<input name='arrayPrioridad[]' disabled data-idprioridad='" + prioridad + "' value='" + textoPrioridad + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<input name='arrayCantidad[]' disabled value='" + cantidad + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>" +
                "</td>" +

                "</tr>";

            $("#matriz tbody").append(markup);


            // CALCULAR TODAS LAS FILAS

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Agregado al Detalle',
                showConfirmButton: false,
                timer: 1500
            })

            document.getElementById('nombre').value = '';
            document.getElementById('cantidad').value = '';
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
                title: '¿Registrar Solicitud?',
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
                    registrarSolicitud();
                }
            })
        }


        function registrarSolicitud(){

            var nRegistro = $('#matriz > tbody >tr').length;

            if (nRegistro <= 0){
                toastr.error('Productos a Ingresar son requeridos');
                return;
            }

            var idObjEspeci = document.getElementById('select-especifico').value;

            var arrayNombre = $("input[name='arrayNombre[]']").map(function(){return $(this).val();}).get();
            var arrayUnidad = $("input[name='arrayUnidad[]']").map(function(){return $(this).attr("data-idunidad");}).get();
            var arrayPrioridad = $("input[name='arrayPrioridad[]']").map(function(){return $(this).attr("data-idprioridad");}).get();
            var arrayCantidad = $("input[name='arrayCantidad[]']").map(function(){return $(this).val();}).get();

            colorBlancoTabla();

            openLoading();

            let formData = new FormData();

            const contenedorArray = [];

            for(var i = 0; i < arrayNombre.length; i++){

                let infoProducto = arrayNombre[i];
                let infoIdUnidad = arrayUnidad[i];
                let infoIdPrioridad = arrayPrioridad[i];
                let infoCantidad = arrayCantidad[i];

                // ESTOS NOMBRES SE UTILIZAN EN CONTROLADOR
                contenedorArray.push({ infoProducto, infoIdUnidad, infoIdPrioridad, infoCantidad });
            }


            formData.append('contenedorArray', JSON.stringify(contenedorArray));

            formData.append('idObjEspeci', idObjEspeci);

            axios.post(url+'/bodega/registrar/nuevasolicitud', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
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

        function limpiar(){
            document.getElementById('nombre').value = '';
            document.getElementById('cantidad').value = '';

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






