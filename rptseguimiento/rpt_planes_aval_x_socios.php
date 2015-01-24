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
include "../core/entidad.datos.php";
include "../core/core.deprecated.inc.php";
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
<!-- -->

<?php 
	$socio_inicial = $_GET["on"];
	$socio_final = $_GET["off"];
	$frecuencia_pagos = $_GET["f1"];

	$corte = fechasys();

	$limiteletras=2;
	$inicioletras = 1;
	if ($frecuencia_pagos == 7) {
		$inicioletras = 7;
		$limiteletras=11;
	} elseif ($frecuencia_pagos == 15) {
		$inicioletras = 4;
		$limiteletras=5;
	} elseif ($frecuencia_pagos == 30) {
		$inicioletras = 2;
		$limiteletras=2;
	} else {
		$limiteletras=1;
	}
	

	if (!$socio_inicial) {
		exit("<p>ERROR AL EJECUTAR REPORTE</p>");
	}
	
	$sql_set = "SELECT creditos_solicitud.numero_socio, creditos_solicitud.numero_solicitud, creditos_solicitud.fecha_ministracion, ";
	$sql_set .=" COUNT(operaciones_mvtos.idoperaciones_mvtos) as 'nLetras', SUM(operaciones_mvtos.afectacion_real * operaciones_tipos.afectacion_en_notificacion) AS 'total', ";
	$sql_set .= " CONCAT(socios_relaciones.nombres, ' ', socios_relaciones.apellido_paterno,' ', socios_relaciones.apellido_materno) AS 'nombre_aval', ";
	$sql_set .= "socios_relaciones.domicilio_completo AS 'domicilio', socios_relaciones.telefono_residencia AS 'telefono', creditos_solicitud.monto_autorizado ";
	$sql_set .= "  FROM creditos_solicitud, socios_relaciones, operaciones_mvtos, operaciones_tipos ";
	$sql_set .= " WHERE operaciones_mvtos.docto_afectado=socios_relaciones.credito_relacionado ";
	$sql_set .= " AND socios_relaciones.credito_relacionado = creditos_solicitud.numero_solicitud  ";
	$sql_set .= " AND operaciones_mvtos.docto_afectado=creditos_solicitud.numero_solicitud AND ";
	$sql_set .= " operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion AND ";
	$sql_set .= " operaciones_mvtos.periodo_socio!=0 AND operaciones_tipos.afectacion_en_notificacion!=0 ";
	$sql_set .= " AND operaciones_mvtos.estatus_mvto=50 AND operaciones_mvtos.docto_neutralizador=1 ";
	$sql_set .= " GROUP BY socios_relaciones.idsocios_relaciones  ";
	$sql_set .= "HAVING  nLetras>=$inicioletras AND nLetras<=$limiteletras ORDER BY creditos_solicitud.numero_socio  LIMIT 0,500";
	
	//echo  $sql_set; exit;
	
	
$rsNot = mysql_query($sql_set);
	while ($rws = mysql_fetch_array($rsNot)) {
		echo getRawHeader();
		$total = $rws[8] + $rws[4];
		
		?>
		<p class='bigtitle'>NOTIFICACION DE COBRO</p>
		<br></br>
		<div class='numc'>
		<table width="60%"   >
			  <tr>
			    <td><?php echo $rws[5]; ?></td>
			  </tr>
			  <tr>
			    <td><?php $rws[6]; ?></td>
			  </tr>
	</table>
	<br></br>
	</div>
			<?php 
			$sql_cargos = "SELECT SUM(operaciones_mvtos.afectacion_real) AS 'Monto_Afectable', operaciones_tipos.descripcion_operacion AS 'Tipo_de_Operacion' ";
			$sql_cargos .= "FROM operaciones_mvtos, operaciones_tipos WHERE operaciones_mvtos.tipo_operacion=operaciones_tipos.idoperaciones_tipos ";
			$sql_cargos .= " AND operaciones_mvtos.docto_afectado=$rws[1]  AND operaciones_tipos.afectacion_en_notificacion!=0 AND operaciones_mvtos.estatus_mvto=50 ";
			$sql_cargos .= " GROUP BY operaciones_tipos.idoperaciones_tipos";
			$rscc = mysql_query($sql_cargos);
			
						
				echo "<div align='right'><table border='0' width='45%'>";
				while ($rwcc = mysql_fetch_array($rscc)) {
					echo "<tr>
					<td class='class_ligth'>$rwcc[1]</td><td class='class_right'> $rwcc[0]</td>
					<tr>";
				}
				echo "<tr>
					<td class='class_ligth'>CAPITAL VENCIDO</td><td class='class_right'> $rws[8]</td>
					</tr>
					<tr>
						<td class='class_ligth'>TOTAL</td><td class='class_right'> $total</td>
					</tr>
				</table></div>";
			@mysql_free_result($rscc);				
			?>
			<br />			
	<p class="legal">Como es de su conocimiento, el d&iacute;a <strong><?php echo fecha_larga($rws[2]); ?></strong>, el socio(a) <strong><?php echo getNombreSocio($rws[0]); ?></strong>,
	 fue apoyado mediante un Cr&eacute;dito por la cantidad total de <strong>$ <?php echo $rws[8]; ?></strong>,
	 siendo usted y firmando como el <strong>Aval Principal</strong> del cr&eacute;dito, seg&uacute;n consta en el expediente de la persona beneficiada.</p>

	<p class="legal">Por lo anterior, y en virtud de haber cumplido con las fechas programadas para pagar de manera parcial el cr&eacute;dito, por no encontrar informaci?n de la misma, 
	y/o no encontrar a la persona en el domicilio proporcionado, le solicitamos pase a nuestras oficinas a liquidar el adeudo pendiente en un plazo no mayor a 
	<strong><?php echo DEUDORES_DIVERSOS_DIAS; ?></strong> d&iacute;as h&aacute;biles a partir de la fecha 
 de recepci&oacute;n de la presente notificaci&oacute;n de cobro.</p>

		<p class="legal">Sin m&aacute;s por el momento reciba un cordial saludo.</p>
		<br />
		<table border='0' width='100%'>
			<tr>
				<td class="ths"><center>Respetuosamente</center></td>
			</tr>
			<tr>
				<td><br><br><br></td>
			</tr>
			<tr>
				<td class="ths2"><center><?php echo $titular_seguimiento; ?></center></td>
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
		
		echo "<br class='newpage'></br>";
	}
	if (mysql_num_rows($rsNot)<= 0){
		echo "<div align='center'><img src='images/no.png' /></div>
		<p class='bigtitle'>NO EXISTEN DATOS</p>";
	}
	@mysql_free_result($rsNot);
	
?>
</body>
</html>
