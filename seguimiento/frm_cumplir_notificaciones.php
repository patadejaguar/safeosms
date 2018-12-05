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
$xHP		= new cHPage("TR.NOTIFICACIONES", HP_FORM);
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

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddiv",$xHP->getTitle());

$xHG->setSQL("SELECT   `seguimiento_notificaciones`.`idseguimiento_notificaciones`,
         `socios`.`nombre`,
         `seguimiento_notificaciones`.`numero_solicitud`,
         `seguimiento_notificaciones`.`numero_notificacion`,
         `seguimiento_notificaciones`.`total`,
         `seguimiento_notificaciones`.`observaciones`,
         `seguimiento_notificaciones`.`estatus_notificacion`,
         `seguimiento_notificaciones`.`fecha_notificacion`,
         `seguimiento_notificaciones`.`hora`,
         `seguimiento_notificaciones`.`canal_de_envio`,
         `seguimiento_notificaciones`.`tiempo_entrega`,
         `usuarios`.`alias`,
         `seguimiento_notificaciones`.`idresultado`,
         `seguimiento_notificaciones`.`nota_entrega`
FROM     `seguimiento_notificaciones`
INNER JOIN `socios`  ON `seguimiento_notificaciones`.`socio_notificado` = `socios`.`codigo`
INNER JOIN `usuarios`  ON `seguimiento_notificaciones`.`oficial_de_seguimiento` = `usuarios`.`idusuarios` ");
$xHG->addList();
$xHG->setOrdenar();
$xHG->addkey("idseguimiento_notificaciones");

//$xHG->col("idseguimiento_notificaciones", "TR.CODIGO", "10%");

$xHG->col("fecha_notificacion", "TR.FECHA", "8%");

$xHG->col("nombre", "TR.NOMBRE", "30%");

$xHG->col("numero_solicitud", "TR.CREDITO", "8%");
//$xHG->col("numero_notificacion", "TR.NUMERO NOTIFICACION", "10%");

//$xHG->col("alias", "TR.USUARIO", "10%");
$xHG->col("estatus_notificacion", "TR.ESTATUS", "8%");

$xHG->col("hora", "TR.HORA", "8%");
$xHG->col("canal_de_envio", "TR.CANAL", "6%");
$xHG->col("total", "TR.TOTAL", "8%");

//$xHG->col("tiempo_entrega", "TR.TIEMPO ENTREGA", "10%");
//$xHG->col("observaciones", "TR.OBSERVACIONES", "10%");
//$xHG->col("idresultado", "TR.IDRESULTADO", "10%");
//$xHG->col("nota_entrega", "TR.NOTA ENTREGA", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idseguimiento_notificaciones +')", "edit.png");

if(MODO_DEBUG == true){
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idseguimiento_notificaciones +')", "delete.png");
}

$xFRM->addHElem("<div id='iddiv'></div>");


$xFRM->addJsCode( $xHG->getJs(true) );


echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frm/.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddiv});
}
function jsAdd(){
    xG.w({url:"../frmseguimiento/notificaciones.add.frm.php?", tiny:true, callback: jsLGiddiv});
}
function jsDel(id){
    xG.rmRecord({tabla:"tmp_3375557832", id:id, callback:jsLGiddiv });
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>