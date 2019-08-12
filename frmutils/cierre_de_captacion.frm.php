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
include_once("../core/core.captacion.utils.inc.php");

include_once("../core/core.riesgo.inc.php");
include_once("../core/core.seguimiento.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.operaciones.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.captacion.inc.php");
    
ini_set("display_errors", "off");
ini_set("max_execution_time", 1600);

$key			= parametro("k", true, MQL_BOOL);
$parser			= parametro("s", false, MQL_BOOL);

//Obtiene la llave del
//if ($key == MY_KEY) {
$messages			= "";
$fechaop		= parametro("f", false, MQL_DATE);
$xF				= new cFecha(0, $fechaop);
$fechaop		= $xF->getFechaISO($fechaop);

$xCierre		= new cCierreDelDia($fechaop);
$EsCerrado		= $xCierre->checkCierre($fechaop, $xCierre->MARCA_CAPTACION);
$forzar			= parametro("forzar", false, MQL_BOOL);

$next			= "./cierre_de_seguimiento.frm.php?s=true&k=" . $key . "&f=$fechaop";
$next			= ($forzar == true) ? $next . "&forzar=true" : $next;
if($EsCerrado == true AND $forzar == false){
	setAgregarEvento_("Cierre De Captacion Existente", 5);
	if ($parser == true){
		header("Location: $next");
	}
} else {
	
	getEnCierre(true);
	//INICIAR
	if(MODULO_CAPTACION_ACTIVADO == true){
		$aliasFil			= $xCierre->getNombreUnico();
	
		$xLog				= new cFileLog($aliasFil, true);
	
		$idrecibo			= DEFAULT_RECIBO;
	
		$xRec				= new cReciboDeOperacion(12, false);
		//$xRec->setGenerarPoliza();
		$xRec->setForceUpdateSaldos();
		$idrecibo			=  $xRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CUENTA_CORRIENTE, $fechaop, 1, RECIBOS_TIPO_CIERRE, $xCierre->getTextoUnico(), "", TESORERIA_COBRO_NINGUNO, "", DEFAULT_GRUPO);
		$xRec->setNumeroDeRecibo($idrecibo);
	
		$messages 			.= "=======================================================================================\r\n";
		$messages 			.= "=========================		" . EACP_NAME . " \r\n";
		$messages 			.= "=========================		" . getSucursal() . " \r\n";
		$messages 			.= "=======================================================================================\r\n";
		$messages 			.= "=========================		INICIANDO EL CIERRE DE CAPTACION   ====================\r\n";
		$messages 			.= "=========================		RECIBO: $idrecibo				   ====================\r\n";
		$messages 			.= "=======================================================================================\r\n";
		$xUCapt					= new cUtileriasParaCaptacion();
		
		$messages 			.= "=========================		Actualizando Saldos de Captacion 	====================\r\n";
		
		$messages			.= $xUCapt->setActualizarSaldos(CAPTACION_TIPO_PLAZO);
		$messages			.= $xUCapt->setActualizarSaldos(CAPTACION_TIPO_VISTA);
		
		$messages 			.= "=========================		Purgando Dias Minimo de Inversion 	====================\r\n";
		
		$messages			.= $xUCapt->vencer_intereses_de_inversion($idrecibo, $fechaop);
		$messages			.= $xUCapt->inversiones_automaticas($idrecibo, $fechaop);
	
	
	
		if ( $xF->getDiaFinal()  == $xF->get()  ){
			$messages		.= $xUCapt->setGenerarInteresSobreSDPM($idrecibo, $fechaop);
			
			if(CAPTACION_IMPUESTOS_A_DEPOSITOS_ACTIVO == true){
				$messages		.= $xUCapt->getGenerarBaseGravadaMensualIDE($fechaop);
				$messages		.= $xUCapt->setGenerarIDE_mensual($idrecibo, $fechaop);
			}
		}
	
		$xRec->setFinalizarRecibo(true);
	
		$xLog->setWrite($messages);
		$xLog->setClose();
		if(ENVIAR_MAIL_LOGS == true){ $xLog->setSendToMail("TR.Eventos del Cierre del Captacion"); }
	}
	if ($parser == true){
		header("Location: $next");
	}
	getEnCierre(false);

}

?>