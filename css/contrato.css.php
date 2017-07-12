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
$strOrder 	= (isset($_GET["o"])) ? $_GET["o"] : "";
$NewOrder 	= (isset($_GET["x"])) ? $_GET["x"] : "";
$cmdOrder 	= array();
$form		= "";
$subpath	= "";
header("Content-Type: text/css");
header("X-Content-Type-Options: nosniff");
?>
h4, h3, h2, table, p {
		
}
h1,h2,h3,h4{
	text-align: center;
}
   caption {
       text-align: left;
       font-weight: bold;
}
p {
	text-align: justify;
}
th, td, p{
	text-transform: none !important;
	text-indent: 0;
}
body, html {
	font: <?php echo CSS_TFUENTE_CONTRATOS;?>pt "Trebuchet MS", Arial, Helvetica, sans-serif !important;
	line-height: 1.1em !important;
	font-stretch: condensed;
}
th {
	text-align: left;
	min-width: 25%;
}
body{
	margin:0;
	height: 100%;
	padding: 0;
}
td {
	text-align: justify;
    min-width: 25%;
}
table {
	border-spacing: 0;
	border-collapse: collapse;
}

@page {
	
}
@media print
{
    footer {
    clear: both;
    position: fixed;
    z-index: 10;
    height: 3em;
    margin-top: -3em;
    } 
}
