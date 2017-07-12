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
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$xRuls		= new cReglaDeNegocio();
$xAlert		= new cAlertasDelSistema();


$aplicaResp	= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_ELIM_USE_BACK);

$clave		= parametro("id", 0, MQL_INT);
$periodo	= parametro("letra", 0, MQL_INT);
$nomina		= parametro("nomina", 0, MQL_INT);

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "ERROR\tRecibo incorrecto $clave\r\n";
//AND MODO_DEBUG == true
if($clave > 0){
	$xRec	= new cReciboDeOperacion(false, false, $clave);
	if( $xRec->init() == false){
		$rs["message"]		= "ERROR\tAl Iniciar el recibo $clave " . $xRec->getMessages() . "\r\n";
		$rs["error"]		= true;
	} else {
		$credito			= $xRec->getCodigoDeDocumento();
		if($aplicaResp == true AND $xRec->isPagable() == true){
			
			$arrV			= array (
					"mail" => ARCHIVO_MAIL,
					"idrecibo" => $clave
			);
			
			$xAlert->setProcesarProgramacion(14, $arrV);
		}
		
		if($xRec->setRevertir(true) == false){
			$rs["message"]		= "ERROR\tAl eliminar al Iniciar el recibo $clave " . $xRec->getMessages() . "\r\n";
			$rs["error"]		= true;
		} else {
			$rs["message"]		= "OK\tRecibo $clave " . $xRec->getMessages() . " Eliminado\r\n";
			$rs["error"]		= false;
			//Eliminar de nomina
			if($nomina > 0 AND $periodo > 0){
				$xPer		= new cEmpresasCobranzaPeriodos($nomina);
				if($xPer->init() == false){
					$rs["message"]	.= "ERROR\tAl Cargar el Periodo $nomina\r\n";
				} else {
					if($xPer->setCancelarOperacion($credito, $periodo)== false){
						$rs["message"]	.= "ERROR\tError al cancelar la Operacion del credito $credito y period $periodo\r\n";
					}
				}
			}
		}	
	}
}

//$xLog->add($xPer->getMessages(), $xLog->DEVELOPER);
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>