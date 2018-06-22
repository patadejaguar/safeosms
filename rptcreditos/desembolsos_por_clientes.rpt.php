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
$xHP		= new cHPage("TR.REPORTE DE MINISTRACION DE CREDITO", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();
$xRuls		= new cReglaDeNegocio();

$UseRecFechaR	= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_RPT_USE_FECHAREAL);

$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);
$destino		= parametro("destino", SYS_TODAS ,MQL_INT);
$omitirceros	= parametro("nocero", false ,MQL_BOOL);
$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
$ext			= parametro("ext", false, MQL_BOOL);
$ext			= parametro("otrosdatos", $ext, MQL_BOOL);
$tipopago		= parametro("tipodepago", SYS_TODAS, MQL_RAW);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false, MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false, MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$BySaldo		= ($omitirceros == true) ? $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">=") : "";
$ByFecha		= ($UseRecFechaR == true) ? $xFil->RecibosPorFechaDeRegistro($FechaInicial, $FechaFinal) : $xFil->CreditosPorFechaDeMinistracion($FechaInicial, $FechaFinal);
$ByConvenio		= $xFil->CreditosPorProducto($producto);
$ByEmpresa		= $xFil->CreditosPorEmpresa($empresa);
$ByPeriocidad	= $xFil->CreditosPorFrecuencia($frecuencia);
$ByTipoPago		= $xFil->RecibosPorTipoDePago($tipopago);
$ByDestino		= $xFil->CreditosPorDestino($destino);
$FEmpresa		= (PERSONAS_CONTROLAR_POR_EMPRESA == false) ? "" : "`personas`.`alias_dependencia`                                     AS `empresa`,";
$DExt			= ($ext == false) ? "" : "`personas`.`correo_electronico`,`personas`.`telefono`,";
$FProducto		= "`creditos_tipoconvenio`.`descripcion_tipoconvenio`                 AS `producto`,";
if($ByConvenio !== ""){
	$xCon	= new cProductoDeCredito($producto);
	$xCon->init();
	$xHP->setTitle($xHP->getTitle() . " - " . $xCon->getNombre() );
	$FProducto	= "";
}

$sql			= "SELECT
	`personas`.`codigo`,
	`personas`.`nombre`,
	$FEmpresa
	$DExt
	COUNT(`creditos_solicitud`.`numero_solicitud`)					AS `numero`,

	SUM(`creditos_solicitud`.`monto_solicitado`)					AS `monto_solicitado`,
	SUM(`creditos_solicitud`.`monto_autorizado`)                    AS `monto_ministrado`
 
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
		ON `creditos_solicitud`.`periocidad_de_pago` = 
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
			INNER JOIN `operaciones_recibos` `operaciones_recibos` 
			ON `creditos_solicitud`.`numero_solicitud` = `operaciones_recibos`.
			`docto_afectado` 
				INNER JOIN `oficiales` `oficiales` 
				ON `operaciones_recibos`.`idusuario` = `oficiales`.`id` 
					INNER JOIN `personas` `personas` 
					ON `creditos_solicitud`.`numero_socio` = `personas`.`codigo` 
						INNER JOIN `creditos_tipo_de_autorizacion` 
						`creditos_tipo_de_autorizacion` 
						ON `creditos_solicitud`.`tipo_autorizacion` = 
						`creditos_tipo_de_autorizacion`.
						`idcreditos_tipo_de_autorizacion` 
							INNER JOIN `creditos_tipoconvenio` 
							`creditos_tipoconvenio` 
							ON `creditos_solicitud`.`tipo_convenio` = 
							`creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
		WHERE creditos_solicitud.numero_socio > 0
		AND (`operaciones_recibos`.`tipo_docto` =" . RECIBOS_TIPO_MINISTRACION . ") 
		$ByFecha
		$ByConvenio
		$ByEmpresa
		$ByPeriocidad
		$ByTipoPago
		$ByDestino
		$BySaldo
		GROUP BY `creditos_solicitud`.`numero_socio`
		ORDER BY `personas`.`nombre`		
		";

$titulo			= $xHP->getTitle();
$archivo		= "$titulo.pdf";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte

$xRPT->addCampoSuma("numero");
$xRPT->addCampoContar("codigo");

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);
$xRPT->setSQL($sql);
$xRPT->addCampoSuma("monto_solicitado");
$xRPT->addCampoSuma("monto_ministrado");

$xRPT->setOut($out);


$xRPT->setKeyUnique("codigo");


$xRPT->setProcessSQL();

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);


?>