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
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$xCaja		= new cCaja();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){
	header ("location:../404.php?i=200");
}


$oficial = elusuario($iduser);
$idrecibo = $_GET["recibo"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>RECIBO DE EGRESOS</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<link href="../css/recibo.css" rel="stylesheet" type="text/css">

<body>
<?php
echo getRawHeader();
?>
<p class="bigtitle">RECIBO DE EGRESOS POR CONCEPTOS VARIOS</p>
<?php 
	if (!$idrecibo) {
		exit($msg_rpt_exit . $fhtm);
	}
	
	$sqlrec = "SELECT * FROM operaciones_recibos WHERE idoperaciones_recibos=$idrecibo";
	$rsrec = mysql_query($sqlrec);
	
	while($rwrec = mysql_fetch_array($rsrec)) {

	if ($rwrec[4] ==1) {
		exit($msg_rpt_exit. $fhtm);
	}

		$idsolicitud 	= $rwrec[3];
		$idsocio 		= $rwrec[2];

		$domicilio 	= sociodom($idsocio);
		$x 		= getDatosSocio($idsocio);
		$rfc 		= $x[4];
		$curp 		= $x[5];
		
		$iddocto	= $rwrec["docto_afectado"];
		
	if ($idsocio != 1) {
		$nombre = getNombreSocio($idsocio);
	} else {
		$rfc 		= "N/A";
		$curp 		= "N/A";
		$domicilio 	= "PARTE RELACIONADA DE LA EMPRESA";
		$nombre  	= mifila($sqlrec, "cadena_distributiva");
	}

		echo "<hr>
		<table border='0' width='100%'>
		<tr>
			<th width='10%'>Referencia:</th>
			<td with='10%'>$idrecibo | $iddocto</td>
			<td width='80%'>" . DEFAULT_NOMBRE_LOCALIDAD . ", " .  DEFAULT_NOMBRE_ESTADO. "; " . fecha_larga($rwrec[1]) . "</td>

		</tr>
		<tr>
			<th>Socio</th>
			<td colspan='2'>[ $idsocio ] $nombre</td>
			
		</tr>
		<tr>
			<th>Domicilio</th>
			<td colspan='2'>$domicilio</td>
		</tr>
		<tr>
			<th>R.F.C./C.U.R.P.</th>
			<td>$rfc / $curp</td>
			<td />
		</tr>
		</table>
		<hr>";
		$monto 		= getFMoney($rwrec[5]);
		$letras 	= convertirletras($rwrec[5]);
		$parrafo 	= "";
		if ($rwrec[4]==32) {
			$parrafo ="<p>ESTA CANTIDAD TENDRA UN CARACTER DEVOLUTIVO, CONFORME A LAS POLITICAS VIGENTES  DE LA SOCIEDAD;
			 EN DADO CASO QUE NO LO RETORNE DIRECTAMENTE EN CAJA, SER&Acute; CARGADO A MI CUENTA Y SE DESCONTARA DIRECTAMENTE
			 A MI SALARIO AL NO PRESENTAR PAGO ANTES DE LA FECHA DE VENCIMIENTO</p>";
		} elseif($rwrec[4]==31) {
			$parrafo ="<p>ESTA CANTIDAD TENDRA UN CARACTER DEVOLUTIVO, CONFORME A LAS POLITICAS VIGENTES  DE LA SOCIEDAD;
			 EN DADO CASO QUE NO LO RETORNE DIRECTAMENTE EN CAJA, SER� CARGADO A MI CUENTA Y SE DESCONTARA DIRECTAMENTE
			 A MI SALARIO AL NO PRESENTAR PAGO ANTES DE LA FECHA DE VENCIMIENTO</p>";
		} else  {
			$parrafo ="";
		}

		$tiporec = eltipo("operaciones_recibostipo", $rwrec[4]);
	
		// RECIBO
		echo "<table width ='100%'>
				<tr>
					<td> RECIBI DE  <b>" . EACP_NAME . "</b>  LA CANTIDAD DE $ <b>$monto</b> --- SON :(<b>$letras</b>)---;
						CANTIDAD RECIBIDA POR CONCEPTO DE <b>$tiporec</b> EN EFECTIVO, A MI ENTERA SATISFACCION.
					</td>
				</tr>
				<tr>
					<td>
						$parrafo
					</td>
				</tr>
					<tr>
						<td>OBSERVACIONES: $rwrec[7]<td>
					</tr>
				<tr>
				</table>
		<hr>";
	}
	@mysql_free_result($rsrec);
	echo "<table border='0'  >
	<tr>
	<td><center>Firma de Conformidad</center></td>
	<td><center>Entrega el Recurso</center></td>
	</tr>
	<tr>
	<td><br><br><br></td>
	</tr>
	<tr>
	<td><center>$nombre</center></td>
	<td><center>$oficial</center></td>
	</tr>
	</table>";
echo getRawFooter();
?>
</body>
</html>