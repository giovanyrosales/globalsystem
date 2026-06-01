<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-body">

                        <table id="tabla" class="table table-bordered table-striped">

                            <thead>
                            <tr>
                                <th style="width: 20%">Nombre de quien reserva</th>
                                <th style="width: 12%">Teléfono</th>
                                <th style="width: 20%">Lugar que reserva</th>
                                <th style="width: 13%">Fecha</th>
                                <th style="width: 13%">Hora inicio</th>
                                <th style="width: 12%">Hora fin</th>
                                <th style="width: 10%">Opciones</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($reservas as $dato)
                                <tr>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->telefono }}</td>
                                    <td>{{ $dato->lugar->nombre ?? 'Sin lugar' }}</td>
                                    <td>{{ $dato->fecha_formateada }}</td>
                                    <td>{{ $dato->hora_inicio_formateada }}</td>
                                    <td>{{ $dato->hora_fin_formateada }}</td>

                                    <td>
                                        <button type="button"
                                                style="font-weight: normal; color: white !important;"
                                                class="btn btn-primary btn-xs"
                                                onclick="informacionReserva({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
                                        </button>

                                        <button type="button"
                                                style="margin: 5px; font-weight: normal; color: white !important;"
                                                class="btn btn-danger btn-xs"
                                                onclick="modalBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
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

            "order": [[3, 'desc']],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",

            "lengthMenu": [
                [10, 25, 50, 100, 150, -1],
                [10, 25, 50, 100, 150, "Todo"]
            ],

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

            "responsive": true

        });

    });

</script>
