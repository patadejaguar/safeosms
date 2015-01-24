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
include_once( "../core/entidad.datos.php");
include_once( "../core/core.deprecated.inc.php");
include_once( "../core/core.fechas.inc.php");
include_once( "../libs/sql.inc.php");
include_once( "../core/core.config.inc.php");

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reporte de Monto de Solicitudes en un Rango de Fechas</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<!--  -->
<?php
echo getRawHeader();
/**
 * Filtrar si hay Fecha
 */
$fecha_inicial = $_GET["on"];
$fecha_final = $_GET["off"];
$si_es_por_fecha = "";
if ($fecha_inicial && $fecha_final){
	$si_es_por_fecha = " WHERE fecha_solicitud>='$fecha_inicial' AND fecha_solicitud<='$fecha_final' ";
}
$sql = "select convenio, count(solicitud) AS 'numero', FORMAT(SUM(monto_solicitado),2) AS 'total_colocado' FROM solicitudes $si_es_por_fecha AND monto_solicitado>=0.99 GROUP BY convenio";
echo "<p class='midtitle'>Reporte de Monto de Solicitudes en un Rango de Fechas <br />Del $fecha_inicial Al $fecha_final</p>";
sqltabla($sql,"","fieldnames");
echo getRawFooter();
?>
</body>
</html>
