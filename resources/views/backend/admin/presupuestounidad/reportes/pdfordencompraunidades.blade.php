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
                <td width="50%">
                    <label style=" font-size: 15px; ">ALCALDÍA MUNICIPAL DE METAPÁN </label>
                </td>
                <td width="50%" >
                <center><label style=" font-size: 16px; "><b>ORDEN DE COMPRA</b></label></center>
                </td>
            </tr>
        </table>
        <table  class="table-head"  border="0" style="margin-top: 10px;">
            <tr>
                <td width="40%" style="text-align:left; ">
                  Avenida Benjamin Estrada Valiente<br>
                  1ra Calle Pte. Bo. San Pedro<br>
                  Ciudad de Metapán<br>
                  Departamento de Santa Ana<br>
                  correo: ucpmetapan@gmail.com<br>
                  Tel.: 2402-7609
                </td>
                <td width="20%"><center>
                <img style="margin-top: -20px; "src="{{ asset('/images/logo.png') }}" width="70px" height="70px"></center>
                </td>
                <td width="40%" style="text-align:left; ">
                   No. {{ $idorden }}<br>
                   Fecha: {{ $dia." ".$mes." ".$anio }}<br>
                   Ref. [CPS-001/23AMM]
                </td>
            </tr>
        </table>
        <table  class="table-head"  border="0" style="margin-top: 15px;">
            <tr>
                <td bgcolor="#66ff99"><center><label style=" font-size: 15px; ">PROVEEDOR </label></center></td>
                <td bgcolor="#66ff99"><center><label style=" font-size: 15px; ">CONTRATANTE </label></center></td>
            </tr>
            <tr>
                <td width="50%" style="text-align:left; ">
                  {{ $proveedor->nombre }}<br>
                  Dirección: $proveedor->direccion<br>
                  Correo: $proveedor->correo<br>
                  Tel.: {{ $proveedor->telefono }}<br>
                  NIT/DUI: {{ $proveedor->nit }}<br>
                  NRC: {{ $proveedor->nrc }}
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  Sr. Israel Peraza Guerra<br>
                  Alcalde Municipal de Metapán<br>
                  Representante Legal y Administrativo<br>
                  Periodo May/2021-Abr/2024<br>
                  DUI: 04407747-3<br>
                  NIT: 0207-310372-101-0
                </td>
            </tr>
        </table>
        <table  class="table-head"  border="1" style="margin-top: 15px; border-collapse: collapse;">
            <tr>
                <td bgcolor="#66ff99"><center><label style=" font-size: 15px; ">Solicitado por</label></center></td>
                <td bgcolor="#66ff99"><center><label style=" font-size: 15px; ">Unidad</label></center></td>
            </tr>
            <tr>
                <td width="50%" style="text-align:left; ">
                  Nombre: {{ $nombreSolicitante }}
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  Nombre Unidad:
                </td>
            </tr>
            <tr>
                <td width="50%" style="text-align:left; ">
                  Cargo:
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  Cod. Proyecto:
                </td>
            </tr>
        </table>

        <table  class="table-head"  border="1" style="margin-top: 15px; border-collapse: collapse;" width="100%">
            @foreach ($dataArray as $info)
                <tr>
                    <td bgcolor="#66ff99"><center><label style=" font-size: 13px; ">Código </label></center></td>
                    <td bgcolor="#66ff99"><center><label style=" font-size: 13px; ">Descripción </label></center></td>
                    <td bgcolor="#66ff99"><center><label style=" font-size: 13px; ">Cantidad </label></center></td>
                    <td bgcolor="#66ff99"><center><label style=" font-size: 13px; ">Precio U. </label></center></td>
                    <td bgcolor="#66ff99"><center><label style=" font-size: 13px; ">Totales </label></center></td>
                </tr>
                <tr>
                    <td width="15%" style="text-align:center; ">{{ $info['cod_presup'] }}</td>
                    <td width="45%" style="text-align:center; ">{{ $info['nombre'] }}</td>
                    <td width="10%" style="text-align:center; ">{{ $info['cantidad']}}</td>
                    <td width="15%" style="text-align:center; ">${{ $info['precio_u'] }}</td>
                    <td width="15%" style="text-align:center; ">${{ $info['multi'] }}</td>
                </tr>
            @endforeach
        </table><br>
        <label style=" font-size: 15px; "><u>Instrucciones</u> </label>
        <table  class="table-head"  border="1" style="margin-top: 15px; border-collapse: collapse;">
            <tr>
                <td bgcolor="#66ff99"><center><label style=" font-size: 15px; ">Dirección de Entrega</label></center></td>
                <td bgcolor="#66ff99"><center><label style=" font-size: 15px; ">Forma de pago</label></center></td>
                <td bgcolor="#66ff99" style="text-align:right;"><label style=" font-size: 15px; ">Total: ${{  $total}} </label></td>
            </tr>
            <tr>
                <td width="50%" style="text-align:left; ">
                  Dirección entrega:
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  Forma de pago:
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  DOLARES 00/100
                </td>
            </tr>
        </table>
        <table  class="table-head"  border="1" style="border-collapse: collapse;"> >
            <tr>
                <td style="text-align: center; background-color: #66ff99;"><label style=" font-size: 15px; ">Plazo de entrega</label></td>
                <td style="text-align: center; background-color: #66ff99;"><label style=" font-size: 15px; ">Garantía a Presentar</label></td>
                <td style="text-align: center; background-color: #66ff99;"><label style=" font-size: 15px; ">Administrador ODC</label></td>
            </tr>
            <tr>
                <td width="50%" style="text-align:left; ">
                  Plazo de entrega
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  Garantía a Presentar
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                {{$administrador->nombre}}
                </td>
            </tr>
        </table>
        <br>


    <div style="bottom: 40px;  position: absolute; width: 100%" >

    <table  class="table-head"  border="0" >
            <tr>
                <td style="text-align:left;"><label style=" font-size: 15px; ">F._________________</label></td>
                <td style="text-align:left;"><label style=" font-size: 15px; ">F._________________</label></td>
                <td style="text-align:left;"><label style=" font-size: 15px; ">F._________________</label></td>
            </tr>
            <tr>
                <td width="50%" style="text-align:left; ">
                  Nombre:
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  Nombre:
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                 Sr. Israel Peraza Guerra
                </td>
            </tr>
            <tr>
                <td width="50%" style="text-align:left; ">
                  Cargo:
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                  Cargo:
                </td>
                <td width="50%" style="text-align:left; padding-left: 10px; ">
                 Alcalde Municipal de Metapán
                </td>
            </tr>
        </table>


        <label style=" font-size: 15px; ">{{ $destinounidad." ".$destino." ".$arraycodigos." ".$acta_acuerdo }}</label>
    </div>


