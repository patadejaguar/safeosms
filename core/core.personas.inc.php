<?php
include_once("core.config.inc.php");
include_once("entidad.datos.php");
include_once("core.db.dic.php");
include_once("core.db.inc.php");

include_once("core.deprecated.inc.php");
include_once("core.fechas.inc.php");
include_once("core.html.inc.php");
include_once("core.region.inc.php");

include_once("core.common.inc.php");
include_once("core.personas.inc.php");

include_once("core.contable.inc.php");
include_once "core.contable.utils.inc.php";


include_once("core.creditos.inc.php");
include_once("core.creditos.utils.inc.php");
include_once("core.operaciones.inc.php");

class cPersonasRiesgosDeCredito {
	private $mPersona	= 0;
	private $mOEstats	= null;
	private $mDeudasPorFamilia	= 0;
	private $mDeudasPorAval		= 0;
	private $mCapacidadDePago	= 0;
	function __construct($persona){
		$this->mPersona	= setNoMenorQueCero($persona);
	}

	function getEndeudamientoPorPatrimonio($explain = false){
		$D		= $this->getOEstats()->getDatosAvalesOtorgados();
		$monto	= setNoMenorQueCero($D[SYS_MONTO]);
		$cnt	= $monto;
		if ( $explain == true ){
			$xT		= new cHTabla();
			$xL		= new cLang();
			$xT->setTitle($xL->getT("TR.Riesgo como Aval"));
			$xT->initRow();
			$xT->addTH($xL->getT("TR.Numero de Personas"));
			$xT->addTH($xL->getT("TR.Numero de Creditos"));
			$xT->addTH($xL->getT("TR.Monto Avalado"));
			$xT->addTH($xL->getT("TR.Monto Diario"));
			$xT->endRow();
				
			$xT->initRow();
			$xT->addTD($D["relaciones"] );
				
			$xT->addTD( $D[SYS_NUMERO] );
				
			$diario	= getFMoney($D["diario"]);
			$xCant	= new cCantidad($D[SYS_MONTO]);
				
			$xT->addTD($xCant->moneda());
			$xT->addTD($diario);
			$xT->endRow();
			$cnt	= $xT->get();
		}
		return $cnt;		
	}
	function getEndeudamientoPorAvalesOtorgados($explain = false){
		$D		= $this->getOEstats()->getDatosAvalesOtorgados();
		$monto	= setNoMenorQueCero($D[SYS_MONTO]);
		$cnt	= $monto;
		if ( $explain == true ){
			$xT		= new cHTabla();
			$xL		= new cLang();
			$xT->setTitle($xL->getT("TR.Riesgo como Aval"));
			$xT->initRow();
			$xT->addTH($xL->getT("TR.Numero de Personas"));
			$xT->addTH($xL->getT("TR.Numero de Creditos"));
			$xT->addTH($xL->getT("TR.Monto Avalado"));
			$xT->addTH($xL->getT("TR.Monto Diario"));
			$xT->endRow();
			
			$xT->initRow();
			$xT->addTD($D["relaciones"] );
			
			$xT->addTD( $D[SYS_NUMERO] );
			
			$diario	= getFMoney($D["diario"]);
			$xCant	= new cCantidad($D[SYS_MONTO]);
			
			$xT->addTD($xCant->moneda());
			$xT->addTD($diario);
			$xT->endRow();
			$cnt	= $xT->get();
		}
		return $cnt;					
	}
	function getEndeudamientoPorFamilia($explain = false){
		$D		= $this->getOEstats()->getDatosDependientesEconomicos();
		$monto	= setNoMenorQueCero($D[SYS_MONTO]);
		$cnt	= $monto;
		if ( $explain == true ){
			$xT		= new cHTabla();
			$xL		= new cLang();
			$xT->setTitle($xL->getT("TR.Riesgo Por Familia"));
			$xT->initRow();
			$xT->addTH($xL->getT("TR.Numero de Personas"));
			$xT->addTH($xL->getT("TR.Numero de Creditos"));
			$xT->addTH($xL->getT("TR.Monto por NUCLEO_DE_RIESGO"));
			$xT->addTH($xL->getT("TR.Monto Diario"));
			$xT->endRow();
				
			$xT->initRow();
			$xT->addTD($D["relaciones"] );
				
			$xT->addTD( $D[SYS_NUMERO] );
				
			$diario	= getFMoney($D["diario"]);
			$xCant	= new cCantidad($D[SYS_MONTO]);
				
			$xT->addTD($xCant->moneda());
			$xT->addTD($diario);
			$xT->endRow();
			$cnt	= $xT->get();
		}
		return $cnt;
	}	
	function getBalancePatrimonial($explain = false){
		$D		= $this->getOEstats()->getDatosPatrimonioActivo();
		$monto	= $D[SYS_MONTO];
		$cnt	= $monto;
		if ( $explain == true ){
			$xT		= new cHTabla();
			$xL		= new cLang();
			$xT->setTitle($xL->getT("TR.Patrimonio"));
			$xT->initRow();
			$xT->addTH($xL->getT("TR.Activo"));
			$xT->addTH($xL->getT("TR.Pasivo"));
			$xT->addTH($xL->getT("TR.Capital"));

			$xT->endRow();
		
			$xT->initRow();
		
			$xT->addTD(getFMoney($D["activo"]));
			$xT->addTD(getFMoney($D["pasivo"]));
			$xT->addTD(getFMoney($D[SYS_MONTO]));
			$xT->endRow();
			$cnt	= $xT->get();
		}
		return $cnt;		
	}
	function getIngresosEgresos($explain = false){
		$D		= $this->getOEstats()->getDatosPatrimonioActivo();
		$monto	= $D[SYS_MONTO];
		$cnt	= $monto;
		if ( $explain == true ){
			$xT		= new cHTabla();
			$xL		= new cLang();
			$xT->setTitle($xL->getT("TR.Patrimonio"));
			$xT->initRow();
			$xT->addTH($xL->getT("TR.Activo"));
			$xT->addTH($xL->getT("TR.Pasivo"));
			$xT->addTH($xL->getT("TR.Capital"));
	
			$xT->endRow();
	
			$xT->initRow();
	
			$xT->addTD(getFMoney($D["activo"]));
			$xT->addTD(getFMoney($D["pasivo"]));
			$xT->addTD(getFMoney($D[SYS_MONTO]));
			$xT->endRow();
			$cnt	= $xT->get();
		}
		return $cnt;
	}	
	function getCapacidadDePago(){
		
	}
	private function getOEstats(){
		if($this->mOEstats == null){
			$this->mOEstats	= new cPersonasEstadisticas($this->mPersona);
		}
		return $this->mOEstats;
	}
}

class cPersonasFlujoDeEfectivo {
	private $mPersona	= 0;
	function __construct($persona = false){
		$this->mPersona	= setNoMenorQueCero($persona);
	}
	function add($monto, $concepto, $periocidad, $descripcion, $observaciones = "", $fecha = false, $credito = false){
		$success	= false;
		$xCF		= new cCreditos_flujoefvo();
		$xT			= new cPersonasFlujoDeEfectivoTipos($concepto);
		$xF			= new cFecha();
		$fecha		= $xF->getFechaISO($fecha);
		$credito	= setNoMenorQueCero($credito);
		$credito	= ($credito <= DEFAULT_CREDITO) ? DEFAULT_CREDITO : $credito;
		$xT->init();
		$afectacion_neta	= round((($monto / $periocidad) * $xT->getAfectacion()),2);
		
		$xCF->afectacion_neta($afectacion_neta);
		$xCF->descripcion_completa($descripcion);
		$xCF->fecha_captura($fecha);
		
		$xCF->idusuario(getUsuarioActual());
		$xCF->monto_flujo($monto);
		$xCF->observacion_flujo($observaciones);
		$xCF->origen_flujo($concepto);
		$xCF->periocidad_flujo($periocidad);
		$xCF->socio_flujo($this->mPersona);
		$xCF->solicitud_flujo($credito);
		$xCF->sucursal(getSucursal());
		$xCF->tipo_flujo($xT->getClase());
		
		$xCF->idcreditos_flujoefvo( $xCF->query()->getLastID() );
		$success	= $xCF->query()->insert()->save();
		return ($success == false) ? false : true;
	}	
}
class cPersonasFlujoDeEfectivoTipos {
	private $mTipo	= 0;
	private $mObj	= null;
	private $mIsInit= false;
	private $mClase	= 1;
	function __construct($tipo = false){
		$this->mTipo	= setNoMenorQueCero($tipo);
		if($this->mTipo > 0){}
	}
	function init($datos = false){
		$this->mObj	= new cCreditos_origenflujo();
		$xQL		= new MQL();
		$datos		= (is_array($datos)) ? $datos : $xQL->getDataRow("SELECT * FROM `creditos_origenflujo` WHERE `idcreditos_origenflujo`=". $this->mTipo);
		if(isset($datos["idcreditos_origenflujo"])){
			$this->mObj->setData($datos);
			$this->mIsInit	= true;
		}
		return $this->mIsInit;
	}
	function getObj(){if($this->mObj == null){ $this->init(); } return $this->mObj; }
	function getAfectacion(){ return $this->getObj()->afectacion()->v(); }
	function getClase(){ return $this->getObj()->tipo()->v(); }
}


class cPersonasMemos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mTxt		= "";
	private $mMessages	= "";
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `socios_memo` WHERE `idsocios_memo`=". $this->mClave);
		if(isset($data["idsocios_memo"])){
			$this->mObj		= new cSocios_memo(); //Cambiar
			$this->mObj->setData($data);
			$this->mTxt		= $this->mObj->texto_memo()->v();
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
	function getClave(){return $this->mClave;}
	function add(){}
	function getTexto(){ return $this->mTxt; }
	function setArchivar(){
		$xQL	= new MQL();
		$xQL->setRawQuery("UPDATE `socios_memo` SET `archivado`=1 WHERE `idsocios_memo`=". $this->mClave);
	}
}

class cPersonasMembresiasTipos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mTipoMembresia	= false;
	private $mDiaPago		= 0;
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `personas_membresia_tipo` WHERE `idpersonas_membresia_tipo`=". $this->mClave);
		if(isset($data["idpersonas_membresia_tipo"])){
			$this->mObj		= new cPersonas_membresia_tipo(); //Cambiar
			$this->mObj->setData($data);
			$this->mNombre	= $this->mObj->descripcion_membresia_tipo()->v();
			//$this->mTipoMembresia = $this->mObj->
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
	function getListaDeCompromisos($periocidad = false, $periodo = false, $persona = false){
		$arr		= array();
		$xCache		= new cCache();
		
		$periodo	= setNoMenorQueCero($periodo);
		$periocidad	= setNoMenorQueCero($periocidad);
		$persona	= setNoMenorQueCero($persona);
		$tt			= ($persona > DEFAULT_SOCIO) ? "personas_pagos_perfil" :  "entidad_pagos_perfil";
		$tm			= ($persona > DEFAULT_SOCIO) ? " (`clave_de_persona`=$persona) " :  " (`tipo_de_membresia`=".$this->mClave . ") ";
		
		$idc		= "$tt-$persona-$periodo-$periocidad";
		$arr		= $xCache->get($idc);
		if(!is_array($arr)){
			$xQL		= new MQL();
			$ByPer1		= "";
			if($periocidad > 0){
				if($periodo >0){
					$ByPer1	= " AND ((`periocidad`= $periocidad) OR (`rotacion`='$periocidad-$periodo')) ";
				} else {
					$ByPer1	= " AND (`periocidad`= $periocidad) ";
				}
			}
			$rs			= $xQL->getDataRecord("SELECT * FROM `$tt` WHERE $tm  $ByPer1");
			foreach ($rs as $rw){
				$arr[$rw["tipo_de_operacion"]][SYS_TIPO]	= $rw["tipo_de_operacion"];
				$arr[$rw["tipo_de_operacion"]][SYS_MONTO]	= $rw["monto"];
			}
			$rs		= null;
		}
		return $arr;
	}
	function addPerfil($operacion, $monto, $periocidad = false, $fecha = false, $rotacion = "", $membresia = false){
		$xF			= new cFecha();
		$xQL		= new MQL();
		$fecha		= $xF->getFechaISO($fecha);
		$periocidad	= setNoMenorQueCero($periocidad);
		$periocidad	= ($periocidad <= 0) ? CREDITO_TIPO_PERIOCIDAD_MENSUAL : $periocidad;
		$membresia	= setNoMenorQueCero($membresia);
		$operacion	= setNoMenorQueCero($operacion);
		$membresia	= setNoMenorQueCero($membresia);
		$membresia	= ($membresia <= 0) ? $this->mClave : $membresia;
		//Eliminar perfil anterior
		$xQL->setRawQuery("DELETE FROM `entidad_pagos_perfil` WHERE `tipo_de_membresia`=$membresia AND `tipo_de_operacion`=$operacion");
		
		$xT			= new cEntidad_pagos_perfil();
		$id			= $xT->query()->getLastID();
		$xT->identidad_pagos_perfil( $id );
		$xT->monto($monto);
		$xT->periocidad($periocidad);
		$xT->tipo_de_membresia($membresia);
		$xT->tipo_de_operacion($operacion);
		$xT->fecha_de_aplicacion($fecha);
		$xT->rotacion($rotacion);
		//Aplicar a todo lo demas
		$mes		= $xF->mes($fecha);
		$ejercicio	= $xF->anno($fecha);
		$xQL->setRawQuery("UPDATE `personas_pagos_plan` SET `monto`=$monto WHERE `tipo_de_membresia`=$membresia AND `periodo`>=$mes AND `ejercicio`>=$ejercicio");
		$res		= $xT->query()->insert()->save();
	}
}

class cPersonasChecklist {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mDataArray	= array();
	private $mPersona	= 0;
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `personas_checklist` WHERE `idpersonas_checklist`=". $this->mClave);
		if(isset($data["idpersonas_checklist"])){
			$this->mObj		= new cPersonas_checklist(); //Cambiar
			$this->mObj->setData($data);
			$this->mClave	= $this->mObj->idpersonas_checklist()->v();
			$this->mPersona	= $this->mObj->clave_de_persona()->v();
			$this->mDataArray	= $data;
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function initByPersona($persona){
		$persona	= setNoMenorQueCero($persona);
		$xQL	= new MQL();
		$data 	= $xQL->getDataRow("SELECT * FROM `personas_checklist` WHERE `clave_de_persona`=". $persona. " LIMIT 0,1");
		return $this->init($data);
	}
	function get($campo){ return (isset($this->mDataArray[$campo])) ? $this->mDataArray[$campo] : false; }
	function set($campo, $valor){
		$valor	= setNoMenorQueCero($valor);
		$xQL	= new MQL();
		$res 	= $xQL->setRawQuery("UPDATE `personas_checklist` SET `$campo`=$valor WHERE `idpersonas_checklist`=" . $this->mClave);
		return ($res === false) ? false : true;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add($persona, $fecha = false){
		$xF			= new cFecha();		
		$persona	= setNoMenorQueCero($persona);
		$fecha		= $xF->getFechaISO($fecha);
		$res		= false;
		if($persona > DEFAULT_SOCIO){
			$mObj		= new cPersonas_checklist();
			$mObj->clave_de_persona($persona);
			$mObj->fecha_de_checklist($fecha);
			$id		= $mObj->query()->getLastID();
			$mObj->idpersonas_checklist($id);
			$res	= $mObj->query()->insert()->save();
			if($res !== false){
				$this->mClave	= $id;
			}
			$mObj	= null;
		}
		return ($res == false) ? false : true;
	}
	function setActualizarPorDocumentos($persona){
		$arrFlds	= $this->getListOfOptions();
		$supd		= "";
		foreach ($arrFlds as $fld){
			$xCamp	= new MQLCampo($fld);
			$exs	= $this->getExisteDocVig($xCamp->get());
			
			if($exs == false AND $xCamp->v() == SYS_UNO ){
				$supd	.= ($supd == "") ? $xCamp->get() . "=0" : "," . $xCamp->get() . "=0";
			} else if($exs == true AND $xCamp->v() == SYS_CERO ){
				$supd	.= ($supd == "") ? $xCamp->get() . "=1" : "," . $xCamp->get() . "=1";
			}
		}
		if($supd !== ""){
			$xQL	= new MQL();
			$xQL->setRawQuery("UPDATE `personas_checklist` SET $supd WHERE `clave_de_persona`=$persona");
			$this->initByPersona($persona);
		}
	}
	function getListOfOptions(){
		$arrFlds	= $this->getObj()->query()->getCampos(); unset($arrFlds["idpersonas_checklist"]);unset($arrFlds["clave_de_persona"]);unset($arrFlds["fecha_de_checklist"]);//unset($arrFlds[""]);
		return $arrFlds;
	}
	private function getExisteDocVig($campo){
		$persona	= $this->mPersona;
		$sql	= "SELECT   COUNT(*) AS `items`	FROM     `personas_documentacion`
		INNER JOIN `personas_documentacion_tipos`  ON `personas_documentacion`.`tipo_de_documento` = `personas_documentacion_tipos`.`clave_de_control`
		WHERE    ( `personas_documentacion`.`clave_de_persona` = $persona ) AND ( `personas_documentacion`.`estado_en_sistema` = 1  ) AND ( `personas_documentacion_tipos`.`checklist` ='$campo' )";
//		setLog($sql);
		$xQL	= new MQL();
		$rr		= $xQL->getDataRow($sql);
		$items	= setNoMenorQueCero($rr["items"]);
		return ($items>0) ? true : false;
	}
	
}


class cDomicilioMunicipio {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mEstado	= "";
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `general_municipios` WHERE `idcreditos_destino_detallado`=". $this->mClave);
		if(isset($data["idgeneral_municipios"])){
			$this->mObj		= new cGeneral_municipios();
			$this->mObj->setData($data);
			$this->mClave	= $this->mObj->idgeneral_municipios()->v();
			$this->mNombre	= $this->mObj->nombre_del_municipio()->v();
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
	function initByEstadoBusqueda($busqueda = "", $estado = false){
	
	}
	function initByNumeroEntidad($numero, $entidad){
		$xQL	= new MQL();
		$dd		= $xQL->getDataRow("SELECT * FROM `general_municipios` WHERE `clave_de_municipio`=$numero AND `clave_de_entidad`=$entidad LIMIT 0,1");
		if(isset($dd["idgeneral_municipios"])){
			$this->mClave	= $dd["idgeneral_municipios"];
		}
		return $this->init($dd);
	}	
}


class cDomicilioLocalidad {
	private $mClave		= 0;
	private $mDatos		= array();
	private $mInit			= false;
	private $mClaveDePais	= null;
	private $mClaveDeEstado	= null;
	private $mNombre		= "";
	function __construct($clave){
		$this->mClave		= $clave;
		$this->mClaveDePais	= EACP_CLAVE_DE_PAIS;
	}
	function set($clave){ $this->mClave	= $clave;	}
	function init($arr = false){
		//clave_unica, nombre_de_la_localidad, clave_de_estado, clave_de_municipio, clave_de_localidad, longitud, altitud, latitud
		$sql	= "SELECT * FROM catalogos_localidades WHERE clave_unica=" . $this->mClave . " LIMIT 0,1";
		if(is_array($arr)){
			$this->mDatos			= $arr;
		} else {
			$this->mDatos			= obten_filas($sql);
		}

		if(isset($this->mDatos["clave_unica"])){
			$this->mClaveDePais		= $this->mDatos["clave_de_pais"];
			$this->mClaveDeEstado	= $this->mDatos["clave_de_estado"];
			$this->mNombre			= $this->mDatos["nombre_de_la_localidad"];
			$this->mInit			= true;
		}
		return $this->mInit;
	}
	function getNombre(){ return $this->mNombre;	}
	function getDatosInArray(){ return $this->mDatos; }
	function existe($localidad = false){
		$localidad	= ($localidad == false) ? $this->mClave : $localidad;
		$sql		= "SELECT * FROM `catalogos_localidades` WHERE clave_unica=$localidad";
		$datos			= obten_filas($sql);
		$existe			= false;
		if(isset($datos["clave_unica"])){
			$this->init($datos);
			$existe		= true;
		}
		return $existe;
	}
	function setBuscar($nombre = "", $estado = 0, $municipio = 0, $pais = EACP_CLAVE_DE_PAIS ){
		$ByEstado		= ( setNoMenorQueCero($estado) >0 ) ? " AND clave_de_estado = $estado" : "";
		$ByMunicipio	= ( setNoMenorQueCero($estado) >0 ) ? " AND clave_de_municipio = $municipio " : "";
		$ByNom			= "";
		$ByPais			= "";
		$xLoc			= new cLocal();

		if($nombre != ""){
			$nombre			= substr( trim($nombre), 0,5);
			$ByNom			= " AND nombre_de_la_localidad LIKE '%$nombre%' ";
		}
		if($pais != EACP_CLAVE_DE_PAIS){
			$ByEstado		= "";
			$ByMunicipio	= "";
				
			$ByPais			= " AND (`catalogos_localidades`.`clave_de_pais` ='$pais')  ";
		}
		$sqlBLoca		= "SELECT * FROM catalogos_localidades WHERE clave_unica > 0 $ByNom $ByEstado $ByMunicipio $ByPais LIMIT 0,1";
		$datos			= obten_filas($sqlBLoca);
		if(!isset($datos["clave_unica"])){
			$sqlBLoca		= "SELECT * FROM catalogos_localidades WHERE clave_unica > 0 $ByNom $ByEstado $ByPais LIMIT 0,1";
			$datos			= obten_filas($sqlBLoca);
			if(!isset($datos["clave_unica"])){
				$sqlBLoca		= "SELECT * FROM catalogos_localidades WHERE clave_unica > 0 $ByEstado $ByPais LIMIT 0,1";
				$datos			= obten_filas($sqlBLoca);
			}
		}
		$clave			= false;
		if(isset($datos["clave_unica"])){
			$this->set($datos["clave_unica"]);
			$this->init($datos);
			$clave		= $datos["clave_unica"];
		}
		return $clave;
	}
	function getClaveUnica(){ return $this->mClave; }
	function getClaveDePais(){ return $this->mClaveDePais; }
	function getClaveDeEstado(){ return $this->mClaveDeEstado; }
}
class cDomiciliosColonias {
	private $mCodigo			= 99;
	private $mCPostal			= 0;
	private $mDatos				= array();
	private $aDatosMunicipio	= array();
	private $aDatosEstado		= array();
	private $mNombre			= "";
	private $mClaveMunicipio	= 0;
	private $mClaveEstado		= 0;
	private $mClaveEstadoABC	= "";
	private $mClaveEstadoSIC	= "";
	private $mClaveLocalidad	= 0;
	private $mClaveDePais		= null;
	private $mNombreMunicipio	= "";
	private $mNombreEstado		= "";
	private $mNombreLocalidad	= "";
	private $mNombreCiudad		= "";

	private $mTipoAsentamiento	= "";
	private $mMessages			= "";
	private $mInit				= false;
	private $mObjColonia		= null;
	private $mObjEstado			= null;
	function __construct($clave = 99){
		$this->mCodigo		= setNoMenorQueCero($clave);
		$this->mClaveDePais	= EACP_CLAVE_DE_PAIS;
	}
	function getClavePorNombre($nombre){
		$nombre			= strtoupper($nombre);
		$xDT			= new cSQLTabla(TCATALOGOS_COLONIAS);
		$xDT->setWhere( " UCASE(nombre_colonia) LIKE '$nombre%' " );
		$D				= obten_filas( $xDT->getSelect(1) );
		$this->mCodigo	= $D[ $xDT->getClaveUnica() ];
		//$this->mMessages	.= $xDT->getSelect(1);
		return $this->mCodigo;
	}
	function getClavePorCodigoPostal($CodigoPostal ){
		$xViv			= new cGeneral_colonias();
		$sql			= "SELECT * FROM general_colonias WHERE codigo_postal=$CodigoPostal ORDER BY fecha_de_revision DESC LIMIT 0,1 ";
		$datos			= obten_filas($sql);
		$xViv->setData($datos );
		$this->mDatos	= $datos;
		$this->mCodigo	= $xViv->idgeneral_colonia()->v();
		$this->init($datos);
		$this->mObjColonia	= $xViv;
		$this->mInit	= true;
		return $this->mCodigo;
	}
	function getDatosInArray(){
		if($this->mInit == false){ $this->init();}
		return $this->mDatos;
	}
	function init($aDatos	= false){
		if($aDatos == false){
			$xDT						= new cSQLTabla(TCATALOGOS_COLONIAS);
			$xDT->setWhere( $xDT->getCondicionPorClave($this->mCodigo) );
			$this->mDatos				= obten_filas( $xDT->getSelect(1) );
			if(!isset($this->mDatos["codigo_postal"])){
				$this->mInit	= false;
			}
		} else {
			$this->mDatos	= $aDatos;
		}
		if(!isset($this->mDatos["codigo_postal"])){
			$this->mInit	= false;
		} else {
			$this->mCPostal					= $this->mDatos["codigo_postal"];
			$this->mClaveEstado				= $this->mDatos["codigo_de_estado"];
			$this->mClaveMunicipio			= $this->mDatos["codigo_de_municipio"];
			$this->mNombreEstado			= $this->mDatos["estado_colonia"];
			$this->mNombreMunicipio			= $this->mDatos["municipio_colonia"];
			$this->mNombre					= $this->mDatos["nombre_colonia"];
			$this->mTipoAsentamiento		= $this->mDatos["tipo_colonia"];
			if($this->isCiudad() == false){
				$this->mNombreLocalidad		= $this->mDatos["nombre_colonia"];	//si no es ciudad, es nombre de la colonia
			} else {
				$this->mNombreLocalidad		= $this->mDatos["ciudad_colonia"];
				$this->mNombreCiudad		= $this->mDatos["ciudad_colonia"];
			}
			$this->mInit					= true;
		}
		return $this->mInit;
	}

	function getDatosMunicipioInArray(){
		$municipio				= $this->mClaveMunicipio;
		$estado					= $this->mClaveEstado;
		$xDT					= new cSQLTabla(TCATALOGOS_MUNICIPIOS);
		$xDT->setWhere("clave_de_municipio=$municipio AND clave_de_entidad=$estado");
		$this->aDatosMunicipio	= obten_filas( $xDT->getSelect(1) );
		return $this->aDatosMunicipio;
	}
	function getDatosEstadoInArray(){
		$municipio				= $this->mClaveMunicipio;
		$estado					= $this->mClaveEstado;
		$xDT					= new cSQLTabla(TCATALOGOS_ESTADOS);
		$xDT->setWhere("clave_numerica=$estado");
		$this->aDatosEstado		= obten_filas( $xDT->getSelect(1) );
		return $this->aDatosEstado;
	}

	function existe($codigo_postal, $id = "", $nombre = "", $Inicializar = false){
		$ByCP		= (setNoMenorQueCero($codigo_postal) <= 0) ? "" : " AND (`general_colonias`.`codigo_postal` = $codigo_postal) ";
		$ById		= (setNoMenorQueCero($id) <= 0) ? "" : " AND idgeneral_colonia = $id ";
		$ByNombre	= ($nombre == "" ) ? "": " AND UCASE(nombre_colonia) LIKE '$nombre%' ";
		$existe		= false;
		$xCache		= new cCache();
		$idc		= setCadenaVal("$codigo_postal-$id-$nombre");
		$D			= $xCache->get($idc);
		if(!is_array($D)){		
			$sql = "SELECT		`general_colonias`.*,
			`general_estados`.`clave_alfanumerica`,
			`general_estados`.`clave_numerica`,
			`general_estados`.`clave_en_sic`,
			`general_colonias`.`codigo_postal`
			FROM
			`general_colonias` `general_colonias`
			INNER JOIN `general_estados` `general_estados`
			ON `general_colonias`.`codigo_de_estado` = `general_estados`.
			`clave_numerica`
			WHERE codigo_postal > 0 $ByCP $ById $ByNombre LIMIT 0,1";
			$existen	= false;
			$D			= obten_filas($sql);
		}
		//setLog($sql);
		if( isset($D["idgeneral_colonia"]) ){
			$this->init($D);
			$this->mClaveEstadoABC	= strtoupper($D["clave_alfanumerica"] );
			$this->mClaveEstadoSIC	= strtoupper($D["clave_en_sic"] );
			$existe					= true;
			$xCache->set($idc, $D);
		}
		return $existe;
	}
	function getOEstado(){
		if($this->mObjEstado == null){
			$this->mObjEstado				= new cGeneral_estados();
			$DEstado						= obten_filas("SELECT * FROM general_estados WHERE clave_numerica= " . $this->mClaveEstado . " LIMIT 0,1");
			$this->mObjEstado->setData($DEstado);
		}
		return $this->mObjEstado;
	}
	function getOColonia(){ return $this->mObjColonia;	}
	function getTipoDeAsentamiento(){ 	return $this->mTipoAsentamiento;	}
	function getClaveDeEstado(){ if($this->mInit == false){ $this->init(); }	return $this->mClaveEstado; 	}
	function getClaveDePais(){ if($this->mInit == false){ $this->init(); }	return $this->mClaveDePais; 	}
	function getClaveDeMunicipio(){
		if($this->mInit == false){ $this->init(); }
		return $this->mClaveMunicipio;
	}
	function getClaveDeEstadoABC(){
		if($this->mClaveEstadoABC == ""){
			if($this->mObjEstado == null){ $this->getOEstado(); }
			$this->mClaveEstadoABC	= strtoupper($this->mObjEstado->clave_alfanumerica()->v());
		}
		return $this->mClaveEstadoABC;
	}
	function getClaveEstadoEnSIC(){
		if($this->mClaveEstadoSIC == ""){
			if($this->mObjEstado == null){ $this->getOEstado(); }
			$this->mClaveEstadoSIC	= strtoupper($this->mObjEstado->clave_en_sic()->v());
		}
		return $this->mClaveEstadoSIC;
	}
	function getNombre(){	return $this->mNombre;	}
	function getCodigoPostal(){	return $this->mCPostal;	}
	function getNombreEstado(){	return $this->mNombreEstado;	}
	function getNombreMunicipio(){	return $this->mNombreMunicipio;	}
	function getNombreLocalidad(){	return $this->mNombreLocalidad;	}
	function getNombreCiudad(){ return $this->mNombreCiudad; }
	function getMessages($put = OUT_TXT){ $xH		= new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function set($mCodigo = false){
		if( $mCodigo != false ){
			$this->mCodigo		= $mCodigo;
			$this->mMessages	.= "INICIAR\tColonia Iniciada con el Codigo Interno $mCodigo\r\n";
			$this->init();
		}
	}
	function get(){ return $this->mCodigo; }
	function getClaveDeLocalidad(){
		if($this->mClaveLocalidad == 0){
			$xDomLoc	= new cDomicilioLocalidad(false);
			$rs			= $xDomLoc->setBuscar($this->mNombreLocalidad, $this->mClaveEstado, $this->mClaveMunicipio);
			if($rs == false){
				//devolver clave local
				$xLoc	= new cLocal();
				$this->mClaveLocalidad = $xLoc->DomicilioLocalidadClave();
			} else {
				$this->mClaveLocalidad 	= $xDomLoc->getClaveUnica();
				$this->mClaveDePais		= $xDomLoc->getClaveDePais();
			}
		}
		return $this->mClaveLocalidad;
	}
	function isCiudad(){
		$es		= true;
		$es		= (preg_match("/EJIDO/", strtoupper($this->mTipoAsentamiento)) == true) ? false : $es;
		$es		= (preg_match("/POBLADO/", strtoupper($this->mTipoAsentamiento)) == true) ? false : $es;
		$es		= (preg_match("/PUEBLO/", strtoupper($this->mTipoAsentamiento)) == true) ? false : $es;
		$es		= (preg_match("/RANCH/", strtoupper($this->mTipoAsentamiento)) == true) ? false : $es;
		return $es;
	}
	function add($nombre, $codigo_postal, $tipo = "colonia", $ciudad = "", $identidad = false, $idmunicipio = false, $nombremunicipio = ""){
		$identidad	= setNoMenorQueCero($identidad);
		$idmunicipio= setNoMenorQueCero($idmunicipio);
		
		$xCol		= new cGeneral_colonias();
		$xL			= new cLocal();
		$xCol->idgeneral_colonia( $xCol->query()->getLastID() );
		$xCol->ciudad_colonia($ciudad);
		$xCol->codigo_postal($codigo_postal);
		
		
		$xCol->codigo_de_estado($identidad);
		$xCol->codigo_de_municipio($idmunicipio);
		
		$xE		= new cDomiciliosEntidadFederal($identidad);
		$xE->init();
		$entidadfed	= setCadenaVal($xE->getNombre());
		
		$xM		= new cDomicilioMunicipio();
		$xM->initByNumeroEntidad($idmunicipio, $identidad);
		$nombremunicipio	= ($nombremunicipio == "") ? $xM->getNombre() : $nombremunicipio;
		$nombremunicipio	= setCadenaVal($nombremunicipio);
		
		$xCol->estado_colonia($entidadfed);
		$xCol->fecha_de_revision(fechasys());
		$xCol->municipio_colonia($nombremunicipio);
		$xCol->nombre_colonia($nombre);
		$xCol->sucursal(getSucursal());
		$xCol->tipo_colonia(strtolower($tipo));
		
		$is		= $xCol->query()->insert()->save();
		if($is !== false){
			$xLoc			= new cDomicilioLocalidad(false);
			$idlocalidad	= $xL->DomicilioLocalidadClave();
			if($xLoc->setBuscar("", $identidad, $idmunicipio) !== false){
				$idlocalidad	= $xLoc->getClaveUnica();
			}
			
			$xD		= new cTmp_colonias_activas();
			$xD->codigo_postal($codigo_postal);
			$xD->clave_alfanumerica($xE->getClaveAlfa());
			$xD->clave_en_sic($xE->getClaveSIC());
			$xD->codigo_de_estado($identidad);
			$xD->idlocalidad($idlocalidad);
			$xD->nombre($nombre);
			$xD->nombre_estado($entidadfed);
			$xD->nombre_municipio($nombremunicipio);
			$xD->numero(1);
			$xD->query()->insert()->save();
		}
	}
}

class cDomiciliosPaises {
	private $mClave		= "";
	private $mMoneda	= "";
	private $mData		= array();
	private $mObj		= null;
	private $mNombre	= "";
	private $mRiesgoAML	= 10;
	private $mInit		= false;
	private $mGentilicio	= "";

	function __construct($clave = ""){
		$this->mClave		= strtoupper($clave);
		$this->mMoneda		= AML_CLAVE_MONEDA_LOCAL;
		if($clave != ""){ $this->init(); }
	}
	function getPaisPorMoneda($moneda){
		$moneda				= strtoupper($moneda);
		$xMon				= new cMonedas($moneda);
		$this->mMoneda		= $moneda;
		$this->mClave		= $xMon->getPais();
		$this->init();
	}
	function init($data = false){
		$clave		= $this->mClave;
		$idcx		= "datos-pais-$clave";
		if(!is_array($data)){
			$xCache			= new cCache();
			$data			= $xCache->get($idcx);
			if(!is_array($data)){
				$ql				= new MQL();
				$data			= $ql->getDataRow("SELECT * FROM `personas_domicilios_paises` WHERE `clave_alfanumerica`='$clave' LIMIT 0,1");
				$xCache->set($idcx, $data);
			}
		}
		//var_dump($data);
		if(isset($data["clave_alfanumerica"])){
			$this->mObj			= new cPersonas_domicilios_paises();
			$this->mObj->setData($data);
			$this->mData		= $data;
			$this->mRiesgoAML 	= $this->mObj->es_considerado_riesgo()->v();
			$this->mNombre		= strtoupper($this->mObj->nombre_oficial()->v());
			$this->mGentilicio	= $this->mObj->gentilicio()->v();
			//$this->mMoneda		= strtoupper($this->mObj->
			//Iniciar moneda
			$xMon	= new cMonedas();
			if($xMon->initByPais($this->mClave) == true){
				$this->mMoneda	= $xMon->getClave();
			}
			$this->mInit		= true;
				
		}
		return $this->mInit;
	}
	function getNombre(){ return $this->mNombre; }
	function getRiesgoAMLAsociado(){ return $this->mRiesgoAML;	}
	function getMoneda(){ return $this->mMoneda;}
	function getGentilicio(){ return $this->mGentilicio; }
}

class cDomiciliosEntidadFederal {
	private $mClave			= "";
	private $mMoneda		= "";
	private $mData			= array();
	private $mObj			= null;
	private $mClaveSIC		= "";
	private $mClaveAlpha	= "";
	
	function __construct($clave = ""){
		$this->mClave	= setNoMenorQueCero($clave);
		$this->setIDCache($this->mClave);
		if( $this->mClave > 0){ $this->init(); }
	}
	function initByClaveAlfa($abc){
		$this->mClaveAlpha	= $abc;
		$xCache		= new cCache();
		$idx		= "general_estados-by-alf-". $this->mClaveAlpha;
		$data		= $xCache->get($idx);
		if(!is_array($data)){
			$sql		= "SELECT * FROM `general_estados` WHERE `clave_alfanumerica` = '" . strtoupper($abc) . "' LIMIT 0,1";		
			$ql			= new MQL();
			$data		= $ql->getDataRecord($sql);
			$xCache->set($idx, $data);
		}
		return $this->init($data);
	}
	function initByClaveSIC($abc){
		$this->mClaveSIC	= $abc;
		$xCache		= new cCache();
		$idx		= "general_estados-by-sic-". $this->mClaveSIC;
		$data		= $xCache->get($idx);
		if(!is_array($data)){
			$sql		= "SELECT * FROM `general_estados` WHERE `clave_en_sic` = '" . strtoupper($abc) . "' LIMIT 0,1";
			$ql			= new MQL();
			$data		= $ql->getDataRecord($sql);
			$xCache->set($idx, $data);
		}
		return $this->init($data);
	}
	function init($data = false){
		$clave		= $this->mClave;
		if(!is_array($data)){
			$ql			= new MQL();
			$data		= $ql->getDataRow("SELECT * FROM `general_estados` WHERE `clave_numerica` = $clave LIMIT 0,1");
		}
		$this->mObj		= new cGeneral_estados();
		$this->mObj->setData($data);
		$this->mData	= $data;
	}
	function getClaveNumerica(){ return $this->mObj->clave_numerica()->v(); }
	function getClaveAlfa(){ return strtoupper($this->mObj->clave_alfanumerica()->v()); }
	function getClaveSIC(){ return strtoupper($this->mObj->clave_en_sic()->v()); }
	function getNombre(){ return strtoupper($this->mObj->nombre()->v()); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "general_estados-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){ 
			$xCache = new cCache();
			$xCache->clean($this->mIDCache); 
			$xCache->clean("general_estados-by-sic-". $this->mClaveSIC);
			$xCache->clean("general_estados-by-alf-". $this->mClaveAlpha);
		} 
	}
}

class cPersonasTiposMemos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	public $NOTA_COBRANZA	= 12;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `socios_memotipos` WHERE `tipo_memo`=". $this->mClave);
		if(isset($data["tipo_memo"])){
			$this->mObj		= new cSocios_memotipos(); //Cambiar
			$this->mObj->setData($data);
			$this->mClave	= $this->mObj->tipo_memo()->v();
			$this->mNombre	= $this->mObj->descripcion_memo()->v();
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
}
class cPersonasProceso {
	public $REGISTRO	= "REGISTRO_DE_PERSONA";
	
	function __construct(){}
	
}
class cPersonasViviendaTipo {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= TPERSONAS_DIRECCIONES_TIPO . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cSocios_viviendatipo();//Tabla
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
			$this->mNombre	= $this->mObj->descripcion_viviendatipo()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
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

}
class cPersonasViviendaRegimen {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= TPERSONAS_DIRECCIONES_REG . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cSocios_regimenvivienda();//Tabla
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
			$this->mNombre	= $this->mObj->descipcion_regimenvivienda()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
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

}

class cEntidadPerfilDePagos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mTipoMembresia	= 0;
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "entidad_pagos_perfil-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){ 
			$xCache = new cCache();
			$xCache->clean($this->mIDCache); 
			$xCache->clean("entidad_pagos_perfil-por-membresia-". $this->mTipoMembresia);
		}
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cEntidad_pagos_perfil();
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
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function setTipoDeMembresia($membresia){ $this->mTipoMembresia; }
	
	function getListadoPorMembresia($membresia){
		$data					= array();
		if($membresia>0){
			$xCache				= new cCache();
			$idc				= "entidad_pagos_perfil-por-membresia-$membresia";
			$data				= $xCache->get($idc);
			$this->mTipoMembresia	= $membresia;
			if(!is_array($data)){
				$xQL	= new MQL();
				$rs		= $xQL->getDataRecord("SELECT * FROM `entidad_pagos_perfil` WHERE `tipo_de_membresia`=$membresia");
				foreach ($rs as $rw){
					$data[$rw["tipo_de_operacion"]]	= $rw;
				}
				$xCache->set($idc, $data, $xCache->EXPIRA_MEDHORA);
				$rs		= null;
			}
		}
		return $data;
	}
}
class cPersonasPerfilDePagos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mPersona	= 0;
	private $mTipoMembresia	= 0;
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "personas_pagos_perfil-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
			$xCache->clean("personas_pagos_perfil-por-persona-".$this->mPersona);
		} 
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cPersonas_pagos_perfil();
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
			$this->mClave			= $data[$xT->getKey()];
			$this->mPersona			= $xT->clave_de_persona()->v();
			$this->mTipoMembresia	= $xT->membresia()->v();
			
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
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add($operacion, $monto, $prioridad = 0, $frecuencia = 30, $rotacion = "", $fecha = false){
		$xTP	= new cPersonas_pagos_perfil();
		$xF		= new cFecha();
		$fecha	= ($fecha === false) ? $xF->getFechaFinAnnio() : $fecha;
		$xTP->idpersonas_pagos_perfil("NULL");
		$xTP->clave_de_persona($this->mPersona);
		$xTP->estatus(SYS_UNO);
		$xTP->fecha_de_aplicacion($fecha);
		$xTP->finalizador(SYS_CERO);
		$xTP->membresia($this->mTipoMembresia);
		$xTP->monto($monto);
		$xTP->periocidad($frecuencia);
		$xTP->prioridad($prioridad);
		$xTP->rotacion($rotacion);
		$xTP->tipo_de_operacion($operacion);
		$rs		= $xTP->query()->insert()->save();
		$xTP	= null;
		return ($rs === false) ? false : true;
	}
	function setPersona($persona){$this->mPersona = $persona; }
	function setCrearPorPersonaMembresia($persona, $membresia){
		if($persona>0 AND $persona>0){
			$this->mPersona			= $persona;
			$this->mTipoMembresia	= $membresia;
			
			$xMem	= new cEntidadPerfilDePagos();
			$rsE	= $xMem->getListadoPorMembresia($membresia);
			$rsP	= $this->getListadoPorPersona($persona);
			//Contar cuantas existen de la persona
			$xTE	= new cEntidad_pagos_perfil();
			$xTP	= new cPersonas_pagos_perfil();
			foreach ($rsP as $idx => $rw){
				$xTP->setData($rw);
				//Si existe el Registro en la configuracion de la entidad
				if(isset($rsE[$xTP->tipo_de_operacion()->v()])){
					$rwx	= $rsE[$xTP->tipo_de_operacion()->v()];	//obtiene el registro desde la entidad
					if($xTP->finalizador()->v() == 0){
						//Si no es Finalizador.- Actualizar al nuevo monto
						$xTE->setData($rwx);						//Asigna valores desde la entidad
						if(round($xTE->monto()->v(), 2) !== round($xTP->monto()->v(),2)){
						//Actualiza la tabla
						$xTP->monto($xTE->monto()->v());			//Heredar monto
						$xTP->fecha_de_aplicacion($xTE->fecha_de_aplicacion()->v());//Hererdar fecha
						$xTP->periocidad($xTE->periocidad()->v());
						$xTP->prioridad($xTE->prioridad()->v()); 	//Heredar prioridad
						$xTP->rotacion($xTE->rotacion()->v()); 		//Heredar rotacion
						$xTP->query()->update()->save($xTP->idpersonas_pagos_perfil()->v());
						
							//setError("Actualizar  a " . $xTE->monto()->v()  );
						} else {
							//setError("No se actualiza nada");
						}
					}
					//Quitar de la Entidad
					unset($rsE[$xTP->tipo_de_operacion()->v()]);
				}
			}
			foreach ($rsE as $idx => $rw){
				$xTE->setData($rw);
				//Insertar
				$this->add($xTE->tipo_de_operacion()->v(), $xTE->monto()->v(), $xTE->prioridad()->v(), $xTE->periocidad()->v(), $xTE->rotacion()->v(), $xTE->fecha_de_aplicacion()->v());
			}
			$this->setCuandoSeActualiza();
		}
	}
	function getListadoPorPersona($persona){
		$data					= array();
		if($this->mPersona>DEFAULT_SOCIO){
			$xCache			= new cCache();
			$idc			= "personas_pagos_perfil-por-persona-".$this->mPersona;
			$data			= $xCache->get($idc);
			$this->mPersona	= $persona;
			if(!is_array($data)){
				$xQL		= new MQL();
				$rs			= $xQL->getDataRecord("SELECT * FROM `personas_pagos_perfil` WHERE `estatus`=1 AND `clave_de_persona`=".$this->mPersona);
				foreach ($rs as $rw){
					$data[$rw["tipo_de_operacion"]]	= $rw;
				}
				$rs			= null;
				$xCache->set($idc, $data, $xCache->EXPIRA_MEDHORA);
			}
		}
		return $data;
	}
}

class cPersonasDocumentacion {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mIDPersona	= 0;
	private $mTipo		= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "personas_documentacion-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cPersonas_documentacion();//Tabla
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
			$this->mClave		= $xT->clave_de_control()->v();
			$this->mIDPersona	= $xT->clave_de_persona()->v();
			$this->mTipo		= $xT->tipo_de_documento()->v();
			
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
	function getClaveDePersona(){ return $this->mIDPersona; }
	function getTipoDocto(){ return $this->mTipo; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}

}
class cPersonasDocumentacionTipos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "personas_documentacion_tipos-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cPersonas_documentacion_tipos();//Tabla
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
			$this->mClave		= $xT->clave_de_control()->v();
			$this->mNombre		= $xT->nombre_del_documento()->v();
			
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
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function initByCheckList($txt){
		$xCache	= new cCache();
		$idx	= $this->mTable . "-by-chk-$txt";
		$data	= $xCache->get($idx);
		if(!is_array($data)){
			$xQL	= new MQL();
			$data	= $xQL->getDataRow("SELECT * FROM `personas_documentacion_tipos` WHERE `checklist`='$txt' LIMIT 0,1");
			if(isset($data["clave_de_control"])){
				$this->mClave	= $data["clave_de_control"];
				$xCache->set($idx, $data);
			}
		}
		return $this->init($data);
	}
}

class cPersonaActividadEconSector {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "socios_aeconomica_sector";
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
		$xT		= new cSocios_aeconomica_sector();//Tabla
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
			$this->mNombre	= $xT->descripcion_aeconomica_sector()->v();
			
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
class cPersonaActividadEconSCIAN {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "personas_ae_scian";
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
		$xT		= new cPersonas_ae_scian();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `clave_de_actividad`='". $this->mClave . "' LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->mNombre	= $xT->nombre_de_la_actividad()->v();
			$this->mTipo	= $xT->sector()->v();
			
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


class cPersonasMoralesDatosExt {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "personas_morales_anx";
	private $mTipo		= 0;
	private $mFechaConstitucion	= false;
	private $mNumeroRegistro	= "";
	private $mActaConstitucion	= "";
	private $mNumeroNotaria		= "";
	private $mNombreNotario		= "";
	
	
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
		$xT		= new cPersonas_morales_anx();//Tabla
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
			$this->mFechaConstitucion = $xT->fecha_de_constitucion()->v();
			$this->mNumeroNotaria		= $xT->clave_notaria()->v();
			$this->mNombreNotario		= $xT->nombre_notario()->v();
			$this->mNombre				= $xT->idregistro_publico()->v();
			
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
	function initByPersona($persona){
		$xQL	= new MQL();
		$data	= $xQL->getDataRow("SELECT * FROM  `personas_morales_anx` WHERE `persona`=$persona LIMIT 0,1");
		
		return $this->init($data);
	}
	function getFechaConstitucion(){ return $this->mFechaConstitucion; }
	function getActaCosntitucion(){ return $this->mActaConstitucion; }
	function getNumeroNotaria(){ return $this->mNumeroNotaria; }
	function getNombreNotario(){ return$this->mNombreNotario; }
}

?>