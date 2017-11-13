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


class cCreditosOtrosDatos {
	public $DEPOSITO_BANCO				= "TRANSFERENCIA_BANCO";
	public $DEPOSITO_CTA_BANCARIA		= "TRANSFERENCIA_CTA_BANCARIA";
	public $DEPOSITO_CLABE_BANCARIA		= "TRANSFERENCIA_CLABE";
	public $DEPOSITO_FECHA_VENCE		= "TRANSFERENCIA_FECHA_VENCE";

	public $AML_CON_PROVEEDOR			= "AML_CON_PROVEEDOR";
	public $AML_CON_PROPIETARIO			= "AML_CON_PROPIETARIO";

	private $mTrad						= array();
	function getDatosInArray(){
		$arr	= array();
		$arr[$this->DEPOSITO_BANCO]			= $this->DEPOSITO_BANCO;
		$arr[$this->DEPOSITO_CTA_BANCARIA]	= $this->DEPOSITO_CTA_BANCARIA;
		$arr[$this->DEPOSITO_CLABE_BANCARIA]= $this->DEPOSITO_CLABE_BANCARIA;
		$arr[$this->DEPOSITO_FECHA_VENCE]	= $this->DEPOSITO_FECHA_VENCE;
		$arr[$this->AML_CON_PROVEEDOR]		= $this->AML_CON_PROVEEDOR;
		$arr[$this->AML_CON_PROPIETARIO]	= $this->AML_CON_PROPIETARIO;
		//$arr[$this->]	= $this->;
		//$arr[$this->]	= $this->;
		//$arr[$this->]	= $this->;
		//$arr[$this->]	= $this->;
		//$arr[$this->]	= $this->;
		//$arr[$this->]	= $this->;
		
		//$arr[$this->]	= $this->;
		return $arr;
	}
}
/*Proceso de credito*/
class cCreditosEventos {
	public $SOLICITUD		= "solicitud";
	public $AUTORIZACION	= "autorizacion";
	public $MINISTRACION	= "ministracion";
	public $ORIGINACION		= "originacion";
	public $FORMALIZACION	= "formalizacion";
	public $DESEMBOLSO		= "desembolso";
	
	public $PAGO			= "credito-pago";
	
	function __construct(){
		
	}
}

class cCreditosEtapas {
	public $SOLICITUD		= "solicitud";
	public $AUTORIZACION	= "autorizacion";
	public $MINISTRACION	= "ministracion";
	public $ORIGINACION		= "originacion";
	public $FORMALIZACION	= "formalizacion";
	public $DESEMBOLSO		= "desembolso";
	public $ADMINISTRACION	= "administracion";
	
	
	function __construct(){
		
	}
}

class cPlanDePagosGenerador {
	private $mPersona			= 0;
	private $mCredito			= 0;
	private $mPagosAutorizados	= 0;
	private $mDiaDeAbono1		= 0;
	private $mDiaDeAbono2		= 0;
	private $mDiaDeAbono3		= 0;
	private $mPeriocidadDePago	= 0;
	private $mTipoDePlanDePago	= 0; //Dias en que pagara
	private $mTipoDePagos		= 0;
	private $mTipoBaseInteres	= 2; //Saldo insolutos
	private $mSaldoInicial		= 0;
	private $mSaldoFinal		= 0;
	private $mTipoDeProducto	= 0;
	private $mTasaDeIVA			= 0;
	private $mTasaDeIVAOtros	= 0;
	private $mTasaDeInteres		= null;
	private $mTasaDeAhorro		= null;
	private $mParcialidadPres	= 0;
	private $mLimiteSimulaciones= 12;
	private $mFormulaInteres	= "";
	private $mCapitalInicial	= 0;
	private $mCapitalActual		= 0;
	private $mSaldoHistorico	= 0; 
	
	private $mMontoAutorizado	= 0;
	private $mMontoActual		= 0;
	private $mMontoPagado		= 0;
	private $mBaseDeCalculos	= 0;
	//private $mInteresPagado		= 0;
	private $mFechaMinistracion	= false;
	private $mFechaPrimerPago	= false;
	private $mFechaUltimoPago	= false;
	private $mInteresPagado		= 0;
	private $mCapitalPagado		= 0;
	private $mTipoCreditoSis	= 0;
	private $mClaveDePlan		= 0;
	private $mFechaFail			= "2014-01-01";			//fecha de migracion de campo
	private $mMessages			= "";
	private $mTipoEnSistema		= 0;
	private $mConDias			= false;
	private $mFechaRAW			= false; 
	private $mFechasCalculadas	= array();
	private $mPagosCalculados	= array();
	private $mIDOtrosCargos		= null;
	private $mMontoOtrosCargos	= 0;
	private $mMontoAhorro		= 0;
	private $mMontoBonificacion	= 0;
	private $mAplicar1erPago	= false;
	private $mOCredito			= null;
	private $mPagosSinCapital	= false;
	private $mFactorRedondeo	= 0;
	private $mEsCreditoAfectado	= false;
	private $mMontoUltimo		= 0;
	private $mPagoActual		= 0; 
	private $mPagoCalculado		= 0;
	//private $mClaveDePlan		= 0;
	private $mObservaciones		= "";
	private $mTotalPlan			= 0;
	private $mDiasTolerancia	= 1;
	private $mDiasParaVencer	= 90;
	private $mUltimoPeriodoPag	= 0;
	private $mFechaArbitraria	= false;
	private $mOnError			= false;
	private $mSinIntereses		= false;
	private $mMostrarCompleto	= false;
	private $mGuardar1eraDiff	= false;	//Mostrar el interes del ultimo pago o varianza
	private $mTolerableIncl		= 0.7;		//Porcentaje tolerable para incluir accesorios del ultimo pago
	private $mTotalCalc			= 0;
	private $mSoloTest			= false;
	private $mArrSinIVA			= array();
	private $mCapitalCAT		= 0;
	private $mTasaCAT			= 0;
	private $mMaximoCAT			= 0;
	private $mForceMonto		= false;
	private $mAnticipo			= 0;
	private $mValorResidual		= 0;
	private $mPagosPlanos		= true;
	public $FECHA_v11			= "2016-10-01";
	private $mNoAjusteSig		= false;
	private $mAjusteSobreInt	= false;
	private $mMontoCapAjust		= 0;
	private $mSinDatosE			= false;
	private $mConDiasInhabiles	= false;
	
	//private $mParcialidadPres	= 0;
	//private $mTipoEnSistema		= false;
	//private $mFechaPrimerPago	= false;
	function __construct($periocidad = false){
		$periocidad						= setNoMenorQueCero($periocidad);
		if($periocidad > 0){
			$this->setDiasPorPeriocidad($periocidad);
			$this->mPeriocidadDePago	= $periocidad;
		}
		$this->mTasaDeIVAOtros			= TASA_IVA;
		$this->mTipoDePlanDePago		= 1;//Dias en que Pagara. 1= PAGOS EN DIAS PREESTABLECIDOS
		$this->mPagosPlanos				= PLAN_DE_PAGOS_PLANO;
		if(WORK_IN_SUNDAY == true){
			$this->mConDiasInhabiles	= true;
		}
	}
	private function setDiasPorPeriocidad($periocidad){
		$periocidad				= setNoMenorQueCero($periocidad);
		if($periocidad > 0){
		switch($periocidad){
			case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
				$this->mDiaDeAbono1	= ($this->mDiaDeAbono1	<= 1) ? PQ_DIA_PRIMERA_QUINCENA : $this->mDiaDeAbono1;
				$this->mDiaDeAbono2	= ($this->mDiaDeAbono2	<= 1) ? PQ_DIA_SEGUNDA_QUINCENA : $this->mDiaDeAbono2;
				break;
			case CREDITO_TIPO_PERIOCIDAD_DECENAL:
				$this->mDiaDeAbono1	= ($this->mDiaDeAbono1	<= 1) ? 10 : $this->mDiaDeAbono1;
				$this->mDiaDeAbono2	= ($this->mDiaDeAbono2	<= 1) ? 20 : $this->mDiaDeAbono2;
				$this->mDiaDeAbono3	= ($this->mDiaDeAbono3	<= 1) ? 30 : $this->mDiaDeAbono3;
				break;
			case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
				$this->mDiaDeAbono1	= PS_DIA_DE_PAGO; //Lunes
				break;
			case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
				$this->mDiaDeAbono1	= PS_DIA_DE_PAGO; //Lunes
				break;
			default:
				$this->mDiaDeAbono1		= PM_DIA_DE_PAGO;
				$this->mDiaDeAbono3		= PM_DIA_DE_PAGO;				
				break;
		}
		}
	}
	function initPorCredito($idcredito, $dataCredito = false){
		$xCred						= new cCredito($idcredito); $xCred->init($dataCredito);
		$xF							= new cFecha();
		$xLog						= new cCoreLog();
		$this->mTipoCreditoSis		= $xCred->getTipoEnSistema();
		$this->mTipoDePagos			= $xCred->getTipoDePago();
		$this->mPeriocidadDePago	= $xCred->getPeriocidadDePago();
		$this->mPagosAutorizados	= $xCred->getPagosAutorizados();
		$this->mMontoAutorizado		= $xCred->getMontoAutorizado();
		$this->mCapitalCAT			= $xCred->getMontoAutorizado();
		$this->mMontoActual			= $xCred->getSaldoActual();
		$this->mInteresPagado		= $xCred->getInteresNormalPagado();
		$this->mMontoPagado			= setNoMenorQueCero(($xCred->getMontoAutorizado() - $xCred->getSaldoActual() ), 2);
		$this->mFechaPrimerPago		= $xCred->getFechaPrimeraParc();
		$this->mFechaMinistracion	= $xCred->getFechaDeMinistracion();
		$this->mTipoBaseInteres		= $xCred->getTipoDeCalculoDeInteres();
		$this->mTasaDeInteres		= $xCred->getTasaDeInteres();
		$this->mCredito				= $xCred->getNumeroDeCredito();
		$this->mPersona				= $xCred->getClaveDePersona();
		$this->mTasaDeAhorro		= $xCred->getTasaDeAhorro();
		$this->mTasaDeIVA			= $xCred->getTasaIVA();
		$this->mTipoEnSistema		= $xCred->getTipoEnSistema();
		$this->mTipoDePlanDePago	= $xCred->getTipoDeDiasDePago();
		$this->mEsCreditoAfectado	= $xCred->getEsCreditoYaAfectado();
		$this->setDiasPorPeriocidad($xCred->getPeriocidadDePago());
		$this->mTasaDeIVAOtros		= $xCred->getTasaIVAOtros();
		$this->mPagoActual			= $xCred->getPeriodoActual();
		$this->mClaveDePlan			= $xCred->getNumeroDePlanDePagos();
		//========================= Inicia el producto
		$xPdto						= $xCred->getOProductoDeCredito();
		$xPer						= $xCred->getOPeriocidad();
		$this->mDiasTolerancia		= $xPdto->getDiasTolerados();
		if($xPer == null){
			$this->mDiasParaVencer	= 89;
		} else {
			$this->mDiasParaVencer	= $xPer->getDiasToleradosEnVencer();
		}
		//afectado por Interes Pagado
		$this->mEsCreditoAfectado	= ($xCred->getInteresNormalPagado() > 0 ) ? true : $this->mEsCreditoAfectado;
		if($this->mEsCreditoAfectado == true){
			$this->mBaseDeCalculos	= $xCred->getSaldoActual();
		} else {  
			$this->mBaseDeCalculos	= $xCred->getMontoAutorizado();
		}
		$this->mOCredito			= $xCred;
		$this->mPagosSinCapital		= $xCred->getPagosSinCapital();
		$xLog->add("WARN\tCargando por el Credito $idcredito\r\n");
		if($xCred->getPeriocidadDePago() >= CREDITO_TIPO_PERIOCIDAD_MENSUAL){
			$this->mDiaDeAbono1		= $xF->dia($this->mFechaMinistracion);
			if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_ANUAL){
				$this->mDiaDeAbono1		= $xF->dia($xCred->getFechaPrimeraParc());
			}
		}
		
		if($this->mTipoEnSistema == SYS_PRODUCTO_NOMINA){
			//cargar datos de la empresa
			$idemp				= $xCred->getClaveDeEmpresa();
			$this->mClaveDePlan	= setNoMenorQueCero($xCred->getNumeroDePlanDePagos());
			
			if($this->mClaveDePlan > 0 ){
				//TODO: validar fecha de primer pago en calculos automaticos
				$this->mFechaPrimerPago	= $xCred->getFechaPrimeraParc();
			} else {
				$this->mFechaPrimerPago	= $this->getFechaDePago($xCred->getFechaDeMinistracion(), 0);
			}
			if($xCred->getFechaPrimeraParc() == $this->mFechaFail OR $xF->getInt($xCred->getFechaPrimeraParc()) <= $xF->getInt( $xCred->getFechaDeMinistracion() ) ){
				$this->mFechaPrimerPago	= $this->getFechaDePago($xCred->getFechaDeMinistracion(), 0);
			}
			$DDias						= array();
			$this->mConDiasInhabiles	= true;
			$xEmp						= new cEmpresas($idemp);
			if($xEmp->init() == true AND $idemp != DEFAULT_EMPRESA){ 
				$DDias = $xEmp->getDiasDeNomina($xCred->getPeriocidadDePago());
				$this->mDiaDeAbono1	= isset($DDias[0]) ? setNoMenorQueCero($DDias[0]) : $this->mDiaDeAbono1;
				$this->mDiaDeAbono2	= isset($DDias[1]) ? setNoMenorQueCero($DDias[1]) : $this->mDiaDeAbono2;
				$this->mDiaDeAbono3	= isset($DDias[2]) ? setNoMenorQueCero($DDias[2]) : $this->mDiaDeAbono3;
				$this->setDiasPorPeriocidad($xCred->getPeriocidadDePago());
				$xLog->add("WARN\tCargando dias de abono por Empresa $idemp\r\n");
				$xLog->add($xEmp->getMessages(), $xLog->DEVELOPER);
			}
			
		} else {
			if($xCred->getNumeroDePlanDePagos() > 0){
				$this->mFechaPrimerPago	= $xCred->getFechaDePrimerPago();
			} else {
				$this->mFechaPrimerPago	= $this->getFechaDePago($xCred->getFechaDeMinistracion(), 1);
			}
		}
		if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_CATORCENAL OR $xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_SEMANAL){
			if($xF->getDiaDeLaSemana($this->mFechaPrimerPago) != $this->mDiaDeAbono1){
				$this->mDiaDeAbono1	= $xF->getDiaDeLaSemana($this->mFechaPrimerPago);
			}
		}
		if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_ANUAL OR $xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_MENSUAL){
			$this->mDiaDeAbono1	= $xF->dia($this->mFechaPrimerPago);
		}
		$this->mMessages	.= $xLog->getMessages();
	}
	function OCredito(){ return $this->mOCredito; }
	function setPagosAutorizados($pagos = 0){ $this->mPagosAutorizados	= $pagos;	}
	function setMostrarCompleto($si = false){$this->mMostrarCompleto=$si;}
	function setIgnorarIntsNoPag($si=false){$this->mSinIntereses=$si;}
	function setGuardarPrimeraDiferencia($si=false){$this->mGuardar1eraDiff = $si;}
	function setDiasDeAbonoFijo($dia1, $dia2 = false, $dia3 = false){
		$dia1				= setNoMenorQueCero($dia1);
		$dia2				= setNoMenorQueCero($dia2);
		$dia3				= setNoMenorQueCero($dia3);
		$this->mDiaDeAbono1	= $dia1;
		$this->mDiaDeAbono2	= ($dia2 >0) ? $dia2 : $this->mDiaDeAbono2;
		$this->mDiaDeAbono3	= ($dia3 >0) ? $dia3 : $this->mDiaDeAbono3;
		$this->getTipoDeDiasDePagoPorDias($this->mDiaDeAbono1, $this->mDiaDeAbono2, $this->mDiaDeAbono3);
		//$this->mMessages	.= "WARN\tDias de Abono ($dia1) ($dia2) ($dia3)\r\n";
	}
	function setNoAjustarSiguiente($opt = false){ $this->mNoAjusteSig = $opt; }
	function setAjustarCapital($opt = false){ $this->mAjusteSobreInt = $opt; }
	function getFechaDePrimerPago(){ return $this->mFechaPrimerPago; }
	function getFechaDeUltimoPago(){ return $this->mFechaUltimoPago; }
	function getDiaAbono1(){ return $this->mDiaDeAbono1; }
	function getDiaAbono2(){ return $this->mDiaDeAbono2; }
	function getDiaAbono3(){ return $this->mDiaDeAbono3; }
	function setMontoAutorizado($monto){$this->mMontoAutorizado = $monto;$this->mCapitalCAT=$monto; }
	function setMontoParcialidad($monto){
		$monto			= setNoMenorQueCero($monto);
		if($monto >0){
			$this->mParcialidadPres = $monto - round(($this->mMontoOtrosCargos/ $this->mPagosAutorizados),2);
			$this->mForceMonto 		= true;
		}
	}
	function setMontoActual($monto){$this->mMontoActual = $monto; }
	function setTipoDePlanDePago($tipo){ $this->mTipoDePlanDePago	= $tipo; }
	function setPeriocidadDePago($periocidad){ $this->mPeriocidadDePago = $periocidad; }
	function setTasaDeInteres($tasa){$this->mTasaDeInteres=$tasa;}
	function setTasaDeIVA($tasa){$this->mTasaDeIVA=$tasa;}
	function setSaldoInicial($cantidad){ $this->mSaldoInicial = $cantidad; }
	function setTipoDeCreditoEnSistema($TipoEnSistema){ $this->mTipoCreditoSis = $TipoEnSistema; }
	function setSaldoFinal($cantidad){ $this->mSaldoFinal = $cantidad; }
	function setTipoDePago($tipo){ $this->mTipoDePagos	= $tipo; }
	function setFechaArbitraria($fecha){ $xF	 = new cFecha(); $this->mFechaArbitraria = $xF->getFechaISO($fecha); }
	function setFechaDesembolso($fecha){$xF	 = new cFecha(); $this->mFechaMinistracion = $xF->getFechaISO($fecha);	}	
	//$xPlan->getFechaDePago($fecha_de_referencia, $simletras1);
	private function getDiasSumados($dias){
		$dias	= floor(($dias * 0.75));
		return $dias;
	}
	private function setComprobarDiasDeAbono($periocidad, $dia1, $dia2 = false, $dia3 = false){
		switch ($periocidad_pago){
			
		}
		
	}
	function setPrecalcularAnnios($fecha = false){
		for($periodo = 1; $periodo <= $this->mPagosAutorizados; $periodo++){
			$xF					= new cFecha();
			$anno				= $xF->anno($fecha)+1;
			if($periodo == 1){
				if($this->mFechaArbitraria != false ){
					$fecha	= $this->mFechaArbitraria;
					$anno	= $xF->anno($fecha);
				}
			}
			
			$mes		= $xF->mes($fecha);
			$dia		= $xF->dia($fecha);
			$fecha		= $xF->getFechaISO("$anno-$mes-$dia");				
			
			$arrFecha[$periodo]	= $fecha;
		}
		$this->mFechasCalculadas	= $arrFecha;
	}
	function setPrecalcularMeses($mFechaInicial, $mDia1 = false){
		$xF					= new cFecha();
		$xLog				= new cCoreLog();
		$fecha_inicial		= ($mFechaInicial == false) ? $xF->getFechaISO($this->mFechaMinistracion) : $xF->getFechaISO( $mFechaInicial);
		$mDia1				= (setNoMenorQueCero($mDia1) <= 0) ? $this->mDiaDeAbono1 : $mDia1;
		$fecha				= $fecha_inicial;
		$proximo			= null;
		$arrFecha			= array();
		$desvio				= 14;
		for($periodo = 1; $periodo <= $this->mPagosAutorizados; $periodo++){
			$dia			= ($xF->getDiasDelMes($fecha) < $mDia1) ? $xF->getDiasDelMes($fecha) : $mDia1;
			$anno			= $xF->anno($fecha);
			$mes			= $xF->mes($fecha);			
			if($periodo == 1){
				$anno		= $xF->anno($fecha);
				$mes		= $xF->mes($fecha);				
				if($this->mFechaArbitraria != false ){
					$fecha	= $this->mFechaArbitraria;
				} else {
					//si la fecha 10/abr  + 15 25Abr 
					if($xF->getInt($xF->setSumarDias($desvio, $fecha)) < $xF->getInt("$anno-$mes-$dia") ){
						$fecha		= "$anno-$mes-$dia";
					} else {
						$fecha	= $xF->setSumarMeses(1, "$anno-$mes-$dia");
					}
				}
			} else {
				if($mes == 1){
					if($xF->getDiasDelMes("$anno-02-01")<$dia){
						$dia	= $xF->getDiasDelMes("$anno-02-01");
					}
				}
				$fecha	= $xF->setSumarMeses(1, "$anno-$mes-$dia");
				
				//setLog("FECH-$periodo $anno-$mes-$dia    ---- $fecha");
			}
			$arrFecha[$periodo]	= $fecha;
		}
		$this->mFechasCalculadas	= $arrFecha;
	}
	function setPrecalcularQuincenas($mFechaInicial = false, $mDia1 = false, $mDia2 = false){
		$xF					= new cFecha();
		$xLog				= new cCoreLog();
				
		$fecha_inicial		= ($mFechaInicial == false) ? $xF->getFechaISO($this->mFechaMinistracion) : $xF->getFechaISO( $mFechaInicial);
		$mDia1				= (setNoMenorQueCero($mDia1) <= 0) ? $this->mDiaDeAbono1 : $mDia1;
		$mDia2				= (setNoMenorQueCero($mDia2) <= 0) ? $this->mDiaDeAbono2 : $mDia2;
		
		$arrFecha			= array();
		
		$arrQuincenas		= array($mDia1, $mDia2);
		sort($arrQuincenas);
		$primer_dia			= setNoMenorQueCero($arrQuincenas[0]);
		$segundo_dia		= setNoMenorQueCero($arrQuincenas[1]);
		//setLog("primer $primer_dia Segundo $segundo_dia");
		$primer_dia			= ($primer_dia <= 0) ? PQ_DIA_PRIMERA_QUINCENA : $primer_dia;
		$segundo_dia		= ($segundo_dia <= 0) ? PQ_DIA_SEGUNDA_QUINCENA : $segundo_dia;
		//================== Validar Primer Error del dia
		$xLog->add("=PERIODO\t=FECHA\t=FECHA1\t=FECHA2\t=NOTAS\r\n", $xLog->DEVELOPER);
		//$xLog->add("<tr><th>PERIODO</th><th>FECHA</th><th>=FECHA1</th><th>=FECHA2</th><th>=NOTAS</th></tr>", $xLog->DEVELOPER);
		//calculo primer pago
		$fecha				= $fecha_inicial;
		$proximo			= null;
		$desvio				= 8;//floor( ($dia2 - $primer_dia)/2 );
		for($periodo = 1; $periodo <= $this->mPagosAutorizados; $periodo++){
			$nota					= "";
			$dia2					= $segundo_dia; 
			$nota					.= "Dias sumados en Desvio $desvio- ";
			$dias_sumados			= $this->mPeriocidadDePago;
			$mes					= $xF->mes($fecha);
			if($periodo == 1){ //irregular
				$fecha					= ($this->mFechaArbitraria == false) ? $xF->setSumarDias($desvio, $fecha) : $this->mFechaArbitraria;
				
				$anno					= $xF->anno($fecha);
				$mes					= $xF->mes($fecha);
				$xDia					= ($xF->getDiasDelMes($fecha) < $dia2) ? $xF->getDiasDelMes($fecha) : $dia2;
				$fecha1					= "$anno-$mes-$primer_dia";
				$fecha2					= "$anno-$mes-$xDia";
				if($this->mFechaArbitraria != false ){
					$fecha				= $this->mFechaArbitraria;
					
					if($xF->getInt($this->mFechaArbitraria) > $xF->getInt($fecha1)){
						$proximo			= $primer_dia;
						//setLog("$fecha1 ignorar  de Fecha a " . $this->mFechaArbitraria. " Proximo $proximo");
					} else {
						$proximo			= $dia2;
					}
					
				} else {		
					if($xF->getInt($fecha) > $xF->getInt($fecha1)){
						$nota				.= "$fecha es mayor a $fecha1 se establece $fecha2";
						$fecha				= $fecha2;
						$proximo			= $primer_dia;
					} else {
						$nota				.= "$fecha es menor o Igual a $fecha1";
						$fecha				= $fecha1;
						$proximo			= $dia2;
					}
				}
				$fecha1					= $fecha_inicial;
				$arrFecha[$periodo]		= $fecha;
			} else {
				$fecha1					= $fecha;
				
				$fecha					= $xF->setSumarDias($this->mPeriocidadDePago, $fecha);
				//setLog("$periodo -- $fecha ---");
				$FRaw					= $xF->setSumarDias($this->mPeriocidadDePago, $fecha);
				$desviar				= $desvio;
				

				
				$nota					.= "Proximo $proximo - Dia 2 = $dia2";
				if($proximo == $dia2){
					
					$fecha				= $xF->setRestarDias($desviar, $fecha);
					$xDia				= ($xF->getDiasDelMes($fecha) < $dia2) ? $xF->getDiasDelMes($fecha) : $dia2;
					$fecha				= $xF->anno($fecha) . "-" . $xF->mes($fecha) . "-$xDia";
					$proximo			= $primer_dia;
					$fecha2				= $xF->anno($FRaw) . "-" . $xF->mes($FRaw) . "-$primer_dia";
				} else {
					$mmes				= $xF->mes($fecha);
					$manno				= 0;
					if(($proximo < $dia2) AND ($xF->dia($fecha) > $dia2)){ //Correcion Aparentemente bien :'( Voy a llorar
						$mmes 			= $mmes + 1;
						if($mmes > 12){
							$mmes		= 1;
							$manno		= 1;
						}
					}					
					$fecha				= ($xF->anno($fecha)+$manno) . "-" . $mmes . "-$primer_dia";
					$proximo			= $dia2;
					$fecha2				= $xF->anno($FRaw) . "-" . $xF->mes($FRaw) . "-$dia2";
				}
				if($xF->getInt($fecha) <$xF->getInt($fecha1)){
					setLog("Error en el periodo $periodo $fecha|$fecha1");
				}
				$fecha					= $xF->getFechaISO($fecha);
				$arrFecha[$periodo]		= $fecha;
			}
			//setLog("$periodo\t$fecha\t$fecha1\t$fecha2\t$nota");
			//setLog($nota);
			$xLog->add("$periodo\t$fecha\t$fecha1\t$fecha2\t$nota\r\n", $xLog->DEVELOPER);
			//$xLog->add("<tr><td>$periodo</td><td>$fecha</td><td>$fecha1</td><td>$fecha2</td><td>$nota</td></tr>", $xLog->DEVELOPER);
		}
		$this->mMessages				.= $xLog->getMessages();
		$this->mFechasCalculadas		= $arrFecha;
		
		return $xLog->getMessages();
	}
	function getFechaDePago($fecha_de_referencia = false, $numeral = false){
		$numeral				= setNoMenorQueCero($numeral);
		$periocidad_pago		= $this->mPeriocidadDePago;
		$tipo_de_plan			= $this->mTipoDePlanDePago;
		$dia_1_ab				= $this->mDiaDeAbono1;
		$dia_2_ab				= $this->mDiaDeAbono2;
		$dia_3_ab				= $this->mDiaDeAbono3;
	
		$xF						= new cFecha(0, $fecha_de_referencia);
		$xF1					= new cFecha(1);
		$this->mFechaRAW		= $xF->setSumarDias($periocidad_pago, $fecha_de_referencia);
		if($tipo_de_plan == CREDITO_TIPO_DIAS_DE_PAGO_NATURAL ){
			$fecha_de_pago 		= ($numeral == 1) ? $fecha_de_referencia : $xF->setSumarDias($periocidad_pago, $fecha_de_referencia);
		} else {
			//$fecha_de_referencia	= $xF->get();
			if($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_DIARIO){
				$fecha_de_pago						= ($numeral == 1) ? $fecha_de_referencia : $xF->setSumarDias(1, $fecha_de_referencia);
				$fecha_de_pago 						= $xF->getDiaHabil($fecha_de_pago);
				$fecha_de_pago 						= $xF->getDiaHabil($fecha_de_pago);
				$fecha_de_pago 						= $xF->getDiaHabil($fecha_de_pago);
				$this->mFechasCalculadas[$numeral]	= $fecha_de_pago;
			} elseif($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_SEMANAL){
				//Obtiene el Dia de Ref + dias del periodo
				if($numeral == 1){							//Si es primer pago, es el dia de abono
					//buscar proximo lunes
					if ( $tipo_de_plan != FALLBACK_CREDITOS_DIAS_DE_PAGO){
						if($this->mFechaArbitraria != false){
							$fecha				= $this->mFechaArbitraria;
						} else {
							$fecha				= $xF->setSumarDias(4, $this->mFechaMinistracion);
						}
						$fecha				= $xF->getDiaAbonoSemanal($this->mDiaDeAbono1, $fecha);
						$fecha_de_pago		= $fecha;
					} else {
						$fecha_de_pago		= $xF->setSumarDias($periocidad_pago, $this->mFechaMinistracion);
					}
				} else {
					$fecha_de_pago			= $xF->setSumarDias($periocidad_pago, $fecha_de_referencia);
				}
				if($numeral > 0){ $this->mFechasCalculadas[$numeral]	= $fecha_de_pago; }
				
			} elseif($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_DECENAL){
				if($numeral == 1){								//Si es primer pago, es el dia de abono
					if ( $tipo_de_plan != FALLBACK_CREDITOS_DIAS_DE_PAGO){
						if($this->mFechaArbitraria != false){
							$fecha				= $this->mFechaArbitraria;
						} else {						
							$fecha				= $xF->setSumarDias(4, $this->mFechaMinistracion);
						}
						$fecha			 	= $xF->getDiaAbonoDecenal($this->mDiaDeAbono1, $this->mDiaDeAbono2, $this->mDiaDeAbono3, $fecha);
						$fecha_de_pago 		= $fecha;
						
					} else {
						$fecha_de_pago		= $xF->setSumarDias($periocidad_pago, $this->mFechaMinistracion);
					}
				} else {
					$fecha_de_pago 		= $xF->setSumarDias($periocidad_pago);
					$fecha_calculada 	= $xF->getDiaAbonoDecenal($this->mDiaDeAbono1, $this->mDiaDeAbono2, $this->mDiaDeAbono3, $fecha_de_pago);
					$fecha_de_pago		= ($tipo_de_plan !=  FALLBACK_CREDITOS_DIAS_DE_PAGO) ? $fecha_calculada : $fecha_de_pago;
				}
				if($numeral > 0){ $this->mFechasCalculadas[$numeral]	= $fecha_de_pago; }
				
			} elseif($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_CATORCENAL){
				//Obtiene el Dia de Ref + dias del periodo
				
				if($numeral == 1){
					//buscar proximo lunes
					if ( $tipo_de_plan != FALLBACK_CREDITOS_DIAS_DE_PAGO){
						if($this->mFechaArbitraria != false){
							$fecha				= $this->mFechaArbitraria;
						} else {
							$fecha				= $xF->setSumarDias(7, $this->mFechaMinistracion);
							$fecha				= $xF->getDiaAbonoSemanal($this->mDiaDeAbono1, $fecha);
						}						
						$fecha_de_pago		= $fecha;
					} else {
						$fecha_de_pago	= $xF->setSumarDias($periocidad_pago, $this->mFechaMinistracion);
					}
				} else {
					$fecha_de_pago		= $xF->setSumarDias($periocidad_pago, $fecha_de_referencia);
				}
				if($numeral > 0){ $this->mFechasCalculadas[$numeral]	= $fecha_de_pago; }
			} elseif (($periocidad_pago >= CREDITO_TIPO_PERIOCIDAD_QUINCENAL) && ($periocidad_pago < CREDITO_TIPO_PERIOCIDAD_MENSUAL)) {
				//Obtiene el Dia de Ref + dias del periodo
				if(isset($this->mFechasCalculadas[$numeral])){
					$fecha_de_pago			= $this->mFechasCalculadas[$numeral];
				} else {
					if($numeral == 1){								//Si es primer pago, es el dia de abono
						$fecha_de_pago 		= $fecha_de_referencia;
					} else {
						$fecha_de_pago 		= $xF->setSumarDias( $periocidad_pago, $fecha_de_referencia );
						if ( $tipo_de_plan != FALLBACK_CREDITOS_DIAS_DE_PAGO){
							$fecha_de_pago 	= $xF->getDiaAbonoQuincenal( $dia_1_ab, $dia_2_ab, $fecha_de_pago);
						}
					}
					if($numeral > 0){ $this->mFechasCalculadas[$numeral]	= $fecha_de_pago; }
				}
				// Tratamiento Mensual o mas, si es menor a la 1era Quincena, baja al dia dos, si no sube un mes al dia dos...
			} elseif (($periocidad_pago >= CREDITO_TIPO_PERIOCIDAD_MENSUAL) && ($periocidad_pago< CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO)){
				if(isset($this->mFechasCalculadas[$numeral])){
					$fecha_de_pago			= $this->mFechasCalculadas[$numeral];
				} else {
					//Obtiene el Dia de Ref + dias del periodo
					$fecha_de_pago 			= ($numeral == 1) ? $fecha_de_referencia : $xF->setSumarDias($periocidad_pago);
					if($numeral == 0 AND $periocidad_pago == CREDITO_TIPO_PERIOCIDAD_MENSUAL){
						$fecha_de_pago		= $xF->setSumarDias(($periocidad_pago-3), $this->mFechaMinistracion);
						//setLog($fecha_de_pago);
					}
					if ( $tipo_de_plan !=  FALLBACK_CREDITOS_DIAS_DE_PAGO){ $fecha_de_pago = $xF->getDiaDeAbonoMensual($dia_1_ab, $fecha_de_pago); }
					if($numeral > 0){ 
						$this->mFechasCalculadas[$numeral]	= $fecha_de_pago;
					}
				}
			} else {
				// Tratamiento 360 o Semanal
				$fecha_de_pago 		= $xF->setSumarDias($periocidad_pago);
			}
		}
		

		return $fecha_de_pago;
	}
	function getFechaRAW(){ return $this->mFechaRAW; }
	function setNoMostrarExtras(){ $this->mSinDatosE = true; }
	function getParcialidadPresumida($redondeo = false, $tipo_extra = 0, $monto_extra = 0, $AplicarEnPrimerPago = false){
		$xRuls		= new cReglaDeNegocio();
		$SinOtros	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_OTROS);	//regla de negocio
		$SinAnual	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_ANUAL);	//regla de negocio
		$ConPagEs	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_CON_PAGESP);	//regla de negocio
		$ConTasa0	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PUEDEN_TASA_CERO);	//regla de negocio
		
		$FInteres_normal			= new cFormula("interes_normal");
		$xF							= new cFecha();
		$xLog						= new cCoreLog();
		$FormaDePago				= $this->mTipoDePagos;
		$PAGOS_AUTORIZADOS			= $this->mPagosAutorizados;
		$monto_autorizado			= $this->mMontoAutorizado;
		$PERIOCIDAD_DE_PAGO			= $this->mPeriocidadDePago;
		$fecha_primer_abono			= $this->mFechaPrimerPago;
		$fecha_ministracion			= $this->mFechaMinistracion;
		$dia_1_ab					= $this->mDiaDeAbono1;
		$dia_2_ab					= $this->mDiaDeAbono2;
		$dia_3_ab					= $this->mDiaDeAbono3;
		$tasa_iva					= $this->mTasaDeIVA;
		$socio						= $this->mPersona;
		$solicitud					= $this->mCredito;
		$tipo_de_plan				= $this->getTipoDeDiasDePagoPorDias($dia_1_ab, $dia_2_ab, $dia_3_ab);// $this->mTipoDePlanDePago;
		$tasa_ahorro				= $this->mTasaDeAhorro;
		$factor_interes				= 1;
		$PlanMalo					= false;
		$parcialidad_presumida		= 0;
		$tipo_de_calculo			= $this->mTipoBaseInteres;
		$tasa_interes				= $this->mTasaDeInteres;
		$this->mFactorRedondeo		= $redondeo;
		$this->mIDOtrosCargos		= setNoMenorQueCero($tipo_extra);
		$this->mMontoOtrosCargos	= $monto_extra;
		$this->mAplicar1erPago		= $AplicarEnPrimerPago;
		$saldo_historico			= $this->mMontoAutorizado;
		$bonificaciones				= 0;
		$xMath						= new cMath();
		//==================================================== BONIFICACIONES ================================================================
		$xB							= new cBases(7022); //base son bonificaciones
		$xB->init();
		if( $xB->getIsMember($tipo_extra) == true){
			$xLog->add("$socio\t$solicitud\tLa operacion $tipo_extra es de Bonificaciones\r\n");
			$this->mMontoBonificacion= $monto_extra;
			$monto_extra			= 0;
			$this->mMontoOtrosCargos= 0;
		}
		//==================================================== BASE IVA OTROS ================================================================
		$xB							= new cBases(BASE_IVA_OTROS);
		$xB->init();
		if($xB->getIsMember($this->mIDOtrosCargos) == false){
			$this->mTasaDeIVAOtros	= 0;
		}		
		//=====================================================================================================================================
		$total_ahorro				= ($monto_autorizado * $tasa_ahorro);
		$this->mMontoAhorro			= $total_ahorro;
		$parcialidad_capital 		= ($FormaDePago == CREDITO_TIPO_PAGO_INTERES_PERIODICO OR $FormaDePago == CREDITO_TIPO_PAGO_INTERES_COMERCIAL) ? 0 : ($monto_autorizado / $PAGOS_AUTORIZADOS);
		$parcialidad_ahorro 		= round( ($total_ahorro / $PAGOS_AUTORIZADOS), 2);
		$parcialidad_interes		= 0;
		$parcialidad_iva			= 0;
		$parcialidad_cargo			= setNoMenorQueCero( ($monto_extra / $PAGOS_AUTORIZADOS), 2);
		$parcialidad_cargo_iva		= setNoMenorQueCero(($parcialidad_cargo * $this->mTasaDeIVAOtros),2);
		$saldo_inicial 				= 0;
		$saldo_final 				= 0;
		$interes_normal 	 	   	= 0;
		$interes_iva		  	 	= 0;
		//================== Mensajes
		$txtInicial					= "";
		$txtNotas					= "";
		$txtNotas					.= "=============================\r\n";
		$txtNotas					.= "====\tMonto Original : $monto_autorizado \r\n";
		$txtNotas					.= "====\tTasa Interes : $tasa_interes \r\n";
		$txtNotas					.= "====\tTasa IVA : $tasa_iva \r\n";
		$txtNotas					.= "====\tIDOtros : $tipo_extra \r\n";
		$txtNotas					.= "====\tMonto Otros : $monto_extra \r\n";
		$txtNotas					.= "=============================\r\n";
		
		//*************************************************************************************************************************************
		//-----------------------------------------     PRIMER PAGO.- Cargos Extras    -------------------------------------------------------
		//*************************************************************************************************************************************
		$monto_otros				= $monto_extra;
		if($AplicarEnPrimerPago == true){
			$parcialidad_cargo		= 0;
			$parcialidad_cargo_iva	= 0;
			$monto_otros			= 0;
			$xLog->add("$socio\t$solicitud\tAPLICAR PRIMER PAGO EXTRA $monto_extra \r\n", $xLog->DEVELOPER);
		}
		//*************************************************************************************************************************************
		//-----------------------------------------     CALCULO DE UNA PARCIALIDAD PRESUMIDA    -----------------------------------------------
		//*************************************************************************************************************************************
		if($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_QUINCENAL){
			$this->setPrecalcularQuincenas($this->mFechaMinistracion, $dia_1_ab, $dia_2_ab);
		}
		if($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_MENSUAL){
			$this->setPrecalcularMeses($this->mFechaMinistracion, $dia_1_ab);
		}
		if($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_ANUAL){
			$this->setPrecalcularAnnios($this->mFechaMinistracion);
		}
		$suma_de_pagos				= 0;
		$saldo_insoluto				= $monto_autorizado;
		$dias_estimados             = 0;
		$estimado_periodico_interes	= 0;
		$fecha_de_pago				= $fecha_primer_abono;
		//PAGO NORMALES
		for ($simletras1=1; $simletras1 <= $PAGOS_AUTORIZADOS; $simletras1++){
			$fecha_de_referencia		= ($simletras1 == 1) ? $fecha_primer_abono : $fecha_de_pago;
			$saldo_final 				= $saldo_inicial - $parcialidad_capital;
			$this->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
			$this->setSaldoInicial($saldo_inicial);
			$this->setSaldoFinal($saldo_final);
			$fecha_de_pago				= $this->getFechaDePago($fecha_de_referencia, $simletras1);
		}
		//================================= TASA DE INTERES CERO
		
		if($tasa_interes <= 0){
			if($ConTasa0 == true){
				$parcialidad_presumida			= round(($monto_autorizado / $PAGOS_AUTORIZADOS),2);
				
				if($ConPagEs == true){
					$MONTO_REM		= 0;
					$PAGO_REM		= 0;
					for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
						$idxcap		= $this->mCredito ."-$i-" . OPERACION_CLAVE_PAGO_CAPITAL;
						$pagoesp	= (isset($_SESSION[$idxcap])) ? $_SESSION[$idxcap] : 0;
						if($pagoesp>0){
							$PAGO_REM++;
							$MONTO_REM+=$pagoesp;
							//setLog("$MONTO_REM+=$pagoesp");
						}
					}
					$parcialidad_presumida		= round((($monto_autorizado-$MONTO_REM) / ($PAGOS_AUTORIZADOS-$PAGO_REM)),2);
				}
			}
		} else {
			$dias_estimados		    		= $xF->setRestarFechas($fecha_de_pago, $fecha_ministracion);
			$dias_desviados					= $dias_estimados - ( $PAGOS_AUTORIZADOS * $PERIOCIDAD_DE_PAGO );
			$desviacion_total				= 1 + ( ($dias_desviados / ( $PAGOS_AUTORIZADOS * $PERIOCIDAD_DE_PAGO ) ) / 10 );
			$desviacion                    	= ( $tipo_de_plan != 99 ) ? 0.013 - ( 0.00013 * $PAGOS_AUTORIZADOS ) : 0;
			$estimado_dias_promedio			= ( ( $dias_estimados / $PAGOS_AUTORIZADOS ) * (1 + $desviacion)  );
					
			if ( $tipo_de_calculo == INTERES_POR_SALDO_HISTORICO ){
				$estimado_periodico_interes = ( ($monto_autorizado * $tasa_interes * $dias_estimados ) /  EACP_DIAS_INTERES ) * (1 +  $tasa_iva);
				$parcialidad_presumida      = ( ($monto_autorizado + $total_ahorro + $monto_otros + $estimado_periodico_interes)  / $PAGOS_AUTORIZADOS);
			
			} else {
				//Recompocision para el tipo de Pago sobre Saldos Insolutos
				$estimado_periodico_interes = ( ( $tasa_interes /  EACP_DIAS_INTERES ) * $estimado_dias_promedio ) * (1 +  $tasa_iva);
				$parcialidad_presumida      = ( ( $monto_autorizado * $estimado_periodico_interes ) / ( 1 - ( pow( (1 + $estimado_periodico_interes), ($PAGOS_AUTORIZADOS * -1) ) ) ) ) + ( ($total_ahorro + $monto_otros) / $PAGOS_AUTORIZADOS);
			}
			$parcialidad_presumida			= ($parcialidad_presumida * $desviacion_total);
			$parcialidad_presumida			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $parcialidad_presumida : round($parcialidad_presumida, 2);
					
			switch($FormaDePago){
				case CREDITO_TIPO_PAGO_CAPITAL_FIJO:
					$saldo_final			= 0;
					$saldo_inicial			= $monto_autorizado;
					$this->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
					$sumar_dias				= 0;
					$scap					= 0;
					$capital_pactado		= round(($this->mMontoAutorizado / $this->mPagosAutorizados),2);
					
					for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
						$letra					= $i;
						$saldo_inicial 			= ($i ==1) ? $monto_autorizado : $saldo_final;
						$fecha_de_referencia 	= ($i ==1) ? $fecha_primer_abono : $fecha_de_pago;
						$fecha_de_pago			= $this->getFechaDePago($fecha_de_referencia, $i);
						// ------------------------------------ Obtiene la Fecha de Pago ----------------------------------------------
						$dias_normales			= ($i == 1) ?  restarfechas($fecha_de_pago, $fecha_ministracion) : restarfechas($fecha_de_pago, $fecha_de_referencia);
						
						if(PLAN_DE_PAGOS_PLANO == true){ $dias_normales		= $PERIOCIDAD_DE_PAGO;	}
						$saldo_insoluto         = $saldo_inicial;
						eval ( $FInteres_normal->getFormula() );
						if($interes_normal < 0){
							$interes_normal		= 0;//FIXX
						}								
						$interes_simulado		= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $interes_normal : round($interes_normal, 2);
						$iva_simulado 			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? ($interes_normal * $tasa_iva) : round(($interes_normal * $tasa_iva), 2);
						$parcialidad_simulada	= ($parcialidad_presumida - ($interes_simulado + $iva_simulado));
						$saldo_final 			= round(($saldo_inicial - $capital_pactado),2);
						$sumar_dias            	+=  $dias_normales;
						if($i == 1){
							$xLog->add("PERSONA\tCREDITO\t[SIM - DE]\tINTERES\tIVA\tCAPITAL\tTOTAL\tTCAP\tFECHA-I\tFECHA-F\tSALDO-I\tSALDO-F\r\n", $xLog->DEVELOPER);
						}
						$tt						= $interes_simulado + $iva_simulado + $capital_pactado;
						$scap					+= $capital_pactado;
						$xLog->add("$socio\t$solicitud\t[$i]\t$interes_simulado\t$iva_simulado\t$capital_pactado\t$tt\t$scap\t$fecha_de_referencia\t$fecha_de_pago\t$saldo_inicial\t$saldo_final\r\n", $xLog->DEVELOPER);
						$this->setSaldoInicial($saldo_inicial);
						$this->setSaldoFinal($saldo_final);								

						$this->mPagosCalculados[$letra][SYS_INTERES_NORMAL]	= $interes_simulado;
						$this->mPagosCalculados[$letra][SYS_FECHA]			= $fecha_de_pago;
							
					}					
					break;
				case CREDITO_TIPO_PAGO_INTERES_COMERCIAL:
					$parcialidad_presumida		= ($saldo_insoluto * ($tasa_interes * $factor_interes) * $PERIOCIDAD_DE_PAGO) / EACP_DIAS_INTERES;
					//setLog("$saldo_insoluto * ($tasa_interes * $factor_interes) * $PERIOCIDAD_DE_PAGO");
					$txtNotas 					.= "$socio\t$solicitud\tINTERES COMERCIAL : Interes = $parcialidad_presumida\r\n";
					$this->mPagosSinCapital		= true;
					break;
				case CREDITO_TIPO_PAGO_INTERES_PERIODICO:
					//$this->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
					$this->mPagosSinCapital	= true;
					for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
						
					}
					break;
				case CREDITO_TIPO_PAGO_FLAT_PARCIAL:
					$FInteres_flat			= new cFormula("");
					$FInteres_flat->init($FInteres_flat->PHP_INT_FLAT_MOD);
					$dias_normales			= $PERIOCIDAD_DE_PAGO;
					$interes_simulado		= ($monto_autorizado * ($tasa_interes*$factor_interes) * $dias_normales) /EACP_DIAS_INTERES;
					$iva_simulado			= ($interes_simulado*$tasa_iva);
					$parcialidad_presumida	= round((($monto_autorizado/$PAGOS_AUTORIZADOS)+($interes_simulado)+($iva_simulado)),2);
					//====================== PAGOS ESPECIALES
					if($ConPagEs == true){
						$MONTO_REM			= 0;
						$PAGO_REM			= 0;
						for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
							$idxcap			= $this->mCredito ."-$i-" . OPERACION_CLAVE_PAGO_CAPITAL;
							$pagoesp		= (isset($_SESSION[$idxcap])) ? $_SESSION[$idxcap] : 0;
							if($pagoesp>0){
								$PAGO_REM++;
								$MONTO_REM	+=$pagoesp;
								//setLog("$MONTO_REM+=$pagoesp");
							}
						}
						$parcialidad_presumida		= round(((($monto_autorizado-$MONTO_REM) / ($PAGOS_AUTORIZADOS-$PAGO_REM)+($interes_simulado)+($iva_simulado))),2);
					}
					$saldo_final			= 0;
					$saldo_inicial			= $monto_autorizado;
					$this->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
					
					for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
						$letra					= $i;
						$saldo_inicial 			= ($i ==1) ? $monto_autorizado : $saldo_final;
						$fecha_de_referencia 	= ($i ==1) ? $fecha_primer_abono : $fecha_de_pago;
						$fecha_de_pago			= $this->getFechaDePago($fecha_de_referencia, $i);
						$this->mPagosCalculados[$letra][SYS_INTERES_NORMAL]	= $interes_simulado;
						$this->mPagosCalculados[$letra][SYS_FECHA]			= $fecha_de_pago;
									
					}				
					
					break;
				case CREDITO_TIPO_PAGO_PERIODICO:
					
					$numero_sim						= $this->mLimiteSimulaciones;
					if($tasa_interes>1 OR $this->mMontoAutorizado>10000){
						$numero_sim					= $numero_sim+6;
					}
					if($PAGOS_AUTORIZADOS>100){
						$numero_sim					= $numero_sim+6;
					}
					//Tasa de Interes Integradas
					$TI								= $this->mTasaDeInteres+($this->mTasaDeInteres*$this->mTasaDeIVA);
					if($this->mValorResidual >= 1){
						$this->mParcialidadPres		= $xMath->getPagoLease($TI, $this->mPagosAutorizados, $monto_autorizado, $this->mPeriocidadDePago, $this->mValorResidual);
						$parcialidad_presumida		= $this->mParcialidadPres ;
						$this->mForceMonto			= true;
					}
					if($this->mForceMonto == true AND $this->mParcialidadPres >0){
						$parcialidad_presumida		= $this->mParcialidadPres;
						$numero_sim					= 1;
						
					} else {
						if($xF->getInt($this->mFechaMinistracion) > $xF->getInt($this->FECHA_v11)){
							$parcialidad_presumida	= $xMath->getPagoLease($TI, $this->mPagosAutorizados, $monto_autorizado, $this->mPeriocidadDePago);
							$simV					= $this->mTasaDeInteres + $this->mTasaDeIVA;	
						}
					}
					
					$TolerarHasta	= 0.01;

					$bingo			= false;
					for($simulaciones=1;$simulaciones<=$numero_sim;$simulaciones++){
						if($bingo == false){
							$sumar_dias						= 0;
							$this->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
							$scap			= 0;
							$saldo_final	= 0;
							$saldo_inicial	= $monto_autorizado;
							$divisor_diff	= $PAGOS_AUTORIZADOS;
							for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
								$letra					= $i;
								$saldo_inicial 			= ($i ==1) ? $monto_autorizado : $saldo_final;
								$fecha_de_referencia 	= ($i ==1) ? $fecha_primer_abono : $fecha_de_pago;
								$fecha_de_pago			= $this->getFechaDePago($fecha_de_referencia, $i);
								// ------------------------------------ Obtiene la Fecha de Pago ----------------------------------------------
								$dias_normales			= ($i == 1) ?  restarfechas($fecha_de_pago, $fecha_ministracion) : restarfechas($fecha_de_pago, $fecha_de_referencia);
								if(PLAN_DE_PAGOS_PLANO == true){ $dias_normales		= $PERIOCIDAD_DE_PAGO;	}
								$saldo_insoluto         = $saldo_inicial;
									
								eval ( $FInteres_normal->getFormula() );
								if($interes_normal < 0){
									$interes_normal		= 0;//FIXX
								}								
								$interes_simulado		= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $interes_normal : round($interes_normal, 2);
								$iva_simulado 			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? ($interes_normal * $tasa_iva) : round(($interes_normal * $tasa_iva), 2);
								$parcialidad_simulada	= ($parcialidad_presumida - ($interes_simulado + $iva_simulado));
								$capital_pactado		= $parcialidad_simulada;
								//
								if($ConPagEs == true){
									$idxcap		= $this->mCredito ."-$i-" . OPERACION_CLAVE_PAGO_CAPITAL;
									$pagoesp	= (isset($_SESSION[$idxcap])) ? $_SESSION[$idxcap] : 0;
									if($pagoesp>0){
										$capital_pactado=$pagoesp;
									}									
								}
									
								$saldo_final 			= round(($saldo_inicial - $capital_pactado),2);
								$sumar_dias            	+=  $dias_normales;
								if($i == 1){
									$xLog->add("PERSONA\tCREDITO\t[SIM - DE]\tINTERES\tIVA\tCAPITAL\tTOTAL\tTCAP\tFECHA-I\tFECHA-F\tSALDO-I\tSALDO-F\r\n", $xLog->DEVELOPER);
								}
								$tt						= $interes_simulado + $iva_simulado + $capital_pactado;
								$scap					+= $capital_pactado;
								$xLog->add("$socio\t$solicitud\t[$simulaciones - $i]\t$interes_simulado\t$iva_simulado\t$capital_pactado\t$tt\t$scap\t$fecha_de_referencia\t$fecha_de_pago\t$saldo_inicial\t$saldo_final\r\n", $xLog->DEVELOPER);
								//setLog("$socio\t$solicitud\t[$simulaciones - $i]\t$interes_simulado\t$iva_simulado\t$capital_pactado\t$tt\t$scap\t$fecha_de_referencia\t$fecha_de_pago\t$saldo_inicial\t$saldo_final\r\n");
								if($saldo_final < 0 AND ($divisor_diff == $PAGOS_AUTORIZADOS)){ 
									$divisor_diff		= $PAGOS_AUTORIZADOS +$i;
								}
								$this->setSaldoInicial($saldo_inicial);
								$this->setSaldoFinal($saldo_final);
								
								if($i == $PAGOS_AUTORIZADOS){
									$factor_annios			= ($sumar_dias / 365);
									$desviacion_simulada	= ($saldo_final !== 0) ? ($saldo_final / ($PAGOS_AUTORIZADOS+$divisor_diff)) : 0;
									if($tasa_interes > 1){
										$desviacion_simulada	= ($desviacion_simulada / $tasa_interes);
									}
									//Verificar db ser años que dura en credito
									if($sumar_dias > 367){
										
										$txtNotas	.= "WARN\tDIVIDIR ANIOS $factor_annios por $desviacion_simulada\r\n";
										//setLog("WARN\tDIVIDIR ANIOS $factor_annios por $desviacion_simulada\r\n");								
										$desviacion_simulada	= ($desviacion_simulada/$factor_annios);
		
									}
									$desviacion_simulada		= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $desviacion_simulada : round($desviacion_simulada,2);
									if($desviacion_simulada <= $TolerarHasta AND $desviacion_simulada >= ($TolerarHasta* (-1)) ){
										//setLog("Tal Vez sea Bingo $desviacion_simulada en ID Simular $simulaciones ");
										$bingo	= true;
									}
									if($this->mForceMonto == true AND $this->mParcialidadPres>0){
										$parcialidad_presumida		= $this->mParcialidadPres;
									} else {
										$parcialidad_presumida		+=  $desviacion_simulada;
									}
									
									$txtNotas	.= "$socio\t$solicitud\t[$simulaciones - $i]\tLa Parcialidad Presumida es $parcialidad_presumida por $desviacion_simulada Desviados, Saldo Final $saldo_final en dias $sumar_dias\r\n";

									
									if($saldo_final >= -0.01 AND $saldo_final <= 0.01){
										$bingo	= true;
									}

								}
								
								//Agregar a el Array
								$this->mPagosCalculados[$letra][SYS_INTERES_NORMAL]	= $interes_simulado;
								$this->mPagosCalculados[$letra][SYS_FECHA]			= $fecha_de_pago;
								
							}
						}
						//END BINGO
						//setLog($parcialidad_presumida);
					}
					if($this->mValorResidual <= 0){
						if($saldo_final > 0){
							$parcialidad_presumida	= round(($parcialidad_presumida + ($saldo_final/$PAGOS_AUTORIZADOS)),2);
						}
					} else {
						//setLog("Saldo de Credito: $saldo_final");
					}
					break;
			}
		}
		$xLog->add($txtNotas, $xLog->DEVELOPER);
		//setLog($xLog->getMessages());
		$this->mMessages		.= $xLog->getMessages();
		//setLog($xLog->getMessages());
		$parcialidad_presumida	= round($parcialidad_presumida,2);
		$parcialidad_presumida	= getCantidadRendonda($parcialidad_presumida, $redondeo);
		//Componer de ahorro y otros
		$parcialidad_presumida	= $parcialidad_presumida + $parcialidad_ahorro + $parcialidad_cargo + round(($parcialidad_cargo * $this->mTasaDeIVAOtros),2) ;
		$this->mParcialidadPres	= $parcialidad_presumida;
		
		return $parcialidad_presumida;
	}
	function getMessages($put = OUT_TXT){ $xH	 = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function getFactorIVA($incluido = false){
		$factor		= 1;
		if($this->mTasaDeIVA > 0 AND $incluido == true){	$factor	= 1 * (1 / (1 + $this->mTasaDeIVA));		}
		return $factor;
	}
	function getControlDias($modalidad_de_dias= false){
		$modalidad_de_dias	= setNoMenorQueCero($modalidad_de_dias);
		$modalidad_de_dias	= ($modalidad_de_dias <= 0) ? $this->mTipoDePlanDePago : $modalidad_de_dias;
		$ctrl				= "";
		$xLng				= new cLang();
		$xF					= new cFecha();
		$xSel				= new cHSelect();
		$xTxt				= new cHText();
		
		switch ($this->mPeriocidadDePago){
			case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
				//$c			= $xTxt->getHidden("dia_primer_abono",10, $this->mDiaDeAbono1);
				//$ctrl		= ($modalidad_de_dias == CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS) ? $xSel->getListaDeDiasDeLaSemana("dia_primer_abono", $this->mDiaDeAbono1)->get(false) : "$c";
				$xNSel		= $xSel->getListaDeDiasDeLaSemana("dia_primer_abono", $this->mDiaDeAbono1);
				$xNSel->setDivClass("tx4 tx18 red");
				$ctrl		= $xNSel->get(true);
				break;
			case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
				$xNSel		= $xSel->getListaDeDiasDeLaSemana("dia_primer_abono", $this->mDiaDeAbono1);
				$xNSel->setDivClass("tx4 tx18 red");
				$ctrl		= $xNSel->get(true);
				break;
			case CREDITO_TIPO_PERIOCIDAD_DECENAL:
				$xTxt->setDivClass("tx13");
				$c			= $xTxt->getDeMoneda("dia_primer_abono", "TR.Dia Pago 1",  $this->mDiaDeAbono1);
				$c			.= $xTxt->getDeMoneda("dia_segundo_abono", "TR.Dia Pago 2",  $this->mDiaDeAbono2);
				$c			.= $xTxt->getDeMoneda("dia_tercer_abono", "TR.Dia Pago 3",  $this->mDiaDeAbono3);
				$c			= "<div id='id' class='tx4'>$c</div>";
				$ctrl		= $c;
				break;
			case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
				$xTxt->setDivClass("tx12");
				$c			= $xTxt->getDeMoneda("dia_primer_abono", "TR.Dia Pago 1",  $this->mDiaDeAbono1);
				$c			.= $xTxt->getDeMoneda("dia_segundo_abono", "TR.Dia Pago 2",  $this->mDiaDeAbono2);
				$c			= "<div id='id' class='tx4'>$c</div>";
				$ctrl		= $c;
				break;
			case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
				$xTxt->setDiv13();
				$c			= $xTxt->getDeMoneda("dia_primer_abono", "TR.Dia Pago 1",  $this->mDiaDeAbono1);
				$ctrl		= $c;
				break;
			default:
				$c			= $xTxt->getDeMoneda("dia_primer_abono", "TR.Dia Pago 1",  $this->mDiaDeAbono1);
				$ctrl		= $c;					
				break;
				
		}				


		return $ctrl;
	}
	function setCompilar($tipo_de_pago = false){
		$xRuls		= new cReglaDeNegocio();
		$SinOtros	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_OTROS);	//regla de negocio
		$SinAnual	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_ANUAL);	//regla de negocio
		$ConPagEs	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_CON_PAGESP);	//regla de negocio
		$ConTasa0	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PUEDEN_TASA_CERO);	//regla de negocio
		$this->mMaximoCAT	= $this->getPeriodosAnnio($this->mPeriocidadDePago);				//Obtiene el numero de periodos por cada annio
		$xLog				= new cCoreLog();
		$tipo				= setNoMenorQueCero($tipo_de_pago);
		$tipo				= ($tipo <= 0 ) ? $this->mTipoDePagos : $tipo;
		$this->mTipoDePagos	= $tipo_de_pago;
		$parcialidad		= $this->mParcialidadPres;
		$InteresPagado		= $this->mInteresPagado;
		$CapitalPagado		= $this->mMontoPagado;
		$xCred				= $this->OCredito();
		$xF					= new cFecha(0, $this->mFechaMinistracion);
		$afectado			= false;
		$mes_de_pago		= $xF->mes();
		$PAGOS_AUTORIZADOS	= $this->mPagosAutorizados;
		$tasa_interes		= $this->mTasaDeInteres;
		$monto_extra		= 0;
		$tipo_extra			= $this->mIDOtrosCargos;
		$monto_ahorro		= 0;
		$bonificacion		= 0;
		$total_pagos		= 0;
		$capital_fijo		= ($this->mMontoAutorizado / $PAGOS_AUTORIZADOS);
		$this->mArrSinIVA	= array();
		$this->mCapitalCAT	= 0;

		if($this->mMontoOtrosCargos > 0 AND $this->mIDOtrosCargos >0){
			if($this->mAplicar1erPago == false){
				$monto_extra	= setNoMenorQueCero(($this->mMontoOtrosCargos / $this->mPagosAutorizados),2);
			}
		}
		if($this->mMontoBonificacion > 0 AND $this->mIDOtrosCargos >0){
			if($this->mAplicar1erPago == false){
				$bonificacion	= setNoMenorQueCero(($this->mMontoBonificacion / $this->mPagosAutorizados),2);
			}			
		}
		if($this->mMontoAhorro > 0){
			$monto_ahorro		= $this->mMontoAhorro / $this->mPagosAutorizados;
		}
		$fecha_anterior			= $this->mFechaMinistracion;
		$total_abonos			= 0;
		$monto_original			= $this->mMontoAutorizado;
		
//============================= PAGOS SOLO INTERES
		//99=NATURAL 2=personalizados
		if($tipo == CREDITO_TIPO_PAGO_INTERES_COMERCIAL OR $tipo == CREDITO_TIPO_PAGO_INTERES_PERIODICO){
			$this->mPagosSinCapital	= true;
			$xLog->add("==\tSOLO INTERES\r\n", $xLog->DEVELOPER);
			$BaseInteres		= $this->mMontoAutorizado;// $this->mBaseDeCalculos;
			$DatosDePago		= array();
			if($this->mEsCreditoAfectado == true){
				if($xCred->initPagosEfectuados() == true){
					$DatosDePago= $xCred->getListadoDePagos();
				}
			}
			for($letra = 1 ; $letra <= $this->mPagosAutorizados; $letra++){
				$fecha														= $fecha_anterior;
				if(isset($this->mFechasCalculadas[$letra])){
					$fecha													= $this->mFechasCalculadas[$letra];
				}
				$this->mPagosCalculados[$letra][SYS_NUMERO]					= $letra;
				$this->mPagosCalculados[$letra][SYS_FECHA]					= $fecha;
				$this->mPagosCalculados[$letra]["SYS_FECHA_ANTERIOR"]		= $fecha_anterior;
				$this->mPagosCalculados[$letra][SYS_AHORRO]					= $monto_ahorro;
				$this->mPagosCalculados[$letra]["SYS_IDOTROS"]				= $tipo_extra;
				$this->mPagosCalculados[$letra]["SYS_OTROS"]				= $monto_extra;
				$this->mPagosCalculados[$letra]["SYS_BONIFICACION"]			= $bonificacion;
				$this->mPagosCalculados[$letra]["CAPITAL_PAGADO"]			= 0;
				$this->mPagosCalculados[$letra]["INTERES_PAGADO"]			= 0;
				$this->mPagosCalculados[$letra]["FECHA_PAGADO"]				= $fecha;
				
				if($letra == 1){
					$this->mFechaPrimerPago									= $fecha;
					
				}
				if($this->mAplicar1erPago == true){
					if($letra == 1){
						$this->mPagosCalculados[$letra]["SYS_OTROS"]		= $this->mMontoOtrosCargos;
						$monto_extra										= $this->mMontoOtrosCargos;
						$parcialidadletra									+= setNoMenorQueCero( ($this->mMontoOtrosCargos  + ($this->mMontoOtrosCargos * $this->mTasaDeIVAOtros)), 2);
						$this->mPagosCalculados[$letra]["SYS_BONIFICACION"]	= $this->mMontoBonificacion;
					} else {
						$this->mPagosCalculados[$letra]["SYS_OTROS"]		= 0;
						$monto_extra										= 0;
						$bonificacion										= 0;
						$parcialidadletra									= $this->mParcialidadPres;
					}
				}
				$this->mPagosCalculados[$letra]["SYS_DIAS"]					= $xF->setRestarFechas($fecha, $fecha_anterior);
				$dias														= ($tipo == CREDITO_TIPO_PAGO_INTERES_COMERCIAL) ? $this->mPeriocidadDePago : $xF->setRestarFechas($fecha, $fecha_anterior);
				$interes_normal												= ($BaseInteres * ($this->mTasaDeInteres * $this->getFactorIVA()) * $dias ) / EACP_DIAS_INTERES;
				
//=============Modifica el Interes
				$anterior													= setNoMenorQueCero(($letra - 1 ));
				if(isset($DatosDePago[$letra])){ //si existe el pago actual
					$DPago													= $DatosDePago[$letra];
					$interes_normal											= ($BaseInteres * ($this->mTasaDeInteres * $this->getFactorIVA()) * $dias ) / EACP_DIAS_INTERES;
					$BaseInteres											= setNoMenorQueCero(($BaseInteres - $DPago[SYS_CAPITAL] ),2);
					$this->mPagosCalculados[$letra]["CAPITAL_PAGADO"]		= $DPago[SYS_CAPITAL];
					$this->mPagosCalculados[$letra]["INTERES_PAGADO"]		= $DPago[SYS_INTERES_NORMAL];
					$this->mPagosCalculados[$letra]["FECHA_PAGADO"]			= $DPago[SYS_FECHA];
				}
				$this->mPagosCalculados[$letra][SYS_INTERES_NORMAL]			= setNoMenorQueCero($interes_normal,2);
				$iva														= setNoMenorQueCero( (($interes_normal * $this->mTasaDeIVA) + ($monto_extra * $this->mTasaDeIVAOtros)), 2);
				$this->mPagosCalculados[$letra][SYS_IMPUESTOS]				= setNoMenorQueCero($iva,2);
				//No entiendo esta parte, ha yaaaa... Es pago solo interes
				if($letra >= $this->mPagosAutorizados){
					$capital												= $BaseInteres;
				} else {
					$capital												= 0;
				}
				//==========================================================		
				$this->mPagosCalculados[$letra][SYS_CAPITAL]				= $capital;
				$this->mPagosCalculados[$letra][SYS_MONTO]					= setNoMenorQueCero((($monto_ahorro + $monto_extra + $interes_normal + $iva - $bonificacion) + $capital),2);
				$this->mTotalPlan											+= setNoMenorQueCero((($monto_ahorro + $monto_extra + $interes_normal + $iva - $bonificacion) + $capital),2);
				//==========================================================
				if($letra < $this->mPagosAutorizados){
					$this->mParcialidadPres									= setNoMenorQueCero((($monto_ahorro + $monto_extra + $interes_normal + $iva - $bonificacion) + $capital),2);
				}
				//if($letra <= $this->mMaximoCAT){
				if(setNoMenorQueCero((($monto_extra + $interes_normal - $bonificacion) + $capital),2) >0){
					$this->mArrSinIVA[]										= setNoMenorQueCero((($monto_extra + $interes_normal - $bonificacion) + $capital),2);
				}
					//$this->mCapitalCAT										+= $capital;
				//}
				$total_abonos												+= $capital;
					
				$this->mPagosCalculados[$letra][SYS_TOTAL]					= setNoMenorQueCero($total_abonos,2);
				$xLog->add("$letra\t$fecha\t$capital\t$interes_normal\t$monto_extra\t$iva\t$monto_ahorro\t$tipo_extra\t$bonificacion\r\n", $xLog->DEVELOPER);
				$fecha_anterior									= $fecha;
			}
			//reconstruir Saldos Iniciales y Finales
			$saldo_inicial	= $this->mTotalPlan;
			$saldo_final	= 0;
			foreach ($this->mPagosCalculados as $idx => $cnt){
				$this->mPagosCalculados[$idx]["SYS_INICIAL"]	= $saldo_inicial;
				$saldo_final									= setNoMenorQueCero( ($saldo_inicial - $this->mPagosCalculados[$idx][SYS_MONTO]), 2);
				$this->mPagosCalculados[$idx]["SYS_FINAL"]		= $saldo_final;
				
				$saldo_inicial									= $saldo_final;
			}
//=============================== PAGOS NIVELADOS
		} else {
			$xLog->add("==\tPAGO NIVELADO : Parcialidad = $parcialidad\r\n", $xLog->DEVELOPER);
			
			$DatosDePago		= array();
			if($this->mEsCreditoAfectado == true){
				if($xCred->initPagosEfectuados() == true){
					$DatosDePago= $xCred->getListadoDePagos();
				}
			
			}			
			foreach ($this->mFechasCalculadas as $letra => $fecha){
				$parcialidadletra											= $this->mParcialidadPres;
				
				$this->mPagosCalculados[$letra][SYS_NUMERO]					= $letra;
				$this->mPagosCalculados[$letra][SYS_FECHA]					= $fecha;
				$this->mPagosCalculados[$letra]["SYS_FECHA_ANTERIOR"]		= $fecha_anterior;
				$this->mPagosCalculados[$letra][SYS_AHORRO]					= $monto_ahorro;
				$this->mPagosCalculados[$letra]["SYS_IDOTROS"]				= $tipo_extra;
				$this->mPagosCalculados[$letra]["SYS_OTROS"]				= $monto_extra;
				$this->mPagosCalculados[$letra]["SYS_BONIFICACION"]			= $bonificacion;
				$this->mPagosCalculados[$letra]["CAPITAL_PAGADO"]			= 0;
				$this->mPagosCalculados[$letra]["INTERES_PAGADO"]			= 0;
				$this->mPagosCalculados[$letra]["FECHA_PAGADO"]				= $fecha;
			
				$marcarLetra												= false;
				if($letra == 1){
					$this->mFechaPrimerPago									= $fecha;
					$fecha_anterior											= $this->mFechaMinistracion;
				}
				if($this->mAplicar1erPago == true){
					if($letra == 1){
						$this->mPagosCalculados[$letra]["SYS_OTROS"]		= $this->mMontoOtrosCargos;
						$monto_extra										= $this->mMontoOtrosCargos;
						$parcialidadletra									+= setNoMenorQueCero( ($this->mMontoOtrosCargos  + ($this->mMontoOtrosCargos * $this->mTasaDeIVAOtros)), 2);
						$this->mPagosCalculados[$letra]["SYS_BONIFICACION"]	= $this->mMontoBonificacion;
					} else {
						$this->mPagosCalculados[$letra]["SYS_OTROS"]		= 0;
						$monto_extra										= 0;
						$bonificacion										= 0;
						$parcialidadletra									= $this->mParcialidadPres;
					}
				}
				$this->mPagosCalculados[$letra]["SYS_DIAS"]					= $xF->setRestarFechas($fecha, $fecha_anterior);
				$interes_normal												= 0;
				if(isset($this->mPagosCalculados[$letra][SYS_INTERES_NORMAL])){
					$interes_normal											= $this->mPagosCalculados[$letra][SYS_INTERES_NORMAL];
				} else {
					if($ConTasa0 == true){
						$this->mPagosCalculados[$letra][SYS_INTERES_NORMAL] = 0;
					}	
				}
				$anterior													= setNoMenorQueCero(($letra - 1 ));
				if(isset($DatosDePago[$letra])){ //si existe el pago actual
					$DPago													= $DatosDePago[$letra];
					$this->mPagosCalculados[$letra]["CAPITAL_PAGADO"]		= setNoMenorQueCero($DPago[SYS_CAPITAL],2);
					$this->mPagosCalculados[$letra]["INTERES_PAGADO"]		= setNoMenorQueCero($DPago[SYS_INTERES_NORMAL],2);
					$this->mPagosCalculados[$letra]["FECHA_PAGADO"]			= $DPago[SYS_FECHA];
					$total_pagos											+= setNoMenorQueCero($DPago[SYS_CAPITAL],2);
					$marcarLetra											= true;
				}

				$iva											= setNoMenorQueCero( (($interes_normal * $this->mTasaDeIVA) + ($monto_extra * $this->mTasaDeIVAOtros)), 2);
				$this->mPagosCalculados[$letra][SYS_IMPUESTOS]	= setNoMenorQueCero($iva,2);
				$fecha_anterior									= $fecha;
//======================= Determinar el Capital

				$idxcap		= $this->mCredito ."-$letra-" . OPERACION_CLAVE_PAGO_CAPITAL;
				$pagoesp	= (isset($_SESSION[$idxcap])) ? $_SESSION[$idxcap] : 0;
				if($ConPagEs == true AND $pagoesp > 0){
					$capital									= $pagoesp;
				} else {
					$capital									= ($parcialidadletra - ($monto_ahorro + $monto_extra + $interes_normal + $iva));
					if($this->mTipoDePagos == CREDITO_TIPO_PAGO_CAPITAL_FIJO){
						$capital								= $capital_fijo;
					}					
				}
				
				$this->mPagosCalculados[$letra][SYS_CAPITAL]	= setNoMenorQueCero($capital, 2);
				if(setNoMenorQueCero(($total_abonos+$capital)) > $monto_original){
					$capital									= setNoMenorQueCero(($monto_original - $total_abonos), 2);
					$this->mPagosCalculados[$letra][SYS_CAPITAL]= $capital;
				}
				if($letra >= $this->mPagosAutorizados){		}
				//==========================================================
				$this->mPagosCalculados[$letra][SYS_MONTO]		= setNoMenorQueCero((($monto_ahorro + $monto_extra + $interes_normal + $iva - $bonificacion) + $capital),2);
				$this->mTotalPlan								+= setNoMenorQueCero((($monto_ahorro + $monto_extra + $interes_normal + $iva - $bonificacion) + $capital),2);
				//if($letra <= $this->mMaximoCAT){
				if(setNoMenorQueCero((($monto_extra + $interes_normal - $bonificacion) + $capital),2)> 0){
					$this->mArrSinIVA[]							= setNoMenorQueCero((($monto_extra + $interes_normal - $bonificacion) + $capital),2);
				}
					//$this->mCapitalCAT							+= $capital;
				//}
				$total_abonos									+= $capital;
				
				$this->mPagosCalculados[$letra][SYS_TOTAL]		= setNoMenorQueCero($total_abonos,2);
				$this->mPagosCalculados[$letra]["TOTAL_PAGADO"]	= setNoMenorQueCero($total_pagos, 2);
				if($marcarLetra	== true){
					$this->mMontoUltimo							= round(($total_abonos - $total_pagos),2);
					$this->mPagoCalculado						= $letra;
				}
				$xLog->add("$letra\t$fecha\t$capital\t$interes_normal\t$monto_extra\t$iva\t$monto_ahorro\t$tipo_extra\t$bonificacion\r\n", $xLog->DEVELOPER);

			} //End For Pagos Nivelados
			
			//reconstruir Saldos Iniciales y Finales
			$saldo_inicial	= $this->mTotalPlan;
			$saldo_final	= 0;
			foreach ($this->mPagosCalculados as $idx => $cnt){
				$this->mPagosCalculados[$idx]["SYS_INICIAL"]	= $saldo_inicial;
				$saldo_final									= setNoMenorQueCero( ($saldo_inicial - $this->mPagosCalculados[$idx][SYS_MONTO]), 2);
				$this->mPagosCalculados[$idx]["SYS_FINAL"]		= $saldo_final;
				
				$saldo_inicial									= $saldo_final;
				
			}
		}
		return $xLog->getMessages();
	}
	function getVersionFinal($guardar = false , $formato = OUT_HTML, $origen = HP_FORM, $FechaDePlan = false){
		$xRuls		= new cReglaDeNegocio();
		$SinOtros	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_OTROS);	//regla de negocio
		$SinAnual	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_ANUAL);	//regla de negocio
		$ConPagEs	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_CON_PAGESP);	//regla de negocio
		$ConPago0	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_CON_CEROS);	//regla de negocio
		$SinAjustF	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_FINAL);	//regla de negocio
		$xLog		= new cCoreLog();
		$xF			= new cFecha();
		$xL			= new cLang();
		$xT			= new cHText();
		$xT->setDivClass("");
		
		$errorres	= "";
		$cuerpo		= "";
		if($guardar == true){
			
			$xPlan	= new cPlanDePagos($this->mClaveDePlan);
			
			if($this->mClaveDePlan > 0){ $xPlan->setEliminar(); }
			$xPlan->setClaveDeCredito($this->mCredito);
			$xPlan->setClaveDePersona($this->mPersona);
			$xPlan->setForzarCeros($ConPago0);
			//$xPlan->initByCredito($this->mCo)
			//Agregar
			$xPlan->add($this->mObservaciones, $xF->getFechaISO($FechaDePlan) );
			$this->mClaveDePlan	= $xPlan->getClaveDePlan();
		}

		$idValid		= ($this->mPagosSinCapital == false) ? SYS_CAPITAL : SYS_INTERES_NORMAL;
		$indice			= 1;
		$ultimo			= 0;//Ultimo Pago
		$inicial		= $this->mTotalPlan;
		$final			= $this->mTotalPlan;
		$TotalPag		= 0;
		$TotalCalc		= 0;
		$IntPag			= 0;
		$IntCalc		= 0;
		$PararCalc		= false;
		$this->mMontoUltimo	= 0;
		$SumaCapital	= 0;
		$SumaInteres	= 0;
		$SumaAhorro		= 0;
		$SumaOtros		= 0;
		$SumaDescto		= 0;
		$SumaIVA		= 0;
		$ToleraMigra	= (MODO_MIGRACION == false) ? 0 : $this->mParcialidadPres * 0.3;
		$MontoMigra		= 0;
		$AjusteMigra	= 0;
		
		foreach ($this->mPagosCalculados as $datos){
			if($this->mMostrarCompleto == false){	//Si es Mostrar Completo, todos los pagos se neutralizan
				$PInteres		= (isset($datos[SYS_INTERES_NORMAL])) ? $datos[SYS_INTERES_NORMAL] : 0;
				$PCapital		= $datos[SYS_CAPITAL];
				$PPago_interes	= $datos["INTERES_PAGADO"];
				$PPago_capital	= $datos["CAPITAL_PAGADO"];
				$TotalPag		+= $PPago_capital;
				$IntPag			+= $PPago_interes;
			}
		}
		
		foreach ($this->mPagosCalculados as $datos){
			
			if(isset($datos[$idValid])){
				$otros			= $datos["SYS_OTROS"];
				$ahorro			= $datos[SYS_AHORRO];
				$interes		= (isset($datos[SYS_INTERES_NORMAL])) ? $datos[SYS_INTERES_NORMAL] : 0;
				$capital		= $datos[SYS_CAPITAL];
				$iva			= $datos[SYS_IMPUESTOS];
				$bonificacion	= $datos["SYS_BONIFICACION"];
				$total			= $datos[SYS_MONTO];
				$pago_interes	= $datos["INTERES_PAGADO"];
				$pago_capital	= $datos["CAPITAL_PAGADO"];
				$idotros		= $datos["SYS_IDOTROS"];
				$DHistorico		= $datos;
				$total_pago		= (isset($datos["TOTAL_PAGADO"])) ? $datos["TOTAL_PAGADO"] : 0;
				
				$fecha			= $this->getDiaHabil($datos[SYS_FECHA]);
				
				
				
				$idxanual		= $this->mCredito ."-$indice-" . OPERACION_CLAVE_ANUALIDAD_C;
				$idxcap			= $this->mCredito ."-$indice-" . OPERACION_CLAVE_PAGO_CAPITAL;
				$anualidad		= (isset($_SESSION[$idxanual])) ? $_SESSION[$idxanual] : 0;
				$pagoesp		= (isset($_SESSION[$idxcap])) ? $_SESSION[$idxcap] : $capital;

				//=======================================Condicionador de Pagos Especiales
				if($ConPagEs == true){
					if($pagoesp>0){
						$capital = $pagoesp;
					} else {
						$pagoesp = $capital;
					}
				}
				$this->mFechaUltimoPago	= $fecha;
				$pagado					= false;
				$operado				= false;
				//Componer Saldo Inicial y Final
				$inicial				= $datos["SYS_INICIAL"];
				$final					= $datos["SYS_FINAL"];
				
				if($indice == 1){ $this->mFechaPrimerPago	= $fecha; }
				if($this->mPagosSinCapital == true){
					if($IntPag >= $interes){
						$IntPag			= ($IntPag - $interes);
						$ahorro			= 0;
						$otros			= 0;
						$bonificacion	= 0;
						$interes		= 0;
						$ultimo			= $indice;
						$pagado			= true;
						
					} else {
						//$interes		= ($interes - $pago_interes);
						$interes		= ($interes - $IntPag);
						$IntPag			= 0;
					}
				} else {
					//== Corregir capital
					//setLog("$indice -- $TotalCalc -- $TotalPag ");
					
					if($TotalPag >= $capital){
						//setLog("$indice -- Total $TotalPag Restar $capital ");
						$TotalPag		= ($TotalPag - $capital);
						$capital		= 0;
					} else {
						if($TotalPag > 0){
							//corrige el abono de capitales, por diferencia tolerable
							if(MODO_MIGRACION == true ){
								$tolerable		= setNoMenorQueCero(($capital * $this->mTolerableIncl),2); //el tolerable es 70%
								$capital		= $capital - $TotalPag;
								$ultimo			= $indice;
								$pagado			= true;
								if($capital > TOLERANCIA_SALDOS AND $this->mGuardar1eraDiff == true AND ($capital >= $tolerable)){
									//no hace nada si se guarda la diferencia, si es mayor a 1 o es mayor al tolerable
								} else {
									$ahorro			= 0;
									$otros			= 0;
									$bonificacion	= 0;
									//XXX: Este interes evita que se elimine la letra anterior
									//====================== Parche de prueba, solo si no hay interes pagado
									if($IntPag <= 0){
										$interes	= 0;
									}
									
								}
								//setLog("TOPAR CON $capital ");
							} else {
								$this->mMontoUltimo	= $TotalPag;
							}
							
							$TotalPag				= 0;
						}
					}
					
					//===  re-corregir
					if($capital > 0 AND $this->mMontoUltimo > 0){
						if($this->mMontoUltimo > $capital){
							$this->mMontoUltimo	= $this->mMontoUltimo - $capital;
							$capital			= 0;
						}
					}
					//===  re-re-corregir
					if($pago_capital > 0  AND $this->mMontoUltimo > 0){
						if($capital > $this->mMontoUltimo){
							$capital			= ($capital - $this->mMontoUltimo);
							$this->mMontoUltimo	= 0;
						}
					}
					 
					if($IntPag >= $interes){
						$IntPag			= ($IntPag - $interes);
						$interes		= 0;
					} else {
						//si el Interes Pagado es mayor a cero, pero menor al pago
						if($IntPag > 0){
							$interes	= $interes - $IntPag;
							$IntPag		= 0;
						}
					}
					//======================================= Ajuste de Capital Inicial
					if($this->mAjusteSobreInt == true AND (($this->mPagoActual+1) == $indice AND $this->mPagoActual > 0) ){
						//Ajustar capital.- Se presume la siguiente letra
						if($this->mMontoCapAjust >= 0.01){
							$mTmpTotal	= $capital + $interes + $otros + $iva + $ahorro;
							$mTmpTotal2	= setNoMenorQueCero(($this->mParcialidadPres -  $mTmpTotal)); //si la parc presumida es mayor a los pagos
							//Ajustar contra toda la parc presumida
							if($mTmpTotal2 >= $this->mMontoCapAjust){
								$capital				= $capital + $this->mMontoCapAjust;
								$this->mMontoCapAjust	= 0;
							} else {
								$capital				= $capital + $mTmpTotal2;
								$this->mMontoCapAjust  	= $this->mMontoCapAjust - $mTmpTotal2;			//Si no es mayo igual, ajustar diferencia
							}
							if($interes >= $this->mMontoCapAjust){
								$interes				= round(($interes - $this->mMontoCapAjust),2);
								$capital				= round(($capital + $this->mMontoCapAjust),2);
								$this->mMontoCapAjust 	= 0;
							}
						}
					}
					if($capital <= 0){
						$ultimo			= $indice;
						$pagado			= true;
						$ahorro			= 0;
						$otros			= 0;
						$bonificacion	= 0;
						$interes		= 0;
					}
					
				} //end solo interes vs capital
				
				if($bonificacion > ($interes + $otros) ){
					$interes			= 0;
					$otros				= 0;
					$bonificacion		= 0;
				} else {
					$totalBon			= $bonificacion;
					if($bonificacion > $otros){
						$bonificacion	= $bonificacion - $otros;
						$otros			= 0;
					} else {
						$otros			= $otros - $bonificacion;
						$bonificacion	= 0;
					}
					if($bonificacion > $interes){
						$interes		= 0;
					} else {
						$interes		= $interes - $bonificacion;
					}
					$bonificacion		= 0;
				}
				
				//Envia centavos al ultimo pago
				if(($capital >= 0.01 AND $capital  <= TOLERANCIA_SALDOS AND ($indice < $this->mPagosAutorizados))){
					$this->mMontoUltimo	= $capital;
					$capital			= 0;
					if($interes <= TOLERANCIA_SALDOS){
						$interes		= 0;
					}
					$otros				= 0;
					$ahorro				= 0;
				}
				
				if($indice >= $this->mPagosAutorizados AND $this->mMontoUltimo > 0){
					//setLog("$indice - Capital de $capital y remanente " . $this->mMontoUltimo);
					$capital			= ($capital > 0) ? ($capital - $this->mMontoUltimo) : $capital;
				}
				
				if($this->mEsCreditoAfectado == true){
					//===== Correccion de migracion
					//XXX: TODO: Esta mal.
					if( $xF->getInt($this->mFechaMinistracion) <= $xF->getInt($this->FECHA_v11) ){
						$mTmpIVA		= setNoMenorQueCero( (($interes * $this->mTasaDeIVA) + ($otros * $this->mTasaDeIVAOtros)), 2);
						$mTmpAcc		= round(($interes + $otros + $ahorro + $mTmpIVA),2);
						$mTmpParc		= round(($capital + $mTmpAcc),2);
						
						if($MontoMigra > 0.01){
							$capital		+= $MontoMigra;
							$MontoMigra		= 0;
							
							if($mTmpParc > $this->mParcialidadPres){
								$mTmpDiff		= $mTmpParc - round($this->mParcialidadPres,2);
								
								$AjusteMigra	= $mTmpDiff;
								$capital		= ($capital - $mTmpDiff);
							}
						} else {
							
							if(($capital <= $ToleraMigra) AND $mTmpAcc <= 0.01){
								$MontoMigra	= $capital;
								$pagado		= true;
								$capital	= 0;
							}
						}
					}
					//
				}
				//setError(" $indice -- $capital");
				if($this->mEsCreditoAfectado == false AND $indice >= $this->mPagosAutorizados){
					$dif			= setNoMenorQueCero( ($this->mMontoAutorizado -($SumaCapital + $capital)), 2);
					
					if($dif > 0 AND $dif <= TOLERANCIA_SALDOS){
						$capital	+= $dif;
						$dif		= setNoMenorQueCero( ($dif * (1/(1 + $this->mTasaDeIVA))), 2);
						$interes	= ($interes - $dif);
						
					}
				}
				if($interes > 0 AND $capital <= 0 AND $this->mSinIntereses == true){
					$interes		= 0;
				}
				//Calcular nuevamente el IVA
				$iva					= setNoMenorQueCero( (($interes * $this->mTasaDeIVA) + ($otros * $this->mTasaDeIVAOtros)), 2);

				$total					= $capital + $interes + $otros + $ahorro + $iva;
				
				if($indice == $this->mPagosAutorizados){
					if($total > $this->mParcialidadPres ){
						$diferencia			= ($total  - $this->mParcialidadPres);
						//Ajustar vs Interes
						if($diferencia <= TOLERANCIA_SALDOS AND $diferencia <= $interes){
							$interes		= setNoMenorQueCero(($interes - ($diferencia -  ($diferencia * $this->mTasaDeIVA)) ),2);
						}
						
						$iva				= setNoMenorQueCero( (($interes * $this->mTasaDeIVA) + ($otros * $this->mTasaDeIVAOtros)), 2);
						$total				= $capital + $interes + $otros + $ahorro + $iva;
					}
					//Correccion chapuzera de urgencia
					if($this->mEsCreditoAfectado == true){
						$capital			= $capital + $AjusteMigra;
						$AjusteMigra		= 0;
					}
				}
				
				if($this->mEsCreditoAfectado == true){
					//Ajusta otros Si solo existe capital y otros
					if($otros > 0){
						if(($capital+$otros) == $total){
							$otros			= 0;
							$total			= $capital;
						}
					}
					//ajusta A Interes de la proxima letra.
					if($this->mAjusteSobreInt == true AND ($this->mPagoActual == $indice AND $this->mPagoActual > 0) ){
						if($capital == $total){
							$this->mMontoCapAjust = $capital;
							$capital	= 0;
							$total		= 0;
						}
					}
				}
				
				$css					= "";
				$cssInt					= "";
				$cssCapt				= "";
				if(($this->mPagoActual+1) == $indice AND $this->mPagoActual > 0 ){
					$css				= " class='warn' ";
				}
				if($origen == HP_FORM){
					if($pagado == true AND $css == ""){
							$css		= " class='notice' ";
					}
				}
				//===================== Sumas
				$SumaCapital			+= $capital;
				$SumaInteres			+= $interes;
				$SumaAhorro				+= $SumaAhorro;
				$SumaOtros				+= $otros;
				$SumaDescto				+= $bonificacion;
				$SumaIVA				+= $iva;
				//===================== Obtiene el Ultimo periodo
				if($total == 0){
					if($indice == $this->mPagosAutorizados){
						if($this->mMontoActual <= $capital){
							$this->mUltimoPeriodoPag	= $indice;
						}
					} else {
						$this->mUltimoPeriodoPag		= $indice;
					}
				}
				if($this->mValorResidual>0){
					$inicial	= $inicial + $this->mValorResidual;
					$final		= $final + $this->mValorResidual;
				}
				$salida		= "";
				if($formato == OUT_TXT){
					$salida	.= $datos[SYS_NUMERO] . "\t";
					$salida	.= $xF->getFechaCorta($fecha) . "\t";
					$salida	.= getFMoney($capital) . "\t";
					$salida	.= getFMoney($interes) . "\t";
					$salida	.= getFMoney($otros) . "\t";
					$salida	.= getFMoney($iva) . "\t";
					$salida	.= getFMoney($ahorro) . "\t";
					$salida	.= getFMoney($bonificacion) . "\t";
					$salida	.= getFMoney($total) . "\t";
					
					$salida	.= $datos["SYS_IDOTROS"] . "\t";
					$salida	.= $datos["SYS_DIAS"] . "\t";
					$salida	.= getFMoney($datos[SYS_TOTAL]) . "\t";
					$salida	.= $xF->getFechaCorta($datos["FECHA_PAGADO"]) . "\t";
					$salida	.= getFMoney($pago_capital) . "\t";
					$salida	.= getFMoney($pago_interes) . "\t";
					$salida	.= getFMoney($total_pago) . "\t";
					$salida	.= getFMoney($inicial) . "\t";
					$salida	.= getFMoney($final) . "\t";
									
				} else {
					$css_1	= " class='mny' ";
					$css_2	= "";
					if($this->mSoloTest == true){
						$css_2	= " class='der' ";
					}
					$salida	.= "<tr$css>";
					$salida	.= "<td>" . $datos[SYS_NUMERO] . "</td>";
					$salida	.= "<td>" . $xF->getFechaCorta($fecha) . "</td>";
					if($ConPagEs == false OR $this->mSoloTest == true){
						$salida	.= "<td>" . getFMoney($capital) . "</td>";
					} else {
						$xT->addEvent("jsSetCapital(this)", "onchange");
						$salida	.= "<td>" . $xT->getDeMoneda($idxcap, "", $pagoesp) . "</td>";
					}
					$salida	.= "<td>" . getFMoney($interes) . "</td>";
					$salida	.= ($SumaOtros ==0 AND $otros == 0 AND $this->mMontoOtrosCargos <= 0) ? "" : "<td>" . getFMoney($otros) . "</td>";
					$salida	.= "<td>" . getFMoney($iva) . "</td>";
					$salida	.= ($SumaAhorro == 0 AND $ahorro == 0) ? "": "<td>" . getFMoney($ahorro) . "</td>";
					$salida	.= ($SumaDescto == 0 AND $bonificacion == 0) ? "" : "<td>" . getFMoney($bonificacion) . "</td>";
					$salida	.= "<td$css_1>" . getFMoney($total) . "</td>";
						
					$salida	.= ($SumaOtros ==0 AND $otros == 0 AND $this->mMontoOtrosCargos <= 0) ? "" : "<td>" . $datos["SYS_IDOTROS"] . "</td>";
					
					if($this->mSinDatosE == false){
						$salida	.= "<td>" . $datos["SYS_DIAS"] . "</td>";
						$salida	.= "<td>" . getFMoney($datos[SYS_TOTAL]) . "</td>";
					}
					
					
					if($this->mSoloTest == false){
						if($SinAnual == false){
							$xT->addEvent("jsSetAnualidad(this)", "onchange");
							$salida	.= "<td>" . $xT->getDeMoneda($idxanual, "", $anualidad) . "</td>";
						}
						$salida	.= "<td>" . $xF->getFechaCorta($datos["FECHA_PAGADO"]) . "</td>";
						$salida	.= "<td$cssCapt>" . getFMoney($pago_capital) . "</td>";
						$salida	.= "<td$cssInt>" . getFMoney($pago_interes) . "</td>";
						$salida	.= "<td>" . getFMoney($total_pago) . "</td>";
					}
					if($this->mSinDatosE == false){
						$salida	.= "<td>" . getFMoney($inicial) . "</td>";
					}
					$salida	.= "<td$css_2>" . getFMoney($final) . "</td>";
					$salida	.= "</tr>";	
				}
				if($this->mClaveDePlan > 0 AND $guardar == true){
					//============== Elimina en Migracion
					if(MODO_MIGRACION == true){
						if($xF->getInt($fecha) <= $xF->getInt(SYS_FECHA_DE_MIGRACION)){
							if($capital <= 0 AND $interes > 0){
								
								$ahorro		= 0;
								$bonificacion=0;
								
								$otros		= 0;
								$interes	= 0;
								$iva		= 0;
							}
						}
						
					}
					if($total>0){
						$final				= $inicial - $total;
					}

					$xPlan->setSaldoInicial($inicial);
					$xPlan->setSaldoFinal($final);
					if(MODULO_CAPTACION_ACTIVADO == true){
						$xPlan->addMvtoDeAhorro($ahorro, $fecha, $indice);
					}
					$xPlan->addBonificacion($bonificacion, $fecha, $indice, $idotros);
					$xPlan->addMvtoDeInteres($interes, $fecha, $indice);
					$xPlan->addMvtoOtros($otros, $fecha, $indice, $idotros);
					$xPlan->addMvtoDeIVA($fecha, $indice, $iva);
					$xPlan->addMvtoDeCapital($capital, $fecha, $indice);
					if($anualidad >0){
						$xPlan->addMvtoOtros($anualidad, $fecha, $indice, OPERACION_CLAVE_ANUALIDAD_C);
					}
					//Guardar Letra
					$hotros			= $DHistorico["SYS_OTROS"];
					$hahorro		= $DHistorico[SYS_AHORRO];
					$hinteres		= (isset($DHistorico[SYS_INTERES_NORMAL])) ? $DHistorico[SYS_INTERES_NORMAL] : 0;
					$hcapital		= $DHistorico[SYS_CAPITAL];
					$hiva			= $DHistorico[SYS_IMPUESTOS];
					//$hbonificacion	= $DHistorico["SYS_BONIFICACION"];
					$hidotros		= $DHistorico["SYS_IDOTROS"];
					$hfinal			= $DHistorico["SYS_FINAL"];
					if($this->mValorResidual>0){
						$hfinal  	= $hfinal + $this->mValorResidual;
					}
					$xLetra	= new cCreditosLetraDePago($this->mCredito, $indice);
					$xLetra->add($fecha, $hcapital, $hinteres, $hiva, $hotros, $hidotros, $hfinal, $hahorro);
				}
				$indice++;
			} else {
				$salida		= ($formato == OUT_TXT) ? "============\t\tERROR" : "<tr><td class='error' colspan='16'>ERROR $idValid</th></tr>";
			}
			$cuerpo		.= $salida;
			$inicial	= $final;
		}
		$encabezado		= ($formato == OUT_TXT) ? "" : "<thead><tr>";
		$encabezado		.= ($formato == OUT_TXT) ? "Num\t" : "<th>" . $xL->getT("TR.Numero") . "</th>";
		$encabezado		.= ($formato == OUT_TXT) ? "Fecha\t" : "<th>" . $xL->getT("TR.Fecha") . "</th>";
		$encabezado		.= ($formato == OUT_TXT) ? "Capital\t" : "<th>" . $xL->getT("TR.Capital") . "</th>";
		$encabezado		.= ($formato == OUT_TXT) ? "Interes\t" : "<th>" . $xL->getT("TR.Interes") . "</th>";
		if($SumaOtros >0 OR $this->mMontoOtrosCargos > 0){
			$encabezado		.= ($formato == OUT_TXT) ? "Otros\t" : "<th>" . $xL->getT("TR.otros") . "</th>";
		}
		$encabezado		.= ($formato == OUT_TXT) ? "Impuestos\t" : "<th>" . $xL->getT("TR.IVA") . "</th>";
		if($SumaAhorro >0){
			$encabezado		.= ($formato == OUT_TXT) ? "Ahorro\t" : "<th>" . $xL->getT("TR.Ahorro") . "</th>";
		}
		if($SumaDescto != 0){
			$encabezado		.= ($formato == OUT_TXT) ? "Descto\t" : "<th>" . $xL->getT("TR.Descuento") . "</th>";
		}
		$encabezado		.= ($formato == OUT_TXT) ? "Total\t" : "<th>" . $xL->getT("TR.Total") . "</th>";
		if($SumaOtros >0 OR $this->mMontoOtrosCargos > 0){
			$encabezado		.= ($formato == OUT_TXT) ? "ID Otros\t" : "<th>" . $xL->getT("TR.Clave Otros") . "</th>";
		}
		if($this->mSinDatosE == false){
			$encabezado		.= ($formato == OUT_TXT) ? "Dias\t" : "<th>" . $xL->getT("TR.Dias") . "</th>";
			$encabezado		.= ($formato == OUT_TXT) ? "Total\t" : "<th>" . $xL->getT("TR.Total CAPITAL") . "</th>";
		}
		if($this->mSoloTest == false){
			if($SinAnual == false){
				$encabezado		.= ($formato == OUT_TXT) ? "Anualidad\t" : "<th>" . $xL->getT("TR.ANUALIDAD") . "</th>";
			}
			$encabezado		.= ($formato == OUT_TXT) ? "FechaPago\t" : "<th>" . $xL->getT("TR.Fecha de pago") . "</th>";
			$encabezado		.= ($formato == OUT_TXT) ? "CapitalPago\t" : "<th>" . $xL->getT("TR.Capital Pagado") . "</th>";
			$encabezado		.= ($formato == OUT_TXT) ? "InteresPago\t" : "<th>" . $xL->getT("TR.Interes pagado") . "</th>";
			$encabezado		.= ($formato == OUT_TXT) ? "SumaPagos\t" : "<th>" . $xL->getT("TR.Suma Pagos") . "</th>";
		}
		if($this->mSinDatosE == false){
			$encabezado		.= ($formato == OUT_TXT) ? "SaldoInicial\t" : "<th>" . $xL->getT("TR.SALDO_INICIAL") . "</th>";
		}
		$encabezado		.= ($formato == OUT_TXT) ? "SaldoFinal\t" : "<th>" . $xL->getT("TR.SALDO_FINAL") . "</th>";
		
		$encabezado		.= ($formato == OUT_TXT) ? "" : "</tr></thead>";
		$xLog->add($encabezado);
		$xLog->add($cuerpo);
		
		//FOOT
		$tfoot	= "<tr>";
		$tfoot	.= "<th>" . "</th>";
		$tfoot	.= "<th>" . "</th>";
		$tfoot	.= "<th>" . getFMoney($SumaCapital) . "</th>";
		$tfoot	.= "<th>" . getFMoney($SumaInteres) . "</th>";
		if($SumaOtros >0 OR $this->mMontoOtrosCargos > 0){
			$tfoot	.= "<th>" .getFMoney($SumaOtros) . "</th>";
		}
		$tfoot	.= "<th>" .getFMoney($SumaIVA) . "</th>"; //impuestos
		if($SumaAhorro >0){
			$tfoot	.= "<th>" . "</th>";
		}
		if($SumaDescto != 0){
			$tfoot	.= "<th>" . "</th>"; //descuento
		}
		//(((($SumaIVA+$SumaInteres)/$SumaCapital)*100)/$this->mPagosAutorizados)
		$TotalPlan	= ($SumaAhorro+$SumaCapital-$SumaDescto+$SumaInteres+$SumaIVA+$SumaOtros);
		
		$tfoot	.= "<th>" . getFMoney($TotalPlan)  . "</th>"; //total
		//calcular CAT
		$xMat 					= new cMath();
		$cat 					= $xMat->cat( $this->mMontoAutorizado, $this->mArrSinIVA, $this->mPagosAutorizados, $this->mMaximoCAT );
		$this->mTasaCAT			= $cat;
		
		
		
		//======================================================================================================================
		if($this->mSinDatosE == false){
			$tfoot	.= "<th>" . "</th>";		 //Dias
			$tfoot	.= "<th>" . "($cat)</th>";	
			//$tfoot	.= "<th>" . "</th>";
		} 
			//$tfoot	.= "<th>" . "($cat)</th>";
		
		if($this->mSoloTest == false){
			if($SinAnual == false){
				$tfoot	.= "<th>" . "</th>"; //Anualidad foot
			}
			$tfoot	.= "<th>" . "</th>";
			$tfoot	.= "<th>" . "</th>";
			$tfoot	.= "<th>" . "</th>";
			$tfoot	.= "<th>" . "</th>";
		}
		if($this->mSinDatosE == false){
			$tfoot	.= "<th>" . "</th>"; //sdo inicial
		}
		$tfoot	.= "<th>" . "($cat)</th>"; //sdo final
		$tfoot	.= "</tr>";		
		//Guardar Datos del Plan
		if($this->mClaveDePlan > 0 AND $guardar == true){
			$xCred						= $this->OCredito();
			$dias_netos					= $xF->setRestarFechas($fecha, $xCred->getFechaDeMinistracion());
			$fecha_de_vencimiento		= $fecha;
			$fecha_primer_pago			= $this->mFechaPrimerPago;
			$fecha_de_mora				= $xF->setSumarDias($this->mDiasTolerancia, $this->mFechaPrimerPago);
			$vencimiento_dinamico		= $xF->setSumarDias($this->mDiasParaVencer, $fecha_de_mora); 
			//"interes_diario"	=> $interes_diario,
			//"contrato_corriente_relacionado" => $cuenta_captacion,
			//"fecha_ministracion"=> $fecha_ministracion,
			$arrUpdate					= array (
					"plazo_en_dias" 				=> $dias_netos,
					"dias_autorizados" 				=> $dias_netos,
					"fecha_vencimiento"			 	=> $fecha_de_vencimiento,
					"monto_parcialidad" 			=> $this->mParcialidadPres,
					"tipo_de_pago" 					=> $this->mTipoDePagos,
					"fecha_mora" 					=> $fecha_de_mora,
					"fecha_vencimiento_dinamico" 	=> $vencimiento_dinamico,
					"fecha_de_primer_pago" 			=> $this->mFechaPrimerPago,
					"tipo_de_dias_de_pago" 			=> $this->mTipoDePlanDePago,
					"ultimo_periodo_afectado"		=> $this->mUltimoPeriodoPag,
					"tasa_cat"						=> $this->mTasaCAT
			);

			$xCred->setUpdate($arrUpdate);
			if($xCred->getEsArrendamientoPuro() == true){
				$xRentas	= new cLeasingRentas();
				$xRentas->setCrearPorCredito($xCred->getClaveDeCredito());
			}
			$xMontos	= new cCreditosMontos($this->mCredito);
			if($xMontos->init() == true){
				$xMontos->setTotales(false,$TotalPlan, $SumaInteres);
			}
			$xBtn		= new cHButton();
			//<tr><th colspan='16'>" . $xBtn->getBasic("TR.Imprimir PLAN_DE_PAGOS " . $this->mClaveDePlan, "var xC=new CredGen();xC.getImprimirPlanPagos(" . $this->mClaveDePlan . ")", $xBtn->ic()->IMPRIMIR) . "</th></tr>

		}
		$salida		= ($formato == OUT_TXT) ? "============\t\tPLAN ID" . $this->mClaveDePlan : $tfoot;
		$xLog->add($salida);		
		return ($formato == OUT_TXT) ? $xLog->getMessages() : "<table>" . $xLog->getMessages() . "</table>";
	}
	function getTipoDeDiasDePagoPorDias($dia_1, $dia_2 = false, $dia_3 = false){
		$dia_1		= setNoMenorQueCero($dia_1);
		$dia_2		= setNoMenorQueCero($dia_2);
		$dia_3		= setNoMenorQueCero($dia_3);
		$this->mTipoDePlanDePago	= CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS;
		switch($this->mPeriocidadDePago){
			case CREDITO_TIPO_PERIOCIDAD_DIARIO:
				$this->mTipoDePlanDePago	= CREDITO_TIPO_DIAS_DE_PAGO_NATURAL;
			break;
			case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
				//$this->mTipoDePlanDePago	= CREDITO_TIPO_DIAS_DE_PAGO_NATURAL;
				$this->mTipoDePlanDePago	= (PS_DIA_DE_PAGO != $dia_1) ? CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS : CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS;
			break;
			case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
				$this->mTipoDePlanDePago	= (PS_DIA_DE_PAGO != $dia_1) ? CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS : CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS;
			break;
			case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
				$this->mTipoDePlanDePago	= (PQ_DIA_PRIMERA_QUINCENA != $dia_1 OR PQ_DIA_SEGUNDA_QUINCENA != $dia_2) ? CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS : CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS;
				break;
			case CREDITO_TIPO_PERIOCIDAD_DECENAL:
				$this->mTipoDePlanDePago	= ($dia_1 != 10 OR $dia_2 != 20 OR $dia_3 != 30) ? CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS : CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS;
				break;				
			default:
				$this->mTipoDePlanDePago	= (PM_DIA_DE_PAGO != $dia_1) ? CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS : CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS;
			break;				
		}
		return $this->mTipoDePlanDePago;
	}
	function setSoloTest($test = true){$this->mSoloTest = $test;}
	function getArrPagosSinIva(){ return $this->mArrSinIVA; }
	function setCapitalCAT($capital){ $this->mCapitalCAT = $capital; }
	function getTasaCAT(){ return $this->mTasaCAT;}
	private function getPeriodosAnnio($Frecuencia){
		$xFrecuencia	= 0;
		switch ($Frecuencia){
			case CREDITO_TIPO_PERIOCIDAD_ANUAL:
				$xFrecuencia	= 1;
				break;
			case CREDITO_TIPO_PERIOCIDAD_BIMESTRAL:
				$xFrecuencia	= 6;
				break;
			case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
				$xFrecuencia	= 26;
				break;
			case CREDITO_TIPO_PERIOCIDAD_DECENAL:
				$xFrecuencia	= 36;
				break;
			case CREDITO_TIPO_PERIOCIDAD_DIARIO:
				$xFrecuencia	= 365;
				break;
			case CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO:
				$xFrecuencia	= 1;
				break;
			case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
				$xFrecuencia	= 12;
				break;
			case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
				$xFrecuencia	= 24;
				break;
			case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
				$xFrecuencia	= 52;
				break;
			case CREDITO_TIPO_PERIOCIDAD_TRIMESTRAL:
				$xFrecuencia	= 4;
				break;
			default:
				$xFrecuencia	= 1;
				break;
		}
		return $xFrecuencia;
	}
	function setAnticipo($anticipo){ $this->mAnticipo = $anticipo; }
	function setValorResidual($valor){ $this->mValorResidual = $valor; }
	private function getDiaHabil($fecha){
		$fecha	= $fecha;
		$xF		= new cFecha();
		if($this->mConDiasInhabiles == false){
			for($i=1;$i<=5;$i++){
				$fecha	= $xF->getDiaHabil($fecha);
				if($xF->getEsHabil() == true){
					$i	= 100;
					break;
				}
			}
		}
		return $fecha;
	}
}

class cCreditoValidador {
	function __construct($proceso){
		
	}
	
}

class cCreditosMontos {
	private $mCredito		= 0;
	private $mInteresNormalCorriente	= 0;
	private $mInteresNormalDevengado	= 0;
	private $mInteresMoratorioCorriente	= 0;
	private $mInteresMoratorioDevengado	= 0;
	private $mIvaInteresNormal			= 0;
	private $mIvaOtros					= 0;
	private $mOtros						= 0;
	private $mCargosCbzaPorPag			= 0;
	private $mPenasPorPagar				= 0;
	private $mBonificaciones			= 0;
	private $mInteresNormalPagado		= 0;
	private $mPeriodoMin				= 0;
	private $mPeriodoMax				= 0;
	private $mPeriodoDeTrabajo			= 0;
	private $mIDCache					= "";
	
	private $mFechaDePrimerAtraso		= false;
	private $mFechaDeUltimoAtraso		= false;
	private $mCapitalPendiente			= 0;
	private $mOCredito					= null;
	private $mSeActualizo				= false;
	private $mInit						= false;
	private $mClave						= false;
	private $mObj						= null;
	private $mTotalPlanPend				= 0;
	private $mTotalPlanExige			= 0;	
	private $mTotalIntCalc				= 0;
	private $mTotalDispuesto			= 0;
	function __construct($credito = false){
		$this->mCredito	= setNoMenorQueCero($credito);
		$this->setIDCache($credito);
		
	}
	function getOCredito($data = false){
		if($this->mOCredito == null){
			$this->mOCredito	= new cCredito($this->mCredito);
			if($this->mOCredito->init($data) == false){
				$this->mOCredito = null;
			}
		}
		return $this->mOCredito;
	}
	function setIDCache($credito){
		$credito	= setNoMenorQueCero($credito);
		if($credito >0){
			$this->mIDCache	= TCREDITOS_MONTOS . "-" . $this->mCredito;
		}
	}
	function getInteresDevengadoMensual(){
		
	}
	function setActualizarPorLetras(){
		$xQL	= new MQL();
		$sql	= "SELECT
			`creditos_letras_del_dia`.`credito`,
			MIN(`creditos_letras_del_dia`.`fecha_de_pago`) AS `fecha_primer_atraso`,
			MAX(`creditos_letras_del_dia`.`fecha_de_pago`) AS `fecha_ultimo_atraso`,
			SUM(`creditos_letras_del_dia`.`capital`)       AS `capital`,
			SUM(`creditos_letras_del_dia`.`interes`)       AS `interes`,
			SUM(`creditos_letras_del_dia`.`iva`)           AS `iva`,
			SUM(`creditos_letras_del_dia`.`ahorro`)        AS `ahorro`,
			SUM(`creditos_letras_del_dia`.`otros`)         AS `otros`,
			SUM(`creditos_letras_del_dia`.`letra`)         AS `letra`,
			SUM(`creditos_letras_del_dia`.`mora`)          AS `moratorio`,
			SUM(`creditos_letras_del_dia`.`iva_moratorio`) AS `iva_moratorio`,
			SUM(`creditos_letras_del_dia`.`dias`)          AS `dias`,
			`creditos_letras_del_dia`.`tasa_de_mora`,
			`creditos_letras_del_dia`.`tasa_de_interes` 
		FROM
			`creditos_letras_del_dia` `creditos_letras_del_dia` 
		WHERE
			(`creditos_letras_del_dia`.`credito` =" . $this->mCredito .") 
		GROUP BY
			`creditos_letras_del_dia`.`credito`";
		$data	= $xQL->getDataRow($sql);
		if(isset($data["credito"])){
			if($this->init() == true){
				$this->mObj->interes_m_corr($data["moratorio"]);
				$this->mObj->interes_n_dev($data["interes"]);
				$this->mObj->capital_exigible($data["capital"]);
				$this->mObj->f_primer_atraso($data["fecha_primer_atraso"]);
				$this->mObj->f_ultimo_atraso($data["fecha_ultimo_atraso"]);
				$this->mObj->imptos_int_n($data["iva"]);
				$this->mObj->imptos_int_m($data["iva_moratorio"]);
				$this->mObj->otros_nc()->v($data["otros"]);
				$this->mObj->usuario(getUsuarioActual());
				$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
				
			}			
			$this->mInteresNormalDevengado		= $data["interes"];
			$this->mInteresMoratorioCorriente	= $data["moratorio"];
			$this->mFechaDePrimerAtraso			= $data["fecha_primer_atraso"];
			$this->mFechaDeUltimoAtraso			= $data["fecha_ultimo_atraso"];
			$this->mCapitalPendiente			= $data["capital"];
			$this->mIvaInteresNormal			= $data["iva"];
			$this->mIvaOtros					= $data["iva_moratorio"];
			$this->mOtros						= $data["otros"];
		}
	}
	function getInteresNormalDevengado(){ return $this->mInteresNormalDevengado; }
	function getInteresNormalCorriente(){ return $this->mInteresNormalCorriente; }
	function getInteresNormalPagado(){ return $this->mInteresNormalPagado; }
	function getInteresMoratorioCorriente(){ return $this->mInteresMoratorioCorriente; }
	function getInteresMoratorioDevengado(){ return $this->mInteresMoratorioDevengado; }
	function getCapitalPendiente(){return $this->mCapitalPendiente;}
	function getInteresMoratorioPendiente(){ return $this->mInteresMoratorioDevengado + $this->mInteresMoratorioCorriente;	}
	function getInteresNormalPendiente(){ return $this->mInteresNormalCorriente + $this->mInteresNormalDevengado; }
	function getInteresNormalCalculado(){ return $this->mTotalIntCalc; }
	function getPenasPorPagar(){return $this->mPenasPorPagar;}
	function getCargosCbzaXPag(){ return $this->mCargosCbzaPorPag; }
	function getTotalDispuesto(){return $this->mTotalDispuesto; }
	function getEsActualizado(){ return $this->mSeActualizo; }
	function getIVAPendiente(){ return $this->mIvaInteresNormal + $this->mIvaOtros; }
	function getPeriodoMinimo(){ return  $this->mPeriodoMin; }
	function getPeriodoMaximo(){ return $this->mPeriodoMax; }
	function getTotalPlanPend(){ return $this->mTotalPlanPend; }
	function getBonificaciones(){return $this->mBonificaciones; }
	function getTotalPlanExigible(){ return $this->mTotalPlanExige; }	
	function getOtros(){ return $this->mOtros; }
	function setPeriodoDeTrabajo($periodo = 0){ $this->mPeriodoDeTrabajo = $periodo; }

	function init($data = false){
		$xMM		= new cCreditos_montos();
		$xCache		= new cCache();
		if($this->mCredito >0){
			$xQL	= new MQL();
			$sql	= "SELECT * FROM `creditos_montos` WHERE `clave_de_credito`=" . $this->mCredito . " LIMIT 0,1";
			$rw		= $data;
			if(!is_array($rw)){
				$rw	= $xCache->get($this->mIDCache);
				if(!is_array($rw)){
					$rw		= $xQL->getDataRow($sql);
				}
			}
			$xF		= new cFecha();
			if(isset($rw["clave_de_credito"])){
				$xMM->setData($rw);
				$this->mInteresNormalDevengado		= $xMM->interes_n_dev()->v();
				$this->mInteresMoratorioCorriente	= $xMM->interes_m_corr()->v();
				$this->mFechaDePrimerAtraso			= $xMM->f_primer_atraso()->v();
				$this->mFechaDeUltimoAtraso			= $xMM->f_ultimo_atraso()->v();
				$this->mCapitalPendiente			= $xMM->capital_exigible()->v();
				$this->mIvaInteresNormal			= $xMM->imptos_int_n()->v();
				$this->mIvaOtros					= $xMM->imptos_otros()->v();
				$this->mOtros						= $xMM->otros_nc()->v();
				$this->mPenasPorPagar				= $xMM->penas()->v();
				$this->mCargosCbzaPorPag			= $xMM->cargos_cbza()->v();
				$this->mInteresNormalCorriente		= $xMM->interes_n_corr()->v();
				$this->mInteresNormalPagado			= $xMM->interes_n_pag()->v();
				$this->mTotalDispuesto				= $xMM->dispocision()->v();
				$this->mPeriodoMax					= $xMM->periodo_max()->v();
				$this->mPeriodoMin					= $xMM->periodo_min()->v();
				$this->mTotalPlanPend				= $xMM->sdo_exig_fut()->v();
				$this->mTotalPlanExige				= $xMM->sdo_exig_act()->v();
				$this->mBonificaciones				= $xMM->bonificaciones()->v();
				$this->mCredito						= $xMM->clave_de_credito()->v();
				$this->mTotalIntCalc				= $xMM->ints_tot_calc()->v();
				$this->mInit						= true;
				$this->setIDCache($this->mCredito);
				$xCache->set($this->mIDCache, $rw);
			} else {
				$this->mInit	= $this->setCrear();
			}
		}
		$this->mObj			= $xMM;
		return $this->mInit;
	}
	function setCrear(){
		$id		= false;
		$xQL	= new MQL();
		$tiempo	= time();
		$sql	= "INSERT INTO `creditos_montos` SET `clave_de_credito`=" . $this->mCredito . ", `sucursal`='" . getSucursal() . "',`usuario`=" . getUsuarioActual() . ", `marca_tiempo`=$tiempo, `marca_acceso`=$tiempo ";
		$id		= $xQL->setRawQuery($sql);
		if($id !== false){
			$this->init();
		}
		return ($id === false) ? false : true;
	}
	function setCuandoSeActualiza(){ 
		$xCache = new cCache(); $xCache->clean($this->mIDCache);
	}
	
	function setMontosCapital($TotalDispocision= false, $TotalPendiente = false, $TotalAbonos = false){
		
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
			if($TotalDispocision !== false){
				$TotalDispocision	= setNoMenorQueCero($TotalDispocision);
				$this->mObj->dispocision($TotalDispocision);
			}
			if($TotalPendiente !== false){
				$TotalPendiente		= setNoMenorQueCero($TotalPendiente);
				$this->mObj->saldo_plan($TotalPendiente);
				$this->mObj->capital_exigible($TotalPendiente);
			}
			if($TotalAbonos !== false){
				$TotalAbonos	= setNoMenorQueCero($TotalAbonos);
				$this->mObj->abonos_ops($TotalAbonos);
			}
			$this->mObj->marca_tiempo(time());
			$this->mObj->marca_acceso(time());
			$this->mObj->usuario(getUsuarioActual());
			$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
			$this->setCuandoSeActualiza();
		}
	}
	function setPeriodos($max = false, $min = false, $actual = false){
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
			if($max !== false){
				$max	= setNoMenorQueCero($max);
				$this->mObj->periodo_max($max);
			}
			if($min !== false){
				$min	= setNoMenorQueCero($min);
				$this->mObj->periodo_min($min);
			}
			if($actual !== false){
				$actual	= setNoMenorQueCero($actual);
				$this->mObj->periodo_last($actual);
			}
			$this->mObj->marca_tiempo(time());
			$this->mObj->marca_acceso(time());
			$this->mObj->usuario(getUsuarioActual());
			$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
			$this->setCuandoSeActualiza();
		}
	}
	function setBonificaciones($BonInts = false, $BonMora = false, $BonOtros = false){
		if($this->mObj == null){ $this->init(); }
		if($this->mInit == true){
			$actualizar	= false;
			if($BonInts !== false){ //si no es falso
				if($this->mObj->bon_int()->isEqualF($BonInts) == false){ //si es diferente
					$BonInts	= setNoMenorQueCero($BonInts);
					$this->mObj->bon_int($BonInts);
					$actualizar	= true;
				}
			}
			if($BonMora !== false){
				if( $this->mObj->bon_mora()->isEqualF($BonMora) == false){
					$BonMora	= setNoMenorQueCero($BonMora);
					$this->mObj->bon_mora($BonMora);
					$actualizar	= true;
				}
			}
			if($BonOtros !== false){
				if($this->mObj->bon_otros()->isEqualF($BonOtros) == false ){
					$BonOtros	= setNoMenorQueCero($BonOtros);
					$this->mObj->bon_otros($BonOtros);
					$actualizar	= true;
				}
			}
			if($actualizar == true){
				$bon		= setNoMenorQueCero($this->mObj->bon_int()->v()) + setNoMenorQueCero($this->mObj->bon_mora()->v()) + setNoMenorQueCero($this->mObj->bon_otros()->v());
				$this->mObj->bonificaciones( $bon );
				$this->mObj->marca_tiempo(time());
				$this->mObj->marca_acceso(time());
				$this->mObj->usuario(getUsuarioActual());
				$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
				$this->setCuandoSeActualiza();
			}
		}
	}
	function setTotales($TotalExigibleAct = false, $TotalExigibleFut = false, $TotalIntsFut = false){
		$actualizar		= false;
		if($this->mObj == null){ $this->init(); }
		if($this->mInit == true){
			if($TotalExigibleAct !== false){
				if($this->mObj->sdo_exig_act()->isEqualF($TotalExigibleAct) == false){
					$TotalExigibleAct	= setNoMenorQueCero($TotalExigibleAct);
					$this->mObj->sdo_exig_act($TotalExigibleAct);
					$actualizar			= true;
					
				}
			}
			if($TotalExigibleFut !== false){
				if($this->mObj->sdo_exig_fut()->isEqualF($TotalExigibleFut) == false){
					$TotalExigibleFut	= setNoMenorQueCero($TotalExigibleFut);
					$this->mObj->sdo_exig_fut($TotalExigibleFut);
					$actualizar			= true;
				}
			}
			if($TotalIntsFut !== false){
				if($this->mObj->ints_tot_calc()->isEqualF($TotalIntsFut) == false){
					$TotalIntsFut		= setNoMenorQueCero($TotalIntsFut);
					$this->mObj->ints_tot_calc($TotalIntsFut);
					$actualizar			= true;
				}
			}
			if($actualizar == true){
				$this->mObj->marca_tiempo(time());
				$this->mObj->marca_acceso(time());
				$this->mObj->usuario(getUsuarioActual());
				$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
				$this->setCuandoSeActualiza();
			}
		}
	}

	function setCargosYPenas($CargosCbza = false, $Penas = false){
		$actualizar		= false;
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
				
			if($CargosCbza !== false){
				if($this->mObj->cargos_cbza()->isEqualF($CargosCbza) == false){
					$CargosCbza		= setNoMenorQueCero($CargosCbza);
					$this->mObj->cargos_cbza($CargosCbza);
					$actualizar		= true;
				}
			}
			if($Penas !== false){
				if($this->mObj->penas()->isEqualF($Penas) == false){
					$Penas			= setNoMenorQueCero($Penas);
					$this->mObj->penas($Penas);
					$actualizar		= true;
					if($this->mPeriodoDeTrabajo > 0){
						$xParc	= new cCreditosLetraDePago($this->mCredito, $this->mPeriodoDeTrabajo);
						if($xParc->init() == true){
							$xParc->setCargosYPenas(false, $Penas);
						}
					}
				}
			}
			if($actualizar == true){
				$this->mObj->marca_tiempo(time());
				$this->mObj->marca_acceso(time());
				$this->mObj->usuario(getUsuarioActual());
				$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
				$this->setCuandoSeActualiza();
			}
		}
	}
	function setInteresesPagados($IntPNormal = 0, $IntPMora = 0){
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
			$this->mObj->interes_n_pag($IntPNormal);
			$this->mObj->interes_m_pag($IntPMora);
			$this->mObj->marca_tiempo(time());
			$this->mObj->marca_acceso(time());
			$this->mObj->usuario(getUsuarioActual());
			$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
			$this->setCuandoSeActualiza();
		}
	}
	function setInteresesCorrientes($IntNormal	= 0, $IntMora = 0){
		$actualizar		= false;
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
			$this->mObj->interes_n_corr($IntNormal);
			$this->mObj->interes_m_corr($IntMora);
			$this->mObj->marca_tiempo(time());
			$this->mObj->marca_acceso(time());
			$this->mObj->usuario(getUsuarioActual());
			$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
			$this->setCuandoSeActualiza();
		}
	}
	function setInteresesDevengados($IntDNormal = 0, $IntDMora = 0){
		$actualizar		= false;
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
			if($this->mObj->interes_n_dev()->isEqualF($IntDNormal) == false AND $this->mObj->interes_m_dev()->isEqualF($IntDMora) == false){
				$this->mObj->interes_n_dev($IntDNormal);
				$this->mObj->interes_m_dev($IntDMora);
				$this->mObj->marca_tiempo(time());
				$this->mObj->marca_acceso(time());
				$this->mObj->usuario(getUsuarioActual());
				$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
				$this->setCuandoSeActualiza();
			}
		}
	}	
	function setActualizarIVA(){
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
			//Ejecutar un query que actualice
			$this->setCuandoSeActualiza();
		}
	}
	function setTasasIVA($IvaIntNorm = false, $IvaIntMora = false, $IvaOtros = false){
		if($this->mObj == null){$this->init();}
		if($this->mInit == true){
			$this->mObj->t_iva_int_n($IvaIntNorm);
			$this->mObj->t_iva_m($IvaIntMora);
			$this->mObj->t_iva_o($IvaOtros);
			$this->mObj->marca_acceso(time());
			$this->mObj->usuario(getUsuarioActual());
			$this->mObj->query()->update()->save($this->mObj->idcreditos_montos()->v());
			$this->setCuandoSeActualiza();
		}
	}	
}

class cCreditosPresupuesto {
	private $mPersona	= 0;
	private $mClave		= false;
	private $mFecha		= false;
	public $PENDIENTE	= 0;
	public $CERRADO		= 1;
	private $mObj		= null;
	private $mIsInit	= false;
	private $mMessages	= "";
	function __construct($clave = false, $persona = false){
		$this->mPersona	= setNoMenorQueCero($persona);
		$this->mClave	= setNoMenorQueCero($clave);
	}
	function init($datos = false){
		$xQL		= new MQL();
		$xF			= new cFecha();
		$datos		= (is_array($datos)) ? $datos : $xQL->getDataRow("SELECT * FROM `creditos_presupuestos` WHERE `clave_de_presupuesto`= " . $this->mClave  . " LIMIT 0,1");
		$this->mObj	= new cCreditos_presupuestos();
		$this->mObj->setData($datos);
		$this->mFecha	= $xF->getFechaISO($this->mObj->fecha_de_elaboracion()->v());
		$this->mPersona	= $this->mObj->clave_de_persona()->v();
		$this->mIsInit	= true;
		return $this->mIsInit;
	}
	function getObj(){
		if($this->mObj == null){ $this->init(); }
		return $this->mObj;
	}
	function add($fecha = false, $notas = "", $oficial = false, $persona = false ){
		$xF				= new cFecha();
		$fecha			= $xF->getFechaISO($fecha);
		$oficial		= setNoMenorQueCero($oficial);
		$oficial		= ($oficial <= 0) ? getUsuarioActual() : $oficial;
		$persona		= setNoMenorQueCero($persona);
		$persona		= ($persona <= 0) ? $this->mPersona : $persona;
		$this->mPersona	= $persona;
				
		$xPre			= new cCreditos_presupuestos();
		$xPre->clave_de_persona($this->mPersona);
		$xPre->estado_actual($this->PENDIENTE);
		$xPre->fecha_de_elaboracion($fecha);
		$xPre->idusuario($oficial);
		$xPre->sucursal(getSucursal());
		$xPre->total_presupuesto(0);
		$xPre->notas($notas);
		$lid		= $xPre->query()->getLastID();
		$xPre->clave_de_presupuesto($lid);
		$rs 		= $xPre->query()->insert()->save();
		if($rs == false){
			$rs		= false;
		} else {
			$rs		= true;
			$this->mClave	= $lid;
		}
		return $rs;
	}
	function getID(){ return $this->mClave; }
	function addItem($proveedor, $destino, $monto, $observaciones = "", $fecha = false, $persona = false){
		
		
		$xF			= new cFecha();
		$fecha		= ($fecha == false) ? $this->mFecha : $fecha;
		$fecha		= $xF->getFechaISO($fecha);
		
		$clave		= $this->mClave;
		$persona	= setNoMenorQueCero($persona);
		$persona	= ($persona <= 0) ? $this->mPersona : $persona;
		$this->mPersona	= $persona;
		
		$xPre		= new cCreditos_destino_detallado();
		$xPre->clave_de_empresa($proveedor);
		$xPre->clave_de_persona($persona);
		$xPre->clave_de_presupuesto($clave);
		$xPre->fecha_de_pago($fecha);
		$xPre->idusuario(getUsuarioActual());
		$xPre->clave_de_destino($destino);
		$xPre->monto($monto);
		$xPre->observaciones($observaciones);
		$xPre->sucursal(getSucursal());
		//$xPre->idusuario()
		$xPre->idcreditos_destino_detallado( $xPre->query()->getLastID());
		$rs	= $xPre->query()->insert()->save();
		$this->setTotal();
		return $rs;
	}
	function setTotal($monto = false){
		$monto	= setNoMenorQueCero($monto);
		if($monto <= 0){
			$xQL	= new MQL();
			$D		= $xQL->getDataRow("SELECT SUM(`monto`) AS 'monto' FROM `creditos_destino_detallado` WHERE `clave_de_presupuesto`=" . $this->mClave .  "");
			$monto	= setNoMenorQueCero($D["monto"]);
		}
		$this->getObj()->total_presupuesto($monto);
		$res	= $this->getObj()->query()->update()->save($this->mClave);
	}
	function setCerrado($idcredito = false, $monto = 0){
		$xQL		= new MQL();
		$idcredito	= setNoMenorQueCero($idcredito);
		$monto		= setNoMenorQueCero($monto);
		$this->getObj()->estado_actual($this->CERRADO);
		$res		= $this->mObj->query()->update()->save($this->mClave);
		//Actualizar ITEMS
		$sql	= "UPDATE `creditos_destino_detallado` SET `credito_vinculado`=$idcredito WHERE `clave_de_presupuesto`= " . $this->mClave;
		$xQL->setRawQuery($sql);
	}
	function getClaveDePersona(){ return $this->mPersona; }
	function getFicha(){
		$xLng		= new cLang();
		$xF			= new cFecha();
		$cantidad	= new cCantidad($this->getObj()->total_presupuesto()->v());
		$txt	= "<fieldset><legend>|&nbsp;&nbsp;&nbsp;" . $xLng->getT("TR.Presupuesto") . "&nbsp;&nbsp;&nbsp;|</legend><table><tbody>
					<tr>
						<th class='izq'>" . $xLng->getT("TR.Clave") . "</th>
						<td>" . $this->getObj()->clave_de_presupuesto()->v() . "</td>
						<td colspan='2'>" . $this->getObj()->notas()->v() . "</td>								
					</tr>
					<tr>
						<th class='izq'>" . $xLng->getT("TR.Fecha") . "</th>
						<td>" . $xF->getFechaMediana($this->getObj()->fecha_de_elaboracion()->v()) . "</td>
						<th class='izq'>" . $xLng->getT("TR.Monto") . "</th>
						<td colspan='2'>" . $cantidad->moneda() . "</td>								
					</tr>								
				</tbody></table></fieldset>";
		return $txt;
	}
	function setEliminar(){
		$xQL		= new MQL();
		$res		= false;
		$xLog		= new cCoreLog();
		$credito	= $this->getCreditoVinculado();
		if($credito > DEFAULT_CREDITO){
			$res	= false;
			$xLog->add("ERROR\tExiste   Credito $credito relacionado\r\n", $xLog->DEVELOPER);
		} else {
			//puedes eliminar
			$res 	= $xQL->setRawQuery("DELETE FROM `creditos_destino_detallado` WHERE `clave_de_presupuesto`=" . $this->mClave . "");
			if($res == false){
				$xLog->add("ERROR\tNo se elimina el Detalle\r\n", $xLog->DEVELOPER);
			} else {
				$res= true;
				$xQL->setRawQuery("DELETE FROM `creditos_presupuestos` WHERE `clave_de_presupuesto`=" .  $this->mClave . "");
				//$xLog->add("ERROR\tNo se elimina el Detalle\r\n", $xLog->DEVELOPER);
			}
		}
		$this->mMessages	.= $xLog->getMessages();
		return $res;
	}
	function setCancelar(){
		$xQL		= new MQL();
		$res		= false;
		$xLog		= new cCoreLog();
		$credito	= $this->getCreditoVinculado();
		$xLng		= new cLang();
		$cancel		= $xLng->getT("TR.Cancelado");
		if($credito > DEFAULT_CREDITO){
			$res	= false;
			$xLog->add("ERROR\tExiste Credito $credito relacionado\r\n", $xLog->DEVELOPER);
		} else {
			//puedes eliminar
			$res 	= $xQL->setRawQuery("UPDATE `creditos_destino_detallado` SET `estado_actual`=1, `notas_del_pago`= '" . $cancel .  "' WHERE `clave_de_presupuesto`=" . $this->mClave . "");
			if($res == false){
				$xLog->add("ERROR\tError al Cancelar el Detalle\r\n", $xLog->DEVELOPER);
			} else {
				$res= true;
				$xQL->setRawQuery("UPDATE `creditos_presupuestos` SET `estado_actual`=" . $this->CERRADO . ",`notas` = CONCAT(`notas`, ':', '" . $cancel . "')  WHERE `clave_de_presupuesto`=" .  $this->mClave . "");
				//$xLog->add("ERROR\tNo se elimina el Detalle\r\n", $xLog->DEVELOPER);
			}
		}
		$this->mMessages	.= $xLog->getMessages();
		return $res;		
	}
	function getMessages($put = OUT_TXT){ $xH	 = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function getCreditoVinculado(){
		$xQL		= new MQL();
		$sqlCred	= "SELECT * FROM `creditos_solicitud` WHERE (`creditos_solicitud`.`operacion_origen` =" . $this->mClave . ") AND (`creditos_solicitud`.`tipo_de_origen` =" . iDE_PRESUPUESTO . ") LIMIT 0,1";
		$D			= $xQL->getDataRow($sqlCred);
		return setNoMenorQueCero($D["numero_solicitud"]);
	}
	function getTasaPonderada(){
		$xQL		= new MQL();
		$ponderado	= 0;
		$sql	= "
	SELECT SUM(`monto`) AS 'total', SUM((`monto`*`socios_aeconomica_dependencias`.`comision_por_encargo`)) AS `descuentos`,
		COUNT(`creditos_destino_detallado`.`clave_de_empresa`)       AS `empresas`,
		(`socios_aeconomica_dependencias`.`comision_por_encargo`) AS `comisiones`
		FROM
		`creditos_destino_detallado` `creditos_destino_detallado` LEFT OUTER JOIN `socios_aeconomica_dependencias` `socios_aeconomica_dependencias` 
		ON `creditos_destino_detallado`.`clave_de_empresa` = `socios_aeconomica_dependencias`.`clave_de_persona`
		WHERE (`creditos_destino_detallado`.`clave_de_presupuesto` =" . $this->mClave . ") ORDER BY `socios_aeconomica_dependencias`.`comision_por_encargo` ";
		$D		= $xQL->getDataRow($sql);
		if(isset($D["descuentos"])){
			$tasa			= ($D["descuentos"] / $D["total"]);// - 1;
			if($tasa != 0){ $ponderado	= $tasa; }
		}
		return $ponderado;
	}
}
class cCreditosPresupuestoDetalle {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	function __construct($clave = false){
		$this->mClave	= setNoMenorQueCero($clave);
	}
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `creditos_destino_detallado` WHERE `idcreditos_destino_detallado`=". $this->mClave);
		if(isset($data["idcreditos_destino_detallado"])){
			$this->mObj		= new cCreditos_destino_detallado();
			$this->mObj->setData($data);
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function setPagado($cheque, $notas = "", $fecha = false){
		$xF		= new cFecha();
		$fecha	= $xF->getFechaISO($fecha);
		if($this->mObj == null ){ $this->init(); }
		$this->mObj->cheque_de_pago($cheque);
		$this->mObj->fecha_de_pago($fecha);
		$this->mObj->notas_del_pago($notas);
		$this->mObj->estado_actual(1);//Pagado
		$rs 	= $this->mObj->query()->update()->save($this->mClave);
		return ($rs == false) ? false : true;
	}
}


class cEmpresasCobranzaPeriodos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mMessages	= "";
	private $mClaveEmpresa	= 0;
	private $mPeriocidad	= 0;
	private $mFechaInicial	= false;
	private $mFechaFinal	= false;
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$xF		= new cFecha();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `empresas_operaciones` WHERE `idempresas_operaciones`=". $this->mClave);
		if(isset($data["idempresas_operaciones"])){
			$this->mObj		= new cEmpresas_operaciones(); //Cambiar
			$this->mObj->setData($data);
			$this->mClave	= $this->mObj->idempresas_operaciones()->v();
			$this->mClaveEmpresa	= $this->mObj->clave_de_empresa()->v();
			$this->mPeriocidad		= $this->mObj->periocidad()->v();
			$this->mFechaFinal		= $xF->getFechaISO($this->mObj->fecha_final()->v());
			$this->mFechaInicial	= $xF->getFechaISO($this->mObj->fecha_inicial()->v());
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= ""; }
	function getClaveDeEmpresa(){ return $this->mClaveEmpresa; }
	function getFechaInicial(){ return $this->mFechaInicial; }
	function getFechaFinal(){ return $this->mFechaFinal; }
	function getFrecuencia(){ return $this->mPeriocidad;}
	function getCobrados(){
		$ql		= new MQL();
		$id		= $this->mClave;
		$sql	= "SELECT COUNT(`idempresas_cobranza`) AS 'cobrados' FROM `empresas_cobranza` WHERE `clave_de_nomina` = $id AND `estado`=0";
		$datos	= $ql->getDataRow($sql);
		return (isset($datos["cobrados"])) ? setNoMenorQueCero($datos["cobrados"]) : 0;
	}
	function setCancelarOperacion($credito, $periodo, $observaciones = "", $recibo = false){
		$res	= false;
		$xDet	= new cEmpresasCobranzaDetalle(false, $this->mClave);
		if($xDet->initByCreditoID($credito, $periodo) == true){
			$res= $xDet->setPagado($observaciones, $recibo);
		}
		return $res;
	}
	function setRevertirOperacion($credito, $periodo, $observaciones = ""){
		$res	= false;
		$xDet	= new cEmpresasCobranzaDetalle(false, $this->mClave);
		if($xDet->initByCreditoID($credito, $periodo) == true){
			$res= $xDet->setRevertirPago($observaciones);
		}
		return $res;
	}	
	function setEliminar(){
		$ql		= new MQL();
		$id		= $this->mClave;		
		$sql	= "DELETE FROM `empresas_operaciones` WHERE `idempresas_operaciones` = $id ";
		$sql2	= "DELETE FROM `empresas_cobranza` WHERE clave_de_nomina=$id ";
		$ql->setRawQuery($sql);
		$ql->setRawQuery($sql2);
	}
	function setCerrar(){
		$ql		= new MQL();
		$id		= $this->mClave;
		$sql	= "UPDATE `empresas_cobranza` SET `estado` = 0 WHERE `clave_de_nomina`=$id ";
		$rs		= $ql->setRawQuery($sql);
		$this->mMessages	.= "WARN\tNomina cerrada con id $id\r\n";
		return ($rs === false ) ? false : true;
	}
	function initByDatos($empresa, $frecuencia, $periodo, $fechaInicial){
		$xQL	= new MQL();
		$xF		= new cFecha();
		$anno	= $xF->anno($fechaInicial);
		$DD		= $xQL->getDataRow("SELECT * FROM `empresas_operaciones` WHERE `clave_de_empresa`=$empresa AND `tipo_de_operacion`=1 
				AND `periodo_marcado`=$periodo AND `periocidad`=$frecuencia AND  DATE_FORMAT(`fecha_de_cobro`,'%Y') = $anno LIMIT 0,1");
		return $this->init($DD);
	}
}


class cEmpresasCobranzaDetalle {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mMessages	= "";
	private $mPeriodo	= false;
	private $mCredito	= false;
	private $mIDNomina	= false;
	private $mMontoCobro= 0;
	private $mReciboPag	= 0;
	private $mFechaPag	= false;
	private $mParcialidad=0;
	
	function __construct($clave = false, $nomina = false){
		$this->mClave	= setNoMenorQueCero($clave);
		$this->mIDNomina= setNoMenorQueCero($nomina);
		$this->mFechaPag= fechasys();
	}
	function initByCreditoID($credito,  $letra ){
		$credito			= setNoMenorQueCero($credito);
		$letra				= setNoMenorQueCero($letra);
		$this->mCredito		= $credito;
		$this->mParcialidad = $letra;
		$xQL			= new MQL();
		$ByNomina		= ($this->mIDNomina > 0) ? " AND (`empresas_cobranza`.`clave_de_nomina` =" . $this->mIDNomina . ")   " : "";
		$sql			= "SELECT * FROM `empresas_cobranza` WHERE (`empresas_cobranza`.`clave_de_credito` =$credito) AND	(`empresas_cobranza`.`parcialidad` =$letra) $ByNomina LIMIT 0,1";
		
		$data			= $xQL->getDataRow($sql);
		if(isset($data["idempresas_cobranza"])){
			$xF					= new cFecha();
			$this->mClave		= $data["idempresas_cobranza"];
			$this->mIDNomina	= $data["clave_de_nomina"];
			$this->mMontoCobro	= $data["monto_enviado"];
			$this->mReciboPag	= $data["recibo"];
			$this->mFechaPag	= $xF->getFechaByInt($data["tiempocobro"]);
			$this->init($data);
			$this->mInit		= true;
		}
		$xQL					= null;
		return $this->mInit;
	}
	function getMontoEnviado(){ return $this->mMontoCobro; }
	function getFechaPagado(){ return $this->mFechaPag; }
	function getReciboPagado(){ return $this->mReciboPag; }
	function getClaveUnica(){ return $this->mClave; }
	function getClaveNomina(){ return $this->mIDNomina; }
	function setActualizarMontoEnviado($monto, $observaciones = "", $recibo = false){
		$recibo	= setNoMenorQueCero($recibo);
		$xQL	= new MQL();
		$tt		= time();
		$monto	= setNoMenorQueCero($monto);
		$idnomina= $this->mClave;
		$rs		= $xQL->setRawQuery("UPDATE empresas_cobranza SET `monto_enviado` = $monto, observaciones = CONCAT(observaciones, '$observaciones'), `recibo`=$recibo, `tiempocobro`=$tt WHERE `empresas_cobranza`.`idempresas_cobranza` = $idnomina ");
		$xQL	= null;
		return ($rs === false) ? false : true;		
	}
	function init($data = false){
		$xQL		= new MQL();
		$idnomina	= $this->mClave;
		$data		= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `empresas_cobranza` WHERE `idempresas_cobranza`= $idnomina ");
		
		if(isset($data["idempresas_cobranza"])){
			$xF					= new cFecha();
			$this->mObj			= new cEmpresas_cobranza();
			$this->mObj->setData($data);
			$this->mClave		= $this->mObj->idempresas_cobranza()->v();
			$this->mIDNomina	= $this->mObj->clave_de_nomina()->v();
			$this->mMontoCobro	= $this->mObj->monto_enviado()->v();
			$this->mReciboPag	= $this->mObj->recibo()->v();
			$this->mFechaPag	= $xF->getFechaByInt($this->mObj->tiempocobro()->v());
			$this->mInit		= true;
		}
		$xQL		= null;
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj			= null; $this->mMessages	= ""; }
	function setPagado($observaciones = "", $recibo = false){
		$recibo	= setNoMenorQueCero($recibo);
		$xQL	= new MQL();
		$tt		= time();
		$idnomina= $this->mClave;
		$sql	= "UPDATE empresas_cobranza SET `estado` = 0, `observaciones` = CONCAT(`observaciones`,' ', '$observaciones'), `recibo`=$recibo, `tiempocobro`=$tt WHERE `empresas_cobranza`.`idempresas_cobranza` =$idnomina";
		$rs		= $xQL->setRawQuery($sql);
		return ($rs === false) ? false : true;
	}
	function setRevertirPago($observaciones = ""){
		$recibo = 0;
		$tt		= time();
		$xQL	= new MQL();
		$idnomina= $this->mClave;
		$rs		= $xQL->setRawQuery("UPDATE empresas_cobranza SET `estado` = 1, `observaciones` = '$observaciones', `recibo`=$recibo, `tiempocobro`=$tt	 WHERE `empresas_cobranza`.`idempresas_cobranza` =$idnomina ");
		$xQL	= null;
		//Agregar Nota de Cancelacion
		return ($rs === false) ? false : true;
	}
	function add($letra, $monto, $notas = "", $credito = false, $idnomina = false){
		$idnomina		= setNoMenorQueCero($idnomina);
		$credito		= setNoMenorQueCero($credito);
		$idnomina		= ($idnomina == 0) ? $this->mIDNomina : $idnomina;
		$credito		= ($credito == 0) ? $this->mCredito : $credito;
		
		$letraAnterior	= $letra - 1;
		$sqlNS			= "SELECT getSaldoPendienteDesdeLetra($credito, $letraAnterior) AS 'saldo_anterior' ";
		$sdo_anterior	= mifila($sqlNS, "saldo_anterior");
		//Neutralizar Letra Anterior
		$xQL			= new MQL();
		$xQL->setRawQuery("UPDATE `empresas_cobranza` SET `estado` = 0,`observaciones`=CONCAT(`observaciones`, ' ', 'Reenvio Nomina $idnomina') WHERE `clave_de_credito`=$credito AND `parcialidad`=$letra");
		$XQL			= null;
		
		$xO				= new cEmpresas_cobranza();
		
		$xO->idempresas_cobranza( 'NULL' );
		$xO->clave_de_credito($credito);
		$xO->clave_de_nomina($idnomina);
		$xO->monto_enviado($monto);
		$xO->observaciones($notas);
		$xO->parcialidad($letra);
		$xO->saldo_inicial($sdo_anterior);
		
		$action			= $xO->query()->insert();
		$id				= $action->save();
		$id				= setNoMenorQueCero($id);
		if($id > 0){
			$this->mParcialidad = $letra;
		}
		$action			= null;
		$xO				= null;
		return $id;
	}
	function getExisteEnOtraNomina($letra, $credito, $empresa, $periodoNomina, $frecuencia ){
		$xQL			= new MQL();
		
		$credito		= setNoMenorQueCero($credito);
		$credito		= ($credito == 0) ? $this->mCredito : $credito;
		
		$LSql	= "SELECT COUNT(`empresas_cobranza`.`parcialidad`) AS `items`, MAX(`clave_de_nomina`) AS `nomina`  FROM `empresas_operaciones` `empresas_operaciones` INNER JOIN `empresas_cobranza` `empresas_cobranza` ON `empresas_operaciones`.`idempresas_operaciones` = `empresas_cobranza`.`clave_de_nomina`
		WHERE (`empresas_cobranza`.`clave_de_credito` =$credito) AND (`empresas_cobranza`.`parcialidad` =$letra)
		AND (`empresas_operaciones`.`clave_de_empresa` =$empresa)
		AND (`empresas_operaciones`.`periodo_marcado` !=$periodoNomina) AND (`empresas_operaciones`.`periocidad` =$frecuencia) AND (`empresas_operaciones`.`tipo_de_operacion` =1)";
		$DDEnv	= $xQL->getDataRow($LSql);
		$items	= 0;
		if(isset($DDEnv["items"])){
			$items	= setNoMenorQueCero($DDEnv["items"]);
			if($items >0){
				$this->mMessages .= "WARN\tLa Parcialidad $letra se ha enviado $items veces, existe en la Nomina " . $DDEnv["nomina"] . " por ejemplo\r\n";
			}
			$DDEnv	= null;
			$xQL	= null;
		}
		return $items;
	}
}

class cCreditosPlanDePagos{
	private $mCredito	= false;
	function __construct($credito = false){
		$this->mCredito	= setNoMenorQueCero($credito);
	}
	function addLetra(){}
	function clear(){
		$xQL	= new MQL();
		$sql	= "DELETE FROM `creditos_plan_de_pagos` WHERE `clave_de_credito`=" . $this->mCredito;
		$xQL->setRawQuery($sql);
	}
}
class cCreditosLetraDePago {
	private $mCredito		= false;
	private $mPeriodo		= false;
	private $mFechaDePago	= false;
	private $mInit			= false;
	private $mCapital		= 0;
	private $mInteres		= 0;
	private $mImpuestos		= 0;
	private $mOtros			= 0;
	private $mTipoOtros		= 0;
	private $mAhorro		= 0;
	private $mTotal			= 0;
	private $mPenas			= 0;
	private $mMora			= 0;
	
	function __construct($credito, $periodo){
		$this->mCredito	= $credito;
		$this->mPeriodo	= $periodo;
	}
	function add($FechaDePago, $capital, $interes, $impuestos = false, $otros = false, $codigo_otros = false, $saldo_inverso = false, $ahorro = 0){
		$otros			= setNoMenorQueCero($otros);
		$codigo_otros	= setNoMenorQueCero($codigo_otros);
		$saldo_inverso	= setNoMenorQueCero($saldo_inverso);
		$capital		= setNoMenorQueCero($capital);
		$interes		= setNoMenorQueCero($interes);
		$ahorro			= setNoMenorQueCero($ahorro);
		$xT				= new cCreditos_plan_de_pagos();
		$xQL			= new MQL();
		$credito		= $this->mCredito;
		$periodo		= $this->mPeriodo;
		$sqlD			= "DELETE FROM creditos_plan_de_pagos WHERE clave_de_credito = $credito AND numero_de_parcialidad=$periodo";
		$xQL->setRawQuery($sqlD);
		$sucursal		= getSucursal();
		$sql	= "INSERT INTO creditos_plan_de_pagos
					(clave_de_credito, numero_de_parcialidad, fecha_de_pago, capital, interes, impuesto, otros, otros_codigo, saldo_inverso, sucursal, ahorro) 
    				VALUES
					($credito, $periodo, '$FechaDePago', $capital, $interes, $impuestos, $otros, $codigo_otros, $saldo_inverso, '$sucursal', $ahorro)";
		$xQL->setRawQuery($sql);
		
	}
	function init($data = false){
		$load		= false;
		if(!is_array($data)){
			$load	= true;
		} else {
			if(!isset($data["clave_de_credito"])){
				$load	= true;
			}
		}
		if($load == true){
			$xQL		= new MQL();
			$credito	= $this->mCredito;
			$periodo	= $this->mPeriodo;
			$sql		= "SELECT * FROM `creditos_plan_de_pagos` WHERE `clave_de_credito` = $credito AND `numero_de_parcialidad`=$periodo LIMIT 0,1";
			$data		= $xQL->getDataRow($sql);
		}
		
		if(isset($data["clave_de_credito"])){
			$this->mInit		= true;
			$this->mFechaDePago	= $data["fecha_de_pago"]; 
			$this->mCapital		= $data["capital"];
			$this->mInteres		= $data["interes"];
			$this->mImpuestos	= $data["impuesto"];
			$this->mOtros		= $data["otros"];
			$this->mTipoOtros	= $data["otros_codigo"];
			$this->mAhorro		= $data["ahorro"];
			$this->mPenas		= $data["penas"];
			$this->mMora		= $data["mora"];
			
			$this->mTotal		= $this->mCapital + $this->mInteres + $this->mImpuestos + $this->mOtros + $this->mAhorro + $this->mMora + $this->mPenas;
		}
		return $this->mInit;
	}
	function getTotal(){return $this->mTotal;}
	function getFechaDePago(){ return $this->mFechaDePago; }
	function getCapital(){return $this->mCapital;}
	function getInteres(){return $this->mInteres;}
	function getImpuesto(){return $this->mImpuestos;}
	function getOtros(){return $this->mOtros;}
	function getTipoOtros(){return $this->mTipoOtros;}
	function getAhorro(){return $this->mAhorro;}
	function getMora(){ return $this->mMora; }
	function getPenas(){ return $this->mPenas; }
	function setCargosYPenas($cargos = false, $penas = false, $mora = false){
		if($penas !== false){
			$xQL	= new MQL();
			$penas	= setNoMenorQueCero($penas);
			$mora	= setNoMenorQueCero($mora);
			$xQL->setRawQuery("UPDATE `creditos_plan_de_pagos` SET `penas`=$penas, `mora`=$mora WHERE `clave_de_credito`=" . $this->mCredito . " AND `numero_de_parcialidad`=" . $this->mPeriodo);
		}
	}
}

class cCreditosOperaciones {
	private $mNumeroCredito		= 0;
	private $mAPagos			= array();
	private $mEstatusP			= array();
	private $mInit				= false;
	private $mOMontos			= null;
	private $mProximaLetraC		= 0;	//Proxima Letra con Capital
	private $mProximaLetraI		= 0;	//Proxima Letra con Interes
	private $mUltimaLetraC		= 0;	//Proxima Letra con Capital
	private $mUltimaLetraI		= 0;	//Proxima Letra con Interes	
	private $mTotalDispuesto	= 0;
	private $mTotalDesembolso	= 0;
	private $mTotalAbonos		= 0;
	private $mTotalPendiente	= 0;
	private $mTotalPendInts		= 0;
	
	private $mTotalBonInt		= 0;
	private $mTotalBonMora		= 0;
	private $mTotalBonOtros		= 0;
	private $mTotalPlanPend		= 0;
	private $mTotalPlanExige	= 0;
	private $mNumeroAbonos		= 0;
	private $mInitMvtosHoy		= false;
	
	private $mTieneAbonos		= false;
	private $mTieneCargosO		= false;
	private $mTieneCargosC		= false;
	private $mTieneMinistra		= false;
	private $mTieneBonif		= false;
	
	function __construct($credito = false){	$this->mNumeroCredito	= setNoMenorQueCero($credito);	}
	function getCargosDeCobranza(){
		if($this->mInit == false){$this->init(); }
		return $this->mAPagos[OPERACION_CLAVE_CARGOS_COBRANZA];
	}
	function getComisionPorApertura(){
		if($this->mInit == false){$this->init(); }
		return $this->mAPagos[OPERACION_CLAVE_COMISION_APERTURA];		
	}
	function init(){
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord("SELECT * FROM `operaciones_tipos`");
		foreach ($rs as $rw){
			$this->mAPagos[$rw["idoperaciones_tipos"]]	= 0;
		}
		$sql	= "SELECT * FROM `operaciones_mvtos` WHERE `docto_afectado`=" . $this->mNumeroCredito;
		$rs		= $xQL->getDataRecord($sql);
		$xOps	= new cOperaciones_mvtos();
		foreach ($rs as $rw){
			$xOps->setData($rw);
			if($xOps->tipo_operacion()->v() == OPERACION_CLAVE_CARGOS_COBRANZA AND $xOps->estatus_mvto()->v() != 40){ $xOps->afectacion_real(0); } //resetear si es gastos de cobranza
			$this->mAPagos[$xOps->tipo_operacion()->v()]	+= $xOps->afectacion_real()->v();
		}
		$rs				= null;
		$xOps			= null;
		$this->mInit	= true;
		return $this->mInit;
	}
	function initAcumOpsSdo($fecha	= false, $DD = false){
		$xF		= new cFecha();
		$xQL	= new MQL();
		$fecha	= $xF->getFechaISO($fecha);
		$init	= false;
		$sql	= "SELECT
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 110,`operaciones_mvtos`.`afectacion_real`,0)) AS 'desembolso',
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 117,`operaciones_mvtos`.`afectacion_real`,0)) AS 'disposicion',
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 120,`operaciones_mvtos`.`afectacion_real`,0)) AS 'abonos',
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 410,`operaciones_mvtos`.`afectacion_real`,0)) AS 'pendiente',
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 411,`operaciones_mvtos`.`afectacion_real`,0)) AS 'pendiente_interes',
				
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 802,`operaciones_mvtos`.`afectacion_real`,0)) AS 'bon_int',
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 801,`operaciones_mvtos`.`afectacion_real`,0)) AS 'bon_mora',
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 803,`operaciones_mvtos`.`afectacion_real`,0)) AS 'bon_otros',
				
				
				MIN(IF(`operaciones_mvtos`.`tipo_operacion`  = 410 AND `operaciones_mvtos`.`afectacion_real` >0,`operaciones_mvtos`.`periodo_socio`,99999)) AS 'letra_capital',
				MIN(IF(`operaciones_mvtos`.`tipo_operacion`  = 411 AND `operaciones_mvtos`.`afectacion_real` >0,`operaciones_mvtos`.`periodo_socio`,99999)) AS 'letra_interes',
				SUM(IF(`operaciones_mvtos`.`tipo_operacion`  = 120,1,0)) AS 'num_abonos',
				MAX(IF(`operaciones_mvtos`.`tipo_operacion`  = 410,`operaciones_mvtos`.`periodo_socio`,0)) AS 'letra_capital_u',
				MAX(IF(`operaciones_mvtos`.`tipo_operacion`  = 411,`operaciones_mvtos`.`periodo_socio`,0)) AS 'letra_interes_u'				
			FROM
				`operaciones_mvtos` `operaciones_mvtos` 
			WHERE
				`docto_afectado`=" . $this->mNumeroCredito . "	
			GROUP BY
				`operaciones_mvtos`.`docto_afectado` ";
		//setLog($sql);
		if(!is_array($DD)){
			$DD	= $xQL->getDataRow($sql);
		}
		
		if(isset($DD["desembolso"])){
			$this->mTotalDesembolso = setNoMenorQueCero($DD["desembolso"]);
			$this->mTotalAbonos		= setNoMenorQueCero($DD["abonos"]);
			$this->mTotalDispuesto	= setNoMenorQueCero($DD["disposicion"]);
			$this->mTotalPendiente	= setNoMenorQueCero($DD["pendiente"]);
			$this->mTotalPendInts	= setNoMenorQueCero($DD["pendiente_interes"]);
			$this->mProximaLetraC	= setNoMenorQueCero($DD["letra_capital"]);
			$this->mProximaLetraI	= setNoMenorQueCero($DD["letra_interes"]);
			$this->mUltimaLetraC	= setNoMenorQueCero($DD["letra_capital_u"]);
			$this->mUltimaLetraI	= setNoMenorQueCero($DD["letra_interes_u"]);			
			$this->mTotalBonInt		= setNoMenorQueCero($DD["bon_int"]);
			$this->mTotalBonMora	= setNoMenorQueCero($DD["bon_mora"]);
			$this->mTotalBonOtros	= setNoMenorQueCero($DD["bon_otros"]);
			$this->mNumeroAbonos	= setNoMenorQueCero($DD["num_abonos"]);
			$init					= true;
		}
		return $init;
	}
	function getTotalDesembolso(){ return $this->mTotalDesembolso; }
	function getTotalAbonos(){ return $this->mTotalAbonos; }
	function getTotalDispuesto(){ return $this->mTotalDispuesto; }
	function getTotalPendiente(){ return $this->mTotalPendiente; }
	function getTotalPendInts(){ return $this->mTotalPendInts; }
	function getProxLetraCap(){ return $this->mProximaLetraC; }
	function getProxLetraInt(){ return $this->mProximaLetraI; }
	function getUltLetraCap(){ return $this->mUltimaLetraC; }
	function getUltLetraInt(){ return $this->mUltimaLetraI; }
	function getTotalBonInt(){ return $this->mTotalBonInt; }
	function getTotalBonMora(){ return $this->mTotalBonMora; }
	function getTotalBonOtros(){ return $this->mTotalBonOtros; }
	function getTotalPlanPend(){ return $this->mTotalPlanPend; }
	function getTotalPlanExigible(){ return $this->mTotalPlanExige; }
	function getNumeroAbonos(){ return $this->mNumeroAbonos; }
	function initAcumOpsPlan($fecha = false){
		$xF		= new cFecha();
		$xQL	= new MQL();
		$fecha	= $xF->getFechaISO($fecha);
		$init	= false;
		$sql	= "SELECT SUM(`monto`) AS `acumulado`, SUM(IF(`fecha_de_pago` <= '$fecha',0, `monto`)) AS `exigible` FROM `creditos_parcialidades` WHERE `credito`=" . $this->mNumeroCredito . " ";
		//setLog($sql);
		$DD	= $xQL->getDataRow($sql);
		
		if(isset($DD["acumulado"])){
			$this->mTotalPlanPend	= setNoMenorQueCero($DD["acumulado"]);
			$this->mTotalPlanExige	= setNoMenorQueCero($DD["exigible"]);
			
			$init					= true;
		}
		return $init;		
	}
	private function initMvtosHoy(){
		$xF		= new cFecha();
		$xQL	= new MQL();
		$fecha	= $xF->getFechaISO($fecha);
		$sql	= "SELECT * FROM `vw_creditos_mvtos_hoy` WHERE `credito`=" . $this->mNumeroCredito;
		$DD		= $xQL->getDataRow($sql);
		
		if(isset($DD["credito"])){
				//pagos
			if($DD["pagos"] ==1){ }
				//cargoscobranza
			if($DD["cargoscobranza"] ==1){ $this->mTieneCargosC = true; }
				//bonificaciones
			if($DD["bonificaciones"] ==1){ $this->mTieneBonif = true; }
				//desembolsos
			if($DD["desembolsos"] ==1){ $this->mTieneMinistra = true; }				
				//otroscargos
			if($DD["otroscargos"] ==1){ $this->mTieneCargosO = true; }				
		}
		$this->mInitMvtosHoy 	= true;
	}
	function getHoyTieneCargosCbza(){
		if($this->mInitMvtosHoy == false){ $this->initMvtosHoy(); }
		return $this->mTieneCargosC;
	}
	function getHoyTieneOtrosCargos(){
		if($this->mInitMvtosHoy == false){ $this->initMvtosHoy(); }
		return $this->mTieneCargosO;
	}
	function getHoyTieneAbonos(){
		if($this->mInitMvtosHoy == false){ $this->initMvtosHoy(); }
		return $this->mTieneAbonos;
	}
	function getHoyTieneDesembolsos(){
		if($this->mInitMvtosHoy == false){ $this->initMvtosHoy(); }
		return $this->mTieneMinistra;
	}
	function getHoyTieneBonificaciones(){
		if($this->mInitMvtosHoy == false){ $this->initMvtosHoy(); }
		return $this->mTieneBonif;
	}
	function getHoyEsAfectado(){
		if($this->mInitMvtosHoy == false){ $this->initMvtosHoy(); }
		return ($this->mTieneCargosC OR $this->mTieneBonif == true OR $this->mTieneCargosO == true OR $this->mTieneMinistra == true OR $this->mTieneAbonos == true) ? true : false;
	}
}

class cCreditosPagos {
	private $mNumeroCredito		= false;
	private $mPersona			= false;
	private $mObj				= null;
	private $mInit				= false;
	private $mNombre			= "";
	private $mMessages			= "";
	private $mOCred				= null;
	private $mReciboDeOperacion	= 0;
	private $mSucess			= true;
	
	function __construct($clave = false){ $this->mNumeroCredito	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xCred	= new cCredito($this->mNumeroCredito);
		if($xCred->init($data) == true){
			$this->mPersona		= $xCred->getClaveDePersona();
			$this->mInit		= true;
			$this->mOCred		= $xCred;
		}
		return $this->mInit;
	}
	function getOCred(){if($this->mOCred ==null){$this->init();} return $this->mOCred; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function addPagoMora($monto, $parcialidad = 1, $observaciones = "",  $tipoDePago = TESORERIA_COBRO_NINGUNO, $fecha = false, $recibo = false){
		$recibo		= setNoMenorQueCero($recibo);
		
		if($recibo <= 0){
			$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO);
			$recibo		= $xRec->setNuevoRecibo($this->mPersona, $this->mNumeroCredito, $fecha, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO);
		}
		$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO, true, $recibo);
		if($xRec->init() == true){
			$operacion	= $xRec->setNuevoMvto($fecha, $monto, OPERACION_CLAVE_PAGO_MORA, $parcialidad, $observaciones);
			if($operacion > 0){
				$this->getOCred()->setInteresMoratorioPagado($monto);
			}
			$xRec->setFinalizarRecibo(true);
		}
		$this->mMessages	.= $xRec->getMessages();
		return $recibo;
	}
	function addCargosCobranza($monto, $parcialidad = 1, $observaciones = "",  $tipoDePago = TESORERIA_COBRO_NINGUNO, $fecha = false, $recibo = false){
		$recibo		= setNoMenorQueCero($recibo);
	
		if($recibo <= 0){
			$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_CARGOSPENDS);
			$recibo		= $xRec->setNuevoRecibo($this->mPersona, $this->mNumeroCredito, $fecha, $parcialidad, RECIBOS_TIPO_CARGOSPENDS);
				
		}
		$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_CARGOSPENDS, true, $recibo);
		$xRec->setDefaultEstatusOperacion(OPERACION_ESTADO_GENERADO);
		if($xRec->init() == true){
			$operacion	= $xRec->setNuevoMvto($fecha, $monto, OPERACION_CLAVE_CARGOS_COBRANZA, $parcialidad, $observaciones);
			if($operacion > 0){
				$this->getOCred()->setGastosCobranzaAfectado($monto);
			}
			$xRec->setFinalizarRecibo(true);
		}
		$this->mMessages	.= $xRec->getMessages();
		return $recibo;
	}	
	function addPagoInteres($monto, $parcialidad = 1, $observaciones = "",  $tipoDePago = TESORERIA_COBRO_NINGUNO, $fecha = false, $recibo = false){
		$recibo		= setNoMenorQueCero($recibo);
	
		if($recibo <= 0){
			$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO);
			$recibo		= $xRec->setNuevoRecibo($this->mPersona, $this->mNumeroCredito, $fecha, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO);
		}
		$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO, true, $recibo);
		if($xRec->init() == true){
			$operacion	= $xRec->setNuevoMvto($fecha, $monto, OPERACION_CLAVE_PAGO_INTERES, $parcialidad, $observaciones);
			if($operacion > 0){
				$this->getOCred()->setInteresNormalPagado($monto);								//Establece el Interes Normal Pagado
				$xPlan		= new cPlanDePagos($this->getOCred()->getNumeroDePlanDePagos());	//Inicializa el Plan de Pagos
				if($xPlan->init() == true){														//Verifica si inicia el Plan
					$OLetra			= $xPlan->getOLetra($parcialidad);							//Obtiene el Objeto Letra
					$interes		= $OLetra->getInteres();									//Interes de la Letra
					if($monto >= $interes){														//Si el pago es Mayor a la letra
						$xPlan->setActualizarParcialidad($parcialidad, false, 0);				//Desaparece de la parcialidad en interes
					} else {
						$interes	= $interes - $monto;
						$xPlan->setActualizarParcialidad($parcialidad, false, $interes);		//Actualiza solo al remanente
					}
					
				}
			}
			$xRec->setFinalizarRecibo(true);
		}
		$this->mMessages	.= $xRec->getMessages();
		return $recibo;
	}
	function addBonificacion($monto, $parcialidad = 1, $observaciones = "",  $tipoDePago = TESORERIA_COBRO_NINGUNO, $tipo = false, $fecha = false, $recibo = false){
		$recibo		= setNoMenorQueCero($recibo);
		$tipo		= setNoMenorQueCero($tipo);
		if($recibo <= 0){
			$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_BONIFPENDS);
			$recibo		= $xRec->setNuevoRecibo($this->mPersona, $this->mNumeroCredito, $fecha, $parcialidad, RECIBOS_TIPO_BONIFPENDS);
	
		}
		$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_BONIFPENDS, true, $recibo);
		$xRec->setDefaultEstatusOperacion(OPERACION_ESTADO_GENERADO);
		if($xRec->init() == true){
			$operacion	= $xRec->setNuevoMvto($fecha, $monto, $tipo, $parcialidad, $observaciones);
			if($operacion > 0){
				$this->getOCred()->setBonificacionesAfectado($monto);
			}
			$xRec->setFinalizarRecibo(true);
		}
		$this->mMessages	.= $xRec->getMessages();
		return $recibo;
	}

	function setAbonoCapital($monto, $parcialidad = SYS_UNO, $cheque = DEFAULT_CHEQUE, $tipo_de_pago = FALLBACK_TIPO_PAGO_CAJA, $recibo_fiscal = DEFAULT_RECIBO_FISCAL,$observaciones = "", $grupo = false, $fecha = false, $recibo = false) {
		$xCred						= $this->getOCred();
		$parcialidad 				= setNoMenorQueCero ( $parcialidad );
		$recibo						= setNoMenorQueCero($recibo);
		$this->mReciboDeOperacion	= setNoMenorQueCero($xCred->getReciboDeOperacionAct());
		$recibo						= ($recibo <=0) ? $this->mReciboDeOperacion : $recibo;
		$solicitud					= $this->mNumeroCredito;
		$credito					= $this->mNumeroCredito;
		$socio 						= $xCred->getClaveDePersona();
		$xF							= new cFecha();
		$cheque						= setNoMenorQueCero($cheque);
		$nuevosaldo					= $xCred->getSaldoActual();
		if ($monto != 0) {
			$this->mMessages .= "WARN\tRECIBO >>>> $recibo\r\n";
			$grupo 					= setNoMenorQueCero ( $grupo );
			if ($grupo <= DEFAULT_GRUPO){$grupo = $xCred->getClaveDeGrupo();}
			$fecha					= ($fecha == false) ? $xCred->getFechaDeOperacionAct() : $fecha;
			$fecha					= $xF->getFechaISO($fecha);
				
			$CRecibo 				= new cReciboDeOperacion ( RECIBOS_TIPO_PAGO_CREDITO, true, $recibo );
			//$CRecibo->setGenerarPoliza();$CRecibo->setGenerarTesoreria();$CRecibo->setGenerarBancos();
			$CRecibo->setNumeroDeRecibo($recibo);
			if ($CRecibo->init() == false) {
				$recibo = $CRecibo->setNuevoRecibo( $socio, $this->mNumeroCredito, $fecha, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO, $observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo, false, "",0, $xCred->getClaveDeEmpresa() );
				$CRecibo->setNumeroDeRecibo($recibo);
				if($CRecibo->init() == false){
					$this->mMessages 	.= "ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCredito . " \r\n";
					$this->mSucess		= false;
				}
			}
			$this->mReciboDeOperacion = $recibo;
			if (setNoMenorQueCero ( $recibo ) > 0) {
				// Agregar el Movimiento
				$CRecibo->setNuevoMvto ( $fecha, $monto, OPERACION_CLAVE_PAGO_CAPITAL, $parcialidad, $observaciones, 1, TM_ABONO, $socio, $this->mNumeroCredito );
				$nuevosaldo 		= $xCred->getSaldoActual() - ($monto);
				$this->mMessages 	.= $CRecibo->getMessages();
				$this->mSucess 		= true;
				// Actualizar la Cuenta
				// $arrAct = array( "saldo_actual" => $this->mNuevoSaldo );
				$this->mMessages 	.= "WARN\tSALDO\tSe Actualiza el Saldo de " . $xCred->getSaldoActual() . " a " . $nuevosaldo . " al $fecha \r\n";
				// $this->setUpdate($arrAct);
			} else {
				$this->mMessages 	.= "ERROR\tNo Existe Recibo con el cual trabajar ($recibo) \r\n";
			}
			// afectar el capital
			if ($this->mSucess == true) {
				$EsPagado			= ($nuevosaldo <= TOLERANCIA_SALDOS) ? true : false;
				$arrUpdate			= array();
				if($xCred->isAFinalDePlazo() == false){
					$numero_plan 		= $this->getNumeroDePlanDePagos();
					if (setNoMenorQueCero ( $numero_plan ) > 0 and $parcialidad > 0) {
						$xPlan 			= new cPlanDePagos ( $numero_plan ); // no se necesita inicializar el plan de pagos
						$xPlan->setActualizarParcialidad ( $parcialidad, "(afectacion_real - $monto)", false, false, false, false, false );
						if($xCred->getPagosSinCapital() == true){
							//TODO: Unificar la funcion de reestablecer cambios.- 2016-08-05
							$proporcion_de_cambios			= 1 - ($monto / $xCred->getSaldoActual());
							$interes_diario					= $xCred->getInteresDiariogenerado() - ($xCred->getInteresDiariogenerado()  * $proporcion_de_cambios);
							$arrUpdate["interes_diario"]	= $interes_diario;		//Actualizar el Interes Diario
							$arrUpdate["monto_parcialidad"]	= $xCred->getMontoDeParcialidad() - ($xCred->getMontoDeParcialidad() * $proporcion_de_cambios);
							$this->mMessages				.= "WARN\tCambios de interes por capital queda en $interes_diario y proporcion de $proporcion_de_cambios\r\n";
							$sqlTM	= "UPDATE operaciones_mvtos SET afectacion_real=(afectacion_real*$proporcion_de_cambios),
							afectacion_cobranza =(afectacion_cobranza*$proporcion_de_cambios),
							afectacion_estadistica=(afectacion_estadistica*$proporcion_de_cambios) WHERE socio_afectado=$socio 
							AND docto_afectado=" . $this->mNumeroCredito . " AND (tipo_operacion=" . OPERACION_CLAVE_PLAN_INTERES . " OR tipo_operacion=" . OPERACION_CLAVE_PLAN_IVA . ") " ;
							$xQL							= new MQL();
							$xQL->setRawQuery($sqlTM);
							$this->mMessages				.= "WARN\tAjustando Intereses de plan de pagos solo interes a una Proporcion $proporcion_de_cambios\r\n";							
						}
					}
				}
				if ($EsPagado == true) {
					$xCred->setCreditoPagado( $fecha );
				} else {
					$arrUpdate["fecha_ultimo_mvto"]	 	= $fecha;
					$arrUpdate["fecha_ultimo_capital"] 	= $fecha;
					$arrUpdate["saldo_actual"] 			= $nuevosaldo;
					$arrUpdate["recibo_ultimo_capital"] = $recibo;
					
					//No es pago total y es diferente al ultimo pago
					if($parcialidad < $this->getPagosAutorizados()){
						$arrUpdate["ultimo_periodo_afectado"]	= $parcialidad;
					}
					$this->setUpdate($arrUpdate);
				}
			}
			if($this->mSucess == true){
				$CRecibo->setFinalizarRecibo(true);
				$this->mFechaOperacion	= $fecha;
				//$xCred->getORecibo()
				//$this->mObjRec 			= $CRecibo;
				$xCred->setReciboDeOperacion($this->mReciboDeOperacion);
			}
			$this->mMessages 		.= $CRecibo->getMessages();
			$xCred 					= null;
		}
		return $this->mReciboDeOperacion;
	}	
}


class cCreditosAvales {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mCredito	= false;
	private $mPersona	= false;
	private $mRelacionado	= false;
	private $mNumeroPorCred	= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `socios_relaciones` WHERE `idsocios_relaciones`=". $this->mClave);
		if(isset($data["idcreditos_destino_detallado"])){
			$this->mObj		= new cSocios_relaciones(); //Cambiar
			$this->mObj->setData($data);
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add(){}
	function initByCredito($credito){
		$credito		= setNoMenorQueCero($credito);
		$this->mCredito	= $credito;
	}
	function initArbolAvalesDirectos(){
		$xLi		= new cSQLListas();
		$xQL		= new MQL();
		$sqlAvales	= $xQL->getDataRecord($xLi->getListadoDeAvales($this->mCredito));
		$this->mNumeroPorCred	= $xQL->getNumberOfRows();
		
	}
	function getNumeroAvalesDirectos(){ return $this->mNumeroPorCred; }
}
class cCreditosDestinos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mTasaIVA	= 0;
	private $mNombre	= "";
	private $mMessages	= "";
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		if(!is_array($data)){
			$xCache	= new cCache();
			$data	= $xCache->get("creditos-destino-id-".$this->mClave);
			if($data == null){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `creditos_destinos` WHERE `idcreditos_destinos`=". $this->mClave);
				$xCache->set("creditos-destino-id-".$this->mClave, $data);
				$xQL	= null;
			}
		} else {
			$data		= $data;
		}

		if(isset($data["idcreditos_destinos"])){
			$this->mObj		= new cCreditos_destinos(); //Cambiar
			$this->mObj->setData($data);
			$this->mNombre	= $this->mObj->descripcion_destinos()->v();
			$this->mTasaIVA	= $this->mObj->tasa_de_iva()->v();
			$this->mObj->setData($data);
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add(){}
	function getTasaIVA(){ return $this->mTasaIVA; }
}

class cCreditosPromociones {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "creditos_productos_promo";
	private $mTipo			= 0;
	private $mOperacion		= 0;
	public $PROMO_DESCTO	= 1;
	public $PROMO_GRATIS	= 2;
	public $TIPO_PAGO_PROMO	= "promocion";
	private $mArrAcreditados= array();
	private $mArrGratuitos	= array();
	private $mArrPrecios	= array();
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){ if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cCreditos_productos_promo();
		if(!is_array($data)){
			$data		= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj			= $xT; //Cambiar
			$this->mClave		= $xT->idcreditos_productos_promo()->v();
			$this->mTipo		= $xT->tipo_promocion()->v();
			$this->mOperacion	= $xT->tipo_operacion()->v();
			
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit		= true;
			$xT 				= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){ return $this->mNombre; }
	function getClave(){ return $this->mClave; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function initItemsGratuitos($producto){
		$xQL	= new MQL();
		$rs		= $xQL->getRecordset("SELECT `tipo_operacion`,`num_items`,`precio` FROM `creditos_productos_promo` WHERE `producto`=$producto AND `tipo_promocion` = " . $this->PROMO_GRATIS . " ");
		while($rw = $rs->fetch_assoc()){
			$this->mArrGratuitos[$rw["tipo_operacion"]]	= $rw["num_items"];
			$this->mArrPrecios[$rw["tipo_operacion"]]	= $rw["precio"];
		}
		$rs->free();
		
		return $this->mArrGratuitos;
	}
	function initItemsAcreditados($credito){
		$sql = "SELECT   `operaciones_mvtos`.`tipo_operacion`,
			COUNT( `operaciones_mvtos`.`idoperaciones_mvtos` )  AS `items`,
		        SUM( `operaciones_mvtos`.`afectacion_real` )  AS `monto`
		FROM     `operaciones_mvtos` 
		INNER JOIN `operaciones_recibos`  ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.`idoperaciones_recibos` 
		WHERE
		( `operaciones_recibos`.`tipo_pago` = '" . $this->TIPO_PAGO_PROMO . "' ) 
		AND ( `operaciones_mvtos`.`docto_afectado` = $credito )
		GROUP BY `tipo_operacion`";
		$xQL	= new MQL();
		$rs		= $xQL->getRecordset($sql);
		while($rw = $rs->fetch_assoc()){
			$this->mArrAcreditados[$rw["tipo_operacion"]]	= $rw["items"]; 
		}
		$rs->free();
		return $this->mArrAcreditados;
	}
	function getArrPrecios(){ return $this->mArrPrecios; }
}
?>