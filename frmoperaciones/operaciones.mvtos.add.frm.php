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
$xHP		= new cHPage("TR.AGREGAR OPERACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xSel		= new cHSelect();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
//$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
//$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
//$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
//$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones= parametro("idobservaciones");


$tipo		= parametro("idtipo", false, MQL_INT);
$periodo	= parametro("idperiodo", 0, MQL_INT);
$xHP->init();
$xFRM		= new cHForm("frm", "operaciones.mvtos.add.frm.php?action=" . MQL_ADD);
$xOP	= new cOperaciones_mvtos();

//$xFRM->addJsBasico();
if($action == SYS_NINGUNO){
	$xFRM->OHidden("idrecibo", $recibo);
	$xRec	= new cReciboDeOperacion(false, false, $recibo);
	
	if($xRec->init() == true){	
		$xFRM->addHElem( $xSel->getListaDeTiposDeOperacion("idtipo", OPERACION_CLAVE_PAGO_CAPITAL)->get(true) );
		$xFRM->OMoneda("idperiodo", $xRec->getPeriodo(), "TR.PERIODO");
		$xFRM->addMonto(0);
		$xFRM->addObservaciones();
		$xFRM->addGuardar();
	} else {
		$xFRM->addCerrar();
	}
	
} else {
	$xRec	= new cReciboDeOperacion(false, false, $recibo);
	$ready	= false;
	if($xRec->init() == true){
		$id	= $xRec->setNuevoMvto($xRec->getFechaDeRecibo(), $monto, $tipo, $periodo, $observaciones);
		$xRec->setForceUpdateSaldos(true);
		$xRec->setFinalizarRecibo(true);
		$ready	= ($id >0) ? true: false;
		$xFRM->addLog($xRec->getMessages());
		$xFRM->addCerrar("", 3);
	} else {
		$xFRM->addAtras();
	}
	$xFRM->setResultado($ready);
}


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>