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
$data		= (isset($_REQUEST["data"])) ? $svc->getDecryptData($_REQUEST["data"]) : null;
$command	= (isset($_REQUEST["cmd"])) ? $svc->getDecryptData($_REQUEST["cmd"]) : null;
//$context	= (isset($_REQUEST["ctx"])) ? $svc->getDecryptData($_REQUEST["ctx"]) : null;
$cnt		= "";
//setLog("$data $command");
switch ($command){
	case TPERSONAS_GENERALES:
		$xSoc	= new cSocios_general();
		$xSoc->setData( $xSoc->query()->initByID($data) );
		$cnt 	= $svc->getEncryptData( json_encode($xSoc->query()->getCampos())  );
		//setLog(json_encode($xSoc->query()->getCampos()));
		break;
	case TPERSONAS_DIRECCIONES:
		$xDom	= new cSocios_vivienda();
		$D		= obten_filas("SELECT * FROM socios_vivienda WHERE socio_numero = $data ORDER BY fecha_alta DESC LIMIT 0,1");
		$xDom->setData($D);
		
		//setLog("SELECT * FROM socios_vivienda WHERE socio_numero = $data ORDER BY fecha_alta DESC LIMIT 0,1");
		//$value = mb_check_encoding($value, 'UTF-8') ? $value : utf8_encode($value);
		//$comment = iconv('UTF-8', 'UTF-8//IGNORE', $comment);
		//SELECT column1, CONVERT(column2 USING utf8)
		
		$query	= $xDom->query();
		$query->setToUTF8();
		//setLog(json_encode($query->getCampos()) );
		$cnt 	= $svc->getEncryptData( json_encode($query->getCampos())  );
		
		break;
	case TPERSONAS_ACTIVIDAD_ECONOMICA:
		$xTrab	= new cSocios_aeconomica();
		$D		= obten_filas("SELECT * FROM `socios_aeconomica` WHERE	(`socios_aeconomica`.`socio_aeconomica` = $data ) ORDER BY `socios_aeconomica`.`fecha_alta` DESC	LIMIT 0,1");
		$xTrab->setData($D);
		$query	= $xTrab->query();
		$query->setToUTF8();
		$cnt 	= $svc->getEncryptData( json_encode($query->getCampos())  );
		break;
	case TCATALOGOS_EMPRESAS:
		$xEmp		= new cSocios_aeconomica_dependencias();
		$D			= obten_filas("SELECT * FROM `socios_aeconomica_dependencias` WHERE (`socios_aeconomica_dependencias`.`clave_de_persona` =$data) LIMIT 0,1");
		$xEmp->setData( $D );
		
		$query	= $xEmp->query();
		$query->setToUTF8();
		$cnt 	= $svc->getEncryptData( json_encode($query->getCampos())  );
		break;
}
echo $cnt;
?>