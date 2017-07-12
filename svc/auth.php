<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
//<=====	FIN_H
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
$xInit->cors();
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$data		= parametro("data", "", MQL_RAW);
$ctx		= parametro("ctx", "", MQL_RAW);
$rs			= array();
$rs["ctx"]	= "";
$xSvc		= new MQLService("", "");
$data		= $xSvc->getDecryptData($data);
$mObj		= json_decode($data, true);
//setLog("PASS " . $mObj["password"]);
//setLog("USER " . $mObj["usuario"]);
$pass				= $mObj["password"];
$user				= $mObj["usuario"];

$nuevo				= array();
$nuevo["ctx"]		= "";
$nuevo["message"]	= "";
$nuevo["uuid"]		= "";
$xUsr				= new cSystemUser($user, false);
if($xUsr->init() == true){
	if($xUsr->getComparePassword($pass) == true){
		$nuevo["ctx"]		= $xUsr->getCTX();
		$nuevo["uuid"]		= $xUsr->getNivel();
		$nuevo["id"]		= $xUsr->getID();
	}
	$nuevo["message"] .= $xUsr->getMessages();
} else {
	$nuevo["message"] .= $xUsr->getMessages();
}

$rs["data"]		= $xSvc->getEncryptData(json_encode($nuevo));

//setLog("data $data --- " . $xSvc->getDecryptData($data)); 
header('Content-type: application/json');
echo json_encode($rs);
?>