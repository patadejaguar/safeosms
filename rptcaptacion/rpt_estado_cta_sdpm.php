<?php
/**
 * Reporte de Estado de Cuenta en SDPM
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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

	
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
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

$ByFechas		= $xFil-> CaptacionSaldosPorFechas($FechaInicial, $FechaFinal);

$sql			= "SELECT
	/*`socios`.`codigo`,
	`socios`.`nombre`,
	`captacion_cuentas`.`numero_cuenta`                 AS `numero_de_cuenta`,
	`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
	`captacion_subproductos`.`descripcion_subproductos` AS `producto`,
	`captacion_cuentas`.`tasa_otorgada`,
	`captacion_cuentas`.`dias_invertidos`,
	`captacion_cuentas`.`saldo_cuenta`,
	`captacion_cuentas`.`sucursal`,*/
	`captacion_sdpm_historico`.`ejercicio`,
	`captacion_sdpm_historico`.`periodo`,
	`captacion_sdpm_historico`.`fecha`,
	`captacion_sdpm_historico`.`recibo`,
	`captacion_sdpm_historico`.`dias`,
	`captacion_sdpm_historico`.`tasa`,
	`captacion_sdpm_historico`.`monto`
FROM
	`captacion_cuentas` `captacion_cuentas`
		INNER JOIN `captacion_sdpm_historico` `captacion_sdpm_historico`
		ON `captacion_cuentas`.`numero_cuenta` = `captacion_sdpm_historico`.
		`cuenta`
			INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
			ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
			`idcaptacion_cuentastipos`
				INNER JOIN `socios` `socios`
				ON `socios`.`codigo` = `captacion_cuentas`.`numero_socio`
					INNER JOIN `captacion_subproductos` `captacion_subproductos`
					ON `captacion_cuentas`.`tipo_subproducto` =
					`captacion_subproductos`.`idcaptacion_subproductos`
WHERE
	(`captacion_cuentas`.`numero_cuenta` =$cuenta)
	$ByFechas
ORDER BY
	`socios`.`codigo`,
	`captacion_cuentas`.`numero_cuenta`,
	`captacion_sdpm_historico`.`ejercicio` ASC,
	`captacion_sdpm_historico`.`periodo` ASC,	
	`captacion_sdpm_historico`.`fecha` ASC";

$sql			= $xL->getListadoDeSDPMCaptacion($cuenta);

$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte


$xCta		= new cCuentaDeCaptacion($cuenta);
if($xCta->init() == true){
	$xRPT->addContent( $xCta->getFicha(false, "", true) );
}

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);



$xRPT->setOmitir("numero_de_socio");
$xRPT->setOmitir("cuenta");
$xRPT->setFormato("monto", $xRPT->FMT_MONEDA);
$xRPT->setFormato("fecha", $xRPT->FMT_FECHA);
$xRPT->addCampoSuma("monto");
$xRPT->addCampoContar("clave");

$xRPT->setProcessSQL();
//============ Agregar HTML


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>