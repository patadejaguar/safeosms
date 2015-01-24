<?php
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
	$xHP				= new cHPage("REPORTE DE CREDITOS POR RANGO DE MORA", HP_REPORT);
	$xF					= new cFecha();
	$xLi				= new cSQLListas();
//=====================================================================================================
/**
 * Filtrar si Existe Caja Local
 */
$estatus 				= (isset($_GET["f2"]) ) ? $_GET["f2"] : SYS_TODAS;
$frecuencia 			= (isset($_GET["f1"]) ) ? $_GET["f1"] : SYS_TODAS;
$convenio 				= (isset($_GET["f3"]) ) ? $_GET["f3"] : SYS_TODAS;

$estatus 				= (isset($_GET["estado"])) ? $_GET["estado"] : $estatus;
$frecuencia 			= (isset($_GET["periocidad"])) ? $_GET["periocidad"] : $frecuencia;
$frecuencia 			= (isset($_GET["frecuencia"])) ? $_GET["frecuencia"] : $frecuencia;
$convenio 				= (isset($_GET["convenio"])) ? $_GET["convenio"] : $convenio;

$empresa				= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 					= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$es_por_estatus 		= "";
$BySaldo				= " AND (creditos_solicitud.saldo_actual>=0.99) ";

if($estatus == CREDITO_ESTADO_AUTORIZADO OR $estatus == CREDITO_ESTADO_SOLICITADO){ $BySaldo		= ""; }

$ByOficial				= $xLi->OFiltro()->CreditosPorOficial(parametro("oficial", SYS_TODAS ,MQL_INT));
$BySucursal				= $xLi->OFiltro()->CreditosPorSucursal(parametro("sucursal", ""));
$ByEmpresa				= $xLi->OFiltro()->CreditosPorEmpresa($empresa);
$es_por_frecuencia 		= $xLi->OFiltro()->CreditosPorFrecuencia($frecuencia);
$es_por_convenio 		= $xLi->OFiltro()->CreditosPorProducto($convenio);
$es_por_estatus 		= $xLi->OFiltro()->CreditosPorEstado($estatus);
/* ***************************************************************************** */
if($out == OUT_EXCEL){
	
} else {
	echo $xHP->getHeader();
	echo $xHP->setBodyinit("initComponents();");
	echo getRawHeader();
	$xRpt		= new cReportes();
	echo $xRpt->getEncabezado($xHP->getTitle() );
}

$sql	= "SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud`,
	saldo,
	dias_morosos,
	dias_vencidos,
		
	(CASE WHEN ( (dias_vencidos + dias_morosos) <=0) THEN ( saldo ) ELSE 0 END) AS 'sin_mora',
	 
	(CASE WHEN ( (dias_morosos >=1 AND dias_morosos <=7)) THEN (saldo) ELSE 0 END) AS 'moroso_7_dias',
	(CASE WHEN ( (dias_morosos >=8 AND dias_morosos <=15)) THEN (saldo) ELSE 0 END) AS 'moroso_15_dias',
	(CASE WHEN ( (dias_morosos >=16 AND dias_morosos <=30)) THEN (saldo) ELSE 0 END) AS 'moroso_30_dias',
	(CASE WHEN ( (dias_morosos >=31 AND dias_morosos <=60)) THEN (saldo) ELSE 0 END) AS 'moroso_60_dias',
	(CASE WHEN ( (dias_morosos >=61 AND dias_morosos <=90)) THEN (saldo) ELSE 0 END) AS 'moroso_90_dias',
	(CASE WHEN ( (dias_morosos >=91 AND dias_morosos <=120)) THEN (saldo) ELSE 0 END) AS 'moroso_120_dias',
	(CASE WHEN ( (dias_morosos >=121 AND dias_morosos <=180)) THEN (saldo) ELSE 0 END) AS 'moroso_180_dias',
	(CASE WHEN ( (dias_morosos >=181)) THEN (saldo) ELSE 0 END) AS 'moroso_mayor',
	 
	(CASE WHEN ( (dias_vencidos >=1 AND dias_vencidos <=7)) THEN (saldo) ELSE 0 END) AS 'vencido_7_dias',
	(CASE WHEN ( (dias_vencidos >=8 AND dias_vencidos <=15)) THEN (saldo) ELSE 0 END) AS 'vencido_15_dias',
	(CASE WHEN ( (dias_vencidos >=16 AND dias_vencidos <=30)) THEN (saldo) ELSE 0 END) AS 'vencido_30_dias',
	(CASE WHEN ( (dias_vencidos >=31 AND dias_vencidos <=60)) THEN (saldo) ELSE 0 END) AS 'vencido_60_dias',
	(CASE WHEN ( (dias_vencidos >=61 AND dias_vencidos <=90)) THEN (saldo) ELSE 0 END) AS 'vencido_90_dias',
	(CASE WHEN ( (dias_vencidos >=91 AND dias_vencidos <=120)) THEN (saldo) ELSE 0 END) AS 'vencido_120_dias',
	(CASE WHEN ( (dias_vencidos >=121 AND dias_vencidos <=180)) THEN (saldo) ELSE 0 END) AS 'vencido_180_dias',
	(CASE WHEN ( (dias_vencidos >=181)) THEN (saldo) ELSE 0 END) AS 'vencido_mayor'
	 
	 FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `dias_en_mora` `dias_en_mora` 
		ON `creditos_solicitud`.`numero_solicitud` = `dias_en_mora`.
		`numero_solicitud` 
			INNER JOIN `socios` `socios` 
			ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo`
	 WHERE 
		saldo > " . TOLERANCIA_SALDOS . "
				AND dias_morosos >=1
		$es_por_convenio
		$es_por_frecuencia
		$es_por_estatus
		$ByOficial
		$BySucursal
		$ByEmpresa
		/*Disable castigados*/
		AND `creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_CASTIGADO . "
	 ORDER BY dias_morosos DESC
	";
//exit( $sql );
if($out == OUT_EXCEL){
	$xls	= new cHExcel();
	$xls->convertTable($sql, "REPORTE DE CREDITOS POR RANGO DE MORA");
} else {
	$xTBL	= new cTabla($sql);
	//$xTBL->setEventKey("jsGoEstadoDeCuentaDeCreditosPorPersona");
	$xTBL->setKeyField("numero_solicitud");
	$xTBL->setTdClassByType();
	$init		= 6;
	$arrCSumas	= array( 
			3 => "saldo", 
			$init => "sin_mora",
			($init+1) => "moroso_7_dias",
			($init+2) => "moroso_15_dias",
			($init+3) => "moroso_30_dias",
			($init+4) => "moroso_60_dias",
			($init+5) => "moroso_90_dias",
			($init+6) => "moroso_120_dias",
			($init+7) => "moroso_180_dias",
			($init+8) => "moroso_mayor",
			
			($init+9)=> "vencido_7_dias",
			($init+10) => "vencido_15_dias",
			($init+11) => "vencido_30_dias",
			($init+12) => "vencido_60_dias",
			($init+13) => "vencido_90_dias",
			($init+14) => "vencido_120_dias",
			($init+15) => "vencido_180_dias",
			($init+16) => "vencido_mayor"
						
			);
	$xTBL->setFootSum($arrCSumas);
	echo $xTBL->Show();
	echo getRawFooter();
	echo $xHP->setBodyEnd();
	?>
	<script>
	<?php
	
	?>
	function initComponents(){
		window.print();
	}
	</script>
	<?php
	$xHP->end();
}
?>