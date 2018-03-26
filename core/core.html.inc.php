<?php
use Enhance\Language;
//use Dompdf\Dompdf;
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
	include_once("core.html.ext.inc.php");
	
	@include_once("../libs/spyc.php");
	@include_once("../libs/open-flash-chart.php");
	@include_once("../libs/sql.inc.php");
	
	@include_once("../libs/PHPExcel.php");
	@include_once("../libs/dompdf/autoload.inc.php");
	//@include_once("../libs/dompdf/dompdf_config.inc.php");
	
	@include_once("../libs/gantti/gantti.php");
	//@include_once("../libs/guzzle/Client.php");
	@include_once("../reports/PHPReportMaker.php");
	
	//HTML Page.- Tipo de Objeto HTML
	define("HP_FORM", 1);
	define("HP_REPORT", 2);
	define("HP_RECIBO", 3);
	
	
	define("HP_RPTXML", 4);
	define("HP_GRID", 5);
	define("HP_SERVICE", 6);
	
	define("HP_LABEL_SIZE", 22);
	define("HP_FORM_MIN_SIZE", 65);
	define("HP_REPLACE_ID", "_REPLACE_ID_");
	define("HP_REPLACE_DATA", "_REPLACE_DATA_");

function getRawHeader($xml = false, $out = OUT_DEFAULT, $replaceText = ""){
	//$xCache			= new cCache();
	//$idx			= "raw-header-$out";
	
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
				//if(MODO_DEBUG == true){  setLog("$header"); }
				break;
			default:
				$hd	= "<header><img src=\"$logo\" class=\"logo\" alt=\"logo\" style=\"margin-left: .5em; max-height: 4em; max-width: 4em; margin-top: 0 !important;	border-color: #808080; z-index: 100000 !important;float:left\" />$header</header>";
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
		$footer				= "<!--OTHER_TEXT--><hr /><h5>$domicilio_corto</h5><h5>$email - $telefono</h5>";
		
		if($xml == true){ $out = OUT_RXML; }
		
		$hd			= "";
		switch ($out){
			case OUT_RXML:
				$hd	= "<XHTML>$footer</XHTML>";
				break;
			case OUT_EXCEL:
				$hd	= "<tr><th class=\"xl25\">$domicilio_corto</th></tr><tr><th class=\"xl25\">$email - $telefono</th></tr>"; //<tr></tr>";
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

		$mText	= "";
		switch ( $Format ) {
			case OUT_HTML:
				//if($this->mEncodeHtml)
				$lineas	= explode("\n", $mTexto, 100);
				if(count($lineas) > 1){
					$arrImg	= array(
							"OK\t" => "<img src='../images/forms/valid.png' />",
							"ERROR\t" => "<img src='../images/forms/error.png' />",
							"SUCESS\t" => "<img src='../images/forms/valid.png' />",
							"WARN\t" => "<img src='../images/forms/alert.png' />" 
					);
					$arrCss	= array(
							"OK\t" => " class='success' ",
							"ERROR\t" => " class='error' ",
							"SUCESS\t" => " class='success' ",
							"WARN\t" => " class='warning' "
					);
					foreach($lineas as $linea){
						$css		= "";
						if(trim($linea) == "" ){
							
						} else {
							$linea	= htmlentities($linea);
							foreach ($arrImg as $idx  => $cnt){
								if(strpos($linea, $idx) !== false){
									$linea	= str_replace($idx, $cnt, $linea);
									$css	= $arrCss[$idx];
									break;
								}
							}
							$mText	.= "<li><a$css>$linea</a></li>";
						}
					}
					$mText	= "<ol class=\"rounded-list\">$mText</ol>";
				} else {
					
					$mText	= htmlentities($mTexto, ENT_IGNORE, "UTF-8");
					$mText	= str_replace("", "\r", $mText);
					$mText	= str_replace("<br/>", "\n", $mText);
				}
				unset($lineas); //destruir;
				break;
			case OUT_TXT:
				$mText	= str_replace("<br />", "\r\n", $mTexto);
				try {
					$mText	= utf8_encode($mTexto);
				} catch(Exception $e){
					$mText	= $mTexto;
					$mTexto	= null;
				}
				
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
		$cadena	= str_replace("/", " ", $cadena);
		
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
	private $mTitle		= "";
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
		$titulo		= ($titulo =="") ? $this->mTitle : $titulo;
		$filename 	= ($titulo == "") ? strtolower($_SERVER['SCRIPT_NAME']) : $titulo;
		$arrPurga 	= array("rpt_", "-", "rpt", ".php", "php", ".");
		$filename 	= str_replace($arrPurga, "", 	$filename);
		$filename 	= trim($filename);
		$arrPurga2 	= array(" ", "  ", "__", "___");
		$filename 	= str_replace($arrPurga2, "_", 	$filename);
		$filename 	= $filename . "-" . date("Y_m_d_Hi") . "-" .  $iduser . ".xls";
		
		$strip_tags 	= "h2|fieldset|caption|table|hr|legend";
		$this->mContent = preg_replace("#<\s*\/?(".$strip_tags.")\s*[^>]*?>#im", '', $this->mContent);
		/*$this->mContent	= str_replace("<table>", "", $this->mContent);
		$this->mContent	= str_replace("<hr />", "", $this->mContent);
		$this->mContent	= str_replace("<table x:str border=0 style='border-collapse: collapse'>", "", $this->mContent);
		$this->mContent	= str_replace("<table x:str border=0   style='border-collapse: collapse' >", "", $this->mContent);
		$this->mContent	= str_replace("<table x:str border=0   style='border-collapse:collapse' >", "", $this->mContent);
		$this->mContent	= str_replace("\" >", "\">", $this->mContent);
		
		$this->mContent	= str_replace("</table>", "", $this->mContent);
		$this->mContent	= str_replace("<h2>", "<tr><th class=\"xl25\">", $this->mContent);
		$this->mContent	= str_replace("</h2>", "</th></tr>", $this->mContent);*/
		
		
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
		$excel			= null;
		$this->mContent	= null;
	}
	function __construct($titulo = ""){
		$this->mTitle	= $titulo;
	} 
}
class cHPage {
	private $mHeader;
	private $mKeywords;
	private $mTitle				= "Default Page";
	private $mAuthor			= "";
	private $mDescription		= "";
	private $mCSS				= array();			//Array de CSS
	private $mBody				= "";				//Cuerpo
	private $mHead				= "";				//Encabezado
	private $mArrMeta			= array();			//Arrays de Propiedades de Encabezado
	private $mJSFiles			= array();			//Archivos JS en el encabezado
	private $mNoCache			= false;			//La Pagina puede ser cacheada?
	private $mHSnipt			= array();			//pedazos de codigo en el header
	//private $css			
	private $processed			= false;			//pagina procesada
	protected $mDefaultCSS		= true;
	protected $mTipoDePagina	= 1;
	protected $mNombreArchivo	= "";
	protected $mDevice			= "desktop";
	private $mGeneralCSS		= "/css/general.css";
	private $mPath				= "..";
	private $mAlto				= false;
	private $mAncho				= false;
	private $mEndScript			= "";
	private $mOnEnd				= false;
	private $mStyles			= "";
	private $mOLang				= null;
	
	//private $mTarget			= "desktop"; 
	function __construct($title = "", $TipoDePagina = HP_FORM, $NombreArch = "", $path = ".."){
		$this->mOLang			= new cLang();		
		$keywords				= "page";
		$author					= "Luis Balam";
		$this->mTitle			= $this->mOLang->getT($title);
		$this->mTipoDePagina	= $TipoDePagina;
		$this->mNombreArchivo	= $NombreArch;
		
		getIncludes($path, $TipoDePagina);
		$this->mPath			= $path;
		$token					= SAFE_VERSION . SAFE_REVISION;
		if( $this->mDefaultCSS == true ){
		//====================================
			switch($TipoDePagina){
				
			case HP_FORM:
				//$this->addCSS("$path/css/grid960.css");
				$this->addCSS($path . $this->mGeneralCSS);
				$this->addJsFile("$path/js/lang.js.php");
				//$this->addJsFile("$path/js/general.js?$token");
				
				$this->addJsFile("$path/js/config.js.php");
				$this->addJsFile("$path/js/jquery/jquery.js");
				$this->addJsFile("$path/js/jquery/excanvas.js");
				$this->addJsFile("$path/js/jquery/jquery.cookie.js");
				$this->addJsFile("$path/js/base64.js");
				
				
				$this->addCSS("$path/css/jquery-ui/jquery-ui.css");
				//$this->addCSS("$path/css/Aristo/Aristo.css");
				$this->addJsFile("$path/js/jquery/all-jquery.ui.js");
				
				//$this->addJsFile("$path/js/general.js");
				$this->addJsFile("$path/js/general.js?$token");
				
				$this->addJsFile("$path/js/jquery/jquery.qtip.min.js");
				$this->addJsFile("$path/js/jquery/visualize.jQuery.js");
				$this->addJsFile("$path/js/jquery/jquery.accordion.js");
				
				$this->addJsFile("$path/js/picker.js");
				$this->addJsFile("$path/js/picker.date.js");
				$this->addJsFile("$path/js/picker.time.js");
												
				$this->addJsFile("$path/js/multi-select.min.js");
				//$this->addJsFile("$path/js/jquery/imagesloaded.pkg.min.js");
				
				$this->addCSS("$path/css/formoid/formoid-default.css");
				$this->addCSS("$path/css/jquery.qtip.css");
				
				$this->addCSS("$path/css/picker/default.css");
				$this->addCSS("$path/css/picker/default.date.css");
				$this->addCSS("$path/css/picker/default.time.css");
				$this->addCSS("$path/css/visualize.css");
				$this->addCSS("$path/css/visualize-light.css");
				$this->addCSS("$path/css/font-awesome.min.css");
				//$this->addCSS("$path/css/fontawesome-all.min.css");
				//$this->addCSS("$path/css/iconFont.min.css");
				
				$this->addCSS("$path/css/tinybox.css");
				
				$this->addCSS("$path/css/multi-select.css");
				
				$this->addJsFile("$path/js/tinybox.js");
				$this->addJsFile("$path/js/deprecated.js");
				$this->addJsFile("$path/js/md5.js");
				$this->addJsFile("$path/js/base64.js");
				$this->addJsFile("$path/js/jscrypt/aes.js");
				//$this->addJsFile("https://dl.dropboxusercontent.com/s/9gkr7jkgd7rctta/formas.js?token_hash=AAFQJVtVHodXcn08DLqlUBMA-rxX7Ux62u5hq-9W72oLEA&expiry=1399741290");
				//amaran
				$this->addJsFile("$path/js/jquery/jquery.amaran.min.js");
				$this->addCSS("$path/css/amaran.min.css");
				//$this->addCSS("$path/css/animate.min.css");
				//agregar panel
				//$this->addJsFile("$path/js/jquery/jquery.jpanelmenu.min.js");
				//$this->addCSS("$path/css/font-awesome.min.css");
				$this->addJsFile("$path/js/spin.min.js");
				$this->addJsFile("$path/js/moment.min.js");

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
				
				//$this->addCSS("$path/css/gantti/gantti.css");
			break;
			case HP_REPORT:
				$this->addCSS("$path/css/reporte.css");
				
				$this->addCSS("$path/css/visualize.css");
				$this->addCSS("$path/css/visualize-light.css");
				$this->addJsFile("$path/js/jquery/jquery.js");
				//$this->addJsFile("$path/js/general.js?$token");
				$this->addJsFile("$path/js/general.js");
				$this->addJsFile("$path/js/reports.js");
				$this->addJsFile("$path/js/jquery/excanvas.js");
				$this->addJsFile("$path/js/jquery/visualize.jQuery.js");
				//$this->addCSS("$path/css/gantti/gantti.css");
				
				$this->addCSS("$path/css/chartist.min.css");
				$this->addJsFile("$path/js/chartist.min.js");
				$this->addJsFile("$path/js/chartist-plugin-barlabels.min.js");
				//PDF & DOC
				$this->addJsFile("$path/js/jspdf.debug.js");
				$this->addJsFile("$path/js/html-docx.js");
				//$this->addJsFile("$path/js/contextMenu.min.js");
				///$this->addCSS("$path/css/contextMenu.min.css");
				break;
			case HP_RECIBO:
				$this->addCSS("$path/css/reporte.css");
				$this->addCSS("$path/css/recibo.css.php");
				$this->addJsFile("$path/js/jquery/jquery.js");
				//$this->addJsFile("$path/js/general.js?$token");
				$this->addJsFile("$path/js/general.js");
				$this->addJsFile("$path/js/reports.js");				
				$this->addCSS("$path/css/tinybox.css");
				$this->addJsFile("$path/js/tinybox.js");
				//PDF & DOC
				$this->addJsFile("$path/js/FileSaver.js");
				$this->addJsFile("$path/js/jspdf.debug.js");
				//$this->addJsFile("$path/js/jspdf-plugins/from_html.js");
				$this->addJsFile("$path/js/html-docx.js");
				//$this->addJsFile("$path/js/contextMenu.min.js");
				//$this->addCSS("$path/css/contextMenu.min.css");
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
	function setIncludeJQueryUI(){}
	function addTabulatorSuport(){ 
		 
		$path	= $this->mPath;
		$this->addCSS("$path/css/tabulator.css"); 
		$this->addJsFile("$path/js/tabulator.js");
	}
	function addWizardSuport(){ 
		$path	= $this->mPath;
		
		//$this->addCSS("$path/css/steps/normalize.css");
		$this->addCSS("$path/css/steps/jquery.steps.css");
		$this->addCSS("$path/css/steps/main.css");
		$this->addJsFile("$path/js/jquery.steps.min.js");
	}
	function setIncludeGantt(){
		include_once($this->mPath. "/libs/gantti/gantti.php");
		$this->addCSS($this->mPath . "/css/gantti/gantti.css");
		$this->addCSS($this->mPath . "/css/gantti/screen.css");
	}
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
	function fin($ret = false){
		if($ret === true){
			return  "</body>" . $this->mEndScript . "</html>";
		} else {
			echo "</body>" . $this->mEndScript . "</html>";
		}
	}
	function addCSS($strCSSFile = ""){ $this->mCSS[$strCSSFile] = $strCSSFile; return "<link href=\"$strCSSFile\" rel=\"stylesheet\">";	}
	function addScript(){	}
	function addReload(){
		//TODO: Invalidado en produccion
		//echo "<script>var blurred = false; window.onblur = function() { blurred = true; };window.onfocus = function() { blurred && (location.reload()); };</script>";
	}
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
			switch (EACP_CLAVE_DE_PAIS){
				case "MX":
					$this->addJsFile( $this->mPath . "/js/mexico.js");
					break;
				default:
					$this->addJsFile( $this->mPath . "/js/locale.js");
					break;
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
		//TODO: peronalizar el tag LANG
		switch( $this->mDevice ){
			case "desktop":
				$xhtml = "<!DOCTYPE html>
						<html lang=\"es-mx\">
						    <head>
						        <meta charset=\"utf-8\" />
								<meta name=\"viewport\" content=\"initial-scale=1.0, maximum-scale=1.0, user-scalable=no\">
								<meta name=\"format-detection\" content=\"telephone=no\" />
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
						<html lang=\"es-mx\">
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
		return $this->mOLang->getTrad($palabra, $palabra2); 
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
	function goToPageX($url){
			header("location:$url");
			exit();
	}
	function getTipoDeUsuario(){
		$xUsr	= new cSystemUser();
		$xUsr->init();
		return $xUsr->getNivel();
	}
	function isMobile($isMobile = false){
		
		//$isMobile 	= false;
		//$_SESSION[SYS_CLIENT_MOB]
		if($isMobile === false){
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
		}
		return $isMobile;
	}
	function cors (){
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
	function addChartSupport(){
		$path	= $this->mPath;
		$this->addCSS("$path/css/chartist.min.css");
		$this->addJsFile("$path/js/chartist.min.js");
		$this->addJsFile("$path/js/chartist-plugin-barlabels.min.js");
	}
	function addJTableSupport(){
		$path	= $this->mPath;
		$this->addCSS("$path/css/jtable/lightcolor/orange/jtable.min.css");
		$this->addJsFile("$path/js/jtable/jquery.jtable.js");
		//$this->addJsFile("$path/js/jtable/extensions/jquery.jtable.footer.js");
	}
	function getServerName(){
		$URL			= ($_SERVER["SERVER_NAME"] == "") ? $_SERVER['SERVER_ADDR'] : $_SERVER["SERVER_NAME"];
		return $URL;
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
	protected $mPathTemp	= "../templates/";
	protected $mEByLine		= 2;
	protected $mJQDate		= array(); //snipt de jquery dates
	protected $mTitle		= "";
	protected $mEnc			= "";
	protected $mToolBar		= "";
	protected $mTools		= array();
	private $mAvisos		= ""; 
	protected $mJS			= ""; 
	protected $mID			= "";
	protected $mFooterBar	= "";
	private $mAvisosInit	= array();
	private $mAvisosErrs	= array();
	protected $mConAcc		= false; 
	private $mArrOpsFrm		= array();
	private $mValidacion	= false;	//evento de validacion
	private	$mStrVal		= "";		//cadenas de validacion
	private $icDic			= null;
	private $mLang			= null;
	public $VALIDARCANTIDAD	= "validacion.nozero";
	public $VALIDARVACIO	= "validacion.novacio";
	private $mJSInit		= "";
	private $mAccLock		= true;
	private $mJSOtros		= "";
	private	$mFieldsetClass	= "fieldform";
	private $mJSCode		= "";
	private $mIsWizard		= false;
	private $mNoFieldForm	= false;
	private $mNoFormTag		= false;
	private $mHappyBtn		= "";
	private $mNoKeyForm		= false; //Invalida la funcion de Key Form
	private $mArrReglaVis	= array();
	
	function __construct($name, $action = "", $id = false, $method = "", $class="formoid-default" ){
		$id				= ($id == false) ? "id-$name" : $id;
		$this->mArrProp["method"]	= ($method == "") ? "POST" : $method;
		$this->mArrProp["class"]	= $class;
		$this->mArrProp["id"]		= $id;
		//$this->mArrProp["autocomplete"]		= "off";
		$this->mID					= $id;
		$this->mName				= $name;
		$this->setAction($action);
		
		$this->mEByLine				= 2;

		$xCat	= new cCatalogoOperacionesDeCaja();
		$this->mArrOpsFrm			= $xCat->getFormatos();
	}
	function setFieldsetClass($class = ""){ $this->mFieldsetClass = $class; }
	function addDataTag($data, $value ){ $this->mArrProp["data-$data"]		= $value; }
	function setElementByLine($NumsElm = 2){	$this->mEByLine		= $NumsElm;	}
	function setTitle($title = ""){ $this->mTitle = $title; }
	
	function setAction($action, $conRND = false){
		if($conRND == true){
			$sim	= rand(0,1000);
			if(strpos($action, "?") === false){
				$action	= $action . "?";
			}
			$action	= $action . "&_$sim=$sim" . sha1(getClaveCifradoTemporal() . microtime());
		}
		$this->mAction = $action;
	}
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
			$div	= "<div class='tx1'><div class='$tipo1' $props1>$content1</div>
			<div class='$tipo2' $props2>$content2</div></div>";
		}
		$this->addHElem($div);
	}
	function addDivMedio($content1, $content2 = "", $tipo1 = "tx34", $tipo2 = "tx14", $props =false){
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
			$div	= "<div class='medio' $props1>$content1</div>";
		} else {
			$div	= "<div class='medio'><div class='$tipo1' $props1>$content1</div>
			<div class='$tipo2' $props2>$content2</div></div>";
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
	function addCobroBasico($events = "", $elegir = ""){
		$xHCob		= new cHCobros();
		$xHCob->setEvents($events);
		if($elegir !== ""){
			$xHCob->setSelectOpt($elegir);
		}
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
	function addMonto($valor = 0, $enLetras = false, $id = ""){
		$id	= ($id == "") ? "idmonto" : $id;
		$this->OMoneda2($id, $valor, "TR.Monto", $enLetras);
	}
	function addValor($valor = 0, $enLetras = false, $id = ""){
		$id	= ($id == "") ? "idmonto" : $id;
		$this->OMoneda2($id, $valor, "TR.Valor", $enLetras);
	}
	/**
	 * @deprecated @since 2016.01.01
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
		$eventclose	= ($eventclose == "") ? "if(typeof jsEnd == 'undefined'){var xG=new Gen();xG.close({actualform:'" . $this->mID .  "'});}else{jsEnd();}" : $eventclose;
		if ( $event == "" ){ $event		=  "var xG=new Gen();xG.enviar({form:'" . $this->mID . "'});"; }//$('#" . $this->mID . "').submit()
		$this->OButton("TR.CERRAR", $eventclose, $this->ic()->CERRAR, "btn_salir", "orange");
		$this->OButton($txtGuardar, $event, $this->ic()->GUARDAR, "btn_guardar", "green");
	}
	function addEnviar($txtGuardar = "", $event = "", $eventclose = ""){
		if($txtGuardar == ""){ $txtGuardar	= $this->lang("aceptar"); }
		$eventclose	= ($eventclose == "") ? "if(typeof jsEnd == 'undefined'){var xG=new Gen();xG.close();}else{jsEnd();}" : $eventclose;
		if ( $event == "" ){ $event		=  "$('#" . $this->mID . "').submit()"; }
		$this->OButton("TR.SALIR", $eventclose, $this->ic()->SALIR, "", "orange");
		$this->OButton($txtGuardar, $event, $this->ic()->OK, "", "green");
	}	
	function addGuardar($EventoGuardar = "", $EventoCerrar = "", $TituloSave = "TR.GUARDAR"){
		$this->addSubmit($TituloSave, $EventoGuardar, $EventoCerrar);
	}
	function addActualizar($EventoActualizar = "", $EventoCerrar = ""){
		$this->addSubmit("TR.Actualizar", $EventoActualizar, $EventoCerrar);
	}
	function addJsInit($str = ""){ $this->mJSInit .= $str; }
	function addJsCode($str = ""){ $this->mJSCode .= $str; }
	function addCerrar( $eventclose = "", $timer = 0){
		$Btn		= new cHButton("id-submit-3");
		$eventclose	= ($eventclose == "") ? "if(typeof jsEnd == 'undefined'){var xgen=new Gen();xgen.close();}else{jsEnd();}" : $eventclose;
		$this->OButton("TR.CERRAR", $eventclose, $this->ic()->CERRAR, "btn_cerrar", "orange");
		//$this->addToolbar($Btn->getBasic($this->lang("cerrar"), $eventclose, "cancelar", "cmdcerrar", false));
		if($timer > 0){
			$timer	= $timer * 1000;
			$this->mJSInit	.= "var xSalirAut=function(){var xG=new Gen();xG.close();}; setTimeout(xSalirAut, $timer);";
		}
	}
	function addImprimir($titulo = "",$eventName = "jsImprimirRecibo()"){
		$titulo	= ($titulo == "") ? "TR.IMPRIMIR RECIBO" : $titulo; 
		$this->OButton($titulo, $eventName, $this->ic()->IMPRIMIR, "cmdimprimir", "red");
	}
	function addRefrescar($Evento = ""){
		$xBtn		= new cHButton();
		$Evento		= ($Evento == "") ? "document.location.reload(true);" : $Evento;
		$this->addToolbar($xBtn->getNav("TR.RECARGAR", $Evento, $this->ic()->RECARGAR, "cmdrefresh", "blue" ) );
	}
	function setEnc($enc){ $this->mEnc = $enc;	}
	function getName(){ return $this->mName; }
	function addAvisoInicial($txt = "", $error = false){
		if($error == false){
			$this->mAvisosInit[]	= $txt;
		} else {
			$this->mAvisosErrs[]	= $txt;
		}
	}
	function setValidacion($id, $function, $message = "", $required = false){
		$xLng	= new cLang();
		$aMsg	= array(
				$this->VALIDARCANTIDAD =>  "TR.Monto Invalido",
				$this->VALIDARVACIO	=> "MS.". MSG_DATA_REQUIRED,
				"validacion.codigopostal" => "MS.PERSONA_DOM_CP_VALIDO",
				"validacion.email" => "MS.GENERAL_FALTA_MAIL"
				
		);
		
		$str	= ($this->mStrVal == "") ? "" : ",";
		$str	.= "'#$id':{";
		$str	.= ($required == false) ? "": "required : true,";
		if(isset($aMsg[$function]) AND $message == ""){
			$message = $aMsg[$function];
		}
		$str	.= ($message == "") ? "" : "message: '" . $xLng->getT($message) . "',";
		$str	.= "test:$function";
		$str	.= "}";
		$this->mStrVal .= $str;
		$this->mValidacion	= true;
	}
	function get($encerrar=true){
		$nProps		= "";
		$nJQDates	= "";
		$nTools		= "";
		foreach ($this->mArrProp as $key => $value) {
			$nProps	.= " $key=\"$value\" ";
		}
		foreach ($this->mJQDate as $key => $value){
			$nJQDates	.= "\$( \"#$value\" ).datepicker( { dateFormat: 'yy-mm-dd' } );\r\n";
		}
		$this->addKey();
		$nTools		= "";
		$limTool	= 6;
		$cnt		= 1;
		$st			= "";
		$js			= "";
		$valids		= "";
		$jsMes		= "";
		$wInit		= "";
		$wEnd		= "";
		$wJs		= "";
		$HBtn		= ($this->mHappyBtn == "") ? "" : ",submitButton:'#" . $this->mHappyBtn . "'";
		if($this->mIsWizard == true){
			$wInit		= "<div id=\"w_" . $this->mID . "\">";
			$wEnd		= "</div>";
			$wJs		= "var wizard = $(\"#w_" . $this->mID . "\").steps({headerTag:'h3', bodyTag:'section',transitionEffect:'slide',enableAllSteps: true, enablePagination:false});";
		}
		//============ Avisos
		foreach ($this->mAvisosInit as $id => $key){
			$jsMes	.= "xG.alerta({msg: '', info:'" . $this->getT($key) . "', type : 'warn'});";
		}
		foreach ($this->mAvisosErrs as $id => $key){
			$jsMes	.= "xG.alerta({msg: '',info:'" . $this->getT($key) . "',type:'error'});";
		}
		if($this->mValidacion == true){
			$valids	= "$('#" . $this->mID . "').isHappy({ fields: {" . $this->mStrVal . "}$HBtn });";
		}
		$footerbar	= (trim($this->mFooterBar) == "") ? "" : "<div class='footer-bar pendiente' id='fb_" . $this->mName . "'>" . $this->mFooterBar . "</div>";
		
		//if(MODO_DEBUG == true ){if(count($this->mTools)>0) { $this->OButton("TR.VER", "serializeForm('#" . $this->mID ."');", "ejecutar"); } }
		
		
		foreach($this->mTools as $clave => $valor){
			$nTools	.= $valor;
			$cnt++;
		}
		if($this->mConAcc == true AND $this->mAccLock == false){
			$this->mContentForm	= "<div id=\"acc" . $this->mName . "\">" . $this->mContentForm . "</div>";
			$js		= "$(\"#acc"  . $this->mName .  "\").accordion({'header': 'h3','fillSpace': false,	'active': 0});";
		}
		$js			.= $this->mJSCode;
		$cid		= ceil($cnt/$limTool);
		
		$nTools		= ($nTools == "") ? "" : "<nav class='nv' id='menu-nav'><ul class=\"toolnav\" id='ultool'>$nTools</ul></nav>\r\n";
		if($this->mEnc !== ""){
			$nProps	.= " enctype=\"" . $this->mEnc .  "\" ";
		}
		$title		= ($this->mTitle == "") ? "" : "<legend class='title title-frm'>&vltri;&nbsp;&nbsp;" . $this->mTitle . "&nbsp;&nbsp;&vrtri;</legend>";
		
		$footer		= (trim($this->mContentFoot) == "") ? "" : "<footer>" . $this->mContentFoot . "</footer>";
		$txtheader	= ($nTools == "") ? "" : "<header>$nTools</header>";
		
		$initTag	= "<form name=\"" . $this->mName  . "\" id=\"" . $this->mID .  "\" action=\"" . $this->mAction . "\" data-mod=\"false\" $nProps>";
		$endTag		= "</form>";
		if($this->mNoFormTag == true){
			$initTag	= "";
			$endTag		= "";
		}
		$jsKeyPress		= "$('form input').on('keypress', function(e) { var xG=new Gen(); xG.formF9key(e); if(xG.isKeyEdit({evt:e}) == true){ $(\"#" . $this->mID .  "\").attr(\"data-mod\", \"true\"); }; return e.which !== 13; });\n";
		if($this->mNoKeyForm == true){ $jsKeyPress	= ""; }
		
		$xForm = $initTag . "$txtheader $wInit<div id=\"dlg\"></div>
		" . $this->mContentForm . "		
		" . $this->mHTML . " $wEnd $footer $endTag $footerbar
		" . $this->mJS  . "
		<!-- funciones -->
		<script>
		var xG			= new Gen();
		var wOrigen		= xG.winOrigen();
		$(document).ready(function(){
			$jsKeyPress" . $wJs . "\n" . $valids . "\n" . $nJQDates . "\n$jsMes\n" . $this->mJSInit . "\n" . $this->mJSOtros . "\n});
		$js</script>
		";
		return ($encerrar == false) ? $xForm :  "<fieldset class='" . $this->mFieldsetClass . "'>$title" .$xForm . "</fieldset>";
	}
	function addHTML($html = ""){ $this->mHTML	.= $html; }
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
	function addAvisoRegistroError($msg = ""){
		$xL		= new cLang();
		$txt 	= $xL->getTrad(MSG_ERROR_SAVE);
		$txt 	.= ($msg == "") ? "" : " " . $xL->getT($msg);
		$this->addAviso($txt, "idmsg-error", true, "error");
	}	
	function addAvisoRegistroOK($msg = ""){
		$xL		= new cLang();
		$txt	= $xL->getTrad(MSG_READY_SAVE);
		$txt 	.= ($msg == "") ? "" : "\r\n". $xL->getT($msg);
		
		$this->addAviso($txt, "idmsg-ok", true, "success");	}
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
	function addCreditBasico($credito = false, $persona = false, $addPersona = true, $titulo = "" ){
		if($addPersona == true){
			$this->addPersonaBasico("", false, $persona);
			$this->mJSOtros	.= "if(entero(\$(\"#idsocio\").val()) == 0 && entero(session(ID_PERSONA)) > DEFAULT_SOCIO){ \$(\"#idsocio\").val(entero(session(ID_PERSONA))); }; $(\"#idsocio\").select();$(\"#idsocio\").focus();\r\n";
		}
		$xTxt2	= new cHText();
		$this->addHElem( $xTxt2->getDeCredito("", $credito, $titulo) );
	}
	function addCuentaCaptacionBasico($conpersona = true, $tipo = CAPTACION_TIPO_VISTA, $subtipo = 0, $cuenta = false){
		if($conpersona == true){ 
			$this->addPersonaBasico("", false, false, "");
			$this->mJSOtros	.= "if(entero(\$(\"#idsocio\").val()) == 0 && entero(session(ID_PERSONA)) > DEFAULT_SOCIO){ \$(\"#idsocio\").val(entero(session(ID_PERSONA))); }; $(\"#idsocio\").select();$(\"#idsocio\").focus();\r\n";
		}
		if($tipo == CAPTACION_TIPO_PLAZO){
			$this->addDataTag("role", "inversion");
		}
		$xTxt2	= new cHText();
		$this->addHElem( $xTxt2->getDeCuentaCaptacion("", $cuenta) );
	}
	function addCuentaCaptacionInteres(){ $xTxt2	= new cHText();	$this->addHElem( $xTxt2->getDeCuentaCaptacionInteres("", DEFAULT_CUENTA_CORRIENTE) );	}	
	function addCuentaContable($id = "", $valor="", $addNombre = true){
		$xTxt	= new cHText();	
		$this->addHElem( $xTxt->getDeCuentaContable($id, $valor, $addNombre) );
		$xTxt	= null;
	}
	function addPersonaBasico($id = "", $SinBoton = false, $persona = false, $blurEvents = "", $titulo = ""){
		$xTxt	= new cHText();
		if(setNoMenorQueCero($persona) > DEFAULT_SOCIO){ 
			getPersonaEnSession($persona);			
		}
		$this->addHElem($xTxt->getDeSocio($id, $SinBoton, $persona, $blurEvents, $titulo) );
		if($id == ""){
			$this->setValidacion("idsocio$id", "validacion.persona", "TR.CLAVE_DE_PERSONA ES OBLIGATORIO", true);
		}
		$xTxt	= null;
	}
	function addEmpresaBasico($id = "", $grupo = false){
		$xTxt	= new cHText();
		$this->addHElem($xTxt->getDeGrupo($id, $grupo) );
	}	
	function addGrupoBasico($id = "", $grupo = false){		$xTxt	= new cHText();		$this->addHElem($xTxt->getDeGrupo($id, $grupo) ); 	}
	function addEmpresaComandos($id = ""){
		$this->OButton("TR.Operaciones de Cobro", "var xEmp = new EmpGen();xEmp.getEstadoDeCuenta($id);", $this->ic()->REPORTE);
	}
	function lang($palabra, $palabra2 = ""){ return $this->l()->getTrad($palabra, $palabra2); }
	function l(){ if($this->mLang == null){$this->mLang = new cLang();} return $this->mLang; }
	function addPersonaComandos($clave_de_persona, $evento = SYS_NINGUNO){
		$xBtn	= new cHButton();
		$xEvt	= new cPersonasProceso();
		
		
		
		if( getUsuarioActual(SYS_USER_NIVEL) != USUARIO_TIPO_OFICIAL_AML OR OPERACION_LIBERAR_ACCIONES == true OR MODO_DEBUG == true){
			
			$this->OButton("TR.Agregar Referencias_Domiciliarias", "var xP= new PersGen();xP.setAgregarVivienda($clave_de_persona)", "vivienda", "cmdagregarvivienda" );
			$t1		= $this->l()->getT("TR.AGREGAR")  ." " . ucfirst(PERSONAS_TITULO_PARTES);
			$this->OButton($t1, "var xP= new PersGen();xP.setAgregarRelacionesSN($clave_de_persona)", $this->ic()->RELACIONES, "cmdagregarrelaciones", "persona");
			
			
			$this->OButton("TR.Agregar Actividad Economica", "var xP=new PersGen();xP.setAgregarActividadE($clave_de_persona)", $this->ic()->EMPLEADOR, "cmdagregaractividad");
			$this->OButton("TR.Agregar Relacion_Patrimonial", "var xP=new PersGen();xP.setAgregarPatrimonio($clave_de_persona)", "balance", "cmagregarpatrimonio");
			$this->OButton("TR.Agregar Otras_referencias", "var xP=new PersGen();xP.setAgregarOtrasReferencias($clave_de_persona)", $this->ic()->RELACIONES, "cmdagregarotrasrefs");
			$this->OButton("TR.Agregar Documento", "var xP=new PersGen();xP.setAgregarDocumentos($clave_de_persona)", $this->ic()->ARCHIVOS, "cmdagregardocumento", "white");
			
		}
		if(MODULO_AML_ACTIVADO == true){
			$this->OButton("TR.AGREGAR perfil transaccional", "var xP= new PersGen();xP.setAgregarPerfilTransaccional($clave_de_persona)", "perfil", "cmdagregartransaccional", "white");
		}
		$this->OButton("TR.Checklist", "var xP=new PersGen();xP.setFormaCheck($clave_de_persona);", $this->ic()->OK, "cmdchecklist", "green");
		
		if( getEsModuloMostrado(USUARIO_TIPO_OFICIAL_AML) == true AND $evento !== $xEvt->REGISTRO){
			$this->OButton("TR.Reporte de Alertas", "var xML = new AmlGen(); xML.getReporteDeAlertas($clave_de_persona)", $this->ic()->REPORTE, "idrptalertas");
			$this->OButton("TR.Reporte de Perfil transaccional", "var xML = new AmlGen(); xML.getReporteDePerfilTransaccional($clave_de_persona)", $this->ic()->REPORTE, "idrptperfil");
			$this->OButton("TR.Listado de Transacciones", "var xML = new AmlGen(); xML.getReporteDeTransacciones($clave_de_persona)", $this->ic()->REPORTE2, "idlistatransacciones");
			$this->OButton("TR.Transacciones Por Nucleo", "var xML = new AmlGen(); xML.getReporteDeTransaccionesPorNucleo($clave_de_persona)", $this->ic()->REGISTROS, "cmdlistatransaccionesnucleo" );
			
		}
		

		if(PERSONAS_CONTROLAR_POR_APORTS == true ){
			$this->OButton("TR.Imprimir SOLICITUDINGRESO", "var xP= new PersGen();xP.getImprimirSolicitud($clave_de_persona)", $this->ic()->IMPRIMIR, "id-solicitudingreso");
		}
		$this->OButton("TR.NUEVO Credito", "var xP= new PersGen();xP.addCredito($clave_de_persona)", $this->ic()->CREDITO, "cmdnewcredito", "credito");
		
		if(MODULO_LEASING_ACTIVADO == true){
			$this->OButton("TR.Agregar Cotizacion LEASING", "var xP= new PersGen();xP.addLeasing($clave_de_persona)", $this->ic()->CONTROL);
		}
		if($evento !== $xEvt->REGISTRO){
			$this->OButton("TR.Expediente", "var xP= new PersGen();xP.getExpediente($clave_de_persona)", $this->ic()->IMPRIMIR, "id-expediente");
		}
		if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED) == true AND $evento !== $xEvt->REGISTRO){
			$this->OButton("TR.Expediente de Cobranza", "var xSeg=new SegGen(); xSeg.getExpediente({persona:$clave_de_persona});", $this->ic()->REPORTE);
			$this->OButton("TR.FORMS_Y_DOCS", "var xP=new PersGen(); xP.getFormatos($clave_de_persona);", $this->ic()->ARCHIVOS, "", "white");
		}		
		if(CREDITO_PRODUCTO_CON_PRESUPUESTO > 0 ){
			$this->OButton("TR.Agregar Presupuesto", "var xP= new PersGen();xP.addPresupuesto($clave_de_persona)", $this->ic()->DINERO);
		}
		if(PERSONAS_CONTROLAR_POR_APORTS == true ){
			if($evento !== $xEvt->REGISTRO){
				$this->OButton("TR.PERFIL APORTACIONES", "var xP= new PersGen();xP.getPerfilAportaciones($clave_de_persona)", $this->ic()->CALENDARIO1);
				$this->OButton("TR.ESTADO_DE_CUENTA APORTACIONES", "var xP= new PersGen();xP.getReporteAportaciones($clave_de_persona)", $this->ic()->ESTADO_CTA);
				$this->OButton("TR.APORTACIONES DETALLE", "var xP= new PersGen();xP.getReporteAportacionesDet($clave_de_persona)", $this->ic()->ESTADO_CTA);
				$this->OButton("TR.ESTADO_DE_CUENTA SEGUROS_A", "var xP= new PersGen();xP.getReporteSeguro($clave_de_persona,5103)", $this->ic()->ESTADO_CTA);
				$this->OButton("TR.ESTADO_DE_CUENTA SEGUROS_B", "var xP= new PersGen();xP.getReporteSeguro($clave_de_persona,5104)", $this->ic()->ESTADO_CTA);
			}
			//colegiacion Datos
			$this->OButton("TR.DATOS DE COLEGIACION", "var xP= new PersGen();xP.getFormaColegiacion($clave_de_persona)", $this->ic()->DESCARGAR);
		}
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
	function addCreditoComandos($clave_de_credito, $estatus = false, $saldo = null){
		$xBtn	= new cHButton();
		if(MODULO_AML_ACTIVADO == true){
			$this->OButton("TR.Vincular Propietarios", "var xML= new AmlGen();xML.addCuestionario($clave_de_credito)", $this->ic()->PREGUNTAR);
		}
		$this->addToolbar( $xBtn->getBasic("TR.AGREGAR FLUJO DE EFECTIVO", "var xP= new CredGen();xP.getFormaFlujoEfectivo($clave_de_credito)", "balance", "cmd-addflujo", false ) );
		
		//$this->addToolbar( $xBtn->getBasic("TR.VINCULAR AVALES", "var xP= new CredGen();xP.getVincularAvales($clave_de_credito)", "vincular", "vincular-avales" , false) );
		//$this->addToolbar( $xBtn->getBasic("TR.AGREGAR AVALES", "var xP= new CredGen();xP.getFormaAvales($clave_de_credito)", "referencias", "add-avales" , false) );
		$this->OButton("TR.ELEGIR AVALES", "var xP= new CredGen();xP.getElegirAvales($clave_de_credito)", $this->ic()->GRUPO, "elegir_avales", "blue");
		
		$this->addToolbar( $xBtn->getBasic("TR.AGREGAR GARANTIAS", "var xP= new CredGen();xP.getFormaGarantias($clave_de_credito)", "bienes", "add-garantias", false ) );
		$this->OButton("TR.DATOS_DE_TRANSFERENCIA", "var CGen=new CredGen(); CGen.setAgregarBancos($clave_de_credito);", $this->ic()->BANCOS);
		
		$this->OButton("TR.Validacion", "var xGen=new CredGen(); xGen.getFormaValidacion($clave_de_credito);", $this->ic()->CHECAR, "", "green");
		$this->OButton("TR.Agregar Memo", "var xP= new CredGen();xP.setNuevaNota($clave_de_credito)", $this->ic()->NOTA);
		
		$this->OButton("TR.Documentacion", "var xC=new CredGen(); xC.getDocumentos($clave_de_credito);", $this->ic()->CHECAR, "", "white");
		//Seguimiento
		//Agregar llamada
		//agregar compromiso
		
		switch($estatus){
			case CREDITO_ESTADO_AUTORIZADO:
				//orden de desembolso
				$this->OButton("TR.REAUTORIZAR", "var xC=new CredGen(); xC.getFormaAutorizacion($clave_de_credito)", $this->ic()->AUTORIZAR);
				$this->OButton("TR.FIRMANTES", "var xC=new CredGen(); xC.getListaDeFirmantes($clave_de_credito)", $this->ic()->LISTA);
				break;
			case CREDITO_ESTADO_SOLICITADO:
				$this->OButton("TR.Autorizar", "var xC=new CredGen(); xC.getFormaAutorizacion($clave_de_credito)", $this->ic()->AUTORIZAR);
				$this->OButton("TR.FIRMANTES", "var xC=new CredGen(); xC.getListaDeFirmantes($clave_de_credito)", $this->ic()->LISTA);
				break;
			default :
				if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED) == true){
					$this->OButton("TR.Agregar Compromiso", "var xCred=new CredGen(); xCred.setAgregarCompromiso($clave_de_credito);", $this->ic()->CALENDARIO1);
					$this->OButton("TR.Agregar llamada", "var xCred=new CredGen(); xCred.setAgregarLlamada($clave_de_credito);", $this->ic()->TELEFONO);
					$this->OButton("TR.Agregar Notificacion", "var xCred=new CredGen(); xCred.setAgregarNotificacion($clave_de_credito);", $this->ic()->AVISO);
					$this->OButton("TR.Expediente de Cobranza", "var xCred=new CredGen(); xCred.getExpedienteDeCobranza($clave_de_credito);", $this->ic()->REPORTE);
					//
					
				}
				break;
		}
		
		//$this->addToolbar( $xBtn->getBasic($xBtn->lang("Imprimir", "Solicitud"), "var xP= new PersGen();xP.getImprimirSolicitud($clave_de_persona)", "imprimir", "id-solicitudingreso", false ) );		
	}
	function addRecibosComando($recibo, $path_formato = ""){
		$url		= $path_formato;
		$xUser		= new cSystemUser();
		if($url == ""){
			$xRec	= new cReciboDeOperacion(false, false, $recibo);
			if($xRec->init() == true){
				$url	= $xRec->getURI_Formato();
			}
		}
		$this->OButton("TR.Reporte del Recibo", "var xRec = new RecGen();xRec.reporte($recibo);", $this->ic()->REPORTE, "rpt-$recibo");
		if(getEsModuloMostrado(USUARIO_TIPO_CAJERO) == true){
			$this->OButton("TR.Reimprimir Recibo", "var xG=new Gen();xG.w({full:true,url:'$url'});", "imprimir", "print-$recibo", "white");
			if($xUser->getPuedeEditarRecibos() == true){
				$this->OButton("TR.Agregar Bancos", "var xRec = new RecGen();xRec.addBancos($recibo);", "bancos", "bn-$recibo");
				$this->OButton("TR.Agregar Tesoreria", "var xRec = new RecGen();xRec.addTesoreria($recibo);", $this->ic()->DINERO, "tes-$recibo");
				$this->OButton("TR.Editar Recibo", "var xRec = new RecGen();xRec.editar($recibo);", $this->ic()->EDITAR, "edit-$recibo", "yellow");
			}
		}

		if( getEsModuloMostrado(USUARIO_TIPO_CONTABLE) == true ){
			$this->OButton("TR.Factura", "var xRec = new RecGen();xRec.factura($recibo);", $this->ic()->EXPORTAR);
			$this->OButton("TR.POLIZA_CONTABLE", "var xRec = new RecGen();xRec.getExistePolizaContable({ recibo:$recibo, open :true});", $this->ic()->EXPORTAR);
		}
		if($xUser->getPuedeEliminarRecibos() == true){
			$this->OButton("TR.Eliminar Recibo", "var xRec = new RecGen();xRec.confirmaEliminar($recibo);", $this->ic()->ELIMINAR, "del-$recibo", "red");
		}
	}
	function addAtras(){ $this->OButton("TR.Regresar", "window.history.back(1)", $this->ic()->ATRAS); }
	function addEliminar($evt=""){ $this->OButton("TR.ELIMINAR", $evt, $this->ic()->ELIMINAR,"cmdeliminar", "red"); }
	function addFecha($fecha = false){
		$this->ODate("idfechaactual", $fecha, "TR.Fecha");
	}
	function addFechaRecibo($fecha = false){
		$xF			= new cFecha();
		$fecha		= $xF->getFechaISO($fecha);
		
		$xRuls		= new cReglaDeNegocio();
		$LimFut		= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_BLOQ_FECHA_FUT);
		$LimPas		= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_BLOQ_FECHA_ANT);
		$id			= "idfechaactual";
		$titulo		= "TR.FECHA_DE_CAPTURA";
		if($LimFut == true AND $LimPas == true){
			$this->ODisabled_13($id, $fecha, $titulo);
		} else {
			$xDate		= new cHDate();
			$xDate->setDivClass("tx4 tx18 blue");
			$xDate->setID($id);
			
			if($LimFut == true){
				$fechaF	= $xF->setSumarDias(1, $fecha);
				$xDate->setDateMax($fechaF);
			}
			if($LimPas == true){
				$xDate->setDateMin($fecha);
			}
			$this->addHElem( $xDate->get($titulo, $fecha ));
		}
		return "";	
		//$this->ODate("idfechaactual", $fecha, "TR.Fecha");
	}
	function addAviso($txt, $id = "", $mostrarTip = false, $class = "notice"){
		$xHO	= new cHObject();
		if($mostrarTip == true){
			$ntxt	= preg_replace( "/\r|\n/", "", $txt);
			if($class == "error"){
				$this->addAvisoInicial($ntxt, true);
			} else {
				$this->addAvisoInicial($ntxt);
			}
			$ntxt	= null;
		}
		$txt	= $xHO->Out($txt, OUT_HTML);
		$id		= ($id== "") ? "idmsgs" : $id;
		$xNot	= new cHNotif($class);
		
		$this->addFootElement( $xNot->get($txt, $id));
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
	function addObservaciones($id = "", $valor = ""){
		$id		= ($id == "") ? "idobservaciones" : $id;
		$xTxt	= new cHText();
		$this->addHElem( $xTxt->getDeObservaciones($id, $valor, "TR.Observaciones"));
	}
	function getAFormsDeTipoPago(){ return  $this->mArrOpsFrm; }
	function OText($id, $valor, $titulo = "", $add = true, $html = ""){
		$xTxt	= new cHText();
		if($add == true){
			$this->addHElem( $xTxt->getNormal($id, $valor, $titulo, $html) );
		}
		return $xTxt;
	}
	function OMail($id, $valor, $titulo = "", $add = true, $html = ""){
		$xTxt	= new cHText();
		if($add == true){
			$titulo	= ($titulo == "") ? "TR.CORREO_ELECTRONICO" : $titulo;
			$xTxt->setPlaceholder("ejemplo@sitio.com");
			$xTxt->setDivClass("tx4 tx18 orange");
			$this->addHElem( $xTxt->getNormal($id, $valor, $titulo, $html) );
			$this->setValidacion($id, "validacion.email", $this->l()->getMensajeByTop("GENERAL_FALTA_MAIL"));
		}
		return $xTxt;
	}
	function OText_13($id, $valor, $titulo = "", $d = true, $html = "", $css =""){
		$xTxt	= new cHText();
		
		$xTxt->setDivClass("tx4 tx18$css");
		$this->addHElem( $xTxt->getNormal($id, $valor, $titulo, $html) );
		
		return $xTxt;
	}
	function OMoneda($id, $valor, $titulo, $letras = false, $add = true){
		$xTxt	= new cHText();
		$xTxt->setDivClass("tx4 tx18");
		
		$this->addHElem( $xTxt->getDeMoneda($id, $titulo, $valor, $letras) );
		
		return $xTxt;		
	}
	function OCodigoPostal($id = "", $valor= ""){
		$xTxt	= new cHText();
		
		$this->addHElem( $xTxt->getDeCodigoPostal($id, $valor) );
		
	}
	function ONumero($id, $valor, $titulo, $letras = false, $add = true){
		$xTxt	= new cHText();
		$xTxt->setDivClass("tx4 tx18");
		if($add == true){
			$this->addHElem( $xTxt->getDeMoneda($id, $titulo, $valor, $letras) );
		}
		return $xTxt;
	}
	function OMoneda2($id, $valor, $titulo, $letras = false, $add = true){
		$xTxt	= new cHText();
		
		if(in_array($id, $this->mArrReglaVis) == true){//Forzar a Hidden
			$this->OHidden($id, $valor);
		} else {
			$xTxt->setDivClass("tx4 tx18");
			$this->addHElem( $xTxt->getDeMoneda2($id, $titulo, $valor, $letras) );
		}
		return $xTxt;
	}
	
	function OTasa($id, $valor, $titulo){
		$xTxt	= new cHText();
		$xTxt->setDivClass("tx4 tx18");
		$this->addHElem( $xTxt->getDeTasa($id, $titulo, $valor) );
		
		return $xTxt;
	}
	function OButton($titulo, $event, $icon = "", $id = "", $class=""){
		$xBtn	= new cHButton();
		if(OPERACION_LIBERAR_ACCIONES == false){
			//Verificar permisos
			$xUser	= new cSystemUser();
			if( $xUser->puede($this->mID, $titulo) == true){
				$this->addToolbar( $xBtn->getNav($titulo, $event, $icon, $id, $class ) );
			}
		} else {
			$this->addToolbar( $xBtn->getNav($titulo, $event, $icon, $id, $class ) );
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
		$xDate->setDivClass("tx4 tx18 blue");
		$xDate->setID($id);
		
		if($add == true){
			$this->addHElem( $xDate->get($titulo, $valor ));
		}
		return $xDate;		
	}
	function OFechaNac($id, $valor, $titulo, $add = true){
		$xDate		= new cHDate();
		$xDate->setDivClass("tx4 tx18 blue");
		$xDate->setID($id);
		$xDate->setEsDeNacimiento();
		if($add == true){
			$this->addHElem( $xDate->get($titulo, $valor ));
		}
		return $xDate;
	}
	function OFechaLarga($id, $valor, $titulo, $add = true){
		$xDate		= new cHDate();
		$xDate->setDivClass("tx4 tx18 blue");
		$xDate->setID($id);
		$xDate->setEsDeReporte();
		if($add == true){
			$this->addHElem( $xDate->get($titulo, $valor ));
		}
		return $xDate;
	}
	function OSelect($id, $valor, $titulo, $options = false, $add = true){
		$xSel		= new cHSelect();
		$xSel->setDivClass("tx4 tx18");
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
	function OCheck($titulo = "", $id = "", $checked = false){
		$xChk		= new cHCheckBox();
		$this->addHElem( $xChk->get($titulo, $id, $checked) );
		$xChk		= null;
	}
	function OSiNo($titulo = "", $id = "", $value = 0, $tiny = true){
		$xChk		= new cHCheckBox();
		if($tiny == true){
			$xChk->setDivClass("tx4 tx18");
		}
		$this->addHElem( $xChk->getSiNo($titulo, $id, $value) );
		$xChk		= null;
	}
	function addSeccion($id, $titulo, $btn=""){

		$titulo 		= $this->l()->getT($titulo);
		if($this->mIsWizard == true){ 
			$this->addHElem("<h3>$titulo $btn</h3><section>");
		} else { 
			$this->addHElem("<h3><a href=\"#\">$titulo</a>$btn</h3><div class='formoid-default formoid-section' id='$id'>");
		}
		 
		$this->mConAcc	= true;
	}
	function endSeccion(){ 
		if($this->mIsWizard == true){ $this->addHElem("</section>"); } else { $this->addHElem("</div>"); }
	}
	function ODetails($resumen, $texto){
		$this->addHElem("<details><summary>$resumen</summary><p>$texto</p>");
	}
	function setIsWizard(){ $this->mIsWizard = true; $this->mAccLock	= true;}
	function addKey(){
		$xF			= new cFecha();
		$key 		= md5($this->mName . MY_KEY . $xF->getMarca() . rand(0, 9995));
		$_SESSION["frm." . $this->mName]	= $key;
		$this->addFootElement("<input value=\"_" . $this->mName . "\" type=\"hidden\" />");
		return $key;	 
	}
	function OHidden($id, $valor, $titulo = "", $add = true){
		$xTxt	= new cHText();
		$this->addFootElement( $xTxt->getHidden($id, "40", $valor) );
		
		return $xTxt;
	}
	function ODisabled($id, $valor, $titulo = "", $add = true){
		$xTxt	= new cHText(); $xTxt2	= new cHText();
		$xTxt->setProperty("disabled", "disabled");
		//$xTxt->setDiv13();
		
		$this->addHElem( $xTxt->get($id, $valor, $titulo) );
		
		$this->addFootElement( $xTxt2->getHidden($id, "", $valor) );
	
		return $xTxt;
	}
	function ODisabled_13($id, $valor, $titulo = "", $add = true){
		$xTxt	= new cHText(); $xTxt2	= new cHText();
		$xTxt->setProperty("disabled", "disabled");
		$xTxt->setDiv13();
		
		$this->addHElem( $xTxt->get($id, $valor, $titulo) );
		
		$this->addFootElement( $xTxt2->getHidden($id, "", $valor) );
		
		return $xTxt;
	}
	function ODisabledM($id, $valor, $titulo = "", $add = true){
		$xTxt	= new cHText(); $xTxt2	= new cHText();
		$xTxt->setProperty("disabled", "disabled");
		$xTxt->setDiv13();
		$xTxt->setProperty("class", "mny");
		$vv		= getFMoney($valor);
		$this->addHElem( $xTxt->get($id, $vv, $titulo) );
		
		$this->addFootElement( $xTxt2->getHidden($id, "", $valor) );
		
		return $xTxt;
	}
	function addDisabledInit($id){
		$idx	= $id . "_dis";
		$this->OHidden($idx, "");
		
		$this->addJsInit("var xG=new Gen();xG.disableSelect('$id');");	
	}
	function ic(){ if($this->icDic == null){ $this->icDic = new cFIcons(); }  return $this->icDic; }
	function addLog($msg){
		if(MODO_DEBUG == true AND $msg != ""){
			$xFL	= new cFileLog(false, true);
			$xFL->setWrite($msg);
			$xFL->setClose();
			unset($msg);
			$this->addToolbar( $xFL->getLinkDownload("TR.LOG_FILE", ""));
		} else {
			$msg	= null;
		}		
	}
	function convert($input, $format = OUT_HTML){	$xObj	= new cHObject();	return $xObj->Out($input, OUT_HTML);	}
	function addFooterBar($html){		$this->mFooterBar	.= $html;	}
	function setResultado($validador, $msgReady = "", $msgError = "", $cerrar = false){
		if($validador == false){
			$this->addAvisoRegistroError($msgError);
		} else {
			$this->addAvisoRegistroOK($msgReady);
			if($cerrar == true){
				$this->addCerrar("",3);
			}
		}
	}
	function setNoAcordion(){ $this->mAccLock	= true; }
	function setNoJsEvtKeyForm(){ $this->mNoKeyForm = true; }
	function addCRUD($tabla, $cerrar = false, $callback=""){
		$cls	= ($cerrar == true) ? ",close:true": "";
		$cb		= ($callback == "") ? "" : ",callback:$callback";
		
		$this->addGuardar("var xG=new Gen();xG.crudAdd({evt:event,id:'" . $this->mID . "',tabla:'$tabla'$cls$cb})");
		//$this->addJsCode("function jsClose");
		$this->setHappyButton("btn_guardar");
	}
	function addCRUDSave($tabla, $clave, $cerrar = false){
		$cls	= ($cerrar == true) ? ",close:true": "";
		$this->addGuardar("var xG=new Gen();xG.save({evt:event,form:'" . $this->mID . "',tabla:'$tabla', id:'$clave'$cls})", "", "TR.ACTUALIZAR");
		//$this->addJsCode("function jsClose");
		$this->setHappyButton("btn_guardar");
	}
	function setHappyButton($id){ $this->mHappyBtn = $id; }
	function getT($str){ return $this->l()->getT($str);}
	function addRangeSupport(){
		
	}
	function setNoFormTags(){ $this->mNoFormTag = true; }
	function OTextContable($id, $valor, $titulo = "", $d = true, $html = "", $css =""){
		$xTxt	= new cHText();
		
		$xTxt->setDivClass("tx4 tx18$css");
		$this->addHElem( $xTxt->getDeCuentaContable($id, $valor, false, false, $titulo) );
		return $xTxt;
	}
	function setMultisel($id){
		$this->addJsInit(" $('#$id').multiSelect();");
	}
	function addJsReload(){
		//$evt	= "var blurred = false; window.onblur = function() { blurred = true; };window.onfocus = function() { blurred && (location.reload()); };";
		//$this->addJsInit($evt);
	}
	function setInitVerPorRegla($regla){
		$xRuls			= new cReglaDeNegocio();
		$arr			= $xRuls->getArrayPorRegla($regla);
		
		$this->mArrReglaVis	= $arr;
	}
	function addButtonPlanDePagos($idnumeroplan){
		$this->OButton("TR.PLAN_DE_PAGOS", "var xC=new CredGen();xC.getImprimirPlanPagos($idnumeroplan);", $this->ic()->CALENDARIO1, "idcmdprintplan", "whiteblue");
	}
	function addTag($txt, $tipo = ""){
		$xNot		= new cHNotif();
		if($tipo == "err" OR $tipo == "error"){
			$tipo	= $xNot->ERROR;
		}
		$this->addHElem( $xNot->getTag($txt, "", $tipo) );
	}
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
	protected $mTitle			= "";	protected $mArrEventsVals	= array();
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
	private $mSelect	= "";
	
	function __construct($nombre = "ctipo_pago", $id="idtipo_pago"){
		$this->mNombre	= $nombre;
		$this->mID		= $id;
		$this->mSelect	= "";
	}
	function setSelectOpt($t){ $this->mSelect=$t; }
	function get($UsarEn = false, $TDExtra	= "", $TDInicial = "", $IncluirRFiscal = true ){
		$RFiscal 	= ($IncluirRFiscal == false) ? "" : "<div class='tx4 tx18'><label for='id-foliofiscal'>Recibo Fiscal</label><input type='text' name='foliofiscal' id='id-foliofiscal' value='" . DEFAULT_RECIBO_FISCAL . "'  /></div>";
		$hiddens	= ($IncluirRFiscal == false) ? "<input type='hidden' name='foliofiscal' id='id-foliofiscal' value='" . DEFAULT_RECIBO_FISCAL . "'  />" : "";
		$tipo		= ($UsarEn == false) ? TESORERIA_TIPO_INGRESOS : $UsarEn;
		$select		= ($tipo == TESORERIA_TIPO_INGRESOS AND $this->mSelect == "") ? DEFAULT_TIPO_PAGO : $this->mSelect;

		$txtcheque		= "<input type='hidden' name='cheque' value='' id='cheque' >";
		if(MODULO_CAJA_ACTIVADO == false OR $tipo == TESORERIA_TIPO_EGRESOS){
			$xT			= new cHText();
			$xT->setDiv13(" blue");
			$txtcheque	= $xT->getNormal("cheque", "", "TR.CLAVE CHEQUE / TRANSFERENCIA");
		}
		$xht	= "$TDInicial
			<div class='tx4 tx18 green'>
			<label for='" . $this->mID . "'>Tipo de Pago</label>
			" . $this->getSelectTiposDePago($tipo, $select) . "
			</div>
			$RFiscal $txtcheque
			$TDExtra
			 $hiddens";
		return $xht;
	}
	function setEvents($mixEvents = ""){ $this->mEvents		.= $mixEvents; }
	function setOptions($opts){ $this->mExtra	.= $opts; }
	function getSelectTiposDePago($tipo = TESORERIA_TIPO_INGRESOS, $select = ""){
		$xUsr	= new cSystemUser();
		$idr	= $xUsr->getIDParaRegla();
		
		$xSel	= new cHSelect();
		$select	= strtolower($select);
		$opts	= "";
		$xQL	= new MQL();
		$ByTipo	= ($tipo == TESORERIA_TIPO_EGRESOS) ? "-1" : "1";
		$sql	= "SELECT `tipo_de_pago`,`descripcion` FROM `tesoreria_tipos_de_pago` WHERE `activo`= 1 AND (`tipo_de_movimiento`=$ByTipo OR `tipo_de_movimiento`=0)  
		AND (`admitidos` LIKE '%,$idr%' OR `admitidos` LIKE '%,$idr,%' OR `admitidos` LIKE '$idr,%') ";
		//setLog($sql);
		$rs		= $xQL->getDataRecord($sql);
		foreach ($rs as $rw){
			$s		= ($rw["tipo_de_pago"] == $select) ? " selected='selected'" : "";
			$opts	.= "<option value='" . strtolower($rw["tipo_de_pago"]) . "'$s>" . $rw["descripcion"] . "</option>";
		}
		$ctrl =  "<select name=\"" . $this->mNombre ."\" id=\"" . $this->mID . "\" " . $this->mEvents . ">$opts " . $this->mExtra . "</select>";
		return  $ctrl;
	}
}
class cHFile extends cHInput {
	private $mLIDs			= array();
	private $mUseProgress	= false;
	function getBasic($id, $valor, $titulo="",$css=""){
		$this->setClearProperties();
		$titulo 					= ($titulo == "") ? "TR.Archivo" : $titulo;
		$this->mArrProp["type"]		= "file";
		$this->mArrProp["name"]		= $id; $this->mLIDs[]	= $id;
		$this->mId					= $id;
		$this->mLbl					= $titulo;
		$this->mIncLabel			= ($titulo!="") ? true : false;
		$this->mArrProp["value"]	= $valor;
		$this->mValue				= $valor;
		$css						= ($css == "") ? $this->mDivClass : $css;
		$this->setDivClass($css);
		//, "<span id=\"na-$id\"><i class=\"fa fa-file\"></i></span>"
		$html	= ($this->mUseProgress == false) ? "" : "<div class='progress'><span class='orange' style='width:1%' id='pgr-$id'></span></div>";
		return $this->get($id, $valor, $titulo,"", false, $html);
	}
	function setUseProgressBar(){ $this->mUseProgress = true; }
}
class cHText extends cHInput {
	private $mLIDs			= array();
	private $mPlaceholder	= "";
	
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
		$npersona						= "";
		if(setNoMenorQueCero($persona)> DEFAULT_SOCIO){
			$xSoc	= new cSocio($persona);
			if($xSoc->init() == true){
				$npersona				= $xSoc->getNombreCompleto(OUT_HTML);
			}
		}
		//$titulo			= $xLn->get("clave de persona");//($titulo == "") ? $xLn->get("clave de persona") : $xLn->getT($titulo);
		$titulo2		= ($titulo == "")  ? $xLn->get("nombre completo") : $xLn->getT($titulo);;
		//$this->addEvent("envsoc()", "onchange");
		$this->addEvent("var xPG = new PersGen();xPG.getNombre(this.value, 'nombresocio$id');$blurEvents", "onblur");
		$this->mIncLabel				= true;
		if($SinBoton == false){
			$xBtn		= new cHImg();
			$this->setClearHTML();
			$this->addHTMLCode($xBtn->get16("common/search.png", " onclick=\"var xPG = new PersGen();xPG.getFormaBusqueda({control:'idsocio$id'});\" "));
		}
		$xhNSocio		= new cHInput("nombresocio$id", "", $titulo2);
		$xhNSocio->setIncludeLabel(false);
		$xhNSocio->setProperty("name", "nombresocio$id");
		$xhNSocio->setProperty("disabled", "disabled");
		$this->setDivClass("tx14");
		$xhNSocio->setDivClass("tx34");
		return "<div class='tx1'> ". $this->get("idsocio$id", $persona, $xLn->get("clave de persona")) . $xhNSocio->get("nombresocio$id", $npersona) . "</div>";		
	}
	function getDeCredito($id="", $credito = false, $titulo = ""){
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= "idsolicitud";
		if($credito == false){
			
		} else {
			$this->mValue				= $credito;
		}
		$this->addEvent("var xc = new CredGen(); xc.getDescripcion(this.value);", "onchange");
		$this->addEvent("var xc = new CredGen(); xc.getDescripcion(this.value);if(typeof jsEvaluarSalida != 'undefined'){jsEvaluarSalida(this);}", "onblur");
		$this->mIncLabel				= true;
		$this->addHTMLCode(CTRL_GOCREDIT);
				
		$dSol							= new cHInput("nombresolicitud", "");
		$dSol->setIncludeLabel(false);
		$dSol->setProperty("name", "nombresolicitud");
		$dSol->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$dSol->setDivClass("tx34");
		
		return "<div class='tx1' id='divcredito$id'> ". $this->get("idsolicitud", "", "TR.CLAVE_DE_CREDITO") . $dSol->get("nombresolicitud", "", "TR.DESCRIPCION") . "</div>";
	}
	function getDeGrupo($id="", $grupo = false){
		$id								= ($id == "") ? "idgrupo" : $id;
		$this->mArrProp["type"]			= "number";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= $id;
		if( setNoMenorQueCero($grupo) >0){ $this->mValue				= $grupo;}
		$this->addEvent("var xgg = new GroupGen(); xgg.getDescripcion(this.value);", "onblur");
		$this->mIncLabel				= true;
		$this->addHTMLCode("<img class='buscador' title=\"Buscar un Grupo\" src=\"../images/common/search.png\"  onclick=\"var xP=new PersGen();xP.getBuscarGrupos('$id');\"/>");
		$dSol				= new cHInput("nombregrupo", "", "");
		$dSol->setIncludeLabel(false);
		$dSol->setProperty("name", "nombregrupo");
		$dSol->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$dSol->setDivClass("tx34");
		//$this->addEvent("var xg = new Gen(); xg.letras({monto: this.value, id: '$id-EnLetras'});", "onblur");
		return "<div class='tx1' id='div$id'> ". $this->get($id, $grupo, "TR.codigo de grupo") . $dSol->get("nombregrupo", "", "TR.NOMBRE DEL GRUPO") . "</div>";
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
		$this->addHTMLCode("<img class='buscador' title=\"Buscar una Cuenta de Captacion\" src=\"../images/common/search.png\"  onclick=\"var xP = new PersGen(); xP.getBuscarCuentas();\"/>");
		//
		$dSol		= new cHInput("nombrecuenta", "", $xLn->get("Descripcion"));
		$dSol->setIncludeLabel(false);
		$dSol->setProperty("name", "nombrecuenta");
		$dSol->setProperty("disabled", "true");
		$this->setDivClass("tx14");
		$dSol->setDivClass("tx34");
	
		return "<div class='tx1'> ". $this->get("idcuenta", $cuenta, $xLn->get("numero_de_cuenta")) . $dSol->get("nombrecuenta") . "</div>";
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
		$ctrl						= "";
		$id							= ( $id == "" ) ? $this->mId : $id;
		$this->mArrProp["type"]		= "number";
		$this->mArrProp["class"]	= "mny";
		$this->mArrProp["name"]		= $id; $this->mLIDs[]	= $id;
		$this->mArrProp["value"]	= $value;
		$this->mArrProp["step"]		= "0.01";
		$this->mValue				= $value;
		$this->mLbl					= $Titulo;
		$this->mId					= $id;
		$this->mIncLabel			= ($Titulo == "" ) ? false : true;
		//agrega un control con Letras
		if ( $AgregarEnLetras == true ){
			$xhN		= new cHInput("$id-iEnLetras", "", "TR.Monto_en_Letras");
			$xhN->setIncludeLabel(false);
			
			$xhN->setProperty("name", "$id-EnLetras");
			$xhN->setProperty("size", "40");
			$xhN->setProperty("disabled", "true");
			$this->addEvent("var xg = new Gen(); xg.letras({monto: this.value, id: '$id-EnLetras'});", "onblur");

			$this->setDivClass("tx13");
			$xhN->setDivClass("tx23");
			$ctrl		= "<div class='medio'> ". $this->get($id, "", $Titulo) . $xhN->get("$id-EnLetras") . "</div>";
		} else {
			
			$ctrl		= $this->get(); 
		}
		return $ctrl;
	}
	function getDeMoneda2($id = "", $Titulo = "", $value = 0, $AgregarEnLetras = false){
		$this->setClearProperties();
		$ctrl							= "";
		$id								= ( $id == "" ) ? $this->mId : $id;
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
	
		$value2							= getFMoney($value);
		$this->mValue					= $value2;
		$this->mArrProp["value"]		= $value2;
		$this->mLbl						= $Titulo;
		$this->mId						= $id . "_mny";
		$this->mIncLabel				= ($Titulo == "" ) ? false : true;
		$this->addHTMLCode("<input type='hidden' value='$value' id='$id' name='$id' />");
		$this->addEvent("var xG=new Gen();xG.aMoneda({idPara:'$id', idDesde:'" . $id . "_mny', evt:event});return false;", "onkeyup");
	
		//agrega un control con Letras
		if ( $AgregarEnLetras == true ){
			$xhN		= new cHInput("$id-iEnLetras", "", "TR.Monto_en_Letras");
			$xhN->setIncludeLabel(false);
			$xhN->setProperty("name", "$id-EnLetras");
			$xhN->setProperty("disabled", "disabled");
			$this->addEvent("var xg = new Gen(); xg.letras({monto: this.value, id: '$id-EnLetras'});", "onblur");
	
			$this->setDivClass("tx13");
			$xhN->setDivClass("tx23");
			$ctrl		= "<div class='medio'> ". $this->get($this->mId, $this->mValue, $Titulo) . $xhN->get("$id-EnLetras") . "</div>";
		} else {
			$ctrl		= $this->get($this->mId, $this->mValue, $Titulo);
		}
		return $ctrl;
	}
	function getDeMoneda3($id = "", $Titulo = "", $value = 0, $AgregarEnLetras = false){
		$this->setClearProperties();
		$ctrl							= "";
		$id								= ( $id == "" ) ? $this->mId : $id;
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
	
		$this->mArrProp["disabled"]		= "disabled";
		$value2							= getFMoney($value);
		$this->mValue					= $value2;
		$this->mArrProp["value"]		= $value2;
		$this->mLbl						= $Titulo;
		$this->mId						= $id . "_dis";
		$this->mIncLabel				= ($Titulo == "" ) ? false : true;
		$this->addHTMLCode("<input type='hidden' value='$value' id='$id' name='$id' />");
		//agrega un control con Letras
		if ( $AgregarEnLetras == true ){
			$xhN		= new cHInput("$id-iEnLetras", "", "TR.Monto_en_Letras");
			$xhN->setIncludeLabel(false);
			$xhN->setProperty("name", "$id-EnLetras");
			$xhN->setProperty("disabled", "disabled");
			$this->setDivClass("tx14");
			$xhN->setDivClass("tx34");
			$ctrl		= "<div class='tx1'> ". $this->get($this->mId, $this->mValue, $Titulo) . $xhN->get("$id-EnLetras") . "</div>";
		} else {
			$ctrl		= $this->get($this->mId, $this->mValue, $Titulo);
		}
		return $ctrl;
	}
	function getDeConteo($id = "", $Titulo = "", $value = 0, $Maximo = 100){
		$this->setClearProperties();
		
		$ctrl						= "";
		$ln							= strlen("$Maximo")+1;
		$id							= ( $id == "" ) ? $this->mId : $id;
		$this->mArrProp["type"]		= "number";
		$this->mArrProp["class"]	= "mny";
		$this->mArrProp["name"]		= $id; $this->mLIDs[]	= $id;
		$this->mArrProp["value"]	= $value;
		$this->mArrProp["step"]		= "1";
		$this->mArrProp["maxlength"]= $ln;
		$this->mArrProp["max"]		= $Maximo;
		$this->mArrProp["min"]		= 0;
		$this->mValue				= $value;
		$this->mLbl					= $Titulo;
		$this->mId					= $id;
		$this->mArrProp["style"]	= "width:" . $ln . "em;max-width:" . $ln . "em";
		$this->mIncLabel			= ($Titulo == "" ) ? false : true;

		$ctrl		= $this->get();
		
		return $ctrl;
	}
	function getDeTasa($id = "", $Titulo = "", $value = 0, $AgregarEnLetras = false){
		$this->setClearProperties();

		$ctrl				= "";
		$id					= ( $id == "" ) ? $this->mId : $id;
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["class"]		= "mny";
		$this->mArrProp["name"]			= $id; $this->mLIDs[]	= $id;
		
		$this->mArrProp["step"]			= "0.01";
		$value2							= ($value*100);
		
		$this->mValue					= $value2;
		$this->mArrProp["value"]		= $value2;
		$this->mArrProp["maxlength"]	= "10";
		$this->mLbl						= $Titulo;
		$this->mId						= $id . "_ts";
		$this->mIncLabel				= ($Titulo == "" ) ? false : true;
		$this->addHTMLCode("<input type='hidden' value='$value' id='$id' name='$id' />");
		$this->addEvent("$('#$id').val(redondear( (flotante(this.value)/100),6 ) );return false;", "onkeyup");
		
		//agrega un control con Letras
		if ( $AgregarEnLetras == true ){
			$xhN		= new cHInput("$id-iEnLetras", "", "TR.Monto_en_Letras");
			$xhN->setIncludeLabel(false);
				
			$xhN->setProperty("name", "$id-EnLetras");
			$xhN->setProperty("disabled", "true");
			$this->addEvent("var xg = new Gen(); xg.letras({monto: this.value, id: '$id-EnLetras'});", "onblur");
	
			$this->setDivClass("tx14");
			$xhN->setDivClass("tx34");
			$ctrl		= "<div class='tx1'> ". $this->get($this->mId, $this->mValue, $Titulo) . $xhN->get("$id-EnLetras") . "</div>";
		} else {
			$ctrl		= $this->get($this->mId, $this->mValue, $Titulo);
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
	function setPlaceholder($txt){
		$this->mArrProp["placeholder"]	= $txt;
		$this->mPlaceholder = $txt;
	}
	function getBasic($id, $size = 0, $class = "normalfield", $valor = "", $titulo = "", $forceClearProps = false){
		if($forceClearProps	== true ){
			$this->setClearProperties();
		}
		$this->mArrProp["type"]			= "text";
		$this->mArrProp["name"]			= $id;
		$this->mId						= $id; $this->mLIDs[]	= $id;
		
		if($class != ""){ $this->mArrProp["class"] = $class; }
		$this->mValue				= $valor;
		$this->mLbl						= $titulo;
		$this->mArrProp["title"]		= $titulo;
		$this->mIncLabel				= ( $titulo != "" ) ? true : false;
		$this->mArrProp["value"]		= $valor;
		return $this->get($id, $valor, $titulo);
	}
	function getNormal($id, $valor = "", $titulo = "", $html = ""){
		$this->setClearProperties();
		if($this->mPlaceholder !== ""){
			$this->mArrProp["placeholder"]			= $this->mPlaceholder;
		}
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
		$this->mArrProp["type"]	= "text";
		$this->mArrProp["name"]	= $id; $this->mLIDs[]	= $id;
		$this->mId				= $id;
		$this->mLbl				= $label;
		$this->mIncLabel		= ($label != "") ? true : false;
		$this->mArrProp["value"]= $valor;
		$this->mValue			= $valor;
	
		return $this->get($id, $valor, $label);
	}
	function getHidden($id, $size=0, $valor = "" , $forceClearProps = false){
		if($forceClearProps	== true ){
			$this->setClearProperties();
		}
		$this->setDivClass("");
		$this->mArrProp["type"]			= "hidden";
		$this->mArrProp["name"]			= $id;
		$this->mId						= $id; $this->mLIDs[]	= $id;
		
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
		if($this->mDivClass !== ""){
			$this->setDiv13();
		}
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
			$snipt				= "<div id='div$id' class='tx23'>
									<label for='dl$id'>" . $this->lang("Colonia") . "</label>
									<input type='text' id='idnombrecolonia' name='idnombrecolonia' list='dl$id' onblur='var xg$id=new DomGen();xg$id.setColoniasXCP(this);'>
									<datalist id='dl$id'><option /></datalist>
									</div>";
		} else {
			$snipt				= "<div id='div$id' class='tx23'><input type='hidden' id='idnombrecolonia' name='idnombrecolonia' /><label for='dl$id'>" . $this->lang("Colonia") . "</label>
								<select id='dl$id' onblur='var xg$id = new DomGen(); xg$id.setColoniasXCP(this);' ><option /></select></div>";
		}
		$this->setDivClass("tx13");
		$ctrl				= "<div class='medio'> ". $this->get($id, $valor, $titulo ) . $snipt . "</div><input type='hidden' id='idcp_$id' />";
		
		return $ctrl;
	}
	function getDeActividadEconomica($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		$titulo 						= ($titulo == "") ? "TR.ACTIVIDAD_ECONOMICA UIF" : $titulo;
		$id								= ( $id == "" ) ? "idactividadeconomica" : $id;
		$this->mLIDs[]					= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg$id=new PersAEGen();xg$id.getListaDeActividades(this, event);", "onkeyup");
		$this->addEvent("var xg$id=new PersAEGen();xg$id.setActividadPorCodigo(this);", "onchange");
	
		$xhN		= new cHInput("iddescripcion$id", "", "TR.NOMBRE ACTIVIDAD_ECONOMICA UIF");
		$xhN->setIncludeLabel(false);
		$xhN->setProperty("name", "iddescripcion$id");
		$xhN->setProperty("disabled", "disabled");
		
		$xBtn		= new cHImg();
		$this->setClearHTML();
		$this->addHTMLCode($xBtn->get16("common/search.png", " onclick=\"var xPA=new PersAEGen(); xPA.getBuscarActs('$id');\" "));
				
		$this->setDivClass("tx13 blue");
		$xhN->setDivClass("tx23 blue");
		
		
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		$this->setProperty("autocomplete", "off");
		
		$ctrl		= "<div class='medio'> ". $this->get($id, $valor, $titulo ) . $xhN->get("iddescripcion$id") . "</div>";
		
		return $ctrl;
	}
	function getDeActividadEconomicaSCIAN($id = "", $valor = "", $titulo = ""){
		$this->setClearProperties();
		$titulo 						= ($titulo == "") ? "TR.ACTIVIDAD_ECONOMICA SCIAN" : $titulo;
		$id								= ( $id == "" ) ? "idactividadeconomica" : $id;
		$this->mLIDs[]					= $id;
		$this->mArrProp["name"]			= $id;
		//$this->addEvent("var xg$id=new PersAEGen();xg$id.getListaDeActividades(this, event);", "onkeyup");
		//$this->addEvent("var xg$id=new PersAEGen();xg$id.setActividadPorCodigo(this);", "onchange");
		
		$xhN		= new cHInput("iddescripcion$id", "", "TR.NOMBRE ACTIVIDAD_ECONOMICA SCIAN");
		$xhN->setIncludeLabel(false);
		$xhN->setProperty("name", "iddescripcion$id");
		$xhN->setProperty("disabled", "disabled");
		
		$xBtn		= new cHImg();
		$this->setClearHTML();
		$this->addHTMLCode($xBtn->get16("common/search.png", " onclick=\"var xPA=new PersAEGen(); xPA.getBuscarActsSCIAN('$id');\" "));
		
		$this->setDivClass("tx13 green");
		$xhN->setDivClass("tx23 green");
		
		
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		$this->setProperty("autocomplete", "off");
		
		$ctrl		= "<div class='medio'> ". $this->get($id, $valor, $titulo ) . $xhN->get("iddescripcion$id") . "</div>";
		
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
		//$this->setProperty("autocomplete", "off");
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
	function getDeCuentaContable($id = "", $valor = "", $addNombre = true, $filtro = false, $titulo = "", $afecta = false){
		$this->setClearProperties();
		//$titulo 			= "TR.Cuenta";
		$id					= ( $id == "" ) ? "idcuentacontable" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$ctrl				= "";
		$filtro				= setNoMenorQueCero($filtro);
		$valor				= setNoMenorQueCero($valor);
		if(trim($titulo) ==""){
			$titulo			= "TR.CUENTA_CONTABLE";
		}
		if($filtro <= 0){
			$this->addEvent("var xg = new ContGen(); xg.getCuentasPorCodigo(this, event);", "onkeyup");
			$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
			$this->setProperty("list", "dl$id");
			if($addNombre == true ){
				$nombre				= "";
				if($valor > 0){
					$xCta			= new cCuentaContable($valor);
					if($xCta->init() == true){
						$nombre		= $xCta->getNombre();
					}
				}
				$this->addEvent("var xg = new ContGen(); xg.getNombreDeCuenta({cuenta: this.value, control: 'nombre_$id'});", "onblur");
				$xhN		= new cHInput("nombre_$id", $nombre, "TR.Nombre de la Cuenta");
				$xhN->setIncludeLabel(false);
				$xhN->setProperty("name", "nombre_$id");
				$xhN->setProperty("disabled", "true");
				$this->setDivClass("tx14");
				$xhN->setDivClass("tx34");
				$ctrl		= "<div class='tx1'> ". $this->get($id, $valor, $titulo ) . $xhN->get("nombre_$id") . "</div>";			
			} else {
				$ctrl		= $this->get($id, $valor, $titulo );
			}
		} else {
			$ByAfect= ($afecta == true ) ? " AND (`afectable`=1) " : "";
			$sql	= "SELECT `numero`, CONCAT(setCuentaFmt(`numero`), IF((`ctamayor`!=3 AND `afectable`=0),' [-] ',''), IF(`ctamayor`=3,' [M] ', ''), IF(`afectable`=1, ' [A] ',''), `nombre`) AS `nombre`  FROM `contable_catalogo` WHERE `numero` LIKE '$filtro%' $ByAfect LIMIT 0,100";
			
			$xSel	= new cSelect($id, $id, $sql);
			$xSel->setLabel($titulo);
			
			$xSel->setEsSql(true);
			if($valor > 0){
				$xSel->setOptionSelect($valor);
			}
			$ctrl	= $xSel->get($titulo, true);
			$xSel	= null;
		}
		return $ctrl;
	}
	function getDeCuentaEquivalenteContable($id = "", $valor = "", $addNombre = true){
		$this->setClearProperties();
		$titulo 			= "TR.Equivalente";
		$id					= ( $id == "" ) ? "idequivalencia" : $id;
		$this->mLIDs[]		= $id;
		$this->mArrProp["name"]			= $id;
		$this->addEvent("var xg = new ContGen(); xg.getCuentaEquivalente(this, event);", "onkeyup");
		$this->addHTMLCode("<datalist id=\"dl$id\"><option /></datalist>");
		$this->setProperty("list", "dl$id");
		if($addNombre == true ){
			$nombre				= "";
			if(setNoMenorQueCero($valor) > 0){
				$xCta			= new cCuentaContable($valor);
				if($xCta->init() == true){
					$nombre		= $xCta->getNombre();
				}
			}
			$this->addEvent("var xg = new ContGen(); xg.getNombreDeCuenta({cuenta: this.value, control: 'nombre_$id'});", "onblur");
			$xhN		= new cHInput("nombre_$id", $nombre, "TR.Nombre Equivalente");
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
		if($this->mDivClass !== ""){ $this->setDiv13(" green"); }
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
	function getLabel($titulo = ""){
		$this->mLbl			= ( $titulo == "") ? $this->mLbl : $titulo;
		$this->mIncLabel	= ( strlen($this->mLbl) > 4 ) ? true : $this->mIncLabel;
		if($this->mLbl != ""){
			$this->mLbl		= $this->getOLang()->getT($this->mLbl);
		}		 
		return "<label for=\"" . $this->mId . "\">" . $this->mLbl . "</label>";
	}
}

class cHButton extends cHInput{
	private $mBClass= "";
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
	function getBasic($label, $actionClick = "", $icono = "", $id = "", $no_toolbar = true, $isTag = false){
		$xL			= new cLang();
		$label		= $xL->getT($label);
		$sniptHTML	= "";
		if($isTag == true){
			$sniptHTML					= "<span>$label</span>";
			$label						= "";
			$this->mHTMLExtra			= "";
			$this->setIcon($icono, "");
		} else {
			$this->setIcon($icono);
		}
		
		$this->mArrProp["type"]			= "button";
		if($no_toolbar == true){ $this->mArrProp["class"]	= "button"; } else { unset($this->mArrProp["class"]); }
		$this->mArrProp["value"]		= $label;
		$id								= ($this->mId == "") ? $id : $this->mId;
		if($this->mBClass !== ""){
			$this->mArrProp["class"]	= $this->mBClass;
		}
		if($id == ""){
			$id = uniqid("btn_");	
		}		
		$this->set($id, $label);
		$this->addEvent($actionClick, "onclick");
		
		return $this->get($id, false, "", $label, "", $sniptHTML);				
	}
	function getNav($label, $actionClick = "", $icono = "", $id = "", $class = ""){
		$xL			= new cLang();
		$label		= $xL->getT($label);
		$sniptHTML	= "";
		$this->setIcon($icono);
		$this->mArrProp["type"]		= "button";
		$this->mArrProp["class"]	= $class;
		$this->mArrProp["value"]	= $label;
		$id				= ($this->mId == "") ? $id : $this->mId;
		if($id == ""){
			$id = uniqid("btn_");
		}
		$this->set($id, $label);
		$this->addEvent($actionClick, "onclick");
	
		return $this->get($id, false, "", $label, "", $sniptHTML);
	}
	function setIcon($icono, $class="fa-2x", $force = false){
		$src		= "";
		
		if(strpos($icono, "fa-") !== false){
			$src	= "<i class=\"fa " . $icono . " $class\"></i>";
			$this->addHTMLCode($src);
		} else {
			
			if( isset( $this->mIcons[$icono] ) ){
				$this->mHTMLExtra	= "";
				$micon				= $this->mIcons[$icono];
				if(strpos($micon, "fa-") !== false){
					$src	= "<i class=\"fa " . $micon . " $class\"></i>";
				} else {
					$src	= "<img src=\"../images/" . $micon . "\"/>"; //DEPRECATED
				}
				$this->addHTMLCode($src);
			}
		}
		if($force == true AND $src == ""){
			$src	= "<i class=\"fa fa-ellipsis-h $class\"></i>";
		}
		return $src;
	}

	function setBClass($cls){ $this->mBClass = $cls; }
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
	function setDelOption($id){ unset($this->mEspOptions[$id]);	}
	function getTitleOption($id){ return $this->mEspOptions[$id];	}
	
	function setChangeOption($id, $title){ $this->mEspOptions[$id]	= $title; }
	//function getListOptions(){ return  }
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
			if($tabla !== ""){
				$xSel->setNoMayus();
			}
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
	/**
	 *  @deprecated @since 2015.01.16 
	 * */
	function getListadoDeBancos($id = "", $selected = false){ return $this->getListaDeBancos($id, $selected); }
	function getListaDeBancos($id = "", $selected = false){
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
	function getListaDeMonedas($id = "", $selected = "", $instrumento = 1){
		$id			= ($id == "") ? "idcodigodemoneda" : $id; $this->mLIDs[]	= $id;
		$w			= ($instrumento === false) ? "": " WHERE `instrumento`=$instrumento ";
		$sqlSc		= "SELECT `tesoreria_monedas`.`clave_de_moneda`, `tesoreria_monedas`.`nombre_de_la_moneda` FROM	`tesoreria_monedas` $w";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$selected	= ($selected == "") ? AML_CLAVE_MONEDA_LOCAL : $selected;
		$xS->setLabel("TR.Moneda");
		$xS->setOptionSelect($selected);
		return $xS;
	}
	function getListaDeCuentasBancarias($id = "", $omitirDefault = false, $selected = false){
		$id			= ($id == "") ? "idcodigodecuenta" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `bancos_cuentas`.`idbancos_cuentas`, `bancos_cuentas`.`descripcion_cuenta` FROM `bancos_cuentas` ";
		$sqlSc		.= ($omitirDefault == false) ? "" : " WHERE	(`bancos_cuentas`.`idbancos_cuentas` !=" . FALLBACK_CUENTA_BANCARIA . ") ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$selected	= setNoMenorQueCero($selected);
		$xS->setDivClass("tx14 tx18");
		$xS->setLabel("TR.Cuenta Bancaria");
		$xS->setEsSql();
		if($selected > 0){	$xS->setOptionSelect($selected);	}
		return $xS;
	}
	function getListaDeCuentasGtiaLiq($id = "", $selected = false, $persona = false){
		$selected	= setNoMenorQueCero($selected);
		$persona	= setNoMenorQueCero($persona);
		
		$id			= ($id == "") ? "idclavecuenta" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT   `captacion_cuentas`.`numero_cuenta`,
         CONCAT(`captacion_cuentas`.`numero_cuenta`,' .- ', getFechaMX(`captacion_cuentas`.`fecha_apertura`),' Cred.-', `captacion_cuentas`.`numero_solicitud`) AS `cuenta` FROM     `captacion_cuentas` WHERE    ( `captacion_cuentas`.`tipo_subproducto` = " . CAPTACION_PRODUCTO_GARANTIALIQ . ") AND ( `captacion_cuentas`.`numero_socio` = $persona )";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		
		$xS->setLabel("TR.CUENTAS");
		$xS->setEsSql();
		if($selected > 0){	$xS->setOptionSelect($selected);	}
		return $xS;
	}
	function getListaDeCuentasCaptaPers($id = "", $selected = false, $persona = false){
		$selected	= setNoMenorQueCero($selected);
		$persona	= setNoMenorQueCero($persona);
		
		$id			= ($id == "") ? "idclavecuenta" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT   `captacion_cuentas`.`numero_cuenta` AS `cuenta`,
         CONCAT(`captacion_cuentas`.`numero_cuenta`, ' - ', `captacion_cuentas`.`alias`,' - ', `captacion_subproductos`.`descripcion_subproductos`, ' - ', `captacion_cuentas`.`fecha_apertura`) AS `descripcion`
		FROM `captacion_cuentas` INNER JOIN `captacion_subproductos`  ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`.`idcaptacion_subproductos` WHERE    ( `captacion_cuentas`.`numero_socio` = $persona ) LIMIT 0,100";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		
		$xS->setLabel("TR.CUENTAS");
		$xS->setEsSql();
		if($selected > 0){	$xS->setOptionSelect($selected);	}
		return $xS;
	}
	function getListaDeContratosPorPers($id = "", $selected = false, $persona = false){
		$selected	= setNoMenorQueCero($selected);
		$persona	= setNoMenorQueCero($persona);
		
		$id			= ($id == "") ? "idcontrato" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `documento`, CONCAT(`documento`, ' .- ', `descripcion`) FROM `vw_doctos_info` WHERE `persona`= $persona LIMIT 0,100 ";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		
		$xS->setLabel("TR.CONTRATOS");
		$xS->setEsSql();
		if($selected > 0){	$xS->setOptionSelect($selected);	}
		return $xS;
	}
	function getListaDeProductosDeCredito($id = "", $selected = false, $SoloActivos = false){
		$id		= ($id == "") ? "idproducto" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$ByActivos	= ($SoloActivos == false) ? "": " AND (`estatus` != 'baja') ";
		$sqlSc		= "SELECT `idcreditos_tipoconvenio`, CONCAT(`descripcion_tipoconvenio`,' .- ',`idcreditos_tipoconvenio`, '') AS `descripcion` 
					FROM `creditos_tipoconvenio` WHERE	(`idcreditos_tipoconvenio` !=99) $ByActivos ORDER BY `descripcion_tipoconvenio` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Producto de Credito");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeProductosDeCreditoNomina($id = "", $selected = false){
		$id		= ($id == "") ? "idproducto" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc		= "SELECT `idcreditos_tipoconvenio`, CONCAT(`descripcion_tipoconvenio`,' .- ',`idcreditos_tipoconvenio`, '') AS `descripcion`
					FROM `creditos_tipoconvenio` WHERE	(`idcreditos_tipoconvenio` !=99) AND `tipo_en_sistema`=" . SYS_PRODUCTO_NOMINA . " AND (`estatus` != 'baja') ORDER BY `descripcion_tipoconvenio` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Producto de Credito");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeProductosDeCreditoConSeguimiento($id = "", $selected = false){
		$id		= ($id == "") ? "idproducto" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc		= "SELECT `idcreditos_tipoconvenio`, `descripcion_tipoconvenio` FROM `creditos_tipoconvenio` WHERE	(`idcreditos_tipoconvenio` !=99) AND `omitir_seguimiento`=0 ";
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
		$xS->setLabel("TR.ESTATUS de Credito");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18");
		
		
		return $xS;
	}
	function getListaDeEmpresas($id = "", $omitirDefault = false, $empresa = false){
		$id			= ($id == "") ? "idcodigodeempresas" : $id; $this->mLIDs[]	= $id;
		$NoDef		= ($omitirDefault == false) ? "" : " WHERE (`idsocios_aeconomica_dependencias` !=" . FALLBACK_CLAVE_EMPRESA . ") ";
		
		$sqlSc		= "SELECT `idsocios_aeconomica_dependencias`, 
				CONCAT(`nombre_corto`, ' - ',`descripcion_dependencia`) AS `descripcion_dependencia`
				 FROM `socios_aeconomica_dependencias` $NoDef ORDER BY `nombre_corto`, `descripcion_dependencia`";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.Empresa");
		if($empresa !== false){ 
			$xS->setOptionSelect($empresa); 
		} else {
			$xS->setOptionSelect(DEFAULT_EMPRESA);
		}
		return $xS;
	}
	function getListaDeEmpresasConCreditosActivos($id = "", $omitirDefault = false, $empresa = false){
		$id			= ($id == "") ? "idcodigodeempresas" : $id; $this->mLIDs[]	= $id;
		
		$sqlSc		= "SELECT `idsocios_aeconomica_dependencias`,
				CONCAT(`nombre_corto`, ' - ',`descripcion_dependencia`, '-ID-',`idsocios_aeconomica_dependencias`, '-', COUNT( `creditos_solicitud`.`numero_solicitud` ) ) AS `descripcion_dependencia`
				FROM     `creditos_solicitud` INNER JOIN `socios_aeconomica_dependencias`  ON `creditos_solicitud`.`persona_asociada` = `socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` 
			WHERE    (`idsocios_aeconomica_dependencias` !=" . FALLBACK_CLAVE_EMPRESA . ") AND ( `creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . " )
		GROUP BY persona_asociada
		ORDER BY `nombre_corto`, `descripcion_dependencia`";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.Empresa");
		if($empresa !== false){
			$xS->setOptionSelect($empresa);
		} else {
			$xS->setOptionSelect(DEFAULT_EMPRESA);
		}
		return $xS;
	}
	function getListaDeCajasLocales($id = "", $soloSucursal = false, $selected = false){
		$id		= ($id == "") ? "idcajalocal" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$soloSucursal	= ($soloSucursal == false) ? "" : " AND sucursal = '" . getSucursal() . "' ";
		$sqlSc		= "SELECT * FROM socios_cajalocal WHERE idsocios_cajalocal !=99 $soloSucursal ORDER BY descripcion_cajalocal";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.CAJA_LOCAL");
		if($selected > 0){ 
			$xS->setOptionSelect($selected); 
		} else {
			$xS->setOptionSelect(getCajaLocal());
		}
	
		return $xS;
	}

	function getListaDeTiposDeIngresoDePersonas($id = "", $tipo = SYS_TODAS, $defaultOpt = DEFAULT_TIPO_INGRESO){
		$id		= ($id == "") ? "idtipodeingreso" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_tipoingreso WHERE estado=1 ";
		$sqlSc		.= ($tipo == SYS_TODAS) ? "" : " AND (tipo_de_persona=0 OR tipo_de_persona=$tipo) ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 green");
		$xS->setLabel("TR.TIPO_DE PERSONA");
		$xS->setOptionSelect( $defaultOpt );
		return $xS;
	}
	function getListaDeFigurasJuridicas($id = "", $tipo = SYS_TODAS, $selected = false){
		$id		= ($id == "") ? "idfigurajuridica" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_figura_juridica  WHERE	`activo`=1 ";
		$sqlSc		.= ($tipo == SYS_TODAS) ? "" : "`tipo_de_integracion` = $tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.FIGURA_JURIDICA");
		$xS->setDivClass("tx4 tx18 green");
		
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}		
		return $xS;
	}
	function getListaDeRegimenesFiscales($id = "", $tipo = SYS_TODAS, $selected = false){
		$id		= ($id == "") ? "idregimenfiscal" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM personas_regimen_fiscal ";
		$sqlSc		.= ($tipo == SYS_TODAS) ? "" : " WHERE	`tipo_de_persona` = $tipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){
			$xS->setOptionSelect($selected);
		} else {		
			$xS->setOptionSelect(PERSONAS_FISCAL_NINGUNO);
		}
		$xS->setLabel("TR.REGIMEN_FISCAL");
		return $xS;
	}	
	function getListaDeTipoDeIdentificacion($id = "", $tipo = PERSONAS_ES_FISICA, $selected = false){
		$id		= ($id == "") ? "idtipoidentificacion" : $id; $this->mLIDs[]	= $id;
		$tipo	= ($tipo == PERSONAS_ES_FISICA) ? BASE_DOCTOS_PERSONAS_FISICAS : BASE_DOCTOS_PERSONAS_MORALES;

		$sqlSc		=  "SELECT `clave_de_control`, `nombre_del_documento` FROM personas_documentacion_tipos";
		
		if($tipo == BASE_DOCTOS_PERSONAS_FISICAS){
			$sqlSc	.= " WHERE (`personas_documentacion_tipos`.`clasificacion` ='IP')";
		}
		// OR (`personas_documentacion_tipos`.`clasificacion` ='DG')
		if($tipo == BASE_DOCTOS_PERSONAS_MORALES){
			$sqlSc	.= " WHERE (`personas_documentacion_tipos`.`clasificacion` ='IPM') ";
		}		
		
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}		
		$xS->setLabel("TR.Tipo de Identificacion");
		$xS->setOptionSelect(2201);
				
		return $xS;
	}
	function getListaDeGeneros($id = "", $selected = false){
		$id		= ($id == "") ? "idgenero" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_genero";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.GENERO");
		$xS->setDivClass("tx4 tx18");
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}		
		return $xS;
	}
	function getListaDeEntidadesFed($id = "", $numerico = false, $estado = false){
		$xLoc		= new cLocal();
		$id			= ($id == "") ? "identidadfederativa" : $id; $this->mLIDs[]	= $id;
		$mid		= ($numerico == false) ? " `general_estados`.`clave_alfanumerica` " : " `general_estados`.`clave_numerica` ";
		$estado		= (setNoMenorQueCero($estado) <= 0) ? $xLoc->DomicilioEstadoClaveNum() : $estado;
		$sel		= ($numerico == false) ? strtoupper($xLoc->DomicilioEstadoClaveABC()) : $estado; 
		$sqlSc		= "SELECT $mid, `general_estados`.`nombre` FROM	`general_estados`";
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
		if($this->mDivClass !== ""){ $xS->setDivClass("tx4 tx18 green"); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeNacionalidad($id = "", $pais = EACP_CLAVE_DE_PAIS){
		$id		= ($id == "") ? "idnacionalidad" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT clave_de_control, gentilicio FROM personas_domicilios_paises ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.NACIONALIDAD");
		$xS->setOptionSelect($pais);
		$xS->setEsSql();
		return $xS;
	}
	
	
	function getListaDeRegionDePersonas($id = "", $selected = false){
		$id			= ($id == "") ? "idregionpersona" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM socios_region";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}
		$xS->setEsSql();
		$xS->setLabel("TR.REGION");
		return $xS;
	}	
	function getListaDeEstadoCivil($id = "", $selected = false){
		$id		= ($id == "") ? "idestadocivil" : $id; $this->mLIDs[]	= $id;
		$sqlSc	= "SELECT * FROM socios_estadocivil";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.ESTADO_CIVIL");
		$xS->setDivClass("tx4 tx18");
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}
		return $xS;
	}
	function getListaDeEstadoDePersonas($id = "", $selected = false){
		$id		= ($id == "") ? "idestadoactual" : $id; $this->mLIDs[]	= $id;
		$sqlSc	= "SELECT * FROM socios_estatus";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}		
		$xS->setEsSql();
		$xS->setLabel("TR.ESTADO_ACTUAL");
		$xS->setDivClass("tx4 tx18");
		return $xS;
	}	
	function getListaDeRegimenMatrimonio($id = "", $selected = ""){
		$id		= ($id == "") ? "idregimenmatrimonial" : $id; $this->mLIDs[]	= $id;
		$arr	= array("SOCIEDAD_CONYUGAL"  => "SOCIEDAD CONYUGAL", "BIENES_SEPARADOS" => "BIENES SEPARADOS");
		//
		/*$sqlSc		= "SELECT * FROM socios_estadocivil";*/
		$xS 		= new cSelect($id, $id, "");
		$xS->addEspOption("NINGUNO" , "NINGUNO");
		$xS->addEspOption("SOCIEDAD_CONYUGAL"  , "SOCIEDAD CONYUGAL");
		$xS->addEspOption("BIENES_SEPARADOS" , "BIENES SEPARADOS");
		if($selected != ""){
			$xS->setOptionSelect(strtoupper($selected) );
		} else {
			$xS->setOptionSelect(strtoupper( SYS_NINGUNO) );
		}
		$xS->setDivClass("tx4 tx18");
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
	function getListaDePeriocidadDePago($id = "", $selected = false, $empresa = false){
		$id		= ($id == "") ? "idperiocidad" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$empresa	= setNoMenorQueCero($empresa);
		$sqlSc		= "SELECT `idcreditos_periocidadpagos`, `descripcion_periocidadpagos` FROM `creditos_periocidadpagos` WHERE (`idcreditos_periocidadpagos` !=99) AND (`estatusactivo`=1) ";
		if($empresa > 0){
			$sqlSc	= "SELECT   `creditos_periocidadpagos`.`idcreditos_periocidadpagos`,
         `creditos_periocidadpagos`.`descripcion_periocidadpagos`,
         COUNT(`creditos_solicitud`.`numero_solicitud`) AS `creds`
			FROM     `creditos_solicitud` 
			INNER JOIN `creditos_periocidadpagos`  ON `creditos_solicitud`.`periocidad_de_pago` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
			WHERE    ( `creditos_solicitud`.`persona_asociada` = $empresa ) AND ( `creditos_solicitud`.`saldo_actual` > 0.99 )
			GROUP BY `creditos_solicitud`.periocidad_de_pago";
		}
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Frecuencia de pagos");
		$xS->setDivClass("tx4 tx18 orange");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDePeriocidadDePagoNomina($id = "", $selected = false){
		$id		= ($id == "") ? "idperiocidad" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		
		$sqlSc		= "SELECT `idcreditos_periocidadpagos`, `descripcion_periocidadpagos` FROM `creditos_periocidadpagos` WHERE (`idcreditos_periocidadpagos` !=99) AND (`estatusactivo`=1) ";

		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Frecuencia de pagos");
		$xS->setDivClass("tx4 tx18 orange");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		$xS->setEliminarOption(360);
		$xS->setEliminarOption(365);
		$xS->setEliminarOption(1);
		$xS->setEliminarOption(60);
		$xS->setEliminarOption(90);
		$xS->setEliminarOption(120);
		$xS->setEliminarOption(180);
		return $xS;
	}
	function getListaDeTipoDePago($id = "", $select = false){
		$select	= setNoMenorQueCero($select);
		$select	= ($select <= 0) ? CREDITO_TIPO_PAGO_PERIODICO : $select;
		$id		= ($id == "") ? "idtipodepago" : $id; $this->mLIDs[]	= $id;
		$sqlSc	= " SELECT * FROM `creditos_tipo_de_pago` WHERE (`creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` !=99) ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo_de Parcialidad");
		$xS->setDivClass("tx4 tx18 green");
		$xS->setOptionSelect( $select );
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDiasDePagoCredito($id = "", $select = false){
		$select	= setNoMenorQueCero($select);
		$select	= ($select <= 0) ? 1 : $select;		
		$id		= ($id == "") ? "iddiasdepago" : $id; $this->mLIDs[]	= $id;
		$sqlSc	= " SELECT * FROM `creditos_dias_de_pago` WHERE `idcreditos_dias_de_pago` ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Forma de Dias de Pago");
		$xS->setOptionSelect( $select );
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposdeActividadEconomica($id = "", $select = false){
		$id			= ($id == "") ? "idactividad" : $id; $this->mLIDs[]	= $id;
		$select		= setNoMenorQueCero($select);
		$sqlSc		= " SELECT `clave_interna`,`nombre_de_la_actividad` FROM `personas_actividad_economica_tipos` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Sector_Economico");
		if($select > 0){ $xS->setOptionSelect($select); }
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeTiposDeRecibos($id = "", $selected = false){
		$id		= ($id == "") ? "idtipoderecibo" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc		= "SELECT	`operaciones_recibostipo`.`idoperaciones_recibostipo`,	`operaciones_recibostipo`.`descripcion_recibostipo` FROM `operaciones_recibostipo` `operaciones_recibostipo` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected > 0){	$xS->setOptionSelect($selected);	}
		$xS->setLabel("TR.Tipo de Recibo");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeRecibosContabilizables($id = "", $selected = false){
		$id		= ($id == "") ? "idtipoderecibo" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc		= "SELECT	`operaciones_recibostipo`.`idoperaciones_recibostipo`,	`operaciones_recibostipo`.`descripcion_recibostipo` 
				FROM `operaciones_recibostipo` WHERE (`tipo_poliza_generada` != " . FALLBACK_TIPO_DE_POLIZA . ")  ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected > 0){	$xS->setOptionSelect($selected);	}
		$xS->setLabel("TR.Tipo de Recibo");
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeTipoDeAutorizacion($id = "", $selected = false){
		$id			= ($id == "") ? "idtipodeautorizacion" : $id; $this->mLIDs[]	= $id;
		$Selected	= setNoMenorQueCero($selected);
		$sqlSc		= " SELECT * FROM `creditos_tipo_de_autorizacion` WHERE (`idcreditos_tipo_de_autorizacion` !=99) ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($Selected > 0){ $xS->setOptionSelect($selected); } else { $xS->setOptionSelect(SYS_UNO); }
		$xS->setDivClass("tx4 tx18 green");
		$xS->setLabel("TR.Tipo de Autorizacion");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDeRiesgoEnCreds($id = "", $selected = false){
		$id		= ($id == "") ? "idnivelderiesgo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT * FROM creditos_nivelesriesgo";
		$Selected	= setNoMenorQueCero($selected);
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Nivel de Riesgo");
		$xS->setDivClass("tx4 tx18 red");
		if($Selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDeRiesgoEnAML($id = "", $selected = ""){
		$id		= ($id == "") ? "idtipoderiesgoaml" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT `clave_de_control`,`nombre_del_riesgo` FROM `aml_risk_types` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Riesgo");
		if($selected != ""){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDeRiesgoEnAMLCAT($id = "", $selected = false, $tipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		$id			= ($id == "") ? "idtipoderiesgo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= ( $tipo > 0) ? "SELECT * FROM aml_risk_catalog WHERE tipo_de_riesgo=$tipo ORDER BY descripcion" : "SELECT * FROM aml_risk_catalog ORDER BY descripcion ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		if(setNoMenorQueCero($selected) > 0){
			$xS->setOptionSelect($selected);
		}
		$xS->setLabel("TR.TIPO_DE RIESGO");
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		return $xS;
	}
	function getListaDeOperacionesPorBase($base, $id = "", $base2 = false, $selected = false){
		$id			= ($id == "") ? "idtipodepago" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$ByBase		= " WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$base) ";
		$ByBase		.= (setNoMenorQueCero($base2) > 0) ? " OR (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$base2)" : "";
		$sqlSc		= "SELECT	`miembro`, `descripcion_operacion`
					FROM `operaciones_tipos` INNER JOIN `eacp_config_bases_de_integracion_miembros` `eacp_config_bases_de_integracion_miembros` 
					ON `operaciones_tipos`.`idoperaciones_tipos` = 	`eacp_config_bases_de_integracion_miembros`.`miembro` 
					$ByBase ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,`descripcion_operacion` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected > 0){
			$xS->setOptionSelect( $selected );
		}
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeBonificacionesCredito($id = "", $selected = false){
		$id			= ($id == "") ? "idtipodebonificacion" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$sqlSc		= "SELECT `idoperaciones_tipos`,`descripcion_operacion` FROM operaciones_tipos WHERE class_efectivo=8";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected > 0){
			$xS->setOptionSelect( $selected );
		}
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeNivelDeRiesgo($id = "", $selected = false){
		$id			= ($id == "") ? "idnivelderiesgo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM `entidad_niveles_de_riesgo`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected != false){ $xS->setOptionSelect($selected); }
		$xS->setLabel("TR.NIVEL DE RIESGO");
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 blue");
		return $xS;		
	}
	function getListaDeRiesgosDeProbabilidad($id = "", $selected = false){
		$id			= ($id == "") ? "idprobabilidad" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM `riesgos_probabilidad`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected != false){ $xS->setOptionSelect($selected); }
		$xS->setLabel("TR.PROBABILIDAD");
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 blue");
		return $xS;
	}
	function getListaDeRiesgosConsecuencias($id = "", $selected = false){
		$id			= ($id == "") ? "idconsecuencia" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM `riesgos_consecuencias`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected != false){ $xS->setOptionSelect($selected); }
		$xS->setLabel("TR.CONSECUENCIA");
		$xS->setDivClass("tx4 tx18 blue");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeNivelDeUsuario($id = "", $selected = false, $limit = false){
		$id			= ($id == "") ? "idniveldeusuario" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$limit		= setNoMenorQueCero($limit);
		$ByLim		= ($limit>0) ? " AND (`general_niveles`.`tipo_sistema`<$limit) " : "";
		$sqlSc		= "SELECT
				`general_niveles`.`idgeneral_niveles`,
				`general_niveles`.`descripcion_del_nivel` 
			FROM
				`general_niveles` `general_niveles` WHERE idgeneral_niveles != 99 $ByLim ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.PERFIL DE USUARIO");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeMenuParents($id = "", $selected = false){
		$id			= ($id == "") ? "idmenusuperior" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `idgeneral_menu`,`menu_title`  FROM `general_menu` WHERE `menu_type`='parent' ORDER BY `menu_parent`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.MENU SUPERIOR");
		if($selected != false){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeDestinosDeCredito($id = "", $selected = false){
		$selected	= setNoMenorQueCero($selected);
		$id		= ($id == "") ? "iddestinodecredito" : $id; $this->mLIDs[]	= $id;
		$sqlSc	= "SELECT `creditos_destinos`.`idcreditos_destinos`, CONCAT(`creditos_destinos`.`descripcion_destinos`,'-',(`creditos_destinos`.`tasa_de_iva`*100), '%') AS 'destino'
					FROM `creditos_destinos` `creditos_destinos`  WHERE (`creditos_destinos`.`idcreditos_destinos` !=" . FALLBACK_CRED_TIPO_DESTINO . ") AND `estatusactivo`=1 ORDER BY `descripcion_destinos` ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.CLAVE DE DESTINO");
		
		if($selected >0){	$xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;		
	}
	function getListaDeTiempo($id = ""){
		$id		= ($id == "") ? "idtiempo" : $id; $this->mLIDs[]	= $id;
		$sqlSc	= "SELECT idsocios_tiempo, descripcion_tiempo FROM socios_tiempo ORDER BY idsocios_tiempo ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tiempo");
		if($this->mDivClass !== ""){ $xS->setDivClass("tx4 tx18 blue"); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeRegimenDeVivienda($id = "", $select	= DEFAULT_PERSONAS_REGIMEN_VIV){
		$id		= ($id == "") ? "idregimendevivienda" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_regimenvivienda, descipcion_regimenvivienda FROM socios_regimenvivienda ORDER BY descipcion_regimenvivienda ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Regimen de Vivienda");
		if($this->mDivClass !== ""){ $xS->setDivClass("tx4 tx18 green"); }
		$xS->setOptionSelect($select);
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
	function getListaDeTiposDeVivienda($id = "", $select = DEFAULT_TIPO_DOMICILIO){
		$id		= ($id == "") ? "idtipodevivienda" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_viviendatipo, descripcion_viviendatipo FROM socios_viviendatipo ORDER BY descripcion_viviendatipo ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Vivienda");
		$xS->setOptionSelect($select);
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeRelaciones($id = "", $tipo = "", $selected =false, $activos = false){
		$id		= ($id == "") ? "idtipoderelacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_relacionestipos, descripcion_relacionestipos FROM socios_relacionestipos ";
		$sqlSc		.= ($tipo == "") ? "" : " WHERE subclasificacion=$tipo ";
		if($tipo == ""){
			$sqlSc	.= ($activos == true) ? " WHERE `mostrar` = 1 " : "";
		} else {
			$sqlSc	.= ($activos == true) ? " AND `mostrar` = 1 " : "";
		}
		$selected	= setNoMenorQueCero($selected);
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de relacion");
		if($this->mDivClass !== ""){ $xS->setDivClass("tx4 tx18 green"); }
		if($selected > 0){$xS->setOptionSelect($selected);}
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeRelaciones2($id = "", $selected =false, $EsFisica = true){
		
		//BASE_ES_PERSONA_MORAL
		$ByTags		= ($EsFisica == true) ? "pf" : "pm";
		$ByTags		= " AND (`tags` LIKE '%$ByTags%' OR `tags` LIKE '%" . SYS_TODAS . "%') ";
		$id			= ($id == "") ? "idtipoderelacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_relacionestipos, descripcion_relacionestipos FROM socios_relacionestipos WHERE `mostrar` = 1 $ByTags ";
		
		$selected	= setNoMenorQueCero($selected);
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo_de relacion");
		if($this->mDivClass !== ""){ $xS->setDivClass("tx4 orange"); }
		
		if($selected > 0){$xS->setOptionSelect($selected);}
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeParentesco($id = "", $tipo = "", $selected = false){
		$id		= ($id == "") ? "idtipodeparentesco" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idsocios_consanguinidad, descripcion_consanguinidad FROM socios_consanguinidad ORDER BY `descripcion_consanguinidad` ";
		$selected	= setNoMenorQueCero($selected);
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($this->mDivClass !== ""){ $xS->setDivClass("tx4 tx18 green"); }
		$xS->setLabel("TR.Parentesco");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeOficiales($id = "", $tipo = "", $selected = false){
		$id			= ($id == "") ? "idoficial" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT id, nombre_completo FROM oficiales";
		if($tipo == SYS_UNO OR $tipo == SYS_USER_ESTADO_ACTIVO){
			$sqlSc	= "SELECT `id`, `nombre_completo` FROM oficiales WHERE `estatus`='". SYS_USER_ESTADO_ACTIVO . "' AND `idrol`>=" . USUARIO_TIPO_OFICIAL_CRED;
		}
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Oficial");
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeUsuarios($id = "", $selected = false, $activos = true ){
		$id			= ($id == "") ? "idusuario" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= ($activos == true) ? "SELECT `idusuarios`, `nombrecompleto` FROM `usuarios` WHERE `estatus`= '" . SYS_USER_ESTADO_ACTIVO . "'" : "SELECT `idusuarios`, `nombrecompleto` FROM `usuarios`"; 
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Usuario");
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		return $xS;
	}
	
	function getListaDeCajasAbiertas($id = "", $tipo = "", $fecha = false){
		$id		= ($id == "") ? "idcaja" : $id; $this->mLIDs[]	= $id;
		$xli		= new cSQLListas();
		$usr		= false;
		if(getUsuarioActual(SYS_USER_NIVEL) <= USUARIO_TIPO_CAJERO){
			$usr	= getUsuarioActual();
		}
		$sqlSc		= $xli->getListadoDeCajasConUsuario(TESORERIA_CAJA_ABIERTA, $fecha, $usr);
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

	function getListaDeTiposDeMemoPersonas($id = "", $selected = false){
		$id			= ($id == "") ? "idtipodememo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= " SELECT * FROM socios_memotipos WHERE tipo_memo!=99 ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected !== false){
			$xS->setOptionSelect($selected);
		}
		$xS->setLabel("TR.Tipo de Memo");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDePolizas($id = "", $omitirDefault = true, $selected = 0){
		$id			= ($id == "") ? "idtipodepoliza" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$omitirDefault	= ($omitirDefault == true ) ?  " WHERE `idcontable_polizadiarios` != 999 " : "";
		$sqlSc		= " SELECT * FROM `contable_polizasdiarios` $omitirDefault ORDER BY `idcontable_polizadiarios` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected > 0){
			$xS->setOptionSelect($selected);
		}
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
		
		$xS->setEsSql();
		return $xS;		
	}
	function getListaDeCatalogoGenerico($buscador , $id = "", $selected = null){
		$id			= ($id == "") ? "id$buscador" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `clave`,`descripcion`  FROM `sistema_catalogo` WHERE `tabla_virtual` = '$buscador' LIMIT 0,20";
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($selected !== null){
			$xS->setOptionSelect($selected);
		}
		$xS->setEsSql();
		return $xS;
	}

	function getListaDeTipoDeCaptacion($id = "", $select = 0){
		$sqlSc		= "SELECT * FROM `captacion_cuentastipos` ";
		$select		= setNoMenorQueCero($select);
		$id			= ($id == "") ? "idtipodecuenta" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Cuenta");
		if($select>0){$xS->setOptionSelect($select);}
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeProductosDeCaptacion($id = "", $select = 0, $tipo = 0){
		$id			= ($id == "") ? "idproductocaptacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT idcaptacion_subproductos, descripcion_subproductos FROM captacion_subproductos WHERE idcaptacion_subproductos != 99 AND (`estatus`=1)";
		if($tipo > 0){
			$sqlSc	= "SELECT idcaptacion_subproductos, descripcion_subproductos FROM captacion_subproductos WHERE idcaptacion_subproductos != 99 AND (`estatus`=1) AND (`captacion_subproductos`.`tipo_de_cuenta`=$tipo) ";
		}
		$xS 		= new cSelect($id, $id, $sqlSc);
		$select		= setNoMenorQueCero($select);
		if($select>0){$xS->setOptionSelect($select);}
		$xS->setLabel("TR.Producto de Captacion");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeOrigenDeCaptacion($id = "", $select = 0){
		$id		= ($id == "") ? "idorigencaptacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM captacion_cuentasorigen";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Origen de cuenta");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeOrigenDeFlujoEfvo($id = "", $select = 0){
		$id		= ($id == "") ? "idorigenflujo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `idcreditos_origenflujo`,`descripcion_origenflujo` FROM `creditos_origenflujo` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setOptionSelect($select);
		$xS->setLabel("TR.ORIGEN");
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTituloDeCaptacion($id = "", $select = 0){
		$id		= ($id == "") ? "idtitulocaptacion" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT * FROM captacion_tipotitulo";
		$select		= setNoMenorQueCero($select);
		$xS 		= new cSelect($id, $id, $sqlSc);
		if($select>0){
			$xS->setOptionSelect($select);
		}
		$xS->setLabel("TR.TIPO_DE TITULO");
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
		$xS->setDivClass("tx4 tx18");
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

	function getListaDeFormaReportaRiesgo($id ="", $selected = false){
		$id			= ($id == "") ? "idformadereportar" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `eq_aml`,`nombre_reporte` FROM `riesgos_reporte`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql(); //C Calificado I Inmediato
		$xS->setDivClass("tx4 tx18 orange");
		if($selected !== false){ $xS->setOptionSelect($selected); }
		$xS->setLabel("TR.TIPO_DE REPORTE");
		return $xS;
	}
	function getListaDeUnidadMedidaAML($id = "", $selected = false){
		$id			= ($id == "") ? "idunidadmedida" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `eq_aml`,`nombre_medida` FROM `riesgos_medidas`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setDivClass("tx4 tx18 orange");
		$xS->setEsSql();
		if($selected !== false){ $xS->setOptionSelect($selected); }
		$xS->setLabel("TR.TIPO_DE MEDIDA");
		return $xS;
	}	
	function getListaDeFrecuenciaChequeoRiesgo($id = "", $selected = false){
		$id			= ($id == "") ? "idfrecuenciadechequeo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `eq_aml`,`nombre_chequeo` FROM `riesgos_chequeo`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql(); //C Calificado I Inmediato D Diario
		$xS->setDivClass("tx4 tx18 orange");
		if($selected !== false){ $xS->setOptionSelect($selected); }
		$xS->setLabel("TR.TIPO_DE CHEQUEO");
		return $xS;	
	}
	function getListaDeFrecuenciaFlujoEfvo($id = "", $selected = false){
		$id			= ($id == "") ? "idfrecuenciaflujo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `idcreditos_periocidadflujo`,`descripcion_periocidadflujo` FROM `creditos_periocidadflujo`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql(); //C Calificado I Inmediato D Diario
		$xS->setDivClass("tx4 tx18 orange");
		if($selected !== false){ $xS->setOptionSelect($selected); }
		$xS->setLabel("TR.FRECUENCIA");
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
	function getListaDeMeses($id = "", $select = false){
		$id			= ($id == "") ? "idnumerodemes" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xF 		= new cFecha (0);
		$select		= setNoMenorQueCero($select);
		$select		= ($select == 0) ? date("m") : $select;
		foreach ( $xF->getMesesInArray () as $key => $value ) {
			$xS->addEspOption($key , "$key - $value");
		}
		$xS->setOptionSelect($select);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 blue");
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
	function getListaDeTiposDeCuentasCaptacion($id = "", $select = false){
		$id		= ($id == "") ? "idtipodecuentacaptacion" : $id; $this->mLIDs[]	= $id;
		$cCta 	= new cSelect($id, $id, "SELECT * FROM `captacion_cuentastipos`");
		$cCta->setEsSql();
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
		$cCta->setLabel("TR.Estado por Movimientos");
		
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
		$xS->setLabel("TR.Centro_de_Costo");
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
	function getListaDeRiesgosAML($id = "", $tipo = false, $selected = false){
		return $this->getListaDeTipoDeRiesgoEnAMLCAT($id, $selected, $tipo);
	}

	function getListaDePeriodosDeCredito($id = "", $fecha = false, $selected = false){
		$xF			= new cFecha();
		$ByFecha	= "";
		if($fecha != false){
			$ByFecha	= " AND (`creditos_periodos`.`fecha_inicial` <= '$fecha') AND (`creditos_periodos`.`fecha_final` > '$fecha') ";
		}
		$id			= ($id == "") ? "idperiododecredito" : $id; $this->mLIDs[]	= $id;
		$sql	= "SELECT
		`creditos_periodos`.`idcreditos_periodos`  AS `clave`,
		CONCAT(`creditos_periodos`.`descripcion_periodos`, ' -- ' , getFechaMX(`creditos_periodos`.`fecha_inicial`), ' . ', getFechaMX(`creditos_periodos`.`fecha_final`))
		AS `descripcion`
		FROM
		`creditos_periodos` `creditos_periodos` 
			INNER JOIN `usuarios` `usuarios` 
			ON `creditos_periodos`.`periodo_responsable` = `usuarios`.`idusuarios`
			WHERE	`creditos_periodos`.`idcreditos_periodos` > 0 $ByFecha";
		$xS 		= new cSelect($id, $id, $sql);
		$xS->setEsSql();
		if(setNoMenorQueCero($selected) > 0){
			$xS->setOptionSelect($selected);
		}
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		return $xS;
	}
	function getListaDeTiposDePatrimonioPersonal($id = "", $tipo = "", $selected = false){
		$id			= ($id == "") ? "idtipodepatrimonio" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= ( setNoMenorQueCero($tipo) > 0) ? "" : "SELECT	`socios_patrimoniotipo`.`idsocios_patrimoniotipo`,	`socios_patrimoniotipo`.`descripcion_patrimoniotipo` FROM `socios_patrimoniotipo` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo de Patrimonio");
		$xS->setEsSql();
		if(setNoMenorQueCero($selected) > 0){
			$xS->setOptionSelect($selected);
		}
		return $xS;
	}
	function getListaDeEstadosDePatrimonioPersonal($id = "", $selected = false){
		$id			= ($id == "") ? "idestadodepatrimonio" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT	`socios_patrimonioestatus`.`idsocios_patrimonioestatus`,	`socios_patrimonioestatus`.`descripcion_patrimonioestatus` FROM `socios_patrimonioestatus` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Estatus del Patrimonio");
		$xS->setEsSql();
		if(setNoMenorQueCero($selected) > 0){
			$xS->setOptionSelect($selected);
		}
		return $xS;
	}

	function getListaDeHoras($id = "", $selected = false, $soloEnteros = false){
		$mHours = array (
				"6:00" => "6:00 AM",
				"7:00" => "7:00 AM",
				"8:00" => "8:00 AM",
				"8:30" => "8:30 AM",
				"9:00" => "9:00 AM",
				"9:30" => "9:30 AM",
				"10:00" => "10:00 AM",
				"10:30" => "10:30 AM",
				"11:00" => "11:00 AM",
				"11:30" => "11:30 AM",
				"12:00" => "12:00 PM",
				"12:30" => "12:30 PM",
				"13:00" => "1:00 PM",
				"13:30" => "1:30 PM",
				"14:00" => "2:00 PM",
				"14:30" => "2:30 PM",
				"15:00" => "3:00 PM",
				"15:30" => "3:30 PM",
				"16:00" => "4:00 PM",
				"16:30" => "4:30 PM",
				"17:00" => "5:00 PM",
				"17:30" => "5:30 PM",
				"18:00" => "6:00 PM",
				"18:30" => "6:30 PM",
				"19:00" => "7:00 PM",
				"20:00" => "8:00 PM",
				"21:00" => "9:00 PM",
				"22:00" => "10:00 PM",
				"23:00" => "11:00 PM",
				"24:00" => "0:00"
		);
		if($soloEnteros == true){
			$mHours = array (
					"6" => "6:00 AM",
					"7" => "7:00 AM",
					"8" => "8:00 AM",
					"9" => "9:00 AM",
					"10" => "10:00 AM",
					"11" => "11:00 AM",
					"12" => "12:00 PM",
					"13" => "1:00 PM",
					"14" => "2:00 PM",
					"15" => "3:00 PM",
					"16" => "4:00 PM",
					"17" => "5:00 PM",
					"18" => "6:00 PM",
					"19" => "7:00 PM",
					"20" => "8:00 PM",
					"21" => "9:00 PM",
					"22" => "10:00 PM",
					"23" => "11:00 PM",
					"24" => "0:00"
			);
		}
		$id			= ($id == "") ? "idhora" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		foreach ($mHours as $valor => $name){
			$xS->addEspOption($valor, $name);
		}	

		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Horario");
		return $xS;			
	}
	function getListaDeDiasDeLaSemana($id = "", $selected = false){
		$xF		= new cFecha();
		$mHours = $xF->getDiasDeSemanaInArray();
		$id			= ($id == "") ? "iddiasemana" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		foreach ($mHours as $valor => $name){
			$idx	= setNoMenorQueCero($valor);
			$xS->addEspOption($idx, $name);
		}
		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.DIA_DE_LA_SEMANA");
		return $xS;
	}	
	function getListaDeEstadoDeLlamada($id = "", $selected = false){
		$id			= ($id == "") ? "idestadodellamada" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$xS->addEspOption(SEGUIMIENTO_ESTADO_PENDIENTE,  $xL->getT("TR.pendiente") );
		$xS->addEspOption(SEGUIMIENTO_ESTADO_EFECTUADO,  $xL->getT("TR.efectuado") );
		$xS->addEspOption(SEGUIMIENTO_ESTADO_CANCELADO, $xL->getT("TR.cancelado") );
		$xS->addEspOption(SEGUIMIENTO_ESTADO_VENCIDO, $xL->getT("TR.vencido") );
		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Estatus de llamada");
		return $xS;
	}	
	function getListaDeTiposDeCompromisos($id = "", $selected = false){
		$id			= ($id == "") ? "idtipodecompromiso" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$xS->addEspOption("promesa_de_pago",  $xL->getT("TR.promesa_de_pago") );
		$xS->addEspOption("promesa_de_revision",  $xL->getT("TR.promesa_de_revision") );
		$xS->addEspOption("promesa_de_reestructuracion",  $xL->getT("TR.promesa_de_reestructuracion") );
		$xS->addEspOption("promesa_de_renovacion",  $xL->getT("TR.promesa_de_renovacion") );
		$xS->addEspOption("promesa_de_visita",  $xL->getT("TR.promesa_de_visita") );

		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Tipo de Compromiso");
		return $xS;
	}
	function getListaDeTiposDeCompromisosLugares($id = "", $selected = false){
		$id			= ($id == "") ? "idlugardecompromiso" : $id; $this->mLIDs[]	= $id;
		$SqlRpt		= "SELECT * FROM `seguimiento_lugar_de_compromiso` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.LUGAR");
		
		return $xS;
	}
	function getListaDeTelefonosPorPersona($persona, $id = "", $selected = false){
		$id			= ($id == "") ? "idtelefono" : $id; $this->mLIDs[]	= $id;
		$xQL		= new MQL();
		$xLi		= new cSQLListas();
		
		$persona	= setNoMenorQueCero($persona);
		$sqlSc		= $xLi->getListadoDeTelefonosPorPersona($persona);
		$xS 		= new cSelect($id, $id, "");

		$rs			= $xQL->getDataRecord($sqlSc);
		foreach ($rs as $rw){
			$telefono	= setNoMenorQueCero($rw["telefono"]);
			if($telefono > 0){
				$xS->addEspOption($telefono,  $rw["descripcion"] );
			}
		}
		if(setNoMenorQueCero($selected) > 0){
			$xS->setOptionSelect($selected);
		}
		$xS->setEsSql();
		$xS->setLabel("TR.Telefono");
		//$xS->addEspOption(SYS_TODAS);
		//$xS->setOptionSelect(SYS_TODAS);
		return $xS;
	}

	function getListaDePersonasConPresupuesto($id = "", $selected = false, $persona_adicional = false){
		$id			= ($id == "") ? "idcodigodeproveedor" : $id; $this->mLIDs[]	= $id;
		$xLi		= new cSQLListas();
		$sqlSc		= $xLi->getListadoDePersonasConPresupuesto($persona_adicional);
		$xS 		= new cSelect($id, $id, $sqlSc);
		if(setNoMenorQueCero($selected) > 0){		$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Proveedor");

		return $xS;
	}
	function getListaDePresupuestoPorPersona($id = "", $selected = false, $persona = false, $estado = false){
		$id			= ($id == "") ? "idpresupuesto" : $id; $this->mLIDs[]	= $id;
		$xLi		= new cSQLListas();
		$ByEstat	= "";
		if($estado !== false){
			$estado		= setNoMenorQueCero($estado);
			$ByEstat	= " AND (`estado_actual`= $estado) ";
		}
		$sqlSc		= "SELECT `clave_de_presupuesto`, CONCAT(`clave_de_presupuesto`, '-', getFechaMX(`fecha_de_elaboracion`), '-', `total_presupuesto`, '-', `notas`) AS 'descripcion' 
					FROM `creditos_presupuestos` WHERE `clave_de_persona`=$persona $ByEstat ";
		
		$xS 		= new cSelect($id, $id, $sqlSc);
		if(setNoMenorQueCero($selected) > 0){		$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Presupuesto");
		
		return $xS;
	}
	function getListaDeTiposDeOperacionesBancarias($id = "", $selected = false){
		$id			= ($id == "") ? "idtipooperacionbanco" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$xS->addEspOption( BANCOS_OPERACION_CHEQUE,  $xL->getT("TR.Cheque") );
		$xS->addEspOption( BANCOS_OPERACION_DEPOSITO,  $xL->getT("TR.Deposito_bancario") );
		$xS->addEspOption( BANCOS_OPERACION_COMISION,  $xL->getT("TR.Comision") );
		$xS->addEspOption( BANCOS_OPERACION_RETIRO,  $xL->getT("TR.Retiro") );
		$xS->addEspOption( BANCOS_OPERACION_TRASPASO,  $xL->getT("TR.Traspaso") );
	
		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Tipo de Operacion");
		return $xS;
	}
	function getListaDePersonasConPagosPorPresupuesto($id = "", $selected = false){
		$id			= ($id == "") ? "idcodigodeproveedor" : $id; $this->mLIDs[]	= $id;
		$xLi		= new cSQLListas();
		$sqlSc		= $xLi->getListadoDePersonasConPresupuestoPorPagar();
		$xS 		= new cSelect($id, $id, $sqlSc);
		if(setNoMenorQueCero($selected) > 0){		$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Proveedor");
	
		return $xS;
	}

	function getListaDeTipoDePagoTesoreria($id = "", $tipo = false, $selected = ""){
		$id			= ($id == "") ? "idtipodepagotesoreria" : $id; $this->mLIDs[]	= $id;
		$xLi		= new cSQLListas();
		$sqlSc		= "";
		if($tipo == TESORERIA_TIPO_EGRESOS){
			$sqlSc	= "SELECT `tipo_de_pago`, `descripcion` FROM `tesoreria_tipos_de_pago` WHERE (`tipo_de_movimiento` = -1 OR `tipo_de_movimiento` = 0)";
		} else if($tipo == TESORERIA_TIPO_INGRESOS){
			$sqlSc	= "SELECT `tipo_de_pago`, `descripcion` FROM `tesoreria_tipos_de_pago` WHERE (`tipo_de_movimiento` = 1 OR `tipo_de_movimiento` = 0)";
		} else {
			$sqlSc	= "SELECT `tipo_de_pago`, `descripcion` FROM `tesoreria_tipos_de_pago` ";
		}
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setNoMayus();
		if($selected != ""){ $xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Tipo de Pago");
	
		return $xS;		
	}
	function getListaDeTiposDeAfectacionOperaciones($id = "", $selected = false){
		$id			= ($id == "") ? "idtipodeafectacion" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$xS->addEspOption( "1",  $xL->getT("TR.Ingreso") );
		$xS->addEspOption( "0",  $xL->getT("TR.Ninguno") );
		$xS->addEspOption( "-1",  $xL->getT("TR.Egreso") );
		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setDivClass("tx4 tx18");
		$xS->setEsSql();
		$xS->setLabel("TR.Tipo de Asiento");
		return $xS;
	}
	function getListaDeTipoDeRedondeo($id = "", $selected = 0){
		$id			= ($id == "") ? "idredondeo" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$xS->addEspOption( "50",  $xL->getT("TR.Redondeo 0.50") );
		$xS->addEspOption( "100",  $xL->getT("TR.Redondeo 1.00") );
		$xS->addEspOption( "0",  $xL->getT("TR.Ninguno") );
	
		$xS->setOptionSelect($selected);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 orange");
		$xS->setLabel("TR.Tipo_de Redondeo");
		return $xS;
	}
	function getListaDeTipoDeDispersion($id = "", $select = false){
		$id		= ($id == "") ? "idtipodispersion" : $id; $this->mLIDs[]	= $id;
		$select	= setNoMenorQueCero($select);
		$sqlSc	= "SELECT * FROM `catalogos_tipo_de_dispersion` ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo_de dispersion");
		if($select > 0){ $xS->setOptionSelect($select); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTipoDeDispersionCreditos($id = "", $select = false){
		$id		= ($id == "") ? "idtipodispersion" : $id; $this->mLIDs[]	= $id;
		$select	= setNoMenorQueCero($select);
		$sqlSc	= "SELECT * FROM `creditos_tipo_de_dispersion` ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.Tipo_de desembolso");
		$xS->setDivClass("tx4 tx18 orange");
		if($select > 0){ $xS->setOptionSelect($select); }
		$xS->setEsSql();
		return $xS;
	}	
	function getListaDeTipoDeLugarDeCobro($id = "", $select = false){
		$id		= ($id == "") ? "idtipolugarcobro" : $id; $this->mLIDs[]	= $id;
		$select	= setNoMenorQueCero($select);
		$sqlSc	= "SELECT * FROM `catalogos_tipo_de_dispersion` ";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$xS->setLabel("TR.TIPO_DE Cobro");
		$xS->setDivClass("tx4 tx18 blue");
		if($select > 0){ $xS->setOptionSelect($select); }
		$xS->setEsSql();
		return $xS;
	}
	function getListaDeTiposDeOperacion($id = "", $selected = SYS_TODAS, $base = false, $TipoRecibo=false, $SoloActivos = false){
		$id			= ($id == "") ? "idtipodeoperacion" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$base		= setNoMenorQueCero($base);
		$TipoRecibo	= setNoMenorQueCero($TipoRecibo);
		$ByActivos	= ($SoloActivos == false) ? "" : " AND (`estatus`=1) ";
		$ByTipoRec	= ($TipoRecibo > 0) ? " AND (`recibo_que_afecta`=$TipoRecibo) " : "";
		$sqlSc		= "SELECT * FROM operaciones_tipos WHERE `idoperaciones_tipos`>0 $ByTipoRec $ByActivos ORDER BY `descripcion_operacion` ";
		$xS 		= new cSelect($id, $id, $sqlSc); $xS->setEsSql(); $xS->setLabel("TR.Tipo de Operacion");
		if($selected > 0){ $xS->setOptionSelect($selected); }
		return $xS;
	}

	function getListaDeTiposDeOperacionContable($id = "", $selected = 0){
		$id			= ($id == "") ? "idredondeo" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$xS->addEspOption( TM_ABONO,  $xL->getT("TR.ABONOS") );
		$xS->addEspOption( TM_CARGO,  $xL->getT("TR.CARGOS") );
	
		if($selected != 0){	$xS->setOptionSelect($selected);	}
		$xS->setEsSql();
		$xS->setLabel("TR.Tipo de Asiento");
		return $xS;
	}	

	function getListaDeGruposSolidarios($id = "", $selected = false){
		$id		= ($id == "") ? "idgrupo" : $id; $this->mLIDs[]	= $id;
		$sqlSc	= "SELECT `idsocios_grupossolidarios`, `nombre_gruposolidario` FROM `socios_grupossolidarios`";
		$xS 	= new cSelect($id, $id, $sqlSc);
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}
		$xS->setEsSql();
		$xS->setLabel("TR.GRUPO_SOLIDARIO");
		return $xS;
	}
	function getListaDeSiNo($id = "", $selected = false){
		$id			= ($id == "") ? "idobjetosderiesgo" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$xS->addEspOption("0" , $xL->get("NO"));
		$xS->addEspOption("1" , $xL->get("SI"));
		if($selected !== false){
			$xS->setOptionSelect($selected);
		}
		$xS->setEsSql();
		//$xS->setLabel("TR.Origen de Riesgo");
		return $xS;
	}
	function getListaDeTiposDeAcceso($id = "", $selected = "calle"){
		$id			= ($id == "") ? "idtipoacceso" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		//$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
		$xS->addEspOption("calle" , $xL->get("CALLE"));
		$xS->addEspOption("avenida" , $xL->get("AVENIDA"));
		$xS->addEspOption("andador" , $xL->get("ANDADOR"));
		$xS->addEspOption("camino_rural" , $xL->get("CAMINO_RURAL"));
		$xS->setOptionSelect($selected);
		$xS->setEsSql();
		$xS->setDivClass("tx14 tx18");
		$xS->setLabel("TR.TIPO_DE_ACCESO");
		return $xS;
	}
	function getListaDeCanalesDeNotificacion($id = "", $selected = "personal"){
		$id			= ($id == "") ? "idtipocanal" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		//$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
		$xS->addEspOption("sms" , $xL->get("SMS"));
		$xS->addEspOption("personal" , $xL->get("PERSONAL"));
		$xS->addEspOption("email" , $xL->get("CORREO_ELECTRONICO"));
		$xS->setOptionSelect($selected);
		$xS->setEsSql();
		$xS->setLabel("TR.TIPO_DE_CANAL");
		return $xS;
	}
	function getListaDeFormatos($id = "", $selected = false, $tipo = SYS_TODAS){
		$id		= ($id == "") ? "idcontrato" : $id; $this->mLIDs[]	= $id;
		$tipo	= setNoMenorQueCero($tipo);
		$ByTipo	= "";
		if($tipo > 0){
			$ByTipo	= " AND `tipo_contrato`=$tipo ";
		}
		$sqlSc	= "SELECT idgeneral_contratos, CONCAT(titulo_del_contrato, '-', idgeneral_contratos) AS 'titulo_del_contrato' FROM general_contratos WHERE `estatus` !='baja' $ByTipo ORDER BY titulo_del_contrato";
		
		
		$xS 	= new cSelect($id, $id, $sqlSc);
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}
		$xS->setEsSql();
		$xS->setLabel("TR.FORMATO");
		return $xS;		
	}
	function getListaDePersonasXClass($id = "", $select = false){
		$id			= ($id == "") ? "idxclasificacion" : $id; $this->mLIDs[]	= $id;
		$select		= setNoMenorQueCero($select);
		$sqlSc		= "SELECT * FROM `personas_xclasificacion` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.XCLASIFICACION");
		if($select > 0){ $xS->setOptionSelect($select); }
		return $xS;
		
	}
	function getListaDePersonasYClass($id = "", $select = false){
		$id			= ($id == "") ? "idyclasificacion" : $id; $this->mLIDs[]	= $id;
		$select		= setNoMenorQueCero($select);
		$sqlSc		= "SELECT * FROM `personas_yclasificacion` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.YCLASIFICACION");
		if($select > 0){ $xS->setOptionSelect($select); }
		return $xS;
	
	}
	function getListaDePersonasZClass($id = "", $select = false){
		$id			= ($id == "") ? "idzclasificacion" : $id; $this->mLIDs[]	= $id;
		$select		= setNoMenorQueCero($select);
		$sqlSc		= "SELECT * FROM `personas_zclasificacion` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.ZCLASIFICACION");
		if($select > 0){ $xS->setOptionSelect($select); }
		return $xS;	
	}

	function getListaDeDiasDelMes($id = "", $selected = false){
		$xF			= new cFecha();
		$id			= ($id == "") ? "iddiames" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		$tit		= $xL->getTrad("DIA");
		for($i = 1; $i<=31;$i++){
			$xS->addEspOption($i, "$tit $i");
		}
		if($selected != false){	$xS->setOptionSelect($selected);	}
		$xS->setDivClass("tx4 tx18 green");
		$xS->setEsSql();
		$xS->setLabel("TR.DIA_MES");
		return $xS;
	}	
	function getListaDePersonasMembresia($id = "", $select = false){
		$id			= ($id == "") ? "idtipomembresia" : $id; $this->mLIDs[]	= $id;
		$select		= setNoMenorQueCero($select);
		$sqlSc		= "SELECT * FROM `personas_membresia_tipo` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->setLabel("TR.TIPO_MEMBRESIA");
		if($select > 0){ $xS->setOptionSelect($select); }
		return $xS;
	
	}	
	function getListaDeReportes($id = "", $tipo = "" ){
		$id			= ($id == "") ? "idreporte" : $id; $this->mLIDs[]	= $id;
		$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT * FROM general_reports $w ORDER BY `order_index` ASC,`idgeneral_reports` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setLabel("TR.REPORTE");
		return $xS;
	
	}	
	function getListaDeCreditosPorPersona($persona, $id, $select = false){
		$xQL		= new MQL();
		$xLi		= new cSQLListas();
		$persona	= setNoMenorQueCero($persona);
		$sql 		= $xLi->getListadoDeCreditos($persona);
		$rs			= $xQL->getDataRecord($sql);
				
		$id			= ($id == "") ? "idcreditospersona" : $id; $this->mLIDs[]	= $id;
		$select		= setNoMenorQueCero($select);
		$xS 		= new cSelect($id, $id, "");
		$xS->setEsSql();
		foreach ($rs as $row){
			$xS->addEspOption($row["credito"], $row["credito"] . "-" . $row["producto"] . "-" . $row["periocidad"] . "-" . $row["saldo"]);
		}
		$rs			= null;
		if($select > 0){ $xS->setOptionSelect($select); }
		return $xS;		
	}
	function getListaDeAhorroPorPersona($persona, $producto = false,$id = "", $select = false){
		$xQL		= new MQL();
		$xLi		= new cSQLListas();
		$persona	= setNoMenorQueCero($persona);
		$sql 		= $xLi->getListadoDeCuentasDeCapt($persona, false, false, $producto);
		$rs			= $xQL->getDataRecord($sql);

		$id			= ($id == "") ? "idcuentaspersona" : $id; $this->mLIDs[]	= $id;
		$select		= setNoMenorQueCero($select);
		$xS 		= new cSelect($id, $id, "");
		$xS->setEsSql();
		foreach ($rs as $row){
			$xS->addEspOption($row["cuenta"], $row["cuenta"] . "-" . $row["subproducto"] . "-" . $row["tasa"] . "-" . $row["saldo"]);
		}
		$rs			= null;
		if($select > 0){ $xS->setOptionSelect($select); }
		return $xS;		
	}

	function getListaDeOtrosDatosEnProdDeCred($id = "", $selected = false){
		$id			= ($id == "") ? "idotrosdatos" : $id; $this->mLIDs[]	= $id;
		$selected	= setNoMenorQueCero($selected);
		$xS 		= new cSelect($id, $id, "");
		$xObj		= new cProductoDeCreditoOtrosDatosCatalogo();
		$xS->setLabel("TR.CLAVE DE OTROS DATOS");
		//if($selected > 0){ $xS->setOptionSelect($selected); }
		$xS->setEsSql();
		$D			= $xObj->getCatalogoInArray();
		foreach ($D as $k => $v){
			$xS->addEspOption($k, $v);
		}
		$D			= null;
		$xObj		= null;
		return $xS;
	}
	/**
	 * Lista de Recibos por Periodo de Documento y Credito, segun operaciones
	 * @param int $credito Credito a Elegir
	 * @param int $periodo Periodo a Elegir
	 * @param string $id
	 * @param string $selected 
	 * @return cSelect
	 */
	function getListaDeRecibosDePago_PCO($credito, $periodo, $id = "", $selected = false){
		$id			= ($id == "") ? "idrecibos-$credito-$periodo" : $id; $this->mLIDs[]	= $id;
		$sqlSc		= "SELECT `operaciones_mvtos`.`recibo_afectado`, CONCAT(`operaciones_mvtos`.`recibo_afectado`,  ' - ', getFechaMX(`operaciones_mvtos`.`fecha_afectacion`),  ' - ', `operaciones_recibos`.`total_operacion`  ) AS `descripcion` 
FROM `operaciones_mvtos` `operaciones_mvtos` INNER JOIN `operaciones_recibos` `operaciones_recibos` ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.`idoperaciones_recibos` 
WHERE	(`operaciones_recibos`.`tipo_docto` =2) AND (`operaciones_mvtos`.`docto_afectado` =$credito) AND(`operaciones_mvtos`.`periodo_socio` =$periodo) 	
GROUP BY `operaciones_mvtos`.`recibo_afectado`";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$selected	= setNoMenorQueCero($selected);
		if($selected > 0){$xS->setOptionSelect($selected);}
		$xS->setEsSql();
		$xS->setLabel("TR.RECIBOS");
		return $xS;
	}
	function getListaDeVehiculosUsos($id = "", $selected = 0 ){
		$id			= ($id == "") ? "idusovehiculo" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt		= "SELECT * FROM `vehiculos_usos` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.TIPOUSO");
		$xS->setDivClass("tx4 tx18");
		
		return $xS;
	}
	function getListaDeVehiculosPorCreds($id = "", $selected = 0, $credito = false ){
		$credito	= setNoMenorQueCero($credito);
		
		$id			= ($id == "") ? "idvehiculo" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt		= "SELECT `idleasing_activos`,`descripcion` FROM `leasing_activos` WHERE `idleasing_activos`>0";
		if($credito > DEFAULT_CREDITO){
			$SqlRpt	.= " AND (`credito`=$credito) ";
		}
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);		
		$xS->setLabel("TR.IDVEHICULO");
		//$xS->setDivClass("tx4 tx18");
		
		return $xS;
	}
	function getListaDeOriginadoresTipos($id = "", $selected = 0 ){
		$id			= ($id == "") ? "idtipooriginador" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT * FROM `leasing_originadores_tipos` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setDivClass("tx4 tx18 green");
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.TIPO_DE ORIGINADOR");
		return $xS;
	}
	function getListaDeVehiculosMarcas($id = "", $selected = 0){
		$id			= ($id == "") ? "idmarca" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT * FROM `vehiculos_marcas` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setDivClass("tx4 tx18");
		$xS->setLabel("TR.MARCA");
		return $xS;
	}
	function getListaDeVehiculosSegmentos($id = "", $selected = 0){
		$id			= ($id == "") ? "idsegmento" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT * FROM `vehiculos_segmento` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setDivClass("tx4 tx18");
		$xS->setLabel("TR.SEGMENTO");
		return $xS;
	}
	function getListaDeVehiculosGPS($id = "", $selected = 0){
		$id			= ($id == "") ? "idsegmento" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT * FROM `vehiculos_gps` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setDivClass("tx4 tx18");
		$xS->setLabel("TR.TIPO_DE GPS");
		return $xS;
	}
	function getListaDeOriginadores($id = "", $selected = 0){
		$id			= ($id == "") ? "idoriginador" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT `idleasing_originadores`,`nombre_originador` FROM `leasing_originadores` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.ORIGINADOR");
		return $xS;
	}
	function getListaDeSubOriginadores($id = "", $selected = 0, $originador = 0){
		$id			= ($id == "") ? "idsuboriginador" : $id; $this->mLIDs[]	= $id;
		$originador	= setNoMenorQueCero($originador);
		
		$w			= ($originador <=0 ) ? "" : " WHERE originador='$originador' ";
		$SqlRpt			= "SELECT `idleasing_usuarios`,`nombre` FROM `leasing_usuarios` $w";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.SUBORIGINADOR");
		return $xS;
	}
	function getListaDeLeasingRAC($id = "", $selected = 0){
		$id			= ($id == "") ? "idtiporac" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT * FROM `leasing_tipo_rac` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.TIPO_DE RAC");
		$xS->setDivClass("tx4 tx18");
		return $xS;
	}
	function getListaDeLeasingEscenarios($id = "", $selected = 0){
		$id			= ($id == "") ? "idtipoescenario" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		//$SqlRpt			= "SELECT `idleasing_escenarios`,`descripcion_escenario` FROM `leasing_escenarios` ";
		$SqlRpt			= "SELECT `plazo`,`descripcion_escenario` FROM `leasing_escenarios` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 orange");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.PLAZO");
		return $xS;
	}

	function getListaDeLeasingLimInf($id = "", $selected = 0){
		$id			= ($id == "") ? "idplazomin" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT (`plazo`+1) AS `id`, `plazo` AS `nombre`  FROM `leasing_escenarios` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->addEspOption("0", "0");
		$xS->setDivClass("tx4 tx18");
		$xS->setLabel("TR.LIMITEINFERIOR");
		return $xS;
	}
	function getListaDeLeasingLimSup($id = "", $selected = 0){
		$id			= ($id == "") ? "idplazomax" : $id; $this->mLIDs[]	= $id;
		//$w			= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt			= "SELECT `plazo`, `plazo` FROM `leasing_escenarios` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setDivClass("tx4 tx18");
//		$xS->addEspOption("0", "0");
		$xS->setLabel("TR.LIMITESUPERIOR");
		return $xS;
	}
	function getListaDeLeasingTipoCom($id = "", $selected = 0){
		$id			= ($id == "") ? "idtipocomision" : $id; $this->mLIDs[]	= $id;
		//$w		= ($tipo == "") ? "" : "WHERE aplica='$tipo'";
		$SqlRpt		= "SELECT * FROM `leasing_tipo_comision`";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.TIPO_DE COMISION");
		return $xS;
	}
	function getListaDeInstrumentosFin($id = "", $selected = 0){
		$id			= ($id == "") ? "idinstrumento" : $id; $this->mLIDs[]	= $id;

		$SqlRpt		= "SELECT * FROM `aml_instrumentos_financieros`";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.TIPO_DE INSTRUMENTO_FINANCIERO");
		return $xS;
	}
	function getListaDeTiposObjetoSistema($id = "", $selected = 0){
		$id			= ($id == "") ? "idobjeto" : $id; $this->mLIDs[]	= $id;
		$xS 		= new cSelect($id, $id, "");
		$xL			= new cLang();
		
		$xS->addEspOption(iDE_AML , $xL->get("PLD"));
		$xS->addEspOption(iDE_AVAL , $xL->get("AVALES"));
		$xS->addEspOption(iDE_BANCOS , $xL->get("BANCOS"));
		$xS->addEspOption(iDE_CAPTACION , $xL->get("CAPTACION"));
		$xS->addEspOption(iDE_CINVERSION , $xL->get("CUENTAS_DE_INVERSION"));
		$xS->addEspOption(iDE_CVISTA, $xL->get("A_LA_VISTA"));
		$xS->addEspOption(iDE_CONTABLE , $xL->get("CONTABLE"));
		$xS->addEspOption(iDE_CPOLIZA , $xL->get("POLIZA_CONTABLE"));
		$xS->addEspOption(iDE_CREDITO , $xL->get("CREDITO"));
		$xS->addEspOption(iDE_EMPRESA , $xL->get("EMPRESA"));
		$xS->addEspOption(iDE_GARANTIA , $xL->get("GARANTIAS"));
		$xS->addEspOption(iDE_GRUPO , $xL->get("GRUPOS_SOLIDARIOS"));
		$xS->addEspOption(iDE_SEGUIMIENTO , $xL->get("SEGUIMIENTO"));
		
		$xS->setOptionSelect($selected);
		$xS->setEsSql();
		$xS->setLabel("TR.TIPO_DE OBJETO");
		return $xS;
	}

	function getListaDeDomicilioPorPers($persona, $id="", $selected = 0){
		$id			= ($id == "") ? "idvivienda" : $id; $this->mLIDs[]	= $id;

		$SqlRpt		= "SELECT `idsocios_vivienda` AS `clave`,CONCAT(UCASE(`tipo_de_acceso`), ' ',`calle`, ', No. ',`numero_exterior`,', ', `colonia`,', ',`localidad`, ', ', `estado`)
		AS `domicilio` FROM `socios_vivienda` WHERE `socio_numero`=$persona";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.LISTADO DE VIVIENDA");
		return $xS;
	}
	function getListaDeTipoDeValuacionGar($id = "", $selected = 0){
		$id			= ($id == "") ? "idtipovaluacion" : $id; $this->mLIDs[]	= $id;
		$SqlRpt		= "SELECT * FROM `creditos_tvaluacion`";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.TIPO_DE VALUACION");
		return $xS;
	}
	function getListaDeProveedores($id = "", $selected = 0){
		$id			= ($id == "") ? "idproveedor" : $id; $this->mLIDs[]	= $id;
		$SqlRpt		= "SELECT `personas_proveedores`.`persona`, `personas`.`nombre` FROM     `personas` INNER JOIN `personas_proveedores`  ON `personas`.`codigo` = `personas_proveedores`.`persona`";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		//$xS->setDivClass("tx4 tx18 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.PROVEEDOR");
		return $xS;
	}
	function getListaDeAseguradoras($id = "", $selected = 0){
		$id			= ($id == "") ? "idaseguradora" : $id; $this->mLIDs[]	= $id;
		$SqlRpt		= "SELECT   `personas_aseguradoras`.`persona`, `personas_aseguradoras`.`alias` FROM     `personas_aseguradoras` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.ASEGURADORA");
		return $xS;
	}
	function getListaDeTipoDeRechazoCred($id = "", $selected = 0){
		$id			= ($id == "") ? "idtiporechazo" : $id; $this->mLIDs[]	= $id;
		$SqlRpt		= "SELECT `idcreditos_rechazos_tipo`,`descripcion` FROM `creditos_rechazos_tipo`";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 tx18 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.MOTIVORECHAZO");
		return $xS;
	}
	function getListaDeCausaMoraCred($id = "", $selected = 0){
		$id			= ($id == "") ? "idtipodecausa" : $id; $this->mLIDs[]	= $id;
		$SqlRpt		= "SELECT   `creditos_causa_de_vencimientos`.`idcreditos_causa_de_vencimientos`,`creditos_causa_de_vencimientos`.`descripcion_de_la_causa` FROM `creditos_causa_de_vencimientos` ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.CAUSAMORA");
		return $xS;
	}
	function getListaDeMunicipiosAct($id = "", $selected = 0){
		$id			= ($id == "") ? "idmunicipioactivo" : $id; $this->mLIDs[]	= $id;
		$SqlRpt		= "SELECT  getIDUMun(`tmp_personas_domicilios`.`identidadfed`, `tmp_personas_domicilios`.`idmunicipio`) AS `clave`,
			CONCAT(getIDUMun(`tmp_personas_domicilios`.`identidadfed`, `tmp_personas_domicilios`.`idmunicipio`),' -',`general_municipios`.`nombre_del_municipio`) AS `descripcion`         
			FROM `tmp_personas_domicilios`, `general_municipios`  
			WHERE `tmp_personas_domicilios`.`idmunicipio` = `general_municipios`.`clave_de_municipio`  AND `tmp_personas_domicilios`.`identidadfed` = `general_municipios`.`clave_de_entidad` 
			GROUP BY getIDUMun(`tmp_personas_domicilios`.`identidadfed`, `tmp_personas_domicilios`.`idmunicipio`) ";
		$xS 		= new cSelect($id, $id, $SqlRpt);
		$xS->setEsSql();
		$xS->setDivClass("tx4 green");
		$selected	= setNoMenorQueCero($selected);
		$xS->setOptionSelect($selected);
		$xS->setLabel("TR.MUNICIPIO");
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
	private $mCaption		= "";
	private $mBody			= "";
	private $mFoot			= "";
	private $mHead			= "";
	private $mOut			= SYS_DEFAULT;
	private $mClass			= "";
	function __construct($id = "", $class=""){
		$this->mId			= $id;
		$this->mClass		= $class;
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
		$cssT		= ($this->mClass == "") ? "" : " class='"  . $this->mClass . "' ";
		$inittags	= "<table$id>";
		$endtags	= "</table>";
		$caption	= ($this->mCaption == "") ? "" : "<caption>" . $this->mCaption . "</caption>";
		if($this->mOut == OUT_EXCEL){
			$inittags	= "<tbody>";
			$endtags	= "</tbody>";
			$cssT		="";
		}
		return "<table$id$cssT>" . $caption . "<thead> ". $this->mHead . "</thead><tbody id=\"tb-" . $this->mId . "\">". $this->mHtml . "</tbody><tfoot><tr>" . $this->mFoot . "</tr></tfoot></table>";
	}
	function initRow($class="", $props = ""){
		$css		= ($class == "") ? "" : " class='$class' ";
		$props		= ($props == "") ? "" : " $props ";
		$this->mHtml .= "<tr$css$props>";
		
	}
	function addTD($html, $props = ""){ 
		$this->mHtml .= "<td$props>$html</td>";
	}
	function addFootTD($html, $props = "", $NewLine = false){
		$this->mFoot .= "<td$props>$html</td>";
		if($NewLine == true){
			$this->mFoot .= "</tr></tr>";
		}
	}	
	
	function addTDM($html, $props = ""){
		$props	= $props . " class='mny' ";
		$this->addTD($html, $props);
	}
	function addTH($text){ $xL	= new cLang(); $text	= $xL->getT($text); $this->mHead .= "<th>$text</th>"; 	}
	function endRow(){
		$this->mHtml .= "</tr>\r\n"; 	
	}
	function addRaw($html){ $this->mHtml .= $html; }
	function setOut($out){ $this->mOut = $out; }
	function setTitle($title){ $this->mCaption = $title; }
	//function getHTML(){ return $this->mHtml; }
	//function setCleanHTML(){ $this->mHtml = ""; }
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
	private $mDateMin		= "";
	private $mDateMax		= "";
	private $mEsParaReport	= false;
	
	function __construct($Index = 0, $Fecha = false, $TipoDeFecha = false){
		$this->mFecha 		= ($Fecha == false) ? fechasys() : $Fecha;
		$this->mIndex		= $Index;
		$this->mTipoFecha	= ($TipoDeFecha == false) ? FECHA_TIPO_OPERATIVA : $TipoDeFecha;
	}
	function setIsSelect($select = true){	$this->mSelects		= $select;	}
	function setID($id){ $this->mId	= $id; }
	function addEvents($events){ $this->mEvents .= $events; }
	function setDivClass($class){ $this->mClassDiv = $class; }
	function setDateMax($f){ $this->mDateMax = $f; }
	function setDateMin($f){ $this->mDateMin = $f; }
	function setEsOperativa(){ $this->mTipoFecha = FECHA_TIPO_OPERATIVA;  }
	function setEsDeNacimiento(){ $this->mTipoFecha = FECHA_TIPO_NACIMIENTO; }
	function setEsDeReporte(){ $this->mEsParaReport	= true; }
	/**
	 * @param string $label Texto de la Etiqueta
	 * @param string $Fecha Fecha del Control en formato ISO
	 * @param mixed $indice Numero o ID del COntrol
	 * @param bool $close Cerrar False/true
	 * @return string Control en HTML
	 */
	function get($label = "", $Fecha = false, $indice	= false, $close = true){
		$xTr			= new cLang();
		$label			= $xTr->getT($label);
		if($indice !== false){
			if(strlen($indice) > 2 AND setNoMenorQueCero($indice) <= 0){
				$this->setID($indice);
			}  else {
				$this->mIndex	= $indice;
			}
		}
		if($close == false){ $this->setDivClass(""); }
		$extraDate		= "";
		$label			= ($label == "") ? "" : "<label for='idfecha-" . $this->mIndex . "'>$label</label>" ;
		$this->set($Fecha);
		$xF				= new cFecha($this->mIndex, $this->mFecha);
		$events			= $this->mEvents;
		$xF->init(FECHA_FORMATO_MX);
		$id				= ($this->mId == "") ? "idfecha-" . $this->mIndex . "" : $this->mId;
		if( $this->mTipoFecha == FECHA_TIPO_NACIMIENTO ){
			
			$anno		= $xF->anno() - 18;
			
			$xF->set("$anno-" . $xF->mes() . "-" . $xF->dia() );
			
			$this->set($xF->get());
			
			$msd		= EDAD_PRODUCTIVA_MAXIMA - 18;
			$extraDate	= ",selectYears: true,selectYears: " . $msd . " ";
			$this->mDateMax = fechasys();
			if(PERSONAS_ACEPTAR_MENORES == false){
				$this->mDateMax = $xF->setRestarMeses((18*12), fechasys());
				$msd		= EDAD_PRODUCTIVA_MAXIMA;
				$extraDate	= ",selectYears: true,selectYears: " . $msd . " ";
			}
		}
		if($this->mEsParaReport == true){
			$extraDate	.= ",selectYears: true";
		}
		if($this->mDateMax !== ""){
			$extraDate	.= ",max: new Date('" . $this->mDateMax . "')";
		}
		if($this->mDateMin !== ""){
			$extraDate	.= ",min: new Date('" . $this->mDateMin . "')";
		}
		
		$txt			= "<input type=\"text\" id=\"$id\" value=\"" . $xF->get() . "\" name=\"$id\" $events><script>$(\"#$id\").pickadate({format: '" . SYS_FORMATO_FECHA . "',formatSubmit:'yyyy-mm-dd', editable : true $extraDate});</script> ";
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
	protected $mOLang			= null;
	protected $mOIcons			= null;
	protected $mCSSLabel		= "";
	protected $mNoCleanProps	= false;
	function __construct($id = "", $value = "", $label = ""){
		$this->mId				= $id;
		$this->mValue			= $value;
		$this->mLbl				= $label;
	}
	function setReDraw($id, $value = "", $label = ""){
		$this->mId				= $id;
		$this->mValue			= $value;
		$this->mLbl				= $label;

	}	
	function addEvent($event, $OnEvent = "onclick", $strParams = ""){
		if(trim($event) != ""){
		if ( strpos($event, ")") > 0){
			$this->mArrEvents[$OnEvent]	= "$event";
		} else {
			$this->mArrEvents[$OnEvent]	= "$event($strParams)";
			if ($strParams != ""){
				$this->mArrEventsVals[$OnEvent]	= $strParams;
			}
		}
		}
	}
	
	function addHTMLCode($str){	$this->mHTMLExtra		.= $str;	}
	function setIncludeLabel( $inc = true ){
		$this->mIncLabel	= $inc;
	}
	function set($id, $value = "", $label = ""){
		$this->mId	= $id;
		$this->mValue	= $value;
		$this->mLbl	= $label;
	}
	function setSize($size){ $this->mArrProp["size"]	= $size;	}
	function setType($type = ""){ $this->mArrProp["type"]	= $type; }
	function addDataX($prop = "", $value = ""){ $this->mArrProp["data-$prop"]	= $value; }
	function setProperty($property, $value){ $this->mArrProp[$property]	= $value;	}
	function getProperty(){	}
	function setDropProperty($property){ unset($this->mArrProp[$property]);	}
	function get( $id = "", $value = false, $label = "", $titulo = "", $arrEvents = false, $snipHTML = "" ){
		$this->mId			= ( $id != "") ? $id : $this->mId;
		$this->mValue		= ( $value !== false) ? $value : $this->mValue;
		$this->mLbl			= ( $label == "") ? $this->mLbl : $label;
		$this->mIncLabel	= ( strlen($this->mLbl) > 4 ) ? true : $this->mIncLabel;
		if($this->mLbl != ""){
			$this->mLbl		= $this->getOLang()->getT($this->mLbl);
		}
		$this->mTitle		= ( trim($titulo) == "") ? $this->mLbl : $titulo;
		if( is_array($arrEvents)){ array_merge($this->mArrEvents, $arrEvents);  }
		if( !isset($this->mArrProp["maxlength"]) AND isset($this->mArrProp["size"]) ){
			$this->mArrProp["maxlength"]	= $this->mArrProp["size"];
		}
		
		$this->mArrProp["name"] 	= $this->mId;
		if ( trim($this->mValue) != "" ){ $this->mArrProp["value"]	= $this->mValue; }
		$this->mType				= (!isset($this->mArrProp["type"])) ? $this->mType : $this->mArrProp["type"];
		$this->mArrProp["type"]		= $this->mType;
		
		$nEvents					= "";
		$nProps						= "";
		
		$otherStrings 				= $this->mHTMLExtra . $snipHTML;
		$csslbl						= ($this->mCSSLabel == "") ? "" : " class=\"" . $this->mCSSLabel . "\"";
		
		foreach ($this->mArrEvents as $key => $valE) {
			$nEvents	.= " $key=\"$valE\" ";
		}
		if($this->mType	== "button"){// OR $this->mType	== "submit"){
			unset($this->mArrProp["type"]);
			unset($this->mArrProp["title"]);
			unset($this->mArrProp["value"]);
			unset($this->mArrProp["name"]);
		} else {
			unset($this->mArrProp["title"]);
		}
		foreach( $this->mArrProp as $key => $valP ){ $nProps	.= " $key=\"$valP\" "; }
		//si existe la propiedad size tomarla para sumar, si no poner 50
		$lbl		= ($this->mIncLabel == true) ? "<label for=\"" . $this->mId . "\"$csslbl>" . $this->mLbl . "</label>"  : "";
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
		if($this->mNoCleanProps == false){
			$this->mArrProp			= array();
			$this->mHTMLExtra		= "";
		}
	}
	function setNoCleanProps(){ $this->mNoCleanProps = true; }
	function setClearHTML(){ $this->mHTMLExtra		= ""; } 
	function setLabelSize($tamanno){}
	function getOLang(){if($this->mOLang == null){$this->mOLang = new cLang(); } return $this->mOLang; }
	function lang($palabra, $palabra2 = ""){ return $this->getOLang()->getTrad($palabra, $palabra2); }
	function setDivClass($class){ $this->mDivClass = $class; }
	function setDiv13($add=""){ $this->mDivClass = "tx4 tx18$add";}
	function ic(){ $ic = new cFIcons(); return $ic;	}
	function setCSSLabel($css){ $this->mCSSLabel = $css; }
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
	public $ERROR	= "alert-error";
	public $SUCCESS	= "success";
	private $OIC	= null;
	private $mLang	= null;
	public $mNot1G	= "fa-1g";
	public $mNot2x	= "fa-2x";
	private $mIncX	= false;
	
	
	function __construct($tipo = "notice"){
		$this->mTipo	= $tipo;
	}
	function get($txt, $id = "", $tipo = false, $event=""){
		$this->mTipo	= ($tipo == false) ? $this->mTipo : $tipo;
		$xDiv	= new cHDiv("alert-box " . $this->mTipo , $id);
		$txt	= ($this->mIncX == true) ? "<span class='close-tip' onclick='var xG = new Gen(); xG.closeTip(this);$event'>x</span>" . $txt : $txt;
		$xDiv->addHElem($txt);
		return $xDiv->get();
	}
	function getTag($txt, $id = "", $tipo = false, $event=""){
		$this->mTipo	= ($tipo == false) ? $this->mTipo : $tipo;
		$xDiv	= new cHDiv("alert-box form_tag " . $this->mTipo . "" , $id);
		$xDiv->addHElem($txt);
		return $xDiv->get();
	}
	
	function setNoClose(){ $this->mIncX = true; }
	function getNoticon($valor ="",$evento ="", $icon = "aviso", $size = "fa-1g", $id = ""){
		$xBtn	= new cHButton();
		$id		= ($id == "") ? "noticon-". rand(0, 100) . "" : $id;
		$ic		= $xBtn->setIcon($icon, $size);
		$evt	= ($evento == "") ? "" : "onclick=\"$evento\"";
		$valor	= ($valor == "") ? "" : " <span class='noticount'>$valor</span>";
		return "<div id='spn-$id' class='noticon' $evt>$ic$valor</div>";
	}
	
	function ic(){
		if($this->OIC == null){ $this->OIC = new cFIcons();}
		return $this->OIC;
	}
	function getDash($titulo = "", $info = "", $icon = "fa-info", $tipo = "notice"){
		$xBtn	= new cHButton();
		$titulo	= $this->getL()->getT($titulo);
		$ic		= $xBtn->setIcon($icon, "fa-3x");
				
		return "<span class='tx4 dash dash-$tipo'>
			
			<div class='tx13'>$ic</div>
			
			<div class='tx23'><p class='dash-title'>$titulo</p><p class='dash-info'>$info</p></div>
			
			</span>";
	}
	function getL(){
		if($this->mLang == null){
			$this->mLang	= new cLang();
		}
		return $this->mLang;
	}
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
	private $mUsarNulls		= false;
	public $T_ELIMINAR		= 2;
	public $T_EDITAR		= 1;
	private $mChartType		= "bar"; 
	public $CHART_PIE		= "pie";
	private $mEsq			= array();
	private $mOmitidos		= array();
	private $mMicroformato	= "";
	private $mLimits		= 0;
	private $mPreSQL		= "";
	private $mNoFilas		= false;
	private $mOpTitleFoot	= "";
	private $mArrReplaces	= array();
	private $mNoFieldset	= false;
	private $mTHStyles		= array();
	private $mInDetails		= array(); //Detalles, elemento compacto
	private $mForceTypes	= array();	//Tipos Forzados
	
	private $mFechaCorte	= false;//Fecha de Corte SQL para reportes
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
		$this->arrWithTilde = array("varchar","date","text","tinytext","enum","blob","string","longtext","datetime", "time");

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
	function setPreSQL($sql){$this->mPreSQL=$sql;} 
	function setOmitidos($item){$this->mOmitidos[$item] = true;}
	function setResumidos($item){ $this->mInDetails[$item] = true; }
	function setFieldReplace($field, $mark ){ $this->mArrReplaces[$field] = $mark;  }
	function setForzarTipoSQL($campo, $tipo){
		$this->mForceTypes[$campo]	= $tipo;
	}
	function setNoFieldset(){ $this->mNoFieldset = true; }
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
	function setTitulo($campo, $titulo){  $this->mColTitles[$campo] = $titulo; }
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
		$id		= ($id == "") ? "id-" . HP_REPLACE_ID  : $id;
		$this->mEspCMD[]	= $xBtn->getBasic($titulo, $event, $icon, $id, false, true );	
	}
	function OCheckBox($event, $campo, $preid = "" ){
		$preid					= ($preid == "") ?  "chk"  . rand(0, 100) : $preid;
		$this->mEspTool[$campo]	= "<div class=\"coolCheck\"><input id=\"$preid-" . HP_REPLACE_ID . "\" " ."_COND_" . " onchange=\"$event\"  type=\"checkbox\"><label for=\"$preid-" . HP_REPLACE_ID . "\"></label></div>";
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
	function setKeyField($name = ""){ $this->mKeyField 	= $name; }
	/**
	 * Nombre de la Tabla Maestra
	 * @param string $table
	 */
	function setKeyTable($table = ""){	$this->mTbl		= $table;	}
	function setWithMetaData($data = true){$this->mDataCustom = $data; }
	function setColTitle($field, $title){
		$this->mColTitles[$field] = $title;
	}
	function setColStyle($field, $style){
		$this->mTHStyles[$field]	= $style;
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
		$xUsr		= new cSystemUser();
		
		$mEquiv		= $xQL->getTipos();
		$xIcs		= $this->ODicIcons();
		$this->mID	= ($TableID == "") ? $this->mID : $TableID;
		$oStr		= array("string" => "string", "varchar" => "varchar", "text" => "text", "tinytext" => "tinytext");
		$puedeEdit	= false;
		$puedeDel	= false;
		$puedeAdd	= false;
		$numreplace = (count($this->mArrReplaces)>0) ? true : false;
		
		if(MODO_DEBUG == true AND isset($_SERVER["REQUEST_URI"]) AND $this->mPrepareChart == false ){
			if(strpos($_SERVER["REQUEST_URI"], "rpt") === false AND strpos($_SERVER["REQUEST_URI"], "chart") === false){ 
				$this->addTool(3);
			}
		}
		/*
numerics 
-------------
BIT: 16
TINYINT: 1
BOOL: 1
SMALLINT: 2
MEDIUMINT: 9
INTEGER: 3
BIGINT: 8
SERIAL: 8
FLOAT: 4
DOUBLE: 5
DECIMAL: 246
NUMERIC: 246
FIXED: 246

dates
------------
DATE: 10
DATETIME: 12
TIMESTAMP: 7
TIME: 11
YEAR: 13

strings & binary
------------
CHAR: 254
VARCHAR: 253
ENUM: 254
SET: 254
BINARY: 254
VARBINARY: 253
TINYBLOB: 252

BLOB: 252
MEDIUMBLOB: 252
TINYTEXT: 252
TEXT: 252
MEDIUMTEXT: 252
LONGTEXT: 252 
*/
		$arrNums	= array(
				"252" => "TEXT",
				"253" => "VARCHAR",
				"254" => "VARCHAR",
				"10" => "DATE",
				"11" => "INT",
				"16" => "INT",
				"12" => "VARCHAR",
				"246" => "FLOAT",
				"5" => "FLOAT",
				"4" => "FLOAT",
				"1" => "INT",
				"2" => "INT",
				"3" => "INT",
				"7" => "INT",
				"8" => "INT",
				"9" => "INT",
				"13" => "IN",
				"" => "VARCHAR"
		);
				
		//titulos
		$arrTitles	= array(
				0 => $xLng->getT("TR.Ejecutar"),
				1 => $xLng->getT("TR.Editar"),
				2 => $xLng->getT("TR.Eliminar"),
				3 => $xLng->getT("TR.Ver")
		);
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
			//Limitar
			if($this->mLimits >0){
				if(strpos($this->mSql," LIMIT ") === false ){
					$this->mSql .= " LIMIT 0," . $this->mLimits . "";
				}
			}
			if($this->mTipoSalida == OUT_EXCEL){ $oldTags = true; }
			//si hay Caption formar el caption
			if($vCaption !="" AND $this->mPrepareChart == false){
				
				$vCaption 	= $xLng->getT($vCaption);
				if($this->mNoFieldset == false){
					$vCaption 	= "<legend>[&nbsp;&nbsp;&nbsp;&nbsp;$vCaption&nbsp;&nbsp;&nbsp;&nbsp;]</legend>";
					$pushInit 	= "<fieldset>$vCaption";
					$pushEnd	= "</fieldset>";
				}
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
			$itmEspTool				= count($this->mEspTool)*2;
			$itmTot					= $il + $itmCmd + $itmEspTool;
			if($itmTot > 0 AND $this->mPrepareChart == false ){
				$wd					= ( setNoMenorQueCero($this->mWidthTool) <=0 ) ? ($itmTot * 31) . "px" : $this->mWidthTool;
				$tht 				= "<th style='min-width:". $wd  . ";width:". $wd .";'>Acciones</th>";
			}
			// --------------------------------------------------------------
			if($this->mFechaCorte !== false){
				$this->mFechaCorte	= $xD->getFechaISO($this->mFechaCorte); 
				$xQL->setRawQuery("SET @fecha_de_corte:='" . $this->mFechaCorte . "';");
				$xQL->setRawQuery("SET @ejercicio:=" . $xD->anno($this->mFechaCorte) . ";");
			}
			if($this->mPreSQL !== ""){ $xQL->setRawQuery($this->mPreSQL); }
			$rs 					= $xQL->getRecordset($this->mSql);// getRecordset( $this->mSql );
			$items					= 0;
			$DatosKey				= array();
			$CamposOmi				= array();
			$CountOmitidos			= 0;
			//============================= Formato de Columnas y otras propiedades
			if($rs){ //=== IF RS 
				while($obj	= $rs->fetch_field()){
					if(!isset($this->mOmitidos[$obj->name])){
					$this->mEsq[$obj->name]["N"] 	= $obj->name;
					$this->mEsq[$obj->name]["TBL"] 	= $obj->table;
					$this->mEsq[$obj->name]["L"] 	= $obj->length;
					$ttipo							= (isset($arrNums[$obj->type])) ? $arrNums[$obj->type] : "STRING"; //INT VARCHAR
					$lttipo							= strtolower($ttipo);
					$this->mEsq[$obj->name]["T"] 	= $lttipo; //setLog($obj->name . "--" . $lttipo);
					if(isset($this->mForceTypes[$obj->name])){
						$this->mEsq[$obj->name]["T"] 	= $this->mForceTypes[$obj->name];
					}
					$this->mEsq[$obj->name]["V"] 	= $obj->def;
					$this->mEsq[$obj->name]["IDX"] 	= $items;
					$this->mEsq[$obj->name]["TPHP"] = $mEquiv[$ttipo];//tipo de numerico a mySQL a PHP
					$this->mEsq[$obj->name]["PHPNUM"] 	= ($this->arrTypesPHP[$lttipo] == "numeric") ? true : false;
					$this->mEsq[$obj->name]["TINY"] 	= (isset($this->mInDetails[$obj->name])) ? true : false;
					//setLog($this->mEsq[$obj->name]["T"]);
					//setLog($this->mEsq[$obj->name]["TPHP"]);
					if(is_numeric($this->mKey) ){
						if($this->mKey == $items){
							$this->mTbl			= ($this->mTbl =="") ? $obj->table : $this->mTbl;
							$this->mKeyField	= ($this->mKeyField == "") ? $obj->name : $this->mKeyField;
							$DatosKey[$items]	= $this->mEsq[$obj->name];
							$DatosKey[$obj->name]	= $this->mEsq[$obj->name];
						}
					}
					//Visualizar
					$items++;
					} else {
						//Generar Campos Omitidos
						$CamposOmi[$obj->name]	= $obj->name;
						$CountOmitidos++;
					}
				}
				unset($obj);
			}		//=== END IF RS
			//Arrays de Nombres y tipo
			$arrFieldNames			= array();
			$arrFieldTypes			= array();
			$preCols				= ( count($this->Fields) <= 1 ) ? false : true;

			//
			$ifl 					= $items - 1;			//Limite de Fields
			//------------------------------------------------------------- // Subquery
			$subHTML				= array();
			if($this->mSubsEnable	== true){
				
				foreach ($this->mSubSQLs as $sidx => $ssqls){
					$xQL->setConTitulos();
					
					$subquery	= $xQL->getDataRecord($ssqls);
					$STit		= $xQL->getTitulos();
					foreach($subquery as $srw){
						$subkey			= $srw[$sidx];
						$sshtml			= $this->mMicroformato;
						foreach ($STit as $sskey => $ssprops){
							//[^)]+
							$sshtml		= preg_replace("/\{\{$sskey\}\}/",$srw[$sskey],$sshtml);
						}
						//setLog("$subkey $sshtml");
						$subHTML[$subkey]	= (isset($subHTML[$subkey])) ? $subHTML[$subkey] . $sshtml : $sshtml;
						//setLog($subHTML[$subkey]);
					}
					$subquery	= null;
				}
			}
			//-------------------------------------------------------------	//Cabeceras de Columnas
			foreach ($this->mEsq as $nombre => $col){
			//for($iCols=0; $iCols<=$ifl; $iCols++){
				$fname 				= $col["N"];
				$ftypes				= $col["T"];
				$index				= $col["IDX"];
				$this->Fields[]		= $nombre;
				if($this->mKey == 0 AND $this->mKeyField != ""){
					if(strtolower($nombre) == strtolower($this->mKeyField)){ 
						$this->mKey 		= $index;
						$DatosKey[$index]	= $this->mEsq[$nombre];
						$DatosKey[$nombre]	= $this->mEsq[$nombre];
					}
				}
				$tths 				= "";
				if(isset($this->mColTitles[$nombre])){
					$tths 			= $this->mColTitles[$nombre]; 
				} else {
					$tths 			= strtoupper(str_replace("_", " ", $nombre));
				}
				$tths				= $xLng->getT("TR.$tths");
				if(strpos($tths, "[") !== false){ $tths		= $xLng->getT("TR.$nombre");	}
				$cssTh				= ($index == $this->mKey) ? " class='key' " : "";
				$scope				= ($this->mPrepareChart == true) ? " scope='col' " : "";
				if(isset($this->mTHStyles[$fname]) ){
					$st				= $this->mTHStyles[$fname];
					$cssTh			.= " style='$st' ";
				}
				//Mejorar codigo
				if($this->mTipoSalida == OUT_EXCEL){
					$ths 			.= "<th class=\"xl25\">$tths</th>";
				} else if($this->mTipoSalida == OUT_TXT OR $this->mTipoSalida == OUT_CSV){
					$ths 			.= ($index == 0) ? $tths : $this->mDelimiter . $tths;
					$ths			.= ($index == $ifl) ? "\r\n" : "";
				} else {
					$ths 			.= ($this->mPrepareChart == true AND ($index == $this->mKey) ) ? "<td></td>" : "<th $scope $cssTh>$tths</th>";
				}
				/**
				 * Construye el Array de Tipos y Nombres
				 */
			}

			//$ths . "\n $tht";
			// --------------------------------------------------------------
			if($rs){
				// ----------------------------------------------------------- Permisos 
				$puedeDel	= $xUsr->puede($this->mTbl, "ELIMINAR T " . $this->mTbl, "TABLE");
				$puedeEdit	= $xUsr->puede($this->mTbl, "EDITAR T " . $this->mTbl, "TABLE");
				
			while ($rw = $rs->fetch_array()){
				$this->mRowCount++;
				$tdt 			= "";
				$DKey			= $DatosKey[$this->mKey];
				$pKey 			= $this->mKey;
				$idKey			= ($DKey["TPHP"] == MQL_STRING ) ? "'" . $rw[$DKey["IDX"]] . "'" : $rw[$DKey["IDX"]];
				$dataCustom		= "";
				$trick			= ($trick >= 2) ? 1 : $trick + 1; //switch oddr
				$lis			= "";
				$liCss			= ($trick == 2) ? "tags green" : "tags blue";
				//1 => $xBtn->getBasic($arrTitles[1], "if(typeof actualizame" . $this->mRndK . " != 'undefined'){actualizame" . $this->mRndK . "($idKey);} else {jsUp('" . $this->mTbl . "','" . $this->mKeyField . "', $idKey); }", $xIcs->EDITAR, "redit$idKey", false, true ),
				$t 				= array();
				$t[0]			= $xBtn->getBasic($arrTitles[0], $this->mActionExec . "($idKey)", $xIcs->EJECUTAR, "exec$idKey", false, true );
				$t[1]			= ($puedeEdit == false) ? "" : $xBtn->getBasic($arrTitles[1], "var xG=new Gen();xG.editar({tabla:'" . $this->mTbl .  "',id:" . $idKey . "});", $xIcs->EDITAR, "redit$idKey", false, true );
				$t[2]			= ($puedeDel == false) ? "" : $xBtn->getBasic($arrTitles[2], "var xG=new Gen();xG.rmRecord({tabla:'" . $this->mTbl .  "',id:" . $idKey . "});", $xIcs->ELIMINAR, "rdel$idKey", false, true );
				$t[3]			= (MODO_DEBUG == false) ? "" : $xBtn->getBasic($arrTitles[3], "var xDev=new SafeDev();xDev.recordRAW({tabla:'" . $this->mTbl .  "',id:" . $idKey . "});", $xIcs->EXPORTAR, "idcmdview$idKey", false, true ); 
								
				foreach ($this->mTool as $idx => $clave){ $lis .= isset($t[$clave]) ? "<li>" . $t[$clave] . "</li>" : ""; }
				foreach ($this->mEspCMD as $idx => $cnt){
					$txt 		= "<li>" . str_replace(HP_REPLACE_ID, $rw[$pKey], $cnt) . "</li>";
					//============= Reemplazar Campos Especiales
					if($numreplace == true){
						foreach ($this->mArrReplaces as $fld => $rr){
							//if(isset($rw[$fld])){
								$txt	= str_replace($rr, $rw[$fld], $txt);
							//}
						}
					}
					//==========================================
					$lis .= $txt;
				}
				
				$tdt		= ($lis == "") ? "" : "<ul class='$liCss'>$lis</ul>";
				/**
				 * Herramientas especiales
				 */
				foreach ($this->mEspTool as $key => $value) {
					$DefValue 			= str_replace(HP_REPLACE_ID, $rw[$this->mKey], $value);
					//============= Reemplazar Campos Especiales
					if($numreplace == true){
						foreach ($this->mArrReplaces as $fld => $rr){
							$DefValue	= str_replace($rr, $rw[$fld], $DefValue);
						}
					}
					//==========================================
					if(isset($rw[$key])){ //El campo Existe
						if($xT->cBool($rw[$key]) == true){
							$DefValue 	= str_replace("_COND_", "checked=\"checked\"", $DefValue);
						} else {
							$DefValue 	= str_replace("_COND_", "", $DefValue);
						}
					}					
					//validar si no es PHP
					if(strpos($DefValue, "PHP::")!== false ){
						$cod	= str_replace("PHP::","\$DefValue=", $DefValue);
						eval($cod);
					}
					$tdt		.= "\n " . $DefValue;
				}
				
				$tdt 		= ( trim($tdt) != "" ) ? "<td id=\"options-" . $rw[$this->mKey] . "\" class=\"toolbar-24\">$tdt</td>" : "";
				if($this->mTipoSalida == OUT_EXCEL){ $tdt	= ""; }
				//------------------------------------------------------- Data Omitidos
				if($CountOmitidos>0){
					foreach ($CamposOmi as $nn => $ccol){
						$omival	= $rw[$ccol];
						if($this->mDataCustom == true){	$dataCustom	.= ($dataCustom == "") ? "$ccol=$omival" : "|$ccol=$omival";	}
					}
				}
				//-------------------------------------------------------
				$ttds 				= "";
				foreach ($this->mEsq as $nombre => $col){
				//for($i=0; $i<=$ifl; $i++){
					$event 		= "";
					$css 		= "";
					$oprp		= "";
					$index		= $col["IDX"];
					$currVal	= $rw[$nombre];
					$sanID		= str_replace(" ", "_", $currVal);
					$type 		= $col["T"];// ( isset($arrFieldTypes[$i]) )  ? $arrFieldTypes[$i] : "text";
					$name		= $nombre;
					$isNumero	= $col["PHPNUM"];
					$scope		= "";
					$delims		= "td";
					$mkEnd		= "";
					$extraCNT	= "";
					$numerico	= false;
					$UsarBlanco	= false;
					if ($index == $this->mKey){
						$event 	= $this->EventKey;
						if($event!=""){
							$event 	= "<a class='button' onclick=\"$event('" . $rw[$nombre] . "');\" >";
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
					if( $isNumero == true) {
						if ( !isset($this->mFieldSum[$name])  ) { 
							$this->mFieldSum[$name] = 0;
						}
						$this->mFieldSum[$name]		+= $currVal;
						$numerico					= true;
						$UsarBlanco					= ($currVal == 0 AND $this->mUsarNulls == true) ? true : false;
					}
					//Tipo de Dato
					/**
					 * Si la Opcion de Class por tipo es verdadero
					 * formatea cada Td como su tipo
					 */
					
					if(!isset($oStr[$type])){
						$css	= ($css == "") ? $type : "$css $type";
					}
					if ( isset($this->arrRowCSS[$name]) ){
						$css	= ($css == "") ? $type : "$css " . $this->arrRowCSS[$name];
					}
					$css	= ($css == "") ? "" : " class=\"$css\" ";
					//Formatea a Moneda el valor si es tpo real
					if($type == MQL_FLOAT){ 
						//setError("FLOAT En $name -- - - - - - $type");
						$currVal 	= ($this->mPrepareChart == true OR $this->mTipoSalida == OUT_EXCEL) ? round($currVal,2) : getFMoney($currVal); 
					}
					if($type == MQL_DATE){ $currVal 	= $xD->getFechaMX($currVal); }
					if(isset($oStr[$type])){ $currVal 	= $xHComm->Out($currVal, OUT_HTML); }
					
					if($this->mTipoSalida == OUT_EXCEL){
						//class=xl2216681 nowrap
						$ttds 			.= "<$delims class=\"xl25\" >" . $currVal . "</$delims>";
					} else if($this->mTipoSalida == OUT_TXT OR $this->mTipoSalida == OUT_CSV){
						$ttds 			.= ($index == 0) ? $currVal : $this->mDelimiter . $currVal;
					} else {
						$css			= ($this->mPrepareChart == true) ? "" : $css;
						//retoques a la salida en html
						if($isNumero == false){
							if($this->mPrepareChart == false AND $col["TINY"] == true AND strlen($currVal) > 20){
								$currVal	= "<details><summary>" . substr($currVal, 0,17) . "</summary>". substr($currVal,17) . "</details>";
							}
						}

						$ttds 			.= ($UsarBlanco == true) ? "<$delims $scope $css $oprp>$event" . "" . "$mkEnd$extraCNT</$delims>" : "<$delims $scope $css $oprp>$event" . $currVal . "$mkEnd$extraCNT</$delims>";
					}
				}	//End Data Analisys
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
					//setLog($rw[$this->mKey]);
					if(isset($subHTML[$rw[$this->mKey]])){
						
						$icx22			= $ifl+2;
						$tds 			.= "<tr id=\"tr-subs-" . $this->mTbl . "-".str_replace(" ", "_", $rw[$this->mKey]) . "\"><td colspan='$icx22'>" . $subHTML[$rw[$this->mKey]] . "</td></tr>";
						unset($subHTML[$rw[$this->mKey]]); 
					}
					/*foreach ($this->mSubSQLs as $idx => $sqls){
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
					}*/
				}
		
			}
			$this->vFields 			= $rw;
			// --------------------------------------------------------------
			$rs->free();
			}
			$ism	= $this->mFootSums;
			//sumas
			$tfoot	= "";
			for($i=0; $i<=$ifl; $i++){
				if(isset($ism[$i]) ){
					if($this->mTipoSalida == OUT_EXCEL){
						$tfoot	.= ( isset($this->mFieldSum[ $ism[$i] ]) ) ? "<th>" . getFMoney($this->mFieldSum[ $ism[$i] ]) . "</th>" : "<td></td>";
					} else {
						$tfoot	.= ( isset($this->mFieldSum[ $ism[$i] ]) ) ? "<th><input type='hidden' id='idsum-" .  $ism[$i] . "' value='" . $this->mFieldSum[ $ism[$i] ] . "' /><span id='sum-" .  $ism[$i] . "'>" . getFMoney($this->mFieldSum[ $ism[$i] ]) . "</span></th>" : "<td></td>";
					}
				} else {
					$tfoot		.= ($i==0 AND $this->mRowCount > 0 AND $this->mNoFilas == false) ? "<th>Filas: " . $this->mRowCount . "</th>" : "<td>" . $this->mOpTitleFoot . "</td>";
					 
				}
			}
			$tfoot				.= ($tht == "") ? "" : "<td></td>"; //corrige columna fatante el tools
			$tfoot				= ($oldTags == true) ? "<tr>$tfoot</tr>" : "<tfoot><tr>$tfoot</tr></tfoot>";
			
			$tfoot	= ( $this->mSumFoot == false OR $this->mPrepareChart == true) ? "" : $tfoot;
			//Da Salida  a la Tbla
			$mID	= $this->mID;
			$aProps	= ($this->mTipoSalida == OUT_EXCEL) ? " x:str border=0   style='border-collapse:collapse' " : " $vClassT id=\"$mID\"";
			$aProps	= ($this->mPrepareChart	== true ) ? " style=\"display:none;text-align:center\" id=\"$mID\"" : $aProps;
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
	function getRowCount(){	return $this->mRowCount; }
	/**
	 * Obtiene en codigo JavaScript acciones CRUD.
	 */
	function getJSActions($snipt	= false, $appendID=""){

		if($this->mPrepareChart == false){
		$str = "
	function jsUp(t, f, id){ var urlE = \"../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=\" + t + \"&f=\" + f + \"=\" + id; var xG	= new Gen(); xG.w({url : urlE, w: 800, h: 600, tiny : true });	}
	function jsDel(t, f, id){
		var siXtar = confirm(\"Desea en Realidad Eliminar \\n el Registro Seleccionado\");
		if(siXtar==true){
			var sURL = \"../utils/frm9d23d795f8170f495de9a2c3b251a4cd.php?t=\" + t + \"&f=\" + f + \"=\" + id; var delme = window.open(sURL, \"\", \"width=300,height=300,scrollbars=yes,dependent\"); document.getElementById(\"tr-\" + t + \"-\" + id).innerHTML = \"\";
		}
	}";
		} else {
			$str = "$('#" .$appendID ."').empty();$('#" . $this->mID . "').visualize({width: SCREENW*0.8, height: SCREENH*0.8,type : '" . $this->mChartType . "',barMargin: 2}).appendTo('#" .$appendID ."').trigger('visualizeRefresh');";
		}
		
		if($snipt == true){
			$str	= "<script >" . $str . "</script>";
		}
		return $str;
	}
	function Todo(){  }
	function setFechaCorte($fecha){
		$xF					= new cFecha();
		$this->mFechaCorte 	= $xF->getFechaISO($fecha);
	}
        /**
         * Retorna un Array con la suma por columna, debe ir despues de Show()
         * @return $array
         * */
	function getFieldsSum($index = false){
		$valor	= 0;
		if($index !== false){
			$valor	= (isset($this->mFieldSum[$index])) ? ($this->mFieldSum[$index]): 0;
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
	function setPrepareChart($prepare  = true, $type = "bar"){ $this->mPrepareChart = $prepare; $this->setFootSum(false); $this->mChartType=$type;}
	function getSQL(){ return $this->mSql; }
	function addSubQuery($sql, $field = 0, $mcroformato =""){
		$this->mSubSQLs[$field] = $sql; 
		$this->mSubsEnable		= true;
		$this->mMicroformato	= $mcroformato;
	}
	function addEliminar($NivelUser = false){ $this->addTool(2); }
	function addEditar($NivelUser = false){	$this->addTool(1);	}
	function setUsarNullPorCero(){ $this->mUsarNulls = true; }
	function setPagination($limits){$this->mLimits = $limits;}
	function setNoFilas($OptionalTitle = ""){ $this->mNoFilas = true; $this->mOpTitleFoot = $OptionalTitle; }
	/*			0 => $xLng->getT("TR.Ejecutar"),
				1 => $xLng->getT("TR.Editar"),
				2 => $xLng->getT("TR.Eliminar"),
				3 => $xLng->getT("TR.Ver")*/
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
	private $mUseIDDiv				= false; //usar ID del Div
	private $mDivClass				= "tx4";
	private $mIDKeys				= array();
	private $mEliminados			= array();
	private $mMultiple				= false;
	
	
	function __construct($name, $id = "", $sql = ""){
		$this->mId 		= $id;
		$this->mName 	= $name;
		$this->mSql 	= $sql;
		if($name == "ereport" OR $id == "ireport"){ $this->setNoMayus(); }
	}
	function setDivClass($div){$this->mDivClass=$div;}
	function setCerrar($cerrar = true){ $this->mCerrar = $cerrar; }
	function setLabel($lbl = ""){ $this->mLabel = $lbl; }
	function addEspOption($value, $caption = ""){
		$caption	= ($caption == "") ? strtoupper($value) : $caption;
		if($caption == SYS_TODAS){ $caption = SYS_TEXTO_TODAS; }
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
	function setSqlLimit($limit = "0,1"){ $this->mSqlLimit = " LIMIT " . $limit;	}
	function setSql($sql){	$this->mSql = $sql;	}
	function setSqlWhere($where = ""){ $this->mSqlWhere = " WHERE " . $where;	}
	/**
	 * Establece si se trata de un SQL o NO
	 * @param boolean $bool
	 */
	function setEsSql($bool = true){ 	$this->mEsSql = $bool;	}
	function setEliminarOption($key){ 
		if(isset($this->mEspOption[$key])){ 
			unset($this->mEspOption[$key]); 
		} 
		$this->mEliminados[$key]	= $key;
	}
	function getOptions(){ return $this->mIDKeys; }
	function setOptionSelect($option){ 	$this->mOptionSelect = $option; }
	function setPut($type = "xul"){ 	$this->mPut = $type; }
	function setNRows($rows = 1){ 		$this->mNRows = $rows; }
	function get($label = "",$cerrar = false){
		if(is_bool($label)){ $cerrar = $label; $label = "";}
		$xL		= new cLang();
		$label	= ($label == "") ? $this->mLabel : $label;
		$cerrar	= ($cerrar == false) ? $this->mCerrar : $cerrar;
		$label	= ($label == "") ? "" : $xL->getT($label);
		$iddiv	= ($this->mUseIDDiv == true) ? " id='div-". $this->mId . "' " : "";	
		$lbl	= "";
		if($label != ""){ $lbl	= "<label for='" . $this->mId . "'>$label</label>"; }
		return ($cerrar == false ) ? "" . $lbl . $this->show(true) . "" : "<div class='" . $this->mDivClass . "'$iddiv>" . $lbl . $this->show(true) . "</div>";
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
		$xQL		= new MQL();
		$rw 		= array();
		//Sql
		if($this->mEsSql == false){
			$pSql = "SELECT * FROM " . $this->mSql;
		} else {
			$pSql = $this->mSql;
		}
		if($this->mSqlWhere !=""){$pSql = $pSql . $this->mSqlWhere;}
		if($this->mSqlLimit != ""){$pSql = $pSql . $this->mSqlLimit; }
		$this->mRs 		=   $xQL->getRecordset($pSql);
		if($this->mRs){
			while ($rw = $this->mRs->fetch_array()){
				$slt 	= "";
				$ival 	=  $rw[$this->mFieldValue];
				if($this->mNoMayus == false){
					$ival	= strtoupper($ival);
				}
				$icap	= (isset($rw[$this->mFieldCaption])) ? $rw[$this->mFieldCaption] : $rw[$this->mFieldValue];
				$icap 	= str_replace('"', "'", $icap);
				$icap	= $xT->setNoAcentos( $icap );
				$icap	= $xT->cMayusculas($icap);
				
				if($this->mOptionSelect==$ival){ $slt = " selected = \"selected\" ";}
				if(isset($this->mEliminados[$ival])){
					//eliminar
				} else {
					$ops	= $ops . "<option value=\"$ival\"$slt>$icap</option>";
					$this->mIDKeys[$ival]		= $ival;
					$this->mCount++;
				}
			}
			$this->mRs	= null;
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
				$sltt 			= "";
				if($this->mOptionSelect == $tmpOp ){ $sltt = " selected=\"selected\" "; }
				$pOpts 			= $pOpts . "<option value=\"$tmpOp\"$sltt>$tmpCap</option>";
				$this->mIDKeys[$tmpOp]		= $tmpOp;
				$this->mCount++;
			}
		}
		$IDCache	= "";
		$mm			= ($this->mMultiple == true) ? " multiple" : "";
			$ctrl = "<select size=\"" . $this->mNRows . "\" name=\"" . $this->mName . "\" $cid $pEvts$mm>
				$ops
				$pOpts
			</select>";


		if($return == true){
			return $ctrl;
		} else {
			echo $ctrl;
		}
	}
	function __destruct(){	}
	function setUseIDDiv(){ $this->mUseIDDiv = true; }
	function setLabelSize($tamanno){}
	function getLabel(){$xL		= new cLang();return "<label for='" . $this->mId . "'>" . $xL->getT($this->mLabel) . "</label>";}
	function getSQL(){ return  $this->mSql; }
	function getCountRows(){ return $this->mCount; }
	function getListaKeys(){ return $this->mIDKeys; }
	function setMultiple(){ $this->mMultiple = true; }
	function setID($id){ $this->mId  = $id; }
}



class cHUl {
	private $mLineas	= "";
	private $mObj		= null;
	public $mId			= "";
	public $mTags		= "a";
	public $mType		= "ol";
	public $mCls		= "rounded-list";
	
	function __construct($id = "", $type = "ol", $class = "rounded-list"){
		$this->mId		= $id;
		$this->mType	= $type;
		$this->mCls		= $class;
	}
	function setTags($tags = "a"){ $this->mTags = $tags; }
	function li($str = "") {
		if($this->mObj == null){
			$this->mObj	= new cHLi($this);
		}
		
		if(trim($str) !== ""){
			$this->mObj->add($str);
		}
		
		return $this->mObj;
	}
	function getO(){
		if($this->mObj == null){
			$this->mObj	= new cHLi($this);
		}
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
	private $mTag		= "a";
	private $mIdL		= "";
	function __construct($parent){
		$this->mParent	= $parent;
		$this->mT		= $this->mParent->mType;
		$this->mCls		= $this->mParent->mCls;
		$this->mTag		= $this->mParent->mTags;
		$this->mIdL		= $this->mParent->mId;
		$parent			= null;
		$this->mParent	= null;
	}
	function add($str, $closeTag = ""){
		if($closeTag == ""){ $closeTag	= $this->mTag; }
		$init	= ($closeTag == "") ? "" : "<$closeTag>";
		$end	= ($closeTag == "") ? "" : "</$closeTag>";
		if(trim($str) == ""){
			$init	= "";
			$end	= "";
		}
		if(trim($str) !== ""){
			$this->mLineas .= "<li>$init$str$end</li>";
		}
		//setLog($str);
	}
	function end(){
		$id		=($this->mIdL == "") ? "" : " id=\"" . $this->mIdL . "\""; 
		$str	= "<" . $this->mT . " class='" . $this->mCls . "' $id>" . $this->mLineas . "</" . $this->mT . ">";
		$this->mLineas	= "";
		return $str;
	}
	function setT($cls){ $this->mT = $cls; }
	function setClass($cls){ $this->mCls = $cls;}
	function li($str = ""){
		$this->add($str);
		return $this;
	}
}
class cHCheckBox {
	protected $mDivClass		= "tx4";
	private $mAEvents			= array();
	private $mOLang				= null;
	function __construct(){
		
	}
	function addEvent($function = "", $onevent = "", $check = false){ $this->mAEvents[$onevent] = $function; }
	function setOnClick($event){ $this->addEvent($event, "onclick"); }
	function get($label = "", $id = "idcoolcheck", $checked = false){
		if($this->mOLang == null){$this->mOLang = new cLang();}
		$xT		= new cTipos();
		$checked=$xT->cBool($checked);
		$label	= ($label == "") ? "" : $this->mOLang->getT($label);
		$cls	= ($this->mDivClass == "") ? "" : "class=\"" . $this->mDivClass . "\" ";
		$check	= ($checked == true) ? " checked='checked'" : "";
		$events	= "";
		if($id == ""){
			$id	= "chk-" . rand(0,100). "-" . time();
		}
		foreach ($this->mAEvents as $onevent => $function){
			$events	.= "$onevent=\"$function\" ";
		}
		$divInit= ($cls == "") ? "" : "<div $cls>";
		$divEnd = ($cls == "") ? "" : "</div>";

		$s		= "$divInit<table class='chk' style='border:none;'>
					<tr>
					<td style='width:85%;border:none;'><label class=\"chk-lbl\">$label</label></td>
					<td style='width:15%;border:none;'><div class=\"coolCheck\"><input type=\"checkbox\" id=\"$id\" name=\"$id\" $events$check/><label for=\"$id\"></label></div></td>
					</tr>
					</table>$divEnd";
		if(trim($label) == ""){
			$s		= "$divInit<div class=\"coolCheck\"><input type=\"checkbox\" id=\"$id\" name=\"$id\" $events$check/><label for=\"$id\"></label></div>$divEnd";
		}
		return $s;
	}
	function getSiNo($label = "", $id = "idcoolcheck", $val = 0, $tiny = false){
		if($this->mOLang == null){$this->mOLang = new cLang();}
		$xT		= new cTipos();
		
		$label	= ($label == "") ? "" : $this->mOLang->getT($label);
		$cls	= ($this->mDivClass == "") ? "" : "class=\"" . $this->mDivClass . "\" ";
		$check	= ($val == 1) ? " checked='checked'" : "";
		$events	= "";
		foreach ($this->mAEvents as $onevent => $function){
			if($onevent !== "onclick"){
				$events	.= "$onevent='$function' ";
			}
		}
		$divInit= ($cls == "") ? "" : "<div $cls>";
		$divEnd = ($cls == "") ? "" : "</div>";
		$t1		= "80"; $t2 = "20";
		if($tiny == true){ $t1 = "70"; $t2 = "30"; }
		$s		= "$divInit<table class='chk' style='border:none;'>
		<tr>
		<td style='width:$t1%;border:none;'><label class=\"chk-lbl\">$label</label><input type=\"hidden\" id=\"$id\" name=\"$id\" value=\"$val\" /></td>
		<td style='width:$t2%;border:none;'>
		<label class=\"switch switch-yes-no\" id=\"lbl-chk-$id\">
			<input id=\"chk-$id\" class=\"switch-input\" type=\"checkbox\" onchange=\"if(this.checked == true){ $('#$id').val(1); } else { $('#$id').val(0); }\" $check/>
			<span class=\"switch-label\" data-on=\"Si\" data-off=\"No\"></span> 
			<span class=\"switch-handle\"></span>
		</label>
		</td>
		</tr>
		</table>$divEnd";

		return $s;
	}
	function setDivClass($class){ $this->mDivClass = $class; }
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
	
	private $mClave		= false;
	private $mForma		= false;
	private $mInit		= false;
	private $mIgnoreTes	= false;
	private $mSenders	= array();
	private $mOut		= "";
	
	function __construct($numero, $formato){
		$this->mClave	= $numero;
		$this->mForma	= $formato;
		$this->mOut		= SYS_DEFAULT;
	}
	function setOut($out){ $this->mOut = strtolower($out); }
	function setIgnorarTesoreria($ignore = true){ $this->mIgnoreTes = $ignore; }
	/*function setOut($out = ""){
		if($out == "" OR $out == SYS_DEFAULT){
			$this->mOut	= SYS_DEFAULT;
		} else {
			$this->setIgnorarTesoreria(true);
			$this->mOut	= $out;
		}
	}*/
	function render($IncPrint = true){
		$xRec			= new cReciboDeOperacion(false, false, $this->mClave);
		$html			= "";
		$recibo			= $this->mClave;
		
		if($xRec->init() == true){
			$this->mInit	= true;
			$scripts		= "";
			$xCaja			= $xRec->getOCaja();
			$forma			= $xRec->getURI_Formato() . "&forma=" . $this->mForma;
			$docto			= $xRec->getCodigoDeDocumento();
			$empresa		= $xRec->getPersonaAsociada();
			$pagable		= $xRec->isPagable();
			
			//Cargar datos si es de credito
			/*if($xRec->getEsDeCredito() == true){
				$xCred		= new cCredito($xRec->getCodigoDeDocumento());
				if($xCred->init() == true){
					if($xCred->getEsNomina() == true){
						$pagable	= false;
					}
				}
			}*/
			
			//Eliminar cache de montos, creditos y cuentas de captacion
			$xCache		= new cCache();
			$xCache->clean("creditos_solicitud-$docto");
			$xCache->clean("creditos_montos-$docto");
			$xCache->clean("captacion_cuentas-$docto");
			//
			//TODO: Resolver ajuste y permisos de ajuste
			
			if(MODULO_CAJA_ACTIVADO == true AND $pagable == true ){

				$totaloperacion		= $xRec->getTotal();
				$TesMontoPagado		= $xRec->getSaldoEnCaja();
				$forma				= $xRec->getURI_Formato() . "&forma=" . $this->mForma;
				if($TesMontoPagado > 0 AND $this->mIgnoreTes == false){
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
					if($IncPrint == true){
						$scripts	= "<script>window.print();</script>";
					}
				}
				
				//$html.= $xRec->getMessages(OUT_HTML);
				$xForms			= new cFormato($this->mForma);
				//$xForms->init();
				$xForms->setRecibo($this->mClave);
				$xForms->setOut($this->mOut);
				$xForms->setProcesarVars();
				
				$html			.= $xForms->get();
				if($this->mOut == OUT_TXT){
					
				} else {
					$html		.= "<script>function jsRevalidarRecibo(){document.location = \"$forma\"; }</script>";
					$html		.= $scripts;
				}
			} else {
				if(MODULO_CAJA_ACTIVADO == false){
					//$html.= $xRec->getMessages(OUT_HTML);
					$xForms			= new cFormato($this->mForma);
					//$xForms->init();
					$xForms->setRecibo($this->mClave);
					$xForms->setOut($this->mOut);
					
					$xForms->setProcesarVars();
					$html			.= $xForms->get();
					$html			.= "<script>function jsRevalidarRecibo(){document.location = \"$forma\"; }</script>";
					$html			.= $scripts;					
				} else {
					if($xRec->isPagable() == false){
						//Print error
						$xForms			= new cFormato($this->mForma);
						//$xForms->init();
						$xForms->setRecibo($this->mClave);
						$xForms->setOut($this->mOut);
						
						$xForms->setProcesarVars();

						$html			.= $xForms->get();
						
					} else {
						$xErr			= new cError();
						$xErr->setGoErrorPage($xErr->ERR_MODULO);
					}			
				}
			}
		}
		
		return $html;
	}
	function isInit(){ return $this->mInit; }
}

class cFormato {
	private $mTxt			= "";
	private $mID			= "";
	private $mArr			= array();
	private $mTitle			= "";
	private $mObj			= null;
	private $mDocumento		= null;
	private $mPersona		= null;
	private $mEsRecibo		= false;
	private $mDataMvto		= array();
	private $mRecibo		= null;
	private $mBasicVars		= array();		//Variables basicas
	private $mFirmasAvales	= "";
	private $mFirmasAvales2	= "";
	private $mFirmasAvales3	= "";
	
	
	
	private $mFirmasAvalesH	= array();
	private $mFicha5Avales	= "";	
	private $mFicha6Avales	= "";
	
	private $mFichasAvales	= "";
	private $mFichasAvales2	= "";
	private $mFichaRiesgoAv	= "";
	private $mLAvalesConDir	= "";		//avales con domicilio
	private $mListaAvales	= "";

	private $mEstadoFlujo	= "";
	private $mEstadoBienes	= "";
	private $mEsatdoCuenta	= "";
	private $mOPersona		= null;
	private $mOCredito		= null;
	private $mOut			= SYS_DEFAULT;
	private $mOLang			= null;
	private $mInit			= false;
	public $FMT_NOMINA_ENVP	= 4502; //pie de pagina de Nomina
	public $PRINT_LINE_CHARS	= 48;
	
	public $VARS_IDPERSONA	= 0;
	public $VARS_IDCREDITO	= 0;
	public $VARS_IDCUENTA	= 0;
	public $VARS_IDRECIBO	= 0;
	
	private $mTipo			= 0;
	private $mListVars		= array();
	
	function __construct($clave = false){
		$xUsr	= new cOficial( getUsuarioActual() );
		$xH		= new cHObject();
		$clave	= setNoMenorQueCero($clave);
		$this->init($clave);
		$xLoc	= new cLocal();
		
		$this->setFechaDeFormato();
		
		
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
		$this->mArr["variable_entidad_logo"]										= EACP_PATH_LOGO;
		
		$this->mArr["vars_simbolo_cuadro"]											= "<span style='font-size:2.5em;'>&#9744;</span>";
		
		$this->mArr["var_entidad_mail"]												= EACP_MAIL;
		$this->mArr["var_entidad_pagos_mail"]										= FACTURACION_MAIL_ARCHIVO;
		
		$this->mArr["variable_documento_de_constitucion_de_la_sociedad"]			= EACP_DOCTO_CONSTITUCION;
		$this->mArr["variable_rfc_de_la_entidad"]									= EACP_RFC;
		
		$this->mArr["variable_nombre_del_representante_legal_de_la_sociedad"]		= EACP_REP_LEGAL;
		$this->mArr["variable_nombre_de_presidente_de_vigilancia_de_la_entidad"]	= EACP_PDTE_VIGILANCIA;
		
		$this->mArr["variable_encabezado_de_reporte"]								= getRawHeader();
		$this->mArr["variable_pie_de_reporte"]										= getRawFooter();
		//$this->mArr["variable_pie_de_reporte"]										= getRawFooter();
		
		$this->mArr["variable_hora_actual"]											= date("H:i");
		$this->mArr["variable_marca_de_tiempo"]										= date("d/m/Y ha:i:s");
		$this->mArr["variable_url_publica"]											= SAFE_HOST_URL;
		$this->mArr["variable_lugar_actual"]										= $xH->Out($xLoc->DomicilioLocalidad() . "," . $xLoc->DomicilioEstado(), OUT_HTML);
		
		$this->mArr["variable_lugar"]												= $xH->Out($xLoc->DomicilioLocalidad() . "," . $xLoc->DomicilioEstado(), OUT_HTML);
		$this->mArr["variable_nombre_id_fiscal"]									= PERSONAS_NOMBRE_ID_FISCAL;
		$this->mArr["variable_nombre_id_poblacional"]								= PERSONAS_NOMBRE_ID_POBLACIONAL;
		$this->mArr["variable_persona_lista_de_bienes"] 							= ""; 
		$this->mArr["variable_credito_estado_flujo_efectivo"] 						= "";
		$this->mArr["variable_aval1_nombre_completo"]								= "";
		$this->mArr["variable_aval2_nombre_completo"]								= "";
		$this->mArr["variable_persona_lista_familiares"]							= "";
		$this->mArr["variable_persona_lista_no_familiares"]							= "";
		$this->mArr["variable_numero_del_recibo"]									= "";
		$this->mArr["variable_empresa_nombre_corto"]								= "";
		$this->mArr["variable_numero_del_recibo"]									= "";
		//Usuario
		$this->mArr["variable_nombre_del_cajero"]									= "";
		$this->mArr["variable_oficial"]												= "";
		$this->mArr["variable_testigo_del_acto"]									= "";
		$this->mArr["variable_tipo_de_credito"]										= "";
		$this->mArr["variable_estado_de_credito"]									= "";
		$this->mArr["variable_credito_numero_de_pagos"]								= "";
		$this->mArr["variable_movimiento_saldo_final"]								= "";//
		$this->mArr["variable_recibo_saldo_final"]									= "";
		$this->mArr["variable_credito_plan_exigible"]								= "";
		$this->mArr["variable_credito_plan_pendiente"]								= "";
		
		
		$this->mBasicVars															= $this->mArr;
	}
	function init($clave = false){
		$clave		= setNoMenorQueCero($clave);
		$this->mID	= $clave;
		$xCache		= new cCache();
		$idc		= "general_contratos-$clave";
		$data		= $xCache->get($idc);
		$this->mObj	= new cGeneral_contratos();
		if(!is_array($data)){
			$xQL	= new MQL();
			$data	= $xQL->getDataRow("SELECT * FROM `general_contratos` WHERE `idgeneral_contratos`=$clave LIMIT 0,1");
		}
		if(isset($data["idgeneral_contratos"])){
			//$this->mTxt		= contrato($clave, "texto_del_contrato");
			$this->mObj->setData($data);
			$this->mTitle	= $this->mObj->titulo_del_contrato()->v();
			$this->mTxt		= stripslashes($this->mObj->texto_del_contrato()->v());
			$this->mInit	= true;
			$this->mTipo	= $this->mObj->tipo_contrato()->v();
			$xCache->set($idc, $data, $xCache->EXPIRA_MEDDIA);
		}
		return $this->mInit;
	}
	function setID($clave){
		$clave	= setNoMenorQueCero($clave);
		if($clave>0){
			$this->mID	= $clave;
		}
	}
	function setBaja(){
		$xCache		= new cCache();
		$idc		= "general_contratos-" . $this->mID;
		$xQL		= new MQL();
		$xQL->setRawQuery("UPDATE `general_contratos` SET `estatus`='baja' WHERE `idgeneral_contratos`=" . $this->mID);
		$xCache->clean($idc);
		
	}
	function setAlta(){
		$xCache		= new cCache();
		$idc		= "general_contratos-" . $this->mID;
		$xQL		= new MQL();
		$xQL->setRawQuery("UPDATE `general_contratos` SET `estatus`='alta' WHERE `idgeneral_contratos`=" . $this->mID);
		$xCache->clean($idc);
		
	}
	function setUsuario($usuario = false){
		$usuario									= ($usuario == false) ? getUsuarioActual() : $usuario;
		$Usr										= new cSystemUser( $usuario );
		
		$this->mArr["variable_nombre_del_cajero"]	= $Usr->getNombreCompleto();
		$this->mArr["variable_oficial"]				= $Usr->getNombreCompleto();
		$this->mArr["variable_testigo_del_acto"]	= $Usr->getNombreCompleto();
		//$this->mArr["variable_usr_"]	=
		$this->mArr["variable_usr_telefono"]		= "";
		$this->mArr["variable_usr_mail"]			= "";
		$this->mArr["variable_usr_nombre"]			= "";
		
		//Iniciar datos del Usuario
		$idpersona		= $Usr->getClaveDePersona();
		$xSoc			= new cSocio($idpersona);
		if($xSoc->init() == true){
			$this->mArr["variable_usr_telefono"]		= $xSoc->getTelefonoPrincipal();
			$this->mArr["variable_usr_mail"]			= $xSoc->getCorreoElectronico();
			$this->mArr["variable_usr_nombre"]			= $xSoc->getNombreCompleto();
		}
	}
	function setSucursal($sucursal = false){
		$sucursal		= ( $sucursal == false ) ? getSucursal() : $sucursal;
		$xSuc			= new cSucursal($sucursal);
		$xSuc->init();
		$variable_lugar																= htmlentities($xSuc->getEstado()) . ", " . htmlentities($xSuc->getMunicipio());
		$this->mArr["variable_lugar"] 												= $variable_lugar;
		$this->mArr["variable_lugar_actual"] 										= $variable_lugar;
	}
	function setEmpresa($empresa){
		$this->mArr["variable_nombre_de_empresa"]			= "";
		$this->mArr["variable_empresa_nombre_corto"]		= "";
		$this->mArr["variable_clave_de_empresa"]			= $empresa;
		$xEmp	= new cEmpresas($empresa); 
		if($xEmp->init() == true){
			$this->mArr["variable_nombre_de_empresa"]		= $xEmp->getNombre();
			$this->mArr["variable_empresa_nombre_corto"]	= $xEmp->getNombreCorto();
		}
				
	}
	function setPersona($clave_de_persona, $datos = false){
		$this->mPersona					= $clave_de_persona;
		$cSoc							= new cSocio($clave_de_persona);
		$idFormaFirmaP					= 13001;
		$fichas_de_respsolidarios		= "";
		$firmas_de_respsolidarios		= "";
		if($cSoc->init($datos) == true){
			$domicilio_del_socio		= $cSoc->getDomicilio();
			$ficha_socio				= $cSoc->getFicha(true, true);
			$this->mOPersona			= $cSoc;
			$xODom						= $cSoc->getODomicilio();
			$xOAE						= $cSoc->getOActividadEconomica();
			$xTipoViv					= new cPersonasDocumentacionTipos();
			
			$xH							= new cHObject();
			$xLng						= $this->getOLang();
			//Caja local por SQL
			$this->setCajaLocal($cSoc->getNumeroDeCajaLocal());
			$this->setRegionLocal($cSoc->getRegion());
			
			$SQLCBen = "SELECT `socios_relacionestipos`.`descripcion_relacionestipos` AS 'relacion', `socios_relaciones`.`nombres`,	`socios_relaciones`.`apellido_paterno`,	`socios_relaciones`.`apellido_materno`,
			`socios_consanguinidad`.`descripcion_consanguinidad` AS 'consaguinidad'
			FROM `socios_relaciones` `socios_relaciones` INNER JOIN `socios_consanguinidad` `socios_consanguinidad` ON `socios_relaciones`.`consanguinidad` = `socios_consanguinidad`.`idsocios_consanguinidad`
			INNER JOIN `socios_relacionestipos` `socios_relacionestipos` ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.`idsocios_relacionestipos`
			WHERE (`socios_relaciones`.`socio_relacionado` =" . $this->mPersona . ")	AND	(`socios_relaciones`.`tipo_relacion`=11)";
			$tblCBen 				= new cTabla($SQLCBen);			
			$xFecha					= new cFecha();
			
			$this->mArr["variable_vivienda_calle"]					= "";
			$this->mArr["variable_vivienda_colonia"]				= "";
			$this->mArr["variable_vivienda_poblacion"]				= "";
			$this->mArr["variable_vivienda_estado"]					= "";
			$this->mArr["variable_estado_civil_del_socio"]			= "";
			
								
			$this->mArr["variable_domicilio_del_socio"] 			= $domicilio_del_socio;
			$this->mArr["variable_rfc_del_socio"] 					= $cSoc->getRFC();
			$this->mArr["variable_curp_del_socio"] 					= $cSoc->getCURP();
			$this->mArr["variable_persona_identificacion_oficial"] 	= $cSoc->getClaveDeIdentificacion();
			
			$this->mArr["variable_numero_de_socio"] 				= $cSoc->getCodigo();
			$this->mArr["variable_persona_id_interna"] 				= $cSoc->getIDInterna();
			$this->mArr["variable_informacion_del_socio"] 			= $ficha_socio;
			$this->mArr["variable_fecha_de_nacimiento_del_socio"]	= $xFecha->getFechaMediana($cSoc->getFechaDeNacimiento());
			$this->mArr["variable_ciudad_de_nacimiento_del_socio"]	= $cSoc->getLugarDeNacimiento();
			$this->mArr["variable_persona_clave_electoral"]						= $cSoc->getClaveDeIFE();
			$this->mArr["variable_persona_regimen_matrimonial"]		= $xLng->getT("TR.". $cSoc->getTipoRegimenMatrimonial());

			$this->mArr["variable_persona_tipo_identificacion"]		= $cSoc->getTipoDeIdentificacion();
			
			$xTI	= new cPersonasDocumentacionTipos($cSoc->getTipoDeIdentificacion()); $xTI->init();
			$this->mArr["variable_persona_tipo_identificacion"]		= $xTI->getNombre();
			
			$this->mArr["variable_persona_clave_identificacion"]	= $cSoc->getClaveDeIdentificacion();
			$this->mArr["variable_persona_profesion"]				= $cSoc->getTituloPersonal();
			$this->mArr["variable_persona_email"]					= $cSoc->getCorreoElectronico();
			$this->mArr["variable_persona_rep_legal"]				= $cSoc->getNombreDelRepresentanteLegal();
			
			//================== Variables de Persona moral
			if($cSoc->getEsPersonaFisica() == true){
				$this->mArr["var_firmante_principal"]				= $cSoc->getNombreCompleto(OUT_HTML);
				$this->mArr["var_firmante_rol"]						= "Acreditado";
				
			}  else {
				$this->mArr["var_firmante_principal"]				= $cSoc->getNombreDelRepresentanteLegal();
				$this->mArr["var_firmante_rol"]						= "Representante Legal";
			}
			
			$this->mArr["var_firmantes_ficha_1"]					= "";
			$this->mArr["var_firmantes_ficha_2"]					= "";
			$this->mArr["var_firmantes_ficha_3"]					= "";
			$this->mArr["var_firmantes_ficha_4"]					= "";
			$this->mArr["var_firmantes_ficha_5"]					= "";
			
			
			if($cSoc->getClaveDePersonalRepLegal()>DEFAULT_SOCIO){
				//setLog($cSoc->getClaveDePersonalRepLegal());
				$xRepLegal	= new cSocio($cSoc->getClaveDePersonalRepLegal());
				if($xRepLegal->init() == true){
					$this->mArr["variable_persona_rep_legal"]			= $xRepLegal->getNombreCompleto();
					$this->mArr["var_replegal_nombre_completo"] 		= $xRepLegal->getNombreCompleto();
					$this->mArr["var_replegal_email"]					= $xRepLegal->getCorreoElectronico();
					$this->mArr["var_replegal_domicilio_convencional"]	= $xRepLegal->getDomicilio();
					$this->mArr["var_replegal_id_tipo"]					= "";
					$this->mArr["var_replegal_id_clave"]				= $xRepLegal->getClaveDeIdentificacion();
					$this->mArr["var_replegal_id_ife"]					= $xRepLegal->getClaveDeIFE();

					if($xRepLegal->getODomicilio() == null){
						$this->mArr["var_replegal_domicilio_localidad"] 	= "";
						$this->mArr["var_replegal_direccion_calle_y_numero"]= "";
						$this->mArr["var_replegal_direccion_estado"] 		= "";
						$this->mArr["var_replegal_direccion_completa"]	 	= "";
					} else {
						$xRPDom	= $xRepLegal->getODomicilio();
						$this->mArr["var_replegal_domicilio_localidad"] 	= $xRPDom->getLocalidad();
						$this->mArr["var_replegal_direccion_calle_y_numero"]= $xRPDom->getCalleConNumero();
						$this->mArr["var_replegal_direccion_estado"] 		= $xRPDom->getEstado();
						$this->mArr["var_replegal_direccion_completa"]	 	= $xRPDom->getDireccionBasica();
					}
					$this->mArr["var_replegal_ocupacion"] 				= $xRepLegal->getTituloPersonal();
					$this->mArr["var_replegal_telefono_principal"]		= $xRepLegal->getTelefonoPrincipal();
					$this->mArr["var_replegal_fecha_de_nacimiento"] 	= $xFecha->getFechaMediana($xRepLegal->getFechaDeNacimiento());
					$this->mArr["var_replegal_id_fiscal"] 				= $xRepLegal->getRFC();
					$this->mArr["var_replegal_id_poblacional"] 			= $xRepLegal->getCURP();
					$this->mArr["var_replegal_lugar_de_nacimiento"] 	= $xRepLegal->getLugarDeNacimiento();
					
					$xEstadoCiv											= new cPersonasEstadoCivil($xRepLegal->getEstadoCivil());
					if($xEstadoCiv->init() == true){
						$this->mArr["var_replegal_estado_civil"] 		= $xEstadoCiv->getNombre();
					} else {
						$this->mArr["var_replegal_estado_civil"] 		= "";
					}
					$this->mArr["var_replegal_nombres"]					= $xRepLegal->getNombre();
					$this->mArr["var_replegal_primer_apellido"]			= $xRepLegal->getApellidoPaterno();
					$this->mArr["var_replegal_segundo_apellido"]		= $xRepLegal->getApellidoMaterno();
				}
				
			} else {
				$this->mArr["var_replegal_nombre_completo"] 		= "";
				$this->mArr["var_replegal_domicilio_convencional"]	= "";
				$this->mArr["var_replegal_email"]					= "";
				$this->mArr["var_replegal_domicilio_localidad"] 	= "";
				$this->mArr["var_replegal_direccion_calle_y_numero"]= "";
				$this->mArr["var_replegal_direccion_estado"] 		= "";
				$this->mArr["var_replegal_direccion_completa"]	 	= "";
				$this->mArr["var_replegal_ocupacion"] 				= "";
				$this->mArr["var_replegal_telefono_principal"]		= "";
				$this->mArr["var_replegal_fecha_de_nacimiento"] 	= "";
				$this->mArr["var_replegal_id_fiscal"] 				= "";
				$this->mArr["var_replegal_id_poblacional"] 			= "";
				$this->mArr["var_replegal_lugar_de_nacimiento"] 	= "";
				$this->mArr["var_replegal_empresa_de_trabajo"] 		= "";
				$this->mArr["var_replegal_estado_civil"] 			= "";
				$this->mArr["var_replegal_nombres"]					= "";
				$this->mArr["var_replegal_primer_apellido"]			= "";
				$this->mArr["var_replegal_segundo_apellido"]		= "";
				
				$this->mArr["var_replegal_id_tipo"]					= "";
				$this->mArr["var_replegal_id_clave"]				= "";
				$this->mArr["var_replegal_id_ife"]					= "";
			}
			$this->mArr["var_pers_rep_legals"]						= $this->mArr["variable_persona_rep_legal"];
			//Nombre de Representante Mancomunado
			$xORepLegalM	= $cSoc->getORepresentanteLegalManc();
			if($xORepLegalM !== null){
				$this->mArr["var_pers_rep_legals"]					.= ", " . $xORepLegalM->getNombreDelRelacionado();
			}
			
			if($cSoc->getClaveDePersonaDeConyuge() > DEFAULT_SOCIO AND $cSoc->getEsPersonaFisica() == true){
				$xCony		= new cSocio($cSoc->getClaveDePersonaDeConyuge());
				if($xCony->init() == true){
					
					$this->mArr["var_conyuge_nombre_completo"] 				= $xCony->getNombreCompleto();
					$this->mArr["var_conyuge_email"]						= $xCony->getCorreoElectronico();
					$this->mArr["var_conyuge_domicilio_convencional"]		= $xCony->getDomicilio();
					$this->mArr["var_conyuge_id_tipo"]						= "";
					$this->mArr["var_conyuge_id_clave"]						= $xCony->getClaveDeIdentificacion();
					$this->mArr["var_conyuge_id_ife"]						= $xCony->getClaveDeIFE();
					
					if($xCony->getODomicilio() == null){
						$this->mArr["var_conyuge_domicilio_localidad"] 		= "";
						$this->mArr["var_conyuge_direccion_calle_y_numero"]	= "";
						$this->mArr["var_conyuge_direccion_estado"] 		= "";
						$this->mArr["var_conyuge_direccion_completa"]	 	= "";
					} else {
						$xRPDom	= $xCony->getODomicilio();
						$this->mArr["var_conyuge_domicilio_localidad"] 		= $xRPDom->getLocalidad();
						$this->mArr["var_conyuge_direccion_calle_y_numero"]	= $xRPDom->getCalleConNumero();
						$this->mArr["var_conyuge_direccion_estado"] 		= $xRPDom->getEstado();
						$this->mArr["var_conyuge_direccion_completa"]	 	= $xRPDom->getDireccionBasica();
					}
					$this->mArr["var_conyuge_ocupacion"] 					= $xCony->getTituloPersonal();
					$this->mArr["var_conyuge_telefono_principal"]			= $xCony->getTelefonoPrincipal();
					$this->mArr["var_conyuge_fecha_de_nacimiento"] 			= $xFecha->getFechaMediana($xCony->getFechaDeNacimiento());
					$this->mArr["var_conyuge_id_fiscal"] 					= $xCony->getRFC();
					$this->mArr["var_conyuge_id_poblacional"] 				= $xCony->getCURP();
					$this->mArr["var_conyuge_lugar_de_nacimiento"] 			= $xCony->getLugarDeNacimiento();
					
					$xEstadoCiv											= new cPersonasEstadoCivil($xCony->getEstadoCivil());
					if($xEstadoCiv->init() == true){
						$this->mArr["var_conyuge_estado_civil"] 		= $xEstadoCiv->getNombre();
					} else {
						$this->mArr["var_conyuge_estado_civil"] 		= "";
					}
					$this->mArr["var_conyuge_nombres"]					= $xCony->getNombre();
					$this->mArr["var_conyuge_primer_apellido"]			= $xCony->getApellidoPaterno();
					$this->mArr["var_conyuge_segundo_apellido"]			= $xCony->getApellidoMaterno();
				}
			}
			if(!isset($this->mArr["var_conyuge_nombre_completo"])){
				$this->mArr["var_conyuge_nombre_completo"] 			= "";
				$this->mArr["var_conyuge_domicilio_convencional"]	= "";
				$this->mArr["var_conyuge_email"]					= "";
				$this->mArr["var_conyuge_domicilio_localidad"] 		= "";
				$this->mArr["var_conyuge_direccion_calle_y_numero"]	= "";
				$this->mArr["var_conyuge_direccion_estado"] 		= "";
				$this->mArr["var_conyuge_direccion_completa"]	 	= "";
				$this->mArr["var_conyuge_ocupacion"] 				= "";
				$this->mArr["var_conyuge_telefono_principal"]		= "";
				$this->mArr["var_conyuge_fecha_de_nacimiento"] 		= "";
				$this->mArr["var_conyuge_id_fiscal"] 				= "";
				$this->mArr["var_conyuge_id_poblacional"] 			= "";
				$this->mArr["var_conyuge_lugar_de_nacimiento"] 		= "";
				$this->mArr["var_conyuge_empresa_de_trabajo"] 		= "";
				$this->mArr["var_conyuge_estado_civil"] 			= "";
				$this->mArr["var_conyuge_nombres"]					= "";
				$this->mArr["var_conyuge_primer_apellido"]			= "";
				$this->mArr["var_conyuge_segundo_apellido"]			= "";
				$this->mArr["var_conyuge_id_tipo"]					= "";
				$this->mArr["var_conyuge_id_clave"]					= "";
				$this->mArr["var_conyuge_id_ife"]					= "";
			}
			//==================
			if($xODom != null){
				$this->mArr["variable_sin_ciudad_domicilio_del_socio"]	= $xODom->getDireccionBasica();
				$this->mArr["variable_ciudad_del_socio"]				= $xH->Out($xODom->getCiudad(), OUT_HTML);
				$this->mArr["variable_persona_domicilio_municipio"]		= $xODom->getMunicipio();
				$this->mArr["variable_vivienda_callenumero"]			= $xODom->getCalleConNumero();
				$this->mArr["variable_vivienda_colonia"]				= $xODom->getColonia();
				$this->mArr["variable_vivienda_poblacion"]				= $xODom->getCiudad();
				$this->mArr["variable_vivienda_estado"]					= $xODom->getEstado();
				$this->mArr["variable_vivienda_municipio"]				= $xODom->getMunicipio();
				$this->mArr["variable_vivienda_codigo_postal"]			= $xODom->getCodigoPostal();
				$this->mArr["variable_persona_domicilio_convencional"]	= $xODom->getDireccionBasica(true);
				$this->mArr["variable_vivienda_calle"]					= $xODom->getCalle();
				$this->mArr["variable_vivienda_numext"]					= $xODom->getNumeroExterior();
				$this->mArr["variable_vivienda_numint"]					= $xODom->getNumeroInterior();
				$this->mArr["variable_vivienda_telmov"]					= $xODom->getTelefonoMovil();
				$this->mArr["var_vivienda_referencia"]					= $xODom->getReferencia();
				
			}
			//===================== Datos de Actividad Economica
			$this->mArr["variable_actividad_economica_del_socio"]	= "";
			$this->mArr["variable_estado_de_actividad_economica"]	= "";
			$this->mArr["variable_municipio_de_actividad_economica"]= "";
			$this->mArr["variable_nombre_de_la_empresa"]			= "";
			$this->mArr["variable_socio_actividad_ciudad"]			= "";
			$this->mArr["variable_socio_actividad_telefono"]		= "";
			$this->mArr["var_persona_ae_describe"]					= "";
			$this->mArr["var_persona_ae_ncomercial"]				= "";
			$this->mArr["var_persona_ae_tiempo"]					= "";
			$this->mArr["vars_persona_ae_clave_scian"]				= "";
			$this->mArr["var_persona_ae_fechainicial"]				= "";
			$this->mArr["var_persona_ae_fechaverif"]				= "";
			
			$this->mArr["vars_pers_ae_dom_calle"]					= "";
			$this->mArr["vars_pers_ae_dom_numext"]					= "";
			$this->mArr["vars_pers_ae_dom_numint"]					= "";
			$this->mArr["vars_pers_ae_dom_colonia"]					= "";
			$this->mArr["vars_pers_ae_dom_codigopos"]				= "";
			$this->mArr["vars_pers_ae_dom_municipio"]				= "";
			$this->mArr["vars_pers_ae_dom_ent_fed"]					= "";
			$this->mArr["vars_pers_ae_dom_referencia"]				= "";
			//$this->mArr["vars_pers_ae_dom_referencia"]				= "";
			if($xOAE === null){
				
			} else {
				
				$this->mArr["variable_actividad_economica_del_socio"]	= $xOAE->getPuesto();
				$this->mArr["variable_estado_de_actividad_economica"]	= $xH->Out($xOAE->getNombreEstado(), OUT_HTML);
				$this->mArr["variable_municipio_de_actividad_economica"]= $xH->Out($xOAE->getNombreMunicipio(), OUT_HTML);
				$this->mArr["variable_nombre_de_la_empresa"]			= $xOAE->getNombreEmpresa();
				$this->mArr["variable_socio_actividad_ciudad"]			= $xH->Out($xOAE->getLocalidad(), OUT_HTML);
				$this->mArr["variable_socio_actividad_telefono"]		= $xOAE->getTelefono();
				$this->mArr["var_persona_ae_describe"]					= $xOAE->getDescripcionAct(true);
				$this->mArr["var_persona_ae_ncomercial"]				= $xOAE->getNombreComercial();
				$this->mArr["var_persona_ae_tiempo"]					= "";
				$xTiempo												= new cTiempoAntiguedad($xOAE->getClaveDeAntiguedad());
				if($xTiempo->init() == true){
					$this->mArr["var_persona_ae_tiempo"]				= $xTiempo->getNombre();
				}
				$this->mArr["vars_persona_ae_clave_scian"]				= $xOAE->getClaveActividadSCIAN();
				$this->mArr["var_persona_ae_fechainicial"]				= $xFecha->getFechaCorta($xOAE->getFechaIngreso());
				$this->mArr["var_persona_ae_fechaverif"]				= $xFecha->getFechaCorta($xOAE->getFechaVerificacion());
				
				$xDomAE													= new cPersonasVivienda($cSoc->getClaveDePersona(), $xTipoViv->TIPO_FISCAL);
				$xDomAE->setID($xOAE->getClaveDeDomicilio());
				if($xDomAE->init() == true){
					$this->mArr["vars_pers_ae_dom_calle"]					= $xDomAE->getCalle();
					$this->mArr["vars_pers_ae_dom_numext"]					= $xDomAE->getNumeroExterior();
					$this->mArr["vars_pers_ae_dom_numint"]					= $xDomAE->getNumeroInterior();
					$this->mArr["vars_pers_ae_dom_colonia"]					= $xDomAE->getColonia();
					$this->mArr["vars_pers_ae_dom_codigopos"]				= $xDomAE->getCodigoPostal();
					$this->mArr["vars_pers_ae_dom_municipio"]				= $xDomAE->getMunicipio();
					$this->mArr["vars_pers_ae_dom_ent_fed"]					= $xDomAE->getEstado();
					$this->mArr["vars_pers_ae_dom_referencia"]				= $xDomAE->getReferencia();
				}
				
			}
			//Domicilio Fiscal y Convencional
			$xDomF			= new cPersonasVivienda($clave_de_persona, PERSONAS_TIPO_DOM_FISCAL);
			$this->mArr["variable_persona_domicilio_fiscal"]			= "";
			if($xDomF->init() == true){
				$this->mArr["variable_persona_domicilio_fiscal"]		= $xDomF->getDireccionBasica(true);
			}
			//Domicilio.- Vivienda en Construccion
			$this->mArr["variable_persona_domicilio_construye"]			= "";
			$xDomEC			= new cPersonasVivienda($clave_de_persona);
			if($xDomEC->initByEnConstruccion() == true){
				$this->mArr["variable_persona_domicilio_construye"]		= $xDomEC->getDireccionBasica(true);
			}
			
			$this->mArr["variable_nombre_del_socio"] 				= $cSoc->getNombreCompleto(OUT_HTML);
			$this->mArr["variable_persona_nombre_completo"]			= $this->mArr["variable_nombre_del_socio"];
			$this->mArr["variable_persona_telefono_principal"]		= $cSoc->getTelefonoPrincipal();
			
			$this->mArr["variable_persona_primer_apellido"]			= $cSoc->getApellidoPaterno();
			$this->mArr["variable_persona_segundo_apellido"]		= $cSoc->getApellidoMaterno();
			$this->mArr["variable_persona_nombres"]					= $cSoc->getNombre();
			//
			$this->mArr["variable_persona_lista_beneficiarios"]		= $tblCBen->Show("TR.beneficiarios");				///NEW
			
	
			$firmas_de_respsolidarios    								= $cSoc->getCoResponsables("firmas");
			$this->mArr["variable_responsable_solidario_en_fichas"]  	= $fichas_de_respsolidarios;
			$this->mArr["variable_firmas_de_obligados_solidarios"] 		= $firmas_de_respsolidarios;
			$this->mArr["var_persona_declara_completo"]					= $this->mArr["variable_nombre_del_socio"];
			if($cSoc->getEsPersonaFisica() == false){
				$this->mArr["var_persona_declara_completo"]				= $this->mArr["variable_nombre_del_socio"] . "<br />Firma y Nombre/Denominacion o Razon Social y Nombre del Representante Legal: " . $this->mArr["var_pers_rep_legals"];
			}
			//Firma Integrada
			$texto_firma 			= contrato($idFormaFirmaP, "texto_del_contrato");
			foreach ($this->mArr as $key => $value) {
				$texto_firma		= str_replace($key, $value, $texto_firma);
			}
			$this->mFirmasAvalesH[$clave_de_persona]				= $texto_firma; //Firmas en forma horizontal
			
			$this->mArr["variable_pm_num_notaria"]					= "";
			$this->mArr["variable_pm_nom_notario"]					= "";
			
			//====================== Datos de constitucion
			if($cSoc->getEsPersonaFisica() == false){
				$xConst					= new cPersonasMoralesDatosExt();
				if($xConst->initByPersona($cSoc->getClaveDePersona()) == true){
					$this->mArr["variable_pm_num_notaria"]			= $xConst->getNumeroNotaria();
					$this->mArr["variable_pm_nom_notario"]			= $xConst->getNombreNotario();
				}
			} else {
				$xEstadoCiv											= new cPersonasEstadoCivil($cSoc->getEstadoCivil());
				if($xEstadoCiv->init() == true){
					$this->mArr["variable_estado_civil_del_socio"]	= $xEstadoCiv->getNombre();
				}
			}
			$this->getListadoDeReferencias($cSoc->getClaveDePersona());
			$this->setConPersonasConRelacionPatrimonial();
		} else {
			//if(MODO_DEBUG == true){ setLog($cSoc->getMessages()); } 
		}
				


	}
	function setEmpresaPeriodo($empresa, $idnomina = false){
		$xEmp	= new cEmpresas($empresa); $xEmp->init();
		$xPer	= $xEmp->getOPeriodo(false, false, $idnomina);
		$xTPer	= new cPeriocidadDePago($xPer->periocidad()->v()); $xTPer->init();
		$xF		= new cFecha();
		
		$this->mArr["variable_nombre_de_empresa"]		= $xEmp->getNombreCorto() . " - " . $xEmp->getNombre();
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
	function addVars($nombre, $valor){
		$this->mArr[$nombre]	= $valor;
	}
	function setProcesarVars($arrVars = false){
		$xTip		= new cTipos();
		
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
				$txt2		= "";
				foreach ($DEsq as $key => $idx){
					$css	= (isset($eCss[$idx])) ? " class=\"". $eCss[$idx] . "\" " : "";
					$txt	.= (isset($rows[$idx]))? "<td$css>" . $rows[$idx] . "</td>" : "";
					
					if($idx == "concepto_del_movimiento"){
						$txt2	.= str_pad($rows[$idx], ceil(48*0.7), ' ');
					}
					if($idx == "monto_del_movimiento"){
						$txt2	.= str_pad($rows[$idx], ceil(48*0.3), " ", STR_PAD_LEFT);
						$txt2	.= "\r\n";
					}
				}
				$txt		.= "</tr>";
				//setLog($txt);
				$tbl		.= ($this->mOut == OUT_TXT) ? $txt2 : $txt;
			}
			$head			= "<thead><tr>";
			$h2				= "";
			
			foreach ($DEsq as $key => $idx){
				//si la clave es destino, usar
				$width		= ( isset( $eWidth[ $idx ] ) ) ? " style=\"width:" . $eWidth[ $idx ] . "\" " : "";
				$title		= ( isset( $eTit[ $idx ] ) ) ?  $eTit[ $idx ] : "";
				$head		.= "<th scope='col'$width>$title</th>";
				if($idx == "concepto_del_movimiento"){
					$txt2	.= str_pad($rows[$idx], ceil(48*0.35), ' ', STR_PAD_BOTH);
					$txt2	.= ".";
				}
				if($idx == "monto_del_movimiento"){
					$txt2	.= str_pad($rows[$idx], ceil(48*0.15), " ", STR_PAD_BOTH);
					$txt2	.= "\r\n";
				}
			}
			$head			.= "</tr></thead>";
			$tbl			= ($this->mOut == OUT_TXT) ? $h2 . $tbl . "\r\n" : "<table>$head<tbody>$tbl</tbody></table>";
			$this->mTxt		= str_replace("_AREA_DE_MOVIMIENTOS_", $tbl, $this->mTxt);
		}
		
	}
	function setCredito($credito, $datos = false, $periodo  = false){
		$this->mDocumento		= $credito;
		$xFDE					= new cFecha();
		$xLng					= new cLang();
		$xQL					= new MQL();
		$cCred 					= new cCredito($credito); $cCred->init($datos);
		$xCant					= new cCantidad();
		$xCredEst				= new cCreditosEstadisticas();
		
		$idsolicitud			= $credito;
		$DCred					= $cCred->getDatosDeCredito();
		$DProd					= $cCred->getOProductoDeCredito();
		$OOParam				= new cProductoDeCreditoOtrosDatosCatalogo();
		$numero_de_socio		= $cCred->getClaveDePersona();
		$this->mPersona			= $numero_de_socio;
		$this->VARS_IDCREDITO	= $credito;
		$this->VARS_IDPERSONA	= $numero_de_socio;
		
		$this->mOCredito		= $cCred;
		$cSoc					= new cSocio($numero_de_socio); $cSoc->init();
		
		$svar_info_cred 		= "";
		$tblInfCred 			= new cFicha(iDE_CREDITO, $idsolicitud);
		$this->setPersona($numero_de_socio, $cSoc->getDatosInArray());
		$svar_info_cred 		= $tblInfCred->show(true);
		//Lista de Beneficiados
		$lst_beneficiados 		= "";


		
		$this->getListadoDeAvales($idsolicitud, $cCred->getMontoAutorizado());
		

		$firmas_de_avales    				= $this->mFirmasAvales;
		//Plan de Pago segun SQL
		$splan_pagos						= $cCred->getPlanDePago(OUT_HTML, true, true);
		//==================================================================================
		$fichas_de_avales					= $this->mFichasAvales;
		$fecha_larga_de_documento			= $xFDE->getFechaLarga($cCred->getFechaDeMinistracion());
		
		$fichas_de_respsolidarios			= "";//TODO: FALTA
		//================================	Firmantes
		$this->getListadoDeFirmantes($idsolicitud, $cCred->getMontoAutorizado());
		
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
		$meses_del_credito					= ($cCred->getPeriocidadDePago() >= CREDITO_TIPO_PERIOCIDAD_MENSUAL) ?   round(($dias_del_credito / 30.416666666666666666666),0) :  round(($dias_del_credito / 30.416666666666666666666),1);
		
		$periocidad							= $cCred->getPeriocidadDePago();
		//Tipo de Credito por SQL
		$SQLTCred 							= "SELECT * FROM creditos_modalidades WHERE idcreditos_modalidades=" . $DCred["tipo_credito"];
		$tipo_de_credito 					= mifila($SQLTCred, "descripcion_modalidades");
		$OProducto							= $cCred->getOProductoDeCredito();

		$nombre_rep_social					= "";
		$codigo_rep_social					= "";
		$nombre_voc_vigila					= "";
		$nombre_del_grupo					= "";
		$domicilio_rep_social				= "";
		if(PERSONAS_CONTROLAR_POR_GRUPO == true){
			if($OProducto->getEsGrupal() == true){
				if($cCred->getClaveDeGrupo() > FALLBACK_CLAVE_DE_GRUPO){
					//Datos del Grupo Solidarios por SQL
					$SQLGAsoc 							= "SELECT * FROM socios_grupossolidarios WHERE idsocios_grupossolidarios=" . $DCred["grupo_asociado"];
					$InfoGrupo							= obten_filas($SQLGAsoc);
					$nombre_rep_social					= $InfoGrupo["representante_nombrecompleto"];
					$codigo_rep_social					= $InfoGrupo["representante_numerosocio"];
					$nombre_voc_vigila					= $InfoGrupo["vocalvigilancia_nombrecompleto"];
					$nombre_del_grupo					= $InfoGrupo["nombre_gruposolidario"];
					$domicilio_rep_social				= domicilio($codigo_rep_social);
				}
			}
		}
		$tabla_asociadas						= "";
		$lista_asociadas						= "";
		$tasa_de_cat							= $cCred->getCAT();
		$DPeriocidad							= new cPeriocidadDePago($cCred->getPeriocidadDePago()); $DPeriocidad->init();
		$monto_con_interes						= "";
		$monto_con_interes_op					= 0;
		$monto_con_interes_letras				= "";
		$OOficial								= $cCred->getOOficial();
		
		if( $DCred["grupo_asociado"] != DEFAULT_GRUPO ){
			$SQL_get_grupo 	= "SELECT `socios_general`.`codigo`, CONCAT(`socios_general`.`nombrecompleto`, ' ', `socios_general`.`apellidopaterno`, ' ', `socios_general`.`apellidomaterno`) AS 'nombre_completo'
									FROM `socios_general` `socios_general` WHERE (`socios_general`.`grupo_solidario` =" . $DCred["grupo_asociado"] . ")";
			$rsg 			=  $xQL->getDataRecord($SQL_get_grupo);
				foreach ($rsg as $rwt) {
					$lista_asociadas .= ", " . $rwt["nombre_completo"];
				}
		}
		if (  EACP_INCLUDE_INTERES_IN_PAGARE == true ){
			if ( $periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
					$monto_con_interes 		= $cCred->getMontoAutorizado() + ($cCred->getInteresDiariogenerado() * $cCred->getDiasAutorizados());
			} else {
					
					if($cCred->getEsAfectable() == false){
						if($cCred->getNumeroDePlanDePagos()>0 ){
							$xPlan			= new cReciboDeOperacion(false, false, $cCred->getNumeroDePlanDePagos());
							if($xPlan->init() == true){
								$monto_con_interes	= $xPlan->getTotal(); //suma del recibo
							}
						}				
					} else {
						$monto_con_interes		= $cCred->getTotalPlanPendiente();
					}
					//setLog("El plan es $monto_con_interes ");
			}
			$monto_con_interes_op		= $monto_con_interes;
			$monto_con_interes_letras	= convertirletras($monto_con_interes);
			$monto_con_interes 			= getFMoney($monto_con_interes);
		}
		$this->mArr["variable_credito_clave"] 								= $credito;
		$this->mArr["variable_credito_descripcion_corta"]					= $cCred->getDescripcion();
		$this->mArr["variable_informacion_del_credito"] 					= $cCred->getFicha();
		$this->mArr["variable_informacion_extendida_del_credito"] 			= $cCred->getFicha(true,"", true);
		//"variable_lista_de_beneficiados" 		=> $lst_beneficiados,

		$this->mArr["variable_tipo_de_credito"] 							= $tipo_de_credito; //consumo comercial etc
		
		$this->mArr["variable_monto2_ministrado_con_intereses_en_letras"] 	= $monto_con_interes_letras;
		$this->mArr["variable_monto2_ministrado_con_intereses"] 			= $monto_con_interes;
		
		$this->mArr["variable_monto_solicitado"] 							= $xCant->moneda($cCred->getMontoSolicitado());
		$this->mArr["variable_letras_monto_solicitado"] 					= $xCant->letras($cCred->getMontoSolicitado());
		
		$this->mArr["variable_monto_ministrado"] 							= getFMoney($monto_ministrado);
		$this->mArr["variable_tasa_mensual_de_interes_ordinario"] 			= $tasa_interes_mensual_ordinario;
		$this->mArr["variable_credito_fecha_de_vencimiento"] 				= $xFDE->getFechaMediana($fecha_de_vencimiento);
		$this->mArr["variable_monto_garantia_liquida"] 						= getFMoney($monto_garantia_liquida);
		$this->mArr["variable_tasa_mensual_de_interes_moratorio"] 			= $tasa_interes_mensual_moratorio . "";
		$this->mArr["variable_tasa_de_garantia_liquida"] 					= $tasa_garantia_liquida . "";
		$this->mArr["variable_plan_de_pagos"] 								= $splan_pagos;
		
		$this->mArr["variable_credito_mensual_dia_pago"] 					= $xFDE->dia($cCred->getFechaPrimeraParc());
		
		$this->mArr["variable_docto_fecha_larga_actual"] 					= $fecha_larga_de_documento;
		$this->mArr["variable_docto_fecha_de_emision"] 						= $fecha_larga_de_documento;
		
		$this->mArr["variable_nombre_de_la_representante_social"] 			= $nombre_rep_social;
		$this->mArr["variable_listado_de_integrantes"] 						= $lista_asociadas;
		$this->mArr["variable_nombre_de_la_vocal_de_vigilancia"] 			= $nombre_voc_vigila;
		$this->mArr["variable_nombre_del_grupo_solidario"] 					= $nombre_del_grupo;
		$this->mArr["variable_domicilio_de_la_representante_social"] 		= $domicilio_rep_social;
		$this->mArr["variable_meses_de_duracion_del_credito"] 				= $meses_del_credito;
		$this->mArr["variable_en_letras_monto_ministrado"] 					= convertirletras($monto_ministrado);
		$this->mArr["variable_credito_fecha_de_ministracion"] 				= $xFDE->getFechaCorta($fecha_de_ministracion);
		$this->mArr["variable_parcialidad_monto"]							= $cCred->getMontoDeParcialidad();

		$this->mArr["variable_tasa_cat"]									= $tasa_de_cat;

		$this->mArr["variable_credito_periocidad"]							= $DPeriocidad->getNombre();
		$this->mArr["variable_credito_monto_parcialidad_fija"]				= getFMoney($cCred->getMontoDeParcialidad());
		$this->mArr["variable_credito_numero_de_pagos"]						= $cCred->getPagosAutorizados();
		$this->mArr["variable_tasa_anual_de_interes_moratorio"]				= ($cCred->getTasaDeMora() * 100) . "%";
		$this->mArr["variable_tasa_anual_de_interes_ordinario"]				= ($cCred->getTasaDeInteres() * 100) . "%";
		
		$this->mArr["variable_letras_tasa_anual_de_interes_moratorio"]		= convertirletras_porcentaje( ($cCred->getTasaDeMora() *100));
		$this->mArr["variable_letras_tasa_anual_de_interes_ordinario"]		= convertirletras_porcentaje( ($cCred->getTasaDeInteres() *100));

		
		//sobreescribir datos de la empresa
		$xEmp																= new cEmpresas($cCred->getClaveDeEmpresa()); $xEmp->init();
		$this->mArr["variable_nombre_de_la_empresa"]						= $xEmp->getNombre();
		$this->mArr["variable_nombre_de_empresa"]							= $xEmp->getNombre();
		
		$this->mArr["variable_fecha_de_primer_pago"]						= $xFDE->getFechaMediana( $cCred->getFechaPrimeraParc());
		$this->mArr["variable_avales_en_fichas"] 							= $fichas_de_avales;
		$this->mArr["variable_2avales_en_fichas"] 							= $this->mFichasAvales2;
		
		$this->mArr["variable_firmas_de_avales"] 							= $firmas_de_avales;
		$this->mArr["variable_avales_autorizacion_central_riesgo"] 			= $this->mFichaRiesgoAv;
		
		$this->mArr["variable_fecha_ultimo_abono"]							= $xFDE->getFechaLarga($cCred->getFechaUltimaParc());
		$this->mArr["variable_fecha_de_primer_abono"]						= $xFDE->getFechaMediana( $cCred->getFechaPrimeraParc());
		$this->mArr["variable_fecha_de_solicitud"]							= $xFDE->getFechaMediana( $cCred->getFechaDeSolicitud());
		//$this->mArr["variable_fecha_de_primer_abono"]						=
		$this->mArr["variable_en_letras_tasa_mensual_de_interes_moratorio"]	= convertirletras_porcentaje( $tasa_interes_mensual_moratorio ); 
		$this->mArr["variable_lista_de_avales_con_domicilio"]				= $this->mLAvalesConDir;
		$this->mArr["vars_creds_lista_de_avales"]							= $this->mListaAvales;
		/*variable_aval1_nombre_completo variable_aval1_domicilio_completo variable_aval1_domicilio_localidad variable_aval1_domicilio_municipio*/
		//Cargar Avales
		$this->mArr["variable_listado_de_garantias"] 					= $this->getListadoDeGarantias();
		//$this->mArr["variable_modalidad_de_credito"]					= $cCred->getOEstado()
		$this->mArr["variable_estado_de_credito"]						= $cCred->getOEstado()->getNombre();
		//$this->mArr["variable_credito_num_de_pago_actual"]				= $cCred->getPeriodoActual();
		$this->mArr["variable_contrato_id_legal"]						= $DProd->getOtrosParametros($OOParam->CONTRATO_ID_LEGAL);
		$this->mArr["variable_contrato_fecha_reg"]						= $DProd->getOtrosParametros($OOParam->CONTRATO_FECHA_REGISTRO);
		$this->mArr["variable_cred_prod_nombre_legal"]					= $DProd->getOtrosParametros($OOParam->PRODUCTO_NOMBRE_LEGAL);
		$this->mArr["variable_cred_prod_tipo_legal"]					= $DProd->getOtrosParametros($OOParam->PRODUCTO_TIPO_LEGAL);
		
		$tasa_comision_por_apertura										= $DProd->getTasaComisionApertura();
		if($tasa_comision_por_apertura > 0){
			
			$mapert														= setNoMenorQueCero( ($cCred->getMontoAutorizado() * $tasa_comision_por_apertura),2 );
			$iapert														= setNoMenorQueCero(($mapert * TASA_IVA),2);
			$this->mArr["var_cred_monto_comapertura"]					= getFMoney($mapert);
			$this->mArr["var_cred_iva_comapertura"]						= getFMoney($iapert);
			$this->mArr["var_cred_total_comapertura"]					= getFMoney(($mapert + $iapert));
		} else {
			$this->mArr["var_cred_monto_comapertura"]					= 0;
			$this->mArr["var_cred_iva_comapertura"]						= 0;
			$this->mArr["var_cred_total_comapertura"]					= 0;
		}
		
		$this->mArr["variable_cred_producto_nombre"]					= $DProd->getNombre();
		$this->mArr["variable_producto_comision_apertura"]				= $tasa_comision_por_apertura;
		$this->mArr["variable_nombre_oficial_de_credito"]				= $OOficial->getNombreCompleto();
		$this->mArr["variable_credito_destino"]							= $cCred->getClaveDeDestino();

		$this->mArr["variable_credito_plan_exigible"]					= $cCred->getTotalPlanExigible();
		$this->mArr["variable_credito_plan_pendiente"]					= $monto_con_interes;
		$xMontos														= $cCred->getOMontos();
		
		
		if($xMontos == null){
			$this->mArr["var_credito_monto_ints_calc"]					= 0;
		} else {
			$this->mArr["var_credito_monto_ints_calc"]					= getFMoney($xMontos->getInteresNormalCalculado());
		}
		//
		$this->mArr["vars_creds_aut_dictamen"]							= $cCred->getRazonAutorizacion();
		$this->mArr["vars_creds_fecha_autoriza"]						= $xFDE->getFechaMX($cCred->getFechaDeAutorizacion());
		$this->mArr["vars_creds_c_fecha_autoriza"]						= $xFDE->getFechaCorta($cCred->getFechaDeAutorizacion());
		$this->mArr["vars_creds_l_fecha_autoriza"]						= $xFDE->getFechaLarga($cCred->getFechaDeAutorizacion());
		
		//2018-02-10
		$this->mArr["vars_creds_saldo_actual"]							= $cCred->getSaldoActual();
		$this->mArr["vars_creds_monto_autorizado"]						= $cCred->getMontoAutorizado();
		$this->mArr["vars_creds_idoficial_credito"]						= $cCred->getClaveDeOficialDeCredito();
		//setLog("Monto con interes $monto_con_interes ");
		$this->mArr["variable_credito_destino"]							= "";
		$xDest					= new cCreditosDestinos();
		if($xDest->init() == true){
			$this->mArr["variable_credito_destino"]						= $xDest->getNombre();
		}
		if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
			$xRuls	= new cReglasDeValidacion();
			if($xRuls->empresa($cCred->getClaveDeEmpresa()) == true){
				$this->setEmpresa($cCred->getClaveDeEmpresa());
			}
		}

		
		
		//if($cCred->getClaveDeEmpresa())
		//================= Parche recibos
		if(!isset($this->mArr["variable_monto_del_recibo"])){
			$xCant		= new cCantidad($cCred->getMontoAutorizado());
			$this->mArr["variable_monto_del_recibo_en_letras"]	= $xCant->letras();
			$this->mArr["variable_monto_del_recibo"] 			= $xCant->moneda();
		}
		//FIXXME FIXX TODO: XXX: Cambiar
		$monto_seguro											= setNoMenorQueCero( ($cCred->getMontoAutorizado() * 0.006),2 );
		$xCant2		= new cCantidad($monto_seguro);
		$this->mArr["variable_credito_seguro_monto"]			= $xCant2->moneda($monto_seguro);
		$this->mArr["variable_credito_letra_seguro_monto"]		= $xCant2->letras($monto_seguro);
		$html													= "";
		$this->mArr["variable_cred_leyenda_con_aval"]			= "";
		$this->mArr["variable_cred_leyenda_sin_aval"]			= "DUPLICADO";
		foreach ($this->mFirmasAvalesH as $id => $cnt){
			$html												.= "<td>$cnt</td>";
			$this->mArr["variable_cred_leyenda_con_aval"]		= "TRIPLICADO";
			$this->mArr["variable_cred_leyenda_sin_aval"]		= "";			
		}
		$html													= "<table><tr>$html</tr></table>";
		$this->mArr["variable_persona_firma_integrada"]			= $html;
		
		$this->mArr["variable_2firmas_de_avales"] 				= $this->mFirmasAvales2;
		$this->mArr["variable_3firmas_de_avales"] 				= $this->mFirmasAvales3;
		$this->mArr["variable_5firmas_de_avales"] 				= $this->mFicha5Avales;
		$this->mArr["variable_6firmas_de_avales"] 				= $this->mFicha6Avales;
		
		$plan_de_pagos											= $cCred->getNumeroDePlanDePagos();
		$this->mArr["variable_credito_idplan_pagos"]			= $plan_de_pagos;
		$this->mArr["variable_cred_calendario_pagos_1"]			= "";
		$this->mArr["variable_cred_calendario_pagos_2"]			= "";
		if($plan_de_pagos > 0){
			$xPlan 	= $cCred->getOPlanDePagos();
			$this->mArr["variable_cred_calendario_pagos_1"]		= $xPlan->getVersionImpresa(false, false, false);
			$this->mArr["variable_cred_calendario_pagos_2"]		= $xPlan->getVersionImpresa(false, false, false,false, false, true);
		}
		//==================================== Datos del Ultimo Pago
		$this->mArr["variable_cred_rec_liq_clave"]				= "";
		$this->mArr["variable_cred_rec_liq_fecha"]				= "";
		$this->mArr["variable_cred_rec_liq_monton"]				= "";
		$this->mArr["variable_cred_rec_liq_montol"]				= "";
		if($cCred->getEsCreditoYaAfectado() == true AND $cCred->getSaldoActual()<= 0){
			$this->mArr["variable_cred_rec_liq_clave"]			= $cCred->getReciboDeLiquidacion();
			$xRec	= new cReciboDeOperacion(false, false, $cCred->getReciboDeLiquidacion());
			if($xRec->init() == true){
				$this->mArr["variable_cred_rec_liq_monton"]		= $xRec->getTotal();
				$this->mArr["variable_cred_rec_liq_montol"]		= $xCant->letras($xRec->getTotal());
				$this->mArr["variable_cred_rec_liq_fecha"]		= $xFDE->getFechaLarga($xRec->getFechaDeRecibo());
			}
		}
		//Otros datos
		$Cat		= $cCred->OCatOtrosDatos();
		$dd			= $Cat->getDatosInArray();
		foreach($dd as $idx => $cdx){
			$this->mArr["variable_cred_" . strtolower($idx)]	= $cCred->getOtroDatos($idx);
		}
		//si es de arrendamiento
		$this->mArr["var_vehiculo_serie"]		= "";
		$this->mArr["var_vehiculo_motor"]		= "";
		$this->mArr["var_vehiculo_color"]		= "";
		$this->mArr["var_vehiculo_placas"]		= "";
		$this->mArr["var_vehiculo_describe"]	= "";
		$this->mArr["var_vehiculo_niv"]			= "";
		
		$this->mArr["var_vehiculo_vec_monto"]	= "";
		$this->mArr["var_vehiculo_vec_iva"]		= "";
		$this->mArr["var_vehiculo_vec_total"]	= "";
		
		$this->mArr["var_proveedor_nombre"]		= "";
		$this->mArr["var_proveedor_direccion"]	= "";
		$this->mArr["var_proveedor_telefono"]	= "";
		
		$this->mArr["var_agente_nombre"]		= "";
		$this->mArr["var_agente_direccion"]		= "";
		$this->mArr["var_agente_telefono"]		= "";
		
		
		$this->mArr["var_leasing_plan"]			= "";
		$this->mArr["var_leasing_residualplan"]	= "";
		
		if($cCred->getEsArrendamientoPuro() == true){
			$this->setOriginacionLeasing($cCred->getClaveDeOrigen());
			//------ Iniciar el Activo
			$xAct	= new cLeasingActivos();
			if($xAct->initForContract($cCred->getClaveDeCredito()) == true ){
				$this->mArr["var_vehiculo_serie"]			= $xAct->getSerie();
				$this->mArr["var_vehiculo_motor"]			= $xAct->getMotor();
				$this->mArr["var_vehiculo_color"]			= $xAct->getColor();
				$this->mArr["var_vehiculo_factura"]			= $xAct->getFactura();
				$this->mArr["var_vehiculo_placas"]			= $xAct->getPlacas();
				$this->mArr["var_vehiculo_modelo"]			= $xAct->getModelo();
				
				$this->mArr["var_vehiculo_vec_monto"]		= getFMoney($xAct->getMontoVEC());
				$this->mArr["var_vehiculo_vec_iva"]			= getFMoney(($xAct->getMontoVEC() * TASA_IVA));
				$totalvec									= round(($xAct->getMontoVEC() * TASA_IVA),2) + $xAct->getMontoVEC();
				$this->mArr["var_vehiculo_vec_total"]		= getFMoney( $totalvec );
				
				$this->mArr["var_vehiculo_vec_letrastotal"]	= $xCant->letras($totalvec);
				
				$this->mArr["var_vehiculo_describe"]		= $xAct->getNombre();
				$this->mArr["var_vehiculo_niv"]				= $xAct->getNIV();
				
				//=============== Datos del Proveedor
				$xSocP	= new cSocio($xAct->getClaveDeProveedor());
				if($xSocP->init() == true){
					$this->mArr["var_proveedor_nombre"]		= $xSocP->getNombreCompleto();
					$this->mArr["var_proveedor_direccion"]	= $xSocP->getDomicilio();
					$this->mArr["var_proveedor_telefono"]	= $xSocP->getTelefonoPrincipal();
					$this->mArr["var_leasing_residualplan"]	= "<table class='plan_de_pagos'><thead><tr><th>Pago</th><th>Fecha</th><th>Monto</th></tr></thead><tbody><tr><td>Unico</td><td>" . $cCred->getFechaDevencimientoLegal() . "</td><td>" . getFMoney($xAct->getMontoVEC()) . "</td></tr></tbody></table>";
				}
			}
			//=============== Datos
			$rentas_sin_iva									= $monto_con_interes_op * (1/(1+TASA_IVA));
			$this->mArr["var_leasing_si_rentas"]			= $xCant->moneda($rentas_sin_iva);
			$this->mArr["var_leasing_si_letras_rentas"]		= $xCant->letras($rentas_sin_iva);
			
			$this->mArr["var_leasing_rentas"]				= $xCant->moneda($monto_con_interes_op);
			$this->mArr["var_leasing_letras_rentas"]		= $xCant->letras($monto_con_interes_op);
			
			//=============== Numero de Plan de Pagos
			$xPlan 	= $cCred->getOPlanDePagos();
			$xPlan->setClaveDeCredito($cCred->getClaveDeCredito());
			$this->mArr["var_leasing_plan"]			= $xPlan->getVersionImpresaLeasing();
			$this->mArr["var_leasing_2plan"]		= $xPlan->getVersionImpresaLeasing(true);

		}
		
	}
	function setRecibo($recibo){
		$xRec		= new cReciboDeOperacion(false, false, $recibo);
	
		if($xRec->init() == true){
			$this->setPersona($xRec->getCodigoDeSocio());
			$this->VARS_IDRECIBO	= $recibo;
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
					$this->setCredito($xRec->getCodigoDeDocumento(), false, $xRec->getPeriodo());
					
					break;
				case RECIBOS_ORIGEN_COLOCACION:
					$this->setCredito($xRec->getCodigoDeDocumento(), false, $xRec->getPeriodo());
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
			$this->mArr["variable_numero_del_recibo"]			= $xRec->getCodigoDeRecibo();
			$this->mArr["variable_recibo_sdo_historico"]		= $xRec->getSaldoHistorico();
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
			`operaciones_mvtos`.`tipo_operacion`		AS `tipo_de_movimiento`,
			`operaciones_mvtos`.`saldo_actual`			AS `saldo_final`
			FROM
			`operaciones_mvtos` `operaciones_mvtos`	INNER JOIN `operaciones_tipos` `operaciones_tipos` ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.`idoperaciones_tipos`
			WHERE (`operaciones_mvtos`.`recibo_afectado` = $recibo ) ORDER BY `operaciones_mvtos`.`afectacion_real` DESC";
			$rs		= $QL->getDataRecord($sqlmvto);
			//setLog($sqlmvto);
			//destino_del_movimiento
			foreach ($rs as $row){
				$rwx											= $row;
				$tipo											= $row["tipo_de_movimiento"];
				$docto											= $row["numero_de_documento"];
				$rwx["monto_del_movimiento"]					= getFMoney($row["monto_del_movimiento"] * $row["naturaleza_del_movimiento"] * $afectEfvo);
				$rwx["destino_del_movimiento"]					= "&nbsp;" . $row["numero_de_documento"];
				$this->mArr["variable_movimiento_saldo_final"]	= getFMoney($row["saldo_final"]);
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
			$rs		= null;
		}
	}
	function setCuentaDeCaptacion($numero_de_cuenta){
		$xCta	= new cCuentaDeCaptacion($numero_de_cuenta);
		$xCta->init();
		$xCant	= new cCantidad($xCta->getSaldoActual());
		$xF		= new cFecha();
		$this->VARS_IDCUENTA	= $numero_de_cuenta;
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
		$this->setPersona($xCta->getClaveDePersona());
		
	}
	function setAvalDeCredito($clave_de_aval, $contar = "", $datos = false ){
		/*variable_aval1_nombre_completo variable_aval1_domicilio_completo variable_aval1_domicilio_localidad variable_aval1_domicilio_municipio*/
	}
	function setGarantiaDeCredito($clave_de_garantia, $contar = "1", $datos = false ){
		$xTG	= new cCreditos_tgarantias();
		$xTV	= new cCreditos_tvaluacion();		
		$xCG	= new cCreditos_garantias();
		
		$this->mArr["variable_credito_garantiareal" . $contar . "_ficha"]		= "";
		$xG		= new cCreditosGarantias($clave_de_garantia);
		if($xG->init($datos) == true){
			$this->mArr["variable_credito_garantiareal" . $contar . "_ficha"]	= $xG->getFicha();
			$datos	= $xG->getDatosInArray();
		}		
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
		$this->mArr["variable_credito_garantiareal" . $contar . "_descripcion"] 	=  $xCG->descripcion()->v();
		$this->mArr["variable_credito_garantiareal" . $contar . "_observaciones"] 	=  $xCG->observaciones()->v();
	}
	function getListadoDeGarantias($credito = false){
		$credito		= (setNoMenorQueCero($credito) <= 0) ? $this->mDocumento : $credito;
		$xLi			= new cSQLListas();
		$xTabla			= new cTabla($xLi->getListadoDeGarantiasReales(false, $credito));
		return $xTabla->Show("TR.Garantias");
	}
	
	function getListadoDeAvales($credito, $monto_del_credito = 0){
		$mSQL		= new cSQLListas();
		$mql		= new MQL();
		$xF			= new cFecha();
		$sql 		= $mSQL->getListadoDeAvales($credito, $this->mPersona);
		$rs			= $mql->getDataRecord($sql);
		
		//setLog($sql);
		$forma		= 8001;
		$firma		= 8002;
		$firma3		= 8003;
		$ficha2		= 8004;
		
		$ficha5		= 8010;
		
		$friesgo	= 5001;
		
		$ficha6		= 8011;
		
		
		
		$cficha		= "";
		$cficha2	= "";
		
		$cfirmas	= "";
		$cfirmas2	= "";
		$cfirmas3	= "";
		
		$cfirmas5	= "";
		$cfirmas6	= "";
		
		$criesgo	= "";
		$clista		= "";
		$itx		= 1;
		//$xAval		= new cSocios_relaciones();
		if($mql->getNumberOfRows()<=0){
			$this->mArr["aval_nombre_completo"] 		= "";
			$this->mArr["aval_domicilio_convencional"]	= "";
			$this->mArr["aval_email"]					= "";
			$this->mArr["aval_representante"]			= "";
			
			$this->mArr["aval_domicilio_localidad"] 	= "";
			$this->mArr["aval_direccion_calle_y_numero"]= "";
			$this->mArr["aval_direccion_estado"] 		= "";
			$this->mArr["aval_direccion_municipio"] 	= "";
			$this->mArr["aval_direccion_colonia"] 		= "";
			$this->mArr["aval_direccion_codigopostal"] 	= "";
			
			$this->mArr["aval_direccion_completa"] 		= "";
			$this->mArr["aval_ocupacion"] 				= "";
			$this->mArr["aval_telefono_principal"]		= "";
			$this->mArr["aval_fecha_de_nacimiento"] 	= "";
			$this->mArr["aval_id_fiscal"] 				= "";
			$this->mArr["aval_id_poblacional"] 			= "";
			$this->mArr["aval_lugar_de_nacimiento"] 	= "";
			$this->mArr["aval_empresa_de_trabajo"] 		= "";
			$this->mArr["aval_estado_civil"] 			= "";
			$this->mArr["aval_tipo_de_relacion"] 		= "";
			$this->mArr["aval_tipo_de_parentesco"] 		= "";
			$this->mArr["aval_tipo_de_identificacion"] 	= "";
			$this->mArr["aval_id_identificacion"] 		= "";
			
			$this->mArr["aval_porcentaje_relacionado"]	= "";
			
			$this->mArr["aval_nombres"] 				= "";
			$this->mArr["aval_primer_apellido"] 		= "";
			$this->mArr["aval_segundo_apellido"] 		= "";
		}
		foreach ($rs as $rows){
			$persona	= $rows["numero_socio"];
			$idrelacion	= $rows["num"];
			$xSoc		= new cSocio($persona);
			$xRel		= new cPersonasRelaciones($idrelacion, $persona); $xRel->init();

			if( $xSoc->init() == true ){
				$avalDom	= $xSoc->getODomicilio();
				$avalEc		= $xSoc->getOActividadEconomica();
				
				$DEstadoCivil											= new cSocios_estadocivil();
				$DEstadoCivil->setData( $DEstadoCivil->query()->initByID($xSoc->getEstadoCivil()) );
				$porcentaje	= setNoMenorQueCero( $xRel->getPorcientorelacionado() );
				if($porcentaje <= 0){
					if($xRel->getMontoRelacionado() > 0){
						$porcentaje	= $xRel->getMontoRelacionado() / $monto_del_credito; 
					} else {
						$porcentaje	= "1";
					}
				}
				$this->mArr["variable_aval" . $itx . "_nombre_completo"] 	= $xSoc->getNombreCompleto();
				
				$this->mArr["variable_aval" . $itx . "_nombres"] 			= $xSoc->getNombre();
				$this->mArr["variable_aval" . $itx . "_primer_apellido"] 	= $xSoc->getApellidoPaterno();
				$this->mArr["variable_aval" . $itx . "_segundo_apellido"] 	= $xSoc->getApellidoMaterno();
				
				$this->mArr["variable_aval" . $itx . "_estado_civil"] 		= $DEstadoCivil->descripcion_estadocivil()->v();
				$this->mArr["variable_aval" . $itx . "_id_fiscal"] 			= $xSoc->getRFC(true);
				$this->mArr["variable_aval" . $itx . "_id_poblacional"] 	= $xSoc->getCURP(true);
				$this->mArr["variable_aval" . $itx . "_telefono_principal"] = $xSoc->getTelefonoPrincipal();
				$this->mArr["variable_aval" . $itx . "_email"] 				= $xSoc->getCorreoElectronico();
				
				$this->mArr["aval_telefono_principal"]						= $xSoc->getTelefonoPrincipal();	
				
				$xTI	= new cPersonasDocumentacionTipos($xSoc->getTipoDeIdentificacion()); $xTI->init();
				$this->mListaAvales					.= ($this->mListaAvales == "") ? $xSoc->getNombreCompleto() : ", " . $xSoc->getNombreCompleto();
				$vars	= array(
					"aval_nombre_completo" 			=> $xSoc->getNombreCompleto(),
					"aval_nombres"					=> $xSoc->getNombre(),
					"aval_primer_apellido"			=> $xSoc->getApellidoPaterno(),
					"aval_segundo_apellido"			=> $xSoc->getApellidoMaterno(),
					"aval_domicilio_convencional"	=> "",
					"aval_email"					=> $xSoc->getCorreoElectronico(),
					"aval_representante"			=> $xSoc->getNombreDelRepresentanteLegal(),
					"aval_domicilio_localidad" 		=> "",
					"aval_direccion_calle_y_numero" => "",
					"aval_direccion_estado" 		=> "",
					
					"aval_direccion_colonia" 		=> "",
					"aval_direccion_municipio" 		=> "",
					"aval_direccion_codigopostal" 	=> "",
						
					"aval_direccion_completa" 		=> $xSoc->getDomicilio(),
					"aval_ocupacion" 				=> $xSoc->getTituloPersonal(),
					"aval_telefono_principal"		=> $xSoc->getTelefonoPrincipal(),
					"aval_fecha_de_nacimiento" 		=> $xF->getFechaCorta($xSoc->getFechaDeNacimiento()),
					"aval_id_fiscal" 				=> $xSoc->getRFC(true),
					"aval_id_poblacional" 			=> $xSoc->getCURP(true),
					"aval_id_identificacion"		=> $xSoc->getClaveDeIdentificacion(),
					
					"aval_lugar_de_nacimiento" 		=> $xSoc->getLugarDeNacimiento(),
					"aval_empresa_de_trabajo" 		=> "",
					"aval_estado_civil" 			=> $DEstadoCivil->descripcion_estadocivil()->v(),
					"aval_tipo_de_relacion" 		=> $xRel->getNombreRelacion(),
					"aval_tipo_de_parentesco" 		=> $xRel->getNombreParentesco(),
					"aval_tipo_de_identificacion" 	=> $xTI->getNombre(),
					"aval_porcentaje_relacionado"	=> ($porcentaje * 100)
				);
				if($avalDom != null){
					$vars["aval_direccion_completa"] 							= $avalDom->getDireccionBasica(true);
					$vars["aval_domicilio_localidad"] 							= $avalDom->getLocalidad(); //TODO: verificar Validez
					$vars["aval_direccion_calle_y_numero"]						= $avalDom->getCalleConNumero();
					$vars["aval_direccion_estado"] 								= $avalDom->getEstado(OUT_TXT);
					$vars["aval_direccion_colonia"] 							= $avalDom->getColonia();
					$vars["aval_direccion_municipio"] 							= $avalDom->getMunicipio();
					$vars["aval_direccion_codigopostal"] 						= $avalDom->getCodigoPostal();
					
					$this->mArr["variable_aval" . $itx . "_domicilio_completo"] = $vars["aval_direccion_completa"];
					$this->mArr["variable_aval" . $itx . "_domicilio_localidad"]= $vars["aval_domicilio_localidad"];
					$this->mArr["variable_aval" . $itx . "_domicilio_estado"] 	= $vars["aval_direccion_estado"]; 
					$vars["aval_direccion_localidad"] 							= $vars["aval_domicilio_localidad"];
					$vars["aval_domicilio_convencional"]						= $avalDom->getDireccionBasica(true);
				}
				if($avalEc != null){
					$vars["aval_ocupacion"] 				= $xSoc->getOActividadEconomica()->getPuesto();
					$vars["aval_empresa_de_trabajo"] 		= $xSoc->getOActividadEconomica()->getNombreEmpresa();
				}
				//Iniciar Domiclio Fiscal y Convencional
				$xODomFiscl			= $xSoc->getODomicilio(PERSONAS_TIPO_DOM_FISCAL);
				if($xODomFiscl != null){
					$vars["aval_domicilio_fiscal"]			= $xODomFiscl->getDireccionBasica(true);
				}
				//hacer merge con el Principal
				//TODO: Verificar problemas y conflictos
				$this->mArr			= array_merge($vars, $this->mArr);
				//
				$texto_ficha 		= contrato($forma, "texto_del_contrato");
				$texto_ficha2 		= contrato($ficha2, "texto_del_contrato");
				
				$texto_firma 		= contrato($firma, "texto_del_contrato");
				$texto_firma3 		= contrato($firma3, "texto_del_contrato");
				$texto_aut			= contrato($friesgo, "texto_del_contrato");
				$texto_5ficha		= contrato($ficha5, "texto_del_contrato");
				$texto_6ficha		= contrato($ficha6, "texto_del_contrato");
				
				$vars				= array_merge($vars, $this->mBasicVars);
				foreach ($vars as $key => $value) {
					$texto_ficha	= str_replace($key, $value, $texto_ficha);
					$texto_ficha2	= str_replace($key, $value, $texto_ficha2);
					
					$texto_firma	= str_replace($key, $value, $texto_firma);
					$texto_aut		= str_replace($key, $value, $texto_aut);
					$texto_firma3	= str_replace($key, $value, $texto_firma3);
					$texto_5ficha	= str_replace($key, $value, $texto_5ficha);
					$texto_6ficha	= str_replace($key, $value, $texto_6ficha);
				}
				
				$cfirmas5			.= $texto_5ficha;			//Firma 5 Avales
				$cfirmas6			.= $texto_6ficha;			//Firma 6 Avales
				
				$cficha				.= $texto_ficha;
				$cficha2			.= $texto_ficha2;
				$cfirmas			.= $texto_firma;
				$cfirmas2			.= "<td>$texto_firma</td>";
				$cfirmas3			.= $texto_firma3;
				$this->mFirmasAvalesH[$persona]	= $texto_firma; //Firmas en forma horizontal
				$criesgo			.= $texto_aut;
				$clista				.= $xSoc->getNombreCompleto(OUT_TXT) . ": " . $xSoc->getDomicilio() . "; ";
				//setLog($texto_ficha);
			}
			//setLog($xSoc->getMessages());	
			$itx++;
		}
		$xT		= new cTabla($sql);
		$this->mArr["variable_listado_de_avales"] 	= $xT->Show("TR.Avales");
		$this->mFichasAvales		= $cficha;
		$this->mFichasAvales2		= $cficha2;
		$this->mFirmasAvales		= $cfirmas;
		$this->mFirmasAvales2		= "<table><tr>$cfirmas2</tr></table>";
		$this->mFirmasAvales3		= $cfirmas3;
		$this->mFichaRiesgoAv		= $criesgo;
		$this->mLAvalesConDir		= $clista;
		$this->mFicha5Avales		= $cfirmas5;
		$this->mFicha6Avales		= $cfirmas6;
		
	}
	function getListadoDeReferencias($persona){
		$mSQL		= new cSQLListas();
		$mql		= new MQL();
		$xF			= new cFecha();
		$sql 		= $mSQL->getListadoDeRelacionesPersonales($persona);
		$rs			= $mql->getDataRecord($sql);
		
		$arrFichas	= array(8012,8013);
		
		$itx		= 1;
		$this->mArr["vrefs_tabla_1"] 		= "";
		
		
		if($mql->getNumberOfRows()<=0){
			
			$this->mArr["vrefs_nombre_completo"] 		= "";
			$this->mArr["vrefs_domicilio_convencional"]	= "";
			$this->mArr["vrefs_email"]					= "";
			$this->mArr["vrefs_representante"]			= "";
			$this->mArr["vrefs_domicilio_localidad"] 	= "";
			$this->mArr["vrefs_direccion_calle_y_numero"]= "";
			$this->mArr["vrefs_direccion_estado"] 		= "";
			$this->mArr["vrefs_direccion_completa"] 	= "";
			$this->mArr["vrefs_ocupacion"] 				= "";
			$this->mArr["vrefs_telefono_principal"]		= "";
			$this->mArr["vrefs_fecha_de_nacimiento"] 	= "";
			$this->mArr["vrefs_id_fiscal"] 				= "";
			$this->mArr["vrefs_id_poblacional"] 		= "";
			$this->mArr["vrefs_lugar_de_nacimiento"] 	= "";
			$this->mArr["vrefs_empresa_de_trabajo"] 	= "";
			$this->mArr["vrefs_estado_civil"] 			= "";
			$this->mArr["vrefs_tipo_de_relacion"] 		= "";
			$this->mArr["vrefs_tipo_de_parentesco"] 	= "";
			$this->mArr["vrefs_nombres"]				= "";
			$this->mArr["vrefs_primer_apellido"]		= "";
			$this->mArr["vrefs_segundo_apellido"]		= "";
			
		} else {
			$idxficha		=1;
			foreach ($arrFichas as $id => $idformato){
				$itx		= 1;
				$fmt		= contrato($idformato, "texto_del_contrato");
				$txt		= ""; //Texto de la Ficha
				
				foreach ($rs as $rows){
					$idpersona	= $rows["clave_de_persona"];
					$xSoc		= new cSocio($idpersona); $xSoc->init();
					
					$this->mArr["vars_refs" . $itx . "_nombre_completo"] 		= $xSoc->getNombreCompleto();
					$this->mArr["vars_refs" . $itx . "_estado_civil"] 			= "";
					$this->mArr["vars_refs" . $itx . "_id_fiscal"] 			= $xSoc->getRFC(true);
					$this->mArr["vars_refs" . $itx . "_id_poblacional"] 		= $xSoc->getCURP(true);
					$this->mArr["vars_refs" . $itx . "_telefono_principal"] 	= $xSoc->getTelefonoPrincipal();
					$this->mArr["vars_refs" . $itx . "_email"] 				= $xSoc->getCorreoElectronico();
					
					$vars	= array(
							"vrefs_nombre_completo" 		=> $xSoc->getNombreCompleto(),
							"vrefs_domicilio_convencional"	=> "",
							"vrefs_email"					=> $xSoc->getCorreoElectronico(),
							"vrefs_representante"			=> $xSoc->getNombreDelRepresentanteLegal(),
							
							"vrefs_domicilio_localidad" 	=> "",
							"vrefs_direccion_calle_y_numero" => "",
							"vrefs_direccion_estado" 		=> "",
							"vrefs_direccion_completa" 		=> "",
							"vrefs_ocupacion" 				=> $xSoc->getTituloPersonal(),
							"vrefs_telefono_principal"		=> $xSoc->getTelefonoPrincipal(),
							"vrefs_fecha_de_nacimiento" 	=> $xF->getFechaCorta($xSoc->getFechaDeNacimiento()),
							"vrefs_id_fiscal" 				=> $xSoc->getRFC(true),
							"vrefs_id_poblacional" 			=> $xSoc->getCURP(true),
							"vrefs_id_ife" 					=> $xSoc->getClaveDeIFE(),
							"vrefs_id_clave" 				=> $xSoc->getClaveDeIdentificacion(),
							"vrefs_lugar_de_nacimiento" 	=> $xSoc->getLugarDeNacimiento(),
							"vrefs_empresa_de_trabajo" 		=> "",
							"vrefs_estado_civil" 			=> "",
							"vrefs_tipo_de_relacion" 		=> "",
							"vrefs_tipo_de_parentesco" 		=> "",
							
							"vrefs_nombres"					=> $xSoc->getNombre(),
							"vrefs_primer_apellido"			=> $xSoc->getApellidoPaterno(),
							"vrefs_segundo_apellido"		=> $xSoc->getApellidoMaterno()
							
					);
					//reemplazar formato
					
					$vars			= array_merge($vars, $this->mBasicVars);
					$subfmt			= $fmt;	//Subformato
					foreach ($vars as $key => $value) {
						$subfmt		= str_replace($key, $value, $subfmt);
					}
					$txt			.= $subfmt;
					$itx++;
				}
				//Compilar las fichas
				$this->mArr["var_vrefs_ficha_$idxficha"]	= $txt;
				$txt											= "";
				$idxficha++;
			}
			$xTBL							= new cTabla($sql);
			$xTBL->setOmitidos("clave_de_persona");
			$xTBL->setOmitidos("ocupacion");
			$xTBL->setOmitidos("domicilio");
			$xTBL->setOmitidos("curp");
			$xTBL->setOmitidos("clave");
			$xTBL->setOmitidos("tipo_de_relacion");
			$xTBL->setNoFilas();
			$this->mArr["vrefs_tabla_1"]	= $xTBL->Show();	
			//
			
			
			$xT		= new cTabla($mSQL->getListadoDeReferenciasBancarias($persona));
			$xT2	= new cTabla($mSQL->getListadoDeReferenciasComerciales($persona));
			$xT->setNoFilas(); $xT2->setNoFilas(); $xT->setNoFieldset(); $xT2->setNoFieldset(); $xT->setOmitidos("clave"); $xT2->setOmitidos("clave");
			$xT->setKeyTable("socios_relaciones");
			$xT2->setKeyTable("socios_relaciones");
			
			$TBan	= $xT->Show("TR.REFERENCIAS_BANCARIAS");//
			if($xT->getRowCount() <=  0 ){
				$this->mArr["vrefs_tabla_2"]	= "";
			} else {
				$this->mArr["vrefs_tabla_2"]	= $TBan; $TBan	= "";
			}
			$TCom	= $xT2->Show("TR.REFERENCIAS_COMERCIALES");//
			if($xT2->getRowCount() <= 0){
				$this->mArr["vrefs_tabla_3"]	= "";
			} else {
				$this->mArr["vrefs_tabla_3"]	= $TCom; $TCom	= "";
			}
		}
		
	}
	function getListadoDeFirmantes($credito, $monto_del_credito = 0){
		$mSQL		= new cSQLListas();
		$mql		= new MQL();
		$xF			= new cFecha();
		$sql 		= $mSQL->getListadoDeFirmantes($credito, $this->mPersona);
		$rs			= $mql->getDataRecord($sql);
		
		$arrFichas	= array(8005,8006,8007,8008,8009);
		
		$itx		= 1;
		
		
		
		if($mql->getNumberOfRows()<=0){
			
			$this->mArr["firmantes_nombre_completo"] 		= "";
			$this->mArr["firmantes_domicilio_convencional"]	= "";
			$this->mArr["firmantes_email"]					= "";
			$this->mArr["firmantes_representante"]			= "";
			
			$this->mArr["firmantes_domicilio_localidad"] 	= "";
			$this->mArr["firmantes_direccion_calle_y_numero"]= "";
			$this->mArr["firmantes_direccion_estado"] 		= "";
			$this->mArr["firmantes_direccion_completa"] 	= "";
			$this->mArr["firmantes_ocupacion"] 				= "";
			$this->mArr["firmantes_telefono_principal"]		= "";
			$this->mArr["firmantes_fecha_de_nacimiento"] 	= "";
			$this->mArr["firmantes_id_fiscal"] 				= "";
			$this->mArr["firmantes_id_poblacional"] 		= "";
			$this->mArr["firmantes_lugar_de_nacimiento"] 	= "";
			$this->mArr["firmantes_empresa_de_trabajo"] 	= "";
			$this->mArr["firmantes_estado_civil"] 			= "";
			$this->mArr["firmantes_tipo_de_relacion"] 		= "";
			$this->mArr["firmantes_tipo_de_parentesco"] 	= "";
			$this->mArr["firmantes_nombres"]				= "";
			$this->mArr["firmantes_primer_apellido"]		= "";
			$this->mArr["firmantes_segundo_apellido"]		= "";
			
		} else {
			$idxficha		=1;
			foreach ($arrFichas as $id => $idformato){
				$itx		= 1;
				$fmt		= contrato($idformato, "texto_del_contrato");
				$txt		= ""; //Texto de la Ficha
				
				foreach ($rs as $rows){
					$idpersona	= $rows["persona"];
					$xSoc		= new cSocio($idpersona); $xSoc->init();
					
					$this->mArr["vars_firmantes" . $itx . "_nombre_completo"] 		= $xSoc->getNombreCompleto();
					$this->mArr["vars_firmantes" . $itx . "_estado_civil"] 			= "";
					$this->mArr["vars_firmantes" . $itx . "_id_fiscal"] 			= $xSoc->getRFC(true);
					$this->mArr["vars_firmantes" . $itx . "_id_poblacional"] 		= $xSoc->getCURP(true);
					$this->mArr["vars_firmantes" . $itx . "_telefono_principal"] 	= $xSoc->getTelefonoPrincipal();
					$this->mArr["vars_firmantes" . $itx . "_email"] 				= $xSoc->getCorreoElectronico();
					
					$vars	= array(
							"firmantes_nombre_completo" 		=> $xSoc->getNombreCompleto(),
							"firmantes_domicilio_convencional"	=> "",
							"firmantes_email"					=> $xSoc->getCorreoElectronico(),
							"firmantes_representante"			=> $xSoc->getNombreDelRepresentanteLegal(),
							
							"firmantes_domicilio_localidad" 	=> "",
							"firmantes_direccion_calle_y_numero" => "",
							"firmantes_direccion_estado" 		=> "",
							"firmantes_direccion_completa" 		=> "",
							"firmantes_ocupacion" 				=> $xSoc->getTituloPersonal(),
							"firmantes_telefono_principal"		=> $xSoc->getTelefonoPrincipal(),
							"firmantes_fecha_de_nacimiento" 	=> $xF->getFechaCorta($xSoc->getFechaDeNacimiento()),
							"firmantes_id_fiscal" 				=> $xSoc->getRFC(true),
							"firmantes_id_poblacional" 			=> $xSoc->getCURP(true),
							"firmantes_id_ife" 					=> $xSoc->getClaveDeIFE(),
							"firmantes_id_clave" 				=> $xSoc->getClaveDeIdentificacion(),
							"firmantes_lugar_de_nacimiento" 	=> $xSoc->getLugarDeNacimiento(),
							"firmantes_empresa_de_trabajo" 		=> "",
							"firmantes_estado_civil" 			=> "",
							"firmantes_tipo_de_relacion" 		=> "",
							"firmantes_tipo_de_parentesco" 		=> "",

							"firmantes_nombres"					=> $xSoc->getNombre(),
							"firmantes_primer_apellido"			=> $xSoc->getApellidoPaterno(),
							"firmantes_segundo_apellido"		=> $xSoc->getApellidoMaterno()
							
					);
					//reemplazar formato
					
					$vars			= array_merge($vars, $this->mBasicVars);
					$subfmt			= $fmt;	//Subformato
					foreach ($vars as $key => $value) {
						$subfmt		= str_replace($key, $value, $subfmt);
					}
					$txt			.= $subfmt;
					$itx++;
				}
				//Compilar las fichas
				$this->mArr["var_firmantes_ficha_$idxficha"]	= $txt;
				$txt											= "";
				$idxficha++;
			}
			//
		}
		
	}
	
	function get(){ return $this->mTxt;	}
	function getTitulo(){ return $this->mTitle; }
	function getSelectVariables($id = "", $props = "", $divCss = "tx4"){
		$id		= "idvariables";
		$lbl	= "Variables";
		$xL		= $this->getOLang();

		//=> $xL->getT("")
/*			$this->mArr["vars_pers_ae_dom_calle"]					= "";
			$this->mArr["vars_pers_ae_dom_numext"]					= "";
			$this->mArr["vars_pers_ae_dom_numint"]					= "";
			$this->mArr["vars_pers_ae_dom_colonia"]					= "";
			$this->mArr["vars_pers_ae_dom_codigopos"]				= "";
			$this->mArr["vars_pers_ae_dom_municipio"]				= "";
			$this->mArr["vars_pers_ae_dom_ent_fed"]					= "";
			$this->mArr["vars_pers_ae_dom_referencia"]				= "";*/
		$arrV	= array("variables_generales" => array(
					"variable_fecha_larga_actual" 		=> $xL->getT("TR.FECHA_ACTUAL .- NOMBRE LARGO"),
					"variable_fecha_dia_actual"			=> $xL->getT("TR.FECHA_ACTUAL .- DIA"),
					"variable_fecha_dianombre_actual"	=> $xL->getT("TR.FECHA_ACTUAL .- DIA NOMBRE"),
					"variable_fecha_mes_actual"			=> $xL->getT("TR.FECHA_ACTUAL .- MES"),
					"variable_fecha_mesnombre_actual"	=> $xL->getT("TR.FECHA_ACTUAL .- MES NOMBRE"),				
					"variable_fecha_anno_actual"		=> $xL->getT("TR.FECHA_ACTUAL .- EJERCICIO"),
					"variable_lugar_actual" 			=> "Lugar de Expedicion del Documento",
					"variable_testigo_del_acto" 		=> "Testigo del Acto",
					"variable_nombre_id_poblacional" 	=> $xL->getT("TR.FORMATO .- NOMBRE CURP"),
					"variable_nombre_id_fiscal"			=> $xL->getT("TR.FORMATO .- NOMBRE RFC"),
					
					"variable_rfc_de_la_entidad" 		=> $xL->getT("TR.ENTIDAD .- RFC"),
					"variable_marca_de_tiempo" 			=> "Marca de Tiempo",
					"variable_encabezado_de_reporte" 	=> $xL->getT("TR.FORMATO .- ENCABEZADO_DE_REPORTE"),
					"variable_pie_de_reporte" 			=> $xL->getT("TR.FORMATO .- PIE_DE_REPORTE"),
				
					/*"variable_nombre_de_la_sociedad"	=> $xL->getT("TR.ENTIDAD .- NOMBRE"),*/
					"variable_nombre_de_la_entidad"		=> $xL->getT("TR.ENTIDAD .- NOMBRE"),
					"variable_entidad_logo"				=> $xL->getT("TR.ENTIDAD .- LOGO"),
					"variable_ciudad_de_la_entidad"		=> $xL->getT("TR.ENTIDAD .- CIUDAD"),
					"variable_entidad_telefono_general"	=> $xL->getT("TR.ENTIDAD .- TELEFONO"),
					"variable_domicilio_de_la_entidad" 	=> $xL->getT("TR.ENTIDAD .- DOMICILIO"),
					"var_entidad_mail"					=> $xL->getT("TR.ENTIDAD .- CORREO_ELECTRONICO"),
					"var_entidad_factura_mail"			=> $xL->getT("TR.ENTIDAD .- FACTURA .- CORREO_ELECTRONICO"),
				
					"variable_entidad_telefono_principal"						=> $xL->getT("TR.ENTIDAD .- TELEFONO PRINCIPAL"),
					"variable_horario_de_trabajo_de_la_entidad" 				=> $xL->getT("TR.ENTIDAD .- HORARIO DE TRABAJO"),
					"variable_nombre_de_presidente_de_vigilancia_de_la_entidad" => $xL->getT("TR.ENTIDAD .- PRESIDENTE VIGILANCIA"),
					"variable_nombre_del_representante_legal_de_la_sociedad" 	=> $xL->getT("TR.ENTIDAD .- PRESIDENTE"),
					"variable_acta_notarial_de_poder_al_representante"			=> $xL->getT("TR.ENTIDAD .- PODERNOTARIAL"),
					"variable_documento_de_constitucion_de_la_sociedad"			=> $xL->getT("TR.ENTIDAD .- ACTANOTARIAL"),
				
				
					"variable_usr_telefono"										=> $xL->getT("TR.USUARIO .- TELEFONO"),
					"variable_usr_mail"											=> $xL->getT("TR.USUARIO .- CORREO_ELECTRONICO"),
					"variable_usr_nombre"										=> $xL->getT("TR.USUARIO .- NOMBRE"),
					"vars_simbolo_cuadro"										=> $xL->getT("TR.FORMATO .- CUADRO"),

					),
				
				"variables_de_personas" => array(
					"variable_nombre_del_socio" 					=> $xL->getT("TR.GENERAL .- NOMBRE_COMPLETO"),
					"variable_domicilio_del_socio" 					=> $xL->getT("TR.VIVIENDA .- DOMICILIO COMPLETO"),
					"variable_ciudad_del_socio" 					=> $xL->getT("TR.VIVIENDA .- CIUDAD"),

					"variable_rfc_del_socio" 						=> $xL->getT("TR.GENERAL .- IDENTIFICACION_FISCAL"),
					"variable_curp_del_socio" 						=> $xL->getT("TR.GENERAL .- CURP"),
					"variable_numero_de_socio" 						=> $xL->getT("TR.GENERAL .- ID"),
					"variable_nombre_caja_local" 					=> $xL->getT("TR.GENERAL .- CAJA_LOCAL"),
					"variable_lista_de_beneficiados" 				=> $xL->getT("TR.LISTA .- BENEFICIARIOS"),
					"variable_firmas_de_obligados_solidarios" 		=> $xL->getT("TR.LISTA .- FIRMA OBLIGADO"),
					"variable_informacion_del_socio" 				=> $xL->getT("TR.GENERAL .- FICHA"),
					"variable_nombre_de_empresa" 					=> $xL->getT("TR.Actividad .- EMPLEADOR"),
						
					"var_persona_ae_describe"						=> $xL->getT("TR.Actividad .- Descripcion"),
					"var_persona_ae_ncomercial"						=> $xL->getT("TR.Actividad .- NOMBRE COMERCIAL"),
					"var_persona_ae_fechainicial"					=> $xL->getT("TR.Actividad .- FECHA_INICIAL"),
					"var_persona_ae_fechaverif"						=> $xL->getT("TR.Actividad .- FECHA_DE VERIFICAR"),
					
					"variable_actividad_economica_del_socio" 		=> $xL->getT("TR.Actividad .- Puesto"),
					"variable_socio_actividad_ciudad" 				=> $xL->getT("TR.Actividad .- Ciudad"),
					"variable_socio_actividad_telefono" 			=> $xL->getT("TR.Actividad .- Telefono"),
					"variable_municipio_de_actividad_economica" 	=> $xL->getT("TR.Actividad .- Municipio"),
					"variable_estado_de_actividad_economica" 		=> $xL->getT("TR.Actividad .- Estado"),
					"vars_pers_ae_dom_calle"						=> $xL->getT("TR.Actividad .- DOMICILIO .- CALLE"),
					"vars_pers_ae_dom_numext"						=> $xL->getT("TR.Actividad .- DOMICILIO .- NUMERO EXTERIOR"),
					"vars_pers_ae_dom_numint"						=> $xL->getT("TR.Actividad .- DOMICILIO .- NUMERO INTERIOR"),
					"vars_pers_ae_dom_colonia"						=> $xL->getT("TR.Actividad .- DOMICILIO .- COLONIA"),
					"vars_pers_ae_dom_codigopos"					=> $xL->getT("TR.Actividad .- DOMICILIO .- CODIGO_POSTAL"),
					"vars_pers_ae_dom_municipio"					=> $xL->getT("TR.Actividad .- DOMICILIO .- MUNICIPIO"),
					"vars_pers_ae_dom_ent_fed"						=> $xL->getT("TR.Actividad .- DOMICILIO .- ESTADO"),
					"vars_pers_ae_dom_referencia"					=> $xL->getT("TR.Actividad .- DOMICILIO .- REFERENCIAS"),
						
					"vars_persona_ae_clave_scian" 					=> $xL->getT("TR.Actividad .- CLAVE SCIAN"),
					"variable_persona_lista_de_bienes"	 			=> $xL->getT("TR.GENERAL .- BALANCE_PATRIMONIAL"),
					"variable_persona_id_interna" 					=> $xL->getT("TR.GENERAL .- IDINTERNA"),
					"variable_persona_telefono_principal"			=> $xL->getT("TR.GENERAL .- TELEFONO_PRINCIPAL"),
					"variable_persona_identificacion_oficial"		=> $xL->getT("TR.GENERAL .- IDENTIFICACION_OFICIAL"),
					"variable_persona_clave_electoral"				=> $xL->getT("TR.GENERAL .- CLAVE ELECTOR"),
					"variable_persona_regimen_matrimonial"			=> $xL->getT("TR.GENERAL .- REGIMEN_MATRIMONIAL"),
					
					"variable_persona_primer_apellido"				=> $xL->getT("TR.GENERAL .- PRIMER_APELLIDO"),
					"variable_persona_segundo_apellido"				=> $xL->getT("TR.GENERAL .- SEGUNDO_APELLIDO"),
					"variable_persona_nombres"						=> $xL->getT("TR.GENERAL .- NOMBRE"),
					"variable_persona_profesion"					=> $xL->getT("TR.GENERAL .- PROFESION"),
					"variable_persona_email"						=> $xL->getT("TR.GENERAL .- CORREO_ELECTRONICO"),
					"variable_persona_tipo_identificacion"			=> $xL->getT("TR.GENERAL .- TIPO DE IDENTIFICACION"),
					"variable_persona_clave_identificacion"			=> $xL->getT("TR.GENERAL .- CLAVE DE IDENTIFICACION"),
						
					"variable_vivienda_calle"						=> $xL->getT("TR.VIVIENDA .- CALLE"),
					"variable_vivienda_colonia"						=> $xL->getT("TR.VIVIENDA .- COLONIA"),
					"variable_vivienda_poblacion"					=> $xL->getT("TR.VIVIENDA .- POBLACION"),
						
					"variable_vivienda_numext"						=> $xL->getT("TR.VIVIENDA .- NUMERO EXTERIOR"),
					"variable_vivienda_numint"						=> $xL->getT("TR.VIVIENDA .- NUMERO INTERIOR"),

					"variable_vivienda_estado"						=> $xL->getT("TR.VIVIENDA .- ESTADO"),
					"variable_vivienda_municipio"					=> $xL->getT("TR.VIVIENDA .- MUNICIPIO"),
					"variable_vivienda_codigo_postal"				=> $xL->getT("TR.VIVIENDA .- CODIGO_POSTAL"),
					"variable_vivienda_telmov"						=> $xL->getT("TR.VIVIENDA .- MOVIL"),


					"variable_persona_domicilio_fiscal"				=> $xL->getT("TR.VIVIENDA .- DOMICILIO FISCAL"),
					"variable_persona_domicilio_convencional"		=> $xL->getT("TR.VIVIENDA .- DOMICILIO CONVENCIONAL"),
					"variable_persona_domicilio_construye"			=> $xL->getT("TR.VIVIENDA .- DOMICILIO ENCONSTRUCCION"),
					"var_vivienda_referencia"						=> $xL->getT("TR.VIVIENDA .- REFERENCIAS"),
						
					"variable_ciudad_de_nacimiento_del_socio"		=> $xL->getT("TR.GENERAL .- Ciudad de Nacimiento"),
					"variable_fecha_de_nacimiento_del_socio" 		=> $xL->getT("TR.GENERAL .- Fecha de Nacimiento"),
					"variable_estado_civil_del_socio" 				=> $xL->getT("TR.GENERAL .- Estado_Civil"),
						
					"variable_persona_rep_legal"					=> $xL->getT("TR.LEGAL .- REPRESENTANTE_LEGAL"),
						
					"variable_pm_num_notaria"						=> $xL->getT("TR.LEGAL .- NUMERO NOTARIA"),
					"variable_pm_nom_notario"						=> $xL->getT("TR.LEGAL .- NOMBRE NOTARIO"),
					"var_vrefs_ficha_1" 							=> $xL->getT("TR.REFERENCIAS .- FICHA 1"),
					"var_vrefs_ficha_2" 							=> $xL->getT("TR.REFERENCIAS .- FICHA 2"),
					"vrefs_tabla_1"									=> $xL->getT("TR.REFERENCIAS .- TABLA 1")
						
				),
				"variables_de_creditos" => array(
					"variable_informacion_del_credito" 				=> "Ficha de Informacion del Credito",
					"variable_tipo_de_credito"						=> "Tipo de Credito",
					"variable_tasa_mensual_de_interes_ordinario" 	=> "Tasa Mensual de Interes Ordinario",
					"variable_credito_fecha_de_vencimiento" 		=> "Fecha de Vencimiento",
					"variable_monto_garantia_liquida" 				=> "Monto de la Garantia Liquida",
					"variable_tasa_anual_de_interes_ordinario" 		=> "Tasa Anualizada de Interes Ordinario",
					"variable_tasa_anual_de_interes_moratorio" 		=> "Tasa Anualizada de Interes Moratorio",
						
					"variable_letras_tasa_anual_de_interes_ordinario" 		=> $xL->getT("TR.Tasa Anualizada de Interes Ordinario - EN_LETRAS"),
					"variable_letras_tasa_anual_de_interes_moratorio" 		=> $xL->getT("TR.Tasa Anualizada de Interes Moratorio - EN_LETRAS"),
												
					"variable_tasa_cat" 							=> "Tasa CAT",
					"variable_tasa_mensual_de_interes_moratorio" 	=> "Tasa Mensual de Interes Moratorio",
					"variable_tasa_de_garantia_liquida" 			=> "Tasa Anualizada de Garantia Liquida",
					"variable_en_letras_monto_ministrado" 			=> "Monto Ministrado en Letras",
					"variable_monto_ministrado" 					=> "Monto Ministrado",
					"variable_meses_de_duracion_del_credito" 		=> "Meses de Duracion del Credito",
					"variable_credito_fecha_de_ministracion" 		=> "Fecha fe Ministracion del credito",
					"variable_plan_de_pagos" 						=> "Tabla de Plan de Pagos simplificada",

					"variable_monto2_ministrado_con_intereses" 		=> "Monto del Credito con Intereses",
					"variable_monto2_ministrado_con_intereses_en_letras" => "Monto del Credito con Intereses en letras",
					"variable_credito_monto_parcialidad_fija" 		=> "Monto de la Parcialidad Fija",
					"variable_credito_numero_de_pagos" 				=> "Numero de Pagos",
					"variable_fecha_de_primer_pago" 				=> "Fecha de Primer Pago",
					"variable_credito_periocidad" 					=> "Periocidad de Pagos",
					"variable_credito_numero_de_pagos" 				=> "Numero de Pagos",
					"variable_avales_autorizacion_central_riesgo" 	=> "Autorizacion de consulta en Central de Riesgo para Avales",
					"variable_lista_de_avales_con_domicilio" 		=> "Listado de avales con Domicilio Completo ",
					"variable_credito_plan_exigible"				=> $xL->getT("TR.Credito .- PLAN_DE_PAGOS exigible"),
					"variable_credito_plan_pendiente"				=> $xL->getT("TR.Credito .- PLAN_DE_PAGOS pendiente"),
					
					
					"variable_producto_comision_apertura" 	=> $xL->getT("TR.Producto .- Comision por Apertura"),
					"variable_credito_estado_flujo_efectivo" => $xL->getT("TR.CREDITO .- Flujo_de_Efectivo"),
					"variable_listado_de_garantias"			=> $xL->getT("TR.CREDITO .- Garantias"),
					"variable_estado_de_credito"			=> $xL->getT("TR.CREDITO .- ESTATUS"),
					"variable_contrato_id_legal"			=> $xL->getT("TR.CONTRATO .- IDLEGAL"),
					"variable_contrato_fecha_reg"			=> $xL->getT("TR.CONTRATO .- FECHA REGISTRO"),
						
					"variable_producto_comision_apertura"	=> $xL->getT("TR.PRODUCTO .- COMISION_POR_APERTURA"),
					"variable_nombre_oficial_de_credito"	=> $xL->getT("TR.CREDITO .- OFICIAL"),
					"variable_credito_clave"				=> $xL->getT("TR.CREDITO .- CLAVE"),
					"variable_credito_mensual_dia_pago"		=> $xL->getT("TR.CREDITO .- MES .- DIA DE PAGO"),
					"variable_monto_solicitado"				=> $xL->getT("TR.CREDITO .- MONTO SOLICITADO"),
					"variable_letras_monto_solicitado"		=> $xL->getT("TR.CREDITO .- MONTO SOLICITADO .- LETRAS"),
					"variable_credito_descripcion_corta"	=> $xL->getT("TR.CREDITO .- DESCRIPCION CORTA"),
					"variable_parcialidad_monto"			=> $xL->getT("TR.CREDITO .- MONTO PARCIALIDAD"),
					"variable_cred_producto_nombre"			=> $xL->getT("TR.CREDITO .- PRODUCTO .- NOMBRE"),
					"variable_credito_destino"				=> $xL->getT("TR.CREDITO .- DESTINO"),
					"variable_persona_firma_integrada"		=> $xL->getT("TR.CREDITO .- FIRMA INTEGRADA"),
					"variable_2firmas_de_avales"			=> $xL->getT("TR.CREDITO .- FIRMA AVAL HORIZONTAL"),
					"variable_3firmas_de_avales"			=> $xL->getT("TR.CREDITO .- FIRMA AVAL VERTICAL"),
					"variable_credito_idplan_pagos"			=> $xL->getT("TR.CREDITO .- CLAVE PLAN_DE_PAGOS"),
					"variable_cred_calendario_pagos_1"		=> $xL->getT("TR.CREDITO .- PLAN_DE_PAGOS 1"),
					"variable_cred_calendario_pagos_2"		=> $xL->getT("TR.CREDITO .- PLAN_DE_PAGOS 2"),
						
					"variable_cred_prod_nombre_legal"		=> $xL->getT("TR.PRODUCTO .- NOMBRE LEGAL"),
					"variable_cred_prod_tipo_legal"			=> $xL->getT("TR.PRODUCTO .- TIPO LEGAL"),
						
					"variable_cred_rec_liq_clave"			=> $xL->getT("TR.CREDITO .- RECIBO LIQUIDACION"),
					"variable_cred_rec_liq_monton"			=> $xL->getT("TR.CREDITO .- RECIBO LIQUIDACION MONTO"),
					"variable_cred_rec_liq_montol"			=> $xL->getT("TR.CREDITO .- RECIBO LIQUIDACION MONTO EN_LETRAS"),
					"var_credito_monto_ints_calc"			=> $xL->getT("TR.CREDITO .- INTERES CALCULADO"),
					"var_cred_monto_comapertura"			=> $xL->getT("TR.CREDITO .- MONTO COMISION_APERTURA"),
					"var_cred_iva_comapertura"				=> $xL->getT("TR.CREDITO .- IVA COMISION_APERTURA"),
					"var_cred_total_comapertura"			=> $xL->getT("TR.CREDITO .- TOTAL COMISION_APERTURA"),
					"vars_creds_saldo_actual"				=> $xL->getT("TR.CREDITO .- SALDO_ACTUAL"),
					"vars_creds_monto_autorizado"			=> $xL->getT("TR.CREDITO .- MONTO AUTORIZADO"),
					"vars_creds_fecha_autoriza"				=> $xL->getT("TR.CREDITO .- FECHA_DE AUTORIZACION"),
					"vars_creds_c_fecha_autoriza"			=> $xL->getT("TR.CREDITO .- FECHA_DE AUTORIZACION CORTO"),
					"vars_creds_l_fecha_autoriza"			=> $xL->getT("TR.CREDITO .- FECHA_DE AUTORIZACION LARGO"),
					"vars_creds_lista_de_avales"			=> $xL->getT("TR.CREDITO .- LISTA DE AVALES"),
					"vars_creds_aut_dictamen"				=> $xL->getT("TR.CREDITO .- DICTAMEN")
					/*,
					"variable_credito_mensual_dia_pago"		=> $xL->getT("TR.CREDITO .- MES .- DIA DE PAGO"),*/

				),
				"variables_de_representante_legal" => array(
						"var_replegal_nombre_completo" 			=> $xL->getT("TR.REPRESENTANTE_LEGAL .- NOMBRE_COMPLETO"),
						"var_replegal_domicilio_convencional"	=> $xL->getT("TR.REPRESENTANTE_LEGAL .- DOMICILIO CONVENCIONAL"),
						"var_replegal_email"					=> $xL->getT("TR.REPRESENTANTE_LEGAL .- CORREO_ELECTRONICO"),
						"var_replegal_domicilio_localidad" 		=> $xL->getT("TR.REPRESENTANTE_LEGAL .- LOCALIDAD"),
						"var_replegal_direccion_calle_y_numero"	=> $xL->getT("TR.REPRESENTANTE_LEGAL .- CALLE Y NUMERO"),
						"var_replegal_direccion_estado" 		=> $xL->getT("TR.REPRESENTANTE_LEGAL .- ESTADO"),
						"var_replegal_direccion_completa"	 	=> $xL->getT("TR.REPRESENTANTE_LEGAL .- DIRECCION"),
						"var_replegal_ocupacion" 				=> $xL->getT("TR.REPRESENTANTE_LEGAL .- OCUPACION"),
						"var_replegal_telefono_principal"		=> $xL->getT("TR.REPRESENTANTE_LEGAL .- TELEFONO"),
						"var_replegal_fecha_de_nacimiento" 		=> $xL->getT("TR.REPRESENTANTE_LEGAL .- FECHA DE NACIMIENTO"),
						"var_replegal_id_fiscal" 				=> $xL->getT("TR.REPRESENTANTE_LEGAL .- RFC"),
						"var_replegal_id_poblacional" 			=> $xL->getT("TR.REPRESENTANTE_LEGAL .- CURP"),
						"var_replegal_lugar_de_nacimiento" 		=> $xL->getT("TR.REPRESENTANTE_LEGAL .- LUGAR DE NACIMIENTO"),
						"var_replegal_empresa_de_trabajo" 		=> $xL->getT("TR.REPRESENTANTE_LEGAL .- EMPRESA"),
						"var_replegal_estado_civil" 			=> $xL->getT("TR.REPRESENTANTE_LEGAL .- ESTADO_CIVIL"),
						"var_replegal_nombres"					=> $xL->getT("TR.REPRESENTANTE_LEGAL .- NOMBRE"),
						"var_replegal_primer_apellido"			=> $xL->getT("TR.REPRESENTANTE_LEGAL .- PRIMER_APELLIDO"),
						"var_replegal_segundo_apellido"			=> $xL->getT("TR.REPRESENTANTE_LEGAL .- SEGUNDO_APELLIDO"),
						
						"var_replegal_id_tipo"					=> $xL->getT("TR.REPRESENTANTE_LEGAL .- TIPO IDENTIFICACION"),
						"var_replegal_id_clave"					=> $xL->getT("TR.REPRESENTANTE_LEGAL .- CLAVE IDENTIFICACION"),
						"var_replegal_id_ife"					=> $xL->getT("TR.REPRESENTANTE_LEGAL .- CLAVE IFE"),
				),
				"variables_de_conyuge" => array(
						"var_conyuge_nombre_completo" 			=> $xL->getT("TR.CONYUGE .- NOMBRE_COMPLETO"),
						"var_conyuge_domicilio_convencional"	=> $xL->getT("TR.CONYUGE .- DOMICILIO CONVENCIONAL"),
						"var_conyuge_email"						=> $xL->getT("TR.CONYUGE .- CORREO_ELECTRONICO"),
						"var_conyuge_domicilio_localidad" 		=> $xL->getT("TR.CONYUGE .- LOCALIDAD"),
						"var_conyuge_direccion_calle_y_numero"	=> $xL->getT("TR.CONYUGE .- CALLE Y NUMERO"),
						"var_conyuge_direccion_estado" 			=> $xL->getT("TR.CONYUGE .- ESTADO"),
						"var_conyuge_direccion_completa"	 	=> $xL->getT("TR.CONYUGE .- DIRECCION"),
						"var_conyuge_ocupacion" 				=> $xL->getT("TR.CONYUGE .- OCUPACION"),
						"var_conyuge_telefono_principal"		=> $xL->getT("TR.CONYUGE .- TELEFONO"),
						"var_conyuge_fecha_de_nacimiento" 		=> $xL->getT("TR.CONYUGE .- FECHA DE NACIMIENTO"),
						"var_conyuge_id_fiscal" 				=> $xL->getT("TR.CONYUGE .- RFC"),
						"var_conyuge_id_poblacional" 			=> $xL->getT("TR.CONYUGE .- CURP"),
						"var_conyuge_lugar_de_nacimiento" 		=> $xL->getT("TR.CONYUGE .- LUGAR DE NACIMIENTO"),
						"var_conyuge_empresa_de_trabajo" 		=> $xL->getT("TR.CONYUGE .- EMPRESA"),
						"var_conyuge_estado_civil" 				=> $xL->getT("TR.CONYUGE .- ESTADO_CIVIL"),
						"var_conyuge_nombres"					=> $xL->getT("TR.CONYUGE .- NOMBRE"),
						"var_conyuge_primer_apellido"			=> $xL->getT("TR.CONYUGE .- PRIMER_APELLIDO"),
						"var_conyuge_segundo_apellido"			=> $xL->getT("TR.CONYUGE .- SEGUNDO_APELLIDO"),
						
						"var_conyuge_id_tipo"					=> $xL->getT("TR.CONYUGE .- TIPO IDENTIFICACION"),
						"var_conyuge_id_clave"					=> $xL->getT("TR.CONYUGE .- CLAVE IDENTIFICACION"),
						"var_conyuge_id_ife"					=> $xL->getT("TR.CONYUGE .- CLAVE IFE"),
				),
				
				"variables_de_firmantes" => array (
						"var_firmantes_ficha_1"				=> $xL->getT("TR.FIRMANTES .- FICHA 1"),
						"var_firmantes_ficha_2"				=> $xL->getT("TR.FIRMANTES .- FICHA 2"),
						"var_firmantes_ficha_3"				=> $xL->getT("TR.FIRMANTES .- FICHA 3"),
						"var_firmantes_ficha_4"				=> $xL->getT("TR.FIRMANTES .- FICHA 4"),
						
						"var_firmante_principal"			=> $xL->getT("TR.FIRMANTES .- PRINCIPAL"),
						"var_firmante_rol"					=> $xL->getT("TR.FIRMANTES .- ROL"),
						
						"firmantes_nombre_completo" 		=> $xL->getT("TR.FIRMANTES .- NOMBRE"),
						"firmantes_domicilio_convencional"	=> $xL->getT("TR.FIRMANTES .- VIVIENDA .- DOMICILIO CONVENCIONAL"),
						"firmantes_email"					=> $xL->getT("TR.FIRMANTES .- CORREO_ELECTRONICO"),

						"firmantes_domicilio_localidad" 	=> $xL->getT("TR.FIRMANTES .- VIVIENDA .- LOCALIDAD"),
						"firmantes_direccion_calle_y_numero"=> $xL->getT("TR.FIRMANTES .- CALLE Y NUMERO"),
						"firmantes_direccion_estado" 		=> $xL->getT("TR.FIRMANTES .- ESTADO"),
						"firmantes_direccion_completa" 		=> $xL->getT("TR.FIRMANTES .- DOMICILIO COMPLETO"),
						"firmantes_ocupacion" 				=> $xL->getT("TR.FIRMANTES .- OCUPACION"),
						"firmantes_telefono_principal"		=> $xL->getT("TR.FIRMANTES .- TELEFONO_PRINCIPAL"),
						"firmantes_fecha_de_nacimiento" 	=> $xL->getT("TR.FIRMANTES .- FECHA DE NACIMIENTO"),
						"firmantes_id_fiscal" 				=> $xL->getT("TR.FIRMANTES .- RFC"),
						"firmantes_id_ife" 					=> $xL->getT("TR.FIRMANTES .- IFE"),
						"firmantes_id_clave" 				=> $xL->getT("TR.FIRMANTES .- NUMERO IDENTIFICACION"),
						"firmantes_id_poblacional" 			=> $xL->getT("TR.FIRMANTES .- CURP"),
						"firmantes_lugar_de_nacimiento" 	=> $xL->getT("TR.FIRMANTES .- LUGAR DE NACIMIENTO"),
						"firmantes_empresa_de_trabajo" 		=> $xL->getT("TR.FIRMANTES .- TRABAJO .- EMPRESA"),
						"firmantes_estado_civil" 			=> $xL->getT("TR.FIRMANTES .- ESTADO_CIVIL"),
						"firmantes_tipo_de_relacion" 		=> $xL->getT("TR.FIRMANTES .- TIPO_DE RELACION"),
						"firmantes_tipo_de_parentesco"	 	=> $xL->getT("TR.FIRMANTES .- TIPO_DE CONSANGUINIDAD"),
						"firmantes_nombres" 				=> $xL->getT("TR.FIRMANTES .- Nombre"),
						"firmantes_primer_apellido" 		=> $xL->getT("TR.FIRMANTES .- PRIMER_APELLIDO"),
						"firmantes_segundo_apellido" 		=> $xL->getT("TR.FIRMANTES .- SEGUNDO_APELLIDO"),
						"firmantes_id_poblacional"			=> $xL->getT("TR.FIRMANTES .- CURP")
				),
				"variables_de_avales" => array(
						"aval_nombre_completo" 				=> $xL->getT("TR.Aval .- Nombre Completo"),
						
						"aval_nombres" 						=> $xL->getT("TR.Aval .- Nombre"),
						"aval_primer_apellido" 				=> $xL->getT("TR.Aval .- PRIMER_APELLIDO"),
						"aval_segundo_apellido" 			=> $xL->getT("TR.Aval .- SEGUNDO_APELLIDO"),
						"aval_id_poblacional" 				=> $xL->getT("TR.Aval .- CURP"),
						
						
						"aval_direccion_completa" 			=> $xL->getT("TR.Aval .- Domicilio Completo"),
						"aval_domicilio_localidad" 			=> $xL->getT("TR.Aval .- Localidad de Domicilio"),
						"aval_direccion_calle_y_numero"		=> $xL->getT("TR.Aval .- Calle y Numero"),
						"aval_direccion_estado" 			=> $xL->getT("TR.Aval .- Entidad Federativa"),
						"aval_direccion_municipio" 			=> $xL->getT("TR.Aval .- MUNICIPIO"),
						"aval_direccion_colonia" 			=> $xL->getT("TR.Aval .- COLONIA"),
						"aval_ocupacion" 					=> $xL->getT("TR.Aval .- Ocupacion Actual"),
						"aval_fecha_de_nacimiento" 			=> $xL->getT("TR.Aval .- Fecha de Nacimiento"),
						"aval_telefono_principal" 			=> $xL->getT("TR.Aval .- TELEFONO_PRINCIPAL"),
						"aval_email" 						=> $xL->getT("TR.Aval .- CORREO_ELECTRONICO"),
						
						"aval_id_fiscal" 					=> $xL->getT("TR.Aval .- RFC"),
						"aval_id_identificacion" 			=> $xL->getT("TR.Aval .- CLAVE DE IDENTIFICACION"),
						
						"aval_lugar_de_nacimiento" 			=> $xL->getT("TR.Aval .- Lugar de Nacimiento"),
						"aval_empresa_de_trabajo" 			=> $xL->getT("TR.Aval .- Nombre de la Empresa Donde Labora"),
						"aval_estado_civil" 				=> $xL->getT("TR.Aval .- Estado_Civil"),
						"aval_tipo_de_relacion" 			=> $xL->getT("TR.Aval .- Tipo_de Relacion"),
						"aval_tipo_de_parentesco" 			=> $xL->getT("TR.Aval .- Tipo_de Parentesco"),
						"aval_tipo_de_identificacion" 		=> $xL->getT("TR.Aval .- Tipo_de IDENTIFICACION"),
						
						"aval_porcentaje_relacionado"		=> $xL->getT("TR.Aval .- Porcentaje Relacionado"),
						"aval_domicilio_convencional"		=> $xL->getT("TR.Aval .- Domicilio Convencional"),
						
						"variable_avales_en_fichas" 		=> $xL->getT("TR.Aval .- Lista en Ficha"),
						
						"variable_2avales_en_fichas"		=> $xL->getT("TR.Aval .- Ficha simple"),
						
						"variable_firmas_de_avales" 		=> "Listado de FIRMAS de AVALES"
						
				),
				"variables_de_recibos" => array(
					"variable_numero_del_recibo" => "Numero de Recibo",
					"variable_fecha_del_recibo" => "Fecha del Recibo",
					"variable_monto_del_recibo" => "Monto del recibo",
					"variable_monto_del_recibo_en_letras" => "Monto en Letras del recibo",
					"variable_nombre_del_banco" => "Nombre del Banco",
					"variable_numero_de_cheque" => "Numero de Cheque",
					"variable_recibo_mvtos_con_socio" => "Ficha de Movimientos con Socios",
					"variable_nombre_del_cajero" => "Nombre del Cajero",
					"variable_tipo_de_pago" => "Tipo de Pago del Recibo",
					"variable_tipo_de_recibo" => "Tipo de Recibo",
					"variable_observacion_del_recibo" => "Observaciones del recibo",
					"variable_recibo_sdo_historico" => $xL->getT("TR.RECIBO.- SALDO GUARDADO")
				),
				
				"variables_de_operaciones" => array("concepto_nombre_corto" => "Nombre Corto")
				);
		//Agregar otros datos de credito por array
		$xCredOD	= new cCreditosOtrosDatos();
		$dd			= $xCredOD->getDatosInArray();
		foreach ($dd as $idx => $cdx){
			$arrV["variables_de_creditos"]["variable_cred_" . strtolower($idx)]	= $xL->getT("TR.CREDITO .- $idx");
		}
		$txt		= "";
		if(MODULO_CAPTACION_ACTIVADO == true){
			
			$arrV["variables_de_captacion"]						= array("variable_numero_de_cuenta" => "Numero de Cuenta",
					"variable_monto_inicial_en_numero" => "Monto Inicial de Apertura en Numero",
					"variable_monto_inicial_en_letras" => "Monto Inicial de Apertura en Letras",
					"variable_numero_de_dias" => "Numero de Dias de Inversion",
					"variable_nombre_mancomunados" => "Listado de Mancomunados",
					"variable_tasa_otorgada" => "Tasa de la Cuenta",
					"variable_fecha_de_vencimiento" => "Inversion.- Fecha de Vencimiento",
					"variable_oficial" => "Oficial de Captacion",
					"variable_lista_de_beneficiados" => "Lista de Beneficiados");
		}
		if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
			$arrV["variables_de_empresas"] = array(
					"variable_clave_de_empresa" => "Clave de Empresa",
					"variable_empresa_nombre_corto" => "Nombre Corto o Alias",
					"variable_nombre_de_empresa" => "Nombre de Empresa Asociada",
					"variable_periodo_de_envio" => "Periocidad y Periodo de Envio",
					"variable_periodo_fecha_inicial" => "Fecha Inicial del Envio",
					"variable_periodo_fecha_final" => "Fecha Final del Envio",
					"variable_periodo_fecha_cobro" => "Fecha De Cobro de Nomina",
					"variable_periodo_observaciones" => "Observaciones del Envio"
			);
		}
		if(PERSONAS_CONTROLAR_POR_GRUPO == true){
			
			$arrV["variables_de_grupos"]						= array(
					"variable_nombre_de_la_representante_social" => "Nombre de la representante",
					"variable_nombre_de_la_vocal_de_vigilancia" => "Nombre de la Vocal de Vigilancia",
					"variable_nombre_del_grupo_solidario" => "Nombre del Grupo Solidario",
					"variable_domicilio_de_la_representante_social" => "Domicilio de la Representante",
					"variable_grupo_nivel_ministracion" => "Nivel de Ministracion del Grupo");
		}
		if(MODULO_LEASING_ACTIVADO == true){
			//Array de leasing
			$arrLeas	= array();
			$xT			= new cOriginacion_leasing();
			
			$fld		= $xT->query()->getCampos();
			foreach ($fld as $fidx => $fcnt){
				$xFld	= new MQLCampo($fcnt);
				$arrLeas["var_leasing_" . $xFld->get()]	= $xL->getT("TR." . $xFld->get());
				if($xFld->isNumber() == true){
					$arrLeas["var_leasing_si_" . $xFld->get()]	= $xL->getT("TR.SIN IVA .- " . $xFld->get());
				}
			}
			$arrLeas["var_leasing_seguro_inicial"]				= $xL->getT("TR.AUTOSEGURO INICIAL");
			$arrLeas["var_leasing_seguro_financiado"]			= $xL->getT("TR.AUTOSEGURO FINANCIADO");
			$arrLeas["var_leasing_tenencia_inicial"]			= $xL->getT("TR.TENENCIA INICIAL");
			$arrLeas["var_leasing_tenencia_financiado"]			= $xL->getT("TR.TENENCIA FINANCIADO");
			
			$arrLeas["var_leasing_si_seguro_inicial"]			= $xL->getT("TR.SIN IVA .- AUTOSEGURO INICIAL");
			$arrLeas["var_leasing_si_seguro_financiado"]		= $xL->getT("TR.SIN IVA .- AUTOSEGURO FINANCIADO");
			$arrLeas["var_leasing_si_tenencia_inicial"]			= $xL->getT("TR.SIN IVA .- TENENCIA INICIAL");
			$arrLeas["var_leasing_si_tenencia_financiado"]		= $xL->getT("TR.SIN IVA .- TENENCIA FINANCIADO");
			
			$arrLeas["var_leasing_inicial_subtotal"]			= $xL->getT("TR.INICIAL SUBTOTAL");
			$arrLeas["var_leasing_inicial_iva"]					= $xL->getT("TR.INICIAL IVA");
			$arrLeas["var_leasing_inicial_total"]				= $xL->getT("TR.INICIAL TOTAL");
			
			$arrLeas["var_leasing_na_inicial_subtotal"]			= $xL->getT("TR.NO ANTICIPO .- INICIAL SUBTOTAL");
			$arrLeas["var_leasing_na_inicial_iva"]				= $xL->getT("TR.NO ANTICIPO .- INICIAL IVA");
			$arrLeas["var_leasing_na_inicial_total"]			= $xL->getT("TR.NO ANTICIPO .- INICIAL TOTAL");
			
			$arrLeas["var_leasing_vehiculo_total"]				= $xL->getT("TR.VEHICULO TOTAL");
			$arrLeas["var_leasing_idoriginacion_leasing"]		= $xL->getT("TR.CLAVE");
			$arrLeas["var_leasing_describe_aliado"]				= $xL->getT("TR.EQUIPOALIADO");
			
			$arrLeas["var_leasing_si_rentas"]					= $xL->getT("TR.TOTAL LEASING SIN IVA");
			$arrLeas["var_leasing_si_letras_rentas"]			= $xL->getT("TR.TOTAL LEASING SIN IVA .- EN_LETRAS");
			
			$arrLeas["var_leasing_rentas"]						= $xL->getT("TR.TOTAL LEASING");
			$arrLeas["var_leasing_letras_rentas"]				= $xL->getT("TR.TOTAL LEASING .- EN_LETRAS");
			
			$arrLeas["var_vehiculo_placas"]						= $xL->getT("TR.VEHICULO PLACAS");
			$arrLeas["var_vehiculo_describe"]					= $xL->getT("TR.VEHICULO DESCRIPCION");
			$arrLeas["var_vehiculo_niv"]						= $xL->getT("TR.VEHICULO SERIENAL");
			
			$arrLeas["var_leasing_es_inicial_subtotal"]			= $xL->getT("TR.INICIAL SUBTOTAL ESPECIAL");
			$arrLeas["var_leasing_es_inicial_iva"]				= $xL->getT("TR.INICIAL IVA ESPECIAL");
			$arrLeas["var_leasing_es_inicial_total"]			= $xL->getT("TR.INICIAL TOTAL ESPECIAL");

			$arrLeas["var_leasing_renta_deposito"]				= $xL->getT("TR.RENTAS EN DEPOSITO");
			$arrLeas["var_leasing_tramite_clave"]				= $xL->getT("TR.TIPO_DE TRAMITE");
			$arrLeas["var_leasing_plan"]						= $xL->getT("TR.CREDITO .- LEASING PLAN_DE_PAGO");
			//Quitar IVA que no van
			$arrOmitidos	= array("var_leasing_si_marca","var_leasing_si_idoriginacion_leasing","var_leasing_si_annio","var_leasing_si_tipo_leasing","var_leasing_si_tipo_uso","var_leasing_si_tipo_gps","var_leasing_si_usuario",
				"var_leasing_si_clave","var_leasing_si_segmento","var_leasing_si_tipo_rac","var_leasing_si_paso_proceso","var_leasing_si_credito","var_leasing_si_suboriginador","var_leasing_si_plazo","var_leasing_si_persona",
					"var_leasing_si_tasa_iva","var_leasing_si_tasa_compra","var_leasing_si_tasa_comision","var_leasing_si_estatus","var_leasing_si_renta_deposito",
					"var_leasing_si_estatus","var_leasing_estatus","var_leasing_usuario","var_leasing_si_usuario","var_leasing_domicilia","var_leasing_si_domicilia","var_leasing_si_oficial","var_leasing_si_originador",
					"var_leasing_financia_seguro","var_leasing_si_financia_seguro","var_leasing_financia_tenencia","var_leasing_si_financia_tenencia","var_leasing_es_moral","var_leasing_si_es_moral",
					"var_leasing_si_tasa_credito","var_leasing_si_tasa_tiie", "var_leasing_si_administrado","var_leasing_administrado","var_leasing_total_credito","var_leasing_si_total_credito", "var_leasing_opts"
			);
			foreach ($arrOmitidos as $idmm => $cmm){
				unset($arrLeas[$cmm]);
			}
			//Vehiculo extra
			$arrLeas["var_vehiculo_serie"]		= $xL->getT("TR.VEHICULO .- SERIE");
			$arrLeas["var_vehiculo_motor"]		= $xL->getT("TR.VEHICULO .- MOTOR");
			$arrLeas["var_vehiculo_color"]		= $xL->getT("TR.VEHICULO .- COLOR");
			
			$arrLeas["var_proveedor_nombre"]	= $xL->getT("TR.PROVEEDOR .- NOMBRE");
			$arrLeas["var_proveedor_direccion"]	= $xL->getT("TR.PROVEEDOR .- DIRECCION");
			$arrLeas["var_proveedor_telefono"]		= $xL->getT("TR.PROVEEDOR .- TELEFONO");
			
			$arrLeas["var_leas_promotor_nombre"]	= $xL->getT("TR.PROMOTOR .- NOMBRE");
			$arrLeas["var_leas_promotor_mail"]		= $xL->getT("TR.PROMOTOR .- CORREO_ELECTRONICO");
			$arrLeas["var_leas_promotor_telefono"]	= $xL->getT("TR.PROMOTOR .- TELEFONO");
			
			//Plazos
			$xEsc		= new cLeasing_escenarios();
			$sql		= "SELECT * FROM `leasing_escenarios`";
			$xQL		= new MQL();
			$rs			= $xQL->getDataRecord($sql);
			foreach ($rs as $rw){
				$xEsc->setData($rw);
				$pzo	= $xEsc->plazo()->v();
				$arrLeas["var_leasing_" . $pzo . "_cuota_renta" ]		= $xL->getT("TR.$pzo .- CUOTA RENTA");
				$arrLeas["var_leasing_" . $pzo . "_cuota_seguro" ]		= $xL->getT("TR.$pzo .- CUOTA AUTOSEGURO");
				$arrLeas["var_leasing_" . $pzo . "_cuota_tenencia" ]	= $xL->getT("TR.$pzo .- CUOTA TENENCIA");
				$arrLeas["var_leasing_" . $pzo . "_cuota_mtto" ]		= $xL->getT("TR.$pzo .- CUOTA MTTO");
				$arrLeas["var_leasing_" . $pzo . "_cuota_accesorios" ]	= $xL->getT("TR.$pzo .- CUOTA ACCESORIOS");
				$arrLeas["var_leasing_" . $pzo . "_residual" ]			= $xL->getT("TR.$pzo .- RESIDUAL");
				
				$arrLeas["var_leasing_" . $pzo . "_na_renta" ]			= $xL->getT("TR.$pzo .- RENTA SIN EQUIPOALIADO");
				$arrLeas["var_leasing_" . $pzo . "_cuota_aliado" ]			= $xL->getT("TR.$pzo .- MONTO EQUIPOALIADO");
				
				$arrLeas["var_leasing_" . $pzo . "_proporcional" ]		= $xL->getT("TR.$pzo .- RENTA PROPORCIONAL");
				$arrLeas["var_leasing_" . $pzo . "_subtotal" ]			= $xL->getT("TR.$pzo .- SUBTOTAL");
				$arrLeas["var_leasing_" . $pzo . "_impuestos" ]			= $xL->getT("TR.$pzo .- IMPUESTOS");
				//$arrLeas["var_leasing_" . $pzo . "_total" ]				= $xL->getT("TR.$pzo .- TOTAL");
				$arrLeas["var_leasing_" . $pzo . "_mensual_total" ]		= $xL->getT("TR.$pzo .- MENSUAL TOTAL");
				
				$arrLeas["var_leasing_" . $pzo . "_pp_subtotal" ]		= $xL->getT("TR.$pzo .- PRIMER PAGO .- SUBTOTAL");
				$arrLeas["var_leasing_" . $pzo . "_iva_total" ]			= $xL->getT("TR.$pzo .- PRIMER PAGO .- IVA");
				$arrLeas["var_leasing_" . $pzo . "_inicial_total" ]		= $xL->getT("TR.$pzo .- PRIMER PAGO .- TOTAL");
				
			
				$arrLeas["var_vehiculo_" . $pzo . "_vec_monto"]			= $xL->getT("TR.$pzo .- VEC MONTO");
				$arrLeas["var_vehiculo_" . $pzo . "_vec_iva"]			= $xL->getT("TR.$pzo .- VEC IVA");
				$arrLeas["var_vehiculo_" . $pzo . "_vec_total"]			= $xL->getT("TR.$pzo .- VEC TOTAL");
				
				//Renta Proporcional
				$arrLeas["var_leasing_" . $pzo . "_renta_deposito"]		= $xL->getT("TR.$pzo .- RENTADEPOSITO");;
				//var_leasing_12_renta_deposito
				$arrLeas["var_leasing_" . $pzo . "_renta_proporcional"]	= $xL->getT("TR.$pzo .- RENTAPROP");
				$arrLeas["var_leasing_" . $pzo . "_si_renta_proporcional"]	= $xL->getT("TR.$pzo .- NO IVA .- RENTAPROP");;
				
			}
			//Datos basicos vehiculo
			for($ivx = 1; $ivx <=6; $ivx++){
				$arrLeas["var_leas_"  . $ivx . "_vehiculo_segmento"]	= $xL->getT("TR.VEHICULO $ivx .- SEGMENTO");
				$arrLeas["var_leas_"  . $ivx . "_vehiculo_total"]		= $xL->getT("TR.VEHICULO $ivx .- PRECIO + IVA");
				$arrLeas["var_leas_"  . $ivx . "_vehiculo_marca"]		= $xL->getT("TR.VEHICULO $ivx .- MARCA");
				$arrLeas["var_leas_"  . $ivx . "_vehiculo_ant"]			= $xL->getT("TR.VEHICULO $ivx .- ANTICIPO");
				$arrLeas["var_leas_"  . $ivx . "_vehiculo_uso"]			= $xL->getT("TR.VEHICULO $ivx .- USO");
				$arrLeas["var_leas_"  . $ivx . "_vehiculo_desc"]		= $xL->getT("TR.VEHICULO $ivx .- DESCRIPCION");
			}
			
			$arrV["variables_de_leasing"]						= $arrLeas;
		}
		$this->mListVars	= $arrV;
		//Eliminar segun su naturaleza
		if($this->mTipo>0){
			switch($this->mTipo){
				case iDE_CREDITO:
					unset($arrV["variables_de_captacion"]);
					unset($arrV["variables_de_recibos"]);
					unset($arrV["variables_de_operaciones"]);
					//unset($arrV[""]);

					break;
				case iDE_SOCIO:
					unset($arrV["variables_de_captacion"]);
					unset($arrV["variables_de_recibos"]);
					unset($arrV["variables_de_operaciones"]);
					
					unset($arrV["variables_de_leasing"]);
					unset($arrV["variables_de_avales"]);
					unset($arrV["variables_de_firmantes"]);
					unset($arrV["variables_de_creditos"]);
					break;
			}
		}
		//
		foreach($arrV as $clave => $valores){
			$txt	.= "<optgroup label=\"" . strtoupper( str_replace("_", " ", $clave) ) . "\">";
			$sbit	= "";
			asort($valores);
			foreach($valores as $subitems => $subvalor){
				//<option value="variable_firmas_de_avales">Listado de FIRMAS de AVALES</option>
				$txt	.= "<option value=\"$subitems\">" . strtoupper( str_replace("_", " ", $subvalor) ) . "</option>";
			}
			$txt		.= "</optgroup>";
		}
		$divCss			= ($divCss == "") ? "" : "<div class='$divCss'>";
		$divEnd			= ($divCss == "") ? "" : "</div>";
		return "$divCss<label for=\"$id\">$lbl</label><select id=\"$id\" name=\"$id\" $props>$txt</select>$divEnd";
	}
	function getSelectDeFormatos(){ $xSel	= new cHSelect(); return  $xSel->getListaDeFormatos("idcontrato");	}
	function getListaDeVars(){ return $this->mListVars; }
	function getVariables(){ return $this->mArr; }
	function getBaseDeFirmas($arrCNT){
		$itm	= 1;
		$tbl	= "";
		foreach ($arrCNT as $cargo => $nombre){
			$tbl	.= "<td><h4>$cargo</h4><br /><br /><br /><h4>$nombre</h4></td>";
		}
		return "<table><tr>$tbl</tr></table>";
	}
	function setTexto($texto, $titulo = "", $update = false){
		$res			= true;
		$this->mTxt		= $texto;
		$this->mTitle	= $titulo;
		if($update == true){
			$xQL		= new MQL();
			$res 		= $xQL->setRawQuery("UPDATE general_contratos SET texto_del_contrato='$texto' WHERE idgeneral_contratos=".$this->mID);
			$res		= ($res === false) ? false : true;
			$xCache		= new cCache();
			$idc		= "general_contratos-" . $this->mID;
			$xCache->clean($idc);
		}
		return $res;
	}
	function getOPersonas(){
		if($this->mOPersona == null and setNoMenorQueCero($this->mPersona) > 0){
			$this->mOPersona	= new cSocio($this->mPersona);
			$this->mOPersona->init();
		}
		return $this->mOPersona;
	}
	function setConPersonasConRelacionPatrimonial(){
		if(setNoMenorQueCero($this->mPersona) > 0){
			$xLi	= new cSQLListas();
			$cTcta	= new cTabla($xLi->getListaDePatrimonioPorPersona($this->mPersona));
			$cTcta->setTdClassByType();
			$cTcta->setTitulo("estado", "estatus");
			$cTcta->setOmitidos("clave");
			$cTcta->setNoFilas();
			$cTcta->setFootSum(array(
					5 => "monto"
			));			
			$this->mEstadoBienes = $cTcta->Show();
			$this->mArr["variable_persona_lista_de_bienes"] = $this->mEstadoBienes;
		}
		
	}
	function setConCreditosFlujoEfectivo(){
		//variable_credito_estado_flujo_efectivo
		if(setNoMenorQueCero($this->mDocumento) > 0){
			$xLi	= new cSQLListas();
			$cTcta	= new cTabla($xLi->getListaDeFlujoDeEfvoPorCredito($this->mDocumento));
			$cTcta->setTdClassByType();
			$cTcta->setNoFilas();
			$cTcta->setFootSum(array(
				4 => "monto_diario"	
			));
			$this->mEstadoBienes = $cTcta->Show();
			$this->mArr["variable_credito_estado_flujo_efectivo"] = $this->mEstadoBienes;
		}
	}
	function setConFamiliares(){
		$persona= $this->mPersona;
		$xQL 	= new MQL();
		$xLi	= new cSQLListas();
		$sql	= $xLi->getListadoDeRelaciones($persona, false, false, false," AND (`socios_relaciones`.`consanguinidad` != 99) ");
		$xT		= new cTabla($sql);
		$xT2	= new cTabla($xLi->getListadoDeRelaciones($persona, false, false, false," AND (`socios_relaciones`.`consanguinidad` = 99) "));
		$xT2->setNoFilas();
		$xT->setNoFilas();
		$this->mArr["variable_persona_lista_familiares"]	= $xT->Show("TR.Familia");	
		$this->mArr["variable_persona_lista_no_familiares"]	= $xT2->Show("TR.No Familia");
	}
	function setToImprimir(){
		if($this->mOut == OUT_HTML OR $this->mOut == OUT_DEFAULT){
			$this->mTxt	.= "<script>window.print();</script>";
		}
	}
	function getOLang(){
		if($this->mOLang == null){$this->mOLang	= new cLang();}
		return $this->mOLang;
	}
	function setOut($out){$this->mOut = $out; }
	function setCreditoParsearEstadoDeCuenta($credito = false, $simple	= false, $emulado = false, $pagoCap = 0){
		$xQL		= new MQL();
		$xLi		= new cSQLListas();
		$xF			= new cFecha();
		$xL			= new cLang();
		$xRN		= new cReglaDeNegocio();
		$xCant		= new cCantidad();
		$txtVal		= $xRN->getCodigoPorRegla($xRN->reglas()->CREDITOS_ECUENTA_VALIDADOR);
		$detalle	= $xRN->getValorPorRegla($xRN->reglas()->CREDITOS_ESTADO_CUENTA_DETALLE);
		
		$html		= "";
		$out		= ($this->mOut == SYS_DEFAULT) ? OUT_HTML : $this->mOut;
		$inicial	= 0;
		//Inicializar el Crédito.

		
		if($this->mOCredito == null){
			//$this->mOCredito	= new cCredito($credito);
			//$this->mOCredito->init();
			//$inicial	= $this->mOCredito->getMontoAutorizado();
		}
		
		$rs		= $xQL->getDataRecord($xLi->getListadoDeOperacionesEstadoCuentaCred($credito));
		$arrF	= array();
		$arrR	= array();
		$TotCap	= 0;
		$TotInt	= 0;
		$TotMor	= 0;
		$totIv	= 0;
		$totIvO	= 0;
		$totOt	= 0;
		$RecItem= 0;
		$cssM	= ($out == OUT_HTML) ? " class='mny' " : "";
		$cssT	= "";//($out == OUT_HTML) ? " class='sumas' " : "";
		$arrMvtos= array();
		$con1	= $xL->getT("TR.Ministracion");
		$con2	= $xL->getT("TR.Pago");
		if($detalle == true){
			$html		.= "<thead><tr>";
			$html		.= "<th>" . $xL->getT("TR.FECHA"). "</th>";
			$html		.= "<th>#</th>";
			$html		.= "<th>" . $xL->getT("TR.PERIODO"). "</th>";
			$html		.= "<th>" . $xL->getT("TR.OPERACION"). "</th>";
			$html		.= "<th>" . $xL->getT("TR.CAPITAL"). "</th>";
			$html		.= "<th>" . $xL->getT("TR.INTERESES"). "</th>";
			$html		.= "<th>" . $xL->getT("TR.OTROS"). "</th>";
			$html		.= "<th>" . $xL->getT("TR.IVA"). "</th>";
			$html		.= "<th>" . $xL->getT("TR.SALDO DE CAPITAL"). "</th>";
			$html		.= "<th>" . $xL->getT("TR.OBSERVACIONES"). "</th>";
			$html		.= "</tr></thead><tbody>";
			$ppemulado		= 0; //Ids emulados
			$arrInc			= array();//Array incompletos
			$arrRC			= array();//Recibos con Número cambiados
			foreach ($rs as $rw){
				//control|fecha|recibo|parcialidad|operacion|monto|total_recibo|tipo_de_pago|observaciones|tipo_de_operacion|capital|interes|iva|otros
				$id			= $rw["control"];
				$fecha		= $rw["fecha"];
				$periodo	= $rw["parcialidad"];
				$recibo		= $rw["recibo"];
				$operacion	= $rw["operacion"];
				$monto		= $rw["monto"];
				$totalR		= $rw["total_recibo"];
				$tipoPago	= $rw["tipo_de_pago"];
				$notas		= $rw["observaciones"];
				$tipoOp		= $rw["tipo_de_operacion"];
				$capital	= $rw["capital"];
				$interes	= $rw["interes"];
				$otros		= $rw["otros"];
				$iva		= $rw["iva"];
				
				if($emulado == true AND ($interes != 0 OR $otros != 0 OR $iva != 0)){
					$periodo		= "";
				}
				
				$cnt		= ""; 	//contenido de concepto
				$plin		= true;	//Imprimir Linea
				$linea		= "";

				if($capital != 0){
					if($tipoOp == OPERACION_CLAVE_MINISTRACION){
						$inicial		= $monto;
					} else {
						if($tipoOp == OPERACION_CLAVE_DISPOCISION){
							$inicial		+= $monto;							
						} else {
							$inicial		-= $monto;
							$TotCap			+= $monto;
						}
					}
					
					
					if($emulado == true AND $tipoOp == OPERACION_CLAVE_PAGO_CAPITAL){
						
						$pagoref			= isset($arrInc[$ppemulado]) ? $arrInc[$ppemulado] : $pagoCap;
						
						if($capital !== $pagoref){
							//setLog("$ppemulado --- $items_em ");
							$plin			= false; //No imprimir
							$memulado		= $capital;
							$semulado		= $inicial+$capital;
							

							
							while($memulado > 0 ){
								$cnt		= "";
								$capemu		= $pagoCap;
								
								$ppemulado++;
								
								//Si existe
								if(isset($arrInc[$ppemulado])){
									$capemu		= $arrInc[$ppemulado];
									unset($arrInc[$ppemulado]);
								}
									
								
								$fidx			= $ppemulado;

								if($memulado < $capemu){
									$capemu			= $memulado;
									
									$pprox			= ($ppemulado);
									$arrInc[ $pprox ] = setNoMenorQueCero( ($pagoCap-$memulado));
									//setError("$pprox --- $capemu ----  " . $arrInc[ $pprox ]);
									$semulado		-= $memulado;
									$memulado		= 0;
									
									$ppemulado		= $ppemulado -1;
									
								} else {
									$semulado		-= $capemu;
									$memulado		-= $capemu;
									//unset($arrInc[$ppemulado]);
								}
								
								$linea		= "<tr>";
								$linea		.= "<td>" . $xF->getFechaCorta($fecha) . "</td>";
								$linea		.= "<td>" . $recibo . "</td>";
								$linea		.= "<td>" . $fidx . "</td>";
								$linea		.= "<td>" . $operacion . "</td>";
								$linea		.= "<td$cssM>" . $xCant->moneda($capemu) . "</td><td></td><td></td><td></td><td$cssM>" . $xCant->moneda($semulado) . "</td>";
								$linea		.= "<td>" . substr($notas, 0,20) . "</td>";
								$linea		.= "</tr>";
								
								$html		.= $linea;
							}
						} else {
							$ppemulado++;
							
							if($monto < $pagoCap){
								$arrInc[ $ppemulado ] = setNoMenorQueCero( ($pagoCap-$monto));
								
								//$ppemulado		= $ppemulado -1;
								//setError($ppemulado);
							}
							$cnt		= "<td$cssM>" . $xCant->moneda($monto) . "</td><td></td><td></td><td></td><td$cssM>" . $xCant->moneda($inicial) . "</td>";
							
						}
						
						
					} else {

						$cnt		= "<td$cssM>" . $xCant->moneda($monto) . "</td><td></td><td></td><td></td><td$cssM>" . $xCant->moneda($inicial) . "</td>";
					}
					
				}
				if($interes != 0){
					$cnt		= "<td></td><td$cssM>" . $xCant->moneda($monto) . "</td><td></td><td></td><td$cssM>" . $xCant->moneda($inicial) . "</td>";
					$TotInt		+= $monto;
				}
				if($otros != 0){
					$cnt		= "<td></td><td></td><td$cssM>" . $xCant->moneda($monto) . "</td><td></td><td$cssM>" . $xCant->moneda($inicial) . "</td>";
					$totOt		+= $monto;
				}
				if($iva >0){
					$cnt		= "<td></td><td></td><td></td><td$cssM>" . $xCant->moneda($monto) . "</td><td$cssM>" . $xCant->moneda($inicial) . "</td>";
					$totIv		+= $monto;
				}
				if($plin == true){
					$linea		= "<tr>";
					$linea		.= "<td>" . $xF->getFechaCorta($fecha) . "</td>";
					$linea		.= "<td>" . $recibo . "</td>";
					$linea		.= "<td>" . $periodo . "</td>";
					$linea		.= "<td>" . $operacion . "</td>";
					$linea		.= $cnt;
					$linea		.= "<td>" . substr($notas, 0,20) . "</td>";
					$linea		.= "</tr>";
					eval( $txtVal );
					$html		.= $linea;
				}
			}
			$html		.= "</tbody><tfoot><tr>";
			$html		.= "<th></th>";
			$html		.= "<th></th>";
			$html		.= "<th></th>";
			$html		.= "<th></th>";
			$html		.= "<th$cssT>" . $xCant->moneda($TotCap) . "</th>";
			$html		.= "<th$cssT>" . $xCant->moneda($TotInt) . "</th>";
			$html		.= "<th$cssT>" . $xCant->moneda($totOt) . "</th>";
			$html		.= "<th$cssT>" . $xCant->moneda($totIv) . "</th>";
			$html		.= "<th></th>";
			$html		.= "<th></th>";
			$html		.= "</tr></tfoot>";

		} else {
			foreach ($rs as $rw){
				//control|fecha|recibo|parcialidad|operacion|monto|total_recibo|tipo_de_pago|observaciones|tipo_de_operacion|capital|interes|iva|otros
				$id			= $rw["control"];
				$fecha		= $rw["fecha"];
				$periodo	= $rw["parcialidad"];
				$recibo		= $rw["recibo"];
				$operacion	= $rw["operacion"];
				$monto		= $rw["monto"];
				$totalR		= $rw["total_recibo"];
				$tipoPago	= $rw["tipo_de_pago"];
				$notas		= $rw["observaciones"];
				$tipoOp		= $rw["tipo_de_operacion"];
				$capital	= $rw["capital"];
				
				
				if(!isset($arrMvtos[$recibo])){
					$arrMvtos[$recibo][SYS_FECHA]				= $fecha;
					$arrMvtos[$recibo][SYS_NUMERO]				= $periodo;
					$arrMvtos[$recibo][SYS_CAPITAL]				= 0;
					$arrMvtos[$recibo][SYS_INTERES_NORMAL]		= 0;
					$arrMvtos[$recibo][SYS_INTERES_MORATORIO]	= 0;
					$arrMvtos[$recibo][SYS_AHORRO]				= 0;
					$arrMvtos[$recibo][SYS_TOTAL]				= $totalR;
					$arrMvtos[$recibo][SYS_IMPUESTOS]			= 0;
					$arrMvtos[$recibo]["SYS_IMPUESTOS_O"]		= 0;
					$arrMvtos[$recibo]["MINISTRACION"]			= 0;
					$arrMvtos[$recibo]["DISPOSICION"]			= 0;
					$arrMvtos[$recibo]["SYS_OTROS"]				= 0;
					$arrMvtos[$recibo]["NOTAS"]					= trim($notas);
					$arrMvtos[$recibo][SYS_INFO]				= $tipoOp;
					$arrMvtos[$recibo][SYS_SALDO]				= 0;
				}
				switch($tipoOp){
					case OPERACION_CLAVE_MINISTRACION:
						$arrMvtos[$recibo]["MINISTRACION"]		+= $monto;
						$inicial								= $monto;
						break;
					case OPERACION_CLAVE_DISPOCISION:
						$arrMvtos[$recibo]["DISPOCISION"]		+= $monto;
						
						break;						
					case OPERACION_CLAVE_PAGO_CAPITAL:
						$arrMvtos[$recibo][SYS_CAPITAL]			+= $monto;
						$TotCap									+= $monto;
						$inicial								-= $monto;
						break;
					case OPERACION_CLAVE_PAGO_INTERES:
						$arrMvtos[$recibo][SYS_INTERES_NORMAL]	+= $monto;
						$TotInt									+= $monto;
						break;
					case OPERACION_CLAVE_PAGO_MORA:
						$arrMvtos[$recibo][SYS_INTERES_MORATORIO]+= $monto;
						$TotMor									+= $monto;
						break;
					case OPERACION_CLAVE_PAGO_IVA_INTS:
						$arrMvtos[$recibo][SYS_IMPUESTOS]		+= $monto;
						$totIv									+= $monto;
						break;
					case OPERACION_CLAVE_PAGO_IVA_OTROS:
						$arrMvtos[$recibo]["SYS_IMPUESTOS_O"]	+= $monto;
						$totIvO									+= $monto;
						break;
					default:
						$arrMvtos[$recibo]["SYS_OTROS"]			+= $monto;
						$totOt									+= $monto;
						break;
				}
				$arrMvtos[$recibo][SYS_SALDO]					= $inicial;
	
			} //end for each
			$html	.= "<thead><tr>";
			$html	.= "<th>" . $xL->getT("TR.FECHA") . "</th>";
			$html	.= "<th>" . $xL->getT("TR.RECIBO") . "</th>";
			$html	.= "<th>" . $xL->getT("TR.PARCIALIDAD") . "</th>";
			$html	.= "<th>" . $xL->getT("TR.OPERACION") . "</th>";
			if($simple == false){
				$html	.= "<th>" . $xL->getT("TR.MINISTRACION") . "</th>";
				$html	.= "<th>" . $xL->getT("TR.CAPITAL") . "</th>";
				$html	.= "<th>" . $xL->getT("TR.INTERES") . "</th>";
				$html	.= ($TotMor == 0)? "" : "<th>" . $xL->getT("TR.MORATORIO") . "</th>";
				$html	.= ($totOt == 0) ? "" :  "<th>" . $xL->getT("TR.OTROS") . "</th>";
				
				$html	.= ($totIv == 0) ? "" : "<th>" . $xL->getT("TR.IVA") . "</th>";
				$html	.= ($totIvO == 0) ? "" : "<th>" . $xL->getT("TR.IVA OTROS") . "</th>";
			}
			$html	.= "<th>" . $xL->getT("TR.TOTAL") . "</th>";
			$html	.= "<th>" . $xL->getT("TR.SALDO DE CAPITAL") . "</th>";
			
			$html	.= "<th>" . $xL->getT("TR.OBSERVACIONES") . "</th>";
			$html	.= "</tr></thead><tbody>";
			$suma	= 0;
			$cssM	= ($out == OUT_HTML) ? " class='mny'" : "";
			foreach ($arrMvtos as $recibo => $datos){
				
				$tipo 	= $datos[SYS_INFO];
				$linea	= "";
				$linea	.= "<td>" . $xF->getFechaDDMM( $datos[SYS_FECHA] ) . "</td>";
				$linea	.= "<td>" . $recibo .  "</td>";
				$linea	.= "<td>" . $datos[SYS_NUMERO] .  "</td>";
				
				$linea	.= ($tipo != OPERACION_CLAVE_MINISTRACION AND $tipo != OPERACION_CLAVE_DISPOCISION) ? "<td>" . $con2 .  "</td>" : "<td>" . $con1 .  "</td>";
				
				if($simple == false){
					$linea	.= ($tipo != OPERACION_CLAVE_MINISTRACION AND $tipo != OPERACION_CLAVE_DISPOCISION) ? "<td />" : "<th$cssM>" . $xCant->moneda($datos["MINISTRACION"]) .  "</th>";
					$linea	.= ($datos[SYS_CAPITAL] > 0) ? "<td$cssM>" . $xCant->moneda($datos[SYS_CAPITAL]) .  "</td>" : "<td/>";
					$linea	.= ($datos[SYS_INTERES_NORMAL] > 0) ? "<td$cssM>" . $xCant->moneda($datos[SYS_INTERES_NORMAL]) .  "</td>" : "<td />";
					if($TotMor > 0){
						$linea	.= ($datos[SYS_INTERES_MORATORIO] > 0) ? "<td$cssM>" . $xCant->moneda($datos[SYS_INTERES_MORATORIO]) .  "</td>" : "<td />";
					}
					if($totOt > 0){
						$linea	.= ($datos["SYS_OTROS"] > 0) ? "<td$cssM>" . $xCant->moneda($datos["SYS_OTROS"]) .  "</td>" : "<td />";
					}			
					if($totIv > 0){
						$linea	.= ($datos[SYS_IMPUESTOS] > 0) ? "<td$cssM>" . $xCant->moneda($datos[SYS_IMPUESTOS]) .  "</td>" : "<td />";
					}
					if($totIvO > 0){
						$linea	.= ($datos["SYS_IMPUESTOS_O"] > 0) ? "<td$cssM>" . $xCant->moneda($datos["SYS_IMPUESTOS_O"]) .  "</td>" : "<td />";
					}
				}
				if($tipo == OPERACION_CLAVE_MINISTRACION  OR $tipo == OPERACION_CLAVE_DISPOCISION){
					$linea	.= "<td />";
				} else {
					$linea	.= "<th$cssM>" . $xCant->moneda($datos[SYS_TOTAL]) .  "</th>";
					$suma	+= $datos[SYS_TOTAL];
				}
				$linea	.= "<td$cssM>" . $xCant->moneda($datos[SYS_SALDO]) .  "</td>";
				$linea	.= "<td>" . $datos["NOTAS"] .  "</td>";
				$html	.= "<tr>$linea</tr>";
				$linea		= null;
			}
			$html	.= "</tbody><tfoot><tr>";
			$html	.= "<td></td>";
			$html	.= "<td></td>";
			$html	.= "<td></td>";
			$html	.= "<td></td>";
			$html	.= "<td></td>";
			if($simple == false){
				$html	.= "<th />";
				$html	.= "<th$cssM>" . $xCant->moneda($TotCap) . "</th>";
				$html	.= "<th$cssM>" . $xCant->moneda($TotInt) . "</th>";
				$html	.= ($TotMor == 0)? "" : "<th$cssM>" . $xCant->moneda($TotMor) . "</th>";
				$html	.= ($totOt == 0) ? "" :  "<th$cssM>" . $xCant->moneda($totOt) . "</th>";
				
				$html	.= ($totIv == 0) ? "" : "<th$cssM>" . $xCant->moneda($totIv) . "</th>";
				$html	.= ($totIvO == 0) ? "" : "<th$cssM>" . $xCant->moneda($totIvO) . "</th>";
			}
			$html	.= "<th$cssM>" . $xCant->moneda($suma) . "</th>";
			$html	.= "<td></td>";
			$html	.= "</tr></tfoot>";
		}
		$rs			= null;
		$arrF		= null;
		$arrR		= null;
		$arrMvtos	= null;
		return "<table>$html</table>";
	}
	function setNotificacionDeCobro($clave){
		$clave	= setNoMenorQueCero($clave);
		if($clave > 0){
			$xSeg	= new cSeguimiento_notificaciones();
			$xSeg->setData( $xSeg->query()->initByID($clave) );
			$this->setCredito($xSeg->numero_solicitud()->v());
			$this->setUsuario($xSeg->oficial_de_seguimiento()->v());
			
			$this->mArr["variable_notificacion_total"] 			= $xSeg->total()->v();
			$this->mArr["variable_notificacion_interes"] 		= $xSeg->interes()->v();
			$this->mArr["variable_notificacion_moratorio"] 		= $xSeg->moratorio()->v();
			$this->mArr["variable_notificacion_impuestos"] 		= $xSeg->impuestos()->v();
			$this->mArr["variable_notificacion_otros"] 			= $xSeg->otros_cargos()->v();
			$this->mArr["variable_notificacion_observaciones"] 	= $xSeg->observaciones()->v();
			
			//`idseguimiento_notificaciones`,`numero_notificacion`,`fecha_notificacion`,`oficial_de_seguimiento`,`capital``interes``moratorio``otros_cargos``impuestos``total``observaciones``formato`
		}
	}
	function setCajaLocal($clave, $datos = false){
		$this->mArr["variable_cajalocal_clave"]			= $clave;
		$this->mArr["variable_nombre_caja_local"]		= "";
		$this->mArr["variable_cajalocal_codigopostal"]	= "";
		if(SISTEMA_CAJASLOCALES_ACTIVA == true){
			$xCL	= new cCajaLocal($clave);
			if($xCL->init($datos) == true){
				$this->mArr["variable_nombre_caja_local"]		= $xCL->getNombre();
				$this->mArr["variable_cajalocal_codigopostal"]	= $xCL->getCodigoPostal();				
			}
		}
	}
	function setRegionLocal($clave, $datos = false){
		$this->mArr["variable_region_clave"]			= $clave;
		$this->mArr["variable_region_nombre"]			= "";
		if(SISTEMA_CAJASLOCALES_ACTIVA == true){
			$xReg	= new cPersonasRegiones($clave);
			if($xReg->init($datos) == true){
				$this->mArr["variable_region_nombre"]	= $xReg->getNombre();
			}
		}
	}
	function setFechaDeFormato($fecha = false){
		$xF	= new cFecha(0,$fecha);
		$this->mArr["variable_fecha_larga_actual"]									= $xF->getFechaLarga($fecha);
		$this->mArr["variable_fecha_dia_actual"]									= $xF->dia($fecha);
		$this->mArr["variable_fecha_dianombre_actual"]								= $xF->getDayName($fecha);
		$this->mArr["variable_fecha_mes_actual"]									= $xF->mes($fecha);
		$this->mArr["variable_fecha_mesnombre_actual"]								= $xF->getMesNombre($fecha);
		$this->mArr["variable_fecha_anno_actual"]									= $xF->anno($fecha);
	}
	function setAMLRiesgo($clave){
		$clave	= setNoMenorQueCero($clave);
		$this->mArr["var_aml_riesgo_fecha_origen"]			= "";
		$this->mArr["var_aml_riesgo_fecha_checking"]		= "";
		$this->mArr["var_aml_riesgo_mensaje"]				= "";
		$this->mArr["var_aml_riesgo_notas_checking"]		= "";
		$this->mArr["var_aml_riesgo_usuario"]				= "";
		$this->mArr["var_aml_riesgo_oficial_aml"]			= "";
		
		if($clave > 0){
			$xR	= new cAMLRiesgos($clave);
			if($xR->init() == true){
				$this->mArr["var_aml_riesgo_fecha_origen"]		= $xR->getFechaOrigen();
				$this->mArr["var_aml_riesgo_fecha_checking"]	= $xR->getFechaChecking();
				$this->mArr["var_aml_riesgo_mensaje"]			= $xR->getNotasSistema();
				$this->mArr["var_aml_riesgo_notas_checking"]	= $xR->getNotasChecking();
				$xUsr1		= new cOficial($xR->getUsuarioOrigen()); $xUsr1->init();
				$this->mArr["var_aml_riesgo_usuario"]			= $xUsr1->getNombreCompleto();
				$xUsr2		= new cOficial($xR->getOficialDest()); $xUsr2->init();
				$this->mArr["var_aml_riesgo_oficial_aml"]		= $xUsr2->getNombreCompleto();
				
			}
		}
	}
	function setAMLAlerta($clave){
		$clave	= setNoMenorQueCero($clave);
		$this->mArr["var_aml_riesgo_fecha_origen"]			= "";
		$this->mArr["var_aml_riesgo_fecha_checking"]		= "";
		$this->mArr["var_aml_riesgo_mensaje"]				= "";
		$this->mArr["var_aml_riesgo_notas_checking"]		= "";
		$this->mArr["var_aml_riesgo_usuario"]				= "";
		$this->mArr["var_aml_riesgo_oficial_aml"]			= "";
				
		if($clave > 0){
			$xR	= new cAMLAlertas($clave);
			if($xR->init() == true){
				$this->mArr["var_aml_riesgo_fecha_origen"]		= $xR->getFechaOrigen();
				$this->mArr["var_aml_riesgo_fecha_checking"]	= $xR->getFechaChecking();
				$this->mArr["var_aml_riesgo_mensaje"]			= $xR->getNotasSistema();
				$this->mArr["var_aml_riesgo_notas_checking"]	= $xR->getNotasChecking();
				$xUsr1		= new cOficial($xR->getUsuarioOrigen()); $xUsr1->init();
				$this->mArr["var_aml_riesgo_usuario"]			= $xUsr1->getNombreCompleto();
				$xUsr2		= new cOficial($xR->getOficialDest()); $xUsr2->init();
				$this->mArr["var_aml_riesgo_oficial_aml"]		= $xUsr2->getNombreCompleto();			
			}			
		}	
	}	

	function setOriginacionLeasing($clave){
		//Cargar Datos de la tabla.
		$xQL	= new MQL();
		$xTip	= new cTipos();
		
		$sql	= "SELECT   `originacion_leasing`.`idoriginacion_leasing`,		         `originacion_leasing`.`fecha_origen`,
		         `originacion_leasing`.`persona`,		         `originacion_leasing`.`credito`,
		         /*`originacion_leasing`.`marca`,*/
		         `vehiculos_marcas`.`nombre_marca` AS `marca`, 
		         `originacion_leasing`.`modelo`,
		         `originacion_leasing`.`annio`,		         `originacion_leasing`.`tipo_leasing`,
		         `vehiculos_usos`.`descripcion_uso` AS `tipo_uso`,		         `leasing_tipo_rac`.`nombre_tipo_rac` AS `tipo_rac`,
		         `vehiculos_gps`.`nombre_gps` AS `tipo_gps`,		         `leasing_originadores`.`nombre_originador` AS `originador`,
		         `leasing_usuarios`.`nombre` AS `suboriginador`,		         `originacion_leasing`.`precio_vehiculo`,
		         `originacion_leasing`.`monto_aliado`,		         `originacion_leasing`.`monto_accesorios`,
		         `originacion_leasing`.`monto_anticipo`,		         `originacion_leasing`.`monto_tenencia`,
		         `originacion_leasing`.`monto_garantia`,		         `originacion_leasing`.`monto_mtto`,
		         `originacion_leasing`.`comision_originador`,		         `originacion_leasing`.`comision_apertura`,
		         `originacion_leasing`.`tasa_iva`,		         `originacion_leasing`.`tasa_compra`,
		         `originacion_leasing`.`financia_seguro`,		         `originacion_leasing`.`financia_tenencia`,
		         `originacion_leasing`.`domicilia`,		         `originacion_leasing`.`paso_proceso`,
		         `originacion_leasing`.`describe_aliado`,		         `originacion_leasing`.`usuario`,
		         `originacion_leasing`.`nombre_cliente`,		         `originacion_leasing`.`nombre_atn`,
		         `oficiales`.`nombre_completo` AS `oficial`,		         `originacion_leasing`.`total_credito`,
		         `vehiculos_segmento`.`nombre_segmento` AS `segmento`,		         `general_estados`.`nombre` AS `entidad_federativa`,
		         `originacion_leasing`.`plazo`,		         `originacion_leasing`.`tasa_credito`,
		         `originacion_leasing`.`tasa_tiie`,		         `originacion_leasing`.`monto_gps`,
		         `originacion_leasing`.`monto_directo`,		         `originacion_leasing`.`monto_seguro`,
		         `originacion_leasing`.`monto_placas`,		         `originacion_leasing`.`monto_gestoria`,
		         `originacion_leasing`.`monto_notario`,		         `originacion_leasing`.`monto_residual`,
		         `originacion_leasing`.`cuota_vehiculo`,		         `originacion_leasing`.`cuota_aliado`,
		         `originacion_leasing`.`cuota_accesorios`,		         `originacion_leasing`.`cuota_tenencia`,
		         `originacion_leasing`.`cuota_mtto`,		         `originacion_leasing`.`cuota_gps`,
		         `originacion_leasing`.`cuota_seguro`,		         `originacion_leasing`.`monto_comision`,
		         `originacion_leasing`.`monto_originador`, `originacion_leasing`.`cuota_garantia`,
		         `originacion_leasing`.`es_moral`,`originacion_leasing`.`estatus`,`originacion_leasing`.`renta_deposito`,`originacion_leasing`.`renta_proporcional`,`originacion_leasing`.`renta_extra`
		         
		FROM     `originacion_leasing` 
		INNER JOIN `leasing_originadores`  ON `originacion_leasing`.`originador` = `leasing_originadores`.`idleasing_originadores` 
		INNER JOIN `leasing_tipo_rac`  ON `originacion_leasing`.`tipo_rac` = `leasing_tipo_rac`.`idleasing_tipo_rac` 
		INNER JOIN `leasing_usuarios`  ON `originacion_leasing`.`suboriginador` = `leasing_usuarios`.`idleasing_usuarios` 
		INNER JOIN `vehiculos_usos`  ON `originacion_leasing`.`tipo_uso` = `vehiculos_usos`.`idvehiculos_usos` 
		INNER JOIN `vehiculos_marcas`  ON `originacion_leasing`.`marca` = `vehiculos_marcas`.`idvehiculos_marcas` 
		INNER JOIN `oficiales`  ON `originacion_leasing`.`oficial` = `oficiales`.`id` 
		INNER JOIN `vehiculos_segmento`  ON `originacion_leasing`.`segmento` = `vehiculos_segmento`.`idvehiculos_segmento` 
		INNER JOIN `general_estados`  ON `originacion_leasing`.`segmento` = `general_estados`.`clave_numerica` ,
		         `vehiculos_gps` WHERE `originacion_leasing`.`idoriginacion_leasing`=$clave LIMIT 0,1";
		$D			= $xQL->getDataRow($sql);
		$xF			= new cFecha();
		$xT			= new cOriginacion_leasing();
		$xCredOrg	= new cCreditosLeasing($clave); $xCredOrg->init();
		$xRuls		= new cReglaDeNegocio();
		$IvaNoInc	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_IVA_NOINC);
		$DividirAnt	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_ANT_DIV);		//Dividir Anticipo
		
		
		$tipo_rac	= $xCredOrg->getTipoDeRAC();
		$xT->setData($D);
		$Fld		= $xT->query()->getCampos();
		$mIVA		= $xT->tasa_iva()->v();
		$factorIVA	= 1 / (1+$mIVA);
		
		$arrBan		= array("marca" => "marca");
		foreach ($Fld as $idx => $cnt){
			$xFld			= new MQLCampo($cnt);
			
			if($xFld->isNumber() == true AND !isset($arrBan[$idx]) ){
				$v			= $xFld->v();
				if($IvaNoInc == true){
					$v			= $xFld->v();
					$v			= round(($v + ($v * TASA_IVA)),2);
				}
				$this->mArr["var_leasing_$idx"]		= getFMoney($v);
				$this->mArr["var_leasing_si_$idx"]	= getFMoney(($v*$factorIVA));
			} else {
				$this->mArr["var_leasing_$idx"]	= $xFld->v();
				unset($this->mArr["var_leasing_si_$idx"]);
			}
		}
		//setError($xT->oficial()->v());
		if($xCredOrg->getClaveDeOficial()>0){
			$this->setUsuario($xCredOrg->getClaveDeOficial());
		}
		//Datos basicos vehiculo Dummy
		for($ivx = 1; $ivx <=6; $ivx++){
			$this->mArr["var_leas_"  . $ivx . "_vehiculo_segmento"]		= "";
			$this->mArr["var_leas_"  . $ivx . "_vehiculo_total"]		= "";
			$this->mArr["var_leas_"  . $ivx . "_vehiculo_marca"]		= "";
			$this->mArr["var_leas_"  . $ivx . "_vehiculo_ant"]			= "";
			$this->mArr["var_leas_"  . $ivx . "_vehiculo_uso"]			= "";
			$this->mArr["var_leas_"  . $ivx . "_vehiculo_desc"]			= "";
		}

		//===================================== Datos basicos de los vehiculos
		$rsV			= $xQL->getDataRecord("SELECT * FROM `leasing_activos` WHERE `clave_leasing`=$clave LIMIT 0,10");
		$cntV			= 1;
		$xTV			= new cLeasing_activos();
		foreach($rsV as $rwV){
			$xSegm	= new cLeasingActivosSegmentos($rwV[$xTV->SEGMENTO]); $xSegm->init();
			$xMarc	= new cLeasingActivosMarcas($rwV[$xTV->MARCA]); $xMarc->init();
			$xUso	= new cLeasingActivosUsos($xCredOrg->getVehiculoUso()); $xUso->init();
			
			$this->mArr["var_leas_"  . $cntV . "_vehiculo_segmento"]		= $xSegm->getNombre();
			$this->mArr["var_leas_"  . $cntV . "_vehiculo_total"]			= getFMoney($rwV[$xTV->VALOR_NOMINAL]);
			$this->mArr["var_leas_"  . $cntV . "_vehiculo_marca"]			= $xMarc->getNombre();
			$this->mArr["var_leas_"  . $cntV . "_vehiculo_ant"]				= getFMoney($rwV[$xTV->MONTO_ANTICIPO]);
			$this->mArr["var_leas_"  . $cntV . "_vehiculo_uso"]				= $xUso->getNombre();
			$this->mArr["var_leas_"  . $cntV . "_vehiculo_desc"]			= $rwV[$xTV->DESCRIPCION];
			$cntV++;

		}
		//=============== Datos del Agente
		$xAge			= new cLeasingOriginadores($xCredOrg->getClaveDeOriginador());
		if($xAge->init() == true){
			$this->mArr["var_agente_nombre"]	= $xAge->getNombre();
			$this->mArr["var_agente_direccion"]	= $xAge->getDomicilio();
			$this->mArr["var_agente_telefono"]	= $xAge->getTelefono();
		}
		//=============== Datos del promotor suboriginador
		$xSubO			= new cLeasingUsuarios($xCredOrg->getClaveDeSubOriginador());  $xSubO->init();
		$this->mArr["var_leas_promotor_nombre"]		= $xSubO->getNombre();
		$this->mArr["var_leas_promotor_mail"]		= $xSubO->getCorreoElectronico();
		$this->mArr["var_leas_promotor_telefono"]	= "";//Terminar
		//===================================== Precio del vehiculo fixeado
		if($IvaNoInc == true){
			$this->mArr["var_leasing_precio_vehiculo"]		= getFMoney($xCredOrg->getMontoVehiculo());
			$this->mArr["var_leasing_si_precio_vehiculo"]	= getFMoney(($xCredOrg->getMontoVehiculo()*$factorIVA));
		}
		$this->mArr["var_leasing_idoriginacion_leasing"]	= $xTip->cSerial(6, $xCredOrg->getClave());
		
		$SeguroInicial										= $xCredOrg->getSeguroInicial();
		$SeguroFinanciado									= $xCredOrg->getSeguroFinanciado();
		$TenenciaInicial									= $xCredOrg->getTenenciaInicial();
		$TenenciaFinanciado									= $xCredOrg->getTenenciaFinanciado();
		
		//valores de Calculo.
		$this->mArr["var_leasing_seguro_inicial"]			= getFMoney($SeguroInicial);
		$this->mArr["var_leasing_si_seguro_inicial"]		= getFMoney($SeguroInicial*$factorIVA);
		
		$this->mArr["var_leasing_seguro_financiado"]		= getFMoney($SeguroFinanciado);
		$this->mArr["var_leasing_si_seguro_financiado"]		= getFMoney($SeguroFinanciado*$factorIVA);
		
		$this->mArr["var_leasing_tenencia_inicial"]			= getFMoney($TenenciaInicial);
		$this->mArr["var_leasing_si_tenencia_inicial"]		= getFMoney($TenenciaInicial*$factorIVA);
		
		$this->mArr["var_leasing_tenencia_financiado"]		= getFMoney($TenenciaFinanciado);
		$this->mArr["var_leasing_si_tenencia_financiado"]	= getFMoney($TenenciaFinanciado*$factorIVA);
		
		$this->mArr["var_leasing_esp_renta_extra"]			= getFMoney($xCredOrg->getAnticipo()); //Renta extraordinaria especial
		$this->mArr["var_leasing_esp2_renta_extra"]			= 0; //Renta extraordinaria especial 2
		$this->mArr["var_leasing_esp2_si_renta_extra"]		= 0;
		//=================================================
		$vecmonto											= $xCredOrg->getValorDeVenta();
		$veciva												= round(($vecmonto * $mIVA),2);
		
		$this->mArr["var_vehiculo_vec_monto"]				= getFMoney($vecmonto);
		$this->mArr["var_vehiculo_vec_iva"]					= getFMoney($veciva);
		$this->mArr["var_vehiculo_vec_total"]				= getFMoney(($veciva + $vecmonto));
		//=================================================
		$rrentaextra										= $xCredOrg->getAnticipo();
		
		$tgestoria											= $xCredOrg->getMontoGestoria() + $xCredOrg->getMontoPlacas();

		
		$tgastosflotilla									= 0;
		$trentaextra										= 0;
		
		$this->mArr["var_leasing_esp_monto_gestoria"]		= getFMoney($tgestoria);
		$this->mArr["var_leasing_esp_si_monto_gestoria"]	= getFMoney( ($tgestoria*$factorIVA) );
		
		
		$this->mArr["var_leasing_esp_monto_flotilla"]		= 0;
		$this->mArr["var_leasing_esp_si_monto_flotilla"]	= 0;
		//if($DivAnt == true){
			if($xCredOrg->getEsDeCarga() == true){
				$trentaextra									= $xCredOrg->getAnticipo();

				$this->mArr["var_leasing_esp2_renta_extra"]		= getFMoney($trentaextra);
				$this->mArr["var_leasing_esp2_si_renta_extra"]	= getFMoney(($trentaextra*$factorIVA) );
				
			} else {
				
				//if($DivAnt == true){
					//monto_gestoria
					$m1												= round(($xCredOrg->getAnticipo() * 0.6),2) + $xT->monto_gestoria()->v();
					$m2												= round(($xCredOrg->getAnticipo() * 0.4),2);
					$tgastosflotilla								= $m2;
					
					$this->mArr["var_leasing_esp_monto_flotilla"]	= getFMoney($m2);
					$this->mArr["var_leasing_esp_si_monto_flotilla"]= getFMoney(($m2*$factorIVA));
					$tgestoria										= $tgestoria+$m1;
					$this->mArr["var_leasing_esp_monto_gestoria"]	= getFMoney(($tgestoria));
					$this->mArr["var_leasing_esp_si_monto_gestoria"]= getFMoney( (($tgestoria)*$factorIVA) );
				//} else {
				//	$tgastosflotilla								= $xCredOrg->getAnticipo();
				//}
			}
		//}
		//$this->mArr["var_leasing_esp_renta_extra"]			= 0; //Renta extraordinaria especial
		/*el monto total del enganche o de la renta se divide en 2 el 60% para gestoría del contrato y el 40% para administración de flotilla*/
		
		
		$tfinanciado										= $xT->precio_vehiculo()->v() + $xT->monto_aliado()->v() + $xCredOrg->getTenenciaFinanciado() + $xT->monto_accesorios()->v() + $xT->monto_garantia()->v();
		$tfinanciado										= ($tfinanciado + $xCredOrg->getSeguroFinanciado()) - $rrentaextra;
		
		
		$this->mArr["var_leasing_total_financiado"]			= getFMoney($tfinanciado); 
		$this->mArr["var_leasing_na_total_financiado"]		= getFMoney(($tfinanciado-$xCredOrg->getAnticipo()));
		
		$this->mArr["var_leasing_limite_vigencia"]			= $xF->getFechaLarga( $xF->setSumarDias(CREDITO_LEASING_DIAS_VIG_COT, $xCredOrg->getFechaCreacion() ) );
		$this->mArr["var_leasing_limite_deducible"]			= $xCredOrg->getMontoAjuste();// getFMoney(CREDITO_LEASING_LIMITE_DED);
		
		//Subtotal inicial especiales
		
		$inicialSub			= $tgastosflotilla+$trentaextra+$tgestoria+$xCredOrg->getMontoComision()+$xCredOrg->getMontoNotario();
		
		
		
		$inicialSub			+= $xCredOrg->getTenenciaInicial()+$xCredOrg->getSeguroInicial();
		
		$SumaSinAnticipos	= $inicialSub;
		
		//Cuotas proporcionales
		$inicialSub		+= $xCredOrg->getMontoDepositoGarantia()+$xCredOrg->getMontoRentaProporcional();
		
		

		
		$totalSub		= $inicialSub;
		$inicialSub		= round(($inicialSub*$factorIVA),2);
		$mIVAInicial	= round(($inicialSub*$mIVA),2);
		
		
		
		
		$this->mArr["var_leasing_es_inicial_subtotal"]		= getFMoney($inicialSub);
		$this->mArr["var_leasing_es_inicial_iva"]			= getFMoney($mIVAInicial);
		$this->mArr["var_leasing_es_inicial_total"]			= getFMoney(($totalSub));
		
		
		//Subtotal Iniciales
		$inicialSub		= $xT->monto_anticipo()->v() + $xCredOrg->getMontoComision() + $xT->monto_gestoria()->v() + $xCredOrg->getSeguroInicial() + $xT->monto_notario()->v() + $xT->renta_proporcional()->v();
		$inicialSub		+= $xCredOrg->getTenenciaInicial();
		$inicialSubO	= $inicialSub;
		
		$inicialSub		= round(($inicialSub*$factorIVA),2);
		$mIVAInicial	= round(($inicialSub*$mIVA),2);
		
		
		
		
		$this->mArr["var_leasing_inicial_subtotal"]		= getFMoney($inicialSub);
		$this->mArr["var_leasing_inicial_iva"]			= getFMoney($mIVAInicial);
		$this->mArr["var_leasing_inicial_total"]		= getFMoney(($inicialSub+$mIVAInicial));
		
		
		
		
		$inicialSub2	= ($inicialSubO-$xT->monto_anticipo()->v());
		$inicialSub2	= round(($inicialSub2*$factorIVA),2);
		$mIVAInicial2	= round(($inicialSub2*$mIVA),2);
		
		$this->mArr["var_leasing_na_inicial_subtotal"]	= getFMoney($inicialSub2);
		$this->mArr["var_leasing_na_inicial_iva"]		= getFMoney($mIVAInicial2);
		$this->mArr["var_leasing_na_inicial_total"]		= getFMoney(($inicialSub2+$mIVAInicial2));
		
		//accesorios aliado y vehiculo
		$mVehiculoTotal									= $xT->precio_vehiculo()->v() + $xT->monto_accesorios()->v() + $xT->monto_aliado()->v();
		$this->mArr["var_leasing_vehiculo_total"]		= getFMoney($mVehiculoTotal);
		
		//Total cuotas
		$this->mArr["var_leasing_cuota_basica"]			= getFMoney($xCredOrg->getCuotaRenta());
		$this->mArr["var_leasing_si_cuota_basica"]		= getFMoney(($xCredOrg->getCuotaRenta()*$factorIVA));
		//$SubTotal		= $CuotaAcc+$CuotaMtto+$CuotaRenta+$CuotaSeguro+$CuotaTenencia;
		$rentasub										= $xCredOrg->getCuotaAccesorios() + $xCredOrg->getCuotaMtto() +  $xCredOrg->getCuotaRenta() + $xCredOrg->getCuotaSeguro() + $xCredOrg->getCuotaTenencia();
		//$rentasub										= round(($rentasub*$factorIVA),2);
		$rentaiva										= round(($rentasub*$mIVA),2);
		$rentatot										= round(($rentasub+$rentaiva),2);
		
		
		
		
		$this->mArr["var_leasing_renta_subtotal"]		= getFMoney($rentasub);
		$this->mArr["var_leasing_renta_iva"]			= getFMoney($rentaiva);
		$this->mArr["var_leasing_renta_total"]			= getFMoney($rentatot);
		//Especial de Rentas
		
		//$SubTotal		= $CuotaAcc+$CuotaMtto+$CuotaRenta+$CuotaSeguro+$CuotaTenencia;
		$rentasub										= $xCredOrg->getCuotaAccesorios() + $xCredOrg->getCuotaMtto() +  $xCredOrg->getCuotaRenta() + $xCredOrg->getCuotaSeguro() + $xCredOrg->getCuotaTenencia();
		$rentasub										+= $xCredOrg->getCuotaGtiaExtendida();
		
		$rentasub										= round(($rentasub*$factorIVA),2);
		$rentaiva										= round(($rentasub*$mIVA),2);
		$rentatot										= round(($rentasub+$rentaiva),2);
		
		$this->mArr["var_leasing_renta_subtotal"]		= getFMoney($rentasub);
		$this->mArr["var_leasing_renta_iva"]			= getFMoney($rentaiva);
		$this->mArr["var_leasing_renta_total"]			= getFMoney($rentatot);
		
		$EsAdministrado									= $xCredOrg->getEsAdministrado();
		$RentaSinIva									= $xCredOrg->getRentaSinIva();
		//Plazos
		$xEsc											= new cLeasing_escenarios();
		$sql											= "SELECT * FROM `leasing_escenarios`";
		$rs												= $xQL->getDataRecord($sql);
		
		foreach ($rs as $rw){
			$xEsc->setData($rw);
			$pzo			= $xEsc->plazo()->v();
			$xLeasTasa		= new cLeasingTasas();
			$xLeasTasa->initByPlazoRAC($pzo, $tipo_rac);
			$TasaPzo		= $xLeasTasa->getTasa(); //Esta tasa no ha sido personalizado
			$TasaPzo		= $xCredOrg->getTasaInteres($pzo);
			
			$TasaInteres	= $TasaPzo / 100;
			$Frecuencia		= CREDITO_TIPO_PERIOCIDAD_MENSUAL;
			$TasaIVA		= $xT->tasa_iva()->v();
			
			$xCalc			= new cLeasingEmulaciones($pzo, $TasaInteres, $Frecuencia, $TasaIVA);
			//setLog("$pzo, $TasaInteres, $Frecuencia, $TasaIVA");
			$TasaResPzo		= $xCredOrg->getTasaResidualPzo($pzo);
			
			$xCalc->setMontoComisionAgen($xCredOrg->getMontoComisionAgencia());
			$xCalc->setMontoComisionOrg($xCredOrg->getMontoComisionOrigen());
			
			$residual		= $xCalc->getValorResidual($xCredOrg->getMontoVehiculo(), $xCredOrg->getMontoAliado(), $pzo, $TasaResPzo, $xCredOrg->getMontoAnticipo(true) );//  $xT->monto_residual()->v();
			
			$MontoGPS		= $xCalc->getMontoGPS($xCredOrg->getClaveDePlanGPS());
			$CuotaRenta		= $xCalc->getCuotaRenta($xT->precio_vehiculo()->v(), $xT->monto_anticipo()->v(), $residual, $xT->monto_aliado()->v(), $MontoGPS);
			
			$CuotaSeguro	= $xCalc->getCuotaSeguro($xT->monto_seguro()->v(), $xT->financia_seguro()->v());
			
			$CuotaTenencia	= $xCalc->getCuotaTenencia($xT->monto_tenencia()->v(), $xT->financia_tenencia()->v());
			$CuotaMtto		= $xCalc->getCuotaMtto($xT->monto_mtto()->v());
			$CuotaAcc		= $xCalc->getCuotaAccesorios($xT->monto_accesorios()->v());
			

			
			
			$SumarRenta		= true;
			$RentaEnDep		= $xCalc->getMontoRentaDeposito();
			
			if($xCredOrg->getMontoDepositoGarantia() <= 0){
				$SumarRenta	= false;
				$RentaEnDep	= 0;
			}
			//setError($xCredOrg->getMontoDepositoGarantia());
			
			$CuotaServs		= $xCredOrg->getMontoAjuste();
			if($xCredOrg->getMontoRentaProporcional() <= 0){
				$RentaProp		= 0;
			} else {
				$RentaProp		= $xCalc->getMontoRentaProp();
			}
			
			
			$tasares		= $xCredOrg->getTasaResidualPzo($pzo);
			//No Aliado
			$CuotaAliado	= $xCalc->getCuotaAliado($xT->monto_aliado()->v());
			$CuotaRentaNA	= $CuotaRenta - $CuotaAliado; //Porque se disminuye?
			//setLog("$pzo -- $CuotaRenta - $CuotaAliado ");
			//setLog("$pzo - $CuotaAcc+$CuotaMtto+$CuotaRenta+$CuotaSeguro+$CuotaTenencia");
			$SubTotal		= $CuotaAcc+$CuotaMtto+$CuotaRenta+$CuotaSeguro+$CuotaTenencia;
			$SubTotal		= round($SubTotal,2);
			$total			= $SubTotal;
			if($IvaNoInc == false){
				$SubTotal	= ($SubTotal*$factorIVA);
			}
			$Iva			= round(($SubTotal*$mIVA),2);
			if($RentaSinIva == true){
				$Iva		= 0;
			}
			
			$this->mArr["var_leasing_" . $pzo . "_cuota_renta" ]		= getFMoney($CuotaRenta);
			$this->mArr["var_leasing_" . $pzo . "_cuota_seguro" ]		= getFMoney($CuotaSeguro);
			$this->mArr["var_leasing_" . $pzo . "_cuota_tenencia" ]		= getFMoney($CuotaTenencia);
			$this->mArr["var_leasing_" . $pzo . "_cuota_mtto" ]			= getFMoney($CuotaMtto);
			$this->mArr["var_leasing_" . $pzo . "_cuota_accesorios" ]	= getFMoney($CuotaAcc);
			$this->mArr["var_leasing_" . $pzo . "_cuota_servicios" ]	= getFMoney($CuotaServs);
			if($CuotaServs>0){
				$this->mArr["var_leasing_" . $pzo . "_cuota_renta" ]	= getFMoney(($CuotaRenta-$CuotaServs));
			}
			
			$this->mArr["var_leasing_" . $pzo . "_cuota_na_renta" ]		= getFMoney($CuotaRentaNA);
			$this->mArr["var_leasing_" . $pzo . "_cuota_aliado" ]		= getFMoney($CuotaAliado);
			$pzoresidual												= $xCalc->getValorResidual($xT->precio_vehiculo()->v(), $xT->monto_aliado()->v(), $pzo, $tasares, $xCredOrg->getMontoAnticipo(true));
			$this->mArr["var_leasing_" . $pzo . "_residual" ]			= getFMoney($pzoresidual);
			
			$this->mArr["var_leasing_" . $pzo . "_proporcional" ]		= getFMoney($xT->renta_proporcional()->v());
			
			
			
			
			$this->mArr["var_leasing_" . $pzo . "_subtotal" ]			= getFMoney($SubTotal);
			
			
			
			$this->mArr["var_leasing_" . $pzo . "_impuestos" ]			= getFMoney($Iva);
			//$this->mArr["var_leasing_" . $pzo . "_total" ]			= getFMoney($total);
			$this->mArr["var_leasing_" . $pzo . "_mensual_total" ]		= getFMoney($total);
			$this->mArr["var_leasing_" . $pzo . "_suma_total" ]			= getFMoney(($total+$Iva));
		
			$vecmontox													= $xCredOrg->getValorDeVenta($pzo);
			$vecivax													= round(($vecmontox * $mIVA),2);
			$this->mArr["var_vehiculo_" . $pzo . "_vec_monto"]			= getFMoney($vecmontox);
			$this->mArr["var_vehiculo_" . $pzo . "_vec_iva"]			= getFMoney($vecivax);
			$this->mArr["var_vehiculo_" . $pzo . "_vec_total"]			= getFMoney(($vecivax + $vecmontox));
			
			//Renta Proporcional
			$h_renta_deposito											= ($SumarRenta == false) ? getFMoney(0) : getFMoney($RentaEnDep);
			
			$this->mArr["var_leasing_" . $pzo . "_renta_deposito"]		= $h_renta_deposito;
			
			//var_leasing_12_renta_deposito
			$this->mArr["var_leasing_" . $pzo . "_renta_proporcional"]	= getFMoney($RentaProp);
			$this->mArr["var_leasing_" . $pzo . "_si_renta_proporcional"]	= getFMoney( ($RentaProp*$factorIVA) );
			
			//var_leasing_12_si_renta_proporcional
			
			
			$pp_renta_disminuida= ($SumarRenta == false) ? $RentaEnDep : 0;
			
			$pp_subtotal_iva	= ($SumaSinAnticipos + $RentaProp) * $factorIVA;	//Quitar la renta y sacar el IVA

			
			$pp_subtotal		= $pp_subtotal_iva + $RentaProp; 					//Suma con renta de deposito
			$pp_iva				= $pp_subtotal_iva * $mIVA;							//Calcular el IVA
			$pp_total			= $pp_subtotal + $pp_iva;
			
			if($SumarRenta == false){
				$pp_subtotal 	= $pp_subtotal - $pp_renta_disminuida;
				$pp_total		= $pp_total - $pp_renta_disminuida;
			}
			
			$this->mArr["var_leasing_" . $pzo . "_pp_subtotal"]		= getFMoney($pp_subtotal);			//Agregado a la lista
			$this->mArr["var_leasing_" . $pzo . "_iva_total"]		= getFMoney($pp_iva);				//Agregado a la lista
			$this->mArr["var_leasing_" . $pzo . "_inicial_total"]	= getFMoney($pp_total);				//Agregado a la lista
			
			//Ocultar columnas
			$arrCols					= $xCredOrg->getOmitidos();
			foreach ($arrCols as $idx => $cnt){
				$this->mArr["td_$cnt"]		= "inv";
			}
			//var_leasing_12_pp_subtotal
			//var_leasing_48_iva_total
			//var_leasing_48_inicial_total
			
		}
		
		
	}
	function setVehiculoLeasing($clave, $conteo = 1){
		//------ Iniciar el Activo
		$xAct	= new cLeasingActivos($clave);
		if($xAct->init() == true ){
			$xCant		= new cCantidad();
			$this->mArr["var_vehiculo_serie"]			= $xAct->getSerie();
			$this->mArr["var_vehiculo_motor"]			= $xAct->getMotor();
			$this->mArr["var_vehiculo_color"]			= $xAct->getColor();
			$this->mArr["var_vehiculo_factura"]			= $xAct->getFactura();
			$this->mArr["var_vehiculo_placas"]			= $xAct->getPlacas();
			$this->mArr["var_vehiculo_modelo"]			= $xAct->getModelo();
			
			$this->mArr["var_vehiculo_vec_monto"]		= getFMoney($xAct->getMontoVEC());
			$this->mArr["var_vehiculo_vec_iva"]			= getFMoney(($xAct->getMontoVEC() * TASA_IVA));
			$totalvec									= round(($xAct->getMontoVEC() * TASA_IVA),2) + $xAct->getMontoVEC();
			$this->mArr["var_vehiculo_vec_total"]		= getFMoney( $totalvec );
			
			$this->mArr["var_vehiculo_vec_letrastotal"]	= $xCant->letras($totalvec);
			
			$this->mArr["var_vehiculo_describe"]		= $xAct->getNombre();
			$this->mArr["var_vehiculo_niv"]				= $xAct->getNIV();
			$this->mArr["var_leasing_vehiculo_total"]	= getFMoney($xAct->getValor());
			$this->mArr["var_leasing_annio"]			= $xAct->getAnnio();
			$this->mArr["var_leasing_modelo"]			= $xAct->getNombre();
			
			
			
			$xVS	= new cLeasingActivosSegmentos($xAct->getClaveDeSegmento()); $xVS->init();
			$this->mArr["var_vehiculo_segmento"]		= $xVS->getNombre();
			$this->mArr["var_leas_"  . $conteo . "_vehiculo_segmento"]		= $xVS->getNombre();
			$this->mArr["var_leas_"  . $conteo . "_vehiculo_total"]			= getFMoney($xAct->getValor());
			//=============== Datos del Proveedor
			$xSocP	= new cSocio($xAct->getClaveDeProveedor());
			if($xSocP->init() == true){
				$this->mArr["var_proveedor_nombre"]		= $xSocP->getNombreCompleto();
				$this->mArr["var_proveedor_direccion"]	= $xSocP->getDomicilio();
				$this->mArr["var_proveedor_telefono"]	= $xSocP->getTelefonoPrincipal();
				$cCred	= new cCredito($xAct->getClaveDeCredito());
				if($cCred->init() == true){
					$this->mArr["var_leasing_residualplan"]	= "<table class='plan_de_pagos'><thead><tr><th>Pago</th><th>Fecha</th><th>Monto</th></tr></thead><tbody><tr><td>Unico</td><td>" . $cCred->getFechaDevencimientoLegal() . "</td><td>" . getFMoney($xAct->getMontoVEC()) . "</td></tr></tbody></table>";
				}
			}
			// Marca
			$xMarc	= new cLeasingActivosMarcas($xAct->getClaveDeMarca());
			if($xMarc->init() == true){
				$this->mArr["var_leasing_marca"]		= $xMarc->getNombre();
			}
		}
	}
	private function getRS($sql){
		$xQL	= new MQL();
		$data	= $xQL->getDataRecord($sql);
		$xQL	= null;
		return $data;
	}
	private function getTEsquema($tabla){
		return $this->getRS("SELECT * FROM `general_structure` WHERE `tabla`='$tabla' ");
	}
	private function getMasIVA($monto){
		$monto		= $monto + ($monto+TASA_IVA);
		return round($monto,2);
	}
	
	function render($init = "", $end = ""){
		$xFS	= new cFileSystem();
		ini_set('xdebug.max_nesting_level', 100);
		
		if($this->mOut == OUT_HTML OR $this->mOut == OUT_DEFAULT){
			//$this->setToImprimir();
			//$this->mTxt .= "<script>$(window).contextMenu(ReportMenu,{triggerOn:'contextmenu'});</script>";
			$this->mTxt .= "<script>document.onkeydown = KeyPress;</script>";
			
			echo $init;
			echo $this->get();
			echo $end;
		} else {
			switch($this->mOut){
				case OUT_PDF:
					$str	= $init;
					$str	.= $this->get();
					$str	.= $end;
					
					//Nuevo DOM
					$dompdf = null;
					//Agregar Limite de Memoria
					try {
						//use Dompdf\Dompdf;
						$dompdf = new Dompdf\Dompdf();
						$dompdf->loadHtml($str);
						$dompdf->setPaper("letter", "portrait");
						$dompdf->render();
						$nn		= $xFS->cleanNombreArchivo($this->getTitulo() . "_" . $this->mDocumento . "_" . $this->mID . "_" . time(), true);
						$nn		= $nn . ".pdf";
						# Enviamos el fichero PDF al navegador.
						$dompdf->stream($nn);
						
					} catch (Exception $e) {
						
					}
					//$this->mMessages	.= "ERROR\tNo se genera el Archivo PDF\r\n";
					/*$nn		= $xFS->cleanNombreArchivo($this->getTitulo() . "_" . $this->mDocumento . "_" . $this->mID . "_" . time(), true);
					$ff		= $xFS->setConvertToPDF($str, $nn);
					if($ff !== ""){
						header("Content-type: application/pdf");
						header("Content-Disposition: attachment; filename=\"$nn.pdf\"; ");
						readfile($ff);
					}*/
					
				break;
				case OUT_DOC:
					$html	= $init;
					$html	.= $this->get();
					$html	.= $end;
					
					$html	= str_replace("../css/", SAFE_HOST_URL . "css/", $html);
					$html	= str_replace("../js/", SAFE_HOST_URL . "js/", $html);
					$html	= str_replace("../images/", SAFE_HOST_URL . "images/", $html);
					$html 	= preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
					
					$nn		= $xFS->cleanNombreArchivo($this->getTitulo() . "_" . $this->mDocumento . "_" . $this->mID . "_" . time(), true);
					$ff		= $xFS->setConvertToDocx($html, $nn);
					if($ff !== ""){
						if(file_exists($ff)){
							header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
							header("Content-Disposition: attachment; filename=\"$nn.docx\"; ");
							readfile($ff);
						}
					}
				break;
			}
		}
		
	}
}

//======= I(mportaciones
class cFIcons {
	public $DEFAULT 	= "";
	public $DINERO 		= "fa-money";
	public $DOLAR 		= "fa-usd";
	public $EXPORTAR 	= "exportar";
	public $REPORTE 	= "fa-book";
	public $REPORTE2 	= "fa-rss";
	public $REPORTE3 	= "fa-bar-chart";
	public $REPORTE4 	= "fa-area-chart";
	public $REPORTE5 	= "fa-files-o";
	
	public $REGISTROS 	= "registros";
	public $CONTABLE 	= "contabilidad";
	public $TIPO 		= "perfil";
	public $PERSONA 	= "fa-user-circle";
	public $ELIMINAR	= "fa-trash";
	public $DESCARTAR	= "fa-ban";
	public $BANCOS		= "fa-university";
	public $BUSCAR		= "fa-search";
	public $BLOQUEAR	= "fa-lock";
	public $OK			= "aceptar";
	public $NO			= "cancelar";
	public $RANDOM		= "fa-random";
	public $RECARGAR	= "refrescar";
	public $CARGAR		= "fa-arrow-circle-up";
	public $DESCARGAR	= "fa-arrow-circle-down";
	public $LIBERAR		= "fa-unlock";
	public $LISTA		= "fa-th-list";
	public $SALDO		= "saldo";
	public $CERRAR		= "cerrar";
	public $COBROS		= "caja";
	public $CREDITO		= "fa-money";
	public $RECIBO		= "fa-bars";
	public $RESTAR		= "fa-minus";
	public $AHORRO		= "fa-university";
	public $CHECAR		= "fa-check";
	public $EJECUTAR	= "ejecutar";
	public $PASSWORD	= "fa-key";
	public $EDITAR		= "editar";
	public $SALUD		= "fa-user-md";
	public $SUMAR		= "fa-plus";
	public $BANEAR		= "fa-ban";
	public $PARAR		= "fa-stop";
	public $PDF			= "fa-file-pdf-o";
	public $CONTROL		= "fa-wrench";
	public $AVISO		= "warning";
	public $IMPRIMIR	= "imprimir";
	public $IR			= "fa-arrow-circle-right";
	public $AGREGAR		= "fa-plus";
	public $ARCHIVAR	= "fa-download";
	public $ARCHIVOS	= "fa-files-o";
	public $OPERACION	= "fa-calendar-plus-o";
	public $GUARDAR		= "guardar";
	public $PREGUNTAR	= "preguntar";
	public $ATRAS		= "atras";
	public $ACTUAL		= "fa-map-marker";
	public $CALENDARIO	= "fa-calendar";
	public $CALENDARIO1	= "fa-calendar-o";
	public $EXCEL		= "fa-file-excel-o";
	public $VER			= "ver";
	public $HOME		= "fa-home";
	public $ADELANTE	= "siguiente";
	public $BIENES		= "fa-car";
	public $NOTA		= "fa-list-alt";
	public $TAREA		= "tarea";
	public $TELEFONO	= "fa-phone";
	public $CALCULAR	= "fa-calculator";
	public $CAJA		= "fa-inbox";
	public $AUTORIZAR	= "fa-check-square";
	public $FORMATO		= "fa-newspaper-o";
	public $FILTRO		= "fa-filter";
	public $CONTRATO	= "fa-file-word-o";
	public $LEGAL		= "fa-legal";
	public $LLENAR		= "fa-battery-three-quarters";
	public $LLENO		= "fa-battery-full";
	public $ESTADO_CTA	= "fa-line-chart";
	public $GRUPO		= "fa-group";
	public $EMPRESA		= "fa-building";
	public $EMPLEADOR	= "fa-building-o";
	public $RELACIONES	= "fa-user-plus";
	public $RIESGO		= "fa-road";
	public $SALIR		= "fa-power-off";
	public $GENERAR		= "fa-bolt";
	public $VINCULAR	= "fa-plug";
	public $VALIDAR		= "fa-check-circle-o";
	public $EDIFICIO	= "fa-building";
	public $VEHICULO	= "fa-car";
	public $TRUCK		= "fa-truck";
	public $PLANE		= "fa-plane";
	public $BAJA		= "fa-thumbs-o-down";
	public $AUTOMAGIC	= "fa-magic";
	//public $CALCULAR	= "fa-superscript";
	/*	private $mIcons	= array("editar" => "fa-edit",
						"referencias" => "fa-group",
						"bienes" => "fa-car",
						"agregar" => "fa-plus",
						"cancelar" => "fa-times-circle",
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
						"baja" => "fa-minus-circle",
						"info" => "fa-info-circle",
						"minus" => "fa-minus-circle",
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
			);*/
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
			"documento" => "cobro-cargo-documento.frm.php",
	
			"multiple"	=> "cobro-multiple.frm.php",
			"ninguno" => 99
	);
	
	private $mArrOps	= array(
			"efectivo" 				=> 9100,
			"efectivo.egreso" 		=> 9100,
			"cheque.ingreso" 		=> 9100,
			"cheque" 				=> 9200,
			"transferencia" 		=> 9200,
			"transferencia.egreso" 	=> 9200,
	
			"foraneo" 				=> 9100,
			"descuento" 			=> 9201,
			"documento" 			=> 9201, /*XXX: Validar esta opcion*/
			"multiple"				=> 99,
			"ninguno" 				=> 99,
			"0" 					=> 99
	);	
	function __construct(){
		$xCache	= new cCache();
		$idc1	= "tesoreria_tipos_de_pago-fmts";
		$idc2	= "tesoreria_tipos_de_pago-cont";
		$d1		= $xCache->get($idc1);
		$d2		= $xCache->get($idc2);
		if(!is_array($d1)){
			$xQL	= new MQL();
			$rs		= $xQL->getDataRecord("SELECT `tipo_de_pago`,`formato`,`eq_contable` FROM `tesoreria_tipos_de_pago`");
			
			foreach ($rs as $rw){
				$d1[$rw["tipo_de_pago"]]	= $rw["formato"];
				$d2[$rw["tipo_de_pago"]]	= $rw["eq_contable"];
			}
			if(is_array($d1)){
				$this->mArrOpsFrm	= array_merge($this->mArrOpsFrm, $d1);
			}
			if(is_array($d2)){
				$this->mArrOps		= array_merge($this->mArrOps, $d2);
			}
			$d1	= null; $d2	= null;
			$xCache->set($idc1, $this->mArrOpsFrm);
			$xCache->set($idc2, $this->mArrOps);
		} else {
			$this->mArrOpsFrm	= $d1; $d1=null;
			$this->mArrOps		= $d2; $d2=null;
		}
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