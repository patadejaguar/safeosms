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
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

$oficial 		= elusuario($iduser);
$ejercicio		= $_GET["e"];
$trabajador		= $_GET["t"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="importHTML();">
<!-- -->
<?php
echo getRawHeader();
?>
<div id="iPhantom">
</div>
<?php
echo getRawFooter();
?>
</body>
<script  >
function importHTML(){
	document.getElementById("iPhantom").innerHTML = opener.document.body.innerHTML;
	iFrms = document.forms.length - 1;
	for(i=0; i<=iFrms; i++){
		document.forms[i].disabled = true;
		iElm = document.forms[i].elements.length - 1;
		for (ie=0; ie<=iElm; ie++){
			document.forms[i].elements[ie].disabled = true;
		}
	}

	window.print();
}
</script>
</html>