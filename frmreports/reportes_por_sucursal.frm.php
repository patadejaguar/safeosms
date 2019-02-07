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
$xHP			= new cHPage("TR.Reportes por Sucursal");

$xHP->init();


$xRPT			= new cPanelDeReportes(0, "sucursales", false);
$xChk			= new cHCheckBox();
$xSel			= new cHSelect();

$xRPT->setTitle($xHP->getTitle());

$xLSuc			= $xSel->getListaDeSucursales("sucursal");


$xLSuc->addEspOption(SYS_TODAS);

$xRPT->OFRM()->addDivSolo($xLSuc->get(false));
$xRPT->addjsVars("sucursal", "sucursal");
//$xRPT->addControl($xLSuc->get(false), "sucursal", "sucursal");

$xRPT->addListReports();
//$xRPT->addSucursales();
$xRPT->setConSucursal(false);
$xRPT->setConCreditos(false);
$xRPT->setConOperacion(false);
$xRPT->setConRecibos(false);

$xRPT->addCheckBox("TR.INCLUIR PERSONA_MORAL", "cualquiera", false);

//$xRPT->addControl($xChk->get("TR.OMITIRCEROS", "nocero"), "nocero", "nocero", true);

echo $xRPT->get();

echo $xRPT->getJs(true);

?>