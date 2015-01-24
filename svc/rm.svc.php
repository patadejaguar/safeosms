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

$tabla		= parametro("tabla", false, MQL_RAW);
$clave		= parametro("id", false, MQL_RAW);

$rs			= array();
//AND MODO_DEBUG == true
if($tabla != false AND $clave != false ){
	$xObj	= new cSAFETabla($tabla);
	if( $xObj->obj() == null){
		$rs["message"]		= "ERROR\tAl eliminar el Registro con ID $clave en la Tabla $tabla\r\n";
		$rs["error"]				= true;
	} else {
		$obj	= $xObj->obj();
		$key	= $obj->getKey();
		$obj	= new cSocios_aeconomica();
		$obj->setData( $obj->query()->initByID($clave) );
		$data	= base64_encode( json_encode($obj->query()->getCampos()) );
		$ql->setRawQuery("DELETE FROM $tabla WHERE $key='$clave'");
		$rs["message"]		= "OK\tSe elimina el Registro con ID $clave en la Tabla $tabla\r\n";
		if(MODO_DEBUG == true){	$rs["message"]	.= $ql->getMessages(); }
		$rs["error"]				= false;
		//guardar error
		$xLog				= new cCoreLog();
		$xLog->add($rs["message"] . " $data", $xLog->COMMON);
		$xLog->guardar($xLog->OCat()->ELIMINAR_RAW);
		//agregar memo
		
	}
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>