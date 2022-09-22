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
            Materiales del Presupuesto<br>
    </div>
</div>


<table id="tablaFor" style="width: 95%">

    <tbody>
    <tr>
        <td width="15%">Código</td>
        <td width="15%">Obj. Espec.</td>
        <td width="30%">Material</td>
        <td width="15%">Medida</td>
        <td width="10%">Cantidad</td>
        <td width="15%">Precio Actual</td>
    </tr>

    @foreach($presupuesto as $dd)

        <tr>
            <td width="15%">{{ $dd->objcodigo }}</td>
            <td width="15%">{{ $dd->objnombre }}</td>
            <td width="30%">{{ $dd->nombre }}</td>
            <td width="15%">{{ $dd->medida }}</td>
            <td width="10%">{{ $dd->cantidad }}</td>
            <td width="15%">${{ $dd->actual }}</td>
        </tr>

    @endforeach

    </tbody>

</table>


<br>
<br>
<!--  -->
<div class="modal-footer justify-content-between">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>


</body>
</html>

