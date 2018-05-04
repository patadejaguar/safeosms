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
$xHP		= new cHPage("TR.BIZRULES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

function jsaUpdateTabla($vv, $ask){
	$xQL	= new MQL();
	$xQL->setRawQuery("UPDATE `entidad_reglas` SET `valor`='$ask' WHERE `identidad_reglas`=$vv");
	return "Regla $vv Actualizada a $ask";
}

$jxc ->exportFunction('jsaUpdateTabla', array('idindice', 'idvalor'), "#idaviso");
$jxc ->process();

$xHP->addJTableSupport();
$xHP->init();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xFRM	= new cHForm("frmreglas", "entidad-reglas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();



/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivreglas",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `entidad_reglas` LIMIT 0,100");
$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("identidad_reglas");


$xHG->col("contexto", "TR.CONTEXTO", "15%");
$xHG->col("nombre", "TR.NOMBRE", "40%");
$xHG->OColFunction("idvalor", "TR.ESTATUSACTIVO", "10%", "jsRenderActivo" );
$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");

$xFRM->addHElem("<div id='iddivreglas'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
$xFRM->addAviso("", "idaviso");
$xFRM->OHidden("idindice", 0);
$xFRM->OHidden("idvalor", "");

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsecurity/entidad-reglas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivreglas});
}
function jsAdd(){
	xG.w({url:"../frmsecurity/entidad-reglas.new.frm.php?", tiny:true, callback: jsLGiddivreglas});
}
function jsDel(id){
	xG.rmRecord({tabla:"entidad_reglas", id:id, callback:jsLGiddivreglas});
}
function jsRenderActivo(data){
	var id 		= data.record.identidad_reglas;
	var vv		= data.record.valor;
	var ctrl	= "";
	if(vv == 1){
		ctrl = "<div class=\"coolCheck\"><input id=\"v_" + id +  "\" name=\"v_" + id +  "\" type=\"checkbox\" checked=\"checked\" onchange=\"jsSetUpdate(" + id + ")\"><label for=\"v_" + id + "\"></label></div>";
	} else {
		ctrl = "<div class=\"coolCheck\"><input id=\"v_" + id +  "\" name=\"v_" + id +  "\" type=\"checkbox\" onchange=\"jsSetUpdate(" + id + ")\"><label for=\"v_" + id + "\"></label></div>";
	}
	return ctrl;
}
function jsSetUpdate(id){
	var idv	= $('#v_' + id).prop('checked');
	$("#idindice").val(id);
	if(idv == true){
		$("#idvalor").val(1);
	} else {
		$("#idvalor").val(0);		
	}
	jsaUpdateTabla();
}
</script>
<?php
	




$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>