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
    require("../libs/jsrsServer.inc.php");
    include_once("../core/core.deprecated.inc.php");
	include_once("../core/entidad.datos.php");
	include_once("../core/core.config.inc.php");

jsrsDispatch("getInfPagoCompleto");

function getInfPagoCompleto($solicitud){
	$infosol = "SELECT * FROM creditos_solicitud WHERE numero_solicitud = " . $solicitud;
	$dDat = new cMFilas($infosol);
	
}

?>