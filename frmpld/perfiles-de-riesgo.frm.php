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

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$xSel		= new cHSelect();
$xTxt		= new cHText();

$xHP->init();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

/* ===========		FORMULARIO		============*/
$clave		= parametro("id", null, MQL_INT);
$xTabla		= new cAml_riesgo_perfiles();
$xFRM		= new cHForm("frmaml_riesgo_perfiles");
$xFRM->addGuardar();


if($action == SYS_NINGUNO){
	if($clave != null){
		$xFRM->setAction("perfiles-de-riesgo.frm.php?id=$clave&action=" . MQL_MOD);	//asignar que es actualizar
		$xTabla->setData( $xTabla->query()->initByID($clave));						//cargar datos del registro
	}
} else {
	$ready		= false;
	$xTabla->setData($_REQUEST);													//cargar datos del request}
	
	$dd			= new cSAFETabla($xTabla->objeto_de_origen()->v());
	$obj		= $dd->obj();
	if($obj != null){
		$xTabla->campo_de_origen( $obj->getKey() );
	}

	if($action == MQL_ADD){
		$ready 	= $xTabla->query()->insert()->save();								//insertar registro
	} else {
		$ready	= $xTabla->query()->update()->save($clave);						//actualizar BD
	}
	$clave		= null;
	if($ready != false){ $xFRM->addAvisoRegistroOK();	}
	// else { $xFRM->addAvisoRegistroError();	}
}
if($clave == null){
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->idaml_riesgo_perfiles($clave);
	$xFRM->setAction("perfiles-de-riesgo.frm.php?action=" . MQL_ADD);
}
$xFRM->OHidden("idaml_riesgo_perfiles", $xTabla->idaml_riesgo_perfiles()->v(), "TR.idaml riesgo perfiles");
$xFRM->addHElem($xSel->getListaDeObjetosOrigenRiesgo("objeto_de_origen", $xTabla->objeto_de_origen()->v())->get("TR.origen", true));
$xFRM->addHElem($xTxt->getDeValoresPorTabla("valor_de_origen", $xTabla->valor_de_origen()->v(), "TR.valor de origen", "objeto_de_origen") );
$xFRM->addHElem($xSel->getListaDeNivelDeRiesgo("nivel_de_riesgo", $xTabla->nivel_de_riesgo()->v())->get(true) );


/* ===========		GRID JS		============*/
$xT			= new cTabla("SELECT * FROM aml_riesgo_perfiles LIMIT 0,100");
$xT->addTool($xT->T_ELIMINAR);
$xFRM->addHTML( $xT->Show() );
/*
$xHG	= new cHGrid("aml_riesgo_perfiles");
$sqlaml_riesgo_perfiles	= base64_encode("SELECT * FROM aml_riesgo_perfiles LIMIT 0,100");
$xHG->setListAction("../svc/datos.svc.php?out=jtable&q=$sqlaml_riesgo_perfiles");$xHG->addKey("idaml_riesgo_perfiles");
$xHG->addElement("objeto_de_origen", "TR.Origen", "10%");
$xHG->addElement("campo_de_origen", "TR.Tipo", "10%");
$xHG->addElement("valor_de_origen", "TR.valor", "10%");
$xHG->addElement("nivel_de_riesgo", "TR.nivel de riesgo", "10%");

$xFRM->addHTML($xHG->getDiv());
*/
echo $xFRM->get();

//echo $xHG->getJsHeaders();
//echo $xHG->getJs(true, true);
//$jxc ->drawJavaScript(false, true);
echo $xT->getJSActions(true);
$xHP->fin();
?>