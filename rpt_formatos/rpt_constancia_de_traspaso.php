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
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");

$oficial	= elusuario($iduser);
$recibo 	= $_GET["r"];
$forma		= $_GET["f"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<!-- -->
<?php
echo getRawHeader();
$sqlRecConSoc = "SELECT
	`socios_general`.*,
	`operaciones_recibos`.* 
FROM
	`operaciones_recibos` `operaciones_recibos` 
		INNER JOIN `socios_general` `socios_general` 
		ON `operaciones_recibos`.`numero_socio` = 
		`socios_general`.`codigo` 
WHERE
	(`operaciones_recibos`.`idoperaciones_recibos` =$recibo)";
	$DCred = obten_filas($sqlRecConSoc);
	
	$sqlMvtoConSoc = "SELECT
	`operaciones_mvtos`.`socio_afectado`        AS `socio`,
	`socios`.`nombre`,
	`operaciones_tipos`.`descripcion_operacion` AS 
	`operacion`,
	`operaciones_mvtos`.`fecha_operacion`       AS `fecha`,
	`operaciones_mvtos`.`afectacion_real`       AS `monto`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `socios` `socios` 
		ON `operaciones_mvtos`.`socio_afectado` = `socios`.
		`codigo` 
			INNER JOIN `operaciones_tipos` 
			`operaciones_tipos` 
			ON `operaciones_mvtos`.`tipo_operacion` = 
			`operaciones_tipos`.`idoperaciones_tipos` 
WHERE
	(`operaciones_mvtos`.`recibo_afectado` =$recibo)";
	$lst_mvtos = "";
	$cTblMvtos = new cTabla($sqlMvtoConSoc);
	$cTblMvtos->setWidth(80);
	$lst_mvtos = $cTblMvtos->Show();
	$vars = array(
			"variable_nombre_del_socio" => $DCred["apellidopaterno"] . " " . $DCred["apellidomaterno"] . " " . $DCred["nombre_completo"],
			"variable_nombre_de_la_sociedad" => EACP_NAME,
			"variable_domicilio_del_socio" => $domicilio_del_socio,
			"variable_documento_de_constitucion_de_la_sociedad" => EACP_DOCTO_CONSTITUCION,
			"variable_rfc_de_la_entidad" => EACP_RFC,
			"variable_rfc_del_socio" => $DCred["rfc"],
			"variable_curp_del_socio" => $DCred["curp"],
			"variable_nombre_del_representante_legal_de_la_sociedad" => EACP_REP_LEGAL,
			"variable_informacion_del_credito" => "NO_APLICA",
			"variable_domicilio_de_la_entidad" => EACP_DOMICILIO_CORTO,
			"variable_acta_notarial_de_poder_al_representante" => EACP_DOCTO_REP_LEGAL,
			"variable_lista_de_beneficiados" => "NO_APLICA",
			"variable_numero_de_socio" => $DCred["codigo"],
			"variable_nombre_caja_local" => "NO_APLICA",
			"variable_tipo_de_credito" => "NO_APLICA",
			"variable_monto_ministrado" => "NO_APLICA",
			"variable_tasa_mensual_de_interes_ordinario" => "NO_APLICA",
			"variable_credito_fecha_de_vencimiento" => "NO_APLICA",
			"variable_monto_garantia_liquida" => "NO_APLICA",
			"variable_tasa_mensual_de_interes_moratorio" => "NO_APLICA",
			"variable_tasa_de_garantia_liquida" => "NO_APLICA",
			"variable_plan_de_pagos" => "NO_APLICA",
			"variable_horario_de_trabajo_de_la_entidad"	=> EACP_HORARIO_DE_TRABAJO,
			"variable_testigo_del_acto" => $oficial,
			"variable_fecha_larga_actual" => fecha_larga(),
			"variable_nombre_de_presidente_de_vigilancia_de_la_entidad"=>EACP_PDTE_VIGILANCIA,
			"variable_nombre_de_la_representante_social" =>"NO_APLICA",
			"variable_listado_de_integrantes" => "NO_APLICA",
			"variable_nombre_de_la_vocal_de_vigilancia" => "NO_APLICA",
			"variable_nombre_del_grupo_solidario" => "NO_APLICA",
			"variable_domicilio_de_la_representante_social" => "NO_APLICA",
			"variable_meses_de_duracion_del_credito" => "NO_APLICA",
			"variable_en_letras_monto_ministrado" => "NO_APLICA",
			"variable_recibo_mvtos_con_socio"=>$lst_mvtos,
			
	);
	$texto_contrato = contrato($forma, "texto_del_contrato");
			foreach ($vars as $key => $value) {
				$texto_contrato = str_replace($key, $value, $texto_contrato);
			}
	echo $texto_contrato;

echo getRawFooter();
?>
</body>
</html>