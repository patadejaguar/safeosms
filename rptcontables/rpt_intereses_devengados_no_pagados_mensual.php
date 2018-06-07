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
//=====================================================================================================
$xHP		= new cHPage("TR.INTDEV por Mes ", HP_REPORT);

$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$mes			 	= parametro("m", $xF->mes(), MQL_INT);
$anno				= parametro("a", $xF->anno(), MQL_INT);
$tipo				= parametro("t", SYS_TODAS, MQL_INT);
$ica				= parametro("ica", false, MQL_BOOL);

$idx				= date("Ym", $xF->getInt($FechaFinal));
$idi				= date("Ym", $xF->getInt($FechaInicial));


$ByPersona			= $xFil->CreditoPorPersona($persona);


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
	`interes_devengado_por_cobrar`.`ejercicio`,
	MAX(`interes_devengado_por_cobrar`.`periodo`) AS `periodo`,
	SUM(`interes_devengado_por_cobrar`.`interes`) AS `interes`
FROM
	`socios` `socios`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio`
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio`
				INNER JOIN `interes_devengado_por_cobrar` `interes_devengado_por_cobrar`
				ON `creditos_solicitud`.`numero_solicitud` =
				`interes_devengado_por_cobrar`.`docto_afectado`
WHERE

	(`interes_devengado_por_cobrar`.`indice` >= $idi)
	AND
	(`interes_devengado_por_cobrar`.`indice` <=$idx)
	
	$ByPersona
AND
(`creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_CASTIGADO . ")
GROUP BY `creditos_solicitud`.`numero_solicitud`

HAVING interes > 0

ORDER BY
	
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud`

";

//exit( $setSql );

	$sql			= $setSql;
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
	//$xT->setOmitidos("codigo");
	//$xT->setOmitidos("indice");
	$xT->setTitulo("indice", "PERCONT");
	$xT->setTitulo("periodo", "MES");
	$xT->setFechaCorte($FechaFinal);
	$xT->setTipoSalida($out);
	$xT->setOmitidos("periodo");
	$xT->setOmitidos("periocidad");
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