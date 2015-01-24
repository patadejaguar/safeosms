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
$data		= (isset($_REQUEST["data"])) ? $_REQUEST["data"] : null;
$command	= (isset($_REQUEST["cmd"])) ? $svc->getDecryptData($_REQUEST["cmd"]) : null;
//$context	= (isset($_REQUEST["ctx"])) ? $svc->getDecryptData($_REQUEST["ctx"]) : null;

$xTu		= new cSystemUser( TASK_USR, false );
$xTu->init();
$ctx		= $xTu->getCTX();
$cmd		= $svc->getEncryptData($command);

switch ($command){
	case TPERSONAS_GENERALES:
		$dpersona	= $svc->getService(SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		
		if(is_array($dpersona)){
			$xSoc	= new cSocios_general($dpersona);
			$xSoc->query()->insert()->save();
			//Iniciar Cuenta de Captacion
			
		}		
		break;
	case TPERSONAS_DIRECCIONES:
		$ddomicilio	= $svc->getService(SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		if(is_array($ddomicilio)){
			$xDom	= new cSocios_vivienda($ddomicilio);
			$xDom->query()->insert()->save();
		}
		break;
	case TPERSONAS_ACTIVIDAD_ECONOMICA:
		$dtrabajo	= $svc->getService(SVC_ASOCIADA_HOST . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		if(is_array($dtrabajo)){
			$xAe	= new cSocios_aeconomica($dtrabajo);
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
?>