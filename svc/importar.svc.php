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
$xInit      = new cHPage("", HP_SERVICE);
$txt		= "";
$svc		= new MQLService("", "");
$ql			= new MQL();
$xSuc		= new cSucursal();
$xShare		= new cPersonasShare();
$xLog		= new cCoreLog();

$data		= (isset($_REQUEST["data"])) ? $_REQUEST["data"] : null;
$command	= (isset($_REQUEST["cmd"])) ? $svc->getDecryptData($_REQUEST["cmd"]) : null;
//$context	= (isset($_REQUEST["ctx"])) ? $svc->getDecryptData($_REQUEST["ctx"]) : null;

$xTu		= new cSystemUser( TASK_USR, false );
$xTu->init();
$ctx		= $xTu->getCTX();
$cmd		= $svc->getEncryptData($command);

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";
$rs["persona"]	= 0;

switch ($command){
	case TPERSONAS_GENERALES:
		$dpersona	= $svc->getService(SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		
		if(is_array($dpersona)){
			$xSoc		= new cSocios_general($dpersona);
			$run		= true;
			
			$idpersona	= $xSoc->codigo()->v();
			$idpersona2	= $xSoc->codigo()->v();
			if($xSuc->existeSocio($idpersona) == true){
				if($xShare->add($idpersona) == true){
					$idpersona	=  $xShare->getPersonaActual();
					//Asignar nuevo codigo
					$xSoc->codigo($idpersona);
					$xLog->add("WARN\tCambio de Persona Importada de $idpersona2 a $idpersona\r\n");
				} else {
					$run		= false;
				}
			}
			$rs["persona"]		= $idpersona;
			if($run == true){
				$res	= $xSoc->query()->insert()->save();
			}
			//Iniciar Cuenta de Captacion
			
		}		
		break;
	case TPERSONAS_DIRECCIONES:
		$ddomicilio		= $svc->getService(SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		//$rs["url"] = SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd";
		
		if(is_array($ddomicilio)){
			$xDom		= new cSocios_vivienda($ddomicilio);
			$idpersona	= $xDom->socio_numero()->v();
			
			$xDom->idsocios_vivienda('NULL'); //setear a null
			
			if($xSuc->existeSocio($idpersona) == true){
				if($xShare->initByImportado($idpersona) == true){
					$idpersona	= $xShare->getPersonaActual();
					$xDom->socio_numero($idpersona);
				}
			}
			
			$rs["persona"]	= $idpersona;
			$xDom->query()->insert()->save();
		}
		//$rs		= $ddomicilio;
		break;
	case TPERSONAS_ACTIVIDAD_ECONOMICA:
		$dtrabajo	= $svc->getService(SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		if(is_array($dtrabajo)){
			$xAe		= new cSocios_aeconomica($dtrabajo);
			$idpersona	= $xAe->socio_aeconomica()->v();
			
			$xAe->idsocios_aeconomica('NULL'); //setear a null
			
			if($xSuc->existeSocio($idpersona) == true){
				if($xShare->initByImportado($idpersona) == true){
					$idpersona	= $xShare->getPersonaActual();
					$xAe->socio_aeconomica($idpersona);
				}
			}
			
			$rs["persona"]	= $idpersona;
			$xAe->query()->insert()->save();
		}
		break;
	case TCATALOGOS_EMPRESAS:
		$dempresa	= $svc->getService(SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		foreach ($dempresa as $indice => $valor){
			if($indice == TCATALOGOS_EMPRESAS){
				$dempresa	= $valor;
			}
		}
		if(is_array($dempresa)){
			$xEmp	= new cSocios_aeconomica_dependencias($dempresa);
			$xEmp->query()->insert()->save();
		}		
		break;
}

$xLog->add($xShare->getMessages());

$xLog->guardar($xLog->OCat()->PERSONA_MODIFICADA, $idpersona);

header('Content-type: application/json');
echo json_encode($rs);
?>