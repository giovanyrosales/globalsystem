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

        #tabla {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 35px;
            text-align: center;
        }

        #tabla td{
            border: 1px solid #1E1E1E;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        #tabla th {
            border: 1px solid #1E1E1E;
            padding: 8px;
            text-align: center;
        }

        #tabla th {
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
        <img id="logo" src="{{ asset('images/logo2.png') }}">
        <p id="titulo">ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Fondo: {{ $fuenter }} <br>
            Hoja de presupuesto <br>
            Fecha: {{ $mes }} <br>
    </div>
</div>


<table id="tabla" style="width: 95%">

        <tbody>
        <tr>
            <th colspan="6">{{ $nombrepro }}</th>
        </tr>
        <tr>
            <td colspan="2">Item</td>
            <td colspan="2">Partida</td>
            <td colspan="2">Cantidad</td>
        </tr>

        @foreach($partida1 as $dd)

            <tr>
                <td colspan="2">{{ $dd->item }}</td>
                <td colspan="2">{{ $dd->nombre }}</td>
                <td colspan="2">{{ $dd->cantidadp }}</td>
            </tr>

            <tr>
                <td width="15%">Material</td>
                <td width="15%">Medida</td>
                <td width="15%">Cantidad</td>
                <td width="15%">P.U</td>
                <td width="15%">Sub Total</td>
                <td width="15%">Total</td>
            </tr>

            @foreach($dd->bloque1 as $gg)

                <tr>
                    <td width="15%">{{ $gg->material }}</td>
                    <td width="15%">{{ $gg->medida }}</td>
                    <td width="15%">{{ $gg->cantidad }}</td>
                    <td width="15%">{{ $gg->pu }}</td>
                    <td width="15%">{{ $gg->subtotal }}</td>
                    <td width="15%"></td>
                </tr>

                @if($loop->last)
                    <tr>
                        <td width="15%"></td>
                        <td width="15%"></td>
                        <td width="15%"></td>
                        <td width="15%"></td>
                        <td width="15%"></td>
                        <td width="15%">{{ $dd->total }}</td>
                    </tr>
                @endif

            @endforeach

        @endforeach


        </tbody>







</table>



</body>
</html>
