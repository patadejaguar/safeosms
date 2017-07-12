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
$xHP		= new cHPage("TR.Estado_de_resultados", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();

$out 			= parametro("out", SYS_DEFAULT);
$senders		= getEmails($_REQUEST);
$ejercicio		= parametro("ejercicio", 0, MQL_INT);
$periodo		= parametro("periodo", 0, MQL_INT);
$moneda			= parametro("moneda", AML_CLAVE_MONEDA_LOCAL);
$ceros			= true;
$fecha			= $xF->getDiaFinal("$ejercicio-$periodo-01");
$sql			= "";
$titulo			= "";
$archivo		= "";
$suma_egresos	= 0;
$suma_ingresos	= 0;
$ingresos		= "";
$egresos		= "";
$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
$activo			= 1;
$xHP->init();
$xFormat		= new cFormato(502);
$arrVals		= array();

if(trim(CONTABLE_FAMILIA_EGRESOS) !== ""){
	$fam_ingresos	= explode(",", CONTABLE_FAMILIA_INGRESOS);
	$fam_egresos	= explode(",", CONTABLE_FAMILIA_EGRESOS);
	foreach ($fam_ingresos as $cid => $id){
		$xSec				= new cCuentasPorSector($id, $fecha);
		$xSec->init($ceros);
		$arrVals["variable_cont_sector_$id"] 	= $xSec->render();
		$ingresos								.= $xSec->render();
		$arrVals["variable_total_sector_$id"] 	= $xSec->getSumaTitulo();
		$suma_ingresos		= $xSec->getSumaTitulo();
	}
	foreach ($fam_egresos as $cid => $id){
		$xSec				= new cCuentasPorSector($id, $fecha);
		$xSec->init($ceros);
		$arrVals["variable_cont_sector_$id"] 	= $xSec->render();
		$egresos								.= $xSec->render();
		$arrVals["variable_total_sector_$id"] 	= $xSec->getSumaTitulo();
		$suma_egresos		= $xSec->getSumaTitulo();
	}	
} else {
//============ Reporte
	$xSec			= new cCuentasPorSector(CONTABLE_CLAVE_INGRESOS, $fecha); 
	$xSec->init($ceros);
	$ingresos		= $xSec->render();
	$suma_ingresos	= $xSec->getSumaTitulo();
	$xSec			= new cCuentasPorSector(CONTABLE_CLAVE_EGRESOS, $fecha); 
	$xSec->init($ceros);
	$egresos		= $xSec->render();
	$suma_egresos	= $xSec->getSumaTitulo();
}
$resultado		= $suma_ingresos - $suma_egresos;
//Actualizar Resultados
$xConf			= new cConfiguration();
$xConf->set("resultado_del_periodo_contable", $resultado);

$arrVals["variable_ficha_ingresos"] 	= $ingresos;
$arrVals["variable_total_ingresos"] 	= getFMoney($suma_ingresos);
$arrVals["variable_ficha_egresos"] 		= $egresos;
$arrVals["variable_total_egresos"] 		= getFMoney($suma_egresos);
$arrVals["variable_resultado_del_periodo"] = $resultado;
//
//$arrVals[] =
 

$xFormat->setProcesarVars($arrVals);

echo $xFormat->get();
$xHP->fin();
?>