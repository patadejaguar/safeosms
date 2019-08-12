<?php

//=====================================================================================================
//=====>	INICIO_H
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");

include_once("../libs/Banxico.php");

$theFile					= __FILE__;
//$permiso					= getSIPAKALPermissions($theFile);
//if($permiso === false){		header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================

$xHP		= new cHPage("Pruebas de Fechas", HP_FORM);
$xHP->init();



//Crear formularios
$xFRM	= new cHForm("frmTest", "./test.php");

$xHTxt	= new cHText("");
$xHChk	= new cHCheckBox();

$xBan	= new Banxico();

$xFRM->OMoneda2("iddolar", $xBan->getExRate(), "TR.DOLAR");

echo $xFRM->get();

$xHP->fin();
//=====================================================================================================
?>