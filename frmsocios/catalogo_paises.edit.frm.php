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
$xHP		= new cHPage("TR.EDITAR PAIS", HP_FORM);
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
$clave		= parametro("id"); $clave		= parametro("clave");
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xTxt		= new cHText();
$xTxt->setDivClass("tx4 tx18 orange");


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cPersonas_domicilios_paises();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmpaises", "catalogo_paises.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xFRM->OHidden("clave_de_control", $xTabla->clave_de_control()->v());

$xFRM->OText("nombre_oficial", $xTabla->nombre_oficial()->v(), "TR.NOMBRE");
$xFRM->OText("gentilicio", $xTabla->gentilicio()->v(), "TR.GENTILICIO");
$xFRM->OMoneda("clave_numerica", $xTabla->clave_numerica()->v(), "TR.CLAVE NUMERICA");
$xFRM->addHElem( $xTxt->getNormal("clave_alfanumerica", $xTabla->clave_alfanumerica()->v(), "TR.CLAVE ALFANUMERICA") );
$xFRM->addHElem($xSel->getListaDeNivelDeRiesgo("es_considerado_riesgo", $xTabla->es_considerado_riesgo()->v())->get(true) );
$xFRM->OSiNo("TR.PARAISOFISCAL", "es_paraiso_fiscal", $xTabla->es_paraiso_fiscal()->v());



//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>