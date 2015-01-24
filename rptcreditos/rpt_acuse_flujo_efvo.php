<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once ("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		header ("location:../404.php?i=999");	
	}
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$idsolicitud = $_GET["s"];
	
$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<!-- -->
<?php
echo getRawHeader();
//--------------------------------------------------------------------------------------------------
	if (!$idsolicitud){
		exit("NO EXISTE LA SOLICITUD");
	}
//--------------------------------------------------------------------------------------------------
echo "<p class='bigtitle'>DECLARACION DE FLUJO DE EFECTIVO</p>
<hr>";
$sqlsoc = "SELECt numero_socio FROM creditos_solicitud WHERE numero_solicitud=$idsolicitud LIMIT 0,1";
$socio = mifila($sqlsoc, "numero_socio");
$mynom = getNombreSocio($socio);
//-------------------------------------------------------------------------------------------------------------------------

$sqlm = $sqlb15  . " AND solicitud_flujo=$idsolicitud";
//echo $sqlm;
minificha(1, $socio);
echo "<hr>";
sqltabla($sqlm, "", "fieldnames");

$sqlss = "SELECT SUM(afectacion_neta) AS 'sumtrim' FROM $t_cfe WHERE solicitud_flujo=$idsolicitud";
$ssmoun = mifila($sqlss, "sumtrim");
echo "<hr>
	<table><tr>
	<th>CAPACIDAD DE PAGO DIARIA:</th>
	<td>$ $ssmoun</td>
	</tr></table>";
echo "<p>Declaro Bajo protesta de Decir Verdad, que los Datos contenidos en la presente <b>DECLARACION DE FLUJO DE EFECTIVO</b>
son Verdad y que Servira para que Cubra el credito Solicitado con Numero de Control <b>$idsolicitud</b>; Teniendo Conocimiento a 
las Dispocisiones de Ley, contenidas en el Articulo 134 Fraccion I de la Ley de Ahorro y Credito Popular, que indica <b>$ley_art134_i</b>
a quienes Incurren en falsedad de Informacion para Obtener un Prestamo</p>
	
<hr><table border='0' width='100%'>
	<tr>
	<td><center>Firma del Solicitante<br>
	Bajo Protesta de Decir Verdad</center></td>
	<td><center>Recibe la Solicitud</center></td>
	</tr>
	<tr>
	<td><br><br><br></td>
	</tr>
	<tr>
	<td><center>$mynom</center></td>
	<td><center>$oficial</center></td>
	</tr>
	</table>";

echo getRawFooter();
?>
</body>
</html>
