@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }

    html, body {
        overflow-x: hidden;
    }

    .checkbox-large {
        width: 15px; /* Adjust the width */
        height: 15px; /* Adjust the height */
        transform: scale(1.5); /* Adjust the scale factor as needed */
        -webkit-transform: scale(1.5); /* For Safari */
    }
</style>

<div id="divcontenedor" style="display: none">
    <section class="content-header">
        <div class="container-fluid">

        </div>
    </section>

    <section class="content">
        <div class="container-fluid" style="margin-left: 15px">
            <div class="row">

                <div class="col-md-12">
                    <form id="formulario-nuevo">
                            <div class="card-body">
                                <div class="card card-gray-dark">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-center text-center">
                                            <img src="{{ asset('images/logonuevo.png') }}" style="width: 60px; height: 60px; margin-right: 10px;">
                                            <h3 class="card-title mb-0" style="font-size: 18px">Alcaldía Municipal de Santa Ana Norte - Distrito Metapán</h3>
                                        </div>
                                    </div>
                                </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-blue">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center ">
                                                <h3 class="card-title mb-0">HOJA DE ACTUALIZACIÓN DE DATOS DE PERSONAL</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" id="contenedor-nombrelist">
                                        <label>Nombre Empleado <label style="color: red"> * </label></label>
                                        <select class="form-control" id="select-nombre">
                                            <option value="0">Seleccionar opción</option>
                                            @foreach($listaEmpleados as $dato)
                                                <option value="{{ $dato->id }}">{{ $dato->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="empleado-check" />
                                            <label style="color: red"> * </label> Marcar si NO se encuentra nombre de Empleado en la lista
                                        </label>
                                    </div>

                                    <div id="contenedor-nuevonombre" style="display: none;">
                                        <div class="form-group">
                                            <label>Nombre Empleado <label style="color: red"> * </label></label>
                                            <input type="text" autocomplete="off" id="nombre-nuevo" maxlength="100" placeholder="Nombre" class="form-control">
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Cargo <label style="color: red"> * </label></label>
                                        <select class="form-control" id="select-cargos">
                                            <option value="0">Seleccionar opción</option>
                                            @foreach($listaCargos as $dato)
                                                <option value="{{ $dato->id }}">{{ $dato->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad <label style="color: red"> * </label></label>
                                        <select class="form-control" id="select-unidad">
                                            <option value="0">Seleccionar opción</option>
                                            @foreach($listaUnidad as $dato)
                                                <option value="{{ $dato->id }}">{{ $dato->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>DUI <label style="color: red"> * </label></label>
                                        <input type="text" autocomplete="off" id="dui" maxlength="20" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>NIT <label style="color: red"> (Opcional)</label></label>
                                        <input type="text" autocomplete="off" id="nit" maxlength="20" class="form-control">
                                    </div>


                                </div>
                                <div class="col-md-6">
                                    <div class="card card-blue">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center ">
                                                <h3 class="card-title mb-0">INFORMACIÓN PARTICULAR</h3>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label>Fecha de Nacimiento <label style="color: red"> * </label></label>
                                        <input type="date" id="fecha-nacimiento" min="1900-01-01" max="2020-12-31" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Lugar de Nacimiento <label style="color: red"> * </label></label>
                                        <input type="text" autocomplete="off" id="lugar-nacimiento" maxlength="200" class="form-control">
                                    </div>




                                    <div class="form-group">
                                        <label>Nivel Académico <label style="color: red"> * </label></label>
                                        <select class="form-control" id="select-academico">
                                            <option value="0">Seleccionar Opción</option>
                                            <option value="1">BASICO</option>
                                            <option value="2">MEDIO</option>
                                            <option value="3">SUPERIOR</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Profesión <label style="color: red"> (Opcional)</label></label>
                                        <input type="text" autocomplete="off" id="profesion" maxlength="100" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Dirección Actual <label style="color: red"> * </label></label>
                                        <input type="text" autocomplete="off" id="direccion-actual" maxlength="200" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Celular <label style="color: red"> * </label></label>
                                        <input type="number" autocomplete="off" id="celular" oninput="validarNumero(event)" class="form-control">
                                    </div>

                                    <br>
                                    <hr>

                                    <div class="form-group">
                                        <label>En Emergencias Llamar A: <label style="color: red"> * </label></label>
                                        <input type="text" autocomplete="off" id="emergencias-llamar" maxlength="100" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Celular Emergencia <label style="color: red"> * </label></label>
                                        <input type="number" autocomplete="off" id="celular-emergencia" oninput="validarNumero(event)" class="form-control">
                                    </div>

                                    <hr>

                                    <div class="form-group" id="contenedor-enfermedadlist">
                                        <label>¿Padece Alguna Enfermedad Crónica o Existe Alguna Condición Fisica? <label style="color: red"> * </label></label>
                                        <select class="form-control" id="select-enfermedad">
                                            <option value="0">Ninguna</option>
                                            @foreach($listaEnfermedad as $dato)
                                                <option value="{{ $dato->id }}">{{ $dato->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="enfermedad-check" />
                                            <label style="color: red"> * </label> Marcar si NO se encuentra su Enfermedad en la lista
                                        </label>
                                    </div>

                                    <div id="contenedor-nuevoenfermedad" style="display: none;">
                                        <div class="form-group">
                                            <label>Enfermedad <label style="color: red"> * </label></label>
                                            <input type="text" autocomplete="off" id="enfermedad-nuevo" maxlength="100" placeholder="Nombre" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-blue">
                                            <div class="card-header">
                                                <div class="d-flex align-items-center ">
                                                    <h3 class="card-title mb-0">DATOS BENEFICIARIO</h3>
                                                </div>
                                            </div>
                                        </div>


                                        <section class="content">
                                            <div class="container-fluid">
                                                <div class="card card-primary">

                                                    <table class="table" id="matriz" data-toggle="table">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 4%">#</th>
                                                            <th style="width: 10%">NOMBRE</th>
                                                            <th style="width: 10%">PARENTESCO</th>
                                                            <th style="width: 10%">PORCENTAJE %</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <p style="padding: 5px; color: red">* Puede Agregar Máximo 5 Beneficiarios</p>
                                                            <p style="padding: 5px; color: red">* El Porcentaje no debe superar el 100% o ser menor a 100%</p>
                                                        </tr>


                                                        <tr>
                                                            <td>
                                                                <p>1</p>
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayNombre[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayParentesco[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="number" autocomplete="off"  name="arrayPorcentaje[]" oninput="validarNumero(event)" min="0" max="100" class="form-control">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <p>2</p>
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayNombre[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayParentesco[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="number" autocomplete="off" name="arrayPorcentaje[]" oninput="validarNumero(event)" min="0" max="100" class="form-control">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <p>3</p>
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayNombre[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayParentesco[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="number" autocomplete="off" name="arrayPorcentaje[]" oninput="validarNumero(event)" min="0" max="100" class="form-control">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <p>4</p>
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayNombre[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayParentesco[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="number" autocomplete="off" name="arrayPorcentaje[]" oninput="validarNumero(event)" min="0" max="100" class="form-control">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <p>5</p>
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayNombre[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="text" autocomplete="off" name="arrayParentesco[]" maxlength="100" class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="number" autocomplete="off" name="arrayPorcentaje[]" oninput="validarNumero(event)" min="0" max="100" class="form-control">
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </section>

                                        <div class="modal-footer justify-content-end">
                                            <button type="button" class="btn btn-success" onclick="modalGuardarDatos()">GUARDAR DATOS</button>
                                        </div>

                                    </div>
                                </div>


                            </div>
                        </form>
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
        $(document).ready(function(){

            $('#select-nombre').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            document.getElementById('empleado-check').addEventListener('change', function() {
                var inputContainer = document.getElementById('contenedor-nuevonombre');
                var listNombre = document.getElementById('contenedor-nombrelist');

                if (this.checked) {
                    inputContainer.style.display = 'block';
                    listNombre.style.display = 'none';
                } else {
                    inputContainer.style.display = 'none';
                    listNombre.style.display = 'block';
                }
            });

            // enfermedades

            document.getElementById('enfermedad-check').addEventListener('change', function() {
                var inputContainer = document.getElementById('contenedor-nuevoenfermedad');
                var listNombre = document.getElementById('contenedor-enfermedadlist');

                if (this.checked) {
                    inputContainer.style.display = 'block';
                    listNombre.style.display = 'none';
                } else {
                    inputContainer.style.display = 'none';
                    listNombre.style.display = 'block';
                }
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function validarNumero(event) {
            let input = event.target;
            // Remueve cualquier carácter que no sea un número, punto decimal o signo menos
            input.value = input.value.replace(/[^0-9.-]/g, '');
        }

        function modalGuardarDatos(){
            Swal.fire({
                title: 'Guardar',
                text: "Se enviara la información a RRHH",
                icon: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    registrar()
                }
            })
        }

        function alertaCampoRequerido(mensaje){
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


        function registrar(){

            // ***** BLOQUE 1 *****
            var selectNombreEmpleado = document.getElementById('select-nombre').value;
            let tEmpleadoCheck = document.getElementById('empleado-check').checked;
            let valEmpleadoCheck = tEmpleadoCheck ? 1 : 0;
            var nombreNuevo = document.getElementById('nombre-nuevo').value;
            var selectCargos = document.getElementById('select-cargos').value;
            var selectUnidad = document.getElementById('select-unidad').value;
            var dui = document.getElementById('dui').value;
            var nit = document.getElementById('nit').value;


            if(valEmpleadoCheck === 0){
                if(selectNombreEmpleado === '0'){
                    alertaCampoRequerido('Buscar nombre de Empleado')
                    return
                }
            }else{
                if(nombreNuevo === ''){
                    alertaCampoRequerido('Nombre de Empleado es Requerido')
                    return
                }
            }

            if(selectCargos === '0'){
                alertaCampoRequerido('Cargo es Requerido')
                return
            }

            if(selectUnidad === '0'){
                alertaCampoRequerido('Unidad es Requerido')
                return
            }

            if(dui === ''){
                alertaCampoRequerido('DUI es Requerido')
                return
            }




            // ***** BLOQUE 2 *****
            var fechaNacimiento = document.getElementById('fecha-nacimiento').value;
            var lugarNacimiento = document.getElementById('lugar-nacimiento').value;
            var selectAcademico = document.getElementById('select-academico').value;
            var profesion = document.getElementById('profesion').value;
            var direccionActual = document.getElementById('direccion-actual').value;
            var celular = document.getElementById('celular').value;
            var emergenciasLlamar = document.getElementById('emergencias-llamar').value;
            var celularEmergencia = document.getElementById('celular-emergencia').value;
            var selectEnfermedad = document.getElementById('select-enfermedad').value;

            let tEnfermedadCheck = document.getElementById('enfermedad-check').checked;
            let valEnfermedadCheck = tEnfermedadCheck ? 1 : 0;
            var enfermedadNuevo = document.getElementById('enfermedad-nuevo').value;

            if(fechaNacimiento === ''){
                alertaCampoRequerido('Fecha Nacimiento es Requerido')
                return
            }

            if(lugarNacimiento === ''){
                alertaCampoRequerido('Lugar de Nacimiento es Requerido')
                return
            }

            if(selectAcademico === '0'){
                alertaCampoRequerido('Nivel Académico es Requerido')
                return
            }

            if(profesion === ''){
                alertaCampoRequerido('Profesión es Requerido')
                return
            }

            if(direccionActual === ''){
                alertaCampoRequerido('Dirección Actual es Requerido')
                return
            }

            if(celular === ''){
                alertaCampoRequerido('Celular es Requerido')
                return
            }

            if(emergenciasLlamar === ''){
                alertaCampoRequerido('En Emergencias LLamar A, es Requerido')
                return
            }

            if(celularEmergencia === ''){
                alertaCampoRequerido('Celular Emergencias es Requerido')
                return
            }


            if(valEnfermedadCheck === 0){
               // no hacer nada
            }else{
                if(enfermedadNuevo === ''){
                    alertaCampoRequerido('Enfermedad es Requerido')
                    return
                }
            }



            // **** BLOQUE TABLA ****

            const contenedorArray = [];
            var porcentajeTotal = 0

            var arrayNombre = $("input[name='arrayNombre[]']").map(function(){return $(this).val();}).get();
            var arrayParentesco = $("input[name='arrayParentesco[]']").map(function(){return $(this).val();}).get();
            var arrayPorcentaje = $("input[name='arrayPorcentaje[]']").map(function(){return $(this).val();}).get();

            // recorrer cada fila
            for(var i = 0; i < arrayNombre.length; i++){

                let fila = i+1
                let infoNombre = arrayNombre[i];
                let infoParentesco = arrayParentesco[i];
                let infoPorcentaje = arrayPorcentaje[i];


                if(infoPorcentaje.length > 0){
                    porcentajeTotal += parseInt(infoPorcentaje);
                }


                if ((infoNombre.trim() && infoParentesco.trim() && infoPorcentaje.trim()) ||
                    (!infoNombre.trim() && !infoParentesco.trim() && !infoPorcentaje.trim())) {

                    if(infoNombre.length > 0 && infoParentesco.length > 0 && infoPorcentaje.length > 0){
                        // ESTOS NOMBRES SE UTILIZAN EN CONTROLADOR
                        contenedorArray.push({ infoNombre, infoParentesco, infoPorcentaje });
                    }
                } else {

                    let mensaje = "En la Fila: " + fila + " Faltan Campos por Completar"
                    alertaCampoRequerido(mensaje)
                    return
                }
            }

            if (contenedorArray.length <= 0) {
                alertaCampoRequerido('Datos Beneficiarios se Requiere Mínimo 1')
                return
            }

            if (porcentajeTotal < 100){
                alertaCampoRequerido('El Porcentaje para el Beneficiario no puede ser MENOR a 100%')
                return
            }

            if (porcentajeTotal > 100){
                alertaCampoRequerido('El Porcentaje para el Beneficiario no puede ser MAYOR a 100%')
                return
            }


            //** PASO TODAS LAS VALIDACIONES **

            openLoading();
            var formData = new FormData();
            formData.append('selectNombre', selectNombreEmpleado);
            formData.append('empleadoCheck', valEmpleadoCheck);
            formData.append('nombreNuevo', nombreNuevo);
            formData.append('selectCargos', selectCargos);
            formData.append('selectUnidad', selectUnidad);
            formData.append('dui', dui);
            formData.append('nit', nit);

            formData.append('fechaNacimiento', fechaNacimiento);
            formData.append('lugarNacimiento', lugarNacimiento);
            formData.append('selectAcademica', selectAcademico);
            formData.append('profesion', profesion);
            formData.append('direccionActual', direccionActual);
            formData.append('celular', celular);
            formData.append('emergenciasLlamar', emergenciasLlamar);
            formData.append('celularEmergencia', celularEmergencia);
            formData.append('selectEnfermedad', selectEnfermedad);
            formData.append('enfermedadCheck', valEnfermedadCheck);
            formData.append('enfermedadNuevo', enfermedadNuevo);
            formData.append('contenedorArray', JSON.stringify(contenedorArray));


            axios.post('/comprasalcaldia.com/admin/actualizacion/datos/guardar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Registrado');
                        camposRegistrados();
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


        function camposRegistrados(){
            Swal.fire({
                title: 'Documento Registrado',
                text: "Cualquier consulta puede hacerla en Recursos Humanos",
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar',
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload()
                }
            })
        }




    </script>


@endsection
