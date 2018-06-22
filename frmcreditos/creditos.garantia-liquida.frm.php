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
$xHP		= new cHPage("TR.AGREGAR GTIALIQ", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xVal		= new cReglasDeValidacion();

//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$pagolibre		= parametro("pagolibre", false, MQL_BOOL);

$xHP->init();

$xFRM		= new cHForm("frm", "./creditos.garantia-liquida.frm.php");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());




if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		
		$xFRM->OHidden("credito", $xCred->getClaveDeCredito());
		$xFRM->OHidden("persona", $xCred->getClaveDePersona());
		
		$xFRM->addHElem( $xCred->getFichaMini() );
		
		if($action == SYS_NINGUNO){
			$xFRM->addGuardar();
			
			$xFRM->addSeccion("iddapo", "TR.DESTINO");
			
				if(GARANTIA_LIQUIDA_EN_CAPTACION == true){
					$xSelCta	= $xSel->getListaDeCuentasGtiaLiq("cuenta", false, $xCred->getClaveDePersona());
					$xSelCta->addEspOption("0", "NUEVO");
					$xFRM->addHElem($xSelCta->get(true));
				} else {
					$xFRM->OHidden("cuenta", "0");
				}
				
				if($pagolibre == true){
					if($monto<=0){
						$monto		= $xCred->getGarantiaLiquida();
					}
				} else {
					$monto		= $xCred->getGarantiaLiquidaPorPagar();
				}
				
				$xFRM->addCobroBasico();
				
				if($pagolibre == true){
					$xFRM->OMoneda2("monto", $monto, "TR.MONTO");
				} else {
					$xFRM->ODisabledM("monto", $monto, "TR.MONTO");
				}
				
				
				
				$xFRM->addObservaciones();
			
			$xFRM->endSeccion();
			
			$xFRM->setAction("./creditos.garantia-liquida.frm.php?action=" . MQL_ADD, true);
		} else {
			$foliofiscal	= parametro("foliofiscal");
			$ctipo_pago		= parametro("ctipo_pago");
			$cheque			= parametro("cheque");
			
			/*$cuenta	= parametro("cuenta");
			$idobservaciones	= parametro("idobservaciones");
			$credito	= parametro("credito");
			$persona	= parametro("persona");
			$monto	= parametro("monto");*/
			
			if(GARANTIA_LIQUIDA_EN_CAPTACION == true){
				if($xVal->cuenta($cuenta) == false){
					
					$xCapt	= new cCuentaALaVista(false);
					$res	= $xCapt->initCuentaPorProducto($xCred->getClaveDePersona(), CAPTACION_PRODUCTO_GARANTIALIQ);
					if($res == false){
						$xCapt->setNuevaCuenta($xCapt->ORIGEN_CRED, CAPTACION_PRODUCTO_GARANTIALIQ, $xCred->getClaveDePersona(), $observaciones, $xCred->getClaveDeCredito() );
					}
					$cuenta	= $xCapt->getNumeroDeCuenta();
				}
				
				$xCta		= new cCuentaALaVista($cuenta);
				if($xCta->init() == true){
					$recibo	 = $xCta->setDeposito($monto, $cheque, $ctipo_pago, $foliofiscal, $observaciones);
					$xRec		= new cReciboDeOperacion(false, false, $recibo);
					if($xRec->init() == true){
						$xFRM->addImprimir();
						$xFRM->addJsCode( $xRec->getJsPrint() );
						$xFRM->addHElem( $xRec->getFicha() );
					}
				} else {
					$xFRM->addAvisoRegistroError($xCta->getMessages(OUT_HTML));
				}
				
			} else {
				//
				$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO);
				$idrecibo	= $xRec->setNuevoRecibo($xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), $fecha, SYS_CERO, RECIBOS_TIPO_PAGO_CREDITO, $observaciones, $cheque, $ctipo_pago, $foliofiscal, $xCred->getClaveDeGrupo());
				if($idrecibo>0){
					$xRec->addMovimiento(OPERACION_CLAVE_PAGO_GLIQ, $monto, SYS_CERO, $observaciones, SYS_UNO,$xCred->getClaveDePersona(), $xCred->getClaveDeCredito());
					$xRec->setFinalizarRecibo(true);
					
					$xFRM->addImprimir();
					$xFRM->addJsCode( $xRec->getJsPrint() );
					$xFRM->addHElem( $xRec->getFicha() );
					
				}else {
					$xFRM->addAvisoRegistroError($xRec->getMessages(OUT_HTML));
				}
			}
			$xFRM->addCerrar();
		}
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>