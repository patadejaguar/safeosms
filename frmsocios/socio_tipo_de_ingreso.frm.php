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
$xHP		= new cHPage("TR.TIPO_DE INGRESO PERSONAS", HP_FORM);
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



$xFRM		= new cHForm("frmpersonastipoing", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivtipoingreso",$xHP->getTitle());

$xHG->setSQL("SELECT   `socios_tipoingreso`.`idsocios_tipoingreso`,
         `socios_tipoingreso`.`descripcion_tipoingreso`,
         `socios_tipoingreso`.`descripcion_detallada`,
         `socios_tipoingreso`.`parte_social`,
         `socios_tipoingreso`.`parte_permanente`,
         getBooleanMX(`socios_tipoingreso`.`estado`) AS `estatusactivo`,
         `entidad_niveles_de_riesgo`.`nombre_del_nivel` AS `nivel_de_riesgo`
FROM     `socios_tipoingreso` 
INNER JOIN `entidad_niveles_de_riesgo`  ON `socios_tipoingreso`.`nivel_de_riesgo` = `entidad_niveles_de_riesgo`.`clave_de_nivel` ");
$xHG->addList();
$xHG->addKey("idsocios_tipoingreso");
$xHG->col("descripcion_tipoingreso", "TR.NOMBRE", "10%");
$xHG->col("descripcion_detallada", "TR.DESCRIPCION", "10%");
//$xHG->col("parte_social", "TR.PARTE_SOCIAL", "10%");
//$xHG->col("parte_permanente", "TR.PARTE_PERMANENTE", "10%");
$xHG->col("estatusactivo", "TR.ESTATUSACTIVO", "10%");
$xHG->col("nivel_de_riesgo", "TR.NIVEL DE RIESGO", "10%");


$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idsocios_tipoingreso +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idsocios_tipoingreso +')", "delete.png");
$xHG->OButton("TR.FORMS_Y_DOCS", "jsGetFormatos('+ data.record.idsocios_tipoingreso +')", "web.png");

$xFRM->addHElem("<div id='iddivtipoingreso'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/personas-tipo_de_ingreso.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivtipoingreso});
}
function jsAdd(){
	xG.w({url:"../frmsocios/personas-tipo_de_ingreso.new.frm.php?", tiny:true, callback: jsLGiddivtipoingreso});
}
function jsRequisitos(id){
	xG.w({url:"../frmsocios/personas-reglas.frm.php?", tiny:true, callback: jsLGiddivtipoingreso});
}
function jsDel(id){
	xG.rmRecord({tabla:"socios_tipoingreso", id:id, callback:jsLGiddivtipoingreso});
}
function jsGetFormatos(id){
	xG.w({url:"../frmutils/contratos-editor.frm.php?tipopersona=" + id, blank:true, tab:true, callback: jsLGiddivtipoingreso});
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>
