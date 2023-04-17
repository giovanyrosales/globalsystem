<link href="{{ asset('css/cssacordeon.css') }}" type="text/css" rel="stylesheet" />

<!-- Main content -->
<section class="col-12" id="bloquecontenedor" style="display: none">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <form class="form-vertical">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Presupuesto Año: {{ $preanio }}</label>
                            </div>

                            <div class="form-group">

                                @if($estado == 1)
                                    <label>Estado: En Desarrollo</label>
                                @elseif($estado == 2)
                                    <label>Estado: En Revisión</label>
                                @else
                                    <label>Estado: <span class="badge bg-success">Presupuesto Aprobado</span> </label>
                                @endif

                            </div>
                        </div>

                        <div style="margin-left: 20px">
                            <label style="color: darkgreen; font-size: 20px; font-family: arial">Total: ${{$totalvalor}}</label>
                        </div>


                        <div class="col-12">
                            <!-- Custom Tabs -->
                            <div class="card">
                                <div class="card-header d-flex p-0">

                                    <button type="button" onclick="modalBuscarMaterial()" class="btn btn-default btn-sm" style="margin-bottom: 5px; margin-top: 5px; background: #E5E7E9">
                                        <i class="fas fa-search"></i>
                                        Buscar Material
                                    </button>

                                    <ul class="nav nav-pills ml-auto p-2">
                                        <li class="nav-item"><a class="nav-link active" href="#tab_1" onclick="mostrarBloque()" data-toggle="tab">Base Presupuesto</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#tab_2" onclick="ocultarBloque()" data-toggle="tab">Nuevos Materiales</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#tab_3" onclick="ocultarBloque()" data-toggle="tab">Proyectos</a></li>
                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">

                                            <!-- inicio -->

                                            <div>

                                                <form>
                                                    <div class="card-body">

                                                        <!-- foreach para rubro -->

                                                        @foreach($rubro as $item)

                                                            <div class="accordion-group" data-behavior="accordion">

                                                                <label class="accordion-header" style="background-color: #c5c6c8; color: black !important;">{{ $item->codigo }} - {{ $item->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $item->sumarubro }}</label>

                                                                <!-- foreach para cuenta -->
                                                                <div class="accordion-body">

                                                                    @foreach($item->cuenta as $cc)

                                                                        <div class="accordion-group" data-behavior="accordion" data-multiple="true">
                                                                            <p class="accordion-header" style="background-color: #b0c2f2; color: black !important;">{{ $cc->codigo }} - {{ $cc->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $cc->sumaobjetototal }}</p>

                                                                            <div class="accordion-body">
                                                                                <div class="accordion-group" data-behavior="accordion" data-multiple="true">

                                                                                    <!-- foreach para objetos -->
                                                                                    @foreach($cc->objeto as $obj)

                                                                                        <p class="accordion-header" style="background-color: #b0f2c2; color: black !important;">{{ $obj->codigo }} | {{ $obj->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $obj->sumaobjeto }}</p>
                                                                                        <div class="accordion-body">

                                                                                            <table data-toggle="table">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th style="width: 30%; text-align: center">Descripción</th>
                                                                                                    <th style="width: 20%; text-align: center">U/M</th>
                                                                                                    <th style="width: 15%; text-align: center">Costo</th>
                                                                                                    <th style="width: 10%; text-align: center">Unidades</th>
                                                                                                    <th style="width: 10%; text-align: center">Periodo</th>
                                                                                                    <th style="width: 10%; text-align: center">Total</th>

                                                                                                </tr>
                                                                                                </thead>
                                                                                                <tbody>

                                                                                                <!-- foreach para material -->

                                                                                                @foreach($obj->material as $mm)

                                                                                                    <tr>

                                                                                                        <td>
                                                                                                            <input type="hidden" name="idmaterial[]" value='{{ $mm->id }}'>
                                                                                                            <input value="{{ $mm->descripcion }}" disabled class="form-control"  type="text">
                                                                                                        </td>
                                                                                                        <td><input value="{{ $mm->unimedida }}" disabled class="form-control"  type="text"></td>
                                                                                                        <td><input value="{{ $mm->precio }}" disabled class="form-control" style="max-width: 150px" ></td>
                                                                                                        <td><input value="{{ $mm->cantidad }}" name="unidades[]" class="form-control" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                        <td><input value="{{ $mm->periodo }}" name="periodo[]" class="form-control" min="1" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                        <td><input value="{{ $mm->total }}" disabled name="total[]" class="form-control" type="text" style="max-width: 180px"></td>

                                                                                                    </tr>

                                                                                                    <!-- fin foreach material -->
                                                                                                @endforeach


                                                                                                <!-- AGREGAR EL PROYECTO APROBADO -->

                                                                                                @foreach($listadoProyectoAprobados as $lpa)

                                                                                                    @if($obj->codigo == $lpa->codigoobj)

                                                                                                        <tr style="background-color: #FAD7A0;">
                                                                                                            <td style="background-color: #FAD7A0;">
                                                                                                                <input value="{{ $lpa->descripcion }}" style="background-color: #FAD7A0; color: black; font-weight: bold" type="text" disabled class="form-control">
                                                                                                            </td>
                                                                                                            <td><input value="" disabled class="form-control" style="background-color: #FAD7A0; color: black; font-weight: bold" type="text"></td>
                                                                                                            <td style="background-color: #FAD7A0;"><input value="{{ $lpa->costoFormat }}" disabled style="background-color: #FAD7A0; color: black; font-weight: bold; max-width: 170px" type="text" class="form-control"></td>
                                                                                                            <td style="background-color: #FAD7A0;"><input value="" disabled style="background-color: #FAD7A0; color: black; font-weight: bold; max-width: 180px" type="text" class="form-control"></td>
                                                                                                            <td style="background-color: #FAD7A0;"><input value="" disabled style="background-color: #FAD7A0; color: black; font-weight: bold; max-width: 180px" type="text" class="form-control"></td>
                                                                                                            <td style="background-color: #FAD7A0;"><input value="{{ $lpa->costoFormat }}" style="background-color: #FAD7A0; color: black; font-weight: bold; max-width: 180px" type="text" disabled class="form-control"></td>
                                                                                                        </tr>

                                                                                                    @endif

                                                                                                @endforeach

                                                                                                </tbody>

                                                                                            </table>

                                                                                        </div>

                                                                                @endforeach
                                                                                <!-- finaliza foreach para objetos-->

                                                                                </div>
                                                                            </div>


                                                                        </div>

                                                                @endforeach
                                                                <!-- fin foreach para cuenta -->
                                                                </div>
                                                            </div>

                                                            @if($loop->last)
                                                                <script>
                                                                    setTimeout(function () {
                                                                        mostrarContenedor();
                                                                        closeLoading();
                                                                    }, 1000);
                                                                </script>
                                                            @endif

                                                    @endforeach
                                                    <!-- fin foreach para rubro -->


                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- LISTA DE NUEVOS MATERIALES - TABS 2 -->
                                        <div class="tab-pane" id="tab_2">

                                            <form>
                                                <div class="card-body">

                                                    <table class="table" id="matrizMateriales" style="border: 80px" data-toggle="table">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 30%; text-align: center">Descripción</th>
                                                            <th style="width: 20%; text-align: left">Unidad de Medida</th>
                                                            <th style="width: 15%; text-align: center">Costo ($)</th>
                                                            <th style="width: 15%; text-align: center">Cantidad</th>
                                                            <th style="width: 10%; text-align: center">Periodo</th>

                                                            <th style="width: 10%; text-align: center">Opciones</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="myTbodyMateriales">

                                                        @foreach($listado as $ll)

                                                            <tr>
                                                                <td style="width: 30%"><input name="descripcionfila[]" disabled value="{{ $ll->descripcion }}" maxlength="300" class="form-control" type="text"></td>
                                                                <td style="width: 20%;"><input name="unidadmedidafila[]" disabled value="{{ $ll->unidadmedida }}" data-infomedida="{{ $ll->id_unidadmedida }}" class="form-control" type="text"></td>
                                                                <td style="width: 15%;"><input name="costoextrafila[]" disabled value="{{ $ll->costo }}" class="form-control" min="0.1" type="number"></td>
                                                                <td style="width: 15%;"><input name="cantidadextrafila[]" disabled value="{{ $ll->cantidad }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number"></td>
                                                                <td style="width: 10%;"><input name="periodoextrafila[]" disabled value="{{ $ll->periodo }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number"></td>

                                                                <td>
                                                                    @if($estado == 1)
                                                                        <button type="button" class="btn btn-block btn-danger" id="btnBorrar" onclick="borrarFila(this)">Borrar</button>
                                                                    @endif
                                                                </td>
                                                            </tr>

                                                        @endforeach

                                                        </tbody>
                                                    </table>

                                                    @if($estado == 1)
                                                        <br>
                                                        <button type="button" class="btn btn-block btn-success" onclick="modalNuevaSolicitud()">Agregar Solicitud de Material</button>
                                                        <br>
                                                    @endif

                                                </div>

                                            </form>

                                        </div>




                                        <!-- LISTA DE NUEVOS PROYECTOS - TABS 3 -->
                                        <div class="tab-pane" id="tab_3">

                                            <form>
                                                <div class="card-body">
                                                    <h3>Proyectos Pendientes</h3>
                                                    <table class="table" id="matrizProyectos" style="border: 80px" data-toggle="table">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 30%; text-align: center">Descripción</th>
                                                            <th style="width: 15%; text-align: center">Costo</th>

                                                            <th style="width: 10%; text-align: center">Opciones</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        @foreach($listadoProyecto as $lp)

                                                            <tr >
                                                                <td style="width: 30%"><input name="proyectodescripcionfila[]" disabled value="{{ $lp->descripcion }}" maxlength="300" class="form-control" type="text"></td>
                                                                <td style="width: 15%;"><input name="proyectocostoextrafila[]" disabled value="{{ $lp->costo }}" class="form-control" type="number"></td>
                                                                <td>
                                                                    @if($estado == 1)
                                                                        <button type="button" class="btn btn-block btn-danger" onclick="borrarFilaProyecto(this)">Borrar</button>
                                                                    @endif
                                                                </td>
                                                            </tr>

                                                        @endforeach

                                                        </tbody>
                                                    </table>

                                                    @if($estado == 1)
                                                        <br>
                                                        <button type="button" class="btn btn-block btn-success" onclick="modalNuevaSolicitudProyecto()">Agregar Solicitud de Proyecto</button>
                                                        <br>
                                                    @endif

                                                </div>

                                            </form>


                                            <!-- MOSTRAR PROYECTOS APROBADOS - TABS 3 -->

                                            <br>
                                            <hr>
                                            <form>
                                                <div class="card-body">
                                                    <h3>Proyectos Aprobados</h3>
                                                    <table class="table" id="matrizProyectosAprobados" style="border: 80px" data-toggle="table">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 15%; text-align: center">Descripción</th>
                                                            <th style="width: 10%; text-align: center">Costo</th>
                                                            <th style="width: 12%; text-align: center">Obj. Específico</th>
                                                            <th style="width: 12%; text-align: center">Fuente Recursos</th>
                                                            <th style="width: 12%; text-align: center">Línea Trabajo</th>
                                                            <th style="width: 12%; text-align: center">Área Gestión</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        @foreach($listadoProyectoAprobados as $lp)

                                                            <tr>
                                                                <td><input disabled value="{{ $lp->descripcion }}" class="form-control"></td>
                                                                <td><input disabled value="{{ $lp->costoFormat }}" class="form-control"></td>
                                                                <td><input disabled value="{{ $lp->objeto }}" class="form-control"></td>
                                                                <td><input disabled value="{{ $lp->fuenterecurso }}" class="form-control"></td>
                                                                <td><input disabled value="{{ $lp->lineatrabajo }}" class="form-control"></td>
                                                                <td><input disabled value="{{ $lp->areagestion }}" class="form-control"></td>
                                                            </tr>

                                                        @endforeach

                                                        </tbody>
                                                    </table>


                                                </div>

                                            </form>




                                        </div>




                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($estado == 1)
                            <div class="card-footer">
                                <button type="button" onclick="verificar()" class="btn btn-success float-right">Guardar</button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="btn-group-vertical" id="bloque-codigo" style="width: 175px !important;">
                <label style="margin-left: 5px">Tipo según Color </label>
                <button type="button" class="btn btn-info" style="background: #c5c6c8; color: black !important; font-weight: bold">RUBRO</button>
                <button type="button" class="btn btn-info" style="background: #b0c2f2; color: black !important; font-weight: bold">CUENTA</button>
                <button type="button" class="btn btn-info" style="background: #b0f2c2; color: black !important; font-weight: bold">OBJETO ESPECÍFICO</button>
            </div>

        </div>

    </div>
</section>


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
                                        <label>Costo Estimado:</label>
                                        <input type="number" class="form-control" autocomplete="off" id="costo-nuevo" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label>Cantidad:</label>
                                        <input type="number" class="form-control" autocomplete="off" id="cantidad-nuevo" placeholder="0">
                                    </div>

                                    <div class="form-group">
                                        <label>Periodo (Mínimo 1):</label>
                                        <input type="number" class="form-control" autocomplete="off" id="periodo-nuevo">
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


<script src="{{ asset('js/jquery.simpleaccordion.js') }}"></script>

<script>

    $(document).ready(function() {
        $('[data-behavior=accordion]').simpleAccordion({cbOpen:accOpen, cbClose:accClose});

        $('#select-medida-nuevo').select2({
            theme: "bootstrap-5",
            "language": {
                "noResults": function(){
                    return "Búsqueda no encontrada";
                }
            },
        });
    });

    function accClose(e, $this) {
        $this.find('span').fadeIn(200);
    }

    function accOpen(e, $this) {
        $this.find('span').fadeOut(200)
    }

    function mostrarContenedor(){
        document.getElementById("bloquecontenedor").style.display = "block";
    }

    function multiplicar(e){

        var table = e.parentNode.parentNode; // fila de la tabla
        var costo = table.cells[2].children[0]; //
        var unidades = table.cells[3].children[0]; //
        var periodo = table.cells[4].children[0];
        var total = table.cells[5].children[0];

        var boolUnidades = false;
        var boolPeriodo = false;

        // validar que unidades y periodo existan para calcular total
        var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;
        var reglaNumeroEntero = /^[0-9]\d*$/;

        if(unidades.value.length > 0) {
            // validar

            if(!unidades.value.match(reglaNumeroDosDecimal)) {
                modalMensaje('Error', 'unidades debe ser número Decimal Positivo. Solo se permite 2 Decimales');
                return;
            }

            if(unidades.value <= 0){
                modalMensaje('Error', 'unidades no debe ser negativo o cero');
                return;
            }

            if(unidades > 1000000){
                modalMensaje('Error', 'unidades máximo 1 millón');
                return;
            }

            boolUnidades = true;
        }

        if(periodo.value.length > 0) {
            // validar

            if(!periodo.value.match(reglaNumeroEntero)) {
                modalMensaje('Error', 'periodo debe ser número entero');
                return;
            }

            if(periodo.value <= 0){
                modalMensaje('Error', 'periodo no debe ser negativo o cero');
                return;
            }

            if(periodo > 1000000){
                modalMensaje('Error', 'periodo máximo 1 millón');
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


    function editarPresupuesto(){

        var idmaterial = $("input[name='idmaterial[]']").map(function(){return $(this).val();}).get();
        var unidades = $("input[name='unidades[]']").map(function(){return $(this).val();}).get();
        var periodo = $("input[name='periodo[]']").map(function(){return $(this).val();}).get();

        var reglaNumeroEntero = /^[0-9]\d*$/;
        var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

        // verificar que todos las unidades y periodos ingresados sean validos

        for(var a = 0; a < unidades.length; a++){

            var datoUnidades = unidades[a];

            if(datoUnidades.length > 0){

                // revisar si es decimal

                if(!datoUnidades.match(reglaNumeroDosDecimal)) {
                    modalMensaje('Presupuesto Base','unidades Debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(datoUnidades <= 0){
                    modalMensaje('Presupuesto Base', 'unidades no debe ser negativos o cero');
                    return;
                }

                if(datoUnidades > 1000000){
                    modalMensaje('Presupuesto Base', 'unidades máximo 1 millón');
                    return;
                }

            }
        }

        for(var b = 0; b < periodo.length; b++){

            var datoPeriodo = periodo[b];

            if(datoPeriodo.length > 0){

                // revisar si es decimal

                if(!datoPeriodo.match(reglaNumeroEntero)) {
                    modalMensaje('Presupuesto Base', 'periodo ingresada no es valido');
                    return;
                }

                if(datoPeriodo <= 0){
                    modalMensaje('Presupuesto Base', 'periodo no debe ser negativos o cero');
                    return;
                }

                if(datoPeriodo > 1000000){
                    modalMensaje('Presupuesto Base', 'periodo máximo 1 millón');
                    return;
                }

            }
        }

        let formData = new FormData();

        // verificar ingreso de materiales extras

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

                if(!datoCostoExtra.match(reglaNumeroDosDecimal)) {
                    modalMensaje('Nuevos Materiales', 'Fila: #' + (d+1) + ', el Costo debe ser Número Decimal Positivo y 2 Decimales Máximo. Borrar fila y agregar de nuevo');
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

                if(!datoCantidadExtra.match(reglaNumeroDosDecimal)) {
                    modalMensaje('Nuevos Materiales', 'Fila: #' + (t+1) + ', la Cantidad debe ser Número Decimal Positivo y 2 Decimales Máximo. Borrar fila y agregar de nuevo');
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
            for(var mate = 0; mate < descripcion.length; mate++){
                formData.append('descripcionfila[]', descripcion[mate]);
                formData.append('costoextrafila[]', costoextra[mate]);
                formData.append('cantidadextrafila[]', cantidadextra[mate]);
                formData.append('periodoextrafila[]', periodoextra[mate]);
                formData.append('unidadmedida[]', unidadmedidafila[mate]);
            }
        }


        // llenar array para enviar
        for(var z = 0; z < unidades.length; z++){

            if(unidades[z].length > 0 && periodo[z].length > 0){

                formData.append('idmaterial[]', idmaterial[z]);
                formData.append('unidades[]', unidades[z]);
                formData.append('periodo[]', periodo[z]);
            }
        }


        // VERIFICAR LOS PROYECTOS QUE SE VAN A SOLICITAR
        var nRegistroProyecto = $('#matrizProyectos >tbody >tr').length;
        if (nRegistroProyecto > 0){


            var descripcionProyecto = $("input[name='proyectodescripcionfila[]']").map(function(){return $(this).val();}).get();
            var costoProyecto = $("input[name='proyectocostoextrafila[]']").map(function(){return $(this).val();}).get();

            for(var pp = 0; pp < descripcionProyecto.length; pp++){

                var datoDescripcionPro = descripcionProyecto[pp];

                if(datoDescripcionPro === ''){
                    modalMensaje('Nuevo Proyecto', 'Fila: #' + (pp+1) + ', falta su descripción. Borrar fila y agregar de nuevo');
                    return;
                }

                if(datoDescripcionPro.length > 300){
                    modalMensaje('Nuevo Proyecto', 'Fila: #' + (pp+1) + ', su descripción supera los 300 caracteres. Borrar fila y agregar de nuevo');
                    return;
                }
            }

            for(var pc = 0; pc < costoProyecto.length; pc++){

                var datoCostoExtraPro = costoProyecto[pc];

                if(datoCostoExtraPro === ''){
                    modalMensaje('Nuevos Proyecto', 'Fila: #' + (pc+1) + ', el Costo es requerido. Borrar fila y agregar de nuevo');
                    return;
                }

                if(!datoCostoExtraPro.match(reglaNumeroDosDecimal)) {
                    modalMensaje('Nuevos Proyecto', 'Fila: #' + (pc+1) + ', el Costo debe ser Número Decimal Positivo y 2 Decimales Máximo. Borrar fila y agregar de nuevo');
                    return;
                }

                if(datoCostoExtraPro <= 0){
                    modalMensaje('Nuevos Proyecto', 'Fila: #' + (pc+1) + ', el Costo no debe ser Negativo o Cero. Borrar fila y agregar de nuevo');
                    return;
                }

                if(datoCostoExtraPro > 9000000){
                    modalMensaje('Nuevos Proyecto', 'Fila: #' + (pc+1) + ', el Costo no debe superar 9 millones. Borrar fila y agregar de nuevo');
                    return;
                }
            }

            // AGREGAR SOLICITUD DE PROYECTOS
            for(var pro = 0; pro < descripcionProyecto.length; pro++){
                formData.append('descripcionfilaproyecto[]', descripcionProyecto[pro]);
                formData.append('costoextrafilaproyecto[]', costoProyecto[pro]);
            }
        }




        var idpresupuesto = {{ $idpresupuesto }};

        formData.append('idpresupuesto', idpresupuesto);

        axios.post(url+'/p/editar/presupuesto/editar', formData, {
        })
            .then((response) => {

                console.log(response);

                if(response.data.success === 1){
                    Swal.fire({
                        title: 'Información',
                        text: "El presupuesto esta en Revisión. No se puede editar",
                        icon: 'info',
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
                    Swal.fire({
                        title: 'Información',
                        text: "El presupuesto esta Aprobado. No se puede editar",
                        icon: 'info',
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

                else if(response.data.success === 3){
                    Swal.fire({
                        title: 'Presupuesto Actualizado',
                        text: "",
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
                    toastr.error('error al actualizar');
                }
            })
            .catch((error) => {
                toastr.error('error al registrar');
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


</script>
