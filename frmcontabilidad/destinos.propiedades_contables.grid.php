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
$xHP		= new cHPage("Destinos de Credito.- Propiedades Contables", HP_FORM);
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

$xHG    = new cHGrid("iddivdestinocont",$xHP->getTitle());

$xHG->setSQL("SELECT idcreditos_destinos, descripcion_destinos, capital_vencido_renovado, capital_vencido_reestructurado, capital_vencido_normal, capital_vigente_renovado, capital_vigente_reestructurado, capital_vigente_normal, interes_vencido_renovado, interes_vencido_reestructurado, interes_vencido_normal, interes_vigente_renovado, interes_vigente_reestructurado, interes_vigente_normal, interes_cobrado, moratorio_cobrado FROM creditos_destinos");
$xHG->addList();
$xHG->setOrdenar();
$xHG->col("idcreditos_destinos", "TR.CLAVE", "10%");
$xHG->col("descripcion_destinos", "TR.DESCRIPCION", "50%");

/*$xHG->col("capital_vencido_renovado", "TR.CAPITAL VENCIDO RENOVADO", "10%");
$xHG->col("capital_vencido_reestructurado", "TR.CAPITAL VENCIDO REESTRUCTURADO", "10%");
$xHG->col("capital_vencido_normal", "TR.CAPITAL VENCIDO NORMAL", "10%");
$xHG->col("capital_vigente_renovado", "TR.CAPITAL VIGENTE RENOVADO", "10%");
$xHG->col("capital_vigente_reestructurado", "TR.CAPITAL VIGENTE REESTRUCTURADO", "10%");
$xHG->col("capital_vigente_normal", "TR.CAPITAL VIGENTE NORMAL", "10%");
$xHG->col("interes_vencido_renovado", "TR.INTERES VENCIDO RENOVADO", "10%");
$xHG->col("interes_vencido_reestructurado", "TR.INTERES VENCIDO REESTRUCTURADO", "10%");
$xHG->col("interes_vencido_normal", "TR.INTERES VENCIDO NORMAL", "10%");
$xHG->col("interes_vigente_renovado", "TR.INTERES VIGENTE RENOVADO", "10%");
$xHG->col("interes_vigente_reestructurado", "TR.INTERES VIGENTE REESTRUCTURADO", "10%");
$xHG->col("interes_vigente_normal", "TR.INTERES VIGENTE NORMAL", "10%");
$xHG->col("interes_cobrado", "TR.INTERES COBRADO", "10%");
$xHG->col("moratorio_cobrado", "TR.MORATORIO COBRADO", "10%");*/

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idcreditos_destinos +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idcreditos_destinos +')", "delete.png");

$xFRM->addHElem("<div id='iddivdestinocont'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );



echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmcontabilidad/creditos-destino-cont.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivdestinocont});
}


</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();



?>