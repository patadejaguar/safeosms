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
	if(MODO_DEBUG == true){$codigo_de_error = 2; }
	$txt				= $texto;
	if(is_array($texto)){
		$txt			= json_encode($texto);
	}
	$xErr		= new cError($codigo_de_error);
	return $xErr->setNewError($codigo_de_error, $usuario, $txt);
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
	
	
	function __construct($codigo = false){
		$codigo			= ( $codigo == false ) ? DEFAULT_CODIGO_DE_ERROR : $codigo;
		$this->mCodigo	= $codigo;
	}
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
		if($llen>4096){
			$txt	= "";
		}
		if($txt !== ""){
			$sqlIE 	= "INSERT INTO general_log( fecha_log, hour_log, type_error, usr_log, text_log, ip_private, ip_proxy, ip_public, `idpersona`) 
		    			VALUES('$fecha', '$hora', '$tipo', '$usr', '$txt', '$ip1', '$ip2', '$ip3', '$persona')";
			$xMQL->setRawQuery($sqlIE);
			$txt	= null;
			$sqlIE	= null;
		}
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
		if($tipo == "security"){
			$title	= "Advertencia";
		}
		foreach ($str as $idx =>$txt){
			$hcod	.= "<div class='num $tipo-num'>$txt</div>";
		}
		$html	= "<div class='error $tipo-error'>
				
				<div class='code $tipo-code'>
				$hcod
				</div>

				<div class='message $tipo-message'>
						<h3>$title</h3>
						<hr /><hr />
						<p>" . $this->mDescripcion . "</p><hr />
						</div>
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
	function guardar($codigo, $persona = 0){ $xErr	= new cError(); $xErr->setNewError($codigo, false, $this->mMessages, false, $persona);	}
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
}


class cErrorCodes {
	public $RECIBO_ELIMINADO 	= 2011;
	public $ERROR_SQL 			= 2;
	public $EDICION_RAW 		= 12;
	public $ELIMINAR_RAW 		= 21;
	public $CREDITO_ELIMINADO 	= 1010;
	public $CREDITO_MODIFICADO	= 1051;
	public $PERSONA_MODIFICADA	= 102;
	public $PERSONA_NO_EN_LISTA	= 103;
	public $ERROR_LOGIN			= 98;
	public $SUCCESS_LOGIN		= 10;
	//public $PERSONA_ELIMINADA	= 102;
	public $PASSWORD_MODIFICADO	= 911;
	public $SIN_PERMISO_REGLA	= 400;
	public $SIN_PERMISO_SISTEMA	= 999;
	public $NOMINA_NO_GUARDADA	= 20102;
	public $NOMINA_GUARDADA		= 20103;
	public $NOMINA_ELIMINADA	= 20104;
	public $NOMINA_ENVIO_DUP	= 20105;
	public $USUARIO_NUEVO		= 901;
	
	
}

?>