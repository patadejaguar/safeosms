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
$xHP->init();

$clave 			= parametro("idoperaciones_tipos", null, MQL_INT);

$xFRM	= new cHForm("frmbase", "");
$xFRM->setTitle($xHP->getTitle());
$xFRM->addRefrescar();
//$xFRM->OButton("TR.AGREGAR", "jsAdd()", $xFRM->ic()->AGREGAR);
$xFRM->addJsInit("jsGetGrid();");

$xSBase	= $xSel->getListadoGenerico("SELECT `codigo_de_base`,CONCAT(`descripcion`, '-', `codigo_de_base`) AS `nombre` FROM `eacp_config_bases_de_integracion` ORDER BY `descripcion`", "idbase");
$xSBase->addEvent("onchange", "jsGetGrid()");
$xFRM->addDivSolo("<label for='idbase'>Nombre de la Base</label>", $xSBase->get("",false), "tx14", "tx34");
$xFRM->addHTML("<iframe src='../principal.php' id='ifoperaciones' height='800'></iframe>");
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsGetGrid(){
	var idbase	= $("#idbase").val();
	$("#ifoperaciones").attr("src", "bases.miembros.grid.php?clave="+idbase ); 
}
function jsAdd(){
	xG.w({url:"../frmutils/bases.miembros.grid.php?", tiny:true, w:800});
}
</script>
<?php
$xHP->fin();
?>