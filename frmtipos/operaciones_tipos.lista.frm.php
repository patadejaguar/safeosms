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
$xHP        	= new cHPage("TR.Tipo de Operacion", HP_FORM);
$xT        		= new cTipos();

$xHP->init();

$clave 			= parametro("idoperaciones_tipos", null, MQL_INT);

$xFRM	= new cHForm("frmoperaciones_tipos", "operaciones_tipos");
$xFRM->setTitle($xHP->getTitle());
$xFRM->OButton("TR.info_General", "jsGetGrid('general')", $xFRM->ic()->REPORTE);
$xFRM->OButton("TR.Contabilidad", "jsGetGrid('contable')", $xFRM->ic()->CONTABLE);
$xFRM->OButton("TR.Clasificacion General", "jsGetGrid('clase')", $xFRM->ic()->TIPO);
$xFRM->OButton("TR.Clasificacion en Recibos", "jsGetGrid('claserecibos')", $xFRM->ic()->TIPO);
$xFRM->OButton("TR.COMO CANCELAR", "jsGetGrid('cancelacion')", $xFRM->ic()->TIPO);
$xFRM->OButton("TR.COMO CALCULAR", "jsGetGrid('formulas')", $xFRM->ic()->CALCULAR);
$xFRM->OButton("TR.AGREGAR", "jsAdd()", $xFRM->ic()->AGREGAR);

$xFRM->addHTML("<iframe src='../principal.php' id='ifoperaciones' height='800'></iframe>");
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsGetGrid(ctipo){
	if(typeof ctipo == undefined){ctipo="general";}
	$("#ifoperaciones").attr("src", "operaciones_tipos.grid.php?tipo=" + ctipo ); 
}
function jsAdd(){
	xG.w({url:"../frmoperaciones/operaciones_tipos.frm.php?", tiny:true, w:800});
}
	/* <iframe src="http://webdesign.about.com/od/iframes/a/aaiframe.htm" width="300" height="600">A page about learning iFrames</iframe> */
</script>
<?php
$xHP->fin();
?> 