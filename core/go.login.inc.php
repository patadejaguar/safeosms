<?php
if(!isset($_SESSION)){ @session_start(); }
include_once("core.config.inc.php");
include_once("core.error.inc.php");
include_once("core.security.inc.php");
include_once("core.db.inc.php");
include_once("core.db.dic.php");
include_once("core.init.inc.php");

@include_once ("../libs/aes.php");

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
/**
 * @deprecated @since 2018.01.01
 * 
 * */
function getStatusConnected($iduser) {
$stat	= true;

	if( isset($iduser) ){
	 		$loguser 	= USR_LOGIN;
			$logpwd 	= PWD_LOGIN;
			$logdb 		= MY_DB_IN;
			$loghost 	= WORK_HOST;
			$mCnx 		= new mysqli( WORK_HOST , USR_LOGIN, PWD_LOGIN, MY_DB_IN, PORT_HOST);
			if(!$mCnx){
				return true;
			} else {
				$iduser	= md5($iduser);
				$fecha	= date("Y-m-d");
				$sqllog = "SELECT count(webid) AS 'connected'
				FROM usuarios_web_connected
				WHERE webid='$iduser'
				AND
				option1='$fecha' ";
				$mCnx->set_charset("utf8");
				
				$rs		= $mCnx->query($sqllog);
				if(!$rs){
					return true;
				} else {
					$row 		= $rs->fetch_assoc();
					if(isset($row["connected"])){
						$val	= $row["connected"];
						if($val > 0){
							return true;
						} else {
							return false;
						}
					} else {
						return true;
					}
					$rs->free();
				}
			}

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
	if($xPer->getEsPublico($mFile) == true){
		$permiso	= true;		
	} else {
		if(isset($_REQUEST)){
			
			if(isset($_REQUEST["ctx"])){
				$init 		= $xUsr->initByCTX($_REQUEST["ctx"]);
				if($init === false){
					$xLog->add("No existe el Usuario por Contexto\r\n");
					$salir	= true;
				}
			}
		}
		if($xUsr->init() == false){
			$xLog->add("El Usuario Actual es Invalido para acceder al Archivo $mFile\r\n");
			$salir	= true;
	
		} else {
			$nivel	= $xUsr->getNivel();
		}
		$permiso	= $xPer->getAccessFile($mFile, $nivel);
		if($xPer->getEsPublico() == true AND $salir == true ){
			$salir	= false;
		}
		if($permiso == false){
			$usr	= $xUsr->getAlias();
			$xLog->add("El Usuario $usr No tiene permisos para el Archivo $mFile\r\n");
		}
		$xLog->add($xPer->getMessages(), $xLog->DEVELOPER);
		if($salir !== false){
			$xLog->guardar($xLog->OCat()->ERROR_LOGIN);
			$xUsr->setEndSession(true);
		}
	}
	return $permiso;
}
?>