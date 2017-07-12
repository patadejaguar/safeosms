<?php
/**
 * Forma de Recibo de Credito
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 * @subpackage formatos
 */
//=====================================================================================================
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");
include_once("../core/core.db.inc.php");
$theFile			= __FILE__;
$permiso			= getSIPAKALPermissions($theFile);
if($permiso === false){	header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.Acuse de Entrega", HP_RECIBO);
$xF			= new cFecha();
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

$xHP->init();

$xFMT		= new cFormato(200);
if($credito > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		if($recibo <= 0){ $recibo = $xCred->getNumeroReciboDeMinistracion(); }
	}
}
if($recibo > 0){
	if($credito <= DEFAULT_CREDITO){
		$xRec	= new cReciboDeOperacion(false, false, $recibo);
		if($xRec->init() == true){
			$credito = $xRec->getCodigoDeDocumento();
		}
	}
}
if($credito > DEFAULT_CREDITO){
	$xFMT->setCredito($credito);
}
if($recibo > 0){
	$xFMT->setRecibo($recibo);
}
$xFMT->setToImprimir();

$xFMT->setProcesarVars();
echo $xFMT->get();
$xHP->fin();

?>