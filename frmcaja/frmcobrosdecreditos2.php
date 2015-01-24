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
$xHP				= new cHPage("TR.COBROS DE prestamos.- PASO 02", HP_FORM);
$xF					= new cFecha();
$msg				= "";
$socio 				= (isset($_REQUEST["idsocio"]) ) ? $_REQUEST["idsocio"] : DEFAULT_SOCIO;
$solicitud 			= (isset($_REQUEST["idsolicitud"]) ) ? $_REQUEST["idsolicitud"] : DEFAULT_CREDITO; //$_REQUEST["idsolicitud"];
$parcialidad 		= (isset($_REQUEST["idparcialidad"]) ) ? $_REQUEST["idparcialidad"] : 1;

$socio 				= (isset($_REQUEST["persona"]) ) ? $_REQUEST["persona"] : $socio;
$solicitud 			= (isset($_REQUEST["credito"]) ) ? $_REQUEST["credito"] : $solicitud;
$parcialidad 		= (isset($_REQUEST["periodo"]) ) ? $_REQUEST["periodo"] : $parcialidad;

/*if($socio == DEFAULT_SOCIO AND $solicitud != DEFAULT_CREDITO){
	
}*/
$Fecha				= parametro("idfecha-0", false);
$Fecha				= ($Fecha == false) ? fechasys() : $xF->getFechaISO($Fecha);

echo $xHP->getHeader( true );
$jsNoValido			= "<script>	jsRegresarConTemporizador({
		url: '../index.xul.php?p=../frmcaja/frmcobrosdecreditos.php',
		msg : 'Credito No Operable ($socio|$solicitud)' });	</script>";
if($solicitud === DEFAULT_CREDITO){
	exit($jsNoValido);
}
if(PERMITIR_EXTEMPORANEO == true){
		$_SESSION[FECHA_OPERATIVA]	= $Fecha;//$Fecha				= $_SESSION[FECHA_OPERATIVA];
}
if(isset($_REQUEST["fecha"])){
	$_SESSION[FECHA_OPERATIVA]	= $_REQUEST["fecha"];
	$Fecha							= $_SESSION[FECHA_OPERATIVA];
}
$xCred = new cCredito($solicitud);
$xCred->init();
$socio	= $xCred->getClaveDePersona();

$FechaInicial		= $xCred->getFechaDeMinistracion();
if(CREDITO_GENERAR_DEVENGADOS_ONFLY == true){
	$msg			.= $xCred->setReestructurarIntereses(false, $Fecha, true);
}

//$dcreds 			= $xCred->getDatosDeCredito() ;
$periocidad 		= $xCred->getPeriocidadDePago();
$xJsBasic			= new jsBasicForm("frmProcesarPago");

$xHP->addHSnip($xJsBasic->setIncludeJQuery() );


echo $xHP->setBodyinit();


if( $xCred->isPagable() == false ){ 
	exit( $jsNoValido );
} else {

$oFrm		= new cHForm("frmProcesarPago", "./", "frmProcesarPago");

//selector de Cobros
$xHCob		= new cHCobros();
$xSelP		= new cHSelect();
$xhBtn		= new cHButton();

$btns		= "";

$defaultPago	= OPERACION_PAGO_COMPLETO;
switch($periocidad){
	case CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO:
		$oFrm->addToolbar( $xhBtn->getBasic("TR.ABONO ORDINARIO", "jsGetPago('ao')", "dinero", "pc2", false) );
		$oFrm->addToolbar( $xhBtn->getBasic("TR.PAGO COMPLETO", "jsGetPago('pc')", "dinero", "pc1", false) );
		break;
	default:
		$oFrm->addToolbar( $xhBtn->getBasic("TR.LETRA COMPLETA", "jsGetPago('plc')", "dinero", "pc2", false) );
		$oFrm->addToolbar( $xhBtn->getBasic("TR.LETRA VARIABLE", "jsGetPago('pli')", "dinero", "pc3", false ) );
		$oFrm->addToolbar( $xhBtn->getBasic("TR.PAGO COMPLETO", "jsGetPago('pc')", "dinero", "pc1", false ) );
		$defaultPago	= OPERACION_PAGO_LETRA_COMPLETA;
		break;
}
$oFrm->addToolbar(  $xhBtn->getRegresar("../index.xul?p=frmcaja/frmcobrosdecreditos.php", true) );
$oFrm->addToolbar(  $xhBtn->getBasic("TR.Recargar", "jsCargarFrame()", "refrescar", "idrefrescar", false ));

if(MODO_DEBUG == true AND ($xCred->getTipoEnSistema() != CREDITO_PRODUCTO_NOMINA )){
	$xLog	= new cFileLog(false, true);
	$xLog->setWrite($msg);
	$xLog->setClose();
	$oFrm->addToolbar($xLog->getLinkDownload("Log de descargas", ""));
}

$xSelP->addEvent("showInCommand()", "onblur");
$xTxt			= new cHText("idobservaciones");

$html			= "";
$oFrm->addFooterBar( "<h3>FECHA DE PAGO : [" . $xF->getFechaDDMM($Fecha) . "] NUMERO DE PARCIALIDAD: [$parcialidad]</h3>" );
$html			.= $xCred->getFicha(false, "", false, true);
$xHCob->setEvents("onblur='jsGetPago()'");
$html			.=  $xHCob->get(false, "", "", false);
$html			.= $xTxt->get("idobservaciones", "", "Observaciones");
$html			.= "<iframe id=\"idFPrincipal\" src=\"./../principal.php\" width='100%' height=\"800px\" ></iframe>";

$oFrm->addHElem( $html );
echo $oFrm->get();

echo $xHP->setBodyEnd();
?>
<script>
var iSRC 		= "./frmprocesarpago.php?<?php echo "p=$socio|$solicitud|$parcialidad|$periocidad|" ?>";
var ixsrc		= "./frmcobrosdecreditos2.php?<?php echo "idsocio=$socio&idsolicitud=$solicitud&idparcialidad=" ?>";
var parcial		= <?php echo $parcialidad; ?>;
var mTipoPago	= "<?php echo $defaultPago; ?>";
var oTipoPago	= $("#idtipo_pago");
var oReciboFis	= $("#id-foliofiscal");
var xGen		= new Gen();
var sURI 		= "";
var iFr 		= document.getElementById("idFPrincipal");
$(document).ready(function(){ $("#idtipo_pago").focus(); setTimeout("jsCargarFrame()",500); });

function jsRegresar(){ var g	= new Gen(); g.w({url: "frmcobrosdecreditos.php"}); }
function jsGetPago(vTipoPago){
	vTipoPago			= (typeof vTipoPago == "undefined") ? mTipoPago : vTipoPago;
	mTipoPago			= vTipoPago;
	var mFormaPago		= oTipoPago.val();
	var monto			= 0;
	//Parcialidad Incompleta o parcialidades varias
	if (mTipoPago == "pli" || mTipoPago == "plv" || mTipoPago == "ao"){
		monto 			= window.prompt("---CAPTURE EL MONTO---\n---QUE SE PRESENTA---\n PARA PAGAR EL CREDITO", 0.00);
	} else {
		monto			= TESORERIA_MONTO_MAXIMO_OPERADO;
	}
	if( flotante(monto) > 0) {
		//Comando + Tipo de pago + recibo Fiscal
		sURI 	= iSRC + monto + "|" + mTipoPago+ "|" + mFormaPago + "|" + oReciboFis.val();
		tip("#fichadecredito", "Carga Lista!", 2000, IMG_LOADING, function(){ 	jsCargarFrame();	});
	} else {
		alert("DEBE CAPTURAR UN MONTO MAYOR A CERO\nPARA QUE EL COBRO SE EFECTUE");
		$("#idtipo_pago").focus();
	}
}
function jsCargarFrame(){ xGen.QFrame({ id : "idFPrincipal", url : sURI }); $("#idobservaciones").focus(); }
</script>
<?php
}

$xHP->end();
?>