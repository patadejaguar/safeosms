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
$xHP		= new cHPage("TR.CATALOGO TIPO_DE RELACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frmrelaciones", "catalogo-tipos-relacion.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();




$xHG	= new cHGrid("iddivrelaciones",$xHP->getTitle());


$xHG->setSQL("SELECT   `idsocios_relacionestipos`,
         `descripcion_relacionestipos`,
         `subclasificacion`,
         `descripcion_larga`,
         `puntos_en_scoring`,
         (`requiere_domicilio`) AS `requiere_domicilio`,
         (`requiere_actividadeconomica`) AS `requiere_actividadeconomica`,
         (`requiere_validacion`) AS `requiere_validacion`,
         (`tiene_vinculo_patrimonial`) AS `tiene_vinculo_patrimonial`,
         (`mostrar`) AS `mostrar`,
         (`checar_aml`) AS `checar_aml`,
         `tags`
FROM `socios_relacionestipos` WHERE `mostrar`=1 LIMIT 0,100");
$xHG->addList();
$xHG->setOrdenar();

$xHG->addKey("idsocios_relacionestipos");

$xHG->col("descripcion_relacionestipos", "TR.NOMBRE", "35%");
$xHG->col("subclasificacion", "TR.CLASIFICACION", "7%");

//$xHG->col("descripcion_larga", "TR.DESCRIPCION", "10%");
//$xHG->col("tipo_relacion", "TR.TIPO RELACION", "10%");
//$xHG->col("puntos_en_scoring", "TR.CALIFICACION", "7%");

$xHG->OColSiNo("requiere_domicilio", "TR.DOMICILIO", "7%");
$xHG->OColSiNo("requiere_actividadeconomica", "TR.ACTIVIDAD_ECONOMICA", "7%");
$xHG->OColSiNo("requiere_validacion", "TR.VALIDACION", "7%");
$xHG->OColSiNo("tiene_vinculo_patrimonial", "TR.VINCULO", "7%");
$xHG->OColSiNo("checar_aml", "TR.AML", "7%");

//$xHG->OColSiNo("mostrar", "TR.MOSTRAR", "7%");
//$xHG->col("tags", "TR.TAGS", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idsocios_relacionestipos +')", "edit.png");

$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.idsocios_relacionestipos +')", "undone.png");

if(MODO_DEBUG == true){
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idsocios_relacionestipos +')", "delete.png");
}

$xFRM->addHElem("<div id='iddivrelaciones'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/catalogo-tipos-relacion.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivrelaciones});
}
function jsAdd(){
	xG.w({url:"../frmsocios/catalogo-tipos-relacion.new.frm.php?", tiny:true, callback: jsLGiddivrelaciones});
}
function jsDel(id){
	xG.rmRecord({tabla:"socios_relacionestipos", id:id, callback:jsLGiddivrelaciones});
}
function jsDeact(id){
    xG.recordInActive({tabla:"socios_relacionestipos", id:id, callback:jsLGiddivrelaciones, preguntar:true });
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>