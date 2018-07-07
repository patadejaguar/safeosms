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
$xQL		= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();

//$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT);
$letra		= parametro("letra", false, MQL_INT);
$cmd		= parametro("cmd", SYS_NINGUNO, MQL_RAW);
//$tipo		= parametro("tipo", false, MQL_INT);
$rs			= array();
$xPlan		= null;
$rs["aviso"]	= "";




switch ($cmd){
	case MQL_DEL:
		$xPlan	= new cPlanDePagos();
		$xPlan->initByCredito($credito);
		$xPlan->setNeutralizarParcialidad($letra, true);
		$rs["aviso"]	= $xPlan->getMessages();
		break;
}
$xCred	= new cCredito($credito);
$xCach	= new cCache();
$dataC	= $xCach->get("credito-letras-$credito");
$dataC	= ($dataC == null) ? false : $dataC;

$init	= $xCred->init($dataC);

if($init == true){
	$xCach->set("credito-letras-$credito", $xCred->getDatosInArray(), $xCach->EXPIRA_MEDDIA);
}

if( setNoMenorQueCero($credito)  > DEFAULT_CREDITO AND $init == true){
	//$sql	= "SELECT * FROM `letras` WHERE docto_afectado = $credito AND periodo_socio=$letra LIMIT 0,1";
	$sql	= $lis->getLetrasIndividual($credito, $letra);
	
	if($xCred->getSaldoActual() <= 0 OR $xCred->getEsAfectable() == false ){
		$rs[SYS_MONTO]	= 0;
		$rs["credito"]	= $credito;
		$rs["periodo"]	= $letra;
		$rs["letra"]	= $letra;
		$rs["aviso"]	.= "ERROR\tEl Credito $credito no tiene saldo o no es afectable\r\n";		
	} else {
		$D				= $xQL->getDataRow($sql);
		//$xL				= new cLetrasVista();
		
		if(isset($D["letra"])){
			$monto			= setNoMenorQueCero($D["total_sin_otros"]);
			$rs[SYS_MONTO]	= $monto;
			$rs["credito"]	= $credito;
			$rs["periodo"]	= $letra;
			$rs["letra"]	= $letra;
			$rs["aviso"]	= "";
			$interes		= setNoMenorQueCero($D["interes"]);
			$iva			= setNoMenorQueCero($D["iva"]);
			$capital		= setNoMenorQueCero($D["capital"]);
			$otros			= setNoMenorQueCero($D["otros"]);
			
			if(MODO_DEBUG == true){
				$rs["aviso"]	= "OK\tLetra $letra Interes $interes IVA $iva Capital $capital Otros $otros\r\n";
			}
			if($interes <= 0){
				$rs["aviso"].= "WARN\tLa Parcialidad Num $letra del Credito $credito : SIN INTERES\r\n";
			}
			if( $iva <= 0 ){
				$rs["aviso"].= "WARN\tLa Parcialidad Num $letra del Credito $credito : SIN IMPUESTOS\r\n";
			}
		} else {
			$rs[SYS_MONTO]	= 0;
			$rs["credito"]	= $credito;
			$rs["periodo"]	= $letra;
			$rs["letra"]	= $letra;
			$rs["aviso"]	.= "ERROR\tNo existe monto en la Letra $letra del Credito $credito\r\n";		
		}
	}
} else {
	$rs[SYS_MONTO]	= 0;
	$rs["credito"]	= $credito;
	$rs["periodo"]	= $letra;
	$rs["letra"]	= $letra;
	$rs["aviso"]	.= "ERROR\tEl Credito $credito No existe o no se inicio\r\n";	
}

header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>