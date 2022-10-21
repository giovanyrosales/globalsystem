<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 6%">Item</th>
                                <th style="width: 10%">Tipo Partida</th>
                                <th style="width: 13%">Nombre</th>
                                <th style="width: 10%">Cantidad Partida</th>
                                <th style="width: 10%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)
                            <tr>
                                <td style="width: 6%">{{ $dato->item }}</td>
                                <td style="width: 10%">{{ $dato->tipopartida }}</td>
                                <td style="width: 13%">{{ $dato->nombre }}</td>
                                <td style="width: 10%">{{ $dato->cantidadp }}</td>
                                <td style="width: 10%">
                                    @if($infoContenedor->estado == 0)
                                        <button type="button" class="btn btn-primary btn-xs" onclick="informacionPresupuesto({{ $dato }})">
                                            <i class="fas fa-pen" title="Editar"></i>&nbsp; Editar
                                        </button>
                                        <br> <br>
                                        <button type="button" class="btn btn-danger btn-xs" onclick="infoBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash-alt" title="Borrar"></i>&nbsp; Borrar
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-xs" onclick="informacionPresupuesto({{ $dato->id }})">
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
