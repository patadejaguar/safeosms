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
$xHP		= new cHPage("TR.CATALOGO DE RIESGOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);



$xHP->addJTableSupport();
$xHP->init();



$xFRM	= new cHForm("frmaml_risk_catalog", "catalogo.riesgos.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();


/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivriskcatalog",$xHP->getTitle());

$xHG->setSQL($xLi->getListadoDeCatalogoRiesgos());
$xHG->addList();
$xHG->addKey("clave_de_control");
$xHG->col("descripcion", "TR.DESCRIPCION", "40%");
$xHG->col("tipo_de_riesgo", "TR.TIPO", "10%");
$xHG->col("valor_ponderado", "TR.VALOR", "10%");
$xHG->col("unidades_ponderadas", "TR.UNIDADES", "10%");
$xHG->col("unidad_de_medida", "TR.MEDIDA", "10%");
$xHG->col("forma_de_reportar", "TR.REPORTE", "7%");
$xHG->col("frecuencia_de_chequeo", "TR.CHEQUEO", "7%");
//$xHG->col("fundamento_legal", "TR.FUNDAMENTO LEGAL", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave_de_control +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave_de_control +')", "delete.png");
$xFRM->addHElem("<div id='iddivriskcatalog'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmpld/catalogo.riesgos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivriskcatalog});
}
function jsAdd(){
	xG.w({url:"../frmpld/catalogo.riesgos.new.frm.php?", tiny:true, callback: jsLGiddivriskcatalog});
}
function jsDel(id){
	xG.rmRecord({tabla:"aml_risk_catalog", id:id, callback:jsLGiddivriskcatalog});
}
</script>
<?php
	



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>