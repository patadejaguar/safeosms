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
html {
	font-size: 13px;
}
@media
only screen and (-webkit-min-device-pixel-ratio: 2),
only screen and ( min-resolution: 192dpi),
only screen and ( min-resolution: 2dppx) {
    
		html {
			font-size: 16px;
			font-style: normal !important;
		}	
}
@media	only screen and (-webkit-min-device-pixel-ratio: 1.3),
	only screen and (-o-min-device-pixel-ratio: 13/10),
	only screen and (min-resolution: 120dpi)
	{
		html {
			font-size: 12px;
			font-style: normal !important;
		}
		
}

body {
	background-attachment: fixed;
	font-family: sans-serif;
	font-stretch: condensed;
	margin: 1px 1px;
	text-align: justify;
	<?php echo CSS_FORM_BACKGROUND; ?>
		
	/*background: #63bad8 url(../images/bg.jpg) 50% 0px repeat-x;*/
/*background: #63bad8 url(../images/bg.jpg) 50% 0px repeat-x;*/
/* Black */
/*background: #5D5878 url(../images/bg-1.jpg) 50% 0px repeat-x;*/
/*Cenizo*/
/*background-image: linear-gradient(to left bottom, #16222a, #1f303b, #28404d, #315060, #3a6073);*/
/*Purple*/
/* background-image: linear-gradient(to left bottom, #3c1053, #591f61, #752f6f, #91407c, #ad5389);*/
/*Ocean*/
/*background-image: linear-gradient(to left bottom, #3a6073, #2c698b, #1f71a4, #2177be, #3a7bd5);*/
 
}
div#jpanel h1#htitle {
<?php echo CSS_FORM_BACKGROUND; ?>
}

/*@media (pointer:none),(pointer:coarse){	html { font-size: 12px; font-style: normal;	}}*/
@media only screen
and (min-width : 120px)
and (max-width : 480px) {
	html { font-size: 12px;	}
}

/*and (min-width : 768px) */
@media only screen
and (min-width : 481px)
and (max-width : 767px) {
	html { font-size: 12px;	}
}


@media only screen
and (min-width : 768px)
and (max-width : 1024px) {
	html { font-size: 12px;	}

}

/* Desktops and laptops ----------- */
@media only screen
and (min-width : 1025px)
and (max-width : 1560px){
html {
	font-size: 13px;
}

}

/* Large screens ----------- */
@media only screen
and (min-width : 1561px) {
html {
	font-size: 13px;
}

}
