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
$xHP		= new cHPage("TR.RESPALDO DE PERMISOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
$jxc 		= new TinyAjax();
function jsaRespaldo($fecha){
	$xF			= new cFecha();
	$fecha		= $xF->getFechaISO($fecha);
	$xSec		= new cSystemPermissions();
	$file		= $xSec->setBackup($fecha);
	return "Se hizo el respaldo en ". $file;
}
function jsaRestaurar($fecha){
	$xF			= new cFecha();
	$fecha		= $xF->getFechaISO($fecha);
	
	$xSec		= new cSystemPermissions();
	$xSec->setRestore($fecha);
	return 		$xSec->getMessages("html");
}
$jxc ->exportFunction('jsaRespaldo', array('idfechaactual'), "#idmsgs" );
$jxc ->exportFunction('jsaRestaurar', array('idfechaactual'), "#idmsgs" );
$jxc ->process();

$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->OButton("TR.RESPALDAR", "jsaRespaldo()", $xFRM->ic()->GUARDAR, "idhacerresp", "yellow");
$xFRM->OButton("TR.RESTAURAR", "jsaRestaurar()", $xFRM->ic()->DESCARGAR, "idhacerrest", "red");

$xFRM->addFecha();

$xFRM->addCerrar();

$xFRM->addAviso("");

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();


?>