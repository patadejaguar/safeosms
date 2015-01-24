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
include_once( "../core/entidad.datos.php");
include_once( "../core/core.deprecated.inc.php");
include_once( "../core/core.fechas.inc.php");
include_once( "../libs/sql.inc.php");
include_once( "../core/core.config.inc.php");

	
$oficial = elusuario($iduser);
//TODO: Actualizar Modulo
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<p class="bigtitle">LISTADO DE CREDITOS A ANALIZAR EN LA SESI&Oacute;N DE CR&Eacute;DITO ACTUAL</p>
<!-- -->
<?php
echo getRawHeader();
sqltabla($sqlb11 . " WHERE creditos_periodos.idcreditos_periodos=$periodosolicitudes", "", "fieldnames");
echo "<hr></hr><hr></hr>";
sqltabla($sqlb19c . " AND  creditos_solicitud.periodo_solicitudes=$periodosolicitudes AND creditos_solicitud.estatus_actual=99", "", "fieldnames");
echo getRawFooter();
?>
</body>
</html>
