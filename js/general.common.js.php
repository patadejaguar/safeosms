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
include_once("../core/core.common.inc.php");


jsrsDispatch("Common_97de3870795ecc1247287ab941d9719b, Common_695bad33e1f2af343f99c6a4ceb9d045, Common_aa694e8dd43c7c608757fae91f4d75fe, Common_52d87bf9711abf3a850de1dc12a14895");
/**
 * Funcion que devuelve los Numero en Letras
 *
 * @param string $params
 */
function Common_97de3870795ecc1247287ab941d9719b($params){

}
/**
 * Funcion que obtiene el Nombre del socio
 *
 * @param integer $params
 * @return string
 */
function Common_695bad33e1f2af343f99c6a4ceb9d045($params){
	$nombre	= MSG_NO_PARAM_VALID;
	$xT		= new cTipos();
	$socio	= $xT->cInt($params);
	$xSoc	= new cSocio($socio);
	if( $xSoc->existe($socio) == true){
		$xSoc->init(); $nombre	= $xSoc->getNombreCompleto(OUT_TXT);
	}
	return $nombre;
}

function Common_aa694e8dd43c7c608757fae91f4d75fe($params){
	//vencimiento 2o parametro
	$socio			= setNoMenorQueCero($params);
	$xFormulaDef	= new cFormula("fondo_defuncion");
	
}
function Common_52d87bf9711abf3a850de1dc12a14895( $strOrden ){
	$v 			= explode(STD_LITERAL_DIVISOR, $strOrden, 2);
	$socio		= setNoMenorQueCero($v[0]);
	$xCl		= new cCajaLocal(0);
	$xCl->setAdmitirSocio( $socio );
}
?>