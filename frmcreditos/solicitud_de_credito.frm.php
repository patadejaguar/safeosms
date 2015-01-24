<?php
/**
 * Solicitud de Creditos, forma de captura
 * @author Balam Gonzalez Luis Humberto
 * @version 1.50
 * @package creditos
 * @subpackage forms
 * 		22/07/2008	Funciones mejoradas de riesgo
 * 					Implementacion de php doc
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Solicitud de Credito");

$oficial 	= elusuario($iduser);

$jxc		= new TinyAjax();

function jsaGetPerfilDeProducto($producto, $persona){
	$OConv		= new cProductoDeCredito($producto); $OConv->init();
	$tab 		= new TinyAjaxBehavior();
	$xSoc		= new cSocio($persona);
	$pagos		= $OConv->getNumeroPagosPreferente();
	$periocidad	= $OConv->getPeriocidadPrefente();
	if($xSoc->init() == true){
		if($xSoc->getClaveDeEmpresa() != FALLBACK_CLAVE_EMPRESA){
			$xEmp	= new cEmpresas($xSoc->getClaveDeEmpresa());
			$xEmp->init();
			$tab -> add(TabSetValue::getBehavior('idproducto', $xEmp->getProductoPref() ));
			$periocidad	= $xEmp->getPeriocidadPref();
		}
	}
	
	$tab -> add(TabSetValue::getBehavior('idnumerodepagos', $pagos ));
	$tab -> add(TabSetValue::getBehavior('idperiocidad', $periocidad ));
	
	return $tab->getString();	
}
function jsaValidarCredito($socio){
	
	if($_SESSION[SYS_UUID] == null){
		
	} else {
		$xBtn	= new cHButton();
		return $xBtn->getBasic("TR.guardar credito", "jsFormularioValidado()", "guardar", "idvalidarok", false); 
	}
}

$jxc ->exportFunction('jsaValidarCredito', array('idsocio'), "#creditoaprobado");
$jxc ->exportFunction('jsaGetPerfilDeProducto', array("idproducto", "idsocio") );

$jxc ->process();
echo $xHP->getHeader(true);

$xFRM		= new cHForm("frmsolicitudcredito", "solicitud_de_credito.2.frm.php", "frmsolicitudcredito");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

$xFRM->addPersonaBasico();

$xFRM->ODate("idFechaDeSolicitud", false, "TR.Fecha de Solicitud");
$selPdto	= $xSel->getListaDeProductosDeCredito();
$selPdto->addEvent("onchange", "jsaGetPerfilDeProducto()");
$selPdto->addEvent("onfocus", "jsaGetPerfilDeProducto()");

$xFRM->addHElem($selPdto->get("TR.producto de credito", true) );
//$xFRM->addHElem($xTxt->getDeMoneda("idnuevocredito", "TR.clave_de_credito"));
$selFreq	= $xSel->getListaDePeriocidadDePago();
$selFreq->addEvent("onblur", "jsSetFrecuenciaDePagos(this)");
$xFRM->addHElem($selFreq->get(true));

$xFRM->addHElem($xSel->getListaDeTipoDePago()->get(true));
$xFRM->OMoneda("idnumerodepagos", 1, "TR.Numero de pagos");
$xFRM->ODate("idFechaVencimiento", false, "TR.Fecha de Vencimiento");
$xFRM->ODate("idFechaMinistracion", false, "TR.Fecha de Ministracion");

$xFRM->addHElem($xTxt->getDeMoneda("idmonto", "TR.Monto Solicitado", 0, true));
if(MODULO_CAPTACION_ACTIVADO == true){	
	$xFRM->addCuentaCaptacionBasico(false);
} else {
	$xFRM->addHTML("<input type='hidden' value='" . DEFAULT_CUENTA_CORRIENTE . "' id='idcuenta' name='idcuenta' />");
}

$txt2		= new cHText();
$txt2->setDivClass("");
$xFRM->addDivSolo($xSel->getListaDeDestinosDeCredito()->get(false), $txt2->get("iddescripciondestino", false, "TR.Descripcion del Destino de los_recursos"), "tx14", "tx34" );
$xFRM->addObservaciones();
//si es credito renovado
$xFRM->OCheck("TR.Es Credito Renovado", "idrenovado");
if(MODULO_AML_ACTIVADO == true){
	$xFRM->OCheck("TR.PREGUNTA_AML_CREDITO_2", "idpropietario");
	$xFRM->OCheck("TR.PREGUNTA_AML_CREDITO_1", "idproveedor");
}



$xFRM->addCerrar();
$xFRM->OButton("TR.Validar Credito", "jsValidarCredito()", "checar", "idcheck");
$xFRM->addToolbar("<span id='creditoaprobado'></span>");
$xFRM->addAviso(" ");



echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
/*
$(document).ready(function () {
	$('#id-frmsolingreso').isHappy({
	    fields: {
	      '#idnombrecompleto': {
			required : true,
	        message: 'Necesita Capturar un Nombre',
			test : jsCheckNombres
	      },
	    }
	  });	
});
*/
var wFrm 	= document.frmsolicitud;
var mMonto	= 0;
var xGen	= new Gen();
function jsSetFrecuenciaDePagos(evt){
	var mFreq		= entero(evt.value);
	var mNumPago	= $("#idnumerodepagos");
	var mTipoPago	= $("#idtipodepago");
	var mFechaVenc	= $("#idFechaVencimiento");
	
	if(mFreq == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
		mNumPago.css("display" , "none");
		mNumPago.val(1);
		mTipoPago.css("display" , "none");
		mTipoPago.val(CREDITO_TIPO_PAGO_UNICO);
		mFechaVenc.css("display" , "inherit");
	} else {
		mNumPago.css("display" , "inherit");
		mTipoPago.css("display" , "inherit");
		mFechaVenc.css("display" , "none");
		mTipoPago.val(CREDITO_TIPO_PAGO_PERIODICO);
	}
}
function jsValidarMonto(){
	mMonto	= flotante($("#sol").val());
	jsaValidarMontoDeCredito();
	setTimeout("jsNotificarValidacion()", 1000);
}
function jsNotificarValidacion() {
	var nMonto	= flotante($("#sol").val());
	if (nMonto < mMonto) {
		alert("El Monto capturado " + mMonto + " ha sido Cambiado a " + nMonto + "\npor el sistema en base a Politicas de la Institucion.\Consulte a su Administrador." );
	}
	///jsaGetLetrasByNumero();
}
function jsValidarCredito(){
	var mNumPago	= $("#idnumerodepagos").val();
	var mTipoPago	= $("#idtipodepago").val();
	var mFreq		= $("#idperiocidad").val();
	var FMin		= $("#idFechaMinistracion").val();
	var FVenc		= $("#idFechaVencimiento").val();
	var Pers		= $("#idsocio").val();
	var Monto		= $("#idmonto").val();
	var Cont		= $("#idcuenta").val();
	var Pdto		= $("#idproducto").val();
	$("#creditoaprobado").empty();
	var murl		= "solicitud_de_credito.validacion.frm.php?persona=" + Pers;
	murl			+= "&monto=" + Monto + "&contrato=" + Cont + "&periocidad=" + mFreq + "&producto=" + Pdto + "&vencido=" + FVenc + "&ministrado=" + FMin + "&pagos=" + mNumPago;
	xGen.w({ url : murl , tiny: true, callback : jsaValidarCredito });
}
function jsFormularioValidado(){	$("#frmsolicitudcredito").submit(); }
</script>
</html>
