<?php
//=====================================================================================================
	include_once("core/go.login.inc.php");
	include_once("core/core.error.inc.php");
	include_once("core/core.html.inc.php");
	include_once("core/core.init.inc.php");	
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
	$iduser = $_SESSION["log_id"];
//=====================================================================================================

include_once "core/entidad.datos.php";
include_once "core/core.deprecated.inc.php";
include_once "core/core.fechas.inc.php";

$oficial = elusuario($iduser);
?>
<html>
<head>
<title>SISTEMA DE ADMINISTRACION FINANCIERA Y ESTADISTICA</title>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>
<br>


</body>
<script  >
</script>
</html>
