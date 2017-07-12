<?php
/**
 * Reporte de Creditos a final de plazo que se dieron un día en concluso. por ejemplo 10 de cada mes
 * Sirve para informar
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
$xHP		= new cHPage("TR.REPORTE DE CREDITOS MINISTRADOS UN DIA COMUN", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); //$FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false);// $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= $xL->getListadoDeCreditos(false, false, false, false, " AND (DATE_FORMAT(fecha_ministracion, '%d')=DATE_FORMAT('$FechaFinal', '%d')) ");
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($xHP->getTitle());
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
$xRPT->addContent( $body );

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
$xRPT->setTitle($xHP->getTitle());




$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>