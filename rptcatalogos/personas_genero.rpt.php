<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP		= new cHPage("REPORTE DE ", HP_REPORT);
$mql		= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$xHP->setTitle("TR.catalogo_de genero");



$estatus 		= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 		= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$empresa		= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();


echo $xHP->getHeader();
$sql 	= "SELECT * FROM socios_genero ";

if($out == OUT_EXCEL ){
	echo $xHP->setBodyinit();
	$xls	= new cHExcel();
	$xls->convertTable($sql, $xHP->getTitle());
} else {
	echo $xHP->setBodyinit("initComponents();");
	$xRPT			= new cReportes();
	
	$xTBL			= new cTabla($sql);
	echo $xRPT->getHInicial($xHP->getTitle());
	echo $xTBL->Show();
	
	echo $xRPT->getPie();
	?>
	<script>
	<?php ?>
	function initComponents(){ window.print();	}
	</script>
	<?php
	
}

echo $xHP->setBodyEnd();
$xHP->end(); 
?>