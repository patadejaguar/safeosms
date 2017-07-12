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
include_once "../core/core.creditos.inc.php";

$oficial 			= elusuario($iduser);
$idgrupo 			= $_GET["idgrupo"];
$idsolicitud 		= $_GET["id"];
$id 				= $_GET["id"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>PAGARE GRUPO SOLIDARIO</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
	<style>
		body{
			text-transform: uppercase;
		}
	</style>
<body>
<?php
	if (!$id) {
		exit($msg_rec_exit  .  $fhtm);
	}

	$select = "SELECT numero_socio, grupo_asociado, monto_autorizado,
					fecha_vencimiento, dias_autorizados, interes_diario,
					tasa_moratorio, periocidad_de_pago
					FROM creditos_solicitud
					WHERE numero_solicitud=$id";

	$rsmill 	= mysql_query($select, cnnGeneral() );
	while ($rt 	= mysql_fetch_array($rsmill)) {
		$monto 		= $rt[2];
		$fecha 		= $rt[3];
		$dias 		= $rt[4];
		$idiario 	= $rt["interes_diario"];
		$imora 		= $rt["tasa_moratorio"];
		$idgrupo 	= $rt["grupo_asociado"];
		$codigorep 	= $rt["numero_socio"];
		$periocidad	= $rt["periocidad_de_pago"];
	}

	if (  EACP_INCLUDE_INTERES_IN_PAGARE == true ){
		if ( $periocidad == 360 ){
			$monto 			= $monto + ($idiario * $dias);
		} else {
			$sqlInt = "SELECT
						`operaciones_mvtos`.`docto_afectado`,
						`operaciones_mvtos`.`tipo_operacion`,
						COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `mvtos`,
							SUM(`operaciones_mvtos`.`afectacion_real` *
							`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'
					FROM
					`operaciones_mvtos` `operaciones_mvtos`
						INNER JOIN `eacp_config_bases_de_integracion_miembros`
						`eacp_config_bases_de_integracion_miembros`
						ON `operaciones_mvtos`.`tipo_operacion` =
						`eacp_config_bases_de_integracion_miembros`.`miembro`
					WHERE
						(`operaciones_mvtos`.`docto_afectado` = $idsolicitud)
						AND
						(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2601)
						GROUP BY
							`operaciones_mvtos`.`docto_afectado`,
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
						ORDER BY
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							`operaciones_mvtos`.`fecha_afectacion`,
							`operaciones_mvtos`.`socio_afectado`
					";
			$xF		= obten_filas($sqlInt);
			$monto 	= $xF["monto"];
		}
	}

	$tasamor 		= ($imora * 100) / 12;
	$montoletras 	= convertirletras($monto);
	$monto 			= getFMoney($monto);
	$nombrerep 		= getNombreSocio($codigorep);
	$domrep 		= sociodom($codigorep);
	$isnow 			= fecha_larga();
	$fecha			= fecha_larga($fecha);


echo "<table width='100%' border='0'>
      <tr>
        <td bordercolor='#000000' class='bigtitle'>PAGARE</td>
        <td></td>
        <td>Numero 01/01</td>
        <td></td>
        <td class='midtitle'>Bueno por : </td>
        <td class='midtitle'>$ $monto</td>
      </tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td class='numc'>EN " . DEFAULT_NOMBRE_LOCALIDAD . ", " . DEFAULT_NOMBRE_ESTADO . " a:</td>
		  <td class='numc'>$isnow</td>
	  </tr>
		<tr>
		  <td colspan='6' class='legal'>Debo(emos) y Pagare(mos) incondicionalmente por este PAGARE
		  a la orden de <b>" . EACP_NAME . "</b> ubicada en <b>" . EACP_DOMICILIO_CORTO . "</b> o
		  en cualquier otra que se me(nos) solicite el pago, el dia <b>$fecha</b> la cantidad de <b>$ $monto</b>
		  - - - - - - - - - - - - - - - - - - - - - -
		  <b>son:($montoletras)</b> - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		  - - - - - - - - - - - - - - - - - - - - - - - - - - - - Valor recibido a mi(nuestra) entera
		  satisfacci&oacute;n. Este PAGAR&Eacute; forma parte de una serie numerada del 01/01 y todos estan sujetos
		  a la condici&oacute;n de que, al no pagarse cualquiera de ellos a su vencimiento, seran exigibles
		  todos los que sigan en n&uacute;mero, ademas de los ya vencidos. Desde la fecha de este documento
		  hasta el dia de su liquidacion, causara intereses moratorios al tipo de <b>$tasamor % </b> Mensual, pagadero
		  en esta ciudad o cualquier otra plaza donde me(nos) sea exigido, conjuntamente con el principal.</td>
	  </tr>
		<tr>
		  <td colspan='6'>&nbsp;</td>
	  </tr>
	</table>
	<p class='order'>
	Acepto
	<br />
	<br />
	<br />
	<br />
	____________________________________
	<br />
	$nombrerep<br />
	$domrep<br />
	</p>
<p class='bigtitle'>AVAL(ES)</b>
";
	$segpo = "SELECT codigo FROM socios_general WHERE grupo_solidario=$idgrupo AND codigo!=$codigorep";
	$rsgpo = mysql_query($segpo);
		while($rwg = mysql_fetch_array($rsgpo)) {
			$name 	= getNombreSocio($rwg[0]);
			$dom 	= sociodom($rwg[0]);
			echo "	<p class='order'>
	Acepto Incondicionalmente las Obligaciones de este Pagar&eacute;, como si del deudor Principal se tratar&aacute;<br>
	<br>
	<br>
	<br>
	____________________________________<br>
	$name<br>
	Direccion: $dom<br>
	</p>";
		}
	@mysql_free_result($rsgpo);
?>
</body>
</html>
