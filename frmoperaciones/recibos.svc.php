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

$persona	= isset($_REQUEST["persona"]) ? $_REQUEST["persona"] : "";
$docto		= isset($_REQUEST["documento"]) ? $_REQUEST["documento"] : "";
$fecha		= isset($_REQUEST["fecha"]) ? $_REQUEST["fecha"] : fechasys();
$mx			= isset($_REQUEST["mx"]) ? true : false;
$xF			= new cFecha();
if($mx == true){
		$fecha	= $xF->getFechaISO($fecha);
}

$rs		= getRecordset($iSQL->getListadoDeRecibos("", $persona, $docto, $fecha));


while($rw	= mysql_fetch_array($rs)){
	$txt	.= "<recibo codigo=\"" . $rw["numero"] . "\" fecha=\"" . $rw["fecha"] . "\" documento=\"" . $rw["documento"] . "\" persona=\"" . $rw["socio"] . "\">";
	$txt	.= "" . $rw["total"];
	$txt	.= "</recibo>\n";
}

echo "<?xml version =\"1.0\" ?>\n<result>\n" . $txt . "</result>";
?>