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

$cuenta		= parametro("cuenta", false, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$rs			= array("error" => true);

if(setNoMenorQueCero($cuenta) > 0 ){
	$xCta	= new cCuentaDeCaptacion($cuenta);
	if($xCta->init() == true){
		$rs["descripcion"]		= $xCta->getDescription();
		$rs["error"] 			= false;
	}
}

/*$cp			= parametro("cp");
$municipio	= parametro("m");
$estado		= parametro("e");
$nombre		= parametro("n");*/

/*$ByCP	= ($cp == "") ? "": " AND (`general_colonias`.`codigo_postal` = $cp) ";
$ByMun	= ($municipio == "") ? "" : " AND `municipio_colonia` LIKE '%$municipio%' "; //TODO: checar
$ByEst	= ($estado == "") ? "" : " AND `estado_colonia` LIKE '%$estado%' " ;
$ByCol	= ($nombre == "") ? "" : "  AND ( `nombre_colonia` LIKE '%$nombre%'  ) ";
$action		= "LIST";
$sql 	= "SELECT
	`general_colonias`.`idgeneral_colonia`   AS `codigo`,
	`general_colonias`.`codigo_postal`       AS `clavepostal`,
	CONCAT(`tipo_colonia`, ' ', `nombre_colonia`, '(', `municipio_colonia`, ', ', `estado_colonia`, ')') AS 'nombre',
	`general_colonias`.`codigo_de_estado`    AS `estado`,
	`general_colonias`.`codigo_de_municipio` AS `municipio` 
FROM
	`general_colonias` `general_colonias`
	WHERE `idgeneral_colonia` > 0 $ByCP $ByEst $ByMun $ByCol
	ORDER BY `codigo_de_estado`, `codigo_de_municipio`, `nombre_colonia` LIMIT 0,100";*/

header('Content-type: application/json');
echo json_encode($rs);
//$xSVC       = new MQLService($action, $sql);
//echo $xSVC->getJSON();
?>