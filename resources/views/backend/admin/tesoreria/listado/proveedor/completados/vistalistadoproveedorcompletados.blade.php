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
    .dataTables_wrapper .dataTables_info {
        float: left !important;
        text-align: left;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: left !important;
        text-align: left;
        padding-left: 10px;
    }
</style>


<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">

                <div class="form-row">
                    <!-- Select de Años -->
                    <div class="form-group col-md-4">
                        <label class="control-label">Año:</label>
                        <select id="select-anios" class="form-control">
                            @foreach($arrayAnios as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select de Meses -->
                    <div class="form-group col-md-4">
                        <label class="control-label">Mes:</label>
                        <select id="select-meses" class="form-control">
                            <option value="0">Todos</option>
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>

                    <!-- Botón Buscar -->
                    <div class="form-group col-md-2 align-self-end">
                        <button type="button" onclick="buscarTabla()" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>

                <hr>


                <button type="button" style="margin: 10px" onclick="checkRegresar()" class="btn btn-primary btn-sm">
                    <i class="fas fa-minus"></i>
                    Regresar a Listado UCP
                </button>

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Registros</li>
                    <li class="breadcrumb-item active">Listado PROVEEDOR</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray">
                <div class="card-header">
                        <h3 class="card-title">Listado Completadas</h3>
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        $(document).ready(function(){

            const anioActual = "{{ $anioActual }}";

            // Selecciona el año actual si está presente
            const selectAnios = document.getElementById("select-anios");

            for (let i = 0; i < selectAnios.options.length; i++) {
                if (selectAnios.options[i].value === anioActual) {
                    selectAnios.selectedIndex = i;
                    break;
                }
            }

            openLoading()

            var ruta = "{{ URL::to('/admin/tesoreria/listado-proveedor-completados/tabla/index') }}/" + anioActual + "/" + 0;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });




        function informacion(id){
            window.location.href="{{ url('/admin/tesoreria/listado/edicion/index') }}/"+id;
        }

       


        function checkRegresar(){

            var tableRows = document.querySelectorAll('#tabla tbody tr');
            var selected = [];

            if (tableRows.length === 0) {
                toastr.error('No hay registros');
                return;
            }

            tableRows.forEach(function(row) {
                var checkbox = row.querySelector('.checkbox');
                if(checkbox != null) {
                    if (checkbox.checked) {
                        var dataInfo = row.getAttribute('data-info');
                        selected.push(dataInfo);
                    }
                }
            });

            if (selected.length <= 0) {
                toastr.error('Seleccionar Mínimo 1 Fila')
                return;
            }

            preguntarRegresar()
        }


        function preguntarRegresar(){
            Swal.fire({
                title: 'Regresar',
                text: "Mover registros seleccionados a Listado UCP",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Mover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    completarSeleccionados()
                }
            })
        }

        function completarSeleccionados(){
            var tableRows = document.querySelectorAll('#tabla tbody tr');

            var selected = [];

            if (tableRows.length === 0) {
                toastr.error('No hay registros');
                return;
            }

            tableRows.forEach(function(row) {
                var checkbox = row.querySelector('.checkbox');
                if(checkbox != null) {
                    if (checkbox.checked) {
                        var dataInfo = row.getAttribute('data-info');
                        selected.push(dataInfo);
                    }
                }
            });

            if (selected.length <= 0) {
                toastr.error('Seleccionar Mínimo 1 Fila')
                return;
            }

            let listado = selected.toString();
            let reemplazo = listado.replace(/,/g, "-");


            openLoading();
            var formData = new FormData();
            formData.append('reemplazo', reemplazo);

            axios.post(url+'/tesoreria/general/mover-a-listado', formData, {
            })
                .then((response) => {

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        recargarAnioTodos()
                    }
                    else {
                        closeLoading();
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }


        function buscarTabla(){
            var anio = document.getElementById('select-anios').value;
            var meses = document.getElementById('select-meses').value;

            if(anio === ''){
                toastr.error('Años es requerido');
                return
            }

            if(meses === ''){
                toastr.error('Mes es requerido');
                return
            }

            openLoading()

            var ruta = "{{ url('/admin/tesoreria/listado-proveedor-completados/tabla/index') }}/" + anio + "/" + meses;
            $('#tablaDatatable').load(ruta);
        }


        function recargarAnioTodos(){

            const anioActual = "{{ $anioActual }}";

            // Selecciona el año actual si está presente
            const selectAnios = document.getElementById("select-anios");

            for (let i = 0; i < selectAnios.options.length; i++) {
                if (selectAnios.options[i].value === anioActual) {
                    selectAnios.selectedIndex = i;
                    break;
                }
            }

            $('#select-meses').prop('selectedIndex', 0).change();

            var ruta = "{{ url('/admin/tesoreria/listado-proveedor-completados/tabla/index') }}/" + anioActual + "/" + 0; // 0: TODOS LOS MESES
            $('#tablaDatatable').load(ruta);
        }


    </script>


@endsection
