<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">

                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 6%">Opción</th>
                                <th style="width: 10%">CONTROL INTERNO</th>
                                <th style="width: 10%">REFERENCIA</th>
                                <th style="width: 10%">DESCRIPCION</th>
                                <th style="width: 10%">PROVEEDOR</th>
                                <th style="width: 10%">GARANTIA</th>
                                <th style="width: 10%">TIPO GARANTIA</th>
                                <th style="width: 10%">MONTO</th>
                                <th style="width: 10%">ASEGURADORA</th>
                                <th style="width: 10%">NOTA ASEGURADORA</th>

                                <th style="width: 10%">VIGENCIA DESDE</th>
                                <th style="width: 10%">VIGENCIA HASTA</th>
                                <th style="width: 10%">FECHA RECIBIDA</th>
                                <th style="width: 10%">FECHA ENTREGA</th>
                                <th style="width: 10%">FECHA ENTREGA UCP</th>

                                <th style="width: 12%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr data-info="{{ $dato->id }}">
                                    <td style="width: 6%">
                                        <input type="checkbox" class="checkbox" style="width: 40px; height: 20px" />
                                    </td>
                                    <td>{{ $dato->control_interno }}</td>
                                    <td>{{ $dato->referencia }}</td>
                                    <td>{{ $dato->descripcion_licitacion }}</td>
                                    <td>{{ $dato->proveedor }}</td>
                                    <td>{{ $dato->garantia }}</td>
                                    <td>{{ $dato->tipoGarantia }}</td>
                                    <td>{{ $dato->monto }}</td>
                                    <td>{{ $dato->tipoAseguradora }}</td>
                                    <td>{{ $dato->aseguradora }}</td>

                                    <td>{{ $dato->vigencia_desde }}</td>
                                    <td>{{ $dato->vigencia_hasta }}</td>
                                    <td>{{ $dato->fecha_recibida }}</td>
                                    <td>{{ $dato->fecha_entrega }}</td>
                                    <td>{{ $dato->fecha_entrega_ucp }}</td>

                                    <td>
                                        <button type="button"
                                                class="btn btn-primary btn-sm py-0 px-2"
                                                style="font-size: 0.80rem;"
                                                onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Editar"></i>&nbsp;Editar
                                        </button>

                                        <div style="margin: 7px">
                                            <button type="button"
                                                    class="btn btn-success btn-sm py-0 px-2"
                                                    style="font-size: 0.80rem;"
                                                    onclick="infoEstado({{ $dato->id }})">
                                                <i class="fas fa-info" title="Estado"></i>&nbsp; Estado
                                            </button>
                                        </div>

                                        <div style="margin: 7px">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm py-0 px-2"
                                                    style="font-size: 0.80rem;"
                                                    onclick="infoBorrar({{ $dato->id }})">
                                                <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>

    $(function () {
        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "order": [[0, 'desc']], // Orden descendente por fecha
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",
            "lengthMenu": [[500, -1], [500, "Todo"]],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                }
            },
            "columnDefs": [
                {
                    "targets": 0,
                    "type": "date-dd-mm-yyyy" // Usamos el tipo de fecha personalizado
                }
            ],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
        });
    });


</script>
