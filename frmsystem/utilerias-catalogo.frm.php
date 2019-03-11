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
$xHP		= new cHPage("TR.CATALOGO DE UTILERIAS", HP_FORM);
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



$xFRM		= new cHForm("frmutileriascats", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivgutils",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `general_utilerias` LIMIT 0,100");
$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("idgeneral_utilerias");
$xHG->col("nombre_utilerias", "TR.NOMBRE UTILERIAS", "30%");
//$xHG->col("descripcion_utileria", "TR.DESCRIPCION UTILERIA", "10%");
//$xHG->col("describe_param_1", "TR.DESCRIBE PARAM 1", "10%");
//$xHG->col("describe_param_2", "TR.DESCRIBE PARAM 2", "10%");
//$xHG->col("describe_param_3", "TR.DESCRIBE PARAM 3", "10%");
//$xHG->col("describe_init", "TR.DESCRIBE INIT", "10%");
//$xHG->col("describe_end", "TR.DESCRIBE END", "10%");
$xHG->col("isdisabled", "TR.INACTIVO", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idgeneral_utilerias +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idgeneral_utilerias +')", "delete.png");
$xFRM->addHElem("<div id='iddivgutils'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmsystem/utilerias-catalogo.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivgutils});
}
function jsAdd(){
    xG.w({url:"../frmsystem/utilerias-catalogo.new.frm.php?", tiny:true, callback: jsLGiddivgutils});
}
function jsDel(id){
    xG.rmRecord({tabla:"general_utilerias", id:id, callback:jsLGiddivgutils });
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>