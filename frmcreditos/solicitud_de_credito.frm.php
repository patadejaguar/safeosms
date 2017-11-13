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
//$oficial 	= elusuario($iduser);
$jxc		= new TinyAjax();
$xRuls		= new cReglaDeNegocio();
//$SinDatosDispersion	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_AUTORIZACION_SIN_DISP);		//regla de negocio
$SinFinalPlazo		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PRODUCTOS_SIN_FINALPZO);		//regla de negocio
$SinLugarPag		= false; //ACTUALIZAR
$ConOrigen			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_SOLICITUD_CON_ORIGEN);		//regla de negocio

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$monto		= parametro("monto",0, MQL_FLOAT);
$producto	= parametro("producto",DEFAULT_TIPO_CONVENIO, MQL_INT);
$idorigen	= parametro("idorigen",1, MQL_INT);
$tipoorigen		= parametro("origen",1, MQL_INT); //Tipo de Origen

$fecha		= parametro("fecha", fechasys(), MQL_DATE);
$pagos		= parametro("pagos", 1, MQL_INT);
$frecuencia	= parametro("frecuencia", false, MQL_INT);
$destino	= parametro("destino", FALLBACK_CRED_TIPO_DESTINO, MQL_INT);
$oficial	= parametro("oficial", getUsuarioActual(), MQL_INT);
$tasa		= parametro("tasa", 0, MQL_FLOAT);
$TipoCobro	= 0;

function jsaGetPerfilDePersona($persona){
	$tab 		= new TinyAjaxBehavior();
	$xSoc		= new cSocio($persona);
	$pagos		= 1;
	$periocidad	= DEFAULT_PERIOCIDAD_PAGO;
	$producto	= DEFAULT_TIPO_CONVENIO;
	$monto		= 0;
	if($xSoc->init() == true){
		if($xSoc->getClaveDeEmpresa() != FALLBACK_CLAVE_EMPRESA){
			$xEmp		= new cEmpresas($xSoc->getClaveDeEmpresa());
			$xEmp->init();
			$producto	= $xEmp->getProductoPref();
			
			$periocidad	= $xEmp->getPeriocidadPref();
			
			$OConv		= new cProductoDeCredito($producto);
			if($OConv->init() == true){
				$pagos		= $OConv->getNumeroPagosPreferente();
				$periocidad	= ($periocidad > 0) ? $periocidad : $OConv->getPeriocidadPrefente();
			}
		}
		$xEst	= new cPersonasEstadisticas($persona);
		$xEst->initDatosDeCredito();
		$creditoprior	= $xEst->getCreditoPrioritario();
		if($creditoprior > DEFAULT_CREDITO){
			$xCred	= new cCredito($creditoprior);
			if($xCred->init() == true){
				$monto		= $xCred->getMontoSolicitado();
				$periocidad	= ($periocidad > 0) ? $periocidad : $xCred->getPeriocidadDePago();
				if($producto == DEFAULT_TIPO_CONVENIO){
					$producto	= $xCred->getClaveDeConvenio();
				}
				if($pagos <= 1){
					$pagos	= $xCred->getPagosSolicitados();
					
				}
			}
		}
		
	}
	if($monto>0){
		if($pagos > 1){
			$tab -> add(TabSetValue::getBehavior('idnumerodepagos', $pagos ));
		}
		$xProd		= new cProductoDeCredito($producto);
		if($xProd->init() == true){
			$tab -> add(TabSetValue::getBehavior('idproducto', $producto ));
		}
		$tab -> add(TabSetValue::getBehavior('idperiocidad', $periocidad ));
		
		$tab -> add(TabSetValue::getBehavior('idmonto', $monto ));
	}
	return $tab->getString();	
}
function jsaGetPerfilDeProducto($producto,$periocidad,  $pagos){
	$tab 		= new TinyAjaxBehavior();
	//$OConv->init();
	///$tab 		= new TinyAjaxBehavior();
	//$xSoc		= new cSocio($persona);
	//$tab -> add(TabSetValue::getBehavior('idperiocidad', $periocidad ));
	//
	$OConv		= new cProductoDeCredito($producto);
	if($OConv->init() == true){
		if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO OR $pagos <=1){
			$tab -> add(TabSetValue::getBehavior('idnumerodepagos', $OConv->getNumeroPagosPreferente() ));
			$tab -> add(TabSetValue::getBehavior('idperiocidad', $OConv->getPeriocidadPrefente() ));
		}
		//$pagos		= $OConv->getNumeroPagosPreferente();
		//$periocidad	= ($periocidad > 0) ? $periocidad : $OConv->getPeriocidadPrefente();
	}
	return $tab->getString();
}

function jsaValidarCredito($socio){
	
	if($_SESSION[SYS_UUID] == null){
		
	} else {
		$xBtn	= new cHButton();
		$xBtn->setBClass("blue");
		return $xBtn->getBasic("TR.GUARDAR SOLICITUD", "jsFormularioValidado()", $xBtn->ic()->GUARDAR, "idvalidarok", false); 
	}
}

$jxc ->exportFunction('jsaValidarCredito', array('idsocio'), "#creditoaprobado");
$jxc ->exportFunction('jsaGetPerfilDePersona', array("idsocio") );
$jxc ->exportFunction('jsaGetPerfilDeProducto', array("idproducto", "idperiocidad", "idnumerodepagos") );

$jxc ->process();
echo $xHP->getHeader(true);

$xFRM		= new cHForm("frmsolicitudcredito", "", "frmsolicitudcredito");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xCred		= new cCredito();
$ready		= true;

$xFRM->setAction("solicitud_de_credito.2.frm.php", true);

//========== Origen
$xFRM->OHidden("idorigen", $idorigen);
$xFRM->OHidden("origen", $tipoorigen);
$xFRM->OHidden("tasa", $tasa);
//-- Manejar origen
if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO){
	$xArr	= new cCreditosLeasing($idorigen);
	if($xArr->init() == true){
		if($xArr->getDomicilia() == true){
			$TipoCobro	= $xCred->COBRO_DOMICILIADO;
		}
	}
}


//===========
$xFRM->setTitle( $xHP->getTitle() );
$xFRM->setNoAcordion();
if($ConOrigen == true AND ($idorigen == 1 OR $tipoorigen == 1)){
	echo JS_CLOSE;
	$ready		= false;
	$xFRM->addAvisoRegistroError("TR.Requiere un ORIGEN_DE_CREDITO");
}
if($ready == true){
	
	if($persona > DEFAULT_SOCIO){
		$xPer	= new cSocio($persona);
		if($xPer->init() == true){
			$xFRM->addHElem( $xPer->getFicha(false, true, "", true) );
			$xFRM->OHidden("persona", $persona);
		}
	} else {
		
		
		if($ConOrigen == false){
			$xFRM->addPersonaBasico("", false, $persona, "jsaGetPerfilDePersona();");
		} else {
			$xFRM->addPersonaBasico("", false, $persona);
		}
	}
	$xFRM->addSeccion("didivgeneral", "TR.INFORMACION_GENERAL");
	
	
	$xFRM->ODate("idFechaSolicitud", $fecha, "TR.Fecha de Solicitud");

	//========== Producto de Credito
	$selPdto	= $xSel->getListaDeProductosDeCredito("", $producto, true);
	if($ConOrigen == false){
		$selPdto->addEvent("onblur", "jsaGetPerfilDeProducto()");
		$selPdto->addEvent("onchange", "jsaGetPerfilDeProducto()");
	}
	$xFRM->addHElem($selPdto->get("TR.producto de credito", true) );
	
	
	$selFreq	= $xSel->getListaDePeriocidadDePago("", $frecuencia);
	
	$selFreq->addEvent("onblur", "jsSetFrecuenciaDePagos(this)");
	$xFRM->addHElem($selFreq->get(true));
	
	$xFRM->addHElem($xSel->getListaDeTipoDePago()->get(true));
	//=============== Tipo y lugart de cobro
	//if($SinLugarPag == true){
	//	$xFRM->OHidden("idtipolugarcobro",);
	//} else {
	$xFRM->addHElem( $xSel->getListaDeTipoDeLugarDeCobro("", $TipoCobro)->get(true) );
	//}
	
	if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $pagos > 0){
		$xFRM->ODisabled_13("idnumerodepagos", $pagos, "TR.Numero de pagos");
	} else if ($tipoorigen == $xCred->ORIGEN_LINEAS AND $pagos > 0){
		$xFRM->ODisabled_13("idnumerodepagos", $pagos, "TR.Numero de pagos");
	} else {
		$xFRM->OMoneda("idnumerodepagos", $pagos, "TR.Numero de pagos");
	}
	
	if($SinFinalPlazo == true OR ($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $pagos > 0) ){
		$xFRM->OHidden("idFechaVencimiento", fechasys());
	} else {
		$xFRM->ODate("idFechaVencimiento", false, "TR.Fecha de Vencimiento");
	}
	
	$xFRM->ODate("idFechaMinistracion", false, "TR.Fecha de Ministracion");
	if($monto > 0){
		$xCant	= new cCantidad($monto);
		$xFRM->OHidden("idmonto", $monto);
		$xFRM->addHElem($xCant->getFicha());
	} else {
		$xFRM->addHElem($xTxt->getDeMoneda("idmonto", "TR.Monto Solicitado", $monto, true));
	}

	if($producto == CREDITO_PRODUCTO_CON_PRESUPUESTO){
		//Destino Detallado
		$xFRM->OHidden("iddestinodecredito", 98);//98 == DETALLADO
		$xFRM->OHidden("iddescripciondestino", "");
	} else {
		$xFRM->addSeccion("iddivdestino", "TR.DESTINO DE LOS_RECURSOS");
		$txt2		= new cHText();
		$txt2->setDivClass("");
		$xFRM->addDivSolo($xSel->getListaDeDestinosDeCredito("", $destino)->get(false), $txt2->get("iddescripciondestino", false, "TR.Descripcion del Destino de los_recursos"), "tx24", "tx24" );
		$xFRM->endSeccion();
	}

	
	$xFRM->endSeccion();
	//====================================== Ahorro condicionado
	if(MODULO_CAPTACION_ACTIVADO == true){
		$xFRM->addSeccion("iddivahorro", "TR.AHORRO_CONDICIONADO");
		$xFRM->addCuentaCaptacionBasico(false, CAPTACION_TIPO_VISTA, false, $cuenta);
		$xFRM->endSeccion();
	} else {
		$xFRM->addHTML("<input type='hidden' value='" . DEFAULT_CUENTA_CORRIENTE . "' id='idcuenta' name='idcuenta' />");
	}
	//====================================== cuestionario
	$xFRM->addSeccion("didivdivision", "TR.CUESTIONARIO");
	//si es credito renovado
	if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $idorigen > 0){
		$xFRM->OHidden("idrenovado", "false");
	} else {
		$xFRM->OCheck("TR.Es Credito Renovado", "idrenovado");
	}
	if(MODULO_AML_ACTIVADO == true){
		$xFRM->OCheck("TR.PREGUNTA_AML_CREDITO_2", "idpropietario");
		$xFRM->OCheck("TR.PREGUNTA_AML_CREDITO_1", "idproveedor");
	}
	$xFRM->endSeccion();
	//====================================== Otros
	$xFRM->addSeccion("iddivotros", "TR.OTROS");
	$xFRM->addHElem($xSel->getListaDeOficiales("oficial",SYS_USER_ESTADO_ACTIVO, $oficial)->get(true));
	$xFRM->addObservaciones();
	$xFRM->endSeccion();
		
	$xFRM->addCerrar();
	$xFRM->OButton("TR.Validar Credito", "jsValidarCredito()", $xFRM->ic()->CHECAR, "idcheck", "green");
	$xFRM->addToolbar("<span id='creditoaprobado'></span>");
	
	if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $idorigen > 0){
		$xFRM->addDisabledInit("iddestinodecredito");
		$xFRM->addDisabledInit("idtipolugarcobro");
		$xFRM->addDisabledInit("idtipodepago");
		$xFRM->addDisabledInit("idproducto");
		$xFRM->addDisabledInit("idperiocidad");
	}
	
	if($tipoorigen == $xCred->ORIGEN_LINEAS AND $idorigen > 0){
		//$xFRM->addDisabledInit("iddestinodecredito");
		//$xFRM->addDisabledInit("idtipolugarcobro");
		$xFRM->addDisabledInit("idtipodepago");
		$xFRM->addDisabledInit("idproducto");
		$xFRM->addDisabledInit("idperiocidad");
	}
	
	$xFRM->addAviso(" ");
}


echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var wFrm 			= document.frmsolicitud;
var mMonto			= 0;
var xGen			= new Gen();
var mNumPago		= $("#idnumerodepagos");
var mTipoPago		= $("#idtipodepago");
var mPeriocidad		= $("#idtipodepago");
function jsChecarFinalDePlazo(){
	var mNumPago	= $("#idnumerodepagos");
	var mTipoPago	= $("#idtipodepago");
	if(mPeriocidad.val() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO && mNumPago.val() <= 1 ){
		xGen.alerta({ msg : "TR.Numero de Pagos Invalido!"});
		return false;
	}
}
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
	var FAct		= $("#idFechaSolicitud").val();
	if($("#persona").length > 0){
	var Pers		= $("#persona").val();
	} else {
		var Pers	= $("#idsocio").val();
	}
	var Monto		= $("#idmonto").val();
	var Cont		= $("#idcuenta").val();
	var Pdto		= $("#idproducto").val();
	var claveorigen	= $("#idorigen").val();
	var tipoorigen	= $("#origen").val();
	
	$("#creditoaprobado").empty();
	var murl		= "solicitud_de_credito.validacion.frm.php?persona=" + Pers;
	murl			+= "&monto=" + Monto + "&contrato=" + Cont + "&periocidad=" + mFreq + "&producto=" + Pdto + "&vencido=" + FVenc + "&ministrado=" + FMin + "&pagos=" + mNumPago + "&fecha=" + FAct;
	murl			+= "&tipoorigen=" + tipoorigen + "&claveorigen=" + claveorigen;
	xGen.w({ url : murl , tiny: true, callback : jsaValidarCredito });
}
function jsFormularioValidado(){	$("#frmsolicitudcredito").submit(); }
</script>
</html>
