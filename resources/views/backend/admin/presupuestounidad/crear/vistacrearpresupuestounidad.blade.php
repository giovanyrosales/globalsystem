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

    .modal-xl {
        max-width: 90% !important;
    }

</style>

<div class="content-wrapper" style="display: none" id="divcontenedor">

    <!-- VISTA PARA CREAR UN PRESUPUESTO DE UNIDAD NUEVO. SOLO SI HAY AÑO DISPONIBLE
        Y NO SE HA CREADO ANTERIORMENTE-->

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Crear Presupuesto</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label style="margin: 8px">Año</label>
                                    <div style="margin-left: 6px" class="col-sm-3">
                                        <select class="form-control" id="select-anio">
                                            @foreach($listado as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="tablaDatatable">

                                        </div>
                                    </div>
                                </div>
                            </section>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalBuscador">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group">
                                            <div class="input-group mb-3" style="width: 40%;">
                                                <input type="text" class="form-control" autocomplete="off" maxlength="100" id="nombre-material" placeholder="Nombre del Material a Buscar...">
                                                <span class="input-group-append">
                                                <button type="button" class="btn btn-info btn-flat" onclick="buscarMaterial()">BUSCAR</button>
                                              </span>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 20px">
                                            <table class="table" id="matriz-material"  data-toggle="table">
                                                <thead>
                                                <tr>
                                                    <th style="width: 20%">RUBRO</th>
                                                    <th style="width: 20%">CUENTA</th>
                                                    <th style="width: 20%">OBJETO ESPE.</th>
                                                    <th style="width: 40%">MATERIAL</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
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

    <div class="modal fade" id="modalNuevoMaterial">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitud de Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo-material">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group">
                                            <h5 style="font-weight: bold; text-align: center "><i class="fas fa-info"></i> Se deberá Notificar a Jefatura de Presupuesto que se Necesita el Material que esta Solicitando</h5>
                                        </div>

                                        <hr>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Nombre del Material</label>
                                            <input type="text" class="form-control" autocomplete="off" maxlength="300" id="material-nuevo" placeholder="Nombre">
                                        </div>

                                        <div class="form-group">
                                            <label>Costo:</label>
                                            <input type="text" class="form-control" autocomplete="off" id="costo-nuevo" placeholder="0.00">
                                        </div>

                                        <div class="form-group">
                                            <label>Cantidad:</label>
                                            <input type="text" class="form-control" autocomplete="off" id="cantidad-nuevo" placeholder="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Periodo:</label>
                                            <input type="text" class="form-control" autocomplete="off" id="periodo-nuevo" placeholder="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Unidad de Medida</label>
                                            <select class="form-control" id="select-medida-nuevo">
                                                @foreach($unidad as $sel)
                                                    <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificarNuevoMaterial()">Agregar</button>
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
            openLoading();

            var ruta = "{{ URL::to('/admin/p/contenedor/nuevo/presupuesto') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-medida-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function multiplicar(e){

            var table = e.parentNode.parentNode; // fila de la tabla
            var costo = table.cells[2].children[0]; //
            var unidades = table.cells[3].children[0]; //
            var periodo = table.cells[4].children[0];
            var total = table.cells[5].children[0];

            var boolUnidades = false;
            var boolPeriodo = false;

            // validar que unidades y periodo existan para calcular total
            var reglaNumeroEntero = /^[0-9]\d*$/;
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(unidades.value.length > 0) {
                // validar

                if(!unidades.value.match(reglaNumeroDecimal)) {
                    modalMensaje('Error', 'Unidades debe ser número decimal');
                    return;
                }

                if(unidades.value <= 0){
                    modalMensaje('Error', 'Unidades no debe ser negativo o cero');
                    return;
                }

                if(unidades.value > 1000000){
                    modalMensaje('Error', 'Unidades máximo 1 millón');
                    return;
                }

                boolUnidades = true;
            }


            if(periodo.value.length > 0) {
                // validar

                if(!periodo.value.match(reglaNumeroEntero)) {
                    modalMensaje('Error', 'Periodo debe ser número entero');
                    return;
                }

                if(periodo.value <= 0){
                    modalMensaje('Error', 'Periodo no debe ser negativo o cero');
                    return;
                }

                if(periodo.value > 1000000){
                    modalMensaje('Error', 'Periodo máximo 1 millón');
                    return;
                }

                boolPeriodo = true;
            }

            if(boolUnidades && boolPeriodo){

                // costo x unidades

                var val1 = costo.value;
                var val2 = unidades.value;
                var val3 = periodo.value;
                var valTotal = (val1 * val2) * val3;

                total.value = '$' + Number(valTotal).toFixed(2);
            }else{
                total.value = '';
            }
        }

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
        }

        function verificar(){
            var sel = document.getElementById("select-anio");
            var anio = sel.options[sel.selectedIndex].text;

            Swal.fire({
                title: 'Crear Presupuesto?',
                text: "Se creara Presupuesto para el Año " + anio + ", se podrá modificar en la Sección Editar",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Crear'
            }).then((result) => {
                if (result.isConfirmed) {
                    crearPresupuesto();
                }
            })
        }

        // verificar datos ingresados
        function crearPresupuesto(){

            var anio = document.getElementById('select-anio').value;

            if(anio === ''){
                toastr.error('año de presupuesto es requerido');
                return;
            }

            var idMaterial = $("input[name='idMaterial[]']").map(function(){return $(this).val();}).get();
            var unidades = $("input[name='unidades[]']").map(function(){return $(this).val();}).get();
            var periodo = $("input[name='periodo[]']").map(function(){return $(this).val();}).get();

            var reglaNumeroEntero = /^[0-9]\d*$/;
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            // verificar que todos las unidades y periodos ingresados sean validos
            for(var a = 0; a < unidades.length; a++){

                var datoUnidades = unidades[a];

                if(datoUnidades.length > 0){

                    // revisar si es decimal

                    if(!datoUnidades.match(reglaNumeroDecimal)) {
                        modalMensaje('Presupuesto Base','Unidades ingresada no es valido');
                        return;
                    }

                    if(datoUnidades <= 0){
                        modalMensaje('Presupuesto Base', 'Unidades no debe ser negativos o cero');
                        return;
                    }

                    if(datoUnidades > 1000000){
                        modalMensaje('Presupuesto Base', 'Unidades máximo 1 millón');
                        return;
                    }
                }
            }

            for(var b = 0; b < periodo.length; b++){

                var datoPeriodo = periodo[b];

                if(datoPeriodo.length > 0){

                    // revisar si es decimal

                    if(!datoPeriodo.match(reglaNumeroEntero)) {
                        modalMensaje('Presupuesto Base', 'Periodo ingresada no es valido');
                        return;
                    }

                    if(datoPeriodo <= 0){
                        modalMensaje('Presupuesto Base', 'Periodo no debe ser negativos o cero');
                        return;
                    }

                    if(datoPeriodo > 1000000){
                        modalMensaje('Presupuesto Base', 'Periodo máximo 1 millón');
                        return;
                    }
                }
            }

            let formData = new FormData();

            // VERIFICAR LOS MATERIALES QUE SE VAN A SOLICITAR

            var nRegistro = $('#matrizMateriales >tbody >tr').length;
            if (nRegistro > 0){

                var descripcion = $("input[name='descripcionfila[]']").map(function(){return $(this).val();}).get();
                var costoextra = $("input[name='costoextrafila[]']").map(function(){return $(this).val();}).get();
                var cantidadextra = $("input[name='cantidadextrafila[]']").map(function(){return $(this).val();}).get();
                var periodoextra = $("input[name='periodoextrafila[]']").map(function(){return $(this).val();}).get();
                var unidadmedidafila = $("input[name='unidadmedidafila[]']").map(function(){return $(this).attr("data-infomedida");}).get();

                for(var c = 0; c < descripcion.length; c++){

                    let detalle = unidadmedidafila[c];

                    // identifica si el 0 es tipo number o texto
                    if(detalle == 0){
                        modalMensaje('Nuevos Materiales', 'En la Fila #' + (c+1) + " No se encuentra la Unidad de Medida. Por favor agregar de nuevo el Material");
                        return;
                    }

                    var datoDescripcion = descripcion[c];

                    if(datoDescripcion === ''){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (c+1) + ', al Material falta su descripción. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(datoDescripcion.length > 300){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (c+1) + ', al Material su descripción supera los 300 caracteres. Borrar fila y agregar de nuevo');
                        return;
                    }
                }

                for(var d = 0; d < costoextra.length; d++){

                    var datoCostoExtra = costoextra[d];

                    if(datoCostoExtra === ''){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (d+1) + ', el Costo es requerido. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(!datoCostoExtra.match(reglaNumeroDecimal)) {
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (d+1) + ', el Costo debe ser Número Decimal. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(datoCostoExtra <= 0){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (d+1) + ', el Costo no debe ser Negativo o Cero. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(datoCostoExtra > 1000000){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (d+1) + ', el Costo no debe superar 1 millón. Borrar fila y agregar de nuevo');
                        return;
                    }
                }

                for(var t = 0; t < cantidadextra.length; t++){

                    var datoCantidadExtra = cantidadextra[t];

                    if(datoCantidadExtra === ''){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (t+1) + ', la Cantidad es Requerida. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(!datoCantidadExtra.match(reglaNumeroDecimal)) {
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (t+1) + ', la Cantidad debe ser Número Decimal. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(datoCantidadExtra <= 0){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (t+1) + ', la Cantidad no debe ser Número negativo o Cero. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(datoCantidadExtra > 1000000){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (t+1) + ', la Cantidad no debe superar 1 millón. Borrar fila y agregar de nuevo');
                        return;
                    }
                }

                for(var e = 0; e < periodoextra.length; e++){

                    var datoPeriodoExtra = periodoextra[e];

                    if(datoPeriodoExtra === ''){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (e+1) + ', el Periodo es Requerido. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(!datoPeriodoExtra.match(reglaNumeroEntero)) {
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (e+1) + ', el Periodo debe ser Número Entero. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(datoPeriodoExtra <= 0){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (e+1) + ', el Periodo no debe ser Número Negativo o Cero. Borrar fila y agregar de nuevo');
                        return;
                    }

                    if(datoPeriodoExtra > 1000000){
                        modalMensaje('Nuevos Materiales', 'Fila: #' + (e+1) + ', El Periodo debe tener máximo 1 millón. Borrar fila y agregar de nuevo');
                        return;
                    }
                }

                // AGREGAR SOLICITUD DE NUEVOS MATERIALES
                for(var p = 0; p < descripcion.length; p++){
                    formData.append('descripcionfila[]', descripcion[p]);
                    formData.append('costoextrafila[]', costoextra[p]);
                    formData.append('cantidadextrafila[]', cantidadextra[p]);
                    formData.append('periodoextrafila[]', periodoextra[p]);
                    formData.append('unidadmedida[]', unidadmedidafila[p]);
                }
            }


            // TODOS LOS MATERIALES DE PRESUPUESTO
            for(var z = 0; z < unidades.length; z++){

                if(unidades[z].length > 0 && periodo[z].length > 0){
                    formData.append('idmaterial[]', idMaterial[z]);
                    formData.append('unidades[]', unidades[z]);
                    formData.append('periodo[]', periodo[z]);
                }
            }
            formData.append('anio', anio);

            axios.post(url+'/p/crear/presupuesto/unidad', formData, {
            })
                .then((response) => {

                    if(response.data.success === 1){
                        // presupuesto ya habia sido creado
                        Swal.fire({
                            title: 'Presupuesto ya habia sido creado',
                            text: "Puede modificarlo en la sección Editar",
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                    else if(response.data.success === 2){
                        // presupuesto creado
                        Swal.fire({
                            title: 'Presupuesto creado',
                            text: "Puede modificarlo en la sección Editar",
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                    else{
                        // error al crear
                        toastr.error('error al crear presupuesto');
                    }

                })
                .catch((error) => {
                    toastr.error('error al crear presupuesto');
                    closeLoading();
                });
        }

        function modalMensaje(titulo, mensaje){
            Swal.fire({
                title: titulo,
                text: mensaje,
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            });
        }


        function mostrarBloque(){
            document.getElementById("bloque-codigo").style.display = "block";
        }

        function ocultarBloque(){
            document.getElementById("bloque-codigo").style.display = "none";
        }

        // buscar material
        function modalBuscarMaterial(){
            $('#modalBuscador').modal('show');
        }

        function buscarMaterial(){

            var nombre = document.getElementById("nombre-material").value;

            if(nombre === ''){
                toastr.error('Nombre Material es Requerido');
                return;
            }

            if(nombre.length < 3){
                toastr.error('Mínimo 3 Caracteres para Buscar');
                return;
            }

            openLoading();
            $("#matriz-material tbody tr").remove();

            axios.post(url+'/p/buscar/material/presupuesto', {
                'texto' : nombre
            })
                .then((response) => {

                    closeLoading();

               if(response.data.success === 1){

                   if(response.data.conteo){

                       let infodetalle = response.data.info;

                       for (var i = 0; i < infodetalle.length; i++) {

                           var markup = "<tr>" +

                               "<td>" +
                               "<input class='form-control' value='" + infodetalle[i].rubro + "' disabled type='text'>" +
                               "</td>" +

                               "<td>" +
                               "<input class='form-control' value='" + infodetalle[i].cuenta + "' disabled type='text'>" +
                               "</td>" +

                               "<td>" +
                               "<input class='form-control' value='" + infodetalle[i].objeto + "' disabled type='text'>" +
                               "</td>" +

                               "<td>" +
                               "<input class='form-control' style='background-color: #b0f2c2' value='" + infodetalle[i].descripcion + "' disabled type='text'>" +
                               "</td>" +

                               "</tr>";

                           $("#matriz-material tbody").append(markup);
                       }
                   }else{
                    toastr.info('Material No Encontrado');
                   }
               }else{
                   toastr.error('Error al buscar');
               }

            })
            .catch((error) => {
               toastr.error('Error al buscar');
            });
        }


        function modalNuevaSolicitud(){
            document.getElementById("formulario-nuevo-material").reset();
            $('#select-medida-nuevo').prop('selectedIndex', 0).change();
            $('#modalNuevoMaterial').modal('show');
        }

        function verificarNuevoMaterial(){

            var material = document.getElementById('material-nuevo').value;
            var costo = document.getElementById('costo-nuevo').value;
            var cantidad = document.getElementById('cantidad-nuevo').value;
            var periodo = document.getElementById('periodo-nuevo').value;
            var medida = document.getElementById('select-medida-nuevo').value;

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;
            var reglaNumeroEntero = /^[0-9]\d*$/;

            // ****

            if(material === ''){
                toastr.error('Material es requerido');
                return;
            }

            if(material.length > 300){
                toastr.error('Material máximo 300 caracteres');
                return;
            }

            // ****

            if(costo === ''){
                toastr.error('Costo es requerido');
                return;
            }

            if(!costo.match(reglaNumeroDecimal)) {
                toastr.error('Costo debe ser número Decimal y No Negativos');
                return;
            }

            if(costo < 0){
                toastr.error('Costo no permite números negativos');
                return;
            }

            if(costo.length > 10){
                toastr.error('Costo máximo 10 dígitos de límite');
                return;
            }

            // ****

            if(cantidad === ''){
                toastr.error('Cantidad es requerido');
                return;
            }

            if(!cantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser número Entero y No Negativos');
                return;
            }

            if(cantidad <= 0){
                toastr.error('Cantidad no permite números negativos y Ceros');
                return;
            }

            if(cantidad.length > 10){
                toastr.error('Cantidad máximo 10 dígitos de límite');
                return;
            }

            // ****

            if(periodo === ''){
                toastr.error('Periodo es requerido');
                return;
            }

            if(!periodo.match(reglaNumeroEntero)) {
                toastr.error('Periodo debe ser número Entero y No Negativos');
                return;
            }

            if(periodo < 0){
                toastr.error('Periodo no permite números negativos');
                return;
            }

            if(periodo.length > 3){
                toastr.error('Periodo máximo 3 dígitos de límite');
                return;
            }

            // ****

            if(medida === ''){
                toastr.error('Unidad Medida es requerido');
                return;
            }

            var texto = $("#select-medida-nuevo option:selected").text();

            var markup = "<tr>"+

                "<td>"+
                "<input name='descripcionfila[]' maxlength='300' value='"+ material +"' disabled class='form-control' type='text'>"+
                "</td>"+

                "<td>"+
                "<input name='unidadmedidafila[]' value='"+texto+"' class='form-control' disabled data-infomedida='"+medida+"' type='text'/>"+
                "</td>"+

                "<td>"+
                "<input name='costoextrafila[]' value='"+costo+"' disabled class='form-control'  type='text'/>"+
                "</td>"+

                "<td>"+
                "<input name='cantidadextrafila[]' value='"+cantidad+"' disabled class='form-control' />"+
                "</td>"+

                "<td>"+
                "<input name='periodoextrafila[]' value='"+periodo+"' disabled class='form-control'/>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matrizMateriales tbody").append(markup);

            $('#modalNuevoMaterial').modal('hide');
        }



    </script>


@endsection
