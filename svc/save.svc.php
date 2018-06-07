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
$xLog		= new cCoreLog();
$xTT		= new cSysTablas();


$tabla		= parametro("tabla", null, MQL_RAW);
$clave		= parametro("id", null, MQL_RAW);
$content	= parametro("content", "", MQL_RAW);

$arrT		= array("originacion_leasing");

//rm = eliminar
//save = actualizar
//add = guardar
$rs			= array();

if($tabla != null AND $clave != null){
	
	$xObj	= new cSQLTabla($tabla);
	if( $xObj->obj() == null){
		$rs["message"]		= "ERROR\tNo se actualiza la Tabla $tabla y el registro $clave\r\n";
		$rs["error"]		= true;
	} else {
		$obj		= $xObj->obj();
		//$obj	= new cAml_alerts();
		$key		= $obj->getKey();
		$antes		= $obj->query()->initByID($clave);
		$obj->setData($antes);
		
		unset($_REQUEST["ctx"]);
		unset($_REQUEST["tabla"]);
		//setLog($_REQUEST);
		
		$despues	= $_REQUEST;
		$obj->setData($despues);
		$oQ			= $obj->query();
		$oQ->setData($despues);
		$despues	= $oQ->getCampos(true);
		
		//setLog($despues);
		
		/*$aAntes		= array_diff($antes, $despues);
		$aDespues	= array_diff($despues, $antes);
		
		//Eliminar Campos que no se actualizan
		foreach ($aAntes as $idx => $idv){
			if(!isset($aDespues[$idx])){
				//unset($aAntes[$idx]);
			}
		}*/
		$aDiffA		= array();
		$aDiffD		= array();
		foreach ($antes as $idx => $idv){
			if(isset($_REQUEST[$idx])){
				$aDiffA[$idx]	= $idv;
				$aDiffD[$idx]	= $despues[$idx];
				if($despues[$idx] === $idv){
					unset($aDiffA[$idx]);
				}
			}
		}
		//setLog($aAntes); setLog($aDespues);
		
		
		$itemsUpd	= count($aDiffA);

		$txtan		= json_encode($aDiffA);
		$txtde		= json_encode($aDiffD);
		
		if($itemsUpd <= 0){
			
			$rs["error"] 		= false;
			$rs["message"]		= "OK\tNo hay Cambios que Guardar $txtde\r\n";
			
			$xLog->add("El Usuario " . getUsuarioActual() . " Intento Guardar el registro ID $clave en $tabla sin cambios ($txtde)");
			
		} else {
			
			
				
			$xCache				= new cCache();
			$xCache->clean("$tabla-$clave");
			
			$res				= $obj->query()->update()->save("$key='$clave'");
			//Logea si se habilita
			
			if($xTT->isLog($tabla) == true){
				foreach ($aDiffD as $idxD => $vvD){
					$vva		= $antes[$idxD];
					setCambio($tabla, $idxD, $clave, $vva, $vvD);
				}
			}
			
			switch ($tabla){
				
				case TPERSONAS_GENERALES:
					//setLog($aDespues);
					$xT			= new cSocios_general();
					if( isset($aDiffD[$xT->SUCURSAL]) ){
						//Guardar cambio de sucursal
						//$xSE	= new cPersonasRelaciones(false, $clave);
						$xQL	= new MQL();
						$rs		= $xQL->getDataRecord("SELECT `numero_socio` FROM `socios_relaciones` WHERE `socio_relacionado` = $clave LIMIT 0,50");
						$xsuc	= $aDiffD[$xT->SUCURSAL];
						
						foreach ($rs as $rw){
							$idsoc	= $rw["numero_socio"];
							$xQL->setRawQuery("UPDATE socios_general SET sucursal='$xsuc' WHERE  codigo = $idsoc"); //setLog("UPDATE socios_general SET sucursal='$xsuc' WHERE  codigo = $idsoc");
							setCambio($tabla, $xT->SUCURSAL, $clave, "", $xsuc);
							$xCache->clean("socios_general-$idsoc");
						}
						
					}
					
					break;

			}
			if($res === false){
				$rs["error"]		= true;
				$rs["message"]		= "ERROR\tFallo al guardar El registro con ID $clave\r\n";
				$xLog->add("El Usuario " . getUsuarioActual() . " Intento Guardar el registro ID $clave en $tabla con errores ($txtde)");
			} else {
				$rs["error"]	= false;
				$rs["message"]		= "OK\tRegistro con ID $clave Guardado\r\n";
				$xLog->add("El Usuario " . getUsuarioActual() . " Actualiza ($txtde) antes ($txtan) de el ID $clave en $tabla");
			}
			
			
			
		}
		$txtan		= null;
		$txtde		= null;
		$antes		= null;
		$despues	= null;
		$aDespues	= null;
		$aAntes		= null;

		if(in_array($tabla, $arrT)){
			//setError($tabla);
		} else {
			$xLog->guardar($xLog->OCat()->EDICION_RAW);
		}

	}
	//setLog($rs["message"]);
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>