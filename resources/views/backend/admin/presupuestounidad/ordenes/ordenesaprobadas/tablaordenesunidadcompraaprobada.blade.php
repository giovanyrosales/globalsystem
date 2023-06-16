<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10%"># de Orden</th>
                                <th style="width: 17%"># de Cotización</th>
                                <th style="width: 17%">Fecha de Orden</th>
                                <th style="width: 12%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($arrayOrdenUnidad as $dato)

                                <tr>
                                    <td>{{ $dato->id }}</td>
                                    <td>{{ $dato->id_cotizacion }}</td>
                                    <td>{{ $dato->fecha_orden }}</td>
                                    <td>

                                        <button type="button" class="btn btn-primary btn-xs" onclick="verDetalles({{ $dato->id_cotizacion }})">
                                            <i class="fas fa-list-alt" title="Detalle"></i>&nbsp; Detalle
                                        </button>

                                        <br> <br>
                                        <button type="button" class="btn btn-success btn-xs" onclick="Imprimir({{ $dato->id }})">
                                            <i class="fa fa-print" title="Generar Acta"></i>&nbsp; Imprimir Orden
                                        </button>

                                        @if($dato->hayActa == 0)
                                            <br><br>
                                            <button type="button" class="btn btn-warning btn-xs" onclick="generarActta({{ $dato->id }})">
                                                <i class="fa fa-file-pdf" title="Generar Acta"></i>&nbsp; Generar Acta
                                            </button>
                                        @else
                                            <br><br>
                                            <button type="button" class="btn btn-info btn-xs" onclick="imprimirActa({{ $dato->idActa }})">
                                                <i class="fas fa-print" title="Imprimir Acta"></i>&nbsp; Imprimir Acta
                                            </button>
                                        @endif

                                    </td>
                                </tr>

                                    @if($loop->last)
                                        <script>
                                            setTimeout(function () {
                                                closeLoading();
                                            }, 1000);
                                        </script>
                                    @endif

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
