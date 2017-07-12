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
$xHP		= new cHPage("TR.ETAPAS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
function jsaRecrear($producto){
	$xQL	= new MQL();
	$xQL->setRawQuery("INSERT INTO `creditos_productos_etapas`(`producto`, `etapa`,`nombre`,`tags`, `permisos`) 
	SELECT $producto, `idcreditos_etapas`,`descripcion`,`tags`, '" . DEFAULT_PERMISOS . "' FROM `creditos_etapas` WHERE `tags` LIKE '%todas%' ");
	
}
$jxc ->exportFunction('jsaRecrear', array('producto'), "#idmsg");
$jxc ->process();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$producto	= parametro("producto",0, MQL_INT);

$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCreditos_productos_etapas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frm", "creditos_productos_etapas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

if($clave <= 0){
	$xTabla->producto($producto);
	$xTabla->idcreditos_productos_etapas("NULL");
}

$xFRM->OHidden("idcreditos_productos_etapas", $xTabla->idcreditos_productos_etapas()->v());
$xFRM->OHidden("producto", $xTabla->producto()->v(), "TR.PRODUCTO");

$xSEtapas	= $xSel->getListadoGenerico("creditos_etapas", "etapas");
$xSEtapas->setOptionSelect($xTabla->etapa()->v());

$xFRM->addHElem($xSEtapas->get("TR.ETAPAS", true));

$xFRM->OText("nombre", $xTabla->nombre()->v(), "TR.NOMBRE");
$xFRM->OText("tags", $xTabla->tags()->v(), "TR.TAGS");
$xFRM->OText("permisos", $xTabla->permisos()->v(), "TR.PERMISOS");
$xFRM->OMoneda("orden", $xTabla->orden()->v(), "TR.ORDEN");


$xFRM->addCRUD($xTabla->get(), true);

$xFRM->OButton("TR.GENERAR", "jsaRecrear()", $xFRM->ic()->AUTOMAGIC);
$xFRM->addAviso("", "idmsg");

//$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();



$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>