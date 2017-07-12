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
$xHP		= new cHPage("TR.CREDITOS REQUISITOS", HP_FORM);
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
	$xQL->setRawQuery("INSERT INTO `creditos_productos_req`(`producto`, `clave`, `tipo_req`,`descripcion`,`numero`,`etapa`,`requerido`)
			SELECT $producto, `clave`, IF(INSTR(`tags`, 'procesal')>0, 2,1) , `descripcion`, 1, 'originacion', 1
			FROM `sistema_catalogo` WHERE `tags` LIKE '%requisitos%' ");
}
$jxc ->exportFunction('jsaRecrear', array('producto'), "#idmsg");
$jxc ->process();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$producto	= parametro("producto",0, MQL_INT);

$xHP->init();

$xCredEtapas	= new cCreditosEventos();


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCreditos_productos_req();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmrequisitos", "creditos.productos.requisitos.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

if($clave <= 0){
	$xTabla->producto($producto);
	$xTabla->idcreditos_productos_req("NULL");
}

$xFRM->OHidden("idcreditos_productos_req", $xTabla->idcreditos_productos_req()->v());
$xFRM->OHidden("producto", $xTabla->producto()->v(), "TR.PRODUCTO");

$xFRM->addHElem($xSel->getListaDeCatalogoGenerico("tipo_requisito", "tipo_req", $xTabla->tipo_req()->v())->get("TR.TIPO", true));
$xFRM->addHElem($xSel->getListaDeCatalogoGenerico("requisitos", "clave", $xTabla->clave()->v())->get("TR.CLAVE", true));

$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.DESCRIPCION");
$xFRM->OMoneda("numero", $xTabla->numero()->v(), "TR.ORDEN");
$xFRM->OText("ruta_validacion", $xTabla->ruta_validacion()->v(), "TR.VALIDADOR");
$xFRM->OMoneda("escore", $xTabla->escore()->v(), "TR.TASAPROGRESO");
$xFRM->OText("etapa", $xTabla->etapa()->v(), "TR.ETAPA");

$xFRM->OSiNo("TR.REQUERIDO","requerido", $xTabla->requerido()->v());


$xFRM->addCRUD($xTabla->get(), true);
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);


$xFRM->OButton("TR.GENERAR", "jsaRecrear()", $xFRM->ic()->AUTOMAGIC);
$xFRM->addAviso("", "idmsg");

echo $xFRM->get();



$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>