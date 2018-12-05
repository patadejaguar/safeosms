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
$xHP		= new cHPage("TR.CATALOGO DE DOCUMENTOS", HP_FORM);
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


$todas		= parametro("todas", false, MQL_BOOL);

$xHP->addJTableSupport();
$xHP->init();



$xFRM	= new cHForm("frmdocumentaciontipos", "catalogo-documentacion.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivcattip",$xHP->getTitle());
if($todas == false){
	$xHG->setSQL("SELECT * FROM `personas_documentacion_tipos` WHERE `estatus`=1 ORDER BY `estatus` DESC LIMIT 0,100");
} else {
	$xHG->setSQL("SELECT * FROM `personas_documentacion_tipos` ORDER BY `estatus` DESC LIMIT 0,100");
}
$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("clave_de_control");


$xHG->col("nombre_del_documento", "TR.NOMBRE DEL DOCUMENTO", "40%");
$xHG->col("clasificacion", "TR.CLASIFICACION", "8%");
//$xHG->col("vigencia_dias", "TR.VIGENCIA", "8%");
//$xHG->col("almacen", "TR.ARCHIVO", "8%");

$xHG->OColSiNo("almacen","TR.ALMACEN", "8%" );

if($todas == true){
	$xHG->OColSiNo("estatus", "TR.ESTATUS", "8%");
	//$xHG->col("estatus", "TR.ESTATUS", "8%");
}

//$xHG->col("es_ident", "TR.IDENTIFICACION_OFICIAL", "8%");

$xHG->OColSiNo("es_ident","TR.IDENTIFICACION_OFICIAL", "8%" );

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.clave_de_control +')", "undone.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave_de_control +')", "edit.png");


if(MODO_DEBUG == true){
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave_de_control +')", "delete.png");
}

$xFRM->addHElem("<div id='iddivcattip'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/catalogo-documentacion.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivcattip});
}
function jsAdd(){
	xG.w({url:"../frmsocios/catalogo-documentacion.new.frm.php?", tiny:true, callback: jsLGiddivcattip});
}
function jsDel(id){
	xG.rmRecord({tabla:"personas_documentacion_tipos", id:id, callback:jsLGiddivcattip});
}
function jsDeact(id){
    xG.recordInActive({tabla:"personas_documentacion_tipos", id:id, callback:jsLGiddivcattip, preguntar:true });
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>