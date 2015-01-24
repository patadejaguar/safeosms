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
$xHP		= new cHPage("REPORTE DE ", HP_REPORT);
$mql		= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();

ini_set("max_execution_time", 600);

$xHP->setTitle($xHP->lang("catalogo de", "riesgo") );



$estatus 		= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 		= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$empresa		= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

//$FechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
//$FechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();

$FechaInicial	= (isset($_GET["on"])) ?  $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
$FechaFinal		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();

$xBuro			= new cReporteBuroDeCredito_tipo(DEFAULT_CREDITO, $FechaFinal);

$sql 			= $mql->getQueryInicialDeCreditos("", "", " 	AND (`creditos_solicitud`.`fecha_ministracion` <= '" . $FechaFinal . "' )
				AND
				(`creditos_solicitud`.`numero_socio` !=" . DEFAULT_SOCIO . ")
				AND
				(`creditos_solicitud`.`monto_autorizado` > " . TOLERANCIA_SALDOS .  ")
				AND	
				(`creditos_solicitud`.`estatus_actual` !=" . CREDITO_ESTADO_AUTORIZADO . " AND `creditos_solicitud`.`estatus_actual` !=" . CREDITO_ESTADO_SOLICITADO .")  
				/*LIMIT 0,100*/ ");
//cargar datos del pago
//exit($sql);

$rsPagos		= $query->getDataRecord( "SELECT * FROM `creditos_abonos_parciales` WHERE	(`creditos_abonos_parciales`.`fecha_de_pago` <='$FechaFinal')");
$DPagos			= array();
foreach ($rsPagos as $dpags ){
	$credito	= $dpags["docto_afectado"];
	$DPagos[$credito][]	= $dpags;
	//var_dump($dpags);
}

if($out == OUT_CSV){
	header("Content-type: text/x-csv");
	//header("Content-type: text/csv");
	//header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=reporte_buro_de_credito-" . date("Ymd") . ".csv");
} else {
	if($out == OUT_EXCEL){
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=Buro_de_Credito.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$cTbl = new cTabla($sql);
		$cTbl->setTipoSalida(OUT_EXCEL);
		
		$excel	= "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
		$excel	.= "\r\n<head>\r\n";
		$excel	.= "<!--[if gte mso 9]>\r\n";
		$excel	.= "<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Buro de Credito</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>";
		$excel	.= "\r\n<![endif]-->\r\n";
		$excel	.= "</head>\r\n";
		$excel	.= "<body>\r\n";
		echo $excel;		
	} else {
		echo $xHP->getHeader();
		echo $xHP->setBodyinit();
	}
	echo "<table>";
}
//" AND (`creditos_solicitud`.`saldo_actual` >= " . TOLERANCIA_SALDOS . ") "

//echo $sql;
$datos	= $query->getDataRecord($sql);

echo $xBuro->getColumnas($out);
$xBuro->setOut($out);
foreach ($datos as $rows){
	$credito		= $rows["numero_solicitud"];
	//echo $credito;
	//$xBuro			= new cReporteBuroDeCredito_tipo($credito);
	$lpagos				= (isset($DPagos[$credito])) ? $DPagos[$credito] : false;
	$xBuro->init($rows, $credito, $lpagos);
	
	echo $xBuro->getLinea($out);
}
//setLog( $xBuro->getMessages() );
//echo $xBuro->getMessages();

if($out == OUT_CSV){
	
} else {
	echo "</table>";
	if($out == OUT_EXCEL){
		echo "\r\n</body>\r\n</html>\r\n";
	} else {
		echo $xHP->setBodyEnd();
		$xHP->end();
	}
} 
?>