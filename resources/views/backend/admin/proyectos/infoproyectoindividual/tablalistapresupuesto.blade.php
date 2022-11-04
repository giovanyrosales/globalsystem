<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10%">Item</th>
                                <th style="width: 30%">Nombre</th>
                                <th style="width: 8%">Monto</th>
                                <th style="width: 12%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($partida as $dato)
                                <tr>
                                    <td>{{ $dato->item }}</td>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>${{ $dato->montopartida }}</td>
                                    <td>
                                        @if($presuaprobado == 0)
                                            <button type="button" class="btn btn-primary btn-xs" onclick="informacionPresupuesto({{ $dato }})">
                                                <i class="fas fa-pen" title="Editar"></i>&nbsp; Editar
                                            </button>
                                            <br> <br>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="infoBorrar({{ $dato->id }})">
                                                <i class="fas fa-trash-alt" title="Borrar"></i>&nbsp; Borrar
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-primary btn-xs" onclick="informacionPresupuesto({{ $dato }})">
                                                <i class="fas fa-eye" title="Ver"></i>&nbsp; Ver
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
            "pagingType": "simple",
            "lengthMenu": [[5, 25, 50, 100, 150, -1], [5, 25, 50, 100, 150, "Todo"]],
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
