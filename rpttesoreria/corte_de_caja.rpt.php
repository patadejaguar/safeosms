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

$cajero 			= parametro("f3", getUsuarioActual(), MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);
$out				= parametro("out", OUT_HTML, MQL_RAW);
$mails				= getEmails($_REQUEST);
$empresa			= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$FechaInicial		= parametro("fechaMX", $xF->getFechaMinimaOperativa(), MQL_DATE);
$FechaInicial		= parametro("on", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal			= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$TipoDePago			= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW);
$ByAll				= "";



$ByEmpresa			= $xSQL->OFiltro()->RecibosPorPersonaAsociada( $empresa);
$ByCajero			= $xSQL->OFiltro()->RecibosPorCajero($cajero);
//if(MODO_DEBUG == true){	$ByCajero		= ""; }
$ByFecha			= $xSQL->OFiltro()->RecibosPorFecha($FechaInicial, $FechaFinal);
$ByTipoDePago		= $xSQL->OFiltro()->RecibosPorTipoDePago($TipoDePago);

$titulo				= $xHP->getTitle();

if ( $ByEmpresa ){
	$xEmp			= new cEmpresas($empresa); $xEmp->init();
	$titulo			= $titulo . " / " . $xEmp->getNombreCorto();
}
if($ByCajero != ""){
	$xCaj			= new cSystemUser($cajero);
	if($xCaj->init() == true){
		$titulo			= $titulo . " / " . $xCaj->getNombreCompleto();
	}
}
if($ByTipoDePago != ""){
	$xTipoP			= new cTesoreriaTiposDePagoCobro($TipoDePago);
	if($xTipoP->init() == true){
		$titulo			= $titulo . " / " . $xTipoP->getNombre();
	}
}

$xCaja				= new cCaja();
$xCaja->initByFechaUsuario($FechaFinal, $cajero);



if(count($mails) <= 0){
	if(MODULO_CAJA_ACTIVADO == true){
		if( $xF->getInt($FechaFinal) > $xF->getInt(fechasys()) ){ if($xCaja->getEstatus() == TESORERIA_CAJA_ABIERTA){ 	$xHP->goToPageError(70102);	}	}
	}
}

$xUsr				= new cSystemUser($cajero); $xUsr->init();
$nombre				= $xUsr->getNombreCompleto();

$xRPT				= new cReportes();


$xRPT->setTitle($titulo);
$xRPT->setOut($out);
$xRPT->setSenders($mails);
$bheader			= $xRPT->getHInicial($titulo, $FechaInicial, $FechaFinal, $nombre);

$xRPT->addContent( $bheader );
$xRPT->setBodyMail($bheader);
$xRPT->setResponse();
$xRPT->addContent($xCaja->getResumenDeCaja() );

//setlog( $xCaja->getMessages() );
//No enviar si no hay operaciones
if(count($mails) > 0){
	if ( $xCaja->getSumaDeRecibos() <= 0){
		$xRPT->setSenders(array());		//no enviar
	}
}
echo $xRPT->render(true);					//Render Normal

?>