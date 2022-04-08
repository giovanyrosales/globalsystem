<style>
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
</style>
<table  class="table-head"  border="0" style="margin-top: 35px;">
    <tr>
        <br><br>
        <td width="20%"></td>
        <td width="75%" style="text-align:right; ">
            <br></br>
            <br></br>
            <label style=" font-size: 18px;  text-align:right;">{{ $proyecto-> codigo }}</label>
        </td>
        <td width="5%"></td>
    </tr>
</table>
<br>

<br>
<br>
<table class="table-body" border="0" cellspacing=0 style="margin-top: -35px;">
    <tr>
        <td  style=" width: 13%; text-align:right; ">
            <label style=" vertical-align:middle; text-align:right;"></label>
        </td>
        <td  style=" width: 5%; text-align:center; ">
            <label style=" vertical-align:middle;   font-size:14px;  text-align:center;">{{  $fecha[0] }}</label>
        </td>
        <td style=" text-align:center; ">
            <label style="vertical-align:middle;   font-size:14px;">{{ $fecha[1] }}</label>
        </td>
        <td style="width: 7%; text-align:right; ">
            <label style=" vertical-align:middle;   font-size:14px;">{{  $fecha[2] }}</label>
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
        <td  style=" width: 17.08%; text-align:left; ">
            <label style=" vertical-align:middle; text-align:left; font-size: 15px;">{{ $proveedor->nit }}</label>
        </td>

    </tr>
</table>

<br></br>
<br></br>

<?php $total = 0; ?>
<table class="table-body center" border="0" cellspacing=0 style="padding-right: 25px; padding-left: 25px; margin-left: 20px; width: 100%">

    @foreach($det_cotizacion as $item)

        <tr style=" height: 30px" >
            <td  style=" width: 8.9%; text-align:center; ">
                <label style="  vertical-align:middle; text-align:center; font-size:14px;">{{ $item->cantidad }}</label>
            </td>
            <td  style=" width: 48.1%; text-align:left; ">
                <label style="  vertical-align:middle; text-align:left; margin-left: 5%;   font-size:14px;">{{ $item->descripcion }} </label>
            </td>
            <td  style=" width: 11.6%; text-align:center; ">
                <label style="  vertical-align:middle; text-align:center;   font-size:14px;">{{ $item->cod_presup }}</label>
            </td>
            <td  style=" width: 1.6%; text-align:left; ">
                <label style="  vertical-align:middle; text-align:left;   font-size:14px;"></label>
            </td>
            <td  style=" width: 10%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;   font-size:14px;">${{ round($item->preciounit, 2) }}</label>
            </td>
            <td  style=" width: 1%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;"></label>
            </td>
            <td  style=" width: 1.6%; text-align:left; ">
                <label style="  vertical-align:middle; text-align:left;   font-size:14px;"></label>
            </td>
            <td  style=" width: 11.2%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;   font-size:14px;">${{ round(($item->cantidad * $item->preciounit), 2) }}</label>
            </td>
            <td  style=" width: 7%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;" value="{{$total = $total + ($item->cantidad * $item->preciounit) }}"></label>
            </td>

        </tr>
    @endforeach
    @for($i = 1; $i < (20-count($det_cotizacion)); ++$i)
        <tr style=" height: 30px" >
            <td  style=" width: 8.9%; text-align:center; ">
                <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
            </td>
            <td  style=" width: 54.1%; text-align:left; ">
                <label style="  vertical-align:middle; text-align:left; margin-left: 5%;   font-size:14px;"> </label>
            </td>
            <td  style=" width: 11.6%; text-align:center; ">
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
            <td  style=" width: 11.2%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;   font-size:14px;"></label>
            </td>
            <td  style=" width: 1%; text-align:right; ">
                <label style="  vertical-align:middle; text-align:right;"></label>
            </td>

        </tr>
    @endfor
</table>

<table class="table-body" border="0" cellspacing=0 >
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
            <label style=" font-weight: bold;vertical-align:middle; text-align:right;   font-size:14px;">${{ round($total, 2) }}</label>
        </td>
        <td  style=" width: 6%; text-align:right; ">
            <label style="  vertical-align:middle; text-align:right;"></label>
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
            <label style="  vertical-align:middle; text-align:right; margin-left: 5%;   font-size:14px;">{{  $proyecto->codigo}} </label>
        </td>
        <td  style=" width: 6%; text-align:center; ">
            <label style="  vertical-align:middle; text-align:center;   font-size:14px;"></label>
        </td>
    </tr>
</table>

<table class="table-body" border="0" cellspacing=0 >
    <tr style=" height: 30px; " >
        <td  style=" width: 100%; text-align:center; ">
            <label style="  vertical-align:middle; text-align:center;   font-size:14px;">{{$proyecto->nombre}}</label>
        </td>
    </tr>
</table>

<table class="table-body" border="0" cellspacing=0  >
    <tr style=" height: 30px; " >
        <td  style=" width: 100%; text-align:center; ">
            <label style="  vertical-align:middle; text-align:center;   font-size:14px;">{{$proyecto->ubicacion}}</label>
        </td>
    </tr>
</table>
