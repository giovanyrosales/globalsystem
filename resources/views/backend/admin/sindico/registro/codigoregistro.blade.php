<script>

    function nuevoRegistro1(){ // REUNIONES DE CONCEJO MUNICIPAL

        var fechaReunion = document.getElementById('fecha-reunion-1').value;
        var asesoria = document.getElementById('asesoria-1').value;
        var estado = document.getElementById('select-estado-1').value;
        var fechaInforme = document.getElementById('fecha-informe-1').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 1);
        formData.append('fechaReunion', fechaReunion);
        formData.append('asesoria', asesoria);
        formData.append('estado', estado);
        formData.append('fechaInforme', fechaInforme);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal1').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro2(){ // LEGALIZACION DE ZONAS VERDES

        var fechaInscripcion = document.getElementById('fecha-inscripcion-2').value;
        var ubicacion = document.getElementById('ubicacion-2').value;
        var zonaPendientes = document.getElementById('zonas-pendientes-2').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 2);
        formData.append('fechaInscripcion', fechaInscripcion);
        formData.append('ubicacion', ubicacion);
        formData.append('zonaPendientes', zonaPendientes);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal2').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro3(){ // LEGALIZACION DE INMUEBLE

        var matricula = document.getElementById('matricula-3').value;
        var fechaInicio = document.getElementById('fecha-inicio-3').value;
        var estado = document.getElementById('select-estado-3').value;
        var fechaLegalizacion = document.getElementById('fecha-legalizacion-3').value;
        var inmueble = document.getElementById('inmueble-3').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 3);
        formData.append('matricula', matricula);
        formData.append('fechaInicio', fechaInicio);
        formData.append('estado', estado);
        formData.append('fechaLegalizacion', fechaLegalizacion);
        formData.append('inmueble', inmueble);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal3').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro4(){ // AVALUO DE INMUEBLE

        var inmueble = document.getElementById('inmueble-4').value;
        var fechaRealizacion = document.getElementById('fecha-realizacion-4').value;
        var realizadoPor = document.getElementById('realizado-por-4').value;
        var montoAvaluo = document.getElementById('monto-4').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 4);
        formData.append('inmueble', inmueble);
        formData.append('fechaRealizacion', fechaRealizacion);
        formData.append('realizadoPor', realizadoPor);
        formData.append('monto', montoAvaluo);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal4').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro5(){ // DILIGENCIAS DE JURISDICCION VOLUNTARIA

        var tipoDeligencia = document.getElementById('select-deligencia-5').value;
        var fechaRecepcion = document.getElementById('fecha-recepcion-5').value;
        var nombreSolicitante = document.getElementById('nombre-solicitante-5').value;
        var duiSolicitante = document.getElementById('dui-solicitante-5').value;
        var fechaRevision = document.getElementById('fecha-revision-5').value;
        var observacion = document.getElementById('observacion-5').value;
        var fechaEmision = document.getElementById('fecha-emision-5').value;
        var fechaEntrega = document.getElementById('fecha-entrega-5').value;
        var recibe = document.getElementById('recibe-5').value;
        var nombre = document.getElementById('nombre-5').value;
        var dui = document.getElementById('dui-5').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 5);
        formData.append('tipoDeligencia', tipoDeligencia);
        formData.append('fechaRecepcion', fechaRecepcion);
        formData.append('nombreSolicitante', nombreSolicitante);
        formData.append('duiSolicitante', duiSolicitante);
        formData.append('fechaRevision', fechaRevision);
        formData.append('observacion', observacion);
        formData.append('fechaEmision', fechaEmision);
        formData.append('fechaEntrega', fechaEntrega);
        formData.append('recibe', recibe);
        formData.append('nombre', nombre);
        formData.append('dui', dui);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal5').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro6(){ // SOLICITUDES DE ADESCO

        var adesco = document.getElementById('select-adesco-6').value;
        var estadoProceso = document.getElementById('select-estado-6').value;
        var fechaFinalizacion = document.getElementById('fecha-finalizacion-6').value;
        var observacion = document.getElementById('observacion-6').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 6);
        formData.append('adesco', adesco);
        formData.append('estado', estadoProceso);
        formData.append('fechaFinalizacion', fechaFinalizacion);
        formData.append('observacion', observacion);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal6').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro7(){ // INSPECCION DE INMUEBLE

        var tipoDiligencia = document.getElementById('select-tipodiligencia-7').value;
        var fechaRecepcion = document.getElementById('fecha-recepcion-7').value;
        var nombre = document.getElementById('nombre-7').value;
        var dui = document.getElementById('dui-7').value;
        var fechaInspeccion = document.getElementById('fecha-inspeccion-7').value;
        var nombreTecnico = document.getElementById('nombretecnico-7').value;
        var resultadoInspeccion = document.getElementById('resultado-7').value;
        var fechaEmision = document.getElementById('fecha-emision-7').value;
        var fechaDiligencia = document.getElementById('fecha-diligencia-7').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 7);
        formData.append('tipoDiligencia', tipoDiligencia);
        formData.append('fechaRecepcion', fechaRecepcion);
        formData.append('nombre', nombre);
        formData.append('dui', dui);
        formData.append('fechaInspeccion', fechaInspeccion);
        formData.append('nombreTecnico', nombreTecnico);
        formData.append('resultadoInspeccion', resultadoInspeccion);
        formData.append('fechaEmision', fechaEmision);
        formData.append('fechaDiligencia', fechaDiligencia);


        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal7').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro8(){ // REGISTRO DE INFORMES DE RECUPERACION DE MORA

        var fechaRecepcion = document.getElementById('fecha-recepcion-8').value;
        var nombreEncargado = document.getElementById('nombre-8').value;
        var informeMeses = document.getElementById('informemeses-8').value;
        var monto = document.getElementById('monto-8').value;
        var observacion = document.getElementById('observacion-8').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 8);
        formData.append('fechaRecepcion', fechaRecepcion);
        formData.append('nombreEncargado', nombreEncargado);
        formData.append('informeMeses', informeMeses);
        formData.append('monto', monto);
        formData.append('observacion', observacion);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal8').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro9(){ // INFORME DE AUTM PARA COBRO JUDICIAL

        var fechaRecepcion = document.getElementById('fecha-recepcion-9').value;
        var encargadoRemitir = document.getElementById('encargado-9').value;
        var numeroEmpresa = document.getElementById('numeroempresa-9').value;
        var numeroInmueble = document.getElementById('numeroinmueble-9').value;
        var montoTotal = document.getElementById('monto-9').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 9);
        formData.append('fechaRecepcion', fechaRecepcion);
        formData.append('encargadoRemitir', encargadoRemitir);
        formData.append('numeroEmpresa', numeroEmpresa);
        formData.append('numeroInmueble', numeroInmueble);
        formData.append('montoTotal', montoTotal);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal9').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }

    function nuevoRegistro10(){ // MONITOREO DE CONTROL DE GASTOS A NIVEL GENERAL

        var fechaRevision = document.getElementById('fecha-revision-10').value;
        var totalDocumentos = document.getElementById('totaldoc-10').value;
        var totalDocumentosApro = document.getElementById('totaldocapro-10').value;

        openLoading();
        var formData = new FormData();
        formData.append('tipoSolicitud', 10);
        formData.append('fechaRevision', fechaRevision);
        formData.append('totalDocumentos', totalDocumentos);
        formData.append('totalDocumentosApro', totalDocumentosApro);

        axios.post(url+'/sindico/registro/nuevo', formData, {
        })
            .then((response) => {
                closeLoading();
                if(response.data.success === 1){
                    toastr.success('Registrado');
                    $('#modal10').modal('hide');
                }
                else {
                    toastr.error('Error al registrar');
                }
            })
            .catch((error) => {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }


</script>
