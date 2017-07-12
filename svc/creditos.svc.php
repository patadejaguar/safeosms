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

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT);
$tipo		= parametro("tipo", false, MQL_INT);
$estado		= parametro("estado", false, MQL_INT);
$rs			= array();

if( setNoMenorQueCero($persona)  > DEFAULT_SOCIO){
	//$xSoc	= new cSocio($persona); $xSoc->init();
	//$tipo 	= ( setNoMenorQueCero($tipo)  > 0) ? $tipo : SYS_TODAS; 
	$estado 	= ( setNoMenorQueCero($estado)  > 0) ? $estado : SYS_TODAS;
	$SinSaldo	= ($estado == CREDITO_ESTADO_AUTORIZADO OR $estado == CREDITO_ESTADO_SOLICITADO) ? true : false;
	$sql		= $lis->getListadoDeCreditos($persona, $SinSaldo, $estado);
	$datos		= $ql->getDataRecord($sql);
		foreach ($datos as $row){
			$describe				= $xF->getFechaDDMM( $row["otorgado"]) .   "-". $row["producto"] . "-".  $row["periocidad"] . "-" .$row["periodo"] . "-" . $row["saldo"];
			
			$rs[$row["credito"]]	= $describe;
		}
	//}
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>