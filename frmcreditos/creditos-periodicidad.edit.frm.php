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
$xHP		= new cHPage("", HP_FORM);
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
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  


$xHP->init();

/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cCreditos_periocidadpagos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmperiodicidad", "creditos-periodicidad.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

$xFRM->OHidden("idcreditos_periocidadpagos", $xTabla->idcreditos_periocidadpagos()->v());
$xFRM->OHidden("periocidad_de_pago", $xTabla->periocidad_de_pago()->v());

$xFRM->OText("descripcion_periocidadpagos", $xTabla->descripcion_periocidadpagos()->v(), "TR.DESCRIPCION");

$xFRM->OText("titulo_en_informe", $xTabla->titulo_en_informe()->v() , "TR.TITULO EN INFORME");

$xFRM->OMoneda("tolerancia_en_dias_para_vencimiento", $xTabla->tolerancia_en_dias_para_vencimiento()->v(), "TR.DIAS PARA VENCIMIENTO");

$xFRM->OSiNo("TR.ESTATUSACTIVO","estatusactivo", $xTabla->estatusactivo()->v());

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>