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
$xHP		= new cHPage("TR.Reporte de Operaciones Bancarias");

//require_once(TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");	
//$jxc ->process();

$xHP->init();

	$xB	= new cPanelDeReportes(iDE_BANCOS, "bancos");
	$xB->setConOperacion(false);
	$xB->setConRecibos(false);
	$xB->setConCajero();
	$xB->setTitle($xHP->getTitle());
	echo $xB->get();
	
	echo $xB->getJs();
	$xHP->fin();
?>