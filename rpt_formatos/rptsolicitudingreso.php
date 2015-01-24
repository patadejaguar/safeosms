<?php
/**
 * @version 1.2.0
 * @author Balam Gonzalez Luis
 * @date 30/03/2008
 */
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
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";

$idsocio = $_GET["socio"];
	if (!$idsocio){
		echo "EL SOCIO NO EXISTE";
		exit;
	}
	
$oficial = elusuario($iduser);
//TODO: Modificar las fichas
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<?php
	echo getRawHeader();
?>
<hr>
<p class="bigtitle">SOLICITUD DE INGRESO</p>
<hr>
<?php /* Obtiene los Datos generales del Socio */

/* OBTIENE EL SQL */
$sqls 		= "SELECT * FROM socios_general WHERE codigo=$idsocio";
$datos 		= obten_filas($sqls);
$fecha 		= $datos["fechaentrevista"];				// fecha de Entrevista
$nom 		= $datos["nombrecompleto"];
$apmat 		= $datos["apellidomaterno"];
$appat 		= $datos["apellidopaterno"];
$rfc 		= $datos["rfc"];
$curp 		= $datos["curp"];
$idcivil 	= $datos["estadocivil"];
	$ncivil = eltipo("socios_estadocivil", $idcivil);
$fnac		= $datos["fechanacimiento"];				// Fecha de Nacimiento
$lnac 		= $datos["lugarnacimiento"];				// Lugar de Nacimiento
$idreg 		= $datos["region"];							// Id region
	$nreg 	= eltipo("socios_region", $idreg);			// Nombre Region
$idcl 		= $datos["cajalocal"];						// id CajaLocal
	$ncl 	= eltipo("socios_cajalocal", $idcl);		// Nombre Caja Local		
$idti 		= $datos["tipoingreso"];					// Id Tipo ingreso
	$nti 	= eltipo("socios_tipoingreso", $idti);		// Nombre Tipo Ingreso
$idgen 		= $datos["genero"];							// Id Genero
	$ngen 	= eltipo("socios_genero", $idgen);			// Nombre Genero

$mynom = getNombreSocio($idsocio);

//$nombre = getNombreSocio($idsocio);
//$domi = socio_dom($idsocio);
//
echo "	<fieldset>
		<legend>| DATOS GENERALES DE LA PERSONA |</legend>
	<table border='0'  >
	<tr>
	<td>Fecha de Solicitud</td><td>$fecha</td>
	<td>Codigo Asignado</td><td>$idsocio</td>
	</tr>
	<tr>
	<td>Tipo de Persona</td><td>$nti</td>
	<td>Nombre(s) </td><td>$nom</td>
	</tr>
	<tr>
	<td>Apellido Paterno</td><td>$appat</td>
	<td>Apellido Materno</td><td>$apmat</td>
	</tr>
	<tr>
	<td>R. F. C. </td><td>$rfc</td>
	<td>C. U. R. P.</td><td>$curp</td>
	</tr>
	<tr>
	<td>Fecha de Nacimiento</td><td>$fnac</td>
	<td>Lugar de Nacimiento</td><td>$lnac</td>
	</tr>
	<tr>
	<td>Genero</td><td>$ngen</td>
	<td>Estado Civil</td><td>$ncivil</td>
	</tr>
	<tr>
	<td>Caja Local</td>
	<td>$ncl</td>
	<td>Region</td>
	<td>$nreg</td>
	</tr>
	</table>";
echo "
	</fieldset>
	<fieldset>
		<legend>| DATOS DE LA VIVIENDA/DOMICILIO(S) |</legend>";
	
	$rowviv	= getDatosDomicilio($datos["codigo"], 99);
	
	$tviv = eltipo("socios_viviendatipo", $rowviv[16]);
	$treg = eltipo("socios_regimenvivienda", $rowviv[2]);
	$tres = eltipo("socios_tiempo", $rowviv[12]);

	echo "<table border='0' width='100%' aling='center'>
		<tr>
			<td>Tipo de Domicilio</td>
			<td class='ths'>$tviv</td>
			<td>Regimen de Vivienda</td>
			<td>$treg</td>
		</tr>
		<tr>
			<td>Calle </td>
			<td>$rowviv[3]</td>
			<td>Num. Ext.</td>
			<td>$rowviv[4]</td>
		</tr>
		<tr>
			<td>Num. Int.</td>
			<td>$rowviv[5]</td>
			<td>Colonia</td>
			<td>$rowviv[6]</td>
		</tr>
			<td>Localidad</td>
			<td>$rowviv[7]</td>
			<td>Municipio</td>
			<td>" . $rowviv["municipio"] . "</td>
		<tr>
			<td>Telefono Residencial</td>
			<td>$rowviv[10]</td>
			<td>Telefono Movil</td>
			<td>$rowviv[11]</td>
		</tr>
		<tr>
			<td>Codigo Postal</td>
			<td>" . $rowviv["codigo_postal"] . "</td>
			<td>Tiempo de Residencia</td>
			<td>$tres</td>
		</tr>
		<td>Referencia</td>
		<td>" . $rowviv["referencia"] . "</td>
		</table>
		
		</fieldset>
		<fieldset>
		<legend>| PARTES RELACIONADAS |</legend>";
		// Tipo de Parte numero de Socio nombres Apellido Materno, paterno, CURP
	$spr = "SELECT * FROM socios_relaciones WHERE socio_relacionado=$idsocio and tipo_relacion<50 ORDER BY tipo_relacion";
	
	$rspr = mysql_query($spr, cnnGeneral());
	while ($rwpr = mysql_fetch_array($rspr)) {
	$trelacion = eltipo("socios_relacionestipos", $rwpr[3]);
	$tcons = eltipo("socios_consanguinidad", $rwpr[18]);
	
	echo "<table border='0'  >
		<tr>
		<td>Tipo de Relacion</td><td class='ths'>$trelacion</td>
		<td>Numero de socio Propio</td><td>$rwpr[4]</td>
		</tr>
		<tr>
		<td>C.U.R.P</td><td>$rwpr[15]</td>
		<td>Nombre(s)</td><td>$rwpr[5]</td>
		</tr>
		<tr>
		<td>Apellido Paterno</td><td>$rwpr[6]</td>
		<td>Apellido Materno</td><td>$rwpr[7]</td>
		</tr>
		<tr>
		<td>Parentesco</td><td>$tcons</td>
		<td>Domicilio Completo</td><td>$rwpr[8]</td>
		</tr>
		<tr>
		<td>Telefono Fijo</td><td>$rwpr[9]</td>
		<td>Telefono Movil</td><td>$rwpr[10]</td>
		</tr>
		</table>";
	}
	echo "
		</fieldset>
		<fieldset>
		<legend>| DATOS DE LA(S) ACTIVIDAD(ES) ECONOMICA(S) |</legend>";

	$sae = "SELECT * FROM socios_aeconomica WHERE socio_aeconomica=$idsocio";
	$rsae = mysql_query($sae, cnnGeneral());
	while ($rwae = mysql_fetch_array($rsae)) {
	$taec			= eltipo("socios_aeconomica_tipos", $rwae[2]);
	$tse 			= eltipo("socios_aeconomica_sector", $rwae[3]);
	$antiguedad 	= eltipo("socios_tiempo", $rwae[12]);

	echo "<table border='0' width='100%'>
		<tr>
		<td>Tipo de Actividad</td><td class='ths'>$taec</td>
		<td>Sector Economico</td><td>$tse</td>
		</tr>
		<tr>
		<td>Nombre o Razon Social</td><td>$rwae[4]</td>
		<td>Domicilio</td><td>$rwae[5]</td>
		</tr>
		<tr>
		<td>Telefono</td><td>$rwae[9]</td>
		<td>Telefono M&oacute;vil</td><td>$rwae[10]</td>
		</tr>
		<tr>
		<td>Departamento</td><td>$rwae[13]</td>
		<td>Antig?edad</td><td>$antiguedad</td>
		</tr>
		<tr>
		<td></td><td></td>
		<td></td><td></td>
		</tr>
		</table>";
	}
	@mysql_free_result($rsae);
/* ---------------------------------------------------------------- */	
		echo "</fieldset>
		<fieldset>
		<legend>| DATOS DE RELACION PATRIMONIAL |</legend>";
		$sqlactivos = "SELECT socios_patrimoniotipo.descripcion_patrimoniotipo AS 'Tipo_de_patrimonio', socios_patrimonio.monto_patrimonio AS 'Monto_Patrimonio', ";
		$sqlactivos .= " socios_patrimonio.fecha_expiracion AS 'Fecha_Expiracion', socios_patrimonio.documento_presentado AS 'Documento_Presentado' ";
		$sqlactivos .= " FROM socios_patrimonio, socios_patrimoniotipo WHERE socios_patrimonio.socio_patrimonio=$idsocio ";
		$sqlactivos .= " AND socios_patrimoniotipo.idsocios_patrimoniotipo=socios_patrimonio.tipo_patrimonio";
			// activos.
			//echo $sqlactivos;
			sqltabla($sqlactivos, "", "fieldnames"); 
			// pasivo.

	echo "</fieldset>
	<table border='0' width='100%'>
	<tr>
	<td><center>Firma de la Representante<br>
	Bajo Protesta de Decir Verdad</center></td>
	<td><center>Recepciona la Solicitud</center></td>
	</tr>
	<tr>
	<td>
		<br />
		<br />
		<br />
	</td>
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