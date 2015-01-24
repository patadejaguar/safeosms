<?php
/**
 * @author Balam Gonzalez Luis H
 * @package otros
 * @subpackage operaciones
 * @since 2007-02-01
 * @version 1.02 2006-07-07
 * Modificaciones
 * 		2008-07-15 Reescritura
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
$xHP		= new cHPage("TR.Cuotas del fondo_de_defuncion");
$xCaja		= new cCaja();
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("idmonto", 0, MQL_FLOAT);

$xTip		= new cTipos();





function jsaGetCuotasDeDefuncion($socio){
	$cuota_pagada 		= 0;
	$fecha_a_tolerar	= restardias(fechasys(), DIAS_A_ROTAR_FONDO_DE_DEFUNCION);
	$cuota_a_pagar		= 100;
		$sql = "SELECT SUM(afectacion_real) AS 'monto',
			MAX(fecha_operacion) AS 'fecha_pago'
			FROM operaciones_mvtos
			WHERE tipo_operacion=902
			AND socio_afectado=$socio
			AND fecha_operacion>='$fecha_a_tolerar'
		";

		$cuota_pagada = mifila($sql, "monto");
		if ($cuota_pagada < 0) {
			$cuota_pagada = 0;
		}
		$cuota_a_pagar	= $cuota_a_pagar - $cuota_pagada;
		//TODO: Terminar
		$tab = new TinyAjaxBehavior();
		$tab -> add(TabSetvalue::getBehavior('idmonto', $cuota_a_pagar));
	return $tab -> getString();
}
function jsaGetNumeroBeneficiarios($idsocio) {
	$xSoc = new cSocio($idsocio);
	$xSoc->init();
	$beneficiarios = $xSoc->getNumeroDeBeneficiarios();
	if ( !isset($beneficiarios) OR $beneficiarios == 0 ){
		return "<p class='warn'>El socio no tiene Beneficiario Registrado</p>";
	} else {
		return "El socio tiene $beneficiarios Beneficiarios Registrado";
	}
}
$jxc = new TinyAjax();
$jxc ->exportFunction('jsaGetCuotasDeDefuncion', array('idsocio'));
$jxc ->exportFunction('jsaGetNumeroBeneficiarios', array('idsocio'), "#idmsgs");
$jxc ->process();
$xHP->init();
$xFRM		= new cHForm("frmfondo", "frmfondodefuncion.php");
$msg		= "";

if ( setNoMenorQueCero($monto) > 0  ){

	$xT 				= new cTipos();
	$Fecha				= parametro("idfechadepago", false);
	$Fecha				= ($Fecha == false) ? fechasys() : $xF->getFechaISO($Fecha);
	
	$observacion		= parametro("idobservaciones", "");
	$monto 				= parametro("idmonto", 0, MQL_FLOAT);
	$cheque 			= parametro("cheque", DEFAULT_CHEQUE);
	$comopago 			= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
	$foliofiscal 		= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);
	
	$fecha_de_operacion	= $Fecha;
		
	$xRec		= new cReciboDeOperacion(20);
	//$xRec->setGenerarBancos();
	//$xRec->setGenerarPoliza();
	//$xRec->setGenerarTesoreria();

	$idrecibo	= $xRec->setNuevoRecibo($persona, 1, $fecha_de_operacion, 1, 20,
			$observacion, $cheque, $comopago, $foliofiscal, DEFAULT_GRUPO);
	$xRec->setNuevoMvto($fecha_de_operacion, $monto, 902, 1, $observacion, 1, TM_ABONO);

	//agregar Poliza
	$xRec->addMvtoContableByTipoDePago();
	//Finalizar recibo
	$xRec->setFinalizarRecibo(true);


	$xRec->init();
	$xFRM->addHTML( $xRec->getFichaSocio() );
	$xFRM->addHTML( $xRec->getFicha(true) );
	$xFRM->addHTML( $xRec->getJsPrint(true) );
	$xFRM->OButton("TR. Imprimir recibo", "jsImprimirRecibo()", "imprimir", "idrec-dep");
	$xFRM->addCerrar();
	if (MODO_DEBUG == true){
		$msg		.= $xRec->getMessages();
		$xFL		= new cFileLog(false, true);
		$xFL->setWrite($msg);
		$xFL->setClose();
		$xFRM->addToolbar( $xFL->getLinkDownload("TR.Archivo de sucesos", ""));
	}	
} else {
	$xFRM->addJsBasico();
	$xFRM->addPersonaBasico();
	$xFRM->addCobroBasico();
	$xFRM->ODate("idfechadepago", "", "TR.Fecha de Pago");
	$xFRM->OMoneda("idmonto", 0, "TR.Cuota", true);
	$xFRM->addCerrar();
	$xFRM->addGuardar();
}
echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>