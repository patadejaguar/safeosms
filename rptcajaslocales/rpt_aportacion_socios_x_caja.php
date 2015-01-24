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
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

$oficial = elusuario($iduser);

$cl_on 			= $_GET["pa3"];
$xestatus 		= $_GET["f70"];
$input 			= $_GET["out"];

$ByCL 			= " AND socios_cajalocal.idsocios_cajalocal=$cl_on ";
if ( !isset($cl_on) OR $cl_on == "todas" ){
	$ByCL		= "";
}

if(!$input){
	$input = "default";
}
//.- Selecciona el PAth de php reports

   include_once "../reports/PHPReportMaker.php";

	$sql_set = "SELECT socios_cajalocal.idsocios_cajalocal AS 'numero_de_caja',
	socios_cajalocal.descripcion_cajalocal AS 'caja_local',
	socios_general.codigo,
	CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto) AS 'nombre_completo',
	socios_tipoingreso.descripcion_tipoingreso AS 'tipo_de_ingreso',
	socios_general.rfc,  socios_general.curp,
	SUM(operaciones_mvtos.afectacion_real) AS 'total_aportado'
	FROM
		`socios_general` `socios_general`
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
		ON `socios_general`.`codigo` = `operaciones_mvtos`.`socio_afectado`
			INNER JOIN `operaciones_tipos` `operaciones_tipos`
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos`
				INNER JOIN `socios_cajalocal` `socios_cajalocal`
				ON `socios_general`.`cajalocal` = `socios_cajalocal`.
				`idsocios_cajalocal`
					INNER JOIN `socios_tipoingreso` `socios_tipoingreso`
					ON `socios_general`.`tipoingreso` = `socios_tipoingreso`.
					`idsocios_tipoingreso`
	WHERE

	operaciones_tipos.constituye_fondo_automatico='1'
	$ByCL
	GROUP BY socios_cajalocal.idsocios_cajalocal,
			socios_general.codigo";


if ($input!=OUT_EXCEL) {
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($sql_set);
	$oRpt->setXML("../repository/report12.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($sql_set, "", "fieldnames");
}
?>