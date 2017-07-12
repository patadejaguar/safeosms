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

//$xH			= new cHObject(HP_SERVICE);
$xInit          = new cHPage(HP_SERVICE );
//header("Content-type: text/xml");
header('Content-type: application/json');
$txt		    = "";
$action		    = "";
$DDATA          = $_REQUEST;
$action         = isset($DDATA["action"]) ? $DDATA["action"] : SVC_LIST;
$arg            = parametro("arg1", 0, MQL_FLOAT);
$arg2           = parametro("arg2", AML_CLAVE_MONEDA_LOCAL);
$rs				= array();

$money			= new cTesoreria_monedas();
$money->setData(  $money->query()->initByID($arg2) );
$valor_local	= $money->quivalencia_en_moneda_local()->v();

$valor				= $arg * $valor_local;
$letras				= convertirletras($valor, $arg2);
$rs["equivalencia"]	= $valor;
$rs["letras"]		= $letras;
$rs["cotizacion"]	= $valor_local;

echo json_encode($rs);
//echo "{ \"valor\" : $valor, \"letras\" : \"$letras\" }";
//$xLog       = new cSystemLog();
//$xLog->setRotate();
//$xLog->setSave($sql);
//$xT->clave_de_actividad()
//print json_encode($jTableResult);
/*
echo "<?xml version =\"1.0\" ?>\n<data>\n" . $txt . "</data>";
*/
?>