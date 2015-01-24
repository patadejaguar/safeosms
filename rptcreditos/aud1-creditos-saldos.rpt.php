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
$xHP					= new cHPage("TR.Saldos de Credito", HP_REPORT);
$xF						= new cFecha();
$xQL					= new MQL();
$xLi					= new cSQLListas();
//=====================================================================================================


$periocidad 			= parametro("f1", SYS_TODAS);
$periocidad 			= parametro("periocidad", $periocidad);
$periocidad 			= parametro("frecuencia", $periocidad);

$estado 				= parametro("estado", SYS_TODAS); 
$estado 				= parametro("estatus", $estado);
$producto 				= parametro("convenio", SYS_TODAS);
$producto 				= parametro("producto", $producto);
$fechaInicial			= parametro("on", EACP_FECHA_DE_CONSTITUCION);
$fechaFinal				= parametro("off", fechasys());
$fechaInicial			= $xF->getFechaISO($fechaInicial);
$fechaFinal				= $xF->getFechaISO($fechaFinal);
$formato				= parametro("out", SYS_DEFAULT, MQL_RAW);
$sucursal				= parametro("sucursal", SYS_TODAS, MQL_RAW);
$xRPT					= new cReportes($xHP->getTitle());

$ByProducto				= (setNoMenorQueCero($producto) > 0) ? " AND (`creditos_solicitud`.`tipo_convenio`=$producto) " : "" ;
$BySucursal				= ($sucursal == SYS_TODAS OR $sucursal == "") ? "": " AND (`creditos_solicitud`.`sucursal` = '$sucursal') ";
/*	`creditos_solicitud`.`numero_solicitud`,
	`creditos_solicitud`.`fecha_solicitud`,
	`creditos_solicitud`.`fecha_autorizacion`,
	`creditos_solicitud`.`monto_solicitado`,
	`creditos_solicitud`.`monto_autorizado`,
	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`docto_autorizacion`,
	`creditos_solicitud`.`plazo_en_dias`,
	`creditos_solicitud`.`numero_pagos`,
	`creditos_solicitud`.`tasa_interes`,
	`creditos_solicitud`.`periocidad_de_pago`,
	`creditos_solicitud`.`tipo_credito`,
	`creditos_solicitud`.`estatus_actual`,
	`creditos_solicitud`.`tipo_autorizacion`,
	`creditos_solicitud`.`oficial_credito`,
	`creditos_solicitud`.`fecha_vencimiento`,
	`creditos_solicitud`.`pagos_autorizados`,
	`creditos_solicitud`.`dias_autorizados`,
	`creditos_solicitud`.`periodo_solicitudes`,
	`creditos_solicitud`.`destino_credito`,
	`creditos_solicitud`.`idusuario`,
	`creditos_solicitud`.`nivel_riesgo`,
	`creditos_solicitud`.`saldo_actual`,
	`creditos_solicitud`.`fecha_ultimo_mvto`,
	`creditos_solicitud`.`tipo_convenio`,
	`creditos_solicitud`.`interes_diario`,
	`creditos_solicitud`.`ultimo_periodo_afectado`,
	`creditos_solicitud`.`tasa_moratorio`,
	`creditos_solicitud`.`observacion_solicitud`,
	`creditos_solicitud`.`tasa_ahorro`,
	`creditos_solicitud`.`grupo_asociado`,
	`creditos_solicitud`.`descripcion_aplicacion`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`contrato_corriente_relacionado`,
	`creditos_solicitud`.`monto_parcialidad`,
	`creditos_solicitud`.`oficial_seguimiento`,
	`creditos_solicitud`.`sucursal`,
	`creditos_solicitud`.`interes_normal_devengado`,
	`creditos_solicitud`.`tipo_de_pago`,
	`creditos_solicitud`.`interes_normal_pagado`,
	`creditos_solicitud`.`interes_moratorio_devengado`,
	`creditos_solicitud`.`interes_moratorio_pagado`,
	`creditos_solicitud`.`fecha_mora`,
	`creditos_solicitud`.`fecha_vencimiento_dinamico`,
	`creditos_solicitud`.`causa_de_mora`,
	`creditos_solicitud`.`estatus_de_negociacion`,
	`creditos_solicitud`.`tipo_de_calculo_de_interes`,
	`creditos_solicitud`.`persona_asociada`,
	`creditos_solicitud`.`perfil_de_intereses`,
	`creditos_solicitud`.`fuente_de_fondeo`,
	`creditos_solicitud`.`fecha_de_primer_pago`,
	COUNT(`operaciones_mvtos`.`tipo_operacion`) AS `operaciones`,
	MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha`,
	SUM(
	IF(`operaciones_mvtos`.`tipo_operacion` = 120,	`operaciones_mvtos`.`afectacion_real`, 0	)
	)  AS `abonos`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		RIGHT OUTER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `operaciones_recibos` `operaciones_recibos` 
			ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
			`idoperaciones_recibos` */

$sql					= "
SELECT
	`creditos_solicitud`.`numero_solicitud`                  AS `contrato`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio`       AS `producto`,
	`socios`.`codigo`                                        AS `persona`,
	`socios`.`nombre`                                        AS `nombre`,
	`socios`.`alias_dependencia`                             AS `empresa`,
	`creditos_solicitud`.`monto_autorizado`                  AS `monto_original`
	,
	`creditos_solicitud`.`plazo_en_dias`,
	`creditos_solicitud`.`fecha_ministracion`                AS 
	`fecha_de_desembolso`,
	`creditos_solicitud`.`tasa_interes`                      AS 
	`tasa_de_interes`,
	`creditos_solicitud`.`pagos_autorizados`                 AS 
	`numero_de_pagos`,
	IF(`creditos_solicitud`.`periocidad_de_pago` = " . CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO . ", 0,`creditos_solicitud`.`monto_parcialidad`)                 AS 
	`monto_de_amortizacion`,
	'?' AS `tipo`,
	`creditos_solicitud`.`fecha_de_primer_pago`,
	`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `frecuencia`,
	`creditos_solicitud`.`ultimo_periodo_afectado`           AS 
	`ultima_parcialidad` ,
	COUNT(`operaciones_mvtos`.`tipo_operacion`) AS `operaciones`,
	MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha`,
	SUM(
	IF(`operaciones_mvtos`.`tipo_operacion` = 120,	`operaciones_mvtos`.`afectacion_real`, 0	)
	)  AS `abonos`,
	(`creditos_solicitud`.`monto_autorizado`  - 	SUM(
	IF(`operaciones_mvtos`.`tipo_operacion` = 120,	`operaciones_mvtos`.`afectacion_real`, 0	)
	)) AS 'saldo',
	`creditos_solicitud`.`saldo_actual` AS 'saldo_sistema'  
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
		ON `creditos_solicitud`.`periocidad_de_pago` = 
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio` 
				INNER JOIN `socios` `socios` 
				ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
					RIGHT OUTER JOIN `operaciones_mvtos` `operaciones_mvtos` 
					ON `operaciones_mvtos`.`docto_afectado` = 
					`creditos_solicitud`.`numero_solicitud`
			
WHERE
	(
	(`operaciones_mvtos`.`tipo_operacion` =120) 
	OR
	(`operaciones_mvtos`.`tipo_operacion` =110))
	AND
	(`operaciones_mvtos`.`fecha_afectacion` <= '$fechaFinal') 
	$ByProducto $BySucursal
GROUP BY
	`operaciones_mvtos`.`docto_afectado`
	
HAVING saldo > 0

ORDER BY `creditos_solicitud`.`fecha_ministracion`
 
";
//$sql				= "CALL sp_saldos_al_cierre('$fechaFinal')";
//exit($sql);
$xTbl					= new cTabla($sql);
$xTbl->setFootSum(array(
		5 => "monto_original",
		17 => "abonos",
		18 => "saldo"
		));
/*$xTbl->setFootSum(array(
	3 => "monto_autorizado",
		52 => "abonos",
		53 => "saldo"
));*/

$xRPT->setSQL($xTbl->getSQL());
$xTbl->setTipoSalida($formato);
$xRPT->setOut($formato);
$xRPT->addContent($xTbl->Show());
//$xRPT->setResponse();
echo $xRPT->render(true);
?>