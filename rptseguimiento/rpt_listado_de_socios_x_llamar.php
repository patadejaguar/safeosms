<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @since 2008-04-08
 * @version 1.0
 * @package seguimiento
 *  Listado de Socios por llamar
 * 		2008-04-08 Crecaion
 *
 */
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../reports/PHPReportMaker.php");


$socio_inicial 		= $_GET["on"];
$socio_final 		= $_GET["off"];
$f1 				= $_GET["f1"];
$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}




	$sql_set = "SELECT creditos_solicitud.numero_socio,CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto) AS 'nombre_completo', ";
	$sql_set .= " socios_vivienda.telefono_residencial, socios_vivienda.telefono_movil, creditos_solicitud.numero_solicitud, ";
	$sql_set .= " creditos_solicitud.fecha_ministracion, COUNT(operaciones_mvtos.idoperaciones_mvtos) as 'letras_vencidas', ";
	$sql_set .= " SUM(operaciones_mvtos.afectacion_real * operaciones_tipos.afectacion_en_notificacion) AS 'total' ";
	$sql_set .= " FROM socios_general,socios_vivienda, creditos_solicitud, operaciones_mvtos, operaciones_tipos ";
 	$sql_set .= " WHERE  creditos_solicitud.numero_socio=socios_general.codigo AND socios_vivienda.socio_numero=socios_general.codigo AND  ";
 	$sql_set .= " socios_vivienda.principal='1' AND operaciones_mvtos.docto_afectado=creditos_solicitud.numero_solicitud AND operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion ";
 	$sql_set .= " AND operaciones_mvtos.periodo_socio!=0 AND operaciones_tipos.afectacion_en_notificacion!=0 AND operaciones_mvtos.estatus_mvto=50 AND ";
 	$sql_set .= " operaciones_mvtos.docto_neutralizador=1 ";
	//$sql_set .= " AND creditos_solicitud.periocidad_de_pago=$frecuencia_pagos ";
	$sql_set .= " AND creditos_solicitud.numero_socio>=$socio_inicial AND creditos_solicitud.numero_socio<=$socio_final ";
	$sql_set .= " GROUP BY creditos_solicitud.numero_solicitud ";
	$sql_set .= " HAVING letras_vencidas=1 ORDER BY creditos_solicitud.numero_socio  LIMIT 0,1000";

	exit($setSql);
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report23.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/

?>
