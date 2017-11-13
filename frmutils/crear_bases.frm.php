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
    $theFile            = __FILE__;
    $permiso            = getSIPAKALPermissions($theFile);
    if($permiso === false){    header ("location:../404.php?i=999");    }
    $_SESSION["current_file"]    = addslashes( $theFile );
//=====================================================================================================
$xHP        	= new cHPage("TR.Bases", HP_FORM);
$xT        		= new cTipos();
$xSel			= new cHSelect();


$xHP->addJTableSupport();

$xHP->init();

$clave 			= parametro("idoperaciones_tipos", null, MQL_INT);
$soloops		= parametro("tipodebase"); $soloops	= strtolower($soloops);


$xFRM	= new cHForm("frmbase", "");
$xFRM->setTitle($xHP->getTitle());

$ByActivo		= " AND (`estatus`= 1) ";
$ByTipo			= ($soloops == "") ? "" : "AND (`tipo_de_base`='$soloops') ";

$xFRM->addCerrar();
$xFRM->OButton("TR.CARGAR", "jsLoadGrid()", $xFRM->ic()->RECARGAR);

$xFRM->OButton("TR.OPERACIONES", "jsGoLoadTipo('de_operaciones')", $xFRM->ic()->IR);

$xSel->addEvent("jsLoadGrid()", "onchange");


$sqlbase		= "SELECT `codigo_de_base`,CONCAT(`descripcion`, '-', `codigo_de_base`) AS `nombre` FROM `eacp_config_bases_de_integracion` WHERE `codigo_de_base` > 0 $ByActivo $ByTipo ORDER BY `descripcion`";
$xSBase			= $xSel->getListadoGenerico($sqlbase, "idbase");

$xSBase->addEvent("onchange", "jsLoadGrid(this)");

$xFRM->addDivSolo("<label>Nombre de la Base</label>", $xSBase->get("",false), "tx14", "tx34");

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivbases",$xHP->getTitle());

$xHG->setSQL("SELECT   `eacp_config_bases_de_integracion_miembros`.`ideacp_config_bases_de_integracion_miembros`,
         `eacp_config_bases_de_integracion_miembros`.`miembro`,
         `eacp_config_bases_de_integracion_miembros`.`descripcion_de_la_relacion` AS `descripcion`,
         `eacp_config_bases_de_integracion_miembros`.`subclasificacion`,
         `eacp_config_bases_de_integracion_miembros`.`afectacion`
FROM     `eacp_config_bases_de_integracion_miembros` WHERE ( `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = ? ) ");


$xHG->addList();

$xHG->addKey("ideacp_config_bases_de_integracion_miembros");

$xHG->col("miembro", "TR.MIEMBRO", "10%");

$xHG->col("descripcion", "TR.DESCRIPCION", "40%");
$xHG->col("subclasificacion", "TR.SUBCLASIFICACION", "10%");

$xHG->col("afectacion", "TR.AFECTACION", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");

$xHG->OToolbar("TR.AGREGAR OTROSINGRESOS", "jsAddOtrosIngresos()", "grid/add.png");

//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.ideacp_config_bases_de_integracion_miembros +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.ideacp_config_bases_de_integracion_miembros +')", "delete.png");

$xFRM->addHElem("<div id='iddivbases'></div>");

$xFRM->addJsCode( $xHG->getJs(false) );


echo $xFRM->get();
?>
<script>
var xG	= new Gen();

function jsEdit(id){
	//xG.w({url:"../frmutils/bases_de_sistema.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivbases});
}
function jsAdd(){
	var idba	= $("#idbase").val();
	xG.w({url:"../frmutils/bases_de_sistema.new.frm.php?base=" + idba, tiny:true, w:480, callback: jsLGiddivbases});
}
function jsDel(id){
	//xG.rmRecord({tabla:"eacp_config_bases_de_integracion_miembros", id:id, callback:jsLGiddivbases});
}
function jsLoadGrid(obj){
	var idba	= $("#idbase").val();
	var str2	= "";
	
	if(typeof obj !== "undefined"){
		idba	= obj.value;
	}
	
	str2		= "&vars=" + idba;
	if( $(".jtable-main-container").length > 0 ){
		$('#iddivbases').jtable('destroy');
	}

	jsLGiddivbases(str2);
}
function jsGoLoadTipo(vtip){
	xG.go({url: "../frmutils/crear_bases.frm.php?tipodebase=" + vtip });
}
function jsAddOtrosIngresos(){
	var idba	= $("#idbase").val();
	xG.w({url:"../frmutils/bases_de_sistema.new.frm.php?tiporecibo=99&base=" + idba, tiny:true, w:480, callback: jsLGiddivbases});
}
</script>
<?php
$xHP->fin();
?>