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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();


$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$ByFecha		= $xFil->OperacionesPorFecha($FechaInicial, $FechaFinal);
$ByTipo			= $xFil->OperacionesPorTipo($operacion);
$ByPersona		= $xFil->OperacionesPorPersona($persona);
$ByDocto		= $xFil->OperacionesPorDocumento($credito);

$sql			= "SELECT operaciones_mvtos.socio_afectado AS 'numero_de_socio',
			CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto)
			AS 'nombre_completo',
			operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion', operaciones_mvtos.fecha_afectacion AS 'fecha',
			operaciones_mvtos.docto_afectado AS 'documento', operaciones_mvtos.afectacion_real AS 'monto', 
			operaciones_mvtos.saldo_actual AS 'saldo', operaciones_mvtos.detalles AS 'observaciones', 
			operaciones_mvtos.tasa_asociada, operaciones_mvtos.dias_asociados FROM socios_general, operaciones_mvtos, operaciones_tipos
			WHERE operaciones_mvtos.socio_afectado=socios_general.codigo AND 
			operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion
			$ByDocto $ByPersona $ByFecha $ByTipo
				ORDER BY operaciones_mvtos.fecha_operacion";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
exit;
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
include_once("../reports/PHPReportMaker.php");


$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 	= $_GET["on"];
$fecha_final 	= $_GET["off"];
$f3 			= $_GET["f3"];
$input 			= $_GET["out"];
$f50 			= $_GET["f50"];



//SQL Extras
$f_x_soc = "";
	if (!$input) {
		$input = "default";
	}
	if($f50){
		$f_x_soc = " AND operaciones_mvtos.socio_afectado=$f50 ";
	}



	$setSql = " SELECT operaciones_mvtos.socio_afectado AS 'numero_de_socio',
			CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto)
			AS 'nombre_completo',
			operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion', operaciones_mvtos.fecha_afectacion AS 'fecha',
			operaciones_mvtos.docto_afectado AS 'documento', operaciones_mvtos.afectacion_real AS 'monto', 
			operaciones_mvtos.saldo_actual AS 'saldo', operaciones_mvtos.detalles AS 'observaciones', 
			operaciones_mvtos.tasa_asociada, operaciones_mvtos.dias_asociados FROM socios_general, operaciones_mvtos, operaciones_tipos
			WHERE operaciones_mvtos.socio_afectado=socios_general.codigo AND 
			operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion
			AND operaciones_mvtos.tipo_operacion=$f3 
			AND operaciones_mvtos.fecha_afectacion>='$fecha_inicial' AND operaciones_mvtos.fecha_afectacion<='$fecha_final' 
			$f_x_soc
				ORDER BY operaciones_mvtos.fecha_operacion";

	//echo $setSql; exit;

if ($input!=OUT_EXCEL) {

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report21.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($setSql, "", "fieldnames");
}
?>
