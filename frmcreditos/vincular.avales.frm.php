<?php
/**
 * Avales de creditos, forma de captura
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 * @subpackage forms
 * 		22/07/2008	Funciones mejoradas de Datos heredados
 */
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xHP		= new cHPage("TR.Avales de Credito");
$jxc 		= new TinyAjax();
$xF			= new cFecha();
$xLoc		= new cLocal();
$jxc 		= new TinyAjax();
function jsaVincularAval($tipo, $AvalPersona, $monto, $consanguinidad, $vinculado, $documento, $depende = false ){

	$xT		= new cTipos();
	$xDoc	= new cCredito($documento);
	$depende= $xT->cBool($depende);
	$xDoc->init();
	$xDoc->addAval($AvalPersona, $monto, $tipo, $consanguinidad, $depende);
	return $xDoc->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaVincularAval', array('idtipoderelacion', 'idsocio', 'idmonto', 'idtipodeparentesco', 'idpersonarelacionado', 'iddocumentorelacionado', 'dependiente'), "#idmsgs");
$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);

if( setNoMenorQueCero($persona) <= DEFAULT_SOCIO ){
		$xDoc			= new cCredito($credito); $xDoc->init();
		$persona		= $xDoc->getClaveDePersona();
		$monto			= $xDoc->getMontoAutorizado();
		if($monto <= 0){
			$monto		= $xDoc->getMontoSolicitado();
		}
}
$xHP->init();

$xFRM		= new cHForm("frmvincularavales", "./");
$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText();

$xFRM->addPersonaBasico();
$xFRM->addGuardar("jsVincularAval()");

$xFRM->addHElem( $xChk->get("TR.es dependiente_economico", "dependiente") );
$xFRM->addHElem( $xHSel->getListaDeTiposDeRelaciones("", PERSONAS_REL_CLASE_AVAL)->get(true) );
$xFRM->addHElem( $xHSel->getListaDeTiposDeParentesco("", false, DEFAULT_TIPO_CONSANGUINIDAD)->get(true)  );
$xFRM->OMoneda("idmonto", $monto, "TR.Monto Avalado");



$xFRM->OHidden("iddocumentorelacionado", $credito);
$xFRM->OHidden("idpersonarelacionado", $persona);
$xFRM->addAviso("");
echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
<script>
var xG	= new Gen();
function jsVincularAval(){
	session(TINYAJAX_CALLB, "jsClose()");
	jsaVincularAval();
}
function jsClose(){ xG.close(); }
</script>
<?php
$xHP->fin();
?>