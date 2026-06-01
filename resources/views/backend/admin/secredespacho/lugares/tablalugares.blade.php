<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 5%">ID</th>
                                <th style="width: 30%">Nombre</th>
                                <th style="width: 40%">Descripción</th>
                                <th style="width: 10%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lugares as $dato)
                                <tr>
                                    <td>{{ $dato->id }}</td>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->descripcion }}</td>
                                    <td>

                                        <button type="button"
                                                style="font-weight: normal; color: white !important;"
                                                class="btn btn-primary btn-xs"
                                                onclick="informacionLugar({{ $dato->id }})">

                                            <i class="fas fa-edit"></i>&nbsp; Editar
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

            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Todo"]
            ],

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
                },

                "oAria": {
                    "sSortAscending": ": Activar para ordenar ascendente",
                    "sSortDescending": ": Activar para ordenar descendente"
                }
            },

            "responsive": true

        });

    });

</script>
