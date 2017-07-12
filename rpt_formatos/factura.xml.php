<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP		= new cHPage("TR.Factura Electronica ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);

$idrecibo 		= parametro("cNumeroRecibo", 0, MQL_INT); $idrecibo 	= parametro("recibo", $idrecibo, MQL_INT); $idrecibo 	= parametro("idrecibo", $idrecibo, MQL_INT); $idrecibo 	= parametro("clave", $idrecibo, MQL_INT);


$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$sql			= "SELECT * FROM `operaciones_archivo_de_facturas` WHERE `clave_de_recibo` = ";
$titulo			= "";
$archivo		= "";

$xRec			= new cReciboDeOperacion(false, false, $idrecibo);
$xRec->init();
$archivo		= $xRec->getFactura(true);
if( $archivo == null){
	header("Content-type: text/plain");
	echo $xRec->getMessages(OUT_TXT);
} else {
	$nombrearchivo		= "Factura_recibo_num_" . $xRec->getCodigoDeRecibo();
	header("Content-type: application/pdf");
	//ISO-8859-1
	header("Content-Disposition: attachment; filename=\"$nombrearchivo.pdf\"; ");
	//readfile($xRec->OFactura()->getComprobante() . ".pdf");
	echo $archivo;
}
?>