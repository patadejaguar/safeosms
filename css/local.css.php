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
include_once("../core/core.deprecated.inc.php");
include_once("../core/entidad.datos.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.lang.inc.php");

$strOrder 	= (isset($_GET["o"])) ? $_GET["o"] : "";
$NewOrder 	= (isset($_GET["x"])) ? $_GET["x"] : "";
$cmdOrder 	= array();
$form		= "";
$subpath	= "";
$xLng		= new cLang();

header("Content-Type: text/css");
header("X-Content-Type-Options: nosniff");
?>
.coolCheck:after {
	content: '<?php echo $xLng->getT("TR.NO"); ?>';
}

.coolCheck:before {
	content: '<?php echo $xLng->getT("TR.SI"); ?>';
}
<?php
if(SAFE_LANG == "pt"){
?>
.coolCheck {
	width: 4.8em;
}
.coolCheck label {
width: 2.4em;
}
<?php
}
?>