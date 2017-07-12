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
$rpt = $_GET["i"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Descripcion del reporte</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
if(!$rpt) {
	echo "<body onload='salir();' />";
} else {
	?>
<body>
<?php
	}
	$sqlrpt = "SELECT * FROM general_reports WHERE idgeneral_reports='$rpt' LIMIT 0,1";
	$drpt = obten_filas($sqlrpt);
	$namerpt =  $drpt[1];
	$descrpt =  $drpt[4];
?>
<p class="aviso">Nombre del Reporte: <?php echo $namerpt; ?><br /></p>
<p class="aviso">Tipo de Reporte: <?php echo $drpt[2]; ?><br /></p>
<p class='aviso'>
	<?php echo $descrpt; ?>
</p>
<div  ><input type='button' value='Cerrar Ventana' onclick='salir();' /></div>
</body>
<script  >
function salir(){
	window.close();
}
</script>
</html>
