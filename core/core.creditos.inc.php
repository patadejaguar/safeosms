<?php
#use Respect\Validation\Rules\Float;

/**
 * Core Creditos File
 * @author Balam Gonzalez Luis Humberto
 * @package creditos
 * Core Creditos File
 * 		16/04/2008
 *		31-Mayo-2008.- cCredito
 *		29/dic/2010	 Varios fixes
 */
include_once ("core.init.inc.php");
include_once ("entidad.datos.php");
include_once ("core.config.inc.php");
include_once ("core.fechas.inc.php");
include_once ("core.deprecated.inc.php");
include_once ("core.db.inc.php");
include_once ("core.db.dic.php");

include_once ("core.common.inc.php");
include_once ("core.html.inc.php");

include_once ("core.contable.inc.php");
include_once "core.contable.utils.inc.php";

include_once ("core.operaciones.inc.php");
include_once ("core.creditos.pagos.inc.php");
include_once ("core.creditos.utils.inc.php");
include_once ("core.creditos.originacion.inc.php");

@include_once ("../libs/sql.inc.php");

// =====================================================================================================

/**
 * Clase de Manejo de Creditos
 * 
 * @package creditos
 * @subpackage core
 * @author Balam Gonzalez Luis Humberto
 * @version 1.3
 */
class cCredito {
	private $mNumeroCredito 			= false;
	private $mNumeroSocio 				= false;
	private $mFechaMinistracion 		= false;
	private $mFechaVencimiento 			= false;
	private $mFechaVencimientoLegal 	= false;
	private $mFechaMora 				= false;
	private $mFechaUltimoMvtoCapital 	= false; // Fecha de ultimo pago de capital
	private $mPeriocidadDePago 			= false;
	private $mFechaDeSolictud 			= false;
	private $mFechaDeAutorizacion 		= false;
	private $mTipoDePago 				= false;
	private $mTipoDeCuota 				= false;
	private $mDiasAutorizados 			= false;
	private $mDiasSolicitados 			= null;
	private $mPagosAutorizados 			= false;
	private $mParcialidadActual 		= false;
	private $mModalidadDeCredito 		= false;
	private $mTipoDeDestino 			= false;
	private $mCausaDeMora 				= false;
	private $mTasaInteres 				= false;
	private $mTasaMoratorio 			= false;
	private $mTasaAhorro 				= false;
	private $mTasaGarantiaLiquida 		= false;
	private $mTasaCat					= false;
	private $mInteresDiario 			= false;
	private $mInteresNormalDevengado 	= false;
	private $mInteresNormalPagado 		= false;
	private $mInteresMoratorioDev 		= false;
	private $mInteresMoratorioPag 		= false;
	private $mSdoCapitalInsoluto 		= false;
	private $mMontoMinistrado 			= false;
	private $mMontoAutorizado 			= 0;
	private $mMontoSolicitado 			= false;
	private $mMontoBonificado			= 0;
	private $mToleranciaEnDiasParaVcto 	= false;
	private $mToleranciaEnDiasParaPago 	= 0;
	private $mEstatusActual 			= false;
	private $mTipoDeConvenio 			= false;
	private $mTipoDeAutorizacion 		= false;
	private $mNuevoSaldo 				= false;
	private $mTipoDeCalculoDeInteres 	= false;
	private $mSaldoVencido 				= 0;
	private $mSaldoOriginal				= 0;
	private $mNumeroDePlanDePago 		= false;
	private $mOficialQueGenera 			= false;
	private $mTasaIVA 					= false;
	private $mIVAIncluido 				= false;
	private $mGrupoAsociado 			= false;
	private $mReciboDeOperacion 		= false;
	private $mFechaOperacion 			= false;
	private $mOficialDeSeguimiento 		= false;
	private $mOficialDeCredito 			= false;
	private $mDatosOficialDeCredito 	= false;
	private $mClaveDeDestino 			= false;
	private $mMontoFijoParcialidad 		= 0;
	private $mCreditoInicializado 		= false;
	private $mArrDatosDeCredito 		= array ();
	private $mMessages 					= "";
	private $mErrorCode 				= 999;
	
	private $mDescripcionPeriocidad 	= "";
	private $mDescripcionProducto 		= "";
	private $mDescripcionDestino 		= "";
	private $mDescripcionEstatus 		= "";
	private $mUsuarioActual 			= false;
	private $mContrato_URL 				= "";
	private $mEmpresa 					= DEFAULT_EMPRESA;
	private $mNombrePersonaAsoc 		= "";
	private $mPagosSolicitados 			= 0;
	private $mForceMinistracion 		= false;
	private $mCuentaDeGarantiaLiquida 	= false;
	// Datos de parcialidades
	private $mNumeroProximaParcialidad 	= 0;
	private $mFechaProximaParcialidad 	= 0;
	private $mParcialidadesConSaldo 	= 0;
	private $mContratoCorriente 		= CTA_GLOBAL_CORRIENTE;
	private $mFechaPrimeraParc 			= ""; // plan
	private $mFechaUltimaParc	 		= ""; // plan
	private $mFechaPrimerAtraso 		= null;
	private $mFechaVenceContrato		= false;
	
	private $mForceVista 				= false;
	private $mObjRec					= null; // Objeto de recibo
	private $mMontoUltimoPago 			= 0;
	private $mFechaUltimoPago 			= false;
	private $mObjEstado 				= null;
	private $mObjSoc 					= null;
	private $mObjPlan 					= null;
	private $mObjTipoAut				= null;
	private $mOPeriocidad 				= null;
	private $mOEmpresa 					= null;
	private $mObjDatosOrigen			= null;
	private $mOB 						= null;
	private $mOProducto					= null;
	private $mOPagos					= null;
	
	private $mSaldoAuditado				= 0;
	
	private $mMontoInteresPagado 	= 0;
	private $mMontoMoraPagado 		= 0;
	private $mMontoCapitalPagado 	= 0;
	private $mDPrimerPagoEfect 		= array ();
	private $mDUltimoPagoEfect 		= array ();
	private $mInitPagos 			= false;
	private $mAbonosAcumulados 		= 0;
	private $mFechaAcumulada 		= false;
	private $mPagos 				= array ();
	private $mLetrasAtrasadas 		= 0;
	private $mArrFechasDePago 		= array ();
	private $mSucursal				= "";
	private $mTipoDeDiasDePago 		= 1;
	private $mNivelDeRiesgo 		= 1;
	private $mTipoDeDispersion 		= 1;
	private $mTipoLugarDeCobro 		= 1; // lugar en que se va a cobrar
	private $mDiasRemanente			= 0;
	
	private $mGastosDeCobranza		= 0;
	private $mReciboDeLiquidacion	= 0;
	private $mValidacionERRS		= 0;
	private $mValidacionWARNS		= 0;
	private $mClaveDePeriodoCredito	= 0;
	private $mInitProximaParc		= false;
	private $mPenasPorCobrar		= 0;
	private $mOIntereses			= null;
	private $mTotalDispuesto		= 0;
	private $mTotalPlanPend			= 0;
	private $mTotalPlanExig			= 0;
	private $mIDCacheCredito		= "";
	private $mIDCacheFicha			= "";
	private $mIDCacheInt			= "";
	private $mRazonAutorizacion		= "";
	
	private $mIDUsuario				= 0;
	public $ORIGEN_PRESUPUESTO		= 280;
	public $ORIGEN_PRECLIENTE		= 270;
	public $ORIGEN_NOMINA			= 290;
	public $ORIGEN_ARRENDAMIENTO	= 281;
	public $ORIGEN_LINEAS			= 295;
	public $ORIGEN_RENOVACION		= 3;
	public $ORIGEN_REESTRUCTURA		= 4;
	
	
	public $COBRO_DOMICILIADO		= 320;
	
	private $mTipoDeOrigen			= 0;
	private $mClaveDeOrigen			= 0;
	private $mMontoDeOrigen			= 0;
	
	private $mValorResidual			= 0;
	private $mEsLeasingPuro			= false;
	private $mRespetaPlan			= true;
	private $mClaveDeRechazo		= 0;
	private $mRechazoNota			= "";
	private $mFechaDesvinculo		= false;
	private $mEmpDesvinculo			= false;
	private $mDescribeDestino		= "";
	private $mMontoSeguroCred		= 0;
	private $mMontoSeguroVid		= 0;
	private $mMontoSeguroDes		= 0;
	                                   // protected $mOtrosParams = array();
	                                   // protected $mSaldoAtrasado = 0;
	function __construct($numero_de_credito = false, $numero_de_socio = false) {
		$this->mNumeroCredito 			= setNoMenorQueCero($numero_de_credito);
		$this->mNumeroSocio 			= setNoMenorQueCero($numero_de_socio);
		$this->mFechaOperacion			= date ( "Y-m-d" );
		$this->mSucursal 				= getSucursal ();
		$this->mUsuarioActual 			= getUsuarioActual();

		$this->mOficialQueGenera 		= elusuario ( $this->mUsuarioActual );
		$this->mClaveDePeriodoCredito	= EACP_PER_SOLICITUDES;
		$this->mIDCacheCredito			= TCREDITOS_REGISTRO . "-" . $this->mNumeroCredito;
	}
	function setResetPersonaAsociada($fecha, $observaciones = "", $empresa = "") {
		$empresa 	= setNoMenorQueCero ( $empresa );
		$empresa 	= ($empresa <= 0) ? FALLBACK_CLAVE_EMPRESA : $empresa;
		$xLng 		= new cLang ();
		$res		= true;
		$txt 		= $xLng->get ( "saldo" ) . " : " . $this->getSaldoActual () . "; " . $xLng->get ( "empresa" ) . ":" . $this->getClaveDeEmpresa () . "/$empresa . $observaciones";
		$tipo 		= ($empresa == FALLBACK_CLAVE_EMPRESA) ? MEMOS_TIPO_DESVINCULACION : MEMOS_TIPO_VINCULACION;
		// Agregar Memo a socio
		$xSoc 		= new cSocio ( $this->getClaveDePersona () );
		if($xSoc->init() == true){
			if($this->setUpdate ( array ("persona_asociada" => $empresa)) == false){
				$res	= false;
				$this->mMessages .= "ERROR\tSe fallo al actualizar a la Empresa $empresa\r\n";
			} else {
				$this->mMessages .= $xSoc->setNewMemo ( $tipo, $txt, $this->getNumeroDeCredito (), $fecha );
			}
		}
		return $res;
	}
	/**
	 * Funcion que Inicializa el Credito
	 * @var array $DCredito Se Refiere al Forzado de Asignacion de Variables, un Array Completo
	 */
	function init($DCredito = false) {
		$xCache		= new cCache();
		$this->mCreditoInicializado = false;
		$WithSocio 	= "";
		$idcredito	= setNoMenorQueCero($this->mNumeroCredito);
		$xT			= new cCreditos_solicitud();
		
		if ( !is_array ( $DCredito )) {
			
			$DCredito	= $xCache->get($this->mIDCacheCredito);
			if ( !is_array ( $DCredito ) AND $idcredito > 0 ) {
				$xSTb 		= new cSQLTabla ( TCREDITOS_REGISTRO );
				$sql 		= $xSTb->getQueryInicial () . " WHERE	(`creditos_solicitud`.`numero_solicitud` =" . $idcredito . ") $WithSocio LIMIT 0,1";			
				$DCredito 	= obten_filas ( $sql );
			}
		}
		if (isset ( $DCredito [$xT->NUMERO_SOLICITUD] )) {
			$this->mNumeroCredito 				= $DCredito["numero_solicitud"];
			$this->mNumeroSocio 				= $DCredito["numero_socio"];
			$this->mFechaMinistracion 			= $DCredito["fecha_ministracion"];
			$this->mFechaVencimiento 			= $DCredito["fecha_vencimiento"];
			$this->mFechaUltimoMvtoCapital 		= $DCredito["fecha_ultimo_capital"];
			$this->mFechaMora 					= $DCredito["fecha_mora"];
			$this->mFechaDeSolictud 			= $DCredito["fecha_solicitud"];
			$this->mFechaDeAutorizacion 		= $DCredito["fecha_autorizacion"];
			$this->mFechaVencimientoLegal 		= $DCredito["fecha_vencimiento_dinamico"];
			
			$this->mPeriocidadDePago 			= $DCredito["periocidad_de_pago"];
			$this->mTipoDePago 					= $DCredito["tipo_de_pago"];	//Tipo de  cuota de pago
			$this->mTipoDeCuota 				= $DCredito["tipo_de_pago"];	//Tipo de  cuota de pago
			$this->mDiasAutorizados 			= $DCredito["dias_autorizados"];
			$this->mDiasSolicitados 			= $DCredito["plazo_en_dias"];
			$this->mPagosAutorizados 			= $DCredito["pagos_autorizados"];
			$this->mTasaInteres 				= $DCredito["tasa_interes"];
			$this->mTasaMoratorio 				= $DCredito["tasa_moratorio"];
			$this->mTasaAhorro 					= $DCredito["tasa_de_ahorro"];
			$this->mTasaGarantiaLiquida 		= $DCredito["porciento_garantia_liquida"];
			$this->mTasaCat						= round($DCredito["tasa_cat"],1);
			
			$this->mSdoCapitalInsoluto 			= $DCredito["saldo_actual"];
			$this->mSaldoOriginal				= $DCredito["saldo_actual"];
			$this->mMontoMinistrado 			= $DCredito["monto_autorizado"];
			$this->mMontoSolicitado 			= $DCredito["monto_solicitado"];
			$this->mToleranciaEnDiasParaVcto 	= $DCredito["tolerancia_en_dias_para_vencimiento"];
			$this->mToleranciaEnDiasParaPago 	= $DCredito["tolerancia_dias_no_pago"];
			
			$this->mEstatusActual 				= $DCredito ["estatus_actual"];
			$this->mIVAIncluido 				= $DCredito ["iva_incluido"];
			$this->mTasaIVA 					= $DCredito ["tasa_iva"];
			$this->mGrupoAsociado 				= $DCredito ["grupo_asociado"];
			$this->mTipoDeConvenio 				= $DCredito ["tipo_convenio"]; //Error, cambiar esto.
			$this->mFechaVenceContrato			= $DCredito ["fecha_vencimiento"];
			//XXX: La consulta query duplica oficial_seguimiento y otros campos
			$this->mOficialDeCredito 			= $DCredito ["oficial_credito"];
			//$this->mOficialDeSeguimiento 		= $DCredito ["oficial_seguimiento"]; //real, oficial_de_seguimiento es fix
			$this->mOficialDeSeguimiento 		= $DCredito ["oficial_de_seguimiento"];
			
			$this->mInteresDiario 				= $DCredito ["interes_diario"];
			$this->mInteresNormalPagado 		= $DCredito ["interes_normal_pagado"];
			$this->mInteresNormalDevengado 		= $DCredito ["interes_normal_devengado"];
			$this->mInteresMoratorioDev 		= $DCredito ["interes_moratorio_devengado"];
			$this->mInteresMoratorioPag 		= $DCredito ["interes_moratorio_pagado"];
			$this->mDescripcionPeriocidad 		= $DCredito ["descripcion_periocidadpagos"];
			$this->mDescripcionProducto 		= $DCredito ["descripcion_tipoconvenio"];
			$this->mDescripcionEstatus 			= $DCredito ["descripcion_estatus"];
			$this->mTipoDeCalculoDeInteres 		= $DCredito ["tipo_de_calculo_de_interes"];
			$this->mParcialidadActual 			= $DCredito ["ultimo_periodo_afectado"];
			$this->mContratoCorriente 			= setNoMenorQueCero($DCredito ["contrato_corriente_relacionado"]);
			$this->mMontoAutorizado 			= $DCredito ["monto_autorizado"];
			$this->mIDUsuario					= $DCredito ["idusuario"];
			
			$this->mModalidadDeCredito 			= $DCredito["tipo_credito"];
			$this->mTipoDeDestino 				= $DCredito["destino_credito"];
			$this->mClaveDeDestino 				= $DCredito["destino_credito"];
			$this->mCausaDeMora 				= $DCredito["causa_de_mora"];
			$this->mDescripcionDestino 			= $DCredito["descripcion_aplicacion"];
			$this->mTipoDeAutorizacion 			= $DCredito["tipo_de_autorizacion"];
			$this->mMontoFijoParcialidad 		= $DCredito["monto_parcialidad"]; //
			
			$this->mContrato_URL 				= $DCredito["path_del_contrato"];
			$this->mEmpresa 					= $DCredito["persona_asociada"];
			$this->mSaldoVencido 				= $DCredito["saldo_vencido"];
			$this->mFechaUltimoPago 			= $DCredito["fecha_ultimo_mvto"];
		//$this->mSdoCapitalInsoluto 			= $DCredito ["saldo_actual"];
			$this->mFechaPrimeraParc 			= $DCredito["fecha_de_primer_pago"];
			$this->mPagosSolicitados 			= $DCredito["numero_pagos"];
			$this->mSucursal 					= $DCredito["sucursal"];
			$this->mNivelDeRiesgo 				= $DCredito["nivel_riesgo"];
			$this->mEmpresa 					= ($this->mEmpresa == 0) ? FALLBACK_CLAVE_EMPRESA : $this->mEmpresa;
			$this->mTipoDeDiasDePago 			= $DCredito["tipo_de_dias_de_pago"];
			$this->mTipoLugarDeCobro 			= $DCredito["tipo_de_lugar_de_pago"];
			$this->mTipoDeDispersion 			= $DCredito["tipo_de_dispersion"];
			$this->mFechaProximaParcialidad		= $DCredito["fecha_de_proximo_pago"];
			$this->mSaldoAuditado				= $DCredito["saldo_conciliado"];
			$this->mGastosDeCobranza			= $DCredito["gastoscbza"];			//Gastos de Cobranza Pendientes
			$this->mReciboDeLiquidacion			= $DCredito["recibo_ultimo_capital"];
			$this->mClaveDePeriodoCredito		= $DCredito["periodo_solicitudes"];
			$this->mFechaProximaParcialidad		= $DCredito["fecha_de_proximo_pago"];
			$this->mNumeroProximaParcialidad	= $DCredito["ultimo_periodo_afectado"]+1;
			$this->mParcialidadesConSaldo		= setNoMenorQueCero(($DCredito["pagos_autorizados"]-$DCredito["ultimo_periodo_afectado"]));
			$this->mMontoBonificado				= $DCredito["bonificaciones"];
			$this->mRazonAutorizacion			= $DCredito[$xT->DOCTO_AUTORIZACION];
			$this->mDescribeDestino				= $DCredito[$xT->DESCRIPCION_APLICACION];
		
			$xMontos							= new cCreditosMontos($this->mNumeroCredito);
			if($xMontos->init() == true){
				$this->mOIntereses				= $xMontos;
				$this->mTotalDispuesto			= $xMontos->getTotalDispuesto();
				$this->mSdoCapitalInsoluto 		= $this->mSdoCapitalInsoluto + $this->mTotalDispuesto;
				$this->mGastosDeCobranza		= $xMontos->getCargosCbzaXPag();
				$this->mNumeroProximaParcialidad= $xMontos->getPeriodoMinimo();
				$this->mTotalPlanExig			= $xMontos->getTotalPlanExigible();
				$this->mTotalPlanPend			= $xMontos->getTotalPlanPend();
				$this->mMontoBonificado			= $xMontos->getBonificaciones();
				$this->mOMonto					= $xMontos;
			}
			if ($this->mEmpresa != FALLBACK_CLAVE_EMPRESA) {
				$xEmp = new cEmpresas ( $this->mEmpresa );
				$xEmp->init();
				$this->mNombrePersonaAsoc 		= $xEmp->getNombreCorto ();
			}
			if(CREDITO_CONTROLAR_POR_ORIGEN == true){
				$xOrigen						= new cCreditosDatosDeOrigen(false, $this->mNumeroCredito);
				if($xOrigen->initByCredito($this->mNumeroCredito) == true){
					$this->mClaveDeOrigen		= $xOrigen->getClaveDeOrigen();
					$this->mTipoDeOrigen		= $xOrigen->getTipoDeOrigen();
					$this->mMontoDeOrigen		= $xOrigen->getMontoDeOrigen();
					$this->initDatosOriginacion();
				}
				$this->mObjDatosOrigen			= $xOrigen;
			}
			if(PERSONAS_CONTROLAR_MICROSEGUROS == true){
				$this->initDatosMicroseguro();
			}
			//Reparaciones al final
			if($this->isAFinalDePlazo() == true){
				$this->mMontoFijoParcialidad	= 0;
			}
			//Datos de Originacion
			
			//=============== Cache
			$this->mIDCacheCredito				= TCREDITOS_REGISTRO . "-" . $this->mNumeroCredito;
			$xCache->set($this->mIDCacheCredito, $DCredito);
			//===============
			$this->mArrDatosDeCredito 			= $DCredito;
			unset ( $DCredito );
			//Inicializar Montos
			//Inicializar Datos de Rechazo
			if($this->getEsRechazado() == true){
				$this->initDatosDeRechazo();
			}
			// Inicializa el Credito
			$this->mCreditoInicializado 		= true;
		} else {
			$this->mCreditoInicializado 		= false;
		}
		return $this->mCreditoInicializado;
	}
	function initCredito($DCredito = false) { return $this->init ( $DCredito ); }
	private function initDatosOriginacion(){
		switch($this->mTipoDeOrigen){
			case $this->ORIGEN_ARRENDAMIENTO:
				$xLeas	= new cCreditosLeasing($this->mClaveDeOrigen);
				if($xLeas->init() == true){
					$this->mValorResidual	= $xLeas->getValorResidual();
					$this->mEsLeasingPuro	= true;
				}
				break;
		}
	}
	private function initDatosDeRechazo(){
		$xCredR	= new cCreditosRechazos();
		if($xCredR->initByCredito($this->getClaveDeCredito()) == true){
			$this->mClaveDeRechazo	= $xCredR->getTipo();
			$this->mRechazoNota		= $xCredR->getNota();
		}
	}
	private function initDatosMicroseguro(){
		$xSeg	= new cCreditosMicroseguros();
		if($xSeg->initByCredito($this->getClaveDeCredito(), $xSeg->TIPO_CREDITO) == true){
			$this->mMontoSeguroCred	= $xSeg->getMontoSeguro();
		}
	}
	function getNotaDeRechazo(){ return $this->mRechazoNota; }
	/**
	 * Funcion que Busca un Fecha por el Numero de Pagos por el que debe llevar
	 * @param integer $pago_buscado pago del cual se debe buscar la fecha
	 * @param array $aD establece los parametros por array
	 */
	function getFechaEstimadaPorNumeroDePago($pago_buscado, $aD = false) {
		if ($this->mCreditoInicializado == false) {
			$this->initCredito ();
		}
		$credito = $this->mNumeroCredito;
		$socio = $this->mNumeroSocio;
		$fecha_ministracion 		= $this->mFechaMinistracion;
		$fecha_vencimiento 			= $this->mFechaVencimiento;
		$pagos_autorizados 			= $this->mPagosAutorizados;
		$periocidad_pago 			= $this->mPeriocidadDePago;
		$dias_autorizados 			= $this->mDiasAutorizados;
		$dias_tolerancia_no_pago 	= $this->mToleranciaEnDiasParaPago;
		$tipo_de_pago 				= $this->mTipoDePago;
		$msg = "";
		$ql = new MQL ();
		
		$fecha_devuelta = $fecha_vencimiento;
		if ($tipo_de_pago == CREDITO_TIPO_PAGO_UNICO or $tipo_de_pago == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
			$msg .= "TIPO.PAG\tEl Tipo de pago es UNICO, se devuelve la fecha de Vencimiento: $fecha_vencimiento\r\n";
			$fecha_devuelta = $fecha_vencimiento;
		} else {
			
			// (is_array($aD) AND isset($aD[]))
			$data = $ql->getDataRow ( "SELECT * FROM historial_de_pagos WHERE credito=$credito AND periodo=$pago_buscado LIMIT 0,1" );
			if (isset ( $data ["fecha"] )) {
				$fecha_devuelta = $data ["fecha"];
			} else {
				// 10*15 - 200 = 50
				// HACK se omite un periodo para Interpretarse como Primer Dia de Abono
				$dias_de_tolerancia = $dias_autorizados - (($pagos_autorizados - 1) * $periocidad_pago);
				
				$fecha_primer_abono = sumardias ( $fecha_ministracion, $dias_de_tolerancia );
				$msg .= "$socio\t$credito\tDIAS_DE_TOLERANCIA:\t$dias_de_tolerancia\r\n";
				$msg .= "$socio\t$credito\tFECHA_PRIMER_ABONO:\t$fecha_primer_abono\r\n";
				
				$dia_1_ab = PQ_DIA_PRIMERA_QUINCENA;
				$dia_2_ab = PQ_DIA_SEGUNDA_QUINCENA;
				$dvparcial = DIAS_PAGO_VARIOS;
				
				$fecha_de_pago = $fecha_primer_abono;
				// filtra solo pagos Quincenales
				if ($periocidad_pago != CREDITO_TIPO_PERIOCIDAD_QUINCENAL) {
					// convierte la Fecha de Vencimiento al dia de pago Predeterminado
					$dia_1_ab = date ( "d", strtotime ( $fecha_vencimiento ) );
					// $msg .= "$socio\t$credito\tPAGOS_NO_QUINCENALES:\t$periocidad_pago\r\n";
				}
				
				$msg .= "$socio\t$credito\tPERIODO\tFECHA_REFERENCIA\tFECHA_PAGO\r\n";
				
				if ($this->mCreditoInicializado == true) {
					
					for($i = 1; $i <= $pagos_autorizados; $i ++) {
						if ($i == 1) {
							$fecha_de_referencia = $fecha_primer_abono;
						} else {
							$fecha_de_referencia = $fecha_de_pago;
						}
						
						// ------------------------------------ Obtiene la Fecha de Pago ----------------------------------------------
						if ($periocidad_pago <= 7) {
							// Obtiene el Dia de Ref + dias del periodo
							if ($i == 1) { // Si es primer pago, es el dia de abono
								$fecha_de_pago = $fecha_de_referencia;
							} else {
								$fecha_de_pago = sumardias ( $fecha_de_referencia, $periocidad_pago );
							}
							$fecha_de_pago = set_no_festivo ( $fecha_de_pago );
						} elseif (($periocidad_pago >= 15) && ($periocidad_pago <= 29)) {
							// Obtiene el Dia de Ref + dias del periodo
							if ($i == 1) { // Si es primer pago, es el dia de abono
								$fecha_de_pago = $fecha_de_referencia;
							} else {
								$fecha_de_pago = sumardias ( $fecha_de_referencia, $periocidad_pago );
							}
							$fecha_de_pago = set_dia_abono_quincenal ( $fecha_de_pago, $dia_1_ab, $dia_2_ab );
							$fecha_de_pago = set_no_festivo ( $fecha_de_pago );
							
							// Tratamiento Mesual o mas, si es menor a la 1era Quincena, baja al dia dos, si no sube un mes al dia dos...
						} elseif (($periocidad_pago >= 30) && ($periocidad_pago < 60)) {
							// Obtiene el Dia de Ref + dias del periodo
							if ($i == 1) { // Si es primer pago, es el dia de abono
								$fecha_de_pago = $fecha_de_referencia;
							} else {
								$fecha_de_pago = sumardias ( $fecha_de_referencia, $periocidad_pago );
							}
							$fecha_de_pago = set_dia_abono_mensual ( $fecha_de_pago, $dia_1_ab );
							$fecha_de_pago = set_no_festivo ( $fecha_de_pago );
						} elseif (($periocidad_pago >= 60) && ($periocidad_pago < 360)) {
							// Obtiene el Dia de Ref + dias del periodo
							if ($i == 1) { // Si es primer pago, es el dia de abono
								$fecha_de_pago = $fecha_de_referencia;
							} else {
								$fecha_de_pago = sumardias ( $fecha_de_referencia, $periocidad_pago );
							}
							$fecha_de_pago = set_dia_abono_mensual ( $fecha_de_pago, $dia_1_ab );
							$fecha_de_pago = set_no_festivo ( $fecha_de_pago );
						} else {
							// Tratamiento 360 o Semanal
							$fecha_de_pago = sumardias ( $fecha_de_referencia, $periocidad_pago );
						}
						// -----------------------------------------------------------------------------------------------------------------------------------
						// $msg .= "$socio\t$credito\t$i\t$fecha_de_referencia\t$fecha_de_pago\r\n";
						// Marcar la Fecha de Pago
						if ($i == $pago_buscado) {
							$fecha_devuelta = $fecha_de_pago;
							$msg .= "$socio\t$credito\tFECHA_BUSCADA:\t$fecha_devuelta\r\n";
							break;
						}
						$dias_para_vencimiento = $dvparcial + $dias_tolerancia_no_pago;
						$vencimiento_parcialidad = sumardias ( $fecha_de_pago, $dias_para_vencimiento ); // Fecha de Pago + Dias en que vence la Parc + 1
					} // END FOR
				} // END IF
			} // END TIPO DE PAGO VALUATE
		}
		$this->mMessages .= $msg;
		return $fecha_devuelta;
	} // END FUNCTION
	/**
	 * Obtiene en un array los datos del plan de Pagos
	 * @return array Array con los Datos del Plan de Pagos
	 */
	function getDatosDelPlanDePagos() {
		$xRec	= new cReciboDeOperacion();
		
		$sqlrs = "SELECT `operaciones_recibos`.*, `operaciones_recibostipo`.*
						FROM
							`operaciones_recibos` `operaciones_recibos`	INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
								ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
								`idoperaciones_recibostipo`
						WHERE
							(docto_afectado=" . $this->mNumeroCredito . ") AND (tipo_docto=" . RECIBOS_TIPO_PLAN_DE_PAGO . ") ORDER BY fecha_operacion DESC LIMIT 0,1";
		$d = obten_filas ( $sqlrs );
		
		if (isset ( $d ["idoperaciones_recibos"] )) {
			$this->mNumeroDePlanDePago = $d ["idoperaciones_recibos"];
			$this->mMessages .= "OK\tPLAN DE PAGOS " . $this->mNumeroDePlanDePago . " CARGADO \r\n";
		} else {
			$this->mNumeroDePlanDePago = false;
			$this->mMessages .= "ERROR\tNO EXISTE EL PLAN DE PAGOS EN EL SISTEMA. SE RECOMIENDA GENERARLO NUEVAMENTE PARA EL BUEN FUNCIONAMIENTO DEL SISTEMA. CONTACTE A SU OFICIAL DE CREDITO \r\n";
		}
		
		return $d;
	}
	function getNumeroDePlanDePagos() {
		if (setNoMenorQueCero ( $this->mNumeroDePlanDePago ) <= 0) {
			$this->getDatosDelPlanDePagos ();
		}
		return $this->mNumeroDePlanDePago;
	}
	/**
	 * Muestra el Plan de Pagos en Texto, o HTML, Simple o Completo
	 * @param string $InOut de Salida, por Defecto HTML
	 * @param boolean $simple Imprime el Plan de Pagos sin detallarla Lista
	 * @param boolean $NoExtend es a TRue solo muestra el Plan de pagos
	 * @return string de Pagos en Formto definido en OUT
	 */
	function getPlanDePago($InOut = OUT_HTML, $simple = false, $NoExtender = false, $tools = false) {
		$PlanBody = "";
		if($this->mCreditoInicializado == false){ $this->init ();	}
		$plan = setNoMenorQueCero ( $this->getNumeroDePlanDePagos () );
		if (($this->getPeriocidadDePago () != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) AND $plan >0) {
			$xPlan 							= new cPlanDePagos ( $plan );
			if ($xPlan->init () == true) {
				$NoExtender 				= ($NoExtender == false) ? true : false;
				$PlanBody 					= $xPlan->getVersionImpresa ( $NoExtender, $NoExtender, $simple, $tools );
				$this->mFechaUltimaParc 	= $xPlan->getFechaPlanUltimoPago();
				$this->mFechaPrimeraParc 	= $xPlan->getFechaPlanPrimerPago();
			}
			$this->mMessages .= $xPlan->getMessages ();
		} else {
			$monto_con_interes 				= $this->getMontoAutorizado() + ($this->getInteresDiariogenerado() * $this->getDiasAutorizados());
			$PlanBody						= "<table class='plan_de_pagos'><thead><tr><th>Pago</th><th>Fecha</th><th>Monto</th></tr></thead><tbody><tr><td>Unico</td><td>" . $this->getFechaDevencimientoLegal() . "</td><td>" . getFMoney($monto_con_interes) . "</td></tr></tbody></table>";  
		}
		
		return $PlanBody;
	} // END FUNCTION
	function getOPlanDePagos() {
		if ($this->mObjPlan == null AND $this->isAFinalDePlazo() == false AND $this->getNumeroDePlanDePagos() >0) {
			$this->mObjPlan = new cPlanDePagos( $this->getNumeroDePlanDePagos() );
			$this->mObjPlan->initByCredito($this->getNumeroDePlanDePagos());
			$this->mObjPlan->init();
		}
		if ($this->mObjPlan == null AND $this->isAFinalDePlazo() == false ){
			//XXX: Aviso
		}
		if ($this->mObjPlan == null){
			$this->mObjPlan = new cPlanDePagos(0);
		}
		return $this->mObjPlan;
	}
	/**
	 * @deprecated @since 2015.07.01 
	  */
	function getDatosOficialDeCredito_InArray() {
		if ($this->mCreditoInicializado == false) {
			$this->initCredito ();
		}
		if ($this->mOficialDeCredito != false) {
			$sql = "SELECT id, nombre_completo, puesto, sucursal, estatus
    				FROM oficiales
    				WHERE id = " . $this->mOficialDeCredito;
			$this->mDatosOficialDeCredito = obten_filas ( $sql );
		}
		return $this->mDatosOficialDeCredito;
	}
	
	/**
	 * Funcion que Actualiza el Credito segun un array tipo Campo=>valor
	 */
	function setUpdate($aParam, $soloActualizar = false) {
		$WithSocio	= "";
		$xQL		= new MQL();
		$x			= true;
		/**
		 * Marcar Segunda condicion
		 */
		if (setNoMenorQueCero ( $this->mNumeroSocio ) > DEFAULT_SOCIO) {
			//$WithSocio = "	AND	(`creditos_solicitud`.`numero_socio` =" . $this->mNumeroSocio . ")  ";
		}
		$sqlBody = "";
		if (is_array ( $aParam ) AND count ( $aParam ) >= 1) {
			$BodyUpdate = "";
			//if($this->isAFinalDePlazo() == false AND $this->getEsAfectable() == false){
				//if(!isset($aParam["tasa_cat"])){
				//	$aParam["tasa_cat"]	= 0;	//Forzar el Update de Tasa CAT
				//}
			//}
			foreach ( $aParam as $key => $value ) {
				// Buscar en el Valor el Nombre del Field
				// $pos = stripos($value, $key);
				// Si el Valor es una Cadena y no existe el Nombre del field
				if (is_string($value) == true) {
					$value = "'" . addslashes($value) . "'";
				}
				if ($BodyUpdate == "") {
					$BodyUpdate .= "`$key` = $value ";
				} else {
					$BodyUpdate .= ",`$key` = $value ";
				}
			} // END FOREACH
			$sqlBody 	= "UPDATE `creditos_solicitud` SET $BodyUpdate WHERE `numero_solicitud` =" . $this->mNumeroCredito . " ";
			$x 			= $xQL->setRawQuery( $sqlBody );
			//my_query($sqlBody);
			//setLog($sqlBody);
			if ($soloActualizar == false) {
				$this->setCuandoSeActualiza();
			}
			
		} else {
			$x	= false;
		}
		return $x;
	}
	/**
	 * Funcion que genera el Credito Reconvenido
	 * @param float $monto_reconvenido del capital por el Cual se reconviene el credito
	 * @param float $interes_reconvenido del interes por el Cual se reconviene el credito
	 * @return boolean false/true segun el resultado de la funcion
	 */
	function setReconvenido($monto_reconvenido, $interes_reconvenido, $tasa_reconvenida, $periocidad_reconvenida, $pagos_reconvenidos, $observaciones = "", $fecha = false, $recibo = false, $FormaDePago = false, $producto = false, $conservarPlan = false) {
		if($this->mCreditoInicializado == false) { $this->init(); }
		$sucess 		= false;
		$fecha 			= ($fecha == false) ? fechasys () : $fecha;
		$producto 		= ($producto == false) ? $this->getClaveDeProducto () : $producto;
		$plan_de_pagos 	= $this->getNumeroDePlanDePagos ();
		$plan_de_pagos	= setNoMenorQueCero($plan_de_pagos);
		$xLog			= new cCoreLog();
		$recibo			= setNoMenorQueCero($recibo);
		$idcredito		= $this->getNumeroDeCredito();
		
		if($this->isAFinalDePlazo() == false AND $plan_de_pagos <= 0){
			$this->mCreditoInicializado	= false;
			$xLog->add("ERROR\tSe requiere un plan de pagos($plan_de_pagos)\r\n");
		}
		if ($this->mCreditoInicializado == true) {
			$xLog->add("WARN\Reconvenio del Credito ". $this->mNumeroCredito . " a Monto $monto_reconvenido Tasa $tasa_reconvenida Periodo $periocidad_reconvenida Pagos $pagos_reconvenidos\r\n");
			$dias 			= $periocidad_reconvenida * $pagos_reconvenidos;
			$vence 			= sumardias ( $fecha, $dias );
			$credito 		= $this->getNumeroDeCredito ();
			// $interes_normal = ($saldo_historico * $dias_normales * ($tasa_interes * $factor_interes)) / EACP_DIAS_INTERES;
			
			$interes_normal 	= 0;
			$FInteres 			= new cFormula ( "interes_normal" );
			$saldo_historico 	= $this->getMontoAutorizado (); // $monto_reconvenido;
			$saldo_actual 		= $this->getSaldoActual (); // $monto_reconvenido;
			$dias_normales 		= $dias;
			$tasa_interes 		= $tasa_reconvenida;
			$factor_interes 	= 1;
			
			if ($this->mIVAIncluido == "1") {
				$factor_interes = 1 / (1 + $this->getTasaIVA ());
			}
			// eval( $FInteres->getFormula() );
			
			// Agregar el SQL
			$xRC 				= new cCreditos_reconvenio ();
			$idconvenio 		= $xRC->query ()->getLastID ();
			$xRC->idcreditos_reconvenio ( $idconvenio );
			$xClon 				= $this->setClonar ( $saldo_actual, $saldo_actual);// $saldo_historico );
			$xLog->add("EL Historial de credito " . $this->mNumeroCredito . " se ha quedado en $xClon\r\n");
			$xRC->numero_solicitud ( $idcredito );
			$xRC->codigo ( $this->getClaveDePersona () );
			$xRC->credito_origen ( $this->getNumeroDeCredito () );
			$xRC->dias ( $dias );
			$xRC->eacp ( EACP_CLAVE );
			$xRC->fecha_reconvenio ( $fecha );
			$xRC->idusuario ( getUsuarioActual () );
			$xRC->interes_diario_re ( $interes_normal );
			$xRC->interes_pendiente ( $interes_reconvenido );
			$xRC->monto_reconvenido ( $monto_reconvenido );
			$xRC->pagos_reconvenidos ( $pagos_reconvenidos );
			$xRC->periocidad_reconvenida ( $periocidad_reconvenida );
			$xRC->sucursal ( getSucursal () );
			$xRC->tasa_reconvenida ( $tasa_reconvenida );
			$xRC->vence ( $vence );
			$x = $xRC->query ()->insert ()->save ();
			
			if ($x != false) {
				// Modificar movimientos
				$observaciones = ($observaciones == "") ? "" : "RNV $idconvenio. $credito-$xClon";
				// Agregare el Movimiento
				$cRecReest = new cReciboDeOperacion ( RECIBOS_TIPO_ESTADISTICO, true, $recibo );
				if ($recibo <= 0) {
					$recibo = $cRecReest->setNuevoRecibo ( $this->mNumeroSocio, $xClon, $fecha, 1, RECIBOS_TIPO_ESTADISTICO, $observaciones, DEFAULT_CHEQUE, TESORERIA_COBRO_NINGUNO, DEFAULT_RECIBO_FISCAL, $this->mGrupoAsociado );
					$xLog->add("WARN\tSe agrega el Recibo $recibo");
					$cRecReest->setNumeroDeRecibo($recibo, true);
				}
				$cRecReest->init();
				/*
				 * No generar poliza, ya que no ha cumplido el pago sostenido
				 */
				
				// agregar Movimiento por el Monto Reconvenido
				$cRecReest->setNuevoMvto ( $fecha, $monto_reconvenido, OPERACION_CLAVE_REESTRUCTURA, $this->getPeriodoActual(), $observaciones, 1, false, $this->getClaveDePersona(), $this->getNumeroDeCredito() );
				$cRecReest->setFinalizarRecibo(true);
				$xLog->add( $cRecReest->getMessages (), $xLog->DEVELOPER);
				// agregar el Movimiento por Intereses no pagados
				$xCredClon 		= new cCredito ($xClon);
				$xCredClon->init();
				
				// Actualizar el saldo y demas
				$cEsUp = array (
						$this->obj()->tipo_autorizacion ()->get () => CREDITO_AUTORIZACION_REESTRUCTURADO,
						$this->obj()->saldo_vencido ()->get () => 0 
				);
				$xCredClon->setUpdate ( $cEsUp, true );
				$this->setUpdate ( $cEsUp, true );
				
				$this->setAbonoCapital ( $this->getSaldoActual (), $this->mParcialidadActual, DEFAULT_CHEQUE, TESORERIA_COBRO_NINGUNO, DEFAULT_RECIBO_FISCAL, $observaciones );
				$this->init();
				
				$sucess 		= true;
				//$xLog->add( $this->setChangeNumeroDeSolicitud ( $xClon, true ), $xLog->DEVELOPER);
				// Cambiar Producto
				$xLog->add( $xCredClon->setCambioProducto ( $producto, $tasa_reconvenida ), $xLog->DEVELOPER);
				// cambiar fecha de ministracion
				$xLog->add($xCredClon->setCambiarFechaMinistracion ( $fecha ), $xLog->DEVELOPER);
				// Cambiar Monto Ministrado
				//
				$xLog->add($xCredClon->setCambiarMontoAutorizado ( $monto_reconvenido, true ), $xLog->DEVELOPER);
				// Ministrar
				$xCredClon->setForceMinistracion ();
				$xCredClon->setMinistrar ( DEFAULT_RECIBO_FISCAL, DEFAULT_CHEQUE, $monto_reconvenido, DEFAULT_CUENTA_BANCARIA, 0, 0, "REESTRUCTURA DE CREDITO $xClon ", $fecha, false, TESORERIA_PAGO_NINGUNO );
				if ($xCredClon->getTasaDeInteres () != $tasa_reconvenida) {
					$xLog->add( $xCredClon->setCambiarTasaNormal ( $tasa_reconvenida ), $xLog->DEVELOPER);
				}
				$xLog->add( $xCredClon->setCambiarPeriocidad ( $periocidad_reconvenida, $pagos_reconvenidos, $FormaDePago ), $xLog->DEVELOPER);
				$xLog->add( $xCredClon->setCambiarMontoMinistrado ( $monto_reconvenido, true ), $xLog->DEVELOPER);
				if ($conservarPlan == true) {
					if ($plan_de_pagos > 0) {
						// $xRec = new cReciboDeOperacion(RECIBOS_TIPO_PLAN_DE_PAGO, true, $plan_de_pagos);
						// $xRec->init();
						// $xRec->setDocumento($this->getNumeroDeCredito());
						//$xPlan = new cPlanDePagos ( $plan_de_pagos );
						// $xPlan->init();
						//$xPlan->setCambiarRelacionados ( $credito );
						//$xLog->add( $xPlan->getMessages ( OUT_TXT ), $xLog->DEVELOPER);
					}
				}
				$xLog->add( $xCredClon->getMessages(), $xLog->DEVELOPER);
				$this->mMessages		.= $xLog->getMessages();
				// Actualizar Estatus
				//Agregar Notas
				$xSoc	= new cSocio($this->mNumeroSocio);
				if($xSoc->init() == true){
					$xSoc->addMemo(MEMOS_TIPO_NOTA_RENOVACION, $xLog->getMessages(), $this->mNumeroCredito);
					$xSoc->addMemo(MEMOS_TIPO_NOTA_RENOVACION, $xLog->getMessages(), $xClon);
				}
				//Datos de Origen
				$xOrg	= new cCreditosDatosDeOrigen();
				$xCredClon->addDatosDeOrigen($xOrg->ORIGEN_REESTRUCTURA, $this->mNumeroCredito);
				
				// $this->setDetermineDatosDeEstatus()
			} // END IF
				  // $this->setReestructurarIntereses();
		} // END Credito Inicializado
		return $sucess;
	} // END FUNCTION
	/**
	 * funcion que devuelve un array por el credito
	 * @return array Matriz de los Datos del credito INICIALIZADO
	 */
	function getDatosDeCredito(){ if ($this->mCreditoInicializado == false){ $this->init(); } return $this->mArrDatosDeCredito;	}
	function getDatosInArray(){ return $this->getDatosDeCredito();	}
	function getDatosDeProducto($convenio = false) {
		$convenio = setNoMenorQueCero ( $convenio );
		$convenio = ($convenio <= 0) ? $this->mTipoDeConvenio : $convenio;
		$xCV = new cProductoDeCredito ( $convenio );
		$xCV->init ();
		
		return $xCV->getDatosInArray ();
	}
	/**
	 * devuelve en una array los datos del convenio de credito
	 */
	function getDatosDeReconvenio() {
		$arrDR = array ();
		
		if ($this->mNumeroCredito != false) {
			$BySocio = "";
			if ($this->mNumeroSocio != false) {
				$BySocio = " AND (codigo = " . $this->mNumeroSocio . ") ";
			}
			$sql = "SELECT
	 					idcreditos_reconvenio, numero_solicitud, fecha_reconvenio,
	 					monto_reconvenido, periocidad_reconvenida, tasa_reconvenida,
	 					idusuario, codigo, pagos_reconvenidos, dias, vence,
	 					interes_diario_re, sucursal, interes_pendiente, eacp
    				FROM creditos_reconvenio
    				WHERE
    					numero_solicitud	= " . $this->mNumeroCredito . "
    					$BySocio";
			$arrDR = obten_filas ( $sql );
		}
		return $arrDR;
	} // End Function getdatosdereconvenio
	/**
	 * Actualiza los Creditos Reconvenidos
	 * 
	 * @param array $aParam de Field => Valor a actualizar
	 * @return boolean Booleano de la Actualizacion
	 */
	function setUpdateReconvenio($aParam) {
		$withSocio = "";
		/**
		 * Marcar Segunda condicion
		 */
		if ($this->mNumeroSocio != false) {
			$withSocio = "	AND
							(`creditos_reconvenio`.`codigo` =" . $this->mNumeroSocio . ")  ";
		}
		$sqlBody = "";
		
		$BodyUpdate = "";
		foreach ( $aParam as $key => $value ) {
			if (is_string ( $value )) {
				$value = "\"" . $value . "\"";
			}
			if ($BodyUpdate == "") {
				$BodyUpdate .= "$key = $value ";
			} else {
				$BodyUpdate .= ", $key = $value ";
			}
		} // END FOREACH
		$sqlBody = "UPDATE creditos_reconvenio
						    SET $BodyUpdate
						    WHERE
					(`creditos_reconvenio`.`numero_solicitud` =" . $this->mNumeroCredito . ")
					$WithSocio";
		$x = my_query ( $sqlBody );
		
		return $x ["stat"];
	}
	/**
	 * Funcion que Envia a Creditos Vigentes un Vencido
	 * @param variant $fecha Fecha de Movimiento
	 * @param integer $parcialidad Parcialidad en que se envia a vencido
	 * @param integer $recibo Numero de Recibo que Carga el Movimiento
	 * @return string del proceso.
	 */
	function setEnviarVigente($fecha, $parcialidad, $recibo = false) {
		// Modificar agregar el cambio de estatus
		$FMora 		= $this->mFechaMora;
		$FVenc 		= $this->mFechaVencimientoLegal;
		$monto 		= $this->mSdoCapitalInsoluto;
		$socio 		= $this->mNumeroSocio;
		$solicitud 	= $this->mNumeroCredito;
		$estatus 	= CREDITO_ESTADO_VIGENTE;
		$xT 		= new cTipos ();
		$msg = "";
		if ($xT->cBool ( CREDITO_GENERA_MVTO_VIGENTE ) == true) {
			// ========================================================
			$xRec = new cReciboDeOperacion ( 10 );
			$xRec->setNumeroDeRecibo ( $recibo );
			// ========================================================
			//
			$nota 	= "Estatus [$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc";
			$nota	= "";
			// agregar Movimiento con Partida
			$xMvto = $xRec->setNuevoMvto ( $fecha, $monto, 114, $parcialidad, $nota, 1, TM_CARGO, $socio, $solicitud );
			// Agregar Contrapartida
			$xRec->addMvtoContable ( 111, $monto, TM_ABONO, $socio, $solicitud );
			$msg = "$socio\t$solicitud\tVIGENTE\tEstatus[$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc\r\n";
		} else {
			$this->setCambiarEstadoActual ( $estatus, $fecha );
		}
		$this->mMessages .= $msg;
		return $msg;
	}
	/**
	 * Funcion que Efectua los pasos necesarios para que un credito esté vencido.
	 * @param variant $fecha	        	Proceso
	 * @param integer $parcialidad  	Parcialidad
	 * @param integer $recibo	      	Recibo al que se agrega el movimiento, si es false se crea uno.
	 * @return string del proceso.
	 *        
	 */
	function setEnviarVencido($fecha, $parcialidad, $recibo = false) {
		$FMora = $this->mFechaMora;
		$FVenc = $this->mFechaVencimientoLegal;
		$monto = $this->mSdoCapitalInsoluto;
		// ========================================================
		$xRec 		= new cReciboDeOperacion ( 10 );
		$xRec->setNumeroDeRecibo ( $recibo );
		// ========================================================
		$socio 		= $this->mNumeroSocio;
		$solicitud 	= $this->mNumeroCredito;
		$estatus 	= 20;
		
		$nota 		= "Estatus [$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc";
		$nota		= "";
		// agregar Movimiento con Partida
		$xMvto 		= $xRec->setNuevoMvto ( $fecha, $monto, 111, $parcialidad, $nota, 1, TM_CARGO, $socio, $solicitud );
		// Agregar Contrapartida
		$xRec->addMvtoContable ( 111, $monto, TM_ABONO, $socio, $solicitud );
		
		// Agrega las Modificaciones del Credito
		// $arrUpdate = array( "estatus_actual" => $estatus,
		// "notas_auditoria" => $nota );
		
		// $this->setUpdate($arrUpdate);
		$msg = "$socio\t$solicitud\tVENCIDO\tEstatus[$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc\r\n";
		return $msg;
	}
	function setEnviarMoroso($fecha, $parcialidad, $recibo = false) {
		$FMora = $this->mFechaMora;
		$FVenc = $this->mFechaVencimientoLegal;
		$monto = $this->mSdoCapitalInsoluto;
		$socio = $this->mNumeroSocio;
		$solicitud = $this->mNumeroCredito;
		$estatus = CREDITO_ESTADO_MOROSO;
		$xT = new cTipos ();
		$msg = "";
		if ($xT->cBool ( CREDITO_GENERAR_MVTO_MORA ) == true) {
			// ========================================================
			$xRec = new cReciboDeOperacion ( 10 );
			$xRec->setNumeroDeRecibo ( $recibo );
			// ========================================================
			$nota 	= "Estatus [$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc";
			$nota	= "";
			// agregar Movimiento con Partida
			$xMvto 	= $xRec->setNuevoMvto ( $fecha, $monto, 115, $parcialidad, $nota, 1, TM_CARGO, $socio, $solicitud );
			// Agregar Contrapartida
			$xRec->addMvtoContable ( 115, $monto, TM_ABONO, $socio, $solicitud );
			$msg = "$socio\t$solicitud\tMOROSO\tEstatus[$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc\r\n";
		} else {
			$this->setCambiarEstadoActual ( $estatus, $fecha );
		}
		$this->mMessages .= $msg;
		return $msg;
	}
	/**
	 * Verifica la validez del Credito con Distintos parametros
	 * @param integer $consecutivo Consecutivo de la Validacion, no requerido ni vital.
	 * @param string $put salida html:txt
	 * @return string de validación
	 */
	function setVerificarValidez($out = OUT_TXT, $PasoCredito = false) {
		$msgIncidencias 	= "";
		$mReportar 			= false;
		$arrEquivalencias 	= array (
				"tasa_ahorro" 		=> "tasa_ahorro",
				"tipo_autorizacion" => "tipo_autorizacion",
				"tipo_credito" 		=> "tipo_de_credito" 
		);
		$xF 			= new cFecha();
		$OConv 			= $this->getOProductoDeCredito();
		$DConv 			= $OConv->getDatosInArray();
		$DCred 			= $this->getDatosDeCredito();
		$xLog 			= new cCoreLog();
		$xPasos			= new cCreditosProceso();
		$valido 		= true;
		$xSoc 			= new cSocio($this->getClaveDePersona());
		$salvarReglas	= true;
		$xReg			= new cReglasDeCalificacion();
		$xVal			= new cReglasDeValidacion();
		$xRuls			= new cReglaDeNegocio();
		$xReg->setCredito($this->getClaveDeCredito());
		
		$SinFechaAnt	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_SOLICITUD_SIN_FECHA_ANT);		//regla de negocio
		$SinPerAnt		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_SOLICITUD_SIN_PERIODO_ANT);		//regla de negocio
		$SinValAE		= $xRuls->getValorPorRegla($xRuls->reglas()->VAL_NO_PERSONA_FALTA_ACT_ECONOM);
		
		if ($xSoc->init () == true) {
			if($xSoc->getValidacion(false) == false){
				$valido 		= false;
				$this->mValidacionERRS++;
				$xReg->add($xReg->CRED_FALLA_PERSONA);
				$xLog->add ("ERROR\tLa persona tiene errores\r\n" );
			} else {
				$xReg->add($xReg->CRED_FALLA_PERSONA, true);
			}
			$xReg->setTipo(iDE_CREDITO);
			
			// verificar los Datos Coincidentes con el Convenio
			$iChkConv = count ( $arrEquivalencias ) - 1;
			foreach ( $arrEquivalencias as $key => $value ) {
				if ($DCred [$key] != $DConv [$value]) {
					$txt = strtoupper ( $key );
					$xLog->add ( "WARN\t$txt\tEl Valor de $txt es igual a " . $DCred [$key] . " cuando debe ser " . $DConv [$value] . " \r\n" );
					$this->mValidacionWARNS++;
				}
			}
			if(PERSONAS_CONTROLAR_POR_GRUPO == true){
				if ($OConv->getEsGrupal() == true) {
					if ($this->getClaveDeGrupo () == DEFAULT_GRUPO) {
						$xLog->add ("ERROR\tEl Grupo de Trabajo no debe ser igual al valor por defecto\r\n" );
						$valido 		= false;
						$this->mValidacionERRS++;
						$xReg->add($xReg->CRED_FALTA_GRUPO);
					} else {
						$xG = new cGrupo ( $this->getClaveDeGrupo () );
						if ($xG->init () == false) {
							$xLog->add ("ERROR\tEl Grupo No existe\r\n" );
							$xLog->add ($xG->getMessages (), $xLog->DEVELOPER );
							$valido 	= false;
							$this->mValidacionERRS++;
							$xReg->add($xReg->CRED_FALTA_GRUPO);
						}
					}
				}
			}
			if($this->isAFinalDePlazo() == false){
				if($this->getEsCreditoYaAfectado() == true OR $this->getEsAfectable() == true OR $this->getEsAutorizado() == true){
					$xPlan	= new cReciboDeOperacion(false, false, $this->getNumeroDePlanDePagos());
					if($xPlan->init() == false){
						$xLog->add ("ERROR\tEl Plan de Pagos " . $this->getNumeroDePlanDePagos() . " no existe\r\n" );
						$xLog->add ($xPlan->getMessages (), $xLog->DEVELOPER );
						$valido = false;
						$this->mValidacionERRS++;
						$xReg->add($xReg->CRED_FALTA_PLAN);
					} else {
						$xReg->add($xReg->CRED_FALTA_PLAN, true);
					}
				}

			}
			// Saldo vs estatus
			if ($this->getSaldoActual () > 0 and $this->getEsAfectable () == false) {
				$xLog->add ( "ESTATUS\tEl Estatus de Credito es INCORRECTO, Existe UN SALDO VALIDO " . $this->getSaldoActual () . "\r\n" );
				$valido = false;
				$this->mValidacionERRS++;
				$xReg->add($xReg->CRED_FALLA_STATUS);
			} else {
				$xReg->add($xReg->CRED_FALLA_STATUS, true);
			}
			// cuenta vs existencia
			if ($OConv->getTasaDeAhorro () > 0) {
				if ($this->getContratoCorriente () != DEFAULT_CUENTA_CORRIENTE AND $this->getContratoCorriente () > 0) {
					$xCta = new cCuentaALaVista ( $this->getContratoCorriente () );
					if ($xCta->init () == false) {
						$xLog->add ( "ERROR\tLa Cuenta de Deposito no es valida\r\n" );
						$xLog->add ( $xCta->getMessages () );
						$valido = false;
						$this->mValidacionERRS++;
						$xReg->add($xReg->CRED_FALLA_CAHORR);
					} else {
						$xReg->add($xReg->CRED_FALLA_CAHORR, true);
					}
				} else {
					$xLog->add ( "ERROR\tDebe existir una Cuenta de Deposito valida\r\n" );
					$valido 		= false;
					$this->mValidacionERRS++;
					$xReg->add($xReg->CRED_FALTA_CAHORR);
				}
			}
			// validar usuario
			$xOficial = new cSystemUser ( $this->getClaveDeOficialDeCredito () );
			if ($xOficial->init () == false) {
				$xLog->add ( "ERROR\tDebe existir un Oficial de Credito valido\r\n" );
				$xLog->add ( $xOficial->getMessages () );
				$valido = false;
				$this->mValidacionERRS++;
				$xReg->add($xReg->CRED_FALLA_OFICIAL);
			} else {
				$xReg->add($xReg->CRED_FALLA_OFICIAL, true);
			}
			//Numero de Avales
			$NumAvales	= $OConv->getNumeroDeAvales();
			if($NumAvales > 0){
				$xAval				= new cCreditosAvales();
				$xAval->initByCredito($this->getClaveDeCredito(), $this->getClaveDePersona());
				$xAval->initArbolAvalesDirectos();
				$NumAvalesPorCred	= $xAval->getNumeroAvalesDirectos();
				
				if($NumAvalesPorCred < $NumAvales){
					$xLog->add ( "ERROR\tEl Numero de Avales del Credito $NumAvalesPorCred es menor al requerido $NumAvales\r\n" );
					$xLog->add ( $xAval->getMessages () );
					$valido = false;
					$this->mValidacionERRS++;
					$xReg->add($xReg->CRED_FALTA_AVALES);
				} else {
					$xReg->add($xReg->CRED_FALTA_AVALES, true);
				}
			} else {
				$xReg->add($xReg->CRED_FALTA_AVALES, true);
				$xLog->add( "OK\tNo aplica avalaes( $NumAvales ) para el producto " . $OConv->getNombre() .  " \r\n", $xLog->DEVELOPER );
			}
			//Resguardo de Garantia
			$RazonGarantia		= $OConv->getRazonGarantia();
			$MontoQueDebeG		= 0;	//Monto que debe Garantizar
			$MontoQueGar		= 0;	//Monto que garantiza
			$xGar				= new cCreditosGarantias();
			$xGar->setClaveDeCredito($this->getClaveDeCredito());
			//	
			// Propio de estatus
			if ($this->getEstadoActual() == CREDITO_ESTADO_SOLICITADO) {
				$MontoQueDebeG	= $this->getMontoSolicitado();
				if($RazonGarantia > 0){ 
					$MontoQueGar	= $xGar->getMontoPresentado(); 
				}
			} else if ($this->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO) {
				//Validar persona
				$MontoQueDebeG	= $this->getMontoAutorizado();
				if($RazonGarantia > 0){ 
					$MontoQueGar	= $xGar->getMontoResguardado(); 
				}
				
			} else {
				$MontoQueDebeG	= $this->getSaldoActual();
			}
			if ($this->getEstadoActual() == CREDITO_ESTADO_SOLICITADO) {
				$xReg->add($xReg->CRED_FALLA_PAGMAX,true);
			} else {
				if ($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
					if ($this->getPagosAutorizados () > 1) {
						$xLog->add ( "ERROR\tLa Periocidad de Pago no Acepta mas de  un pago\r\n" );
						$valido = false;
						$this->mValidacionERRS++;
						$xReg->add($xReg->CRED_FALLA_NPAGOS);
					} else {
						$xReg->add($xReg->CRED_FALLA_NPAGOS, true);
					}
				} else {

					if ($this->getPagosAutorizados() > $OConv->getPagosMaximos ()) {
						$xLog->add ( "ERROR\tPAGOS_MAX\tEl Valor de Pagos Autorizados es igual a " . $this->getPagosAutorizados () . " cuando debe ser como Maximo " . $OConv->getPagosMaximos () . " \r\n" );
						$valido = false;
						$this->mValidacionERRS++;
						$xReg->add($xReg->CRED_FALLA_PAGMAX);
					} else {
						$xReg->add($xReg->CRED_FALLA_PAGMAX,true);
					}
					if ($this->getPagosAutorizados () < $OConv->getPagosMinimo ()) {
						$xLog->add ( "ERROR\tPAGOS_MIN\tEl Valor de Pagos Autorizados es igual a " . $this->getPagosAutorizados () . " cuando debe ser como Minimo " . $OConv->getPagosMinimo () . " \r\n" );
						$valido = false;
						$this->mValidacionERRS++;
						$xReg->add($xReg->CRED_FALLA_PAGMIN);
					} else {
						$xReg->add($xReg->CRED_FALLA_PAGMIN, true);
					}
				}
			}
			
			//===================== Validar tipo
			$itemValEmp		= true;
			if($this->getEsNomina() == true){
				$emp		= $xVal->cuenta($this->getClaveDeEmpresa());
				if($emp == false){
					$itemValEmp	= false;
				}
			}
			if($itemValEmp == false) {
				$valido = false;
				$this->mValidacionERRS++;
				$xReg->add($xReg->CRED_FALTA_EMPRESA);
				$xLog->add ( "ERROR\tEMPRESA\tEl Credito es Nomina y requiere un Empleador Valido : "  . $this->getClaveDeEmpresa() .  " \r\n" );
				
			} else {
				$xReg->add($xReg->CRED_FALTA_EMPRESA, true);
			}
			
			//===================== Validar Garantia
			if($RazonGarantia > 0){
				if($MontoQueDebeG < $MontoQueGar){
					$xLog->add ("ERROR\tLa Garantia del Credito que debe cubrirse $MontoQueDebeG es menor ($MontoQueGar)\r\n" );
					$valido = false;
					$this->mValidacionERRS++;
					$xReg->add($xReg->CRED_FALTA_GARANT);
				} else {
					$xReg->add($xReg->CRED_FALTA_GARANT, true);
				}
			}
			//===================== Validar Tasa de IVA
			$xApp	= new cCreditosDestinos($this->getClaveDeDestino());
			if($xApp->init() == true){
				if($this->getTasaIVA() != $xApp->getTasaIVA()){
					$xLog->add ("ERROR\tLa tasa de IVA es diferente a el Destino\r\n" );
					$valido				 = false;
					$this->mValidacionERRS++;
					$xReg->add($xReg->CRED_FALLA_DESTIVA);					
				} else {
					$xReg->add($xReg->CRED_FALLA_DESTIVA, true);
				}
			} else {
				$xLog->add ("ERROR\tEl Destino / Aplicacion no existe\r\n" );
				$valido				 = false;
				$this->mValidacionERRS++;
				$xReg->add($xReg->CRED_FALLA_DEST);				
			}
			//Validar Periodo de Credito
			
			if($this->getEsArrendamientoPuro() == true){
				$xCredOrg	= new cCreditosLeasing($this->getClaveDeOrigen());
				if($xCredOrg->init() == true){
					$xReg->add($xReg->CRED_FALLA_O_ARR, true);
					
				} else {
					$xReg->add($xReg->CRED_FALLA_O_ARR);
					$this->mValidacionERRS++;
					$xLog->add("ERROR\tLa Cotizacion de Arrendamiento no existe\r\n" );
				}
				//Validar vehiculo
				
				$xAct	= new cLeasingActivos();
				if($xAct->initForContract($this->getClaveDeCredito()) == true){
					$xReg->add($xReg->CRED_ARRED_NOACT, true);
				} else {
					$xReg->add($xReg->CRED_ARRED_NOACT);
					$this->mValidacionERRS++;
					$xLog->add("ERROR\tEl Activo y/o Vehiculo no existe\r\n");
				}
			}
			if($this->getEsReestructuracion() == true){
				
				if($xSoc->existeCredito($this->getClaveDeOrigen()) == false OR ( $xVal->credito($this->getClaveDeOrigen())== false ) ){
					$xReg->add($xReg->CRED_FALTA_DREEST);
					$this->mValidacionERRS++;
					$xLog->add("ERROR\tFaltan datos de Reestructura\r\n");
				} else {
					$xReg->add($xReg->CRED_FALTA_DREEST, true);
				}
			} else {
				$xReg->add($xReg->CRED_FALTA_DREEST, true);
			}
			if($this->getEsRenovado() == true){
				if($xSoc->existeCredito($this->getClaveDeOrigen()) == false OR ( $xVal->credito($this->getClaveDeOrigen())== false ) ){
					$xReg->add($xReg->CRED_FALTA_DRENOV);
					$this->mValidacionERRS++;
					$xLog->add("ERROR\tFaltan datos de Renovacion\r\n");
				} else {
					$xReg->add($xReg->CRED_FALTA_DRENOV, true);
				}
			} else {
				$xReg->add($xReg->CRED_FALTA_DRENOV, true);
			}
			
			$RazonGtiaLiq			= $OConv->getTasaDeGarantiaLiquida();
			//MODULO_CAPTACION_ACTIVADO == true AND 
			if($RazonGtiaLiq > 0){
				
				if($this->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO){
					$MontoGtiaLiq	= round(($this->getMontoAutorizado() * $RazonGtiaLiq),2);
					
					if(GARANTIA_LIQUIDA_EN_CAPTACION == true){
						$cuentas		= $xSoc->getContarDoctos(iDE_CAPTACION,false, false, CAPTACION_PRODUCTO_GARANTIALIQ);
						if($cuentas <= 0){
							$xReg->add($xReg->CRED_FALTA_GTIALIQ);
							$this->mValidacionERRS++;
							$xLog->add("ERROR\tFalta la Garantia Liquida\ en Cuentasr\n");
						} else {
							$xVista	= new cCuentaALaVista(false);
							$res	= $xVista->initCuentaPorProducto($this->getClaveDePersona(), CAPTACION_PRODUCTO_GARANTIALIQ);
							if($res == false){
								$xReg->add($xReg->CRED_FALTA_GTIALIQ);
								$this->mValidacionERRS++;
								$xLog->add("ERROR\tFalta la Garantia Liquida en Cuentas\r\n");
							} else {
								$LoQueHay	= $xVista->getSaldoActual();
								if($LoQueHay < $MontoGtiaLiq ){
									$xReg->add($xReg->CRED_FALTA_GTIALIQ, true);
									$xReg->add($xReg->CRED_FALLA_GTIALIQ);
									$this->mValidacionERRS++;
									$xLog->add("ERROR\tGarantia Liquida Insuficiente\r\n");
								} else {
									$xReg->add($xReg->CRED_FALLA_GTIALIQ, true);
									$xReg->add($xReg->CRED_FALTA_GTIALIQ, true);
								}
							}
						}
					} else {
						//Sumar Operaciones de garantia Liquida
						$SdoGtiaLiq		= round($this->getGarantiaLiquidaPagada(),2);
						if($SdoGtiaLiq >= $MontoGtiaLiq){
							$xReg->add($xReg->CRED_FALTA_GTIALIQ, true);
						} else {
							$xLog->add("ERROR\tGarantia Liquida ($SdoGtiaLiq) Insuficiente\r\n");
							$this->mValidacionERRS++;
							$xReg->add($xReg->CRED_FALTA_GTIALIQ);
						}
					}
					//$xCapta			= new cCuentaDeCaptacion(false)
				}
				//Otros de captacion
			}
			//========================== Por Pasos
			switch ($PasoCredito){
				case $xPasos->PASO_ADESEMBOLSO:
					if($OConv->getAplicaGtosNot() == true){
						$gastos_not_pagados = $this->getPagoDeGastosNotariales ();
						if ($gastos_not_pagados < TOLERANCIA_SALDOS) {
							$xLog->add("ERROR\tNo ha Pagado sus Gastos Notariales\r\n");
							$valido 	= false;
							$this->mValidacionERRS++;
						}
					}
					$montomin = $this->getSumMovimiento ( OPERACION_CLAVE_MINISTRACION );
					if ($montomin >= TOLERANCIA_SALDOS) {
						$xLog->add("ERROR\tEl credito se ha ministrado de forma parcial / Total, o se ha forzado su edicion; el Monto Ministrado es $montomin \r\n");
						$valido 		= false;
						$this->mValidacionERRS++;
					}
					// verificar si pago su fondo de defuncion. // SI ES DIFERENTE A AUTOMATIZADO
					
					$fondo_def_ob 	= $OConv->getMontoFondoDefuncion();
					if($fondo_def_ob > 0){
						$fondo_def_pag 		= $xSoc->getFondoDeDefuncion();
						if($fondo_def_pag < $fondo_def_pag) {
							$xLog->add("ERROR\tNo ha Pagado sus Fondo de Defuncion por $fondo_def_pag, ha pagado $fondo_def_pag \r\n");
							$valido 		= false;
							$this->mValidacionERRS++;
						}
					}
					// VERIFICA LA GARANTIA SEGUN TIPO CONVENIO.- La seleccion se hace segun el Numero de Socio y no debe estar entregada
					// NO APLICA EN GRUPOS SOLIDARIOS
					$razon_garantias = $OConv->getRazonGarantia();
					if ($razon_garantias > 0){
						$monto_garantizado 		= $xSoc->getGarantiasFisicasDepositadas ();
						$monto_a_garantizar 	= $this->getMontoAutorizado() * $razon_garantias;
						if ($monto_garantizado < $monto_a_garantizar) {
							$xLog->add("ERROR\tNo ha garantizado el Total del Credito, se debe garantizar $monto_a_garantizar y solamente se tiene en resguardo $monto_garantizado \r\n");
							$valido 			= false;
							$this->mValidacionERRS++;
						}
					}
					// verificar si tiene aportaciones sociales
					/*if ($this->mForceMinistracion == false) {
						$aportaciones = $xSocio->getAportacionesSociales ();
						$cuotas = $xSocio->getOTipoIngreso ()->getParteSocial () + $xSocio->getOTipoIngreso ()->getPartePermanente (); // $DIngreso["parte_social"] + $DIngreso["parte_permanente"];
						if ($aportaciones < $cuotas) {
							$xLog->add("ERROR\tNo ha Pagado sus Cuotas Sociales por $cuotas, ha pagado $aportaciones \r\n");
							$sucess = false;
						}
					}
					*/
					if($this->getNumeroDePlanDePagos()<= 0){
						$xLog->add("ERROR\tNo se ha Generado el PLAN DE PAGOS \r\n");
						$valido = false;
						$this->mValidacionERRS++;
					}
					break;
			}
		} else {
			$xLog->add ("ERROR\tError al Iniciar a la Persona\r\n" );
			$xLog->add ( $xSoc->getMessages (), $xLog->DEVELOPER );
			$this->mValidacionERRS++;
			$valido = false;
		}
		if($out == OUT_HTML){
			if($this->mValidacionERRS > 0){
				$xLog->add ("ERROR\tNumero de Errores : " . $this->mValidacionERRS . "\r\n" );
			}
			if($this->mValidacionWARNS > 0){
				$xLog->add ("WARN\tNumero de Advertencias : " . $this->mValidacionWARNS . "\r\n" );
			}
		} else {
			$xLog->add ("ERROR\tNumero de Errores : " . $this->mValidacionERRS . "\r\n" );
			$xLog->add ("WARN\tNumero de Advertencias : " . $this->mValidacionWARNS . "\r\n" );
		}

		
		$this->mMessages .= $xLog->getMessages();
		return ($out === false) ? $valido : $xLog->getMessages ( $out );
	} // END FUNCTION
	/**
	 * @deprecated 2014.04.01
	 */
	function getAvales_InText($style = "ficha") { return $this->getAvales_InHTML ( $style ); }
	/**
	 * @deprecated 2014.04.01
	 */
	function getAvales_InHTML($style = "ficha") {
		$Slistas = new cSQLListas ();
		
		$sql = $Slistas->getListadoDeAvales ( $this->getNumeroDeCredito () );
		$rs = getRecordset ( $sql );
		$tbl = "";
		$tds = "";
		$cnt = 0;
		while ( $rw = mysql_fetch_array ( $rs ) ) {
			$tds .= "
					<tr>
						<th id=\"id-relacion-" . $rw ["num"] . "\" colspan='4'>" . $rw ["relacion"] . "</th>
					</tr>
					<tr>
						<td>Socio Num.</td>
						<th>" . $rw ["numero_socio"] . "</th>
						<td>Consanguinidad</td>
						<th>" . $rw ["consanguinidad"] . "</th>
					</tr>
					<tr>
						<td>Nombre</td>
						<td colspan='3'>" . $rw ["nombre"] . "</td>
					</tr>
					<tr>
						<td>Telefono(s)</td>
						<td>" . $rw ["telefonos"] . "</td>
						<td>C.U.R.P.</td>
						<td>" . $rw ["curp"] . "</td>
					</tr>
					<tr>
						<td>Domicilio</td>
						<td colspan='3'>" . $rw ["domicilio"] . "</td>
					</tr>
					";
			$cnt ++;
		}
		$tbl = "
			<fieldset>
			<legend>|&nbsp;&nbsp;&nbsp;INFORMACI&Oacute;N DE LOS AVALES&nbsp;&nbsp;&nbsp;|</legend>
			<table >
				$tds
			</table>
			</fieldset>";
		return ($cnt == 0) ? "" : $tbl;
	}
	/**
	 * Muestra en Formato HTML una ficha de descripción del Socio
	 * @param boolean $marco        	
	 * @param string $extraTool        	
	 * @param boolean $domicilio_extendido        	
	 */
	function getFichaDeSocio($marco = true, $extraTool = "", $domicilio_extendido = false) {
		$Soc = new cSocio ( $this->mNumeroSocio, true );
		return $Soc->getFicha ( $domicilio_extendido, $marco, $extraTool );
	}
	function getFichaMini(){
		$xCache					= new cCache();
		$this->mIDCacheFicha	= EACP_CLAVE . ".ficha." . $this->mNumeroCredito . ".tiny";
		$ficha					= $xCache->get($this->mIDCacheFicha);
		if($ficha == null){ 
			$xFMT	= new cFormato(20011);
			$xFMT->setCredito($this->mNumeroCredito, $this->getDatosInArray());
			$xFMT->setProcesarVars();
			$ficha		=$xFMT->get();
			$xCache->set($this->mIDCacheFicha, $ficha);
		}
		return $ficha;
	}
	function getEsRechazado(){
		$val	= false;
		if($this->getEstadoActual() == CREDITO_ESTADO_CASTIGADO AND $this->getMontoAutorizado() <= 0){
			$val	= true;
		}
		return $val;
	}
	function getFicha($mark = true, $extraTool = "", $extendido = false, $ConPersona = false) {
		if ($this->mCreditoInicializado == false){ $this->init(); }
		$xL 				= new cLang();
		$xRuls				= new cReglaDeNegocio();
		$xD 				= new cFecha();
		$OProducto			= $this->getOProductoDeCredito();
		$OPeriocidad		= $this->getOPeriocidad();
		$OEstatus			= $this->getOEstado();
		$OTAutorizacion		= $this->getOTipoAutorizacion();
		
		$TasaFija			= $OProducto->getTasaFija();
		
		$lafila 			= $this->getDatosDeCredito ();
		$solicitud 			= $this->getNumeroDeCredito ();
		$convenio 			= $OProducto->getNombre();
		$fministracion 		= $this->getFechaDeMinistracion();
		$fvencimiento 		= $this->getFechaDeVencimiento();
		$periocidad_pago 	= $OPeriocidad->getNombre();// $lafila["descripcion_periocidadpagos"];
		//$autorizado 		= getFMoney ( $this->getMontoAutorizado() );
		$saldo 				= getFMoney ( $this->getSaldoActual() );
		$estatus 			= $OEstatus->getNombre();// $lafila ["descripcion_estatus"];
		$autorizado			= $OTAutorizacion->getNombre();
		$tool				= $extraTool;
		//Datos de Origen
		$origen				= $this->getODatosOrigen()->getDescripcion();
		$montoautorizado	= getFMoney( $this->getMontoAutorizado() );
		//
		$tasa 				= getFMoney ( ($this->getTasaDeInteres() * 100) );
		$mora 				= $this->getTasaDeMora() * 100;
		$pagos 				= $this->getPagosAutorizados();
		$ultimopago 		= $this->getPeriodoActual();
		
		$cls 				= "";
		$TasaIVA 			= $this->getTasaIVA ();
		$TasaMora 			= $this->getTasaDeMora ();
		$trInicial 			= "";
		$cls 				= "credito-estado-" . $this->getEstadoActual ();
		$montoParc 			= getFMoney ( $this->getMontoDeParcialidad() );
		$activo = true;
		if ($ConPersona == true) {
			$xSoc = $this->getOPersona ();
			$trInicial = "<tr><th>" . $xL->getT ( "TR.Persona" ) . "</th><td>" . $xSoc->getCodigo () . "</td><td colspan='2'>" . $xSoc->getNombreCompleto () . "</td></tr>";
		}
		if ($this->getTipoEnSistema () == SYS_PRODUCTO_NOMINA) {
			if ($this->getClaveDeEmpresa () != DEFAULT_EMPRESA) {
				$xEmp 		= new cEmpresas ( $this->getClaveDeEmpresa () );
				$xEmp->init();
				$convenio = "$convenio - " . $xEmp->getNombreCorto ();
			} else {
				$convenio = "$convenio - ND";
				//TODO: Activar Alerta
			}
		}
		$tdSaldo = "<th class='izq'>" . $xL->getT ( "TR.Saldo Principal" ) . "</th><td class='mny'>$saldo</td>";
		$tdMonto = "<th class='izq'>" . $xL->getT ( "TR.Monto Original" ) . "</th><td class='mny'>$montoautorizado</td>";
		$tdFecha = "<th class='izq'>" . $xL->getT ( "TR.fecha de desembolso" ) . "</th><td>" . $xD->getFechaCorta ( $fministracion ) . "</td>";
		$tdPagos = ($this->getPeriocidadDePago () != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) ? "<td class='mny'>$ultimopago de $pagos POR " . OPERACION_MONEDA_SIMBOLO . " $montoParc</td>" : "<td class='izq'>UNICO</td>";
		$tdVencimiento = "";
		// Si el Estatus es AUTORIZADO
		if ($this->getEstadoActual () == CREDITO_ESTADO_AUTORIZADO) {
			$tdSaldo 	= "";
			$tdFecha 	= "<th class='izq'>" . $xL->getT ( "TR.Fecha de Autorizacion" ) . "</th><td>" . $xD->getFechaCorta ( $this->getFechaDeAutorizacion() ) . "</td>";
			$activo 	= false;
			$tdPagos 	= "<td class='mny'>$pagos</td>";
		} else if ($this->getEstadoActual () == CREDITO_ESTADO_SOLICITADO) {
			$tdSaldo 	= "";
			$tdMonto 	= "<th class='izq'>" . $xL->getT ( "TR.Monto Solicitado" ) . "</th><td class='mny'>" . getFMoney ( $this->getMontoSolicitado() ) . "</td>";
			$tdFecha 	= "<th class='izq'>" . $xL->getT ( "TR.Fecha de Solicitud" ) . "</th><td>" . $xD->getFechaCorta ( $this->getFechaDeMinistracion() ) . "</td>";
			$activo 	= false;
			$tdPagos 	= "<td class='mny'>" . $this->getPagosSolicitados () . "</td>";
		} else if($this->getEsRechazado() == true){
			$activo 	= false;
			$tdSaldo 	= "";
			$tdPagos 	= "<td class='mny'>" . $this->getPagosSolicitados () . "</td>";
			$extendido	= false;
			$estatus	= $xL->getT ( "TR.RECHAZADO" );
			$cls		= "credito-estado-rechazado";
			//$tdFecha 	= "<th class='izq'>" . $xL->getT ( "TR.R" ) . "</th><td>" . $xD->getFechaCorta ( $this->getFechaDeAutorizacion() ) . "</td>";
			
			$xRaz		= new cCreditosRechazos();
			if($xRaz->initByCredito($this->getClaveDeCredito()) == true){
				$xRazTipo	= new cCreditosRechazosRazones($xRaz->getTipo());
				
				$tdFecha 	= "<th class='izq'>" . $xL->getT ( "TR.FECHA_de RECHAZO" ) . "</th><td>" . $xD->getFechaCorta ( $xRaz->getFecha() ) . "</td>";
				$xUsrR		= new cSystemUser($xRaz->getUsuario());
				if($xUsrR->init() == true){
					$tdFecha 	.= "<th class='izq'>" . $xL->getT ( "TR.USUARIO RECHAZO" ) . "</th><td>" . $xUsrR->getNombreCompleto() . "</td>";
				}
				if($xRazTipo->init() == true){
					$tdMonto = "<th class='izq'>" . $xL->getT ( "TR.MOTIVORECHAZO" ) . "</th><td colspan='3'>" . $xRazTipo->getNombre() . " / " . $xRaz->getRazones() . "</td>";
				}
			}
		}
		
		if($extendido == true){
			if($this->isAFinalDePlazo() == true){
				$fvencimiento			= $this->getFechaDeVencimiento();
			} else {
				if($this->getEsCreditoYaAfectado() == false){
					$fvencimiento		= $this->getFechaDeVencimiento();
				} else {
					$fvencimiento 		= $this->getFechaDeMora();
				}				
			}
			//$tdVencimiento 		= "<th class='izq'>" . $xL->getT ( "TR.FECHA_LIMITE_PAGO" ) . "</th><td>" . $xD->getDayName ( $fvencimiento ) . "; " . $xD->getFechaCorta ( $fvencimiento ) . "</td>";
			$tdVencimiento 		= "<th class='izq'>" . $xL->getT ( "TR.PLAZOMESES" ) . "</th><td>" . $this->getPlazoEnMeses() . "m</td>";
		}
		if ($extendido == true) {
			$Destino			= "";
			$xDest				= new cCreditosDestinos($this->getClaveDeDestino () );
			if($xDest->init() == true){ $Destino = $xDest->getNombre(); }

			$tdExigible 		= ($activo == false) ? "" : "<th class='izq'>" . $xL->getT ( "TR.Saldo exigible" ) . "</th><td class='mny'>" . getFMoney ( $this->getSaldoIntegrado ( false, true ) ) . "</td>";
			$oficial 			= $this->getOOficial()->getNombreCompleto ();
			$codigo_de_oficial 	= $this->getClaveDeOficialDeCredito();
			$trOficial			= "<tr><th class='izq'>" . $xL->getT( "TR.ASIGNADO" ). "</td><td colspan='3'>$oficial</td></tr>";
			if($this->isPagable() == true){
				$xOfiSeg		= new cOficial($this->getOficialDeSeguimiento()); $xOfiSeg->init();
				$trOficial		= "<tr><th class='izq'>" . $xL->getT( "TR.OFICIAL_DE_CREDITO" ). "</td><td>$oficial</td>
										<th class='izq'>" . $xL->getT( "TR.SEGUIMIENTO_DE_CARTERA" ). "</td><td>" . $xOfiSeg->getNombreCompleto() . "</td>
										</tr>";
			}
			$tool = "<tr>
						<th class='izq'>" . $xL->getT( "TR.Tasa Anualizada de Moratorio" ) . "</th><td class='mny'>" . getFMoney ( ($TasaMora * 100) ) . "%</td>
						$tdExigible</tr>
					<tr><th class='izq'>" . $xL->getT( "TR.Destino" ) . " / " . $xL->getT( "TR.Origen" ) . "</th><td colspan='3'>" . $Destino . ": " . $this->mDescripcionDestino . " / $origen</td></tr>
					$trOficial" . $tool;
			if ($activo == true) {
				if ($this->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
					$letra 	= $this->getPeriodoActual() + 1;
					$IntAct = $this->getInteresDevengado( false, $letra, false, true );
				} else {
					$IntAct = $this->getInteresDevengado();
				}
				
				$IntDevNorm 	= $this->getInteresNormalDevengado ();
				$IntDevMor 		= $this->getInteresMoratorioDev ();
				$IntPerNor 		= $IntAct [SYS_INTERES_NORMAL];
				$IntPerMor 		= $IntAct [SYS_INTERES_MORATORIO];
				$IntPagMor 		= $this->getInteresMoratorioPagado ();
				$IntPagNor 		= $this->getInteresNormalPagado ();
				
				$TIntNorm 		= setNoMenorQueCero( ($IntDevNorm + $IntPerNor - $IntPagNor),2);
				$TIntMor 		= setNoMenorQueCero( ($IntDevMor + $IntPerMor - $IntPagMor),2);
				$BaseIVA 		= ($TIntNorm > 0) ? $TIntNorm : 0;
				$BaseIVA 		+= ($TIntMor > 0) ? $TIntMor : 0;
				$IntIVA 		= setNoMenorQueCero ( ($TIntNorm + $TIntMor) * $TasaIVA );
				$cargos 		= $this->getCargosDeCobranza ();
				$cargosIVA 		= setNoMenorQueCero ( $cargos * $this->getTasaIVAOtros() );
				$penas			= $this->getPenasPorCobrar();
				$penasIVA		= setNoMenorQueCero ( $penas * $this->getTasaIVAOtros() );
				$trCargos 		= "";
				$trPenas 		= "";
				
				if ($cargos > 0) {
					$trCargos = "<tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.Cargos por Cobranza" ) . "</th><td class='mny'>" . getFMoney ( $cargos ) . "</td>
					</tr><tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.IVA de Otros cargos" ) . "</th><td class='mny'>" . getFMoney ( $cargosIVA ) . "</td>
					</tr>	";
				}
				if($penas > 0){
					$trPenas = "<tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.PENAS_POR_ATRASOS" ) . "</th><td class='mny'>" . getFMoney ( $penas ) . "</td>
					</tr><tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.IVA de penas" ) . "</th><td class='mny'>" . getFMoney ( $penasIVA ) . "</td>
					</tr>	";					
				}
				$tool .= "<tr>
						<th>" . $xL->getT ( "TR.Interes Normal Generado" ) . "</th><td class='mny'>" . getFMoney ( $IntDevNorm ) . "</td>
						
						<th>" . $xL->getT ( "TR.Interes Moratorio Generado" ) . "</th><td class='mny'>" . getFMoney ( $IntDevMor ) . "</td>
					</tr>
					<tr>
						<th>" . $xL->getT ( "TR.Interes Normal del Mes / Periodo" ) . "</th><td class='mny'>" . getFMoney ( $IntPerNor ) . "</td>
						<th>" . $xL->getT ( "TR.Interes Moratorio del mes" ) . "</th><td class='mny'>" . getFMoney ( $IntPerMor ) . "</td>
					</tr>
					<tr>
						<th>" . $xL->getT ( "TR.Interes Normal Pagado" ) . "</th><td class='mny'>(" . getFMoney ( $IntPagNor ) . ")</td>
						<th>" . $xL->getT ( "TR.Interes Moratorio Pagado" ) . "</th><td class='mny'>(" . getFMoney ( $IntPagMor ) . ")</td>
					</tr>
					<tr>
						<th>" . $xL->getT ( "TR.TOTAL INTERES NORMAL" ) . "</th><th class='mny'>" . getFMoney ( $TIntNorm ) . "</th>
						<th>" . $xL->getT ( "TR.TOTAL INTERES MORATORIO" ) . "</th><th class='mny'>" . getFMoney ( $TIntMor ) . "</th>
					</tr>

					<tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.SALDO DE CAPITAL" ) . "</th><td class='total, mny'>" . getFMoney ( $this->getSaldoActual () ) . "</td>
					</tr>
					<tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.TOTAL INTERESES" ) . "</th><td class='mny'>" . getFMoney ( $TIntNorm + $TIntMor ) . "</td>
					</tr>
					<tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.IVA POR INTERESES" ) . "</th><td class='mny'>" . getFMoney ( $IntIVA ) . "</td>
					</tr>					
								$trCargos
								$trPenas
					<tr>
						<td /><td />
						<th>" . $xL->getT ( "TR.TOTAL POR PAGAR" ) . "</th><td class='total, mny'>" . getFMoney ( $TIntNorm + $TIntMor + $IntIVA + $this->getSaldoActual () + ($cargos + $cargosIVA) + ($penas +  $penasIVA) ) . "</td>
					</tr>";
			}
			if(MODO_MIGRACION == true){
				
			}
			$tool = "<tfoot>$tool</tfoot>";
		}
		$thCAT 		= (EACP_CLAVE_DE_PAIS == "MX") ? "<th class='izq'>CAT</th><td class='mny'>" . $this->getCAT () . "</td>" : "";
		$trTasas 	= (OPERACION_IGNORAR_IVA == false) ? "<tr><th class='izq'>" . $xL->getT ( "TR.Tasa de IVA" ) . "</th><td class='mny'>" . getFMoney ( $TasaIVA * 100 ) . "%</td>$thCAT</tr>" : "";
		$ttasa		= ($TasaFija>0) ? "<th class='izq'>" . $xL->getT ( "TR.TASA_FIJA" ) . "</th><td class='mny'>" . getFMoney ( $TasaFija ) . "%</td>" : "<th class='izq'>" . $xL->getT ( "TR.Tasa Anualizada de interes" ) . "</th><td class='mny'>" . getFMoney ( $tasa ) . "%</td>";
		$exoFicha = "
		<table id='fichadecredito'>
			<tbody>
				$trInicial
				<tr>
					<th class='izq'>" . $xL->getT ( "TR.Numero de Credito" ) . "</th><td>$solicitud</td>
					<th class='izq'>" . $xL->getT ( "TR.Producto" ) . "</th><td>$convenio</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT ( "TR.Periocidad de Pago" ) . "</th><td>$periocidad_pago</td>
					<th class='izq'>" . $xL->getT ( "TR.Numero de Pagos" ) . "</th>$tdPagos
				</tr>
				<tr>
					
					$ttasa
					<th class='izq'>" . $xL->getT ( "TR.Estado_actual" ) ." / ". $xL->getT ( "TR.AUTORIZADO" ) . "</th><td  class='$cls'>$estatus / $autorizado</td>
				</tr>
				$trTasas
				<tr>$tdFecha $tdVencimiento</tr>
				<tr>$tdMonto $tdSaldo</tr>
			</tbody>
			$tool
		</table>";
		if ($mark == true) {
			$exoFicha = "<fieldset><legend>|&nbsp;&nbsp;" . $xL->getT ( "TR.Informacion de Credito" ) . "&nbsp;&nbsp;|</legend>$exoFicha</fieldset>";
		}
		//$OProducto		= null;
		//$OEstatus		= null;
		
		return $exoFicha;
	}
	function getGarantiaLiquidaPagada($ByGrupo = false) {
		$monto 	= 0;
		$xGtia	= new cCreditosGarantiasLiquidas();
		$xGtia->setClaveDeCredito($this->getClaveDeCredito());
		$monto	= $xGtia->getSaldoGantiaLiq();
		$this->mMessages .= $xGtia->getMessages();
		return $monto;
	}
	function getCuentaGarantiaLiquida() {
		return $this->mCuentaDeGarantiaLiquida;
	}
	function getGarantiaLiquidaPorPagar(){
		$PorPagar	= $this->getGarantiaLiquida();
		if($PorPagar > 0){
			$PorPagar	= $PorPagar - $this->getGarantiaLiquidaPagada();
			$PorPagar	= setNoMenorQueCero($PorPagar);
		}
		return $PorPagar;
	}
	/**
	 * Garantia Liquida por el Total del credito
	 * @param boolean $ForcePagados
	 * @return number
	 */
	function getGarantiaLiquida($ForcePagados = false) {
		if($this->mCreditoInicializado == false) { $this->initCredito(); }
		
		$monto	= 0;
		if($this->getEstadoActual() == CREDITO_ESTADO_SOLICITADO){
			
			return 0;
		} else {
			$base	= 0;
			
			if($this->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO){
				$base	= $this->getMontoAutorizado();
			} else {
				if($this->getEsPagado() == true){
					return 0;
				} else if($this->getEsAfectable() == true){
					$base	= $this->getSaldoActual();
				}
			}
			if($base > 0){
				$tasa	= $this->getOProductoDeCredito()->getTasaDeGarantiaLiquida();
				$monto	= $base * $tasa;
			}
			
		}
		return $monto;
	}
	/**
	 * Mensajes de la Libreria
	 * @param string $put Formato de Salida
	 * @return string de Texto
	 */
	function getMessages($put = OUT_TXT) {
		$xH = new cHObject ();
		return $xH->Out ( $this->mMessages, $put );
	}
	function getSaldoActual($fecha = false) {
		$xF 	= new cFecha ();
		$fecha 	= $xF->getFechaISO($fecha);
		if ($this->mCreditoInicializado == false){$this->init();}
		$saldo 	= $this->mSdoCapitalInsoluto;
		if ($xF->getEsActual ( $fecha ) == true) {
			// No se inician pagos
			// $this->mMessages .= "WARN\tNo se Inicializan Pagos por la fecha $fecha \r\n";
		} else {
			$saldo = $this->getSaldoAFecha ( $fecha );
		}
		$this->mMessages .= "WARN\tSaldo de Credito $saldo al $fecha\r\n";
		return $saldo;
	}
	function getSaldoVencido(){ return $this->mSaldoVencido; }
	function getSaldoAuditado(){ return $this->mSaldoAuditado; }
	function getFechaDeMora(){ return $this->mFechaMora; }
	function getFechaVenceContrato(){ return $this->mFechaVenceContrato; }
	function getTotalPlanPendiente(){ return  $this->mTotalPlanPend;}
	function getTotalPlanExigible(){ return $this->mTotalPlanExig; }
	function getContratoCorriente() {
		if ($this->mCreditoInicializado == false){ $this->init ();	}
		if ($this->mContratoCorriente == 0) {
			$this->mContratoCorriente = $this->getOPersona()->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_ORDINARIO);
			if($this->mContratoCorriente == 0){
				$xCta	= new cCuentaALaVista(0);
				$this->mContratoCorriente = $xCta->setNuevaCuenta(CAPTACION_ORIGEN_CONDICIONADO, CAPTACION_PRODUCTO_ORDINARIO, $this->getClaveDePersona(), "", $this->getNumeroDeCredito(), "", "", $this->getClaveDeGrupo(), $this->getFechaDeMinistracion());
			}
			$arr	= array($this->obj()->contrato_corriente_relacionado()->get() => $this->mContratoCorriente);
			$this->setUpdate($arr, true);
			//$this->mContratoCorriente = CTA_GLOBAL_CORRIENTE;
			//Actualizar
		}
		return $this->mContratoCorriente;
	}
	function getClaveDeCredito(){return $this->mNumeroCredito; }
	function getvalorResidual(){ return $this->mValorResidual; }
	/**
	 * Funcion que retorna una descripcion Corta del Credito
	 * 
	 * @return string Corta del credito
	 */
	function getShortDescription() {
		if ($this->mCreditoInicializado == false) {
			$this->init ();
		}
		$txt = "";
		$txt = $this->mNumeroSocio . "-" . $this->mDescripcionProducto . "-";
		$txt .= $this->mDescripcionPeriocidad . "-" . $this->mPagosAutorizados;
		if ($this->mEstatusActual != CREDITO_ESTADO_VIGENTE) {
			$txt .= "-" . $this->mDescripcionEstatus;
		} else {
			$txt .= "";
		}
		
		if ($this->mEmpresa != FALLBACK_CLAVE_EMPRESA) {
			$txt .= "-" . $this->mNombrePersonaAsoc;
		}
		return $txt;
	}
	function getDescripcion(){ return $this->getShortDescription (); }
	function getCAT() {
		$xMat 					= new cMath ();
		$pagos 					= $this->getPagosAutorizados ();
		$parc 					= $this->mMontoFijoParcialidad;
		$arrPagos 				= array();
		$capital 				= $this->getMontoAutorizado ();
		$calcular 				= ($this->getEstadoActual () == CREDITO_ESTADO_SOLICITADO or $this->getEstadoActual () == CREDITO_ESTADO_CASTIGADO) ? false : true;
		$guardar				= true;
		$cat 					= $this->mTasaCat;
		//Si el cat está iniciado, no calcular
		if($cat > 0){ $calcular = false; }
		//Si el Credito no tiene saldo, omitir calculo
		if ($capital <= TOLERANCIA_SALDOS) { $calcular = false;	}
		
		if ($calcular == true) {
			$xProd				= $this->getOProductoDeCredito();
			//Si ya existe como dijo en el producto el cat no se recalcula
			if($xProd->getCATFijo() >0){
				$cat				= $xProd->getCATFijo();
			} else {
				//Agrega comision por apertura
				$comision_apertura	= ($xProd->getTasaComisionApertura() >= 1) ? $xProd->getTasaComisionApertura() : ($capital * $xProd->getTasaComisionApertura());
				$comision_apertura	= ($comision_apertura * ($this->getTasaIVAOtros()+ 1));
				//Se debe agregar todos los descuentos
				$capital			= $capital - $comision_apertura;
				//Si el credito es Final de Plazo Emular la letra sin IVA
				if ($this->getPeriocidadDePago () == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
					$pago 			= ($this->getMontoAutorizado() * $this->getTasaDeInteres () * $this->getDiasAutorizados()) / EACP_DIAS_INTERES;
					$arrPagos[] 	= $pago + $this->getMontoAutorizado ();
					$periodos 		= (360/$this->getDiasAutorizados());
					//un pago una como periodo de pagos
					$cat 			= $xMat->cat ( $capital, $arrPagos, $periodos,1 );
				} else {
					//Si son pagos periodicos, Simula un plan de pagos
					$xGen			= new cPlanDePagosGenerador($this->getPeriocidadDePago());
					$xGen->setPagosAutorizados($this->getPagosAutorizados());
					$xGen->setMontoActual($this->getMontoAutorizado());
					$xGen->setMontoAutorizado($this->getMontoAutorizado());
					$xGen->setPeriocidadDePago($this->getPeriocidadDePago());
					$xGen->setTasaDeInteres($this->getTasaDeInteres());
					$xGen->setTasaDeIVA($this->getTasaIVA());
					$xGen->setFechaDesembolso($this->getFechaDeMinistracion());
					$xGen->setSoloTest(true);
					$xGen->setTipoDePago($this->getTipoDePago());
					//Obtener la parcialidad presumida de pago
					$parcial 	= $xGen->getParcialidadPresumida();
					//Compilar el plan con las fechas
					$xGen->setCompilar(false);
					//Aplicar pagos y operaciones
					$xGen->getVersionFinal();
					//Obtiene la tasa cat del plan calculado
					$cat		= $xGen->getTasaCAT();
				}
			}
			if($cat > 0){ $this->setUpdate(array("tasa_cat" => $cat), true); }
		}
		return $cat;
	}
	function setChangeNumeroDeSolicitud($NuevoNumero, $EsReconvenio = false) {
		$socio 		= $this->mNumeroSocio;
		$credito 	= $this->mNumeroCredito;
		$solicitud 	= $credito;
		$NCredito 	= $NuevoNumero;
		$msg 		= "=========\tCAMBIO DE UN CREDITO\tDE: $credito\ţA:$NuevoNumero\r\n";
		$sql 		= array ();
		$xQL		= new MQL();
		if ($EsReconvenio == false) {
			$sql [] = "UPDATE creditos_solicitud 		SET numero_solicitud = $NCredito WHERE numero_socio = $socio AND numero_solicitud = $credito";
			$sql [] = "UPDATE captacion_cuentas 		SET numero_solicitud=$NCredito	WHERE numero_socio=$socio AND numero_solicitud=$credito";
			$sql [] = "UPDATE creditos_garantias 		SET solicitud_garantia=$NCredito WHERE socio_garantia=$socio AND solicitud_garantia=$credito";
			$sql [] = "UPDATE socios_relaciones 		SET credito_relacionado=$credito WHERE socio_relacionado=$socio AND credito_relacionado=$credito";
			$sql [] = "UPDATE creditos_flujoefvo 		SET solicitud_flujo = $NCredito WHERE socio_flujo=$socio AND solicitud_flujo = $credito";
			$sql [] = "UPDATE creditos_parametros_negociados 	SET numero_de_credito=$NCredito 	WHERE numero_de_credito=$credito";
			$sql [] = "UPDATE creditos_reconvenio 				SET numero_solicitud=$NCredito 		WHERE numero_solicitud=$credito AND codigo=$socio";
			$sql [] = "UPDATE `seguimiento_compromisos` 		SET credito_comprometido=$NCredito 	WHERE `credito_comprometido`=$credito ";
			$sql [] = "UPDATE `seguimiento_llamadas` 			SET numero_solicitud=$NCredito 		WHERE `numero_solicitud`=$credito";
			$sql [] = "UPDATE `seguimiento_notificaciones` 			SET numero_solicitud=$NCredito 		WHERE `numero_solicitud`=$credito";
			$sql [] = "UPDATE `socios_memo` 					SET numero_solicitud = $NCredito 	WHERE `numero_solicitud`=$credito";
			$sql [] = "UPDATE `usuarios_web_notas` 				SET `documento`=$NCredito 			WHERE `documento`=$credito";
			$sql [] = "UPDATE `creditos_otros_datos` 				SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
			$sql [] = "UPDATE `creditos_plan_de_pagos` 				SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
		}
		$sql[] = "UPDATE operaciones_recibos 			SET docto_afectado=$NCredito 			WHERE docto_afectado=$credito AND numero_socio=$socio";
		$sql[] = "UPDATE operaciones_mvtos 				SET docto_afectado=$NCredito 			WHERE docto_afectado=$credito AND socio_afectado=$socio";
		$sql[] = "UPDATE contable_polizas_proforma 		SET documento=$NCredito 				WHERE documento=$credito ";
		$sql[] = "UPDATE creditos_rechazados 			SET numero_de_credito=$NCredito 		WHERE numero_de_credito=$credito ";
		$sql[] = "UPDATE `tesoreria_cajas_movimientos` 	SET `documento`=$NCredito 				WHERE `documento`=$credito";
		
		$sql[] = "UPDATE `tesoreria_cajas_movimientos` 	SET `documento`=" . $NCredito . " WHERE `documento`=$solicitud";
		$sql[] = "UPDATE `tesoreria_cajas_movimientos` 	SET `documento_descontado`=" . $NCredito . " WHERE `documento_descontado`=$solicitud";
		$sql[] = "UPDATE `tesoreria_caja_arqueos` 		SET `documento`=" . $NCredito . " WHERE `documento`=$solicitud";
		$sql[] = "UPDATE `usuarios_web_notas` 			SET `documento`=" . $NCredito . " WHERE `documento`=$solicitud";
		$sql[] = "UPDATE contable_polizas_proforma 		SET documento=" . $NCredito . " WHERE documento=$solicitud";
		$sql[] = "UPDATE aml_alerts 					SET documento_relacionado=" . $NCredito . " WHERE documento_relacionado = $solicitud";
		$sql[] = "UPDATE aml_risk_register				SET documento_relacionado=" . $NCredito . " WHERE documento_relacionado = $solicitud";
		$sql[] = "UPDATE bancos_operaciones				SET numero_de_documento=" . $NCredito . " WHERE numero_de_documento = $solicitud";
		$sql[] = "UPDATE `socios_memo` 					SET `numero_solicitud`=" . $NCredito . " WHERE `numero_solicitud`=$solicitud";
		$sql[] = "UPDATE `empresas_cobranza` 			SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
		$sql[] = "UPDATE operaciones_mvtos 				SET docto_neutralizador= " . $NCredito . " WHERE docto_neutralizador=$solicitud";
		$sql[] = "UPDATE captacion_cuentas 				SET numero_solicitud= " . $NCredito . " WHERE numero_solicitud=$solicitud";
		$sql[] = "UPDATE creditos_reconvenio 			SET credito_origen= " . $NCredito . " WHERE credito_origen=$solicitud";
		$sql[] = "UPDATE `creditos_preclientes` SET `idcredito`=$NCredito WHERE `idcredito`=$solicitud";
		//2017-04-11
		$sql[] = "UPDATE `creditos_otros_datos` 		SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
		$sql[] = "UPDATE `creditos_plan_de_pagos` 		SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
		$sql[] = "UPDATE `creditos_destino_detallado` 	SET `credito_vinculado`=" . $NCredito . " WHERE `credito_vinculado`=$solicitud";
		$sql[] = "UPDATE `entidad_calificacion` 		SET `clave_de_documento`=" . $NCredito . " WHERE `clave_de_documento`=$solicitud";
		$sql[] = "UPDATE `creditos_montos` 				SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
		$sql[] = "UPDATE `creditos_datos_originacion` 	SET `credito`=" . $NCredito . " WHERE `credito`=$solicitud";
		$sql[] = "UPDATE `creditos_preclientes` 		SET `idcredito`=" . $NCredito . " WHERE `idcredito`=$solicitud";
		$sql[] = "UPDATE `originacion_leasing` 			SET `credito`=" . $NCredito . " WHERE `credito`=$solicitud";
		//$sql[] = "UPDATE `` 	SET ``=" . $NCredito . " WHERE ``=$solicitud";
		
		
		foreach ( $sql as $key => $query ) {
			$x = $xQL->setRawQuery( $query );
			
		}
		return $msg;
	}

	/**
	 * Funcion que Elimina un Credito de la Base de Datos
	 */
	function setDelete() {
		// XXX: Verficar $socio, si es mayor a cero 2014/11/02
		$msg 			= "=========\tELIMINANDO UN CREDITO\r\n";
		$msg 			.= "=========\tFecha:\t\t" . date ( "Y-m-d H:i:s" ) . "\r\n";
		$sql 			= array ();
		// Elimina un Credito
		$solicitud		= $this->mNumeroCredito;
		$socio 			= $this->mNumeroSocio;
		$xLog			= new cCoreLog();
		$xQL			= new MQL();
		
		$sql[] = "DELETE FROM creditos_solicitud 			WHERE numero_solicitud=$solicitud AND numero_socio=$socio";
		$sql[] = "DELETE FROM creditos_flujoefvo 			WHERE solicitud_flujo=$solicitud AND socio_flujo=$socio";
		$sql[] = "DELETE FROM creditos_garantias 			WHERE solicitud_garantia=$solicitud AND socio_garantia=$socio";
		$sql[] = "DELETE FROM socios_relaciones 			WHERE credito_relacionado=$solicitud AND socio_relacionado=$socio";
		$sql[] = "DELETE FROM operaciones_recibos 			WHERE docto_afectado=$solicitud AND numero_socio=$socio";
		$sql[] = "DELETE FROM operaciones_mvtos 			WHERE docto_afectado=$solicitud AND socio_afectado=$socio";
		
		$sql[] = "DELETE FROM creditos_parametros_negociados WHERE numero_de_credito=$solicitud";
		$sql[] = "DELETE FROM creditos_rechazados 			WHERE numero_de_credito=$solicitud";
		$sql[] = "DELETE FROM `seguimiento_compromisos` 	WHERE `credito_comprometido`=$solicitud";
		$sql[] = "DELETE FROM `seguimiento_llamadas` 		WHERE `numero_solicitud`=$solicitud";
		$sql[] = "DELETE FROM `seguimiento_notificaciones`	WHERE `numero_solicitud`=$solicitud";
		$sql[] = "DELETE FROM `creditos_otros_datos` 		WHERE `clave_de_credito`=$solicitud";
		$sql[] = "DELETE FROM `creditos_reconvenio` 		WHERE `numero_solicitud`=$solicitud";
		
		$sql[] = "DELETE FROM `creditos_plan_de_pagos` 		WHERE `clave_de_credito`=$solicitud";
		$sql[] = "DELETE FROM `creditos_sdpm_historico` 	WHERE `numero_de_credito`=$solicitud";
		
		$sql[] = "DELETE FROM `creditos_otros_datos` 		WHERE `clave_de_credito`=$solicitud";
		$sql[] = "DELETE FROM `creditos_plan_de_pagos` 		WHERE `clave_de_credito`=$solicitud";
		$sql[] = "DELETE FROM `creditos_destino_detallado` 	WHERE `credito_vinculado`=$solicitud";
		$sql[] = "DELETE FROM `entidad_calificacion` 		WHERE `clave_de_documento`=$solicitud";
		$sql[] = "DELETE FROM `creditos_montos` 			WHERE `clave_de_credito`=$solicitud";
		$sql[] = "DELETE FROM `originacion_leasing` 		WHERE `credito`=$solicitud";
		$sql[] = "DELETE FROM `creditos_datos_originacion` 	WHERE `credito`=$solicitud";
		//$sql[] = "DELETE FROM `` 	WHERE ``=$solicitud";
		
		//$sql[] = "DELETE FROM `` 	WHERE ``=$solicitud";
		
		
		$sql[] = "UPDATE `tesoreria_cajas_movimientos` 	SET `documento`=" . DEFAULT_CREDITO . " WHERE `documento`=$solicitud";
		$sql[] = "UPDATE `tesoreria_cajas_movimientos` 	SET `documento_descontado`=" . DEFAULT_CREDITO . " WHERE `documento_descontado`=$solicitud";
		$sql[] = "UPDATE `tesoreria_caja_arqueos` 			SET `documento`=" . DEFAULT_CREDITO . " WHERE `documento`=$solicitud";
		$sql[] = "UPDATE `usuarios_web_notas` 				SET `documento`=" . DEFAULT_CREDITO . " WHERE `documento`=$solicitud";
		$sql[] = "UPDATE contable_polizas_proforma 		SET documento=" . DEFAULT_CREDITO . " WHERE documento=$solicitud";
		$sql[] = "UPDATE aml_alerts 						SET documento_relacionado=" . DEFAULT_CREDITO . " WHERE documento_relacionado = $solicitud";
		$sql[] = "UPDATE aml_risk_register					SET documento_relacionado=" . DEFAULT_CREDITO . " WHERE documento_relacionado = $solicitud";
		$sql[] = "UPDATE bancos_operaciones				SET numero_de_documento=" . DEFAULT_CREDITO . " WHERE numero_de_documento = $solicitud";
		$sql[] = "UPDATE `socios_memo` 					SET `numero_solicitud`=" . DEFAULT_CREDITO . " WHERE `numero_solicitud`=$solicitud";
		$sql[] = "UPDATE `empresas_cobranza` 				SET `clave_de_credito`=" . DEFAULT_CREDITO . " WHERE `clave_de_credito`=$solicitud";
		$sql[] = "UPDATE operaciones_mvtos 				SET docto_neutralizador= " . DEFAULT_CREDITO . " WHERE docto_neutralizador=$solicitud";
		$sql[] = "UPDATE captacion_cuentas 				SET numero_solicitud= " . DEFAULT_CREDITO . " WHERE numero_solicitud=$solicitud";
		$sql[] = "UPDATE creditos_reconvenio 				SET credito_origen= " . DEFAULT_CREDITO . " WHERE credito_origen=$solicitud";
		$sql[] = "UPDATE `creditos_preclientes` SET `idcredito`=0 WHERE `idcredito`=$solicitud";
		//TODO: terminar eliminar creditos
		foreach ( $sql as $key => $send ) {
			$x = $xQL->setRawQuery( $send );
			
		}
		
		$xLog->add("WARN\tSe elimina el Credito $solicitud de la Persona $socio");
		$xLog->add($msg, $xLog->DEVELOPER);
		$this->mMessages	.= $xLog->getMessages();
		$xLog->guardar($xLog->OCat()->CREDITO_ELIMINADO, $socio, $solicitud);
		$this->setCuandoSeActualiza(MQL_DEL);
		return $xLog->getMessages();
	}
	function getEvolucionDeSaldos(){
	}
	function setAbonoInteres($monto, $parcialidad = 0, $cheque = DEFAULT_CHEQUE, $tipo_de_pago = FALLBACK_TIPO_PAGO_CAJA, $recibo_fiscal = DEFAULT_RECIBO_FISCAL,$observaciones = "", $grupo = false, $fecha = false, $recibo = false, $esMora= false) {
		if($this->mCreditoInicializado == false){$this->init();}
		$xF							= new cFecha();
		$xLog						= new cCoreLog();
		$fecha						= ($fecha === false) ? $this->mFechaOperacion : $fecha;
		$fecha						= $xF->getFechaISO($fecha);
		
		$parcialidad 				= setNoMenorQueCero ($parcialidad);
		$parcialidad				= ($parcialidad<=0) ? $this->getNumeroDeParcialidad() : $parcialidad;
		$parcialidad				= ($parcialidad<=0) ? SYS_UNO : $parcialidad;
		
		$grupo 						= setNoMenorQueCero ($grupo);
		$grupo						= ($grupo<=0) ? $this->mGrupoAsociado : $grupo;
		$grupo						= ($grupo<=0) ? DEFAULT_GRUPO : $grupo;
		
		$recibo						= setNoMenorQueCero($recibo);
		$recibo						= ($recibo <= 0) ? $this->mReciboDeOperacion : $recibo;
		$this->mReciboDeOperacion	= setNoMenorQueCero($recibo);
		$socio 						= $this->mNumeroSocio;
		$cheque						= setNoMenorQueCero($cheque);
		
		
		$interesPagado				= ($esMora == false) ? $this->mInteresNormalPagado : $this->mInteresMoratorioPag;
		$tipoOperacion				= ($esMora == false) ? OPERACION_CLAVE_PAGO_INTERES : OPERACION_CLAVE_PAGO_MORA;
		$tipoOpIVA					= ($esMora == false) ? OPERACION_CLAVE_PAGO_IVA_INTS : OPERACION_CLAVE_PAGO_IVA_OTROS;
		
		if ($monto != 0) {
			$xLog->add("WARN\tRECIBO HEREDADO :  $recibo\r\n", $xLog->DEVELOPER);
			$CRecibo 				= new cReciboDeOperacion ( RECIBOS_TIPO_PAGO_CREDITO, true, $recibo );
			if($CRecibo->init() == true){
				$xLog->add("OK\Se inicializa el Recibo: $recibo\r\n", $xLog->DEVELOPER);
			} else {
				$recibo 			= $CRecibo->setNuevoRecibo ( $socio, $this->mNumeroCredito, $fecha, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO, $observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
				$CRecibo 			= new cReciboDeOperacion ( RECIBOS_TIPO_PAGO_CREDITO, true, $recibo );
			}
			
			// Set a Mvto Contable
			$CRecibo->setGenerarPoliza();
			$CRecibo->setGenerarTesoreria();
			$CRecibo->setGenerarBancos();
			
			if($CRecibo->init() == true){
				$recibo						= $CRecibo->getCodigoDeRecibo();
				$this->mReciboDeOperacion	= $recibo;
				// Operacion: Agregar el Movimiento
				$CRecibo->setNuevoMvto ( $fecha, $monto, $tipoOperacion, $parcialidad, $observaciones, 1, TM_ABONO, $socio, $this->mNumeroCredito );
				$interesPagado 				= $interesPagado - ($monto);
				$this->mSucess 		= true;
				// Actualizar la Cuenta
				$CRecibo->setFinalizarRecibo(true);
			} else {
				$this->mSucess		= false;
				$recibo				= 0;
				$xLog->add("ERROR\tNo Existe Recibo con el cual trabajar ($recibo) \r\n", $xLog->DEVELOPER);
			}
			// Credito: afectar el Interes
			if ($this->mSucess == true) {
				$OMon				= $this->getOMontos();
				$moraPagado			= $this->getInteresMoratorioPagado();
				$normalPagado		= $this->getInteresNormalPagado();
				
				if($esMora == false){
					$this->setInteresNormalPagado($interesPagado);
					$OMon->setInteresesPagados($interesPagado, $moraPagado);
					
					if($this->isAFinalDePlazo() == true){
						
					} else {
						$LPlan 					= $this->getOPlanDePagos();
						$xLetra					= $LPlan->getOLetra($parcialidad);
						$LPlan->setPagosAutorizados($this->getPagosAutorizados());
						
						$parcialidad_interes	= 0;
						
						if($xLetra != null){
							$parcialidad_fecha_pago	= $xLetra->getFechaDePago();
							$parcialidad_interes	= $xLetra->getInteres();
							$parcialidad_capital	= $xLetra->getCapital();
						}
						
						$amortizable	= setNoMenorQueCero(($monto - $parcialidad_interes));
						$NLetra			= $parcialidad+1;
						
						if($amortizable > 0.01){
							if($this->getPagosSinCapital() == true){
								$nueva_parcialidad = $LPlan->setAmortizarLetras($amortizable, $NLetra, OPERACION_CLAVE_PLAN_INTERES);
							} else {
								$LPlan->setAmortizarLetras($amortizable, $NLetra, OPERACION_CLAVE_PLAN_INTERES);
							}
						}
					}
				} else {
					$this->setInteresMoratorioPagado($interesPagado);
					$OMon->setInteresesPagados($normalPagado, $interesPagado);
				}

			}
		
			$this->mFechaOperacion	= $fecha;
			$this->mObjRec 			= $CRecibo;
			$xLog->add($CRecibo->getMessages(), $xLog->DEVELOPER);
			$this->mMessages 		.= $xLog->getMessages();
		}
		return $recibo;
	}
	function setAbonoCapital($monto, $parcialidad = 0, $cheque = DEFAULT_CHEQUE, $tipo_de_pago = FALLBACK_TIPO_PAGO_CAJA, $recibo_fiscal = DEFAULT_RECIBO_FISCAL,$observaciones = "", $grupo = false, $fecha = false, $recibo = false) {
		if($this->mCreditoInicializado == false){$this->init();}
		$xF							= new cFecha();
		$xLog						= new cCoreLog();
		$fecha						= ($fecha === false) ? $this->mFechaOperacion : $fecha;
		$fecha						= $xF->getFechaISO($fecha);
		
		$parcialidad 				= setNoMenorQueCero ($parcialidad);
		$parcialidad				= ($parcialidad<=0) ? $this->getNumeroDeParcialidad() : $parcialidad;
		$parcialidad				= ($parcialidad<=0) ? SYS_UNO : $parcialidad;
		
		$grupo 						= setNoMenorQueCero ($grupo);
		$grupo						= ($grupo<=0) ? $this->mGrupoAsociado : $grupo;
		$grupo						= ($grupo<=0) ? DEFAULT_GRUPO : $grupo;
		
		$recibo						= setNoMenorQueCero($recibo);
		$recibo						= ($recibo <= 0) ? $this->mReciboDeOperacion : $recibo;
		$this->mReciboDeOperacion	= setNoMenorQueCero($recibo);
		$socio 						= $this->mNumeroSocio;
		$cheque						= setNoMenorQueCero($cheque);
		
		
		if ($monto != 0) {
			$xLog->add("WARN\tRECIBO HEREDADO :  $recibo\r\n", $xLog->DEVELOPER);
			$CRecibo 				= new cReciboDeOperacion ( RECIBOS_TIPO_PAGO_CREDITO, true, $recibo );
			if($CRecibo->init() == true){
				
			} else {
				$recibo 			= $CRecibo->setNuevoRecibo ( $socio, $this->mNumeroCredito, $fecha, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO, $observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
				$CRecibo 			= new cReciboDeOperacion ( RECIBOS_TIPO_PAGO_CREDITO, true, $recibo );
			}
			
			// Set a Mvto Contable
			$CRecibo->setGenerarPoliza();
			$CRecibo->setGenerarTesoreria();
			$CRecibo->setGenerarBancos();
			if($CRecibo->init() == true){
				$recibo						= $CRecibo->getCodigoDeRecibo();
				$this->mReciboDeOperacion	= $recibo;
				// Operacion: Agregar el Movimiento
				$CRecibo->setNuevoMvto ( $fecha, $monto, OPERACION_CLAVE_PAGO_CAPITAL, $parcialidad, $observaciones, 1, TM_ABONO, $socio, $this->mNumeroCredito );
				$this->mNuevoSaldo 	= $this->mSdoCapitalInsoluto - ($monto);
				$this->mSucess 		= true;
				// Actualizar la Cuenta
				$xLog->add("WARN\tSALDO\tSe Actualiza el Saldo de " . $this->mSdoCapitalInsoluto . " a " . $this->mNuevoSaldo . " al $fecha \r\n");
				$CRecibo->setFinalizarRecibo(true);
			} else {
				$this->mSucess		= false;
				$recibo				= 0;
				$xLog->add("ERROR\tNo Existe Recibo con el cual trabajar ($recibo) \r\n");
			}

			

			// Credito: afectar el capital
			if ($this->mSucess == true) {
				$EsPagado			= ($this->mNuevoSaldo <= TOLERANCIA_SALDOS) ? true : false;
				if($this->isAFinalDePlazo() == false){
					$numero_plan 		= $this->getNumeroDePlanDePagos();
					if (setNoMenorQueCero ( $numero_plan ) > 0 and $parcialidad > 0) {
						$xPlan 			= new cPlanDePagos ( $numero_plan ); // no se necesita inicializar el plan de pagos
						$xPlan->setActualizarParcialidad ( $parcialidad, "(afectacion_real - $monto)", false, false, false, false, false );
					}
				}
				if ($EsPagado == true) {
					$this->setCreditoPagado( $fecha );
				} else {
					$arrUpdate			=  array ("fecha_ultimo_mvto" => $fecha, "fecha_ultimo_capital" => $fecha, "saldo_actual" => $this->mNuevoSaldo, "recibo_ultimo_capital" => $recibo);
					//No es pago total y es diferente al ultimo pago
					if($parcialidad < $this->getPagosAutorizados()){
						$arrUpdate["ultimo_periodo_afectado"]	= $parcialidad;
					}
					$this->setUpdate($arrUpdate);
				}
			}
			
			$this->mFechaOperacion	= $fecha;
			$this->mObjRec 			= $CRecibo;
			$xLog->add($CRecibo->getMessages(), $xLog->DEVELOPER);
			$this->mMessages 		.= $xLog->getMessages();
		}
		return $recibo;
	}
	function setDispocision($Monto , $TipoDePago	= TESORERIA_PAGO_NINGUNO, $ChequeDocumento = "", $Fecha = false, $Observaciones = "" , $CuentaBancaria = false, 
			$ReciboFiscal = "", $Moneda = EACP_CLAVE_MONEDA_LOCAL, $Periodo = 0, $ReciboOperacion = false){
		$xF					= new cFecha();
		$ReciboOperacion	= setNoMenorQueCero($ReciboOperacion);
		$Fecha				= $xF->getFechaISO($Fecha);
		$CuentaBancaria		= setNoMenorQueCero($CuentaBancaria);
		$Ready				= true;
		$xRec				= new cReciboDeOperacion(RECIBOS_TIPO_DISPOCISION, false, $ReciboOperacion);
		$xLog				= new cCoreLog();
		if($xRec->init() == false){
			//Agregar Recibo
			$ReciboOperacion = $xRec->setNuevoRecibo($this->getClaveDePersona(), $this->getClaveDeCredito(), $Fecha, $Periodo, RECIBOS_TIPO_DISPOCISION, $Observaciones, $ChequeDocumento, 
					$TipoDePago, $ReciboFiscal, $this->getClaveDeGrupo(), $CuentaBancaria,
					$Moneda, $Monto, $this->getClaveDeEmpresa(), 0);
			//Inicializar Recibo
			$xRec			= new cReciboDeOperacion(RECIBOS_TIPO_DISPOCISION, false, $ReciboOperacion);
			if($xRec->init() == false){
				$Ready		= false;
				$xLog->add( "ERROR\tEl Recibo no se genera ($ReciboOperacion)r\n");
			}
		}
		if($Ready == true){
			$xRec->setNuevoMvto($Fecha, $Monto, OPERACION_CLAVE_DISPOCISION, $Periodo, $Observaciones, SYS_UNO, false, $this->getClaveDePersona(),$this->getClaveDeCredito());
			$xRec->setFinalizarRecibo(true);
			$this->setRevisarSaldo(false);
		}
		$xLog->add($xRec->getMessages(), $xLog->DEVELOPER);
		$this->mMessages		.= $xLog->getMessages();
		return $ReciboOperacion;
	}
	function setReciboDeOperacion($recibo) { $this->mReciboDeOperacion = $recibo;	}
	function setTipoDeLugarDeCobro($TipoDeLugar = false, $actualizar = false){
		$TipoDeLugar	= setNoMenorQueCero($TipoDeLugar);
		if($TipoDeLugar > 0){
			$this->mTipoLugarDeCobro	= $TipoDeLugar;
			if($actualizar == true){
				$this->setUpdate(array("tipo_de_lugar_de_pago" => $TipoDeLugar), true);
			}
		}
	}
	
	/**
	 * Funcion que asiste en la Ministracion del Credito
	 */
	function setMinistrar($recibo_fiscal, $cheque, $monto_cheque = 0, $cuenta_cheques = false, $cheque2 = 0, $cuenta_cheques2 = 0, $observaciones = "", $fecha = false, $recibo = false, $tipo_de_pago = TESORERIA_PAGO_CHEQUE) {
		$sucess 		= true;
		$monto_cheque	= setNoMenorQueCero($monto_cheque);
		$monto_cheque 	= ($monto_cheque == 0) ? $this->getMontoAutorizado () : $monto_cheque;
		$cuenta_cheques	= setNoMenorQueCero($cuenta_cheques);
		$cuenta_cheques = ($cuenta_cheques <= 0) ? DEFAULT_CUENTA_BANCARIA : $cuenta_cheques;
		$cheque 		= setNoMenorQueCero ( $cheque );
		// Corrige la Inicializacion del credito
		if (setNoMenorQueCero($this->mNumeroSocio)<= DEFAULT_SOCIO) {
			$this->init ();
		}
		$xSocio 		= new cSocio ( $this->mNumeroSocio );
		$xSocio->init();		
		$msg 			= "";
		$xLog			= new cCoreLog();
		$xF				= new cFecha();
		$xPaso			= new cCreditosProceso();
		
		// $tipo_de_pago = TESORERIA_PAGO_CHEQUE;
		$recibo 		= setNoMenorQueCero( $recibo );
		$DConvenio 		= $this->getDatosDeProducto();
		$xT				= new cCreditos_solicitud();
		
		$idsolicitud 	= $this->mNumeroCredito;
		$solicitud 		= $this->mNumeroCredito;
		/* ------------------------------ obtiene el Monto Autorizado ---------------------------------- */
		$dsol 					= $this->getDatosDeCredito ();
		$montoaut 				= $this->getMontoAutorizado (); // $dsol["monto_autorizado"]; // Monto Autorizado
		$fvcred 				= $this->getFechaDeVencimiento();
		$modpagos 				= $this->getPeriocidadDePago ();
		$tasacred 				= $this->getTasaDeInteres ();
		$totalop 				= $this->getMontoAutorizado();
		$tipoaut				= $this->getTipoDeAutorizacion();
		$socio 					= $this->mNumeroSocio;
		$intdi 					= $this->getInteresDiariogenerado(); 
		$diasa 					= $this->getDiasAutorizados();
		$tipoconvenio 			= $this->getClaveDeProducto();
		$elgrupo 				= $this->getClaveDeGrupo();
		$grupo 					= $this->getClaveDeGrupo();
		$idsocio 				= $this->getClaveDePersona();
		$fecha_propuesta 		= $this->getFechaDeMinistracion();
		
		$tasa_ordinaria_de_interes 		= $tasacred;
		$monto_autorizado_a_ministrar 	= $this->getMontoAutorizado(); //$dsol ["monto_autorizado"];
		$grupo 							= $this->mGrupoAsociado;
		$moneda							= AML_CLAVE_MONEDA_LOCAL;//TODO Agregar dolares o moneda extranjera;
		if ($fecha == false) {
			$fecha 						= $this->mFechaOperacion;
		}
		$socio							= $this->mNumeroSocio;
		// Valores conservados
		$fechavcto 						= $this->getFechaDeVencimiento();
		$diasaut 						= $this->getDiasAutorizados();
		$intdiario 						= $this->getInteresDiariogenerado();
		/**
		 * Corrige la opcion de que el Cheque es Igual a Cero
		 */
		if ($monto_cheque <= 0) {
			$monto_cheque = $this->getMontoAutorizado();
		}
		// Corrige el dato de recibo no valido
		if ($recibo <= 0 AND setNoMenorQueCero ( $this->mReciboDeOperacion ) > 0) {
			$recibo = $this->mReciboDeOperacion;
			$xLog->add("WARN\tSe Asigna por Recibo del Credito\r\n");
		}
		/* --------------------------------------------------------------------------------------------------------- */
		
		$OConv = $this->getOProductoDeCredito ( $tipoconvenio );
		
		// Varificar la validez
		if($this->mForceMinistracion == false){
			$valido		= $this->setVerificarValidez(false, $xPaso->PASO_ADESEMBOLSO);
			if($this->mValidacionERRS > 0){
				//$xLog->add();
				$sucess = false;
			}
		}
		
		// SI EL CREDITO ES AUTOMATIZADO
		if ($modpagos == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
			$diasaut = restarfechas ( $fechavcto, $fecha ); // $dsol["dias_autorizados"]; // Dias Autorizados.
			$xLog->add("WARN\tLos dias Autorizados se cambian a $diasaut\r\n", $xLog->DEVELOPER);
			// $fechavcto = sumardias($fecha, $diasaut); // Fecha del Sistema + Dias Autorizados
			//TODO: Modificar mediante formula por producto
			$intdiario = ($this->getMontoAutorizado() * $this->getTasaDeInteres()) / EACP_DIAS_INTERES;
		} else {
			// Verifica si existe el Plan de Pagos:
			$sqlck = "SELECT COUNT(tipo_docto) AS 'planes' FROM operaciones_recibos WHERE docto_afectado=$idsolicitud AND tipo_docto=11";
			$plan 			= $this->getNumeroDePlanDePagos ();
			$plan			= setNoMenorQueCero($plan);
			if ($plan <= 0){
				$xLog->add("ERROR\tNo se ha Generado el PLAN DE PAGOS \r\n");
				$sucess 	= false;
			}
			if ( $xF->getInt( $fecha ) !== $xF->getInt( $fecha_propuesta )) {
				
				$xLog->add("ERROR\tNo se puede Otorgar el Credito porque la Fecha de Desembolso($fecha) no coincide con el Plan de Pagos, fecha Inicial(" . $fecha_propuesta . "), vuelva a elaborar el PLAN DE PAGOS\r\n");
				$sucess = false;
			}
		}

		if ($this->mForceMinistracion == true) {
			$sucess = true;
			$xLog->add("WARN\tLa Ministracion es FORZADA \r\n");
		}
		/* ------------------------------- AFECTACIONES ------------------------------------------------- */
		if ($sucess == true) {
			$monto 			= $this->getMontoAutorizado();
			$parcialidad 	= 0;//2015-08-20
			if ($monto >0) {
				
				$CRecibo = new cReciboDeOperacion ( RECIBOS_TIPO_MINISTRACION, true, $recibo );
				// Set a Mvto Contable
				// $CRecibo->setGenerarPoliza();
				$CRecibo->setGenerarTesoreria ();
				$CRecibo->setGenerarBancos ( true );
				
				// Agregar recibo si no hay
				if (setNoMenorQueCero ( $recibo ) <= 0) {
					$recibo = $CRecibo->setNuevoRecibo ( $socio, $solicitud, $fecha, $parcialidad, RECIBOS_TIPO_MINISTRACION, 
							$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo, $cuenta_cheques, $moneda );
					/*$Tipo = false, $cadena = "", $cheque_afectador = "NA",
						$TipoPago = "", $recibo_fiscal = "", $grupo = false,
						$cuenta_bancaria = false, $moneda = "", $unidades = 0, $persona_asociada = false, $periodo = false*/
					
					// Checar si se agrego el recibo
					if (setNoMenorQueCero ( $recibo ) > 0) {
						$xLog->add("OK\tSe Agrego Exitosamente el Recibo $recibo de la Cuenta " . $this->mNumeroCredito . " Por Ministracion\r\n");
						$this->mReciboDeOperacion 	= $recibo;
						$sucess 					= true;
					} else {
						$xLog->add("ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCredito . " Por Ministracion \r\n");
						$sucess 					= false;
					}
				}
				$this->mReciboDeOperacion = $recibo;
				
				if (setNoMenorQueCero ( $recibo ) > 0) {
					// Agregar el Movimiento
					$CRecibo->setNuevoMvto ( $fecha, $monto, OPERACION_CLAVE_MINISTRACION, $parcialidad, $observaciones, 1, TM_CARGO, $socio, $solicitud );
					$this->addSDPM ( 0, 0, $fecha, 0, CREDITO_ESTADO_VIGENTE, $fecha, OPERACION_CLAVE_MINISTRACION );
					$sucess = true;
				} else {
					$xLog->add("ERROR\tNo Existe Recibo con el cual trabajar ($recibo) \r\n");
				}
				$CRecibo->setFinalizarRecibo ( true );
				$xLog->add( $CRecibo->getMessages(), $xLog->DEVELOPER );
				
				
				$xOrg		= new cCreditosDatosDeOrigen(false, $this->getClaveDeCredito());
				
				switch($this->getTipoDeOrigen()){
					case $xOrg->ORIGEN_ARRENDAMIENTO:
						$xLeas	= new cCreditosLeasing($this->getClaveDeOrigen());
						if($xLeas->init() == true){
							
							$xLeas->setPaso($xPaso->PASO_VIGENTE);
							
						}
						break;
				}
				
			} else {
				$xLog->add("ERROR\tEl Monto ($monto) a Otorgar deber ser Mayor a 0\r\n");
			}
			
			// Actualiza el estatus del credito a Vigente, la fecha de Operacion y la de vencimiento.
			
			$arrAct = array (
					"estatus_actual" => CREDITO_ESTADO_VIGENTE,
					"fecha_ministracion" => $fecha,
					"fecha_ultimo_mvto" => $fecha,
					
					"fecha_vencimiento" => $fechavcto,
					"plazo_en_dias" => $diasaut,
					"dias_autorizados" => $diasaut,
					"interes_diario" => $intdiario,
					"saldo_actual" => $montoaut 
			);
			if($sucess == true){
				if($this->setUpdate ( $arrAct ) === false){
					$xLog->add("ERROR\tLa actualizacion Fallo del Credito $solicitud\r\n");
				}
			} else {
				$xLog->add("ERROR\tEl Credito no puede actualizarse \r\n");
			}
			// --------------------------------- ENVIA LAS SENTENCIAS SQL----------------------------------------
			// verifica la dif entre cheque 1 y el monto a ministrar
			if ($monto_cheque <= 0) {
				$monto_cheque = $montoaut;
			}
			if($tipo_de_pago == TESORERIA_PAGO_TRANSFERENCIA){
				$xBan		= new cCuentaBancaria($cuenta_cheques);
				$xCaj		= new cCaja(false, $fecha);
				if($xBan->init() == true){
					$beneficiario 		= $xSocio->getNombreCompleto();
					$xBan->setNuevoRetiro($cheque, $recibo, $beneficiario, $monto_cheque);
					//Agregar Tesoreria
					$xCaj->addOperacion($recibo, $tipo_de_pago, $monto_cheque, $monto_cheque, 0, false, $cheque, $cuenta_cheques, 0, $observaciones, $fecha, false, $moneda, 0, $idsocio);
				}
				$xLog->add($xCaj->getMessages(), $xLog->DEVELOPER);
				$xLog->add($xBan->getMessages(), $xLog->DEVELOPER);
			}
			if($tipo_de_pago == TESORERIA_PAGO_CHEQUE){
				$difCheque 			= $montoaut - $monto_cheque;
				$beneficiario 		= $xSocio->getNombreCompleto();
				$descuento 			= 0;
				// Separar la generación del cheque
				$xBan		= new cCuentaBancaria($cuenta_cheques);
				$xCaj		= new cCaja(false, $fecha);
				if($xBan->init() == true){
					//Agregar Bancos
					$xBan->setNuevoCheque($cheque, $cuenta_cheques, $recibo, $beneficiario,$monto_cheque,$fecha, false, 0, $idsocio);
					//Agregar Tesoreria
					$xCaj->addOperacion($recibo, $tipo_de_pago, $monto_cheque, $monto_cheque, 0, false, $cheque, $cuenta_cheques, 0, $observaciones, $fecha, false, $moneda, 0, $idsocio);
					$xBan->setUltimoCheque($cheque);
				}
				
				
				if ($difCheque > 0) {
					$xBan		= new cCuentaBancaria($cuenta_cheques2);
					if($xBan->init() == true){
						$xBan->setNuevoCheque($cheque, $cuenta_cheques2, $recibo, $beneficiario,$monto_cheque2,$fecha, false, 0, $idsocio);
					}				
					// setPolizaProforma($recibo, 9200, $difCheque, $socio, $idsolicitud, TM_ABONO);
					//setNuevoCheque ( $cheque2, $cuenta_cheques2, $recibo, $beneficiario, $difCheque, $fecha, false, $descuento );
					$xBan->setUltimoCheque($cheque2 );
					//Agregar Tesoreria
					$xCaj->addOperacion($recibo, $tipo_de_pago2, $monto_cheque, $monto_cheque2, 0, false, $cheque2, $cuenta_cheques2, 0, $observaciones, $fecha, false, $moneda, 0, $idsocio);				
				}
				$xLog->add($xCaj->getMessages(), $xLog->DEVELOPER);
				$xLog->add($xBan->getMessages(), $xLog->DEVELOPER);
			}
			// Agregar Avisos de Credito por renovacion
			if ($this->getTipoDeAutorizacion () == CREDITO_TIPO_AUTORIZACION_RENOVACION) {
				// $xSoc = $this->getOPersona();
				$OEstats = $xSocio->getOEstats ();
				$OEstats->initDatosDeCredito ();
				
				$DCreds = $OEstats->getDatosDeCreditos ();
				$xCred = new cCreditos_solicitud ();
				foreach ( $DCreds as $clave => $valores ) {
					$xCred->setData ( $valores );
					if ($xCred->saldo_actual ()->v () >= TOLERANCIA_SALDOS) {
						if ($xCred->numero_solicitud ()->v () != $this->getNumeroDeCredito ()) { //Agregar Aviso de Renovacion
							$xSocio->addMemo ( MEMOS_TIPO_NOTA_RENOVACION, "Credito Renovado en la solicitud #" . $this->getNumeroDeCredito (), $xCred->numero_solicitud ()->v (), $fecha );
						}
					}
				}
			}
			$xLog->add($xSocio->getMessages(), $xLog->DEVELOPER);
			// ejecutar alertas por Ministracion de Reglas de Negocios
			// Ministracion de Credito de la persona {clave_de_persona} {nombre_de_persona}
			// credito numero {clave_de_credito} con monto {monto_de_credito} y tipo de autorizacion {tipo_de_autorizacion}.
			$OTipoAut = new cCreditos_tipo_de_autorizacion ();
			$OTipoAut->setData ( $OTipoAut->query ()->initByID ( $this->getTipoDeAutorizacion () ) );
			$xRegla = new cReglaDeNegocio ();
			$xRegla->setVariables ( array (
					"clave_de_persona" => $xSocio->getCodigo (),
					"nombre_de_persona" => $xSocio->getNombreCompleto (),
					"clave_de_credito" => $this->getNumeroDeCredito (),
					"monto_de_credito" => $this->getMontoAutorizado (),
					"tipo_de_autorizacion" => $OTipoAut->descripcion_tipo_de_autorizacion ()->v ( OUT_TXT ) 
			) );
			$xRegla->setExecuteActions ( $xRegla->reglas ()->RN_MINISTRAR_CREDITO );
			
			$TipoDeOrigen				= $this->getTipoDeOrigen();
			$xTO						= new cCreditosDatosDeOrigen($this->getClaveDeOrigen());
			//$xTO->init();
			
			switch ($TipoDeOrigen){
				case $xTO->ORIGEN_LINEA:
					$xLin				= new cCreditosLineas($this->getClaveDeOrigen());
					if($xLin->init() == true){
						$xLin->setUpdateMontoDisponible();
						$xLog->add($xLin->getMessages());
					}
					break;
			}
			
		} else {
			$xLog->add("ERROR\tNo se efectua operacion alguna\r\n");
		}
		$this->setAddEvento($xLog->getMessages(), 20018);
		
		$this->mMessages .= $xLog->getMessages();
		return $recibo;
	}
	/**
	 * Obtiene un monto de Gatos Notariales Pagados
	 */
	function getPagoDeGastosNotariales(){ return $this->getSumMovimiento(OPERACION_CLAVE_PAGO_NOT);	}
	function setForceMinistracion($force = true){$this->mForceMinistracion = $force;}
	
	/**
	 * funcion que agrega un aval al crédito
	 */
	function addAval($AvalNumeroSocio, $MontoAvalado, $TipoDeAval, $Consanguinidad = 99, $Dependiente = 0, $Observaciones = "") {
		if ($this->mCreditoInicializado == false) {
			$this->init ();
		}
		$xSoc = new cSocio ( $this->getClaveDePersona () );
		if($xSoc->init () == true){
			$porcentaje = (setNoMenorQueCero ( $MontoAvalado ) > 0 and setNoMenorQueCero ( $this->mMontoAutorizado ) > 0) ? round ( ($MontoAvalado / $this->mMontoAutorizado), 2 ) : $this->mMontoAutorizado;
			$fecha 		= fechasys();
			$rs			= $xSoc->addRelacion ( $AvalNumeroSocio, $TipoDeAval, $Consanguinidad, $Dependiente, $Observaciones, $MontoAvalado, $porcentaje, $fecha, $this->mNumeroCredito );
			if($rs === false){
				$this->mMessages .= "ERROR\tError en alta de Aval Vinculado $AvalNumeroSocio\r\n";
			} else {
				$this->mMessages	.= "OK\tAval Codigo $AvalNumeroSocio agregado al credito " . $this->mNumeroCredito . "\r\n";
				$this->setCuandoSeActualiza();
			}
		$this->mMessages .= ($rs == true) ? "OK\tAval Codigo $AvalNumeroSocio agregado al credito " . $this->mNumeroCredito . "\r\n" : "ERROR\tError en alta de Aval\r\n";
		} else {
			$rs			= false;
		}
		if(MODO_DEBUG == true) {
			$this->mMessages .= $xSoc->getMessages();
		}
		$this->setAddEvento($this->mMessages, 20017);
		return $rs;
	}
	/**
	 * Agregar un Nuevo Credito.
	 * 
	 * @param int $TipoDeConvenio
	 * @param int $NumeroDeSocio
	 * @param int $ContratoCorriente
	 * @param float $MontoSolicitado
	 * @param int $PeriocidadDePago
	 * @param int $NumeroDePagos
	 * @param int $PlazoEnDias
	 * @param int $DestinoDeCredito
	 * @param int $NumeroDeCredito
	 * @param int $GrupoAsociado
	 * @param string $DescripcionDelDestino
	 * @param string $Observaciones
	 * @param int $OficialDeCredito
	 * @param string $FechaDeSolicitud
	 * @param int $TipoDePago
	 * @param int $TipoDeBase Base Insolutos o Base capital
	 * @param float $TasaDeInteres
	 * @param string $FechaDeMinistracion
	 * @param int $persona_asociada
	 * @param int $TipoDeAutorizacion
	 * @param int $id_de_origen
	 * @param int $tipo_de_origen
	 * @param int $LugarDeCobro
	 * @param int $TipoDeDesembolso
	 * @return boolean Resultado del proceso
	 */
	function add($TipoDeConvenio, $NumeroDeSocio, $ContratoCorriente, $MontoSolicitado, $PeriocidadDePago = 0, $NumeroDePagos = 0, $PlazoEnDias = 0, $DestinoDeCredito = 0, $NumeroDeCredito = false, 
			$GrupoAsociado = 0, $DescripcionDelDestino = "", $Observaciones = "", $OficialDeCredito = 0, $FechaDeSolicitud = false, $TipoDePago = 0, $TipoDeBase = 0, $TasaDeInteres = false, 
			$FechaDeMinistracion = false, $persona_asociada = false,$TipoDeAutorizacion = false, $id_de_origen = 1, $tipo_de_origen = false, $LugarDeCobro = false, $TipoDeDesembolso = false) {
		$xF 				= new cFecha();
		$xT 				= new cTipos();
		$xSoc 				= new cSocio( $NumeroDeSocio );
		$xLog 				= new cCoreLog();
		$xRuls				= new cReglaDeNegocio();
		$PuedeTasaCero		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PUEDEN_TASA_CERO);
		$xSoc->init ();
		$OficialDeCredito	= setNoMenorQueCero($OficialDeCredito);
		$OficialDeCredito 	= ($OficialDeCredito <= 0) ? getUsuarioActual () : $OficialDeCredito;
		
		$PlazoEnDias 		= ($PlazoEnDias == 0) ? ($PeriocidadDePago * $NumeroDePagos) : $PlazoEnDias;
		$NumeroDePagos 		= ($NumeroDePagos == 0) ? ($PlazoEnDias / $PeriocidadDePago) : $NumeroDePagos;
		$ContratoCorriente 	= setNoMenorQueCero ( $ContratoCorriente );
		$ContratoCorriente 	= ($ContratoCorriente <= 0) ? CTA_GLOBAL_CORRIENTE : $ContratoCorriente;
		$persona_asociada 	= ($persona_asociada == false) ? $xSoc->getClaveDeEmpresa () : $persona_asociada;
		$persona_asociada	= setNoMenorQueCero($persona_asociada);
		$persona_asociada	= ($persona_asociada > 0) ? $persona_asociada : FALLBACK_CLAVE_EMPRESA;
		
		
		$GrupoAsociado 		= setNoMenorQueCero ( $GrupoAsociado );
		$GrupoAsociado 		= ($GrupoAsociado <= 0) ? DEFAULT_GRUPO : $GrupoAsociado;
		$TipoDeAutorizacion = setNoMenorQueCero ( $TipoDeAutorizacion );
		$LugarDeCobro 		= setNoMenorQueCero ( $LugarDeCobro ); // lugar en que se le va a cobrar
		$TipoDeDesembolso 	= setNoMenorQueCero ( $TipoDeDesembolso );
		$LugarDeCobro 		= ($LugarDeCobro <= 0) ? FALLBACK_CREDITOS_LUGAR_DE_PAGO : $LugarDeCobro;
		$TipoDeDesembolso 	= ($TipoDeDesembolso <= 0) ? FALLBACK_CREDITOS_TIPO_DESEMBOLSO : $TipoDeDesembolso;
		$NumeroDeCredito 	= setNoMenorQueCero ( $NumeroDeCredito );
		if ($NumeroDeCredito <= DEFAULT_CREDITO or $NumeroDeCredito <= FALLBACK_CLAVE_DE_CREDITO) {
			$NumeroDeCredito = $xSoc->getIDNuevoDocto ( iDE_CREDITO );
		}
		//
		$DestinoDeCredito	= setNoMenorQueCero($DestinoDeCredito);
		$DestinoDeCredito	= ($DestinoDeCredito <= 0) ? CREDITO_DEFAULT_DESTINO : $DestinoDeCredito;
		$TipoDePago			= setNoMenorQueCero($TipoDePago);
		$TipoDePago			= ($TipoDePago <= 0) ? CREDITO_TIPO_PAGO_UNICO : $TipoDePago;
		//
		$DConv 				= $this->getDatosDeProducto ( $TipoDeConvenio );
		$DOConv 			= $this->getOProductoDeCredito ( $TipoDeConvenio );
		$EsTasaModificable 	= ($TasaDeInteres === false) ? true : false; // Tasa modificable 2015-03-03
		$TasaDeInteres 		= ($TasaDeInteres === false) ? $DOConv->getTasaDeInteres() : setNoMenorQueCero ( $TasaDeInteres, 4 );
		if($PuedeTasaCero == false AND $TasaDeInteres <= 0){
			$TasaDeInteres	= $DOConv->getTasaDeInteres();
		}
		//Purga la empresa, si el credito no es de nomina
		if($DOConv->getEsProductoDeNomina() == false){
			$persona_asociada	= FALLBACK_CLAVE_EMPRESA;
		}
		if($DOConv->getEsProductoDeGrupos() == false){
			$GrupoAsociado		= FALLBACK_CLAVE_DE_GRUPO;
		}
		$TasaMoratorio 		= $DConv ["interes_moratorio"];
		$TasaDeAhorro 		= $DOConv->getTasaDeAhorro ();
		$NivelDeRiesgo 		= $DConv ["nivel_riesgo"];
		$TipoDeCredito 		= $DConv ["tipo_de_credito"];
		$factor_interes 	= 1;
		if ($tipo_de_origen == iDE_PRESUPUESTO) {
			$xPres 			= new cCreditosPresupuesto ( $id_de_origen );
			if ($xPres->init () == true) {
				$factor_interes = $xPres->getTasaPonderada ();
			}
		}
		$DiasToleradosVenc	= 0;
		$xPer				= new cPeriocidadDePago($PeriocidadDePago);
		if($xPer->init() == true){
			$DiasToleradosVenc	= $xPer->getDiasToleradosEnVencer();
		}
		if ($EsTasaModificable == true) {
			$xForm = new cFormula ( "solicitud_pre_tasa_producto_$TipoDeConvenio" );
			if ($xForm->init () == true) {
				$tasa_interes = $TasaDeInteres;
				eval ( $xForm->getFormula () ); // mutar tasa
				//setLog("Evaluar " . $xForm->getFormula());
				$TasaDeInteres = ($tasa_interes < 0) ? $TasaDeInteres : $tasa_interes; //evaluar cero
			}
		}
		$TipoDeAutorizacion 	= ($TipoDeAutorizacion <= 0) ? $DConv ["tipo_autorizacion"] : $TipoDeAutorizacion;
		$PeriodoDeCredito 		= EACP_PER_SOLICITUDES;
		
		if ($TipoDePago == CREDITO_TIPO_PAGO_UNICO and $NumeroDePagos > 1) {
			$TipoDePago = CREDITO_TIPO_PAGO_PERIODICO;
		}
		if ($PeriocidadDePago == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
			if ($NumeroDePagos > 1) {
				$xLog->add ( "WARN\tNumero de Pagos Incorrectos a la periocidad $TipoDePago, Se cambia de $NumeroDePagos a 1\r\n" );
				$NumeroDePagos = 1;
			}
		}
		$FechaDeSolicitud 			= ($FechaDeSolicitud == false) ? $xF->get () : $FechaDeSolicitud;
		$FechaDeMinistracion 		= ($FechaDeMinistracion == false) ? $FechaDeSolicitud : $FechaDeMinistracion;
		$FechaDeUltOperacion		= $FechaDeSolicitud;
		$FechaDeRevision 			= $FechaDeSolicitud;
		$FechaConciliada 			= $FechaDeSolicitud;
		$tipo_de_origen 			= setNoMenorQueCero ( $tipo_de_origen );
		$id_de_origen 				= setNoMenorQueCero ( $id_de_origen );
		$xP 						= new cPeriodoDeCredito ( $PeriodoDeCredito );
		$FechaDeAutorizacion 		= $FechaDeMinistracion; // $xP->getFechaDeReunion();
		
		$CausaDeMora 				= 99;
		$EstatusActual 				= CREDITO_ESTADO_SOLICITADO;
		
		$FechaDeVencimiento 		= $xF->setSumarDias ( $PlazoEnDias, $FechaDeMinistracion );
		$FechaDeMora 				= $xF->setSumarDias($DOConv->getDiasTolerados(), $FechaDeVencimiento);
		$FechaDeVencimientoDinamico = $xF->setSumarDias($DiasToleradosVenc, $FechaDeMora);
		$FechaDeCastigo 			= $xF->getFechaMaximaOperativa ();
		$FechaPrimerPago 			= ($PeriocidadDePago == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) ? $FechaDeVencimiento : $xF->setSumarDias ( $PeriocidadDePago, $FechaDeMinistracion );
		$TipoDeBase				= ($TipoDeBase <= 0) ? $DOConv->getTipoDeBaseCalc() : $TipoDeBase;
		$TipoDeBase				= ($TipoDeBase <= 0) ? INTERES_POR_SALDO_INSOLUTO : $TipoDeBase;
		$PagosAutorizados 			= 0;
		$PlazoEnDiasAutorizado 		= 0;
		
		$MontoAutorizado 			= 0;
		$SaldoActual 				= 0;
		$SaldoVencido 				= 0;
		$SaldoConciliado 			= 0;
		$MontoParcialidad 			= 0;
		
		$InteresNormalDevengado 	= 0;
		$InteresNormalPagado 		= 0;
		$InteresMoratorioPagado 	= 0;
		$InteresMoratorioDevengado 	= 0;
		$InteresDiario 				= 0;
		$InteresAnticipado 			= 0;
		
		$OficialDeSeguimiento 		= $OficialDeCredito;
		$iduser 					= getUsuarioActual ();
		
		$PeriodoDeNotificacion 		= 0;
		$PeriodoAfectado 			= 0;
		$NotasDeAuditoria 			= "";
		$CadenaH 					= "";
		$DoctoDeAutorizacion 		= "";
		
		$sucursal 					= getSucursal ();
		$eacp 						= EACP_CLAVE;
		
		$sqlNC = "INSERT INTO creditos_solicitud(
					numero_socio, numero_solicitud, grupo_asociado, contrato_corriente_relacionado,
					tipo_convenio, tipo_de_pago, tipo_de_calculo_de_interes, periocidad_de_pago, tipo_credito, nivel_riesgo,
					estatus_actual, tipo_autorizacion, causa_de_mora, periodo_solicitudes, destino_credito,
					fecha_solicitud, fecha_autorizacion, fecha_ministracion, fecha_ultimo_mvto, fecha_conciliada, 
					fecha_mora, fecha_vencimiento, fecha_vencimiento_dinamico, fecha_castigo,
					plazo_en_dias, dias_autorizados, numero_pagos, pagos_autorizados,
					monto_solicitado, monto_autorizado, saldo_actual, saldo_vencido, saldo_conciliado, monto_parcialidad, 
					interes_normal_devengado,  interes_normal_pagado, interes_moratorio_devengado, interes_moratorio_pagado, interes_diario,  sdo_int_ant,
					tasa_interes, tasa_moratorio, tasa_ahorro,
					ultimo_periodo_afectado,  periodo_notificacion,
					idusuario, oficial_seguimiento, oficial_credito,
					docto_autorizacion, observacion_solicitud,  notas_auditoria, descripcion_aplicacion,
					sucursal, eacp, persona_asociada, `operacion_origen`,`tipo_de_origen`, `fecha_de_primer_pago`, `tipo_de_lugar_de_pago`,`tipo_de_dispersion`,
					`fecha_de_proximo_pago`,`fecha_ultimo_capital`
					)
					VALUES (
					$NumeroDeSocio, $NumeroDeCredito, $GrupoAsociado, $ContratoCorriente,
					$TipoDeConvenio, $TipoDePago, $TipoDeBase, $PeriocidadDePago, $TipoDeCredito, $NivelDeRiesgo,
					$EstatusActual, $TipoDeAutorizacion, $CausaDeMora, $PeriodoDeCredito, $DestinoDeCredito,
					'$FechaDeSolicitud', '$FechaDeAutorizacion', '$FechaDeMinistracion', '$FechaDeUltOperacion', '$FechaConciliada',
					'$FechaDeMora', '$FechaDeVencimiento', '$FechaDeVencimientoDinamico', '$FechaDeCastigo',
					$PlazoEnDias, $PlazoEnDiasAutorizado, $NumeroDePagos, $PagosAutorizados, 
					$MontoSolicitado, $MontoAutorizado, $SaldoActual, $SaldoVencido, $SaldoConciliado, $MontoParcialidad,
					$InteresNormalDevengado, $InteresNormalPagado, $InteresMoratorioDevengado, $InteresMoratorioPagado, $InteresDiario, $InteresAnticipado,
					$TasaDeInteres, $TasaMoratorio, $TasaDeAhorro,
					$PeriodoAfectado, $PeriodoDeNotificacion,
					$iduser, $OficialDeSeguimiento, $OficialDeCredito,
					'$DoctoDeAutorizacion', '$Observaciones',  '$NotasDeAuditoria', '$DescripcionDelDestino',
					'$sucursal', '$eacp', $persona_asociada, $id_de_origen, $tipo_de_origen, '$FechaPrimerPago', $LugarDeCobro, $TipoDeDesembolso,
					'$FechaPrimerPago', '$FechaDeMinistracion'
					) ";
		$xQL		= new MQL();
		$x 			= $xQL->setRawQuery($sqlNC);// my_query ( $sqlNC );
		$x			= ($x === false ) ? false : true;
		if ($x == false) {
			$xLog->add ( "$NumeroDeSocio\t$NumeroDeCredito\tERROR\tError al agregar el credito $NumeroDeCredito\r\n" );
		} else {
			$xLog->add ( "$NumeroDeSocio\t$NumeroDeCredito\tOK\tCredito $NumeroDeCredito Agregado con exito\r\n" );
			$this->mNumeroCredito	= $NumeroDeCredito;
			$this->set ( $NumeroDeCredito, true );
			// Actualizar Planeacion en Grupos
			if ($DOConv->getEsProductoDeGrupos () == true) {
				$xGrupo = new cGrupo ( $xSoc->getClaveDeGrupo () );
				$xGrupo->init ();
				$xGrupo->setActualizarPlaneacion ( $FechaDeSolicitud, $NumeroDeSocio, $NumeroDeCredito );
				$xLog->add ( $xGrupo->getMessages () );
			}
			//Datos de Originacion
			$xOrg			= new cCreditosDatosDeOrigen(false, $NumeroDeCredito);
			$MontoVinculado	= 0;
			if($tipo_de_origen == $xOrg->ORIGEN_REESTRUCTURA OR $tipo_de_origen == $xOrg->ORIGEN_RENOVACION){
				$xCredO	= new cCredito($id_de_origen);
				if($xCredO->init() == true){
					$MontoVinculado	= $xCredO->getSaldoActual();
				}
			}
			//
				
			$ores			= $xOrg->add($tipo_de_origen, $id_de_origen, $MontoVinculado);
			//$ores = $this->addDatosDeOrigen($tipo_de_origen, $id_de_origen);
			if($ores == true){
				$xLog->add("OK\tSe agrega Origen de Credito\r\n", $xLog->DEVELOPER);
			}
			// Actualizar Origenes
			switch ($tipo_de_origen) {
				case $xOrg->ORIGEN_PRESUPUESTO :
					$xPre = new cCreditosPresupuesto ( $id_de_origen );
					$xPre->init();
					$xPre->setCerrado ( $NumeroDeCredito, $MontoAutorizado );
					$xLog->add( $xPre->getMessages() );
				break;
				case $xOrg->ORIGEN_PRECLIENTE :
					$xPre 		= new cCreditosPreclientes( $id_de_origen );
					if($xPre->init() == true){
						$xPre->setInactivo($NumeroDeCredito);
						$xLog->add( $xPre->getMessages() );
					}
					break;
				case $xOrg->ORIGEN_ARRENDAMIENTO:
					$xLeas	= new cCreditosLeasing($id_de_origen);
					if($xLeas->init() == true){
						$xLeas->setCredito($NumeroDeCredito);
						
					}
				break;
			}
		}
		$this->setCuandoSeActualiza();
		$this->setAddEvento($xLog->getMessages(),20001);
		
		$this->mMessages .= $xLog->getMessages();
		return $x;
	}
	function addDatosDeOrigen($tipo_de_origen, $id_de_origen){
		$xOrg	= new cCreditosDatosDeOrigen(false, $this->mNumeroCredito);
		$ores	= $xOrg->add($tipo_de_origen, $id_de_origen);
		return $ores;
	}
	/**
	 * Agrega un Saldo Diario Promedio por cada evento de credito
	 * 
	 * @param float $interes        	
	 * @param float $moratorio        	
	 * @param variant $FechaAnterior        	
	 * @param float $saldo        	
	 * @param integer $estatus        	
	 * @param variant $fecha        	
	 * @param float $operacion        	
	 */
	function addSDPM($interes = 0, $moratorio = 0, $FechaAnterior = false, $saldo = false, $estatus = false, $fecha = false, $operacion = false, $saldo_calculado = false, $periodo = false, $dias = false) {
		$solicitud 			= $this->mNumeroCredito;
		$xLog				= new cCoreLog();
		$xD 				= new cFecha ( 0 );
		$xQL				= new MQL();
		// agregar valoracion de inicio de credito
		if ($this->mCreditoInicializado == false){ $this->init (); }
		$socio 				= $this->mNumeroSocio;
		$periodo 			= ($periodo == false) ? $this->getPeriodoActual () : $periodo;
		$saldo 				= ($saldo === false) ? $this->mSdoCapitalInsoluto : $saldo;
		$estatus 			= ($estatus == false) ? $this->mEstatusActual : $estatus;
		$operacion 			= ($operacion == false) ? 0 : $operacion;
		$msg 				= "";
		
		
		$fecha 				= ($fecha == false) ? $xD->get () : $fecha;
		$dias_transcurridos = ($dias == false) ? $xD->setRestarFechas ( $fecha, $FechaAnterior ) : $dias;
		$saldo_calculado 	= ($saldo_calculado === false) ? $saldo * $dias_transcurridos : $saldo_calculado;
		
		$sqlSD = "INSERT INTO creditos_sdpm_historico
		            (numero_de_socio, numero_de_credito,
		            fecha_actual, fecha_anterior, dias_transcurridos,
					monto_calculado, saldo, estatus, interes_normal, interes_moratorio, tipo_de_operacion, periodo)
				    VALUES ($socio, $solicitud, '$fecha', '$FechaAnterior', $dias_transcurridos,
					$saldo_calculado, $saldo, $estatus, $interes, $moratorio, $operacion, $periodo) ";
		$res				= $xQL->setRawQuery( $sqlSD );
		if ($res === false) {
			$xLog->add("$socio\t$solicitud\tSDPM\tERROR\tSe Fallo al Agregar SDPM $saldo_calculado, dias $dias_transcurridos, Estatus $estatus, Interes $interes, Moratorio $moratorio\r\n", $xLog->DEVELOPER);
		} else {
			$xLog->add( "$socio\t$solicitud\tSDPM\tSUCESS\tSe agrego SDPM $saldo_calculado, dias $dias_transcurridos, Estatus $estatus, Interes $interes, Moratorio $moratorio\r\n", $xLog->DEVELOPER);
		}
		$this->mMessages 	.= $xLog->getMessages();
		return $xLog->getMessages();
	}
	/**
	 * Obtiene la Suma de Movimientos de un credito
	 * 
	 * @param integer $idMvto        	
	 */
	function getSumMovimiento($idMvto) {
		$docto = $this->mNumeroCredito;
		$sqlsm = "SELECT SUM(afectacion_real) AS 'neto' FROM operaciones_mvtos WHERE docto_afectado=$docto AND tipo_operacion=$idMvto";
		$sumado = mifila ( $sqlsm, "neto" );
		return $sumado;
	}
	/**
	 * Obtiene la proxima parcialidad de pago de la persona
	 */
	function getProximaParcialidad(){ return $this->mNumeroProximaParcialidad; }
	/**
	 * @deprecated @since 2016.02.01
	 * @return number
	 */
	function getInitProximaParcialidad(){
		$xF			= new cFecha();
		$xQL		= new MQL();
		if($this->mCreditoInicializado == false) { $this->init (); }
		$sqlPP 		= "SELECT * FROM `creditos_proximas_parcialidades` WHERE credito=" . $this->mNumeroCredito . " LIMIT 0,1";
		$DPP 		= $xQL->getDataRow( $sqlPP );
	
		if (! isset ( $DPP ["parcialidad"] )) {
			$this->mMessages 					.= "ERROR\tLa Parcialidad no fue encontrada\r\n";
			$this->mNumeroProximaParcialidad	= $this->getPeriodoActual()+1;
			$this->mParcialidadesConSaldo		= setNoMenorQueCero($this->getPagosAutorizados()-$this->getPeriodoActual());
		}  else {
			$this->mNumeroProximaParcialidad	= $DPP["parcialidad"];
			$this->mFechaProximaParcialidad 	= $xF->getFechaISO($DPP ["fecha_de_pago"]);
			$this->mParcialidadesConSaldo		= $DPP ["capital_pendiente"];
			$this->mInitProximaParc				= true;
		}
	
		unset ( $DPP );
	
		return $this->mNumeroProximaParcialidad;
	}	
	/**
	 * Autoriza los creditos
	 * 
	 * @param float $Monto        	
	 * @param integer $Pagos        	
	 * @param integer $Periocidad        	
	 * @param integer $TipoDeAutorizacion        	
	 * @param mixed $FechaDeAutorizacion        	
	 * @param string $DocumentoDeAutorizacion        	
	 * @param integer $FormaDePago        	
	 * @param mixed $FechaDeMinistracionPropuesta        	
	 * @param integer $NivelDeRiesgo        	
	 * @param integer $PlazoEnDias        	
	 * @param mixed $FechaDeVencimiento        	
	 * @param integer $EstatusImpuesto        	
	 * @param float $SaldoImpuesto        	
	 * @param float $InteresImpuesto        	
	 * @param mixed $FechaOperacionImpuesta        	
	 */
	function setAutorizado($Monto, $Pagos, $Periocidad, $TipoDeAutorizacion, $FechaDeAutorizacion, $DocumentoDeAutorizacion, $FormaDePago = false, $FechaDeMinistracionPropuesta = false, 
			$NivelDeRiesgo = 2, $PlazoEnDias = false, $FechaDeVencimiento = false, $EstatusImpuesto = false, $SaldoImpuesto = 0, $InteresImpuesto = 0, $FechaOperacionImpuesta = false, $TasaDeInteres = false, 
			$LugarDeCobro = false, $TipoDeDesembolso = false) {
		$this->init();
		$xProc						= new cCreditosEventos();
		$xRuls						= new cReglaDeNegocio();
		//$xEvt
		
		$xF 						= new cFecha(0);
		$PuedeTasaCero				= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PUEDEN_TASA_CERO);
		
		$LugarDeCobro 				= setNoMenorQueCero ( $LugarDeCobro ); // lugar en que se le va a cobrar
		$TipoDeDesembolso 			= setNoMenorQueCero ( $TipoDeDesembolso );
		$LugarDeCobro 				= ($LugarDeCobro <= 0) ? FALLBACK_CREDITOS_LUGAR_DE_PAGO : $LugarDeCobro;
		$TipoDeDesembolso 			= ($TipoDeDesembolso <= 0) ? FALLBACK_CREDITOS_TIPO_DESEMBOLSO : $TipoDeDesembolso;
		$TasaDeInteres				= setNoMenorQueCero($TasaDeInteres);
		$FechaDeMinistracionPropuesta = $xF->getFechaISO($FechaDeMinistracionPropuesta);
		$tasa_moratorio				= $this->getTasaDeMora();
		$ready						= true;
		$xPdto						= $this->getOProductoDeCredito();
		$FechaDeProximoPago			= $FechaOperacionImpuesta;
		switch ($Periocidad) {
			case CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO :
				$InteresImpuesto 		= ($InteresImpuesto == 0) ? (($this->mMontoSolicitado * $this->mTasaInteres) / EACP_DIAS_INTERES) : $InteresImpuesto;
				$FormaDePago 			= CREDITO_TIPO_PAGO_UNICO;
				$FechaDeVencimiento 	= ($FechaDeVencimiento == false) ? $this->mFechaVencimiento : $FechaDeVencimiento;
				$PlazoEnDias 			= ($PlazoEnDias == false) ? $xF->setRestarFechas ( $FechaDeVencimiento, $FechaDeMinistracionPropuesta ) : $PlazoEnDias;
				$FechaOperacionImpuesta	= $FechaDeMinistracionPropuesta;
				break;
			default :
				$InteresImpuesto 	= ($InteresImpuesto == 0) ? (($this->mMontoSolicitado * $this->mTasaInteres) / EACP_DIAS_INTERES) : $InteresImpuesto;
				$PlazoEnDias 		= ($PlazoEnDias == false) ? ($Periocidad * $Pagos) : $PlazoEnDias;
				$FechaDeVencimiento = ($FechaDeVencimiento == false) ? $xF->setSumarDias ( $PlazoEnDias, $FechaDeMinistracionPropuesta ) : $FechaDeVencimiento;
				$FormaDePago 		= ($FormaDePago == false) ? CREDITO_TIPO_PAGO_PERIODICO : $FormaDePago;
				if($FechaOperacionImpuesta === false){
					$FechaOperacionImpuesta	= $FechaDeMinistracionPropuesta;
					$FechaDeProximoPago		= $xF->setSumarDias($Periocidad, $FechaDeMinistracionPropuesta);
				}
				break;
		}
		switch ($TipoDeAutorizacion) {
			case CREDITO_TIPO_AUTORIZACION_AUTOMATICA :
				
				break;
			case CREDITO_TIPO_AUTORIZACION_RENOVACION :
				break;
			case CREDITO_TIPO_AUTORIZACION_NORMAL :
				break;
		}
		$EstatusImpuesto	= setNoMenorQueCero($EstatusImpuesto);
		$EstatusImpuesto 	= ($EstatusImpuesto == 0) ? CREDITO_ESTADO_AUTORIZADO : $EstatusImpuesto;
		$TasaDeInteres 		= ($TasaDeInteres <= 0 AND $PuedeTasaCero == false) ? $this->mTasaInteres : $TasaDeInteres; 
		//--- Normalizar nombres
		$TASA_MORATORIO		= $tasa_moratorio;
		$TASA_INTERES		= $TasaDeInteres; 
		//Evaluaciones
		eval($xPdto->getFormulaInteresMoratorio($xProc->AUTORIZACION));
		eval($xPdto->getFormulaInteresNormal($xProc->AUTORIZACION));
		$TasaDeInteres		= $TASA_INTERES;
		$tasa_moratorio		= $TASA_MORATORIO;
		//comprobaciones
		if($TasaDeInteres<=0){
			if($TasaDeInteres == 0){
				if($PuedeTasaCero == true){
					$ready				= true;
					$tasa_moratorio		= 0;
				} else {
					$ready				= false;
					$this->mMessages	.= "ERROR\tLa TASA_INTERES No debe ser menor  cero segun REGLA \r\n";					
				}
				
			} else {
				$ready	= false;
				$this->mMessages	.= "ERROR\tLa TASA_INTERES No debe ser menor  cero \r\n";				
			}
		}
		if($tasa_moratorio <=0){
			if($tasa_moratorio == 0){
				if($PuedeTasaCero == true){
					$ready				= true;
				} else {
					$ready				= false;
					$this->mMessages	.= "ERROR\tLa TASA_INTERES No debe ser menor  cero segun REGLA \r\n";
				}				
			} else {
				$ready	= false;
				$this->mMessages	.= "ERROR\tLa TASA_MORATORIO No debe ser menor  cero \r\n";
			}
		}	
		//
		$arrUpdate = array (
				"estatus_actual" => $EstatusImpuesto,
				"monto_autorizado" => $Monto,
				"pagos_autorizados" => $Pagos,
				"tipo_de_pago" => $FormaDePago,
				"periocidad_de_pago" => $Periocidad,
				"tipo_autorizacion" => $TipoDeAutorizacion,
				
				"fecha_autorizacion" => $FechaDeAutorizacion,
				"docto_autorizacion" => $DocumentoDeAutorizacion,
				"fecha_ministracion" => $FechaDeMinistracionPropuesta,
				"nivel_riesgo" => $NivelDeRiesgo,
				
				
				"saldo_actual" => $SaldoImpuesto,
				"interes_diario" => $InteresImpuesto,
				"fecha_ultimo_mvto" => $FechaOperacionImpuesta,
				
				"plazo_en_dias" => $PlazoEnDias,
				"dias_autorizados" => $PlazoEnDias,
				"fecha_vencimiento" => $FechaDeVencimiento,
				"tasa_interes" => $TasaDeInteres,
				"tasa_moratorio" => $tasa_moratorio,
				"tipo_de_lugar_de_pago" => $LugarDeCobro,
				"tipo_de_dispersion" => $TipoDeDesembolso,
				"fecha_ultimo_capital" => $FechaOperacionImpuesta,
				"fecha_de_proximo_pago" => $FechaDeProximoPago
		);
		
		if($ready == true){
			$ready 		= $this->setUpdate ( $arrUpdate );
			
			$xOrg		= new cCreditosDatosDeOrigen(false, $this->getClaveDeCredito());
			$xRuls->setCodigoDeCredito($this->getClaveDeCredito());
			$xRuls->setEjecutarAlertas($xRuls->reglas()->RN_CREDITOS_AL_AUTORIZAR);
			
			switch($this->getTipoDeOrigen()){
				case $xOrg->ORIGEN_ARRENDAMIENTO:
					$xLeas	= new cCreditosLeasing($this->getClaveDeOrigen());
					if($xLeas->init() == true){
						$xPaso	= new cCreditosProceso();
						$xLeas->setPaso($xPaso->PASO_AUTORIZADO);
						
					}
					break;
			}
			$this->setAddEvento($this->mMessages, 20013);
		} else {
			$this->mMessages	.= "ERROR\tNo se Autoriza el Credito \r\n";
			$this->setAddEvento($this->mMessages, 20012);
		}
		return $ready;
	}
	function setCancelado($razones = "", $fecha = false) {
		$fecha = ($fecha == false) ? fechasys () : $fecha;
		$xRuls = new cReglaDeNegocio ();
		$xRuls->setExecuteActions ( $xRuls->reglas ()->RN_CANCELAR_CREDITO );

		$this->setUpdate ( array (
				"estatus_actual" => CREDITO_ESTADO_CASTIGADO,
				"monto_autorizado" => 0,
				"docto_autorizacion" => $razones,
				"fecha_ultimo_mvto" => $fecha,
				"fecha_autorizacion" => $fecha,
				"pagos_autorizados" => 0,
				"dias_autorizados" => 0,
				"fecha_castigo" => $fecha 
		) );
		$this->mMessages .= "WARN\tCredito Cancelado\r\n";
		$this->setAddEvento($this->mMessages, 20015);
	}
	function setCastigado($razones = "", $fecha = false) {
		
		$fecha = ($fecha == false) ? fechasys () : $fecha;
		$xRuls = new cReglaDeNegocio ();
		$xRuls->setExecuteActions ( $xRuls->reglas ()->RN_CASTIGAR_CREDITO );
		$msg	= "Credito Castigado por : $razones ";
		$xLog	= new cCoreLog();
		$xLog->add($msg);
		$xLog->guardar($xLog->OCat()->CREDITO_CASTIGADO, $this->getClaveDePersona(), $this->getClaveDeCredito());
		
		//Agregar Nota
		
		$xSoc	= new cSocio($this->getClaveDePersona());
		if($xSoc->init() == true){
			$xMem	= new cPersonasMemos();
			
			
			$xSoc->addMemo($xMem->TIPO_CRED_CASTIGO, $msg, $this->getClaveDeCredito());
		}
		
		/*
		 * `creditos_solicitud`.`estatus_actual`,
		 * `creditos_solicitud`.`nivel_riesgo`,
		 * `creditos_solicitud`.`fecha_castigo`,
		 * `creditos_solicitud`.``
		 */
		$this->setUpdate ( array (
				"estatus_actual" => CREDITO_ESTADO_CASTIGADO,
				
				"fecha_castigo" => $fecha,
				"nivel_riesgo" => SYS_RIESGO_ALTO,
				"notas_auditoria" => $razones 
		) );
		$this->mMessages .= $xLog->getMessages();
		
	}

	/**
	 * @deprecated @since 2015.07.01        	
	 */
	function set($NumeroDeCredito, $iniciar = false){ 
		$this->mNumeroCredito 	= setNoMenorQueCero($NumeroDeCredito);
		$this->mIDCacheCredito	= TCREDITOS_REGISTRO . "-" . $this->mNumeroCredito;
		if($iniciar == true){ $this->init (); }
	}
	/**
	 * @deprecated @since 2015.07.01        	
	 */
	function getBaseCalculoMoratorio($FechaInicial, $FechaFinal){	}
	function getClaveDeConvenio(){ return $this->mTipoDeConvenio; }
	function getFechaDeMinistracion(){ return $this->mFechaMinistracion;	}
	function getFechaDePrimerPago(){ return $this->mFechaPrimeraParc;	}
	function getFechaDeProximoPago(){
		$xF		= new cFecha();
		//la fecha debe ser dinamica para Final de Plazo
		$fecha	= $this->mFechaProximaParcialidad;
		if($this->isAFinalDePlazo() == true){
			//Actualizar
		} 
		return $fecha; /*Aplica a creditos 360, es fecha de vencimiento*/ }
	function getPeriocidadDePago(){ return $this->mPeriocidadDePago; }
	/**
	 * @deprecated @since 2018.08.01
	 */
	function getTipoDePago(){ return $this->mTipoDeCuota;	}
	function getTipoDeCuota(){ return $this->mTipoDeCuota;	}
	function getNiVelDeRiesgo(){ return $this->mNivelDeRiesgo; }
	function getClaveDeProducto(){ return $this->mTipoDeConvenio; }
	function getClaveDePersona() { return $this->mNumeroSocio; }
	function getClaveDeEmpresa(){ return $this->mEmpresa; }
	function getClaveDeGrupo(){ return $this->mGrupoAsociado;}
	function getClaveDePeriodoCred(){ return $this->mClaveDePeriodoCredito;}
	function getNumeroDeCredito(){ return $this->mNumeroCredito; }
	function getNumeroDeParcialidad(){ return $this->mParcialidadActual; }
	function getMontoAutorizado(){ return $this->mMontoAutorizado; }
	function getMontoSolicitado(){ return $this->mMontoSolicitado; }
	function getMontoDeParcialidad(){ return $this->mMontoFijoParcialidad; }
	function getFechaDeSolicitud(){ return $this->mFechaDeSolictud; }
	function getFechaDeAutorizacion(){ return $this->mFechaDeAutorizacion; }
	function getFechaDeVencimiento(){ return $this->mFechaVencimiento; }
	function getFechaPrimeraParc(){
		$xF = new cFecha ();
		
		if ($xF->getInt ( $this->mFechaPrimeraParc ) <= $xF->getInt ( $this->mFechaMinistracion )) {
			if ($this->mPeriocidadDePago == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
				$this->mFechaPrimeraParc = $this->mFechaVencimiento; // $xF->setSumarDias($this->mDiasAutorizados)
			} else {
				//
				$xLetra	= new cParcialidadDeCredito();
				if($xLetra->init($this->getClaveDePersona(), $this->getClaveDeCredito(), 1) == true){
					$this->mFechaPrimeraParc = $xLetra->getFechaDePago();
				} else {
					$this->mFechaPrimeraParc = $xF->setSumarDias ( $this->mPeriocidadDePago, $this->mFechaMinistracion );
				}
			}
		}
		return $this->mFechaPrimeraParc;
	} // Dato del Plan de Pago
	function getFechaUltimaParc(){ return $this->mFechaUltimaParc; } // Dato del Plan de Pago
	function getFechaUltimoMvtoCapital(){ return $this->mFechaUltimoMvtoCapital; }
	function getTasaIVA() {
		$xDest			= new cCreditosDestinos($this->mClaveDeDestino);
		$this->mTasaIVA = 0;
		if($xDest->init() == true){
			$this->mTasaIVA = $xDest->getTasaIVA();
		}
		return $this->mTasaIVA;
	}
	function getTasaIVAOtros(){ return TASA_IVA;}
	function getTasaDeAhorro(){ return $this->mTasaAhorro; }
	function getDiasAutorizados(){ return $this->mDiasAutorizados;}
	function getEsVencido(){ return $this->getEstadoActual() == CREDITO_ESTADO_VENCIDO ? true : false; }
	function getTasaDeInteres(){ return $this->mTasaInteres;	}
	function getPagosAutorizados(){return $this->mPagosAutorizados; }
	function getPagosSolicitados(){ return $this->mPagosSolicitados; }
	function getDiasSolicitados(){ return $this->mDiasSolicitados;}
	function getFechaUltimoDePago(){ return $this->mFechaUltimoPago; }
	function getMontoUltimoPago(){ return $this->mMontoUltimoPago; }
	function getMontoDispuesto(){ return $this->mTotalDispuesto; }
	function getTipoDeLugarDeCobro(){ return $this->mTipoLugarDeCobro; }
	function getTipoDeDispersion(){ return $this->mTipoDeDispersion; }
	function getFechaDevencimientoLegal(){ return $this->mFechaVencimientoLegal; }
	
	function getFormaDePago(){ return $this->mTipoDePago; }
	
	function getSucursal(){ return $this->mSucursal; }
	function getEstadoActual(){ return $this->mEstatusActual; }
	function getInteresNormalPagado(){ return setNoMenorQueCero ( $this->mInteresNormalPagado ); }
	function getInteresNormalDevengado(){ return setNoMenorQueCero ( $this->mInteresNormalDevengado );}
	function getInteresMoratorioPagado() { return setNoMenorQueCero ( $this->mInteresMoratorioPag ); }
	function getInteresMoratorioDev(){ return setNoMenorQueCero ( $this->mInteresMoratorioDev ); }
	function getInteresDiariogenerado(){ return $this->mInteresDiario; }
	function getClaveDeDestino(){ return $this->mClaveDeDestino; }
	function getDescripcionDestino(){ return $this->mDescribeDestino; }
	function getClaveDeUsuario(){ return $this->mIDUsuario; }
	function getPeriodoActual() {
		return $this->mParcialidadActual;
	}
	function getTasaDeMora(){return $this->mTasaMoratorio;}
	function getTipoDeCalculoDeInteres(){ return $this->mTipoDeCalculoDeInteres; }
	function getTipoDeBaseDeInteres(){ return $this->mTipoDeCalculoDeInteres; }
	function getTipoDeDiasDePago(){ return $this->mTipoDeDiasDePago; }
	function getTipoDeCuotaDePago(){ return $this->mTipoDeDiasDePago; }
	function getTipoDeAutorizacion(){ return $this->mTipoDeAutorizacion; }
	function getPathDelContrato() {
		return $this->mContrato_URL . $this->getNumeroDeCredito ();
	}
	function setForceVista($vista = false) {
		$this->mForceVista = $vista;
	}
	function setRazonRechazo($razones = "", $notas = "", $fecha = false, $idmotivo = false) {
		$xF			= new cFecha();
		
		$res		= false;		
		$idc 		= $this->mNumeroCredito;
		$idmotivo	= setNoMenorQueCero($idmotivo);
		$razones	= setCadenaVal($razones,80);
		$notas		= setCadenaVal($notas,80);
		$fecha 		= $xF->getFechaISO($fecha);
		
		
		
		if($idc > DEFAULT_CREDITO){
			//Cancelar
			$this->setCancelado($razones, $fecha);
			//Actualizar los demas motivos
			$xQL	= new MQL();
			$xQL->setRawQuery("UPDATE `creditos_rechazados` SET `estatusactivo`=0 WHERE `numero_de_credito`=$idc");
			
			//Agregar nuevo motivo
			$v 		= new cCreditos_rechazados();
			$v->numero_de_credito( $idc );
			$v->fecha_de_rechazo( $fecha );
			$v->razones( $razones );
			$v->notas( $notas );
			$v->claverechazo($idmotivo);
			$v->idusuario(getUsuarioActual());
			$v->tiempo(time());
			$v->estatusactivo(SYS_UNO);
			$lid 	= $v->query()->getLastID ();
			$v->idcreditos_rechazados( $lid );
			
			$res	= $v->query()->insert()->save();
			$this->setAddEvento("Se Agrega Razones de Rechazo $razones", 20014);
		}
		
		return ($res === false) ? false : true;
	}
	function setCambioProducto($producto, $tasa = false, $mora = false, $destino = false) {
		$xLog		= new cCoreLog();
		$xRuls		= new cReglaDeNegocio();
		$PuedeTasa0	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PUEDEN_TASA_CERO);
		$tasa 		= setNoMenorQueCero($tasa);
		
		$tasa 		= ($tasa >= 100) ? ($tasa / 100) : $tasa;
		if($PuedeTasa0 == false){
			$tasa	= ($tasa <= 0) ? $this->getTasaDeInteres() : $tasa;
		}
		$mora 		= setNoMenorQueCero ( $mora );
		$mora 		= ($mora >= 100) ? ($mora / 100) : $mora;
		$mora 		= ($mora <= 0) ? $this->getTasaDeMora() : $mora;
		
		$tasa		= round($tasa, 4);
		$mora		= round($mora, 4);
		
		$destino 	= setNoMenorQueCero ( $destino );
		$destino 	= ($destino <= 0) ? $this->getClaveDeDestino() : $destino;
		$xProd		= new cProductoDeCredito($producto);
		$BaseCalc	= $this->obj()->tipo_de_calculo_de_interes()->v(); //Historico base saldos insolutos
		if($xProd->init() == true){
			$BaseCalc	= $xProd->getTipoDeBaseCalc();
		}
		$res		= $this->setUpdate ( array (
				$this->obj()->tasa_interes()->get() => $tasa,
				$this->obj()->tasa_moratorio()->get() => $mora,
				$this->obj()->tipo_convenio()->get() => $producto,
				$this->obj()->destino_credito()->get() => $destino,
				$this->obj()->tipo_de_calculo_de_interes()->get() => $BaseCalc
		) );
		$res		= ($res == false) ? false : true;
		if($res == true){
			
			$xLog->add("WARN\tSe han cambiado los parametros Tasa $tasa Mora $mora Producto $producto Destino $destino\r\n");
			if (($tasa != $this->getTasaDeInteres()) OR $mora != ($this->getTasaDeMora()) ) {
				$t2		= $this->getTasaDeInteres();
				$t3		= $this->getTasaDeMora();
				$idPlan = $this->getNumeroDePlanDePagos();
				$xLog->add("WARN\tEliminar el Plan de Pagos $idPlan por que la Tasa Normal ($tasa - $t2) o la Tasa de mora ($mora -  $t3) Varian\r\n");
				// eliminar plan de pagos
				if ($idPlan > 0) {
					$xPlan = new cReciboDeOperacion ( false, true, $idPlan );
					$xPlan->init();
					$xPlan->setRevertir( true );
					$xLog->add($xPlan->getMessages(), $xLog->DEVELOPER);
				}
				$xLog->add($this->setReestructurarIntereses(), $xLog->DEVELOPER);
				
			} else {
				$xLog->add( "WARN\tSin cambios en Tasas.- Anterior " . $this->getTasaDeInteres() . " Mora : " . $this->getTasaDeMora() . "\r\n");
			}
			$this->init();
		}
		$this->mMessages	.= $xLog->getMessages();
		$xLog->guardar($xLog->OCat()->CREDITO_MODIFICADO, $this->getClaveDePersona());
		return $xLog->getMessages();
	}
	function setPeriodoActual($periodo) {
		$this->setUpdate ( array (
				"ultimo_periodo_afectado" => $periodo 
		) );
		$this->mMessages .= "WARN\tCredito Actualizado a la Letra $periodo\r\n";
		$this->setAddEvento($this->mMessages, 1051);
	}
	function setCambiarPeriocidad($NuevaPeriocidad, $nuevosPagos = false, $formaPago = false, $fecha = false) {
		$pagos 		= $this->getPagosAutorizados ();
		$periocidad = $this->getPeriocidadDePago ();
		$dias 		= $this->getDiasAutorizados ();
		$formaPago 	= ($formaPago == false) ? $this->getFormaDePago () : $formaPago;
		$pagoActual = $this->getPeriodoActual ();
		
		$cT 		= new cTipos();
		$xF 		= new cFecha();
		$xLog		= new cCoreLog();
		$success 	= true;
		$nuevosDias = ($nuevosPagos == false) ? $dias : $NuevaPeriocidad * $nuevosPagos;
		$nuevosPagos = ($nuevosPagos == false) ? $cT->cInt ( ($dias / $NuevaPeriocidad) ) : $nuevosPagos;
		
		// cuadrar periodo actual
		if ($NuevaPeriocidad > $periocidad) {
			// 15 > 7 ? 15/7 :: 7 24/1
			$factoria = ($NuevaPeriocidad / $periocidad);
			$pagoActual = $cT->cInt ( $pagoActual / $factoria );
		}
		if ($NuevaPeriocidad < $periocidad) {
			// 30 > 15 :: 30/5 = 2
			$factoria = ($periocidad / $NuevaPeriocidad);
			$pagoActual = $cT->cInt ( $pagoActual * $factoria );
		}
		$xConv = new cProductoDeCredito ( $this->getClaveDeProducto () );
		$DConv = $xConv->obj ();
		
		if ($NuevaPeriocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
			$nuevosPagos = SYS_UNO;
			$formaPago = CREDITO_TIPO_PAGO_UNICO;
			if ($fecha != false) {
				$nuevosDias = $xF->setRestarFechas ( $fecha, $this->getFechaDeMinistracion () );
			} else {
				$nuevosDias = $this->getDiasAutorizados ();
			}
		}
		if ($nuevosDias > $DConv->dias_maximo ()->v ()) {
			// sucees false
			$success = false;
			$xLog->add("ERROR\Periocidad Limitada a Maximo de dias \r\n");
		}
		$fechaVencmiento = $xF->setSumarDias ( $nuevosDias, $this->getFechaDeMinistracion () );
		if ($success == true) {
			if ($this->isAFinalDePlazo () == false) {
				// eliminar plan de pagos
				$idPlan = $this->getNumeroDePlanDePagos ();
				if ($idPlan > 0) {
					$xPlan = new cReciboDeOperacion ( false, true, $idPlan );
					$xPlan->init ();
					$xPlan->setRevertir ( true );
					$xLog->add($xPlan->getMessages(), $xLog->DEVELOPER);
				}
			}
			// actualizar credito
			$this->setUpdate ( array (
					$this->obj()->dias_autorizados ()->get () => $nuevosDias,
					$this->obj()->plazo_en_dias ()->get () => $nuevosDias,
					$this->obj()->periocidad_de_pago ()->get () => $NuevaPeriocidad,
					$this->obj()->pagos_autorizados ()->get () => $nuevosPagos,
					$this->obj()->fecha_vencimiento ()->get () => $fechaVencmiento,
					$this->obj()->fecha_vencimiento_dinamico ()->get () => $fechaVencmiento,
					$this->obj()->tipo_de_pago ()->get () => $formaPago,
					$this->obj()->ultimo_periodo_afectado ()->get () => $pagoActual 
			));
			$xLog->add("WARN\tCredito " . $this->getClaveDeCredito() . " Modificado en Dias Autorizados($nuevosDias), Plazo en Dias($nuevosDias),Periocidad($NuevaPeriocidad), pagos($nuevosPagos),Vencimiento($fechaVencmiento),Tipo de Pago($formaPago),Pago Actual($pagoActual)\r\n");
			$xLog->add("WARN\tSe reestructura Intereses, Elimina Plan, etc. \r\n");
			$this->init ();
			$xLog->add($this->setReestructurarIntereses(), $xLog->DEVELOPER);
		}
		$this->mMessages	.= $xLog->getMessages();
		$xLog->guardar($xLog->OCat()->CREDITO_MODIFICADO, $this->getClaveDePersona(), $this->getClaveDeCredito());
		return $xLog->getMessages();
	}
	function setCambiarMontoAutorizado($monto, $force = false) {
		$msg 		= "";
		$success 	= true;
		if ($this->getEstadoActual() !== CREDITO_ESTADO_AUTORIZADO) {
			$msg 	.= "ERROR\tNo se puede cambiar un credito con Estado diferente, Use Cambiar Monto Ministrado\r\n";
			$success = false;
		}
		$success 	= ($force == true) ? true : $success;
		if ($success == true) {
			$arr	= array ("monto_autorizado" => $monto);
			if($this->getEsArrendamientoPuro() == true){
				$arr["monto_solicitado"]		= $monto;
			}
			$this->setUpdate( $arr );
			
			$msg 	.= "SUCCESS\tCambio de Monto Autorizado aplicado a $monto\r\n";
		}
		//$xLog	= new cCoreLog(); $xLog->add($msg); $xLog->guardar($xLog->OCat()->CREDITO_MODIFICADO, $this->getClaveDePersona(), $this->getClaveDeCredito());
		$this->setAddEvento($msg, 1051);
		return $msg;
	}
	function setCambiarMontoSolicitado($monto, $force = false) {
		$msg 		= "";
		$success 	= true;
		if ($this->getEstadoActual() !== CREDITO_ESTADO_SOLICITADO) {
			$msg 	.= "ERROR\tNo se puede cambiar un credito con Estado diferente, Use Cambiar Monto Ministrado\r\n";
			$success = false;
		}
		$success 	= ($force == true) ? true : $success;
		if ($success == true) {
			$arr	= array ("monto_solicitado" => $monto);
			$this->setUpdate( $arr );
			
			$msg 	.= "SUCCESS\tCambio de Monto Solicitado aplicado a $monto\r\n";
		}
		$this->setAddEvento($msg, 1051);
		return $msg;
	}
	function setCambiarMontoMinistrado($monto, $force = false) {
		$monto		= setNoMenorQueCero($monto,2);
		$solicitud 	= $this->mNumeroCredito;
		// no permitir si el total de abonos es mayos
		$abonos 	= $this->mMontoAutorizado - $this->mSdoCapitalInsoluto;
		$sucess 	= true;
		$xLog		= new cCoreLog();
		$saldo 		= ($this->getMontoAutorizado()  == $this->getSaldoActual()) ? $monto : $this->getSaldoActual();
		$xT			= new cCreditos_solicitud();
		
		if ($abonos > $monto) {
			$xLog->add("ERROR\tEl nuevo Monto no debe ser menor a $abonos, usted intenta agregar $monto\r\n");
			$sucess = false;
		}
		if ($sucess == true) {
			$xLog->add("WARN\tSe Cambia Monto Ministrado($monto) del Credito ". $this->getClaveDeCredito() . ", se elimina plan de pagos, reestructura Intereses y cambia recibo de pago \r\n");
			$this->mMontoAutorizado 	= $monto;
			if ($this->mMontoSolicitado < $monto) {
				$this->mMontoSolicitado = $monto;
				$xLog->add("WARN\tSe Cambia Monto Solicitado a $monto\r\n");
			}
			if ($force == true) {
				$this->mMontoSolicitado = $monto;
				$this->mMontoAutorizado = $monto;
				$saldo = $monto;
				$xLog->add("WARN\tMontos Forzados $monto\r\n");
			}

			$tasa = $this->getTasaDeInteres ();
			
			$fecha_corte = fechasys();
			// actualizar credito
			/*$mNArr		= array (
					$this->obj()->tasa_interes()->get() => $tasa,
					$this->obj()->ultimo_periodo_afectado()->get() => 0,
					$this->obj()->interes_diario()->get() => 0,
					$this->obj()->monto_solicitado()->get() => $this->mMontoSolicitado,
					$this->obj()->monto_autorizado()->get() => $this->mMontoAutorizado,
					$this->obj()->saldo_actual()->get() => $saldo,
					$this->obj()->saldo_conciliado()->get() => $saldo
			);*/
			
			$mNArr		= array (
					$xT->TASA_INTERES 		=> $tasa,
					$xT->ULTIMO_PERIODO_AFECTADO => 0,
					$xT->INTERES_DIARIO 	=> 0,
					$xT->MONTO_SOLICITADO 	=> $this->mMontoSolicitado,
					$xT->MONTO_AUTORIZADO 	=> $this->mMontoAutorizado,
					$xT->SALDO_ACTUAL 		=> $saldo,
					$xT->SALDO_CONCILIADO 	=> $saldo
			);
			
			$this->setUpdate ( $mNArr );
			// Cambiar monto del Recibo
			$recMin = $this->getNumeroReciboDeMinistracion ();
			if ($recMin > 0) {
				$xRec = new cReciboDeOperacion( false, true, $recMin );
				$xRec->init();
				$xRec->setTotalPorProrrateo( $monto );
				$xLog->add($xRec->getMessages(), $xLog->DEVELOPER);
			}
			// eliminar plan de pagos
			$idPlan = $this->getNumeroDePlanDePagos ();
			if ($idPlan > 0) {
				$xPlan = new cReciboDeOperacion ( false, true, $idPlan );
				$xPlan->init();
				$xPlan->setRevertir ( true );
				$xLog->add($xPlan->getMessages(), $xLog->DEVELOPER);
				$xLog->add("WARN\tEl Plan de Pagos Codigo $idPlan se elimino\r\n");
			}			
			// reestructurar SDPM
			$xLog->add($this->setReestructurarIntereses(), $xLog->DEVELOPER);
			
			$this->setDetermineDatosDeEstatus( $fecha_corte );
			
			$this->init();
		}
		$this->setAddEvento($xLog->getMessages(), 1051);
		$this->mMessages	.= $xLog->getMessages();
		return $xLog->getMessages();
	}
	function setCambiarTasaNormal($tasa) {
		$solicitud = $this->mNumeroCredito;
		$xLog		= new cCoreLog();
		// eliminar plan de pagos
		$idPlan = $this->getNumeroDePlanDePagos ();
		if ($idPlan > 0) {
			$xPlan = new cReciboDeOperacion ( false, true, $idPlan );
			$xPlan->init ();
			$xPlan->setRevertir ( true );
		}
		$fecha_corte = fechasys();
		$xLog->add("WARN\tSe Cambia Tasa de Interes Normal($tasa) del Credito ". $this->getClaveDeCredito() . ", se elimina plan de pagos, reesctructura Intereses y cambia ultimo pago efectuado\r\n");
		// actualizar credito
		$this->setUpdate ( array (
				$this->obj()->tasa_interes ()->get () => $tasa,
				$this->obj()->ultimo_periodo_afectado()->get () => 0 
		) );
		// reestructurar SDPM
		$xLog->add($this->setReestructurarIntereses(), $xLog->DEVELOPER);
		$this->setDetermineDatosDeEstatus ( $fecha_corte );
		$this->init ();
		$xLog->guardar($xLog->OCat()->CREDITO_MODIFICADO, $this->getClaveDePersona());
		return $xLog->getMessages();
	}
	function setCambiarFechaMinistracion($fecha, $soloCambiar = false) {
		$xF 		= new cFecha ( 0 );
		$xQL		= new MQL();
		$xLog		= new cCoreLog();
		
		$fecha 		= $xF->getFechaISO ( $fecha );
		$solicitud 	= $this->mNumeroCredito;
		if($fecha == $this->getFechaDeMinistracion()){ $soloCambiar	= true; }
		
		if ($soloCambiar == true) {
			$this->setUpdate ( array (
					"fecha_ministracion" => $fecha 
			), true );
			$xLog->add( "WARN\tSolo se cambia la fecha a $fecha\r\n");
		} else {
			$xLog->add("Se cambia la Fecha de Ministracion del Credito " . $this->getClaveDeCredito() . " a la Fecha $fecha, se elimina plan de pagos, se cambia recibo, se cambia de fecha de ultimo Movimiento, Autorizacion y Solicitud, se reestructuran Intereses\r\n");
			$reciboMin 	= $this->getNumeroReciboDeMinistracion ();
			if ($reciboMin > 0) {
				$xRec 	= new cReciboDeOperacion ( false, true, $reciboMin );
				$xRec->init();
				$xRec->setFecha ( $fecha, true );
			}
			// eliminar plan de pagos
			$idPlan = $this->getNumeroDePlanDePagos ();
			if ($idPlan > 0) {
				$xPlan 			= new cPlanDePagos ( $idPlan );
				$xPlan->setEliminar ();
				$this->mMessages .= $xPlan->getMessages ();
			}
			// cambiar fecha ministracion
			if ($xF->getInt ( $this->mFechaDeAutorizacion ) > $xF->getInt ( $fecha )) {
				$this->mFechaDeAutorizacion = $fecha;
			}
			if ($xF->getInt ( $this->mFechaDeSolictud ) > $xF->getInt ( $this->mFechaDeAutorizacion )) {
				$this->mFechaDeSolictud = $this->mFechaDeAutorizacion;
			}
			if ($xF->getInt ( $this->mFechaUltimoMvtoCapital ) > $xF->getInt ( $fecha )) {
				$this->mFechaUltimoMvtoCapital = $fecha;
			}
			// Modificar operaciones de pago con fecha Menor a la fecha de Ministracion
			$xQL->setRawQuery( "UPDATE operaciones_mvtos SET fecha_afectacion='$fecha', fecha_operacion='$fecha' WHERE docto_afectado=$solicitud AND fecha_operacion < '$fecha' " );
			$xQL->setRawQuery( "UPDATE operaciones_recibos SET fecha_operacion='$fecha' WHERE docto_afectado=$solicitud AND fecha_operacion < '$fecha'" );
			//
			$fecha_corte = fechasys ();
			
			// actualizar credito
			$this->setUpdate ( array (
					"fecha_ministracion" => $fecha,
					"ultimo_periodo_afectado" => 0,
					"fecha_autorizacion" => $this->mFechaDeAutorizacion,
					"fecha_solicitud" => $this->mFechaDeSolictud,
					"fecha_ultimo_mvto" => $this->mFechaUltimoMvtoCapital 
			) );
			// reestructurar SDPM
			$xLog->add( $this->setReestructurarIntereses(), $xLog->DEVELOPER);
			$this->setDetermineDatosDeEstatus ( $fecha_corte );
		}
		$xLog->guardar($xLog->OCat()->CREDITO_MODIFICADO, $this->getClaveDePersona());
		return $xLog->getMessages();
	}
	function setCambiarFechaDeVencimiento($fecha) {
		$xF = new cFecha ();
		$xPer = new cPeriocidadDePago ( $this->getPeriocidadDePago () );
		$xPer->init ();
		$xPdto = $this->getOProductoDeCredito ();
		$dias_tolerados_del_plan = $xPer->getDiasToleradosEnVencer ();
		$dias_tolerados_del_pdto = $xPdto->getDiasTolerados ();
		$total_dias_tolerados = ($dias_tolerados_del_pdto + $dias_tolerados_del_plan);
		$this->mMessages .= "WARN\tDias tolerados del Producto $dias_tolerados_del_pdto, Dias Tolerados de la periocidad $dias_tolerados_del_plan \r\n";
		$sucess = false;
		if ($xF->getInt ( $fecha ) > $xF->getInt ( $this->getFechaDeVencimiento () )) {
			$nuevos_dias = $xF->setRestarFechas ( $fecha, $this->getFechaDeMinistracion () );
			$nuevos_pagos = $this->getPagosAutorizados ();
			ceil ( ($nuevos_dias / $this->getPagosAutorizados ()) );
			if ($this->getPeriocidadDePago () != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
				$nuevos_pagos = ceil ( ($nuevos_dias / $this->getPagosAutorizados ()) );
			}
			
			$nuevos_pagos = ($nuevos_pagos > $this->getPagosAutorizados ()) ? $nuevos_pagos : $this->getPagosAutorizados ();
			$fecha_de_vencimiento = $fecha;
			$fecha_de_vencimiento_dinamico = $xF->setSumarDias ( $total_dias_tolerados, $fecha );
			$fecha_de_mora = $xF->setSumarDias ( $dias_tolerados_del_pdto, $fecha );
			$this->mMessages .= "WARN\tNuevos dias $nuevos_dias, Nuevo pagos $nuevos_pagos, vencimiento $fecha_de_vencimiento, Venc dinamico $fecha_de_vencimiento_dinamico, Mora $fecha_de_mora \r\n";
			$sucess = true;
			$this->setUpdate ( array (
					"dias_autorizados" => $nuevos_dias,
					"pagos_autorizados" => $nuevos_pagos,
					
					"fecha_vencimiento" => $fecha_de_vencimiento,
					"fecha_mora" => $fecha_de_mora,
					"fecha_vencimiento_dinamico" => $fecha_de_vencimiento_dinamico 
			) );
		} else {
			$this->mMessages .= "ERROR\tLa fecha no puede ser cambiada a uno menor ($fecha) \r\n";
		}
		$this->setAddEvento($this->mMessages, 1051);
		return $sucess;
	}
	function setCambiarEstadoActual($nuevoEstado, $fecha) {
		$estado 		= $this->getEstadoActual ();
		$success 		= true;
		$msg 			= "";
		$arrUpdate 		= array ();
		$eliminarOps 	= false;
		$xLog			= new cCoreLog();
		$this->setCambiarFechaDeVencimiento ( $fecha );
		
		if ($this->getMontoAutorizado() != $this->getSaldoActual ()) {
			$success = false;
			$xLog->add("OK\tMOVIMIENTOS\tEl Credito no puede modificado con movimientos\r\n");
		}
		
		if (($nuevoEstado == CREDITO_ESTADO_VIGENTE) and ($estado == CREDITO_ESTADO_SOLICITADO) or ($estado == CREDITO_ESTADO_AUTORIZADO)) {
			$success = false;
			$xLog->add("ERROR\tINVALIDOVIG\tEl Credito no puede Ministrarse en este procedimiento\r\n");
		}
		if ((($nuevoEstado == CREDITO_ESTADO_SOLICITADO) or ($nuevoEstado == CREDITO_ESTADO_AUTORIZADO)) and (($this->getMontoAutorizado () != $this->getSaldoActual ()) or $estado != CREDITO_ESTADO_VIGENTE)) {
			
			if($this->getEsRechazado() == false){
				$success = false;
				$xLog->add("ERROR\tCONMVTO\tEl Credito no se modifica si ya tiene Cambios (N - $nuevoEstado A - $estado)\r\n");
			}
		}
		if ($estado == $nuevoEstado) {
			$success = false;
			$xLog->add("WARN\tSINCAMBIOS\tEl Credito no tiene cambios\r\n");
		}
		if ($estado >= CREDITO_ESTADO_VIGENTE and $nuevoEstado < CREDITO_ESTADO_AUTORIZADO) { // si es igual a vigente y el estado nuevo es vencido, castigado, moroso, etc
			$eliminarOps 	= false;
			$success 		= true;
			$xLog->add("WARN\tCAMBIOS\tEl Credito pasa de $estado a $nuevoEstado\r\n");
		}
		
		if ($success == true) {
			if ($nuevoEstado == CREDITO_ESTADO_SOLICITADO) {
				$arrUpdate[$this->obj()->MONTO_AUTORIZADO] 		= 0;
				$arrUpdate[$this->obj()->DIAS_AUTORIZADOS] 		= 0;
				$arrUpdate[$this->obj()->DOCTO_AUTORIZACION] 	= "";
				if($this->getEsRechazado() == true){
					$xQL	= new MQL();
					$xQL->setRawQuery("UPDATE `creditos_rechazados` SET `estatusactivo`=0 WHERE `numero_de_credito`='" . $this->getClaveDeCredito() . "'");
					$xLog->add("WARN\tCAMBIOS\tSe revierte el Rechazo\r\n");
				}
			}
			// Si el Nuevo estado es Solicitado y el Anterior era diferente
			if (($nuevoEstado == CREDITO_ESTADO_SOLICITADO or $nuevoEstado == CREDITO_ESTADO_AUTORIZADO) and ($estado != CREDITO_ESTADO_SOLICITADO or $estado != CREDITO_ESTADO_AUTORIZADO)) {
				
				$idRP = $this->getNumeroReciboDeMinistracion ();
				if ($idRP > 0) {
					$xRP = new cReciboDeOperacion ( false, true, $idRP );
					$xRP->init ();
					$xRP->setRevertir ( true );
					$xLog->add("WARN\tMinistracion\tSe cancela el Recibo $idRP de Ministracion\r\n");
					$xLog->add($xRP->getMessages(), $xLog->DEVELOPER);
					
				}
				$idPlan = $this->getNumeroDePlanDePagos ();
				if ($idPlan > 0) {
					$xPlan 				= new cReciboDeOperacion ( false, true, $idPlan );
					$xPlan->init();
					$xPlan->setRevertir( true );
					$xLog->add("WARN\tPlanDePago\tSe cancela el Plan de Pago $idPlan\r\n");
					$xLog->add($xPlan->getMessages(), $xLog->DEVELOPER);
				}
				$arrUpdate [$this->obj()->SALDO_ACTUAL] 		= 0;
				$arrUpdate [$this->obj()->FECHA_MINISTRACION] 	= $this->getFechaDeAutorizacion ();
				$arrUpdate [$this->obj()->FECHA_ULTIMO_MVTO] 	= $this->getFechaDeAutorizacion ();
			}
			$arrUpdate [$this->obj()->ESTATUS_ACTUAL] 			= $nuevoEstado;
			// actualizar credito
			$this->setUpdate ( $arrUpdate );
		}
		$this->setAddEvento($xLog->getMessages(), 20020);
		$this->mMessages .= $xLog->getMessages();
		return $msg;
	}
	function setCambiarOficialSeg($oficial) {
		$oficial 	= setNoMenorQueCero ( $oficial );
		$ready 		= false;
		if($oficial > 0){
			$xT			= new cCreditos_solicitud();
			$ready		= $this->setUpdate(array(
					$xT->OFICIAL_SEGUIMIENTO => $oficial
			));
			$xT		= null;
			$this->setAddEvento("Se actualiza Oficial de Seguimiento $oficial",1053);
		}
		
		return ($ready == false) ? false : true;
	}
	function setCambiarOficialCred($oficial) {
		$oficial 	= setNoMenorQueCero ( $oficial );
		$ready 		= false;
		if($oficial > 0){
			$xT			= new cCreditos_solicitud();
			$ready		= $this->setUpdate(array(
					$xT->OFICIAL_CREDITO => $oficial
			));
			$xT		= null;
			$this->setAddEvento("Se actualiza Oficial de Credito $oficial",1053);
		}
		
		return ($ready == false) ? false : true;
	}
	private function setAddEvento($mensaje, $clave){
		setAgregarEvento_($mensaje, $clave, $this->getClaveDePersona(), $this->getClaveDeCredito());
	}
	function getNumeroReciboDeMinistracion() {
		$sql = "SELECT idoperaciones_recibos FROM operaciones_recibos WHERE tipo_docto = " . RECIBOS_TIPO_MINISTRACION . " AND docto_afectado= " . $this->mNumeroCredito . " LIMIT 0,1";
		$numero = mifila ( $sql, "idoperaciones_recibos" );
		return $numero;
	}
	function setClonar($saldo = false, $autorizado = false) {
		$saldo 			= ($saldo == false) ? $this->getSaldoActual () : $saldo;
		$autorizado 	= ($autorizado == false) ? $this->getMontoAutorizado () : $autorizado;
		// retorna numero de credito
		$xS 			= new cSocio ( $this->getClaveDePersona () ); $xS->init();
		$nuevoID 		= $xS->getIDNuevoDocto ( iDE_CREDITO );
		$xCD 			= new cCreditos_solicitud ();
		$xCD->numero_solicitud ( $nuevoID );
		$xCD->numero_socio ( $this->getClaveDePersona () );
		$xCD->causa_de_mora ( $this->mCausaDeMora );
		$xCD->contrato_corriente_relacionado ( $this->mContratoCorriente );
		$xCD->descripcion_aplicacion ( $this->mDescripcionDestino );
		$xCD->destino_credito ( $this->mTipoDeDestino );
		$xCD->dias_autorizados ( $this->mDiasAutorizados );
		$nota	= "$nuevoID .- CLONADO DEL CREDITO " . $this->mNumeroCredito;
		$this->mMessages	.= $nota. "\r\n";
		$xCD->docto_autorizacion ( $nota );
		$xCD->eacp ( EACP_CLAVE );
		$xCD->estatus_actual ( $this->getEstadoActual () );
		//$xCD->estatus_de_negociacion( $xCD->estatus_de_negociacion()->v() );
		$xCD->fecha_autorizacion ( $this->mFechaDeAutorizacion );
		$xCD->fecha_castigo ( "2018-01-01" );
		$xCD->fecha_conciliada ( fechasys () );
		$xCD->fecha_ministracion ( $this->mFechaMinistracion );
		$xCD->fecha_mora ( $this->mFechaMora );
		//$xCD->fecha_revision ( fechasys () );
		$xCD->fecha_solicitud ( $this->mFechaDeSolictud );
		$xCD->fecha_ultimo_mvto ( $this->mFechaUltimoMvtoCapital );
		$xCD->fecha_vencimiento ( $this->mFechaVencimiento );
		$xCD->fecha_vencimiento_dinamico ( $this->mFechaVencimientoLegal );
		$xCD->persona_asociada( $this->obj()->persona_asociada()->v() );
		$xCD->grupo_asociado ( $this->mGrupoAsociado );
		$xCD->idusuario ( getUsuarioActual () );
		$xCD->interes_diario ( $this->mInteresDiario );
		$xCD->interes_moratorio_devengado ( $this->mInteresMoratorioDev );
		$xCD->interes_moratorio_pagado ( $this->mInteresMoratorioPag );
		$xCD->interes_normal_devengado ( $this->mInteresNormalDevengado );
		$xCD->interes_normal_pagado ( $this->mInteresNormalPagado );
		$xCD->monto_autorizado ( $autorizado );
		$xCD->monto_parcialidad ( $this->mMontoFijoParcialidad );
		$xCD->monto_solicitado ( $autorizado ); // igual que el autorizado
		$xCD->numero_pagos ( $this->getPagosAutorizados () );
		$xCD->saldo_actual ( $saldo );
		$xCD->saldo_conciliado ( $saldo );
		$xCD->saldo_vencido ( 0 );
		$xCD->sdo_int_ant ( 0 );
		$xCD->sucursal ( getSucursal () );
		$xCD->pagos_autorizados ( $this->getPagosAutorizados () );
		$xCD->periocidad_de_pago ( $this->getPeriocidadDePago () );
		$xCD->periodo_notificacion ( 0 );
		$xCD->periodo_solicitudes ( EACP_PER_SOLICITUDES );
		$xCD->plazo_en_dias ( $this->mDiasAutorizados );
		$xCD->tipo_credito ( $this->mModalidadDeCredito );
		$xCD->tipo_autorizacion ( $this->mTipoDeAutorizacion );
		$xCD->tasa_ahorro ( $this->mTasaAhorro );
		$xCD->tasa_interes ( $this->mTasaInteres );
		$xCD->tasa_moratorio ( $this->mTasaMoratorio );
		$xCD->tipo_convenio ( $this->mTipoDeConvenio );
		$xCD->tipo_de_calculo_de_interes ( $this->mTipoDeCalculoDeInteres );
		$xCD->tipo_de_pago ( $this->mTipoDeCuota );
		$xCD->ultimo_periodo_afectado ( $this->mParcialidadActual );
		//New 
		$xCD->perfil_de_intereses( $this->obj()->perfil_de_intereses()->v() );
		$xCD->fuente_de_fondeo( $xCD->fuente_de_fondeo()->v() );
		$xCD->operacion_origen( $this->obj()->operacion_origen()->v() );
		$xCD->tipo_de_origen( $this->obj()->tipo_de_origen()->v() );
		$xCD->tipo_de_dias_de_pago( $this->obj()->tipo_de_dias_de_pago()->v() );
		$xCD->tipo_de_lugar_de_pago( $this->obj()->tipo_de_lugar_de_pago()->v() );
		$xCD->tipo_de_dispersion($this->obj()->tipo_de_dispersion()->v());
		$xCD->omitir_seguimiento( $this->obj()->omitir_seguimiento()->v() );
		$xCD->tasa_cat( $this->obj()->tasa_cat()->v() );
		$xCD->fecha_de_primer_pago( $this->obj()->fecha_de_primer_pago()->v() );
		$xCD->fecha_de_proximo_pago( $this->obj()->fecha_de_proximo_pago()->v() );
		$xCD->fecha_ultimo_capital($this->obj()->fecha_ultimo_capital()->v() );		
		$xCD->recibo_ultimo_capital($this->obj()->recibo_ultimo_capital()->v());
		$xCD->query()->insert ()->save ();
		
		return $nuevoID;
	}
	function getInteresNormalPorPagar($fecha = false, $parcialidad = false){
		$parcialidad	= setNoMenorQueCero($parcialidad);
		$interes		= 0;
		if($parcialidad > 0){
			
		}
		$DInt 			= $this->getInteresDevengado ( $fecha );
		$interes		= $this->mInteresNormalDevengado - $this->mInteresNormalPagado + $DInt [SYS_INTERES_NORMAL];
		
		return $interes;
	}
	function getEsAfectable() {
		$afectable = true;
		if ($this->getEstadoActual () == CREDITO_ESTADO_AUTORIZADO or $this->getEstadoActual () == CREDITO_ESTADO_SOLICITADO) {
			$afectable = false;
		}
		if($this->getEsRechazado() == true){
			$afectable = false;
		}
		return $afectable;
	}

	function obj(){
		if($this->mCreditoInicializado == false){$this->init();}
		if ($this->mOB == null) {
			$this->mOB = new cCreditos_solicitud ();
			$data		= $this->getDatosInArray();
			$this->mOB->setData($data);
		}
		return $this->mOB;
	}
	function getOProductoDeCredito($TipoDeConvenio = false) {
		$TipoDeConvenio = setNoMenorQueCero ( $TipoDeConvenio );
		$TipoDeConvenio = ($TipoDeConvenio <= 0) ? $this->getClaveDeProducto () : $TipoDeConvenio;
		if($this->mOProducto === null){
			$this->mOProducto = new cProductoDeCredito( $TipoDeConvenio );
			if($this->mOProducto->init() == false){
				//$this->mMessages .= $this->mOProducto->getMessages ();
				//setLog("Error Producto $TipoDeConvenio");
			}
		}
		return $this->mOProducto;
	}
	function getOPagos(){
		if($this->mOPagos == null){
			$this->mOPagos	= new cCreditosOperaciones($this->mNumeroCredito);
			$this->mOPagos->init();
		}
		return $this->mOPagos;
	}
	function getORecibo(){ return $this->mObjRec;	}
	function getOPeriocidad(){
		if ($this->mOPeriocidad == null) {
			$this->mOPeriocidad = new cPeriocidadDePago ( $this->getPeriocidadDePago () );
			$this->mOPeriocidad->init();
		}
		return $this->mOPeriocidad;
	}
	private function getOIntereses(){
		if($this->mOIntereses == null){
			$this->mOIntereses	= new cCreditosMontos($this->mNumeroCredito);
			$this->mOIntereses->init();
		}
		return $this->mOIntereses;
	}
	function getOMontos(){
		if($this->mOIntereses == null){
			$this->mOIntereses	= new cCreditosMontos($this->mNumeroCredito);
			$this->mOIntereses->init();
		}
		return $this->mOIntereses;
	}	
	function addGarantiaReal($tipo, $tipo_de_valuacion = false, $valor = 0, $persona_propietaria = false, $nombre_del_propietario = "", $fecha_de_adquisicion = false, $documento_presentado = "", $estado_fisico = false, $descripcion = "", $observaciones = "", $fecha_actual = false) {
		$xgar = new cCreditosGarantias ();
		$xgar->setClaveDeCredito ( $this->getNumeroDeCredito () );
		$xgar->setClaveDePersona ( $this->getClaveDePersona () );
		$clave = $xgar->add ( $tipo, $tipo_de_valuacion, $valor, $persona_propietaria, $nombre_del_propietario, $fecha_de_adquisicion, $documento_presentado, $estado_fisico, $descripcion, $observaciones, $fecha_actual );
		$this->mMessages	.= $xgar->getMessages();
		return setNoMenorQueCero($clave);
	}
	
	function getCargosDeCobranza(){
		if($this->mGastosDeCobranza <= 0){
			$this->mGastosDeCobranza	= $this->getOMontos()->getCargosCbzaXPag();
			$this->setGastosCobranzaAfectado($this->mGastosDeCobranza);
		}
		return $this->mGastosDeCobranza;
	}
	function getRazonAutorizacion(){ return $this->mRazonAutorizacion; }
	function getPenasPorCobrar(){
		if($this->mPenasPorCobrar <= 0 ){
			if($this->getOIntereses() !== null){
				$this->mPenasPorCobrar = $this->getOIntereses()->getPenasPorPagar();
			}
		}
		return $this->mPenasPorCobrar;
	}
	function getRespetarPlanDePago(){
		if ($this->mObjEstado == null){ $this->getOEstado();	}
		return $this->mRespetaPlan;
	}
	function getOEstado(){
		$xEst = new cCreditosEstados($this->getEstadoActual());
		if($xEst->init() == true){
			$this->mRespetaPlan	= $xEst->getRespetarPlan();
		}
		$this->mObjEstado = $xEst;
		return $this->mObjEstado;
	}
	function getOTipoAutorizacion(){
		if($this->mObjTipoAut === null){
			$xTipoAut			= new cCreditosTiposDeAutorizacion($this->getTipoDeAutorizacion());
			$xTipoAut->init();
			$this->mObjTipoAut	= $xTipoAut;
		}
		return $this->mObjTipoAut;
	}
	function getPagosSinCapital($tipo = false) {
		$tipo = ($tipo === false) ? $this->getTipoDePago () : $tipo;
		return ($tipo == CREDITO_TIPO_PAGO_INTERES_COMERCIAL or $tipo == CREDITO_TIPO_PAGO_INTERES_PERIODICO) ? true : false;
	}
	/**
	 * Funcion que determina es Estatus de un Credito segun su Tipo de Pago
	 * @param variant $fecha_de_corte de Estimación
	 * @param boolean $explain	Estatus
	 */
	function setDetermineDatosDeEstatus($fecha_de_corte = false, $explain = false, $actualizar = false, $DPagos = false, $EnCierre = false) {
		$xF 				= new cFecha();
		$xLog 				= new cCoreLog();
		$xCache				= new cCache();
		$fecha_de_corte 	= $xF->getFechaISO ( $fecha_de_corte );
		$fecha_de_corte_int = $xF->getInt ( $fecha_de_corte );
		$exoExplain 		= "";
		$aviso 				= "";
		$res 				= null;
		$SoloActualizar		= ($EnCierre == true) ? true : false;
		$EstadoActual		= $this->mEstatusActual;
		if ($this->mCreditoInicializado == false){ $this->init ();	}
		if ($this->getEsAfectable () == true) {
			
			$credito 					= $this->mNumeroCredito;
			$socio 						= $this->mNumeroSocio;
			$fecha_ministracion 		= $this->mFechaMinistracion;
			$fecha_vencimiento 			= $this->mFechaVencimiento;
			$pagos_autorizados 			= $this->mPagosAutorizados;
			$periocidad_pago 			= $this->mPeriocidadDePago;
			$dias_autorizados 			= $this->mDiasAutorizados;
			$tipo_de_pago 				= $this->mTipoDePago;
			$fecha_ultimo_mvto 			= $this->mFechaUltimoMvtoCapital;
			$saldo_insoluto 			= $this->mSdoCapitalInsoluto;
			$monto_ministrado 			= $this->mMontoMinistrado;
			$dias_tolerados_para_vencer = $this->mToleranciaEnDiasParaVcto;
			$estatus_actual 			= $this->mEstatusActual;
			$interes_diario 			= $this->mInteresDiario;
			$pagos_pendientes 			= $this->mPagosAutorizados;
			$xPer 						= $this->getOPeriocidad();
			$OProd 						= $this->getOProductoDeCredito();
			$dias_para_mora 			= $OProd->getDiasTolerados();
			$dias_tolerados_para_vencer = $xPer->getDiasToleradosEnVencer();
			
			$stat 						= $xCache->get($this->mIDCacheCredito . "-datos-estado");
			if(!is_array($stat)){
			$ql 						= new MQL ();
			$stat [SYS_ESTADO] 					= $this->getEstadoActual();
			$stat [SYS_CREDITO_DIAS_NORMALES] 	= 0;
			$stat [SYS_CREDITO_DIAS_MOROSOS] 	= 0;
			$stat [SYS_CREDITO_DIAS_VENCIDOS] 	= 0;
			$stat ["fecha_de_inicio_de_pagos"] 	= $fecha_vencimiento;
			$xLog->add ( "----------CREDITO\t$credito --- Ministrado $fecha_ministracion\r\n", $xLog->DEVELOPER );
			
			if ($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
				// Tomado para los tipos 1: Pago unico a Final de Plazo --- 360 ---
				$mFechaDeInicio 			= $xF->setSumarDias( $this->getDiasAutorizados (), $this->getFechaDeMinistracion () );
				$mFechaDeMora 				= $xF->setSumarDias( $dias_para_mora, $mFechaDeInicio );
				$mFechaDeVencimiento 		= $xF->setSumarDias( $dias_tolerados_para_vencer, $mFechaDeMora );
				$letra_actualmente_pagada 	= $this->getPeriodoActual ();
				$xLog->add ( "WARN\tFinal de Plazo Fecha de Inicio $mFechaDeInicio . Mora $mFechaDeMora . Vence $mFechaDeVencimiento \r\n", $xLog->DEVELOPER );
			} else {
				// obtener la letra que debe pagar.
				$DFechas 					= (is_array($DPagos)) ? $DPagos : $ql->getDataRecord("SELECT * FROM historial_de_pagos WHERE credito=$credito ");
				// ==============================================================
				$DataFechas 				= array ();
				$Historico 					= array ();
				$letra_que_debe_estar_pagada = $this->getPeriodoActual();
				$letra_actualmente_pagada 	= $this->getPeriodoActual();
				// lo que se ha pagado
				$capital_pagado 			= 0;
				$capital_pendiente 			= 0;
				$interes_pagado 			= 0;
				$interes_pendiente 			= 0;
				$interes_nosaldado 			= 0;
				$terminado = false; // determina si ya termino la busqueda
				foreach ( $DFechas as $rows ) {
					$idxc = $rows ["periodo"];
					$DataFechas[$idxc][SYS_FECHA] 				= $rows ["fecha"];
					$DataFechas[$idxc][SYS_CAPITAL] 			= setNoMenorQueCero( $rows ["capital"] );
					$DataFechas[$idxc][SYS_MONTO] 				= setNoMenorQueCero( $rows ["pagos"] );
					$DataFechas[$idxc][SYS_INTERES_NORMAL] 		= setNoMenorQueCero( $rows ["interes"] );
					$DataFechas[$idxc]["SYS_INTERES_PAGADO"] 	= setNoMenorQueCero( $rows ["interes_pagado"] );
					$DataFechas[$idxc]["SYS_INTERES_SALDO"] 	= setNoMenorQueCero( ($rows ["interes"] - $rows ["interes_pagado"]) );
					$capital_pendiente 							+= $DataFechas[$idxc][SYS_CAPITAL];
					$capital_pagado 							+= $DataFechas[$idxc][SYS_MONTO];
					$interes_pagado 							+= $DataFechas[$idxc]["SYS_INTERES_PAGADO"];
					$interes_pendiente							+= $DataFechas[$idxc][SYS_INTERES_NORMAL];
					
					// $xLog->add("WARN\t$idxc\tAgregando pagos $capital_pendiente|$capital_pagado . $interes_pendiente|$interes_pagado \r\n", $xLog->DEVELOPER);
					
					// corregir
					if ($rows ["fecha"] == "0000-00-00") {
						$idxAnterior = setNoMenorQueCero ( ($idxc - 1) );
						if (isset ( $DataFechas [$idxAnterior] )) {
							$DataFechas [$idxc] [SYS_FECHA] = $xF->setSumarDias ( $periocidad_pago, $DataFechas [$idxAnterior] [SYS_FECHA] );
							$ql->setRawQuery ( "UPDATE operaciones_mvtos SET fecha_afectacion='" . $DataFechas [$idxc] [SYS_FECHA] . "' WHERE docto_afectado=$credito AND periodo_socio='$idxc' AND fecha_afectacion='0000-00-00' " );
							$xLog->add ( "ERROR\t$idxc\tFecha Invalida, corregida\r\n", $xLog->DEVELOPER );
						}
					}
				}
				unset ( $DFechas );
				// lo que debe pagarse
				for($i = 1; $i <= $this->getPagosAutorizados (); $i ++) {
					if ($terminado == false) {
						$idpagoanterior = setNoMenorQueCero ( $i - 1 );
						$idpagoproximo = setNoMenorQueCero ( $i + 1 );
						if (! isset ( $DataFechas [$i] )) {
							// ==========================
							$xLog->add( "ERROR\t$i\tNumero de Pago con error o inexistente\r\n", $xLog->DEVELOPER );
							$xLetra	= new cCreditosLetraDePago($this->mNumeroCredito, $i);
							if($xLetra->init() == true){
								$fecha_de_referencia	= $xLetra->getFechaDePago();
								$FechaDePago 			= $xLetra->getFechaDePago();
							} else {
								$xPlan 					= new cPlanDePagosGenerador ();
								$xPlan->initPorCredito ( $this->mNumeroCredito, $this->getDatosInArray () );
								$fecha_de_referencia 	= $xF->setSumarDias ( ($this->getPeriocidadDePago () * $i), $this->getFechaDeMinistracion () );
								$FechaDePago 			= $xPlan->getFechaDePago ( $fecha_de_referencia, $i );
							}
						} else {
							$Datos = $DataFechas [$i];
							$fecha = $Datos [SYS_FECHA];
							if ($xF->getInt ( $fecha ) <= $fecha_de_corte_int) {
								// creditos con solo interes
								if ($this->getPagosSinCapital () == true) {
									if ($Datos ["SYS_INTERES_SALDO"] > 0) {
										$fecha = $xF->setSumarDias ( 1, $fecha );
										$xLog->add ( "WARN\t$i\tInicia la fecha de Mora en $fecha porque el interes es " . $Datos ["SYS_INTERES_SALDO"] . " \r\n", $xLog->DEVELOPER );
										$FechaDePago = $fecha;
										$terminado = true;
									}
								} else {
									$saldoLetra = setNoMenorQueCero ( ($Datos [SYS_CAPITAL] - $Datos [SYS_MONTO]) );
									if ($saldoLetra > 0) {
										$fecha = $xF->setSumarDias ( 1, $fecha );
										$xLog->add ( "ERROR\t$i\tInicia la fecha de Mora en $fecha porque el Saldo de la Letra es $saldoLetra \r\n", $xLog->DEVELOPER );
										$FechaDePago = $fecha;
										$terminado = true;
									}
								}
							} else {
								$xLog->add ( "WARN\t$i\tFecha de Pago($fecha) Superior a la Fecha de corte ($fecha_de_corte)\r\n", $xLog->DEVELOPER );
								$FechaDePago = $fecha;
								$terminado = true;
							}
						}
					}
				}
				// ==============================================================
				if (! isset ( $FechaDePago )) {
					$xPlan = new cPlanDePagosGenerador ();
					$xPlan->initPorCredito ( $this->mNumeroCredito, $this->getDatosInArray () );
					$fecha_de_referencia = $xF->setSumarDias ( ($this->getPeriocidadDePago () * $this->getPeriodoActual ()), $this->getFechaDeMinistracion () );
					$FechaDePago = $xPlan->getFechaDePago ( $fecha_de_referencia, $this->getPeriodoActual () );
					$xLog->add ( "ERROR\tError en la Fecha. Se carga por estimacion $FechaDePago del periodo " . $this->getPeriodoActual () . " \r\n", $xLog->DEVELOPER );
					// corregir y actualizar
				}
				// $letra_que_debe_estar_pagada = ($letra_que_debe_estar_pagada ==0) ? 1 : $letra_que_debe_estar_pagada;
				// $FechaDePago = $Historico[$letra_que_debe_estar_pagada][SYS_FECHA];
				$mFechaDeInicio 		= $FechaDePago;
				$mFechaDeMora 			= $xF->setSumarDias ( $dias_para_mora, $mFechaDeInicio );
				$mFechaDeVencimiento 	= $xF->setSumarDias ( $dias_tolerados_para_vencer, $mFechaDeMora );
				// $xLog->add("OK\t$idxc\t=" . $letra_que_debe_estar_pagada . "|" . $letra_actualmente_pagada . "|" . $FechaDePago . "|$mFechaDeInicio|$mFechaDeMora|$mFechaDeVencimiento\r\n", $xLog->DEVELOPER);
				// $xLog->add("Letra pagara $letra_actualmente_pagada , que debe pagar $letra_que_debe_estar_pagada\r\n", $xLog->DEVELOPER);
				// $pagos_pendientes = setNoMenorQueCero( $this->mPagosAutorizados - $letra_actualmente_pagada );
			}
			
			$stat [SYS_CREDITO_DIAS_NORMALES] = setNoMenorQueCero ( $xF->setRestarFechas ( $mFechaDeMora, $fecha_ministracion ) );
			$stat [SYS_CREDITO_DIAS_MOROSOS] = setNoMenorQueCero ( $xF->setRestarFechas ( $mFechaDeVencimiento, $mFechaDeMora ) );
			$stat [SYS_CREDITO_DIAS_VENCIDOS] = setNoMenorQueCero ( $xF->setRestarFechas ( $fecha_de_corte, $mFechaDeVencimiento ) );
			
			$stat ["fecha_de_inicio"] 		= $mFechaDeInicio;
			$stat ["fecha_de_mora"] 		= $mFechaDeMora;
			$stat ["fecha_de_vencimiento"] 	= $mFechaDeVencimiento;
			/**
			 * Calcular el Estatus por metodo reversivo
			 */
			
			if ($xF->getInt ( $fecha_de_corte ) >= $xF->getInt ( $mFechaDeVencimiento )) {
				$xLog->add ( "ERROR\tA.VENC\tLa fecha de corte (" . $xF->getFechaCorta ( $fecha_de_corte ) . ") es mayor a la de vencimiento(" . $xF->getFechaCorta ( $mFechaDeVencimiento ) . ") por o que se da como ***VENCIDO***\r\n" );
				$stat [SYS_ESTADO] = CREDITO_ESTADO_VENCIDO;
			} else if (($xF->getInt ( $fecha_de_corte ) >= $xF->getInt ( $mFechaDeMora )) and ($xF->getInt ( $fecha_de_corte ) < $xF->getInt ( $mFechaDeVencimiento ))) {
				$xLog->add ( "WARN\tA.MOR\tLa fecha de corte (" . $xF->getFechaCorta ( $fecha_de_corte ) . ") es mayor a la de Mora(" . $xF->getFechaCorta ( $mFechaDeMora ) . ") y Menor a la Fecha de Vencimiento (" . $xF->getFechaCorta ( $mFechaDeVencimiento ) . ") por lo que se da como ***MOROSO***\r\n" );
				$stat [SYS_ESTADO] = CREDITO_ESTADO_MOROSO;
				$stat [SYS_CREDITO_DIAS_VENCIDOS] = 0;
			} else if ($xF->getInt ( $fecha_de_corte ) < $xF->getInt ( $mFechaDeMora )) {
				$xLog->add ( "OK\tA.VIG\tLa fecha de corte (" . $xF->getFechaCorta ( $fecha_de_corte ) . ") es mayor a la de Mora(" . $xF->getFechaCorta ( $mFechaDeMora ) . ") por o que se da como ***VIGENTE***\r\n" );
				$stat [SYS_ESTADO] = CREDITO_ESTADO_VIGENTE;
				$stat [SYS_CREDITO_DIAS_VENCIDOS] = 0;
				$stat [SYS_CREDITO_DIAS_MOROSOS] = 0;
			} else {
			}
			
			$this->mFechaVencimientoLegal 	= $mFechaDeVencimiento;
			$this->mFechaMora 				= $mFechaDeMora;
			// if($actualizar == true){ $xLog->add("Se actualizaran saldos12\r\n", $xLog->DEVELOPER);}
			if ($explain == true) {
				$stat ["notas"] = $xLog->getMessages ();
			} else {
				$stat ["notas"] = "";
			}
			} //End check estatus
			if ($actualizar == true) {
				if(CREDITO_PURGAR_ESTADOS == true) {
					$xQL	= new MQL();
					//Eliminar Traspasos de Movimiento Anteriores
					$xQL->setRawQuery( "DELETE FROM operaciones_mvtos WHERE socio_afectado=$socio AND docto_afectado=$credito
							AND ( tipo_operacion=111 OR tipo_operacion=113 OR tipo_operacion=114 OR tipo_operacion=115)" );
					unset($xQL);
				}
				
				$EstadoCalculado 	= $stat[SYS_ESTADO];
				if ($EstadoActual != $EstadoCalculado) {
					$xLog->add ( "WARN\tRE_ESTATUS\tRecalcular Estatus de " . $this->getEstadoActual () . " A $EstadoCalculado\n" );
					switch ($EstadoCalculado) {
						case CREDITO_ESTADO_VIGENTE : // Fecha de Corte?
							$this->setEnviarVigente ( $fecha_de_corte, $this->getPeriodoActual (), DEFAULT_RECIBO );
							break;
						case CREDITO_ESTADO_MOROSO :
							$this->setEnviarMoroso ( $this->mFechaMora, $this->getPeriodoActual (), DEFAULT_RECIBO );
							break;
						case CREDITO_ESTADO_VENCIDO :
							$this->setEnviarVencido ( $this->mFechaVencimientoLegal, $this->getPeriodoActual (), DEFAULT_RECIBO );
							break;
					}
				} else {
					$xLog->add ( "ESTATUS\tSin Cambios de Estado : " . $this->getEstadoActual () . "\n" );
				}
				$arrUpdate = array (
						"fecha_mora" 					=> $mFechaDeMora,
						"fecha_vencimiento_dinamico" 	=> $mFechaDeVencimiento,
						"estatus_actual" 				=> $EstadoCalculado 
				);
				//Mejora 28Abril2017
				if ($EstadoActual == $EstadoCalculado){
					//eliminar la Actualizacion del Estado y solo actualizar Fechas de Vencimiento
					unset($arrUpdate["estatus_actual"]);
					$SoloActualizar	= true;
				}
				$this->setUpdate ( $arrUpdate, $SoloActualizar ); //add true, solo actualizar el estado
				$xLog->add ( "WARN\tActualizar Fecha de Mora a $mFechaDeMora y Fecha de Vencimiento a $mFechaDeVencimiento con estado " . $stat [SYS_ESTADO] . "\r\n", $xLog->DEVELOPER );
			}
			
			if ($explain == true) {
				$aviso .= $this->getMessages ( OUT_HTML );
				$xLng = new cLang ();
				$exoExplain = "<fieldset>
                    <legend>Explicacion de estatus al " . $xF->getFechaCorta ( $fecha_de_corte ) . "</legend>
                    <table>
                        <tbody>
							<tr>
								<th class='izq'>Fecha de Ministracion</th><td>" . $xF->getFechaCorta ( $fecha_ministracion ) . "</td>
								<th class='izq'>Fecha de Inicio de Pagos</th><td>" . $xF->getFechaCorta ( $stat ["fecha_de_inicio_de_pagos"] ) . "</td>
							</tr>
							<tr>
								<th class='izq'>Pagos Autorizados</th><td>$pagos_autorizados</td>
								<th class='izq'>Periocidad de Pagos</th><td>$periocidad_pago</td>
							</tr>
							<tr>
								<th class='izq'>Monto Ministrado</th><td>$monto_ministrado</td>
								<th class='izq' >Saldo Insoluto</th><td>$saldo_insoluto</td>
							</tr>
							
							<tr>
								<th class='izq'>" . $xLng->getT ( "TR.Pagos Efectuados" ) . "</th><td>$letra_actualmente_pagada</td>
								<th class='izq'>" . $xLng->getT ( "TR.Pagos Pendientes" ) . "</th><td>$pagos_pendientes</td>
							</tr>

							<tr>
								<th class='izq'>" . $xLng->getT ( "TR.Dias" ) . "</th><td>" . $this->getDiasSolicitados () . "</td>
								<th class='izq'>" . $xLng->getT ( "TR.Dias Autorizados" ) . "</th><td>" . $this->getDiasAutorizados () . "</td>
							</tr>
							
							<tr>
								<th class='izq'>" . $xLng->getT ( "TR.Dias Tolerados Mora" ) . "</th><td>" . $dias_para_mora . "</td>
								<th class='izq'>" . $xLng->getT ( "TR.Dias Tolerados vencimiento" ) . "</th><td>" . $dias_tolerados_para_vencer . "</td>
							</tr>

							<tr>
								<th class='izq'>Fecha de Inicio de Calculo</th><td>" . $xF->getFechaCorta ( $mFechaDeInicio ) . "</td>
								<th class='izq'>Fecha de Mora</th><td>" . $xF->getFechaCorta ( $mFechaDeMora ) . "</td>
							</tr>
							<tr>
								<th class='izq'>Fecha de Vencimiento</th><td>" . $xF->getFechaCorta ( $mFechaDeVencimiento ) . "</td>
								<th class='izq'>Estatus Determinado</th><td>" . $stat ["estatus"] . "</td>
							</tr>
							<tr>
								<th class='izq'>Dias Vigentes</th><td>" . $stat [SYS_CREDITO_DIAS_NORMALES] . "</td>
								<th class='izq'>Dias Morosos</th><td>" . $stat [SYS_CREDITO_DIAS_MOROSOS] . "</td>
							</tr>
							<tr>
								<th class='izq'>Dias Vencidos</th><td>" . $stat [SYS_CREDITO_DIAS_VENCIDOS] . "</td>
								<th class='izq'>Tipo de Pago</th><td >$tipo_de_pago</td>
							</tr>
					<tr><th colspan='4'>$aviso</th></tr></tbody></table></fieldset>";
				$res = $exoExplain;
			} else {
				$res = $stat;
			}
		}
		//setLog($xLog->getMessages());
		$this->mMessages .= $xLog->getMessages();
		return $res;
	} // END FUNCTION
	function getInteresDevengado($fecha_de_calculo = false, $parcialidad = false, $fecha_anterior = false, $solo_mora_corriente = false) {
		$xCache				= new cCache();
		$xF					= new cFecha();
		$fecha_de_calculo	= $xF->getFechaISO($fecha_de_calculo);
		
		if($this->mCreditoInicializado == false) { 	$this->init();	}
		$FECHA_DE_OPERACION = $fecha_de_calculo;
		$OProd 				= $this->getOProductoDeCredito ();
		$xF	 				= new cFecha( 0, $FECHA_DE_OPERACION );
		$xT					= new cTipos();
		$xLog 				= new cCoreLog();
		$xF2 				= new cFecha();
		$credito 			= $this->getNumeroDeCredito ();

		
		if($parcialidad === false){
			$parcialidad		= $this->getPeriodoActual();
			$this->mIDCacheInt	= "$credito-NP-ID-". $xF->getInt(); //Id cache de calculo
		} else {
			$this->mIDCacheInt	= "$credito-$parcialidad-ID-". $xF->getInt(); //Id cache de calculo
		}
		$PERIODO_A_PAGAR 	= $parcialidad;
		$ESTADO_ACTUAL 		= $this->getEstadoActual();
		
		
		//setError($this->mIDCacheInt);
		
		$arrInteres			= $xCache->get($this->mIDCacheInt);
		if($arrInteres == null OR !is_array($arrInteres)){
			
		
		
		$TASA_NORMAL 		= $this->getTasaDeInteres ();
		$TASA_MORA 			= $this->getTasaDeMora();
		$SALDO_ACTUAL 		= $this->getSaldoActual();
		$BASE_NORMAL 		= $this->getSaldoActual();
		$BASE_MORA 			= $this->getSaldoActual();
		$TOTAL_MONTO_LETRA	= $this->getMontoDeParcialidad();
		$TIPO_DE_PAGO 		= $this->getTipoDePago();
		$PAGOS_SIN_CAPITAL 			= $this->getPagosSinCapital();
		$APLICA_PENAS				= $OProd->getAplicaPenas();
		$APLICA_GTOS_X_MORA			= $xT->cBool( $OProd->getAplicaMoraPorGastos() );
		$FDIAS_TOLERANCIA			= $OProd->getFormulaDeDiasTolerancia(); //Formula dias de tolerancia
		$DIAS_TOLERADOS				= $OProd->getDiasTolerados();
		$PERIOCIDAD_DE_PAGO			= $this->getPeriocidadDePago();
		$DIAS_NORMAL 				= 0;
		$DIAS_MORA 					= 0;
		$DIAS_PENA					= 0;
		$DIVISOR_DE_INTERESES 		= EACP_DIAS_INTERES;
		$MONTO_ORIGINAL_DE_CREDITO 	= $this->getMontoAutorizado();
		
		$MONTO_PROXIMO_PAGO			= 0;
		$FECHA_DE_PROXIMO_PAGO		= $this->getFechaDeProximoPago();
		
		$INTERES_DIARIO_GENERADO 	= $this->getInteresDiariogenerado();
		$rw 						= $this->getDatosDeCredito();
		$socio 						= $this->getClaveDePersona();
		$fecha 						= $this->getFechaDeMinistracion();
		$INTERES_NORMAL_PENDIENTE 	= $this->getInteresNormalDevengado() - $this->getInteresNormalPagado(); // $this->getInteresNormalPorPagar($fecha_de_calculo);
		$xPlan 						= null;
		$msg 						= "";
		$interes 					= 0;
		$moratorio 					= 0;
		$cobranza 					= 0;
		$penas						= 0;
		$xPlan 						= null;
		$PlanInit 					= false;
		//Corrige periodo a Pagar
		if($PERIODO_A_PAGAR <=0){$PERIODO_A_PAGAR = 1;}
		// Evaluador de tasa
		eval ( $OProd->getPreModInteres () );
		if ($fecha_anterior === false) {
			
			$sqlDev 				= "SELECT * FROM `creditos_sdpm_acumulado` WHERE credito = $credito LIMIT 0,1";
			$DDev 					= obten_filas ( $sqlDev );
			$FECHA_DE_ULTIMO_PAGO 	= (isset ( $DDev ["fechaActual"] )) ? $DDev ["fechaActual"] : $this->getFechaDeMinistracion ();
			
		} else {
			$FECHA_DE_ULTIMO_PAGO 	= $fecha_anterior;
		}
		// corrige FECHA_ULTIMO_PAGO
		if ($this->isAFinalDePlazo() == false AND $this->getPeriodoActual() > 0 AND $this->getEsAfectable () == true ) {
			
			$xPlan 					= $this->getOPlanDePagos();
			if ($xPlan->isInit () == true) {
				//$this->getPeriodoActual()
				$PERIODO_ANTERIOR	= ($PERIODO_A_PAGAR-1);
				$ALetra 			= $xPlan->getOLetra ( $PERIODO_ANTERIOR );
				$TOTAL_MONTO_LETRA	= $ALetra->getTotal();
				//Si 
				if ($xF->getInt ( $ALetra->getFechaDePago() ) > $xF->getInt ( $FECHA_DE_ULTIMO_PAGO )) {
					$FECHA_DE_ULTIMO_PAGO 	= $ALetra->getFechaDePago ();
					$xLog->add( "OK\tFP\tCredito a Fecha de Pago xx $FECHA_DE_ULTIMO_PAGO\r\n", $xLog->DEVELOPER );
				}
			}
		}
		
		// pre evaluadores
		if ($xF2->getInt ( $FECHA_DE_ULTIMO_PAGO ) < $xF2->getInt ( $FECHA_DE_OPERACION )) {
			$DIAS_NORMAL = $xF->setRestarFechas ( $FECHA_DE_OPERACION, $FECHA_DE_ULTIMO_PAGO );
			if ($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
				$xLog->add( "OK\tFP\tCredito con pago Pago a Final de Plazo, Interes Diario de $INTERES_DIARIO_GENERADO\r\n", $xLog->DEVELOPER );
			} else {
				$xPlan 						= $this->getOPlanDePagos ();
				$PlanInit 					= $xPlan->isInit ();
				$DLetra 					= $xPlan->getOLetra ( $PERIODO_A_PAGAR );
				$FECHA_COMPROMETIDA_DE_PAGO = $DLetra->getFechaDePago ();
				$MONTO_PROXIMO_PAGO			= $DLetra->getCapital();
				$TOTAL_MONTO_LETRA			= $DLetra->getTotal();
				$xLog->add( "OK\tPLAN\tPlan de Pago con letra $PERIODO_A_PAGAR y Fecha $FECHA_COMPROMETIDA_DE_PAGO - $DIAS_NORMAL .- $FECHA_DE_ULTIMO_PAGO\r\n", $xLog->DEVELOPER );
				if ($PAGOS_SIN_CAPITAL == true) {
					$xLog->add( "WARN\tSIN_CAPITAL\tPAGOS DE INTERES SIN CAPITAL e Interes $INTERES_NORMAL_PENDIENTE\r\n", $xLog->DEVELOPER );
				}
				// ================ Repara dias Normales
			}
			
			if ($this->getPeriocidadDePago () == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
				$FECHA_COMPROMETIDA_DE_PAGO = $this->getFechaDeMora();
				$DIAS_MORA 					= setNoMenorQueCero ( $xF->setRestarFechas ( $FECHA_DE_OPERACION, $FECHA_COMPROMETIDA_DE_PAGO ) );
				$xLog->add( "WARN\tDIAS_MORA\tDias de mora($DIAS_MORA) Calculado entre $FECHA_DE_OPERACION y $FECHA_COMPROMETIDA_DE_PAGO\r\n", $xLog->DEVELOPER );
				$xLog->add( "WARN\tBASE_MORA_PERIODO\tBase de Mora $BASE_MORA, por Dias $DIAS_MORA a TASA $TASA_MORA, desde el dia $FECHA_COMPROMETIDA_DE_PAGO\r\n", $xLog->DEVELOPER );
			} else {
				if ($PlanInit == true) {
					if (isset ( $FECHA_COMPROMETIDA_DE_PAGO ) and $FECHA_COMPROMETIDA_DE_PAGO != false){
						//Calcular desde la fecha de pago de la letra mas los dias tolerados
						$DIAS_MORA 			= setNoMenorQueCero ( $xF->setRestarFechas ( $FECHA_DE_OPERACION, $FECHA_COMPROMETIDA_DE_PAGO ) );
						// ================================ EVALUAR DIAS TOLERADOS DE PAGO
						
						eval($FDIAS_TOLERANCIA);
						// ================================ CAMBIAR SEGUN DIAS TOLERADOS DE PAGO
						$FECHA_TOLERADA_DE_MORA = $xF2->setSumarDias ( $DIAS_TOLERADOS, $FECHA_COMPROMETIDA_DE_PAGO );
						// == si la fecha de corte es mayor a los dias transcurridos
						if ($xF2->getInt ( $FECHA_DE_OPERACION ) <= $xF2->getInt ( $FECHA_TOLERADA_DE_MORA )) {
							$xLog->add( "WARN\tMORA_DD\tDias de Mora $DIAS_MORA cambiado por Fecha de Tolerancia a $FECHA_TOLERADA_DE_MORA de Fecha Prometida $FECHA_COMPROMETIDA_DE_PAGO\r\n", $xLog->DEVELOPER );
							$DIAS_PENA		= $DIAS_MORA;
							$DIAS_MORA 		= 0; // se perdona la mora
						} else {
							if($this->getPagosSinCapital() == true AND $DIAS_MORA > $DIAS_TOLERADOS){
								$xLog->add( "WARN\tMORA_DDSP\tDias de Mora a $DIAS_MORA, cambiado por Fecha de Tolerancia a $FECHA_TOLERADA_DE_MORA por Dias " . $DIAS_TOLERADOS . " Tolerados\r\n", $xLog->DEVELOPER );
								//Evaluar si se espera en definitivo
								$DIAS_MORA	= $DIAS_MORA;// - $OProd->getDiasTolerados(); //MORA COMPLETA SI SUPERA LOS DIAS TOLERADOS
								//$BASE_MORA	= $this->getSaldoActual();
							}
						}
						// ================================ CAMBIAR POR EVALUADOR
						$BASE_MORA 		= ($PAGOS_SIN_CAPITAL == true) ? $BASE_MORA : $DLetra->getCapital();
						if ($solo_mora_corriente == true AND $this->getPagosSinCapital() == false) {
							if ($DIAS_MORA > $xF->getDiasCorrientesDeMes()) {
								$xLog->add ( "WARN\tDIAS_MORA\tSe Cambias $DIAS_MORA a " . $xF->getDiasCorrientesDeMes() . "\r\n", $xLog->DEVELOPER );
								$DIAS_MORA = $xF->getDiasCorrientesDeMes();
							}
							if($this->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
								if($this->getInteresMoratorioDev() > 0){
									$xLog->add ( "WARN\tDIAS_MORA\tDias de mora($DIAS_MORA) Eliminado por Devengacion de " . $this->getInteresMoratorioDev() . "\r\n", $xLog->DEVELOPER );
									$DIAS_MORA		= 0;
								}
							}
						}
						$xLog->add ( "WARN\tDIAS_MORA\tDias de mora($DIAS_MORA) Calculado entre $FECHA_DE_OPERACION y $FECHA_COMPROMETIDA_DE_PAGO ne periodo $PERIODO_A_PAGAR\r\n", $xLog->DEVELOPER );
						$xLog->add ( "WARN\tBASE_MORA_LETRA\tBase de Mora $BASE_MORA, por Dias $DIAS_MORA a TASA $TASA_MORA, desde el dia $FECHA_COMPROMETIDA_DE_PAGO\r\n", $xLog->DEVELOPER );
					} else {
						$xLog->add ( "WARN\tMORA\tSin datos para letra $PERIODO_A_PAGAR\r\n", $xLog->DEVELOPER );
					}
				} else {
					$xLog->add ( "WARN\tNO_INIT\tPLAN NO INICIADO\r\n", $xLog->DEVELOPER );
				}
				$xLog->add ( $xPlan->getMessages ( OUT_TXT ), $xLog->DEVELOPER );
			}
			//Ajustar Mora solamente por Creditos solo interes
			
			// Ajustar dias de mora MAXIMO
			if ($DIAS_MORA > $this->getDiasMaximoPorDevengarVencido()) {
				$xLog->add ( "WARN\tDIAS_MORA\tLos dias de Mora se LIMITAN A " . $this->getDiasMaximoPorDevengarVencido() . " desde $DIAS_MORA\r\n", $xLog->DEVELOPER );
				$DIAS_MORA = $this->getDiasMaximoPorDevengarVencido();
				
			}
			if ($DIAS_NORMAL > $this->getDiasMaximoPorDevengarVigente()) {
				$xLog->add ( "WARN\tDIAS_MORA\tLos dias de Mora se LIMITAN A " . $this->getDiasMaximoPorDevengarVigente() . " desde $DIAS_NORMAL\r\n", $xLog->DEVELOPER );
				$DIAS_NORMAL = $this->getDiasMaximoPorDevengarVigente();
			}
			if ($BASE_NORMAL <= 0) {
				$xLog->add ( "WARN\tBASE_NORMAL_0\tLa base Normal $BASE_NORMAL es CERO, se resetean valores(Base Mora $BASE_MORA, Tasa $TASA_MORA, Dias $DIAS_MORA)\r\n", $xLog->DEVELOPER );
				$BASE_MORA = 0;
				$DIAS_MORA = 0;
				$TASA_MORA = 0;
				
			}
			
			eval ( $OProd->getPreModInteres() );
			// ================= Intereses
			$moratorio 	= (($BASE_MORA * $DIAS_MORA) * $TASA_MORA) / $DIVISOR_DE_INTERESES;
			$interes 	= (($BASE_NORMAL * $DIAS_NORMAL) * $TASA_NORMAL) / $DIVISOR_DE_INTERESES;
			
			if ($APLICA_GTOS_X_MORA == true) {
				$cobranza = $moratorio;
				$xLog->add ( "WARN\tBASE_CBZA_LETRA\tGastos de Cobranza $moratorio en Lugar de Mora ($BASE_MORA . $DIAS_MORA . $TASA_NORMAL)\r\n", $xLog->DEVELOPER );
				$moratorio = 0;
			}
			// POS Valuadores
			eval( $OProd->getPosModInteres() );
			eval( $OProd->getFormulaDePena() ); 
			$xLog->add ( "$socio\t$credito\tSALDO: $BASE_NORMAL\t DIAS : $DIAS_NORMAL\tINTERES : $interes\tMORA : $moratorio\tPenas: $penas\r\n", $xLog->DEVELOPER );
		} else {
			$xLog->add ( "WARN\tNO_CALCULO\tNo se Calculan Devengados ($FECHA_DE_OPERACION|$FECHA_DE_ULTIMO_PAGO)\r\n", $xLog->DEVELOPER );
		}
		
		$arrInteres [SYS_INTERES_NORMAL] 		= round($interes,2);
		$arrInteres [SYS_INTERES_MORATORIO] 	= round($moratorio,2);
		$arrInteres [SYS_GASTOS_DE_COBRANZA] 	= round($cobranza,2);
		$arrInteres [SYS_PENAS] 				= round($penas,2);

		//Si es calculo al dia y es sin capital y existe interes moratorio devengado
		if($solo_mora_corriente == true AND $this->getPagosSinCapital() == true AND $this->getInteresMoratorioDev() > 0){
			$arrInteres [SYS_INTERES_MORATORIO]	= setNoMenorQueCero(($arrInteres [SYS_INTERES_MORATORIO]-$this->getInteresMoratorioDev()),2);;
		}
		//if($PERIODO_A_PAGAR == 14){ setLog($xLog->getMessages()); }
		$this->mMessages 						.= $xLog->getMessages();
		$arrInteres [SYS_MSG] 					= "";// $xLog->getMessages();
		
		//setLog($xLog->getMessages());

		
		if($this->getOIntereses() !== null){
			$this->getOIntereses()->setPeriodoDeTrabajo($PERIODO_A_PAGAR);
			$this->getOIntereses()->setInteresesCorrientes($interes, $moratorio);
			$this->getOIntereses()->setCargosYPenas($cobranza, $penas);
			//setLog("Penas $penas=(($SALDO_ACTUAL-$MONTO_PROXIMO_PAGO)*($TASA_NORMAL/360))*$DIAS_PENA; $penas");
		}
		$xCache->set($this->mIDCacheInt, $arrInteres);
		
		//setLog($xLog->getMessages());
			
		}
		return $arrInteres;
	}
	function setReestructurarIntereses($FechaInicial = false, $FechaFinal = false, $ForceMoratorios = false) {
		if ($this->mCreditoInicializado == false){ $this->init(); }
		$xT 			= new cTipos ();
		$xLog 			= new cCoreLog ();
		$xQL 			= new MQL ();
		$xF				= new cFecha();
		$procesar		= true;
	
		$socio 			= $this->getClaveDePersona ();
		$solicitud 		= $this->getNumeroDeCredito ();
		$xCUtils 		= new cUtileriasParaCreditos ();

		$FechaFinal 	= $xF->getFechaISO($FechaFinal);
		
		$FechaInicial 	= ($FechaInicial === false) ? $this->getFechaDeMinistracion() : $FechaInicial;
		if($this->getEsAfectable() == true AND $procesar == true){
			if(($this->getTipoDePago() == CREDITO_TIPO_PAGO_UNICO) or ($this->getPeriocidadDePago () == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO)) {
				//Eliminar si son a final de Plazo
				$xQL->setRawQuery( "DELETE FROM operaciones_recibos WHERE numero_socio=$socio AND docto_afectado=$solicitud AND tipo_docto=11" );
				//Eliminar operaciones sin son a Final de Plazo Operaciones de Plan de pago
				$xQL->setRawQuery( "DELETE FROM operaciones_mvtos WHERE socio_afectado=$socio AND docto_afectado=$solicitud 
						AND (tipo_operacion=410 OR tipo_operacion=411 OR tipo_operacion=412 OR tipo_operacion=413 OR tipo_operacion=1005 OR tipo_operacion=601)" );
			}

			//Reestructurar Estatus
			$DEstado 		= $this->setDetermineDatosDeEstatus( $FechaFinal, false, true );
			//Reestructurar Intereses
			$xCUtils->setGenerarMvtoFinDeMes( $FechaInicial, $FechaFinal, $solicitud, true );
			if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				//$xLog->add($xCUtils->setReestructurarSDPM ( false, $solicitud ), $xLog->DEVELOPER);
				$xCUtils->setReestructurarSDPM( false, $solicitud );
			} else {
				//$xLog->add($xCUtils->setReestructurarSDPM_Planes ( false, $solicitud ), $xLog->DEVELOPER);
				$xCUtils->setReestructurarSDPM_Planes( false, $solicitud );
			}
			$xCUtils->setRegenerarInteresDevengado( $solicitud, $FechaInicial, $FechaFinal, $ForceMoratorios );
			$xCUtils->setAcumularIntereses( false, $solicitud );
				
			if($this->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) { $xCUtils->setAcumularMoraDeParcialidades( $solicitud );	}
			$xLog->add ($xCUtils->getMessages(), $xLog->DEVELOPER );
		} else {
			$xLog->add("NO_PROCESADO\tCredito No Afectable\n");
		}
		$this->mMessages .= $xLog->getMessages ();
		$this->setCuandoSeActualiza();
		return $xLog->getMessages();
	}
		
	function setCreditoPagado($fecha = false) {
		$fecha = ($fecha == false) ? fechasys () : $fecha;
		// efectuar las operaciones de cierre de credito
		if ($this->getPeriocidadDePago () == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
			$idPlan = $this->getNumeroDePlanDePagos ();
			if (isset ( $idPlan ) and ($idPlan > 1)) {
				$xPlan = new cPlanDePagos ( $idPlan );
				$xPlan->setEliminar(false);
				$this->mMessages .= $xPlan->getMessages ();
			}
		}
		//Actualizar recibo de pago de Capital.
		$sql	= "SELECT `recibo_afectado` AS `recibo`,`fecha_afectacion` AS `fecha` FROM `operaciones_mvtos` WHERE `tipo_operacion`=" . OPERACION_CLAVE_PAGO_CAPITAL . " AND `docto_afectado`=" . $this->getNumeroDeCredito() . " ORDER BY `fecha_afectacion` DESC LIMIT 0,1";
		$xQL	= new MQL();
		$DD		= $xQL->getDataRow($sql);
		$recibo	= 0;
		if(isset($DD["recibo"])){
			$recibo		= setNoMenorQueCero($DD["recibo"]);
			$RFecha		= $DD["fecha"];
			$sqlU		= "UPDATE `creditos_solicitud` SET `fecha_ultimo_capital`='$fecha',`recibo_ultimo_capital`='$recibo' WHERE `numero_solicitud`= ". $this->getNumeroDeCredito();
			$xQL->setRawQuery($sqlU);
		}
		$this->setUpdate ( array (
				"saldo_conciliado" => 0,
				"saldo_actual" => 0,
				"saldo_vencido" => 0,
				"fecha_conciliada" => $fecha,
				
				"ultimo_periodo_afectado" => $this->getPagosAutorizados (),
				"sdo_int_ant" => 0,
				"fecha_ultimo_mvto" => $fecha,
				"fecha_ultimo_capital" => $fecha,
				"recibo_ultimo_capital" => $recibo
		) );
		$this->setRevisarSaldo($fecha);
		//,	"clave_de_recibo" => $recibo
		$xRN 	= new cReglaDeNegocio ();
		$oP 	= $this->getOPersona ();
		$xEmp 	= $this->getOEmpresa ();
		$xRN->setVariables ( array (
				"nombre_de_persona" => $oP->getNombreCompleto (),
				"codigo" => $this->getNumeroDeCredito (),
				"descripcion" => $this->getDescripcion (),
				"nombre_de_la_empresa" => $xEmp->getNombreCorto () 
		) );
		$xRN->setExecuteActions ( $xRN->reglas ()->RN_CREDITOS_AL_LIQUIDAR );
	}
	function getOPersona($clave = false) {
		$clave = ($clave == false) ? $this->getClaveDePersona () : $clave;
		if ($this->mObjSoc == null) {
			$this->mObjSoc = new cSocio ( $clave );
			$this->mObjSoc->init ();
		}
		return $this->mObjSoc;
	}
	function getOOficial() {
		$xO = new cOficial ( $this->mOficialDeCredito );
		$xO->init ();
		return $xO;
	}
	function getClaveDeOficialDeCredito() {
		return $this->mOficialDeCredito;
	}
	function getSaldoIntegrado($fecha = false, $conImpuestos = false) {
		$fecha = ($fecha == false) ? fechasys () : $fecha;
		if ($this->getPeriocidadDePago () == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
			$periodo = $this->getPeriodoActual () + 1;
			$DINT = $this->getInteresDevengado ( $fecha, $periodo, false, true );
		} else {
			$DINT = $this->getInteresDevengado ( $fecha );
		}
		$intereses 	= (($this->getInteresNormalDevengado () + $DINT [SYS_INTERES_NORMAL]) - $this->getInteresNormalPagado ());
		$mora		= (($this->getInteresMoratorioDev() + $DINT [SYS_INTERES_MORATORIO]) - $this->getInteresMoratorioPagado ());
		$impuestos 	= 0;
		$cobranza	= $this->getCargosDeCobranza();
		//setLog("$fecha $mora $intereses --- " . $this->getInteresMoratorioDev() . "-----" . $DINT [SYS_INTERES_MORATORIO]);
		if ($conImpuestos == true) {
			$impuestos = ($intereses * $this->getTasaIVA())+(($mora+$cobranza) * $this->getTasaIVAOtros());
		}

		return $this->getSaldoActual ( $fecha ) + $intereses + $mora + $impuestos + $cobranza;
	}
	function initPagosEfectuados($data = false, $fecha_de_corte = false, $report = false) {
		$xF 					= new cFecha ();
		$fecha_minis 			= $xF->getInt( $this->getFechaDeMinistracion () );
		$fecha_de_corte 		= $xF->getInt( $fecha_de_corte );
		
		if ($this->mInitPagos == false) {
			// comparar pagos con letras
			if($report == true OR getEnCierre() == true){
				$sql			= "SELECT `periodo_socio`,`fecha_de_pago`,`total`,`interes_normal`,`interes_moratorio`,`otros`,`capital`,`impuesto` FROM `tmp_creditos_abonos_parciales` WHERE `docto_afectado`=" . $this->mNumeroCredito;
			} else {
				$xVis			= new cSQLVistas();
				$sql			= $xVis->CreditoPagosAcumulados($this->mNumeroCredito);
				//$sql			= "SELECT `periodo_socio`,`fecha_de_pago`,`total`,`interes_normal`,`interes_moratorio`,`otros`,`capital`,`impuesto` FROM `creditos_abonos_parciales` WHERE `docto_afectado`=" . $this->mNumeroCredito;
			}
			//$xCache				= new cCache();
			
			$mq 				= new MQL ();
			$data 				= ($data == false) ? $mq->getDataRecord ( $sql ) : $data;
			$cnt 				= 1;
			$OProd 				= $this->getOProductoDeCredito ();
			
			$this->mMontoCapitalPagado 	= 0;
			$this->mMontoInteresPagado 	= 0;
			$this->mMontoMoraPagado 	= 0;
			$this->mMontoUltimoPago 	= 0;
			$this->mFechaUltimoPago 	= $this->getFechaDeMinistracion ();
			
			$this->mAbonosAcumulados 	= 0;
			
			if ($fecha_minis > $fecha_de_corte) {
				$this->mMessages .= "WARN\tCREDITO OMITIDO\r\n";
				// $this->mFechaUltimoMvtoCapital = $this->getFechaDeMinistracion();
			} else {
				foreach ( $data as $row ) {
					$idparcial 												= $row ["periodo_socio"];
					$fecha_op 												= $xF->getInt ( $row ["fecha_de_pago"] );
					if ($fecha_op > $fecha_de_corte) {
						$this->mMessages .= "WARN\tPARCIALIDAD $idparcial de fecha " . $row ["fecha_de_pago"] . " OMITIDA\r\n";
					} else {
						$this->mPagos[$idparcial][SYS_MONTO] 				= $row["total"];
						$this->mPagos[$idparcial][SYS_FECHA] 				= $row["fecha_de_pago"];
						$this->mPagos[$idparcial][SYS_INTERES_NORMAL] 		= $row["interes_normal"];
						$this->mPagos[$idparcial][SYS_INTERES_MORATORIO] 	= $row["interes_moratorio"];
						$this->mPagos[$idparcial][SYS_VARIOS] 				= $row["otros"];
						$this->mPagos[$idparcial][SYS_CAPITAL] 				= $row["capital"];
						$this->mPagos[$idparcial][SYS_IMPUESTOS] 			= $row["impuesto"];
						
						// $this->mMessages .= "OK\t$idparcial\t" . $row["fecha_de_pago"] . "\t" . $row["capital"] . "\t" . $row["interes_normal"] . "\t" . $row["total"] ."\r\n";
						
						// elegir si es plan de pagos
						if ($this->getPeriocidadDePago () == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
							$this->mPagos [$idparcial] [SYS_ESTADO] = CREDITO_ESTADO_VIGENTE;
						} else {
							// $fecha_de_pago = $xPlan->getFechaDePago($fecha_de_referencia, $simletras1);
							$fecha_de_pago = $this->getFechaEstimadaPorNumeroDePago ( $idparcial );
							// Agregar tolerancia de pagos
							$fecha_de_pago = $xF->setSumarDias ( $OProd->getDiasTolerados (), $fecha_de_pago );
							$this->mPagos [$idparcial] [SYS_ESTADO] = CREDITO_ESTADO_VIGENTE;
							if ($xF->getInt ( $row ["fecha_de_pago"] ) > $xF->getInt ( $fecha_de_pago )) {
								$this->mPagos [$idparcial] [SYS_ESTADO] = CREDITO_ESTADO_VENCIDO;
								$this->mMessages .= "WARN\t$idparcial\tPago $idparcial cambiado a Vencido\r\n";
							}
							// si es vencido y la fecha de primer atraso es null
							if ($this->mPagos [$idparcial] [SYS_ESTADO] == CREDITO_ESTADO_VENCIDO and ($this->mFechaPrimerAtraso == null)) {
								$this->mFechaPrimerAtraso = $row ["fecha_de_pago"];
								$this->mMessages .= "ERROR\t$idparcial\tFecha de Primera atraso a " . $this->mFechaPrimerAtraso . " \r\n";
							}
						}
						// estado estado del credito
						$this->mMontoCapitalPagado 			+= $row[SYS_CAPITAL];
						$this->mMontoInteresPagado 			+= $row["interes_normal"];
						$this->mMontoMoraPagado 			+= $row["interes_moratorio"];
						
						if ($cnt == 1) {
							$this->mDPrimerPagoEfect = $this->mPagos [$idparcial];
						}
						$this->mDUltimoPagoEfect = $this->mPagos [$idparcial];
						
						$this->mMontoUltimoPago = $row["total"];
						if($xF->getInt($row["fecha_de_pago"]) > $xF->getInt($this->mFechaUltimoPago)){
							$this->mFechaUltimoPago 	= $row["fecha_de_pago"];
						}
						$this->mMessages 				.= "OK\t$idparcial\tFecha de ultimos datos establecidos a (" . getFMoney ( $row ["total"] ) . ") y Fecha " . $row ["fecha_de_pago"] . " \r\n";
						$this->mAbonosAcumulados 		+= $row["capital"];
						$this->mFechaAcumulada 			= $row["fecha_de_pago"];
						// SALDO INSOLUTO SEGUN LA LETRA
						$this->mPagos [$idparcial] [SYS_SALDO] = setNoMenorQueCero ( $this->getMontoAutorizado () - $this->mAbonosAcumulados );
					}
					$cnt ++;
				}
				$this->mInitPagos = true;
			}
		}
		return $this->mInitPagos;
	}
	function getDPrimerPagoEfect(){ return $this->mDPrimerPagoEfect; }
	function getDUltimoPagoEfect(){ return $this->mDUltimoPagoEfect; }
	function getTipoEnSistema(){ return $this->getOProductoDeCredito ()->getTipoEnSistema (); }
	function getSaldoAFecha($fecha = false) {
		$xF 		= new cFecha ();
		$saldo 		= $this->mSdoCapitalInsoluto;
		$fecha 		= $xF->getFechaISO($fecha);
		if ($xF->getEsActual ( $fecha ) == true) {
			$this->mFechaAcumulada 		= $this->getFechaUltimoDePago ();
			$this->mAbonosAcumulados 	= setNoMenorQueCero ( (($this->getMontoAutorizado () + $this->mTotalDispuesto) - $this->getSaldoActual()) );
			// $this->mMessages .= "WARN\tFecha NADA \r\n";
		} else {
			// Iniciar pagos
			$this->initPagosEfectuados( false, $fecha );
			$saldo = ($this->getMontoAutorizado () + $this->mTotalDispuesto) - $this->mAbonosAcumulados;
		}
		return $saldo;
	}
	function getFechaDePagoDinamica(){ return $this->mFechaAcumulada;}
	function getAbonosDinamico(){ return $this->mAbonosAcumulados;	}
	function getOficialDeSeguimiento(){	return $this->mOficialDeSeguimiento;	}
	function getOficialDeCredito(){ return  $this->mOficialDeCredito; }
	function getMOP($fecha = false, $central_de_riesgo = false) {
		$mop = "";
		$xF = new cFecha ();
		$fecha = ($fecha == false) ? fechasys () : $fecha;
		$this->initPagosEfectuados ( false, $fecha );
		if ($this->getSaldoActual ( $fecha ) <= 0) {
			$mop = "UR";
		} else {
			if ($this->getSaldoActual ( $fecha ) == $this->getMontoAutorizado ()) {
				$dias = $xF->setRestarFechas ( $fecha, $this->getFechaDeMinistracion () );
				if ($dias < 90) {
					$mop = "00";
				} else {
					$mop = "UR";
				}
			} else {
				// verificar Plan de pagos
				// if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				if ($this->getEstadoActual ( $fecha ) == CREDITO_ESTADO_VIGENTE) {
					$mop = "01";
				} else {
					$dias = setNoMenorQueCero ( $xF->setRestarFechas ( $fecha, $this->getFechaDeVencimiento () ) );
					if ($dias > 0 and $dias <= 29) {
						$mop = "02";
					} else if ($dias >= 30 and $dias <= 59) {
						$mop = "03";
					} else if ($dias >= 60 and $dias <= 89) {
						$mop = "04";
					} else if ($dias >= 90 and $dias <= 119) {
						$mop = "05";
					} else if ($dias >= 120 and $dias <= 149) {
						$mop = "06";
					} else if ($dias >= 150 and $dias <= 360) {
						$mop = "07";
					} else {
						$mop = "96";
					}
				}
				/*
				 * } else {
				 * //planes de pago
				 * }
				 */
			}
		}
		RETURN $mop;
	}
	function getEsRevolvente() {
		return false;
	}
	function getEsNomina(){
		return $this->getOProductoDeCredito()->getEsProductoDeNomina();
	}
	function getFechaDePrimerAtraso() {
		return $this->mFechaPrimerAtraso;
	}
	function getListadoDePagos() {
		return $this->mPagos;
	}
	function isAFinalDePlazo() {
		return ($this->getPeriocidadDePago () == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) ? true : false;
	}
	function isAtrasado(){ return ($this->getEstadoActual()== CREDITO_ESTADO_MOROSO  OR $this->getEstadoActual() == CREDITO_ESTADO_VENCIDO) ? true : false;	}
	function isPagable() {
		$pagable = true;
		if ($this->getEstadoActual () == CREDITO_ESTADO_AUTORIZADO or $this->getEstadoActual () == CREDITO_ESTADO_SOLICITADO or $this->getSaldoActual () <= 0) {
			$pagable = false;
		}
		if($this->getEsRechazado() == true){
			$pagable = false;
		}
		return $pagable;
	}
	function getEsPagado() {
		$pagado = false;
		if (($this->getEstadoActual() !== CREDITO_ESTADO_AUTORIZADO AND $this->getEstadoActual() !== CREDITO_ESTADO_SOLICITADO) AND $this->getSaldoActual () <= TOLERANCIA_SALDOS) {
			$pagado = true;
		}
		return $pagado;
	}
	function getOtroDatos($clave) {
		$sql = "SELECT * FROM `creditos_otros_datos` WHERE `clave_de_credito`=" . $this->mNumeroCredito . " AND `clave_de_parametro`='$clave' LIMIT 0,1";
		$D = obten_filas ( $sql );
		$valor = "";
		if (isset ( $D ["valor_de_parametro"] )) {
			$valor = $D ["valor_de_parametro"];
		}
		return $valor;
	}
	function setOtrosDatos($clave, $valor) {
		$xCat 	= new cCreditos_otros_datos ();
		$xF 	= new cFecha ();
		$sql 	= "DELETE FROM `creditos_otros_datos` WHERE `clave_de_credito`=" . $this->mNumeroCredito . " AND `clave_de_parametro`='$clave'";
		$xQL	= new MQL(); $xQL->setRawQuery($sql);
		
		$xCat->clave_de_credito ( $this->mNumeroCredito );
		$xCat->clasificacion_de_parametro ( "" );
		$xCat->clave_de_parametro ( $clave );
		$xCat->fecha_de_expiracion ( $xF->getFechaMaximaOperativa () );
		$xCat->idcreditos_otros_datos ( $xCat->query ()->getLastID () );
		$xCat->sucursal ( getSucursal () );
		$xCat->valor_de_parametro ( $valor );
		$xCat->query ()->insert ()->save ();
		$this->mMessages .= "OK\tAgregar $clave con Valor $valor\r\n";
	}
	function OCatOtrosDatos() {
		$xCat = new cCreditosOtrosDatos ();
		return $xCat;
	}
	
	function setCuandoSeActualiza($evt	= ""){
		$xCache		= new cCache();
		$xQL		= new MQL();
		$idcredito	= $this->mNumeroCredito;
		$xCache->clean(EACP_CLAVE . ".ficha.$idcredito.ext");
		$xCache->clean(EACP_CLAVE . ".ficha.$idcredito.ext.tiny");
		$xCache->clean(EACP_CLAVE . ".ficha.$idcredito.tiny");
		//Eliminar Cache de Informacion de Credito
		
		if($this->mIDCacheCredito !== ""){
			$xCache->clean($this->mIDCacheCredito);
		}
		if($this->mIDCacheFicha !== ""){
			$xCache->clean($this->mIDCacheFicha);
		}
		if($this->mIDCacheInt !== ""){
			$xCache->clean($this->mIDCacheInt);
		}
		if($evt !== MQL_DEL){
			//Cache de Montos
			if($this->getOIntereses() !== null){
				$this->getOIntereses()->setCuandoSeActualiza();
			}
			//Cuadrar por Movimientos
			$xUtil				= new cUtileriasParaCreditos();
			$xUtil->setCuadrarCreditosByMvtos($idcredito);
			$this->mMessages	.= $xUtil->getMessages();
			$xUtil				= null;
		}
		//Cuadrar Gastos de Cobranza Exigibles
		
	}
	function getDiasMaximoPorDevengarVencido() {
		return INTERES_DIAS_MAXIMO_DE_MORA;
	}
	function getDiasMaximoPorDevengarVigente() {
		return $this->getDiasAutorizados() + INTERES_DIAS_MAXIMO_A_DEVENGAR;
	}
	function getEsCreditoYaAfectado() {
		$afectar = false;
		if (($this->getEstadoActual () != CREDITO_ESTADO_AUTORIZADO and $this->getEstadoActual () != CREDITO_ESTADO_SOLICITADO)) {
			if ($this->getSaldoActual () < $this->getMontoAutorizado ()) {
				$afectar = true;
			} else {
				if ($this->getPagosSinCapital () == true) {
					if ($this->getInteresNormalPagado () > 0 or $this->getInteresMoratorioPagado () > 0) {
						$afectar = true;
					}
				}
			}
		}
		return $afectar;
	}
	function getEsDeDespedido(){
		$es	= false;
		if($this->getClaveDeProducto() == SYS_PRODUCTO_FUERA_NOMINA OR $this->getClaveDeProducto() == SYS_PRODUCTO_DESCARTADOS){
			$this->initDatosDesvinculacion();
			$es	= true;
		}
		return $es;
	}
	function getFactorRedondeo() {
		$zero = floor($this->getMontoDeParcialidad());
		$zero = round(($this->getMontoDeParcialidad() - $zero),2);
		switch($zero){
			case 0:
				$zero = 100;
				break;
			case 0.50:
				$zero = 50;
				break;
			default:
				$zero = 0;
				break;
		}
		if(MODO_MIGRACION == true){
			if(PLAN_DE_PAGOS_SIN_REDONDEO == true){
				$zero	= 0;
			} else {
				$zero	= 100;
			}
		}
		if($this->getEsArrendamientoPuro() == true){
			$zero	= 0;
		}
		if($this->getPagosAutorizados() == 1){
			$zero	= 0;
		}
		return $zero;
	}
	function getOEmpresa() {
		if ($this->mOEmpresa == null) {
			$this->mOEmpresa = new cEmpresas ( $this->getClaveDeEmpresa () );
			$this->mOEmpresa->init ();
		}
		return $this->mOEmpresa;
	}
	function setInteresNormalPagado($monto) {
		$monto	= setNoMenorQueCero($monto,2);
		$valor	= $this->obj()->interes_normal_pagado()->v();
		$monto	= $valor + $monto;
		if($this->obj()->interes_normal_pagado()->isEqualF($monto) == false){
			$this->obj()->interes_normal_pagado($monto);
			$res	= $this->obj()->query()->update()->save($this->mNumeroCredito);
		}
	}
	function setInteresNormalDevengado($monto){
		//$this->obj()->interes_diario()
	}
	function setInteresMoratorioPagado($monto) {
		$monto	= setNoMenorQueCero($monto,2);
		$valor	= $this->obj()->interes_moratorio_pagado()->v();
		$monto	= $valor + $monto;
		if($this->obj()->interes_moratorio_pagado()->isEqualF($monto) == false){
			$this->obj()->interes_moratorio_pagado($monto);
			$res	= $this->obj()->query()->update()->save($this->mNumeroCredito);
		}
	}
	function setInteresMoratorioDev() {
		
	}
	function setGastosCobranzaAfectado($monto, $disminuir = false){
		
		$monto		= setNoMenorQueCero($monto,2);
		$valor		= $this->obj()->gastoscbza()->v();
		$cantidad	= ($disminuir == false) ? $valor + $monto : $valor - $monto;
		$cantidad	= setNoMenorQueCero($cantidad);
		//Si hay cambios
		if($this->obj()->gastoscbza()->isEqualF($cantidad) == false){
			$this->obj()->gastoscbza($cantidad);
			$res		= $this->obj()->query()->update()->save($this->mNumeroCredito);
			if($disminuir == true){
				//Disminuir Operaciones
				$xOp		= new cMovimientoDeOperacion();
				$xOp->setActualizaRAW(OPERACION_CLAVE_CARGOS_COBRANZA, $monto, "-", $this->getClaveDeCredito(), $this->getClaveDePersona(), false);
				//Actualizar CreditosMontos
				$this->getOMontos()->setCargosYPenas($cantidad, false);
			}
		}
	}
	function setBonificacionesAfectado($monto, $disminuir = false, $tipo = false, $periodo = false){
		$monto		= setNoMenorQueCero($monto,2);
		$tipo		= setNoMenorQueCero($tipo);
		
		if($disminuir == true){
			$tipo	= ($tipo <= 0) ? OPERACION_CLAVE_BON_VARIAS : $tipo;
			//Disminuir Operaciones
			$xOp		= new cMovimientoDeOperacion();
			//$baseM303 	= $m[801] + $m[802] + $m[803];
			$xOp->setActualizaRAW($tipo, $monto, "-", $this->getClaveDeCredito(), $this->getClaveDePersona(), $periodo);
		}
		$this->setRevisarSaldo();		
	}	
	function getReciboDeLiquidacion(){ return $this->mReciboDeLiquidacion; }
	function getReciboDeOperacionAct(){ return $this->mReciboDeOperacion; }
	function getFechaDeOperacionAct(){ return $this->mFechaOperacion; }
	function getEsAutorizado(){
		$es			= false;
		
		if($this->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO){
			$es	= true;
		}
		return $es;
	}
	function getEsSolicitado(){
		$es			= false;
		if($this->getEstadoActual() == CREDITO_ESTADO_SOLICITADO){
			$es	= true;
		}
		return $es;
	}
	function getEsAutorizable($fecha = false){
		$valido 	= $this->getEsValido();
		if(CREDITO_CONTROLAR_POR_PERIODOS == true){
			$xPer	= new cPeriodoDeCredito($this->getClaveDePeriodoCred());
			if($xPer->init() == true){
				$xReg	= New cReglasDeCalificacion();
				$xReg->setCredito($this->getClaveDeCredito());
				$xF		= new cFecha();
				if($xF->getInt($fecha)> $xF->getInt($xPer->getFechaFinal())){
					$valido		= false;
					$this->mValidacionERRS++;
					$xReg->add($xReg->CRED_FALLA_FPERIODO);
				} else {
					$xReg->add($xReg->CRED_FALLA_FPERIODO, true);
				}
			}
		}
		return $valido;	 
	}
	function getEsValido(){ return $this->setVerificarValidez(false); }
	function getBonificaciones(){ return $this->mMontoBonificado; }
	function setRevisarSaldo($fecha = false, $DatosSaldo = false){
		$xOp			= new cCreditosOperaciones($this->getClaveDeCredito());
		$ActualSaldo	= $this->getSaldoActual($fecha);
		$ActualBonif	= $this->getBonificaciones();
		$ActualLetra	= $this->getPeriodoActual();
		$ActualGasto	= $this->getCargosDeCobranza();
		$ActualPena		= $this->getPenasPorCobrar();
		$xMontos		= $this->getOMontos();
		
		//if($xOp->getHoyEsAfectado() == true){
			if($ActualSaldo <= 0){
				
				
				$xMontos->setBonificaciones(0,0,0);
				$xMontos->setCargosYPenas(0,0);
				$xMontos->setMontosCapital(false, 0, false);
				$xMontos->setTotales(0,0);
				
				if(($ActualGasto+$ActualBonif) !== 0){
					$arrUpdate		= array("bonificaciones" => 0, "gastoscbza" => 0);
					$this->setUpdate($arrUpdate, true);
				}
			} else {
				if($xOp->initAcumOpsSdo($fecha, $DatosSaldo) == true){
					$Desembolso		= $xOp->getTotalDesembolso();
					$Disposicion	= $xOp->getTotalDispuesto();
					$Abonos			= $xOp->getTotalAbonos();
					$Pendiente		= $xOp->getTotalPendiente();
					$Saldo			= $Desembolso + $Disposicion - $Abonos;
					$Bons			= $xOp->getTotalBonInt() + $xOp->getTotalBonMora() + $xOp->getTotalBonOtros();
					$ultima			= $this->getPeriodoActual();
					$Saldo			= setNoMenorQueCero($Saldo,2);
					$Bons			= setNoMenorQueCero($Bons,2);
					$arrUpdate		= array();
					$actualizar		= false;
					if($ActualSaldo !== $Saldo){
						$xMontos->setMontosCapital($Disposicion, $Pendiente, $Abonos );
						$arrUpdate["saldo_actual"] 	=  $Saldo;
						$actualizar					= true;
					}
					if($ActualBonif !== $Bons){
						$xMontos->setBonificaciones($xOp->getTotalBonInt() , $xOp->getTotalBonMora() , $xOp->getTotalBonOtros());
						$arrUpdate["bonificaciones"] =  $Bons;
						$actualizar					= true;
					}
					//Actualizar Ultima Letra
					if($this->getPagosSinCapital() == true){
						$ultima						= $xOp->getProxLetraInt() -1;
						if($ultima !== $ActualLetra){
							$xMontos->setPeriodos($xOp->getUltLetraInt(), $xOp->getProxLetraInt());;
							$arrUpdate["ultimo_periodo_afectado"]	= $ultima;
							$actualizar								= true;
						}
						
					} else {
						$ultima		= ($this->isAFinalDePlazo() == true) ? $xOp->getNumeroAbonos() : $xOp->getProxLetraCap() -1; //Si es final, la ultima letra es el numero de pagos
						//Si la letra de pago de Interes es Menor a la Letra de Pago de Capital, se tomara la Menor
						if($this->isAFinalDePlazo() == false){
							if($xOp->getProxLetraInt()<$xOp->getProxLetraCap()){
								$ultima			= $xOp->getProxLetraInt() -1;
							}
						}
						
						
						if($ultima !== $ActualLetra){
							if($this->isAFinalDePlazo() == true){
								$xMontos->setPeriodos($xOp->getNumeroAbonos(), ($xOp->getNumeroAbonos() + 1));;
							} else {
								$xMontos->setPeriodos($xOp->getUltLetraCap(), $xOp->getProxLetraCap());;
							}
							$arrUpdate["ultimo_periodo_afectado"]	= $ultima;
							$actualizar					= true;
						}
						
					}
					
					if($this->isAFinalDePlazo() == false){
						if($xOp->initAcumOpsPlan($fecha) == true){
							$xMontos->setTotales($xOp->getTotalPlanExigible(), $xOp->getTotalPlanPend(), $xOp->getTotalPendInts() );
							$xMontos->setCuandoSeActualiza();
						}
					}
					if($actualizar == true){
						$xMontos->setCuandoSeActualiza();
						$this->setUpdate($arrUpdate, true);
					}
				}
			}
		//}
	}
	function getEsArrendamientoPuro(){ return $this->mEsLeasingPuro; }
	function getClaveDeOrigen(){ return $this->mClaveDeOrigen; }
	function getTipoDeOrigen(){ return $this->mTipoDeOrigen; }
	function getMontoDeOrigen(){ return $this->mMontoDeOrigen; }
	function getODatosOrigen(){
		if($this->mObjDatosOrigen === null){
			$this->mObjDatosOrigen	= new cCreditosDatosDeOrigen(false, $this->getClaveDeCredito());
			$this->mObjDatosOrigen->initByCredito($this->getClaveDeCredito());
		}
		return $this->mObjDatosOrigen;
	}
	
	function getDiasRemanente($Fecha = false){
		
		if($this->isPagable() == true){
			if($this->getEsVencido() == false){
				$xF		= new cFecha();
				$Fecha	= $xF->getFechaISO($Fecha);
				
				if($this->isAtrasado() == true){
					$FechaMora		= $xF->getInt($this->getFechaDeMora());
					$FechaContrato	= $xF->getInt($this->getFechaVenceContrato());
					if($FechaContrato > $FechaMora){
						$this->mDiasRemanente	= $xF->setRestarFechas($FechaContrato, $Fecha);
					} else {
						$this->mDiasRemanente	= $xF->setRestarFechas($FechaMora, $Fecha);
					}
					
				} else {
					$this->mDiasRemanente	= $xF->setRestarFechas($this->getFechaVenceContrato(), $Fecha);
				}
				$this->mDiasRemanente		= setNoMenorQueCero($this->mDiasRemanente);
			}
		}
		return $this->mDiasRemanente;
	}
	/**
	 * Devuelve una cantidad presumida por pagar en una Fecha del Vencimiento del Contrato
	 * @param string $Fecha
	 */
	function getMontoTotalPresumido($Fecha = false){
		$presumido			= 0;
		
		if($this->isAFinalDePlazo() == true){
			$InteresFuturo	= round(($this->getDiasRemanente($Fecha)  * $this->mInteresDiario),2);
			$presumido		= $this->getInteresNormalDevengado() - $this->getInteresNormalPagado() + $InteresFuturo + $this->getSaldoActual($Fecha);
		} else {
			$presumido		= $this->getOMontos()->getTotalPlanPend();
			if($presumido <=  0){
				$this->setRevisarSaldo($Fecha);
			}
			
		}

		return round($presumido,2);
	}
	function getDiasDeMora($Fecha = false){
		$xF		= new cFecha();

		$dias	= $xF->setRestarFechas($Fecha, $this->mFechaMora);
		$dias	= setNoMenorQueCero($dias);
		return  $dias;
	}
	function setTasaDeMora($tasa){
		$aParam	= array("tasa_moratorio"=> $tasa);
		$this->setUpdate($aParam);
	}
	function getPlazoEnMeses(){
		$nums	= 0;
		$d1 	= new DateTime($this->getFechaDeMinistracion());
		$d2 	= new DateTime($this->getFechaDevencimientoLegal());
		//setLog($this->getFechaDeMinistracion()  . "----" . $this->getFechaDevencimientoLegal());
		$diff	= $d1->diff($d2);
		$m		= $diff->m;
		$m		+= ($diff->y * 12);
		//setLog($diff->format('%y years, %m month, %d days'));
		
		return $m;
	}
	function getEsRenovado(){
		$es		= false;
		if($this->getTipoDeAutorizacion() == CREDITO_TIPO_AUTORIZACION_RENOVACION){
			$es	= true;
		}
		return $es;
	}
	function getEsReestructuracion(){
		$es		= false;
		if($this->getTipoDeAutorizacion() == CREDITO_TIPO_AUTORIZACION_REESTRUCTURA){
			$es	= true;
		}
		return $es;
	}
	function getClaveCredVincRenov(){ return ($this->getEsRenovado() == false) ? 0 : $this->getClaveDeOrigen(); }
	function getMontoCredVincRenov(){ return ($this->getEsRenovado() == false) ? 0 : $this->getMontoDeOrigen(); }
	private function initDatosDesvinculacion(){
		$xMem	= new cPersonasMemos();
		if($xMem->initByDoctoTipo($this->getClaveDeCredito(), MEMOS_TIPO_DESVINCULACION) == true){
			$this->mFechaDesvinculo	= $xMem->getFechaDeMemo();
		}
	}
	function getFechaDesvinculo(){ return $this->mFechaDesvinculo; }
	function getTienePagosEspeciales(){
		$tiene	= false;
		$xEsp	= new cCreditosPlanPagoEsp();
		$tiene	= ($xEsp->getCountByCredito($this->mNumeroCredito) > 0 ) ? true : false;
		
		return $tiene;
	}
} // END CLASS


class cCreditosVencidos {
	private $mClaveDeCredito 	= false;
	private $mClaveDePersona 	= false;
	private $mObjC 				= null;
	function __construct($credito, $iniciar = false) {
	}
	function setObj($obj) {
		$this->mObjC = $obj;
	}
	function getMOP(){
		//====================================
		
	}
}


class cPeriodoDeCredito {
	private $mCode		= 99;
	private $mArrDatos	= array();
	private $mInit		= false;
	private $mMessages	= "";
	private $mObj		= null;
	
	/**
	 * Inicializa la clase
	 * @param integer $codigo
	 */
	function __construct($codigo = false){
		$codigo			= setNoMenorQueCero($codigo);
		$this->mCode	= ($codigo <= 0) ? EACP_PER_SOLICITUDES : $codigo ;
		if(CREDITO_CONTROLAR_POR_PERIODOS == false){
			$xQL		= new MQL();
			$items		= $xQL->getDataValue("SELECT COUNT(*) AS `items` FROM `creditos_periodos` WHERE `idcreditos_periodos`=$codigo", "items");
			if($items <= 0){
				$xF		= new cFecha();
				$fi		= $xF->getFechaInicialDelAnno();
				$ff		= $xF->getFechaFinAnnio();
				$this->add($fi, $ff, false, $ff, 'PERIODO_GENERAL-' . $this->mCode, $this->mCode);
			}
		}
	}
	function init($data = false){
		$sqlI	= "SELECT * FROM creditos_periodos WHERE idcreditos_periodos = " . $this->mCode . " LIMIT 0,1 ";
		$this->mArrDatos	= (is_array($data)) ? $data : obten_filas($sqlI);
		if(isset($this->mArrDatos["idcreditos_periodos"])){
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function add($fecha_inicial = false, $fecha_final = false, $responsable = false,
				$fecha_reunion = false, $descripcion = "", $codigo = false ){
				$xF				= new cFecha();
				
				$fecha_inicial	= ( $fecha_inicial 	== false) ? fechasys() : $fecha_inicial;
				$anno			= ( $fecha_inicial 	== false) ? date("Y" , strtotime(fechasys()) ) : date("Y" , strtotime($fecha_inicial) ) ;
				$fecha_final	= ( $fecha_final 	== false) ? date("Y-m-d", strtotime("$anno-12-31") ) : $fecha_final;
				$responsable	= ( $responsable 	== false ) ? DEFAULT_USER : $responsable;
				$codigo			= ( $codigo 		== false ) ? $anno . "99" : $codigo;
				$descripcion	= ( $descripcion 	== "" ) ? "[$codigo]Periodo de solicitudes de Credito del " . $xF->getFechaCorta($fecha_inicial) .  " al " . $xF->getFechaCorta($fecha_final) : $descripcion;
				$fecha_reunion	= ( $fecha_reunion 	== false ) ? $xF->setSumarDias(1, $fecha_final) : $fecha_reunion;
				
		$sql	= "INSERT INTO creditos_periodos(idcreditos_periodos, descripcion_periodos, fecha_inicial, fecha_final, fecha_reunion, periodo_responsable) 
    			VALUES($codigo, '$descripcion', '$fecha_inicial', '$fecha_final', '$fecha_reunion', $responsable) ";
		$xQL	= new MQL();
		$res	= $xQL->setRawQuery($sql);
		return ($res === false) ? false : true;
		
	}
	/**
	 * Modifica el periodo de Credito Actual
	 * @param integer $nuevo_periodo
	 */
	function setCambiar($nuevo_periodo){
		$nuevo_periodo	= setNoMenorQueCero($nuevo_periodo);
		$xC				= new cConfiguration();
		$msg			= "";
		$xC->set("periodo_de_solicitudes_anterior", EACP_PER_SOLICITUDES );
		$xC->set("periodo_de_solicitudes_actual", $nuevo_periodo);
		if ( $nuevo_periodo != $xC->get("periodo_de_solicitudes_actual") ){
			$msg	.= "ERROR\tLa actualizacion del periodo ha Fallado\r\n";
		} else {
			$msg	.= "SUCESS\tLa actualizacion del periodo($nuevo_periodo) ha sido un exito!!\r\n";
		}
		return $msg;
	}
	/**
	 * Obtiene la Fecha de reunion de un Periodo de Solicitud
	 */
	function getFechaDeReunion(){
		if($this->mInit == false){ $this->init(); }
		return $this->mArrDatos["fecha_reunion"];
	}
	function getFechaFinal(){ return $this->mArrDatos["fecha_final"]; }
	function getFechaInicial(){return $this->mArrDatos["fecha_inicial"]; }
	function getEsFechaDentroDelPeriodo($fecha){
		$xF 	= new cFecha ();
		$fecha 	= $xF->getFechaISO( $fecha );
		$fecha	= $xF->getInt($fecha);
		if($this->mInit == false) { $this->init ();	}
		$fechaFinal 	= $xF->getInt($this->getFechaFinal());
		$fechaInicial	= $xF->getInt($this->getFechaInicial());
		return (($fecha >= $fechaInicial) AND ($fecha <= $fechaFinal)) ? true : false;
		//$this->mMessages .= "WARN\tLa Fecha del periodo es $fecha_final, fecha comparada es $fecha\r\n";	
	}
	function checkPeriodoVigente($fecha = false) {
		$xF 			= new cFecha ();
		$fecha 			= $xF->getFechaISO ( $fecha );
		if ($this->mInit == false) { $this->init (); }
		$fecha_final 	= $this->getFechaFinal ();
		$this->mMessages .= "WARN\tLa Fecha del periodo es $fecha_final, fecha comparada es $fecha\r\n";
		
		return ($xF->getInt ( $fecha ) > $xF->getInt ( $fecha_final )) ? false : true;
	}
	function getMessages($put = OUT_TXT) { $xH = new cHObject (); return $xH->Out ( $this->mMessages, $put );	}
	function getExistePeriodoPorFecha($fecha){
		$xF		= new cFecha();
		$fecha	= $xF->getFechaISO($fecha);
		$xQL	= new MQL();
		$data	= $xQL->getDataRow("SELECT * FROM `creditos_periodos` WHERE	('$fecha' >= `fecha_inicial` ) AND	('$fecha' <= `fecha_final`) LIMIT 0,1");
		//setLog("SELECT * FROM `creditos_periodos` WHERE	('$fecha' >= `fecha_inicial` ) AND	('$fecha' <= `fecha_final`) LIMIT 0,1");
		return $this->init($data);
	}
}

class cPlanDePagos{
	private $mPagosAutorizados	= 0;
	private $mTipoDePlanDePago	= false;
	private $mPeriocidadDePago	= false;
	private $mDiaDeAbono1		= false;
	private $mDiaDeAbono2		= false;
	private $mDiaDeAbono3		= false;
	
	private $mMessages			= "";
	private $mNumeroDePlan		= 0;
	private $mParcialidadActiva	= 0;
	private $mDatosParcialidadActiva= array();
	private $mDatosInArray		= array();

	private $mTasaDeIVA			= 0;
	private $mDatosDeCredito	= array();
	private $mDatosDeProducto	= array();
	private $mOB				= null;
	private $mORec				= null;
	private $mOCred				= null;
	private $mPagos				= array();
	private $mIsInit			= false;
	private $mTipoDeDias		= false;
	
	private $mSocio				= 0;
	private $mCredito			= 0;
		
	private $mClaveDePersona		= false;
	private $mClaveDeCredito		= false;
	private $mClaveDeGrupo			= false;
	private $mClaveDeEmpresa		= false;
	private $mMontoOperado			= 0;
	private $mFechaOperado			= false;
	private $mFechaPago				= false;
	private $mSaldoInicial			= 0;
	private $mSaldoFinal			= 0;
	private $mDiasTolerancia		= 0;
	private $mTotalParciales		= 0;
	private $mBaseIVAOperado		= 0;
	private $mParcsPends			= array();
	private $mPagosAtrasados		= 0;
	private $mMontoAtrasado			= 0;
	private $mFechaPrimerAtraso		= false;
	private $mPagosConSaldo			= 0;
	private $mFechaProximoPago		= false;
	private $mLimitPlan				= 25;
	private $mFechaPrimeraParc		= ""; 	//plan
	private $mFechaUltimaParc		= "";	//plan	
	//private $mNumeroProximoPagoo= false;
	private $mPagoSegunFecha		= false;
	private $mMontoGuardado			= 0;
	private $mInitCred				= false;
	private $mForceCeros			= false;
	/* array(letra => array(fecha => , monto => , interes =>) */ 
	//private $m
	function __construct($CodigoDePlanDePagos = 0){
		$this->mNumeroDePlan	= $CodigoDePlanDePagos;
		$this->mFechaOperado	= fechasys();
	}
	function setTipoDeDiasDepago($tipo){ $this->mTipoDeDias = $tipo; }
	function setOperarCapital($NumeroDeParcialidad, $operando){
		//AND ( socio_afectado=" . $this->getClaveDePersona() . ") AND ( docto_afectado=" . $this->getClaveDeCredito() . ")
		$sql1 = "UPDATE operaciones_mvtos 
		SET afectacion_real=(afectacion_real$operando)
		WHERE (recibo_afectado = " . $this->mNumeroDePlan . ") 
		
		AND ( periodo_socio= $NumeroDeParcialidad ) AND	( (tipo_operacion=" . OPERACION_CLAVE_PLAN_CAPITAL . ") ) ";
		$xEs				= my_query($sql1);
		$this->mMessages	.= $xEs[SYS_INFO];
	}
	function getOCredito(){
		if($this->mOCred == null){
			$xCred	= new cCredito($this->mClaveDeCredito);
			if($xCred->init() == true){ $this->mOCred	= $xCred;	}
		}
		return $this->mOCred;
	}
	function initByCredito($ClaveCredito = false){
		$ClaveCredito			= ($ClaveCredito == false) ? $this->mCredito : $ClaveCredito;
		$xCred					= new cCredito($ClaveCredito);
		if($xCred->init() == true){
			$this->mInitCred		= true;
			$this->mClaveDeCredito	= $xCred->getNumeroDeCredito();
			$this->mClaveDePersona	= $xCred->getClaveDePersona();
			$this->mClaveDeGrupo	= $xCred->getClaveDeGrupo();
			$this->mClaveDeEmpresa	= $xCred->getClaveDeEmpresa();
			$this->mTasaDeIVA		= $xCred->getTasaIVA();
			$this->mTotalParciales	= $xCred->getPagosAutorizados();
			if($this->mDiasTolerancia == 0){
				$DProducto				= $xCred->getOProductoDeCredito();
				$this->mDiasTolerancia	= $DProducto->getDiasTolerados();
			}
			if(setNoMenorQueCero($xCred->getNumeroDePlanDePagos()) > 0){
				$this->mNumeroDePlan	= $xCred->getNumeroDePlanDePagos();
				if($this->mIsInit == false){ $this->init(); }
			}
			$this->mIsInit			= true;
			$this->mOCred			= $xCred;
			
		}
		$this->mMessages		.= $xCred->getMessages(OUT_TXT);
		return $this->mNumeroDePlan;
	}
	function getParcsPendientes($inicio = 0, $fecha_de_corte = false, $data = false){
		return  $this->initParcsPendientes($inicio, $fecha_de_corte, $data);
	}
	function initParcsPendientes($inicio = 0, $fecha_de_corte = false, $data = false){
		$arrDev		= array();
		$xF			= new cFecha();
		$mql		= new MQL();
		$xVis		= new cSQLVistas();
		
		$fecha_de_corte	= $xF->getFechaISO($fecha_de_corte);
		$int_fecha		= $xF->getInt($fecha_de_corte);
		$inicio			= setNoMenorQueCero($inicio);
		$ByNum			= ($inicio == 0) ? "  AND (`operaciones_mvtos`.`periodo_socio`) > 0 " : " AND (`operaciones_mvtos`.`periodo_socio`) >= $inicio ";
		$persona		= $this->getClaveDePersona();
		$credito		= $this->getClaveDeCredito();
		$sql			= $xVis->getVistaLetras($credito, false, false, false, $ByNum);
		
		//$sql			= "SELECT * FROM `letras` WHERE docto_afectado = $credito $ByNum";
		//setLog($sql);
		$rs				= ($data == false) ? $mql->getDataRecord($sql) : $data;
		foreach($rs as $rw){
			
			$idxp											= $rw["periodo_socio"];
			$this->mParcsPends[$idxp][SYS_TOTAL]			= $rw["total_sin_otros"];
			$this->mParcsPends[$idxp][SYS_CAPITAL]			= $rw["capital"];
			$this->mParcsPends[$idxp][SYS_INTERES_NORMAL]	= $rw["interes"];
			$this->mParcsPends[$idxp][SYS_IMPUESTOS]		= $rw["iva"];
			$this->mParcsPends[$idxp][SYS_AHORRO]			= $rw["ahorro"];
			$this->mParcsPends[$idxp][SYS_VARIOS]			= $rw["otros"];
			$this->mParcsPends[$idxp][SYS_FECHA]			= $rw["fecha_de_pago"];
			$this->mParcsPends[$idxp][SYS_NUMERO]			= $idxp;
			//compatible cCreditosLetraDePago
			$this->mParcsPends[$idxp]["clave_de_credito"]	= $credito;
			$this->mParcsPends[$idxp]["fecha_de_pago"]		= $rw["fecha_de_pago"];
			$this->mParcsPends[$idxp]["interes"]			= $rw["interes"];
			$this->mParcsPends[$idxp]["impuesto"]			= $rw["iva"];
			$this->mParcsPends[$idxp]["otros_codigo"]		= $rw["clave_otros"];
			$this->mParcsPends[$idxp]["penas"]				= 0;
			$this->mParcsPends[$idxp]["mora"]				= 0;
				
			

			//setError("Corte $fecha_de_corte : $int_fecha ------ Fecha de Letra : " . $rw["fecha_de_pago"]);
			if( $xF->getInt($rw["fecha_de_pago"]) <= $int_fecha ){
				//setError("Letra $idxp -- " . $rw["fecha_de_pago"] . " : " . $xF->getInt($rw["fecha_de_pago"]));
				if(setNoMenorQueCero($rw[SYS_CAPITAL]) > 0){
					$this->mPagosAtrasados++;
					$this->mMontoAtrasado			+= $rw[SYS_CAPITAL];
					
					if($this->mFechaPrimerAtraso == false){
						$this->mFechaPrimerAtraso	= $rw["fecha_de_pago"];
						$this->mMessages			.= "WARN\tPER $idxp\tFecha de Atraso pago al " . $rw["fecha_de_pago"] . "\r\n";
					}
					$this->mPagoSegunFecha			= $rw["periodo_socio"];
				} else {
					//$this->mMessages			.= "WARN\tPER $idxp\tNo hay Capital " . $rw[SYS_CAPITAL] . "\r\n";
				}
			}
			if(setNoMenorQueCero($rw["capital"]) > 0){ $this->mPagosConSaldo++; }
			if($this->mFechaProximoPago == false){
				$this->mFechaProximoPago	= $rw["fecha_de_pago"];
				$this->mMessages			.= "OK\tPER $idxp\tFecha de proximo pago al " . $rw["fecha_de_pago"] . "\r\n";
			}
		}
		//si la fecha es mayor... cargar el periodo actual
		///setLog($this->mMessages . "\r\nPagos: " . $this->mPagosAtrasados);
		
		return $this->mParcsPends;
	}
	function getLetrasInArray($tipo, $inicio = 0){
		$arrDev		= array();
		$numeroPlan	= $this->mNumeroDePlan;
		$persona	= $this->getClaveDePersona();
		$credito	= $this->getClaveDeCredito();
		if($numeroPlan > 0 AND $persona > 0 AND $credito > 0){
			$inCache	= true;
			$xCache		= new cCache();
			$idxc		= "letra-in-array-$credito-$numeroPlan-$tipo-$inicio";
			$arrDev		= $xCache->get($idxc);
			
			if(!is_array($arrDev)){
				$inCache	= false;
				$sql1 		= "SELECT periodo_socio, afectacion_real FROM operaciones_mvtos 
				WHERE (recibo_afectado = " . $numeroPlan . ") 
				AND ( socio_afectado=" . $persona . ") AND ( docto_afectado=" . $credito . ")
				AND ( periodo_socio >= $inicio ) AND	(tipo_operacion=$tipo) ORDER BY periodo_socio";
				$xQL		= new MQL();
				//TODO: Mejorar carga a cache
				$rs			= $xQL->getRecordset($sql1);
				if($rs){
					while($rw = $rs->fetch_assoc()){
						$arrDev[$rw["periodo_socio"]]	= $rw["afectacion_real"];
					}
				}
				$xQL		= null;
				$rs			= null;
			}
			if($inCache == false){
				$xCache->set($idxc, $arrDev);
			}
		}
		return $arrDev;
	}
	function getPagosAtrasados(){ return $this->mPagosAtrasados; }
	function getMontoAtrasado(){ return $this->mMontoAtrasado; }
	function getFechaPrimerAtraso(){ return $this->mFechaPrimerAtraso; }

	function setPagosAutorizados($pagos = 0){ $this->mPagosAutorizados	= $pagos;	}
	/**
	 * @deprecated @since 2014.09.20
	 */	
	function setTipoDePlanDePago($tipo){ $this->mTipoDePlanDePago	= $tipo; }
	/**
	 * @deprecated @since 2014.09.20
	 */	
	function setPeriocidadDePago($periocidad){ $this->mPeriocidadDePago = $periocidad; }
	function getPagosConSaldo(){ return $this->mPagosConSaldo; }
	function getFechaDeProximoPago(){ return $this->mFechaProximoPago; }
	function getPeriodoProximoSegunFecha(){ return $this->mPagoSegunFecha; }
	/**
	 * @deprecated @since 2014.09.20 
	 */
	function setDiasDeAbonoFijo($dia1, $dia2 = false, $dia3 = false){
		$this->mDiaDeAbono1	= $dia1;
		$this->mDiaDeAbono2	= ($dia2 == false) ? $this->mDiaDeAbono2 : $dia2;
		$this->mDiaDeAbono3	= ($dia3 == false) ? $this->mDiaDeAbono3 : $dia3;
	}
	function setForzarCeros($forzar = true){ $this->mForceCeros = $forzar; }
	function setSaldoInicial($cantidad){ $this->mSaldoInicial = $cantidad; }
	
	function setSaldoFinal($cantidad){ $this->mSaldoFinal = $cantidad; }
	/**
	 * @deprecated @since 2014.09.20
	 */		
	function getFechaDePago($fecha_de_referencia, $numeral){
		$xGen		= new cPlanDePagosGenerador();
		$xGen->setDiasDeAbonoFijo($this->mDiaDeAbono1, $this->mDiaDeAbono2, $this->mDiaDeAbono3);
		$xGen->setPagosAutorizados($this->mPagosAutorizados);
		$xGen->setPeriocidadDePago($this->mPeriocidadDePago);
		$xGen->setTipoDePlanDePago($this->mTipoDePlanDePago);
		$xGen->setSaldoInicial($this->mSaldoInicial);
		$xGen->setSaldoFinal($this->mSaldoFinal);
		return $xGen->getFechaDePago($fecha_de_referencia, $numeral);
	}
	function setDeleteEstadisticos(){}
	function setMontoOperado($monto){$this->mMontoOperado = $monto; $this->mBaseIVAOperado = 0; }
	function setEliminar($EliminarDef = true){
		$xRec	= new cReciboDeOperacion(false, true, $this->mNumeroDePlan);
		if($xRec->init() == true){
			$xRec->setRevertir($EliminarDef);
			$this->mMessages .= $xRec->getMessages(OUT_TXT);
		}
	}
	function setEliminarOperacion($tipo = false){
		$xMvto	= new cMovimientoDeOperacion();
		if($this->mNumeroDePlan == false){
			$this->mMessages	.= "ERROR\tNo se puede eliminar la operaciones $tipo del Plan " . $this->mNumeroDePlan . "\r\n";
		} else {
			$xMvto->setEliminarRAW($tipo, $this->mClaveDeCredito, false, $this->mNumeroDePlan);
		}
		$this->mMessages	.= $xMvto->getMessages(OUT_TXT);
	}
	function init($CodigoDePlanDePagos = false, $arrDatos = false){
		$CodigoDePlanDePagos		= ( $CodigoDePlanDePagos == false ) ? $this->mNumeroDePlan : $CodigoDePlanDePagos;
		$CodigoDePlanDePagos		= setNoMenorQueCero($CodigoDePlanDePagos);
		$sql						= "SELECT * FROM operaciones_recibos WHERE idoperaciones_recibos = $CodigoDePlanDePagos LIMIT 0,1 ";
		//TODO: Verificar incidencias del inicio en cero
		if(is_array($arrDatos)){
			$this->mDatosInArray		= $arrDatos;
		} else {
			$this->mDatosInArray		= obten_filas($sql);
		}
		if(count($this->mDatosInArray) > 1 AND isset($this->mDatosInArray["idoperaciones_recibos"])){
			$this->setClaveDePersona( $this->mDatosInArray["numero_socio"] );
			$this->setClaveDeCredito( $this->mDatosInArray["docto_afectado"] );
			$this->mIsInit			= true;
			if($this->mTasaDeIVA <= 0 AND $this->mInitCred == false){
				$this->initByCredito($this->getClaveDeCredito() );
			}
			
			if($this->mORec == null){
				$this->mORec		= new cReciboDeOperacion(false, false, $CodigoDePlanDePagos);
				$this->mORec->init();
			}
		} else {
			$this->mIsInit			= false;
		}
		return $this->mIsInit;
	}
	function isInit(){return $this->mIsInit; }
	function setClaveDePersona($persona){ $this->mClaveDePersona = $persona; $this->mSocio = $persona; }
	function setClaveDeCredito($credito){ $this->mClaveDeCredito = $credito; $this->mCredito	= $credito; }
	function getClaveDePersona(){ return $this->mClaveDePersona; }
	function getClaveDeCredito(){ return $this->mClaveDeCredito; }
	function getClaveDePlan(){ return $this->mNumeroDePlan; }
	function setNumeroDeSolicitud($credito){ $this->setClaveDeCredito($credito); }
	function add($observaciones, $fecha_operacion = false){
		$fecha_operacion		= ($fecha_operacion == false) ? fechasys() : $fecha_operacion;
		$xRecN					= new cReciboDeOperacion(RECIBOS_TIPO_PLAN_DE_PAGO, true);
		$this->mNumeroDePlan	= $xRecN->setNuevoRecibo($this->mClaveDePersona, $this->mClaveDeCredito, $fecha_operacion, 0, false, $observaciones, DEFAULT_CHEQUE, TESORERIA_COBRO_NINGUNO,
				DEFAULT_RECIBO_FISCAL, $this->mClaveDeGrupo );
		$xRecN->setNumeroDeRecibo($this->mNumeroDePlan, true);
		$xRecN->setDefaultEstatusOperacion(OPERACION_ESTADO_GENERADO);
		$this->init($this->mNumeroDePlan);//, $xRecN->getDatosInArray());
		$xCred					= new cCredito($this->mClaveDeCredito);
		if($xCred->init() == true){
			$xCred->setCuandoSeActualiza();
		}
		$this->mMessages			.= $xRecN->getMessages();
		
		$this->mORec				= $xRecN; unset($xRecN);
		return $this->mNumeroDePlan;
	}
	function setNeutralizarParcialidad($NumeroDeParcialidad = 0, $eliminar = false){
		$msg	= "";
		$xMvto	= new cMovimientoDeOperacion();
		if ( setNoMenorQueCero($NumeroDeParcialidad) > 0 AND setNoMenorQueCero($this->mNumeroDePlan) > 0){
			if($eliminar == false){
				$xMvto->setNeutralizarRAW(false, false, false, $this->mNumeroDePlan, $NumeroDeParcialidad);
			} else {
				$xMvto->setEliminarRAW(false, false, false, $this->mNumeroDePlan, $NumeroDeParcialidad);
			}
			$msg	.= $xMvto->getMessages();
		} else {
			$msg	.= "ERROR\tNo existen parametros para trabajar (" .  $this->mNumeroDePlan."-$NumeroDeParcialidad)\r\n";
		}
		$this->mMessages	.= $msg;
		return $msg;
	}
	function setActualizarParcialidad($NumeroDeParcialidad, $capital = false, $interes = false, $ahorro = false,
					  $OtroClave = false, $OtroMonto = false, $AfectarCredito = true){
		$msg		= "";
		$xQL		= new MQL();
		$BWhere		= "(recibo_afectado = " . $this->mNumeroDePlan . ") AND ( periodo_socio= $NumeroDeParcialidad ) AND ";
		//AND ( socio_afectado=" . $this->mSocio . ") AND ( docto_afectado=" . $this->mCredito . ")
		if ( setNoMenorQueCero($NumeroDeParcialidad) > 0){
			if($capital !== false ){
			$sql1 = "UPDATE operaciones_mvtos SET afectacion_real=$capital
				    WHERE $BWhere  (tipo_operacion=" . OPERACION_CLAVE_PLAN_CAPITAL . ") ";
			//setLog($sql1);
				$xEs	= $xQL->setRawQuery($sql1);
				
				//Actualizar credito
				//TODO: Actualizar a tolerancia de Saldos
				if(($interes <= 0.01 && $capital <= 0.01) AND $AfectarCredito == true){
					//Obtener datos de la parcialidad
					$fechaV	= $this->getParcialidadFechaDePago($NumeroDeParcialidad);
					$sqlMA	= "UPDATE creditos_solicitud SET ultimo_periodo_afectado=$NumeroDeParcialidad,
					fecha_ultimo_mvto = '$fechaV'
					WHERE numero_solicitud=" . $this->mCredito . "
					";
					$xQL->setRawQuery($sqlMA);
				}
			}
			if($interes !== false ){
				$tasa_iva	= $this->mTasaDeIVA;
				$iva		= $interes	* $tasa_iva;
				$sql1 		= "UPDATE operaciones_mvtos SET afectacion_real=$interes  WHERE $BWhere (tipo_operacion=" . OPERACION_CLAVE_PLAN_INTERES . ") ";
				$xEs		= $xQL->setRawQuery($sql1);
				//if($iva > 0.001){		
				//actualizar IVA
				$sql1 		= "UPDATE operaciones_mvtos  SET afectacion_real=$iva  WHERE $BWhere (tipo_operacion=" . OPERACION_CLAVE_PLAN_IVA . ") ";
				$xEs		= $xQL->setRawQuery($sql1);
				//}				
			}
			if($ahorro !== false){
				$sql1 		= "UPDATE operaciones_mvtos SET afectacion_real=$ahorro WHERE $BWhere (tipo_operacion=" . OPERACION_CLAVE_PLAN_AHORRO . ") ";
				$xEs		= $xQL->setRawQuery($sql1);				
			}
			if($OtroClave !== false AND $OtroMonto !== false){
				$sql1 		= "UPDATE operaciones_mvtos SET afectacion_real=$OtroMonto WHERE $BWhere (tipo_operacion=$OtroClave) ";
				$xEs		= $xQL->setRawQuery($sql1);
			}
		}
		//$this->mMessages	.= $msg . "\r\n";
		return $msg;		
		
	}

	function getMessages($put = OUT_TXT){ $xH	 = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function getListaPagos(){ if($this->mInitPagos == false){ $this->initPagos(); } return $this->mPagos; }
	function getEnTabla($IncluyeCeros = true){
		$plan	= $this->mNumeroDePlan;
		$byCero	= ($IncluyeCeros == true) ? "" : "AND afectacion_real != 0 ";
		$sql	= " SELECT parcialidad, getFechaMX(fecha_de_pago) AS 'vence',monto  FROM `creditos_parcialidades` WHERE credito=" . $this->mCredito . " ";
		return new cTabla($sql);
	}
	function getDatosDeParcialidad($parcialidad){
		$itms		= count($this->mDatosParcialidadActiva);
		if(($parcialidad != $this->mParcialidadActiva) AND $itms > 1){
			$this->mParcialidadActiva	= $parcialidad;
			$plan						= $this->mNumeroDePlan;
			if(!isset($this->mPagos[$this->mParcialidadActiva])){
				$this->getOLetra($this->mParcialidadActiva);
			}
			
			$this->mDatosParcialidadActiva				= $this->mPagos[$this->mParcialidadActiva][SYS_DATOS];
		}
		return $this->mDatosParcialidadActiva;
	}
	function getParcialidadVencimiento($parcialidad){ $DParc	= $this->getDatosDeParcialidad($parcialidad); 	return (isset($DParc[SYS_FECHA_VENCIMIENTO])) ? $DParc[SYS_FECHA_VENCIMIENTO] : false; 	}
	function getParcialidadFechaDePago($parcialidad){ $DParc	= $this->getDatosDeParcialidad($parcialidad); 	return (isset($DParc[SYS_FECHA])) ? $DParc[SYS_FECHA] : false;	}
	function getObjRec(){ return $this->mORec; }
	function getOLetra($numero){
		$numero													= setNoMenorQueCero($numero);
		$numero													= ($numero <= 0) ? 1 : $numero;
		$xLetra													= new cParcialidadDeCredito($this->getClaveDePersona(), $this->getClaveDeCredito(), $numero);
		//setLog($this->getClaveDePersona());
		if(isset($this->mPagos[$numero])){
			$xLetra->setDatos($this->mPagos[$numero]);
		}
		$xLetra->init($this->getClaveDePersona(), $this->getClaveDeCredito(), $numero);
		$this->mPagos[$numero][SYS_INTERES_NORMAL] 					= $xLetra->getInteres();
		$this->mPagos[$numero][SYS_CAPITAL] 						= $xLetra->getCapital();
		$this->mPagos[$numero][SYS_TOTAL] 							= $xLetra->getMonto();
		$this->mPagos[$numero][SYS_FECHA]							= $xLetra->getFechaDePago();
		$this->mPagos[$numero][SYS_FECHA_VENCIMIENTO]				= $xLetra->getFechaDeVencimiento();
		
		$this->mPagos[$numero][SYS_DATOS]							= $xLetra->getDatosInArray();
		$this->mPagos[$numero][SYS_DATOS][SYS_INTERES_NORMAL]		= $this->mPagos[$numero][SYS_INTERES_NORMAL];
		$this->mPagos[$numero][SYS_DATOS][SYS_CAPITAL]				= $this->mPagos[$numero][SYS_CAPITAL];
		$this->mPagos[$numero][SYS_DATOS][SYS_TOTAL]				= $this->mPagos[$numero][SYS_TOTAL];
		$this->mPagos[$numero][SYS_DATOS][SYS_FECHA]				= $this->mPagos[$numero][SYS_FECHA];
		$this->mPagos[$numero][SYS_DATOS][SYS_FECHA_VENCIMIENTO]	= $this->mPagos[$numero][SYS_FECHA_VENCIMIENTO];
		$this->mMessages											.= $xLetra->getMessages();
		return $xLetra;
	}
	function setCambiarRelacionados($NuevoCredito = false, $NuevaPersona = false){
		$xRec	= new cReciboDeOperacion(RECIBOS_TIPO_PLAN_DE_PAGO, true, $this->mNumeroDePlan);
		$xRec->setCambiarRelacionados($NuevoCredito, $NuevaPersona, true);
		$this->mMessages		.= $xRec->getMessages(OUT_TXT);
	}
	function addMvtoDeCapital($monto, $fecha, $letra){
		if($this->addMvto($monto, OPERACION_CLAVE_PLAN_CAPITAL, $fecha, $letra) !=  false){
			$this->mMontoOperado	-= $monto;
			if($letra == $this->mTotalParciales){
				$this->mORec->setFinalizarRecibo(true);
			}
		}
		return $this->mMontoOperado;
	}
	function addMvtoDeAhorro($monto, $fecha, $letra){
		if($this->addMvto($monto, OPERACION_CLAVE_PLAN_AHORRO, $fecha, $letra) !=  false){
			$this->mMontoOperado	-= $monto;
		}
		return $this->mMontoOperado;
	}
	function addMvtoDeIVA($fecha, $letra, $monto = false){
		$monto	= ($monto === false) ? round(($this->mBaseIVAOperado * $this->mTasaDeIVA), 2) : $monto;
		if($this->addMvto($monto, OPERACION_CLAVE_PLAN_IVA, $fecha, $letra) !=  false){
			$this->mMontoOperado	-= $monto;
			if($this->mTasaDeIVA > 0){
				$this->mBaseIVAOperado += $monto;
			}
		}
		return $this->mMontoOperado;
	}
	function addMvtoDeInteres($monto, $fecha, $letra){
		if($this->addMvto($monto, OPERACION_CLAVE_PLAN_INTERES, $fecha, $letra) !=  false){
			$this->mMontoOperado	-= $monto;
			if($this->mTasaDeIVA > 0){
				$this->mBaseIVAOperado += $monto;
			}
		}
		return $this->mMontoOperado;
	}
	function addMvtoOtros($monto, $fecha, $letra, $operacion, $extraerIVA = false){
		if($monto > 0){
			if($extraerIVA == true){
				$monto		= round( ($monto * (1/(1+$this->mTasaDeIVA))), 2);
			}
			if($this->addMvto($monto, $operacion, $fecha, $letra) !=  false){
				$this->mMontoOperado	-= $monto;
				if($this->mTasaDeIVA > 0){
					$this->mBaseIVAOperado += $monto;
				}
			}
		}
		return $this->mMontoOperado;
	}
	function addBonificacion($monto, $fecha, $letra, $operacion, $extraerIVA = false){
		if($monto > 0){
			if($extraerIVA == true){
				$monto		= round( ($monto * (1/(1+$this->mTasaDeIVA))), 2);
			}
			$montooperado	= $monto; //($monto -1);
			if($this->addMvto($montooperado, $operacion, $fecha, $letra, -1) !=  false){
				$this->mMontoOperado	+= $monto;
				if($this->mTasaDeIVA > 0){
					$this->mBaseIVAOperado -= $monto;
				}
			}
		}
		return $this->mMontoOperado;
	}	
	function addMvto($monto, $tipo, $fecha_de_pago, $letra, $afectacion = 1){
		$operacion		= false;
		if ($this->mORec != null){
			$fecha_operacion	= $this->mFechaOperado;
			$vencimiento		= $this->getCalculoFechaVenceParcialidad($fecha_de_pago);
			$this->mORec->setForzarCeros($this->mForceCeros);
			if($monto == 0){
				if($this->mForceCeros == true){
					$operacion 		= $this->mORec->setNuevoMvto($fecha_operacion, $monto, $tipo, $letra, "", $afectacion, false, false, false, $fecha_de_pago, $vencimiento, $this->mSaldoInicial, $this->mSaldoFinal);
					$this->mMontoGuardado	= $monto;					
				}
			} else {
				$operacion 		= $this->mORec->setNuevoMvto($fecha_operacion, $monto, $tipo, $letra, "", $afectacion, false, false, false, $fecha_de_pago, $vencimiento, $this->mSaldoInicial, $this->mSaldoFinal);
				$this->mMontoGuardado	= $monto;				
			}
		} else {
			
		}
		return ( setNoMenorQueCero($operacion) == 0) ? false : $operacion;
	}
	function getMontoGuardado(){ return $this->mMontoGuardado; }
	function getMontoOperado(){ return $this->mMontoOperado; }
	function getCalculoFechaVenceParcialidad($fecha = false){
		$fecha		= ($fecha == false) ? $this->mFechaPago : $fecha;
		$xF			= new cFecha();
		return $xF->setSumarDias($this->mDiasTolerancia, $fecha);
	}
	/**
	 * @deprecated @since 2014.09.20
	 */	
	function getFactorIVA($incluido = false){
		$factor		= 1;
		if($this->mTasaDeIVA > 0 AND $incluido == true){	$factor	= 1 * (1 / (1 + $this->mTasaDeIVA));		}
		return $factor;
	}
	function getFicha(){ $xF = ""; if($this->mORec != null){ $xF = $this->getObjRec()->getFicha(true);} return $xF;	}
	function calcular($fecha_inicial = false, $PrimerPago = false, $SegundoPago = false, $TercerPago = false ){
		$xCred					= new cCredito($this->mClaveDeCredito); $xCred->init();
		$xF						= new cFecha();
		$msg					= "";
		$FORMA_DE_PAGO			= $xCred->getFormaDePago();
		$PAGOS_AUTORIZADOS		= $xCred->getPagosAutorizados();
		$PERIOCIDAD_DE_PAGO		= $xCred->getPeriocidadDePago();
		$MONTO_AUTORIZADO		= $xCred->getMontoAutorizado();
		$SALDO_ACTUAL			= $xCred->getSaldoActual();
		$INTERES_PAGADO			= $xCred->getInteresNormalPagado();
		$MORA_PAGADO			= $xCred->getInteresMoratorioPagado();
		$xCred->initPagosEfectuados();
		$CAPITAL_PAGADO			= setNoMenorQueCero( ($MONTO_AUTORIZADO - $SALDO_ACTUAL) );
		$SALDO_DE_PLAN			= $xCred->getMontoAutorizado();
		$TASA_NORMAL			= $xCred->getTasaDeInteres();
		$TASA_MORA				= $xCred->getTasaDeMora();
		$TASA_IVA				= $xCred->getTasaIVA();
		$DIVISOR_DE_INTS		= EACP_DIAS_INTERES;
		$FECHA_DE_PAGO			= $xCred->getFechaPrimeraParc();
		$FECHA_INICIAL			= $xCred->getFechaDeMinistracion();
		$opciondia_1			= $PrimerPago;
		$opciondia_2			= $SegundoPago;
		$opciondia_3			= $TercerPago;
		
		//sanitiza la fecha de pago
		if($FECHA_DE_PAGO == false){
			$FECHA_DE_PAGO		= $xF->setSumarDias($PERIOCIDAD_DE_PAGO, $FECHA_INICIAL);
		}
		if( (($PERIOCIDAD_DE_PAGO > CREDITO_TIPO_PERIOCIDAD_CATORCENAL OR $PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_CATORCENAL)) AND ($opciondia_1 == false OR $opciondia_2 == false OR $opciondia_3 == false) ){
		
			if($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_QUINCENAL){
				$opciondia_1	= ($opciondia_1 == false) ? PQ_DIA_PRIMERA_QUINCENA : $opciondia_1;
				$opciondia_2	= ($opciondia_2 == false) ? PQ_DIA_SEGUNDA_QUINCENA : $opciondia_2;
			} elseif ($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_DECENAL){
				$opciondia_1	= ($opciondia_1 == false) ? 10 : $opciondia_1;
				$opciondia_2	= ($opciondia_2 == false) ? 20 : $opciondia_2;
				$opciondia_3	= ($opciondia_3 == false) ? 30 : $opciondia_3;			
			} else {
				$opciondia_1	= ($opciondia_1 == false) ? PM_DIA_DE_PAGO : $opciondia_1;
			}
		}

		//pagos decenales entre el mes
		$arrPagos				= array();
		//obtener los dias de pago en el mes por tipo de pago
		$dia_de_pago			= $xCred->getFechaDeMinistracion();
		for($i = 1; $i <= $PAGOS_AUTORIZADOS; $i++){
			$letra				= $i;
			switch($PERIOCIDAD_DE_PAGO){
				case CREDITO_TIPO_PERIOCIDAD_DIARIO:
					$dia_de_pago					= $xF->getDiaHabil( $xF->setSumarDias(1, $dia_de_pago) );
					$arrPagos[$letra][SYS_FECHA]	= $dia_de_pago;
					$msg					.= "OK\t$letra\tDIARIO\tFecha de pago a $dia_de_pago\r\n";
					break;
				case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
					break;
				case CREDITO_TIPO_PERIOCIDAD_DECENAL:
					break;
				case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
					//dias naturales, cada dos semanas?
					break;
				case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
					$dia_de_pago					= $xF->setSumarDias(CREDITO_TIPO_PERIOCIDAD_QUINCENAL, $dia_de_pago);
					$dia_de_pago					= $xF->getDiaAbonoQuincenal($opciondia_1, $opciondia_2, $dia_de_pago);
					$arrPagos[$letra][SYS_FECHA]	= $dia_de_pago;
					$msg							.= "OK\t$letra\tQUINCENAL\tFecha de pago a $dia_de_pago, opcion $opciondia_1, $opciondia_2\r\n";
					break;
				case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
					$dia_de_pago					= date("Y-m-", $xF->getInt($dia_de_pago)). $opciondia_1;
					$dia_de_pago					= $xF->setSumarMeses(1, $dia_de_pago);
					$dia_de_pago					= $xF->getDiaHabil($dia_de_pago);
					$arrPagos[$letra][SYS_FECHA]	= $dia_de_pago;
					$msg					.= "OK\t$letra\tMENSUAL\tFecha de pago a $dia_de_pago, opcion $opciondia_1\r\n";
					break;
				case CREDITO_TIPO_PERIOCIDAD_BIMESTRAL:
					break;
				case CREDITO_TIPO_PERIOCIDAD_TRIMESTRAL:
					break;
			}
		}
		//$FORMULA_INT			= new cFormula("interes_normal");
		//dias de pago
		//si es empresa, tomar la fecha de la empresa semanal, quincenal, mensual, decenal
		//Iniciar los Pagos Actuales
		//$this->initParcsPendientes();
		$DatosDePagados			= $xCred->getListadoDePagos();
		
		/* interes capital impuestos */
		switch ($FORMA_DE_PAGO){
			case CREDITO_TIPO_PAGO_PERIODICO: //parcialidad interes + capital
				break;
			case CREDITO_TIPO_PAGO_FLAT_PARCIAL:
				for($i = 1; $i <= $PAGOS_AUTORIZADOS; $i++){
					$letra							= $i;
					$letraAnterior					= setNoMenorQueCero($letra-1);
					$PagoAnterior					= (isset($DatosDePagados[$letra])) ? $DatosDePagados[$letra] : array();
					$capital_pagado					= (isset($DPago[SYS_CAPITAL])) ? $DPago[SYS_CAPITAL] : 0;
					$SALDO_DE_PLAN					= $SALDO_DE_PLAN - $capital_pagado;
					$interes_normal					= ($MONTO_AUTORIZADO * $PERIOCIDAD_DE_PAGO) * $TASA_NORMAL / $DIVISOR_DE_INTS;
					$capital						= ($MONTO_AUTORIZADO / $PAGOS_AUTORIZADOS);
					$capital						= setNoMenorQueCero(($capital - $capital_pagado));
					$interes_pagado					= (isset($DPago[SYS_INTERES_NORMAL])) ? $DPago[SYS_INTERES_NORMAL] : 0;
					$interes_normal					= setNoMenorQueCero( ($interes_normal -  $interes_pagado) );
					$arrPagos[$letra][SYS_INTERES_NORMAL]			= $interes_normal;
					if($letra == $PAGOS_AUTORIZADOS){
						if($SALDO_DE_PLAN > 0){
							$arrPagos[$letra][SYS_CAPITAL] = $SALDO_DE_PLAN;
							$msg							.= "WARN\t$letra\tCapital a ultimo pago $SALDO_DE_PLAN \r\n";
						}
					}
					//determinar proxima fecha de pago
					$msg									.= "OK\t$letra\tInteres Normal en $interes_normal, Pagado $interes_pagado \r\n";
				}			
				break;
			case CREDITO_TIPO_PAGO_INTERES_PERIODICO:
				for($i = 1; $i <= $PAGOS_AUTORIZADOS; $i++){
					$letra							= $i;
					$letraAnterior					= setNoMenorQueCero($letra-1);
					$DAnterior						= (isset($DatosDePagados[$letraAnterior])) ? $DatosDePagados[$letraAnterior] : array();
					$capital_pagado					= (isset($DAnterior[SYS_CAPITAL])) ? $DAnterior[SYS_CAPITAL] : 0;
					$SALDO_DE_PLAN					= $SALDO_DE_PLAN - $capital_pagado;
					$DPago							= (isset($DatosDePagados[$letra])) ? $DatosDePagados[$letra] : array();
					$interes_pagado					= (isset($DPago[SYS_INTERES_NORMAL])) ? $DPago[SYS_INTERES_NORMAL] : 0;
					//datos del pago anterior
					$DLetraAnterior					= (isset($arrPagos[$letraAnterior])) ? $arrPagos[$letraAnterior] : array();
					$DLetra							= (isset($arrPagos[$letra])) ? $arrPagos[$letra] : array();
					
					$FechaAnterior					= (isset($DLetraAnterior[SYS_FECHA])) ? $DLetraAnterior[SYS_FECHA] : $xCred->getFechaDeMinistracion();
					$FechaActual					= (isset($DLetra[SYS_FECHA])) ? $DLetra[SYS_FECHA] : false;
					$DIAS_TRANSCURRIDOS				= $xF->setRestarFechas($FechaActual, $FechaAnterior);
					//INTERES NORMAL
					$interes_normal					= (($SALDO_DE_PLAN * $DIAS_TRANSCURRIDOS) * $TASA_NORMAL) / $DIVISOR_DE_INTS;
					$interes_normal					= setNoMenorQueCero( ($interes_normal -  $interes_pagado) );
					$arrPagos[$letra][SYS_INTERES_NORMAL] = $interes_normal;
					if($capital_pagado > 0){
						$msg							.= "WARN\t$letra\tCapital en pago $letraAnterior de $capital_pagado \r\n";
					}
					if($letra == $PAGOS_AUTORIZADOS){
						if($SALDO_DE_PLAN > 0){
							$arrPagos[$letra][SYS_CAPITAL] = $SALDO_DE_PLAN;
							$msg							.= "WARN\t$letra\tCapital a ultimo pago $SALDO_DE_PLAN \r\n";
						}
					}
					$msg									.= "OK\t$letra\tInteres Normal en $interes_normal, Pagado $interes_pagado, Dias $DIAS_TRANSCURRIDOS,Fecha : Actual $FechaActual Anterior $FechaAnterior\r\n";
				}				
				break;
			case CREDITO_TIPO_PAGO_INTERES_COMERCIAL:
				for($i = 1; $i <= $PAGOS_AUTORIZADOS; $i++){
					$letra							= $i;
					$letraAnterior					= setNoMenorQueCero($letra-1);
					$DAnterior						= (isset($DatosDePagados[$letraAnterior])) ? $DatosDePagados[$letraAnterior] : array();
					$capital_pagado					= (isset($DAnterior[SYS_CAPITAL])) ? $DAnterior[SYS_CAPITAL] : 0;
					$SALDO_DE_PLAN					= $SALDO_DE_PLAN - $capital_pagado;
					$DPago							= (isset($DatosDePagados[$letra])) ? $DatosDePagados[$letra] : array();
					$interes_pagado					= (isset($DPago[SYS_INTERES_NORMAL])) ? $DPago[SYS_INTERES_NORMAL] : 0;
					//INTERES NORMAL
					$interes_normal					= (($SALDO_DE_PLAN * $PERIOCIDAD_DE_PAGO) * $TASA_NORMAL) / $DIVISOR_DE_INTS;
					$interes_normal					= setNoMenorQueCero( ($interes_normal -  $interes_pagado) );
					$arrPagos[$letra][SYS_INTERES_NORMAL] = $interes_normal;
					if($capital_pagado > 0){
						$msg							.= "WARN\t$letra\tCapital en pago $letraAnterior de $capital_pagado \r\n";
					}
					if($letra == $PAGOS_AUTORIZADOS){
						if($SALDO_DE_PLAN > 0){
							$arrPagos[$letra][SYS_CAPITAL] = $SALDO_DE_PLAN;
							$msg							.= "WARN\t$letra\tCapital a ultimo pago $SALDO_DE_PLAN \r\n";
						}
					}
					$msg									.= "OK\t$letra\tInteres Normal en $interes_normal, Pagado $interes_pagado \r\n";
				}
				break;
		}
		$NumeroPlan			= $this->mNumeroDePlan; 
		//$this->setEliminar();
		//eliminar plan de pagos
		foreach ($arrPagos as $periodo){
			
		}
		$this->mMessages	.= $msg;
		return $msg;
	}
	function getCodigoDePlanDePagos(){ return $this->mNumeroDePlan; }
	function getFechaPlanPrimerPago(){ return $this->mFechaPrimeraParc; }
	function getFechaPlanUltimoPago(){ return $this->mFechaUltimaParc; }
	function getVersionImpresa($ConFichas = false, $ConFirmas = false, $simple = false, $tools = false, $mostrar = true, $conSdoCap = false){
		$xF				= new cFecha();
		$xL				= new cLang();
		$xQ				= new MQL();
		$xNot			= new cHNotif();
		$xList			= new cSQLListas();
		$xRuls			= new cReglaDeNegocio();
		$xFor			= new cFormula();
		//$xUsr			= new cSystemUser();$xUser->in
		$ConCeros		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_CON_CEROS);		//regla de negocio
		$LetraFija		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PAGO_LETRAF);
		$xUsr			= new cSystemUser(); $xUsr->init();
		$xF->init(FECHA_FORMATO_MX);
		$xF->setSeparador("/");
		$html			= "";
		$idplan			= $this->getCodigoDePlanDePagos();
		$xCred			= $this->getOCredito();
		$mTotalPagos	= $xCred->getPagosAutorizados();
		$mHead			= "";	//cabeza
		$mFoot			= "";	//foot
		$PlanHead		= "";
		$PlanBody		= "";
		$PlanFoot		= "";
		$nombre_otro	= "";
		$SumTotal		= 0;
		$idcredito		= $xCred->getNumeroDeCredito();
		$tasa_iva		= $xCred->getTasaIVA();
		$proximo		= $xCred->getPeriodoActual() + 1;
		//$isTDOtros		= false;				//Activar TD Otros
		$isTDIVA		= false;				//Activar TD IVA
		//$isTDAhorro		= false;				//Activar TD Ahorro
		//protected $mFechaPrimeraParc			= ""; 	//plan
		//protected $mFechaUltimaParc			= "";	//plan
		$isSoloInt		= $xCred->getPagosSinCapital();
		$isAfectado		= $xCred->getEsCreditoYaAfectado();
		$EsArrendamiento= $xCred->getEsArrendamientoPuro();
		$mTotalSaldo	= ($isAfectado == true) ? $xCred->getSaldoActual() : $xCred->getMontoAutorizado();
		$mTotalCap		= 0;
		if($idplan > 0){
			if($ConFichas == true OR $ConFirmas == true){
				//Iniciar Persona
				$xSoc				= new cSocio($xCred->getClaveDePersona());
				$xSoc->init();
				$nombre				= $xSoc->getNombreCompleto();
				if($ConFichas == true){
					$mHead 			.= $xSoc->getFicha(true);
					$mHead 			.= $xCred->getFicha();
				}
				if($ConFirmas == true){
					$OOficial		= $xCred->getOOficial();
					$nombre_oficial	= $OOficial->getNombreCompleto();
					$mFoot			= "<table><tr><td><center>" . $xL->getT("TR.Firma del Solicitante") ."</td><td><center>" . $xL->getT("TR.Genera el Plan de Pagos") ."</center></td></tr>
									<tr><td><br /><br /><br /> </td></tr><tr><td><center>$nombre</center></td><td><center>$nombre_oficial</center></td></tr></table>";
				}
			}				
			if ( $simple == false ){
				$sql		= $xList->getListadoDeParcialidades($idplan);
				//setLog($sql);
				$rsIDX 		= $xQ->getDataRecord($sql);
				$rs			= array();
				$trs 		= "";
				$SCap 	= 0; $SAho = 0; $SInt = 0; $SIva = 0; $SOtros = 0; $SDesc = 0; $SMora = 0; $SPenas = 0; $SIvaOtros = 0;
				$cssMoneda	= " class='mny'";
				$tieneOtros	= false;
				$tieneAhorro= false;
				$arrExists	= array();	//items existentes
				

				if($tools == true){
					foreach ($rsIDX as $rw){
						if($rw["otros"] > 0){
							$tieneOtros	= true;
						}
						$rs[$rw["periodo"]] = $rw;
					}
					for($idx = 1; $idx <= $xCred->getPagosAutorizados(); $idx++){
						if(!isset($rs[$idx])){
							$xPO		= new cCreditosPlanDePagosOriginal();
							$fallFecha	= false;
							if($xPO->initByCredito($idcredito, $idx) == true){
								$fallFecha	= $xPO->getFechaDePago();
							}
							$rs[$idx] = array("fecha" => $fallFecha, 
									"periodo" => $idx,
									"capital" => 0,
									"interes" => 0,
									"ahorro" => 0,
									"iva" => 0,
									"descuentos" => 0,
									"otros" => 0,
									"total" => 0,
									"saldo" => 0
									); 
						}
					}
					ksort($rs);
					$rsIDX	= null;
				} else {
					foreach ($rsIDX as $rw){
						if($rw["otros"] > 0){
							$tieneOtros	= true;
						}
					}
					$rs 	= $rsIDX;
					$rsIDX	= null;
				}
				$arrExists		= null;
				//Array de Pagos hechos
				$arrPagos 		= array();
				if($tools == true){
					$sqlPagos	= "SELECT `operaciones_mvtos`.`periodo_socio` AS `periodo`,`operaciones_mvtos`.`recibo_afectado` AS `recibo`, CONCAT(`operaciones_mvtos`.`recibo_afectado`,  ' - ', getFechaMX(`operaciones_mvtos`.`fecha_afectacion`),  ' - ', `operaciones_recibos`.`total_operacion`  ) AS `descripcion` 
								FROM `operaciones_mvtos` `operaciones_mvtos` INNER JOIN `operaciones_recibos` `operaciones_recibos` ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.`idoperaciones_recibos` 
								WHERE	(`operaciones_recibos`.`tipo_docto` =2) AND (`operaciones_mvtos`.`docto_afectado` =$idcredito)";
					$rsp		= $xQ->getDataRecord($sqlPagos);
					foreach ($rsp as $prw){
						$arrPagos[$prw["periodo"]][$prw["recibo"]] = $prw["descripcion"];
						//$arrPagos[$prw["periodo"]]
					}
					$rsp		= null;
				}
				
				foreach ($rs as $rw){
					$tds		= "";
					//================= VALORES
					$fecha		= $rw["fecha"];
					$idparc		= $rw["periodo"];
					$capital	= setNoMenorQueCero($rw["capital"],2);
					
					
					$interes	= $rw["interes"];
					$ahorro		= $rw["ahorro"];
					$iva		= $rw["iva"];
					$descto		= $rw["descuentos"];
					$otros		= $rw["otros"];
					$total		= $rw["total"];
					$saldo		= setNoMenorQueCero($rw["saldo"],2);
					$mTotalCap += $capital;
					$sdocapital	= ($mTotalSaldo - $mTotalCap);
					$dia_semana	= $xF->getDayName($fecha);
					//WsetLog("$idparc --- $fecha");
				
					//================== SUMAS
					$SDesc		+= $descto;
					$SInt		+= $interes;
					$SCap		+= $capital;
					$SAho		+= $ahorro;
					$SIva		+= $iva;
					$SOtros		+= $otros;
					$trCSS		= ($idparc == $proximo) ? " class='notice'" : "";
					//========== Ajustar Saldo
					if($mTotalPagos == $idparc){
						if($EsArrendamiento == false){
							if($sdocapital > 0 ){
								$sdocapital	= setNoMenorQueCero(( $mTotalSaldo - $SCap),2);
							}
							if($saldo > 0){
								$saldo		= 0;
							}
						}
						//Guardar Remanente
					}					
					$tds		.= "<tr$trCSS><th>" . $idparc . "</th>";
					$tds		.= "<td>" . $dia_semana . "</td>";
					$tds		.= "<td>" . $xF->getFechaCorta($fecha) . "</td>";
					
					$tds		.= "<td$cssMoneda>" . getFMoney($capital) . "</td>";
					$tds		.= "<td$cssMoneda>" . getFMoney($interes) . "</td>";
					if($tasa_iva > 0 OR $SIva > 0 OR $iva > 0){
						$tds	.=  "<td$cssMoneda>" . getFMoney($iva) . "</td>";
					}
					if($SAho > 0 OR $ahorro > 0 ){
						$tds	.=  "<td$cssMoneda>" . getFMoney($ahorro) . "</td>";
					}
					//($SOtros > 0 OR $otros > 0) OR					
					if( $tieneOtros == true ){
						$tds	.=  "<td$cssMoneda>" . getFMoney($otros) . "</td>";
					}
					if($SDesc != 0 OR $descto != 0){
						$tds	.=  "<td$cssMoneda>(" . getFMoney($descto) . ")</td>";
					}
					
					
					if($tools == false){
					//Total
					$tds	.= "<td$cssMoneda>" . getFMoney($total) . "</td>";
					$tds	.= ($conSdoCap == true) ? "<td$cssMoneda>" . getFMoney($sdocapital) . "</td>" : "<td$cssMoneda>" . getFMoney($saldo) . "</td>";
					}
//============ Herramientas					
					if($tools == true){
						$nnivel	= $xUsr->getTipoEnSistema();
						//==== Penas y Mora
						$mora 		= 0;
						$penas 		= 0;
						$ivaotros	= 0;
						if(($xF->getInt($fecha) < $xF->getInt(fechasys())) AND $total >0){
							
							$xParc	= new cParcialidadDeCredito($xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), $idparc);
							$xParc->setIDProducto($xCred->getClaveDeProducto());
							$xParc->setTasaMora($xCred->getTasaDeMora());
							$xParc->setPeriocidadDePago($xCred->getPeriocidadDePago());
							$DD							= $rw;
							$DD["letra"]				= $rw["total"];
							$DD["periodo_socio"]		= $rw["periodo"];
							$DD["fecha_de_pago"]		= $rw["fecha"];
							$DD["docto_afectado"]		= $xCred->getNumeroDeCredito();
							$DD["total_sin_otros"]		= $rw["total"] - $rw["otros"];
							$DD["fecha_de_vencimiento"]	= $xF->setSumarDias(1, $rw["fecha"]);
							
							//$xParc->setDatos($DD);
							
							//if($isAfectado == true){
								if($xParc->init() == true){
									$DMora	= $xParc->setCalcularPenas_Y_Mora(fechasys(), true);
									$mora	= $DMora[SYS_INTERES_MORATORIO];
									$penas	= $DMora[SYS_PENAS];
								}
								
								$SMora	+= $mora;
								$SPenas	+= $penas;
							//}
							
						}
						$ivaotros	= round( (($mora+$penas)*TASA_IVA),2);
						$SIvaOtros	+= $ivaotros;
						
						$tds	.= "<td class='mny'>" . getFMoney(($mora+$penas+$ivaotros)) . "</td>";
						$total	= $total + $mora + $penas + $ivaotros;
												
						$tds	.= "<td$cssMoneda>" . getFMoney($total) . "</td>";
						$tds	.= ($conSdoCap == true) ? "<td$cssMoneda>" . getFMoney($sdocapital) . "</td>" : "<td$cssMoneda>" . getFMoney($saldo) . "</td>";						
						
						$xMin	= new cHButton();
						$xLi	= new cHUl("idtto", "ul", "tags blue"); $xLi->setTags("");
						//si son pagos anteriores en cache
						
						if(isset($arrPagos[$idparc])){
							$xSel	= new cHSelect("idrecibos-$idcredito-$idparc", $arrPagos[$idparc]);
							
							$xSel->setEnclose(false);
							$xLi->li($xSel->get("idrecibos-$idcredito-$idparc", ""));
							$xLi->li($xMin->getBasic("TR.Imprimir", "var xR=new RecGen();xR.formato($('#idrecibos-$idcredito-$idparc').val());", $xMin->ic()->IMPRIMIR, "", false, true));							
						}

						
						//si hay saldo por pagar
						if($total > 0 AND $xUsr->getPuedeCobrar() == true AND $LetraFija == false){
							$xLi->li($xMin->getBasic("TR.Pagar", "var xC=new CredGen();xC.goToCobrosDeCredito({credito:$idcredito,periodo:$idparc});", $xMin->ic()->DINERO, "", false, true));
						}
						
						if( $xUsr->getEnDesarrollo() == true){
							$xLi->li($xMin->getBasic("TR.Eliminar", "var xP=new PlanGen();xP.setEliminarLetra({credito:$idcredito,periodo:$idparc});", $xMin->ic()->ELIMINAR, "", false, true));
						}
						
						$ttol		= $xLi->get();
						
						$tds			.= "<td class='toolbar-24'>$ttol</td>";
					}
					$tds	.= "</tr>";

					if($total <= 0 AND $tools == false AND $ConCeros == false){
						$tds	= "";
					}
					$trs	.= $tds;
					
				}
				$rs			= null;
				//--- end sumas totales
				$PlanHead 	.= "<thead><tr><th>" . $xL->getT("TR.Pago") ."</th><th colspan='2'>" . $xL->getT("TR.Fecha de Pago") . "</th><th>" . $xL->getT("TR.Capital"). "</th><th>" . $xL->getT("TR.Interes") ."</th>";
				$PlanHead	.= ($SIva == 0) ? "" : "<th>" . $xL->getT("TR.IVA"). "</th>";
				$PlanHead	.= ($SAho == 0) ? "" : "<th>" . $xL->getT("TR.ahorro"). "</th>";
				$PlanHead	.= ($SOtros == 0) ? "" : "<th>" . $xL->getT("TR.otros"). "</th>";
				$PlanHead	.= ($SDesc == 0) ? "" : "<th>" . $xL->getT("TR.Descuentos"). "</th>";
				
				
				if($tools == false){
					$PlanHead	.="	<th>" . $xL->getT("TR.TITULO1PLAN") . "</th>";
					$PlanHead	.= ($conSdoCap == true) ? "<th>" . $xL->getT("TR.saldo capital"). "</th>" : "<th>" . $xL->getT("TR.saldo insoluto"). "</th>";
				}
				//Header.- Herramientas
				if($tools == true ){
					$PlanHead	.= "<th>" . $xL->getT("TR.CARGOS_POR_ATRASOS"). "</th>";
					$PlanHead	.="	<th>" . $xL->getT("TR.TITULO1PLAN") . "</th>";
					$PlanHead	.= ($conSdoCap == true) ? "<th>" . $xL->getT("TR.saldo capital"). "</th>" : "<th>" . $xL->getT("TR.saldo insoluto"). "</th>";					
					$PlanHead	.= "<th>" . $xL->getT("TR.Herramientas"). "</th>";
				}
				$PlanHead	.= "</tr></thead>";
				
				$PlanFoot	.="<tfoot><tr><td colspan='3'>" . $xL->getT("TR.SUMAS") . "</td><th$cssMoneda>" . getFMoney($SCap) . "</th><th$cssMoneda>" . getFMoney($SInt) . "</th>";
				$PlanFoot	.= ($SIva ==0) ? "" : "<th$cssMoneda>" . getFMoney($SIva) . "</th>";
				$PlanFoot	.= ($SAho == 0) ? "" : "<th$cssMoneda>" . getFMoney($SAho) . "</th>";
				$PlanFoot	.= ($SOtros == 0) ? "" : "<th$cssMoneda>" . getFMoney($SOtros) . "</th>";
				$PlanFoot	.= ($SDesc == 0) ? "" : "<th$cssMoneda>" . getFMoney($SDesc) . "</th>";
				$SumTotal	= $SCap + $SInt + $SIva + $SAho + $SOtros + $SDesc;
				if($tools == false){
					$PlanFoot	.="<th$cssMoneda>" . getFMoney($SumTotal) . "</th><td />";
				}
				if($SMora > 0 OR $SPenas > 0){
					//Guarda el Monto Global de Penas
					$xMon 		= new cCreditosMontos($xCred->getClaveDeCredito());
					if($xMon->init() == true){
						$xMon->setCargosYPenas(0, $SPenas);
						
					}
				}
				if($tools == true AND getUsuarioActual(SYS_USER_NIVEL)>= USUARIO_TIPO_OFICIAL_CRED){
					$SumTotal	= $SumTotal + $SMora + $SPenas + $SIvaOtros;
					$PlanFoot	.="<th$cssMoneda>" . getFMoney(($SMora+$SPenas+$SIvaOtros)) . "</th>";
					$PlanFoot	.="<th$cssMoneda>" . getFMoney($SumTotal) . "</th>";
					$PlanFoot	.="<td />";
				}
				$PlanFoot	.="</tr></tfoot>";
				
				$html	= "$mHead<table>$PlanHead<tbody>$trs</tbody>$PlanFoot</table>$mFoot";
										
			} else {	//===== INIT Plan Simple
				$this->mLimitPlan	= ceil(($xCred->getPagosAutorizados() / 3));
				$sqlparc = "SELECT periodo_socio AS 'parcialidad', MAX(fecha_afectacion) AS 'fecha_de_pago', SUM(afectacion_real) AS 'total_parcialidad', MAX(saldo_anterior) AS 'saldo_anterior_', 
				MIN(saldo_actual) AS 'saldo_actual_'
				FROM operaciones_mvtos	WHERE recibo_afectado=$idplan	GROUP BY periodo_socio ORDER BY periodo_socio";
					
				$rs 	= $xQ->getDataRecord($sqlparc);
				$pi 	= 1;	//periodo
				$tp		= "";
				$pt		= 1;
				$lim	= $xQ->getNumberOfRows();
				//si el Limite es mayor a 100, restar 100 y poner
				if($lim > ($this->mLimitPlan * 4) ){ $this->mLimitPlan = ceil( ($lim / 4) ); }
				if($lim < $this->mLimitPlan){ $this->mLimitPlan = ceil( ($lim / 2) ); }
				$items	= 1;
					
				foreach ($rs as $rw ){
					$saldo 			= setNoMenorQueCero($rw["saldo_actual_"]);
					$SumTotal		+= $rw["total_parcialidad"];
					$ffecha_de_pago	= $xF->getFechaCorta($rw["fecha_de_pago"]);
					$fmonto			= (setNoMenorQueCero($rw["total_parcialidad"]) <= 0) ? "-" : getFMoney($rw["total_parcialidad"]);
					$fsaldo			= (setNoMenorQueCero($rw["total_parcialidad"]) <= 0) ? "-" : getFMoney($saldo);
					$fperiodo		= $rw["parcialidad"];
					if($items == 1){ $this->mFechaPrimeraParc	= $rw["fecha_de_pago"]; }	//primer pago segun el Plan de Pagos
					$flinea			= "<tr><td>$fperiodo</td><td>$ffecha_de_pago</td><td class='mny'>$fmonto</td><td class='mny'>$fsaldo</td></tr>";
					if ( ($pi == $this->mLimitPlan) OR ($pt == $lim) ){
						$tp .= "$flinea</tbody></table></td>";
						$pi = 1;
					} else if ($pi == 1){
						$tp .= "<td><table class='sector_plan_simplificado'><tbody>
							<tr><th>" . $xL->getT("TR.PAGO") ."</th><th>" . $xL->getT("TR.Fecha") . "</th><th>" . $xL->getT("TR.Monto") . "</th><th>" . $xL->getT("TR.Saldo") ."</th></tr>
							$flinea";
						$pi++;
					} else {
						$tp .= $flinea;
						$pi++;
					}
					$pt++;
					$items++;
					$this->mFechaUltimaParc		= $rw["fecha_de_pago"];
				}	//end bucle simple
				$rs		= null;
				$html	= "<table class='plan_de_pagos'><tbody>$tp</tbody>
						<!-- SUMAS --><tfoot><tr><td colspan='2'><hr />" . $xL->getT("TR.Total") . "</th><td  class='mny'><hr />" . getFMoney($SumTotal) . "</th></tr></tfoot>
						</table>";
			} 	//END Simple / Complejop			
		} else {
			$html	= $xNot->get($xL->getT("TR.No existe el plan_De_pagos"), $xNot->ERROR);
		}
		return $html;	
	}
	function setAmortizarLetras($Amortizable, $LetraInicial, $tipo){
		$DPlan			= $this->getLetrasInArray($tipo, $LetraInicial);
		$ultimaletra	= $LetraInicial;
		for($idLetra = $LetraInicial; $idLetra <= $this->mPagosAutorizados; $idLetra++){
			
			if(isset($DPlan[$idLetra]) AND $Amortizable > 0){
				$monto		= setNoMenorQueCero( $DPlan[$idLetra] );
				if($Amortizable >= $monto){
					//if($tipo == OPERACION_CLAVE_PLAN_CAPITAL){
						//$this->setNeutralizarParcialidad($idLetra);
					//} else {
						$capital	= ($tipo == OPERACION_CLAVE_PLAN_CAPITAL) ? 0 : false;
						$interes	= ($tipo == OPERACION_CLAVE_PLAN_INTERES) ? 0 : false;
						$this->setActualizarParcialidad($idLetra, $capital, $interes, false );
					//}
					$Amortizable	= setNoMenorQueCero(($Amortizable - $monto));
					$ultimaletra	= $idLetra;
				} else {
					$capital		= ($tipo == OPERACION_CLAVE_PLAN_CAPITAL) ? ($monto - $Amortizable) : false;
					$interes		= ($tipo == OPERACION_CLAVE_PLAN_INTERES) ? ($monto - $Amortizable) : false;
					$this->setActualizarParcialidad($idLetra, $capital, $interes, false );
					$ultimaletra	= $idLetra;
					$Amortizable	= 0;
				}
			}
		}
		return $ultimaletra;
	}
	function getVersionImpresaLeasing($simple = false){
		$xHT	= new cHDicccionarioDeTablas();
		return $xHT->getLeasingTablasDeRenta($this->mClaveDeCredito, $simple);
	}
}

class cProductoDeCredito {
	private $mClaveDeConvenio		= 99;
	private $mArrDatos				= array();
	private $mArrDatoExtras			= array();
	private $mInitDExtras			= false;
	private $mInitOCargos			= false;
	private $mTasaAhorro			= 0;
	private $mOB					= null;
	private $mTipoEnSistema			= false;
	private $mTipoDeIntegracion		= 1; //1 Individual 2 copropiedad 3 Grupo
	private $mObjOD					= null;
	private $mInit					= false;
	private $mFormaDePagare			= null;
	private $mFormaDeMandato		= null;
	private $mTasaComPorApertura	= 0;
	private $mMessages				= "";
	private $mNombre				= "";
	private $mDOtrosCargos			= array();
	private $mDOtrosCargosParcs		= array();
	private $mDOtrosCargosPRaw		= array();
	
	private $mDescripcion			= "";
	private $mSumaOtrosCargosP		= 0;
	private $mClaveDecaratula		= 0;
	private $mNumeroDeAvales		= 0;
	private $mRazonGarantia			= 0;
	private $mAplicaPenas			= false;
	private $mDiasTolerados			= 0;
	private $mAplicaGastosPorMora	= 0;
	private $mTasaIncluyeIVA		= false;
	private $mBaseCalculoInt		= 0;
	private $mTipoDeContratoCR		= 0;
	private $mNumeroPagosDef		= 0;
	private $mNumeroPagosMin		= 0;
	private $mNumeroPagosMax		= 0;
	private $mPeriodicidadDef		= 0;
	private $mTasaInteres			= 0;
	private $mTasaMora				= 0;
	private $mMontoMaximo			= 0;
	private $mTasaGtiaLiq			= 0;
	private $mOficialSeg			= 0; 
	private $mAplicaGtosNots		= false;
	private $mAplicaPagosEsp		= false;
	private $mMontoFondoDef			= 0;
	private $mEstatus				= "";
	private $mIDCache				= "";
	private $mTabla					= "creditos_tipoconvenio";
	
	public $COSTOS_EN_TASA			= 1;
	
	function __construct($mClaveDeConvenio = false){ $this->mClaveDeConvenio	= setNoMenorQueCero($mClaveDeConvenio); }
	function init(){
		$cT				= new cTipos();
		$xCache			= new cCache();
		$xQL			= new MQL();
		$xT				= new cCreditos_tipoconvenio();
		$code			= $this->mClaveDeConvenio;
		$idx			= $this->mTabla . "-" . $this->mClaveDeConvenio;
		$inCache		= true;
		$data			= $xCache->get($idx);
		if(!is_array($data)){
			$sql		= "SELECT * FROM creditos_tipoconvenio WHERE `idcreditos_tipoconvenio` = $code LIMIT 0,1";
			$data		= $xQL->getDataRow($sql);
			$inCache	= false;
		}
		
		if(isset($data[$xT->IDCREDITOS_TIPOCONVENIO])){
			if($inCache == false){
				$data[$xT->TASA_AHORRO]					= $cT->cFloat($data[$xT->TASA_AHORRO]);
				$data[$xT->TOLERANCIA_DIAS_NO_PAGO]		= setNoMenorQueCero($data[$xT->TOLERANCIA_DIAS_NO_PAGO]);
				$data[$xT->NUMERO_AVALES]				= setNoMenorQueCero($data[$xT->NUMERO_AVALES]);
				$data[$xT->IVA_INCLUIDO]				= setNoMenorQueCero($data[$xT->IVA_INCLUIDO]);
				$data[$xT->TIPO_DE_INTEGRACION]			= setNoMenorQueCero($data[$xT->TIPO_DE_INTEGRACION]);
				$data[$xT->NUMERO_DE_PAGOS_PREFERENTE]	= setNoMenorQueCero($data[$xT->NUMERO_DE_PAGOS_PREFERENTE]);
				//$data[$xT->]
				//$data[$xT->]							= $cT->cFloat($data[$xT->]);
				$data[$xT->INTERES_NORMAL]				= $cT->cFloat($data[$xT->INTERES_NORMAL]);
				$data[$xT->INTERES_MORATORIO]			= $cT->cFloat($data[$xT->INTERES_MORATORIO]);
				$data[$xT->MAXIMO_OTORGABLE]			= setNoMenorQueCero($data[$xT->MAXIMO_OTORGABLE]);
				$data[$xT->OFICIAL_SEGUIMIENTO]			= setNoMenorQueCero($data[$xT->OFICIAL_SEGUIMIENTO]);
				if($data[$xT->OFICIAL_SEGUIMIENTO]<= 0){
					$data[$xT->OFICIAL_SEGUIMIENTO]		= DEFAULT_USER;
				}
				$data[$xT->APLICA_GASTOS_NOTARIALES]	= setNoMenorQueCero($data[$xT->APLICA_GASTOS_NOTARIALES]);
				
			}
			
			//Guardar en Cache
			$this->mArrDatos		= $data;
			$xT->setData($data);
			
			
			
			
			$this->mClaveDeConvenio		= $data[$xT->IDCREDITOS_TIPOCONVENIO];
			$this->mTasaAhorro			= $data[$xT->TASA_AHORRO];
			$this->mDiasTolerados		= $data[$xT->TOLERANCIA_DIAS_NO_PAGO];
			
			$this->mTipoEnSistema		= $data[$xT->TIPO_EN_SISTEMA];
			$this->mTipoDeIntegracion	= $data[$xT->TIPO_DE_INTEGRACION];
			$this->mTasaComPorApertura	= $data[$xT->COMISION_POR_APERTURA];
			$this->mNombre				= $data[$xT->DESCRIPCION_TIPOCONVENIO];
			$this->mNumeroDeAvales		= $data[$xT->NUMERO_AVALES];
			$this->mRazonGarantia		= $data[$xT->RAZON_GARANTIA];
			$this->mAplicaGastosPorMora	= $data[$xT->APLICA_MORA_POR_COBRANZA];
			$this->mTasaIncluyeIVA		= ($data[$xT->IVA_INCLUIDO] <= 0) ? false : true;
			$this->mTipoDeIntegracion	= $data[$xT->TIPO_DE_INTEGRACION];
			$this->mBaseCalculoInt		= $data[$xT->BASE_DE_CALCULO_DE_INTERES];
			$this->mTipoDeContratoCR	= $data[$xT->CLAVE_DE_TIPO_DE_PRODUCTO];
			$this->mNumeroPagosDef		= $data[$xT->NUMERO_DE_PAGOS_PREFERENTE];
			$this->mPeriodicidadDef		= $data[$xT->TIPO_DE_PERIOCIDAD_PREFERENTE];
			$this->mNumeroPagosMin		= 1;//($this->mPeriodicidadDef !== CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) ? 2 : 1;
			$this->mNumeroPagosMax		= $data[$xT->PAGOS_MAXIMO];
			$this->mTasaInteres			= $data[$xT->INTERES_NORMAL];
			$this->mTasaMora			= $data[$xT->INTERES_MORATORIO];
			$this->mMontoMaximo			= $data[$xT->MAXIMO_OTORGABLE];
			$this->mTasaGtiaLiq			= $data[$xT->PORCIENTO_GARANTIA_LIQUIDA];
			$this->mOficialSeg			= $data[$xT->OFICIAL_SEGUIMIENTO];
			$this->mAplicaGtosNots		= ($data[$xT->APLICA_GASTOS_NOTARIALES] <= 0) ? false : true;
			$this->mMontoFondoDef		= $data[$xT->MONTO_FONDO_OBLIGATORIO];
			$this->mDescripcion			= $data[$xT->DESCRIPCION_COMPLETA];
			$this->mEstatus				= $data[$xT->ESTATUS];
			$this->mOB					= $xT;
			
			//Correccion de errores en carga de creditos Iniciales.
			if($data[$xT->TIPO_CONVENIO] !== $this->mClaveDeConvenio){
				//Se corrige.- Cagada de SQL Inicial de Creditos
				$xQL->setRawQuery("UPDATE creditos_tipoconvenio SET tipo_convenio=$code WHERE `idcreditos_tipoconvenio` = $code ");
			}
			
			if($inCache == false){
				$xCache->set($idx, $data);
			}
			
			$this->initOtrosDatos();
			$this->initOtrosCargos();
			$this->mInit				= true;
			
		} else {
			$this->mMessages			.= "ERROR\tAl iniciar el Producto $code\r\n";
		}
		return $this->mInit;
	}
	function get(){ return $this->mClaveDeConvenio;	}
	function getCodigo(){ return $this->mClaveDeConvenio;	}
	function getDatosInArray(){ $this->init(); return $this->mArrDatos;}
	function getTasaDeAhorro(){ return $this->mTasaAhorro; }
	function getOOtrosParametros(){
		if($this->mObjOD == null){
			$this->mObjOD	= new cProductoDeCreditoOtrosDatosCatalogo($this->mClaveDeConvenio);
			$this->mObjOD->init();
		}
		return $this->mObjOD;
	}
	function getOtrosParametrosInArray(){
		if($this->getOOtrosParametros() == null){
			$this->mInitDExtras		= false;
		} else {
			$this->mInitDExtras		= true;
			$this->mArrDatoExtras	= $this->getOOtrosParametros()->getDatosInArray();
		}
		return $this->mArrDatoExtras;
	}
	function getOtrosParametros($Parametro){
		if($this->mInitDExtras == false){ $this->getOtrosParametrosInArray(); }
		$Parametro				= strtoupper($Parametro);
		return isset($this->mArrDatoExtras[$Parametro]) ? $this->mArrDatoExtras[$Parametro] : "";
	}
	function setOtrosParametros($Parametro, $Valor, $FechaDeExpiracion = false){ $this->getOOtrosParametros()->set($Valor, $Parametro, false, $FechaDeExpiracion);	}
	function obj(){
		if( $this->mOB == null){ $this->init(); }
		return $this->mOB;	
	}
	function getAplicaMoraPorGastos(){ return $this->mAplicaGastosPorMora;	}
	function getAplicaPenas(){ return $this->mAplicaPenas;	}
	function getAplicaPagosEsp(){ return $this->mAplicaPagosEsp;	}
	function getDiasTolerados(){
		$DIAS_TOLERADOS = $this->mDiasTolerados;
		//TODO: Considerar Formula.- Productos de credito.- dias tolerados
		return ($DIAS_TOLERADOS == 0)? 1 : $DIAS_TOLERADOS;
	}
	function getTasaIncluyeIVA(){ return $this->mTasaIncluyeIVA; }
	function getTipoDeIntegracion(){ return $this->mTipoDeIntegracion; }
	function getTipoDeBaseCalc(){ return $this->mBaseCalculoInt; }
	function getTipoDeContratoCR(){ return $this->mTipoDeContratoCR; }
	function getPeriocidadPrefente(){ return $this->mPeriodicidadDef; }
	function getPagosMaximos(){ return $this->mNumeroPagosMax; }
	function getPagosMinimo(){ return $this->mNumeroPagosMin; }
	function getRazonGarantia(){ return $this->mRazonGarantia; }	
	function getNombre(){return $this->mNombre;}
	function getNumeroDeAvales(){ return $this->mNumeroDeAvales; }
	function getPathPagare($credito){
		$path 		= "";
		if($this->mInit == false){ $this->init(); }
		if( $this->mFormaDePagare == null){
			$path = ($this->getEsProductoDeGrupos() == true) ? "../rpt_formatos/rptgruposolidariopagare.php?id=$credito" : "../rpt_formatos/rptcreditopagare.php?solicitud=$credito";
		} else {
			$path = ($this->getEsProductoDeGrupos() == true) ? "../rpt_formatos/rptgruposolidariopagare.php?id=$credito&forma=" . $this->mFormaDePagare : "../rpt_formatos/rptcreditopagare.php?solicitud=$credito&forma=" . $this->mFormaDePagare;
		}
		return $path;
	}
	function getPathMandato($credito){
		$path 		= "";
		if($this->mInit == false){ $this->init(); }
		if( $this->mFormaDeMandato == null){
			$path 	= "../rpt_formatos/mandato_en_creditos.rpt.php?id=$credito";
		} else {
			$path 	= "../rpt_formatos/mandato_en_creditos.rpt.php?id=$credito&forma=" . $this->mFormaDeMandato;
		}
		return $path;	
	}
	function getPathCaratulaCredito($credito){
		$path 		= "";
		if($this->mInit == false){ $this->init(); }
		$this->initOtrosDatos();
		
		$idformato	= setNoMenorQueCero($this->getOOtrosParametros()->get($this->getOOtrosParametros()->CLAVE_DE_CARATULA));
		if($idformato > 0){
			$path 	= "../rpt_formatos/credito.caratula.rpt.php?credito=$credito&forma=$idformato";
		} else {
			$path 	= "../rpt_formatos/credito.caratula.rpt.php?credito=$credito";
		}
		return $path;
	}
	function getDescripcion(){ return $this->mDescripcion; }
	function getNumeroPagosPreferente(){
		$pagos	= $this->mNumeroPagosDef;
		$pagos	= ($pagos <= 0) ? $this->getPagosMinimo() : $pagos; 
		return $pagos;
	 }
	
	function getEsProductoDeNomina(){
		$res	= false;
		if($this->mTipoEnSistema == SYS_PRODUCTO_NOMINA){ $res	= true; }
		return $res;
	}
	function getEsProductoDeGrupos(){
		$res	= false;
		
		if($this->mTipoEnSistema == SYS_PRODUCTO_GRUPOS OR $this->mTipoDeIntegracion > 2){ $res	= true; }
		return $res;		
	}
	function getEsProductoDeLinea(){
		$res	= false;
		if($this->mTipoEnSistema == SYS_PRODUCTO_REVOLVENTES ){ $res	= true; }
		return $res;
	}
	
	function getEsProductoDeArrendamientoP(){
		$res	= false;
		if($this->mTipoEnSistema== SYS_PRODUCTO_ARREND){ $res = true; }
		return $res;
	}
	function getTipoEnSistema(){ return $this->mTipoEnSistema; }
	function getTasaDeInteres(){
		/*eval( $this->getPreModInteres() );*/
		$TASA_NORMAL	= $this->mTasaInteres;
		/*eval( $this->getPosModInteres() );*/
		return $TASA_NORMAL;
	}
	function getTasaDeMora(){
		/*eval( $this->getPreModInteres() );*/
		$TASA_MORA	= $this->mTasaMora;
		/*eval( $this->getPosModInteres() );*/
		return $TASA_MORA;
	}
	function initOtrosDatos(){
		$xTi		= new cTipos();
		if($this->getOOtrosParametros() !== null){
			$this->mFormaDePagare		= $this->getOOtrosParametros()->get($this->getOOtrosParametros()->CLAVE_DE_PAGARE);
			$this->mFormaDeMandato		= $this->getOOtrosParametros()->get($this->getOOtrosParametros()->CLAVE_DE_MANDATO);
			if($this->mTasaComPorApertura <= 0){
				$this->mTasaComPorApertura	= setNoMenorQueCero($this->getOOtrosParametros()->get($this->getOOtrosParametros()->TASA_DE_COMISION_AP));
			}
			$this->mAplicaPenas			= $xTi->cBool($this->getOOtrosParametros()->get($this->getOOtrosParametros()->APLICA_PENAS));
			$this->mAplicaPagosEsp		= $xTi->cBool($this->getOOtrosParametros()->get($this->getOOtrosParametros()->APLICA_PAGOS_ESP));
		}
		if($this->mTasaComPorApertura > 1){ $this->mTasaComPorApertura = $this->mTasaComPorApertura / 100; }
		return $this->getOOtrosParametros(); 
	}
	function getTasaComisionApertura(){ $this->initOtrosDatos(); return $this->mTasaComPorApertura;	}
	function getOficialDeSeguimiento(){ return $this->mOficialSeg; }
	function getTasaDeGarantiaLiquida(){ return $this->mTasaGtiaLiq; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function add($id, $nombre, $copiarde = false){
		$copiarde	= setNoMenorQueCero($copiarde);
		$xP			= new cCreditos_tipoconvenio();
		if($copiarde > 0){ $xP->setData( $xP->query()->initByID($copiarde) ) ; }
		$xP->idcreditos_tipoconvenio($id);
		$xP->tipo_convenio($id);
		$xP->descripcion_tipoconvenio($nombre);
		$xP->descripcion_completa($nombre);
		$id		= $xP->query()->insert()->save();
		if($id === false){
			$this->mMessages	.= "ERROR\tError al agregar el Producto $id con nombre $nombre, clonado de $copiarde \r\n";
		} else {
			$this->mMessages	.= "OK\tAgregado el Producto $id con nombre $nombre, clonado de $copiarde \r\n";
		}
		return ($id === false) ? false : true;
	}
	function getPreModInteres(){ return $this->obj()->pre_modificador_de_interes()->v(OUT_TXT);	}
	function getPosModInteres(){ return $this->obj()->pos_modificador_de_interes()->v(OUT_TXT);	}	
	function getFormulaInteresNormal($idproceso = "", $posEvento = false){
		if($this->mOB == null){ $this->init(); }
		
		//XXX: L4955.-core.creditos getFormulaInteresNormal : Terminar y evaluar funcionalidades
		$xStep	= new cCreditosEventos();
		$xF		= new cFormula();
		$txt	= "";
		if($this->mOB === null){
			
		} else {
			//que va aplicar
			switch ($idproceso){
				case $xStep->AUTORIZACION:
					$txt	= $this->mOB->pre_modificador_de_autorizacion()->v(OUT_TXT);
					break;
				case $xStep->MINISTRACION:
					$txt	= $this->mOB->pre_modificador_de_ministracion()->v(OUT_TXT);
					break;
				default:
					$txt	= ($posEvento == true) ? $this->mOB->pos_modificador_de_interes()->v(OUT_TXT) : $this->mOB->pre_modificador_de_interes()->v(true);
					break;
			}
		}
		return $txt;
	}
	function getFormulaInteresMoratorio($idproceso = "", $posEvento = false){
		if($this->mOB == null){ $this->init(); }
		$xStep	= new cCreditosEventos();
		$xF		= new cFormula();
		$txt	= "";
		
		//que va aplicar
		if($this->mOB === null){
			
		} else {
			switch ($idproceso){
				case $xStep->AUTORIZACION:
					$txt	= $this->mOB->pre_modificador_de_autorizacion()->v(OUT_TXT);
					break;
				case $xStep->MINISTRACION:
					$txt	= $this->mOB->pre_modificador_de_ministracion()->v(OUT_TXT);
					break;
				default:
					$txt	= ($posEvento == true) ? $this->mOB->pos_modificador_de_interes()->v(OUT_TXT) : $this->mOB->pre_modificador_de_interes()->v(true);
					break;
			}
		}
		return $txt;	
	}
	function getFormulaDePena($idproceso = "", $posEvento = false){
		$txt	= "";
		if($this->mAplicaPenas == true){
			$xStep	= new cCreditosEventos();
			$xFor	= new cFormula();
			$xFor->init($xFor->PHP_PENA_X_LETRA);
			
			//CREDITO_FORMULA_DE_PENA_PRODUCTO1
			$ID_DE_PRODUCTO	= $this->mClaveDeConvenio;
			//que va aplicar
			switch ($idproceso){
				case $xStep->AUTORIZACION:
					$txt	= "";
					break;
				case $xStep->MINISTRACION:
					$txt	= "";
					break;
				default:
					$txt	= $xFor->getFormula();
					break;
			}
			$xFor = null;
		}
		return $txt;
	}
	function getFormulaDeMoraPorLetra($idproceso = "", $posEvento = false){
		$txt	= "";
		//if($this->mAplicaPenas == true){
			$xStep	= new cCreditosEventos();
			$xFor	= new cFormula();
			$xFor->init($xFor->PHP_MORA_X_LETRA);
				
			//CREDITO_FORMULA_DE_PENA_PRODUCTO1
			$ID_DE_PRODUCTO	= $this->mClaveDeConvenio;
			//que va aplicar
			switch ($idproceso){
				case $xStep->AUTORIZACION:
					$txt	= "";
					break;
				case $xStep->MINISTRACION:
					$txt	= "";
					break;
				default:
					$txt	= $xFor->getFormula();
					break;
			}
			
			$xFor = null;
		//}
		return $txt;
	}	
	function getFormulaDeDiasTolerancia($idproceso = "", $posEvento = false){
		$txt	= "";
		$xStep	= new cCreditosEventos();
		$xFor	= new cFormula();
		$xFor->init($xFor->PHP_MORA_TOLE_PID . $this->mClaveDeConvenio);
				
		//CREDITO_FORMULA_DE_PENA_PRODUCTO1
		$ID_DE_PRODUCTO	= $this->mClaveDeConvenio;
		switch ($idproceso){
			case $xStep->AUTORIZACION:
				$txt	= "";
				break;
			case $xStep->MINISTRACION:
				$txt	= "";
				break;
			default:
				$txt	= $xFor->getFormula();
				break;
		}
		return $txt;
	}	
		
	function getMontoMaximoOtorgable(){ 
		$monto_maximo	= $this->mMontoMaximo;
		$xRuls			= new cCreditosProductosReglas();
		$rs				= $xRuls->getRsByPdtoAndTipo($this->mClaveDeConvenio, $xRuls->TIPO_MONTO);
		
		foreach($rs as $data){
			if( $xRuls->init($data) ){
				if($xRuls->getMontoMax()>$monto_maximo){
					$monto_maximo	= $xRuls->getMontoMax();
				}
			}
		}
		return $monto_maximo;
	}
	function getMontoFondoDefuncion(){ return $this->mMontoFondoDef; }
	function initOtrosCargos($fecha = false){
		if($this->mInitOCargos == false OR $fecha !== false){
			$xCach	= new cCache();
			$xLi	= new cSQLListas();
			$xQL	= new MQL();
			$idx	= ($fecha !== false) ? "rs-productos-costos-d-$fecha-". $this->mClaveDeConvenio : "rs-productos-costos-". $this->mClaveDeConvenio; 
			$rs		= $xCach->get($idx);
			if($rs === null){
				if($fecha !== false ){
					$xF		= new cFecha();
					$fecha	= $xF->getFechaISO($fecha);
					$sql 	= "SELECT * FROM `creditos_productos_costos` WHERE ('$fecha' >= `aplicar_desde` AND '$fecha' <= `aplicar_hasta`) AND `clave_de_producto`=" . $this->mClaveDeConvenio;
				} else {
					$sql 	= "SELECT * FROM `creditos_productos_costos` WHERE `estatus` =1 AND `clave_de_producto`=" . $this->mClaveDeConvenio;
				}
				$rs 		= $xQL->getDataRecord($sql);
				$xCach->set($idx, $rs);
			}
			//setLog($sql);
			$xCT	= new cCreditos_productos_costos();
			$sumaL	= 0;
			foreach ($rs as $rw){
				$xCT->setData($rw);
				$tipooperacion	= $rw[$xCT->CLAVE_DE_OPERACION];
				$unidades		= $rw[$xCT->UNIDADES];
				$EsTasa			= (setNoMenorQueCero($rw[$xCT->UNIDAD_DE_MEDIDA]) == 1 ) ? true : false;
				
				$monto			= ($EsTasa == false) ? $unidades : $unidades / 100; //0 = peso
				
				$EnPlan			= (setNoMenorQueCero($rw[$xCT->EN_PLAN]) <=0 ) ? false : true;
				//if($xCT->clave_de_operacion()->v() == OPERACION_CLAVE_COMISION_APERTURA){
				switch ($tipooperacion){
					case OPERACION_CLAVE_COMISION_APERTURA:
						if($this->mTasaComPorApertura == 0 AND $rw[$xCT->UNIDAD_DE_MEDIDA] == $this->COSTOS_EN_TASA){
							$this->mTasaComPorApertura = $monto;
						}
						break;
					case OPERACION_CLAVE_PAGO_NOT:
						$this->mAplicaGtosNots			= true;	//Si es mayor a 0
						break;
				}
				//TODO: AGREGAR POR MONTO FONDO OBLIGATORIO
				//$this->mMontoFondoDef		= $data[$xT->MONTO_FONDO_OBLIGATORIO];
				//if($rw[$xCT->CLAVE_DE_OPERACION] == OPERACION_CLAVE_COMISION_APERTURA){
					//si la comision es cero
					//if($this->mTasaComPorApertura == 0 AND $xCT->unidad_de_medida()->v() == $this->COSTOS_EN_TASA){
				//}
				if($EnPlan == true){
					if($EsTasa == true){ //solo porcentajes.- Si no, causa error en el calculo del CAT
						$this->mDOtrosCargosParcs[$tipooperacion]	= $monto;
						$sumaL										+= $monto;
						//setLog("Otros Cargos Parcialidades $tipooperacion -- $monto");
					}
				} else {
					$this->mDOtrosCargos[$tipooperacion]			= $monto;
					//setLog("Otros Cargos Tasa : $tipooperacion --  $monto");
				}
			}
			//setLog($this->mDOtrosCargosParcs);
			//Sumar Cargos en Letras y COnvertirlos en porcentaje
			$xcgo						= $this->mDOtrosCargosParcs;
			$this->mSumaOtrosCargosP	= $sumaL;
			$this->mDOtrosCargosPRaw	= $this->mDOtrosCargosParcs;
			
			foreach ($xcgo as $idx => $vv){
				$this->mDOtrosCargosParcs[$idx]	= ( $sumaL > 0) ? round( ($vv/$sumaL), 3 ) : 0;
				
				//setLog("CARGO $idx A " . $this->mDOtrosCargosParcs[$idx]);
			}
			//Si es Arrendamiento
			if($this->getEsProductoDeArrendamientoP() == true){
				//OPERACION_CLAVE_PAGO_CAPTACION
			}
			//
			$xcgo	= null;
			$rs		= null;
			$xQL	= null;
			$this->mInitOCargos			= true;
		}
	}
	/**
	 * Obtiene una lista en Array de los otros cargos sin ningun tipo de transformacion
	 * @return array
	 */
	function getListaOtrosCargos(){ return $this->mDOtrosCargos;}
	/**
	 * 
	 * @return array
	 */
	function getListaOtrosCargosEnParcs(){ return $this->mDOtrosCargosParcs;}
	function getListaOtrosCargosEnParcsRaw(){ return $this->mDOtrosCargosPRaw;}
	function getSumaOtrosCargosEnParcs(){ return $this->mSumaOtrosCargosP; }
	function getEsGrupal(){ return $this->getEsProductoDeGrupos(); }
	function getCATFijo(){
		$cat 	= $this->getOtrosParametros($this->getOOtrosParametros()->CAT_FIJO);
		$cat	= setNoMenorQueCero($cat,1);
		return $cat;
	}
	function getTasaFija(){
		$tf 	= $this->getOtrosParametros($this->getOOtrosParametros()->TASA_FIJA);
		$tf		= setNoMenorQueCero($tf,2);
		return $tf;
	}
	function getAMLRiesgoAsoc(){
		$riesgo	=1;
		$xRP	= new cAMLRiesgoProducto();
		if($xRP->initByTipoAndProd(iDE_CREDITO, $this->mClaveDeConvenio) == true){
			$riesgo	= $xRP->getNivelDeRiesgo();
		}
		return $riesgo;
	}
	function getAplicaGtosNot(){ return $this->mAplicaGtosNots; }
	function getFicha(){
		$xLng	= new cLang();
		
		$html	= "<table border='0' style='width:100%' class='ficha'>
	<caption>" .   "</caption>
	<tbody>
		<tr><th>" . $xLng->getT("TR.CLAVE") ."</th><td>" . $this->getCodigo()  . "</td><th>" . $xLng->getT("TR.NOMBRE") ."</th><td>" . $this->getNombre() . "</td></tr>
		<tr><th>" . $xLng->getT("TR.DESCRIPCION") ."</th><td colspan='3'>" . $this->getDescripcion() . "</td></tr></tbody></table>";
		return $html;
	}
	function setInActivo($baja=true){
		$clave	= $this->mClaveDeConvenio;
		$stat	= ($baja == true) ? "baja" : "activo";
		
		$sql	= "UPDATE `creditos_tipoconvenio` SET `estatus`='$stat' WHERE `idcreditos_tipoconvenio`=$clave ";
		$xQL	= new MQL();
		$res	= $xQL->setRawQuery($sql);
		$xQL	= null;
		
		$this->setCuandoSeActualiza();
		if($res !== false){
			setAgregarEvento_("OK\Estado del Producto de Credito a $stat", 1504);
			setCambio($this->mTabla,$clave, "estatus",$this->mEstatus, $stat );
		}
		return ($res === false) ? false : true;
	}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	private function setCleanCache(){
		if($this->mClaveDeConvenio > 0){
			$xCache = new cCache(); 
			$xCache->clean($this->mTabla . "-" . $this->mClaveDeConvenio);
		} 
	}
}
class cProductoDeCreditoOtrosDatosCatalogo {
	public $SIC_TIPO_DE_RESPONSABILIDAD = "SIC_TIPO_DE_RESPONSABILIDAD";
	public $SIC_TIPO_DE_CUENTA			= "SIC_TIPO_DE_CUENTA";
	public $SIC_TIPO_DE_CONTRATO		= "SIC_TIPO_DE_CONTRATO";
	public $CLAVE_DE_CONTRATO			= "CLAVE_DE_CONTRATO";
	public $CLAVE_DE_MANDATO			= "CLAVE_DE_MANDATO";
	public $CLAVE_DE_PAGARE				= "CLAVE_DE_PAGARE";
	public $CLAVE_DE_PLAN				= "CLAVE_DE_PLAN_DE_PAGOS";
	public $CLAVE_DE_CARATULA			= "CLAVE_DE_CARATULA";
	
	public $TASA_DE_COMISION_AP			= "TASA_DE_COMISION_POR_APERTURA";
	public $REQUIERE_PERFIL_AML			= "REQUIERE_PERFIL_AML";
	public $CONTRATO_ID_LEGAL			= "CONTRATO_ID_LEGAL";
	public $CONTRATO_FECHA_REGISTRO		= "CONTRATO_FECHA_DE_REGISTRO";
	public $PRODUCTO_NOMBRE_LEGAL		= "PRODUCTO_NOMBRE_LEGAL";
	public $PRODUCTO_TIPO_LEGAL			= "PRODUCTO_TIPO_LEGAL";
	public $PLAN_PAGOS_SIMPLE			= "USAR_PLAN_DE_PAGOS_SIMPLE";
	public $APLICA_PENAS				= "APLICA_PENAS";
	public $CAT_FIJO					= "CAT_FIJO";
	public $TASA_FIJA					= "TASA_FIJA";
	
	public $ESTADOCUENTA_EMUL			= "ESTADO_CUENTA_EMULADO";
	public $PAGOS_EN_DOMINGO			= "ACEPTAR_PAGOS_EN_DOMINGO";
	public $APLICA_PAGOS_ESP			= "APLICA_PAGOS_ESPECIALES";

	private $mDatos						= array();
	private $mProducto					= false;
	private $mInit						= false;
	function __construct($producto = false){
		$this->mProducto	= setNoMenorQueCero($producto);
		if($this->mProducto > 0){ $this->init(); }
	}
	function init(){
		$xCache			= new cCache();
		$idx			= "rs-producto-otro-dato-" . $this->mProducto;
		$this->mDatos	= $xCache->get($idx);
		if(!is_array($this->mDatos)){
			$xQL		= new MQL();
			$rs			= $xQL->getDataRecord("SELECT `clave_del_parametro`,`valor_del_parametro` FROM `creditos_productos_otros_parametros` WHERE `clave_del_producto` = " . $this->mProducto . " ");
			if(is_array($rs)){
				foreach ($rs as $datos){
					$idx				= strtoupper($datos["clave_del_parametro"]);
					$this->mDatos[$idx] = $datos["valor_del_parametro"];
					$this->mInit 		= true;
				}
			}
			$rs			= null;
		} else {
			$this->mInit= true;
		}
		
		return $this->mInit;	
	}
	function set($valor, $parametro, $fechaExpira = false,  $fecha = false){
		$parametro		= strtoupper($parametro);
		$xF				= new cFecha();
		$xQL			= new MQL();
		$xObj			= new cCreditos_productos_otros_parametros();
		$fecha			= $xF->getFechaISO($fecha);
		$fechaExpira	= ($fechaExpira == false) ? $xF->getFechaMaximaOperativa() : $fechaExpira;
		
		$xQL->setRawQuery("DELETE FROM creditos_productos_otros_parametros WHERE clave_del_producto=" . $this->mProducto . " AND clave_del_parametro='$parametro'");
		$xObj->clave_del_parametro($parametro);
		$xObj->clave_del_producto($this->mProducto);
		$xObj->fecha_de_alta($fecha);
		$xObj->fecha_de_expiracion($fechaExpira);
		$xObj->valor_del_parametro($valor);
		$id	= $xObj->query()->getLastID();
		$xObj->idcreditos_productos_otros_parametros($id);
		$res = $xObj->query()->insert()->save();
		return ($res == false) ? false : true;
	}
	function get($parametro){
		$dato	= null;
		if(isset($this->mDatos[$parametro])){ $dato	= $this->mDatos[$parametro]; }
		return $dato;
	}
	function getDatosInArray(){ return $this->mDatos; }
	function getCatalogoInArray(){
		$arr				= array();
		$arr[$this->SIC_TIPO_DE_RESPONSABILIDAD] 	= $this->SIC_TIPO_DE_RESPONSABILIDAD;
		$arr[$this->SIC_TIPO_DE_CUENTA] 			= $this->SIC_TIPO_DE_CUENTA;
		$arr[$this->SIC_TIPO_DE_CONTRATO] 			= $this->SIC_TIPO_DE_CONTRATO;
		$arr[$this->CLAVE_DE_CONTRATO] 				= $this->CLAVE_DE_CONTRATO;
		$arr[$this->CLAVE_DE_MANDATO] 				= $this->CLAVE_DE_MANDATO;
		$arr[$this->CLAVE_DE_PAGARE] 				= $this->CLAVE_DE_PAGARE;
		$arr[$this->TASA_DE_COMISION_AP] 			= $this->TASA_DE_COMISION_AP;
		$arr[$this->REQUIERE_PERFIL_AML] 			= $this->REQUIERE_PERFIL_AML;
		$arr[$this->CONTRATO_ID_LEGAL] 				= $this->CONTRATO_ID_LEGAL;
		$arr[$this->CONTRATO_FECHA_REGISTRO] 		= $this->CONTRATO_FECHA_REGISTRO;
		$arr[$this->PLAN_PAGOS_SIMPLE] 				= $this->PLAN_PAGOS_SIMPLE;
		$arr[$this->PRODUCTO_NOMBRE_LEGAL]			= $this->PRODUCTO_NOMBRE_LEGAL;
		$arr[$this->PRODUCTO_TIPO_LEGAL]			= $this->PRODUCTO_TIPO_LEGAL;
		$arr[$this->APLICA_PENAS] 					= $this->APLICA_PENAS;
		$arr[$this->CAT_FIJO] 						= $this->CAT_FIJO;
		$arr[$this->TASA_FIJA] 						= $this->TASA_FIJA;
		$arr[$this->ESTADOCUENTA_EMUL] 				= $this->ESTADOCUENTA_EMUL;
		$arr[$this->PAGOS_EN_DOMINGO] 				= $this->PAGOS_EN_DOMINGO;
		$arr[$this->APLICA_PAGOS_ESP] 				= $this->APLICA_PAGOS_ESP;
		//$arr[$this->] = $this->;
		//$arr[$this->] = $this->;
		//$arr[$this->] = $this->;
		//$arr[$this->] = $this->;
		//$arr[$this->] = $this->;
		//$arr[$this->] = $this->;
		//$arr[$this->] = $this->;
		
		return $arr;
	}
}
class cPeriocidadDePago {
	private $mClave			= 99;
	private $mArrDatos		= array();
	private $mOb			= null;
	private $mInit			= false;
	private $mTabla			= "creditos_periocidadpagos";
	
	function __construct($clave){
		$this->mClave	= setNoMenorQueCero($clave);
	}
	function getDatosInArray(){
		$this->init();
		return $this->mArrDatos;
	}
	function init(){
		$idcx				= $this->mTabla . "-" . $this->mClave;
		$xCache				= new cCache();
		$this->mArrDatos	= $xCache->get($idcx);
		if(!is_array($this->mArrDatos)){
			$sql				= "SELECT * FROM creditos_periocidadpagos WHERE `idcreditos_periocidadpagos` = " . $this->mClave . " LIMIT 0,1";
			$this->mArrDatos	= obten_filas($sql);
		}
		$xPer				= new cCreditos_periocidadpagos();
		$xPer->setData( $this->mArrDatos );
		
		$this->mOb			= $xPer;
		if(isset($this->mArrDatos["idcreditos_periocidadpagos"])){
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getNombre(){
		return $this->mArrDatos["descripcion_periocidadpagos"];
	}
	function getDiasToleradosEnVencer(){
		return $this->mOb->tolerancia_en_dias_para_vencimiento()->v();
	}
	function isFinalDePlazo(){
		return ($this->mOb->idcreditos_periocidadpagos()->v() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) ? true : false;
	}
}


class cParcialidadDeCredito {
	private $mPersona			= false;
	private $mCredito			= false;
	private $mNumero			= false;
	private $mPeriodo			= false;
	
	private $mCapital			= 0;
	private $mInteres			= 0;
	private $mMoratorio			= 0;
	private $mFechaDePago		= false;
	private $mFechaDeVenc		= false;
	
	private $mImpuestos			= 0;
	private $mOtros				= 0;
	private $mTipoOtros			= 0;
	private $mAhorro			= 0;
	private $mTotal				= 0;
	private $mTotalSinOtros		= 0;
		
	private $mCapitalPagado		= 0;
	private $mInteresPagado		= 0;
	private $mMoratorioPagado	= 0;
	private $mOtrosPagado		= 0;
		
	private $mOB				= null;
	private $mOCredito			= null;
	private $mInitCredito		= false;
	private $mDatosInArray		= array();
	private $mMessages			= "";
	private $mIDOtros			= 0;
	private $mIDPlanDePagos		= 0;
	private $mInit				= false;
	private $mInitHistorico		= false;
	private $mIDProducto		= 0;
	private $mTasaMora			= 0;
	private $mPeriocidadDePago	= 0;
	private $mIvaOtros			= 0;
	private $mMontoPenas		= 0;
	
	function setDatos($aDatos = false){ 
		$this->mDatosInArray	= $aDatos;
	}
	
	function __construct($persona = false, $credito = false, $numero = false){
		$this->mPersona			= setNoMenorQueCero($persona);
		$this->mCredito			= setNoMenorQueCero($credito);
		$this->mNumero			= setNoMenorQueCero($numero);
		$this->mPeriodo			= setNoMenorQueCero($numero);
	}
	function initSuma($fecha){
		$xQL		= new MQL();
		$xLi		= new cSQLListas();
		$xT			= new cLetrasVista();
		
		$sql		= $xLi->getListadoDeLetrasVista($this->mPersona, $this->mCredito, null, $fecha);
		$Datos		= $xQL->getDataRow($sql);
		if(isset($Datos[$xT->LETRA])){
			$this->mDatosInArray	= $Datos;
		}
		return $this->init();
	}
	
	function init($persona = false, $credito = false, $numero = false){
		$xF					= new cFecha();
		$xT					= new cLetrasVista();
		
		
		//TODO: Verificar cambios de incidencias 2014-10-01
		$persona			= setNoMenorQueCero($persona);
		$credito			= setNoMenorQueCero($credito);
		$numero				= setNoMenorQueCero($numero);
		
		$persona			= ($persona > DEFAULT_SOCIO) ? $persona : $this->mPersona;
		$credito			= ($credito > DEFAULT_CREDITO) ? $credito : $this->mCredito;
		$numero				= ($numero > 0) ? $numero : $this->mNumero;
		
		$this->mPersona		= $persona;
		$this->mCredito		= $credito;
		$this->mNumero		= $numero;
		$this->mPeriodo		= $numero;
		
		
		$init				= true;
		//$Datos				= (is_array($this->mDatosInArray) && isset($this->mDatosInArray["letra"])) ? $this->mDatosInArray : obten_filas($sql);
		$Datos				= $this->mDatosInArray;
		
		if(!is_array($Datos) OR !isset($Datos[$xT->LETRA]) ){
			$xQL		= new MQL();
			$xLi		= new cSQLListas();
			$sql		= $xLi->getListadoDeLetrasVista($persona, $credito, $numero);
			$Datos		= $xQL->getDataRow($sql);
			if(isset($Datos[$xT->LETRA])){
				$this->mDatosInArray	= $Datos;
			}
		}
		//setLog($Datos);
		
		//setLog($sql);
		//Si la letra no existe, crear un Historico
		if(!isset($Datos[$xT->LETRA])){
			$init = false;
			//fecha_de_pago|fecha_de_vencimiento|capital|interes|iva|ahorro|otros|letra|total_sin_otros
			//tratar con plan
			$xLetra				= new cCreditosLetraDePago($credito, $numero);
			if($xLetra->init() == true){
				//recuperar solo la fecha de la letra
				$Datos[$xT->SOCIO_AFECTADO]			= $persona;
				$Datos[$xT->DOCTO_AFECTADO]			= $credito;
				$Datos[$xT->PERIODO_SOCIO]			= $numero;
				$Datos[$xT->LETRA]					= $xLetra->getTotal();
				$Datos[$xT->FECHA_DE_PAGO]			= $xLetra->getFechaDePago();
				$Datos[$xT->FECHA_DE_VENCIMIENTO]	= $xF->setSumarDias(1, $xLetra->getFechaDePago());
				
				$Datos[$xT->CAPITAL]				= 0;
				$Datos[$xT->INTERES]				= 0;
				$Datos[$xT->IVA]					= 0;
				$Datos[$xT->AHORRO]					= 0;
				$Datos[$xT->OTROS]					= 0;
				$Datos[$xT->CLAVE_OTROS]			= 0;
				$Datos[$xT->TOTAL_SIN_OTROS]		= 0;
				$Datos[$xT->INTERES_MORATORIO]		= 0;
				
				$this->mInit						= true;
				$this->mInitHistorico				= true;
			}
		} else {
			if(!isset($Datos[$xT->FECHA_DE_VENCIMIENTO])){
				$Datos[$xT->FECHA_DE_VENCIMIENTO]	= $xF->setSumarDias(1,$Datos[$xT->FECHA_DE_PAGO]);
			}
			if(!isset($Datos[$xT->CLAVE_OTROS])){
				$Datos[$xT->CLAVE_OTROS]			= 0;
			}
		}
		//setLog($Datos);
		
		if(isset($Datos[$xT->CAPITAL])){
			$this->mCapital			= $Datos[$xT->CAPITAL];
			$this->mTotal			= $Datos[$xT->LETRA];
			$this->mImpuestos		= $Datos[$xT->IVA];
			$this->mTotalSinOtros	= $Datos[$xT->TOTAL_SIN_OTROS];
			$this->mInteres			= $Datos[$xT->INTERES];
			$this->mOtros			= $Datos[$xT->OTROS];
			$this->mIDOtros			= $Datos[$xT->CLAVE_OTROS];
			$this->mFechaDePago		= $xF->getFechaISO($Datos[$xT->FECHA_DE_PAGO]);
			$this->mFechaDeVenc		= $xF->getFechaISO($Datos[$xT->FECHA_DE_VENCIMIENTO]);
			$this->mMoratorio		= $Datos[$xT->INTERES_MORATORIO];
			//TODO : Verificar de donde viene el isset
			if( !isset($Datos[$xT->IVA_MORATORIO]) ){
				if($this->mMoratorio>0){
					$Datos[$xT->IVA_MORATORIO]	= round(($this->mMoratorio * TASA_IVA),2);
				} else {
					$Datos[$xT->IVA_MORATORIO]	= 0;
				}
			}
			$this->mIvaOtros		= $Datos[$xT->IVA_MORATORIO]; //(isset($Datos[$xT->IVA_MORATORIO])) ? $Datos[$xT->IVA_MORATORIO] : 0;
			
			
			
			$this->mInit			= $init;
			$this->mDatosInArray	= $Datos;
			$this->mOB				= $xT;
			$this->mOB->setData($Datos);
		} else {
			//if($init == true){ setError(); }
		}
		return $init;
	}
	function getDatosInArray(){ return $this->mDatosInArray; }
	
	function getMonto(){ return  $this->mTotal; }
	function getTotal(){ return  $this->mTotal; }
	function getTotalSinOtros(){ return  $this->mTotalSinOtros; }
	function getCapital(){ return $this->mCapital;	}
	function getInteres(){ return $this->mInteres;	}
	function getOtros(){ return $this->mOtros;	}
	function getIDOtros(){ return $this->mIDOtros; }
	function getMora(){ return $this->mMoratorio; }
	function getIvaOtros(){ return $this->mIvaOtros; }
	function getImpuestos(){ return $this->mImpuestos; }
	function getFechaDePago(){ return $this->mFechaDePago;	}
	
	function getFechaDeVencimiento(){ return $this->mFechaDeVenc; }
	function getAhorro(){ return $this->mAhorro; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function initPago(){ 
		$periodo	= $this->mNumero; 
	}
	function setClaveDePlan($idplan){ $this->mIDPlanDePagos = $idplan; }
	function setActualizarIVA($monto, $operador = "+"){ $this->setActualizarConcepto(OPERACION_CLAVE_PLAN_IVA, $monto, $operador); }
	function setActualizarCapital($monto, $operador = "+"){ $this->setActualizarConcepto(OPERACION_CLAVE_PLAN_CAPITAL, $monto, $operador); }
	function setActualizarInteres($monto, $operador = "+"){ $this->setActualizarConcepto(OPERACION_CLAVE_PLAN_INTERES, $monto, $operador); }
	function setActualizarDesglose($monto, $operador = "+"){ $this->setActualizarConcepto(OPERACION_CLAVE_PLAN_DESGLOSE, $monto, $operador); }
	private function setActualizarConcepto($idconcepto, $monto = false, $operador = "+"){
		$idconcepto	= setNoMenorQueCero($idconcepto);
		
		if($monto !== false AND $this->mIDPlanDePagos > 0 AND $idconcepto >0){
			$xMov	= new cMovimientoDeOperacion();
			$xMov->setActualizarEstado(OPERACION_ESTADO_APLICADO);
			$xMov->setActualizarAfecta(SYS_UNO);
			$xMov->setActualizarDNeutral(SYS_UNO);
			$numero	= $this->mNumero;
			switch($idconcepto){
				case OPERACION_CLAVE_PLAN_CAPITAL:
					if($this->getOCredito() == null){
						
					} else {
						
						//si es pago solo de interes, el numero se lleva a la ultima letra
						if($this->getOCredito()->getPagosSinCapital() == true){
							$numero		= $this->getOCredito()->getPagosAutorizados();
							//se debe actualizar el interes a la proporcion
							if($operador == "+"){
							//$this->setActualInteresPropCred($this->mPersona, $this->mCredito, $this->mNumero, $monto, $this->getOCredito()->getSaldoActual());
							}
						}
						
					}
					break;
				case OPERACION_CLAVE_PLAN_INTERES:
					break;
			}
			$xMov->setActualizaRAW($idconcepto, $monto, $operador, $this->mCredito, $this->mPersona, $this->mIDPlanDePagos, $numero);
		}
	}
	private function initHistorico(){
		
	}
	/**
	 * Actualiza las parcialidades de credito a un interes proporcional al Cambio de Capital
	 * @param integer $persona
	 * @param integer $credito
	 * @param integer $Parcialidad
	 * @param float $CapitalPagado
	 * @param float $SaldoActual
	 * @param float $InteresDiarioGen
	 * @return string Mensajes
	 */
	function setActualInteresPropCred($persona, $credito, $Parcialidad, $CapitalPagado, $SaldoActual, $reversion = false, $InteresDiarioGen = 0){
		$persona						= setNoMenorQueCero($persona);
		$credito						= setNoMenorQueCero($credito);
		$msg							= "";
		$proporcion_de_cambios			= 1 - ($CapitalPagado / $SaldoActual);
		if($reversion == true){
			$proporcion_de_cambios		= ($CapitalPagado + $SaldoActual) / $SaldoActual;
		}
		$interes_diario					= $InteresDiarioGen - ($InteresDiarioGen  * $proporcion_de_cambios);

		
		$msg							.= "WARN\tCambios de interes por capital queda en $interes_diario y proporcion de $proporcion_de_cambios\r\n";
		
			//=============================== Ajuste de Interes
			$sqlTM	= "UPDATE operaciones_mvtos SET afectacion_real=(afectacion_real*$proporcion_de_cambios),
			afectacion_cobranza =(afectacion_cobranza*$proporcion_de_cambios),
			afectacion_estadistica=(afectacion_estadistica*$proporcion_de_cambios) WHERE socio_afectado=$persona 
			AND periodo_socio > $Parcialidad
			AND docto_afectado=$credito AND (tipo_operacion=" . OPERACION_CLAVE_PLAN_INTERES . " OR tipo_operacion=" . OPERACION_CLAVE_PLAN_IVA . ") " ;
			$xQL	= new MQL();
			$xQL->setRawQuery($sqlTM);
			
			
			$msg							.= "WARN\tAjustando Intereses de plan de pagos solo interes a una Proporcion $proporcion_de_cambios\r\n";
		return $msg;	
	}
	function setIDProducto($producto){ $this->mIDProducto = $producto; }
	function setTasaMora($tasa=0){ $this->mTasaMora = $tasa; }
	function setPeriocidadDePago($periocidad){ $this->mPeriocidadDePago = $periocidad; }
	function getEsAdelantadoSegunFecha($fecha=false){
		$xF	= new cFecha();
		$es	= false;
		if($xF->getInt($fecha) < $xF->getInt($this->mFechaDePago) ){
			$es	= true;
		}
		return $es;
	}
	function setCalcularPenas_Y_Mora($fecha_de_calculo = false, $guardar = true, $EsNomina = false){
		$xF	 				= new cFecha();
		$xT					= new cTipos();
		$xLog 				= new cCoreLog();
		$xFor				= new cFormula();
		$xCache				= new cCache();
		$xRuls				= new cReglaDeNegocio();
		$useMoraBD			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_USE_MORA_BD);
		$NoMoraNom			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_NOMINA_NOMORA);
		
		$PERIODO_A_PAGAR 	= $this->mNumero;
		$FECHA_DE_OPERACION = $xF->getFechaISO($fecha_de_calculo);
		
		$IDCache			= $this->mCredito . "-" . $this->mNumero . "-penas-mora-p-$PERIODO_A_PAGAR-" . $FECHA_DE_OPERACION;
		
		$arrInteres			= $xCache->get($IDCache);
		if(!is_array($arrInteres)){
			
			$xProd				= new cProductoDeCredito($this->mIDProducto); $xProd->init();
			//TODO: Optimizar carga de Clases
			$credito 			= $this->mCredito;
			
			
			$FECHA_DE_PAGO		= $this->mFechaDePago;
			$BASE_NORMAL		= $this->mCapital;
			$BASE_PENAS			= $this->mTotalSinOtros;
			$BASE_MORA			= $this->mCapital;
			$TASA_MORA			= $this->mTasaMora;
			$TOTAL_MONTO_LETRA	= $this->mTotal;
			$PERIOCIDAD_DE_PAGO	= $this->mPeriocidadDePago;
			$TASA_PENA			= setNoMenorQueCero(CREDITO_TASA_PENA_GLOBAL,2)/100;
			$DIAS_MORA			= $xF->setRestarFechas($FECHA_DE_OPERACION, $FECHA_DE_PAGO);
			$DIAS_PENA			= $xF->setRestarFechas($FECHA_DE_OPERACION, $FECHA_DE_PAGO);
			$DIAS_ATRASO		= setNoMenorQueCero($xF->setRestarFechas($fecha_de_calculo, $FECHA_DE_PAGO));
			$DIAS_TOLERADOS		= $xProd->getDiasTolerados();
			
			eval($xProd->getFormulaDeDiasTolerancia());
			$DIAS_PENA			= setNoMenorQueCero($DIAS_PENA);
			$penas				= 0;
			$moratorio			= 0;
			//Carga la tasa de pena de la persona
			$xSoc				= new cSocio($this->mPersona);
			if($xSoc->init() == true){
				if($xSoc->getTasaPena()>0){
					$TASA_PENA			= setNoMenorQueCero($xSoc->getTasaPena(),3)/100;
				}
			}
			//setLog("$credito $FECHA_DE_PAGO -  $TASA_MORA --- $TOTAL_MONTO_LETRA --- " . $this->mIDProducto);
			if($DIAS_ATRASO > $DIAS_TOLERADOS){
				eval($xProd->getFormulaDePena());
				eval($xProd->getFormulaDeMoraPorLetra());
			}

			//Si Guarda Rules
			if($useMoraBD == true){
				if($this->init() == true){
					$moratorio	= $this->getMora();
				}
			}
			if($EsNomina == true AND $NoMoraNom == true){
				$moratorio		= 0;
			}
			$arrInteres[SYS_INTERES_MORATORIO]	= setNoMenorQueCero($moratorio,2);
			$arrInteres[SYS_PENAS]				= setNoMenorQueCero($penas,2);
			
			if($guardar == true ){
				$xL				= new cCreditosLetraDePago($this->mCredito, $this->mNumero);
				$xL->setCargosYPenas(false, $penas, $moratorio);
			}
			$xCache->set($IDCache, $arrInteres);
		}
		
		return $arrInteres;
	}
	function getOCredito(){
		if($this->mOCredito == null){
			$this->mOCredito = new cCredito($this->mCredito);
			if($this->mOCredito->init() == true){
				$this->mInitCredito	= true;
			} else {
				$this->mInitCredito	= false;
				$this->mOCredito 	= null;
			}
		}
		return $this->mOCredito;
	}
	function getSaldoNoPagado(){
		$xQL	= new MQL();
		$sql1	= "SELECT SUM(`total_operacion`) AS `monto` FROM `operaciones_recibos` WHERE `docto_afectado`=" . $this->mCredito . " AND `periodo_de_documento`=" . $this->mPeriodo;
		$montoP	= $xQL->getDataValue($sql1, "monto");
		$sql2	= "SELECT `total_base` FROM `creditos_plan_de_pagos` WHERE `clave_de_credito`=" . $this->mCredito . " AND `numero_de_parcialidad`=" . $this->mPeriodo . " LIMIT 0,1";
		$montoO	= $xQL->getDataValue($sql2, "total_base");
		$sdo	= setNoMenorQueCero( ($montoO - $montoP) );
		return $sdo;
	}
}

class cCreditosGarantias {
	private $mCodigo		= false;
	private $mClavePersona	= false;
	private $mClaveCredito	= false;
	private $mData			= array();
	private $mMessages		= "";
	private $mOb			= null;
	private $mMontoValuado	= 0;
	private $mFechaResguardo= false;
	private $mFechaRegistro		= false;
	private $mInit				= false;
	private $mEstadoGar			= false; 
	private $mColor				= "";
	private $mModelo			= "";
	private $mAnnio				= "";
	
	public $TIPO_PRENDARIA		= 2;
	public $TIPO_INMUEBLE		= 1;
	
	public $ESTADO_COTEJO		= 1;
	public $ESTADO_RESGUARDO	= 2;
	public $ESTADO_DEVUELTO		= 3;
	
	public $VALUADO_DOCTO		= 1;
	public $VALUADO_LEGAL		= 2;
	public $VALUADO_ESTIMADA	= 3;
	
	public $COMO_NUEVO			= 11;
	public $COMO_BUENO			= 10;
	public $COMO_MALO			= 20;
	
	function __construct($id = false){
		$this->mCodigo		= setNoMenorQueCero($id);
	}
	function getClaveDePersona(){ return $this->mClavePersona; }
	function getClaveDeCredito(){ return $this->mClaveCredito; }
	function init($aDIniciales = false){
		$xF			= new cFecha();
		if($this->mCodigo != false){
			$xT		= new cCreditos_garantias();
			$data	= ($aDIniciales == false) ? $xT->query()->initByID($this->mCodigo) : $aDIniciales;
			
			if(isset($data["solicitud_garantia"])){
				$xT->setData( $data );
				$this->mClaveCredito	= $xT->solicitud_garantia()->v();
				$this->mClavePersona	= $xT->socio_garantia()->v();
				$this->mCodigo			= $xT->idcreditos_garantias()->v();
				$this->mMontoValuado	= $xT->monto_valuado()->v();
				$this->mFechaRegistro	= $xT->fecha_recibo()->v();
				$this->mFechaResguardo	= $xT->fecha_resguardo()->v();
				$this->mColor			= $xT->caracteristica1()->v();
				$this->mModelo			= $xT->caracteristica2()->v();
				$this->mAnnio			= $xT->caracteristica3()->v();
				$this->mEstadoGar		= $data[$xT->ESTATUS_ACTUAL];
				
				$this->mOb				= $xT;
				$this->mData			= $data;
				
				$this->mInit			= true;
			}
		}
		$this->mFechaRegistro			= $xF->getFechaISO($this->mFechaRegistro);
		$this->mFechaResguardo			= $xF->getFechaISO($this->mFechaResguardo);
		
		return $this->mInit;
	}
	function setClaveDePersona($persona){ $this->mClavePersona	= $persona;	}
	function setClaveDeCredito($credito){ $this->mClaveCredito	= $credito;	}
	function add($tipo, $tipo_de_valuacion = false, $valor = 0, $persona_propietaria = false, $nombre_del_propietario = "", $fecha_de_adquisicion = false, 
			$documento_presentado = "", $estado_fisico = false, $descripcion = "", $observaciones = "", $fecha_actual = false){
		$fecha_actual		= ($fecha_actual == false) ? fechasys() : $fecha_actual; 
		$persona_propietaria= ($persona_propietaria == false) ? DEFAULT_SOCIO : $persona_propietaria;
		$tipo_de_valuacion	= ($tipo_de_valuacion == false) ? FALLBACK_CRED_GARANTIAS_TVALUACION : $tipo_de_valuacion;
		
		$xGar				= new cCreditos_garantias();
		
		$xGar->descripcion($descripcion);
		$xGar->documento_presentado($documento_presentado);
		$xGar->eacp( EACP_CLAVE );
		$xGar->estado_presentado($estado_fisico);
		$xGar->estatus_actual(CREDITO_GARANTIA_ESTADO_PRESENTADO);
		$xGar->fecha_adquisicion($fecha_de_adquisicion);
		$xGar->fecha_devolucion($fecha_actual);
		$xGar->fecha_recibo($fecha_actual);
		$xGar->fecha_resguardo($fecha_actual);
		$xGar->idsocio_duenno($persona_propietaria);
		$xGar->idusuario( getUsuarioActual() );
		$xGar->observaciones($observaciones);
		$xGar->observaciones_del_resguardo("");
		$xGar->propietario($nombre_del_propietario);
		$xGar->socio_garantia($this->mClavePersona);
		$xGar->solicitud_garantia( $this->mClaveCredito );
		$xGar->sucursal( getSucursal() );
		$xGar->tipo_garantia($tipo);
		$xGar->tipo_valuacion($tipo_de_valuacion);
		$xGar->monto_valuado($valor);
		$clave				= $xGar->query()->getLastID();
		$xGar->idcreditos_garantias( $clave );
		
		$q					= $xGar->query()->insert()->save();
		if($q=== false){
			$this->mMessages	.= "ERROR\tAl Registrar la Garantia con valor $valor";
		}
		$id					= ($q === false) ? 0 : $clave;
				
		return $id;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getMontoValuado(){ return $this->mMontoValuado; }
	function getModelo(){ return $this->mModelo; }
	function getColor(){ return $this->mColor; }
	function getAnnio(){ return $this->mAnnio; }
	function getEstadoGarantia(){ return $this->mEstadoGar; }
	function setDatosVehiculo($modelo = "", $color = "", $annio = ""){
		if($this->mOb == null){ $this->init(); }
		if($this->mOb !== null){
			$this->mOb->caracteristica1($modelo);
			$this->mOb->caracteristica2($color);
			$this->mOb->caracteristica3($annio);
			$this->mOb->query()->update()->save($this->mCodigo);
		}
	}
	function getFicha(){
		if($this->mInit == false){$this->init();}
		$xLng		= new cLang();
		$xF			= new cFecha();
		$xTTipo		= new cCreditos_tgarantias();
		$xTTipo->setData($xTTipo->query()->initByID($this->mOb->tipo_garantia()->v()));
		$xTVal		= new cCreditos_tvaluacion();
		$xTVal->setData( $xTVal->query()->initByID($this->mOb->tipo_valuacion()->v() ) );
		
		$fresguardo	= $xF->getFechaCorta($this->mOb->fecha_resguardo()->v());
		$fcompra	= $xF->getFechaCorta($this->mOb->fecha_adquisicion()->v());
		$exoFicha =  "
		<table class='ficha'>
		<tbody>
		<tr>
			<th class='izq'>" . $xLng->getT("TR.Clave") . "</th><td>" . $this->mOb->idcreditos_garantias()->v() . "</td>
			<th class='izq'>" . $xLng->getT("TR.Tipo") . "</th><td>" . $xTTipo->descripcion_tgarantias()->v() . "</td>
		</tr>
		<tr>
			<th class='izq'>" . $xLng->getT("TR.FORMA_DE_VALUACION") . "</th><td>" . $xTVal->descripcion_tvaluacion()->v()  .  "</td>
			<th class='izq'>" . $xLng->getT("TR.Fecha de Resguardo") . "</th><td>" . $fresguardo . "</td>
		</tr>
		<tr>
			<th class='izq'>" . $xLng->getT("TR.Fecha de compra") . "</th><td>" . $fcompra . "</td>
			<th class='izq'>" . $xLng->getT("TR.Documento / Factura") . "</th><td>" . $this->mOb->documento_presentado()->v() . "</td>
		</tr>
		<tr>
			<th class='izq'>" . $xLng->getT("TR.MONTO_VALUADO") . "</th><td>" . getFMoney($this->mOb->monto_valuado()->v()) . "</td>
			<th class='izq'>" . $xLng->getT("TR.Propietario") . "</th><td>" . $this->mOb->propietario()->v() . "</td>
		</tr>
		<tr>
			<th>" . $xLng->getT("TR.Descripcion") . "</th>
			<td colspan='3'>" . $this->mOb->descripcion()->v() . "</td>
		</tr>
		</tbody>
		</table>";
		return $exoFicha;
	}
	function setEstatus($estado, $fecha = false, $observaciones = ""){
		if($this->mInit == false){$this->init();}
		$xF		= new cFecha();
		$fecha	= $xF->getFechaISO($fecha);
		$this->mOb->fecha_resguardo($fecha);
		$this->mOb->observaciones_del_resguardo($observaciones);
		$this->mOb->idusuario(getUsuarioActual());
		$this->mOb->estatus_actual($estado);
		$rs 	= $this->mOb->query()->update()->save($this->mCodigo);
		return ($rs === false) ? false : true;	
	}
	function getDatosInArray(){ return $this->mData; }
	function getMontoResguardado(){
		return $this->getMontoGarantiaByX($this->ESTADO_RESGUARDO);
	}
	function getMontoPresentado(){
		return $this->getMontoGarantiaByX($this->ESTADO_COTEJO);
	}
	function getMontoEntregado(){
		return $this->getMontoGarantiaByX($this->ESTADO_DEVUELTO);
	}
	private function getMontoGarantiaByX($estatus){
		$monto		= 0;
		$ByFiltro	= "";
		
		if($this->mClaveCredito > DEFAULT_CREDITO){
			$ByFiltro	= " `solicitud_garantia`='" . $this->mClaveCredito . "' ";
		} else {
			if($this->mClavePersona > DEFAULT_SOCIO){
				$ByFiltro	= " `socio_garantia`='" . $this->mClavePersona . "' ";
			}
		}
		if($ByFiltro !== ""){
			$xQL	= new MQL();
			$D		= $xQL->getDataRow("SELECT SUM(`monto_valuado`) AS '" . SYS_MONTO . "' FROM `creditos_garantias` WHERE $ByFiltro AND `estatus_actual`=$estatus");
			$monto	= setNoMenorQueCero($D[SYS_MONTO]);
		}
		return $monto;
	}
	function getEsPresentado(){
		$res	= false;
		if($this->getEstadoGarantia() == $this->ESTADO_COTEJO){
			$res	= true;
		}
		return $res;
	}
	function getEsResguardado(){
		$res	= false;
		if($this->getEstadoGarantia() == $this->ESTADO_RESGUARDO){
			$res	= true;
		}
		return $res;
	}
	function getEsDevuelto(){
		$res	= false;
		if($this->getEstadoGarantia() == $this->ESTADO_DEVUELTO){
			$res	= true;
		}
		return $res;
	}
}

class cCreditosGarantiasLiquidas {
	private $mCodigo		= false;
	private $mClavePersona	= false;
	private $mClaveCredito	= false;
	private $mData			= array();
	private $mMessages		= "";
	private $mClaveGrupo	= 0;
	
	function __construct(){
		
	}
	function setClaveDePersona($persona){ $this->mClavePersona	= $persona;	}
	function setClaveDeCredito($credito){ $this->mClaveCredito	= $credito;	}
	function setClaveDeGrupo($grupo){ $this->mClaveGrupo	= $grupo;	}
	
	function getSaldoGantiaLiq($AppGrupo = true){
		$monto 		= 0;
		$B1Grupo	= " `socios_general`.`grupo_solidario`, ";
		$msg		= "";
		$By			= "";
		
		if(PERSONAS_CONTROLAR_POR_GRUPO == false OR ($this->mClaveGrupo <= 0 OR $this->mClaveGrupo == FALLBACK_CLAVE_DE_GRUPO) ){
			$AppGrupo	= false;
		}
		if (GARANTIA_LIQUIDA_EN_CAPTACION == true) {
			// obtiene el monto de garantia liquida por ahorro
			
			if($AppGrupo == true){
				$By 	= " AND (`socios_general`.`grupo_solidario` = " . $this->mClaveGrupo . ") ";
				$msg 	.= "WARN\tLa Garantia Liquida es valuado por Grupo : " . $this->mClaveGrupo . "\r\n";
			} else {
				if($this->mClaveCredito > DEFAULT_CREDITO){
					$By	= " AND (`captacion_cuentas`.`numero_solicitud`=" . $this->mClaveCredito . ") ";
					$msg 	.= "WARN\tLa Garantia Liquida es valuado por Credito : "  . $this->mClaveCredito .   "\r\n";
				} else {
					if($this->mClavePersona > DEFAULT_SOCIO){
						$By 	= " AND (`socios_general`.`codigo` = " . $this->mClavePersona. ") ";
						$msg 	.= "WARN\tLa Garantia Liquida es valuado por Persona : "  . $this->mClavePersona .   "\r\n";
					}
				}
			}
			$sql 				= "SELECT
			MAX(`captacion_cuentas`.`numero_cuenta`) AS 'cuenta', 
			SUM(`captacion_cuentas`.`saldo_cuenta`) AS '" . SYS_MONTO . "'
			FROM `socios_general` `socios_general`
			INNER JOIN `captacion_cuentas` `captacion_cuentas` ON `socios_general`.`codigo` = `captacion_cuentas`.`numero_socio`
			WHERE (`captacion_cuentas`.`tipo_subproducto` = " . CAPTACION_PRODUCTO_GARANTIALIQ. ") $By
			GROUP BY $B1Grupo `captacion_cuentas`.`tipo_cuenta`
			ORDER BY `captacion_cuentas`.`fecha_afectacion` DESC, `captacion_cuentas`.`saldo_cuenta` DESC";
			
			if($By !== ""){
				$xQL	= new MQL();
				$monto	= $xQL->getDataValue($sql, SYS_MONTO);
			}
			
		} else {
			$B2Credito	= "";
			if($this->mClaveCredito > DEFAULT_CREDITO){
				$By			= " AND (`operaciones_mvtos`.`docto_afectado`=" . $this->mClaveCredito . ") ";
				$msg 		.= "WARN\tLa Garantia Liquida es valuado por Credito : "  . $this->mClaveCredito .   "\r\n";
				$B2Credito	= ", `operaciones_mvtos`.`docto_afectado` ";
			} else {
				if($this->mClavePersona > DEFAULT_SOCIO){
					$By 		= " AND (`operaciones_mvtos`.`socio_afectado` = " . $this->mClavePersona. ") ";
					$msg 		.= "WARN\tLa Garantia Liquida es valuado por Persona : "  . $this->mClavePersona .   "\r\n";
					$B2Credito	= ", `operaciones_mvtos`.`socio_afectado` ";
				}
			}
			
			$msg 	.= "WARN\tLa Garantia Liquida es valuado como Cuenta Aparte y no de CAPTACION\r\n";
			$sql	= "SELECT `operaciones_mvtos`.`docto_afectado`,`operaciones_mvtos`.`socio_afectado`,
						SUM((`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)) AS '" . SYS_MONTO . "'
					FROM `operaciones_mvtos` `operaciones_mvtos` INNER JOIN `eacp_config_bases_de_integracion_miembros`
							`eacp_config_bases_de_integracion_miembros` ON `operaciones_mvtos`.`tipo_operacion` = `eacp_config_bases_de_integracion_miembros`.`miembro`
					WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2500) $By
					GROUP BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` $B2Credito
					ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` ";
			if($By !== ""){
				$xQL	= new MQL();
				$monto	= $xQL->getDataValue($sql, SYS_MONTO);
			}
		}
		//setLog($sql);
		$this->mMessages .= $msg;
		return $monto;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
}

?>