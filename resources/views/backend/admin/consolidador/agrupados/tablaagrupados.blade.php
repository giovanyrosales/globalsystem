<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Fecha Agrupado</th>
                                <th>Destino</th>
                                <th>Justificación</th>
                                <th>Administrador</th>
                                <th>Evaluador</th>

                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>{{ $dato->nombreodestino }}</td>
                                    <td>{{ $dato->justificacion }}</td>
                                    <td>{{ $dato->nomadmin }}</td>
                                    <td>{{ $dato->nomevaluador }}</td>

                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="informacionPdf({{ $dato->id }})">
                                            <i class="fas fa-file-pdf" title="PDF"></i>&nbsp; PDF
                                        </button>

                                        @if($dato->btnborrar == 1)
                                            <br><br>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="informacionBorrar({{ $dato->id }})">
                                                <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
                                            </button>
                                        @endif

                                        @if($dato->btnborrar == 1)
                                            <br><br>
                                            <button type="button" class="btn btn-primary btn-xs" onclick="informacionEditar({{ $dato->id }})">
                                                <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
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
