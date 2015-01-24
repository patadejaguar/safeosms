<?php
/**
* @author Balam Gonzalez Luis Humberto
* @package creditos.formas
* @since 1.8 - 03/04/2008
* @since 2.0 - 09/09/2014
* @version 1.8.0
*  Archivo de Guardado de Ministraciones de Credito
* 		- 03/04/2008 -
* 		- 27/05/2008 - Se agrego el Reporte de Mandato
* @version 2.0.0
* 		- Reescritura total
* 		- Nuevas formas
* 		- nuevo recibos de abono a capital y comisiones
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
$xHP				= new cHPage("TR.Desembolso de Creditos");
$xBtn				= new cHButton();
$uPagare			= "";
$uContrato 			= "";
$msg				= "";
$recibo				= SYS_CERO;
/* ------------------------------ CONTROL DE USUARIOS  -----------------------------------------*/

//----------------------------------------------------------------------------------------------
$idsolicitud 		= parametro("idsolicitud", 0, MQL_INT);
$idsocio			= parametro("idsocio", 0, MQL_INT);
$cheque 			= parametro("idnumerocheque", DEFAULT_CHEQUE, MQL_INT);
$observaciones 		= parametro("idobservaciones");
$cuenta_cheques 	= parametro("idcodigodecuenta", DEFAULT_CUENTA_BANCARIA, MQL_INT);

$monto_cheque1		= parametro("idmontocheque", 0, MQL_FLOAT);
$cuenta_cheques2 	= DEFAULT_CUENTA_BANCARIA;
$cheque2 			= 0;

$recibo_fiscal 		= parametro("idfoliofiscal");
$fecha				= parametro("idfechaactual", false, MQL_DATE);
//descuentos		

$creditodescontado	= parametro("idcreditodescontado", 0, MQL_INT);
$montocreditodesc	= parametro("idmontocreditodescontado", 0, MQL_FLOAT);
$montocomision		= parametro("idmontocomisiondescontado", 0, MQL_FLOAT);

$xHP->init();
$xFRM			= new cHForm("frmacciones", "./");

if($idsolicitud <= 0 OR $monto_cheque1 <= 0){
	echo JS_CLOSE;
} else {
	$xCred			= new cCredito($idsolicitud, $idsocio);
	$xCred->init();
	if($xCred->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO OR $monto_cheque1 <= 0){

		
		$recibo		= $xCred->setMinistrar($recibo_fiscal, $cheque, $monto_cheque1, $cuenta_cheques,
				$cheque2, $cuenta_cheques2, $observaciones, $fecha );
		if(setNoMenorQueCero($recibo) > 0){
			$xFRM->addHTML( $xCred->getFichaDeSocio() );
			$xFRM->addHTML( $xCred->getFicha() );
			
			$xFRM->OButton("TR.RECIBO DE MINISTRACION", "jsImprimirReciboMinistracion()", $xFRM->ic()->REPORTE, "id4");
			
			$xFRM->addToolbar($xBtn->getIrAlInicio(true) );
			$uPagare 		= "elUrl='" . $xCred->getOProductoDeCredito()->getPathPagare($idsolicitud) . "';";
			$uContrato 		= "esUrl='" . $xCred->getPathDelContrato() . "';";
			
	
			if($creditodescontado >= DEFAULT_CREDITO){
				$xDCred		= new cCredito($creditodescontado); $xDCred->init();
				$saldodesc	= $xDCred->getSaldoActual($fecha);
				if($saldodesc < $montocreditodesc ){
					$montocreditodesc	= $saldodesc;
					$montocomision		= $montocomision + ($montocreditodesc - $saldodesc);
				}
				$idrecibocap			= $xDCred->setAbonoCapital($montocreditodesc, SYS_UNO, $cheque, TESORERIA_COBRO_DESCTO, $recibo_fiscal, $observaciones);
				if(setNoMenorQueCero($idrecibocap) > 0){
					$xRecCapt	= new cReciboDeOperacion(false, false, $idrecibocap); $xRecCapt->init();
					if($xRecCapt->setFinalizarRecibo(true) == true){
						$xFRM->OButton("TR.Recibo de Abono", "jsImprimirReciboCapital()", "imprimir");
						$xFRM->addHTML($xRecCapt->getJsPrint(true, "jsImprimirReciboCapital"));
						//finalizar tesoreria
						$xRecCapt->setFinalizarTesoreria(array(
							"cuenta" => $cuenta_cheques,
							"cheque" => $cheque
						));
					}
					$msg	.= ( MODO_DEBUG == true ) ? $xRecCapt->getMessages() : "";
				}
			}
			if($montocomision > 0){
				$xRec				= new cReciboDeOperacion();
				$idrecibo			= $xRec->setNuevoRecibo($idsocio, $idsolicitud, $fecha, 1, RECIBOS_TIPO_OINGRESOS, $observaciones, $cheque, TESORERIA_COBRO_DESCTO, $recibo_fiscal);
				if(setNoMenorQueCero($idrecibo) > 0){
					$montocomision	= round( ($montocomision * (1 / (1 + TASA_IVA))), 2);
					$montoivacomi	= round( ($montocomision	* TASA_IVA), 2);
					$xRec->setNuevoMvto($fecha, $montocomision, OPERACION_CLAVE_COMISION_APERTURA, 1, $observaciones, 1, TM_CARGO, $idsocio);
					$xRec->setNuevoMvto($fecha, $montoivacomi, OPERACION_CLAVE_PAGO_IVA_OTROS, 1, $observaciones, 1, TM_CARGO, $idsocio);
						
					//$xRec->addMvtoContableByTipoDePago($montocomision, TM_CARGO);
						
					if($xRec->setFinalizarRecibo(true) == true){
						$xFRM->OButton("TR.Recibo de Comisiones", "jsImprimirRecibo()", "imprimir");
						$xFRM->addHTML($xRec->getJsPrint(true));
						$xRec->setFinalizarTesoreria(array(
							"cuenta" => $cuenta_cheques,
							"cheque" => $cheque
						));
					}
					$msg	.= ( MODO_DEBUG == true ) ? $xRec->getMessages() : "";
				}
			}
			//Buttons
			$xFRM->OButton("TR.VER/IMPRIMIR PAGARE DE CREDITO", "printpagare();", "imprimir", 'id1');
			$xFRM->OButton("TR.VER/IMPRIMIR CONTRATO DE CREDITO", "contratocredito();", "imprimir", 'id2');
			$xFRM->OButton("TR.IMPRIMIR MANDATO", "printmandato()", "imprimir", "id3");			
		} else {
			$xFRM->addAvisoRegistroError();
		}
		if ( MODO_DEBUG == true ){
			$msg	.= $xCred->getMessages();
			
			$xFRM->addAviso($msg);
		}
	} else {
		//ESTADO NO APPLICABLE
		echo JS_CLOSE;
	}
}
echo $xFRM->get();
?>
</body>
<script  >
	function jsImprimirReciboMinistracion() {
		var elUrl= "../rpt_formatos/recibo_de_prestamo.rpt.php?recibo=<?php echo $recibo; ?>";
		rptrecibo = window.open(elUrl, "");
		rptrecibo.focus();
	}
	function printpagare() {
	<?php
		echo $uPagare;
	?>
		rptpagare = window.open( elUrl, "");
		rptpagare.focus();
	}
	function contratocredito() {
	<?php
		echo $uContrato;
	?>

		rptcontrato = window.open( esUrl, "");
		rptcontrato.focus();

	}
	function printmandato(){
		var elUrl= "../rpt_formatos/mandato_en_creditos.rpt.php?i=<?php echo $idsolicitud; ?>";
		rptrecibo = window.open(elUrl, "");
		rptrecibo.focus();
	}
</script>
</html>
