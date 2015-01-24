<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package caja.forms
 * 
 */
//=====================================================================================================
	include_once ("../core/go.login.inc.php");
	include_once ("../core/core.error.inc.php");
    include_once("../core/core.html.inc.php");
    include_once("../core/core.init.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		saveError(999, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Acceso no permitido a :" . addslashes(__FILE__));
		header ("location:404.php?i=999");
	} else {
        $_SESSION["current_file"]   = addslashes(__FILE__);
    }
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Cobro de Multas");
$idrecibo	= 0;
$xCaja		= new cCaja();
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);

//remote values
$msgs			= ( isset($_GET[SYS_MSG]) ) ? isset($_GET[SYS_MSG]) : "RELLENE LOS DATOS SOLICITADO Y GUARDELO";
$dMonto			= ( isset($_GET["mn"]) ) ? isset($_GET["mn"]) : 0;
$dSocio			= ( isset($_GET["s"]) ) ? isset($_GET["s"]) : DEFAULT_SOCIO;
$dCon			= ( isset($_GET["c"]) ) ? isset($_GET["c"]) : "";


if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	$xHP->goToPageError(200); }
$xHP->init();
$xFRM			= new cHForm("frmmultas", "frmcobrodemultas.php?action=" . MQL_ADD);
if($action == SYS_NINGUNO){
	$xFRM->addPersonaBasico();
	$xFRM->ODate("idfechaactual", false, "TR.Fecha de cobro");
	$xFRM->addCobroBasico();
	$xFRM->OText("idconceptomulta", "", "TR.Concepto de Multa");
	$xFRM->OMoneda("idmontomulta", 0, "TR.Monto de Multa", true);
	
	$xFRM->addGuardar();	
} else {
	$xT					= new cTipos();
	//===========================================================================
	$idsocio 			= ( isset($_POST["idsocio"]) ) ? $xT->cInt($_POST["idsocio"]) : 0;
	$observaciones 		= ( isset($_POST["idconceptomulta"]) ) ? $xT->cChar($_POST["idconceptomulta"]) : "";
	$monto 				= ( isset($_POST["idmontomulta"]) ) ? $xT->cFloat($_POST["idmontomulta"]) : 0;
	$cheque 			= ( isset($_POST["cheque"]) ) ? $xT->cChar($_POST["cheque"]) : DEFAULT_CHEQUE;
	$comopago 			= ( isset($_POST["ctipo_pago"]) ) ? $xT->cChar($_POST["ctipo_pago"]) : DEFAULT_TIPO_PAGO;
	$foliofiscal 		= ( isset($_POST["foliofiscal"]) ) ? $xT->cChar($_POST["foliofiscal"]) : DEFAULT_RECIBO_FISCAL;
			
	if ( setNoMenorQueCero($idsocio)<= 0) {
		//header("location: frmcobrodemultas.php?msg=FALTAN_DATOS");
		$xFRM->addAvisoRegistroError();
	} else {
		if ( setNoMenorQueCero($monto) <= 0) {
			$xFRM->addAvisoRegistroError();
		}	else {
			
			
			$iddocto	= DEFAULT_CREDITO;
		
			$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_TERCEROS, false, false);
			$xRec->setGenerarBancos();
			$xRec->setGenerarPoliza();
			$xRec->setGenerarTesoreria();
		
			$idrecibo 			= $xRec->setNuevoRecibo($idsocio, $iddocto, $fecha, 1, RECIBOS_TIPO_TERCEROS, $observaciones, $cheque, $comopago, $foliofiscal);
		
			$xRec->setNuevoMvto($fecha, $monto, OPERACION_CLAVE_MULTAS, 1, $observaciones, 1, TM_ABONO, $idsocio);
		
			$xRec->addMvtoContableByTipoDePago($monto, TM_CARGO);
		
			$xRec->setFinalizarRecibo(true);
			$xFRM->addHTML( $xRec->getFichaSocio() );
			$xFRM->addHTML( $xRec->getFicha() );
			$xFRM->addPrintRecibo();
			$xFRM->addHTML($xRec->getJsPrint(true));
			$xFRM->addAvisoRegistroOK();
			if(MODO_DEBUG == true){ $xFRM->addAviso($xRec->getMessages()); }
		}			
	}
}
echo  $xFRM->get();
	
$xHP->fin();
?>