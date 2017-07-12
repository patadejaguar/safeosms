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
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();

$fecha		= parametro("fecha", false, MQL_DATE);
//$cajero		= parametro("cajero", getUsuarioActual(), MQL_INT);
$valor		= parametro("valor", 0, MQL_FLOAT);
$numero		= parametro("numero", 0, MQL_INT);
$notas		= parametro("notas");
$caja		= parametro("caja", "", MQL_RAW);
$documento	= parametro("documento", "");
$xCaja		= new cCaja();
//$xCaja->initByFechaUsuario($fecha, $cajero);
//$idcaja		= $xCaja->getKey();
$xArq		= new cCajaArqueos($caja);
$hora		= time();
$rs			= array();
if($numero > 0 AND $valor > 0){
	$xArq->addValorArqueado($valor, $numero, $documento, $notas, $fecha, $hora);
	$rs		= array("error" => false, "message" => $xArq->getMessages(), "arqueado" => $xArq->getValoresArqueados($fecha));
} else {
	$rs		= array("error" => true, "message" => "ERROR\t$valor, $numero, $documento, $notas, $fecha, $hora", "arqueado" => 0);
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>