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
	
		$ip1 	= ( isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "0";
		$ip2 	= ( isset($_SERVER['HTTP_VIA']) ) ? $_SERVER['HTTP_VIA'] : "DESCONOCIDO";
		$ip3 	= ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "DESCONOCIDO";
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
		$mql				= new MQL();
		$rs					= $mql->getDataRecord($sql_errors);
		foreach ($rs as $rw){
			$this->mCodigo		= $rw["idgeneral_error_codigos"];
			$this->mDescripcion = $rw["description_error"];
			$this->mTipo		= $rw["type_err"];
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
						<h3>$title</h3>
						<hr />
						<p class='$cls'>" . $this->mDescripcion . "</p><hr /><span class='error-num'>" . $this->mCodigo . "</span>
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

?>