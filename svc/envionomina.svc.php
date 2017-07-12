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
$letra		= parametro("letra", 0, MQL_INT);
$monto		= parametro("monto", 0, MQL_FLOAT);

$fechaI		= parametro("on", fechasys());
$fechaF		= parametro("off", fechasys());
$fechaEnv	= parametro("to", fechasys());

$observaciones	= parametro("observaciones", "");
$empresa		= parametro("empresa", false);
$periodo		= parametro("periodo", false);
$periocidad		= parametro("periocidad", false);
$notas			= parametro("notas", false);

$rs			= array();
$idnomina	= parametro("nomina", false);
$xEmp		= new cEmpresas($empresa); $xEmp->init();

$rs["message"] 	= "";
$msg			= "";
if( setNoMenorQueCero($idnomina)  <= 0){
	$rs["error"] 	= true;
	$rs["message"] 	.= "Nomina Invalida $idnomina ";	
} else {
	if( setNoMenorQueCero($credito) > 1){
		$xCob			= new cEmpresasCobranzaDetalle(false, $idnomina);
		$id				= 0;
		if($xCob->getExisteEnOtraNomina($letra, $credito, $empresa, $periodo, $periocidad) > 0 ){
			$xErr		= new cCoreLog();
			$xErr->add($xCob->getMessages());
			$xErr->guardar($xErr->OCat()->NOMINA_ENVIO_DUP);
			$rs["message"] 	.= $xErr->getMessages();
			
		}
		//else {
		$id = $xCob->add($letra, $monto, $notas, $credito);
		if(setNoMenorQueCero($id) > 0){
			$rs["error"] 	= false;
			$rs["message"] 	.= "Registro guardado con el ID $id";
			$rs["credito"] 	= $credito;
		} else {
			$rs["error"] 	= true;
			$rs["credito"] 	= $credito;
			$rs["message"] 	.= "Error al Guardar el registro : " . $xCob->getMessages();
		}			
		//}

	} else {
		$rs["error"] 	= true;
		$rs["message"] 	= "credito invalido $credito ";
	}	
}

header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>