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
$xInit      = new cHPage("", HP_SERVICE );
$txt		= "";
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT);// $nombre	= parametro("n", $nombre);
$nombre		= parametro("nombre"); $nombre	= parametro("n", $nombre);
$apaterno	= parametro("apaterno"); $apaterno	= parametro("p", $apaterno);
$amaterno	= parametro("amaterno"); $amaterno	= parametro("m", $amaterno);


$action		= "LIST";
$ByPersona	= (setNoMenorQueCero($persona) <= DEFAULT_SOCIO) ? " codigo != " . DEFAULT_SOCIO . "" : " codigo = $persona ";
$ByNombre	= ($nombre != "") ? " AND nombrecompleto LIKE '%$nombre%' " : "";
$ByAPaterno	= ($apaterno != "") ? " AND (apellidopaterno LIKE '%$apaterno%' OR nombrecompleto LIKE '%$apaterno%') " : "";
$ByAMaterno	= ($amaterno != "") ? " AND apellidomaterno LIKE '%$amaterno%' " : "";

$sql 	= "SELECT
		`socios_general`.`codigo`          AS `codigo`,
		CONCAT(`socios_general`.`apellidopaterno`, ' ',
		`socios_general`.`apellidomaterno`, ' ',
		`socios_general`.`nombrecompleto`)  AS `nombrecompleto`,
		
		`socios_general`.`apellidopaterno` ,
		`socios_general`.`apellidomaterno`,
		`socios_general`.`nombrecompleto` AS 'nombre'
	FROM
		`socios_general` `socios_general`
	WHERE
		$ByPersona
		$ByNombre
		$ByAPaterno
		$ByAMaterno
	ORDER BY
		`socios_general`.`apellidopaterno`,
		`socios_general`.`apellidomaterno`,
		`socios_general`.`nombrecompleto`
	LIMIT 0,20 ";

header('Content-type: application/json');

$xSVC       = new MQLService($action, $sql);
echo $xSVC->getJSON();
?>