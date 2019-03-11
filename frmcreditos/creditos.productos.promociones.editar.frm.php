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
$xHP		= new cHPage("TR.EDITAR PROMOCIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$producto	= parametro("producto",0, MQL_INT);

$xHP->init();


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCreditos_productos_promo();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmprodspromsedit", "creditos_productos_promo.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

if($clave <= 0){
	$xTabla->producto($producto);
	$xTabla->idcreditos_productos_promo("NULL");
	$xTabla->fecha_inicial($xF->getFechaISO());
}
	
$xFRM->OHidden("idcreditos_productos_promo", $xTabla->idcreditos_productos_promo()->v());
$xFRM->OHidden("producto", $xTabla->producto()->v(), "TR.PRODUCTO");
$xFRM->OHidden("fecha_inicial", $xTabla->fecha_inicial()->v(), "TR.FECHA VIGENTE");


$xFRM->addHElem( $xSel->getListaDeCatalogoGenerico("tipo_promociones","tipo_promocion", $xTabla->tipo_promocion()->v())->get("TR.TIPO PROMOCION", true) );


$xFRM->addHElem($xSel->getListaDeTiposDeOperacion("tipo_operacion", $xTabla->tipo_operacion()->v())->get(true));

$xFRM->OText("condiciones", $xTabla->condiciones()->v(), "TR.CONDICIONES");
$xFRM->OMoneda("num_items", $xTabla->num_items()->v(), "TR.NUMERO");
$xFRM->OMoneda("descuento", $xTabla->descuento()->v(), "TR.DESCUENTO");
$xFRM->OMoneda("precio", $xTabla->precio()->v(), "TR.PRECIO");
$xFRM->OHidden("sucursal", $xTabla->sucursal()->v(), "TR.SUCURSAL");

$xFRM->ODate("fecha_final", $xTabla->fecha_final()->v(), "TR.FECHA VENCIMIENTO");
$xFRM->OSiNo("TR.ESTATUSACTIVO", "estatus", $xTabla->estatus()->v());


//$xFRM->OButton("TR.GENERAR", "jsaRecrear()", $xFRM->ic()->AUTOMAGIC);
$xFRM->addAviso("", "idmsg");
//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();




//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>