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
                                        <table class="table" id="matriz-busqueda" data-toggle="table">
                                            <tbody>
                                            <tr style="width: 100%">
                                                <td >
                                                    <input type='text' id="materialnuevosolicitado" autocomplete="off" data-info='0' class='form-control' onkeyup='buscarMaterialSolicitud(this)' maxlength='300'  >
                                                    <div class='droplistado' style='position: absolute; z-index: 9; width: 85% !important;'></div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Unidades</label>
                                            <input type="number" class="form-control" autocomplete="off" id="cantidad-material-nuevo">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Periodo</label>
                                            <input type="number" class="form-control" autocomplete="off" id="cantidad-material-nuevo">
                                        </div>


                                        <div class="form-group">
                                            <label>Descripción del Material</label>
                                            <input type="text" class="form-control" autocomplete="off" id="descripcion-material-nuevo">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificarNuevaFila()">Agregar Fila</button>
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
            let id = {{ $id }}; // id PRESUP UNIDAD
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

        function agregarSolicitud(){
            document.getElementById("formulario-nuevo-material").reset();
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

            // obtener texto del li
            let texto = $(edrop).text();
            // setear el input de la descripcion
            $(txtContenedorGlobal).val(texto);

            // agregar el id al atributo del input descripcion
            $(txtContenedorGlobal).attr('data-info', edrop.id);
            //$(txtContenedorGlobal).data("info");
        }


    </script>


@endsection
