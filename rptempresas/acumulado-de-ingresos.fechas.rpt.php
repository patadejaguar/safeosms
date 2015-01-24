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
$xHP		= new cHPage("ACUMULADO DE INGRESOS POR FECHAS", HP_REPORT);
$xHP->setIncludes();
	
$oficial = elusuario($iduser);

/**
 */
$xF				= new cFecha();
$estatus 		= (isset($_GET["estado"]) ) ? $_GET["estado"] : "todas";
$frecuencia 		= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : "todas";
$convenio 		= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : "todas";
$empresa		= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : "todas";
$input 			= (isset($_GET["out"])) ? $_GET["out"] : "default";
$fechaInicial		= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();

//$xHP->setNoDefaultCSS();

//$xHP->addCSS( CSS_REPORT_FILE );

echo $xHP->getHeader();

echo $xHP->setBodyinit("initComponents();");

echo getRawHeader();
?>
<!-- -->
<table >
	<thead>
		<tr>
			<th colspan="3" class='title'><?php echo $xHP->getTitle(); ?></th>
		</tr>
<!-- DATOS GENERALES DEL REPORTE  -->
		<tr>
			<td width="50%">&nbsp;</td>
			<td width="20%">Fecha de Elaboracion:</td>
			<td width="30%"><?php echo fecha_larga(); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Preparado por:</td>
			<td><?php echo $oficial; ?></td>
		</tr>
		<tr>
			<td>Fechas</td>
			<td><?php echo $xF->getFechaCorta($fechaInicial); ?></td>
			<td><?php echo $xF->getFechaCorta($fechaFinal); ?></td>
		</tr>

	</thead>
</table>
<?php


	$xD		= new cFecha();
	$sql		= new cSQLListas();
	$xT		= new cTabla($sql->getBasesPorFechasPorDependencia($fechaInicial, $fechaFinal, 2002));
	$xT->setKeyField($sql->getClave());
	$xT->setTdClassByType();
	//$xT->setPrepareChart();
	
	$xT->setFootSum(array(1 => "monto"));
	echo $xT->Show();//"Reporte de Ingresos Mensuales por Empresas", true, "tingresos");


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