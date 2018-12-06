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
$clave		= parametro("clave", false, MQL_RAW); $clave		= parametro("id", $clave, MQL_RAW);
$action		= parametro("action", SYS_NINGUNO);
$out		= parametro("out", "", MQL_RAW);
$buscar		= parametro("buscar", "", MQL_RAW);
$endonde	= parametro("en", "", MQL_RAW);
$operador	= parametro("operador", "=");
$activar	= parametro("activar", false, MQL_BOOL);

$rs				= array();
$rs[SYS_ERROR]	= true;

header('Content-type: application/json');
 
if($tabla !== false ){
	$xObj	= new cSQLTabla($tabla);
	$obj	= $xObj->obj();
	
	if( $obj === null OR $clave === false){
		$rs["message"]		= "ERROR\tPara la Tabla $tabla y clave $clave\r\n";
		
	} else {
		$key	= $obj->getKey();
		$campo	= "estatus";
		switch ($tabla){
			case "creditos_lineas";
			$campo	= "estado";
			break;
			case "creditos_periocidadpagos";
			$campo	= "estatusactivo";
			break;
			case "socios_relacionestipos":
				$campo	= "mostrar";
				break;
		}
		if($tabla == "creditos_tipoconvenio"){
			$xProd	= new cProductoDeCredito($clave);
			if($xProd->init() == true){
				if($activar == true){
					$xProd->setInActivo(false);
				} else {
					$xProd->setInActivo();
				}
				
				
				$rs["message"]	= "OK\tActualizacion Exitosa.";
				$rs[SYS_ERROR]	= false;
			} else {
				$rs["message"]	= "ERROR\tNo existe el producto";
				$rs[SYS_ERROR]	= true;
			}
			

		} else {
		
			$xQL	= new MQL();
			$in		= ($activar == true) ? "1" : "0";
			$res	= $xQL->setRawQuery("UPDATE `$tabla` SET `$campo`=$in WHERE `$key` = $clave ");
			if($res === false){
				$rs["message"]	= "ERROR\tFallo en la actualizacion.";
			} else {
				$rs["message"]	= "OK\tActualizacion Exitosa.";
				$rs[SYS_ERROR]	= false;
			}
		}
	}
} else {
	$rs["message"]	= "Error\tNo hay registros para T";
}

echo json_encode($rs);
?>