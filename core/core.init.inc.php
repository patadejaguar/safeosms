<?php
include_once("core.config.inc.php");
include_once("entidad.datos.php");
include_once("core.error.inc.php");
include_once("core.common.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");

@include_once ("../libs/aes.php");
@include_once ("../libs/Encoding.php");
//mysql_set_charset('latin1');

/**
 * funcion que dispone del valor y si es menor a cero, lo deja como cero
 * @param	float	value	Valor a tratar
 * @return	float			Valor tratado
 */
function setNoMenorQueCero($value, $decimals = false){
	if(!isset($value) ){ $value  = 0;  }
	if(is_null($value) ){ $value = 0; }
	$value		= ($value == "") ? 0 : $value;
	if($value !== 0){
		$xT			= new cTipos();
		$value		= $xT->cFloat($value, $decimals);
		$value		= ($value == "") ? 0 : $value;
	}
	return ($value< 0) ? 0 : $value;
}
function setFechaValida($value){
	if(!isset($value) ){ $value  = fechasys();  }
	if(is_null($value) ){ $value = fechasys(); }
	$value		= ($value == "") ? fechasys() : $value;
	$value		= ($value == "0000-00-00") ? fechasys() : $value;
	$value 		= str_replace("/", "-", $value);
	$xF			= new cFecha();
	$value		= $xF->getFechaISO($value);
	return $value;
}
function setCadenaVal($valor, $largo=0){
	if($largo>0){
		$valor	= substr($valor, 0,$largo);
	}
	$xT			= new cTipos();
	$xT->setForceMayus();
	$xT->setForceClean();
	
	return $xT->cChar($valor);
}
function setLimpiarCadena($str){
	$str	= preg_replace("/[^a-zA-Z0-9]/", "", $str);
	return $str;
}
function getIncludes($extPath = "..", $mTipoObj = HP_FORM){
	$extPath	= ($extPath == "") ? "." : $extPath;
	
	include_once("$extPath/core/core.config.inc.php");
	include_once("$extPath/core/entidad.datos.php");
	include_once("$extPath/core/core.deprecated.inc.php");
	include_once("$extPath/core/core.fechas.inc.php");
	//include_once("$extPath/libs/sql.inc.php");
	//include_once("$extPath/libs/compacw.inc.php");
	
	include_once("$extPath/core/core.creditos.inc.php");
	include_once("$extPath/core/core.creditos.pagos.inc.php");
	include_once("$extPath/core/core.creditos.utils.inc.php");
	include_once("$extPath/core/core.creditos.originacion.inc.php");
	
	include_once("$extPath/core/core.operaciones.inc.php");
	include_once("$extPath/core/core.operaciones.utils.inc.php");
	
	include_once("$extPath/core/core.common.inc.php");
	include_once("$extPath/core/core.personas.inc.php");
	include_once("$extPath/core/core.personas.utils.inc.php");
	
	include_once("$extPath/core/core.security.inc.php");
	include_once("$extPath/core/core.utils.inc.php");
	
	include_once("$extPath/core/core.captacion.utils.inc.php");
	include_once("$extPath/core/core.captacion.inc.php");
	
	include_once("$extPath/core/core.contable.inc.php");
	include_once("$extPath/core/core.contable.utils.inc.php");
		
	include_once("$extPath/core/core.tesoreria.inc.php");
	
	include_once("$extPath/core/core.db.inc.php");
	include_once("$extPath/core/core.db.dic.php");
	
	
	include_once("$extPath/core/core.aml.inc.php");
	include_once("$extPath/core/core.riesgo.inc.php");
	
	include_once("$extPath/core/core.seguimiento.inc.php");
	include_once("$extPath/core/core.seguimiento.utils.inc.php");
	
	include_once("$extPath/core/core.region.inc.php");
	
	switch ($mTipoObj ){
	case HP_FORM:
		@include_once("$extPath/libs/TinyAjax.php");
	break;
	case HP_RECIBO:
	break;
	case HP_RPTXML:
		@include_once "$extPath/reports/PHPReportMaker.php";
	break;
	case HP_GRID:
		include_once(GRID_SOURCE."class/gridclasses.php"); //Include the grid engine.      
		//Define identifying name(s) to your grid(s). Must be unqiue name(s).
		$grid_id = array("grid");
		//Remember to comment the again line when publishing PHP Grid, or else PHP Grid wont remember the settings between page loads.
		unset($_SESSION["grid"]); 
		include_once(GRID_SOURCE."class/gridcreate.php"); //Creates grid objects.			
	break;
	case HP_REPORT:
		include_once("$extPath/core/core.riesgo.reports.php");
		break;
	}	
}
function parametro($nombre, $fallback = null, $tipo = MQL_STRING, $fuente = false){
	$valor		= null;
	$xT			= new cTipos();
	$fuente		= ($fuente == false) ? $_REQUEST : $fuente;
	//if($fuente == false){
		//if(isset($_REQUEST)){ $fuente		= $_REQUEST; }
		switch ($tipo){
			case MQL_INT:
				$fallback	= ($fallback == null) ? 0 : $fallback;
				$valor 		= isset($fuente[$nombre]) ? setNoMenorQueCero($fuente[$nombre],0) : $fallback;
				break;
			case MQL_FLOAT:
				$fallback	= ($fallback == null) ? 0 : $fallback;
				$valor 		= isset($fuente[$nombre]) ? $xT->cFloat( $fuente[$nombre] ) : $fallback;
				break;
			case MQL_BOOL:
				$fallback	= ($fallback == null) ? false : $fallback;
				$valor 		= isset($fuente[$nombre])  ? $xT->cBool( $fuente[$nombre] ) : $fallback;
				//setLog(" $tipo, $fallback $nombre ... " . $fuente[$nombre] . " ----  ") . $xT->cBool( $fuente[$nombre] );
				break;
			case MQL_RAW:
				//$fallback	= ($fallback == null) ? "" : $fallback;
				$valor		= isset($fuente[$nombre]) ? $fuente[$nombre] : $fallback;
				break;
			case MQL_DATE:
				$valor		= isset($fuente[$nombre]) ? $fuente[$nombre] : $fallback; //setLog(setFechaValida($fuente[$nombre]));
				$valor		= setFechaValida($valor);
				break;
			case MQL_ARR_INT:
				$arr		= isset($fuente[$nombre]) ? explode(",", $fuente[$nombre]) : array();
				$valor		= array();
				//setLog($fuente[$nombre]);
				foreach ($arr as $idx => $v){
					$valor[$idx]	= setNoMenorQueCero($v);
				}
				unset($arr);
				break;
			default:
				$fallback	= ($fallback == null) ? "" : $fallback;
				$valor		= isset($fuente[$nombre]) ? $fuente[$nombre] : $fallback;
				if($valor == SYS_TODAS){
					
				} else {
					$xT			= new cTipos();
					$arrRAW		= array("action" => true, "out" => true, "c" => true, "s" => "true", "ctipo_pago" => true, "i" => true, "callback" => true);
					if(SAFE_CLEAN_LANG == true){
						$valor		= (isset($arrRAW[$nombre])) ? $valor : strtoupper($xT->cChar($valor));
					} else {
						$valor		= (isset($arrRAW[$nombre])) ? $valor : $xT->cChar($valor);
					}
					
				}
			break;
		}
	//}
	return $valor;
}
function setChecarValores($arrParams, $source = false){
	//$source		= ($source == false) ? $_REQUEST : $source;
	$result		= false;
	foreach ($arrParams as $itm => $tipo){
		switch ($tipo){
			case MQL_INT:
				$result	= ( $itm == 0 ) ? true : false;
				break;
			case MQL_STRING;
			$result	= ( trim($itm) == "" ) ? true : false;
				break;
		}
	}
	return $result;
}
function getEmails($source = false, $out = false){
	$source = ($source == false) ? $_REQUEST : $source;
	$mails		= ($out == false) ? array() : "";
	//VIAS DE ENVIO
	foreach ($source as $params => $vals){
		if(strpos($params, "mail") !== false){
			if (filter_var($vals, FILTER_VALIDATE_EMAIL)) {
				if($out == MQL_STRING){
					$mails		.= $vals . ",";
				} else {
					$mails[]	= $vals;
				}
			}
		}
	}
	return $mails;
}
function setNoTodas($valor, $resultado){
	$resultado	= ($valor == SYS_TODAS) ? "" : $resultado;
	return $resultado;
}
/**
 * Clase de trabajo para tipos, bug de PHP
 * @package common
 * @subcpakage core
 */
class cTipos {
	private $mPrimate	= false;
	private $mMessages	= "";
	private $mForceUTF	= false;
	private $mForceMayus	= false;
	private $mForceClean	= false; 
	private $mForceEnc		= false;
	private $mEncodeSRC		= "ISO-8859-1"; 
	
	private $mArrOps	= array(
			"efectivo" =>  9100,
			"efectivo.egreso" =>  9100,
			"cheque.ingreso" => 9100,
	
			"cheque" => 9200,
			
			"transferencia" => 9101,
			"transferencia.egreso" => 9101,
	
			"foraneo" => 9100,
			"descuento" => 9201,
			
			"multiple"	=> 99,
			"ninguno" => 99,
			"0" => 99
		);
		
	function  __construct($value = false){
		$this->mPrimate = $value;
	}
	function setValue($value){
		$this->mPrimate = $value;
	}
	function cInt($value = false, $ForceType = false){
		if ($value == false){
			$value 	= $this->mPrimate;
		}
		$value	= $this->setPurgeNumeric( trim($value) );
		//$value	= intval($value);
		//TODO: Posible bug 04/08/2013

		return $value;
	}
	function cFloat($value = false, $digitos = false){
		if ($value == false){
			$value 	= $this->mPrimate;
		}
		$value		= $this->setPurgeNumeric($value, $digitos);
		$value		= floatval($value);
		if ($digitos !== false){
			$value	= round($value, $digitos);
		}
		return $value;
	}
	/**
	 * @deprecated @since 2015.07.01
	 * */
	function cFecha($value){
		if ($value == false){
			$value 	= $this->mPrimate;
		}
		if ( trim($value) == "" ){
			$value = fechasys();
		}
		//reemplazar / por -
		$value		= str_replace("/", "-", $value);
		//hacer un array, buscar si el ultimo digito es mayor a doce
		//verificar si el ultimo digito es mayor a 31
		//verificar si el
		return $value;
	}
	function cChar($value = false, $tamano = false){
		if ($value == false){ $value 	= $this->mPrimate;	}
		//$value			= mb_convert_encoding ($value, mb_detect_encoding($value), "UTF-8");
		$value			= strval($value);
		if(SAFE_CLEAN_LANG == true){
        	$value          = $this->setNoAcentos($value);
		}
        $value			= ($tamano != false ) ? substr($value, 0, $tamano) : $value;
        if($this->mForceClean == true){ $value = $this->cleanString($value); }
        if($this->mForceMayus	== true){ $value		= strtoupper($value); }
		$value			= addslashes($value);
		return 		$value;
	}
	function cPercent($value = false){
		if ($value == false){
			$value 	= $this->mPrimate;
		}
		$value	= $this->setPurgeNumeric($value);
		settype($value, "float");
		if ($value > 1){
			$value	= ($value / 100);
		}
		return $value;
	}
	function setPurgeNumeric($value = false, $digitos = false){
	$value	= ($value === false) ? $value 	= $this->mPrimate : $value;
	if(SYS_SEPARADOR_DECIMAL == "."){ 	$value		= str_replace(",", "", $value);		}
		if( preg_match("/[eE][-]?/", $value) AND is_numeric($value) ){
			$digitos	= ($digitos === false) ? 4 : $digitos; 
			$numero 	= round($value, $digitos) * -1; //setLog("Posible valor negativo en $value / $numero"); 
		} else {	
				//$patron		= "/[..]|[,,]|[--]/";
				$numero		= 0;
				$patron		= "/[^0-9.,-]/";
				//$patron		= "/[[:alpha:]]|[%#$(),*]|[[:space:]]/";
				$value		= preg_replace("/([.]|[,]|[-])\\1+/", "$1", $value);
				$numero		= preg_replace($patron, "", $value);
				$numero		= trim($numero);
		}
		return $numero;
	}
	//MOVIL, FIJO
	function cNumeroTelefonico($value = false, $NumeroModelo = "9811098164"){
		$value 			= ($value == false ) ? $this->mPrimate : $value;
		$value			= $this->cInt($value);
		$value			= str_replace("-", "", $value);
		$value			= trim($value);
		$largo			= strlen($value);
		$LimiteLargo	= 9; //981 10 98164 -- 3 -2 -5 = 9
		$MinimoLargo	= 9;
		//largo basico 5
		if ( $largo < $MinimoLargo){
			//si es menor a 5, error de numero
			$value	= false;
		} else {
			$value	= ( substr( strrev($value), 0, $LimiteLargo ) );
			$value	= strrev($value);
		}
		return $value;
	}
	/**
	 * Funcion para eliminar acentos, C&P de algun foro de la web??, no me ha funcionado
	 * @param $cadena
	 * @return string
	 */
	function setNoAcentos($cadena)	{

		if($this->mForceEnc == true){
			$cadena	= mb_convert_encoding($cadena, "UTF-8", $this->mEncodeSRC );
		}
		$html	= @htmlentities(strtolower($cadena), ENT_COMPAT, "UTF-8");
		if($html == false){ $html = htmlentities($cadena); }
		
		$text	= preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', $html);
		$text	= htmlspecialchars_decode($text);
		$text 	= html_entity_decode($text);
		
		$text	= ($this->mForceMayus == true) ? strtoupper($text) : $text;
		$text	= ($this->mForceClean == true) ? cleanString($text) : $text;
		return $text;
	}
	function setForceEncode($IDEncode = "ISO-8859-1"){ $this->mEncodeSRC = $IDEncode; $this->mForceEnc = true; }
	function cMayusculas($cadena){
		$buscar	= "àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ";
		$reemp	= "aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY";
		$cadena	= utf8_encode( (strtr($cadena ,utf8_decode($buscar),$reemp)));
		$cadena	= strtoupper($cadena);
		//$cadena 	= $this->getStringASCII($cadena);
		return $cadena;
	}
	/**
	 * Serializa un numero o una cadena
	 * @param integer $largo	Largo del serial
	 * @param string $valor		Valor a serializar
	 * @return	string 			Numero Serializado
	 *  - 2012-02-12 : Fixed
	 */
	function cSerial($largo, $valor = false){
		$valor		= ( $valor == false ) ? $this->mPrimate : $valor; 
		settype($largo, "integer");
		$valor		= strrev( "0000000000" . trim($valor) );
		$valor		= substr($valor, 0, $largo);
		return		strrev($valor); 
	}
	/**
	 * Funcion que evalua si un valor es nulo o falso
	 * @param $Valor		Array de valores
	 * @return boolean		Retorna false si tiene un valor invalido.
	 */
	function getEvalNotNull($arrValues = false){

		$eval		= true;
		if ( is_array($arrValues) == true ){
		$lim		= count($arrValues) - 1;
			for ($i = 0; $i<=$lim; $i++){
				if ( ($arrValues[ $i ] == false)
						OR is_null($arrValues[ $i ])
						OR !isset($arrValues[ $i ]) ){
					$eval	= false;
					$this->mPrimate		= $arrValues[ $i ];
					$this->mMessages	.= "ERR_V1\tError en el Valor $i de $lim valores\r\n";
				}
			}
		} else {
			$eval	= false;
			$this->mMessages	.= "ERR_V2\tEl Valor $arrValues no es un array\r\n";
		}
		return $eval;
	}
	function get(){
		return $this->mPrimate;
	}
	function getMessages($put = OUT_TXT){
		$xH		= new cHObject();
		return $xH->Out($this->mMessages, $put);
	}
	/**
	 * Devuelve un Tipo de Operacion a partir de una Tipo de Pago de recibos
	 * @param string $TipoDePago
	 * @return	string Tipo de Operacion
	 * @deprecated @since 2014.09.09
	 */
	function getTipoOperacionByTipoPago($TipoDePago = "efectivo"){
		$arrOps		= $this->mArrOps;
		return $arrOps[$TipoDePago];
	}
	/**
	 * Compara dos Numero y devuelve si son similares segun una tolerancia de saldo
	 * @param float $valorReferencia
	 * @param float $valor
	 * @return boolean
	 */
	function getEvalNumeroSimilar($valorReferencia, $valor){
		$similar 			= false;
		$tolerancia			= TOLERANCIA_SALDOS;
		
		$valor				= $this->cFloat($valor);
		$valorReferencia	= $this->cFloat( $valorReferencia );
		
		if ( $valor == $valorReferencia ){
			$similar	= true;
		}
		//si 0 >  -0.99 AND 0 <= 0.99
		if ( ($valor >= ($valorReferencia - $tolerancia) ) AND ($valor <= ($valorReferencia + $tolerancia) ) ){
			$similar	= true;
		}
		return $similar;
	}
	function cBool($valor = false){
		$rs		= false; //$valor;
		if($valor === true OR $valor === false){
			$rs 	= (bool) $valor;
		} else {
			$aEq	= array("1" => true, 1 => true, "TRUE" => true, "VERDADERO" => true, "ON" => true, "YES" => true, "SI" => true, true => true);
			$rs		= ( isset($aEq[strtoupper( (string) $valor)]) ) ? true : false;
			//setLog(strtoupper($valor));
		}
		return $rs;
	}
	function getCSV($strVal){
		$strVal	= str_replace(",", "", $strVal);
		$strVal	= str_replace(".", "", $strVal);
		
		$strVal	= trim($strVal);
		return $strVal;
	}
	/**
	 * Reemplaza todos los acentos por sus equivalentes sin ellos
	 *
	 * @param $string
	 *  string la cadena a sanear
	 *
	 * @return $string
	 *  string saneada
	 */
	function getStringASCII($string)
	{
	
	    $string = trim($string);
	
	    $string = str_replace(
		array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä', 'á'),
		array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'a'),
		$string
	    );
	
	    $string = str_replace(
		array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
		array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
		$string
	    );
	
	    $string = str_replace(
		array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
		array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
		$string
	    );
	
	    $string = str_replace(
		array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
		array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
		$string
	    );
	
	    $string = str_replace(
		array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
		array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
		$string
	    );
	
	    $string = str_replace(
		array('ñ', 'Ñ', 'ç', 'Ç'),
		array('n', 'N', 'c', 'C',),
		$string
	    );
	
	    //Esta parte se encarga de eliminar cualquier caracter extraño
	    /*$string = str_replace(
		array("\\", "¨", "º", "-", "~",
		     "#", "@", "|", "!", "\"",
		     "·", "$", "%", "&", "/",
		     "(", ")", "?", "'", "¡",
		     "¿", "[", "^", "`", "]",
		     "+", "}", "{", "¨", "´",
		     ">", "< ", ";", ",", ":",
		     ".", " "),
		'',
		$string
	    );*/
	    return $string;
	}
	function cMail($mail = false){
		$mail		= ($mail == false) ? $this->mPrimate : $mail;
		$mail		= (filter_var($mail, FILTER_VALIDATE_EMAIL)) ? $mail : "";
		return $mail;
	}
	function setToUTF8(){	$this->mForceUTF		= true;	}
	function setForceMayus(){ $this->mForceMayus = true;}
	function setForceClean(){ $this->mForceClean = true; }
	function cleanString($cadena, $otros = false){
		$cleanArr	= array('/\s\s+/', '/(\")/', '[\\\\]', '/(\')/');
		if(is_array($otros)){
			$cleanArr	= array_merge($cleanArr,$otros);
		}
		$cadena 		= preg_replace($cleanArr, ' ', $cadena); //dob
		return $cadena;
	}	
}

function getMemoriaLibre($megas = false){
	$multiplo	= 1;
	$iniVal		= strtoupper(ini_get("memory_limit"));
	if(strpos($iniVal, "M") !== false){	$multiplo = (1024 * 1000); }
	if(strpos($iniVal, "K") !== false){	$multiplo = 1024; }
	$iniVal		= preg_replace("/[^0-9]/", "", $iniVal);
	$v			= ($iniVal	* $multiplo) - memory_get_peak_usage();
	if($megas == true){
		$v = round( (($v / 1024) /1000),0 );
	}
	return $v;
}

/**
 * 
 * Dibuja funciones Javascript para Operaciones Comunes
 * @author Balam Gonzalez Luis Humberto
 *
 */
class jsBasicForm {
	private $mType 				= iDE_CREDITO;
	private $mForm				= null;
	//Funciones Incluidas
	public  $mIncludeCalendar	= false;
	public  $mIncludeSocio		= true;
	public	$mIncludeCaptacion	= true;
	public	$mIncludeCommon		= true;
	public 	$mIncludeCreditos	= true;
	public 	$mIncludeGrupos		= true;
	public 	$mIncludeRecibos	= true;

	public  $mSubPath			= "";
	protected $mTypeCaptacion	= 0;
	protected $mSubproducto		= "all";
	private $IncJQuery			= false;
	private $strJQueryIncs		= "";
	private $mLoadVals			= true;

	private $mArrCalendarJs		= array();
	protected $mInputs			= array();
	
	protected $mWidth			= 600;
	protected $mHeigth			= 480;

	
	private $mFiltroCreditos	= "todos";
	
	/**
	 * Dibuja funciones Javascript para Operaciones Comunes
	 * @param string $form		Nombre del Formulario
	 * @param integer $type		Tipo de Operaciones JavaScript [iDE_CREDITO]
	 * @param string $subPath	Path por defecto,  . o ..
	 */
	function __construct($form, $type = iDE_CREDITO, $subPath = "."){
		$this->mForm 												= $form;
		$this->mSubPath												= $subPath;
		$this->mInputs["descripcion_de_la_solicitud"]["name"]		= "nombresolicitud";
		$this->mInputs["descripcion_de_la_cuenta"]["name"]		= "nombrecuenta";
		$this->mInputs["descripcion_del_socio"]["name"]			= "nombresocio";

		$this->mInputs["codigo_de_solicitud"]["name"]			= "idsolicitud";
		$this->mInputs["codigo_de_solicitud"]["id"]			= "idsolicitud";

		$this->mInputs["codigo_de_socio"]["name"]			= "idsocio";
		$this->mInputs["codigo_de_socio"]["id"]				= "idsocio";

		$this->mInputs["codigo_de_recibo"]["name"]			= "idrecibo";
		$this->mInputs["codigo_de_recibo"]["id"]			= "idrecibo";

		$this->mInputs["codigo_de_grupo"]["name"]			= "idgrupo";
		$this->mInputs["codigo_de_grupo"]["id"]				= "idgrupo";

		$this->mInputs["codigo_de_cuenta"]["name"]			= "idcuenta";
		$this->mInputs["codigo_de_cuenta"]["id"]			= "idcuenta";
		
		
		switch ( $type ){
			case iDE_CAPTACION:
				$this->mIncludeCaptacion	= true;
				$this->mIncludeCreditos		= false;
				$this->mIncludeCommon		= true;
			break;
			case iDE_CINVERSION:
				$this->mIncludeCaptacion	= true;
				$this->mIncludeCreditos		= false;
				$this->mTypeCaptacion		= CAPTACION_TIPO_PLAZO;
				$this->mIncludeCommon		= true;
			break;		
			case iDE_CREDITO:
				$this->mIncludeCreditos		= true;
				$this->mIncludeCaptacion	= false;
				$this->mIncludeCommon		= true;
				break;
			case iDE_OPERACION:
				$this->mIncludeCreditos		= true;
				$this->mIncludeCaptacion	= true;
				$this->mIncludeCommon		= true;
				break;
		}
		$this->mIncludeSocio	= true;
	}
	/**
	 * Personaliza una variable de controles Input
	 * @param string $input
	 * @param string $property
	 * @param string $value
	 */
	function setInputProp($input, $property, $value){ $this->mInputs[$input][$property] = $value; }
	function setConCaptacion($w = true){ $this->mIncludeCaptacion	= $w; }
	function setIncludeCaptacion($w = true){ $this->mIncludeCaptacion	= $w; }	
	function setConCreditos($w = true){ $this->mIncludeCreditos	= $w; }
	function setIncludeCreditos($w = true){ $this->mIncludeCreditos	= $w; }	
	function setConGrupos($w = true){ $this->mIncludeGrupos	= $w; }
	function setIncludeGrupos($w = true){ $this->mIncludeGrupos	= $w; }	
	function setTypeCaptacion($type = CAPTACION_TIPO_PLAZO){ $this->mTypeCaptacion	= $type; }
	function setSubproducto($type = 99){ $this->mSubproducto		= $type; }
	function setConRecibos($w = true){ $this->mIncludeRecibos	= $w; }
	function setConCommon($w = true){ $this->mIncludeCommon	= $w; }
	function setNCtrlGrupo($control){	$this->mInputs["codigo_de_grupo"]["name"]	= $control;	}
	function setIncludeCalendar($toInclude = true){	$this->mIncludeCalendar = $toInclude;	}
	function setConSocios($w = true){	$this->mIncludeSocio	= $w;	}
	function setIncludeOnlyCommons(){
		$this->mIncludeCalendar		= false;
		$this->mIncludeCaptacion	= false;
		$this->mIncludeCommon		= true;
		$this->mIncludeCreditos		= false;
		$this->mIncludeGrupos		= false;
		$this->mIncludeRecibos		= false;
		$this->mIncludeSocio		= false;
	}
	function setNameForm($name){ $this->mForm = $name; }
	function setNombreCtrlRecibo($nombre){ $this->mInputs["codigo_de_recibo"]["name"] = $nombre;	}
	function setLoadDefaults($load = false){ $this->mLoadVals = $load; }
	function setEstatusDeCreditos($estatus){ $this->mFiltroCreditos	= $estatus;	}
	/**
	 * Agrega la Opcion del Calendario al Javascript
	 * @param string $id_control		ID en el Documento XHTML
	 * @param boolean $type
	 * @param string $cmdButton 		Nombre del Boton asociado
	 */
	function addSetupCalendar($id_control, $type = false, $cmdButton = "cmdCalendar", $format = "%Y-%m-%d"){
		if ($type == "multiple"){
			$this->mArrCalendarJs[] = "
	//Dia
	Calendar.setup({
        inputField     :    \"ideldia$id_control\",
        ifFormat       :    \"%d\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});
	//Mes
	Calendar.setup({
        inputField     :    \"idelmes$id_control\",	// id of the input field
        ifFormat       :    \"%m\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});
	//Anno
	Calendar.setup({
        inputField     :    \"idelanno$id_control\",	// id of the input field
        ifFormat       :    \"%Y\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});
	";
		} else {
			$this->mArrCalendarJs[] = "
	Calendar.setup({
        inputField     :    \"$id_control\",
        ifFormat       :    \"$format\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});";
		}
	}
	/*			<link rel=\"stylesheet\" href=\"" . $this->mSubPath . "./css/jquery.qtip.css\" media=\"all\" />
			
			<script  src=\"" . $this->mSubPath . "./js/jquery/jquery.js\"></script><script  src=\"" . $this->mSubPath . "./js/jquery/jquery.qtip.min.js\"></script>*/
	function setIncludeJQuery($ask = true){
		if ($ask == true ){
			$this->strJQueryIncs	= "<link rel=\"stylesheet\" href=\"" . $this->mSubPath . "./css/jquery-ui/jquery-ui.css\" media=\"all\" />	<script  src=\"" . $this->mSubPath . "./js/jquery/jquery.ui.js\"></script>";
		}
		return $this->strJQueryIncs;
	}

	function setDrawCalendar($cmdButton = "cmdCalendar"){
		return "<img style=\"width: 16px; height: 16px;\" alt=\"\" src=\"../images/common/calendar.gif\" align='middle' id=\"$cmdButton\" alt=\"Muestra el Calendario\" />";

	}
	function getJsSocios(){
//================================	SOCIOS
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio		= $this->mInputs["codigo_de_socio"]["name"];
		$idcuenta		= $this->mInputs["codigo_de_cuenta"]["name"];
		$subtipo		= ($this->mSubproducto == "all") ? 0 : setNoMenorQueCero($this->mSubproducto);
		$jsSocios		= "
/** Funciones de Compatibilidad */
function envsoc(){ jsSetNombreSocio(); }

/* funcion que retorna el nombre de socio */
function jsSetNombreSocio(mObtenerI){
	var tipocuenta		= jsTypeCaptacion;
	mObtenerI		= (typeof mObtenerI == \"undefined\") ? true : mObtenerI;
	if (jsWorkForm.$idsocio) {
		var mSocio	= entero(jsWorkForm.$idsocio.value);
		if( mSocio > 0 ) {
			jsrsExecute(jsrGeneralCommon, jsReturnSocio, \"Common_695bad33e1f2af343f99c6a4ceb9d045\", jsWorkForm.$idsocio.value);
			if(mObtenerI == true && mLoadDefs == true){
				/** Busca la Solicitud de Credito con Prioridad */
				if(jsWorkForm.$idsolicitud) {
					if(typeof jsReturnPrioriCredit != \"undefined\"){
						jsrsExecute(jsrCreditsCommon, jsReturnPrioriCredit, 'Common_86d8b5015acb366cec42bf1556d8258a', jsWorkForm.$idsocio.value + vSEPARATOR + mFiltroCred);
					}
				}
				/** Busca la Cuenta de captacion con Prioridad */
				if(jsWorkForm.$idcuenta){
					var mOr		= mSocio  + '|' + tipocuenta + '|" . $this->mSubproducto . "';
					var xCG	= new CaptGen(); xCG.getPrincipal({ persona: mSocio, tipo: tipocuenta, subtipo : $subtipo, control : jsWorkForm.$idcuenta.id });
				}
			}
		} else { goSocio_(); }
	}
}
/** Retorna El Numero de Socio */
function jsReturnSocio(mRetNombre){
	var xRetNombre	= mRetNombre
		if( (xRetNombre == \"" . MSG_NO_PARAM_VALID . "\") || (xRetNombre == '0' ) ){
					goSocio_();
		} else {
			try{
				jsWorkForm." . $this->mInputs["descripcion_del_socio"]["name"] . ".value = xRetNombre;
			} catch (err) {	}
		}
}
function jsPersonaRegresarCaptura(){ jsWorkForm.$idsocio.value = " . DEFAULT_SOCIO . "; jsWorkForm.$idsocio.focus(); jsWorkForm.$idsocio.select(); }
function goSocio_(){
	jsPersonaRegresarCaptura();
	var isoc 	= jsWorkForm.$idsocio.value;
	var pfSoc 	= \"../utils/frmbuscarsocio.php?i=\";
	try {
		var xurl = pfSoc + isoc + \"&f=" . $this->mForm . "\";
		mGlo.w({ url: xurl, h: 600, w : 800, tiny : true});
	} catch (e){}
}";
		return $jsSocios;		
	}
	function getJsCreditos(){
		//================================	CREDITOS
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio		= $this->mInputs["codigo_de_socio"]["name"];
		$jsCreditos		= "
		var isCredit		= (typeof jsWorkForm.$idsolicitud != \"undefined\") ? true : false;
		var mCredit		= (typeof jsWorkForm.$idsolicitud != \"undefined\") ? jsWorkForm.$idsolicitud : null;
		var MG			= (typeof Gen  != \"undefined\") ? new Gen(): {};
		var MCRED		= (typeof CredGen  != \"undefined\") ? new CredGen(): null;
    /** Funciones de compatibilidad */
	function envsol(){ jsGetDescCredito(); }
	/** Retorna la Descripcion de la Solicitud	*/
	function jsGetDescCredito(){
		esGuardable=false;
		if( mCredit != null){
			if(MCRED == null){
				if( entero(mCredit.value) > 0 ) { jsrsExecute(jsrCreditsCommon, jsReturnDescCredito,'Common_b05dfbfaf8125673c6dc350143777ee1', mCredit.value); }
			} else {
				if( entero(mCredit.value) > 0 ) { MCRED.getDescripcion(mCredit.value, '$nombresolicitud'); }
			}
		}
	}
	function jsReturnPrioriCredit(idsolicitud){ if(isCredit == true){jsWorkForm.$idsolicitud.value=idsolicitud; setTimeout(\"jsGetDescCredito()\", 1000); }	}
	/** Returna una Descripcion del Credito */
	function jsReturnDescCredito(stringDescription){
		var mDescription = stringDescription;
		if (mDescription == '" . MSG_NO_PARAM_VALID . "'||$.trim(mDescription) == '') {
			jsWorkForm.$idsocio.focus();
			//var siBuscar = confirm(\"EL CREDITO SOLICITADO NO EXISTE \\n O ESTA INACTIVO. DESEA BUSCARLO?\");
			//if(siBuscar){ goCredit_(); } else { jsCredRegresarCaptura(); }
		} else {
			jsWorkForm.$nombresolicitud.value = mDescription; esGuardable=true;
		}
	}
	function jsCredRegresarCaptura(){
		jsWorkForm.$nombresolicitud.focus(); jsWorkForm.$nombresolicitud.select(); 
	}
	function goCredit_(){
		var isoc 	= jsWorkForm.$idsocio.value;
		var pfcred 	= \"../utils/frmscreditos_.php?i=\";
		var xurl	= pfcred + isoc + \"&f=" . $this->mForm . "&tipo=\" + mFiltroCred;
		mGlo.w({ url: xurl, h: 600, w : 800, tiny: true});
	}
	function envparc() {
		if (jsWorkForm.$idsolicitud) {
			if( entero(jsWorkForm.$idsolicitud.value) > 0){
				var misol = jsWorkForm.$idsolicitud.value;
				jsrsExecute(jsrFile, darparc,'damecredito', misol + ' 27');
			}
		}
	}
	function darparc(laparc)  {
		var uparc = parseInt(laparc)+1;
		if (jsWorkForm.idparcialidad){
			jsWorkForm.idparcialidad.value = uparc;
		}
	}

	function goLetra_(){
		var isoc 	= jsWorkForm." . $this->mInputs["codigo_de_solicitud"]["name"] . ".value;
		var urlLetra 	= \"../utils/frmletras.php?i=\" + isoc + \"&f=" . $this->mForm . "\";
		mGlo.w({ url: urlLetra, tiny: true});
	}
	";
		return $jsCreditos;
	}
	function getJsRecibos(){
		$jsRecibos		= "
			function goRecibos_(){
				var iRec 	= jsWorkForm." . $this->mInputs["codigo_de_recibo"]["name"] . ".value;
				var pfRec 	= \"../utils/frmbuscarrecibos.php?i=\";
				mGlo.w({ url: pfRec + iRec + \"&f=" . $this->mForm . "\" + \"&c=" . $this->mInputs["codigo_de_recibo"]["name"] . "\", h: 600, w : 800, tiny : true});
			}";
		return $jsRecibos;
	}
	function get(){ return $this->show(true); }
	function show($Devolver = false){
		$token					= SAFE_VERSION . SAFE_REVISION;
		$jsCalendarBody		= "";
		$jsJQueryUI			= $this->strJQueryIncs;
		$jsCalendarIncludes	= "";
		$jsLoadDef			= ($this->mLoadVals == true) ? "true" : "false";
		//$jsCaptacion
		$jsCalendarIncludes = "	<link rel=\"stylesheet\"  media=\"all\" href=\"" . $this->mSubPath . "./js/jscalendar/calendar-green.css\" title=\"green\" />
								<script  src=\"" . $this->mSubPath . "./js/jscalendar/calendar.js\"></script>
								<script  src=\"" . $this->mSubPath . "./js/jscalendar/lang/calendar-es.js\"></script>
								<script  src=\"" . $this->mSubPath . "./js/jscalendar/calendar-setup.js\"></script>";
		$jsMD5Include		= "<script  src='" . $this->mSubPath . "./js/md5.js'></script>";
		/**
		 * Include Segmentados
		 */
		$jsRecibos			= ($this->mIncludeRecibos == false) ? "" : $this->getJsRecibos();
		$jsSocios			= ($this->mIncludeSocio == false ) ? "" : $this->getJsSocios();
		$jsCreditos			= ($this->mIncludeCreditos == false) ? "" : $this->getJsCreditos();
		$jsCaptacion		= ($this->mIncludeCaptacion == false) ? "" : $this->getJsCaptacion();
		$jsCommon			= ($this->mIncludeCommon == false) ? "" : $this->getJsCommon();
		$jsGrupos			= ($this->mIncludeGrupos == false ) ? "" : $this->getJsGrupos();
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio			= $this->mInputs["codigo_de_socio"]["name"];
		$idcuenta			= $this->mInputs["codigo_de_cuenta"]["name"];
		$nombrecuenta		= $this->mInputs["descripcion_de_la_cuenta"]["name"];
		$claveSocio		= getPersonaEnSession();
		foreach ($this->mArrCalendarJs AS $key=>$value){
			$jsCalendarBody 		.= $value;
		}
		if ($this->mIncludeCalendar == false){
			$jsCalendarIncludes	= "";
			$jsCalendarBody		= "";
		}
//================================	PRINCIPAL
		$js = "
			$jsCalendarIncludes
			$jsJQueryUI
			<script src='" . $this->mSubPath . "./js/jsrsClient.js'></script>
			<!-- <script src='" . $this->mSubPath . "./js/general.js?$token'></script> -->
			<script>
			var jsrFile 				= \"" . $this->mSubPath . "./clsfunctions.inc.php\";
			var jsWorkForm				= document." . $this->mForm . ";
			var jsrCreditsCommon		= \"" . $this->mSubPath . "./js/creditos.common.js.php\";
			var jsrCaptacionCommon		= \"" . $this->mSubPath . "./js/captacion.common.js.php\";
			var jsrGeneralCommon		= \"" . $this->mSubPath . "./js/general.common.js.php\";
			var jsrSeguimientoCommon	= \"" . $this->mSubPath . "./js/seguimiento.common.js.php\";
			
			var jsTypeCaptacion			= " . $this->mTypeCaptacion . ";
			var setToGo					= true;
			var mInputsCheck			= new Array();
			var mFiltroCred			= \"" . $this->mFiltroCreditos . "\";
			var vSEPARATOR			= \"" . STD_LITERAL_DIVISOR . "\";
			var mLoadDefs			= $jsLoadDef;
			var mGlo			= new Gen();
			var esGuardable			= false;
			var autoEjecutar		= true;
			var enBusqueda			= false;
			function frmSubmit( evaluate ){
				//Valida que los Campos
				evaluate = (typeof evaluate != \"undefined\" ) ? evaluate : false;
				if ( evaluate == false ){
					setToGo = jsEvaluarFormulario(false);
					if(setToGo == false){
						alert(\"Su Formulario contiene errores\");
					} else {
						var mGoSubmit	= confirm(\"Quiere Guardar los Datos Capturados?\");
						if( mGoSubmit == false ){
							setToGo = false;
						} else {
							jsWorkForm.submit();
						}
					}
				} else {
					jsWorkForm.submit();
				}
			}
			$jsSocios
			$jsCreditos
			$jsGrupos
			$jsCommon
			$jsRecibos
			$jsCaptacion
			$jsCalendarBody
			function out(msg){ if(typeof msg != \"undefined\"){ console.log(msg); }	}
			function jsLoadNombreValores(){
				if(autoEjecutar == true){
					if(jsWorkForm.$idsocio){
						if($.trim(jsWorkForm.$idsocio.value) == \"\"){ jsWorkForm.$idsocio.value = $claveSocio; }
						jsWorkForm.$idsocio.focus();
						if( entero(jsWorkForm.$idsocio.value) > 0 ){ jsSetNombreSocio(); }
					}
					if(jsWorkForm.$idsolicitud){
						if( entero(jsWorkForm.$idsolicitud.value) > 0 ){ jsGetDescCredito(); }
					}
				}
			}
			/*function jsEvaluarSalida(evt){ if(evt.id =='$idcuenta'){ envcta(); } }*/
			jsLoadNombreValores();
			</script>";

		if( $Devolver == false){
			echo $js;
		} else {
			return $js;
		}
	}
	function getJsGrupos(){
//================================	GRUPOS
	$jsGrupos		= "/** FUNCION QUE RETORNA EL NOMBRE DEL GPO SOLIDARIO */
	function envgpo() {
		var idgpo = jsWorkForm." . $this->mInputs["codigo_de_grupo"]["name"] . ".value;
		jsrsExecute(jsrFile, jsGetNombreGrupo,'mostrargrupo', idgpo + ' 1');
	}
	function jsGetNombreGrupo(nombredev) {
		if(jsWorkForm.nombregrupo){ jsWorkForm.nombregrupo.value = nombredev;  }
	}
	function goGrupos_(){
		var iGrp 	= jsWorkForm." . $this->mInputs["codigo_de_grupo"]["name"] . ".value;
		var pfGrp 	= \"../utils/frmsgrupos.php?i=\";
		frmGrp 	= window.open(pfGrp + iGrp + \"&f=" . $this->mForm . "\", \"\", \"width=600,height=600,scrollbars,dependent=yes\");
		frmGrp.focus();
	}
	";
	return $jsGrupos;		
	}
	function getJsCaptacion(){
		$markSubproducto	= "";
		if ( $this->mSubproducto != "all"){
			$markSubproducto	= "&s=" .$this->mSubproducto;
		}
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio			= $this->mInputs["codigo_de_socio"]["name"];
		$idcuenta			= $this->mInputs["codigo_de_cuenta"]["name"];
		$nombrecuenta		= $this->mInputs["descripcion_de_la_cuenta"]["name"];
		
		$jsCaptacion	= "
	//.- FUNCION OBTIENE DETALLES DE LA CUENTA DE CAPTACION
	/** Funcion de Compatibilidad */
	function envcta(iTipo) {
		vTipoC	= (typeof iTipo != \"undefined\") ? iTipo : jsTypeCaptacion;
		jsGetCuenta(iTipo);
	}
	function jsGetCuenta(inttipo) {
		if(jsWorkForm.$idcuenta){
			var lacta = jsWorkForm.$idcuenta.value;
				if (lacta!='' || lacta!=NaN || lacta!=0) {
					jsrsExecute(jsrCaptacionCommon, jsSetCuenta,'Common_82cbe75762e2714baaf92926f0d26d6b', lacta);
				}
		}
	}
	/** Obtiene una Descripcion de la Cuenta */
	function jsSetCuenta(depcta)  {
		var ccta = depcta;
		jsWorkForm.$nombrecuenta.value = ccta;
	}
	function jsReturnPrioriCaptacion(escta) {
		if (escta!='' || escta!=NaN || escta!=0) {
			var micta = escta;
			if(jsWorkForm.$idcuenta){
				jsWorkForm.$idcuenta.value = micta;
			}
		}
	}

	function goCuentas_(tipoc){
		var vTipoC	= \"\";
		if(typeof tipoc == 'undefined'){
			if(jsTypeCaptacion == 0){ } else { vTipoC	= \"&a=\" + jsTypeCaptacion; }
		} else { vTipoC	= \"&a=\" + tipoc; }
		var isoc 	= jsWorkForm.$idsocio.value;
		var urlcap 	= \"../utils/frmcuentas_.php?i=\" + isoc + \"&c=$idcuenta" . "$markSubproducto&f=" . $this->mForm . "\" + vTipoC;
		console.log(urlcap);
		mGlo.w({ url: urlcap, tiny: true});
	}";
		return $jsCaptacion;		
	}
	function getJsCommon(){
		$EventOnLoad = " jsResizeWindow(); ";
		$jsCommon		= "
	// funcion que checa que el valor no sea cero
	function chkmonto(eValue){ return isNumber(eValue); }
	function notnan(isthis){ return isNotEmpty(isthis); }
	function muestralo(id_e) {
		var mist_s = document.getElementById(id_e);
		mist_s.style.visibility='visible';
	}
	function ocultalo(id_e) { var mist_e = document.getElementById(id_e); mist_e.style.visibility='hidden';	}
	function msgbox(string_alert) { alert (string_alert);	}

	function cierrame(){ window.close(); }
	/** function que cambia una propiedad de un elemento */
	function jsChangeProperty(id, prop, val){
		document.getElementById(id).removeAttribute(prop);
		document.getElementById(id).setAttribute(prop, val);
	}
	function jsRestarFechas(date1, date2) {
	    var DSTAdjuste 	= 0;
	    // ------------------------------------
	    oneMinute 		= 1000 * 60;
	    var oneDay 		= oneMinute * 60 * 24;
	    // ------------------------------------
	    date1.setHours(0);
	    date1.setMinutes(0);
	    date1.setSeconds(0);
	
	    date2.setHours(0);
	    date2.setMinutes(0);
	    date2.setSeconds(0);
	    // ------------------------------------
	    if (date2 > date1) {
	        DSTAdjuste =
	            (date2.getTimezoneOffset() - date1.getTimezoneOffset()) * oneMinute;
	    } else {
	        DSTAdjuste =
	            (date1.getTimezoneOffset() - date2.getTimezoneOffset()) * oneMinute;
	    }
	    var diff = Math.abs(	date2.getTime() - date1.getTime()	) - DSTAdjuste;
	    return Math.ceil(diff/oneDay);
	}
	function jsSumarDias(vFecha, days){
	    var mDays   = parseInt(days);
	    var vFecha	= new String(vFecha);
	    var sDays	= 86400000 * mDays;
	    var sDate   = vFecha.split('-');
	    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );
	
	    var vDate	= varDate.getTime()+sDays;
		varDate.setTime( vDate );
		
	    var mMonth  = varDate.getMonth()+1;
	    var mDate	= varDate.getDate()+1;
	    if (mMonth == 0){
	        alert('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
	    }
		return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
	}
	function jsRestarDias(vFecha, days){
		
	    var mDays   = new Number(days);
	    var vFecha	= new String(vFecha);
	    var sDays	= 86400000 * mDays;
	    var sDate   = vFecha.split('-');
	    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );
	
	    var vDate	= varDate.getTime()-sDays;
	
		varDate.setTime(vDate);
	    var mMonth  = varDate.getMonth()+1;
	    var mDate	= varDate.getDate()+1;
	    
	    if (mMonth == 0){
	        alert('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
	    }
		return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
	}
	function setCheckForm(vFrm){ return jsEvaluarFormulario(); }
	function jsEvaluarFormulario(enviar){
		vFrm				= jsWorkForm;
		var isLims 			= vFrm.elements.length - 1;
		enviar				= (typeof enviar == 'undefined') ? true : enviar;
		  	
			setToGo			= true;
	  		for(i=0; i<=isLims; i++){
				var elem	= vFrm.elements[i];
				var mTyp 	= elem.getAttribute(\"type\");
				var mCls	= elem.getAttribute(\"class\");

				if ( (mTyp == \"text\" || mTyp == \"textarea\") ){
					/* Validar si no esta vacio */

					if ( /(req)/.test(mCls) ){
							setToGo = isNotEmpty(elem);
					}
					//validar que los numeros sean numeros , siempre que no este vacio
					if ( /(mny)/.test(mCls) ){
							setToGo = isNumber(elem);
					}
					if ( (setToGo == false) && (mTyp!=\"hidden\") ){
							elem.focus();
							break;
					}
				}//eval
	  		}
		if( setToGo == true&& enviar == true ){
			var mGoSubmit	= confirm(\"Quiere Guardar los Datos Capturados?\");
			if( mGoSubmit == false ){
				setToGo = false;
			} else {
				vFrm.submit();
			}
		}	
		return setToGo;
	}
	
	function isLenX(elem, mLen) {
		var str 	= elem.value;
		var sucess	= true;
		var mTit	= elem.getAttribute(\"title\");
	    var re 		= /\b.{mLen}\b/;
	    if (!str.match(re)) {
	        alert(\"[ERROR]El Campo no tiene la Numero de Entradas Aceptadas.\");
	        sucess	= false;
	    } else {
	        sucess 	= true;
	    }
	    return sucess;
	}
	
	// validates that the field value string has one or more characters in it
	function isNotEmpty(elem) {
	    var str 	= elem.value;
	    var mTit	= elem.getAttribute(\"title\");
	    var sucess	= true;
	    if( str == null || str.length == 0 || /^\s+$/.test(str) ) {
	        alert(\"[ERROR]El Valor de [\" + mTit + \"] no debe quedar vacio\");
	        sucess	= false;
	    } else {
	        sucess	= true;
	    }
	    return sucess;
	}
	
	//validates that the entry is a positive or negative number
	function isNumber(elem) {
	    var str 	= elem.value;
	    var sucess	= true;
	    var mTit	= elem.getAttribute(\"title\");
    	var re 		= /^[-]?\d*\.?\d*$/;
    	str 		= str.toString( );
    	if (!str.match(re)) {
	        alert(\"[ERROR]El Valor de [\" + mTit + \"] debe ser un Numero\");
	        sucess	= false;
	    }
	    return sucess;
	}
	

	

	function jsResizeWindow(){
			top.resizeTo(" . $this->mWidth . "," . $this->mHeigth . ");	

	}	
	function jsRoundPesos(mCantidad){
		var mStrCantidad	= new String(mCantidad);
		var rF = new RegExp(\",\" , \"g\");
		var rF2 = new RegExp(\"$\" , \"g\");
		var rF3 = new RegExp(\" \" , \"g\");
	
		mStrCantidad = mStrCantidad.replace(rF, \"\");
		mStrCantidad = mStrCantidad.replace(rF2, \"\");
		mStrCantidad = mStrCantidad.replace(rF3, \"\");
		mStrCantidad = mStrCantidad.replace(\"$\", \"\");
	
			mStrCantidad	+= \".00\";
		var arrCantidad		= mStrCantidad.split(\".\");
	
		return arrCantidad[0] + \".\" + arrCantidad[1];
	}

	function jsInitComponents(){
	$EventOnLoad
	}
	";
		return $jsCommon;		
	}
}

class cTableStructure{
	public $ACTUALIZAR			= 1;
	public $AGREGAR				= 2;	
	public $NUEVO				= 0;
	public $OPT_ACT_TITULO		= "actualizar_titulo";
	
	private $mTable		= "";
	
	
	function __construct($nombre_de_la_tabla){
		$this->mTable = $nombre_de_la_tabla;
	}
	function getTitulosInArray(){
		$idx	= $this->mTable . "-general_structure-describe";
		$xCache	= new cCache();
		$arr	= $xCache->get($idx);
		if(!is_array($arr)){
			$sql 	= "SELECT * FROM general_structure WHERE
							tabla = '" . $this->mTable . "'
							ORDER BY tab_num, order_index ASC ";
			$xQL	= new MQL();
			$rs		= $xQL->getDataRecord($sql);
			foreach ($rs as $rw){
				$arr[$rw["campo"]] 	= $rw["titulo"];
			}
			$xCache->set($idx, $arr);
		}
		return $arr;
	}
	function getCampos_InArray(){
		$sql 	= "SELECT * FROM general_structure WHERE
						tabla = '" . $this->mTable . "'
						ORDER BY tab_num, order_index ASC ";
		$lsF	= array();
		$i		= 0;
			$rs = getRecordset($sql);
			while($rw = mysql_fetch_array($rs) ) {
				$lsF[ $rw["campo"] ]	= $rw["campo"]; 
			}
			return $lsF;
	}
	function getCampos_InText(){
		$sql 	= "SELECT * FROM general_structure WHERE
						tabla = '" . $this->mTable . "'
						ORDER BY tab_num, order_index ASC ";
		$lsF	= "";
		$i		= 0;
			$rs = getRecordset($sql); //mysql_query($sql, cnnGeneral());
			while($rw = mysql_fetch_array($rs) ) {
				if ( $i == 0){
					$lsF	.= $rw["campo"];
				} else {
					$lsF	.= ", ". $rw["campo"];
				}
				$i++;
			}
			return $lsF;
	}
	function getInfoField($field){
		$table		= $this->mTable;
		
		$sql 		= "SELECT * FROM general_structure WHERE tabla='$table' AND campo='$field' ORDER BY order_index LIMIT 0,1";
	
		$rs = mysql_query($sql, cnnGeneral());
		if(!$rs) {
			return array();
		} else {
			$filas = mysql_fetch_array($rs);
			return $filas;
		}
	
		@mysql_free_result($rs);
	
	}
	/**
	 * Funcion que crea o actualiza una tabla en el sistema
	 * @param integer $TCond	Tipo de Operacion 0 = nueva Estructura, 1 = Actaulizacion de la estructura
	 * @param array $options COndiciones varias
	 * @return	null
	 **/
	function setStructureTableByDemand( $TCond = 0, $options = array() ){
		$xMQ		= new MQL();
		$EQUIV 		= $xMQ->getTipos();
		
		//$TCond 1 = Actualizar, 0 = Nuevo
		$NTable		= $this->mTable;
		/**$NTable,
			 * Crea la Estructura de una Tabla Determinada
			 */
		$msg	= "";
			//Elimna los registros anteriores
			$sql_d_reg = "DELETE FROM general_structure WHERE tabla='$NTable'";
			if($TCond == $this->NUEVO){
				$xMQ->setRawQuery($sql_d_reg);
			}
	
			//ahora a grabar
				$sql_fields = "SHOW FIELDS IN $NTable";
				$rs_fields 	= $xMQ->getRecordset($sql_fields);
				
				$i			= 0;
				$goKey		= false;
				while($rowf = $rs_fields->fetch_array($rs_fields)){
					$valor 				= $rowf[4];
					$titulo				= ucfirst(str_replace("_", " ", $rowf[0]));
					$ctrl 				= "text";
	
					//$hay_div = strpos($rowf[1], "(");
					$atype 				= explode(" ", $rowf[1]);
					$atype 				= $atype[0];
					$atype 				= str_replace(")", "", $atype);
					$atype 				= str_replace("(", "@", $atype);
					$iType 				= explode("@", $atype);
					$field_type			= isset($iType[0]) ? $iType[0] : "varchar";
					$field_long 		= isset($iType[1]) ? $iType[1] : 0;
					if(!$field_long){
						$field_long 	= 0;
					}
	
					switch ($field_type){
						case "enum":
							$valor 		= str_replace(",", "", $field_long);
							$valor 		= str_replace("''", "'", $valor);
							$valor 		= str_replace("'", "|", $valor);
							$field_long = 0;
							$ctrl 		= "select";
							break;
						default:
							//pocsionamiento de float enteros+fracciones + divisor
							if(strpos($field_long, ",") > 0){
								$field_long = explode(",", $field_long);
								$field_long = $field_long[0] + $field_long[0] + 1 ;
							}
							break;
					}
					//si el key es si
					if($rowf[3] == "PRI" AND $goKey == false){
						$valor 		= "primary_key";
						$goKey		= true;
					}
					//if( $EQUIV[strtoupper($field_type)] == MQL_INT||$EQUIV[strtoupper($field_type)] == MQL_FLOAT) { $ctrl = "number"; }
					if($field_long > 75){ $ctrl 		= "textarea"; }
					
					$DExiste		= $xMQ->getDataRow("SELECT COUNT(*) AS 'numero' FROM general_structure WHERE tabla='$NTable' AND campo='$rowf[0]' ");
					$existentes		= $DExiste["numero"];
					$sqlNuevo 		= "INSERT INTO general_structure(tabla, campo,valor,tipo,longitud,titulo,control, order_index) VALUES ('$NTable','$rowf[0]', '$valor', '$field_type', $field_long, '$titulo', '$ctrl', $i)";
					//
					//"SELECT COUNT(0) AS 'idcnt' FROM general_structure WHERE tabla='$NTable' AND campo='$rowf[0]'"
					switch ($TCond){
						case $this->NUEVO:
							
							$xMQ->setRawQuery($sqlNuevo);
							$msg		.= "$NTable\t$rowf[0]\tNUEVO\tAgregando Campo Tipo $field_type, con Valor $valor y tamano $field_long\r\n";
							break;
						case $this->ACTUALIZAR:
							$upTitulo	= ( isset( $options[$this->OPT_ACT_TITULO] ) ) ? ", titulo='$titulo' " : "";
							$upOrden	= "";
							$sqlUpd 	= "UPDATE general_structure
										SET valor='$valor', tipo='$field_type', longitud=$field_long $upTitulo $upOrden WHERE tabla='$NTable' AND campo='$rowf[0]' ";
							if($existentes > 0){
								$xMQ->setRawQuery($sqlUpd);
								$msg	.= "$NTable\t$rowf[0]\tACTUALIZAR\tAgregando Campo Tipo $field_type, con Valor $valor y tamano $field_long\r\n";
							} else {
								$xMQ->setRawQuery($sqlNuevo);
								$msg	.= "$NTable\t$rowf[0]\tNUEVO\tAgregando Campo Tipo $field_type, con Valor $valor y tamano $field_long\r\n";
							}
							break;
						case $this->AGREGAR:
							if($existentes <= 0){
								$xMQ->setRawQuery($sqlNuevo);
								$msg	.= "$NTable\t$rowf[0]\tNUEVO\tAgregando Campo Tipo $field_type, con Valor $valor y tamano $field_long\r\n";
							}
							break;
					}
					//echo "<p class='aviso'>$sql_i_d</p>";
					$i++;
				}
			$rs_fields->free();
			
		return $msg;
	}
	function setActualizar(){
		$this->setStructureTableByDemand($this->AGREGAR);
	}
	function getNumeroDeCampos(){
		$result		= 0;
		$sql	= "SELECT COUNT(*) AS 'registros' FROM general_structure WHERE tabla='" . $this->mTable . "' ";
		$result	= mifila($sql, "registros");
		return $result;
	}
}

/**
 * Operaciones con la Base de Datos
 * @version 1.0.02
 * @package common
 * @subpackage core
 */
class cSAFEData{
	private $mCnn   = false;
	
	function __construct(){
	    //$this->connect();
	}
	function getIDUnico($tipo){
		$tipo	= setNoMenorQueCero($tipo);
		switch ($tipo){
					
		}
	}
	function execQuery($sql){
		$xQL	= new MQL();
		$rs 	= $xQL->setRawQuery($sql);
		unset($xQL);
		return ($rs == false) ? false : true;
	}
	function connect(){
	    $mCnx 	= new mysqli( WORK_HOST , USR_DB, PWD_DB, MY_DB_IN, PORT_HOST);
	    $this->mCnn	= $mCnx;
	    return $mCnx;
	}
	/**
	 * Devuelve un array en los campos 0 y 1, de un catalogo (tablas de tipos)
	 * @param 	string		$tabla_de_tipo	Nombre de la tabla de catalogo
	 * @param 	string		$campo_clave	Nombre del campo que sirve de Key
	 * @param 	string		$campo_valor	Nombre del campo que sirve de value
	 * @return	array		Array de la tabla catalogo en los campos 0, 1
	 */
	function setCatalogoInArray($tabla_de_tipo, $campo_clave = 0, $campo_valor = 1){
		$DArray	= array();
		$sql	= "SELECT * FROM $tabla_de_tipo LIMI 0,100";
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord($sql);
		foreach($rs as $rw){
				$DArray[ $rw[$campo_clave] ] = $rw[$campo_valor];
		}
		unset($rs);
		return $DArray;
	}
	function setPurgueDB(){
		$sqlT	= array();
		$msg	= "";
		$xF		= new cFecha();
		$xQL	= new MQL();
		$sqlT[]	= "DELETE FROM bancos_cuentas WHERE idbancos_cuentas != " . FALLBACK_CUENTA_BANCARIA;
		$sqlT[]	= "DELETE FROM bancos_operaciones ";
		
		$sqlT[]	= "DELETE FROM captacion_cuentas WHERE numero_cuenta!=" . CTA_GLOBAL_CORRIENTE;
		$sqlT[]	= "DELETE FROM captacion_sdpm_historico ";
		$sqlT[]	= "DELETE FROM captacion_firmas";
		$sqlT[]	= "DELETE FROM captacion_sdpm_historico";

		$sqlT[]	= "DELETE FROM contable_catalogo ";
		$sqlT[]	= "DELETE FROM contable_catalogorelacion ";
		$sqlT[]	= "DELETE FROM contable_movimientos ";
		$sqlT[]	= "DELETE FROM contable_polizas ";
		$sqlT[]	= "DELETE FROM contable_saldos";
		$sqlT[]	= "DELETE FROM contable_polizas_proforma ";
		
		$sqlT[]	= "DELETE FROM creditos_reconvenio";
		$sqlT[]	= "DELETE FROM creditos_solicitud WHERE numero_solicitud !=" .  DEFAULT_CREDITO;
		$sqlT[]	= "DELETE FROM creditos_garantias";
		$sqlT[]	= "DELETE FROM creditos_flujoefvo";
		$sqlT[]	= "DELETE FROM creditos_lineas";
		$sqlT[]	= "DELETE FROM creditos_productos_otros_parametros ";
		$sqlT[]	= "DELETE FROM creditos_sdpm_historico";
		$sqlT[]	= "DELETE FROM creditos_periodos "; //WHERE idcreditos_periodos != 
		$sqlT[]	= "DELETE FROM creditos_rechazados ";
		
		$sqlT[]	= "DELETE FROM general_sucursales WHERE codigo_sucursal != \"matriz\" AND codigo_sucursal !='" . getSucursal() . "'  ";
		$sqlT[]	= "DELETE FROM general_log ";
		$sqlT[]	= "DELETE FROM general_tmp ";
		$sqlT[]	= "DELETE FROM general_import ";
		
		$sqlT[]	= "DELETE FROM operaciones_recibos";
		$sqlT[]	= "DELETE FROM operaciones_mvtos";
		$sqlT[]	= "DELETE FROM seguimiento_compromisos";
		$sqlT[]	= "DELETE FROM seguimiento_llamadas ";
		$sqlT[]	= "DELETE FROM seguimiento_notificaciones";
		
		$sqlT[]	= "DELETE FROM socios_aeconomica";
		
		
		
		
		$sqlT[]	= "DELETE FROM socios_aportaciones";
		$sqlT[]	= "DELETE FROM socios_baja";
		$sqlT[]	= "DELETE FROM socios_cajalocal WHERE idsocios_cajalocal != " . getCajaLocal() . " AND sucursal != '" . getSucursal() .  "' " ;
		$sqlT[]	= "DELETE FROM `socios_aeconomica_dependencias`
			WHERE `idsocios_aeconomica_dependencias` !=" . DEFAULT_EMPRESA . "
			AND `idsocios_aeconomica_dependencias` !=" . FALLBACK_CLAVE_EMPRESA ;

		$sqlT[]	= "DELETE FROM socios_general WHERE codigo!=" . DEFAULT_SOCIO . " AND codigo !=" . EACP_ID_DE_PERSONA;
		$sqlT[]	= "DELETE FROM socios_memo";
		$sqlT[]	= "DELETE FROM socios_patrimonio";
		$sqlT[]	= "DELETE FROM socios_relaciones";
		$sqlT[]	= "DELETE FROM socios_vivienda";
		$sqlT[]	= "DELETE FROM socios_grupossolidarios WHERE idsocios_grupossolidarios!=" . DEFAULT_GRUPO;
		$sqlT[]	= "DELETE FROM  `personas_documentacion` ";
		$sqlT[]	= "DELETE FROM  `socios_otros_parametros` ";
		
		$sqlT[]	= "DELETE FROM t_03f996214fba4a1d05a68b18fece8e71 WHERE idusuarios !=99";
		
		$sqlT[]	= "DELETE FROM tesoreria_cajas";
		$sqlT[]	= "DELETE FROM `tesoreria_cajas_movimientos` ";
		
		$sqlT[]	= "DELETE FROM usuarios_web ";
		$sqlT[]	= "DELETE FROM usuarios_web_connected";
		$sqlT[]	= "DELETE FROM `usuarios_web_notas` ";
		
		$sqlT[]	= "DELETE FROM general_tmp";

		
		$sqlT[]	= "DELETE FROM general_folios ";
		$sqlT[]	= "DELETE FROM contable_polizas_proforma ";

		$sqlT[]	= "DELETE FROM contable_centrodecostos WHERE idcontable_centrodecostos !=0 ";
		$sqlT[]	= "DELETE FROM general_log ";

		$sqlT[]	= "UPDATE t_03f996214fba4a1d05a68b18fece8e71 SET f_34023acbff254d34664f94c3e08d836e = getHash('root') WHERE f_28fb96d57b21090705cfdf8bc3445d2a = 'root'"; //*/

		
		//TODO: Actualizar nombre de la tabla
	
		$sqlT[]	= "DELETE FROM `empresas_operaciones` ";
		$sqlT[]	= "DELETE FROM `empresas_cobranza`";
		$sqlT[]	= "DELETE FROM `sistema_programacion_de_avisos` ";
		
		$sqlT[]	= "DELETE FROM `personas_perfil_transaccional` ";
		$sqlT[]	= "DELETE FROM `creditos_rechazados` ";
		
		//AML
		$sqlT[]	= "DELETE FROM  `aml_risk_register`";
		$sqlT[]	= "DELETE FROM  `aml_alerts`";
		
		$sqlT[]	= "DELETE FROM  `personas_documentacion` ";
		$sqlT[]	= "DELETE FROM  `socios_otros_parametros` ";
		
		
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '' WHERE `idgeneral_contratos` = '5' ";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '' WHERE `idgeneral_contratos` = '9' ";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '' WHERE `idgeneral_contratos` = '8' ";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '102' ";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '103' ";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '104' ";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '105' ";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '106'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '107'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '201'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '202'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '203'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '220'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '900'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '2011'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '2012'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '2013'";
		$sqlT[]	= "DELETE FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` = '2014'";
		
		//$sqlT[]	= "DELETE FROM  ";
		$sqlT[]	= "DELETE FROM creditos_otros_datos ";
		$sqlT[]	= "DELETE FROM `aml_perfil_egresos_por_persona` ";
		$sqlT[]	= "DELETE FROM `historial_de_pagos` ";
		$sqlT[]	= "DELETE FROM `operaciones_archivo_de_facturas` ";
		$sqlT[]	= "DELETE FROM `personas_operaciones_recursivas` ";
		$sqlT[]	= "DELETE FROM `personas_relaciones_recursivas` ";
		$sqlT[]	= "DELETE FROM `tesoreria_caja_arqueos` ";
		$sqlT[]	= "DELETE FROM `aml_personas_descartadas`";
		$sqlT[]	= "DELETE FROM `creditos_destino_detallado`";
		$sqlT[]	= "DELETE FROM `programacion_de_avisos`";
		$sqlT[]	= "DELETE FROM `creditos_presupuestos`";
		
		$sqlT[]	= "UPDATE `socios_general` SET `nombrecompleto` = 'REGISTRO_INICIAL_FINANCIERA' WHERE `codigo` = '10000'";
		
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE  `nombre_del_parametro` = 'curp_del_representante_legal'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE  `nombre_del_parametro` = 'nombre_del_presidente_del_consejo_de_vigilancia'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'nombre_del_representante_legal'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'rfc_del_representante_legal'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'domicilio.domicilio_integrado'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'email_de_la_entidad'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'nombre_de_la_entidad'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'registro_ante_la_cnbv'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'registro_casfin'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'registro_patronal_imss'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'rfc_de_la_entidad'";
		
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'email_de_nominas'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'entidad_corto_en_el_sic'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'contrasenna_de_sms_automaticos'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'usuario_de_sms_automaticos'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'email_del_administrador'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'password_de_usuario_ftp' ";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'nombre_de_usuario_ftp' ";
		
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'system_pay_email_register'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'password_del_email_del_administrador'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'contrasenna_de_trabajos_automaticos'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'usuario_de_sms_automaticos'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '00000000' WHERE `nombre_del_parametro` = 'entidad_clave_en_el_sic'";
		
		
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '00000000' WHERE `nombre_del_parametro` = 'domicilio.telefono_principal'";
		$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'https://demo.sipakal.com/' WHERE `nombre_del_parametro` = 'url_de_entidad_transmisora'";
		//$sqlT[]	= " UPDATE `entidad_configuracion` SET `valor_del_parametro` = '00000000' WHERE `nombre_del_parametro` = 'entidad_clave_en_el_sic'";
		
		$sqlT[]	= "INSERT INTO `bancos_entidades` (`idbancos_entidades`, `nombre_de_la_entidad`, `rfc_de_la_entidad`) VALUES ('999', 'BANCO_POR_DEFECTO', 'BAN999999')  ";
		$sqlT[]	= "INSERT INTO `bancos_cuentas` (`idbancos_cuentas`, `descripcion_cuenta`, `fecha_de_apertura`, `estatus_actual`, `consecutivo_actual`, `saldo_actual`, `sucursal`, `entidad_bancaria`) VALUES ('99', 'FALLBACK_CUENTA', '2014-01-01', 'activo', '1', '100000000', 'matriz', '1') ";
		$sqlT[]	= "INSERT INTO `t_03f996214fba4a1d05a68b18fece8e71` (`idusuarios`, `f_28fb96d57b21090705cfdf8bc3445d2a`, `apellidopaterno`, `apellidomaterno`, `puesto`, `periodo_responsable`, `codigo_de_persona`) VALUES ('1', 'USUARIO POR DEFECTO', '', '', 'Usuario por Defecto', '1', '99999')";
		
		
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '' WHERE `idgeneral_contratos` = '801'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '' WHERE `idgeneral_contratos` = '4'";
		$sqlT[]	= "INSERT INTO `contable_centrodecostos` (`idcontable_centrodecostos`, `nombre_centrodecostos`) VALUES ('1', 'POR DEFECTO')";
		$sqlT[]	= "TRUNCATE `tmp_creditos_mensuales_cnivelsalarial`";
		$sqlT[]	= "TRUNCATE `tmp_personas_extranjeras`";
		$sqlT[]	= "TRUNCATE `creditos_plan_de_pagos`";
		//$sqlT[]	= "";
		$sqlT[]	= "TRUNCATE `creditos_productos_otros_parametros`";
		
		$sqlT[]	= "ALTER TABLE `creditos_sdpm_historico` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `operaciones_mvtos` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `general_folios` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `personas_operaciones_recursivas` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `contable_polizas_proforma` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `aml_risk_catalog` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `operaciones_recibos` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `seguimiento_llamadas` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `contable_movimientos` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `contable_polizas` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `empresas_operaciones` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE  `empresas_cobranza` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `socios_vivienda` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `socios_relaciones` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `socios_patrimonio` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `socios_aeconomica` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `seguimiento_llamadas` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `seguimiento_compromisos` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `personas_perfil_transaccional` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `personas_documentacion` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `aml_alerts` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `personas_operaciones_recursivas` AUTO_INCREMENT = 1";
		$sqlT[]	= "ALTER TABLE `usuarios_web_connected` AUTO_INCREMENT = 1 ";
		$sqlT[]	= "INSERT INTO `operaciones_mvtos`(`idoperaciones_mvtos`) VALUES (1)";			//Operacion null
		
		$sqlT[]	= "TRUNCATE creditos_montos";
		$sqlT[]	= "TRUNCATE `tmp_personas_estadisticas`";
		$sqlT[]	= "TRUNCATE `entidad_calificacion`";
		//== 2017 04 11
		$sqlT[]	= "TRUNCATE `aml_riesgo_producto`";
		$sqlT[]	= "TRUNCATE `creditos_datos_originacion`";
		$sqlT[]	= "TRUNCATE `tmp_creds_prox_letras`";
		$sqlT[]	= "TRUNCATE `personas_consulta_lista`";
		$sqlT[]	= "TRUNCATE `tmp_personas_domicilios`";
		$sqlT[]	= "TRUNCATE `tmp_personas_geografia`";
		$sqlT[]	= "TRUNCATE `sistema_eliminados`";
		$sqlT[]	= "TRUNCATE `sistema_eliminados`";
		$sqlT[]	= "TRUNCATE `mercadeo_envios`";
		$sqlT[]	= "TRUNCATE `personas_checklist`";
		$sqlT[]	= "TRUNCATE `entidad_creditos_proyecciones`";
		//$sqlT[]	= "";
		
		//==
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '1003' ";
		$sqlT[]	= "DELETE FROM `general_contratos` WHERE `idgeneral_contratos` = '1005'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '102'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '191'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '20010'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '21'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<p>variable_encabezado_de_reporte</p>\r\n\r\n<h1>CARTA DE ENTREGA</h1>\r\n\r\n<h2 style=\"text-align:right\">BUENO POR <strong>variable_monto_del_recibo</strong></h2>\r\n\r\n<p>RECIBI DE LA SOCIEDAD MERCANTIL , ENTIDAD NO REGULADA, LA CANTIDAD DE <strong>variable_monto_del_recibo</strong>&nbsp; SON : ( <strong>variable_monto_del_recibo_en_letras</strong> ).</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4 style=\"text-align:right\">variable_lugar a variable_docto_fecha_larga_actual</h4>\r\n\r\n<h2>RECIB&Iacute;</h2>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>_____________________</h4>\r\n\r\n<h4><strong>variable_nombre_del_socio</strong></h4>\r\n\r\n<p>variable_pie_de_reporte</p>\r\n' WHERE `idgeneral_contratos` = '200'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '1503'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '9002'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '9001'";
		$sqlT[]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- contenido -->' WHERE `idgeneral_contratos` = '9003'";
		//$sqlT[]	= "";
		
		
		$sqlT[]	= "CALL `proc_creditos_a_final_de_plazo`";
		$sqlT[]	= "CALL `proc_creditos_abonos_por_mes`";
		$sqlT[]	= "CALL `proc_creditos_letras_pendientes`";
		$sqlT[]	= "CALL `proc_historial_de_pagos`";
		$sqlT[]	= "CALL `proc_listado_de_ingresos`";
		$sqlT[]	= "CALL `proc_perfil_egresos_por_persona`";
		$sqlT[]	= "CALL `proc_personas_operaciones_recursivas`";
		$sqlT[]	= "CALL `sp_clonar_actividades`";
		$sqlT[]	= "CALL `proc_creditos_letras_del_dia`()";
		$sqlT[]	= "CALL `proc_personas_extranjeras`()";
		$sqlT[]	= "CALL `sp_setFoliosAlMaximo`()";
		
		$sqlT[]	= "UPDATE `general_estados` SET `operacion_habilitada`=1";
		$sqlT[]	= "CALL `proc_colonias_activas`";
		$sqlT[]	= "UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET `f_28fb96d57b21090705cfdf8bc3445d2a`='default', `f_34023acbff254d34664f94c3e08d836e`=getHash(NOW()),`f_f2cd801e90b78ef4dc673a4659c1482d`=1,`estatus`='baja' WHERE `idusuarios`=1 ";
		//$sqlT[]	= "";
		//$sqlT[]	= "";
		//$sqlT[]	= "";
		//$sqlT[]	= "";
		foreach($sqlT as $id => $sql){
			$x	= $xQL->setRawQuery($sql);
			//$isUpdate	= (strpos($sql, "DELETE") !== false ) ? true : false;
			//$isDelete	= (strpos($sql, "DELETE") !== false ) ? true : false;
			
			if($x === false){
				$msg	.= "ERROR\tSe fallo al ejecutar : [$sql] \r\n";
			} else {
				$msg	.= "OK\tEjecucion exitosa : [$sql] \r\n";
			}

		}
		//Reparar tablas
		$this->setCheckDatabase();
		//llevar los folios al maximo
		setFoliosAlMaximo();
		//
		$xRec		= new cReciboDeOperacion(12);
		$idrecibo	=  $xRec->setNuevoRecibo(1,1,fechasys(), 1, 12, "CIERRE_ESTABLECIDO_POR_DEFECTO", "NA", "ninguno", "NA", DEFAULT_GRUPO);
		$xRec->setFinalizarRecibo(false);

		//Crear periodo de credito
		//TODO: Agregar informacion de valores por DEFAULT
		$xP			= new cPeriodoDeCredito();
		$xP->add();
		$msg		.= $xP->setCambiar(EACP_PER_SOLICITUDES);
		$xConf		= new cConfiguration();
		$xConf->set("fecha_de_inicio_de_operaciones_en_el_sistema", fechasys());
		$msg	.= "OK\tCONFIGURATION\tSe cambio la fecha de Inicio de Operaciones en el sistema\r\n";
		
		$xSoc			= new cSocio(10000);//);
		$xSoc->setOmitirAML();
		$xSoc->add(EACP_NAME, "", "", EACP_RFC, "", getCajaLocal(), EACP_FECHA_DE_CONSTITUCION, EACP_LOCALIDAD);
		$xSoc->addVivienda(EACP_DOMICILIO_CORTO, "", EACP_CODIGO_POSTAL, "", "", EACP_TELEFONO_PRINCIPAL, "", true, 1,1,99, EACP_COLONIA, "calle", "", EACP_CLAVE_DE_LOCALIDAD, EACP_CLAVE_DE_PAIS);
		$xQL->setRawQuery("INSERT INTO `socios_general` (`codigo`, `nombrecompleto`, `estatusactual`, `cajalocal`, `sucursal`) VALUES ('10000', '" . EACP_NAME . "', '10', '1', '" . getSucursal() . "')");	
		return $msg;
	}
	function setPurgueSucursal($sucursal = false ){
		if ( $sucursal == false ){
			$sucursal	= getSucursal();
		}
		//Actualiza root y usuario de Impotacion a la sucursal.
		$sqlUsrs = "UPDATE t_03f996214fba4a1d05a68b18fece8e71
				    SET sucursal='$sucursal'
				    WHERE  (f_28fb96d57b21090705cfdf8bc3445d2a LIKE '%root%')
				    OR
				    (f_28fb96d57b21090705cfdf8bc3445d2a LIKE '%IMPORT%') ";
		my_query($sqlUsrs);
		$sqlST	= "SHOW TABLES IN " . MY_DB_IN;
		$rs		= mysql_query($sqlST, cnnGeneral() );
		$msg	=  "=============\tELIMINANDO REGISTROS NO PERTECIENTES A ESTA SUCURSAL \r\n";
		$msg	.= "=============\tSUCURSAL:\t$sucursal \r\n";
		while( $rw = mysql_fetch_array($rs) ){
			$table 		= $rw[0];
			$msg	.= "=============\tTABLA:\t$table\r\n";
			$sqlMT		= "DELETE FROM $table WHERE sucursal != \"$sucursal\" ";
			$x			=  my_query($sqlMT);
			$msg		.= $x["info"];
		}
		return $msg;
	}
	function setDeleteSucursal($sucursal = false ){
		if ( $sucursal == false ){
			$sucursal	= getSucursal();
		}
		$xQL		= new MQL();
		//Actualiza root y usuario de Impotacion a la sucursal.
		$sqlUsrs 	= "UPDATE t_03f996214fba4a1d05a68b18fece8e71
				    SET sucursal='$sucursal'
				    WHERE  (f_28fb96d57b21090705cfdf8bc3445d2a LIKE '%root%') OR (f_28fb96d57b21090705cfdf8bc3445d2a LIKE '%IMPORT%') ";
		$xQL->setRawQuery($sqlUsrs);
		$xTab		= new cSQLTabla();
		$arrT		= $xTab->getTablasConOperaciones();
		
		$msg	= "=============\tSUCURSAL:\t$sucursal \r\n";
		$msg	.=  "=============\tELIMINANDO REGISTROS PERTECIENTES A ESTA SUCURSAL \r\n";
		
		foreach( $arrT as $key => $value ){
			$table 		= $value;
			$msg		.= "=============\tTABLA:\t$table\r\n";
			$sqlMT		= "DELETE FROM $table WHERE sucursal = \"$sucursal\" ";
			$x			=  my_query($sqlMT);
			$msg		.= $x["info"];			
		}

		return $msg;
	}	
	/**
	 * lleva el nombre de la sucursales a minusculas por todas la tablas de la BD
	 * @return	string		Texto de los resultados de las consultas
	 * */
	function setLowerSucursal(){
		$msg	= "============= \tREPARANDO DATOS DE SUCURSAL \r\n";
		$xQL	= new MQL();
		$sql	= "SELECT DISTINCT TABLE_NAME, COLUMN_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE COLUMN_NAME IN ('sucursal', 'centro_de_trabajo')
        AND TABLE_SCHEMA='" . MY_DB_IN ."'";
		$rs		= $xQL->getDataRecord($sql);
		foreach ($rs as $rw){
			$tabla	= $rw["TABLE_NAME"];
			$col	= $rw["COLUMN_NAME"];
			$sqlMT	= "UPDATE $tabla SET `$col` = IF(TRIM(`$col`)='', 'matriz', LCASE(`$col`)) ";
			$ready	= $xQL->setRawQuery($sqlMT);	
			$msg	.= ($ready == false) ? "ERROR\tAl actualizar la columna $col en la Tabla $tabla\r\n" : "OK\tColumna $col de la Tabla $tabla Actualizada\r\n";
		}
		return $msg;
	}

	/**
	 * elimina la cache de sesiones de la BD.
	 * @return	null
	 * */
	function clearCacheSessions(){
		$sql = "DELETE FROM usuarios_web_connected";
		$this->execQuery($sql);
	}

	function setFoliosAlMaximo(){
		$xQL	= new MQL();
		$arrDev = array();
			/*
			 * Actualiza los Folios a Maximos
			 */
			//elimina los registros
			$sql_t = "DELETE FROM general_folios";
			$xQL->setRawQuery($sql_t);
			//Busca los Maximos
			$sql_u_ops	= "INSERT INTO general_folios(	numerooperacion, numerocredito, numerosocio, numerocontrato , numeroestadistico, numerorecibo, 
				numerogposolidario , polizacontable) VALUES( 
				COALESCE( (SELECT MAX(idoperaciones_mvtos) FROM operaciones_mvtos), 0 ), 
				COALESCE( (SELECT MAX(numero_solicitud) FROM creditos_solicitud), 0 ), 
				COALESCE( ( SELECT MAX(codigo) FROM socios_general ), 0), 
				COALESCE( ( SELECT MAX(numero_cuenta)  FROM captacion_cuentas ), 0), 
				0,
				COALESCE( ( SELECT MAX(idoperaciones_recibos) FROM operaciones_recibos ), 0), 
				COALESCE( ( SELECT MAX(idsocios_grupossolidarios) FROM socios_grupossolidarios ), 0),
				'')";
			$xQL->setRawQuery($sql_u_ops);
			$arrDev["recibos"] 		= 0;
			$arrDev["operaciones"] 	= 0;
			return $arrDev;
	}
	function setCheckDatabase(){
		/*$ql		= new MQL();
		$rs		= $ql->getDataRecord("SHOW TABLES");
		foreach ($rs as $rw){
			$t	= $rw["Tables_in_". MY_DB_IN];
			$ql->setRawQuery("OPTIMIZE TABLE $t");
		}
		$rs		= null;*/
		exec( "mysqlcheck --user=" . USR_DB . " --password=" . PWD_DB . " --databases " . MY_DB_IN . " --auto-repair --optimize " );
	//exec	mysqlcheck --user=root --password= --databases matriz --auto-repair
	}
	function setCrearEjemplos(){
		$sqlT	= array();
		$msg	= "";
		$xF		= new cFecha();
		$msg	.= setFoliosAlMaximo();
		$ff		= $xF->getFechaISO();
		
		$sqlT[]	= "INSERT INTO `socios_general` (`codigo`, `nombrecompleto`, `apellidopaterno`, `apellidomaterno`, `rfc`, `curp`, `estatusactual`, `cajalocal`, `lugarnacimiento`,
				`tipoingreso`, `estadocivil`, `genero`, `eacp`, `sucursal`, `documento_de_identificacion`, `correo_electronico`, `telefono_principal`, `dependientes_economicos`) VALUES
				('99999', 'SUJETO DE', 'PRUEBAS', 'PARA REPORTAR', 'NNGL000000', 'NGL000000', '10', '1', 'YC,MERIDA', '200', '1', '1', 'SRNC6900601', 'matriz', 'IFE9999999', 'tasks@opencorebanking.com', '00000000', '1')";
		$sqlT[]	= "INSERT INTO `socios_general` (`codigo`, `nombrecompleto`, `rfc`, `curp`, `fechaentrevista`, `fechaalta`, `estatusactual`, `cajalocal`, `fechanacimiento`, `lugarnacimiento`,
				`tipoingreso`, `genero`, `eacp`, `sucursal`, `fecha_de_revision`, `tipo_de_identificacion`, `correo_electronico`, `telefono_principal`,
				`dependientes_economicos`, `titulo_personal`) VALUES ('99998', 'PERSONA DE EJEMPLO',
				'RFC0000000', 'CURP00000000', '$ff', '$ff', '10', '1', '1981-08-22', 'MERIDA,YUCATAN', '500', '1', 'SRN69300601', 'matriz', '$ff', '99', 'admin@sipakal.com', '9811098164', '4', 'SR')";
		$sqlT[]	= "INSERT INTO `socios_aeconomica` (`socio_aeconomica`, `tipo_aeconomica`, `sector_economico`, `nombre_ae`, `domicilio_ae`, `localidad_ae`, `municipio_ae`,
					`estado_ae`, `telefono_ae`, `extension_ae`, `numero_empleado`, `antiguedad_ae`, `departamento_ae`, `monto_percibido_ae`, `fecha_alta`, `sucursal`, `oficial_de_verificacion`, `numero_de_seguridad_social`) VALUES ('99999', '10', '1', 'EMPRESA DE EJEMPLO, S.A. DE C.V.', 'DOMICILIO CONOCIDO, MERIDA YUCATAN.', 'Merida', 'Merida', 'Yucatan', '9811098164', '0', '0', '2266', 'NA', '7000', '$ff', 'matriz', '99', '0000000000')";
		$sqlT[]	= "INSERT INTO `socios_aeconomica` (`socio_aeconomica`, `tipo_aeconomica`, `sector_economico`, `nombre_ae`, `domicilio_ae`, `localidad_ae`, `municipio_ae`,
					`estado_ae`, `telefono_ae`, `extension_ae`, `numero_empleado`, `antiguedad_ae`, `departamento_ae`, `monto_percibido_ae`, `fecha_alta`, `sucursal`, `oficial_de_verificacion`, `numero_de_seguridad_social`) VALUES ('99998', '10', '1', 'EMPRESA DE EJEMPLO, S.A. DE C.V.', 'DOMICILIO CONOCIDO, MERIDA YUCATAN.', 'Merida', 'Merida', 'Yucatan', '9811098164', '0', '0', '2266', 'NA', '7000', '$ff', 'matriz', '99', '0000000000')";
		$sqlT[]	= "INSERT INTO `creditos_solicitud` (`numero_solicitud`, `fecha_solicitud`, `fecha_autorizacion`, `monto_solicitado`, `monto_autorizado`, `numero_socio`, `plazo_en_dias`, `numero_pagos`, `tasa_interes`, `periocidad_de_pago`, `tipo_credito`, `fecha_vencimiento`, `pagos_autorizados`, `dias_autorizados`, `periodo_solicitudes`, `fecha_ultimo_mvto`, `tipo_convenio`, `interes_diario`, `tasa_moratorio`, `fecha_ministracion`) VALUES ('29000201', '$ff', '$ff', '120000', '120000', '99999', '360', '24', '0.6', '15', '3', '2014-12-31', '24', '365', '201499', '$ff', '200', '10', '1.2', '$ff')";
		$sqlT[]	= "INSERT INTO `personas_perfil_transaccional` (`clave_de_persona`, `fecha_de_registro`, `fecha_de_vencimiento`, `clave_de_tipo_de_perfil`, `pais_de_origen`, `maximo_de_operaciones`, `cantidad_maxima`, `operaciones_calculadas`, `cantidad_calculada`, `fecha_de_calculo`, `afectacion`, `observaciones`) VALUES ('99999', '1394172000', '1394172000', '101', 'MX', '10', '10000', '0', '0', '1394172000', '-1', '')";
		$sqlT[]	= "INSERT INTO `personas_perfil_transaccional` (`clave_de_persona`, `fecha_de_registro`, `fecha_de_vencimiento`, `clave_de_tipo_de_perfil`, `pais_de_origen`, `maximo_de_operaciones`, `cantidad_maxima`, `operaciones_calculadas`, `cantidad_calculada`, `fecha_de_calculo`, `afectacion`, `observaciones`) VALUES ('99999', '1394172000', '1394172000', '201', 'MX', '5', '50000', '0', '0', '1394172000', '-1', '')";
		$sqlT[]	= "INSERT INTO `personas_perfil_transaccional` (`clave_de_persona`, `fecha_de_registro`, `fecha_de_vencimiento`, `clave_de_tipo_de_perfil`, `pais_de_origen`, `maximo_de_operaciones`, `cantidad_maxima`, `operaciones_calculadas`, `cantidad_calculada`, `fecha_de_calculo`, `afectacion`, `observaciones`) VALUES ('99999', '1394172000', '1394172000', '301', 'MX', '5', '50000', '0', '0', '1394172000', '-1', '')";
		$sqlT[]	= "INSERT INTO `personas_perfil_transaccional` (`clave_de_persona`, `fecha_de_registro`, `fecha_de_vencimiento`, `clave_de_tipo_de_perfil`, `pais_de_origen`, `maximo_de_operaciones`, `cantidad_maxima`, `operaciones_calculadas`, `cantidad_calculada`, `fecha_de_calculo`, `afectacion`, `observaciones`) VALUES ('99999', '1394172000', '1394172000', '401', 'MX', '5', '50000', '0', '0', '1394172000', '-1', '')";
		$sqlT[]	= "INSERT INTO `bancos_entidades` (`idbancos_entidades`, `nombre_de_la_entidad`, `rfc_de_la_entidad`, `pais_de_origen`) VALUES ('700', 'EL BANCO EXTRANJERO', 'EXT99999', 'US')";
		$sqlT[]	= "INSERT INTO `creditos_tipoconvenio` (`idcreditos_tipoconvenio`, `descripcion_tipoconvenio`, `tipo_convenio`, `numero_creditos_maximo`, `dias_maximo`, `pagos_maximo`, `interes_normal`, `interes_moratorio`, `maximo_otorgable`, `tolerancia_dias_primer_abono`, `numero_avales`, `minimo_otorgable`, `php_monto_maximo`, `tasa_iva`) VALUES ('501', 'EJEMPLO DIARIO', '501', '10', '180', '180', '6.896551724', '6.896551724', '1', '20', '0', '100', '\$monto_maximo = \$producto_monto_maximo;', '0.16')";
		$sqlT[]	= "UPDATE `creditos_tipoconvenio` SET `maximo_otorgable` = '1000000' WHERE `idcreditos_tipoconvenio` = '501'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '99' WHERE `nombre_del_parametro` = 'clave_de_usuario_del_oficial_de_cumplimiento'";
		$sqlT[]	= "INSERT INTO `t_03f996214fba4a1d05a68b18fece8e71` 
				(`idusuarios`, `f_28fb96d57b21090705cfdf8bc3445d2a`, `f_34023acbff254d34664f94c3e08d836e`, `nombres`, `apellidopaterno`, `apellidomaterno`, `puesto`, 
				`f_f2cd801e90b78ef4dc673a4659c1482d`, `periodo_responsable`, `date_expire`, `codigo_de_persona`) 
				VALUES 
				('400', 'cajero', getHash('cajero'), 'CAJERO', 'DE', 'PRUEBA', 'Cajero', '4', '400', '$ff', '1'),
				('1000', 'cumplimiento', getHash('cumplimiento'), 'OFICIAL', 'DE', 'CUMPLIMIENTO', 'Oficial de Cumplimiento', '10', '1000', '$ff', '1'),
				('600', 'contabilidad', getHash('contabilidad'), 'USUARIO', 'DE', 'CONTABILIDAD', 'Contabilidad', '6', '600', '$ff', '1'),
				('700', 'credito', getHash('credito'), 'OFICIAL', 'DE', 'CREDITO', 'Oficial de Credito', '7', '700', '$ff', '1')
		";
		$sqlT[]	= "INSERT INTO `t_03f996214fba4a1d05a68b18fece8e71`
				(`idusuarios`, `f_28fb96d57b21090705cfdf8bc3445d2a`, `f_34023acbff254d34664f94c3e08d836e`, `nombres`, `apellidopaterno`, `apellidomaterno`, `puesto`,
				`f_f2cd801e90b78ef4dc673a4659c1482d`, `periodo_responsable`, `date_expire`, `codigo_de_persona`)
				VALUES
				('100', 'remoteuser', getHash('remoteuser'), 'TRABAJOS', '', 'AUTOMATICOS', 'Trabajos automaticos', '7', '100', '$ff', '1')	";		
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'remoteuser' WHERE `nombre_del_parametro` = 'contrasenna_de_trabajos_automaticos' ";
		$sqlT[]	= "INSERT INTO `bancos_entidades` (`idbancos_entidades`, `nombre_de_la_entidad`, `rfc_de_la_entidad`, `pais_de_origen`) VALUES ('700', 'EL BANCO EXTRANJERO', 'EXT99999', 'US')";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '99999' WHERE `nombre_del_parametro` = 'registro_ante_la_cnbv'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '99999' WHERE `nombre_del_parametro` = 'registro_casfin'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'LA FINANCIERA SOFOM ENR' WHERE `nombre_del_parametro` = 'nombre_de_la_entidad'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'tasks@opencorebanking.com' WHERE `nombre_del_parametro` = 'email_de_la_entidad'";
		$sqlT[]	= "INSERT INTO `bancos_entidades` (`idbancos_entidades`, `nombre_de_la_entidad`, `rfc_de_la_entidad`, `pais_de_origen`) VALUES ('701', 'EL BANCO IRANI', 'IRAN0001', 'IR')";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '1-1-2-2-2' WHERE `nombre_del_parametro` = 'mascara_de_cuenta_contable'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '#-#-##-##-##' WHERE `nombre_del_parametro` = 'mascara_sql_de_cuenta_contable'";
		$sqlT[]	= "UPDATE `sistema_programacion_de_avisos` SET `destinatarios` = 'CORREO:patadejaguar@gmail.com|'";
		$sqlT[]	= "UPDATE `bancos_cuentas` SET `codigo_contable` = '110215'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '99999' WHERE `nombre_del_parametro` = 'registro_ante_la_cnbv'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '99999' WHERE `nombre_del_parametro` = 'registro_casfin'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'LA FINANCIERA SOFOM ENR' WHERE `nombre_del_parametro` = 'nombre_de_la_entidad'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'tasks@opencorebanking.com' WHERE `nombre_del_parametro` = 'email_de_la_entidad'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'smtp.opencorebanking.com' WHERE `nombre_del_parametro` = 'servidor_smtp_para_notificaciones'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'Pruebas016' WHERE `nombre_del_parametro` = 'password_del_email_del_administrador'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'pruebas@opencorebanking.com' WHERE `nombre_del_parametro` = 'email_de_nominas'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'pruebas@opencorebanking.com' WHERE `nombre_del_parametro` = 'email_del_administrador'";
		$sqlT[]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'pruebas@opencorebanking.com' WHERE `nombre_del_parametro` = 'facturacion.email_de_almacenamiento'";
		$sqlT[]	= "INSERT INTO `socios_general` (`codigo`, `nombrecompleto`, `apellidopaterno`, `apellidomaterno`, `rfc`, `curp`, `estatusactual`, `cajalocal`, `fechanacimiento`, `lugarnacimiento`, `tipoingreso`, `estadocivil`, `genero`, `eacp`, `sucursal`, `documento_de_identificacion`, `correo_electronico`, `telefono_principal`) VALUES ('90001', 'LUIS HUMBERTO', 'BALAM', 'GONZALEZ', 'BAGL810822VE5', 'BAGL810822HCCLNS12', '10', '1', '1981-08-22', 'CC,CAMPECHE', '200', '1', '1', 'SRN69300601', 'matriz', 'IFE1000000', 'patadejaguar@gmail.com', '9811371867'); ";
		$sqlT[]	= "UPDATE `socios_general` SET `correo_electronico` = 'luis.balam@opencorebanking.com' WHERE `codigo` = '90001' ";
		$sqlT[]	= "UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET `codigo_de_persona` = '99999' WHERE `idusuarios` != '99'";
		$sqlT[]	= "INSERT INTO `bancos_cuentas` (`idbancos_cuentas`, `descripcion_cuenta`, `fecha_de_apertura`, `estatus_actual`, `consecutivo_actual`, `saldo_actual`, `codigo_contable`, `entidad_bancaria`) VALUES ('12000', 'BANCO DE PRUEBA', '$ff', 'activo', '00001', '100000', '110215', '50')";

		$sqlT[]	= "INSERT INTO `creditos_periodos` (`idcreditos_periodos`, `descripcion_periodos`, `fecha_inicial`, `fecha_final`, `fecha_reunion`) VALUES ('" . $xF->anno() . "99', 'Periodo General', '" . $xF->getFechaInicialDelAnno() . "', '" . $xF->anno() . "-12-31', '" . $xF->anno() . "-12-31');";
		
		//$sqlT[]	= "";
		$xQL	= new MQL();
		foreach($sqlT as $id => $sql){
			$x = $xQL->setRawQuery( $sql );
			if($x === false){
				$msg	.= "ERROR\tNo echo ($sql)!\r\n";
			} else {
				$msg	.= "OK\techo!\r\n";
			}
		}
		//llevar los folios al maximo
		return $msg;
	}
	function setModArrendamientoDA($enable = false){
		
		//Des-habilitar contratos
		$this->setContratoDisEn(1900, $enable);
		$this->setContratoDisEn(1901, $enable);
		$this->setContratoDisEn(1902, $enable);
		$this->setContratoDisEn(1903, $enable);
		$this->setContratoDisEn(1904, $enable);
		$this->setContratoDisEn(1905, $enable);
		
		$this->setContratoDisEn(1906, $enable);
		$this->setContratoDisEn(1907, $enable);
		$this->setContratoDisEn(1908, $enable);
		$this->setContratoDisEn(1909, $enable);
		$this->setContratoDisEn(1910, $enable);

		$this->setContratoDisEn(1911, $enable);
		$this->setContratoDisEn(1912, $enable);
		$this->setContratoDisEn(1913, $enable);
		$this->setContratoDisEn(1914, $enable);
		$this->setContratoDisEn(1915, $enable);
		$this->setContratoDisEn(1916, $enable);
		$this->setOperacionDisEn(157, $enable);
		$this->setOperacionDisEn(171, $enable);
		$this->setOperacionDisEn(172, $enable);
		$this->setOperacionDisEn(173, $enable);
		$this->setOperacionDisEn(174, $enable);
		$this->setOperacionDisEn(175, $enable);
		$this->setOperacionDisEn(176, $enable);
		$this->setOperacionDisEn(177, $enable);
		$this->setOperacionDisEn(178, $enable);
		$this->setOperacionDisEn(179, $enable);
		$this->setOperacionDisEn(180, $enable);
	}
	function setModAML_DA($enable = false){
		$this->setContratoDisEn(8801, $enable);
		$this->setContratoDisEn(8802, $enable);
		$this->setContratoDisEn(811, $enable);
		$this->setContratoDisEn(812, $enable);
		$this->setContratoDisEn(800, $enable);
		
	}
	function setModContableDA($enable = false){
		$this->setContratoDisEn(501, $enable);
		$this->setContratoDisEn(502, $enable);
		$this->setContratoDisEn(70021, $enable);
	}
	function setModSeguimientoDA($enable = false){
		$this->setContratoDisEn(3002, $enable);
		$this->setContratoDisEn(10, $enable);
		//$this->setContratoDisEn(502, $enable);
		
	}
	function setModCaptacionDA($enable = false){
		$this->setContratoDisEn(16, $enable);
		$this->setContratoDisEn(2, $enable);
		$this->setContratoDisEn(7, $enable);
		$this->setContratoDisEn(6, $enable);
		$this->setContratoDisEn(9, $enable);
		$this->setContratoDisEn(18, $enable);
		$this->setOperacionDisEn(412, $enable);
		$this->setOperacionDisEn(220, $enable);
		$this->setOperacionDisEn(500, $enable);
		$this->setOperacionDisEn(510, $enable);
		$this->setOperacionDisEn(251, $enable);
		
	}
	private function setContratoDisEn($id, $enable = false){
		$estatus	= ($enable == true) ? "alta" : "baja";
		$this->execQuery("UPDATE `general_contratos` SET `estatus`='$estatus' WHERE `idgeneral_contratos`=$id");
	}
	private function setOperacionDisEn($id, $enable = false){
		$estatus	= ($enable == true) ? "1" : "0";
		$this->execQuery("UPDATE `operaciones_tipos` SET `estatus` = '$estatus' WHERE `idoperaciones_tipos` = '$id'");
	}
	function setModGruposDisEn($enable = false){
		//$this->setContratoDisEn(3002, $enable);
		//$this->setContratoDisEn(10, $enable);
		//$this->setOperacionDisEn(510, $enable);
		$this->setOperacionDisEn(50, $enable);
		$this->setOperacionDisEn(112, $enable);
		$this->setOperacionDisEn(417, $enable);
		
	}
	function setModAportacionesDisEn($enable = false){
		//$this->setContratoDisEn(3002, $enable);
		//$this->setContratoDisEn(10, $enable);
		//$this->setOperacionDisEn(510, $enable);
		$this->setOperacionDisEn(701, $enable);
		$this->setOperacionDisEn(702, $enable);
		$this->setOperacionDisEn(703, $enable);
		$this->setOperacionDisEn(704, $enable);
		$this->setOperacionDisEn(705, $enable);
		$this->setOperacionDisEn(706, $enable);
		$this->setOperacionDisEn(707, $enable);
		$this->setOperacionDisEn(708, $enable);
		$this->setOperacionDisEn(710, $enable);
		$this->setOperacionDisEn(711, $enable);
		$this->setOperacionDisEn(712, $enable);
		$this->setOperacionDisEn(902, $enable);
		
	}
	function setManejarGarantiasEnCaptacion($manejar = false){
		if($manejar == false){
			//Habilitar operaciones de garantia
			$this->setOperacionDisEn(353, true);
			$this->setOperacionDisEn(901, true);
		} else {
			//inhabilitar
			$this->setOperacionDisEn(353, false);
			$this->setOperacionDisEn(901, false);
		}
	} 
	function setModTesoreriaDisEn($enable = false){
		//$this->setContratoDisEn(3002, $enable);
		//$this->setContratoDisEn(10, $enable);
		//$this->setOperacionDisEn(510, $enable);
		$this->setOperacionDisEn(9100, $enable);
		$this->setOperacionDisEn(9101, $enable);
		$this->setOperacionDisEn(9200, $enable);
		$this->setOperacionDisEn(9201, $enable);
		
	}
}
function getUsuarioActual($parametro = false){
	$usr	= false;
	if($parametro == false){
		$usr	= ( isset($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]) ) ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : false;
	} else {
		$usr	= ( isset($_SESSION[$parametro]) ) ? $_SESSION[$parametro] : false;
	}
	return $usr;
}
function getCantidadRendonda($cantidad, $redondeo){
	$numero			= $cantidad;
	$cantidad		= explode(STD_NUMERICAL_DIV, $cantidad);
	if(isset($cantidad[1])){
		$superior		= $cantidad[0];
		$cantidad		= intval($cantidad[1]);
		if($redondeo == 50){
			if($cantidad <= 50){
				$numero	= $superior . STD_NUMERICAL_DIV . "50";
			} else {
				$numero	= $superior + 1;
			}
		} elseif ($redondeo == 100){
			$numero	= $superior + 1;
		}
	}
	return $numero;
}
function getVariablesSanas($contents, $vars){
	$xT					= new cTipos();
	foreach ( $vars as $valor => $tipo ){
		$valor			= trim($valor);
		
		switch ($tipo){
			case MQL_FLOAT:
				$contents[$valor]	= (isset($contents[$valor])) ?  $xT->cFloat($contents[$valor]) : 0;
				break;
			case MQL_INT:
				$contents[$valor]	= (isset($contents[$valor])) ? $xT->cInt($contents[$valor]) : 0;
				break;
			default:
				$contents[$valor]	= (isset($contents[$valor])) ? $xT->cChar($contents[$valor]) : "";
				break;
		}		
		
	}
	return $contents;
}
function getEnCierre($v = null){
	if(!isset($_SESSION)){
		return false;
	} else {
		if(!isset($_SESSION["oncierre"])){
			$_SESSION["oncierre"] = false;
		}
		if($v !== null){
			$_SESSION["oncierre"] = $v;
		}
		return $_SESSION["oncierre"];
	}
}

class cCierreDelDia {
	private $mFecha			= false;
	private $mMessages		= "";
	private $mFechaUltima	= false;
	private $mForce			= false;
	function __construct($fecha = false){
		$this->mFecha	= ($fecha == false) ? fechasys() : $fecha;
	}
	function setForzar($forzar = false){ $this->mForce = $forzar; }
	function checkCierre($fecha = false){
		$recibos	= 0;
		if($this->mForce == false){
			$fecha		= ($fecha == false) ? $this->mFecha : $fecha;
			$sql		= "SELECT COUNT(idoperaciones_recibos) AS 'recibos' FROM operaciones_recibos WHERE fecha_operacion='$fecha' AND tipo_docto=12 ";
			$recibos	= mifila($sql, "recibos");
			$recibos	= setNoMenorQueCero($recibos);
			$xCaja		= new cCaja();
			if($xCaja->getCajasAbiertas($fecha) > 0 ){ $this->mMessages	.= "ERROR\tFecha $fecha tiene cortes pendientes\r\n"; }
			if ($recibos > 0){ $this->mMessages	.= "WARN\tFecha $fecha Existe en el cierre\r\n"; }
		}
		return ($recibos == 0) ? false : true;
	}
	function check5Cierres($fecha_final, $alCerrar = false){
		$xF							= new cFecha();
		$fecha_inicio_sistema		= FECHA_INICIO_OPERACIONES_SISTEMA;
		$dias_a_revisar				= REVISAR_DIAS_DE_CIERRE;
		$fecha_inicial				= $xF->setRestarDias($dias_a_revisar, $fecha_final);
		$res						= array();
		$ok							= false;
		$xSuc						= new cSucursal();
		for($i=0; $i<= $dias_a_revisar; $i++){
			$fecha					= $xF->setSumarDias($i, $fecha_inicial);
			if( $xF->getInt($fecha_inicio_sistema) >= $xF->getInt($fecha) ){
				$this->mMessages	.= "WARN\tFecha $fecha OMITIDO por ser menor al inicio de operaciones\r\n";
				$res[$fecha]		= true;
				$ok					= true;
			} elseif( $xF->getInt($fecha) == $xF->getInt(fechasys()) ){
				$this->mMessages	.= "WARN\tFecha $fecha OMITIDO por ser Fecha Actual\r\n";
				$res[$fecha]		= true;
				$ok					= true;
				if ($alCerrar == true){	
					if ( (int) date("H") < (int) $xSuc->getHorarioDeCierre()  ){
						//considerar si es dia festivo
						$this->mMessages	.= "ERROR\tNO ES EL HORARIO MINIMO DE CIERRE PARA LA FECHA $fecha SON LAS " . date("H") . " HRS. DE " . $xSuc->getHorarioDeCierre() . ", MINIMO DE CIERRE\r";
						$res[$fecha]		= false;
						$ok					= false;
					}
				}
			} else {
				//$this->mMessages	.= "WARN\tFecha $fecha OMITIDO por ser menor al inicio de operaciones\r\n";
				
				if($this->checkCierre($fecha) == false){
					$res[$fecha]		= false;
					$ok					= false;
					$this->mMessages	.= "ERROR\tFecha $fecha No existe en el sistema\r\n";
					if($this->mFechaUltima === false){
						$this->mFechaUltima	= $fecha;
					}
					if($xF->getInt($fecha) == $xF->getInt($fecha_final) ){
						$this->mMessages	.= "ERROR\tPROCESAR LA FECHA $fecha_final|$fecha LAS FECHAS SON LAS MISMAS A " . fechasys() . "\r\n";
						$res[$fecha]		= true;
						$ok					= true;
					}
					if(MODO_MIGRACION == true AND $ok == false){
						$res[$fecha]		= true;
						$ok					= true;
						$this->mMessages	.= "OK\tSistema en Migracion a la Fecha $fecha_final|$fecha\r\n";
					}
				} else {
					$res[$fecha]		= true;
					$ok					= true;
					$this->mMessages	.= "OK\tFecha $fecha existente\r\n";
				}
			}
			
			$xCaja						= new cCaja();
			if ($alCerrar == true){
				if($xCaja->getCajasAbiertas($fecha) > 0 ){
					$ok					= false;
					$res[$fecha]		= false;
					$this->mMessages	.= "OK\tFecha $fecha tiene cortes pendientes\r\n";
				}
			}
			if($this->mForce == true){
				$res[$fecha]		= true;
				$ok					= true;
			}
		}
		unset($res[fechasys()]);
		foreach($res as $dateme => $rs){
			
			if($dateme != SYS_ESTADO){
				if($rs == false){
					$this->mMessages	.= "ERROR\tFecha $dateme tiene cortes pendientes.-2\r\n";
					$ok					= false;
				}
			}
		}
		if(MODO_DEBUG == true){ setLog($this->mMessages); }
		$res[SYS_ESTADO]				= $ok;
		return $res;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getFechaUltima(){ return $this->mFechaUltima; }
}

function estaFueraDeRango($valor, $rango_inicial, $rango_final){
	$rs		= false;
	$rs		= ($valor > $rango_final) ? true: false;
	$rs		= ($valor < $rango_inicial) ? true : $rs;
	return $rs;
}
function getCajaLocal(){ $xSucursal	= new cSucursal();return $xSucursal->getCajaLocalResidente();}
function getRegion(){ $xSucursal	= new cSucursal();return $xSucursal->getRegionLocal();   }
function getAdsense(){
	$adsense	= (SAFE_PAY_VERSION == "") ? "<script async src=\"https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
<!-- SAFE-OSMS -->
<ins class=\"adsbygoogle\" style=\"display:inline-block;width:234px;height:60px\" data-ad-client=\"ca-pub-1005748569860531\" data-ad-slot=\"9760371821\"></ins>
		<script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>" : "";
	return $adsense;
}

function getPalabraDeFrase($frase, $idx = 0, $separador = " "){
	$str	= $frase;
	$str	= (strpos($str, "+") !== false) ? urldecode($str) : $str;
	$str	= trim($str);
	
	if(strpos($str, $separador) !== false){
		$segs	= explode($separador, $str);
		$str	= isset($segs[$idx]) ? trim($segs[$idx]) : "";
	}
	return $str;
}

function cleanString($text) {
	// 1) convert á ô => a o
	$text = preg_replace("/[áàâãªä]/u","a",$text);
	$text = preg_replace("/[ÁÀÂÃÄ]/u","A",$text);
	$text = preg_replace("/[ÍÌÎÏ]/u","I",$text);
	$text = preg_replace("/[íìîï]/u","i",$text);
	$text = preg_replace("/[éèêë]/u","e",$text);
	$text = preg_replace("/[ÉÈÊË]/u","E",$text);
	$text = preg_replace("/[óòôõºö]/u","o",$text);
	$text = preg_replace("/[ÓÒÔÕÖ]/u","O",$text);
	$text = preg_replace("/[úùûü]/u","u",$text);
	$text = preg_replace("/[ÚÙÛÜ]/u","U",$text);
	$text = preg_replace("/[’‘‹›‚]/u","'",$text);
	$text = preg_replace("/[“”«»„]/u",'"',$text);
	$text = str_replace("–","-",$text);
	$text = str_replace(" "," ",$text);
	$text = str_replace("ç","c",$text);
	$text = str_replace("Ç","C",$text);
	$text = str_replace("ñ","n",$text);
	$text = str_replace("Ñ","N",$text);

	//2) Translation CP1252. &ndash; => -
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
	$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
	$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
	$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
	$trans[chr(134)] = '&dagger;';    // Dagger
	$trans[chr(135)] = '&Dagger;';    // Double Dagger
	$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
	$trans[chr(137)] = '&permil;';    // Per Mille Sign
	$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
	$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
	$trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
	$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
	$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
	$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
	$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
	$trans[chr(149)] = '&bull;';    // Bullet
	$trans[chr(150)] = '&ndash;';    // En Dash
	$trans[chr(151)] = '&mdash;';    // Em Dash
	$trans[chr(152)] = '&tilde;';    // Small Tilde
	$trans[chr(153)] = '&trade;';    // Trade Mark Sign
	$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
	$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
	$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
	$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
	$trans['euro'] = '&euro;';    // euro currency symbol
	ksort($trans);

	foreach ($trans as $k => $v) {
		$text = str_replace($v, $k, $text);
	}

	// 3) remove <p>, <br/> ...
	$text = strip_tags($text);

	// 4) &amp; => & &quot; => '
	$text = html_entity_decode($text);

	// 5) remove Windows-1252 symbols like "TradeMark", "Euro"...
	$text = preg_replace('/[^(\x20-\x7F)]*/','', $text);

	$targets=array('\r\n','\n','\r','\t');
	$results=array(" "," "," ","");
	$text = str_replace($targets,$results,$text);

	//XML compatible
	/*
	$text = str_replace("&", "and", $text);
	$text = str_replace("<", ".", $text);
	$text = str_replace(">", ".", $text);
	$text = str_replace("\\", "-", $text);
	$text = str_replace("/", "-", $text);
	*/

	return ($text);
}

function getOficialAML(){
	$xLoc	= new cLocal();
	$xLoc->init();
	return $xLoc->getOficialDeCumplimiento();
}

class cLocal {
	private $mCajaLocal				= 0;
	private $mClaveLocalidad		= 0;
	private $mClaveDeMunicipio		= 0;
	private $mClaveDeEstado			= 0;
	private $mClaveDeEstadoABC		= "";
	private $mClaveDeEstadoSIC		= "";
	private $mLocalidad				= "";
	private $mMunicipio				= "";
	private $mClavePostal			= 0;
	private $mEntidadFed			= "";
	private $mNumeroExt				= "";
	private $mNumeroInt				= "";
	private $mCalle					= "";
	private $mNombrePais			= "";
	private $mColonia				= "";
	private $mOficialDeCump			= 0;
	private $mInit					= false;
	function __construct($sucursal	= false){
		$sucursal	= ($sucursal == false) ? getSucursal() : $sucursal;
		$this->init($sucursal);
	}
	function init($sucursal = false){
		$sucursal		= ($sucursal == false) ? getSucursal() : $sucursal;
		$cargar			= false;
		$cargar			= (!isset($_SESSION[SYS_LOCAL_VARS_LOAD]))? true : $cargar;
		$cargar			= ($sucursal != getSucursal()) ? true : $cargar;
		if(  $cargar == true ){
			//$this->mCajaLocal
			
			$this->mClaveDeEstado		= EACP_CLAVE_NUM_ENTIDADFED;
			$this->mClaveDeEstadoABC	= EACP_CLAVE_DE_ENTIDADFED;
			$this->mClaveDeEstadoSIC	= EACP_CLAVE_DE_ENTIDAD_SIC;
			$this->mLocalidad			= EACP_LOCALIDAD;
			$this->mClaveLocalidad		= EACP_CLAVE_DE_LOCALIDAD;
			$this->mMunicipio			= EACP_MUNICIPIO;
			$this->mClaveDeMunicipio	= EACP_CLAVE_DE_MUNICIPIO;
			$this->mClavePostal			= EACP_CODIGO_POSTAL;
			$this->mEntidadFed			= EACP_ESTADO;
			$this->mCajaLocal			= DEFAULT_CAJA_LOCAL;
			$this->mCalle				= EACP_DOMICILIO_CALLE;
			$this->mNumeroExt			= EACP_DOMICILIO_NUM_EXT;
			$this->mNumeroInt			= EACP_DOMICILIO_NUM_INT;
			$this->mNombrePais			= EACP_DOMICILIO_PAIS;
			$this->mColonia				= EACP_COLONIA;
			$this->mOficialDeCump		= AML_OFICIAL_DE_CUMPLIMIENTO;
			$xSuc = new cSucursal($sucursal);
			if( $xSuc->init() == true){
				$this->mClaveDeEstado		= $xSuc->getClaveDeEstado();
				$this->mClaveDeEstadoABC	= $xSuc->getClaveDeEstadoABC();
				$this->mClaveDeEstadoSIC	= $xSuc->getClaveDeEstadoSIC();
				$this->mLocalidad			= $xSuc->getNombreLocalidad();
				$this->mClaveLocalidad		= $xSuc->getClaveDeLocalidad();
				$this->mMunicipio			= $xSuc->getMunicipio();
				$this->mClaveDeMunicipio	= $xSuc->getClaveDeMunicipio();
				$this->mClavePostal			= $xSuc->getCodigoPostal();
				$this->mEntidadFed			= $xSuc->getEstado();			
				$this->mCajaLocal			= $xSuc->getCajaLocalResidente();
				$this->mCalle				= $xSuc->getCalle();
				$this->mNumeroExt			= $xSuc->getNumeroExterior();
				$this->mNumeroInt			= $xSuc->getNumeroInterior();
				//$this->mNombrePais			= $xSuc->ge
				$this->mColonia				= $xSuc->getColonia();
				$this->mOficialDeCump		= $xSuc->getOficialDeCumplimiento();
				$this->mInit				= true;
			}
			$_SESSION["domicilio.localidad"]		= $this->mLocalidad;
			$_SESSION["domicilio.localidad.clave"]	= $this->mClaveLocalidad;
			$_SESSION["domicilio.municipio"]		= $this->mMunicipio;
			$_SESSION["domicilio.municipio.clave"]	= $this->mClaveDeMunicipio;
			$_SESSION["domicilio.estado.clave.abc"]	= $this->mClaveDeEstadoABC;
			$_SESSION["domicilio.estado.clave.sic"]	= $this->mClaveDeEstadoSIC;
			$_SESSION["domicilio.estado.clave.num"]	= $this->mClaveDeEstado;
			$_SESSION["domicilio.estado"]			= $this->mEntidadFed;
			$_SESSION["domicilio.cp"]				= $this->mClavePostal;
			$_SESSION["domicilio.cajalocal"]		= $this->mCajaLocal;
			$_SESSION["domicilio.colonia"]			= $this->mColonia;
			$_SESSION["domicilio.calle"]			= $this->mCalle;
			$_SESSION["domicilio.numero.ext"]		= $this->mNumeroExt;
			$_SESSION["domicilio.numero.int"]		= $this->mNumeroInt;
			$_SESSION["domicilio.pais"]				= $this->mNombrePais;
			$_SESSION["oficial.aml"]				= $this->mOficialDeCump;
			$_SESSION[SYS_LOCAL_VARS_LOAD]			= true;
		}
		return $this->mInit;
	}
	function DomicilioLocalidad($valor = false){	if($valor != false) { $_SESSION["domicilio.localidad"] = $valor; } return $_SESSION["domicilio.localidad"];	}
	function DomicilioLocalidadClave($valor = false){ if($valor != false) { $_SESSION["domicilio.localidad.clave"] = $valor; } return $_SESSION["domicilio.localidad.clave"];	}
	function DomicilioMunicipio($valor = false){ if($valor != false) { $_SESSION["domicilio.municipio"] = $valor; } return $_SESSION["domicilio.municipio"];	}
	function DomicilioMunicipioClave($valor = false){ if($valor != false) { $_SESSION["domicilio.municipio.clave"] = $valor; } return $_SESSION["domicilio.municipio.clave"];	}
	function DomicilioEstadoClaveABC($valor = false){ if($valor != false) { $_SESSION["domicilio.estado.clave.abc"] = $valor; } return $_SESSION["domicilio.estado.clave.abc"];	}
	function DomicilioEstadoClaveSIC($valor = false){ if($valor != false) { $_SESSION["domicilio.estado.clave.sic"] = $valor; }	return $_SESSION["domicilio.estado.clave.sic"];	}
	function DomicilioEstadoClaveNum($valor = false){ if($valor != false) { $_SESSION["domicilio.estado.clave.num"] = $valor; } return $_SESSION["domicilio.estado.clave.num"];	}
	function DomicilioEstado($valor = false){ if($valor != false) { $_SESSION["domicilio.estado"] = $valor; } return $_SESSION["domicilio.estado"]; }
	function DomicilioCodigoPostal($valor = false){ if($valor != false) { $_SESSION["domicilio.cp"] = $valor; } return $_SESSION["domicilio.cp"]; 	}
	function DomicilioCalle(){ return $_SESSION["domicilio.calle"]; }
	function DomicilioNumeroInterior(){ return $_SESSION["domicilio.numero.int"]; }
	function DomicilioNumeroExterior(){ return $_SESSION["domicilio.numero.ext"]; }
	function getFechaActual(){ return fechasys(); }
	function getNombreUsuario(){ $xUsr	= new cSystemUser(); return $xUsr->getNombreCompleto();	}
	function getCajaLocal(){ return $_SESSION["domicilio.cajalocal"];  }
	function getNombreDePais(){ return $_SESSION["domicilio.pais"]; }
	function DomicilioColonia(){ return $_SESSION["domicilio.colonia"]; }
	function getOficialDeCumplimiento(){ return $_SESSION["oficial.aml"]; }
	function getListadoDePersonasBuscadas($arrDatos, $DModel = array(), $useSound = false){
	
		//primerapellido segundoapellido nombre
		$ByPaterno		= "";
		$ByMaterno		= "";
		$ByNombre		= "";
		$ByCurp			= "";
		$ByOtros		= "";
		$sound			= ($useSound == true) ? "SOUNDS" : "";
		$CModel			= count($DModel);
		$sels			= "";
		$ql				= new MQL();
		$factores		= 0;
		if(isset($arrDatos["AP"])) {
			 if(trim($arrDatos["AP"]) != ""){
				$ByPaterno = " AND ( `socios_general`.`apellidopaterno` $sound LIKE '%" . $arrDatos["AP"] . "%' ) ";
				$factores++;
			 }
		}
		if(isset($arrDatos["AM"])) {
			if(trim($arrDatos["AM"]) != ""){
				$ByMaterno = " AND ( `socios_general`.`apellidomaterno` $sound LIKE '%" . $arrDatos["AM"] . "%' ) ";
				$factores++;
			}
		}
		if(isset($arrDatos["N"])) {
			if(trim($arrDatos["N"]) != ""){
				$ByNombre = " AND ( `socios_general`.`nombrecompleto` $sound LIKE '%" . $arrDatos["N"] . "%' ) ";
				$factores++;
			}
		}
		if(isset($arrDatos["otros"])) { 
			$ByOtros = $arrDatos["otros"];
			$factores++;
		}
		
		$sql	= "SELECT
		`socios_general`.`codigo`          AS `codigo`,
		
		`socios_general`.`apellidopaterno` AS `AP`,
		`socios_general`.`apellidomaterno` AS `AM`,
		`socios_general`.`nombrecompleto`  AS `N`,
	CONCAT(`socios_general`.`apellidopaterno`, ' ',	`socios_general`.`apellidomaterno`, ' ',`socios_general`.`nombrecompleto`)  AS `completo`,
		`socios_general`.`curp`
		FROM
		`socios_general` `socios_general`
		WHERE `socios_general`.`codigo` != 0
		$ByCurp $ByMaterno $ByNombre $ByPaterno $ByOtros
		ORDER BY
	
		`socios_general`.`apellidopaterno`,
		`socios_general`.`apellidomaterno`,
		`socios_general`.`nombrecompleto`
		LIMIT 0,100";
		//setLog($sql);
		
		$rows = array();
		$data = $ql->getDataRecord($sql);
		if($factores > 0){
			if($CModel == 0){
				$rows	= $data;// unset($data);
			} else {
				foreach ($data as $rw){
					$rwTMP	= array();
					$rwTMP["codigo"]	= $rw["codigo"];
					$rwTMP["curp"]		= $rw["curp"];
					foreach ($DModel as $indice => $traduccion){
						$rwTMP[$traduccion]		= $rw[$indice]; //setLog("$rwTMP[$traduccion]		= $rw[$indice]");
					}
					$rows[] = $rwTMP;
				}
				
			}
		}
		return $rows;
	}	
}

class cCantidad {
	private $mValor		= 0;
	private $mNumero	= 0;
	private $mArrData	= array();
	private $mMoneda	= "MXN";
	function __construct($monto = 0){
		$xT	= new cTipos();
		$this->mValor	= $xT->cFloat($monto);
		$this->mMoneda	= AML_CLAVE_MONEDA_LOCAL;
	}
	function moneda($cantidad = false){ 
		return ($cantidad === false) ? getFMoney($this->mValor) : getFMoney($cantidad);
	}
	function letras($monto = false){
		$monto	= (setNoMenorQueCero($monto) <= 0) ? $this->mValor : $monto; 	
		return convertirletras($monto, $this->mMoneda);
	}
	function v(){ return setNoMenorQueCero($this->mValor, 2); }
	function diff($cantidad){
		$xT	= new cTipos();
		$cantidad	= $xT->cFloat($cantidad);
		return $xT->cFloat(($this->mValor - $cantidad),2);
	}
	function set($arrValores){
		if(is_array($arrValores)){
			$xT				= new cTipos();
			$this->mValor	= (isset($arrValores[SYS_MONTO])) ? $xT->cFloat($arrValores[SYS_MONTO]) : 0;
			$this->mNumero	= (isset($arrValores[SYS_NUMERO])) ? $xT->cFloat($arrValores[SYS_NUMERO]) : 0;
			$this->mMoneda	= (isset($arrValores[SYS_MONEDA])) ? $arrValores[SYS_MONEDA] : AML_CLAVE_MONEDA_LOCAL;
			$this->mArrData	= $arrValores;
		}
	}
	function getOtros($v){ return (isset($this->mArrData[$v])) ? $this->mArrData[$v] : null; }
	function getClaveDeMoneda(){ return $this->mMoneda; }
	function getNumero(){ return $this->mNumero; }
	function getFicha(){
		return "<div class='tx1'><div class='cantidad-moneda' style='min-width:24%'>" . $this->moneda() . "</div><div class='cantidad-letras' style='min-width:74%'>" . $this->letras() . "</div></div>";
	}
}

/*================================================================= CLASE PARA MIGRAR ============================================*/


function unidad($numero){
	$numero 	= intval($numero);
	$numu 		= "";
	
	$arrN["pt"]	= array("","um","dois","três","quatro","cinco","seis","sete","oito","nove");
	$arrN["es"]	= array("","un","dos","tres","cuatro","cinco","seis","siete","ocho","nueve");
	$arrN["en"] = array("","one", "two","three", "four", "five", "six", "seven", "eight", "nine");
	
	$nums		= $arrN[SAFE_LANG];
	
	$numu		= strtoupper($nums[$numero]);
	return $numu;
}

function decena($numdero){
	$arrN["pt"]	= array(10 => "dez",11 => "onze",12 => "dezessete",13 => "treze",14 => "quatorze",15 => "quinze",16 => "dezesseis",17 => "dezessete",18 => "dezoito",19 => "dezenove",
			20 => "vinte",21 => "vinte e um",22 => "vinte e dois",23 => "vinte e três",24 => "vinte e quatro",25 => "vinte e cinco",26 => "vinte e seis",27 => "vinte e sete",28 => "vinte e oito",29 => "vinte e nove",
			30 => "trinta",40 => "quarenta",50 => "cinquenta",60 => "sessenta",70 => "setenta",80 => "oitenta",90 => "noventa");
	$arrN["es"]	= array(10 =>"diez",11 =>"once",12 => "doce",13 => "trece",14 => "catorce",15 => "quince",16 => "dieciseis",17 => "diecisiete",18 => "dieciocho",19 => "diecinueve",
			20 => "veinte", 21 => "veintiuno",22 => "veintidos",23 => "veintitres", 24 =>"veinticuatro",25 => "veinticinco",26 =>"veintiseis",27 => "veintisiete",28 => "veintiocho",29 => "veintinueve",
			30 => "treinta", 40 => "cuarenta", 50 => "cincuenta",60 => "sesenta", 70 => "setenta",80 => "ochenta",90 => "noventa");
	$arrN["en"] = array(10 => "ten", 11 =>"eleven",12 => "twelve", 13 => "thirteen", 14 => "fourteen", 15 => "fifteen", 16 => "sixteen",17 =>"seventeen", 18 => "eighteen", 19 => "nineteen",
			20 => "twenty", 21 => "twenty-one", 22 => "twenty-two",23 => "twenty-three",24 =>"twenty-four",25=>"twenty-five", 26 => "twenty-six", 27=> "twenty-seven",28 => "twenty-eight", 29=>"twenty-nine",
			30 => "thirty",40=> "forty",50 => "fifty",60 =>"sixty",70=> "seventy",80 => "eighty", 90=>"ninety");
	$nums		= $arrN[SAFE_LANG];
	$arrY		= array( "en" => " & ", "pt" => " e ", "es" => " Y ");
	$Y			= $arrY[SAFE_LANG];
	
	if ($numdero >= 90 && $numdero <= 99)
	{
		$numd = strtoupper($nums[90]);
		if ($numdero > 90)
			$numd = $numd . $Y  .(unidad($numdero - 90));
	}
	elseif ($numdero >= 80 && $numdero <= 89)
	{
		$numd = strtoupper($nums[80]);
		if ($numdero > 80)
			$numd = $numd. $Y .(unidad($numdero - 80));
	}
	elseif ($numdero >= 70 && $numdero <= 79)
	{
		$numd = strtoupper($nums[70]);
		if ($numdero > 70)
			$numd = $numd. $Y .(unidad($numdero - 70));
	}
	elseif ($numdero >= 60 && $numdero <= 69)
	{
		$numd = strtoupper($nums[60]);
		if ($numdero > 60)
			$numd = $numd. $Y .(unidad($numdero - 60));
	}
	elseif ($numdero >= 50 && $numdero <= 59)
	{
		$numd = strtoupper($nums[50]);
		if ($numdero > 50)
			$numd = $numd. $Y .(unidad($numdero - 50));
	}
	elseif ($numdero >= 40 && $numdero <= 49)
	{
		$numd = strtoupper($nums[40]);
		if ($numdero > 40)
			$numd = $numd. $Y .(unidad($numdero - 40));
	}
	elseif ($numdero >= 30 && $numdero <= 39)
	{
		$numd = strtoupper($nums[30]);
		if ($numdero > 30)
			$numd = $numd. $Y .(unidad($numdero - 30));
	}
	elseif ($numdero >= 20 && $numdero <= 29)
	{
		$numd = strtoupper($nums[$numdero]);

	}
	elseif ($numdero >= 10 && $numdero <= 19)
	{
		$numd = strtoupper($nums[$numdero]);

	}
	else
		$numd = unidad($numdero);
	return $numd;
}

function centena($numc){
	if ($numc >= 100)
	{
		$arrN["pt"]	= array(100 => "cem ",200 => "duzentos ",300 => "trezentos ",400 => "quatrocentos ",500 => "quinhentos ",600 => "seiscentos ",700 => "setecentos ",800 => "oitocentos ",900 => "novecentos ");
		$arrN["es"]	= array(100 => "cien ",200 => "doscientos ",300 => "trescientos ",400 => "cuatrocientos ",500 => "quinientos ",600 => "seiscientos ",700 => "setecientos ",800 => "ochocientos ",900 => "novecientos ");
		$arrN["en"] = array(100 =>"one hundred ",200 => "two hundred ",300 => "three hundred ",400 => "four hundred ",500 => "five hundred ",600 => "six hundred ",700 => "seven hundred ",800 => "eight hundred ",900 => "nine hundred ");
		$nums		= $arrN[SAFE_LANG];
		
		if ($numc >= 900 && $numc <= 999)
		{
			$numce = strtoupper($nums[900]);
			if ($numc > 900)
				$numce = $numce.(decena($numc - 900));
		}
		elseif ($numc >= 800 && $numc <= 899)
		{
			$numce = strtoupper($nums[800]);
			if ($numc > 800)
				$numce = $numce.(decena($numc - 800));
		}
		elseif ($numc >= 700 && $numc <= 799)
		{
			$numce = strtoupper($nums[700]);
			if ($numc > 700)
				$numce = $numce.(decena($numc - 700));
		}
		elseif ($numc >= 600 && $numc <= 699)
		{
			$numce = strtoupper($nums[600]);
			if ($numc > 600)
				$numce = $numce.(decena($numc - 600));
		}
		elseif ($numc >= 500 && $numc <= 599)
		{
			$numce = strtoupper($nums[500]);
			if ($numc > 500)
				$numce = $numce.(decena($numc - 500));
		}
		elseif ($numc >= 400 && $numc <= 499)
		{
			$numce = strtoupper($nums[400]);
			if ($numc > 400)
				$numce = $numce.(decena($numc - 400));
		}
		elseif ($numc >= 300 && $numc <= 399)
		{
			$numce = strtoupper($nums[300]);
			if ($numc > 300)
				$numce = $numce.(decena($numc - 300));
		}
		elseif ($numc >= 200 && $numc <= 299)
		{
			$numce = strtoupper($nums[200]);
			if ($numc > 200)
				$numce = $numce.(decena($numc - 200));
		}
		elseif ($numc >= 100 && $numc <= 199)
		{
			$numce = strtoupper($nums[100]);
			if ($numc == 100){
				
			} else {
				if(SAFE_LANG == "es"){
					$numce = "CIENTO ".(decena($numc - 100));
				} else {
					$numce = $numce.(decena($numc - 100));
				}
			}
		}
	}
	else
		$numce = decena($numc);

	return $numce;
}

function miles($nummero){
	$arrM		= array( "en" => "thousand", "pt" => "MIL", "es" => "MIL");
	$mMil		= strtoupper($arrM[SAFE_LANG]);
	
	if ($nummero >= 1000 && $nummero < 2000){
		$numm = "$mMil ".(centena($nummero%1000));
		if(SAFE_LANG == "en"){
			$numm = "ONE $mMil".(centena($nummero%1000));
		}
	}
	if ($nummero >= 2000 && $nummero <10000){
		$numm = unidad(floor($nummero/1000))." $mMil ".(centena($nummero%1000));
	}
	if ($nummero < 1000)
		$numm = centena($nummero);

	return $numm;
}

function decmiles($numdmero){
	$arrM		= array( "en" => "THOUSAND", "pt" => "MIL", "es" => "MIL");
	$arr10K		= array( "en" => "TEN THOUSAND ", "pt" => "DEZ MIL ", "es" => "DIEZ MIL ");
	$mMil		= $arrM[SAFE_LANG];
	
	
	if ($numdmero == 10000)
		$numde 		= $arr10K[SAFE_LANG];
	if ($numdmero > 10000 && $numdmero <20000){
		$numde = decena(floor($numdmero/1000))." $mMil ".(centena($numdmero%1000));
	}
	if ($numdmero >= 20000 && $numdmero <100000){
		$numde = decena(floor($numdmero/1000))."  $mMil ".(miles($numdmero%1000));
	}
	if ($numdmero < 10000)
		$numde = miles($numdmero);

	return $numde;
}

function cienmiles($numcmero){
	$arrM			= array( "en" => "THOUSAND", "pt" => "MIL", "es" => "MIL");
	$arr100K		= array( "en" => "HUNDRED THOUSAND ", "pt" => "CEN MIL ", "es" => "CIEN MIL ");
	$mMil			= $arrM[SAFE_LANG];
	
	if ($numcmero == 100000)
		$num_letracm 	= $arr100K[SAFE_LANG];
		
	if ($numcmero >= 100000 && $numcmero <1000000){
		$num_letracm = centena(floor($numcmero/1000))." $mMil ".(centena($numcmero%1000));
	}
	if ($numcmero < 100000)
		$num_letracm = decmiles($numcmero);
	return $num_letracm;
}

function millon($nummiero){
	$arrM			= array( "en" => "MILLIONS", "pt" => "MILHÕES", "es" => "MILLLONES");
	$arr10K10K		= array( "en" => "ONE MILLION ", "pt" => "UM MILHÕES ", "es" => "UN MILLON");
	$mMil			= $arrM[SAFE_LANG];
	
	if ($nummiero >= 1000000 && $nummiero <2000000){
		$num_letramm = $arr10K10K[SAFE_LANG];
		$num_letramm = "$num_letramm ".(cienmiles($nummiero%1000000));
	}
	if ($nummiero >= 2000000 && $nummiero <10000000){
		$num_letramm = unidad(floor($nummiero/1000000))." $mMil ".(cienmiles($nummiero%1000000));
	}
	if ($nummiero < 1000000)
		$num_letramm = cienmiles($nummiero);

	return $num_letramm;
}

function decmillon($numerodm){
	$arrM			= array( "en" => "MILLIONS", "pt" => "MILHÕES", "es" => "MILLLONES");
	$arr10K10K		= array( "en" => "TEN MILLION ", "pt" => "DEZ MILHÕES ", "es" => "DIEZ MILLONES");
	$mMil			= $arrM[SAFE_LANG];
	
	if ($numerodm == 10000000)
		$num_letradmm	= $arr10K10K[SAFE_LANG];
		//$num_letradmm 	= "DIEZ MILLONES";
	if ($numerodm > 10000000 && $numerodm <20000000){
		$num_letradmm = decena(floor($numerodm/1000000))." $mMil ".(cienmiles($numerodm%1000000));
	}
	if ($numerodm >= 20000000 && $numerodm <100000000){
		$num_letradmm = decena(floor($numerodm/1000000))." $mMil ".(millon($numerodm%1000000));
	}
	if ($numerodm < 10000000)
		$num_letradmm = millon($numerodm);

	return $num_letradmm;
}
/*TOD: Actualizar a varios idiomas*/
function cienmillon($numcmeros){
	if ($numcmeros == 100000000)
		$num_letracms = "CIEN MILLONES";
	if ($numcmeros >= 100000000 && $numcmeros <1000000000){
		$num_letracms = centena(floor($numcmeros/1000000))." MILLONES ".(millon($numcmeros%1000000));
	}
	if ($numcmeros < 100000000)
		$num_letracms = decmillon($numcmeros);
	return $num_letracms;
}

function milmillon($nummierod){
	if ($nummierod >= 1000000000 && $nummierod <2000000000){
		$num_letrammd = "MIL ".(cienmillon($nummierod%1000000000));
	}
	if ($nummierod >= 2000000000 && $nummierod <10000000000){
		$num_letrammd = unidad(floor($nummierod/1000000000))." MIL ".(cienmillon($nummierod%1000000000));
	}
	if ($nummierod < 1000000000)
		$num_letrammd = cienmillon($nummierod);

	return $num_letrammd;
}


function convertirletras($numero, $moneda =AML_CLAVE_MONEDA_LOCAL){
	$numero			= str_replace(",", "", $numero);
	$fin			= OPERACION_MONEDA_TERMINO;
	$nombreMoneda	= OPERACION_MONEDA_NOMBRE;
	if($moneda != AML_CLAVE_MONEDA_LOCAL){
		$xMn		= new cMonedas($moneda);
		$xMn->init();
		$nombreMoneda	= $xMn->getNombre();
		$fin			= ""; //$xMn->getPais();
	}
	$numero 		= number_format(floatval($numero), 2, '.', '');
	$padar 			= explode(".", $numero);
	$numf 			= milmillon($padar[0]);
	$cents			= intval($padar[1]);
	return $numf." $nombreMoneda $cents/100 $fin";
}

function convertirletras_porcentaje($numero){
	//$numero			= $numero * 100;
	$numero 		= number_format(floatval($numero), 2, '.', '');

	$padar 			= explode(".", $numero);
	$numf 	= milmillon($padar[0]);
	$cents	= milmillon(intval($padar[1]));
	return $numf." PUNTO $cents POR CIENTO";
}

function getEsModuloMostrado($tipo_de_usuario, $contexto = false){
	$acceder		= false;
	$lvl			= getUsuarioActual(SYS_USER_TIPO);
	//$xUsr			= new cSystemUser();
	
	
	if( OPERACION_LIBERAR_ACCIONES == true OR MODO_DEBUG == true){
		$acceder	= true;
		
	} else {
		if($lvl == $tipo_de_usuario){
			$acceder	= true;
		}
	}
	//acceso denegado
	if($tipo_de_usuario == USUARIO_TIPO_CONTABLE AND MODULO_CONTABILIDAD_ACTIVADO == false ){
		$acceder		= false;
	}
	if($tipo_de_usuario == USUARIO_TIPO_OFICIAL_AML AND MODULO_AML_ACTIVADO == false){
		$acceder		= false;
	}
	if($tipo_de_usuario == USUARIO_TIPO_OFICIAL_CRED AND MODULO_SEGUIMIENTO_ACTIVADO == false){
		$acceder		= false;
	}
	
	switch ($contexto){
		case MMOD_AML:
			break;
		case MMOD_TESORERIA:
			
			if($lvl == USUARIO_TIPO_CAJERO OR $lvl == 5){
				$acceder	= true;
			} else {
				$acceder	= false;
			}
			break;
	}
	if(MODO_DEBUG == true){
		$acceder	= true;
	}
	return $acceder;
}
function getSePuedeMostrar($contexto = false, $accion = false){
	$tipo_de_usuario	= getUsuarioActual(SYS_USER_NIVEL);
	$acceder			= false;
	
	if(MODO_CORRECION == true OR MODO_CORRECION == true OR MODO_DEBUG == true){
		$acceder	= true;
	} else {
		if($tipo_de_usuario == USUARIO_TIPO_OFICIAL_AML AND $contexto == MMOD_AML ){
			$acceder	= true;
		}
	}
	return $acceder;
}
function cors() {
	// Allow from any origin
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}
	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		exit(0);
	}
}

class cFolios {
	private $mQ		= null;
	function __construct(){
		$this->mQ	= new MQL();
		$sql		= "SELECT COUNT(`idgeneral_folios`) AS `items` FROM `general_folios`";
		$folios		= $this->mQ->getDataRow($sql);
		if(isset($folios["items"])){
			if($folios["items"] >= 4000){
				$this->mQ->setRawQuery("CALL `sp_setFoliosAlMaximo`");
			}
		} else {
			$this->mQ->setRawQuery("CALL `sp_setFoliosAlMaximo`");
		}
	}
	function getClaveDeRecibo(){
		$datos	= $this->mQ->getDataRow("SELECT getUltimoRecibo() AS 'folio'");
		$item	= 1;
		if(isset($datos["folio"])){
			$item	= $datos["folio"];
		}
		return $item;
	}
	function getClaveDePersonas($reservar = true, $comparar = false){
		//SELECT idgeneral_folios, numerooperacion, numerocredito, , numerocontrato, numeroestadistico, numerorecibo, numerogposolidario, polizacontable
		$campo		=  "numerosocio";
		$sumar		= '1';
		$item		= 1;
    	if($comparar != false){
    		$datos	= $this->mQ->getDataRow("SELECT COUNT(*) AS `existentes` FROM `general_folios` WHERE $campo = $comparar");
    		if(isset($datos["existentes"]) ){
    			//existe y actualizar
    			$folios	= $this->mQ->getDataRow("SELECT COALESCE((MAX($campo)+$sumar),1) AS `folio` FROM `general_folios`");
    			$item	= $folios["folio"];
    			$folios	= null;
    		} else {
    			//si no existe.- Item es igual al folio
    			$item	= $comparar;
    		}
    		$datos		= null;
    	} else {
    		$folios	= $this->mQ->getDataRow("SELECT COALESCE((MAX($campo)+$sumar),1) AS `folio` FROM `general_folios`");
    		$item	= $folios["folio"];
    		$folios	= null;    		
    	}
    	if($reservar == true){
    		$this->mQ->setRawQuery("INSERT INTO `general_folios`($campo) VALUES ($item)");
    	}
    	return $item;
	}
	function getClaveDeOperaciones($reservar = true, $comparar = false){
		//SELECT idgeneral_folios, , numerocredito, , numerocontrato, numeroestadistico, numerorecibo, numerogposolidario, polizacontable
		$campo		= "numerooperacion";
		$sumar		= '1';
		$item		= 1;
		if($comparar != false){
			$datos	= $this->mQ->getDataRow("SELECT COUNT(*) AS `existentes` FROM `general_folios` WHERE $campo = $comparar");
			if(isset($datos["existentes"]) ){
				//existe y actualizar
				$folios	= $this->mQ->getDataRow("SELECT COALESCE((MAX($campo)+$sumar),1) AS `folio` FROM `general_folios`");
				$item	= $folios["folio"];
				$folios	= null;
			} else {
    			//si no existe.- Item es igual al folio
    			$item	= $comparar;
    		}
			$datos		= null;
		} else {
			$folios	= $this->mQ->getDataRow("SELECT COALESCE((MAX($campo)+$sumar),1) AS `folio` FROM `general_folios`");
			$item	= $folios["folio"];
			$folios	= null;
		}
		if($reservar == true){
			$this->mQ->setRawQuery("INSERT INTO `general_folios`($campo) VALUES ($item)");
		}
		return $item;
	}	
	function __destruct(){ $this->mQ	= null;	}
}

function setCambio($tabla, $campo, $clave, $antes, $despues){
	if($antes == $despues){
		return false;
	}
	$xT	= new cSistemas_modificados();
	$xT->idobjeto($tabla);
	$xT->identificador($clave);
	$xT->idsistemas_modificados("NULL");
	$xT->idsubobjeto($campo);
	$xT->idtipoobjeto("T"); //T=TABLA
	$xT->idusuario(getUsuarioActual());
	$xT->tiempo(time());
	$xT->v_antes($antes);
	$xT->v_despues($despues);
	$res	= $xT->query()->insert()->save();
	return ($res === false) ? false : true;
}


/**
 * Clase que Trabaja las distintas bases de Registros en SAFE
 * @version 1.0.02
 * @package common
 * @subpackage core
 */
class cBases{
	private $mCodigoDeBase	= false;
	private $mAMembers		= false;
	private $mMessages		= "";
	private $mTipo			= "";
	private $mInit			= false;
	private $mIDCache		= "";
	private $mTabla			= "eacp_config_bases_de_integracion";
	private $mClave			= false;
	
	public $BASE_CREDITOS_ESTADO_CUENTA	= 1000;
	public $BASE_MVTOS_ELIMINAR			= 10019;
	public $BASE_MVTOS_RECIBO			= 1001;
	
	public $BASE_ESTADO_APORTACIONES	= 101;
	public $BASE_IVA_OTROS				= 7013;
	
	function __construct($codigo = false){ $this->setClave($codigo); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cEacp_config_bases_de_integracion();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave			= $xT->codigo_de_base()->v();
			$this->mCodigoDeBase	= $this->mClave;
			$this->mTipo			= $xT->tipo_de_base()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit			= true;
			$xT 					= null;
		}
		
		if($this->mCodigoDeBase>0){
			$this->getMembers_InArray();
		}
		return $this->mInit;
	}
	function setClave($codigo = false){ 
		$this->mCodigoDeBase	= setNoMenorQueCero($codigo);
		$this->mClave			= $this->mCodigoDeBase;
		$this->setIDCache($this->mClave);
	}
	function getMembers_InArray($ConAfectacion = false, $base = false){
		$xCache			= new cCache();
		$base			= setNoMenorQueCero($base);
		$base			= ($base <= 0 ) ? $this->mCodigoDeBase : $base;
		$this->mCodigoDeBase	= $base;
		$IDCache		= ($ConAfectacion == true) ? "base-arr-ca-id-$base" : "base-arr-sa-$base";
		$members		= $xCache->get($IDCache);
		$this->mAMembers= array();
		if($members === null){
			$sql 		= "SELECT `codigo_de_base`, `miembro`, `afectacion` FROM `eacp_config_bases_de_integracion_miembros` WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$base)";
			$xQL		= new MQL();
			$rs 		= $xQL->getDataRecord($sql);
			
			foreach($rs as $rw){ 
				$this->mAMembers[] = ($ConAfectacion == true ) ? $rw["miembro"] . "-" . $rw["afectacion"] : $rw["miembro"];
			}
			$xCache->set($IDCache, $this->mAMembers);
			$this->mInit		= true;
		} else {
			//si el cache es array y es valido
			if(is_array($members)){
				$this->mAMembers	= $members;
				$members			= null;
				$this->mInit		= true;
			} else {
				$this->mInit		= false;
			}
		}
		return $this->mAMembers;
	}
	function getIsMember($member){
		$stat	= false;
		if (  $this->mInit == false ){
			$this->getMembers_InArray();
		}
		if ( in_array($member, $this->mAMembers) ){
			$stat = true;
		}
		return $stat;
	}
	function getEsDeOperaciones(){ return ($this->mTipo == "de_operaciones") ? true : false; }
	/**
	 * Funcion que retorna una base de mvtos clasificado en socio@document
	 * @param	string	$AndWhere		Se refiere a los Filtros extras en la clausula WHERE
	 * @param	boolean	$IncludeDocto	Indica si se incluye el Documento Afectado como Filtro extra, por default es TRUE
	 * @return	array	Base de Movimientos
	 */
	function getBaseMvtosInArray($AndWhere = "", $IncludeDocto = true){
		$arr    		= array();
		
		
		$GByDocto		= " , `operaciones_mvtos`.`docto_afectado` ";
		$FByDocto		= " `operaciones_mvtos`.`docto_afectado`, ";

		if ( $IncludeDocto == false ){
			$GByDocto	= "";
			$FByDocto	= "";
		}
		$sql    = "SELECT
		`operaciones_mvtos`.`socio_afectado`,
		$FByDocto
		SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS `monto`
		FROM
		`operaciones_mvtos` `operaciones_mvtos`
		INNER JOIN `eacp_config_bases_de_integracion_miembros`
		`eacp_config_bases_de_integracion_miembros`
		ON `operaciones_mvtos`.`tipo_operacion` =
		`eacp_config_bases_de_integracion_miembros`.`miembro`
		WHERE
		(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = "  . $this->mCodigoDeBase . ")
		$AndWhere
		GROUP BY
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
		`operaciones_mvtos`.`socio_afectado`
		$GByDocto
		ORDER BY
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` ";
		$xQL				= new MQL();
		$rs  = $xQL->getDataRecord($sql);
		
		foreach ( $rs as $rw ){
			if ( $IncludeDocto == false ){
				$arr[ $rw["socio_afectado"] ] = $rw["monto"];
			} else {
							$arr[ $rw["socio_afectado"] . "@" . $rw["docto_afectado"] ] = $rw["monto"];
			}
		}
						$rs = null;
						return $arr;
	}
	function getMembers_InString($ConAfectacion = false, $base = false){
		$xCache		= new cCache();
		$base		= setNoMenorQueCero($base);
		$base		= ($base <= 0 ) ? $this->mCodigoDeBase : $base;
		$IDCache	= ($ConAfectacion == true) ? "base-str-ca-id-$base" : "base-str-sa-$base";
		$str		= $xCache->get($IDCache);;
		$itn		= 0;
		if($str === null){
			$arr		= $this->getMembers_InArray($ConAfectacion, $base);
			foreach($arr as $m => $v){
				$str	.= ($itn == 0) ? "'$v'" : ", '$v'";
				$itn++;
			}
		}
		return $str;
	}
	function getMessages($put = OUT_TXT){ $xH	= new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function addMember($clave, $afecta = 1,$subclase = 0, $descripcion = ''){
		$res	= true;
		$xQL	= new MQL();
		$base	= $this->mCodigoDeBase;
		$existe	= $xQL->getDataValue("SELECT COUNT(*) AS 'items' FROM `eacp_config_bases_de_integracion_miembros` WHERE `miembro`=$clave AND `codigo_de_base`=$base", "items");
		if($existe > 0){
			$this->mMessages	.= "WARN\tEl Item existe ( $base - $clave ) \r\n";
			$res	= false;
		} else {
			$res	= $xQL->setRawQuery("INSERT INTO `eacp_config_bases_de_integracion_miembros`(`codigo_de_base`,`miembro`,`afectacion`,`descripcion_de_la_relacion`,`subclasificacion`) VALUES ($base, $clave, $afecta, '$descripcion', $subclase)");
			$res	= ($res === false) ? false : true;
			//setLog("INSERT INTO `eacp_config_bases_de_integracion_miembros`(`codigo_de_base`,`miembro`,`afectacion`,`descripcion_de_la_relacion`,`subclasificacion`) VALUES ($base, $clave, $afecta, '$descripcion', $subclase)");
		}
		if($res == true){
			$this->setCuandoSeActualiza();
		}
		return $res;
	}
	private function setCuandoSeActualiza(){
		$xCache		= new cCache();
		$base		= $this->mCodigoDeBase;
		$xCache->clean("base-arr-ca-id-$base");
		$xCache->clean("base-arr-sa-$base");
	}
}
?>