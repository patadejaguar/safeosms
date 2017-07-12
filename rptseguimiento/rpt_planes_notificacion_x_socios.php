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
		$inicioletras = 4;
		$limiteletras=6;
	} elseif ($frecuencia_pagos == 15) {
		$inicioletras 	= 2;
		$limiteletras	=3;
	} elseif ($frecuencia_pagos == 30) {
		$inicioletras = 1;
		$limiteletras=1;
	} else {
		$limiteletras=1;
	}

	if (!$socio_inicial) {
		exit("<p>ERROR AL EJECUTAR REPORTE</p>");
	}

	$sql_set = "SELECT creditos_solicitud.numero_socio, creditos_solicitud.numero_solicitud, creditos_solicitud.fecha_ministracion,
	COUNT(operaciones_mvtos.idoperaciones_mvtos) as 'nLetras', SUM(operaciones_mvtos.afectacion_real * operaciones_tipos.afectacion_en_notificacion) AS 'total'
	FROM creditos_solicitud, operaciones_mvtos, operaciones_tipos WHERE operaciones_mvtos.docto_afectado=creditos_solicitud.numero_solicitud AND operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion AND
	operaciones_mvtos.periodo_socio!=0 AND operaciones_tipos.afectacion_en_notificacion!=0 AND operaciones_mvtos.estatus_mvto=50 AND operaciones_mvtos.docto_neutralizador=1
	AND creditos_solicitud.periocidad_de_pago=$frecuencia_pagos
	AND creditos_solicitud.numero_socio>=$socio_inicial AND creditos_solicitud.numero_socio<=$socio_final
	GROUP BY creditos_solicitud.numero_solicitud HAVING
	nLetras>=$inicioletras AND nLetras<=$limiteletras ORDER BY creditos_solicitud.numero_socio  LIMIT 0,500";


$rsNot = mysql_query($sql_set);
	while ($rws = mysql_fetch_array($rsNot)) {
		echo getRawHeader();
		?>
		<p class='bigtitle'>NOTIFICACI&Oacute;N DE COBRO</p>
		<br />
		<div class='numc'>
		<table width="60%"   >
			  <tr>
			    <td><?php echo $rws[0]; ?></td>
			  </tr>
			  <tr>
			    <td><?php echo getNombreSocio($rws[0]); ?></td>
			  </tr>
			  <tr>
			    <td><?php echo sociodom($rws[0]); ?></td>
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
				echo "<td class='class_ligth'>TOTAL</td><td class='class_right'> $rws[4]</td>
				</table></div>";
			@mysql_free_result($rscc);
			?>
		<br></br>
		<p class="legal">Nos permitimos manifestarle que su Cr&eacute;dito Vigente ministrado el d&iacute;a <b><?php echo fecha_larga($rws[2]); ?></b>,
		con n&uacute;mero de Control <b><?php echo $rws[1]; ?></b> a la fecha, refleja <b><?php echo $rws[3]; ?></b> letra(s) vencida(s), debido a que no ha dado cumplimiento en tiempo y forma al
		plan de pagos establecido con la Empresa.</p>

		<p class="legal">Por lo anterior, lo invitamos a realizar su (s) pago (s) pendiente (s) y seguir conservando los buenos
		antecedentes crediticios que se tomaron en cuenta para el otorgamiento
		de su cr&eacute;dito, en un plazo no mayor a  <b><?php echo DEUDORES_DIVERSOS_DIAS; ?> d&iacute;as h&aacute;biles</b> a partir de la fecha de recepci&oacute;n de la presente <b>NOTIFICACI&Oacute;N</b>.</p>

		<p class="legal">As&iacute; mismo, se le informa que el hecho de no haber pagado oportunamente su (s) letra (s)
		respectivas, le ha originado Gastos de Cobranza del <b><?php echo $tasa_seguimiento * 100; ?>%</b>. De igual manera, se le comunica que si pasa a liquidar
		su adeudo en el tiempo se&ntilde;alado, podr&aacute; recibir atractivos <strong>descuentos</strong>.</p>

		<p class="legal">Sin m&aacute;s por el momento reciba un cordial saludo.</p>
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
