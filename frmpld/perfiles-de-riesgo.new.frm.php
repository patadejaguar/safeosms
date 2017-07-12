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
$xHP		= new cHPage("TR.MATRIZRIESGO ADICIONAL", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
function jsaGetCampo($tabla){
	$xObj	= new cSQLTabla($tabla);
	$obj	= $xObj->obj();
	if( $xObj->obj() == null){
		
	} else {
		return $obj->getKey();
	}
}
$jxc ->exportFunction('jsaGetCampo', array('objeto_de_origen'), "#campo_de_origen");
$jxc ->process();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");


$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cAml_riesgo_perfiles();
$xTabla->setData( $xTabla->query()->initByID($clave));

$xFRM	= new cHForm("frmaml_riesgo_perfiles", "perfiles-de-riesgo.frm.php?action=$action");
$xSel		= new cHSelect();
$xTxt		= new cHText();
$xFRM->setTitle($xHP->getTitle());

$xSelT		= $xSel->getListaDeObjetosOrigenRiesgo("objeto_de_origen", $xTabla->objeto_de_origen()->v());
$xSelT->addEvent("onblur", "jsaGetCampo()");

$xFRM->OHidden("idaml_riesgo_perfiles", "NULL");
$xFRM->addHElem($xSelT->get("TR.origen", true));
$xFRM->addHElem($xTxt->getDeValoresPorTabla("valor_de_origen", $xTabla->valor_de_origen()->v(), "TR.valor de origen", "objeto_de_origen") );
$xFRM->OHidden("campo_de_origen", $xTabla->campo_de_origen()->v());
$xFRM->addHElem($xSel->getListaDeNivelDeRiesgo("nivel_de_riesgo", $xTabla->nivel_de_riesgo()->v())->get(true) );


$xFRM->addCRUD($xTabla->get(), true);

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>