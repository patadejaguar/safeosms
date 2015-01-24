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
$xHP		= new cHPage("TR.Actualizar estado de riesgo", HP_FORM);
$xF			= new cFecha();
$xlistas	= new cSQLListas();

$jxc 		= new TinyAjax();

function jsaConfirmaRiesgo($id, $observaciones, $reporte24){
	$xAML		= new cAMLAlertas($id);
	$inmediato	= false;
	if(strtolower($reporte24) == "on"){ $inmediato = true; }
	$xAML->setConfirmaAlerta($observaciones, false, $inmediato);
	return $xAML->getMessages(OUT_HTML);
}

function jsaDescartaRiesgo($id, $observaciones){
	$xAML	= new cAMLAlertas($id);
	$xAML->setDescartaAlerta($observaciones);
	return $xAML->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaConfirmaRiesgo', array('idriesgo', 'idnotas', 'ides24'), "#idmsgs");
$jxc ->exportFunction('jsaDescartaRiesgo', array('idriesgo', 'idnotas'), "#idmsgs");

$jxc ->process();

$codigo		= parametro("codigo", 0, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();
$xFRM		= new cHForm("frmeditariesgo", "./");
$msg		= "";

$xAlert		= new cAMLAlertas($codigo);
$xAlert->init();
$xFRM->addAviso( $xAlert->getDescripcion() );

$xFRM->OTextArea("idnotas", "", "TR.Acciones_tomadas / observaciones");
$xFRM->OCheck("TR.Guardar como REPORTE_X_HORAS", "ides24");
$xFRM->addHTML("<input type='hidden' id='idriesgo' value='$codigo'");
$xFRM->OButton("TR.Confirmar", "jsConfirmaRiesgo()", $xFRM->ic()->OK, "idconfirma" );
$xFRM->OButton("TR.Descartar", "jsDescartaRiesgo()", $xFRM->ic()->NO, "iddescarta" );


echo $xFRM->get();
?>
<script>
var xG		= new Gen();
function jsDescartaRiesgo(){ xG.confirmar({ msg : "Desea Descartar la Alerta como Riesgo?", callback : "jsDescartaRiesgo2()", evaluador : jsRazonNoVacia(), alert : "La observacion no puede quedar vacia"}); }
function jsConfirmaRiesgo(){ xG.confirmar({ msg : "Desea Confirmar la Alerta como Riesgo?", callback : "jsConfirmaRiesgo2()", evaluador : jsRazonNoVacia(), alert : "La observacion no puede quedar vacia" }); }
function jsRazonNoVacia(){
	var valid	= new ValidGen();
	return valid.NoVacio( $("#idnotas").val() );
}
function jsDescartaRiesgo2(){
	jsaDescartaRiesgo();
	setTimeout("jsSalir()", 2000);
}
function jsConfirmaRiesgo2(){
	jsaConfirmaRiesgo();
	setTimeout("jsSalir()", 2000);
}
function jsSalir(){ xG.close(); }
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>