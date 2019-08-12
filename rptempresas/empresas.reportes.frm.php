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
$xHP		= new cHPage("TR.Reportes de Empresa");
$xHP->init();
$xSel		= new cHSelect();
$xRPT		= new cPanelDeReportes(iDE_EMPRESA, "empresas", false);
$xRPT->setTitle($xHP->getTitle());
$xRPT->addEmpresasConConvenio(false);
$xSelEmp	= $xSel->getListaDeEmpresas("idempresa");
$xSelEmp->addEspOption(SYS_TODAS);
$xSelEmp->setOptionSelect(SYS_TODAS);
$xSelEmp->setDivClass("");
$lbl		= $xSelEmp->getLabel();
$xSelEmp->setLabel("");
$xRPT->OFRM()->addDivSolo($lbl, $xSelEmp->get(false), "tx14", "tx34");

$xRPT->addListReports();
$xRPT->addCreditosProductos();
$xRPT->addCreditosPeriocidadDePago();
$xRPT->addTipoDePago();
$xRPT->setConSucursal(false);
$xRPT->setConOperacion(false);
$xRPT->setConCajero(false);
$xRPT->setConRecibos(false);



echo $xRPT->get();

echo $xRPT->getJs(true);

$xHP->fin();
?>