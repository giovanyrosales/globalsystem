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

    @foreach ($array_merged as $items => $items_value)

        <table  class="table-head"  border="0" style="margin-top: 35px;">
            <tr>
                <br><br>
                <td width="20%"></td>
                <td width="75%" style="text-align:right; ">
                    <br></br>
                    <br></br>
                    <label style=" font-size: 18px;  text-align:right;">xxxxx</label>
                </td>
                <td width="5%"></td>
            </tr>
        </table>

        <br><br><br>
        <table class="table-body" border="0" cellspacing=0 style="margin-top: -35px;">
            <tr>
                <td  style=" width: 13%; text-align:right; ">
                    <label style=" vertical-align:middle; text-align:right;"></label>
                </td>
                <td  style=" width: 5%; text-align:center; ">
                    <label style=" vertical-align:middle;   font-size:14px;  text-align:center;">{{ $dia  }}</label>
                </td>
                <td style=" text-align:center; ">
                    <label style="vertical-align:middle;   font-size:14px;">{{ $mes }}</label>
                </td>
                <td style="width: 7%; text-align:right; ">
                    <label style=" vertical-align:middle;   font-size:14px;">{{  $anio }}</label>
                </td>
                <td style="width: 6%; text-align:right; ">
                    <label style=" vertical-align:middle;"></label>
                </td>
            </tr>
        </table>

        <table class="table-body" border="0" cellspacing=0  style="margin-top:10px">
            <tr>
                <td  style=" width: 10.42%; text-align:right; ">
                    <label style=" vertical-align:middle; text-align:right;"></label>
                </td>
                <td  style=" width: 53.75%; text-align:left; ">
                    <label style=" vertical-align:middle; text-align:left;   font-size:14px;">{{ $proveedor->nombre }}</label>
                </td>
                <td  style=" width: 18.8%; text-align:right; ">
                    <label style="vertical-align:middle; text-align:right;"></label>
                </td>
                <td  style=" width: 33%; text-align:left; ">
                    <label style=" vertical-align:middle; text-align:left; font-size: 14px;">{{ $proveedor->nit }}</label>
                </td>

            </tr>
        </table>

        <br></br>
        <br></br>


    <table class="table-body" border="0" cellspacing=0  style="margin-left:25px" >
        @foreach ($items_value as $item => $item_value)
            <tr style=" height: 30px" >
                <td  style=" width: 8.9%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;">{{ $item_value['cantidad'] }}</label>
                </td>
                <td  style=" width: 48.1%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left; margin-left: 5%;   font-size:14px;">{{ $item_value['nombre'] }} </label>
                </td>
                <td  style=" width: 11.6%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;">{{ $item_value['cod_presup'] }}</label>
                </td>
                <td  style=" width: 1.6%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;   font-size:14px;"></label>
                </td>
                <td  style=" width: 10%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right;   font-size:14px;">${{ $item_value['precio_u'] }}</label>
                </td>
                <td  style=" width: 1%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right;"></label>
                </td>
                <td  style=" width: 1.6%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;   font-size:14px;"></label>
                </td>
                <td  style=" width: 11.2%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right;   font-size:14px;">${{ $item_value['multi'] }}</label>
                </td>
                <td  style=" width: 7%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right;" ></label>
                </td>
            </tr>
        @endforeach

        </table>

     <table class="table-body" border="0" cellspacing=0 style="position: absolute; float: bottom">
        <tr style=" height: 30px" >
            <td  style=" width: 8.9%; text-align:center; ">
                <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
            </td>
            <td  style=" width: 54.1%; text-align:left; ">
                <label style="  vertical-align:middle; text-align:left; margin-left: 5%;   font-size:14px;"> </label>
            </td>
            <td  style=" width: 5.6%; text-align:center; ">
                <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
            </td>
            <td  style=" width: 1.6%; text-align:left; ">
                <label style="  vertical-align:middle; text-align:left;   font-size:14px;"></label>
            </td>
            <td  style=" width: 9%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;   font-size:14px;"></label>
            </td>
            <td  style=" width: 1%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;"></label>
            </td>
            <td  style=" width: 1.6%; text-align:left; ">
                <label style="  vertical-align:middle; text-align:left;   font-size:14px;"></label>
            </td>
            <td  style=" width: 12.2%; text-align:right; ">
                <label style=" font-weight: bold;vertical-align:middle; text-align:right;   font-size:14px;"></label>
            </td>
            <td  style=" width: 6%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;"></label>
            </td>
        </tr>
    </table>

    <div style="bottom: 0;
    position: absolute;">
        <table class="table-body" border="0" cellspacing=0 style="margin-top:20px; " >
            <tr style=" height: 30px; " >
                <td  style=" width: 11%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
                </td>
                <td  style=" width: 52%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;   font-size:14px;"></label>
                </td>
                <td  style=" width: 25%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right; margin-left: 5%;   font-size:14px;">${{  $total}} </label>
                </td>
                <td  style=" width: 6%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
                </td>
            </tr>
        </table>

        <table class="table-body" border="0" cellspacing=0 style="margin-top:20px" >
            <tr style=" height: 30px; " >
                <td  style=" width: 11%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
                </td>
                <td  style=" width: 52%; text-align:left; ">
                    <label style="  vertical-align:middle; text-align:left;   font-size:14px;">{{$administrador->nombre}}</label>
                </td>
                <td  style=" width: 31%; text-align:right; ">
                    <label style="  vertical-align:middle; text-align:right; margin-left: 5%;   font-size:14px;">wwwwwwwwww</label>
                </td>
                <td  style=" width: 6%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
                </td>
            </tr>
        </table>

        <table class="table-body" border="0" cellspacing=0 >
            <tr style=" height: 30px; " >
                <td  style=" width: 100%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;">aaaaaaaaa</label>
                </td>
            </tr>
        </table>

        <table class="table-body" border="0" cellspacing=0  >
            <tr style=" height: 30px; " >
                <td  style=" width: 100%; text-align:center; ">
                    <label style="  vertical-align:middle; text-align:center;   font-size:14px;">xxxxx</label>
                </td>
            </tr>
        </table>
    </div>


        @if(!$loop->last)
            <div class="page_break"></div>
        @endif


    @endforeach
