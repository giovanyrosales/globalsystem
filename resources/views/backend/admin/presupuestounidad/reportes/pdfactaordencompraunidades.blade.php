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
</style>
<head>
    <meta charset="UTF-8">
</head>
<table  class="table-head" style="margin-top: 10px;">
    <tr>
        <br></br>
        <td width="20%"><img style="margin-top: -20px; "src="{{ asset('/images/logo.png') }}" width="80px" height="80px"></td>
        <td width="60%" style="text-align:center; ">
            <label style="font-weight: bold; font-size: 25px;  text-align:center;">ACTA DE RECEPCION DE BIENES, SERVICIOS Y OBAS.</label>
        </td>
        <td width="20%"></td>
    </tr>
</table>

<table  class="table-head" style="margin-top: -10px;">
    <tr>
        <td width="100%" style="text-align:center; ">
            <label style=" font-size: 15px;  text-align:center;">Alcaldía Municipal de Metapán.</label>
        </td>
    </tr>
</table>

<table  class="table-head" style="margin-top: 100px;">
    <tr>
        <td width="10%"></td>
        <td width="80%" style="text-align:left; ">
            <label style=" font-size: 15px;  text-align:left;">Reunidos en las instalaciones de </label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $proveedor->nombre}}</label>
            <label style=" font-size: 15px;  text-align:left;">, a las  </label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $hora }}</label>
            <label style=" font-size: 15px;  text-align:left;">del día  </label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ strftime("%d", strtotime($fecha)) }} / {{ strftime("%m", strtotime($fecha)) }} / {{ strftime("%y", strtotime($fecha)) }}</label>
            <label style=" font-size: 15px;  text-align:left;">; con el propósito de hacer entrega formal por parte de </label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $proveedor->nombre}}</label>
            <label style=" font-size: 15px;  text-align:left;">.</label>
        </td>
        <td width="10%"></td>
    </tr>
</table>

<table  class="table-head" style="margin-top: 50px;">
    <tr>
        <td width="10%"></td>
        <td width="80%" style="text-align:left; ">
            <label style=" font-size: 15px;  text-align:left;">Todo lo correspondiente a la orden No.</label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $orden->id }}</label>
            <label style=" font-size: 15px;  text-align:left;"> y con base a lo solicitado; presente los señores</label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $proveedor->nombre }}</label>
            <label style=" font-size: 15px;  text-align:left;">, por parte del proveedor; </label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $administrador->nombre}}.</label>
            <label style=" font-size: 15px;  text-align:left;"> en calidad  de administrador de contrato del proyecto:</label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">xxx.</label>
            <label style=" font-size: 15px;  text-align:left;">, de código </label>
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">xxx</label>
            <label style=" font-size: 15px;  text-align:left;">.</label>
        </td>
        <td width="10%"></td>
    </tr>
</table>



<table  class="table-head" style="margin-top: 50px;">
    <tr>
        <td width="10%"></td>
        <td width="80%" style="text-align:left; ">
            <label style=" font-size: 15px;  text-align:left;">Cabe mencionar que dichos bienes, servicios u obras cumple con las especificaciones previamente definidas en el contrato u orden de compra.</label>
        </td>
        <td width="10%"></td>
    </tr>
</table>


<table  class="table-head" style="margin-top: 50px;">
    <tr>
        <td width="10%"></td>
        <td width="80%" style="text-align:left; ">
            <label style=" font-size: 15px;  text-align:left;">Y no habiendo más que hacer constar, firmamos y ratificamos la presente acta.</label>
        </td>
        <td width="10%"></td>
    </tr>
</table>

<table  class="table-head" style="margin-top: 150px;">
    <tr>
        <td width="10%"></td>
        <td width="40%" style="text-align:left; ">
            <label style=" font-size: 15px;  text-align:left;">Entrega: </label>
        </td>
        <td width="40%" style="text-align:left; ">
            <label style=" font-size: 15px;  text-align:left;">Recibe: </label>
        </td>
    </tr>
</table>


<table  class="table-head" style="margin-top: 20px;">
    <tr>
        <td width="10%"></td>
        <td width="40%"style="text-align:left; ">
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">F. _________________________</label>
        </td>
        <td width="40%" style="text-align:left; ">
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">F. _________________________</label>
        </td>
    </tr>
    <tr>
        <td width="10%"></td>
        <td width="40%">
            <label style=" font-size: 15px;  text-align:left;">Proveedor</label>
        </td>
        <td width="40%" style="text-align:left; ">
            <label style=" font-size: 15px;  text-align:left;">Administrador de contrato</label>
        </td>
    </tr>
    <tr>
        <td width="10%"></td>
        <td width="40%">
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $proveedor->nombre }}</label>
        </td>
        <td width="40%" style="text-align:left; ">
            <label style="font-weight: bold; font-size: 15px;  text-align:left;">{{ $administrador->nombre }}</label>
        </td>
    </tr>
</table>
