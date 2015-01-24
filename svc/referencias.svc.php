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
$rs			= array();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$relacionado= parametro("relacionado", DEFAULT_SOCIO, MQL_INT);
$tipo		= parametro("tipo", 0, MQL_INT);
$parentesco	= parametro("parentesco", 0, MQL_INT);
$depende	= parametro("depende", false, MQL_BOOL);
$documento	= parametro("documento", false, MQL_INT);
$error		= false;
$format		= parametro("out", "", MQL_STRING);
header('Content-type: application/json');

if($persona > DEFAULT_SOCIO){
	$xPer	= new cSocio($persona);
	if($xPer->init() == true){
		//setLog($lis->getListadoDeRelaciones($persona));
		$svc	= new MQLService("list", $lis->getListadoDeRelaciones($persona, $documento));
		echo	$svc->getJSON($format);
	} else {
		$error	= true;
		$txt	.= "ERROR\tNo existe la persona $persona\r\n";
	}
	if(MODO_DEBUG == true){ $txt .= $xPer->getMessages(); }
}
if($error == true){
	$rs["error"]= true;
	$rs["msg"]	= $txt;
	echo json_encode($rs);
}



//setLog(json_encode($rs));
//setLog($sql);
?>