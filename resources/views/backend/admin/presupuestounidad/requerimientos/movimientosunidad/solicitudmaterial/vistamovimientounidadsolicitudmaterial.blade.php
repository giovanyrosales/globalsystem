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
        <div class="container-fluid">
            <div class="row mb-6">
                <div class="col-sm-5">
                    <h1>Solicitud de Material Para Presupuesto</h1>
                </div>

                <button type="button" style="margin-top: 15px;font-weight: bold; background-color: #17a2b8; color: white !important;"
                        onclick="agregarSolicitud()" class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-list-alt"></i>
                    Agregar Solicitud
                </button>

            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
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


    <div class="modal fade" id="modalNuevoSolicitud">
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

                                        <label>Material del Presupuesto</label>
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input type='text' id="materialnuevosolicitado" autocomplete="off" data-info='0' class='form-control' onkeyup='buscarMaterialSolicitud(this)' maxlength='300'  >
                                                    <div class='droplistado' style='position: absolute; z-index: 9; width: 85% !important;'></div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Costo Actual del Material</label>
                                            <input type="text" class="form-control" autocomplete="off" disabled id="costo-actual">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Unidades</label>
                                            <input type="number" class="form-control" autocomplete="off" id="cantidad-material-nuevo">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Periodo</label>
                                            <input type="number" class="form-control" placeholder="Mínimo es 1" autocomplete="off" id="periodo-material-nuevo">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Objeto Específico a Descontar</label>
                                            <select class="form-control" id="select-obj" onchange="buscarSaldoRestante()" disabled>
                                            </select>
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Restante <p style="color: red">(Se resta Saldo Retenido)</p></label>
                                            <input type="text" class="form-control" disabled id="saldo-restante">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="preguntarSolicitud()">Guardar</button>
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
            let id = {{ $idpresubunidad }}; // id PRESUP UNIDAD
            var ruta = "{{ URL::to('/admin/p/movicuentaunidad/solicitud/materialtabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

            // variable global para setear input al buscar nuevo material
            window.txtContenedorGlobal = this;
            window.seguroBuscador = true;

            $(document).click(function(){
                $(".droplistado").hide();
            });

            $(document).ready(function() {
                $('[data-toggle="popover"]').popover({
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });
    </script>

    <script>

        function recargar(){
            let id = {{ $idpresubunidad }}; // id PRESUP UNIDAD
            var ruta = "{{ URL::to('/admin/p/movicuentaunidad/solicitud/materialtabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function agregarSolicitud(){
            document.getElementById("formulario-nuevo-material").reset();
            document.getElementById("select-obj").options.length = 0;
            document.getElementById("select-obj").disabled = true;
            $('#saldo-restante').val('');

            $('#modalNuevoSolicitud').modal('show');
        }

        function buscarMaterialSolicitud(e){

            let idpresubuni = {{ $idpresubunidad }};

            // seguro para evitar errores de busqueda continua
            if(seguroBuscador){
                seguroBuscador = false;

                var row = $(e).closest('tr');
                txtContenedorGlobal = e;

                let texto = e.value;

                if(texto === ''){
                    // si se limpia el input, setear el atributo id
                    $(e).attr('data-info', 0);
                    document.getElementById("select-obj").options.length = 0;
                    document.getElementById("select-obj").disabled = true;
                    $('#saldo-restante').val('');
                    $('#costo-actual').val('');
                }

                axios.post(url+'/p/buscar/material/solicitud/unidad', {
                    'query' : texto,
                    'idpresuunidad': idpresubuni,
                })
                    .then((response) => {

                        seguroBuscador = true;
                        $(row).each(function (index, element) {
                            $(this).find(".droplistado").fadeIn();
                            $(this).find(".droplistado").html(response.data);
                        });

                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        // cuando se busca un material en requisición y se hace clic en material se modifica el valor
        function modificarValorSolicitud(edrop){

            let texto = $(edrop).text();
            $(txtContenedorGlobal).val(texto);
            $(txtContenedorGlobal).attr('data-info', edrop.id);

            // solicitud llenar select

            openLoading();

            let idpresubuni = {{ $idpresubunidad }};

            var formData = new FormData();
            formData.append('idmaterial', edrop.id);
            formData.append('idpresup', idpresubuni);

            axios.post(url+'/p/select/objespecifico/solicitud', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        document.getElementById("select-obj").options.length = 0;
                        document.getElementById("select-obj").disabled = false;

                        $('#select-obj').append('<option selected value="0">Seleccionar Objeto Específico</option>');

                        $.each(response.data.arrayobj, function( key, val ){
                            $('#select-obj').append('<option value="' +val.id +'">'+val.codigo + ' - ' + val.nombre+'</option>');
                        });

                        $('#costo-actual').val(response.data.costoactual);

                    }else{
                        toastr.error('información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('información no encontrada');
                });
        }

        // buscar saldo restante MENOS el saldo retenido
        function buscarSaldoRestante(){
            let id = document.getElementById('select-obj').value;

            // compara si es tipo texto o numero
            if(id == 0){
                $('#saldo-restante').val('');
                return;
            }

            openLoading();

            let idpresubuni = {{ $idpresubunidad }};

            var formData = new FormData();
            formData.append('idobj', id);
            formData.append('idpresup', idpresubuni);

            axios.post(url+'/p/select/obj/saldos/solicitud/material', formData, {

            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#saldo-restante').val(response.data.restante);
                    }else{
                        toastr.error('Error al buscar saldo restante');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al buscar saldo restante');
                });
        }

        function preguntarSolicitud(){

            Swal.fire({
                title: 'Guardar Solicitud',
                text: "Se debe esperar la Aprobación por Presupuesto",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarNuevaSolicitud();
                }
            })
        }

        // guardar nueva solicitud de materiales
        function guardarNuevaSolicitud(){

            var idobj = document.getElementById('select-obj').value;

            if(idobj == 0){
                toastr.error("Seleccionar objeto específico");
                return;
            }

            var idmaterial = document.querySelector('#materialnuevosolicitado');

            if(idmaterial.dataset.info == 0){
                toastr.error("Se debe seleccionar un Material del Buscador");
                return;
            }

            var cantidad = document.getElementById('cantidad-material-nuevo').value;
            var periodo = document.getElementById('periodo-material-nuevo').value;

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;
            var reglaNumeroEntero = /^[0-9]\d*$/;

            // ****

            if(cantidad === ''){
                toastr.error('Cantidad es requerido');
                return;
            }

            if(!cantidad.match(reglaNumeroDosDecimal)) {
                toastr.error('Cantidad debe ser número Decimal y No Negativos. Solo 2 decimales');
                return;
            }

            if(cantidad <= 0){
                toastr.error('Cantidad no permite números negativos y Ceros');
                return;
            }

            if(cantidad > 99000000){
                toastr.error('Cantidad máximo 99 millones');
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

            if(periodo <= 0){
                toastr.error('Periodo no permite Cero o negativos');
                return;
            }

            if(periodo > 999){
                toastr.error('Periodo máximo 999 veces de límite');
                return;
            }

            // ****

            openLoading();

            let idpresubuni = {{ $idpresubunidad }};

            var formData = new FormData();
            formData.append('idobj', idobj);
            formData.append('idpresup', idpresubuni);
            formData.append('idmaterial', idmaterial.dataset.info);
            formData.append('cantidad', cantidad);
            formData.append('periodo', periodo);

            axios.post(url+'/p/guardar/solicitud/material', formData, {

            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        let restante = response.data.restante;
                        let costo = response.data.costo;

                        Swal.fire({
                            title: 'Saldo Insuficiente',
                            html: "La Cuenta a Descontar no tiene suficiente Saldo " + "<br>"
                                + "Saldo Restante $"+ restante +"<br>"
                                + "Saldo de Material solicitado $"+ costo +"<br>"
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
                        $('#modalNuevoSolicitud').modal('hide');
                        toastr.success('Solicitud guardada');

                        recargar();
                    }
                    else if(response.data.success === 3){
                       toastr.error('Cuenta unidad no encontrada');
                    }
                    else{
                        toastr.error('Error al guardar solicitud');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar solicitud');
                });

        }



    </script>


@endsection
