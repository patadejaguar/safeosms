<?php

/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("Modulo para agregar creditos", HP_SERVICE);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); $fecha = parametro("fecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("observaciones");
$letra			= parametro("letra", 0, MQL_INT);
$frecuencia		= parametro("frecuencia", 0, MQL_INT);
$pagos			= parametro("pagos",0, MQL_INT);
$aplicacion		= parametro("aplicacion", 0, FALLBACK_CRED_TIPO_DESTINO);
$destino		= parametro("destino",0, MQL_INT);
$oficial		= parametro("oficial", 0, MQL_INT);
$tasa			= parametro("tasa", 0, MQL_FLOAT);
$ministrado		= parametro("ministrado", fechasys(), MQL_DATE);
$recibo_fiscal	= parametro("recibo", 0, MQL_INT);
$cheque			= parametro("cheque", 0, MQL_INT);
$fechaaprob		= parametro("fechaaprobacion", fechasys(), MQL_DATE);
$DocumentoAut	= parametro("doctoautorizacion");
$FechaMinist	= parametro("ministrado", fechasys(), MQL_DATE);
$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";
$rs["credito"]	= 0;
$xCred			= new cCredito(false, $persona);
$TipoDeConvenio	= parametro("producto",DEFAULT_TIPO_CONVENIO, MQL_INT);;
$xCred->add($TipoDeConvenio, $persona, false, $monto,
		$frecuencia, $pagos, 0,
		$destino, false, false,
		$aplicacion, $observaciones, $oficial, $fecha, CREDITO_TIPO_PAGO_PERIODICO, 0, $tasa, $ministrado);
//$TipoDeConvenio, $NumeroDeSocio, $ContratoCorriente, $MontoSolicitado,
//$PeriocidadDePago, $NumeroDePagos, $PlazoEnDias,
//$DestinoDeCredito, $NumeroDeCredito, $GrupoAsociado,
//$DescripcionDelDestino, $Observaciones, $OficialDeCredito, $FechaDeSolicitud, $TipoDePago, $TipoDeCalculo, $TasaDeInteres ,
//$FechaDeMinistracion, $persona_asociada, $TipoDeAutorizacion, $id_de_origen, $tipo_de_origen, $LugarDeCobro, $TipoDeDesembolso

if($xCred->init() == true){
	$xCred->setAutorizado($monto, $pagos, $frecuencia, CREDITO_TIPO_AUTORIZACION_NORMAL, $fechaaprob, $DocumentoAut);
	$xCred2			= new cCredito($xCred->getClaveDeCredito());
	$xCred2->setForceMinistracion(true);
	if($xCred2->init() == true){
		$xCred2->setMinistrar($recibo_fiscal, $cheque, $monto, false, 0,0, "", $FechaMinist);
		$rs["error"]	= false;
	}
	$rs["message"]		.=	$xCred2->getMessages();
	$rs["credito"]	= $xCred->getClaveDeCredito();
}
$rs["message"]		.=	$xCred->getMessages();
//$recibo_fiscal, $cheque, $monto_cheque = 0, $cuenta_cheques = false, $cheque2 = 0, $cuenta_cheques2 = 0, $observaciones = "", $fecha = false, $recibo = false, $tipo_de_pago = TESORERIA_PAGO_CHEQUE) {

header('Content-type: application/json');
echo json_encode($rs);






















?>