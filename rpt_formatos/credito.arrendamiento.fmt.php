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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP			= new cHPage("TR.LEASING", HP_RECIBO);

$idsolicitud 	= parametro("i", DEFAULT_CREDITO, MQL_INT); $idsolicitud = parametro("credito", $idsolicitud, MQL_INT); $idsolicitud = parametro("solicitud", $idsolicitud, MQL_INT);

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);

$formato		= parametro("forma", 1906, MQL_INT);
$senders		= getEmails($_REQUEST);
$out 			= parametro("out", SYS_DEFAULT); $out = strtolower($out);

$idtramite		= parametro("tramite",0, MQL_INT);
$idvehiculo		= parametro("vehiculo", 0, MQL_INT); $idvehiculo		= parametro("idvehiculo", $idvehiculo, MQL_INT);

//$xHP->setNoDefaultCSS();
$xHP->addCSS("../css/contrato.css.php");


	$xFecha				= new cFecha();
	

	$xFMT				= new cFormato($formato);
	$xFMT->init();
	$xFMT->setOut($out);


	
	if($idtramite > 0){ //Carta Poder
		$xCatT			= new cLeasingTramitesCatalogo($idtramite);
		if($xCatT->init() == true){
			$tramite	= $xCatT->getNombre();
			$xFMT->addVars("var_leasing_tramite_clave", $tramite);
		}
	}
	
	if($idsolicitud > DEFAULT_CREDITO){
		$xCred = new cCredito($idsolicitud);
		$xCred->init();
		
		$DCred				= $xCred->getDatosDeCredito();
		$numero_de_socio	= $xCred->getClaveDePersona();
		//setLog("$idsolicitud $numero_de_socio");
		$xFMT->setPersona($numero_de_socio);
		$xFMT->setCredito($idsolicitud);
		
	} else {
		$xFMT->setOriginacionLeasing($clave);
		
	}
	if($idvehiculo > 0){
		$xFMT->setVehiculoLeasing($idvehiculo);
	}
	$xFMT->setProcesarVars();
	
	$xHP->setTitle($xFMT->getTitulo());
	
	
	$xFMT->render($xHP->init("", true), $xHP->fin(true));
	
	
?>