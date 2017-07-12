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
	$xHP			= new cHPage("TR.REPORTE DE ", HP_REPORT);
	$xL				= new cSQLListas();
	$xF				= new cFecha();
	$query			= new MQL();
	
	$subproducto 	= parametro("subproducto", SYS_TODAS, MQL_INT);
	$producto 		= parametro("producto", SYS_TODAS, MQL_INT);
	$operacion 		= parametro("operacion", SYS_TODAS, MQL_INT);
	
	//$empresa		= parametro("empresa", SYS_TODAS);
	$out 			= parametro("out", SYS_DEFAULT);
	
	$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
	$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
	
	$senders		= getEmails($_REQUEST);
	
	$xHP->init();
	//XXX: Cambiar SQL por uno que facilite la ejecucion
	
	$sql = $xL->getListadoDeCuentasDeCapt(false, false, $producto, $subproducto);
	
//setLog($sql);

	$titulo			= "";
	$archivo		= "reporte-de-captacion-del-$FechaInicial-al-$FechaFinal";
	
	$xRPT			= new cReportes($titulo);
	$xRPT->setFile($archivo);
	$xRPT->setOut($out);
	$xRPT->setSQL($sql);
	$xRPT->setTitle($xHP->getTitle());
	
	//============ Reporte

	
	$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
	$xRPT->setBodyMail($body);
	
	$xRPT->addContent($body);
	
	$xRPT->addCampoSuma("saldo");
	$xRPT->addCampoContar("codigo");
	if(PERSONAS_CONTROLAR_POR_GRUPO == false){
		$xRPT->setOmitir("grupo");
	}
	$xRPT->setProcessSQL();
	$xRPT->setResponse();
	$xRPT->setSenders($senders);
	echo $xRPT->render(true);
	
?>