<?php
/**
*	
*	29Mayo08 - Se Agrego la Actualizacion de Estatus Socio y fecha de Alta.
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
$xHP		= new cHPage("TR.capital_social");
$jxc 		= new TinyAjax();
$xCaja		= new cCaja();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);


if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }


	function jsSetCuotas($socio, $form){
		//selecciona el tipo de ingreso
		$SQLSP = "SELECT
	`socios_general`.`codigo`,
	`socios_tipoingreso`.`parte_social`,
	`socios_tipoingreso`.`parte_permanente`
FROM
	`socios_general` `socios_general`
		INNER JOIN `socios_tipoingreso` `socios_tipoingreso`
		ON `socios_general`.`tipoingreso` =
		`socios_tipoingreso`.`idsocios_tipoingreso`
WHERE
	(`socios_general`.`codigo` =$socio)";
		$dSocio = obten_filas($SQLSP);
		$social		= $dSocio["parte_social"];
			if(!$social) {
				$social = 0;
			}
		$permanente	= $dSocio["parte_permanente"];
			if(!$permanente){
				$permanente = 0;
			}
		$total = $social + $permanente;
		$tab = new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("idpartesocial", $social));
		$tab -> add(TabSetValue::getBehavior("idpartepermanente", $permanente));
		$tab -> add(TabSetValue::getBehavior("idtotalcuotas", $total));
		return $tab -> getString();
	}

$jxc ->exportFunction('jsSetCuotas', array('idsocio', "frmpartessociales"));
$jxc ->process();
$xF			= new cFecha();

$xFRM		= new cHForm("frmcuotassociales", "frmpartessociales.php?action=next");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();

$xSel		= new cHSelect();

$xTxt->setDivClass("");

$xFRM->addJsBasico(iDE_OPERACION);

echo $xHP->getHeader(true);

?>
<script  >
function jsSetTotalCuotas(){
	var mTotal = parseFloat(document.getElementById("idpartesocial").value) + parseFloat(document.getElementById("idpartepermanente").value);
		mTotal = jsRoundPesos(mTotal);
		document.getElementById("idtotalcuotas").value = mTotal;
}
</script>
<?php
	$com		= parametro("action", SYS_NINGUNO);
	$persona	= parametro("idsocio", DEFAULT_SOCIO, MQL_INT);
	$monto1		= parametro("monto1", 0, MQL_FLOAT);
	$monto2		= parametro("monto2", 0, MQL_FLOAT);
	$monto3		= parametro("monto3", 0, MQL_FLOAT);
	
	$tipo1		= parametro("tipo1", false, MQL_INT);
	$tipo2		= parametro("tipo2", false, MQL_INT);
	$tipo3		= parametro("tipo3", false, MQL_INT);
	
	if ( $com == SYS_NINGUNO OR (($monto1 + $monto2 + $monto3) <= 0) ) {
		
		$xFRM->addPersonaBasico();
		
		//$xFRM->addHElem($xDate->get("TR.Fecha de Operacion"));
		$xFRM->addFechaRecibo($fecha);
		$xFRM->addCobroBasico();
		$xFRM->addObservaciones();
		$xFRM->addDivSolo($xSel->getListaDeOperacionesPorBase(2800, "tipo1")->get(), $xTxt->getDeMoneda("monto1"));
		$xFRM->addDivSolo($xSel->getListaDeOperacionesPorBase(2800, "tipo2")->get(), $xTxt->getDeMoneda("monto2"));
		$xFRM->addDivSolo($xSel->getListaDeOperacionesPorBase(2800, "tipo3")->get(), $xTxt->getDeMoneda("monto3"));
		
		$xFRM->addSubmit();
		
		

	} else {

		$observaciones		= parametro("idobservaciones");
		$cheque 			= parametro("cheque");
		$comopago			= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
		$foliofiscal		= parametro("foliofiscal");
		
		
		
		$aportaciones 		= $monto1 + $monto2 + $monto3;
	
		$cRec 				= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_APORTACIONES, false);
		/*
		 * TODO: Agregar enviar PP a Ahorro
		 * activar configuracion
		 * activar cuenta por defecto
		 * verificar cuenta
		 * agregar cuenta
		 * agregar deposito
		 */
		//$cRec->setGenerarBancos();
		$cRec->setGenerarPoliza();
		$cRec->setGenerarTesoreria();
		$sucess			= true;
		$msg			= "";
		
		if(CAPITAL_SOCIAL_EN_CAPTACION == true){
			$xSoc		= new cSocio($persona);
			$cuenta 	= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_CAPITALSOCIAL);
			$xCta	= new cCuentaALaVista($cuenta);
			if($cuenta == 0){
				//Aperturar nueva cuenta
				$cuenta	= $xCta->setNuevaCuenta(DEFAULT_CAPTACION_ORIGEN, CAPTACION_PRODUCTO_CAPITALSOCIAL, $persona);
				$xCta->set($cuenta);
			}
			if( $xCta->init() == true){
				$idrecibo	= $cRec->setNuevoRecibo($persona, $xCta->getNumeroDeCuenta(), $fecha, 1, RECIBOS_TIPO_DEPOSITO_VISTA, $observaciones, $cheque, $comopago, $foliofiscal);
				$cRec->init();
				if($tipo1 != false AND $monto1 > 0){ $xCta->setDeposito($monto1, $cheque, $comopago, $foliofiscal, "Origen $tipo1:" . $observaciones, DEFAULT_GRUPO, $fecha, $cRec->getCodigoDeRecibo());	}
				
				if($tipo2 != false AND $monto2 > 0){ $xCta->setDeposito($monto2, $cheque, $comopago, $foliofiscal, "Origen $tipo2:" . $observaciones, DEFAULT_GRUPO, $fecha, $cRec->getCodigoDeRecibo());	}
				
				if($tipo3 != false AND $monto3 > 0){ $xCta->setDeposito($monto2, $cheque, $comopago, $foliofiscal, "Origen $tipo2:" . $observaciones, DEFAULT_GRUPO, $fecha, $cRec->getCodigoDeRecibo());	}
			} else {
				$msg			.= "ERROR\tError en la carga de la cuentar $cuenta\r\n";
				$sucess			= false;
			}
			if(MODO_DEBUG == true){ $msg			.= $xCta->getMessages();		}	
		} else {
			$idrecibo	= $cRec->setNuevoRecibo($persona, DEFAULT_CREDITO, $fecha, 1, 5, $observaciones, $cheque, $comopago, $foliofiscal);

			if($tipo1 != false AND $monto1 > 0){ $cRec->setNuevoMvto($fecha, $monto1, $tipo1, 1, $observaciones, 1, TM_ABONO, $persona);	}
			
			if($tipo2 != false AND $monto2 > 0){ $cRec->setNuevoMvto($fecha, $monto2, $tipo2, 1, $observaciones, 1, TM_ABONO, $persona);	}
			
			if($tipo3 != false AND $monto3 > 0){ $cRec->setNuevoMvto($fecha, $monto3, $tipo3, 1, $observaciones, 1, TM_ABONO, $persona);	}
		}
		if(MODO_DEBUG == true){ $msg			.= $cRec->getMessages();		}
			
		if($sucess == true){
			$cRec->setFinalizarRecibo(true);
			$xFRM->addHTML( $cRec->getOPersona()->getFicha() );
			$xFRM->addHTML( $cRec->getFicha() );
			$xFRM->addPrintRecibo();
			$xFRM->addAvisoRegistroOK();
			echo $cRec->getJsPrint(true);
		} else {
			$xFRM->addAviso($msg);
		}
	}
	echo $xFRM->get();
	$jxc ->drawJavaScript(false, true);
	
	?>
</body>
</html>
