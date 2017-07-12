<?php
/**
 * Core de Operaciones
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package operaciones
 *  Core Operaciones File
 * 		16/05/2008
 * 		Se agrego el Numero de recibo
 * 		9/oct/2010.- Se Mejora la Ficha
 */
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.creditos.inc.php");
include_once("core.captacion.inc.php");
include_once("core.fechas.inc.php");

include_once("core.aml.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");

@include_once("../libs/sql.inc.php");
@include_once("../libs/FacturacionModerna.php");		//facturacion

//=====================================================================================================
class cReciboDeOperacion{
	private $mCodigoDeRecibo 		= 0;
	private $mTipoDeRecibo			= false;

	private $mSumaDeRecibo			= 0;
	private $mNumeroDeMvtos			= 0;
	private $mFechaDeOperacion		= false;
	private $mSetGenerarPoliza		= false;
	private $mSetGenerarTesoreria	= false;
	private $mSetGenerarBancos		= false;
	private $mTotalRecibo			= 0;
	
	private $mDefMvtoStatus			= 30;
	private $mAfectar				= true;
	private $mReciboEnHtml			= "";
	private $mMvtosEnHtml			= "";
	
	private $mSocio					= false;
	private $mDocto					= false;
	private $mUsuario				= false;
	
	private $mTipoDePago			= false;
	private $mGrupoAsociado			= false;
	//private $mPersonaAsociada			= 0;
	private $mCuentaBancaria		= false;
	private $mObservaciones			= "";
	private $mPathToFormato			= "";
	private $mOrigen				= "";
	private $mIndiceOrigen			= 99;
	
	private $mTipoDescripcion		= "";
	private $mAplicadoA				= "otros";
	private $mDatosByArray			= array();
	private $mMoneda				= "MXN";
	private $mUnidadesOriginales	= 0;

	private $mForceUpdateSaldos		= false;

	private $TxtLog					= "";
	private $mMessages				= "";
	private $mNumeroCheque			= "NA";

	private $mFechaDeVcto			= false;
	private $mReciboIniciado		= false;
	private $mUnicoDocto			= false;
	private $mReciboFiscal			= ""; 
	private $mOTesoreria			= null; 
	private $mOCaja					= null;
	private $mOCuentaBancaria		= null;
	private $mPeriodoActivo			= null;			//Parcialidad o periodo del recibo
	private $mOPersona				= null;
	private $mOUsuario				= null;			//Usuario
	private $mOTipoRecibo			= null;
	private $mTipoOrigenAML			= 0;
	private $mClavePersonAsoc		= false;
	private $mOFactura				= null;
	private $mFechaDeCaptura		= false;
	private $mForceCeros			= false;
	private $mSucursal				= "";
	private $mIDCache				= "";
	private $mSaldoHistorico		= 0;
	private $mAfectaEnCaja			= false;
	
	function __construct($tipo = false, $solo_por_docto	= false, $recibo = false){
		if ($tipo == false){
			$tipo				= FALLBACK_TIPO_DE_RECIBO;
		}
		$this->mTipoDeRecibo	= $tipo;
		$recibo					= setNoMenorQueCero($recibo);
		if ( $recibo > 0 ){
			$this->mCodigoDeRecibo			= $recibo;
			$this->setNumeroDeRecibo($this->mCodigoDeRecibo, true);
			$this->setIDcache($recibo);
		}
		$this->mFechaDeVcto					= fechasys();
		$this->mUnicoDocto					= $solo_por_docto;
		$this->mMoneda						= AML_CLAVE_MONEDA_LOCAL;
		$this->mClavePersonAsoc				= DEFAULT_SOCIO;
		$this->mSucursal					= getSucursal();
	}
	function setSocio($socio){ $this->mSocio   = $socio; }
	function setIDcache($clave){ if($clave>0){ $this->mIDCache = TOPERACIONES_RECIBOS . "-" . $clave; } }
	function setCleanCache(){ 
		$xCache = new cCache();
		if($this->mIDCache != ""){
			$xCache->clean($this->mIDCache);
		}
	}
	function setDocumento($docto){ 			$this->mDocto   = $docto; }
	function setGenerarPoliza($By = true){ 		$this->mSetGenerarPoliza	= $By;	}
	function setForceUpdateSaldos($force = true){ 	$this->mForceUpdateSaldos	= $force; }
	function setGenerarTesoreria($By = true){ 	$this->mSetGenerarTesoreria		= $By; }
	function setGenerarBancos($By = true){ 		$this->mSetGenerarBancos	= $By;	}
	function setAfectable($afectar = false){ 	$this->mAfectar = $afectar; }
	/**
	 * Funcion que Inicia los Datos del recibo de forma opcional
	 * @param int $codigo_de_recibo
	 * @param boolean $inicializar
	 * @param array	$arrInicial
	 */
	function setNumeroDeRecibo($codigo_de_recibo, $inicializar = false, $arrInicial = false){
		if($codigo_de_recibo != $this->mCodigoDeRecibo){ $this->mMessages	.= "OK\tSe Inicializa el Recibo Num $codigo_de_recibo. Antes " . $this->mCodigoDeRecibo . "\r\n"; }
		$this->mCodigoDeRecibo	= $codigo_de_recibo;
		if ( $inicializar == true ){ $this->init(); }
	}
	/**
	 * Inicializa el Recibo
	 * @param array $arrInicial
	 */
	function initRecibo($arrInicial = false){ return $this->init($arrInicial);	}
	function getFechaDeRecibo(){ return $this->mFechaDeOperacion; }
	function getFechaDeCaptura(){ return $this->mFechaDeCaptura; }
	function getTipoDeRecibo(){ return $this->mTipoDeRecibo; }
	function getTipoDePago(){ return  $this->mTipoDePago; }
	function getNumeroDeCheque(){ return $this->mNumeroCheque; }
	function getObservaciones(){ return $this->mObservaciones; }
	function getMoneda(){  return $this->mMoneda; }
	function getReciboFiscal(){ return $this->mReciboFiscal; }
	/**
	 * Funcion que inicializa el Recibo
	 * @param array $arrInicial
	 * @return unknown_type
	 */
	function init($arrInicial = false){
			$DR			= array();
			$recibo		= $this->mCodigoDeRecibo;
			$xCache		= new cCache();
			if($arrInicial != false AND is_array($arrInicial)){
				$DR		= $arrInicial;
			} else {
				$DR		= $xCache->get($this->mIDCache);
				if(!is_array($DR)){
					$xLi	= new cSQLListas();
					$DR		= obten_filas($xLi->getInicialDeRecibos($this->mCodigoDeRecibo, $this->mSocio));
				}
			}
			$ORec							= new cOperaciones_recibos();
			
			if( isset($DR["tipo_docto"])){
				$ORec->setData($DR);
				$this->mTipoDeRecibo		= $DR["tipo_docto"];
				$this->mTipoDePago			= $DR["tipo_pago"];
				$this->mGrupoAsociado		= $DR["grupo_asociado"];
				$this->mSocio				= $DR["numero_socio"];
				$this->mDocto				= $DR["docto_afectado"];
				$this->mNumeroCheque		= $DR["cheque_afectador"];
				$this->mFechaDeOperacion	= $DR["fecha_operacion"];
				$this->mObservaciones		= $DR["observacion_recibo"];
				$this->mPathToFormato		= $DR["path_formato"];
				$this->mTipoDescripcion		= $DR["descripcion_recibostipo"];
				$this->mAplicadoA			= $DR["origen"];
				$this->mReciboFiscal		= $DR["recibo_fiscal"];
				$this->mTotalRecibo			= $DR["total_operacion"];
				
				$this->mPathToFormato		= $DR["path_formato"];
				$this->mOrigen				= $DR["origen"];
				$this->mIndiceOrigen		= $DR["indice_origen"];
					
				$this->mDatosByArray		= $DR;
				$this->mFechaDeVcto			= $this->mFechaDeOperacion;
				$this->mUsuario				= $ORec->idusuario()->v();
				$this->mMoneda				=  strtoupper( $ORec->clave_de_moneda()->v() );
				$this->mUnidadesOriginales	=  strtoupper( $ORec->unidades_en_moneda()->v() );
				$this->mTipoOrigenAML		= $ORec->origen_aml()->v();
				$this->mClavePersonAsoc		= $ORec->persona_asociada()->v();
				$this->mPeriodoActivo		= $ORec->periodo_de_documento()->v();
				$this->mFechaDeCaptura		= $ORec->fecha_de_registro()->v();
				$this->mSucursal			= $ORec->sucursal()->v();
				$this->mCuentaBancaria		= $ORec->cuenta_bancaria()->v();
				$this->mSaldoHistorico		= $ORec->montohist()->v();
				$this->setIDcache($ORec->idoperaciones_recibos()->v());
				$xCache->set($this->mIDCache, $DR);
				unset($DR);
				$this->mReciboIniciado		= true;
			} else {
				$this->mMessages			.= "ERROR\tRecibo no encontrado # $recibo\r\n";
			}
			return $this->mReciboIniciado;
	}
	function setGrupoAsociado($grupo){ $this->mGrupoAsociado	= $grupo; }
	function setForzarCeros($forzar = true){ $this->mForceCeros = $forzar; }
	/**
	 * @param integer $socio
	 * @param integer $documento
	 * @param string $fecha
	 * @param integer $parcialidad
	 * @param integer $Tipo
	 * @param string $cadena
	 * @param string $cheque_afectador
	 * @param string $TipoPago
	 * @param string $recibo_fiscal
	 * @param integer $grupo
	 * @param integer $cuenta_bancaria
	 * @param float $moneda
	 * @param number $unidades
	 * @param integer $persona_asociada
	 * @param integer $periodo
	 * @return integer
	 */
	function setNuevoRecibo($socio, $documento, $fecha, $parcialidad,
						$Tipo = false, $cadena = "", $cheque_afectador = "NA",
						$TipoPago = "", $recibo_fiscal = "", $grupo = false,
						$cuenta_bancaria = false, $moneda = "", $unidades = 0, $persona_asociada = false, $periodo = false){
		$ql					= new MQL();
		$xF					= new cFecha();
		//----------------------------------DATOS DEL RECIBO------------------------
		$Tipo				= setNoMenorQueCero($Tipo);
		$Tipo				= ($Tipo<= 0) ? $this->mTipoDeRecibo : $Tipo;
		$TipoPago			= trim(strtolower($TipoPago));
		$TipoPago			= ($TipoPago == "") ? SYS_NINGUNO : $TipoPago;
		$fecha				= $xF->getFechaISO($fecha);
		$total_operacion 	= 0;					// Total Operacion
		$observaciones 		= $cadena;
		$indice_origen 		= 99;					// Indice de Origen
		$grupo				= setNoMenorQueCero($grupo);
		$grupo				= ($grupo <= 0) ? DEFAULT_GRUPO : $grupo;
		$moneda				= setCadenaVal($moneda);
		$moneda				= ($moneda == "") ? $this->mMoneda : $moneda;
		$unidades			= setNoMenorQueCero($unidades);
		$unidades			= ($unidades == 0) ? $this->mUnidadesOriginales : $unidades;
		$persona_asociada	= ($persona_asociada == false) ? $this->mClavePersonAsoc : $persona_asociada;
		$persona_asociada	= setNoMenorQueCero($persona_asociada);
		$periodo			= setNoMenorQueCero($periodo);
		$parcialidad		= setNoMenorQueCero($parcialidad);
		$periodo			= ($periodo <= 0 AND $parcialidad >0) ? $parcialidad : $periodo;
		//si es pago o ministracion
		if($Tipo == RECIBOS_TIPO_PAGO_CREDITO AND $persona_asociada <= DEFAULT_SOCIO){
			$xCred			= new cCredito($documento);
			if($xCred->init() == true){
				$persona_asociada		= $xCred->getClaveDeEmpresa();
			}
		}
		if($persona_asociada <= DEFAULT_SOCIO ){
			if($socio <= DEFAULT_SOCIO){
				$persona_asociada	= DEFAULT_EMPRESA;
			} else {
				$xSoc		= new cSocio($socio);
				if($xSoc->init() == true){
					$persona_asociada	 = $xSoc->getClaveDeEmpresa();
				}
			}
		}
		//
		$cuenta_bancaria	= setNoMenorQueCero($cuenta_bancaria);
		if($cuenta_bancaria > 0){
			$this->setCuentaBancaria($cuenta_bancaria);
		}
		// DATOS GENERALES
		$idrecibo			= getFolio(iDE_RECIBO);
		//Verificar la Cuenta Bancaria

		if ( !isset($socio) ){	$socio	= $this->mSocio; }
		$iduser 			= getUsuarioActual();
		//$xDTab				= new cSQLTabla(TOPERACIONES_RECIBOS);
	//----------------------------------------------------------------------------- clave_de_moneda, unidades_en_moneda
		$xRec				= new cOperaciones_recibos();
		$xRec->archivo_fisico("");
		$xRec->cadena_distributiva($cadena);
		$xRec->cheque_afectador($cheque_afectador);
		$xRec->clave_de_moneda($moneda);
		$xRec->docto_afectado($documento);
		$xRec->eacp(EACP_CLAVE);
		$xRec->fecha_de_registro(fechasys());
		$xRec->fecha_operacion($fecha);
		$xRec->grupo_asociado($grupo);
		$xRec->idoperaciones_recibos($idrecibo);
		$xRec->idusuario($iduser);
		$xRec->indice_origen($indice_origen);
		$xRec->numero_socio($socio);
		$xRec->observacion_recibo($observaciones);
		$xRec->origen_aml(0);
		$xRec->persona_asociada($persona_asociada);	//empresa
		$xRec->recibo_fiscal($recibo_fiscal);
		$xRec->sucursal(getSucursal());
		$xRec->tipo_docto($Tipo);
		$xRec->total_operacion($total_operacion);
		$xRec->unidades_en_moneda($unidades);
		$xRec->tipo_pago($TipoPago);
		$xRec->periodo_de_documento($periodo);
		$xRec->cuenta_bancaria($cuenta_bancaria);
		
		if ($this->mAfectar == true){
			$xsr = $xRec->query()->insert()->save();// my_query($sql_i_rec);
			if ( $xsr !== false){
				$this->mMessages	.= "OK\tSe agrego exitosamente el Recibo : $idrecibo : del Documento $documento\r\n";
				$this->mCodigoDeRecibo	= $idrecibo;
				$this->setNumeroDeRecibo($idrecibo);
				if($this->init() == false){
					$this->mMessages	.= "ERROR\tEl Recibo : $idrecibo : del Documento $documento No inicia\r\n";
				}
			} else {
				$this->mMessages	.= "ERROR\tError al Agregar el Recibo $idrecibo del Documento $documento \r\n";
				$idrecibo			= false;
			}
		} else {
			$this->mMessages	.= "WARN\tNo se agrega el Recibo por que es NO AFECTABLE \r\n";
		}
		
		return $idrecibo;
	}
	function setCuentaBancaria($cuenta){ $this->mCuentaBancaria = $cuenta; }
	function getPersonaAsociada(){ return $this->mClavePersonAsoc; }
	function addMovimiento($tipo, $monto, $periodo =1, $observaciones = "", $afectacion =1,$persona = false, $documento = false, $fecha = false, $SaldoInicial = 0, $SaldoFinal=0){
		$xF			= new cFecha();
		$fecha		= $xF->getFechaISO($fecha);
		$persona	= setNoMenorQueCero($persona);
		$persona	= ($persona <= DEFAULT_SOCIO) ? $this->mSocio : $persona;
		$documento	= ($documento <= 0) ? $this->mDocto : $documento;
		$documento	= setNoMenorQueCero($documento);
		$xFols		= new cFolios();
		//$folio	= $xFols->getClaveDeOperaciones();
		//$xOp		= new cOperaciones_mvtos();
		//$xOp->afectacion_cobranza($monto);
		return $this->setNuevoMvto($fecha, $monto, $tipo, $periodo, $observaciones, $afectacion, false,$persona, $documento, $fecha, $fecha, $SaldoInicial, $SaldoFinal);
	}
	/**
	 * @return integer Numero de Operacion
	 **/
	function setNuevoMvto($fecha_operacion, $monto, $tipo_operacion, $periodo_de_socio, $observaciones, $signo_afectacion = 1, $mvto_contable = false,
						  $socio = false, $documento = false, $fecha_afectacion = false, $fecha_vencimiento = false, $saldo_anterior = false, $saldo_nuevo = false){
		$sucess		= false;
		$socio		= setNoMenorQueCero($socio);
		$documento	= setNoMenorQueCero($documento);
		$xFols		= new cFolios();
		$xLog		= new cCoreLog();
		$xQL		= new MQL();
		$viable		= true;
		
		//inicializa el recibo
		if ( setNoMenorQueCero($this->mCodigoDeRecibo) <= 0 ){ $viable = false; }
		//Si no hay valores, obtenerlos del recibo
		if ( $documento <= 0 ){	$documento	= $this->mDocto; }
		if ( $socio <=0 ){	$socio		= $this->mSocio; }
		
		//Verificar la Cuenta Bancaria
		if ( setNoMenorQueCero($this->mCuentaBancaria) <= 0 ){	$this->mCuentaBancaria = DEFAULT_CUENTA_BANCARIA;	}
		$recibo			= $this->mCodigoDeRecibo;
		$fecha_afectacion	= ($fecha_afectacion == false) ? $fecha_operacion : $fecha_afectacion;
		// --------------------------------------- VALOR SQL DEL MVTO.-------------------------------------------------------
		$monto				= setNoMenorQueCero($monto,2);
		//
		$iduser 			= getUsuarioActual();
		$eacp 				= '';//EACP_CLAVE;
										// PERIODOS

		$periodo_de_socio	= setNoMenorQueCero($periodo_de_socio);						// periodo del Socio.
		$estatus_mvto 		= $this->mDefMvtoStatus;
		$fecha_vcto 		= ($fecha_vencimiento == false) ? $this->mFechaDeVcto : $fecha_vencimiento;
		$saldo_anterior		= ($saldo_anterior === false ) ? 0 : $saldo_anterior;
		$saldo_nuevo		= ($saldo_nuevo === false ) ? $monto : $saldo_nuevo;
		$sucursal 		= getSucursal();
		$idoperacion 	= 0;
		$tasa			= 0;
		$dias			= 0;
		$grupo			= $this->mGrupoAsociado;
		$observaciones	= substr($observaciones, 0, 40); //40 Max obervaciones
		$xT				= new cTipos(0);
		if ($this->mGrupoAsociado == false ){
			$grupo		= DEFAULT_GRUPO;
		}

		$arrD	= array( $socio, $documento, $recibo );
		$viable	= $xT->getEvalNotNull($arrD);
		if($monto === 0){
			$viable		= false;
			if($this->mForceCeros == true){
				$viable	= true;
				$monto	= setNoMenorQueCero($monto);
			}
		}
		if ( $viable == false ){
			$xLog->add("ERROR\tVARS\tError al Evaluar alguno de estos Valores Socio $socio, Documento $documento, Recibo $recibo Monto $monto\r\n");
			$xLog->add($xT->getMessages(), $xLog->DEVELOPER);
		}
		$idoperacion			= 0;
		if($this->mAfectar == true AND $viable == true){
			//Ejecutar Query
			$SQLM				= "INSERT INTO `operaciones_mvtos`
							(`idusuario`, `codigo_eacp`,
							`socio_afectado`, `docto_afectado`, `recibo_afectado`, `grupo_asociado`, `fecha_operacion`,`fecha_afectacion`, `fecha_vcto`,
							 `periodo_socio`,`saldo_anterior`,`saldo_actual`,
							`afectacion_real`,`afectacion_estadistica`, `valor_afectacion`, 
							`tipo_operacion`, `estatus_mvto`,  `detalles`,`sucursal`, `tasa_asociada`,`dias_asociados`) 
							VALUES ($iduser, '$eacp', 
							$socio, $documento, $recibo, $grupo, '$fecha_operacion', '$fecha_afectacion', '$fecha_vcto',
							$periodo_de_socio, $saldo_anterior, $saldo_nuevo,
							$monto, $monto, $signo_afectacion,
							$tipo_operacion, $estatus_mvto, '$observaciones', '$sucursal', $tasa, $dias)";
			
				
			//end Query
			
			$exec = $xQL->setRawQuery($SQLM);

			if ( $exec == false ){
				$sucess	= false;
				$xLog->add("ERROR\t$recibo\tSe Fallo al Agregar la Operacion($tipo_operacion) por $monto con Numero $idoperacion\r\n");
				setLog("ERROR\t$recibo\tSe Fallo al Agregar la Operacion($tipo_operacion) por $monto \r\n");
			} else {
				$sucess	= true;
				$idoperacion	= $xQL->getLastInsertID();
				$xLog->add("OK\t$recibo\tSe agrego Exitosamente la Operacion($tipo_operacion) por $monto con Numero $idoperacion \r\n", $xLog->DEVELOPER);
			}
			$exec		= null;
		} else {
			$xLog->add("WARN\tSe simula Agregar el Mvto $idoperacion del tipo $tipo_operacion por un monto de $monto \r\n", $xLog->DEVELOPER);
		}
		$this->mMessages		.= $xLog->getMessages();
		//Sumar al Recibo el Monto
		$this->mSumaDeRecibo	+= $monto;
		$this->mNumeroDeMvtos++;
		//
		return $idoperacion;
	}
	function addMvtoContable($tipo_operacion, $monto, $mvto_contable = false, $socio = false, $documento = false){
		if ( $this->mSetGenerarPoliza == true ){
			setPolizaProforma($this->mCodigoDeRecibo, $tipo_operacion, $monto, $this->mSocio, $this->mDocto, $mvto_contable);
		}
	}
	function addMvtoContableByTipoDePago($monto = false, $afectacion = false){
		if ($afectacion	== false ){
			$afectacion	= TM_CARGO;
		}
		$tipo_pago			= $this->mTipoDePago;
		$socio_de_recibo	= $this->mSocio;
		$codigo_de_recibo	= $this->mCodigoDeRecibo;
		$docto_de_recibo	= $this->mDocto;
		if( $tipo_pago == TESORERIA_COBRO_NINGUNO ){
			
		} else {
			$monto 			= ($monto == false ) ? $this->mSumaDeRecibo : $monto;
			if ($this->mSetGenerarPoliza == true && $this->mCodigoDeRecibo != false){
				$xTn				= new cCatalogoOperacionesDeCaja();
				setPolizaProforma($codigo_de_recibo, $xTn->getTipoOperacionByTipoPago($tipo_pago), $monto, $socio_de_recibo, $docto_de_recibo, $afectacion);
				$this->mMessages	.= "WARN\tTPAGO\tMovimiento Contable por $tipo_pago por un Monto de $monto\r\n";
			} else {
				$this->mMessages	.= "ERROR\tNo se genero el Movimiento Contable por Tipo de Pago($tipo_pago) por un Monto de $monto\r\n";
			}
		}
	}
	/**
	 * funcion que retorna los mensajes generados por la Clase
	 * @param	string		$put		Salida del Mensaje HTML/TXT
	 * @return	string		Mensajes de la Clase
	 */
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	/**
	 * Actualiza la suma del recibo, de forma forzada
	 * @param float $monto	//Monto que se Pone al Recibo
	 */
	function setSumaDeRecibo($monto = 0){ 	$this->mSumaDeRecibo = $monto;	}
	/**
	 * Efectua operaciones de finalizacion del recibo
	 * @param boolean $UpdateSaldo Marca si Actualiza el Saldo del Credito
	 */
	function setFinalizarRecibo($UpdateSaldo = false, $tesoreria_cargada = false){
		$this->mMoneda		= strtoupper($this->mMoneda);
		$xLog				= new cCoreLog();
		$xAv				= new cAML();
		$xQL				= new MQL();
		//generar Poliza a demanda
		$finalizado				= true;
		//tranferencia.egresos cheque
		if(MODULO_CAJA_ACTIVADO == false){ $tesoreria_cargada = true;/* Forzar AML si Tesoreria esta Desactivado */  }
		/**
		 * Modificacion de la condicion de socio por afectar al recibo en SI?
		 */
		$this->mPeriodoActivo;
		if ($UpdateSaldo == true){
			if ( ($this->mSumaDeRecibo == 0) OR ($this->mForceUpdateSaldos == true) OR ( !isset($this->mSumaDeRecibo) ) OR ( $this->mSumaDeRecibo == false ) OR ( $this->mSumaDeRecibo == '' ) ){
				$sqlSUM = "SELECT
								SUM(`operaciones_mvtos`.`afectacion_real` *  `operaciones_mvtos`.`valor_afectacion`) 	AS 'monto',
								COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`)										AS 'numero',
								`operaciones_mvtos`.`recibo_afectado`,
								`operaciones_mvtos`.`socio_afectado`
							FROM
								`operaciones_mvtos` `operaciones_mvtos`
							WHERE
								(`operaciones_mvtos`.`recibo_afectado` =" . $this->mCodigoDeRecibo . ")

							GROUP BY
								`operaciones_mvtos`.`recibo_afectado` ";
				$TDRec					= obten_filas($sqlSUM);

				$this->mSumaDeRecibo 	= (!isset($TDRec["monto"])) ? 0 : $TDRec["monto"];
				$this->mNumeroDeMvtos	= (!isset($TDRec["numero"])) ? 0 : $TDRec["numero"];
				$this->mMessages		.= "OK\tMonto Actualizado a " . $this->mSumaDeRecibo . " y # Operaciones " . $this->mNumeroDeMvtos . "\r\n";
			}
			if($this->getAfectacionEnCaja() !== 0){
				$this->mSumaDeRecibo	= $this->mSumaDeRecibo * $this->getAfectacionEnCaja();
			}
			
			$sql = "UPDATE operaciones_recibos SET total_operacion=" . $this->mSumaDeRecibo . "	WHERE	idoperaciones_recibos= " . $this->mCodigoDeRecibo . "	";
			$xRs = $xQL->setRawQuery($sql);
		}

		$this->mTotalRecibo	= $this->mSumaDeRecibo;
		$this->setCleanCache();
		//-- AML
		if(MODULO_AML_ACTIVADO == true AND $this->isPagable() == true AND $tesoreria_cargada == true){
			$xAml		= new cAMLPersonas($this->mSocio);
			$xMat		= new cAMLMatrizDeRiesgo();
			$xAmlO		= new cAMLOperaciones();
			$NivelRiesgo= 0;
			$Factores	= 0;
			$xAml->init();
			$xAml->setForceAlerts();
			
			
			$this->init();
			//Revisa una persona si es M}Vigilada
			if($xAml->getEsPersonaVigilada() == true){
				$xTipoPago			= new cTesoreriaTiposDePagoCobro($this->mTipoDePago);
				$tipo_de_pago_aml	= $this->mTipoDePago;
				$res				= false;
				if($xTipoPago->init() == true){
					//Actualizar Origen AML
					$this->mTipoOrigenAML	= $xTipoPago->getTipoEnAML();
					if($this->mTipoOrigenAML>0){
						$res		= $xAmlO->analizarOperacion($this->mSocio, 0, $this->mMoneda, $tipo_de_pago_aml, 	$this->getFechaDeRecibo(), $this->getCodigoDeRecibo(), $this->mTipoOrigenAML);
					}
				}
				
				if($res == false ){
					$xLog->add("OK\tAML Normal por Tipo de Pago $tipo_de_pago_aml y en AML " . $this->mTipoOrigenAML . "\r\n", $xLog->DEVELOPER);
					$xLog->add($xAmlO->getMessages(OUT_TXT), $xLog->DEVELOPER);
				} else {
					//REPORTAR
					$xAv			= new cAML();
					if($xAmlO->getTipoDeReporte() == AML_REPORTE_INMEDIATO){
						$xAv->setForceRegistroRiesgo();
						$xLog->add("WARN\tAML de Aviso Inmediato por Tipo de Pago $tipo_de_pago_aml y en AML " . $this->mTipoOrigenAML . "\r\n", $xLog->DEVELOPER);
					}
					$xAv->sendAlerts($this->mSocio, getOficialAML(), $xAmlO->getTipoDeAlerta(), $xAmlO->getMessageAlert(), $this->mCodigoDeRecibo, $this->mFechaDeOperacion);
				}
				//Riesgo de Operación Implicita
				
				$xLog->add($xAmlO->getMessages(OUT_TXT), $xLog->DEVELOPER);
			}
			//Validar si es person riesgosa
			
			$xSoc					= $xAml->getOPersona();
			$EstadoReportado		= false;
			//Operaciones con Personas Bloqueadas
			if($xSoc->getEsPersonaSDN() == true ){ //AND $xAml->getEsPersonaVigilada() == true
				$xMat->initByTopico($xMat->O_PERSONA_BLOQUEADO);
				$idriesgo			= $xMat->getTipoRiesgo();
				$xLog->add("REPORTE\tOperaciones con persona ALTAMENTE RIESGOSA en el Recibo de Operacion : " . $this->mCodigoDeRecibo);
				$xAv				= new cAML();
				$xAv->setForceRegistroRiesgo();
				$xAv->sendAlerts($this->mSocio, getOficialAML(), $idriesgo, $xLog->getMessages(), $this->mCodigoDeRecibo, $this->getFechaDeRecibo());
				$xLog->add($xAv->getMessages(OUT_TXT), $xLog->DEVELOPER);
				$EstadoReportado	= true;
				$NivelRiesgo		+= $xMat->getNivelRiesgo();
				$Factores++;
			}
			//REPORTAR operaciones con PEPs
			if( $xSoc->getEsPersonaPoliticamenteExpuesta() == true AND $xAml->getEsPersonaVigilada() == true ){
				$xMat->initByTopico($xMat->O_PERSONA_PEPS);
				$idriesgo			= $xMat->getTipoRiesgo();
				$xLog->add("REPORTE\tOperaciones con PEPS Recibo de Operacion : " . $this->mCodigoDeRecibo);
				$xAv			= new cAML();
				$xAv->sendAlerts($this->mSocio, getOficialAML(), $idriesgo, $xLog->getMessages(), $this->mCodigoDeRecibo, $this->getFechaDeRecibo());
				$xLog->add($xAv->getMessages(OUT_TXT), $xLog->DEVELOPER);
				$EstadoReportado	= true;
				$NivelRiesgo		+= $xMat->getNivelRiesgo();
				$Factores++;
			}
			//Reportar Operaciones con Persona de Alto Riesgo, si es vigilada y si no ha sido verificado antes
			if($xSoc->getEsPersonaRiesgosa() == true AND $xAml->getEsPersonaVigilada() == true AND $EstadoReportado == false ){
				$xMat->initByTopico($xMat->O_PERSONA_ALTORIESGO);
				$idriesgo			= $xMat->getTipoRiesgo();
				$xLog->add("REPORTE\tOperaciones con Personas de Alto Riesgo en el Recibo de Operacion : " . $this->mCodigoDeRecibo);
				$xAv			= new cAML();
				$xAv->sendAlerts($this->mSocio, getOficialAML(), $idriesgo, $xLog->getMessages(), $this->mCodigoDeRecibo, $this->getFechaDeRecibo());
				$xLog->add($xAv->getMessages(OUT_TXT), $xLog->DEVELOPER);
				$NivelRiesgo		+= $xMat->getNivelRiesgo();
				$Factores++;
			}
			
			//Operaciones de una exhibicion 500 y en Moneda Extranjera
			if($this->mMoneda != AML_CLAVE_MONEDA_LOCAL){
				$xRisk			= new cAMLCatalogoDeRiesgos(AML_CLAVE_RIESGO_OPS_INDIVIDUALES);
				
				if($xRisk->init() == true){
					$xMon		= new cMonedas($this->mMoneda);
					$unidades	= $xMon->getEnDolares($this->getUnidadesOriginales());
					if($unidades >= $xRisk->getUnidadesPonderadas()){
						$xLog->add("REPORTE\tOperaciones($unidades) excedidas de 500 USD en el recibo " . $this->mCodigoDeRecibo . " Moneda " . $this->mMoneda);
						$idriesgo		= $xRisk->getClave();
						$xAv			= new cAML();
						$xAv->setForceRegistroRiesgo();
						$xAv->sendAlerts($this->mSocio, getOficialAML(), $idriesgo, $xLog->getMessages(), $this->mCodigoDeRecibo, $this->getFechaDeRecibo());
						$xLog->add($xAv->getMessages(OUT_TXT), $xLog->DEVELOPER);
						$NivelRiesgo		+= $xMat->getNivelRiesgo();
						$Factores++;
						
					} else {
						$xLog->add("OK\tLimites(500USD) sin exceder en la Moneda " . $this->mMoneda . " por $unidades \r\n", $xLog->DEVELOPER);
					}
				} else {
					$xLog->add("OK\tNo hay Riesgo(500USD) Iniciado para la" . $this->mMoneda . " por $unidades \r\n", $xLog->DEVELOPER);
				}
			}
			// Agregar Relevantes por 10000USD
			if($this->isEfectivo() == true){
				$xRisk			= new cAMLCatalogoDeRiesgos(AML_CLAVE_RIESGO_OPS_RELEVANTES);
				if($xRisk->init() == true){
					$xMon		= new cMonedas($this->mMoneda);
					$mmonto		= ($this->mMoneda == AML_CLAVE_MONEDA_LOCAL) ? $this->getTotal() : $this->getUnidadesOriginales();
					$unidades	= $xMon->getEnDolares($mmonto);
					$idriesgo	= $xRisk->getClave();
					
					if($unidades >= $xRisk->getUnidadesPonderadas()){
						$xLog->add("REPORTE\tOperaciones Relevantes por $unidades USD en el recibo " . $this->mCodigoDeRecibo . " Moneda " . $this->mMoneda);
						$xAv->setForceRegistroRiesgo();
						$xAv->sendAlerts($this->mSocio, getOficialAML(), $idriesgo, $xLog->getMessages(), $this->mCodigoDeRecibo, $this->getFechaDeRecibo());
						$xLog->add($xAv->getMessages(OUT_TXT), $xLog->DEVELOPER);
						$NivelRiesgo		+= $xMat->getNivelRiesgo();
						
						$Factores++;
					} else {
						$xLog->add("OK\tLimites(Relevantes) sin exceder en la Moneda " . $this->mMoneda . " por $unidades \r\n", $xLog->DEVELOPER);
					}
				} else {
					$xLog->add("OK\tNo hay Riesgo(Relevantes) Iniciado para la" . $this->mMoneda . " por $unidades \r\n", $xLog->DEVELOPER);
				}
			}
				
			
			if($xAml->getEsPersonaOmitida() == true){
				$xLog->add("OK\tLa Persona esta en Lista de Omitidos", $xLog->DEVELOPER);
			} else {
				//Operaciones Internas Preocupantes por Usuario
				$xAml->setAnalizarTransaccionalidadPorNucleo($this->mCodigoDeRecibo, $this->mFechaDeOperacion, $this->mUsuario, true);
				$xAml->setVerificarPerfilTransaccional(false, true);
				//Analizar Riesgo de Producto

					
				$xTipoRec	= new cTipoDeRecibo($this->getTipoDeRecibo());
				if($xTipoRec->init() == true){
					if($xTipoRec->getEsDeCaptacion()){
						$xCapt	= new cCuentaDeCaptacion($this->getCodigoDeDocumento());
						if($xCapt->init() == true){
							$NivelRiesgo	= $xCapt->OProducto()->getAMLRiesgoAsoc();
							$Factores++;
						}
					}
					if($xTipoRec->getEsDeCredito()){
						$xCred	= new cCredito($this->getCodigoDeDocumento());
						if($xCred->init() == true){
							$NivelRiesgo	= $xCred->getOProductoDeCredito()->getAMLRiesgoAsoc();
							$Factores++;
						}
					}
				}
			}
			if($NivelRiesgo>0){
				$xMRisk		= new cRiesgos();
				$riesgo		= $xMRisk->getNivelarR(($NivelRiesgo/$Factores));
				if($riesgo >= SYS_RIESGO_MEDIO){
					if($xMat->initByTopico($xMat->O_RIESGO_PDTO) == true){
						$idriesgo	= $xMat->getTipoRiesgo();
						$xLog->add("REPORTE\tLa operacion en el Recibo ". $this->mCodigoDeRecibo . " se Reportar por Riesgo Medio o Mayor");
						$xAv->setForceRegistroRiesgo();
						$xAv->sendAlerts($this->mSocio, getOficialAML(), $idriesgo, $xLog->getMessages(), $this->mCodigoDeRecibo, $this->getFechaDeRecibo());
					}
				}
			}
			//Agregar Nivel de Riesgo de Producto
			$xLog->add($xAml->getMessages(OUT_TXT), $xLog->DEVELOPER);
		}
		$this->mMessages		.= $xLog->getMessages();
		return $finalizado;
	}
	/**
	 * Revierte las Afectaciones del Recibo
	 */
	function setRevertir($ForzarEliminar = false){
		$xQL			= new MQL();
		$xLog			= new cCoreLog();
		if($this->getTipoDeRecibo() == RECIBOS_TIPO_PLAN_DE_PAGO){
			$xLog->add("WARN\tEliminar Plan de Pagos con Codigo " . $this->mCodigoDeRecibo ."\r\n");
			$xLog->add("WARN\tEliminando Operaciones del Recibo " . $this->mCodigoDeRecibo ."\r\n", $xLog->DEVELOPER);
			$sqlDM 			= "DELETE FROM `operaciones_mvtos` WHERE `recibo_afectado` =" . $this->mCodigoDeRecibo . ""; $xQL->setRawQuery($sqlDM);
			$xLog->add("WARN\tEliminando El Recibo " . $this->mCodigoDeRecibo ."\r\n", $xLog->DEVELOPER);
			$sqlDR 			= "DELETE FROM operaciones_recibos WHERE idoperaciones_recibos =" . $this->mCodigoDeRecibo . ""; $xQL->setRawQuery($sqlDR);
			$sucess			= true;
			$xLog->guardar($xLog->OCat()->RECIBO_ELIMINADO, $this->getCodigoDeSocio());
		} else {
			$sucess			= true;
			$arrValuesRev	= array("-1" => "1", "1" => "-1", "0" => "0");
			
			
			$periodo		= $this->getPeriodo();
			$sqlM 			= "SELECT
						`operaciones_mvtos`.*,
						`operaciones_tipos`.*,
						`operaciones_mvtos`.`recibo_afectado`
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
							INNER JOIN `operaciones_tipos` `operaciones_tipos`
							ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
							`idoperaciones_tipos`
					WHERE
						(`operaciones_mvtos`.`recibo_afectado` =" . $this->mCodigoDeRecibo . ")";
			$xLog->add("WARN\tReversion del Recibo " . $this->mCodigoDeRecibo . "] - Persona : " . $this->getCodigoDeSocio() . " - Documento: " . $this->getCodigoDeDocumento() . " \r\n");
			$original		= "";
			$rs 			= $xQL->getDataRecord($sqlM); 	//getRecordset($sqlM);
			$items			= $xQL->getNumberOfRows();
			$tocarplan		= false;						//Reconstruir Letra de Pago
			$docto			= $this->getCodigoDeDocumento();
			$OPT_CALCULAR_INTERES	= false;
			$PAGOS_SIN_CAPITAL		= false;// variable
			//XXX: Revisar procesos de reversion
			if( $this->init() == true){
				$original	= json_encode($this->getDatosInArray());
				$original	= "====[". base64_encode( $original ) . "]====";
				setArchivarRegistro($original, iDE_RECIBO);
				$contar		= 0;
				$DoctoTrabajo			= 0;
				if($rs){
					foreach ($rs as $rw){
						$msg			= "";
						$codigo			= $rw["idoperaciones_mvtos"];
						$docto			= $rw["docto_afectado"];
						$socio			= $rw["socio_afectado"];
						$preservar_mvto = $rw["preservar_movimiento"];
						$CodeRevertir	= $rw["formula_de_cancelacion"];
						$monto			= $rw["afectacion_real"];
						$afectacion		= $rw["valor_afectacion"];
						$recibo			= $rw["recibo_afectado"];
						$parcialidad	= $rw["periodo_socio"];
						$parcialidad	= ($parcialidad <=0 ) ? $periodo : $parcialidad;
						$colocacion		= array();
						$captacion		= array();
						$contar++;
						if($DoctoTrabajo!== $docto ){
							//selecciona un comportamiento segun el Origen del Recibo
							//$PAGOS_SIN_CAPITAL
							
							switch ($this->mAplicadoA){
								case "colocacion":
									//cargar datos del credito
		                               $Credito        = new cCredito($docto, $socio);
		                               $Credito->init();
		                               $colocacion     = $Credito->getDatosDeCredito();
		                               if($Credito->isAFinalDePlazo() == false){
		                               		$Credito->getNumeroDePlanDePagos();
		                               }
		                               $PAGOS_SIN_CAPITAL	= $Credito->getPagosSinCapital();
										break;
									case "captacion":
										//cargar datos de la cuenta
		                                $Cuenta        		= new cCuentaALaVista($docto);
		                                $Cuenta->init();
		                                $captacion      	= $Cuenta->getDatosInArray();
		
										break;
									case "mixto":
										//cargar datos de la cuenta y del credito
		                                $Credito       		= new cCredito($docto, $socio);
		                                $Credito->init();
		                                if($Credito->isAFinalDePlazo() == false){
		                                	$Credito->getNumeroDePlanDePagos();
		                                }
		                                $PAGOS_SIN_CAPITAL	= $Credito->getPagosSinCapital();
		                                
		                                $colocacion			= $Credito->getDatosDeCredito();
		                                $Cuenta				= new cCuentaALaVista($docto);
		                                if($Cuenta->init() == false){
		                                	//$Cuenta			= new cCuentaALaVista($Credito->getNu);
		                                }
		                                $captacion      = $Cuenta->getDatosInArray();
		                                $xLog->add("WARN\tEL Recibo es Mixto, se carga tanto Captacion como Colocacion\r\n");
										break;
									default:
		                                $xLog->add("WARN\tEL Recibo es " . $this->mAplicadoA . ", NO SE CARGA CODIGO\r\n");
										break;
							}
						}
						
						
							eval( $CodeRevertir );
							
	
								if($preservar_mvto=='1' AND $ForzarEliminar == false){
									$SQL_DM = "UPDATE operaciones_mvtos
											SET afectacion_estadistica=afectacion_real,
											afectacion_real = 0, valor_afectacion=0,
											estatus_mvto = 99,
											docto_neutralizador = " . $this->mCodigoDeRecibo  .",
											recibo_afectado	= " . DEFAULT_RECIBO . "
											WHERE idoperaciones_mvtos = $codigo
											";
									$xLog->add("Actualizando el Movimiento $codigo\r\n", $xLog->DEVELOPER);
								} else {
									$SQL_DM = "DELETE FROM operaciones_mvtos
											WHERE idoperaciones_mvtos = $codigo";
									$xLog->add("Eliminado el Movimiento $codigo\r\n", $xLog->DEVELOPER);
								}
								$xQL->setRawQuery($SQL_DM);
						$DoctoTrabajo	= $docto;
						if($contar >= $items){
							//Actualizar Saldos
							if(isset($Cuenta)){ $Cuenta->setCuandoSeActualiza(); }
							if(isset($Credito)){ 
								$Credito->setCuandoSeActualiza();
								$xLog->add($Credito->getMessages(), $xLog->DEVELOPER);
							}
						}
						$xLog->add($msg, $xLog->DEVELOPER);
						
					}
				}
				//Elimnar Prepoliza
				$xLog->add("WARN\tEliminando Prepolizas\r\n", $xLog->DEVELOPER);
				$sqlDP 				= "DELETE FROM contable_polizas_proforma	WHERE numero_de_recibo = " . $this->mCodigoDeRecibo . ""; $xQL->setRawQuery($sqlDP);
				//Eliminar Recibo
				$xLog->add("WARN\tEliminando El Recibo" . $this->mCodigoDeRecibo ."\r\n", $xLog->DEVELOPER);
				$sqlDR 				= "DELETE FROM operaciones_recibos WHERE idoperaciones_recibos =" . $this->mCodigoDeRecibo . ""; $xQL->setRawQuery($sqlDR);
				//Agregar Tesoreria y Bancos
				$xLog->add("WARN\tEliminando Operaciones de Caja\r\n", $xLog->DEVELOPER);
				$DelTesoreria		= "DELETE FROM `tesoreria_cajas_movimientos` WHERE `recibo`= " . $this->mCodigoDeRecibo . ""; $xQL->setRawQuery($DelTesoreria);
				$xLog->add("WARN\tEliminando Operaciones de Bancos\r\n", $xLog->DEVELOPER);
				$DelBancos			= "DELETE FROM `bancos_operaciones` WHERE `recibo_relacionado` = " . $this->mCodigoDeRecibo . ""; $xQL->setRawQuery($DelBancos);
				//Neutraliza Pagos en los envio de cobranza
				$xQL->setRawQuery("UPDATE `empresas_cobranza` SET `recibo`= 0, `estado`=1, `tiempocobro`=0 WHERE `recibo`=" . $this->mCodigoDeRecibo);
				
				if($this->mAplicadoA == "colocacion" OR $this->mAplicadoA == "mixto" AND $this->isPagable() == true){
					if(!isset($Credito)){
						$Credito	= new cCredito($docto);
					}
					if($Credito->init() == true){
						if($Credito->getEsAfectable() == true){
							$Credito->setReestructurarIntereses(false, false, true);
						}
					}
				}
			}
			
			$xCE	= new cErrorCodes();
			//setLog($this->mMessages .  json_encode($this->getDatosInArray()), $xCE->RECIBO_ELIMINADO);
			$xLog->guardar($xLog->OCat()->RECIBO_ELIMINADO, $this->getCodigoDeSocio());
			$this->mMessages	.= $xLog->getMessages();
			if($this->isPagable() == true){
				//agregar Aviso.
				$xRuls	= new cReglaDeNegocio();
				$xRuls->setVariables(array(
						"mensaje" => $this->getMessages(),
						"original" => $original
				));
				$xRuls->setExecuteActions( $xRuls->reglas()->RN_DATOS_AL_ELIMINAR_RECIBO );
			}
		}
		return $sucess;
	}	
	function getCodigoDeRecibo(){ return $this->mCodigoDeRecibo;	}
	/**
	 * Retorna un Array por los datos del Recibo
	 * @return array
	 */
	function getDatosInArray(){ return $this->mDatosByArray; }
	/**
	 * @deprecated 1.9.42
	 */	
	function getDatosReciboInArray(){ return $this->mDatosByArray;	}
	function getURI_Formato(){ return $this->mPathToFormato . $this->mCodigoDeRecibo; }
	/**
	 * Funcion que Retorna una Ficha Descriptiva por el recibo
	 * @param boolean $fieldset
	 * @param string $trTool
	 * @param string $wTable
	 * @return string
	 */
	function getFicha($fieldset = false, $trTool = "", $extend = false){
			$this->init();
			$xLg			= new cLang();
			$personaAsoc	= $this->getPersonaAsociada();
			$xRuls			= new cReglaDeNegocio();
			$sinImpreso		= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_SIN_VERSIONIMP);
			//$fichaEmpresa	= "";
			$xF		= new cFecha(0);
		if($this->mReciboIniciado == false){
			$exoFicha =  "<div class='error'>" . $xLg->get(MSG_NO_DATA) .  "</div>";
		} else {
			$xLg		= new cLang();
			$tool 	= "";
			if($extend == true){
				$xUsr	= new cSystemUser($this->getCodigoDeUsuario());
				$xUsr->init();
				
				$xSoc	= new cSocio($this->getCodigoDeSocio()); $xSoc->init();
				
				$tool	.= "<tr><th class='izq'>" . $xLg->getT("TR.persona") . "</th>";
				$tool	.= "<td>" . $xSoc->getNombreCompleto() . "</td>" ;
				
				$tool	.= "<th class='izq'>" . $xLg->getT("TR.Documento") . "</th>";
				$tool	.= "<td>" . $this->getCodigoDeDocumento() . "</td>" ;
								
				$tool	.= "<tr><th class='izq'>" . $xLg->getT("TR.Elabora") . "</th>";
				$tool	.= "<td>" . $xUsr->getNombreCompleto(). "-" . $xF->getFechaDDMM($this->mFechaDeCaptura) . "</td>" ;
				
				if($this->isDeEmpresa() == true){
					$xEmp	= new cEmpresas($personaAsoc);
					$xEmp->init();
					$tool	.= "<th class='izq'>" . $xLg->getT("TR.Empresa") . "</th>";
					$tool	.= "<td>" . $xEmp->getNombre() . "</td>" ;
				}
				$tool	.= "</tr>";
				if($this->getObservaciones() != ""){
					$tool	.= "<tr>
					<th class='izq'>" . $xLg->getT("TR.Observaciones") . "</th>
					<td colspan='3'>" . $this->getObservaciones() . "</td>
					</tr>";
				}
			}
			if($this->isDivisaExtranjera() == true){
				$tool	.= "<tr><th class='izq'>" . $xLg->getT("TR.Moneda") . "</th>";
				$tool	.= "<td>" . $this->getMoneda() . "</td>" ;
				$tool	.= "<th class='izq'>" . $xLg->getT("TR.Original") . "</th>";
				$tool	.= "<td>" . $this->getUnidadesOriginales() . "</td>" ;				
				$tool	.= "</tr>";
			}			
			$tool		.= $trTool;
			
			$exoFicha =  "
				<table id=\"ficharecibo\">
				<tbody>
					<tr>
						<th class='izq'>" . $xLg->getT("TR.Numero de Recibo"). "</th>
						<td class='mny'>" . $this->mCodigoDeRecibo . "</td>
						<th class='izq'>" . $xLg->getT("TR.Tipo de Recibo"). "</th>
						<td>" . $this->mTipoDescripcion . "</td>
					</tr>
					<tr>
						<th class='izq'>" . $xLg->getT("TR.Fecha de Recibo"). "</th>
						<td>" . $xF->getFechaCorta($this->mFechaDeOperacion) . "</td>
						<th class='izq'>" . $xLg->getT("TR.Recibo Fiscal") ."</th>
						<td>" . $this->mReciboFiscal . "</td>
					</tr>
					<tr>
						<th class='izq'>" . $xLg->getT("TR.Tipo de Pago") . "</th>
						<td>" .  strtoupper( $this->mTipoDePago ) . "</td>
						<th class='izq'>" . $xLg->getT("TR.Total") ."</th>
						<td class='mny'>" .  getFMoney( $this->mTotalRecibo ) . "</td>

					</tr>

					$tool
				</tbody>
				</table>";
			if ($fieldset == true){
				$exoFicha = "<fieldset>
								<legend>&nbsp;&nbsp;INFORMACI&Oacute;N DEL RECIBO&nbsp;&nbsp;</legend>
								$exoFicha
							</fieldset>";
			}
		}
		return $exoFicha;
	}
	/**
	 * Retorna un Array de Datos por el Documento Usado segun el Origen del recibo
	 * @return array
	 */
	function getInfoDoctoInArray(){
		$datos	= false;
		switch($this->mAplicadoA){
			case "otros":

				break;
			case "captacion":
				$sql = "SELECT * FROM captacion_cuentas WHERE
				numero_socio = " . $this->mSocio . "
				AND
				numero_cuenta = " . $this->mDocto . "
				LIMIT 0,1";
				$datos	= obten_filas($sql);
			break;
			case "colocacion":
				$sql = "SELECT * FROM creditos_solicitud WHERE
				numero_socio = " . $this->mSocio . "
				AND
				numero_solicitud = " . $this->mDocto . "
				LIMIT 0,1";
				$datos	= obten_filas($sql);
			break;
			default:
				$sqlC = "SELECT * FROM creditos_solicitud WHERE
				numero_socio = " . $this->mSocio . "
				AND
				numero_solicitud = " . $this->mDocto . "
				LIMIT 0,1";
				$datosC	= obten_filas($sqlC);

				$sqlA = "SELECT * FROM captacion_cuentas WHERE
				numero_socio = " . $this->mSocio . "
				AND
				numero_cuenta = " . $this->mDocto . "
				LIMIT 0,1";
				$datosA		= obten_filas($sqlA);
				$datos		= $datosC;
			break;
		}
		return $datos;
	}
	function setDefaultEstatusOperacion($estatus = 30){ $this->mDefMvtoStatus	= $estatus; }
	function setFinalizarTesoreria($arrParams = array()){
		$ocaja	= $this->getOCaja();
		if($ocaja != null){
			switch ($this->mTipoDePago){
				case TESORERIA_PAGO_EFECTIVO:
					$ocaja->setPagoEfectivo($this->mCodigoDeRecibo, $this->mTotalRecibo, 0, $this->mObservaciones, $this->mFechaDeOperacion, $this->mMoneda, $this->mTotalRecibo);
				break;
				case TESORERIA_COBRO_DESCTO:
					$cuenta	= (isset($arrParams["cuenta"])) ? $arrParams["cuenta"] : $this->mCuentaBancaria;
					$cheque	= (isset($arrParams["cheque"])) ? $arrParams["cheque"] : $this->mNumeroCheque;
					$monto	= (isset($arrParams[SYS_MONTO])) ? $arrParams[SYS_MONTO] : $this->mTotalRecibo; 
					$ocaja->setCobroDescuentoCheque($this->mCodigoDeRecibo, $monto, $cuenta, $cheque);
				break;
			}
		}
	}
	function setValorar(){
		$msg		= "";
		if ( $this->mReciboIniciado == false ) {
			$this->init();
		}
		$D			= $this->getDatosInArray();
		$socio		= $this->getCodigoDeSocio();
		$documento	= $this->getCodigoDeDocumento();
		$recibo		= $D["idoperaciones_recibos"];
		//verifica si el socio existe
		$xS			= new cSocio($socio);
		if ( $xS->init() == false ){
			$msg	.= "ERROR\tSOCIO\t$recibo\tEL SOCIO $socio NO Existe\r\n";
		}
		//verifica si el documento existe
		//verifica si el tipo de recibo existe
		//verifica los movimientos
		//verifica si el socio existe
		//verifica si el documento existe
		//verifica si el tipo de mvto existe
		return $msg;
	}
	function getFichaSocio(){ $xS = new cSocio($this->mSocio, true); return $xS->getFicha(); }
	function getJsPrint($tags	= false, $jsNombreFunc = ""){
		$jsNombreFunc	= ($jsNombreFunc == "") ? "jsImprimirRecibo" : $jsNombreFunc;
		$js	= "if(typeof printrec =='undefined'){ function printrec(){ $jsNombreFunc(); } }
	function $jsNombreFunc(){ var xGen = new Gen(); xGen.w({url : \"" . $this->mPathToFormato . $this->mCodigoDeRecibo . "\", w:800, h: 860 });	}";
		if ( $tags == true ){ $js = "<script> $js </script>"; }
		return $js;
	}
	function getDescripcion(){
		$xF		= new cFecha(0);
		if( $this->mReciboIniciado == false ){ $this->init(); }
		
		$xSoc	= new cSocio($this->mSocio);
		$xSoc->init();
				
		$desc	= $this->mCodigoDeRecibo . "|" . $xF->getFechaCorta($this->mFechaDeOperacion) . "|" . $xSoc->getNombreCompleto() ;
		
		return $desc;
	}
	function setCambiarCodigo($folio = false){
		$recibo		= $this->mCodigoDeRecibo;
		$xQL		= new MQL();
		$xFolios	= new cFolios();
		$this->init();
		$socio		= $this->mSocio;
		$folio		= setNoMenorQueCero($folio);
		$folio		= ($folio <= 0) ? $xFolios->getClaveDeRecibo() : $folio;
	
		$msg		= "$recibo\tCambiando el Recibo $recibo de la persona $socio al Nuevo Numero $folio\r\n";
	
		$sqlUM 		= "UPDATE operaciones_mvtos SET recibo_afectado = $folio WHERE recibo_afectado = $recibo AND socio_afectado = $socio";
		$m			= $xQL->setRawQuery($sqlUM);
	
		$sqlURec	= "UPDATE operaciones_recibos SET idoperaciones_recibos = $folio WHERE idoperaciones_recibos = $recibo AND numero_socio = $socio ";
		$r 			= $xQL->setRawQuery($sqlURec);

		return $msg;
	}
	function setCambiarCuentaBancaria($cuenta = false){
		$cuenta	= setNoMenorQueCero($cuenta);
		if($cuenta != $this->getClaveCuentaBancaria()){
			$xQL	= new MQL();
			$xQL->setRawQuery("UPDATE `tesoreria_cajas_movimientos` SET `cuenta_bancaria`=$cuenta WHERE `recibo`=" . $this->getCodigoDeRecibo());
			$xQL->setRawQuery("UPDATE `operaciones_recibos` SET `cuenta_bancaria`=$cuenta WHERE `idoperaciones_recibos`=" . $this->getCodigoDeRecibo());
			$xQL->setRawQuery("UPDATE `contable_polizas_proforma` SET `banco`=$cuenta WHERE `numero_de_recibo`=" . $this->getCodigoDeRecibo());
			$xQL->setRawQuery("UPDATE `bancos_operaciones` SET `cuenta_bancaria`=$cuenta WHERE `recibo_relacionado`=". $this->getCodigoDeRecibo());
		} else {
			$this->mMessages	.= "WARN\tNo se Actualizo la cuenta $cuenta\r\n";
		}
	}	
	function getCodigoDeSocio(){ return $this->mSocio; }
	function getSocio(){ return new cSocio($this->mSocio);	}
	function getCodigoDeDocumento(){ return $this->mDocto; }
	function getCredito(){ return new cCredito($this->mDocto); }
	function getTotal(){ return $this->mTotalRecibo; }
	function getOrigen(){ return $this->mOrigen; }
	function getCodigoDeUsuario(){ return $this->mUsuario; }
	function getClaveDeOrigen(){ return $this->mOrigen; }
	function getSaldoHistorico(){ return $this->mSaldoHistorico; }
	function getClaveCuentaBancaria(){ return $this->mCuentaBancaria; }
	function getIndiceOrigen(){ return $this->mIndiceOrigen; }
	function setMoneda($moneda){$this->mMoneda = $moneda;}
	function setUnidadesOriginales($unidades){ $this->mUnidadesOriginales = $unidades; }
	function getUnidadesOriginales(){ return $this->mUnidadesOriginales; }
	function setFechaVencimiento($fecha = false){ 	if ($fecha == false ){ $fecha	= fechasys(); } $this->mFechaDeVcto	= $fecha;	}
	function setFecha($fecha, $actualizacion = false){
		$xQL				= new MQL();
		$xF					= new cFecha();
		$fecha				= $xF->getFechaISO($fecha);
		$this->mFechaDeOperacion	= $fecha;
		$recibo				= $this->mCodigoDeRecibo;
		$this->mMessages	.= "WARN\tRecibo $recibo . Actualizando la fecha " . $this->getFechaDeRecibo() . " A  $fecha\r\n";
		
		if( $actualizacion == true ){
			$xQL->setRawQuery("UPDATE operaciones_recibos SET fecha_operacion='$fecha' WHERE idoperaciones_recibos=$recibo");
			if($this->mUnicoDocto == true){
				$xQL->setRawQuery("UPDATE operaciones_mvtos SET fecha_afectacion='$fecha', fecha_operacion='$fecha' WHERE recibo_afectado=$recibo");
			}
			//actualizar tesoreria
			$xQL->setRawQuery("UPDATE tesoreria_cajas_movimientos SET  fecha='$fecha'    WHERE  recibo=$recibo");
			//actualizar bancos
			$xQL->setRawQuery("UPDATE `bancos_operaciones` SET `fecha_expedicion`='$fecha' WHERE`recibo_relacionado`=$recibo");
			//ejecutar aviso
			$xLog		= new cCoreLog();
			$xLog->add($this->mMessages);
			$xLog->guardar($xLog->OCat()->EDICION_RAW, $this->getCodigoDeSocio());
		}
	}
	function setPeriodo($periodo = false, $actualizacion = false){
		$periodAnterior			= $this->getPeriodo();
		$this->mPeriodoActivo	= setNoMenorQueCero($periodo);
		$recibo					= setNoMenorQueCero($this->mCodigoDeRecibo);
		if($actualizacion == true AND $recibo >  0){
			$ql					= new MQL();
			$periodo			= $this->mPeriodoActivo;
			$this->mMessages	.= "WARN\tActualizando a nuevo periodo $periodo\r\n";
			$this->setCleanCache();
			$ql->setRawQuery("UPDATE operaciones_mvtos SET periodo_socio=$periodo WHERE recibo_afectado=$recibo");
			$ql->setRawQuery("UPDATE `operaciones_recibos` SET `periodo_de_documento`=$periodo WHERE `idoperaciones_recibos`=$recibo");
			//Actualiza los envios de nomina
			$ql->setRawQuery("UPDATE `empresas_cobranza` SET `recibo`= 0, `estado`=1, `tiempocobro`=0, `observaciones`='' WHERE `recibo`=$recibo");
			//Actualiza los recibos al nuevo
			$ql->setRawQuery("UPDATE `empresas_cobranza`, `operaciones_recibos` SET `recibo`= `operaciones_recibos`.`idoperaciones_recibos`, `tiempocobro`=UNIX_TIMESTAMP(`operaciones_recibos`.`fecha_operacion`), `estado`=0 
			WHERE `empresas_cobranza`.`recibo`=0 AND `operaciones_recibos`.`docto_afectado`=`empresas_cobranza`.`clave_de_credito` AND `operaciones_recibos`.`periodo_de_documento`=`empresas_cobranza`.`parcialidad` AND `operaciones_recibos`.`tipo_docto`= 2");
		}
	}
	function setTotalPorProrrateo($total){
		if(setNoMenorQueCero($total)>0){
			$recibo				= $this->mCodigoDeRecibo;
			$factor				= $total / $this->getTotal(); //2500 / 8000 = 
			$this->mMessages	.= "CHANGES\tCambiar " . $this->getTotal() . " a $total\r\n";
			$rec				= my_query("UPDATE operaciones_recibos SET total_operacion=(total_operacion*$factor) WHERE idoperaciones_recibos=$recibo");
			$ops				= my_query("UPDATE operaciones_mvtos SET afectacion_real=(afectacion_real*$factor), afectacion_cobranza=(afectacion_cobranza*$factor), afectacion_contable=(afectacion_contable*$factor), afectacion_estadistica=(afectacion_estadistica*$factor) WHERE recibo_afectado=$recibo");
			$this->mMessages	.= $rec[SYS_INFO];
			$this->mMessages	.= $ops[SYS_INFO];
		}
	}
	function getBancoPorOperacion(){
		$ob		= null;
		$sql 	= "SELECT `bancos_operaciones`.`cuenta_bancaria` FROM	`bancos_operaciones` `bancos_operaciones` 
		WHERE (`bancos_operaciones`.`recibo_relacionado` =" . $this->mCodigoDeRecibo . ") ORDER BY `bancos_operaciones`.`monto_real` DESC LIMIT 0,1";
		$d		= obten_filas($sql);
		if(isset($d["cuenta_bancaria"])){
			$ob	= new cCuentaBancaria($d["cuenta_bancaria"]);
			if($ob->init($d) == true){
				$this->mMessages	.= "WARN\tSe Carga la cuenta " . $d["cuenta_bancaria"] . " desde Tesoreria\r\n";
			}
		} else {
			$this->mMessages	.= "ERROR\tNo hay Operacion Bancaria del Recibo " . $this->mCodigoDeRecibo . " \r\n";
		}
		return $ob;
	}
	function getTesoreriaPorOperacion(){
		$ob	= null;
		$sql 	= "SELECT `tesoreria_cajas_movimientos`.*  FROM	`tesoreria_cajas_movimientos` `tesoreria_cajas_movimientos` 
		WHERE (`tesoreria_cajas_movimientos`.`recibo` =" . $this->mCodigoDeRecibo . ") LIMIT 0,1";
		$d	= obten_filas($sql);
		if(isset($d["idtesoreria_cajas_movimientos"])){
			//$ob	= new cCuentaBancaria($d["cuenta_bancaria"]);
			//$ob->init();
		}
		return $ob;
	}
	function setCambiarRelacionados($NDocumento= false, $NPersona = false, $AfectarOperaciones = false){
		$xOO		= new cOperaciones_mvtos();
		//$docto		= $xOO->docto_afectado()->v();
		//$socio		= $xOO->socio_afectado()->v();
		$recibo			= $this->getCodigoDeRecibo();
		$Bupdate		= ($NDocumento == false) ? "" : " docto_afectado=$NDocumento ";
		$ByDocto		= ($NDocumento == false) ? false : true;
		$BySocio		= ($NPersona == false) ? false : true;
		
		if($BySocio == true){
			$Bupdate	.= ($Bupdate == "") ? " numero_socio=$NPersona " : ", numero_socio=$NPersona ";
		}
		my_query("UPDATE operaciones_recibos SET $Bupdate  WHERE  idoperaciones_recibos=$recibo");
		if($this->mUnicoDocto == true){
			if($AfectarOperaciones == true){
				$Bupdate		= ($NDocumento == false) ? "" : " docto_afectado=$NDocumento ";
				if($BySocio == true){
					$Bupdate	.= ($Bupdate == "") ? " socio_afectado=$NPersona " : ", socio_afectado=$NPersona ";
				}
				my_query("UPDATE operaciones_mvtos SET $Bupdate   WHERE recibo_afectado=$recibo");
				if($ByDocto == true){
					my_query("UPDATE tesoreria_cajas_movimientos SET documento=$NDocumento WHERE  recibo=$recibo");
				}
				$Bupdate		= ($NDocumento == false) ? "" : " numero_de_documento=$NDocumento ";
				if($BySocio == true){
					$Bupdate	.= ($Bupdate == "") ? " numero_de_socio=$NPersona " : ", numero_de_socio=$NPersona ";
				}
				my_query("UPDATE bancos_operaciones SET $Bupdate  WHERE recibo_relacionado=$recibo");
			}			
		}
	}
	function setDatosDePago($moneda = '', $monto_moneda = 0, $cheque ='', $tipo_de_pago = false, $transaccion = SYS_NINGUNO, $CuentaBancaria = 0){
		$xQL			= new MQL();
		$moneda			= ($moneda =='') ? $this->mMoneda : $moneda;
		$moneda			= strtoupper($moneda);
		$CuentaBancaria	= setNoMenorQueCero($CuentaBancaria);
		$tipo_de_pago	= ($tipo_de_pago == false) ? $this->getTipoDePago() : $tipo_de_pago;
		$recibo			= $this->getCodigoDeRecibo();
		$tipo_de_pago	= strtolower($tipo_de_pago);
		//el indice de origen se actualizará dependiendo el tipo de perfiltransaccional, mayor a 99
		$origenAML		= 0;
		switch ($tipo_de_pago){
			case TESORERIA_COBRO_EFECTIVO:
				$origenAML	= ($moneda != AML_CLAVE_MONEDA_LOCAL) ? AML_OPERACIONES_PAGOS_EFVO_INT : AML_OPERACIONES_PAGOS_EFVO;
				$this->mTipoOrigenAML	= $origenAML;
				break;
			case TESORERIA_PAGO_EFECTIVO:
				$origenAML	= ($moneda != AML_CLAVE_MONEDA_LOCAL) ? AML_OPERACIONES_RETIRO_EFVO_INT : AML_OPERACIONES_RETIRO_EFVO;
				$this->mTipoOrigenAML	= $origenAML;
				break;
			default:

				if( setNoMenorQueCero($transaccion) > 0){
					//obtener Infor de la DB
					$origenAML				= setNoMenorQueCero($transaccion);
					$this->mTipoOrigenAML	= $origenAML;
					//$this->mMessages	.= "OK\tActualiz$transaccion)\r\n";
				} else {
					if($tipo_de_pago != false){
						$xTPag	= new cTesoreria_tipos_de_pago();
						$xTPag->setData($xTPag->query()->initByID($tipo_de_pago));
						if( setNoMenorQueCero($xTPag->equivalente_aml()->v()) > 0){
							$origenAML				= setNoMenorQueCero($xTPag->equivalente_aml()->v());
							$this->mTipoOrigenAML	= $origenAML;
						}
					}
				}
				break;
		}
		$sql			= "UPDATE operaciones_recibos SET  cheque_afectador='$cheque', tipo_pago='$tipo_de_pago', origen_aml = $origenAML, 
							clave_de_moneda='$moneda', unidades_en_moneda=$monto_moneda,
							cuenta_bancaria='$CuentaBancaria' 
							WHERE idoperaciones_recibos= $recibo";
		$rs 			= $xQL->setRawQuery($sql);
		//
		$this->setCleanCache();
		//Validar Moneda Extranjera
		
		if($rs === false){
			$this->mMessages	.= "ERROR\tError al actualizar el Recibo $recibo ($moneda|$tipo_de_pago|$monto_moneda|$cheque|$transaccion)\r\n";
		} else{
			$this->mMessages	.= "OK\tActualizacion correcta del Recibo $recibo ($moneda|$tipo_de_pago|$monto_moneda|$cheque|$transaccion)\r\n";
		}
		//-- AML
		$this->init();
		$this->setFinalizarRecibo(false, true);
	}
	function getDatosDeCobro(){
		$OTipo		= $this->getOTipoRecibo();
		$info		= "";
		$info		.=	strtoupper( $this->getTipoDePago() ) . "|";
		//TODO: Terminar tipos de info
		switch($OTipo->getAfectacionEnEfvo()){
			case SYS_ENTRADAS:
				$info		.= $this->getOCaja()->getChequeActivo() . "|";
				$info		.= $this->getOCaja()->getCuentaBancoActivo() . "|";
				$info		.= $this->getOCaja()->getBancoActivo() . "|";
				if($this->getOCuentaBancaria() != null){
					$xBanc		= new cBancos_entidades();
					$xBanc->setData( $xBanc->query()->initByID( $this->getOCuentaBancaria()->getClaveDeBanco() ) );
					$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";					
				}
				//$this->mCuentaBancaria	= setNoMenorQueCero($this->getOCaja()->getCuentaBancoActivo());
				//$xCtaBanc	= new cCuentaBancaria($this->mCuentaBancaria);
				//if($xCtaBanc->init() == true){
				//	$this->mOCuentaBancaria	= $xCtaBanc;
				//	$xBanc		= new cBancos_entidades();
				//	$xBanc->setData( $xBanc->query()->initByID( $xCtaBanc->getClaveDeBanco() ) );
				//	$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";					
				//}
								
				break;
			case SYS_SALIDAS:
				switch ($this->getTipoDePago()){
					case TESORERIA_PAGO_CHEQUE:
						//$xBac	= new cCuentaBancaria($numero_de_cuenta)
						//Buscar si existe
						$xOp	= new cOperacionBancaria();
						$xOp->initByRecibo($this->mCodigoDeRecibo);
						//$PorOperar	= setNoMenorQueCero($this->getTotal() - $xOp->getMonto());
						$info		.= $xOp->getNumeroDeCheque() . "|";
						$info		.= $xOp->getCuentaBancaria() . "|";
						//obtener el nombre del banco
						if($this->getOCuentaBancaria() != null){
							$xBanc		= new cBancos_entidades();
							$xBanc->setData( $xBanc->query()->initByID( $this->getOCuentaBancaria()->getClaveDeBanco() ) );
							$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";
						}
						//$this->mCuentaBancaria	= setNoMenorQueCero($xOp->getCuentaBancaria());
						//$xCtaBanc	= new cCuentaBancaria($this->mCuentaBancaria);
						//if($xCtaBanc->init() == true){
						//	$this->mOCuentaBancaria	= $xCtaBanc;
						//	$xBanc		= new cBancos_entidades();
						//	$xBanc->setData( $xBanc->query()->initByID( $xCtaBanc->getClaveDeBanco() ) );
						//	$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";
						//}
						break;
					case TESORERIA_PAGO_DOCTO:
		
						break;
					case TESORERIA_PAGO_EFECTIVO:
		
						break;
					case TESORERIA_PAGO_TRANSFERENCIA:
						$xOp	= new cOperacionBancaria();
						$xOp->initByRecibo($this->mCodigoDeRecibo);
						//$PorOperar	= setNoMenorQueCero($this->getTotal() - $xOp->getMonto());
						$info		.= $xOp->getNumeroDeCheque() . "|";
						$info		.= $xOp->getCuentaBancaria() . "|";
						if($this->getOCuentaBancaria() != null){
							$xBanc		= new cBancos_entidades();
							$xBanc->setData( $xBanc->query()->initByID( $this->getOCuentaBancaria()->getClaveDeBanco() ) );
							$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";
						}						
						//obtener el nombre del banco
						//$this->mCuentaBancaria	= setNoMenorQueCero($xOp->getCuentaBancaria());
						//$xCtaBanc	= new cCuentaBancaria($this->mCuentaBancaria);
						//if($xCtaBanc->init() == true){
						//	$this->mOCuentaBancaria	= $xCtaBanc;
						//	$xBanc		= new cBancos_entidades();
						//	$xBanc->setData( $xBanc->query()->initByID( $xCtaBanc->getClaveDeBanco() ) );
						//	$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";
						//}		
						break;
				}
				break;
		}
				
		
		
		

		return $info;
	}
	function getOCaja(){
		$xC		= new cCaja();
		if($this->mOCaja == null){
			$xC->initByRecibo($this->getCodigoDeRecibo());
			$this->mOCaja	= $xC;
		}
		return $this->mOCaja;
	}
	function getOPersona(){
		if($this->mOPersona == null AND $this->mReciboIniciado == true){
			$this->mOPersona		= new cSocio($this->mSocio); $this->mOPersona->init();
		}
		return $this->mOPersona;
	}
	function getOUsuario(){ if($this->mOUsuario == null){ $this->mOUsuario = new cSystemUser($this->mUsuario); $this->mOUsuario->init(); } return $this->mOUsuario; }
	function getPeriodo(){
		//si es inversiones o pago de credito
		if(setNoMenorQueCero($this->mPeriodoActivo) <= 0 AND $this->isPagable() == true){
			$sql					= "SELECT MAX(periodo_socio) AS 'parcialidad' FROM operaciones_mvtos WHERE recibo_afectado=" . $this->mCodigoDeRecibo;
			$this->mPeriodoActivo	= mifila($sql, "parcialidad");
		}
		return $this->mPeriodoActivo;
	}
	function isDivisaExtranjera(){
		return ( strtoupper($this->mMoneda) == strtoupper(AML_CLAVE_MONEDA_LOCAL) ) ? false : true;
	}
	function isPagable(){
		$pagable	= true;
		if($this->mTipoDePago == SYS_NINGUNO OR $this->mTipoDePago == TESORERIA_COBRO_NINGUNO OR $this->mTipoDePago == TESORERIA_PAGO_NINGUNO){
			$this->mMessages	.= "WARN\tNo pagable " . $this->mTipoDePago . "\r\n";
			$pagable = false;
		} 
		return $pagable;
	}
	function getOTipoRecibo(){
		if($this->mOTipoRecibo == null){
			$xT	= new cTipoDeRecibo($this->mTipoDeRecibo); $xT->init();
			$this->mOTipoRecibo	= $xT;
		}
		return $this->mOTipoRecibo;
	}
	function isDeEmpresa(){
		$EsEmpresa		= false;
		$personaAsoc	= $this->getPersonaAsociada();
		if($personaAsoc > 0 AND $personaAsoc != DEFAULT_EMPRESA AND $personaAsoc != DEFAULT_SOCIO){	$EsEmpresa	= true;		}
		return $EsEmpresa;
	}
	function isEfectivo(){
		$es		= false;
		if($this->mTipoDePago == TESORERIA_PAGO_EFECTIVO OR ($this->mTipoOrigenAML == AML_OPERACIONES_PAGOS_EFVO) OR ($this->mTipoOrigenAML == AML_OPERACIONES_PAGOS_EFVO_INT) ){
			$es		= true;
		}
		return $es;
	}
	function getFactura($enviar = false, $out = OUT_PDF){
		$ready		= null;
		$xml		= "";
		$unidad		= "NO APLICA";
		$cantidad	= 1; 
		$mql		= new MQL();
		$xLis		= new cSQLListas();
		$xLoc		= new cLocal();
		$xPais		= new cDomiciliosPaises(EACP_CLAVE_DE_PAIS);
		$xLog		= new cCoreLog();
		$sql		= "SELECT * FROM `operaciones_archivo_de_facturas` WHERE `clave_de_recibo` = " . $this->mCodigoDeRecibo . " LIMIT 0,1";
		$xArch		= new cOperaciones_archivo_de_facturas();
		$DFact		= $mql->getDataRow($sql);
		
		if(isset($DFact["clave_de_recibo"])){
			$xArch->setData($DFact);
			$this->mMessages	.= "OK\tEl UUID existe  " . $xArch->uuid()->v(OUT_TXT) . "\r\n";
			if($enviar == true){
				if($this->getOPersona() == null){
					
				} else {
					$xSoc			= $this->getOPersona();
					$email			= $xSoc->getCorreoElectronico();					
					$comprobante	= PATH_FACTURAS . $xArch->uuid()->v(OUT_TXT);
					file_put_contents($comprobante.".xml", base64_decode( $xArch->contenido()->v(OUT_TXT)));
					file_put_contents($comprobante.".pdf", base64_decode( $xArch->impreso()->v(OUT_TXT)));
					$ready			= ($out == OUT_PDF) ? base64_decode( $xArch->impreso()->v(OUT_TXT)) : base64_decode( $xArch->contenido()->v(OUT_TXT));
					$xNotif			= new cNotificaciones();
					//PDF y XML
					$arrFil	= array();
					$arrFil["archivo1"]["path"]		=  $comprobante . ".pdf";
					$arrFil["archivo2"]["path"]		=  $comprobante . ".xml";
						
					$xNotif->sendMail("Factura del Recibo " . $this->mCodigoDeRecibo, "Factura del Recibo " . $this->mCodigoDeRecibo, $email, $arrFil);
					//Enviar al Archivo mail
					$xNotif->sendMail("Factura del Recibo " . $this->mCodigoDeRecibo, "Factura del Recibo " . $this->mCodigoDeRecibo, FACTURACION_MAIL_ARCHIVO, $arrFil);
					$this->mMessages	.= $xNotif->getMessages();
					
				}				
			} else {
				$ready			= ($out == OUT_PDF) ? $xArch->impreso()->v(OUT_TXT) : $xArch->contenido()->v(OUT_TXT);
			}
		} else {
			$xLog->add("WARN\tGenerando Nueva Factura\r\n", $xLog->DEVELOPER);
			//cargar Archivo
			$xPais->init();
			if($this->mReciboIniciado == false){$this->init(); }
			if($this->getOPersona() == null){
				$xLog->add("ERROR\tAl cargar la Persona\r\n");
				
			} else {
				$xFact	= new cFacturaElectronica();
				//datos de la emisora
				//persona iniciada
				$xSoc	= $this->getOPersona();
				$email	= $xSoc->getCorreoElectronico();
				$xFact->setEmisor(EACP_NAME, EACP_RFC, EACP_DOMICILIO_CALLE
						, EACP_DOMICILIO_NUM_EXT, EACP_DOMICILIO_NUM_INT, EACP_CODIGO_POSTAL, EACP_COLONIA, EACP_MUNICIPIO, EACP_ESTADO, $xPais->getNombre());
				$xFact->setRegimenFiscal(EACP_REGIMEN_FISCAL);
				$calle			= $xLoc->DomicilioCalle();
				$numeroInt		= $xLoc->DomicilioNumeroInterior();
				$numeroExt		= $xLoc->DomicilioNumeroExterior();
				$codigoPostal	= $xLoc->DomicilioCodigoPostal();
				$colonia		= $xLoc->DomicilioColonia();
				$xSocDom		= $xSoc->getODomicilio();
				$pais			= $xLoc->getNombreDePais();
				$estado			= $xLoc->DomicilioEstado();
				$municipio		= $xLoc->DomicilioMunicipio();
				$tasa_iva		= TASA_IVA;
				
				$xFact->setLugarDeExpedicion($xLoc->DomicilioCalle(), $xLoc->DomicilioNumeroExterior(), 
						$xLoc->DomicilioNumeroInterior(), $xLoc->DomicilioCodigoPostal(), $xLoc->DomicilioColonia(),
					$xLoc->DomicilioMunicipio(), $xLoc->DomicilioEstado(), $xLoc->getNombreDePais());
				
				
				if($xSocDom == null){
					$xLog->add("WARN\tNo hay domicilio Valido\r\n", $xLog->DEVELOPER);
				} else {
					$calle			= $xSocDom->getCalle();
					$numeroExt		= $xSocDom->getNumeroExterior();
					$numeroInt		= $xSocDom->getNumeroInterior();
					$codigoPostal	= $xSocDom->getCodigoPostal();
					$colonia		= $xSocDom->getColonia();
					$municipio		= $xSocDom->getMunicipio();
					$estado			= $xSocDom->getEstado();
					$pais			= $xSocDom->getNombreDePais();
				}
				//Cargar datos del Docto
				//$this->getOrigen();
				$OTipoRec			= $this->getOTipoRecibo();
				if($OTipoRec->getOrigen() == RECIBOS_ORIGEN_MIXTO OR $OTipoRec->getOrigen() == RECIBOS_ORIGEN_COLOCACION){
					$xCred			= new cCredito($this->getCodigoDeDocumento()); $xCred->init();
					$tasa_iva		= $xCred->getTasaIVA();
					$xLog->add("WARN\tLa tasa de IVA es $tasa_iva\r\n", $xLog->DEVELOPER);
				}
				$xFact->setReceptor($xSoc->getNombreCompleto(), $xSoc->getRFC(true, true), $calle, $numeroExt, $numeroInt, $codigoPostal, $colonia, $municipio,$estado, $pais);
				
				
				
				//Datos del pagos
				$formaDePago		= "Pago en una sola exhibición";
				
				$cuentaDePago		= "No Identificado";
				$arrEquiv			= array(
					TESORERIA_COBRO_DOCTO => "Documentos",
					TESORERIA_COBRO_EFECTIVO => "Efectivo",
					TESORERIA_COBRO_INTERNO => "Documentos",
					TESORERIA_COBRO_MULTIPLE => "Documentos",
					TESORERIA_COBRO_NINGUNO => "Documentos",
					TESORERIA_COBRO_TRANSFERENCIA => "Transferencia",
					TESORERIA_COBRO_CHEQUE		=> "Cheque"
				);
				$metodoDePago		= $arrEquiv[$this->getTipoDePago()]; //cargar equivalencias
				//TODO: Considerar cambios en otro de tipo de tributacion
				if($this->getTipoDePago() == TESORERIA_COBRO_TRANSFERENCIA OR $this->getTipoDePago() == TESORERIA_COBRO_CHEQUE){
					$OCaja			= $this->getOCaja();
					$cuentaDePago	= $OCaja->getCuentaBancoActivo();
				}
				//Cheque, Transferencia, Depósito
				$xFact->setDatosDePago($formaDePago, $metodoDePago, $cuentaDePago);
				$sql				= "SELECT
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`operaciones_mvtos`.`recibo_afectado`,
					`eacp_config_bases_de_integracion_miembros`.`miembro`,
					`eacp_config_bases_de_integracion_miembros`.`subclasificacion`,
					`operaciones_tipos`.`descripcion_operacion` AS `operacion`,
					`operaciones_mvtos`.`fecha_operacion`,
					`operaciones_mvtos`.`afectacion_real` AS 'monto'  
				FROM
					`operaciones_mvtos` `operaciones_mvtos` 
						INNER JOIN `eacp_config_bases_de_integracion_miembros` 
						`eacp_config_bases_de_integracion_miembros` 
						ON `operaciones_mvtos`.`tipo_operacion` = 
						`eacp_config_bases_de_integracion_miembros`.`miembro` 
							INNER JOIN `operaciones_tipos` `operaciones_tipos` 
							ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
							`idoperaciones_tipos` 
				WHERE
					(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =11000) AND
					(`operaciones_mvtos`.`recibo_afectado` =" . $this->mCodigoDeRecibo . " )";
				$iva	= 0;
				$total	= 0;
				$rs		= $mql->getDataRecord( $sql );
				foreach ($rs as $rows){
					$descripcion	= $rows["operacion"];
					$valor			= $rows["monto"];
					$tipo			= $rows["subclasificacion"];
					$total			+= $valor;
					if($tipo == 1000){
						$iva		+= $rows["monto"];
					} else {
						$xFact->addConcepto($cantidad, $unidad, $valor, $descripcion);
					}
				}
				//agregar IVA
				if($iva > 0){
					$xFact->addTrasladado("IVA", $tasa_iva, $iva);
				}
				$xml		= $xFact->get();
				$this->mOFactura	= $xFact;
				if($total <= 0){
					$enviar			= false;
				}
				
			}
			if($enviar == true){
				if($this->mOFactura != null){
					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
						if($this->mOFactura->timbrar() == 1){
							//ready = true
							$xNotif	= new cNotificaciones();
							//PDF y XML
							$arrFil	= array();
							$arrFil["archivo1"]["path"]		=  $this->mOFactura->getComprobante() . ".pdf";
							$arrFil["archivo2"]["path"]		=  $this->mOFactura->getComprobante() . ".xml";
							
							$xNotif->sendMail("Factura del Recibo " . $this->mCodigoDeRecibo, "Factura del Recibo " . $this->mCodigoDeRecibo, $email, $arrFil);
							//Enviar al Archivo mail
							$xNotif->sendMail("Factura del Recibo " . $this->mCodigoDeRecibo, "Factura del Recibo " . $this->mCodigoDeRecibo, FACTURACION_MAIL_ARCHIVO, $arrFil);
							//Guardar en DB
							$this->mOFactura->setArchivar($this->mCodigoDeRecibo);
							$xLog->add($xNotif->getMessages(), $xLog->DEVELOPER);
							$ready				= ($out == OUT_PDF) ? $this->mOFactura->getPDF() :$this->mOFactura->getXML() ;
						}
					} else {
						$xLog->add("WARN\tNo hay email valido para la Factura ($email)\r\n", $xLog->DEVELOPER);
					}
					$xLog->add($this->mOFactura->getMessages(), $xLog->DEVELOPER);
				}
			}
		}
		$this->mMessages	.= $xLog->getMessages();
		return $ready;
	}
	function OFactura(){ if($this->mOFactura == null) { $this->getFactura(); }; return $this->mOFactura; }
	function getSaldoEnCaja(){
		//cargar si es egreso o ingreso
		$OTipo		= $this->getOTipoRecibo();
		$PorOperar	= 0;
		//setLog($OTipo->getAfectacionEnEfvo());
		switch($OTipo->getAfectacionEnEfvo()){
			case SYS_ENTRADAS:
				$xCa	= $this->getOCaja();
				$pagado	= $xCa->getReciboEnCorte($this->mCodigoDeRecibo);
				
				$PorOperar	= setNoMenorQueCero($this->getTotal() - $pagado);
				break;
			case SYS_SALIDAS:
				switch ($this->getTipoDePago()){
					case TESORERIA_PAGO_CHEQUE:
						//$xBac	= new cCuentaBancaria($numero_de_cuenta)
						//Buscar si existe
						$xOp	= new cOperacionBancaria();
						$xOp->initByRecibo($this->mCodigoDeRecibo);
						$PorOperar	= setNoMenorQueCero($this->getTotal() - $xOp->getMonto());
						break;
					case TESORERIA_PAGO_DOCTO:
						
						break;
					case TESORERIA_PAGO_EFECTIVO:
						
						break;
					case TESORERIA_PAGO_TRANSFERENCIA:
						$xOp	= new cOperacionBancaria();
						$xOp->initByRecibo($this->mCodigoDeRecibo);
						$PorOperar	= setNoMenorQueCero($this->getTotal() - $xOp->getMonto());												
						break;
				}
				break;
		}
		return $PorOperar;
	}
	function getOCuentaBancaria(){
		if($this->mOCuentaBancaria == null){
			$xF	= new cFecha();
			if( ($xF->getInt($this->mFechaDeCaptura) < $xF->getInt("2015-06-30")) AND $this->mCuentaBancaria <= FALLBACK_CUENTA_BANCARIA){
				//Actualizar Banco
				$this->mOCuentaBancaria	= $this->getBancoPorOperacion();
				if($this->mOCuentaBancaria != null){
					$CuentaBancaria	= $this->mOCuentaBancaria->getNumeroDeCuenta();
					$xQL			= new MQL();
					$xQL->setRawQuery("UPDATE `operaciones_recibos` SET `cuenta_bancaria`=$CuentaBancaria WHERE`idoperaciones_recibos`=" . $this->getCodigoDeRecibo());
					$this->mCuentaBancaria	= $CuentaBancaria;
					$this->mMessages	.= "OK\tLa Cuenta Bancaria Iniciada es "  . $CuentaBancaria .  " en primera opcion\r\n";
				} else {
					$this->mMessages	.= "ERROR\tSe falla al obtener la Cuenta por Operacion\r\n";
				}
			} else {
				$this->mOCuentaBancaria	= new cCuentaBancaria($this->mCuentaBancaria);
				if($this->mOCuentaBancaria->init() == true){
					$this->mMessages	.= "OK\tLa Cuenta Bancaria Iniciada es "  . $this->mCuentaBancaria .  "\r\n";
				} else {
					$this->mMessages	.= $this->mOCuentaBancaria->getMessages();
				}
			}
		} else {
			$this->mMessages	.= $this->mOCuentaBancaria->getMessages();
		}
		return $this->mOCuentaBancaria;
	}
	function getSucursal(){ return $this->mSucursal; }
	
	function setCuandoSeActualiza(){
		$this->setCleanCache();
		//Actualizar Saldo
		$this->setForceUpdateSaldos(true);
		$this->setFinalizarRecibo(true);
		//Actualizar Relacionados
		switch ($this->getOrigen()){
			case TESORERIA_RECIBOS_ORIGEN_CAPT:
				$xCapt	= new cCuentaDeCaptacion($this->getCodigoDeDocumento());
				if($xCapt->init() == true){
					$xCapt->setCuandoSeActualiza();
					$this->mMessages	.= $xCapt->getMessages();
				}				
				break;
			case TESORERIA_RECIBOS_ORIGEN_CRED:
				$xCred	= new cCredito($this->getCodigoDeDocumento());
				if($xCred->init() == true){
					$xCred->setCuandoSeActualiza();
					$this->mMessages	.= $xCred->getMessages();
				}				
				break;
			case TESORERIA_RECIBOS_ORIGEN_MIXTO:
				//Actualizar Credito
				$xCred	= new cCredito($this->getCodigoDeDocumento());
				if($xCred->init() == true){
					$xCred->setCuandoSeActualiza();
					$this->mMessages	.= $xCred->getMessages();
				}
				$xCapt	= new cCuentaDeCaptacion($this->getCodigoDeDocumento());
				if($xCapt->init() == true){
					$xCapt->setCuandoSeActualiza();
					$this->mMessages	.= $xCapt->getMessages();
				}
				break;
		}
	}
	function setMontoHistorico($monto){
		$xQL	= new MQL();
		$monto	= setNoMenorQueCero($monto);
		$xQL->setRawQuery("UPDATE `operaciones_recibos` SET `montohist`=$monto WHERE `idoperaciones_recibos`=" . $this->mCodigoDeRecibo);
		$this->setCleanCache();
	}
	function getAfectacionEnCaja(){
		if($this->mAfectaEnCaja === false){
			if($this->getOTipoRecibo() !== null){
				$this->mAfectaEnCaja 	= $this->getOTipoRecibo()->getAfectacionEnEfvo();
			}
		}
		return $this->mAfectaEnCaja;
	}
}

class cMovimientoDeOperacion{
	private $mCodigo			= 0;
	private $mArrayData			= array();
	private $mInit				= false;
	private $mMessages			= "";
	private $mEstado			= 30;
	private $mValorAfectacion	= 1;
	private $mActualizarEst		= false;
	private $mActualizarAfec	= false;
	private $mDoctoNeutral		= 1;
	private $mActualizarNeutral	= false;

	function __construct($codigo = false){ $this->mCodigo		= setNoMenorQueCero($codigo); }
	function init($arrInicial = false){
		$sqlM = "SELECT
					`operaciones_mvtos`.*,
					`operaciones_tipos`.*,
					`operaciones_mvtos`.`recibo_afectado`
				FROM
					`operaciones_mvtos` `operaciones_mvtos`
						INNER JOIN `operaciones_tipos` `operaciones_tipos`
						ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
						`idoperaciones_tipos`
				WHERE
					(`operaciones_mvtos`.`idoperaciones_mvtos` =" . $this->mCodigo . ")";
			if($arrInicial != false AND is_array($arrInicial)){
				$DR		= $arrInicial;
			} else {
				$DR		= obten_filas($sqlM);
			}
			$this->mArrayData	= $DR;
			$this->mInit		= true;
	}
	function setRevertir(){
			$sucess			= true;
			$arrValuesRev	= array("-1" => "1", "1" => "-1", "0" => "0");
			if ( $this->mInit == false ){
				$this->init();
			}
			$rw				= $this->mArrayData;
			$codigo			= $rw["idoperaciones_mvtos"];
			$docto			= $rw["docto_afectado"];
			$socio			= $rw["socio_afectado"];
			$preservar_mvto = $rw["preservar_movimiento"];
			$CodeRevertir	= $rw["formula_de_cancelacion"];
			$monto			= $rw["afectacion_real"];
			$afectacion		= $rw["valor_afectacion"];
			$recibo			= $rw["recibo_afectado"];
			$colocacion		= array();
			$captacion		= array();
			//selecciona un comportamiento segun el Origen del Recibo
			switch ($this->mAplicadoA){
				case "colocacion":
					//cargar datos del credito
                             $Credito          = new cCredito($docto, $socio);
                             $Credito->initCredito();
                             $colocacion     = $Credito->getDatosDeCredito();
						break;
				case "captacion":
					//cargar datos de la cuenta
                             $Cuenta           = new cCuentaALaVista($docto);
                             $Cuenta->initCuentaByCodigo();
                             $captacion      = $Cuenta->getDatosInArray();
						break;
				case "mixto":
					//cargar datos de la cuenta y del credito
                             $Credito          = new cCredito($docto, $socio);
                             $Credito->initCredito();
                             $colocacion     = $Credito->getDatosDeCredito();
                             $Cuenta           = new cCuentaALaVista($docto);
                             $Cuenta->initCuentaByCodigo();
                             $captacion      = $Cuenta->getDatosInArray();
                             $this->mMessages	.= "WARN\tEL Recibo es Mixto, se carga tanto Captacion como Colocacion\r\n";
					break;
				default:
                             $this->mMessages	.= "ERROR\tEL Recibo es " . $this->mAplicadoA . ", NO SE CARGA CODIGO\r\n";
					break;
			}
			eval( $CodeRevertir );
			if($preservar_mvto=='1'){
				$SQL_DM = "UPDATE operaciones_mvtos
						SET afectacion_estadistica=afectacion_real,
						afectacion_real = 0, afectacion_contable=0,
						afectacion_cobranza=0, valor_afectacion=0,
						estatus_mvto = 99,
						docto_neutralizador = " . DEFAULT_RECIBO  .",
						recibo_afectado	= " . DEFAULT_RECIBO . "
						WHERE idoperaciones_mvtos = $codigo
						";
				$this->mMessages	.= "Actualizando el Movimiento $codigo\r\n";
			} else {
				$SQL_DM = "DELETE FROM operaciones_mvtos
						WHERE idoperaciones_mvtos = $codigo";
				$this->mMessages	.= "Eliminado el Movimiento $codigo\r\n";
			}
			my_query($SQL_DM);
	}
	function setEliminarRAW($tipo = false, $credito = false, $persona = false, $recibo = false, $periodo = false){
		$sucess		= false;
		$sql		= "DELETE FROM operaciones_mvtos WHERE idoperaciones_mvtos != 0 ";
		$sql		.= (setNoMenorQueCero($tipo) <= 0) ?  "" : " AND (tipo_operacion = $tipo) ";
		$sql		.= (setNoMenorQueCero($credito) <= 0) ?  "" : " AND (docto_afectado = $credito) ";
		$sql		.= (setNoMenorQueCero($persona) <= 0) ?  "" : " AND (socio_afectado=$persona) ";
		$sql		.= (setNoMenorQueCero($recibo) <= 0 ) ?  "" : " AND (recibo_afectado=$recibo) ";
		$sql		.= (setNoMenorQueCero($periodo) <= 0 ) ?  "" : " AND (periodo_socio=$periodo) ";
		$rs			= my_query($sql);
		$sucess		= $rs[SYS_ESTADO];
		if($sucess	== true ){
			$this->mMessages	.= "OK\tDEL_MVTO:Operacion Eliminada ($persona-$credito-$recibo-$tipo-$periodo)\r\n";
		}
		if(MODO_DEBUG == true){ $this->mMessages	.= $rs[SYS_MSG]; }
		return $sucess;
	}
	function setNeutralizarRAW($tipo = false, $credito = false, $persona = false, $recibo = false, $periodo = false){
		$sucess		= false;
		$sql		= "UPDATE operaciones_mvtos SET afectacion_real=0 WHERE idoperaciones_mvtos != 0 ";
		$sql		.= (setNoMenorQueCero($tipo) <= 0) ?  "" : " AND (tipo_operacion = $tipo) ";
		$sql		.= (setNoMenorQueCero($credito) <= 0) ?  "" : " AND (docto_afectado = $credito) ";
		$sql		.= (setNoMenorQueCero($persona) <= 0) ?  "" : " AND (socio_afectado=$persona) ";
		$sql		.= (setNoMenorQueCero($recibo) <= 0 ) ?  "" : " AND (recibo_afectado=$recibo) ";
		$sql		.= (setNoMenorQueCero($periodo) <= 0 ) ?  "" : " AND (periodo_socio=$periodo) ";
		$xQL		= new MQL();
		$rs			= $xQL->setRawQuery($sql);
		$sucess		= ($rs === false) ? false : true;
		if($sucess	== true ){
			$this->mMessages	.= "OK\tUP_MVTO\tOperacion Actualizada ($persona-$credito-$recibo-$tipo-$periodo)\r\n";
		}
		
		return $sucess;
	}
	function setActualizaRAW($tipo = false, $monto = "", $operador = "+", $credito = false, $persona = false, $recibo = false, $periodo = false){
		$sucess				= false;
		$UpdEstado			= ($this->mActualizarEst == false) ? "" : ", `estatus_mvto`=" . $this->mEstado;
		$UpdAfecta			= ($this->mActualizarAfec == false) ? "" : " ,`valor_afectacion`= " . $this->mValorAfectacion;
		$UpdDoctoNeutral	= ($this->mActualizarNeutral == false) ? "" : ", `docto_neutralizador`=" . $this->mDoctoNeutral;
		$sql		= "UPDATE `operaciones_mvtos` SET `afectacion_real`=setNoMenorCero(`afectacion_real` $operador $monto), `afectacion_estadistica`=setNoMenorCero(`afectacion_estadistica` $operador $monto) $UpdEstado $UpdAfecta $UpdDoctoNeutral WHERE `idoperaciones_mvtos` != 0 ";
		$sql		.= (setNoMenorQueCero($tipo) <= 0) ?  "" : " AND (tipo_operacion = $tipo) ";
		$sql		.= (setNoMenorQueCero($credito) <= DEFAULT_CREDITO) ?  "" : " AND (docto_afectado = $credito) ";
		$sql		.= (setNoMenorQueCero($persona) <= DEFAULT_SOCIO) ?  "" : " AND (socio_afectado=$persona) ";
		$sql		.= (setNoMenorQueCero($recibo) <= 0 ) ?  "" : " AND (recibo_afectado=$recibo) ";
		$sql		.= (setNoMenorQueCero($periodo) <= 0 ) ?  "" : " AND (periodo_socio=$periodo) ";
		$rs			= my_query($sql);
		
		$sucess		= $rs[SYS_ESTADO];
		if($sucess	== true ){
			$this->mMessages	.= "OK\tUP_MVTO\tOperacion Actualizada ($persona-$credito-$recibo-$tipo-$periodo)\r\n";
		}
		if(MODO_DEBUG == true){ $this->mMessages	.= $rs[SYS_MSG]; }
		return $sucess;
	}	
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function setActualizarEstado($estado = 30){ $this->mEstado = $estado; $this->mActualizarEst = true; }
	function setActualizarAfecta($ValorAfectacion = 1){$this->mValorAfectacion= $ValorAfectacion; $this->mActualizarAfec = true; }
	function setActualizarDNeutral($DoctoNeutralizador = 1){ $this->mDoctoNeutral = $DoctoNeutralizador; $this->mActualizarNeutral = true; }
	function add($monto){
		$xOp	= new cOperaciones_mvtos();
		$xOp->afectacion_cobranza(0);
		$xOp->afectacion_contable(0);
		$xOp->afectacion_estadistica($monto);
		$xOp->afectacion_real($monto);
		
	}
	function setNeutralizarBatch($montoInit, $documento, $tipo, $periodoInit = 0){
		$xQL	= new MQL();
		$rs		= $xQL->getRecordset("SELECT * FROM `operaciones_mvtos` WHERE `docto_afectado`=$documento AND `periodo_socio`>=$periodoInit AND `tipo_operacion`=$tipo LIMIT 0,100");
		if($rs){
			while($rw = $rs->fetch_assoc() ){
				$xMov	= new cOperaciones_mvtos();
				$xMov->setData($rw);
				$id		= $xMov->idoperaciones_mvtos()->v();
				$monto	= $xMov->afectacion_real()->v();
				
				if($montoInit > 0){
					if($monto >= $montoInit){
						$monto		= round(($monto - $montoInit),2);
						$montoInit	= 0;
						//echo "<h2>2.- Monto a matar $monto , Monto Restante $montoInit</h2>";
						$xQL->setRawQuery("UPDATE `operaciones_mvtos` SET `afectacion_real`=$monto, `afectacion_estadistica`=$monto WHERE `idoperaciones_mvtos`=$id");
					} else {
						$montoInit	= round(($montoInit - $monto),2);
						//echo "<h3>3.- Monto a matar $monto , Monto Restante $montoInit</h3>";
						$xQL->setRawQuery("UPDATE `operaciones_mvtos` SET `afectacion_real`=0, `afectacion_estadistica`=0 WHERE `idoperaciones_mvtos`=$id");
					}
				} else {
					//echo "<h3>A la Mierda</h3>";
					break;
				}
			}
		}
	}
}

class cTipoDeOperacion{
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mTasaIVA	= 0;
	private $mPrecio	= 0;
	private $mIDCache	= "";
	function __construct($clave = false){
		$this->mClave	= setNoMenorQueCero($clave);
		$this->mTasaIVA	= TASA_IVA;
	}
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= TOPERACIONES_TIPOS . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache			= new cCache();
		if(!is_array($data)){
			$data		= $xCache->get($this->mClave);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `operaciones_tipos` WHERE `idoperaciones_tipos`=". $this->mClave);
			}
		}
		if(isset($data["idoperaciones_tipos"])){
			$this->mObj		= new cOperaciones_tipos(); //Cambiar
			$this->mObj->setData($data);
			$this->mNombre	= $this->mObj->descripcion_operacion()->v();
			$this->mTasaIVA	= $this->mObj->tasa_iva()->v();
			$this->mClave	= $this->mObj->idoperaciones_tipos()->v();
			$this->mPrecio	= $this->mObj->precio()->v();
			$this->setIDCache($this->mClave);
			$this->mInit	= true;
			$xCache->set($this->getIDCache(), $data);
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getExigenciaEnPagTotCred($ClaveProducto){
		$xQL	= new MQL();
		$res	= false;
		$DD		= $xQL->getDataRow("SELECT * FROM `creditos_productos_costos` WHERE `clave_de_producto`=$ClaveProducto AND `clave_de_operacion`=" . $this->mClave . " LIMIT 0,1");
		if(isset($DD["exigencia"])){
			$res= ($DD["exigencia"] == 1) ? true : false;
		}
		return $res;
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function getTasaIVA(){return $this->mTasaIVA;}
	function getPrecio(){ return $this->mPrecio; }
	function setEsOtrosIngresos(){
		$this->getObj()->recibo_que_afecta(RECIBOS_TIPO_OINGRESOS);
		$this->getObj()->query()->update()->save($this->getObj()->idoperaciones_tipos()->v());
		$this->setCleanCache();
	}
	function setEsOtrosEgresos(){
		//Cambiar estadistico.- Por defecto se guarda
		//$xO->es_estadistico("0");//Si es estadistico, operacion base estadisticos.- Obsoleto pero usado.
		//Agregar clase efectivo
		//Cambiar el recibo que afecta
		$this->getObj()->recibo_que_afecta(RECIBOS_TIPO_OEGRESOS);
		$this->getObj()->query()->update()->save($this->getObj()->idoperaciones_tipos()->v());
		$this->setCleanCache();
	}	
	function add($nombre, $numero = false, $alias = "", $clonarDe = false, $precio = 0){
		$numero		= setNoMenorQueCero($numero);
		$alias		= ($alias == "") ? $nombre : $alias;
		$clonarDe	= setNoMenorQueCero($clonarDe);
		
		/*insert  into `operaciones_tipos`(`idoperaciones_tipos`,`descripcion_operacion`,`clasificacion`,`subclasificacion`,`cuenta_contable`,`descripcion`,`recibo_que_afecta`,`tipo_operacion`,`visible_reporte`,`class_efectivo`,`mvto_que_afecta`,`afectacion_en_recibo`,`afectacion_en_notificacion`,`producto_aplicable`,`constituye_fondo_automatico`,`integra_vencido`,`afectacion_en_sdpm`,`cargo_directo`,`codigo_de_valoracion`,`periocidad_afectada`,`integra_parcialidad`,`es_estadistico`,`formula_de_calculo`,`formula_de_cancelacion`,`importancia_de_neutralizacion`,`preservar_movimiento`,`tasa_iva`,`nombre_corto`,`estatus`)
		 * values
		 * (99,'NO DETERMINADO',99,99,'$cuenta = CUENTA_DE_CUADRE;','Ingresos no Clasificados',999,99,1,1,99,0,0,0,'0','1',0,0,'','ninguna','0','1','','',0,'0',0.000,'',1)*/
		$xO	= new cOperaciones_tipos();
		if($numero <=0){
			$numero	= $xO->query()->getLastID();
		}		
		if($clonarDe > 0){
			$DD		= $xO->query()->initByID($clonarDe);
			$xO->setData($DD);
			$xO->descripcion($nombre);
			$xO->descripcion_operacion($nombre);
			$xO->idoperaciones_tipos($numero);
			$xO->nombre_corto($alias);
			$xO->tipo_operacion($numero);
			$xO->estatus("1");//Activo 1
		} else {
			$xO->afectacion_en_notificacion("0");
			$xO->afectacion_en_recibo("0");
			$xO->afectacion_en_sdpm("0");
			$xO->cargo_directo("0");
			$xO->clasificacion("0");//Tipo de producto que afecta.- Obsoleto
			$xO->class_efectivo("1");//Base de efectivo.- Obsoleto pero usado penosamente .- 8 es descuentos
			$xO->codigo_de_valoracion("");//Javascript.- Obsoleto
			$xO->constituye_fondo_automatico("0");//Base Fondos automaticos.- obsoleto, pero usado
			$xO->cuenta_contable("\$cuenta = CUENTA_DE_CUADRE;");//CUENTA DE CUADRE
			$xO->descripcion($nombre);
			$xO->descripcion_operacion($nombre);
			$xO->es_estadistico("0");//Si es estadistico, operacion base estadisticos.- Obsoleto pero usado.
			$xO->estatus("1");//Activo 1
			$xO->formula_de_calculo("");//Pre formula de calculo en PHP
			$xO->formula_de_cancelacion("");//Formula de Cancelacion en PHP
			$xO->importancia_de_neutralizacion("0");//Orden en que se Cancela, en recibos.
	
			$xO->idoperaciones_tipos($numero);
			$xO->integra_parcialidad("0");//Si es parte de la parcialidad de pago.- Obsoleto.- en uso
			$xO->integra_vencido("0");//Si integra base vencidos.- Obsoleto
			$xO->mvto_que_afecta("99");//Ninguno.-
			$xO->nombre_corto($alias);
			$xO->periocidad_afectada("ninguna");//No m acuerdo :p ninguna todas vencimiento periodico
			$xO->preservar_movimiento("0");//Si no
			$xO->producto_aplicable("0");//Producto de Credito 2.- Captacio  21.- captacion corriente.- obsoleto y usado
			$xO->recibo_que_afecta("999");//Tipo de recibo que integra.- Obsoleto 999.- Ninguno
			$xO->subclasificacion("0");//No tiene un finalidad.- Obsoleto
			
			$xO->tasa_iva(0);			//tasa de IVA
			$xO->tipo_operacion($numero);//cagada
			$xO->visible_reporte("0");//Visible en reportes.- Obsoleto.
		}
		$xO->precio($precio);
		
		$rs		= $xO->query()->insert()->save();
		if($rs != false){
			$this->mClave	= $numero;
		}
		return ($rs == false) ? false : $rs;
	}
}
class cTipoDeRecibo{
	private $mCodigo			= 99;
	private $mPathFormato		= "404.php";
	private $mTipoPolizaCont	= 999; 
	private $aDatos				= array();
	private $mOrigen			= "mixto";
	private $mAfectacionEfvo	= 0;
	private $mNombre			= "";
	
	public $ORIGEN_MIXTO		= "mixto";
	public $ORIGEN_COLOCACION	= "colocacion";
	public $ORIGEN_CAPTACION	= "captacion";
	public $ORIGEN_OTROS		= "otros";
	function __construct($tipo){
		$this->mCodigo	= setNoMenorQueCero($tipo);
		if($this->mCodigo>0){
			$this->setIDCache($this->mCodigo);
			$this->init();
		}
	}
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mCodigo : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= TOPERACIONES_RECIBOSTIPOS . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== "" AND $this->mCodigo>0){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$arrEq	= array(
			"aumento" 		=> 1,
			"disminucion" 	=> -1,
			"" 				=> 0,
			"ninguno" 		=> 0,
			"ninguna"		=> 0,
		);
		$xCache	= new cCache();
		$xT		= new cOperaciones_recibostipo();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mCodigo . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT;
			$this->mCodigo			= $data[$xT->getKey()];

			$this->aDatos			= $data;
			$this->mPathFormato		= $this->aDatos["path_formato"];
			$this->mTipoPolizaCont	= $this->aDatos["tipo_poliza_generada"];
			$this->mOrigen			= $this->aDatos["origen"];
			//ninguna disminucion aumento
			$this->mAfectacionEfvo	= $arrEq[ $this->aDatos["afectacion_en_flujo_efvo"] ];
			$this->mNombre			= $this->aDatos["descripcion_recibostipo"];
			
			$this->setIDCache($this->mCodigo);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit			= true;
			$xT 					= null;
		}
	}
	function getPathForma(){ return $this->mPathFormato;	}
	function getTipoPolizaContable(){ return $this->mTipoPolizaCont; }
	function getOrigen(){ return $this->mOrigen; }
	function getAfectacionEnEfvo(){ return $this->mAfectacionEfvo; }
	function isEstadistico(){
		$esta	= false;
		if($this->getTipoPolizaContable() == CONTABLE_TIPO_POLIZA_NINGUNA){$esta = true; }
		return $esta;
	}
	function getNombre(){ return $this->mNombre; }
	function getEsDeColocacion(){
		return ($this->mOrigen == $this->ORIGEN_MIXTO OR $this->mOrigen == $this->ORIGEN_COLOCACION) ? true : false;
	}
	function getEsDeCaptacion(){
		return ($this->mOrigen == $this->ORIGEN_MIXTO OR $this->mOrigen == $this->ORIGEN_COLOCACION) ? true : false;
	}
}
class cFacturaElectronica {
	private $mEmisor			= "";
	private $mReceptor			= "";
	private $mConceptos			= "";
	private $mSubtotal			= 0;
	private $mSumaConceptos		= 0;
	private $mSumaTrasladados	= 0;
	private $mRegimenFiscal		= "";
	private $mLugarExp			= "";
	private $mImptosTras		= "";
	private $mDatosDePago		= "";
	private $mHeadLugarExp		= "";
	private $mRFC				= "";
	private $mPEM				= "";
	private $mCERT				= "";
	private $mMessages			= "";
	private $mComprobante		= "";
	private $mXML				= "";
	private $mPDF				= "";
	private $mUUID				= "";
	//private $mDatosDePago		= "";
	
	public $REGIMEN_NO_APLICA		= "No aplica";
	public $REGIMEN_PM_GRAL_DE_LEY	= "REGIMEN GENERAL DE LEY PERSONAS MORALES";
	function __construct(){
		$this->mPEM		= PATH_CERTS . FACTURACION_ARCHIVO_PEM;
		$this->mCERT	= PATH_CERTS . FACTURACION_ARCHIVO_CERT;
	}
	function setEmisor($nombre, $rfc, $calle, $numeroExt, $numeroInt, $codigoPostal, $colonia = "", $municipio = "", $estado = "", $pais = ""){
		$this->mRFC		= $rfc;
		$this->mEmisor	= '<cfdi:Emisor nombre="' . strtoupper($nombre)  .  '" rfc="' . strtoupper($rfc) . '">
		<cfdi:DomicilioFiscal calle="' . strtoupper($calle) . '" noExterior="' . strtoupper($numeroExt) . '" 
		colonia="' . strtoupper($colonia) . '" 
		municipio="' . strtoupper($municipio) . '" 
		estado="' . strtoupper($estado) . '" 
		pais="' . strtoupper($pais) . '" 
		codigoPostal="' . $codigoPostal . '" />';
	}
	function setLugarDeExpedicion($calle, $numeroExt, $numeroInt, $codigoPostal, $colonia = "", $municipio = "", $estado = "", $pais = ""){	
		if(setNoMenorQueCero($codigoPostal) > 0 AND $colonia == ""){
			$xCol	= new cDomiciliosColonias();
			$xLoc	= new cLocal();
			$idcol	= $xCol->getClavePorCodigoPostal($codigoPostal);
			$colonia= $xCol->getNombre();
			$estado		= $xCol->getNombreEstado();
			$municipio	= $xCol->getNombreMunicipio();
			$pais		= ($pais == "") ? $xLoc->getNombreDePais() : $pais;
		}
				
		$this->mLugarExp = '<cfdi:ExpedidoEn calle="' . strtoupper($calle) . '" 
		noExterior="' . setCadenaVal($numeroExt) . '" 
		colonia="' . setCadenaVal($colonia) . '" 
		municipio="' . setCadenaVal($municipio) . '" 
		estado="' . setCadenaVal($estado) . '" 
		pais="' . setCadenaVal($pais) . '" 
		codigoPostal="' . $codigoPostal . '" />';
		$this->mHeadLugarExp			= ' LugarExpedicion="' . setCadenaVal($municipio) .',' . setCadenaVal($estado) . '" ';
	}
	function setRegimenFiscal($regimen = ""){	$this->mRegimenFiscal = '<cfdi:RegimenFiscal Regimen="' . strtoupper($regimen) . '" />'; }
	function setReceptor($nombre, $rfc, $calle, $numeroExt, $numeroInt,$codigoPostal, $colonia = "", $municipio = "", $estado = "", $pais = ""){
		if(setNoMenorQueCero($codigoPostal) > 0 AND $colonia == ""){
			$xCol	= new cDomiciliosColonias();
			$xLoc	= new cLocal();
			$idcol	= $xCol->getClavePorCodigoPostal($codigoPostal);
			$colonia= $xCol->getNombre();
			$estado		= $xCol->getNombreEstado();
			$municipio	= $xCol->getNombreMunicipio();
			$pais		= ($pais == "") ? $xLoc->getNombreDePais() : $pais;	
		}
		$sInterior		=($numeroInt == "") ? "" : ' noInterior="' . strtoupper($numeroInt) . '" ';
		$this->mReceptor .= '<cfdi:Receptor 
		nombre="' . strtoupper($nombre)  .  '" 
		rfc="' . strtoupper($rfc) . '">
		<cfdi:Domicilio calle="' . setCadenaVal($calle) . '" 
		noExterior="' . setCadenaVal($numeroExt) . '" 
		' . $sInterior . '
		colonia="' . setCadenaVal($colonia) . '" 
		municipio="' . setCadenaVal($municipio) . '" 
		estado="' . setCadenaVal($estado) . '" pais="' . setCadenaVal($pais) . '" 
		codigoPostal="' . $codigoPostal . '" /></cfdi:Receptor>';
	}
	
	function getConceptos(){ return "<cfdi:Conceptos>\n" . $this->mConceptos . "</cfdi:Conceptos>"; 	}
	function addConcepto($cantidad, $unidad, $valor, $descripcion,  $identificacion = "" ){
		$identificacion	= ($identificacion == "") ? "" : " noIdentificacion=\"" . htmlentities($identificacion) . "\"  ";
		$importe		= $cantidad * $valor;
		$this->mSumaConceptos	+= $importe;
		$rvalor			= money_format('%i', $importe);
		$ivalor			= money_format('%i', $valor);
		$xml			= "<cfdi:Concepto cantidad=\"$cantidad\" unidad=\"$unidad\" $identificacion descripcion=\"" . htmlentities($descripcion) . "\" valorUnitario=\"$ivalor\" importe=\"$rvalor\" />\n";
		$this->mConceptos	.= $xml;
	}
	function addTrasladado ($nombre, $tasa, $importe){
		$tasa				= $tasa * 100;
		$tasa				= money_format('%i', $tasa);
		 	
		$this->mImptosTras	.= '<cfdi:Traslado impuesto="' . strtoupper($nombre) . '" tasa="' . $tasa . '" importe="' . $importe . '"></cfdi:Traslado>'; 
		$this->mSumaTrasladados += $importe;
	}
	function getEmisor(){ return $this->mEmisor . $this->mLugarExp . $this->mRegimenFiscal . "</cfdi:Emisor>";  }
	function getReceptor(){ return $this->mReceptor; }
	function setDatosDePago($formaDePago = "", $metodoDePago = "", $numeroCtaDePago = "No Identificado"){
		//Pago en una sola exhibición
		//Transferencia Electrónica
		$this->mDatosDePago = 'formaDePago="' . strtoupper($formaDePago) . '"
					metodoDePago="' . strtoupper($metodoDePago) . '"
					NumCtaPago="' . strtoupper($numeroCtaDePago) . '"';
	}
	function getImpuestos(){
		$totalTras	= $this->mSumaTrasladados;
		$txt	= '<cfdi:Impuestos totalImpuestosTrasladados="' . $totalTras . '">
			  <cfdi:Traslados>
				' . $this->mImptosTras . '		    
			  </cfdi:Traslados>
			</cfdi:Impuestos>';
		return $txt;
	}
	function get(){
		$fecha_actual = substr( date('c'), 0, 19);
		$total	= $this->mSumaConceptos	+ $this->mSumaTrasladados;
		$cfdi = '<?xml version="1.0" encoding="UTF-8"?>
					<cfdi:Comprobante xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd"
							xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
							xmlns:xs="http://www.w3.org/2001/XMLSchema" version="3.2"
							fecha="' . $fecha_actual  . '"
							tipoDeComprobante="ingreso"
							noCertificado="" certificado="" sello=""
							' . $this->mDatosDePago .  ' ' . $this->mHeadLugarExp .  '
							subTotal="' . $this->mSumaConceptos . '" total="' . $total . '">
				';		
		$cfdi	.= $this->getEmisor();
		$cfdi	.= $this->getReceptor();
		$cfdi	.= $this->getConceptos();
		$cfdi	.= $this->getImpuestos();
		$cfdi 	.= '</cfdi:Comprobante>';
		// <cfdi:Complemento><tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" xsi:schemaLocation="http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd" version="1.0" UUID="3E78597A-F101-4EF1-B6E6-C6F08DC82C08" FechaTimbrado="2013-12-13T17:12:39" selloCFD="RbmlU6XDZ3l2fNAdGCArMRU1uLJwHn+WsgjwRJZjNO4O8KKEbUM/aelCl569of0EuMLqgyUs3VEYbf+JDM9+5Hn0QRW0Jg8ehgHCXWebP1o4N29YMtSTSaGbfXdPjBERDvkj272ohqeR6spSoVZUqqIkOazN3r668l16VdNFP6U=" noCertificadoSAT="20001000000100005761" selloSAT="VabzekE27gTMsVlkGW5vB3lBKP2fXnrSoSwLW6vsDDeQxyMBGIoAiZGwAUW21uZjHHZisu6PxHujoYuzou0R6pdAhX0twJO+/U3Pq72503qUDz7qHsC+VWPleiA42b20F79/FRSimtYuxJUN0yU4btoiPjGQzr7oNT2GFJDub4o=" /></cfdi:Complemento>
  		return $cfdi;
	}
	function setValidacionLocal(){
		// Create temporary file and save manually created DOMDocument.
		$tempFile = PATH_TMP . "/" . time() . '-' . rand() . '-document.tmp';
		file_put_contents($tempFile, $this->get());
		// Create temporary DOMDocument and re-load content from file.
		$tempDom = new DOMDocument();
		$tempDom->load($tempFile);
	
		// Delete temporary file.
		if (is_file($tempFile))
		{
			unlink($tempFile);
		}
	//setLog(PATH_XSD . 'cfdv3.xsd');
		// Validate temporary DOMDocument.
		return $tempDom->schemaValidate( PATH_XSD . 'cfdv3.xsd');
	}
	function sellarXML($numero_de_certificado){
		//$cfdi, $numero_certificado, $archivo_cer, $archivo_pem
		$cfdi			= $this->get();
		$archivo_pem	= $this->mPEM;
		$archivo_cer	= $this->mCERT;
		
		$private 		= openssl_pkey_get_private(file_get_contents($archivo_pem));
		$certificado 	= str_replace(array('\n', '\r'), '', base64_encode(file_get_contents($archivo_cer)));
	
		$xdoc 			= new DomDocument();
		$xdoc->loadXML($cfdi) or die("XML invalido");
	
		$XSL 			= new DOMDocument();
		$XSL->load(PATH_XSD . 'cadenaoriginal_3_2.xslt');
	
		$proc 			= new XSLTProcessor;
		$proc->importStyleSheet($XSL);
	
		$cadena_original = $proc->transformToXML($xdoc);
		openssl_sign($cadena_original, $sig, $private);
		$sello = base64_encode($sig);
	
		$c = $xdoc->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0);
		$c->setAttribute('sello', $sello);
		$c->setAttribute('certificado', $certificado);
		$c->setAttribute('noCertificado', $numero_de_certificado);
		return $xdoc->saveXML();
	
	}
	function timbrar($num_certificado = ""){
		$sucess			= false;
		/**
		 * Niveles de debug:
		 * 0 - No almacenar
		 * 1 - Almacenar mensajes SOAP en archivo log.
		 */
	
		$debug 				= (MODO_DEBUG == true) ? 1 : 0;
	
		//RFC utilizado para el ambiente de pruebas
		$rfc_emisor 		= $this->mRFC; //"ESI920427886";
	
		//Archivos del CSD de prueba proporcionados por el SAT.
		//ver http://developers.facturacionmoderna.com/webroot/CertificadosDemo-FacturacionModerna.zip
		
		$archivo_cer 		= $this->mCERT; //"utilerias/certificados/20001000000200000192.cer";
		$archivo_pem 		= $this->mPEM; //"utilerias/certificados/20001000000200000192.key.pem";
		$num_certificado	= ($num_certificado == "") ? FACTURACION_NUM_CERT : $num_certificado;
	
		//Datos de acceso al ambiente de pruebas
		$url_timbrado 		= FACTURACION_URL_SERVICIO;// "https://t1demo.facturacionmoderna.com/timbrado/wsdl";
		$user_id 			= FACTURACION_USUARIO_NOMBRE;//"UsuarioPruebasWS";
		$user_password 		= FACTURACION_USUARIO_CLAVE;//"b9ec2afa3361a59af4b4d102d3f704eabdf097d4";
	
		//generar y sellar un XML con los CSD de pruebas
		$cfdi = $this->get();
		$cfdi = $this->sellarXML($num_certificado);
	
	
		$parametros 		= array('emisorRFC' => $rfc_emisor,'UserID' => $user_id,'UserPass' => $user_password);
	
		$opciones 			= array();
	
		/**
		 * Establecer el valor a true, si desea que el Web services genere el CBB en
		 * formato PNG correspondiente.
		 * Nota: Utilizar está opción deshabilita 'generarPDF'
		*/
		$opciones['generarCBB'] = false;
	
		/**
		 * Establecer el valor a true, si desea que el Web services genere la
		 * representación impresa del XML en formato PDF.
		 * Nota: Utilizar está opción deshabilita 'generarCBB'
		 */
		$opciones['generarPDF'] = true;
	
		/**
		 * Establecer el valor a true, si desea que el servicio genere un archivo de
		 * texto simple con los datos del Nodo: TimbreFiscalDigital
		 */
		$opciones['generarTXT'] = false;
	
		//setLog($url_timbrado);
		$cliente = new FacturacionModerna($url_timbrado, $parametros, $debug);
	
		if($cliente->timbrar($cfdi, $opciones)){
	
			//Almacenanos en la raíz del proyecto los archivos generados.
			$comprobante 	= PATH_FACTURAS . $cliente->UUID;
			$this->mUUID	= $cliente->UUID;
			$this->mComprobante		= $comprobante;	
			if($cliente->xml){
				$this->mMessages	.= "OK\tXML almacenado correctamente en $comprobante.xml\n";
				file_put_contents($comprobante.".xml", $cliente->xml);
				$this->mXML			= $cliente->xml;
			}
			if(isset($cliente->pdf)){
				$this->mMessages	.= "OK\tPDF almacenado correctamente en $comprobante.pdf\n";
				file_put_contents($comprobante.".pdf", $cliente->pdf);
				$this->mPDF			= $cliente->pdf;
			}
			if(isset($cliente->png)){
				$this->mMessages	.= "OK\tCBB en formato PNG almacenado correctamente en $comprobante.png\n";
				file_put_contents($comprobante.".png", $cliente->png);
			}
			$this->mMessages	.= "OK\tTimbrado exitoso\n";
			$sucess				= true;
		} else {
    		$this->mMessages	.= "ERROR\t[".$cliente->ultimoCodigoError."] - ".$cliente->ultimoError."\n";
  		}
  		return $sucess;
	}
	function getComprobante(){ return $this->mComprobante; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function setArchivar($recibo = false){
		$recibo	= setNoMenorQueCero($recibo);
		$xArch	= new cOperaciones_archivo_de_facturas();
		$xml	= base64_encode($this->mXML);
		$pdf	= base64_encode($this->mPDF);
		$xArch->uuid($this->mUUID);
		$xArch->contenido($xml);
		$xArch->clave_de_recibo($recibo);
		$xArch->impreso($pdf);
		$xArch->query()->insert()->save();
		
	}
	function getPDF(){ return $this->mPDF; 	}
	function getXML(){ return $this->mXML; }
}

class cMonedas {
	private $mClave		= "";
	private $mData		= array();
	private $isInit		= false;
	private $mObj		= null;
	private $mPais		= "";
	private $mValor		= 1;
	private $mNombre	= "MONEDA_DESCONOCIDA";
	function __construct($clave = ""){
		$this->mClave		= strtoupper($clave);
		if($clave != ""){ $this->init(); }
	}
	function initByPais($pais){
		$xCache		= new cCache();
		$pais		= strtoupper($pais);
		
		$data		= $xCache->get("moneda-por-pais-$pais");
		if($data == null){
			$ql			= new MQL();
			$data		= $ql->getDataRow("SELECT * FROM `tesoreria_monedas` WHERE `pais_de_origen`='$pais' LIMIT 0,1");
			$xCache->set("moneda-por-pais-$pais", $data);
		}
		return $this->init($data);	
	}
	function init($data = false){
		$clave		= $this->mClave;
		if(is_array($data)){
			
		} else {
			$ql			= new MQL();
			$data		= $ql->getDataRow("SELECT * FROM `tesoreria_monedas` WHERE `clave_de_moneda`='$clave' LIMIT 0,1");
		}
		if(isset($data["clave_de_moneda"])){
			$this->mObj		= new cTesoreria_monedas();
			$this->mObj->setData($data);
			$this->mData	= $data;
			$this->isInit	= true;
			$this->mPais	= strtoupper($this->mObj->pais_de_origen()->v());
			$this->mNombre	= strtoupper($this->mObj->nombre_de_la_moneda()->v(OUT_TXT));
			$this->mClave	= strtoupper($this->mObj->clave_de_moneda()->v());
			$this->mValor	= setNoMenorQueCero($this->mObj->quivalencia_en_moneda_local()->v());
		}
		return $this->isInit;
	}
	function getClave(){return $this->mClave;}
	function getPais(){ return $this->mPais; }
	function getNombre(){ return $this->mNombre; }
	function getValor(){ return $this->mValor; }
	function getEnDolares($cantidad = 1){
		return round((($this->getValor() * $cantidad) / VALOR_ACTUAL_DOLAR),2);
	}
}

class cTesoreriaTiposDePagoCobro {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mEquivCont	= 0;
	private $mTipoAML	= 0;
	function __construct($clave = false){ $this->mClave	= strtolower($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = ""){
		$clave = ($clave != "") ? $this->mClave : $clave;
		$clave = ($clave != "") ? microtime() : $clave;
		$this->mIDCache	= TTESORERIA_TIPOS_DE_PAGO . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cTesoreria_tipos_de_pago();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`='". $this->mClave . "' LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj			= $xT;
			$this->mClave		= $data[$xT->getKey()];
			$this->mNombre		= $xT->descripcion()->v();
			$this->mTipoAML		= $xT->equivalente_aml()->v();
			$this->mEquivCont	= $xT->eq_contable()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit		= true;
			$xT 				= null;
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
	function getTipoEnAML(){ return $this->mTipoAML; }
	function getTipoContable(){ return $this->mEquivCont; }
	function add(){}

}

?>