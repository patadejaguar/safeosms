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
	$sql_set .= "COUNT(operaciones_mvtos.idoperaciones_mvtos) as 'nLetras', SUM(operaciones_mvtos.afectacion_real * operaciones_tipos.afectacion_en_notificacion) AS 'total', ";
	$sql_set .= "creditos_solicitud.saldo_vencido FROM creditos_solicitud, operaciones_mvtos, operaciones_tipos WHERE operaciones_mvtos.docto_afectado=creditos_solicitud.numero_solicitud AND operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion AND ";
	$sql_set .= "operaciones_mvtos.periodo_socio!=0 AND operaciones_tipos.afectacion_en_notificacion!=0 AND operaciones_mvtos.estatus_mvto=50 AND operaciones_mvtos.docto_neutralizador=1 ";
	$sql_set .= " AND creditos_solicitud.periocidad_de_pago=$frecuencia_pagos  ";
	$sql_set .= " AND creditos_solicitud.numero_socio>=$socio_inicial AND creditos_solicitud.numero_socio<=$socio_final ";
	$sql_set .= "GROUP BY creditos_solicitud.numero_solicitud HAVING ";
	$sql_set .= "nLetras>=$inicioletras AND nLetras<=$limiteletras ORDER BY creditos_solicitud.numero_socio  LIMIT 0,500";

$rsNot = mysql_query($sql_set);
	while ($rws = mysql_fetch_array($rsNot)) {
		echo getRawHeader();
		$total = $rws[4] + $rws[5];
		
		?>
		<p class='bigtitle'>NOTIFICACION EXTRAJUDICIAL</p>
		<br></br>
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
				echo "<tr>
					<td class='class_ligth'>CAPITAL VENCIDO</td><td class='class_right'> $rws[5]</td>
					</tr>
					<tr>
						<td class='class_ligth'>TOTAL</td><td class='class_right'> $total</td>
					</tr>
				</table></div>";
			@mysql_free_result($rscc);				
			?>
			<br />			
			<p class="legal"><b><?php echo getNombreSocio($rws[0]); ?></b>, deber&aacute; presentarse <b><?php echo DEUDORES_DIVERSOS_DIAS; ?> d&iacute;as h&aacute;biles</b> despu&eacute;s de 
			haber recibido esta <strong>Notificaci&oacute;n Extrajudicial</strong>, 
			en la <b><?php echo EACP_NAME; ?></b>. 
			con domicilio en <b><?php echo EACP_DOMICILIO_CORTO; ?></b>, para que liquide el total del adeudo
			 que tiene pendiente mas accesorios generados o ejecutaremos judicialmente el proceso legal del documento 
			 comprometido con la empresa.</p>   
		<p class="legal">Asimismo nos deber&aacute; reintegrar la cantidad de <strong>$ <?php echo $total; ?></strong> que corresponden al saldo de capital Vencido, m&aacute;s gastos 
		originados que tiene pendiente por Pagar a la fecha.</p>
		<p class="legal">Por otra parte, le comunico que todos los gastos y costas que se originen ser&aacute;n por cuenta suya.</p>
		<p class="legal">No omito recordarle que la garant&aacute;a comprometida con la Empresa por parte de ustedes es la siguiente:</p> 
	<ol type="1">
		<li>PAGAR&Eacute; EN ORIGINAL.</li>
		<li>CONTRATO DE CR&Eacute;DITO.</li>
		<li>GARANT&Iacute;A COMPROMETIDA.</li>
	</ol>
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
		
		echo "<br class='newpage' />";
	}
	if (mysql_num_rows($rsNot)<= 0){
		echo "<div align='center'><img src='images/no.png' /></div>
		<p class='bigtitle'>NO EXISTEN DATOS</p>";
	}
	@mysql_free_result($rsNot);
	
?>
</body>
</html>
