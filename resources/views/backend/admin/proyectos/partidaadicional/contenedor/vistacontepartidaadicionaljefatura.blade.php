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
                    <h1>Solicitudes para Partidas Adicionales</h1>

                    <!-- Botón para dar permiso y crear x partidas adicionales. Para jefe presupuesto -->
                    @can('boton.autorizar.denegar.partida.adicional')
                        @if($infoPro->permiso_partida_adic == 1)
                            <button type="button" style="margin-top: 15px" onclick="modalPermisoDenegar()" class="btn btn-danger btn-sm">
                                <i class="fas fa-stop"></i>
                                Denegar Partidas Adicionales
                            </button>
                        @else
                            <button type="button" style="margin-top: 15px" onclick="modalPermisoAprobar()" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i>
                                Autorizar Partidas Adicionales
                            </button>
                        @endif
                    @endcan

                    <!-- Botón para modificar porcentaje de obre adicional, por defecto sera 20% -->
                    @can('boton.modal.porcentaje.obra.dicional')
                        <button type="button" style="margin-top: 15px" onclick="infoPorcentajeObra()" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i>
                            Porcentaje Obra Adicional
                        </button>
                    @endcan

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

    <div class="modal fade" id="modalEstado">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estado de Partida Adicional</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input id="id-contenedor" type="hidden">

                                        <div class="form-group">
                                            <label>Monto de Partida Adicional</label>
                                            <label id="txt-monto-partida" class="form-control"></label>
                                        </div>

                                        <div class="form-group">
                                            <label>Bolsón asignado a Proyecto</label>
                                            <label id="txt-nombre-bolson" class="form-control"></label>
                                        </div>

                                        <div class="form-group">
                                            <label>Monto Restantes de Bolsón </label>
                                            <label id="txt-restante-bolson" class="form-control"></label>
                                        </div>

                                        <div class="form-group">
                                            <label>Documento</label>
                                            <input type="file" id="documento-obra" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarU" onclick="preguntarAprobar()">Aprobar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPorcentaje">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Porcentaje Máximo de Obra Adicional</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-porcentaje">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Porcentaje %:</label>
                                        <input type="number" autocomplete="off" class="form-control" id="porcentaje-obra">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarPorcentajeObra()">Actualizar</button>
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
            var ruta = "{{ URL::to('/admin/partida/adici/conte/jefatura/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            let id = {{ $id }}; // id PROYECTO
            var ruta = "{{ URL::to('/admin/partida/adicional/contenedor/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function modalPermisoAprobar(){
            Swal.fire({
                title: 'Autorizar Partidas',
                text: "Se autorizara poder crear las partidas adicionales necesarias",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    autorizarPartida();
                }
            })
        }

        function autorizarPartida(){

            openLoading();

            // id proyecto
            let id = {{$id}};

            axios.post(url+'/partida/adicional/permiso/autorizar',{
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Autorizado',
                            text: "Se podrá crear las partidas adicionales necesarias",
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
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function modalPermisoDenegar(){
            Swal.fire({
                title: 'Denegar Partidas',
                text: "Se restringirá seguir creando partidas adicionales",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    denegarPartida();
                }
            })
        }

        function denegarPartida(){

            openLoading();

            // id proyecto
            let id = {{$id}};

            axios.post(url+'/partida/adicional/permiso/denegar', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Autorizar Partidas',
                            text: "No se podrá crear partidas adicionales al Proyecto",
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
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function vistaPartidasAdicionales(id){
            // id Contenedor
            window.location.href="{{ url('/admin/partida/adicional/creacion/index') }}/" + id;
        }

        // MODAL PARA REVISAR CUANDO MONTO TIENE LAS PARTIDAS Y ASIGNAR UN BOLSÓN
        function vistaInformacionEstado(id){

            document.getElementById("txt-monto-partida").innerHTML = '';
            document.getElementById("txt-nombre-bolson").innerHTML = '';
            document.getElementById("txt-restante-bolson").innerHTML = '';

            openLoading();

            axios.post(url+'/partida/adicio/infojefatura/estado/informacion', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    // está en modo desarrollo
                    if(response.data.success === 1) {

                        Swal.fire({
                            title: 'Partida En Desarrollo',
                            text: "La Partida Adicional se encuentra en modo desarrollo",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }
                    else if(response.data.success === 2){
                            // ya estaba aprobada

                            Swal.fire({
                                title: 'Partida Ya Aprobada',
                                text: "La Partida Adicional ya se encontraba Aprobada",
                                icon: 'info',
                                showCancelButton: false,
                                allowOutsideClick: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })

                    }else if(response.data.success === 3){
                        //no tiene partidas el contenedor, así que no se puede actualizar

                        Swal.fire({
                            title: 'No hay Partidas',
                            text: "No se se encuentra ninguna Partida Adicional Registrada",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 4){
                        //no tiene partidas el contenedor, así que no se puede actualizar

                        Swal.fire({
                            title: 'Sin Bolsón',
                            text: "El proyecto no tiene bolsón asignado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 5){
                            // viene la información

                        $('#modalEstado').modal('show');

                        // asignar ID contenedor
                        $('#id-contenedor').val(id);

                        document.getElementById("txt-monto-partida").innerHTML = response.data.montopartida;
                        document.getElementById("txt-nombre-bolson").innerHTML = response.data.nombolson;
                        document.getElementById("txt-restante-bolson").innerHTML = response.data.bolsonrestante;

                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function infoPorcentajeObra(){

            openLoading();
            let id = {{ $id }}; // id PROYECTO

            document.getElementById("formulario-porcentaje").reset();

            axios.post(url+'/partida/adicional/porcentaje/info', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalPorcentaje').modal('show');
                        $('#porcentaje-obra').val(response.data.porcentaje);
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

        function actualizarPorcentajeObra(){

            var porcentaje = document.getElementById('porcentaje-obra').value;
            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(porcentaje === ''){
                toastr.error('Porcentaje es requerido');
                return;
            }

            if(!porcentaje.match(reglaNumeroDosDecimal)) {
                toastr.error('Porcentaje debe ser número Decimal Positivo. Solo se permite 2 Decimales');
                return;
            }

            if(porcentaje < 0){
                toastr.error('Porcentaje no permite números negativos');
                return;
            }

            if(porcentaje > 100){
                toastr.error('Porcentaje Máximo sera 100%');
                return;
            }

            openLoading();
            let id = {{ $id }}; // id PROYECTO

            var formData = new FormData();
            formData.append('id', id);
            formData.append('porcentaje', porcentaje);

            document.getElementById("formulario-porcentaje").reset();

            axios.post(url+'/partida/adicional/porcentaje/actualizar', formData, {

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalPorcentaje').modal('hide');
                        toastr.success('Actualizado');
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


        function preguntarAprobar(){

            Swal.fire({
                title: 'Aprobar Partida Adicional',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    aprobarPartidaAdicional();
                }
            })
        }

        function aprobarPartidaAdicional(){

            var idcontenedor = document.getElementById('id-contenedor').value;
            var documento = document.getElementById('documento-obra'); // null file

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|.pdf')){
                    toastr.error('formato permitidos: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            openLoading();

            var formData = new FormData();
            formData.append('idcontenedor', idcontenedor);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/partida/adicional/aprobar', formData, {
            })
                .then((response) => {
                    closeLoading();


                    if(response.data.success === 1) {

                        let conteo = response.data.conteo;

                        Swal.fire({
                            title: 'Materiales Incompleto',
                            text: "Hay " + conteo + " Materiales que no tiene asignado un Objeto Específico",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })

                    }
                    else if(response.data.success === 2){
                        $('#modalEstado').modal('hide');
                        toastr.success('Partida Adicional Aprobada');
                        recargar();
                    }
                    else if(response.data.success === 3){

                        let porcentaje = response.data.porcentaje + "%";
                        let montomaximo = response.data.montomaximo;
                        let resta = response.data.restado;

                        Swal.fire({
                            title: 'Monto Excedido',
                            html: "El Proyecto Supero el monto para Partida Adicional." + "<br>"
                                + "Porcentaje para Obra Adicional: "+ porcentaje + "<br>"
                                + "Monto máximo para Obra Adicional es $"+ montomaximo +"<br>"
                                + "Se esta Excediendo por $"+ resta +"<br>"
                            ,
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else {
                        toastr.error('Error al Aprobar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al Aprobar');
                    closeLoading();
                });
        }

        function infoPdf(id){
            // id Contenedor

            openLoading();

            axios.post(url+'/partida/adicional/comprobar/quehaya', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    // esta en modo revisión
                    if(response.data.success === 1){
                        window.open("{{ URL::to('admin/partida/adicional/verpdf') }}/" + id);

                    }else{
                        // la partida adicional esta aprobada

                        Swal.fire({
                            title: 'No Encontrada',
                            text: "No se encontró Partidas Adicionales",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });

        }


    </script>

@endsection
