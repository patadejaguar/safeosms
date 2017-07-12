<?php
@include_once("../core/core.config.inc.php");
//require_once("../libs/wiky.inc.php");
//include_once("../libs/wiki/MediawikiApi.php");
//include_once("../libs/wiki/ApiUser.php");
//include_once("../libs/Wikimate.php");
// Load all the stuff
//require_once( __DIR__ . '/vendor/autoload.php' );

// Log in to a wiki
class cWikipedia {
	private $mURL	= "";
	private $mAuth	= "";
	private $mUSR	= "";
	private $mPWD	= "";
	private $mCook	= "";
	function __construct(){
		$this->mURL	= SVC_HOST_CONSULTA_WIKI . "w/api.php?";
		$this->mUSR	= SVC_USER_CONSULTA_WIKI;
		$this->mPWD	= SVC_PWD_CONSULTA_WIKI;
		$this->mCook= PATH_TMP . "/cookie.tmp";
	}
	function buscar($texto){
		$content	= "";
		$txt		= "";
		$datos		= null;
		if($this->setLogin() ){
			$texto	= urlencode($texto);
			$url 	= $this->mURL . "action=query&format=json";
			$params	= "action=query&list=search&srwhat=nearmatch&format=json&continue=&iwurl=&srsearch=$texto";
			$content= $this->requestHTTP($url, $params);
			//echo $content;
			//setLog("$content");
			if($content != ""){
				$content	= json_decode($content, true);
				
				//var_dump( $content["query"] );
				if(isset($content["query"])){
					$datos= $content["query"];
					
					//$squery	= $content["query"];
					//$datos	= $squery["search"];
					//var_dump($info);
					/*if(is_array($info)){
						foreach($info as $rs){
							$title		= $rs["title"];
							$snip		= $rs["snippet"];
							if(strpos($snip, "#REDIR") !== false){
								$snip	= str_replace("#REDIRECCIÓN", "", $snip);
								$DSnip	= explode("[[", $snip);
								$snip	= $DSnip[1];
								$snip	= str_replace("]]", "", $snip);
								$title	= $snip;
							}
							
							$pparams	= "prop=extracts&exintro=&explaintext=&titles=" . urlencode($title);
							$QTxt		= $this->requestHTTP($url, $pparams);
							//setLog($QTxt);
							$DQtxt		= json_decode($QTxt, true);
							if(isset($DQtxt["query"])){
								$DPages	= $DQtxt["query"];
								$pages	= $DPages["pages"];
								foreach ($pages as $prs => $cnt){
									if(isset($cnt["extract"])){
										$txt	= $cnt["extract"];
									}
								}
							}
							$QTxt		= null;
							$DQtxt		= null;
						}
					}*/
					//setLog($content);
					//
				} else {
					$datos	= null;
				}
			} 
		}
		//$api = new MediawikiApi( SVC_HOST_CONSULTA_WIKI . '/w/api.php' );
		//$api->login( new ApiUser( SVC_USER_CONSULTA_WIKI, SVC_PWD_CONSULTA_WIKI ) );
		// Make a POST request
		//$api->postRequest( new SimpleRequest( 'purge', array( 'titles' => 'FooBar' ) ) );
		// Make a GET request
		//$queryResponse = $api->getRequest( new SimpleRequest( 'query', array( 'meta' => 'siteinfo' ) ) );
		// Make a bad request and catch the error
		//try{
		//    $api->postRequest( new SimpleRequest( 'FooBarBaz' ) );
		//}
		//catch ( UsageException $e ) {
		//    echo "Oh no the api returned an error!";
		//}
		//Logout
		//$api->logout();
		//return $queryResponse;
		return $datos;
	}
	function requestHTTP($url, $post="") {

		$ch 		= curl_init();
		//Change the user agent below suitably
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
		curl_setopt($ch, CURLOPT_URL, ($url));
		curl_setopt( $ch, CURLOPT_ENCODING, "UTF-8" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->mCook);
		curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->mCook);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		
		if (!empty($post)) curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
		//UNCOMMENT TO DEBUG TO output.tmp
		//curl_setopt($ch, CURLOPT_VERBOSE, true); // Display communication with server
		//$fp = fopen("output.tmp", "w");
		//curl_setopt($ch, CURLOPT_STDERR, $fp); // Display communication with server
		//setLog($post);
		$content = curl_exec($ch);
	//setLog($url . $post);
		if (!$content) {
			throw new Exception("Error getting data from server ($url): " . curl_error($ch));
		}
	
		curl_close($ch);
	
		return $content;
	}

	function setLogin ($token='') {
		$user	= $this->mUSR;
		$pass	= $this->mPWD;	
		$url 	= $this->mURL . "action=login&format=xml";
	
		$params = "action=login&lgname=$user&lgpassword=$pass";
		if (!empty($token)) { $params .= "&lgtoken=$token"; }
	
		$data 	= $this->requestHTTP($url, $params);// httpRequest($url, $params);
	
		if (empty($data)) {
			throw new Exception("No data received from server. Check that API is enabled.");
		}
		$data 	= preg_replace('/<warnings[^>]*>.*?<\/warnings>/i', '', $data);
		
		$xml 	= simplexml_load_string($data);
		
		if($xml){
			if (!empty($token)) {
				//Check for successful login
				$expr = "/api/login[@result='Success']";
				$result = $xml->xpath($expr);
		
				if(!count($result)) {
					throw new Exception("Login failed");
				}
			} else {
				$expr = "/api/login[@token]";
				$result = $xml->xpath($expr);
		
				if(!count($result)) {
					throw new Exception("Login token not found in XML");
				}
			}
		
			return $result[0]->attributes()->token;
		} else {
			return false;
		}
	}
	function getLink($snippet){
		$snippet	= $this->cleanSnipet($snippet);
		$snippet		= SVC_HOST_CONSULTA_WIKI . "wiki/" . $snippet;
		return $snippet;
	}
	function cleanSnipet($snippet){
		//$wiky=new wiky;
		$snippet	= str_replace("#REDIRECT", "", $snippet);
		$snippet	= str_replace("#REDIRECCI\u00d3N", "", $snippet);
		$snippet	= str_replace("#REDIRECCIÓN", "", $snippet);
		$snippet	= str_replace("[[", "", $snippet);
		$snippet	= str_replace("]]", "", $snippet);
		$snippet	= trim($snippet);
		$snippet	= mb_convert_encoding($snippet, "UTF-8", "auto");
		$snippet	= str_replace(" ","_", $snippet);
		$snippet	= urlencode($snippet);
		return $snippet;		
	}
	function esBusqueda($item){
		$valido		= false;
		if(is_array($item)){
			if(isset($item["search"])){
				$finds	= $item["search"];
				if(is_array($finds)){
					foreach ($finds as $icx){
						$valido	= true;
					}
				}
			}
		}
		return $valido;
	}
	function getPage($texto){
		///http://en.wikipedia.org/w/api.php?format=json&action=query&titles=Jes%C3%BAs_Murillo_Karam&prop=revisions&rvprop=content&continue=
		$html		= "";
		$content	= "";
		if($this->setLogin() ){
			$texto	=  urlencode($texto);////$this->cleanSnipet($texto);
			$url 	= $this->mURL . "action=query&format=json";
			$params	= "format=json&action=query&titles=$texto&prop=revisions&rvprop=content&continue=";
			$content= $this->requestHTTP($url, $params);
			//setLog($params);
			if($content != ""){
				$content	= json_decode($content, true);
				$content	= (isset($content["query"])) ? $content["query"] : array();
				if(isset($content["pages"])){
					$content	= $content["pages"];
					//setLog($content["pages"]);
					$html		= "";
					//$content	= $content["query"];
					foreach ($content as $cnt ){
						
						if(isset($cnt["revisions"])){
							$revs	= $cnt["revisions"][0];
							//setLog($revs["*"]);
							if(isset($revs["*"])){
								if($html == ""){
									//$wiky	= new wiky;
									// Call for the function parse() on the variable You created and pass some unparsed text to it, it will return parsed HTML or false if the content was empty. In this example we are loading the file input.wiki, escaping all html characters with htmlspecialchars, running parse and echoing the output
									$input	= $revs["*"];
									//$input 	= htmlspecialchars($input);
									$html	= $this->parseBio($input);// $wiky->parse($input);
									//setLog($html);
								}							
							}
						}
					}
				} else {
					$content	= null;
				}
			}
		}
		//$api = new MediawikiApi( SVC_HOST_CONSULTA_WIKI . '/w/api.php' );
		//$api->login( new ApiUser( SVC_USER_CONSULTA_WIKI, SVC_PWD_CONSULTA_WIKI ) );
		// Make a POST request
		//$api->postRequest( new SimpleRequest( 'purge', array( 'titles' => 'FooBar' ) ) );
		// Make a GET request
		//$queryResponse = $api->getRequest( new SimpleRequest( 'query', array( 'meta' => 'siteinfo' ) ) );
		// Make a bad request and catch the error
		//try{
		//    $api->postRequest( new SimpleRequest( 'FooBarBaz' ) );
			//}
			//catch ( UsageException $e ) {
			//    echo "Oh no the api returned an error!";
			//}
			//Logout
			//$api->logout();
			//return $queryResponse;
			return $html;		
	}
	function parseBio($text){
		$initPOS	= stripos($text, "\n}}\n");
		$endPOS		= stripos($text, "\n\n==");
		$large		= $endPOS  - $initPOS;
		$str		= substr($text, ($initPOS+4), ($large-4));
		//$wiky		= new wiky();
		
		
		preg_match_all("/<ref(.*?)<\/ref>/", $str, $marray2);
		if(isset($marray2[0])){
			$refs		= $marray2[0];
			foreach ($refs as $err){
				$str	= str_replace($err, "", $str);
			}
		}

		$str 		= htmlspecialchars($str);
		// containing floating point numbers
		preg_match_all("/\[\[(.*?)\]\]/", $str, $marray);
		$coin		= (isset($marray[0]) ) ? $marray[0] : array();
		foreach ($coin as $arr){
			$text		= $arr;
			//setLog($arr);
			if(strpos($text, "|") !== false){
				$text	= explode("|", $text);
				$text	= isset($text[1]) ? $text[1] : ""; 
				//setLog($text);
			}
			$str		= str_replace($arr, $text, $str);
		}
		$str			= str_replace(array("[", "]", "'" ), "", $str);
		$str			= strip_tags($str);
		
		$xT				= new cTipos();
		$str			= $xT->setNoAcentos($str);
		//$str		= $wiky->parse($str);
		//setLog($str);
		return $str;
	}
}
?>