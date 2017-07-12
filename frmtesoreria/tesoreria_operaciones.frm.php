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
$xHP		= new cHPage("TR.OPERACION DE CAJA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xLog		= new cCoreLog();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave					= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha					= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona				= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito				= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta					= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback				= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto					= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo					= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$cuentabanco			= parametro("idcodigodecuenta", DEFAULT_CUENTA_BANCARIA, MQL_INT);
$banco					= parametro("idcodigodebanco", FALLBACK_CLAVE_DE_BANCO, MQL_INT);
$cheque					= parametro("idcheque", 0, MQL_INT);
$origen					= parametro("origen", SYS_NINGUNO, MQL_RAW);
$origen					= parametro("tipodeorigen", $origen, MQL_RAW);

//$tipoDeExp				= parametro("idtipodeafectacion", SYS_NINGUNO, MQL_RAW);
$tipoDeExp				= parametro("idtipodepagotesoreria", SYS_NINGUNO, MQL_RAW);
$moneda					= parametro("idcodigodemoneda", AML_CLAVE_MONEDA_LOCAL, MQL_RAW);
$observaciones			= parametro("idobservaciones");
$unidades_originales	= 0;
$MontoOperacion			= parametro("idmontooperacion", 0, MQL_FLOAT);
$MontoCambio			= parametro("idmontocambio", 0, MQL_FLOAT);
$documento				= parametro("iddocumento", 0, MQL_INT);
$hora					= parametro("idhora", 0);
$ClaveDeOrigen			= parametro("item", $recibo, MQL_INT);

if($action == SYS_NINGUNO){
	if($origen == iDE_RECIBO OR $origen == "recibo"){
		$recibo			= $ClaveDeOrigen;
		
		$xRec			= new cReciboDeOperacion(false, false, $recibo);
		$xRec->init();
		$monto			= $xRec->getTotal();
	}

}

$xHP->init();

$xFRM	= new cHForm("tesoreria_operaciones", "./tesoreria_operaciones.frm.php?action=" . MQL_ADD);
$xCob	= new cHCobros();
$xSel	= new cHSelect();

if($action == MQL_ADD AND $monto > 0){
	$xCaja	= new cCaja();
	$xCaja->addOperacion($recibo, $tipoDeExp, $monto, $MontoOperacion, $MontoCambio, $banco, $cheque, $cuentabanco,
			 $documento, $observaciones, $fecha, $hora,
			$moneda, $unidades_originales, $persona, $documento	);
	$xFRM->addLog($xCaja->getMessages());	
}
if($action == MQL_MOD AND $clave > 0){
	$xTes	= new cOperacionDeCaja($clave);
	if($xTes->init() == true){
		$xTes->getObj()->banco($banco);
		$xTes->getObj()->recibo($recibo);
		$xTes->getObj()->tipo_de_exposicion($tipoDeExp);
		$xTes->getObj()->monto_recibido($monto);
		$xTes->getObj()->monto_del_movimiento($MontoOperacion);
		$xTes->getObj()->monto_en_cambio($MontoCambio);
		$xTes->getObj()->numero_de_cheque($cheque);
		$xTes->getObj()->cuenta_bancaria($cuentabanco);
		$xTes->getObj()->moneda_de_operacion($moneda);
		$xTes->getObj()->documento($documento);
		$xTes->getObj()->observaciones($observaciones);
		$xTes->getObj()->fecha($fecha);
		$xTes->getObj()->hora($hora);
		$xTes->getObj()->unidades_de_moneda($unidades_originales);
		$xTes->getObj()->persona($persona);
		$xTes->getObj()->documento_descontado($documento);
		$rs	= $xTes->getObj()->query()->update()->save($clave);
		$xFRM->setResultado($rs);
	}
}
$msg		= "";
if($action == SYS_NINGUNO){
	if($clave > 0){
		$xTes			= new cOperacionDeCaja($clave);
		if( $xTes->init() == true){
			$tipoDeExp		= $xTes->getTipoDeExpocision();
			$MontoOperacion	= $xTes->getMontoOperado();
			$MontoCambio	= $xTes->getMontoDevuelto();
			$banco			= $xTes->getBanco();
			$monto			= $xTes->getMontoOperado();
			$clave			= $xTes->getClave();
			$hora			= $xTes->getObj()->hora()->v();
			$persona		= $xTes->getObj()->persona()->v();
			$documento		= $xTes->getObj()->documento()->v();
			$recibo			= $xTes->getObj()->recibo()->v();
			$observaciones	= $xTes->getObj()->observaciones()->v();
		}
	}	
	if($recibo > 1 ){
		$xRec	= new cReciboDeOperacion(false, false, $recibo);
		if($xRec->init() == true){
			$xFRM->addHElem( $xRec->getFicha(true) );
			$MontoOperacion	= $xRec->getTotal();
			$fecha			= $xRec->getFechaDeRecibo();
			$cheque			= $xRec->getNumeroDeCheque();
			$observaciones	= $xRec->getObservaciones();
			$xBanc			= $xRec->getBancoPorOperacion();
			$tipoDeExp		= $xRec->getTipoDePago();
			$documento		= $xRec->getCodigoDeDocumento();
			$persona		= $xRec->getCodigoDeSocio();
			//$monto			= $xRec->getTotal();
			if($xBanc != null){
				$banco			= $xBanc->getClaveDeBanco();
				$CuentaBancaria	= $xBanc->getNumeroDeCuenta();
			}
			$xFRM->OHidden("idrecibo", $recibo);
			$xLog->add("Se carga datos del Recibo $recibo", $xLog->DEVELOPER);
			$xTes			= new cOperacionDeCaja();
			if( $xTes->initByRecibo($recibo) == true){
				
				$tipoDeExp		= $xTes->getTipoDeExpocision();
				$MontoOperacion	= $xTes->getMontoOperado();
				$MontoCambio	= $xTes->getMontoDevuelto();
				$banco			= $xTes->getBanco();
				$monto			= $xTes->getMontoOperado();
				$clave			= $xTes->getClave();
				$hora			= $xTes->getObj()->hora()->v();
				$xFRM->setAction("./tesoreria_operaciones.frm.php?clave=$clave&action=" . MQL_MOD);
				$xLog->add("Se carga la Operacion $clave para editarla");
			}
			//if($xCaja != null){	}
			//$xFRM->addLog( $xRec->getMessages(OUT_TXT) );		
		}
	}

}

if($persona > DEFAULT_SOCIO){
	$xFRM->OHidden("idsocio", $persona);
	if($recibo <= 1){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xFRM->addHElem( $xSoc->getFicha(false, false, "", true) );
		}
	}
} else {
		$xFRM->addPersonaBasico();
}

$xFRM->setTitle($xHP->getTitle());

$xFRM->addFecha($fecha);
$xFRM->addHElem($xSel->getListaDeTiposDeAfectacionOperaciones("", SYS_ENTRADAS)->get(true));
$xFRM->addHElem($xSel->getListaDeTipoDePagoTesoreria("", false, $tipoDeExp)->get(true) );
$xFRM->addHElem($xSel->getListaDeBancos("", $banco)->get(true) );
$xFRM->addHElem($xSel->getListaDeCuentasBancarias("", false, $cuentabanco)->get(true) );
$xFRM->addHElem($xSel->getListaDeMonedas("", $moneda)->get(true));
$xFRM->OMoneda("iddocumento", $documento, "TR.Documento");
if($recibo <= 1){
	$xFRM->OMoneda("idrecibo", $recibo, "TR.Recibo de Operacion");
}
$xFRM->addMonto($monto, true);
$xFRM->setValidacion("idmonto", $xFRM->VALIDARCANTIDAD);
//$xFRM->addAvisoInicial("TR.ver");
if($MontoOperacion > 0){
	$xFRM->OHidden("idmontooperacion",$MontoOperacion);
	$xCant	= new cCantidad($MontoOperacion);
	$xFRM->addHElem( $xCant->getFicha() );
} else {
	$xFRM->OMoneda("idmontooperacion", $MontoOperacion, "TR.Monto Operacion", true);
}

$xFRM->OMoneda("idmontocambio", $MontoCambio, "TR.Monto devuelto", true);
$xFRM->addObservaciones("", $observaciones);
$xFRM->OHidden("idhora", $hora);

$xFRM->addGuardar();

$xFRM->addAviso($xLog->getMessages());


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>