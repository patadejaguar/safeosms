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
	$socio_inicial 	= $_GET["on"];
	$socio_final 	= $_GET["off"];
	
	
	$sql_set = "SELECT	
						`socios_general`.`codigo`,
						CONCAT(`socios_general`.`nombrecompleto`, ' ',
						`socios_general`.`apellidopaterno`, ' ',
						`socios_general`.`apellidomaterno`) AS 'nombre',
						`creditos_solicitud`.fecha_ministracion AS 'ministrado',
						`creditos_solicitud`.fecha_vencimiento AS 'vencimiento',
						`seguimiento_notificaciones`.*
					
					FROM
						`seguimiento_notificaciones` `seguimiento_notificaciones` 
							INNER JOIN `creditos_solicitud` `creditos_solicitud` 
							ON `seguimiento_notificaciones`.`numero_solicitud` = 
							`creditos_solicitud`.`numero_solicitud` 
								INNER JOIN `socios_general` `socios_general` 
								ON `seguimiento_notificaciones`.`socio_notificado` = 
								`socios_general`.`codigo` 
					WHERE
						(`seguimiento_notificaciones`.`numero_notificacion` =0)
						AND
						(`seguimiento_notificaciones`.`socio_notificado` >=$socio_inicial) 
						AND
						(`seguimiento_notificaciones`.`socio_notificado` <=$socio_final)";
	
	$rsnoa = mysql_query($sql_set, cnnGeneral());

		while($rws = mysql_fetch_array($rsnoa)) {
		echo getRawHeader();
		?>
				<p class='bigtitle'>RECORDATORIO DE PAGO</p>
		<br />
		<div class='numc'>
		<table width="60%"   >
			  <tr>
			    <td><?php echo $rws["codigo"]; ?></td>
			  </tr>
			  <tr>
			    <td><?php echo $rws["nombre"]; ?></td>
			  </tr>
			  <tr>
			    <td><?php echo $rws["domicilio_completo"]; ?></td>
			  </tr>
  			  <tr>
			    <td>N&uacute;mero de Control: <?php echo $rws["idseguimiento_notificaciones"]; ?></td>
			  </tr>

	</table>
	<br />
	</div>
	<p class="legal"><strong>APRECIABLE SOCIO (A):</strong></p>

	<p class="legal">Sirva la presente, para recordarle de una manera cordial y atenta, que su cr&eacute;dito Ministrado el d&iacute;a <strong><?php echo fecha_larga($rws["ministrado"]); ?></strong>,
	 vence el pr&oacute;ximo d&iacute;a <strong><?php echo fecha_larga($rws["vencimiento"]); ?></strong>.</p>

	<p class="legal">Por lo anterior y tomando como referencia sus excelentes antecedentes crediticios que se tomaron en cuenta para el 
	otorgamiento de su cr&eacute;dito, le recordamos acudir a nuestras oficinas el d&iacute;a de vencimiento se&ntilde;alado anteriormente, para realizar su (s) pago (s)
	 oportuno en tiempo y forma y conservar sus buenas referencias para cr&eacute;ditos futuros.</p>

	<p class="legal">En espera de sus atentas atenciones, reciba un afectuoso saludo.</p>
		<br />
		<br />
		<table border='0' width='100%'>
			<tr>
				<td class="ths"><center>Respetuosamente</center></td>
			</tr>
			<tr>
				<td><br /><br /><br /></td>
			</tr>
			<tr>
				<td class="ths2"><center><?php echo $oficial ?><br />
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
	if (mysql_num_rows($rsnoa)<= 0){
		echo "<div align='center'><img src='images/no.png' /></div>
		<p class='bigtitle'>NO EXISTEN DATOS</p>";
	}		
	@mysql_free_result($rsnoa);	// */
	
?>
</body>
</html>
