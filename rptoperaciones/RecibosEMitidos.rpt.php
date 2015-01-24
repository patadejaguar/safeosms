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
include_once "../reports/PHPReportMaker.php";


$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 			= $_GET["on"];
$fecha_final 			= $_GET["off"];
$f3 					= $_GET["f3"];							// Numero de Cajero

$ByCajero				= "";
$ByAll					= "";

$input = $_GET["out"];
	if (!$input) {
		$input = "default";
	}

$setSql = "SELECT operaciones_recibos.*,
		socios.nombre,
		operaciones_recibostipo.descripcion_recibostipo AS 'tipo_de_recibo'

FROM operaciones_recibos, socios, operaciones_recibostipo
WHERE socios.codigo=operaciones_recibos.numero_socio
		AND operaciones_recibostipo.idoperaciones_recibostipo=operaciones_recibos.tipo_docto
		AND operaciones_recibostipo.mostrar_en_corte!='0'
		AND operaciones_recibos.idusuario=$f3
		AND operaciones_recibostipo.mostrar_en_corte!='0'
		AND operaciones_recibos.fecha_operacion>='$fecha_inicial'
		AND operaciones_recibos.fecha_operacion<='$fecha_final'
ORDER BY 	operaciones_recibos.fecha_operacion, operaciones_recibos.tipo_pago,
			operaciones_recibos.tipo_docto,
			socios.codigo ";


if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report38B.xml");
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