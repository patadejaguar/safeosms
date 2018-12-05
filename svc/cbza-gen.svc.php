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
$xSeg		= new cSeguimiento();
$xSegN		= new cSeguimientoNotificaciones();
$xSuc		= new cSucursal();

//$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT);
$letra		= parametro("letra", false, MQL_INT);
$cmd		= parametro("cmd", SYS_NINGUNO);



$interes	= parametro("interes", 0, MQL_FLOAT);
$moratorio	= parametro("moratorio", 0, MQL_FLOAT);
$impuestos	= parametro("impuestos", 0, MQL_FLOAT);
$otros		= parametro("otros", 0, MQL_FLOAT);
$capital	= parametro("capital", 0, MQL_FLOAT);

$oficial	= parametro("idoficial", getUsuarioActual(), MQL_INT);
$formato	= parametro("idformato", 0, MQL_INT);
$canal		= parametro("idtipocanal", $xSegN->CANAL_PERSONAL, MQL_RAW);
$hora		= parametro("idhora", $xSuc->getHorarioDeEntrada(), MQL_RAW);
$enviarnow	= parametro("idahora", false, MQL_BOOL);
$periodos	= parametro("periodos", 0, MQL_INT);
$observaciones= parametro("idobservaciones");


$cmd		= strtolower($cmd);
//$tipo		= parametro("tipo", false, MQL_INT);
$rs			= array();
//header('Content-type: application/json');
//echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);


switch ($cmd){
	case $xSeg->T_COMPROMISO:
		
		$res	= $xSegN->add($credito, $fecha, $hora, $total, $observaciones, $oficial, $canal, $formato, $capital, $interes, $moratorio, $otros, $impuestos, $periodos);
		
	break;
	case $xSeg->T_NOTIFICACION:
		
	break;
	case $xSeg->T_LLAMADA:
		
	break;
}

?>