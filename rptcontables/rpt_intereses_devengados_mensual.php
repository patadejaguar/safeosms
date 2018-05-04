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
$xHP		= new cHPage("TR.INTDEV por Mes ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$xF->set($FechaFinal);

$mes			 	= parametro("m", $xF->mes(), MQL_INT);
$anno				= parametro("a", $xF->anno(), MQL_INT);
$tipo				= parametro("t", SYS_TODAS, MQL_INT);
$ica				= parametro("ica", false, MQL_BOOL);

$idx				= date("Ym", $xF->getInt());
$idi				= date("Ym", $xF->getInt($FechaInicial));

switch($ica){
	case true:
		$InTable	= "interes_normal_devengado_solo_ica";
		break;
	case false:
		$InTable	= "interes_normal_devengado_sin_ica";
		break;
	default:
		$InTable	= "interes_normal_devengado";
		break;
}





$sSql[2] = " SELECT
`socios`.`codigo`,
`socios`.`nombre`,

`socios`.`alias_dependencia` AS `empresa`,

`creditos_solicitud`.`numero_solicitud`            AS `solicitud`,
`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `convenio`,
`creditos_solicitud`.`fecha_ministracion`,
`creditos_solicitud`.`monto_autorizado`            AS `saldo_original`,
`creditos_solicitud`.`pagos_autorizados`           AS `pagos`,
`creditos_solicitud`.`periocidad_de_pago`            AS `periocidad`,
`creditos_solicitud`.`tipo_autorizacion`,
`creditos_solicitud`.`fecha_conciliada`           AS `ultima_operacion`,
`creditos_solicitud`.`saldo_conciliado`           AS `saldo_insoluto`,

`creditos_solicitud`.`fecha_conciliada`,
`creditos_solicitud`.`saldo_conciliado`,
`$InTable`.`ejercicio`,
`$InTable`.`periodo`,
`$InTable`.`interes`,
`creditos_solicitud`.`estatus_actual`
FROM
`socios` `socios`
INNER JOIN `creditos_solicitud` `creditos_solicitud`
ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio`
INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
`idcreditos_tipoconvenio`
INNER JOIN `$InTable` `$InTable`
ON `creditos_solicitud`.`numero_solicitud` =
`$InTable`.`docto_afectado`
WHERE
(`$InTable`.`periodo` <= $mes)
AND
(`$InTable`.`ejercicio` <=$anno)
AND (`creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_CASTIGADO . ")

ORDER BY
`creditos_solicitud`.`estatus_actual`,
`creditos_tipoconvenio`.`tipo_autorizacion` DESC,
`creditos_solicitud`.`tipo_convenio`,
`$InTable`.`ejercicio`,
`$InTable`.`periodo`,
`socios`.`codigo`,
`creditos_solicitud`.`numero_solicitud`
";
///Acumulado a la fecha de corte


$sSql[2] = " SELECT
`socios`.`codigo`,
`socios`.`nombre`,

`socios`.`alias_dependencia` AS `empresa`,

`creditos_solicitud`.`numero_solicitud`            AS `solicitud`,
`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `convenio`,
`creditos_solicitud`.`fecha_ministracion`,
`creditos_solicitud`.`monto_autorizado`            AS `saldo_original`,
`creditos_solicitud`.`pagos_autorizados`           AS `pagos`,
`creditos_solicitud`.`periocidad_de_pago`            AS `periocidad`,
`creditos_solicitud`.`tipo_autorizacion`,
`creditos_solicitud`.`fecha_conciliada`           AS `ultima_operacion`,
`creditos_solicitud`.`saldo_conciliado`           AS `saldo_insoluto`,

`creditos_solicitud`.`fecha_conciliada`,
`creditos_solicitud`.`saldo_conciliado`,
MAX(`$InTable`.`indice`) AS `indice`,
SUM(`$InTable`.`interes`) AS `interes`,
`creditos_solicitud`.`estatus_actual`
FROM
`socios` `socios`
INNER JOIN `creditos_solicitud` `creditos_solicitud`
ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio`
INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
`idcreditos_tipoconvenio`
INNER JOIN `$InTable` `$InTable`
ON `creditos_solicitud`.`numero_solicitud` =
`$InTable`.`docto_afectado`
WHERE
(`$InTable`.`indice` >= $idi)
AND
(`$InTable`.`indice` <= $idx)

AND (`creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_CASTIGADO . ")
GROUP BY `creditos_solicitud`.`numero_solicitud`
ORDER BY
`creditos_solicitud`.`estatus_actual`,
`creditos_tipoconvenio`.`tipo_autorizacion` DESC,
`creditos_solicitud`.`tipo_convenio`,
`$InTable`.`indice`,

`socios`.`codigo`,
`creditos_solicitud`.`numero_solicitud`
";


$uPer = date("Ym", strtotime("$anno-$mes-01") );

$sSql[1] = "SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`socios`.`alias_dependencia` AS `empresa`, 
	`creditos_solicitud`.`numero_solicitud`            	AS `solicitud`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio` 	AS `convenio`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`monto_autorizado`            	AS `saldo_historico`,
	`creditos_solicitud`.`pagos_autorizados`           	AS `pagos`,
	`creditos_solicitud`.`periocidad_de_pago`          	AS `periocidad`,
	`creditos_solicitud`.`tipo_autorizacion`,
	`creditos_solicitud`.`fecha_conciliada`           	AS `ultima_operacion`,
	`creditos_solicitud`.`saldo_conciliado`           	AS `saldo_insoluto`,
	
	`creditos_solicitud`.`fecha_conciliada`,
	`creditos_solicitud`.`saldo_conciliado`,
	
	MAX(`interes_devengado_por_cobrar`.`ejercicio`) AS 'ejercicio',
	MAX(`interes_devengado_por_cobrar`.`periodo`) AS 'periodo',
	setNoMenorCero(
	SUM(`interes_devengado_por_cobrar`.`interes`)
	)	AS 'interes',
	`creditos_solicitud`.`interes_normal_devengado`,
	`creditos_solicitud`.`interes_normal_pagado`,
	`creditos_solicitud`.`sdo_int_ant`	AS 'saldo_ica',
	setNoMenorCero(
	(`creditos_solicitud`.`interes_normal_devengado` -
	(`creditos_solicitud`.`interes_normal_pagado` + `creditos_solicitud`.`sdo_int_ant`) )
	) AS 'saldo_interes', `creditos_solicitud`.`estatus_actual`

FROM
	`socios` `socios` 
	INNER JOIN `creditos_solicitud` `creditos_solicitud` ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio` 
	INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
	INNER JOIN `interes_devengado_por_cobrar` `interes_devengado_por_cobrar` ON `creditos_solicitud`.`numero_solicitud` = `interes_devengado_por_cobrar`.`docto_afectado`
WHERE
(`interes_devengado_por_cobrar`.`indice` <= $uPer)
AND
(`creditos_solicitud`.`saldo_conciliado` >" . TOLERANCIA_SALDOS . ")
AND
(`creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_CASTIGADO . ")
GROUP BY
`creditos_solicitud`.`numero_solicitud`
ORDER BY
	`creditos_solicitud`.`estatus_actual`,
	`creditos_solicitud`.`tipo_convenio`,
	`socios`.`codigo`,
	`creditos_solicitud`.`numero_solicitud` ";

$sql			= (isset($sSql[$tipo])) ? $sSql[$tipo] : $sSql[2];
$titulo			= "";
$archivo		= "";

//setLog($sql);

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setOmitidos("estatus_actual");
$xT->setOmitidos("saldo_insoluto");
$xT->setOmitidos("ultima_operacion");
$xT->setOmitidos("fecha_conciliada");
$xT->setOmitidos("saldo_conciliado");
$xT->setOmitidos("tipo_autorizacion");
$xT->setOmitidos("codigo");
//$xT->setOmitidos("indice");
$xT->setTitulo("indice", "PERCONT");

$xT->setTipoSalida($out);
$xT->setFootSum(array( 9 => "interes" ));

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

?>