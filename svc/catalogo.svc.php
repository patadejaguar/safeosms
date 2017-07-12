<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE);
$txt		= "";
$cuenta		= parametro("cuenta", false, MQL_RAW); $cuenta = parametro("idcuenta", $cuenta, MQL_RAW);
$rs			= array("error" => true);
$idcuenta	= setNoMenorQueCero($cuenta);
$ncuenta	= ($idcuenta > 0) ? "" : $cuenta;
$ByCuenta	= "";
$ByNombre	= "";
if($idcuenta >  0){
	$xCta		= new cCuentaContableEsquema($idcuenta);
	$ByCuenta	= " AND (`contable_catalogo`.`numero` LIKE '" . $xCta->CUENTARAW . "%') ";
}
if($ncuenta != ""){
	$ByNombre	= ($ByCuenta == "") ? " AND (`contable_catalogo`.`nombre` LIKE '%$ncuenta%') " : " OR (`contable_catalogo`.`nombre` LIKE '%$ncuenta%') ";
}
//cuenta por numero, cuenta por nombre
$sql		= "SELECT
	`contable_catalogo`.`numero` AS `numero_de_cuenta`,
	CONCAT(`contable_catalogo`.`numero`, ' - ', `contable_catalogo`.`nombre`) AS `nombre_de_cuenta` 
FROM
	`contable_catalogo`
WHERE (`contable_catalogo`.`numero` > 0 ) $ByCuenta $ByNombre LIMIT 0,50 ";
setLog($sql);
header('Content-type: application/json');
if($idcuenta > 0 OR $ncuenta != ""){
	$xSVC       = new MQLService("LIST", $sql);
	echo $xSVC->getJSON();	
} else {
	echo json_encode($rs);
}
/*
header('Content-type: application/json');

*/
?>