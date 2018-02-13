<?php
//header("Content-type: text/plain");
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
//=====================================================================================================
$xHP			= new cHPage("TR.CARTA BIENVENIDA", HP_REPORT);

$idsolicitud 	= parametro("i", DEFAULT_CREDITO, MQL_INT); $idsolicitud = parametro("credito", $idsolicitud, MQL_INT); $idsolicitud = parametro("solicitud", $idsolicitud, MQL_INT);
$formato		= parametro("forma", 1902, MQL_INT);
$senders		= getEmails($_REQUEST);
$out 			= parametro("out", SYS_DEFAULT); $out = strtolower($out);

//$xHP->setNoDefaultCSS();
$xHP->addCSS("../css/contrato.css.php");


	
	
	$xFecha				= new cFecha();
	$xCred = new cCredito($idsolicitud);
	$xCred->initCredito();

	$DCred				= $xCred->getDatosDeCredito();
	$numero_de_socio	= $xCred->getClaveDePersona();

	$xForma				= new cFormato($formato);
	
	$xForma->setOut($out);
	$xForma->setCredito($idsolicitud, $DCred);
	$xForma->setProcesarVars();

	$xForma->render($xHP->init("", true), $xHP->fin(true));
	
	/*echo $xHP->init();
	echo $xForma->get();
	$xHP->fin();*/
?>