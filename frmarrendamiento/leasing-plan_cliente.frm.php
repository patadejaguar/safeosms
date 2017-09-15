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
$xHP		= new cHPage("TR.PLAN_DE_PAGOS CLIENTE", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

function jsaGenerarPlanCliente($credito){
	$xRentas	= new cLeasingRentas();
	$xRentas->setCrearPorCredito($credito);
	return $xRentas->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaGenerarPlanCliente', array('credito'), "#idaviso");
$jxc ->process();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);


$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();

$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
$xFRM->addAviso("", "idaviso");
$xFRM->OButton("TR.GENERAR", "jsGenerar()", $xFRM->ic()->EJECUTAR);
$xFRM->OHidden("credito", $credito);

$xCred	= new cCredito($credito);
if($xCred->init() == true){
	$xFRM->addHElem( $xCred->getFichaMini() );
	
	//$xPlan	= new cPlanDePagos($xCred->getNumeroDePlanDePagos());
	//$xPlan->setClaveDeCredito($credito);
	
	//$xFRM->addHElem($xPlan->getVersionImpresaLeasing());
}




$xHG	= new cHGrid("iddivrentas", $xHP->getTitle());



$xHG->setSQL($xLi->getListadoDeLeasingPlanCliente($credito));

$xHG->addList();
$xHG->addKey("idleasing_renta");

//$xHG->col("clave_leasing", "TR.CLAVE LEASING", "10%");
//$xHG->col("credito", "TR.CREDITO", "10%");
$xHG->col("periodo", "TR.PERIODO", "10%");
$xHG->col("fecha", "TR.FECHA", "10%");

$xHG->col("deducible", "TR.RENTA", "10%", true);
$xHG->col("nodeducible", "TR.GASTOS", "10%", true);

//$xHG->col("iva_ded", "TR.IVA DED", "10%");
//$xHG->col("iva_no_ded", "TR.IVA NO DED", "10%");

$xHG->col("iva", "TR.IVA", "10%", true);

$xHG->col("total", "TR.TOTAL", "10%", true);

$xHG->col("pagos", "TR.PAGOS", "10%", true);


//$xHG->col("fecha_max", "TR.FECHA MAX", "10%");
//$xHG->col("clave_no_ded", "TR.CLAVE NO DED", "10%");
//$xHG->col("fecha_pago", "TR.FECHA PAGO", "10%");
//$xHG->col("recibo_pago", "TR.RECIBO PAGO", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.id +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.id +')", "delete.png");
$xFRM->addHElem("<div id='iddivrentas'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/leasing-plan_cliente.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivrentas});
}
function jsAdd(){
	xG.w({url:"../frmarrendamiento/leasing-plan_cliente.new.frm.php?", tiny:true, callback: jsLGiddivrentas});
}
function jsDel(id){
	xG.rmRecord({tabla:"leasing_rentas", id:id, callback:jsLGiddivrentas});
}
function jsGenerar(){
	session(TINYAJAX_CALLB, "jsLGiddivrentas()");
	xG.confirmar({msg: "¿ Confirma generar de nuevo el PLAN_DE_PAGOS ?", callback: jsaGenerarPlanCliente});
}
</script>
<?php

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>