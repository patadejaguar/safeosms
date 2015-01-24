<?php
include_once("global.inc.php");
include_once("database.dic.php");
@include_once("../libs/aes.php");

//----------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------- OK
//----------------------------------------------------------------------------------------------------------------------------



class MQLCampo {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mEquivalencias	= array();

	function __construct($datos){
		$this->mDatos	= $datos;
		$mql			= new MQL();
		$this->mEquivalencias	= $mql->getTipos();
	}
	function getTipo(){
		return $this->mEquivalencias[ $this->mDatos["T"] ];
	}
	function getLongitud(){
		return $this->mDatos["L"];
	}
	function getValor(){
		return ( $this->getTipo() == MQL_STRING )? htmlentities($this->mDatos["V"]) : $this->mDatos["V"];
	}
	function v($out = OUT_HTML){
		$dato	= "";
		if ( $this->getTipo() == MQL_STRING ){
			if( $out == OUT_HTML){
				$dato	= htmlentities($this->mDatos["V"]);
			} else {
				$dato	= $this->mDatos["V"];
			}
				
		} else {
			$dato	= $this->mDatos["V"];
		}
		return $dato;
	}
	function get(){
		return $this->mDatos["N"];
	}
	function isEqual($value){
		return ( $this->getTipo() == MQL_STRING ) ? $this->get() . "=\"$value\" " : $this->get() . "=$value ";
	}
	function HTMLInput($etiqueta = ""){}
	function getHTMLJs(){}

}

class MQL {
	private $mDatos	= array();
	private $mTabla	= "";
	private $mPrimary	= "";
	private $cnn		= null;
	private $mMessages	= "";
	private $mSql		= "";
	private $mInsertID	= false; 


	private $mEquivalencias	= array(
			"INT" 		=> "int",
			"TINYINT" 	=> "int",
			"SMALLINT" 	=> "int",
			"MEDIUMINT" => "int",
			"BIGINT" 	=> "int",
			"YEAR" 		=> "int",
			"TIMESTAMP" => "int",

			"FLOAT" 	=> "float",
			"DOUBLE" 	=> "float",
			"DECIMAL" 	=> "float",

			"VARCHAR" 	=> "string",
			"CHAR" 		=> "string",
			"TEXT" 		=> "string",
			"LONGTEXT" 	=> "string",
			"TINYTEXT" 	=> "string",
			"MEDIUMTEXT" => "string",
			"DATE" 		=> "string",
			"DATETIME" 	=> "string",
			"TIME" 		=> "string",
			"ENUM"		=> "string",

			"BINARY" 	=> "string",
			"BLOB" 		=> "string",
			"MEDIUMBLOB" 		=> "string"
	);
	

	function __construct($tabla = "", $datos = array(), $primaryK = ""){
		$this->mDatos	= $datos;
		$this->mTabla	= $tabla;
		$this->mPrimary	= $primaryK;
	}
	function select(){	return new MQLSelect($this->mTabla, $this->mDatos, $this->mPrimary);	}
	function insert(){	return new MQLInsert($this->mTabla, $this->mDatos, $this->mPrimary);	}
	function delete(){	}
	function update(){	return new MQLUpdate($this->mTabla, $this->mDatos,  $this->mPrimary);	}
	function setToUTF8(){ 
		foreach ($this->mDatos as $campo => $opts ){
			if(isset($this->mDatos[$campo]["T"])){
				if( $this->mEquivalencias[ $this->mDatos[$campo]["T"] ] == MQL_STRING ){
					$this->mDatos[$campo]["V"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->mDatos[$campo]["V"]);
				}
			}			
		}
	}
	function getTipos(){ return $this->mEquivalencias;	}
	function connect(){
		$this->cnn = new mysqli(MQL_SERVER, MQL_USER, MQL_PASS, MQL_DB);
		if ($this->cnn->connect_errno) {
			$this->mMessages	.= "ERROR EN LA CONEXION : ". $this->cnn->connect_error . " \n"; $this->getDebug();
			$this->cnn			= null;
		}
		return $this->cnn;
	}
	function row($data){ return $this->setData($data); }
	function setData($data = null){
		
		$this->mMessages	.= "ASIGNANDO DATOS\r\n";
		$data		= ($data == null) ? $_REQUEST : $data;
			
		foreach($this->mDatos as $dato => $field){
			$campo							= $field["N"];
			if(isset($data[$campo])){
				$this->mDatos[$campo ]["V"] 	= ($this->mEquivalencias[ $this->mDatos[$campo]["T"] ] == MQL_STRING  ) ? addslashes($data[$campo]) : $data[$campo];
			}
		}
		return $this->mDatos;
	}
	function getRow($where){
		$select				= new MQLSelect($this->mTabla, $this->mDatos, $this->mPrimary);
		$select->get();
		$datos				= $select->exec($where);
		$this->mMessages	.= $select->log();
		return ( isset($datos[0]) ) ? $datos[0] : array();
	}
	function setRow($where){
		$this->setData( $this->getRow($where) );
	}
	function getLastID(){
		$contar				= 0;
		$cnn				= $this->connect();
		$rs					= $cnn->query("SELECT LAST_INSERT_ID() AS 'conteo' FROM " . $this->mTabla . " LIMIT 0,1");
		$row 				= $rs->fetch_assoc();
		$contar				= $row["conteo"];
		if($contar == 0){
			$rs				= $cnn->query("SELECT MAX(" . $this->mPrimary . " ) AS 'conteo' FROM " . $this->mTabla . " LIMIT 0,1");
			$row 			= $rs->fetch_assoc();
			$contar			= $row["conteo"];
		}
		return $contar + 1;
	}
	function getLog(){
		return $this->mMessages;
	}
	function initByID($id){
		if( is_string($id)){
			$id			= "'$id'";
		}
		return $this->getRow($this->mPrimary . "=$id" );
	}
	function getRecordset($sql= ""){
		//$sql	= ($sql == "")
		$cnn		= $this->connect();
		$rs			= false;
		if($cnn == null){
			//
		} else {
			$rs		= $cnn->query($sql);
			if($rs == false){
				$this->mMessages	.= "ERROR(". $cnn->error . ") EN EL QUERY : " . $sql . "  \n"; $this->getDebug();
			}
		}
		return $rs;
	}
	function getDataRecord($sql){
		$this->mSql		= $sql;
		$this->mMessages .= "SQL[ " . $this->mSql . "]\r\n";

		$cnn				= $this->connect();
		$rs					= $cnn->query($this->mSql);
		if(!$rs){ $this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n"; $this->getDebug(); }
		$data				= array();
		if($rs){
			while ($row = $rs->fetch_assoc()) { 	$data[]		= $row; }
		}
		return $data;
	}
	function getArrayRecord($sql){
		$this->mSql		= $sql;
		$this->mMessages .= "SQL[ " . $this->mSql . "]\r\n";
	
		$cnn				= $this->connect();
		$rs					= $cnn->query($this->mSql);
		if(!$rs){ $this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n"; $this->getDebug(); }
		$data				= array();
		if($rs){
			while ($row = $rs->fetch_array()) { 	$data[$row[0]]		= isset($row[1]) ? $row[1] : $row[0]; }
		}
		return $data;
	}	
	function setRawQuery($sql){
		$this->mSql		= $sql;
		$this->mMessages .= "SQL[ " . $this->mSql . "]\r\n";

		$cnn				= $this->connect();
		$rs					= $cnn->query($this->mSql);
		if( $rs == true){
			$this->mMessages .= "SUCESS\tQuery is OK!\r\n";
		} else {
			$this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n";
			$this->getDebug();
		}
		if(isset($cnn->insert_id)){
			$this->mInsertID	= $cnn->insert_id;	
		}
		return $rs;
	}
	function html(){
		return new MQLHtml($this->mTabla, $this->mDatos, $this->mPrimary);
	}
	function campo($campo = ""){
		//return new MQLCampo($this->mCampos["eacp"]);
		return new MQLCampo($this->mDatos[$campo]);
	}
	function getCampos(){
		return $this->mDatos;
	}
	function setCampos($campos){
		$this->mDatos	= $campos;
	}
	function getMessages($out ){ return $this->mMessages; }
	function getDebug(){
		if(function_exists("setLog")){
			if( defined("MODO_DEBUG")){
				setLog($this->mMessages);
			}
		}
	}
	function getLastInsertID(){ return $this->mInsertID; }
	
	
}
class MQLInsert {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mEquivalencias	= array();

	private $mSql				= "";
	private $mIns				= array();
	private $mMessages			= "";

	function __construct($tabla, $datos){
		$this->mDatos	= $datos;
		$this->mTabla	= $tabla;
		$mql			= new MQL($tabla, $datos);
		$this->mEquivalencias	= $mql->getTipos();
		
	}

	function save(){
		$mql	= new MQL();
		$this->get();
		$cnn	= $mql->connect();
		$rs		= $cnn->query($this->mSql);
		$id		= false;
		if($rs == false){
			$this->mMessages	.= "ERROR(". $cnn->error . ") EN EL QUERY : " . $this->mSql . "  \n"; $this->getDebug();
		} else { $id = $cnn->insert_id; }
		return $id;
	}
	function get(){
		$sql 	= "";
		$vals	= "";
		$camp	= "";
		$icnt	= 0;
		foreach ($this->mDatos as $t){
			$tipo	= $this->mEquivalencias[$t["T"]];
			$nombre	= $t["N"];
			$valor	= $t["V"];
			$this->mIns[$nombre] = $valor;		//medoo Insert
			if($tipo == MQL_STRING){
				$valor	= "\"$valor\"";
			}
			$camp 	.= ($icnt == 0) ? "$nombre" : ",$nombre";
			$vals	.= ($icnt == 0) ? "$valor" : ",$valor";
			$icnt++;
		}
		$this->mSql		= "INSERT INTO " . $this->mTabla . "($camp) VALUES ($vals) ";
		return $this->mSql;
	}
	function getMessages($out ){ return $this->mMessages; }
	function getDebug(){
		if(function_exists("setLog")){
			if( defined("MODO_DEBUG")){
				setLog($this->mMessages);
			}
		}
	}
}
class MQLSelect	 {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mPrimary			= "";
	private $mEquivalencias	= array();
	private $mMessages			= "";

	private $mSql				= "";
	private $mWhere				= "";

	private $mIns				= array();
	private $mOrderBy			= "";

	function __construct($tabla, $datos, $primaryK){
		$this->mDatos			= $datos;
		$this->mTabla			= $tabla;
		$this->mPrimary			= $primaryK;

		$mql					= new MQL($tabla, $datos);
		$this->mEquivalencias	= $mql->getTipos();
	}
	function order($str){
		$this->mOrderBy	= $str;
		$this->mSql = $this->mSql . " ORDER BY $str";
	}
	function addAnd($txt){
		$this->mWhere	.= " AND  ($txt) ";
	}

	function get($where = ""){
		$sql 	= "";
		$vals	= "";
		$camp	= "";
		$icnt	= 0;
		foreach ($this->mDatos as $t){
			$tipo	= $this->mEquivalencias[$t["T"]];
			$nombre	= $t["N"];
			$valor	= $t["V"];
			$camp .= ($icnt == 0) ? "$nombre" : ",$nombre";
			$icnt++;
		}
		$where			= ( trim($where) == "" ) ? "" : " WHERE $where ";
		if(trim($this->mWhere != "") ){
			$this->mSql		.= ( strpos( strtoupper($this->mSql), "WHERE" ) === false) ?  " WHERE " : "";
			$this->mSql		.= $this->mWhere;
		}

		$this->mSql		= "SELECT $camp FROM " . $this->mTabla . " $where ";
		return $this->mSql;
	}
	/**
	 * @param mixed $conditions
	 * @return string
	 * @example where("CAMPO = VALOR");
	 * @example where(array("CAMPO" => "VALOR", "CAMPO2" => "VALOR2");
	 * @example where(array(
	 * 					array("CAMPO", "!=", "VALOR")
	 * 					);
	 */
	function where($conditions){
		$where		= "";
		$arrOps		= array(" AND ", " OR ", "!=", "=");
		if (is_array($conditions)){
			$icnt	= 0;
			foreach($conditions as $campo => $valor){
				if( is_array($valor)){
					$items		= count($valor);
					$operador	= ($items == 2) ? "=" : $valor[1];
					$ICampo		= $valor[0];
					$Ivalor		= ($items == 2) ? $valor[1] : $valor[2];
					$Ivalor 	= ($this->mEquivalencias[ $this->mDatos[ $ICampo ]["T"] ] == MQL_STRING) ? "\"$Ivalor\"" : $Ivalor;
					$where 		.= ($icnt == 0) ? "$ICampo $operador $Ivalor" : " AND $ICampo $operador $Ivalor";
					$this->mIns[$ICampo] = $Ivalor;
				} else {
					//array campo , operador, valor
					$valor 		= ($this->mEquivalencias[ $this->mDatos[ $campo ]["T"] ] == MQL_STRING) ? "\"$valor\"" : $valor;
					$where 		.= ($icnt == 0) ? "$campo = $valor" : " AND $campo = $valor";
					$this->mIns[$campo] = $valor;
				}
				$icnt++;
			}
		} else {
			//checkvar
			$isStr	= false;
			foreach($arrOps as $key => $val){
				if (strpos($conditions, $val) !== false){
					$isStr		= true;
				}
			}
			if( $isStr == true ){
				$where		= $conditions;
			} else {
				$where 		.= $this->mPrimary . " = ";
				$where 		.= ($this->mEquivalencias[$this->mDatos[$this->mPrimary]["T"]] == MQL_STRING) ? " \"$conditions\" " : $conditions;
			}
		}
		$where		= ($where != "") ? " WHERE $where " : "";
		$this->mSql	= $this->get() . $where;
		return $this->mSql;
	}
	function limit($init = 0, $end = 1){
		$this->mSql	= $this->get() . " LIMIT $init, $end";
		return $this->mSql;
	}
	function exec($where = "", $orders	= ""){
		if($this->mSql == "") { $this->get(); }
		if($where != ""){ $this->where($where); }
		if($orders != ""){ $this->order($orders); }
		if(trim($this->mWhere != "") ){ //TODO: Analizar
			$this->mSql		.= ( strpos( strtoupper($this->mSql), "WHERE" ) === false) ?  " WHERE " : "";
			$this->mSql		.= $this->mWhere;
		}
		$this->mMessages .= "SQL[ " . $this->mSql . "]\r\n";

		$mql				= new MQL();
		$cnn				= $mql->connect();
		$rs					= $cnn->query($this->mSql);
		if(!$rs){ $this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n"; $this->getDebug(); }
		$data				= array();
		if($rs){
			while ($row = $rs->fetch_assoc()) { 	$data[]		= $row; }
		}
		return $data;
	}
	function log(){	return $this->mMessages;	}
	function getMessages(){	return $this->mMessages;	}
	function set($sql){ $this->mSql	= $sql;	}
	function getDebug(){
		if(function_exists("setLog")){
			if( defined("MODO_DEBUG")){
				setLog($this->mMessages);
			}
		}
	}
	function service($action = ""){
		return new MQLService($action, $this->get());
	}
}

class MQLDelete {  }
class MQLUpdate {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mEquivalencias	= array();
	private $mPrimaryKey		= "";
	private $mValueKey			= "";
	private $mSql				= "";
	private $mIns				= array();

	function __construct($tabla, $datos, $primaryKey){
		$this->mDatos	= $datos;
		$this->mTabla	= $tabla;
		$mql			= new MQL($tabla, $datos);
		$this->mEquivalencias	= $mql->getTipos();
		$this->mPrimaryKey		= $primaryKey;
	}
	function setID($id){
		$this->mValueKey	= $id;
	}
	function save($idKey){
		$this->mValueKey	= $idKey;
		$mql	= new MQL();
		$where		=  $this->mPrimaryKey . "=\"" . $this->mValueKey . "\" ";
		$this->get($where);
		$cnn	= $mql->connect();
		
		$rs		= $cnn->query($this->mSql);
		if($rs == false){
			$this->mMessages	.= "ERROR(". $cnn->error . ") EN EL QUERY : " . $this->mSql . "  \n"; $this->getDebug();
		}
				
		return true; //$cnn->insert_id;
	}
	function get($where = ""){
		$sql 	= "";
		$vals	= "";
		$icnt	= 0;
		//$where	= ($this->mValueKey == "") ? $where : $this->mPrimaryKey . "=\"" . $this->mValueKey . "\" $where ";
		$where		= ($where == "") ? $this->mPrimaryKey . "=\"" . $this->mValueKey . "\"" : $where;
		foreach ($this->mDatos as $t){
			if(isset($t["N"]) ){
				$tipo	= $this->mEquivalencias[$t["T"]];
				$nombre	= $t["N"];
				$valor	= $t["V"];
				$this->mIns[$nombre] = $valor;		//medoo Insert
				$valor	= ($tipo == MQL_STRING) ? "`$nombre`=\"$valor\"" : "`$nombre`=$valor" ;
				$vals	.= ($icnt == 0) ? "$valor" : ", $valor";
				$icnt++;
			}
		}
		$this->mSql		= "UPDATE " . $this->mTabla . " SET $vals WHERE $where";
		return $this->mSql;
	}
	function getMessages($out){ return $this->mMessages; }
	function getDebug(){
		if(function_exists("setLog")){
			if( defined("MODO_DEBUG")){
				setLog($this->mMessages);
			}
		}
	}	
}
class MQLHtml {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mPrimary			= "";
	private $mMessages			= "";

	function __construct($tabla, $datos, $primaryK){
		$this->mDatos			= $datos;
		$this->mTabla			= $tabla;
		$this->mPrimary			= $primaryK;
	}
	function select($label = "", $where = "", $id = ""){
		$q		= new MQLSelect($this->mTabla, $this->mDatos, $this->mPrimary);
		$data	= $q->exec($where);
		$this->mMessages	.= $q->log();
		$options	= array();
		foreach($data as $data){
			$options[ $data[$this->mPrimary] ]	= $data[ $label ];
		}
		return new cHSelect($id, $options);
	}
	function log(){
		return $this->mMessages;
	}
}
class MQLService {
	private $mSQL		= "";
	private $mAction	= ""; //select insert update
	private $mKey		= "HIMITSU";
	private $mError		= array("code" => 0, "msg" => "");
	private $mMessages	= "";
	
	function __construct($action, $sql){
		$this->mAction	= $action;
		$this->mSQL		= $sql;
		$this->mKey		= "HIMITSU";
	}
	function setSQL($sql){	$this->mSQL	= $sql;	}

	function getJSON(){
		$json			= array();
		$mql			= new MQL();
		$rs				= $mql->getRecordset($this->mSQL);
		$idx			= 0;
		if($rs){
			while ($row = $rs->fetch_assoc()) {
				foreach($row as $campo => $valor){
					if ( is_string($valor) ){
						$valor		= htmlentities($valor);//htmlentities( (string) $valor, ENT_QUOTES, 'utf-8', FALSE);
					}
					$json["record_$idx"][$campo]	= $valor; //base64_encode($valor);//utf8_encode($valor);
				}
				$idx++;
			}
		} else {
			$json			= $json["error"] = $mql->getMessages(OUT_TXT);
		}
		return json_encode($json);
	}
	function getJsonField(){
		$json			= array();
		$mql			= new MQL();
		$rs				= $mql->getRecordset($this->mSQL);
		$idx			= 0;
		$rw				= array();
		if($rs){
			while ($row = $rs->fetch_assoc()) {
				$rw[]		= $row;
			}
			$json["Result"]		= "OK";
		} else {
			$json["error"] 		= $mql->getMessages(OUT_TXT);
			$json["Result"]		= "ERROR";
		}
		$json["Records"] 		= $rw;
		$json["Message"] 		= $mql->getMessages(OUT_TXT);
		return json_encode($json);
	}
	function getJsonSelect(){
		$json			= array();
		$mql			= new MQL();
		$rs				= $mql->getRecordset($this->mSQL);
		$idx			= 0;
		if($rs){
			while ($row = $rs->fetch_assoc()) {
				foreach($row as $campo => $valor){
					if ( is_string($valor) ){
						$valor		= htmlentities($valor);//htmlentities( (string) $valor, ENT_QUOTES, 'utf-8', FALSE);
					}
					$json["record_$idx"][$campo]	= $valor; //base64_encode($valor);//utf8_encode($valor);
				}
				$idx++;
			}
		} else {
			$json			= $json["error"] = $mql->getMessages(OUT_TXT);
		}
		return json_encode($json);
	}
	function getEncryptData($content){
		$xA			= new AesCtr();
		$content	= $xA->encrypt($content, $this->mKey, 256);
		$content	= base64_encode($content);
		return $content;
	}
	function getDecryptData($content){
		$xA			= new AesCtr();
		$content 	= base64_decode($content);

		$content 	= $xA->decrypt($content, $this->mKey, 256);
		return $content;
	}
	function checkCTX($ctx){
		$rs		= ($ctx == $this->getCTX()) ? true : false;
		if($rs == false) { $this->mError = array("code" => 1, "msg" => "No autenticado"); }
		return $rs;
	}
	function getCTX(){
		//SERVERKEY + SERVERDATE + USER
		$usr			= "";
		$date			= date("Ymd");
		$ip 			= ( isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "0";
		return  md5("|" . $usr . "|" . $date . "|" . $ip);
	}
	function getError(){ return json_encode($this->mError);	}
	
	function getService($url){
		$obj		= null;
		$req 		= $this->getRequest($url);// file_get_contents($url);
		if(!$req){
			setLog("Error al procesar la url : $url");
		} else {
			$req		= $this->getDecryptData($req);
			$obj		= json_decode($req, true);
			/*if(!$obj){
				
			} else {
				
			}*/
			/*	str	= base64.decode(str);
			str	= Aes.Ctr.decrypt(str, CloudConfig.apiKey, 256)
			*/
			/*
			$data		= (isset($_REQUEST["data"])) ? $svc->getDecryptData($_REQUEST["data"]) : null;
			$command	= (isset($_REQUEST["cmd"])) ? $svc->getDecryptData($_REQUEST["cmd"]) : null;
			$context	= (isset($_REQUEST["ctx"])) ? $svc->getDecryptData($_REQUEST["ctx"]) : null;
			$obj		= json_decode($data, true);
			*/			
		}
		return $obj;
	}
	function getRequest($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		$return = curl_exec($ch); curl_close ($ch);
		return $return;		
	}
}


?>