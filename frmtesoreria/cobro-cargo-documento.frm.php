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
$xP		= new cHPage("Cobranza.- Efectivo");
$xP->setIncludes();
echo $xP->getHeader();

$xP->setBodyinit();

$xFrm	= new cHForm("frmCobrosEnEfectivo", "cobro-efectivo.frm.php");

$xTxt	= new cHText("id");
//$xTxt->setIncludeLabel();

$xFrm->addHElem( $xTxt->getDeMoneda("iMontoOperacion", "Monto de la Operacion") );
$xFrm->addHElem( $xTxt->getDeMoneda("iMontoRecibido", "Monto Recibido") );
$xFrm->addHElem( $xTxt->getDeMoneda("iMontoCambio", "Monto de Cambio") );
echo $xFrm->get();
$xP->setBodyEnd();

?>