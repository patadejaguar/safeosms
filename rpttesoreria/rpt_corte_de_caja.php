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
$xHP		= new cHPage("TR.CORTE de operaciones por cajero", HP_REPORT);
$mql		= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$xHP->setTitle($xHP->getTitle() );

//=====================================================================================================
$fecha_inicial	= (isset($_GET["on"])) ?  $_GET["on"] : "";
$fecha_final	= (isset($_GET["off"])) ?  $_GET["off"] : "";

$dependencia	= (isset($_REQUEST["dependencia"])) ?  $_GET["dependencia"] : SYS_TODAS;
$dependencia	= (isset($_REQUEST["empresa"])) ?  $_GET["empresa"] : $dependencia;

$cajero		= (isset($_GET["f3"])) ? $_GET["f3"] : SYS_TODAS;
$cajero		= (isset($_GET["cajero"])) ? $_GET["cajero"] : $cajero;

$ByCajero	= ($cajero == SYS_TODAS) ? "" : "AND operaciones_mvtos.idusuario=$cajero ";
$input		= (isset($_GET["out"])) ? $_GET["out"] : OUT_DEFAULT;
$ByFecha	= "";
$ByDependencia	= ($dependencia != SYS_TODAS) ? " AND socios_general.dependencia = $dependencia " : "";
/* ******************************************************************************/
if($fecha_inicial != ""){
	$ByFecha	= " AND operaciones_mvtos.fecha_afectacion>='$fecha_inicial' ";
}

if($fecha_final != ""){
	$ByFecha	.= " AND operaciones_mvtos.fecha_afectacion<='$fecha_final' ";
}

$setSql = " SELECT operaciones_recibos.tipo_pago AS 'tipo_de_pago',
				operaciones_mvtos.socio_afectado AS 'numero_de_socio',
				CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS
				'nombre_completo',
				operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion',
				operaciones_mvtos.fecha_afectacion AS 'fecha',
				`operaciones_mvtos`.`idoperaciones_mvtos` AS `operacion`,
				`operaciones_mvtos`.`recibo_afectado`     AS `recibo`,
				operaciones_mvtos.docto_afectado AS 'documento',
				(operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion) AS 'monto',
				operaciones_mvtos.detalles AS 'observaciones',
				operaciones_mvtos.tasa_asociada,
				operaciones_mvtos.dias_asociados,
				operaciones_mvtos.saldo_actual AS 'saldo'
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

				WHERE
				operaciones_tipos.afectacion_en_recibo!=0
				AND
				operaciones_mvtos.valor_afectacion!=0
				/*AND
				(operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion) != 0*/
				$ByCajero
	 			$ByFecha
				$ByDependencia
			ORDER BY
				`operaciones_mvtos`.`sucursal`,
				`operaciones_mvtos`.`idusuario`,
				`operaciones_recibos`.`tipo_pago`,
				operaciones_mvtos.fecha_afectacion,
				`operaciones_recibos`.`idoperaciones_recibos`,
				`operaciones_mvtos`.`idoperaciones_mvtos`";
	//echo $setSql; exit;

if ($input != OUT_EXCEL) {

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report27b.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
	$xHEx	= new cHExcel();
	$xHEx->convertTable($setSql);
}
?>