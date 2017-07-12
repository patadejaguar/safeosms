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

jsrsDispatch("Common_3de8e697db5bb95c43c3139743a47d8b, Common_84fb77b61619740746901b9329ff2c9d ");
/**
* Funcion que actualiza el estado de la Notificacuon
* @
*/
function Common_3de8e697db5bb95c43c3139743a47d8b ($params){
	$stdDiv		= STD_LITERAL_DIVISOR;
	$DPar		= explode($stdDiv, $params, STD_MAX_ARRAY_JS);
	$id		= 0;
	settype($id, "integer");
	$id		= $DPar[0];
	$estat	= $DPar[1];
	$msg	= "";
	$arrEstat	= array (
							1=>"pendiente",
							2=>"efectuado",
							3=>"comprometido",
							4=>"cancelado",
							5=>"vencido"
						);
	$sqlUNO = "UPDATE seguimiento_notificaciones
    			SET  estatus_notificacion='" . $arrEstat[$estat] . "'
    			WHERE idseguimiento_notificaciones=$id";
	$x = my_query($sqlUNO);

	if($x["stat"]!= false){
		if ($estat != "efectuado"){
			$msg 	= "Se Actualizo la Notificacion #$id A " . $arrEstat[$estat] . " Exitosamente!!!";
		}
	} else {
		$msg	= "Se produjo un Error al Actualizar #$id";
	}
	return $msg;
}

?>