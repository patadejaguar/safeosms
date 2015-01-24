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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../reports/PHPReportMaker.php");
$oficial = elusuario($iduser);
//=====================================================================================================
/**
 * Filtrar si Existe Caja Local
 */
$caja_local 			= $_GET["pa3"];
$si_es_caja_local 		= "  AND socios.numero_caja_local=$caja_local ";
if (!isset($caja_local) OR $caja_local == "todas" ) {
	$si_es_caja_local 	= ""	;
}
//$fecha_final = $_GET["off"];
$f3 					= $_GET["f3"];
$input 					= $_GET["out"];
	//$si_es_por_fecha
	if (!$input) {
		$input = "default";
	}


	/* ******************************************************************************/


	$setSql = "
	SELECT socios_cajalocal.idsocios_cajalocal AS 'id',
		socios.numero_caja_local,
		socios_cajalocal.sucursal,
		socios_cajalocal.descripcion_cajalocal AS 'caja_local',
		socios.nombre, creditos.*
	FROM
	`socios_cajalocal` `socios_cajalocal` 
		INNER JOIN `socios` `socios` 
		ON `socios_cajalocal`.`idsocios_cajalocal` = `socios`.
		`numero_caja_local` 
			INNER JOIN `creditos` `creditos` 
			ON `socios`.`codigo` = `creditos`.`numero_socio`
			
			WHERE creditos.saldo_actual>=" . TOLERANCIA_SALDOS . "
			$si_es_caja_local
			
			ORDER BY
				`socios_cajalocal`.`idsocios_cajalocal`
	";


if ($input!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report31.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
	
	$xHEx	= new cHExcel();
	echo $xHEx->convertTable($setSql);
	
	/*$filename = $_SERVER['SCRIPT_NAME'];
	$filename = str_replace(".php", "", $filename);
	$filename = str_replace("rpt", "", $filename);
	$filename = str_replace("-", "", 	$filename);
  	$filename = "$filename-" . date("YmdHi") . "-from-" .  $iduser . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");

	$cTbl = new cTabla($setSql);
	$cTbl->setWidth();
	$cTbl->Show("", false);*/
}
?>