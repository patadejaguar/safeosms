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
$xHP		= new cHPage("TR.CREDITOS_LINEAS", HP_FORM);
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



$xFRM		= new cHForm("frmlineas", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();


/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivlineas",$xHP->getTitle());


$xHG->setSQL($xLi->getListadoDeLineasDeCred());

$xHG->addList();

$xHG->addKey("clave");
$xHG->col("clave_de_persona", "TR.CLAVE_DE_PERSONA", "10%");
$xHG->col("nombre", "TR.NOMBRE", "30%");

$xHG->col("fecha_de_vencimiento", "TR.FECHA_DE VENCIMIENTO", "15%");

$xHG->col("monto_autorizado", "TR.AUTORIZADO", "15%", true);
$xHG->col("monto_ejercido", "TR.EJERCIDO", "15%", true);


//$xHG->col("tasa", "TR.TASA", "10%");
//$xHG->col("periocidad", "TR.PERIOCIDAD", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");


$xHG->OButton("TR.PANEL", "jsPanelLineas('+ data.record.clave +')", "web.png");
$xHG->OButton("TR.AGREGAR CREDITO", "jsAddCredito('+ data.record.clave +')", "cash_stack_add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");


//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idcreditos_lineas +')", "delete.png");

$xFRM->addHElem("<div id='iddivlineas'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );

echo $xFRM->get();

?>
<script>
var xG	= new Gen();
var xC	= new CredGen();

function jsEdit(id){
//	xG.w({url:"../frmcreditos/creditos-lineas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivlineas});
}
function jsAdd(){
	xG.w({url:"../frmcreditos/frmcreditoslineas.php?", blank : true, tab:true, callback: jsLGiddivlineas});
}
function jsPanelLineas(id){
	xG.w({url:"../frmcreditos/creditos-lineas.panel.frm.php?clave=" + id, principal:true, callback: jsLGiddivlineas});
}
function jsDel(id){
	//xG.rmRecord({tabla:"creditos_lineas", id:id, callback:jsLGiddivlineas});
}
function jsAddCredito(id){
	xC.addCredito({origen:CNF.credito.origen.lineas, idorigen:id});
}
</script>
<?php
	


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>