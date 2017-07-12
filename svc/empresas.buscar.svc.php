<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package servicios.empresa
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
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";
$rs["messages"] = "Sin datos validos";
$existe				= false;
$xEmp			= new cEmpresas();
if($persona > DEFAULT_SOCIO){
	if($xEmp->initPorPersona($persona) == true){
		$existe	= true;	
		$rs["messages"]	.= $xEmp->getMessages();
	}
}
if($existe == false){
	$rs["messages"]		= "OK\tLa Persona NO existe como Empresa";
} else {
	$rs["messages"]		= "ERROR\tLa Persona existe como Empresa";
}
$rs["existe"]		= $existe;
header('Content-type: application/json');
echo json_encode($rs);
?>