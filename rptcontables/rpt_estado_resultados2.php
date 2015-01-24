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

$fecha			= $xF->getDiaFinal("$ejercicio-$periodo-01");
$sql			= "";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());

$activo			= 1;
$xHP->init();
$xFormat		= new cFormato(502);

//============ Reporte
$xSec			= new cCuentasPorSector(5, $fecha); 
$xSec->init(false);
$ingresos			= $xSec->render();
$suma_ingresos	= $xSec->getSumaTitulo();

$xSec			= new cCuentasPorSector(4, $fecha); 
$xSec->init(false);
$egresos			= $xSec->render();
$suma_egresos	= $xSec->getSumaTitulo();

$resultado		= $suma_ingresos - $suma_egresos;
//Actualizar Resultados
$xConf			= new cConfiguration();
$xConf->set("resultado_del_periodo_contable", $resultado);

$xFormat->setProcesarVars(array(
		"variable_ficha_ingresos" => $ingresos,
		"variable_total_ingresos" => getFMoney($suma_ingresos),
		"variable_ficha_egresos" => $egresos,
		"variable_total_egresos" => getFMoney($suma_egresos),
		"variable_resultado_del_periodo" => $resultado
));

echo $xFormat->get();
$xHP->fin();
?>