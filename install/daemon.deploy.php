<?php
if(isset($_REQUEST["key"])){
	
	$key	= $_REQUEST["key"];
	$OS 	= strtolower(substr(PHP_OS, 0, 3));
	$mpath	= realpath(dirname("../../"));
	//ini_set('include_path', ini_get('include_path') .':' . $mpath . '/reports:' . $mpath . '/libs:' . $mpath . '/core'); 
	//include_once ( $mpath . '/core/core.config.inc.php');
	//include_once ( $mpath . '/core/core.db.inc.php');
	
	$out	= $mpath . "/core/core.config.os." . $OS . ".inc.php";
	$fp 	= fopen ($out, 'w+');
	
	/*if(SAFE_ON_DEV == true){
		$xPatch = new cSystemPatch();
		$xPatch->patch(true);
		$xPatch->setActualizarToLocalhost(fechasys(), 0);
	}*/
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.dropbox.com/s/$key/core.config.os." . $OS . ".inc.php?dl=1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:65.0) Gecko/20100101 Firefox/65.0');
	
	//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	//curl_setopt($ch, CURLOPT_USERPWD, "user:pass");
	
	
	
	$results = curl_exec($ch);
	if(curl_exec($ch) === false)
	{
		unlink($out);
		echo 'Curl error: ' . curl_error($ch);
	}	
}
?>