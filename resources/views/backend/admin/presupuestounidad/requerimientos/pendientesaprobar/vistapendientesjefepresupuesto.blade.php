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
                    <h1>REQUERIMIENTOS PENDIENTES</h1>

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

    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detalle Requerimiento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-detalle">
                        <div class="card-body">
                            <div class="row">
                                <table  class="table" id="matriz-requisicion"  data-toggle="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%">#</th>
                                            <th style="width: 5%">Obj. Específico</th>
                                            <th style="width: 8%">Material</th>
                                            <th style="width: 3%">Cantidad</th>
                                            <th style="width: 5%">Monto</th>
                                            <th style="width: 12%">Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

            var ruta = "{{ URL::to('/admin/presupuesto/verrequerimientos/pendientes/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/presupuesto/verrequerimientos/pendientes/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function informacionMaterial(id){

            $("#matriz-requisicion tbody tr").remove();

            openLoading();

            axios.post(url+'/presupuesto/verrequerimientos/pendientes/info', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                   if(response.data.success === 1){

                        var infodetalle = response.data.lista;

                        for (var i = 0; i < infodetalle.length; i++) {

                            var markup = "<tr id='"+infodetalle[i].id+"'>"+

                                "<td>"+
                                "<p id='fila"+(i+1)+"' class='form-control' style='max-width: 65px'>"+(i+1)+"</p>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].objcodigo+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].matedescripcion+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].cantidad+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='$"+infodetalle[i].dinero+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].material_descripcion+"' disabled class='form-control'>"+
                                "</td>"+

                                "</tr>";

                            $("#matriz-requisicion tbody").append(markup);
                        }

                        // TOTAL (CANTIDAD * PRECIO UNITARIO)

                        var markup = "<tr id=''>"+

                            "<td>"+
                            "<p class='form-control' style='max-width: 65px'>Total</p>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='$"+response.data.total+"' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "</tr>";

                        $("#matriz-requisicion tbody").append(markup);

                        $('#modalDetalle').css('overflow-y', 'auto');
                        $('#modalDetalle').modal({backdrop: 'static', keyboard: false})
                   }else{
                        toastr.error('Información no encontrada');
                   }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function informacionAprobar(id){

            Swal.fire({
                title: 'Aprobar Requerimiento',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aprobar',
            }).then((result) => {
                if (result.isConfirmed) {
                    aprobarRequerimiento(id);
                }
            })
        }

        function aprobarRequerimiento(id){

            openLoading();

            axios.post(url+'/presupuesto/verrequerimientos/pendientes/aprobar', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        recargar();
                        toastr.success('Aprobado correctamente');
                    }else{
                        toastr.error('Error al aprobar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al aprobar');
                });
        }



    </script>

@endsection
