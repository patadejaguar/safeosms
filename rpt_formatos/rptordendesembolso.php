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


$idsolicitud 		= $_GET["solicitud"];

	if (!$idsolicitud) {
		exit("NO HAY SUFICIENTES DATOS
		PARA LLEVAR A CABO EL INFORME" . $fhtm);
	}


	$sqlcred = "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$idsolicitud LIMIT 0,1";
	$rscred = mysql_query($sqlcred);
	while($rwc = mysql_fetch_array($rscred)) {
	// datos generales del socio
	$idsocio 		= $rwc[5];	// Numero de Socio
	$mynom 			= getNombreSocio($idsocio);
	// datos de la solicitud
	$fechaaut 		= $rwc[2];
	$taut 			= $rwc[13];
		$tipoaut 	= "  ANALITICA MEDIANTE COMITE U OFICIAL DE CREDITO, CERTIFICADO CON EL DOCUMENTO $rwc[6]";
		if($taut == 2) {
			$tipoaut = " AUTOMATIZADA; POR TENER LOS FONDOS SUFICIENTES";
		}
	$perpagos 		= eltipo("creditos_periocidadpagos", $rwc[10]);
	$modal 			= eltipo("creditos_modalidades", $rwc[11]);
	$eldest 		= eltipo("creditos_destinos", $rwc[19]);
	$tconvenio 		= eltipo("creditos_tipoconvenio", $rwc[19]);
	$montoaut 		= number_format($rwc[4], 2, '.', ',');
	$montosol 		= number_format($rwc[3], 2, '.', ',');
	$numpagos 		= $rwc[16];	// pagos aut...
	$fechavenct 	= $rwc["fecha_vencimiento"];
	$montol 		= convertirletras($rwc[4]);
	$diasaut 		= $rwc[17];
	$tasa 			= $rwc[9] * 100;
	$fechamin 		= $rwc["fecha_ministracion"];
	} // end while
	@mysql_free_result($rscred);
?>
<p class="bigtitle">ORDEN DE DESEMBOLSO</p>
<?php echo "<p class='order'>NUMERO DE ORDEN:   $idsolicitud </p>" ; ?>
<br>
<?php echo "<p class='p_right'>" . DEFAULT_NOMBRE_LOCALIDAD . ", " . DEFAULT_NOMBRE_ESTADO . "; A " . fecha_larga() . "</p>";?>
<br /><br />
<table     >
  <tr>
    <td class="ths2">DEPARTAMENTO DE CREDITO Y COBRANZA</td>
  </tr>
  <tr>
    <td class="ths2">AREA DE CAJA Y TESORERIA</td>
  </tr>
</table>
<br><br><br>
<?php
echo "<p class='legal'>POR ESTE CONDUCTO SE LE DA A CONOCER QUE LA PERSONA <b>$mynom</b>, PERSONA N&Uacute;MERO <b>$idsocio</b>;
 SE LE FUE AUTORIZADO EL CR&Eacute;DITO CON N&Uacute;MERO DE SOLICITUD <b>$idsolicitud</b> EL D&Iacute;A <b>" . getFechaLarga($fechaaut) . "</b>,
DE FORMA <b>$tipoaut</b>, CON LA MODALIDAD DE <b>\"CREDITO $modal\"</b>, EL MONTO DEL <b>CR&Eacute;DITO AUTORIZADO</b> FUE DE <b>$ $montoaut</b>
 -- SON: (<b>$montol</b>)--- DE UN TOTAL DE <b>$ $montosol</b> QUE FUE SOLICITADO ORIGINALMENTE</p>
<p class='legal'>LOS PAGOS SERAN DE <b>$numpagos $perpagos</b>, UN TOTAL DE <b>$diasaut</b> DIAS APROXIMADAMENTE, N&Uacute;MERO EL CU&Aacute;L PODR&Aacute;
CAMBIAR SEGUN EL PLAN DE PAGOS FINAL; A UNA TASA ANUAL EFECTIVA DE <b> $tasa %</b>,
CON FECHA DE VENCIMIENTO <b>" . getFechaLarga($fechavenct). "</b>. POR LO QUE PUEDE EMITIR EL RESPECTIVO CHEQUE POR CONCEPTO DE MINISTRACION DEL CREDITO.</p>
<p class='legal'>CABE MENCIONAR EL SOCIO DEBE CUBRIR LOS REQUISITOS MANIFESTADOS EN EL MANUAL DE CREDITO, POR LO QUE SE RECUERDA QUE
CUBRA SUS REQUISITOS ECONOMICOS Y DOCUMENTALES, LA FECHA DE PAGO PLANEADA SER&Aacute; EL <strong>" . getFechaLarga($fechamin) . "</strong> </p>";
?>
<br /><br /><br />
<table     >
  <tr>
    <td class="ths2">DEPARTAMENTO DE CREDITO</td>
  </tr>
  <tr>
    <td class="ths2">
	    <br />
    	<br />
    	<br />
    	<br />
    </td>
  </tr>
  <tr>
    <td class="ths2"><?php echo $oficial; ?></td>
  </tr>
  <tr>
    <td class="ths2">OFICIAL DE CREDITO</td>
  </tr>
</table>
<?php
echo getRawFooter();
?>
</body>
</html>
