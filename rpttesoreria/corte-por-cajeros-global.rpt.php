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
$xHP		= new cHPage("TR.REPORTE GLOBAL POR PRODUCTO Y POR FORMA DE PAGO", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();
$xRuls		= new cReglaDeNegocio();
$xQL		= new MQL();

	
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

$arrCajero		= parametro("idcajero_array", array(), MQL_ARR_INT);
$ByCajero		= "";

foreach ($arrCajero as $idx => $v){
	if($v>0){
		$ByCajero	.= ($ByCajero == "") ? " `operaciones_recibos`.`idusuario`= $v " : " OR `operaciones_recibos`.`idusuario`= $v ";
	}
}
$ByCajero		= ($ByCajero == "") ? "" : " AND ($ByCajero) ";

$UsarFechaR		= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_RPT_USE_FECHAREAL);

$ByFechas		= $xFil->RecibosPorFechaDeRegistro($FechaInicial, $FechaFinal);
if($UsarFechaR == false){
	$ByFechas	= $xFil->RecibosPorFecha($FechaInicial, $FechaFinal);
}
$ByTipoPago		= $xFil->RecibosPorTipoDePago($TipoDePago);
$ByEmpresa		= $xFil->RecibosPorPersonaAsociada($empresa);
$ByProducto		= $xFil->CreditosPorProducto($producto);

$ByFechaOp		= $xFil->OperacionesPorFecha($FechaInicial, $FechaFinal);

$xTPag			= new cTesoreriaTiposDePagoCobro();


$strOmitO		= "'" . $xTPag->INGRESO_EFECTIVO . "," . $xTPag->INGRESO_TDEBITO . "," . $xTPag->INGRESO_TCREDITO . ",transferencia,dpn50,dpn21'";
$strBanc		= "'" . $xTPag->INGRESO_TDEBITO.  "," . $xTPag->INGRESO_TCREDITO .  "'";

$strCbza		= "'145,146,147'";
$strMora		= "'141,142,143,148'";
$strComp		= "'145,146,147,141,142,143,148'";
$sql			= "SELECT

`operaciones_mvtos`.`fecha_operacion`        AS `fecha`,

       `vw_documentos`.`tipo`,
		`vw_documentos`.`producto`,
		

	`personas`.`dependencia`                       AS `empresa`,
	`usuarios`.`alias` 								AS `cajero`,

	`personas`.`codigo`                           	AS `codigo`,
	SUBSTR(`personas`.`nombre`,1,30)                            	AS `nombre`,

	`vw_documentos`.`documento`,
 	`operaciones_mvtos`.`recibo_afectado`			AS `recibo`,
	
	`operaciones_mvtos`.`periodo_socio`          AS `parcialidad`,
	
	SUM(IF(`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2003,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `capital`,
	SUM(IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2110 OR `eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2210),`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `interes`,
	SUM(IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 10001 AND FIND_IN_SET(`operaciones_mvtos`.`tipo_operacion`, $strMora)>0 ),`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `mora`,		
	SUM(IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 10001 AND FIND_IN_SET(`operaciones_mvtos`.`tipo_operacion`, $strCbza)>0 ),`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `gastos_cbza`,
	SUM(IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 10001 AND FIND_IN_SET(`operaciones_mvtos`.`tipo_operacion`, $strComp)<=0),`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `otros`,
	SUM(IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 7021),`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `iva`,
		
	SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS `total`,

	SUM(IF(`operaciones_recibos`.`tipo_pago` ='" . $xTPag->INGRESO_EFECTIVO . "',`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `efectivo`,
	SUM(IF(`operaciones_recibos`.`tipo_pago` ='transferencia',`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `transferencia`,

	SUM(IF(FIND_IN_SET(`operaciones_recibos`.`tipo_pago`,$strBanc)>0,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `tarjeta`,

	SUM(IF(`operaciones_recibos`.`tipo_pago` ='dpn50',`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `dpn50`,
	SUM(IF(`operaciones_recibos`.`tipo_pago` ='dpn21',`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `dpn21`,


	SUM(IF(FIND_IN_SET(`operaciones_recibos`.`tipo_pago`,$strOmitO)>0,0,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)) AS `pago_otros`,
	`operaciones_recibos`.`tipo_pago` AS `tipo_pago`
	
FROM     `operaciones_recibos`
 
LEFT OUTER JOIN `tmp_recibos_datos_bancarios`  ON `operaciones_recibos`.`idoperaciones_recibos` = `tmp_recibos_datos_bancarios`.`recibo` 
INNER JOIN `operaciones_mvtos`  ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.`idoperaciones_recibos` 
INNER JOIN `personas`  ON `operaciones_recibos`.`numero_socio` = `personas`.`codigo` 
INNER JOIN `eacp_config_bases_de_integracion_miembros`  ON `eacp_config_bases_de_integracion_miembros`.`miembro` = `operaciones_mvtos`.`tipo_operacion` 
INNER JOIN `operaciones_recibostipo`  ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.`idoperaciones_recibostipo` 
INNER JOIN `vw_documentos`  ON `vw_documentos`.`documento` = `operaciones_mvtos`.`docto_afectado` 
INNER JOIN `usuarios`  ON `operaciones_recibos`.`idusuario` = `usuarios`.`idusuarios` 
	
	
	WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 10001)
	AND (`operaciones_recibostipo`.`mostrar_en_corte` <> '0')
	$ByFechas
	$ByTipoPago
	$ByEmpresa
	$ByCajero
	AND (`vw_documentos`.`tipo`  = <1>) AND (`vw_documentos`.`producto` = <2>)
	GROUP BY  `operaciones_mvtos`.`recibo_afectado`
	ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,`vw_documentos`.`tipo`, `vw_documentos`.`producto`,`operaciones_mvtos`.`fecha_operacion`,`operaciones_mvtos`.`recibo_afectado`
";

//exit($sql);

$sum_cap		= 0;
$sum_int		= 0;
$sum_otr		= 0;
$sum_pag		= 0;

$sum_efv		= 0;
$sum_tar		= 0;
$sum_opg		= 0;

$sum_dpn50		= 0;
$sum_dpn21		= 0;
$sum_trans		= 0;
$sum_iva		= 0;
$sum_gtos		= 0;
$sum_mora		= 0;

	
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

//Creditos
$rsProd		= $xQL->getDataRecord("SELECT * FROM `creditos_tipoconvenio` WHERE `idcreditos_tipoconvenio` != 99 ORDER BY `descripcion_tipoconvenio`");
foreach ($rsProd as $rw){
	$idproducto	= $rw["idcreditos_tipoconvenio"];
	$nombreprod	= $rw["descripcion_tipoconvenio"];
	$sqlx		= str_replace("<1>", iDE_CREDITO, $sql);
	$sqlx		= str_replace("<2>", $idproducto, $sqlx);
	
	//setLog($sqlx);
	
	$xTable		= new cTabla($sqlx,3);
	$xTable->setNoFieldset();
	$xTable->setUsarNullPorCero();
	$xTable->setKeyField("recibo");
	$xTable->setKeyTable("operaciones_recibos");
	
	$xTable->setOmitidos("tipo");
	$xTable->setOmitidos("producto");
	$xTable->setOmitidos("empresa");
	$xTable->setOmitidos("parcialidad");
	$xTable->setOmitidos("codigo");
	$xTable->setOmitidos("documento");
	
	
	$xTable->setColTitle("interes_normal", "Interes");
	$xTable->setColTitle("interes_moratorio", "Mora");
	$xTable->setColTitle("gastos_cbza", "Cobranza");
	
	$xTable->setColStyle("fecha", "width:6%");
	$xTable->setColStyle("cajero", "width:6%");
	$xTable->setColStyle("nombre", "width:12%");
	$xTable->setColStyle("recibo", "width:6%");
	$xTable->setColStyle("mora", "width:6%");
	
	
	$xTable->setColStyle("codigo", "width:6%");
	$xTable->setColStyle("documento", "width:6%");
	
	
	$xTable->setColStyle("capital", "width:6%");
	$xTable->setColStyle("interes", "width:6%");
	$xTable->setColStyle("gastos_cbza", "width:6%");
	$xTable->setColStyle("iva", "width:6%");
	
	$xTable->setColStyle("otros", "width:6%");
	$xTable->setColStyle("total", "width:6%");
	
	$xTable->setColStyle("efectivo", "width:6%");
	$xTable->setColStyle("tarjeta", "width:6%");
	$xTable->setColStyle("pago_otros", "width:6%");
	
	$xTable->setColStyle("transferencia", "width:6%");
	$xTable->setColStyle("dpn50", "width:6%");
	$xTable->setColStyle("dpn21", "width:6%");
	$xTable->setColStyle("tipo_pago", "width:5%");
	
	
	$xTable->setFootSum(array(
			4 => "capital",
			5 => "interes",
			6 => "mora",
			7 => "gastos_cbza",
			8 => "otros",
			9 => "iva",
			10 => "total",
			11 => "efectivo",
			12 => "transferencia",
			13 => "tarjeta",
			14 => "dpn50",
			15 => "dpn21",
			16 => "pago_otros"
	));
	
	$cnt		= $xTable->Show($nombreprod);
	
	if($xTable->getRowCount()>0){
		$xRPT->addContent("<table><tr><th class='title' style='text-align:left;'>$nombreprod</th></tr></table>");
		$xRPT->addContent($cnt);
		$sum_cap		+= $xTable->getFieldsSum("capital");
		$sum_int		+= $xTable->getFieldsSum("interes");
		$sum_otr		+= $xTable->getFieldsSum("otros");
		
		$sum_pag		+= $xTable->getFieldsSum("total");
		
		$sum_efv		+= $xTable->getFieldsSum("efectivo");
		$sum_tar		+= $xTable->getFieldsSum("tarjeta");
		$sum_opg		+= $xTable->getFieldsSum("pago_otros");
		
		$sum_trans		+= $xTable->getFieldsSum("transferencia");
		$sum_dpn50		+= $xTable->getFieldsSum("dpn50");
		$sum_dpn21		+= $xTable->getFieldsSum("dpn51");
		
		$sum_iva		+= $xTable->getFieldsSum("iva");
		$sum_gtos		+= $xTable->getFieldsSum("gastos_cbza");
		$sum_mora		+= $xTable->getFieldsSum("mora");
	}
}




//Captacion

//Sin Producto


$idproducto	= 99;
$nombreprod	= "OTROS INGRESOS";
$sqlx		= str_replace("<1>", iDE_CREDITO, $sql);
$sqlx		= str_replace("<2>", $idproducto, $sqlx);



$xTable		= new cTabla($sqlx,3);
$xTable->setNoFieldset();
$xTable->setUsarNullPorCero();
$xTable->setKeyField("recibo");
$xTable->setKeyTable("operaciones_recibos");

$sqllim	= "SELECT `operaciones_mvtos`.`recibo_afectado` AS `recibo`, `operaciones_tipos`.`descripcion_operacion` AS `operacion`,
`operaciones_mvtos`.`afectacion_real` AS `monto`
FROM     `operaciones_mvtos`
INNER JOIN `operaciones_tipos`  ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.`idoperaciones_tipos`
WHERE `operaciones_mvtos`.`afectacion_real` > 0 $ByFechaOp  AND (`operaciones_tipos`.`recibo_que_afecta`= 99) LIMIT 0,1000";


$xTable->addSubQuery($sqllim, "recibo", "{{operacion}} por $ {{monto}};");

$xTable->setOmitidos("tipo");
$xTable->setOmitidos("producto");
$xTable->setOmitidos("empresa");
$xTable->setOmitidos("parcialidad");

$xTable->setOmitidos("documento");
$xTable->setOmitidos("codigo");

$xTable->setColTitle("interes_normal", "Interes");
$xTable->setColTitle("interes_moratorio", "Mora");
$xTable->setColTitle("gastos_cbza", "Cobranza");


$xTable->setColStyle("fecha", "width:6%");
$xTable->setColStyle("cajero", "width:6%");
$xTable->setColStyle("nombre", "width:12%");
$xTable->setColStyle("recibo", "width:6%");
$xTable->setColStyle("codigo", "width:6%");
$xTable->setColStyle("documento", "width:6%");


$xTable->setColStyle("capital", "width:6%");
$xTable->setColStyle("interes", "width:6%");
$xTable->setColStyle("mora", "width:6%");
$xTable->setColStyle("gastos_cbza", "width:6%");
$xTable->setColStyle("iva", "width:6%");

$xTable->setColStyle("otros", "width:6%");
$xTable->setColStyle("total", "width:6%");

$xTable->setColStyle("efectivo", "width:6%");
$xTable->setColStyle("tarjeta", "width:6%");
$xTable->setColStyle("pago_otros", "width:6%");

$xTable->setColStyle("transferencia", "width:6%");
$xTable->setColStyle("dpn50", "width:6%");
$xTable->setColStyle("dpn21", "width:6%");
$xTable->setColStyle("tipo_pago", "width:5%");


$xTable->setFootSum(array(
		4 => "capital",
		5 => "interes",
		6 => "mora",
		7 => "gastos_cbza",
		8 => "otros",
		9 => "iva",
		10 => "total",
		11 => "efectivo",
		12 => "transferencia",
		13 => "tarjeta",
		14 => "dpn50",
		15 => "dpn21",
		16 => "pago_otros"
));

$cnt		= $xTable->Show();

if($xTable->getRowCount()>0){
	$xRPT->addContent("<table><tr><th class='title' style='text-align:left;'>$nombreprod</th></tr></table>");
	$xRPT->addContent($cnt);
	
	$sum_cap		+= $xTable->getFieldsSum("capital");
	$sum_int		+= $xTable->getFieldsSum("interes");
	$sum_otr		+= $xTable->getFieldsSum("otros");
	$sum_pag		+= $xTable->getFieldsSum("total");
	
	$sum_efv		+= $xTable->getFieldsSum("efectivo");
	$sum_tar		+= $xTable->getFieldsSum("tarjeta");
	$sum_opg		+= $xTable->getFieldsSum("pago_otros");
	
	$sum_trans		+= $xTable->getFieldsSum("transferencia");
	$sum_dpn50		+= $xTable->getFieldsSum("dpn50");
	$sum_dpn21		+= $xTable->getFieldsSum("dpn51");
	$sum_iva		+= $xTable->getFieldsSum("iva");
	$sum_gtos		+= $xTable->getFieldsSum("gastos_cbza");
	$sum_mora		+= $xTable->getFieldsSum("mora");
}


//Agregar sumas


$tt	= "<tr>
<th style='width:6%'></th>
<th style='width:6%'></th>
<th style='width:12%'>SUMA TOTAL</th>
<th style='width:6%'></th>

<th style='width:6%'><mark id='sum-capital'>" . getFMoney($sum_cap) . "</mark></th>
<th style='width:6%'><mark id='sum-interes'>" . getFMoney($sum_int) . "</mark></th>
<th style='width:6%'><mark id='sum-mora'>" . getFMoney($sum_mora) . "</mark></th>

<th style='width:6%'><mark id='sum-gtos'>" . getFMoney($sum_gtos) . "</mark></th>
<th style='width:6%'><mark id='sum-otros'>" . getFMoney($sum_otr) . "</mark></th>
<th style='width:6%'><mark id='sum-iva'>" . getFMoney($sum_iva) . "</mark></th>
<th style='width:6%'><mark id='sum-total'>" . getFMoney($sum_pag) . "</mark></th>

<th style='width:6%'><mark id='sum-efectivo'>" . getFMoney($sum_efv) . "</mark></th>
<th style='width:6%'><mark id='sum-trans'>" . getFMoney($sum_trans) . "</mark></th>

<th style='width:6%'><mark id='sum-tarjeta'>" . getFMoney($sum_tar) . "</mark></th>
<th style='width:6%'><mark id='sum-dpn50'>" . getFMoney($sum_dpn50) . "</mark></th>
<th style='width:6%'><mark id='sum-dpn21'>" . getFMoney($sum_dpn21) . "</mark></th>
<th style='width:6%'><mark id='sum-pago_otros'>" . getFMoney($sum_opg) . "</mark></th>
<th style='width:5%'>****</th>
</tr>";

$xRPT->addContent("<table class='listado'>$tt</table>");


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>