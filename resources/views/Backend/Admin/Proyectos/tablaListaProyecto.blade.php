<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10%">Código</th>
                                <th style="width: 35%">Nombre</th>
                                <th style="width: 10%">Fecha Inicio</th>
                                <th style="width: 17%">Encargado</th>
                                <th style="width: 12%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)
                                <tr>
                                    <td>{{ $dato->codigo }}</td>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->fechaini }}</td>
                                    <td>{{ $dato->encargado }}</td>
                                    <td>
                                        @can('boton.ver.proyecto')
                                        <button type="button" class="btn btn-warning btn-xs" onclick="vista({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Ver"></i>&nbsp; Ver
                                        </button>
                                        @endcan

                                        @can('boton.editar.proyecto')
                                        <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-pen" title="Editar"></i>&nbsp; Editar
                                        </button>
                                        @endcan

                                        @can('boton.ver.presupuesto')
                                                <br><br>
                                                <button type="button" class="btn btn-info btn-xs" onclick="informacionPresupuesto({{ $dato->id }})">
                                                    <i class="fas fa-eye" title="Presupuesto"></i>&nbsp; Presupuesto
                                                </button>
                                                <span class="badge bg-success">Presupuesto Aprobado</span>
                                        @endcan

                                        @can('boton.ver.planilla')
                                            <br><br>
                                            <button type="button" class="btn btn-info btn-xs" onclick="informacionPlanilla({{ $dato->id }})">
                                                <i class="fas fa-eye" title="Planilla"></i>&nbsp; Planilla
                                            </button>
                                        @endcan

                                        <!-- administradores de proyecto pueden ver unicamente el presupuesto -->

                                                <button type="button" class="btn btn-info btn-xs" onclick="verPresupuestoPorAdministrador({{ $dato->id }})">
                                                    <i class="fas fa-eye" title="Presupuesto"></i>&nbsp; Presupuestox
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
