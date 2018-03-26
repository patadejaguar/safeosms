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
$xHP		= new cHPage("TR.INGRESOS DETALLADOS", HP_REPORT);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xV			= new cReglasDeValidacion();

ini_set("max_execution_time", 180);
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto, MQL_INT);
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

$TipoDePago		= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW); $TipoDePago	= parametro("pago", $TipoDePago, MQL_RAW);
$cajero 		= parametro("f3", getUsuarioActual(), MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);


$idmunicipio			= parametro("municipioactivo", "");
$ByMunicipio			= $xLi->OFiltro()->VSociosPorMunicipio($idmunicipio);


$titulo			= $xHP->getTitle();

$otros			= "";
if($TipoDePago != "" AND $TipoDePago != SYS_TODAS ){
	$titulo		= $titulo . " / $TipoDePago";	
}
if ( $empresa >0 AND $empresa != DEFAULT_EMPRESA AND $empresa != FALLBACK_CLAVE_EMPRESA ){
	$xEmp			= new cEmpresas($empresa); $xEmp->init();
	$titulo			= $titulo . " / " . $xEmp->getNombreCorto();
}
if($cajero >0){
	$xCaj			= new cSystemUser($cajero);
	if($xCaj->init() == true){
		$titulo		= $titulo . " / " . $xCaj->getNombreCompleto();
	}
	$otros			.= $xLi->OFiltro()->RecibosPorCajero($cajero);
}
if($producto > 0){
	$xProd		= new cProductoDeCredito($producto);
	if($xProd->init() == true){
		$titulo		= $titulo . " / " . $xProd->getNombre();
	}
}
$BySuc				= "";
if(MULTISUCURSAL == true){
	$BySuc			= $xLi->OFiltro()->VSociosPorSucursal($sucursal);
	$otros			.= $BySuc;
	if($BySuc !== ""){
		$titulo			= $titulo . " / Sucursal : $sucursal";
	}
}
if($ByMunicipio !== ""){
	$otros			.= $ByMunicipio;
	$xMun		= new cDomicilioMunicipio(); $xMun->initByIDUnico($idmunicipio);
	$municipio	= $xMun->getNombre();
	$entidadfed	= $xMun->getOEstado()->getNombre();
	$titulo		= $titulo . " / Municipio : $entidadfed - $municipio";
}

$xRPT			= new cReportes($titulo);
$xRPT->setFile($titulo);
$xRPT->setOut($out);
$xRPT->setTitle($titulo);
//============ Reporte
/*		`operaciones_recibos` `operaciones_recibos` 
			LEFT OUTER JOIN `tmp_recibos_datos_bancarios` `tmp_recibos_datos_bancarios` 
			ON `operaciones_recibos`.`idoperaciones_recibos` = 
			`tmp_recibos_datos_bancarios`.`recibo` 
				INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
				ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
				`idoperaciones_recibos` 
					INNER JOIN `creditos_solicitud` `creditos_solicitud` 
					ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
					`numero_solicitud` 
						INNER JOIN `socios` `socios` 
						ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
							INNER JOIN `operaciones_tipos` `operaciones_tipos` 
							ON `operaciones_mvtos`.`tipo_operacion` = 
							`operaciones_tipos`.`idoperaciones_tipos` 
								INNER JOIN 
								`eacp_config_bases_de_integracion_miembros` 
								`eacp_config_bases_de_integracion_miembros` 
								ON `operaciones_tipos`.`idoperaciones_tipos` = 
								`eacp_config_bases_de_integracion_miembros`.
								`miembro` 
									INNER JOIN `operaciones_recibostipo` 
									`operaciones_recibostipo` 
									ON `operaciones_recibos`.`tipo_docto` = 
									`operaciones_recibostipo`.
									`idoperaciones_recibostipo`*/
$sql			= $xLi->getListadoDeIngresos($FechaInicial, $FechaFinal, $TipoDePago, $empresa, $producto, false, $otros);
//setLog($sql);

$body		= $xRPT->getEncabezado($titulo, $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);

$xRPT->setPreSQL("CALL `proc_recs_datos_bancarios`");

$xRPT->setSQL($sql);

$xRPT->setConfig("OPERACIONES-INGRESOS");

$xRPT->addCampoSuma("capital");
$xRPT->addCampoSuma("interes_normal");
$xRPT->addCampoSuma("interes_moratorio");
$xRPT->addCampoSuma("iva");
$xRPT->addCampoSuma("otros");

if($TipoDePago != "" AND $TipoDePago != SYS_TODAS ){
	$xRPT->setOmitir("tipo_de_pago");
}
if ( $empresa >0 AND $empresa != DEFAULT_EMPRESA AND $empresa != FALLBACK_CLAVE_EMPRESA ){
	$xRPT->setOmitir("empresa");
}
$xRPT->setOmitir("clave_empresa");
$xRPT->setOmitir("clave_de_operacion");
$xRPT->setOmitir("indice");
$xRPT->setOmitir("producto");
$xRPT->setOmitir("periocidad");
$xRPT->setOmitir("oficial_de_credito");
$xRPT->setOmitir("oficial_de_seguimiento");
$xRPT->setOmitir("persona_asociada");
$xRPT->setFormato("fecha", $xRPT->FMT_FECHA);

$xRPT->setProcessSQL();
$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>