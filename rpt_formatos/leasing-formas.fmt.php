<?php
/**
 * Archivo de Pro-formas de originacion de Leasing
 * @since 31/03/2008
 * @author Balam Gonzalez Luis Humberto
 */
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
$xHP		= new cHPage("", HP_RECIBO);



$formato	= parametro("forma", 400, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$clave		= parametro("id", 0, MQL_INT); $clave = parametro("clave", $clave, MQL_INT); $clave = parametro("arrendamiento", $clave, MQL_INT);

$xForma						= new cFormato($formato);
$xForma->setCredito($credito);

$xForma->setProcesarVars();



$xHP->setTitle($xForma->getTitulo());
$xHP->init();


echo $xForma->get();


$xHP->fin();
?>