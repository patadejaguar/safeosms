<?php
$sqlb01 = "SELECT socios_cajalocal.idsocios_cajalocal AS 'Caja Local',
socios_cajalocal.descripcion_cajalocal AS 'Nombre Caja Local',
socios_region.descripcion_region AS 'Region'
FROM socios_cajalocal NATURAL JOIN socios_region ";

$sqlb01a = "SELECT socios_cajalocal.idsocios_cajalocal AS 'Caja_Local',
socios_cajalocal.descripcion_cajalocal AS 'Nombre_Caja_Local',
socios_region.descripcion_region AS 'Region'
FROM socios_cajalocal NATURAL JOIN socios_region ";

$sqlb02 = "SELECT socios_general.codigo AS 'Codigo',
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ' ,socios_general.nombrecompleto) AS 'Nombre_Completo'
FROM socios_general ";

$sqlb02_ext = "SELECT socios_general.codigo AS 'Codigo',
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto) AS 'Nombre_Completo',
socios_tipoingreso.descripcion_tipoingreso AS 'Tipo_de_Ingreso',
socios_grupossolidarios.nombre_gruposolidario AS 'Grupo_Solidario'
FROM socios_general, socios_tipoingreso, socios_grupossolidarios
WHERE socios_grupossolidarios.idsocios_grupossolidarios=socios_general.grupo_solidario
AND socios_tipoingreso.idsocios_tipoingreso=socios_general.tipoingreso ";

$sqlb15 = "SELECT creditos_flujoefvo.idcreditos_flujoefvo AS 'codigo',
creditos_tflujo.descripcion_tflujo AS 'Tipo',
creditos_flujoefvo.descripcion_completa as 'concepto',
creditos_periocidadflujo.descripcion_periocidadflujo AS 'Periocidad',
FORMAT(creditos_flujoefvo.monto_flujo, 2) AS 'monto',
FORMAT(creditos_flujoefvo.afectacion_neta, 2) AS 'monto_diario', creditos_flujoefvo.observacion_flujo AS 'Observacion'
FROM creditos_flujoefvo, creditos_tflujo, creditos_periocidadflujo
WHERE creditos_tflujo.tipo_flujo=creditos_flujoefvo.tipo_flujo
AND creditos_periocidadflujo.periocidad_flujo=creditos_flujoefvo.periocidad_flujo ";

$sqlb16 ="SELECT socios_relaciones.idsocios_relaciones AS 'ID',
socios_relacionestipos.descripcion_relacionestipos AS 'Tipo_de_Relacion',
socios_consanguinidad.descripcion_consanguinidad AS 'Consanguinidad',
CONCAT(socios_relaciones.nombres ,' ', socios_relaciones.apellido_paterno, ' ', socios_relaciones.apellido_materno) AS 'Nombre_Completo',
socios_relaciones.curp AS 'C_U_R_P',
CONCAT(socios_relaciones.telefono_residencia, '; ' , socios_relaciones.telefono_movil)  AS 'telefonos'
FROM socios_relacionestipos, socios_consanguinidad, socios_relaciones
WHERE socios_relacionestipos.idsocios_relacionestipos=socios_relaciones.tipo_relacion
AND socios_consanguinidad.idsocios_consanguinidad=socios_relaciones.consanguinidad ";

$sqlb16_ext = "SELECT socios_relacionestipos.descripcion_relacionestipos AS 'Tipo_de_Relacion',
socios_consanguinidad.descripcion_consanguinidad AS 'Consanguinidad',
socios_relaciones.numero_socio AS 'Numero_de_socio',
CONCAT(socios_relaciones.nombres ,' ', socios_relaciones.apellido_paterno, ' ', socios_relaciones.apellido_materno) AS 'Nombre_Completo',
socios_relaciones.curp AS 'C_U_R_P',
socios_relaciones.fecha_nacimiento AS 'Fecha_de_Nacimiento',
socios_relaciones.telefono_residencia AS 'Telefono_Residencial',
socios_relaciones.ocupacion AS 'Ocupacion_',
socios_relaciones.telefono_movil AS 'Telefono_Movil',
socios_relaciones.domicilio_completo AS 'Domicilio'
FROM socios_relacionestipos, socios_consanguinidad,
socios_relaciones
WHERE socios_relacionestipos.idsocios_relacionestipos=socios_relaciones.tipo_relacion
AND socios_consanguinidad.idsocios_consanguinidad=socios_relaciones.consanguinidad ";

$sqlb17 = "SELECT creditos_tgarantias.descripcion_tgarantias AS 'Tipo_de_Garantia',
creditos_tvaluacion.descripcion_tvaluacion AS 'Tipo_de_Valuacion',
creditos_garantias.documento_presentado AS 'Docto_o_Fact',
FORMAT(creditos_garantias.monto_valuado, 2) AS 'Monto_valuado',
creditos_garantias.fecha_adquisicion AS 'Fecha_de_Adquisicion'
FROM creditos_tvaluacion, creditos_garantias, creditos_tgarantias
WHERE creditos_tgarantias.idcreditos_tgarantias=creditos_garantias.tipo_garantia
AND creditos_tvaluacion.idcreditos_tvaluacion=creditos_garantias.tipo_valuacion";

$sqlb17_alt = "SELECT creditos_garantias.idcreditos_garantias AS 'ID',
creditos_tgarantias.descripcion_tgarantias AS 'Tipo_de_Garantia',
creditos_tvaluacion.descripcion_tvaluacion AS 'Tipo_de_Valuacion',
creditos_garantias.fecha_recibo AS 'Fecha_de_Recibo',
FORMAT(creditos_garantias.monto_valuado, 2) AS 'Monto_valuado', creditos_garantias.fecha_adquisicion AS 'Fecha_de_Adquisicion'
FROM creditos_tvaluacion, creditos_garantias, creditos_tgarantias
WHERE creditos_tgarantias.idcreditos_tgarantias=creditos_garantias.tipo_garantia
AND creditos_tvaluacion.idcreditos_tvaluacion=creditos_garantias.tipo_valuacion";

$sqlb17_ext = "SELECT creditos_tgarantias.descripcion_tgarantias AS 'Tipo_de_Garantia',
creditos_tvaluacion.descripcion_tvaluacion AS 'Tipo_de_Valuacion',
creditos_garantias.fecha_recibo AS 'Fecha_de_Recibo',
FORMAT(creditos_garantias.monto_valuado, 2) AS 'Monto_valuado',
creditos_garantias.fecha_adquisicion AS 'Fecha_de_Adquisicion',
creditos_garantias.documento_presentado AS 'Documento_Presentado',
creditos_garantias.descripcion AS 'Descripcion',
creditos_garantias.propietario AS 'Propietario'
FROM creditos_tvaluacion, creditos_garantias, creditos_tgarantias
WHERE creditos_tgarantias.idcreditos_tgarantias=creditos_garantias.tipo_garantia
AND creditos_tvaluacion.idcreditos_tvaluacion=creditos_garantias.tipo_valuacion";

$sqlb18 = "SELECT operaciones_mvtos.fecha_operacion AS 'Fecha_Operacion',
operaciones_mvtos.docto_afectado AS 'Documento',
operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
FORMAT(operaciones_mvtos.afectacion_real, 2) AS 'Monto',
operaciones_mvtos.valor_afectacion AS 'Afectacion'
FROM operaciones_mvtos, operaciones_tipos
WHERE operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion";

$sqlb18b = "SELECT operaciones_mvtos.fecha_operacion AS 'Fecha_Operacion',
operaciones_mvtos.docto_afectado AS 'Documento',
operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
FORMAT((operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion), 2) AS 'Monto'
FROM operaciones_mvtos, operaciones_tipos
WHERE operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion";

$sqlb18c = "SELECT operaciones_mvtos.fecha_operacion AS 'Fecha_Operacion',
operaciones_mvtos.fecha_afectacion AS 'Fecha_Aplicacion', operaciones_mvtos.docto_afectado AS 'Documento',
operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
FORMAT(operaciones_mvtos.afectacion_real, 2) AS 'Monto'
FROM operaciones_mvtos, operaciones_tipos
WHERE operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion";

$sqlb18d = "SELECT operaciones_mvtos.fecha_operacion AS 'Fecha_Operacion',
operaciones_mvtos.recibo_afectado AS 'Recibo', operaciones_mvtos.periodo_socio as 'parcialidad',
operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
FORMAT((operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion), 2) AS 'Monto',
operaciones_mvtos.detalles FROM operaciones_mvtos, operaciones_tipos
WHERE operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion";

$sqlb18e = "SELECT operaciones_mvtos.fecha_operacion AS 'Fecha_Operacion',
operaciones_mvtos.recibo_afectado AS 'Recibo',
operaciones_mvtos.periodo_socio as 'parcialidad',
operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
FORMAT((operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion), 2) AS 'Monto',
operaciones_mvtos.cadena_heredada
FROM operaciones_mvtos, operaciones_tipos
WHERE operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion";

$sqlb18d_15enero2007 = "SELECT operaciones_mvtos.idoperaciones_mvtos AS 'id',
operaciones_mvtos.fecha_operacion AS 'Fecha_Operacion',
operaciones_mvtos.recibo_afectado AS 'Recibo',
operaciones_mvtos.periodo_socio as 'parcialidad',
operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
(operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion) AS 'Monto',
operaciones_mvtos.detalles, operaciones_mvtos.tipo_operacion
FROM operaciones_mvtos, operaciones_tipos
WHERE operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion ";

$sqlb18e_15enero2007 = "SELECT operaciones_mvtos.idoperaciones_mvtos AS 'id',
operaciones_mvtos.fecha_operacion AS 'Fecha_Operacion',
operaciones_mvtos.recibo_afectado AS 'Recibo',
operaciones_mvtos.periodo_socio as 'parcialidad',
operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
(operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion) AS 'Monto',
operaciones_mvtos.cadena_heredada, operaciones_mvtos.tipo_operacion
FROM operaciones_mvtos, operaciones_tipos
WHERE operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion ";


/*$sqlb19 = "SELECT creditos_solicitud.numero_solicitud AS 'Solicitud',
creditos_solicitud.fecha_solicitud AS 'Fecha_Solicitud',
creditos_solicitud.fecha_autorizacion AS 'Fecha_Autorizacion',
format(creditos_solicitud.monto_solicitado,2) AS 'Monto_Autorizado',
creditos_modalidades.descripcion_modalidades AS 'Tipo_Credito',
creditos_periocidadpagos.descripcion_periocidadpagos AS 'Periocidad',
creditos_solicitud.pagos_autorizados AS 'Num_de_Pagos',
CONCAT((creditos_solicitud.tasa_interes * 100) , ' %') AS 'Tasa_de_Interes',
creditos_solicitud.fecha_vencimiento AS 'Fecha_de_Vencimiento',
creditos_estatus.descripcion_estatus AS 'Estatus'
FROM creditos_solicitud, creditos_modalidades, creditos_periocidadpagos,
creditos_estatus
WHERE creditos_estatus.estatus_actual=creditos_solicitud.estatus_actual
AND creditos_modalidades.tipo_credito=creditos_solicitud.tipo_credito
AND creditos_periocidadpagos.periocidad_de_pago=creditos_solicitud.periocidad_de_pago";*/

$sqlb19b = "SELECT socios_general.codigo,
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'Nombre_Completo',
creditos_solicitud.numero_solicitud AS 'Solicitud', creditos_solicitud.fecha_autorizacion AS 'Fecha_Autorizacion',
format(creditos_solicitud.monto_autorizado,2) AS 'Monto_Autorizado',
creditos_modalidades.descripcion_modalidades AS 'Tipo_Credito',
creditos_periocidadpagos.descripcion_periocidadpagos AS 'Periocidad',
creditos_solicitud.pagos_autorizados AS 'Num_de_Pagos',
CONCAT((creditos_solicitud.tasa_interes * 100) , ' %') AS  'Tasa_de_Interes',
creditos_solicitud.fecha_vencimiento AS 'Fecha_de_Vencimiento',
creditos_estatus.descripcion_estatus AS 'Estatus'
FROM socios_general, creditos_solicitud, creditos_modalidades, creditos_periocidadpagos,
creditos_estatus WHERE creditos_solicitud.numero_socio=socios_general.codigo
AND creditos_estatus.estatus_actual=creditos_solicitud.estatus_actual
AND creditos_modalidades.tipo_credito=creditos_solicitud.tipo_credito
AND creditos_periocidadpagos.periocidad_de_pago=creditos_solicitud.periocidad_de_pago";

$sql19b_ext ="SELECT socios_general.codigo,
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'Nombre_Completo',
operaciones_mvtos.docto_afectado AS 'Documento', operaciones_mvtos.recibo_afectado AS 'Recibo',
operaciones_mvtos.fecha_afectacion AS 'Fecha', operaciones_tipos.descripcion_operacion AS 'Tipo_Operacion',
FORMAT(operaciones_mvtos.afectacion_real,2) AS 'Monto'
FROM socios_general, operaciones_mvtos, operaciones_tipos
WHERE operaciones_mvtos.socio_afectado=socios_general.codigo AND operaciones_mvtos.tipo_operacion=operaciones_tipos.idoperaciones_tipos ";

$sqlb19c = "SELECT socios_general.codigo,
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'Nombre_Completo',
creditos_solicitud.numero_solicitud AS 'Solicitud', creditos_solicitud.fecha_solicitud AS 'Fecha_Solicitud',
FORMAT(creditos_solicitud.monto_solicitado,2) AS 'Monto_Solicitado',
creditos_modalidades.descripcion_modalidades AS 'Tipo_Credito',
creditos_periocidadpagos.descripcion_periocidadpagos AS 'Periocidad',
creditos_solicitud.numero_pagos AS 'Num_de_Pagos',
CONCAT((creditos_solicitud.tasa_interes * 100) , ' %') AS 'Tasa_de_Interes',
creditos_solicitud.fecha_vencimiento AS 'Fecha_de_Vencimiento',
creditos_estatus.descripcion_estatus AS 'Estatus'
FROM socios_general, creditos_solicitud, creditos_modalidades,
creditos_periocidadpagos, creditos_estatus WHERE creditos_solicitud.numero_socio=socios_general.codigo AND creditos_estatus.estatus_actual=creditos_solicitud.estatus_actual AND creditos_modalidades.tipo_credito=creditos_solicitud.tipo_credito AND creditos_periocidadpagos.periocidad_de_pago=creditos_solicitud.periocidad_de_pago ";

$sqlb19d = "SELECT socios_general.codigo,
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'Nombre_Completo',
socios_aeconomica.puesto AS 'Puesto', creditos_solicitud.numero_solicitud AS 'Solicitud', creditos_solicitud.fecha_solicitud AS 'Fecha_Solicitud', format(creditos_solicitud.monto_solicitado,2) AS 'Monto_Solicitado', creditos_modalidades.descripcion_modalidades AS 'Tipo_Credito', creditos_periocidadpagos.descripcion_periocidadpagos AS 'Periocidad', creditos_solicitud.numero_pagos AS 'Num_de_Pagos', CONCAT((creditos_solicitud.tasa_interes * 100) , ' %') AS 'Tasa_de_Interes', creditos_solicitud.fecha_vencimiento AS 'Fecha_de_Vencimiento', creditos_estatus.descripcion_estatus AS 'Estatus' FROM socios_general, creditos_solicitud, creditos_modalidades, creditos_periocidadpagos, creditos_estatus, socios_aeconomica WHERE creditos_solicitud.numero_socio=socios_general.codigo AND creditos_estatus.estatus_actual=creditos_solicitud.estatus_actual AND creditos_modalidades.tipo_credito=creditos_solicitud.tipo_credito AND creditos_periocidadpagos.periocidad_de_pago=creditos_solicitud.periocidad_de_pago  AND socios_aeconomica.socio_aeconomica=socios_general.codigo AND creditos_solicitud.saldo_actual>0 ";
/**
 *  Esta Funcion Agrega el Campo de Dependencia
 */
$sqlb19f = "SELECT socios_general.codigo, CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'Nombre_Completo', 
		creditos_solicitud.numero_solicitud AS 'Solicitud',
		creditos_solicitud.fecha_solicitud AS 'Fecha_Solicitud', 
		format(creditos_solicitud.monto_solicitado,2) AS 'Monto_Solicitado', 
		creditos_modalidades.descripcion_modalidades AS 'Tipo_Credito', 
		creditos_periocidadpagos.descripcion_periocidadpagos AS 'Periocidad', creditos_solicitud.numero_pagos AS 'Num_de_Pagos', 
		CONCAT((creditos_solicitud.tasa_interes * 100) , ' %') AS 'Tasa_de_Interes', creditos_solicitud.fecha_vencimiento AS 'Fecha_de_Vencimiento', 
		creditos_estatus.descripcion_estatus AS 'Estatus' , socios_aeconomica_dependencias.descripcion_dependencia AS 'dependencia' FROM socios_general, creditos_solicitud, creditos_modalidades, 
		creditos_periocidadpagos, creditos_estatus, socios_aeconomica_dependencias 
		WHERE creditos_solicitud.numero_socio=socios_general.codigo 
		AND creditos_estatus.estatus_actual=creditos_solicitud.estatus_actual AND creditos_modalidades.tipo_credito=creditos_solicitud.tipo_credito AND creditos_periocidadpagos.periocidad_de_pago=creditos_solicitud.periocidad_de_pago  AND socios_aeconomica_dependencias.idsocios_aeconomica_dependencias=socios_general.dependencia ";
/**
 * Variable que suma el monto solicitado
 */
$sqlb19g = "SELECT format(SUM(creditos_solicitud.monto_solicitado),2) AS 'suma_solicitudes' FROM creditos_solicitud  ";

$sqlb12v = "select operaciones_recibos.idoperaciones_recibos AS 'Numero_recibo',
operaciones_recibos.fecha_operacion AS 'Fecha_de_operacion', operaciones_recibostipo.descripcion_recibostipo AS 'Tipo_de_recibo',
operaciones_recibos.cheque_afectador AS 'Cheque' FROM operaciones_recibos NATURAL JOIN operaciones_recibostipo ";
$sqlb12v1 = "select operaciones_recibos.idoperaciones_recibos AS 'Numero_recibo',
operaciones_recibos.fecha_operacion AS 'Fecha_de_operacion', operaciones_recibostipo.descripcion_recibostipo AS 'Tipo_de_recibo',
operaciones_recibos.cheque_afectador AS 'Cheque' FROM operaciones_recibos, operaciones_recibostipo
WHERE operaciones_recibos.tipo_docto=operaciones_recibostipo.tipo_docto ";
$sqlb10 = "SELECT socios_general.codigo,
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'Nombre_Completo',
CONCAT('Calle ', socios_vivienda.calle, ' Num. ', socios_vivienda.numero_exterior, '-', socios_vivienda.numero_interior, ' Col. ', socios_vivienda.colonia, ', ', socios_vivienda.localidad) AS 'Domicilio_Completo', socios_vivienda.telefono_residencial FROM socios_general, socios_vivienda WHERE socios_general.codigo=socios_vivienda.socio_numero AND socios_vivienda.principal='1' ";

$sqlb11 = "select creditos_periodos.idcreditos_periodos AS 'codigo_de_periodo',
creditos_periodos.descripcion_periodos AS 'nombre_periodo',
creditos_periodos.fecha_inicial AS 'fecha_de_inicio',
creditos_periodos.fecha_final AS 'fecha_de_termino',
creditos_periodos.fecha_reunion AS 'fecha_de_reunion',
CONCAT(usuarios.nombres, ' ',usuarios.apellidopaterno, ' ', usuarios.apellidomaterno) AS 'oficial_responsable'
FROM creditos_periodos NATURAL JOIN usuarios ";

$sqlb12 = "select captacion_cuentas.numero_cuenta AS 'Codigo_de_Cuenta', captacion_cuentastipos.descripcion_cuentastipos AS 'Modalidad',
captacion_cuentas.fecha_apertura AS 'Fecha_de_Apertura',
captacion_tipotitulo.descripcion_tipotitulo AS 'Tipo_Titulo',
captacion_cuentas.saldo_cuenta, captacion_cuentas.tasa_otorgada AS 'Tasa',
CONCAT(captacion_cuentas.nombre_mancomunado1, ', ', captacion_cuentas.nombre_mancomunado2) AS 'Mancomunantes',
captacion_cuentas.observacion_cuenta AS 'Observaciones'
FROM captacion_cuentas, captacion_cuentastipos, captacion_tipotitulo
WHERE captacion_cuentas.tipo_cuenta=captacion_cuentastipos.tipo_cuenta AND captacion_cuentas.tipo_titulo=captacion_tipotitulo.tipo_titulo ";

$sqlb03_ext = "SELECT socios_aeconomica.idsocios_aeconomica AS 'Control', socios_aeconomica_tipos.nombre_taeconomica AS 'Tipo', socios_aeconomica_sector.descripcion_aeconomica_sector AS 'Sector', socios_aeconomica.nombre_ae AS 'Nombre_o_razon_social', CONCAT(socios_aeconomica.domicilio_ae, ', ', socios_aeconomica.localidad_ae, ', ', socios_aeconomica.estado_ae) AS 'Domicilio', CONCAT(socios_aeconomica.telefono_ae, ' Ext. ', socios_aeconomica.extension_ae) AS 'Telefono', numero_empleado AS 'Numero_Empleado', socios_aeconomica.puesto AS 'Puesto_Ocupado', socios_aeconomica.departamento_ae AS 'Departamento', socios_aeconomica.monto_percibido_ae AS 'Ingreso' FROM socios_aeconomica, socios_aeconomica_sector, socios_aeconomica_tipos WHERE socios_aeconomica.sector_economico=socios_aeconomica_sector.idsocios_aeconomica_sector AND socios_aeconomica.tipo_aeconomica=socios_aeconomica_tipos.idsocios_aeconomica_tipos ";
$sqlb40_b = " SELECT socios_aeconomica.idsocios_aeconomica AS 'ID', socios_aeconomica.nombre_ae AS 'Nombre_o_Denominacion', socios_aeconomica.domicilio_ae AS 'Domicilio', socios_aeconomica.telefono_ae AS 'Telefono', socios_aeconomica.departamento_ae AS 'Departamento' , socios_aeconomica.puesto AS 'Puesto' FROM socios_aeconomica ";

$sql_captacion_001 = "select socios.codigo, socios.nombre,
captacion_cuentas.numero_cuenta AS 'numero_de_cuenta',
captacion_cuentastipos.descripcion_cuentastipos AS 'modalidad',
captacion_cuentas.fecha_apertura AS 'fecha_de_apertura',
captacion_tipotitulo.descripcion_tipotitulo AS 'tipo_titulo',
captacion_cuentas.saldo_cuenta AS 'saldo', captacion_cuentas.tasa_otorgada
AS 'tasa',  CONCAT(captacion_cuentas.nombre_mancomunado1, ', ', captacion_cuentas.nombre_mancomunado2) AS 'mancomunantes',
captacion_cuentas.observacion_cuenta AS 'observaciones'
FROM captacion_cuentas, socios, captacion_cuentastipos, captacion_tipotitulo
 WHERE socios.codigo=captacion_cuentas.numero_socio AND captacion_cuentas.tipo_cuenta=captacion_cuentastipos.tipo_cuenta AND captacion_cuentas.tipo_titulo=captacion_tipotitulo.tipo_titulo ";

// SQL que devulve Sql Filtrado
function sqlb20($filter="") {
		$sqltmp = "select SUM(afectacion_real) AS 'Neto', periodo_socio, socio_afectado FROM operaciones_mvtos WHERE periodo_socio!=0  AND docto_neutralizador=1 $filter GROUP BY docto_afectado, periodo_socio";
		return $sqltmp;
}
function sqlb03($filter="") {
		$sqltmp = "SELECT periodo_socio AS 'Parcialidad', MAX(fecha_afectacion)
					AS 'Fecha_de_Pago', SUM(Afectacion_Real) AS 'Total_Parcialidad',
					 MAX(saldo_anterior) AS 'Saldo_Anterior_', MIN(saldo_actual) AS 'Saldo_actual_'
					FROM operaciones_mvtos WHERE
				$filter
				GROUP BY periodo_socio ORDER BY periodo_socio";
		return $sqltmp;
}
function sqlb03b($filter="") {
		$sqltmp = "SELECT periodo_socio AS 'Parcialidad', Afectacion_Real AS 'Total_Parcialidad' FROM operaciones_mvtos WHERE $filter GROUP BY periodo_socio ORDER BY periodo_socio";
		return $sqltmp;
}
//Arrays de tablas
$CTBL = array();
$CTBL['general8']['key']='idgeneral_folios';
$CTBL['general10']['name']='general_formulas';
$CTBL['general10']['key']='idgeneral_formulas';
$CTBL['general11']['name']='general_help';
$CTBL['general11']['key']='idgeneral_help';
$CTBL['general12']['name']='general_import';
$CTBL['general12']['key']='idgeneral_import';
$CTBL['general13']['name']='general_log';
$CTBL['general13']['key']='idgeneral_log';
$CTBL['general14']['name']='general_menu';
$CTBL['general14']['key']='idgeneral_menu';
$CTBL['general15']['name']='general_reports';
$CTBL['general15']['key']='idgeneral_reports';
$CTBL['general16']['name']='general_scoring_conceptos';
$CTBL['general16']['key']='idgeneral_scoring_conceptos';
$CTBL['general17']['name']='general_sql_stored';
$CTBL['general17']['key']='idgeneral_sql_stored';
$CTBL['general18']['name']='general_structure';
$CTBL['general18']['key']='idgeneral_structure';
$CTBL['general19']['name']='general_sucursales';
$CTBL['general19']['key']='idgeneral_sucursales';
$CTBL['general20']['name']='general_tmp';
$CTBL['general20']['key']='idgeneral_tmp';
$CTBL['general21']['name']='general_utilerias';
$CTBL['general21']['key']='idgeneral_utilerias';

$CTBL['inventarios0']['name']='inventarios_activos';
$CTBL['inventarios0']['key']='idinventarios_activos';
$CTBL['inventarios1']['name']='inventarios_activos_depreciaciones';
$CTBL['inventarios1']['key']='idinventarios_activos_depreciaciones';
$CTBL['inventarios2']['name']='inventarios_estatus';
$CTBL['inventarios2']['key']='idinventarios_estatus';
$CTBL['inventarios3']['name']='inventarios_familia_activos';
$CTBL['inventarios3']['key']='idinventarios_familia_activos';
$CTBL['inventarios4']['name']='inventarios_motivos_baja';
$CTBL['inventarios4']['key']='idinventarios_motivos_baja';
$CTBL['inventarios5']['name']='inventarios_otras_caracteristicas';
$CTBL['inventarios5']['key']='idinventarios_otras_caracteristicas';
$CTBL['inventarios6']['name']='inventarios_proveedores';
$CTBL['inventarios6']['key']='idinventarios_proveedores';
$CTBL['inventarios7']['name']='inventarios_razones_baja';
$CTBL['inventarios7']['key']='idinventarios_razones_baja';
$CTBL['inventarios8']['name']='inventarios_unidades_medida';
$CTBL['inventarios8']['key']='idinventarios_unidades_medida';

$CTBL['nominas0']['name']='nominas';
$CTBL['nominas0']['key']='idnominas';
$CTBL['nominas1']['name']='nominas_bases_integracion';
$CTBL['nominas1']['key']='idnominas_bases_integracion';
$CTBL['nominas2']['name']='nominas_bases_integracion_historica';
$CTBL['nominas2']['key']='idnominas_bases_integracion_historica';
$CTBL['nominas3']['name']='nominas_conceptos';
$CTBL['nominas3']['key']='idnominas_conceptos';
$CTBL['nominas4']['name']='nominas_conceptos_subsidio_na';
$CTBL['nominas4']['key']='idnominas_conceptos_subsidio_na';
$CTBL['nominas5']['name']='nominas_integracion_bases';
$CTBL['nominas5']['key']='idnominas_integracion_bases';
$CTBL['nominas6']['name']='nominas_integracion_subsidio_na';
$CTBL['nominas6']['key']='idnominas_integracion_subsidio_na';
$CTBL['nominas7']['name']='nominas_movimientos';
$CTBL['nominas7']['key']='idnominas_movimientos';

$CTBL['operaciones0']['name']='operaciones';
$CTBL['operaciones0']['key']='idoperaciones';

$CTBL['operaciones2']['name']='operaciones_detalle';
$CTBL['operaciones2']['key']='idoperaciones_detalle';
$CTBL['operaciones3']['name']='operaciones_detalle_ne';
$CTBL['operaciones3']['key']='idoperaciones_detalle_ne';
$CTBL['operaciones4']['name']='operaciones_mvtos';
$CTBL['operaciones4']['key']='idoperaciones_mvtos';
$CTBL['operaciones5']['name']='operaciones_mvtosestatus';
$CTBL['operaciones5']['key']='idoperaciones_mvtosestatus';
$CTBL['operaciones6']['name']='operaciones_recibos';
$CTBL['operaciones6']['key']='idoperaciones_recibos';
$CTBL['operaciones7']['name']='operaciones_recibostipo';
$CTBL['operaciones7']['key']='idoperaciones_recibostipo';
$CTBL['operaciones8']['name']='operaciones_sumas';
$CTBL['operaciones8']['key']='idoperaciones_sumas';
$CTBL['operaciones9']['name']='operaciones_tipos';
$CTBL['operaciones9']['key']='idoperaciones_tipos';

$CTBL['seguimiento0']['name']='seguimiento_compromisos';
$CTBL['seguimiento0']['key']='idseguimiento_compromisos';
$CTBL['seguimiento1']['name']='seguimiento_llamadas';
$CTBL['seguimiento1']['key']='idseguimiento_llamadas';
$CTBL['seguimiento2']['name']='seguimiento_notificaciones';
$CTBL['seguimiento2']['key']='idseguimiento_notificaciones';

$CTBL['socios0']['name']='socios';
$CTBL['socios0']['key']='idsocios';
$CTBL['socios1']['name']='socios_aeconomica';
$CTBL['socios1']['key']='idsocios_aeconomica';
$CTBL['socios2']['name']='socios_aeconomica_dependencias';
$CTBL['socios2']['key']='idsocios_aeconomica_dependencias';
$CTBL['socios3']['name']='socios_aeconomica_sector';
$CTBL['socios3']['key']='idsocios_aeconomica_sector';
$CTBL['socios4']['name']='socios_aeconomica_tipos';
$CTBL['socios4']['key']='idsocios_aeconomica_tipos';
$CTBL['socios5']['name']='socios_aportaciones';
$CTBL['socios5']['key']='idsocios_aportaciones';
$CTBL['socios6']['name']='socios_aportacionesorigen';
$CTBL['socios6']['key']='idsocios_aportacionesorigen';
$CTBL['socios7']['name']='socios_baja';
$CTBL['socios7']['key']='idsocios_baja';
$CTBL['socios8']['name']='socios_baja_razones';
$CTBL['socios8']['key']='idsocios_baja_razones';
$CTBL['socios9']['name']='socios_cajalocal';
$CTBL['socios9']['key']='idsocios_cajalocal';
$CTBL['socios10']['name']='socios_consanguinidad';
$CTBL['socios10']['key']='idsocios_consanguinidad';
$CTBL['socios11']['name']='socios_estadocivil';
$CTBL['socios11']['key']='idsocios_estadocivil';
$CTBL['socios12']['name']='socios_estatus';
$CTBL['socios12']['key']='idsocios_estatus';
$CTBL['socios13']['name']='socios_general';
$CTBL['socios13']['key']='idsocios_general';
$CTBL['socios14']['name']='socios_genero';
$CTBL['socios14']['key']='idsocios_genero';
$CTBL['socios15']['name']='socios_grupossolidarios';
$CTBL['socios15']['key']='idsocios_grupossolidarios';
$CTBL['socios16']['name']='socios_memo';
$CTBL['socios16']['key']='idsocios_memo';
$CTBL['socios17']['name']='socios_memotipos';
$CTBL['socios17']['key']='idsocios_memotipos';
$CTBL['socios18']['name']='socios_patrimonio';
$CTBL['socios18']['key']='idsocios_patrimonio';
$CTBL['socios19']['name']='socios_patrimonioestatus';
$CTBL['socios19']['key']='idsocios_patrimonioestatus';
$CTBL['socios20']['name']='socios_patrimoniotipo';
$CTBL['socios20']['key']='idsocios_patrimoniotipo';
$CTBL['socios21']['name']='socios_regimenvivienda';
$CTBL['socios21']['key']='idsocios_regimenvivienda';
$CTBL['socios22']['name']='socios_region';
$CTBL['socios22']['key']='idsocios_region';
$CTBL['socios23']['name']='socios_relaciones';
$CTBL['socios23']['key']='idsocios_relaciones';
$CTBL['socios24']['name']='socios_relacionesestatus';
$CTBL['socios24']['key']='idsocios_relacionesestatus';
$CTBL['socios25']['name']='socios_relacionestipos';
$CTBL['socios25']['key']='idsocios_relacionestipos';
$CTBL['socios26']['name']='socios_tiempo';
$CTBL['socios26']['key']='idsocios_tiempo';
$CTBL['socios27']['name']='socios_tipoingreso';
$CTBL['socios27']['key']='idsocios_tipoingreso';
$CTBL['socios28']['name']='socios_vivienda';
$CTBL['socios28']['key']='idsocios_vivienda';
$CTBL['socios29']['name']='socios_viviendatipo';
$CTBL['socios29']['key']='idsocios_viviendatipo';

$CTBL['tarifas1']['name']='tarifas_credito_al_salario';
$CTBL['tarifas1']['key']='idtarifas_credito_al_salario';
$CTBL['tarifas2']['name']='tarifas_credito_al_salario_anual';
$CTBL['tarifas2']['key']='idtarifas_credito_al_salario_anual';
$CTBL['tarifas3']['name']='tarifas_deduccion_isr_salarios';
$CTBL['tarifas3']['key']='idtarifas_deduccion_isr_salarios';
$CTBL['tarifas4']['name']='tarifas_deduccion_isr_salarios_anual';
$CTBL['tarifas4']['key']='idtarifas_deduccion_isr_salarios_anual';
$CTBL['tarifas5']['name']='tarifas_isr_salarios';
$CTBL['tarifas5']['key']='idtarifas_isr_salarios';
$CTBL['tarifas6']['name']='tarifas_isr_salarios_anual';
$CTBL['tarifas6']['key']='idtarifas_isr_salarios_anual';
$CTBL['tarifas7']['name']='tarifas_subsidio_isr_salarios';
$CTBL['tarifas7']['key']='idtarifas_subsidio_isr_salarios';
$CTBL['tarifas8']['name']='tarifas_subsidio_isr_salarios_anual';
$CTBL['tarifas8']['key']='idtarifas_subsidio_isr_salarios_anual';

$CTBL['trabajador0']['name']='trabajador_asistencia';
$CTBL['trabajador0']['key']='idtrabajador_asistencia';
$CTBL['trabajador1']['name']='trabajador_conceptos';
$CTBL['trabajador1']['key']='idtrabajador_conceptos';
$CTBL['trabajador2']['name']='trabajador_faltas';
$CTBL['trabajador2']['key']='idtrabajador_faltas';
$CTBL['trabajador3']['name']='trabajador_general';
$CTBL['trabajador3']['key']='idtrabajador_general';
$CTBL['trabajador4']['name']='trabajador_historico_salarios';
$CTBL['trabajador4']['key']='idtrabajador_historico_salarios';
$CTBL['trabajador5']['name']='trabajador_inasistencias';
$CTBL['trabajador5']['key']='idtrabajador_inasistencias';

$CTBL['usuarios0']['name']='usuarios';
$CTBL['usuarios0']['key']='idusuarios';
$CTBL['usuarios1']['name']='usuarios_web';
$CTBL['usuarios1']['key']='idusuarios_web';
$CTBL['usuarios2']['name']='usuarios_web_connected';
$CTBL['usuarios2']['key']='idusuarios_web_connected';
class csSQL{
	function __construct($sql){

	}
}
?>