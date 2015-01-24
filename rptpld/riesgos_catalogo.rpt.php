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

$sql = " SELECT
	`aml_risk_catalog`.`clave_de_control`      AS `clave`,
	`aml_risk_catalog`.`descripcion`           AS `nombre`,
	`aml_risk_types`.`nombre_del_riesgo`       AS `tipo`,
	`aml_risk_catalog`.`valor_ponderado`       AS `valor`,
	`aml_risk_catalog`.`unidades_ponderadas`   AS `unidades`,
	`aml_risk_catalog`.`unidad_de_medida`      AS `medida`,
	`aml_risk_catalog`.`forma_de_reportar`     AS `reporte`,
	`aml_risk_catalog`.`frecuencia_de_chequeo` AS `chequeo` 
FROM
	`aml_risk_catalog` `aml_risk_catalog` 
		INNER JOIN `aml_risk_types` `aml_risk_types` 
		ON `aml_risk_catalog`.`tipo_de_riesgo` = `aml_risk_types`.
		`clave_de_control` 
	ORDER BY
		`aml_risk_catalog`.`descripcion`,
		`aml_risk_catalog`.`clave_de_control` ";

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