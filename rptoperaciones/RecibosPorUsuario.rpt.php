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

//=====================================================================================================
$xHP			= new cHPage("TR.Reporte de Recibos", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();



$TipoDePago		= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW); $TipoDePago	= parametro("pago", $TipoDePago, MQL_RAW);
$TipoDeRecibo	= parametro("tipoderecibo", 0, MQL_INT); $TipoDeRecibo = parametro("tiporecibo", $TipoDeRecibo, MQL_INT);


$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$cajero 		= parametro("f3", 0, MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);

$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);

$estadisticos	= parametro("estadisticos", false, MQL_BOOL);


$ByTipoPago		= $xFil->RecibosPorTipoDePago($TipoDePago);
$ByTRecibo		= $xFil->RecibosPorTipo($TipoDeRecibo);
$ByEmpresa		= $xFil->RecibosPorPersonaAsociada($empresa);
$ByUsuario		= $xFil->RecibosPorCajero($cajero);
$ByFecha		= $xFil->RecibosPorFecha($FechaInicial, $FechaFinal);	
$ByEstad		= ($estadisticos == true) ? $xFil->RecibosNoEstadisticos() : "";

$titulo			= $xHP->getTitle();
$archivo		= $xHP->getTitle();


	$setSql = "SELECT
		`usuarios`.`nombreusuario`                          AS `usuario`,
		`operaciones_recibos`.`idoperaciones_recibos`       AS `numero`,
		`operaciones_recibos`.`fecha_operacion`             AS `fecha`,
		`operaciones_recibos`.`numero_socio`                AS `socio`,
		`socios`.`nombre` ,
		`operaciones_recibos`.`docto_afectado`              AS `documento`,
		`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
		/*`operaciones_recibos`.`cheque_afectador`            AS `cheque`,*/
		`operaciones_recibos`.`tipo_pago`                   AS `forma_de_pago`,
		/*`operaciones_recibos`.`recibo_fiscal`,*/
		/*`operaciones_recibos`.`sucursal` ,*/
		`operaciones_recibos`.`total_operacion`             AS `total`,
		`operaciones_recibos`.`observacion_recibo` 
	FROM
		`operaciones_recibos` `operaciones_recibos` 
			INNER JOIN `socios` `socios` 
			ON `operaciones_recibos`.`numero_socio` = `socios`.`codigo` 
				INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
				ON `operaciones_recibostipo`.`idoperaciones_recibostipo` = 
				`operaciones_recibos`.`tipo_docto` 
					INNER JOIN `usuarios` `usuarios` 
					ON `operaciones_recibos`.`idusuario` = `usuarios`.`idusuarios`
				
	WHERE  `operaciones_recibos`.`idoperaciones_recibos` > 0
		$ByFecha
		$ByUsuario
		$ByTipoPago
		$ByTRecibo
		$ByEmpresa
		$ByEstad
	ORDER BY
		`operaciones_recibos`.`fecha_operacion`,
		`operaciones_recibos`.`tipo_pago`,
		`usuarios`.`idusuarios`,
		`operaciones_recibos`.`numero_socio`
	";


//exit($setSql);


$xRpt	= new cReportes($xHP->getTitle());
$xRpt->setOut($out);

if($cajero >0){
	$xCaj			= new cSystemUser($cajero);
	if($xCaj->init() == true){
		$titulo		= $titulo . " / " . $xCaj->getNombreCompleto();
	}
	$xRpt->setOmitir("usuario");
}

if ( $empresa >0 AND $empresa != DEFAULT_EMPRESA AND $empresa != FALLBACK_CLAVE_EMPRESA ){
	$xEmp			= new cEmpresas($empresa); $xEmp->init();

}


$xRpt->setTitle($titulo);

$xRpt->addContent( $xRpt->getEncabezado($titulo, $fechaInicial, $fechaFinal, $oficial ) );

$xRpt->setSQL($setSql);
$xRpt->addCampoSuma("total");
$xRpt->addCampoContar("numero");

$xRpt->setProcessSQL();


echo $xRpt->render(true);


?>