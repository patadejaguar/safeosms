<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
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
$xHP		= new cHPage("TR.DATOS DE PRODUCTOS DE CREDITOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");
$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

$sql = $xLi->getListadoDeProductosCred();

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivprodc",$xHP->getTitle());

$xHG->setSQL($sql);
$xHG->addList();
$xHG->setOrdenar();

$xHG->addKey("codigo");

$xHG->col("codigo", "TR.CLAVE", "5%");
$xHG->col("nombre", "TR.NOMBRE", "10%");

//$xHG->col("tasa_ahorro", "TR.TASA AHORRO", "10%");
//$xHG->col("tipo_convenio", "TR.TIPO CONVENIO", "10%");
//$xHG->col("razon_garantia", "TR.RAZON GARANTIA", "10%");

$xHG->col("alias", "TR.ALIAS", "10%");


//$xHG->col("dias_maximo", "TR.DIAS MAXIMO", "10%");
//$xHG->col("pagos_maximo", "TR.PAGOS MAXIMO", "10%");

$xHG->col("tasa_normal", "TR.INTERES NORMAL", "10%");
$xHG->col("tasa_moratorio", "TR.INTERES MORATORIO", "10%");



/*$xHG->col("creditos_mayores_a", "TR.CREDITOS MAYORES A", "10%");
$xHG->col("porciento_garantia_liquida", "TR.PORCIENTO GARANTIA LIQUIDA", "10%");
$xHG->col("monto_fondo_obligatorio", "TR.MONTO FONDO OBLIGATORIO", "10%");
$xHG->col("porcentaje_otro_credito", "TR.PORCENTAJE OTRO CREDITO", "10%");
$xHG->col("aplica_gastos_notariales", "TR.APLICA GASTOS NOTARIALES", "10%");
$xHG->col("numero_creditos_maximo", "TR.NUMERO CREDITOS MAXIMO", "10%");

$xHG->col("tipo_autorizacion", "TR.TIPO AUTORIZACION", "10%");
$xHG->col("nivel_riesgo", "TR.NIVEL RIESGO", "10%");
$xHG->col("porcentaje_ica", "TR.PORCENTAJE ICA", "10%");
$xHG->col("estatus_predeterminado", "TR.ESTATUS PREDETERMINADO", "10%");
$xHG->col("leyenda_docto_autorizacion", "TR.LEYENDA DOCTO AUTORIZACION", "10%");

$xHG->col("tolerancia_dias_no_pago", "TR.TOLERANCIA DIAS NO PAGO", "10%");
$xHG->col("maximo_otorgable", "TR.MAXIMO OTORGABLE", "10%");
$xHG->col("tolerancia_dias_primer_abono", "TR.TOLERANCIA DIAS PRIMER ABONO", "10%");
$xHG->col("numero_avales", "TR.NUMERO AVALES", "10%");
$xHG->col("nivel_autorizacion_oficial", "TR.NIVEL AUTORIZACION OFICIAL", "10%");
$xHG->col("code_valoracion_javascript", "TR.CODE VALORACION JAVASCRIPT", "10%");
$xHG->col("minimo_otorgable", "TR.MINIMO OTORGABLE", "10%");
$xHG->col("descripcion_completa", "TR.DESCRIPCION COMPLETA", "10%");
$xHG->col("oficial_seguimiento", "TR.OFICIAL SEGUIMIENTO", "10%");
$xHG->col("valoracion_php", "TR.VALORACION PHP", "10%");
$xHG->col("tipo_de_credito", "TR.TIPO DE CREDITO", "10%");
$xHG->col("php_monto_maximo", "TR.PHP MONTO MAXIMO", "10%");
$xHG->col("tipo_de_convenio", "TR.TIPO DE CONVENIO", "10%");
$xHG->col("tipo_de_garantia", "TR.TIPO DE GARANTIA", "10%");
$xHG->col("estatus", "TR.ESTATUS", "10%");
$xHG->col("tasa_iva", "TR.TASA IVA", "10%");
$xHG->col("contable_cartera_vigente", "TR.CONTABLE CARTERA VIGENTE", "10%");
$xHG->col("contable_cartera_vencida", "TR.CONTABLE CARTERA VENCIDA", "10%");
$xHG->col("contable_intereses_devengados", "TR.CONTABLE INTERESES DEVENGADOS", "10%");
$xHG->col("contable_intereses_anticipados", "TR.CONTABLE INTERESES ANTICIPADOS", "10%");
$xHG->col("contable_intereses_cobrados", "TR.CONTABLE INTERESES COBRADOS", "10%");
$xHG->col("contable_intereses_moratorios", "TR.CONTABLE INTERESES MORATORIOS", "10%");
$xHG->col("iva_incluido", "TR.IVA INCLUIDO", "10%");
$xHG->col("comision_por_apertura", "TR.COMISION POR APERTURA", "10%");
$xHG->col("codigo_de_contrato", "TR.CODIGO DE CONTRATO", "10%");
$xHG->col("contable_cartera_castigada", "TR.CONTABLE CARTERA CASTIGADA", "10%");
$xHG->col("path_del_contrato", "TR.PATH DEL CONTRATO", "10%");
$xHG->col("tipo_de_integracion", "TR.TIPO DE INTEGRACION", "10%");
$xHG->col("contable_intereses_vencidos", "TR.CONTABLE INTERESES VENCIDOS", "10%");
$xHG->col("base_de_calculo_de_interes", "TR.BASE DE CALCULO DE INTERES", "10%");
$xHG->col("capital_vencido_renovado", "TR.CAPITAL VENCIDO RENOVADO", "10%");
$xHG->col("capital_vencido_reestructurado", "TR.CAPITAL VENCIDO REESTRUCTURADO", "10%");
$xHG->col("capital_vencido_normal", "TR.CAPITAL VENCIDO NORMAL", "10%");
$xHG->col("capital_vigente_renovado", "TR.CAPITAL VIGENTE RENOVADO", "10%");
$xHG->col("capital_vigente_reestructurado", "TR.CAPITAL VIGENTE REESTRUCTURADO", "10%");
$xHG->col("capital_vigente_normal", "TR.CAPITAL VIGENTE NORMAL", "10%");
$xHG->col("interes_cobrado", "TR.INTERES COBRADO", "10%");
$xHG->col("moratorio_cobrado", "TR.MORATORIO COBRADO", "10%");
$xHG->col("interes_vencido_renovado", "TR.INTERES VENCIDO RENOVADO", "10%");
$xHG->col("interes_vencido_reestructurado", "TR.INTERES VENCIDO REESTRUCTURADO", "10%");
$xHG->col("interes_vencido_normal", "TR.INTERES VENCIDO NORMAL", "10%");
$xHG->col("interes_vigente_renovado", "TR.INTERES VIGENTE RENOVADO", "10%");
$xHG->col("interes_vigente_reestructurado", "TR.INTERES VIGENTE REESTRUCTURADO", "10%");
$xHG->col("interes_vigente_normal", "TR.INTERES VIGENTE NORMAL", "10%");
$xHG->col("tipo_de_interes", "TR.TIPO DE INTERES", "10%");
$xHG->col("aplica_mora_por_cobranza", "TR.APLICA MORA POR COBRANZA", "10%");
$xHG->col("pre_modificador_de_interes", "TR.PRE MODIFICADOR DE INTERES", "10%");
$xHG->col("pos_modificador_de_interes", "TR.POS MODIFICADOR DE INTERES", "10%");
$xHG->col("pre_modificador_de_ministracion", "TR.PRE MODIFICADOR DE MINISTRACION", "10%");
$xHG->col("pre_modificador_de_autorizacion", "TR.PRE MODIFICADOR DE AUTORIZACION", "10%");
$xHG->col("pre_modificador_de_vencimiento", "TR.PRE MODIFICADOR DE VENCIMIENTO", "10%");
$xHG->col("pre_modificador_de_solicitud", "TR.PRE MODIFICADOR DE SOLICITUD", "10%");
$xHG->col("clave_de_tipo_de_producto", "TR.CLAVE DE TIPO DE PRODUCTO", "10%");
$xHG->col("perfil_de_interes", "TR.PERFIL DE INTERES", "10%");
$xHG->col("fuente_de_fondeo_predeterminado", "TR.FUENTE DE FONDEO PREDETERMINADO", "10%");
$xHG->col("tipo_de_periocidad_preferente", "TR.TIPO DE PERIOCIDAD PREFERENTE", "10%");
$xHG->col("numero_de_pagos_preferente", "TR.NUMERO DE PAGOS PREFERENTE", "10%");
$xHG->col("tipo_en_sistema", "TR.TIPO EN SISTEMA", "10%");
$xHG->col("omitir_seguimiento", "TR.OMITIR SEGUIMIENTO", "10%");*/


//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");

//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idcreditos_tipoconvenio +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idcreditos_tipoconvenio +')", "delete.png");

$xHG->OButton("TR.PANEL", "jsPanel('+ data.record.codigo +')", "web.png");

$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.codigo +')", "undone.png");

$xFRM->addHElem("<div id='iddivprodc'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frm/.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivprodc});
}
function jsAdd(){
    xG.w({url:"../frm/.new.frm.php?", tiny:true, callback: jsLGiddivprodc});
}

function jsPanel(id){
	xG.w({url:"../frmcreditos/creditos.productos.panel.frm.php?producto=" + id, tiny:true, callback: jsLGiddivprodc});
}

function jsDeact(id){
    xG.recordInActive({tabla:"creditos_tipoconvenio", id:id, callback:jsLGiddivprodc, preguntar:true });
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>