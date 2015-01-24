<?php
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================

include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$persona 		= (isset($_GET["persona"])) ?  $_GET["persona"] : DEFAULT_SOCIO;
$tipo			= (isset($_GET["tipo"])) ? $_GET["tipo"] : false;

$data	= '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';
$sucess	= false;
header("Content-Type: image/png");
if($persona != DEFAULT_SOCIO AND $tipo !== false){
	$xSoc	= new cSocio($persona);
	$img	= $xSoc->getDocumentoGuardado($tipo);
	if($img === SYS_CERO){ $sucess = false;  } else { echo $img;  }
}

if($sucess == false){
	
	$data 	= base64_decode($data);
	$im 	= imagecreatefromstring($data);
	if ($im !== false) {
		//header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
}
?>