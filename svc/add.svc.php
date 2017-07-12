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
$xQL		= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$tabla		= parametro("tabla", null, MQL_RAW);
//rm 		= eliminar
//save 		= actualizar
//add 		= guardar
$rs			= array();
$rs["message"]		= "ERROR\tDatos Invalidos o Incompletos\r\n";
$rs["error"]		= true;
$rs["id"]			= 0;
if($tabla != null){
	$xObj	= new cSQLTabla($tabla);
	if( $xObj->obj() == null){
		$rs["message"]		= "ERROR\tNo existe el elemento($tabla), consulte al administrador\r\n";
		$rs["error"]		= true;
	} else {
		$obj	= $xObj->obj();
		//$obj	= new cAml_alerts();
		$key	= $obj->getKey();
		$obj->setData($_REQUEST); 
		//setLog($obj->query()->insert()->get());
		$res	= $obj->query()->insert()->save();
		$rs["error"] 		= ($res === false) ? true : false;
		$rs["message"]		= ($rs["error"] == true)? "ERROR\tNo se guardo el registro\r\n" : "OK\tRegistro Guardado\r\n";
		//Si el registro de guarda
		
		if($rs["error"] == false){
			$rs["id"]		= $res;
			switch ($tabla){
				case "tesoreria_valoracion_diaria":
					//$obj	= new cTesoreria_valoracion_diaria();
					$moneda	= $obj->denominacion()->v();
					$valor	= $obj->valor()->v();
					$xQL->setRawQuery("UPDATE `tesoreria_monedas` SET `quivalencia_en_moneda_local`=$valor WHERE `clave_de_moneda`='$moneda' ");
					break;
			}
			
		}
	}
}
header('Content-type: application/json');
echo json_encode($rs);
?>