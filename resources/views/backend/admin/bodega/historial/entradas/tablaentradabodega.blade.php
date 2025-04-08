

<section class="content">
    <div class="container-fluid">
        <div class="row">

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 5%">Fecha Ingreso</th>
                                <th style="width: 4%">Lote</th>
                                <th style="width: 20%">Observación</th>
                                <th style="width: 6%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr>
                                    <td style="width: 5%">{{ $dato->fecha }}</td>
                                    <td style="width: 4%">{{ $dato->lote }}</td>
                                    <td style="width: 20%">{{ $dato->observacion }}</td>
                                    <td style="width: 6%">
                                        <button type="button" class="btn btn-warning btn-xs"
                                                onclick="vistaDetalle2({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Datos"></i>&nbsp; Datos
                                        </button>

                                        <button type="button" style="margin: 3px"  class="btn btn-info btn-xs"
                                                onclick="vistaDetalle({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Detalle"></i>&nbsp; Detalle
                                        </button>

                                        <button style="margin: 3px" type="button" class="btn btn-danger btn-xs"
                                                onclick="infoBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
                                        </button>

                                        <button style="margin: 3px" type="button" class="btn btn-warning btn-xs"
                                                onclick="infoNuevoIngreso({{ $dato->id }})">
                                            <i class="fas fa-plus" title="Nuevo Ingreso"></i>&nbsp; Nuevo Ingreso
                                        </button>
                                    </td>
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
    $.fn.dataTable.ext.type.order['date-dd-mm-yyyy-pre'] = function (date) {
        var parts = date.split('-'); // Dividimos por guiones
        return new Date(parts[2], parts[1] - 1, parts[0]).getTime(); // Convertimos a timestamp
    };

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
