@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

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
                <h1>Cotizaciones Unidad Autorizadas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Cotizaciones Autorizadas</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
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


    <div class="modal fade" id="modalGenerarOrden">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detalles de Orden</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario-crear-orden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <label>Fecha de Orden</label>
                                            <input type="date" id="fecha_orden" class="form-control">
                                            <input type="hidden" id="id-coti" class="form-control">
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label>Número de Acta (no ingresar palabra acta)</label>
                                                <input type="text" class="form-control" maxlength="100" id="num_acta">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label>Número de Acuerdo (no ingresar palabra acuerdo)</label>
                                                <input type="text" class="form-control" maxlength="100" id="num_acuerdo">
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
                    <button type="button" class="btn btn-primary" onclick="verificarOrden()">Guardar</button>
                </div>
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
            let idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/cotizacion/unidad/autorizadas/tabla') }}/" + idanio;
            $('#tablaDatatable').load(ruta);

            var fecha = new Date();
            document.getElementById('fecha_orden').value = fecha.toJSON().slice(0,10);
            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            let idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/cotizacion/unidad/autorizadas/tabla') }}/" + idanio;
            $('#tablaDatatable').load(ruta);
        }

        // VER DETALLES
        function verProcesadas(idcoti){
            window.location.href="{{ url('/admin/p/cotizacion/unidad/detalle') }}/" + idcoti;
        }

        function abrirModalOrden(id){
            $('#id-coti').val(id);
            document.getElementById("formulario-crear-orden").reset();
            $('#modalGenerarOrden').modal('show');
        }

        function verificarOrden(){
            var fecha = document.getElementById('fecha_orden').value;
            var idcoti = document.getElementById('id-coti').value;

            var numacuerdo = document.getElementById('num_acuerdo').value;
            var numacta = document.getElementById('num_acta').value;

            if(fecha === ''){
                toastr.error('Fecha para es requerida');
                return;
            }

            if(numacuerdo === ''){
                toastr.error('Número de acuerdo es requerido');
                return;
            }

            if(numacuerdo.length > 100){
                toastr.error('Número de acuerdo debe tener máximo 100 caracteres');
                return;
            }

            if(numacta === ''){
                toastr.error('Número de acuerdo de acta');
                return;
            }

            if(numacta.length > 100){
                toastr.error('Número de acta debe tener máximo 100 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('idcoti', idcoti);
            formData.append('fecha', fecha);
            formData.append('numacta', numacta);
            formData.append('numacuerdo', numacuerdo);

            axios.post(url+'/p/ordencompra/unidad/generar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {
                       // GENERADO CORRECTAMENTE

                        Swal.fire({
                            title: 'Generado Correctamente',
                            text: "",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Recargar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }

                    else {
                        toastr.error('Error al crear orden');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al crear orden');
                    closeLoading();
                });
        }


    </script>


@endsection
