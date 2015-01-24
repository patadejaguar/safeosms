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
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<?php
echo getRawHeader();

$idsolicitud = $_GET["solicitud"];
	if (!$idsolicitud) {
		exit("NO HAY SUFICIENTES DATOS
		PARA LLEVAR A CABO EL INFORME");		
	}

?>
<!-- -->
<p class="bigtitle">CEDULA DE AUTORIZACI&Oacute;N/INFORMACI&Oacute;N</p>
<hr>
<?php
	$sqlcred = "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$idsolicitud";
	$rscred = mysql_query($sqlcred);
	while($rwc = mysql_fetch_array($rscred)) {
	// datos generales del socio
	$idsocio = $rwc["numero_socio"];	// Numero de Socio
	$sqlmysoc = "SELECT * FROM socios_general WHERE codigo=$idsocio";
	$rsmysoc = mysql_query($sqlmysoc);
	$mynom = getNombreSocio($idsocio);
		while($rwy = mysql_fetch_array($rsmysoc)) {
		$genero = eltipo("socios_genero", $rwy[15]);
		$civil = eltipo("socios_estadocivil", $rwy[14]);
		$ocupacion = volcartabla("socios_aeconomica", 18, "socio_aeconomica=$idsocio");
		$thisdom = sociodom($idsocio);
			echo "<table border='0'  >
				<tr>
					<td class='ths'>Clave de Persona</td><td>$rwy[0]</td>
					<td class='ths'>Nombre(s)</td><td>$rwy[1]</td>
				</tr>
				<tr>
					<td class='ths'>Apellido Paterno</td><td>$rwy[2]</td>
					<td class='ths'>Apellido Materno</td><td>$rwy[3]</td>
				</tr>
				<tr>
					<td class='ths'>R. F. C.</td><td>$rwy[4]</td>
					<td class='ths'>C. U. R. P.</td><td>$rwy[5]</td>
				</tr>
				<tr>
					<td class='ths'>Genero</td><td>$genero</td>
					<td class='ths'>Estado Civil</td><td>$civil</td>
				</tr>
				<tr>
					<td class='ths'>Ocupaci&oacute;n</td><td>$ocupacion</td>
				</tr>
				<tr>
					<td>Direcci&oacute;n</td><td colspan='3'>$thisdom</td>
				</tr>

			</table>
			<hr>";
		} //*/
	// datos de la solicitud
	$perpagos = eltipo("creditos_periocidadpagos", $rwc[10]);
	$eldest = eltipo("creditos_destinos", $rwc[19]);
	$montosol = number_format($rwc[4], 2, '.', ',');
	$montoletras = convertirletras($rwc[4]);
	$tasa = $rwc[9] * 100;
	echo "<table  >
		<tr>
		<td class='ths'>Numero de Solicitud</td><td>$rwc[0]</td>
		<td class='ths'>Periocidad de Pago</td><td>$perpagos</td>
		</tr>
		<tr>
		<td class='ths'>Destino del Recurso</td><td>$eldest</td>
		<td class='ths'>Fecha de Vencimiento</td><td>$rwc[15]</td>
		</tr>
		<tr>
		<td class='ths'>Tasa de Interes</td><td>$tasa %</td>
		<td class='ths'>Numero de Pagos</td><td>$rwc[16]</td>
		</tr>
		<tr>
		<td class='ths'>Monto Autorizado</td><td><b>$montosol</b></td>
		<td class='ths' colspan='2'>$montoletras</td>
		</tr>
		</table>";
	
		/** garantias */
		echo "<hr>
		<p class='subt'>RELACION DE GARANTIAS RELACIONADAS CON EL CREDITO</p>
		<hr>";
	
		

			$sqlgarantias = "SELECT idcreditos_garantias FROM creditos_garantias WHERE solicitud_garantia=$idsolicitud";
			$rsga = mysql_query($sqlgarantias);
			while($rwb = mysql_fetch_array($rsga)) {
				$sqlgar = $sqlb17_ext . " AND creditos_garantias.idcreditos_garantias=$rwb[0] ";
				ficha($sqlgar);
			}
			@mysql_free_result($rsga);


	// avales

		echo "<hr>
		<p class='subt'>RELACION DE AVALES RELACIONADOS CON EL CREDITO</p>
		<hr>";
		$sqlavales = "SELECT idsocios_relaciones FROM socios_relaciones WHERE credito_relacionado=$idsolicitud ";
		
			$rsav = mysql_query($sqlavales);
			while($rwb = mysql_fetch_array($rsav)) {
				$sqlav = $sqlb16_ext . " AND socios_relaciones.idsocios_relaciones=$rwb[0] ";
				ficha($sqlav);
			}
			@mysql_free_result($rsav);
	} // end while
	@mysql_free_result($rscred);
	echo "<br /><br >
	Fecha de Pago: ___//________//_____";

echo getRawFooter();
?>
</body>
</html>