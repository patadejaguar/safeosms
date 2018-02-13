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
$xHP			= new cHPage("TR.Depositos a_la_vista");
$xF				= new cFecha();
$jxc 			= new TinyAjax();
$iddocto 		= parametro("idcuenta", 0, MQL_INT);
$recibo			= parametro("idrecibo", 0, MQL_INT);
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);

$actload 		= "";
$ReciboIDE		= 0;
$urlRec			= "";
$msg			= "";
$action			= parametro("action", SYS_NINGUNO);
$xCaja			= new cCaja();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }
if ($iddocto > 0){ $actload = " onload='jsImprimirRecibo();' ";	}
	//

function jsaGetIDE($socio, $monto, $tipo_de_pago){

	$xSoc 		= new cSocio($socio);
	$xSoc->init();
	$x 			= $xSoc->getIDExPagarByPeriodo(false, $monto, $tipo_de_pago);

		$tab = new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("idide", $x));
		return $tab -> getString();
}
$jxc ->exportFunction('jsaGetIDE', array('idsocio', "idmonto",  "idtipo_pago"));

$jxc ->process();	

echo $xHP->getHeader();


$xFRM		= new cHForm("depositoahorro", "frmdepositoahorro.php?action=next");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

?>
<body <?php echo $actload; ?> >
<?php 
$xFRM->setTitle($xHP->getTitle());

if ($action == SYS_NINGUNO) {
$xFRM->addCuentaCaptacionBasico();
$xFRM->addSubmit();

$xFRM->addFechaRecibo();
$xFRM->addCobroBasico();
//$xFRM->addHElem($xDate->get("TR.Fecha"));
$xFRM->addHElem( $xTxt->getDeMoneda("idmonto", "TR.DEPOSITO_CAPTACION", 0, true) );


$xFRM->addObservaciones();

$xFRM->addDataTag("role", iDE_CVISTA);
//$xFRM->addJsBasico(iDE_CAPTACION);
$jxc ->drawJavaScript(false, true);
} else {
		$xT 			= new cTipos();
		
		$idsocio 		= parametro("idsocio", false, MQL_INT);//$xT->cInt($_POST["idsocio"]);
		$detalles 		= parametro("idobservaciones", "");
		$monto 			= parametro("idmonto", 0, MQL_FLOAT);
		$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
		$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
		$folio_fiscal 	= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);
		
		$fecha_de_operacion	= $fecha;
		
		//$xSoc			= new cSocio($idsocio);
		
		if ( setNoMenorQueCero($monto) <= 0) {
			$msg		.= "ERROR\tEL MONTO DEBE SER MAYOR A CERO\r\n";
		} else {
	
			
	
			$xCta		= new cCuentaALaVista($iddocto, $idsocio);
			if($xCta->init() == true){
				$ide		= $xCta->getMontoIDE($fecha_de_operacion, $monto, $comopago);
				$recibo		= $xCta->setDeposito($monto, $cheque, $comopago, $folio_fiscal, $detalles, DEFAULT_GRUPO, $fecha_de_operacion);
				
				if ( $ide > 0 ){
						$saldo				= $xCta->getNuevoSaldo();
						$ide_observacion	= "Retencion Generada por un Deposito de $monto, Recibo $recibo, saldo de $saldo";
						//Si el Saldo de la Cuenta es Mayor al IDE
						if ( ($saldo > $ide) ){
							$ReciboIDE 		= $xCta->setRetenerIDE($fecha_de_operacion, false, $ide, $ide_observacion);
						} else {
						//Si no el IDE es igual al Saldo
							$ide 			= $saldo;
							$ReciboIDE 		= $xCta->setRetenerIDE($fecha_de_operacion, false, $ide, $ide_observacion);
						}
				}
				//Imprime la Ficha del socio
				$xRec		= new cReciboDeOperacion();
				$xRec->setNumeroDeRecibo($recibo, true);
				$xFRM->addHTML( $xRec->getFichaSocio() );
				$xFRM->addHTML( $xRec->getFicha(true) );
				
				// *****************************************************************************
				if(CAPTACION_IMPUESTOS_A_DEPOSITOS_ACTIVO == true){
					if ($ReciboIDE != 0 ){
						$xFRM->addToolbar( $xBtn->getBasic("TR. Imprimir recibo de impuestos", "jsPrintIDE()", "imprimir", "idrec-ide", false) );
					}
				}
				//echo "<input type='button' name='btsend' value='IMPRIMIR/VER RECIBO DE DEPOSITO' onClick='jsImprimirRecibo();'>";
				$xFRM->addToolbar( $xBtn->getBasic("TR. Imprimir recibo de deposito", "jsImprimirRecibo()", "imprimir", "idrec-dep", false) );
				$xFRM->addToolbar( $xBtn->getIrAlInicio(true) );
				
				if (MODO_DEBUG == true){
					$msg 		.= $xCta->getMessages();
					$msg		.= $xRec->getMessages();
					$xFRM->addLog($msg);
				}
				$xFRM->addHTML( $xRec->getJsPrint(true) );
			} else {
				$msg	.= "ERROR\tLa cuenta no existe\r\n";
			}
		
			
		}
		$xFRM->addAviso($msg, "idmsg");
		
}
echo $xFRM->get();
?>
</body>
<script>
var xG	= new Gen();
	
 	function jsPrintIDE() {
		var elUrl= "../rpt_formatos/frmreciboretiro.php?recibo=<?php echo $ReciboIDE; ?>";
		xG.w({ url: elUrl });
	}	
</script>
</html>