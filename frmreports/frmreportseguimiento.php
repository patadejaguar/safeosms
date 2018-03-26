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
$xHP		= new cHPage("TR.Reportes de Seguimiento_de_Cartera");

$xRuls		= new cReglaDeNegocio();
$ConEntFed	= $xRuls->getValorPorRegla($xRuls->reglas()->REPORTES_USAR_EFED);

$xRPT		= new cPanelDeReportes(iDE_CREDITO, "seguimiento");
$xRPT->setTitle($xHP->getTitle());
$xRPT->addOficialDeCredito();
$xRPT->setConOperacion(true);
//$xRPT->addTipoDeOperacion();
//$xRPT->setConRecibos(false);
//$xRPT->OFRM()->OCheck("TR.NO OMITIR NINGUNO");
$xRPT->addCheckBox("TR.OMITIRCEROS", "nocero", true);

if($ConEntFed == true){
	$xRPT->addMunicipiosActivos();
}


$xHP->init();

echo $xRPT->get();

echo $xRPT->getJs(true);

$xHP->fin();

?>