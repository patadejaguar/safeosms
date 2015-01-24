<?php
/**
 * Core Creditos File
 * @author Balam Gonzalez Luis Humberto
 * @package creditos
 * Core Creditos File
 * 		16/04/2008
 *		31-Mayo-2008.- cCredito
 *		29/dic/2010	 Varios fixes
 */
include_once("core.init.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");
include_once("core.deprecated.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");


include_once("core.common.inc.php");
include_once("core.html.inc.php");

include_once("core.contable.inc.php");
include_once "core.contable.utils.inc.php";

include_once("core.operaciones.inc.php");
include_once("core.creditos.inc.php");
include_once("core.creditos.utils.inc.php");

@include_once("../libs/sql.inc.php");

class cPlanDePagosGenerador {
	private $mPagosAutorizados	= 0;
	private $mDiaDeAbono1		= 0;
	private $mDiaDeAbono2		= 0;
	private $mDiaDeAbono3		= 0;
	private $mPeriocidadDePago	= 0;
	private $mTipoDePlanDePago	= 0;
	private $mSaldoInicial		= 0;
	private $mSaldoFinal		= 0;
	private $mTipoDeProducto	= 0;
	private $mTasaDeIVA			= 0;
	private $mParcialidadPres	= 0;
	private $mLimiteSimulaciones= 10;
	private $mFormulaInteres	= "";
	private $mCapitalInicial	= 0;
	private $mCapitalActual		= 0;
	private $mSaldoHistorico	= 0; 
	private $mFechaPrimerPago	= 0;
	
	
	private $mInteresPagado		= 0;
	private $mCapitalPagado		= 0;
	private $mTipoCreditoSis	= 0;
	private $mClaveDePlan		= 0;
	private $mFechaFail			= "2014-01-01";			//fecha de migracion de campo
	//private $mTipoEnSistema		= false;
	//private $mFechaPrimerPago	= false;
	function __construct($periocidad = false){
		$this->mDiaDeAbono1		= PM_DIA_DE_PAGO;
		switch($periocidad){
			case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
				$this->mDiaDeAbono1	= PQ_DIA_PRIMERA_QUINCENA;
				$this->mDiaDeAbono2	= PQ_DIA_SEGUNDA_QUINCENA;
				break;
			case CREDITO_TIPO_PERIOCIDAD_DECENAL:
				$this->mDiaDeAbono1	= 10;
				$this->mDiaDeAbono2	= 20;				
				$this->mDiaDeAbono3	= 30;
				break;
		}
	}
	function initPorCredito($idcredito, $dataCredito = false){
		$xCred			= new cCredito($idcredito);
		$xCred->init($dataCredito);
		$xF				= new cFecha();
		
		if($xCred->getTipoEnSistema() == CREDITO_PRODUCTO_NOMINA){
			//cargar datos de la empresa
			$idemp				= $xCred->getClaveDeEmpresa();
			$periocidad			= $xCred->getPeriocidadDePago();
			$this->setPeriocidadDePago($periocidad);
			$this->mTipoCreditoSis	=$xCred->getTipoEnSistema();
			
			$this->mClaveDePlan	= setNoMenorQueCero($xCred->getNumeroDePlanDePagos());
			
			if($this->mClaveDePlan > 0 ){
				//TODO: validar fecha de primer pago en calculos automaticos
				$this->mFechaPrimerPago	= $xCred->getFechaPrimeraParc();
			} else {
				$this->mFechaPrimerPago	= $this->getFechaDePago($xCred->getFechaDeMinistracion(), 1);
			}
			if($xCred->getFechaPrimeraParc() == $this->mFechaFail OR $xF->getInt($xCred->getFechaPrimeraParc()) <= $xF->getInt( $xCred->getFechaDeMinistracion() ) ){
				$this->mFechaPrimerPago	= $this->getFechaDePago($xCred->getFechaDeMinistracion(), 1);
			}
			$xEmp				= new cEmpresas($idemp);
			$xEmp->init();
			$DDias				= $xEmp->getDiasDeNomina($periocidad);
			
			
			$this->mDiaDeAbono1	= isset($DDias[0]) ? setNoMenorQueCero($DDias[0]) : $this->mDiaDeAbono1;
			$this->mDiaDeAbono2	= isset($DDias[1]) ? setNoMenorQueCero($DDias[1]) : $this->mDiaDeAbono2;
			$this->mDiaDeAbono3	= isset($DDias[2]) ? setNoMenorQueCero($DDias[2]) : $this->mDiaDeAbono3;
		} else {
			$this->mFechaPrimerPago	= $this->getFechaDePago($xCred->getFechaDeMinistracion(), 1);
		}
	}
	function setPagosAutorizados($pagos = 0){ $this->mPagosAutorizados	= $pagos;	}
	
	function setDiasDeAbonoFijo($dia1, $dia2 = false, $dia3 = false){
		$this->mDiaDeAbono1	= $dia1;
		$this->mDiaDeAbono2	= ($dia2 == false) ? $this->mDiaDeAbono2 : $dia2;
		$this->mDiaDeAbono3	= ($dia3 == false) ? $this->mDiaDeAbono3 : $dia3;
	}
	function getFechaDePrimerPago(){ return $this->mFechaPrimerPago; }
	function getDiaAbono1(){ return $this->mDiaDeAbono1; }
	function getDiaAbono2(){ return $this->mDiaDeAbono2; }
	function getDiaAbono3(){ return $this->mDiaDeAbono3; }
	
	function setTipoDePlanDePago($tipo){ $this->mTipoDePlanDePago	= $tipo; }
	function setPeriocidadDePago($periocidad){ $this->mPeriocidadDePago = $periocidad; }
	function setSaldoInicial($cantidad){ $this->mSaldoInicial = $cantidad; }
	function setTipoDeCreditoEnSistema($TipoEnSistema){ $this->mTipoCreditoSis = $TipoEnSistema; }
	function setSaldoFinal($cantidad){ $this->mSaldoFinal = $cantidad; }
	//$xPlan->getFechaDePago($fecha_de_referencia, $simletras1);
	function getFechaDePago($fecha_de_referencia, $numeral){
		$periocidad_pago		= $this->mPeriocidadDePago;
		$tipo_de_plan			= $this->mTipoDePlanDePago;
		$dia_1_ab				= $this->mDiaDeAbono1;
		$dia_2_ab				= $this->mDiaDeAbono2;
		$dia_3_ab				= $this->mDiaDeAbono3;
	
		$xF						= new cFecha(0, $fecha_de_referencia);
		$xF1					= new cFecha(1);
	
		if($tipo_de_plan == CREDITO_TIPO_DIAS_DE_PAGO_NATURAL ){
			$fecha_de_pago 		= ($numeral == 1) ? $fecha_de_referencia : $xF->setSumarDias($periocidad_pago, $fecha_de_referencia);
		} else {
			//$fecha_de_referencia	= $xF->get();
			if($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_DIARIO){
				//obtener si no es festivo
				//if(!isset($fecha_de_pago)){$fecha_de_pago = $fecha_de_referencia;}
				$fecha_de_pago		= ($numeral == 1) ? $fecha_de_referencia : $xF->setSumarDias(1, $fecha_de_referencia);
				$fecha_de_pago 		= $xF->getDiaHabil($fecha_de_pago);
			} elseif($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_SEMANAL){
				//Obtiene el Dia de Ref + dias del periodo
				if($numeral == 1){								//Si es primer pago, es el dia de abono
					$fecha_de_pago 		= $fecha_de_referencia;
				} else {
					$fecha_de_pago 		= $xF->setSumarDias($periocidad_pago);
					if($this->mDiaDeAbono1 != false ){
						$fecha_de_pago	= $xF->getDiaAbonoSemanal($this->mDiaDeAbono1, $fecha_de_pago);
					}
				}
			} elseif($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_DECENAL){
				$desviacion			= intval($periocidad_pago * 0.4);
				if($numeral == 1){								//Si es primer pago, es el dia de abono
					$fecha_de_pago 	= $fecha_de_referencia;
				} else {
					$fecha_de_pago 		= $xF->setSumarDias($periocidad_pago);
					$fecha_calculada 	= $xF->getDiaAbonoDecenal($this->mDiaDeAbono1, $this->mDiaDeAbono2, $this->mDiaDeAbono3, $fecha_de_pago);
					$fecha_de_pago		= ($tipo_de_plan !=  FALLBACK_CREDITOS_DIAS_DE_PAGO) ? $fecha_calculada : $fecha_de_pago;
				}
			} elseif($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_CATORCENAL){
				//Obtiene el Dia de Ref + dias del periodo
				if($numeral == 1){								//Si es primer pago, es el dia de abono
					$fecha_de_pago 	= $fecha_de_referencia;
				} else {
					$fecha_de_pago 	= $xF->setSumarDias($periocidad_pago);
				}
			} elseif (($periocidad_pago >= CREDITO_TIPO_PERIOCIDAD_QUINCENAL) && ($periocidad_pago < CREDITO_TIPO_PERIOCIDAD_MENSUAL)) {
				//Obtiene el Dia de Ref + dias del periodo
				if($numeral == 1){								//Si es primer pago, es el dia de abono
					$fecha_de_pago 	= $fecha_de_referencia;
				} else {
					$fecha_de_pago 	= $xF->setSumarDias($periocidad_pago);
					if ( $tipo_de_plan != FALLBACK_CREDITOS_DIAS_DE_PAGO){
						$fecha_de_pago 	= $xF->getDiaAbonoQuincenal( $dia_1_ab, $dia_2_ab, $fecha_de_pago);
					}
				}
				// Tratamiento Mensual o mas, si es menor a la 1era Quincena, baja al dia dos, si no sube un mes al dia dos...
			} elseif (($periocidad_pago >= CREDITO_TIPO_PERIOCIDAD_MENSUAL) && ($periocidad_pago< CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO)){
				//Obtiene el Dia de Ref + dias del periodo
				$fecha_de_pago 		= ($numeral == 1) ? $fecha_de_referencia : $xF->setSumarDias($periocidad_pago);
				if ( $tipo_de_plan !=  FALLBACK_CREDITOS_DIAS_DE_PAGO){ $fecha_de_pago = $xF->getDiaDeAbonoMensual($dia_1_ab, $fecha_de_pago); }
			} else {
				// Tratamiento 360 o Semanal
				$fecha_de_pago 		= $xF->setSumarDias($periocidad_pago);
			}
		}
		
		if($this->mTipoCreditoSis != CREDITO_PRODUCTO_NOMINA ){
			$fecha_de_pago 			= $xF->getDiaHabil($fecha_de_pago);
			$fecha_de_pago 			= $xF->getDiaHabil($fecha_de_pago);
			$fecha_de_pago 			= $xF->getDiaHabil($fecha_de_pago);
			$fecha_de_pago 			= $xF->getDiaHabil($fecha_de_pago);
		}
		//if(MODO_DEBUG == true){ $this->mMessages	.= $xF->getMessages(); }
		return $fecha_de_pago;
	}
	function getParcialidadPresumida(){
		$FInteres_normal			= new cFormula("interes_normal");
		
		switch($FormaDePago){
			case CREDITO_TIPO_PAGO_INTERES_COMERCIAL:
				$parcialidad_presumida	= ($saldo_insoluto * ($tasa_interes * $factor_interes) * $PERIOCIDAD_DE_PAGO) / EACP_DIAS_INTERES;
					
				$msgC .= "$socio\t$solicitud\tINTERES COMERCIAL : Interes = $parcialidad_presumida\r\n";
				break;
			case CREDITO_TIPO_PAGO_INTERES_PERIODICO:
				break;
		
			case CREDITO_TIPO_PAGO_PERIODICO:
				for($simulaciones=1;$simulaciones<=10;$simulaciones++){
					$sumar_dias						= 0;
		
					for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
						$saldo_inicial 			= ($i ==1) ? $monto_autorizado : $saldo_final;
						$fecha_de_referencia 	= ($i ==1) ? $fecha_primer_abono : $fecha_de_pago;
						$xPlanGen->setPagosAutorizados($PAGOS_AUTORIZADOS);
						$xPlanGen->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
						$xPlanGen->setTipoDePlanDePago($tipo_de_plan);
						$xPlanGen->setPeriocidadDePago($PERIOCIDAD_DE_PAGO);
						$fecha_de_pago			= $xPlanGen->getFechaDePago($fecha_de_referencia, $i);
						// ------------------------------------ Obtiene la Fecha de Pago ----------------------------------------------
						$dias_normales			= ($i == 1) ?  restarfechas($fecha_de_pago, $fecha_ministracion) : restarfechas($fecha_de_pago, $fecha_de_referencia);
						if(PLAN_DE_PAGOS_PLANO == true){ $dias_normales		= $PERIOCIDAD_DE_PAGO;	}
						$saldo_insoluto         = $saldo_inicial;
							
						eval ( $FInteres_normal->getFormula() );
							
						if($PlanMalo == true){ $interes_normal		= $saldo_insoluto * ( $tasa_interes /12 );	}
							
						$interes_simulado		= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $interes_normal : round($interes_normal, 2);
						$iva_simulado 			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? ($interes_normal * $tasa_iva) : round(($interes_normal * $tasa_iva), 2);
						$parcialidad_simulada	= ($parcialidad_presumida - ($interes_simulado + $iva_simulado)); // + $parcialidad_cargo + $parcialidad_ahorro));
							
						$saldo_final 			= $saldo_inicial - $parcialidad_simulada;
						$sumar_dias            	+=  $dias_normales;
						if(MODO_DEBUG == true){
							$msg				.= ($i == 1) ? "PERSONA\tCREDITO\t[SIMULACION/DE]\tINTERES\tIVA\tCAPITAL\tFI\tFF\r\n" : "";
							$msg				.= "$socio\t$solicitud\t[$simulaciones/$i]\t$interes_simulado\t$iva_simulado\t$parcialidad_simulada\t$fecha_de_referencia\t$fecha_de_pago\r\n";
						}
						$xPlanGen->setSaldoInicial($saldo_inicial);
						$xPlanGen->setSaldoFinal($saldo_final);
							
						if($i == $PAGOS_AUTORIZADOS){
							$desviacion_simulada	= ($saldo_final != 0) ? ($saldo_final / $PAGOS_AUTORIZADOS) : 0;
							//echo "<p class='aviso'>$desviacion_simulada	= ($saldo_final != 0) ? ($saldo_final / $PAGOS_AUTORIZADOS)</p>";
							//Verificar db ser años que dura en credito
							if($sumar_dias > 367){
								$factor_annios			= ($sumar_dias / 365);
								$desviacion_simulada	= ($desviacion_simulada/$factor_annios);
								$msg					.= "DIVIDIR ANIOS $factor_annios por $desviacion_simulada\r\n";
							}
							$desviacion_simulada	= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $desviacion_simulada : round($desviacion_simulada,2);
							$parcialidad_presumida	+=  $desviacion_simulada;
		
							$msg					.= "$socio\t$solicitud\t[$simulaciones/$i]\tLa Parcialidad Presumida es $parcialidad_presumida por $desviacion_simulada Desviados, Saldo Final $saldo_final en dias $sumar_dias\r\n";
						}
					}
				}
				break;
		}		
	}
	function getFactorIVA($incluido = false){
		$factor		= 1;
		if($this->mTasaDeIVA > 0 AND $incluido == true){	$factor	= 1 * (1 / (1 + $this->mTasaDeIVA));		}
		return $factor;
	}	
}

class cCreditoValidador {
	function __construct($proceso){
		
	}
	
}

class cCreditosInteresDevengado{
	private $mCred		= null;
	function __construct(){
		
	}
	function setOCredito(){
		
	}
	function getInteresDevengadoMensual(){
		
	}
	
}

?>