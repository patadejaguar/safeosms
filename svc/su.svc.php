<?php
/**
 * Modulo de Baja y Cambios del Usuario
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
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
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
$xInit->cors();
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$idusuario	= parametro("usuario", 0, MQL_INT);
$action		= parametro("action", SYS_NINGUNO, MQL_RAW);

$rs			= array();
$currentUser	= getUsuarioActual();
$xCurrUser		= new cSystemUser($currentUser);
$xCurrUser->init();
if($xCurrUser->getPuedeEditarUsuarios() == true OR $xCurrUser->getID() == $idusuario){
	switch($action){
		case "baja":
			$xUser	= new cSystemUser($idusuario);
			if($xUser->init() == true){
				$xUser->setBaja();
				$rs[SYS_ERROR]		= false;
				$rs[SYS_MSG]		= $xUser->getMessages();				
			}
			break;
		case "suspension":
			$xUser	= new cSystemUser($idusuario);
			if($xUser->init() == true){
				$xUser->setSuspender();
				$rs[SYS_ERROR]		= false;
				$rs[SYS_MSG]		= $xUser->getMessages();
			}			
			break;
		case "activar":
			$xUser	= new cSystemUser($idusuario);
			if($xUser->init() == true){
				$xUser->setActivo();
				$rs[SYS_ERROR]		= false;
				$rs[SYS_MSG]		= $xUser->getMessages();
			}
			break;			
	}
} else {
	$rs[SYS_ERROR]		= true;
	$rs[SYS_MSG]		= "El usuario actual no pueder editar usuario o su propio usuario";
}

header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>