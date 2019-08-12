<?php
//=====================================================================================================
	include_once ("../core/go.login.inc.php");
	include_once ("../core/core.error.inc.php");

	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		saveError(999, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Acceso no permitido a :" . addslashes(__FILE__));
		header ("location:404.php?i=999");
	} else {
        $_SESSION["current_file"]   = addslashes(__FILE__);
    }
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
    @require_once("../libs/jsrsServer.inc.php");
	include_once("../core/core.deprecated.inc.php");
	include_once("../core/entidad.datos.php");
	include_once("../core/core.fechas.inc.php");
	include_once("../core/core.config.inc.php");
	include_once("../core/core.common.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.captacion.inc.php");
	include_once("../core/core.creditos.inc.php");
    

jsrsDispatch("Common_82cbe75762e2714baaf92926f0d26d6b, Common_82d8036ee2608f6745d1dbd2f808866f, Common_bcab7cadc036c4d68cd83705a5e015c4, Common_c8581154ac4e27cb0e122d71341dd7d8");
	/**
	 * Funcion que Muestra la descripcion de la Cuenta
	 * @param string $params
	 */
	function Common_82cbe75762e2714baaf92926f0d26d6b($params){
		$datos 		= explode("|", $params);
		$xT			= new cTipos(0);
		$idcuenta 	= (isset($datos[0]) ) ? $xT->cInt($datos[0]) : 0;
		$txt		= "";
		if($idcuenta > 0){
			$xC		= new cCuentaDeCaptacion($idcuenta);
			$xC->init();
			$txt	=$xC->getDescription();
		}
		return $txt;
	}
	/**
	 * Funcion que Retorna la Cuenta Primaria
	 * @param $idchar
	 */
	function Common_82d8036ee2608f6745d1dbd2f808866f($idchar){
		$xT			= new cTipos(0);
		$datos		= explode("|", $idchar);
		$idsocio 	= $xT->cInt($datos[0]);
		$tipo 		= $xT->cInt($datos[1]);
		$sub		= $xT->cInt($datos[2]);
		if ( $datos[2] == "all" ){
			$sub		= false;
		}
		if ( $idsocio != 0 AND $tipo != 0  ){
			$cSoc		= new cSocio($idsocio);
			
			return $cSoc->getCuentaDeCaptacionPrimaria($tipo, $sub);
		}
	}
	function Common_bcab7cadc036c4d68cd83705a5e015c4($tipo){
		//
		$xTs		= new cInformacionProductoCaptacion($tipo);
		$D			= $xTs->init();
		//return ( $xC->getDestinoDelInteres() == "CUENTA_INTERESES" ) ? true : false ;
		return $D["destino_del_interes"];
	}
	function Common_c8581154ac4e27cb0e122d71341dd7d8($strCmd){
		$DPar 				= explode(STD_LITERAL_DIVISOR, $strCmd);


	 	$xTip				= new cTipos();
	 	//
	 	$socio				= $xTip->cInt($DPar[0]);
	 	$cuenta				= $xTip->cInt($DPar[1]);
		$deposito			= $xTip->cFloat($DPar[2]);
		$retiro				= $xTip->cFloat($DPar[3]);
		$nota				= $xTip->cChar($DPar[4]);
		$numero				= $xTip->cInt($DPar[5]);
		$limit				= $xTip->cInt($DPar[6]);
		if ( isset($_SESSION["recibo_en_proceso"]) ){
			$recibo 		= $_SESSION["recibo_en_proceso"];
			$fecha			= fechasys();
			$_SESSION["total_recibo_en_proceso"] += ($deposito - $retiro);
	
			$xRec 			= new cReciboDeOperacion(200, false, $recibo);
			$xRec->setNumeroDeRecibo($recibo, true);
			$DRec			= $xRec->getDatosInArray();
			$cheque			= $DRec["cheque_afectador"];
			$tipopago		= $DRec["tipo_pago"];
			$recibofiscal	= $DRec["recibo_fiscal"];
			
			if ($deposito > 0 OR $retiro > 0){
				$xC 		= new cCuentaALaVista($cuenta);
				$grupo		= DEFAULT_GRUPO;
				
				$xC->setSocioTitular($socio);
				$xC->setReciboDeOperacion($recibo);
				if( $deposito > 0){
					$xC->setDeposito($deposito, $cheque, $tipopago, $recibofiscal, $nota, $grupo, $fecha, $recibo );
				}
				if( $retiro > 0){
					$xC->setRetiro($retiro, $cheque, $tipopago, $recibofiscal, $nota, $grupo, $fecha, $recibo );
				}
			}
			if ($numero == $limit ){
				$xRec->setForceUpdateSaldos();
				$xRec->setFinalizarRecibo(true);
				//$MsgEnd		.= "**** proceso terminado ****";
			}
		}
		//retorna el id del control de origen para neutralizar
		return "-$numero";		
	}
?>