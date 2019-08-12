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

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();
$xTxt		= new cHText();
$xSel		= new cHSelect();
/* ===========		FORMULARIO		============*/
$clave		= parametro("clave_de_riesgo", null, MQL_INT);
$xTabla		= new cAml_risk_register();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave		= parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->clave_de_riesgo($clave);
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmaml_risk_register", "riesgo.editar.frm.php?action=$step");

if($step == MQL_MOD){ $xFRM->addGuardar(); } else { $xFRM->addSubmit(); }
$clave 		= parametro($xTabla->getKey(), null, MQL_INT);

if( ($action == MQL_ADD OR $action == MQL_MOD) AND ($clave != null) ){
	$xTabla->setData( $xTabla->query()->initByID($clave));
	$xTabla->setData($_REQUEST);	
	$persona	= parametro("idsocio0", $xTabla->persona_relacionada()->v(), MQL_INT);
	$tercero	= parametro("idsocio1", $xTabla->tercero_relacionado()->v(), MQL_INT);
	$xTabla->persona_relacionada($persona);
	$xTabla->tercero_relacionado($tercero);
	if($action == MQL_ADD){
		$xTabla->query()->insert()->save();
	} else {
		$xTabla->query()->update()->save($clave);
	}
	$xFRM->addAvisoRegistroOK();
}

$xFRM->OHidden("clave_de_riesgo", $xTabla->clave_de_riesgo()->v(), "TR.clave de riesgo");

$xFRM->addHElem( $xTxt->getDeSocio("0", false, $xTabla->persona_relacionada()->v(),"", "TR.persona") );
$xFRM->addHElem( $xTxt->getDeSocio("1", false, $xTabla->tercero_relacionado()->v(),"", "TR.persona relacionada") );
//$xFRM->OMoneda("persona_relacionada"
//$xFRM->OMoneda("tercero_relacionado"
$xFRM->addHElem( $xSel->getListaDeRiesgosAML("tipo_de_riesgo", "", $xTabla->tipo_de_riesgo()->v())->get(true) );
$xFRM->OMoneda("documento_relacionado", $xTabla->documento_relacionado()->v(), "TR.documento relacionado");
$xFRM->addHElem( $xSel->getListaDeObjetosEnSistema("tipo_de_documento", $xTabla->tipo_de_documento()->v())->get(true) );

//$xFRM->OMoneda("tipo_de_documento", $xTabla->tipo_de_documento()->v(), "TR.tipo de documento");
$ti		= $xSel->getListadoGenerico("aml_tipos_de_operacion", "tipo_de_operacion");
$ti->setOptionSelect($xTabla->tipo_de_operacion()->v());
$xFRM->addHElem($ti->get("TR.tipo de operacion", true) );

$ti2		= $xSel->getListadoGenerico("aml_instrumentos_financieros", "instrumento_financiero");
$ti2->setOptionSelect($xTabla->instrumento_financiero()->v());
$xFRM->addHElem($ti2->get("TR.instrumento_financiero", true) );

//$xFRM->OText("tipo_de_operacion", $xTabla->tipo_de_operacion()->v(), "TR.tipo de operacion");
//$xFRM->OMoneda("instrumento_financiero", $xTabla->instrumento_financiero()->v(), "TR.instrumento financiero");

$xFRM->OMoneda("monto_total_relacionado", $xTabla->monto_total_relacionado()->v(), "TR.monto total relacionado");
//$xFRM->OTextArea("notas_de_checking", $xTabla->notas_de_checking()->v(), "TR.notas de checking");

//$xFRM->OTextArea("mensajes_del_sistema", $xTabla->mensajes_del_sistema()->v(), "TR.mensajes del sistema");

//$xFRM->OMoneda("tipo_de_riesgo", $xTabla->tipo_de_riesgo()->v(), "TR.tipo de riesgo");
//$xFRM->OMoneda("fecha_de_envio", $xTabla->fecha_de_envio()->v(), "TR.fecha de envio");
//$xFRM->OMoneda("estado_de_envio", $xTabla->estado_de_envio()->v(), "TR.estado de envio");
//$xFRM->OMoneda("fecha_de_checking", $xTabla->fecha_de_checking()->v(), "TR.fecha de checking");
//$xFRM->OMoneda("oficial_de_checking", $xTabla->oficial_de_checking()->v(), "TR.oficial de checking");
//$xFRM->OText("firma_de_checking", $xTabla->firma_de_checking()->v(), "TR.firma de checking");
//$xFRM->OMoneda("fecha_de_reporte", $xTabla->fecha_de_reporte()->v(), "TR.fecha de reporte");
//$xFRM->OMoneda("hora_de_reporte", $xTabla->hora_de_reporte()->v(), "TR.hora de reporte");
//$xFRM->OMoneda("escore", $xTabla->escore()->v(), "TR.escore");
//$xFRM->OMoneda("usuario_de_origen", $xTabla->usuario_de_origen()->v(), "TR.usuario de origen");
//$xFRM->OTextArea("razones_de_reporte", $xTabla->razones_de_reporte()->v(), "TR.razones de reporte");
//$xFRM->OTextArea("acciones_tomadas", $xTabla->acciones_tomadas()->v(), "TR.acciones tomadas");
//$xFRM->OTextArea("metadata", $xTabla->metadata()->v(), "TR.metadata");
//$xFRM->OMoneda("reporte_inmediato", $xTabla->reporte_inmediato()->v(), "TR.reporte inmediato");


echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>