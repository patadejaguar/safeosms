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
$xHP		= new cHPage("TR.REPORTES", HP_FORM);
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



$xFRM	= new cHForm("frmreports", "reportes-datos.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xFRM->addCerrar();



/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivrpts",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `general_reports` ORDER BY `general_reports`.`aplica`,
         `general_reports`.`order_index`,
         `general_reports`.`descripcion_reports` LIMIT 0,100");
$xHG->addList();
//$xHG->col("idgeneral_reports", "TR.IDGENERAL REPORTS", "10%");
$xHG->addKey("idreport");

$xHG->col("descripcion_reports", "TR.NOMBRE", "60%");
$xHG->col("aplica", "TR.CLASIFICACION", "20%");
$xHG->col("order_index", "TR.ORDEN", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idreport +')", "edit.png");
$xHG->OButton("TR.PERMISOS", "jsPermisos('+ data.record.idreport +')", "lock.png");

//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idreport +')", "delete.png");
$xFRM->addHElem("<div id='iddivrpts'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmtipos/reportes-datos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivrpts});
}
function jsAdd(){
	xG.w({url:"../frmtipos/reportes-datos.new.frm.php?", tiny:true, callback: jsLGiddivrpts});
}
function jsDel(id){
	xG.rmRecord({tabla:"general_reports", id:id, callback:jsLGiddivrpts});
}

function jsPermisos(id){
	xG.w({url:"../frmsecurity/permisos.frm.php?idreporte=" + id, tab: true});
}


</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>