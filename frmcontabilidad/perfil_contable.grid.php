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
$xHP		= new cHPage("TR.Perfil Contable de recibos", HP_FORM);
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

$xHG    = new cHGrid("iddivperfilrecs",$xHP->getTitle());

$xHG->setSQL("SELECT   `contable_polizas_perfil`.`idcontable_poliza_perfil`,
         `operaciones_recibostipo`.`descripcion_recibostipo`,
         `operaciones_tipos`.`descripcion_operacion`,
         `contable_polizas_perfil`.`descripcion`,
         `contable_polizas_perfil`.`operacion`
FROM     `contable_polizas_perfil`
INNER JOIN `operaciones_recibostipo`  ON `contable_polizas_perfil`.`tipo_de_recibo` = `operaciones_recibostipo`.`idoperaciones_recibostipo`
INNER JOIN `operaciones_tipos`  ON `contable_polizas_perfil`.`tipo_de_operacion` = `operaciones_tipos`.`idoperaciones_tipos` ");

$xHG->addList();
$xHG->setOrdenar();

$xHG->col("idcontable_poliza_perfil", "TR.CLAVE", "10%");
$xHG->col("descripcion_recibostipo", "TR.RECIBO", "10%");
$xHG->col("descripcion_operacion", "TR.OPERACION", "10%");
//$xHG->col("descripcion", "TR.DESCRIPCION", "10%");
$xHG->col("operacion", "TR.OPERACION", "10%");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idcontable_poliza_perfil +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idcontable_poliza_perfil +')", "delete.png");
$xFRM->addHElem("<div id='iddivperfilrecs'></div>");


$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmcontabilidad/recibos-contabilidad.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivperfilrecs});
}
function jsAdd(){
    //xG.w({url:"../frmcontabilidad/recibos-contabilidad.new.frm.php?", tiny:true, callback: jsLGiddivperfilrecs});
}
function jsDel(id){
    xG.rmRecord({tabla:"tmp_3627077819", id:id, callback:jsLGiddivperfilrecs });
}
</script>
<?php



//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>