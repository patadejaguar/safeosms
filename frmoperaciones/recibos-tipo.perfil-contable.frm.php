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
$xSel		= new cHSelect();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$tipo		= parametro("tipo", 0, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");

$msg		= "";


$clave		= parametro("idcontable_poliza_perfil", null, MQL_INT);
$xTabla		= new cContable_polizas_perfil();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave		= parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->idcontable_poliza_perfil($clave);
	$xTabla->tipo_de_recibo($tipo);
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmcontable_polizas_perfil", "recibos-tipo.perfil-contable.frm.php?action=$step");

if($step == MQL_MOD){ $xFRM->addGuardar(); } else { $xFRM->addSubmit(); }
$clave 		= parametro($xTabla->getKey(), null, MQL_INT);

if( ($action == MQL_ADD OR $action == MQL_MOD) AND ($clave != null) ){
	$xTabla->setData( $xTabla->query()->initByID($clave));
	$xTabla->setData($_REQUEST);

	if($action == MQL_ADD){
		$xTabla->query()->insert()->save();
	} else {
		$xTabla->query()->update()->save($clave);
	}
	$xFRM->addAvisoRegistroOK();
}
$xFRM->OHidden("idcontable_poliza_perfil", $xTabla->idcontable_poliza_perfil()->v(), "TR.Clave");
$xFRM->addHElem( $xSel->getListaDeTiposDeRecibos("tipo_de_recibo", $xTabla->tipo_de_recibo()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeTiposDeOperacion("tipo_de_operacion", $xTabla->tipo_de_operacion()->v())->get(true) );
$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.descripcion");
$xFRM->addHElem($xSel->getListaDeTiposDeOperacionContable("operacion", $xTabla->operacion()->v())->get(true) );

$xFRM->OTextArea("formula_posterior", $xTabla->formula_posterior()->v(), "TR.formula");




echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>