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
$xHP		= new cHPage("TR.Reportes de Credito");
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();

$xRuls		= new cReglaDeNegocio();
$ConEntFed	= $xRuls->getValorPorRegla($xRuls->reglas()->REPORTES_USAR_EFED);


$xHP->init();

$xRPT		= new cPanelDeReportes(iDE_CREDITO, "general_creditos");
$xRPT->setTitle($xHP->getTitle());
$xRPT->addOficialDeCredito();
$xRPT->setConOperacion(true);

$xCtrl		= $xSel->getListaDeDestinosDeCredito();
$xCtrl->addEspOption(SYS_TODAS, "TODAS");
$xCtrl->setOptionSelect(SYS_TODAS);

if($ConEntFed == true){
	$xRPT->addMunicipiosActivos();
}


$xRPT->addControl( $xCtrl->get(true), "iddestinodecredito", "destino" );

$xCtrl2		= $xSel->getListaDeTipoDeCuotaPag("tipodecuota");
$xCtrl2->addEspOption(SYS_TODAS, "TODAS");
$xCtrl2->setOptionSelect(SYS_TODAS);

$xRPT->addControl( $xCtrl2->get(true), "tipodecuota", "tipodecuota" );

$xChk->setDivClass("tx4 tx18");
$xRPT->addControl($xChk->get("TR.Incluir Otros", "idotrosd"), "idotrosd", "otrosdatos", true);
$xRPT->addControl($xChk->get("TR.Datos Simples", "idcompacto"), "idcompacto", "compacto", true);
$xRPT->addControl($xChk->get("TR.OMITIRCEROS", "nocero"), "nocero", "nocero", true);

if(PERSONAS_CONTROLAR_POR_EMPRESA == true ){
	$xRPT->addControl($xChk->get("TR.CON EMPLEADOR", "conempleador"), "conempleador", "conempleador", true);
	$xRPT->addControl($xChk->get("TR.SIN EMPLEADOR", "sinempleador"), "sinempleador", "sinempleador", true);
}
if(MODO_DEBUG == true){
	$xSelTS	= $xSel->getListaDeCatalogoGenerico("cpdtos_tipo_sistema", "tipoensistema");
	$xSelTS->addTodas(true);
	
	$xSelTS->setDivClass("tx4 tx18 red");
	$xRPT->addControl($xSelTS->get("TR.TIPO EN SISTEMA", true), "tipoensistema", "tipoensistema");
	
}
/*		
		$xFRM->addHElem( $xSelTS->get("TR.TIPO EN SISTEMA", true) );*/
//$xRPT->addTipoDeOperacion();
//$xRPT->setConRecibos(false);


echo $xRPT->get();

echo $xRPT->getJs(true);

$xHP->fin();
?>