<?php
/**
 * Titulo: Planeacion de Creditos- Formato
 * @since Actualizado: 27-Agosto-2007
 * @author Responsable: Balam Gonzalez Luis
 * @version 1.2
 * @package creditos
 * @subpackage formatos
 * 20080722	Se Agrego Mejoras en la presentacion
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
include_once "../core/core.common.inc.php";

$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>PLANEACION DEL CREDITO EN GRUPOS SOLIDARIOS</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<?php
echo getRawHeader();
	$title			= "PLANEACION DEL CREDITO EN GRUPOS SOLIDARIOS EN ETAPA DE AUTORIZACION";
	$thTit			= "Monto Autorizado";
	$idplan 		= $_GET["plan"];
	$tipo_docto		= 40;
	$rs_find_plan	= false;

	if (!isset($idplan) ) {
		$idgrupo 			= $_GET["on"];
		$sql_find_plan 		= "SELECT * FROM operaciones_recibos WHERE grupo_asociado=$idgrupo AND tipo_docto=14 LIMIT 0,1";
		$rs_find_plan 		= obten_filas($sql_find_plan);
		$idplan				= $rs_find_plan["idoperaciones_recibos"];
	}

	//
	echo "";
	//

	//$rsrain 	= mysql_query($sqlrain);
	$xRec			= new cReciboDeOperacion($tipo_docto, false, $idplan);
	$xRec->setNumeroDeRecibo($idplan, true, $rs_find_plan);
	$rsr			= $xRec->getDatosReciboInArray();
	//while ($rsr = mysql_fetch_array($rsrain)) {
	$idgrupo 		= $rsr["grupo_asociado"];
	$tipo_docto		= $rsr["tipo_docto"];
	//
	$sql_infogroup	= "select * from socios_grupossolidarios ";
	$sql_infogroup 	.= " where idsocios_grupossolidarios=$idgrupo";
	$lafila 		= the_row($sql_infogroup);
	//
	$nombregrupo 	= $lafila["nombre_gruposolidario"];
	$presidenta 	= $lafila["representante_numerosocio"];
	$mynom 			= getNombreSocio($presidenta);
	$nomrep 		= $lafila["representante_nombrecompleto"];
	$nomvv 			= $lafila["vocalvigilancia_nombrecompleto"];

		if ($tipo_docto	== 14 ){
			$title			= "PLANEACION DEL CREDITO EN GRUPOS SOLIDARIOS EN ETAPA DE SOLICITUD";
			$thTit			= "Monto Solicitado";
		}

		echo "
<p class='bigtitle'>$title</p>
<hr />
<table width='100%' border='0'>
		<tr>
			<th class='izq'>Referencia</th>
			<td>" . $rsr["idoperaciones_recibos"] . "</td>
			<th class='izq'>Fecha de Elaboracion</th>
			<td>" . getFechaLarga($rsr["fecha_operacion"]) . "</td>
		</tr>
		<tr>
			<th class='izq'>Codigo de Grupo</th>
			<td>$idgrupo</td>
			<th class='izq'>Nombre de Grupo</th>
			<td>$nombregrupo</td>
		</tr>
		<tr>
			<th class='izq'>Responsable del Grupo</td>
			<td>$nomrep</td>
			<th class='izq'>Vocal de Vigilancia del Grupo</td>
			<td>$nomvv</td>
		</tr>
	</table>
	<hr />";
		$suma 		= 0;
		$sqlsun 	= "SELECT * FROM operaciones_mvtos
								WHERE recibo_afectado=" . $rsr["idoperaciones_recibos"] . "
										AND grupo_asociado=$idgrupo";
		$rss 	= mysql_query($sqlsun);
		echo "<table width='100%' border='0'>
		<tr>
      <th>Socia(o)</th>
      <th>C.U.R.P.</th>
		  <th>Nombre Completo</th>
		  <th>$thTit</th>
		  <th>Firma</th>
    </tr>";
			while($rws = mysql_fetch_array($rss)) {
			$xSoc  = new cSocio($rws["socio_afectado"], true);
			$DS    = $xSoc->getDatosInArray();
			$socia = $xSoc->getNombreCompleto();
			$curp  = $DS["curp"];
			$suma = $suma + $rws["afectacion_real"];
				echo "<tr>
				<td class='ctr'>" . $rws["socio_afectado"] . "</td>
				<td>$curp</td>
				<td>$socia</td>
				<td class='mny'>" . getFMoney($rws["afectacion_real"]) . "</td>
				<td><br /><br /><br />______________________</td>
				</tr>";
			}
			$letters = convertirletras($suma);
			$suma = getFMoney($suma);
		echo "<td>SUMA DE LA PLANEACION</td><th>$letters</th><th>$suma</th></table>
		<p>Manifestamos Bajo Protesta de Decir Verdad que las personas que integramos este grupo, nos caracterizamos
		por tener gran solvencia Moral, al ser Honestas y responsables, asi como tener la Suficiente solvencia
		Economica para cubrir el Monto Manifestado en esta Cedula.</p>
		<p>El Total de la Planeacion del Credito sera considerado como el Monto del Credito Solicitado.</p>";
		@mysql_free_result($rss);

		echo "<hr /><table border='0' width='100%'>
	<tr>
	<td><center>Firma del Solicitante<br>
	Bajo Protesta de Decir Verdad</center></td>
	<td><center>Procesa la Planeaci&oacute;n</center></td>
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
