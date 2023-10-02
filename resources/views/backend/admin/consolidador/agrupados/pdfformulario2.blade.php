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
        font-family:Arial, sans-serif; font-size:12px;
    }
    .table-body td{
        font-family:Arial, sans-serif; font-size:12px;
    }

    .page_break {
        page-break-before: always;
    }
</style>

<table  class="table-head"  border="0" style="margin-top: 35px;">
    <tr>
        <td style="width: 40%; text-align: center;">
            <img style="margin-top: -20px; "src="{{ asset('/images/logo.png') }}" width="85px" height="85px">
        </td>
        <td style="width: 60%; text-align: left;">
            <label style=" font-size: 15px; ">ALCALDÍA MUNICIPAL DE METAPÁN </label><br>
            <label style=" font-size: 16px; "><b>Solicitud de Obra, Bien o Servicio</b></label>
        </td>
    </tr>
</table>
<table  class="table-head"   style="margin-top: 10px; border: none;">
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:right; width: 100%;">
            Acta:_____    Acuerdo:_____       Aprobación de Proyecto:_______    N° de Solicitud:_________
        </td>
    </tr>
</table>
<table  class="table-head"   style="margin-top: 10px; border: 1px solid black; border-collapse: collapse;">
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 100%;">
           Unidad Solicitante: {{$nombresDep}}
        </td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 100%;">
            Nombre o Destino del Proyecto: {{$infoRequiAgrupado->nombreodestino}}
        </td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 100%; ">
            Fecha: {{$fecha}}
        </td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style=" width: 100%; text-align: center; background-color: #ccffdd;">JUSTIFICACIÓN:
        </td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 100%; "> {{$infoRequiAgrupado->justificacion}}
        </td>
    </tr>
</table>

<table  class="table-head" style="margin-top: 15px; border: 1px solid black; border-collapse: collapse;">
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style=" width: 100%; text-align: center; background-color: #ccffdd;">DETALLE DEL BIEN SOLICITADO
        </td>
    </tr>
</table>

<table  class="table-head"  style=" border: 1px solid black; border-collapse: collapse;">

        <tr style="border: 1px solid black; border-collapse: collapse;">
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Item </label></td>
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Código P. </label></td>
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Cantidad </label></td>
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">U. Medida </label></td>
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Descripción del Bien</label></td>
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Especificación Técnica</label></td>
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">P. Unitario</label></td>
            <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Total</label></td>
        </tr>
        @foreach($arrayReqADetalle as $detalles)
        <tr style="border: 1px solid black; border-collapse: collapse;">
            <td style="text-align:center; width: 5%; border: 1px solid black; border-collapse: collapse;">{{ $detalles->contador }}</td>
            <td style="text-align:center; width: 10%; border: 1px solid black; border-collapse: collapse;">{{$detalles->codigo}}</td>
            <td style="text-align:center; width: 10%; border: 1px solid black; border-collapse: collapse;">{{$detalles->cantidad}}</td>
            <td style="text-align:center; width: 10%; border: 1px solid black; border-collapse: collapse;">{{$detalles->unidadmedida}}</td>
            <td style="text-align:center; width: 30%; border: 1px solid black; border-collapse: collapse;">{{$detalles->descripcion}}</td>
            <td style="text-align:center; width: 35%; border: 1px solid black; border-collapse: collapse;">{{$detalles->especificacion}}</td>
            <td style="text-align:center; width: 20%; border: 1px solid black; border-collapse: collapse;">{{ $detalles->unitarioFormat }}</td>
            <td style="text-align:center; width: 20%; border: 1px solid black; border-collapse: collapse;">{{ $detalles->multiplicadoFormat }}</td>
        </tr>
        @endforeach


    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:center; width: 5%; border: 1px solid black; border-collapse: collapse;">TOTAL</td>
        <td style="text-align:center; width: 10%; border: 1px solid black; border-collapse: collapse;"></td>
        <td style="text-align:center; width: 10%; border: 1px solid black; border-collapse: collapse;"></td>
        <td style="text-align:center; width: 10%; border: 1px solid black; border-collapse: collapse;"></td>
        <td style="text-align:center; width: 30%; border: 1px solid black; border-collapse: collapse;"></td>
        <td style="text-align:center; width: 35%; border: 1px solid black; border-collapse: collapse;"></td>
        <td style="text-align:center; width: 20%; border: 1px solid black; border-collapse: collapse;"></td>
        <td style="text-align:center; width: 20%; border: 1px solid black; border-collapse: collapse; font-weight: bold">{{ $totalsumado }}</td>
    </tr>





</table><br>
<table  class="table-head"  style=" border: 1px solid black; border-collapse: collapse;">

    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align: left; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Propuesta de Admin. de Contrato</label></td>
        <td style="text-align: left; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Propuesta de Evaluador Técnico</label></td>

        @if ($infoRequiAgrupado->id_evaluador2 != null)
            <td style="text-align: left; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">Propuesta de Evaluador Técnico</label></td>
        @endif

    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Nombre: {{$nombreadmin}}</td>
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Nombre: {{$nombreeva}}</td>
        @if ($infoRequiAgrupado->id_evaluador2 != null)
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Nombre: {{$nombreeva2}}</td>
        @endif
    </tr>

    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Cargo:{{$cargoadmin}}</td>
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Cargo:{{$cargoeva}}</td>
        @if ($infoRequiAgrupado->id_evaluador2 != null)
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Cargo:{{$cargoeva2}}</td>
        @endif
    </tr>

</table><br>
<table  class="table-head"  style=" border: 1px solid black; border-collapse: collapse;">
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align: center; border: 1px solid black; border-collapse: collapse; background-color: #ccffdd;"><label style=" font-size: 13px; ">CONDICIONES DE COMPRA</label></td>
    </tr>
</table>
<table  class="table-head"  style=" border: 1px solid black; border-collapse: collapse;">
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Forma de Entrega (Parcial o Total):</td>
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">{{$infoRequiAgrupado->entrega}}</td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Plazo de Entrega:</td>
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">{{$infoRequiAgrupado->plazo}}</td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Lugar de Entrega:</td>
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">{{$infoRequiAgrupado->lugar}}</td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Forma de Contratación (Contrato u Orden de Compra):</td>
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">{{$infoRequiAgrupado->forma}}</td>
    </tr>
    <tr style="border: 1px solid black; border-collapse: collapse;">
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">Otras Condiciones Necesarias (Especificar):</td>
        <td style="text-align:left; width: 50%; border: 1px solid black; border-collapse: collapse;">{{$infoRequiAgrupado->otros}}</td>
    </tr>

</table><br>


<div style="bottom: 10px;  position: absolute; width: 100%" >

    <table  class="table-head"  border="0" >
        <tr>
            <td width="50%" style="text-align:center; ">
                UNIDAD CONSOLIDADORA<br>
            </td>
            <td width="50%" style="text-align:center; padding-left: 10px; ">
                AUTORIZA<br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;"><label style=" font-size: 14px; padding-top: 15px;">F._________________</label></td>
            <td style="text-align:left;"><label style=" font-size: 14px; padding-top: 15px;">F._________________</label></td>

        </tr>
        <tr>
            <td width="50%" style="text-align:left; ">
                Nombre:
            </td>
            <td width="50%" style="text-align:left; padding-left: 10px; ">
                Nombre:
            </td>
        </tr>
        <tr>
            <td width="50%" style="text-align:left; ">
                Cargo:
            </td>
            <td width="50%" style="text-align:left; padding-left: 10px; ">
                Cargo:
            </td>
        </tr>
    </table><br>
    <table  class="table-head"  border="0" >
        <tr>
            <td width="50%" style="text-align:center; ">
                REVISADOR POR:<br>
            </td>
            <td width="50%" style="text-align:center; padding-left: 10px; ">
                RECIBE UCP<br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;"><label style=" font-size: 14px; padding-top: 15px;">F._________________</label></td>
            <td style="text-align:left;"><label style=" font-size: 14px; padding-top: 15px;">F._________________</label></td>

        </tr>
        <tr>
            <td width="50%" style="text-align:left; ">
                Nombre:
            </td>
            <td width="50%" style="text-align:left; padding-left: 10px; ">
                Nombre:
            </td>
        </tr>
        <tr>
            <td width="50%" style="text-align:left; ">
                Cargo:
            </td>
            <td width="50%" style="text-align:left; padding-left: 10px; ">
                Fecha:<br>
                Hora:
            </td>
        </tr>
    </table>

</div>

