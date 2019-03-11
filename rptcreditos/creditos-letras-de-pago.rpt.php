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
$xHP		= new cHPage("TR.REPORTE DE LETRAS NO PAGADAS", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

	
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

$ByProducto		= $xFil->CreditosPorProducto($producto);
$ByPeriocidad	= $xFil->CreditosPorFrecuencia($frecuencia);

$ByFechas		= " AND (`tmp_creds_prox_letras`.`fecha_de_pago` >= '$FechaInicial' AND `tmp_creds_prox_letras`.`fecha_de_pago` <='$FechaFinal') ";
$sql			= "SELECT   `personas`.`codigo`,
         `personas`.`nombre`,
         `creditos_solicitud`.`numero_solicitud` AS `credito`,
		`creditos_solicitud`.`pagos_autorizados` AS `pagos`,
         `creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
         
		`creditos_solicitud`.`fecha_ministracion` AS `fecha_de_ministracion`,
		`creditos_solicitud`.`fecha_ultimo_mvto` AS `fecha_ultima_letra`,

		
		`creditos_solicitud`.`ultimo_periodo_afectado` AS `ultima_letra`,
		`tmp_creds_prox_letras`.`periodo_socio` AS `periodo`,
         `creditos_estatus`.`estatus_actual` AS `estatus_actual`,
         
         `creditos_tipo_de_pago`.`descripcion` AS `forma_de_pago`,
         ROUND((`creditos_solicitud`.`tasa_interes`*100),2) AS `tasa_interes`,
		ROUND((`creditos_solicitud`.`tasa_moratorio`*100),2) AS `tasa_mora`,

         `creditos_solicitud`.`monto_autorizado` AS `monto_original`,
         `creditos_solicitud`.`saldo_actual` AS `saldo_principal`,

		`tmp_creds_prox_letras`.`fecha_de_pago` AS `fecha_de_pago`,
         DATE_FORMAT(`tmp_creds_prox_letras`.`fecha_de_pago`, '%W') AS `dia_de_pago`,
         
         `tmp_creds_prox_letras`.`capital`,
         `tmp_creds_prox_letras`.`interes`,
         `tmp_creds_prox_letras`.`iva`,
         `tmp_creds_prox_letras`.`otros`,
         `tmp_creds_prox_letras`.`letra` AS `monto_letra`,
         `tmp_creds_prox_letras`.`saldo_principal` AS `saldo_letra`


         
         

FROM     `tmp_creds_prox_letras` 
INNER JOIN `creditos_solicitud`  ON `tmp_creds_prox_letras`.`docto_afectado` = `creditos_solicitud`.`numero_solicitud` 
INNER JOIN `personas`  ON `creditos_solicitud`.`numero_socio` = `personas`.`codigo` 
INNER JOIN `creditos_periocidadpagos`  ON `creditos_periocidadpagos`.`idcreditos_periocidadpagos` = `creditos_solicitud`.`periocidad_de_pago` 
INNER JOIN `creditos_estatus`  ON `creditos_estatus`.`idcreditos_estatus` = `creditos_solicitud`.`estatus_actual` 
INNER JOIN `creditos_tipo_de_pago`  ON `creditos_solicitud`.`tipo_de_dias_de_pago` = `creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` 
INNER JOIN `creditos_tipoconvenio`  ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio` = `creditos_solicitud`.`tipo_convenio` 
WHERE    ( `creditos_solicitud`.`saldo_actual` >0 ) $ByFechas $ByProducto $ByPeriocidad 
ORDER BY `creditos_solicitud`.`numero_solicitud`
";



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


$xRPT->setFormato("capital", $xRPT->FMT_MONEDA);
$xRPT->setFormato("interes", $xRPT->FMT_MONEDA);
$xRPT->setFormato("iva", $xRPT->FMT_MONEDA);
$xRPT->setFormato("monto_letra", $xRPT->FMT_MONEDA);
$xRPT->setFormato("saldo_principal", $xRPT->FMT_MONEDA);
$xRPT->setFormato("saldo_letra", $xRPT->FMT_MONEDA);
$xRPT->setFormato("monto_original", $xRPT->FMT_MONEDA);

$xRPT->setFormato("fecha_de_pago", $xRPT->FMT_FECHA);
$xRPT->setFormato("fecha_de_ministracion", $xRPT->FMT_FECHA);
$xRPT->setFormato("fecha_ultima_letra", $xRPT->FMT_FECHA);

$xRPT->setOmitir("codigo");



$xRPT->addCampoSuma("ahorro");
$xRPT->addCampoSuma("capital");
$xRPT->addCampoSuma("interes");
$xRPT->addCampoSuma("iva");
$xRPT->addCampoSuma("otros");
$xRPT->addCampoSuma("monto_letra");
$xRPT->addCampoSuma("saldo_principal");
$xRPT->addCampoSuma("saldo_letra");
$xRPT->addCampoSuma("monto_original");


$xRPT->setProcessSQL();
$xRPT->setResponse();
$xRPT->setSenders($senders);


echo $xRPT->render(true);

?>