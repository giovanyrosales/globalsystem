<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla-requisicion" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 4%">#</th>
                                <th style="width: 10%">Fecha</th>
                                <th style="width: 18%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listaRequisicion as $dato)

                                <tr>
                                    <td style="width: 4%">{{ $dato->numero }}</td>
                                    <td style="width: 10%">{{ $dato->fecha }}</td>

                                    <td>

                                        @can('boton.editar.requisicion.unidad')
                                            <button type="button" class="btn btn-info btn-xs" onclick="vistaEditarRequisicion({{ $dato }})">
                                                <i class="fas fa-pen-alt" title="Información"></i> Información
                                            </button>
                                        @endcan


                                        <button type="button" class="btn btn-success btn-xs" onclick="vistaPDFRequerimiento({{ $dato->id }})">
                                            <i class="fas fa-file-pdf" title="PDF"></i>&nbsp; PDF
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
