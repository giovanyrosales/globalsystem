@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
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
                    <h1>Editar Presupuesto</h1>
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

    <div class="modal fade" id="modalBuscador">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group">
                                            <div class="input-group mb-3" style="width: 40%;">
                                                <input type="text" class="form-control" autocomplete="off" maxlength="100" id="nombre-material" placeholder="Nombre del Material a Buscar...">
                                                <span class="input-group-append">
                                                <button type="button" class="btn btn-info btn-flat" onclick="buscarMaterial()">BUSCAR</button>
                                              </span>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 20px">
                                            <table class="table" id="matriz-material"  data-toggle="table">
                                                <thead>
                                                <tr>
                                                    <th style="width: 20%">RUBRO</th>
                                                    <th style="width: 20%">CUENTA</th>
                                                    <th style="width: 20%">OBJETO ESPE.</th>
                                                    <th style="width: 40%">MATERIAL</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
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

    <!-- PROYECTOS SOLICITUD -->
    <div class="modal fade" id="modalNuevoProyecto">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitud de Proyecto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo-proyecto">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Descripción</label>
                                            <input type="text" class="form-control" autocomplete="off" maxlength="300" id="proyecto-descripcion-nuevo" placeholder="Nombre">
                                        </div>

                                        <div class="form-group">
                                            <label>Monto ($)</label>
                                            <input type="number" class="form-control" autocomplete="off" id="proyecto-costo-nuevo" placeholder="0.00">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificarNuevoProyecto()">Agregar</button>
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
            openLoading();

            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/p/editar/presupuesto/anio/contenedor') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function modalBuscarMaterial(){
            $('#modalBuscador').modal('show');
        }

        function buscarMaterial(){

            var nombre = document.getElementById("nombre-material").value;

            if(nombre === ''){
                toastr.error('Nombre Material es Requerido');
                return;
            }

            if(nombre.length < 3){
                toastr.error('Mínimo 3 Caracteres para Buscar');
                return;
            }

            openLoading();
            $("#matriz-material tbody tr").remove();

            axios.post(url+'/p/buscar/material/presupuesto', {
                'texto' : nombre
            })
                .then((response) => {

                    closeLoading();

                    if(response.data.success === 1){

                        if(response.data.conteo){

                            let infodetalle = response.data.info;

                            for (var i = 0; i < infodetalle.length; i++) {

                                var markup = "<tr>" +

                                    "<td>" +
                                    "<input class='form-control' value='" + infodetalle[i].rubro + "' disabled type='text'>" +
                                    "</td>" +

                                    "<td>" +
                                    "<input class='form-control' value='" + infodetalle[i].cuenta + "' disabled type='text'>" +
                                    "</td>" +

                                    "<td>" +
                                    "<input class='form-control' value='" + infodetalle[i].objeto + "' disabled type='text'>" +
                                    "</td>" +

                                    "<td>" +
                                    "<input class='form-control' style='background-color: #b0f2c2' value='" + infodetalle[i].descripcion + "' disabled type='text'>" +
                                    "</td>" +

                                    "</tr>";

                                $("#matriz-material tbody").append(markup);
                            }
                        }else{
                            toastr.info('Material No Encontrado');
                        }
                    }else{
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                });
        }

        function modalNuevaSolicitud(){
            document.getElementById("formulario-nuevo-material").reset();
            $('#select-medida-nuevo').prop('selectedIndex', 0).change();
            $('#modalNuevoMaterial').modal('show');
        }

        function verificar(){
            Swal.fire({
                title: 'Actualizar Presupuesto',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    editarPresupuesto();
                }
            })
        }


        function verificarNuevoMaterial(){

            var material = document.getElementById('material-nuevo').value;
            var costo = document.getElementById('costo-nuevo').value;
            var cantidad = document.getElementById('cantidad-nuevo').value;
            var periodo = document.getElementById('periodo-nuevo').value;
            var medida = document.getElementById('select-medida-nuevo').value;

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;
            var reglaNumeroEntero = /^[0-9]\d*$/;

            // ****

            if(material === ''){
                toastr.error('Material es requerido');
                return;
            }

            if(material.length > 300){
                toastr.error('Material máximo 300 caracteres');
                return;
            }

            // ****

            if(costo === ''){
                toastr.error('Costo es requerido');
                return;
            }

            if(!costo.match(reglaNumeroDosDecimal)) {
                toastr.error('Costo debe ser número Decimal Positivo. Solo se permite 2 Decimales');
                return;
            }

            if(costo < 0){
                toastr.error('Costo no permite números negativos');
                return;
            }

            if(costo > 99000000){
                toastr.error('Costo máximo 99 millones');
                return;
            }

            // ****

            if(cantidad === ''){
                toastr.error('Cantidad es requerido');
                return;
            }

            if(!cantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser número Entero y No Negativos');
                return;
            }

            if(cantidad <= 0){
                toastr.error('Cantidad no permite números negativos y Ceros');
                return;
            }

            if(cantidad > 99000000){
                toastr.error('Cantidad máximo 99 millones');
                return;
            }

            // ****

            if(periodo === ''){
                toastr.error('Periodo es requerido');
                return;
            }

            if(!periodo.match(reglaNumeroEntero)) {
                toastr.error('Periodo debe ser número Entero y No Negativos');
                return;
            }

            if(periodo < 0){
                toastr.error('Periodo no permite números negativos');
                return;
            }

            if(periodo > 999){
                toastr.error('Periodo máximo Número 999 de límite');
                return;
            }

            // ****

            if(medida === ''){
                toastr.error('Unidad Medida es requerido');
                return;
            }

            var texto = $("#select-medida-nuevo option:selected").text();

            var markup = "<tr>"+

                "<td>"+
                "<input name='descripcionfila[]' maxlength='300' value='"+ material +"' disabled class='form-control' type='text'>"+
                "</td>"+

                "<td>"+
                "<input name='unidadmedidafila[]' value='"+texto+"' class='form-control' disabled data-infomedida='"+medida+"' type='text'/>"+
                "</td>"+

                "<td>"+
                "<input name='costoextrafila[]' value='"+costo+"' disabled class='form-control'  type='text'/>"+
                "</td>"+

                "<td>"+
                "<input name='cantidadextrafila[]' value='"+cantidad+"' disabled class='form-control' />"+
                "</td>"+

                "<td>"+
                "<input name='periodoextrafila[]' value='"+periodo+"' disabled class='form-control'/>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matrizMateriales tbody").append(markup);

            $('#modalNuevoMaterial').modal('hide');
        }


        function mostrarBloque(){
            document.getElementById("bloque-codigo").style.display = "block";
        }

        function ocultarBloque(){
            document.getElementById("bloque-codigo").style.display = "none";
        }

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
        }


        //********************* PROYECTOS ***********************************

        function borrarFilaProyecto(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
        }


        function modalNuevaSolicitudProyecto(){
            document.getElementById("formulario-nuevo-proyecto").reset();
            $('#modalNuevoProyecto').modal('show');
        }


        function verificarNuevoProyecto(){

            var descripcion = document.getElementById('proyecto-descripcion-nuevo').value;
            var costo = document.getElementById('proyecto-costo-nuevo').value;

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            // ****

            if(descripcion === ''){
                toastr.error('Descripción es requerido');
                return;
            }

            if(descripcion.length > 300){
                toastr.error('Descripción máximo 300 caracteres');
                return;
            }

            // ****

            if(costo === ''){
                toastr.error('Costo es requerido');
                return;
            }

            if(!costo.match(reglaNumeroDosDecimal)) {
                toastr.error('Costo debe ser número Decimal Positivo. Solo se permite 2 Decimales');
                return;
            }

            if(costo < 0){
                toastr.error('Costo no permite números negativos');
                return;
            }

            if(costo > 99000000){
                toastr.error('Costo máximo 99 millones de límite');
                return;
            }

            var markup = "<tr>"+

                "<td>"+
                "<input name='proyectodescripcionfila[]' maxlength='300' value='"+ descripcion +"' disabled class='form-control' type='text'>"+
                "</td>"+

                "<td>"+
                "<input name='proyectocostoextrafila[]' value='"+costo+"' disabled class='form-control' type='text'/>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaProyecto(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matrizProyectos tbody").append(markup);

            $('#modalNuevoProyecto').modal('hide');
        }







    </script>


@endsection
