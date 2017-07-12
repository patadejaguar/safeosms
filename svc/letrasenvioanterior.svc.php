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
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT);
$letra			= parametro("letra", 0, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT);
$periodo		= parametro("periodo", 0, MQL_INT);
$periocidad		= parametro("periocidad", 0, MQL_INT);
$periocidad		= parametro("frecuencia", $periocidad, MQL_INT);

$rs			= array();
$idnomina	= parametro("nomina", false);
$xEmp		= new cEmpresas($empresa); $xEmp->init();

$rs["message"] 	= "";
if( setNoMenorQueCero($credito) > 1){
		$xCob			= new cEmpresasCobranzaDetalle(false, $idnomina);
		$id				= 0;
	if($xCob->getExisteEnOtraNomina($letra, $credito, $empresa, $periodo, $periocidad) > 0 ){
		$rs["error"] 	= true;
		$rs["credito"] 	= $credito;
		$rs["message"] 	.=  $xCob->getMessages();			
	} else {
		$rs["error"] 	= false;
		$rs["credito"] 	= $credito;
	}
} else {
	$rs["error"] 	= true;
	$rs["message"] 	.= "credito invalido $credito ";
}	


header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>