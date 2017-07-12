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

$xB	= new cPanelDeReportes(iDE_OPERACION, "caja_tesoreria", false);
$xB->setTitle( $xHP->getTitle() );
$xS		= $xB->addCajeros(false);
$lbl	= $xS->getLabel();

$xS->setLabel("");
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