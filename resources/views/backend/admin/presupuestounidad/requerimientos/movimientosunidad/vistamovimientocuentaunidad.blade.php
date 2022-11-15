@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
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
                    <h1>Movimiento Cuenta de Proyecto</h1>
                    <button type="button" style="margin-top: 15px;font-weight: bold; background-color: #17a2b8; color: white !important;"
                            onclick="verHistorico()" class="button button-3d button-rounded button-pill button-small">
                        <i class="fas fa-list-alt"></i>
                        Histórico
                    </button>
                    <button type="button" style="margin-top: 15px;font-weight: bold; background-color: #17a2b8; color: white !important;"
                            onclick="verSolicitudes()" class="button button-3d button-rounded button-pill button-small">
                        <i class="fas fa-list-alt"></i>
                        Solicitud Material
                    </button>
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
                                        <input type="hidden" class="form-control" id="id-editar">
                                    </div>

                                    <label>Cuenta a Aumentar</label>
                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">


                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <input type="text" disabled class="form-control" id="codigo">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cuenta</label>
                                            <input type="text" disabled class="form-control" id="cuenta">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Restante</label>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-restante">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Aumento de Saldo</label>
                                            <input type="text" autocomplete="off" class="form-control" placeholder="0.00" id="saldo-modificar">
                                        </div>
                                    </div>

                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="form-group">
                                        <label>Obj. Específico a Modificar para Disminuir Saldo</label>
                                        <select class="form-control" id="select-cuentaunidad" onchange="buscarSaldoRestante()" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="col-md-12 row">

                                        <div class="form-group col-md-6">
                                            <label style="font-weight: bold">Saldo Restante:</label>
                                            <p style="color: red">Se resta también el Saldo Retenido</p>
                                            <input type="text" disabled class="form-control" id="restante">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Fecha</label>
                                            <input type="date" class="form-control" id="fecha-nuevo">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="verificar()">Guardar</button>
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
            let id = {{ $idpresup }}; // id presup_unidad
            var ruta = "{{ URL::to('/admin/p/requerimientos/movicuentaunidad/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            $('#select-cuentaunidad').select2({
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

        function recargar(){
            let id = {{ $idpresup }}; // id presup_unidad
            var ruta = "{{ URL::to('/admin/p/requerimientos/movicuentaunidad/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function verHistorico(){
            let id = {{ $idpresup }}; // ID presup_unidad
            window.location.href="{{ url('/admin/p/movicuentaunidad/historico') }}/" + id;
        }

        function buscarSaldoRestante(){
            let id = document.getElementById('select-cuentaunidad').value;
            openLoading();

            axios.post(url+'/p/movicuentaunidad/informacion/saldo',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#restante').val(response.data.restante);
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function informacionAgregar(id){
            // id de cuenta unidad
            openLoading();
            document.getElementById("formulario-nuevo").reset();

            axios.post(url+'/p/movicuentaunidad/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalAgregar').modal('show');

                        $('#id-editar').val(id);

                        let objeto = response.data.objeto;

                        $('#codigo').val(objeto.codigo + " - " + objeto.nombre);
                        $('#cuenta').val(response.data.cuenta);
                        $('#saldo-restante').val(response.data.restante);

                        var fecha = new Date();
                        $('#fecha-editar').val(fecha.toJSON().slice(0,10));

                        document.getElementById("select-cuentaunidad").options.length = 0;

                        $('#select-cuentaunidad').append('<option value="0">Seleccionar Opción</option>');

                        $.each(response.data.arraycuentaunidad, function( key, val ){
                            $('#select-cuentaunidad').append('<option value="' +val.id +'">'+val.codigo + ' - ' + val.nombre +'</option>');
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


        function verificar(){
            Swal.fire({
                title: 'Guardar Movimiento',
                text: "Se debe esperar la Aprobación",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    nuevoMovimientoCuenta();
                }
            })
        }

        function nuevoMovimientoCuenta(){

            // ID CUENTA UNIDAD
            var idcuentaunidad = document.getElementById('id-editar').value;

            var saldomodificar = document.getElementById('saldo-modificar').value;

            var selectcuenta = document.getElementById('select-cuentaunidad').value;
            var fecha = document.getElementById('fecha-nuevo').value;

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(saldomodificar === ''){
                toastr.error('Saldo a modificar es requerido');
                return;
            }

            if(!saldomodificar.match(reglaNumeroDosDecimal)) {
                toastr.error('Saldo a modificar debe ser Decimal Positivo. Solo se permite 2 Decimales');
                return;
            }

            if(saldomodificar < 0){
                toastr.error('Saldo a modificar no debe ser negativo');
                return;
            }

            if(saldomodificar > 1000000){
                toastr.error('Saldo a modificar debe tener máximo 1 millón');
                return;
            }

            if(selectcuenta === '0'){
                toastr.error('Cuenta a Modificar es requerido');
                return;
            }

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('idcuentaunidad', idcuentaunidad); // id cuenta unidad a subir
            formData.append('saldomodificar', saldomodificar); // dinero
            formData.append('selectcuenta', selectcuenta); // id cuenta unidad a descontar
            formData.append('fecha', fecha);

            axios.post(url+'/p/registrar/movimiento/cuentaunidad', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Movimiento Denegado',
                            text: "Se denegó el Permiso para crear un movimiento de Cuenta",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })

                    }
                    else if(response.data.success === 2){

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
                        $('#modalAgregar').modal('hide');
                        recargar();

                        Swal.fire({
                            title: 'Movimiento Creado',
                            text: "Se debera esperar que sea Aprobado el Movimiento de Cuenta",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }

                    else if(response.data.success === 4){

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
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        // vista para solicitar material que no esta en mi presupuesto y quitara
        // dinero de un código
        function verSolicitudes(){
            let id = {{ $idpresup }}; // ID presup_unidad
            window.location.href="{{ url('/admin/p/movicuentaunidad/solicitud/material') }}/" + id;
        }



    </script>

@endsection
