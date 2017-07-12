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
$xHP		= new cHPage("TR.Reporte de Alertas", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xAl		= new cAml_alerts();
$xlistas	= new cSQLListas();
	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);
$extenso		= parametro("ext", false, MQL_BOOL);
$conchecking	= parametro("condictamen", false, MQL_BOOL);
$consistema		= parametro("consistema", false, MQL_BOOL);
$tiporiesgo		= parametro("tipoderiesgo", false, MQL_INT);
$clasificacion	= parametro("clasificacion", 0, MQL_INT);


$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
//
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$CamposExtras	= "";
if($conchecking	== true){
	$CamposExtras	= ", `mensaje`, getFechaMXByInt(`fecha_de_checking`) AS `fecha_de_dictamen` ,`notas_de_checking` AS `dictamen`, getBooleanMX( `estado_en_sistema` ) AS `estatusactivo` ";
}
$ByClase			= $xlistas->OFiltro()->AMLRiesgosPorTipo($clasificacion);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);


//Descartados

$sql		= $xlistas->getListadoDeAlertas($tiporiesgo,$FechaInicial, $FechaFinal, false, $ByClase, $CamposExtras);


$xRPT->setSQL($sql);
$xRPT->setProcessSQL();

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>