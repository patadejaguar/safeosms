<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.AGREGAR REGLA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xRuls		= new cReglasDeNegocioLista();
$arr		= $xRuls->getInArray(); ksort($arr);

$xHP->init();
/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cEntidad_reglas();
$xFRM		= new cHForm("frmreglas", "editar-reglas.frm.php?action=$action");
$xSel		= new cHSelect();
$xSel2		= new cHSelect();

$xTabla->setData( $xTabla->query()->initByID($clave));

$xFRM->setTitle($xHP->getTitle());
if($clave <= 0){
	$xTabla->reglas("");
	$xTabla->identidad_reglas( $xTabla->query()->getLastID() );
}
$xFRM->ONumero("identidad_reglas", $xTabla->identidad_reglas()->v(), "TR.CLAVE");


$xSel->addOptions($arr);
$xSel2->addOptions(array("FORM" => "FORMATO", "REPORT" => "REPORTE", "ACTION" => "ACCION", "DTABLA" => "TABLA DEL SISTEMA"));

$xFRM->addHElem( $xSel2->get("contexto", "TR.OBJETO", $xTabla->contexto()->v()) );
//$xFRM->OText_13("contexto", $xTabla->contexto()->v(), "TR.CONTEXTO");
$xFRM->addHElem( $xSel->get("nombre", "TR.NOMBRE", $xTabla->nombre()->v()) );
//$xFRM->OText("nombre", $xTabla->nombre()->v(), "TR.NOMBRE");

$xFRM->OHidden("evento", $xTabla->evento()->v(), "TR.EVENTO");
$xFRM->OHidden("sujetos", $xTabla->sujetos()->v(), "TR.SUJETOS");
$xFRM->OHidden("metadata", $xTabla->metadata()->v(), "TR.METADATA");

$xFRM->OHidden("reglas", $xTabla->reglas()->v(), "TR.REGLAS");



$xFRM->addCRUD($xTabla->get(), true);
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>