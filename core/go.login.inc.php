<?php
if(!isset($_SESSION)){ @session_start(); }
include_once("core.config.inc.php");
include_once("core.error.inc.php");
include_once("core.security.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");

function goLogged($camposolicitado, $username) {
	$TID 	= session_id();
	$xUsr	= new cSystemUser($username, false);
	$D		= null;
	if( (isset($camposolicitado) ) AND (isset($username))){
			$ql			= new MQL();
			if($xUsr->init() == false){
				$xUsr->setEndSession(true);
			} else {
				if($xUsr->getEsBaja() == true){
					$xUsr->setEndSession(true);
				} else {
					$Datos 	= $xUsr->getDatosInArray();
					if(!isset($Datos[$camposolicitado])){
						$xUsr->setEndSession(true);
					} else {
						$D	= $Datos[$camposolicitado];
					}
				}
			}
	} else {
		$xUsr->setEndSession(true);
	}
	return $D;
}

function getStatusConnected($iduser) {
$stat	= true;

	if( isset($iduser) ){
	 		$loguser 	= USR_LOGIN;
			$logpwd 	= PWD_LOGIN;
			$logdb 		= MY_DB_IN;
			$loghost 	= WORK_HOST;

			$logcnn = mysql_connect($loghost, $loguser, $logpwd);
				if (!$logcnn) {
						$stat = true;
					}

			$logdbo = mysql_select_db($logdb, $logcnn);

			$iduser	= md5($iduser);
			$fecha	= date("Y-m-d");
			$sqllog = "SELECT count(webid) AS 'connected'
						FROM usuarios_web_connected
						WHERE webid='$iduser'
						AND
						option1='$fecha' ";
			$rslog = mysql_query($sqllog, $logcnn);
				if (!isset($rslog)) {
					$stat = false;
				} else {
					$counts = mysql_result($rslog, 0, "connected");

					if ( !isset($counts) or $counts == 0 ){
						$stat = false;
					} else {
						$stat = true;
					}
				}

			@mysql_free_result($rslog);
			@mysql_close($logcnn);
			unset($logcnn);
			unset($logdbo);
			unset($rslog);

	} else {
		$stat = true;
	}

return $stat;
}

function getSIPAKALPermissions($mFile){
	$xUsr	= new cSystemUser(getUsuarioActual());
	$xLog	= new cCoreLog();
	$xPer	= new cSystemPermissions();
	$salir	= false;
	$nivel	= 0;
	if(isset($_REQUEST)){
		
		if(isset($_REQUEST["ctx"])){
			$init = $xUsr->initByCTX($_REQUEST["ctx"]);
			if($init === false){
				$xLog->add("No existe el Usuario por Contexto\r\n");
				$salir	= true;
			}
		}
	}
	if($xUsr->init() == false){
		$xLog->add("El Usuario Actual es Invalido \r\n");
		$salir	= true;

	} else {
		$nivel	= $xUsr->getNivel();
	}
	$permiso	= $xPer->getAccessFile($mFile, $nivel);
	if($xPer->getEsPublico() == true AND $salir == true ){
		$salir	= false;
	}
	$xLog->add($xPer->getMessages(), $xLog->DEVELOPER);
	if($salir !== false){
		$xLog->guardar($xLog->OCat()->ERROR_LOGIN);
		$xUsr->setEndSession(true);
	}
	return $permiso;
}
?>