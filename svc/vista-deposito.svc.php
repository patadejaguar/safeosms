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

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", 0, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");
$tipo_de_pago	= parametro("tipodepago", DEFAULT_TIPO_PAGO, MQL_RAW);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$rs				= array();
$rs["error"]	= true;
$rs["messages"]	= "";
$rs["recibo"]	= 0;
$rs["alta"]		= false;


if($persona > DEFAULT_SOCIO AND $cuenta <= 0){
	$xSoc		= new cSocio($persona);
	if($xSoc->init() == true){
		//if($xSoc->existeCuenta($cuenta) == false){	} 
		$cuenta  	= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_ORDINARIO);
		$cuenta		= setNoMenorQueCero($cuenta);
		//Si es menor a cero
		if($cuenta <= 0){
			$xCta		= new cCuentaALaVista(false);
			$cuenta		= $xCta->setNuevaCuenta(DEFAULT_CAPTACION_ORIGEN, CAPTACION_PRODUCTO_ORDINARIO, $persona, "REGISTRO_AUTOMATICO");
			$rs["alta"]	= true;
			$rs["messages"]	.= $xCta->getMessages();
		} else {
			$rs["messages"]	.= "WARN\tSe Carga la Cuenta $cuenta\r\n";
		}
	}
}

if($cuenta > DEFAULT_CUENTA_CORRIENTE && $monto > 0){
	$xCta	= new cCuentaALaVista($cuenta);
	if($xCta->init() == true){
		$recibo		= $xCta->setDeposito($monto, "", $tipo_de_pago, false, $observaciones, false, $fecha, false, $empresa, $ctabancaria);
		if($xCta->getSuccess() == false){
			$rs["error"]	= true;
			$rs["messages"]	.= "ERROR\tAl Agregar DEPOSITO_CAPTACION a la Cuenta $cuenta por $monto " . $xCta->getMessages() . "\r\n";
		} else {
			$rs["error"]	= false;
			$rs["messages"]	.= "OK\tAgregar DEPOSITO_CAPTACION a la Cuenta $cuenta por $monto y Recibo $recibo\r\n";
			$rs["recibo"]	= $recibo;
		}
	} else {
		$rs["error"]	= true;
		$rs["messages"]	.= "ERROR\tNo se inicia la cuenta $cuenta\r\n";		
	}
} else {
	$rs["messages"]	.= "ERROR\tLa cuenta $cuenta de Persona $persona tal vez no existe\r\n";
}
header('Content-type: application/json');
echo json_encode($rs);
?>