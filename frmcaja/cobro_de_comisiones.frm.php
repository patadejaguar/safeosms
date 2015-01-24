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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Cobro de Comisiones");
$xCaja		= new cCaja();
$xF			= new cFecha();
$jxc = new TinyAjax();

function jsaGetSumas($com1, $com2, $com3){
	$suma	= $com1 + $com2 + $com3;
	$iva	= round( ($suma * TASA_IVA), 2 );
	$tab = new TinyAjaxBehavior();
	$tab -> add(TabSetValue::getBehavior("idsuma", ($suma+$iva)));
	$tab -> add(TabSetValue::getBehavior("idiva", $iva));
	return $tab -> getString();	
	
}
function jsaGetComisionPorApertura($idcredito){
	$xCred	= new cCredito($idcredito);
	$xCred->init();
	$tasa	= $xCred->getOProductoDeCredito()->getTasaComisionApertura();
	return  round(($xCred->getMontoAutorizado() * $tasa), 2);
}
$jxc ->exportFunction('jsaGetSumas', array('idcom1', 'idcom2', 'idcom3'));
$jxc ->exportFunction('jsaGetComisionPorApertura', array('idsolicitud'), '#idcom1');

$jxc ->process();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();
$xFRM		= new cHForm("frmcomisiones", "cobro_de_comisiones.frm.php?action=" . MQL_ADD);
$msg		= "";
if($action == MQL_ADD){
	
	$xRec 					= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, false);
	$xRec->setGenerarPoliza();
	$xRec->setGenerarTesoreria();
	
	$detalles 		= parametro("idobservaciones", "");
	//$monto 			= parametro("idmonto", 0, MQL_FLOAT);
	$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
	$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
	$foliofiscal 	= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);
	$fecha			= parametro("idfechacomision", fechasys());
	$fecha			= $xF->getFechaISO($fecha);
	
	$com1			= parametro("idcom1", 0, MQL_FLOAT);
	$ob1			= parametro("idob1", "");
	
	$com2			= parametro("idcom2", 0, MQL_FLOAT);
	$ob2			= parametro("idob2", "");
		
	$com3			= parametro("idcom3", 0, MQL_FLOAT);
	$ob3			= parametro("idob3", "");
	
	$iva			= parametro("idiva", 0, MQL_FLOAT);
	
	
	$monto			= $com1 + $com2 + $com3 + $iva;
	$idrecibo			= $xRec->setNuevoRecibo($persona, $credito, $fecha, 1,RECIBOS_TIPO_OINGRESOS, $detalles, $cheque, $comopago, $foliofiscal);
	if(setNoMenorQueCero($idrecibo) > 0){
		
		if($com1 > 0){	$xRec->setNuevoMvto($fecha, $com1, OPERACION_CLAVE_COMISION_APERTURA, 1, $ob1, 1, TM_ABONO, $persona); }
		
		if($com2 > 0){	 $xRec->setNuevoMvto($fecha, $com2, OPERACION_CLAVE_PAGO_CBZA, 1, $ob2, 1, TM_ABONO, $persona); }
		
		if($com3 > 0){	 $xRec->setNuevoMvto($fecha, $com3, OPERACION_CLAVE_PAGO_COM_VARIAS, 1, $ob3, 1, TM_ABONO, $persona); }
		
		if($iva > 0){	 $xRec->setNuevoMvto($fecha, $iva, OPERACION_CLAVE_PAGO_IVA_OTROS, 1, $detalles, 1, TM_ABONO, $persona); }
		
		$xRec->addMvtoContableByTipoDePago($monto, TM_CARGO);
		
		if($xRec->setFinalizarRecibo(true) == true){
			$xFRM->setAction("");
			$xFRM->addHElem( $xRec->getFichaSocio() );
			$xFRM->addHElem( $xRec->getFicha(true) );
			$xFRM->OButton("TR.Imprimir Recibo", "jsImprimirRecibo()", "imprimir");
			$xFRM->addAvisoRegistroOK();
			$xFRM->addCerrar();
		
			echo $xRec->getJsPrint(true);	
		}
	} else {
		$xFRM->addAviso($xRec->getMessages());
	}
} else {
	
	
	
	$xFRM->addJsBasico();
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();

	$xSel		= new cHSelect();
	$xTxt		= new cHText(); $xTxt2		= new cHText();
	$xTxt3		= new cHText();
	$xTCob		= new cHCobros(); //"idtipodepago", "idtipodepago"
	$xFRM->ODate("idfechacomision", "", "TR.Fecha de Cobro");
	$xFRM->addCobroBasico();
	
	$xTxt2->setDivClass("");
	$xTxt->setDivClass("");
	$xTxt3->setDivClass("");
	$xTxt->addEvent("jsaGetSumas()", "onchange");	$xTxt->addEvent("jsaGetSumas()", "onblur"); 
	$xTxt3->addEvent("jsaGetSumas()", "onchange");	$xTxt3->addEvent("jsaGetSumas()", "onblur");
	
	$xTxt->addEvent("jsaGetComisionPorApertura()", "onfocus");
	//$xTxt->setClearEvents(false);
	$xFRM->addDivSolo($xTxt->getDeMoneda("idcom1", "TR.Comisiones por Apertura de credito"), $xTxt2->getDeObservaciones("idob1", "", "TR.Observaciones"), "tx24", "tx24");
	$xTxt->addEvent("jsaGetSumas()", "onchange");	$xTxt->addEvent("jsaGetSumas()", "onblur");
	$xFRM->addDivSolo($xTxt3->getDeMoneda("idcom2", "TR.Comisiones por Cobranza"), $xTxt2->getDeObservaciones("idob2", "", "TR.Observaciones"), "tx24", "tx24");
	$xTxt->addEvent("jsaGetSumas()", "onchange");	$xTxt->addEvent("jsaGetSumas()", "onblur");
	$xFRM->addDivSolo($xTxt3->getDeMoneda("idcom3", "TR.Comisiones varias"), $xTxt2->getDeObservaciones("idob3", "", "TR.Observaciones"), "tx24", "tx24");
	
	$xFRM->addDivSolo($xTxt->getDeMoneda("idiva", "TR.IVA"), " ", "tx24", "tx24");
	$xTxt->setProperty("disabled", "true");
	$xFRM->addDivSolo($xTxt->getDeMoneda("idsuma", "TR.Total"), " ", "tx24", "tx24");
	
}
echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>