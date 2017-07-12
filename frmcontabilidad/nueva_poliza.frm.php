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
$xHP		= new cHPage("TR.Poliza_Contable", HP_FORM);
$xQL		= new cSQLListas();
$jxc 		= new TinyAjax();

function jsaGuardarPolizas($fecha, $tipo, $centrocosto, $concepto){
	$xF			= new cFecha();
	$fecha 		= $xF->getFechaISO($fecha);
	$xPol		= new cPoliza($tipo);
	$xPol->add($concepto, $fecha, false, 0,0, false, $centrocosto);
	return $xPol->getMessages();
}

$jxc ->exportFunction('jsaGuardarPolizas', array('idfecha-0', 'idtipodepoliza', "idcentrodecosto", "idconcepto"), "#idmsgs");

$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "./");

$msg		= "";
$xBtn		= new cHImg();
$xHF		= new cHDate();
$xHS		= new cHSelect();
$xDiv		= new cHDiv();

$xFRM->addHElem( $xHF->get("TR.Fecha") );
$xFRM->addHElem( $xHS->getListaDeTiposDePolizas()->get(true) );
$xFRM->addHElem( $xHS->getListaDeTiposDeCentrosDeCosto()->get(true) );
$xFRM->OText("idconcepto", "", "TR.Concepto");

$xFRM->addAviso("");

$xFRM->addGuardar("jsGuardarPolizas()");

echo $xFRM->get();
?>
<script>
function jsGuardarPolizas(){
	jsaGuardarPolizas();
	setTimeout("jsEnd()", 2000);
}
function jsEnd(){
	var xG	= new Gen();
	xG.close();
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>