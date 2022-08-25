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
        <td width="10%">Código</td>
        <td width="30%">Nombre</td>
        <td width="20%">Saldo Inicial.</td>
        <td width="20%">Saldo Inicial.</td>
    </tr>



    </tbody>

</table>


<br>
<br>
<!-- partida de mano de obra -->


<script type="text/php">
       if (isset($pdf)) {
        $x = 270;
        $y = 750;
        $text = "Página {PAGE_NUM}/{PAGE_COUNT}";
        $font = null;
        $size = 9;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>

</body>
</html>

