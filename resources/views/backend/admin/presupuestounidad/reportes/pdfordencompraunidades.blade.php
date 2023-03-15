<style type="text/css">
    input {
        border-width:1px;  border:none;
    }
    .ordenInput{
        display: block;
    }
    .table-body{
        width: 100%;
    }
    .table-head{
        width: 100%;
    }
    .table-head td{
        font-family:Arial, sans-serif; font-size:14px;
    }
    .table-body td{
        font-family:Arial, sans-serif; font-size:14px;
    }

    .page_break {
        page-break-before: always;
    }
</style>



        <table  class="table-head"  border="0" style="margin-top: 35px;">
            <tr>
                <br><br>
                <td width="20%"></td>
                <td width="75%" style="text-align:right; ">
                    <br></br>
                    <br></br>
                    <label style=" font-size: 18px;  text-align:right;">{{ $idorden }}</label>
                </td>
                <td width="5%"></td>
            </tr>
        </table>

        <br><br><br>
        <table class="table-body" border="0" cellspacing=0 style="margin-top: -48px;">
            <tr>

                <td  style=" width: 20%; text-align:right; ">
                    <label style=" vertical-align:middle;
                      font-size:12px;  text-align:right; padding-right: 28px">{{ $dia  }}</label>
                </td>
                <td style=" text-align:center; ">
                    <label style="vertical-align:middle;   font-size:12px;">{{ $mes }}</label>
                </td>
                <td style="width: 25%; text-align:right; ">
                    <label style=" vertical-align:middle; padding-right: 15px;   font-size:12px;">{{  $anio }}</label>
                </td>

            </tr>
        </table>

        <table class="table-body" border="0" cellspacing=0  style="margin-top:0px">
            <tr>
                <td  style=" width: 10.42%; text-align:right; ">
                    <label style=" vertical-align:middle; text-align:right;"></label>
                </td>
                <td  style=" width: 53.75%; text-align:left; ">
                    <label style=" vertical-align:middle; text-align:left;   font-size:11px;">{{ $proveedor->nombre }}</label>
                </td>
                <td  style=" width: 18.8%; text-align:right; ">
                    <label style="vertical-align:middle; text-align:right;"></label>
                </td>
                <td  style=" width: 33%; text-align:left; ">
                    <label style=" vertical-align:middle; text-align:left; font-size: 14px;">{{ $proveedor->nit }}</label>
                </td>

            </tr>
        </table>

        <br>
        <br>

    <table class="table-body" border="0" cellspacing=0  style="margin-right:22px;" width="100%">
        @foreach ($dataArray as $info)

            <tr style=" height: 30px" >
                <td  style=" width: 7%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;   font-size:11px;">{{ $info['cantidad'] }}</label>
                </td>
                <td  style=" width: 48.1%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;
                     padding-left: 15px;
                     margin-left: 5%;   font-size:11px;">{{ $info['nombre'] }} </label>
                </td>
                <td  style=" width: 11.6%; text-align:center;">
                    <label style="  vertical-align:middle; text-align:center;
                    padding-left: 20px; margin-left: 20px;
                    font-size:11px;">{{ $info['cod_presup'] }}</label>
                </td>

                <td  style=" width: 16%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right;   font-size:11px;">${{ $info['precio_u'] }}</label>
                </td>

                <td  style=" width: 11.2%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right;   font-size:11px;">${{ $info['multi'] }}</label>
                </td>
                <td  style=" width: 7%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right;" ></label>
                </td>
            </tr>
        @endforeach


        </table>


    <div style="bottom: 40px;  position: absolute; width: 100%" >

        <table class="table-body" border="0" >
            <tr style=" height: 30px; " >

            <tr>
                <td  style=" width: 20%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:11px;">NIT: {{ $proveedor->nit }}</label>
                </td>
            </tr>
            <tr>
                <td  style=" width: 20%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:11px;">Códigos: {{ $arraycodigos }}</label>
                </td>
            </tr>
            <tr>
                <td  style=" width: 20%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:11px;">Solic.: {{ $nombreSolicitante }}</label>
                </td>
            </tr>
            <tr>
                <td  style=" width: 20%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:11px;">{{ $acta_acuerdo }}</label>
                </td>
            </tr>

            </tr>
        </table>


        <table border="0" width="100%" style="margin-bottom: 12px">
            <tr style=" height: 30px; ">

                <td  style=" width: 25%; text-align:right;">
                    <label style=" font-size:13px; margin-right: 15px;">${{  $total}} </label>
                </td>
            </tr>
        </table>


        <table class="table-body" border="0" cellspacing=0 style="margin-bottom: 10px">
            <tr style=" height: 30px; " >
                <td  style=" width: 60%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left; font-size:11px;">{{$administrador->nombre}}</label>
                </td>
                <td  style=" width: 40%; ">
                    <label style="  vertical-align:middle; float: right; text-align:right; margin-left: 5%;  margin-right: 15px; font-size:14px;">Fondos Propios</label>
                </td>
            </tr>

        </table>



        <table class="table-body" border="0" cellspacing=0 style="margin-left: 15px;">


            <tr style=" height: 30px; " >
                <td  style=" width: 60%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;   font-size:11px;">{{ $destino  }}</label>
                </td>
                <td  style=" width: 40%; ">
                </td>
            </tr>

            <tr style=" height: 30px; " >
                <td  style=" width: 60%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;   font-size:11px;">{{ $destinounidad  }}</label>
                </td>
                <td  style=" width: 40%; ">
                </td>
            </tr>

        </table>


    </div>


