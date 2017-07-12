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
$xHP		= new cHPage("TR.COLOCACION ACUMULADA", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();
$xRuls		= new cReglaDeNegocio();

$UseRecFechaR	= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_RPT_USE_FECHAREAL);

$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);
$omitirceros	= parametro("nocero", false ,MQL_BOOL);
$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);



$BySuc			= $xFil->OperacionesPorSucursal($sucursal);

$ByFecha		= ($UseRecFechaR == true) ? $xFil->RecibosPorFechaDeRegistro($FechaInicial, $FechaFinal) : $xFil->CreditosPorFechaDeMinistracion($FechaInicial, $FechaFinal);
$BySaldo		= ($omitirceros == true) ? $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">=") : "";
$sql			= "SELECT
	
	`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,
	COUNT(`operaciones_mvtos`.`docto_afectado`)        AS `creditos`,
	
	
	SUM(IF(`operaciones_recibos`.`tipo_pago` ='" . TESORERIA_PAGO_NINGUNO . "', `operaciones_mvtos`.`afectacion_real`,0))         AS `reestructuras`,
	AVG(`operaciones_mvtos`.`afectacion_real`)         AS `promedio`,
			
	SUM(`operaciones_mvtos`.`afectacion_real`)         AS `monto`
	
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
		ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio` 
			INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
			ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
			`numero_solicitud` 
				INNER JOIN `operaciones_recibos` `operaciones_recibos` 
				ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`
				.`idoperaciones_recibos` 
WHERE
	(`operaciones_mvtos`.`tipo_operacion` = " . OPERACION_CLAVE_MINISTRACION . ") 
	$ByFecha
	$BySuc
	$BySaldo
GROUP BY
	`creditos_solicitud`.`tipo_convenio`,
	`operaciones_mvtos`.`tipo_operacion`
ORDER BY `monto` DESC
";


	$titulo			= $xHP->getTitle();
	$archivo		= "$titulo.pdf";
	
	$xRPT			= new cReportes($titulo);
	$xRPT->setFile($archivo);
	$xRPT->setOut($out);
	$xRPT->setSQL($sql);
	$xRPT->setTitle($xHP->getTitle());
	//============ Reporte
	$xT		= new cTabla($sql, 0);
	$xT->setTipoSalida($out);
	//$xT->setPrepareChart(true, $xT->CHART_PIE);
	
	$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
	$xRPT->setBodyMail($body);
	
	
	$xRPT->addContent($body);
	$xRPT->addContent("<div id='idivchart'></div>");
	$xCh	= new cChart("idivchart");
	$xCh->addDataset($sql, "monto", "producto");
	$xT->setFootSum(array(1 => "creditos", 4=> "monto", 3=>"reestructuras", 2=>"promedio"));
	$xCh->setFuncConvert("enmiles");
	$xCh->setAlto("400");
	$xCh->setProcess();
	$xRPT->addJsCode($xCh->getJs());
	$xRPT->addContent( $xT->Show("", true, "idtbl"  ) );
	
	$xRPT->setResponse();
	$xRPT->setSenders($senders);
	echo $xRPT->render(true);	
?>