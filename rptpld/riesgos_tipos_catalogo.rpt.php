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
	
$xHP->setTitle($xHP->lang("catalogo de", "riesgo") );

$oficial = elusuario($iduser);

/**
 */
$xF				= new cFecha();
$estatus 		= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 		= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$empresa		= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();

echo $xHP->getHeader();

echo $xHP->setBodyinit("initComponents();");

echo getRawHeader();


$xRPT			= new cReportes();

echo $xRPT->getEncabezado($xHP->getTitle());

$sql = "SELECT * FROM aml_risk_types";

$xTBL	= new cTabla($sql);
echo $xTBL->Show();

echo getRawFooter();
echo $xHP->setBodyEnd();
?>
<script>
<?php

?>
function initComponents(){
	window.print();
}
</script>
<?php
$xHP->end(); 
?>