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
$xHP		= new cHPage("TR.DATOS_DE_TRANSFERENCIA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();

function jsaGuardarDatosBanco($credito, $banco, $cuenta, $clabe, $vencimiento, $tipolugarcobro){
	$xCred		= new cCredito($credito); $xCred->init();
	$Cat		= $xCred->OCatOtrosDatos();
	$datobanco	= "";
	$xBan		= new cBancos_entidades(); $xBan->setData( $xBan->query()->initByID($banco) );
	$datobanco	= $xBan->idbancos_entidades()->v() . " " . STD_LITERAL_TER_DIV . " " . $xBan->nombre_de_la_entidad()->v();
	$xCred->setOtrosDatos($Cat->DEPOSITO_BANCO, strtoupper($datobanco));
	$xCred->setOtrosDatos($Cat->DEPOSITO_CTA_BANCARIA, $cuenta);
	$xCred->setOtrosDatos($Cat->DEPOSITO_CLABE_BANCARIA, $clabe);
	$xCred->setOtrosDatos($Cat->DEPOSITO_FECHA_VENCE, $vencimiento);
	$xCred->setTipoDeLugarDeCobro($tipolugarcobro, true);
	return $xCred->getMessages(OUT_HTML);
}
$jxc->exportFunction('jsaGuardarDatosBanco', array('idcredito', 'idbanco', 'idctabancaria', 'idctaclabe', 'idvencimiento', 'idtipolugarcobro'), "#idmsgs");
$jxc->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xOD		= new cCreditosOtrosDatos();

$xCred		= new cCredito($credito);

$xHP->init();

$xFRM		= new cHForm("frmdatosbanco", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

if($xCred->init() == true){
	//if($SinLugarPag == true){
	//	$xFRM->OHidden("idtipolugarcobro", $TipoDeLugarDeCobro);
	//} else {
	$xFRM->addHElem( $xSel->getListaDeTipoDeLugarDeCobro("", $xCred->getTipoDeLugarDeCobro())->get(true) );
	//}
	
	$idbanco	= $xCred->getOtroDatos($xOD->DEPOSITO_BANCO);
	$dbanco		= explode(STD_LITERAL_TER_DIV, $idbanco);
	$idbanco	= setNoMenorQueCero($dbanco[0]);
	
	$xFRM->addHElem( $xSel->getListaDeBancos("idbanco", $idbanco)->get("TR.".$xOD->DEPOSITO_BANCO, true) );
	$xFRM->OMoneda("idctabancaria", $xCred->getOtroDatos($xOD->DEPOSITO_CTA_BANCARIA), "TR.".$xOD->DEPOSITO_CTA_BANCARIA);
	$xFRM->OMoneda("idctaclabe", $xCred->getOtroDatos($xOD->DEPOSITO_CLABE_BANCARIA), "TR.".$xOD->DEPOSITO_CLABE_BANCARIA);
	$xFRM->ODate("idvencimiento", fechasys(), "TR.Fecha de Vencimiento");
	$xFRM->addGuardar("jsaGuardarDatosBanco()");
	$xFRM->OHidden("idcredito", $credito, "");

	$xFRM->addAviso("");
}
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var xG	= new Gen();
function jsSalir(){	xG.close(); }
session(TINYAJAX_CALLB, "jsSalir()");

</script>
<?php
$xHP->fin();
?>