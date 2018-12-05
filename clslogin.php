<?php
if(!isset($_SESSION)){ @session_start(); }

include_once("core/go.login.inc.php");
include_once("core/core.error.inc.php");
include_once("core/core.init.inc.php");
include_once("libs/aes.php");
include_once("core/core.db.inc.php");
$mKey		= getClaveCifradoTemporal();

$usuario	= parametro("u$mKey", "", MQL_RAW);
$password	= parametro("p$mKey", "", MQL_RAW);
$sucursal	= parametro("idsucursal", DEFAULT_SUCURSAL, MQL_RAW);
$indice		= parametro("o",0, MQL_INT);


//$xLog					= new cCoreLog();
$_SESSION["sucursal"]	= strtolower($sucursal);
$xUsr					= new cSystemUser();



if($xUsr->initSession($usuario, $password) === true){
	$arrFiles	= array(
			1 => "./frmutils/cierre_de_colocacion.frm.php?k=" . MY_KEY,
			2 => "./frmutils/cierre_de_captacion.frm.php?k=" . MY_KEY,
			3 => "./frmutils/cierre_de_seguimiento.frm.php?k=" . MY_KEY,
			4 => "./frmutils/cierre_de_contabilidad.frm.php?k=" . MY_KEY,
			5 => "./frmutils/cierre_de_sistema.frm.php?k=" . MY_KEY
	);
	//Asignar Sucursal
	if($xUsr->init() == true){
		if(MULTISUCURSAL == true){
			if($xUsr->getEsCorporativo() == false){
				if(strtolower($xUsr->getSucursal()) == $sucursal){
					
				} else {
				//if($xUsr->getSucursalAccede() == false){
					$xLog	= new cCoreLog();
					$xLog->add("Sucursal Incorrecta para el Usuario $usuario\r\n");
					$xLog->guardar($xLog->OCat()->ERROR_LOGIN);
					$msg	= "Sucursal Incorrecta";
					$xUsr->setEndSession(true, true, $msg);
				}
			}
		}
	}
	
	
	
	
	//
	$index	= $xUsr->getIndexPage();
	if($usuario == TASK_USR){
		if(isset($arrFiles[$indice])){
			$index	= $arrFiles[$indice];
		}
	}
	header ("location:$index");

} else {
	$xLog	= new cCoreLog();
	$xLog->add("Datos Incorrectos para el Usuario $usuario\r\n");
	$xLog->guardar($xLog->OCat()->ERROR_LOGIN);
	$msg	= "Credenciales incorrectas para iniciar sesion. " . $xUsr->getMessages();
	
	$xUsr->setEndSession(true, true, $msg);
}
?>
