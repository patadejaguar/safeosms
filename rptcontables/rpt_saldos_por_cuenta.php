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
include_once "../libs/sql.inc.php";

$oficial = elusuario($iduser);
$cuenta_inicial = $_GET["ci"];
$cuenta_final = $_GET["cf"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<!-- -->
<?php
echo getRawHeader();
//diario
$sqlsdo = "SELECT
	`contable_catalogo`.`numero`,
	/*`contable_catalogo`.`nombre`, */
	`contable_saldos`.`ejercicio`,
	`contable_saldos`.`tipo`,
	`contable_saldos`.`saldo_inicial`,
	`contable_saldos`.`imp1`,
	`contable_saldos`.`imp2`,
	`contable_saldos`.`imp3`,
	`contable_saldos`.`imp5`,
	`contable_saldos`.`imp6`,
	`contable_saldos`.`imp7`,
	`contable_saldos`.`imp8`,
	`contable_saldos`.`imp9`,
	`contable_saldos`.`imp10`,
	`contable_saldos`.`imp11`,
	`contable_saldos`.`imp12` 
FROM
	`contable_catalogo` `contable_catalogo` 
		INNER JOIN `contable_saldos` `contable_saldos` 
		ON `contable_catalogo`.`numero` = `contable_saldos`.`cuenta` 
WHERE 
	`contable_saldos`.cuenta = $cuenta_inicial";
$tbl = new cTabla($sqlsdo);
echo $tbl->Show();
echo  " <br />
<br />
<br />
<br />
<br /> ";
echo $tbl->getValorCampo("nombre");
/* $rs = mysql_query($sqlsdo);
while($rw = mysql_fetch_array($rs)){
	
} */
echo getRawFooter();
?>
</body>
</html>