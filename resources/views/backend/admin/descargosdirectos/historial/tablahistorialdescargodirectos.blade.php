<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla-requisicion" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 12%">Fecha</th>
                                <th style="width: 15%">Tipo Descargo</th>
                                <th style="width: 15%">Número Orden</th>
                                <th style="width: 15%">Número Orden</th>
                                <th style="width: 12%">Monto Descontar</th>
                                <th style="width: 10%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)

                                <tr>
                                    <td style="width: 4%">{{ $dato->fecha }}</td>
                                    <td style="width: 10%">{{ $dato->tipodescargo }}</td>
                                    <td style="width: 10%">{{ $dato->numero_orden }}</td>
                                    <td style="width: 10%">{{ $dato->numero_acuerdo }}</td>
                                    <td style="width: 10%">{{ $dato->montodescontar }}</td>

                                    <td>
                                        <button type="button" class="btn btn-warning btn-xs" onclick="informacion({{ $dato }})">
                                            <i class="fas fa-edit" title="Revisar"></i>&nbsp; Revisar
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
        $("#tabla-requisicion").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "simple",
            "lengthMenu": [[10, 25, 50, 100, 150, -1], [10, 25, 50, 100, 150, "Todo"]],
            "language": {

                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Total _TOTAL_ registros",
                "sInfoEmpty": "Total 0 registros",
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
