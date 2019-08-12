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
include_once("../core/core.operaciones.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.db.inc.php");

include_once("../core/core.contable.inc.php");
include_once("../core/core.contable.utils.inc.php");

ini_set("display_errors", "off");
ini_set("max_execution_time", 900);
    
$key			= parametro("k", true, MQL_BOOL);
$parser			= parametro("s", false, MQL_BOOL);

$messages		= "";
$fechaop		= parametro("f", false, MQL_DATE);
$xF				= new cFecha(0, $fechaop);
$fechaop		= $xF->getFechaISO($fechaop);

$xCierre		= new cCierreDelDia($fechaop);
$EsCerrado		= $xCierre->checkCierre($fechaop, $xCierre->MARCA_CONTABILIDAD);
$forzar			= parametro("forzar", false, MQL_BOOL);

$next			= "./cierre_de_riesgos.frm.php?s=true&k=" . $key . "&f=$fechaop";
$next			= ($forzar == true) ? $next . "&forzar=true" : $next;
if($EsCerrado == true AND $forzar == false){
	setAgregarEvento_("Cierre De Contabilidad Existente", 5);
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
	
		$aliasFil	= $xCierre->getNombreUnico();
		$xLog		= new cFileLog($aliasFil);
		$idrecibo	= DEFAULT_RECIBO;
		
		//$xRec		= new cReciboDeOperacion(12);
		//$xRec->setGenerarPoliza();
		//$xRec->setForceUpdateSaldos();
		//$idrecibo	=  $xRec->setNuevoRecibo(1,1,$fechaop, 1, 12, "CIERRE_DE_SEGUIMIENTO", "NA", "ninguno", "NA", DEFAULT_GRUPO);
		//$xRec->setNumeroDeRecibo($idrecibo);
	
	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		" . EACP_NAME . " \r\n";
	$messages 		.= "=========================		" . getSucursal() . " \r\n";
	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		INICIANDO EL CIERRE DE CONTABILIDAD ===================\r\n";
	$messages 		.= "=========================		RECIBO: $idrecibo				   ====================\r\n";
	if (MODULO_CONTABILIDAD_ACTIVADO == true){
		$xCUtils		= new cUtileriasParaContabilidad();
		$xCUtils->setGenerarPolizasAlCierre($fechaop);
		$messages		.= $xCUtils->getMessages();
		//Si es Fin de annio
		if($xF->getInt($fechaop) == $xF->getInt($xF->getFechaFinAnnio() )){
			//Generar Saldo al del proximo periodo
			$ejercicio	= $xF->anno() + 1;
			$xUCont		= new cUtileriasParaContabilidad();
			$messages	.= $xUCont->setGenerarSaldosDelEjercicio($ejercicio);		
		}
	} else {
		$messages		.= "=========================\tNO ACTIVADO\t====================\r\n";
	}
	
	$xLog->setWrite($messages);
	$xLog->setClose();
	if(ENVIAR_MAIL_LOGS == true){ $xLog->setSendToMail("TR.Eventos del Cierre de Contabilidad"); }
	
		if ($parser == true){
			header("Location: $next");
		}
	
	getEnCierre(false);

}
?>