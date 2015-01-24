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
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

// Variables
$idsolicitud 		= $_GET["s"]; //numero de credito que no existe
$idsocio 		= $_GET["i"];

if (!$idsolicitud or !$idsocio) {
	echo VJS_REGRESAR;
} else {
 /* verifica solicitud */
$tiporel 			= $_POST["relacion"];
$numerosoc 			= $_POST["idsocio"];
$montorel 			= $_POST["montorel"];
$consanguinidad 	= $_POST["consan"];
$depende 			= $_POST["depende"];
$observa 			= $_POST["observaciones"];
$estatus 			= 10;	// ESTATUS ACTUAL = 10 ACTIVO

$xCred	= new cCredito($idsolicitud, $idsocio);
$xCred->init();

$xCred->addAval($numerosoc, $montorel, $tiporel, $consanguinidad, $depende, $observa );


header("location: frmcreditosavales.php?i=". $idsocio . "&s=" . $idsolicitud);
}?>
