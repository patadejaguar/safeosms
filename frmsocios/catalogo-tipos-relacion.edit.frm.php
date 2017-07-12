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
$xHP		= new cHPage("TR.AGREGAR TIPO_DE RELACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();



/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cSocios_relacionestipos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmrelaciones", "catalogo-tipos-relacion.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xFRM->setNoAcordion();
if($clave <= 0){
	$xTabla->idsocios_relacionestipos("NULL");
}
$xTabla->tipo_relacion($xTabla->idsocios_relacionestipos()->v());

$xFRM->addSeccion("idgeneral", "TR.DATOS GENERALES");
$xFRM->OHidden("idsocios_relacionestipos", $xTabla->idsocios_relacionestipos()->v());
$xFRM->OText("descripcion_relacionestipos", $xTabla->descripcion_relacionestipos()->v(), "TR.NOMBRE");
$xFRM->OMoneda("subclasificacion", $xTabla->subclasificacion()->v(), "TR.CLASIFICACION");
$xFRM->OText("descripcion_larga", $xTabla->descripcion_larga()->v(), "TR.DESCRIPCION");
//$xFRM->OMoneda("tipo_relacion", $xTabla->tipo_relacion()->v(), "TR.TIPO RELACION");
$xFRM->OMoneda("puntos_en_scoring", $xTabla->puntos_en_scoring()->v(), "TR.PUNTOS CALIFICADO");
$xFRM->OText("tags", $xTabla->tags()->v(), "TR.TAGS");
$xFRM->endSeccion();

$xFRM->addSeccion("idopciones", "TR.OPCIONES");
$xFRM->OSiNo("TR.REQUIERE DOMICILIO", "requiere_domicilio", $xTabla->requiere_domicilio()->v());
$xFRM->OSiNo("TR.REQUIERE ACTIVIDAD_ECONOMICA", "requiere_actividadeconomica", $xTabla->requiere_actividadeconomica()->v());
$xFRM->OSiNo("TR.REQUIERE VALIDAR", "requiere_validacion", $xTabla->requiere_validacion()->v());
$xFRM->OSiNo("TR.TIENE VINCULO PATRIMONIAL", "tiene_vinculo_patrimonial", $xTabla->tiene_vinculo_patrimonial()->v());
$xFRM->OSiNo("TR.ESTATUSACTIVO", "mostrar", $xTabla->mostrar()->v());
$xFRM->OSiNo("TR.CHECAR AML", "checar_aml", $xTabla->checar_aml()->v());

$xFRM->endSeccion();


//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>