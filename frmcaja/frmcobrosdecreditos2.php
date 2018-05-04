<?php
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
	$iduser 		= $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.COBROS DE CREDITOS .- 2", HP_FORM);
$xF			= new cFecha();
$xCaja		= new cCaja();

$html		= "";
$msg		= "";

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	$xHP->goToPageError(200); }

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$parcialidad= parametro("idparcialidad", 0, MQL_INT); $parcialidad = parametro("periodo", $parcialidad, MQL_INT); $parcialidad = parametro("parcialidad", $parcialidad, MQL_INT);
$Fecha		= parametro("idfecha-0", false);
$Fecha		= parametro("fecha", $Fecha, MQL_DATE); $Fecha = parametro("idfechaactual", $Fecha, MQL_DATE);
$Fecha		= $xF->getFechaISO($Fecha);

echo $xHP->getHeader( true );
$jsNoValido			= "<script>	jsRegresarConTemporizador({
		url: '../index.xul.php?p=../frmcaja/frmcobrosdecreditos.php',
		msg : 'Credito No Operable ($persona|$credito)' });	</script>";
if( setNoMenorQueCero($credito) <= DEFAULT_CREDITO){
	exit($jsNoValido);
}
if(PERMITIR_EXTEMPORANEO == true){
		$_SESSION[FECHA_OPERATIVA]	= $Fecha;//$Fecha				= $_SESSION[FECHA_OPERATIVA];
}
if(isset($_REQUEST["fecha"])){
	$_SESSION[FECHA_OPERATIVA]	= $_REQUEST["fecha"];
	$Fecha						= $_SESSION[FECHA_OPERATIVA];
}
$xCred = new cCredito($credito);
$xCred->init();
$xCred->setRevisarSaldo();
$persona			= $xCred->getClaveDePersona();
$FechaInicial		= $xCred->getFechaDeMinistracion();

if(CREDITO_GENERAR_DEVENGADOS_ONFLY == true OR $xCred->isAFinalDePlazo() == true){
	if($xF->getInt($xCred->getFechaUltimoDePago()) <= $xF->getInt($Fecha)){
		$msg		.= $xCred->setReestructurarIntereses(false, $Fecha, true);
	}
}

$periocidad 		= $xCred->getPeriocidadDePago();
$xJsBasic			= new jsBasicForm("frmProcesarPago");

$xHP->addHSnip($xJsBasic->setIncludeJQuery() );

echo $xHP->setBodyinit();

if( $xCred->isPagable() == false ){ 
	exit( $jsNoValido );
} else {
//Style
?><style>.formoid-default{padding-bottom:0;</style><?php
$xFRM		= new cHForm("frmProcesarPago", "./", "frmProcesarPago");
//selector de Cobros
$xhBtn		= new cHButton();
$btns		= "";

$xFRM->setNoAcordion();
$idnumeroplan		= $xCred->getNumeroDePlanDePagos();
$montoapagar		= 0;

$xFRM->addRefrescar("jsCargarFrame()");



$defaultPago	= OPERACION_PAGO_COMPLETO;
switch($periocidad){
	case CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO:
		$xFRM->OButton("TR.ABONOS", "jsGetPago('ao')", $xFRM->ic()->ACTUAL, "pc2", "white");
		$xFRM->OButton("TR.PAGO COMPLETO", "jsGetPago('pc')", $xFRM->ic()->LLENO, "pc1", "blue3");
		$montoapagar	= $xCred->getSaldoActual();
		break;
	default:
		$xFRM->OButton("TR.LETRA COMPLETA", "jsGetPago('plc')", $xFRM->ic()->ACTUAL, "pc2", "white");
		$xFRM->OButton("TR.LETRA VARIABLE", "jsAjustarPagoF()", $xFRM->ic()->RANDOM, "pc3", "yellow");
		//$xFRM->OButton("TR.LETRA VARIABLE", "jsGetPago('pli')", $xFRM->ic()->RANDOM, "pc3", "yellow");
		$xFRM->OButton("TR.PAGO COMPLETO", "jsGetPago('pc')", $xFRM->ic()->LLENO, "pc1", "blue3");
		$defaultPago	= OPERACION_PAGO_LETRA_COMPLETA;
		$xLetra			= new cParcialidadDeCredito();
		$xLetra->init($xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), $parcialidad);
		$montoapagar	= $xLetra->getTotal();
		
		break;
}
$xFRM->setTitle($xHP->getTitle());

if( setNoMenorQueCero($idnumeroplan) > 0) {
	$xFRM->OButton("TR.PLAN_DE_PAGOS", "var xC=new CredGen();xC.getImprimirPlanPagos($idnumeroplan);", $xFRM->ic()->CALENDARIO1);
	$xFRM->OButton("TR.Parcialidades Pendientes", "var xcg = new CredGen();xcg.getLetrasEnMora($credito)", $xFRM->ic()->PREGUNTAR);
}

if(MODO_DEBUG == true){
	//$xFRM->addAtras();
}

$xFRM->OButton("TR.Guardar Pago", "jsGuardarPagoF()", $xFRM->ic()->GUARDAR, "btn_guardar", "green");
$xFRM->OButton("Vista Previa", "jsViewPagoF()", $xFRM->ic()->VER, "idprev");
$xFRM->addImprimir();

$xFRM->addCerrar();

if(MODO_DEBUG == true AND ($xCred->getTipoEnSistema() != SYS_PRODUCTO_NOMINA )){ 
	$xFRM->addLog($msg); 
}

$xTxt			= new cHText("idobservaciones");
$xTxt->addEvent("jsActualizarObservacion(this)", "onchange");
$xTxt->addEvent("jsActualizarObservacion(this)", "onblur");

$xFRM->addHElem( $xCred->getFicha(false, "", false, true) );
$xFRM->addCobroBasico("onchange='jsGetPago()'");
$xFRM->addObservaciones();
$xFRM->setValidacion("idobservaciones", "jsActualizarObservacion");

//$xFRM->addTag("-", "warning");

//$xFRM->addTag("<strong>". $xFRM->getT("TR.TOTAL") . "</strong> : <span id='spantotal' style='font-size:1.5em'></span>", "notice");
$strInfo	= $xFRM->getT("TR.FECHA") . "&target;" . $xF->getFechaDDMM($Fecha) . "&dash;" . $xFRM->getT("TR.PARCIALIDAD") . " &numero; $parcialidad";
$xFRM->addSeccion("iddivpagos", $strInfo);

$xFRM->addHElem("<iframe id=\"idFPrincipal\" src=\"./../principal.php\" width='100%' height=\"800px\" ></iframe>");

$xFRM->addHElem( $html );
$xFRM->addJsInit("jsAsLoaded();");
echo $xFRM->get();


?>
<script>
var iSRC 		= "./frmprocesarpago.php?<?php echo "p=$persona|$credito|$parcialidad|$periocidad|" ?>";
var ixsrc		= "./frmcobrosdecreditos2.php?<?php echo "idsocio=$persona&idsolicitud=$credito&idparcialidad=" ?>";
var parcial		= <?php echo $parcialidad; ?>;
var mTipoPago	= "<?php echo $defaultPago; ?>";
var oTipoPago	= $("#idtipo_pago");
var oReciboFis	= $("#id-foliofiscal");
var xGen		= new Gen();
var sURI 		= "";
var iFr 		= document.getElementById("idFPrincipal");
var iWFram		= document.getElementById('idFPrincipal').contentWindow;
var mTotalPag	= <?php echo round($montoapagar,2); ?>;
var mStrInfo	= "<?php echo $strInfo; ?>";

function jsAsLoaded(){
	var mFormaPago		= oTipoPago.val();
	sURI 				= iSRC + TESORERIA_MONTO_MAXIMO_OPERADO + "|" + mTipoPago+ "|" + mFormaPago + "|" + oReciboFis.val();
	xGen.verControl("cmdimprimir", false);
	jsCargarFrame();
}
function jsRegresar(){ var g	= new Gen(); g.w({url: "frmcobrosdecreditos.php"}); }
function jsGetPago(vTipoPago, vMonto){
	vTipoPago			= (typeof vTipoPago == "undefined") ? mTipoPago : vTipoPago;
	vMonto				= (typeof vMonto == "undefined") ? mTotalPag : vMonto;
	
	mTipoPago			= vTipoPago;
	var mFormaPago		= oTipoPago.val();
	var monto			= vMonto;
	//Parcialidad Incompleta o parcialidades varias
	if (mTipoPago == "pli" || mTipoPago == "plv" || mTipoPago == "ao"){
		$("#h3iddivpagos").addClass("yellow");
		//monto 			= window.prompt("---CAPTURE EL MONTO---\n---QUE SE PRESENTA---\n PARA PAGAR EL CREDITO", 0.00);
	} else {
		monto			= TESORERIA_MONTO_MAXIMO_OPERADO;
		if (mTipoPago == "pc"){
			$("#h3iddivpagos").addClass("blue3");
		} else {
			$("#h3iddivpagos").removeClass();
		}
	}
	if( flotante(monto) > 0) {
		sURI 			= iSRC + monto + "|" + mTipoPago+ "|" + mFormaPago + "|" + oReciboFis.val();
	} else {
		xGen.alerta({ msg: "MSG_MONTO_REQUIRED"});
	}
	
	jsCargarFrame();
}
function jsCargarFrame(){
	xGen.spinInit();
	xGen.QFrame({ id : "idFPrincipal", url : sURI });
	$("#idtipo_pago").focus();
}

function jsFrameTotalActualizado(){
	var mxTotalPag	= iWFram.jsGetTotal();
	//console.log();
	//xGen.notify({msg: "Frame Actualizado..."});
	var simb = Configuracion.moneda.simbolo + " "  + mxTotalPag;
	$("#h3iddivpagos").html(mStrInfo + " - " + simb);
}

function jsActualizarObservacion(){
	if ($("#idFPrincipal").length > 0){
		//
		var ddoc	= document.getElementById("idFPrincipal").contentWindow.document;
		if(ddoc.getElementById("idobservaciones")){
			ddoc.getElementById("idobservaciones").value = $("#idobservaciones").val();
		}
	} else {
		jsCargarFrame();
	}
	return true;
}
function jsEndCarga(){
	xGen.spinEnd();
}
function jsAjustarPagoF(){
	
	var mxTotalPag	= iWFram.jsGetTotal();
	var mMonto	= window.prompt("---CAPTURE EL MONTO---\n---QUE SE PRESENTA---\n PARA PAGAR EL CREDITO", mxTotalPag);
	mMonto		= redondear(mMonto);
	if(mMonto < mxTotalPag){
		jsGetPago('pli', mMonto);
		
	} else {
		iWFram.jsGetPagoAjustado(mMonto);
	}
}
function jsGuardarPagoF(){
	jsDisableSave();
	iWFram.FormSucess();
}
function jsViewPagoF(){
	iWFram.showVistaPago();
}
function jsEnableSave(){
	xGen.ena("#btn_guardar");
}
function jsDisableSave(){
	xGen.dis("#btn_guardar");
}
function jsRemoveSave(){
	xGen.verControl("btn_guardar", false);
	xGen.verControl("pc2", false);
	xGen.verControl("pc3", false);
	xGen.verControl("pc1", false);
	xGen.verControl("idprev", false);
	xGen.verControl("cmdrefresh", false);
}
function jsImprimirRecibo(){
	iWFram.printrec();
}
function jsEnablePrint(){
	xGen.verControl("cmdimprimir", true);
}
</script>
<?php
}

$xHP->fin();
?>