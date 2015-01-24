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
	$strOrder 	= $_GET["o"];
	$NewOrder 	= $_GET["x"];
	$cmdOrder 	= array();
	$form		= "";
	$subpath	= "";
	
	//
	if ( isset($strOrder) ){
		$cmdOrder	= explode(STD_LITERAL_DIVISOR, $strOrder);
	} else {
		$cmdOrder	= explode(STD_LITERAL_DIVISOR, $newOrder);
	}

?>