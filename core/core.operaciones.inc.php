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
	private $mCodigoDeRecibo 		= false;
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
	private $mOBanco				= null;
	private $mPeridoActivo			= null;			//Parcialidad o periodo del recibo
	private $mOPersona				= null;
	private $mOUsuario				= null;			//Usuario
	private $mOTipoRecibo			= null;
	private $mTipoOrigenAML			= 0;
	private $mClavePersonAsoc		= false;
	private $mOFactura				= null; 
	function __construct($tipo = false, $solo_por_docto	= false, $recibo = false){
		if ($tipo == false){
			$tipo				= FALLBACK_TIPO_DE_RECIBO;
		}
		$this->mTipoDeRecibo	= $tipo;
		if ( setNoMenorQueCero($recibo) > 0 ){
			$this->mCodigoDeRecibo			= $recibo;
			$this->setNumeroDeRecibo($this->mCodigoDeRecibo, true);
		}
		$this->mFechaDeVcto					= fechasys();
		$this->mUnicoDocto					= $solo_por_docto;
		$this->mMoneda						= AML_CLAVE_MONEDA_LOCAL;
		$this->mClavePersonAsoc				= DEFAULT_SOCIO;
	}
	function setSocio($socio){			$this->mSocio   = $socio; }
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
			if($arrInicial != false AND is_array($arrInicial)){
				$DR		= $arrInicial;
			} else {
				$xLi	= new cSQLListas();
				
				$DR		= obten_filas($xLi->getInicialDeRecibos($this->mCodigoDeRecibo, $this->mSocio));
			}
			$ORec							= new cOperaciones_recibos();
			$ORec->setData($DR);
			if( isset($DR["tipo_docto"])){
				$this->mTipoDeRecibo		= $DR["tipo_docto"];
				$this->mTipoDePago		= $DR["tipo_pago"];
				$this->mGrupoAsociado		= $DR["grupo_asociado"];
				$this->mSocio			= $DR["numero_socio"];
				$this->mDocto			= $DR["docto_afectado"];
				$this->mNumeroCheque		= $DR["cheque_afectador"];
				$this->mFechaDeOperacion	= $DR["fecha_operacion"];
				$this->mObservaciones		= $DR["observacion_recibo"];
				$this->mPathToFormato		= $DR["path_formato"];
				$this->mTipoDescripcion		= $DR["descripcion_recibostipo"];
				$this->mAplicadoA		= $DR["origen"];
				$this->mReciboFiscal		= $DR["recibo_fiscal"];
				$this->mTotalRecibo		= $DR["total_operacion"];
				
				$this->mPathToFormato		= $DR["path_formato"];
				$this->mOrigen			= $DR["origen"];
				$this->mIndiceOrigen		= $DR["indice_origen"];
					
				$this->mDatosByArray		= $DR;
				$this->mFechaDeVcto			= $this->mFechaDeOperacion;
				$this->mUsuario				= $ORec->idusuario()->v();
				$this->mMoneda				=  strtoupper( $ORec->clave_de_moneda()->v() );
				$this->mUnidadesOriginales	=  strtoupper( $ORec->unidades_en_moneda()->v() );
				$this->mTipoOrigenAML		= $ORec->origen_aml()->v();
				$this->mClavePersonAsoc		= $ORec->persona_asociada()->v();
				unset($DR);
				$this->mReciboIniciado				= true;
			} else {
				$this->mMessages			.= "ERROR\tRecibo no encontrado # $recibo\r\n";
			}
			return $this->mReciboIniciado;
	}
	function setGrupoAsociado($grupo){ $this->mGrupoAsociado	= $grupo; }
	/**
	 * Agrega un Recibo Nuevo
	 * @param integer	$socio
	 * @param integer	$documento
	 * @param date		$fecha_operacion
	 * @param integer	$parcialidad
	 * @param integer	$tipo_docto
	 * @param string	$cadena
	 * @param string	$cheque_afectador
	 * @param string	$tipo_pago
	 * @param string	$recibo_fiscal
	 * @param integer	$grupo_asoc
	 * @param integer	$cuenta_bancaria
	 */
	function setNuevoRecibo($socio, $documento, $fecha_operacion, $parcialidad,
						$tipo_docto = false, $cadena = "", $cheque_afectador = "NA",
						$tipo_pago = "ninguno", $recibo_fiscal = "NA", $grupo_asoc = DEFAULT_GRUPO,
						$cuenta_bancaria = false, $moneda = false, $unidades_originales = 0, $persona_asociada = false){
		$ql					= new MQL();					
		$tipo_docto 		=	( setNoMenorQueCero($tipo_docto) <= 0) ? $this->mTipoDeRecibo : $tipo_docto;
		//----------------------------------DATOS DEL RECIBO------------------------
		$total_operacion 	= 0;					// Total Operacion
		$observaciones 		= $cadena;
		$indice_origen 		= 99;					// Indice de Origen
		$sucursal			= getSucursal();
		$eacp				= EACP_CLAVE;
		$moneda				= ($moneda == false) ? $this->mMoneda : $moneda;
		$unidades_originales= ($unidades_originales == 0) ? $this->mUnidadesOriginales : $unidades_originales;
		$persona_asociada	= ($persona_asociada == false) ? $this->mClavePersonAsoc : $persona_asociada;
		$persona_asociada	= setNoMenorQueCero($persona_asociada);
		//si es pago o ministracion
		if($tipo_docto == RECIBOS_TIPO_PAGO_CREDITO AND $persona_asociada <= DEFAULT_SOCIO){
			$xCred			= new cCredito($documento);
			if($xCred->init() == true){
				$persona_asociada	= $xCred->getClaveDeEmpresa();
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
		$xDTab				= new cSAFETabla(TOPERACIONES_RECIBOS);
	//----------------------------------------------------------------------------- clave_de_moneda, unidades_en_moneda
		$xRec				= new cOperaciones_recibos();
		$xRec->archivo_fisico("");
		$xRec->cadena_distributiva($cadena);
		$xRec->cheque_afectador($cheque_afectador);
		$xRec->clave_de_moneda($moneda);
		$xRec->docto_afectado($documento);
		$xRec->eacp($eacp);
		$xRec->fecha_de_registro(fechasys());
		$xRec->fecha_operacion($fecha_operacion);
		$xRec->grupo_asociado($grupo_asoc);
		$xRec->idoperaciones_recibos($idrecibo);
		$xRec->idusuario($iduser);
		$xRec->indice_origen($indice_origen);
		$xRec->numero_socio($socio);
		$xRec->observacion_recibo($observaciones);
		$xRec->origen_aml(0);
		$xRec->persona_asociada($persona_asociada);	//empresa
		$xRec->recibo_fiscal($recibo_fiscal);
		$xRec->sucursal($sucursal);
		$xRec->tipo_docto($tipo_docto);
		$xRec->total_operacion($total_operacion);
		$xRec->unidades_en_moneda($unidades_originales);
		$xRec->tipo_pago($tipo_pago);
		
		if ($this->mAfectar == true){
			$xsr = $xRec->query()->insert()->save();// my_query($sql_i_rec);
			if ( $xsr != false){
				$this->mMessages	.= "OK\tSe agrego exitosamente el Recibo : $idrecibo : del Documento $documento\r\n";
				$this->mCodigoDeRecibo	= $idrecibo;
				$this->setNumeroDeRecibo($idrecibo);
				$this->init();
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
	/**
	 * @return integer Numero de Operacion
	 **/
	function setNuevoMvto($fecha_operacion, $monto, $tipo_operacion, $periodo_de_socio, $observaciones, $signo_afectacion = 1, $mvto_contable = false,
						  $socio = false, $documento = false, $fecha_afectacion = false, $fecha_vencimiento = false, $saldo_anterior = false, $saldo_nuevo = false){
		$sucess		= false;
		//inicializa el recibo
		if ( !isset($this->mCodigoDeRecibo) OR $this->mCodigoDeRecibo == false ){
				//
		}
		//Si no hay valores, obtenerlos del recibo
		if ($documento == false OR !isset($documento) ){
			$documento	= $this->mDocto;
		}
		if ( $socio == false OR !isset($socio) ){
			$socio		= $this->mSocio;
		}
		//Verificar la Cuenta Bancaria
		if ( $this->mCuentaBancaria == false ){
			$this->mCuentaBancaria = DEFAULT_CUENTA_BANCARIA;
		}
		$recibo			= $this->mCodigoDeRecibo;
		$fecha_afectacion	= ($fecha_afectacion == false) ? $fecha_operacion : $fecha_afectacion;
		// --------------------------------------- VALOR SQL DEL MVTO.-------------------------------------------------------
			// VALORES FIJOS
		$smf	= "idusuario, codigo_eacp, socio_afectado, docto_afectado, recibo_afectado, fecha_operacion, ";
			// PERIODOS
		$smf	.= "periodo_contable, periodo_cobranza, periodo_seguimiento, ";
		$smf	.= "periodo_anual, periodo_mensual, periodo_semanal, ";
			// AFECTACIONES
		$smf	.= "afectacion_cobranza, afectacion_contable, afectacion_estadistica, ";
		$smf	.= "afectacion_real, valor_afectacion, ";
			// FECHAS Y TIPOS
		$smf	.= "idoperaciones_mvtos, tipo_operacion, estatus_mvto, periodo_socio, ";
		$smf	.= "fecha_afectacion, fecha_vcto, ";
			// SALDOS
		$smf	.= "saldo_anterior, saldo_actual, detalles, sucursal, tasa_asociada, dias_asociados, grupo_asociado";

		//
		$iduser 		= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
		$eacp 			= EACP_CLAVE;
										// PERIODOS
		$percont 		= EACP_PER_CONTABLE;										// Periodo Contable
		$percbza 		= EACP_PER_COBRANZA;										// Periodo Cobranza.
		$perseg 		= EACP_PER_SEGUIMIENTO;										// Period de Seguimiento.
		$permens 		= date("m", strtotime($fecha_operacion) );					// Periodo mes
		$persem 		= date("N", strtotime($fecha_operacion) );					// Periodo de dias en la semana.
		$peranual 		= date("Y", strtotime($fecha_operacion) );					// A�o Natural.

		$persoc 		= $periodo_de_socio;												// periodo del Socio.
		$estatus_mvto 		= $this->mDefMvtoStatus;

		$fecha_vcto 		= ($fecha_vencimiento == false) ? $this->mFechaDeVcto : $fecha_vencimiento;
		$saldo_anterior		= ($saldo_anterior === false ) ? 0 : $saldo_anterior;
		$saldo_nuevo		= ($saldo_nuevo === false ) ? $monto : $saldo_nuevo;
		$sucursal 		= getSucursal();
		$afect_cbza		= $monto;
		$afect_seg		= $monto;
		$afect_cont		= $monto;
		$afect_esta		= $monto;
		$idoperacion 		= folios(2);
		$tasa			= 0;
		$dias			= 0;
		$grupo			= $this->mGrupoAsociado;
		$viable			= true;
		$xT				= new cTipos(0);
		if ($this->mGrupoAsociado == false ){
			$grupo		= DEFAULT_GRUPO;
		}
		$smv = "$iduser, '$eacp', $socio, $documento, $recibo, '$fecha_operacion',
				$percont, $percbza, $perseg, $peranual, $permens, $persem,
				$afect_cbza, $afect_cont, $afect_esta,
				$monto, $signo_afectacion,
				$idoperacion, $tipo_operacion, $estatus_mvto, $persoc,
				'$fecha_afectacion', '$fecha_vcto',
				$saldo_anterior, $saldo_nuevo, '$observaciones', '$sucursal', $tasa, $dias, $grupo
				";
		$arrD	= array( $socio, $documento, $recibo );
		$viable	= $xT->getEvalNotNull($arrD);
		if ( $viable == false ){
			$this->mMessages	.= "ERROR\tVARS\tError al Evaluar alguno de estos Valores Socio $socio, Documento $documento, Recibo $recibo\r\n";
			$this->mMessages	.= $xT->getMessages();
		}
		$SQl_comp = "INSERT INTO operaciones_mvtos($smf) VALUES ($smv)
						ON DUPLICATE KEY UPDATE idoperaciones_mvtos = " . folios(2) . "
					";
		if($monto !=0 AND isset($monto) AND $this->mAfectar == true AND $viable == true){
				$exec = my_query($SQl_comp);

				if ( $exec["stat"] == false ){
					$sucess	= false;
					$this->mMessages	.= "ERROR\t$recibo\tSe Fallo al Agregar la Operacion($tipo_operacion) por $monto con Numero $idoperacion\r\n";
				} else {
					$sucess	= true;
					$this->mMessages	.= "SUCESS\t$recibo\tSe agrego Exitosamente la Operacion($tipo_operacion) por $monto con Numero $idoperacion \r\n";
				}
		} else {
			$this->TxtLog	.= "WARNING\tSe simula Agregar el Mvto $idoperacion del tipo $tipo_operacion por un monto de $monto \r\n";
		}
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
		//generar Poliza a demanda
		$finalizado				= true;
		//tranferencia.egresos cheque
		if(MODULO_CAJA_ACTIVADO == false){ $tesoreria_cargada = true;/* Forzar AML si Tesoreria esta Desactivado */  }
		/**
		 * Modificacion de la condicion de socio por afectar al recibo en SI?
		 */
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

				$this->mSumaDeRecibo 	= $TDRec["monto"];
				$this->mNumeroDeMvtos	= $TDRec["numero"];
				$this->mMessages		.= "SUCESSACT\tMonto Actualizado a " . $this->mSumaDeRecibo . " y # Operaciones " . $this->mNumeroDeMvtos . "\r\n";
			}
			 if (!isset($this->mSumaDeRecibo) ){
			   $this->mSumaDeRecibo = 0;
			}
			$sql = "UPDATE operaciones_recibos SET total_operacion=" . $this->mSumaDeRecibo . "
    				WHERE	idoperaciones_recibos= " . $this->mCodigoDeRecibo . "	";
			$xRs = my_query($sql);
		}
		
		if ($this->mSetGenerarPoliza == true){
			//PolizaPorRecibo($this->mCodigoDeRecibo, GENERAR_POLIZAS_AL_CIERRE);
		}
		$this->mTotalRecibo	= $this->mSumaDeRecibo;
		
		//-- AML
		if(MODULO_AML_ACTIVADO == true AND $this->isPagable() == true AND $tesoreria_cargada == true){
			$xAml		= new cAMLPersonas($this->mSocio);
			$xAml->init();
			$xAml->setForceAlerts();
			$xAmlO		= new cAMLOperaciones();
			
			$this->init();
			if($xAml->getEsPersonaVigilada() == true){
				$tipo_de_pago_aml	= $this->mTipoDePago;
				if(setNoMenorQueCero($this->mTipoOrigenAML) > 0){
					$xRisK				= new cPersonas_perfil_transaccional_tipos(); $xRisK->query()->initByID($this->mTipoOrigenAML);
					$tipo_de_pago_aml	= strtolower( $xRisK->tipo_de_exhibicion()->v(OUT_TXT));
					$this->mMessages	.= "WARN\tCambiar el perfil de pago de " . $this->mTipoDePago . " a $tipo_de_pago_aml \r\n";
				}
				$res				= $xAmlO->analizarOperacion($this->mSocio, 0, $this->mMoneda, $tipo_de_pago_aml, 	$this->getFechaDeRecibo(), $this->getCodigoDeRecibo(), $this->mTipoOrigenAML);
				
				if($res == false ){
					$this->mMessages	.= "OK\tAML Normal \r\n";
					if(MODO_DEBUG == true){ $this->mMessages	.= $xAmlO->getMessages(OUT_TXT); }
				} else {
					//REPORTAR
					$xAv			= new cAML();
					if($xAmlO->getTipoDeReporte() == AML_REPORTE_INMEDIATO){
						$xAv->setForceRegistroRiesgo();
						$this->mMessages	.= "WARN\tAML de Aviso Inmediato\r\n";
					}
					$xAv->sendAlerts($this->mSocio, AML_OFICIAL_DE_CUMPLIMIENTO, $xAmlO->getTipoDeAlerta(), $xAmlO->getMessageAlert(), 
							$this->mCodigoDeRecibo, $this->mFechaDeOperacion);
				}
				$this->mMessages	.=  $xAmlO->getMessages(OUT_TXT);
				//validar perfil transaccional
				 $xAml->setVerificarPerfilTransaccional(); 
			}
			//Validar si es person riesgosa
			
			$xSoc	= $xAml->getOPersona();
			if($xSoc->getEsPersonaSDN() == true){
				//REPORTAR operaciones con alto riesgo
				$idriesgo			= AML_ID_OPERACIONES_PERSONAS_ALTO_RIESGO; //operaciones con criminales
				$xRiesgo			= new cAml_risk_catalog();
				$xRiesgo->setData( $xRiesgo->query()->initByID($idriesgo) );
				
				$xAv			= new cAML();
				$xAv->setForceRegistroRiesgo();
				$xAv->sendAlerts($this->mSocio, AML_OFICIAL_DE_CUMPLIMIENTO, $idriesgo, "ERROR\tOperaciones con persona ALTAMENTE RIESGOSA Recibo " . $this->mCodigoDeRecibo, 
						$this->mCodigoDeRecibo, $this->getFechaDeRecibo());
				$this->mMessages	.= $xAv->getMessages(OUT_TXT);				
			}
			
			if( $xSoc->getEsPersonaPoliticamenteExpuesta() == true ){
				//REPORTAR operaciones con PEPs
				$idriesgo			= AML_ID_OPERACIONES_PERSONAS_PEP; //operaciones con criminales
				$xRiesgo			= new cAml_risk_catalog();
				$xRiesgo->setData( $xRiesgo->query()->initByID($idriesgo) );
				
				$xAv			= new cAML();
				$xAv->sendAlerts($this->mSocio, AML_OFICIAL_DE_CUMPLIMIENTO, $idriesgo, "ERROR\tOperaciones con PEPS Recibo " . $this->mCodigoDeRecibo,
						$this->mCodigoDeRecibo, $this->getFechaDeRecibo());
				$this->mMessages	.= $xAv->getMessages(OUT_TXT);			
			}
			if($xSoc->getEsPersonaRiesgosa() == true ){
				//REPORTAR operaciones con PEPs
				$idriesgo			= 101005; //operaciones con criminales
				$xRiesgo			= new cAml_risk_catalog();
				$xRiesgo->setData( $xRiesgo->query()->initByID($idriesgo) );
			
				$xAv			= new cAML();
				$xAv->sendAlerts($this->mSocio, AML_OFICIAL_DE_CUMPLIMIENTO, $idriesgo, "ERROR\tOperaciones con Personas de Alto Riesgo.- Recibo " . $this->mCodigoDeRecibo,
						$this->mCodigoDeRecibo, $this->getFechaDeRecibo());
				$this->mMessages	.= $xAv->getMessages(OUT_TXT);
			}
			//Operaciones de una exhibicion 500 y USD
			if($this->mMoneda != AML_CLAVE_MONEDA_LOCAL){
				$idriesgo			= AML_CLAVE_RIESGO_OPS_INDIVIDUALES;
				$xRiesgo			= new cAml_risk_catalog();
				$xMon				= new cMonedas($this->mMoneda);
				
				$xRiesgo->setData( $xRiesgo->query()->initByID($idriesgo) );
				$unidades			= $xMon->getEnDolares($this->getUnidadesOriginales());
				if( $unidades >= $xRiesgo->unidades_ponderadas()->v() ){
					$xAv			= new cAML();
					$xAv->setForceRegistroRiesgo();
					$xAv->sendAlerts($this->mSocio, AML_OFICIAL_DE_CUMPLIMIENTO, $idriesgo, "Operaciones($unidades) excedidas de 500 USD en el recibo " . $this->mCodigoDeRecibo . " Moneda " . $this->mMoneda,
						$this->mCodigoDeRecibo, $this->getFechaDeRecibo());
					$this->mMessages	.= $xAv->getMessages(OUT_TXT);
				} else {
					$this->mMessages	.= "OK\tNo hay modificacion para la Moneda " . $this->mMoneda . " por $unidades \r\n";
				}
			}
			// Agregar Relevantes por 10000USD
			if($this->isEfectivo() == true){
				$idriesgo			= AML_CLAVE_RIESGO_OPS_RELEVANTES;
				$xRiesgo			= new cAml_risk_catalog();
				$xMon				= new cMonedas($this->mMoneda);
				$mmonto				= ($this->mMoneda == AML_CLAVE_MONEDA_LOCAL) ? $this->getTotal() : $this->getUnidadesOriginales();
				$xRiesgo->setData( $xRiesgo->query()->initByID($idriesgo) );
				$unidades			= $xMon->getEnDolares($mmonto);

				if( $unidades >= $xRiesgo->unidades_ponderadas()->v() ){
					$xAv			= new cAML();
					$xAv->setForceRegistroRiesgo();
					$xAv->sendAlerts($this->mSocio, AML_OFICIAL_DE_CUMPLIMIENTO, $idriesgo, "Operaciones Relevantes por $unidades USD en el recibo " . $this->mCodigoDeRecibo . " Moneda " . $this->mMoneda,
						$this->mCodigoDeRecibo, $this->getFechaDeRecibo());
					
					$this->mMessages	.= $xAv->getMessages(OUT_TXT);
				} else {
					$this->mMessages	.= "OK\tNo hay modificacion para la Moneda " . $this->mMoneda . " por $unidades \r\n";
					
				}
			}
			if($xAml->getEsPersonaVigilada() == true){
				//Operaciones Internas Preocupantes por Usuario
				$xAml->setAnalizarTransaccionalidadPorNucleo($this->mCodigoDeRecibo, $this->mFechaDeOperacion, $this->mUsuario, true);
			}
			$this->mMessages	.=  $xAml->getMessages(OUT_TXT);
			
		}
		return $finalizado;
	}
	/**
	 * Revierte las Afectaciones del Recibo
	 */
	function setRevertir($ForzarEliminar = false){
			$sucess			= true;
			$arrValuesRev	= array("-1" => "1", "1" => "-1", "0" => "0");
			$xQL			= new MQL();
			$xLog			= new cCoreLog();
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
			$xLog->add("======================== REVERSION DE RECIBO[" . $this->mCodigoDeRecibo . "] \r\n");
			$original			= "";
			$rs 			= $xQL->getDataRecord($sqlM); //getRecordset($sqlM);
			if( $this->init() == true){
				$original	= "====[". base64_encode( json_encode($this->getDatosInArray()) ) . "]====";
			
				if($rs){
					foreach ($rs as $rw){ //$rw = mysql_fetch_array($rs)){
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
	                               $Credito->init();
	                               $colocacion     = $Credito->getDatosDeCredito();
	
									break;
								case "captacion":
									//cargar datos de la cuenta
	                                $Cuenta           = new cCuentaALaVista($docto);
	                                $Cuenta->init();
	                                $captacion      = $Cuenta->getDatosInArray();
	
									break;
								case "mixto":
									//cargar datos de la cuenta y del credito
	                                $Credito          = new cCredito($docto, $socio);
	                                $Credito->init();
	                                $colocacion     = $Credito->getDatosDeCredito();
	                                $Cuenta           = new cCuentaALaVista($docto);
	                                $Cuenta->init();
	                                $captacion      = $Cuenta->getDatosInArray();
	                                $this->mMessages	.= "WARN\tEL Recibo es Mixto, se carga tanto Captacion como Colocacion\r\n";
									break;
								default:
	                                $this->mMessages	.= "WARN\tEL Recibo es " . $this->mAplicadoA . ", NO SE CARGA CODIGO\r\n";
									break;
							}
							eval( $CodeRevertir );
	
	
								if($preservar_mvto=='1' AND $ForzarEliminar == false){
									$SQL_DM = "UPDATE operaciones_mvtos
											SET afectacion_estadistica=afectacion_real,
											afectacion_real = 0, afectacion_contable=0,
											afectacion_cobranza=0, valor_afectacion=0,
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
								my_query($SQL_DM);
						//Actualizar Saldos
						if(isset($Cuenta)){ $Cuenta->setUpdateSaldoByMvtos(); }
						if(isset($Credito)){ $xUtil		= new cUtileriasParaCreditos();$xUtil->setCuadrarCreditosByMvtos($docto); }
					}
				}
				//Elimnar Prepoliza
				$xLog->add("WARN\tEliminando Prepolizas\r\n", $xLog->DEVELOPER);
				$sqlDP 				= "DELETE FROM contable_polizas_proforma	WHERE numero_de_recibo = " . $this->mCodigoDeRecibo . ""; my_query($sqlDP);
				//Eliminar Recibo
				$sqlDR 				= "DELETE FROM operaciones_recibos WHERE idoperaciones_recibos =" . $this->mCodigoDeRecibo . ""; my_query($sqlDR);
				//Agregar Tesoreria y Bancos
				$xLog->add("WARN\tEliminando Operaciones de Caja\r\n", $xLog->DEVELOPER);
				$DelTesoreria		= "DELETE FROM `tesoreria_cajas_movimientos` WHERE `recibo`= " . $this->mCodigoDeRecibo . ""; my_query($DelTesoreria);
				$xLog->add("WARN\tEliminando Operaciones de Bancos\r\n", $xLog->DEVELOPER);
				$DelBancos			= "DELETE FROM `bancos_operaciones` WHERE `recibo_relacionado` = " . $this->mCodigoDeRecibo . ""; my_query($DelBancos);
			}
			
			$xCE	= new cErrorCodes();
			//setLog($this->mMessages .  json_encode($this->getDatosInArray()), $xCE->RECIBO_ELIMINADO);
			$xLog->guardar($xLog->OCat()->RECIBO_ELIMINADO);
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
			return $sucess;
	}	
	function getCodigoDeRecibo(){ return $this->mCodigoDeRecibo;	}
	/**
	 * Retorna un Array por los datos del Recibo
	 * @return array
	 */
	function getDatosInArray(){ return $this->mDatosByArray; }
	/**
	 * Retorna un Array por los datos del Recibo
	 * @deprecated 1.9.42
	 * @return array
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
			//$fichaEmpresa	= "";
			
		if($this->mReciboIniciado == false){
			$exoFicha =  "<div class='error'>" . $xLg->get(MSG_NO_DATA) .  "</div>";
		} else {
			$xLg		= new cLang();
			$tool 	= $trTool;
			if($extend == true){
				$xUsr	= new cSystemUser($this->getCodigoDeUsuario());
				$xUsr->init();
				
				$xSoc	= new cSocio($this->getCodigoDeSocio()); $xSoc->init();
				
				$tool	.= "<tr><th class='izq'>" . $xLg->getT("TR.persona") . "</th>";
				$tool	.= "<td>" . $xSoc->getNombreCompleto() . "</td>" ;
				
				$tool	.= "<th class='izq'>" . $xLg->getT("TR.Documento") . "</th>";
				$tool	.= "<td>" . $this->getCodigoDeDocumento() . "</td>" ;
								
				$tool	.= "<tr><th class='izq'>" . $xLg->getT("TR.Elabora") . "</th>";
				$tool	.= "<td>" . $xUsr->getNombreCompleto() . "</td>" ;
				
				if($this->isDeEmpresa() == true){
					$xEmp	= new cEmpresas($personaAsoc);
					$xEmp->init();
					$tool	.= "<th class='izq'>" . $xLg->getT("TR.Empresa") . "</th>";
					$tool	.= "<td>" . $xEmp->getNombre() . "</td>" ;
				}
				$tool	.= "</tr>";
			}
			if($this->isDivisaExtranjera() == true){
				$tool	.= "<tr><th class='izq'>" . $xLg->getT("TR.Moneda") . "</th>";
				$tool	.= "<td>" . $this->getMoneda() . "</td>" ;
				$tool	.= "<th class='izq'>" . $xLg->getT("TR.Original") . "</th>";
				$tool	.= "<td>" . $this->getUnidadesOriginales() . "</td>" ;				
				$tool	.= "</tr>";
			}			
			$xF		= new cFecha(0);
			$exoFicha =  "
				<table id=\"ficharecibo\">
				<tbody>
					<tr>
						<th class='izq'>Numero de Recibo</th>
						<td class='mny'>" . $this->mCodigoDeRecibo . "</td>
						<th class='izq'>Tipo de Recibo</th>
						<td>" . $this->mTipoDescripcion . "</td>
					</tr>
					<tr>
						<th class='izq'>Fecha de Recibo</th>
						<td>" . $xF->getFechaCorta($this->mFechaDeOperacion) . "</td>
						<th class='izq'>Recibo Fiscal</th>
						<td>" . $this->mReciboFiscal . "</td>
					</tr>
					<tr>
						<th class='izq'>Tipo de Pago</th>
						<td>" .  strtoupper( $this->mTipoDePago ) . "</td>
						<th class='izq'>Total</th>
						<td>" .  getFMoney( $this->mTotalRecibo ) . "</td>

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
	 *
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
				$datos		= $datosC; //array_merge($datosC, $datosA);
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
					$cuenta		= $arrParams["cuenta"];
					$cheque		= $arrParams["cheque"];
					$ocaja->setCobroChequeInterno($this->mCodigoDeRecibo, $this->mTotalRecibo, $cuenta, $cheque);
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
		$this->init();
		$socio		= $this->mSocio;
		$folio		= ( $folio == false ) ? getFolio( iDE_RECIBO ) : $folio;
	
		$msg		= "$recibo\tCambiando el Recibo $recibo de la persona $socio al Nuevo Numero $folio\r\n";
	
		$sqlUM 		= "UPDATE operaciones_mvtos SET recibo_afectado = $folio WHERE
					recibo_afectado = $recibo AND socio_afectado = $socio";
		$m			= my_query($sqlUM);
		if ( $m["stat"] == false ){
			$msg	.= $m[SYS_MSG] . "\r\n";
		}
		
		$sqlURec	= "UPDATE operaciones_recibos SET idoperaciones_recibos = $folio WHERE
						idoperaciones_recibos = $recibo AND numero_socio = $socio ";
		$r = my_query($sqlURec) . "\r\n";
		if ($r["stat"] == false ){
			$msg	.= $r[SYS_MSG];
		}
		return $msg;
	}
	function getCodigoDeSocio(){ return $this->mSocio; }
	function getSocio(){ return new cSocio($this->mSocio);	}
	function getCodigoDeDocumento(){ return $this->mDocto; }
	function getCredito(){ return new cCredito($this->mDocto); }
	function getTotal(){ return $this->mTotalRecibo; }
	function getOrigen(){ return $this->mOrigen; }
	function getCodigoDeUsuario(){ return $this->mUsuario; }
	function getClaveDeOrigen(){ return $this->mOrigen; }
	function getIndiceOrigen(){ return $this->mIndiceOrigen; }
	function setMoneda($moneda){$this->mMoneda = $moneda;}
	function setUnidadesOriginales($unidades){ $this->mUnidadesOriginales = $unidades; }
	function getUnidadesOriginales(){ return $this->mUnidadesOriginales; }
	function setFechaVencimiento($fecha = false){ 	if ($fecha == false ){ $fecha	= fechasys(); } $this->mFechaDeVcto	= $fecha;	}
	function setFecha($fecha, $actualizacion = false){
		$this->mFechaDeOperacion	= $fecha;
		$recibo				= $this->mCodigoDeRecibo;
		$this->mMessages	.= "WARN\tRecibo $recibo . Actualizando la fecha " . $this->getFechaDeRecibo() . " A  $fecha\r\n";
		$xQL				= new MQL();
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
			$xLog->guardar($xLog->OCat()->EDICION_RAW);
		}
	}
	function setPeriodo($periodo = false, $actualizacion = false){
		if($actualizacion == true){
			$this->mMessages		.= "WARN\tActualizando a nuevo periodo $periodo\r\n";
			$recibo					= $this->mCodigoDeRecibo;
			my_query("UPDATE operaciones_mvtos SET periodo_socio=$periodo WHERE recibo_afectado=$recibo");
			$this->mPeridoActivo	= $periodo;
		}
	}
	function setTotalPorProrrateo($total){
		$recibo		= $this->mCodigoDeRecibo;
		$factor		= $total / $this->getTotal(); //2500 / 8000 = 
		$this->mMessages	.= "CHANGES\tCambiar " . $this->getTotal() . " a $total\r\n";
		$rec		= my_query("UPDATE operaciones_recibos SET total_operacion=(total_operacion*$factor) WHERE idoperaciones_recibos=$recibo");
		$ops		= my_query("UPDATE operaciones_mvtos SET afectacion_real=(afectacion_real*$factor), afectacion_cobranza=(afectacion_cobranza*$factor), afectacion_contable=(afectacion_contable*$factor), afectacion_estadistica=(afectacion_estadistica*$factor) WHERE recibo_afectado=$recibo");
		$this->mMessages	.= $rec[SYS_INFO];
		$this->mMessages	.= $ops[SYS_INFO];
	}
	function getBancoPorOperacion(){
		$ob	= null;
		$sql 	= "SELECT `bancos_operaciones`.`cuenta_bancaria` FROM	`bancos_operaciones` `bancos_operaciones` 
		WHERE (`bancos_operaciones`.`recibo_relacionado` =" . $this->mCodigoDeRecibo . ") ORDER BY `bancos_operaciones`.`monto_real` DESC LIMIT 0,1";
		$d	= obten_filas($sql);
		if(isset($d["cuenta_bancaria"])){
			$ob	= new cCuentaBancaria($d["cuenta_bancaria"]);
			$ob->init();
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
	function setDatosDePago($moneda = '', $monto_moneda = 0, $cheque ='', $tipo_de_pago = false, $transaccion = SYS_NINGUNO){
		
		$moneda			= ($moneda =='') ? $this->mMoneda : $moneda;
		$moneda			= strtoupper($moneda);
		
		$tipo_de_pago	= ($tipo_de_pago == false) ? $this->getTipoDePago() : $tipo_de_pago;
		$recibo			= $this->getCodigoDeRecibo();
		$tipo_de_pago	= strtolower($tipo_de_pago);
		//el indice de origen se actualizará dependiendo el tipo de perfiltransaccional, mayor a 99
		$origenAML		= 0;
		switch ($tipo_de_pago){
			case TESORERIA_COBRO_EFECTIVO:
				$origenAML	= ($moneda != AML_CLAVE_MONEDA_LOCAL) ? AML_OPERACIONES_PAGOS_EFVO_INT : AML_OPERACIONES_PAGOS_EFVO;
				break;
			case TESORERIA_PAGO_EFECTIVO:
				$origenAML	= ($moneda != AML_CLAVE_MONEDA_LOCAL) ? AML_OPERACIONES_RETIRO_EFVO_INT : AML_OPERACIONES_RETIRO_EFVO;
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

		$sql			= "UPDATE operaciones_recibos SET  cheque_afectador='$cheque', tipo_pago='$tipo_de_pago', origen_aml = $origenAML, clave_de_moneda='$moneda', unidades_en_moneda=$monto_moneda WHERE idoperaciones_recibos= $recibo";
		$rs 			= my_query($sql);
		//Validar Moneda Extranjera
		if(MODO_DEBUG == true){ $this->mMessages	.= $rs[SYS_INFO];	}
		if($rs[SYS_ESTADO] == true){
			$this->mMessages	.= "OK\tActualizacion correcta del Recibo $recibo ($moneda|$tipo_de_pago|$monto_moneda|$cheque|$transaccion)\r\n";
		} else{
			$this->mMessages	.= "ERROR\tError al actualizar el Recibo $recibo ($moneda|$tipo_de_pago|$monto_moneda|$cheque|$transaccion)\r\n";
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
				$this->mCuentaBancaria	= setNoMenorQueCero($this->getOCaja()->getCuentaBancoActivo());
				$xCtaBanc	= new cCuentaBancaria($this->mCuentaBancaria);
				if($xCtaBanc->init() == true){
					$this->mOBanco	= $xCtaBanc;
					$xBanc		= new cBancos_entidades();
					$xBanc->setData( $xBanc->query()->initByID( $xCtaBanc->getClaveDeBanco() ) );
					$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";					
				}
								
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
						$this->mCuentaBancaria	= setNoMenorQueCero($xOp->getCuentaBancaria());
						$xCtaBanc	= new cCuentaBancaria($this->mCuentaBancaria);
						if($xCtaBanc->init() == true){
							$this->mOBanco	= $xCtaBanc;
							$xBanc		= new cBancos_entidades();
							$xBanc->setData( $xBanc->query()->initByID( $xCtaBanc->getClaveDeBanco() ) );
							$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";
						}
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
						//obtener el nombre del banco
						$this->mCuentaBancaria	= setNoMenorQueCero($xOp->getCuentaBancaria());
						$xCtaBanc	= new cCuentaBancaria($this->mCuentaBancaria);
						if($xCtaBanc->init() == true){
							$this->mOBanco	= $xCtaBanc;
							$xBanc		= new cBancos_entidades();
							$xBanc->setData( $xBanc->query()->initByID( $xCtaBanc->getClaveDeBanco() ) );
							$info		.= $xBanc->nombre_de_la_entidad()->v(OUT_TXT) . "|";
						}		
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
		if($this->mPeridoActivo == null){
			$sql					= "SELECT MAX(periodo_socio) AS 'parcialidad' FROM operaciones_mvtos WHERE recibo_afectado=" . $this->mCodigoDeRecibo;
			$this->mPeridoActivo	= mifila($sql, "parcialidad");
		}
		return $this->mPeridoActivo;
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
	function getOBanco(){ return  $this->mOBanco; }
}

class cMovimientoDeOperacion{
	private $mCodigo		= 0;
	private $mArrayData		= array();
	private $mInit			= false;
	private $mMessages		= "";
	
	function __construct($codigo = false){ $this->mCodigo		= $codigo; }
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
			$rw			= $this->mArrayData;
			$codigo			= $rw["idoperaciones_mvtos"];
			$docto			= $rw["docto_afectado"];
			$socio			= $rw["socio_afectado"];
			$preservar_mvto 	= $rw["preservar_movimiento"];
			$CodeRevertir		= $rw["formula_de_cancelacion"];
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
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }

}

class cTipoDeOperacion{
	function __construct(){

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
	 
	function __construct($tipo){
		$this->mCodigo	= $tipo;
		$this->init();
	}
	function init(){
		$arrEq	= array(
			"aumento" => 1,
			"disminucion" => -1,
			"" => 0,
			"ninguno" => 0,
			"ninguna" => 0,
		);
		$sql	= "SELECT idoperaciones_recibostipo, descripcion_recibostipo, detalles_del_concepto, subclasificacion,
			        nombre_sublasificacion, mostrar_en_corte, tipo_poliza_generada,
			        afectacion_en_flujo_efvo, path_formato, origen
			    FROM operaciones_recibostipo
			    WHERE idoperaciones_recibostipo=" . $this->mCodigo . " LIMIT 0,1 ";
		$this->aDatos			= obten_filas($sql);
		$this->mPathFormato		= $this->aDatos["path_formato"];
		$this->mTipoPolizaCont	= $this->aDatos["tipo_poliza_generada"];
		$this->mOrigen			= $this->aDatos["origen"];
		//ninguna disminucion aumento
		$this->mAfectacionEfvo	= $arrEq[ $this->aDatos["afectacion_en_flujo_efvo"] ];
		$this->mNombre			= $this->aDatos["descripcion_recibostipo"];
		unset($arrEq);
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
		noExterior="' . strtoupper($numeroExt) . '" 
		colonia="' . strtoupper($colonia) . '" 
		municipio="' . strtoupper($municipio) . '" 
		estado="' . strtoupper($estado) . '" 
		pais="' . strtoupper($pais) . '" 
		codigoPostal="' . $codigoPostal . '" />';
		$this->mHeadLugarExp			= ' LugarExpedicion="' . strtoupper($municipio) .',' . strtoupper($estado) . '" ';
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
		<cfdi:Domicilio calle="' . strtoupper($calle) . '" 
		noExterior="' . strtoupper($numeroExt) . '" 
		' . $sInterior . '
		colonia="' . strtoupper($colonia) . '" 
		municipio="' . strtoupper($municipio) . '" 
		estado="' . strtoupper($estado) . '" pais="' . strtoupper($pais) . '" 
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
	function timbrar($numero_de_certificado = "20001000000200000192"){
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
	
	
		//Datos de acceso al ambiente de pruebas
		$url_timbrado 		= FACTURACION_URL_SERVICIO;// "https://t1demo.facturacionmoderna.com/timbrado/wsdl";
		$user_id 			= FACTURACION_USUARIO_NOMBRE;//"UsuarioPruebasWS";
		$user_password 		= FACTURACION_USUARIO_CLAVE;//"b9ec2afa3361a59af4b4d102d3f704eabdf097d4";
	
		//generar y sellar un XML con los CSD de pruebas
		$cfdi = $this->get();
		$cfdi = $this->sellarXML($numero_de_certificado);
	
	
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
	function __construct($clave = ""){
		$this->mClave		= strtoupper($clave);
		if($clave != ""){ $this->init(); }
	}
	function init($data = false){
		$clave		= $this->mClave;
		if(is_array($data)){
			
		} else {
			$ql			= new MQL();
			$data		= $ql->getDataRow("SELECT * FROM `tesoreria_monedas` WHERE `clave_de_moneda`='$clave' ");
		}
		$this->mObj		= new cTesoreria_monedas();
		$this->mObj->setData($data);
		$this->mData	= $data;
	}
	function getPais(){ return strtoupper($this->mObj->pais_de_origen()->v()); }
	function getNombre(){ return strtoupper($this->mObj->nombre_de_la_moneda()->v(OUT_TXT)); }
	function getValor(){ return setNoMenorQueCero($this->mObj->quivalencia_en_moneda_local()->v()); }
	function getEnDolares($cantidad = 1){
		return ($this->getValor() * $cantidad) / VALOR_ACTUAL_DOLAR;
	}
}

?>