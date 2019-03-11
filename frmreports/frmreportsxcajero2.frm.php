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

$xHP		= new cHPage("TR.Reportes por Cajero");
$xHP->init();

$xChk		= new cHCheckBox();
//$xSel		= new cHSelect();

$xB	= new cPanelDeReportes(iDE_OPERACION, "caja_tesoreria2", false);
$xB->setTitle( $xHP->getTitle() );
$xS		= $xB->addCajeros(false, "idcajero_array");
$lbl	= $xS->getLabel();
$xS->setMultiple();
//$xS->setDivClass("tx14 blue");

$xB->OFRM()->setMultisel("idcajero_array");
$xB->addjsVars("idcajero_array", "idcajero_array");
$xS->setLabel("");

//$xS->setEliminarOption(SYS_TODAS);
$xB->OFRM()->addDivSolo($lbl, $xS->get(), "tx14", "tx34");
$xB->addListReports();
//$xB->setConOperacion();
//$xB->setConRecibos();
$xB->setSinUsuarios();
$xB->addListadDeCuentasBancarias();
$xB->addHTML( $xChk->get("TR.Omitir Estadisticos", "estadisticos") );
$xB->addjsVars("estadisticos", "estadisticos", true);

echo $xB->get();
echo $xB->getJs(true);
$xHP->fin();

?>