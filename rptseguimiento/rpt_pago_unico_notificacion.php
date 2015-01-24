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
<title>NOTIFICACION DE COBRO PARA CREDITOS AUTOMATIZADOS</title>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	$socio_inicial = $_GET["on"];
	$socio_final = $_GET["off"];
	$sql_notificaciones = "SELECT * FROM seguimiento_notificaciones WHERE tipo_credito='pago_unico' 
	AND estatus_notificacion='pendiente' AND numero_notificacion<3";
	$rs = mysql_query($sql_notificaciones);
while($rw = mysql_fetch_array($rs))	{
	$nombre = getNombreSocio($rw[1]);
	$domicilio = $rw[15];
	if ($domicilio == '0'){
		$domicilio = "NO EXISTE DOMICILIO DEFINIDO. CAPTURELO O EDITELO";
		
	}
	//Datos del Credito
	$sql_cred = "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$rw[2] AND numero_socio=$rw[1] LIMIT 0,1";
	$dsol = obten_filas($sql_cred);
	$dias_venc = restarfechas($dsol[15], fechasys());
	$interes = cmoney($rw[9]);
	$moratorio = cmoney($rw[10]);
	$otros_cargos = cmoney($rw[11]);
	$capital = cmoney($rw[8]);
	$total = cmoney($rw[12]);
	
	
	echo getRawHeader();
?>
	<p class='bigtitle'>NOTIFICACION DE COBRO NUM. <?php echo $rw[3]; ?></p>
		<br />
		<div class='numc'>
		<table width="60%"   >
			  <tr>
			    <td><?php echo $rw[1]; ?></td>
			  </tr>
			  <tr>
			    <td><?php echo $nombre; ?></td>
			  </tr>
			  <tr>
			    <td><?php echo $domicilio; ?></td>
			  </tr>
			  <tr>
			    <td>CREDITO: <?php echo $rw[2]; ?> - CONTROL: <?php echo $rw[0]; ?></td>
			  </tr>

	</table>
	</div>
	<div align="right">
	<table width='33%'>
		<tr>
			<td height="5px">Capital:</td><td class='mny'><?php echo $capital; ?></td>
		</tr>
		<tr>
			<td height="5px">Intereses: </td><td  class='mny'><?php echo $interes; ?></td>
		</tr>
		<tr>
			<td height="5px">Moratorios: </td><td class='mny'><?php echo $moratorio; ?></td>
		</tr>
		<tr>
			<td height="5px">Otros Cargos: </td><td class='mny'><?php echo $otros_cargos; ?></td>
		</tr>
		<tr>
			<td height="5px">TOTAL: </td><td class='mny'><?php echo $total; ?></td>
		</tr>			
	</table></div>
	
		<p class="legal"> Nos permitimos manifestarle que su cr&eacute;dito Ministrado el d&iacute;a <strong><?php echo fecha_larga($dsol[36]); ?></strong>
		 venci&oacute; el  d&iacute;a <strong><?php echo fecha_larga($dsol[15]); ?></strong> y a la fecha, refleja  <strong><?php echo $dias_venc; ?></strong> D&iacute;as vencido(s),
		  debido a que no ha dado cumplimiento en tiempo y forma al plan de pagos establecido con la Empresa.</p>

		<p class="legal">Por lo que lo invitamos a realizar su (s) pago (s) y seguir conservando los buenos antecedentes crediticios que 
		se tomaron en cuenta para el otorgamiento de su cr&eacute;dito, en un plazo no mayor a <strong><?php echo DEUDORES_DIVERSOS_DIAS; ?> d&iacute;as h&aacute;biles</strong> 
		a partir de fecha de entrega de la presente <strong>NOTIFICACI&Oacute;N</strong>.</p>

		<p class="legal">As&iacute; mismo se le informa que su cr&eacute;dito ha generado intereses moratorios, as&iacute; como gastos de cobranza. En caso de dar 
		cumplimiento al pago en el nuevo plazo establecido se podr&aacute; considerar parte del inter&eacute;s moratorio o el total del mismo, 
		ya de no hacerlo as&iacute;, se continuara con el proceso  por la v&iacute;a correspondiente.</p>

			<p class="legal">Sin m&aacute;s por el momento reciba un cordial saludo.</p>
			<p style="text-align:right; "><?php echo "$eacp_localidad, $eacp_estado; A " . fecha_larga(); ?></p>
		<br />
		<table border='0' width='100%'>
			<tr>
				<td class="ths"><center>Respetuosamente</center></td>
			</tr>
			<tr>
				<td><br /><br /><br /><br /></td>
			</tr>
			<tr>
				<td class="ths2"><center><?php echo $oficial; ?><br/>
				OFICIAL DE SEGUIMIENTO</center></td>
			</tr>
			</table>
			<div align='right'>
			<table border='0' width='25%'>
			<tr>
			<td>C.c.p.</td><td> Titular del &Aacute;rea Jur&iacute;dica</td>
			</tr>
			<tr>
				<td>C.c.p.</td><td>Gerencia</td>
			</tr>
			<tr>
				<td>C.c.p.</td><td> Expediente</td>
			</tr>
		</table>
		</div>

		<?php
		echo getRawFooter();
		echo "<br class='newpage' />";
}
?>
</body>
</html>
