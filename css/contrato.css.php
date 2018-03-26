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
	font-size: <?php echo CSS_TFUENTE_CONTRATOS;?>pt;
	line-height: 125% !important;
	font-stretch: condensed;
}
h4, h3, h2, table, p {
		
}
h1,h2,h3,h4{
	text-align: center;
	line-height: 150% !important;
}
   caption {
       text-align: left;
       font-weight: bold;
}
p {
	text-align: justify;
	line-height : 1.5em;
}
th, td, p{
	text-transform: none !important;
	text-indent: 0;
}

th {
	text-align: left;
	/*min-width: 25%;*/
}
body{
	margin:0;
	height: 100%;
	padding: 0;
}
td {
	text-align: justify;
    /*min-width: 25%;*/
}
td p {
margin:1px;
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


ul.a {list-style-type: circle;}
ul.b {list-style-type: disc;}
ul.c {list-style-type: square;}

ol.d {list-style-type: armenian;}
ol.e {list-style-type: cjk-ideographic;}
ol.f {list-style-type: decimal;}
ol.g {list-style-type: decimal-leading-zero;}
ol.h {list-style-type: georgian;}
ol.i {list-style-type: hebrew;}
ol.j {list-style-type: hiragana;}
ol.k {list-style-type: hiragana-iroha;}
ol.l {list-style-type: katakana;}
ol.m {list-style-type: katakana-iroha;}
ol.n {list-style-type: lower-alpha;}
ol.o {list-style-type: lower-greek;}
ol.p {list-style-type: lower-latin;}
ol.q {list-style-type: lower-roman;}
ol.r {list-style-type: upper-alpha;}
ol.s {list-style-type: upper-latin;}
ol.t {list-style-type: upper-roman;}
ol.u {list-style-type: none;}
ol.v {list-style-type: inherit;}

@media print {
	.noprint {
		display: none;
	}
}