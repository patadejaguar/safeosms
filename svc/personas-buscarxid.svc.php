<?php
/**
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
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();


//$clave		= parametro("id", ""); $clave		= parametro("clave", $clave);  
$curp		= parametro("curp", "");
$rfc		= parametro("rfc", "");

$rs					= array();
//$rs["error"]		= true;
$rs["messages"]		= "";
$existe				= false;
$xBus				= new cPersonasBuscadores();
header('Content-type: application/json');
$existe			= ($curp != "") ? $xBus->setBuscarPorIDPoblacional($curp, true, true) : $existe; 
$existe			= ($rfc != "") ? $xBus->setBuscarPorIDFiscal($rfc, true, true) : $existe;
if($existe == false){
	$rs["messages"]		= "OK\tNo existe ID_POBLACIONAL $curp O ID_FISCAL $rfc\r\n";
} else {
	$rs["messages"]		= "WARN\tExiste ID_POBLACIONAL $curp O ID_FISCAL $rfc\r\n";
}
$rs["existe"]		= $existe;
echo json_encode($rs);
?>