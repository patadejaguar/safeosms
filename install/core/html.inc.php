<?php
include_once ("global.inc.php");
include_once ("global.static.inc.php");
include_once ("lang.inc.php");

class cFInit{
	public $Obj	= null;
	
	function __construct($title = "", $id = "", $tipo = HP_FORM) {
		$this->Obj		= null;
		$xLng			= new cFLang();
		$title			= $xLng->word($title);
		
		//include_once ("");
		//include_once ("");
		//include_once ("");
		switch ($tipo){
			case HP_FORM:
				$this->Obj	= new cFForms($id);
				$this->Obj->setTitle($title);
				$this->Obj->setTipo($tipo);
				break;
		}
		return $this->Obj;
	}
}

class cFObject {
	protected $mID			= "";
	protected $mName		= "";
	protected $mEvents		= array();
	protected $mExo			= "";
	protected $mTitle		= "";
	protected $mOut			= OUT_HTML;
	
	protected $mInitTag		= "";
	protected $mEndTag		= "";
	protected $mTipoHTML	= HP_FORM;
	protected $mClass		= "";
	protected $mDataRole	= "";
	protected $mSql			= "";
		
	function setID($id){ 
		$this->mID		= $id;
		$this->mName	= $id;
	}
	function setTitle($title){ $this->mTitle	= $title; }
	function setTipo($tipo){ $this->mTipoHTML	= $tipo; }
	function prop($valor, $nombre){
		$cnt		= ($valor == "") ? "" : " $nombre=\"$valor\"";
		return $cnt;
	}
	function trad($word){
		$xLng		= new cFLang();
		return $xLng->word($word);		
	}
	function addEvent($function, $event, $args = null){
		if($args == null){
			$this->mEvents[$event]	= "$function()";
		} else {
			$this->mEvents[$event]	= "$function($args)";
		}
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
class cFPage extends cFObject {
	private $mHead	= "";
	
	function __construct($title = "", $tipo = HP_FORM){
		switch ($tipo){
			case HP_FORM:
				//$this->addCSS("jquery.mobile.css");
				//$this->addCSS("font-awesome.min.css");
				$this->addCSS("metro-bootstrap.min.css");
				$this->addCSS("metro-bootstrap-responsive.min.css");
				$this->addCSS("iconFont.min.css");
				//$this->addCSS("jquery.qtip.css");
				//$this->addCSS("general.css");
				$this->addCSS("jtable/metro/blue/jtable.min.css");
				
				$this->addJS("jquery/jquery.new.js");
				$this->addJS("jquery/jquery.widget.min.js");
				//$this->addJS("jquery/jquery.mobile.js");
				
				$this->addJS("jquery/jquery.mousewheel.js");
				$this->addJS("jquery/metro.min.js");
				//$this->addJS("jquery/jquery.qtip.min.js");
				
				$this->addJS("numeral-min.js");
				$this->addJS("jquery/jquery.jpanelmenu.min.js");
				$this->addJS("jtable/jquery.jtable.min.js");
				break;
		}
	}
	function getHeader(){
		$html	= "<!DOCTYPE html><html><head><meta charset=\"utf-8\" />
								<meta name=\"viewport\" content=\"initial-scale=1.0, maximum-scale=1.0, user-scalable=no\">
						        <title>" . $this->mTitle . "</title>" . $this->mHead . "</head><body class=\"metro\">\n";
		return $html;	
	}
	function getFooter(){
		return "</body></html>";
	}
	function addJS($file = ""){	$this->mHead	.= "<script src=\"../js/" . $file . "\"></script>"; }
	function addCSS($file = ""){ $this->mHead	.= "<link href=\"../css/$file\" rel=\"stylesheet\">"; }
	function render(){
		
	}
} 
class cFForms extends cFObject {
	private $mMethod	= "POST";
	private $mAction	= "";
	private $mBody		= "";
	private $mIncludeH	= false;
	private $mCInit		= "";
	private $mCFin		= "";
	private $mJS		= "";
	private $mMenu		= "";
	function __construct($id, $action = ""){
		$this->mID		= $id;
		$this->mAction	= $action;
		$this->mInitTag	= "<form class=\"example\"";
		$this->mEndTag	= "</form>";		
	}
	function setAction($action){ $this->mAction = $action; }
	function setHeaders(){
		$xP		= new cFPage($this->mTitle, $this->mTipoHTML);
		$this->mCInit	= $xP->getHeader();
		$this->mCFin	= $xP->getFooter();
	}
	function render(){
		$txt	= $this->mCInit;
		$txt	.= $this->mInitTag;
		$txt	.= $this->prop($this->mID, "id");
		$txt	.= $this->prop($this->mAction, "action");
		$txt	.= $this->prop($this->mMethod, "method");
		$txt	.= ">";
		$txt	.= "<fieldset>";
		$txt	.= ($this->mTitle == "") ? "" : "<legend>" . $this->mTitle . "</legend>";
		$txt	.= ($this->mMenu == "") ? "" : "<div class=\"toolbar\">" . $this->mMenu . "</div>";
		$txt	.= $this->mBody;
		$txt	.= "<script>$(\"#" . $this->mID . "\").css(\"content\", \"EJEP\" ); function init" . $this->mID . "(){  }</script>";
		$txt	.= "</fieldset>";
		
		$txt	.= $this->mEndTag;
		
		$txt	.= $this->mCFin;
		return $txt;
	}
	function text($id, $titulo = "", $valor = ""){
		$xO	= new cFText();
		$this->addContent($xO->getText($id, $titulo, $valor));
	}
	function number($id, $titulo = "", $valor = ""){
		$xO	= new cFText();
		$this->addContent($xO->getNumero($id, $titulo, $valor));		
	}
	function button($titulo, $onclick, $icon = "" ){
		$xBtn		= new cFButton();
		$this->mMenu .= $xBtn->getBShorcut($titulo, $onclick, $icon);
	}
	function addContent($html){ $this->mBody	.= $html; }
}

class cFInput extends cFObject {

	protected $mType	= "";
	
	function __construct(){
		$this->mInitTag	= "<input";
		$this->mEndTag	= "/>";
	}
	function onClick($function, $args = null){
		$this->addEvent($function, "onclick", $args);
	}
	function getLabel($title){
		$title = $this->trad($title);
		if(trim($title) == ""){
			$title	= "";
		} else {
			$title	= "<label for=\"" . $this->mID . "\">$title</label>";  
		}
		return $title;
	}
	function render($valor, $tipo = "text"){
		$this->mType	= $tipo;
		
		$txt	= $this->mInitTag	. $this->prop($tipo, "type");
		$txt	.= $this->prop($valor, "value");
		$txt	.= $this->prop($this->mID, "id");
		$txt	.= $this->prop($this->mID, "name");
		$txt	.= $this->mEndTag;
		$titulo	= $this->getLabel($this->mTitle);
		$xDiv	= new cFDiv("div-" . $this->mID, "input-control text", "input-control");
		$xDiv2	= new cFDiv("divs-" . $this->mID, "");
		$btn	= $this->getMinBtn();
		//$txt	= $titulo . $txt;
		return 	$xDiv2->render( $titulo . $xDiv->render($txt . $btn) );
	}
	function getMinBtn(){
		$btn		= "";
		switch ($this->mType){
			case "text":
				$btn	=  '<button type="button" class="btn-clear" tabindex="-1"></button>';
				break;
				case "number":
					$btn	=  '<button type="button" class="btn-clear" tabindex="-1"></button>';
					break;				
		}
		return $btn;
	}
}

class cFText extends cFInput {
	function getNumero($id, $titulo, $valor){
		$this->mID		= $id;
		$this->mTitle	= $titulo;
		return 	$this->render($valor, "number");
	}
	function getText($id, $titulo, $valor){
		$this->mID		= $id;
		$this->mTitle	= $titulo;
		return 	$this->render($valor, "text");				
	}
}
class cFTextArea extends cFInput  {
	
}
class cFButton extends cFInput {
	private $mIcon		= "";
	function getIcon($icon = ""){
		return "<i class=\"icon-$icon on-left\"></i>";
	}
	function getBShorcut($titulo, $onclick = "", $icon = ""){
		$icon	= $this->getIcon($icon);
		return "<button class=\"large primary\" onclick=\"$onclick\">$icon$titulo</button>";
	}
}
class cFDiv extends cFObject {
	function __construct($id = "", $class = "tx4", $data_role = "" ){
		$this->mInitTag	= "<div";
		$this->mEndTag	= "</div>";
		$this->mID		= $id;
		$this->mClass	= $class;
		$this->mDataRole	= $data_role;
	}
	function render($content = ""){
		$txt	= $this->mInitTag	. $this->prop($this->mID, "id");
		$txt	.= $this->prop($this->mClass, "class");
		$txt	.= $this->prop($this->mDataRole, "data-role");
		$txt	.= ">";
		$txt	.= $content;
		
		$txt	.= $this->mEndTag;
		return $txt;
	}
}

class cFReports extends cFObject {
	
}

class cFTabs extends  cFObject {
	
}

class cFJTable  extends cFObject {
	
	function __construct($sql){
		$this->mSql	= $sql;
	}
	function render(){
		
	}
	function getClient(){
		//$D	= explode("FROM", $this->mSQL);
		//$fields		= "";
	}
	function getService(){
		
	}
}

?>