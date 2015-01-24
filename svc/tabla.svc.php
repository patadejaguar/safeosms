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

$tabla	= parametro("tabla", false, MQL_RAW);
$clave	= parametro("clave", false, MQL_RAW);
$action	= parametro("action", SYS_NINGUNO);
$out	= parametro("out", "", MQL_RAW);
$rs			= array();
header('Content-type: application/json');
 
if($tabla != false ){
	$xObj	= new cSAFETabla($tabla);
	if( $xObj->obj() == null){
		$rs[MSG_NO_PARAM_VALID]		= "ERROR\t para la Tabla $tabla y clave $clave\r\n";
	} else {
		$obj	= $xObj->obj();
		
		if($action == SYS_NINGUNO){
			if($clave != false){
				$obj->setData($obj->query()->initByID($clave));
			}
			$rs		= $obj->query()->getCampos();
			$cadena		= json_encode($rs);
					
		} else {
			//$obj	= new cSocios_aeconomica();
			
			
			$indice	= $obj->getKey();
			$etiq	= $xObj->getCampoDescripcion();
			if($etiq == ""){
				$campos	= $obj->query()->getCampos();
				$cnt	= 0;
				foreach ($campos as $props){
					$etiq	= ($cnt == 1) ? $props["N"] : $etiq;
					$cnt++;
				}
			}
			$sql	= "SELECT `$tabla`.`$indice` AS `indice`, `$tabla`.`$etiq` AS `etiqueta` FROM $tabla WHERE (`$tabla`.`$indice` LIKE '%$clave%' OR  `$tabla`.`$etiq` LIKE '%$clave%' ) LIMIT 0,100";
			//setLog($sql);
			$xSVC	= new MQLService($action, $sql);
			$cadena	=  $xSVC->getJSON($out);
		}
	}
}

echo $cadena;
?>