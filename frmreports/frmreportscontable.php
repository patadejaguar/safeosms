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
$xHP		= new cHPage("TR.Reportes DE CONTABILIDAD", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();


$xHP->init();

$xRPT		= new cPanelDeReportes(iDE_CONTABLE, "contable_general");
$xRPT->setTitle($xHP->getTitle());

$xRPT->setConCajero(false);
$xRPT->setConOperacion(false);
$xRPT->setConRecibos(false);

echo $xRPT->get();

echo $xRPT->getJs(TRUE);
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>