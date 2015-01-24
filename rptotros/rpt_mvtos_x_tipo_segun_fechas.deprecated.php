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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
include_once("../reports/PHPReportMaker.php");


$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 	= $_GET["on"];
$fecha_final 	= $_GET["off"];
$f3 			= $_GET["f3"];
$input 			= $_GET["out"];
$f50 			= $_GET["f50"];
//SQL Extras
$f_x_soc = "";
	if (!$input) {
		$input = "default";
	}
	if($f50){
		$f_x_soc = " AND operaciones_mvtos.socio_afectado=$f50 ";
	}



	$setSql = " SELECT operaciones_mvtos.socio_afectado AS 'numero_de_socio',
			CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto)
			AS 'nombre_completo',
			operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion', operaciones_mvtos.fecha_afectacion AS 'fecha',
			operaciones_mvtos.docto_afectado AS 'documento', operaciones_mvtos.afectacion_real AS 'monto', ";
	$setSql .= " operaciones_mvtos.saldo_actual AS 'saldo', operaciones_mvtos.detalles AS 'observaciones', ";
	$setSql .= " operaciones_mvtos.tasa_asociada, operaciones_mvtos.dias_asociados FROM socios_general, operaciones_mvtos, operaciones_tipos ";
	$setSql .= " WHERE operaciones_mvtos.socio_afectado=socios_general.codigo AND ";
	$setSql .= " operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion ";
	$setSql .= " AND operaciones_mvtos.tipo_operacion=$f3 ";
	$setSql .= "AND operaciones_mvtos.fecha_afectacion>='$fecha_inicial' AND operaciones_mvtos.fecha_afectacion<='$fecha_final' ";
	$setSql .= " $f_x_soc
				ORDER BY operaciones_mvtos.fecha_operacion";

	//echo $setSql; exit;

if ($input!=OUT_EXCEL) {

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report21.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($setSql, "", "fieldnames");
}
?>
