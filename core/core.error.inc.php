<?php
include_once("core.config.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");

/**
 * Guarda un Error del Core
 * @deprecated
 */
function saveError($type = 0, $usr = false, $text = ""){
	$xErr		= new cError($type);
	$xErr->setNewError($type, $usr, $text);
}
function setLog($texto, $codigo_de_error = false, $usuario = false){
	$codigo_de_error	= ($codigo_de_error == false) ? DEFAULT_CODIGO_DE_ERROR : $codigo_de_error;
	if(MODO_DEBUG == true AND SAFE_ON_DEV == true){ $codigo_de_error = 2; }
	$txt				= $texto;
	if(is_array($texto)){
		$txt			= json_encode($texto);
	}
	$xErr		= new cError($codigo_de_error);
	return $xErr->setNewError($codigo_de_error, $usuario, $txt);
}
function setAgregarEvento_($mensaje, $codigoError,$persona = false, $contrato=false, $recibo = false){
	$xErr		= new cError($codigoError);
	$xErr->setIDDocto($contrato);
	$xErr->setIDRecibo($recibo);
	
	$usuario	= getUsuarioActual();
	$xErr->setNewError($codigoError, $usuario, $mensaje,false, $persona);
	$mensaje	= null;
}
function setNuevoEvento_($mensaje, $codigoError,$persona = false, $contrato=false, $recibo = false){
	setAgregarEvento_($mensaje, $codigoError,$persona, $contrato, $recibo);
}
function setNuevaTarea_($mensaje, $tipo, $usuario, $relevancia = false, $persona = false, $contrato=false){
	$xUN	= new cSystemUserNotes();
	$xUN->add($usuario, $mensaje, $relevancia, $tipo, $persona, $contrato);
	$xUN	= null;
}
function setError($str = ""){ setLog("Error : $str");}
function setArchivarRegistro($src , $tipo = 0){
	$xT	= new cSistema_eliminados();
	$xT->idsistema_eliminados("NULL");
	$xT->tipoobjeto($tipo);
	$xT->contenido($src);
	$xT->idusuario(getUsuarioActual());
	$xT->tiempo(time());
	$xT->query()->insert()->save();
	unset($xT);
}
class cError {
	protected $mArrValues	= array();
	protected $mDescripcion	= "";
	protected $mCodigo		= 9001;
	protected $mIDCod		= false;
	private $mTipo			= "common";
	public $DEVELOPER		= "developer";
	public $SECURITY		= "security";
	public $COMMON			= "common";	
	public $ERR_NO_ACTIVO	= 404;
	public $ERR_MODULO		= 97;
	private $mIDDocto		= 0;
	private $mIDRecibo		= 0;
	
	function __construct($codigo = false){
		$codigo			= ( $codigo == false ) ? DEFAULT_CODIGO_DE_ERROR : $codigo;
		$this->mCodigo	= $codigo;
	}
	function setIDDocto($id){ $this->mIDDocto = $id; }
	function setIDRecibo($id){ $this->mIDRecibo = $id; }
	function setNewError($tipo = 0, $usr = false, $txt = "", $fecha = false, $persona = 0){
		$xMQL	= new MQL();
	
		$ip1 	= get_real_ip();//isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "0";
		$ip2 	= ( isset($_SERVER['HTTP_VIA']) ) ? $_SERVER['HTTP_VIA'] : "";
		$ip3 	= ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "";
		$defUsr	= isset($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]) ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : "USUARIO_DESCONOCIDO";
			
		$usr	= ($usr == false ) ? $defUsr : $usr; 
		$txt	= (!isset($txt) OR $txt == false OR trim($txt) == "") ? "" : addslashes("$txt"); 
		$fecha	= ($fecha == false) ?  date("Y-m-d") : $fecha;
		$hora 	= date("H:i:s");
		$llen	= strlen($txt);
		$xlim	= 4096;
		if(MODO_DEBUG){
			$xlim	= 6144;
		}
		if($llen>$xlim){
			$txt	= substr($txt, 0, $xlim);
		}
		$f1		= "";
		$v1		= "";
		
		$f2		= "";
		$v2		= "";
		if($this->mIDDocto>1){
			$f1	= ",`iddocumento`";
			$v1	= ",'"  . $this->mIDDocto . "'";
		}
		if($this->mIDRecibo>1){
			$f2	= ",`idrecibo`";
			$v2	= ",'"  . $this->mIDRecibo . "'";
		}
		if($txt !== ""){
			$sqlIE 	= "INSERT INTO general_log( fecha_log, hour_log, type_error, usr_log, text_log, ip_private, ip_proxy, ip_public, `idpersona` $f1 $f2) 
		    			VALUES('$fecha', '$hora', '$tipo', '$usr', '$txt', '$ip1', '$ip2', '$ip3', '$persona' $v1 $v2 )";
			$xMQL->setRawQuery($sqlIE);
		}
		$txt	= null;
		$sqlIE	= null;
		return $xMQL->getLastInsertID();
	}
	function setGoErrorPage($codigo = false){
		$codigo		= ( $codigo == false ) ? DEFAULT_CODIGO_DE_ERROR : $codigo;
		header("location:../404.php?i=" . $codigo);
	}
	function init(){
		$id					= $this->mCodigo;
		$sql_errors 		= "SELECT  idgeneral_error_codigos, description_error, type_err 
    							FROM general_error_codigos WHERE idgeneral_error_codigos=$id
    							LIMIT 0,1 ";
		$xQL				= new MQL();
		$rw					= $xQL->getDataRow($sql_errors);
		$xT					= new cGeneral_error_codigos();
		if(isset($rw[$xT->IDGENERAL_ERROR_CODIGOS])){
			$this->mCodigo		= $rw[$xT->IDGENERAL_ERROR_CODIGOS];
			$this->mDescripcion = $rw[$xT->DESCRIPTION_ERROR];
			$this->mTipo		= $rw[$xT->TYPE_ERR];
		}
	}
	function getDescription(){ 	return $this->mDescripcion;	}
	function getCodigo(){ return $this->mCodigo; }
	function getFicha(){
		$str	= str_split($this->mCodigo);
		$hcod	= "";
		$tipo	= $this->mTipo;
		$title	= "Aviso";
		$cls	= "notice";
		if($tipo == "security"){
			$title	= "Advertencia";
			$cls	= "error";
		}

		$html	= "<div class='cuadro'>
						<fieldset id=\"inputs\">
						<h3>$title</h3>
						<hr />
						<p class='$cls'>" . $this->mDescripcion . "</p><hr /><span class='error-num'>" . $this->mCodigo . "</span>
						</fieldset>
						<fieldset id=\"actions\">
								<a class=\"button\" onclick=\"window.history.back(1)\"><img src='images/back-button.png' height='24px' style='margin-top:6px' alt='Atras' /></a>
						</fieldset>
						</div>";
		$hcod	= null;
		return $html;
	}
	function getREQVars(){
		$cnt	= "";
		foreach ($_REQUEST  as $var => $val){
			$cnt	.= "($var|$val)";
		}
		return $cnt;
	}
}
class cCoreLog{
	private $SalidaDestino	= "sys";
	private $mIDError		= false;
	public $DEVELOPER		= "developer";
	public $SECURITY		= "security";
	public $COMMON			= "common";
	private $mMessages		= "";
	private $mOCat			= null;
	private $mForceNoLog	= false;
	
	function __construct($iderror = false){
		$this->mIDError	= $iderror;
	}
	function toOut($text	= ""){
		/*if ( MODO_DEBUG == true ){
			
		}*/
	}
	function init($id = false){
		$id		= ($id == false) ? $this->mIDError : $id;
	}
	function getDescription(){	}
	function guardar($codigo, $persona = 0, $contrato=0, $recibo=0){ 
		$xErr	= new cError();
		$xErr->setIDDocto($contrato);
		$xErr->setIDRecibo($recibo);
		$xErr->setNewError($codigo, false, $this->mMessages, false, $persona);	
	}
	function add($msg, $level = "common"){
		$this->mMessages	.= ($level == $this->DEVELOPER AND (MODO_DEBUG == false)) ? "" : $msg;
		$msg					= null;
		if($this->mForceNoLog == true){
			$this->mMessages	= "";
		}
	}
	function getMessages($put = OUT_TXT){ $xH	 = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function OCat(){if($this->mOCat == null){$this->mOCat = new cErrorCodes(); } return $this->mOCat; }
	function setNoLog(){ $this->mForceNoLog = false; }
	function getListadoDeEventosSQL($persona=false, $documento=false, $recibo=false, $buscar=""){
		$xL			= new cSQLListas();
		$sql		= $xL->getListadoDeEventos("", "", "", "", "", $buscar, $persona, $documento, $recibo);
		return $sql;
	}
}


class cErrorCodes {
	public $RECIBO_ELIMINADO 	= 2011;
	public $ERROR_SQL 			= 2;
	public $EDICION_RAW 		= 12;
	public $ELIMINAR_RAW 		= 21;
	public $CREDITO_ELIMINADO 	= 1010;
	public $CREDITO_MODIFICADO	= 1051;
	public $CREDITO_CASTIGADO	= 1052;
	public $CREDITO_PLANNEW		= 20019;
	public $PERSONA_MODIFICADA	= 102;
	public $PERSONA_NO_EN_LISTA	= 103;
	public $PERSONA_INOUT_TADA	= 104;
	public $ERROR_LOGIN			= 98;
	public $SUCCESS_LOGIN		= 10;
	public $OPERACION_NO_REG	= 501;
	//public $PERSONA_ELIMINADA	= 102;
	public $PASSWORD_MODIFICADO	= 911;
	public $SIN_PERMISO_REGLA	= 400;
	public $SIN_PERMISO_SISTEMA	= 999;
	public $NOMINA_NO_GUARDADA	= 20102;
	public $NOMINA_GUARDADA		= 20103;
	public $NOMINA_ELIMINADA	= 20104;
	public $NOMINA_ENVIO_DUP	= 20105;
	public $USUARIO_NUEVO		= 901;
	public $PERMISO_NUEVO		= 91;
	
}


class cErrorLog {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "general_log";
	private $mTipo			= 0;
	private $mUsuario		= 0;
	private $mFecha			= false;
	private $mHora			= false;
	private $mTiempo		= 0;
	private $mTexto			= "";
	private $mObservacion	= "";
	
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
		$xT			= new cGeneral_log();
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->IDGENERAL_LOG])){
			$xT->setData($data);
			$this->mClave	= $data[$xT->IDGENERAL_LOG];
			$this->mFecha	= $data[$xT->FECHA_LOG];
			$this->mHora	= $data[$xT->HOUR_LOG];
			
			$xD				= new DateTime($this->mFecha . " " . $this->mHora);
			$this->mTiempo	= $xD->getTimestamp();
			
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
	function getTiempo(){ return $this->mTiempo; } 
	function getAntiguedadEnMinutos(){
		$ahora	= time();
		$antes	= $this->getTiempo();
		
		if($antes> 60){
			return ceil( ( ($ahora-$antes) / 60),0 );
		}
		return 0;
	}
}
?>