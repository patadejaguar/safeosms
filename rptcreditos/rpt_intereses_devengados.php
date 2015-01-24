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
include_once("../core/core.creditos.inc.php");

$idsolicitud 	= $_GET["pb"];
$id 			= $_GET["pa"];
$f15 			= $_GET["f15"];
$f14 			= $_GET["f14"];
$f16 			= $_GET["f16"];
$f18 			= $_GET["f18"];		//Mostrar Movimiento Especifico
$f19 			= $_GET["f19"];		//Codigo de Tipo de Operacion.- Mvto Especifico

$socio 			= $_GET["f50"];		//Numero de Socio

$paginar		= $_GET["v"];

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<link href="../css/flags.css" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<!-- -->
<?php
echo getRawHeader();
$xCred	= new cCredito($idsolicitud);
$xCred->init();

echo $xCred->getFichaDeSocio();
echo $xCred->getFicha();
$sqlCred	= "SELECT
			`creditos_sdpm_historico`.`fecha_actual`,
			`creditos_sdpm_historico`.`fecha_anterior`,
			`creditos_sdpm_historico`.`monto_calculado` AS 'calculado',
			`creditos_sdpm_historico`.`saldo`,
			`creditos_sdpm_historico`.`dias_transcurridos` AS 'dias',
			`creditos_sdpm_historico`.`estatus`,
			`creditos_sdpm_historico`.`tipo_de_operacion` AS 'operacion',
			`creditos_sdpm_historico`.`interes_normal` AS 'intereses',
			`creditos_sdpm_historico`.`interes_moratorio` AS 'moratorios'
			 
		FROM
			`creditos_sdpm_historico` `creditos_sdpm_historico` 
		WHERE
			(`creditos_sdpm_historico`.`numero_de_credito` = $idsolicitud)
		ORDER BY `creditos_sdpm_historico`.`fecha_anterior`
			";
//TODO: Terminar proceso

	$xT		= new cTabla($sqlCred);
	$xT->setWidth();
	echo $xT->Show("", true);
	$DSum	= $xT->getFieldsSum();
	
	echo	 "	<table width=\"100%\" align=\"center\" >

			<tfoot>
				<tr>
					<td />
					<th />
					<th />
					<th />
					<th />
					<th>SUMAS </th>
					<th class='mny'>" . getFMoney( $DSum["dias"] ) . "</th>
					<th />
					<th />
					<th class='mny'>" . getFMoney( $DSum["intereses"] ) . "</th>
					<th class='mny'>" . getFMoney( $DSum["moratorios"]) . "</th>
				</tr>
			</tfoot>
		</table>
	";


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