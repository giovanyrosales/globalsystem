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


        .saltopagina{page-break-after:always;}


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

        @foreach($partida1 as $dd)

            @if(!$loop->first)

                <tr>
                    <td width="100%" colspan="6"></td>
                </tr>

            @endif

            <tr>
                <td colspan="1" width="10%">Item</td>
                <td colspan="3" width="30%">Partida</td>
                <td colspan="2" width="20%">Cantidad P.</td>
            </tr>

            <tr>
                <td colspan="1" width="10%">{{ $dd->item }}</td>
                <td colspan="3" width="30%">{{ $dd->nombre }}</td>
                <td colspan="2" width="20%">{{ $dd->cantidadp }}</td>
            </tr>

            <tr>
                <td width="15%">Material</td>
                <td width="11">U/M</td>
                <td width="12%">Cantidad</td>
                <td width="10%">P.U</td>
                <td width="12%">Sub Total</td>
                <td width="20%">Total</td>
            </tr>

            @foreach($dd->bloque1 as $gg)

                <tr>
                    <td width="15%">{{ $gg->material }}</td>
                    <td width="10%">{{ $gg->medida }}</td>
                    <td width="10%">{{ $gg->cantidad }}</td>
                    <td width="10%">{{ $gg->pu }}</td>
                    <td width="12%">{{ $gg->subtotal }}</td>
                    <td width="20%"></td>
                </tr>

                @if($loop->last)
                    <tr>
                        <td width="15%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="20%" style="font-weight: bold">{{ $dd->total }}</td>
                    </tr>
                @endif

            @endforeach

        @endforeach

        </tbody>

</table>

<div style="page-break-after:always;"></div>


<table id="tabla" style="width: 95%">

    <tbody>

    @foreach($manoobra as $dd)

        @if(!$loop->first)

            <tr>
                <td width="100%" colspan="6"></td>
            </tr>

        @endif

        <tr>
            <td colspan="6">MANO DE OBRA POR ADMINISTRACIÓN</td>
        </tr>

        <tr>
            <td colspan="1" width="10%">Item</td>
            <td colspan="3" width="30%">Partida</td>
            <td colspan="2" width="20%">Cantidad P.</td>
        </tr>

        <tr>
            <td colspan="1" width="10%">{{ $dd->item }}</td>
            <td colspan="3" width="30%">{{ $dd->nombre }}</td>
            <td colspan="2" width="20%">{{ $dd->cantidadp }}</td>
        </tr>

        <tr>
            <td width="15%">Material</td>
            <td width="11">U/M</td>
            <td width="12%">Cantidad</td>
            <td width="10%">P.U</td>
            <td width="12%">Sub Total</td>
            <td width="20%">Total</td>
        </tr>

        @foreach($dd->bloque3 as $gg)

            <tr>
                <td width="15%">{{ $gg->material }}</td>
                <td width="10%">{{ $gg->medida }}</td>
                <td width="10%">{{ $gg->cantidad }}</td>
                <td width="10%">{{ $gg->pu }}</td>
                <td width="12%">{{ $gg->subtotal }}</td>
                <td width="20%"></td>
            </tr>

            @if($loop->last)
                <tr>
                    <td width="15%"></td>
                    <td width="10%"></td>
                    <td width="10%"></td>
                    <td width="10%"></td>
                    <td width="10%"></td>
                    <td width="20%" style="font-weight: bold">{{ $dd->total }}</td>
                </tr>
            @endif

        @endforeach

    @endforeach

    </tbody>

</table>

    <br>
    <br>
    <!-- partida de mano de obra -->


<table id="tabla" style="width: 95%">

    <tbody>

        <tr>
            <td colspan="3">APORTE PATRONAL</td>
        </tr>

        <tr>
            <td width="20%">Descripción</td>
            <td width="12%">Sub Total</td>
            <td width="20%">Total</td>
        </tr>

        <tr>
            <td width="20%">ISSS (7.5% mano de obra)</td>
            <td width="12%">{{ $isss }}</td>
            <td width="20%"></td>
        </tr>
        <tr>
            <td width="20%">AFP (7.75% mano de obra)</td>
            <td width="12%">{{ $afp }}</td>
            <td width="20%"></td>
        </tr>
        <tr>
            <td width="20%">INSAFOR (1.0% mano de obra)</td>
            <td width="12%">{{ $insaforp }}</td>
            <td width="20%"></td>
        </tr>

        <tr>
            <td width="20%"></td>
            <td width="12%"></td>
            <td width="20%"><strong>{{ $totalDescuento }}</strong></td>
        </tr>


    </tbody>

</table>


</body>
</html>
