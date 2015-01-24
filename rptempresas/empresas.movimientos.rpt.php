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
$xHP		= new cHPage("TR.Operaciones de Empresa ", HP_REPORT);
$xF			= new cFecha();
	
$periodo	= (isset($_GET["periodo"])) ? $_GET["periodo"]: SYS_TODAS;
$empresa	= (isset($_GET["empresa"])) ? $_GET["empresa"]: SYS_TODAS;
$periocidad	= (isset($_GET["periocidad"])) ? $_GET["periocidad"]: SYS_TODAS;
//$estado		= (isset($_GET["estado"])) ? $_GET["estado"]: "todas";
$fecha_inicial	= parametro("idfecha-0", false); $fecha_inicial	= parametro("on", $fecha_inicial);  
$fecha_final	= parametro("idfecha-1", false); $fecha_final	= parametro("off", $fecha_final);

echo $xHP->getHeader();

echo $xHP->setBodyinit("initComponents();");

$ByFecha		= ($fecha_final == false OR $fecha_inicial == false) ? "" :  " AND	(`empresas_operaciones`.`fecha_de_operacion` >='" . $xF->getFechaISO($fecha_inicial) . "') AND	(`empresas_operaciones`.`fecha_de_operacion` <='" . $xF->getFechaISO($fecha_final) . "') ";
$ByPeriodo		= ($periodo == SYS_TODAS) ? "" : "AND ( `periodo_marcado` = $periodo) ";

$xRPT			= new cReportes();

echo $xRPT->getHInicial("TR.Estado de cuenta de Empresas", $fecha_inicial, $fecha_final);


//TODO: Acabar
	$lt			= new cSQLListas();
	$sql 		= $lt->getListadoDeOperacionesDeEmpresas($empresa, $periocidad, $ByFecha . $ByPeriodo);
	$xT			= new cTabla($sql);
	$xEmp		= new cEmpresas($empresa);
	echo $xEmp->getFicha();
	$xT->getFieldsSum("monto");
	
	$xT->setTdClassByType();
	$xT->setFootSum(array( 6=> "envios", 7 => "cobros"));
	echo $xT->Show();
	
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