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
$iptask		= get_real_ip();

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
		setAgregarEvento_("Comando Desconocido desde la IP $iptask", 5);
		break;
}
echo $rs;
//sendURLQuery("http://192.168.1.210/frmutils/cierre_de_captacion.frm.php?ctx=3435d6ae2e0ebaaac8444dc6cbd39d13&f=" . date("Y-m-d"), "100", "remoteuserabcdefghijk");

function sendURLQuery($url){
	$rs		= false;
	//@session_start();
	$iptask	= get_real_ip();
	if($iptask == TASK_IP){
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
	} else {
		setAgregarEvento_("Error al Iniciar el Cierre desde la IP $iptask", 5);
	}
	return $rs;
}

?>