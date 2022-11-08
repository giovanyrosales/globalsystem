<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 20%">Proyecto Cod.</th>
                                <th style="width: 10%">Num. de Orden</th>
                                <th style="width: 17%">Requi. Destino</th>
                                <th style="width: 17%">Cotización #</th>
                                <th style="width: 17%">Proveedor</th>
                                <th style="width: 12%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)

                                <tr>

                                    <td>{{ $dato->proyecto_cod }}</td>
                                    <td>{{ $dato->id }}</td>
                                    <td>{{ $dato->requidestino }}</td>
                                    <td>{{ $dato->cotizacion_id }}</td>
                                    <td>{{ $dato->nomproveedor }}</td>
                                    <td>

                                        <!-- ORDEN NO ANULADA -->
                                            <button type="button" class="btn btn-primary btn-xs" onclick="verProcesadas({{ $dato->cotizacion_id }})">
                                                <i class="fas fa-list-alt" title="Detalle"></i>&nbsp; Detalle
                                            </button>

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
</section>

<script>
    $(function () {
        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",
            "lengthMenu": [[10, 25, 50, 100, 150, -1], [10, 25, 50, 100, 150, "Todo"]],
            "language": {

                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }

            },
            "responsive": true, "lengthChange": true, "autoWidth": false,
        });
    });


</script>
