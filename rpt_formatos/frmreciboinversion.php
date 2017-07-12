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
include_once("../core/core.captacion.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

$oficial 	= elusuario($iduser);
$idrecibo 	= $_GET["recibo"];
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


	if (!$idrecibo) {
		exit($msg_rpt_exit);
	}

$sqlrec = "SELECT * FROM operaciones_recibos WHERE idoperaciones_recibos=$idrecibo";
	//echo $sqlrec; exit;
	$datos 			= obten_filas($sqlrec);
	$idsocio 		= $datos["numero_socio"];
	$tiporec 		= $datos["tipo_docto"];
	$docto 			= $datos["docto_afectado"];
	$eltitulo 		= eltipo("operaciones_recibostipo", $tiporec);
	$sumaRecibo		= $datos["total_operacion"];
	$fechaRecibo	= $datos["fecha_operacion"];
	echo "
	<p class='bigtitle'>CONSTANCIA DE DEPOSITO A PLAZO</p>
	<hr />";
	$totaloperacion 	= $datos["total_operacion"];
	$total_fmt 			= number_format($totaloperacion, 2, '.', ',');
	$montoletras 		= convertirletras($totaloperacion);
	// obtiene datos del socio
	$xSoc								= new cSocio($idsocio);
	$xSoc->init();
	$DSoc								= $xSoc->getDatosInArray();
	$domicilio_del_socio 				= $xSoc->getDomicilio();
	$nombre_del_socio 					= $xSoc->getNombreCompleto();
		
	if ($idsocio != 1) {
		$nombre 	= $nombre_del_socio;
	} else {
		$nombre  	= mifila($sqlrec, "cadena_distributiva");
	}
	$direccion  	= $domicilio_del_socio;
	$rfc 			= $DSoc["rfc"];
	$curp 			= $DSoc["curp"];

// obtiene datos del documento que ayudaran al detalle en contabilidad
	$observaciones = mifila($sqlrec, "observacion_recibo");
//
$sql_d_mvto = "SELECT * FROM operaciones_mvtos
						WHERE recibo_afectado=$idrecibo
						AND tipo_operacion=500 ";

$sql_d_inv = "SELECT * FROM operaciones_mvtos
						WHERE recibo_afectado=$idrecibo
						AND tipo_operacion=223 ";

$sql_d_ide = "SELECT * FROM operaciones_mvtos
						WHERE
						fecha_operacion = '$fechaRecibo'
						AND docto_afectado = $docto
						AND tipo_operacion=235 ";

$xInv	= new cCuentaInversionPlazoFijo($docto, $idsocio);


$dcuentas 	= $xInv->getDatosInArray();
$dmvto 		= obten_filas($sql_d_mvto);
$dInv		= obten_filas($sql_d_inv);

$dIDE		= obten_filas($sql_d_ide);
//

$iSocio	= new cFicha(iDE_SOCIO, $idsocio);
$iSocio->setTableWidth();
$iSocio->show();
echo "<hr />";

	$cInv	= new cFicha(iDE_CINVERSION, $docto);
	$cInv->setTableWidth();
	$cInv->show();
?>
<hr />
<?php
$tasa		= getFMoney($dcuentas["tasa_otorgada"] * 100);
$interes 	= getFMoney($dmvto["afectacion_real"]);
//$capital 	= getFMoney($dInv["afectacion_real"]);

$capital 	= getFMoney($dcuentas["saldo_cuenta"]);

$dias		= $dcuentas["dias_invertidos"];
$isr		= getISRByInversion($dcuentas["saldo_cuenta"], $dias);
$isr		= round($isr, 2);
$ide		= $dIDE["afectacion_real"];

//$ide		= 0;

$aliquidar 	= ($dInv["afectacion_real"] + $dmvto["afectacion_real"]) - ($isr + $ide);
$vencimiento	= $dcuentas["inversion_fecha_vcto"];


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
    <td class='mny'>$dias</td>
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
    <td class='mny'>$capital</td>
    <td>&nbsp;</td>

  </tr>

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>Interes Generado:</th>
    <th class='mny'>$interes</th>
    <td></td>
  </tr>
<!-- ISR -->
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>ISR a Retener:</th>
    <th class='mny'>(" . getFMoney($isr) .")</th>
    <td></td>
  </tr>

<!-- IDE -->
<!-- <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <th>IDE Retenido:</th>
    <th class='mny'>(" . getFMoney($ide) .")</th>
    <td></td>
  </tr> -->


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
<p class='legal'>El Impuesto a Depositos en Efectivo Cobrado en esta transaccion es de " . getFMoney($ide) . " </p>
<hr />
<table border='0' width='100%'>
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
";

echo getRawFooter();
?>
</body>
</html>
