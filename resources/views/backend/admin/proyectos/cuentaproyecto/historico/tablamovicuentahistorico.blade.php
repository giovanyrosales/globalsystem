<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Código</th>
                                <th>Cuenta</th>
                                <th>Aumento</th>
                                <th>Disminuye</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($infoMovimiento as $dato)

                                <td>{{ $dato->fecha }}</td>
                                <td>{{ $dato->codigo }}</td>
                                <td>{{ $dato->cuenta }}</td>
                                <td>${{ $dato->aumento }}</td>
                                <td>${{ $dato->disminuye }}</td>

                                <td>
                                    @if($dato->reforma != null)
                                        <a href="{{ url('/admin/movicuentaproy/documento/'.$dato->id) }}">
                                            <button class="btn btn-success btn-xs"><i class="fa fa-download"></i> Descargar</button>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-success btn-xs" onclick="infoSubirDoc({{ $dato->id }})">
                                            <i class="fas fa-upload" title="Cargar Reforma"></i>&nbsp; Cargar Reforma
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
