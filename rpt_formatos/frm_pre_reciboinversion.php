<?php
/**
 * @since 31/03/2008
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1.0
 * 
 * 		01/04/2008
 * 		- Agregar prediccion de Intereses
 * 		07072008	- Soporte de IDE y Saldos por Mvtos
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.captacion.inc.php");

$oficial 	= elusuario($iduser);
$cuenta 	= $_GET["c"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>RECIBO DE REINVERSION</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<?php

echo getRawHeader();

	$docto 			= $cuenta;
	$fechaRecibo	= date("Y-m-d");
	
	$sql_d_ide 		= "SELECT * FROM operaciones_mvtos
							WHERE
							fecha_operacion = '$fechaRecibo'
							AND docto_afectado = $docto
							AND tipo_operacion=235";
	$dIDE			= obten_filas($sql_d_ide);
	$ide			= $dIDE["afectacion_real"];
	
	$xInv			= new cCuentaInversionPlazoFijo($docto, false, 0);


	$dcuentas 		= $xInv->getDatosInArray();

	$idsocio 		= $dcuentas["numero_socio"];
	$docto 			= $dcuentas["numero_cuenta"];
	$dias			= $dcuentas["dias_invertidos"];
	$tasa 			= $dcuentas["tasa_otorgada"];
	$capital		= $dcuentas["saldo_cuenta"]; // + $ide);(
	$vencimiento	= sumardias($dcuentas["inversion_fecha_vcto"], $dias);

	$interes		= ($capital * $dias * $tasa) / EACP_DIAS_INTERES;

	$xSoc								= new cSocio($idsocio);
	$xSoc->init();
	$DSoc								= $xSoc->getDatosInArray();
	$domicilio_del_socio 				= $xSoc->getDomicilio();
	$nombre_del_socio 					= $xSoc->getNombreCompleto();
	$numero_de_socio 					= $idsocio;

	$tasa		= getFMoney($tasa * 100);
	$isr		= getISRByInversion($capital, $dias);



	$aliquidar 	= $capital + $interes - ($isr);
	$nombre		= $nombre_del_socio;

	echo "
	<p class='bigtitle'>CONSTANCIA PROVISIONAL DE DEPOSITO A PLAZO</p>
	<hr />";

	// obtiene datos del socio

	$direccion  	= $domicilio_del_socio;
	$rfc 			= $DSoc["rfc"];
	$curp 			= $DSoc["curp"];

//
	echo $xSoc->getFicha();
	echo $xInv->getFicha(true);
	
?>
<hr />
<?php


echo "
<table width='75%'>
  <tr>
    <td width='15%'>&nbsp;</td>
    <td width='15%'>&nbsp;</td>

    <th>Fecha de Apertura:</th>
    <td>$dcuentas[5]</td>
    <td>&nbsp;</td>

  </tr>

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>Fecha de Vencimiento:</th>
    <td>$vencimiento</td>
    <td>&nbsp;</td>

  </tr>


  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <td></td>
    <td></td>
    <td>&nbsp;</td>

  </tr>


  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>Dias Invertidos:</th>
    <td class='mny'>$dcuentas[15]</td>
    <td>&nbsp;</td>

  </tr>

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>Tasa Otorgada</th>
    <td class='mny'>$tasa %</td>
    <td>&nbsp;</td>

  </tr>


  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <td></td>
    <td></td>
    <td>&nbsp;</td>

  </tr>


   <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>Capital Invertido:</th>
    <td class='mny'>" . getFMoney($capital) . "</td>
    <td>&nbsp;</td>

  </tr>

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>Interes Generado:</th>
    <th class='mny'>" . getFMoney($interes) ."</th>
    <td>$dmvto[0]</td>
  </tr>
<!--ISR -->
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>ISR a Retener:</th>
    <th class='mny'>(" . getFMoney($isr) .")</th>
    <td></td>
  </tr>
<!-- IDE -->
<!--  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>IDE Retenido:</th>
    <th class='mny'>(" . getFMoney($ide) .")</th>
    <td></td>
  </tr>  -->

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>A Liquidar</th>
    <td class='mny'>" . getFMoney($aliquidar) . "</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
  	<td />
  	<td />
  	<th colspan='3'>" . convertirletras( $aliquidar ) . "</th>
  </tr>
</table>
<hr /><table border='0' width='100%'>
	<tr>
	<td><center>Firma de CONFORMIDAD</td>
	<td><center>GENERO LA REINVERSION</center></td>
	</tr>
	<tr>
	<td><br /><br ><br /></td>
	</tr>
	<tr>
	<td><center>$nombre
	</td>
		<td><center>$oficial</center></td>
	</tr>
	</table>
	<p class='legal'>Comprobante Provisional de Reinversion, solo para fines Informativos,
	ya que la informacion contenida en esta cedula es de caracter
	provisional y suceptible a que el Aceptante modifique los montos y/o plazos durante el lapso del Dia Laboral.</p>
	<p class='legal'>El Impuesto a Depositos en Efectivo Cobrado en esta transaccion es de " . getFMoney($ide) . " </p>
";


echo getRawFooter();
?>
</body>
</html>