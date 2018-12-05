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
$xHP		= new cHPage("TR.CATALOGO DE PERIOCIDAD", HP_FORM);
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

$xHG    = new cHGrid("iddivperiodicidad",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `creditos_periocidadpagos` WHERE estatusactivo=1 LIMIT 0,100");
$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("idcreditos_periocidadpagos");
$xHG->col("periocidad_de_pago", "TR.PERIOCIDAD", "10%");
$xHG->col("descripcion_periocidadpagos", "TR.DESCRIPCION", "10%");

$xHG->col("titulo_en_informe", "TR.TITULO", "10%");
$xHG->col("tolerancia_en_dias_para_vencimiento", "TR.DIAS PARA VENCIMIENTO", "10%");
//$xHG->col("estatusactivo", "TR.ESTATUSACTIVO", "10%");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idcreditos_periocidadpagos +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idcreditos_periocidadpagos +')", "delete.png");
$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.idcreditos_periocidadpagos +')", "undone.png");
$xFRM->addHElem("<div id='iddivperiodicidad'></div>");

$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmcreditos/creditos-periodicidad.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivperiodicidad});
}
function jsAdd(){
    xG.w({url:"../frmcreditos/creditos-periodicidad.new.frm.php?", tiny:true, callback: jsLGiddivperiodicidad});
}
function jsDel(id){
    xG.rmRecord({tabla:"creditos_periocidadpagos", id:id, callback:jsLGiddivperiodicidad });
}
function jsDeact(id){
    xG.recordInActive({tabla:"creditos_periocidadpagos", id:id, callback:jsLGiddivperiodicidad, preguntar:true });
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>