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
include_once ("../core/entidad.datos.php");
include_once ("../core/core.deprecated.inc.php");
include_once ("../core/core.fechas.inc.php");
include_once ("../core/core.config.inc.php");
include_once ("../core/core.common.inc.php");

header("Content-type: text/xml");
$txt		= "";
$iSQL		= new cSQLListas();
$xQL		= new MQL();
$xF			= new cFecha();

$persona	= isset($_REQUEST["persona"]) ? $_REQUEST["persona"] : "";
$docto		= isset($_REQUEST["documento"]) ? $_REQUEST["documento"] : "";
$fecha		= parametro("fecha", false, MQL_RAW);
$mx			= isset($_REQUEST["mx"]) ? true : false;

$periodo	= parametro("periodo", 0, MQL_INT);
if($fecha === false){
	$fecha	= "";
} else {
	$fecha	= $xF->getFechaISO($fecha);
}


$rs		= $xQL->getRecordset($iSQL->getListadoDeRecibos("", $persona, $docto, $fecha, "", "", $periodo));
//setLog($iSQL->getListadoDeRecibos("", $persona, $docto, $fecha, "", "", $periodo));

while($rw	= $rs->fetch_assoc()){
	$txt	.= "<recibo codigo=\"" . $rw["numero"] . "\" fecha=\"" . $rw["fecha"] . "\" documento=\"" . $rw["documento"] . "\" periodo=\"" . $rw["periodo"] . "\" persona=\"" . $rw["socio"] . "\">";
	$txt	.= "" . $rw["total"];
	$txt	.= "</recibo>\n";
}

echo "<?xml version =\"1.0\" ?>\n<result>\n" . $txt . "</result>";
?>