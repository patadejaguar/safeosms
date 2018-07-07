<?php
include_once ("core.config.inc.php");
include_once ("entidad.datos.php");
include_once ("core.db.inc.php");
include_once ("core.db.dic.php");
include_once ("core.error.inc.php");
include_once ("core.common.inc.php");
include_once ("core.sys.inc.php");
include_once ("core.fechas.inc.php");
include_once ("core.utils.inc.php");



class cGruposCotizaciones {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "originacion_grupos";
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
		$xT			= new cOriginacion_grupos();//Tabla
		
		
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
	function add($idgrupo, $nivel = false, $fecha = false){
		$xF		= new cFecha();
		$id		= 0;
		$xGpo	= new cGrupo($idgrupo);
		
		$nivel	= setNoMenorQueCero($nivel);
		
		if($xGpo->init() == true){
			$nivel	= ($nivel<=0) ? $xGpo->getProximoNivel() : $nivel;
			$xOrg	= new cOriginacion_grupos();
			
			$xOrg->idusuario(getUsuarioActual());
			$xOrg->estatusactivo(SYS_UNO);
			$xOrg->fecha_autorizacion($xF->getFechaISO($fecha));
			$xOrg->fecha_solicitud($xF->getFechaISO($fecha));
			$xOrg->grupo_id($xGpo->getCodigo());
			$xOrg->nivel_id($nivel);
			$xOrg->originacion_grupos_id('NULL');
			$xOrg->presidenta_id($this->getRepresentanteCodigo());
			$xOrg->suma_autorizado(0);
			$xOrg->suma_solicitado(0);
			$xOrg->tiempo(time());
			
			$qq		= $xOrg->query();
			$res	= $qq->insert()->save();
			$id		= $qq->getLastInsertID();
		}
		return setNoMenorQueCero($id);
	}
	
}

class cGruposCotizacionesDetalle {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "grupos_solicitud";
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
		$xT			= new cGrupos_solicitud();//Tabla
		
		
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
	function add($idgrupo, $idcotizacion, $persona, $solicitado){
		$xT			= new cGrupos_solicitud();//Tabla
		$xT->grupo_id($idgrupo);
		$xT->grupos_solicitud_id("NULL");
		$xT->idusuario(getUsuarioActual());
		$xT->monto_autorizado(0);
		$xT->monto_solicitado($solicitado);
		$xT->originacion_grupos_id($idcotizacion);
		$xT->persona_id($persona);
		
		
		$xT->tiempo(time());
		$qq		= $xT->query();
		$res 	= $qq->insert()->save();
		$id		= setNoMenorQueCero($qq->getLastInsertID());
		return ($res === false) ? false : $id;
	}
	
}




class cGruposNiveles {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "creditos_nivelesdegrupo";
	private $mTipo		= 0;
	private $mTasaNormal	= 0;
	private $mtasaMora		= 0;
	private $mNivelMaximo	= 0;
	private $mTasaAhorro	= 0;
	private $mDiasMaximo	= 0;
	private $mMontoBase		= 0;
	
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
		$xT			= new cCreditos_nivelesdegrupo();//Tabla
		
		
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
			
			$this->mClave		= $data[$xT->IDCREDITOS_NIVELESDEGRUPO];
			
			$this->mMontoBase	= $data[$xT->MONTO_XINTEGRANTE];
			$this->mDiasMaximo	= $data[$xT->DIAS_MAXIMO];
			
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
	function getMontoBase(){ return $this->mMontoBase; }
}

?>