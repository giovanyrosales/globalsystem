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
                    <h1>Histórico</h1>
                </div>

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

    <!-- CARGAR REFORMA -->
    <div class="modal fade" id="modalReforma" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Documento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-repuesto">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Reforma</label>
                                <input id="id-reforma" type="hidden">
                                <input type="file" id="documento" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarDocumento()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Movimiento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" class="form-control" id="id-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Fecha:</label>
                                        <input type="text" disabled class="form-control" id="fecha-control">
                                    </div>

                                    <label>Cuenta a Aumentar</label>
                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <input type="text" disabled class="form-control" id="objeto-aumenta-control">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cuenta</label>
                                            <input type="text" disabled class="form-control" id="cuenta-aumenta-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Aumentar</label>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-aumentar-control">
                                        </div>
                                    </div>


                                    <label>Cuenta a Disminuir</label>
                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <input type="text" disabled class="form-control" id="objeto-baja-control">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cuenta</label>
                                            <input type="text" disabled class="form-control" id="cuenta-baja-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Restante Actualmente</label>
                                            <p style="color: red">Se resta también Saldo Retenido</p>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-restante-actual">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Disminuir</label>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-baja-control">
                                        </div>
                                    </div>

                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="form-group">
                                        <label>Reforma</label>
                                        <input type="file" id="documento-control" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-danger" onclick="verificarDenegar()">Denegar</button>
                    <button type="button" class="btn btn-success" onclick="verificarAutorizar()">Autorizar</button>
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
            let id = {{ $id }}; // id PROYECTO
            var ruta = "{{ URL::to('/admin/movicuentaproy/tablahistorico/') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){

            let id = {{ $id }}; // id PROYECTO
            var ruta = "{{ URL::to('/admin/movicuentaproy/tablahistorico/') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function infoSubirDoc(id){
            document.getElementById("formulario-repuesto").reset();
            $('#id-reforma').val(id);
            $('#modalReforma').modal('show');
        }

        function guardarDocumento(){
            var documento = document.getElementById('documento');
            var id = document.getElementById('id-reforma').value;

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|.pdf')){
                    toastr.error('formato permitidos: .png .jpg .jpeg .pdf');
                    return;
                }
            }else{
                toastr.error('Documento es requerido');
                return;
            }

            let formData = new FormData();
            formData.append('id', id);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/movicuentaproy/documento/guardar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalReforma').modal('hide');
                        toastr.success('Documento guardado');
                        recargar();
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

        // Mostrar información al jefe presupuesto si se autoriza el movimiento de cuenta
        function infoRevisarMovimiento(id){

            openLoading();
            document.getElementById("formulario-nuevo").reset();

            axios.post(url+'/movimientohistorico/verificar/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalAgregar').modal('show');
                        $('#id-control').val(id);
                        $('#fecha-control').val(response.data.fecha);
                        $('#objeto-aumenta-control').val(response.data.objetosube);
                        $('#cuenta-aumenta-control').val(response.data.cuentaaumenta);

                        $('#saldo-aumentar-control').val("$" + response.data.info.dinero);
                        $('#objeto-baja-control').val(response.data.objetobaja);
                        $('#cuenta-baja-control').val(response.data.cuentabaja);
                        $('#saldo-baja-control').val("-$" + response.data.info.dinero);
                        $('#saldo-restante-actual').val("$"+response.data.restantecuentabaja);

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function verificarDenegar(){

            Swal.fire({
                title: 'Denegar Movimiento',
                text: "Se borrar el Movimiento de Cuenta",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarMovimiento();
                }
            })
        }

        function borrarMovimiento(){

            var id = document.getElementById('id-control').value;

            openLoading();
            axios.post(url+'/movimientohistorico/denegar/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    $('#modalAgregar').modal('hide');

                    // el movimiento ya fue autorizado
                    if(response.data.success === 1){
                        recargar();

                        Swal.fire({
                            title: 'Movimiento Ya Autorizado',
                            text: "No se pudo borrar el Movimiento de Cuenta porque ya estaba Autorizado",
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
                        toastr.success('Movimiento Denegado');
                        recargar();
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


        function verificarAutorizar(){
            Swal.fire({
                title: 'Autorizar Movimiento',
                text: "Se registrara el Movimiento de Cuenta",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    autorizarMovimiento();
                }
            })
        }


        function autorizarMovimiento(){

            var id = document.getElementById('id-control').value;
            var documento = document.getElementById('documento-control');

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|.pdf')){
                    toastr.error('formato permitidos: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            var formData = new FormData();

            formData.append('id', id); // ID movicuentaproy
            formData.append('documento', documento.files[0]);

            openLoading();
            axios.post(url+'/movimientohistorico/autorizar/actualizar', formData, {

            })
                .then((response) => {
                    closeLoading();
                    $('#modalAgregar').modal('hide');

                    // el movimiento no se encontrado o fue borrado
                    if(response.data.success === 1){
                        recargar();

                        Swal.fire({
                            title: 'Movimiento Ya Autorizado',
                            text: "El Movimiento de Cuenta ya estaba autorizado",
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

                    else if(response.data.success === 2) {

                        let objeto = response.data.objeto;
                        let restante = response.data.restante;
                        let retenido = response.data.retenido;
                        let arestar = response.data.dinero;
                        let calculado = response.data.calculado;

                        Swal.fire({
                            title: 'Movimiento Inválido',
                            html: "La Cuenta a Modificar con el Código " + objeto +"<br>"
                                + "Tiene Saldo Insuficiente "+"<br>"
                                + "Saldo Restante $"+ restante +"<br>"
                                + "Saldo Retenido $"+ retenido +"<br>"
                                + "Saldo a Restar $"+ arestar +"<br>"
                                + "La cuenta quedara cón "+ calculado+"<br>"
                            ,
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 3){
                        toastr.success('Movimiento Autorizado');
                        recargar();
                    }
                    else{
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al registrar');
                });
        }


    </script>

@endsection
