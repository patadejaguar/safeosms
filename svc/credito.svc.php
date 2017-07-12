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

$credito	= parametro("credito", 0, MQL_INT);
$que		= parametro("q", "");
$rs			= array();

if(setNoMenorQueCero($credito) > 0){
	$xCred		= new cCredito($credito);
	if($xCred->init()  == true){
		$rs["descripcion"]	= $xCred->getDescripcion();
		//Otros datos
		switch($que){
			case "ESTATUS":
				//
				$rs["estatus"]	= $xCred->getEstadoActual();
				break;
		}
	}
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>