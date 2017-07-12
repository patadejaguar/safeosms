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
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

$rs				= array();
$rs[SYS_ERROR]	= true;
$rs[SYS_MSG]	= "";
if($credito > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$idplan	= setNoMenorQueCero($xCred->getNumeroDePlanDePagos());
		if($idplan > 0){
			$rs[SYS_ERROR]	= false;
			$rs[SYS_NUMERO]	= $idplan;
		}
	}
	$rs[SYS_MSG]	= $xCred->getMessages();
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>