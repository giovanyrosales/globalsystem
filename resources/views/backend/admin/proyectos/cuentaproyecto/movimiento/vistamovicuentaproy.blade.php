@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
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
        <div class="container-fluid">
            <div class="row mb-6">
                <div class="col-sm-5">
                    <h1>Movimiento Cuenta de Proyecto</h1>
                    <button type="button" style="margin-top: 15px" onclick="verHistorico()" class="btn btn-primary btn-sm">
                        <i class="fas fa-list-alt"></i>
                        Histórico
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
                    <h4 class="modal-title">Nueva Cuenta Proyecto</h4>
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

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Código y Objeto Específico</label>
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
                                            <label>Saldo a Modificar</label>
                                            <input type="text" class="form-control" placeholder="0.00" id="saldo-modificar">
                                        </div>
                                    </div>

                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">


                                    <div class="form-group">
                                        <label>Cuenta a Modificar para Disminuir Saldo</label>
                                        <select class="form-control" id="select-cuentaproy" onchange="buscarSaldoRestante()" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="col-md-12 row">
                                        <div class="form-group col-md-6">
                                            <label>Fecha:</label>
                                            <input type="date" class="form-control" id="fecha-nuevo">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label style="font-weight: bold">Saldo Restante:</label>
                                            <input type="text" disabled class="form-control" id="restante">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Reforma</label>
                                            <input type="file" id="documento" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificar()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/movicuentaproy/tablamovicuentaproy') }}/" + id;
            $('#tablaDatatable').load(ruta);

            $('#select-proyecto').select2({
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

        function recargar(){
            var ruta = "{{ url('/admin/movicuentaproy/tablamovicuentaproy') }}";
            $('#tablaDatatable').load(ruta);
        }

        function verHistorico(){
            let id = {{ $id }}; // ID PROYECTO
            window.location.href="{{ url('/admin/movicuentaproy/historico') }}/" + id;
        }

        function buscarSaldoRestante(){
            let id = document.getElementById('select-cuentaproy').value;
            openLoading();

            axios.post(url+'/movicuentaproy/info/saldo',{
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
            openLoading();
            document.getElementById("formulario-nuevo").reset();

            axios.post(url+'/movicuentaproy/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregar').modal('show');
                        document.getElementById('select-tipo').value = '0';

                        $('#id-editar').val(id);

                        let objeto = response.data.objeto;

                        $('#codigo').val(objeto.codigo + " - " + objeto.nombre);
                        $('#cuenta').val(response.data.cuenta);
                        $('#saldo-restante').val(response.data.restante);

                        var fecha = new Date();
                        $('#fecha-editar').val(fecha.toJSON().slice(0,10));

                        document.getElementById("select-cuentaproy").options.length = 0;

                        $('#select-cuentaproy').append('<option value="0">Seleccionar Opción</option>');

                        $.each(response.data.arraycuentaproy, function( key, val ){
                            if(response.data.info.objespeci_id == val.id){
                                $('#select-cuentaproy').append('<option value="' +val.id +'" selected="selected">'+val.codigo + ' - ' + val.nombre +'</option>');
                            }else{
                                $('#select-cuentaproy').append('<option value="' +val.id +'">'+val.codigo + ' - ' + val.nombre +'</option>');
                            }
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
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                   nuevo();
                }
            })
        }

        function nuevo(){

            // ID CUENTAPROY
            var id = document.getElementById('id-editar').value;

            var saldomodificar = document.getElementById('saldo-modificar').value;

            var selectcuenta = document.getElementById('select-cuentaproy').value;
            var fecha = document.getElementById('fecha-nuevo').value;
            var documento = document.getElementById('documento');

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(saldomodificar === ''){
                toastr.error('Saldo a modificar es requerido');
                return;
            }

            if(!saldomodificar.match(reglaNumeroDecimal)) {
                toastr.error('Saldo a modificar debe ser decimal y no negativo');
                return;
            }

            if(saldomodificar < 0){
                toastr.error('Saldo a modificar no debe ser negativo');
                return;
            }

            if(saldomodificar.length > 10){
                toastr.error('Saldo a modificar debe tener máximo 10 caracteres');
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

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|.pdf')){
                    toastr.error('formato permitidos: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('saldomodi', saldomodificar);
            formData.append('selectcuenta', selectcuenta);
            formData.append('fecha', fecha);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/movicuentaproy/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let saldo = response.data.saldo;
                        let unido = response.data.unido;

                        Swal.fire({
                            title: 'Movimiento Inválido',
                            text: "La Cuenta a Modificar con el Código " + unido + ". Queda con Saldo Negativo $" + saldo,
                            icon: 'info',
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



                    if(response.data.success === 1){
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



        function editar(){
            var id = document.getElementById('id-editar').value;
            var proyecto = document.getElementById('select-proyecto-editar').value;
            var cuenta = document.getElementById('select-cuentaproy-editar').value;
            var documento = document.getElementById('documento-editar');
            var aumenta = document.getElementById('aumenta-editar').value;
            var disminuye = document.getElementById('disminuye-editar').value;
            var fecha = document.getElementById('fecha-editar').value;

            if(proyecto === ''){
                toastr.error('Nombre de Proyecto es Requerido');
                return;
            }

            if(cuenta === ''){
                toastr.error('Cuenta Proyecto es Requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(aumenta.length > 0){
                if(!aumenta.match(reglaNumeroDecimal)) {
                    toastr.error('Aumenta debe ser decimal y no negativo');
                    return;
                }

                if(aumenta < 0){
                    toastr.error('Aumenta no debe ser negativo');
                    return;
                }

                if(aumenta.length > 10){
                    toastr.error('Aumenta debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                aumenta = 0;
            }

            if(disminuye.length > 0){
                if(!disminuye.match(reglaNumeroDecimal)) {
                    toastr.error('Disminuye debe ser decimal y no negativo');
                    return;
                }

                if(disminuye < 0){
                    toastr.error('Disminuye no debe ser negativo');
                    return;
                }

                if(disminuye.length > 10){
                    toastr.error('Disminuye debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                disminuye = 0;
            }

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|pdf')){
                    toastr.error('formato de documento permitido: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('proyecto', proyecto);
            formData.append('cuenta', cuenta);
            formData.append('aumenta', aumenta);
            formData.append('disminuye', disminuye);
            formData.append('fecha', fecha);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/movicuentaproy/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
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
