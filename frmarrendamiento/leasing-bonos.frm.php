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
$xHP		= new cHPage("TR.BONOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
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


$xFRM		= new cHForm("frmbonos", "leasing-bonos.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

//Generar Bono si no existe

$xBon		= new cLeasingBonos();
$xBon->setCrearSobreLeasing($clave);
$xFRM->addCerrar();

$xFRM->OHidden("idleasing", $clave);

$xHG	= new cHGrid("iddivbonos",$xHP->getTitle());
$xHG->setOrdenar();

$xHG->setSQL("SELECT   `leasing_bonos`.`idleasing_bonos`,
         `vw_leasing_bonos_dest`.`descripcion`,
         `leasing_bonos`.`tasa_bono`,
         `leasing_bonos`.`monto_bono`,
         `leasing_bonos`.`fecha`
FROM     `vw_leasing_bonos_dest` 
INNER JOIN `leasing_bonos`  ON `vw_leasing_bonos_dest`.`clave` = `leasing_bonos`.`tipo_destino` 
WHERE    ( `leasing_bonos`.`clave_leasing` = $clave )");
$xHG->addList();
$xHG->addKey("idleasing_bonos");

$xHG->col("descripcion", "TR.DESTINO", "10%");
$xHG->col("fecha", "TR.FECHA", "10%");
$xHG->col("tasa_bono", "TR.TASA BONO", "10%");
$xHG->col("monto_bono", "TR.MONTO BONO", "10%", true);

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idleasing_bonos +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idleasing_bonos +')", "delete.png");
$xFRM->addHElem("<div id='iddivbonos'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/leasing-bonos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivbonos});
}
function jsAdd(){
	var idleasing	= $("#idleasing").val();
	xG.w({url:"../frmarrendamiento/leasing-bonos.new.frm.php?idleasing=" + idleasing, tiny:true, callback: jsLGiddivbonos});
}
function jsDel(id){
	xG.rmRecord({tabla:"leasing_bonos", id:id, callback:jsLGiddivbonos});
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>