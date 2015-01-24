<?php
include_once("core.config.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");

/**
 * Guarda un Error del Core
 * @param string $type	Tipo de Error
 * @param string $usr	Usuario que genero el Error
 * @param string $text	Texto Guardado
 * @return boolean
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
		foreach ($texto as $key => $val){
			$txt	.= "$key = $val\r\n";
		}
	}
	$xErr		= new cError($codigo_de_error);
	return $xErr->setNewError($codigo_de_error, $usuario, $txt);
}

class cError {
	protected $mArrValues	= array();
	protected $mDescripcion	= "";
	protected $mCodigo		= 9001;
	protected $mIDCod		= false;
	
	
	
	function __construct($codigo = false){
		$codigo			= ( $codigo == false ) ? DEFAULT_CODIGO_DE_ERROR : $codigo;
		$this->mCodigo	= $codigo;
	}
	function setNewError($tipo = 0, $usr = false, $txt = "", $fecha = false){
		$ip1 	= ( isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "0";
		$ip2 	= ( isset($_SERVER['HTTP_VIA']) ) ? $_SERVER['HTTP_VIA'] : "DESCONOCIDO";
		$ip3 	= ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "DESCONOCIDO";
		$defUsr	= isset($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]) ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : "USUARIO_DESCONOCIDO";
		
		$usr	= ($usr == false ) ? $defUsr : $usr; 
		$txt	= (!isset($txt) OR $txt == false OR $txt == "") ? "" : addslashes("$txt"); 
		//$txt 	= addslashes($txt);
		$fecha	= ($fecha == false) ?  date("Y-m-d") : $fecha;
		$hora 	= date("H:i:s");
		$sqlIE 	= "INSERT INTO general_log( fecha_log, hour_log, 
							type_error, usr_log, text_log,
							ip_private, ip_proxy, ip_public) 
	    			VALUES('$fecha', '$hora', '$tipo', '$usr', '$txt', '$ip1', '$ip2', '$ip3')";
		
		$xMQL	= new MQL();
		$xMQL->setRawQuery($sqlIE);
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
		}
	}
	function getDescription(){ 	return $this->mDescripcion;	}
	function getCodigo(){ return $this->mCodigo; }
	function getFicha(){
		$html	= "<div class='error'>
				<header>
				<h3>ERROR</h3> <h1>" . $this->mCodigo . "</h1>
						</header>
						<p class='message'>
				" . $this->mDescripcion . "</p>
						</div>";
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
	function guardar($codigo){ $xErr	= new cError(); $xErr->setNewError($codigo, false, $this->mMessages);	}
	function add($msg, $level = "common"){
		$this->mMessages	.= ($level == $this->DEVELOPER AND (MODO_DEBUG == false)) ? "" : $msg;
		$msg		= null;
	}
	function getMessages($put = OUT_TXT){ $xH	 = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function OCat(){if($this->mOCat == null){$this->mOCat = new cErrorCodes(); } return $this->mOCat; }
}


class cErrorCodes {
	public $RECIBO_ELIMINADO 	= 2011;
	public $ERROR_SQL 			= 2;
	public $EDICION_RAW 		= 12;
	public $ELIMINAR_RAW 		= 21;
}

?>