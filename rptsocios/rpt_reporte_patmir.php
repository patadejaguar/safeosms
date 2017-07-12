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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");

/** $id = $_GET["id"];
	if (!$id){
		echo $regresar;
		exit;
	}	*/

$input 				= $_GET["out"];
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$sucursal			= $_GET["f700"];

$BySucursal			= "";
if ( isset($sucursal) ){
	$BySucursal		= " AND sucursal = '$sucursal' ";
}
$oficial = elusuario($iduser);
$sql 	= "SELECT * FROM socios_general WHERE estatusactual!=20
 				AND fechaalta>='$fecha_inicial'
				AND fechaalta<='$fecha_final'
				$BySucursal
			ORDER BY fechaalta";
$contar	= 1;
$rs		= mysql_query($sql, cnnGeneral());
$tbHEAD	= "
		<tr>
			<th>No</th>
			<th>Clave de Persona</th>
			<th>Fecha de registro como Socio</th>
			<th>Nombre 1</th>
			<th>Nombre 2</th>
			<th>Apellido Paterno</th>
			<th>Apellido Materno</th>
			<th>Calle/No</th>
			<th>Colonia</th>
			<th>Delegacion/Municipio</th>
			<th>C&oacute;digo Postal</th>
			<th>Ciudad</th>
			<th>Estado</th>
		</tr>
";
$trs	= "";
	while($rw = mysql_fetch_array($rs)){
		$socio		= $rw["codigo"];
		$nombres	= str_replace("_", " ", $rw["nombrecompleto"]);
		$nombres	= explode(" ", $nombres, 2);
		$fecha		= date("d.m.y", strtotime($rw["fechaalta"]) );
		$nombre1	= $nombres[0];
		//Obtiene el segundo nombre mediante el array
		$nombre2	= trim($nombres[1]);
		//
		$apPaterno	= $rw["apellidopaterno"];
		$apMaterno	= $rw["apellidomaterno"];
		//==========			Datos del Domicilio
		$CalleNumeroExInt		= "";
		$Colonia				= "";
		$MunicipioDelegacion	= "";
		$CodigoPostal			= "";
		$Ciudad					= "";
		$Estado					= "";

		$DDom		= getDatosDomicilio($socio, 99);
		$DomID		= $DDom["idsocios_vivienda"];
		if ( isset($DomID) ){
			$CalleNumeroExInt		= trim($DDom["calle"]) . " " . trim( $DDom["numero_exterior"]) . " " . trim($DDom["numero_interior"]);
			$CalleNumeroExInt		= trim($CalleNumeroExInt);
			$Colonia				= $DDom["colonia"];
			$MunicipioDelegacion	= $DDom["municipio"];
			$Ciudad					= $DDom["localidad"];
			$Estado					= $DDom["estado"];
			$CodigoPostal			= $DDom["codigo_postal"];
		}
		$trs	.= "<tr>
						<td>$contar</td>
						<td>$socio</td>
						<td>$fecha</td>
						<td>$nombre1</td>
						<td>$nombre2</td>
						<td>$apPaterno</td>
						<td>$apMaterno</td>
						<td>$CalleNumeroExInt</td>
						<td>$Colonia</td>
						<td>$MunicipioDelegacion</td>
						<td>$CodigoPostal</td>
						<td>$Ciudad</td>
						<td>$Estado</td>
					</tr>";
	$contar++;
	unset($DDom);
	}
	$table = "
		<table width='100%'>
		<thead>
			$tbHEAD
		</thead>
		<tbody>
			$trs
		</tbody>
		</table>
	";
if ($input!=OUT_EXCEL) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<!-- -->
<?php
echo getRawHeader();

echo $table;

echo getRawFooter();
?>
</body>
<script  >
<?php

?>
function initComponents(){
	window.print();
}
</script>
</html>
<?php
} else {
	$filename = $_SERVER['SCRIPT_NAME'];
	$filename = str_replace(".php", "", $filename);
	$filename = str_replace("rpt", "", $filename);
	$filename = str_replace("-", "", 	$filename);
  	$filename = "$filename-" . date("YmdHi") . "-from-" .  $iduser . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");

	//$cTbl = new cTabla($setSql);
	//$cTbl->setWidth();
	//$cTbl->Show("", false);
	echo $table;
}
?>