@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Requerimientos</h1>
                </div>

            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label style="margin: 8px">Año de Presupuesto</label>
                                    <div style="margin-left: 6px" class="col-sm-3">
                                        <select class="form-control" id="select-anio">
                                            @foreach($anios as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" onclick="verificar()" style="font-weight: bold; background-color: #28a745; color: white !important;"
                                            class="button button-rounded button-pill button-small">Buscar</button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaDatatableRequisicion">
                                </div>
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


    <script>

        function verificar(){

            var anio = document.getElementById('select-anio').value;

            if(anio === ''){
                toastr.error('Año es requerido');
                return;
            }

            var sel = document.getElementById("select-anio");
            var txtanio = sel.options[sel.selectedIndex].text;

            axios.post(url+'/p/anio/permiso/requerimiento', {
                'anio' : anio
            })
                .then((response) => {

                    if(response.data.success === 1){
                        Swal.fire({
                            title: 'Departamento No Encontrado',
                            text: "El usuario no esta registrado a ningún Departamento",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });
                    }
                    else if(response.data.success === 2){
                        Swal.fire({
                            title: 'Sin Autorización',
                            text: "Presupuesto de Año " + txtanio + ", esta en modo Desarrollo",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });
                    }
                    else if(response.data.success === 3){
                        Swal.fire({
                            title: 'Presupuesto Pendiente',
                            text: "Presupuesto de Año " + txtanio + ", esta en modo Revisión",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });
                    }
                    else if(response.data.success === 4){
                       // proceder a ver requerimientos

                        window.location.href="{{ url('/admin/p/requerimientos/vista') }}/" + anio;

                    }
                    else if(response.data.success === 5){
                        Swal.fire({
                            title: 'Presupuesto Aprobado',
                            text: "Esperando que se cree las Cuentas de Unidades",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });
                    }
                    else if(response.data.success === 6){
                        // presupuesto no creado

                        Swal.fire({
                            title: 'Presupuesto No Creado',
                            text: "Presupuesto de Año " + txtanio + ", no esta creado",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });
                    }

                    else{
                        toastr.error('error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al buscar');
                    closeLoading();
                });
        }

    </script>


@endsection
