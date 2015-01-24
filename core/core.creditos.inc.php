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
include_once("core.creditos.pagos.inc.php");
include_once("core.creditos.utils.inc.php");


@include_once("../libs/sql.inc.php");

//=====================================================================================================

/**
 * Clase de Manejo de Creditos
 * @package 		creditos
 * @subpackage		core
 * @author 			Balam Gonzalez Luis Humberto
 * @version 		1.3
 */
class cCredito {
	protected $mNumeroCredito				= false;
	protected $mNumeroSocio					= false;
	protected $mFechaMinistracion			= false;
	protected $mFechaVencimiento			= false;
	protected $mFechaVencimientoLegal		= false;
	
	
	protected $mFechaMora					= false;
	protected $mFechaUltimoMvtoCapital		= false; //Fecha de ultimo pago de capital
	protected $mPeriocidadDePago			= false;
	protected $mFechaDeSolictud				= false;
	protected $mFechaDeAutorizacion			= false;
	protected $mTipoDePago					= false;
	protected $mDiasAutorizados				= false;
	protected $mPagosAutorizados			= false;
	protected $mParcialidadActual			= false;
	protected $mModalidadDeCredito			= false;
	protected $mTipoDeDestino			= false;
	protected $mCausaDeMora				= false;

	protected $mTasaInteres					= false;
	protected $mTasaMoratorio				= false;
	protected $mTasaAhorro					= false;
	protected $mTasaGarantiaLiquida			= false;
	protected $mInteresDiario				= false;
	protected $mInteresNormalDevengado		= false;
	protected $mInteresNormalPagado			= false;
	protected $mInteresMoratorioDev			= false;
	protected $mInteresMoratorioPag			= false;

	protected $mSdoCapitalInsoluto			= false;
	protected $mMontoMinistrado				= false;
	protected $mMontoAutorizado				= 0;
	protected $mMontoSolicitado				= false;
	protected $mToleranciaEnDiasParaVcto	= false;
	protected $mToleranciaEnDiasParaPago	= 0;
	protected $mEstatusActual				= false;
	protected $mTipoDeConvenio				= false;
	protected $mTipoDeAutorizacion				= false;
	protected $mNuevoSaldo					= false;
	protected $mTipoDeCalculoDeInteres		= false;
	protected $mSaldoVencido				= 0;

	protected $mNumeroDePlanDePago			= false;
	protected $mOficialQueGenera			= false;
	protected $mTasaIVA						= false;
	protected $mIVAIncluido					= false;
	protected $mGrupoAsociado				= false;
	protected $mReciboDeOperacion			= false;
	protected $mFechaOperacion				= false;

	protected $mOficialDeCredito			= false;
	protected $mDatosOficialDeCredito		= false;
	protected $mClaveDeDestino			= false;
	protected $mMontoFijoParcialidad		= 0;

	protected $mCreditoInicializado			= false;
	protected $mArrDatosDeCredito			= array();
	protected $mMessages					= "";
	protected $mErrorCode					= 999;

	protected $mLimitPlan					= 25;
	protected $mDescripcionPeriocidad		= "";
	protected $mDescripcionProducto			= "";
	protected $mDescripcionDestino			= "";
	protected $mDescripcionEstatus			= "";
	protected $mUsuarioActual				= false;
	protected $mContrato_URL				= "";
	protected $mEmpresa						= DEFAULT_EMPRESA;
	protected $mNombrePersonaAsoc			= "";

	protected $mForceMinistracion			= false;
	protected $mCuentaDeGarantiaLiquida		= false;
	//Datos de parcialidades			
	protected $mNumeroProximaParcialidad		= 0;
	protected $mFechaProximaParcilidad		= 0;
	protected $mParcialidadesConSaldo		= 0;
	protected $mContratoCorriente			= CTA_GLOBAL_CORRIENTE;
	
	protected $mFechaPrimeraParc			= ""; 	//plan
	protected $mFechaUltimaParc				= "";	//plan
	protected $mFechaPrimerAtraso			= null; 
	protected $mOB							= null;
	protected $mForceVista					= false;
	protected $mObjRec						= null;		//Objeto de recibo
	protected $mMontoUltimoPago				= 0;
	protected $mFechaUltimoPago				= 0;
	
	protected $mObjEstado					= null;	
	protected $mObjSoc						= null;
	protected $mObjPlan						= null;
	
	protected $mMontoInteresPagado			= 0;
	protected $mMontoMoraPagado				= 0;
	protected $mMontoCapitalPagado			= 0;
	
	protected $mDPrimerPagoEfect			= array();
	protected $mDUltimoPagoEfect			= array();
	protected $mInitPagos					= false;
	protected $mAbonosAcumulados			= 0;
	protected $mFechaAcumulada				= false;
	protected $mPagos						= array();
	protected $mLetrasAtrasadas				= 0;
	protected $mArrFechasDePago				= array();  
	//protected $mOtrosParams					= array();
	//protected $mSaldoAtrasado				= 0;
		
	function __construct($numero_de_credito = false, $numero_de_socio = false){
		$this->mNumeroCredito		= $numero_de_credito;
		$this->mNumeroSocio		= $numero_de_socio;
		$this->mFechaOperacion		= date("Y-m-d");

		if ( isset($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]) ){
			$this->mUsuarioActual	= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
		}
		$this->mOficialQueGenera	= elusuario($this->mUsuarioActual);
	}
	function setResetPersonaAsociada($fecha, $observaciones = "", $empresa = ""){
		$empresa	= ($empresa == "") ? FALLBACK_CLAVE_EMPRESA : $empresa; 
		$xLng		= new cLang();
		$txt		= $xLng->get("saldo") . " : " . $this->getSaldoActual() . "; " . $xLng->get("empresa") . ":" . $this->getClaveDeEmpresa() . "/$empresa . $observaciones" ;
		//Agregar Memo a socio
		$xSoc		= new cSocio($this->getClaveDePersona());
		$this->mMessages	.= $xSoc->setNewMemo(MEMOS_TIPO_DESVINCULACION, $txt , $this->getNumeroDeCredito(), $fecha );
		
		$this->setUpdate(array("persona_asociada" => $empresa));
	}
	/**
	* Funcion que Inicializa el Credito
	* @var array $DCredito Se Refiere al Forzado de Asignacion de Variables, un Array Completo
	*/
	function init($DCredito = false){


	$WithSocio = "";
	/**
	*	Marcar Segunda condicion
	*/
	if ( $this->mNumeroSocio != false ){
		$WithSocio	= "	AND
						(`creditos_solicitud`.`numero_socio` =" . $this->mNumeroSocio . ") ";
	}
		$xSTb					= new cSAFETabla(TCREDITOS_REGISTRO);
		$sql = $xSTb->getQueryInicial() . "
				WHERE
					(`creditos_solicitud`.`numero_solicitud` =" . $this->mNumeroCredito . ")
					$WithSocio
				LIMIT 0,1";
		
		if ( ($DCredito == false) OR ( !is_array($DCredito) ) ){
			$DCredito	= obten_filas($sql);
		}
		if(isset($DCredito["numero_solicitud"])){
			$this->mNumeroCredito				= $DCredito["numero_solicitud"];
			$this->mNumeroSocio					= $DCredito["numero_socio"];
			$this->mFechaMinistracion			= $DCredito["fecha_ministracion"];
			$this->mFechaVencimiento			= $DCredito["fecha_vencimiento"];
			$this->mFechaUltimoMvtoCapital		= $DCredito["fecha_ultimo_mvto"];
			$this->mFechaMora					= $DCredito["fecha_mora"];
			$this->mFechaDeSolictud				= $DCredito["fecha_solicitud"];
			$this->mFechaDeAutorizacion			= $DCredito["fecha_autorizacion"];
			$this->mFechaVencimientoLegal		= $DCredito["fecha_vencimiento_dinamico"];
					
			$this->mPeriocidadDePago			= $DCredito["periocidad_de_pago"];
			$this->mTipoDePago					= $DCredito["tipo_de_pago"];
			$this->mDiasAutorizados				= $DCredito["dias_autorizados"];
			$this->mPagosAutorizados			= $DCredito["pagos_autorizados"];
			$this->mTasaInteres					= $DCredito["tasa_interes"];
			$this->mTasaMoratorio				= $DCredito["tasa_moratorio"];
			$this->mTasaAhorro					= $DCredito["tasa_de_ahorro"];
			$this->mTasaGarantiaLiquida			= $DCredito["porciento_garantia_liquida"];
	
			$this->mSdoCapitalInsoluto			= $DCredito["saldo_actual"];
			$this->mMontoMinistrado				= $DCredito["monto_autorizado"];
			$this->mMontoSolicitado				= $DCredito["monto_solicitado"];
			$this->mToleranciaEnDiasParaVcto	= $DCredito["tolerancia_en_dias_para_vencimiento"];
			$this->mToleranciaEnDiasParaPago	= $DCredito["tolerancia_dias_no_pago"];
			
			$this->mEstatusActual				= $DCredito["estatus_actual"];
			$this->mIVAIncluido					= $DCredito["iva_incluido"];
			$this->mTasaIVA						= $DCredito["tasa_iva"];
			$this->mGrupoAsociado				= $DCredito["grupo_asociado"];
			$this->mTipoDeConvenio				= $DCredito["tipo_convenio"];
			$this->mOficialDeCredito			= $DCredito["oficial_credito"];
	
			$this->mInteresDiario				= $DCredito["interes_diario"];
			$this->mInteresNormalPagado			= $DCredito["interes_normal_pagado"];
			$this->mInteresNormalDevengado		= $DCredito["interes_normal_devengado"];
			$this->mInteresMoratorioDev			= $DCredito["interes_moratorio_devengado"];
			$this->mInteresMoratorioPag			= $DCredito["interes_moratorio_pagado"];
			$this->mDescripcionPeriocidad		= $DCredito["descripcion_periocidadpagos"];
			$this->mDescripcionProducto			= $DCredito["descripcion_tipoconvenio"];
			$this->mDescripcionEstatus			= $DCredito["descripcion_estatus"];
			$this->mTipoDeCalculoDeInteres		= $DCredito["tipo_de_calculo_de_interes"];
			$this->mParcialidadActual			= $DCredito["ultimo_periodo_afectado"];
			$this->mContratoCorriente			= $DCredito["contrato_corriente_relacionado"];
			$this->mMontoAutorizado				= $DCredito["monto_autorizado"];
	
			$this->mModalidadDeCredito			= $DCredito["tipo_credito"];
			$this->mTipoDeDestino				= $DCredito["destino_credito"];
			$this->mClaveDeDestino				= $DCredito["destino_credito"];
			$this->mCausaDeMora					= $DCredito["causa_de_mora"];
			$this->mDescripcionDestino			= $DCredito["descripcion_aplicacion"];
			$this->mTipoDeAutorizacion			= $DCredito["tipo_autorizacion"];
			$this->mMontoFijoParcialidad		= $DCredito["monto_parcialidad"]; //
			$this->mContrato_URL				= $DCredito["path_del_contrato"];
			$this->mEmpresa						= $DCredito["persona_asociada"];
			$this->mSaldoVencido				= $DCredito["saldo_vencido"];
			$this->mFechaUltimoPago				= $DCredito["fecha_ultimo_mvto"];
			$this->mSdoCapitalInsoluto			= $DCredito["saldo_actual"];
			$this->mFechaPrimeraParc			= $DCredito["fecha_de_primer_pago"];
			
			$this->mEmpresa						= ($this->mEmpresa == 0) ? FALLBACK_CLAVE_EMPRESA : $this->mEmpresa;
			if($this->mEmpresa != FALLBACK_CLAVE_EMPRESA ){
				$xEmp	= new cEmpresas($this->mEmpresa); $xEmp->init();
				$this->mNombrePersonaAsoc		= $xEmp->getNombreCorto();
			}
			$this->mArrDatosDeCredito			= $DCredito;
			unset($DCredito);
		//Inicializa el Credito
			$this->mCreditoInicializado = true;
		} else {
			$this->mCreditoInicializado = false;
		}
		return $this->mCreditoInicializado;
	}
	function initCredito($DCredito = false){ return $this->init($DCredito);	}
	/**
	 * Funcion que Busca un Fecha por el Numero de Pagos por el que debe llevar
	 * @param integer	$pago_buscado	Numero de pago del cual se debe buscar la fecha
	 * @param array 	$aD 			establece los parametros por array
	 */
	function getFechaEstimadaPorNumeroDePago($pago_buscado, $aD = false){
		if ($this->mCreditoInicializado == false){ $this->initCredito();	}
		$credito					= $this->mNumeroCredito;
		$socio						= $this->mNumeroSocio;
		$fecha_ministracion			= $this->mFechaMinistracion;
		$fecha_vencimiento 			= $this->mFechaVencimiento;
		$pagos_autorizados			= $this->mPagosAutorizados;
		$periocidad_pago			= $this->mPeriocidadDePago;
		$dias_autorizados			= $this->mDiasAutorizados;
		$dias_tolerancia_no_pago	= $this->mToleranciaEnDiasParaPago;
		$tipo_de_pago				= $this->mTipoDePago;		
		$msg						= "";
		$ql							= new MQL();
		
		$fecha_devuelta				= $fecha_vencimiento;
		if ( $tipo_de_pago == CREDITO_TIPO_PAGO_UNICO OR $tipo_de_pago == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
			$msg				.= "TIPO.PAG\tEl Tipo de pago es UNICO, se devuelve la fecha de Vencimiento: $fecha_vencimiento\r\n";
			$fecha_devuelta			= $fecha_vencimiento;
		} else {
			
		//(is_array($aD) AND isset($aD[]))
		$data						= $ql->getDataRow("SELECT * FROM historial_de_pagos WHERE credito=$credito AND periodo=$pago_buscado LIMIT 0,1");
		if(isset($data["fecha"])){
			$fecha_devuelta			= $data["fecha"];	
		} else {
			//10*15 - 200 = 50
			//HACK se omite un periodo para Interpretarse como Primer Dia de Abono
			$dias_de_tolerancia			= $dias_autorizados - (($pagos_autorizados - 1) * $periocidad_pago);
		
			$fecha_primer_abono			= sumardias($fecha_ministracion, $dias_de_tolerancia);
			$msg						.= "$socio\t$credito\tDIAS_DE_TOLERANCIA:\t$dias_de_tolerancia\r\n";
			$msg						.= "$socio\t$credito\tFECHA_PRIMER_ABONO:\t$fecha_primer_abono\r\n";
		
			$dia_1_ab 					= PQ_DIA_PRIMERA_QUINCENA;
			$dia_2_ab 					= PQ_DIA_SEGUNDA_QUINCENA;
			$dvparcial					= DIAS_PAGO_VARIOS;
		

			$fecha_de_pago				= $fecha_primer_abono;
			//filtra solo pagos Quincenales
			if ( $periocidad_pago != CREDITO_TIPO_PERIOCIDAD_QUINCENAL ){
				//convierte la Fecha de Vencimiento al dia de pago Predeterminado
				$dia_1_ab			= date("d", strtotime($fecha_vencimiento) );
				//$msg				.= "$socio\t$credito\tPAGOS_NO_QUINCENALES:\t$periocidad_pago\r\n";
			}

		$msg						.= "$socio\t$credito\tPERIODO\tFECHA_REFERENCIA\tFECHA_PAGO\r\n";
		
		if ( $this->mCreditoInicializado == true ) {

			for ($i=1; $i<=$pagos_autorizados; $i++){
				if($i == 1){
					$fecha_de_referencia = $fecha_primer_abono;
				} else {
					$fecha_de_referencia = $fecha_de_pago;
				}

	// ------------------------------------ Obtiene la Fecha de Pago ----------------------------------------------
				if($periocidad_pago <= 7){
					//Obtiene el Dia de Ref + dias del periodo
					if($i==1){								//Si es primer pago, es el dia de abono
						$fecha_de_pago = $fecha_de_referencia;
					} else {
						$fecha_de_pago 	= sumardias($fecha_de_referencia, $periocidad_pago);
					}
					$fecha_de_pago 		= set_no_festivo($fecha_de_pago);

				} elseif (($periocidad_pago >= 15) && ($periocidad_pago <= 29)) {
					//Obtiene el Dia de Ref + dias del periodo
					if($i==1){								//Si es primer pago, es el dia de abono
						$fecha_de_pago 	= $fecha_de_referencia;
					} else {
						$fecha_de_pago 	= sumardias($fecha_de_referencia, $periocidad_pago);
					}
					$fecha_de_pago 		= set_dia_abono_quincenal($fecha_de_pago, $dia_1_ab, $dia_2_ab);
					$fecha_de_pago 		= set_no_festivo($fecha_de_pago);

					// Tratamiento Mesual o mas, si es menor a la 1era Quincena, baja al dia dos, si no sube un mes al dia dos...
				} elseif (($periocidad_pago >= 30) && ($periocidad_pago < 60)){
					//Obtiene el Dia de Ref + dias del periodo
					if($i==1){								//Si es primer pago, es el dia de abono
						$fecha_de_pago 	= $fecha_de_referencia;
					} else {
						$fecha_de_pago 	= sumardias($fecha_de_referencia, $periocidad_pago);
					}
					$fecha_de_pago 		= set_dia_abono_mensual($fecha_de_pago, $dia_1_ab);
					$fecha_de_pago 		= set_no_festivo($fecha_de_pago);

				} elseif (($periocidad_pago >= 60) && ($periocidad_pago<360)){
					//Obtiene el Dia de Ref + dias del periodo
					if($i==1){								//Si es primer pago, es el dia de abono
						$fecha_de_pago 	= $fecha_de_referencia;
					} else {
						$fecha_de_pago 	= sumardias($fecha_de_referencia, $periocidad_pago);
					}
					$fecha_de_pago 		= set_dia_abono_mensual($fecha_de_pago, $dia_1_ab);
					$fecha_de_pago 		= set_no_festivo($fecha_de_pago);

				} else {
					// Tratamiento 360 o Semanal
					$fecha_de_pago 		= sumardias($fecha_de_referencia, $periocidad_pago);

				}
	//-----------------------------------------------------------------------------------------------------------------------------------
				//$msg						.= "$socio\t$credito\t$i\t$fecha_de_referencia\t$fecha_de_pago\r\n";
				//Marcar la Fecha de Pago
					if ($i == $pago_buscado){
						$fecha_devuelta		= $fecha_de_pago;
						$msg				.= "$socio\t$credito\tFECHA_BUSCADA:\t$fecha_devuelta\r\n";
						break;
					}
				$dias_para_vencimiento 		= $dvparcial + $dias_tolerancia_no_pago;
				$vencimiento_parcialidad 	= sumardias($fecha_de_pago, $dias_para_vencimiento);	//Fecha de Pago + Dias en que vence la Parc + 1

			}	//END FOR

 		} 		//END IF
		}		//END TIPO DE PAGO VALUATE
		}
 		$this->mMessages					.= $msg;
		return $fecha_devuelta;
	}			//END FUNCTION
	/**
	 * Obtiene en un array los datos del plan de Pagos
	 * @return array Array con los Datos del Plan de Pagos
	 */
	function getDatosDelPlanDePagos(){

			$sqlrs 	= "SELECT
							`operaciones_recibos`.*,
							`operaciones_recibostipo`.*
						FROM
							`operaciones_recibos` `operaciones_recibos`
								INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
								ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
								`idoperaciones_recibostipo`
						WHERE
							(docto_afectado=" . $this->mNumeroCredito . ")
							AND
							(numero_socio = " . $this->mNumeroSocio . ")
							AND
							(tipo_docto=11)
						ORDER BY fecha_operacion DESC
						LIMIT 0,1";
			$d		= obten_filas($sqlrs);
			if ( isset($d["idoperaciones_recibos"]) ){
				$this->mNumeroDePlanDePago	= $d["idoperaciones_recibos"];
				$this->mMessages			.= "OK\tPLAN DE PAGOS " . $this->mNumeroDePlanDePago . " CARGADO \r\n";
			} else {
				$this->mNumeroDePlanDePago	= false;
				$this->mMessages			.= "ERROR\tNO EXISTE EL PLAN DE PAGOS EN EL SISTEMA. SE RECOMIENDA GENERARLO NUEVAMENTE PARA EL BUEN FUNCIONAMIENTO DEL SISTEMA. CONTACTE A SU OFICIAL DE CREDITO \r\n";
			}
			
			return $d;
	}
	function getNumeroDePlanDePagos(){
		if ( setNoMenorQueCero($this->mNumeroDePlanDePago) <= 0 ){ $this->getDatosDelPlanDePagos();	}
		return $this->mNumeroDePlanDePago;
	}
	/**
	 * Muestra el Plan de Pagos en Texto, o HTML, Simple o Completo
	 *
	 * @param string $InOut		Tipo de Sal�ida, por Defecto HTML
	 * @param boolean $simple	Solo Imprime el Plan de Pagos sin detallarla Lista
	 * @param boolen $NoExtend	SI es a TRue solo muestra el Plan de pagos
	 * @return string			Plan de Pagos en Formto definido en OUT
	 */
	function getPlanDePago($InOut = "html", $simple = false, $NoExtend = false){
		$PlanBody		= "";
		if ( $this->mCreditoInicializado == false ){	$this->init();		}
		$xF	= new cFecha();
		$xL	= new cLang();
		$xQ	= new MQL();
		$xF->init(FECHA_FORMATO_MX);
		$xF->setSeparador("/");
		if ( $this->mPagosAutorizados > 1 ){
			if (setNoMenorQueCero($this->mNumeroDePlanDePago) <=0 ){
				$extras 		= $this->getDatosDelPlanDePagos();
			} else {
				$sqlrs = "SELECT *
						FROM operaciones_recibos
						WHERE idoperaciones_recibos=" . $this->getNumeroDePlanDePagos();
				$idrecibo		= $this->getNumeroDePlanDePagos();
				$extras 		= obten_filas($sqlrs);
			}
			//------------------------------------- DATOS DEL RECIBO
			//TODO: Validar IDRECIBO, si es Mayor a cero y si existe 2014-10-31
			//XXX: Terminar valicacion de IDRECIBO 201/10/31
			$idrecibo		= $extras["idoperaciones_recibos"];
			$idsocio 		= $extras["numero_socio"]; //"numero_socio"
			$idsolicitud 	= $extras["docto_afectado"]; // docto_afectado
			$sumrec 		= $extras["total_operacion"];

			$nombre 		= getNombreSocio($idsocio);
			$SUMCap			= 0;
			$SUMInt			= 0;
			$SUMIva			= 0;
			$SUMAh			= 0;
			$SUMOtros		= 0;
			$SumTotal		= 0;
			// ------------------------------------ DATOS DE LA SOLICITUD.
			if ( $this->mCreditoInicializado == false ){
				$sqlsol 	= "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$idsolicitud";
				$dsol 		= obten_filas($sqlsol);
				$tasa_ahorro 	= $dsol["tasa_ahorro"] * 100;
				$tasa_interes 	= $dsol["tasa_interes"] * 100;
				$dias_totales 	= $dsol["dias_autorizados"];
				$numero_pagos	= $dsol["pagos_autorizados"];
				$nombre_otro	= "";
			} else {

				$tasa_ahorro 	= $this->mTasaAhorro * 100;
				$tasa_interes 	= $this->mTasaInteres * 100;
				$dias_totales 	= $this->mDiasAutorizados;
				$numero_pagos	= $this->mPagosAutorizados;
				$nombre_otro	= "";
			}
			$this->mLimitPlan	= ceil(($numero_pagos / 3));
			if ($NoExtend == false){
				$PlanBody 	.= $this->getFichaDeSocio();
				$PlanBody 	.= $this->getFicha();
			}
		if ( $simple == false ){
			$sql = "
					SELECT operaciones_mvtos.periodo_socio AS 'parcialidad', fecha_afectacion,
							operaciones_tipos.idoperaciones_tipos  As 'tipo',
							operaciones_tipos.descripcion_operacion AS 'concepto' ,
							operaciones_mvtos.afectacion_real AS 'monto',
							operaciones_mvtos.saldo_actual AS 'saldo'

					FROM 	`operaciones_mvtos` `operaciones_mvtos`
							INNER JOIN `operaciones_tipos` `operaciones_tipos`
							ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
							`idoperaciones_tipos`

					WHERE operaciones_mvtos.recibo_afectado=$idrecibo
					ORDER BY operaciones_mvtos.periodo_socio, operaciones_tipos.idoperaciones_tipos";


				$rs 		= $xQ->getDataRecord($sql);
				$trs 		= "";
				//Parcialidad, evaluador de inciio y final
				$PInit		= 0;
				$PFin		= 0;

				$capital	= 0;
				$interes	= 0;
				$iva		= 0;
				$ahorro		= 0;
				$otros		= 0;
				$total		= 0;


				$saldo		= 0;

				$tds		= "";
				foreach ($rs as $rw){
					$PInit		= $rw["parcialidad"];
					$tipo		= $rw["tipo"];

					switch($tipo){
						case 410:
							$capital	= $rw["monto"];
							$SUMCap		+= $capital;
							$saldo		= $rw["saldo"];
							if ($saldo < 0){
								$saldo = 0;
							}
							break;
						case 411:
							$interes	= $rw["monto"];
							$SUMInt 	+= $interes;
							break;
						case 412:
							$ahorro		= $rw["monto"];
							$SUMAh		+= $ahorro;
							break;
						case 413:
							$iva		= $rw["monto"];
							$SUMIva		+= $iva;
							break;
						default:
							$otros		= $rw["monto"];
							$SUMOtros	+= $otros;
							$top		= getInfTOperacion($tipo);
							$nombre_otro=$top["descripcion_operacion"];
							break;
					}
					if($PInit!=$PFin){
						$trs .= $tds;
					} else {
						$total	= $capital + $interes + $ahorro + $iva;
						$tdAhorro 	= "<td class='mny'>" . getFMoney($ahorro) . "</td>";
						$tdOtros	= "<td class='mny'>" . getFMoney($otros) . "</td>";

						if ( $ahorro == 0){
							$tdAhorro 	= "";
						}
						if ($otros == 0){
							$tdOtros	= "";
						}

						$tds = "<tr>
							<td>" . $rw["parcialidad"] . "</td>
							<td class='ctr'>" . getDiaDeLaSemana($rw["fecha_afectacion"]) . "</td><td class='ctr'>" . $xF->getFechaCorta($rw["fecha_afectacion"]) . "</td>
							<td class='mny'>" . getFMoney($capital) . "</td>
							<td class='mny'>" . getFMoney($interes) . "</td>
							<td class='mny'>" . getFMoney($iva) . "</td>
							$tdAhorro
							$tdOtros
							<td class='mny'>" . getFMoney($total) . "</td>
							<td class='mny'>" . getFMoney($saldo) . "</td>
						</tr>
						";
					}
					$PFin	= $PInit;
				}
				$trs .= $tds;
				$SumTotal	= $SUMCap + $SUMAh + $SUMInt + $SUMIva + $SUMOtros;
				$thAhorro 	= " <th>" . $xL->getT("TR.Ahorro") ."</th>";
				$thOtros	= "<th>$nombre_otro</th>";
				$tfAhorro	= "<th class='mny'>" . getFMoney($SUMAh) . "</th>";
				$tfOtros	= "<th class='mny'>" . getFMoney($SUMOtros) . "</th>";
				if ($SUMAh == 0){ $thAhorro 	= ""; $tfAhorro	= ""; }
				if ($SUMOtros == 0){ $thOtros 	= ""; $tfOtros	= ""; }
			$PlanBody .= "<table >
			  <tbody>
			    <tr>
			      <th>" . $xL->getT("TR.Pago") ."</th>
			      <th colspan='2'>" . $xL->getT("TR.Fecha de Pago") . "</th>
			      <th>" . $xL->getT("TR.Capital"). "</th>
			      <th>" . $xL->getT("TR.Interes") ."</th>
			      <th>" . $xL->getT("TR.IVA") ."</th>
			     $thAhorro
			      $thOtros
			      <th>" . $xL->getT("TR.Total") . "</th>
			      <th>" . $xL->getT("TR.saldo"). "</th>
			    </tr>
			    $trs
			    <tr>
			      <td colspan='3'>" . $xL->getT("TR.SUMAS") . "</td>
			      <th class='mny'>" . getFMoney($SUMCap) . "</th>
			      <th class='mny'>" . getFMoney($SUMInt) . "</th>
			      <th class='mny'>" . getFMoney($SUMIva) . "</th>
			      $tfAhorro
			      $tfOtros
			      <th class='mny'>" . getFMoney($SumTotal) . "</th>
			      <td />
			    </tr>
			  </tbody>
			</table>";

		} else {
			$sqlparc = "SELECT periodo_socio AS 'parcialidad', MAX(fecha_afectacion)
								AS 'fecha_de_pago', SUM(afectacion_real) AS 'total_parcialidad',
								 MAX(saldo_anterior) AS 'saldo_anterior_', MIN(saldo_actual) AS 'saldo_actual_'
								FROM operaciones_mvtos
							WHERE
								recibo_afectado=$idrecibo
							GROUP BY periodo_socio ORDER BY periodo_socio";
			
			$rs 	= $xQ->getDataRecord($sqlparc);
			$pi 	= 1;
			$tp	= "";
			$pt	= 1;
			$lim	= $xQ->getNumberOfRows();
			//si el Limite es mayor a 100, restar 100 y poner
			if ($lim > ($this->mLimitPlan * 4) ){ $this->mLimitPlan = ceil( ($lim / 4) ); }
			if($lim < $this->mLimitPlan){ $this->mLimitPlan = ceil( ($lim / 2) ); }
			$items	= 1;
			
			foreach ($rs as $rw ){
				$saldo 		= $rw["saldo_actual_"];
				$saldo 		= ( $saldo < 0 ) ? 0 : $saldo;
				$SumTotal	+= $rw["total_parcialidad"];
				if($items == 1){ $this->mFechaPrimeraParc	= $rw["fecha_de_pago"]; }
				
				if ( ($pi == $this->mLimitPlan) OR ($pt == $lim) ){
					$tp .= "
					<tr>
						<td>" . $rw["parcialidad"] . "</td>
						<td>" . $xF->getFechaCorta($rw["fecha_de_pago"]) . "</td>
						<td class='mny'>" . getFMoney($rw["total_parcialidad"]) . "</td>
						<td class='mny'>" . getFMoney($saldo)  . "</td>
					</tr>
					</tbody>
					</table>
					</td>";
					$pi = 1;
				} elseif ($pi == 1){
					
					$tp .= "
						<td>

						<table class='sector_plan_simplificado'>
						<tbody>
						<tr>
							<th>" . $xL->getT("TR.PAGO") ."</th>
							<th>" . $xL->getT("TR.Fecha") . "</th>
							<th>" . $xL->getT("TR.Monto") . "</th>
							<th>" . $xL->getT("TR.Saldo") ."</th>
						</tr>
						<tr>
							<td>" . $rw["parcialidad"] . "</td>
							<td>" . $xF->getFechaCorta($rw["fecha_de_pago"]) . "</td>
							<td class='mny'>" . getFMoney($rw["total_parcialidad"]) . "</td>
							<td class='mny'>" . getFMoney($saldo) . "</td>
						</tr>";
					$pi++;
				} else {
					$tp .= "
						<tr>
							<td>" . $rw["parcialidad"] . "</td>
							<td>" . $xF->getFechaCorta($rw["fecha_de_pago"]) . "</td>
							<td class='mny'>" . getFMoney($rw["total_parcialidad"]) . "</td>
							<td class='mny'>" . getFMoney($saldo) . "</td>
						</tr>";
					$pi++;
				}
				$pt++;
				$items++;
				$this->mFechaUltimaParc		= $rw["fecha_de_pago"];
			}
			$PlanBody	.= "<table class='plan_de_pagos'>
								<tbody>
								$tp
								</tbody>
								<!-- SUMAS -->
								<tfoot>
									<tr>
										<td colspan='2'><hr />TOTAL</th>
										
										<td  class='mny'><hr />" . getFMoney($SumTotal) . "</th>
										
									</tr>
								</tfoot>
							</table>
							";

		} 	//END IF
		$PlanFoot = "<hr />
			<table>
				<tr>
					<td><center>" . $xL->getT("TR.Firma del Solicitante") ."</td>
					<td><center>" . $xL->getT("TR.Genera el Plan de Pagos") ."</center></td>
				</tr>
				<tr>
					<td><br /><br /><br /> </td>
				</tr>
				<tr>
						<td><center>$nombre</center></td>
						<td><center>" . $this->mOficialQueGenera . "</center></td>
				</tr>
				</table>";
				if ($NoExtend == false){
					$PlanBody .= "<hr />
						<table>
						  	<tr>
						    	<td>" . $xL->getT("TR.Dias Totales") . ":</td>
						    	<th>$dias_totales Dias</th>
						    	<td>" . $xL->getT("TR.Tasa de Interes") ." :</td>
						    	<th>$tasa_interes %</th>
						    	<td>" . $xL->getT("TR.Tasa de Ahorro") ." :</td>
						    	<th>$tasa_ahorro %</th>
						    </tr>
					    </table>
						";
					$PlanBody .= $PlanFoot;
				}

		} //End evaluate si es Mas Planes de pagos

		return $PlanBody;
	}		//END FUNCTION
	function getOPlanDePagos(){
		if($this->mObjPlan == null){
			$this->mObjPlan	= new cPlanDePagos($this->getNumeroDePlanDePagos());
			$this->mObjPlan->init();
		}
		return $this->mObjPlan;
	}
	function getDatosOficialDeCredito_InArray(){
		if ($this->mCreditoInicializado == false){
			$this->initCredito();
		}
		if ( $this->mOficialDeCredito != false){
			$sql = "SELECT id, nombre_completo, puesto, sucursal, estatus
    				FROM oficiales
    				WHERE id = " . $this->mOficialDeCredito;
			$this->mDatosOficialDeCredito = obten_filas($sql);
		}
		return $this->mDatosOficialDeCredito;
	}

	/**
	 * Funcion que determina es Estatus de un Credito segun su Tipo de Pago
	 * @param date $fecha_de_corte	Fecha de Estimación
	 * @param boolean $explain	Explicar Estatus
	 */
	function setDetermineDatosDeEstatus($fecha_de_corte = false, $explain = false, $actualizar = false, $DPagos = false){
		$xF								= new cFecha();
		$xLog							= new cCoreLog();
		$fecha_de_corte					= ($fecha_de_corte == false) ? fechasys() : $fecha_de_corte;
		$fecha_de_corte_int				= $xF->getInt($fecha_de_corte);
		$exoExplain                     = "";
		$aviso							= "";
		
		if ($this->mCreditoInicializado == false){ $this->init(); }
		if($this->getEsAfectable() == true){
			
		$credito						= $this->mNumeroCredito;
		$socio							= $this->mNumeroSocio;		
		$fecha_ministracion				= $this->mFechaMinistracion;
		$fecha_vencimiento 				= $this->mFechaVencimiento;
		$pagos_autorizados				= $this->mPagosAutorizados;
		$periocidad_pago				= $this->mPeriocidadDePago;
		$dias_autorizados				= $this->mDiasAutorizados;
		$tipo_de_pago					= $this->mTipoDePago;
		$fecha_ultimo_mvto				= $this->mFechaUltimoMvtoCapital;
		$saldo_insoluto					= $this->mSdoCapitalInsoluto;
		$monto_ministrado				= $this->mMontoMinistrado;
		$dias_tolerados_para_vencer		= $this->mToleranciaEnDiasParaVcto;
		$estatus_actual					= $this->mEstatusActual;
		$interes_diario					= $this->mInteresDiario;
		$pagos_pendientes				= $this->mPagosAutorizados;
		$xPer							= $this->getOPeriocidad();
		$OProd							= $this->getOProductoDeCredito();
		$dias_para_mora					= $OProd->getDiasTolerados();
		$dias_tolerados_para_vencer		= $xPer->getDiasToleradosEnVencer();
		$stat							= array();
		$ql								= new MQL();
		$stat[SYS_ESTADO]						= $this->getEstadoActual();
		$stat[SYS_CREDITO_DIAS_NORMALES]		= 0;
		$stat[SYS_CREDITO_DIAS_MOROSOS]		= 0;
		$stat[SYS_CREDITO_DIAS_VENCIDOS]		= 0;
		$stat["fecha_de_inicio_de_pagos"]		= $fecha_vencimiento;
		$xLog->add("----------CREDITO\t$credito --- Ministrado $fecha_ministracion\r\n", $xLog->DEVELOPER);
		
		if($periocidad_pago == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
			
			//Tomado para los tipos 1: Pago unico a Final de Plazo --- 360 ---
			$mFechaDeInicio				= $fecha_vencimiento;
			$mFechaDeMora 				= $xF->setSumarDias($dias_para_mora, $mFechaDeInicio);
			$mFechaDeVencimiento 		= $xF->setSumarDias($dias_tolerados_para_vencer, $mFechaDeMora);
			$letra_actualmente_pagada	= $this->getPeriodoActual();
		} else {
			//obtener la letra que debe pagar.
			$DFechas						= (is_array($DPagos)) ? $DPagos : $ql->getDataRecord("SELECT * FROM historial_de_pagos WHERE credito=$credito ");
			//==============================================================
			$DataFechas						= array();
			$Historico						= array();
			$letra_que_debe_estar_pagada	= $this->getPeriodoActual();
			$letra_actualmente_pagada		= $this->getPeriodoActual();
			//lo que se ha pagado
			$capital_pagado					= 0;
			$capital_pendiente				= 0;
			$interes_pagado					= 0;
			$interes_pendiente				= 0;
			$interes_nosaldado				= 0;
			$terminado						= false; //determina si ya termino la busqueda
			foreach ($DFechas as $rows ){
				$idxc			= $rows["periodo"];
				$DataFechas[$idxc][SYS_FECHA] 				= $rows["fecha"];
				$DataFechas[$idxc][SYS_CAPITAL] 			= setNoMenorQueCero($rows["capital"]);
				$DataFechas[$idxc][SYS_MONTO] 				= setNoMenorQueCero($rows["pagos"]);
				$DataFechas[$idxc][SYS_INTERES_NORMAL] 		= setNoMenorQueCero($rows["interes"]);
				$DataFechas[$idxc]["SYS_INTERES_PAGADO"] 	= setNoMenorQueCero($rows["interes_pagado"]);
				$DataFechas[$idxc]["SYS_INTERES_SALDO"] 	= setNoMenorQueCero(($rows["interes"] - $rows["interes_pagado"]));
				$capital_pendiente							+= $DataFechas[$idxc][SYS_CAPITAL];
				$capital_pagado								+= $DataFechas[$idxc][SYS_MONTO];
				$interes_pagado								+= $DataFechas[$idxc]["SYS_INTERES_PAGADO"];
				$interes_pendiente							+= $DataFechas[$idxc][SYS_INTERES_NORMAL];
				
				//$xLog->add("WARN\t$idxc\tAgregando pagos $capital_pendiente|$capital_pagado . $interes_pendiente|$interes_pagado \r\n", $xLog->DEVELOPER);
				
				//corregir
				if($rows["fecha"] == "0000-00-00"){
					$idxAnterior						= setNoMenorQueCero(($idxc-1));
					if (isset($DataFechas[$idxAnterior])){
						$DataFechas[$idxc][SYS_FECHA]	= $xF->setSumarDias($periocidad_pago, $DataFechas[$idxAnterior][SYS_FECHA]);
						$ql->setRawQuery("UPDATE operaciones_mvtos SET fecha_afectacion='" . $DataFechas[$idxc][SYS_FECHA] . "' WHERE docto_afectado=$credito AND periodo_socio='$idxc' AND fecha_afectacion='0000-00-00' ");
							$xLog->add("ERROR\t$idxc\tFecha Invalida, corregida\r\n", $xLog->DEVELOPER);
					}
				}
			}
			//lo que debe pagarse
			for($i = 1; $i <= $this->getPagosAutorizados(); $i++){
				if($terminado == false){
					$idpagoanterior		= setNoMenorQueCero($i - 1);
					$idpagoproximo		= setNoMenorQueCero($i + 1);
					if(!isset($DataFechas[$i])){
						//==========================
						$xLog->add("ERROR\t$i\tNumero de Pago con error\r\n", $xLog->DEVELOPER);
						$xPlan					= new cPlanDePagosGenerador();
						$xPlan->initPorCredito($this->mNumeroCredito, $this->getDatosInArray());
						$fecha_de_referencia	= $xF->setSumarDias( ($this->getPeriocidadDePago() * $i), $this->getFechaDeMinistracion() );
						$FechaDePago			= $xPlan->getFechaDePago($fecha_de_referencia, $i);
					} else {
						$Datos			= $DataFechas[$i];
						$fecha			= $Datos[SYS_FECHA];
						if($xF->getInt($fecha) <= $fecha_de_corte_int){
							//creditos con solo interes
							if($this->getPagosSinCapital() == true){
								if($Datos["SYS_INTERES_SALDO"] > 0){
									$fecha		= $xF->setSumarDias(1, $fecha);
									$xLog->add("WARN\t$i\tInicia la fecha de Mora en $fecha porque el interes es " . $Datos["SYS_INTERES_SALDO"] . " \r\n", $xLog->DEVELOPER);
									$FechaDePago	= $fecha;
									$terminado		= true;
								}
					
							} else {
								$saldoLetra		= setNoMenorQueCero(($Datos[SYS_CAPITAL] - $Datos[SYS_MONTO]) );
								if($saldoLetra >0){
									$fecha		= $xF->setSumarDias(1, $fecha);
									$xLog->add("ERROR\t$i\tInicia la fecha de Mora en $fecha porque el Saldo de la Letra es $saldoLetra \r\n", $xLog->DEVELOPER);
									$FechaDePago	= $fecha;
									$terminado		= true;
								}
							}
						} else {
							$xLog->add("WARN\t$i\tFecha de Pago($fecha) Superior a la Fecha de corte ($fecha_de_corte)\r\n", $xLog->DEVELOPER);
							$FechaDePago	= $fecha;
							$terminado		= true;
						}
					}
				}
			}
			//==============================================================
			if(!isset($FechaDePago)){
				$xPlan					= new cPlanDePagosGenerador();
				$xPlan->initPorCredito($this->mNumeroCredito, $this->getDatosInArray());
				$fecha_de_referencia	= $xF->setSumarDias( ($this->getPeriocidadDePago() * $this->getPeriodoActual()), $this->getFechaDeMinistracion() );
				$FechaDePago			= $xPlan->getFechaDePago($fecha_de_referencia, $this->getPeriodoActual());
				$xLog->add("ERROR\tError en la Fecha. Se carga por estimacion $FechaDePago del periodo " . $this->getPeriodoActual() . " \r\n", $xLog->DEVELOPER);
				//corregir y actualizar
			}
			//$letra_que_debe_estar_pagada	= ($letra_que_debe_estar_pagada ==0) ? 1 : $letra_que_debe_estar_pagada;
			//$FechaDePago					= $Historico[$letra_que_debe_estar_pagada][SYS_FECHA];
			$mFechaDeInicio					= $FechaDePago;
			$mFechaDeMora					= $xF->setSumarDias($dias_para_mora, $mFechaDeInicio);
			$mFechaDeVencimiento			= $xF->setSumarDias($dias_tolerados_para_vencer, $mFechaDeMora);			
			//$xLog->add("OK\t$idxc\t=" . $letra_que_debe_estar_pagada  . "|" . $letra_actualmente_pagada . "|" . $FechaDePago . "|$mFechaDeInicio|$mFechaDeMora|$mFechaDeVencimiento\r\n", $xLog->DEVELOPER);
//			$xLog->add("Letra pagara $letra_actualmente_pagada , que debe pagar $letra_que_debe_estar_pagada\r\n", $xLog->DEVELOPER);
			//$pagos_pendientes				= setNoMenorQueCero( $this->mPagosAutorizados - $letra_actualmente_pagada );
		}
		
		$stat[SYS_CREDITO_DIAS_NORMALES]					= setNoMenorQueCero( $xF->setRestarFechas($mFechaDeMora, $fecha_ministracion) );
		$stat[SYS_CREDITO_DIAS_MOROSOS]						= setNoMenorQueCero( $xF->setRestarFechas($mFechaDeVencimiento, $mFechaDeMora) );
		$stat[SYS_CREDITO_DIAS_VENCIDOS]					= setNoMenorQueCero( $xF->setRestarFechas($fecha_de_corte, $mFechaDeVencimiento) );
			
		$stat["fecha_de_inicio"]							= $mFechaDeInicio;
		$stat["fecha_de_mora"] 								= $mFechaDeMora;
		$stat["fecha_de_vencimiento"]						= $mFechaDeVencimiento;
			/**
			 * Calcular el Estatus por metodo reversivo
			 */
		
			if ( $xF->getInt($fecha_de_corte) >= $xF->getInt($mFechaDeVencimiento) ){
				$xLog->add( "ERROR\tA.VENC\tLa fecha de corte (". $xF->getFechaCorta($fecha_de_corte) . ") es mayor a la de vencimiento(". $xF->getFechaCorta( $mFechaDeVencimiento) . ") por o que se da como ***VENCIDO***\r\n");
				$stat[SYS_ESTADO]	= CREDITO_ESTADO_VENCIDO;
				
			} else if ( ($xF->getInt($fecha_de_corte) >= $xF->getInt( $mFechaDeMora )) AND ($xF->getInt($fecha_de_corte) < $xF->getInt($mFechaDeVencimiento)) ) {
				$xLog->add("WARN\tA.MOR\tLa fecha de corte (". $xF->getFechaCorta($fecha_de_corte) . ") es mayor a la de Mora(". $xF->getFechaCorta( $mFechaDeMora) . ") y Menor a la Fecha de Vencimiento (". $xF->getFechaCorta( $mFechaDeVencimiento) . ") por lo que se da como ***MOROSO***\r\n");
				$stat[SYS_ESTADO]	= CREDITO_ESTADO_MOROSO;
				$stat[SYS_CREDITO_DIAS_VENCIDOS]			= 0;
			} else if ( $xF->getInt($fecha_de_corte) < $xF->getInt($mFechaDeMora) ){
				$xLog->add("OK\tA.VIG\tLa fecha de corte (". $xF->getFechaCorta($fecha_de_corte) . ") es mayor a la de Mora(". $xF->getFechaCorta( $mFechaDeMora) . ") por o que se da como ***VIGENTE***\r\n");
				$stat[SYS_ESTADO]	= CREDITO_ESTADO_VIGENTE;
				$stat[SYS_CREDITO_DIAS_VENCIDOS]			= 0;
				$stat[SYS_CREDITO_DIAS_MOROSOS]				= 0;
			} else {
				
			}
					
						
			$this->mFechaVencimientoLegal				= $mFechaDeVencimiento;
			$this->mFechaMora							= $mFechaDeMora;
			//if($actualizar == true){ $xLog->add("Se actualizaran saldos12\r\n", $xLog->DEVELOPER);}
			if($explain == true){ $stat["notas"]		= $xLog->getMessages();	} else { $stat["notas"] = ""; }
			
			
			if($actualizar == true){
				$arrUpdate	= array (
						"fecha_mora" => $mFechaDeMora,
						"fecha_vencimiento_dinamico" => $mFechaDeVencimiento,
						"estatus_actual" => $stat[SYS_ESTADO]
				);
				$this->setUpdate($arrUpdate);
				$xLog->add("WARN\tActualizar Fecha de Mora a $mFechaDeMora y Fecha de Vencimiento a $mFechaDeVencimiento\r\n", $xLog->DEVELOPER);
			}
			$this->mMessages					.=$xLog->getMessages();
			if ($explain == true){
				$aviso			.= $this->getMessages(OUT_HTML);
				$exoExplain		= "<fieldset>
                    <legend>Explicacion de estatus al ". $xF->getFechaCorta($fecha_de_corte) . "</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th class='izq'>Fecha de Ministracion</th>
                                <td>". $xF->getFechaCorta($fecha_ministracion) ."</td>
                                <th class='izq'>Fecha de Inicio de Pagos</th>
                                <td>". $xF->getFechaCorta($stat["fecha_de_inicio_de_pagos"])  . "</td>
                            </tr>
                            <tr>
                                <th class='izq'>Pagos Autorizados</th>
                                <td>$pagos_autorizados</td>
                                <th class='izq'>Periocidad de Pagos</th>
                                <td>$periocidad_pago</td>

                            </tr>
                            <tr>
                                <th class='izq'>Monto Ministrado</th>
                                <td>$monto_ministrado</td>
                                <th class='izq' >Saldo Insoluto</th>
                                <td>$saldo_insoluto</td>
                            </tr>
                            <tr>
                                <th class='izq'>Pagos Efectuados</th>
                                <td>$letra_actualmente_pagada</td>
                                <th class='izq'>Pagos Pendientes</th>
                                <td>$pagos_pendientes</td>
                            </tr>
                            <tr>
                                <th class='izq'>Fecha de Inicio de Calculo</th>
                                <td>". $xF->getFechaCorta( $mFechaDeInicio) . "</td>
                                <th class='izq'>Fecha de Mora</th>
                                <td>". $xF->getFechaCorta($mFechaDeMora)  . "</td>
                            </tr>
                            <tr>
                                <th class='izq'>Fecha de Vencimiento</th>
                                <td>". $xF->getFechaCorta($mFechaDeVencimiento)  . "</td>
                                <th class='izq'>Estatus Determinado</th>
                                <td>" . $stat["estatus"] . "</td>
                            </tr>

                            <tr>
                                <th class='izq'>Dias Vigentes</th>
                                <td>" . $stat[SYS_CREDITO_DIAS_NORMALES]  . "</td>
                                <th class='izq'>Dias Morosos</th>
                                <td>" . $stat[SYS_CREDITO_DIAS_MOROSOS]  . "</td>
                            </tr>
                            <tr>
                                <th class='izq'>Dias Vencidos</th>
                                <td>" . $stat[SYS_CREDITO_DIAS_VENCIDOS]  . "</td>
                                <th class='izq'>Tipo de Pago</th>
                                <td >$tipo_de_pago</td>
                            </tr>
                            <tr>
                                <th colspan='4'>$aviso</th>
                            </tr>
                        </tbody>
                    </table>
					</fieldset>";
				return $exoExplain;
			} else {
				return $stat;
			}
		}
	}		//END FUNCTION
	/**
	 * Funcion que Actualiza el Credito segun un array tipo Campo=>valor
	 *
	 */
	function setUpdate($aParam){
		$WithSocio = "";
		/**
		*	Marcar Segunda condicion
		*/
		if ( $this->mNumeroSocio != false )	{
			$WithSocio	= "	AND
							(`creditos_solicitud`.`numero_socio` =" . $this->mNumeroSocio . ")  ";
		}
		$sqlBody		= "";
		if ( is_array($aParam) AND count($aParam) >=1 ){
			$BodyUpdate = "";
			foreach ($aParam as $key => $value) {
				//Buscar en el Valor el Nombre del Field
				//$pos	= stripos($value, $key);
				//Si el Valor es una Cadena y no existe el Nombre del field
				if ( is_string($value)  ){
					$value		= "\"" . $value . "\"";
				}
				if ($BodyUpdate == ""){
					$BodyUpdate .= "$key = $value ";
				} else {
					$BodyUpdate .= ", $key = $value ";
				}
			}	//END FOREACH
			$sqlBody	= "UPDATE creditos_solicitud SET $BodyUpdate
					   WHERE
						(`creditos_solicitud`.`numero_solicitud` =" . $this->mNumeroCredito . ")
						$WithSocio ";
			$x 			= my_query($sqlBody);
			
			return $x["stat"];
		} else {
			return false;
		}
	}
	/**
	 * Funcion que genera el Credito Reconvenido
	 * @param float $monto_reconvenido	Monto del capital por el Cual se reconviene el credito
	 * @param float $interes_reconvenido	Monto del interes por el Cual se reconviene el credito
	 *
	 * @return boolean false/true 			segun el resultado de la funcion
	 */
	function setReconvenido( $monto_reconvenido, $interes_reconvenido, $tasa_reconvenida,
						$periocidad_reconvenida, $pagos_reconvenidos, $observaciones ="",
						$fecha = false, $recibo = false, $FormaDePago = false, $producto = false, $conservarPlan = false){
		$sucess 				= false;
		$fecha 					= ($fecha == false) ? fechasys() : $fecha;
		$producto				= ($producto == false) ? $this->getClaveDeProducto() : $producto;
		$plan_de_pagos			= $this->getNumeroDePlanDePagos();
		
		if ($this->mCreditoInicializado == true){

			$dias				= $periocidad_reconvenida * $pagos_reconvenidos;
			$vence				= sumardias($fecha, $dias);
			$credito			= $this->getNumeroDeCredito();
			//$interes_normal = ($saldo_historico * $dias_normales * ($tasa_interes * $factor_interes)) / EACP_DIAS_INTERES;

			$interes_normal		= 0;
			$FInteres			= new cFormula("interes_normal");
			$saldo_historico	= $this->getMontoAutorizado();// $monto_reconvenido;
			$saldo_actual		= $this->getSaldoActual(); //$monto_reconvenido;
			$dias_normales		= $dias;
			$tasa_interes		= $tasa_reconvenida;
			$factor_interes		= 1;

				if ( $this->mIVAIncluido == "1"){
					$factor_interes	= 1 / (1 + $this->getTasaIVA() );
				}
			//eval( $FInteres->getFormula() );
			
			//Agregar el SQL
			$xRC				= new cCreditos_reconvenio();
			$idconvenio			= $xRC->query()->getLastID();
			$xRC->idcreditos_reconvenio( $idconvenio );
			$xClon				= $this->setClonar($saldo_actual, $saldo_historico);
			
			$xRC->numero_solicitud($xClon);
			$xRC->codigo($this->getClaveDePersona());
			$xRC->credito_origen($this->getNumeroDeCredito());
			$xRC->dias( $dias );
			$xRC->eacp(EACP_CLAVE );
			$xRC->fecha_reconvenio($fecha);
			$xRC->idusuario( getUsuarioActual() );
			$xRC->interes_diario_re( $interes_normal );
			$xRC->interes_pendiente( $interes_reconvenido );
			$xRC->monto_reconvenido( $monto_reconvenido );
			$xRC->pagos_reconvenidos( $pagos_reconvenidos );
			$xRC->periocidad_reconvenida( $periocidad_reconvenida );
			$xRC->sucursal( getSucursal() );
			$xRC->tasa_reconvenida( $tasa_reconvenida );
			$xRC->vence( $vence );
			$x	= $xRC->query()->insert()->save();
		
			if ( $x != false ){
				//Modificar movimientos
				$observaciones		= ($observaciones == "") ? "" : "RNV $idconvenio. $credito|$xClon";
				//Agregare el Movimiento
				$cRecReest		= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, true, $recibo);
				if ($recibo == false ){
					$cRecReest->setNuevoRecibo($this->mNumeroSocio, $xClon, $fecha, 1, RECIBOS_TIPO_ESTADISTICO, $observaciones, DEFAULT_CHEQUE, TESORERIA_COBRO_NINGUNO, DEFAULT_RECIBO_FISCAL, $this->mGrupoAsociado);
				}
			
				/*
				 * No generar poliza, ya que no ha cumplido el pago sostenido
				 */
				
				//agregar Movimiento por el Monto Reconvenido
				$cRecReest->setNuevoMvto($fecha, $monto_reconvenido, OPERACION_CLAVE_REESTRUCTURA, 1, $observaciones, 1);
				$this->mMessages	.= $cRecReest->getMessages();
				//agregar el Movimiento por Intereses no pagados
				$xCredClon		= new cCredito($xClon); $xCredClon->init();
				
				//Actualizar el saldo y demas
				$cEsUp			= array(
										$this->obj()->tipo_autorizacion()->get() => CREDITO_AUTORIZACION_REESTRUCTURADO,
										$this->obj()->saldo_vencido()->get() => 0
									);
				$xCredClon->setUpdate($cEsUp);
				$this->setUpdate($cEsUp);
				
				$xCredClon->setAbonoCapital($this->getSaldoActual(), $this->mParcialidadActual, DEFAULT_CHEQUE, TESORERIA_COBRO_NINGUNO, DEFAULT_RECIBO_FISCAL, $observaciones);
				
				$this->mMessages	.= $xCredClon->getMessages();
				$sucess				= true;
				$this->mMessages	.= $this->setChangeNumeroDeSolicitud($xClon, true);
				//Cambiar Producto
				$this->mMessages	.= $this->setCambioProducto($producto, $tasa_reconvenida);
				//cambiar fecha de ministracion
				$this->mMessages	.= $this->setCambiarFechaMinistracion($fecha);
				//Cambiar Monto Ministrado
				//
				$this->mMessages	.= $this->setCambiarMontoAutorizado($monto_reconvenido, true);
				//Ministrar
				$this->setForceMinistracion();
				$this->setMinistrar(DEFAULT_RECIBO_FISCAL, DEFAULT_CHEQUE, $monto_reconvenido, DEFAULT_CUENTA_BANCARIA,0,0,
					"REESTRUCTURA DE CREDITO $xClon ", $fecha, false, TESORERIA_PAGO_NINGUNO );
				if( $this->getTasaDeInteres() != $tasa_reconvenida ){
					$this->mMessages	.= $this->setCambiarTasaNormal($tasa_reconvenida);
				}
				$this->mMessages	.= $this->setCambiarPeriocidad($periocidad_reconvenida, $pagos_reconvenidos, $FormaDePago );
				$this->mMessages	.= $this->setCambiarMontoMinistrado($monto_reconvenido, true);
				if($conservarPlan == true){
					if($plan_de_pagos != false){
						//$xRec		= new cReciboDeOperacion(RECIBOS_TIPO_PLAN_DE_PAGO, true, $plan_de_pagos);
						//$xRec->init();
						//$xRec->setDocumento($this->getNumeroDeCredito());
						$xPlan		= new cPlanDePagos($plan_de_pagos);
						//$xPlan->init();
						$xPlan->setCambiarRelacionados($credito);
						$this->mMessages	.= $xPlan->getMessages(OUT_TXT);
					}
				}
			}	//END IF
			//$this->setReestructurarIntereses();
		}		//END Credito Inicializado
		return $sucess;
	}			//END FUNCTION
	/**
	 * funcion que devuelve un array por el credito
	 * @return array Matriz de los Datos del credito INICIALIZADO
	 */
	function getDatosDeCredito(){
		if ($this->mCreditoInicializado == false){
			$this->init();
		}
		return $this->mArrDatosDeCredito;
	}
	function getDatosInArray(){ return $this->getDatosDeCredito(); }
	function getDatosDeProducto($convenio = false){
		$convenio	= ( $convenio == false ) ? $this->mTipoDeConvenio : $convenio;
		$xCV		= new cProductoDeCredito($convenio); $xCV->init();
		
		return  $xCV->getDatosInArray();
	}
	/**
	 * devuelve en una array los datos del convenio de credito
	 * */
	function getDatosDeReconvenio(){
	 	$arrDR	= array();

	 	if ( $this->mNumeroCredito != false ){
	 		$BySocio	= "";
	 		if ( $this->mNumeroSocio != false ){
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
	 		$arrDR		= obten_filas($sql);
	 	}
	 	return $arrDR;
	}		//End Function getdatosdereconvenio
	/**
	 * Actualiza los Creditos Reconvenidos
	 * @param array $aParam	Parametros de Field => Valor a actualizar
	 * @return boolean	Resultado Booleano de la Actualizacion
	 */
	function setUpdateReconvenio($aParam){
		$withSocio = "";
		/**
		*	Marcar Segunda condicion
		*/
		if ( $this->mNumeroSocio != false )	{
			$withSocio	= "	AND
							(`creditos_reconvenio`.`codigo` =" . $this->mNumeroSocio . ")  ";
		}
		$sqlBody	= "";

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
		$sqlBody	= "UPDATE creditos_reconvenio
						    SET $BodyUpdate
						    WHERE
					(`creditos_reconvenio`.`numero_solicitud` =" . $this->mNumeroCredito . ")
					$WithSocio";
		$x = my_query($sqlBody);

		return $x["stat"];

	}
	/**
	 * Funcion que Envia a Creditos Vigentes un Vencido
	 * @param $fecha Fecha de Movimiento
	 * @param $parcialidad   Parcialidad en que se envia a vencido
	 * @param $recibo        Numero de Recibo que Carga el Movimiento
	 * @return	string	Mensajes del proceso.
	 **/
	function setEnviarVigente($fecha, $parcialidad, $recibo = false){
			//Modificar agregar el cambio de estatus
			$FMora		= $this->mFechaMora;
			$FVenc		= $this->mFechaVencimientoLegal;
			$monto		= $this->mSdoCapitalInsoluto;
			$socio		= $this->mNumeroSocio;
			$solicitud	= $this->mNumeroCredito;
			$estatus	= CREDITO_ESTADO_VIGENTE;
			$xT			= new cTipos();
			$msg		= "";
			if( $xT->cBool(CREDITO_GENERA_MVTO_VIGENTE) == true){
				//========================================================
				$xRec		= new cReciboDeOperacion(10);
				$xRec->setNumeroDeRecibo($recibo);
				//========================================================
			//
			$nota		= "Estatus [$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc";
						//agregar Movimiento con Partida
			$xMvto	= $xRec->setNuevoMvto($fecha, $monto, 114, $parcialidad,
										 $nota, 1, TM_CARGO, $socio, $solicitud);
						//Agregar Contrapartida
					$xRec->addMvtoContable(111, $monto, TM_ABONO, $socio, $solicitud);
			$msg		= "$socio\t$solicitud\tVIGENTE\tEstatus[$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc\r\n";
			} else {
				$this->setCambiarEstadoActual($estatus, $fecha);
			}
			$this->mMessages	.= $msg;
			return		$msg;
	}
	/**
	 * Funcion que Efectua los pasos necesarios para que un credito esté vencido.
	 * @param	date	$fecha		Fecha de Proceso
	 * @param	integer	$parcialidad	Numero de Parcialidad
	 * @param	integer	$recibo		Numero de Recibo al que se agrega el movimiento, si es false se crea uno.
	 * @return	string	Mensajes del proceso.
	 **/
	function setEnviarVencido($fecha, $parcialidad, $recibo = false){
			$FMora		= $this->mFechaMora;
			$FVenc		= $this->mFechaVencimientoLegal;
			$monto		= $this->mSdoCapitalInsoluto;
			//========================================================
			$xRec		= new cReciboDeOperacion(10);
			$xRec->setNumeroDeRecibo($recibo);
			//========================================================
			$socio		= $this->mNumeroSocio;
			$solicitud	= $this->mNumeroCredito;
			$estatus	= 20;

			$nota		= "Estatus [$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc";

						//agregar Movimiento con Partida
			$xMvto	= $xRec->setNuevoMvto($fecha, $monto, 111, $parcialidad,
										 $nota,	 1, TM_CARGO, $socio, $solicitud);
			//Agregar Contrapartida
			$xRec->addMvtoContable(OPERACION_CLAVE_MINISTRACION, $monto, TM_ABONO, $socio, $solicitud);

			//Agrega las Modificaciones del Credito
			//$arrUpdate	= array( "estatus_actual" => $estatus,
			//					 "notas_auditoria" => $nota );

			//$this->setUpdate($arrUpdate);
			$msg		= "$socio\t$solicitud\tVENCIDO\tEstatus[$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc\r\n";
			return		$msg;
	}
	function setEnviarMoroso($fecha, $parcialidad, $recibo = false){
			$FMora		= $this->mFechaMora;
			$FVenc		= $this->mFechaVencimientoLegal;
			$monto		= $this->mSdoCapitalInsoluto;
			$socio		= $this->mNumeroSocio;
			$solicitud	= $this->mNumeroCredito;
			$estatus	= CREDITO_ESTADO_MOROSO;
			$xT			= new cTipos();
			$msg		= "";
			if( $xT->cBool(CREDITO_GENERAR_MVTO_MORA) == true){
				//========================================================
				$xRec		= new cReciboDeOperacion(10);
				$xRec->setNumeroDeRecibo($recibo);
				//========================================================
				$nota		= "Estatus [$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc";
							//agregar Movimiento con Partida
				$xMvto		= $xRec->setNuevoMvto($fecha, $monto, 115, $parcialidad,
											 $nota,	 1, TM_CARGO, $socio, $solicitud);
							//Agregar Contrapartida
						$xRec->addMvtoContable(OPERACION_CLAVE_MINISTRACION, $monto, TM_ABONO, $socio, $solicitud);
				$msg		= "$socio\t$solicitud\tMOROSO\tEstatus[$estatus] Modificado en el cierre del $fecha, Mora: $FMora, Vence: $FVenc\r\n";
			} else {
				$this->setCambiarEstadoActual($estatus, $fecha);
			}
			$this->mMessages	.= $msg;
			return		$msg;
	}
	/**
	 * Verifica la validez del Credito con Distintos parametros
	 * @param integer	$consecutivo 	Numero Consecutivo de la Validacion, no requerido ni vital.
	 * @param string	$put			Tipo de salida html:txt
	 * @return string					Mensages de validación
	 */
	function setVerificarValidez($consecutivo = 1, $put = "txt"){
		$msgIncidencias		= "";
		$mReportar			= false;
		$arrEquivalencias 	= array(
								"tasa_interes" => "interes_normal",
								"tasa_moratorio" => "interes_moratorio",
								"tasa_ahorro" => "tasa_ahorro",
								"tipo_autorizacion" => "tipo_autorizacion",
								"tipo_credito" => "tipo_de_credito"
								);
		$xF					= new cFecha();
		
		$DConv				= getInfoConvenio($this->mTipoDeConvenio);
		$OConv				= $this->getOProductoDeCredito();
		$DCred				= $this->getDatosDeCredito();
		//$msgIncidencias	.= "$consecutivo\tVERIFICANDO EL CREDITO " . $this->mNumeroCredito . " DEL SOCIO              " . $this->mNumeroSocio . "\r\n";
		//verificar los Datos Coincidentes con el Convenio
		$iChkConv			= count($arrEquivalencias) - 1;
			foreach( $arrEquivalencias as $key=>$value){
				if ( $DCred[$key] != $DConv[$value]){
					$txt	= strtoupper($key);
					$msgIncidencias	.= "$txt\tEl Valor de $txt es igual a " . $DCred[$key] . " cuando debe ser " . $DConv[$value] . " \r\n";
					$mReportar			= true;
				}
			}
		//Verificar "pagos_autorizados" => "pagos_maximo"
				if ( $DCred["pagos_autorizados"] > $DConv["pagos_maximo"]){
					$msgIncidencias	.= "PAGOS_MAX\tEl Valor de Pagos Autorizados es igual a " . $DCred["pagos_autorizados"] . " cuando debe ser como Maximo " . $DConv["pagos_maximo"] . " \r\n";
					$mReportar			= true;
				}
		//Verificar pagos Minimos de Credito
				$pagos_minimos	= round( (EACP_DIAS_MINIMO_CREDITO / $DCred["periocidad_de_pago"]) );
				if ( ($DCred["pagos_autorizados"] <= $pagos_minimos) AND ($DCred["periocidad_de_pago"] != 360) ){
					$msgIncidencias	.= "PAGOS_MIN\tEl Valor de Pagos Autorizados es igual a " . $DCred["pagos_autorizados"] . " cuando debe ser como MINIMO $pagos_minimos \r\n";
					$mReportar			= true;
				}
				if ( ($DCred["pagos_autorizados"] < 1) AND ($DCred["periocidad_de_pago"] == 360) ){
					$msgIncidencias	.= "PAGOS_MIN\tEl Valor de Pagos Autorizados es igual a " . $DCred["pagos_autorizados"] . " cuando debe ser como MINIMO 1 \r\n";
					$mReportar			= true;
				}
		//Verificar dias Autorizados
				$diasCred	= $DCred["dias_autorizados"];
				$diasConv	= ( ($DConv["dias_maximo"] ) + $DConv["tolerancia_dias_primer_abono"] ) ;
				if ( $diasCred > $diasConv ){
					$msgIncidencias	.= "DIAS_MAX\tEl Valor de Dias Autorizados($diasCred) debe ser como Maximo $diasConv \r\n";
					$mReportar			= true;
				}
		//Verificar dias Autorizados sean Mayores a los MINIMOS
				$diasCred	= $DCred["dias_autorizados"];
				$diasConv	= ( EACP_DIAS_MINIMO_CREDITO + $DConv["tolerancia_dias_primer_abono"] ) ;
				if ( $diasCred < $diasConv ){
					$msgIncidencias		.= "DIAS_MIN\tEl Valor de Dias Autorizados($diasCred) debe ser Mayor a $diasConv \r\n";
					$mReportar			= true;
				}

		//verificar Monto Maximo autorizado
				if ( $DCred["monto_autorizado"] > $DConv["maximo_otorgable"]){
					$msgIncidencias		.= "MONTO\tEl Valor de Monto Otorgado(" . $DCred["monto_autorizado"] . ") debe ser como Maximo " . $DConv["maximo_otorgable"] . " \r\n";
					$mReportar			= true;
				}

		//Verificar STATUS vs SALDO
		//98: Autorizado.- puede ser una constante
				if( ($DCred["estatus_actual"] >= 98) AND ( $DCred["saldo_actual"] > 0  ) ){
					$msgIncidencias		.= "ESTATUS\tEl Estatus de Credito es INCORRECTO, Existe UN SALDO VALIDO \r\n";
					$mReportar			= true;
				}
		//verificar Fecha de Vencimiento
				$FVencOrd	= $DCred["fecha_vencimiento"];
				$FVencCalc	= $xF->getDiaHabil( $xF->setSumarDias( $DCred["dias_autorizados"], $DCred["fecha_ministracion"]) );
				if ( strtotime($FVencOrd) > strtotime($FVencCalc)){
					$msgIncidencias	.= "FECHA_VENC\tLa Fecha de Vencimiento Ordinario($FVencOrd) es Mayor a el que deberia tener($FVencCalc) \r\n";
					$mReportar			= true;
				}
		//Verificar Oficial de Credito
				if ( USE_OFICIAL_BY_PRODUCTO == true){
						if ( $DCred["oficial_seguimiento"] != $DConv["oficial_seguimiento"]){
							$msgIncidencias	.= "OFICIAL_CREDITO\tEl Oficial de Seguimiento es " . $DCred["oficial_seguimiento"] . " cuando debe ser  " . $DConv["oficial_seguimiento"] . " \r\n";
							$mReportar			= true;
						}
						if ( $DCred["oficial_credito"] != $DConv["oficial_seguimiento"]){
							$msgIncidencias	.= "OFICIAL_SEGUIMIENTO\tEl Oficial de Credito es " . $DCred["oficial_credito"] . " cuando debe ser  " . $DConv["oficial_seguimiento"] . " \r\n";
							$mReportar			= true;
						}
				}
		//Verificar si el tipo de Convenio es Grupal, debe tener un grupo Asignado Diferente a DEFAULT_GROUP
				if ( $OConv->getEsProductoDeGrupos() == true){
					if ($DCred["grupo_asociado"] == DEFAULT_GRUPO){
						$msgIncidencias	.= "GRUPO\tEl Convenio es 'GRUPAL' pero no tiene un Grupo Asignado, tiene " . $DCred["grupo_asociado"] . " \r\n";
						$mReportar			= true;
					}
				}
		//verificar si el usuario existe
		$sqlusr = "SELECT COUNT(idusuarios) AS 'valido'
					    FROM usuarios
					WHERE idusuarios = " . $DCred["idusuario"];
					$DUsr = obten_filas($sqlusr);
					if ( !isset($DUsr["valido"]) OR $DUsr["valido"] == 0 ){
						$msgIncidencias	.= "PROPIETARIO\tEl Propietario # " . $DCred["idusuario"] . " NO EXISTE \r\n";
						$mReportar			= true;
					}
					unset($DUsr);

		//verificar si el socio existe
		$sqlusr = "SELECT COUNT(codigo) AS 'valido'
					    FROM socios_general
					WHERE codigo = " . $DCred["numero_socio"];
					$DUsr = obten_filas($sqlusr);
					if ( !isset($DUsr["valido"]) OR $DUsr["valido"] == 0 ){
						$msgIncidencias	.= "SOCIO\tEl Socio # " . $DCred["numero_socio"] . " NO EXISTE \r\n";
						$mReportar			= true;
					}
					unset($DUsr);
		//verificar si la cuenta de captacion existe
		$sqlusr = "SELECT numero_socio, COUNT(numero_cuenta) AS 'valido'
					    FROM captacion_cuentas
					WHERE numero_cuenta = " . $DCred["contrato_corriente_relacionado"] . " GROUP BY numero_socio "; 
					$DUsr = obten_filas($sqlusr);
					if ( !isset($DUsr["valido"]) OR $DUsr["valido"] == 0 ){
						$msgIncidencias	.= "CAPTACION\tla Cuenta Corriente  # " . $DCred["contrato_corriente_relacionado"] . " NO EXISTE \r\n";
						$mReportar			= true;
					}
		//validar Numero de Socio de Cuenta === Numero de Socio de Credito
					if ( $DCred["numero_socio"] != $DUsr["numero_socio"] ){
						$msgIncidencias	.= "CAPTACION.SOC\tLa Persona del Credito " . $DCred["numero_socio"] . " es DIFERENTE al de el Contrato Corriente Relacionado " . $DUsr["numero_socio"] . " \r\n";
						$mReportar			= true;
					}
					unset($DUsr);
		//verificar si el convenio existe

		//verificar si el grupo existe
        $sqlGrp     = "SELECT COUNT(idsocios_grupossolidarios) AS 'grupos'
                        FROM socios_grupossolidarios
                        WHERE idsocios_grupossolidarios=" . $DCred["grupo_asociado"] . " ";
                        $DG = obten_filas($sqlGrp);
                        if ( !isset($DG["grupos"]) OR $DG["grupos"] <= 0 ){
                            $msgIncidencias	.= "IDGRUPO\tEl Numero de Grupo no Existe " . $DCred["grupo_asociado"] . " NO EXISTE \r\n";
                            $mReportar			= true;
                        }
		$msgIncidencias	= ( $mReportar == false ) ? "" : $msgIncidencias;
		$this->mMessages .= $msgIncidencias;
		if ( $put == "html"){
			$msgIncidencias = str_replace("\r\n", "<br />", $msgIncidencias);
		}
		return $msgIncidencias;

	}		//END FUNCTION
	function getAvales_InText($style = "ficha"){ return $this->getAvales_InHTML($style);	}
	/** @deprecated 2014.04.01 */
	function getAvales_InHTML($style = "ficha"){
		$Slistas	= new cSQLListas();
		
		$sql 	= $Slistas->getListadoDeAvales($this->getNumeroDeCredito() );
		$rs 	= getRecordset($sql);
		$tbl	= "";
		$tds	= "";
		$cnt	= 0;
		while($rw = mysql_fetch_array($rs)){
			$tds	.= "
					<tr>
						<th id=\"id-relacion-" . $rw["num"] . "\" colspan='4'>" . $rw["relacion"] . "</th>
					</tr>
					<tr>
						<td>Socio Num.</td>
						<th>" . $rw["numero_socio"] . "</th>
						<td>Consanguinidad</td>
						<th>" . $rw["consanguinidad"] . "</th>
					</tr>
					<tr>
						<td>Nombre</td>
						<td colspan='3'>" . $rw["nombre"] . "</td>
					</tr>
					<tr>
						<td>Telefono(s)</td>
						<td>" . $rw["telefonos"] . "</td>
						<td>C.U.R.P.</td>
						<td>" . $rw["curp"] . "</td>
					</tr>
					<tr>
						<td>Domicilio</td>
						<td colspan='3'>" . $rw["domicilio"] . "</td>
					</tr>
					";
			$cnt++;
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
	function getFichaDeSocio($marco = true, $extraTool = "", $domicilio_extendido = false){
		$Soc = new cSocio($this->mNumeroSocio, true);
		return $Soc->getFicha($domicilio_extendido, $marco, $extraTool);
	}
	function getFicha($mark = true, $extraTool = "", $extendido = false, $ConPersona = false){
		if ($this->mCreditoInicializado == false){ $this->initCredito(); }
		$lafila 			= $this->getDatosDeCredito();
		$xL					= new cLang();
		$solicitud 			= $this->getNumeroDeCredito();
		$convenio 			= $lafila["descripcion_tipoconvenio"];
		$fministracion 		= $lafila["fecha_ministracion"];
		$fvencimiento 		= $lafila["fecha_vencimiento"];
		$periocidad_pago	= $lafila["descripcion_periocidadpagos"];
		$autorizado 		= getFMoney($lafila["monto_autorizado"]);
		$saldo 				= getFMoney($lafila["saldo_actual"]);
		$estatus  			= $lafila["descripcion_estatus"];
		$tool 				= $extraTool;
		$tasa				= getFMoney( ($lafila["tasa_interes"] * 100) );
		$mora				= $lafila["tasa_moratorio"] * 100;
		$pagos				= $lafila["pagos_autorizados"];
		$ultimopago			= $lafila["ultimo_periodo_afectado"];
		$cls				= "";
		$xD					= new cFecha(0);
		$TasaIVA			= $this->getTasaIVA();
		$TasaMora			= $this->getTasaDeMora();
		$trInicial			= ""; 
		$cls				= "credito-estado-" . $this->getEstadoActual();
		$montoParc			= getFMoney($this->mMontoFijoParcialidad);
		$activo				= true;
		if($ConPersona == true){
			$xSoc			= $this->getOPersona();// new cSocio($this->getClaveDePersona(), true);
			$trInicial		= "<tr><th>" . $xL->getT("TR.Persona") . "</th><td>" . $xSoc->getCodigo() . "</td><td colspan='2'>" .  $xSoc->getNombreCompleto() . "</td></tr>";
		}
		if($this->getTipoEnSistema() ==  CREDITO_PRODUCTO_NOMINA){
			if($this->getClaveDeEmpresa() != DEFAULT_EMPRESA){
				$xEmp		= new cEmpresas($this->getClaveDeEmpresa()); $xEmp->init();
				$convenio	= "$convenio - " . $xEmp->getNombreCorto();
			} else {
				$convenio	= "$convenio - ND";
			}
		}
		$tdSaldo		= "<th class='izq'>" . $xL->getT("TR.Saldo Principal") . "</th><td class='mny'>$saldo</td>";
		$tdMonto		= "<th class='izq'>" . $xL->getT("TR.Monto Original") . "</th><td class='mny'>$autorizado</td>";
		$tdFecha		= "<th class='izq'>" . $xL->getT("TR.fecha de desembolso") . "</th><td>" . $xD->getFechaCorta($fministracion) . "</td>";
		$tdPagos		= ($this->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) ? "<td class='mny'>$ultimopago de $pagos POR $ $montoParc</td>" : "<td class='izq'>UNICO</td>"; 
		$tdVencimiento	= "<th class='izq'>" . $xL->getT("TR.Fecha de Vencimiento") . "</th><td>" . $xD->getDayName($fvencimiento) ."; " . $xD->getFechaCorta($fvencimiento) . "</td>";
		
		//Si el Estatus es AUTORIZADO
		if($this->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO){
			$tdSaldo	= "";
			$tdFecha	= "<th class='izq'>" . $xL->getT("TR.Fecha de Autorizacion") . "</th><td>" . $xD->getFechaCorta( $this->getFechaDeAutorizacion()) . "</td>";
			$activo		= false;
			$tdPagos	= "<td class='mny'>$pagos</td>";
			
		} elseif ($this->getEstadoActual() == CREDITO_ESTADO_SOLICITADO){
			$tdSaldo	= "";
			$tdMonto	= "<th class='izq'>" . $xL->getT("TR.Monto Solicitado") . "</th><td class='mny'>" . getFMoney( $this->getMontoSolicitado() ) . "</td>";
			$tdFecha	= "<th class='izq'>" . $xL->getT("TR.Fecha de Solicitud") . "</th><td>" . $xD->getFechaCorta($this->getFechaDeMinistracion() ) . "</td>";
			$activo		= false;
			$tdPagos	= "<td class='mny'>$pagos</td>";
		}
		if( $this->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
			$fvencimiento	= $this->getFechaDeMora();
			$tdVencimiento	= "<th class='izq'>" . $xL->getT("TR.Fecha de Vencimiento") . "</th><td>" . $xD->getDayName($fvencimiento) ."; " . $xD->getFechaCorta($fvencimiento) . "</td>";
		}
		if($extendido == true){
			$xPP				= new cCreditos_destinos();
			$xPP->setData( $xPP->query()->initByID( $this->getClaveDeDestino()) );
			$tdExigible			= ($activo == false) ? "" : "<th class='izq'>" . $xL->getT("TR.Saldo exigible") . "</th><td class='mny'>" . getFMoney($this->getSaldoIntegrado(false, true)) . "</td>";
			$oficial			=  $this->getOOficial()->getNombreCompleto();
			$codigo_de_oficial	=  $this->getClaveDeOficialDeCredito();
						
			$tool				= "<tr>
								<th class='izq'>" . $xL->getT("TR.Tasa Anualizada de Moratorio") . "</th><td class='mny'>" . getFMoney( ($TasaMora*100) ) . "%</td>
								$tdExigible</tr>
					<tr><th class='izq'>" . $xL->getT("TR.Destino del Credito") . "</th><td colspan='3'>" . $xPP->descripcion_destinos()->v()  . ": " . $this->mDescripcionDestino . "</td></tr>
					<tr><th class='izq'>Oficial a Cargo</td><td class='notice' colspan='3'>$oficial</td></tr>" . $tool;
			if($activo == true){
				if($this->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
					$letra		= $this->getPeriodoActual() + 1;
					$IntAct		= $this->getInteresDevengado(fechasys(), $letra, false, true);
				} else {
					$IntAct		= $this->getInteresDevengado();
				}
				
				$IntDevNorm	= $this->getInteresNormalDevengado();
				$IntDevMor	= $this->getInteresMoratorioDev();
				$IntPerNor	= $IntAct[SYS_INTERES_NORMAL];
				$IntPerMor	= $IntAct[SYS_INTERES_MORATORIO];
				$IntPagMor	= $this->getInteresMoratorioPagado();
				$IntPagNor	=  $this->getInteresNormalPagado();
				
				$TIntNorm	= $IntDevNorm + $IntPerNor - $IntPagNor;
				$TIntMor	= $IntDevMor + $IntPerMor  - $IntPagMor;
				$BaseIVA	= ($TIntNorm > 0) ? $TIntNorm : 0;
				$BaseIVA	+= ($TIntMor > 0) ? $TIntMor : 0;
				$IntIVA		= setNoMenorQueCero(($TIntNorm + $TIntMor) * $TasaIVA);
				$cargos		= $this->getCargosDeCobranza();
				$cargosIVA	= setNoMenorQueCero($cargos * TASA_IVA);
				$trCargos	= "";
				if($cargos > 0){
					$trCargos	= "<tr>
						<td /><td />
						<th>" . $xL->getT("TR.Cargos por Cobranza") . "</th><td class='mny'>" . getFMoney($cargos) . "</td>
					</tr><tr>
						<td /><td />
						<th>" . $xL->getT("TR.IVA de Otros cargos") . "</th><td class='mny'>" . getFMoney($cargosIVA) . "</td>
					</tr>	";
				}
				$tool	.= "<tr>
						<th>" . $xL->getT("TR.Interes Normal Generado") . "</th><td class='mny'>" . getFMoney($IntDevNorm) . "</td>
						
						<th>" . $xL->getT("TR.Interes Moratorio Generado") . "</th><td class='mny'>" . getFMoney($IntDevMor) . "</td>
					</tr>
					<tr>
						<th>" . $xL->getT("TR.Interes Normal del Mes") . "</th><td class='mny'>" . getFMoney($IntPerNor ) . "</td>
						<th>" . $xL->getT("TR.Interes Moratorio del mes") . "</th><td class='mny'>" . getFMoney($IntPerMor) . "</td>
					</tr>
					<tr>
						<th>" . $xL->getT("TR.Interes Normal Pagado") . "</th><td class='mny'>(" . getFMoney($IntPagNor) . ")</td>
						<th>" . $xL->getT("TR.Interes Moratorio Pagado") . "</th><td class='mny'>(" . getFMoney($IntPagMor) . ")</td>
					</tr>
					<tr>
						<th>" . $xL->getT("TR.TOTAL INTERES NORMAL") . "</th><th class='mny'>" . getFMoney($TIntNorm) . "</th>
						<th>" . $xL->getT("TR.TOTAL INTERES MORATORIO") . "</th><th class='mny'>" . getFMoney($TIntMor) . "</th>
					</tr>

					<tr>
						<td /><td />
						<th>" . $xL->getT("TR.SALDO DE CAPITAL") . "</th><td class='total, mny'>" . getFMoney($this->getSaldoActual()) . "</td>
					</tr>
					<tr>
						<td /><td />
						<th>" . $xL->getT("TR.TOTAL INTERESES") . "</th><td class='mny'>" . getFMoney($TIntNorm + $TIntMor) . "</td>
					</tr>
					<tr>
						<td /><td />
						<th>" . $xL->getT("TR.IVA POR INTERESES") . "</th><td class='mny'>" . getFMoney($IntIVA) . "</td>
					</tr>					
								$trCargos
					<tr>
						<td /><td />
						<th>" . $xL->getT("TR.TOTAL POR PAGAR") . "</th><td class='total, mny'>" . getFMoney($TIntNorm + $TIntMor + $IntIVA + $this->getSaldoActual() + ($cargos + $cargosIVA)) . "</td>
					</tr>";
			}
			$tool		= "<tfoot>$tool</tfoot>";
		}
		$exoFicha =  "
		<table id='fichadecredito'>
			<tbody>
				$trInicial
				<tr>
					<th class='izq'>" . $xL->getT("TR.Numero de Credito") . "</th><td>$solicitud</td>
					<th class='izq'>" . $xL->getT("TR.Producto") . "</th><td>$convenio</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.Periocidad de Pago") . "</th><td>$periocidad_pago</td>
					<th class='izq'>" . $xL->getT("TR.Numero de Pagos") . "</th>$tdPagos
				<tr>
				<tr>
					
					<th class='izq'>" . $xL->getT("TR.Tasa Anualizada de interes") . "</th><td class='mny'>" . getFMoney( $tasa) . "%</td>
					<th class='izq'>" . $xL->getT("TR.Estado_actual") . "</th><td  class='$cls'>$estatus</td>
				</tr>
				<tr>
					<th class='izq'>" . $xL->getT("TR.Tasa de IVA") . "</th><td class='mny'>" . getFMoney($TasaIVA*100) . "%</td>
					<th class='izq'>CAT</th><td class='mny'>" . $this->getCAT() . "</td>
				</tr>
				<tr>$tdFecha $tdVencimiento</tr>
				<tr>$tdMonto $tdSaldo</tr>
			</tbody>
			$tool
		</table>";
				if ($mark == true){
					$exoFicha =  "<fieldset><legend>|&nbsp;&nbsp;" . $xL->getT("TR.Informacion de Credito") . "&nbsp;&nbsp;|</legend>$exoFicha</fieldset>";
				}
			return $exoFicha;
	}
	function getGarantiaLiquidaPagada($ByGrupo = false){
		$monto	= 0;
		if ( GARANTIA_LIQUIDA_EN_CAPTACION == true ){
			//obtiene el monto de garantia liquida por ahorro

			if ($ByGrupo == true){
				$condicionante_de_garantia_liquida = " (`socios_general`.`grupo_solidario` = " . $this->mGrupoAsociado . ") ";
				$msg	.=  "WARN\tLa Garantia Liquida es valuado por GRUPO \r\n";
			} else {
				$condicionante_de_garantia_liquida = " (`socios_general`.`codigo` = " . $this->mNumeroSocio . ")";
				$msg	.=  "WARN\tLa Garantia Liquida es valuado por SOCIO \r\n";
			}
			$subproducto_de_ahorro_inicial 		= CAPTACION_PRODUCTO_GARANTIALIQ;
			$sqlSUMDepInicial 			= "
								SELECT
									MAX(`captacion_cuentas`.`numero_cuenta`) AS 'cuenta',
									`socios_general`.`grupo_solidario`,
									SUM(`captacion_cuentas`.`saldo_cuenta`) AS 'sumas'
								FROM
									`socios_general` `socios_general`
										INNER JOIN `captacion_cuentas` `captacion_cuentas`
											ON `socios_general`.`codigo` = `captacion_cuentas`.`numero_socio`
								WHERE
									(`captacion_cuentas`.`tipo_subproducto` = $subproducto_de_ahorro_inicial)
									AND
									$condicionante_de_garantia_liquida
								GROUP BY
									`socios_general`.`grupo_solidario`,
									`captacion_cuentas`.`tipo_cuenta`
								ORDER BY
									`captacion_cuentas`.`fecha_afectacion` DESC,
									`captacion_cuentas`.`saldo_cuenta` DESC";
			$DCta					= obten_filas($sqlSUMDepInicial);
			$monto					= $DCta["sumas"];
			$this->mCuentaDeGarantiaLiquida		= $DCta["cuenta"];
			unset($DCta);
		} else {
			$msg	.=  "WARN\tLa Garantia Liquida es valuado como Cuenta aparte y no de CAPTACION\r\n";
			$sqlSUM = "SELECT
						`operaciones_mvtos`.`docto_afectado`,
						SUM(`operaciones_mvtos`.`afectacion_real` *
						`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
							INNER JOIN `eacp_config_bases_de_integracion_miembros`
							`eacp_config_bases_de_integracion_miembros`
							ON `operaciones_mvtos`.`tipo_operacion` =
							`eacp_config_bases_de_integracion_miembros`.`miembro`
					WHERE
						(`operaciones_mvtos`.`docto_afectado` =" . $this->mNumeroCredito .") AND
						(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2500)
					GROUP BY
						`operaciones_mvtos`.`docto_afectado`,
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
					ORDER BY
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` ";
			$d		= obten_filas($sqlSUM);

			$monto	= $d["monto"];
		unset($d);
		}
		$this->mMessages	.= $msg;
		return $monto;
	}
	function getCuentaGarantiaLiquida(){ return $this->mCuentaDeGarantiaLiquida; }
	function getGarantiaLiquida($ForcePagados = false){
		if ($this->mCreditoInicializado == false ){
			$this->initCredito();
		}
		$monto	= 0;
		$monto = $this->mMontoMinistrado * $this->mTasaGarantiaLiquida;
			if ($ForcePagados == true AND $this->mSdoCapitalInsoluto <= TOLERANCIA_SALDOS ){
				$monto = 0;
			}
		return $monto;
	}
	/**
	 * Mensajes de la Libreria
	 * @param string $put Formato de Salida
	 * @return string	Mesajes de Texto
	 */
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getSaldoActual($fecha = false){
		$xF			= new cFecha();
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		if( $this->mCreditoInicializado == false ){ $this->init(); }
		$saldo		= $this->mSdoCapitalInsoluto; 
		
		if($xF->getEsActual($fecha) == true ) {
			//No se inician pagos
			//$this->mMessages			.= "WARN\tNo se Inicializan Pagos por la fecha $fecha \r\n";
		} else {
			$saldo	= $this->getSaldoAFecha($fecha);
		}
		$this->mMessages			.= "WARN\tSaldo de Credito $saldo\r\n";
		return $saldo;
	}
	function getSaldoVencido(){ return $this->mSaldoVencido; }
	function getFechaDeMora(){ return $this->mFechaMora; } 
	function getContratoCorriente(){
		if( $this->mCreditoInicializado == false ){ $this->init(); }
		if($this->mContratoCorriente == 0){
			$this->mContratoCorriente	= CTA_GLOBAL_CORRIENTE;
		}
		return $this->mContratoCorriente;
	}
	/**
	 * Funcion que retorna una descripcion Corta del Credito
	 * @return string	Descripcion Corta del credito
	 */
	function getShortDescription(){
		if( $this->mCreditoInicializado == false ){ $this->init(); }
		$txt	= "";
		$txt	= $this->mNumeroSocio .  "-" . $this->mDescripcionProducto. "|";
		$txt	.= $this->mDescripcionPeriocidad . "-" . $this->mPagosAutorizados;
		if($this->mEstatusActual != CREDITO_ESTADO_VIGENTE ){
			$txt	.= "-" . $this->mDescripcionEstatus;
		} else {
			$txt	.= "";
		}
		
		if($this->mEmpresa != FALLBACK_CLAVE_EMPRESA){
			$txt	.= "-" . $this->mNombrePersonaAsoc;
		}
		return $txt;
	}
	function getDescripcion(){ return $this->getShortDescription(); }
	function setChangeNumeroDeSolicitud($NuevoNumero, $EsReconvenio	= false){
	$socio		= $this->mNumeroSocio;
	$credito	= $this->mNumeroCredito;
	$solicitud	= $credito;
	$NCredito	= $NuevoNumero;
	$msg		= "=========\tCAMBIO DE UN CREDITO\tDE: $credito\ţA:$NuevoNumero\r\n";
	$sql		= array();
		if($EsReconvenio == false){
			$sql[] = "UPDATE creditos_solicitud 		SET numero_solicitud = $NCredito WHERE numero_socio = $socio AND numero_solicitud = $credito";
			$sql[] = "UPDATE captacion_cuentas 		SET numero_solicitud=$NCredito	WHERE numero_socio=$socio AND numero_solicitud=$credito";
			$sql[] = "UPDATE creditos_garantias 		SET solicitud_garantia=$NCredito WHERE socio_garantia=$socio AND solicitud_garantia=$credito";
			$sql[] = "UPDATE socios_relaciones 		SET credito_relacionado=$credito WHERE socio_relacionado=$socio AND credito_relacionado=$credito";
			$sql[] = "UPDATE creditos_flujoefvo 		SET solicitud_flujo = $NCredito WHERE socio_flujo=$socio AND solicitud_flujo = $credito";
			$sql[] = "UPDATE creditos_parametros_negociados 	SET numero_de_credito=$NCredito 	WHERE numero_de_credito=$credito";
			$sql[] = "UPDATE creditos_reconvenio 				SET numero_solicitud=$NCredito 		WHERE numero_solicitud=$credito AND codigo=$socio";
			$sql[] = "UPDATE `seguimiento_compromisos` 		SET credito_comprometido=$NCredito 	WHERE `credito_comprometido`=$credito ";
			$sql[] = "UPDATE `seguimiento_llamadas` 			SET numero_solicitud=$NCredito 		WHERE `numero_solicitud`=$credito";
			$sql[] = "UPDATE `seguimiento_notificaciones` 			SET numero_solicitud=$NCredito 		WHERE `numero_solicitud`=$credito";
			$sql[] = "UPDATE `socios_memo` 					SET numero_solicitud = $NCredito 	WHERE `numero_solicitud`=$credito";
			$sql[] = "UPDATE `usuarios_web_notas` 				SET `documento`=$NCredito 			WHERE `documento`=$credito";
			$sql[] 	= "UPDATE `creditos_otros_datos` 				SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
			$sql[] 	= "UPDATE `creditos_plan_de_pagos` 				SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
		}
		$sql[] = "UPDATE operaciones_recibos 				SET docto_afectado=$NCredito 			WHERE docto_afectado=$credito AND numero_socio=$socio";
		$sql[] = "UPDATE operaciones_mvtos 				SET docto_afectado=$NCredito 			WHERE docto_afectado=$credito AND socio_afectado=$socio";
		$sql[] = "UPDATE contable_polizas_proforma 		SET documento=$NCredito 				WHERE documento=$credito ";
		$sql[] = "UPDATE creditos_rechazados 				SET numero_de_credito=$NCredito 		WHERE numero_de_credito=$credito ";
		$sql[] = "UPDATE `tesoreria_cajas_movimientos` 	SET `documento`=$NCredito 				WHERE `documento`=$credito";
		
		$sql[] 	= "UPDATE `tesoreria_cajas_movimientos` 	SET `documento`=" . $NCredito . " WHERE `documento`=$solicitud";
		$sql[] 	= "UPDATE `tesoreria_cajas_movimientos` 	SET `documento_descontado`=" . $NCredito . " WHERE `documento_descontado`=$solicitud";
		$sql[] 	= "UPDATE `tesoreria_caja_arqueos` 			SET `documento`=" . $NCredito . " WHERE `documento`=$solicitud";
		$sql[] 	= "UPDATE `usuarios_web_notas` 				SET `documento`=" . $NCredito . " WHERE `documento`=$solicitud";
		$sql[] 	= "UPDATE contable_polizas_proforma 		SET documento=" . $NCredito . " WHERE documento=$solicitud";
		$sql[] 	= "UPDATE aml_alerts 						SET documento_relacionado=" . $NCredito . " WHERE documento_relacionado = $solicitud";
		$sql[] 	= "UPDATE aml_risk_register					SET documento_relacionado=" . $NCredito . " WHERE documento_relacionado = $solicitud";
		$sql[] 	= "UPDATE bancos_operaciones				SET numero_de_documento=" . $NCredito . " WHERE numero_de_documento = $solicitud";
		$sql[] 	= "UPDATE `socios_memo` 					SET `numero_solicitud`=" . $NCredito . " WHERE `numero_solicitud`=$solicitud";
		$sql[] 	= "UPDATE `empresas_cobranza` 				SET `clave_de_credito`=" . $NCredito . " WHERE `clave_de_credito`=$solicitud";
		$sql[] 	= "UPDATE operaciones_mvtos 				SET docto_neutralizador= " . $NCredito .  " WHERE docto_neutralizador=$solicitud";
		$sql[] 	= "UPDATE captacion_cuentas 				SET numero_solicitud= " . $NCredito .  " WHERE numero_solicitud=$solicitud";
		$sql[] 	= "UPDATE creditos_reconvenio 				SET credito_origen= " . $NCredito .  " WHERE credito_origen=$solicitud";
				
		foreach ( $sql AS $key => $query  ){
				$x	= my_query( $query );
				$msg	.= $x[SYS_INFO];
		}
		return $msg;
	}
	function getCAT(){
		$xMat			= new cMath();
		$pagos			= $this->getPagosAutorizados();
		$parc			= $this->mMontoFijoParcialidad;
		
		$arrPagos		= array();
		$capital		= $this->getMontoAutorizado();
		$arrDefinidos	= array();
		$arrDefinidos[CREDITO_TIPO_PERIOCIDAD_SEMANAL][60] 		= 101.2; //101.6 segun jose juan
		$arrDefinidos[CREDITO_TIPO_PERIOCIDAD_QUINCENAL][60] 	= 98.6;		//100.5 segun JJ
		$calcular		= true;
		$cat			= 0;
		if (isset($arrDefinidos[$this->getPeriocidadDePago()])){
			if( isset($arrDefinidos[$this->getPeriocidadDePago()][$this->getTasaDeInteres()]) ){
				$calcular	= false;
				$cat		= $arrDefinidos[$this->getPeriocidadDePago()][$this->getTasaDeInteres()];
			}
		}
		if($calcular == true){
			if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				$pago		= ($this->getDiasAutorizados() >= 365) ? ($this->getMontoAutorizado() * $this->getTasaDeInteres() * 365)/EACP_DIAS_INTERES : ($this->getMontoAutorizado() * $this->getTasaDeInteres() * $this->getDiasAutorizados())/ EACP_DIAS_INTERES;
				$arrPagos[] = $pago + $this->getMontoAutorizado();
				//$arrPagos[]	= $this->getMontoAutorizado();
				$periodos	= ($this->getDiasAutorizados() >= 365) ? 1 : floor(365/$this->getDiasAutorizados());
				//$cat		= $xMat->irr($capital, $arrPagos);
			} else {
				if($this->getTipoDePago() == CREDITO_TIPO_PAGO_INTERES_COMERCIAL OR $this->getTipoDePago() == CREDITO_TIPO_PAGO_INTERES_PERIODICO ){
					//corregir fallo en planes de pago
					if($parc > $this->getMontoAutorizado()){
						$parc	= $parc - $this->getMontoAutorizado();
					}
				}
				for($i=1; $i <= $pagos; $i++){
					if($this->getTipoDePago() == CREDITO_TIPO_PAGO_INTERES_COMERCIAL OR $this->getTipoDePago() == CREDITO_TIPO_PAGO_INTERES_PERIODICO ){
						if($i == $pagos){
							$parc	= $parc + $this->getMontoAutorizado();
						}
					}
					$arrPagos[]	= $parc;
				}
				$periodos	= floor((365/$this->getPeriocidadDePago()));
			}
			$cat		= $xMat->cat($capital, $arrPagos, $periodos);
		}
		return $cat;
	}
	/**
	 * Funcion que Elimina un Credito de la Base de Datos
	 */
	function setDelete(){
		//XXX: Verficar $socio, si es mayor a cero 2014/11/02
		$msg	= "=========\tELIMINANDO UN CREDITO\r\n";
		$msg	.= "=========\tFecha:\t\t" . date("Y-m-d H:i:s") . "\r\n";
		$sql	= array();
		//Elimina un Credito
		$solicitud	= $this->mNumeroCredito;
		$socio		= $this->mNumeroSocio;
		$sql[] 	= "DELETE FROM creditos_solicitud 			WHERE numero_solicitud=$solicitud AND numero_socio=$socio";
		$sql[] 	= "DELETE FROM creditos_flujoefvo 			WHERE solicitud_flujo=$solicitud AND socio_flujo=$socio";
		$sql[] 	= "DELETE FROM creditos_garantias 			WHERE solicitud_garantia=$solicitud AND socio_garantia=$socio";
		$sql[] 	= "DELETE FROM socios_relaciones 			WHERE credito_relacionado=$solicitud AND socio_relacionado=$socio";
		$sql[] 	= "DELETE FROM operaciones_recibos 			WHERE docto_afectado=$solicitud AND numero_socio=$socio";
		$sql[] 	= "DELETE FROM operaciones_mvtos 			WHERE docto_afectado=$solicitud AND socio_afectado=$socio";
		
		$sql[] 	= "DELETE FROM creditos_parametros_negociados WHERE numero_de_credito=$solicitud";
		$sql[] 	= "DELETE FROM creditos_rechazados 			WHERE numero_de_credito=$solicitud";
		$sql[] 	= "DELETE FROM `seguimiento_compromisos` 	WHERE `credito_comprometido`=$solicitud";
		$sql[] 	= "DELETE FROM `seguimiento_llamadas` 		WHERE `numero_solicitud`=$solicitud";
		$sql[] 	= "DELETE FROM `seguimiento_notificaciones`	WHERE `numero_solicitud`=$solicitud";
		$sql[] 	= "DELETE FROM `creditos_otros_datos` 		WHERE `clave_de_credito`=$solicitud";
		$sql[] 	= "DELETE FROM `creditos_reconvenio` 		WHERE `numero_solicitud`=$solicitud";
		
		$sql[] 	= "DELETE FROM `creditos_plan_de_pagos` 	WHERE `clave_de_credito`=$solicitud";
		$sql[] 	= "DELETE FROM `creditos_sdpm_historico` 	WHERE `numero_de_credito`=$solicitud";
		
		$sql[] 	= "UPDATE `tesoreria_cajas_movimientos` 	SET `documento`=" . DEFAULT_CREDITO . " WHERE `documento`=$solicitud";
		$sql[] 	= "UPDATE `tesoreria_cajas_movimientos` 	SET `documento_descontado`=" . DEFAULT_CREDITO . " WHERE `documento_descontado`=$solicitud";
		$sql[] 	= "UPDATE `tesoreria_caja_arqueos` 			SET `documento`=" . DEFAULT_CREDITO . " WHERE `documento`=$solicitud";
		$sql[] 	= "UPDATE `usuarios_web_notas` 				SET `documento`=" . DEFAULT_CREDITO . " WHERE `documento`=$solicitud";
		$sql[] 	= "UPDATE contable_polizas_proforma 		SET documento=" . DEFAULT_CREDITO . " WHERE documento=$solicitud";
		$sql[] 	= "UPDATE aml_alerts 						SET documento_relacionado=" . DEFAULT_CREDITO . " WHERE documento_relacionado = $solicitud";
		$sql[] 	= "UPDATE aml_risk_register					SET documento_relacionado=" . DEFAULT_CREDITO . " WHERE documento_relacionado = $solicitud";
		$sql[] 	= "UPDATE bancos_operaciones				SET numero_de_documento=" . DEFAULT_CREDITO . " WHERE numero_de_documento = $solicitud";
		$sql[] 	= "UPDATE `socios_memo` 					SET `numero_solicitud`=" . DEFAULT_CREDITO . " WHERE `numero_solicitud`=$solicitud";
		$sql[] 	= "UPDATE `empresas_cobranza` 				SET `clave_de_credito`=" . DEFAULT_CREDITO . " WHERE `clave_de_credito`=$solicitud";
		$sql[] 	= "UPDATE operaciones_mvtos 				SET docto_neutralizador= " . DEFAULT_CREDITO .  " WHERE docto_neutralizador=$solicitud";
		$sql[] 	= "UPDATE captacion_cuentas 				SET numero_solicitud= " . DEFAULT_CREDITO .  " WHERE numero_solicitud=$solicitud";
		$sql[] 	= "UPDATE creditos_reconvenio 				SET credito_origen= " . DEFAULT_CREDITO .  " WHERE credito_origen=$solicitud";
		
		foreach ($sql as $key => $send){
			$x	= my_query($send);
			$msg	.= $x[SYS_INFO];
		}
		return $msg;
	}
	function getEvolucionDeSaldos(){}
	function setAbonoCapital($monto, $parcialidad = SYS_UNO, $cheque = DEFAULT_CHEQUE,
						$tipo_de_pago = DEFAULT_TIPO_PAGO, $recibo_fiscal = DEFAULT_RECIBO_FISCAL,
						$observaciones = "", $grupo = DEFAULT_GRUPO,
						$fecha = false, $recibo = false){
		
		if ($monto != 0){
			$this->mMessages	.= "WARN\tRECIBO >>>> $recibo\r\n";
			$grupo		= setNoMenorQueCero($grupo);
			if ( $grupo == DEFAULT_GRUPO ){ $grupo = $this->mGrupoAsociado; }
			if(setNoMenorQueCero($this->mNumeroSocio) <= DEFAULT_SOCIO){ $this->init(); }

			if ($fecha == false ){
				$fecha = $this->mFechaOperacion;
			}

			$socio		= $this->mNumeroSocio;

			$CRecibo 	= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO, true, $recibo);
			//Set a Mvto Contable
			$CRecibo->setGenerarPoliza();
			$CRecibo->setGenerarTesoreria();
			$CRecibo->setGenerarBancos();
			if ( setNoMenorQueCero($recibo) <= 0 ){
				if(setNoMenorQueCero($this->mReciboDeOperacion)>0){
					$CRecibo->setNumeroDeRecibo( $this->mReciboDeOperacion, true );
					$recibo		= $this->mReciboDeOperacion;
				}
			} else {
				$this->mReciboDeOperacion	= $recibo;
			}

			//Agregar recibo si no hay
			if ( setNoMenorQueCero($recibo)<= 0 ){
				$recibo = $CRecibo->setNuevoRecibo($socio, $this->mNumeroCredito,
										$this->mFechaOperacion, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO,
										$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );
				//Checar si se agrego el recibo
				if ( setNoMenorQueCero($recibo) > 0 ){
					$this->mMessages	.= "OK\tSe Agrego Exitosamente el Recibo $recibo de la Cuenta " . $this->mNumeroCredito . " \r\n";
					$this->mReciboDeOperacion	= $recibo;
					$this->mSucess		= true;
				} else {
					$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCredito . " \r\n";
					$this->mSucess		= false;
				}
			}
			$this->mReciboDeOperacion	= $recibo;

			if ( setNoMenorQueCero($recibo) > 0 ){
			//Agregar el Movimiento
				$CRecibo->setNuevoMvto($fecha, $monto, OPERACION_CLAVE_PAGO_CAPITAL, $parcialidad, $observaciones, 1, TM_ABONO, $socio, $this->mNumeroCredito);
				$this->mNuevoSaldo	= $this->mSdoCapitalInsoluto - ($monto);
				$this->mMessages	.= $CRecibo->getMessages();
				$this->mSucess		= true;
				//Actualizar la Cuenta
				$arrAct	= array( "saldo_actual" => $this->mNuevoSaldo );
				$this->mMessages	.= "WARN\tSALDO\tSe Actualiza el Saldo de " . $this->mSdoCapitalInsoluto . " a " . $this->mNuevoSaldo . " \r\n";
				$this->setUpdate($arrAct);
			} else {
				$this->mMessages	.= "ERROR\tNo Existe Recibo con el cual trabajar ($recibo) \r\n";
			}
			//afectar el capital
			if($this->mSucess == true){
				$numero_plan	= $this->getNumeroDePlanDePagos();
				if( setNoMenorQueCero($numero_plan) > 0){
					$xPlan			= new cPlanDePagos($numero_plan); //no se necesita inicializar el plan de pagos
					$xPlan->setActualizarParcialidad($parcialidad, "(afectacion_real - $monto)");
				}
			}
			$this->mObjRec		= $CRecibo;
			$this->mMessages	.= $CRecibo->getMessages();
			//Agrega el Devengado de Interes
			
		}
		return $recibo;
	}
	function getORecibo(){ return $this->mObjRec; }
	function setReciboDeOperacion($recibo){ $this->mReciboDeOperacion	= $recibo; }
	/**
	 * Funcion que asiste en la Ministracion del Credito
	 */
	function setMinistrar($recibo_fiscal, $cheque, $monto_cheque = 0, $cuenta_cheques= false,
						  $cheque2 = 0, $cuenta_cheques2 = 0, $observaciones = "", $fecha = false, $recibo = false, $tipo_de_pago = TESORERIA_PAGO_CHEQUE){
		$sucess					= true;
		$monto_cheque			= ($monto_cheque == 0) ? $this->getMontoAutorizado() : $monto_cheque;
		$cuenta_cheques			= ($cuenta_cheques == false) ? DEFAULT_CUENTA_BANCARIA : $cuenta_cheques;
		$cheque					= setNoMenorQueCero($cheque);
		$xSocio					= new cSocio($this->mNumeroSocio);
		$xSocio->init();
		//Corrige la Inicializacion del credito
		if ( !isset($this->mNumeroSocio) OR ($this->mNumeroSocio == 1)){ $this->init(); 	}

		$DIngreso				= $xSocio->getDatosDeIngreso_InArray();
		$msg					= "";
		//$tipo_de_pago			= TESORERIA_PAGO_CHEQUE;
		$recibo					= setNoMenorQueCero($recibo);
		$DConvenio				= $this->getDatosDeProducto();

		$idsolicitud 			= $this->mNumeroCredito;
		$solicitud				= $this->mNumeroCredito;
	/* ------------------------------ obtiene el Monto Autorizado ---------------------------------- */
		$dsol 					= $this->getDatosDeCredito();
		$montoaut 				= $this->getMontoAutorizado();// $dsol["monto_autorizado"];	// Monto Autorizado
		$fvcred 				= $dsol["fecha_vencimiento"];
		$modpagos				= $this->getPeriocidadDePago();
		$tasacred				= $this->getTasaDeInteres();
		$totalop 				= $montoaut;
		$tipoaut 				= $dsol["tipo_autorizacion"];
		$socio 					= $this->mNumeroSocio;
		$intdi 					= $dsol["interes_diario"];
		$diasa 					= $dsol["dias_autorizados"];
		$tipoconvenio 			= $dsol["tipo_convenio"];
		$elgrupo 				= $dsol["grupo_asociado"];
		$grupo					= $elgrupo;
		$idsocio				= $socio;
		$fecha_propuesta		= $dsol["fecha_ministracion"];

		$tasa_ordinaria_de_interes 		= $tasacred;
		$monto_autorizado_a_ministrar	= $dsol["monto_autorizado"];

		$grupo = $this->mGrupoAsociado;



		if ($fecha == false ){ $fecha = $this->mFechaOperacion; }

		$socio		= $this->mNumeroSocio;

		//Valores conservados
		$fechavcto 						= $fvcred;
		$diasaut 						= $diasa;
		$intdiario 						= $intdi;
		/**
		 * Corrige la opcion de que el Cheque es Igual a Cero
		 */
		 if ( $monto_cheque == 0 ){
		 	$monto_cheque = $montoaut;
		 }
		//Corrige el dato de recibo no valido
		if ( $recibo <= 0 AND setNoMenorQueCero($this->mReciboDeOperacion) > 0 ){ 	$recibo	= $this->mReciboDeOperacion; }
	/*	--------------------------------------------------------------------------------------------------------- */
		
		$OConv		= $this->getOProductoDeCredito($tipoconvenio);
		if ( $this->mForceMinistracion == false ){
			// Checa si el credito ya fue Ministrado
			//Modificar
			$montomin 		= $this->getSumMovimiento(OPERACION_CLAVE_MINISTRACION);

			if ( $montomin > 1 ) {
				$msg		.= "ERROR\tEl credito se ha ministrado de forma parcial / Total, o se ha forzado su edicion; el Monto Ministrado es $montomin \r\n";
				$sucess		= false;
			}
		}
		// verificar si tiene aportaciones sociales
		if ( $this->mForceMinistracion == false ){
			$aportaciones	= $xSocio->getAportacionesSociales();
			$cuotas 		= $DIngreso["parte_social"] + $DIngreso["parte_permanente"];
			if ($aportaciones < $cuotas) {
					$msg	.=  "ERROR\tNo ha Pagado sus Cuotas Sociales por $cuotas, ha pagado $aportaciones \r\n";
					$sucess	= false;
			}
		}
	// verificar si pago su fondo de defuncion. // SI ES DIFERENTE A AUTOMATIZADO
		if ( $this->mForceMinistracion == false ){
			$fondo_def_ob 		= $DConvenio["monto_fondo_obligatorio"];
			$fondo_def_pag 		= $xSocio->getFondoDeDefuncion();

			if ($fondo_def_pag < $fondo_def_pag) {
					$msg	.=  "ERROR\tNo ha Pagado sus Fondo de Defuncion por $fondo_def_pag, ha pagado $fondo_def_pag \r\n";
					$sucess	= false;
			}
		}
	// condiciones del credito autorizado por sesion de credito.

		//si el convenio Aplica Gtos Notariales
		$aplica_gtos_not 	=  $DConvenio["aplica_gastos_notariales"];
		if ( ($aplica_gtos_not==1) AND ($this->mForceMinistracion == false) ) {
			$gastos_not_pagados		= $this->getPagoDeGastosNotariales();
			if ($gastos_not_pagados < TOLERANCIA_SALDOS ) {
				$msg				.= "ERROR\tNo ha Pagado sus Gastos Notariales\r\n";
				$sucess				= false;
			}
		}
		// verificar si tiene garantia liquida
		$porc_garantia_liquida 		= $OConv->getTasaDeGarantiaLiquida();
		if( ($porc_garantia_liquida > 0 ) AND ($this->mForceMinistracion == false) ){
			$msg	.=  "WARN\tLa Garantia Liquida es de $porc_garantia_liquida sobre el Monto Autorizado\r\n";
			if ($OConv->getEsProductoDeGrupos() == true ){
				$condicionante_de_garantia_liquida = " (`socios_general`.`grupo_solidario` = $elgrupo) ";
				$msg	.=  "WARN\tLa Garantia Liquida es valuado por GRUPO \r\n";
			} else {
				$condicionante_de_garantia_liquida = " (`socios_general`.`codigo` = $idsocio)";
				$msg	.=  "WARN\tLa Garantia Liquida es valuado por SOCIO \r\n";
			}

			$tgtia 								= $montoaut * $porc_garantia_liquida;
			$subproducto_de_ahorro_inicial 		= CAPTACION_PRODUCTO_GARANTIALIQ;
			$sqlSUMDepInicial 					= "
												SELECT
													`socios_general`.`grupo_solidario`,
													SUM(`captacion_cuentas`.`saldo_cuenta`) AS 'sumas'
												FROM
													`socios_general` `socios_general`
														INNER JOIN `captacion_cuentas` `captacion_cuentas`
														ON `socios_general`.`codigo` = `captacion_cuentas`.`numero_socio`
												WHERE
													(`captacion_cuentas`.`tipo_subproducto` =$subproducto_de_ahorro_inicial)
													AND
													$condicionante_de_garantia_liquida
												GROUP BY
													`socios_general`.`grupo_solidario`,
													`captacion_cuentas`.`tipo_cuenta` ";

			$garliq								= mifila($sqlSUMDepInicial, "sumas");

			if ($garliq < ($tgtia - TOLERANCIA_SALDOS ) ) {
				$msg	.=  "ERROR\tNo ha depositado el su totalidad la Garantia Liquida, ha Depositado $garliq de un total de $tgtia \r\n";
				$msg	.=  "WARN\tRecuerde que el DEPOSITO DE LA GARANTIA LIQUIDA se efectua en el Modulo de Captacion \r\n";
				$sucess	= false;
			}
		} //END: verificar Garantia

		// VERIFICA LA GARANTIA SEGUN TIPO CONVENIO.- La seleccion se hace segun el Numero de Socio y no debe estar entregada
		// NO APLICA EN GRUPOS SOLIDARIOS
		$razon_garantias  			= $DConvenio["razon_garantia"];

		if( ($razon_garantias > 0) AND ($this->mForceMinistracion == false) ){
			$monto_garantizado		= $xSocio->getGarantiasFisicasDepositadas();
			$monto_a_garantizar		= $montoaut * $razon_garantias;
			if($monto_garantizado < $monto_a_garantizar)		{
				$msg	.=  "ERROR\tNo ha garantizado el Total del Credito, se debe garantizar $monto_a_garantizar y solamente se tiene en resguardo $monto_garantizado \r\n";
				$sucess	= false;
			}
		}

		// SI EL CREDITO ES AUTOMATIZADO
		if ($modpagos == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				$diasaut 	= restarfechas($fechavcto, $fecha);	//$dsol["dias_autorizados"];				// Dias Autorizados.
				$msg		.= "WARN\tLos dias Autorizados se cambian a $diasaut\r\n";
				//$fechavcto 	= sumardias($fecha, $diasaut);				// Fecha del Sistema + Dias Autorizados
				$intdiario	= ($montoaut * $tasacred) / EACP_DIAS_INTERES;
		} else {
			//Verifica si existe el Plan de Pagos:
			$sqlck = "SELECT COUNT(tipo_docto) AS 'planes' FROM operaciones_recibos WHERE docto_afectado=$idsolicitud AND tipo_docto=11";
			$plan		= $this->getNumeroDePlanDePagos();
				if ( !isset($plan) OR empty($plan) ) {
				$msg	.=  "ERROR\tNo se ha Generado el PLAN DE PAGOS \r\n";
				$sucess	= false;
				}
			if ( strtotime($fecha) != strtotime($fecha_propuesta) ){
				$msg	.=  "ERROR\tNo se puede ministrar el Credito($fecha), ya que el PLAN DE PAGO de calcula desde la fecha " . getFechaLarga($fecha_propuesta) . ", vuelva a elaborar el PLAN DE PAGOS\r\n";
				$sucess	= false;
			}
		}
		//VERIFICA EL ICA A PAGAR
		$razon_interes_anticipado	= $DConvenio["porcentaje_ica"];
		$iva_incluido				= $DConvenio["iva_incluido"];
		$tasa_iva					= $DConvenio["tasa_iva"];

		//RULE: Modificar segun el Tipo de pago
		if( ($razon_interes_anticipado > 0) AND ($this->mForceMinistracion == false) ){
					// verifica si tiene el Pago de Int Anticipado

					$sumia		= $dsol["sdo_int_ant"];
					$mIntDiario	= ($monto_autorizado_a_ministrar * $tasa_ordinaria_de_interes )/ EACP_DIAS_INTERES;

					$mntia 		= (($mIntDiario * $diasa) * $razon_interes_anticipado) - TOLERANCIA_SALDOS;
					$MontoICA	= $mntia;
							if($iva_incluido == '1'){
								$MontoICA	= $MontoICA * (1 / (1 + $tasa_iva));
							}

						if ($sumia < $MontoICA) {
							$msg	.=  "ERROR\tNo se ha cubierto el Interes Anticipado, se ha pagado $sumia de $MontoICA \r\n";
							$sucess	= false;
						} else {
							//
						}
		}
		if ( $this->mForceMinistracion == true ){ $sucess	= true; $msg	.=  "WARN\tLa Ministracion es FORZADA \r\n"; }
/*------------------------------- AFECTACIONES ------------------------------------------------- */
		if ($sucess ==  true ){
			$monto			= $montoaut;
			$parcialidad	= 1;
			if ( $monto != 0){



					$CRecibo = new cReciboDeOperacion(RECIBOS_TIPO_MINISTRACION, true, $recibo);
					//Set a Mvto Contable
					//$CRecibo->setGenerarPoliza();
					$CRecibo->setGenerarTesoreria();
					$CRecibo->setGenerarBancos(true);


					//Agregar recibo si no hay
					if ( setNoMenorQueCero($recibo) <= 0 ){
						$recibo = $CRecibo->setNuevoRecibo($socio, $solicitud,
												$fecha, $parcialidad, RECIBOS_TIPO_MINISTRACION,
												$observaciones, $cheque, $tipo_de_pago, $recibo_fiscal, $grupo );

						//Checar si se agrego el recibo
						if ( setNoMenorQueCero($recibo) > 0  ){
							$this->mMessages	.= "OK\tSe Agrego Exitosamente el Recibo $recibo de la Cuenta " . $this->mNumeroCredito . " \r\n";
							$this->mReciboDeOperacion	= $recibo;
							$this->mSucess		= true;
						} else {
							$this->mMessages	.= "ERROR\tSe Fallo al Agregar el Recibo $recibo de la Cuenta " . $this->mNumeroCredito . " \r\n";
							$this->mSucess		= false;
						}

					}
					$this->mReciboDeOperacion	= $recibo;

					if ( setNoMenorQueCero($recibo) > 0 ){
					//Agregar el Movimiento
						$CRecibo->setNuevoMvto($fecha, $monto, OPERACION_CLAVE_MINISTRACION, $parcialidad, $observaciones, 1, TM_CARGO, $socio, $solicitud);
						/** @since 2010-11-21 */
						$this->addSDPM(0,0, $fecha, 0, CREDITO_ESTADO_VIGENTE, $fecha, OPERACION_CLAVE_MINISTRACION);
						
						//$this->mMessages	.= $CRecibo->getMessages();
						$this->mSucess		= true;
					} else {
						$this->mMessages	.= "ERROR\tNo Existe Recibo con el cual trabajar ($recibo) \r\n";
					}
					$CRecibo->setFinalizarRecibo(true);
					$this->mMessages	.= $CRecibo->getMessages();
			}

		// Actualiza el estatus del credito a Vigente, la fecha de Operacion y la de vencimiento.

			$arrAct		= array (
								"fecha_ministracion" 	=> $fecha,
								"fecha_ultimo_mvto" 	=> $fecha,
								"estatus_actual"		=> CREDITO_ESTADO_VIGENTE,
								"fecha_vencimiento"		=> $fechavcto,
								"plazo_en_dias"			=> $diasaut,
								"dias_autorizados"		=> $diasaut,
								"interes_diario"		=> $intdiario,
								"saldo_actual"			=> $montoaut
								);
			$this->setUpdate($arrAct);
		// --------------------------------- ENVIA LAS SENTENCIAS SQL----------------------------------------

			//verifica la dif entre cheque 1 y el monto a ministrar
				if($monto_cheque <= 0){
					$monto_cheque = $montoaut;
				}
				$difCheque 		= $montoaut - $monto_cheque;
				$beneficiario	= $xSocio->getNombreCompleto();
				$descuento		= 0;
				//Separar la generación del cheque
				setNuevoCheque($cheque, $cuenta_cheques, $recibo, $beneficiario, $monto_cheque, $fecha, false, $descuento);
				setUltimoCheque($cuenta_cheques, $cheque);

				if($difCheque>0){
					//setPolizaProforma($recibo, 9200, $difCheque, $socio, $idsolicitud, TM_ABONO);
					setNuevoCheque($cheque2, $cuenta_cheques2, $recibo, $beneficiario, $difCheque, $fecha, false, $descuento);
					setUltimoCheque($cuenta_cheques2, $cheque2);
				}

				//Agregar Avisos de Credito por renovacion
				if($this->getTipoDeAutorizacion() == CREDITO_TIPO_AUTORIZACION_RENOVACION){
					//$xSoc		= $this->getOPersona();
					$xSocio->initDatosDeCredito();
					$DCreds		= $xSocio->getDatosDeCreditos();
					$xCred		= new cCreditos_solicitud();
					foreach ($DCreds as $clave => $valores){
						$xCred->setData($valores);
						if($xCred->saldo_actual()->v() >= TOLERANCIA_SALDOS ){
							if($xCred->numero_solicitud()->v() != $this->getNumeroDeCredito() ){
								//agregar aviso
								$xSocio->addMemo(MEMOS_TIPO_NOTA_RENOVACION, "Credito Renovado en la solicitud #" . $this->getNumeroDeCredito(), $xCred->numero_solicitud()->v() , $fecha );
								$msg	.= $xSocio->getMessages();
							}
						}
					}
				}
				//ejecutar alertas por Ministracion de Reglas de Negocios
				//Ministracion de Credito de la persona {clave_de_persona} {nombre_de_persona} 
				//credito numero {clave_de_credito} con monto {monto_de_credito} y tipo de autorizacion {tipo_de_autorizacion}.
				$OTipoAut	= new cCreditos_tipo_de_autorizacion(); $OTipoAut->setData( $OTipoAut->query()->initByID($this->getTipoDeAutorizacion()) );
				$xRegla				= new cReglaDeNegocio();
				$xRegla->setVariables(array(
						"clave_de_persona" => $xSocio->getCodigo(), 
						"nombre_de_persona" => $xSocio->getNombreCompleto(),
						"clave_de_credito" => $this->getNumeroDeCredito(),
						"monto_de_credito" => $this->getMontoAutorizado(),
						"tipo_de_autorizacion" => $OTipoAut->descripcion_tipo_de_autorizacion()->v(OUT_TXT)
				));
				$xRegla->setExecuteActions($xRegla->reglas()->RN_MINISTRAR_CREDITO);
								
			} else {
				$msg			.= "ERROR\tNo se efectua operacion alguna\r\n";
			}

			$this->mMessages	.= $msg;
			return $recibo;
	}
	/**
	 * Obtiene un monto de Gatos Notariales Pagados
	 */
	function getPagoDeGastosNotariales(){
		$socio	= $this->mNumeroSocio;
		// verificar si tiene pago de gastos notariales
		$sqlgtos = "SELECT
					MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha_de_pago`,
					SUM(`operaciones_mvtos`.`afectacion_real`)  AS `monto_pagado`
					FROM operaciones_mvtos
					WHERE socio_afectado=$socio
					AND tipo_operacion=1001
					AND docto_neutralizador='1'";
		$monto	= mifila($sqlgtos, "monto_pagado");
		return $monto;
	}
	function setForceMinistracion($force = true){ $this->mForceMinistracion = $force; }

	/**
	 * funcion que agrega un aval al crédito
	 *
	 * */
	function addAval($AvalNumeroSocio, $MontoAvalado, $TipoDeAval, $Consanguinidad = 99, $Dependiente = 2, $Observaciones = ""){
		if($this->mCreditoInicializado == false){ $this->init(); }
		$xSoc				= new cSocio($this->getClaveDePersona()); 
		$xSoc->init();
		$porcentaje			= (setNoMenorQueCero($MontoAvalado) > 0 AND setNoMenorQueCero($this->mMontoAutorizado) > 0 ) ? round(($MontoAvalado / $this->mMontoAutorizado), 2) : $this->mMontoAutorizado; 
		$fecha				= fechasys();
		$rs					= $xSoc->addRelacion($AvalNumeroSocio, $TipoDeAval, $Consanguinidad, $Dependiente, $Observaciones, $MontoAvalado, $porcentaje, $fecha, $this->mNumeroCredito);
		$this->mMessages	.= ($rs == true) ? "OK\tAval Codigo $AvalNumeroSocio agregado al credito " . $this->mNumeroCredito . "\r\n" : "ERROR\tError en alta de Aval\r\n";
		if(MODO_DEBUG == true){ $this->mMessages .= $xSoc->getMessages(); }
		return $rs;
	}
	/**
	 * Probar : 2012-04-02 || terminar 2012-02-15
	 * @param integer $TipoDeConvenio
	 * @param integer $NumeroDeSocio
	 * @param integer $ContratoCorriente
	 * @param float $MontoSolicitado
	 * @param integer $PeriocidadDePago
	 * @param integer $NumeroDePagos
	 * @param integer $PlazoEnDias
	 * @param integer $DestinoDeCredito
	 * @param integer $NumeroDeCredito
	 * @param integer $GrupoAsociado
	 * @param string $DescripcionDelDestino
	 * @param string $Observaciones
	 * @param integer $OficialDeCredito
	 * @param mixed $FechaDeSolicitud
	 * @param integer $TipoDePago
	 * @param integer $TipoDeCalculo
	 * @param float $TasaDeInteres
	 * @return boolean		true/false of query result
	 */
	function add($TipoDeConvenio, $NumeroDeSocio, $ContratoCorriente, $MontoSolicitado, $PeriocidadDePago = 0, 
				$NumeroDePagos = 0, $PlazoEnDias = 0, $DestinoDeCredito = CREDITO_DEFAULT_DESTINO, $NumeroDeCredito = false,
				$GrupoAsociado = DEFAULT_GRUPO, $DescripcionDelDestino = "", $Observaciones = "", $OficialDeCredito = false, $FechaDeSolicitud = false, 
				$TipoDePago =  CREDITO_TIPO_PAGO_UNICO , $TipoDeCalculo = INTERES_POR_SALDO_INSOLUTO, $TasaDeInteres = false, $FechaDeMinistracion = false,
			$persona_asociada = false, $TipoDeAutorizacion = false ){
		
		$xF							= new cFecha();
		$xT							= new cTipos();
		$xSoc						= new cSocio($NumeroDeSocio);
		$xSoc->init();
		$OficialDeCredito			= ( $OficialDeCredito == false ) ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : $OficialDeCredito;
		$PlazoEnDias				= ( $PlazoEnDias == 0) ? ($PeriocidadDePago * $NumeroDePagos): $PlazoEnDias;
		$NumeroDePagos				= ( $NumeroDePagos == 0) ? ( $PlazoEnDias / $PeriocidadDePago ) : $NumeroDePagos;
		$ContratoCorriente			= ( $xT->cInt($ContratoCorriente) <0 ) ? CTA_GLOBAL_CORRIENTE : $ContratoCorriente;
		$persona_asociada			= ($persona_asociada == false) ? $xSoc->getClaveDeEmpresa() : $persona_asociada;
		$GrupoAsociado				= setNoMenorQueCero($GrupoAsociado);
		$GrupoAsociado				= ($GrupoAsociado <= 0) ? DEFAULT_GRUPO : $GrupoAsociado;
		if( $NumeroDeCredito == false ) {
			$NumeroDeCredito		= $xSoc->getIDNuevoDocto(iDE_CREDITO);
		}
		$DConv						= $this->getDatosDeProducto($TipoDeConvenio);
		$DOConv						= $this->getOProductoDeCredito($TipoDeConvenio);
		$TasaDeInteres				= ($TasaDeInteres === false) ? $DConv["interes_normal"] : $TasaDeInteres;
		$TasaMoratorio				= $DConv["interes_moratorio"];
		$TasaDeAhorro				= $DConv["tasa_ahorro"];
		$NivelDeRiesgo				= $DConv["nivel_riesgo"];
		$TipoDeCredito				= $DConv["tipo_de_credito"];
		$TipoDeAutorizacion			= ($TipoDeAutorizacion == false) ? $DConv["tipo_autorizacion"] : $TipoDeAutorizacion;
			
		$PeriodoDeCredito			= EACP_PER_SOLICITUDES;
		$TipoDePago				= ( $NumeroDePagos > 1 ) ? CREDITO_TIPO_PAGO_PERIODICO : CREDITO_TIPO_PAGO_UNICO ;

		$FechaDeSolicitud			= ($FechaDeSolicitud == false) ? $xF->get() : $FechaDeSolicitud;
		$FechaDeMinistracion		= ($FechaDeMinistracion == false ) ? $FechaDeSolicitud : $FechaDeMinistracion;
		$FechaDeUltOperacion		= $FechaDeSolicitud;
		$FechaDeRevision			= $FechaDeSolicitud;
		$FechaConciliada			= $FechaDeSolicitud;
				
		
		$xP							= new cPeriodoDeCredito($PeriodoDeCredito);
		$FechaDeAutorizacion		= $FechaDeMinistracion;		//$xP->getFechaDeReunion();
		
		$CausaDeMora				= 99;
		$EstatusActual				= 99;
					
		$FechaDeVencimiento		= $xF->setSumarDias($PlazoEnDias, $FechaDeMinistracion) ;
		$FechaDeMora				= $xF->getFechaMaximaOperativa();
		$FechaDeVencimientoDinamico	= $xF->getFechaMaximaOperativa();
		$FechaDeCastigo			= $xF->getFechaMaximaOperativa();
					
		$PagosAutorizados			= 0;
		$PlazoEnDiasAutorizado	= 0;
		
		$MontoAutorizado			= 0;
		$SaldoActual				= 0;
		$SaldoVencido				= 0;
		$SaldoConciliado			= 0;
		$MontoParcialidad			= 0;
		
		$InteresNormalDevengado		= 0;
		$InteresNormalPagado		= 0;
		$InteresMoratorioPagado		= 0;
		$InteresMoratorioDevengado	= 0;
		$InteresDiario				= 0;
		$InteresAnticipado			= 0;
		
		$OficialDeSeguimiento		= $OficialDeCredito;
		$iduser						= getUsuarioActual();
		
		$PeriodoDeNotificacion	= 0;
		$PeriodoAfectado			= 0;
		$NotasDeAuditoria			= "";
		$CadenaH					= "";
		$DoctoDeAutorizacion 		= "";
		
		$sucursal					= getSucursal(); 
		$eacp						= EACP_CLAVE;
		
		
		$sqlNC	= "INSERT INTO creditos_solicitud(
					numero_socio, numero_solicitud, grupo_asociado, contrato_corriente_relacionado,
					tipo_convenio, tipo_de_pago, tipo_de_calculo_de_interes, periocidad_de_pago, tipo_credito, nivel_riesgo,
					estatus_actual, tipo_autorizacion, causa_de_mora, periodo_solicitudes, destino_credito,
					fecha_solicitud, fecha_autorizacion, fecha_ministracion, fecha_ultimo_mvto, fecha_revision, fecha_conciliada, 
					fecha_mora, fecha_vencimiento, fecha_vencimiento_dinamico, fecha_castigo,
					plazo_en_dias, dias_autorizados, numero_pagos, pagos_autorizados,
					monto_solicitado, monto_autorizado, saldo_actual, saldo_vencido, saldo_conciliado, monto_parcialidad, 
					interes_normal_devengado,  interes_normal_pagado, interes_moratorio_devengado, interes_moratorio_pagado, interes_diario,  sdo_int_ant,
					tasa_interes, tasa_moratorio, tasa_ahorro,
					ultimo_periodo_afectado,  periodo_notificacion,
					idusuario, oficial_seguimiento, oficial_credito,
					docto_autorizacion, observacion_solicitud, cadena_heredada, notas_auditoria, descripcion_aplicacion,
					sucursal, eacp, persona_asociada)
					VALUES (
					$NumeroDeSocio, $NumeroDeCredito, $GrupoAsociado, $ContratoCorriente,
					$TipoDeConvenio, $TipoDePago, $TipoDeCalculo, $PeriocidadDePago, $TipoDeCredito, $NivelDeRiesgo,
					$EstatusActual, $TipoDeAutorizacion, $CausaDeMora, $PeriodoDeCredito, $DestinoDeCredito,
					'$FechaDeSolicitud', '$FechaDeAutorizacion', '$FechaDeMinistracion', '$FechaDeUltOperacion', '$FechaDeRevision', '$FechaConciliada',
					'$FechaDeMora', '$FechaDeVencimiento', '$FechaDeVencimientoDinamico', '$FechaDeCastigo',
					$PlazoEnDias, $PlazoEnDiasAutorizado, $NumeroDePagos, $PagosAutorizados, 
					$MontoSolicitado, $MontoAutorizado, $SaldoActual, $SaldoVencido, $SaldoConciliado, $MontoParcialidad,
					$InteresNormalDevengado, $InteresNormalPagado, $InteresMoratorioDevengado, $InteresMoratorioPagado, $InteresDiario, $InteresAnticipado,
					$TasaDeInteres, $TasaMoratorio, $TasaDeAhorro,
					$PeriodoAfectado, $PeriodoDeNotificacion,
					$iduser, $OficialDeSeguimiento, $OficialDeCredito,
					'$DoctoDeAutorizacion', '$Observaciones', '$CadenaH', '$NotasDeAuditoria', '$DescripcionDelDestino',
					'$sucursal', '$eacp', $persona_asociada) ";
		$x		= my_query($sqlNC);
		if($x[SYS_ESTADO] == false ){
			$this->mMessages		.= "$NumeroDeSocio\t$NumeroDeCredito\tERROR\tError al agregar el credito $NumeroDeCredito\r\n";
			
			//$this->mMessages		.= $x[SYS_MSG];
		} else {
			$this->set($NumeroDeCredito, true);
			//Actualizar Planeacion en Grupos
			if($DOConv->getEsProductoDeGrupos() == true) {
				$xGrupo					= new cGrupo( $xSoc->getClaveDeGrupo()); $xGrupo->init();
				$xGrupo->setActualizarPlaneacion($FechaDeSolicitud, $NumeroDeSocio, $NumeroDeCredito);
				$this->mMessages		.= $xGrupo->getMessages();
			}			
		}
		return $x[SYS_ESTADO];
	}
	/**
	 * Agrega un Saldo Diario Promedio por cada evento de credito
	 * @param float $interes
	 * @param float $moratorio
	 * @param date $FechaAnterior
	 * @param float $saldo
	 * @param integer $estatus
	 * @param date $fecha
	 * @param float $operacion
	 */
	function addSDPM($interes =0, $moratorio = 0, $FechaAnterior = false, 
			$saldo = false, $estatus = false, $fecha = false, $operacion = false,
			$saldo_calculado = false, $periodo = false){
		$solicitud			= $this->mNumeroCredito;
		//agregar valoracion de inicio de credito
		if ( $this->mCreditoInicializado == false ){ $this->init(); }
		$socio				= $this->mNumeroSocio;
		$periodo			= ($periodo == false ) ? $this->getPeriodoActual() : $periodo;
		$saldo				= ( $saldo === false ) ? $this->mSdoCapitalInsoluto : $saldo;
		$estatus			= ( $estatus == false ) ? $this->mEstatusActual : $estatus;
		$operacion			= ( $operacion == false ) ? 0 : $operacion;
		$msg				= "";

		$xD					= new cFecha(0);
		$fecha				= ( $fecha == false ) ? $xD->get() : $fecha;
		$dias_transcurridos	= $xD->setRestarFechas($fecha, $FechaAnterior);
		$saldo_calculado	=  ($saldo_calculado === false) ? $saldo * $dias_transcurridos : $saldo_calculado;
		
		$sqlSD	= "INSERT INTO creditos_sdpm_historico
		            (numero_de_socio, numero_de_credito,
		            fecha_actual, fecha_anterior, dias_transcurridos,
					monto_calculado, saldo, estatus, interes_normal, interes_moratorio, tipo_de_operacion, periodo)
				    VALUES ($socio, $solicitud, '$fecha', '$FechaAnterior', $dias_transcurridos,
					$saldo_calculado, $saldo, $estatus, $interes, $moratorio, $operacion, $periodo) ";
		$x		= my_query($sqlSD);
		if ( $x["stat"] == true ){
			$msg	.= "$socio\t$solicitud\tSDPM\tSUCESS\tSe agrego SDPM $saldo_calculado, dias $dias_transcurridos, Estatus $estatus, Interes $interes, Moratorio $moratorio\r\n";
		} else {
			$msg	.= "$socio\t$solicitud\tSDPM\tERROR\tSe Fallo al Agregar SDPM $saldo_calculado, dias $dias_transcurridos, Estatus $estatus, Interes $interes, Moratorio $moratorio\r\n";
		}
		$this->mMessages	.= $msg;
		return $msg;
	}
	/**
	 * Obtiene la Suma de Movimientos de un credito
	 * @param integer $idMvto
	 */
	function getSumMovimiento($idMvto){
		$docto		= $this->mNumeroCredito;
		$sqlsm		= "SELECT SUM(afectacion_real) AS 'neto' FROM operaciones_mvtos WHERE docto_afectado=$docto AND tipo_operacion=$idMvto";
		$sumado		= mifila($sqlsm, "neto");
		return	$sumado;	
	}
	/**
	 * Obtiene la proxima parcialidad de pago del socio
	 */
	function getProximaParcialidad(){
		if ($this->mCreditoInicializado == false){		$this->init();		}
		$sqlPP								= "SELECT * FROM `creditos_proximas_parcialidades` WHERE credito=" . $this->mNumeroCredito . " LIMIT 0,1";
		$DPP								= obten_filas($sqlPP);
		if ( !isset($DPP["parcialidad"]) ){
			$this->mMessages				.= "ERROR\tLa Parcialidad no fue encontrada\r\n";
			
		}
		$this->mNumeroProximaParcialidad	= ( isset($DPP["parcialidad"]) ) ? $DPP["parcialidad"] : 1;
		$this->mFechaProximaParcilidad		= ( isset($DPP["fecha_de_pago"]) ) ? $DPP["fecha_de_pago"] : $this->mFechaVencimiento;
		$this->mParcialidadesConSaldo		= ( isset($DPP["parcialidades_con_saldo"]) ) ? $DPP["parcialidades_con_saldo"] : $this->mPagosAutorizados;
		$mPlanCapitalPendiente				= ( isset($DPP["capital_pendiente"]) ) ? $DPP["capital_pendiente"] : $this->mSdoCapitalInsoluto;
		
		unset($DPP);
		
		return $this->mNumeroProximaParcialidad;
		
	}
	/**
	 * Autoriza los creditos
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
	function setAutorizado($Monto, $Pagos, $Periocidad, $TipoDeAutorizacion,$FechaDeAutorizacion, $DocumentoDeAutorizacion,	$FormaDePago = false, 
				$FechaDeMinistracionPropuesta = false, $NivelDeRiesgo = 2, $PlazoEnDias = false, $FechaDeVencimiento = false,
				$EstatusImpuesto = false, $SaldoImpuesto = 0, $InteresImpuesto = 0, $FechaOperacionImpuesta = false,
				$TasaDeInteres	= false	){
		$this->init();
		$xF				= new cFecha(0);
		
		$FechaDeMinistracionPropuesta	= ( $FechaDeMinistracionPropuesta == false ) ? $xF->get(): $FechaDeMinistracionPropuesta;
		
		switch( $Periocidad ){
			case CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO:
				$InteresImpuesto		= ($InteresImpuesto == 0) ? ( ($this->mMontoSolicitado * $this->mTasaInteres) / EACP_DIAS_INTERES ) : $InteresImpuesto;
				$FormaDePago			= CREDITO_TIPO_PAGO_UNICO;
				$FechaDeVencimiento		= ($FechaDeVencimiento == false) ? $this->mFechaVencimiento : $FechaDeVencimiento;
				$PlazoEnDias			= ($PlazoEnDias == false ) ? $xF->setRestarFechas($FechaDeVencimiento, $FechaDeMinistracionPropuesta) : $PlazoEnDias;
				break;
			default:
				$InteresImpuesto		= ($InteresImpuesto == 0) ? ( ($this->mMontoSolicitado * $this->mTasaInteres) / EACP_DIAS_INTERES ) : $InteresImpuesto;
				$PlazoEnDias			= ($PlazoEnDias == false ) ? ($Periocidad *  $Pagos) : $PlazoEnDias;
				$FechaDeVencimiento		= ($FechaDeVencimiento == false) ? $xF->setSumarDias($PlazoEnDias, $FechaDeMinistracionPropuesta) : $FechaDeVencimiento;
				$FormaDePago			= ($FormaDePago == false ) ? CREDITO_TIPO_PAGO_PERIODICO : $FormaDePago;
				break;
		}
		switch($TipoDeAutorizacion){
			case CREDITO_TIPO_AUTORIZACION_AUTOMATICA:
				
				break;
			case CREDITO_TIPO_AUTORIZACION_RENOVACION:
				break;
			case CREDITO_TIPO_AUTORIZACION_NORMAL:
				break;
		}
		$EstatusImpuesto			= ($EstatusImpuesto == false) ? CREDITO_ESTADO_AUTORIZADO : $EstatusImpuesto;
		//$FechaOperacionImpuesta		= ($FechaOperacionImpuesta == false) ? fechasys();
		//
		$TasaDeInteres		= ($TasaDeInteres == false) ? $this->mTasaInteres : $TasaDeInteres; 		//
		$arrUpdate	= array(
						"monto_autorizado"		=> $Monto,
						"pagos_autorizados"		=> $Pagos,
						"tipo_de_pago"			=> $FormaDePago,
						"periocidad_de_pago"    => $Periocidad,
						"tipo_autorizacion"		=> $TipoDeAutorizacion,
		
						"fecha_autorizacion"    => $FechaDeAutorizacion,
						"docto_autorizacion"    => $DocumentoDeAutorizacion,
						"fecha_ministracion"    => $FechaDeMinistracionPropuesta,
						"nivel_riesgo"			=> $NivelDeRiesgo,
		
						"estatus_actual"		=> $EstatusImpuesto,
						"saldo_actual"			=> $SaldoImpuesto,
						"interes_diario"		=> $InteresImpuesto,
						"fecha_ultimo_mvto"		=> $FechaOperacionImpuesta,
						
						"plazo_en_dias"			=> $PlazoEnDias,
						"dias_autorizados"		=> $PlazoEnDias,
						"fecha_vencimiento"		=> $FechaDeVencimiento,
						"tasa_interes"			=> $TasaDeInteres
		);

		$this->setUpdate($arrUpdate);

	}
	function setCancelado($razones = "", $fecha = false){
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		$xRuls		= new cReglaDeNegocio(); $xRuls->setExecuteActions( $xRuls->reglas()->RN_CANCELAR_CREDITO );
		$this->setUpdate(array(
				"estatus_actual" => CREDITO_ESTADO_CASTIGADO,
				"monto_autorizado" => 0,
				"docto_autorizacion" => $razones,
				"fecha_ultimo_mvto" => $fecha,
				"fecha_revision" => $fecha, 
				"fecha_castigo" => $fecha
		));
		$this->mMessages	.= "WARN\tCredito Cancelado\r\n";
	}
	function setCastigado($razones = "", $fecha = false){
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		$xRuls		= new cReglaDeNegocio(); $xRuls->setExecuteActions( $xRuls->reglas()->RN_CASTIGAR_CREDITO );
		/*`creditos_solicitud`.`estatus_actual`,
		`creditos_solicitud`.`nivel_riesgo`,
		`creditos_solicitud`.`fecha_castigo`,
		`creditos_solicitud`.``*/		
		$this->setUpdate(array(
				"estatus_actual" => CREDITO_ESTADO_CASTIGADO,
				"fecha_revision" => $fecha,
				"fecha_castigo" => $fecha,
				"nivel_riesgo" => SYS_RIESGO_ALTO,
				"notas_auditoria" => $razones
		));
		$this->mMessages	.= "WARN\tCredito Castigado\r\n";
	}		
	function getTasaIVA(){
		$xMod	= new cCreditos_destinos();
		$xMod->setData( $xMod->query()->initByID($this->mClaveDeDestino) );
		$this->mTasaIVA		= $xMod->tasa_de_iva()->v();
		return $this->mTasaIVA;
	}
	/**
	 * Establece el Numero de Credito y opcionalmente Inicializa el Credito
	 * @param integer $NumeroDeCredito
	 * @param boolean $iniciar
	 */
	function set($NumeroDeCredito, $iniciar = false){
		$this->mNumeroCredito	= $NumeroDeCredito;
		if($iniciar == true){ $this->init();	}
	}
	/**
	 * Obtiene la Base de Calculo para Intereses Moratorios
	 * @param mixed $FechaInicial
	 * @param mixed $FechaFinal
	 */
	function getBaseCalculoMoratorio($FechaInicial, $FechaFinal){	}
	function getClaveDeConvenio(){ return $this->mTipoDeConvenio; }
	function getFechaDeMinistracion(){ return $this->mFechaMinistracion; }
	function getPeriocidadDePago(){ return $this->mPeriocidadDePago; }
	function getTipoDePago(){ return $this->mTipoDePago; }
	function getClaveDeProducto(){ return $this->mTipoDeConvenio; }
	function getClaveDePersona(){ return $this->mNumeroSocio; }
	function getClaveDeEmpresa(){ return $this->mEmpresa; }
	function getClaveDeGrupo(){ return $this->mGrupoAsociado; }
	function getNumeroDeCredito(){ return $this->mNumeroCredito; }
	function getMontoAutorizado(){ return $this->mMontoAutorizado; }
	function getMontoSolicitado(){ return $this->mMontoSolicitado; }
	function getMontoDeParcialidad(){ return $this->mMontoFijoParcialidad; }
	function getFechaDeSolicitud(){ return $this->mFechaDeSolictud; }
	function getFechaDeAutorizacion(){ return $this->mFechaDeAutorizacion; }
	function getFechaDeVencimiento(){ return  $this->mFechaVencimiento; }
	function getFechaPrimeraParc(){ return $this->mFechaPrimeraParc; } //Dato del Plan de Pago
	function getFechaUltimaParc(){ return $this->mFechaUltimaParc; } //Dato del Plan de Pago
	function getFechaUltimoMvtoCapital(){ return $this->mFechaUltimoMvtoCapital; }
	function getTasaDeAhorro(){ return $this->mTasaAhorro; }
	function getDiasAutorizados(){ return $this->mDiasAutorizados; }
	function getTasaDeInteres(){ return $this->mTasaInteres; }
	function getPagosAutorizados(){ return $this->mPagosAutorizados; }
	function getFormaDePago(){ return $this->mTipoDePago; }
	
	function getEstadoActual(){ return $this->mEstatusActual; }
	function getInteresNormalPagado(){ return setNoMenorQueCero($this->mInteresNormalPagado); }
	function getInteresNormalDevengado(){ return setNoMenorQueCero($this->mInteresNormalDevengado); }
	function getInteresMoratorioPagado(){ return setNoMenorQueCero($this->mInteresMoratorioPag); }
	function getInteresMoratorioDev(){ return setNoMenorQueCero($this->mInteresMoratorioDev); }
	function getInteresDiariogenerado(){ return $this->mInteresDiario; } 
	function getClaveDeDestino(){ return $this->mTipoDeDestino; }
	function getPeriodoActual(){ return $this->mParcialidadActual; }
	function getTasaDeMora(){ return $this->mTasaMoratorio; }
	function getTipoDeCalculoDeInteres(){ return $this->mTipoDeCalculoDeInteres; }
	function getTipoDeAutorizacion(){ return $this->mTipoDeAutorizacion; }
	function getPathDelContrato(){ return $this->mContrato_URL . $this->getNumeroDeCredito(); }
	function setForceVista($vista = false){ $this->mForceVista = $vista; }	 
	function setRazonRechazo($razones = "", $notas = "", $fecha = false){
		$fecha	= ($fecha == false) ? fechasys() : $fecha;
		$v	= new cCreditos_rechazados();
		$lid	= $v->query()->getLastID();
		$idc	= $this->mNumeroCredito;

		$v->idcreditos_rechazados($lid);
		$v->numero_de_credito($idc);
		$v->fecha_de_rechazo($fecha);
		$v->razones($razones);
		$v->notas($notas);
		
		$v->query()->insert()->save();
		//error_log($v->query()->insert()->get() . "$fecha, $razones" );
	}
	function setCambioProducto($producto, $tasa = false, $mora = false){
		$tasa		= setNoMenorQueCero($tasa);
		$tasa		= ($tasa >= 100 ) ? ($tasa /100) : $tasa; 
		$tasa		= ($tasa <= 0) ? $this->getTasaDeInteres() : $tasa;
		$mora		= setNoMenorQueCero($mora);
		$mora		= ($mora >= 100) ? ($mora/100) : $mora;
		$mora		= ($mora <= 0) ? $this->getTasaDeMora() : $mora;
		
		$msg		= "";
		
		$this->setUpdate(array(
			$this->obj()->tasa_interes()->get() => $tasa,
			$this->obj()->tasa_moratorio()->get() => $mora,
			$this->obj()->tipo_convenio()->get() => $producto
		));
		if( $tasa != $this->getTasaDeInteres() OR $mora != $this->getTasaDeMora() ){
			$this->init();
			//eliminar plan de pagos
			$idPlan		= $this->getNumeroDePlanDePagos();
			if( $idPlan > 0){
				$xPlan		= new cReciboDeOperacion(false, true, $idPlan); $xPlan->init();
				$xPlan->setRevertir(true);
			}
			$msg	.= $this->setReestructurarIntereses();
			$this->init();
		}
		return $msg;
	}
	function setPeriodoActual($periodo){
		$this->setUpdate(array("ultimo_periodo_afectado" => $periodo));
		$this->mMessages	.= "WARN\tCredito Actualizado a la Letra $periodo\r\n";
	}
	function setCambiarPeriocidad($NuevaPeriocidad, $nuevosPagos	= false, $formaPago = false, $fecha = false){
		$pagos				= $this->getPagosAutorizados();
		$periocidad			= $this->getPeriocidadDePago();
		$dias				= $this->getDiasAutorizados();
		$formaPago			= ($formaPago == false) ? $this->getFormaDePago() : $formaPago;
		$pagoActual			= $this->getPeriodoActual();
		
		$cT					= new cTipos();
		$xF					= new cFecha();
		$msg				= "";
		$success			= true;
		$nuevosDias			= ($nuevosPagos == false ) ? $dias : $NuevaPeriocidad * $nuevosPagos;
		$nuevosPagos		= ($nuevosPagos == false) ? $cT->cInt(( $dias/$NuevaPeriocidad)) : $nuevosPagos;
		
		//cuadrar periodo actual
		if( $NuevaPeriocidad > $periocidad ){
			//15 > 7 ? 15/7 :: 7 24/1
			$factoria		= ($NuevaPeriocidad/$periocidad);
			$pagoActual		= $cT->cInt($pagoActual/$factoria);
		}
		if( $NuevaPeriocidad < $periocidad ){
			//30 > 15 :: 30/5 = 2
			$factoria		= ($periocidad/$NuevaPeriocidad);
			$pagoActual		= $cT->cInt($pagoActual * $factoria);
		}
		$xConv				= new cProductoDeCredito($this->getClaveDeProducto());
		$DConv				= $xConv->obj();
	
		if( $NuevaPeriocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
			$nuevosPagos	= SYS_UNO;
			$formaPago		= CREDITO_TIPO_PAGO_UNICO;
			if($fecha != false){
				$nuevosDias	= $xF->setRestarFechas($fecha, $this->getFechaDeMinistracion());
			} else {
				$nuevosDias	= $this->getDiasAutorizados();
			}
		}
		if( $nuevosDias > $DConv->dias_maximo()->v() ){
			//sucees false
			$success		= false;
			$msg			.= "ERROR\Periocidad Limitada a Maximo de dias\r\n";
		}
		$fechaVencmiento	= $xF->setSumarDias($nuevosDias, $this->getFechaDeMinistracion());
		if($success == true){
			if($this->isAFinalDePlazo() == false){
			//eliminar plan de pagos
				$idPlan		= $this->getNumeroDePlanDePagos();
				if( $idPlan > 0){
					$xPlan		= new cReciboDeOperacion(false, true, $idPlan); $xPlan->init();
					$xPlan->setRevertir(true);
					$msg	.= $xPlan->getMessages();
				}
			}
			//actualizar credito
			$this->setUpdate(array(
				$this->obj()->dias_autorizados()->get() => $nuevosDias,
				$this->obj()->plazo_en_dias()->get() => $nuevosDias,
				$this->obj()->periocidad_de_pago()->get() => $NuevaPeriocidad,
				$this->obj()->pagos_autorizados()->get() => $nuevosPagos,
				$this->obj()->fecha_vencimiento()->get() => $fechaVencmiento,
				$this->obj()->fecha_vencimiento_dinamico()->get() => $fechaVencmiento,
				$this->obj()->tipo_de_pago()->get() => $formaPago,
				$this->obj()->ultimo_periodo_afectado()->get() => $pagoActual
			));
			$this->init();
			$msg	.= $this->setReestructurarIntereses();
		}
		$this->mMessages		.= $msg;
		return $msg;
	}
	function setCambiarMontoAutorizado($monto, $force = false){
		$msg		= "";
		$success	= true;
		if( $this->getEstadoActual() !== CREDITO_ESTADO_AUTORIZADO ){
			$msg		.= "ERROR\tNo se puede cambiare un credito con Estado diferente, Use Cambiar Monto Ministrado\r\n";
			$success	= false;
		}
		$success		= ($force == true) ? true : $success; 
		if($success == true ) {
			$this->setUpdate(array(
				"monto_autorizado" => $monto
			));
			$msg	.= "SUCCESS\tCambio Aplicado\r\n";
		}
		return $msg;
	}
	function setCambiarMontoMinistrado($monto, $force	= false){
		$solicitud	= $this->mNumeroCredito;
		//no permitir si el total de abonos es mayos
		$abonos		= $this->mMontoAutorizado - $this->mSdoCapitalInsoluto;
		$sucess		= true;
		$msg		= "";
		$saldo		= ($this->getMontoAutorizado() == $this->getSaldoActual()) ? $monto : $this->getSaldoActual();
		
		if($abonos > $monto ){
			$msg	.= "ERROR\tEl nuevo Monto no debe ser menor a $abonos, usted intenta agregar $monto\r\n";
			$sucess	= false;
		}
		if($sucess == true){
			if($this->mMontoAutorizado < $monto){
				$this->mMontoAutorizado = $monto;
			}
			if($this->mMontoSolicitado < $monto){
				$this->mMontoSolicitado = $monto;
			}
			if($force == true){
				$this->mMontoSolicitado = $monto;
				$this->mMontoAutorizado = $monto;
				$saldo	= $monto;
				$msg	.= "ERROR\tMontos Forzados $monto\r\n";
			}
		//eliminar plan de pagos
		$idPlan		= $this->getNumeroDePlanDePagos();
		if( $idPlan > 0){
			$xPlan	= new cReciboDeOperacion(false, true, $idPlan); $xPlan->init();
			$xPlan->setRevertir(true);
			$msg	.= $xPlan->getMessages();
		}
		$tasa		= $this->getTasaDeInteres();
		
		$fecha_corte	= fechasys();
		//actualizar credito
		$this->setUpdate(array(
					$this->obj()->tasa_interes()->get() => $tasa,
					$this->obj()->ultimo_periodo_afectado()->get() => 0,
					$this->obj()->interes_diario()->get() => 0,
					$this->obj()->monto_solicitado()->get() => $this->mMontoSolicitado,
					$this->obj()->monto_autorizado()->get() => $this->mMontoAutorizado,
					$this->obj()->saldo_actual()->get() => $saldo,
					$this->obj()->saldo_conciliado()->get()	=> $saldo
				    ));
		//Cambiar monto del Recibo
		$recMin		= $this->getNumeroReciboDeMinistracion();
		if($recMin > 0){
			$xRec	= new cReciboDeOperacion(false, true, $recMin); $xRec->init();
			$xRec->setTotalPorProrrateo($monto);
			$msg	.= $xRec->getMessages();
		}
		//reestructurar SDPM
		$msg			.= $this->setReestructurarIntereses();

		$this->setDetermineDatosDeEstatus($fecha_corte);
		
		$this->init();
		}
		return $msg;
	}
	function setCambiarTasaNormal($tasa){
		$solicitud	= $this->mNumeroCredito;

		//eliminar plan de pagos
		$idPlan		= $this->getNumeroDePlanDePagos();
		if( $idPlan > 0){
			$xPlan		= new cReciboDeOperacion(false, true, $idPlan); $xPlan->init();
			$xPlan->setRevertir(true);
		}
		$fecha_corte	= fechasys();
		//actualizar credito
		$this->setUpdate(array(
					$this->obj()->tasa_interes()->get() => $tasa,
					$this->obj()->ultimo_periodo_afectado()->get() => 0
		));
		//reestructurar SDPM
		$msg			.= $this->setReestructurarIntereses();
		$this->setDetermineDatosDeEstatus($fecha_corte);
		$this->init();
		return $msg;
	}
	function setCambiarFechaMinistracion($fecha){
		$xF		= new cFecha(0);
		$solicitud	= $this->mNumeroCredito;
		$reciboMin	= $this->getNumeroReciboDeMinistracion();
		if($reciboMin > 0){
			$xRec		= new cReciboDeOperacion(false, true, $reciboMin); $xRec->init();
			$xRec->setFecha($fecha, true);
		}
		//eliminar plan de pagos
		$idPlan		= $this->getNumeroDePlanDePagos();
		if( $idPlan > 0){ $xPlan = new cPlanDePagos($idPlan); $xPlan->setEliminar(); $this->mMessages .= $xPlan->getMessages();	}
		//cambiar fecha ministracion
		if( $xF->getInt($this->mFechaDeAutorizacion) > $xF->getInt($fecha) ){
			$this->mFechaDeAutorizacion	= $fecha;
		}
		if( $xF->getInt($this->mFechaDeSolictud) > $xF->getInt($this->mFechaDeAutorizacion) ){
			$this->mFechaDeSolictud		= $this->mFechaDeAutorizacion;
		}
		if( $xF->getInt($this->mFechaUltimoMvtoCapital) > $xF->getInt($fecha) ){ $this->mFechaUltimoMvtoCapital		= $fecha;		}
		//Modificar operaciones de pago con fecha Menor a la fecha de Ministracion
		my_query("UPDATE operaciones_mvtos SET fecha_afectacion='$fecha', fecha_operacion='$fecha' WHERE docto_afectado=$solicitud AND fecha_operacion < '$fecha' ");
		my_query("UPDATE operaciones_recibos SET fecha_operacion='$fecha' WHERE docto_afectado=$solicitud AND fecha_operacion < '$fecha'");
		//
		$fecha_corte	= fechasys();
		
		//actualizar credito
		$this->setUpdate(array(
					"fecha_ministracion" => $fecha,
					"ultimo_periodo_afectado" => 0,
					"fecha_autorizacion" => $this->mFechaDeAutorizacion,
					"fecha_solicitud" => $this->mFechaDeSolictud,
					"fecha_ultimo_mvto" => $this->mFechaUltimoMvtoCapital
				    ));
		//reestructurar SDPM
		$msg			= $this->setReestructurarIntereses();
		$this->setDetermineDatosDeEstatus($fecha_corte);
		return $msg;
	}
	function setCambiarFechaDeVencimiento($fecha){
		$xF			= new cFecha();
		$xPer		= new cPeriocidadDePago($this->getPeriocidadDePago()); $xPer->init();
		$xPdto		= $this->getOProductoDeCredito();
		$dias_tolerados_del_plan	= $xPer->getDiasToleradosEnVencer();
		$dias_tolerados_del_pdto	= $xPdto->getDiasTolerados();
		$total_dias_tolerados		= ($dias_tolerados_del_pdto + $dias_tolerados_del_plan);
		$this->mMessages			.= "WARN\tDias tolerados del Producto $dias_tolerados_del_pdto, Dias Tolerados de la periocidad $dias_tolerados_del_plan \r\n";
		$sucess						= false;
		if( $xF->getInt($fecha) > $xF->getInt($this->getFechaDeVencimiento())){
			$nuevos_dias			= $xF->setRestarFechas($fecha, $this->getFechaDeMinistracion());
			$nuevos_pagos			= $this->getPagosAutorizados(); ceil( ($nuevos_dias / $this->getPagosAutorizados()) );
			if($this->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
				$nuevos_pagos			= ceil( ($nuevos_dias / $this->getPagosAutorizados()) );
			}
				
			$nuevos_pagos			= ($nuevos_pagos > $this->getPagosAutorizados()) ? $nuevos_pagos : $this->getPagosAutorizados();
			$fecha_de_vencimiento	= $fecha;
			$fecha_de_vencimiento_dinamico	= $xF->setSumarDias($total_dias_tolerados, $fecha);
			$fecha_de_mora					= $xF->setSumarDias($dias_tolerados_del_pdto, $fecha);
			$this->mMessages				.= "WARN\tNuevos dias $nuevos_dias, Nuevo pagos $nuevos_pagos, vencimiento $fecha_de_vencimiento, Venc dinamico $fecha_de_vencimiento_dinamico, Mora $fecha_de_mora \r\n";
			$sucess							= true;
			$this->setUpdate(array(
				"dias_autorizados" => $nuevos_dias,
				"pagos_autorizados" => $nuevos_pagos,
				"fecha_revision" => fechasys(),
				"fecha_vencimiento" => $fecha_de_vencimiento,
				"fecha_mora" => $fecha_de_mora,
				"fecha_vencimiento_dinamico" => $fecha_de_vencimiento_dinamico
			));
		} else {
			$this->mMessages	.= "ERROR\tLa fecha no puede ser cambiada a uno menor ($fecha) \r\n";
		}
		return $sucess;
	}
	function setCambiarEstadoActual($nuevoEstado, $fecha){
		$estado		= $this->getEstadoActual();
		$success	= true;
		$msg		= "";
		$arrUpdate	= array();
		$this->setCambiarFechaDeVencimiento($fecha);
		
		if( $this->getMontoAutorizado() != $this->getSaldoActual()  ){
			$success	= false;
			$msg		.= "OK\tMOVIMIENTOS\tEl Credito no puede modificado con movimientos\r\n";
		}
		if( ($estado == CREDITO_ESTADO_MOROSO) OR ($estado == CREDITO_ESTADO_VENCIDO)){
			//$success	= false;
			$msg		.= "WARN\tINVALIDO\tEl Credito no puede si esta MOROSO/VENCIDO\r\n";
		}
		if( ($nuevoEstado == CREDITO_ESTADO_VIGENTE)
			AND ($estado == CREDITO_ESTADO_SOLICITADO) OR ($estado == CREDITO_ESTADO_AUTORIZADO)
			 ){
			$success	= false;
			$msg		.= "ERROR\tINVALIDOVIG\tEl Credito no puede Ministrarse en este procedimiento\r\n";
		}
		if($estado == $nuevoEstado){
			$success	= false;
			$msg		.= "WARN\tSINCAMBIOS\tEl Credito no tiene cambios\r\n";
		}
		if($success == true){
			if($nuevoEstado == CREDITO_ESTADO_SOLICITADO){
				$arrUpdate[$this->obj()->monto_autorizado()->get() ] 	= 0;
				$arrUpdate[$this->obj()->dias_autorizados()->get() ] 	= 0;
				$arrUpdate[$this->obj()->docto_autorizacion()->get() ] 	= "";
			}
			
			if($estado == CREDITO_ESTADO_VIGENTE ){

					$idRP		= $this->getNumeroReciboDeMinistracion();
					if( $idRP > 0){
						$xRP		= new cReciboDeOperacion(false, true, $idRP); $xRP->init();
						$xRP->setRevertir(true);
					}
					$idPlan		= $this->getNumeroDePlanDePagos();
					if( $idPlan > 0){
						$xPlan		= new cReciboDeOperacion(false, true, $idPlan); $xPlan->init();
						$xPlan->setRevertir(true);
						$msg	.= $xPlan->getMessages();
					}
					$arrUpdate[$this->obj()->saldo_actual()->get() ] 		= 0;
					$arrUpdate[$this->obj()->fecha_ministracion()->get() ] 	= $this->getFechaDeAutorizacion();
					$arrUpdate[$this->obj()->fecha_ultimo_mvto()->get() ] 	= $this->getFechaDeAutorizacion();
			}
			$arrUpdate[$this->obj()->estatus_actual()->get() ] 				= $nuevoEstado;
			//actualizar credito
			$this->setUpdate( $arrUpdate );
		}
		$this->mMessages		.= $msg;
		return $msg;
	}
	
	function getNumeroReciboDeMinistracion(){
		$sql	= "SELECT idoperaciones_recibos FROM operaciones_recibos WHERE tipo_docto = 1 AND docto_afectado= " . $this->mNumeroCredito . " LIMIT 0,1";
		$numero	= mifila($sql, "idoperaciones_recibos");
		return $numero;
	}

	function setClonar($saldo = false, $autorizado = false){
		$saldo		= ($saldo == false) ? $this->getSaldoActual() : $saldo;
		$autorizado	= ($autorizado == false) ? $this->getMontoAutorizado() : $autorizado;
		//retorna numero de credito
		$xS			= new cSocio($this->getClaveDePersona()); $xS->init();
		$nuevoID 	= $xS->getIDNuevoDocto(iDE_CREDITO);
		$xCD		= new cCreditos_solicitud();
		$xCD->numero_solicitud($nuevoID);
		$xCD->numero_socio($this->getClaveDePersona());
		$xCD->causa_de_mora($this->mCausaDeMora);
		$xCD->contrato_corriente_relacionado($this->mContratoCorriente);
		$xCD->descripcion_aplicacion($this->mDescripcionDestino);
		$xCD->destino_credito($this->mTipoDeDestino);
		$xCD->dias_autorizados($this->mDiasAutorizados);
		$xCD->docto_autorizacion("CLONADO DEL CREDITO " . $this->mNumeroCredito);
		$xCD->eacp(EACP_CLAVE);
		$xCD->estatus_actual($this->getEstadoActual());
		//$xCD->estatus_de_negociacion()
		$xCD->fecha_autorizacion($this->mFechaDeAutorizacion);
		$xCD->fecha_castigo("2018-01-01");
		$xCD->fecha_conciliada(fechasys());
		$xCD->fecha_ministracion($this->mFechaMinistracion);
		$xCD->fecha_mora($this->mFechaMora);
		$xCD->fecha_revision(fechasys());
		$xCD->fecha_solicitud($this->mFechaDeSolictud);
		$xCD->fecha_ultimo_mvto($this->mFechaUltimoMvtoCapital);
		$xCD->fecha_vencimiento($this->mFechaVencimiento);
		$xCD->fecha_vencimiento_dinamico($this->mFechaVencimientoLegal);
		$xCD->grupo_asociado($this->mGrupoAsociado);
		$xCD->idusuario( getUsuarioActual() );
		$xCD->interes_diario( $this->mInteresDiario );
		$xCD->interes_moratorio_devengado( $this->mInteresMoratorioDev );
		$xCD->interes_moratorio_pagado( $this->mInteresMoratorioPag );
		$xCD->interes_normal_devengado( $this->mInteresNormalDevengado );
		$xCD->interes_normal_pagado( $this->mInteresNormalPagado );
		$xCD->monto_autorizado( $autorizado );
		$xCD->monto_parcialidad( $this->mMontoFijoParcialidad );
		$xCD->monto_solicitado( $autorizado );				//igual que el autorizado
		$xCD->numero_pagos($this->getPagosAutorizados());
		$xCD->saldo_actual($saldo);
		$xCD->saldo_conciliado($saldo);
		$xCD->saldo_vencido(0);
		$xCD->sdo_int_ant(0);
		$xCD->sucursal(getSucursal());
		$xCD->pagos_autorizados($this->getPagosAutorizados());
		$xCD->periocidad_de_pago($this->getPeriocidadDePago());
		$xCD->periodo_notificacion(0);
		$xCD->periodo_solicitudes( EACP_PER_SOLICITUDES  );
		$xCD->plazo_en_dias($this->mDiasAutorizados);
		$xCD->tipo_credito( $this->mModalidadDeCredito );
		$xCD->tipo_autorizacion($this->mTipoDeAutorizacion);
		$xCD->tasa_ahorro( $this->mTasaAhorro );
		$xCD->tasa_interes( $this->mTasaInteres );
		$xCD->tasa_moratorio( $this->mTasaMoratorio );
		$xCD->tipo_convenio( $this->mTipoDeConvenio );
		$xCD->tipo_de_calculo_de_interes( $this->mTipoDeCalculoDeInteres );
		$xCD->tipo_de_pago( $this->mTipoDePago );
		$xCD->ultimo_periodo_afectado( $this->mParcialidadActual );
		
		$xCD->query()->insert()->save();
		
		return $nuevoID;
	}

	function getInteresNormalPorPagar($fecha = false){
		$DInt	= $this->getInteresDevengado($fecha);
		return $this->mInteresNormalDevengado - $this->mInteresNormalPagado + $DInt[SYS_INTERES_NORMAL];
	}
	function getEsAfectable(){ $afectable = true; if($this->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO OR $this->getEstadoActual() == CREDITO_ESTADO_SOLICITADO){ $afectable = false; } return $afectable; }
	function setReestructurarIntereses($FechaInicial = false, $FechaFinal = false, $ForceMoratorios = false){
		$xT				= new cTipos();
		$msg			= "";
		$socio			= $this->getClaveDePersona();
		$solicitud		= $this->getNumeroDeCredito();
		$xCUtils		= new cUtileriasParaCreditos();
		
		$FechaFinal		= ($FechaFinal == false) ? fechasys() : $FechaFinal;
		$FechaInicial	= ($FechaInicial == false) ? "1998-01-01" : $FechaInicial;
		if($this->getEsAfectable() == true ){
			if( ($this->getTipoDePago() == CREDITO_TIPO_PAGO_UNICO) OR ($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) ){
				my_query("DELETE FROM operaciones_recibos WHERE numero_socio=$socio AND docto_afectado=$solicitud AND tipo_docto=11");
				my_query("DELETE FROM operaciones_mvtos WHERE socio_afectado=$socio AND docto_afectado=$solicitud
				AND (tipo_operacion=410 OR tipo_operacion=411 OR tipo_operacion=412 OR tipo_operacion=413 
				OR tipo_operacion=1005 OR tipo_operacion=601)");
			}
			if( CREDITO_PURGAR_ESTADOS == true){
				my_query("DELETE FROM operaciones_mvtos WHERE socio_afectado=$socio AND docto_afectado=$solicitud	AND ( tipo_operacion=111 OR tipo_operacion=113 OR tipo_operacion=114 OR tipo_operacion=115)");				
			} 
			//Reestructurar Estatus
			$msg   .= $xCUtils->setEstatusDeCreditos(DEFAULT_RECIBO, $FechaFinal, false, true, $solicitud);
			//Reestructurar Intereses
			if( $this->getEstadoActual() != CREDITO_ESTADO_VIGENTE){
				//$this->
				//$msg			.= $cUCredit->setRegenerarCreditosAVencidos( $fecha );
				//$msg			.= $cUCredit->setRegenerarCreditosAMora( $fecha );
				$DEstado		= $this->setDetermineDatosDeEstatus($FechaFinal);
				$EstadoCalculado	= $DEstado[SYS_ESTADO];
				$msg			.= "WARN\tRE_ESTATUS\tRecalcular Estatus de " . $this->getEstadoActual() . " A $EstadoCalculado\n";
				switch($EstadoCalculado){
					case CREDITO_ESTADO_VIGENTE:
						$this->setEnviarVigente($FechaFinal, $this->getPeriodoActual(), DEFAULT_RECIBO);
					break;
					case CREDITO_ESTADO_MOROSO:
						$this->setEnviarMoroso($this->mFechaMora, $this->getPeriodoActual(), DEFAULT_RECIBO);
						break;
					case CREDITO_ESTADO_VENCIDO:
						$this->setEnviarVencido($this->mFechaVencimientoLegal, $this->getPeriodoActual(), DEFAULT_RECIBO);
						break;
				}
			} else {
				$msg			.= "ESTATUS\tSin Cambios de Estado : " . $this->getEstadoActual() . "\n";
			}
			$msg			.= $xCUtils->setGenerarMvtoFinDeMes($FechaInicial, $FechaFinal, $solicitud, true);
			
			if ( $this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
				$msg			.= $xCUtils->setReestructurarSDPM(false, $solicitud);
			} else {
				$msg			.= $xCUtils->setReestructurarSDPM_Planes(false, $solicitud);
			}
			$msg			.= $xCUtils->setRegenerarInteresDevengado( $solicitud, $FechaInicial, $FechaFinal, $ForceMoratorios );
			$msg			.= $xCUtils->setAcumularIntereses(false, $solicitud);
		} else { $msg		.= "NO_PROCESADO\tCredito No Afectable\n"; }
		$this->mMessages	.= $msg;
		$this->init();
		return $msg;
	}
	function obj(){
		if( $this->mOB == null){
			$this->mOB	= new cCreditos_solicitud();
			$this->mOB->setData( $this->mOB->query()->initByID($this->mNumeroCredito) );
		}
		return $this->mOB;
	}
	function getOProductoDeCredito($TipoDeConvenio = false){
		$TipoDeConvenio	= ($TipoDeConvenio == false) ? $this->getClaveDeProducto() : $TipoDeConvenio;	
		$xP		= new cProductoDeCredito($TipoDeConvenio);	$xP->init();	return $xP;
	}
	function getOPeriocidad(){ $xPer = new cPeriocidadDePago($this->getPeriocidadDePago()); $xPer->init(); return $xPer; }
	function addGarantiaReal($tipo, $tipo_de_valuacion = false, $valor = 0, $persona_propietaria = false, $nombre_del_propietario = "", $fecha_de_adquisicion = false, 
			$documento_presentado = "", $estado_fisico = false, $descripcion = "", $observaciones = "", $fecha_actual = false){
		$xgar	= new cCreditosGarantias();
		$xgar->setClaveDeCredito($this->getNumeroDeCredito());
		$xgar->setClaveDePersona($this->getClaveDePersona());
		$xgar->add($tipo, $tipo_de_valuacion, $valor, $persona_propietaria, $nombre_del_propietario, $fecha_de_adquisicion, 
			$documento_presentado, $estado_fisico, $descripcion, $observaciones, $fecha_actual);
	}
	function getCargosDeCobranza(){
		$credito	= $this->mNumeroCredito;
		$persona	= $this->mNumeroSocio;
		$sql		= "SELECT SUM(afectacion_real) AS 'monto' FROM operaciones_mvtos WHERE `socio_afectado`=$persona AND `docto_afectado` = $credito AND	(`tipo_operacion` = 601) AND `estatus_mvto`= 40 ";
		$monto		= mifila($sql, SYS_MONTO);
		return $monto;
	}

	function getFechaUltimoDePago(){	return $this->mFechaUltimoPago;	}
	function getMontoUltimoPago(){ return $this->mMontoUltimoPago; 	}
	function getFechaDevencimientoLegal(){ return $this->mFechaVencimientoLegal; }
	function getRespetarPlanDePago(){
		if($this->mObjEstado == null){ $this->getOEstado(); }
		return ( intval($this->mObjEstado->respetar_plan_de_pagos()->v() ) ==1 ) ? true : false;
	}
	function getOEstado(){
		$xEst	= new cCreditos_estatus();
		$xEst->setData( $xEst->query()->initByID($this->getEstadoActual()) );
		$this->mObjEstado = $xEst;
		return $this->mObjEstado; 
	}
	function getPagosSinCapital($tipo = false){
		$tipo		= ($tipo === false) ? $this->getTipoDePago() : $tipo;
		return ($tipo == CREDITO_TIPO_PAGO_INTERES_COMERCIAL OR $tipo == CREDITO_TIPO_PAGO_INTERES_PERIODICO) ? true : false; 
	}
	function getInteresDevengado($fecha_de_calculo = false, $parcialidad = false, $fecha_anterior = false, $solo_mora_corriente = false){
		$FECHA_DE_OPERACION		= ($fecha_de_calculo == false ) ? fechasys() : $fecha_de_calculo ;
		$OProd					= $this->getOProductoDeCredito();
		$xF						= new cFecha(0, $FECHA_DE_OPERACION);
		$credito				= $this->mNumeroCredito;
		$PERIODO_A_PAGAR		= ($parcialidad === false) ? $this->getPeriodoActual() : $parcialidad;
		$ESTADO_ACTUAL			= $this->getEstadoActual();
	
		$TASA_NORMAL			= $this->getTasaDeInteres();
		$TASA_MORA				= $this->getTasaDeMora();
		$SALDO_ACTUAL			= $this->getSaldoActual();
		$BASE_NORMAL			= $this->getSaldoActual();
		$BASE_MORA				= $this->getSaldoActual();
		$TIPO_DE_PAGO			= $this->getTipoDePago();
		$PAGOS_SIN_CAPITAL		= $this->getPagosSinCapital();
		$DIAS_NORMAL			= 0;
		$DIAS_MORA				= 0;
		$DIVISOR_DE_INTERESES		= EACP_DIAS_INTERES;
		$MONTO_ORIGINAL_DE_CREDITO	= $this->getMontoAutorizado();
		$rw						= $this->getDatosDeCredito();
		$socio					= $this->getClaveDePersona();
		$fecha					= $this->getFechaDeMinistracion();
			
		$msg					= "";
		$interes				= 0;
		$moratorio				= 0;
		$cobranza				= 0;
		$xF2					= new cFecha();
		$xPlan					= null;
		$PlanInit				= false;
		if($fecha_anterior == false){
			$credito				= setNoMenorQueCero($credito);
			$sqlDev					= "SELECT * FROM `creditos_sdpm_acumulado` WHERE credito = $credito ";
			$DDev					= obten_filas($sqlDev);
			$FECHA_DE_ULTIMO_PAGO	= (isset($DDev["fechaActual"])) ? $DDev["fechaActual"] : $this->getFechaDeMinistracion();
		} else {
			$FECHA_DE_ULTIMO_PAGO	= $fecha_anterior;
		}
		
		//pre evaluadores
		if ( $xF2->getInt($FECHA_DE_ULTIMO_PAGO) < $xF2->getInt($FECHA_DE_OPERACION) ){
			$DIAS_NORMAL						= $xF->setRestarFechas($FECHA_DE_OPERACION, $FECHA_DE_ULTIMO_PAGO);
			if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				
			} else {
				$xPlan							= $this->getOPlanDePagos();
				$PlanInit						= $xPlan->init();
				$DLetra							= $xPlan->getOLetra($PERIODO_A_PAGAR);
				$FECHA_COMPROMETIDA_DE_PAGO		= $DLetra->getFechaDePago();
				//$MONTO_PAGADO_DE_PARCIALIDAD	= 0;
			}
	
			if($TASA_MORA > 0){
	
				if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
					$FECHA_COMPROMETIDA_DE_PAGO		= $this->getFechaDeMora();
					$DIAS_MORA	= $xF->setRestarFechas($FECHA_DE_OPERACION, $FECHA_COMPROMETIDA_DE_PAGO );
					$DIAS_MORA	= ($DIAS_MORA < 0) ? 0 : $DIAS_MORA;
					$msg		.= "WARN\tDIAS_MORA\tDias de mora Calculado entre $FECHA_DE_OPERACION y $FECHA_COMPROMETIDA_DE_PAGO\r\n";
					$msg		.= "WARN\tBASE_MORA_LETRA\tBase de Mora $BASE_NORMAL, por Dias $DIAS_MORA a TASA $TASA_MORA, desde el dia $FECHA_COMPROMETIDA_DE_PAGO\r\n";
				} else {
					if($PlanInit	== true ){
						if(isset($FECHA_COMPROMETIDA_DE_PAGO) AND $FECHA_COMPROMETIDA_DE_PAGO != false ){
							$DIAS_MORA	= $xF->setRestarFechas($FECHA_DE_OPERACION, $FECHA_COMPROMETIDA_DE_PAGO);
							$DIAS_MORA	= ($DIAS_MORA < 0) ? 0 : $DIAS_MORA;
							$BASE_MORA	= $DLetra->getCapital();// + $DLetra->getInteres() + $DLetra->getOtros();
							//$DIAS_MES	= $xF->g
							if( $solo_mora_corriente == true){
								if($DIAS_MORA > $xF->getDiasCorrientesDeMes()){
									$DIAS_MORA	= $xF->getDiasCorrientesDeMes();
								}
							}
							$msg		.= "WARN\tBASE_MORA_LETRA\tBase de Mora $BASE_MORA, por Dias $DIAS_MORA a TASA $TASA_MORA, desde el dia $FECHA_COMPROMETIDA_DE_PAGO\r\n";
							$msg		.= "WARN\tDIAS_MORA\tDias de mora Calculado entre $FECHA_DE_OPERACION y $FECHA_COMPROMETIDA_DE_PAGO\r\n";
						} else {
							$msg		.= "WARN\tMORA\tSin datos para letra $PERIODO_A_PAGAR\r\n";
						}
					} else { $msg	.= "WARN\tNO_INIT\tPLAN NO INICIADO\r\n"; }
					$msg			.= $xPlan->getMessages(OUT_TXT);
				}
				//Ajustar dias de mora MAXIMO
				if($DIAS_MORA > INTERES_DIAS_MAXIMO_DE_MORA){ $DIAS_MORA = INTERES_DIAS_MAXIMO_DE_MORA;	}
				if($DIAS_NORMAL > INTERES_DIAS_MAXIMO_A_DEVENGAR){ $DIAS_NORMAL = INTERES_DIAS_MAXIMO_A_DEVENGAR; }
				if($BASE_NORMAL <= 0){$BASE_MORA = 0; $DIAS_MORA = 0; $TASA_MORA = 0; }
				
				eval($OProd->getPreModInteres());
				$moratorio		= ( ($BASE_MORA * $DIAS_MORA) * $TASA_MORA ) / $DIVISOR_DE_INTERESES;
				$interes		= ( ($BASE_NORMAL * $DIAS_NORMAL) * $TASA_NORMAL ) / $DIVISOR_DE_INTERESES;
				if($OProd->getAplicaMoraPorGastos() == SYS_UNO){
					$cobranza	= $moratorio;
					$msg		.= "WARN\tBASE_CBZA_LETRA\tBase de Cobranza $moratorio\r\n";
					$moratorio	= 0;
				}
				//POS Valuadores
				eval($OProd->getPosModInteres());
	
				$msg				.= "$socio\t $credito\tSALDO: $BASE_NORMAL\t DIAS : $DIAS_NORMAL\tINTERES : $interes\tMORA : $moratorio\r\n";
			} else {
				$msg		.= "OK\tTASA_MORA\t Tasa de Mora ($TASA_MORA) CERO\r\n";
			}
		} else {
			$msg		.= "WARN\tNO_CALCULO\tNo se Calculan Devengados ($FECHA_DE_OPERACION|$FECHA_DE_ULTIMO_PAGO)\r\n";
		}
		
		$arrInteres[SYS_INTERES_NORMAL]		= $interes;
		$arrInteres[SYS_INTERES_MORATORIO]	= $moratorio;
		$arrInteres[SYS_GASTOS_DE_COBRANZA]	= $cobranza;
		$this->mMessages						.= $msg;
		$arrInteres[SYS_MSG]					= $msg;
		return $arrInteres;
	}
	function setCreditoPagado($fecha = false){
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		//efectuar las operaciones de cierre de credito
		if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
			$idPlan	= $this->getNumeroDePlanDePagos();
			if( isset($idPlan)  AND ($idPlan > 1) ){ $xPlan	= new cPlanDePagos($idPlan); $xPlan->setEliminar(); $this->mMessages	.= $xPlan->getMessages(); }
		}
		$this->setUpdate(array(
				"saldo_conciliado"	=>0,
				"saldo_actual"	=>0,
				"saldo_vencido"	=>0,
				"fecha_conciliada"	=>$fecha,
				"fecha_revision"	=> $fecha,
				"ultimo_periodo_afectado" => $this->getPagosAutorizados(),
				"sdo_int_ant" => 0,
				"fecha_ultimo_mvto" => $fecha
		));
	}
	function getOPersona($clave = false ){
		$clave		= ($clave == false) ? $this->getClaveDePersona() : $clave;
		if($this->mObjSoc == null){ $this->mObjSoc	= new cSocio($clave); $this->mObjSoc->init(); }
		return $this->mObjSoc;
	}
	function getOOficial(){ $xO	= new cOficial($this->mOficialDeCredito);	$xO->init();		return $xO;	}
	function getClaveDeOficialDeCredito(){ return $this->mOficialDeCredito; }
	function getSaldoIntegrado($fecha = false, $conImpuestos = false){
		$fecha			= ($fecha == false ) ? fechasys() : $fecha;
		if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
			$periodo		= $this->getPeriodoActual() + 1;
			$DINT			= $this->getInteresDevengado($fecha, $periodo, false, true);
		} else {
			$DINT			= $this->getInteresDevengado($fecha);
		}
		$intereses		= ( ($this->getInteresNormalDevengado() + $DINT[SYS_INTERES_NORMAL]) - $this->getInteresNormalPagado());
		$mora			= ( ($this->getInteresMoratorioDev() + $DINT[SYS_INTERES_MORATORIO])- $this->getInteresMoratorioPagado() );
		$impuestos		= 0;
		if ( $conImpuestos == true ){
			$impuestos	= ($intereses + $mora ) * $this->getTasaIVA();
		}
		return $this->getSaldoActual($fecha) + $intereses + $mora + $impuestos;
	}
	function initPagosEfectuados($data = false, $fecha_de_corte = false){
		$xF				= new cFecha();
		$fecha_minis	= $xF->getInt($this->getFechaDeMinistracion());
		$fecha_de_corte	= $xF->getInt($fecha_de_corte);
		
		if($this->mInitPagos == false){
			//comparar pagos con letras
			$sql		= "SELECT * FROM `creditos_abonos_parciales` WHERE docto_afectado=" . $this->mNumeroCredito ;
			$mq			= new MQL();
			$data		= ($data == false) ? $mq->getDataRecord($sql) : $data;
			$cnt		= 1;
			$OProd		= $this->getOProductoDeCredito();
			
			$this->mMontoCapitalPagado							= 0;
			$this->mMontoInteresPagado							= 0;
			$this->mMontoMoraPagado								= 0;
				
			$this->mMontoUltimoPago								= 0;
			$this->mFechaUltimoPago								= $this->getFechaDeMinistracion();
			
			$this->mAbonosAcumulados							= 0;
			
			if($fecha_minis > $fecha_de_corte){
				$this->mMessages									.= "WARN\tCREDITO OMITIDO\r\n";
				//$this->mFechaUltimoMvtoCapital						= $this->getFechaDeMinistracion();
				
			} else {
				foreach ($data as $row){
					$idparcial		= $row["periodo_socio"];
					$fecha_op		= $xF->getInt($row["fecha_de_pago"]);
					if($fecha_op > $fecha_de_corte){
						$this->mMessages									.= "WARN\tPARCIALIDAD $idparcial de fecha " . $row["fecha_de_pago"] . " OMITIDA\r\n";
					} else {
						$this->mPagos[$idparcial][SYS_MONTO]				= $row["total"];
						$this->mPagos[$idparcial][SYS_FECHA]				= $row["fecha_de_pago"];
						$this->mPagos[$idparcial][SYS_INTERES_NORMAL]		= $row["interes_normal"];
						$this->mPagos[$idparcial][SYS_INTERES_MORATORIO]	= $row["interes_moratorio"];
						$this->mPagos[$idparcial][SYS_VARIOS]				= $row["otros"];
						$this->mPagos[$idparcial][SYS_CAPITAL]				= $row["capital"];
						$this->mPagos[$idparcial][SYS_IMPUESTOS]			= $row["impuesto"];
						
						//$this->mMessages			.= "OK\t$idparcial\t" . $row["fecha_de_pago"] . "\t" . $row["capital"]  . "\t" . $row["interes_normal"] . "\t" . $row["total"] ."\r\n";
						
						//elegir si es plan de pagos
						if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
							$this->mPagos[$idparcial][SYS_ESTADO]			= CREDITO_ESTADO_VIGENTE;
						} else {
							//$fecha_de_pago				= $xPlan->getFechaDePago($fecha_de_referencia, $simletras1);
							$fecha_de_pago					= $this->getFechaEstimadaPorNumeroDePago($idparcial);
							//Agregar tolerancia de pagos
							$fecha_de_pago					= $xF->setSumarDias($OProd->getDiasTolerados(), $fecha_de_pago);
							$this->mPagos[$idparcial][SYS_ESTADO]			= CREDITO_ESTADO_VIGENTE;
							if($xF->getInt($row["fecha_de_pago"]) > $xF->getInt($fecha_de_pago) ){
								$this->mPagos[$idparcial][SYS_ESTADO]	= CREDITO_ESTADO_VENCIDO;
								$this->mMessages			.= "WARN\t$idparcial\tPago $idparcial cambiado a Vencido\r\n";
							}
							//si es vencido y la fecha de primer atraso es null
							if($this->mPagos[$idparcial][SYS_ESTADO] == CREDITO_ESTADO_VENCIDO AND ($this->mFechaPrimerAtraso == null)){
								$this->mFechaPrimerAtraso				= $row["fecha_de_pago"];
								$this->mMessages			.= "ERROR\t$idparcial\tFecha de Primera atraso a " .  $this->mFechaPrimerAtraso . " \r\n";
							}
						}
						//estado estado del credito
						$this->mMontoCapitalPagado							+= $row["capital"];
						$this->mMontoInteresPagado							+= $row["interes_normal"];
						$this->mMontoMoraPagado								+= $row["interes_moratorio"];
						
						if($cnt == 1){ $this->mDPrimerPagoEfect				= $this->mPagos[$idparcial]; }
						$this->mDUltimoPagoEfect							= $this->mPagos[$idparcial];
						
						$this->mMontoUltimoPago								= $row["total"];
						$this->mFechaUltimoPago								= $row["fecha_de_pago"];
						$this->mMessages									.= "OK\t$idparcial\tFecha de ultimos datos establecidos a (" . getFMoney($row["total"]) .  ") y Fecha " . $row["fecha_de_pago"] . " \r\n";
						$this->mAbonosAcumulados							+= $row["capital"];
						$this->mFechaAcumulada								= $row["fecha_de_pago"];
						//SALDO INSOLUTO SEGUN LA LETRA
						$this->mPagos[$idparcial][SYS_SALDO]				= setNoMenorQueCero( $this->getMontoAutorizado() - $this->mAbonosAcumulados);
					}
					$cnt++;
				}
				$this->mInitPagos	= true;
			}
		}
		return $this->mInitPagos;
	}
	function getDPrimerPagoEfect(){ return $this->mDPrimerPagoEfect;	}
	function getDUltimoPagoEfect(){ return $this->mDUltimoPagoEfect; }
	function getTipoEnSistema(){ return $this->getOProductoDeCredito()->getTipoEnSistema(); }
	function getSaldoAFecha($fecha = false){
		$xF			= new cFecha();
		$saldo		= $this->mSdoCapitalInsoluto;
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		if($xF->getEsActual($fecha) == true ){
			$this->mFechaAcumulada			= $this->getFechaUltimoDePago();
			$this->mAbonosAcumulados		= setNoMenorQueCero($this->getMontoAutorizado() - $this->getSaldoActual());
			//$this->mMessages			.= "WARN\tFecha NADA \r\n";
		} else {
			//Iniciar pagos
			$this->initPagosEfectuados(false, $fecha);
			$saldo			= $this->getMontoAutorizado() - $this->mAbonosAcumulados;
		}
		return $saldo;
	}
	function getFechaDePagoDinamica(){ return $this->mFechaAcumulada; }
	function getAbonosDinamico(){ return $this->mAbonosAcumulados; }
	function getMOP($fecha = false, $central_de_riesgo = false){
		$mop		= "";
		$xF			= new cFecha();
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		$this->initPagosEfectuados(false, $fecha);
		if($this->getSaldoActual($fecha) <= 0){
			$mop		= "UR";
		} else {
			if($this->getSaldoActual($fecha) == $this->getMontoAutorizado()){
				$dias		= $xF->setRestarFechas($fecha, $this->getFechaDeMinistracion());
				if($dias < 90){
					$mop	= "00";
				} else {
					$mop	= "UR";
				}
			} else {
				//verificar Plan de pagos
				//if($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
					if($this->getEstadoActual($fecha) == CREDITO_ESTADO_VIGENTE ){
						$mop		= "01";
					} else {
						$dias		= setNoMenorQueCero($xF->setRestarFechas($fecha , $this->getFechaDeVencimiento() ));
						if($dias > 0 AND $dias <=29){
							$mop		= "02";
						} else if($dias >= 30 AND $dias <= 59){
							$mop		= "03";
						} else if($dias >= 60 AND $dias <= 89){
							$mop		= "04";
						} else if($dias >= 90 AND $dias <= 119){
							$mop		= "05";
						} else if($dias >= 120 AND $dias <= 149){
							$mop		= "06";
						} else if($dias >= 150 AND $dias <= 360){
							$mop		= "07";
						} else {
							$mop		= "96";
						}
					}
				/*} else {
					//planes de pago
				}*/
			}
		}
		RETURN $mop;
	}
	function getEsRevolvente(){ return false; }
	function getFechaDePrimerAtraso(){ return $this->mFechaPrimerAtraso; }
	function getListadoDePagos(){ return $this->mPagos; }
	function isAFinalDePlazo(){	return ($this->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO )? true : false;	}
	function isAtrasado(){ /**/ }
	function isPagable(){
		$pagable	= true;
		if($this->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO OR $this->getEstadoActual() == CREDITO_ESTADO_SOLICITADO
		OR $this->getSaldoActual() <= 0){
			$pagable = false;
		}
		return $pagable;
	}
	function getOtroDatos($clave){
		$sql	= "SELECT * FROM `creditos_otros_datos` WHERE `clave_de_credito`=" . $this->mNumeroCredito . " AND `clave_de_parametro`='$clave' LIMIT 0,1";
		$D		= obten_filas($sql);
		$valor	= "";
		if(isset($D["valor_de_parametro"])){ $valor	= $D["valor_de_parametro"]; }
		return $valor;
	}
	function setOtrosDatos($clave, $valor){
		$xCat 	= new cCreditos_otros_datos();
		$xF		= new cFecha();
		$sql	= "DELETE FROM `creditos_otros_datos` WHERE `clave_de_credito`=" . $this->mNumeroCredito . " AND `clave_de_parametro`='$clave'";
		my_query($sql);
		$xCat->clave_de_credito($this->mNumeroCredito);
		$xCat->clasificacion_de_parametro("");
		$xCat->clave_de_parametro($clave);
		$xCat->fecha_de_expiracion($xF->getFechaMaximaOperativa());
		$xCat->idcreditos_otros_datos( $xCat->query()->getLastID() );
		$xCat->sucursal(getSucursal());
		$xCat->valor_de_parametro($valor);
		$xCat->query()->insert()->save();
		$this->mMessages	.= "OK\tAgregar $clave con Valor $valor\r\n";
	}
	function OCatOtrosDatos(){ $xCat	= new cCreditosOtrosDatos(); return $xCat; }
}//END CLASS

class cCreditosVencidos {
	private $mClaveDeCredito	= false;
	private $mClaveDePersona	= false;
	private $mObjC		= null;
	
	function __construct($credito, $iniciar = false){
		
	}
	function setObj($obj){
		$this->mObjC	= $obj;
	}
	function getMOP(){
		
	}
}


class cPeriodoDeCredito {
	private $mCode		= 99;
	private $mArrDatos	= array();
	private $mInit		= false;
	private $mMessages			= "";
	
	/**
	 * Inicializa la clase
	 * @param integer $codigo
	 */
	function __construct($codigo = false){
		$this->mCode	= ($codigo == false) ? EACP_PER_SOLICITUDES : $codigo ;
	}
	function init(){
		$sqlI	= "SELECT * FROM creditos_periodos WHERE idcreditos_periodos = " . $this->mCode . " LIMIT 0,1 ";
		$this->mArrDatos	= obten_filas($sqlI);
		$this->mInit		= true;
	}
	function add($fecha_inicial = false, $fecha_final = false, $responsable = false,
				$fecha_reunion = false, $descripcion = "", $codigo = false ){
				$xF				= new cFecha();
				
				$fecha_inicial	= ( $fecha_inicial == false) ? fechasys() : $fecha_inicial;
				$anno			= ( $fecha_inicial == false) ? date("Y" , strtotime(fechasys()) ) : date("Y" , strtotime($fecha_inicial) ) ;
				$fecha_final	= ( $fecha_final == false) ? date("Y-m-d", strtotime("$anno-12-31") ) : $fecha_final;
				$responsable	= ( $responsable == false ) ? DEFAULT_USER : $responsable;
				$codigo			= ( $codigo == false ) ? $anno . "99" : $codigo;
				$descripcion	= ( $descripcion == "" ) ? "[$codigo]Periodo de solicitudes de Credito del " . $xF->getFechaCorta($fecha_inicial) .  " al " . $xF->getFechaCorta($fecha_final) : $descripcion;
				$fecha_reunion	= ( $fecha_reunion == false ) ? $xF->setSumarDias(1, $fecha_final) : $fecha_reunion;
				
		$sql	= "INSERT INTO creditos_periodos(idcreditos_periodos, descripcion_periodos, fecha_inicial, fecha_final, fecha_reunion, periodo_responsable) 
    			VALUES($codigo, '$descripcion', '$fecha_inicial', '$fecha_final', '$fecha_reunion', $responsable) ";
		my_query($sql);
	}
	/**
	 * Modifica el periodo de Credito Actual
	 * @param integer $nuevo_periodo
	 */
	function setCambiar($nuevo_periodo){
		$xC		= new cConfiguration();
		$msg	= "";
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
	function checkPeriodoVigente($fecha = false){
		$fecha 	= ($fecha == false) ? fechasys() : $fecha;
		$xF				= new cFecha();
		if($this->mInit == false){ $this->init(); }
		$fecha_final		= $this->getFechaFinal();
		$this->mMessages	.= "WARN\tLa Fecha del periodo es $fecha_final, fecha comparada es $fecha\r\n";
		return ($xF->getInt($fecha) > $xF->getInt($fecha_final )) ? false : true;
	}
	function getMessages($put = OUT_TXT){ $xH	 = new cHObject(); return $xH->Out($this->mMessages, $put);	}
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

	private $mTasaDeIVA			= false;
	private $mDatosDeCredito	= array();
	private $mDatosDeProducto	= array();
	private $mOB				= null;
	private $mORec				= null;
	private $mPagos				= array();
	private $mIsInit			= false;
	private $mTipoDeDias		= false;
	
	private $mSocio				= 0;
	private $mCredito			= 0;
		
	private $mClaveDePersona	= false;
	private $mClaveDeCredito	= false;
	private $mClaveDeGrupo		= false;
	private $mClaveDeEmpresa	= false;
	private $mMontoOperado		= 0;
	private $mFechaOperado		= false;
	private $mFechaPago			= false;
	private $mSaldoInicial		= 0;
	private $mSaldoFinal		= 0;
	private $mDiasTolerancia	= 0;
	private $mTotalParciales	= 0;
	private $mBaseIVAOperado	= 0;
	private $mParcsPends		= array();
	private $mPagosAtrasados	= 0;
	private $mMontoAtrasado		= 0;
	private $mFechaPrimerAtraso	= false;
	private $mPagosConSaldo		= 0;
	private $mFechaProximoPago	= false;
			
	//private $mNumeroProximoPagoo= false;
	private $mPagoSegunFecha		= false;
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
	function initByCredito($ClaveCredito = false){
		$ClaveCredito			= ($ClaveCredito == false) ? $this->mCredito : $ClaveCredito;
		$xCred					= new cCredito($ClaveCredito);
		$xCred->init();
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
		if($xCred->getNumeroDePlanDePagos() != false){
			$this->mNumeroDePlan	= $xCred->getNumeroDePlanDePagos();
			$this->init();
		}
		$this->mIsInit			= true;
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
		$fecha_de_corte	= ($fecha_de_corte == false) ? fechasys() : $fecha_de_corte;
		$int_fecha		= $xF->getInt($fecha_de_corte);
		$ByNum			= ($inicio == 0) ? "" : " AND periodo_socio >= $inicio ";
		$persona		= $this->getClaveDePersona();
		$credito		= $this->getClaveDeCredito();
		$sql			= "SELECT * FROM `letras` WHERE docto_afectado = $credito $ByNum";
		//setLog($sql);
		$rows			= ($data == false) ? $mql->getDataRecord($sql) : $data;
		foreach($rows as $rw){
			$this->mParcsPends[$rw["periodo_socio"]][SYS_TOTAL]			= $rw["letra"];
			$this->mParcsPends[$rw["periodo_socio"]][SYS_CAPITAL]			= $rw["capital"];
			$this->mParcsPends[$rw["periodo_socio"]][SYS_INTERES_NORMAL]	= $rw["interes"];
			$this->mParcsPends[$rw["periodo_socio"]][SYS_IMPUESTOS]		= $rw["iva"];
			$this->mParcsPends[$rw["periodo_socio"]][SYS_AHORRO]			= $rw["ahorro"];
			$this->mParcsPends[$rw["periodo_socio"]][SYS_VARIOS]			= $rw["otros"];
			$this->mParcsPends[$rw["periodo_socio"]][SYS_FECHA]			= $rw["fecha_de_pago"];
			$this->mParcsPends[$rw["periodo_socio"]][SYS_NUMERO]			= $rw["periodo_socio"];
			
			if( $xF->getInt($rw["fecha_de_pago"]) <= $int_fecha ){
				if(setNoMenorQueCero($rw["capital"]) > 0){
					$this->mPagosAtrasados++;
					$this->mMontoAtrasado			+= $rw["capital"];
					if($this->mFechaPrimerAtraso == false){
						$this->mFechaPrimerAtraso	= $rw["fecha_de_pago"];
						$this->mMessages			.= "WARN\tFecha de Atraso pago al " . $rw["fecha_de_pago"] . "\r\n";
					}
					$this->mPagoSegunFecha			= $rw["periodo_socio"];
				}
			}
			if(setNoMenorQueCero($rw["capital"]) > 0){ $this->mPagosConSaldo++; }
			if($this->mFechaProximoPago == false){
				$this->mFechaProximoPago	= $rw["fecha_de_pago"];
				$this->mMessages			.= "OK\tFecha de proximo pago al " . $rw["fecha_de_pago"] . "\r\n";
			}
		}
		//si la fecha es mayor... cargar el periodo actual
		
		return $this->mParcsPends;
	}
	function getLetrasInArray($tipo, $inicio = 0){
		$arrDev		= array();
		$sql1 		= "SELECT periodo_socio, afectacion_real FROM operaciones_mvtos 
		WHERE (recibo_afectado = " . $this->mNumeroDePlan . ") 
		AND ( socio_afectado=" . $this->getClaveDePersona() . ") AND ( docto_afectado=" . $this->getClaveDeCredito() . ")
		AND ( periodo_socio >= $inicio ) AND	(tipo_operacion=$tipo) ORDER BY periodo_socio";
		$rs				= getRecordset($sql1);
		while($rw	= mysql_fetch_array($rs)){
			$arrDev[$rw["periodo_socio"]]	= $rw["afectacion_real"];
		}
		return $arrDev;
	}
	function getPagosAtrasados(){ return $this->mPagosAtrasados; }
	function getMontoAtrasado(){ return $this->mMontoAtrasado; }
	function getFechaPrimerAtraso(){ return $this->mFechaPrimerAtraso; }
	/**
	 * @deprecated @since 2014.09.20
	 */	
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
	/**
	 * @deprecated @since 2014.09.20
	 */	
	function setSaldoInicial($cantidad){ $this->mSaldoInicial = $cantidad; }
	/**
	 * @deprecated @since 2014.09.20
	 */	
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
	function setEliminar(){$xRec	= new cReciboDeOperacion(false, true, $this->mNumeroDePlan);$xRec->init(); $xRec->setRevertir(true);$this->mMessages .= $xRec->getMessages(OUT_TXT); }
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
		if(count($this->mDatosInArray) > 1){
			$this->setClaveDePersona( $this->mDatosInArray["numero_socio"] );
			$this->setClaveDeCredito( $this->mDatosInArray["docto_afectado"] );
			if($this->mTasaDeIVA === false){
				$this->initByCredito($this->getClaveDeCredito() );
			}
			$this->mIsInit			= true;
		} else {
			$this->mIsInit			= false;
		}
		return $this->mIsInit;
	}
	function setClaveDePersona($persona){ $this->mClaveDePersona = $persona; $this->mSocio = $persona; }
	function setClaveDeCredito($credito){ $this->mClaveDeCredito = $credito; $this->mCredito	= $credito; }
	function getClaveDePersona(){ return $this->mClaveDePersona; }
	function getClaveDeCredito(){ return $this->mClaveDeCredito; }

	function setNumeroDeSolicitud($credito){ $this->setClaveDeCredito($credito); }
	function add($observaciones, $fecha_operacion = false){
		$fecha_operacion		= ($fecha_operacion == false) ? fechasys() : $fecha_operacion;
		$xRecN					= new cReciboDeOperacion(RECIBOS_TIPO_PLAN_DE_PAGO, true);
		$this->mNumeroDePlan	= $xRecN->setNuevoRecibo($this->mClaveDePersona, $this->mClaveDeCredito, $fecha_operacion, 0, false, $observaciones, DEFAULT_CHEQUE, TESORERIA_COBRO_NINGUNO,
				DEFAULT_RECIBO_FISCAL, $this->mClaveDeGrupo );
		$xRecN->setNumeroDeRecibo($this->mNumeroDePlan, true);
		$xRecN->setDefaultEstatusOperacion(OPERACION_ESTADO_GENERADO);
		$this->init($this->mNumeroDePlan);//, $xRecN->getDatosInArray());
		$this->mMessages			.= $xRecN->getMessages();
		$this->mORec				= $xRecN; unset($xRecN);
		return $this->mNumeroDePlan;
	}
	function setNeutralizarParcialidad($NumeroDeParcialidad = 0){
		$msg	= "";
		/*						AND ( socio_afectado=" . $this->mSocio . ") 
						AND ( docto_afectado=" . $this->mCredito . ")*/
		if ( setNoMenorQueCero($NumeroDeParcialidad) > 0){
			$sql1 = "UPDATE operaciones_mvtos 
				    SET afectacion_real=0
				    WHERE 
						(recibo_afectado = " . $this->mNumeroDePlan . ") 
						AND ( periodo_socio= $NumeroDeParcialidad )
						AND
						( (tipo_operacion=" . OPERACION_CLAVE_PLAN_CAPITAL . ") 
						OR
						(tipo_operacion=" . OPERACION_CLAVE_PLAN_INTERES . ")
						OR
						(tipo_operacion=" . OPERACION_CLAVE_PLAN_AHORRO . ") 
						OR
						(tipo_operacion=" . OPERACION_CLAVE_PLAN_IVA . ") ) ";
			$xEs	= my_query($sql1);
			$msg	.= $xEs["info"];
		}
		$this->mMessages	.= $msg . "\r\n";
		return $msg;
	}
	function setActualizarParcialidad($NumeroDeParcialidad, $capital = false, $interes = false, $ahorro = false,
					  $OtroClave = false, $OtroMonto = false){
		$msg	= "";
		$BWhere		= "(recibo_afectado = " . $this->mNumeroDePlan . ") AND ( periodo_socio= $NumeroDeParcialidad ) AND ";
		//AND ( socio_afectado=" . $this->mSocio . ") AND ( docto_afectado=" . $this->mCredito . ")
		if ( setNoMenorQueCero($NumeroDeParcialidad) > 0){
			if($capital !== false ){
			$sql1 = "UPDATE operaciones_mvtos SET afectacion_real=$capital
				    WHERE $BWhere  (tipo_operacion=" . OPERACION_CLAVE_PLAN_CAPITAL . ") ";
			setLog($sql1);
				$xEs	= my_query($sql1);
				$msg	.= $xEs[SYS_INFO];
				//Actualizar credito
				//TODO: Actualizar a tolerancia de Saldos
				if($interes <= 0.01 && $capital <= 0.01){
					//Obtener datos de la parcialidad
					$fechaV	= $this->getParcialidadFechaDePago($NumeroDeParcialidad);
					$sqlMA	= "UPDATE creditos_solicitud SET ultimo_periodo_afectado=$NumeroDeParcialidad,
					fecha_ultimo_mvto = '$fechaV'
					WHERE numero_solicitud=" . $this->mCredito . "
						AND
						numero_socio = " . $this->mSocio . "
					";
					my_query($sqlMA);
				}
			}
			if($interes !== false ){
				$tasa_iva	= $this->mTasaDeIVA;
				$iva		= $interes	* $tasa_iva;
				$sql1 	= "UPDATE operaciones_mvtos SET afectacion_real=$interes  WHERE $BWhere (tipo_operacion=" . OPERACION_CLAVE_PLAN_INTERES . ") ";
				$xEs	= my_query($sql1);	$msg	.= $xEs[SYS_INFO];
				//if($iva > 0.001){		
				//actualizar IVA
				$sql1 	= "UPDATE operaciones_mvtos  SET afectacion_real=$iva  WHERE $BWhere (tipo_operacion=" . OPERACION_CLAVE_PLAN_IVA . ") ";
				$xEs	= my_query($sql1);	$msg	.= $xEs[SYS_INFO];
				//}				
			}
			if($ahorro !== false){
				$sql1 = "UPDATE operaciones_mvtos SET afectacion_real=$ahorro WHERE $BWhere (tipo_operacion=" . OPERACION_CLAVE_PLAN_AHORRO . ") ";
				$xEs	= my_query($sql1);	$msg	.= $xEs[SYS_INFO];				
			}
			if($OtroClave !== false AND $OtroMonto !== false){
			$sql1 = "UPDATE operaciones_mvtos SET afectacion_real=$OtroMonto WHERE $BWhere (tipo_operacion=$OtroClave) ";
				$xEs	= my_query($sql1); $msg	.= $xEs[SYS_INFO];
			}
		}
		$this->mMessages	.= $msg . "\r\n";
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
		$xLetra													= new cParcialidadDeCredito($this->getClaveDePersona(), $this->getClaveDeCredito(), $numero);
		//setLog($this->getClaveDePersona());
		if(isset($this->mPagos[$numero])){
			$xLetra->setDatos($this->mPagos[$numero]);
		}
		$xLetra->init($this->getClaveDePersona(), $this->getClaveDeCredito(), $numero);
		$this->mPagos[$numero][SYS_INTERES_NORMAL] 				= $xLetra->getInteres();
		$this->mPagos[$numero][SYS_CAPITAL] 						= $xLetra->getCapital();
		$this->mPagos[$numero][SYS_TOTAL] 							= $xLetra->getMonto();
		$this->mPagos[$numero][SYS_FECHA]							= $xLetra->getFechaDePago();
		$this->mPagos[$numero][SYS_FECHA_VENCIMIENTO]				= $xLetra->getFechaDeVencimiento();
		
		$this->mPagos[$numero][SYS_DATOS]							= $xLetra->getDatosInArray();
		$this->mPagos[$numero][SYS_DATOS][SYS_INTERES_NORMAL]	= $this->mPagos[$numero][SYS_INTERES_NORMAL];
		$this->mPagos[$numero][SYS_DATOS][SYS_CAPITAL]			= $this->mPagos[$numero][SYS_CAPITAL];
		$this->mPagos[$numero][SYS_DATOS][SYS_TOTAL]				= $this->mPagos[$numero][SYS_TOTAL];
		$this->mPagos[$numero][SYS_DATOS][SYS_FECHA]				= $this->mPagos[$numero][SYS_FECHA];
		$this->mPagos[$numero][SYS_DATOS][SYS_FECHA_VENCIMIENTO]= $this->mPagos[$numero][SYS_FECHA_VENCIMIENTO];
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
	function addMvtoDeIVA($fecha, $letra){
		$monto	= round(($this->mBaseIVAOperado * $this->mTasaDeIVA), 2);
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
			if($monto != 0){
				$operacion 		= $this->mORec->setNuevoMvto($fecha_operacion, $monto, $tipo, $letra, "", $afectacion, false, false, false, $fecha_de_pago, $vencimiento, $this->mSaldoInicial, $this->mSaldoFinal);
			}
		}
		return ( setNoMenorQueCero($operacion) == 0) ? false : $operacion;
	}
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

}

class cProductoDeCredito {
	private $mClaveDeConvenio	= 99;
	private $mArrDatos			= array();
	private $mArrDatoExtras		= array();
	private $mInitDExtras		= false;
	private $mTasaAhorro		= 0;
	private $mOB				= null;
	private $mTipoEnSistema		= false;
	private $mTipoDeIntegracion	= 1; //1 Individual 2 copropiedad 3 Grupo
	private $mObjOD				= null;
	private $mInit				= false;
	private $mFormaDePagare		= null;
	private $mFormaDeMandato	= null;
	private $mTasaComPorApertura	= 0;
	private  $mMessages				= "";
	function __construct($mClaveDeConvenio = false){ $this->mClaveDeConvenio	= setNoMenorQueCero($mClaveDeConvenio); }
	function init(){
		$cT							= new cTipos();
		$code						= $this->mClaveDeConvenio;
		$sql						= "SELECT * FROM creditos_tipoconvenio WHERE `idcreditos_tipoconvenio` = $code LIMIT 0,1";
		$this->mArrDatos			= obten_filas($sql);
		$this->mTasaAhorro			= $cT->cFloat($this->mArrDatos["tasa_ahorro"]);
		$this->mOB					= new cCreditos_tipoconvenio();
		$this->mOB->setData($this->mArrDatos);
		$this->mTipoEnSistema		= $this->mOB->tipo_en_sistema()->v();
		$this->mTipoDeIntegracion	= $this->mOB->tipo_de_integracion()->v();
		$this->initOtrosDatos();
	}
	function get(){ return $this->mClaveDeConvenio;	}
	function getDatosInArray(){ $this->init(); return $this->mArrDatos;}
	function getTasaDeAhorro(){ return $this->mTasaAhorro; }
	function getOtrosParametrosInArray(){
		$xTab		= new cSAFETabla(TCREDITOS_PRODUCTOS_OTROS_PARAMETROS);
		$xTab->setWhere("clave_del_producto=" . $this->mClaveDeConvenio);
		$sql		= $xTab->getSelect();
		$rs			= getRecordset($sql);
		while( $rw 	= mysql_fetch_array($rs) ){
			$this->mArrDatoExtras[ strtoupper($rw["clave_del_parametro"]) ] = $rw["valor_del_parametro"];
			
		}
		$this->mInitDExtras		= true;
		return $this->mArrDatoExtras;
	}
	function getOtrosParametros($Parametro){
		if($this->mInitDExtras == false){ $this->getOtrosParametrosInArray(); }
		$Parametro				= strtoupper($Parametro);
		return isset($this->mArrDatoExtras[$Parametro]) ? $this->mArrDatoExtras[$Parametro] : "";
	}
	function setOtrosParametros($Parametro, $Valor, $FechaDeExpiracion = false){
		$xDb				= new cSAFETabla(TCREDITOS_PRODUCTOS_OTROS_PARAMETROS);
		$dpt				= $this->mClaveDeConvenio;
		$f				= fechasys();
		$FechaDeExpiracion	= ($FechaDeExpiracion == false) ? '2029-01-01' : $FechaDeExpiracion;
		//idcreditos_productos_otros_parametros, clave_del_producto, clave_del_parametro, valor_del_parametro, fecha_de_alta, fecha_de_expiracion
		$insert		= $xDb->getInsert("$dpt, '$Parametro', '$Valor', '$f', '$FechaDeExpiracion' ", $xDb->getCamposSinClaveUnica());
		my_query($insert);	
	}
	function obj(){
		if( $this->mOB == null){
			$this->mOB	= new cCreditos_tipoconvenio();
			$this->mOB->setData( $this->mOB->query()->initByID($this->mClaveDeConvenio) );
		}
		return $this->mOB;	
	}
	function getAplicaMoraPorGastos(){ return $this->obj()->aplica_mora_por_cobranza()->v();	}
	function getDiasTolerados(){ $dias = $this->obj()->tolerancia_dias_no_pago()->v(); return ($dias == 0)? 1 : $dias; }
	function getTasaIncluyeIVA(){ return (intval($this->obj()->iva_incluido()->v()) == 1)? true : false; }
	function getTipoDeIntegracion(){ return intval($this->obj()->tipo_de_integracion()->v()); }
	function getTipoDeContratoCR(){ return $this->obj()->clave_de_tipo_de_producto()->v(); }
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
	function getPreModInteres(){
		return $this->obj()->pre_modificador_de_interes()->v(OUT_TXT);
	}
	function getPosModInteres(){
		return $this->mOB->pos_modificador_de_interes()->v(OUT_TXT);
	}
	function getNumeroPagosPreferente(){
		$pagos	=  setNoMenorQueCero( $this->obj()->numero_de_pagos_preferente()->v());
		$pagos	= ($pagos <= 0) ? $this->getPagosMinimo() : $pagos; 
		return $pagos;
	 }
	function getPeriocidadPrefente(){ return $this->obj()->tipo_de_periocidad_preferente()->v(); }
	function getPagosMaximos(){ return $this->obj()->pagos_maximo()->v(); }
	function getPagosMinimo(){ return ($this->getPeriocidadPrefente() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ) ? 2 : 1; }
	function getEsProductoDeNomina(){
		$res	= false;
		if($this->mTipoEnSistema == CREDITO_PRODUCTO_NOMINA){ $res	= true; }
		return $res;
	}
	function getEsProductoDeGrupos(){
		$res	= false;
		if($this->mTipoEnSistema == CREDITO_PRODUCTO_GRUPOS OR $this->mTipoDeIntegracion > 2){ $res	= true; }
		return $res;		
	}
	function getTipoEnSistema(){ return $this->mTipoEnSistema; }
	function getTasaDeInteres(){
		//eval( $this->getPreModInteres() );
		$TASA_NORMAL	= $this->obj()->interes_normal()->v();
		//eval( $this->getPosModInteres() );
		return $TASA_NORMAL;
	}
	function initOtrosDatos(){
		if($this->mObjOD == null){ $this->mObjOD = new cProductoDeCreditoOtrosDatosCatalogo($this->mClaveDeConvenio); }
		$this->mFormaDePagare		= $this->mObjOD->get($this->mObjOD->CLAVE_DE_PAGARE);
		$this->mFormaDeMandato		= $this->mObjOD->get($this->mObjOD->CLAVE_DE_MANDATO);
		$this->mTasaComPorApertura	= setNoMenorQueCero($this->mObjOD->get($this->mObjOD->TASA_DE_COMISION_AP));
		if($this->mTasaComPorApertura > 0){ $this->mTasaComPorApertura = $this->mTasaComPorApertura / 100; }
		return $this->mObjOD; 
	}
	function getTasaComisionApertura(){ $this->initOtrosDatos(); return $this->mTasaComPorApertura; }
	function getOficialDeSeguimiento(){ return $this->obj()->oficial_seguimiento()->v(); }
	function getTasaDeGarantiaLiquida(){ return $this->obj()->porciento_garantia_liquida()->v(); }
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
}
class cProductoDeCreditoOtrosDatosCatalogo {
	public $SIC_TIPO_DE_RESPONSABILIDAD = "SIC_TIPO_DE_RESPONSABILIDAD";
	public $SIC_TIPO_DE_CUENTA			= "SIC_TIPO_DE_CUENTA";
	public $SIC_TIPO_DE_CONTRATO		= "SIC_TIPO_DE_CONTRATO";
	public $CLAVE_DE_CONTRATO			= "CLAVE_DE_CONTRATO";
	public $CLAVE_DE_MANDATO			= "CLAVE_DE_MANDATO";
	public $CLAVE_DE_PAGARE				= "CLAVE_DE_PAGARE";
	public $TASA_DE_COMISION_AP			= "TASA_DE_COMISION_POR_APERTURA";
	public $REQUIERE_PERFIL_AML			= "REQUIERE_PERFIL_AML";
	
	public $CONTRATO_ID_LEGAL			= "CONTRATO_ID_LEGAL";
	

	private $mDatos						= array();
	private $mProducto					= false;
	function __construct($producto = false){
		$this->mProducto	= setNoMenorQueCero($producto);
		$sql				= "SELECT * FROM creditos_productos_otros_parametros WHERE `clave_del_producto` = " . $this->mProducto . " ";
		$ql					= new MQL();
		$rs					= $ql->getDataRecord($sql);
		
		foreach ($rs as $datos){
			$idx			= strtoupper($datos["clave_del_parametro"]); 
			$this->mDatos[$idx] = $datos["valor_del_parametro"];
		}
	}
	function set($valor, $parametro){
		$parametro	= strtoupper($parametro);
		//, fecha_de_alta='2014-5-23', fecha_de_expiracion='2014-5-23'
		$sql	= "UPDATE creditos_productos_otros_parametros SET  valor_del_parametro='$valor' WHERE clave_del_producto=" . $this->mProducto . " AND clave_del_parametro='$parametro' ";
		$ql		= new MQL(); $ql->setRawQuery($sql);
	}
	function get($parametro){
		$dato	= null;
		
		if(isset($this->mDatos[$parametro])){ $dato	= $this->mDatos[$parametro]; }
		return $dato;
	}
}
class cPeriocidadDePago {
	private $mClave			= 99;
	private $mArrDatos		= array();
	private $mOb			= null;
	function __construct($clave){
		$this->mClave	= $clave;
	}
	function getDatosInArray(){
		$this->init();
		return $this->mArrDatos;
	}
	function init(){
		$cT					= new cTipos();
		$code				= $this->mClave;
		$sql				= "SELECT * FROM creditos_periocidadpagos WHERE `idcreditos_periocidadpagos` = $code LIMIT 0,1"; //XXX: Error en el cierre de dia
		$this->mArrDatos	= obten_filas($sql);
		$xPer				= new cCreditos_periocidadpagos();
		$xPer->setData( $this->mArrDatos );
		$this->mOb			= $xPer;
	}
	function getNombre(){
		return $this->mArrDatos["descripcion_periocidadpagos"];
	}
	function getDiasToleradosEnVencer(){
		return $this->mOb->tolerancia_en_dias_para_vencimiento()->v();
	}
}


class cParcialidadDeCredito {
	private $mPersona		= false;
	private $mCredito		= false;
	private $mNumero		= false;
	
	private $mCapital		= 0;
	private $mInteres		= 0;
	private $mMoratorio		= 0;
	
	private $mCapitalPagado		= 0;
	private $mInteresPagado		= 0;
	private $mMoratorioPagado	= 0;
	private $mOtrosPagado		= 0;
		
	private $mOB			= null;
	private $mDatosInArray	= array();
	protected $mMessages	= "";
	function setDatos($aDatos){
		$this->mDatosInArray	= $aDatos;
	}
	function __construct($persona = false, $credito = false, $numero = false){
		$persona				= setNoMenorQueCero($persona);
		$credito				= setNoMenorQueCero($credito);
		$numero					= setNoMenorQueCero($numero);
		$this->mPersona			= $persona;
		$this->mCredito			= $credito;
		$this->mNumero			= $numero;
	}
	function init($persona = false, $credito = false, $numero = false){
		//TODO: Verificar cambios de incencias 2014-10-01
		$persona				= setNoMenorQueCero($persona);
		$credito				= setNoMenorQueCero($credito);
		$numero					= setNoMenorQueCero($numero);
		$this->mPersona			= ($persona > 0) ? $persona : $this->mPersona;
		$this->mCredito			= ($credito > 0) ? $credito : $this->mCredito;
		$this->mNumero			= ($numero > 0) ? $numero : $this->mNumero;
		$sql					= "SELECT * FROM letras WHERE socio_afectado=$persona AND docto_afectado=$credito AND periodo_socio=$numero LIMIT 0,1";
		$init					= true;
		if(count($this->mDatosInArray) > 2){
			$Datos					= $this->mDatosInArray;
		} else {
			$Datos					= obten_filas($sql);
			if(!isset($Datos["letra"])){ $init = false; }
		}
		
		$this->mOB				= new cLetrasVista();
		$this->mOB->setData($Datos);
		$this->mDatosInArray	= $Datos;
		//$fecha					= $this->mOB->fecha_de_pago()->v();
		//$this->mMessages		.= "[FECHA_DE_PAGO : $fecha]\r\n";
		return $init;
	}
	function getDatosInArray(){
		return $this->mDatosInArray;
	}
	function getMonto(){
		return $this->mOB->letra()->v();
	}
	function getCapital(){
		return $this->mOB->capital()->v();
	}
	function getInteres(){
		return $this->mOB->interes()->v();
	}
	function getOtros(){
		return $this->mOB->otros()->v();
	}
	function getFechaDePago(){
		return $this->mOB->fecha_de_pago()->v();
	}
	function getFechaDeVencimiento(){
		return $this->mOB->fecha_de_vencimiento()->v();
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function initPago(){
		$periodo	= $this->mNumero;
	}
}

class cCreditosGarantias {
	private $mCodigo		= false;
	private $mClavePersona	= false;
	private $mClaveCredito	= false;
	protected $mMessages	= "";
	private $mOb			= null;
	function __construct($id = false){
		$this->mCodigo		= $id;
	}
	function getClaveDePersona(){ return $this->mClavePersona; }
	function getClaveDeCredito(){ return $this->mClaveCredito; }
	function init($aDIniciales = false){
		if($this->mCodigo != false){
			$xOb	= new cCreditos_garantias();
			$data	= ($aDIniciales == false) ? $xOb->query()->initByID($id) : $aDIniciales;
			$xOb->setData( $data );
			$this->mClaveCredito	= $xOb->solicitud_garantia()->v();
			$this->mClavePersona	= $xOb->socio_garantia()->v();
			$this->mOb				= $xOb;
		}
	}
	function setClaveDePersona($persona){ $this->mClavePersona	= $persona;	}
	function setClaveDeCredito($credito){ $this->mClaveCredito	= $credito;	}
	function add($tipo, $tipo_de_valuacion = false, $valor = 0, $persona_propietaria = false, $nombre_del_propietario = "", $fecha_de_adquisicion = false, 
			$documento_presentado = "", $estado_fisico = false, $descripcion = "", $observaciones = "", $fecha_actual = false){
		$fecha_actual		= ($fecha_actual == false) ? fechasys() : $fecha_actual; 
		$persona_propietaria= ($persona_propietaria == false) ? DEFAULT_SOCIO : $persona_propietaria;
		$tipo_de_valuacion	= ($tipo_de_valuacion == false) ? FALLBACK_CRED_GARANTIAS_TVALUACION : $tipo_de_valuacion;
			
		$xGar				= new cCreditos_garantias();
		$xGar->idcreditos_garantias( $xGar->query()->getLastID() );
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
		$q		= $xGar->query()->insert();
		$id		= $q->save();
		$this->mMessages	.= $q->getMessages(OUT_TXT);
		
		return $id;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
}





class cCreditosOtrosDatos {
	public $DEPOSITO_BANCO				= "TRANSFERENCIA_BANCO";
	public $DEPOSITO_CTA_BANCARIA		= "TRANSFERENCIA_CTA_BANCARIA";
	public $DEPOSITO_CLABE_BANCARIA		= "TRANSFERENCIA_CLABE";
	public $AML_CON_PROVEEDOR			= "AML_CON_PROVEEDOR";
	public $AML_CON_PROPIETARIO			= "AML_CON_PROPIETARIO";
		
}

class cCreditosProceso {
	public $AUTORIZACION	= "AUTORIZACION_DE_CREDITO";
	public $MINISTRACION	= "DESEMBOLSO_DE_CREDITO";
}


?>