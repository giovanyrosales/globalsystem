@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<style>

    .modal-xl {
        max-width: 90% !important;
    }

</style>

<div class="content-wrapper" style="display: none" id="divcontenedor">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Solicitud IT Año: {{ $anio }}</h1> <br>

                    <p style="color: red; font-weight: bold">Fecha Límite para Actualizar Solicitud: {{ $fechaLimite }}</p>
                </div>
            </div>

            <button type="button" style="font-weight: bold; margin-top: 10px; margin-bottom: 10px; background-color: #28a745; color: white !important;" onclick="modalEjemploDatos()" class="button button-3d button-rounded button-pill button-small">
                <i class="fas fa-info"></i>
                Ejemplo de Equipos Informáticos
            </button>
        </div>
    </section>

    <section class="content" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">

                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">

                                        <table class="table" id="matriz-solicitudes"  data-toggle="table">
                                            <thead>
                                            <tr>
                                                <th style="width: 3%">#</th>
                                                <th style="width: 35%">DESCRIPCIÓN EQUIPO INFORMÁTICO</th>
                                                <th style="width: 12%">CANTIDAD</th>
                                                <th style="width: 8%">OPCIONES</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @if($listado != null)
                                                    @foreach($listado as $item)

                                                        <tr>

                                                            <td>
                                                                <p id='fila"+({{ $item->conteo }})+"'  disabled class='form-control'>{{ $item->conteo }}</p>
                                                            </td>

                                                            <td>
                                                                <input class="form-control" name="arrayNombre[]" data-info='" + {{ $item->id }} + "' maxlength="1000" type="text" autocomplete="off" value="{{ $item->nombre }}">
                                                            </td>

                                                            <td>
                                                                <input class="form-control" name="arrayCantidad[]" oninput='validarNumero(event)' type="number" autocomplete="off" value="{{ $item->cantidad }}">
                                                            </td>

                                                            <td>
                                                                <button class='btn btn-block btn-danger' type="button" onclick='borrarFila(this)'>Borrar</button>
                                                            </td>

                                                        </tr>

                                                        @if($loop->last)
                                                            <script>
                                                                setTimeout(function () {
                                                                    closeLoading();
                                                                }, 1000);
                                                            </script>
                                                        @endif

                                                    @endforeach



                                                @endif
                                            </tbody>
                                        </table>


                                        <div class="col-md-10">
                                        <button style="width: 100%; margin-left: 15px; margin-right: 15px" type="button" class="btn btn-success" onclick="agregarNuevaFila()">Agregar Fila</button>
                                        </div>

                                    </div>
                                </div>
                            </section>


                            <div id="contenedorGuardar">
                            <button style="float: right; margin-right: 15px; margin-top: 60px" type="button" class="btn btn-primary" onclick="modalVerificarDatos()">GUARDAR DATOS</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ejemplo de Equipo Informático</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">


                                    <table class="table" id="matriz-informatico"  data-toggle="table">
                                        <thead>
                                        <tr>
                                            <th style="width: 3%">#</th>
                                            <th style="width: 6%">OBJ. ESPECÍFICO</th>
                                            <th style="width: 35%">DESCRIPCIÓN</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                            @foreach($arrayInformatico as $item)

                                                <tr>
                                                    <td>
                                                        <p class="form-control">{{ $item->conteo }}</p>
                                                    </td>

                                                    <td>
                                                        <p class="form-control">{{ $item->codigo }}</p>
                                                    </td>

                                                    <td>
                                                        <p class="form-control">{{ $item->descripcion }}</p>
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>
                                    </table>




                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            let lista = {{ $haydatos }};
            if(lista === 1){
                openLoading();
            }

            let puedeActualizar = {{ $puedeActualizar }};

            if(puedeActualizar === 1){
                var inputContainer = document.getElementById('contenedorGuardar');
                inputContainer.style.display = 'none';
            }

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function modalEjemploDatos(){
            $('#modalEditar').modal('show');
        }

        function agregarNuevaFila(){

            var nFilas = $('#matriz-solicitudes >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>"+

                "<td>"+
                "<p id='fila"+(nFilas)+"' disabled class='form-control'>"+(nFilas)+"</p>"+
                "</td>"+

                "<td>"+
                "<input name='arrayNombre[]' autocomplete='off' data-info='" + 0 + "' maxlength='1000' class='form-control' type='text'>"+
                "</td>"+

                "<td>"+
                "<input name='arrayCantidad[]' autocomplete='off' oninput='validarNumero(event)' class='form-control' type='number'/>"+
                "</td>"+


                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matriz-solicitudes tbody").append(markup);
        }



        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFilaConteo()
        }

        function setearFilaConteo(){

            var table = document.getElementById('matriz-solicitudes');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        function modalVerificarDatos(){
            Swal.fire({
                title: 'Guardar',
                text: "Al guardar siempre podra editar los datos hasta la Fecha Límite",
                icon: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarDatos()
                }
            })
        }


        function guardarDatos(){

            // ** VERIFICAR TABLA **

            var reglaNumeroEntero = /^[0-9]\d*$/;
            const contenedorArray = [];

            var arrayNombre = $("input[name='arrayNombre[]']").map(function(){return $(this).val();}).get();
            var arrayCantidad = $("input[name='arrayCantidad[]']").map(function(){return $(this).val();}).get();
            var arrayID = $("input[name='arrayNombre[]']").map(function(){return $(this).attr("data-info");}).get();


            // recorrer cada fila
            for(var i = 0; i < arrayNombre.length; i++){

                let fila = i+1
                let infoNombre = arrayNombre[i];
                let infoCantidad = arrayCantidad[i];
                let infoID = arrayID[i];

                if(infoNombre.length <= 0){
                    alertaFilaVacia('Fila #' + (fila) + ' Nombre es requerido')
                    return;
                }


                if(infoCantidad.length <= 0){
                    alertaFilaVacia('Fila #' + (fila) + ' Cantidad es requerido')
                    return;
                }


                if(!infoCantidad.match(reglaNumeroEntero)) {
                    alertaFilaVacia('Fila #' + (fila) + ' Cantidad debe ser Número Entero Positivo')
                    return;
                }

                if(infoCantidad <= 0){
                    alertaFilaVacia('Fila #' + (fila) + ' Cantidad no debe ser negativo o igual a Cero')
                    return;
                }

                if(infoCantidad > 1000000){
                    toastr.error('Fila #' + (fila) + ' Cantidad máxima no debe superar 1 millón');
                    return;
                }


                contenedorArray.push({ infoNombre, infoCantidad, infoID });
            }

            openLoading();
            var formData = new FormData();

            let idanio = {{ $idanio }};

            formData.append('idanio', idanio);
            formData.append('contenedorArray', JSON.stringify(contenedorArray));

            axios.post(url+'/p/solicitudesit/guardardatos', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.error('Fecha Límite Superada, no se ha guardado');
                    }
                    else if(response.data.success === 2){
                        toastr.success('Guardado Correctamente');
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


        function alertaFilaVacia(mensaje){
            Swal.fire({
                title: 'Campo Requerido',
                text: mensaje,
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        }


        function validarNumero(event) {
            let input = event.target;
            // Remueve cualquier carácter que no sea un número, punto decimal o signo menos
            input.value = input.value.replace(/[^0-9.-]/g, '');
        }

    </script>


@endsection
