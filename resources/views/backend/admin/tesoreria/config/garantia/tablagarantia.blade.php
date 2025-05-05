<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 50%">Garantía</th>
                                <th style="width: 8%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>
                                        <button type="button" style="font-weight: bold; color: white !important;"
                                                class="button button-primary button-rounded button-pill button-small" onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
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
