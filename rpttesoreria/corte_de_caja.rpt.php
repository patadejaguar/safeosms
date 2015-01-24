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

//=====================================================================================================
$xHP				= new cHPage("TR.Corte de caja", HP_REPORT);

$xF					= new cFecha();
$xSQL				= new cSQLListas();
//=====================================================================================================
$fecha_inicial 		= (isset($_GET["on"])) ? $_GET["on"] : fechasys();
$fecha_final 		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
$cajero 			= (isset($_GET["f3"])) ? $_GET["f3"] : getUsuarioActual() ;						// Numero de Cajero
$cajero				= parametro("cajero", $cajero, MQL_RAW);

$out				= parametro("out", OUT_HTML, MQL_RAW);
$mails				= getEmails($_REQUEST);

$ByCajero			= "";
$ByAll				= "";

$xCaja				= new cCaja();
$xCaja->initByFechaUsuario($fecha_final, $cajero);

if( isset($_REQUEST["fechaMX"]) ){
	$fecha_inicial		= $xF->getFechaISO($_REQUEST["fechaMX"]);
	$fecha_final		= $xF->getFechaISO($_REQUEST["fechaMX"]);
}


if(count($mails) <= 0){
	if(MODULO_CAJA_ACTIVADO == true){
		if( $xF->getInt($fecha_final) > $xF->getInt(fechasys()) ){ if($xCaja->getEstatus() == TESORERIA_CAJA_ABIERTA){ 	$xHP->goToPageError(70102);	}	}
	}
}

$xUsr				= new cSystemUser($cajero); $xUsr->init();
$nombre				= $xUsr->getNombreCompleto();

$ByDependencia		= ( isset($_GET["dependencia"]) AND $_GET["dependencia"] != SYS_TODAS  ) ? " AND `socios`.`iddependencia`=" . $_GET["dependencia"] : "";

$xRPT				= new cReportes();
$title				= $xHP->getTitle();

$xRPT->setTitle($title);
$xRPT->setOut($out);
$xRPT->setSenders($mails);
$bheader			= $xRPT->getHInicial($xHP->getTitle(), $fecha_inicial, $fecha_final, $nombre);

$xRPT->addContent( $bheader );
$xRPT->setBodyMail($bheader);
$xRPT->setResponse();
$xRPT->addContent($xCaja->getResumenDeCaja() );

//setlog( $xCaja->getMessages() );
if(count($mails) > 0){
	if ( $xCaja->getSumaDeRecibos() <= 0){
		$xRPT->setSenders(array());		//no enviar
	}
}
echo $xRPT->render(true);					//Render Normal

?>