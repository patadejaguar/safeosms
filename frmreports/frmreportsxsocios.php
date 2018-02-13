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
$xHP					= new cHPage("TR.Reportes por persona", HP_FORM);
$oficial 				= elusuario($iduser);

$xHP->init();

$xPanel					= new cPanelDeReportes(iDE_SOCIO, "socios", false);
$xPanel->OFRM()->addPersonaBasico();


$xPanel->addListReports();
$xPanel->setConOperacion(true);
$xPanel->setConCajero(false);
$xPanel->setConCreditos(false);
$xPanel->setConRecibos(false);
$xPanel->addjsVars("idsocio", "idsocio");
$xPanel->addjsVars("idsocio", "persona");

$xPanel->setTitle($xHP->getTitle());


echo $xPanel->get();
echo $xPanel->getJs(true);

$xHP->fin();

?>