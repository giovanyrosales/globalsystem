<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 35%">Proyecto</th>
                                <th style="width: 10%">Num. de Orden</th>
                                <th style="width: 17%">Requisición</th>
                                <th style="width: 17%">Cotización</th>
                                <th style="width: 12%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)

                                @if($dato->estado == 1)
                                    <tr class="table-danger">
                                    @else
                                    <tr>
                                @endif

                                    <td>{{ $dato->proyecto_cod }}</td>
                                    <td>{{ $dato->id }}</td>
                                    <td>{{ $dato->requisicion_id }}</td>
                                    <td>{{ $dato->cotizacion_id }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-xs" onclick="Imprimir({{ $dato->id }})">
                                            <i class="fa fa-print" title="Generar Acta"></i>&nbsp; Imprimir
                                        </button>

                                        @if($dato->estado == 0)
                                        <button type="button" class="btn btn-danger btn-xs" onclick="abrirModalAnular({{ $dato->id }})">
                                            <i class="fas fa-trash-alt" title="Anular"></i>&nbsp; Anular
                                        </button>
                                        @endif

                                        @if($dato->actaid == 0)
                                            <button type="button" class="btn btn-warning btn-xs" onclick="abrirModalActa({{ $dato->id }})">
                                                <i class="fa fa-laptop" title="Generar Acta"></i>&nbsp; Generar Acta
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-info btn-xs" onclick="imprimirActa({{ $dato->actaid }})">
                                                <i class="fas fa-print" title="Imprimir Acta"></i>&nbsp; Imprimir Acta
                                            </button>
                                        @endif

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
