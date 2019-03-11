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
$xHP		= new cHPage("TR.EMPRESAS ENVIOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xTxt		= new cHText();

//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$todas			= parametro("todas", false, MQL_BOOL); $todas = parametro("todos", $todas, MQL_BOOL);

$xHP->addJTableSupport();
$xHP->init();



$xFRM	= new cHForm("frmempresas", "empresas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

$xSelP	= $xSel->getListaDePeriocidadDePago("idperiocidad");
$xSelP->addEspOption(SYS_TODAS, SYS_TODAS);
$xSelP->setOptionSelect(SYS_TODAS);

$xSelP->addEvent("onchange", "jsGetEmpresas()");

$xFRM->addHElem($xSelP->get(true) );

//$xFRM->OText("idbuscar", "", "TR.BUSCAR");
$xTxt->addEvent("jsGetEmpresas()", "onkeyup");
//$xTxt->setDiv13();
$xFRM->addHElem($xTxt->get("idbuscar", false, "TR.BUSCAR"));
//============================================================


$xHG	= new cHGrid("iddivempresas",$xHP->getTitle());

if($todas == true){
	$xHG->setSQL($xLi->getListadoDeEmpresas(false, false, false));
} else {
	$xHG->setSQL($xLi->getListadoDeEmpresasConCreditos(false, false, false));
	
	$xHG->setNoDefaultParam();
	$xHG->addList("../svc/empresas-con-nomina.svc.php?buscar=' + $('#idbuscar').val() + '&idfrecuencia=' + $('#idperiocidad').val() + '");
	
}


		
$xHG->addKey("clave");

$xHG->col("clave_de_persona", "TR.CLAVE_DE_PERSONA", "10%");
$xHG->col("alias", "TR.NOMBRE_CORTO", "20%");
$xHG->col("nombre", "TR.NOMBRE", "40%");

if($todas == true){
	$xHG->col("telefono", "TR.TELEFONO", "10%");
} else {
	$xHG->col("creditos", "TR.CREDITOS", "10%");
}
//$xHG->col("", "TR.", "10%");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
//$xHG->OButton("TR.PANEL", "jsGoToPanel('+ data.record.clave +')", "controls.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idsocios_aeconomica_dependencias +')", "delete.png");
$xHG->OButton("TR.AGREGAR NOMINA", "jsNuevaNomina('+ data.record.clave +')", "add.png");
$xFRM->addHElem("<div id='iddivempresas'></div>");

//$xHG->setOrdenar();
$xHG->setNoPaginar();




$xFRM->addJsCode( $xHG->getJs(true) );


$xFRM->OButton("TR.BUSCAR", "jsGetEmpresas()", $xFRM->ic()->BUSCAR);


echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var xEmp	= new EmpGen();
function jsEdit(id){
	xG.w({url:"../frmempresas/empresas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivempresas});
}
function jsAdd(){
	xG.w({url:"../frmempresas/empresas.new.frm.php?", tiny:true, callback: jsLGiddivempresas});
}
function jsDel(id){
	//xG.rmRecord({tabla:"socios_aeconomica_dependencias", id:id, callback:jsLGiddivempresas});
}
function jsGoToPanel(id){
	xEmp.goToPanel(id);
}
function jsGoTodas(){
	xG.go({url: "../frmempresas/empresas.frm.php?todas=true" });
}
function jsGetEmpresas(){
	$('#iddivempresas').jtable('destroy');
	jsLGiddivempresas();
}
function jsNuevaNomina(id){
	var idperiocidad	= entero($("#idperiocidad").val());
	xG.w({url:"../frmcreditos/envios_de_cobranza.frm.php?empresa=" + id + "&periocidad=" + idperiocidad, tab:true, callback: jsLGiddivempresas});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>