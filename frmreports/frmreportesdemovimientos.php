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

$xHP		= new cHPage("TR.Reportes de Operaciones");
$xRuls		= new cReglaDeNegocio();
$ConEntFed	= $xRuls->getValorPorRegla($xRuls->reglas()->REPORTES_USAR_EFED);

$xHP->init();

$xChk		= new cHCheckBox();

$xB			= new cPanelDeReportes(iDE_OPERACION, "general_operaciones");

$xB->setTitle( $xHP->getTitle() );
$xB->setConOperacion();
$xB->setConRecibos();
$xB->setConCreditos(true);
$xB->addOpciones($xB->OPTS_CREDSNOPERI);
$xB->addOpciones($xB->OPTS_CREDSNOSTAT);
$xB->addOpciones($xB->OPTS_NOUSUARIOS);
$xB->addOficialDeCredito();
$xB->addListadDeCuentasBancarias();
$xB->addHTML( $xChk->get("TR.Omitir Estadisticos", "estadisticos", true) );
$xB->addjsVars("estadisticos", "estadisticos", true);

if($ConEntFed == true){
	$xB->addMunicipiosActivos();	
}
echo $xB->get();
echo $xB->getJs(true);
$xHP->fin();
?>