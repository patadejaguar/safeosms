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
$xHP		= new cHPage("TR.Cobro de MEMBRESIA");
$idrecibo	= 0;
$xCaja		= new cCaja();
$xVal		= new cReglasDeValidacion();
$xLog		= new cCoreLog();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);

//remote values
$persona			= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$ctipo_pago			= parametro("ctipo_pago", SYS_NINGUNO, MQL_RAW);
$cheque				= parametro("cheque");
$foliofiscal		= parametro("foliofiscal");
$idobservaciones	= parametro("idobservaciones");
$idtotal			= parametro("idtotal", 0, MQL_FLOAT);
$idtipomembresia	= parametro("idtipomembresia", 0, MQL_INT);
$idnumerodemes		= parametro("idnumerodemes",0, MQL_INT);
$idnumerodemes		= parametro("mes",$idnumerodemes, MQL_INT);
//$idnumerodeanno		= parametro("idnumerodeanno", 0, MQL_INT);
$periodo 			= $idnumerodemes;
$periocidad			= CREDITO_TIPO_PERIOCIDAD_MENSUAL;

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	$xHP->goToPageError(200); }
$xHP->init();
$xFRM			= new cHForm("frmmem", "cobro_de_membresia.frm.php");
$xTxt			= new cHText();
$xSel			= new cHSelect();
$jsSum			= "";
$xFRM->setNoAcordion();
if($action == SYS_NINGUNO){
	if($persona > DEFAULT_SOCIO){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			
			$xFRM->OHidden("idsocio", $xSoc->getCodigo());
			$xFRM->OHidden("idtipomembresia", $xSoc->getTipoDeMembresia());
			$xFRM->addGuardar();
			
			$xFRM->addHElem($xSoc->getFicha(false, true, "", false));
			$xFRM->addHElem( $xSel->getListaDeMeses("", $idnumerodemes)->get(true) );
			
			$xFRM->addFechaRecibo($fecha);
			
			$xFRM->addCobroBasico();
			$xFRM->addObservaciones();
			
			
			$xMem	= new cPersonasMembresiasTipos($xSoc->getTipoDeMembresia());
			$arr	= $xMem->getListaDeCompromisos($periocidad, $periodo, $persona);
			$xTxt->setDivClass("txmon");
			$xTxt->addEvent("jsUpdateSumas()", "onchange");
			$total	= 0;
			$xFRM->addSeccion("didivca", "TR.Lista de CUOTAS");	
			foreach ($arr as $datos){
				$idtipo	= $datos[SYS_TIPO];
				$monto	= $datos[SYS_MONTO];
				$total += $monto;
				$xTO	= new cTipoDeOperacion($idtipo);
				
				$xTO->init();
				$xFRM->addDivSolo( $xTxt->getDeMoneda("idm-$idtipo", $xTO->getNombre(), $monto) );
				$jsSum	.= ($jsSum == "") ? "flotante(\$('#idm-$idtipo').val())" :"+flotante(\$('#idm-$idtipo').val())";				
			}
			$xFRM->addDivSolo( $xTxt->getDeMoneda("idtotal", "TR.TOTAL", $total) );
			$xFRM->endSeccion();
			$xFRM->setAction("cobro_de_membresia.frm.php?action=" . MQL_ADD);
		}
	} else {
		$xFRM->addPersonaBasico();
	}
	//$xFRM->ODate("idfechaactual", false, "TR.Fecha de cobro");
} else {
	$xFRM->addCerrar();
	if($action == MQL_ADD AND $persona > DEFAULT_SOCIO AND $idtotal > 0){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xMem	= new cPersonasMembresiasTipos($xSoc->getTipoDeMembresia());
			$arr	= $xMem->getListaDeCompromisos(false, false, $persona);
			$xRec	= new cReciboDeOperacion();
			$numero	= $xRec->setNuevoRecibo($persona, DEFAULT_CREDITO, $fecha, $periodo, RECIBOS_TIPO_PAGO_APORTACIONES, $idobservaciones, $cheque, $ctipo_pago,$foliofiscal);
			foreach ($arr as $datos){
				$idtipo	= $datos[SYS_TIPO];
				$monto	= parametro("idm-$idtipo", 0, MQL_FLOAT);
				if($numero > 0){
					//FOSOHOP ES AHORRO
					switch($idtipo){
						case OPERACION_CLAVE_PAGO_CAPTACION:
							$cuenta	= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_ORDINARIO);
							//if($xVal->cuenta($cuenta) == true){
								$xCta	= new cCuentaALaVista($cuenta, $persona);
								if($xCta->init() == false){
									//$xLog->add($xCta->getMessages(), $xLog->DEVELOPER);
									$cuenta 	= $xCta->setNuevaCuenta(DEFAULT_CAPTACION_ORIGEN, CAPTACION_PRODUCTO_ORDINARIO, $persona, $idobservaciones, DEFAULT_CREDITO);
									$xCta->set($cuenta, true);
								}
								$recibo_ahorro = $xCta->setDeposito($monto, $cheque, $ctipo_pago, $foliofiscal, $idobservaciones, false, $fecha, false, false, false, $periodo);
								$xLog->add($xCta->getMessages(), $xLog->DEVELOPER);
								//Agregar recibo de Ahorro a FORM
								$xARec	= new cReciboDeOperacion(false, false, $recibo_ahorro);
								if($xARec->init() == true){
									$xRec->setFinalizarRecibo(true);
									$xFRM->addImprimir("TR.RECIBO AHORRO", "jsImprimirRecibo2()");
									$xFRM->addHTML($xARec->getJsPrint(true, "jsImprimirRecibo2"));									
								}
							//}
							break;
						default:
							$xRec->addMovimiento($idtipo, $monto, $periodo, "", 1,$persona);

							break;
					}
					
				}
			}
			$xRec->setFinalizarRecibo(true);
			$xFRM->addImprimir("TR.RECIBO CUOTAS");
			$xFRM->addAvisoRegistroOK();
			$xFRM->addLog($xLog->getMessages());
			$xFRM->addHTML($xRec->getJsPrint(true));
			$xLog->add($xRec->getMessages(), $xLog->DEVELOPER);
			$xFRM->addJsInit("jsImprimirRecibo();jsImprimirRecibo2();");
			$jsSum	= "0";
		}
	}
	
}
echo  $xFRM->get();
?>
<script>
function jsUpdateSumas(){
	var mTotal = <?php echo $jsSum ?>;
	$("#idtotal").val(mTotal);
}
</script>
<?php
$xHP->fin();
?>