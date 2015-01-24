<?php
use Enhance\Language;
//use Enhance\Language;
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package commons
 * 
 */
	include_once("core.config.inc.php");
	include_once("entidad.datos.php");
	include_once("core.init.inc.php");
	include_once("core.deprecated.inc.php");
	
	include_once("core.db.inc.php");
	include_once("core.db.dic.php");
	include_once("core.lang.inc.php");
	include_once("core.fechas.inc.php");
	
	@include_once("../libs/spyc.php");
	@include_once("../libs/open-flash-chart.php");
	@include_once("../libs/sql.inc.php");
	@include_once("../libs/dompdf/dompdf_config.inc.php");
	@include_once("../libs/gantti/gantti.php");
	//@include_once("../libs/guzzle/Client.php");
	@include_once("../reports/PHPReportMaker.php");
	
	//HTML Page.- Tipo de Objeto HTML
	define( "HP_FORM", 1);
	define( "HP_REPORT", 2);
	define( "HP_RECIBO", 3);
	define( "HP_RPTXML", 4);
	define( "HP_GRID", 5);
	define( "HP_SERVICE", 6);
	
	define( "HP_LABEL_SIZE", 22);
	define( "HP_FORM_MIN_SIZE", 65);
	define("HP_REPLACE_ID", "_REPLACE_ID_");
	//TODO: Verificar errores
function getRawHeader($xml = false, $out = OUT_DEFAULT, $replaceText = ""){
	$hd				= "";
	if(defined("EACP_PATH_LOGO")){
		$logo		= EACP_PATH_LOGO;
		$nombre		= EACP_NAME;
		$desc		= EACP_DESCRIPTION;
		$rfc		= EACP_RFC;
		$header		= "<h2>$nombre</h2><h4>$desc</h4><h4>$rfc</h4><hr /><!--OTHER_TEXT-->";
		if($xml == true){ $out = OUT_RXML; }
		$hd			= "";
		switch ($out){
			case OUT_RXML:
				break;
				$hd	= "<XHTML>$header</XHTML>";
			case OUT_EXCEL:
				$hd	= "<tr><th class=\"xl25\">$nombre</th></tr>
				<tr><th class=\"xl25\">$desc</th></tr>
				<tr><th class=\"xl25\">$rfc</th></tr>"; //<tr></tr>";
				break;
			case OUT_DOC:
				$hd	= $header; 
				if(MODO_DEBUG == true){  setLog("$header"); }
				break;
			default:
				$hd	= "<header><img src=\"$logo\" class=\"logo\" alt=\"logo\" style=\"margin-left: .5em; max-height: 4em; max-width: 4em; margin-top: 0 !important;	border-color: #808080; z-index: 100000 !important; }\" />$header</header>";
				break;
		}
		
		$hd		= str_replace("<!--OTHER_TEXT-->", $replaceText, $hd);
	}
		return $hd;
}
function getRawFooter($xml = false, $out = OUT_DEFAULT, $replaceText = ""){
	$hd				= "";
	if(defined("EACP_DOMICILIO_CORTO")){
		$domicilio_corto	= EACP_DOMICILIO_CORTO;
		$email				= EACP_MAIL;
		$telefono			= EACP_TELEFONO_PRINCIPAL; 
		$footer				= "<!--OTHER_TEXT--><hr /><h5>$domicilio_corto</h5><h5>$email | $telefono</h5>";
		
		if($xml == true){ $out = OUT_RXML; }
		
		$hd			= "";
		switch ($out){
			case OUT_RXML:
				$hd	= "<XHTML>$footer</XHTML>";
				break;
			case OUT_EXCEL:
				$hd	= "<tr><th class=\"xl25\">$domicilio_corto</th></tr><tr><th class=\"xl25\">$email | $telefono</th></tr>"; //<tr></tr>";
				break;
			case OUT_DOC:
				$hd	= $footer;
				break;
			default:
				$hd	= "<footer>$footer</footer>";
				break;
		}
		
		$hd		= str_replace("<!--OTHER_TEXT-->", $replaceText, $hd);
	}
	return $hd;
				
}

define("CABEZA_PAGINA", getRawHeader());
define("PIE_PAGINA", getRawFooter());


class cHObject {
	protected $mMessages	= "";
	protected $mTitle		= "";
	protected $mEncodeHtml	= false; 
	function __construct(){
		
	}
	/**
	 * Muestra un texto en un formato determinado
	 * @param string $mText
	 * @param string $Format
	 */
	function Out($mTexto = "", $Format = OUT_TXT){
		$ImgErr	= "<img src='../images/forms/error.png' />";
		$ImgOk	= "<img src='../images/forms/valid.png' />";
		$ImgWarn= "<img src='../images/forms/alert.png' />";
		$mText	= "";
		switch ( $Format ) {
			case OUT_HTML:
				//if($this->mEncodeHtml)
				$lineas	= explode("\n", $mTexto, 100);
				if(count($lineas) > 1){
					foreach($lineas as $linea){
						if(trim($linea) == "" ){
							
						} else {
							$linea	= htmlentities($linea);
							$linea	= str_replace("ERROR\t", $ImgErr, $linea);
							$linea	= str_replace("OK\t", $ImgOk, $linea);
							$linea	= str_replace("SUCESS\t", $ImgOk, $linea);
							$linea	= str_replace("WARN\t", $ImgWarn, $linea);
							$mText	.= "<li><a>$linea</a></li>";
						}
					}
					$mText	= "<ol class=\"rounded-list\">$mText</ol>";
				} else {
					unset($lineas); //destruir;
					$mText	= htmlentities($mTexto);
					$mText	= str_replace("", "\r", $mText);
					$mText	= str_replace("<br/>", "\n", $mText);
				}
				break;
			case OUT_TXT:
				$mText	= str_replace("<br />", "\r\n", $mTexto);
				$mText	= utf8_encode($mTexto);
				
				break;
			default:
				$mText	= str_replace("<br />", "\r\n", $mTexto);
				break;
		}
		
		return  $mText;
	}
	/**
	 * Obtiene un nombre limpio, para guardar como un archivo
	 * @param $mNombreArchivo
	 */
	function getNombreExportable( $mNombreArchivo ){
		$filename 	= $mNombreArchivo;
		$filename 	= str_replace(".php", "", $filename);
		$filename 	= str_replace("rpt_", "", $filename);
		$filename 	= str_replace("frm_", "", $filename);

		$filename 	= str_replace(".", "", $filename);
		$filename 	= str_replace("rpt", "", $filename);
		$filename 	= str_replace("frm", "", $filename);
		//$filename 	= str_replace("-", "", 	$filename);
		$filename 	= str_replace("___", "-", 	$filename);
		$filename 	= str_replace("__", "-", 	$filename);
		$filename 	= str_replace("_", "-", 	$filename);
		$filename 	= str_replace("---", "-", 	$filename);
		$filename 	= str_replace("--", "-", 	$filename);
		$filename 	= str_replace(" ", "", 	$filename);
		$xT			= new cTipos();
		$filename	= $xT->setNoAcentos($filename);
		return $filename;
	}
	function getTitulize($cadena){
		$cadena	= $this->getNombreExportable($cadena);
		$cadena	= str_replace("[", " ", $cadena);
		$cadena	= str_replace("]", " ", $cadena);
		$cadena	= str_replace("_", " ", $cadena);
		
		$cadena	= ucfirst($cadena);
		return $cadena;
	}
	function setExcelType($NombreDeArchivo){
		$iduser 	= $_SESSION["log_id"];
		$filename 	= $this->getNombreExportable( $NombreDeArchivo  );
	  	$filename 	= "$filename-" . date("YmdHi") . "-" .  $iduser . ".xls";
	  	
	  	header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=$filename");
		header("Pragma: no-cache");
		header("Expires: 0");

	}
	function getHAviso($text){
		$cssAviso	= "aviso";
		return "<p class=\"$cssAviso\">$text</p>";
	}
	
	function navigate($url = ""){
		$url		= SAFE_HOST_URL . $url;
		$req 		= file_get_contents($url);
		if(!$req){
			if(MODO_DEBUG == true){  setLog("Error al procesar la url : $url"); }
		}
	}
}
class cHExcel {
	private $mContent	= "";
	function addContent($html){
		$this->mContent .= $html;
	}
	function convertTable($sql, $titulo = "", $ret = false){
		$iduser		= getUsuarioActual();
		$filename 	= ($titulo == "") ? strtolower($_SERVER['SCRIPT_NAME']) : $titulo;
		$arrPurga 	= array("rpt_", "-", "rpt", ".php", "php", ".");
		$filename 	= str_replace($arrPurga, "", 	$filename);
		$filename 	= trim($filename);
		$arrPurga2 	= array(" ", "  ", "__", "___");
		$filename 	= str_replace($arrPurga2, "_", 	$filename);
		//$filename 	= ( substr($filename, 0,1) == "_") ? substr($filename,1) : $filename;
		
		$filename 	= $filename . "-" . date("Y_m_d_Hi") . "-" .  $iduser . ".xls";

		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$filename");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$cTbl = new cTabla($sql);
		$cTbl->setTipoSalida(OUT_EXCEL);
		
		$excel	= "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
		$excel	.= "\r\n<head>\r\n";
		$excel	.= "<!--[if gte mso 9]>\r\n";
		$excel	.= "<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>$filename</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>";
		$excel	.= "\r\n<![endif]-->\r\n";
		$excel	.= "</head>\r\n";
		$excel	.= "<body>\r\n";
		$excel	.= $this->mContent;
		$excel	.= $cTbl->Show();
		$excel	.= "\r\n</body>\r\n</html>\r\n";
		/*$excel	= "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
		$excel	.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$excel	.= "<html><head><meta http-equiv=\"Content-type\" content=\"text/html;charset=\"iso-8859-1\" />";
		$excel	.= "<style id=\"Classeur1_16681_Styles\"> </style> </head><body><div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\"> ";
		$excel	.= $cTbl->Show();
		$excel	.= "</div></body></html>";*/
		
		if($ret == true) {
			return utf8_encode($excel);
		} else {
			echo utf8_encode($excel);
		}
	}
	function render($ret = false, $titulo = ""){
		$iduser		= getUsuarioActual();
		$filename 	= ($titulo == "") ? strtolower($_SERVER['SCRIPT_NAME']) : $titulo;
		$arrPurga 	= array("rpt_", "-", "rpt", ".php", "php", ".");
		$filename 	= str_replace($arrPurga, "", 	$filename);
		$filename 	= trim($filename);
		$arrPurga2 	= array(" ", "  ", "__", "___");
		$filename 	= str_replace($arrPurga2, "_", 	$filename);
		$filename 	= $filename . "-" . date("Y_m_d_Hi") . "-" .  $iduser . ".xls";
		
		$this->mContent	= str_replace("<table>", "", $this->mContent);
		$this->mContent	= str_replace("<hr />", "", $this->mContent);
		$this->mContent	= str_replace("<table x:str border=0 style='border-collapse: collapse'>", "", $this->mContent);
		$this->mContent	= str_replace("<table x:str border=0   style='border-collapse: collapse' >", "", $this->mContent);
		
		$this->mContent	= str_replace("</table>", "", $this->mContent);
		$this->mContent	= str_replace("<h2>", "<tr><th class=\"xl25\">", $this->mContent);
		$this->mContent	= str_replace("</h2>", "</th></tr>", $this->mContent);
		
		
		$excel	= "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
		$excel	.= "\r\n<head>\r\n";
		$excel	.= "<!--[if gte mso 9]>\r\n";
		$excel	.= "<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>$filename</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>";
		$excel	.= "\r\n<![endif]-->\r\n";
		$excel	.= "</head>\r\n";
		$excel	.= "<body>\r\n";
		$excel	.= "<table x:str border=0 style='border-collapse: collapse'>\r\n";
		$excel	.= $this->mContent;
		$excel	.= "</table>\r\n";
		$excel	.= "\r\n</body>\r\n</html>\r\n";
		/*$excel	= "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
		 $excel	.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$excel	.= "<html><head><meta http-equiv=\"Content-type\" content=\"text/html;charset=\"iso-8859-1\" />";
		$excel	.= "<style id=\"Classeur1_16681_Styles\"> </style> </head><body><div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\"> ";
		$excel	.= $cTbl->Show();
		$excel	.= "</div></body></html>";*/
		
		if($ret == true) {
			return utf8_encode($excel);
		} else {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename");
			header("Pragma: no-cache");
			header("Expires: 0");			
			echo utf8_encode($excel);
		}		
	}
}
class cHPage {
	private $mHeader;
	private $mKeywords;
	private $mTitle				= "Default Page";
	private $mAuthor			= "";
	private $mDescription			= "";
	private $mCSS				= array();			//Array de CSS
	private $mBody				= "";				//Cuerpo
	private $mHead				= "";				//Encabezado
	private $mArrMeta			= array();			//Arrays de Propiedades de Encabezado
	private $mJSFiles			= array();			//Archivos JS en el encabezado
	private $mNoCache			= false;			//La Pagina puede ser cacheada?
	private $mHSnipt			= array();			//pedazos de codigo en el header
	//private $css			
	private $processed			= false;			//pagina procesada
	protected $mDefaultCSS			= true;
	protected $mTipoDePagina		= 1;
	protected $mNombreArchivo		= "";
	protected $mDevice			= "desktop";
	private $mGeneralCSS			= "/css/general.css";
	private $mPath				= "..";
	private $mAlto				= false;
	private $mAncho				= false;
	private $mEndScript			= "";
	private $mOnEnd				= false;
	private $mStyles			= "";
	
	//private $mTarget			= "desktop"; 
	function __construct($title = "", $TipoDePagina = HP_FORM, $NombreArch = "", $path = ".."){
		$xLng			= new cLang();		
		$keywords				= "page";
		$author					= "Balam Gonzalez Luis Humberto";
		$this->mTitle			= $xLng->getT($title);
		$this->mTipoDePagina	= $TipoDePagina;
		$this->mNombreArchivo	= $NombreArch;
		$token					= SAFE_VERSION . SAFE_REVISION;
		getIncludes($path, $TipoDePagina);
		$this->mPath	= $path;
		if( $this->mDefaultCSS == true ){
		//====================================
			switch($TipoDePagina){
				
			case HP_FORM:
				//$this->addCSS("$path/css/grid960.css");
				$this->addCSS($path . $this->mGeneralCSS);
				$this->addJsFile("$path/js/lang.js.php");
				$this->addJsFile("$path/js/general.js?$token");
				$this->addJsFile("$path/js/config.js.php");
				$this->addJsFile("$path/js/jquery/jquery.js");
				$this->addJsFile("$path/js/jquery/excanvas.js");
				$this->addJsFile("$path/js/jquery/jquery.cookie.js");
				$this->addJsFile("$path/js/base64.js");
				
				$this->addJsFile("$path/js/jquery/jquery.qtip.min.js");
				$this->addJsFile("$path/js/jquery/visualize.jQuery.js");
				$this->addJsFile("$path/js/jquery/jquery.accordion.js");
				
				$this->addJsFile("$path/js/picker.js");
				$this->addJsFile("$path/js/picker.date.js");
				$this->addJsFile("$path/js/picker.time.js");
												
				//$this->addJsFile("$path/js/jquery/imagesloaded.pkg.min.js");
				
				$this->addCSS("$path/css/formoid/formoid-default.css");
				$this->addCSS("$path/css/jquery.qtip.css");
				
				$this->addCSS("$path/css/picker/default.css");
				$this->addCSS("$path/css/picker/default.date.css");
				$this->addCSS("$path/css/picker/default.time.css");
				$this->addCSS("$path/css/visualize.css");
				$this->addCSS("$path/css/visualize-light.css");
				$this->addCSS("$path/css/font-awesome.min.css");
				$this->addCSS("$path/css/tinybox.css");
				$this->addJsFile("$path/js/tinybox.js");
				$this->addJsFile("$path/js/deprecated.js");
				$this->addJsFile("$path/js/md5.js");
				//$this->addJsFile("https://dl.dropboxusercontent.com/s/9gkr7jkgd7rctta/formas.js?token_hash=AAFQJVtVHodXcn08DLqlUBMA-rxX7Ux62u5hq-9W72oLEA&expiry=1399741290");
				//amaran
				$this->addJsFile("$path/js/jquery/jquery.amaran.min.js");
				$this->addCSS("$path/css/amaran.min.css");
				//$this->addCSS("$path/css/animate.min.css");
				//agregar panel
				$this->addJsFile("$path/js/jquery/jquery.jpanelmenu.min.js");
				//$this->addCSS("$path/css/font-awesome.min.css");
				
				if(defined("SAFE_LANG")){
					$jslang		= strtolower( SAFE_LANG );
					//$this->addJsFile("$path/js/jquery/localization/messages_$jslang.js");
					if( $jslang == "es"){
						$this->addJsFile("$path/js/picker-lang/es_ES.js");
					} else {
						if($jslang != "en"){
							$this->addJsFile("$path/js/picker-lang/". $jslang ."_" . strtoupper($jslang) . ".js");
						}
					}
				}
				if(defined("MODO_DEBUG")){
					if(MODO_DEBUG == true){
						$this->addJsFile("$path/js/dev.js");
					}
				}
				//validation
				//$this->addJsFile("$path/js/jquery/jquery.validate.min.js");
				//$this->addJsFile("$path/js/jquery/parsley.min.js");
				//$this->addJsFile("$path/js/jquery/parsley.remote.min.js");
				$this->addJsFile("$path/js/happy.js");
				$this->addJsFile("$path/js/happy.methods.js");
				$this->addJsFile("$path/js/xdate.js");
				$this->addJsFile("$path/js/xdate.i18n.js");
				
				$this->addCSS("$path/css/gantti/gantti.css");
			break;
			case HP_REPORT:
				$this->addCSS("$path/css/reporte.css");
				$this->addCSS("$path/css/visualize.css");
				$this->addCSS("$path/css/visualize-light.css");
				$this->addJsFile("$path/js/jquery/jquery.js");
				$this->addJsFile("$path/js/general.js?$token");
				$this->addJsFile("$path/js/reports.js");
				$this->addJsFile("$path/js/jquery/excanvas.js");
				$this->addJsFile("$path/js/jquery/visualize.jQuery.js");
				$this->addCSS("$path/css/gantti/gantti.css");
				break;
			case HP_RECIBO:
				$this->addCSS("$path/css/reporte.css");
				$this->addCSS("$path/css/recibo.css");
				
				$this->addCSS("$path/css/tinybox.css");
				$this->addJsFile("$path/js/tinybox.js");				
			case HP_RPTXML:
				break;
			case HP_GRID:
				//$this->addCSS("$path/css/grid960.css");
				$this->addCSS("$path/css/grid.css");
				$this->addCSS($path . $this->mGeneralCSS);
				$this->addJsFile("$path/js/jquery/jquery.js");
				$this->addJsFile(GRID_SOURCE . "javascript/javascript.js");
				$this->addJsFile(GRID_SOURCE . "server.php?client=all");
				$this->addHSnip("<script>HTML_AJAX.defaultServerUrl = '" . GRID_SOURCE. "server.php';	</script>");
			break;
		default:
			$this->addCSS($path . $this->mGeneralCSS);
			//$this->addJsFile("$path/js/jquery/jquery.js");
			break;
			}
		}
		if(MODO_DEBUG == true){
			
			// start profiling
			if(function_exists("xhprof_enable")){
			//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
			//$xhprof_data = xhprof_disable();
			}
						
		}
	}
	function process(){ }
	function get(){	}
	function show(){ }
	function setIncludeJQueryUI(){ $this->addCSS($this->mPath . "/css/jquery-ui/jquery-ui.css"); $this->addJsFile($this->mPath . "/js/jquery/jquery.ui.js"); }
	function getEncabezado($otroTexto = ""){ return getRawHeader(false, $this->mDevice , $otroTexto);	}
	function getPieDePagina($otroTexto = ""){	 return getRawFooter(false, $this->mDevice , $otroTexto); 	}
	function h1(){ return "<h1>" . $this->getTitle() . "</h1>";}
	function setArchivo($Archivo){ 	$this->mNombreArchivo	= $Archivo;	}
	function getJsBack($msg = "", $params = ""){
		$msg	= ($msg == "") ? "" : "alert('$msg');";
		$file	= $this->mNombreArchivo;
		$js		= "<script>$msg window.location = \"$file?$params\"; </script>";
		return $js;
	}
	function setNoDefaultCSS(){
		$this->mDefaultCSS	= false;
		unset($this->mCSS[$this->mPath . "/css/grid960.css"]);
		unset($this->mCSS[ $this->mPath . $this->mGeneralCSS ]);
		unset($this->mCSS[ $this->mPath . "/css/reporte.css" ]);
		unset($this->mCSS[ $this->mPath . "/css/recibo.css" ]);
	}
	function setBodyinit($OnLoadEvent = ""){
		$OnLoadEvent	= ( $OnLoadEvent != "") ? " onload='$OnLoadEvent' " : "";
		return "<body $OnLoadEvent>";
	}
	/**
	 * @deprecated since 2014.04.01
	 */
	function setBodyEnd(){ echo "</body>" . $this->mEndScript;	}
	/**
	 * @deprecated since 2014.04.01
	 */
	function end(){	echo "</html>";	}
	function init($jsEvent = "", $ret = false){ if($ret == false){ echo $this->getHeader() . $this->setBodyinit($jsEvent); } else { return $this->getHeader() . $this->setBodyinit($jsEvent); }	}
	function fin(){ echo "</body>" . $this->mEndScript . "</html>"; }
	function addCSS($strCSSFile = ""){ $this->mCSS[$strCSSFile] = $strCSSFile; return "<link href=\"$strCSSFile\" rel=\"stylesheet\">";	}
	function addScript(){	}
	function addJsFile($file){ $this->mJSFiles[$file]	= $file;	}
	function addObject($strObject	= ""){	}
	function addHSnip($html = ""){	$this->mHSnipt[]	= $html;}
	function addMeta($meta){	$this->mArrMeta[]	= $meta;	}
	function setNoCache(){
	// Don't use cache (required for Opera)
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		header('Expires: 0'); 											// rfc2616 - Section 14.21
		header('Last-Modified: ' . $now);
		header('Cache-Control: no-store, no-cache, must-revalidate'); 	// HTTP/1.1
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');	// HTTP/1.1
		header('Pragma: no-cache');
	}
	function addStyle($cnt){ $this->mStyles	.= $cnt; }
	function setTitle($title){ $xLg	= new cLang(); $this->mTitle	= $xLg->getT($title); }
	/**
	 * Obtiene el HEADER html
	 * @param boolean $force_refresh
	 * @return string
	 */
	function getHeader($force_refresh = false){
		if ($force_refresh == true){ $this->setNoCache();	}
		$metas	= "";
		$js		= "";
		$snipt	= "";
		$styl	= ($this->mStyles == "") ? "" : "<style>" . $this->mStyles . "</style>";
		if($this->mTipoDePagina == HP_GRID){
			$this->setNoDefaultCSS();
		}
		if(defined("EACP_CLAVE_DE_PAIS")){
			if(EACP_CLAVE_DE_PAIS == "MX"){
				$this->addJsFile( $this->mPath . "/js/mexico.js");
			}
		}
		/**
		 * Construye Css por el Array mCSS
		 */
		$Css	= "";
			foreach ($this->mCSS as $key => $value) {
				$Css .= "<link href=\"$value\" rel=\"stylesheet\">";
			}
			foreach ($this->mArrMeta as $key => $value) {
				$metas	.= "<meta $value >";
			}
			foreach ($this->mJSFiles as $key => $value) {
				$js	.= "<script src=\"" . $value . "\"></script>";
			}
			
			foreach ($this->mHSnipt as $key => $value) {
				$snipt	.= "$value";
			}
		switch( $this->mDevice ){
			case "desktop":
				$xhtml = "<!DOCTYPE html>
						<html>
						    <head>
						        <meta charset=\"utf-8\" />
						<title>" . $this->mTitle . "</title>
										$metas
										$Css
										$js
										$snipt
										$styl
										</head>	";				
				break;
			case "doc":
				$xhtml = "<html xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:w=\"urn:schemas-microsoft-com:office:word\" xmlns=\"http://www.w3.org/TR/REC-html40\">
						    <head><title>" . $this->mTitle . "</title> $metas <meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
										$snipt
										$styl
										</head>	";				
				break;
				
			default:
				$xhtml	= "<!DOCTYPE html>
						<html>
						    <head>
						        <meta charset=\"utf-8\" />
								<meta name=\"viewport\" content=\"initial-scale=1.0, maximum-scale=1.0, user-scalable=no\">
						        <title>" . $this->mTitle . "</title>
										        $metas
										        $Css
										        $js
										        $snipt
										        $styl
										        </head>";				
				break;
				
		}

		return $xhtml;
	}
	/**
	 * Incluye todos los archivos relacionados con el Sistema SAFE
	 * @param string $extPath
	 */
	function setIncludes($extPath = ".."){
		//getIncludes($extPath, $this->mTipoDePagina);
	}
	function setDevice($Device){
		$this->mDevice	= $Device;
	}
	function getTitle(){ return $this->mTitle; }
	function setTamannio($ancho = false, $alto = false){
		$this->mAlto		= $alto;
		$this->mAncho		= $ancho;
		$this->mEndScript	= ($alto == false) ? "<script>if(typeof Gen !=\"undefined\" ){var MyGen = new Gen(); MyGen.rz({ w: $ancho});}</script>" : "<script>if(typeof Gen !=\"undefined\" ){var MyGen = new Gen(); MyGen.rz({ w: $ancho, h: $alto});}</script>";
	}
	function lang($palabra, $palabra2 = ""){
		$xLng		= new cLang();
		return $xLng->getTrad($palabra, $palabra2); 
	}
	function local(){ $xL	= new cLocal(); 	return $xL;	}
	function getMessages($msgs){
		$xHO	= new cHObject();
		return $xHO->Out($msgs, OUT_HTML);
	}
	function goToPageError($iderror = DEFAULT_CODIGO_DE_ERROR, $out = OUT_HTML){
		if($out == OUT_HTML){
			header("location:../404.php?i=$iderror");
			exit();
		} else {
			$xErr			= new cError($iderror); $xErr->init();
			$arrResponse	= array( "codigo" => $iderror, "message" => $xErr->getDescription());
			exit (json_encode($arrResponse));
		}
	}

	function getTipoDeUsuario(){
		$xUsr	= new cSystemUser();
		$xUsr->init();
		return $xUsr->getNivel();
	}
	function isMobile(){
		$isMobile 	= false;
		$isBot 		= false;
		$ua 		= strtolower($_SERVER['HTTP_USER_AGENT']);
		$ac 		= (isset($_SERVER['HTTP_ACCEPT'])) ? strtolower($_SERVER['HTTP_ACCEPT']) : "";
		$ip 		= $_SERVER['REMOTE_ADDR'];
	
		$isMobile = strpos($ac, 'application/vnd.wap.xhtml+xml') !== false
		|| strpos($ua, 'sony') !== false
		|| strpos($ua, 'symbian') !== false
		|| strpos($ua, 'nokia') !== false
		|| strpos($ua, 'samsung') !== false
		|| strpos($ua, 'mobile') !== false
		|| strpos($ua, 'windows ce') !== false
		|| strpos($ua, 'epoc') !== false
		|| strpos($ua, 'opera mini') !== false
		|| strpos($ua, 'nitro') !== false
		|| strpos($ua, 'j2me') !== false
		|| strpos($ua, 'midp-') !== false
		|| strpos($ua, 'cldc-') !== false
		|| strpos($ua, 'netfront') !== false
		|| strpos($ua, 'mot') !== false
		|| strpos($ua, 'up.browser') !== false
		|| strpos($ua, 'up.link') !== false
		|| strpos($ua, 'audiovox') !== false
		|| strpos($ua, 'blackberry') !== false
		|| strpos($ua, 'ericsson,') !== false
		|| strpos($ua, 'panasonic') !== false
		|| strpos($ua, 'philips') !== false
		|| strpos($ua, 'sanyo') !== false
		|| strpos($ua, 'sharp') !== false
		|| strpos($ua, 'sie-') !== false
		|| strpos($ua, 'portalmmm') !== false
		|| strpos($ua, 'blazer') !== false
		|| strpos($ua, 'avantgo') !== false
		|| strpos($ua, 'danger') !== false
		|| strpos($ua, 'palm') !== false
		|| strpos($ua, 'series60') !== false
		|| strpos($ua, 'palmsource') !== false
		|| strpos($ua, 'pocketpc') !== false
		|| strpos($ua, 'smartphone') !== false
		|| strpos($ua, 'rover') !== false
		|| strpos($ua, 'ipaq') !== false
		|| strpos($ua, 'ipad') !== false
		|| strpos($ua, 'iphone') !== false
		|| strpos($ua, 'android') !== false
		|| strpos($ua, 'hpwos') !== false
		|| strpos($ua, 'au-mic,') !== false
		|| strpos($ua, 'alcatel') !== false
		|| strpos($ua, 'ericy') !== false
		|| strpos($ua, 'up.link') !== false
		|| strpos($ua, 'vodafone/') !== false
		|| strpos($ua, 'wap1.') !== false
		|| strpos($ua, 'wap2.') !== false;
	
		$isBot =  $ip == '66.249.65.39'
				|| strpos($ua, 'googlebot') !== false
				|| strpos($ua, 'mediapartners') !== false
				|| strpos($ua, 'yahooysmcm') !== false
				|| strpos($ua, 'baiduspider') !== false
				|| strpos($ua, 'msnbot') !== false
				|| strpos($ua, 'slurp') !== false
				|| strpos($ua, 'ask') !== false
				|| strpos($ua, 'teoma') !== false
				|| strpos($ua, 'spider') !== false
				|| strpos($ua, 'heritrix') !== false
				|| strpos($ua, 'attentio') !== false
				|| strpos($ua, 'twiceler') !== false
				|| strpos($ua, 'irlbot') !== false
				|| strpos($ua, 'fast crawler') !== false
				|| strpos($ua, 'fastmobilecrawl') !== false
				|| strpos($ua, 'jumpbot') !== false
				|| strpos($ua, 'googlebot-mobile') !== false
				|| strpos($ua, 'yahooseeker') !== false
				|| strpos($ua, 'motionbot') !== false
				|| strpos($ua, 'mediobot') !== false
				|| strpos($ua, 'chtml generic') !== false
				|| strpos($ua, 'nokia6230i/. fast crawler') !== false;
		return $isMobile;
	}	
}

class cHTabs{
	protected $mArrTabs = array();
	protected $mWidth	= "100%";
	protected $mHeight	= "100%";
	protected $mId		= 0;
	function __construct($id = false){
		$this->mId	= ($id == false) ? "tab" : $id;
	}
	function setWidth($width){
		$this->mWidth	= $width;
	}
	function setHeight($height){
		$this->mHeight = $height;
	}
	function addTab($titulo, $contenido = ""){
		$xL	= new cLang();
		//$titulo		= $xL->getT($titulo);
		
			if( isset($this->mArrTabs[$titulo]) ){
				$this->mArrTabs[$titulo] .= $contenido;
			} else {
				$this->mArrTabs[$titulo] = $contenido;
			}
	}
	function setContenido($tab, $contenido){
		
	}
	function get(){
		$strLi		= "";
		$strCont	= "";
		$ix			= 0;
		$mid		= $this->mId;
		$xLng		= new cLang();
		foreach ($this->mArrTabs as $clave => $valor){
			//Reemplazar espacios y poner a minusculas
			//$keyTab	= strtolower( str_replace(" ", "_", $clave) );
			$tabtit		= $xLng->getT($clave);
			$keyTab		= $ix;
			$strLi		.= "<li><a href=\"#$mid-$keyTab\">$tabtit</a></li>";
			$strCont	.= "<div id=\"$mid-$keyTab\" >$valor</div>\r\n";
			$ix++;
		}
		$strH	= "<div id=\"$mid\" style='min-height:  " . $this->mHeight . "; min-width:  " . $this->mWidth . ";'><ul>$strLi</ul> $strCont</div><script>setTimeout('go$mid()',1000); function go$mid(){ if(document.getElementById(\"$mid\")){ try{ $(\"#$mid\" ).tabs();} catch(e){}} }</script>";
		return $strH;
	}
	function getIdTab(){
		
	}
}
/**
 * 
 * Clase de Trabajo de Formulario
 * @author Balam Gonzalez Luis Humberto
 * @version 0.9.0
 */
class cHForm {
	protected $mHeadForm		= "";
	protected $mContentForm		= "";
	protected $mContentFoot		= "";
	protected $mAction		= "404.php";
	protected $mName		= "";
	protected $mArrProp		= array();
	protected $mHTML		= "";
	protected $mPathTemp		= "../templates/";
	protected $mEByLine		= 2;
	protected $mJQDate		= array(); //snipt de jquery dates
	protected $mTitle		= "";
	protected $mEnc			= "";
	protected $mToolBar		= "";
	protected $mTools		= array();
	protected $mAvisos		= ""; 
	protected $mJS			= ""; 
	protected $mID			= "";
	protected $mFooterBar	= "";
	protected $mConAcc		= false; 
	private $mArrOpsFrm	= array();

	private $icDic			= null;
	private	$mFieldsetClass	= "fieldform";
	function __construct($name, $action = "", $id = false, $method = "", $class="formoid-default" ){
		$id				= ($id == false) ? "id-$name" : $id;
		$this->mArrProp["method"]	= ($method == "") ? "POST" : $method;
		$this->mArrProp["class"]	= $class;
		$this->mArrProp["id"]		= $id;
		//$this->mArrProp["autocomplete"]		= "off";
		$this->mID					= $id;
		$this->mName				= $name;
		$this->mAction				= $action;
		$this->mEByLine				= 2;
		if(MODO_DEBUG == true){
			$this->OButton("TR.Serialize", "serializeForm('#$id');", "ejecutar");
		}
		$xCat	= new cCatalogoOperacionesDeCaja();
		$this->mArrOpsFrm			= $xCat->getFormatos();
	}
	function setFieldsetClass($class = ""){ $this->mFieldsetClass = $class; }
	function addDataTag($data, $value ){ $this->mArrProp["data-$data"]		= $value; }
	function setElementByLine($NumsElm = 2){
		$this->mEByLine		= $NumsElm;
	}
	function setTitle($title = ""){ $this->mTitle = $title; }
	function setAction($action){ $this->mAction = $action; }
	function addToolbar($element = ""){
		if ($element != ""){ $this->mTools[] 	= "<li>$element</li>";	}
	}
	function addDivSolo($content1, $content2 = "", $tipo1 = "tx34", $tipo2 = "tx14", $props =false){
		$div		= "";
		$props1		= "";
		$props2		= "";
		if(is_array($props)){
			//1= > array ("id" => "" )
			if(isset($props[1])){
				$propsA		= $props[1];
				if(isset($propsA["id"])){
					$props1	.= " id=\"" . $propsA["id"] . "\"";
				}
			}
			if(isset($props[2])){
				$propsA		= $props[2];
				if(isset($propsA["id"])){
					$props2	.= " id=\"" . $propsA["id"] . "\"";
				}
			}			
		}
		if($content2 == ""){
			$div	= "<div class='tx1' $props1>$content1</div>";
		} else {
			$div	= "<div class='tx1'><div class='$tipo1' $props1>$content1</div><div class='$tipo2' $props2>$content2</div></div>";
		}
		$this->addHElem($div);
	}
	function addJsBasico($tipo = iDE_CREDITO, $subtipo = false){
		$xJs		= new jsBasicForm($this->getName(), $tipo);
		if($subtipo == false){
			
		} else {
			$xJs->setTypeCaptacion($subtipo);
		}
		$this->mJS	.= $xJs->get();
	}
	function addCobroBasico($events = ""){
		$xHCob		= new cHCobros();
		$xHCob->setEvents($events);
		$this->addHElem($xHCob->get(false, "", "", false));
	}
	function addPagoBasico($events = ""){
		$xHCob		= new cHCobros();
		$xHCob->setEvents($events);
		$this->addHElem($xHCob->get(TESORERIA_TIPO_EGRESOS, "", "", false));
	}	
	function addHElem($Elements = ""){
		if ( !is_array($Elements) ){
			$colspan			= $this->mEByLine;
			$this->mContentForm	.=  $Elements;
		} else {
			//$this->mContentForm	.=  "<tr>";
			foreach ($Elements as $key => $value ){
				$this->mContentForm	.=  $value;
			}
		}
	}
	/**
	 * Agrega Control textbox a un Form desde una array
	 * @param array $arrM
	 * @example
	 *  Array( Array(
	 *  		"Control.id",
	 *  		"Control.Etiqueta",
	 *  		"Control.Valor",
	 *  		"Control.Tamanio",
	 *  		"Control.Clase-CSS",
	 *  		Array(
	 *  			"Propiedad" => "Valor"
	 *  			)
	 *  	) );"
	 */
	function addHElementsInArray( $arrM ){
		
		if( is_array($arrM)){
			
			$iMark			= 1;
			$items			= count($arrM) -1;
			$mxItem			= 0;
			$aElem			= array();
			
			foreach( $arrM as $clave => $arrMen ){

				if( is_array($arrMen)){
					$id		= isset($arrMen[0]) ? $arrMen[0] : "";
					$id		= isset($arrMen["id"]) ? $arrMen["id"] : $id;
					
					$label	= isset($arrMen[1]) ? $arrMen[1] : "";
					$valor	= isset($arrMen[2]) ? $arrMen[2] : 0;
					$size	= isset($arrMen[3]) ? $arrMen[3] : 10;
					$class	= isset($arrMen[4]) ? $arrMen[4] : "normalfield";
					$aVals	= isset($arrMen[5]) ? $arrMen[5] : false;
					
					$txt	= new cHText($id);
					$txt->init($id, $size, $class);
					$txt->set($id, $valor, $label);
					
					if ( is_array($aVals)){
						foreach( $aVals as $mKey => $mValue ){
							if(strpos($mValue, "=") > 0){
								$DProp		= explode("=", $mValue);
								$txt->setProperty($DProp[0], $DProp[1]);
							} else {
								$txt->setProperty($mKey, $mValue);
							}
							
						}
					}
					$Element		= ($label == "") ? $id : $txt->get() ;
					// si el marcador es igual al numero de elementos
					if($iMark == $this->mEByLine ){
						$aElem[]	= $Element;
						$this->addHElem($aElem);
						$iMark		= 1;
						$aElem		= array();
					} else {
						$aElem[]	= $Element;
						if( $mxItem == $items ){
							if( $iMark <= ($this->mEByLine - 1) ){
								$this->addHElem($aElem);
							}
						}
						 
						$iMark++;
					}

				/*
				 array ( array (
						id,
						label
						value,
						size,
						class,
						options[])
				 */
				}
				$mxItem++;
			}
			$x = fopen("../images/tmp/test.yaml", "w+");
			fwrite($x, Spyc::YAMLDump($arrM) );
			fclose($x);
		}
	}
	function addSubmit($txtGuardar = "", $event = "", $eventclose = ""){
		if($txtGuardar == ""){ $txtGuardar	= $this->lang("aceptar"); }
		$Btn		= new cHButton("id-submit0");
		$Btn2		= new cHButton("id-submit");
		$eventclose	= ($eventclose == "") ? "if(typeof jsEnd == 'undefined'){var xgen=new Gen();xgen.close();}else{jsEnd();}" : $eventclose;
		//$Btn->init($txtGuardar);
		if ( $event == "" ){ $event		=  "$('#" . $this->mID . "').submit()"; /*$this->mName . ".submit()";*/	}
		$this->addToolbar($Btn->getBasic($this->lang("cancelar"), $eventclose, "cancelar", "cmdSalir", false));
		$this->addToolbar($Btn2->getBasic($txtGuardar, $event, "aceptar", "cmdSubmit", false));
	}
	function addGuardar($EventoGuardar = "", $EventoCerrar = ""){
		$this->addSubmit("TR.guardar", $EventoGuardar, $EventoCerrar);
	}	
	function addCerrar( $eventclose = ""){
		$Btn		= new cHButton("id-submit-3");
		$eventclose	= ($eventclose == "") ? "if(typeof jsEnd == 'undefined'){var xgen=new Gen();xgen.close();}else{jsEnd();}" : $eventclose;
		$this->addToolbar($Btn->getBasic($this->lang("cerrar"), $eventclose, "cancelar", "cmdcerrar", false));
	}
	function addRefrescar($Evento = ""){
		$Btn		= new cHButton("id-submit-3");
		$this->addToolbar($Btn->getBasic($this->lang("refrescar"), $Evento, "refrescar", "cmdrefresh", false));
	}
	function setEnc($enc){ $this->mEnc		= $enc;	}
	function getName(){ return $this->mName; }
	function get(){
		$nProps		= "";
		$nJQDates	= "";
		$nTools		= "";
		foreach ($this->mArrProp as $key => $value) {
			$nProps	.= " $key=\"$value\" ";
		}
		foreach ($this->mJQDate as $key => $value){
			$nJQDates	.= "\$( \"#$value\" ).datepicker( { dateFormat: 'yy-mm-dd' } );\r\n";
		}
		if( trim($nJQDates) != ""){
			$nJQDates	= "\$(function() {
				$nJQDates
			});";
		}
		$this->addKey();
		$nTools		= "";
		$limTool	= 6;
		$cnt		= 1;
		$st			= "";
		$js			= "";
		$footerbar	= (trim($this->mFooterBar) == "") ? "" : "<div class='footer-bar notice' id='fb_" . $this->mName . "'>" . $this->mFooterBar . "</div>";
		foreach($this->mTools as $clave => $valor){
			$nTools	.= $valor;
			$cnt++;
		}//style=\"height: auto;\"
		if($this->mConAcc == true){
			$this->mContentForm	= "<div id=\"acc" . $this->mName . "\">" . $this->mContentForm . "</div>";
			$js		= "$(\"#acc"  . $this->mName .  "\").accordion({'header': 'h3','fillSpace': false,	'active': 0});";
		}
		$cid		= ceil($cnt/$limTool);
		$nTools		= ($nTools == "") ? "" : "<nav class='nv' id='menu-nav'><ul class=\"toolnav\" id='ultool'>$nTools</ul></nav>\r\n";
		if($this->mEnc !== ""){
			$nProps		.= " enctype=\"" . $this->mEnc .  "\" ";
		}
		$title	= ($this->mTitle == "") ? "" : "<legend class='title'>&#124;&nbsp;" . $this->mTitle . "&nbsp;&#124;</legend>";
		
		$footer	= (trim($this->mContentFoot) == "") ? "" : "<footer>" . $this->mContentFoot . "</footer>";
		
		$xForm = "<form name=\"" . $this->mName  . "\" action=\"" . $this->mAction . "\" $nProps>
		<header>$nTools</header>
		" . $this->mContentForm . "
		
		" . $this->mHTML . "
		$footer
		</form>
		$footerbar
		" . $this->mJS  . "
		<!-- Inicializa Datos jQuery -->
		<script>$nJQDates $js</script>
		";
		return "<fieldset class='" . $this->mFieldsetClass . "'>$title" .$xForm . "</fieldset>";
	}
	function addHTML($html = ""){
		$this->mHTML	.= $html;
	}
	function addElementByYAML($file){
		$data = Spyc::YAMLLoad( $this->mPathTemp . $file);
		$this->addHElementsInArray($data);
		//var_dump($data);
	}
	function addJQDates($id){ $this->mJQDate[] = $id;	}
	function addPrintRecibo(){
		$xBtn	= new cHButton();
		$this->addToolbar( $xBtn->getBasic("TR.Imprimir Recibo", "if(typeof printrec == 'undefined'){ if(typeof jsImprimirRecibo == 'undefined'){} else { jsImprimirRecibo(); } } else {printrec();}", "imprimir", "id-printrec", false ) );
	}
	function addAvisoRegistroError(){		$xL		= new cLang();		$txt	= $xL->getTrad(MSG_ERROR_SAVE);		$this->addAviso($txt, "idmsg-error", true, "error");	}	
	function addAvisoRegistroOK(){		$xL		= new cLang();		$txt	= $xL->getTrad(MSG_READY_SAVE);		$this->addAviso($txt, "idmsg-ok", true, "success");	}
	function addFootElement($Elements){
		if ( !is_array($Elements) ){
			$colspan			= $this->mEByLine;
			$this->mContentFoot	.=  $Elements;
		} else {
			foreach ($Elements as $key => $value ){
				$this->mContentFoot	.=  $value;
			}
		}		
	}
	function addCreditBasico($credito = false, $persona = false ){
		$this->addPersonaBasico("", false, $persona);
		$xTxt2	= new cHText();
		$this->addHElem( $xTxt2->getDeCredito("", $credito) );
	}
	function addCuentaCaptacionBasico($conpersona = true, $tipo = CAPTACION_TIPO_VISTA, $subtipo = 0){
		if($conpersona == true){ $this->addPersonaBasico("", false, false, "");	}
		
		if($tipo == CAPTACION_TIPO_PLAZO){
			$this->addDataTag("role", "inversion");
		}
		$xTxt2	= new cHText();
		$this->addHElem( $xTxt2->getDeCuentaCaptacion() );
	}
	function addCuentaCaptacionInteres(){ $xTxt2	= new cHText();	$this->addHElem( $xTxt2->getDeCuentaCaptacionInteres("", DEFAULT_CUENTA_CORRIENTE) );	}	
	function addPersonaBasico($id = "", $SinBoton = false, $persona = false, $blurEvents = "", $titulo = ""){
		$xTxt	= new cHText();
		if(setNoMenorQueCero($persona) > DEFAULT_SOCIO){ getPersonaEnSession($persona); }
		$this->addHElem($xTxt->getDeSocio($id, $SinBoton, $persona, $blurEvents, $titulo) );
	}
	function addEmpresaBasico($id = "", $grupo = false){
		$xTxt	= new cHText();
		$this->addHElem($xTxt->getDeGrupo($id, $grupo) );
	}	
	function addGrupoBasico($id = "", $grupo = false){		$xTxt	= new cHText();		$this->addHElem($xTxt->getDeGrupo($id, $grupo) ); 	}
	function addEmpresaComandos($id = ""){
		$this->OButton("TR.Operaciones de Cobro", "var xEmp = new EmpGen();xEmp.getEstadoDeCuenta($id);", $this->icDic->REPORTE);
	}
	function lang($palabra, $palabra2 = ""){
		$xLng		= new cLang();
		return $xLng->getTrad($palabra, $palabra2);
	}
	function l(){ return new cLang(); }
	function addPersonaComandos($clave_de_persona){
		$xBtn	= new cHButton();
		if( getUsuarioActual(SYS_USER_NIVEL) != USUARIO_TIPO_OFICIAL_AML OR OPERACION_LIBERAR_ACCIONES == true OR MODO_DEBUG == true){
			$this->addToolbar( $xBtn->getBasic("TR.Agregar Referencias_Domiciliarias", "var xP= new PersGen();xP.setAgregarVivienda($clave_de_persona)", "vivienda", "id-agregarvivienda", false ) );
			$this->addToolbar( $xBtn->getBasic($xBtn->lang("Agregar")  ." " . ucfirst(PERSONAS_TITULO_PARTES), "var xP= new PersGen();xP.setAgregarRelaciones($clave_de_persona)", "persona", "id-relaciones", false ) );
			$this->addToolbar( $xBtn->getBasic("TR.Agregar Actividad Economica", "var xP= new PersGen();xP.setAgregarActividadE($clave_de_persona)", "empresa", "id-econ", false ) );
			$this->addToolbar( $xBtn->getBasic("TR.Agregar Relacion_Patrimonial", "var xP= new PersGen();xP.setAgregarPatrimonio($clave_de_persona)", "balance", "idrelacionpat", false ) );
		}
		if( getUsuarioActual(SYS_USER_NIVEL) == USUARIO_TIPO_OFICIAL_AML OR MODO_DEBUG == true ){
			$this->addToolbar( $xBtn->getBasic("TR.Reporte de Alertas", "var xML = new AmlGen(); xML.getReporteDeAlertas($clave_de_persona)", "reporte", "idrptalertas", false ) );
			$this->addToolbar( $xBtn->getBasic("TR.Reporte de Perfil transaccional", "var xML = new AmlGen(); xML.getReporteDePerfilTransaccional($clave_de_persona)", "reporte", "idrptperfil", false ) );
			$this->addToolbar( $xBtn->getBasic("TR.Listado de Transacciones", "var xML = new AmlGen(); xML.getReporteDeTransacciones($clave_de_persona)", "riesgo", "idlistatransacciones", false ) );
			$this->OButton("TR.Transacciones Por Nucleo", "var xML = new AmlGen(); xML.getReporteDeTransaccionesPorNucleo($clave_de_persona)", $this->ic()->REGISTROS, "idlistatransaccionesnucleo" );
			
		}
		$this->addToolbar( $xBtn->getBasic("TR.Agregar Documento", "var xP= new PersGen();xP.setAgregarDocumentos($clave_de_persona)", "documento", "id-agregardocto", false ) );
		$this->addToolbar( $xBtn->getBasic("TR.perfil transaccional", "var xP= new PersGen();xP.setAgregarPerfilTransaccional($clave_de_persona)", "perfil", "id-agregarperfil", false ) );
		$this->addToolbar( $xBtn->getBasic("TR.Imprimir Solicitud", "var xP= new PersGen();xP.getImprimirSolicitud($clave_de_persona)", "imprimir", "id-solicitudingreso", false ) );		
		$this->addToolbar( $xBtn->getBasic("TR.Expediente", "var xP= new PersGen();xP.getExpediente($clave_de_persona)", "imprimir", "id-expediente", false ) );
	}
	function addCaptacionComandos($contrato, $urlcontrato = ""){
		$xBtn			= new cHButton();
		
		$xCta			= new cCuentaDeCaptacion($contrato);
		if( $xCta->init() == true){
			$urlcontrato	= $xCta->getURLContrato();
			$urlcontrato	= ($urlcontrato == "") ? "" : "../rpt_formatos/$urlcontrato.php?idcuenta=$contrato";
			$this->addToolbar( $xBtn->getBasic("TR.imprimir contrato", "var xG= new CaptGen();xG.getImprimirContrato('$urlcontrato')", "imprimir", "idcontrato", false ) );
			if($xCta->isTipoVista() == true){
				$this->addToolbar( $xBtn->getBasic("TR.Estado_de_cuenta", "var xG= new CaptGen();xG.getEstadoDeCuentaVista('$contrato')", "imprimir", "idestado", false ) );
				$this->addToolbar( $xBtn->getBasic("TR.Operaciones SDPM", "var xG= new CaptGen();xG.getEstadoDeCuentaSDPM('$contrato')", "imprimir", "idestadosdpm", false ) );
			} else {
				$this->addToolbar( $xBtn->getBasic("TR.Estado_de_cuenta", "var xG= new CaptGen();xG.getEstadoDeCuentaInversion('$contrato')", "imprimir", "idestado", false ) );
			}
			
			$this->addToolbar( $xBtn->getBasic("TR.Actualizar Datos", "var xG= new CaptGen();xG.setActualizarDatos($contrato);", "actualizar", "idactualizar", false ) );
		}
	}
	function addCreditoComandos($clave_de_credito, $estatus = false){
		$xBtn	= new cHButton();
		$this->OButton("TR.Vincular Propietarios", "var xML= new AmlGen();xML.addCuestionario($clave_de_credito)", $this->ic()->PREGUNTAR);
		$this->addToolbar( $xBtn->getBasic("TR.AGREGAR FLUJO DE EFECTIVO", "var xP= new CredGen();xP.getFormaFlujoEfectivo($clave_de_credito)", "balance", "cmd-addflujo", false ) );
		$this->addToolbar( $xBtn->getBasic("TR.VINCULAR AVALES", "var xP= new CredGen();xP.getVincularAvales($clave_de_credito)", "vincular", "vincular-avales" , false) );
		$this->addToolbar( $xBtn->getBasic("TR.AGREGAR AVALES", "var xP= new CredGen();xP.getFormaAvales($clave_de_credito)", "referencias", "add-avales" , false) );
		$this->addToolbar( $xBtn->getBasic("TR.AGREGAR GARANTIAS", "var xP= new CredGen();xP.getFormaGarantias($clave_de_credito)", "bienes", "add-garantias", false ) );
		$this->addToolbar( $xBtn->getBasic("TR.imprimir solicitud", "var xP= new CredGen();xP.getImprimirSolicitud($clave_de_credito)", "reporte", "idimprimirsolicitud", false ) );
		
		$this->OButton("TR.Datos de Transferencia", "var CGen=new CredGen(); CGen.setAgregarBancos($clave_de_credito);", $this->ic()->BANCOS);
		switch($estatus){
			case CREDITO_ESTADO_AUTORIZADO:
				//orden de desembolso
				//$this->addToolbar( $xBtn->getBasic("TR.IMPRIMIR PAGARE", "printpagare()", "dinero", "view-pagare", false )  );
				break;
			case CREDITO_ESTADO_SOLICITADO:
				break;
			default :
				
				break;
		}
		//$this->addToolbar( $xBtn->getBasic($xBtn->lang("Imprimir", "Solicitud"), "var xP= new PersGen();xP.getImprimirSolicitud($clave_de_persona)", "imprimir", "id-solicitudingreso", false ) );		
	}
	
	function addAviso($txt, $id = "", $mostrarTip = false, $class = "notice"){
		$xHO	= new cHObject();
		$txt	= $xHO->Out($txt, OUT_HTML);
		$id		= ($id== "") ? "idmsgs" : $id;
		$xNot	= new cHNotif($class);
		if($mostrarTip == true){
			$txt		= str_replace("\r\n", "", $txt);
			$txt		= str_replace("\r", "", $txt);
			$txt		= str_replace("\n", "", $txt);
			$txt		= str_replace("\t", ":", $txt);
			$xT			= new cTipos();
			//$txt		= $xT->cChar($txt);
			$this->addFootElement("<script>setTimeout(\"jsGoTip()\", 1000); function jsGoTip(){tipSuggest(\"#$id\", \"" . addslashes($txt) . "\");}</script>");
			
		}
		
		$this->addFootElement( $xNot->get($txt, $id));// "<div id='idmsgs' class='alert-box $class'>" . $txt . "</div>");
	}
	function getProcessIDs($arr, $out = false){
		$tt	= "";
		foreach($arr as $v => $vars){
			if($out == OUT_TXT){
				
			} else {
				$tt	.= (trim($vars) == "") ? "" : "var $vars\t=$(\"#$vars\");\r\n";
			}
		}
		return $tt;
	}
	function addObservaciones($id = "idobservaciones"){
		$xTxt		= new cHText();
		$this->addHElem( $xTxt->getDeObservaciones($id, "", "TR.Observaciones"));
	}
	function getAFormsDeTipoPago(){ return  $this->mArrOpsFrm; }
	function OText($id, $valor, $titulo, $add = true, $html = ""){
		$xTxt	= new cHText();
		if($add == true){
			$this->addHElem( $xTxt->getNormal($id, $valor, $titulo, $html) );
		}
		return $xTxt;
	}
	function OMoneda($id, $valor, $titulo, $letras = false, $add = true){
		$xTxt	= new cHText();
		if($add == true){
			$this->addHElem( $xTxt->getDeMoneda($id, $titulo, $valor, $letras) );
		}
		return $xTxt;		
	}
	function OButton($titulo, $event, $icon = "", $id = "", $add = true){
		$xBtn	= new cHButton();
		$id		= ($id == "") ? "id" . rand(0, 100) : $id;
		if($add == true){
			$this->addToolbar( $xBtn->getBasic($titulo, $event, $icon, $id, false ) );
		}
		return $xBtn;
	}
	function OTextArea($id, $valor, $titulo, $add = true){
		$xTxt	= new cHTextArea();
		if($add == true){
			$this->addHElem( $xTxt->get($id, $valor, $titulo) );
		}
		return $xTxt;
	}
	function ODate($id, $valor, $titulo, $add = true){
		$xDate		= new cHDate();
		$xDate->setID($id);
		if($add == true){
			$this->addHElem( $xDate->get($titulo, $valor ));
		}
		return $xDate;		
	}
	function OSelect($id, $valor, $titulo, $options = false, $add = true){
		$xSel		= new cHSelect();
		if(is_array($options) ){
			$xSel->addOptions($options);
		}
		if($add == true){
			$this->addHElem( $xSel->get($id, $titulo, $valor) );
		}
		return $xSel;
	}	
	function OFile($id , $valor = "", $titulo = ""){
		$xFil		= new cHFile();
		$this->addHElem( $xFil->getBasic($id, $valor, $titulo) );
		$this->setEnc("multipart/form-data");
	}
	function OCheck($titulo = "", $id = ""){
		$xChk		= new cHCheckBox();
		$this->addHElem( $xChk->get($titulo, $id) );
	}
	function addSeccion($id, $titulo){$xL 	= new cLang(); $titulo = $xL->getT($titulo); $this->addHElem("<h3><a href=\"#\">$titulo</a></h3><div class='formoid-default'>"); $this->mConAcc	= true;	}
	function endSeccion(){		$this->addHElem("</div>");	}
	function addKey(){
		$xF			= new cFecha();
		$key 		= md5($this->mName . MY_KEY . $xF->getMarca() . rand(0, 9995));
		$_SESSION["frm." . $this->mName]	= $key;
		$this->addFootElement("<input value=\"_" . $this->mName . "\" type=\"hidden\" />");
		return $key;	 
	}
	function OHidden($id, $valor, $titulo = "", $add = true){
		$xTxt	= new cHText();
		if($add == true){
			$this->addFootElement( $xTxt->getHidden($id, "40", $valor) );
		}
		return $xTxt;
	}
	function ic(){ if($this->icDic == null){ $this->icDic = new cFIcons(); }  return $this->icDic; }
	function addLog($msg){
		//if(MODO_DEBUG == true){
			$xFL	= new cFileLog(false, true);
			$xFL->setWrite($msg);
			$xFL->setClose();
			unset($msg);
			$this->addToolbar( $xFL->getLinkDownload("TR.LOG_FILE", ""));
		//}		
	}
	function convert($input, $format = OUT_HTML){	$xObj	= new cHObject();	return $xObj->Out($input, OUT_HTML);	}
	function addFooterBar($html){		$this->mFooterBar	.= $html;	}
}
class cHTextArea {
	protected $mId				= "";
	protected $mName			= "";
	protected $mValue			= "";
	protected $mSize			= "";
	protected $mStr				= "";
	protected $mType			= "text";
	protected $mClass			= "";
	protected $mArrEvents		= array();
	protected $mLbl				= "";
	protected $mArrProp			= array();
	protected $mIncLabel		= false;
	protected $mHTMLExtra		= "";
	protected $mLegend			= "";
	protected $mTitle			= "";
	protected $mArrEventsVals	= array();
	//protected $mDivClass		= "";
	
	protected $mLabelSize		= 0;
	
	function __construct($id = "", $value = "", $label = ""){
		$this->mId				= $id;
		$this->mValue			= $value;
		$this->mLbl				= $label;
		/*$this->mArrProp["type"]	= "text";*/
	}
	function setReDraw($id, $value = "", $label = ""){
		$this->mId				= $id;
		$this->mValue			= $value;
		$this->mLbl				= $label;

	}	
	function addEvent($event, $OnEvent = "onclick", $strParams = ""){
		if ( strpos($event, ")") > 0){
			$this->mArrEvents[$OnEvent]	= "$event";
		} else {
			$this->mArrEvents[$OnEvent]	= "$event($strParams)";
			if ($strParams != ""){
				$this->mArrEventsVals[$OnEvent]	= $strParams;
			}
		}
	}
	
	function addHTMLCode($str){
		$this->mHTMLExtra		.= $str;
	}
	function setIncludeLabel( $inc = true ){
		$this->mIncLabel	= $inc;
	}
	function set($id, $value = "", $label = ""){
		$this->mId				= $id;
		$this->mValue			= $value;
		$this->mLbl				= $label;
	}
	function setSize($size){
		$this->mArrProp["size"]	= $size;
	}
	function setType($type = ""){
		$this->mArrProp["type"]	= $type;
	}
	/**
	 * agrega una propiedad al Input
	 * @param string $property
	 * @param string $value
	 * @example size|maxlength|disabled|class
	 */
	function setProperty($property, $value){
		$this->mArrProp[$property]	= $value;
	}
	function getProperty(){
		
	}
	function setDropProperty($property){
		unset($this->mArrProp[$property]);
	}
	function setLabelSize($tamanno){
		$this->mLabelSize	= $tamanno;
	}
	function get( $id = "", $value = "", $label = "", $forceClearProps = false, $forceValue = true ){
		if($forceClearProps	== true ){	$this->setClearProperties();	}
		$xL						= new cLang();
		$this->mId				= ( $id != "") ? $id : $this->mId;
		$this->mValue			= ( ($value != "") OR ($forceValue == true) ) ? $value : $this->mValue;
		$label					= $xL->getT($label);
		$this->mLbl				= ( $label != "") ? $label : $this->mLbl;
		$this->mTitle			= ( $this->mTitle == "") ? $this->mLbl : "";
				
		$this->mIncLabel		= ( strlen($this->mLbl) > 4 ) ? true : $this->mIncLabel;
		$this->mArrProp["name"]	= $id;
		//if ( !isset($this->mArrProp["name"]) ){		$this->mArrProp["name"] 		= $this->mId;		}
		//if ( !isset($this->mArrProp["title"]) ){		$this->mArrProp["title"] 		= $this->mTitle;		}

		$nEvents							= "";
		$nProps								= "";
		
		$otherStrings 						= $this->mHTMLExtra;
		
			foreach ($this->mArrEvents as $key => $value) {
				//$EvemtEnd	= ( isset($this->mArrEventsVals[$key] ) ) ? $this->mArrEventsVals[$key] : ""
				$nEvents	.= " $key=\"$value$EventEnd\" ";
			}
			foreach ($this->mArrProp as $key => $value) {
				$nProps	.= " $key=\"$value\" ";
			}
		
		$lbl		= ($this->mIncLabel == true) ? "<label for=\"" . $this->mId . "\">" . $this->mLbl . "</label>"  : "";
		
		return "$lbl<textarea  id=\"" . $this->mId . "\"  $nProps $nEvents >" . $this->mValue . "</textarea> $otherStrings";
	}
	function setClearProperties(){
		$this->mArrProp		= array();
	}		
}
class cHCobros {
	private $mNombre	= "";
	private $mID		= "";
	private $mEvents	= "";
	private $mExtra		= "";
	
	function __construct($nombre = "ctipo_pago", $id="idtipo_pago"){
		$this->mNombre	= $nombre;
		$this->mID	= $id;
	}
	function get($UsarEn = false, $TDExtra	= "", $TDInicial = "", $IncluirRFiscal = true ){
		$RFiscal 	= ($IncluirRFiscal == false) ? "" : "<div class='tx4'><label for='id-foliofiscal'>Recibo Fiscal</label><input type='text' name='foliofiscal' id='id-foliofiscal' value='" . DEFAULT_RECIBO_FISCAL . "'  /></div>";
		$hiddens	= ($IncluirRFiscal == false) ? "<input type='hidden' name='foliofiscal' id='id-foliofiscal' value='" . DEFAULT_RECIBO_FISCAL . "'  />" : "";
		$tipo		= ($UsarEn == false) ? TESORERIA_TIPO_INGRESOS : $UsarEn;
		//$txtcheque	= ($tipo == TESORERIA_TIPO_INGRESOS) ? "<input type='hidden' name='cheque' value='' id='id-cheque' >" : "<div class='tx4'><label for='id-foliofiscal'>Numero de Cheque</label><input type='text' name='cheque' value='' id='id-cheque' ></div>";
		$txtcheque		= "<input type='hidden' name='cheque' value='' id='id-cheque' >";
		$xht	= "$TDInicial
			<div class='tx4'>
			<label for='" . $this->mID . "'>Tipo de Pago</label>
			" . $this->getSelectTiposDePago($tipo) . "
			</div>
			$RFiscal $txtcheque
			$TDExtra
			 $hiddens";
		return $xht;
	}
	function setEvents($mixEvents = ""){
		//checar por array onclick=event()
		$this->mEvents		.= $mixEvents;
	}
	function setOptions($opts){
		//checar por array key = value
		$this->mExtra	.= $opts;
	}
	function getSelectTiposDePago($tipo = TESORERIA_TIPO_INGRESOS){
		$opts	= "";
		switch($tipo){
			case TESORERIA_TIPO_INGRESOS:
				$opts	= "
					<option value='efectivo'>EFECTIVO</option>
					<option value='transferencia' selected=\"true\">DEPOSITO/TRANSFERENCIA A BANCOS</option>
					<option value='foraneo'>CHEQUE</option>
					<option value='ninguno'>NINGUNO O AJUSTE</option>
					";
				/*
					<!--
					<option value='cheque.ingreso'>CANJE DE CHEQUE</option>
					<option value='descuento'>CHEQUE DESCONTADO</option>
					<option value='multiple'>PAGO MULTIPLE</option>
					<option value='documento'>CARGO A CUENTA</option>
					-->
				*/
			break;
			case TESORERIA_TIPO_EGRESOS:
				$opts	= "
				<option value='efectivo.egreso' selected=\"true\">PAGO EN EFECTIVO</option>
				<option value='cheque'>PAGO EN CHEQUE</option>
				<option value='transferencia.egreso'>SPEI/TRANFERENCIA/DEPOSITO BANCARIO</option>
				
				";
				//<option value='documento.egreso'>DEPOSITO A CUENTA</option>
			break;
		}
	$ctrl =  "<select name=\"" . $this->mNombre ."\" id=\"" . $this->mID . "\" " . $this->mEvents . ">$opts " . $this->mExtra . "</select>";
	return  $ctrl;
	}
}
class cHFile extends cHInput {
	private $mLIDs			= array();
	
	function getBasic($id, $valor, $titulo = ""){
		$this->setClearProperties();
		$titulo 					= ($titulo == "") ? "TR.Archivo" : $titulo;
		$this->mArrProp["type"]		= "file";
		$this->mArrProp["name"]		= $id; $this->mLIDs[]	= $id;
		$this->mId					= $id;
	
		$this->mLbl					= $titulo;
		//$this->mArrProp["title"]	= $titulo;
		$this->mIncLabel			= ( $titulo != "" ) ? true : false;
		$this->mArrProp["value"]	= $valor;
		$this->mValue				= $valor;
	
		return $this->get($id, $valor, $titulo, "");
	}
}
class cHText extends cHInput {
	private $mLIDs			= array();
	
	function init($name, $size = 40, $class = "normalfield", $valor = false){
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["name"]			= $name;
		$this->mArrProp["size"]			= $size;
		$this->mArrProp["class"]		= $class;
		$this->mIncLabel				= true;
		if($valor !== false ){ $this->mArrProp["value"]	= $valor; }
		//$this->mId					= ( $id != false  ) ? $d : $this->mId;
	}
	function getDeSocio($id = "", $SinBoton = false, $persona = false, $blurEvents = "", $titulo = ""){
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= "idsocio$id";
		$persona						=( setNoMenorQueCero($persona ) < 0 ) ? getPersonaEnSession() : $persona;
		$this->mValue					= $persona;
		$this->mArrProp["maxlength"]	= "18";
		$xLn							= new cLang();
		//$titulo			= $xLn->get("clave de persona");//($titulo == "") ? $xLn->get("clave de persona") : $xLn->getT($titulo);
		$titulo2		= ($titulo == "")  ? $xLn->get("nombre completo") : $xLn->getT($titulo);;
		//$this->addEvent("envsoc()", "onchange");
		$this->addEvent("var xPG = new PersGen();xPG.getNombre(this.value, 'nombresocio$id');$blurEvents", "onblur");
		$this->mIncLabel				= true;
		if($SinBoton == false){
			$xBtn		= new cHImg();
			$this->setClearHTML();
			$this->addHTMLCode($xBtn->get16("common/search.png", " onclick=\"var xPG = new PersGen();xPG.getFormaBusqueda('idsocio$id');\" "));
		}
		$xhNSocio		= new cHInput("nombresocio$id", "", $titulo2);
		$xhNSocio->setIncludeLabel(false);
		$xhNSocio->setProperty("name", "nombresocio$id");
		$xhNSocio->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$xhNSocio->setDivClass("tx34");
		return "<div class='tx1'> ". $this->get("idsocio$id", $persona, $xLn->get("clave de persona")) . $xhNSocio->get("nombresocio$id") . "</div>";		
	}
	function getDeCredito($id="", $credito = false){
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= "idsolicitud";
		$this->mArrProp["maxlength"]	= "18";
		$xLn			= new cLang();
		if($credito == false){
			
		} else {
			$this->mValue				= $credito;
		}
		//$this->addEvent("envsol()", "onchange");
		$this->addEvent("var xc = new CredGen(); xc.getDescripcion(this.value);", "onchange");
		$this->addEvent("var xc = new CredGen(); xc.getDescripcion(this.value);if(typeof jsEvaluarSalida != 'undefined'){jsEvaluarSalida(this);}", "onblur");
		//$this->addEvent("if(typeof jsEvaluarSalida != 'undefined'){jsEvaluarSalida(this);} ", "onblur");
		$this->mIncLabel				= true;
		//$c_gocredit 		= "<img class='buscador' title=\"Buscar un Credito\" src=\"../images/common/search.png\" onclick=\"goCredit_();\"/>";
		$this->addHTMLCode(CTRL_GOCREDIT);
		//$xBtn		= new cHImg();
		//$this->addHTMLCode($xBtn->get16("common/search.png", " onclick=\"var xPG = new PersGen();xPG.getFormaBusqueda('idsolicitud');\" "));
				
		$dSol		= new cHInput("nombresolicitud", "", $xLn->get("Descripcion"));
		$dSol->setIncludeLabel(false);
		$dSol->setProperty("name", "nombresolicitud");
		$dSol->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$dSol->setDivClass("tx34");
		
		return "<div class='tx1' id='divcredito$id'> ". $this->get("idsolicitud", "", $xLn->get("clave de credito")) . $dSol->get("nombresolicitud") . "</div>";
	}
	function getDeGrupo($id="", $grupo = false){
		$id								= ($id == "") ? "idgrupo" : $id;
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= $id;
		//$this->mArrProp["maxlength"]	= "18";
		$xLn			= new cLang();
		if( setNoMenorQueCero($grupo) >0){
			$this->mValue				= $grupo;
		}
		$this->addEvent("var xgg = new GroupGen(); xgg.getDescripcion(this.value);", "onblur");
		//$this->addEvent("envsol()", "onchange");
		//$this->addEvent("if(typeof jsEvaluarSalida != 'undefined'){jsEvaluarSalida(this);} ", "onblur");
		$this->mIncLabel				= true;
		$this->addHTMLCode(CTRL_GOGRUPOS);
		$dSol				= new cHInput("nombregrupo", "", $xLn->getT("TR.Nombre del grupo"));
		$dSol->setIncludeLabel(false);
		$dSol->setProperty("name", "nombregrupo");
		$dSol->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$dSol->setDivClass("tx34");
		//$this->addEvent("var xg = new Gen(); xg.letras({monto: this.value, id: '$id-EnLetras'});", "onblur");
		return "<div class='tx1' id='div$id'> ". $this->get($id, $grupo, $xLn->getT("TR.codigo de grupo")) . $dSol->get("nombregrupo") . "</div>";
	}
		
	function getDeCuentaCaptacion($id = "", $cuenta = false){
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= "idcuenta";
		$this->mArrProp["maxlength"]	= "18";
		$xLn							= new cLang();
		if( setNoMenorQueCero($cuenta) > 0){
			$this->mValue				= $cuenta;
		}
		$this->addEvent("var xCG = new CaptGen();xCG.getDescripcion(this.value, 'nombrecuenta')", "onchange");
		//$this->addEvent("envcta()", "onblur");
		$this->addEvent("var xCG = new CaptGen();xCG.getDescripcion(this.value, 'nombrecuenta'); if(typeof jsEvaluarSalida != 'undefined'){jsEvaluarSalida(this);} ", "onblur");
		$this->mIncLabel				= true;
		$this->addHTMLCode(CTRL_GOCUENTAS);
		$dSol		= new cHInput("nombrecuenta", "", $xLn->get("Descripcion"));
		$dSol->setIncludeLabel(false);
		$dSol->setProperty("name", "nombrecuenta");
		$dSol->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$dSol->setDivClass("tx34");
	
		return "<div class='tx1'> ". $this->get("idcuenta", "", $xLn->get("numero_de_cuenta")) . $dSol->get("nombrecuenta") . "</div>";
	}
	function getDeCuentaCaptacionInteres($id = "", $cuenta = false){
		$id								= ($id == "") ? "idcuentainteres" : $id;
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= $id;
		$this->mArrProp["maxlength"]	= "18";
		$xLn			= new cLang();
		if( setNoMenorQueCero($cuenta) >0){ $this->mValue				= $cuenta; }		
		$this->addEvent("var xgc = new CaptGen(); xgc.getDescripcion(this.value, 'nombrecuentainteres');", "onblur");
		$this->mIncLabel				= true;
		$this->addHTMLCode(CTRL_GOCUENTAS);
		$dSol		= new cHInput("nombrecuentainteres", "", $xLn->getT("TR.Descripcion de la Cuenta de Interes"));
		$dSol->setIncludeLabel(false);
		$dSol->setProperty("name", "nombrecuentainteres");
		$dSol->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$dSol->setDivClass("tx34");
		
		return "<div class='tx1' id='div$id'> ". $this->get($id, $cuenta, $xLn->get("numero_de_cuenta")) . $dSol->get("nombrecuentainteres") . "</div>";
	}	
	/**
	 * Obtiene un cuadro de texto para el manejo de monedas
	 * @param string $id
	 * @param string $Titulo
	 * @param string $value
	 * @param boolean $AgregarEnLetras
	 */
	function getDeMoneda($id = "", $Titulo = "", $value = 0, $AgregarEnLetras = false){
		$this->setClearProperties();
		//$this->setClearEvents();
		$ctrl				= "";
		$id					= ( $id == "" ) ? $this->mId : $id;
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
		$this->mArrProp["value"]		= $value;
		$this->mValue				= $value;
		$this->mArrProp["maxlength"]		= "15";
		$this->mLbl				= $Titulo;
		$this->mId				= $id;
		$this->mIncLabel			= ($Titulo == "" ) ? false : true;
		//agrega un control con Letras
		if ( $AgregarEnLetras == true ){
			$xhN		= new cHInput("$id-iEnLetras", "", "TR.Monto_en_Letras");
			$xhN->setIncludeLabel(false);
			
			$xhN->setProperty("name", "$id-EnLetras");
			$xhN->setProperty("size", "40");
			$xhN->setProperty("disabled", "true");
			$this->addEvent("var xg = new Gen(); xg.letras({monto: this.value, id: '$id-EnLetras'});", "onblur");

			$this->setDivClass("tx14");
			$xhN->setDivClass("tx34");
			$ctrl		= "<div class='tx1'> ". $this->get($id, "", $Titulo) . $xhN->get("$id-EnLetras") . "</div>";
		} else {
			$ctrl		= $this->get(); 
		}
		return $ctrl;		
	}	
	function getPassword($id = "", $titulo = "", $valor = ""){
		$id								= ( $id == "" ) ? $this->mId : $id;
		$this->mArrProp["type"]			= "password";
		/*$this->mArrProp["class"]		= "password";*/
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
		$this->mArrProp["size"]			= "10";
		$this->mValue					= $valor;
		$this->mArrProp["value"]		= $valor;
		$this->mArrProp["maxlength"]	= "20";
		$this->mLbl						= $titulo;
		$this->mId						= $id;
		$this->mIncLabel				= ($titulo == "" ) ? false : true;
		//agrega un control con Letras
		return $this->get($id, $valor, $titulo);		
	}
	function getBasic($id, $size = 40, $class = "normalfield", $valor = "", $titulo = "", $forceClearProps = false){
		if($forceClearProps	== true ){
			$this->setClearProperties();
		}
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["name"]			= $id;
		$this->mId				= $id; $this->mLIDs[]	= $id;
		if($size != false AND $size != ""){ $this->mArrProp["size"] = $size; }
		if($class != ""){ $this->mArrProp["class"] = $class; }
		$this->mValue				= $valor;
		$this->mLbl				= $titulo;
		$this->mArrProp["title"]		= $titulo;
		$this->mIncLabel			= ( $titulo != "" ) ? true : false;
		$this->mArrProp["value"]		= $valor;
		return $this->get($id, $valor, $titulo);
	}
	function getNormal($id, $valor = "", $titulo = "", $html = ""){
		$this->setClearProperties();
		
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
		$this->mId						= $id;
		$this->mLbl						= $titulo;
		$this->mIncLabel				= ( $titulo != "" ) ? true : false;
		$this->mArrProp["value"]		= $valor;
		$this->mValue					= $valor;
		
		return $this->get($id, $valor, $titulo, $titulo, false, $html);
	}
	function getDeObservaciones($id = "idobservaciones", $valor = "", $label = ""){
		$this->setClearProperties();
		$this->setClearEvents();
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
		$this->mId				= $id;
	
		$this->mLbl				= $label;
		$this->mArrProp["title"]		= $label;
		$this->mIncLabel			= ( $label != "" ) ? true : false;
		$this->mArrProp["value"]		= $valor;
		$this->mValue				= $valor;
	
		return $this->get($id, $valor, $label);
	}
	function getHidden($id, $size = 40, $valor = "" , $forceClearProps = false){
		if($forceClearProps	== true ){
			$this->setClearProperties();
		}
		$this->mArrProp["type"]			= "hidden";
		$this->mArrProp["name"]			= $id;
		$this->mId						= $id; $this->mLIDs[]	= $id;
		$this->mArrProp["size"]			= $size;
		unset($this->mArrProp["class"]);
		$this->mLbl						= "";
		$this->mArrProp["title"]		= "";
		$this->mIncLabel				= false;
		//if($valor != "" ){
			$this->mArrProp["value"]	= $valor;
		//}
		return $this->get($id, $valor);
	}
	function getIDs(){ return $this->mLIDs; }
	function getNumero($id, $valor, $titulo = ""){
		$this->setClearProperties();
	
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
		$this->mId						= $id;
	
		$this->mLbl						= $titulo;
		$this->mArrProp["title"]		= $titulo;
		$this->mIncLabel				= ( $titulo != "" ) ? true : false;
		$this->mArrProp["value"]		= $valor;
		$this->mValue					= $valor;
	
		return $this->get($id, $valor, $titulo, $titulo);
	}
	function getEmail($id, $valor = "", $titulo = ""){
		$this->setClearProperties();
		$titulo 						= ($titulo == "") ? "TR.correo_electronico" : $titulo;	
		$this->mArrProp["type"]			= "email";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
		$this->mId						= $id;
	
		$this->mLbl						= $titulo;
		$this->mArrProp["title"]		= $titulo;
		$this->mIncLabel				= ( $titulo != "" ) ? true : false;
		$this->mArrProp["value"]		= $valor;
		$this->mValue					= $valor;
	
		return $this->get($id, $valor, $titulo, $titulo);
	}	
	function getDeCodigoPostal($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		$titulo 			= "TR.Codigo_postal";
		$id					= ( $id == "" ) ? "idcodigopostal" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg$id = new DomGen(); xg$id.getColoniasXCP(this);", "onblur");
		if(PERSONAS_VIVIENDA_MANUAL == true){
			$snipt				= "<div id='div$id' class='tx34'><label for='dl$id'>" . $this->lang("Colonia") . "</label>
									<input type='text' id='idnombrecolonia' name='idnombrecolonia' list='dl$id' onblur='var xg$id = new DomGen(); xg$id.setColoniasXCP(this);'>
									<datalist id='dl$id'><option /></datalist>
									</div>";
		} else {
			$snipt				= "<div id='div$id' class='tx34'><label for='dl$id'>" . $this->lang("Colonia") . "</label><select id='dl$id' onblur='var xg$id = new DomGen(); xg$id.setColoniasXCP(this);' ><option /></select></div>";
		}
		$this->setDivClass("tx14");
		$ctrl				= "<div class='tx1'> ". $this->get($id, $valor, $titulo ) . $snipt . "</div><input type='hidden' id='idcp_$id' /><div id='' class='tx1'></div>";
		
		return $ctrl;
	}
	function getDeActividadEconomica($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		$titulo 						= "TR.Clave de Actividad";
		$id								= ( $id == "" ) ? "idactividadeconomica" : $id;
		$this->mLIDs[]					= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg$id = new PersAEGen(); xg$id.getListaDeActividades(this, event);", "onkeyup");
		$this->addEvent("var xg$id = new PersAEGen(); xg$id.setActividadPorCodigo(this);", "onblur");
		
		$xhN		= new cHInput("iddescripcion$id", "", "TR.Descripcion");
		$xhN->setIncludeLabel(false);
		$xhN->setProperty("name", "iddescripcion$id");
		$xhN->setProperty("disabled", "true");
		
		$this->setDivClass("tx14");
		$xhN->setDivClass("tx34");
		
		
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		$this->setProperty("autocomplete", "off");
		
		$ctrl		= "<div class='tx1'> ". $this->get($id, $valor, $titulo ) . $xhN->get("iddescripcion$id") . "</div>";
		
		return $ctrl;
	}
		
	function getDeNombreDeMunicipio($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		$titulo 			= "TR.Municipio";
		$id					= ( $id == "" ) ? "idnombremunicipio" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id; 
		$this->addEvent("var xg = new DomGen(); xg.getMunicipioNombreXA(this, event);", "onkeyup");
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		$this->setProperty("autocomplete", "off");
		$ctrl		= $this->get($id, $valor, $titulo );
		return $ctrl;
	}
	
	function getDeNombreDeColonia($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		//$this->setClearEvents();
		$titulo 			= "TR.Colonia";
		$id					= ( $id == "" ) ? "idnombrecolonia" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg = new DomGen(); xg.getColoniasNombreXA(this, event);", "onkeyup");
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		$ctrl		= $this->get($id, $valor, $titulo );
		return $ctrl;
	}
	function getDeNombreDeLocalidad($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		//$this->setClearEvents();
		$titulo 			= "TR.Localidad";
		$id					= ( $id == "" ) ? "idnombrelocalidad" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg = new DomGen(); xg.getLocalidadNombreXA(this, event);", "onkeyup");
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		$ctrl		= $this->get($id, $valor, $titulo );
		return $ctrl;
	}
	function getDeCuentaContable($id = "", $valor = "", $addNombre = true){
		$this->setClearProperties();
		$titulo 			= "TR.Cuenta";
		$id					= ( $id == "" ) ? "idcuentacontable" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg = new ContGen(); xg.getCuentasPorCodigo(this, event);", "onkeyup");
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		if($addNombre == true){
			$this->addEvent("var xg = new ContGen(); xg.getNombreDeCuenta({cuenta: this.value, control: 'nombre_$id'});", "onblur");
			$xhN		= new cHInput("nombre_$id", "", "TR.Nombre de la Cuenta");
			$xhN->setIncludeLabel(false);
			$xhN->setProperty("name", "nombre_$id");
			$xhN->setProperty("disabled", "true");
			$this->setDivClass("tx14");
			$xhN->setDivClass("tx34");
			$ctrl		= "<div class='tx1'> ". $this->get($id, $valor, $titulo ) . $xhN->get("nombre_$id") . "</div>";			
		} else {
			$ctrl		= $this->get($id, $valor, $titulo );
		}
		return $ctrl;
	}
	function getDeNombreDePersona($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		//$this->setClearEvents();
		$titulo 			= ($titulo == "") ? "TR.Persona" : $titulo;
		$id					= ( $id == "" ) ? "idpersona" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg = new PersGen(); xg.buscar(this, event);", "onkeyup");
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		$ctrl		= $this->get($id, $valor, $titulo );
		return $ctrl;
	}
	function getDeValoresPorTabla($id = "", $valor = "", $titulo = "", $idtabla = ""){
	
		$this->setClearProperties();
		//$this->setClearEvents();
		$titulo 			= ($titulo == "") ? "TR.Campo" : $titulo;
		$id					= ( $id == "" ) ? "idcampo" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$this->setProperty("list", "dl$id");
		$this->addEvent("var xg = new Gen(); xg.getTValores(this, event, '$idtabla');", "onkeyup");
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$ctrl		= $this->get($id, $valor, $titulo );
		return $ctrl;
	}	
}
class cHImg {
	private $mIcon		= "icon.png";
	function __construct($icon = ""){ $this->mIcon = $icon; }
	function get16($icon = "", $snipt = ""){
		$icon		= ($icon == "") ? $this->mIcon : $icon;
		$icon		= (strpos($icon, "png") === false) ? "$icon.png" : $icon;
		return "<img src=\"../images/$icon\" $snipt class=\"x16\"/>";
	}
	function get24($icon = "", $snipt = ""){
		$icon		= ($icon == "") ? $this->mIcon : $icon;
		$icon		= (strpos($icon, "png") === false) ? "$icon.png" : $icon;
		return "<img src=\"../images/$icon\" $snipt class=\"x24\"/>";
	}	
}
class cHButton extends cHInput{
	private $mIcons	= array("editar" => "fa-edit",
						"dinero" => "fa-money",
						"persona" => "fa-user",
						"referencias" => "fa-group",
						"bienes" => "fa-car",
						"agregar" => "fa-plus",
						"cancelar" => "fa-times-circle",
						"calendario" => "fa-calendar",
						"lista" => "fa-book",
						"imprimir" => "fa-print",
						"guardar" => "fa-save",
							"mas-dinero" => "cash_stack_add.png",
							"csv" => "csv.png",
						"eliminar" => "fa-eraser",
							"tasa" => "percent.png",
						"bancos" => "fa-file-text-o",
							"actualizar" => "sign-up.png",
						"ver" => "fa-eye",
						"salir" => "fa-power-off",
						"ejecutar" => "fa-cog",
						"balance" => "fa-university",
						"documento" => "fa-folder-o",
						"reporte" => "fa-book",
							"trabajo" => "current-work.png",
						"colaborar" => "fa-circle-o-notch",
							"comunicar" => "communication.png",
							"especial" => "limited-edition.png",
						"statistics" => "fa-line-chart",
							"checar" => "check.png",
							"fecha" => "date.png",
						"aceptar" => "fa-check-square-o",
						"panel" => "fa-wrench",
						"grafico" => "fa-pie-chart",
						"refrescar" => "fa-refresh",
							"poliza" => "bank-check-icon.png",
							"bien" => "check.png",
							"mal" =>"error-icon.png",
						"alerta" => "fa-exclamation-circle",
						"aviso" => "fa-exclamation-circle",
						"quitar" => "fa-minus-circle",
						"empresa" => "fa-building",
						"vivienda" => "fa-home",
						"tarea" => "fa-tasks",
						"inicio" => "fa-home",
						"baja" => "fa-minus-circle",
						"info" => "fa-info-circle",
						"minus" => "fa-minus-circle",
						"siguiente" => "fa-arrow-circle-right",
						"anterior" => "fa-arrow-circle-left",
						"atras" => "fa-arrow-circle-left",
							"verde" => "seguimiento/green_dot.png",
							"amarillo" => "seguimiento/yellow_dot.png",
					"finalizar" => "fa-check-square", 
					"bloquear" => "fa-lock",
					"cerrar" => "fa-times",
					"perfil" => "fa-history",
			"doctos" => "fa-bookmark-o",
			"documentos" => "fa-bookmark-o",
			"riesgo" =>"fa-bullseye",
			"importar" => "fa-exchange",
			"deposito" => "fa-cloud-download",
			"moneda" => "fa-dollar",
			"codigo" => "fa-code",
			"permisos" => "fa-cube",
			"garantia" => "fa-car",
			"contabilidad" => "fa-building-o",
			"exportar" => "fa-exchange",
			"imagen" => "fa-file-image-o",
			"documento" => "fa-file-pdf-o",
			"desconocido" => "fa-exclamation",
			"buscar" => "fa-search",
			"cargar" => "fa-arrow-circle-down",
			"carcular" => "fa-calculator",
			"saldo" => "fa-credit-card",
			"caja" => "fa-inbox",
			"mail" => "fa-at",
			"salud" => "fa-user-md",
			"warning" => "fa-exclamation-circle",
			"error" => "fa-times-circle",
			"sucess" => "fa-check-circle",
			"notice" => "fa-info-circle",
			"registros" => "fa-database",
			"descargar" => "fa-download",
			"preguntar" => "fa-comments-o"
			);
	function init($label = ""){
		$this->mArrProp["type"]		= "button";
		$this->mArrProp["class"]	= "button";
		$this->mArrProp["value"]	= $label;
	}
	function getImprimirRecibo($id = "id-cmdImprimirRecibo", $value = ""){
		$this->mArrProp["type"]		= "button";
		$this->mArrProp["class"]	= "button";
		$value						= ($value == "") ? $this->lang("imprimir", "recibo") : $value;
		$this->mHTMLExtra		= "";
		$this->addHTMLCode("<img src=\"../images/print.png\" />");
		$this->set($id, $value);
		$this->addEvent("jsImprimirRecibo()", "onclick");
		return $this->get();
	}
	function getEjecutar($accion = "", $id = "", $value = "", $toolbar = false){
		$id					= ($id == "") ? "idCmdExec" : $id;
		$this->mArrProp["type"]		= "button";
		if($toolbar == false){
			$this->mArrProp["class"]	= "button";
		} else {
			unset($this->mArrProp["class"]);
		}
		$value						= ($value == "") ? $this->lang("Ejecutar") : $value;
		$this->mHTMLExtra			= "";
		$this->setIcon("ejecutar");
		$this->set($id, $value);
		$this->addEvent($accion, "onclick");
		return $this->get();
	}
	function getContableImprimir(){
		
	}
	function getRegresar($url = "", $toolbar = false){
		$this->mArrProp["type"]		= "button";
		if($toolbar == false){
			$this->mArrProp["class"]	= "button";
		} else {
			unset($this->mArrProp["class"]);
		}
		$v				= $this->lang("Regresar");
		$this->mArrProp["value"]	= $v;
		$id				= $this->mId;
		$this->mHTMLExtra		= "";
		$url				= ($url == "") ? "javascript:history.back()" : "var xG=new Gen(); xG.close({url:'$url'});";
		$this->setIcon("anterior");
		$this->set($id, $v);
		$this->addEvent($url, "onclick");
		return $this->get();		
	}
	function getSalir($url = "../index.php", $toolbar = false){
		$url	= ($url == "") ? "" : $url;
		$this->mArrProp["type"]		= "button";
		if($toolbar == false){
			$this->mArrProp["class"]= "button";
		} else {
			unset($this->mArrProp["class"]);
		}
		$v				= $this->lang("salir");
		$this->mArrProp["value"]	= $v;
		$id				= $this->mId;
		$this->setIcon("salir");
			
		$this->set($id, $v);
		$this->addEvent("var xG=new Gen(); xG.close({url:'$url'})", "onclick");
		return $this->get();			
	}
	function getIrAlInicio($toolbar = false){
		$this->mArrProp["type"]		= "button";
		if($toolbar == false){
			$this->mArrProp["class"]	= "button";
		} else {
			unset($this->mArrProp["class"]);
		}
		$this->setIcon("inicio");
		$v	= $this->lang("ir al inicio");
		$this->mArrProp["value"]	= $v;
		$this->set("id-inicio-salir", $v);
		$this->addEvent("var xG=new Gen(); xG.close({url:'../index.php'});", "onclick");
		return $this->get("id-inicio-salir", $v);			
	}
	function getBasic($label, $actionClick = "", $icono = "", $id = "cmd", $no_toolbar = true, $isTag = false){
		$xL			= new cLang();
		$label		= $xL->getT($label);
		$sniptHTML	= "";
		if($isTag == true){
			$sniptHTML	= "<span>$label</span>";
			$label		= "";
			$this->setIcon($icono, "");
		} else {
			$this->setIcon($icono);
		}
		
		$this->mArrProp["type"]		= "button";
		if($no_toolbar == true){ $this->mArrProp["class"]	= "button"; } else { unset($this->mArrProp["class"]); }
		$this->mArrProp["value"]	= $label;
		$id				= ($this->mId == "") ? $id : $this->mId;
		$this->set($id, $label);
		$this->addEvent($actionClick, "onclick");
		return $this->get("", false, "", "", false, $sniptHTML);				
	}
	function setIcon($icono, $class="fa-2x"){
		$src		= "";
		if( isset( $this->mIcons[$icono] ) ){
			$this->mHTMLExtra	= "";
			$micon				= $this->mIcons[$icono];
			if(strpos($micon, "fa-") !== false){
				$src	= "<i class=\"fa " . $micon . " $class\"></i>";
			} else {
				$src	= "<img src=\"../images/" . $micon . "\"/>";
			}
			$this->addHTMLCode($src);
		}
		return $src;
	}


}
class cHSelect {
	protected $mEspOptions		= array();
	protected $mArrEvents		= array();
	protected $mArrEventsVals	= array();
	protected $mArrProp		= array();
	protected $mSql			= "";
	protected $mLabelSize		= 0;
	protected $mId			= "";
	protected $mDefault		= "";
	protected $mTags		= true;
	protected $mDivClass	= "element-select";
	private $mLIDs			= array();
	function __construct($id = "", $options = array() ){
		$this->mEspOptions	= $options;
		$this->mId		= $id;
		$this->mLIDs[]	= $id;
	}
	function init(){}
	function setTags($tags = false){ $this->mTags	= $tags; }
	function setEnclose($tags = false){ $this->mTags	= $tags; }
	function setSQL($sql){$this->mSql = $sql; }
	function setClearOptions(){
		$this->mEspOptions 	= false;
		$this->mEspOptions	= array();
	}
	function addOptions( $aOptions ){
		if ( is_array($aOptions) ){
			foreach ($aOptions as $valor => $label){
				$valor	= (trim($valor) == "") ? $label : $valor;
				$label	= (trim($label) == "") ? $valor : $label;
				$this->mEspOptions[$valor] = $label;
				//setLog("AGREGANDO $label $valor");
			}	
		} else {
			$this->mEspOptions[$aOptions] = ucfirst($aOptions);
		}
	}
	function setDefault($value){ $this->mDefault	= $value; }
	function setDivClass($class = ""){ $this->mDivClass = $class; }
	function get($id = "", $label = "", $DefaultValue = false, $tabla = ""){
		$xL				= new cLang();
		$label			= $xL->getT($label);
		$this->mLIDs[]	= $id;
		$select			= "";
		$maxSize		= ( $this->mLabelSize == 0) ? HP_LABEL_SIZE : $this->mLabelSize; //$this->mMaxLineSize - $this->mLineSize;
		$rll			= ($maxSize - strlen( html_entity_decode($label) ) );
		$id				= ($id == "") ? $this->mId : $id;
		$this->mDefault	= ($DefaultValue !== false) ? $DefaultValue : $this->mDefault;
		$DefaultValue	= $this->mDefault;
		
		$nEvents		= "";
		$nProps			= "";		
		$label			= $label;//($label == "") ? "" : $label .  str_repeat("&nbsp;", $rll );
		//elimina el Name y le asigna el ID como name
		//unset($this->mArrProp["name"]);
		$this->mArrProp["name"] 			= $id;
		
		foreach ($this->mArrEvents as $key => $value) {
			$nEvents	.= " $key=\"$value\" ";
		}
		foreach ($this->mArrProp as $key => $value) {
			$nProps	.= " $key=\"$value\" ";
		}
		if($this->mSql != ""){
			$tabla	= $this->mSql;
		}
		if( $tabla == ""){
			$txt		= "";
			$isDef		= "";
			
			foreach( $this->mEspOptions as $valor => $tit ){
				
				$isDef	= ( $valor == $DefaultValue ) ? "selected" : "";
				$txt	.= "<option $isDef value='$valor'>$tit</option>";
			}
			$label		= ($label == "" ) ? "" : "<label for='$id'>$label</label>" ;
			$select		= ($this->mTags == true) ? "<div class='" . $this->mDivClass . "'>$label<select id='$id' $nProps $nEvents >$txt</select></div> " : "$label<select id='$id' $nProps $nEvents >$txt</select>";
		} else {
			$xSel	= new cSelect($id, $id, $tabla);
			if($this->mSql != ""){ $xSel->setEsSql(); }
			foreach ($this->mArrEvents as $key => $value) {
				$xSel->addEvent($key, $value);
			}			
			if( $DefaultValue != false ){
				$xSel->setOptionSelect($DefaultValue);
			}
			$xSel->setLabelSize($this->mLabelSize);
			$select	= $xSel->get($label, $this->mTags);
		}
		return $select;
	}
	function addEvent($event, $OnEvent = "onclick", $strParams = ""){
		if ( strpos($event, ")") > 0){
			$this->mArrEvents[$OnEvent]	= "$event";
		} else {
			$this->mArrEvents[$OnEvent]	= "$event($strParams)";
			if ($strParams != ""){
				$this->mArrEventsVals[$OnEvent]	= $strParams;
			}
		}
	}
	function setClearEvents(){
		$this->mArrEvents	= array();
	}
	function setClearProperties(){
		$this->mArrProp		= array();
	}	
	function setLabelSize($tamanno){
		$this->mLabelSize	= $tamanno;
	}
	function getDiasInversion($id = "iddias", $name = "dias"){
		$this->mArrProp["id"]		= $id; $this->mLIDs[]	= $id;
		$this->mArrProp["name"]		= $name;
		$control	= "";
		if ( CAPTACION_INVERSIONES_POR_DIA == true ){
			$xTxt		= new cHText($id);
			echo "<input name='dias' type='text' value='7' onchange=\"envtasa(); getReporto()\"
				onfocus=\"getReporto()\"
				onblur=\"setFechaInv(); jsaGetTasa()\" id=\"iddias\"  class=\"mny\" size=\"4\" /> ";
		} else {
			echo "<select name='dias' id=\"iddias\" onchange=\"jsaGetTasa(); getReporto(); setFechaInv();\"
							onfocus=\"getReporto()\"
							onblur=\"setFechaInv(); jsaGetTasa()\">
							<option value='30'>30</option>
								<option value='60'>60</option>
								<option value='90'>90</option>
								<option value='180'>180</option>
								<option value='360'>360</option>
							</select>";
		}
		return $control;
	}

	function getUsuarios($id= "idusuarios"){
		$this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idusuarios, nombrecompleto FROM usuarios";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		return $xS;
	}
	function getTiposDeDoctosPersonales($id = "", $tipo = ""){
		$id			= ($id == "") ? "idtipodedocto" : $id; $this->mLIDs[]	= $id;
		$sqlSc		=  "SELECT `clave_de_control`, `nombre_del_documento` FROM personas_documentacion_tipos";
		
		if($tipo == BASE_DOCTOS_PERSONAS_FISICAS){
			$sqlSc	.= " WHERE (`personas_documentacion_tipos`.`clasificacion` ='IP') OR (`personas_documentacion_tipos`.`clasificacion` ='DG') ";
		}
		if($tipo == BASE_DOCTOS_PERSONAS_MORALES){
			$sqlSc	.= " WHERE (`personas_documentacion_tipos`.`clasificacion` ='IPM') OR (`personas_documentacion_tipos`.`clasificacion` ='DG') ";
		}		
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.Tipo de Documento");
		return $xS;
	}
	/**
	 * @deprecated @since 2015.01.00
	 * */
	function getCatalogoDeRiesgos($id = "", $tipo = "", $selected = false){
		$id			= ($id == "") ? "idtipoderiesgo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= ( setNoMenorQueCero($tipo) > 0) ? "SELECT * FROM aml_risk_catalog WHERE tipo_de_riesgo=$tipo ORDER BY descripcion" : "SELECT * FROM aml_risk_catalog ORDER BY descripcion ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		if(setNoMenorQueCero($selected) > 0){
			$xS->setOptionSelect($selected);
		}
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		return $xS;
	}
	function getListadoDeBancos($id = "", $selected = false){
		$id			= ($id == "") ? "idcodigodebanco" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `bancos_entidades`.`idbancos_entidades`, `bancos_entidades`.`nombre_de_la_entidad` FROM `bancos_entidades` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.Banco");
		//$xS->addEspOption(SYS_TODAS);
		if($selected !== false){
			$xS->setOptionSelect($selected);
		}
		return $xS;
	}	
	function getListaDeMonedas($id = ""){
		$id			= ($id == "") ? "idcodigodemoneda" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `tesoreria_monedas`.`clave_de_moneda`, `tesoreria_monedas`.`nombre_de_la_moneda` FROM	`tesoreria_monedas` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		//$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(AML_CLAVE_MONEDA_LOCAL);
		return $xS;
	}
	function getListaDeCuentasBancarias($id = "", $omitirDefault = false){
		$id			= ($id == "") ? "idcodigodecuenta" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `bancos_cuentas`.`idbancos_cuentas`, `bancos_cuentas`.`descripcion_cuenta` FROM `bancos_cuentas` ";
		$sqlSc		.= ($omitirDefault == false) ? "" : " WHERE	(`bancos_cuentas`.`idbancos_cuentas` !=" . FALLBACK_CUENTA_BANCARIA . ") ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		return $xS;
	}
	function getListaDeProductosDeCredito($id = "", $selected = false){
		$id		= ($id == "") ? "idproducto" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc		= "SELECT `idcreditos_tipoconvenio`, `descripcion_tipoconvenio` FROM `creditos_tipoconvenio` WHERE	(`idcreditos_tipoconvenio` !=99)";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Producto de Credito");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeEstadosDeCredito($id = "", $selected = false){
		$id		= ($id == "") ? "idestado" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc		= "SELECT idcreditos_estatus, descripcion_estatus FROM creditos_estatus";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Estado de Credito");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeEmpresas($id = "", $omitirDefault = false){
		$id			= ($id == "") ? "idcodigodeempresas" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `idsocios_aeconomica_dependencias`, `descripcion_dependencia`  FROM `socios_aeconomica_dependencias` ";
		$sqlSc		.= ($omitirDefault == false) ? "" : " WHERE (`idsocios_aeconomica_dependencias` !=" . FALLBACK_CLAVE_EMPRESA . ") ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.Empresa");
		//$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(DEFAULT_EMPRESA);
		return $xS;
	}
	function getListaDeCajasLocales($id = "", $soloSucursal = false){
		$id		= ($id == "") ? "idcajalocal" : $id; $this->mLIDs[]	= $id;
		$soloSucursal	= ($soloSucursal == false) ? "" : " AND sucursal = '" . getSucursal() . "' ";
		$sqlSc		= "SELECT * FROM socios_cajalocal WHERE idsocios_cajalocal !=99 $soloSucursal ORDER BY descripcion_cajalocal";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		//$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(getCajaLocal());
		return $xS;
	}
	function getListaDeTiposDeIngresoDePersonas($id = "", $tipo = SYS_TODAS, $defaultOpt = DEFAULT_TIPO_INGRESO){
		$id		= ($id == "") ? "idtipodeingreso" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_tipoingreso WHERE estado=1 ";
		$sqlSc		.= ($tipo == SYS_TODAS) ? "" : " AND (tipo_de_persona=0 OR tipo_de_persona=$tipo) ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setOptionSelect( $defaultOpt );
		return $xS;
	}
	function getListaDeFigurasJuridicas($id = "", $tipo = SYS_TODAS){
		$id		= ($id == "") ? "idfigurajuridica" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_figura_juridica ";
		$sqlSc		.= ($tipo == SYS_TODAS) ? "" : " WHERE	`tipo_de_integracion` = $tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeRegimenesFiscales($id = "", $tipo = SYS_TODAS){
		$id		= ($id == "") ? "idregimenfiscal" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM personas_regimen_fiscal ";
		$sqlSc		.= ($tipo == SYS_TODAS) ? "" : " WHERE	`tipo_de_persona` = $tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setOptionSelect(PERSONAS_FISCAL_NINGUNO);
		return $xS;
	}	
	function getListaDeTipoDeIdentificacion($id = "", $tipo = PERSONAS_ES_FISICA){
		$id		= ($id == "") ? "idtipoidentificacion" : $id; $this->mLIDs[]	= $id;
		$tipo	= ($tipo == PERSONAS_ES_FISICA) ? BASE_DOCTOS_PERSONAS_FISICAS : BASE_DOCTOS_PERSONAS_MORALES;

		$sqlSc		=  "SELECT `clave_de_control`, `nombre_del_documento` FROM personas_documentacion_tipos";
		
		if($tipo == BASE_DOCTOS_PERSONAS_FISICAS){
			$sqlSc	.= " WHERE (`personas_documentacion_tipos`.`clasificacion` ='IP') OR (`personas_documentacion_tipos`.`clasificacion` ='DG') ";
		}
		if($tipo == BASE_DOCTOS_PERSONAS_MORALES){
			$sqlSc	.= " WHERE (`personas_documentacion_tipos`.`clasificacion` ='IPM') OR (`personas_documentacion_tipos`.`clasificacion` ='DG') ";
		}		
		
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		$xS->setLabel("TR.Tipo de Identificacion");
		$xS->setOptionSelect(2201);
				
		return $xS;
	}
	function getListaDeGeneros($id = ""){
		$id		= ($id == "") ? "idgenero" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_genero";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeEntidadesFed($id = "", $numerico = false, $estado = false){
		$xLoc		= new cLocal();
		$id			= ($id == "") ? "identidadfederativa" : $id; $this->mLIDs[]	= $id;
		$mid		= ($numerico == false) ? " `general_estados`.`clave_alfanumerica` " : " `general_estados`.`clave_numerica` ";
		$estado		= (setNoMenorQueCero($estado) <= 0) ? $xLoc->DomicilioEstadoClaveNum() : $estado;
		$sel		= ($numerico == false) ? strtoupper($xLoc->DomicilioEstadoClaveABC()) : $estado; 
		$sqlSc		= "SELECT $mid, `general_estados`.`nombre` FROM	`general_estados` `general_estados`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setOptionSelect($sel);
		$xS->addEvent("onblur", "var xDG= new DomGen();xDG.setAccionPorEstado(this);");
		$xS->setLabel("TR.entidad_federativa");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDePaises($id = "", $pais = EACP_CLAVE_DE_PAIS){
		$id		= ($id == "") ? "idpais" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT clave_de_control, nombre_oficial FROM personas_domicilios_paises ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Pais");
		$xS->addEvent("onblur", "var xDG= new DomGen();xDG.setAccionPorPais(this);");
		$xS->setOptionSelect($pais);
		$xS->setEsSql();
		return $xS;
	}
	
	function getListaDeEstadoCivil($id = ""){
		$id		= ($id == "") ? "idestadocivil" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_estadocivil";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeRegimenMatrimonio($id = ""){
		$id		= ($id == "") ? "idregimenmatrimonial" : $id; $this->mLIDs[]	= $id;
		$arr	= array("SOCIEDAD_CONYUGAL"  => "SOCIEDAD CONYUGAL", "BIENES_SEPARADOS" => "BIENES SEPARADOS");
		//
		/*$sqlSc		= "SELECT * FROM socios_estadocivil";*/
		$xS 		= new cSelect($id, $id, "");
		$xS->addEspOption("NINGUNO" , "NINGUNO");
		$xS->addEspOption("SOCIEDAD_CONYUGAL"  , "SOCIEDAD CONYUGAL");
		$xS->addEspOption("BIENES_SEPARADOS" , "BIENES SEPARADOS");
		$xS->setOptionSelect(strtoupper( SYS_NINGUNO) );
		$xS->setEsSql();
		$xS->setLabel("TR.REGIMEN_MATRIMONIAL");
		return $xS;
	}
	function getListaDePerfilTransaccional($id = ""){
		$id		= ($id == "") ? "idtipotransaccion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM `personas_perfil_transaccional_tipos` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		return $xS;
	}
	function getIDs(){ return $this->mLIDs; }
	function getListaDePeriocidadDePago($id = "", $selected = false){
		$id		= ($id == "") ? "idperiocidad" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc	= "SELECT `idcreditos_periocidadpagos`, `descripcion_periocidadpagos` FROM `creditos_periocidadpagos` WHERE (`idcreditos_periocidadpagos` !=99) ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Frecuencia de pagos");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDePago($id = ""){
		$id		= ($id == "") ? "idtipodepago" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT * FROM `creditos_tipo_de_pago` WHERE (`creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` !=99) ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Pago");
		$xS->setOptionSelect( CREDITO_TIPO_PAGO_PERIODICO );
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeRecibos($id = ""){
		$id		= ($id == "") ? "idtipoderecibo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT	`operaciones_recibostipo`.`idoperaciones_recibostipo`,	`operaciones_recibostipo`.`descripcion_recibostipo` FROM `operaciones_recibostipo` `operaciones_recibostipo` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Recibo");
		//$xS->setOptionSelect( CREDITO_TIPO_PAGO_PERIODICO );
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeTipoDeAutorizacion($id = ""){
		$id		= ($id == "") ? "idtipodeautorizacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT * FROM `creditos_tipo_de_autorizacion` WHERE (`idcreditos_tipo_de_autorizacion` !=99) ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setOptionSelect(SYS_UNO);
		$xS->setLabel("TR.Tipo de Autorizacion");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDeRiesgoEnCreds($id = ""){
		$id		= ($id == "") ? "idnivelderiesgo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT * FROM creditos_nivelesriesgo";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Nivel de Riesgo");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDeRiesgoEnAML($id = "", $selected = ""){
		$id		= ($id == "") ? "idtipoderiesgoaml" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT * FROM `aml_risk_types` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Riesgo");
		if($selected != ""){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}		
	function getListaDeOperacionesPorBase($base, $id = "", $base2 = false){
		$id		= ($id == "") ? "idtipodepago" : $id; $this->mLIDs[]	= $id;
		$ByBase		= " WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$base) ";
		$ByBase		.= (setNoMenorQueCero($base2) > 0) ? " OR (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$base2)" : "";
		$sqlSc		= "SELECT	`miembro`, `descripcion_operacion`
					FROM `operaciones_tipos` INNER JOIN `eacp_config_bases_de_integracion_miembros` `eacp_config_bases_de_integracion_miembros` 	ON `operaciones_tipos`.`idoperaciones_tipos` = 	`eacp_config_bases_de_integracion_miembros`.`miembro` 
					$ByBase ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeNivelDeRiesgo($id = "", $selected = false){
		$id			= ($id == "") ? "idnivelderiesgo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM `entidad_niveles_de_riesgo`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected != false){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;		
	}
	function getListaDeDestinosDeCredito($id = ""){
		$id		= ($id == "") ? "iddestinodecredito" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `creditos_destinos`.`idcreditos_destinos`, CONCAT(`creditos_destinos`.`descripcion_destinos`,'-',(`creditos_destinos`.`tasa_de_iva`*100), '%') AS 'destino'
					FROM `creditos_destinos` `creditos_destinos`  WHERE (`creditos_destinos`.`idcreditos_destinos` !=99) ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Destino de los_recursos");
		$xS->setEsSql();
		return $xS;		
	}
	function getListaDeTiempo($id = ""){
		$id		= ($id == "") ? "idtiempo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_tiempo, descripcion_tiempo FROM socios_tiempo ORDER BY idsocios_tiempo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tiempo");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeRegimenDeVivienda($id = ""){
		$id		= ($id == "") ? "idregimendevivienda" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_regimenvivienda, descipcion_regimenvivienda FROM socios_regimenvivienda ORDER BY descipcion_regimenvivienda ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Regimen de Vivienda");
		$xS->setOptionSelect(1);
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeRazonesDeBaja($id = ""){
		$id		= ($id == "") ? "idrazondebaja" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM `socios_baja_razones`  ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Razon de Baja");
		//$xS->setOptionSelect(1);
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeTiposDeVivienda($id = ""){
		$id		= ($id == "") ? "idtipodevivienda" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_viviendatipo, descripcion_viviendatipo FROM socios_viviendatipo ORDER BY descripcion_viviendatipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Vivienda");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeRelaciones($id = "", $tipo = ""){
		$id		= ($id == "") ? "idtipoderelacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_relacionestipos, descripcion_relacionestipos FROM socios_relacionestipos ";
		$sqlSc		.= ($tipo == "") ? "" : " WHERE subclasificacion=$tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de relacion");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeParentesco($id = "", $tipo = ""){
		$id		= ($id == "") ? "idtipodeparentesco" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_consanguinidad, descripcion_consanguinidad FROM socios_consanguinidad ";
		$sqlSc		.= ($tipo == "") ? "" : " WHERE subclasificacion=$tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Parentesco");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeOficiales($id = "", $tipo = "", $selected = false){
		$id		= ($id == "") ? "idoficial" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT id, nombre_completo FROM oficiales";
		//$sqlSc		.= ($tipo == "") ? "" : " WHERE subclasificacion=$tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Oficial");
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeCajasAbiertas($id = "", $tipo = ""){
		$id		= ($id == "") ? "idcaja" : $id; $this->mLIDs[]	= $id;
		$xli		= new cSQLListas();
		$sqlSc		= $xli->getListadoDeCajasConUsuario(TESORERIA_CAJA_ABIERTA);
		//TODO : Probar con cSQListas
		//$sqlSc		.= ($tipo == "") ? "" : " WHERE subclasificacion=$tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Caja");
		$xS->setNoMayus();
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeCaptacionProductos($id = "", $tipo = ""){
		$id		= ($id == "") ? "idproductocaptacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `idcaptacion_subproductos`, `descripcion_subproductos` FROM `captacion_subproductos` ";
		//$sqlSc		.= ($tipo == "") ? "" : " WHERE subclasificacion=$tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Caja");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDePerfilTransaccional($id = "", $tipo = SYS_TODAS){
		$id			= ($id == "") ? "idtipodeperfil" : $id; $this->mLIDs[]	= $id;
		$ByTipo		= ($tipo == SYS_TODAS) ? "" : "WHERE (`personas_perfil_transaccional_tipos`.`afectacion` = $tipo)";
		$sqlSc		= "SELECT `idpersonas_perfil_transaccional_tipos`, `nombre_del_perfil`	FROM  `personas_perfil_transaccional_tipos` $ByTipo";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de transaccion");
		$xS->setEsSql();
		return $xS;		
	}

	function getListaDeTiposDeMemoPersonas($id = ""){
		$id			= ($id == "") ? "idtipodememo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT * FROM socios_memotipos WHERE tipo_memo!=99 ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Memo");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDePolizas($id = "", $omitirDefault = true){
		$id			= ($id == "") ? "idtipodepoliza" : $id; $this->mLIDs[]	= $id;
		$omitirDefault	= ($omitirDefault == true ) ?  " WHERE `idcontable_polizadiarios` != 999 " : "";
		$sqlSc		= " SELECT * FROM `contable_polizasdiarios` $omitirDefault ORDER BY `idcontable_polizadiarios` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Poliza_Contable");
		$xS->setEsSql();
		return $xS;
	}		
	function getListaDeTiposDeCentrosDeCosto($id = "", $omitirDefault = false){
		$id			= ($id == "") ? "idcentrodecosto" : $id; $this->mLIDs[]	= $id;
		$omitirDefault	= ($omitirDefault == true ) ?  " WHERE `idcontable_centrodecostos` != 999 " : "";
		$sqlSc		= " SELECT * FROM `contable_centrodecostos` $omitirDefault ORDER BY `idcontable_centrodecostos` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setOptionSelect(999);
		$xS->setLabel("TR.Tipo de centro_de_costo");
		$xS->setEsSql();
		return $xS;
	}	
	function getListadoGenerico($tabla , $id = ""){
		$id			= ($id == "") ? "id$tabla" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM $tabla LIMIT 0,100";
		if(strpos($tabla, "FROM") > 0){
			$sqlSc	= $tabla;
		}
		$xS 		= new cSelect($id, $id, $sqlSc);
		//$xS->setLabel("TR.Tipo de transaccion");
		$xS->setEsSql();
		return $xS;		
	}

	function getListaDeTipoDeCaptacion($id = ""){
		$sqlSc		= "SELECT * FROM `captacion_cuentastipos` ";
		$id			= ($id == "") ? "idtipodecuenta" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Cuenta");
		//$xS->setOptionSelect($default);
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeProductosDeCaptacion($id = ""){
		$id		= ($id == "") ? "idproductocaptacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idcaptacion_subproductos, descripcion_subproductos FROM captacion_subproductos WHERE idcaptacion_subproductos != 99";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Producto de Captacion");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeOrigenDeCaptacion($id = ""){
		$id		= ($id == "") ? "idorigencaptacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM captacion_cuentasorigen";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Origen de cuenta");
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeTituloDeCaptacion($id = ""){
		$id		= ($id == "") ? "idtitulocaptacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM captacion_tipotitulo";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Origen de la cuenta");
		$xS->setEsSql();
		return $xS;
	}	

	function getListaDeSucursales($id = "", $sucursal = false){
		$sqlSc		= "SELECT codigo_sucursal, nombre_sucursal FROM general_sucursales";
		$id			= ($id == "") ? "idsucursal" : $id; $this->mLIDs[]	= $id;
		$sucursal	= ($sucursal == false) ? getSucursal() : $sucursal;
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Sucursal");
		$xS->setNoMayus();
		$xS->setOptionSelect($sucursal);
		$xS->setEsSql();
		return $xS;	
	}
	function getListaDeMunicipios($id = "", $estado = SYS_TODAS, $selected = false){
		$xLoc	= new cLocal();
		$id		= ($id == "") ? "idmunicipio" : $id; $this->mLIDs[]	= $id;
		$selected = (setNoMenorQueCero($selected) <= 0) ? $xLoc->DomicilioMunicipioClave() : $selected;
		$ByEst	= setNoMenorQueCero($estado) <= 0 ? "" : " AND (`general_estados`.`clave_numerica` ='$estado') ";
		$sql	= "SELECT `general_municipios`.`clave_de_municipio`, `general_municipios`.`nombre_del_municipio`
		FROM  `general_estados` `general_estados`
		INNER JOIN `general_municipios` `general_municipios`
		ON `general_estados`.`clave_numerica` = `general_municipios`.
		`clave_de_entidad`  WHERE `clave_de_municipio`  $ByEst ";
		$xS 		= new cSelect($id, $id, $sql);
		$xS->setLabel("TR.municipio");
		$xS->setOptionSelect( $selected );
		$xS->setEsSql();
		return $xS;		
	}
	function getListaDeLocalidades($id = "", $estado = SYS_TODAS, $pais = EACP_CLAVE_DE_PAIS, $selected = false){
		$xLoc	= new cLocal();
		$id		= ($id == "") ? "idlocalidad" : $id; $this->mLIDs[]	= $id;
		$selected = (setNoMenorQueCero($selected) <= 0) ? $xLoc->DomicilioLocalidadClave() : $selected;
		$ByEst	= setNoMenorQueCero($estado) <= 0 ? "" : " AND (`catalogos_localidades`.`clave_de_estado` ='$estado') ";
		if($pais != EACP_CLAVE_DE_PAIS){
			$ByEst		= " AND	(`catalogos_localidades`.`clave_de_pais` ='$pais') ";
		}
		$sql = "
		SELECT
			`catalogos_localidades`.`clave_unica`,	`catalogos_localidades`.`nombre_de_la_localidad`
		FROM
			`catalogos_localidades`
		WHERE `catalogos_localidades`.`clave_unica` > 0 $ByEst";
		
		$xS 		= new cSelect($id, $id, $sql);
		$xS->setLabel("TR.Localidad");
		$xS->setOptionSelect( $selected );
		$xS->setEsSql();
		return $xS;
	}

	function getListaDeFormaReportaRiesgo($id =""){
		$id			= ($id == "") ? "idformadereportar" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xS->addEspOption("C" , "Calificado");
		$xS->addEspOption("I" , "Inmediato");
		$xS->addEspOption("D" , "Diario");
		$xS->setEsSql(); //C Calificado I Inmediato D Diario
		$xS->setLabel("TR.Forma de Reportar");
		return $xS;
	}
	function getListaDeUnidadMedidaAML($id = ""){
		$id			= ($id == "") ? "idunidadmedida" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xS->addEspOption(AML_CLAVE_MONEDA_LOCAL , "Moneda Local");
		$xS->addEspOption("USD" , "Dolares");
		$xS->addEspOption("EVENTO" , "Evento");
		$xS->setEsSql();
		$xS->setLabel("TR.Forma de Reportar");
		return $xS;
	}	
	function getListaDeFrecuenciaChequeoRiesgo($id = ""){
		$id			= ($id == "") ? "idfrecuenciadechequeo" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xS->addEspOption("I" , "Inmediato");
		$xS->addEspOption("D" , "Diario");
		$xS->setEsSql(); //C Calificado I Inmediato D Diario
		$xS->setLabel("TR.Frecuencia de Chequeo");
		return $xS;	
	}
	function getListaDeObjetosEnSistema($id = "", $selected = null){
		$id			= ($id == "") ? "idobjetodesistema" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		//$xS->addEspOption(iDE_SOCIO , "Persona");
		$xS->addEspOption(iDE_CREDITO , "Credito");
		$xS->addEspOption(iDE_CAPTACION , "Captacion");
		$xS->addEspOption(iDE_RECIBO , "Recibos");
		if($selected != null){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		$xS->setLabel("TR.Tipo");
		return $xS;		
	}
	function getListaDeMeses($id = ""){
		$id			= ($id == "") ? "idnumerodemes" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xF 		= new cFecha (0);
		foreach ( $xF->getMesesInArray () as $key => $value ) {
			$xS->addEspOption($key , "$key - $value");
		}
		$xS->setOptionSelect(date("m"));
		$xS->setEsSql();
		$xS->setLabel("TR.Mes");
		return $xS;		
	}
	function getListaDeAnnos($id = ""){
		$id			= ($id == "") ? "idnumerodeanno" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xF 		= new cFecha (0);
		foreach ( $xF->getAnnosInArray() as $key => $value ) {
			$xS->addEspOption($key , "$value");
		}
		$xS->setOptionSelect(date("Y"));
		$xS->setEsSql();
		$xS->setLabel("TR.Ejercicio");
		return $xS;
	}	
	function getListaDeTiposDeCuentasContables($id = "", $agregar_especiales = false, $select = false){
		$id		= ($id == "") ? "idtipodecuentacontable" : $id; $this->mLIDs[]	= $id;
		$xL		= new cLang();
		$cCta 	= new cSelect($id, $id, "contable_catalogotipos");
		if($agregar_especiales == true){
			$cCta->addEspOption("algunas", $xL->getT("TR.Algunas"));
			$cCta->addEspOption(SYS_TODAS, $xL->getT("TR.Todas"));
			$cCta->addEspOption("cuadre", $xL->getT("TR.Cuadre"));
		}
		if($select !== false){
			$cCta->setOptionSelect($select);
		}
		$cCta->setLabel("TR.Tipo de cuenta");

		return $cCta;
	}
	function getListaDeNivelesDeCuentasContables($id = "", $agregar_especiales = false, $select = false){
		$id		= ($id == "") ? "idniveldecuenta" : $id; $this->mLIDs[]	= $id;
		$xL		= new cLang();
		$cCta 	= new cSelect($id, $id, "");
		
		
		$cCta->addEspOption(1, $xL->getT("TR.Titulo"));
		$cCta->addEspOption(2, $xL->getT("TR.Subtitulo"));
		$cCta->addEspOption(3, $xL->getT("TR.Mayor"));
		$cCta->addEspOption(4, $xL->getT("TR.Subcuenta"));
		
		if($agregar_especiales == true){
			$cCta->addEspOption(SYS_TODAS, $xL->getT("TR.Todas"));
		}
		if($select !== false){
			$cCta->setOptionSelect($select);
		}
		$cCta->setEsSql();
		$cCta->setLabel("TR.Nivel de Cuenta");
		
		return $cCta;
		

	}
	function getListaDeEstadoMvtosDeCuentasContables($id = "", $agregar_especiales = false, $select = false){
		$id		= ($id == "") ? "idestadomvto" : $id; $this->mLIDs[]	= $id;
		$xL		= new cLang();
		$select	= ($select == false) ? SYS_TODAS : $select;
		$cCta 	= new cSelect($id, $id, "");
		$cCta->setNoMayus();
		$cCta->addEspOption(SYS_TODAS, $xL->getT("TR.Todas"));
		$cCta->addEspOption("saldo_no_cero", $xL->getT("TR.Saldos Diferentes a Cero"));
		$cCta->addEspOption("saldo_no_cero_con_mvtos", $xL->getT("TR.Movimientos y Saldo Diferentes a Cero"));
		$cCta->addEspOption("saldo_no_cero_o_mvtos", $xL->getT("TR.Movimientos o Saldo Diferentes a Cero"));
		
		$cCta->setOptionSelect($select);
		
		$cCta->setEsSql();
		$cCta->setLabel("TR.Estado por Movimiento");
		
		return $cCta;
	}
	function getListaDeDiarioDeMvtosContables($id = "", $default = DEFAULT_CONTABLE_DIARIO_MVTOS){
		$sqlSc		= "SELECT * FROM `contable_movimientosdiarios` ";
		$id			= ($id == "") ? "iddiariomvtos" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Diario de Movimientos");
		$xS->setOptionSelect($default);
		$xS->setEsSql();
		return $xS;		
	}
	function getListaDeCentroDeCostoCont($id = "", $default = DEFAULT_CENTRO_DE_COSTO){
		$sqlSc		= "SELECT * FROM `contable_centrodecostos` ";
		$id			= ($id == "") ? "idcentrodecosto" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Centro de Costo");
		$xS->setOptionSelect($default);
		$xS->setEsSql();
		return $xS;
	}	
	
	function getListaDeObjetosOrigenRiesgo($id = "", $selected = false){
		$id			= ($id == "") ? "idobjetosderiesgo" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		
		$xS->addEspOption(TCATALOGOS_RELACIONES , "Partes Relacionadas");
		$xS->addEspOption(TCATALOGOS_LOCALIDADES , "Localidades");
		$xS->addEspOption(TCATALOGOS_ACTIVIDADES_ECONOMICAS , "Actividades Economicas");
		$xS->addEspOption(TCATALOGOS_PAISES , "Paises");
		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Origen de Riesgo");
		return $xS;		
	}
	function getListaDeCamposPorTabla($id = "", $valor = "", $titulo = "", $idorigen = ""){

	 $this->setClearProperties();
		//$this->setClearEvents();
		$titulo 			= ($titulo == "") ? "TR.Campo" : $titulo;
		$id					= ( $id == "" ) ? "idcampo" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$idorigen			= ($idorigen == "") ? "this" : "'$idorigen'";
		$this->addEvent("var xg = new Gen(); xg.getTCampos(this, $idorigen);", "onfocus");
		$ctrl		= $this->get($id, $valor, $titulo );
		return $ctrl;
	}
	function getListaDeRiesgosAML($id = "", $tipo = "", $selected = false){
		$id			= ($id == "") ? "idtipoderiesgo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= ( setNoMenorQueCero($tipo) > 0) ? "SELECT * FROM aml_risk_catalog WHERE tipo_de_riesgo=$tipo ORDER BY descripcion" : "SELECT * FROM aml_risk_catalog ORDER BY descripcion ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		if(setNoMenorQueCero($selected) > 0){
			$xS->setOptionSelect($selected);
		}
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		return $xS;
	}
} //END


class cHTabla {
	protected $mRows		= array();
	protected $mStyles		= array();
	protected $mTitles		= array();
	protected $mHtml		= "";
	protected $mId			= "";
	public	$TFOOT			= "tfoot";
	public	$THEAD			= "thead";
	public	$TBODY			= "tbody";
	
	private $mBody			= "";
	private $mFoot			= "";
	private $mHead			= "";
	private $mOut			= SYS_DEFAULT;
	function __construct($id = ""){
		$this->mId			= $id;
	}
	function addRow($arrDatos, $tag = "td", $props = ""){
		
		if(is_array($arrDatos)){
			$txt		= "";
			foreach ($arrDatos as $key => $vals){ $txt		.= "<$tag>$vals</$tag>"; }
			if($tag == "th"){
				$this->mTitles[]	= "<tr$props>" . $txt . "</tr>";
			} else {
				$this->mRows[]		= "<tr$props>" . $txt . "</tr>";
			}
		} else {
			if($tag == "th"){
				$this->mTitles[]	= $arrDatos;
			} else {
				$this->mRows[]		= $arrDatos;
			}
		}
		
	}
	function addTitles($arrTitles){ $this->addRow($arrTitles, "th"); }
	function addStyles($arrStyles){ }
	function get(){
		foreach ($this->mTitles as $idx => $cnt){
			$this->mHtml	.= "<tr>" . $cnt . "</tr>";
		}		
		foreach ($this->mRows as $idx => $cnt){
			$this->mHtml	.= $cnt;
		}
		$id			= ($this->mId == "") ? "" : " id=\"" . $this->mId . "\"";
		$inittags	= "<table$id>";
		$endtags	= "</table>";
		if($this->mOut == OUT_EXCEL){
			$inittags	= "<tbody>";
			$endtags	= "</tbody>";
		}
		return "<table$id>" . "<thead> ". $this->mHead . "</thead><tbody id=\"tb-" . $this->mId . "\">". $this->mHtml . "</tbody></table>";
	}
	function initRow(){ $this->mHtml .= "<tr>"; 	}
	function addTD($html){ 
		$this->mHtml .= "<td>$html</td>";
	}
	function addTH($text){ $xL	= new cLang(); $text	= $xL->getT($text); $this->mHead .= "<th>$text</th>"; 	}
	function endRow(){ $this->mHtml .= "</tr>\r\n"; 	}
	function addRaw($html){ $this->mHtml .= $html; }
	function setOut($out){ $this->mOut = $out; }
}

class cHDate{
	protected $mFecha		= "";
	protected $mIndex		= 0;
	protected $mTipoFecha	= false;
	protected $mLabelSize	= 0;
	protected $mSelects		= false;
	protected $mId			= "";
	protected $mClassDiv	= "tx4";
	protected $mEvents		= "";
	function __construct($Index = 0, $Fecha = false, $TipoDeFecha = false){
		$this->mFecha 		= ($Fecha == false) ? fechasys() : $Fecha;
		$this->mIndex		= $Index;
		$this->mTipoFecha	= ($TipoDeFecha == false) ? FECHA_TIPO_OPERATIVA : $TipoDeFecha;
	}
	function setIsSelect($select = true){	$this->mSelects		= $select;	}
	function setID($id){ $this->mId	= $id; }
	function addEvents($events){ $this->mEvents .= $events; }
	function setDivClass($class){ $this->mClassDiv = $class; }
	function get($label = "", $Fecha = false, $indice	= false){
		$xTr			= new cLang();
		$label			= $xTr->getT($label);
		$maxSize		= ( $this->mLabelSize == 0) ? HP_LABEL_SIZE : $this->mLabelSize;
		if($indice !== false){ $this->mIndex	= $indice; }
		if( strlen($label) < $maxSize ){
			$rll		= ($maxSize - strlen( html_entity_decode($label) ) );
		}
		$extraDate		= "";
		$label			= ($label == "") ? "" : "<label for='idfecha-" . $this->mIndex . "'>$label</label>" ;
		$this->set($Fecha);
		$xF				= new cFecha($this->mIndex, $this->mFecha);
		$events			= $this->mEvents;
		$xF->init(FECHA_FORMATO_MX);
		$id			= ($this->mId == "") ? "idfecha-" . $this->mIndex . "" : $this->mId;
		if( $this->mTipoFecha == FECHA_TIPO_NACIMIENTO ){
			$anno	= $xF->anno() - 18;
			$xF->set("$anno-" . $xF->mes() . "-" . $xF->dia() );
			$this->set($xF->get());
			$extraDate	= ",selectYears: true ";
		}
		$txt			= "<input type=\"text\" id=\"$id\" value=\"" . $xF->get() . "\" name=\"$id\" $events><script>$(\"#$id\").pickadate({format: 'dd-mm-yyyy',formatSubmit:'yyyy-mm-dd', editable : true $extraDate});</script> ";
		$initDiv		= ($this->mClassDiv == "") ? "" : "<div class=\"" . $this->mClassDiv . "\">";
		$endDiv			= ($this->mClassDiv == "") ? "" : "</div>";
		return ($this->mSelects == false) ? $initDiv . $label . $txt . $endDiv : $initDiv . $label . $xF->show(true, $this->mTipoFecha) . $endDiv;
	}
	function set($Fecha = false){
		if( $Fecha != false ){	$this->mFecha	= $Fecha;	}
	}
	function setLabelSize($tamanno){	$this->mLabelSize	= $tamanno;	}	
}

class cHInput {
	protected $mId				= "";
	protected $mName			= "";
	protected $mValue			= "";
	protected $mSize			= "";
	protected $mStr				= "";
	protected $mType			= "text";
	protected $mClass			= "";
	protected $mArrEvents		= array();
	protected $mArrEventsVals	= array();
	protected $mLbl				= "";
	protected $mArrProp			= array();
	protected $mIncLabel		= false;
	protected $mHTMLExtra		= "";
	protected $mLegend			= "";
	protected $mLineSize		= 0;	//Tamaño total de la linea de registro
	protected $mMaxLineSize		= 110;	//Tamaño maximo de la linea de registro que es usada como Label
	protected $mTitle			= "";
	protected $mLabelSize		= 0;
	protected $mDivClass		= "tx4";
	
	function __construct($id = "", $value = "", $label = ""){
		$this->mId				= $id;
		$this->mValue			= $value;
		$this->mLbl				= $label;
		/*$this->mArrProp["type"]	= "text";*/
	}
	function setReDraw($id, $value = "", $label = ""){
		$this->mId				= $id;
		$this->mValue			= $value;
		$this->mLbl				= $label;

	}	
	function addEvent($event, $OnEvent = "onclick", $strParams = ""){
		if ( strpos($event, ")") > 0){
			$this->mArrEvents[$OnEvent]	= "$event";
		} else {
			$this->mArrEvents[$OnEvent]	= "$event($strParams)";
			if ($strParams != ""){
				$this->mArrEventsVals[$OnEvent]	= $strParams;
			}
		}
	}
	
	function addHTMLCode($str){
		$this->mHTMLExtra		.= $str;
	}
	function setIncludeLabel( $inc = true ){
		$this->mIncLabel	= $inc;
	}
	function set($id, $value = "", $label = ""){
		$this->mId	= $id;
		$this->mValue	= $value;
		$this->mLbl	= $label;
	}
	function setSize($size){
		$this->mArrProp["size"]	= $size;
	}
	function setType($type = ""){
		$this->mArrProp["type"]	= $type;
	}
	/**
	 * agrega una propiedad al Input
	 * @param string $property
	 * @param string $value
	 * @example size|maxlength|disabled|class
	 */
	function setProperty($property, $value){
		$this->mArrProp[$property]	= $value;
	}
	function getProperty(){
		
	}
	function setDropProperty($property){
		unset($this->mArrProp[$property]);
	}
	function get( $id = "", $value = false, $label = "", $titulo = "", $arrEvents = false, $snipHTML = "" ){
		$this->mId			= ( $id != "") ? $id : $this->mId;
		$this->mValue		= ( $value !== false) ? $value : $this->mValue;
		$this->mLbl			= ( $label == "") ? $this->mLbl : $label;
		$this->mTitle		= ( $titulo == "") ? $this->mLbl : $titulo;
		$this->mIncLabel	= ( strlen($this->mLbl) > 4 ) ? true : $this->mIncLabel;
		if($this->mLbl != ""){
			$xL			= new cLang();
			$this->mLbl		= $xL->getT($this->mLbl);
		}
		if( is_array($arrEvents)){ array_merge($this->mArrEvents, $arrEvents);  }
		if( !isset($this->mArrProp["maxlength"]) AND isset($this->mArrProp["size"]) ){
			$this->mArrProp["maxlength"]	= $this->mArrProp["size"];
		}
		
		$this->mArrProp["name"] 		= $this->mId;
		//$this->mArrProp["title"] 		= $this->mTitle;
		
		if ( trim($this->mValue) != "" ){ $this->mArrProp["value"]	= $this->mValue; }
		$this->mType					= (!isset($this->mArrProp["type"])) ? $this->mType : $this->mArrProp["type"];
		$this->mArrProp["type"]			= $this->mType;
		
		$nEvents				= "";
		$nProps					= "";
		
		$otherStrings 						= $this->mHTMLExtra . $snipHTML;
		
		foreach ($this->mArrEvents as $key => $value) {
			$nEvents	.= " $key=\"$value\" ";
		}
		if($this->mType	== "button"){// OR $this->mType	== "submit"){
			unset($this->mArrProp["type"]);
			unset($this->mArrProp["title"]);
			unset($this->mArrProp["value"]);
			unset($this->mArrProp["name"]);
		}
		foreach( $this->mArrProp as $key => $value ){ $nProps	.= " $key=\"$value\" "; }
		//si existe la propiedad size tomarla para sumar, si no poner 50
		$minSize	= HP_FORM_MIN_SIZE;
		if ( $this->mLineSize  == 0 ){
			//50 + 75 = 125
			$this->mLineSize	= ( isset($this->mArrProp["size"]) ) ? $this->mArrProp["size"] : $minSize; 
		}
		//si la linea es menor a 75, poner a 75
		if ( $this->mLineSize < $minSize ){ $this->mLineSize	= $minSize; }			
		$maxSize	= ( $this->mLabelSize == 0) ? HP_LABEL_SIZE : $this->mLabelSize; //$this->mMaxLineSize - $this->mLineSize;

		if( strlen($this->mLbl) < $maxSize ){			
			$rll		= ($maxSize - strlen( html_entity_decode($this->mLbl) ) );
		}		
		$lbl		= ($this->mIncLabel == true) ? "<label for=\"" . $this->mId . "\">" . $this->mLbl . "</label>"  : "";
		$ctrl		= ($this->mDivClass == "") ? "$lbl<input  id=\"" . $this->mId . "\"  $nProps $nEvents > $otherStrings" : "<div class=\"" . $this->mDivClass .   "\">$lbl<input  id=\"" . $this->mId . "\"  $nProps $nEvents > $otherStrings</div>";
		if($this->mType == "button"){
			$ctrl	= "$lbl<a id=\"" . $this->mId . "\" $nProps $nEvents>$otherStrings " . $this->mValue . "</a>";
		}
		return $ctrl;
	}
	function getLineSize (){
		return $this->mLineSize;
	}
	function setLineSize($size){
		$this->mLineSize			= $size;
	}
	function setClearEvents(){
		$this->mArrEvents	= array();
	}
	function setClearProperties(){
		$this->mArrProp			= array();
		$this->mHTMLExtra		= "";
	}
	function setClearHTML(){ $this->mHTMLExtra		= ""; } 
	function setLabelSize($tamanno){}

	function lang($palabra, $palabra2 = ""){
		$xLng		= new cLang();
		return $xLng->getTrad($palabra, $palabra2);
	}
	function setDivClass($class){ $this->mDivClass = $class; }
	function ic(){ $ic = new cFIcons(); return $ic;	}
	
}
class cHFieldset {
	protected $mContentField		= "";
	protected $mArrProp				= array();
	protected $mId					= "";
	protected $mLegend				= "";

	function __construct($legend	=  "", $id =""){
		$this->mId		= $id;
		$xLs			= new cLang();
		$this->mLegend	= $xLs->getT($legend);
	}
	function addHElem($strElement = "<br />"){
		$this->mContentField	.= $strElement;
	}
	function get(){
		$nProps		= "";
		foreach ($this->mArrProp as $key => $value) {
			$nProps	.= " $key=\"$value\" ";
		}			
		$xForm = "<fieldset id=\"" . $this->mLegend . "\" $nProps>
		<legend>" . $this->mLegend . "</legend>
		" . $this->mContentField . "
		
		</fieldset>";
		return $xForm;
	}	
}

class cHNotif {
	private $mTipo	= "";
	
	public $NOTICE	= "notice";
	public $WARNING	= "warning";
	public $ERROR	= "error";
	public $SUCCESS	= "success";
	private $OIC	= null;
	function __construct($tipo = "notice"){
		$this->mTipo	= $tipo;
	}
	function get($txt, $id = "", $tipo = false){
		$this->mTipo	= ($tipo == false) ? $this->mTipo : $tipo;
		$xDiv	= new cHDiv("alert-box " . $this->mTipo , $id);
		$txt	= "<span class='close-tip' onclick='var xG = new Gen(); xG.closeTip(this)'>x</span>" . $txt;
		$xDiv->addHElem($txt);
		return $xDiv->get();
	}
	function getNoticon($idcounter = "idc", $icon = "aviso"){
		$xBtn	= new cHButton();
		$ic		= $xBtn->setIcon($icon);
		
		return "<div class='noticon'>$ic <span id='$idcounter' class='noticount'></span></div>";
	}
	function ic(){
		if($this->OIC == null){ $this->OIC = new cFIcons();}
		return $this->OIC;
	}
}

class cHGrid {
	private $mCampos	= array();
	private $mActions	= array();
	private $mTitle		= "";
	private $mId		= "";
	function __construct($id, $title = ""){ $xlng	= new cLang(); $this->mId	= $id; $this->mTitle	= $xlng->getT($title); }
	//listAction: '../svc/referencias.svc.php?out=jtable&persona=' + idxpersona + "&documento=" + idxcredito
	function setListAction($url){ $this->mActions["listAction"] = $url; }
	function addElement($nombre, $titulo, $tamannio){
		/*tipo_de_relacion:{ title: 'Relacion', width: '20%'}*/
		$xlng	= new cLang();
		$titulo	= $xlng->getT($titulo);
		$this->mCampos[$nombre] = array ("title" => $titulo, "width" => $tamannio);
	}
	function addkey($nombre, $show = false){
		$DKey	= array("key" => "true"); 
		$DKey["list"]	= ($show == false) ? "false" : "true";
		if(setCadenaVal($show) != ""){	//si existe es titulo y enable
			$xlng	= new cLang();
			$DKey["list"]	= "true";
			$DKey["title"]	= $xlng->getT("$show");
			$DKey["width"]	= "10%";
		}
		$this->mCampos[$nombre] = $DKey;
	}	
	function getJs($init = false, $enclose = false){
		$flds		= "";
		foreach ($this->mCampos as $campos => $items){
			$flds	.= ($flds == "") ? "$campos : {" : ",$campos : {";
				foreach ($items as $props => $vals){
					$flds	.= ($vals == "true" OR $vals == "false") ? "\"$props\" : $vals," : "\"$props\" : \"$vals\",";
				}
			$flds	.= "}";
		}
		$acts	= "";
		foreach ($this->mActions as $act => $url){
			$acts	.= ($acts == "") ? "$act : '$url'" : ", $act : '$url'";
		}
		$sinit		= ($init == false) ? "" : "$('#". $this->mId . "').jtable('load');";
		$str		= "$('#" . $this->mId . "').jtable({
        title: '" . $this->mTitle . "',
        actions: { $acts },
        fields: { $flds } }); $sinit";
		return ($enclose == false) ? $str: "<script>$str</script>";
	}
	function getJsHeaders(){
		return '<link href="../css/jtable/lightcolor/orange/jtable.min.css" rel="stylesheet" type="text/css" /><script src="../js/jtable/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script><script src="../js/jtable/jquery.jtable.js" type="text/javascript"></script>';
	}
	function getDiv(){ return "<div id='" . $this->mId  . "'></div>"; }
}
/*function getResourceLang ( $Txt ){
	$mLang								= "ES";
	
	$mText								= array();
	
	$mText["ES"]["RecordSucess"]		= "EL REGISTRO SE HA AGREGADO SATISFACTORIAMENTE";
	$mText["ES"]["AccountNotOperate"]	= "LA CUENTA NO ES OPERATIVA";
	
	return $mText[ $mLang ][ $Txt ];
}*/


class cHMenuItem {
	private $mDatos		= array();
	public $CLAVE		= 0;
	public $TIPO		= "command";
	public $NOTA		= "";
	public $TITULO		= "";
	public $DESTINO		= "";
	public $ARCHIVO		= "";
	public $ICON		= "";
	public $PARENT		= 0;
	private $mEventPa	= "";
	function __construct($id, $datos = false){
		$xMen	= new cGeneral_menu();
		if(is_array($datos)){
			$xMen->setData($datos);
		} else {
			
			$xMen->setData( $xMen->query()->initByID($id) );
		}
		$this->CLAVE	= $xMen->idgeneral_menu()->v();
		$this->NOTA		= $xMen->menu_description()->v(OUT_TXT);
		$this->TIPO		= $xMen->menu_type()->v();
		$this->DESTINO	= $xMen->menu_destination()->v(OUT_TXT);
		$this->ARCHIVO	= $xMen->menu_file()->v(OUT_TXT);
		$this->ICON		= $xMen->menu_image()->v(OUT_TXT);
		$this->TITULO	= $xMen->menu_title()->v();
		$this->PARENT	= $xMen->menu_parent()->v();
	}
	function getLi($WithImages = false, $html = "", $extraTags = ""){
		$Clave		= $this->CLAVE;
		$Tipo		= $this->TIPO;
		$Titulo		= ucfirst($this->TITULO);
		$Imagen		= $this->ICON;
		$TipoDeDes	= $this->DESTINO;
		$Archivo	= $this->ARCHIVO;
		$Descrip	= $this->NOTA;
		$Descrip	= ($Descrip == "" OR $Descrip == "NO_DESCRIPTION") ? "": "title=\"$Descrip\"";
		$isMobile	= $_SESSION[SYS_CLIENT_MOB];
			
		if( SAFE_LANG != "ES" AND SYS_TRADUCIR_MENUS == true){ $Titulo	= $xL->getT("TR.$Titulo");	}
			
		$mImagen		= "";
		if(($WithImages == true OR $isMobile == true) AND $this->PARENT > 0){
			$xBtn			= new cHButton();
			$mImagen		= $xBtn->setIcon($Imagen, "fa-lg");
		}
		$mCmd			= $this->getTipoDestino($TipoDeDes);
		$id				= "";
		if(MODO_DEBUG == true AND $this->PARENT > 0){ $id = "<span>$Clave</span>"; }
		$mCmd			= "onclick=\"$mCmd('$Archivo')\"";
		$dKey			= "";
		if($Tipo == "parent"){
			$dKey		= "data-key='$Clave'";
			$mCmd		= "";
			if($isMobile == true){ $mCmd = "onclick='jsGetMenuChilds(this.id)'"; }
		}
		$menu			= "<li id=\"md_$Clave\"><a  id=\"amenu_$Clave\" $mCmd $extraTags $dKey>$mImagen$Titulo$id</a>$html</li>\n";
		return $menu;		
	}
	private function getTipoDestino($tipodestino){
		$destino		= "";
		switch ($tipodestino){
			case "principal":
				$destino	= "setInFrame";
				break;
			case "tiny":
				$destino	= "getNewTiny";
				break;
			default:
				$destino	= "getNewWindow";
				break;
		}
		return $destino;
	}	
}

class cHMenu {
	private $mType		= "html";
	private $mDevice	= "desktop";
	private $mID		= "jMenu";
	private $mIncImages	= false;
	private $mKeyEvent	= "";
	private $mFilter	= "";
	
	public $PARENT		= "parent";
	public $DESKTOP		= "desktop"; 
	private $mIsMobile	= false;
	
	function __construct($type= OUT_HTML, $Device = "desktop"){
		$this->mDevice	= $Device;
		$this->mType	= $type;
	}
	function setIsMobile($mobile = true){ $this->mIsMobile = $mobile; $this->mIncImages = true; }
	
	function setID($id){ $this->mID	= $id; }
	function setKeyEvent($evt){ $this->mKeyEvent = $evt; }
	function getAll($liTags = ""){
		
		$xL				= new cLang();
		$xQl			= new MQL();
		$xBtn			= new cHButton();
		$ConHijos 		= ($this->mIsMobile == false) ? true : false;
		$pUSRNivel 		= $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
		$menu			= "";
		
		$this->mFilter	= " AND (FIND_IN_SET('$pUSRNivel@rw', menu_rules)>0	OR FIND_IN_SET('$pUSRNivel@ro', menu_rules)>0) ";
		$filter			= $this->mFilter;
  		$sql_TN1 		= "SELECT * FROM general_menu WHERE menu_parent=0 $filter AND menu_parent = 0 ORDER BY menu_order ";
  		$mmenu			= null;
  		$xCache			= new cCache();
  		$idcache		= ($ConHijos == true) ? "menu.childs.$pUSRNivel"  : "menu.normal.$pUSRNivel";
  		if($xCache->isReady() == true){
  			$mmenu		= $xCache->get($idcache);
  		}
		if($mmenu == null){
			$rs				= $xQl->getDataRecord($sql_TN1);
	  		foreach ($rs as $rw){
	  			$Clave		= $rw["idgeneral_menu"];
	  			$xItem		= new cHMenuItem($Clave, $rw);
	  			$mxItem		= "";
	  			$run		= true;
	  			if($Clave == 4000 AND MODULO_SEGUIMIENTO_ACTIVADO == false){ $run = false;	}
	  			if($Clave == 5000 AND MODULO_CONTABILIDAD_ACTIVADO == false){ $run = false;	}
	  			if($Clave == 7000 AND MODULO_AML_ACTIVADO == false){ $run = false;	}
	  			if($Clave == 8000 AND MODULO_CAPTACION_ACTIVADO == false){ $run = false;	}
	  			if($run == true){ 			  			
	  			if($ConHijos == true){
	  				$mxItem		= $this->getItems($Clave);
	  				$mxItem		= (trim($mxItem) == "") ? "" : "<ul>" . $mxItem . "</ul>";
	  			}
	  			
	  			$menu		.= $xItem->getLi($this->mIncImages, $mxItem, $liTags);
	  			}
	  		}
	  		$mmenu			= "<ul id=\"" . $this->mID . "\">"  . $menu . "</ul>";
		}
  		return $mmenu;
	}
	function getItems($parent = 0){
		$menu		= "";
		$ConHijos 	= ($this->mIsMobile == false) ? true : false;
		$ql			= new MQL();
		$filter		= $this->mFilter;
		$sql		= "SELECT * FROM `general_menu` WHERE (`general_menu`.`menu_parent` =$parent) $filter ORDER BY menu_order";
		$rs			= $ql->getDataRecord($sql);
		foreach($rs as $rw){
			$Clave		= $rw["idgeneral_menu"];
			$xItem		= new cHMenuItem($Clave, $rw);
			$subMenu	= "";
			if( ($xItem->TIPO == $this->PARENT) AND ($ConHijos == true) ){
				$subMenu	= $this->getChilds($Clave);
				$subMenu		= (trim($subMenu) == "") ? "" : "<ul>$subMenu</ul>";
			}
			
			$menu		.= $xItem->getLi($this->mIncImages, $subMenu);
		}
		return $menu;		
	}
	function getChilds($MenuParent){
		$ConHijos 	= ($this->mIsMobile == false) ? true : false;
		$filter		= $this->mFilter;
		$ql			= new MQL();
		$sql		= "SELECT *	FROM `general_menu` WHERE (`general_menu`.`menu_parent` = $MenuParent) $filter  ORDER BY menu_order";
		$rs			= $ql->getDataRecord($sql);
		$childs		= "";
		foreach ($rs as $rw){
			$Clave		= $rw["idgeneral_menu"];
			$xItem		= new cHMenuItem($Clave, $rw);
			$subMenu	= "";
			if( ($xItem->TIPO == $this->PARENT) AND ($ConHijos == true) ){
				
				$subMenu	= $this->getChilds($Clave);
				$subMenu		= (trim($subMenu) == "") ? "" : "<ul>$subMenu</ul>";				
			}			
			$childs		.= $xItem->getLi($this->mIncImages, $subMenu);
		}
		return $childs;
	}
	function getSubChilds($MenuParent){
		$ConHijos 	= ($this->mIsMobile == false) ? true : false;
		$menu		= "";
		$ql			= new MQL();
		$filter		= $this->mFilter;
		$sql		= "SELECT * FROM `general_menu` WHERE (`general_menu`.`menu_parent` =$MenuParent) $filter ORDER BY menu_order";
		$rs			= $ql->getDataRecord($sql);
		foreach($rs as $rw){
			$Clave		= $rw["idgeneral_menu"];
			$xItem		= new cHMenuItem($Clave, $rw);
			$subMenu	= "";
			if( ($xItem->TIPO == $this->PARENT) AND ($ConHijos == true) ){
				$subMenu	= $this->getSubChilds($Clave);
			}
			$subMenu		= (trim($subMenu) == "") ? "" : "<ul>$subMenu</ul>";
			$menu		.= $xItem->getLi($this->mIncImages, $subMenu);
		}
		return $menu;
	}
	function add(){  }
	function setIncludeIcons(){ $this->mIncImages	= true; }
	function get(){	}
	function setDevice($Device){ $this->mDevice	= $Device;	}

}

/**
 * Clase que genera una Tabla html generica con funciones CRUD basicas
 * @author Balam Gonzalez Luis Humberto
 * @package core
 * @subpackage core.controls
 *
 */
class cTabla {
	private $mSql;			//Sql
	private $mCampos;		//Campos
	private $mTitles;		//Titles
	private $mTool;			//Addon
	private $mKey;			//Id ield Key
	private $mClassT;		//Clase de la Tabla
	private $mCaption;		//Table caption
	private $mClassTD;		//Clase TD
	private $mClassTH;		//Clase de TH
	private $EventKey		= "";		//Evento de la Llave
	private $Fields			= array();		//Fields
	private $mWidth;		//Ancho
	private $vFields;		//Valor del primer Field
	private $tdClassByType	= false;//	Clase por tipo de datos
	private $mActionExec;	//Evento del Tool Exec
	private $mTbl			= "";			//Nombre de la tabla
	private $mKeyField;		//
	private $mRowCount;		//
	private $mPutType;
	private $mEspTool		= array();		//Herramientas con comandos especiales
	private $mShowProp;
	private $mFieldSum		= array();		//Array assoc
	private $arrWithTilde;
	private $arrTypesPHP	= array();
	private $arrRowCSS		= array();
	private $mRndK			= 0;
	private $mFootSums		= array();
	private $mSumFoot		= true;
	private $mPrepareChart	= false;
	private $mTipoSalida	= "normal";
	private $mDataCustom	= false;
	private $mWidthTool		= false;
	private $mSubSQLs		= array();
	private $mSubsEnable	= false;
	private $mDelimiter		= ",";
	private $mID			= "sqltable";
	private $mColTitles		= array(); 
	private $mEspCMD		= array();
	private $mDicIcons		= null;
	public $T_ELIMINAR		= 2;
	public $T_EDITAR		= 1;
	
	/**
	 * Inicializador de la Clase
	 * @param string	$vSql	SQL que genera la Tabla
	 * @param integer	$vKey	Numero de Campo Clave
	 */
	function __construct($vSql ="", $vKey = 0, $id = "sqltable") {
		$this->mSql 			= $vSql;
		$this->mKey 			= $vKey;
		$this->mTitles 			= "";
		$this->mCaption			= "";
		$this->mCampos 			= "";
		$this->mClassTD 		= "";
		$this->mClassTH 		= "";
		$this->mClassT 			= "";
		$this->mTool 			= array();
		$this->EventKey 		= "";
		$this->Fields 			= array();
		$this->mWidth 			= 95;
		$this->Fields 			= array();
		$this->tdClassByType	= false;
		$this->mActionExec		= "the_action";
		$this->mTbl				= "";
		$this->mKeyField		= "";
		$this->mRowCount		= 0;
		$this->mPutType			= "default";
		$this->mEspTool			= array();
		$this->mShowProp		= false;
		$this->mFieldSum		= array();
		$this->mRndK			= rand(0, 100);
		
		
		//TIPO DE COMPARACION
		$this->arrWithTilde = array(
						"varchar",
						"date",
						"text",
						"tinytext",
						"enum",
						"blob",
						"string",
						"longtext",
						"datetime",
						"time"
						);

		$this->arrTypesPHP = array(
						"" => "string",
						"enum"=> "string",
						"varchar"=>"string",
						"tinytext"=>"string",
						"float"=>"numeric",
						"decimal"=>"numeric",
						"int"=>"numeric",
						"real"=>"numeric",
						"integer"=>"numeric",
						"text"=>"string",
						"longtext"=>"string",
						"blob"=>"string",
						"tinyint"=>"numeric",
						"datetime"=>"string",
						"date"=>"string",
						"time"=>"string"
						);
		$this->mID		= $id;
	}
	function setWidthTool($width){ $this->mWidthTool	= $width; }
	/**
	 * Modo de salida del Archivo
	 * @param string $tipo	Tipo de salida, XML, HTML, CSV
	 */	
	function setTipoSalida($tipo, $delimitador = false){$this->mTipoSalida	= $tipo; $this->mDelimiter = ($delimitador == false) ? $this->mDelimiter : $delimitador; }
	/**
	 * Pie de suma
	 * @var array Array del Tipo columna => campo
	 * @example array(idcolumna => nombre_del_campo_a_sumar)
	*/
	function setFootSum($arr){
		if( is_bool($arr) ){
			$this->mSumFoot	= $arr;
		}
		if( is_array($arr) ){
			$this->mSumFoot		= true;
			$this->mFootSums	= $arr;
		}
	}
	function setSQL($vSql){ $this->mSql = $vSql; }
	/**
	 * Convierte una Tabla en una consulta SQL
	 * @param string $Table
	 * @param string $FieldKeyName
	 * @param string $FieldKeyValue
	 */
	function setTableToSQL($Table, $FieldKeyName = "", $FieldKeyValue = ""){
		$mWhere				= "";
		$mSQL				= "";
		
		if ( $FieldKeyName	!= ""){
			$this->setKeyField($FieldKeyName);
			
			if ( $FieldKeyValue != ""){
				$mWhere		= " $FieldKeyName = $FieldKeyValue ";
			}
		}
		$mSQL				= "SELECT * FROM $Table $mWhere ";
		$this->setSQL($mSQL);
	}
	function setFields($aFields){ $this->Fields = $aFields;	}
	function getValorCampo($campo){ return $this->vFields[$campo];	}
	function setTitles($vTitles = ""){ $this->mTitles = $vTitles; }
	function setClassTH($vTH){ $this->mClassTH =$vTH; }
	/**
	 * funcion que habilita si se muestra en Propietario del Registro
	 * @param boolean $show
	 */
	function setShowPropietary($show = true){ $this->mShowProp	= $show; }
	function setClassT($vT){ $this->mClassT =$vT; }
	/**
	 * Funcion que Agrega Comandos Comuneas a la Tabla
	 * @param integer $id
	 * 0 = Exec
	 * 1 = Edit
	 * 2 = Delete
	 */
	function addTool($id){	array_push($this->mTool, $id);	}
	/**
	 * $DefValue = str_replace("_REPLACE_ID_", $rw[$pKey], $value);
	 */
	function addEspTool($html){ $this->mEspTool[] = $html;	}
	function OButton($titulo, $event, $icon = "", $id = "" ){ 
		$xBtn	= new cHButton();
		$id		= ($id == "") ? "id" . rand(0, 100) : $id;
		
		$this->mEspCMD[]	= $xBtn->getBasic($titulo, $event, $icon, $id, false, true );
		//return $xBtn;	
	}
	function ODicIcons(){
		if($this->mDicIcons == null){ $this->mDicIcons = new cFIcons(); }
		return $this->mDicIcons;
	}
	/**
	 * Establece el Campo Llave que se usara en la tabla para Operaciones
	 * @param integer $id	Numero de campo en el Array
	 */
	function setKey($id=0){	$this->mKey = $id; }
	/**
	 * Establece el Evento del Campo Clave
	 * @param string	$events	Eventos javascript
	 */
	function setEventKey($events){ $this->EventKey = $events; }
	function setWidth($sWidth=100){ $this->mWidth = $sWidth; }
	/**
	 * Aplica un CSS a las Columnas segun el tipo (int,strig,date,etc)
	 */
	function setTdClassByType($AplicarPorTipo = true){
		$this->tdClassByType = $AplicarPorTipo;
	}
	function setActionExec($javascript_function){
		$this->mActionExec 	= $javascript_function;
	}
	function setKeyField($name = ""){
		$this->mKeyField 	= $name;
	}
	/**
	 * Nombre de la Tabla Maestra
	 * @param string $table
	 */
	function setKeyTable($table = ""){	$this->mTbl		= $table;	}
	function setWithMetaData($data = true){$this->mDataCustom = $data; }
	function setColTitle($field, $title){
		$this->mColTitles[$field] = $title;
	}
	/**
	 * Genera la Tabla
	 * @param string	$vCaption	Titulo de la Tabla
	 * @param boolean	$retorna	Retornar la Tabla como Valor? true/false
	 */
	function Show($vCaption="", $retorna = true, $TableID = ""){
		$xHComm		= new cHObject();
		$xD			= new cFecha();
		$xLng		= new cLang();
		$xT			= new cTipos();
		$xQL		= new MQL();
		$xBtn		= new cHButton();
		$xIcs		= $this->ODicIcons();
		$this->mID	= ($TableID == "") ? $this->mID : $TableID;
		if(MODO_DEBUG == true AND isset($_SERVER["REQUEST_URI"]) ){
			//setLog($_SERVER["REQUEST_URI"]);
			if(strpos($_SERVER["REQUEST_URI"], "rpt") === false){ 
				$this->addTool(3);
			}
		}
		$xD->setSeparador("/");
		if($this->mSql!=""){
			//Miembros de las Tablas
			$tds 			= "";
			$ths 			= "";
			//Inicio de Tabla
			$pushInit		= "";
			$pushEnd		= "";
			$vClassT 		= "";
			$vWidth 		= "";
			$tdt 			= "";
			$tht 			= "";
			$tab			= "";
			$tfoot			= "";
			$trick			= 1;
			$capTable		= "";
			$oldTags		= false;
			if($this->mTipoSalida == OUT_EXCEL){ $oldTags = true; }
			//si hay Caption formar el caption
			if($vCaption!="" AND $this->mPrepareChart == false){
				$vCaption 	= $xLng->getT($vCaption);
				$vCaption 	= "<legend>[&nbsp;&nbsp;&nbsp;&nbsp;$vCaption&nbsp;&nbsp;&nbsp;&nbsp;]</legend>";
				$pushInit 	= "<fieldset>$vCaption";
				$pushEnd	= "</fieldset>";
			}
			if($vCaption!="" AND $this->mPrepareChart == true){
				$capTable	= "<caption>$vCaption</caption>";
			}
			if($vCaption!="" AND $this->mTipoSalida == OUT_EXCEL){ $capTable	= "<caption>$vCaption</caption>"; }
			//Clase de la Tabla
			if($this->mClassT != "") {
				$this->mClassT	= " ". $this->mClassT;
			}
			$vClassT = "class=\"listado" . $this->mClassT . "\" ";
			//Contar Tools
			$il 					= count($this->mTool);
			$itmCmd					= count($this->mEspCMD);
			$itmEspTool				= count($this->mEspTool);
			$itmTot					= $il + $itmCmd + $itmEspTool;
			if($itmTot > 0 ){
				$wd					= ( setNoMenorQueCero($this->mWidthTool) <=0 ) ? ($itmTot * 26) . "px" : $this->mWidthTool;
				$tht 				= "<th style='min-width:". $wd  . ";width:". $wd .";'>Acciones</th>";
			}
			// --------------------------------------------------------------
			$rs 					=  getRecordset( $this->mSql );

			if($this->mKeyField == ""){ $this->mKeyField	= mysql_field_name($rs, $this->mKey);	}
			if( $this->mTbl == "" ){ $this->mTbl			= mysql_field_table($rs, $this->mKey);	}

			//Arrays de Nombres y tipo
			$arrFieldNames			= array();
			$arrFieldTypes			= array();
			$preCols				= ( count($this->Fields) <= 1 ) ? false : true;

			//
			$ifl 					= mysql_num_fields($rs) - 1;			//Limite de Fields
			//-------------------------------------------------------------	//Cabeceras de Columnas 
			for($iCols=0; $iCols<=$ifl; $iCols++){
				$fname 				= mysql_field_name($rs, $iCols);
				$ftypes				= mysql_field_type($rs, $iCols);
				$this->Fields[]		= $fname;
				if($this->mKey == 0 AND $this->mKeyField != ""){
					if(strtolower($fname) == strtolower($this->mKeyField)){ $this->mKey = $iCols; }
				}
				$tths 				= "";
				if(isset($this->mColTitles[$fname])){
					$tths 			= $this->mColTitles[$fname]; 
				} else {
					$tths 			= strtoupper(str_replace("_", " ", $fname));
				}
				$tths				= $xLng->getT("TR.$tths");
				$cssTh				= ($iCols == $this->mKey) ? " class='key' " : "";
				$scope				= ($this->mPrepareChart == true) ? " scope='col' " : "";
				//Mejorar codigo
				if($this->mTipoSalida == OUT_EXCEL){
					$ths 				.= "<th class=\"xl25\">$tths</th>";
				} else if($this->mTipoSalida == OUT_TXT OR $this->mTipoSalida == OUT_CSV){
					$ths 				.= ($iCols == 0) ? $tths : $this->mDelimiter . $tths;
					$ths				.= ($iCols == $ifl) ? "\r\n" : "";
				} else {
					$ths 				.= ($this->mPrepareChart == true AND ($iCols == $this->mKey) ) ? "<td></td>" : "<th $scope $cssTh>$tths</th>";
				}
				/**
				 * Construye el Array de Tipos y Nombres
				 */
				$arrFieldNames[$iCols]		= $fname;
				$arrFieldTypes[$iCols]		= $ftypes;
			}

			//$ths . "\n $tht";
			// --------------------------------------------------------------
			while ($rw = mysql_fetch_array($rs)){
				$this->mRowCount++;
				$tdt 			= "";
				$pKey 			= $this->mKey;
				$idKey			= ( is_string($rw[$pKey]) == true ) ? "'" . $rw[$pKey] . "'" : $rw[$pKey];
				$dataCustom		= "";
				$trick			= ($trick >= 2) ? 1 : $trick + 1; //switch oddr
				$lis			= "";
				$liCss			= ($trick == 2) ? "tags green" : "tags blue";
				/*$t = array(
					0 => $xBtn->getBasic("TR.Ejecutar", $this->mActionExec . "($idKey)", ) "<img src='../images/common/execute.png' onclick=\"" . $this->mActionExec . "($idKey);\" title='Ejecutar Una acci&oacute;n' />",
					1 => "<img src='../images/common/edit.png' onclick=\"if(typeof actualizame" . $this->mRndK . " != 'undefined'){actualizame" . $this->mRndK . "($idKey);} else {jsUp('" . $this->mTbl . "','" . $this->mKeyField . "', $idKey); } \" title='Editar Registro' />",
					SYS_DOS => "<img src='../images/common/trash.png' onclick=\"var xG = new Gen(); xG.rmRecord({tabla:'" . $this->mTbl .  "',id:" . $idKey . "});\" title='Eliminar Registro' />"
				);*/
				$t = array(
					0 => $xBtn->getBasic("TR.Ejecutar", $this->mActionExec . "($idKey)", $xIcs->EJECUTAR, "exec$idKey", false, true ),
					1 => $xBtn->getBasic("TR.Editar", "if(typeof actualizame" . $this->mRndK . " != 'undefined'){actualizame" . $this->mRndK . "($idKey);} else {jsUp('" . $this->mTbl . "','" . $this->mKeyField . "', $idKey); }", $xIcs->EDITAR, "redit$idKey", false, true ),
					2 => $xBtn->getBasic("TR.Eliminar", "var xG = new Gen(); xG.rmRecord({tabla:'" . $this->mTbl .  "',id:" . $idKey . "});", $xIcs->ELIMINAR, "rdel$idKey", false, true ),
					3 => $xBtn->getBasic("TR.Ver", "var xDev=new SafeDev(); xDev.recordRAW({tabla:'" . $this->mTbl .  "',id:" . $idKey . "});", $xIcs->EXPORTAR, "idcmdview$idKey", false, true )
				);
							
				foreach ($this->mTool as $idx => $clave){ $lis .= isset($t[$clave]) ? "<li>" . $t[$clave] . "</li>" : ""; }
				foreach ($this->mEspCMD as $idx => $cnt){ $lis .= "<li>" . str_replace(HP_REPLACE_ID, $rw[$pKey], $cnt) . "</li>"; }
				
				$tdt		= ($lis == "") ? "" : "<ul class='$liCss'>$lis</ul>";
				/**
				 * Herramientas especiales
				 */
				foreach ($this->mEspTool as $key => $value) {
					$DefValue 	= str_replace(HP_REPLACE_ID, $rw[$pKey], $value);
					//validar si no es PHP
					if(strpos($DefValue, "PHP::")!== false ){
						$cod	= str_replace("PHP::","\$DefValue=", $DefValue);
						eval($cod);
					}
					$tdt		.= "\n " . $DefValue;
				}
				
				$tdt 		= ( trim($tdt) != "" ) ? "<td id=\"options-" . $rw[$pKey] . "\" class=\"toolbar-24\">$tdt</td>" : "";
				if($this->mTipoSalida == OUT_EXCEL){ $tdt	= ""; }
				//-------------------------------------------------------
				$ttds 				= "";
				for($i=0; $i<=$ifl; $i++){
					$event 		= "";
					$css 		= "";
					$oprp		= "";
					$currVal	= $rw[$i];
					$sanID		= str_replace(" ", "_", $currVal);
					$type 		= ( isset($arrFieldTypes[$i]) )  ? $arrFieldTypes[$i] : "text";
					$name		= $arrFieldNames[$i];
					$scope		= "";
					$delims		= "td";
					$mkEnd		= "";
					$extraCNT	= "";
					if ($i == $this->mKey){
						$event 	= $this->EventKey;
						if($event!=""){
							$event = "<a class='button' onclick=\"$event('$rw[$i]');\" >";
							$mkEnd	= "</a>";
						}
						$css 		.= "key";
						$oprp		= " id=\"pk-" . $sanID . "\" ";
						$scope		= ($this->mPrepareChart == true ) ? "scope=\"row\"" : "";
						$delims		= ($this->mPrepareChart == true ) ? "th" : "td";
					}
					/**
					 *Agrega el data Custom
					 */
					if($this->mDataCustom == true){	$dataCustom	.= ($dataCustom == "") ? "$name=$currVal" : "|$name=$currVal";	}
					/**
					 * Agrega una suma si el tipo aplica
					 */
					if( isset($this->arrTypesPHP[$type]) AND $this->arrTypesPHP[$type] == "numeric") {
						if ( !isset($this->mFieldSum[$name])  ) { 
							$this->mFieldSum[$name] = 0;
						}
						$this->mFieldSum[$name]		+= $currVal;
					}
					//Agregar sub consulta
					/*if($this->mSubsEnable	== true){
						$sqlSub			= "";
						//si no encuentra
						if(isset($this->mSubSQLs[$i])){
							$sqlSub		= $this->mSubSQLs[$i];
						} elseif(isset($this->mSubSQLs[$name])){
							$sqlSub		= $this->mSubSQLs[$name];
						} else {
							if ($i == $this->mKey OR $name == $this->mKeyField){
								$sqlSub		= (isset($this->mSubSQLs[0])) ? $this->mSubSQLs[0] : "";
							}
						}
						if($sqlSub == ""){
							
						} else {
							$sqlSub			= str_replace(HP_REPLACE_ID,$currVal, $sqlSub);
							//if($currVal == 	200039104){setLog($sqlSub);}
							$mql			= new MQL();
							$data			= $mql->getDataRecord($sqlSub);
							$cnt			= "";
							$outc			= new cHObject();
							
							foreach($data as $info){
								foreach($info as $pfield => $pvalue){
									$cnt		.= $pvalue . "\r\n";
								}
							}
							$extraCNT			.= $outc->Out($cnt, OUT_HTML);
						}
					}*/
					//Tipo de Dato
					/**
					 * Si la Opcion de Class por tipo es verdadero
					 * formatea cada Td como su tipo
					 */
					$oStr		= array("string" => "string", "varchar" => "varchar", "text" => "text", "tinytext" => "tinytext");
					if(!isset($oStr[$type])){
						$css	= ($css == "") ? $type : "$css $type";
					}
					if ( isset($this->arrRowCSS[$name]) ){
						$css	= ($css == "") ? $type : "$css " . $this->arrRowCSS[$name];
					}
					$css	= ($css == "") ? "" : " class=\"$css\" ";
					
					//Formatea a Moneda el valor si es tpo real
					if($type == "real") { $currVal 	= ($this->mPrepareChart == true OR $this->mTipoSalida == OUT_EXCEL) ? round($currVal,2) : getFMoney($currVal); }
					if($type == "date"){ $currVal 	=  $xD->getFechaMX($currVal); }
					if(isset($oStr[$type])){ $currVal 	= $xHComm->Out($currVal, OUT_HTML); }
					
					if($this->mTipoSalida == OUT_EXCEL){
						//class=xl2216681 nowrap
						$ttds 			.= "<$delims class=\"xl25\" >" . $currVal . "</$delims>";
					} else if($this->mTipoSalida == OUT_TXT OR $this->mTipoSalida == OUT_CSV){
						$ttds 				.= ($i == 0) ? $currVal : $this->mDelimiter . $currVal;
					} else {
						$css			= ($this->mPrepareChart == true) ? "" : $css;
						$ttds 			.= "<$delims $scope $css $oprp>$event" . $currVal . "$mkEnd$extraCNT</$delims>";
					}
				}
				//
				
				$trcss	= ($trick >= 2) ? " class='trOdd' " : "";
				if($this->mTipoSalida == OUT_EXCEL){
					$tds 				.= "<tr>$ttds$tdt</tr>\r\n";
				} else if($this->mTipoSalida == OUT_TXT OR $this->mTipoSalida == OUT_CSV){
					$tds 				.= $ttds . "\r\n";
				} else {
						$dataCustom		= ($dataCustom != "") ? " data-info=\"$dataCustom\" " : "";
						$tds 			.= "<tr id=\"tr-" . $this->mTbl . "-".str_replace(" ", "_", $rw[$this->mKey]) . "\"$dataCustom $trcss>$ttds $tdt </tr>";
				}
				if($this->mSubsEnable	== true){
					foreach ($this->mSubSQLs as $idx => $sqls){
						if($sqls != ""){
							$sqlSub			= str_replace(HP_REPLACE_ID, $rw[$this->mKey], $sqls);
							$mql			= new MQL();
							$data			= $mql->getDataRecord($sqlSub);
							$outc			= new cHObject();
							foreach($data as $info){
								foreach($info as $pfield => $pvalue){
									$cnt		.= $pvalue . "\r\n";
								}
							}
							$extraCNT		.= $outc->Out($cnt, OUT_HTML);
							$icx22			= $ifl+2;
							$tds 			.= "<tr id=\"tr-subs-" . $this->mTbl . "-".str_replace(" ", "_", $rw[$this->mKey]) . "\"><td colspan='$icx22'>$extraCNT</td></tr>";
						}
					}
				}
		
			}
			$this->vFields 			= $rw;
			// --------------------------------------------------------------
			@mysql_free_result($rs);
			$ism	= $this->mFootSums;
			//sumas
			$tfoot	= "";
			for($i=0; $i<=$ifl; $i++){
				if(isset($ism[$i]) ){
					if($this->mTipoSalida == OUT_EXCEL){
						$tfoot	.= ( isset($this->mFieldSum[ $ism[$i] ]) ) ? "<th>" . getFMoney($this->mFieldSum[ $ism[$i] ]) . "</th>" : "<td></td>";
					} else {
						$tfoot	.= ( isset($this->mFieldSum[ $ism[$i] ]) ) ? "<th><input type='hidden' id='idsum-" .  $ism[$i] . "' value='" . $this->mFieldSum[ $ism[$i] ] . "' /><mark id='sum-" .  $ism[$i] . "'>" . getFMoney($this->mFieldSum[ $ism[$i] ]) . "</mark></th>" : "<td></td>";
					}
				} else {
					$tfoot	.= ($i==0) ? "<th>Filas: " . $this->mRowCount . "</th>" : "<td></td>";
				}
			}
			$tfoot	= ($oldTags == true) ? "<tr>$tfoot</tr>" : "<tfoot><tr>$tfoot</tr></tfoot>";
			
			$tfoot	= ( $this->mSumFoot == false OR $this->mPrepareChart == true) ? "" : $tfoot;
			//Da Salida  a la Tbla
			$mID	= $this->mID;
			$aProps	= ($this->mTipoSalida == OUT_EXCEL) ? " x:str border=0   style='border-collapse: collapse' " : " $vClassT id=\"$mID\"";
			$aProps	= ($this->mPrepareChart	== true ) ? "  style=\"display: none; text-align: center \" > " : $aProps;
			$tBody	= ($oldTags == true) ? "$tds" : "<tbody>$tds</tbody>";
			$tHead	= ($oldTags == true) ? "<tr>$ths$tht</tr>" : "<thead><tr>$ths$tht</tr></thead>";
			 
			$tHead	= (trim($ths) == "") ? "" : $tHead;
			if($this->mTipoSalida == OUT_TXT OR $this->mTipoSalida == OUT_CSV){
				$tab	= "$ths $tds";
			} else {
				$tab	= "$pushInit<table$aProps>$capTable$tHead$tBody$tfoot</table>$pushEnd";
			}
			if($retorna == true){
				return $tab;
			} else {
				echo $tab;
			}
		}
	}
	/**
	 * Obtiene el Numero de Filas de la tabla
	 */
	function getRowCount(){
		return $this->mRowCount;
	}
	/**
	 * Obtiene en codigo JavaScript acciones CRUD.
	 */
	function getJSActions($snipt	= false){
		//return $this->mTbl;
		$str = "
	function jsUp(t, f, id) {
		var urlE = \"../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=\" + t + \"&f=\" + f + \"=\" + id;
		var xG	= new Gen(); xG.w({url : urlE, w: 800, h: 600, tiny : true });
	}
	function jsDel(t, f, id) {
		var siXtar = confirm(\"Desea en Realidad Eliminar \\n el Registro Seleccionado\");
		if(siXtar==true){
			var sURL = \"../utils/frm9d23d795f8170f495de9a2c3b251a4cd.php?t=\" + t + \"&f=\" + f + \"=\" + id;
				delme = window.open(sURL, \"\", \"width=300,height=300,scrollbars=yes,dependent\");
				document.getElementById(\"tr-\" + t + \"-\" + id).innerHTML = \"\";
		} else {
				if( window.console ) { window.console.log( '' ); }
				window.statusText = \"Operacion Cancelada\";

		}
	}
";
		if($snipt == true){
			$str	= "<script type=\"text/javascript\" >" . $str . "</script>";
		}
		return $str;
	}
	function Todo(){  }
        /**
         * Retorna un Array con la suma por columna, debe ir despues de Show()
         * @return $array
         * */
	function getFieldsSum($index = false){
		$valor	= 0;
		if($index !== false){
			$valor	= (isset($this->mFieldSum[$index])) ? setNoMenorQueCero($this->mFieldSum[$index]): 0;
		}
		return ($index == false) ? $this->mFieldSum : $valor; }
	/**
	 * Agrega un CSS al campo por nombre.
	 * @example NombreColumna, .CSSType
	 * @param string $field
	 * @param string $css
	 */
	function setRowCSS($field, $css){
		//
		$this->arrRowCSS[$field] = $css;
	}
	function setPrepareChart($prepare  = true){ $this->mPrepareChart = $prepare;}
	function getSQL(){ return $this->mSql; }
	function addSubQuery($sql, $field = 0){
		$this->mSubSQLs[$field] = $sql; $this->mSubsEnable	= true;
	}
}
class cSelect{
	private $mSql 					= "";
	private $mEspOption 			= array();
	private $mId 					= "";
	private $mName 					= "";
	private $mEsSql 				= false;
	private $mOptionSelect 			= "";
	private $mSqlWhere				= "";
	private $mFieldValue 			= 0;
	private $mFieldCaption 			= 1;
	private $mSqlLimit 				= "";
	private $mRs 					= false;
	private $mEvents 				= array();
	private $mPut 					= "default";
	private $mNRows 				= 1;
	private $mEventsValue			= array();
	protected $mLabelSize			= 0;
	private $mCerrar				= false;
	private $mLabel					= "";
	private $mCount					= 0;
	private $mNoMayus				= false; 
	function __construct($name, $id = "", $sql = ""){
		$this->mId 		= $id;
		$this->mName 	= $name;
		$this->mSql 	= $sql;
		if($name == "ereport" OR $id == "ireport"){ $this->setNoMayus(); }
	}
	function setCerrar($cerrar = true){ $this->mCerrar = $cerrar; }
	function setLabel($lbl = ""){ $this->mLabel = $lbl; }
	function addEspOption($value, $caption = ""){
		$caption	= ($caption == "") ? strtoupper($value) : $caption;
		$this->mEspOption[$value] = $caption;
	}
	function addEvent($event, $func, $value = false){
		if ( strpos($event, ")") > 0){
			
		}
		$this->mEvents[$event] 	= $func;
		if ($value != false){
			$this->mEventsValue[$event]		= $value;
		}
	}
	function setSqlLimit($limit = "0,1"){
		$this->mSqlLimit = " LIMIT " . $limit;
	}
	function setSql($sql){
		$this->mSql = $sql;
	}
	function setSqlWhere($where = ""){
		$this->mSqlWhere = " WHERE " . $where;
	}
	/**
	 * Establece si se trata de un SQL o NO
	 * @param boolean $bool
	 */
	function setEsSql($bool = true){ 	$this->mEsSql = $bool;	}
	function setOptionSelect($option){ 	$this->mOptionSelect = $option; }
	function setPut($type = "xul"){ 	$this->mPut = $type; }
	function setNRows($rows = 1){ 		$this->mNRows = $rows; }
	function get($label = "",$cerrar = false){
		if(is_bool($label)){ $cerrar = $label; $label = "";}
		$xL		= new cLang();
		$label	= ($label == "") ? $this->mLabel : $label;
		$cerrar	= ($cerrar == false) ? $this->mCerrar : $cerrar;
		$label	= ($label == "") ? "" : $xL->getT($label);	
		$lbl	= "";
		if ($label != ""){ $lbl	= "<label for='" . $this->mId . "'>$label</label> " ; }
		return ($cerrar == false ) ? "" . $lbl . $this->show(true) . "" : "<div class='tx4'>" . $lbl . $this->show(true) . "</div>";
	}
	function setNoMayus(){ $this->mNoMayus = true; }
	function show( $return = true){
		$pSql 		= "";
		$ctrl 		= "";
		$ops 		= "";
		$cnam 		= "";
		$cid 		= "";
		$pEvts 		= "";
		$pOpts 		= "";
		$extraLbl	= "";
		$xT			= new cTipos(0);
		$rw 		= array();
		//$xQl		= new MQL();
		//Sql
		if($this->mEsSql == false){
			$pSql = "SELECT * FROM " . $this->mSql;
		} else {
			$pSql = $this->mSql;
		}
		if($this->mSqlWhere !=""){
			$pSql = $pSql . $this->mSqlWhere;
		}
		if($this->mSqlLimit != ""){
			$pSql = $pSql . $this->mSqlLimit;
		}
		//if(trim($pSql) != ""){
		$this->mRs = getRecordset($pSql);
		//}
		if($this->mRs){
			//saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|||Numero: " .mysql_errno() . "|||Instruccion SQL:". $pSql . "|EN:" . $_SESSION["current_file"]);
			//return 0;
			//exit();
			while($rw = mysql_fetch_array($this->mRs)){
				$slt 	= "";
				$ival 	=  $rw[$this->mFieldValue];
				//$ival	= $xT->setNoAcentos( $ival );
				if($this->mNoMayus == false){
					$ival	= strtoupper($ival);
				}
				$icap	= (isset($rw[$this->mFieldCaption])) ? $rw[$this->mFieldCaption] : $rw[$this->mFieldValue];
				$icap 	= str_replace('"', "'", $icap);
				//$icap	= htmlentities($icap);
				//$icap	= $xT->setNoAcentos( $icap );
				$icap	= $xT->cMayusculas($icap);
				//$icap	= $xT->cChar($icap);
				if($this->mOptionSelect==$ival){
					switch ($this->mPut){
						case "xul":
							$extraLbl 	= $rw[$this->mFieldValue];
						break;
						default:
							$slt 		= " selected = \"true\" ";
						break;
					}
	
				}
					switch ($this->mPut){
						case "xul":
						$ops = $ops . "
						<listitem label=\"$icap\" value=\"$ival\"$slt/>";
							break;
						case "xul-menu":
						$ops = $ops . "
						<menuitem label=\"$icap\" value=\"$ival\"$slt/>";
							break;
						default:
						$ops = $ops . "
						<option value=\"$ival\"$slt>$icap</option>";
						break;
					}
					$this->mCount++;
			}
		}
		//busca si hay ID
		if($this->mId ==""){
			$cid = " id=\"id" . $this->mName . "\" ";
		} else {
			$cid = " id=\"" . $this->mId . "\" ";
		}

		$iEvts 		= sizeof($this->mEvents);
		$iEspOpts 	= sizeof($this->mEspOption);
		if($iEvts > 0){
			foreach ($this->mEvents as $key => $value) {
				$tmpValue	= "";
				if ( isset( $this->mEventsValue[ $key ] ) ){
					$tmpValue	= "'" . $this->mEventsValue[ $key ] . "'";
				}
				if ( strpos($value, ")") > 0){
					$pEvts = $pEvts . " $key=\"$value;\" ";
				} else {
					$pEvts = $pEvts . " $key=\"$value(" . $tmpValue . ");\" ";
				}
				
			}
		}
		if($iEspOpts>0){
			foreach ($this->mEspOption as $tmpOp => $tmpCap) {
			$sltt = "";
				if($this->mOptionSelect == $tmpOp ){ $sltt = " selected = \"true\" "; }
				switch ($this->mPut){
					case "xul":
				$pOpts = $pOpts . "
						<listitem label=\"$tmpCap\" value=\"$tmpOp\" $sltt />";
						break;
					case "xul-menu":
				$pOpts = $pOpts . "<menuitem label=\"$tmpCap\" value=\"$tmpOp\" $sltt />";
						break;
					default:
				$pOpts = $pOpts . "<option value=\"$tmpOp\"$sltt>$tmpCap</option>";
					break;
				}
			}
		}

		switch ($this->mPut){
			case "xul":
			$ctrl = "<listbox rows=\"" . $this->mNRows . "\" $cid>
				$ops
				$pOpts
			</listbox>";
				break;
			case "xul-menu":
				//rows=\"" . $this->mNRows . "\"
			$ctrl = "<menulist label=\"$extraLbl\" $cid>
			<menupopup>
				$ops
				$pOpts
			</menupopup>
			</menulist>";
				break;
			default:
			$ctrl = "<select size=\"" . $this->mNRows . "\" name=\"" . $this->mName . "\" $cid $pEvts>
				$ops
				$pOpts
			</select>";
			break;
		}


		if($return == true){
			return $ctrl;
		} else {
			echo $ctrl;
		}
	}
	function __destruct(){
		@mysql_free_result($this->mRs);
	}
	function setLabelSize($tamanno){
		$this->mLabelSize	= $tamanno;
	}	
	function getSQL(){ return  $this->mSql; }
	function getCountRows(){ return $this->mCount; } 
}


class cPanelDeReportesContables {
	private $mOFRM				= null;
	function __construct($ConPeriodos = true, $ConCuentas = true){
		$this->mOFRM	= new cHForm("reportescontable", "", "reportescontable", "");
		$this->mOFRM->setFieldsetClass("fieldform frmpanel");
		$this->mOFRM->OButton("TR.Obtener Reporte", "jsGetReporte()", "reporte", "cmdgetreporte");
		if($ConPeriodos == true){ $this->addPeriodoInicial(); }
		if($ConCuentas == true){ $this->addCuentaInicial(); }
		
	}
	function addPeriodoInicial(){
		$xSel	= new cHSelect();
		$this->mOFRM->addDivSolo($xSel->getListaDeAnnos("idejercicioinicial")->get(false), $xSel->getListaDeMeses("idperiodoinicial")->get(false), "tx24", "tx24");
	}
	function addPeriodoFinal(){
		$xSel	= new cHSelect();
		$this->mOFRM->addDivSolo($xSel->getListaDeAnnos("idejerciciofinal")->get(false), $xSel->getListaDeMeses("idperiodofinal")->get(false), "tx24", "tx24");		
	}
	function addMoneda(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem($xSel->getListaDeMonedas()->get("TR.Moneda", true) );
	}
	function addCuentaInicial(){
		$xTxt	= new cHText();
		$this->mOFRM->addHElem( $xTxt->getDeCuentaContable("idcuentainicial", ZERO_EXO));
	}
	function addCuentaFinal(){
		$xTxt	= new cHText();
		$this->mOFRM->addHElem( $xTxt->getDeCuentaContable("idcuentafinal", ZERO_EXO));		
	}
	function addTipoDeCuentas(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem($xSel->getListaDeTiposDeCuentasContables("", true, SYS_TODAS)->get(true) );		
	}
	function addNivelesDeCuentas(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem( $xSel->getListaDeNivelesDeCuentasContables("idniveldecuenta", true, SYS_TODAS)->get(true) );
	}
	function addEstadoDeMovimiento(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem( $xSel->getListaDeEstadoMvtosDeCuentasContables()->get(true));
	}
	function render(){ return $this->mOFRM->get(); }
	function addFechaInicial($fecha = false){
		$xF	= new cFecha(0, $fecha);
		$this->mOFRM->ODate("idfechainicial", $xF->getDiaInicial(), "TR.Fecha Inicial");
	}
	function addFechaFinal($fecha = false){
		$xF	= new cFecha(0, $fecha);
		$this->mOFRM->ODate("idfechafinal", $xF->getDiaFinal(), "TR.Fecha Final");		
	}
}

class cPanelDeReportes {
	private $mFechaInicial		= "";
	private $mFechaFinal		= "";
	private $mTiposSalida		= "";
	private $mSucursales		= "";
	
	private $mCreditosFrecPagos	= "";
	private $mCreditosEstatus	= "";
	private $mCreditosProductos	= "";
	private $mTipo			= "";
	private $mForceRecibos		= true;
	private $mForceOperaciones	= false;
	private $mForceCredito		= false;
	private $mForceCajeros		= false;
	
	private $mFiltro			= "";
	private $mStruct			= "";
	private $mTitle				= ""; 
	private $mSelectReports		= "";
	private $mJsVars			= "function jsGetReporte(){\r\n";
	private $mURL				= "\"\"";
	private $mHtml				= "";
	private $mConFecha			= true;
	private $mFooterBar			= "";
	private $mOFRM				= null;
	function __construct($tipo = iDE_CREDITO, $filtro = "", $addList = true){
		$this->mTipo	= $tipo;
		$this->mFiltro	= $filtro;
		$this->mOFRM	= new cHForm("frmpanel");
		$this->mOFRM->OButton("TR.Obtener Reporte", "jsGetReporte()", "reporte", "cmdgetreporte");
		//$this->mOFRM->setFieldsetClass("fieldform frmpanel");
		$SqlRpt			= "SELECT * FROM general_reports WHERE aplica='" . $this->mFiltro . "' ORDER BY `order_index` ASC,`idgeneral_reports` ";
		$cSRpt			= new cSelect("idreporte", "idreporte", $SqlRpt );
		$cSRpt->setEsSql();
		$cSRpt->setNoMayus();
		$cSRpt->addEvent("onblur", "if(typeof jsBlurListaDeReportes !='undefined'){ jsBlurListaDeReportes(); }");
		$this->mJsVars	.= "var idreporte	= $('#idreporte').val();\r\n";
		$this->mSelectReports		= $cSRpt->get("TR.Nombre del Reporte", true);
		if($addList == true){
			$this->mOFRM->addHElem( $this->mSelectReports );
		}
	}
	function addListReports(){ $this->mOFRM->addHElem( $this->mSelectReports ); }
	function setConRecibos($force = true){ $this->mForceRecibos	= $force; }
	function setTitle($title){ $this->mTitle	= $title; } 
	function setConOperacion($force = true){ $this->mForceOperaciones	= $force; }
	function setConCreditos($force = true){ $this->mForceCredito	= $force; }
	function setConCajero($force = true){ $this->mForceCajeros = $force; }
	function setConFechas($fechas = false){ $this->mConFecha = $fechas; }
	function addFooterBar($html = ""){ $this->mFooterBar .= $html; }
	function OFRM(){ return $this->mOFRM; }
	function addTipoDeCuentaDeCaptacion(){
		$xSel			= new cHSelect();
		$xOb			= $xSel->getListaDeTipoDeCaptacion();
		$xOb->addEspOption(SYS_TODAS);
		$xOb->setOptionSelect(SYS_TODAS);
		$this->OFRM()->addHElem( $xOb->get(true)  );
		$this->mJsVars	.= "var idtipodecuenta	= $('#idtipodecuenta').val();\r\n";
		$this->mURL		.= " + \"&producto=\" + idtipodecuenta ";
		//$this->mURL		.= " + \"&dependencia=\" + idempresa ";		
	}
	function addProductoDeCuentaDeCaptacion(){
		$xSel			= new cHSelect();
		$xOb			= $xSel->getListaDeProductosDeCaptacion();
		$xOb->addEspOption(SYS_TODAS);
		$xOb->setOptionSelect(SYS_TODAS);
		$this->OFRM()->addHElem( $xOb->get(true)  );
		$this->mJsVars	.= "var idproductocaptacion	= $('#idproductocaptacion').val();\r\n";
		$this->mURL		.= " + \"&subproducto=\" + idproductocaptacion ";
		//$this->mURL		.= " + \"&dependencia=\" + idempresa ";
	}	
	function get(){
		$xBtn			= new cHButton();
		$xFRM			= $this->mOFRM;
		
		$xFRM->setTitle( $this->mTitle );
		if($this->mTipo == iDE_CAPTACION){
			$this->addTipoDeCuentaDeCaptacion();
			$this->addProductoDeCuentaDeCaptacion();
		}
		if($this->mConFecha == true){
			$xFRM->addHElem( $this->addFechaInicial() );
			$xFRM->addHElem( $this->addFechaFinal() );
		}
		if($this->mTipo == iDE_USUARIO){ $this->addOficialDeCredito();	}
		if($this->mTipo != iDE_BANCOS AND ($this->mTipo != iDE_AML)){ $this->addEmpresasConConvenio(); }
		if($this->mTipo == iDE_AML){
			$this->mForceRecibos = false;
		} else {
			if(MULTISUCURSAL == true){
				$this->addSucursales(true);
			}
		}
		if($this->mTipo == iDE_CREDITO OR $this->mForceCredito == true){
			$this->addCreditosProductos();
			//frecuencia
			$this->addCreditosPeriocidadDePago();
			//estatus
			$this->addCreditosEstados();
		}
		if($this->mTipo == iDE_CAPTACION){
			//TODO: Considerar
		}//&base
		if($this->mTipo == iDE_BANCOS){
			$this->addListadDeCuentasBancarias();
			$this->addTiposDeOperacionesBancarias();
		}
		if($this->mTipo == iDE_RECIBO OR $this->mForceRecibos == true){
			$this->addTipoDePago();
			$this->addTiposDeRecibos();
		}
		if(($this->mTipo == iDE_RECIBO) OR ($this->mForceRecibos == true) OR ($this->mForceCajeros == true)){
			$this->addUsuarios();
		}
		if( $this->mTipo == iDE_OPERACION OR $this->mForceOperaciones == true){ $this->addTipoDeOperacion();	}
		$xFRM->addHElem( $this->mStruct );
		
		
		$xFRM->addHElem( $this->addTiposDeSalida() );
		$xFRM->addHTML($this->mHtml);
		if($this->mFooterBar != ""){
			$xFRM->addFooterBar( $this->mFooterBar );
		}
		//Button
		return $xFRM->get();
	}
	function addOficialDeCredito(){
		$sqlSc		= "SELECT id, nombre_completo FROM `oficiales`";
		$xS 		= new cSelect("idoficial", "idoficial", $sqlSc);
		$xS->setEsSql();
		$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idoficial	= $('#idoficial').val();\r\n";
		$this->mURL		.= " + \"&f700=\" + idoficial ";
		$this->mURL		.= " + \"&oficial=\" + idoficial ";
		
		$this->mStruct	.=  $xS->get("TR.Oficial de Credito", true);
	}
	function addCreditosProductos(){
		$xConv	= new cCreditos_tipoconvenio();
		$xSel	= new cSelect("idtipo_de_convenio", "idtipo_de_convenio", $xConv->get() );
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$v		= $xSel->get("TR.Producto de Credito", true) ;
		
		$this->mURL		.= " + \"&producto=\" + idproducto "; //"&producto=" + producto
		$this->mURL		.= " + \"&convenio=\" + idproducto ";
		$this->mURL		.= " + \"&f5=\" + idproducto ";
		
		$this->mJsVars	.= "var idproducto	= $('#idtipo_de_convenio').val();\r\n";
		
		$this->mStruct	.= $v ;
	}
	function addCreditosPeriocidadDePago(){
		$xTb	= new cCreditos_periocidadpagos();
		$xSel	= new cSelect("idperiocidad", "idperiocidad", $xTb->get() );
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idperiocidad	= $('#idperiocidad').val();\r\n";
		$this->mURL		.= " + \"&f1=\" + idperiocidad ";
		$this->mURL		.= " + \"&periocidad=\" + idperiocidad ";
		$this->mURL		.= " + \"&frecuencia=\" + idperiocidad ";
		
		$v		= $xSel->get("TR.Periocidad de Pago", true);
		$this->mStruct	.= $v ;
	}
	function addTipoDePago(){
		$xTipo	= new cHCobros();
		$xTipo->setOptions("<option value='" . SYS_TODAS . "'>TODAS</option>");
		
		$this->mJsVars	.= "var idtipodepago	= $('#idtipo_pago').val();\r\n";
		
		$this->mURL		.= " + \"&tipopago=\" + idtipodepago ";
		$this->mURL		.= " + \"&tipodepago=\" + idtipodepago ";
		$this->mURL		.= " + \"&pago=\" + idtipodepago ";
		
		$v		= $xTipo->get(false, "", "", false);
		$this->mStruct	.= $v;
		return $v;
	}
	function addjsVars($id	= "", $geteq = ""){
		$this->mJsVars	.= "var $id	= $('#$id').val();\r\n";
		//TODO: Agregar Cuentas
		$this->mURL		.= " + \"&$geteq=\" + $id ";		
	}
	function addSucursales($close = false){
		$sqlSc		= "SELECT codigo_sucursal, nombre_sucursal FROM general_sucursales";
		$xS 		= new cSelect("idsucursal", "idsucursal", $sqlSc);
		$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(SYS_TODAS);
		$xS->SetEsSql();
		if($close == false){
			$this->mSucursales	= $xS->show();
		} else {
			$this->mSucursales	= $xS->get("TR.Sucursal", true);
		}
		$this->mJsVars	.= "var idsucursal	= $('#idsucursal').val();\r\n";
		$this->mURL		.= " + \"&sucursal=\" + idsucursal ";
		$this->mURL		.= " + \"&s=\" + idsucursal ";
		
		$this->mStruct	.=	$this->mSucursales;
		return $this->mSucursales;
	}
	function addFechaInicial(){
		/*$xF						= new cFecha(0);
		$this->mFechaInicial	= $xF->show(true, TIPO_FECHA_OPERATIVA);*/
		$xDate			= new cHDate(0, false, TIPO_FECHA_OPERATIVA);
		$this->mJsVars	.= "var fechaInicial	= $('#idfecha-0').val();\r\n";
		
		$this->mURL		.= " + \"&on=\" + fechaInicial ";
		$this->mURL		.= " + \"&fechainicial=\" + fechaInicial ";
		$this->mURL		.= " + \"&fechaMX=\" + fechaInicial ";
		
		return $xDate->get("TR.Fecha_Inicial");
	}
	function addFechaFinal($titulo = ""){
		/*$xF						= new cFecha(1);
		$this->mFechaInicial	= $xF->show(true, TIPO_FECHA_OPERATIVA);*/
		$titulo			= ($titulo == "") ? "TR.Fecha_Final" : $titulo;
		$xDate			= new cHDate(1, false, TIPO_FECHA_OPERATIVA);
		$this->mJsVars	.= "var fechaFinal	= $('#idfecha-1').val();\r\n";
		
		$this->mURL		.= " + \"&off=\" + fechaFinal ";
		$this->mURL		.= " + \"&fechafinal=\" + fechaFinal ";
		
		return $xDate->get($titulo);
	}
	function addTiposDeSalida(){
		$this->mTiposSalida = "<div class='tx4'><label>Exportar Reporte Como</label>
			<select name=\"idtipodesalida\" id=\"idtipodesalida\">
				<option value=\"" . SYS_DEFAULT . "\" selected>Por Defecto(xml)</option>
				<option value=\"csv\">Archivo Delimitado por comas (cvs)</option>
				<option value=\"tsv\">Archivo Delimitado por Tabulaciones(tvs)</option>
				<option value=\"txt\">Archivo de Texto(txt)</option>
				<option value=\"html\">Pagina Web(www)</option>
				<option value=\"xls\">Excel</option>
			</select></div> ";
		
		$this->mJsVars	.= "var idtiposalida	= $('#idtipodesalida').val();\r\n";
		$this->mURL		.= " + \"&out=\" + idtiposalida ";
		
		return $this->mTiposSalida;
	}
	function addListadDeCuentasBancarias(){
		$xConv	= new cBancos_cuentas();
		$xSel	= new cSelect("idcuentabancaria", "idcuentabancaria", $xConv->get() );
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idcuentabancaria	= $('#idcuentabancaria').val();\r\n";
		//TODO: Agregar Cuentas
		$this->mURL		.= " + \"&cuenta=\" + idcuentabancaria ";
		$this->mURL		.= " + \"&cuentabancaria=\" + idcuentabancaria ";
		$this->mStruct	.= $xSel->get("TR.Numero de Cuenta", true);		
	}
	function addTiposDeOperacionesBancarias(){
		$arrOpts	= array(
				"cheque" => "CHEQUES",
				"deposito" => "DEPOSITOS",
				"comision" => "COMISIONES",
				"retiro" => "RETIROS",
				"traspaso" => "TRASPASOS",
				SYS_TODAS => SYS_TODAS
		);
		$xHOp	= new cHSelect("idtipooperacion", $arrOpts);
		$this->mJsVars	.= "var idoperacionbancaria	= $('#idtipooperacion').val();\r\n";
		//TODO: Agregar parametros de operacion bancaria
		$this->mURL		.= " + \"&operacion=\" + idoperacionbancaria ";
		
		$this->mStruct	.= $xHOp->get("", "TIPO DE OPERACION", SYS_TODAS);
	}
	function addEmpresasConConvenio(){
		$xEmp	= new cSocios_aeconomica_dependencias();
		$xSel	= new cSelect("idempresa", "idempresa", $xEmp->get() );
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$v		= $xSel->get("TR.Empresa", true);
		$this->mJsVars	.= "var idempresa	= $('#idempresa').val();\r\n";
		
		$this->mURL		.= " + \"&empresa=\" + idempresa ";
		$this->mURL		.= " + \"&dependencia=\" + idempresa ";
		
		$this->mStruct	.= $v;
	}
	function addCreditosEstados(){
		$xTb	= new cCreditos_estatus();
		$xSel	= new cSelect("idestado", "idestado", $xTb->get() );
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$v		= $xSel->get("TR.Estado del Credito", true);
		$this->mJsVars	.= "var idestado	= $('#idestado').val();\r\n";
		
		$this->mURL		.= " + \"&f2=\" + idestado ";
		$this->mURL		.= " + \"&estado=\" + idestado ";
		
		$this->mStruct	.= $v;
	}
	function addUsuarios(){
		//$xFRM->addHElem(  );
		$sqlSc		= "SELECT idusuarios, nombrecompleto FROM usuarios";
		$xS 		= new cSelect("idusuario", "idusuario", $sqlSc);
		$xS->setEsSql();
		$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idusuario	= $('#idusuario').val();\r\n";
		
		$this->mURL		.= " + \"&cajero=\" + idusuario ";
		$this->mURL		.= " + \"&f3=\" + idusuario ";
		
		$v			= $xS->get("TR.Usuario", true);
		$this->mStruct	.= $v;
	}
	function addTipoDeOperacion($base = false, $base2 = false){
		$base	= setNoMenorQueCero($base);
		if($base > 0){
			$xHSel	= new cHSelect();
			$xSel	= $xHSel->getListaDeOperacionesPorBase($base, "idtipo_de_operacion", $base2);
		} else {
			$xTb	= new cOperaciones_tipos();
			$xSel	= new cSelect("idtipo_de_operacion", "idtipo_de_operacion", $xTb->get() );			
		}
		
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idtipo_de_operacion	= $('#idtipo_de_operacion').val();\r\n";
		$this->mURL		.= " + \"&operacion=\" + idtipo_de_operacion ";
		
		$v		=  $xSel->get("TR.Tipo de Operacion", true);
		//TODO. Agregar indentificadores de tipo de operacion
		$this->mStruct	.= $v;
	}
	function getSelectReportes(){ return $this->mSelectReports; }
	function addHTML($html = ""){ $this->mHtml	.= $html; }
	function getJs($close	= true){
		$this->mJsVars	.= ""; 
		$this->mJsVars	.= "var g 		= new Gen();\r\n"; 
		$this->mJsVars	.= "var murl 	= idreporte + \"mx=true\" + " . $this->mURL . ";\r\n";
		if(MODO_DEBUG == true){
			$this->mJsVars	.= "console.log(murl);\r\n";
		}
		$this->mJsVars	.= "g.w({ url : murl }); \r\n}";
		return ($close == true) ? "<script>\r\n" . $this->mJsVars . "\r\n</script>" : $this->mJsVars;
	}
	function addCheckBox($title = "", $parametro = ""){
		$xChk	= new cHCheckBox();
		$this->mJsVars	.= "var id$parametro	= $('#id$parametro').prop('checked');\r\n";
		
		$this->mURL		.= " + \"&$parametro=\" + id$parametro ";
		
		$this->mStruct	.= $xChk->get($title, "id$parametro");		
	}
	function addTiposDeRecibos(){
		$xSel	=  new cHSelect();
		$this->mStruct	.= $xSel->getListaDeTiposDeRecibos()->get(true);
		$this->mJsVars	.= "var idtipoderecibo	= $('#idtipoderecibo').val();\r\n";
		$this->mURL		.= " + \"&tiporecibo=\" + idtipoderecibo ";
	}
}

class cHUl {
	private $mLineas	= "";
	private $mObj		= null;
	public $mId			= "";
	function __construct($id = ""){
		$this->mId		= $id;
	}
	
	function li($str) {
		if($this->mObj == null){ $this->mObj	= new cHLi($this);}
		$this->mObj->add($str);
		return $this->mObj;
	}
	function getO(){
		if($this->mObj == null){ $this->mObj	= new cHLi($this);}
		return $this->mObj;		
	}
	function get(){
		if($this->mObj == null){ $this->mObj	= new cHLi($this);}
		return $this->mObj->end();
	}
}
class cHLi{
	private $mLineas	= "";
	private $mParent	= null;
	private $mCls		= "rounded-list"; 
	private $mT			= "ol";
	function __construct($parent){
		$this->mParent	= $parent;
	}
	function add($str, $closeTag = "a"){
		$init	= ($closeTag == "") ? "" : "<a>";
		$end	= ($closeTag == "") ? "" : "</a>";
		$this->mLineas .= "<li>$init$str$end</li>";
	}
	function end(){
		$id		=($this->mParent->mId == "") ? "" : " id=\"" . $this->mParent->mId . "\""; 
		$str	= "<" . $this->mT . " class='" . $this->mCls . "' $id>" . $this->mLineas . "</" . $this->mT . ">";
		$this->mLineas	= "";
		return $str;
	}
	function setT($cls){ $this->mT = $cls; }
	function setClass($cls){ $this->mCls = $cls;}
	function li($str){
		$this->add($str);
		return $this;
	}
}
class cHCheckBox {
	protected $mDivClass		= "tx4";
	function __construct(){
		
	}
	function get($label = "", $id = "idcoolcheck"){
		$lng	= new cLang();
		//setLog($label);
		$label	= ($label == "") ? "" : $lng->getT($label);
		$cls	= ($this->mDivClass == "") ? : "class=\"" . $this->mDivClass . "\" ";
		$s		= "<div $cls><table class='chk'>
					<tr>
					<td style='width:75%;border:none;'><label>$label</label></td>
					<td style='width:25%;border:none;'><div class=\"coolCheck\"><input type=\"checkbox\" id=\"$id\" name=\"$id\" /><label for=\"$id\"></label></div></td>
					</tr>
					</table></div>";
		return $s;
	}
	function setDivClass($class){ $this->mDivClass = $class; }
}
class cReportes {
	private $mBody		= "";
	private $mHeader	= "";
	private $mFooter	= "";
	private $mTitulo	= "";
	private $mOut		= "";
	private $mSenders	= array(); 
	private $mMessages	= "";
	private $mBodyMail	= "";
	private $mResponse	= false; 
	private $mFile		= ""; 
	private $mSQL		= "";
	private $mJS		= "";
	private	$mCSSList	= array(); 
	private $mIncluirH3	= false;
	protected $mFooterBar	= "";
	
	function __construct($titulo = ""){
		$xL				= new cLang();
		$this->mTitulo	= $xL->getT( $titulo );
		$this->mOut		= OUT_HTML;
	}
	function getTitle(){ return $this->mTitulo; }
	function setTitle($title, $incluir = false){ $this->mTitulo = $title; $this->mIncluirH3 = $incluir;}
	function getHInicial($titulo, $FechaInicial = "", $FechaFinal = "", $nombreusuario = ""){
		return $this->getEncabezado($titulo, $FechaInicial, $FechaFinal, $nombreusuario);
	}
	function getEncabezado($titulo = "", $FechaInicial = "", $FechaFinal = "", $usuario = ""){
		$xF	= new cFecha();
		//$FechaInicial	= $xF->getFechaCorta($FechaInicial);
		//$FechaFinal		= $xF->getFechaCorta($FechaFinal);
		$usuario		= ($usuario == "") ? elusuario( getUsuarioActual() ) : $usuario;
		$titulo			= ($titulo == "") ? $this->mTitulo : $titulo;
		$fi				= ($FechaInicial == "") ? "" : "<td>Fecha Inicial:</td><td>" . $xF->getFechaCorta($FechaInicial) . "</td>";
		$ff				= ($FechaFinal == "") ? "" : "<td>Fecha Final:</td><td>" . $xF->getFechaCorta($FechaFinal) . "</td>";
		$html	= "<table>
		<thead>
			<tr>
				<th colspan=\"4\" class=\"title\">$titulo</th>
			</tr>
			<!-- DATOS GENERALES DEL REPORTE  -->
			<tr>
				<td width=\"20%\">Preparado por:</td>
				<td width=\"30%\">$usuario</td>
				<td width=\"20%\">Fecha de Elaboracion:</td>
				<td width=\"30%\">" . $xF->getFechaCorta(fechasys()) . "</td>
			</tr>
			<tr>
				$fi
				$ff
			</tr>
		</thead>
		</table>";
		return $html;
	}
	function getPie(){ return getRawFooter();	}
	function setBodyMail($txt){ $this->mBodyMail	.= $txt; }
	function addContent($html){ $this->mBody	.= $html;	}
	function addHeaderCNT($txt = ""){ $this->mHeader	.= $txt; }
	function addCSSFiles($css){ $this->mCSSList[] = $css; }
	function setOut($out = OUT_HTML){
		if($out == SYS_DEFAULT ){ $out = OUT_HTML; }
		
		$xHP		= new cHPage("", HP_REPORT);
		$this->mOut	= $out;
		switch ($out){
			case OUT_EXCEL:
				//NADA
				break;
			case OUT_TXT:
				//NADA
				break;
			default:
				$xHP->setTitle($this->mTitulo);
				$xHP->setDevice($out);
				//setLog($out);
				foreach ($this->mCSSList as $key => $file){
					$xHP->addCSS($file);
				}
				$this->mHeader	= $xHP->getHeader() . $this->mHeader;
				//$this->mHeader	.= "<style>.logo{ margin-left: .5em; max-height: 5em; max-width: 5em;	margin-top: 0 !important; border-color: #808080; z-index: 100000 !important;}</style>";
				//$this->mHeader	.= $xHP->setBodyinit("javascript:window.print();");
				$this->mHeader	.= "<body>";
				$this->mFooter	.= "</body></html>";
				
				break;
		}
	}
	function setResponse($response = true){ $this->mResponse = $response;}
	function setSenders($arrSend){		$this->mSenders	= $arrSend;	}
	function setFile($file){ $this->mFile	= $file; }
	function setSQL($sql){ $this->mSQL = $sql; }
	function setToPrint(){ $this->mJS .= "xRpt.print();"; }
	function setToPagination($init = 0){ $this->mJS .= "xRpt.setPagePagination($init);"; }
	function render($includeHeaders = false){
		$xOH		= new cHObject();
		$cnt		= "";
		$toMail		= (count($this->mSenders) >= 1) ? true : false;
		$body		= "";
		
		if($includeHeaders == true){
			$this->mHeader	.= getRawHeader(false, $this->mOut);
			$this->mFooter	= getRawFooter(false, $this->mOut) . $this->mFooter;
		}
		if($this->mIncluirH3 == true){
			$this->mHeader = $this->mHeader . "<h3 class='title'>" . $this->mTitulo . "</h3>";
		}
		switch($this->mOut){
			case OUT_EXCEL:
				if($this->mSQL != ""){
					$xls	= new cHExcel();
					$html	= $this->mHeader . $this->mBody . $this->mFooter;
					$xls->addContent($html);
					//$cnt 	= $xls->convertTable($this->mSQL, $this->mTitulo, true);
					$cnt	= $xls->render();					
				}				
				break;
			case OUT_RXML:
				$arrPar		= array( "titulo" => $this->mTitulo	);
				$output		= SYS_DEFAULT;
				$oRpt 		= new PHPReportMaker();
				$oRpt->setParameters($arrPar);
				$oRpt->setDatabase(MY_DB_IN);
				$oRpt->setUser(RPT_USR_DB);
				$oRpt->setPassword(RPT_PWD_DB);
				$oRpt->setSQL($this->mSQL);
				$oRpt->setXML("../repository/". $this->mFile . ".xml");
				$oOut 		= $oRpt->createOutputPlugin("html");
				//$oOut->setClean(false);
				$oRpt->setOutputPlugin($oOut);
				//echo  $oRpt->run(true);exit;
				if($toMail == true){
					$html	= $oRpt->run(true);
					
					$title	= $xOH->getTitulize($this->mTitulo);
					$body	= ($this->mBodyMail == "") ? $title : $this->mBodyMail;
					
					$dompdf = new DOMPDF();
					$dompdf->load_html($html);
					$dompdf->set_paper("letter", "portrait" );
					$dompdf->render();
					$this->mFile	= PATH_TMP . "" . $title . ".pdf";
					$output = $dompdf->output();
					file_put_contents($this->mFile, $output);					
				} else {
					$oRpt->run();
				}
				break;
			case OUT_PDF:
				$html	= $this->mHeader . $this->mBody . $this->mFooter;
				$title	= $xOH->getTitulize($this->mTitulo);
				$body	= ($this->mBodyMail == "") ? $title : $this->mBodyMail;
				
				$dompdf = new DOMPDF();
				$dompdf->load_html($html);
				$dompdf->set_paper("letter", "portrait" );
				$dompdf->render();
				if($toMail == true){
					$this->mFile	= PATH_TMP . "" . $title . ".pdf";
					$output = $dompdf->output();
					file_put_contents($this->mFile, $output);
				} else {
					$this->mFile	= $title . ".pdf";
					# Enviamos el fichero PDF al navegador.
					$dompdf ->stream($this->mFile);
				}			
				break;

			default:
				
				
				$cnt	= $this->mHeader . $this->mBody . $this->mFooter;
				
				
				if($toMail == true){
					$html	= $cnt;
					$title	= $xOH->getTitulize($this->mTitulo);
					$dompdf = new DOMPDF();
					$dompdf->load_html($html);
					$dompdf->set_paper("letter", "portrait" );
					$dompdf->render();
					$body			= ($this->mBodyMail == "") ? $title : $this->mBodyMail;
					$this->mFile	= PATH_TMP . "" . $title . ".pdf";
					$output 		= $dompdf->output();
					file_put_contents($this->mFile, $output);
				} else {
					
					if($this->mOut == OUT_DOC){
						$this->mJS	= "";
					}
					$this->mJS	= ($this->mJS == "") ? "" : "<script>var xRpt = new RepGen();" . $this->mJS . "</script>";
					$footerbar	= (trim($this->mFooterBar) == "") ? "" : "<div class='footer-bar warning'>" . $this->mFooterBar . "</div>";
					$cnt		= $this->mHeader . $this->mBody . $this->mJS . $footerbar . $this->mFooter;
				}
				break;
				
		}
		if($toMail == true){
			$xMail		= new cNotificaciones();
			foreach ($this->mSenders as $idmail => $email){
				$this->mMessages	.= $xMail->sendMail($this->mTitulo, $body, $email, array( "path" => $this->mFile ));
			}

			if($this->mResponse == true){
				$rs		= array("message"  => $this->mMessages);
				$cnt	= json_encode($rs);
			}
		}
		return $cnt;
	}

	function addFooterBar($html){		$this->mFooterBar	.= $html;	}
}

class cHDiv {
	protected $mDivClass		= "tx4";
	protected $mHTML			= "";
	protected $mID			= "";
	function __construct($class = "tx4", $id = ""){	$this->mDivClass = $class; $this->mID = $id;	}
	function get($cnt = "", $label = "", $id =""){
		$xL			= new cLang();
		$label		= ($label == "") ? "" : "<label for='$id'>" . $xL->getT($label) . "</label>";
		$cnt		.= $this->mHTML;
		$pid		= ($this->mID == "") ? "" : " id='" . $this->mID . "' ";
		return "<div class='" . $this->mDivClass . "' $pid>$label$cnt</div>";
	}
	function addHElem($html){ $this->mHTML	.= $html; }
	function addHLinea($html){ $this->mHTML	.= "<div class='tx1'>$html</div>"; }
}


/**
 * SAFE chart is a implement from open_flash_chart
 *
 */
class SAFEChart{
	private $mValues	= array();
	private $mValues2	= false;
	private $mLabels	= array();
	private $mTitle		= "";
	private $mColor		= false;
	function __construct(){

	}
	function setValues($values, $values2 = false){
		$this->mValues		= $values;
		$this->mValues2	= $values2;
	}
	/**
	 * Agrega los Titulos de la tabla
	 * @param array $title
	 */
	function setTitle($title){
		$this->mTitle	= $title;
	}
	function setLabels($labels){
		$this->mLabels = $labels;
	}

	function ChartPIE(){

	$iduser	= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];

	$data 	= $this->mValues;
	$label	= $this->mLabels;


	$g = new graph();
		//
		// PIE chart, 60% alpha
		//
		//$g->set_swf_path(vIMG_PATH . "/tmp/");
		$g->pie(60,'#505050','{font-size: 10px; color: #404040;');
		//
		// pass in two arrays, one of data, the other data labels
		//
		$g->pie_values( $data, $label );
		//
		// Colours for each slice, in this case some of the colours
		// will be re-used (3 colurs for 5 slices means the last two
		// slices will have colours colour[0] and colour[1]):
		//
		if ( $this->mColor == false ){
			$lim = sizeof($data);
			$colorInit		= hexdec("d01f3c");
			$this->mColor	= array();

			for ($i = 0; $i < $lim; $i++){

				$colorInit			+= floor($i * rand(-255,255));
				$this->mColor[]		= "#" . dechex($colorInit);
			}
		}
		$g->pie_slice_colours( $this->mColor );

		$g->set_tool_tip( '#val#%' );

		$g->title( $this->mTitle, '{font-size:14px; color: #d01f3c}' );

		$x = $g->render();
		return $this->setWriteFile($x);
	}

	function Chart3DBAR($LimiteMaximo = 100, $titulo ="", $titulo2 = ""){
	$data 	= $this->mValues;
	$label	= $this->mLabels;

		$g = new graph();
		$g->title( $this->mTitle, '{font-size:16px; color: #FFFFFF; margin: 5px; background-color: #505050; padding:5px; padding-left: 20px; padding-right: 20px;}' );

		//$g->set_data( $data_1 );
		//$g->bar_3D( 75, '#D54C78', '2006', 10 );

		//$g->set_data( $data_2 );
		//$g->bar_3D( 75, '#3334AD', '2007', 10 );
		//Crea el Bar Blue
		$bar_blue = new bar_3d( 75, '#3334AD' );
		$bar_blue->key( $titulo, 10);
		$bar_blue->data	= $this->mValues;
		
		$g->data_sets[] = $bar_blue;
		if ( is_array($this->mValues2) ){
			$bar_blue2 = new bar_3d( 75, '#ff0000' );
			$bar_blue2->key( $titulo2, 10);
			$bar_blue2->data	= $this->mValues2;
			$g->data_sets[] = $bar_blue2;
		}

		$g->set_x_axis_3d( 12 );

		$g->x_axis_colour( '#909090', '#ADB5C7' );

		$g->y_axis_colour( '#909090', '#ADB5C7' );

		$g->set_x_labels( $this->mLabels );

		$g->set_y_max( $LimiteMaximo );

		$g->y_label_steps( 5 );
		//$g->set_y_legend( 'Open Flash Chart', 12, '#736AFF' );
		$x = $g->render();

		return $this->setWriteFile($x);
	}
	function setWriteFile($x){
		$iduser			= $_SESSION["log_id"];
		$tmpKey			= md5(date("Ymdhsi") . $iduser. getRndKey());
		//Abre Otro, lo crea si no existe
		$mFILE			= vIMG_PATH . "/tmp/chart-". $tmpKey . ".dat";
		$URIFil			= fopen($mFILE, "a+");
		@chmod($mFILE, 0666);
		@fwrite($URIFil, $x);
		@fclose($URIFil);
		return $mFILE;
	}

}

class cFileLog{
	private $mName				= false;
	private $mPath				= false;
	private $mType				= "txt";
	private $mSucursal			= false;
	private $mUser				= false;
	private $mFecha				= false;
	private $mRootPath			= "";
	private $mMessages			= "";
	private $mRFile				= false;
	private $mCompleteName		= "";
	private $mEstatOpen			= false; 
	function __construct($name = false, $DelIfExist = false){
		$this->mSucursal	= getSucursal();
		$this->mUser		= getUsuarioActual();
		$this->mFecha		= fechasys();
		$this->mRootPath	= PATH_TMP;

		if ($name == false ){
			$name = "log-" . date("Hsi") . "-" . $this->mUser;
			$this->mName	=	$this->mSucursal . "-" . $this->mFecha . "-" . $name;
		} else {
			$this->mName	= $name;
		}
		$this->mCompleteName	= $this->mRootPath . $this->mName . "." . $this->mType;
		$OpenOption		= "a+";
	//Elimina el Archivo
		if ($DelIfExist == true ){
			@unlink($this->mCompleteName);
			$OpenOption	= "w+";
		}
		$this->mRFile = fopen($this->mRootPath . $this->mName . "." . $this->mType, $OpenOption);
		//saveError(10,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "EL Usuario $oficial Utilizo la Utileria $command, Params $id|$id2|$id3");
	}
	function setWrite($text){
		if ($this->mRFile != false){
			@fwrite($this->mRFile, $text);
			$this->mEstatOpen	= true;
		}
	}
	function setMType($type){
		$this->mType	= $type;
	}
	function getName(){
		return $this->mName;
	}
	function setClose(){
		if ($this->mRFile != false){
			@fclose($this->mRFile);
			$this->mEstatOpen	= false;
		}
	}
	function setSendToMail($titulo = "",$email = false){
		$email = ($email == false) ? ADMIN_MAIL : $email;
		$lng			= new cLang();
		$titulo			= $lng->getT($titulo);
		$body			= "TITLE: " . $titulo . "\r\n";
		$body			.= "FILE: " . $this->mName . "\r\n"; //
		$xAviso			= new cNotificaciones();
		$xAviso->sendMail($titulo, $body, $email, array( "path" => $this->mCompleteName ));
		
		return $xAviso->getMessages();			
	}
	function getRead(){
		//echo "<a href=\"../utils/download.php?type=txt&download=$aliasFils&file=$aliasFils\" target=\"_blank\" class='boton'>Descargar Archivo de EVENTOS</a>";
	}
	function setCompress(){

	}
	function getLinkDownload($title, $class = "button"){
		if($this->mEstatOpen	== true){ $this->setClose(); }
		$xBtn	= new cHButton();
		$xLn	= new cLang();
		$title	= $xLn->getT($title);
		$ic		= $xBtn->setIcon( $xBtn->ic()->DESCARGAR );
		$class	= ($class == "") ? "" : " class=\"$class\" ";
		$str = "<a href=\"../utils/download.php?type=txt&download=" . $this->mName . "&file=" . $this->mName . "\" target=\"_blank\" $class>$ic $title</a>";
		return $str;
	}

	function __destruct(){

	}
}
class cHTMLObject{
	private $mTitle 	= "";
	private $mCSS		= array();

	function __construct($title = ""){
		$this->mTitle	= $title;
	}
	function addCSS($fileCss){
		$this->mCSS[]	= $fileCss;
	}
	function setHeaders(){
		$this->setNoCache();											//
	}
	function getHEAD($force_refresh = false){
		if ($force_refresh == true){
			$this->setHeaders();
		}
		/**
		 * Construye Css por el Array mCSS
		 *
		 */
		$Css	= "";
			foreach ($this->mCSS as $key => $value) {
				$Css .= "<link href=\"$value\" rel=\"stylesheet\" type=\"text/css\" >";
			}
		$xhtml = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
					\"http://www.w3.org/TR/html4/loose.dtd\">
					<html>
					<head>
						<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
							<title>" . $this->mTitle . "</title>
						</head>
					$Css
					";
		return $xhtml;
	}
	function getJSPaginador(){

	}
	function getAlert($msg){

		return ( $msg != "" ) ? "<p class='warn'>$msg</p>" : "" ;
	}
	function setInHTML($txt = ""){

			$txt = str_replace("\r\n", "<br />", $txt);

		return $txt;
	}
	function setJsDestino($url){
		return "<script>window.location.href='$url';</script>";
	}
	/**
	 * Obtiene un nombre limpio, para guardar como un archivo
	 * @param $mNombreArchivo
	 */
	function getNombreExportable( $mNombreArchivo ){
		$xh	= new cHObject();
		return $xh->getNombreExportable( $mNombreArchivo );
	}
	function setNoCache(){
	// Don't use cache (required for Opera)
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		header('Expires: 0'); 											// rfc2616 - Section 14.21
		header('Last-Modified: ' . $now);
		header('Cache-Control: no-store, no-cache, must-revalidate'); 	// HTTP/1.1
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');	// HTTP/1.1
		header('Pragma: no-cache');
	}
}

class cHFormatoRecibo {
	
	private $mClave	= false;
	private $mForma	= false;
	function __construct($numero, $formato){
		$this->mClave	= $numero;
		$this->mForma	= $formato;
	}
	function render(){
		$xRec			= new cReciboDeOperacion(false, false, $this->mClave);
		$html			= "";
		$recibo			= $this->mClave;
		
		if($xRec->init() == true){
			$scripts	= "";
			$xCaja		= $xRec->getOCaja();
			//TODO: Resolver ajuste y permisos de ajuste
			if(MODULO_CAJA_ACTIVADO == true AND $xRec->isPagable() == true ){
				$totaloperacion		= $xRec->getTotal();
				$TesMontoPagado		= $xRec->getSaldoEnCaja();
				$forma				= $xRec->getURI_Formato();
				if($TesMontoPagado > 0){
					$xFRM			= new cHForm("frmrecibo");
					$arrTPag		= $xFRM->getAFormsDeTipoPago();
					$frm			= $arrTPag[ $xRec->getTipoDePago() ];
					//si la caja de tesoreria esta abierta, proceder, si no cerrar
					if ( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){
						$scripts	= "<script>alert('El Recibo $recibo no ha sido SALDADO($TesMontoPagado) en su totalidad($totaloperacion),\\n No se puede efectuar operaciones en Caja Cerrada\\nNecesita Autorizar una Sesion de Caja'); document.location = '../404.php?i=7001';</script>";
					} else {
						$scripts	= "<script> TINY.box.show({iframe:'../frmtesoreria/$frm?r=$recibo',boxid:'frameless',width:400,height:540,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){ jsRevalidarRecibo() }})</script>";
					}
					
				} else {
					$scripts	= "<script>window.print();</script>";
				}
				//$html.= $xRec->getMessages(OUT_HTML);
				$xForms			= new cFormato($this->mForma);
				//$xForms->init();
				$xForms->setRecibo($this->mClave);
				$xForms->setProcesarVars();
				$html			.= $xForms->get();
				
				$html			.= "<script>function jsRevalidarRecibo(){document.location = \"$forma\"; }</script>";
				$html			.= $scripts;
			}			
		}
		return $html;
	}
}

class cFormato {
	private $mTxt		= "";
	private $mID		= "";
	private $mArr		= array();
	private $mTitle		= "";
	private $mObj		= null;
	private $mDocumento	= null;
	private $mPersona	= null;
	private $mEsRecibo	= false;
	private $mDataMvto	= array();
	private $mRecibo	= null;
	private $mBasicVars	= array();		//Variables basicas
	private $mFirmasAvales	= "";
	private $mFichasAvales	= "";
	private $mFichaRiesgoAv	= "";
	private $mLAvalesConDir	= "";
	
	function __construct($clave = false){
		$xF				= new cFecha();
		$xUsr			= new cOficial( getUsuarioActual() );
		$this->init($clave);
		$xLoc			= new cLocal();
		
		$this->mArr["variable_fecha_larga_actual"]									= $xF->getFechaLarga();
		$this->mArr["variable_horario_de_trabajo_de_la_entidad"]					= EACP_HORARIO_DE_TRABAJO;
		$this->mArr["variable_nombre_de_presidente_de_vigilancia_de_la_entidad"]	= EACP_PDTE_VIGILANCIA;
		$this->mArr["variable_nombre_de_la_sociedad"]								= EACP_NAME;
		$this->mArr["variable_nombre_de_la_entidad"]								= EACP_NAME;
		$this->mArr["variable_ciudad_de_la_entidad"]								= DEFAULT_NOMBRE_LOCALIDAD;
		$this->mArr["variable_domicilio_de_la_entidad"]								= EACP_DOMICILIO_CORTO;
		$this->mArr["variable_acta_notarial_de_poder_al_representante"]				= EACP_DOCTO_REP_LEGAL;
		
		$this->mArr["variable_domicilio_de_la_entidad"]								= EACP_DOMICILIO_CORTO;
		$this->mArr["variable_entidad_telefono_general"]							= EACP_TELEFONO_PRINCIPAL;
		$this->mArr["variable_entidad_telefono_principal"]							= EACP_TELEFONO_PRINCIPAL;
		
		$this->mArr["variable_documento_de_constitucion_de_la_sociedad"]			= EACP_DOCTO_CONSTITUCION;
		$this->mArr["variable_rfc_de_la_entidad"]									= EACP_RFC;
		
		$this->mArr["variable_nombre_del_representante_legal_de_la_sociedad"]		= EACP_REP_LEGAL;
		$this->mArr["variable_nombre_de_presidente_de_vigilancia_de_la_entidad"]	= EACP_PDTE_VIGILANCIA;
		
		$this->mArr["variable_encabezado_de_reporte"]								= getRawHeader();
		$this->mArr["variable_pie_de_reporte"]										= getRawFooter();
		$this->mArr["variable_pie_de_reporte"]										= getRawFooter();
		
		$this->mArr["variable_hora_actual"]											= date("H:i");
		$this->mArr["variable_marca_de_tiempo"]										= date("Ymd:His");
		$this->mArr["variable_url_publica"]											= SAFE_HOST_URL;
		$this->mArr["variable_lugar_actual"]										= $xLoc->DomicilioLocalidad() . "," . $xLoc->DomicilioEstado();
		$this->mBasicVars															= $this->mArr;
	}
	function init($clave = false){
		if($clave != false){
			$this->mTxt		= contrato($clave, "texto_del_contrato");
			$this->mObj		= new cGeneral_contratos();
			$this->mObj->setData($this->mObj->query()->initByID($clave) );
			$this->mTitle	= $this->mObj->titulo_del_contrato()->v();
		}
	}
	function setUsuario($usuario = false){
		$usuario		= ($usuario == false) ? getUsuarioActual() : $usuario;
		$Usr			= new cSystemUser( $usuario );
		
		$this->mArr["variable_nombre_del_cajero"]									= $Usr->getNombreCompleto();
		$this->mArr["variable_oficial"]												= $Usr->getNombreCompleto();
		$this->mArr["variable_testigo_del_acto"]									= $Usr->getNombreCompleto();
	}
	function setSucursal($sucursal = false){
		$sucursal		= ( $sucursal == false ) ? getSucursal() : $sucursal;
		$xSuc			= new cSucursal($sucursal);
		$xSuc->init();
		$variable_lugar																= $xSuc->getEstado() . ", " . $xSuc->getMunicipio();
		$this->mArr["variable_lugar"] 												= $variable_lugar;
		$this->mArr["variable_lugar_actual"] 										= $variable_lugar;
	}
	function setPersona($clave_de_persona){
		$this->mPersona				= $clave_de_persona;
		$cSoc						= new cSocio($clave_de_persona);
		$fichas_de_respsolidarios	= "";
		$firmas_de_respsolidarios	= "";
		if($cSoc->init() == true){
			$domicilio_del_socio		= $cSoc->getDomicilio();
			$ficha_socio				= $cSoc->getFicha(true, true);
			
			$xODom						= $cSoc->getODomicilio();
			$xOAE						= $cSoc->getOActividadEconomica();
			//Caja local por SQL
			$SQLCL = "SELECT idsocios_cajalocal, descripcion_cajalocal, ultimosocio, region, sucursal FROM socios_cajalocal WHERE idsocios_cajalocal=". $cSoc->getNumeroDeCajaLocal();
			$caja_local 				= mifila($SQLCL, "descripcion_cajalocal");
			
			
			$xFecha						= new cFecha();
					
			$this->mArr["variable_domicilio_del_socio"] 			= $domicilio_del_socio;
			$this->mArr["variable_rfc_del_socio"] 					= $cSoc->getRFC();
			$this->mArr["variable_curp_del_socio"] 					= $cSoc->getCURP();
			$this->mArr["variable_numero_de_socio"] 				= $cSoc->getCodigo();
			$this->mArr["variable_nombre_caja_local"] 				= $caja_local;
			$this->mArr["variable_informacion_del_socio"] 			= $ficha_socio;
			$this->mArr["variable_fecha_de_nacimiento_del_socio"]	= $xFecha->getFechaMediana($cSoc->getFechaDeNacimiento());
			$this->mArr["variable_ciudad_de_nacimiento_del_socio"]	= $cSoc->getLugarDeNacimiento();
						
			if($xODom != null){
				$this->mArr["variable_sin_ciudad_domicilio_del_socio"]	= $xODom->getDireccionBasica();
				$this->mArr["variable_ciudad_del_socio"]				= $xODom->getCiudad();
				$this->mArr["variable_persona_domicilio_municipio"]		= $xODom->getMunicipio();
			}
			if($xOAE != null){
				$this->mArr["variable_actividad_economica_del_socio"]	= $xOAE->getPuesto();
				$this->mArr["variable_estado_de_actividad_economica"]	= $xOAE->getNombreEstado();
				$this->mArr["variable_municipio_de_actividad_economica"]= $xOAE->getNombreMunicipio();
				$this->mArr["variable_nombre_de_la_empresa"]			= $xOAE->getNombreEmpresa();
				$this->mArr["variable_socio_actividad_ciudad"]			= $xOAE->getLocalidad();
				$this->mArr["variable_socio_actividad_telefono"]		= $xOAE->getTelefono();
			}
			$this->mArr["variable_nombre_del_socio"] 				= $cSoc->getNombreCompleto();
			$this->mArr["variable_persona_nombre_completo"]			= $cSoc->getNombreCompleto();
			
			$idestadocivil											= $cSoc->getEstadoCivil();
			$DEstadoCivil											= new cSocios_estadocivil();
			$DEstadoCivil->setData( $DEstadoCivil->query()->initByID($idestadocivil) );
			$nombre_estadocivil										= $DEstadoCivil->descripcion_estadocivil()->v();
			$this->mArr["variable_estado_civil_del_socio"]			= $nombre_estadocivil;
	
			$firmas_de_respsolidarios    							= $cSoc->getCoResponsables("firmas");
		} else {
			if(MODO_DEBUG == true){ setLog($cSoc->getMessages()); } 
		}
				
		$this->mArr["variable_responsable_solidario_en_fichas"]  	= $fichas_de_respsolidarios;
		$this->mArr["variable_firmas_de_obligados_solidarios"] 		= $firmas_de_respsolidarios;

	}
	function setEmpresaPeriodo($empresa, $idnomina = false){
		$xEmp	= new cEmpresas($empresa); $xEmp->init();
		$xPer	= $xEmp->getOPeriodo(false, false, $idnomina);
		$xTPer	= new cPeriocidadDePago($xPer->periocidad()->v()); $xTPer->init();
		$xF		= new cFecha();
		
		$this->mArr["variable_nombre_de_empresa"]		= $xEmp->getNombre();
		$this->mArr["variable_periodo_de_envio"]		= $xTPer->getNombre() . "/" . $xPer->periodo_marcado()->v();
		$this->mArr["variable_periodo_fecha_inicial"]	= $xF->getFechaCorta( $xPer->fecha_inicial()->v() );
		$this->mArr["variable_periodo_fecha_final"]		= $xF->getFechaCorta( $xPer->fecha_final()->v() );
		$this->mArr["variable_periodo_fecha_cobro"]		= $xF->getFechaCorta( $xPer->fecha_de_cobro()->v() );
		$this->mArr["variable_periodo_observaciones"]	= $xPer->observaciones()->v();
		
	}
	function setCambiarTexto($variable, $texto){
		$this->mTxt = str_replace($variable, $texto, $this->mTxt);
		return $this->mTxt;
	}
	function setProcesarVars($arrVars = false){
		$arrVars 	= ($arrVars == false) ? $this->mArr : array_merge($this->mArr, $arrVars);
		foreach ($arrVars as $key => $value) {
			//$this->mTxt = str_replace($key, $value, $this->mTxt);
			$this->setCambiarTexto($key, $value);
		}
		if($this->mEsRecibo == true){
			$IniMvtos		= strpos($this->mTxt, "---");
			$FinMvtos		= strrpos($this->mTxt, "---");
			$txtOps			= str_replace("---", "", substr($this->mTxt, $IniMvtos, ($FinMvtos - $IniMvtos) ) );
			$DEsq			= explode("|", $txtOps);	//esquema
			$this->mTxt		= str_replace("---$txtOps---", "_AREA_DE_MOVIMIENTOS_", $this->mTxt);
			$tbl			= "";
			$xLng			= new cLang();
			$eTit			= array("numero_del_movimiento" => "#", "concepto_del_movimiento" => $xLng->getT("TR.Concepto"),"monto_del_movimiento" => $xLng->getT("TR.Monto"), "destino_del_movimiento" => $xLng->getT("TR.Destino")	);
			$eWidth			= array("numero_del_movimiento" => "10%", "concepto_del_movimiento" => "35%", "monto_del_movimiento" => "17%", "destino_del_movimiento" => "38%");			
			$eCss			= array("monto_del_movimiento" => "mny");
			foreach ($this->mDataMvto as $rows){
				$txt		= "<tr>";
				foreach ($DEsq as $key => $idx){
					$css	= (isset($eCss[$idx])) ? " class=\"". $eCss[$idx] . "\" " : "";
					$txt	.= "<td$css>" . $rows[$idx] . "</td>";
				}
				$txt		.= "</tr>";
				$tbl		.= $txt;
			}
			$head			= "<thead><tr>";
			foreach ($DEsq as $key => $idx){
				//si la clave es destino, usar
				$width		= ( isset( $eWidth[ $idx ] ) ) ? " style=\"width:" . $eWidth[ $idx ] . "\" " : "";
				$title		= ( isset( $eTit[ $idx ] ) ) ?  $eTit[ $idx ] : "";
					
				$head		.= "<th scope='col'$width>$title</th>";			
			}
			$head			.= "</tr></thead>";
			$tbl			= "<table>$head<tbody>$tbl</tbody></table>";
			$this->mTxt		= str_replace("_AREA_DE_MOVIMIENTOS_", $tbl, $this->mTxt);
		}
		
	}
	function setCredito($credito){
		$this->mDocumento		= $credito;
		$xFDE					= new cFecha();
		$xLng					= new cLang();
		$cCred 					= new cCredito($credito); $cCred->init();
		$idsolicitud			= $credito;
		$DCred					= $cCred->getDatosDeCredito();
		$DProd					= $cCred->getOProductoDeCredito();
		$OOParam				= new cProductoDeCreditoOtrosDatosCatalogo();
		$numero_de_socio		= $cCred->getClaveDePersona();
		$this->mPersona			= $numero_de_socio;
		$cSoc					= new cSocio($numero_de_socio); $cSoc->init();
		
		$svar_info_cred 		= "";
		$tblInfCred 			= new cFicha(iDE_CREDITO, $idsolicitud);
		$this->setPersona($numero_de_socio);
		$svar_info_cred 		= $tblInfCred->show(true);
		//Lista de Beneficiados
		$lst_beneficiados 		= "";
		$this->getListadoDeAvales($idsolicitud);
		
		$SQLCBen = "SELECT `socios_relacionestipos`.`descripcion_relacionestipos` AS 'relacion', `socios_relaciones`.`nombres`,	`socios_relaciones`.`apellido_paterno`,	`socios_relaciones`.`apellido_materno`,
			`socios_consanguinidad`.`descripcion_consanguinidad` AS 'consaguinidad'
			FROM `socios_relaciones` `socios_relaciones` INNER JOIN `socios_consanguinidad` `socios_consanguinidad` ON `socios_relaciones`.`consanguinidad` = `socios_consanguinidad`.`idsocios_consanguinidad`
			INNER JOIN `socios_relacionestipos` `socios_relacionestipos` ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.`idsocios_relacionestipos`
			WHERE (`socios_relaciones`.`socio_relacionado` =$numero_de_socio) AND (`socios_relaciones`.`credito_relacionado` =$idsolicitud)	AND	(`socios_relaciones`.`tipo_relacion`=11)";		
		$tblCBen 				= new cTabla($SQLCBen); $lst_beneficiados 		= $tblCBen->Show();

		$firmas_de_avales    				= $this->mFirmasAvales; // $cSoc->getCoResponsables("firmas", "avales", $idsolicitud );
		//Plan de Pago segun SQL
		$splan_pagos			= $cCred->getPlanDePago(OUT_HTML, true, true);
		
		//==================================================================================
		$fichas_de_avales					= $this->mFichasAvales; //$cCred->getAvales_InText();
		$fecha_larga_de_documento			= $xFDE->getFechaLarga($cCred->getFechaDeMinistracion());
		
		$fichas_de_respsolidarios			= "";//TODO: FALTA
		//Otros Datos
		$monto_ministrado 					= $cCred->getMontoAutorizado();
		$tasa_interes_mensual_ordinario		= round( (($cCred->getTasaDeInteres() / 12) * 100), 2);
		$tasa_interes_anual_ordinario		= $cCred->getTasaDeInteres();
		$fecha_de_vencimiento				= $cCred->getFechaDeVencimiento();
		$fecha_de_ministracion				= $cCred->getFechaDeMinistracion();
		$tasa_garantia_liquida				= $DCred["porciento_garantia_liquida"] * 100;
		$monto_garantia_liquida				= $monto_ministrado * $tasa_garantia_liquida;
		$tasa_interes_mensual_moratorio		= round( (($cCred->getTasaDeMora() / 12) * 100), 2);
		$dias_del_credito					= $cCred->getDiasAutorizados();
		$meses_del_credito					= sprintf ("%02d",  ceil($dias_del_credito / 30.416666666666666666666));
		$periocidad							= $cCred->getPeriocidadDePago();
		//Tipo de Credito por SQL
		$SQLTCred 							= "SELECT * FROM creditos_modalidades WHERE idcreditos_modalidades=" . $DCred["tipo_credito"];
		$tipo_de_credito 					= mifila($SQLTCred, "descripcion_modalidades");
		//Datos del Grupo Solidarios por SQL
		$SQLGAsoc 							= "SELECT * FROM socios_grupossolidarios WHERE idsocios_grupossolidarios=" . $DCred["grupo_asociado"];
		$InfoGrupo							= obten_filas($SQLGAsoc);
		$nombre_rep_social					= $InfoGrupo["representante_nombrecompleto"];
		$codigo_rep_social					= $InfoGrupo["representante_numerosocio"];
		$nombre_voc_vigila					= $InfoGrupo["vocalvigilancia_nombrecompleto"];
		$nombre_del_grupo					= $InfoGrupo["nombre_gruposolidario"];
		$domicilio_rep_social				= domicilio($codigo_rep_social);
		$tabla_asociadas					= "";
		$lista_asociadas					= "";
		$tasa_de_cat						= $cCred->getCAT();
		$DPeriocidad						= new cPeriocidadDePago($cCred->getPeriocidadDePago()); $DPeriocidad->init();
		$monto_con_interes					= "";
		$monto_con_interes_letras			= "";
		if( $DCred["grupo_asociado"] != DEFAULT_GRUPO ){
			$SQL_get_grupo 	= "SELECT `socios_general`.`codigo`, CONCAT(`socios_general`.`nombrecompleto`, ' ', `socios_general`.`apellidopaterno`, ' ', `socios_general`.`apellidomaterno`) AS 'nombre_completo'
									FROM `socios_general` `socios_general` WHERE (`socios_general`.`grupo_solidario` =" . $DCred["grupo_asociado"] . ")";
			$rsg 			=  getRecordset($SQL_get_grupo);
				while ($rwt = mysql_fetch_array($rsg)) {
					$lista_asociadas .= ", " . $rwt["nombre_completo"];
				}
		}
		if (  EACP_INCLUDE_INTERES_IN_PAGARE == true ){
			if ( $periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
					$monto_con_interes 		= $cCred->getMontoAutorizado() + ($cCred->getInteresDiariogenerado() * $cCred->getDiasAutorizados());
			} else {
					$sqlInt 				= "SELECT `operaciones_mvtos`.`docto_afectado`, `operaciones_mvtos`.`tipo_operacion`, COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `mvtos`,
					SUM(`operaciones_mvtos`.`afectacion_real` *	`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'
					FROM `operaciones_mvtos` `operaciones_mvtos` INNER JOIN `eacp_config_bases_de_integracion_miembros`	`eacp_config_bases_de_integracion_miembros`
					ON `operaciones_mvtos`.`tipo_operacion` = `eacp_config_bases_de_integracion_miembros`.`miembro` WHERE (`operaciones_mvtos`.`docto_afectado` = $idsolicitud)
					AND (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2601)
					GROUP BY `operaciones_mvtos`.`docto_afectado`, `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
					ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`, `operaciones_mvtos`.`fecha_afectacion`, `operaciones_mvtos`.`socio_afectado`	";
					$xF							= obten_filas($sqlInt);
					$monto_con_interes 			= $xF["monto"];
					
			}
			$monto_con_interes_letras	= convertirletras($monto_con_interes);
			$monto_con_interes 			= getFMoney($monto_con_interes);
		}
							
		$this->mArr["variable_informacion_del_credito"] 					= $cCred->getFicha();
		
		//"variable_lista_de_beneficiados" 		=> $lst_beneficiados,

		$this->mArr["variable_tipo_de_credito"] 							= $tipo_de_credito;
		
		$this->mArr["variable_monto2_ministrado_con_intereses_en_letras"] 	= $monto_con_interes_letras;
		$this->mArr["variable_monto2_ministrado_con_intereses"] 			= $monto_con_interes;
		
		$this->mArr["variable_monto_ministrado"] 							= getFMoney($monto_ministrado);
		$this->mArr["variable_tasa_mensual_de_interes_ordinario"] 			= $tasa_interes_mensual_ordinario;
		$this->mArr["variable_credito_fecha_de_vencimiento"] 				= $xFDE->getFechaMediana($fecha_de_vencimiento);
		$this->mArr["variable_monto_garantia_liquida"] 						= getFMoney($monto_garantia_liquida);
		$this->mArr["variable_tasa_mensual_de_interes_moratorio"] 			= $tasa_interes_mensual_moratorio . "";
		$this->mArr["variable_tasa_de_garantia_liquida"] 					= $tasa_garantia_liquida . "";
		$this->mArr["variable_plan_de_pagos"] 								= $splan_pagos;
		
		$this->mArr["variable_docto_fecha_larga_actual"] 					= $fecha_larga_de_documento;
		
		$this->mArr["variable_nombre_de_la_representante_social"] 			= $nombre_rep_social;
		$this->mArr["variable_listado_de_integrantes"] 						= $lista_asociadas;
		$this->mArr["variable_nombre_de_la_vocal_de_vigilancia"] 			= $nombre_voc_vigila;
		$this->mArr["variable_nombre_del_grupo_solidario"] 					= $nombre_del_grupo;
		$this->mArr["variable_domicilio_de_la_representante_social"] 		= $domicilio_rep_social;
		$this->mArr["variable_meses_de_duracion_del_credito"] 				= $meses_del_credito;
		$this->mArr["variable_en_letras_monto_ministrado"] 					= convertirletras($monto_ministrado);
		$this->mArr["variable_credito_fecha_de_ministracion"] 				= $xFDE->getFechaCorta($fecha_de_ministracion);
		

		$this->mArr["variable_tasa_cat"]									= $tasa_de_cat;

		$this->mArr["variable_credito_periocidad"]							= $DPeriocidad->getNombre();
		$this->mArr["variable_credito_monto_parcialidad_fija"]				= getFMoney($cCred->getMontoDeParcialidad());
		$this->mArr["variable_credito_numero_de_pagos"]						= $cCred->getPagosAutorizados();
		$this->mArr["variable_tasa_anual_de_interes_moratorio"]				= (($cCred->getTasaDeInteres()*2) * 100) . "%";
		$this->mArr["variable_tasa_anual_de_interes_ordinario"]				= ($cCred->getTasaDeInteres() * 100) . "%";
		//sobreescribir datos de la empresa
		$xEmp																= new cEmpresas($cCred->getClaveDeEmpresa()); $xEmp->init();
		$this->mArr["variable_nombre_de_la_empresa"]						= $xEmp->getNombre();
		$this->mArr["variable_nombre_de_empresa"]							= $xEmp->getNombre();
		
		$this->mArr["variable_fecha_de_primer_pago"]						= $xFDE->getFechaMediana( $cCred->getFechaPrimeraParc());
		$this->mArr["variable_avales_en_fichas"] 							= $fichas_de_avales;
		$this->mArr["variable_firmas_de_avales"] 							= $firmas_de_avales;
		$this->mArr["variable_avales_autorizacion_central_riesgo"] 			= $this->mFichaRiesgoAv;
		
		$this->mArr["variable_fecha_ultimo_abono"]							= $xFDE->getFechaLarga($cCred->getFechaUltimaParc());
		$this->mArr["variable_fecha_de_primer_abono"]						= $xFDE->getFechaMediana( $cCred->getFechaPrimeraParc());
		
		//$this->mArr["variable_fecha_de_primer_abono"]						=
		$this->mArr["variable_en_letras_tasa_mensual_de_interes_moratorio"]	= convertirletras_porcentaje( $tasa_interes_mensual_moratorio ); 
		$this->mArr["variable_lista_de_avales_con_domicilio"]				= $this->mLAvalesConDir;
		
		/*variable_aval1_nombre_completo variable_aval1_domicilio_completo variable_aval1_domicilio_localidad variable_aval1_domicilio_municipio*/
		//Cargar Avales
		$this->mArr["variable_listado_de_garantias"] 							= $this->getListadoDeGarantias();
		//$this->mArr["variable_modalidad_de_credito"]					= $cCred->getOEstado()
		$this->mArr["variable_estado_de_credito"]						= $cCred->getOEstado()->descripcion_estatus()->v(OUT_TXT);
		//$this->mArr["variable_credito_num_de_pago_actual"]				= $cCred->getPeriodoActual();
		$this->mArr["variable_contrato_id_legal"]						= $DProd->getOtrosParametros($OOParam->CONTRATO_ID_LEGAL);
		$this->mArr["variable_producto_comision_apertura"]				= $DProd->getOtrosParametros($OOParam->TASA_DE_COMISION_AP);
	}
	function setRecibo($recibo){
		$xRec		= new cReciboDeOperacion(false, false, $recibo);
	
		if($xRec->init() == true){
			$this->setPersona($xRec->getCodigoDeSocio());
			$OTipo		= $xRec->getOTipoRecibo();
			$origen		= $OTipo->getOrigen();
			$afectEfvo	= $OTipo->getAfectacionEnEfvo();
			$xCant		= new cCantidad($xRec->getTotal());
			$QL			= new MQL();
			$xF			= new cFecha();
			$describe	= "";
			$xCta		= null;
			$xCred		= null;
			//Bases de Operaciones de Captacion en Inversiones
			$xB3100		= new cBases(3100);
			$DB3100		= $xB3100->getMembers_InArray();
			$xB3200		= new cBases(3200);
			$DB3200		= $xB3200->getMembers_InArray();
			
			switch($origen){
				case RECIBOS_ORIGEN_MIXTO:
					$this->setCredito($xRec->getCodigoDeDocumento());
					break;
				case RECIBOS_ORIGEN_COLOCACION:
					$this->setCredito($xRec->getCodigoDeDocumento());
					break;
				case RECIBOS_ORIGEN_CAPTACION:
					$this->setCuentaDeCaptacion($xRec->getCodigoDeDocumento());
					break;
			}
			$this->mArr["variable_tipo_de_recibo"]				= $OTipo->getNombre();
			$this->mArr["variable_datos_del_pago"]				= $xRec->getDatosDeCobro();
			$this->mArr["variable_numero_de_recibo"]			= $recibo;
			$this->mArr["variable_docto_fecha_larga_actual"] 	= $xF->getFechaLarga( $xRec->getFechaDeRecibo());
			$this->mArr["variable_observacion_del_recibo"] 		= $xRec->getObservaciones();
			$this->mArr["variable_monto_del_recibo_en_letras"]	= $xCant->letras();
			$this->mArr["variable_monto_del_recibo"] 			= $xCant->moneda();
			///$this->mArr["variable_nombre_del_cajero"] 			= $xRec->getOUsuario()->getNombreCompleto();
			$this->mEsRecibo									= true;
			$this->setUsuario($xRec->getCodigoDeUsuario());
			//obtener operaciones
			$this->mRecibo										= $recibo;
			$sqlmvto = "SELECT
			`operaciones_mvtos`.`socio_afectado`        AS `numero_de_socio`,
			`operaciones_mvtos`.`docto_afectado`        AS `numero_de_documento`,
			`operaciones_mvtos`.`recibo_afectado`       AS `numero_de_recibo`,
			`operaciones_mvtos`.`idoperaciones_mvtos`   AS `numero_del_movimiento`,
			`operaciones_tipos`.`descripcion_operacion` AS `concepto_del_movimiento`,
			`operaciones_mvtos`.`afectacion_real`       AS `monto_del_movimiento`,
			`operaciones_mvtos`.`valor_afectacion`      AS `naturaleza_del_movimiento`,
			`operaciones_tipos`.`nombre_corto` 			AS `concepto_nombre_corto`,
			`operaciones_mvtos`.`periodo_socio`        	AS `parcialidad`,
			`operaciones_mvtos`.`detalles` 				AS `observacion_del_mvto`,
			`operaciones_mvtos`.`tipo_operacion`		AS `tipo_de_movimiento`
			FROM
			`operaciones_mvtos` `operaciones_mvtos`	INNER JOIN `operaciones_tipos` `operaciones_tipos` ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.`idoperaciones_tipos`
			WHERE (`operaciones_mvtos`.`recibo_afectado` = $recibo ) ORDER BY `operaciones_mvtos`.`afectacion_real` DESC";
			$rs		= $QL->getDataRecord($sqlmvto);
			//destino_del_movimiento
			foreach ($rs as $row){
				$rwx	= $row;
				$tipo	= $row["tipo_de_movimiento"];
				$docto	= $row["numero_de_documento"];
				$rwx["monto_del_movimiento"]	= getFMoney($row["monto_del_movimiento"] * $row["naturaleza_del_movimiento"] * $afectEfvo);
				$rwx["destino_del_movimiento"]	= "&nbsp;" . $row["numero_de_documento"];
				
				if(in_array($tipo, $DB3100) == true OR in_array($tipo, $DB3200)){
					if($origen == RECIBOS_ORIGEN_MIXTO){
						//TODO: Cargar datos de la cuenta de captacion y mostrar sus caracteristicas
					} else {
						//if($xCta == null){ $xCta = new cCuentaDeCaptacion($docto); $xCta->init(); }
						$rwx["destino_del_movimiento"]	.= "|" . $this->mArr["variable_tipo_de_cuenta"];
					}
				} else {
					$rwx["destino_del_movimiento"]	.= "|" . substr($this->mArr["variable_tipo_de_credito"],0,5);
					$rwx["destino_del_movimiento"]	.= "|" . substr($this->mArr["variable_estado_de_credito"],0,3);
					$rwx["destino_del_movimiento"]	.= "|" . $row["parcialidad"] ."/". $this->mArr["variable_credito_numero_de_pagos"];
				}
				
				$this->mDataMvto[]	= $rwx;
			}
		}
	}
	function setCuentaDeCaptacion($numero_de_cuenta){
		$xCta	= new cCuentaDeCaptacion($numero_de_cuenta);
		$xCta->init();
		$xCant	= new cCantidad($xCta->getSaldoActual());
		$xF		= new cFecha();
		//$this->mArr[]
		$this->mArr["variable_numero_de_cuenta"]		= $numero_de_cuenta;
		$this->mArr["variable_monto_inicial_en_numero"]	= $xCant->moneda();
		$this->mArr["variable_monto_inicial_en_letras"]	= $xCant->letras();
		$this->mArr["variable_numero_de_dias"]			= $xCta->getDiasDeCuenta();
		$this->mArr["variable_nombre_mancomunados"]		= $xCta->getNombreMancomunados();
		$this->mArr["variable_tasa_otorgada"]			= $xCta->getTasaActual();
		$this->mArr["variable_fecha_de_vencimiento"]	= $xCta->getFechaDeVencimiento();
		//$this->mArr["variable_oficial"]					
		$this->mArr["variable_lista_de_beneficiados"]	= "";
		$this->mArr["variable_tipo_de_cuenta"]				= $xCta->getOTipoDeCuenta()->getNombre();
	}
	function setAvalDeCredito($clave_de_aval, $contar = "", $datos = false ){
		/*variable_aval1_nombre_completo variable_aval1_domicilio_completo variable_aval1_domicilio_localidad variable_aval1_domicilio_municipio*/
	}
	function setGarantiaDeCredito($clave_de_garantia, $contar = "1", $datos = false ){
		$xTG	= new cCreditos_tgarantias();
		$xTV	= new cCreditos_tvaluacion();		
		$xCG	= new cCreditos_garantias();
		
		$datos	= ($datos == false) ? $xCG->query()->initByID($clave_de_garantia) : $datos;
		$xTG->setData( $xTG->query()->initByID( $xCG->tipo_garantia()->v() ) );
		$xTV->setData( $xTV->query()->initByID( $xCG->tipo_valuacion()->v() ) );
		$this->mArr["variable_credito_garantiareal" . $contar . "_clave"] 	=  $clave_de_garantia;
		
		$this->mArr["variable_credito_garantiareal" . $contar . "_tipo"] 	=  $xTG->descripcion_tgarantias()->v();
		$this->mArr["variable_credito_garantiareal" . $contar . "_valuacion"] 	=  $xTV->descripcion_tvaluacion()->v();
		$this->mArr["variable_credito_garantiareal" . $contar . "_valor"] 	=  $xCG->monto_valuado()->v();
		
		$this->mArr["variable_credito_garantiareal" . $contar . "_fecharesguardo"] 	=  $xCG->fecha_resguardo()->v();
		$this->mArr["variable_credito_garantiareal" . $contar . "_fecharegistro"] 	=  $xCG->fecha_recibo()->v();
		$this->mArr["variable_credito_garantiareal" . $contar . "_documento"] 		=  $xCG->documento_presentado()->v();
		$this->mArr["variable_credito_garantiareal" . $contar . "_descripcion"] 	=  $xCG->descripcion ->v();
		$this->mArr["variable_credito_garantiareal" . $contar . "_observaciones"] 	=  $xCG->observaciones()->v();
	}
	function getListadoDeGarantias($credito = false){
		$credito		= ($credito == false) ? $this->mDocumento : $credito;
		$xLng			= new cLang();
		//cargar garantias
		$xCG			= new cCreditos_garantias();
		$data			= $xCG->query()->select()->exec("(`creditos_garantias`.`solicitud_garantia` =$credito)");
		$contar			= 1;
		$strgarantias	= "";
		foreach($data as $campos){
			$xCG->setData($campos);
				$xTG->setData( $xTG->query()->initByID( $xCG->tipo_garantia()->v() ) );
				$xTV->setData( $xTV->query()->initByID( $xCG->tipo_valuacion()->v() ) );
			//$this->setGarantiaDeCredito($xCG->idcreditos_garantias()->v(), $contar, $campos );
			$str	= "<tr>";
			$str	.= "<td>" . $xCG->documento_presentado->v() . "<td>";
			$str	.= "<td>" . $xCG->descripcion()->v() . "<td>";
			$str	.= "<td>" . $xCG->observaciones()->v() . "<td>";
			$str	.= "</tr>";
			$contar++;
			$strgarantias	.= "";
		}
		$strgarantias	= "<table><thead><tr>
								<th>" . $xLng->getT("TR.factura") .  "</th>
								<th>" . $xLng->getT("TR.descripcion") .  "</th>
								<th>" . $xLng->getT("TR.observaciones") .  "</th>
								</tr></thead><tbody>$strgarantias</tbody></table>";
		return $strgarantias;
	}
	function getListadoDeAvales($credito){
		//TODO: Terminar
		$mSQL		= new cSQLListas();
		$mql		= new MQL();
		$sql 		= $mSQL->getListadoDeAvales($credito, $this->mPersona);
		$rs			= $mql->getDataRecord($sql);
		//setLog($sql);
		$forma		= 8001;
		$firma		= 8002;
		$friesgo	= 5001;
		
		$cficha		= "";
		$cfirmas	= "";
		$criesgo	= "";
		$clista		= "";
		//$xAval		= new cSocios_relaciones();
		
		foreach ($rs as $rows){
			$persona	= $rows["numero_socio"];
			$idrelacion	= $rows["num"];
			$xSoc		= new cSocio($persona);
			$xRel		= new cPersonasRelaciones($idrelacion, $persona); $xRel->init();

			if( $xSoc->init() == true ){
				$avalDom	= $xSoc->getODomicilio();
				$avalEc		= $xSoc->getOActividadEconomica();
				$vars	= array(
					"aval_nombre_completo" 			=> $xSoc->getNombreCompleto(),
					"aval_domicilio_localidad" 		=> "",
					"aval_direccion_calle_y_numero" => "",
					"aval_direccion_estado" 		=> "",
					"aval_direccion_completa" 		=> "",
					"aval_ocupacion" 				=> "",
					"aval_fecha_de_nacimiento" 		=> $xSoc->getFechaDeNacimiento(),
					"aval_id_fiscal" 				=> $xSoc->getRFC(),
					"aval_lugar_de_nacimiento" 		=> $xSoc->getLugarDeNacimiento(),
					"aval_empresa_de_trabajo" 		=> "",
					"aval_estado_civil" 			=> $xSoc->getEstadoCivil(),
					"aval_tipo_de_relacion" 		=> $xRel->getNombreRelacion(),
					"aval_tipo_de_parentesco" 		=> $xRel->getNombreParentesco(),
					"aval_porcentaje_relacionado"	=> ($xRel->getPorcientorelacionado() * 100)
				);
				if($avalDom != null){
					$vars["aval_direccion_completa"] 		= $xSoc->getDomicilio();
					$vars["aval_domicilio_localidad"] 		= $xSoc->getODomicilio()->getCiudad();
					$vars["aval_direccion_calle_y_numero"]	= $xSoc->getODomicilio()->getCalleConNumero();
					$vars["aval_direccion_estado"] 			= $xSoc->getODomicilio()->getEstado(OUT_TXT);
				}
				if($avalEc != null){
					$vars["aval_ocupacion"] 				= $xSoc->getOActividadEconomica()->getPuesto();
					$vars["aval_empresa_de_trabajo"] 		= $xSoc->getOActividadEconomica()->getNombreEmpresa();
				}
				$texto_ficha 		= contrato($forma, "texto_del_contrato");
				$texto_firma 		= contrato($firma, "texto_del_contrato");
				$texto_aut			= contrato($friesgo, "texto_del_contrato");
				$vars				= array_merge($vars, $this->mBasicVars);
				foreach ($vars as $key => $value) {
					$texto_ficha	= str_replace($key, $value, $texto_ficha);
					$texto_firma	= str_replace($key, $value, $texto_firma);
					$texto_aut		= str_replace($key, $value, $texto_aut);
				}
				$cficha				.= $texto_ficha;
				$cfirmas			.= $texto_firma;
				$criesgo			.= $texto_aut;
				$clista				.= $xSoc->getNombreCompleto(OUT_TXT) . ": " . $xSoc->getDomicilio() . "; ";
				//setLog($texto_ficha);
			}
			//setLog($xSoc->getMessages());	
		}
		$this->mFichasAvales		= $cficha;
		$this->mFirmasAvales		= $cfirmas;
		$this->mFichaRiesgoAv		= $criesgo;
		$this->mLAvalesConDir		= $clista;
	}
	function get(){ 	return $this->mTxt;	}
	function getTitulo(){ return $this->mTitle; }
	function getSelectVariables($id = "", $props = ""){
		$id		= "idvariables";
		$lbl	= "Variables";





		
		$arrV	= array("variables_generales" => array(
					"variable_fecha_larga_actual" => "Fecha Larga Actual",
					"variable_lugar_actual" => "Lugar de Expedicion del Documento",
					"variable_testigo_del_acto" => "Testigo del Acto"),
				
				"variables_de_personas" => array(
					"variable_nombre_del_socio" => "Nombre de la Persona",
					"variable_domicilio_del_socio" => "Domicilio de la Persona",
					"variable_domicilio_estado_del_socio" => "Entidad Federativa  del Domicilio de la Persona",
					"variable_ciudad_del_socio" => "Ciudad de Domicilio de la Persona",

					"variable_rfc_del_socio" => "RFC de la Persona",
					"variable_curp_del_socio" => "CURP de la Persona",
					"variable_numero_de_socio" => "Clave de Persona",
					"variable_nombre_caja_local" => "Nombre de la Caja Local",
					"variable_lista_de_beneficiados" => "Listado de Beneficiarios",
					"variable_firmas_de_obligados_solidarios" => "Listado de FIRMAS de Obligados Solidarios",
					"variable_informacion_del_socio" => "Ficha de Informacion General de la Persona",
					"variable_nombre_de_empresa" => "Nombre de Empresa Asociada",
						
					"variable_ciudad_de_nacimiento_del_socio" => "Persona.- Ciudad de Nacimiento",
					"variable_fecha_de_nacimiento_del_socio" => "Persona.- Fecha de Nacimiento",
					"variable_estado_civil_del_socio" => "Persona.- Estado Civil",
					
					"variable_actividad_economica_del_socio" => "Persona.- Actividad Economica/Puesto",
					"variable_socio_actividad_ciudad" => "Personas.- Actividad Economica.- Ciudad",
					"variable_socio_actividad_telefono" => "Personas.- Actividad Economica.- Telefono",
					"variable_municipio_de_actividad_economica" => "Personas.- Actividad Economica.- Municipio",
					"variable_estado_de_actividad_economica" => "Personas.- Actividad Economica.- Estado"
				),
				"variables_de_creditos" => array(
					"variable_informacion_del_credito" => "Ficha de Informacion del Credito",
					"variable_tipo_de_credito" => "Tipo de Credito",
					"variable_tasa_mensual_de_interes_ordinario" => "Tasa Mensual de Interes Ordinario",
					"variable_credito_fecha_de_vencimiento" => "Fecha de Vencimiento",
					"variable_monto_garantia_liquida" => "Monto de la Garantia Liquida",
						"variable_tasa_anual_de_interes_ordinario" => "Tasa Anualizada de Interes Ordinario",
						"variable_tasa_anual_de_interes_moratorio" => "Tasa Anualizada de Interes Moratorio",
						"variable_tasa_cat" => "Tasa CAT",
					"variable_tasa_mensual_de_interes_moratorio" => "Tasa Mensual de Interes Moratorio",
					"variable_tasa_de_garantia_liquida" => "Tasa Anualizada de Garantia Liquida",
					"variable_en_letras_monto_ministrado" => "Monto Ministrado en Letras",
					"variable_monto_ministrado" => "Monto Ministrado",
					"variable_meses_de_duracion_del_credito" => "Meses de Duracion del Credito",
					"variable_credito_fecha_de_ministracion" => "Fecha fe Ministracion del credito",
					"variable_plan_de_pagos" => "Tabla de Plan de Pagos simplificada",
					"variable_avales_en_fichas" => "Listado de Avales en Fichas",
					"variable_firmas_de_avales" => "Listado de FIRMAS de AVALES",
					"variable_monto2_ministrado_con_intereses" => "Monto del Credito con Intereses",
					"variable_monto2_ministrado_con_intereses_en_letras" => "Monto del Credito con Intereses en letras",
						"variable_credito_monto_parcialidad_fija" => "Monto de la Parcialidad Fija",
						"variable_credito_numero_de_pagos" => "Numero de Pagos",
						"variable_fecha_de_primer_pago" => "Fecha de Primer Pago",
						"variable_credito_periocidad" => "Periocidad de Pagos",
						"variable_credito_numero_de_pagos" => "Numero de Pagos",
						"variable_avales_autorizacion_central_riesgo" => "Autorizacion de consulta en Central de Riesgo para Avales",
						"variable_lista_de_avales_con_domicilio" => "Listado dee avales con Domicilio Completo ",

					"aval_nombre_completo" 			=> "Aval.- Nombre Completo",
					"aval_direccion_completa" 		=> "Aval.- Domicilio Completo",
					"aval_domicilio_localidad" 		=> "Aval.- Localidad de Domicilio",
					"aval_direccion_calle_y_numero" => "Aval.- Calle y Numero",
					"aval_direccion_estado" 		=> "Aval.- Entidad Federativa",
					"aval_ocupacion" 				=> "Aval.- Ocupacion Actual",
					"aval_fecha_de_nacimiento" 		=> "Aval.- Fecha de Nacimiento",
					"aval_id_fiscal" 				=> "Aval.- ID Fiscal",
					"aval_lugar_de_nacimiento" 		=> "Aval.- Lugar de Nacimiento",
					"aval_empresa_de_trabajo" 		=> "Aval.- Nombre de la Empresa Donde Labora",
					"aval_estado_civil" 			=> "Aval.- Estado Civil",
					"aval_tipo_de_relacion" 		=> "Aval.- Tipo de Relacion",
					"aval_tipo_de_parentesco" 		=> "Aval.- Tipo de Parentesco",
					"aval_porcentaje_relacionado"	=> "Aval.- Porcentaje Relacionado",						
					"variable_producto_comision_apertura" => "Producto de Credito.- Comision por Apertura"	
				),

				"variables_de_captacion" => array(
					"variable_numero_de_cuenta" => "Numero de Cuenta",
					"variable_monto_inicial_en_numero" => "Monto Inicial de Apertura en Numero",
					"variable_monto_inicial_en_letras" => "Monto Inicial de Apertura en Letras",
					"variable_numero_de_dias" => "Numero de Dias de Inversion",
					"variable_nombre_mancomunados" => "Listado de Mancomunados",
					"variable_tasa_otorgada" => "Tasa de la Cuenta",
					"variable_fecha_de_vencimiento" => "Inversion.- Fecha de Vencimiento",
					"variable_oficial" => "Oficial de Captacion",
					"variable_lista_de_beneficiados" => "Lista de Beneficiados"
				),
				
				
				"variables_de_grupos" => array(
					"variable_nombre_de_la_representante_social" => "Nombre de la representante",
					"variable_nombre_de_la_vocal_de_vigilancia" => "Nombre de la Vocal de Vigilancia",
					"variable_nombre_del_grupo_solidario" => "Nombre del Grupo Solidario",
					"variable_domicilio_de_la_representante_social" => "Domicilio de la Representante",
					"variable_grupo_nivel_ministracion" => "Nivel de Ministracion del Grupo"),
				
				"variables_de_recibos" => array(
					"variable_fecha_del_recibo" => "Fecha del Recibo",
					"variable_monto_del_recibo" => "Monto del recibo",
					"variable_monto_del_recibo_en_letras" => "Monto en Letras del recibo",
					"variable_nombre_del_banco" => "Nombre del Banco",
					"variable_numero_de_cheque" => "Numero de Cheque",
					"variable_recibo_mvtos_con_socio" => "Ficha de Movimientos con Socios",
					"variable_nombre_del_cajero" => "Nombre del Cajero",
					"variable_tipo_de_pago" => "Tipo de Pago del Recibo",
					"variable_tipo_de_recibo" => "Tipo de Recibo",
					"variable_observacion_del_recibo" => "Observaciones del recibo"),
				
				"variables_de_operaciones" => array("concepto_nombre_corto" => "Nombre Corto"),
				"variables_de_empresas" => array(
						"variable_nombre_de_empresa" => "Nombre de Empresa Asociada",
						"variable_periodo_de_envio" => "Periocidad y Periodo de Envio",
						"variable_periodo_fecha_inicial" => "Fecha Inicial del Envio",
						"variable_periodo_fecha_final" => "Fecha Final del Envio",
						"variable_periodo_fecha_cobro" => "Fecha De Cobro de Nomina",
						"variable_periodo_observaciones" => "Observaciones del Envio"
					),
				"otras_variables" => array(
					"variable_nombre_de_la_sociedad" => "Variable Nombre de la Entidad",
					"variable_rfc_de_la_entidad" => "Variable RFC de la Entidad",
					"variable_domicilio_de_la_entidad" => "Domicilio de la Entidad",
					"variable_marca_de_tiempo" => "Marca de Tiempo",
						"variable_encabezado_de_reporte" => "Encabezado de reporte"
				) );
		$txt		= "";
		foreach($arrV as $clave => $valores){
			$txt	.= "<optgroup label=\"" . strtoupper( str_replace("_", " ", $clave) ) . "\">";
			$sbit	= "";
			foreach($valores as $subitems => $subvalor){
				//<option value="variable_firmas_de_avales">Listado de FIRMAS de AVALES</option>
				$txt	.= "<option value=\"$subitems\">" . strtoupper( str_replace("_", " ", $subvalor) ) . "</option>";
			}
			$txt		.= "</optgroup>";
		}
		return "<div class='tx4'><label for=\"$id\">$lbl</label><select id=\"$id\" name=\"$id\" $props>$txt</select></div>";
	}
	function getSelectDeFormatos(){
		$id		= "idcontrato";
		$sql	= "SELECT idgeneral_contratos, titulo_del_contrato FROM general_contratos ORDER BY titulo_del_contrato ";
		$xSel	= new cSelect($id, $id, $sql);
		$xSel->setEsSql();
		$xSel->setLabel("TR.Lista de Formas");
		return $xSel;		
	}
	function getBaseDeFirmas($arrCNT){
		$itm	= 1;
		$tbl	= "";
		foreach ($arrCNT as $cargo => $nombre){
			$tbl	.= "<td><h4>$cargo</h4><br /><br /><br /><h4>$nombre</h4></td>";
		}
		return "<table><tr>$tbl</tr></table>";
	}
	function setTexto($texto, $titulo = ""){
		$this->mTxt		= $texto;
		$this->mTitle	= $titulo;
	}
}

//======= I(mportaciones
class cFIcons {
	public $DEFAULT 	= "";
	public $EXPORTAR 	= "exportar";
	public $REPORTE 	= "reporte";
	public $REGISTROS 	= "registros";
	public $CONTABLE 	= "contabilidad";
	public $TIPO 		= "perfil";
	public $PERSONA 	= "persona";
	public $ELIMINAR	= "eliminar";
	public $BANCOS		= "bancos";
	public $OK			= "aceptar";
	public $NO			= "cancelar";
	public $RECARGAR	= "refrescar";
	public $CARGAR		= "cargar";
	public $DESCARGAR	= "descargar";
	public $SALDO		= "saldo";
	public $CERRAR		= "cerrar";
	public $COBROS		= "caja";
	public $EJECUTAR	= "ejecutar";
	public $EDITAR		= "editar";
	public $SALUD		= "salud";
	public $CONTROL		= "panel";
	public $AVISO		= "warning";
	public $IMPRIMIR	= "imprimir";
	public $AGREGAR		= "agregar";
	public $GUARDAR		= "guardar";
	public $PREGUNTAR	= "preguntar";
}

class cCatalogoOperacionesDeCaja {
	private $mArrOpsFrm	= array(
			"efectivo" =>  "cobro-efectivo.frm.php",
			"efectivo.egreso" =>  "",
			"cheque.ingreso" => "cobro-cheques-internos.frm.php",
	
			"cheque" => "pago-cheques-internos.frm.php",
	
			"transferencia" => "cobro-transferencia.frm.php",
			"transferencia.egreso" => "pago-transferencia.frm.php",
	
			"foraneo" => "cobro-cheques.frm.php",
			"descuento" => "cobro-cargo-documento.frm.php",
	
			"multiple"	=> "cobro-multiple.frm.php",
			"ninguno" => 99
	);
	
	/*
	 "efectivo" =>  "cobro-efectivo.frm.php",
	"efectivo.egreso" =>  "",
	"cheque.ingreso" => "cobro-cheques-internos.frm.php",
	
	"cheque" => "pago-cheques-internos.frm.php",
	
	"transferencia" => "cobro-transferencia.frm.php",
	"transferencia.egreso" => 9101,
	
	"foraneo" => "cobro-cheques.frm.php",
	"descuento" => "cobro-cargo-documento.frm.php",
	
	"multiple"	=> "cobro-multiple.frm.php",
	"ninguno" => 99*/	
	
	private $mArrOps	= array(
			"efectivo" =>  9100,
			"efectivo.egreso" =>  9100,
			"cheque.ingreso" => 9100,
	
			"cheque" => 9200,
				
			"transferencia" => 9101,
			"transferencia.egreso" => 9201,
	
			"foraneo" => 9100,
			"descuento" => 9201,
				
			"multiple"	=> 99,
			"ninguno" => 99,
			"0" => 99
	);	
	function __construct(){
		
	}
	function getFormatos($tpago = false){
		return $this->mArrOpsFrm;
	}
	function getCatalogoEquivalente($arrEquiv = false){
		return $this->mArrOps;
	}
	/**
	 * Devuelve un Tipo de Operacion a partir de una Tipo de Pago de recibos
	 * @param string $TipoDePago
	 * @return		Tipo de Operacion
	 */
	function getTipoOperacionByTipoPago($TipoDePago = "efectivo"){
		return (isset($this->mArrOps[strtolower($TipoDePago)])) ? $this->mArrOps[strtolower($TipoDePago)] :  99;
	}	
}

?>