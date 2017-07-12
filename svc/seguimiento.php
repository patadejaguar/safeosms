<?php
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
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
//$xInit->cors();

$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$xVc		= new MQLService("", "");
$info		= array();

//$tabla		= parametro("tabla", false, MQL_RAW);
//$clave		= parametro("id", 	false, MQL_RAW);
$cmd		= parametro("cmd", 	false, MQL_RAW);
$oficial	= parametro("oficial", 0, MQL_INT);
$oficial	= setNoMenorQueCero($oficial);
$data		= parametro("data", "", MQL_RAW);
if($oficial <= 0){
	$oficial	= getUsuarioActual();
	$oficial	= setNoMenorQueCero($oficial);
}
$cmd		= $xVc->getDecryptData($cmd);
$data		= $xVc->getDecryptData($data);
$mObj		= json_decode($data, true);
if(isset($mObj["oficial"])){
	$oficial	= setNoMenorQueCero($mObj["oficial"]);
}
$rs			= array();
$sql		= "";
switch($cmd){
	case "clientes":
	$sql	= "SELECT
	`creditos_solicitud`.`numero_socio` AS `codigo`,
	`personas`.`nombre`,
	`personas`.`alias_dependencia`      AS `empresa`,
	`creditos_solicitud`.`oficial_seguimiento` 
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `personas` `personas` 
		ON `creditos_solicitud`.`numero_socio` = `personas`.`codigo` 
WHERE
	(`creditos_solicitud`.`oficial_seguimiento` =$oficial) LIMIT 0,10";
		break;
	case "cartera":
		
		break;
}
header('Content-type: application/json');
$xSVC			= new MQLService("", $sql);
$info["data"]	= $xSVC->getEncryptData($xSVC->getJSON($xSVC->XPLAIN));
echo json_encode($info);
//setLog(json_encode($rs));
//setLog($sql);
?>