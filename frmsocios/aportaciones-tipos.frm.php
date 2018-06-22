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
$xHP		= new cHPage("TR.TIPO_DE CAPITAL_SOCIAL", HP_FORM);
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

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivaportst",$xHP->getTitle());

$xHG->setSQL("SELECT   `personas_aports_tipos`.`personas_aports_tipos_id`,
 		`personas_aports_tipos`.`operacion_tipo_id`,
         `operaciones_tipos`.`descripcion_operacion` AS `nombre`,
         `personas_aports_tipos`.`tasa_rendimiento`,
         `personas_aports_tipos`.`pagadero`,
         `personas_aports_tipos`.`estatusactivo`
FROM     `personas_aports_tipos` 
INNER JOIN `operaciones_tipos`  ON `personas_aports_tipos`.`operacion_tipo_id` = `operaciones_tipos`.`idoperaciones_tipos` ");
$xHG->addList();
$xHG->addKey("personas_aports_tipos_id");
$xHG->col("operacion_tipo_id", "TR.ID", "10%");
$xHG->col("nombre", "TR.NOMBRE", "50%");
$xHG->col("tasa_rendimiento", "TR.TASA", "10%");
//$xHG->col("pagadero", "TR.PAGADERO", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.personas_aports_tipos_id +')", "edit.png");

//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.personas_aports_tipos_id +')", "delete.png");


$xFRM->addHElem("<div id='iddivaportst'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/aportaciones-tipos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivaportst});
}
function jsAdd(){
	xG.w({url:"../frmsocios/aportaciones-tipos.new.frm.php?", tiny:true, callback: jsLGiddivaportst});
}
function jsDel(id){
	xG.rmRecord({tabla:"personas_aports_tipos", id:id, callback:jsLGiddivaportst});
}
</script>
<?php



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>