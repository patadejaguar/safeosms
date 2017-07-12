<?php
/**
 * Solicitud de Creditos, forma de captura
 * @author Balam Gonzalez Luis Humberto
 * @version 1.50
 * @package creditos
 * @subpackage forms
 * 		22/07/2008	Funciones mejoradas de riesgo
 * 					Implementacion de php doc
 */
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Validar Creditos");
$xHP->init();
$xFRM		= new cHForm("frm", "./");
$msg		= "";

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$periocidad	= parametro("periocidad", 0, MQL_INT);
$convenio	= parametro("producto", 0, MQL_INT);
$pagos		= parametro("pagos", 0, MQL_INT);
$contrato	= parametro("contrato", DEFAULT_CUENTA_CORRIENTE, MQL_INT);
$vencido	= parametro("vencido");
$ministrado	= parametro("ministrado");
$monto		= parametro("monto", 0, MQL_FLOAT);
$fecha		= parametro("fecha", fechasys(), MQL_DATE);

$tipoorigen	= parametro("tipoorigen",0, MQL_INT);
$claveorigen= parametro("claveorigen",0, MQL_INT);
//function jsaValidarCredito($socio,  $numpagos, $periocidad, $convenio, $contrato, $fechaMin, $fechaVenc, $monto){
	$xSoc			= new cSocio($persona);
	
	$out 			= false;
	$msg 			= "";
	//"numero_de_solicitud" => $solicitud,
	$arrDatos		= array(
			
			"periocidad_de_pago" => $periocidad,
			"tipo_de_producto" => $convenio,
			"numero_de_pagos" => $pagos,
			"contrato_corriente_relacionado" => $contrato,
			"fecha_de_ministracion" => $ministrado,
			"fecha_de_vencimiento" => $vencido,
			"monto_solicitado"		=> $monto,
			"fecha_de_solicitud" => $fecha,
			"tipo_de_origen" => $tipoorigen,
			"clave_de_origen" => $claveorigen
	);
	if($xSoc->init() == true){
		if(MODULO_AML_ACTIVADO == true){
			$xSoc->setAMLAutoActualizarNivelRiesgo();
		}
		
		$valido		= $xSoc->getValidacion(false, false);
		
		if($xSoc->isOperable() == true AND $valido == true){
			$out	= $xSoc->setPrevalidarCredito($arrDatos);
		} else {
			$msg	.= "ERROR\tLA PERSONA $persona NO PUEDE REALIZAR OPERACIONES.\r\n";
		}
	
	} else {
		$msg	.= "ERROR\tLA PERSONA $persona No existe.\r\n";
	}
	$msg	.= $xSoc->getMessages();
	if($out == true){
		$_SESSION[SYS_UUID]		= $xSoc->getUUID();
		$msg	.= "OK\tEL CREDITO HA SIDO VALIDADO POR EL SISTEMA - CUMPLE LOS REQUISITOS\r\n";
		//$xFRM->OButton("TR.guardar credito", "var xG = new Gen(); xG.close()", "guardar", "idvalidarok");
		//$xFRM->OButton("TR.validar nuevamente", "jsaValidarCredito()", "checar", "idnuevavalidacion");
		$xFRM->addAvisoRegistroOK($msg);
	} else {
		$_SESSION[SYS_UUID]		= null;
		//$ctrl = "<input type=\"button\" name=\"cmdSubmit\" onclick=\"jsPrevalidarCredito();\" value=\"VALIDAR CREDITO NUEVAMENTE\" />";
		//$xFRM->OButton("TR.validar nuevamente", "jsaValidarCredito()", "checar", "idnuevavalidacion");
		$xFRM->addAvisoRegistroError($msg);
	}
	
	$xFRM->addCerrar();
	echo $xFRM->get();
	
	
$xHP->fin();
?>
