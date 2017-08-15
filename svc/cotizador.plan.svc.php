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
$xHP			= new cHPage("", HP_SERVICE);
$xQL			= new MQL();
$xLi			= new cSQLListas();
$xF				= new cFecha();
$xMath			= new cMath();

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");

$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$frecuencia 	= parametro("periocidad", 7, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$pagos			= parametro("pagos", 52, MQL_INT);
$redondeo		= parametro("redondeo", true, MQL_BOOL);
$siniva			= parametro("siniva", false, MQL_BOOL);
$tasa			= parametro("tasa", 0.6, MQL_FLOAT);
$anticipo		= parametro("anticipo", 0, MQL_FLOAT);
$residual		= parametro("residual", 0, MQL_FLOAT);

$tasaiva		= parametro("iva", TASA_IVA, MQL_FLOAT);
$tasaiva		= ($siniva == true) ? 0 : $tasaiva;

$tasa			= ($tasa/100);
$soloint		= parametro("solointeres", false, MQL_BOOL);

$redondeo		= ($redondeo == true) ? 100 : 0;

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";
$xGen			= new cPlanDePagosGenerador($frecuencia);
$xGen->setPagosAutorizados($pagos);
$xGen->setMontoActual($monto);
$xGen->setMontoAutorizado($monto);
$xGen->setPeriocidadDePago($frecuencia);
$xGen->setTasaDeInteres($tasa);
$xGen->setTasaDeIVA($tasaiva);
$xGen->setFechaDesembolso(fechasys());


if($soloint == true){
	$xGen->setTipoDePago(CREDITO_TIPO_PAGO_INTERES_COMERCIAL);
} else {
	$xGen->setTipoDePago(CREDITO_TIPO_PAGO_PERIODICO);
}

$xGen->setSoloTest(true);
$xGen->setNoMostrarExtras();

$xGen->setAnticipo($anticipo);
$xGen->setValorResidual($residual);
$parcial 	= $xGen->getParcialidadPresumida($redondeo); //$redondeo, $idotros, $montootros, $primer_pago);
$xGen->setCompilar(false);
//$xGen->initPorCredito($idcredito);
$cnnt		= base64_encode( $xGen->getVersionFinal() );

$vence		= $xGen->getFechaDeUltimoPago();


$rs["error"]	= false;
$rs["message"]	= $xGen->getMessages();
$rs[SYS_MONTO]	= $parcial;
$rs[SYS_FECHA_VENCIMIENTO]	= $vence;
$rs["html"]		= $cnnt;
header('Content-type: application/json');
echo json_encode($rs);

?>