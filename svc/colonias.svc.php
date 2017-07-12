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

$cp			= parametro("cp", 0, MQL_INT);
$municipio	= parametro("m");
$estado		= parametro("e");
$nombre		= parametro("n");
$limit		= parametro("limit", 100, MQL_INT);
$limit		= parametro("lim", $limit, MQL_INT);
$ByMun		= "";
$ByEst		= "";
$ByCol		= "";
$ByCP		= ( $cp <= 0) ? "": " AND (`general_colonias`.`codigo_postal` = $cp) ";
if($cp <= 0){
	if( setNoMenorQueCero($municipio) > 0){
		$ByMun	= " AND `codigo_de_municipio` = $municipio "; //TODO: checar
	} else {
		$ByMun	= ($municipio == "") ? "" : " AND `municipio_colonia` LIKE '%$municipio%' "; //TODO: checar
	}
	if(setNoMenorQueCero($estado) > 0 ){
		$ByEst	= " AND `codigo_de_estado` = $estado " ;
	} else {
		$ByEst	= ($estado == "") ? "" : " AND `estado_colonia` LIKE '%$estado%' " ;
	}
	$ByCol	= ($nombre == "") ? "" : "  AND ( `nombre_colonia` LIKE '%$nombre%'  ) ";
}
$action		= "LIST";

$sql 	= "SELECT
	`general_colonias`.`idgeneral_colonia`   AS `codigo`,
	`general_colonias`.`codigo_postal`       AS `clavepostal`,
	CONCAT(`tipo_colonia`, ' ', `nombre_colonia`, '(', `municipio_colonia`, ', ', `estado_colonia`, ')') AS 'nombre',
	`general_colonias`.`codigo_de_estado`    AS `estado`,
	`general_colonias`.`codigo_de_municipio` AS `municipio`,
	`general_colonias`.`nombre_colonia` AS `colonia`,
	
	`general_municipios`.`nombre_del_municipio`,
	`general_estados`.`nombre` AS `nombre_del_estado`,
	CONCAT(`general_colonias`.`codigo_postal`, '-', `general_colonias`.`idgeneral_colonia`) AS 'buscador',
	`general_colonias`.`ciudad_colonia` AS `ciudad`
FROM

	`general_municipios` `general_municipios` 
		INNER JOIN `general_estados` `general_estados` 
		ON `general_municipios`.`clave_de_entidad` = `general_estados`.
		`clave_numerica` 
			INNER JOIN `general_colonias` `general_colonias` 
			ON `general_colonias`.`codigo_de_municipio` = `general_municipios`.
			`clave_de_municipio` AND
			`general_colonias`.`codigo_de_estado` = `general_municipios`.
			`clave_de_entidad`
			

	WHERE `idgeneral_colonia` > 0 $ByCP $ByEst $ByMun $ByCol
	ORDER BY `codigo_de_estado`, `codigo_de_municipio`, `nombre_colonia` LIMIT 0,$limit";

header('Content-type: application/json');
//setLog($sql);
$xSVC       = new MQLService($action, $sql);
echo $xSVC->getJSON();
?>