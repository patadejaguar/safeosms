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
	$sql_notificaciones = "SELECT * FROM seguimiento_notificaciones WHERE tipo_credito='automatizado' 
	AND estatus_notificacion='pendiente' AND numero_notificacion=4";
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
	<p class='bigtitle'>CITATORIO EXTRAJUDICIAL</p>
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

	<p class="legal"><b><?php echo $nombre; ?></b>, deber&aacute; presentarse <b><?php echo DEUDORES_DIVERSOS_DIAS; ?> d&iacute;as h&aacute;biles</b> despu&eacute;s de 
			haber recibido este <strong>Citatorio Extrajudicial</strong>, 
			en la <b><?php echo EACP_NAME; ?></b>. 
			con domicilio en <b><?php echo EACP_DOMICILIO_CORTO; ?></b>, para que liquide el total del adeudo
			 que tiene pendiente mas accesorios generados o ejecutaremos judicialmente el proceso legal del documento 
			 comprometido con la empresa.</p>   
		<p class="legal">Asimismo nos deber&aacute; reintegrar la cantidad de <strong>$ <?php echo $total; ?></strong> que corresponden al saldo de capital Vencido, m&aacute;s gastos 
		originados que tiene pendiente por Pagar a la fecha.</p>
		<p class="legal">Por otra parte, le comunico que todos los gastos y costas que se originen ser&aacute;n por cuenta suya.
		No omito recordarle que la garant&iacute;a comprometida con la Empresa por parte de ustedes es la siguiente:</p> 
	<ol type="1">
		<li>PAGAR&Eacute; EN ORIGINAL.</li>
		<li>CONTRATO DE CR&Eacute;DITO.</li>
	</ol>
		<p class="legal">Sin m&aacute;s por el momento reciba un cordial saludo.</p>
		<p style="text-align:right; "><?php echo "$eacp_localidad, $eacp_estado; A " . fecha_larga(); ?></p>
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