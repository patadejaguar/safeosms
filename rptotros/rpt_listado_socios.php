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

$ide = $_GET["pa3"];
	
	$filter = "";
$output = $_GET["out"];
$oficial = elusuario($iduser);
// exporta si OUT == XLS
	if ($output == "xls") {
	//exportar_xls($sqlb02_ext, "tituloRAD");
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=midori.xls");

	} else {

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Listado de Personas</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<?php
	}
?>
<body>
<!-- -->
<?php
echo getRawHeader();
echo "<p class='bigtitle'>LISTADO DE PERSONAS</p><hr></hr>";

	$rs = mysql_query($sqlb01a);

		while($rw = mysql_fetch_array($rs)) {
		echo "<hr></hr>
			<table border='0' width='65%'>
		<tr>
			<th>Numero de Caja Local</th><td>$rw[0]</td>
			<th>Nombre de la Caja Local</th><td>$rw[1]</td>
			<th>Region</th><td>$rw[2]</td>
		</tr>
	</table>
		<hr></hr>";
				if (($ide) && ($ide!="")) {
						$filter = " AND socios_general.estatusactual=$ide AND socios_general.cajalocal=$rw[0]";
				} else {		
						$filter = " AND socios_general.estatusactual=10  AND socios_general.cajalocal=$rw[0]";
				}
			sqltabla($sqlb02_ext . $filter, "", "fieldnames");
		}

	
echo getRawFooter();
?>
</body>
</html>