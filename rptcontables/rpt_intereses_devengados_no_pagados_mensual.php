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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../reports/PHPReportMaker.php");

$oficial = elusuario($iduser);
//=====================================================================================================
$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}

$mes			 	= $_GET["m"];
$anno				= $_GET["a"];
$modalidad			= $_GET["t"];


$sqlO = "SELECT
	`interes_normal_devengado`.`docto_afectado` AS `credito`,
	`interes_normal_devengado`.`socio_afectado` AS `socio`,
	MAX(`interes_normal_devengado`.`periodo`)   AS `mes`,
	MAX(`interes_normal_devengado`.`ejercicio`) AS `anno`,
	SUM(`interes_normal_devengado`.`interes`) 
FROM
	`interes_normal_devengado` `interes_normal_devengado` 
WHERE
	(`interes_normal_devengado`.`periodo` <=2005) AND
	(`interes_normal_devengado`.`ejercicio` <=2008) 
GROUP BY
	`interes_normal_devengado`.`docto_afectado`,
	`interes_normal_devengado`.`socio_afectado` ";



	$setSql = " SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud`            AS `solicitud`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `convenio`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`monto_autorizado`            AS `saldo_historico`,
	`creditos_solicitud`.`pagos_autorizados`           AS `pagos`,
	`creditos_solicitud`.`periocidad_de_pago`            AS `periocidad`,
	`creditos_solicitud`.`tipo_autorizacion`,
	`creditos_solicitud`.`fecha_conciliada`           AS `ultima_operacion`,
	`creditos_solicitud`.`saldo_conciliado`           AS `saldo_insoluto`,

	`creditos_solicitud`.`fecha_conciliada`,
	`creditos_solicitud`.`saldo_conciliado`,
	`interes_normal_devengado`.`ejercicio`,
	`interes_normal_devengado`.`periodo`,
	`interes_normal_devengado`.`interes`
FROM
	`socios` `socios`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio`
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio`
				INNER JOIN `interes_normal_devengado` `interes_normal_devengado`
				ON `creditos_solicitud`.`numero_solicitud` =
				`interes_normal_devengado`.`docto_afectado`
WHERE
	(`interes_normal_devengado`.`periodo` =$mes)
	AND
	(`interes_normal_devengado`.`ejercicio` =$anno)
ORDER BY
	`creditos_tipoconvenio`.`tipo_autorizacion` DESC,
	`creditos_solicitud`.`tipo_convenio`,
	`interes_normal_devengado`.`ejercicio`,
	`interes_normal_devengado`.`periodo`,
	`socios`.`codigo`,
	`creditos_solicitud`.`numero_solicitud`
";

//exit( $setSql );

?>