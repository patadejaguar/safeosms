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
$xHP		= new cHPage("TR.Reportes por CAJA_LOCAL");
$xHP->init();

$xRPT		= new cPanelDeReportes("cajas_locales", "cajalocal", false);
$xRPT->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
if( SISTEMA_CAJASLOCALES_ACTIVA == true ){
	$xSelCL	= $xSel->getListaDeCajasLocales("cajalocal", false, SYS_TODAS);
	$xSelCL->setDivClass("txmon");
	$xSelCL->addEspOption(SYS_TODAS, SYS_TODAS);
	$xRPT->OFRM()->addDivSolo( $xSelCL->get(true), "" );
	$xRPT->addjsVars("cajalocal", "cajalocal");
} else {
	$xRPT->setConSucursal(false);
}


$xRPT->setConSucursal(true);

$xRPT->addListReports();
//$xRPT->addFechaInicial();
//$xRPT->addFechaFinal();

$xRPT->setConOperacion(false);
$xRPT->setConCajero(false);
$xRPT->setConRecibos(false);

echo $xRPT->get();

echo $xRPT->getJs(true);

$xHP->fin();

?>