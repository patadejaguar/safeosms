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
$xHP		= new cHPage("REPORTE DE OPERACIONES DETALLADAS POR PERSONA ", HP_REPORT);
	


$oficial = elusuario($iduser);

/**
 */
$xF		= new cFecha();
$estatus 	= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 	= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$empresa	= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 		= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$mx 		= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}

$ByConvenio	= ($convenio == SYS_TODAS) ? "" : " AND	(`listado_de_ingresos`.`producto` =$convenio) ";
$ByEmpresa	= ($empresa == SYS_TODAS) ? "" : " AND (`listado_de_ingresos`.`clave_empresa` = $empresa) ";
$ByFecha	= "";

echo $xHP->getHeader();

echo $xHP->setBodyinit("initComponents();");

echo getRawHeader();

$xRpt		= new cReportes();

echo $xRpt->getEncabezado($xHP->getTitle(), $fechaInicial, $fechaFinal, $oficial );

$sql	= "SELECT * FROM listado_de_ingresos WHERE (`listado_de_ingresos`.`fecha` >='$fechaInicial')
AND (`listado_de_ingresos`.`fecha` <='$fechaFinal') $ByConvenio $ByEmpresa
AND tipo_de_pago !='" . TESORERIA_COBRO_NINGUNO .  "'
";
//echo $sql;
$xTBL	= new cTabla($sql);
$xTBL->setTdClassByType();
$xTBL->setFootSum(array(
			9 => "capital",
			10 => "interes_normal",
			11 => "interes_moratorio",
			12 => "iva",
			13 => "otros"
			)
		);
echo $xTBL->Show();

echo getRawFooter();
echo $xHP->setBodyEnd();
?>
<script language="javascript">
<?php

?>
function initComponents(){
	window.print();
}
</script>
<?php
$xHP->end(); 
?>