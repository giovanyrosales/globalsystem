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
                text: "Solo se podra Borrar si ningun material ha sido cotizado de esta Agrupación",
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

                    else if(response.data.success === 2){

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



    </script>


@endsection
