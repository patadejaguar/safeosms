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
$xHP		= new cHPage("TR.REPORTE DE PERSONAS DE ALTO RIESGO", HP_REPORT);
$mql		= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$oficial = elusuario($iduser);


$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);

$out 			= parametro("out", SYS_DEFAULT);


$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();


echo $xHP->getHeader();
$sql 	= $mql->getListadoDeSocios(" (`socios_general`.`nivel_de_riesgo_aml` >= 50) ", "0,10000");

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