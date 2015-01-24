<?php
include_once ("global.inc.php");
include_once ("global.static.inc.php");

class cFLang {
	private $mCurrLang	= "en";
	function __construct($lang = "en"){
		$this->mCurrLang	= $lang;
	}
	function word($word, $arrParams = array()){
		$T		= array();
		return $word;
	}
	
}

?>