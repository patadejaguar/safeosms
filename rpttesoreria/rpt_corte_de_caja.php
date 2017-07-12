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

$xF					= new cFecha();
$xSQL				= new cSQLListas();
//=====================================================================================================
$cajero 			= parametro("f3", getUsuarioActual(), MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);
$out				= parametro("out", OUT_HTML, MQL_RAW);
$mails				= getEmails($_REQUEST);
$empresa			= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$FechaInicial		= parametro("fechaMX", $xF->getFechaMinimaOperativa(), MQL_DATE);
$FechaInicial		= parametro("on", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal			= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$TipoDePago			= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW);
$operacion			= parametro("operacion", SYS_TODAS, MQL_INT); $operacion = parametro("tipodeoperacion", $operacion, MQL_INT);

$ByOperacion		= $xSQL->OFiltro()->OperacionesPorTipo($operacion);
$ByEmpresa			= $xSQL->OFiltro()->RecibosPorPersonaAsociada( $empresa);
$ByCajero			= $xSQL->OFiltro()->RecibosPorCajero($cajero);
//if(MODO_DEBUG == true){	$ByCajero		= ""; }
$ByFecha			= $xSQL->OFiltro()->OperacionesPorFecha($FechaInicial, $FechaFinal);
$ByTipoDePago		= $xSQL->OFiltro()->RecibosPorTipoDePago($TipoDePago);

$titulo				= $xHP->getTitle();

if ( $ByEmpresa ){
	$xEmp			= new cEmpresas($empresa); $xEmp->init();
	$titulo			= $titulo . " / " . $xEmp->getNombreCorto();
}
if($ByCajero != ""){
	$xCaj			= new cSystemUser($cajero);
	if($xCaj->init() == true){
		$titulo			= $titulo . " / " . $xCaj->getNombreCompleto();
	}
}
if($ByTipoDePago != ""){
	$xTipoP			= new cTesoreriaTiposDePagoCobro($TipoDePago);
	if($xTipoP->init() == true){
		$titulo			= $titulo . " / " . $xTipoP->getNombre();
	}
}
if($ByOperacion != ""){
	$xTipoO			= new cTipoDeOperacion($operacion);
	if($xTipoO ->init()){
		$titulo			= $titulo . " / " . $xTipoO->getNombre();
	}
}

$sql = " SELECT operaciones_recibos.tipo_pago AS 'tipo_de_pago',
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

				WHERE `operaciones_mvtos`.`tipo_operacion` !=0
				/*operaciones_tipos.afectacion_en_recibo!=0
				AND
				operaciones_mvtos.valor_afectacion!=0
				AND
				(operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion) != 0*/
				$ByCajero
	 			$ByFecha
				$ByEmpresa
				$ByTipoDePago
				$ByOperacion
			ORDER BY
				`operaciones_mvtos`.`sucursal`,
				`operaciones_mvtos`.`idusuario`,
				`operaciones_recibos`.`tipo_pago`,
				operaciones_mvtos.fecha_afectacion,
				`operaciones_recibos`.`idoperaciones_recibos`,
				`operaciones_mvtos`.`idoperaciones_mvtos`";
	//echo $sql; exit;

$xRPT		= new cReportes($titulo);
$xRPT->addContent($xRPT->getEncabezado($xRPT->getTitle(), $FechaInicial, $FechaFinal));

$xRPT->setSenders($mails);
$xRPT->setOut($out);
$xRPT->setConfig("CORTE-OPERACIONES");

$xRPT->setSQL($sql);
if($ByEmpresa != "" OR PERSONAS_CONTROLAR_POR_EMPRESA == false){ $xRPT->setOmitir("empresa"); }
if($ByCajero != ""){ $xRPT->setOmitir("cajero"); }
if($ByTipoDePago != ""){ $xRPT->setOmitir("tipo_de_pago"); }
$xRPT->addCampoSuma("monto");
$xRPT->setFormato("fecha", $xRPT->FMT_FECHA);
$xRPT->setToPrint();
$xRPT->setProcessSQL();





echo $xRPT->render(true);

?>