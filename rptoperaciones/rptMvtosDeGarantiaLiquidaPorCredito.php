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
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");

$fecha_inicial			= $_GET["on"];
$fecha_final			= $_GET["off"];

//$credito				= $_GET[""];

$filtro_por_credito 	= "";
if ( isset($credito)){
	$filtro_por_credito = " AND ( `operaciones_mvtos`.`docto_afectado` =$credito ) ";
}

$paginar	= $_GET["v"];
if($paginar == 1){
	$paginar = true;
} else {
	$paginar = false;
}
$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<!-- -->
<?php
echo getRawHeader();
$sql = "SELECT
	`socios_general`.`codigo`,
	`socios_general`.`nombrecompleto`,
	`socios_general`.`apellidopaterno`,
	`socios_general`.`apellidomaterno`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`recibo_afectado`,
	`operaciones_tipos`.`descripcion_operacion`,
	`operaciones_mvtos`.`fecha_operacion`,
	`operaciones_mvtos`.`afectacion_real`,
	`operaciones_mvtos`.`valor_afectacion`
FROM
	`socios_general` `socios_general`
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
		ON `socios_general`.`codigo` = `operaciones_mvtos`.`socio_afectado`
			INNER JOIN `operaciones_tipos` `operaciones_tipos`
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos`
WHERE
	(
		(`operaciones_mvtos`.`tipo_operacion` = 901)
		OR
		(`operaciones_mvtos`.`tipo_operacion` = 353)
	)
	AND
	(`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
	AND
	(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
	$filtro_por_credito
ORDER BY
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`fecha_operacion`,
	`operaciones_mvtos`.`tipo_operacion` DESC";
$tdBody		= "";
$tdHead		= "";
$tdKey		= 0;
$tdAll		= "";
$td			= "";
$saldo		= 0;
$total		= 0;
$rsMc	= mysql_query($sql, cnnGeneral());
	while($rw = mysql_fetch_array($rsMc)){
		$socio		= $rw["codigo"];
		$nombre		= $rw["apellidopaterno"] . " " . $rw["apellidomaterno"] . " " . $rw["nombrecompleto"];
		$credito	= $rw["docto_afectado"];
		$recibo		= $rw["recibo_afectado"];
		$fecha		= $rw["fecha_operacion"];
		$monto		= $rw["afectacion_real"] * $rw["valor_afectacion"];
		$tipo		= $rw["descripcion_operacion"];
		if ($tdKey != $credito ){
			$tdAll		.= $tdHead . $tdBody;
			$tdBody	    = "";
			$tdHead     = "";
            $saldo      = 0;
		}
			$saldo	+= $monto;
			$total	+= $monto;
			$tdHead	= "<tr>
					<th>$socio</th>
					<th>$credito</th>
					<th class='izq'>$nombre</th>
                    <th class='mny'>$saldo</th>
				</tr>";
            $tdBody .= "<tr>
                            <td>$fecha</td>
                            <td>$recibo</td>
                            <td class='izq'>$tipo</td>
                            <td class='mny'>$monto</td>
                        </tr>";
            $tdKey		= $credito;

	}
	echo "<table width='100%'>
		<tbody>
		$tdAll

		</tbody>
		<tfoot>
			<td></td>
			<td></td>
			<td></td>
			<td class='mny'>$total</td>
		</tfoot>
		</table>";
	@mysql_free_result($rsMc);
echo getRawFooter();
?>
</body>
<script  >
<?php

?>
function initComponents(){
	window.print();
}
</script>
</html>