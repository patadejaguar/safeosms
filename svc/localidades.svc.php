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

$municipio	= parametro("m");
$estado		= parametro("e");
$nombre		= parametro("n");

$ByCP	= "";//($cp == "") ? "": " AND (`general_colonias`.`codigo_postal` = $cp) ";
$ByMun	= ""; //( setNoMenorQueCero($municipio) <= 0 ) ? "" : " AND (`general_municipios`.`clave_de_municipio` ='$municipio') "; //TODO: checar
$ByEst	= ( setNoMenorQueCero($estado <= 0) ) ? "" : " AND (`catalogos_localidades`.`clave_de_estado` ='$estado') " ;
$ByCol	= ($nombre == "") ? "" : "  AND (`catalogos_localidades`.`nombre_de_la_localidad` LIKE '%$nombre%'  ) ";

$action		= "LIST";
/*
	`catalogos_localidades`.`clave_unica`,
	`catalogos_localidades`.`nombre_de_la_localidad`,
	`catalogos_localidades`.`clave_de_estado`,
	`catalogos_localidades`.`clave_de_municipio`,
	`catalogos_localidades`.`clave_de_localidad`,
	`catalogos_localidades`.`longitud`,
	`catalogos_localidades`.`altitud`,
	`catalogos_localidades`.`latitud`,
	`catalogos_localidades`.`clave_de_pais` 
*/
$sql 	= "
SELECT
	
	`catalogos_localidades`.* 
FROM
	`catalogos_localidades` `catalogos_localidades`
WHERE
	`clave_unica` > 0  $ByCP $ByEst $ByMun $ByCol
	LIMIT 0,20
";

header('Content-type: application/json');

$xSVC       = new MQLService($action, $sql);
echo $xSVC->getJSON();
?>