<?php
include_once '../core/core.config.inc.php';
$cmd		= isset($_REQUEST["cmd"]) ? $_REQUEST["cmd"] : false;
$fecha		= isset($_REQUEST["f"]) ? $_REQUEST["f"] : date("Y-m-d");
$url		= "";
$rs			= "";
//http://192.168.1.210/frmutils/cierres.task.php?f=2014-06-16&cmd=1
switch ($cmd){
	case 1:
		$rs	= sendURLQuery(SAFE_HOST_URL . "frmutils/cierre_de_colocacion.frm.php?f=$fecha", TASK_USR, TASK_PWD);
		 break;
	case 2:
		$rs	= sendURLQuery(SAFE_HOST_URL . "frmutils/cierre_de_captacion.frm.php?f=$fecha", TASK_USR, TASK_PWD);
		break;
	case 3:
		$rs	= sendURLQuery(SAFE_HOST_URL . "frmutils/cierre_de_contabilidad.frm.php?f=$fecha", TASK_USR, TASK_PWD);
		break;
	case 4:
		$rs	= sendURLQuery(SAFE_HOST_URL . "frmutils/cierre_de_seguimiento.frm.php?f=$fecha", TASK_USR, TASK_PWD);
		break;
	case 5:
		$rs	= sendURLQuery(SAFE_HOST_URL . "frmutils/cierre_de_riesgos.frm.php?f=$fecha", TASK_USR, TASK_PWD);
		break;
	case 6:
		$rs	= sendURLQuery(SAFE_HOST_URL . "frmutils/cierre_de_sistema.frm.php?f=$fecha", TASK_USR, TASK_PWD);
		break;		
}
echo $rs;//sendURLQuery("http://192.168.1.210/frmutils/cierre_de_captacion.frm.php?ctx=3435d6ae2e0ebaaac8444dc6cbd39d13&f=" . date("Y-m-d"), "100", "remoteuserabcdefghijk");

function sendURLQuery($url, $user, $pwd){
	$user	= md5($user); //iduser
	$pwd	= md5($pwd);
	$pwd	= md5($pwd);
	$ctx	= md5("$user|$pwd");
	
	$ch 	= curl_init();
	$url	= "$url&ctx=$ctx";//SAFE_HOST_URL . "/clslogin.php". "?o=$o";
	set_time_limit(0);// to infinity for example
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,1000);
		$rs = curl_exec($ch); 
		curl_close ($ch);

	return $rs;
}
?>