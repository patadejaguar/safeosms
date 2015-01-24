<?php
/**
 * Core Captacion File
 * @author Balam Gonzalez Luis Humberto
 * @package captacion
 * @subpackage core
 * @version 1.2.35
 */
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.contable.inc.php");
include_once("core.operaciones.inc.php");
include_once("core.tesoreria.inc.php");
include_once("core.common.inc.php");
include_once("core.html.inc.php");
include_once("core.security.inc.php");
include_once("core.fechas.inc.php");

include_once("core.aml.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");

include_once("core.captacion.utils.inc.php");

@include_once("../libs/sql.inc.php");
@include_once("../libs/Encoding.php");
//=====================================================================================================
//=====================================================================================================
function getISRByInversion($monto_invertido, $dias_invertidos){
	$impuesto				= 0;
	$salario_minimo_df 		= SALARIO_VIGENTE_DF;
	$exento 				= 5 * $salario_minimo_df * 365;
	$tarifa_diaria_isr		=  (TASA_ISR_POR_INTERESES /360);
	$tarifa_por_inversion	= $tarifa_diaria_isr * $dias_invertidos;
	$base_gravada			= $monto_invertido - $exento;
	if (setNoMenorQueCero( $base_gravada) > 0){
		$impuesto			= $base_gravada * $tarifa_por_inversion;
	}
	return $impuesto;
}
/**
 * Clase de Manejo de Cuentas a la Vista u Ordinarias
 * @author Balam Gonzalez Luis Humberto
 *
 */
class cCuentaALaVista extends cCuentaDeCaptacion {
	function setRetiro($monto, $cheque = DEFAULT_CHEQUE,
						$tipo_de_pago = "cheque", $recibo_fiscal = DEFAULT_RECIBO_FISCAL,
						$observaciones = "", $grupo = DEFAULT_GRUPO,
						$fecha = false, $recibo = false){
		$iduser		= getUsuarioActual();
		/**
		 * verifica el maximo Retirable
		 * Si forzar es VERDADERO, el Maximo retirable es igual al SALDO
		 */
		 if ( $this->mForceOperations == true){
		 	$maximo_ret			= $this->mSaldoActual;
		 	$this->mMessages 	.= "ADVERTENCIA\tLa Operacion sera FORZADA \r\n";
		 } else {
			$maximo_ret	= $this->getMaximoRetirable($fecha);
		 }

		if ( $monto > $maximo_ret) {
			$this->mMessages 	.= "ERROR\tEl Monto a Retirar($monto) es Mayor al Retirable($maximo_ret) \r\n";
			$monto	= 0;
			$recibo	= false;
		}
		if ($monto > 0){
			if ( ($this->mForceOperations != true) AND (in_array($this->mEstatusActual, $this->mEstatusNoOperativos) == true OR ($monto > $this->mSaldoActual) ) ){

				$this->mMessages 	.= "ERROR\tLa Cuenta no esta permitida para recibir Operacion, tiene estatus " . $this->mEstatusActual ." ";
				$this->mMessages 	.= "o su saldo(" .  $this->mSaldoActual . ") es Mayor al Monto a  retirar($monto)  \r\n";
				$this->mSucess		= false;
			} else {
				if ( setNoMenorQueCero($this->mPeriodoCuenta) == 0 ){ $this->mPeriodoCuenta	= 1; }
				if ( setNoMenorQueCero($this->mSocioTitular) <= DEFAULT_SOCIO){	$this->init();	}
				
				if ($fecha == false ){
					if ( isset($this->mFechaOperacion) AND ($this->mFechaOperacion != false)) {
						$fecha = $this->mFechaOperacion;
					} else {
						$fecha = fechasys();
					}
				}
				$fecha					= setFechaValida($fecha);
				$this->mFechaOperacion	= $fecha;

				$socio		= $this->mSocioTitular;

				$CRecibo = new cReciboDeOperacion(4, true);
				//Agregar recibo si no hay
				if ( setNoMenorQueCero($recibo) == 0 ){
					$recibo = $CRecibo->setNuevoRecibo($socio, $this->mNumeroCuenta,
											$this->mFechaOperacion, $this->mPeriodoCuenta, 4,
											$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
					if ( setNoMenorQueCero( $recibo ) == 0 ){
						$this->mMessages	.= "SUCESS\tSe Agrego Exitosamente el Recibo [$recibo] de la Cuenta " . $this->mNumeroCuenta . " \r\n";
						$this->mReciboDeOperacion	= $recibo;
						$this->mSucess		= true;
					} else {
						$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo [$recibo] de la Cuenta " . $this->mNumeroCuenta . " \r\n";
						$this->mSucess		= false;
					}
				}

				$this->mReciboDeOperacion	= $recibo;

				if ( setNoMenorQueCero($recibo) > 0 ){
				//Agregar el Movimiento
					//$CRecibo->setCuentaBancaria($this->mCuentaBancaria);
					$CRecibo->setNumeroDeRecibo($recibo);
					$CRecibo->setNuevoMvto($fecha, $monto, $this->mOperacionRetiro, $this->mPeriodoCuenta, $observaciones, -1, TM_CARGO, $socio,  $this->mNumeroCuenta);
					$CRecibo->setFinalizarRecibo(true);
					$CRecibo->setFinalizarTesoreria();
					$this->mNuevoSaldo	= $this->mSaldoAnterior - $monto;

					$this->mSucess	= true;
					///Actualizar el recibo
					$this->mReciboDeOperacion	= $recibo;
					//Actualizar la Cuenta
					$this->setUpdateSaldo();
				} else {
					$this->mMessages	.= "ERROR\tNo Existe Recibo con el cual trabajar($recibo) \r\n";
				}
				$this->mORec			= $CRecibo;
				$this->mMessages		.= $CRecibo->getMessages();
			}

			$this->addSDPM();
		} else {
			$this->mMessages 			.= "ERROR\tEl Monto a Retirar($monto) No es un Valor Valido \r\n";
		}
		return  $recibo;
	}
	function setDeposito($monto, $cheque = "NA",
						$tipo_de_pago = TESORERIA_COBRO_CHEQUE, $recibo_fiscal = DEFAULT_RECIBO_FISCAL,
						$observaciones = "", $grupo = DEFAULT_GRUPO,
						$fecha = false, $recibo = false){
		if ($monto > 0 ){
			if ( setNoMenorQueCero($this->mPeriodoCuenta) == 0 ){ $this->mPeriodoCuenta	= 1; }
			if ( setNoMenorQueCero($this->mSocioTitular) <= DEFAULT_SOCIO){	$this->init();	}			
			if ($grupo == DEFAULT_GRUPO ){ 	$grupo = $this->mGrupoAsociado;	}

				if ($fecha == false ){
					if ( isset($this->mFechaOperacion) AND ($this->mFechaOperacion != false)) {
						$fecha = $this->mFechaOperacion;
					} else {
						$fecha = fechasys();
					}
				}
				$fecha					= setFechaValida($fecha);
				$this->mFechaOperacion	= $fecha;

				$socio		= $this->mSocioTitular;

				$CRecibo = new cReciboDeOperacion(4, true);
				//Set a Mvto Contable

				if (setNoMenorQueCero($recibo) > 0 ){
					$CRecibo->setNumeroDeRecibo($recibo);
					if( $CRecibo->init() == true){
						$CRecibo->setGrupoAsociado($grupo);
						$CRecibo->setDocumento($this->mNumeroCuenta);
						$CRecibo->setSocio($this->mSocioTitular);
					} else {
						$recibo	= 0;
					}
				}

				//Agregar recibo si no hay
				if ( setNoMenorQueCero($recibo) == 0 ){
					$recibo = $CRecibo->setNuevoRecibo($socio, $this->mNumeroCuenta,
											$this->mFechaOperacion, $this->mPeriodoCuenta, 3,
											$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
					//Checar si se agrego el recibo
					if ($recibo != false ){
						$this->mMessages	.= "OK\tSe Agrego Exitosamente el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " \r\n";
						$this->mReciboDeOperacion	= $recibo;
						$this->mSucess		= true;
					} else {
						$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " \r\n";
						$this->mSucess		= false;
					}
				}
				$this->mReciboDeOperacion	= $recibo;

				if ( setNoMenorQueCero($recibo) > 0 ){
				//Agregar el Movimiento
					$CRecibo->setNuevoMvto($fecha, $monto, $this->mOperacionDeposito, $this->mPeriodoCuenta, $observaciones, 1, TM_ABONO, $socio,  $this->mNumeroCuenta);
					$CRecibo->setFinalizarRecibo(true);
					$CRecibo->setFinalizarTesoreria();
					$this->mNuevoSaldo	= $this->mSaldoAnterior + $monto;

					$this->mMessages	.= $CRecibo->getMessages();
					$this->mSucess		= true;
					$this->mMessages	.= "OK\tSaldo Nuevo por " . $this->mNuevoSaldo .  "(" . $this->mSaldoAnterior . "|$monto) \r\n";
					//Actualizar la Cuenta
					$this->setUpdateSaldo();
				} else {
					$this->mMessages	.= "ERROR\tNo Existe Recibo con el cual trabajar($recibo) \r\n";
				}
				$this->mMessages	.= $CRecibo->getMessages();
				$this->addSDPM();
		}
		return  $recibo;
	}
	/**
	 * Funcion que genera un SDPM segun los Movimientos que se dan
	 * @param	boolean	$actualizar	Indica si actualiza el SDPM  o es un nuevo registro
	 * @param	float	$monto		Monto del SDPM(opcional)
	 */
	function addSDPM($actualizar = false, $monto = 0){
		if ($this->mSucess	!= false){
			$nuevatasa		= $this->getTasaAplicable(0, $monto );
			$fecha			= $this->mFechaOperacion;
			$fechaAnterior	= $this->mFechaUltimaOp;
			$dias_del_mes	= date("t", strtotime($fecha) );
			$idrecibo		= $this->mReciboDeOperacion;
			//Valores iniciados a cero
			$diastrans		= 0;
			//2012-012-10
			$socio			= $this->mSocioTitular;
			$sucursal		= getSucursal();
			
			$diastrans	= restarfechas($fecha, $this->mFechaUltimaOp);
			if ( $diastrans > $dias_del_mes ){
				$diastrans	= $dias_del_mes;
			}

			if ($diastrans < 0){

				$this->mMessages	.= "Error al Procesar el Numero de Dias Transcurridos($diastrans), se lleva a 1 (UNO)\r\n";
				$diastrans 			= 1;
			}
			if ( $actualizar == true AND $monto > 0){
				$sdpd			= $monto * $diastrans;
			} else {
				$sdpd			= $this->mSaldoAnterior * $diastrans;
			}

				$ejer = date( "Y", strtotime($fecha) );
				$peri = date( "m", strtotime($fecha) );

				if ( $actualizar == true ){
					///* (ejercicio, periodo, cuenta, fecha, dias, tasa, monto, recibo) */
					$sqlUSPM = "UPDATE captacion_sdpm_historico
	    							SET monto = $sdpd
									WHERE ejercicio=$ejer AND periodo=$peri AND cuenta=" . $this->mNumeroCuenta . " AND fecha='$fecha'";
					$this->mMessages	.= $this->mNumeroCuenta . "\tACT_SDPM\tActualizar SDPM a $sdpd de la fecha $fecha\r\n";
					
				} else {
					$sqlUSPM = "INSERT INTO captacion_sdpm_historico
									(ejercicio, periodo, cuenta, fecha, dias, tasa, monto, recibo, numero_de_socio, sucursal)
	    							VALUES( $ejer, $peri, " . $this->mNumeroCuenta . ", '$fecha', $diastrans, $nuevatasa, $sdpd, $idrecibo, $socio, '$sucursal')";
					$this->mMessages	.= $this->mNumeroCuenta . "\tADD_SDPM\tAgregando SDPM por $sdpd, $diastrans dias y recibo $idrecibo \r\n";					
				}
				$xs	= my_query($sqlUSPM);


		} else {
			$this->mMessages	.= "ERROR\tNo se Agrego el SDPM de la cuenta " . $this->mNumeroCuenta . " por que se ha fallado en la Operacion\r\n";
		}
	}
	function setUpdateSaldo(){
		$estat	= false;
		if ($this->mSucess	!= false){
			$nuevatasa		= $this->getTasaAplicable(0,0, $this->mNuevoSaldo);
			$fecha			= $this->mFechaOperacion;
			$fechaAnterior	= $this->mFechaUltimaOp;
			$dias_del_mes	= date("t", strtotime($fecha) );
			//Valores iniciados a cero
			$diastrans		= 0;

			$diastrans	= restarfechas($fecha, $this->mFechaUltimaOp);
			if ( $diastrans > $dias_del_mes ){
				$diastrans	= $dias_del_mes;
			}

			if ($diastrans < 0){
				$diastrans = 0;
				$this->mMessages	.= "ERROR\tError al Procesar el Numero de Dias Transcurridos\r\n";
			}

			$sqlucta = "UPDATE captacion_cuentas SET tasa_otorgada=$nuevatasa, fecha_afectacion='$fecha', ";
			$sqlucta .= "saldo_cuenta=" . $this->mNuevoSaldo . ", dias_invertidos=$diastrans
				WHERE numero_cuenta=" . $this->mNumeroCuenta . "";

			if ( $this->mNotUpdateSaldo == false ){
				$x = my_query($sqlucta);
			} else {
				$x["stat"]		= false;
			}
			$estat 				= $x["stat"];
			if ($estat != false){
				$this->mMessages	.= "SUCESS\tSe Actualizo el Saldo de la Cuenta " . $this->mNumeroCuenta . ", Sdo. Ant.(" . $this->mSaldoAnterior . "); Sdo Nuevo(" . $this->mNuevoSaldo . ") Tasa $nuevatasa\r\n";
			} else {
				$this->mMessages	.= "ERROR\tNo se Actualizo la Cuenta(" . $x["info"] . ") Saldo Anterior" . $this->mSaldoAnterior . "; Saldo Nuevo " . $this->mNuevoSaldo . "; Tasa $nuevatasa\r\n";
			}
		} else {
			$this->mMessages	.= "ERROR\tNo se Actualizo la Cuenta, Saldo Anterior" . $this->mSaldoAnterior . "; Saldo Nuevo " . $this->mNuevoSaldo . "\r\n";
		}
		return $estat;
	}

}			//END CLASS CUENTAALAVISTA
/**
 * Clase de Manejo de Cuentas de Inversion
 * @author Balam Gonzalez Luis Humberto
 *
 */
class cCuentaInversionPlazoFijo  extends cCuentaDeCaptacion {
	private $mURLReciboInversion	= "";
	private $mOReciboInversion		= null;
	 
	//las cuentas en cero no forzar dias de inversion.
	function setRetiro($monto, $cheque = DEFAULT_CHEQUE,	$tipo_de_pago = "cheque", $recibo_fiscal = DEFAULT_RECIBO_FISCAL,
						$observaciones = "", $grupo = DEFAULT_GRUPO, $fecha = false, $recibo = false){
		$iduser		= getUsuarioActual();

		/**
		 * verifica el maximo Retirable
		 * Si forzar es VERDADERO, el Maximo retirable es igual al SALDO
		 */
		 if ( $this->mForceOperations == true){
		 	$maximo_ret			= $this->mSaldoActual;
		 	$this->mMessages 	.= "WARN\tLa Operacion sera FORZADA \r\n";
		 } else {
			$maximo_ret	= $this->getMaximoRetirable($fecha);
		 }
		if ( $monto > $maximo_ret) {
			$this->mMessages 	.= "ERROR\tEl Monto a Retirar($monto) es Mayor al Retirable($maximo_ret) \r\n";
			$monto	= 0;
			$recibo	= false;
		}
		if ( $monto > 0){
				if ( in_array($this->mEstatusActual, $this->mEstatusNoOperativos) == true OR ($monto > $this->mSaldoActual)  ){

					$this->mMessages 	.= "ERROR\tLa Cuenta no esta permitida para recibir Operacion, tiene estatus " . $this->mEstatusActual ." ";
					$this->mMessages 	.= "o su saldo(" .  $this->mSaldoActual . ") es Mayor al Monto a  retirar($monto)  \r\n";
					$this->mSucess		= false;
				} else {

					if ( setNoMenorQueCero($this->mPeriodoCuenta) == 0 ){ $this->mPeriodoCuenta	= 1; }
					if ( setNoMenorQueCero($this->mSocioTitular) <= DEFAULT_SOCIO){	$this->init();	}

					if ($fecha == false ){
						if ( isset($this->mFechaOperacion) AND ($this->mFechaOperacion != false)) {
							$fecha = $this->mFechaOperacion;
						} else {
							$fecha = fechasys();
						}
					}
					$fecha					= setFechaValida($fecha);
					$this->mFechaOperacion	= $fecha;
					$socio					= $this->mSocioTitular;

					$CRecibo = new cReciboDeOperacion(8, true);
					//Set a Mvto Contable
					//Agregar recibo si no hay
					if ( setNoMenorQueCero($recibo) == 0 ){
						$recibo = $CRecibo->setNuevoRecibo($socio, $this->mNumeroCuenta,
												$this->mFechaOperacion, $this->mPeriodoCuenta, 8,
												$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
						if (setNoMenorQueCero( $recibo ) == 0 ){
							$this->mMessages	.= "OK\tSe Agrego Exitosamente el Recibo [$recibo] de la Cuenta " . $this->mNumeroCuenta . " \r\n";
							$this->mReciboDeOperacion	= $recibo;
							$this->mSucess		= true;
						} else {
							$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo [$recibo] de la Cuenta " . $this->mNumeroCuenta . " \r\n";
							$this->mSucess		= false;
						}
					}

					$this->mReciboDeOperacion	= $recibo;

					if ( setNoMenorQueCero($recibo) > 0 ){
					//Agregar el Movimiento
						$CRecibo->setNumeroDeRecibo($recibo);
						$CRecibo->setNuevoMvto($fecha, $monto, $this->mOperacionRetiro, $this->mPeriodoCuenta, $observaciones, -1, TM_CARGO, $socio,  $this->mNumeroCuenta);
						$CRecibo->setFinalizarRecibo(true);
						$CRecibo->setFinalizarTesoreria();
						$this->mNuevoSaldo	= $this->mSaldoAnterior - $monto;

						$this->mSucess	= true;
						///Actualizar el recibo
						$this->mReciboDeOperacion	= $recibo;
						//Actualizar la Cuenta
						$this->setUpdateSaldo();
					} else {
						$this->mMessages	.= "ERROR\tNo Existe Recibo con el cual trabajar($recibo) \r\n";
					}

					$this->mMessages	.= $CRecibo->getMessages();
					$this->mORec		= $CRecibo;
				}
		}

		return  $recibo;
	}
	function setDeposito($monto, $cheque = DEFAULT_CHEQUE, $tipo_de_pago = "cheque", $recibo_fiscal = "NA",
						$observaciones = "", $grupo = DEFAULT_GRUPO,	$fecha = false, $recibo = false){
			if ($monto > 0){
				if ( setNoMenorQueCero($this->mPeriodoCuenta) == 0 ){ $this->mPeriodoCuenta	= 1; }
				if ( setNoMenorQueCero($this->mSocioTitular) <= DEFAULT_SOCIO){	$this->init();	}
				if ($grupo == DEFAULT_GRUPO ){ $grupo = $this->mGrupoAsociado; }
					//Corregir la fecha si no existe
					if ($fecha == false ){
						if ( isset($this->mFechaOperacion) AND ($this->mFechaOperacion != false)) {
							$fecha = $this->mFechaOperacion;
						} else {
							$fecha = fechasys();
						}
					}
					$this->mFechaOperacion	= $fecha;
					$socio					= $this->mSocioTitular;

					$CRecibo = new cReciboDeOperacion( 7, true);
					//Agregar recibo si no hay
					if (setNoMenorQueCero($recibo) == 0 ){
						$recibo = $CRecibo->setNuevoRecibo($socio, $this->mNumeroCuenta,
												$this->mFechaOperacion, $this->mPeriodoCuenta, 7,
												$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
						//Checar si se agrego el recibo
						if ( setNoMenorQueCero($recibo) == 0 ){
							$this->mMessages	.= "OK\tSe Agrego Exitosamente el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " de fecha $fecha \r\n";
							$this->mReciboDeOperacion	= $recibo;
							$this->mSucess		= true;
						} else {
							$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " de Fecha $fecha\r\n";
							$this->mSucess		= false;
						}


					}
					$this->mReciboDeOperacion	= $recibo;

					if ( setNoMenorQueCero($recibo) > 0 ){
					//Agregar el Movimiento
						$CRecibo->setNuevoMvto($fecha, $monto, $this->mOperacionDeposito, $this->mPeriodoCuenta, $observaciones, 1, TM_ABONO, $socio,  $this->mNumeroCuenta);
						$CRecibo->addMvtoContableByTipoDePago();
						$CRecibo->setFinalizarRecibo(true);
						$CRecibo->setFinalizarTesoreria();
						$this->mNuevoSaldo	= $this->mSaldoAnterior + $monto;

						$this->mMessages	.= $CRecibo->getMessages();
						$this->mSucess		= true;
						//Actualizar la Cuenta
						$this->setUpdateSaldo();
					} else {
						$this->mMessages	.= "ERROR\tNo Existe Recibo con el cual trabajar($recibo) \r\n";
					}
					$this->mMessages	.= $CRecibo->getMessages();
			}
		return  $recibo;
	}
/**
 * Genera un reinversion
 *
 * @param date $fecha		Fecha de Cierre se refiere a la fecha que que se efectua la Reinversion
 * @param boolean $cerrar	Cerrar, indica si se cierra la inversion.
 * @param float $tasa
 * @param int $dias
 * @return integer Numero de Recibo
 */
	function setReinversion($fecha = false, $cerrar = false,
							$tasa = false, $dias = false,
							$InversionParcial = false, $invertido = 0){
		$recibo			= false;
		$xF				= new cFecha();
		//en Monto de la reinversion sera despues de los depositos (Saldo Actual)
		//si cerrar = false, no efectuar ninguno movimiento
		if ( $cerrar == true ){
			$this->mPeriodoCuenta	= setNoMenorQueCero($this->mPeriodoCuenta) + 1;
			$this->mDiasInvertidos	= ( setNoMenorQueCero($dias) > 0 ) ? $dias : $this->mDiasInvertidos;
			//2012-01-09: correccion de dias menores a cero
			$this->mDiasInvertidos	= ($this->mDiasInvertidos < INVERSION_DIAS_MINIMOS ) ? INVERSION_DIAS_MINIMOS : $this->mDiasInvertidos;
			
			if ( $tasa != false ){
				if ($tasa > $this->mTasaInteres ){
					$this->mTasaInteres = $tasa;
					$this->mMessages	.= "WARN\tTASA\tSe respeta una Tasa Especial de $tasa sobre ". $this->mTasaInteres . " que tenia Anteriormente  \r\n";
				}
			}
				$grupo 				= $this->mGrupoAsociado;
				$observaciones		= "REINVERSION AUTOMATICA # " . $this->mPeriodoCuenta;;
				$recibo_fiscal		= "NA";
				$tipo_de_pago		= TESORERIA_COBRO_NINGUNO;
				$cheque				= "NA";

				$tipo_de_recibo		= 6;
				//OK: Verificar Monto de la Operacion
				$monto				= ( ($InversionParcial == true) AND ($invertido > 0) ) ? $invertido : $this->mNuevoSaldo;
				//verificar Nuevamente la Tasa  
				$tasa				= ( $this->mTasaInteres <= 0 ) ? $this->getTasaAplicable($this->mDiasInvertidos, 0, $monto) : $this->mTasaInteres;
				//algoritmo de tasa incremental
				eval( $this->mModificadorTasa );
				
				$this->mTasaInteres	= $tasa;
			if ( setNoMenorQueCero($this->mSocioTitular) <= DEFAULT_SOCIO){ $this->initCuentaByCodigo(); }
			//2014-09-09 .- validar si el recibo se inicia y tiene fecha de hoy
			if($this->getOReciboInversion() == null){
				//El recibo no existe
				$this->mMessages	.= "WARN\tNo existe el Recibo de Inversion\r\n";
			} else {
				$dia_de_inversion	= $this->getOReciboInversion()->getFechaDeRecibo();
				$recibo_anterior	= $this->getOReciboInversion()->getCodigoDeRecibo();
				if( $xF->getInt($dia_de_inversion) == $xF->getInt($fecha)  ){
					$this->mMessages	.= "WARN\tLa fecha de reinversion[$fecha] es igual al recibo anterior[$recibo_anterior - " . $dia_de_inversion . "] se Eliminara\r\n";
					$this->getOReciboInversion()->setRevertir(true);
					$this->mMessages	.= $this->getOReciboInversion()->getMessages(); 
				}
			}
			if ($fecha == false ){
				if ( isset($this->mFechaOperacion) AND ($this->mFechaOperacion != false)) {
					$fecha 	= $this->mFechaOperacion;
				} else {
					$fecha 	= fechasys();
				}
			}
			//Corrige el Dia Inhabil
			
			//$this->mFechaVencimiento;
			//corrige la fecha de Vencimiento, si es menor a la actual
			if ( strtotime($fecha) >= strtotime($this->mFechaVencimiento) ){
				$this->mMessages	.= "WARN\tLa fecha de reinversion[$fecha] es mayor a la de vencimiento[" . $this->mFechaVencimiento . "]\r\n";
				if ( $this->mDiasInvertidos > 0 ){
					$this->mFechaVencimiento	= $xF->setSumarDias($this->mDiasInvertidos, $fecha);
				}
			}
			$this->mFechaVencimiento			= $xF->getDiaHabil($this->mFechaVencimiento);
			$diasCalculados						= $xF->setRestarFechas($this->mFechaVencimiento, $fecha);
			if ( $diasCalculados > $this->mDiasInvertidos ){
				$this->mDiasInvertidos			= $diasCalculados;
				$this->mMessages				.= "WARN\tSe Actualizan los Dias de Inversion a $diasCalculados\r\n";
			}
			$socio		= $this->mSocioTitular;
			//Inicializar el Recibo
			$CRecibo = new cReciboDeOperacion($tipo_de_recibo, true);


			//Agregar recibo si no hay
			if ( setNoMenorQueCero($recibo) <= 0 ){
				$recibo = $CRecibo->setNuevoRecibo($socio, $this->mNumeroCuenta,
										$this->mFechaOperacion, $this->mPeriodoCuenta, $tipo_de_recibo,
										$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
				//Checar si se agrego el recibo
				if ( setNoMenorQueCero($recibo) > 0 ){
					$this->mMessages	.= "SUCESS\tSe Agrego Exitosamente el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " \r\n";
					$this->mReciboDeReinversion	= $recibo;
					$this->mSucess		= true;
				} else {
					$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " \r\n";
					$this->mSucess		= false;
				}


			} else {
				
			}
			$this->mReciboDeReinversion	= $recibo;
			//Calcular el Interes
			$interes					= (($monto * $this->mTasaInteres) * $this->mDiasInvertidos) / EACP_DIAS_INTERES;
			//OK: Ejecutar modificador de interes
			eval ( $this->mModificadorDeInteres );
			//si hay recibo, agregar
			if ( setNoMenorQueCero($recibo) > 0 ){
			//Agregar el Movimiento de Reinversion
				$CRecibo->setNuevoMvto($fecha, $monto, 223, $this->mPeriodoCuenta, $observaciones, 1, TM_ABONO, $socio,  $this->mNumeroCuenta);
				$this->mMessages	.= "OK\tEVENT\tAgregando la Reinversion por un Monto de $monto de la Cuenta " . $this->mNumeroCuenta . " con fecha $fecha \r\n";
			//Agregar el Movimiento de Interes
				$CRecibo->setNuevoMvto($this->mFechaVencimiento, $interes, 500, $this->mPeriodoCuenta, $observaciones, 1, TM_ABONO, $socio,  $this->mNumeroCuenta);

				$CRecibo->setFinalizarRecibo(true);
				$this->mMessages	.= $CRecibo->getMessages("txt");
				$this->mSucess		= true;
				//Actualizar la Cuenta
				if ( $InversionParcial == false  AND $invertido == 0){
					$this->setUpdateInversion();
				}
			} else {
				$this->mMessages	.= "ERROR\tNo Existe Recibo con el cual trabajar($recibo) \r\n";
			}
			$this->mMessages	.= $CRecibo->getMessages();

		return  $recibo;
		}


	}
	function setUpdateInversion($force_update_saldos = false){
		//Forzar Actualizacion de saldos
		if ( $force_update_saldos == true ){
			$this->setUpdateSaldoByMvtos();
		}
		$stat	= false;
			if ( ($this->mTasaInteres == false) OR !isset($this->mTasaInteres) ){
				$this->mTasaInteres =  $this->getTasaAplicable($this->mDiasInvertidos, 0, $this->mNuevoSaldo);
			}
			//Actualizar la Inversion
					$sqlucta = "UPDATE captacion_cuentas
								SET tasa_otorgada=" . $this->mTasaInteres . ", inversion_fecha_vcto='" . $this->mFechaVencimiento . "'
								, dias_invertidos=" . $this->mDiasInvertidos . ", inversion_periodo = " . $this->mPeriodoCuenta . ",
								recibo_de_inversion = " . $this->mReciboDeReinversion .  "
					WHERE numero_cuenta=" . $this->mNumeroCuenta . "";

					$x		= my_query($sqlucta);
					$estat	= $x["stat"];
			$this->mMessages	.= "La Cuenta se Actualiza a " . $this->mDiasInvertidos . " Dias, Con vencimiento " . $this->mFechaVencimiento . " y Tasa " . $this->mTasaInteres . "\r\n";
		return $stat;
	}

	function setUpdateSaldo(){
		$estat	= false;
		if ($this->mSucess	!= false){
			if ( ($this->mTasaInteres == false) OR !isset($this->mTasaInteres) ){
				$this->mTasaInteres = $this->getTasaAplicable($this->mDiasInvertidos, 0, $this->mNuevoSaldo);
			}
			$nuevatasa		= $this->mTasaInteres;
			$fecha			= $this->mFechaOperacion;
			$fechaAnterior	= $this->mFechaUltimaOp;
			$dias_del_mes	= date("t", strtotime($fecha) );
			$Estatus		= 10;
			//Valores iniciados a cero
			$diastrans		= 0;

			$diastrans		= $this->mDiasInvertidos;

			if ($diastrans < 0){
				$diastrans = 0;
				$this->mMessages	.= "ERROR\tError al Procesar el Numero de Dias Transcurridos($diastrans)\r\n";
			}
			//parche captacion, actualiza el saldo
			if ( $this->mNuevoSaldo > 0 ){
				$Estatus	= 10;
			} else {
				//$Estatus	= 20;
				//Se elimino para que ninguna cuenta este de baja
			}
			$sqlucta = "UPDATE captacion_cuentas SET tasa_otorgada=$nuevatasa, fecha_afectacion='$fecha',
						saldo_cuenta=" . $this->mNuevoSaldo . ", dias_invertidos=$diastrans, estatus_cuenta=$Estatus WHERE numero_cuenta=" . $this->mNumeroCuenta . " ";

			$x 				= my_query($sqlucta);
			$estat 			= $x[SYS_ESTADO];
			if ($estat == false){
				$this->mMessages	.= "ERROR\tNo se Actualizo la Cuenta(" . $x["info"] . ") Saldo Anterior" . $this->mSaldoAnterior . "; Saldo Nuevo " . $this->mNuevoSaldo . "\r\n";
			} else {
				$this->mMessages	.= "OK\tSe Actualizo el Saldo de la Cuenta " . $this->mNumeroCuenta . ", Sdo. Ant.(" . $this->mSaldoAnterior . "); Sdo Nuevo(" . $this->mNuevoSaldo . ") Estado $Estatus\r\n";
			}
		} else {
			$this->mMessages		.= "ERROR\tNo se Actualizo la Cuenta, Saldo Anterior" . $this->mSaldoAnterior . "; Saldo Nuevo " . $this->mNuevoSaldo . "\r\n";
		}
		return $estat;
	}
	function getFechaDeVencimiento(){
		return $this->mFechaVencimiento;
	}

	function getNumeroDePeriodo(){ return $this->mPeriodoCuenta;	}
	function getURLReciboInversion($recibo = false){ return ($recibo == false) ? "../rpt_formatos/frm_pre_reciboinversion.php?c=" . $this->getNumeroDeCuenta() : "../rpt_formatos/frmreciboinversion.php?recibo=$recibo"; }
	function getOReciboInversion(){
		if($this->mOReciboInversion == null){
			if(setNoMenorQueCero($this->mReciboDeReinversion) > 0){
				$xRec	= new cReciboDeOperacion(false, false, $this->mReciboDeReinversion);
				if( $xRec->init() == true){
					$this->mOReciboInversion = $xRec;
				} else {
					$this->mMessages	.= $xRec->getMessages();
				}
			} else {
				$this->mMessages	.= "ERROR\tNo Existe Recibo el recibo de Reinversion con el cual trabajar(" . $this->mReciboDeReinversion . ") \r\n";
			}
		}
		return $this->mOReciboInversion;
	}
}	//END CLASS CUENTAINVERSION


/**
 * Clase de manejo madre de Cuentas de Captacion
 * @author Balam gonzalez Luis Humberto
 *
 */
class cCuentaDeCaptacion {
	protected $mNumeroCuenta			= 0;
	protected $mFechaUltimaOp			= false;
	protected $mSaldoActual				= 0;
	protected $mSaldoAnterior			= 0;
	protected $mSubProducto				= false;
	protected $mTipoDeCuenta			= false;
	protected $mEstatusActual			= false;
	protected $mEstatusNoOperativos		= array();
	protected $mSocioTitular			= 0;
	protected $mPeriodoCuenta			= 1;
	protected $mGrupoAsociado			= DEFAULT_GRUPO;
	protected $mCreditoAsoc				= DEFAULT_CREDITO;
	protected $mDatosCuentaByArray		= array();
	protected $mReciboDeOperacion		= false;
	protected $mReciboDeIDE				= false;
	protected $mNuevoSaldo				= 0;
	protected $mFechaOperacion			= false;
	protected $mDestinoDelInteres		= "";
	protected $mNombreMancomunados		= "";
	protected $mInit					= false;

	protected $mSucess					= false;
	protected $mMessages				= "";
	protected $mIDExRetener				= 0;
	protected $mCuentaIniciada			= false;
	protected $mRaiseError				= false;
	protected $mCuentaBancaria			= false;
	protected $mForceOperations			= false;
	protected $mNotUpdateSaldo			= false;
	protected $mDiasInvertidos			= false;
	protected $mFechaVencimiento		= false;
	protected $mTasaInteres				= false;
	protected $mReciboDeCancelacion		= false;
	protected $mReciboDeReinversion		= false;
	protected $mURLContrato				= "";
	protected $mOperacionRetiro			= 230;
	protected $mOperacionDeposito		= 220;
	protected $mModificadorDeInteres	= "";
	protected $mModificadorTasa			= "";
	protected $mORec					= null; 
	protected $mOTipoCuenta				= null; 
	function __construct($numero_de_cuenta, $socio = 0, $dias_invertidos = 0, $tasa = false, $fecha = false){
		$xT								= new cTipos();
		
		$this->mNumeroCuenta 			= $xT->cInt($numero_de_cuenta);
		$xF								= new cFecha();
		$socio							= $xT->cInt($socio);
			if ($socio != 0 ){
				$this->mSocioTitular	= $socio;
			}
			$this->mFechaOperacion		= ( $fecha == false ) ? fechasys() : $fecha;
			$tasa						= ( $tasa == false ) ? 0 : $tasa;

		if ( isset($numero_de_cuenta) ){
			$this->init();

			//Datos para Operar
			//Inicia los dias Invertidos y la Tasa de Interes
			$this->mDiasInvertidos		= ( $dias_invertidos > 0 ) ? $dias_invertidos : $this->mDiasInvertidos;
			$this->mTasaInteres			= ($tasa > 0) ? $tasa : $this->mTasaInteres;
			//Asigna la fecha de Vencimiento
			$this->mFechaVencimiento	= ( $dias_invertidos > 0 ) ? sumardias($this->mFechaOperacion, $this->mDiasInvertidos) : $this->mFechaVencimiento;
			//Array de estatus no permitidos para operar
			$this->mEstatusNoOperativos = array( 20,30,31 );
		}
		//vuelve a verificar los datos, despues de iniciar las cuentas
			$this->mDiasInvertidos		= ( !isset($this->mDiasInvertidos) OR $this->mDiasInvertidos == false) ? 0 : $this->mDiasInvertidos;
			$this->mTasaInteres			= ( !isset($this->mTasaInteres) OR $this->mTasaInteres == false ) ? 0 : $this->mTasaInteres;
			//Asigna la fecha de Vencimiento
			$this->mFechaVencimiento	= ( !isset($this->mFechaVencimiento) OR $this->mFechaVencimiento == false ) ? fechasys() : $this->mFechaVencimiento;
	}
	function init($ArrayInicial =  false, $force = false){
		$xDTb		= new cSAFETabla(TCAPTACION_CUENTAS);
		//Datos de la cuenta
		$SqlCta =  $xDTb->getQueryInicial() . "
				WHERE
					(`captacion_cuentas`.`numero_cuenta` =" . $this->mNumeroCuenta . ")
				LIMIT 0,1 ";
			if ( $ArrayInicial != false AND is_array($ArrayInicial) ){
				$DC		= $ArrayInicial;
				$this->mMessages					.= "DATOS\tCarga de Datos Externa\r\n";
			} else {
				$DC		= obten_filas($SqlCta);
			}
			if ( isset($DC) AND ($DC != false) ){
				$this->mFechaUltimaOp				= $DC["fecha_afectacion"];
				$this->mSaldoActual					= $DC["saldo_cuenta"];
				$this->mEstatusActual				= $DC["estatus_cuenta"];
				$this->mSocioTitular				= $DC["numero_socio"];
				$this->mPeriodoCuenta				= $DC["inversion_periodo"];
				$this->mCreditoAsoc					= $DC["numero_solicitud"];
				$this->mGrupoAsociado				= $DC["numero_grupo"];
				$this->mSaldoAnterior				= $this->mSaldoActual;
				$this->mTipoDeCuenta				= $DC["tipo_cuenta"];
				$this->mSubProducto					= $DC["tipo_subproducto"];
				$this->mURLContrato					= $DC["nombre_del_contrato"];
				$this->mModificadorDeInteres		= $DC["algoritmo_modificador_del_interes"];
				$this->mModificadorTasa				= $DC["algoritmo_de_tasa_incremental"];
				//TODO: verificar incidencias
				$this->mDiasInvertidos				= $DC["dias"];
				$this->mTasaInteres					= $DC["tasa_otorgada"];
				$this->mFechaVencimiento			= $DC["vencimiento"];
				$this->mDestinoDelInteres			= $DC["destino_del_interes"];
				$this->mReciboDeReinversion			= $DC["recibo_de_inversion"];
				$this->mNombreMancomunados			= $DC["nombre_mancomunado1"];
				 $this->mNombreMancomunados			.= (trim($DC["nombre_mancomunado2"]) == "") ? "" : " & " . $DC["nombre_mancomunado2"];
				$this->mMessages					.= "WARN\tD.FVENC\tLa fecha de Vencimiento es " . $this->mFechaVencimiento . "\r\n";
				
				//Inicia el Nuevo Saldo como el Anterior
				$this->mNuevoSaldo					= $this->mSaldoAnterior;
				$this->mDatosCuentaByArray			= $DC;
				if ( $this->mTipoDeCuenta == CAPTACION_TIPO_PLAZO ){
					$this->mOperacionDeposito		= 221;
					$this->mOperacionRetiro			= 231;
				} else {
					$this->mOperacionDeposito		= 220;
					$this->mOperacionRetiro			= 230;
				}
				$this->mMessages					.= "WARN\tD.OP\tEl Tipo de Operacion para RETIRO ES " . $this->mOperacionRetiro . ", y para DEPOSITO es " . $this->mOperacionDeposito . "\r\n";
				$this->mCuentaIniciada				= (isset($DC["numero_socio"])) ? true : false;
				$this->mInit						= $this->mCuentaIniciada;
				unset ($DC);
			}
		
		return $this->mCuentaIniciada;
	}
	function getClaveDePersona(){ return $this->mSocioTitular; }
	function getNumeroDeCuenta(){ return $this->mNumeroCuenta; }
	function initCuentaByCodigo($ArrayInicial = false){
		return $this->init($ArrayInicial);
	}
	function set($numero_de_cuenta){ $this->mNumeroCuenta	= $numero_de_cuenta; }
	/**
	 * Actualiza el Numero de Recibo de Operacion, en los casos que se eftuan operaciones
	 * @param integer $recibo Numero de Recibo de Operacion
	 */
	function setReciboDeOperacion($recibo){ $this->mReciboDeOperacion	= $recibo; }
	function setSocioTitular($socio){ $this->mSocioTitular	= $socio; }
	function setNotUpdateSaldo($update = true){		$this->mNotUpdateSaldo		= $update;	}
	function setCuentaBancaria($cuenta){ $this->mMessages	.= "WARN\tLa Cuenta Bancaria es $cuenta \r\n"; $this->mCuentaBancaria	= $cuenta;	}
	/**
	 * Agrega un mensaje de Texto al Core
	 * @param string $txt
	 * @return null
	 */
	function addMessage($txt = ""){	$this->mMessages	.= $txt;	}
	function getMontoIDE($fecha = false, $monto = 0, $tipodepago = "efectivo"){
		$ide					= 0;
		if(CAPTACION_IMPUESTOS_A_DEPOSITOS_ACTIVO == true){
			if ( $this->mCuentaIniciada == false ){ $this->init();	}
			$xSoc 				= new cSocio($this->mSocioTitular, true);
			$ide 				= $xSoc->getIDExPagarByPeriodo($fecha, $monto, $tipodepago);
			$this->mMessages 	.= $xSoc->getMessages();
			$this->mIDExRetener	= $ide;
			
		}
		return $ide;
	}		//END FUNCTION IDE
	function setRetenerIDE($fecha = false, $recibo = false, $monto = false, $observaciones = "RETENCION AUTOMATICA DE IDE"){

				$grupo 				= $this->mGrupoAsociado;
				$recibo_fiscal		= "NA";
				$tipo_de_pago		= "descuento";
				$cheque				= "NA";
				$tipo_de_recibo		= 23;
				if ( $monto == false ){
					$monto			= $this->mIDExRetener;
				}

			if ( !isset($this->mSocioTitular) OR ($this->mSocioTitular == 1)){
				$this->initCuentaByCodigo();
			}

			if ($fecha == false ){
				$fecha = $this->mFechaOperacion;
			}

			$socio		= $this->mSocioTitular;
			//Inicializar el Recibo
			$CRecibo 	= new cReciboDeOperacion($tipo_de_recibo, true, $recibo);
			//Set a Mvto Contable
			$CRecibo->setGenerarPoliza();
			$CRecibo->setForceUpdateSaldos();

			//Agregar recibo si no hay
			if ( $recibo == false or !isset($recibo) ){
				$recibo = $CRecibo->setNuevoRecibo($socio, $this->mNumeroCuenta,
										$this->mFechaOperacion, $this->mPeriodoCuenta, $tipo_de_recibo,
										$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
				//Checar si se agrego el recibo
				if ($recibo != false ){
					$this->mMessages	.= "SUCESS\tSe Agrego Exitosamente el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " \r\n";
					$this->mReciboDeIDE	= $recibo;
					$this->mSucess		= true;
				} else {
					$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCuenta . " \r\n";
					$this->mSucess		= false;
				}


			} else {
				$CRecibo->setNumeroDeRecibo($recibo, true);
			}
			$this->mReciboDeIDE	= $recibo;
			//si hay recibo, agregar
			if ( $recibo != false ){
			//Agregar el Movimiento de Reinversion
				$CRecibo->setNuevoMvto($fecha, $monto, 235, $this->mPeriodoCuenta, $observaciones, 1, TM_ABONO, $socio,  $this->mNumeroCuenta);
				$this->mMessages	.= "MVTO_IDE\tAgregando el IDE por un Monto de $monto de la Cuenta " . $this->mNumeroCuenta . " \r\n";

				$CRecibo->setFinalizarRecibo(true);
				$this->mMessages	.= $CRecibo->getMessages();
				$this->mSucess		= true;
				//Actualizar la Cuenta
				$this->mNuevoSaldo	= $this->mNuevoSaldo - $monto;
				$this->setUpdateSaldo();
			} else {
				$this->mMessages		.= "REC_ERR\tNo Existe Recibo con el cual trabajar($recibo) \r\n";
			}
			$this->mMessages			.= $CRecibo->getMessages();

		return  $recibo;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getNuevoSaldo(){ $this->mMessages 			.= "WARN\tNVO_SALDO\tEl Nuevo Saldo es " . $this->mNuevoSaldo . "\r\n";	return $this->mNuevoSaldo;	}
	function getDescription(){
		$describe					= "";
		$xF							= new cFecha();
		if ($this->mCuentaIniciada == false){ $this->init();	}
		$tasa	= getFMoney( ($this->mTasaInteres * 100) );
		switch ($this->mTipoDeCuenta){
			case CAPTACION_TIPO_PLAZO:
				$describe = "INVERSION|VCTO:" . $xF->getFechaCorta($this->mFechaVencimiento) . "|SDO:$" . getFMoney($this->mSaldoActual) ."|TASA:% $tasa|DIAS: " . $this->mDiasInvertidos ;
				break;
			case CAPTACION_TIPO_VISTA:
				$describe = "A_LA_VISTA|OP:" . $xF->getFechaCorta($this->mFechaUltimaOp) . "|SDO:$" . getFMoney($this->mSaldoActual) ."|TASA:% $tasa|PERSONA:" . $this->mSocioTitular;
				break;
		}
		return $describe;
	}
	/**
	 * Retorna la información de una Cuenta de Captación
	 * @return	array	Datos de la Cuenta en un array
	 */
	function getDatosInArray(){
		if ($this->mCuentaIniciada == false){
			$this->init();
		}
		return $this->mDatosCuentaByArray;
	}
	/**
	 * Retorna la información de una Cuenta de Captación
	 * @return	array	Datos de la Cuenta en un array
	 * @deprecated	1.9.41 rev 33
	 */
	function getDatosCuentaByArray(){
		return $this->getDatosInArray();
	}

	/**
	 * Funcion que Actualiza la Cuenta de Captacion segun un array tipo Campo=>valor
	 *
	 */
	function setUpdate($aParam){
		$WithSocio = "";
		/**
		*	Marcar Segunda condicion
		*/
		if ( $this->mSocioTitular != false )	{

			$WithSocio	= "	AND
							(`captacion_cuentas`.`numero_socio` =" . $this->mSocioTitular . ")  ";

		}
		$sqlBody	= "";
		//TODO: Duplicar en las demas clases
		if ( is_array($aParam) AND count($aParam) >=1 ){
			$BodyUpdate = "";
			foreach ($aParam as $key => $value) {
				if ( is_string($value) ){
					$value	= "\"" . $value . "\"";
				}
				if ($BodyUpdate == ""){
					$BodyUpdate .= "$key = $value ";
				} else {
					$BodyUpdate .= ", $key = $value ";
				}
			}	//END FOREACH
			$sqlBody	= "UPDATE captacion_cuentas
							    SET $BodyUpdate
							    WHERE
						(`captacion_cuentas`.`numero_cuenta` =" . $this->mNumeroCuenta . ")
						$WithSocio";
			$x = my_query($sqlBody);

			return $x["stat"];
		} else {
			return false;
		}
	}
	function setForceOperations( $force = true ){ $this->mForceOperations		= $force;}
	function setCambiarCodigo($NuevoCodigo){
		$socio 		= $this->mSocioTitular;
		$cuenta		= $this->mNumeroCuenta;
		$NCuenta	= $NuevoCodigo;
		$msg		.= "";
		$arrICap	= array();
		$arrISoc	= array();
		//verifica si existe la cuentas
		$SqlEC 		= "SELECT count(numero_cuenta) AS 'isd' FROM captacion_cuentas WHERE numero_cuenta = $NCuenta";
		$hay		= mifila($SqlEC, "isd");
		if ($hay >= 1){
		 $msg 		.= date("H:i:s") . "\t$socio\t$cuenta\tLa Cuenta $NCuenta EXISTE \r\n";

		} else {
			//Actualiza las Firmas
		$rs1	= "UPDATE captacion_firmas
					SET numero_de_cuenta=$NCuenta
					WHERE
				numero_de_cuenta=$cuenta";
		//my_query($rs1);
		//$msg .= date("Y-m-d H:i:s") . "\t$socio\t$cuenta\tSe Actualizo la Tabla Captacion Firmas\r\n";

		$rs2	= "UPDATE captacion_sdpm_historico
	    SET cuenta=$NCuenta
	    WHERE
		cuenta=$cuenta";
		//my_query($rs2);
		//$msg .= date("Y-m-d H:i:s") . "\t$socio\t$cuenta\tSe Actualizo la Tabla Captacion SDPM\r\n";


		$rs3	= "UPDATE captacion_cuentas
				    SET numero_cuenta=$NCuenta
				    WHERE
					numero_cuenta=$cuenta
					AND
					numero_socio=$socio";
		$x = my_query($rs3);
			if ($x["stat"] == false ){
				$msg .= date("H:i:s") . "\t$socio\t$cuenta\tLa Actualizacion Fallo(" . $x["error"] .  ")\r\n";
			} else {
			$msg .= date(" H:i:s") . "\t$socio\t$cuenta\tSe Actualizo la Tabla Captacion Cuentas(" . $x["info"] .  ")\r\n";
			}

		// Actualiza los Recibos
		$rs4 = "UPDATE operaciones_recibos SET docto_afectado=$NCuenta
					WHERE
					docto_afectado=$cuenta
						AND
					numero_socio=$socio";
		$x = my_query($rs4);
			if ($x["stat"] == false ){
				$msg .= date("Y-m-d H:i:s") . "\t$socio\t$cuenta\tLa Actualizacion Fallo(" . $x["error"] .  ")\r\n";
			} else {
			$msg .= date("Y-m-d H:i:s") . "\t$socio\t$cuenta\tSe Actualizo la Tabla Operaciones Recibos(" . $x["info"] .  ")\r\n";
			}

		// Actualiza los Movimientos
		$rs5 = "UPDATE operaciones_mvtos SET docto_afectado=$NCuenta
					WHERE docto_afectado=$cuenta
					AND
					socio_afectado=$socio";
		$x = my_query($rs5);
			if ($x["stat"] == false ){
					$msg .= date("Y-m-d H:i:s") . "\t$socio\t$cuenta\tLa Actualizacion Fallo(" . $x["error"] .  ")\r\n";
			} else {
					$msg .= date("Y-m-d H:i:s") . "\t$socio\t$cuenta\tSe Actualizo la Tabla Operaciones Movimientos(" . $x["info"] .  ")\r\n";
			}
		}
		return $msg;
	}
	/**
	 * Elimina una cuenta de Captacion
	 * @return string	Mensajes del proceso
	 */
	function setDelete(){
		$msg	= "===========\tELIMINADO LA CUENTA " . $this->mNumeroCuenta . "\r\n";
		$cuenta	= $this->mNumeroCuenta;
		$socio	= $this->mSocioTitular;
			//Cuenta
			$SQLDCuenta 	= "DELETE FROM captacion_cuentas WHERE numero_cuenta = $cuenta AND numero_socio = $socio ";
			$x = my_query($SQLDCuenta);

			if ($x["stat"] == false ){
				$msg	.= date("H:i:s") . "\tERROR\t" . $x["error"] . "\r\n";
			} else {
				$msg	.= date("H:i:s") . "\tSUCESS\t Eliminando la Cuenta (" . $x["info"] . ")\r\n";
			}
			//Firma
			/*$SQLDFirma 	= "DELETE FROM socios_firmas WHERE numero_de_cuenta = $cuenta ";
			$x = my_query($SQLDFirma);

			if ($x["stat"] == false ){
				$msg	.= date("H:i:s") . "\tERROR\t" . $x["error"] . "\r\n";
			} else {
				$msg	.= date("H:i:s") . "\tSUCESS\tEliminando las Firmas (" . $x["info"] . ")\r\n";
			}*/
			//sdpm
			$SQLD_SDPM 	= "DELETE FROM captacion_sdpm_historico WHERE cuenta =  $cuenta ";
			$x = my_query($SQLD_SDPM);

			if ($x["stat"] == false ){
				$msg	.= date("H:i:s") . "\tERROR\t" . $x["error"] . "\r\n";
			} else {
				$msg	.= date("H:i:s") . "\tSUCESS\t" . $x["info"] . "\r\n";
			}

			//Movimientos
			$SQLDOpes	= "DELETE FROM operaciones_mvtos WHERE docto_afectado = $cuenta AND socio_afectado = $socio ";
			$x = my_query($SQLDOpes);
			if ($x["stat"] == false ){
				$msg	.= date("H:i:s") . "\tERROR\t" . $x["error"] . "\r\n";
			} else {
				$msg	.= date("H:i:s") . "\tSUCESS\t" . $x["info"] . "\r\n";
			}

			$SQLDRecs	= "DELETE FROM operaciones_recibos WHERE docto_afectado = $cuenta AND numero_socio = $socio ";
			$x = my_query($SQLDRecs);
			if ($x["stat"] == false ){
				$msg	.= date("H:i:s") . "\tERROR\t" . $x["error"] . "\r\n";
			} else {
				$msg	.= date("H:i:s") . "\tSUCESS\t" . $x["info"] . "\r\n";
			}

			//Actualizar el Credito Relacionado
			$SQLDCC	= "UPDATE creditos_solicitud
						SET contrato_corriente_relacionado = " . CTA_GLOBAL_CORRIENTE . "
						WHERE contrato_corriente_relacionado = $cuenta ";
			$x = my_query($SQLDCC);
			if ($x["stat"] == false ){
				$msg	.= date("H:i:s") . "\tERROR\t" . $x["error"] . "\r\n";
			} else {
				$msg	.= date("H:i:s") . "\tSUCESS\tActualizando Creditos Relacionados (" . $x["info"] . ") \r\n";
			}
		return $msg;
	}
	/**
	 * Retorna el Numero de Cuentas Existentes
	 * @return integer		Numero de cuentas Existentes
	 */
	function setContarCuenta(){
		$sql_hay 		= "SELECT COUNT(numero_cuenta) AS 'cuentame'
							FROM captacion_cuentas
							WHERE numero_cuenta=" . $this->mNumeroCuenta;
		$cuentas		= mifila($sql_hay, "cuentame");
		return $cuentas;
	}
	function setContarCuentaBySocio($socio, $subproducto = 10){
		$sql_hay		= "SELECT COUNT(numero_cuenta) AS 'cuentame'
							FROM captacion_cuentas
							WHERE numero_socio=" . $socio . "
							AND tipo_subproducto=$subproducto ";
		$cuentas		= mifila($sql_hay, "cuentame");
		return $cuentas;
	}
	function setDiasInvertidos($dias = 0){ $this->mDiasInvertidos	= $dias; }
	function setFechaDeOperacion($Fecha){ $this->mFechaOperacion	= $Fecha; }
	/**
	 * Obtiene el Maximo a Retirar por Cuenta
	 * @param date $fecha 	Fecha del Retiro
	 * @return float 		Monto Maximo permitido
	 */
	function getMaximoRetirable($fecha = false){
		if ( $fecha == false ){
			$fecha = fechasys();
		}
		if ( $this->mCuentaIniciada == false ) {
		  $this->init();
		}
		//Obtener la Captacion Total
		$xSoc					= new cSocio($this->mSocioTitular);
		$DTCap					= $xSoc->getTotalCaptacionActual();
		$this->mMessages 		.= "MAX_RETIRABLE\tA la fecha $fecha, El Saldo Total de Captacion es " . $DTCap["saldo"] . " \r\n";
		$this->mMessages 		.= "MAX_RETIRABLE\tA la fecha $fecha, El saldo por Esta Cuenta es " . $this->mNuevoSaldo . " \r\n";
		$saldo					= $this->mSaldoAnterior;
		$saldo					= ( $DTCap["saldo"] - $saldo ) + $this->mNuevoSaldo ;

		$maximo_retirable		= 0;
		//obtener los maximos retirables por credito
		//2011-1-30 cambio de monto_autorizado a saldo_actual
		$sqlCompCreditos = "SELECT
								SUM(`creditos_solicitud`.`saldo_actual`) AS `monto`
							FROM
								`creditos_solicitud` `creditos_solicitud`
									INNER JOIN `eacp_config_bases_de_integracion_miembros`
									`eacp_config_bases_de_integracion_miembros`
									ON `creditos_solicitud`.`tipo_convenio` =
									`eacp_config_bases_de_integracion_miembros`.`miembro`
								WHERE
									(`creditos_solicitud`.`numero_socio` = " . $this->mSocioTitular . ")
									AND
									(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 1110)
									AND
                 					(`creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . ")
								GROUP BY
									`creditos_solicitud`.`numero_socio`,
									`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
                   ";
		$d							= obten_filas($sqlCompCreditos);
		//Obtener el Maximo Comprometidos por Garantia Liquida
		$sqlCC = "SELECT
							`creditos_solicitud`.`numero_socio`,
							SUM(`creditos_solicitud`.`saldo_actual`) AS 'monto_creditos',
							SUM(`creditos_solicitud`.`saldo_Actual` *
							`creditos_tipoconvenio`.`porciento_garantia_liquida`) AS 'monto_garantia'

						FROM
							`creditos_solicitud` `creditos_solicitud`
								INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
								ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
								`idcreditos_tipoconvenio`
						WHERE
							(`creditos_solicitud`.`numero_socio` =" . $this->mSocioTitular . ")
							AND
							(`creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . ")
						GROUP BY
							`creditos_solicitud`.`numero_socio` ";
		$comprometido_por_garantia	= 0;

		$comprometido_por_creditos	= setNoMenorQueCero( $d["monto"] );
		$comprometido_por_ide		= 0;			//Terminar el saldo de IDE retirable
		$this->mMessages 			.= "MAX_RETIRABLE\tA la fecha $fecha, Lo Comprometido por Creditos es de $comprometido_por_creditos \r\n";
		$this->mMessages 			.= "MAX_RETIRABLE\tA la fecha $fecha, Lo Comprometido por IDE es de $comprometido_por_ide\r\n";

		//
		if ( $this->mSubProducto == CAPTACION_PRODUCTO_GARANTIALIQ){
			$c							= obten_filas($sqlCC);
			$comprometido_por_garantia	= setNoMenorQueCero( $c["monto_garantia"] );
			$this->mMessages 			.= "MAX_RETIRABLE\tA la fecha $fecha, Lo Comprometido por GARANTIA LIQUIDA ES $comprometido_por_garantia \r\n";
			$saldo						= $this->mNuevoSaldo;	//correccion por garantia liquida
			$comprometido_por_creditos	= 0;					//La ganartia liquida solo es presionado por lo comprometido por garantia
		}
		//


		$maximo_retirable			= $saldo - ($comprometido_por_creditos + $comprometido_por_ide + $comprometido_por_garantia);
		$maximo_retirable			= setNoMenorQueCero($maximo_retirable);
		$this->mMessages 			.= "MAX_RETIRABLE\tA la fecha $fecha, El Maximo Retirable es de $maximo_retirable de un Monto de $saldo\r\n";
		//Componer el Saldo, no puede ser mayor al nuevo saldo
		return ($maximo_retirable <= $this->mNuevoSaldo ) ? round($maximo_retirable, 2) : round($this->mNuevoSaldo, 2);
	}
	/**
	 * Funcion que agrega una nueva Cuenta
	 * @param $origen					Origen de la cuenta, lavado de dinero
	 * @param $subproducto				Subproducto al que pertenece
	 * @param $socio
	 * @param $observaciones
	 * @param $credito
	 * @param $mancomunado1
	 * @param $mancomunado2
	 * @param $grupo
	 * @param $fecha_alta
	 * @param $tipo_de_cuenta
	 * @param $tipo_de_titulo
	 * @param $DiasInvertidos
	 * @param $tasa
	 * @param $CuentaDeIntereses
	 * @return unknown_type
	 */
	function setNuevaCuenta($origen, $subproducto, $socio,
							$observaciones = "", $credito = 1,
							$mancomunado1 = "", $mancomunado2 = "",
							$grupo = 99, $fecha_alta = false,
							$tipo_de_cuenta = 20, $tipo_de_titulo = 99, $DiasInvertidos = false,
							$tasa = false, $CuentaDeIntereses	= false, $FechaVencimiento = false
							){


		$xT		= new cTipos(0);

		if ( $this->mNumeroCuenta == false ){
			//Asigna una cuenta por estructura
				$cuenta 		= $xT->cSerial(2, $subproducto) . $xT->cSerial(DIGITOS_DE_CLAVE_DE_SOCIO, $socio) . "01";
				$sqlNCta 		= "SELECT COUNT(numero_cuenta) AS 'cuentas'
									FROM captacion_cuentas
									WHERE numero_socio=$socio";
				$datos 			= obten_filas($sqlNCta);

				if ( isset($datos["cuentas"]) OR $datos["cuentas"] > 0) {
					$cuenta = $xT->cSerial(2, $subproducto) . $xT->cSerial(DIGITOS_DE_CLAVE_DE_SOCIO, $socio) . ($datos["cuentas"] + 1);
					$this->mMessages 		.= "WARN\tSe Presume al Numero de cuenta $cuenta por que el socio tiene  " . $datos["cuentas"] . " cuentas\r\n";
				}
				$this->mNumeroCuenta = $cuenta;
				unset($datos);
		}
				$idsolicitud 	= $credito;
				$idgrupo 		= $grupo;
				$observacion	= $observaciones;
				$xF				= new cFecha();
					if ( $DiasInvertidos == false ){
						$DiasInvertidos		= $this->mDiasInvertidos;
					}
					$fechaalta	= ($fecha_alta == false) ? $this->mFechaOperacion : $fecha_alta;

					if ( $tasa == false){
						$tasa				= $this->mTasaInteres;
					} else {
						$this->mTasaInteres	= $tasa;
					}
					if ( ($this->mSocioTitular != 0) AND (isset($this->mSocioTitular))  AND ($this->mSocioTitular != false) ){
						$idsocio		= $this->mSocioTitular;
					} else {
						if (  isset($socio) && ($socio != false) ){
							$idsocio 				= $socio;
							$this->mSocioTitular 	= $socio;
						}
					}
					if ( $CuentaDeIntereses	== false ){
						$CuentaDeIntereses	= CTA_GLOBAL_CORRIENTE;
					}
					$FechaVencimiento	= ($FechaVencimiento == false) ? $xF->setSumarDias($DiasInvertidos, $fechaalta) : $FechaVencimiento;

					$origencuenta 		= $origen;
					$tipotitulo			= $tipo_de_titulo;
					$lacuenta 			= $this->mNumeroCuenta;
					$sucursal			= getSucursal();
					$fechabaja 			= "2029-12-31";

					$estatus 			= 10;
					$man1 				= $mancomunado1;
					$man2 				= $mancomunado2;

					$subpdto 			= $subproducto;
					$sqlPdto 			= "SELECT * FROM captacion_subproductos	WHERE idcaptacion_subproductos=$subpdto";
					$dPdto 				=  obten_filas($sqlPdto);

					$tipocuenta 		= $dPdto["tipo_de_cuenta"];
					$contrato			= $dPdto["nombre_del_contrato"];

					$iduser				= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
					$eacp				= EACP_CLAVE;
	//verifica si existe la Cuenta
					$cuentas_existentes = $this->setContarCuenta();
	//
					if ( $cuentas_existentes > 0 ) {
						//sumar otro folio
						$lacuenta++;
						$this->mNumeroCuenta	= $lacuenta;
							$cuentas_existentes	= $this->setContarCuenta();
							if ( $cuentas_existentes > 0 ) {
								 $lacuenta++;
								 $this->mNumeroCuenta	= $lacuenta;
							}
					}
	// Si es Inversion la Cuenta Estara Inactiva
					if ($tipocuenta == 20) {
						$estatus = 20;
					}
			$sqlNR	= "INSERT INTO captacion_cuentas(
						numero_cuenta, numero_socio, numero_grupo, numero_solicitud, tipo_cuenta,
						fecha_apertura, fecha_afectacion, fecha_baja, estatus_cuenta, saldo_cuenta,
						eacp, idusuario, inversion_fecha_vcto, inversion_periodo, tasa_otorgada, dias_invertidos, observacion_cuenta, origen_cuenta,
						tipo_titulo, tipo_subproducto, nombre_mancomunado1, nombre_mancomunado2, minimo_mancomunantes, saldo_conciliado, fecha_conciliada,
						sucursal, ultimo_sdpm, oficial_de_captacion,
						cuenta_de_intereses)
    					VALUES(
						$lacuenta, $idsocio, $idgrupo, $idsolicitud, $tipocuenta,
					'$fechaalta', '$fechaalta', '$fechabaja', $estatus, 0,
					'$eacp', $iduser,
					'$FechaVencimiento', 1, $tasa, $DiasInvertidos, '$observacion', $origencuenta,
					$tipotitulo, $subpdto, '$man1', '$man2', 0, 0, '$fechaalta',
					'$sucursal', 0, $iduser,
					$CuentaDeIntereses)";
			$r = my_query($sqlNR);
			if ( $r["stat"] == true) {
				$this->mMessages 		.= "OK\tSe Agrego Existosamente la Cuenta $lacuenta del subproducto $subpdto \r\n";
				$this->mSucess 				= true;
				$this->mSocioTitular		= $idsocio;
				$this->mGrupoAsociado		= $grupo;
				$this->mCreditoAsoc			= $idsolicitud;
				$this->mNumeroCuenta		= $lacuenta;
				$this->mDiasInvertidos		= $DiasInvertidos;
				$this->mFechaVencimiento	= $FechaVencimiento;
			} else {
				$this->mMessages 		.= "ERROR\tal Agregar la Cuenta $lacuenta del subproducto $subpdto\r\n";
				if ( MODO_DEBUG == true){
					$this->mMessages	.= $r["info"];
				}
				$this->mSucess 			= false;
			}


			return $this->mNumeroCuenta;
	}

	function getFicha($fieldset = false, $trTool = "", $extendido = false){
			$Dcta	= $this->getDatosInArray();
			$xF					= new cFecha();
			$xLi				= new cSQLListas();
			$xQL				= new MQL();
			$rw 				= $Dcta;
			$cuenta 			= $rw["numero_cuenta"];
			$modalidad 			= $rw["tipo"];
			$FApertura 			= getFechaMX($rw["fecha_apertura"]);
			$tasa 				= $rw["tasa_otorgada"];
			$producto			= $rw["subproducto"];
			$saldo 				= $rw["saldo_cuenta"];
			$mancomunantes 		= $this->mNombreMancomunados;
			$observaciones 		= $rw["observaciones"];
			$tool 				= $trTool;
			$thead				= "";
			$xL					= new cLang();
			if($extendido == true){
				$xSoc			= new cSocio($this->getClaveDePersona());
				$xSoc->init();
				$nombrepersona	= $xSoc->getNombreCompleto();
				$thead			.= "<tr><th  class='izq'>" . $xL->getT("TR.nombre completo") .  "</th>" ;
				$thead			.= "<td colspan='3'>$nombrepersona</td><tr>";
			}
			//eOperations		= false;
			if(trim($mancomunantes) == ""){
				
			} else {
				$tool			.= "<tr><th>" . $xL->getT("TR.Mancomunados") . "</th><td colspan='3'>$mancomunantes</td><tr>";
			}
			//Obtener Mancomunantes
			/*$SQLMan			= $xLi->getListadoDeRelaciones($this->getClaveDePersona(), $this->getNumeroDeCuenta(), PERSONAS_REL_MANCOMUNADO );
			$rsMan				= $xQL->getDataRecord($SQLMan);
			if($xQL->getNumberOfRows() > 0){
				$tool			.= "<tr><th colspan='4'>" . $xL->getT("TR.Mancomunados") . "</th><tr>";
				foreach ($rsMan as $rowM){
					//$idmanco	= 
					$tool		.= "<tr>";
					$tool		.= "<td>";
					$tool		.= "</tr>";
				}
			}*/
			if ( $this->mTipoDeCuenta == CAPTACION_TIPO_PLAZO ){
				$thead			= ($thead == "") ? "" : "<thead>$thead</thead>";
			$exoFicha =  "
				<table id='ficha-captacion'>
					$thead
				<tbody>
				<tr>
					<th class='izq'>" . $xL->getT("TR.clave_de_cuenta") . "</th>
					<td>" . $this->mNumeroCuenta . "</td>
					<th class='izq'>" . $xL->getT("TR.Producto") . "</th>
					<td>" . $Dcta["subproducto"] . "</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.Fecha de Operacion") . "</th>
					<td>" . $xF->getFechaCorta($Dcta["apertura"]) . "</td>
					<th class='izq'>Fecha de Vencimiento</th>
					<td>" . $xF->getFechaCorta($Dcta["vencimiento"]) . "</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.tasa actual") . "</th>
					<td class='mny'> %  " . ($Dcta["tasa"]  * 100) . "</td>
					<th class='izq'>" . $xL->getT("TR.dias de Inversion") . "</th>
					<td class='mny'>" . $Dcta["dias"] . "</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.saldo") . "</th><td class='mny'>" . getFMoney($Dcta["saldo"]) . "</td>
					<th class='izq'>" . $xL->getT("TR.observaciones") . "</th><td>" . $Dcta["observaciones"] . "</td>
				</tr>
				$tool
				</tbody>
				</table>";
			} elseif ($this->mTipoDeCuenta == CAPTACION_TIPO_VISTA ){
				$thead			= ($thead == "") ? "" : "<thead>$thead</thead>";
				$exoFicha =  "
				<table id='ficha-captacion'>
				$thead
				<tbody>
				<tr>
					<th class='izq'>" . $xL->getT("TR.clave_de_cuenta") . "</th><td>$cuenta</td>
					<th class='izq'>" . $xL->getT("TR.Fecha de Registro") . "</th><td>". $xF->getFechaCorta($FApertura) . "</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.tipo") . "</th><td>$modalidad</td>
					<th class='izq'>" . $xL->getT("TR.producto") . "</th><td>$producto</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.tasa") . "</th><td class='mny'>% " . getFMoney( ($tasa * 100) ) . "</td>
					<th class='izq'>" . $xL->getT("TR.saldo") . "</th><td class='mny'>" . getFMoney($saldo) . "</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.notas") . "</th><td colspan='3'>$observaciones</td>
				</tr>
				$tool
				</tbody>
				</table>";
			} else {

			}
			if ($fieldset == true){
				$exoFicha = "<fieldset>
								<legend>&nbsp;&nbsp;" . $xL->getT("TR.Ficha de datos") . "&nbsp;&nbsp;</legend>
								$exoFicha
							</fieldset>";
			}
		return $exoFicha;
	}
	function getURLContrato(){
		return $this->mURLContrato;
	}
	function getDatosDeTipoDeCuenta(){ return $this->getOTipoDeCuenta()->getDatosInArray();	}
	function getOTipoDeCuenta(){
		if($this->mOTipoCuenta == null){ $this->mOTipoCuenta = new cTipoDeCuentaDeCaptacion($this->mTipoDeCuenta); $this->mOTipoCuenta->init(); }
		return $this->mOTipoCuenta;
	}
	function getDestinoDelInteres(){
		return $this->mDestinoDelInteres;
	}
	function getTipoDeSubproducto(){
		return $this->mSubProducto;
	}
	function setUpdateSaldoByMvtos(){
		$EquivBase	= array(
					10	=> 3100,
					20	=> 3200
					);
		$cuenta		= $this->mNumeroCuenta;
		$ready		= $this->mCuentaIniciada;
		$ready		= (isset($EquivBase[ $this->mTipoDeCuenta ])) ? true : false;
		if($ready == true){
		$sql		= "SELECT
				`operaciones_mvtos`.`docto_afectado` AS 'documento',
				COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS 'operaciones',
				SUM(`operaciones_mvtos`.`afectacion_real` *
				`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'saldo'
			FROM
			`operaciones_mvtos` `operaciones_mvtos`
				INNER JOIN `eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
				ON `operaciones_mvtos`.`tipo_operacion` =
				`eacp_config_bases_de_integracion_miembros`.`miembro`
			WHERE
			(`operaciones_mvtos`.`docto_afectado` =$cuenta) AND
			(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = " . $EquivBase[ $this->mTipoDeCuenta ] . ")
			GROUP BY
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
			`operaciones_mvtos`.`docto_afectado`
			ORDER BY
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`operaciones_mvtos`.`fecha_afectacion`,
				`eacp_config_bases_de_integracion_miembros`.`afectacion`";
		$DT			= obten_filas($sql);
		$saldo		= ( isset($DT["saldo"]) ) ? $DT["saldo"] : 0 ;
		my_query("UPDATE captacion_cuentas SET saldo_cuenta = $saldo WHERE numero_cuenta = $cuenta");
		} else {
			$this->mMessages	.= "ERROR\tLa cuenta $cuenta no se Inicio o no existe la base";
		}
		return $ready;
	}
	/**
	 * Valida la Cuenta de Captacion
	 * @param boolean $ForzarCorreccion
	 */
	function setValidar($ForzarCorreccion = false){
		
		$arrUp		= array();
		$DC			= $this->getDatosInArray();
		$socio		= $this->mSocioTitular;
		$cuenta		= $this->mNumeroCuenta;
		$grupo		= $this->mGrupoAsociado;
		$sucursal	= $DC["sucursal"];
		$user		= $DC["idusuario"];
		$oficial	= $DC["oficial_de_captacion"];
		$credito	= $DC["numero_solicitud"];
		$FApertura	= $DC["fecha_apertura"];
		$FAfecta	= $DC["fecha_afectacion"];
		//Fecha Valida
		$FechaValida	= true;
		
		$msg		= "VALIDAR CUENTA $cuenta\r\n";
		//Datos propios de la Inversion
		$FVencInv	= $DC["inversion_fecha_vcto"];
		$DiasInv	= $DC["dias_invertidos"];
		$TCuenta	= $DC["tipo_cuenta"];
		//Cuenta de Intereses
		$CtaInts	= $DC["cuenta_de_intereses"];
		//Datos producto
		$xDT		= new cInformacionProductoCaptacion($TCuenta);
		$DT			= $xDT->init();
		//validar socios
		$xSoc		= new cSocio($socio);
		if ( $xSoc->existe($socio) == false ){
			$msg	.= "CRITICO\tSOCIO\tEl Socio $socio no EXISTE \r\n";
			if ( $ForzarCorreccion == true ){
				$xSoc->add("SOCIO_CUENTA_$cuenta");
				$msg	.= "NUEVO\tSOCIO\tAgregado el Socio $socio\r\n";
			}
		}
		//validar grupo
		if ( $grupo == false OR $grupo == 0 ){
			$arrUp["numero_grupo"]	= DEFAULT_GRUPO;
			$msg	.= "ERROR\tGRUPO\tEl Grupo $grupo No es Valido, se actualiza al default \r\n";
		} else {
			//Controlar Grupo
			$xGrp	= new cGrupo($grupo);
			if ( $xGrp->existe($grupo) == false ){
				$msg	.= "CRITICO\tGRUPO\tEl Grupo $grupo no EXISTE \r\n";
				if ( $ForzarCorreccion == true ){
					$xGrp->add("GRUPO_CUENTA_$cuenta", "", false, false, 10, 1, $grupo, $sucursal);
					$msg	.= "NUEVO\tGRUPO\tSe Agrego el Grupo $grupo \r\n";
				}
			}
		}
		//Restaurar Credito
		if ($credito != DEFAULT_CREDITO){
			$arrUp["numero_solicitud"]	= DEFAULT_CREDITO;
		}
		//
		//Controlar Usuario
		$xUsr		= new cSystemUser($user);
		if ( $xUsr->existe($user) == false ){
			$msg	.= "CRITICO\tUSUARIO\tEl Usuario $user no EXISTE \r\n";
			if ( $ForzarCorreccion == true ){
				$xUsr->add("usr$user", "", 2, "USR_CTA_$cuenta", "", "", "", false, "baja", "", $sucursal, $user);
				$msg	.= "NUEVO\tUSUARIO\tSe Agrego el Usuario $user \r\n";
			}
		}
		//Controlar Oficial de Captacion
		if ( $xUsr->existe($oficial) == false ){
			$msg	.= "ERROR\tOFICIAL\tEl Oficial de Captacion $oficial no EXISTE \r\n";
			if ( $ForzarCorreccion == true ){
				$xUsr->add("usr$oficial", "", 2, "OFICIAL_CTA_$cuenta", "", "", "", false, "baja", "", $sucursal, $oficial);
				$msg	.= "NUEVO\tOFICIAL\tSe Agrego el OFICIAL $oficial \r\n";
			}
		}
		if ( $FApertura == '0000-00-00' ){
				$msg	.= "WARN\tLa fecha de Apertura $FApertura es INVALIDA\r\n";
				$arrUp["fecha_apertura"]	= fechasys();
				$FechaValida = false;		
		}
		if ( $FAfecta == '0000-00-00' ){
				$msg	.= "WARN\tLa fecha de Afectacion $FAfecta es INVALIDA\r\n";
				$arrUp["fecha_afectacion"]	= fechasys();
				$FechaValida = false;		
		}
		//Validar las fechas
		if ( $FechaValida == true ){
			if ( strtotime($FApertura) > strtotime($FAfecta) ){
				$msg	.= "WARN\tLa fecha de Apertura $FApertura es mayor a la de operaciones $FAfecta\r\n";
				$arrUp["fecha_apertura"]	= $FAfecta;
			}
		}
		
		//Validar las funciones del Subproducto
		if ( $ForzarCorreccion == true ){
			//Guardar Cambios
			$this->setUpdate($arrUp);
			return $msg;
		}
	}
	/**
	 * Elimina una serie de Movimientos filtrados de la cuenta de captacion
	 * @param integer $tipo
	 * @param string $fecha
	 * @param double $recibo
	 */
	function setDeleteMvto($tipo, $fecha = false, $recibo = false){
		$mWFecha		= ( $fecha == false ) ? "" : " AND (`operaciones_mvtos`.`fecha_operacion` = '$fecha')";
		$mWRecibo		= ( $recibo == false ) ? "" : " AND (`operaciones_mvtos`.`recibo_afectado` = $recibo )  ";
		$mDSQL			= " DELETE FROM `operaciones_mvtos` 
							WHERE
								(`operaciones_mvtos`.`docto_afectado` = " . $this->mNumeroCuenta . ")
								AND
								(`operaciones_mvtos`.`tipo_operacion` = $tipo) $mWFecha $mWRecibo ";
		$xM				= my_query($mDSQL);
		return 			$xM["info"];
	}
	/**
	 * Obtienen una tasa aplicable al producto
	 * @param integer $Dias
	 * @param float $Monto
	 */	
	function getTasaAplicable($Dias = 0 ,$MontoAdicional = 0, $BaseForzada = false){
		if ( $this->mCuentaIniciada == false ){	$this->init();	}
		$BaseDeCalculo	= (setNoMenorQueCero($BaseForzada) > 0) ? $BaseForzada : $MontoAdicional + $this->mSaldoActual;
		$DiasEstimados	= ( $Dias == 0 ) ? $this->mDiasInvertidos : $Dias;
		$tasa			= obtentasa($BaseDeCalculo, $this->mTipoDeCuenta, $DiasEstimados, $this->mSubProducto);
		//A_LA_VISTA_MONTO_MINIMO
		//algoritmo de tasa incremental
		eval( $this->mModificadorTasa );
		return $tasa;
	}
	function setTraspaso($CuentaDestino, $TipoDestino, $observaciones = "", $monto = false){
		
		if( $this->mCuentaIniciada == false ){
			$this->init();
		}
		$cuentaOrigen	= $this->mNumeroCuenta;
		$socio			= $this->mSocioTitular;
		$saldoOrigen	= $this->mSaldoActual;
		
		$fechaOperacion	= fechasys();
		$tipoPago		= TESORERIA_COBRO_NINGUNO;
		$cheque			= "NA";
		$reciboFiscal	= "";
		$tipoDocumento	= 9;
		$msg			= "";		

		if ($TipoDestino == CAPTACION_TIPO_PLAZO ){
			$xCDestino	= new cCuentaInversionPlazoFijo($CuentaDestino, $socio);
		} else {
			$xCDestino	= new cCuentaALaVista($CuentaDestino, $socio);
		}
		$xCDestino->init();
					
		
		if ( $monto > $saldoOrigen ){
			$msg			.= "ERROR\tEl Monto a Retirar $monto es mayor al saldo de Origen $saldoOrigen \r\n";
		} else {
			//Crear el Recibo
			$xRec			= new cReciboDeOperacion($tipoDocumento);
			$ReciboTrasp	= $xRec->setNuevoRecibo($socio, $cuentaOrigen, $fechaOperacion, 1,  $tipoDocumento, $observaciones, $cheque, $tipoPago);
			$xRec->setNumeroDeRecibo($ReciboTrasp);
			
			$xRec->setGenerarPoliza();
			$xRec->setForceUpdateSaldos();
			$xRec->initRecibo();
			$this->setCuentaBancaria(DEFAULT_CUENTA_BANCARIA);
				
			$this->setReciboDeOperacion($ReciboTrasp);
			$this->setForceOperations(true);
			
			$xCDestino->setReciboDeOperacion($ReciboTrasp);
			
			$this->setRetiro($monto, $cheque, $tipoPago, $reciboFiscal, $observaciones, DEFAULT_GRUPO, $fechaOperacion, $ReciboTrasp);
			$xCDestino->setDeposito($monto, $cheque, $tipoPago, $reciboFiscal, $observaciones, DEFAULT_GRUPO, $fechaOperacion, $ReciboTrasp);
			
			$xRec->setFinalizarRecibo(true);
			$msg			.= $xRec->getMessages("txt");
		}
		return $msg;
	}
	function getReciboDeOperacion(){
		return $this->mReciboDeOperacion;
	}
	function getEsOperable($Fecha = false){
		$Fecha					= ( $Fecha == false ) ? fechasys() : $Fecha;
		$EsOperable				= true;
		$msg					= "";
		if( $this->mCuentaIniciada == false ){ $this->init(); }
		//reglas para inversiones
		if ( ( $this->mTipoDeCuenta == CAPTACION_TIPO_PLAZO ) ){
			$msg				.= "ERROR\tRULS\tReglas para cuentas de Inversion\r\n";
			if ( ($this->mFechaVencimiento == $Fecha) ) {
				$EsOperable		=  true;
				$msg			.= "OK\tOPER\tLa Cuenta es Operable por que la Inversion tiene la misma Fecha\r\n";
			} else {
				$EsOperable		=  false;
				$msg			.= "ERROR\tNO.OPER\tLa Cuenta NO es Operable por que la Inversion NO tiene la misma Fecha de vencimiento (" . $this->mFechaVencimiento . ")\r\n";
			}
		}
		if(MODO_DEBUG == true){ $EsOperable = true; }
		$this->mMessages		.= $msg;
		return $EsOperable;
	}	
	function getORec(){ return $this->mORec; }
	function getURLRecibo(){
		
	}
	function isTipoVista(){
		$ret	= false;
		if($this->mTipoDeCuenta == CAPTACION_TIPO_VISTA){ $ret = true; }
		return $ret;
	}
	function getSaldoActual(){ return $this->mSaldoActual; }
	function getTasaActual(){ return $this->mTasaInteres; }
	function getDiasDeCuenta(){ return $this->mDiasInvertidos; }
	function getNombreMancomunados(){ return $this->mNombreMancomunados; }
	function getFechaDeVencimiento(){ return $this->mFechaVencimiento; }
	
}



class cTipoDeCuentaDeCaptacion {
	private $mCodigo			= 99;
	private $aDatos				= array();
	private $mNombre			= "";
	
	function __construct($tipo){
		$this->mCodigo	= $tipo;
		$this->init();
	}
	//
	function init(){
		$sql	= "SELECT idcaptacion_cuentastipos, descripcion_cuentastipos, tipo_cuenta
					    FROM captacion_cuentastipos
					WHERE idcaptacion_cuentastipos=" . $this->mCodigo . " LIMIT 0,1 ";
		$this->aDatos		= obten_filas($sql);
		
		$this->mNombre		= $this->aDatos["descripcion_cuentastipos"];
		
		return $this->aDatos;
	}
	function getDatosInArray(){ return $this->aDatos; }
	function getNombre(){ return $this->mNombre; }
}
class cCaptacionProducto {
	private $mClave	= null;
	private $mObj	= null;
	private $mClase	= null;
	private $mDestinoInt	= null;
	function __construct($clave = false){
		$this->mClave	= $clave;
		$this->mDestinoInt	= CAPTACION_DESTINO_CTA_INTERES;
		if( setNoMenorQueCero($this->mClave) > 0 ){
			$this->init();
		}
	}
	function init(){
		$this->mObj		= new cCaptacion_subproductos();
		$this->mObj->setData( $this->mObj->query()->initByID($this->mClave) );
		$this->mClase	= $this->mObj->tipo_de_cuenta()->v();
		$this->mDestinoInt	= strtoupper($this->mObj->destino_del_interes()->v());
	}
	function getClase(){ return $this->mClase; }
	function getDestinoInteres(){ return $this->mDestinoInt; } 
	function getListaDeDias($events = false){
		$selDias	= "";
		$xLn		= new cLang();
		$dias		= $xLn->get("dias");
	
		if ( CAPTACION_INVERSIONES_POR_DIA == true ){
			$xTxt		= new cHText();
			if(is_array($events)){
				foreach ($events as $evento => $func){
					$xTxt->addEvent($func, $evento);
				}
			}
			$selDias	= $xTxt->getNormal("iddias", INVERSION_DIAS_MINIMOS, "TR.Plazo de Inversion");
				
		} else {
			$xSel		= new cHSelect();
			if(is_array($events)){
				foreach ($events as $evento => $func){
					$xSel->addEvent($func, $evento);
				}
			}
			$xSel->addOptions(array(
					7 => "7 $dias",
					14 => "14 $dias",
					28 => "28 $dias",
					30 => "30 $dias",
					60 => "60 $dias",
					90 => "90 $dias",
					120 => "120 $dias",
					180 => "180 $dias",
					360 => "360 $dias"
						
			));
			$selDias	= $xSel->get("iddias", "TR.Plazo de Inversion", INVERSION_DIAS_MINIMOS);
		}
		return $selDias;
	}	
}
class cInformacionProductoCaptacion {
	private $mCodigo			= 99;
	private $aDatos				= array();

	function __construct($tipo){
		$this->mCodigo	= $tipo;
		$this->init();
	}
	//
	function init(){
		$sql	= "SELECT *
					/*
					idcaptacion_subproductos, descripcion_subproductos, descripcion_completa,
					fecha_alta, fecha_baja, algoritmo_de_premio, algoritmo_de_tasa_incremental,
					tipo_de_cuenta, nombre_del_contrato, contable_movimientos, contable_intereses_por_pagar,
					contable_gastos_por_intereses, contable_cuentas_castigadas, metodo_de_abono_de_interes,
					destino_del_interes, algoritmo_modificador_del_interes
					*/
    				FROM captacion_subproductos
    				WHERE idcaptacion_subproductos = " . $this->mCodigo . " LIMIT 0,1 ";
		$this->aDatos		= obten_filas($sql);
		return $this->aDatos;
	}
}
?>