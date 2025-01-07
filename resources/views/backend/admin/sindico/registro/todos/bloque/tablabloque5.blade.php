<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 3%">Fecha General</th>
                                <th style="width: 20%">Fecha de Emisión de Resolución</th>
                                <th style="width: 20%">Fecha de Entrega de Diligencia a Interesado</th>
                                <th style="width: 6%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr>
                                    <td style="width: 3%">{{ $dato->fecha_general }}</td>
                                    <td style="width: 20%">{{ $dato->fecha_emision_diligencia }}</td>
                                    <td style="width: 20%">{{ $dato->fecha_entrega }}</td>
                                    <td style="width: 6%">
                                        <button type="button" class="btn btn-success btn-xs"
                                                onclick="tipoVista({{ $dato->id }}, '{{ $dato->id_tiposolicitud }}')">
                                            <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
                                        </button>

                                        <button type="button" class="btn btn-danger btn-xs" onclick="infoBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
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
