<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 15%">Descripción</th>
                                <th style="width: 4%">U/M</th>
                                <th style="width: 4%">Cantidad Solicitada</th>
                                <th style="width: 4%">Prioridad</th>
                                <th style="width: 4%">Cantidad Recibida</th>
                                <th style="width: 4%">Estado</th>
                                <th style="width: 6%">Observación</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr>
                                    <td style="width: 15%">{{ $dato->nombre }}</td>
                                    <td style="width: 4%">{{ $dato->unimedida }}</td>
                                    <td style="width: 4%">{{ $dato->cantidad }}</td>
                                    <td style="width: 4%">{{ $dato->nombrePrioridad }}</td>
                                    <td style="width: 4%">{{ $dato->cantidad_entregada }}</td>
                                    <td style="width: 4%">
                                        @if($dato->estado == 1)
                                            <span class="badge bg-gray-dark">{{ $dato->nombreEstado }}</span>
                                        @elseif($dato->estado == 2)
                                            <span class="badge bg-success">{{ $dato->nombreEstado }}</span>
                                        @elseif($dato->estado == 3)
                                            <span class="badge bg-warning">{{ $dato->nombreEstado }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $dato->nombreEstado }}</span>
                                        @endif
                                    </td>
                                    <td style="width: 6%">{{ $dato->nota }} </td>

                                </tr>
                            @endforeach

                            <script>
                                setTimeout(function () {
                                    closeLoading();
                                }, 1000);
                            </script>

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
            "order": [[0, 'desc']],
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
