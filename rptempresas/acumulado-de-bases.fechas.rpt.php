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
$xHP		= new cHPage("ACUMULADO DE OPERACIONES POR EMPRESAS", HP_REPORT);
	
$oficial = elusuario($iduser);

/**
 */
$xF				= new cFecha();
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$convenio 		= parametro("convenio", SYS_TODAS);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);
$convenio 		= parametro("producto", $convenio);

if(isset($_GET["mx"])){
	$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ?$_GET["off"] : fechasys();
}
$base			= parametro("base", 2002);

$xB				= new cEacp_config_bases_de_integracion();
$xB->setData($xB->query()->getRow("codigo_de_base=$base"));
$nombreBase		= $xB->descripcion()->v();
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
			<td width="20%">&nbsp;</td>
			<td width="40%">Fecha de Elaboracion:</td>
			<td width="40%"><?php echo fecha_larga(); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Preparado por:</td>
			<td><?php echo $oficial; ?></td>
		</tr>
		<tr>
			<td></td>
			<td>Fechas</td>
			<td><?php echo $xF->getFechaCorta($fechaInicial) . " AL " . $xF->getFechaCorta($fechaFinal); ?></td>
		</tr>
		<tr>
			<td />
			<td>Base</td>
			<td ><?php echo $nombreBase; ?></td>
		</tr>
	</thead>
</table>
<?php


	$xD		= new cFecha();
	$sql		= new cSQLListas();
	$xT		= new cTabla($sql->getBasesPorFechasPorDependencia($fechaInicial, $fechaFinal, $base));
	$xT->setKeyField($sql->getClave());
	
	if($out == "chart"){
		$xT->setTdClassByType(false);
		$xT->setPrepareChart();
		$xT->setFootSum(false);
		echo "<div id='treport'></div><span style='display:none'>" . $xT->Show($xHP->getTitle(), true, "tdatos") . "</span>";
	}
	$xT->setPrepareChart(false);
	$xT->setTdClassByType();
	$xT->setFootSum(array(1 => "monto"));
	echo $xT->Show();//"Reporte de Ingresos Mensuales por Empresas", true, "tingresos");

echo getRawFooter();
echo $xHP->setBodyEnd();
?>
<script>
<?php
if($out == "chart"){
echo "$(document).ready( function(){ jsGetChart(); });";
}
?>
function initComponents(){
	window.print();
}
function jsGetChart(mType){
	mType	= (typeof mType == "undefined") ? "bar" : mType;
	$('#treport').empty();
	$('#tdatos')
	   .visualize({
		width: SCREENW*0.8,
		height: SCREENH*0.45,
		type : mType,
		barMargin: 2
		})
	   .appendTo('#treport')
	   .trigger('visualizeRefresh');
}
</script>
<?php
$xHP->end(); 
?>