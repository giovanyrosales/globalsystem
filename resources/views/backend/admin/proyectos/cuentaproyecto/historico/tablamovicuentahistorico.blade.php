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
                                <th>Cuenta Aumento</th>
                                <th>Cuenta Disminuye</th>
                                <th style="font-weight: bold">Monto</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($infoMovimiento as $dato)
                                <tr>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>{{ $dato->cuentaaumenta }}</td>
                                    <td>{{ $dato->cuentabaja }}</td>
                                    <td style="font-weight: bold">${{ $dato->dinero }}</td>

                                    @if($dato->autorizado == 0)
                                        <td><span class="badge bg-warning">Pendiente</span></td>
                                    @else
                                        <td><span class="badge bg-success">Autorizada</span></td>
                                    @endif

                                    <td>
                                            @can('boton.agregar.reforma.movimiento.cuenta')
                                                @if($dato->reforma != null)
                                                    <button href="{{ url('/admin/movicuentaproy/documento/'.$dato->id) }}" style="font-weight: bold; color: white !important;">
                                                        <button class="button button-primary button-rounded button-pill button-small"><i class="fa fa-download"></i> Descargar</button>
                                                    </button>
                                                @endif
                                            @endcan

                                            @can('boton.descargar.reforma.movimiento.cuenta')

                                                @if($dato->reforma == null && $dato->autorizado == 1)
                                                    <button type="button" style="margin-top: 5px;font-weight: bold; color: white !important;" class="button button-primary button-rounded button-pill button-small" onclick="infoSubirDoc({{ $dato->id }})">
                                                        <i class="fas fa-upload" title="Cargar Reforma"></i>&nbsp; Cargar Reforma
                                                    </button>
                                                @endif
                                            @endcan

                                            @can('boton.revision.movimiento.cuenta')

                                                    @if($dato->autorizado == 0)
                                                        <button type="button" style="margin-top: 5px; font-weight: bold; color: white !important;" class="button button-primary button-rounded button-pill button-small" onclick="infoRevisarMovimiento({{ $dato->id }})">
                                                            <i class="fas fa-check" title="Cargar Reforma"></i>&nbsp; Revisar
                                                        </button>
                                                    @endif

                                            @endcan

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
