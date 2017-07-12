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

$cp			= parametro("cp");
$municipio	= parametro("m");
$estado		= parametro("e");
$nombre		= parametro("n");

$ByCP	= "";//($cp == "") ? "": " AND (`general_colonias`.`codigo_postal` = $cp) ";
$ByMun	= ( setNoMenorQueCero($municipio) <= 0 ) ? "" : " AND (`general_municipios`.`clave_de_municipio` ='$municipio') "; //TODO: checar
$ByEst	= ( setNoMenorQueCero($estado <= 0) ) ? "" : " AND (`general_municipios`.`clave_de_entidad` ='$estado') " ;
$ByCol	= ($nombre == "") ? "" : "  AND ( `nombre_del_municipio` LIKE '%$nombre%'  ) ";

$action		= "LIST";

$sql 	= "
SELECT
	`general_municipios`.`nombre_del_municipio`,
	`general_municipios`.`clave_de_entidad`,
	`general_municipios`.`clave_de_municipio` 
FROM
	`general_municipios` `general_municipios` 
WHERE
	`idgeneral_municipios` > 0  $ByCP $ByEst $ByMun $ByCol
	LIMIT 0,200
";

header('Content-type: application/json');

$xSVC       = new MQLService($action, $sql);
echo $xSVC->getJSON();
?>