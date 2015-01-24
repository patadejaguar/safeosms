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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once ("../core/entidad.datos.php");
include_once ("../core/core.deprecated.inc.php");
include_once ("../core/core.fechas.inc.php");
include_once ("../core/core.config.inc.php");
include_once ("../core/core.common.inc.php");
include_once ("../libs/medoo.min.php");
header("Content-type: text/xml");
$txt		= "";
$nombre		= parametro("nombre");
$apaterno	= parametro("apaterno");
$amaterno	= parametro("amaterno");

$ByNombre	= ($nombre != "") ? " AND nombrecompleto LIKE '%$nombre%' " : "";
$ByAPaterno	= ($apaterno != "") ? " AND apellidopaterno LIKE '%$apaterno%' " : "";
$ByAMaterno	= ($amaterno != "") ? " AND apellidomaterno LIKE '%$amaterno%' " : "";

$sqllike 	= "SELECT
		`socios_general`.`codigo`          AS `codigo`,
		`socios_general`.`apellidopaterno` AS `apellido_paterno`,
		`socios_general`.`apellidomaterno` AS `apellido_materno`,
		`socios_general`.`nombrecompleto`  AS `nombres`,
		`socios_general`.`curp`
	FROM
		`socios_general` `socios_general`
	WHERE
		codigo != " . DEFAULT_SOCIO . "
		$ByNombre
		$ByAPaterno
		$ByAMaterno
	ORDER BY
		`socios_general`.`apellidopaterno`,
		`socios_general`.`apellidomaterno`,
		`socios_general`.`nombrecompleto`
	LIMIT 0,10 ";
$rs		= getRecordset($sqllike);
//$socios	= new medoo(MY_DB_IN);
//$data		= $suc->select(, "*");

while($rw	= mysql_fetch_array($rs)){
	$txt	.= "<persona codigo=\"" . $rw["codigo"] . "\">";
	$txt	.= "";// . $rw[""] . "</socio>";
	$txt	.= $rw["nombres"];
	$txt	.= " " . $rw["apellido_paterno"];
	$txt	.= " " . $rw["apellido_materno"];
	$txt	.= "</persona>\n";
}

echo "<?xml version =\"1.0\" ?>\n<result>\n" . $txt . "</result>";
?>