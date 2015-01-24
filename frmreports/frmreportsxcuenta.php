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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

$xHP->init();

$xPanel		= new cPanelDeReportes(iDE_CAPTACION, "captacion_corriente", false);
$xPanel->OFRM()->addCuentaCaptacionBasico(true, CAPTACION_TIPO_VISTA);
$xPanel->OFRM()->addJsBasico(iDE_CAPTACION);
$xPanel->addListReports();
$xPanel->setConOperacion(false);
$xPanel->setConCajero(false);
$xPanel->setConCreditos(false);
$xPanel->setConRecibos(false);
$xPanel->addjsVars("idcuenta", "cuenta");
echo $xPanel->get();
echo $xPanel->getJs(true);

?>