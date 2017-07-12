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
include_once "../core/core.fechas.inc.php";
include_once "../core/core.config.inc.php";

$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<?php
echo getRawHeader();
?>
<span class="Estilo1">
<!-- -->
<?php
$idgarantia = parametro("clave", false, MQL_INT);
$idgarantia = parametro("i", $idgarantia, MQL_INT);

	if (!$idgarantia) {
		exit($msg_rpt_exit);
	}
	//-------------------------------------------
	echo "<center><big><b>RECIBO DE GARANTIA EN RESGUARDO</b></big></center>
		<hr>";
	//-------------------------------------------
	$sqllg = "SELECT * FROM  creditos_garantias WHERE idcreditos_garantias=$idgarantia";
	$rslg = mysql_query($sqllg);
		while($rwg = mysql_fetch_array($rslg)) {
		$idsocio = $rwg[1];			// numero de socio
		$idsolicitud = $rwg[2];		// numero de solicitud
		$idsol = $rwg[2];
		$iduser = $rwg[12];
		$nombre = getNombreSocio($idsocio);
		$domicilio = sociodom($idsocio);
		echo "<table  >
			<tr>
			<td class='ths'>Nombre del Socio</td><td>$nombre</td>
			<td class='ths'>Domicilio</td><td>$domicilio</td>
			</tr>
		</table>";
		$montoaut= volcarsol($idsolicitud, 4);
		$fvencimiento = volcarsol($idsolicitud, 15);
		$idtcred  = volcarsol($idsolicitud, 11);
		$modalidad = eltipo("creditos_modalidades", $idtcred); // */

		echo "<table  >
			<tr>
				<td class='ths'>Numero de Solicitud</td><td>$idsolicitud</td>
				<td class='ths'>Modalidad del credito</td><td>$modalidad</td>
			</tr>
			<tr>
				<td class='ths'>Monto Autorizado</td><td>$montoaut</td>
				<td class='ths'>Fecha de Vencimiento</td><td>$fvencimiento</td>
			</tr>
			</table>
			<hr>";
			$tipogar = eltipo("creditos_tgarantias", $rwg[3]);
			$tipoval = eltipo("creditos_tvaluacion", $rwg[6]);
			echo "<table  >
				<tr>
				<td class='ths'>Identificador</td><td>$rwg[0]</td>
				<td class='ths'>Tipo de Garantia</td><td>$tipogar</td>
				</tr>

				<tr>
				<td class='ths'>Fecha de Resguardo</td><td>$rwg[11]</td>
				<td class='ths'>Fecha de Alta</td><td>$rwg[4]</td>
				</tr>

				<tr>
				<td class='ths'>Tipo de Valuacion</td><td>$tipoval</td>
				<td class='ths'>Monto valuado</td><td>$rwg[7]</td>
				</tr>

				<tr>
				<td class='ths'>Documento Presentado</td><td>$rwg[9]</td>
				<td class='ths'>Observaciones del Resg.</td><td>$rwg[19]</td>
				</tr>

				</table>
				<p class='ths'>Las Garantias se Entregar Una vez ya Liquidado el credito</p>";

		}
	@mysql_free_result($rslg);
	echo "<hr><table border='0' width='100%'>
	<tr>
		<td><center>Entrega la Garantia</td>
		<td><center>Recibe en Resguardo</center></td>
		<td><center>Vo. Bo.</center></td>
	</tr>
	<tr>
		<td colspan='2'><br /><br /><br /></td>
	</tr>
	<tr>
		<td><center>$nombre</center></td>
		<td><center>$oficial</center></td>
		<td><center>$titular_gerencia</center></td>
	</tr>
	<tr>
		<td colspan='3' style='font-size: smallest; '>Nota: Al Momento de la liquidaci&oacute;n del Pr&eacute;stamo, la devoluci&oacute;nde garant&iacute;as ser&aacute; exclusivamente al propietario del mismo,
o en caso de ser una persona diferente, favor de traer carta poder notariado. Si su Pr&eacute;stamo fue Hipotecario, deber&aacute; de solicitar adem&aacute;s una constancia de finiquito para tramitar la cancelaci&oacute;n
de la hipoteca, el cual correr&aacute; a cuenta de usted</td>
	</tr>
	</table>";

?>
<!-- -->
</span>
<?php
echo getRawFooter();
?>
</body>
</html>
