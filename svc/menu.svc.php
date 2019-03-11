<?php
/**
 * Modulo
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_SERVICE);
//$xQL		= new MQL();
//$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  


$xSVC		= new MQLService($action, "");

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";

header('Content-type: application/json');

$xMen			= new cHMenu();
$xMen->setID("navigator");
$menu			= "";

$xMen->setIsMobile($_SESSION[SYS_CLIENT_MOB]);

if($clave > 0){
	//$btn			= "";
	/*if($_SESSION[SYS_CLIENT_MOB] == true){
		$menu 		.= "<li><a onclick='var xG = new Gen(); xG.home();'><i class='fa fa-home fa-lg'></i>Inicio</a></li>";
	}*/
	
	$menu			= $xMen->getItems($clave);
	$rs["error"]	= false;
	$rs["menu"]		= base64_encode($menu);
}

echo json_encode($rs);
?>