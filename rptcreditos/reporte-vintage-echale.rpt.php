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
$xHP		= new cHPage("TR.REPORTE VINTAGE ECHALE", HP_REPORT);
$xLi			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

//$xLayout	= new cReportes_Layout();
	
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$TipoDePago		= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW); $TipoDePago	= parametro("pago", $TipoDePago, MQL_RAW);
$TipoDeRecibo	= parametro("tipoderecibo", 0, MQL_INT); $TipoDeRecibo = parametro("tiporecibo", $TipoDeRecibo, MQL_INT);

$cajero 		= parametro("f3", 0, MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT); $operacion = parametro("tipodeoperacion", $operacion, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$tipoautorizacion		= parametro("tipoautorizacion", SYS_TODAS, MQL_INT);
$destino				= parametro("destino", SYS_TODAS ,MQL_INT);



$ByProducto				= $xLi->OFiltro()->CreditosPorProducto($producto);
$BySucursal				= $xLi->OFiltro()->CreditosPorSucursal($sucursal);

$idmunicipio			= parametro("municipioactivo", "");
$ByMunicipio			= $xLi->OFiltro()->CreditosPorMunicipioAct($idmunicipio);

$titulo					= $xHP->getTitle();


$ByFecha				= $xLi->OFiltro()->CreditosPorFechaDeMinistracion($FechaInicial, $FechaFinal);
$ByTipoAut				= $xLi->OFiltro()->CreditosPorAutorizacion($tipoautorizacion);
$ByDestino				= $xLi->OFiltro()->CreditosPorDestino($destino);

/*
NUMERO DE SOCIO/CLIENTE
NOMBRE
CURP
RFC
ESTADO
MUNICIPIO
LOCALIDAD
SUCURSAL
NOMBRE DEL PRODUCTO
FECHA DE OTORGAMIENTO
FECHA DE VENCIMIENTO
MONTO ORIGINAL
PLAZO DEL CREDITO
FRECUENCIA DE PAGO
TASA
 CAPITAL VIGENTE 
 CAPITAL VENCIDO 
INTERESES VIGENTES
INTERESES VENCIDOS
INTERESES MORATORIOS
"SALDO 
INSOLUTO TOTAL"
FECHA ULTIMO PAGO DE CAPITAL
MONTO ULTIMO PAGO DE CAPITAL
FECHA ULTIMO PAGO DE INTERESES
 MONTO ULTIMO PAGO DE INTERESES 
CLASIFICACION DE CARTERA (VIGENTE, VENCIDA)
TIPO DE CREDITO (NORMAL, REESTRUCTURADO, RENOVADO)
DIAS DE MORA
*/
//$xQL->setCall("proc_creditos_letras_del_dia");
//$xQL->setCall("proc_creditos_abonos_parciales");
//$xQL->setCall("proc_recibos_distrib");

$sql	= "SELECT   `socios_general`.`codigo`,
		IF(`socios_figura_juridica`.`tipo_de_integracion` = " . PERSONAS_ES_MORAL . ", `socios_general`.`nombrecompleto` ,
		TRIM(CONCAT(`socios_general`.`apellidopaterno`,' ',`socios_general`.`apellidomaterno`,' ',`socios_general`.`nombrecompleto`)))
		AS `nombre`,

         `socios_general`.`curp`,
         `socios_general`.`rfc`,

         sv.`estado`,
         sv.`municipio`,
         sv.`localidad`,
         `general_sucursales`.`nombre_sucursal` 							AS `sucursal`,
		`creditos_solicitud`.`numero_solicitud` 							AS `credito`,

         `creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,
         `creditos_solicitud`.`fecha_ministracion`,
         `creditos_solicitud`.`fecha_vencimiento`,
         `creditos_solicitud`.`monto_autorizado`,
         CEIL((`creditos_solicitud`.`plazo_en_dias`/30.4166666666666)) 		AS `plazo`,
         `creditos_periocidadpagos`.`descripcion_periocidadpagos` 			AS `periodicidad`,
         ROUND((`creditos_solicitud`.`tasa_interes`*100),2)					AS `tasa`,
		 
         setNoMenorCero( (`creditos_solicitud`.`saldo_actual`-`creditos_montos`.`capital_exigible`) ) AS `capital_vigente`,
		`creditos_montos`.`capital_exigible` 								AS `capital_vencido`,

		(`ints_tot_calc`) 	AS `interes_vigente`,
		setNoMenorCero(`creditos_montos`.`interes_n_dev` - `creditos_montos`.`interes_n_pag`) 	AS `interes_vencido`,
		
        `creditos_montos`.`interes_m_dev`									AS `interes_moratorio`,

         `creditos_solicitud`.`fecha_ultimo_capital` 						AS `fecha_ult_pago_cap`,
         `creditos_solicitud`.`fecha_ultimo_mvto` 							AS `fecha_ult_pago_int`,
         CONCAT(`creditos_tipo_de_autorizacion`.`descripcion_tipo_de_autorizacion`, ' ', `creditos_estatus`.`descripcion_estatus`) 							AS `clasificacion`,

         `creditos_tipo_de_autorizacion`.`descripcion_tipo_de_autorizacion` AS `tipo_autorizacion`
FROM     `socios_general` 
INNER JOIN `socios_figura_juridica`  ON `socios_general`.`personalidad_juridica` = `socios_figura_juridica`.`idsocios_figura_juridica`
INNER JOIN `general_sucursales`  ON `socios_general`.`sucursal` = `general_sucursales`.`codigo_sucursal` 
INNER JOIN `creditos_solicitud`  ON `socios_general`.`codigo` = `creditos_solicitud`.`numero_socio` 
INNER JOIN `creditos_tipoconvenio`  ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
INNER JOIN `creditos_periocidadpagos`  ON `creditos_solicitud`.`periocidad_de_pago` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
INNER JOIN `creditos_estatus`  ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.`idcreditos_estatus` 
INNER JOIN `creditos_tipo_de_autorizacion`  ON `creditos_solicitud`.`tipo_autorizacion` = `creditos_tipo_de_autorizacion`.`idcreditos_tipo_de_autorizacion` 
INNER JOIN `creditos_montos`  ON `creditos_solicitud`.`numero_solicitud` = `creditos_montos`.`clave_de_credito`
LEFT OUTER JOIN
(
SELECT   `tmp_personas_domicilios`.`codigo` AS `persona_id`,
         `tmp_personas_domicilios`.`nestado` AS `estado`,
         `tmp_personas_domicilios`.`nmunicipio` AS `municipio`,
		 `tmp_personas_domicilios`.`nlocalidad` AS `localidad`
FROM     `tmp_personas_domicilios`
) sv ON `socios_general`.`codigo` = sv.`persona_id`  
WHERE `creditos_solicitud`.`saldo_actual`> " . TOLERANCIA_SALDOS . "
$ByProducto
$BySucursal
$ByTipoAut
";
//echo $sql;
$titulo			= $xHP->getTitle();
$archivo		= $xHP->getTitle();



$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);


$xRPT->setFormato("fecha_ult_pago_cap", $xRPT->FMT_FECHA);
$xRPT->setFormato("fecha_ult_pago_int", $xRPT->FMT_FECHA);

$xRPT->setFormato("fecha_ministracion", $xRPT->FMT_FECHA);
$xRPT->setFormato("fecha_vencimiento", $xRPT->FMT_FECHA);

$xRPT->setFormato("monto_autorizado", $xRPT->FMT_MONEDA);

$xRPT->setFormato("capital_vigente", $xRPT->FMT_MONEDA);
$xRPT->setFormato("capital_vencido", $xRPT->FMT_MONEDA);

$xRPT->setFormato("interes_vigente", $xRPT->FMT_MONEDA);
$xRPT->setFormato("interes_vencido", $xRPT->FMT_MONEDA);
$xRPT->setFormato("interes_moratorio", $xRPT->FMT_MONEDA);


$xRPT->addCampoSuma("monto_autorizado");

$xRPT->addCampoSuma("capital_vigente");
$xRPT->addCampoSuma("capital_vencido");

$xRPT->addCampoSuma("interes_vigente");
$xRPT->addCampoSuma("interes_vencido");
$xRPT->addCampoSuma("interes_moratorio");

//$xRPT->setFormato("", $xRPT->FMT_MONEDA);


$xRPT->setProcessSQL();

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>