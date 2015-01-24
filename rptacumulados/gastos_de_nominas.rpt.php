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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.fechas.inc.php";


$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reporte de Gastos por Nomina</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<!--  -->
<?php
echo getRawHeader();
/**
 * Filtrar si hay Fecha
 */
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$si_es_por_fecha 	= "";

//Tipo de Operacion
$def_type = 120;


$inputG = $_GET["outg"];

if ($fecha_inicial && $fecha_final){
	$si_es_por_fecha = " AND `nominas_movimientos`.`fecha_de_mvto>='$fecha_inicial'
						AND `nominas_movimientos`.`fecha_de_mvto<='$fecha_final' ";
}

$sql = "SELECT
	`nominas_movimientos`.`concepto`,
	`nominas_conceptos`.`descripcion`,
	SUM(`nominas_movimientos`.`monto`)
FROM
	`nominas_movimientos` `nominas_movimientos`
		INNER JOIN `nominas_conceptos` `nominas_conceptos`
		ON `nominas_movimientos`.`concepto` =
		`nominas_conceptos`.`codigo`
WHERE
	(`nominas_movimientos`.`fecha_de_mvto` >=CURDATE())
	$si_es_por_fecha
GROUP BY
	`nominas_movimientos`.`concepto`
";

echo "<p class='midtitle'>Acumulado de Conceptos de Nomina<br />Del  " . fecha_corta($fecha_inicial) . " Al " . fecha_corta($fecha_final) . " </p>";

	$rs				= mysql_query($sql);
	//echo $sql;
	$gvalues 		= "";
	$gvalues2 		= "";
	$gnames 		= "";
	$i 				= 0;
	$tds 			= "";
	$mnt 			= 0;
	$sm 			= 0;
	$nm 			= 0;

	while ($rw = mysql_fetch_array($rs)){
		if ($gvalues == ""){
			$gvalues	= "$rw[2]";
			$gvalues2 	= "$rw[1]";
		} else {
			$gvalues 	= $gvalues . "|$rw[2]";
			$gvalues2 	= $gvalues2 .  "|$rw[1]";
		}
		if ($gnames == ""){
			$gnames 	= "$rw[0]";
		} else {
			$gnames 	= $gnames . "|$rw[0]";
		}
	$mnt 				= cmoney($rw[2]);
	$sm 				= $sm + $rw[2];
	$nm					= $nm + $rw[1];


	$tds = $tds . "<tr>
	<td>$rw[0]</td>
	<td>$rw[1]</td>
	<td>$mnt</td>
	</tr>";
		$i++;
	}
	//echo $gnames;
	//echo $gvalues;
	$sm = cmoney($sm);

	echo "<table  >
	<tr>
		<th>Tipo de Convenio</th>
		<th>Numero de Operaciones</th>
		<th>Cantidad</th>
	</tr>

	$tds

	<tr>
		<td>Sumas</td>
		<th>$nm</th>
		<th>$sm</th>
	</tr>
	</td>";

if($inputG == "yes"){

	$tmpKey			= md5(date("Ymdhsi") . $iduser. getRndKey());


	echo "<img src=\"../utils/image.php?file=$tmpKey&type=png\" />";
	}
	echo getRawFooter();
?>
</body>
</html>