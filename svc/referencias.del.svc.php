<?php
/**
 * Modulo
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_SERVICE);
//$xQL		= new MQL();
//$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT); 




$xSVC			= new MQLService($action, "");

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";

switch ($action){
	default:
		$xRels	= new cPersonasRelaciones($clave, false);
		if($xRels->init() == true){
			$xRels->setDarDeBaja();
			$rs["error"]	= false;
			$rs["message"]	= "La relacion se ha dado de baja";
			setAgregarEvento_("La relacion se ha dado de baja", 10102, $xRels->getCodigoDePersona());
			setAgregarEvento_("La relacion se ha dado de baja", 10102, $xRels->getCodigoDePersonaRelacionado());
		}
		break;
}


header('Content-type: application/json');
echo json_encode($rs);
?>