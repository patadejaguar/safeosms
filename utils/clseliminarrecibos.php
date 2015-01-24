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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.operaciones.inc.php";

$oficial 	= elusuario($iduser);

$idrec 		= $_POST["idrecibo"];
//$frn 		= $_GET["u"];
	if(!$idrec) {
		echo JS_CLOSE;
	}


	$cRec		= new cReciboDeOperacion(false, false, $idrec);
	$cRec->setNumeroDeRecibo($idrec);
	$cRec->setRevertir();
	//echo $cRec->getMessages("html");

	saveError(10,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "EL Usuario $oficial Elimino el Recibo $idrec y sus Operaciones");

if ($u == "exn"){
	header("location: frmeditarrecibos.php");
} else {
	header("location: frmeliminarrecibos.php");
} //*/

?>