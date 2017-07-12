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
$cuenta		= parametro("cuenta", false, MQL_RAW); $cuenta = parametro("idcuenta", $cuenta, MQL_RAW);
$rs			= array("error" => true);
$idcuenta	= setNoMenorQueCero($cuenta);
if($idcuenta > 0){
	$xCta	= new cCuentaContable($idcuenta);
	$xCta->init();
	$rs			= array("nombre_de_cuenta" => $xCta->getNombre());
}

header('Content-type: application/json');
echo json_encode($rs);
?>