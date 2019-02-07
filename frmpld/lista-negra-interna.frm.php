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
$xHP		= new cHPage("TR.LISTA_NEGRA INTERNA", HP_FORM);
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

$xFRM	= new cHForm("frmlistanegra", "lista-negra-interna.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xFRM->addCerrar();
/* ===========		GRID JS		============*/


$xFRM->OBuscar("idbuscar", "", "", "jsBuscar");

$xHG	= new cHGrid("iddivlistanegra",$xHP->getTitle());

$xHG->setSQL($xLi->getListadoDePersonasConsultasLInt($persona));
$xHG->addList();
$xHG->addKey("clave_interna");
//$xHG->col("persona", "TR.PERSONA", "10%");
$xHG->col("nombre", "TR.PERSONA", "30%");
$xHG->col("fecha_de_registro", "TR.FECHA", "10%");
$xHG->col("fecha_de_vencimiento", "TR.VENCIMIENTO", "10%");
$xHG->col("riesgo", "TR.RIESGO", "10%");
$xHG->col("usuario", "TR.USUARIO", "10%");
$xHG->col("motivo", "TR.MOTIVO", "10%");
$xHG->col("estatus", "TR.ESTATUSACTIVO", "10%");

$xHG->col("observaciones", "TR.OBSERVACIONES", "10%");

$xHG->setOrdenar();

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave_interna +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave_interna +')", "delete.png");
$xFRM->addHElem("<div id='iddivlistanegra'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmpld/lista-negra-interna.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivlistanegra});
}
function jsAdd(){
	xG.w({url:"../frmpld/lista-negra-interna.new.frm.php?", tiny:true, callback: jsLGiddivlistanegra});
}
function jsDel(id){
	//xG.rmRecord({tabla:"aml_listanegra_int", id:id, callback:jsLGiddivlistanegra});
}
function jsBuscar(){
	var idbuscar= $("#idbuscar").val(); 
	var str		= " AND `aml_listanegra_int`.`nombre_comp` LIKE  '%" + idbuscar + "%' ";
	str			= base64.encode(str);
	str			= "&w=" + str;
	$("#iddivlistanegra").jtable("destroy");
	jsLGiddivlistanegra(str);
	
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>