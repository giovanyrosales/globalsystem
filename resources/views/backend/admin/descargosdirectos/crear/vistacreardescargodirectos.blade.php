@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
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
                <h2>Formulario de Descargos Directos</h2>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Crear</li>
                    <li class="breadcrumb-item active">Descargos Directos</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Tipo de Descargo Directo</h3>
                </div>

                    <div class="card-body col-md-7">

                        <div class="form-group">
                            <label>Tipo de Descargo:</label>
                            <select class="form-control" id="select-tipodescargo" onchange="tipoDescargo(this)">
                                <option value="" disabled selected>Seleccionar Opción</option>
                                <option value="1">Proveedor</option>
                                <option value="2">Proyecto</option>
                                <option value="3">Contribución</option>
                            </select>
                        </div>
                    </div>

                <!------ BLOQUE DE PROYECTOS ------>

                <div class="card-body col-md-7" id="bloque-proyectos" style="display: none">

                    <div class="form-group">
                        <label>Proyectos:</label>
                        <select class="form-control" disabled id="select-proyectos" onchange="buscarObjProyecto(this)">
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Objeto Específico de Proyecto:</label>
                        <select class="form-control" disabled id="select-objproyectos" onchange="restanteCuentaPro(this)">
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Monto Proyecto (Saldo Restante - Saldo Retenido):</label>
                        <input type="text" id="monto-restantepro"  disabled class="form-control" autocomplete="off">
                    </div>

                </div>

                <!------ BLOQUE AÑO DE UNIDAD ------>
                <div class="card-body col-md-7" id="bloque-unidad" style="display: none">

                    <div class="form-group">
                        <label>Año de Presupuesto:</label>
                        <select class="form-control" id="select-anio" onchange="buscarDepartamento(this)">
                            <option value="0" disabled selected>Seleccionar Año Presupuesto</option>
                            @foreach($anios as $sel)
                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Objeto Específico de Unidad:</label>
                        <select class="form-control" id="select-objunidad" onchange="restanteCuentaUnidad(this)">
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Monto Unidad (Saldo Restante - Saldo Retenido):</label>
                        <input type="text" id="monto-restanteunidad"  disabled class="form-control" autocomplete="off">
                    </div>


                </div>

                <!------ BLOQUE DE PROVEEDOR ------>
                <div class="card-body col-md-7" id="bloque-proveedor" style="display: none">

                    <div class="form-group">
                        <label>Proveedor:</label>
                        <select class="form-control" id="select-proveedor" disabled>
                            @foreach($proveedores as $sel)
                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!------ BLOQUE DE BENEFICIARIO ------>
                <div class="card-body col-md-7" id="bloque-beneficiario" style="display: none">

                    <div class="form-group">
                        <label>Beneficiario</label>
                        <input type="text" id="txt-beneficiario" maxlength="300" disabled class="form-control" autocomplete="off">
                    </div>

                </div>


                <!------ BLOQUE DE APARTE ------>

                <div class="card-body col-md-7" id="bloque-aparte" style="display: none">

                    <div class="form-group">
                        <label>Número de Acuerdo:</label>
                        <input type="text" maxlength="300" id="numero-acuerdo" class="form-control" placeholder="" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>Número de Orden (Opcional):</label>
                        <input type="text" id="numero-orden" class="form-control" placeholder="" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>Linea de Trabajo:</label>
                        <select class="form-control" id="select-linea">
                            @foreach($arrayLineaTrabajo as $sel)
                                <option value="{{ $sel->id }}">{{ $sel->codigo }} - {{ $sel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fuente de Financiamiento:</label>
                        <select class="form-control" id="select-fuente-financiamiento" >
                            @foreach($arrayFuenteFinanciamiento as $sel)
                                <option value="{{ $sel->id }}">{{ $sel->codigo }} - {{ $sel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Concepto (opcional):</label>
                        <textarea id="txt-concepto" maxlength="10000" rows="4" cols="50" class="form-control"></textarea>
                    </div>


                    <div class="form-group">
                        <label>Monto a Descontar ($):</label>
                        <input type="number" id="monto-descontar" class="form-control" placeholder="" autocomplete="off">
                    </div>

                </div>

                <hr>

                <div class="card-footer" id="bloque-boton" style="display: none">
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important; float: right" class="button button-3d button-rounded button-pill button-small" onclick="verificar();">Guardar</button>
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
            document.getElementById("divcontenedor").style.display = "block";


            $('#select-proyectos').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

        });
    </script>

    <script>


        function tipoDescargo(e){

            // 1 proveedor
            // 2 proyecto
            // 3 contribución
            let idtipo = $(e).val();

            // limpiar select unidad
            document.getElementById("select-objunidad").options.length = 0;
            $("#monto-restanteunidad").val('');

            if(idtipo == 1){

                document.getElementById("bloque-proveedor").style.display = "block";
                document.getElementById("bloque-aparte").style.display = "block";
                document.getElementById("bloque-boton").style.display = "block";
                document.getElementById("bloque-unidad").style.display = "block";

                document.getElementById("bloque-proyectos").style.display = "none";
                document.getElementById("bloque-beneficiario").style.display = "none";


                // limpiar select unidad
                document.getElementById("select-objunidad").options.length = 0;
                $("#monto-restanteunidad").val('');
                $('#select-anio').prop('selectedIndex', 0).change();
            }
            else if(idtipo == 2){

                document.getElementById("bloque-proyectos").style.display = "block";
                document.getElementById("bloque-aparte").style.display = "block";
                document.getElementById("bloque-boton").style.display = "block";

                document.getElementById("bloque-proveedor").style.display = "none";
                document.getElementById("bloque-beneficiario").style.display = "none";
                document.getElementById("bloque-unidad").style.display = "none";
            }
            else if(idtipo == 3){
                document.getElementById("bloque-aparte").style.display = "block";
                document.getElementById("bloque-boton").style.display = "block";
                document.getElementById("bloque-beneficiario").style.display = "block";
                document.getElementById("bloque-unidad").style.display = "block";

                document.getElementById("bloque-proveedor").style.display = "none";
                document.getElementById("bloque-proyectos").style.display = "none";

                // limpiar select unidad
                document.getElementById("select-objunidad").options.length = 0;
                $("#monto-restanteunidad").val('');
                $('#select-anio').prop('selectedIndex', 0).change();
            }else{
                toastr.error('Tipo descargo no encontrado');
            }

            openLoading();

            // PROYECTOS
            document.getElementById("select-proyectos").options.length = 0;
            document.getElementById("select-proyectos").disabled = true;

            document.getElementById("select-objproyectos").options.length = 0;
            document.getElementById("select-objproyectos").disabled = true;

            $("#monto-restantepro").val('');


            // PROVEEDOR
            document.getElementById("select-proveedor").disabled = true;

            // BENEFICIARIO
            $("#txt-beneficiario").val('');
            document.getElementById("txt-beneficiario").disabled = true;


            axios.post(url+'/verificar/tipodescargo/directo/informacion',{
                'idtipo': idtipo
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("select-proveedor").disabled = false;

                    }
                    else if(response.data.success === 2){

                        document.getElementById("select-proyectos").options.length = 0;
                        document.getElementById("select-proyectos").disabled = false;
                        // obtener lista de proyectos
                        // NO ESTÁN FINALIZADOS
                        // NO PAUSADOS
                        // NO PRIORIZADO
                        // SOLO LOS QUE ESTÁN INICIADOS

                        $('#select-proyectos').append('<option disabled selected value="">Seleccionar Proyecto</option>');

                        $.each(response.data.proyectos, function( key, val ){
                            $('#select-proyectos').append('<option value="' +val.id +'">'+ val.codigo + ' - ' + val.nombre +'</option>');
                        });

                    }
                    else if(response.data.success === 3){
                        // BENEFICIARIO
                        document.getElementById("txt-beneficiario").disabled = false;
                    }
                    else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function buscarObjProyecto(e){

            let idproyecto = $(e).val();

            if(idproyecto == 0){
                return;
            }

            if(idproyecto == ''){
                return;
            }

            openLoading();

            axios.post(url+'/obj/proyecto/descargodirecto/informacion',{
                'idproyecto': idproyecto
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("select-objproyectos").options.length = 0;
                        document.getElementById("select-objproyectos").disabled = false;
                        // obtener lista de proyectos
                        // NO ESTÁN FINALIZADOS
                        // NO PAUSADOS
                        // NO PRIORIZADO
                        // SOLO LOS QUE ESTÁN INICIADOS

                        $('#select-objproyectos').append('<option disabled selected value="">Seleccionar Objeto Específico</option>');

                        $.each(response.data.objetos, function( key, val ){
                            $('#select-objproyectos').append('<option value="' +val.id +'">'+ val.objnombre +'</option>');
                        });

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function restanteCuentaPro(e){

            let idcuenta = $(e).val(); // id cuenta proy

            if(idcuenta == 0){
                return;
            }

            if(idcuenta == ''){
                return;
            }

            openLoading();

            axios.post(url+'/obj/cuentaproy/saldo/descargodirecto/info',{
                'idcuenta': idcuenta
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let restante = response.data.montorestante;

                        $("#monto-restantepro").val(restante);

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function restanteCuentaUnidad(e){

            let idunidad = $(e).val(); // id cuenta unidad

            if(idunidad == 0){
                return;
            }

            if(idunidad == ''){
                return;
            }

            openLoading();

            axios.post(url+'/obj/cuentaunidad/saldo/descargodirecto/info',{
                'idunidad': idunidad
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let restante = response.data.montorestante;

                        $("#monto-restanteunidad").val(restante);

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function verificar(){
            Swal.fire({
                title: 'Guardar Proyecto?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    crearRegistro();
                }
            })
        }

        function crearRegistro(){

            var tipodescargo = document.getElementById('select-tipodescargo').value;

            if(tipodescargo === ''){
                toastr.error('Tipo descargo es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;
            var reglaNumeroEntero = /^[0-9]\d*$/;


            // VALIDACIONES GENERALES

            var numacuerdo = document.getElementById('numero-acuerdo').value; // tipo texto 300
            var numorden = document.getElementById('numero-orden').value;
            var sellinea = document.getElementById('select-linea').value;
            var selfuentef = document.getElementById('select-fuente-financiamiento').value;
            var concepto = document.getElementById('txt-concepto').value; // tipo texto 10,000
            var montodescontar = document.getElementById('monto-descontar').value;

            if(numacuerdo === ''){
                toastr.error('Número de Acuerdo es requerido');
                return;
            }

            if(numacuerdo.length > 300){
                toastr.error('Número de Acuerdo máximo 300 caracteres');
                return;
            }


            if(concepto.length > 10000){
                toastr.error('Concepto máximo 10,000 caracteres');
                return;
            }

            //*************

            if(montodescontar === ''){
                toastr.error('Monto a descontar es requerido');
                return;
            }

            if(!montodescontar.match(reglaNumeroDecimal)) {
                toastr.error('Monto a descontar debe ser número Decimal y no Negativo. Solo 2 decimales');
                return;
            }

            if(montodescontar <= 0){
                toastr.error('Monto a descontar no debe ser negativo o cero');
                return;
            }

            if(montodescontar > 9000000){
                toastr.error('Cantidad máximo 9 millones');
                return;
            }

            if(numorden.length > 0){

                if(!numorden.match(reglaNumeroEntero)) {
                    toastr.error('Número de orden debe ser número Entero y no Negativo');
                    return;
                }

                if(numorden <= 0){
                    toastr.error('Número de orden no debe ser negativo o cero');
                    return;
                }

                if(numorden > 9000000){
                    toastr.error('Número de orden máximo 9 millones');
                    return;
                }
            }

            // 1 proveedor
            // 2 proyecto
            // 3 contribución

            if(tipodescargo == 1){

                var objunidad = document.getElementById('select-objunidad').value;

                if(objunidad === ''){
                    toastr.error('Objeto Específico de Unidad es requerido');
                    return;
                }


                var idproveedor = document.getElementById('select-proveedor').value;

                if(idproveedor === ''){
                    toastr.error('Proveedor es requerido');
                    return;
                }

                //************************************************

                let formData = new FormData();
                formData.append('idcuentaunidad', objunidad);
                formData.append('numacuerdo', numacuerdo);
                formData.append('numorden', numorden);

                formData.append('sellinea', sellinea);
                formData.append('selfuentef', selfuentef);

                formData.append('concepto', concepto);
                formData.append('montodescontar', montodescontar);
                formData.append('idproveedor', idproveedor);

                axios.post(url+'/guardar/descargodirecto/tipo/proveedor', formData, {

                })
                    .then((response) => {
                        closeLoading();

                        if(response.data.success === 1){

                            let restante = response.data.restante;
                            let solicitado = response.data.solicitado;

                            Swal.fire({
                                title: 'Saldo Insuficiente',
                                html: "La Cuenta a Descontar no tiene suficiente Saldo " + "<br>"
                                    + "Saldo Restante (Se resta el Retenido) $"+ restante +"<br>"
                                    + "Monto Solicitado $"+ solicitado +"<br>"
                                ,
                                icon: 'info',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {

                                }
                            })

                        }
                        else if(response.data.success === 2){


                            // redireccionar a la tabla de registros

                            Swal.fire({
                                title: 'Descargo Registrado',
                                text: "Se redireccionara a Listado de Descargos Directos",
                                icon: 'info',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href="{{ url('/') }}";
                                }
                            })
                        }
                        else{
                            toastr.error('Información no encontrada');
                        }
                    })
                    .catch((error) => {
                        closeLoading();
                        toastr.error('Información no encontrada');
                    });

            }
            else if(tipodescargo == 2){

                var objpro = document.getElementById('select-objproyectos').value;

                if(objpro === ''){
                    toastr.error('Objeto Específico de Proyecto es requerido');
                    return;
                }

                //************************************************

                let formData = new FormData();
                formData.append('idcuentaproy', objpro);
                formData.append('numacuerdo', numacuerdo);
                formData.append('numorden', numorden);

                formData.append('sellinea', sellinea);
                formData.append('selfuentef', selfuentef);

                formData.append('concepto', concepto);
                formData.append('montodescontar', montodescontar);

                axios.post(url+'/guardar/descargodirecto/tipo/proyecto', formData, {

                })
                    .then((response) => {
                        closeLoading();

                        if(response.data.success === 1){

                            let restante = response.data.restante;
                            let solicitado = response.data.solicitado;

                            Swal.fire({
                                title: 'Saldo Insuficiente',
                                html: "La Cuenta a Descontar no tiene suficiente Saldo " + "<br>"
                                    + "Saldo Restante (Se resta el Retenido) $"+ restante +"<br>"
                                    + "Monto Solicitado $"+ solicitado +"<br>"
                                ,
                                icon: 'info',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {

                                }
                            })

                        }
                        else if(response.data.success === 2){


                            // redireccionar a la tabla de registros

                            Swal.fire({
                                title: 'Descargo Registrado',
                                text: "Se redireccionara a Listado de Descargos Directos",
                                icon: 'info',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href="{{ url('/') }}";
                                }
                            })
                        }
                        else{
                            toastr.error('Información no encontrada');
                        }
                    })
                    .catch((error) => {
                        closeLoading();
                        toastr.error('Información no encontrada');
                    });

            }
            else if(tipodescargo == 3){

                var objunidad = document.getElementById('select-objunidad').value;
                var beneficiario = document.getElementById('txt-beneficiario').value;

                if(objunidad === ''){
                    toastr.error('Objeto Específico de Unidad es requerido');
                    return;
                }

                if(beneficiario === ''){
                    toastr.error('Beneficiario es requerido');
                    return;
                }

                if(beneficiario.length > 300){
                    toastr.error('Beneficiario máximo 300 caracteres');
                    return;
                }

                //************************************************

                let formData = new FormData();
                formData.append('idcuentaunidad', objunidad);
                formData.append('numacuerdo', numacuerdo);
                formData.append('numorden', numorden);

                formData.append('sellinea', sellinea);
                formData.append('selfuentef', selfuentef);

                formData.append('beneficiario', beneficiario);

                formData.append('concepto', concepto);
                formData.append('montodescontar', montodescontar);

                axios.post(url+'/guardar/descargodirecto/tipo/contribucion', formData, {

                })
                    .then((response) => {
                        closeLoading();

                        if(response.data.success === 1){

                            let restante = response.data.restante;
                            let solicitado = response.data.solicitado;

                            Swal.fire({
                                title: 'Saldo Insuficiente',
                                html: "La Cuenta a Descontar no tiene suficiente Saldo " + "<br>"
                                    + "Saldo Restante (Se resta el Retenido) $"+ restante +"<br>"
                                    + "Monto Solicitado $"+ solicitado +"<br>"
                                ,
                                icon: 'info',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {

                                }
                            })

                        }
                        else if(response.data.success === 2){

                            // redireccionar a la tabla de registros

                            Swal.fire({
                                title: 'Descargo Registrado',
                                text: "Se redireccionara a Listado de Descargos Directos",
                                icon: 'info',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href="{{ url('/') }}";
                                }
                            })
                        }
                        else{
                            toastr.error('Información no encontrada');
                        }
                    })
                    .catch((error) => {
                        closeLoading();
                        toastr.error('Información no encontrada');
                    });

            }
            else{
                toastr.error('Tipo descargo es requerido');
            }
        }


        function buscarDepartamento(e){

            let idanio = $(e).val();

            if(idanio == 0){
                return;
            }

            if(idanio == ''){
                return;
            }

            if(idanio == null){
                return;
            }

            var idtipo = document.getElementById('select-tipodescargo').value;

            openLoading();

            let formData = new FormData();
            formData.append('idanio', idanio);
            formData.append('idtipo', idtipo);

            axios.post(url+'/unidades/descargodirecto/anio/presupuesto', formData, {

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("select-objunidad").options.length = 0;
                        $('#select-objunidad').append('<option value="0" selected>Seleccionar Objeto Específico</option>');

                        $.each(response.data.listado, function( key, val ){
                            $('#select-objunidad').append('<option value="' +val.id +'">'+ val.codigo +'</option>');
                        });
                    }
                    else if(response.data.success === 2){
                        // no se encuentra ninguna cuenta unidad
                        document.getElementById("select-objunidad").options.length = 0;
                        toastr.info('No hay Cuenta Unidad');
                    }
                    else if(response.data.success === 3){
                        // no se encuentra ninguna cuenta unidad
                        document.getElementById("select-objunidad").options.length = 0;
                        toastr.info('No hay Cuenta Unidad');
                    }
                    else if(response.data.success === 4){

                        document.getElementById("select-objunidad").options.length = 0;

                        $.each(response.data.listado, function( key, val ){
                            $('#select-objunidad').append('<option value="' +val.id +'">'+ val.codigo +'</option>');
                        });
                    }
                    else if(response.data.success === 5){
                        // no se encuentra ninguna cuenta unidad
                        document.getElementById("select-objunidad").options.length = 0;
                        toastr.info('No hay Cuenta Unidad');
                    }
                    else if(response.data.success === 6){
                        // no se encuentra ninguna cuenta unidad
                        document.getElementById("select-objunidad").options.length = 0;
                        toastr.info('No hay Cuenta Unidad');
                    }

                    else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });

        }



    </script>


@endsection
