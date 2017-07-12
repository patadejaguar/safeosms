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
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
$xInit->cors();
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();

$tabla		= parametro("tabla", false, MQL_RAW);
$clave		= parametro("id", false, MQL_RAW);

$rs			= array();
$xSys		= new cTableStructure($tabla);
$xSys->setActualizar();
header('Content-type: application/json');

echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>