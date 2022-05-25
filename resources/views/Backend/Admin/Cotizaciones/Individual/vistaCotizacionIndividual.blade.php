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
                <h1>Cotización</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Cotizaciones Pendientes</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Cotizaciones Pendientes</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">

                            <div class="form-group">
                                <label>Destino</label>
                                <input type="text" value="{{ $info->destino }}" class="form-control" disabled>
                            </div>
                            <div class="form-group">
                                <label>Necesidad</label>
                                <textarea class="form-control" rows="3" disabled>{{ $info->necesidad }}</textarea>
                            </div>
                        </div>

                        <div class="col-sm-5" style="margin-left: 25px">

                            <div class="form-group">
                                <label>Proveedor</label>
                                <input type="text" value="{{ $proveedor->nombre }}" class="form-control" disabled>
                            </div>

                            <div class="form-group">
                                <label>Fecha de Cotización</label>
                                <input type="text" value="{{ $info->fecha }}" class="form-control" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <table class="table" id="matriz" data-toggle="table">
                            <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th style="width: 6%">Cantidad</th>
                                <th style="width: 15%">Descripción</th>
                                <th style="width: 5%">Precio Unitario</th>
                                <th style="width: 5%">Cod. Presup</th>
                                @if ($estado == 0 )
                                <th style="width: 5%">Opciones</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($detalle as $dd)
                                <tr id="{{ $dd->id }}">
                                    <td><p id="fila{{$dd->conteo}}" class='form-control'>{{$dd->conteo}}</p></td>
                                    <td><input name="cantidadarray[]" class="form-control" type="number" value="{{$dd->cantidad}}"></td>
                                    <td><input disabled class="form-control" type="text" value="{{$dd->descripcion}}"></td>
                                    <td><input name="preciounitarioarray[]" class="form-control" value="{{$dd->precio_u }}"></td>
                                    <td><input name="codpresuparray[]" class="form-control" type="number" value="{{$dd->cod_presup }}"></td>
                                    @if ($estado == 0 )
                                    <td><button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button></td>
                                    @endif
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-8">
                            <a class="btn btn-info mt-3 float-left" href= "javascript:history.back()" target="frameprincipal">
                                <i title="Cancelar"></i> Cancelar </a>
                        </div>

                        <div class="col-md-4">
                            @if ($estado == 0 )
                                <button type="button" style="margin-left: 25px" class="btn btn-warning float-right mt-3"  onclick="actualizarCotizacion()">Actualizar Cotización</button>
                                <button type="button" class="btn btn-danger float-right mt-3" onclick="autorizarCotizacion()">Autorizar Cotización</button>
                                <button type="button" class="btn btn-danger float-right mt-3" onclick="denegarCotizacion()">Denegar Cotización</button>
                            @endif
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

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFilaConteo();
        }

        function setearFilaConteo(){

            var table = document.getElementById('matriz');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        function actualizarCotizacion(){
            Swal.fire({
                title: 'Actualizar Cotización',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarActualizacion();
                }
            })
        }

        function autorizarCotizacion(){
            Swal.fire({
                title: 'Autorizar Cotización',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarAutorizacion();
                }
            })
        }

        function denegarCotizacion(){
            Swal.fire({
                title: 'Denegar Cotización',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarDenegada();
                }
            })
        }

        function verificarAutorizacion(){
            // id de cotizacion
            var id = {{ $id }};

            openLoading();

            axios.post(url+'/cotizacion/autorizar', {
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        Swal.fire({
                            title: 'Cotización Autorizada',
                            text: "",
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href="{{ url('/admin/cotizacion/autorizadas/index') }}";
                            }
                        })
                    }
                    else{
                        toastr.error('error al autorizar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al autorizar');
                    closeLoading();
                });
        }

        function verificarDenegada(){
            // id de cotizacion
            var id = {{ $id }};

            openLoading();

            axios.post(url+'/cotizacion/denegar', {
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        Swal.fire({
                            title: 'Cotización Denegada',
                            text: "",
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href="{{ url('/admin/cotizacion/denegadas/index') }}";
                            }
                        })
                    }
                    else{
                        toastr.error('error al autorizar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al autorizar');
                    closeLoading();
                });
        }


        function verificarActualizacion(){
            var hayRegistro = 0;
            var nRegistro = $('#matriz >tbody >tr').length;
            let formData = new FormData();

            var idcoti = {{ $id }};

            if (nRegistro > 0){

                colorBlancoTabla();

                var cantidad = $("input[name='cantidadarray[]']").map(function(){return $(this).val();}).get();
                var preciounitario = $("input[name='preciounitarioarray[]']").map(function(){return $(this).val();}).get();
                var codpresup = $("input[name='codpresuparray[]']").map(function(){return $(this).val();}).get();

                var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;
                var reglaNumeroEntero = /^[0-9]\d*$/;

                for(var a = 0; a < cantidad.length; a++){

                    let datoCantidad = cantidad[a];

                    if(datoCantidad === ''){
                        colorRojoTabla(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad es requerida');
                        return;
                    }

                    if(!datoCantidad.match(reglaNumeroDecimal)) {
                        colorRojoTabla(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad debe ser decimal y no negativo');
                        return;
                    }

                    if(datoCantidad <= 0){
                        colorRojoTabla(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad no debe ser negativo');
                        return;
                    }

                    if(datoCantidad.length > 10){
                        colorRojoTabla(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad máximo 10 caracteres');
                        return;
                    }
                }

                for(var b = 0; b < preciounitario.length; b++){

                    let datoPrecio = preciounitario[b];

                    if(datoPrecio === ''){
                        colorRojoTabla(b);
                        toastr.error('Fila #' + (b+1) + ' Precio Unitario es requerida');
                        return;
                    }

                    if(!datoPrecio.match(reglaNumeroDecimal)) {
                        colorRojoTabla(b);
                        toastr.error('Fila #' + (b+1) + ' Precio Unitario debe ser decimal y no negativo');
                        return;
                    }

                    if(datoPrecio <= 0){
                        colorRojoTabla(b);
                        toastr.error('Fila #' + (b+1) + ' Precio Unitario no debe ser negativo');
                        return;
                    }

                    if(datoPrecio.length > 10){
                        colorRojoTabla(b);
                        toastr.error('Fila #' + (b+1) + ' Precio Unitario máximo 10 caracteres');
                        return;
                    }
                }

                for(var c = 0; c < codpresup.length; c++){

                    var datoCodigo = codpresup[c];

                    if(datoCodigo === ''){
                        colorRojoTabla(c);
                        toastr.error('Fila #' + (c+1) + ' Código de Presupuesto es requerido');
                        return;
                    }

                    if(!datoCodigo.match(reglaNumeroEntero)) {
                        colorRojoTabla(c);
                        toastr.error('Fila #' + (c+1) + ' Código de Presupuesto debe ser Entero y no negativo');
                        return;
                    }

                    if(datoCodigo <= 0){
                        colorRojoTabla(c);
                        toastr.error('Fila #' + (c+1) + ' Código de Presupuesto no debe ser negativo');
                        return;
                    }

                    if(datoCodigo.length > 10){
                        colorRojoTabla(c);
                        toastr.error('Fila #' + (c+1) + ' Código de Presupuesto máximo 10 caracteres');
                        return;
                    }
                }

                // como tienen la misma cantidad de filas, podemos recorrer
                // todas las filas de una vez
                for(var p = 0; p < cantidad.length; p++){
                    // obtener el id de la fila
                    var id = $("#matriz tr:eq("+(p+1)+")").attr('id');
                    formData.append('idarray[]', id);
                    formData.append('cantidadarray[]', cantidad[p]);
                    formData.append('preciounitarioarray[]', preciounitario[p]);
                    formData.append('codpresuparray[]', codpresup[p]);
                }

                hayRegistro = 1;
            }

            openLoading();
            formData.append('hayregistro', hayRegistro);
            formData.append('idcotizacion', idcoti);

            axios.post(url+'/cotizacion/pendiente/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                    }
                    else if(response.data.success === 2){
                        Swal.fire({
                            title: 'La Cotización ha sido Borrada',
                            text: "",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href="{{ url('/admin/cotizacion/pendiente/index') }}";
                            }
                        })
                    }
                    else{
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al actualizar');
                    closeLoading();
                });
        }

        function colorRojoTabla(index){
            $("#matriz tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function colorBlancoTabla(){
            $("#matriz tbody tr").css('background', 'white');
        }


    </script>


@endsection
