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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.captacion.inc.php");
include_once("../core/core.riesgo.inc.php");
include_once("../core/core.seguimiento.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.creditos.utils.inc.php");
include_once("../core/core.operaciones.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

    ini_set("display_errors", "off");
    ini_set("max_execution_time", 1600);
    
    
    $key		 	= (isset($_GET["k"]) ) ? true : false;
    $parser			= (!isset($_GET["s"]) ) ? false : $_GET["s"];
    
    //Obtiene la llave del
//if ($key == MY_KEY) {
	$messages		= "";
	$fechaop		= parametro("f", fechasys());
	$xF				= new cFecha(0, $fechaop);
	/**
	 * Generar el Archivo HTMl del LOG
	 * eventos-del-cierre + fecha_de_cierre + .html
	 *
	 */

	$aliasFil	= getSucursal() . "-eventos-al-cierre-de-colocacion-del-dia-$fechaop";

	$xLog		= new cFileLog($aliasFil);
	$ql			= new MQL();

	$xRec		= new cReciboDeOperacion(12);
	$xRec->setGenerarPoliza();
	$xRec->setForceUpdateSaldos();
	$idrecibo	= $xRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,$fechaop, 1, 12, "CIERRE_DE_COLOCACION_$fechaop", DEFAULT_CHEQUE, DEFAULT_TIPO_PAGO, 
										DEFAULT_RECIBO_FISCAL, DEFAULT_GRUPO);

	$xRec->setNumeroDeRecibo($idrecibo);


	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		" . EACP_NAME . " \r\n";
	$messages 		.= "=========================		" . getSucursal() . " \r\n";
	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		INICIANDO EL CIERRE DE COLOCACION ====================\r\n";
	$messages 		.= "=========================		RECIBO: $idrecibo				   ====================\r\n";

	//reconstruir db de pagos
	$ql->setRawQuery("CALL `proc_historial_de_pagos` ");
	$xCUtils		= new cUtileriasParaCreditos();

	$messages		.= $xCUtils->setEstatusDeCreditos($idrecibo, $fechaop, false, true );

	if ( date("Y-m-t", strtotime($fechaop) ) == date("Y-m-d", strtotime($fechaop) )  ){
		$messages		.= $xCUtils->setGenerarMvtoFinDeMes( $xF->getDiaInicial(), $xF->getDiaFinal() );
	}
	if(CREDITO_CIERRE_FORZAR_DEVENGADOS == true){
		$messages		.= $xCUtils->setReestructurarSDPM_Planes(true, false, false, false, $xF->getDiaInicial(), false);
		$messages		.= $xCUtils->setReestructurarSDPM(true, false, false, false, false, $xF->getDiaInicial(), false);
		$messages		.= $xCUtils->setRegenerarInteresDevengado(false, $xF->get(), $xF->get() );
	}
	
	$messages		.= $xCUtils->setAcumularIntereses();
	
	$xRec->setFinalizarRecibo(true);

	$xLog->setWrite($messages);
	$xLog->setClose();
	if(ENVIAR_MAIL_LOGS == true){ $xLog->setSendToMail("TR.Eventos del Cierre del colocacion");	}
	if ($parser != false){
		header("Location: ./cierre_de_captacion.frm.php?s=true&k=" . $key . "&f=$fechaop");
	}
//}

?>
