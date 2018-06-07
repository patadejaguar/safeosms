<?php
include_once ("core.config.inc.php");
include_once ("entidad.datos.php");
include_once ("core.error.inc.php");
include_once ("core.common.inc.php");
include_once ("core.db.inc.php");
include_once ("core.creditos.inc.php");
include_once ("core.creditos.utils.inc.php");
include_once ("core.creditos.pagos.inc.php");




class cCreditosDeNomina{
	private $mObjCred	= null;
	private $mCredito	= 0;
	private $mInit		= false;
	function __construct($credito = false){
		$this->mCredito	= setNoMenorQueCero($credito);
	}
	function getOCredito(){
		if($this->mObjCred == null){
			if($this->mCredito > DEFAULT_CREDITO){
				$this->mObjCred	= new cCredito($this->mCredito);
				if($this->mObjCred->init() == true){
					$this->mInit	= true;
				}
			}
		}
		return $this->mObjCred;
	}
	function init(){ $this->getOCredito(); return $this->mInit;
	}
	//TODO: Asociar mensajes
	function setVincularEmpresa($empresa, $notas = "", $fecha = false){
		$empresa	= setNoMenorQueCero($empresa);
		$xLog		= new cCoreLog();
		if($this->init() == true){
			$xF		= new cFecha(0);
			$fecha	= $xF->getFechaISO($fecha);
			$xCred	= $this->getOCredito();
			$socio	= $xCred->getClaveDePersona();
			$credito= $this->mCredito;
			if($xCred->getTipoEnSistema() != SYS_PRODUCTO_NOMINA ){
				$xCred->setCambioProducto( CREDITO_PRODUCTO_NOMINA );
			}
			$xCred->setResetPersonaAsociada($fecha, $notas, $empresa);
			if($xCred->isPagable() == true){
				//Agregar operacion de desvinculacion
				$xRe	= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, false, DEFAULT_RECIBO);
				$xRe->init();
				$xRe->setNuevoMvto($fecha, $xCred->getSaldoActual(), OPERACION_CLAVE_VINCULACION, $xCred->getPeriodoActual(), "", 1, false,	$socio, $credito, $fecha);
				$xRe->setFinalizarRecibo();
				$xLog->add( $xRe->getMessages(OUT_TXT), $xLog->DEVELOPER);
				//Mensaje
				$oP			= $xCred->getOPersona();
				$xEmp		= new cEmpresas($empresa); $xEmp->init();
				$xRN		= new cReglaDeNegocio();
				$xRN->setVariables(array(
						"nombre_de_persona" => $oP->getNombreCompleto(),
						"mensaje" => $notas,
						"descripcion" => $xCred->getDescripcion(),
						"saldo_del_credito" => $xCred->getSaldoActual(),
						"nombre_de_la_empresa" => $xEmp->getNombreCorto()
				));
				$xRN->setExecuteActions( $xRN->reglas()->RN_NOMINA_AL_VINCULAR );
				$xLog->add( $xRN->getMessages(), $xLog->DEVELOPER);
			}
				
			$xLog->add( $xCred->getMessages(OUT_TXT));
				
		}
		return $xLog->getMessages();
	}
	function setDesvincularEmpresa($notas = "", $fecha = false){
		$msg		= "";
		$xLng		= new cLang();

		if($this->init() == true){
			$xCred		= $this->getOCredito();
			$xF			= new cFecha(0);
			$fecha		= $xF->getFechaISO($fecha);
			$empresa	= $xCred->getClaveDeEmpresa();
			$xCred->setResetPersonaAsociada($fecha, $notas);
			if($xCred->isPagable() == true){
				$oP			= $xCred->getOPersona();
				$xEmp		= new cEmpresas($empresa); $xEmp->init();
				$xRN		= new cReglaDeNegocio();
				$xRN->setVariables(array(
						"nombre_de_persona" => $oP->getNombreCompleto(),
						"mensaje" => $notas,
						"descripcion" => $xCred->getDescripcion(),
						"saldo_del_credito" => $xCred->getSaldoActual(),
						"nombre_de_la_empresa" => $xEmp->getNombreCorto()
				));
				$xRN->setExecuteActions( $xRN->reglas()->RN_NOMINA_AL_DESVINCULAR );
			}
		}
	}
	//TODO: Agregar Enviar a Despedidos. nominas.desvincular.frm.php

}
class cCreditosDeGrupo{
	private $mObjCred	= null;
	private $mCredito	= 0;
	private $mInit		= false;
	function __construct($credito = false){
		$this->mCredito	= setNoMenorQueCero($credito);
	}
	function getOCredito(){
		if($this->mObjCred == null){
			if($this->mCredito > DEFAULT_CREDITO){
				$this->mObjCred	= new cCredito($this->mCredito);
				if($this->mObjCred->init() == true){
					$this->mInit	= true;
				}
			}
		}
	}
	function init(){ $this->getOCredito(); return $this->mInit;
	}
	function setVincularGrupo(){

	}
	function setQuitarGrupo(){

	}

}

class cCreditosLeasing {
	public $TIPO_PURO		= 1;
	public $TIPO_FINANCIERO	= 2;
	
	public $TIPO_USO_CARGA	= 200;
	public $TIPO_RAC_PEQ	= 1;
	
	
	private $mClave				= false;
	private $mObj				= null;
	private $mInit				= false;
	private $mNombre			= "";
	private $mMessages			= "";
	private $mIDCache			= "";
	private $mTable				= "";
	private $mIDPersona			= 0;
	private $mIDCredito			= 0;
	private $mAnticipo			= 0;
	private $mFinanciamiento	= 0;
	private $mValorResidual		= 0;
	private $mTasaInteres		= 0;
	private $mTasaTIIE			= 0;
	private $mCuotaSeguro		= 0;
	private $mCuotaMtto			= 0;
	private $mCuotaTenencia		= 0;
	private $mCuotaAccesorios	= 0;
	private $mCuotaIVA			= 0;
	private $mTasaIVA			= 0;
	private $mTipoRAC			= 0;
	private $mNumeroPagos		= 0;
	
	private $mMontoSeguroFin	= 0;
	private $mMontoSeguroInit	= 0;
	
	private $mTenenciaInicial	= 0;
	private $mTenenciaFinanc	= 0;
	
	private $mDomicilia			= false;
	private $mVehiculoDescripcion	= "";
	private $mVehiculoColor			= "";
	private $mVehiculoAnnio			= "";
	private $mVehiculoMarca			= "";
	private $mVehiculoValor			= 0;
	private $mVehiculoExtras		= "";
	private $mVehiculoSerie			= "";
	private $mVehiculoMotor			= "";
	private $mVehiculoPlaca			= "";
	private $mVehiculoProveedor		= 0;
	private $mVehiculoUso			= 0;
	private $mClaveDeUsuario		= 0;
	private $mClaveDeOficial		= 0;
	private $mClaveDeOriginador		= 0;
	private $mClaveDeSubOriginador	= 0;
	private $mClaveDeSegmento		= 0;
	private $mIDVehiculoVinculado	= 0;
	private $mEsMoral				= false;
	private $mArrResiduales			= array();
	private $mArrTasas				= array();
	private $mArrVecs				= array();
	
	private $mFechaCreacion			= false;
	private $mCuotaVehiculo			= 0;
	private $mCuotaGPS				= 0;
	private $mTipoDeUso				= 0;
	private $mEsDeCarga				= false;
	private $mMontoPlacas			= 0;
	private $mMontoNotario			= 0;
	private $mMontoGestoria			= 0;
	private $mMontoDepositoGarantia	= 0;
	private $mMontoRentaProporcional= 0;
	private $mMontoComision			= 0;
	private $mCuotaGtiaExtendida	= 0;
	private $mMontoVehiculo			= 0;
	private $mMontoAliado			= 0;
	private $mMontoAccesorios		= 0;
	private $mMontoGarantiaExt		= 0;
	private $mMontoAnticipo			= 0;
	private $mFactorMas				= 1;
	private $mTasaVec				= 0;
	private $mMontoAjuste			= 0;
	private $mEsAdministrado		= false;
	private $mRentaSinIva			= false;
	private $mTasaComOrg			= 0;
	private $mTasaComAgen			= 0;
	private $mMOIComisiones			= 0;
	private $mMontoComAgencia		= 0;
	private $mMontoComOrigen		= 0;
	private $mClavePlanGPS			= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "originacion_leasing-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
			$xCache->clean("originacion_leasing-credito-" . $this->mIDCredito);
		} 
	}
	function initByCredito($credito){
		$credito				= setNoMenorQueCero($credito);
		$data					= false;
		if($credito > DEFAULT_CREDITO){
			$this->mIDCredito	= $credito;
			$idc				= "originacion_leasing-credito-" . $this->mIDCredito;
			$xCache				= new cCache();
			$data				= $xCache->get($idc);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `originacion_leasing` WHERE `credito`=". $this->mIDCredito . " LIMIT 0,1");
				if(isset($data["idoriginacion_leasing"])){
					$this->mClave		= $data["idoriginacion_leasing"];
					$this->mIDPersona	= $data["persona"];
					$xCache->set($idc, $data);
				}
			}
			$this->init($data);
		}
		return $this->mInit;
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cOriginacion_leasing();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT; //Cambiar
			$this->mClave			= $data[$xT->IDORIGINACION_LEASING];
			$this->mIDCredito		= $data[$xT->CREDITO];
			$this->mIDPersona		= $data[$xT->PERSONA];
			$this->mFinanciamiento	= $data[$xT->TOTAL_CREDITO];
			$this->mNumeroPagos		= $data[$xT->PLAZO]; //$data[$xT->];
			$this->mAnticipo		= $data[$xT->MONTO_ANTICIPO];
			$this->mValorResidual	= $data[$xT->MONTO_RESIDUAL];
			
			$this->mTasaInteres		= ($xT->tasa_credito()->v()/100);
			$this->mTasaTIIE		= ($xT->tasa_tiie()->v() /100);
			
			$this->mCuotaAccesorios	= $xT->cuota_accesorios()->v();
			$this->mCuotaMtto		= $xT->cuota_mtto()->v();
			$this->mCuotaSeguro		= $xT->cuota_seguro()->v();
			$this->mCuotaTenencia	= $xT->cuota_tenencia()->v();
			$this->mCuotaIVA		= $xT->cuota_iva()->v();
			$this->mTasaIVA			= $xT->tasa_iva()->v();
			$this->mMontoSeguroFin	= ($xT->financia_seguro()->v() == 1) ? $xT->monto_seguro()->v() : 0;
			$this->mMontoSeguroInit	= ($xT->financia_seguro()->v() == 0) ? $xT->monto_seguro()->v() : 0;
			$this->mTenenciaFinanc	= ($xT->financia_tenencia()->v() == 1) ? $xT->monto_tenencia()->v() : 0;
			$this->mTenenciaInicial	= ($xT->financia_tenencia()->v() == 0) ? $xT->monto_tenencia()->v() : 0;
			$this->mDomicilia		= ($xT->domicilia()->v() == 1) ? true : false;
			
			$this->mVehiculoDescripcion = $xT->modelo()->v();
			$this->mVehiculoMarca		= $xT->marca()->v();
			$this->mVehiculoAnnio		= $xT->annio()->v();
			$this->mVehiculoValor		= $xT->precio_vehiculo()->v();
			$this->mVehiculoExtras		= $xT->describe_aliado()->v();
			
			$this->mClaveDeUsuario		= $xT->usuario()->v();
			$this->mClaveDeOficial		= $xT->oficial()->v();
			$this->mClaveDeOriginador	= $xT->originador()->v();
			$this->mClaveDeSubOriginador= $xT->suboriginador()->v();
			$this->mEsMoral				= ($xT->es_moral()->v() == SYS_UNO) ? true : false;
			$this->mFechaCreacion		= $xT->fecha_origen()->v();
			$this->mCuotaGPS			= $xT->cuota_gps()->v();
			$this->mCuotaVehiculo		= $xT->cuota_vehiculo()->v();
			$this->mTipoDeUso			= $xT->tipo_uso()->v();
			$this->mEsDeCarga			= ($this->mTipoDeUso == $this->TIPO_USO_CARGA) ? true : false;
			$this->mMontoPlacas			= $xT->monto_placas()->v();
			$this->mMontoNotario		= $xT->monto_notario()->v();
			$this->mMontoGestoria		= $xT->monto_gestoria()->v();
			$this->mMontoDepositoGarantia 	= $data[$xT->RENTA_DEPOSITO];// $xT->renta_deposito()->v();
			$this->mMontoRentaProporcional	= $data[$xT->RENTA_PROPORCIONAL]; //$xT->renta_proporcional()->v();
			$this->mMontoComision			= $xT->monto_comision()->v();
			$this->mCuotaGtiaExtendida		= $xT->cuota_garantia()->v();
			$this->mTipoRAC					= $xT->tipo_rac()->v();
			$this->mMontoVehiculo			= $xT->precio_vehiculo()->v();
			$this->mMontoAliado				= $xT->monto_aliado()->v();
			$this->mMontoAccesorios			= $xT->monto_accesorios()->v();
			$this->mMontoGarantiaExt		= $xT->monto_garantia()->v();
			$this->mMontoAjuste				= $data[$xT->MONTOAJUSTE];// $xT->montoajuste()->v(); //$data[$xT->];
			
			$this->mEsAdministrado			= (setNoMenorQueCero($data[$xT->ADMINISTRADO]) <= 0) ? false : true;
			$this->mRentaSinIva				= (setNoMenorQueCero($data[$xT->NOIVARENT]) <= 0) ? false : true;
			$this->mClaveDeSegmento			= $data[$xT->SEGMENTO];
			$this->mVehiculoUso				= $data[$xT->TIPO_USO];
			$this->mTasaComAgen				= $data[$xT->COM_AGENCIA];
			$this->mTasaComOrg				= $data[$xT->COMISION_ORIGINADOR];
			$this->mMontoComOrigen			= $data[$xT->MONTO_ORIGINADOR];
			$this->mMontoComAgencia			= $data[$xT->MONTOCOM_AGEN];
			$this->mClavePlanGPS			= $data[$xT->TIPO_GPS];
			//$this->mCuotaPrincipal	= $this->getCuota($mCoste, $residual);
			//$this->mCuotaGPS		= $this->getCuota($costeGPS);
			$xRuls							= new cReglaDeNegocio();
			$IvaNoInc						= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_IVA_NOINC);
			
			if($IvaNoInc ==true){
				$this->mFactorMas			= 1 + TASA_IVA;
			}
			//Calcula MOI Comisiones
			
			//$this->mArrResiduales		= 
			//setLog("309.- ". $xT->residuales()->v());
			$arrRes						= explode(",", $xT->residuales()->v());
			foreach ($arrRes as $dres){
				$DTasa	= explode("-", $dres);
				$PRes	= setNoMenorQueCero($DTasa[0]);
				$TRes	= (isset($DTasa[1])) ? $DTasa[1] : 0;
				$TRes	= setNoMenorQueCero($TRes,3);
				
				$this->mArrResiduales[$PRes]	= $TRes;
			}
			$arrVecs						= explode(",", $xT->vecs()->v());
			foreach ($arrVecs as $vres){
				$DTasa	= explode("-", $vres);
				$PRes	= setNoMenorQueCero($DTasa[0]);
				$TRes	= (isset($DTasa[1])) ? $DTasa[1] : 0;
				$TRes	= setNoMenorQueCero($TRes,3);
				$this->mArrVecs[$PRes]			= $TRes;
			}
			$arrTas						= explode(",", $xT->tasas()->v());
			foreach ($arrTas as $tres){
				$DTasa	= explode("-", $tres);
				$PRes	= setNoMenorQueCero($DTasa[0]);
				$TRes	= (isset($DTasa[1])) ? $DTasa[1] : 0;
				$TRes	= setNoMenorQueCero($TRes,3);
				$this->mArrTasas[$PRes]			= $TRes;
			}
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getMontoPlacas(){ return $this->mMontoPlacas*$this->mFactorMas; }
	function getMontoNotario(){ return $this->mMontoNotario*$this->mFactorMas; }
	function getMontoGestoria(){ return $this->mMontoGestoria*$this->mFactorMas; }
	function getMontoDepositoGarantia(){ return $this->mMontoDepositoGarantia*$this->mFactorMas; }
	function getMontoRentaProporcional(){ return $this->mMontoRentaProporcional*$this->mFactorMas; }
	function getMontoComision(){ return $this->mMontoComision*$this->mFactorMas; }
	function getMontoComisionAgencia(){ return $this->mMontoComAgencia; }
	function getMontoComisionOrigen(){ return $this->mMontoComOrigen; }
	/*function getMontoMOIComisiones(){
		$this->mMOIComisiones			= $this->mMontoVehiculo + $this->mMontoAliado + $this->mMontoAccesorios + $this->mMontoSeguroFin;
		$this->mMOIComisiones			+= $this->mTenenciaFinanc;
		return $this->mMOIComisiones;
	}*/
	function getMontoAnticipo($SinIva = false){ 
		$anticipo	= ($SinIva == true) ? $this->mAnticipo : $this->mAnticipo*$this->mFactorMas;
	 	return $anticipo;
	}
	function getAnticipo(){ return $this->mAnticipo*$this->mFactorMas; }
	
	function getMontoAccesorios(){ return $this->mMontoAccesorios*$this->mFactorMas; }
	function getMontoGarantiaExt(){ return $this->mMontoGarantiaExt*$this->mFactorMas; }
	
	function getMontoVehiculo(){ return $this->mMontoVehiculo; }
	function getMontoAliado(){ return $this->mMontoAliado; }
	function getEsAdministrado(){ return $this->mEsAdministrado; }
	function getRentaSinIva(){ return $this->mRentaSinIva; }
	
	function getVehiculoDescripcion(){ return $this->mVehiculoDescripcion;	}
	function getVehiculoMarca(){ return $this->mVehiculoMarca; }
	function getVehiculoAnnio(){ return $this->mVehiculoAnnio; }
	function getVehiculoValor(){ return $this->mVehiculoValor; }
	function getVehiculoExtras(){ return $this->mVehiculoExtras; }
	function getVehiculoUso(){ return $this->mVehiculoUso; }
	function getClaveDeOriginador(){ return $this->mClaveDeOriginador; }
	function getClaveDeSubOriginador(){ return $this->mClaveDeSubOriginador; }
	
	
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){ return $this->mClave; }
	function getDomicilia(){ return $this->mDomicilia; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function setPersona($persona){
		$persona	= setNoMenorQueCero($persona);
		$xQL		= new MQL();
		$xQL->setRawQuery("UPDATE `originacion_leasing` SET `persona`=$persona WHERE `idoriginacion_leasing`=" . $this->mClave);
		$xCP		= new cCreditosProceso();
		$this->setPaso($xCP->PASO_CON_PERSONA);
		
		$xQL		= null;
		$this->setCuandoSeActualiza();
	}
	function setCredito($credito){
		$credito	= setNoMenorQueCero($credito);
		$xQL		= new MQL();
		$xQL->setRawQuery("UPDATE `originacion_leasing` SET `credito`=$credito WHERE `idoriginacion_leasing`=" . $this->mClave);
		$xCP		= new cCreditosProceso();
		$this->setPaso($xCP->PASO_CON_CREDITO);
		$xQL		= null;
		$this->setCuandoSeActualiza();
	}
	function getValorResidual(){ return $this->mValorResidual; }

	function getFinanciamiento(){ return $this->mFinanciamiento; }
	
	
	function getTasaVec($plazo = false){
		$plazo	= setNoMenorQueCero($plazo);
		$tasa	= isset($this->mArrVecs[$plazo]) ? $this->mArrVecs[$plazo] : $this->mTasaVec;
		return $tasa;
	}
	function getTasaInteres($plazo = false){
		$plazo	= setNoMenorQueCero($plazo);
		$tasa	= $this->mTasaInteres;
		if($plazo > 0){
			$tasa	= isset($this->mArrTasas[$plazo]) ? $this->mArrTasas[$plazo] : $this->mTasaInteres;
		}
		return $tasa;
	}
	function getTasaTiie(){ return $this->mTasaTIIE; }
	
	function getCuotaAccesorios(){ return $this->mCuotaAccesorios*$this->mFactorMas; }
	function getCuotaMtto(){ return $this->mCuotaMtto*$this->mFactorMas; }
	function getCuotaSeguro(){ return $this->mCuotaSeguro*$this->mFactorMas; }
	function getCuotaTenencia(){ return $this->mCuotaTenencia*$this->mFactorMas; }
	function getCuotaRenta(){ return round((($this->mCuotaGPS+$this->mCuotaVehiculo)*$this->mFactorMas),2); }
	function getCuotaGtiaExtendida(){ return ($this->mCuotaGtiaExtendida*$this->mFactorMas); }
	
	function getCuotasNoCapitalizadas(){ return (($this->mCuotaMtto+$this->mCuotaSeguro+$this->mCuotaTenencia+$this->mCuotaAccesorios + $this->mCuotaGtiaExtendida)*$this->mFactorMas)+$this->mCuotaIVA; }
	
	function getCuotaIVA(){ return $this->mCuotaIVA; }
	function getTotalCuota(){ 
		$cuota	= $this->getCuotaAccesorios() + $this->getCuotaGtiaExtendida() + $this->getCuotaMtto() + $this->getCuotaRenta() + $this->getCuotaSeguro() + $this->getCuotaTenencia();
		return $cuota;
	}
	function getSeguroInicial(){ return $this->mMontoSeguroInit*$this->mFactorMas; }
	function getSeguroFinanciado(){ return $this->mMontoSeguroFin*$this->mFactorMas; }
	function getTenenciaInicial(){ return $this->mTenenciaInicial*$this->mFactorMas; }
	function getTenenciaFinanciado(){ return $this->mTenenciaFinanc*$this->mFactorMas; }
	function getClaveDeSegmento(){ return $this->mClaveDeSegmento; }
	function getClaveDeOficial(){  return $this->mClaveDeOficial; }
	function getClaveDePersona(){ return $this->mIDPersona; }
 	function getClaveDeCredito(){ return $this->mIDCredito; }
	function getClaveDeVehiculo(){
		$xQL	= new MQL();
		//$xDO	= new cCreditosDatosDeOrigen();
		$xCache	= new cCache();
		$xIDc	= "";
		$xAct	= new cLeasingActivos();
		if($xAct->initForContract($this->mIDCredito) == true){
			$this->mIDVehiculoVinculado	= $xAct->getClave();
			//$this->mIDPersona			= $xAct->getClaveDeProveedor();
			$this->mVehiculoMotor		= $xAct->getMotor();
			$this->mVehiculoPlaca		= $xAct->getPlacas();
			$this->mVehiculoSerie		= $xAct->getSerie();
			$this->mVehiculoProveedor	= $xAct->getClaveDeProveedor();
		}

		return $this->mIDVehiculoVinculado;
	}
	function getClaveDePlanGPS(){ return $this->mClavePlanGPS; }
	function getTasaResidualPzo($plazo, $periodicidad = 30){
		$residual		= 0;
		if(isset($this->mArrResiduales[$plazo])){
			$residual	= $this->mArrResiduales[$plazo];
		}
		return $residual;
	}
	function getEsPersonaMoral(){ return $this->mEsMoral;	}
	function getFechaCreacion(){ return $this->mFechaCreacion; }
	function getEsDeCarga(){ return $this->mEsDeCarga; }
	function getTipoDeRAC(){ return $this->mTipoRAC;	}
	function getMontoAjuste(){ return $this->mMontoAjuste; }
	function getMontoDeducible($monto = 0){
		$montodeducible	= setNoMenorQueCero(CREDITO_LEASING_LIMITE_DED);
		$monto			= ($monto <= 0) ? $this->getTotalCuota() : $monto;
		$deducible		= ($this->mEsDeCarga == true ) ? $monto : $montodeducible;
		if($montodeducible <= 0){
			//$deducible	= $this->getMontoAjuste();
		}
		if($deducible > $monto){
			$deducible	= $monto;
		}
		return $deducible;
	}
	function getValorDeVenta($plazo = 0){
		$xTasa		= new cLeasingTasas();
		$monto		= 0;
		$factoIVA	= 1 / (1+$this->mTasaIVA);
		$plazo		= setNoMenorQueCero($plazo);
		$plazo		= ($plazo <= 0) ? $this->mNumeroPagos : $plazo;
		$tasavec	= $this->getTasaVec($plazo);
		if($tasavec <= 0){
			if($xTasa->initByPlazoRAC($plazo, $this->mTipoRAC) == true){
				$tasavec		= $xTasa->getTasaVEC();
			}
		}
		$valor_sin_iva		= ($this->mMontoVehiculo + $this->mMontoAliado) * $factoIVA;
		$base				= $valor_sin_iva - $this->mAnticipo;
		$monto				= round(($base * ($tasavec/100) ),2);
		
		return $monto;
	}
	function getMOI(){
		$tfinanciado	= $this->getMontoVehiculo() + $this->getMontoAliado() + $this->getMontoAccesorios();
		$tfinanciado	+= $this->getMontoGarantiaExt() + $this->getTenenciaFinanciado() + $this->getSeguroFinanciado();
		$factoriva		= (1/(1+TASA_IVA) );
		$tfinanciado	= $tfinanciado * $factoriva;
		$tfinanciado	= $tfinanciado - $this->getMontoAnticipo();
		
		return round($tfinanciado,2);
	}
	function setPaso($paso){
		$clave	= $this->mClave;
		$xQL	= new MQL();
		$res	= $xQL->setRawQuery("UPDATE `originacion_leasing` SET `paso_proceso`=$paso WHERE `idoriginacion_leasing`=$clave");
	}
	function setOmitidos($pon = null, $quita = null){
		$idx	= "arrendamiento.omitidos." . $this->mClave;
		
		$xCache	= new cCache();
		$dd		= $xCache->get($idx);
		if(!is_array($dd)){
			$pon	= setNoMenorQueCero($pon);
			if($pon > 0){
				$dd[$pon]	= $pon;
				$xCache->set($idx, $dd, $xCache->EXPIRA_UNDIA);
			}
		} else {
			if($quita > 0){
				unset($dd[$quita]);
			}
			if($pon > 0){
				$dd[$pon]	= $pon;
			}
			$xCache->set($idx, $dd, $xCache->EXPIRA_UNDIA);
		}
		if($pon === null AND $quita === null){
			$xCache->clean($idx);
		}
	}
	function getOmitidos(){
		$idx	= "arrendamiento.omitidos." . $this->mClave;
		$xCache	= new cCache();
		$dd		= $xCache->get($idx);
		if(!is_array($dd)){
			$dd	= array();
		}
		return $dd;
	}
	function getCodigoReciboPagoInit(){
		$xQL	= new MQL();
		$dd		= $xQL->getDataRow("SELECT `idoperaciones_recibos` AS `recibo` FROM `operaciones_recibos` WHERE `docto_afectado`=" . $this->mIDCredito . " AND `tipo_docto`= " . RECIBOS_TIPO_PRIMERPAG . " LIMIT 0,1");
		//setLog("SELECT `idoperaciones_recibos` AS `recibo` FROM `operaciones_recibos` WHERE `docto_afectado`=" . $this->mIDCredito . " AND `tipo_docto`= " . RECIBOS_TIPO_PRIMERPAG . " LIMIT 0,1");
		$rr		= (isset($dd["recibo"])) ? setNoMenorQueCero($dd["recibo"]) : 0;
		return $rr;
	}
}
class cLeasingBonos {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "leasing_bonos";
	private $mTipo			= 0;
	private $mDestinatario	= 0;
	
	public $DEST_PROMOTOR	= 1;
	public $DEST_REGIONAL	= 2;
	public $DEST_REFIERE	= 3;
	public $DEST_DPROMO		= 4;
	public $DEST_DALIANZA	= 5;
	public $DEST_EMPLEADO	= 6;
	public $DEST_GTOSOP		= 7;
	public $DEST_EJECUTIVO	= 8;
	
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_bonos();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add($idleasing, $monto, $tasa, $tipo, $destino, $fecha = false){
		$xT		= new cLeasing_bonos();//Tabla
		$xF		= new cFecha();
		$fecha	= $xF->getFechaISO($fecha);
		
		$xT->clave_leasing($idleasing);
		$xT->fecha($fecha);
		$xT->fecha_de_pago($fecha);
		$xT->idleasing_bonos("NULL");
		$xT->monto_bono($monto);
		$xT->tasa_bono($tasa);
		$xT->tipo_bono($tipo);
		$xT->tipo_destino($destino);
		$id	= $xT->query()->insert()->save();
		
		return $id;
	}
	function setCrearSobreLeasing($idleasing){
		$xLeas	= new cCreditosLeasing($idleasing);
		if($xLeas->init() == true){
			//Contar si existe
			$xQL	= new MQL();
			$ddr	= $xQL->getDataRow("SELECT COUNT(*) AS 'items'  FROM `leasing_bonos` WHERE `clave_leasing`=$idleasing");
			$items	= $ddr["items"];
			$xOrig	= new cLeasingOriginadores($xLeas->getClaveDeOriginador());
			$xOrig->init();
			$tipoor	= $xOrig->getTipo();
			$xCom	= new cLeasingComisiones(); $xCom->initByTipoOriginador($tipoor);
			$base	= $xLeas->getMOI();
			$tipo 	= 1;
			
			if($items <= 0){
				//Bono de promotor
				$bpmonto	= $base * $xCom->getComisionOriginador(); $bpmonto = round(($bpmonto/100),2);
				$this->add($idleasing, $bpmonto, $xCom->getComisionOriginador(), $tipo, $this->DEST_PROMOTOR);
				//Bono de Gerente Regional
				$brmonto	= $base * $xCom->getComisionRegional(); $brmonto = round(($brmonto/100),2);
				$this->add($idleasing, $brmonto, $xCom->getComisionRegional(), $tipo, $this->DEST_REGIONAL);
				//Bono de Referenciador
				
				//Ejecutivo
				$bemonto	= $base * $xCom->getComisionEjecutivo(); $bemonto = round(($bemonto/100),2);
				$this->add($idleasing, $bemonto, $xCom->getComisionEjecutivo(), $tipo, $this->DEST_EJECUTIVO);
				//Bono del Director de Operacion
				
				//Bono del Director de Alianza
				
				//Bono personal ABC
				
				//Gastos de Operacion
			}
		}
	}
}

class cLeasingOriginadores {
	private $mClave				= false;
	private $mObj				= null;
	private $mInit				= false;
	private $mNombre			= "";
	private $mMessages			= "";
	private $mIDCache			= "";
	private $mTable				= "";
	private $mTasaComision		= 0;
	private $mMeta				= 0;
	private $mClaveDePersona	= 0;
	private $mDireccion			= "";
	private $mTelefono			= "";
	private $mMail				= "";
	private $mTipo				= 0; 
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "leasing_originadores" . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_originadores();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave			= $data[$xT->getKey()];
			$this->mNombre			= $xT->nombre_originador()->v();
			$this->mMeta			= $xT->meta()->v();
			$this->mTasaComision	= $xT->comision()->v();
			$this->mClaveDePersona	= $xT->clave_de_persona()->v();
			$this->mDireccion		= $xT->direccion()->v();
			$this->mTelefono		= $xT->telefono()->v();
			$this->mMail			= $xT->email_de_contacto()->v();
			$this->mTipo			= $xT->tipo_de_originador()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit			= true;
			$xT 					= null;
		}
		return $this->mInit;
	}

	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){ return $this->mNombre; }
	function getTasaComision(){ return $this->mTasaComision;	}
	function getClave(){ return $this->mClave; }
	function getCorreoElectronico(){ return $this->mMail; }
	function getTelefono(){ return $this->mTelefono; }
	function getDomicilio(){ return $this->mDireccion; }
	function getClaveDePersona(){ return $this->mClaveDePersona; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function getTipo(){ return $this->mTipo; }
	function getFicha(){
		$xLng	= new cLang();
		$html	= "<table>
		<thead></thead>
		<tbody>
		<tr>
		<th>" . $xLng->getT("TR.NOMBRE") . "</th><td>" . $this->mNombre . "</td>
		<th>" . $xLng->getT("TR.TELEFONO") . "</th><td>" . $this->mTelefono . "</td>
		</tr>
		<tr>
		<th>" . $xLng->getT("TR.DOMICILIO") . "</th><td>" . $this->mDireccion . "</td>
		<th>" . $xLng->getT("TR.CORREO_ELECTRONICO") . "</th><td>" . $this->mMail . "</td>
		</tr>
		</tbody>
		<tfoot></tfoot>
		</table>";
		//$html	= "<table><thead></thead><tbody></tbody><tfoot></tfoot></table>";
		
		return $html;
	}
}

class cLeasingUsuarios {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable				= "";
	private $mClaveOriginador	= false;
	private $mClaveUsuario		= false;
	private $mEsActivo			= false;
	private $mEsAdmin			= false;
	private $mMail				= "";
	private $mOOriginador		= null;
	private $mTasaComision		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "leasing_usuarios" . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_usuarios();//Tabla
		$inCache= true;
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT; //Cambiar
			$this->mClave			= $data[$xT->IDLEASING_USUARIOS];
			
			$this->mClaveOriginador	= $data[$xT->ORIGINADOR];
			$this->mClaveUsuario	= setNoMenorQueCero($data[$xT->IDUSUARIO]);
			$this->mEsActivo		= ($data[$xT->ESTATUS]== SYS_UNO) ? true: false;
			$this->mEsAdmin			= ($data[$xT->ADMINISTRADOR]== SYS_UNO) ? true : false;
			$this->mMail			= $data[$xT->CORREO_ELECTRONICO];
			$this->mNombre			= $data[$xT->NOMBRE];
			$this->mTasaComision	= $data[$xT->TASA_COM];

			if($inCache == false){
				$this->setIDCache($this->mClave);
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}

	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function initByIDUsuario($user = 0){
		$user	= setNoMenorQueCero($user);
		$res	= false;
		if($user >0){
			$xQL	= new MQL();
			$data	= $xQL->getDataRow("SELECT * FROM `leasing_usuarios` WHERE `idusuario`=". $user . " LIMIT 0,1");
			$res	= $this->init($data);
		}
		return $res;
	}
	function getOriginador(){ return $this->mClaveOriginador; }
	function getSubOriginador(){return $this->mClave;}
	function getTasaComision(){ return $this->mTasaComision; }
	function getUsuario(){ return $this->mClaveUsuario; }
	function getEsActivo(){ return $this->mEsActivo; }
	function getEsAdmin(){ return $this->mEsAdmin; }
	function getCorreoElectronico(){ return $this->mMail; }
	function setPin($pin = ""){
		$xUser	= new cSystemUser($this->getUsuario());
		$xQL	= new MQL();
		$iduser	= $this->getUsuario();
		
		if($xUser->init() === false OR $iduser <= 0 ){
			
			$xImport	= new cTiposLimpiadores();
			$NC			= $xImport->cleanNombreComp($this->mNombre);
			$xUser->add($this->getCorreoElectronico(), $pin, USUARIO_TIPO_ORIGINADOR, $NC[0], $NC[1], $NC[2] );
			$xUser		= new cSystemUser($this->getCorreoElectronico(), false);
			$xUser->init();
			$iduser		= $xUser->getID();
			
		} else {
			$xUser->setPassword($pin);
		}
		$pin	= $xUser->getHash($pin);
		$res	= $xQL->setRawQuery("UPDATE `leasing_usuarios` SET `pin`='$pin', `idusuario`=$iduser WHERE `idleasing_usuarios`=" . $this->mClave);
		$this->setCuandoSeActualiza();
		return ($res === false) ? false : true;
	}
	function add(){}
	function getFicha(){
		$xHT			= new cHTabla("idfichausrorg_" . $this->getClave(), "ficha" );
		$xLng			= new cLang();
		$xHT->initRow();
		$nmoriginador	= $this->getOOriginador()->getNombre();
		$xHT->addTD($xLng->getT("TR.NOMBRE"), " class='title cPanel' ");
		$xHT->addTD($this->getNombre());
		$xHT->addTD($xLng->getT("TR.ORIGINADOR"), " class='title cPanel' ");
		$xHT->addTD($nmoriginador);
		$xHT->endRow();
		return $xHT->get();
	}
	function getOOriginador(){
		if($this->mOOriginador == null){
			$this->mOOriginador	= new cLeasingOriginadores($this->getOriginador());
			$this->mOOriginador->init();
		}
		return $this->mOOriginador;
	}
}

class cLeasingTasas {
	private $mClave				= false;
	private $mObj				= null;
	private $mInit				= false;
	private $mNombre			= "";
	private $mMessages			= "";
	private $mIDCache			= "";
	private $mTable				= "";
	private $mPlazo				= 0;
	private $mTasa				= 0;
	private $mTipoRac			= 0;
	private $mTasaVEC			= 0;
	
	private $mComisionApertura	= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "leasing_tasas" . "-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache(); 
			$xCache->clean($this->mIDCache);
			$xCache->clean("leasing_tasas-plazo-rac-" . $this->mPlazo . "-" . $this->mTipoRac);
		}
	}
	function init($data = false){
		$xCache		= new cCache();
		$xT			= new cLeasing_tasas();
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj					= $xT; //Cambiar
			$this->mClave				= $data[$xT->getKey()];
			$this->mTasa				= $xT->tasa_ofrecida()->v();
			$this->mPlazo				= $xT->limite_superior()->v();
			$this->mTipoRac				= $xT->tipo_de_rac()->v();
			$this->mComisionApertura	= $xT->comision_apertura()->v();
			$this->mTasaVEC				= $xT->tasa_vec()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit				= true;
			$xT 						= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function getTasa(){ return $this->mTasa; }
	function getComisionApertura(){ return $this->mComisionApertura; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function initByPlazoRAC($plazo, $rac){
		$xCache		= new cCache();
		$idx		= "leasing_tasas-plazo-rac-$plazo-$rac";
		$datos		= $xCache->get($idx);
		
		if(!is_array($datos)){
			$xQL	= new MQL();
			$datos	= $xQL->getDataRow("SELECT * FROM `leasing_tasas` WHERE ($plazo >= `limite_inferior` AND $plazo<=`limite_superior`) AND `tipo_de_rac`=$rac LIMIT 0,1");
		}
		if(isset($datos["idleasing_tasas"])){
			$this->mClave		= $datos["idleasing_tasas"];
			$this->mPlazo		= $plazo;
			$this->mTipoRac		= $rac;
			$xCache->set($idx, $datos);
		}
		return $this->init($datos);
	}
	function getTasaVEC(){ return $this->mTasaVEC; }
}
class cLeasingComisiones {
	private $mClave					= false;
	private $mObj					= null;
	private $mInit					= false;
	private $mNombre				= "";
	private $mMessages				= "";
	private $mIDCache				= "";
	private $mTable					= "leasing_comisiones";
	private $mComisionOriginador	= 0;
	private $mComisionEjecutivo		= 0;
	private $mComisionRegional		= 0;
	private $mTasaBono				= 0;
	private $mMetaBono				= 0;
	private $mTipoOriginador		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "leasing_comisiones-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache(); 
			$xCache->clean($this->mIDCache);
			$xCache->clean("leasing_comisiones-by-tipo-" . $this->mTipoOriginador);
		} 
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_comisiones();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj					= $xT;
			$this->mClave				= $data[$xT->getKey()];
			$this->mTasaBono			= $xT->bono()->v();
			$this->mTipoOriginador		= $xT->tipo_de_originador()->v();
			$this->mComisionEjecutivo	= $xT->comision_ejecutivo()->v();
			$this->mComisionOriginador	= $xT->tasa_comision()->v();
			$this->mComisionRegional	= $xT->comision_regional()->v();
			
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){ return $this->mNombre; }
	function getClave(){ return $this->mClave; }
	function getComisionOriginador(){ return $this->mComisionOriginador; }
	function getComisionEjecutivo(){ return $this->mComisionEjecutivo; }
	function getComisionRegional(){ return $this->mComisionRegional; }
	
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function initByTipoOriginador($tipo){
		$xCache			= new cCache();
		$this->mTipoOriginador	= $tipo;
		$datos			= $xCache->get("leasing_comisiones-by-tipo-" . $this->mTipoOriginador);
		if(!is_array($datos)){
			$xQL		= new MQL();
			$datos		= $xQL->getDataRow("SELECT * FROM `leasing_comisiones` WHERE `tipo_de_originador`=$tipo LIMIT 0,1");
		}
		if(isset($datos["idleasing_comisiones"])){
			$this->mClave	= $datos["idleasing_comisiones"];
			$this->init($datos);
		}
		return $this->mInit;
	}
}

class cLeasingGPSCosteo {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mPlazo		= 0;
	private $mTipo		= 0;
	private $mMonto		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "vehiculos_gps_costeo-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache); 
			$idcx			= "vehiculos_gps_costeo-plazo-tipo-" . $this->mPlazo ."-" . $this->mTipo;
			$xCache->clean($idcx);
		} 
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cVehiculos_gps_costeo();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->mMonto	= $xT->monto_gps()->v();
			$this->mTipo	= $xT->tipo_de_gps()->v();
			$this->mPlazo	= $xT->limite_superior()->v();
			
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function getPlazo(){ return $this->mPlazo; }
	function getMonto(){ return $this->mMonto; }
	function getTipo(){ return $this->mTipo; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function initByPlazoTipo($plazo, $tipo){
		$this->mPlazo	= $plazo;
		$this->mTipo	= $tipo;
		$xCache			= new cCache();
		$idcx			= "vehiculos_gps_costeo-plazo-tipo-" . $this->mPlazo ."-" . $this->mTipo;
		$datos			= $xCache->get($idcx);
		if(!is_array($datos)){
			$xQL		= new MQL();
			$datos		= $xQL->getDataRow("SELECT * FROM `vehiculos_gps_costeo` WHERE ($plazo>=`limite_inferior` AND $plazo<=`limite_superior`) AND `tipo_de_gps`=$tipo LIMIT 0,1");
		}
		if(isset($datos["idvehiculos_gps_costeo"])){
			$this->mClave	= $datos["idvehiculos_gps_costeo"];
			$this->init($datos);
			$xCache->set($idcx, $datos);
		}
		//
		return $this->mInit;
	}
}

class cLeasingValorResidual {
	private $mClave				= false;
	private $mObj				= null;
	private $mInit				= false;
	private $mNombre			= "";
	private $mMessages			= "";
	private $mIDCache			= "";
	private $mTable				= "";
	private $mPorcientoResidual	= 0;
	private $mPlazo				= 0;
	private $mTipo				= 0;
	
	function __construct($clave = false){
		$this->mClave	= setNoMenorQueCero($clave);
		$this->setIDCache($this->mClave);
	}
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "leasing_residual-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
			$idcx			= "leasing_residual-plazo-tipo-" . $this->mPlazo ."-" . $this->mTipo;
			$xCache->clean($idcx);
		} 
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_residual();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj					= $xT;
			$this->mClave				= $data[$xT->getKey()];
			$this->mPorcientoResidual	= $xT->porciento_residual()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function getPorcientoResidual(){ return $this->mPorcientoResidual; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function initByPlazoTipo($plazo, $tipo=0){
		$this->mPlazo	= $plazo;
		$this->mTipo	= $tipo;
		$xCache			= new cCache();
		$idcx			= "leasing_residual-plazo-tipo-" . $this->mPlazo ."-" . $this->mTipo;
		$datos			= $xCache->get($idcx);
		if(!is_array($datos)){
			$xQL		= new MQL();
			$datos		= $xQL->getDataRow("SELECT * FROM `leasing_residual` WHERE ($plazo>=`limite_inferior` AND $plazo<=`limite_superior`) LIMIT 0,1");
		}
		if(isset($datos["idleasing_residual"])){
			$this->mClave	= $datos["idleasing_residual"];
			$xCache->set($idcx, $datos);
		}
		return $this->init($datos);
	}
}

class cCreditosDatosDeOrigen {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mCredito		= 0;
	private $mTipoOrigen	= 0;
	private $mMontoOrigen	= 0;
	private $mClaveOrigen	= 0;
	
	public $ORIGEN_PRESUPUESTO		= 280;
	public $ORIGEN_PRECLIENTE		= 270;
	public $ORIGEN_NOMINA			= 290;
	public $ORIGEN_LINEA			= 295;
	public $ORIGEN_ARRENDAMIENTO	= 281;
	public $ORIGEN_RENOVACION		= 3;
	public $ORIGEN_REESTRUCTURA		= 4;
	
	function __construct($clave = false, $credito = false){
		$this->mClave	= setNoMenorQueCero($clave);
		$this->mCredito	= setNoMenorQueCero($credito);
		$this->setIDCache($this->mClave);
	}
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "creditos_datos_originacion-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
			$xCache->clean("creditos_datos_originacion-credito-" . $this->mCredito);
		}
	}
	function initByCredito($credito = false){
		$credito		= setNoMenorQueCero($credito);
		$credito		= ($credito <= DEFAULT_CREDITO) ? $this->mCredito : $credito;
		$this->mCredito	= $credito;
		$data			= false;
		if($credito > DEFAULT_CREDITO){
			$xCache		= new cCache();
			$data		= $xCache->get("creditos_datos_originacion-credito-" . $this->mCredito);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `creditos_datos_originacion` WHERE `credito`=". $this->mCredito . " ORDER BY `tiempo` DESC LIMIT 0,1");
			}
			if(isset($data["credito"])){
				$this->mClave	= $data["idcreditos_datos_originacion"]; 
				$xCache->set("creditos_datos_originacion-credito-" . $this->mCredito, $data);
			}
		}
		return $this->init($data);
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cCreditos_datos_originacion();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj			= $xT; //Cambiar
			$this->mClave		= $data[$xT->getKey()];
			$this->mTipoOrigen	= $xT->tipo_originacion()->v();
			$this->mClaveOrigen	= $xT->clave_vinculada()->v();
			$this->mMontoOrigen = $xT->monto_vinculado()->v();
			
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
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function getClaveDeOrigen(){ return $this->mClaveOrigen; }

	function getTipoDeOrigen(){ return $this->mTipoOrigen; }
	function getMontoDeOrigen(){ return $this->mMontoOrigen; }
	
	function add($tipo_de_origen = false, $id_de_origen = false, $Monto = 0){
		$id_de_origen	= setNoMenorQueCero($id_de_origen);
		$tipo_de_origen	= setNoMenorQueCero($tipo_de_origen);
		$Monto			= setNoMenorQueCero($Monto);
		
		$xOrg	= new cCreditos_datos_originacion();
		$xOrg->idcreditos_datos_originacion("NULL");
		$xOrg->credito($this->mCredito);
		$xOrg->idusuario(getUsuarioActual());
		$xOrg->tiempo(time());
		$xOrg->tipo_originacion($tipo_de_origen);
		$xOrg->clave_vinculada($id_de_origen);
		$xOrg->monto_vinculado($Monto);
		
		$rs	= $xOrg->query()->insert()->save();
		
		if($rs === false){
			return false;
		} else {
			$this->mTipoOrigen 	= $tipo_de_origen;
			$this->mClaveOrigen	= $id_de_origen;
			return true;
		}
		
	}
	function getDescripcion(){
		$describe	= "";
		switch ($this->mTipoOrigen){
			case $this->ORIGEN_RENOVACION:
				$describe	= "Credito No. " . $this->mClaveOrigen . " Renovado por " . getFMoney($this->mMontoOrigen);
				break;
			case $this->ORIGEN_REESTRUCTURA:
				$describe	= "Credito No. " . $this->mClaveOrigen . " Reestructurado por " . getFMoney($this->mMontoOrigen);
				break;
		}
		return $describe;
	}
	function getMontoActualPorOrigen($clave, $tipo_origen){
		$xQL			= new MQL();
		$dispuesto 		= $xQL->getDataValue("SELECT getMontoActualPorOrigen($clave, $tipo_origen) AS 'monto' ", 'monto');
		return $dispuesto;
	}
}

class cLeasingEmulaciones {
	private $mPlazo				= 0;
	private $mAnticipo			= 0;
	private $mTasaIVA			= 0;
	private $mFrecuencia		= 0;
	private $mTasaAnual			= 0;
	private $mCuotaPrincipal	= 0;
	private $mCuotaGPS			= 0;
	private $mCuotaSeguro		= 0;
	private $mCuotaTenencia		= 0;
	private $mCuotaMtto			= 0;
	private $mCuotaAccesorios	= 0;
	private $mCuotaAliado		= 0;
	private $mCuotaRenta		= 0;
	private $mMComisionAgen		= 0;
	private $mMComisionOrg		= 0;
	
	public $FACTOR_RENTAPROP	= 1;
	public $FACTOR_RENTADEP		= 1;
	//private $mCuota
	function __construct($plazo, $TasaInteres, $Frecuencia, $TasaIVA = 0){
		$this->mPlazo 		= setNoMenorQueCero($plazo,0);
		$this->mTasaIVA		= setNoMenorQueCero($TasaIVA, 4);
		$this->mTasaAnual	= setNoMenorQueCero($TasaInteres, 6);
		$this->mFrecuencia	= setNoMenorQueCero($Frecuencia,0);
	}
	function getCuotaRenta($precio, $anticipo, $residual, $aliado = 0, $costeGPS = 0){
		$xRuls				= new cReglaDeNegocio();
		$SumComisPrinc		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_SUM_COMS);
		
		$precio				= $this->getMontoSinIva($precio);
		$aliado				= $this->getMontoSinIva($aliado);
		$costeGPS			= $this->getMontoSinIva($costeGPS);
		
		$mCoste					= ($precio+$aliado) - $anticipo;
		if($SumComisPrinc == true){
			$mCoste				= $mCoste + $this->mMComisionAgen + $this->mMComisionOrg;
			//setLog("Sumar Agencia : " . $this->mMComisionAgen );
			//setLog("Sumar Origen : " . $this->mMComisionOrg );
		}
		//setLog($mCoste);
		$this->mCuotaPrincipal	= $this->getCuota($mCoste, $residual);
		$this->mCuotaGPS		= $this->getCuota($costeGPS);
		
		
		//setLog($this->mPlazo . " -- "  . $this->mCuotaGPS);
		//setLog($this->mTasaAnual . " --- " . $this->mPlazo . " $costeGPS  " . $this->mFrecuencia);
		$this->mCuotaRenta		= round(($this->mCuotaGPS+$this->mCuotaPrincipal),2);
		
		return $this->mCuotaRenta;
	}
	function setMontoComisionAgen($monto){ $this->mMComisionAgen = $monto; }
	function setMontoComisionOrg($monto){ $this->mMComisionOrg = $monto; }
	function getCuotaSeguro($monto, $financia = false){
		$financia			= ($financia == 1 OR $financia == true) ? true : false;
		$this->mCuotaSeguro	= 0;
		if($financia == true){
			$monto				= $this->getMontoSinIva($monto);
			$this->mCuotaSeguro	= $this->getCuota($monto);
			
		}
		return $this->mCuotaSeguro;
	}
	function getCuotaTenencia($monto, $financia = false){
		$financia				= ($financia == 1 OR $financia == true) ? true : false;
		$this->mCuotaTenencia	= 0;
		if($financia == true){
			$monto				= $this->getMontoSinIva($monto);
			$this->mCuotaTenencia= $this->getCuota($monto);
				
		}
		return $this->mCuotaTenencia;
	}
	function getCuotaMtto($monto){
		$this->mCuotaMtto	= 0;
		$monto				= $this->getMontoSinIva($monto);
		$this->mCuotaMtto	= $this->getCuota($monto);
		return $this->mCuotaMtto;
	}
	function getCuotaAccesorios($monto){
		$this->mCuotaAccesorios	= 0;
		$monto					= $this->getMontoSinIva($monto);
		$this->mCuotaAccesorios	= $this->getCuota($monto);
		return $this->mCuotaAccesorios;
	}

	private function getMontoSinIva($monto){
		
		
		if($monto > 0){
			$monto		= (1/(1+$this->mTasaIVA)) * $monto;
			
			return round($monto,2);
		} else {
			return 0;
		}
	}
	private function getCuota($monto, $residual = 0){
		
		if($monto > 0){
			$xMath		= new cMath();
			return $xMath->getPagoLease($this->mTasaAnual, $this->mPlazo, $monto, $this->mFrecuencia, $residual);
		} else {
			return 0;
		}
	}
	function getValorResidual($precio, $aliado, $plazo = 0, $TasaResidual = 0, $anticipo = 0, $admin = false){
		$xRes		= new cLeasingValorResidual();
		$xRuls		= new cReglaDeNegocio();
		$ConAnt		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_RES_CON_ANT);
		$ConIVA		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_RES_CON_IVA);
		
		$anticipo	= setNoMenorQueCero($anticipo);
		$plazo		= setNoMenorQueCero($plazo);
		$plazo		= ($plazo <= 0) ? $this->mPlazo : $plazo;
		$TasaResidual	= setNoMenorQueCero($TasaResidual);
		
		
		
		$residual		= 0;
		if($xRes->initByPlazoTipo($plazo) == true){
			if($admin == false){
			$TasaResidual	= ($TasaResidual <= 0) ? $xRes->getPorcientoResidual() : $TasaResidual;
			}
			
			$FactorIVA	= (1/(1+TASA_IVA));
			$coste		= ($precio+$aliado);
			$base		= $coste;
			$tasa		= ($TasaResidual / 100);
			
			if($ConIVA == false){
				$base	= ($coste * $FactorIVA);
			}
			
			if($ConAnt == false){
				$base	= $base - $anticipo;
			}
			$residual	= $base * $tasa;
		}
		
		$residual		= round($residual,2);
		return $residual;
	}

	function getCuotaAliado($monto){
		$this->mCuotaAliado	= 0;
		$monto				= $this->getMontoSinIva($monto);
		$this->mCuotaAliado	= $this->getCuota($monto);
		return $this->mCuotaAliado;
	}
	function getMontoRentaDeposito(){
		//$xRuls			= new cReglaDeNegocio();
		$CUOTA_RENTA	= $this->mCuotaRenta;
		$CUOTA_RENTA	= $CUOTA_RENTA * $this->FACTOR_RENTADEP;
		$CUOTA_RENTA	= $CUOTA_RENTA * (1+$this->mTasaIVA);
		return $CUOTA_RENTA;
	}
	function getMontoRentaProp(){
		$xRuls			= new cReglaDeNegocio();
		
		$CUOTA_RENTA	= $this->mCuotaRenta;
		$CUOTA_RENTA	= $CUOTA_RENTA * $this->FACTOR_RENTAPROP;
		$CUOTA_RENTA	= $CUOTA_RENTA * (1+$this->mTasaIVA);
		return $CUOTA_RENTA;
	}
	function getMontoGPS($plan){
		$xGPS			= new cLeasingGPSCosteo();
		$monto			= 0;
		if($xGPS->initByPlazoTipo($this->mPlazo, $plan) == true){
			$monto		= $xGPS->getMonto();
			//$monto		= $this->getMontoSinIva($monto);
		}
		return $monto;
	}
	function getValorDeVenta($PrecioVehiculo, $Anticipo = 0, $TasaVec = 0, $plazo = 0, $MontoAliado = 0, $TipoRac = false){
		//
		$monto		= 0;
		$factorIVA	= 1 / (1+TASA_IVA);
		$plazo		= ($plazo <= 0) ? $this->mPlazo : $plazo;
		if($TasaVec <= 0){
			$xTasa		= new cLeasingTasas();
			if($xTasa->initByPlazoRAC($plazo, $TipoRac) == true){
				$TasaVec	= $xTasa->getTasaVEC();
			}
		}
		$valor_sin_iva		= ($PrecioVehiculo + $MontoAliado) * $factorIVA;
		$base				= $valor_sin_iva - $Anticipo;
		$monto				= round(($base * ($TasaVec/100) ),2);

		return $monto;
	}
	function getFmtTasa($v){
		return number_format($v, 2);
	}
}


class cLeasingActivos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "leasing_activos";
	private $mTipo		= 0;
	private $mIDLeasing	= 0;
	private $mIDPersona	= 0;
	private $mIDCredito	= 0;
	private $mIDProveedor 	= 0;
	private $mValor			= 0;
	private $mIDSegmento	= "";
	
	private $mSerie		= "";
	private $mColor		= "";
	private $mMotor		= "";
	private $mPlacas	= "";
	private $mNIV		= "";
	private $mFactura	= "";
	private $mMontoVEC	= 0;
	private $mModelo	= "";
	private $mAnnio		= "";
	private $mIDMarca	= 0;
	private $mEstatus	= 0;
	
	
	public $ESTADO_ACTIVO 		= 1;
	public $ESTADO_BAJA 		= 0;
	
	
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_activos();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->mIDCredito	= $data[$xT->CREDITO];
			$this->mIDLeasing	= $data[$xT->IDLEASING_ACTIVOS];
			$this->mIDPersona	= $data[$xT->PERSONA];
			$this->mSerie		= $data[$xT->SERIE];
			$this->mMotor		= $data[$xT->MOTOR];
			$this->mPlacas		= $data[$xT->PLACAS];
			$this->mFactura		= $data[$xT->FACTURA];
			$this->mColor		= $data[$xT->COLOR];
			$this->mIDProveedor	= $data[$xT->PROVEEDOR];
			$this->mMontoVEC	= $data[$xT->VALOR_VENTA];
			$this->mNIV			= $data[$xT->SERIE_NAL];
			$this->mNombre		= $data[$xT->DESCRIPCION];
			$this->mModelo		= $data[$xT->ANNIO];
			$this->mAnnio		= $data[$xT->ANNIO];
			$this->mValor		= $data[$xT->VALOR_NOMINAL];
			$this->mIDMarca		= $data[$xT->MARCA];
			$this->mEstatus		= $data[$xT->STATUS];
			$this->mIDSegmento	= $data[$xT->SEGMENTO];
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function getClaveDeMarca(){return $this->mIDMarca;}
	function getClaveDeSegmento(){ return $this->mIDSegmento; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function getSerie(){ return $this->mSerie; }
	function getMotor(){ return $this->mMotor; }
	function getValor(){ return $this->mValor; }
	function getPlacas(){ return $this->mPlacas; }
	function getFactura(){ return $this->mFactura; }
	function getColor(){ return $this->mColor; } 
	function getNIV(){ return $this->mNIV; }
	function getAnnio(){ return $this->mAnnio; }
	function getModelo(){ return $this->mModelo; }
	function getClaveDeProveedor(){ return $this->mIDProveedor; }
	function initForContract($credito){
		$xCache	= new cCache();
		$idx	= $this->mTabla . "-by-ct-$credito";
		$sql	= "SELECT  * FROM `leasing_activos` WHERE ( `leasing_activos`.`credito` = $credito ) AND ( `leasing_activos`.`tipo_activo` = 100 ) LIMIT 0,1";
		$datos	= $xCache->get($idx);
		if(!is_array($datos)){
			$xQL	= new MQL();
			$datos	= $xQL->getDataRow($sql);
			if(isset($datos["idleasing_activos"])){
				$this->mClave		= $datos["idleasing_activos"];
				$this->mIDCredito	= $credito;
				$xCache->set($idx, $datos);
			}
		}
		return $this->init($datos);
	}
	function getMontoVEC(){ return $this->mMontoVEC; }
	function getClaveDeCredito(){return $this->mIDCredito;}
	function getClaveDePersona(){return $this->mIDPersona;}
}

class cLeasingRentas {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "leasing_rentas";
	private $mTipo		= 0;
	private $mIDLeasing	= 0;
	private $mIDCredito	= 0;
	private $mPeriodo	= 0;
	

	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_rentas();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add($fecha, $periodo, $deducible, $nodeducible, $ivarenta = 0, $ivaotros = 0){
		$xT		= new cLeasing_rentas();
		
		$xT->idleasing_renta("NULL");
		$xT->credito($this->mIDCredito);
		$xT->clave_leasing($this->mIDLeasing);
		$xT->clave_no_ded(99);
		$xT->periodo($periodo);
		
		
		$xT->deducible($deducible);
		$xT->fecha($fecha);
		$xT->fecha_max($fecha);
		$xT->fecha_pago($fecha);
		$xT->iva_ded($ivarenta);
		$xT->iva_no_ded($ivaotros);
		$xT->no_deducible($nodeducible);
		$total	= $deducible + $nodeducible + $ivaotros + $ivarenta;
		$xT->total($total);
		$clave = $xT->query()->insert()->save();
		return $clave;
	}
	function setCrearPorCredito($credito){
		$credito	= setNoMenorQueCero($credito);
		$xQL		= new MQL();
		//$credito	= ($credito)
		$xCred		= new cCredito($credito);
		if($xCred->init() == true){
			$this->mIDCredito	= $credito;
			$this->mIDLeasing	= $xCred->getClaveDeOrigen();
			$renta				= $xCred->getMontoDeParcialidad();
			$xLeasing			= new cCreditosLeasing($this->mIDLeasing);
			$tasaiva			= TASA_IVA; //$xCred->getTasaIVA();
			$factorIVA			= 1 / (1+$tasaiva);
			$xQL->setRawQuery("DELETE FROM `leasing_rentas` WHERE `credito`=$credito");
			
			$xLeasing->init();
			
			//var_dump($xLeasing->getTotalCuota());
			//var_dump($renta);
			
			$xPlan				= new cPlanDePagos($xCred->getNumeroDePlanDePagos());
			if($xPlan->init() == true){
				$arrPends = $xPlan->initParcsPendientes();
				
				foreach ($arrPends as $idperiodo => $dd){
					
					
					
					$xLetra				= new cCreditosLetraDePago($credito, $idperiodo);
					if($xLetra->init($dd) == true){
						$letratotal		= $xLetra->getTotal();
						$letratotal		= $letratotal * $factorIVA;
						
						$deducible		= $xLeasing->getMontoDeducible($letratotal);
						$nodeducible	= setNoMenorQueCero(($letratotal - $deducible));
						if($xLeasing->getMontoAjuste()>0){
							
							$nodeducible	= $xLeasing->getMontoAjuste();
							$deducible		= setNoMenorQueCero(($letratotal - $nodeducible));
						}
						$ivadeducible	= $deducible * $tasaiva;
						$ivaotros		= $nodeducible * $tasaiva;
						
						$idrenta 		= $this->add($xLetra->getFechaDePago(), $idperiodo, $deducible, $nodeducible, $ivadeducible, $ivaotros);
						
						
					}
				}
			}
			
		}
	}
	function setAmortizarRenta($monto, $periodo, $recibo,  $credito = false){
		$periodo	= setNoMenorQueCero($periodo);
		$credito	= setNoMenorQueCero($credito);
		$credito	= ($credito > DEFAULT_CREDITO) ? $credito : $this->mIDCredito;
		$monto		= setNoMenorQueCero($monto, 2);
		$xQL		= new MQL();
		
		$res		= $xQL->setRawQuery("UPDATE `leasing_rentas` SET `suma_pagos`=(`suma_pagos`+$monto), `recibo_pago` = $recibo WHERE `credito`=$credito AND `periodo`=$periodo");
		
		return ($res === false) ? false : true;
	}
	function initByCreditoPeriodo($credito, $periodo){
		$periodo	= setNoMenorQueCero($periodo);
		$credito	= setNoMenorQueCero($credito);
		$xCache		= new cCache();
		$idx		= $this->mTabla . "by-c-l-$credito-$periodo";
		$data		= $xCache->get($idx);
		if(!is_array($data)){
			$xQL	= new MQL();
			$date	= $xQL->getDataRow("SELECT * FROM `leasing_rentas` WHERE `credito`=$credito AND  `periodo`=$periodo LIMIT  0,1");
			if(isset($data["idleasing_renta"])){
				$this->mClave		= $data["idleasing_renta"];
				$this->mPeriodo		= $periodo;
				$this->mIDCredito	= $credito;
				
				$xCache->set($idx, $data);
			}
		}
		return $this->init($data);
		
	}
	function setCredito($credito){ $this->mIDCredito = setNoMenorQueCero($credito); }
}


class cCreditosLineas {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "creditos_lineas";
	private $mTipo		= 0;
	private $mMontoOriginal	= 0;
	private $mDescribeGar	= "";
	private $mFecha			= false;
	private $mVencimiento	= false;
	private $mTasa			= 0;
	private $mMontoDispuesto= 0;
	private $mSaldo			= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
		}
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cCreditos_lineas();//Tabla
		if(!is_array($data)){
			$data		= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT; //Cambiar
			$this->mClave			= $data[$xT->getKey()];
			$this->mMontoOriginal	= $xT->monto_linea()->v();
			$this->mDescribeGar		= $xT->numerohipoteca()->v() . " - $ " . $xT->monto_hipoteca()->v();
			$this->mVencimiento		= $xT->fecha_de_vencimiento()->v();
			$this->mTasa			= $xT->tasa()->v();
			$this->mMontoDispuesto	= ($xT->monto_linea()->v() - $xT->saldo_disponible()->v());
			$this->mSaldo			= $xT->saldo_disponible()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function getMontoDisponible(){
		$xDO			= new cCreditosDatosDeOrigen();
		$dispuesto		= $xDO->getMontoActualPorOrigen($this->mClave, $xDO->ORIGEN_LINEA);
		$this->mMontoDispuesto = $dispuesto;
		$monto			= setNoMenorQueCero($this->mMontoOriginal - $dispuesto);
		
		return $monto;
	}
	function getFicha(){
		$html	= "";
		$html	.= "<table>";
		$xLg	= new cLang();
		$xF		= new cFecha();
		/*$html	.= "<tr>";
		$html	.= "<th></th>";
		$html	.= "<td></td>";
		$html	.= "</tr>";*/

		$html	.= "<tr>";
		$html	.= "<th>" .  $xLg->getT("TR.CLAVE"). "</th>";
		$html	.= "<td>" . $this->mClave . "</td>";

		$html	.= "<th>"  .  $xLg->getT("TR.ESTATUS"). "</th>";
		$html	.= "<td>"  .  $xLg->getT("TR.ESTATUSACTIVO"). "</td>";

		$html	.= "<th>"  .  $xLg->getT("TR.FECHA_DE_EMISION") . "</th>";
		$html	.= "<td>"  .  $xF->getFechaMediana($this->mFecha) . "</td>";
		$html	.= "</tr>";
		
		$html	.= "<tr>";
		$html	.= "<th>" .  $xLg->getT("TR.MONTO"). "</th>";
		$html	.= "<td>" . getFMoney($this->mMontoOriginal) . "</td>";
		
		$html	.= "<th>"  .  $xLg->getT("TR.DISPUESTO"). "</th>";
		$html	.= "<td>"  .  getFMoney($this->mMontoDispuesto) . "</td>";
		
		$html	.= "<th>"  .  $xLg->getT("TR.SALDO") . "</th>";
		$html	.= "<td>"  .  getFMoney($this->mSaldo) . "</td>";
		$html	.= "</tr>";
		
		/*$html	.= "<tr>";
		$html	.= "<th>" . "</th>";
		$html	.= "<td>" . "</td>";
		$html	.= "</tr>";*/

		$html	.= "</table>";
		
		return $html;
	}
}


class cLeasingTramitesCatalogo {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "leasing_tramites_cat";
	private $mTipo		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_tramites_cat();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->mNombre	= $xT->nombre_tramite()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	
}




class cLeasingEscenarios {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "leasing_escenarios";
	private $mTipo		= 0;
	private $mPlazo		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cLeasing_escenarios();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			
			$this->mPlazo	= $xT->plazo()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function getAll(){
		$xQL	= new MQL();
		//$xEsc	= new cLeasing_escenarios();
		$rs		= $xQL->getDataRecord("SELECT * FROM `leasing_escenarios`");
		return $rs;
	}
	function getPlazo(){ return $this->mPlazo; }
}
class cLeasingActivosSegmentos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "vehiculos_segmento";
	private $mTipo		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cVehiculos_segmento();//Tabla
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave	= $data[$xT->IDVEHICULOS_SEGMENTO];
			$this->mNombre	= $data[$xT->NOMBRE_SEGMENTO];
			
			$this->mObj		= $xT;
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	
}
class cLeasingActivosMarcas {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "vehiculos_marcas";
	private $mTipo		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cVehiculos_marcas();//Tabla
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave	= $data[$xT->getKey()];
			$this->mNombre	= $data[$xT->NOMBRE_MARCA];
			
			$this->mObj		= $xT;
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	
}


class cLeasingActivosUsos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "vehiculos_usos";
	private $mTipo		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cVehiculos_usos();//Tabla
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave	= $data[$xT->getKey()];
			$this->mNombre	= $data[$xT->DESCRIPCION_USO];
			
			$this->mObj		= $xT;
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre; }
	function getClave(){return $this->mClave; }
	function getTipo(){ return $this->mTipo; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	
}

class cCreditosRechazosRazones {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "creditos_rechazos_tipo";
	private $mTipo		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cCreditos_rechazos_tipo();//Tabla
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave	= $data[$xT->IDCREDITOS_RECHAZOS_TIPO];
			$this->mNombre	= $data[$xT->DESCRIPCION];
			//$this->mTipo	= $data[$xT->];
			$this->mObj		= $xT;
			
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre; }
	function getClave(){return $this->mClave; }
	function getTipo(){ return $this->mTipo; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	
}
class cCreditosRechazos {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "";
	private $mTipo			= 0;
	private $mFecha			= false;
	private $mTiempo		= 0;
	private $mUsuario		= 0;
	private $mObservaciones	= "";
	private $mTexto			= "";
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cCreditos_rechazados();
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave	= $data[$xT->getKey()];
			$this->mTipo	= $data[$xT->CLAVERECHAZO];
			$this->mFecha	= $data[$xT->FECHA_DE_RECHAZO];
			$this->mUsuario	= $data[$xT->IDUSUARIO];
			$this->mTexto	= $data[$xT->RAZONES];
			
			$this->mObj		= $xT;
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre; }
	function getClave(){return $this->mClave; }
	function getTipo(){ return $this->mTipo; }
	function getNota(){ return $this->mTexto; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function initByCredito($credito = false){
		$xQL	= new MQL();
		$credito	= setNoMenorQueCero($credito);
		$data	= $xQL->getDataRow("SELECT * FROM `creditos_rechazados` WHERE `estatusactivo`=1 AND `numero_de_credito`=$credito ORDER BY `tiempo` DESC LIMIT 0,1");
		return $this->init($data);
	}
	function getUsuario(){ return $this->mUsuario; }
	function getFecha(){ return $this->mFecha; }
	function getRazones(){ return $this->mTexto; }
}
?>