<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once( "../core/entidad.datos.php");
include_once( "../core/core.deprecated.inc.php");
include_once( "../core/core.fechas.inc.php");
include_once( "../libs/sql.inc.php");
include_once( "../core/core.config.inc.php");
include_once( "../reports/PHPReportMaker.php");
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$on_f = $_GET["on"];
$off_f = $_GET["off"];
$f1 = $_GET["f1"];
$f2 = $_GET["f2"];
$input = "default";



$setSql = "SELECT socios_general.codigo, CONCAT(socios_general.nombrecompleto, ' ', socios_general.apellidopaterno, ' ', socios_general.apellidomaterno) AS 'nombre_completo',  ";
$setSql .= " socios_tipoingreso.descripcion_tipoingreso AS 'tipo_de_ingreso', creditos_tipoconvenio.descripcion_tipoconvenio AS 'tipo_de_convenio', creditos_solicitud.numero_solicitud AS 'numero_de_solicitud', ";
$setSql .= " creditos_estatus.descripcion_estatus AS 'estatus', creditos_solicitud.fecha_ministracion AS 'fecha_de_ministracion', creditos_solicitud.fecha_vencimiento AS 'fecha_de_vencimiento', ";
$setSql .= " creditos_solicitud.monto_autorizado AS 'monto_ministrado',creditos_periocidadpagos.descripcion_periocidadpagos AS 'frecuencia_de_pagos',  creditos_solicitud.pagos_autorizados AS 'numero_de_pagos' ";
$setSql .= "FROM socios_general, socios_tipoingreso, creditos_solicitud, creditos_tipoconvenio, creditos_periocidadpagos, creditos_estatus ";
$setSql .= "WHERE creditos_solicitud.numero_socio=socios_general.codigo AND socios_tipoingreso.idsocios_tipoingreso=socios_general.tipoingreso ";
$setSql .= "AND creditos_solicitud.tipo_convenio=creditos_tipoconvenio.idcreditos_tipoconvenio AND creditos_periocidadpagos.idcreditos_periocidadpagos=creditos_solicitud.periocidad_de_pago ";
$setSql .= "AND creditos_estatus.idcreditos_estatus=creditos_solicitud.estatus_actual ";
$setSql .= " AND creditos_solicitud.estatus_actual=$f2 ";
$setSql .=  " AND creditos_solicitud.fecha_vencimiento>='$on_f' AND  creditos_solicitud.fecha_vencimiento<='$off_f' ";

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report17.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
?>