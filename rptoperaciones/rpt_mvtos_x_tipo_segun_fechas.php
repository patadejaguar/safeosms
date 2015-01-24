<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @since 2007-01-02
 *  Cambios en la Version
 * 		- 2008/04/16 Agregar Paramatro de sucursal
 * 		  2008/04/16 Cambio en el archivo XML y la consulta SQL
 */
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
include_once "../reports/PHPReportMaker.php";
$oficial = elusuario($iduser);
//=====================================================================================================
$xF					= new cFecha();
$fecha_inicial 		= (isset($_GET["on"])) ? $_GET["on"] : "";
$fecha_final 		= (isset($_GET["off"])) ? $_GET["off"] : "";

$operacion 			= (isset($_GET["f3"])) ? $_GET["f3"] : SYS_TODAS;
$output 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$persona 			= (isset($_GET["f50"])) ? $_GET["f50"] : SYS_NINGUNO;
$sucursal			= (isset($_GET["f700"])) ? $_GET["f700"] : SYS_TODAS;
$MX					= (isset($_GET["mx"])) ? true : false;
$forma_de_pago		= (isset($_GET["tipodepago"])) ? $_GET["tipodepago"] : SYS_TODAS;

$operacion			= (isset($_GET["tipooperacion"])) ? $_GET["tipooperacion"] : $operacion;
$fecha_final		= $xF->getFechaISO($fecha_final);
$fecha_inicial		= $xF->getFechaISO($fecha_inicial);

//http://localhost/rptotros/rpt_mvtos_x_tipo_segun_fechas.php
//estado=todas
//out=default
//empresa=todas
//convenio=todas
//tipodepago=transferenci
//usuario=todas
if($output != OUT_EXCEL ){ $fmt = "getFechaMX"; }
$BySucursal 		= ($sucursal == SYS_TODAS) ? "" : "  AND operaciones_mvtos.sucursal = '$sucursal'  ";
$xmlFile			= "report73";
$ByPersona 		= ($persona == SYS_NINGUNO) ? "" : " AND operaciones_mvtos.socio_afectado=$persona ";
$ByOperacion		= ($operacion == SYS_TODAS) ? "" : " AND `operaciones_mvtos`.`tipo_operacion` = $operacion ";
$ByPago				= ($forma_de_pago == SYS_TODAS) ? "" : " AND operaciones_recibos.tipo_pago ='$forma_de_pago' ";

$setSql 		= " SELECT
				operaciones_mvtos.sucursal,
				operaciones_recibos.tipo_pago 				AS 'tipo_de_pago',
				operaciones_mvtos.socio_afectado 			AS 'numero_de_socio',
				CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS
				'nombre_completo',
				operaciones_tipos.descripcion_operacion 	AS 'tipo_de_operacion',
				$fmt(operaciones_mvtos.fecha_afectacion) 			AS 'fecha',
				`operaciones_mvtos`.`idoperaciones_mvtos`	AS `operacion`,
				`operaciones_mvtos`.`recibo_afectado`   	AS `recibo`,
				`operaciones_recibos`.`recibo_fiscal`   	AS `fiscal`,
				operaciones_mvtos.docto_afectado 			AS 'documento',
				operaciones_mvtos.afectacion_real			AS 'monto',
				operaciones_mvtos.detalles 					AS 'observaciones'
				FROM
				`socios_general` `socios_general`
				INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
				ON `socios_general`.`codigo` = `operaciones_mvtos`.`socio_afectado`
					INNER JOIN `operaciones_recibos` `operaciones_recibos`
					ON `operaciones_recibos`.`idoperaciones_recibos` =
					`operaciones_mvtos`.`recibo_afectado`
						INNER JOIN `operaciones_tipos` `operaciones_tipos`
						ON `operaciones_tipos`.`idoperaciones_tipos` =
						`operaciones_mvtos`.`tipo_operacion`
				WHERE operaciones_mvtos.fecha_afectacion>='$fecha_inicial' AND operaciones_mvtos.fecha_afectacion<='$fecha_final'
					$ByPersona
					$BySucursal
					$ByOperacion
					$ByPago
			ORDER BY
				`operaciones_mvtos`.`sucursal`,
				`operaciones_recibos`.`fecha_operacion`,
				`operaciones_recibos`.`idoperaciones_recibos`,
				`operaciones_mvtos`.`idoperaciones_mvtos` ";
	//exit($setSql);

if ($output!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/$xmlFile.xml");
	$oOut = $oRpt->createOutputPlugin($output);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
	$xls	= new cHExcel();
	$xls->convertTable($setSql);
}


?>