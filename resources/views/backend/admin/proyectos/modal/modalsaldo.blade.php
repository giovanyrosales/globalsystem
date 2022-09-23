<html>
<head>
    <meta charset="UTF-8" />
    <title>Alcaldía Metapán | Reporte</title>

    <style>
        .firma {
            left: 0;
            font-size: 20px;
            margin-top: 200px;
            text-align: left;
            page-break-inside: avoid;
        }

        br[style] {
            display:none;
        }

        #titulo{
            text-align: center;
            font-size: 18px;
            font-weight: bold;"
        }

        #logo{
            float: right;
            height : 88px;
            width : 71px;
            margin-right: 15px;
        }

        #tablaFor {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 35px;
            text-align: center;
        }

        #tablaFor td{
            border: 1px solid #1E1E1E;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        #tablaFor th {
            border: 1px solid #1E1E1E;
            padding: 8px;
            text-align: center;
        }

        #tablaFor th {
            padding-top: 12px;
            padding-bottom: 12px;
            color: #1E1E1E;
            text-align: left;
            font-size: 14px;
        }




    </style>
</head>

<body>
<div id="header">
    <div class="content">
        <p id="titulo">ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Saldo de Cuenta<br>
    </div>
</div>


<table id="tablaFor" style="width: 95%">

    <tbody>
    <tr>
        <td width="10%" style="font-weight: bold">Código</td>
        <td width="30%" style="font-weight: bold">Objeto</td>
        <td width="20%" style="font-weight: bold">Saldo Inicial</td>
        <td width="20%" style="font-weight: bold">Saldo Restante</td>
        <td width="20%" style="font-weight: bold">Saldo Retenido <i class="fas fa-question-circle" onclick="infoSaldoRetenido()"></i></td>
    </tr>

    @foreach($presupuesto as $dd)

        <tr>
            <td width="10%" style="font-weight: bold">{{ $dd->codigo }}</td>
            <td width="30%" style="font-weight: bold">{{ $dd->nombre }}</td>
            <td width="20%" style="font-weight: bold">${{ $dd->saldo_inicial }}</td>
            @if($dd->totalRetenido >= $dd->saldoRestante)
                <td width="20%" style="font-weight: bold; color: white; background: red">${{ $dd->saldo_restante }}</td>
                <td width="20%" style="font-weight: bold; color: white; background: red">${{ $dd->total_retenido }}</td>
            @else
                <td width="20%" style="font-weight: bold">${{ $dd->saldo_restante }}</td>
                <td width="20%" style="font-weight: bold">${{ $dd->total_retenido }}</td>
            @endif

        </tr>

    @endforeach

    </tbody>

</table>


<br>
<br>
<!-- partida de mano de obra -->
<div class="modal-footer justify-content-between">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>

<script>

    function infoSaldoRetenido(){
        Swal.fire({
            title: 'Saldo Retenido',
            text: "Se retiene el Saldo al hacer una requisición. Se libera el Saldo al borrar el requerimiento completo o un material",
            icon: 'question',
            showCancelButton: false,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {

            }
        })
    }


</script>

</body>
</html>

