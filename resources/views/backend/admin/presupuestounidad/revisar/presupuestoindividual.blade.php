@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

@stop

<style>

    .modal-xl {
        max-width: 90% !important;
    }

</style>

<div class="content-wrapper" style="display: none" id="divcontenedor">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label style="font-size: 18px">Presupuesto Año: {{ $infoAnio->nombre }}</label>
                    </div>


                    <div class="form-group col-md-3">
                        <label style="color:#191818">Estado</label>
                        <br>
                        <div>
                            <select class="form-control" id="select-estado" onchange="actualizarEstado()">
                                @foreach($arrayestado as $item)

                                    @if($estado == $item->id)
                                        <option value="{{$item->id}}" selected="selected">{{$item->nombre}}</option>
                                    @else
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    <section class="content" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">

                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="tablaDatatable">

                                        </div>
                                    </div>
                                </div>
                            </section>

                        </form>
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            openLoading();

            let iddepa = {{ $iddepa }};
            let idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/departamento/presupuesto/contenedor/') }}/" + iddepa + "/" + idanio;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function actualizarEstado(){

            var estado = document.getElementById('select-estado').value;

            var idpresupuesto = {{ $idpre }};

            let formData = new FormData();
            formData.append('idpresupuesto', idpresupuesto);
            formData.append('idestado',estado);

            axios.post(url+'/p/presupuesto/unidad/cambiar/estado', formData, {
            })
                .then((response) => {

                    if(response.data.success === 1) {

                        Swal.fire({
                            title: 'Presupuesto Vacío',
                            text: "El Presupuesto esta creado, pero no tiene ningún Material Registrado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Recargar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Estado Actualizado',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }else{
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
