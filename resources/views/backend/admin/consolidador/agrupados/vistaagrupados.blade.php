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
        <div class="row mb-2">
            <div class="col-sm-12">

                <div class="row">
                    <h1 style="margin-left: 15px">Listado de Agrupados</h1>
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


    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Agrupado</h4>
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

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Fecha:</label>
                                                <input type="hidden" id="id-agrupado">
                                                <input type="date" id="fecha-agrupados" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-6">

                                            <label>Nombre o Destino (Opcional):</label>
                                            <input type="text" maxlength="800" autocomplete="off" id="nombreodestino-agrupados" placeholder="Nombre o Destino del Proyecto" class="form-control">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Justificación:</label>
                                                <input type="text" maxlength="800" autocomplete="off" id="justificacion-agrupados" placeholder="Justificación " class="form-control">
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <label>Forma de Entrega (Parcial o Total):</label>
                                            <input type="text" maxlength="350" autocomplete="off" id="entrega-agrupados" placeholder="Forma de Entrega" class="form-control">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Plazo de Entrega:</label>
                                                <input type="text" maxlength="350" autocomplete="off" id="plazo-agrupados" placeholder="Plazo o tiempo de entrega" class="form-control">
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <label>Lugar de Entrega</label>
                                            <input type="text" maxlength="350" autocomplete="off" id="lugar-agrupados" placeholder="Lugar de entrega" class="form-control">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Forma de Contratación (Contrato u Orden):</label>
                                                <input type="text" maxlength="350" autocomplete="off" id="forma-agrupados" placeholder="Forma de Pago" class="form-control">
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <label>Otras Condiciones Especificar (Opcional):</label>
                                            <input type="text" maxlength="350" autocomplete="off" id="otros-agrupados" placeholder="Otras Condiciones (Opcional)" class="form-control">
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-6">

                                            <label>Administrador</label>
                                            <select class="custom-select" id="select-administrador">

                                            </select>

                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-6">

                                            <label>Evaluador Técnico 1</label>
                                            <select class="custom-select" id="select-evaluador">
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Evaluador Técnico 2</label>
                                            <select class="custom-select" id="select-evaluador2">
                                            </select>
                                        </div>

                                    </div>

                                    <br><br>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="verificar()">Actualizar</button>
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

    <script src="{{ asset('js/multiselect.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            var ruta = "{{ url('/admin/consolidador/listado/agrupados/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/consolidador/listado/agrupados/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function informacionPdf(id){
            window.open("{{ URL::to('/admin/consolidador/generar/pdf') }}/" + id);
        }


        // UNICAMENTE PODRA BORRAR SI MATERIAL NO HA SIDO COTIZADO
        // CUALQUIER MATERIAL DENTRO DEL AGRUPADO
        function informacionBorrar(id){

            Swal.fire({
                title: 'Borrar Agrupado',
                text: "Solo se podrá Borrar si ningún material ha sido cotizado o denegado de esta Agrupación",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Borrar',
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarAgrupado(id);
                }
            })
        }


        function borrarAgrupado(id){

            var formData = new FormData();
            formData.append('id', id);

            openLoading();

            axios.post(url+'/consolidador/borrar/agrupado', formData,{

            })
                .then((response) => {
                    closeLoading();


                    if(response.data.success === 1){

                        // FUE DENEGADO POR UCP

                        Swal.fire({
                            title: 'Error al Borrar',
                            text: "El agrupado por Denegado por UCP",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                recargar();
                            }
                        })

                    }

                    else if(response.data.success === 2){

                        // UN MATERIAL YA ESTA COTIZADO

                        Swal.fire({
                            title: 'Error al Borrar',
                            text: "Un Material de esta Agrupación ya se encuentra Cotizado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                recargar();
                            }
                        })

                    }

                    else if(response.data.success === 3){

                        // BORRADO CORRECTAMENTE

                        toastr.success('Agrupación Borrada');
                        recargar();
                    }
                    else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });
        }







        function informacionEditar(id){

            Swal.fire({
                title: 'Editar Agrupado',
                text: "Solo se podrá Editar si ningún material ha sido cotizado o denegado de esta Agrupación",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Ver',
            }).then((result) => {
                if (result.isConfirmed) {
                    buscarAgrupado(id);
                }
            })
        }


        function buscarAgrupado(id){

            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/consolidador/informacion/agrupada',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-agrupado').val(id);


                        $('#fecha-agrupados').val(response.data.lista.fecha);
                        $('#nombreodestino-agrupados').val(response.data.lista.nombreodestino);
                        $('#justificacion-agrupados').val(response.data.lista.justificacion);
                        $('#entrega-agrupados').val(response.data.lista.entrega);
                        $('#plazo-agrupados').val(response.data.lista.plazo);
                        $('#lugar-agrupados').val(response.data.lista.lugar);
                        $('#forma-agrupados').val(response.data.lista.forma);
                        $('#otros-agrupados').val(response.data.lista.otros);



                        document.getElementById("select-administrador").options.length = 0;
                        document.getElementById("select-evaluador").options.length = 0;
                        document.getElementById("select-evaluador2").options.length = 0;

                        $('#select-evaluador2').append('<option value="0">Seleccionar Opción</option>');

                        $.each(response.data.arraydatos, function( key, val ){

                            if(response.data.lista.id_contrato == val.id){
                                $('#select-administrador').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-administrador').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }


                            if(response.data.lista.id_evaluador == val.id){
                                $('#select-evaluador').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-evaluador').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }


                            if(response.data.lista.id_evaluador2 == val.id){
                                $('#select-evaluador2').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-evaluador2').append('<option value="' +val.id +'">'+val.nombre+'</option>');
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

            var idagrupado = document.getElementById('id-agrupado').value;
            var fecha = document.getElementById('fecha-agrupados').value;
            var nombreodestino = document.getElementById('nombreodestino-agrupados').value; // NULL
            var justificacion = document.getElementById('justificacion-agrupados').value; // NULL
            var entrega = document.getElementById('entrega-agrupados').value; // NULL
            var plazo = document.getElementById('plazo-agrupados').value; // NULL
            var lugar = document.getElementById('lugar-agrupados').value; // NULL
            var forma = document.getElementById('forma-agrupados').value; // NULL
            var otros = document.getElementById('otros-agrupados').value; // NULL

            var administrador = document.getElementById('select-administrador').value;
            var evaluador = document.getElementById('select-evaluador').value;
            var evaluador2 = document.getElementById('select-evaluador2').value;

            if(fecha === ''){
                toastr.error('Fecha es requerida');
                return;
            }

            if(administrador === ''){
                toastr.error('Administrador es requerido');
                return;
            }

            if(evaluador === ''){
                toastr.error('Evaluador es requerida');
                return;
            }

            var formData = new FormData();
            formData.append('idagrupado', idagrupado)
            formData.append('fecha', fecha);
            formData.append('nombreodestino', nombreodestino);
            formData.append('justificacion', justificacion);
            formData.append('entrega', entrega);
            formData.append('plazo', plazo);
            formData.append('lugar', lugar);
            formData.append('forma', forma);
            formData.append('otros', otros);
            formData.append('administrador', administrador);
            formData.append('evaluador', evaluador);
            formData.append('evaluador2', evaluador2);


            openLoading();

            axios.post(url+'/consolidador/actualizar/agrupado', formData,{

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // UN MATERIAL YA ESTA AGRUPADO O CANCELADO

                        Swal.fire({
                            title: 'Error',
                            text: "Se detecto un Material que ya fue Agrupado o Cancelado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }

                    else if(response.data.success === 2){

                        // ACTUALIZADO CORRECTAMENTE

                        $('#modalEditar').modal('hide');
                        toastr.success('Actualizado correctamente');
                        recargar();

                    }
                    else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });



        }





    </script>


@endsection
