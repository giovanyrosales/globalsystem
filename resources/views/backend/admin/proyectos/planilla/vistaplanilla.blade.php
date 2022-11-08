@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="col-sm-11">
            <h4>Proyecto: {{ $datos }}</h4>
        </div>
        <br>
        <button type="button" style="margin-left: 10px;font-weight: bold; background-color: #28a745; color: white !important;"
                onclick="modalAgregar()" class="button button-3d button-rounded button-pill button-small">
            <i class="fas fa-plus-square"></i>
            Agregar Planilla
        </button>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Planilla</h3>
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

    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Planilla</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Fecha De</label>
                                                <input type="date" class="form-control" id="fechade-nuevo">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Fecha Hasta</label>
                                                <input type="date" class="form-control" id="fechahasta-nuevo">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Salario Total</label>
                                                <input type="number" class="form-control" id="salariototal-nuevo" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Horas Extras</label>
                                                <input type="number" class="form-control" id="horasextras-nuevo" placeholder="Horas Extras">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Insaforp</label>
                                                <input type="number" class="form-control" id="insaforp-nuevo" placeholder="Insaforp">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <label>ISSS</label>
                                    <div class="row">
                                        <div class="col-md-3">

                                            <div class="form-group">
                                                <label>Laboral</label>
                                                <input type="number" class="form-control" id="issslaboral-nuevo" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Patronal</label>
                                                <input type="number" class="form-control" id="issspatronal-nuevo" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>



                                    <hr>
                                    <label>AFP Confía</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Laboral</label>
                                                <input type="number" class="form-control" id="confialaboral-nuevo" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Patronal</label>
                                                <input type="number" class="form-control" id="confiapatronal-nuevo" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <label>AFP Crecer</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Laboral</label>
                                                <input type="number" class="form-control" id="crecerlaboral-nuevo" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Patronal</label>
                                                <input type="number" class="form-control" id="crecerpatronal-nuevo" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;"
                            class="button button-rounded button-pill button-small" onclick="nuevo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Planilla</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">


                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Fecha De</label>
                                            <input type="hidden" id="id-editar">
                                            <input type="date" class="form-control" id="fechade-editar">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Fecha Hasta</label>
                                            <input type="date" class="form-control" id="fechahasta-editar">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Salario Total</label>
                                                <input type="number" class="form-control" id="salariototal-editar" placeholder="Salario Total">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Horas Extras</label>
                                                <input type="number" class="form-control" id="horasextras-editar" placeholder="Horas Extras">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Insaforp</label>
                                                <input type="number" class="form-control" id="insaforp-editar" placeholder="Insaforp">
                                            </div>
                                        </div>
                                    </div>


                                    <hr>
                                    <label>ISSS</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Laboral</label>
                                                <input type="number" class="form-control" id="issslaboral-editar" placeholder="ISSS Laboral">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Patronal</label>
                                                <input type="number" class="form-control" id="issspatronal-editar" placeholder="ISSS Patronal">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <label>AFP Confía</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Laboral</label>
                                                <input type="number" class="form-control" id="confialaboral-editar" placeholder="AFP Confía - Labporal">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Patronal</label>
                                                <input type="number" class="form-control" id="confiapatronal-editar" placeholder="AFP Confía - Patronal">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <label>AFP Crecer</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Laboral</label>
                                                <input type="number" class="form-control" id="crecerlaboral-editar" placeholder="AFP Crecer - Labporal">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Patronal</label>
                                                <input type="number" class="form-control" id="crecerpatronal-editar" placeholder="AFP Crecer - Patronal">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small"
                            onclick="editar()">Guardar</button>
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

    <script type="text/javascript">
        $(document).ready(function(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/planilla/tabla/lista') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/planilla/tabla/lista') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var fechade = document.getElementById('fechade-nuevo').value;
            var fechahasta = document.getElementById('fechahasta-nuevo').value;
            var salariototal = document.getElementById('salariototal-nuevo').value;
            var horasextras = document.getElementById('horasextras-nuevo').value;
            var insaforp = document.getElementById('insaforp-nuevo').value;

            var issslaboral = document.getElementById('issslaboral-nuevo').value;
            var issspatronal = document.getElementById('issspatronal-nuevo').value;

            var confialaboral = document.getElementById('confialaboral-nuevo').value;
            var confiapatronal = document.getElementById('confiapatronal-nuevo').value;

            var crecerlaboral = document.getElementById('crecerlaboral-nuevo').value;
            var crecerpatronal = document.getElementById('crecerpatronal-nuevo').value;

            if(fechade === ''){
                toastr.error('Fecha De es requerido');
                return;
            }

            if(fechahasta === ''){
                toastr.error('Fecha Hasta es requerido');
                return;
            }


            var f1 = new Date(fechade);
            var f2 = new Date(fechahasta);

            if(f1 > f2){
                toastr.error('Fecha De no puede ser Mayor');
                return;
            }


            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(salariototal.length > 0){
                if(!salariototal.match(reglaNumeroDosDecimal)) {
                    toastr.error('Salario Total debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(salariototal < 0){
                    toastr.error('Salario Total no debe ser negativo');
                    return;
                }

                if(salariototal > 99000000){ // máximo 99 millones
                    toastr.error('Salario Total debe tener máximo 99 millones');
                    return;
                }
            }else{
                salariototal = 0;
            }

            if(horasextras.length > 0){
                if(!horasextras.match(reglaNumeroDosDecimal)) {
                    toastr.error('Horas Extras debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(horasextras < 0){
                    toastr.error('Horas Extras Total no debe ser negativo');
                    return;
                }

                if(horasextras > 99000000){ // máximo 99 millones
                    toastr.error('Horas Extras Total debe tener máximo 99 millones');
                    return;
                }
            }else{
                horasextras = 0;
            }

            if(insaforp.length > 0){
                if(!insaforp.match(reglaNumeroDosDecimal)) {
                    toastr.error('Insaforp debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(insaforp < 0){
                    toastr.error('Insaforp Total no debe ser negativo');
                    return;
                }

                if(insaforp > 99000000){ // máximo 99 millones
                    toastr.error('Insaforp Total debe tener máximo 99 millones');
                    return;
                }
            }else{
                insaforp = 0;
            }

            // *****

            if(issslaboral.length > 0){
                if(!issslaboral.match(reglaNumeroDosDecimal)) {
                    toastr.error('ISSS Laboral debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(issslaboral < 0){
                    toastr.error('ISSS Laboral no debe ser negativo');
                    return;
                }

                if(issslaboral > 99000000){ // máximo 99 millones
                    toastr.error('ISSS Laboral debe tener máximo 99 millones');
                    return;
                }
            }else{
                issslaboral = 0;
            }

            if(issspatronal.length > 0){
                if(!issspatronal.match(reglaNumeroDosDecimal)) {
                    toastr.error('ISSS Patronal debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(issspatronal < 0){
                    toastr.error('ISSS Patronal no debe ser negativo');
                    return;
                }

                if(issspatronal > 99000000){ // máximo 99 millones
                    toastr.error('ISSS Patronal debe tener máximo 99 millones');
                    return;
                }
            }else{
                issspatronal = 0;
            }

            // -----

            if(confialaboral.length > 0){
                if(!confialaboral.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Confía Laboral debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(confialaboral < 0){
                    toastr.error('AFP Confía Laboral no debe ser negativo');
                    return;
                }

                if(confialaboral > 99000000){ // máximo 99 millones
                    toastr.error('AFP Confía Laboral debe tener máximo 99 millones');
                    return;
                }
            }else{
                confialaboral = 0;
            }

            if(confiapatronal.length > 0){
                if(!confiapatronal.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Confía Patronal debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(confiapatronal < 0){
                    toastr.error('AFP Confía Patronal no debe ser negativo');
                    return;
                }

                if(confiapatronal > 99000000){ // máximo 99 millones
                    toastr.error('AFP Confía Patronal debe tener máximo 99 millones');
                    return;
                }
            }else{
                confiapatronal = 0;
            }

            // -----

            if(crecerlaboral.length > 0){
                if(!confialaboral.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Crecer Laboral debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(crecerlaboral < 0){
                    toastr.error('AFP Crecer Laboral no debe ser negativo');
                    return;
                }

                if(crecerlaboral > 99000000){ // máximo 99 millones
                    toastr.error('AFP Crecer Laboral debe tener máximo 99 millones');
                    return;
                }
            }else{
                crecerlaboral = 0;
            }

            if(crecerpatronal.length > 0){
                if(!crecerpatronal.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Crecer Patronal debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(crecerpatronal < 0){
                    toastr.error('AFP Crecer Patronal no debe ser negativo');
                    return;
                }

                if(crecerpatronal > 99000000){ // máximo 99 millones
                    toastr.error('AFP Crecer Patronal debe tener máximo 99 millones');
                    return;
                }
            }else{
                crecerpatronal = 0;
            }

            var id = {{ $id }};

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fechade', fechade);
            formData.append('fechahasta', fechahasta);
            formData.append('salariototal', salariototal);
            formData.append('horasextra', horasextras);
            formData.append('insaforp', insaforp);
            formData.append('issslaboral', issslaboral);
            formData.append('issspatronal', issspatronal);
            formData.append('confialaboral', confialaboral);
            formData.append('confiapatronal', confiapatronal);
            formData.append('crecerlaboral', crecerlaboral);
            formData.append('crecerpatronal', crecerpatronal);

            axios.post(url+'/planilla/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                   if(response.data.success === 1) {

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/planilla/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#fechade-editar').val(response.data.planilla.fecha_de);
                        $('#fechahasta-editar').val(response.data.planilla.fecha_hasta);
                        $('#salariototal-editar').val(response.data.planilla.salario_total);
                        $('#horasextras-editar').val(response.data.planilla.horas_extra);
                        $('#insaforp-editar').val(response.data.planilla.insaforp);

                        $('#issslaboral-editar').val(response.data.planilla.isss_laboral);
                        $('#issspatronal-editar').val(response.data.planilla.isss_patronal);

                        $('#confialaboral-editar').val(response.data.planilla.afpconfia_laboral);
                        $('#confiapatronal-editar').val(response.data.planilla.afpconfia_patronal);

                        $('#crecerlaboral-editar').val(response.data.planilla.afpcrecer_laboral);
                        $('#crecerpatronal-editar').val(response.data.planilla.afpcrecer_patronal);
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function editar(){
            var id = document.getElementById('id-editar').value;
            var fechade = document.getElementById('fechade-editar').value;
            var fechahasta = document.getElementById('fechahasta-editar').value;
            var salariototal = document.getElementById('salariototal-editar').value;
            var horasextras = document.getElementById('horasextras-editar').value;
            var insaforp = document.getElementById('insaforp-editar').value;

            var issslaboral = document.getElementById('issslaboral-editar').value;
            var issspatronal = document.getElementById('issspatronal-editar').value;

            var confialaboral = document.getElementById('confialaboral-editar').value;
            var confiapatronal = document.getElementById('confiapatronal-editar').value;

            var crecerlaboral = document.getElementById('crecerlaboral-editar').value;
            var crecerpatronal = document.getElementById('crecerpatronal-editar').value;

            var idproyecto = {{ $id }};

            if(fechade === ''){
                toastr.error('Fecha De es requerido');
                return;
            }

            if(fechahasta === ''){
                toastr.error('Fecha Hasta es requerido');
                return;
            }

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(salariototal.length > 0){
                if(!salariototal.match(reglaNumeroDosDecimal)) {
                    toastr.error('Salario Total debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(salariototal < 0){
                    toastr.error('Salario Total no debe ser negativo');
                    return;
                }

                if(salariototal > 99000000){ // máximo 99 millones
                    toastr.error('Salario Total debe tener máximo 99 millones');
                    return;
                }
            }else{
                salariototal = 0;
            }

            if(horasextras.length > 0){
                if(!horasextras.match(reglaNumeroDosDecimal)) {
                    toastr.error('Horas Extras debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(horasextras < 0){
                    toastr.error('Horas Extras Total no debe ser negativo');
                    return;
                }

                if(horasextras > 99000000){ // máximo 99 millones
                    toastr.error('Horas Extras Total debe tener máximo 99 millones');
                    return;
                }
            }else{
                horasextras = 0;
            }

            if(insaforp.length > 0){
                if(!insaforp.match(reglaNumeroDosDecimal)) {
                    toastr.error('Insaforp debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(insaforp < 0){
                    toastr.error('Insaforp Total no debe ser negativo');
                    return;
                }

                if(insaforp > 99000000){ // máximo 99 millones
                    toastr.error('Insaforp Total debe tener máximo 99 millones');
                    return;
                }
            }else{
                insaforp = 0;
            }

            // *****

            if(issslaboral.length > 0){
                if(!issslaboral.match(reglaNumeroDosDecimal)) {
                    toastr.error('ISSS Laboral debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(issslaboral < 0){
                    toastr.error('ISSS Laboral no debe ser negativo');
                    return;
                }

                if(issslaboral > 99000000){ // máximo 99 millones
                    toastr.error('ISSS Laboral debe tener máximo 99 millones');
                    return;
                }
            }else{
                issslaboral = 0;
            }

            if(issspatronal.length > 0){
                if(!issspatronal.match(reglaNumeroDosDecimal)) {
                    toastr.error('ISSS Patronal debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(issspatronal < 0){
                    toastr.error('ISSS Patronal no debe ser negativo');
                    return;
                }

                if(issspatronal > 99000000){ // máximo 99 millones
                    toastr.error('ISSS Patronal debe tener máximo 99 millones');
                    return;
                }
            }else{
                issspatronal = 0;
            }

            // -----

            if(confialaboral.length > 0){
                if(!confialaboral.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Confía Laboral debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(confialaboral < 0){
                    toastr.error('AFP Confía Laboral no debe ser negativo');
                    return;
                }

                if(confialaboral > 99000000){ // máximo 99 millones
                    toastr.error('AFP Confía Laboral debe tener máximo 99 millones');
                    return;
                }
            }else{
                confialaboral = 0;
            }

            if(confiapatronal.length > 0){
                if(!confiapatronal.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Confía Patronal debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(confiapatronal < 0){
                    toastr.error('AFP Confía Patronal no debe ser negativo');
                    return;
                }

                if(confiapatronal > 99000000){ // máximo 99 millones
                    toastr.error('AFP Confía Patronal debe tener máximo 99 millones');
                    return;
                }
            }else{
                confiapatronal = 0;
            }

            // -----

            if(crecerlaboral.length > 0){
                if(!confialaboral.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Crecer Laboral debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(crecerlaboral < 0){
                    toastr.error('AFP Crecer Laboral no debe ser negativo');
                    return;
                }

                if(crecerlaboral > 99000000){ // máximo 99 millones
                    toastr.error('AFP Crecer Laboral debe tener máximo 99 millones');
                    return;
                }
            }else{
                crecerlaboral = 0;
            }

            if(crecerpatronal.length > 0){
                if(!crecerpatronal.match(reglaNumeroDosDecimal)) {
                    toastr.error('AFP Crecer Patronal debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(crecerpatronal < 0){
                    toastr.error('AFP Crecer Patronal no debe ser negativo');
                    return;
                }

                if(crecerpatronal > 99000000){ // máximo 99 millones
                    toastr.error('AFP Crecer Patronal debe tener máximo 99 millones');
                    return;
                }
            }else{
                crecerpatronal = 0;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id); // id editar planilla
            formData.append('idproyecto', idproyecto);
            formData.append('fechade', fechade);
            formData.append('fechahasta', fechahasta);
            formData.append('salariototal', salariototal);
            formData.append('horasextra', horasextras);
            formData.append('insaforp', insaforp);
            formData.append('issslaboral', issslaboral);
            formData.append('issspatronal', issspatronal);
            formData.append('confialaboral', confialaboral);
            formData.append('confiapatronal', confiapatronal);
            formData.append('crecerlaboral', crecerlaboral);
            formData.append('crecerpatronal', crecerpatronal);

            axios.post(url+'/planilla/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }

    </script>


@endsection
