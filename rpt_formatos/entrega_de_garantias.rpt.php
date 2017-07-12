<?php
/**
 * Forma de Entrega de garantias
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 * @subpackage formatos
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
$xHP		= new cHPage("TR.Acuse de Entrega", HP_REPORT);
	
$xF			= new cFecha();
$clave 		= parametro("clave", 0, MQL_INT);// (isset($_GET["clave"]) ) ? $_GET["clave"] : SYS_NINGUNO;

$xHP->init();


$xFMT		= new cFormato(152);
$xGar		= new cCreditosGarantias($clave);
if( $xGar->init() == true){
	$xFMT->setPersona( $xGar->getClaveDePersona() );
	$xFMT->setCredito( $xGar->getClaveDeCredito() );
	$xFMT->setGarantiaDeCredito($clave, "1", $xGar->getDatosInArray());
	$xFMT->setProcesarVars();
	
	$xFMT->setToImprimir();
	
}
echo $xFMT->get();

$xHP->fin(); 
?>