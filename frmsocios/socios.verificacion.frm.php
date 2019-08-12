<?php
/**
 * Verificacion de Domicilios y actividad economica
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.VERIFICACION DE PERSONA", HP_FORM);
$jxc 		= new TinyAjax();
function jsaGuardarVerificacion($fecha, $oficial, $tipo, $id, $notas, $socio){
	$xF		= new cFecha();
	$fecha	= $xF->getFechaISO($fecha);
	$tipo	= ( $tipo == "d" ) ? TPERSONAS_DIRECCIONES : TPERSONAS_ACTIVIDAD_ECONOMICA;
	$xSoc	= new cSocio($socio);
	$xSoc->init();
	$xSoc->setVerificacion($tipo, $id, $fecha, $notas, $oficial);
	
	return $xSoc->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaGuardarVerificacion', array('idfechaverificacion', 'idoficial', 'idtipo', 'idclave', "idnotas", 'idsocio'), "#avisos");
$jxc ->process();


$clave		= parametro("id", 0, MQL_INT); $clave = parametro("clave", $clave, MQL_INT); $clave = parametro("i", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

$tipo		= parametro("t", ""); $tipo = parametro("tipo", $tipo);
$tipo		= strtolower($tipo);

$xHP->init();
//d = domicilio t = trabajo y o actividad economica


$xFRM		= new cHForm("socios_verificacion", "socios_verificacion.frm.php");
$xFRM->setTitle($xHP->getTitle());

$xHSel		= new cHSelect();

$xOfi		= $xHSel->get("idoficial", "Oficial a Cargo", getUsuarioActual(), TVISTA_OFICIALES );
$xFRM->ODate("idfechaverificacion", false, "TR.Fecha de Verificacion");

$xFRM->addHElem($xOfi);
$xFRM->OTextArea("idnotas", "", "TR.Observaciones");

$xFRM->addHTML("<input type='hidden' id='idtipo' value='$tipo' >");
$xFRM->addHTML("<input type='hidden' id='idclave' value='$clave' >");
$xFRM->addHTML("<input type='hidden' id='idsocio' value='$persona' >");

$xFRM->addHTML("<div class='aviso' id='avisos'></div>");

$xFRM->addSubmit("Guardar Verificacion", "jsGuardarVerificacion()");

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script  >
var xG	= new Gen();	
function jsGuardarVerificacion(){	jsaGuardarVerificacion(); jsCloseWithTimer(1500); }
</script>
<?php
$xHP->fin();
?>