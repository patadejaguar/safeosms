<?php
include_once("../libs/phoogle.php");

$map = new PhoogleMap();
$map->setAPIKey("ABQIAAAAm7-WgoUcljbjg9lZ826pHxRN4KJJp6EBD1WWP8QcpU_yf0xsLhRxf0cBTQ-sCTPwQutPfmSfAMpghw");
$map->zoomLevel = 3;
$map->setWidth(640);
$map->setHeight(480);
$map->addGeoPoint(19.842264,-90.536951,"Domicilio de la Caja Solidaria")
//,-90.536951), 15);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Geo Localizacion de Vivienda</title>
	<?php
	$map->printGoogleJS();
	?>
  </head>
  <body onload="load()" onunload="GUnload()">
    <?php
    $map->showMap();
    ?>
  </body>
</html>