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
$xHP		= new cHPage("TR.REPORTE DE OPERACIONES DE CAJA", HP_REPORT);
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
$estatus 			= parametro("estado", SYS_TODAS);
$frecuencia 		= parametro("periocidad", SYS_TODAS);
$producto 			= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);

$ByAll				= "";


$ByEmpresa			= $xSQL->OFiltro()->RecibosPorPersonaAsociada( $empresa);
$ByCajero			= $xSQL->OFiltro()->TesoreriaOperacionesPorCajero($cajero);
//if(MODO_DEBUG == true){	$ByCajero		= ""; }
$ByFecha			= $xSQL->OFiltro()->TesoreriaOperacionesPorFechas($FechaInicial, $FechaFinal);
$ByTipoDePago		= $xSQL->OFiltro()->RecibosPorTipoDePago($TipoDePago);

$titulo				= $xHP->getTitle();

if ( $ByEmpresa != "" ){
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




$ByCajero	= ($cajero == "" OR $cajero == SYS_TODAS) ? "" : " AND (`tesoreria_cajas_movimientos`.`idusuario` = $cajero) ";


$sql 	= "
SELECT
	`tesoreria_cajas_movimientos`.`fecha`,
	`tesoreria_cajas_movimientos`.`recibo`,
	`tesoreria_cajas_movimientos`.`tipo_de_exposicion`   AS `tipo_de_pago` ,
	
	`tesoreria_cajas_movimientos`.`monto_del_movimiento` AS `monto`,
	`tesoreria_cajas_movimientos`.`monto_recibido`       AS `recibido`,
	`tesoreria_cajas_movimientos`.`monto_en_cambio`      AS `cambio`,
	/*(CASE WHEN ( `tipo_de_exposicion` = 'transferencia') THEN (`tesoreria_cajas_movimientos`.`cuenta_bancaria`) ELSE NULL END) AS 'banco2', */

	/*`tesoreria_cajas_movimientos`.`banco`                AS `banco`,*/
	(CASE WHEN ( `tipo_de_exposicion` = 'foraneo' OR `tipo_de_exposicion` = '" . TESORERIA_PAGO_CHEQUE . "'
	OR `tipo_de_exposicion` = '" . TESORERIA_COBRO_TRANSFERENCIA . "'
 ) THEN CONCAT(`tesoreria_cajas_movimientos`.`numero_de_cheque`, '|', `tesoreria_cajas_movimientos`.`cuenta_bancaria`, '|',
 (SELECT `nombre_de_la_entidad` FROM `bancos_entidades`	WHERE `idbancos_entidades` = `tesoreria_cajas_movimientos`.`banco` LIMIT 0,1)) 
	ELSE '' END) AS 'datos'
FROM
	`tesoreria_cajas_movimientos`
WHERE `tesoreria_cajas_movimientos`.`idtesoreria_cajas_movimientos`>0
		$ByFecha
	
		$ByCajero
ORDER BY
	`tesoreria_cajas_movimientos`.`fecha`,
	`tesoreria_cajas_movimientos`.`tipo_de_exposicion`,
	`tesoreria_cajas_movimientos`.`recibo`,
	`tesoreria_cajas_movimientos`.`cuenta_bancaria`
	";

$xRPT		= new cReportes($titulo);
$xRPT->addContent($xRPT->getEncabezado($xRPT->getTitle(), $FechaInicial, $FechaFinal));
$xRPT->setSenders($mails);
$xRPT->setOut($out);
$xRPT->setConfig("CORTE-RECIBOS");

$xRPT->setSQL($sql);
//if($ByEmpresa != "" OR PERSONAS_CONTROLAR_POR_EMPRESA == false){ $xRPT->setOmitir("empresa"); }
if($ByCajero != ""){ $xRPT->setOmitir("cajero"); }
if($ByTipoDePago != ""){ $xRPT->setOmitir("tipo_de_pago"); }
$xRPT->addCampoSuma("monto");
$xRPT->setFormato("fecha", $xRPT->FMT_FECHA);

$xRPT->setToPrint();
$xRPT->setProcessSQL();
		
echo $xRPT->render(true);
		

?>