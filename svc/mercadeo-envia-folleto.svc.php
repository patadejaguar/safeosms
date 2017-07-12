<?php
/**
 * Modulo
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
//$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
//$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xSVC		= new MQLService($action, "");

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";
$xNot			= new cNotificaciones();


if($clave > 0){
	$xMkt	= new cMercadeo_envios();
	$xMkt->setData($xMkt->query()->initByID($clave));
	if($xMkt->idmercadeo_envios()->v() > 0  ){
		if($xMkt->estatus()->v() == SYS_UNO){
			$xSoc	= new cSocio($xMkt->persona()->v());
			if($xSoc->init() == true){
				$actual 	= file_get_contents("folleto.html");
				$xNot->setMailSettings(MKT_SERVER, MKT_PORT, MKT_SERVER_TLS, MKT_MAIL, MKT_PWD, EACP_NAME, $xSoc->getNombreCompleto());
				$res		= $xNot->sendMail($xSoc->getNombreCompleto(), $actual, $xSoc->getCorreoElectronico());
				//$res		= $xNot->sendMail($xSoc->getNombreCompleto(), $actual, "patadejaguar@gmail.com");
				$rs["error"]	= false;
				$rs["message"]	.= $res;//$xNot->getMessages();
				$rs["message"]	.= "Mail enviado";
				$rs["clave"]	= $clave;
				$xQL->setRawQuery("UPDATE `mercadeo_envios` SET `estatus`=0 WHERE `idmercadeo_envios`= $clave ");
			}
		} else {
			$rs["message"]	= "En envio con clave $clave ya se ha hecho";
		}
	}
}




header('Content-type: application/json');
echo json_encode($rs);
?>