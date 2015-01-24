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
    
$key		 		= (isset($_GET["k"]) ) ? true : false;
$parser				= (!isset($_GET["s"]) ) ? false : $_GET["s"];
    
    //Obtiene la llave del
//if ($key == MY_KEY) {
	$messages		= "";
	$fechaop		= parametro("f", fechasys());

/**
 * Generar el Archivo HTMl del LOG
 * eventos-del-cierre + fecha_de_cierre + .html
 *
 */

	$aliasFil	= getSucursal() . "-eventos-al-cierre-de-contabilidad-del-dia-$fechaop";

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
} else {
	$messages		.= "=========================\tNO ACTIVADO\t====================\r\n";
}

//TODO: Si es Anual generar Saldos al Cierre


$xLog->setWrite($messages);
$xLog->setClose();
if(ENVIAR_MAIL_LOGS == true){ $xLog->setSendToMail("TR.Eventos del Cierre de Contabilidad"); }
	if ($parser != false){
		//TODO: Agregar cierre de riesgos 
		header("Location: ./cierre_de_riesgos.frm.php?s=true&k=" . $key . "&f=$fechaop");
	}
	
//}

?>
