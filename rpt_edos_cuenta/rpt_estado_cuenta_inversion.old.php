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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../reports/PHPReportMaker.php";

$fecha_inicial 	= $_GET["on"];
$fecha_final 	= $_GET["off"];
$iddocto 		= $_GET["docto"];
$input 			= $_GET["out"];
$appByFechas	= $_GET["v73"];
$varByFechas	= "";
if ($appByFechas == 1){
	$varByFechas	= " AND operaciones_mvtos.fecha_afectacion>='$fecha_inicial' AND operaciones_mvtos.fecha_afectacion<='$fecha_final' ";
}
	if (!$input) {
		$input = "default";
	}


	//$sql_set .= "  WHERE captacion_cuentas.tipo_cuenta=20 and captacion_cuentas.saldo_cuenta>0 and captacion_cuentas.numero_socio=socios_general.codigo ";
	
	$sql_set = "SELECT 
	
	
	socios_general.codigo,
	CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto) AS 'nombre_completo',
	
	captacion_cuentas.numero_cuenta AS 'numero_de_cuenta',
	captacion_cuentas.fecha_afectacion AS 'fecha_de_apertura',
	captacion_cuentastipos.descripcion_cuentastipos AS 'tipo_de_cuenta' ,
	captacion_cuentas.saldo_cuenta AS 'saldo_actual',
	captacion_cuentas.inversion_fecha_vcto AS 'proximo_vencimiento',
	captacion_cuentas.tasa_otorgada, captacion_cuentas.dias_invertidos	AS 'numero_de_dias',
	captacion_cuentas.observacion_cuenta AS 'observaciones',
	operaciones_mvtos.fecha_afectacion AS 'fecha_de_operacion',
	operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion',
	(operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion) AS 'monto',
	operaciones_mvtos.saldo_anterior,
	operaciones_mvtos.saldo_actual AS 'saldo_historico',
	operaciones_mvtos.detalles,
	operaciones_mvtos.idoperaciones_mvtos
	FROM
	`captacion_cuentas` `captacion_cuentas`
		INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
		ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
		`idcaptacion_cuentastipos`
			INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
			ON `captacion_cuentas`.`numero_cuenta` = `operaciones_mvtos`.
			`docto_afectado`
				INNER JOIN `operaciones_tipos` `operaciones_tipos`
				ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
				`idoperaciones_tipos`
					INNER JOIN `socios_general` `socios_general`
					ON `captacion_cuentas`.`numero_socio` = `socios_general`.
					`codigo`

	WHERE captacion_cuentas.numero_cuenta=$iddocto
	$varByFechas
	ORDER BY socios_general.codigo,captacion_cuentas.numero_cuenta,  operaciones_mvtos.fecha_afectacion ";
	
	$rs = mysql_query($sql_set, cnnGeneral() );
	$td	= "";
	
	
	while($rw = mysql_fetch_array($rs) ){
		
		$td		.= "
			<tr>
				<td>" . $rw[""] . "</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		";
	}
	
	$tBody	= "
	<table width=\"100%\" align=\"center\" >
		<thead>
			<tr>
				<th>Fecha</th>
				<th>Referencia</th>
				<th>Operacion</th>
				<th>Inversiones</th>
				<th>Depositos</th>
				<th>Retiros</th>
				<th>Otros</th>
				<th>Saldo</th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
	";

	/*if ($input!=OUT_EXCEL) {
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($sql_set);
	$oRpt->setXML("../repository/report25.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($sql_set, "", "fieldnames");
}*/

?>