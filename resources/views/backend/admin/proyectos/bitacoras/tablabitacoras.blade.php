<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla-bitabora" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10%">Número</th>
                                <th style="width: 10%">Fecha</th>
                                <th style="width: 18%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listaBitacora as $dato)
                                <tr>
                                    <td>{{ $dato->numero }}</td>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-xs" onclick="vistaBitacora({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Ver"></i>&nbsp; Fotos
                                        </button>

                                        <button type="button" class="btn btn-success btn-xs" onclick="vistaEditarBitacora({{ $dato->id }})">
                                            <i class="fas fa-pen" title="Editar"></i>&nbsp; Editar
                                        </button>

                                        <button type="button" class="btn btn-danger btn-xs" onclick="preguntaBorrarBitacora({{ $dato->id }})">
                                            <i class="fas fa-trash-alt" title="Borrar"></i>&nbsp; Borrar
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
        $("#tabla-bitabora").DataTable({
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
