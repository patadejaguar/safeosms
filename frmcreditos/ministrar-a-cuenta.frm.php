<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.Abonar Credito a Cuenta", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "ministrar-a-cuenta.frm.php?action=" . MQL_ADD);

if($action == MQL_ADD){
	if($cuenta > DEFAULT_CUENTA_CORRIENTE AND $credito > DEFAULT_CREDITO){
		$xRec	= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, true);
		
		$xCred	= new cCredito($credito);
		if($xCred->init() == true){
			
			$persona	= $xCred->getClaveDePersona();
			$xRec->setNuevoRecibo($persona, $credito, $fecha, 0, RECIBOS_TIPO_MINISTRACION);
			//Verificar Plan de Pagos
			$plan		= 0;
			$valido		= $xCred->setVerificarValidez(false);
			if($valido == true){
			//Ministrar
				$xCred->setForceMinistracion();
				$recibo		= $xCred->setMinistrar(DEFAULT_RECIBO_FISCAL, DEFAULT_CHEQUE, $xCred->getMontoAutorizado(), DEFAULT_CUENTA_BANCARIA,0,0, " ", $fecha, $xRec->getCodigoDeRecibo(), TESORERIA_PAGO_NINGUNO );
				if($recibo > 0){
					$xCta	= new cCuentaALaVista($cuenta);
					$xCta->setDeposito($xCred->getMontoAutorizado(), DEFAULT_CHEQUE, TESORERIA_PAGO_NINGUNO, $xRec->getCodigoDeRecibo(), "Credito $credito");
				}
				$xFRM->addAviso($xCred->getMessages());
				$xFRM->addAviso($xCta->getMessages());
			} else {
				$xFRM->addAviso($xCred->getMessages());
			}
		}
		
		//if($xCta->set)
	}
} else {
$valido		= true;
	if($credito > DEFAULT_CREDITO){
		$xFRM->OHidden("idsolicitud", $credito);
		$xCred			= new cCredito($credito);
		if($xCred->init() == true){
			$xFRM->addHElem( $xCred->getFicha(true, "", false, true)  );
			$persona	= $xCred->getClaveDePersona();
			if($xCred->setVerificarValidez(false) == false){
				$xFRM->addAviso($xCred->getMessages());
				$valido		= false;
				$xFRM->addAvisoRegistroError("TR.Credito Incompleto");
			}
		}
	} else {
		$xFRM->addCreditBasico();
	}
	//iniciar la persona
	if($persona > DEFAULT_SOCIO){
		$xSoc			= new cSocio($persona);
		if($xSoc->init() == true){
			if($cuenta <= DEFAULT_CUENTA_CORRIENTE){
				$cuenta	= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_ORDINARIO);
				//$xFRM->addAviso("CUENTA $cuenta");
			}
			if($cuenta <= DEFAULT_CUENTA_CORRIENTE){
				$xCta	= new cCuentaALaVista(false);
				$cuenta = $xCta->setNuevaCuenta(DEFAULT_CAPTACION_ORIGEN, CAPTACION_PRODUCTO_ORDINARIO, $persona, "", $credito);
				$xFRM->addAviso($xCta->getMessages());			
			}
		}
	}
	if($cuenta > DEFAULT_CUENTA_CORRIENTE){
		$xCta	= new cCuentaALaVista($cuenta);
		if($xCta->init() == true){
			$xFRM->addHElem( $xCta->getFicha() );
		}
		$xFRM->OHidden("idcuenta", $cuenta);
	} else {
		$xFRM->addCuentaCaptacionBasico(false, CAPTACION_TIPO_VISTA, 0, $cuenta);
	}

	if($valido == true){
		$xFRM->addGuardar();
	} else {
		$xFRM->addCerrar();
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>