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
$xHP		= new cHPage("TR.PERIOCIDAD FLUJO_DE_EFECTIVO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivperiodiflu",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `creditos_periocidadflujo` LIMIT 0,100");
$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("idcreditos_periocidadflujo");
$xHG->col("descripcion_periocidadflujo", "TR.DESCRIPCION", "10%");
$xHG->col("periocidad_flujo", "TR.PERIOCIDAD", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idcreditos_periocidadflujo +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idcreditos_periocidadflujo +')", "delete.png");
//$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.idcreditos_periocidadflujo +')", "undone.png");
$xFRM->addHElem("<div id='iddivperiodiflu'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmcreditos/periodicidad_de_flujo.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivperiodiflu});
}
function jsAdd(){
    xG.w({url:"../frmcreditos/periodicidad_de_flujo.new.frm.php?", tiny:true, callback: jsLGiddivperiodiflu});
}
function jsDel(id){
    xG.rmRecord({tabla:"creditos_periocidadflujo", id:id, callback:jsLGiddivperiodiflu });
}
function jsDeact(id){
    xG.recordInActive({tabla:"creditos_periocidadflujo", id:id, callback:jsLGiddivperiodiflu, preguntar:true });
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>