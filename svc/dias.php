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
$xInit->cors();
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$fecha		= parametro("f", false, MQL_RAW);
$dia1		= parametro("d1", 0, MQL_INT);
$dia2		= parametro("d2", 0, MQL_INT);
$dia3		= parametro("d3", 0, MQL_INT);
$credito	= parametro("credito", 0, MQL_INT);
$fecha		= $xF->getFechaISO($fecha);

$onISO		= parametro("iso", true, MQL_BOOL);

$rs				= array();
$rs["error"]	= false;
$rs["message"]	= "";
$xCred			= new cCredito($credito);
if($xCred->init() == true){
	$xGen			= new cPlanDePagosGenerador($xCred->getPeriocidadDePago());
	$xGen->initPorCredito($xCred->getNumeroDeCredito(), $xCred->getDatosInArray());
	$xGen->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
	$fechaD			= $xGen->getFechaDePago($fecha, 0);
	$rs["fecha"]	= ($onISO == false) ? $xF->getFechaMX($fechaD, "-") : $fechaD;
	$rs["message"]	= $xGen->getMessages();
} else {
	$rs["error"]	= true;
	$rs["message"]	= $xCred->getMessages();
}

header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>