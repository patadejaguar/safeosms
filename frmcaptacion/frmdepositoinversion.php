<?php
/**
 * Depositos a la Vista
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package captacion
 *  Forma para enviar datos de Depositos a la Vista
 * 		06Junio08 	- se Agrego Mejor presentacion
 *		07Julio08	- Se mejoro el Codigo de Recibo de Impresion
 *		07julio08	- Se agrego el soporte de Debug
 *
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
$xHP			= new cHPage("TR.Depositos cuentas_de_inversion");
$xF				= new cFecha();

$xT 			= new cTipos();
$iddocto 		= parametro("idcuenta", DEFAULT_CUENTA_CORRIENTE);
$recibo			= parametro("idrecibo");
$reciboIDE		= 0;
//
$actload 		= "";
$Fecha			= parametro("idfecha-0", false);
$Fecha			= ($Fecha == false) ? fechasys() : $xF->getFechaISO($Fecha);
		
$idsocio 		= parametro("idsocio", false, MQL_INT);//$xT->cInt($_POST["idsocio"]);
$detalles 		= parametro("idobservaciones", "");
$monto 			= parametro("idmonto", 0, MQL_FLOAT);
$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
$folio_fiscal 	= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);
$msg			= parametro(SYS_MSG);
$action			= parametro("action", SYS_NINGUNO);

$xCaja		= new cCaja();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }
if (isset($iddocto)){ $actload = " onload='jsImprimirRecibo();' "; 	}

$jxc = new TinyAjax();
function jsaGetIDE($socio, $monto, $tipo_de_pago){

	$xSoc 	= new cSocio($socio);
	$xSoc->init();
	$x 		= $xSoc->getIDExPagarByPeriodo(false, $monto, $tipo_de_pago);

		$tab = new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("idide", $x));
		return $tab -> getString();
}
$jxc ->exportFunction('jsaGetIDE', array('idsocio', "idmonto",  "idtipo_pago"));

$jxc ->process();	

echo $xHP->getHeader();


$xFRM		= new cHForm("depositoahorro", "frmdepositoinversion.php?action=next");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
	
?>
<body <?php echo $actload; ?> >
<?php 
$xFRM->setTitle($xHP->getTitle());

if ($action == SYS_NINGUNO) {
	$xFRM->addCuentaCaptacionBasico(true, CAPTACION_TIPO_PLAZO);
	$xFRM->addSubmit();
	$xFRM->addHElem( $xTxt->getDeMoneda("idmonto", "TR.deposito", 0, true) );
	$xFRM->addCobroBasico();
	$xFRM->addHElem($xDate->get("TR.Fecha"));
	$xFRM->addObservaciones();
	
	$xFRM->addJsBasico(iDE_CAPTACION, CAPTACION_TIPO_PLAZO);
	$jxc ->drawJavaScript(false, true);

} else {

	
		if ($monto <= 0) {
			$msg		.= "ERROR\tEL MONTO DEBE SER MAYOR A CERO\r\n";
		} else {
	

			$xCta			= new cCuentaInversionPlazoFijo($iddocto, $idsocio);
			$xCta->init();
			$ide			= $xCta->getMontoIDE($Fecha, $monto, $comopago);
			
			if( $xCta->getEsOperable($Fecha) == true ){
				$recibo		= $xCta->setDeposito($monto, $cheque, $comopago, $folio_fiscal, $detalles, DEFAULT_GRUPO, $Fecha);

				//si el IDE es mayor a cero
				if ( $ide > 0 ){
					$saldo				= $xCta->getNuevoSaldo();
					$ide_observacion	= "Retencion Generada por un Deposito de $monto, Recibo $recibo, saldo de $saldo";
					//Si el Saldo de la Cuenta es Mayor al IDE
					if ( ($saldo > $ide) ){
						$reciboIDE 		= $xCta->setRetenerIDE($Fecha, false, $ide, $ide_observacion);
					} else {
					//Si no el IDE es igual al Saldo
						$ide 			= $saldo;
						$reciboIDE 		= $xCta->setRetenerIDE($Fecha, false, $ide, $ide_observacion);
					}
				}
			//Imprime la Ficha del socio
			$xRec		= new cReciboDeOperacion();
			$xRec->setNumeroDeRecibo($recibo, true);
			$xFRM->addHTML( $xRec->getFichaSocio() );
			$xFRM->addHTML( $xRec->getFicha(true) );
			$xFRM->addHTML( $xRec->getJsPrint(true) );	
				if(CAPTACION_IMPUESTOS_A_DEPOSITOS_ACTIVO == true){
					if ($ReciboIDE != 0 ){
						$xFRM->addToolbar( $xBtn->getBasic("TR. Imprimir recibo de impuestos", "jsPrintIDE()", "imprimir", "idrec-ide", false) );
					}
				}			
				if (MODO_DEBUG == true){
					$msg 		.= $xCta->getMessages();
					$msg		.= $xRec->getMessages();
					$xFL		= new cFileLog(false, true);
					$xFL->setWrite($msg);
					$xFL->setClose();
					$xFRM->addToolbar( $xFL->getLinkDownload("TR.Archivo de sucesos", ""));
				
				
				}
				$xFRM->addToolbar( $xBtn->getBasic("TR. Imprimir recibo de deposito", "jsImprimirRecibo()", "imprimir", "idrec-dep", false) );
			} else {
				$msg		.= "ERROR\tLA CUENTA NO ES OPERATIVA EN LA FECHA $Fecha\r\n";
			}
			$xFRM->addToolbar( $xBtn->getIrAlInicio(true) );
		}
		$xFRM->addAviso($msg, "idmsg", true);
	}
	
	echo $xFRM->get();
?>
</body>
<script  >

 	function jsPrintIDE() {
		var elUrl= "../rpt_formatos/frmreciboretiro.php?recibo=<?php echo $reciboIDE; ?>";
		jsGenericWindow(elUrl);
	}	
</script>
</html>