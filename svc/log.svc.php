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
$rs["message"]	= "";
if(isset($php_errormsg)){
	$rs["message"]	= $php_errormsg;
	unset($php_errormsg);
}
	
header('Content-type: application/json');
echo json_encode($rs)
//setLog(json_encode($rs["message"]));
//setLog($sql);
?>