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

//$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT);
$letra		= parametro("letra", false, MQL_INT);
//$tipo		= parametro("tipo", false, MQL_INT);
$rs			= array();

if( setNoMenorQueCero($credito)  > DEFAULT_CREDITO){
	$sql	= "SELECT * FROM `letras` WHERE docto_afectado = $credito AND periodo_socio=$letra LIMIT 0,1";
	$D		= obten_filas($sql);
	$rs[SYS_MONTO]	= ( isset($D["letra"]) ) ? setNoMenorQueCero($D["letra"]) : 0;
	$rs["credito"]	= $credito;
	$rs["periodo"]	= $letra;
	$rs["letra"]	= $letra;
	/*
	//$xSoc	= new cSocio($persona); $xSoc->init();
	$xCred	= new cCredito($credito); $xCred->init();
	$plan	= $xCred->getNumeroDePlanDePagos();
	//letra //monto
	$xPlan	= new cPlanDePagos($plan);
	$xPlan->init();
	//$D		= $xPlan->getDatosDeParcialidad($letra);
	$D		= $xPlan->getOLetra($letra);*/
	
	//$rs[SYS_MONTO]		= $D->getMonto();
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>