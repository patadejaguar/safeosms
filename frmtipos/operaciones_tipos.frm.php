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
$xHP        	= new cHPage("TR.Tipos de Operacion", HP_FORM);
$xT        		= new cTipos();
$xIcs			= new cFIcons();
$xHP->init();

$clave 			= parametro("idoperaciones_tipos", null, MQL_INT);

$xFRM	= new cHForm("frmoperaciones_tipos", "operaciones_tipos");
$xFRM->OButton("TR.General", "jsGetGrid('general')", $xIcs->REPORTE);
$xFRM->OButton("TR.Contabilidad", "jsGetGrid('contable')", $xIcs->CONTABLE);
$xFRM->OButton("TR.Clase", "jsGetGrid('clase')", $xIcs->TIPO);

$xFRM->OButton("TR.Formulas", "jsGetGrid('formulas')", $xIcs->TIPO);

$xFRM->addHTML("<iframe src='../principal.php' id='ifoperaciones' height='800'></iframe>");
echo $xFRM->get();
?>
<script>
	function jsGetGrid(ctipo) {
		var xG	= new Gen();
		//xG.w({ url : "operaciones_tipos.grid.php?tipo=" + ctipo });
		$("#ifoperaciones").attr("src", "operaciones_tipos.grid.php?tipo=" + ctipo );
	}
	/* <iframe src="http://webdesign.about.com/od/iframes/a/aaiframe.htm" width="300" height="600">A page about learning iFrames</iframe> */
</script>
<?php
$xHP->end();
?> 