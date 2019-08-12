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
$xHP		= new cHPage("TR.Dictamen de ALERTA_AML", HP_FORM);
$xF			= new cFecha();
$xlistas	= new cSQLListas();
$jxc 		= new TinyAjax();

function jsaConfirmaRiesgo($id, $observaciones, $reporte24, $fecha){
	$xAML		= new cAMLAlertas($id);
	$inmediato	= false;
	if(strtolower($reporte24) == "on"){ $inmediato = true; }
	$xAML->setConfirmaAlerta($observaciones, $fecha, $inmediato);
	return $xAML->getMessages(OUT_HTML);
}

function jsaDescartaRiesgo($id, $observaciones, $fecha, $recursivo){
	$xAML	= new cAMLAlertas($id);
	
	$xAML->setDescartaAlerta($observaciones, $fecha, $recursivo);
	return $xAML->getMessages(OUT_HTML);
}

function jsaSetFalsoPositivo($id, $observaciones, $fecha, $recursivo){
	$xAML	= new cAMLAlertas($id);
	$xAML->setEsFalsoPositivo($observaciones, $fecha, $recursivo);
	return $xAML->getMessages(OUT_HTML);
}


$jxc->exportFunction('jsaConfirmaRiesgo', array('idriesgo', 'idnotas', 'ides24', 'idfechaactual'), "#idmsgs");
$jxc->exportFunction('jsaDescartaRiesgo', array('idriesgo', 'idnotas', 'idfechaactual', 'iddescartar'), "#idmsgs");
$jxc->exportFunction('jsaSetFalsoPositivo', array('idriesgo', 'idnotas', 'idfechaactual', 'iddescartar'), "#idmsgs");

$jxc->process();

$codigo		= parametro("codigo", 0, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();
$xFRM		= new cHForm("frmeditariesgo", "./");
$xFRM->setNoAcordion();
$msg		= "";
$xFRM->setTitle($xHP->getTitle());

$xAlert		= new cAMLAlertas($codigo);
$xAlert->init();
$xFRM->addSeccion("iddiv", $xHP->getTitle());
if(PERMITIR_EXTEMPORANEO == true){
	$xFRM->addFecha();
} else {
	$xFRM->OHidden("idfechaactual", $xF->getFechaISO());
}

$xFRM->OCheck("TR.Guardar como REPORTE_X_HORAS", "ides24");
$xFRM->OCheck("TR.Descartar Avisos Anterior", "iddescartar");

$xFRM->OTextArea("idnotas", "", "TR.Acciones_tomadas / observaciones");
$xFRM->setValidacion("idnotas", "validacion.novacio", "", true);

$xFRM->endSeccion();
$xFRM->addSeccion("iddivmsg", "TR.REPORTE DEL SISTEMA");
$xFRM->addAviso( $xAlert->getDescripcion() );
$xFRM->OHidden("idriesgo", $codigo);
$xFRM->endSeccion();
$xFRM->OButton("TR.Confirmar Riesgo", "jsConfirmaRiesgo()", $xFRM->ic()->OK, "idconfirma", "yellow" );
$xFRM->OButton("TR.Descartar Riesgo", "jsDescartaRiesgo()", $xFRM->ic()->NO, "iddescarta", "green" );

$xFRM->OButton("TR.ES FALSOPOS", "jsSetFalsoPositivo()", $xFRM->ic()->NO, "idfalsopositivo", "gray" );


switch ($xAlert->getTipoDeDocto()){
	case iDE_RECIBO:
		$recibo		= $xAlert->getDocumento();
		$xFRM->OButton("TR.Reporte del Recibo", "var xRec = new RecGen();xRec.reporte($recibo);", $xFRM->ic()->REPORTE, "rpt-$recibo");		
		break;
	case iDE_CREDITO:
		break;
}

echo $xFRM->get();
?>
<script>
var xG		= new Gen();
function jsDescartaRiesgo(){ xG.confirmar({ msg : "Desea Descartar la Alerta_AML como Riesgo?", callback : "jsDescartaRiesgo2()", evaluador : jsRazonNoVacia(), alert : "La observacion no puede quedar vacia"}); }
function jsConfirmaRiesgo(){ xG.confirmar({ msg : "Desea Confirmar la Alerta_AML como Riesgo?", callback : "jsConfirmaRiesgo2()", evaluador : jsRazonNoVacia(), alert : "La observacion no puede quedar vacia" }); }
function jsSetFalsoPositivo(){ xG.confirmar({ msg : "Desea Guardar como FALSOPOS?", callback : "jsSetFalsoPositivo2()" }); }

function jsRazonNoVacia(){
	var valid	= new ValidGen();
	xG.cleanText("#idnotas");
	//$("#idnotas").val()
	return valid.NoVacio( $("#idnotas").val() );
}
function jsSetFalsoPositivo2(){
	xG.postajax("jsSalir()");
	jsaSetFalsoPositivo();
}

function jsDescartaRiesgo2(){
	xG.postajax("jsSalir()");
	jsaDescartaRiesgo();
	
}
function jsConfirmaRiesgo2(){
	xG.postajax("jsSalir()");
	jsaConfirmaRiesgo();
}
function jsSalir(){ xG.close(); }
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>