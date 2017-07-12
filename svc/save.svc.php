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
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();

$tabla		= parametro("tabla", null, MQL_RAW);
$clave		= parametro("id", null, MQL_RAW);
$content	= parametro("content", "", MQL_RAW);
//rm = eliminar
//save = actualizar
//add = guardar
$rs			= array();

if($tabla != null AND $clave != null){
	
	$xObj	= new cSQLTabla($tabla);
	if( $xObj->obj() == null){
		$rs["message"]		= "ERROR\tNo se actualiza la Tabla $tabla y el registro $clave\r\n";
		$rs["error"]				= true;
	} else {
		$obj	= $xObj->obj();
		//$obj	= new cAml_alerts();
		$key	= $obj->getKey();
		$antes	= $obj->query()->initByID($clave);
		$obj->setData($antes);
		$despues= $_REQUEST;
		$obj->setData($despues);
		$txtan	= json_encode(array_diff($antes, $despues));
		$txtde	= json_encode(array_diff($despues, $antes));
		$xLog	= new cCoreLog();
		$xLog->add("El Usuario " . getUsuarioActual() . " Actualiza ($txtan) antes ($txtde) de el ID $clave en $tabla");
		$txtan	= null;
		$txtde	= null;
		$antes	= null;
		$despues= null;
		
		$xLog->guardar($xLog->OCat()->EDICION_RAW);
		$xCache				= new cCache();
		$xCache->clean("$tabla-$clave");
		
		$res	= $obj->query()->update()->save("$key='$clave'");
		
		$rs["error"] = ($res == false) ? true : false;
		$rs["message"]		= "OK\tRegistro con ID $clave Guardado\r\n";
	}
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>