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


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$campo			= parametro("docto", "", MQL_RAW);
$valor			= parametro("valor",false, MQL_BOOL);
$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";
//checar si existe el checklist
$xSoc			= new cSocio($persona);
if($xSoc->init() == true){
	//verificar si existe check List
	$xCheck		= new cPersonasChecklist();
	if($xCheck->initByPersona($persona) == false){
		$xCheck->add($persona);
	}
	$valor		= ($valor == false) ? 0 : 1;
	if($xCheck->set($campo, $valor) == false){
		//error
	} else {
		$rs["error"]	= false;
		$rs["message"]	= "OK\tActualizado al Valor $valor";		
	}
} else {
	$rs["message"]	= "La persona $persona NO EXISTE";
}
header('Content-type: application/json');
echo json_encode($rs);
?>