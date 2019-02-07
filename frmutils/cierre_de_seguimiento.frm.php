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
include_once("../core/core.seguimiento.utils.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.operaciones.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.db.inc.php");


ini_set("display_errors", "off");
ini_set("max_execution_time", 900);
ini_set("memory_limit", SAFE_MEMORY_LIMIT);
$key			= parametro("k", true, MQL_BOOL);
$parser			= parametro("s", false, MQL_BOOL);

$messages		= "";
$fechaop		= parametro("f", false, MQL_DATE);
$xF				= new cFecha(0, $fechaop);
$fechaop		= $xF->getFechaISO($fechaop);

$xCierre		= new cCierreDelDia($fechaop);
$EsCerrado		= $xCierre->checkCierre($fechaop);
$forzar			= parametro("forzar", false, MQL_BOOL);

$next			= "./cierre_de_contabilidad.frm.php?s=true&k=" . $key . "&f=$fechaop";
$next			= ($forzar == true) ? $next . "&forzar=true" : $next;
if($EsCerrado == true AND $forzar == false){
	setAgregarEvento_("Cierre De Seguimiento Existente", 5);
	if ($parser == true){
		header("Location: $next");
	}
} else {
	
getEnCierre(true);

	/**
	 * Generar el Archivo HTMl del LOG
	 * eventos-del-cierre + fecha_de_cierre + .html
	 *
	 */
	if(MODULO_SEGUIMIENTO_ACTIVADO == true){
		$aliasFil		= getSucursal() . "-eventos-al-cierre-de-seguimiento-del-dia-$fechaop";
	
		$xLog			= new cFileLog($aliasFil);
	
		$idrecibo		= DEFAULT_RECIBO;
	
	
		$messages 		.= "=======================================================================================\r\n";
		$messages 		.= "=========================		" . EACP_NAME . " \r\n";
		$messages 		.= "=========================		" . getSucursal() . " \r\n";
		$messages 		.= "=======================================================================================\r\n";
		$messages 		.= "=========================		INICIANDO EL CIERRE DE SEGUIMIENTO ====================\r\n";
		$messages 		.= "=========================		RECIBO: $idrecibo				   ====================\r\n";
		$messages 		.= "=========================		FECHA: $fechaop					   ====================\r\n";
		$messages 		.= "=======================================================================================\r\n";
		
		
		//Avisos
		$dia_siguiente	= sumardias($fechaop, 1);
		$xAv			= new cAlertasDelSistema($dia_siguiente);
		$messages		.= $xAv->setGenerarAlCierre($dia_siguiente);
		
		
		
		$messages		.= vencer_notificaciones();
		$messages		.= vencer_llamadas();
		$messages		.= vencer_compromisos();
		
		
		$xLlam			= new cUtileriasParaSeguimiento();
		$xLlam->setCancelarLlamadasAnteriores($fechaop);
		if(SEGUIMIENTO_GENERAR_AL_CIERRE == true){
			$messages	.= setLlamadasDiariasCreditosNo360($fechaop, $idrecibo);
			$messages	.= setLlamadasDiariasPorMora($fechaop, $idrecibo);
			
			//
			
			$xUtils		= new cUtileriasParaSeguimiento();
			
			if(SEGUIMIENTO_NOTIF_CBZA_PREV == true){
				$xUtils->setGenNotificacionCobroPrev();
			}
		}
	
		$xLog->setWrite($messages);
		$xLog->setClose();
		
		if(ENVIAR_MAIL_LOGS == true){ 
			$xLog->setSendToMail("TR.Eventos del Cierre del Seguimiento"); 
		}
	}
	if ($parser != false){
		header("Location: $next"); flush();
	}
	getEnCierre(false);
	
}
?>
