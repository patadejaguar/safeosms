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
include_once "../core/core.config.inc.php";

$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Estado de Cuenta por Credito</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<?php echo $head_pagina ?>
<span class="Estilo1">

<p class="bigtitle">ESTADO DE CUENTA DE CREDITOS CON TODAS SUS OPERACIONES</p>
<hr>
<?php
//rptedoctacredito.php?pb=2005228&f18=yes&f19=999&f50=20052&on=1998-1-16&off=2012-1-16

$idsolicitud 	= $_GET["solicitud"];
$ids 			= $_GET["pb"];
$TipoOp			= ( isset($_GET["f19"]) ) ? $_GET["f19"] : "todas";
$FechaFinal		= ( isset($_GET["off"]) ) ? $_GET["off"] : fechasys();
$xF				= new cFecha(0, $FechaFinal);
$FechaInicial	= ( isset($_GET["on"]) ) ? $_GET["on"] : $xF->getDiaInicial();

$ByTipo			= ( $TipoOp == "todas") ? "" : " AND (`operaciones_mvtos`.`tipo_operacion` = $TipoOp) " ;

	if (!$idsolicitud) {

		$idsolicitud = $ids;
		//exit($msg_rpt_exit);
	}
	if ((!$idsolicitud) && (!$ids)) {
		exit($msg_rpt_exit);
	}


	// datos de la solicitud

	$xCred		= new cCredito($idsolicitud);
	$xCred->init();
	echo $xCred->getFichaDeSocio();
	echo $xCred->getFicha();

	echo "<hr />";
			$sqlmvto = "SELECT
	`operaciones_mvtos`.`fecha_operacion`       AS `fecha`,
	`operaciones_mvtos`.`recibo_afectado`       AS `recibo`,
	`operaciones_recibos`.`tipo_pago`           AS `tipo_de_pago`,
	`operaciones_recibos`.`recibo_fiscal`       AS `recibo_fiscal`,

	`operaciones_mvtos`.`periodo_socio`         AS `parcialidad`,
	`operaciones_tipos`.`descripcion_operacion` AS `operacion`,
	`operaciones_mvtos`.`afectacion_real`       AS `monto`,

	`operaciones_mvtos`.`detalles`              AS `observaciones`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `operaciones_recibos` `operaciones_recibos` 
		ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
		`idoperaciones_recibos` 
			INNER JOIN `operaciones_tipos` `operaciones_tipos` 
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos` 
WHERE
	(`operaciones_mvtos`.`docto_afectado` =$idsolicitud) 
	AND
	(
	(`operaciones_mvtos`.`fecha_operacion` >='$FechaInicial')
	AND
	(`operaciones_mvtos`.`fecha_operacion`<='$FechaFinal' )
	)
	$ByTipo
ORDER BY
	`operaciones_mvtos`.`fecha_operacion`,
	`operaciones_tipos`.`descripcion_operacion` ";
//exit($sqlmvto);
			$x = new cTabla($sqlmvto);
			$x->setKeyField("idoperaciones_mvtos");
			$x->setTdClassByType();
			$x->setWidth();
			echo $x->Show();
			
?>
</span>
<?php echo getRawFooter(); ?>
</body>
</html>