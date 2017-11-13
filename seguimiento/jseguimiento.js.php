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
include_once ("../core/entidad.datos.php");
include_once ("../core/core.deprecated.inc.php");
include_once ("../core/core.fechas.inc.php");
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
include_once ("../core/core.seguimiento.inc.php");
require("../libs/jsrsServer.inc.php");

$oficial = elusuario($iduser);


jsrsDispatch("getCompromisosDia, getCompromisosById, jsSetEstatusCompromiso, jsSetOficialDeSeguimiento, Common_84fb77b61619740746901b9329ff2c9d, Common_eb8d3f1b179bfca7a3d31880b4d66778, jsSetCausaDeMora");

function getCompromisosDia($dia){
	return getFichaCompromiso(" AND seguimiento_compromisos.fecha_vencimiento='$dia' " );
}

function getCompromisosById($idt){
	return getFichaCompromiso(" AND seguimiento_compromisos.idseguimiento_compromisos=$idt ");
	//return $sql;
}
function jsSetEstatusCompromiso($strOrden){
	$DOrden 		= explode(STD_LITERAL_DIVISOR, $strOrden, 2);
	$compromiso		= $DOrden[0];
	$estatus		= $DOrden[1];
	
	saveError(11, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Se Actualizo el Compromiso $compromiso al estatus $estatus " );
	
	switch  ( $estatus ){

		default:
			$sql = "UPDATE seguimiento_compromisos
    					SET  estatus_compromiso='$estatus'
    					WHERE idseguimiento_compromisos=$compromiso";
			my_query($sql);
			break;

	}
	return "El Compromiso #$compromiso ha sido marcado como $estatus";
}
function jsSetOficialDeSeguimiento($strOrden){
	$DOrden 		= explode(STD_LITERAL_DIVISOR, $strOrden, 2);
	//$socio			= $DOrden[0];
	$solicitud		= $DOrden[0];
	$oficial		= $DOrden[1];
	saveError(11, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Asignacion del Credito $solicitud al Oficial $oficial desde Modulo Directo" );
	
	/*$sql = "UPDATE creditos_solicitud
    		SET oficial_credito=$oficial, oficial_seguimiento=$oficial
    		WHERE numero_solicitud=$solicitud";
    		my_query($sql);*/
	
    		return "Se ha Asignado el Credito $solicitud al Oficial #$oficial";

}
function jsSetCausaDeMora($strOrden){
	$DOrden 		= explode(STD_LITERAL_DIVISOR, $strOrden, 2);
	$solicitud		= $DOrden[0];
	$causa		= $DOrden[1];
	saveError(11, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Asignacion del Credito $solicitud a la Causa $causa desde Modulo Directo" );
	
	$sql = "UPDATE creditos_solicitud
    		SET causa_de_mora=$causa
    		WHERE numero_solicitud=$solicitud";
    		my_query($sql);
    		return "Se ha Asignado el Credito $solicitud la Causa de Morosidad #$causa";

}

/**
*	Funcion para Agregar Llamada
*/
function Common_eb8d3f1b179bfca7a3d31880b4d66778($strOrden){
	$d				= explode(STD_LITERAL_DIVISOR, $strOrden, 4);
	$socio			= $d[0];
	$solicitud		= $d[1];
	$fecha			= $d[2];
	$hora			= $d[3];
	$observaciones		= "AGREGADO_DESDE_EL_CALENDARIO";
	
	saveError(11, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Se creo la Llamada del Socio $socio por el Credito $solicitud" );
	
	setNewLlamadaBySocio($socio, $solicitud, $fecha, $hora, $observaciones);
	return "Llamada Agregada al socio $socio por el Credito $solicitud";
}
/**
* Funcion que actualiza el estado de la llamada
*/
function Common_84fb77b61619740746901b9329ff2c9d($strOrden){
	$msg 		= "";
	$v 			= explode(STD_LITERAL_DIVISOR, $strOrden, 2);
	$estatus	= $v[1];
	$codigo		= $v[0];
	$sql = "UPDATE seguimiento_llamadas 
    		SET estatus_llamada='$estatus'
    		WHERE idseguimiento_llamadas=$codigo ";
//'efectuado','cancelado','pendiente','vencido'
//formato llamada@estatus
	$x = my_query($sql);
	if ($x["stat"] != false){
		if ($estatus != "efectuado"){
			$msg = "Llamada #$codigo se actualizo a {$estatus} ";
		}
	} else {
		$msg = "ERROR al actualizar la Llamada #$codigo  a { $estatus } ";
	}
	
		return 		$msg;
}
?>