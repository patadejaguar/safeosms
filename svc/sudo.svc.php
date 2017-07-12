<?php
/**
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
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$enable		= parametro("enable", false, MQL_BOOL);
$perfil		= parametro("perfil", 0, MQL_INT);
$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";


$xP	= new cSystemPermissions($clave);
$xP->init();

if($enable == true){
	$xP->setAgregarPermiso($perfil);
	$rs["error"]	= false;
} else {
	$xP->setEliminarPermiso($perfil);
	$rs["error"]	= false;
}
$rs["message"]	= $xP->getMessages();

header('Content-type: application/json');
echo json_encode($rs);
?>