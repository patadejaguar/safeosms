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
$nombre		= (isset($_GET["nombre"])) ? trim($_GET["nombre"]) : "";

$ByNombre	= ($nombre != "") ? " AND nombrecompleto LIKE '%$nombre%' " : "";

$estado		= substr(DEFAULT_NOMBRE_ESTADO, 0,3);

$sqllike 	= "SELECT *
	FROM
		`general_colonias`
	WHERE
		`nombre_colonia` LIKE '%" . $nombre . "%'
		AND estado_colonia LIKE '%" . $estado . "%'
	ORDER BY
		`nombre_colonia`
	LIMIT 0,20 ";
$rs		= getRecordset($sqllike);
//$socios	= new medoo(MY_DB_IN);
//$data		= $suc->select(, "*");

while($rw	= mysql_fetch_array($rs)){
	$txt	.= "<colonia codigo=\"" . $rw["codigo_postal"] . "\" id=\"" . $rw["idgeneral_colonia"] . "\" >";
	$txt	.= "<![CDATA[";
	$txt	.= htmlentities($rw["nombre_colonia"]);
	//$txt	.= " " . $rw["nombre_colonia"];
	$txt	.= "-" . htmlentities($rw["municipio_colonia"]) . "," . htmlentities($rw["estado_colonia"]);
	$txt	.= "]]>";
	$txt	.= "</colonia>\n";
}
echo "<?xml version =\"1.0\" ?>\n<result>\n" . $txt . "</result>";
?>