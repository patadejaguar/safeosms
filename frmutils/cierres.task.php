<?php
include_once('../core/core.config.inc.php');
include_once('../core/core.db.inc.php');
include_once('../core/core.security.inc.php');

$cmd		= isset($_REQUEST["cmd"]) ? $_REQUEST["cmd"] : false;
$fecha		= isset($_REQUEST["f"]) ? $_REQUEST["f"] : date("Y-m-d");
$url		= "";
$rs			= "";
//http://192.168.1.210/frmutils/cierres.task.php?f=2014-06-16&cmd=1
$host		= getSafeHost();

switch ($cmd){
	case 1:
		$rs	= sendURLQuery($host . "frmutils/cierre_de_colocacion.frm.php?f=$fecha");
		 break;
	case 2:
		$rs	= sendURLQuery($host. "frmutils/cierre_de_captacion.frm.php?f=$fecha");
		break;
	case 3:
		$rs	= sendURLQuery($host. "frmutils/cierre_de_contabilidad.frm.php?f=$fecha");
		break;
	case 4:
		$rs	= sendURLQuery($host. "frmutils/cierre_de_seguimiento.frm.php?f=$fecha");
		break;
	case 5:
		$rs	= sendURLQuery($host. "frmutils/cierre_de_riesgos.frm.php?f=$fecha");
		break;
	case 6:
		$rs	= sendURLQuery($host. "frmutils/cierre_de_sistema.frm.php?f=$fecha");
		break;
	default:
		//echo $host;
		break;
}
echo $rs;
//sendURLQuery("http://192.168.1.210/frmutils/cierre_de_captacion.frm.php?ctx=3435d6ae2e0ebaaac8444dc6cbd39d13&f=" . date("Y-m-d"), "100", "remoteuserabcdefghijk");

function sendURLQuery($url){
	$rs		= false;
	//@session_start();
	
	$xUsr	= new cSystemUser(TASK_USR, false);
	$key	= getClaveCifradoTemporal();
	$xQL	= new MQLService("", "");
	$xQL->setKey($key);
	$pwd	= $xQL->getEncryptData(TASK_PWD);
	
	if($xUsr->initSession(TASK_USR, $pwd) == true){
		
		$ctx	= $xUsr->getCTX();
		
		$ch 	= curl_init();
		$url	= "$url&ctx=$ctx";//SAFE_HOST_URL . "/clslogin.php". "?o=$o";
		
		set_time_limit(0);// to infinity for example
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,1000);
		$rs 	= curl_exec($ch); 
		curl_close ($ch);
		
	}
	return $rs;
}
function url_exists($url) {
	$url	= $url . "/inicio.php";
	
	//check if URL is valid
	if(!filter_var($url, FILTER_VALIDATE_URL)){
		return false;
	}
	
	$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_VERBOSE, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
	//curl_setopt($ch,CURLOPT_SSLVERSION, 3);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
	//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
	
	$page=curl_exec($ch);
	//echo curl_error($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpcode >= 200 && $httpcode < 300)
		return true;
		else
			return false;
}
function getSafeHost(){
	$host		= SAFE_HOST_URL;
	if(url_exists($host) == false){
		$subh		= (strpos($host, "https") === false) ? substr($host, 0, 7) : substr($host, 0, 8);
		$host		= $subh . $_SERVER["SERVER_ADDR"] . "/";
		if(url_exists($host) == false){
			$host	= $subh . "127.0.0.1/";
		}
	}
	return $host;
}
function get_real_ip()
{
	
	if (isset($_SERVER["HTTP_CLIENT_IP"]))
	{
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
	{
		return $_SERVER["HTTP_X_FORWARDED"];
	}
	elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
	{
		return $_SERVER["HTTP_FORWARDED_FOR"];
	}
	elseif (isset($_SERVER["HTTP_FORWARDED"]))
	{
		return $_SERVER["HTTP_FORWARDED"];
	}
	else
	{
		return $_SERVER["REMOTE_ADDR"];
	}
}
?>