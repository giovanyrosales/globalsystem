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
            <div class="col-sm-6">
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Datos</li>
                    <li class="breadcrumb-item active">Listado de Personal</li>
                </ol>
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

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/rrhh/hojadatos/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/rrhh/hojadatos/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function generarReporte(id){
            window.open("{{ URL::to('admin/rrhh/hojadatos/reporte') }}/" + id);
        }

        function modalBorrar(id){
            Swal.fire({
                title: 'Borrar',
                text: "Eliminar Registro",
                icon: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#ce1818',
                confirmButtonText: 'Borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id)
                }
            })
        }

        function borrarRegistro(id){
            openLoading();

            axios.post(url+'/rrhh/hojadatos/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                       toastr.success('Registro borrado')
                        recargar()
                    }else{
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al borrar');
                });
        }

    </script>


@endsection
