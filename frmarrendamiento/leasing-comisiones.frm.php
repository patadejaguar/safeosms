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
$xHP		= new cHPage("TR.ARRENDAMIENTO COMISIONES", HP_FORM);
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

$xFRM	= new cHForm("frmleasing_comisiones", "leasing-comisiones.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->addCerrar();


/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivleasingcomisiones",$xHP->getTitle());

$xHG->setSQL("SELECT   `leasing_comisiones`.`idleasing_comisiones`,
         `leasing_originadores_tipos`.`nombre_tipo_originador` AS `tipo_de_originador`,
         `leasing_comisiones`.`tasa_comision`,
         `leasing_comisiones`.`comision_ejecutivo`,
         `leasing_comisiones`.`comision_regional`,
		`leasing_comisiones`.`bono`
FROM     `leasing_comisiones` 
INNER JOIN `leasing_originadores_tipos`  ON `leasing_comisiones`.`tipo_de_originador` = `leasing_originadores_tipos`.`idleasing_originadores_tipos` ");
$xHG->addList();
$xHG->addKey("idleasing_comisiones");
$xHG->col("tipo_de_originador", "TR.TIPO DE ORIGINADOR", "10%");
$xHG->col("tasa_comision", "TR.COMISION ORIGINADOR", "10%");
$xHG->col("comision_ejecutivo", "TR.COMISION EJECUTIVO", "10%");
$xHG->col("comision_regional", "TR.COMISION REGIONAL", "10%");
$xHG->col("bono", "TR.BONO", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idleasing_comisiones +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idleasing_comisiones +')", "delete.png");
$xFRM->addHElem("<div id='iddivleasingcomisiones'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/leasing-comisiones.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivleasingcomisiones});
}
function jsAdd(){
	xG.w({url:"../frmarrendamiento/leasing-comisiones.new.frm.php?", tiny:true, callback: jsLGiddivleasingcomisiones});
}
function jsDel(id){
	xG.rmRecord({tabla:"leasing_comisiones", id:id, callback:jsLGiddivleasingcomisiones});
}
</script>
<?php
	

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>