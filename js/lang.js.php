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
include_once("../core/core.deprecated.inc.php");
include_once("../core/entidad.datos.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.lang.inc.php");

//header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
header("Content-type:text/javascript");
$xCache		= new cCache();
$idx		= "idx-lang-gen";
$lng		= $xCache->get($idx);
if($lng === null){
	$xLng	= new cLang();
	$arr	= $xLng->getWords();
	//mensajes
	$xQL	= new MQL();
	//$sql	= "";
	$rs		= $xQL->getRecordset("SELECT * FROM `sistema_mensajes` ");
	while($rw = $rs->fetch_assoc()){
		$arr[$rw["topico"]] = $rw["mensaje"];
	}
	$lng	= "var jsonWords = " . json_encode( $arr ) . ";";
	
	$xCache->set($idx, $lng, $xCache->EXPIRA_UNDIA);
}
echo $lng;
?>
