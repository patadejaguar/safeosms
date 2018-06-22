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
body, html {
	font-family: "Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif !important;
	font-size: <?php echo CSS_TFUENTE_RECIBOS;?>pt;
}

.Estilo1 {
	font-size: 1.1em;
	text-align: justify;
}

.Estilo3 {
	color: #990000;
	font-size: 1.3em;
	font-weight: bold;
	text-align: center;
}

.Estilo4 {
	color: #0000FF;
	font-size: 1.4em;
	text-align: center;
}

.Estilo5 {
	color: #0000CC;
	font-size: 1.1em;
	font-weight: bold;
	text-align: center;
}

.bigtitle {
	color: #800000;
	font-size: 1.1em;
	font-variant: small-caps;
	font-weight: bold;
	text-align: center;
}

.btmp {
	color: #FFFFFF;
}

.money {
	font-weight: bold;
	text-align: right;
}

.title {
	font-style: italic;
	font-variant: normal;
	font-weight: bold;
}

b {
	text-align: justify;
}

hr {
	background-color: #FFFFFF;
	border-color: #FFFFFF;
	text-align: center;
}

p {
	text-align: justify;
	text-indent: 1.5em;
	line-height: 1.2em;
	margin: 0;
	padding: 0;
}

.header_tit {
	color: #990000;
	font-size: 1.1em;
	font-weight: bold;
	text-align: center;
}
table {
	border-spacing: 0px;
	width: 100%;
}
.header_txt {
	color: #0000FF;
	font-size: 1em;
	text-align: center;
}

th {
	border-left-style: solid;
	border-left-width: thin;
	border-left-color: #800000;
	
	border-bottom-style: solid;
	border-bottom-width: thin;
	border-bottom-color: #800000;
	
	font-style: normal;
	font-variant: small-caps;
	font-weight: bold;
	text-align: center;
}

.table_special {
	font-weight: bolder;
}
td {
    overflow: hidden !important;
}
td.res {
	font-stretch: normal;
	font-weight: bold;
}

td.firm {
	text-align: center;
}

.mny,td.mny,th.mny,.numc {
	color: #000162;
	text-align: right;
}